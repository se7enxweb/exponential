<?php
/**
 * File containing the expVelocity class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Drives the bundled application server as a service.
 *
 * The server is an ordinary PHP process that serves this installation from
 * persistent workers instead of one process per request. Running it means
 * assembling a long command line, backgrounding it so it survives the shell
 * that started it, and then knowing which processes belong to it when the
 * time comes to stop them. Getting any of that subtly wrong is easy, and the
 * failure modes are unpleasant: a pkill that takes the calling shell with it,
 * an orphaned pool still holding the port, a second server started because
 * the first was not noticed.
 *
 * So the knowledge lives here rather than in a shell history, and the command
 * line is built from settings rather than typed. Everything a caller needs is
 * a verb -- start, stop, graceful, restart, kill, status -- and the class
 * answers with a result array rather than printing, so a module view can use
 * it as readily as the console can.
 *
 * Settings come from velocity.ini, which ships with defaults and can be
 * overridden per installation in the usual way.
 *
 *   $velocity = new expVelocity();
 *   $result = $velocity->start();
 *   if ( !$result['ok'] ) echo $result['message'];
 *
 * @package kernel
 */
class expVelocity
{

    /**
     * Seconds to wait between polls when watching for a state change.
     */
    const POLL_INTERVAL = 0.25;

    /**
     * Globals this kernel needs kept between requests, whatever the site.
     *
     * Each is a registry filled by a file the kernel reaches with include_once,
     * which cannot be refilled once cleared because include_once will not run
     * that file again: clearing one leaves a worker unable to resolve a
     * datatype, a workflow event, a notification event or a payment gateway
     * for the rest of its life. They are facts about the kernel, not choices
     * of an installation, so they live here rather than in a file under /etc
     * or a setting someone has to remember. Settings and --keep-global append
     * to this list; [ApplicationSettings] KeepGlobalsDefaults=disabled drops it.
     */
    const DEFAULT_KEEP_GLOBALS = array(
        'eZDataTypes', 'eZDataTypeObjects', 'eZDataTypeAllowedTypes',
        'eZWorkflowTypes', 'eZWorkflowTypeObjects', 'eZWorkflowAllowedTypes',
        'eZNotificationEventTypes', 'eZNotificationEventTypeObjects', 'eZNotificationEventTypeAllowedTypes',
        'eZPaymentGateways',
    );

    /** @var array names added for this run, from --keep-global */
    protected $extraKeepGlobals = array();

    /** Used when the caller's PATH is empty, and searched after it for executables. */
    const DEFAULT_PATH = '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin';

    /**
     * @var eZINI
     */
    protected $ini;

    /**
     * @var string Absolute path to the installation root.
     */
    protected $rootDir;

    /**
     * @var string What the configuration tree said on the last start, for its message.
     */
    protected $layoutNote = '';

    public function __construct( $iniName = 'velocity.ini' )
    {
        $this->ini = eZINI::instance( $iniName );
        $this->rootDir = rtrim( eZSys::rootDir(), '/' );
        if ( $this->rootDir === '' )
            $this->rootDir = rtrim( getcwd(), '/' );
    }

    // ── Settings ─────────────────────────────────────────────────────────

    /**
     * A setting, with a fallback when the block or variable is absent.
     *
     * eZINI throws when asked for something that is not there, and a control
     * script that dies because an optional setting was never written is not
     * much use.
     *
     * @param string $block
     * @param string $variable
     * @param mixed $default
     * @return mixed
     */
    protected function setting( $block, $variable, $default = null )
    {
        if ( !$this->ini->hasVariable( $block, $variable ) )
            return $default;

        return $this->ini->variable( $block, $variable );
    }

    /**
     * Absolute path, resolving a relative setting against the root.
     *
     * @param string $path
     * @return string
     */
    protected function absolute( $path )
    {
        $path = (string)$path;
        if ( $path === '' )
            return $this->rootDir;

        return $path[0] === '/' ? $path : $this->rootDir . '/' . $path;
    }

    /**
     * Where the pid file lives.
     *
     * @return string
     */
    public function pidFile()
    {
        return $this->absolute( $this->setting( 'ServerSettings', 'PidFile', 'var/tmp/velocity.pid' ) );
    }

    /**
     * Where the server's own output goes.
     *
     * @return string
     */
    public function logFile()
    {
        return $this->absolute( $this->setting( 'ServerSettings', 'LogFile', 'var/tmp/velocity.log' ) );
    }

    /**
     * The server script this installation drives.
     *
     * @return string
     */
    public function scriptPath()
    {
        return $this->absolute( $this->setting( 'ServerSettings', 'ScriptPath',
            'vendor/se7enxweb/qbix-webserver/qbixserver.php' ) );
    }

    /**
     * A [CacheSettings] value, falling back to its older [ServerSettings] name.
     *
     * The cache settings started out in [ServerSettings] as CacheSkipCookies,
     * CacheStaleWhileRevalidate and so on, and an installation may still set
     * them there. The new block wins when it names a value.
     *
     * @param string $variable name in [CacheSettings]
     * @param string|null $legacy name in [ServerSettings], null when there is none
     * @param mixed $default
     * @return mixed
     */
    protected function cacheSetting( $variable, $legacy, $default )
    {
        $value = $this->setting( 'CacheSettings', $variable, null );
        if ( $value !== null && $value !== '' )
            return $value;
        if ( $legacy !== null )
        {
            $old = $this->setting( 'ServerSettings', $legacy, null );
            if ( $old !== null && $old !== '' )
                return $old;
        }
        return $value !== null ? $value : $default;
    }

    /**
     * The cookies that mean a visitor is signed in, as site.ini names them.
     *
     * @return array
     */
    public function sessionCookies()
    {
        $siteIni = eZINI::instance( 'site.ini' );
        $handler = $siteIni->hasVariable( 'Session', 'SessionNameHandler' )
                 ? (string)$siteIni->variable( 'Session', 'SessionNameHandler' ) : 'default';

        if ( $handler === 'custom' )
        {
            $name = $siteIni->hasVariable( 'Session', 'SessionNamePrefix' )
                  ? (string)$siteIni->variable( 'Session', 'SessionNamePrefix' ) : 'eZSESSID';
        }
        else
        {
            // What PHP will call it. The server process reads the same php.ini
            // as this one, and the kernel does not rename it with this handler.
            $name = (string)ini_get( 'session.name' );
            if ( $name === '' )
                $name = 'PHPSESSID';
        }

        $cookies = array();
        if ( $name !== '' )
            $cookies[] = $name;
        $cookies[] = 'is_logged_in';
        return $cookies;
    }

    /**
     * The default siteaccess, reduced to what is safe in a file name.
     *
     * @return string empty when there is none
     */
    protected function defaultSiteAccess()
    {
        $siteIni = eZINI::instance( 'site.ini' );
        $name = $siteIni->hasVariable( 'SiteSettings', 'DefaultAccess' )
              ? (string)$siteIni->variable( 'SiteSettings', 'DefaultAccess' ) : '';
        return preg_replace( '/[^A-Za-z0-9_-]+/', '', $name );
    }

    /**
     * Whether TLS is configured and the certificate actually exists.
     *
     * Reporting TLS as enabled when the files are missing produces a server
     * that starts and then serves plain HTTP on the port a browser will
     * address as https, which is a confusing way to spend an afternoon.
     *
     * @return bool
     */
    public function httpsEnabled()
    {
        $enabled = $this->setting( 'HTTPSSettings', 'Enabled', 'false' );
        if ( $enabled !== 'true' && $enabled !== 'enabled' && $enabled !== true )
            return false;

        $cert = $this->absolute( $this->setting( 'HTTPSSettings', 'Certificate', '' ) );
        $key  = $this->absolute( $this->setting( 'HTTPSSettings', 'Key', '' ) );

        return $cert !== $this->rootDir && $key !== $this->rootDir
            && is_file( $cert ) && is_file( $key );
    }

    // ── Command line ─────────────────────────────────────────────────────

