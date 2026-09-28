<?php
/**
 * File containing the ezpHttpCacheListener class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * The kernel side of the role-aware HTTP cache: stores rendered content views
 * for the early exit and Velocity's parent to serve, and turns the kernel's
 * cache-clear events into tag purges.
 *
 * Attached by ezpEvent::registerEventListeners() only while httpcache.ini
 * [HttpCacheSettings] Enabled=enabled (registerListeners()), and for scripts
 * by eZScript (registerPurgeListeners()):
 *   request/input        requestInput()   a layout-editor write purges every page
 *   response/preoutput   preOutput()      attaches store() last
 *   content/cache        purgeNodes()     Smart View Cache Clear
 *   content/cache/all    purgeAll()       everything, roles, classes
 *   user/cache/all       purgeAll()
 * Called from kernel/content/view.php (noteContentView()), eZUser (noteLogin(),
 * forgetUser(), purgeAll()), eZCache (Setup > Caches) and the layout blocks
 * (addTags()).
 */
class ezpHttpCacheListener
{
    /** @var array|null the content view this request rendered */
    private static $view = null;

    /** @var ezpHttpCacheContract|false|null */
    private static $contract = null;

    /** @var bool this process keeps sessions elsewhere than the contract says */
    private static $sessionStorageDiffers = false;

    /** @var array tags added while the page rendered (layout blocks: addTags()) */
    private static $extraTags = array();

    /** @var int|null the response/output listener id of store() in this request */
    private static $storeListenerId = null;

    /** @var bool the listeners are attached for this web request (the cache is on) */
    private static $active = false;

    // ── Configuration ────────────────────────────────────────────────────

    /** The contract, writing its configuration file when needed; null when off. */
    public static function contract()
    {
        if ( self::$contract !== null )
            return self::$contract ?: null;
        self::$contract = false;
        $ini = eZINI::instance( 'httpcache.ini' );
        $dir = eZSys::rootDir() . '/' . eZSys::cacheDirectory() . '/exphttpcache';
        if ( $ini->variable( 'HttpCacheSettings', 'Enabled' ) !== 'enabled' )
        {
            // Hits never reach the kernel, so switching off has to reach the
            // early exit: it reads this file, not httpcache.ini.
            $current = ezpHttpCacheContract::fromDir( $dir );
            if ( $current )
            {
                $current->atomicWrite( $dir . '/contract.php', "<?php\n// Switched off in httpcache.ini.\nreturn array( 'enabled' => false );\n" );
                if ( function_exists( 'opcache_invalidate' ) )
                    @opcache_invalidate( $dir . '/contract.php', true );
            }
            return null;
        }
        $siteaccess = $GLOBALS['eZCurrentAccess']['name'] ?? '';
        if ( !eZExecution::isWebRequest()
            || !in_array( $siteaccess, (array)$ini->variable( 'HttpCacheSettings', 'CachedSiteAccesses' ), true ) )
        {
            // Only the web server of a cached siteaccess knows its session
            // storage, cookie and paths. Scripts and other siteaccesses (the
            // admin host may be another PHP pool with other session settings)
            // use the contract it wrote and never rewrite it.
            // No contract yet means nothing was stored, so nothing to purge.
            self::$contract = ezpHttpCacheContract::fromDir( $dir ) ?: false;
            return self::$contract ?: null;
        }
        $config = self::buildConfig( $ini, $dir );
        if ( $config === null )
            return null;
        $file = $dir . '/contract.php';
        // Every server of a cached siteaccess (Apache, Velocity, ...) must keep
        // sessions where the contract says. One that does not leaves the
        // contract alone and stores nothing for signed-in visitors: the early
        // exit could not find their sessions and would take them for anonymous.
        $existing = ezpHttpCacheContract::fromDir( $dir );
        if ( $existing && $existing->config['sessionSavePath'] !== '' && $existing->config['sessionSavePath'] !== $config['sessionSavePath'] )
        {
            self::$sessionStorageDiffers = true;
            return self::$contract = $existing;
        }
        $php = "<?php\n// Written by ezpHttpCacheListener from httpcache.ini; do not edit.\nreturn " . var_export( $config, true ) . ";\n";
        if ( @file_get_contents( $file ) !== $php )
        {
            // Switched back on: while it was off nothing listened for changes,
            // so nothing stored before can be trusted.
            $wasOff = is_file( $file ) && !$existing;
            $c = new ezpHttpCacheContract( $config );
            $c->ensureDir( $dir );
            $c->atomicWrite( $file, $php );
            @chmod( $file, 0640 );
            // The early exit includes it; OPcache would keep the old one for
            // up to opcache.revalidate_freq seconds.
            if ( function_exists( 'opcache_invalidate' ) )
                @opcache_invalidate( $file, true );
            if ( $wasOff )
                $c->bumpGeneration();
        }
        return self::$contract = new ezpHttpCacheContract( $config );
    }

