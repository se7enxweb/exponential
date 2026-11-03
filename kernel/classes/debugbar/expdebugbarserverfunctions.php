<?php
/**
 * The ezjscore server functions of the Exp Debug bar, registered as [ezjscServer_expdebugbar] in
 * extension/ezjscore/settings/ezjscore.ini:
 *
 *   expdebugbar::settings[::<siteaccess>]   every setting, its value in effect, origin, scopes; presets; log
 *   expdebugbar::set                        POST setting, op, value, scope[, siteaccess, confirm, dry_run]
 *   expdebugbar::undo                       POST entry[, force] or group[, force]
 *   expdebugbar::preset                     POST preset, scope | save[, values, snapshot] | delete
 *   expdebugbar::cache                      action=list (GET) | POST action=clear,
 *                                           by=tag|id|all|node|velocity|opcache|querycache_counters
 *   expdebugbar::iptest                     address[, list]
 *   expdebugbar::log[::<limit>]             the newest log entries
 *   expdebugbar::summary                    the summary of this request
 *   expdebugbar::policies[::<role>[::<offset>[::<search>]]]
 *                                           a page of the policies of the user's roles (User tab); "-" for no role
 *                                           or search, the search as base64url
 *   expdebugbar::session                    POST (form token when signed in): the request's own PHP session (User
 *                                           tab, section Session), never cached
 *   expdebugbar::panelheight                POST height: the open panel's height, kept in the user preference
 *                                           expdebugbar_panel_height
 *
 * Reading: summary needs debug output for this request (the people who see the report) or a policy below. settings,
 * log, iptest and policies need setup/setup, the cache list setup/managecache; with debugbar.ini ReadAccess=debug,
 * the default, the debug output reads them too (readsAll()), with ReadAccess=privileged nothing about the
 * configuration (values, file paths, IP and user lists, the change log) goes to anybody else. Changing: set, undo and
 * preset need setup/setup, clearing caches setup/managecache, always, each a POST with the form token.
 * Guide: doc/eng-us/bc/6.0/debug-bar.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expDebugBarServerFunctions extends ezjscServerFunctions
{
    /** @var callable|null Builds the settings service (tests): function( $siteAccess ) */
    public static $serviceFactory = null;

    /** @var bool|null Overrides the POST and token checks (tests): true passes them */
    public static $trustRequest = null;

    /** Never cached by the packer. */
    public static function getCacheTime( $functionName )
    {
        return -1;
    }

    // ------------------------------------------------------------------ checks

    /** Whether the current user has module/function. */
    public static function hasAccess( $module, $function )
    {
        $user = eZUser::currentUser();
        if ( !$user instanceof eZUser )
            return false;
        $access = $user->hasAccessTo( $module, $function );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    public static function canWrite()
    {
        return self::hasAccess( 'setup', 'setup' );
    }

    public static function canCache()
    {
        return self::hasAccess( 'setup', 'managecache' );
    }

    /** @throws InvalidArgumentException unless the user may read what the bar shows */
    public static function requireRead()
    {
        if ( self::canWrite() || self::canCache() || eZDebug::isDebugEnabled() )
            return;
        throw new InvalidArgumentException( 'The debug bar needs debug output for this request, or the policy setup/setup or setup/managecache' );
    }

    /**
     * Whether this request reads everything the bar shows without the policies: it gets the debug output and
     * debugbar.ini ReadAccess is debug, the default (expDebugBarRegistry::readsAll()). Never for a change.
     */
    public static function readsAll()
    {
        return expDebugBarRegistry::readsAll( eZDebug::isDebugEnabled() );
    }

    /** Whether the user may read what setup/setup reads (settings, log, IP test, policies, session frame). */
    public static function canReadSetup()
    {
        return self::canWrite() || self::readsAll();
    }

    /** Whether the user may read the cache list. */
    public static function canReadCache()
    {
        return self::canCache() || self::readsAll();
    }

    /** @throws InvalidArgumentException unless the user may read the settings, the log and the IP test (canReadSetup()) */
    public static function requireSetup()
    {
        if ( self::canReadSetup() )
            return;
        throw new InvalidArgumentException( 'Sign in with setup access to change debug settings (policy setup/setup)' );
    }

    /** @throws InvalidArgumentException unless the user may read the cache list (canReadCache()) */
    public static function requireCache()
    {
        if ( self::canReadCache() )
            return;
        throw new InvalidArgumentException( 'Sign in with cache access to manage caches (policy setup/managecache)' );
    }

    /**
     * Whether this request reads the settings, the log and the IP test without setup/setup, only because it gets the
     * debug output (ReadAccess=debug, readsAll()). Such a reader gets nobody else's data: the change log without who
     * made each change (forReader()), the IP and the user list masked (listsMasked()), the IP test only for the
     * reader's own address (ownIpAnswer()). With DebugByIP disabled that reader can be every visitor of the site.
     */
    public static function readsWithoutPolicy()
    {
        return !self::canWrite() && self::readsAll();
    }

    /**
     * Change log entries without the user id, the login and the address of whoever made each change: those are other
     * people's data. What changed, where and when stays.
     *
     * @param array[] $entries expDebugBarLog::recent()
     * @return array[]
     */
    public static function forReader( array $entries )
    {
        foreach ( $entries as $i => $entry )
        {
            if ( !is_array( $entry ) )
                continue;
            foreach ( array( 'user_id', 'user', 'ip' ) as $key )
            {
                if ( array_key_exists( $key, $entry ) )
                    $entries[$i][$key] = null;
            }
        }
        return $entries;
    }

    /**
     * Settings (expDebugBarSettings::describeAll()) with the values of the IP list and the user list masked, as a
     * secret is: their lines are the addresses and user ids of other people.
     *
     * @param array[] $settings
     * @return array[]
     */
    public static function listsMasked( array $settings )
    {
        foreach ( $settings as $i => $s )
        {
            if ( !is_array( $s ) || !isset( $s['type'] ) || !in_array( $s['type'], array( 'iplist', 'userlist' ), true ) )
                continue;
            $s['secret'] = true;
            foreach ( array( 'effective', 'default' ) as $key )
            {
                if ( array_key_exists( $key, $s ) )
                    $s[$key] = expIniEditor::maskValue( $s[$key] );
            }
            if ( isset( $s['files'] ) && is_array( $s['files'] ) )
            {
                foreach ( $s['files'] as $f => $file )
                {
                    if ( is_array( $file ) && array_key_exists( 'value', $file ) )
                        $s['files'][$f]['value'] = expIniEditor::maskValue( $file['value'] );
                }
            }
            if ( isset( $s['scopes'] ) && is_array( $s['scopes'] ) )
                $s['scopes'] = expIniEditor::maskValue( $s['scopes'] );
            unset( $s['entries'] );
            $settings[$i] = $s;
        }
        return $settings;
    }

    /**
     * An IP test answer (ipAnswer()) for a reader without setup/setup: whether the reader's own address is allowed,
     * without the lines of the list, the line that matched or the warnings that quote a line.
     *
     * @param array $answer
     * @return array
     */
    public static function ownIpAnswer( array $answer )
    {
        $answer['entries'] = array();
        $answer['matched'] = null;
        if ( isset( $answer['request_match'] ) )
            $answer['request_match'] = true;
        if ( isset( $answer['warnings'] ) && is_array( $answer['warnings'] ) )
        {
            $answer['warnings'] = array_values( array_filter( $answer['warnings'], function ( $w ) {
                return is_array( $w ) && isset( $w['code'] ) && in_array( $w['code'], array( 'expired', 'empty', 'lockout' ), true );
            } ) );
        }
        return $answer;
    }

    /** @throws InvalidArgumentException unless the user has setup/setup or setup/managecache */
    public static function requirePrivileged()
    {
        if ( self::canWrite() || self::canCache() )
            return;
        throw new InvalidArgumentException( 'This needs the policy setup/setup or setup/managecache' );
    }

    /**
     * @param string $function 'setup' or 'managecache'
     * @throws InvalidArgumentException unless this is a POST with the right form token by a user with setup/<function>
     */
    public static function requireWrite( $function )
    {
        if ( !self::hasAccess( 'setup', $function ) )
            throw new InvalidArgumentException( "Changing this needs the policy setup/$function" );
        self::requirePost();
    }

    /** @throws InvalidArgumentException unless this is a POST with the right form token (when the protection is on) */
    public static function requirePost()
    {
        if ( self::$trustRequest === true )
            return;
        if ( !isset( $_SERVER['REQUEST_METHOD'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' )
            throw new InvalidArgumentException( 'Changes are sent with POST' );
        $expected = self::token();
        if ( $expected === null )
            return;
        $given = isset( $_POST['ezxform_token'] ) ? (string)$_POST['ezxform_token']
               : ( isset( $_SERVER['HTTP_X_CSRF_TOKEN'] ) ? (string)$_SERVER['HTTP_X_CSRF_TOKEN'] : '' );
        if ( $given === '' || !hash_equals( $expected, $given ) )
            throw new InvalidArgumentException( 'The form token is missing or wrong: reload the page and try again' );
    }

    /** The form token of this session, null when the form token protection is not active. */
    public static function token()
    {
        if ( !class_exists( 'ezxFormToken' ) || !ezxFormToken::isEnabled() )
            return null;
        return ezxFormToken::getToken();
    }

    /** A request variable: POST first, then GET. */
    protected static function param( $name, $default = null )
    {
        if ( isset( $_POST[$name] ) )
            return $_POST[$name];
        if ( isset( $_GET[$name] ) )
            return $_GET[$name];
        return $default;
    }

    protected static function flag( $name )
    {
        $v = self::param( $name, '' );
        return in_array( strtolower( (string)$v ), array( '1', 'true', 'yes', 'on' ), true );
    }

    /** @return expDebugBarSettings */
    protected static function service( $siteAccess = null )
    {
        if ( self::$serviceFactory !== null )
            return call_user_func( self::$serviceFactory, $siteAccess );
        return new expDebugBarSettings( null, $siteAccess );
    }

    protected static function siteAccessParam( $args = array() )
    {
        $sa = isset( $args[0] ) && $args[0] !== '' ? $args[0] : self::param( 'siteaccess' );
        return $sa !== null && $sa !== '' ? (string)$sa : null;
    }

    // ------------------------------------------------------------------ functions

    /**
     * expdebugbar::settings[::<siteaccess>]
     */
    public static function settings( $args )
    {
        self::requireSetup();
        $service = self::service( self::siteAccessParam( $args ) );
        $user = eZUser::currentUser();
        $registry = $service->registry();
        $settings = $service->describeAll();
        $ip = self::ipAnswer( null, null, $service );
        $log = $service->log()->recent( (int)$registry->option( 'LogListLimit', 20 ) );
        // a reader without setup/setup (ReadAccess=debug) gets nobody else's data
        if ( self::readsWithoutPolicy() )
        {
            $settings = self::listsMasked( $settings );
            $ip = self::ownIpAnswer( $ip );
            $log = self::forReader( $log );
        }
        return array(
            'siteaccess' => $service->siteAccess(),
            'siteaccesses' => expIniEditor::knownSiteAccesses(),
            'can_write' => self::canWrite(),
            'can_cache' => self::canCache(),
            'token' => self::token(),
            'token_field' => 'ezxform_token',
            'user' => array( 'id' => (int)$user->attribute( 'contentobject_id' ), 'login' => (string)$user->attribute( 'login' ) ),
            'scopes' => $service->scopes(),
            'default_scope' => $service->defaultScope(),
            'groups' => $registry->groups(),
            'settings' => $settings,
            'presets' => $service->presets(),
            'ip' => $ip,
            'log' => $log,
            'problems' => $registry->problems(),
        );
    }

    /**
     * expdebugbar::set, POST setting, op, value, scope[, siteaccess, confirm, dry_run]
     */
    public static function set( $args )
    {
        self::requireWrite( 'setup' );
        $service = self::service( self::siteAccessParam() );
        $id = (string)self::param( 'setting', '' );
        $scope = (string)self::param( 'scope', $service->defaultScope() );
        return self::guard( function () use ( $service, $id, $scope ) {
            return $service->write( $id, (string)self::param( 'op', 'set' ), self::param( 'value', '' ), $scope,
                                    array( 'confirm' => self::flag( 'confirm' ), 'dry_run' => self::flag( 'dry_run' ) ) );
        } );
    }

    /**
     * expdebugbar::undo, POST entry[, force] or group[, force]
     */
    public static function undo( $args )
    {
        self::requireWrite( 'setup' );
        $service = self::service( self::siteAccessParam() );
        $group = (string)self::param( 'group', '' );
        $entry = (string)self::param( 'entry', isset( $args[0] ) ? $args[0] : '' );
        return self::guard( function () use ( $service, $group, $entry ) {
            if ( $group !== '' )
                return $service->undoGroup( $group, self::flag( 'force' ) );
            return $service->undo( $entry, self::flag( 'force' ) );
        } );
    }

    /**
     * expdebugbar::preset, POST preset + scope (apply) | save [+ values | snapshot] | delete
     */
    public static function preset( $args )
    {
        self::requireWrite( 'setup' );
        $service = self::service( self::siteAccessParam() );
        return self::guard( function () use ( $service ) {
            $save = self::param( 'save' );
            if ( $save !== null && $save !== '' )
            {
                $values = self::param( 'values' );
                $values = self::flag( 'snapshot' ) || $values === null || $values === '' ? null : json_decode( (string)$values, true );
                if ( $values !== null && !is_array( $values ) )
                    throw new InvalidArgumentException( 'values must be a JSON object of setting id => value' );
                return array( 'ok' => true, 'preset' => $service->saveUserPreset( (string)$save, $values ) );
            }
            $delete = self::param( 'delete' );
            if ( $delete !== null && $delete !== '' )
                return array( 'ok' => $service->deleteUserPreset( (string)$delete ) );
            return $service->applyPreset( (string)self::param( 'preset', '' ), (string)self::param( 'scope', $service->defaultScope() ),
                                          array( 'confirm' => self::flag( 'confirm' ), 'dry_run' => self::flag( 'dry_run' ) ) );
        } );
    }

    /**
     * expdebugbar::cache, action=list | action=clear (POST) by=tag|id|all|node|velocity|opcache|querycache_counters
     * (the query cache's entries are the cache id 'querycache'; querycache_counters zeroes this server's counters)
     */
    public static function cache( $args )
    {
        $action = (string)self::param( 'action', isset( $args[0] ) ? $args[0] : 'list' );
        if ( $action !== 'clear' )
        {
            self::requireCache();
            return self::cacheList();
        }
        self::requireWrite( 'managecache' );
        $by = (string)self::param( 'by', '' );
        $names = expCacheManager::splitList( self::param( 'names', '' ) );
        $dryRun = self::flag( 'dry_run' );
        switch ( $by )
        {
            case 'all':
            case 'tag':
            case 'id':
                $manager = new expCacheManager();
                $result = $manager->clear( $by, $names, $dryRun );
                break;
            case 'node':
                $nodeID = (int)self::param( 'node_id', 0 );
                $node = $nodeID ? eZContentObjectTreeNode::fetch( $nodeID ) : null;
                if ( !$node instanceof eZContentObjectTreeNode )
                    $result = expCacheManager::result( false, "No node $nodeID" );
                else if ( $dryRun )
                    $result = expCacheManager::result( true, "would clear the view cache of node $nodeID", array(), true );
                else
                {
                    eZContentCacheManager::clearNodeViewCacheArray( array( $nodeID ), array( (int)$node->attribute( 'contentobject_id' ) ) );
                    $result = expCacheManager::result( true, "cleared the view cache of node $nodeID (" . $node->attribute( 'name' ) . ')' );
                }
                $names = array( (string)$nodeID );
                break;
            case 'velocity':
                $result = expCacheManager::clearVelocityCache( $dryRun );
                break;
            case 'opcache':
                $result = expCacheManager::resetOPcache( $dryRun );
                break;
            case 'querycache_counters':
                if ( !class_exists( 'eZDBQueryCache' ) )
                    $result = expCacheManager::result( false, 'The SQL query cache is not available' );
                else if ( $dryRun )
                    $result = expCacheManager::result( true, "would reset this server's query cache counters", array(), true );
                else
                {
                    eZDBQueryCache::resetStats();
                    $result = expCacheManager::result( true, "reset this server's query cache counters" );
                }
                break;
            default:
                $result = expCacheManager::result( false, "unknown by '$by': all, tag, id, node, velocity, opcache or querycache_counters" );
        }
        if ( $result['ok'] && !$dryRun )
        {
            try
            {
                self::service()->log()->append( array( 'op' => 'cache', 'by' => $by, 'names' => $names, 'message' => $result['message'], 'undoes' => null ) );
            }
            catch ( Exception $e )
            {
                $result['warnings'][] = $e->getMessage();
            }
        }
        return $result;
    }

    /** The cache tab: every cache with its description, the tags, when each was last cleared from the bar. */
    public static function cacheList()
    {
        $manager = new expCacheManager();
        $caches = array();
        foreach ( $manager->cacheList() as $item )
        {
            $d = $manager->describeItem( $item );
            $d['description'] = $d['name'] . ': ' . $d['how'];
            $caches[] = $d;
        }
        $tags = array();
        foreach ( $manager->tagMap() as $tag => $ids )
            $tags[] = array( 'tag' => $tag, 'ids' => $ids, 'description' => 'Clears ' . implode( ', ', $ids ) );
        $last = array();
        foreach ( self::service()->log()->recent( 0, 'cache' ) as $e )
        {
            $names = !empty( $e['names'] ) ? (array)$e['names'] : array( '' );
            foreach ( $names as $n )
            {
                $key = $e['by'] . ( $n !== '' ? ':' . $n : '' );
                if ( !isset( $last[$key] ) )
                    $last[$key] = array( 'time' => $e['time'], 'user' => $e['user'] );
            }
        }
        $php = expCacheManager::phpCacheState();
        $nodeID = (int)self::param( 'node_id', 0 );
        return array(
            'can_cache' => self::canCache(),
            'caches' => $caches,
            'tags' => $tags,
            'last_cleared' => $last,
            'page' => array( 'node_id' => $nodeID ?: null, 'object_id' => (int)self::param( 'object_id', 0 ) ?: null ),
            'velocity' => expCacheManager::velocityCacheStatus(),
            'opcache' => isset( $php['opcache'] ) ? $php['opcache'] : null,
            // the SQL query cache: settings, counters, APCu memory and what it holds (tables, largest entries)
            'querycache' => class_exists( 'eZDBQueryCache' ) ? eZDBQueryCache::inventory() : null,
            'http_cache' => expCacheManager::httpCacheEnabled() ? expCacheManager::httpCacheStatus() : null,
        );
    }

    /**
     * expdebugbar::iptest, address[, list]
     */
    public static function iptest( $args )
    {
        self::requireSetup();
        $address = self::param( 'address', isset( $args[0] ) ? $args[0] : null );
        $list = self::param( 'list' );
        if ( is_string( $list ) && $list !== '' )
        {
            $list = json_decode( $list, true );
            if ( !is_array( $list ) )
                throw new InvalidArgumentException( 'list must be a JSON array of lines' );
        }
        // a reader without setup/setup tests only their own address against the list in effect: another address
        // would tell whether it is in the list
        if ( self::readsWithoutPolicy() )
            return self::ownIpAnswer( self::ipAnswer( null, null, self::service( self::siteAccessParam() ) ) );
        $answer = self::ipAnswer( $address !== null && $address !== '' ? (string)$address : null, is_array( $list ) ? $list : null, self::service( self::siteAccessParam() ) );
        return $answer;
    }

    /**
     * The iptest answer.
     *
     * @param string|null $address null: the request's
     * @param array|null $list null: the list in effect
     * @param expDebugBarSettings $service
     * @return array
     */
    public static function ipAnswer( $address, $list, expDebugBarSettings $service )
    {
        $ini = eZINI::instance();
        $header = $ini->variable( 'HTTPHeaderSettings', 'ClientIpByCustomHTTPHeader' );
        // The header counts only for a request from a trusted proxy
        // ([HTTPHeaderSettings] TrustedProxies[]), as in eZSys::clientIP().
        $trusts = $header && $header !== 'false' && eZSys::isFromTrustedProxy();
        $remote = eZSys::serverVariable( 'REMOTE_ADDR', true );
        $client = eZSys::clientIP();
        $test = $address !== null ? $address : ( $client ? (string)$client : null );
        $packed = $test !== null ? expDebugBarIPList::packedClient( $test ) : false;
        if ( $list === null )
            $list = (array)$service->effective( 'debug_ip_list' );
        $analysis = expDebugBarIPList::analyse( $list, $packed !== false ? $test : null, $service->now );
        $byIP = expDebugBarRegistry::isOn( (string)$service->effective( 'debug_by_ip' ) );
        $warnings = $analysis['warnings'];
        if ( !$byIP )
            $warnings = array_values( array_filter( $warnings, function ( $w ) { return in_array( $w['code'], array( 'invalid', 'expired' ), true ); } ) );
        return array(
            'address' => $test,
            'valid' => $packed !== false,
            'family' => $packed === false ? null : ( strlen( $packed ) === 4 ? 4 : 6 ),
            'remote_addr' => $remote ? (string)$remote : null,
            'client_ip' => $client ? (string)$client : null,
            'trusts_proxy' => $trusts,
            'proxy_header' => $trusts ? $header : null,
            'suggest' => expDebugBarIPList::suggest( $test ),
            'debug_by_ip' => $byIP,
            'allowed' => $analysis['allowed'],
            'matched' => $analysis['matched'],
            'request_match' => isset( $GLOBALS['eZDebugIPMatch'] ) ? $GLOBALS['eZDebugIPMatch'] : null,
            'entries' => $analysis['entries'],
            'warnings' => $warnings,
        );
    }

    /**
     * expdebugbar::log[::<limit>]
     */
    public static function log( $args )
    {
        self::requireSetup();
        $limit = isset( $args[0] ) && ctype_digit( (string)$args[0] ) ? (int)$args[0] : 50;
        $entries = self::service()->log()->recent( min( 500, max( 1, $limit ) ) );
        return array( 'entries' => self::readsWithoutPolicy() ? self::forReader( $entries ) : $entries );
    }

    /**
     * expdebugbar::policies[::<role>[::<offset>[::<search>]]]: the policies of the current user's roles for the User
     * tab, a page at a time (expDebugBarPolicies::PAGE rows), with the roles and their number of policies. Everything
     * is in the path, nothing in the query string (policyArgs()): role, one of the user's role ids or "-" for all; the
     * offset of the page; the search as base64url of its UTF-8 text (it may hold "/" and "::"), "-" for none.
     * For who may read what setup/setup reads (canReadSetup()), as the section.
     *
     * @return array expDebugBarPolicies::forUser()
     */
    public static function policies( $args )
    {
        self::requireSetup();
        list( $role, $offset, $query ) = self::policyArgs( $args );
        return expDebugBarPolicies::forUser( eZUser::currentUser(), $role, $query, $offset );
    }

    /**
     * expdebugbar::session: the PHP session of this very request for the User tab (section "Session", loaded when
     * opened): its keys as a tree, grouped by extension, values of secrets masked, and its frame. Never another
     * session. Who sees the User tab may open it (expDebugBarUser::mayView()); the save path and the handler class
     * for who may read what setup/setup reads (canReadSetup()).
     *
     * @return array expDebugBarSession::forSession()
     */
    public static function session( $args )
    {
        $user = eZUser::currentUser();
        $loggedIn = $user instanceof eZUser && $user->isRegistered();
        // the answer holds the user's own session: no cache or proxy may keep it, the refusal included
        self::noStore();
        if ( !expDebugBarUser::mayView( self::canWrite(), $loggedIn, eZDebug::isDebugEnabled() ) )
            throw new InvalidArgumentException( 'The session is shown to who sees the User tab (setup/setup, or the debug output under debugbar.ini ReadAccess)' );
        self::requireSessionRequest( $loggedIn );
        return expDebugBarSession::current( self::canReadSetup() );
    }

    /** Headers that keep an answer out of every cache: the browser's, a proxy's, Velocity's response cache. */
    protected static function noStore()
    {
        if ( headers_sent() )
            return;
        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );
        header( 'X-Robots-Tag: noindex' );
    }

    /**
     * The session is asked for with POST from the page itself: a signed-in user's request carries the form token
     * (requirePost()); an anonymous visitor has none, so a request the browser marks as sent by another site is refused.
     *
     * @throws InvalidArgumentException
     */
    public static function requireSessionRequest( $loggedIn, $server = null )
    {
        if ( self::$trustRequest === true )
            return;
        $server = is_array( $server ) ? $server : $_SERVER;
        if ( !isset( $server['REQUEST_METHOD'] ) || $server['REQUEST_METHOD'] !== 'POST' )
            throw new InvalidArgumentException( 'The session is asked for with POST' );
        if ( isset( $server['HTTP_SEC_FETCH_SITE'] ) && !in_array( strtolower( (string)$server['HTTP_SEC_FETCH_SITE'] ), array( 'same-origin', 'none' ), true ) )
            throw new InvalidArgumentException( 'The session is only given to the page of this site' );
        if ( $loggedIn )
            self::requirePost();
    }

    /**
     * The arguments of expdebugbar::policies from the path, checked: "-" or nothing for no role and no search.
     *
     * @param array $args the path arguments after the function name
     * @return array role id or null, offset, search ('' for none; at most 100 characters)
     * @throws InvalidArgumentException for a role or offset that is not a number, or a search that is not base64url
     *         of UTF-8 text
     */
    public static function policyArgs( $args )
    {
        $args = array_values( (array)$args );
        $role = isset( $args[0] ) && $args[0] !== '' ? (string)$args[0] : '-';
        $offset = isset( $args[1] ) && $args[1] !== '' ? (string)$args[1] : '0';
        $search = isset( $args[2] ) && $args[2] !== '' ? (string)$args[2] : '-';
        if ( !preg_match( '/^(-|[0-9]{1,10})$/', $role ) )
            throw new InvalidArgumentException( 'The role is a role id, or - for all' );
        if ( !preg_match( '/^[0-9]{1,9}$/', $offset ) )
            throw new InvalidArgumentException( 'The offset is a number' );
        $query = '';
        if ( $search !== '-' )
        {
            $raw = preg_match( '/^[A-Za-z0-9_-]{2,800}$/', $search )
                 ? base64_decode( strtr( $search, '-_', '+/' ) . str_repeat( '=', ( 4 - strlen( $search ) % 4 ) % 4 ), true )
                 : false;
            if ( $raw === false || !preg_match( '//u', $raw ) )
                throw new InvalidArgumentException( 'The search is base64url of UTF-8 text, or - for none' );
            $query = function_exists( 'mb_substr' ) ? mb_substr( $raw, 0, 100, 'UTF-8' ) : substr( $raw, 0, 100 );
        }
        return array( $role === '-' ? null : (int)$role, (int)$offset, $query );
    }

    /**
     * expdebugbar::summary
     */
    public static function summary( $args )
    {
        self::requireRead();
        return expDebugBarSummary::collect();
    }

    /**
     * expdebugbar::panelheight, POST height (pixels, 0 for the default) with the form token: the height the user
     * dragged the open panel to, stored in the user preference expdebugbar_panel_height
     * (expDebugBarReport::PANEL_HEIGHT_PREFERENCE), which the report reads back. For anybody who sees the debug
     * output and is logged in: an anonymous user's preferences would need a session, which the debug bar never starts,
     * so the script keeps an anonymous visitor's height in localStorage instead and never calls this; it refuses an
     * anonymous visitor before eZPreferences is touched.
     *
     * @return array ok, height (as stored: 0, or held between PANEL_HEIGHT_MIN and PANEL_HEIGHT_MAX), preference
     */
    public static function panelheight( $args )
    {
        // the anonymous visitor first: refused before the token check or the preferences could touch a session
        $user = eZUser::currentUser();
        if ( !$user instanceof eZUser || !$user->isRegistered() )
            throw new InvalidArgumentException( 'Only a logged-in user has preferences: the height is not saved for anonymous visitors' );
        self::requireRead();
        self::requirePost();
        $height = expDebugBarReport::panelHeight( self::param( 'height', '' ) );
        if ( $height === null )
            throw new InvalidArgumentException( 'The height is a whole number of pixels, or 0 for the default' );
        if ( !eZPreferences::setValue( expDebugBarReport::PANEL_HEIGHT_PREFERENCE, (string)$height ) )
            throw new RuntimeException( 'The height could not be stored' );
        return array( 'ok' => true, 'height' => $height, 'preference' => expDebugBarReport::PANEL_HEIGHT_PREFERENCE );
    }

    /**
     * Runs a write and turns the INI engine's refusals into an answer with ok false (ezjscore turns any other
     * exception into its error_text).
     */
    protected static function guard( $fn )
    {
        try
        {
            return $fn();
        }
        catch ( expIniException $e )
        {
            return array( 'ok' => false, 'message' => $e->getMessage(), 'needs_confirm' => null );
        }
    }
}