    /**
     * The command line the server is started with.
     *
     * Built rather than typed, so the parts that must not be forgotten --
     * an explicit worker count, the globals the application keeps -- cannot
     * be left off by accident.
     *
     * @return array
     */
    /**
     * The engine archive the server should run from, or '' for the files on disk.
     *
     * The bootstrap only loads the engine out of an archive when the
     * EXP_ENGINE_PHAR environment variable names one that exists. Nothing set
     * it, so the archive could be built and inspected but never actually run,
     * and the Setup > System information page could only ever report
     * "Individual files on disk". Testing the archive meant exporting the
     * variable by hand into whatever started the server.
     *
     * [ServerSettings]EnginePhar takes:
     *
     *   disabled   run from the files on disk. The default.
     *   enabled    run from dist/engine.phar, wherever expPhar puts it.
     *   <path>     run from that archive, relative to the installation root
     *              or absolute.
     *
     * A path that does not exist is refused rather than ignored: starting from
     * the files on disk after being asked for an archive looks identical to
     * success, and the difference only shows up much later.
     *
     * @return string absolute path, or ''
     */
    public function enginePhar()
    {
        $setting = trim( (string)$this->setting( 'ServerSettings', 'EnginePhar', 'disabled' ) );

        if ( $setting === '' || $setting === 'disabled' || $setting === 'false' )
            return '';

        if ( $setting === 'enabled' || $setting === 'true' )
        {
            if ( !class_exists( 'expPhar' ) )
                @include_once( $this->absolute( 'kernel/classes/expphar.php' ) );
            return class_exists( 'expPhar' ) ? expPhar::enginePath() : '';
        }

        return $this->absolute( $setting );
    }

    public function command()
    {
        $documentRoot = $this->absolute( $this->setting( 'ServerSettings', 'DocumentRoot', '' ) );
        $host    = $this->setting( 'ServerSettings', 'Host', '127.0.0.1' );
        $port    = (int)$this->setting( 'ServerSettings', 'Port', 8088 );
        $workers = (int)$this->setting( 'ServerSettings', 'Workers', 4 );

        $arguments = array( PHP_BINARY );

        // Interpreter settings, applied to this process only.
        //
        // The opcode cache is the single largest win available to this
        // installation: measured on a content page, four workers, requests
        // round-robined between servers so drift hits them equally, it is
        // about 26% off the time to render. Nothing else came close -- an
        // engine archive was within noise, and tracing JIT was *slower* than
        // the plain cache.
        //
        // It has to be asked for here because /etc/php.d/99-no-opcache-cli.ini
        // turns it off for every command-line PHP on this machine. That is a
        // system-wide decision affecting every site on the box, so it is left
        // alone and the setting is made per process instead.
        foreach ( (array)$this->setting( 'PHPSettings', 'IniOptions', array() ) as $iniOption )
        {
            $iniOption = trim( (string)$iniOption );
            if ( $iniOption !== '' )
            {
                $arguments[] = '-d';
                $arguments[] = $iniOption;
            }
        }

        // APCu, when the response cache is to use it.
        //
        // The extension is off for command-line PHP unless apc.enable_cli
        // says otherwise, and the server is command-line PHP. Without this
        // CacheSettings/APCu=enabled did nothing: every entry went to disk,
        // however small. Asked for per process, like the opcode cache above,
        // and only when IniOptions does not already decide it.
        $cacheOn = $this->cacheSetting( 'Enabled', null, 'enabled' );
        $apcuOn = $this->cacheSetting( 'APCu', null, 'enabled' );
        if ( ( $cacheOn === 'enabled' || $cacheOn === 'true' )
             && ( $apcuOn === 'enabled' || $apcuOn === 'true' )
             && !preg_grep( '/^\s*apc\.enable_cli\s*=/',
                            (array)$this->setting( 'PHPSettings', 'IniOptions', array() ) ) )
        {
            $arguments[] = '-d';
            $arguments[] = 'apc.enable_cli=1';
        }

        $arguments[] = $this->scriptPath();
        $arguments = array_merge( $arguments, array(
            '--root=' . $documentRoot,
            '--host=' . $host,
            '--port=' . $port,
            '--workers=' . ( $workers > 0 ? $workers : 4 ),
            '--pid=' . $this->pidFile(),
        ) );

        if ( $this->httpsEnabled() )
            $arguments[] = '--https-port=' . (int)$this->setting( 'ServerSettings', 'HTTPSPort', 8080 );

        $keepGlobals = $this->keepGlobals();
        if ( $keepGlobals )
            $arguments[] = '--keep-globals=' . implode( ',', $keepGlobals );

        $extra = $this->setting( 'ControlSettings', 'ExtraOptions', array() );
        if ( is_array( $extra ) )
            foreach ( $extra as $option )
                if ( trim( (string)$option ) !== '' )
                    $arguments[] = (string)$option;

        return $arguments;
    }