    private static function buildConfig( eZINI $ini, $dir )
    {
        // The HMAC key: generated once, kept beside the entries, never sent.
        $secretFile = $dir . '/secret';
        $secret = @file_get_contents( $secretFile );
        if ( !is_string( $secret ) || strlen( $secret ) < 64 )
        {
            if ( !is_dir( $dir ) )
                @mkdir( $dir, 0770, true );
            $secret = bin2hex( random_bytes( 32 ) );
            if ( @file_put_contents( $secretFile, $secret ) === false )
                return null;
            @chmod( $secretFile, 0600 );
        }
        $site = eZINI::instance();
        // eZSiteAccess::match() decides before any siteaccess is loaded, on
        // the settings without siteaccess overrides; this one holds those
        // (as eZSiteAccess::getIni() starts), not the current siteaccess's.
        $global = new eZINI( 'site.ini', 'settings', null, null, true );
        $hosts = array();
        $cached = array_values( array_filter( (array)$ini->variable( 'HttpCacheSettings', 'CachedSiteAccesses' ), 'strlen' ) );
        foreach ( (array)$site->variable( 'SiteAccessSettings', 'HostMatchMapItems' ) as $item )
        {
            $parts = explode( ';', $item );
            if ( count( $parts ) === 2 && in_array( $parts[1], $cached, true ) )
                $hosts[strtolower( $parts[0] )] = $parts[1];
        }
        $cookies = array();
        foreach ( $cached as $sa )
        {
            // The same rules as eZSession::registerFunctions(), on the merged
            // settings of that siteaccess (getSiteAccessIni() reads one file only).
            $saIni = eZSiteAccess::getIni( $sa, 'site.ini' );
            if ( $saIni->variable( 'Session', 'SessionNameHandler' ) === 'custom' )
            {
                $name = (string)$saIni->variable( 'Session', 'SessionNamePrefix' );
                if ( $saIni->variable( 'Session', 'SessionNamePerSiteAccess' ) === 'enabled' )
                    $name .= md5( $sa );
            }
            else
            {
                $name = (string)ini_get( 'session.name' );
            }
            $cookies[$sa] = $name;
        }
        $handler = ini_get( 'session.save_handler' );
        $path = ini_get( 'session.save_path' );
        if ( strpos( (string)$path, ';' ) !== false )
            $path = substr( $path, strrpos( $path, ';' ) + 1 );
        return array(
            'enabled' => true,
            'secret' => $secret,
            'dir' => $dir,
            'hosts' => $hosts,
            'siteaccesses' => $cached,
            'match' => self::matchRules( $global ),
            'sslPort' => (string)$global->variable( 'SiteSettings', 'SSLPort' ),
            'sslProxyServerName' => $global->hasVariable( 'SiteSettings', 'SSLProxyServerName' )
                ? (string)$global->variable( 'SiteSettings', 'SSLProxyServerName' ) : '',
            'sessionCookie' => $cookies,
            // Only a files handler can be read before the kernel boots.
            'sessionSavePath' => ( $handler === 'files' && $path !== '' ) ? rtrim( $path, '/' ) : '',
            'formTokenSecret' => (string)$site->variable( 'HTMLForms', 'Secret' ),
            'formTokenIntention' => 'legacy',
            'maxAge' => (int)$ini->variable( 'HttpCacheSettings', 'MaxAge' ),
            'swr' => (int)$ini->variable( 'HttpCacheSettings', 'StaleWhileRevalidate' ),
            'proxyHeaders' => $ini->variable( 'HttpCacheSettings', 'ProxyHeaders' ) === 'enabled',
            'apcu' => $ini->variable( 'HttpCacheSettings', 'APCu' ) === 'enabled',
            'maxBodySize' => (int)$ini->variable( 'HttpCacheSettings', 'MaxBodySize' ),
            'queryParameters' => array_values( array_filter( (array)$ini->variable( 'HttpCacheSettings', 'QueryStringParameters' ), 'strlen' ) ),
        );
    }

