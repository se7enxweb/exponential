<?php
/**
 * File containing the expVelocityConfigLayout class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * The server's configuration as a Debian Apache-style tree, and the move to it.
 *
 * The owner's standard for this installation: Velocity is configured the way
 * Debian configures Apache, because that is the structure organisations
 * already run and audit:
 *
 *   /etc/vc/vc.conf, ports.conf, envvars
 *   /etc/vc/mods-available/  -> mods-enabled/   engine modules
 *   /etc/vc/conf-available/  -> conf-enabled/   shared snippets
 *   /etc/vc/sites-available/ -> sites-enabled/  one per installation
 *   /var/lib/vc/sites/<site>.json               what is known about a site
 *
 * /etc/qbix is the engine's base tree; the engine loads it first and /etc/vc,
 * Velocity's overlay, on top. See doc/bc/6.0/velocity-ondisk-layout.md.
 *
 * velocity.ini stays the source of what is generated. Generated files carry
 * "_generated": "exp:velocity" and are rewritten on every start; like a dpkg
 * conffile, one whose marker an administrator removed is never touched
 * again. Whether a module, snippet or site is enabled is the administrator's
 * decision: a symlink is created only when its file is first created, and
 * never recreated after someone removed it.
 *
 * Nothing is deleted, and the single configuration file the server used
 * before (var/tmp/velocity-server.json) is still written, so every older
 * path keeps working and the tree can be abandoned by one setting
 * ([LayoutSettings] ConfDir=disabled).
 *
 * @package kernel
 */
class expVelocityConfigLayout
{
    const MARKER_KEY = '_generated';
    const MARKER = 'exp:velocity';

    /**
     * Engine modules: file name => the config paths it owns. A path ending in
     * '*' takes every key of that block starting with the given prefix.
     * Paths naming this installation's files are left for the site file.
     */
    protected static $modules = array(
        'http2'       => array( 'Q.web.http2' ),
        'static'      => array( 'Q.web.static' ),
        'cache'       => array( 'Q.web.cache' ),
        'precompress' => array( 'Q.webserver.precompress' ),
        'keepalive'   => array( 'Q.webserver.keepAlive' ),
        'brand'       => array( 'Q.webserver.brand*', 'Q.webserver.maintainer*' ),
        'log'         => array( 'Q.webserver.log' ),
        'pool'        => array( 'Q.webserver.forkPerRequest', 'Q.webserver.spareWorkers', 'Q.webserver.idleWorkerTimeout' ),
        'dashboard'   => array( 'Q.dashboard' ),
    );

    /** Keys that belong to the site even inside a module's block: this installation's paths and secrets. */
    protected static $siteKeys = array( 'dir', 'token', 'cert', 'key', 'prewarmDir', 'warmup' );

    /** @var expVelocity */
    protected $velocity;

    /** @var array what the last apply() did, for the log */
    public $actions = array();

    public function __construct( expVelocity $velocity )
    {
        $this->velocity = $velocity;
    }

    // ── Where ────────────────────────────────────────────────────────────

    /**
     * The configuration directory, or null when the tree is turned off.
     *
     * [LayoutSettings] ConfDir: auto (default), disabled, or a path. auto is
     * /etc/vc when it exists or can be made, else an existing /etc/qbix, else
     * var/vc/etc inside the installation -- for an account without root.
     *
     * @return string|null
     */
    public function confDir()
    {
        $setting = trim( (string)$this->velocity->layoutSetting( 'ConfDir', 'auto' ) );
        if ( in_array( strtolower( $setting ), array( 'disabled', 'none', 'false', 'off' ), true ) )
            return null;
        if ( $setting !== '' && strtolower( $setting ) !== 'auto' )
            return rtrim( $this->velocity->absolutePath( $setting ), '/' );

        if ( is_dir( '/etc/vc' ) || is_writable( '/etc' ) )
            return '/etc/vc';
        if ( is_dir( '/etc/qbix' ) )
            return '/etc/qbix';
        return $this->velocity->absolutePath( 'var/vc/etc' );
    }

