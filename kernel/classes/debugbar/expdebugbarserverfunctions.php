<?php
/**
 * The ezjscore server functions of the Exp Debug bar, registered as [ezjscServer_expdebugbar] in
 * extension/ezjscore/settings/ezjscore.ini:
 *
 *   expdebugbar::settings[::<siteaccess>]   every setting, its value in effect, origin, scopes; presets; log
 *   expdebugbar::set                        POST setting, op, value, scope[, siteaccess, confirm, dry_run]
 *   expdebugbar::undo                       POST entry[, force] or group[, force]
 *   expdebugbar::preset                     POST preset, scope | save[, values, snapshot] | delete
 *   expdebugbar::cache                      action=list (GET) | POST action=clear, by=tag|id|all|node|velocity|opcache
 *   expdebugbar::iptest                     address[, list]
 *   expdebugbar::log[::<limit>]             the newest log entries
 *   expdebugbar::summary                    the summary of this request
 *
 * Reading needs setup/setup, setup/managecache or debug output for this request (the people who see the report);
 * writing settings needs setup/setup, clearing caches setup/managecache; every write is a POST with the form token.
 * Guide: doc/bc/6.0/debug-bar.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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

    /** @throws InvalidArgumentException unless the user has setup/setup or setup/managecache */
    public static function requirePrivileged()
    {
        if ( self::canWrite() || self::canCache() )
            return;
        throw new InvalidArgumentException( 'This needs the policy setup/setup or setup/managecache' );
    }

    /**
     * The iptest answer for a visitor without the policies: the own address and whether it is let in, not the
     * list.
     */
    public static function publicIPAnswer( array $ip )
    {
        $ip['entries'] = array();
        if ( $ip['matched'] !== null )
            $ip['matched'] = array( 'index' => $ip['matched']['index'] );
        $ip['request_match'] = $ip['request_match'] !== null ? array( 'index' => $ip['request_match']['index'] ) : null;
        $ip['warnings'] = array();
        return $ip;
    }

    /**
     * @param string $function 'setup' or 'managecache'
     * @throws InvalidArgumentException unless this is a POST with the right form token by a user with setup/<function>
     */
    public static function requireWrite( $function )
    {
        if ( !self::hasAccess( 'setup', $function ) )
            throw new InvalidArgumentException( "Changing this needs the policy setup/$function" );
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
        self::requireRead();
        $service = self::service( self::siteAccessParam( $args ) );
        $user = eZUser::currentUser();
        $registry = $service->registry();
        $privileged = self::canWrite() || self::canCache();
        $settings = $service->describeAll();
        $ip = self::ipAnswer( null, null, $service );
        if ( !$privileged )
        {
            // who gets debug (addresses, user ids) and who changed what are for the people who may change them
            foreach ( $settings as $i => $s )
            {
                if ( in_array( $s['type'], array( 'iplist', 'userlist' ), true ) )
                {
                    $settings[$i]['effective'] = null;
                    $settings[$i]['files'] = array();
                    $settings[$i]['scopes'] = array();
                    $settings[$i]['entries'] = array();
                    $settings[$i]['hidden'] = true;
                }
            }
            $ip = self::publicIPAnswer( $ip );
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
            'log' => $privileged ? $service->log()->recent( (int)$registry->option( 'LogListLimit', 20 ) ) : array(),
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
     * expdebugbar::cache, action=list | action=clear (POST) by=tag|id|all|node|velocity|opcache
     */
    public static function cache( $args )
    {
        $action = (string)self::param( 'action', isset( $args[0] ) ? $args[0] : 'list' );
        if ( $action !== 'clear' )
        {
            self::requirePrivileged();
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
            default:
                $result = expCacheManager::result( false, "unknown by '$by': all, tag, id, node, velocity or opcache" );
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
            'http_cache' => expCacheManager::httpCacheEnabled() ? expCacheManager::httpCacheStatus() : null,
        );
    }

    /**
     * expdebugbar::iptest, address[, list]
     */
    public static function iptest( $args )
    {
        self::requireRead();
        $address = self::param( 'address', isset( $args[0] ) ? $args[0] : null );
        $list = self::param( 'list' );
        if ( is_string( $list ) && $list !== '' )
        {
            $list = json_decode( $list, true );
            if ( !is_array( $list ) )
                throw new InvalidArgumentException( 'list must be a JSON array of lines' );
        }
        $answer = self::ipAnswer( $address !== null && $address !== '' ? (string)$address : null, is_array( $list ) ? $list : null, self::service( self::siteAccessParam() ) );
        // the list in effect is for the people who may change it; a list the caller sent is theirs to see
        if ( !is_array( $list ) && !self::canWrite() && !self::canCache() )
            $answer = self::publicIPAnswer( $answer );
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
        $trusts = $header && $header !== 'false';
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
        self::requirePrivileged();
        $limit = isset( $args[0] ) && ctype_digit( (string)$args[0] ) ? (int)$args[0] : 50;
        return array( 'entries' => self::service()->log()->recent( min( 500, max( 1, $limit ) ) ) );
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