    // ── Store ────────────────────────────────────────────────────────────

    /** Called from kernel/content/view.php with the view's $Result. */
    public static function noteContentView( $result, $viewMode )
    {
        if ( is_array( $result ) && isset( $result['node_id'] ) )
            self::$view = array( 'result' => $result, 'viewMode' => (string)$viewMode );
        return $result;
    }

    /**
     * response/preoutput: attach store() to response/output now, so it runs
     * after every listener registered at boot (ezformtoken above all), on the
     * page exactly as it is sent.
     */
    public static function preOutput( $templateResult )
    {
        if ( self::$view !== null && self::contract() )
            self::$storeListenerId = ezpEvent::getInstance()->attach( 'response/output', array( __CLASS__, 'store' ) );
        return $templateResult;
    }

    /** response/output: store the page if it may be stored. */
    public static function store( $html )
    {
        try
        {
            self::storeOrSay( $html );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
            self::header( 'X-Exp-Cache', 'BYPASS (error)' );
        }
        return $html;
    }

    /**
     * The site.ini values eZSiteAccess::match() decides with, for
     * ezpHttpCacheContract::resolveSiteAccess(): the early exit and the web
     * server's process find the siteaccess from these before the kernel runs.
     * $site is to hold the settings without siteaccess overrides, as match()
     * reads them.
     *
     * @return array
     */
    private static function matchRules( eZINI $site )
    {
        $var = function ( $group, $name, $default = '' ) use ( $site ) {
            return $site->hasVariable( $group, $name ) ? $site->variable( $group, $name ) : $default;
        };
        $items = function ( $name ) use ( $site ) {
            if ( !$site->hasVariable( 'SiteAccessSettings', $name ) )
                return array();
            return array_values( array_filter( (array)$site->variableArray( 'SiteAccessSettings', $name ),
                                               function ( $item ) { return is_array( $item ) && count( $item ) >= 2; } ) );
        };
        $order = (string)$var( 'SiteAccessSettings', 'MatchOrder', 'none' );
        return array(
            'static' => (string)$var( 'SiteAccessSettings', 'StaticMatch' ),
            'default' => (string)$var( 'SiteSettings', 'DefaultAccess' ),
            'order' => $order === 'none' ? array( 'none' ) : (array)$site->variableArray( 'SiteAccessSettings', 'MatchOrder' ),
            'list' => array_values( (array)$var( 'SiteAccessSettings', 'AvailableSiteAccessList', array() ) ),
            'uriType' => (string)$var( 'SiteAccessSettings', 'URIMatchType' ),
            'uriElement' => (int)$var( 'SiteAccessSettings', 'URIMatchElement', 1 ),
            'uriMap' => $items( 'URIMatchMapItems' ),
            'hostType' => (string)$var( 'SiteAccessSettings', 'HostMatchType' ),
            'hostMap' => $items( 'HostMatchMapItems' ),
            'hostUri' => $items( 'HostUriMatchMapItems' ),
            'hostUriMethod' => (string)$var( 'SiteAccessSettings', 'HostUriMatchMethodDefault', 'strict' ),
        );
    }