    /**
     * Where extended information about sites is kept: /var/lib/<name>, beside
     * the configuration directory's name, or var/vc/lib for an account
     * without root.
     *
     * @return string
     */
    public function stateDir()
    {
        $setting = trim( (string)$this->velocity->layoutSetting( 'StateDir', 'auto' ) );
        if ( $setting !== '' && strtolower( $setting ) !== 'auto' )
            return rtrim( $this->velocity->absolutePath( $setting ), '/' );
        $confDir = $this->confDir();
        $name = $confDir !== null && strpos( $confDir, '/etc/' ) === 0 ? basename( $confDir ) : null;
        if ( $name !== null && ( is_dir( "/var/lib/$name" ) || is_writable( '/var/lib' ) ) )
            return "/var/lib/$name";
        return $this->velocity->absolutePath( 'var/vc/lib' );
    }

    /**
     * This installation's site name: [LayoutSettings] SiteName, else the
     * host of site.ini's SiteURL, else the installation directory's name.
     *
     * @return string
     */
    public function siteName()
    {
        $name = trim( (string)$this->velocity->layoutSetting( 'SiteName', '' ) );
        if ( $name === '' )
        {
            $ini = eZINI::instance( 'site.ini' );
            $url = $ini->hasVariable( 'SiteSettings', 'SiteURL' ) ? (string)$ini->variable( 'SiteSettings', 'SiteURL' ) : '';
            $host = parse_url( strpos( $url, '://' ) === false ? 'http://' . $url : $url, PHP_URL_HOST );
            $name = $host ? $host : basename( $this->velocity->absolutePath( '' ) );
        }
        $name = strtolower( preg_replace( '/[^A-Za-z0-9.-]+/', '-', $name ) );
        return trim( $name, '.-' ) !== '' ? trim( $name, '.-' ) : 'default';
    }

    public function siteFile( $enabled = true )
    {
        $dir = $this->confDir();
        return $dir === null ? null
            : $dir . '/sites-' . ( $enabled ? 'enabled' : 'available' ) . '/' . $this->siteName() . '.conf';
    }

    public function metadataFile()
    {
        return $this->stateDir() . '/sites/' . $this->siteName() . '.json';
    }

    // ── Splitting the generated configuration ────────────────────────────

    /**
     * The generated configuration split into ports.conf, one file per module
     * and the site file. Merged back in the engine's order they give exactly
     * the configuration plus the ports; verify() checks that before use.
     *
     * @param array $config the configuration exp:velocity generated
     * @param array $ports array( 'http' => int, 'https' => int|null )
     * @return array ports, mods (name => array), site
     */
    public function split( array $config, array $ports )
    {
        $site = $config;
        $mods = array();
        foreach ( self::$modules as $name => $paths )
        {
            $mod = array();
            foreach ( $paths as $path )
                $this->movePath( $site, $mod, explode( '.', $path ) );
            if ( $mod )
                $mods[$name] = $mod;
        }

        $portsConf = array( 'Q' => array( 'webserver' => array( 'port' => (int)$ports['http'] ) ) );
        if ( !empty( $ports['https'] ) )
            $portsConf['Q']['web']['https']['port'] = (int)$ports['https'];

        return array( 'ports' => $portsConf, 'mods' => $mods, 'site' => $this->prune( $site ) );
    }

    /**
     * Move one path from $from to $to, leaving site keys behind.
     */
    protected function movePath( array &$from, array &$to, array $keys )
    {
        $last = array_pop( $keys );
        $src =& $from;
        $dst =& $to;
        foreach ( $keys as $k )
        {
            if ( !isset( $src[$k] ) || !is_array( $src[$k] ) )
                return;
            if ( !isset( $dst[$k] ) )
                $dst[$k] = array();
            $src =& $src[$k];
            $dst =& $dst[$k];
        }
        $names = substr( $last, -1 ) === '*'
            ? array_values( array_filter( array_keys( $src ), function ( $n ) use ( $last ) {
                  return strpos( (string)$n, substr( $last, 0, -1 ) ) === 0; } ) )
            : ( array_key_exists( $last, $src ) ? array( $last ) : array() );
        foreach ( $names as $name )
        {
            $value = $src[$name];
            if ( is_array( $value ) && !array_is_list( $value ) )
            {
                // Keep this installation's paths and secrets in the site file.
                foreach ( self::$siteKeys as $siteKey )
                    unset( $value[$siteKey] );
                foreach ( array_keys( $src[$name] ) as $sub )
                    if ( !in_array( $sub, self::$siteKeys, true ) )
                        unset( $src[$name][$sub] );
                if ( $value )
                    $dst[$name] = $value;
            }
            else
            {
                $dst[$name] = $value;
                unset( $src[$name] );
            }
        }
    }