    /**
     * A JSON config file for the server, written from velocity.ini.
     *
     * The server takes its TLS paths through a config file rather than the
     * command line, so one is generated instead of being maintained by hand
     * in two places.
     *
     * @return string|false path, or false when nothing needs configuring
     */
    protected function writeServerConfig()
    {
        $web = array();

        if ( $this->httpsEnabled() )
        {
            $web['https'] = array(
                'mode' => 'manual',
                'cert' => $this->absolute( $this->setting( 'HTTPSSettings', 'Certificate', '' ) ),
                'key'  => $this->absolute( $this->setting( 'HTTPSSettings', 'Key', '' ) ),
            );
        }

        // How long a browser may keep a stylesheet, script or image without
        // asking again.
        //
        // The server's own default permits caching and then requires the
        // client to revalidate anyway, so a page carrying dozens of assets
        // costs dozens of conditional requests on every view. Measured here:
        // 41 assets on the front page, every one revalidated every time, while
        // the same site behind the other web server fetched none of them
        // again.
        //
        // Zero leaves the server's default alone. The trade is staleness: a
        // file replaced in place is not noticed until the lifetime expires,
        // which costs nothing for an asset whose URL changes with its content
        // and is why this is a setting rather than an assumption.
        $staticMaxAge = (int)$this->setting( 'ServerSettings', 'StaticMaxAge', 0 );
        if ( $staticMaxAge > 0 )
            $web['static'] = array( 'maxAge' => $staticMaxAge );

        // HTTP/2, which the server negotiates during the TLS handshake.
        //
        // A page here references 41 assets, and over HTTP/1.1 a browser
        // fetches them about six at a time in waves; over HTTP/2 they share
        // one connection. Measured on this installation, asset delivery was
        // 393-418ms against 85-104ms through a front end that spoke it.
        //
        // Off unless asked for, in the server and here both. The protocol is
        // agreed during the handshake and a client does not fall back
        // afterwards, so a fault is a page that never arrives rather than one
        // that arrives slowly -- which is also why the server now answers any
        // stream still open with a page rather than silence.
        if ( $this->setting( 'ServerSettings', 'HTTP2', 'disabled' ) === 'enabled' )
        {
            $web['http2'] = array( 'enabled' => true );

            $errorImage = (string)$this->setting( 'ServerSettings', 'HTTP2ErrorImage', '' );
            if ( $errorImage !== '' )
                $web['http2']['errorPage'] = array( 'image' => $this->absolute( $errorImage ) );
        }

        // Keep a compressed copy of each static file, instead of building one
        // per request.
        //
        // Every asset was read and gzipped again on every request. The packed
        // bundles this installation serves cost, measured with gzencode at the
        // level the server uses:
        //
        //     641 kB of JavaScript    25.1 ms
        //     389 kB of CSS            9.9 ms
        //     229 kB of JavaScript     9.1 ms
        //
        // That is 44ms of compression on one cold page load, spent producing
        // bytes identical to the last time, in the single event loop every
        // other connection is waiting on.
        //
        // The server has had the cache all along, defaulting to off, and
        // nothing here switched it on.
        $webserver = array();
        $precompress = $this->setting( 'ServerSettings', 'PrecompressStatic', 'enabled' );
        if ( $precompress !== 'disabled' && $precompress !== 'false' )
        {
            $web2 = array( 'enabled' => true );

            $maxFiles = (int)$this->setting( 'ServerSettings', 'PrecompressMaxFiles', 0 );
            if ( $maxFiles > 0 )
                $web2['maxFiles'] = $maxFiles;

            $minSize = (int)$this->setting( 'ServerSettings', 'PrecompressMinSize', 0 );
            if ( $minSize > 0 )
                $web2['minSize'] = $minSize;

            // Under var/, so it is cleared with everything else and is not in
            // a shared temporary directory another site could read.
            $web2['dir'] = $this->absolute( 'var/tmp/precompress' );

            $webserver['precompress'] = $web2;
        }

        // Which cookies mean "this response is personal, do not cache it".
        //
        // The server's own default is PHPSESSID and Q_sid, which are PHP's and
        // Qbix's names and are not what signs anybody in here. The effect was
        // that anyone carrying a stale cookie of either name -- a browser that
        // had ever touched a Qbix application on this host, for instance --
        // bypassed the response cache on every request, while a real signed-in
        // session sailed straight through it.
        //
        // Both halves of that were wrong, and the first was expensive: the same
        // front page is 74ms served from cache and about 1300ms rendered,
        // because the session path runs 529 queries instead of 122. The pages
        // were byte for byte identical apart from two packed asset filenames.
        //
        // Left empty, the list is the session cookie plus is_logged_in.
        //
        // Which name the session cookie has depends on SessionNameHandler.
        // With "custom" it is SessionNamePrefix, followed by md5 of the
        // siteaccess name where SessionNamePerSiteAccess is enabled -- the
        // server matches a skip cookie as a prefix, so the bare prefix covers
        // every siteaccess. With "default", the site.ini default, PHP names it
        // and SessionNamePrefix is not used at all: the cookie is PHPSESSID.
        // Reading only the prefix got that case wrong -- on an installation
        // with the default handler the skip list said eZSESSID, the session
        // cookie was PHPSESSID, and a signed-in request was answered from the
        // cache, or stored in it for everybody else.
        //
        // is_logged_in is the cookie the kernel sets for exactly this purpose
        // (ezpKernelWeb, "for use by http cache solutions"), so it is on the
        // list whichever handler is in use.
        $skip = $this->cacheSetting( 'SkipCookies', 'CacheSkipCookies', '' );
        if ( !is_array( $skip ) )
            $skip = $skip === '' ? array() : array( $skip );
        $skip = array_values( array_filter( array_map( 'trim', $skip ), 'strlen' ) );
        if ( !$skip )
            $skip = $this->sessionCookies();
        $cache = array();
        if ( $skip )
            $cache['skip'] = array( 'cookies' => array_values( $skip ) );

        // How long an expired page may still be served while one request
        // renders the replacement.
        //
        // An entry expires at a moment, so every request in flight for that
        // page misses at once, and before this each of them rendered it. The
        // same front page is 0.6ms from cache and 1382ms rendered, so with a
        // five minute lifetime the page stopped for one and a half seconds
        // every five minutes -- for everyone who happened to be there, all of
        // them doing identical work to produce identical bytes. In the load
        // test it read as p99 111ms at eight concurrent and 182ms at sixteen:
        // a tail that grows with the number of people present, which is a
        // stampede and not load.
        //
        // With this set, one request renders and everyone else is handed the
        // copy that already exists. The trade is that a visitor may see a page
        // up to this many seconds past its lifetime, which for a five minute
        // lifetime is a page at most six minutes old instead of five.
        $grace = $this->cacheSetting( 'StaleWhileRevalidate', 'CacheStaleWhileRevalidate', '60' );
        if ( $grace !== '' && (int)$grace > 0 )
            $cache['staleWhileRevalidate'] = (int)$grace;

        // How long a 404 is remembered.
        //
        // A not-found runs the whole routing and rendering path before
        // concluding there is nothing there. Measured across 278 public URLs
        // on this installation, they cost 220-340ms each and none were cached,
        // so anything walking a list of dead links -- a crawler, an old
        // sitemap, a page of stale links -- paid full price on every request
        // and so did the server, with nothing bounding it.
        //
        // Short, because the cost of being wrong is a page that exists
        // appearing not to. A minute blunts a crawl without visibly delaying
        // a publish.
        $negative = $this->cacheSetting( 'NotFoundSeconds', 'CacheNotFoundSeconds', '60' );
        if ( $negative !== '' && (int)$negative > 0 )
            $cache['negativeTtl'] = (int)$negative;

        // Collapse the indentation eZ's templates ship with.
        //
        // The front page is 94,090 bytes of which 28,934 are whitespace, and
        // collapsing the runs leaves 68,842 -- 27% smaller before compression.
        // gzip already handles repeated whitespace, so the wire saving is
        // small; the decompressed document is the point, because that is what
        // the browser parses and what the navigation cache in the browser
        // stores.
        $minify = $this->cacheSetting( 'MinifyHtml', 'MinifyCachedHtml', 'enabled' );
        if ( $minify !== 'disabled' && $minify !== 'false' )
            $cache['minifyHtml'] = true;

        // Compress stored entries against a shared dictionary.
        //
        // Every page here carries the same head, navigation, footer and asset
        // URLs, and gzip cannot see any of it: its window is 32KB and starts
        // empty for each document, so the hundredth page pays full price for a
        // header the ninety-nine before it also contained. A dictionary is a
        // block of bytes the compressor may reference before it has seen them.
        //
        // Measured on this installation's own 273 cached pages: 1,626,195
        // bytes as plain gzip against 1,290,140 with the dictionary, 20.7%
        // smaller, every one round-tripping exactly.
        //
        // Build the dictionary from a warm cache before switching this on
        // (the engine's middle-out tooling). Without one,
        // entries are simply stored the ordinary way; nothing breaks.
        //
        // MEASURED HERE AND LEFT OFF. Switched on against this cache, 271 of
        // 272 entries were stored uncompressed anyway, because the cache holds
        // bodies in wire form -- already gzipped, so a hit needs no compression
        // work -- and a dictionary cannot shrink gzip output. The 20.7% above
        // is what it saves on the *plain* HTML, and capturing it would mean
        // re-compressing on every hit: 20% of cache memory bought with CPU on
        // every request, on a machine with 24GB free. The wrong trade.
        //
        // It is worth switching on where the cache holds uncompressed bodies,
        // or where memory is the scarce resource rather than CPU.
        $middleOut = $this->cacheSetting( 'MiddleOutCompression', 'MiddleOutCompression', 'disabled' );
        if ( $middleOut === 'enabled' || $middleOut === 'true' )
            $cache['middleOut'] = true;

        // Switch it on, and give Exponential's pages a lifetime.
        //
        // Everything above only tunes a cache the server keeps off unless
        // told otherwise, and nothing here told it: an anonymous front page
        // was rendered on every request, 27 req/s against about 3,500 from
        // the cache on the same machine. Switching it on is not enough by
        // itself either. Exponential sends Cache-Control: no-cache,
        // must-revalidate, which the server does not treat as a refusal, so a
        // page is stored only when it has a lifetime -- its own max-age, or
        // DefaultTtl. With neither, nothing is ever cached.
        //
        // The trade DefaultTtl makes: an anonymous visitor may see a page up
        // to that many seconds old, and a page whose content differs between
        // anonymous visitors without any cookie telling them apart would be
        // shared between them. Signed-in visitors carry the session cookie and
        // are never served from here.
        //
        // Needs qbix-webserver v0.0.4.26 or later: older versions matched
        // SkipCookies by exact name and never recognised <prefix><digest>, so
        // a signed-in visitor's page was stored and handed to everybody else.
        $enabled = $this->cacheSetting( 'Enabled', null, 'enabled' );
        if ( $enabled === 'enabled' || $enabled === 'true' )
        {
            $cache['enabled'] = true;

            $ttl = (int)$this->cacheSetting( 'DefaultTtl', null, '30' );
            if ( $ttl > 0 )
                $cache['defaultTtl'] = $ttl;

            // Under var/, so it is cleared with everything else. Left to
            // itself the server puts it beside the installation, in
            // files/cache/reverse of the directory above the document root.
            $cache['dir'] = $this->cacheDirectory();

            foreach ( array( 'FileMode' => 'fileMode', 'DirMode' => 'dirMode' ) as $variable => $key )
            {
                $value = trim( (string)$this->cacheSetting( $variable, null, '' ) );
                if ( $value !== '' )
                    $cache[$key] = $value;
            }

            // Small responses in shared memory, the rest on disk. The
            // server uses APCu whenever the extension is loaded; this makes
            // it a decision.
            $apcu = $this->cacheSetting( 'APCu', null, 'enabled' );
            $cache['apcu'] = array( 'enabled' => ( $apcu === 'enabled' || $apcu === 'true' ) );
            $apcuMax = (int)$this->cacheSetting( 'APCuMaxSize', null, '0' );
            if ( $apcuMax > 0 )
                $cache['apcu']['maxSize'] = $apcuMax;

            // Clearing expired files. Without MaxAge an entry whose own
            // max-age is a year stays on disk for a year.
            $cache['sweep'] = array(
                'every'  => (int)$this->cacheSetting( 'SweepEvery', null, '300' ),
                'maxAge' => (int)$this->cacheSetting( 'SweepMaxAge', null, '86400' ),
            );
        }

        if ( $cache )
            $web['cache'] = $cache;

        // How long a connection may sit idle before it is closed, and how many
        // requests one may carry.
        //
        // Every new connection pays a TLS handshake, and on this machine that
        // is 21ms against 0.3ms of TCP and 1.5ms of work -- by far the most
        // expensive thing a visitor can be made to do. Session resumption
        // would make the second handshake cheap, but PHP builds an SSL_CTX per
        // accepted socket, so there is no shared session cache to resume
        // against and every connection is a full handshake. Verified with a
        // single worker, so it is not divergent ticket keys between them:
        // openssl s_client -reconnect reports New six times and Reused never.
        //
        // What is left, then, is to make visitors open fewer connections. An
        // idle socket costs a few kilobytes; a handshake costs 21ms of CPU.
        // Someone who reads a page for three minutes and then clicks a link
        // was paying a fresh handshake at the default of 120 seconds, and now
        // does not.
        $idle = (int)$this->setting( 'ServerSettings', 'ConnectionIdleSeconds', '600' );
        if ( $idle > 0 )
            $web['http2']['limits']['idleSeconds'] = $idle;

        $perConnection = (int)$this->setting( 'ServerSettings', 'KeepAliveMaxRequests', '1000' );
        if ( $perConnection > 0 )
            $webserver['keepAlive'] = array( 'max' => $perConnection );

        // Product name shown across the served /Q/ views -- dashboard,
        // docs, panel, manifest. A parameter with a default so the server
        // stays upstream ("Qbix Server") unless this installation names
        // itself. This branch sets it to Exponential Velocity in velocity.ini.
        $brand = trim( (string)$this->setting( 'ServerSettings', 'Brand', '' ) );
        if ( $brand !== '' )
            $webserver['brand'] = $brand;

        // Where the brand name and the maintainer line link. All optional; a
        // fork fills them, the server links nothing it was not given.
        $brandUrl = trim( (string)$this->setting( 'ServerSettings', 'BrandUrl', '' ) );
        if ( $brandUrl !== '' )
            $webserver['brandUrl'] = $brandUrl;
        $maintainer = trim( (string)$this->setting( 'ServerSettings', 'Maintainer', '' ) );
        if ( $maintainer !== '' )
            $webserver['maintainer'] = $maintainer;
        $maintainerUrl = trim( (string)$this->setting( 'ServerSettings', 'MaintainerUrl', '' ) );
        if ( $maintainerUrl !== '' )
            $webserver['maintainerUrl'] = $maintainerUrl;

        // The icon, colours and link-preview text of the /Q/ views: the
        // favicon, home-screen icons, web app manifest and the image a shared
        // link shows. Empty keeps the server's own (the Qbix logo under its
        // default name, otherwise a mark drawn from the brand's first letter).
        // Paths resolve against the installation root.
        foreach ( array( 'BrandMark' => 'brandMark', 'BrandColor' => 'brandColor',
                         'BrandAccent' => 'brandAccent', 'BrandBackground' => 'brandBackground',
                         'BrandDescription' => 'brandDescription' ) as $iniName => $key )
        {
            $value = trim( (string)$this->setting( 'ServerSettings', $iniName, '' ) );
            if ( $value !== '' )
                $webserver[$key] = $value;
        }
        foreach ( array( 'BrandIcon' => 'brandIcon', 'BrandOgImage' => 'brandOgImage' ) as $iniName => $key )
        {
            $value = trim( (string)$this->setting( 'ServerSettings', $iniName, '' ) );
            if ( $value !== '' )
                $webserver[$key] = $this->absolute( $value );
        }

        // Parent warm-up: load the kernel and render a page in the parent
        // before it forks, so workers inherit it shared instead of each
        // building it privately. Off by default; the script closes the DB
        // before returning so no worker shares the parent's connection.
        //
        // It goes to Q.webserver.warmup, never Q.webserver.preload. The server
        // requires a preload file before its source transform exists, so a
        // kernel loaded there keeps the real exit and header() in every
        // worker: eZExecution::cleanExit() then ends the worker rather than
        // the request, and every ezjscore call (the load-more buttons) answers
        // 502 "Worker died". The warm-up key is read after the transform.
        $warmup = trim( (string)$this->setting( 'ServerSettings', 'PreloadWarmup', 'disabled' ) );
        if ( in_array( strtolower( $warmup ), array( 'enabled', 'true', '1', 'yes' ), true ) )
        {
            $warmScript = $this->absolute( 'bin/php/velocity-warmup.php' );
            if ( is_file( $warmScript ) )
                $webserver['warmup'] = $warmScript;
        }
        // One request per worker, then a fresh fork.
        //
        // Persistent workers clear a request's state before the next one, and
        // on this installation that clearing cost more than a new fork: the
        // rendered front page went from 17.5-19.8 to 23.6-23.9 req/s with the
        // mode switched and nothing else. It also leaves nothing for the
        // clearing to miss.
        //
        // Needs qbix-webserver v0.0.4.25 or later: older versions lose the
        // session cookie in this mode, so nobody can sign in.
        $fork = $this->setting( 'ServerSettings', 'ForkPerRequest', 'enabled' );
        if ( $fork === 'enabled' || $fork === 'true' )
            $webserver['forkPerRequest'] = true;

        // The server's access and error log.
        //
        // The server writes neither unless its configuration has a log
        // section, and nothing here wrote one -- so a Velocity install had no
        // record of a single request, only the console output in LogFile.
        //
        // The files are named after the default siteaccess unless named here,
        // so installations sharing a log directory stay apart.
        if ( $this->setting( 'LogSettings', 'Enabled', 'enabled' ) === 'enabled' )
        {
            $log = array(
                'dir' => $this->absolute( $this->setting( 'LogSettings', 'Dir', 'var/log/qbix' ) ),
            );
            foreach ( array( 'Format' => 'format', 'FileMode' => 'fileMode',
                             'DirMode' => 'dirMode' ) as $variable => $key )
            {
                $value = trim( (string)$this->setting( 'LogSettings', $variable, '' ) );
                if ( $value !== '' )
                    $log[$key] = $value;
            }

            $site = $this->defaultSiteAccess();
            foreach ( array( 'AccessName' => array( 'accessName', 'access' ),
                             'ErrorName'  => array( 'errorName', 'error' ) ) as $variable => $target )
            {
                $name = trim( (string)$this->setting( 'LogSettings', $variable, '' ) );
                if ( $name === '' )
                    $name = ( $site !== '' ? $site . '-' : '' ) . $target[1] . '.log';
                $log[$target[0]] = $name;
            }
            $webserver['log'] = $log;
        }

        // A dynamic worker pool.
        //
        // Every worker costs its own memory from the moment it is forked:
        // measured here as ~10 MB of real RAM each (AnonPages), idle or not,
        // because that much of the warmed parent is copied at fork. A fixed
        // pool of 590 therefore held ~6 GB while serving a few dozen requests
        // at a time. With SpareWorkers set, the server starts that many, forks
        // more when all of them are busy -- up to Workers -- and retires the
        // extras after IdleWorkerTimeout seconds without a request. Zero keeps
        // the fixed pool.
        $spare = (int)$this->setting( 'ServerSettings', 'SpareWorkers', 0 );
        if ( $spare > 0 )
        {
            $webserver['spareWorkers'] = $spare;
            $idleTimeout = (int)$this->setting( 'ServerSettings', 'IdleWorkerTimeout', 60 );
            if ( $idleTimeout > 0 )
                $webserver['idleWorkerTimeout'] = $idleTimeout;
        }

        // Where the server pre-transforms PHP before forking.
        //
        // Its default is the directory above the document root, on the
        // assumption that the root is a public/ inside the project. Here the
        // document root is the installation itself, so the directory above it
        // holds every sibling installation on the host, and the server walked
        // and cached all of them in memory every worker inherits.
        $compat = array( 'prewarmDir' => $this->absolute( $this->setting( 'ServerSettings', 'DocumentRoot', '' ) ) );

        // Who may use the server's own admin views (/Q/dashboard, /Q/stats,
        // /Q/metrics, the full /Q/health, /Q/phpinfo). The server answers
        // them only from this machine unless a token is given (then the
        // token is required) or DashboardRemote allows remote access without
        // one. /Q/phpinfo, which shows the process environment, is never
        // remote without the token.
        $dashboard = array();
        $dashToken = trim( (string)$this->setting( 'DashboardSettings', 'Token', '' ) );
        if ( $dashToken !== '' )
            $dashboard['token'] = $dashToken;
        $dashRemote = strtolower( trim( (string)$this->setting( 'DashboardSettings', 'Remote', 'disabled' ) ) );
        if ( in_array( $dashRemote, array( 'enabled', 'true', '1', 'yes' ), true ) )
            $dashboard['remote'] = true;

        $config = array( 'Q' => array( 'web' => $web, 'compat' => $compat ) );
        if ( $webserver )
            $config['Q']['webserver'] = $webserver;
        if ( $dashboard )
            $config['Q']['dashboard'] = $dashboard;

        $path = $this->absolute( 'var/tmp/velocity-server.json' );
        $directory = dirname( $path );
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );

        if ( file_put_contents( $path, json_encode( $config, JSON_PRETTY_PRINT ) ) === false )
            return false;

        @chmod( $path, 0600 );
        return $path;
    }

    // ── Process state ────────────────────────────────────────────────────

    /**
     * Process ids belonging to this server.
     *
     * The pid file names the parent only. The parent forks a pool, and a stop
     * that leaves the children running leaves the port held, so the whole
     * family is found by matching the script path -- which is specific enough
     * not to catch an unrelated server driven from another checkout.
     *
     * @return array
     */
    public function processIDs()
    {
        $script = $this->scriptPath();
        $pids = array();
        foreach ( $this->processTable() as $proc )
            if ( strpos( $proc['args'], $script ) !== false )
                $pids[] = $proc['pid'];

        return $pids;
    }

    /**
     * Every process as array( pid, ppid, args ).
     *
     * From /proc where there is one -- Linux, and so every container -- and
     * from ps elsewhere. This used ps alone, and a container image without
     * procps has none: start then reported "did not start" although the
     * server was running, and stop believed it was not running and did
     * nothing.
     *
     * @return array
     */
    protected function processTable()
    {
        $table = array();
        if ( is_dir( '/proc/self' ) )
        {
            foreach ( glob( '/proc/[0-9]*', GLOB_ONLYDIR ) ?: array() as $dir )
            {
                $stat = @file_get_contents( $dir . '/stat' );
                $cmd  = @file_get_contents( $dir . '/cmdline' );
                if ( $stat === false || $cmd === false || $cmd === '' )
                    continue;
                // "pid (comm) state ppid ..."; comm may contain spaces and parentheses.
                $rest = explode( ' ', substr( $stat, strrpos( $stat, ')' ) + 2 ) );
                // A zombie has exited: it is not running anything.
                if ( ( $rest[0] ?? '' ) === 'Z' )
                    continue;
                $table[] = array( 'pid' => (int)basename( $dir ), 'ppid' => (int)( $rest[1] ?? 0 ),
                                  'args' => trim( str_replace( "\0", ' ', $cmd ) ) );
            }
            return $table;
        }

        $output = array();
        @exec( 'ps -eo pid=,ppid=,args= 2>/dev/null', $output );
        foreach ( $output as $line )
        {
            $parts = preg_split( '/\s+/', trim( $line ), 3 );
            if ( count( $parts ) === 3 && ctype_digit( $parts[0] ) && ctype_digit( $parts[1] ) )
                $table[] = array( 'pid' => (int)$parts[0], 'ppid' => (int)$parts[1], 'args' => $parts[2] );
        }
        return $table;
    }

    /**
     * The worker processes belonging to a given parent.
     *
     * Used to tell a reload that happened from one that did not: the parent
     * keeps its pid across a re-exec, so only the set of children it has
     * forked shows whether it started over.
     *
     * @param int $parent
     * @return array of int, empty when there are none or ps is unavailable
     */
    public function childIDs( $parent )
    {
        $parent = (int)$parent;
        if ( $parent <= 0 )
            return array();

        $script = $this->scriptPath();
        $pids = array();
        foreach ( $this->processTable() as $proc )
            if ( $proc['ppid'] === $parent && strpos( $proc['args'], $script ) !== false )
                $pids[] = $proc['pid'];

        sort( $pids );
        return $pids;
    }

    /**
     * The parent process id, from the pid file, when it is still alive.
     *
     * @return int|false
     */
    public function parentID()
    {
        $file = $this->pidFile();
        if ( !is_file( $file ) )
            return false;

        $pid = (int)trim( (string)file_get_contents( $file ) );
        if ( $pid <= 0 )
            return false;

        return $this->isAlive( $pid ) ? $pid : false;
    }

    /**
     * @param int $pid
     * @return bool
     */
    protected function isAlive( $pid )
    {
        if ( function_exists( 'posix_kill' ) )
            return @posix_kill( $pid, 0 );

        return is_dir( '/proc/' . (int)$pid );
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->processIDs() ? true : false;
    }

    /**
     * Ports the server is listening on.
     *
     * @return array
     */
    public function listeningPorts()
    {
        $wanted = array( (int)$this->setting( 'ServerSettings', 'Port', 8088 ) );
        if ( $this->httpsEnabled() )
            $wanted[] = (int)$this->setting( 'ServerSettings', 'HTTPSPort', 8080 );

        // Listening sockets from /proc/net where there is one (Linux, every
        // container), else from ss, else by connecting to the port. This used
        // ss alone, which a minimal image does not have.
        $open = array();
        $procFiles = array_filter( array( '/proc/net/tcp', '/proc/net/tcp6' ), 'is_readable' );
        if ( $procFiles )
        {
            foreach ( $procFiles as $file )
                foreach ( array_slice( file( $file, FILE_IGNORE_NEW_LINES ) ?: array(), 1 ) as $row )
                {
                    $cols = preg_split( '/\s+/', trim( $row ) );
                    // local_address is HEXIP:HEXPORT; state 0A is LISTEN.
                    if ( isset( $cols[3] ) && $cols[3] === '0A' && strpos( $cols[1], ':' ) !== false )
                        $open[hexdec( substr( $cols[1], strrpos( $cols[1], ':' ) + 1 ) )] = true;
                }
        }
        else
        {
            $output = array();
            @exec( 'ss -ltn 2>/dev/null', $output );
            foreach ( $output as $line )
                if ( preg_match_all( '/:(\d+)\s/', $line, $m ) )
                    foreach ( $m[1] as $p )
                        $open[(int)$p] = true;
        }

        $listening = array();
        foreach ( $wanted as $port )
        {
            if ( isset( $open[$port] ) )
            {
                $listening[] = $port;
                continue;
            }
            // Nothing to read the socket table from: ask the port itself.
            if ( !$procFiles && !$open )
            {
                $host = (string)$this->setting( 'ServerSettings', 'Host', '127.0.0.1' );
                if ( $host === '0.0.0.0' || $host === '' )
                    $host = '127.0.0.1';
                $s = @fsockopen( $host, $port, $errno, $errstr, 1 );
                if ( $s )
                {
                    fclose( $s );
                    $listening[] = $port;
                }
            }
        }

        return $listening;
    }

    // ── Verbs ────────────────────────────────────────────────────────────

    /**
     * Start the server, unless it is already running.
     *
     * @return array
     */
    public function start()
    {
        if ( $this->isRunning() )
            return $this->result( false, 'already running', $this->status() );

        $script = $this->scriptPath();
        if ( !is_file( $script ) )
            return $this->result( false, "no server script at $script" );

        $log = $this->logFile();
        $directory = dirname( $log );
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );

        $arguments = $this->command();
        $config = $this->writeServerConfig();
        $this->layoutNote = '';
        $layoutEnv = array();
        if ( $config !== false )
        {
            // The Debian Apache-style tree (/etc/vc: vc.conf, ports.conf,
            // mods-enabled, conf-enabled, sites-enabled/<site>.conf), brought
            // up to date from the same settings and used only if, merged the
            // way the engine merges, it is exactly the file just written.
            // Otherwise that single file is used, as before.
            $serverArgs = array( '--config=' . $config );
            $layout = $this->layout();
            $applied = $layout->apply(
                (array)json_decode( (string)file_get_contents( $config ), true ),
                array( 'http' => (int)$this->setting( 'ServerSettings', 'Port', 8088 ),
                       'https' => $this->httpsEnabled() ? (int)$this->setting( 'ServerSettings', 'HTTPSPort', 8080 ) : null ) );
            $this->logLayout( $applied );
            if ( !empty( $applied['ok'] ) )
            {
                $serverArgs = array( '--conf-dir=' . $applied['confDir'], '--config=' . $applied['siteFile'] );
                $layoutEnv = $layout->envvars( $applied['confDir'] );
            }
            $this->layoutNote = $applied['message'];

            // Insert immediately after the server script, wherever that is.
            // This used to splice at a fixed index on the assumption that the
            // script was always the second element. Interpreter settings now
            // come before it, so the fixed index landed between -d and its
            // value and PHP read the value as the file to run: "Could not
            // open input file: opcache.enable_cli=1".
            $scriptIndex = array_search( $this->scriptPath(), $arguments, true );
            if ( $scriptIndex === false )
                $scriptIndex = count( $arguments ) - 1;

            array_splice( $arguments, $scriptIndex + 1, 0, $serverArgs );
        }

        // The engine archive, if one was asked for.
        //
        // It has to be in the environment rather than on the command line
        // because autoload.php reads it before it has parsed anything, which
        // is necessarily before any argument could be looked at.
        $environment = '';
        $enginePhar = $this->enginePhar();
        if ( $enginePhar !== '' )
        {
            if ( !file_exists( $enginePhar ) )
                return $this->result( false,
                    'EnginePhar names an archive that does not exist: ' . $enginePhar
                    . ' -- build it first, or set EnginePhar=disabled',
                    $this->status() );

            // An archive older than the kernel on disk runs the old kernel:
            // every class it carries is taken from it, not from the file you
            // just changed. That kept a day of kernel fixes off the site while
            // their CLI tests (which read the disk) passed. A stale archive is
            // rebuilt before the server can load from it.
            $ready = $this->ensureEngineArchive();
            if ( $ready !== true )
                return $this->result( false, $ready, $this->status() );

            $environment = 'EXP_ENGINE_PHAR=' . escapeshellarg( $enginePhar ) . ' ';
        }

        // Started from cron, a systemd unit or a stripped container, PATH can
        // be empty or minimal. setsid was then not found, nothing started, and
        // a restart had already stopped the running server. The server gets a
        // usable PATH too, for whatever it runs in turn (image converters).
        if ( trim( (string)getenv( 'PATH' ) ) === '' )
            $environment .= 'PATH=' . escapeshellarg( self::DEFAULT_PATH ) . ' ';

        // envvars from the configuration directory, as Apache's apache2ctl
        // reads /etc/apache2/envvars: values only, nothing executed.
        foreach ( $layoutEnv as $name => $value )
            $environment .= $name . '=' . escapeshellarg( $value ) . ' ';

        // setsid detaches the server from this process group, so it is not
        // taken down with the shell or the script that started it. Without
        // one the server still starts, backgrounded and with no terminal.
        $setsid = $this->findExecutable( 'setsid' );
        $command = $environment . ( $setsid !== false ? escapeshellarg( $setsid ) . ' ' : '' )
                 . implode( ' ', array_map( 'escapeshellarg', $arguments ) )
                 . ' > ' . escapeshellarg( $log ) . ' 2>&1 < /dev/null &';

        @exec( $command );

        $deadline = microtime( true ) + 20;
        while ( microtime( true ) < $deadline )
        {
            if ( $this->listeningPorts() )
                return $this->result( true, ( $this->engineRebuilt
                    ? 'started (engine archive rebuilt first: ' . $this->engineRebuilt . ' kernel file(s) had changed)'
                    : 'started' ) . ( $this->layoutNote !== '' ? '; ' . $this->layoutNote : '' ), $this->status() );

            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
        }

        if ( $this->isRunning() )
            return $this->result( true, 'started, but no port is listening yet', $this->status() );

        return $this->result( false, 'did not start; see ' . $log, $this->status() );
    }

    /**
     * Stop the server and wait for it to go.
     *
     * Graceful in the sense that matters: the parent is asked to end, the
     * pool follows, and anything still alive after the timeout is reported
     * rather than silently left behind.
     *
     * @param int $signal
     * @return array
     */
    public function stop( $signal = null )
    {
        if ( $signal === null )
            $signal = defined( 'SIGTERM' ) ? SIGTERM : 15;

        $pids = $this->processIDs();
        if ( !$pids )
            return $this->result( true, 'not running' );

        // Ask the server to stop itself first. It knows the order to take its
        // own pool down in; signalling is the fallback for when it cannot.
        $this->control( '--stop' );

        $settle = microtime( true ) + 3;
        while ( microtime( true ) < $settle )
        {
            if ( !$this->isRunning() )
            {
                @unlink( $this->pidFile() );
                return $this->result( true, 'stopped' );
            }
            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
        }

        $pids = $this->processIDs();
        if ( !$pids )
        {
            @unlink( $this->pidFile() );
            return $this->result( true, 'stopped' );
        }

        // The parent first, so it can take its own pool down in order.
        $parent = $this->parentID();
        if ( $parent && in_array( $parent, $pids, true ) )
        {
            $this->signal( $parent, $signal );
            array_unshift( $pids, $parent );
        }

        foreach ( $pids as $pid )
            $this->signal( $pid, $signal );

        $timeout = (float)$this->setting( 'ControlSettings', 'StopTimeout', 15 );
        $deadline = microtime( true ) + ( $timeout > 0 ? $timeout : 15 );

        while ( microtime( true ) < $deadline )
        {
            if ( !$this->isRunning() )
            {
                @unlink( $this->pidFile() );
                return $this->result( true, 'stopped' );
            }
            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );
        }

        return $this->result( false, 'still running after ' . $timeout . 's; try kill',
                              $this->status() );
    }

    /**
     * Ask a running server to re-exec, keeping the listening socket.
     *
     * Falls back to a restart when the server is not running, so the verb
     * does something sensible either way.
     *
     * @return array
     */
    public function graceful()
    {
        $parent = $this->parentID();
        if ( !$parent )
            return $this->restart();

        // What the workers are before the reload, so we can tell afterwards
        // whether one actually happened.
        //
        // Waiting for the ports to come back cannot tell us that. A re-exec
        // keeps the listening socket open across it -- that is the whole point
        // of the verb -- so from out here the ports never go away, the very
        // first poll succeeds, and the reload is reported as done whether or
        // not anything happened. It reported success for a complete no-op:
        // parent and every worker carried on with the code they were already
        // running, while the operator believed the new code was live.
        //
        // The parent keeps its pid across a re-exec, because that is what exec
        // does, so the parent is no use as a witness either. The workers are:
        // the re-executed parent forks a fresh set, so a changed set of
        // children is the one observable fact that means it really happened.
        $before = $this->childIDs( $parent );

        $this->control( '--reload' );

        $deadline = microtime( true ) + 20;
        while ( microtime( true ) < $deadline )
        {
            usleep( (int)( self::POLL_INTERVAL * 1000000 ) );

            $after = $this->childIDs( $parent );

            // A reload is finished when the old workers are gone, new ones are
            // up, and the ports are answering again. Requiring all three stops
            // us reporting success while it is still half-way through.
            if ( $after && !array_intersect( $before, $after )
                 && $this->listeningPorts() )
            {
                return $this->result( true, 'reloaded', $this->status() );
            }
        }

        // It did not come back. Leaving a half-reloaded server behind is
        // worse than the reload having failed, so put it back on its feet.
        $this->kill();
        $started = $this->start();
        return $this->result( $started['ok'],
            'reload did not come back; restarted instead', $started['data'] );
    }

    /**
     * Stop, then start.
     *
     * @return array
     */
    /**
     * Rebuild the engine archive if any file it carries is newer on disk.
     *
     * Safe to call with the server running: the running processes loaded
     * their classes long ago, and the new archive is only read at the next
     * start.
     *
     * @return true|string true when the archive is current (or not used),
     *         otherwise why it could not be made so
     */
    protected function ensureEngineArchive()
    {
        $enginePhar = $this->enginePhar();
        if ( $enginePhar === '' || !file_exists( $enginePhar ) )
            return true;   // start() reports a missing archive itself

        $stale = $this->staleEngineFiles( $enginePhar );
        if ( !$stale )
            return true;

        $out = array(); $code = 0;
        @exec( escapeshellarg( PHP_BINARY ) . ' -d phar.readonly=0 '
            . escapeshellarg( $this->absolute( 'bin/php/phar.php' ) )
            . ' build --allow-root-user --output=' . escapeshellarg( $enginePhar ) . ' 2>&1', $out, $code );
        clearstatcache();
        $still = $code === 0 ? $this->staleEngineFiles( $enginePhar ) : $stale;
        if ( $code !== 0 || $still )
            return 'the engine archive is older than ' . count( $stale ) . ' kernel file(s) (e.g. '
                . implode( ', ', array_slice( $stale, 0, 3 ) ) . ') and could not be rebuilt: '
                . trim( implode( ' ', array_slice( $out, -3 ) ) )
                . ' -- run php bin/php/phar.php build --allow-root-user, or set EnginePhar=disabled';

        $this->engineRebuilt = count( $stale );
        return true;
    }

    public function restart()
    {
        // Everything that can refuse a start is checked while the running
        // server is still up. This stopped first and rebuilt the engine
        // archive afterwards, so a rebuild that failed left no server at all
        // (2026-09-24: about a minute of "can't connect" on alpha).
        $ready = $this->ensureEngineArchive();
        if ( $ready !== true )
            return $this->result( false, $ready . ' -- the running server was left as it is', $this->status() );

        $stopped = $this->stop();
        if ( !$stopped['ok'] && $this->isRunning() )
            return $this->result( false, 'could not stop: ' . $stopped['message'], $this->status() );

        return $this->start();
    }

    /**
     * Stop without asking.
     *
     * For when a worker is wedged and will not answer a polite signal.
     *
     * @return array
     */
    public function kill()
    {
        $pids = $this->processIDs();
        if ( !$pids )
            return $this->result( true, 'not running' );

        $signal = defined( 'SIGKILL' ) ? SIGKILL : 9;
        foreach ( $pids as $pid )
            $this->signal( $pid, $signal );

        usleep( (int)( self::POLL_INTERVAL * 2 * 1000000 ) );
        @unlink( $this->pidFile() );

        $left = $this->processIDs();
        if ( $left )
            return $this->result( false, count( $left ) . ' process(es) survived SIGKILL',
                                  $this->status() );

        return $this->result( true, 'killed ' . count( $pids ) . ' process(es)' );
    }

    /**
     * Add globals to keep for this run (--keep-global), after the defaults and
     * the settings.
     *
     * @param array|string $names names, or a comma-separated list
     */
    public function appendKeepGlobals( $names )
    {
        foreach ( (array)$names as $list )
            foreach ( explode( ',', (string)$list ) as $name )
                if ( ( $name = trim( $name ) ) !== '' )
                    $this->extraKeepGlobals[] = $name;
    }

    /**
     * The globals the server keeps between requests: the kernel's defaults,
     * then [ApplicationSettings] KeepGlobals[], then --keep-global -- each
     * appended, never replacing, and each name once, in that order.
     *
     * @return array
     */
    public function keepGlobals()
    {
        $defaults = strtolower( trim( (string)$this->setting( 'ApplicationSettings', 'KeepGlobalsDefaults', 'enabled' ) ) );
        $list = in_array( $defaults, array( 'disabled', 'false', 'no', '0' ), true ) ? array() : self::DEFAULT_KEEP_GLOBALS;
        $configured = $this->setting( 'ApplicationSettings', 'KeepGlobals', array() );
        $list = array_merge( $list, is_array( $configured ) ? $configured : array(), $this->extraKeepGlobals );

        $seen = array();
        $out = array();
        foreach ( $list as $name )
        {
            $name = trim( (string)$name );
            // Global names only: the list goes onto a command line and into
            // the server's snapshot rules.
            if ( $name === '' || isset( $seen[$name] ) || !preg_match( '/^[A-Za-z_][A-Za-z0-9_]*$/', $name ) )
                continue;
            $seen[$name] = true;
            $out[] = $name;
        }
        return $out;
    }

    /**
     * A [LayoutSettings] value, for expVelocityConfigLayout.
     *
     * @param string $variable
     * @param mixed $default
     * @return mixed
     */
    public function layoutSetting( $variable, $default = null )
    {
        return $this->setting( 'LayoutSettings', $variable, $default );
    }

    /**
     * An absolute path, a relative one resolved against the installation root.
     *
     * @param string $path
     * @return string
     */
    public function absolutePath( $path )
    {
        return $this->absolute( $path );
    }

    /**
     * The configuration tree for this installation (Debian Apache style).
     *
     * @return expVelocityConfigLayout
     */
    public function layout()
    {
        if ( !class_exists( 'expVelocityConfigLayout', false ) )
            require_once __DIR__ . '/expvelocityconfiglayout.php';
        return new expVelocityConfigLayout( $this );
    }

    /**
     * Every file and directory the server uses, in one place: what
     * `exp:velocity layout` lists and what the site's metadata records.
     *
     * @return array name => path (null where not in use)
     */
    public function assets()
    {
        $layout = $this->layout();
        $confDir = $layout->confDir();
        return array(
            'confDir'        => $confDir,
            'siteFile'       => $layout->siteFile( true ),
            'metadata'       => $confDir !== null ? $layout->metadataFile() : null,
            'legacyConfig'   => $this->absolute( 'var/tmp/velocity-server.json' ),
            'pidFile'        => $this->pidFile(),
            'serverLog'      => $this->logFile(),
            'accessErrorLogs'=> $this->absolute( $this->setting( 'LogSettings', 'Dir', 'var/log/qbix' ) ),
            'responseCache'  => $this->cacheDirectory(),
            'cacheMarker'    => $this->cacheDirectory() . '/.generation',
            'precompress'    => $this->absolute( 'var/tmp/precompress' ),
            'compatPrewarm'  => sys_get_temp_dir() . '/qbixserver-compat',
            'certificate'    => $this->httpsEnabled() ? $this->absolute( $this->setting( 'HTTPSSettings', 'Certificate', '' ) ) : null,
            'certificateKey' => $this->httpsEnabled() ? $this->absolute( $this->setting( 'HTTPSSettings', 'Key', '' ) ) : null,
            'warmup'         => $this->absolute( 'bin/php/velocity-warmup.php' ),
            'engineScript'   => $this->scriptPath(),
            'engineArchive'  => $this->enginePhar() !== '' ? $this->enginePhar() : null,
        );
    }

    /**
     * Bring the configuration tree up to date now, without starting anything:
     * what start() does first, for `exp:velocity layout migrate`.
     *
     * @return array result
     */
    public function migrateLayout()
    {
        $config = $this->writeServerConfig();
        if ( $config === false )
            return $this->result( false, 'could not write the server configuration' );
        $applied = $this->layout()->apply(
            (array)json_decode( (string)file_get_contents( $config ), true ),
            array( 'http' => (int)$this->setting( 'ServerSettings', 'Port', 8088 ),
                   'https' => $this->httpsEnabled() ? (int)$this->setting( 'ServerSettings', 'HTTPSPort', 8080 ) : null ) );
        $this->logLayout( $applied );
        return $this->result( !empty( $applied['ok'] ), $applied['message'], $applied );
    }

    /**
     * Append what the configuration tree did to its log. The server's own log
     * is truncated on every start, so this one is kept apart and appended to:
     * a migration is exactly what should still be readable later.
     *
     * @param array $applied expVelocityConfigLayout::apply()'s result
     */
    protected function logLayout( array $applied )
    {
        $file = $this->absolute( $this->setting( 'LogSettings', 'Dir', 'var/log/qbix' ) ) . '/velocity-layout.log';
        if ( !is_dir( dirname( $file ) ) )
            eZDir::mkdir( dirname( $file ), false, true );
        $lines = array();
        foreach ( (array)( $applied['actions'] ?? array() ) as $action )
            $lines[] = date( 'c' ) . '  ' . $action;
        $lines[] = date( 'c' ) . '  ' . ( !empty( $applied['ok'] ) ? 'using ' : 'not using the tree: ' ) . $applied['message'];
        @file_put_contents( $file, implode( "\n", $lines ) . "\n", FILE_APPEND );
    }

    /**
     * Where the response cache lives. Under var/, so it is cleared with
     * everything else; left to itself the server puts it beside the
     * installation, in files/cache/reverse of the directory above the root.
     *
     * @return string
     */
    public function cacheDirectory()
    {
        $dir = trim( (string)$this->cacheSetting( 'Dir', null, '' ) );
        return $this->absolute( $dir !== '' ? $dir : 'var/cache/qbix-reverse' );
    }

    /**
     * Invalidate every page the server has cached, now.
     *
     * The response cache revalidates against what the kernel says about a
     * page, and the kernel judges by content: a template or stylesheet changed
     * on disk moves nothing it looks at, so pages rendered from the old one
     * were served until they aged out. This touches the cache's generation
     * marker, which the server reads at most once a second; every entry
     * stored before it -- on disk and in the server's APCu -- is a miss from
     * then on. Nothing is deleted here and the server need not be running;
     * stale files are removed as they are next looked up.
     *
     * @return array result
     */
    public function clearCache()
    {
        $dir = $this->cacheDirectory();
        $marker = $dir . '/.generation';

        // The engine does this itself now (Q_WebServer_Ctl::clearCache(), also
        // `qbixctl`'s cache:clear); asked first, so both stay one behaviour.
        if ( $this->engineCtl() )
        {
            list( $ok, $message ) = Q_WebServer_Ctl::clearCache( $dir );
            return $this->result( $ok, $ok ? 'response cache cleared (every page stored before now is re-rendered on its next request)' : $message,
                                  array( 'marker' => $marker ) );
        }

        if ( !is_dir( $dir ) && !@mkdir( $dir, 0750, true ) )
            return $this->result( false, 'cache directory does not exist and could not be created: ' . $dir );
        if ( !@touch( $marker ) )
            return $this->result( false, 'could not touch ' . $marker );

        return $this->result( true, 'response cache cleared (every page stored before now is re-rendered on its next request)',
                              array( 'marker' => $marker ) );
    }

    /**
     * Load the engine's control class (qbixctl's built-ins) when the engine
     * in use ships it. False with an older engine: callers keep their own
     * implementation then.
     *
     * @return bool
     */
    public function engineCtl()
    {
        if ( class_exists( 'Q_WebServer_Ctl', false ) )
            return true;
        $src = dirname( $this->scriptPath() ) . '/src';
        foreach ( array( 'Q/Console.php', 'Q/WebServer/Layout.php', 'Q/WebServer/Ctl.php' ) as $file )
        {
            if ( !is_file( "$src/$file" ) )
                return false;
        }
        if ( !class_exists( 'Q_Config', false ) && is_file( "$src/Q.php" ) && !class_exists( 'Q', false ) )
        {
            // The engine's Q.php defines Q_Config, which Ctl reads; loaded
            // only when nothing of the engine is loaded yet.
            require_once "$src/Q.php";
        }
        require_once "$src/Q/Console.php";
        require_once "$src/Q/WebServer/Layout.php";
        require_once "$src/Q/WebServer/Ctl.php";
        Q_WebServer_Ctl::$sourceDir = dirname( $this->scriptPath() );
        return class_exists( 'Q_WebServer_Ctl', false );
    }

    /**
     * Run the engine's qbixctl with this installation's configuration:
     * `exp:velocity ctl -S`, `ctl status`, `ctl ensite NAME` ...
     *
     * @param array $args qbixctl arguments
     * @return int exit code
     */
    public function ctl( array $args )
    {
        $ctl = dirname( $this->scriptPath() ) . '/qbixctl.php';
        if ( !is_file( $ctl ) )
        {
            fwrite( STDERR, "this engine has no qbixctl.php ($ctl)\n" );
            return 1;
        }
        $layout = $this->layout();
        $confDir = $layout->confDir();
        $fixed = array( '--pid=' . $this->pidFile() );
        if ( $confDir !== null && is_file( (string)$layout->siteFile( true ) ) )
        {
            $fixed[] = '--conf-dir=' . $confDir;
            $fixed[] = '--config=' . $layout->siteFile( true );
        }
        $command = implode( ' ', array_map( 'escapeshellarg', array_merge( array( PHP_BINARY, $ctl ), $args, $fixed ) ) );
        passthru( $command, $code );
        return (int)$code;
    }

    /**
     * What the server is doing.
     *
     * @return array
     */
    public function status()
    {
        $pids = $this->processIDs();
        $ports = $this->listeningPorts();

        return array(
            'running'   => $pids ? true : false,
            'parent'    => $this->parentID(),
            'processes' => count( $pids ),
            'pids'      => $pids,
            'listening' => $ports,
            'https'     => $this->httpsEnabled(),
            // Which engine the running server was started with. Worth stating
            // here because it cannot be told apart from the outside: an
            // archive that failed to load and a run from the files on disk
            // look exactly the same until something behaves oddly.
            'engine'    => ( $this->enginePhar() === '' ) ? 'files on disk' : $this->enginePhar(),
            'script'    => $this->scriptPath(),
            'log'       => $this->logFile(),
            'pidFile'   => $this->pidFile(),
        );
    }

    // ── Internals ────────────────────────────────────────────────────────

    /**
     * An executable's absolute path, from PATH and then the usual system
     * directories, so an empty PATH does not hide it.
     *
     * @param string $name
     * @return string|false
     */
    protected function findExecutable( $name )
    {
        $dirs = array_merge( explode( PATH_SEPARATOR, (string)getenv( 'PATH' ) ),
                             explode( PATH_SEPARATOR, self::DEFAULT_PATH ) );
        foreach ( array_unique( array_filter( $dirs ) ) as $dir )
        {
            $candidate = rtrim( $dir, '/' ) . '/' . $name;
            if ( is_file( $candidate ) && is_executable( $candidate ) )
                return $candidate;
        }
        return false;
    }

    /**
     * Run the server script with one of its own control options.
     *
     * The server implements --stop and --reload itself, against the pid file
     * it wrote. Those are the supported ways to ask it to do something, and
     * they do it in the order it expects. Signalling the parent directly is a
     * guess about its internals: SIGHUP, the obvious guess for a reload, took
     * the worker pool down and left the parent alive holding no ports.
     *
     * @param string $option --stop or --reload
     * @return bool whether the command could be run at all
     */
    protected function control( $option )
    {
        $script = $this->scriptPath();
        if ( !is_file( $script ) )
            return false;

        $command = implode( ' ', array_map( 'escapeshellarg', array(
            PHP_BINARY, $script, $option, '--pid=' . $this->pidFile()
        ) ) ) . ' > /dev/null 2>&1';

        @exec( $command, $output, $status );
        return $status === 0;
    }

    /**
     * @param int $pid
     * @param int $signal
     * @return bool
     */
    protected function signal( $pid, $signal )
    {
        if ( function_exists( 'posix_kill' ) )
            return @posix_kill( (int)$pid, (int)$signal );

        @exec( 'kill -' . (int)$signal . ' ' . (int)$pid . ' 2>/dev/null' );
        return true;
    }

    /**
     * @param bool $ok
     * @param string $message
     * @param mixed $data
     * @return array
     */
    /** How many changed kernel files made start() rebuild the archive; 0 if none. */
    protected $engineRebuilt = 0;

    /**
     * Files the engine archive packages that have changed on disk since it
     * was built, as paths relative to the root. Empty when it is current.
     *
     * @param string $archive
     * @return array
     */
    public function staleEngineFiles( $archive )
    {
        $built = @filemtime( $archive );
        if ( $built === false || !class_exists( 'expPhar' ) )
            return array();
        $root = expPhar::root();
        $stale = array();
        foreach ( expPhar::collect() as $rel )
        {
            $m = @filemtime( $root . '/' . $rel );
            if ( $m !== false && $m > $built )
                $stale[] = $rel;
        }
        return $stale;
    }

    /**
     * Rewrite a command line into the one form eZCLI::getOptions() reads, so
     * exp:velocity takes GNU and BSD spellings alike, as the engine's own
     * qbixconsole, qbixctl and qbixserver.php do:
     *
     *   --name=V  --name V  -name=V  -name V   an option that takes a value
     *   --name    -name                        a flag
     *   --no-name                              a flag turned off (last one wins)
     *   --                                     ends the options
     *
     * Only a word that is a known long name is read as a single-dash long
     * option, so eZ's one-letter options (-s admin, -v, -d) keep their meaning.
     * A value option takes the next word only when it does not start with a
     * dash. A real option named no-something (--no-colors) is left alone.
     * Everything after "--" is handed back apart, never parsed, so it reaches
     * the engine's own console untouched (exp:velocity ctl status -- --json).
     *
     * @param array $args the command line, without the program name
     * @param array $valued long names that take a value
     * @param array $flags long names that do not
     * @return array array( $argumentsForEzcli, $afterDoubleDash )
     */
    public static function normalizeCliArguments( array $args, array $valued, array $flags )
    {
        $out = array();
        $tail = array();
        $flagAt = array();
        $args = array_values( $args );
        for ( $i = 0, $n = count( $args ); $i < $n; $i++ )
        {
            $arg = (string)$args[$i];
            if ( $arg === '--' )
            {
                $tail = array_slice( $args, $i + 1 );
                break;
            }
            if ( !preg_match( '/^(--?)([A-Za-z][\w-]+)(=.*)?$/s', $arg, $m ) )
            {
                $out[] = $arg;
                continue;
            }
            $name = $m[2];
            $eq = isset( $m[3] ) ? $m[3] : '';
            $isValued = in_array( $name, $valued, true );
            $isFlag = in_array( $name, $flags, true );
            if ( !$isValued && !$isFlag && strncmp( $name, 'no-', 3 ) === 0
                 && in_array( substr( $name, 3 ), $flags, true ) && $eq === '' )
            {
                // --no-json: drop every earlier --json, and add none.
                $off = substr( $name, 3 );
                if ( isset( $flagAt[$off] ) )
                {
                    foreach ( $flagAt[$off] as $at )
                        $out[$at] = null;
                    unset( $flagAt[$off] );
                }
                continue;
            }
            if ( !$isValued && !$isFlag )
            {
                $out[] = $arg;
                continue;
            }
            if ( $isValued && $eq === '' && $i + 1 < $n
                 && ( (string)$args[$i + 1] === '' || ( (string)$args[$i + 1] )[0] !== '-' ) )
            {
                $out[] = '--' . $name . '=' . $args[++$i];
                continue;
            }
            if ( $isFlag && $eq === '' )
                $flagAt[$name][] = count( $out );
            $out[] = '--' . $name . $eq;
        }
        return array( array_values( array_filter( $out, function ( $a ) { return $a !== null; } ) ), $tail );
    }

    protected function result( $ok, $message, $data = null )
    {
        return array( 'ok' => (bool)$ok, 'message' => $message, 'data' => $data );
    }
}