    private static function storeOrSay( $html )
    {
        $contract = self::contract();
        $reason = self::uncacheableReason( $html );
        if ( $reason !== null )
        {
            self::header( 'X-Exp-Cache', 'BYPASS (' . $reason . ')' );
            return;
        }
        $siteaccess = $GLOBALS['eZCurrentAccess']['name'] ?? '';
        if ( !$contract->cachesSiteAccess( $siteaccess ) )
        {
            self::header( 'X-Exp-Cache', 'BYPASS (siteaccess not cached)' );
            return;
        }
        // The early exit and the web server find a page by the scheme, host
        // and siteaccess they work out before the kernel starts. Stored only
        // when those are the ones the kernel used for this page, so a lookup
        // can never hand out a page rendered for another host, scheme or
        // siteaccess -- at worst a page is not served early.
        list( $scheme, $originHost ) = $contract->requestOrigin( $_SERVER );
        if ( $originHost !== (string)eZSys::hostname() || $scheme !== ( eZSys::isSSLNow() ? 'https' : 'http' ) )
        {
            self::header( 'X-Exp-Cache', 'BYPASS (scheme or host not known before the kernel)' );
            return;
        }
        $host = strtolower( preg_replace( '/:\d+$/', '', $originHost ) );
        if ( $contract->resolveSiteAccess( $originHost, eZSys::serverVariable( 'REQUEST_URI' ) ) !== $siteaccess )
        {
            self::header( 'X-Exp-Cache', 'BYPASS (siteaccess not known before the kernel)' );
            return;
        }
        if ( !$contract->queryAllowed( eZSys::serverVariable( 'REQUEST_URI' ) ) )
        {
            self::header( 'X-Exp-Cache', 'BYPASS (query string)' );
            return;
        }
        // A template that keeps its page out of the view cache ({set-block
        // scope=global variable=cache_ttl}0{/set-block}) keeps it out of this one.
        $result = self::$view['result'];
        if ( !empty( $result['no_cache'] ) || ( isset( $result['cache_ttl'] ) && (int)$result['cache_ttl'] === 0 ) )
        {
            self::header( 'X-Exp-Cache', 'BYPASS (cache_ttl=0)' );
            return;
        }
        // The early exit finds the visitor by this cookie; if it names another,
        // signed-in visitors would be taken for anonymous ones.
        if ( session_name() !== ( $contract->config['sessionCookie'][$siteaccess] ?? null ) )
        {
            self::header( 'X-Exp-Cache', 'BYPASS (session cookie name differs from contract)' );
            return;
        }

        $user = eZUser::currentUser();
        if ( self::$sessionStorageDiffers && session_id() !== '' )
        {
            self::header( 'X-Exp-Cache', 'BYPASS (session storage differs from contract)' );
            return;
        }
        $placeholders = array();
        $sessionID = session_id();
        $context = self::userContext( $contract, $user, $siteaccess );
        if ( $sessionID !== '' && class_exists( 'ezxFormToken' ) && ezxFormToken::isEnabled() )
            $placeholders['form_token'] = ezxFormToken::getToken();

        list( $body, $offsets ) = ezpHttpCacheContract::extractPlaceholders( $html, $placeholders );
        $tags = array_values( array_unique( array_merge( self::tags( self::$view['result'] ), self::$extraTags ) ) );
        $key = $contract->entryKey( $scheme, $host, $siteaccess, eZSys::serverVariable( 'REQUEST_URI' ), $context );
        $ttl = (int)( self::$view['result']['cache_ttl'] ?? -1 );
        // The security headers go with the page: an answer from this cache is
        // assembled without the kernel (Velocity asks the contract directly),
        // so a page kept with Content-Type alone was served without them. The
        // key carries the scheme, so an HTTPS-only header never reaches HTTP.
        $headers = array( 'Content-Type' => 'text/html; charset=utf-8' );
        if ( method_exists( 'ezpKernelWeb', 'securityHeaders' ) )
            $headers += ezpKernelWeb::securityHeaders();
        $contract->storeEntry( $key, 200, $headers, $body, $tags, $offsets, $ttl > 0 ? $ttl : null );
        if ( $user->isRegistered() )
            $contract->storeRecord( $user->id(), $context );

        $lookup = $GLOBALS['EXP_HTTPCACHE_LOOKUP'] ?? '';
        self::header( 'X-Exp-Cache', 'MISS' . ( $lookup !== '' ? ' (' . $lookup . ')' : '' ) );
        if ( !empty( $contract->config['proxyHeaders'] ) )
        {
            self::header( 'xkey', implode( ' ', $tags ) );
            self::header( 'Surrogate-Key', implode( ' ', $tags ) );
        }
    }