    /** Drop blocks left empty by the split. */
    protected function prune( array $a )
    {
        foreach ( $a as $k => $v )
        {
            if ( is_array( $v ) && !array_is_list( $v ) )
            {
                $a[$k] = $this->prune( $v );
                if ( !$a[$k] )
                    unset( $a[$k] );
            }
        }
        return $a;
    }

    /** The engine's merge (Q_Config::deepMerge), so verify() sees what it will. */
    public static function merge( array $base, array $overlay )
    {
        foreach ( $overlay as $k => $v )
            $base[$k] = ( is_array( $v ) && isset( $base[$k] ) && is_array( $base[$k] ) ) ? self::merge( $base[$k], $v ) : $v;
        return $base;
    }

    /** Arrays equal regardless of key order in maps, ignoring the generated marker. */
    public static function same( $a, $b )
    {
        if ( is_array( $a ) && is_array( $b ) )
        {
            unset( $a[self::MARKER_KEY], $b[self::MARKER_KEY] );
            if ( count( $a ) !== count( $b ) )
                return false;
            foreach ( $a as $k => $v )
                if ( !array_key_exists( $k, $b ) || !self::same( $v, $b[$k] ) )
                    return false;
            return true;
        }
        return $a === $b;
    }

    // ── Applying ─────────────────────────────────────────────────────────

    /**
     * Bring the tree up to date for this installation and say which files the
     * server should be started with.
     *
     * @param array $config the generated configuration
     * @param array $ports array( 'http' => int, 'https' => int|null )
     * @return array ok, message, confDir, siteFile, actions
     */
    public function apply( array $config, array $ports )
    {
        $this->actions = array();
        $dir = $this->confDir();
        if ( $dir === null )
            return array( 'ok' => false, 'message' => 'layout disabled ([LayoutSettings] ConfDir)', 'actions' => array() );

        foreach ( array( '', '/mods-available', '/mods-enabled', '/conf-available', '/conf-enabled',
                         '/sites-available', '/sites-enabled', '/designs' ) as $sub )
        {
            if ( !is_dir( $dir . $sub ) )
            {
                if ( !@mkdir( $dir . $sub, 0755, true ) )
                    return array( 'ok' => false, 'message' => "could not create $dir$sub", 'actions' => $this->actions );
                $this->actions[] = "created $dir$sub";
            }
        }

        $name = basename( $dir ) === 'qbix' ? 'qbix' : 'vc';
        $parts = $this->split( $config, $ports );

        $retired = $this->retireLegacyEtcFile();
        if ( $retired !== null )
            $this->actions[] = $retired;

        // The base file and envvars are the administrator's; created once, empty.
        $this->writeOnce( "$dir/$name.conf", array( self::MARKER_KEY => 'created by ' . self::MARKER . '; settings shared by every site go here' ) );
        if ( !file_exists( "$dir/envvars" ) )
        {
            file_put_contents( "$dir/envvars", "# Environment for the Velocity server process, read (not executed) by exp:velocity.\n# export NAME=value\n" );
            $this->actions[] = "created $dir/envvars";
        }

        $this->writeGenerated( "$dir/ports.conf", $parts['ports'], 0644 );
        foreach ( $parts['mods'] as $mod => $settings )
            $this->writeGenerated( "$dir/mods-available/$mod.conf", $settings, 0644, "$dir/mods-enabled/$mod.conf" );

        $site = $this->siteName();
        // The site holds certificates' paths and the dashboard token: owner only.
        $this->writeGenerated( "$dir/sites-available/$site.conf", $parts['site'], 0600, "$dir/sites-enabled/$site.conf" );

        $enabledSite = "$dir/sites-enabled/$site.conf";
        if ( !is_file( $enabledSite ) )
            return array( 'ok' => false, 'message' => "site $site is disabled (exp:velocity site enable $site)",
                          'confDir' => $dir, 'actions' => $this->actions );

        // The split must lose nothing: its pieces, merged the way the engine
        // merges, are exactly what was generated plus the ports. That guards
        // against a mistake here, and if it fails the single file is used.
        $pieces = self::merge( array(), $parts['ports'] );
        foreach ( $parts['mods'] as $settings )
            $pieces = self::merge( $pieces, $settings );
        $pieces = self::merge( $pieces, $parts['site'] );
        if ( !self::same( $pieces, self::merge( $config, $parts['ports'] ) ) )
            return array( 'ok' => false, 'message' => 'the split of the generated configuration is not lossless (a bug in expVelocityConfigLayout); using the single file',
                          'confDir' => $dir, 'actions' => $this->actions );

        // What the engine will load can still differ, on purpose: a module or
        // snippet an administrator disabled or added, a file they took over.
        // Those are theirs to decide, as in /etc/apache2 -- honoured, and
        // named, so the difference is never silent.
        $merged = array();
        foreach ( $this->engineFiles( $dir ) as $file )
            $merged = self::merge( $merged, (array)json_decode( (string)file_get_contents( $file ), true ) );
        $merged = self::merge( $merged, (array)json_decode( (string)file_get_contents( $enabledSite ), true ) );
        $notes = array();
        if ( !self::same( $merged, self::merge( $config, $parts['ports'] ) ) )
        {
            foreach ( array_keys( $parts['mods'] ) as $mod )
                if ( !is_file( "$dir/mods-enabled/$mod.conf" ) )
                    $notes[] = "mod $mod disabled";
            foreach ( glob( "$dir/conf-enabled/*.conf" ) ?: array() as $f )
                if ( is_file( $f ) )
                    $notes[] = 'conf ' . basename( $f, '.conf' ) . ' enabled';
            foreach ( $this->actions as $a )
                if ( strpos( $a, 'kept ' ) === 0 )
                    $notes[] = substr( $a, 5 );
            if ( !$notes )
                $notes[] = 'administrator changes in the tree';
        }

        $this->writeMetadata( $dir );
        return array( 'ok' => true,
                      'message' => 'configuration from ' . $dir . ( $notes ? ' (differs from velocity.ini: ' . implode( ', ', $notes ) . ')' : '' ),
                      'confDir' => $dir, 'siteFile' => $enabledSite, 'actions' => $this->actions );
    }