    /** Why this response may not be stored, or null when it may. */
    private static function uncacheableReason( $html )
    {
        if ( ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) !== 'GET' )
            return 'method';
        if ( !empty( $_POST ) )
            return 'post';
        $code = http_response_code();
        if ( $code !== false && $code !== 200 )
            return 'status ' . $code;
        if ( !is_string( $html ) || $html === '' )
            return 'empty';
        foreach ( headers_list() as $h )
        {
            // A cookie set for this response belongs to this visitor alone,
            // except the session and login-state cookies every page carries.
            if ( stripos( $h, 'Set-Cookie:' ) === 0
                && !preg_match( '/^Set-Cookie:\s*(eZSESSID[0-9a-f]*|is_logged_in)=/i', $h ) )
                return 'sets a cookie';
        }
        return null;
    }

    /**
     * The permission context of a user: shared by everyone with the same
     * roles and assignment limitations, private to the user when a policy
     * depends on who they are, the anonymous one for the anonymous user.
     */
    private static function userContext( ezpHttpCacheContract $contract, eZUser $user, $siteaccess )
    {
        if ( !$user->isRegistered() )
            return $contract->anonymousContext( $siteaccess );
        if ( self::hasPerUserLimitation( $user ) )
            return $contract->privateContext( $user->id(), $siteaccess );
        return $contract->roleContext( $user->roleIDList(), $user->limitValueList(), $siteaccess );
    }

    /**
     * Called when a user signs in (eZUser::setCurrentlyLoggedInUser()): writes
     * the record that tells the early exit and Velocity which pages are theirs,
     * so the first cached page after signing in is served, not rendered.
     */
    public static function noteLogin( $user )
    {
        try
        {
            if ( !( $user instanceof eZUser ) || !$user->isRegistered() )
                return;
            $contract = self::contract();
            $siteaccess = $GLOBALS['eZCurrentAccess']['name'] ?? '';
            if ( !$contract || self::$sessionStorageDiffers || !$contract->cachesSiteAccess( $siteaccess ) )
                return;
            $contract->storeRecord( $user->id(), self::userContext( $contract, $user, $siteaccess ) );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
        }
    }

    private static function hasPerUserLimitation( eZUser $user )
    {
        $access = $user->hasAccessTo( 'content', 'read' );
        if ( ( $access['accessWord'] ?? '' ) !== 'limited' )
            return false;
        foreach ( (array)( $access['policies'] ?? array() ) as $limitations )
        {
            foreach ( array_keys( (array)$limitations ) as $identifier )
            {
                if ( in_array( $identifier, ezpHttpCacheContract::PER_USER_LIMITATIONS, true ) )
                    return true;
            }
        }
        return false;
    }

    /** Ibexa-style tags for a content view result. */
    public static function tags( array $result )
    {
        $info = $result['content_info'] ?? array();
        $tags = array( 'ez-all' );
        if ( isset( $info['object_id'] ) ) $tags[] = 'c' . (int)$info['object_id'];
        if ( isset( $info['class_id'] ) ) $tags[] = 'ct' . (int)$info['class_id'];
        if ( isset( $result['node_id'] ) ) $tags[] = 'l' . (int)$result['node_id'];
        if ( isset( $info['main_node_id'] ) ) $tags[] = 'l' . (int)$info['main_node_id'];
        if ( isset( $info['parent_node_id'] ) ) $tags[] = 'pl' . (int)$info['parent_node_id'];
        if ( isset( $result['section_id'] ) ) $tags[] = 's' . (int)$result['section_id'];
        foreach ( (array)( $result['path'] ?? array() ) as $step )
        {
            if ( !empty( $step['node_id'] ) )
                $tags[] = 'p' . (int)$step['node_id'];
        }
        return array_values( array_unique( $tags ) );
    }

    private static function header( $name, $value )
    {
        if ( !headers_sent() )
            header( $name . ': ' . $value );
    }

    // ── Purge ────────────────────────────────────────────────────────────

    /**
     * content/cache: the node list Smart View Cache Clear computed (and the
     * objects). Purges the location and parent-location tags of each node and
     * the content tag of each object, so exactly the pages the view cache
     * clears are refetched.
     */
    /**
     * Attaches every listener of a web request; called by
     * ezpEvent::registerEventListeners() only while the cache is enabled.
     */
    public static function registerListeners( ezpEvent $events )
    {
        // A new request. Under a persistent worker (Velocity) the statics
        // outlive the last one: a kept contract would key pages with the state
        // it read then (an old generation, which nothing looks up), and a kept
        // view could have this request store the previous page.
        self::$view = null;
        self::$contract = null;
        self::$sessionStorageDiffers = false;
        self::$extraTags = array();
        self::$active = true;
        // store() is attached per request (preOutput()); a persistent worker
        // would otherwise keep every earlier one and store each page again.
        if ( self::$storeListenerId !== null )
            $events->detach( 'response/output', self::$storeListenerId );
        self::$storeListenerId = null;
        $events->attach( 'request/input', array( __CLASS__, 'requestInput' ) );
        $events->attach( 'response/preoutput', array( __CLASS__, 'preOutput' ) );
        self::registerPurgeListeners( $events );
    }

    /**
     * Attaches the purge listeners at runtime, for scripts (eZScript), where the
     * site.ini [Event] listeners are not registered.
     */
    public static function registerPurgeListeners( ?ezpEvent $events = null )
    {
        if ( eZINI::instance( 'httpcache.ini' )->variable( 'HttpCacheSettings', 'Enabled' ) !== 'enabled' )
            return;
        $events = $events ?: ezpEvent::getInstance();
        $events->attach( 'content/cache', 'ezpHttpCacheListener::purgeNodes' );
        $events->attach( 'content/cache/all', 'ezpHttpCacheListener::purgeAll' );
        $events->attach( 'user/cache/all', 'ezpHttpCacheListener::purgeAll' );
    }

    /**
     * request/input: a change made in the layout editor (any module named
     * explayouts*, its JSON API included) changes pages without a content
     * event, so a successful write request there purges every page.
     */
    public static function requestInput( $uri )
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ( $method === 'GET' || $method === 'HEAD' || !( $uri instanceof eZURI ) )
            return;
        if ( strncmp( (string)$uri->element( 0 ), 'explayouts', 10 ) !== 0 )
            return;
        register_shutdown_function( function () {
            $code = http_response_code();
            if ( $code === false || $code < 400 )
                self::purgeTags( array( 'ez-all' ) );
        } );
    }

    /**
     * Whole pages include what the view cache never held (the pagelayout's
     * menus, layout blocks with queries), which the tags of a change cannot
     * name yet. So by default any content change purges every page
     * (httpcache.ini ContentChangePurges=all); "tags" purges only pages
     * tagged with the changed objects, nodes and parents.
     */
    public static function purgeNodes( $nodeList = array(), $objectList = array() )
    {
        $contract = self::contract();
        if ( !$contract )
            return;
        if ( eZINI::instance( 'httpcache.ini' )->variable( 'HttpCacheSettings', 'ContentChangePurges' ) !== 'tags' )
        {
            $contract->purgeTags( array( 'ez-all' ) );
            return;
        }
        $tags = array();
        foreach ( (array)$nodeList as $n )
        {
            $tags[] = 'l' . (int)$n;
            $tags[] = 'pl' . (int)$n;
        }
        foreach ( (array)$objectList as $o )
            $tags[] = 'c' . (int)$o;
        // Blocks that run queries may show anything that changed.
        $tags[] = 'dq';
        $contract->purgeTags( array_values( array_unique( $tags ) ) );
    }

    /**
     * A user's cached permissions were dropped (roles or group membership
     * changed): drop their context record too, or their next hit would be
     * served from the context they no longer have.
     */
    public static function forgetUser( $userID )
    {
        $contract = self::contract();
        if ( $contract )
            $contract->deleteRecord( $userID );
    }

    /** content/cache/all, user/cache/all: everything, and every context. */
    /**
     * Tags for what the page being rendered shows beyond its own content --
     * layout blocks listing other objects call this -- so a change to those
     * purges the page too (httpcache.ini ContentChangePurges=tags).
     */
    public static function addTags( array $tags )
    {
        if ( !self::$active )
            return;
        foreach ( $tags as $tag )
        {
            if ( is_string( $tag ) && preg_match( '/^[a-z-]+\d*$/', $tag ) )
                self::$extraTags[] = $tag;
        }
    }

    /**
     * A template listed nodes (fetch content list, tree, list_count,
     * tree_count -- the pagelayout's menus among them). A one-level list of a
     * parent's children is purged by pl<parent>, which every change to one of
     * them purges; anything deeper can change with any content ("dq").
     */
    public static function noteListing( $parentNodeID, $depth, $depthOperator )
    {
        if ( !self::$active )
            return;
        $oneLevel = ( $depth === false || $depth === null || (int)$depth === 1 )
                 && in_array( $depthOperator, array( false, null, 'eq', 'le' ), true );
        if ( $oneLevel && is_numeric( $parentNodeID ) )
            self::$extraTags[] = 'pl' . (int)$parentNodeID;
        else
            self::$extraTags[] = 'dq';
    }

    public static function purgeTags( array $tags )
    {
        $contract = self::contract();
        if ( $contract )
            $contract->purgeTags( $tags );
    }

    public static function purgeAll()
    {
        $contract = self::contract();
        if ( $contract )
            $contract->bumpGeneration();
    }
}