    /** The files the engine loads before the site, in its order (see Q_WebServer_Layout::files). */
    public function engineFiles( $dir )
    {
        $files = array();
        foreach ( array_unique( array( basename( $dir ) . '.conf', 'vc.conf', 'qbix.conf' ) ) as $n )
            if ( is_file( "$dir/$n" ) ) { $files[] = "$dir/$n"; break; }
        if ( is_file( "$dir/ports.conf" ) )
            $files[] = "$dir/ports.conf";
        foreach ( array( 'mods', 'conf' ) as $pair )
        {
            $list = array_merge( glob( "$dir/$pair-enabled/*.conf" ) ?: array(), glob( "$dir/$pair-enabled/*.json" ) ?: array() );
            sort( $list, SORT_STRING );
            foreach ( $list as $f )
                if ( is_file( $f ) )
                    $files[] = $f;
        }
        return $files;
    }

    protected function writeOnce( $file, array $content )
    {
        if ( file_exists( $file ) )
            return;
        file_put_contents( $file, json_encode( $content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
        $this->actions[] = "created $file";
    }

    /**
     * Write a generated file unless an administrator has taken it over, and
     * enable it with $link -- but only when the file is new.
     */
    protected function writeGenerated( $file, array $content, $mode, $link = null )
    {
        $new = !file_exists( $file );
        if ( !$new )
        {
            $current = json_decode( (string)@file_get_contents( $file ), true );
            if ( !is_array( $current ) || ( $current[self::MARKER_KEY] ?? '' ) !== self::MARKER )
            {
                $this->actions[] = "kept $file (edited by an administrator)";
                return;
            }
        }
        $content = array( self::MARKER_KEY => self::MARKER ) + $content;
        $json = json_encode( $content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n";
        if ( $new || file_get_contents( $file ) !== $json )
        {
            $tmp = $file . '.' . getmypid() . '.tmp';
            file_put_contents( $tmp, $json );
            @chmod( $tmp, $mode );
            rename( $tmp, $file );
            $this->actions[] = ( $new ? 'created ' : 'updated ' ) . $file;
        }
        if ( $new && $link !== null && !file_exists( $link ) && !is_link( $link ) )
        {
            symlink( '../' . basename( dirname( $file ) ) . '/' . basename( $file ), $link );
            $this->actions[] = "enabled $link";
        }
    }

    protected function writeMetadata( $dir )
    {
        $file = $this->metadataFile();
        if ( !is_dir( dirname( $file ) ) )
            @mkdir( dirname( $file ), 0755, true );
        $previous = is_file( $file ) ? (array)json_decode( (string)file_get_contents( $file ), true ) : array();
        $meta = array(
            'site'        => $this->siteName(),
            'root'        => $this->velocity->absolutePath( '' ),
            'confDir'     => $dir,
            'siteFile'    => $dir . '/sites-available/' . $this->siteName() . '.conf',
            'migratedAt'  => $previous['migratedAt'] ?? date( DATE_ATOM ),
            'lastApplied' => date( DATE_ATOM ),
            'previousConfig' => $this->velocity->absolutePath( 'var/tmp/velocity-server.json' ),
            'assets'      => $this->velocity->assets(),
        );
        if ( @file_put_contents( $file, json_encode( $meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" ) !== false && !$previous )
            $this->actions[] = "recorded $file";
    }

    /**
     * The environment envvars sets, as Apache's envvars: "export NAME=value"
     * or "NAME=value" lines, # comments, optional quotes. Read, never
     * executed. Same rules as the engine's Q_WebServer_Layout::envvars().
     *
     * @param string|null $dir
     * @return array name => value
     */
    public function envvars( $dir )
    {
        $vars = array();
        if ( $dir === null || !is_file( "$dir/envvars" ) )
            return $vars;
        foreach ( file( "$dir/envvars", FILE_IGNORE_NEW_LINES ) ?: array() as $line )
        {
            $line = trim( $line );
            if ( $line === '' || $line[0] === '#' )
                continue;
            if ( !preg_match( '/^(?:export\s+)?([A-Za-z_][A-Za-z0-9_]*)=(.*)$/', $line, $m ) )
                continue;
            $value = trim( $m[2] );
            if ( strlen( $value ) >= 2 && ( $value[0] === '"' || $value[0] === "'" ) && substr( $value, -1 ) === $value[0] )
                $value = substr( $value, 1, -1 );
            $vars[$m[1]] = $value;
        }
        return $vars;
    }

    /**
     * What `exp:velocity layout` shows: the directory, every pair's available
     * and enabled files, and every asset path.
     *
     * @return array
     */
    public function describe()
    {
        $dir = $this->confDir();
        $pairs = array();
        if ( $dir !== null )
        {
            foreach ( array( 'sites', 'conf', 'mods' ) as $p )
            {
                $available = array_map( 'basename', glob( "$dir/$p-available/*.conf" ) ?: array() );
                $enabled = array_map( 'basename', array_filter( glob( "$dir/$p-enabled/*.conf" ) ?: array(), 'is_file' ) );
                $pairs[$p] = array( 'available' => $available, 'enabled' => array_values( $enabled ) );
            }
        }
        return array(
            'confDir'  => $dir,
            'stateDir' => $this->stateDir(),
            'site'     => $this->siteName(),
            'pairs'    => $pairs,
            'envvars'  => array_keys( $this->envvars( $dir ) ),
            'assets'   => $this->velocity->assets(),
        );
    }

    // ── The old single file under /etc ───────────────────────────────────

    /** Where the server's configuration used to be kept by hand. */
    const LEGACY_ETC_FILE = '/etc/qbix/qbix.json';

    /**
     * Retire /etc/qbix/qbix.json once everything in it is provided elsewhere.
     *
     * Before exp:velocity, the server was started with that file: TLS paths
     * and the list of globals to keep. Nothing reads it now -- HTTPSSettings
     * give the certificate, and the globals are exp:velocity's built-in
     * defaults plus KeepGlobals[] -- but a file under /etc that looks like
     * configuration and is not is how someone edits the wrong thing. So it is
     * compared, value by value, with what the server actually gets; only if
     * every value is covered is it renamed to qbix.json.migrated-<date>,
     * beside its .bak. Never deleted. Anything not covered leaves it in place
     * and says what.
     *
     * @return string|null what happened, or null when there is nothing to do
     */
    public function retireLegacyEtcFile()
    {
        $file = self::LEGACY_ETC_FILE;
        if ( !is_file( $file ) )
            return null;
        $data = json_decode( (string)@file_get_contents( $file ), true );
        if ( !is_array( $data ) )
            return "left $file: not readable as JSON";

        $uncovered = array();
        $q = $data['Q'] ?? array();
        foreach ( $data as $top => $unused )
            if ( $top !== 'Q' )
                $uncovered[] = $top;

        // Globals: every one must be kept anyway.
        $wanted = $q['webserver']['keepGlobals'] ?? array();
        $wanted = is_array( $wanted ) ? $wanted : explode( ',', (string)$wanted );
        $missing = array_diff( array_filter( array_map( 'trim', $wanted ) ), $this->velocity->keepGlobals() );
        if ( $missing )
            $uncovered[] = 'keepGlobals ' . implode( ',', $missing );
        unset( $q['webserver']['keepGlobals'] );

        // TLS: the same certificate and key the server is given.
        if ( isset( $q['web']['https'] ) )
        {
            $https = $q['web']['https'];
            $assets = $this->velocity->assets();
            if ( ( $https['cert'] ?? null ) !== $assets['certificate'] || ( $https['key'] ?? null ) !== $assets['certificateKey'] )
                $uncovered[] = 'web.https (a different certificate)';
            unset( $q['web']['https'] );
        }

        foreach ( array( 'web', 'webserver' ) as $block )
            if ( isset( $q[$block] ) && !$q[$block] )
                unset( $q[$block] );
        foreach ( array_keys( $q ) as $rest )
            $uncovered[] = 'Q.' . $rest;

        if ( $uncovered )
            return "left $file: not everything in it is provided elsewhere (" . implode( '; ', $uncovered ) . ')';

        $target = $file . '.migrated-' . date( 'Ymd' );
        if ( file_exists( $target ) )
            $target .= '-' . date( 'His' );
        if ( !@rename( $file, $target ) )
            return "left $file: could not rename it";
        return "retired $file to $target (its TLS paths come from HTTPSSettings, its globals are exp:velocity's defaults)";
    }

    // ── a2ensite and friends ─────────────────────────────────────────────

    /**
     * Enable or disable a site, conf or mod, as a2ensite/a2dissite do.
     *
     * @param string $pair site, conf or mod
     * @param string $name file name without .conf
     * @param bool $enable
     * @return array result
     */
    public function toggle( $pair, $name, $enable )
    {
        $map = array( 'site' => 'sites', 'conf' => 'conf', 'mod' => 'mods' );
        $dir = $this->confDir();
        // The engine does this itself now (Q_WebServer_Ctl::toggle(), also
        // `qbixctl ensite` and friends); asked first, so both stay one behaviour.
        if ( $dir !== null && $this->velocity->engineCtl() )
        {
            list( $ok, $message ) = Q_WebServer_Ctl::toggle( $dir, $pair, $name, $enable );
            return array( 'ok' => $ok, 'message' => str_replace( 'reload to apply', 'restart to apply', $message ) );
        }
        if ( $dir === null || !isset( $map[$pair] ) )
            return array( 'ok' => false, 'message' => $dir === null ? 'layout disabled' : "unknown kind $pair" );
        if ( !preg_match( '/^[A-Za-z0-9._-]+$/', $name ) )
            return array( 'ok' => false, 'message' => "invalid name $name" );
        $p = $map[$pair];
        $available = "$dir/$p-available/$name.conf";
        $link = "$dir/$p-enabled/$name.conf";
        if ( $enable )
        {
            if ( !is_file( $available ) )
                return array( 'ok' => false, 'message' => "no $p-available/$name.conf" );
            if ( is_link( $link ) || file_exists( $link ) )
                return array( 'ok' => true, 'message' => "$pair $name already enabled" );
            if ( !symlink( "../$p-available/$name.conf", $link ) )
                return array( 'ok' => false, 'message' => "could not create $link" );
            return array( 'ok' => true, 'message' => "enabled $pair $name; restart to apply" );
        }
        if ( !is_link( $link ) )
            return array( 'ok' => true, 'message' => is_file( $link ) ? "$link is a file, not a link; left alone" : "$pair $name already disabled" );
        unlink( $link );
        return array( 'ok' => true, 'message' => "disabled $pair $name; restart to apply" );
    }
}
