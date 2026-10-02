<?php
/**
 * The audit: what happens in Exponential, recorded as hash-chained JSON lines per channel
 * (doc/bc/6.0/audit.md, "The developer API (Z3)").
 *
 *   expAudit::event( 'content.node.move', array( 'object' => ..., 'target' => ..., 'before' => ..., 'after' => ... ) );
 *   $parent = expAudit::begin( 'content.node.remove', ... ); ... expAudit::end( $parent, array( 'after' => ... ) );
 *   expAudit::withParent( $id, function () { ... } );   expAudit::setJob( $jobID );   expAudit::setRun( $runID );
 *   if ( expAudit::isOn( 'access.role.change' ) ) { ...build the expensive before/after... }
 *
 * event() never throws: a failure to record goes to error.log through eZDebug and the caller's work goes on.
 * Events are buffered and written in one append per channel at the end of the request (the cleanup handler of
 * eZExecution, which ezpKernelWeb::shutdown() and eZScript::shutdown() run; on a fatal error the fatal error
 * handler; as a last resort a shutdown function); ImmediateEvents[] (access.*, system.audit.*, settings writes)
 * are written at once. Per-request state is reset in ezpKernelWeb::__construct() (resetRequest()) and whenever
 * REQUEST_TIME_FLOAT changes, so nothing of one request reaches the next in a persistent Velocity worker.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAudit
{
    const VERSION = 1;
    const ULID_ALPHABET = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

    /** @var string|null The request the state below belongs to (REQUEST_TIME_FLOAT) */
    protected static $requestKey = null;

    /** @var string|null */
    protected static $requestId = null;

    /** @var array|null The request fields of this request (without ms and status) */
    protected static $requestFields = null;

    /** @var array user id => actor fields, this request */
    protected static $actors = array();

    /** @var expAuditBuffer|null */
    protected static $buffer = null;

    /** @var array id => open parent: name, data, depth, children, omitted, channel */
    protected static $open = array();

    /** @var string[] withParent() stack */
    protected static $parentStack = array();

    /** @var string|null */
    protected static $job = null;

    /** @var string|null */
    protected static $run = null;

    /** @var float Time of the last flush (commands flush every FlushInterval seconds) */
    protected static $lastFlush = 0.0;

    /** @var int Depth of event() calls in progress (a record that records another one) */
    protected static $inside = 0;

    /** @var bool The cleanup and fatal error handlers are registered (they persist in a Velocity worker) */
    protected static $handlersRegistered = false;

    /** @var bool The shutdown function is registered for this request */
    protected static $shutdownRegistered = false;

    /** @var bool An early flush was recorded as system.audit.overflow in this request */
    protected static $overflowRecorded = false;

    /** @var bool A checkpoint is being written by this process */
    protected static $checkpointing = false;

    /** @var array channel => record[] that could not be written, for one more try at the end */
    protected static $failed = array();

    /** @var bool resetRequest() is flushing what the previous request left */
    protected static $resetting = false;

    /** @var array|null Fixed clock for tests: microtime float */
    protected static $now = null;

    // ------------------------------------------------------------------ the API

    /**
     * Records one event.
     *
     * @param string $name a taxonomy name, e.g. 'content.node.move'
     * @param array $data object, target, before, after, result ('success'), reason, error, severity, parent, x,
     *                    actor (only to name an actor other than the current user), verb, id (reserved by begin())
     * @return string|null the event id (a ULID), or null when it is not recorded
     */
    public static function event( $name, array $data = array() )
    {
        try
        {
            self::forRequest();
            if ( self::$inside > 4 )
                return null;
            $config = expAuditConfig::get();
            if ( !$config['enabled'] )
                return null;
            $decision = expAuditTaxonomy::decide( $name, $config );
            if ( !$decision['valid'] )
            {
                self::warn( "Not an audit event name: '" . (string)$name . "'" );
                return null;
            }
            if ( !$decision['on'] )
                return null;
            if ( $decision['sampled'] && mt_rand() / mt_getrandmax() > $config['sampleRate'] )
                return null;

            $parent = isset( $data['parent'] ) ? (string)$data['parent'] : ( self::$parentStack ? end( self::$parentStack ) : null );
            $depth = 0;
            if ( $parent !== null && isset( self::$open[$parent] ) )
            {
                $depth = self::$open[$parent]['depth'] + 1;
                if ( $depth > $config['childDepth'] || self::$open[$parent]['children'] >= $config['maxChildren'] )
                {
                    self::$open[$parent]['omitted']++;
                    return null;
                }
                self::$open[$parent]['children']++;
            }
            $data['parent'] = $parent;
            $data['depth'] = $depth;

            $record = self::build( $name, $data, $decision['channel'], $decision['severity'], $config );
            self::$inside++;
            try
            {
                self::store( $record, $decision['immediate'], $config );
            }
            finally
            {
                self::$inside--;
            }
            return $record['id'];
        }
        catch ( Throwable $e )
        {
            self::failure( $e, 'recording ' . (string)$name );
            return null;
        }
    }

    /**
     * Starts a parent event: its id is returned at once so children can name it; the record is written by end()
     * (after its children), or by the final flush with result failed when the request ends first.
     *
     * @param string $name
     * @param array $data as event()
     * @return string|null
     */
    public static function begin( $name, array $data = array() )
    {
        try
        {
            self::forRequest();
            $config = expAuditConfig::get();
            if ( !$config['enabled'] || !expAuditTaxonomy::decide( $name, $config )['on'] )
                return null;
            $id = self::ulid();
            $parent = isset( $data['parent'] ) ? (string)$data['parent'] : ( self::$parentStack ? end( self::$parentStack ) : null );
            $depth = $parent !== null && isset( self::$open[$parent] ) ? self::$open[$parent]['depth'] + 1 : 0;
            self::$open[$id] = array( 'name' => $name, 'data' => $data + array( 'time_ms' => self::nowMs() ), 'depth' => $depth,
                                      'children' => 0, 'omitted' => 0, 'parent' => $parent );
            return $id;
        }
        catch ( Throwable $e )
        {
            self::failure( $e, 'beginning ' . (string)$name );
            return null;
        }
    }

    /**
     * Ends a parent event and records it.
     *
     * @param string|null $id from begin()
     * @param array $data merged over begin()'s data (after is merged key by key)
     * @return string|null
     */
    public static function end( $id, array $data = array() )
    {
        if ( $id === null || !isset( self::$open[$id] ) )
            return null;
        $open = self::$open[$id];
        unset( self::$open[$id] );
        $merged = $open['data'];
        foreach ( $data as $k => $v )
        {
            if ( $k === 'after' && isset( $merged['after'] ) && is_array( $merged['after'] ) && is_array( $v ) )
                $merged['after'] = $v + $merged['after'];
            else
                $merged[$k] = $v;
        }
        if ( $open['omitted'] > 0 )
            $merged['after'] = ( isset( $merged['after'] ) && is_array( $merged['after'] ) ? $merged['after'] : array() )
                               + array( 'children_omitted' => $open['omitted'] );
        $merged['id'] = $id;
        $merged['parent'] = $open['parent'];
        return self::event( $open['name'], $merged );
    }

    /**
     * Runs code with $id as the implicit parent of every event inside.
     *
     * @param string|null $id
     * @param callable $code
     * @return mixed what $code returns
     */
    public static function withParent( $id, $code )
    {
        if ( $id === null )
            return call_user_func( $code );
        self::$parentStack[] = $id;
        try
        {
            return call_user_func( $code );
        }
        finally
        {
            array_pop( self::$parentStack );
        }
    }

    /** Sets the content job id of the following events of this process (null resets it). */
    public static function setJob( $jobID )
    {
        self::forRequest();
        self::$job = $jobID === null ? null : (string)$jobID;
    }

    /** Sets the cronjob run of the following events of this process (null resets it). */
    public static function setRun( $runID )
    {
        self::forRequest();
        self::$run = $runID === null ? null : (string)$runID;
    }

    /** @return bool Audit=enabled */
    public static function isEnabled()
    {
        try
        {
            return expAuditConfig::get()['enabled'];
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * Whether a name is recorded (ask before building expensive before/after values).
     *
     * @param string $name
     * @return bool
     */
    public static function isOn( $name )
    {
        try
        {
            $config = expAuditConfig::get();
            return $config['enabled'] && expAuditTaxonomy::decide( $name, $config )['on'];
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /** @return string The request id of this request ("r-" + ULID), made on first use */
    public static function requestId()
    {
        self::forRequest();
        if ( self::$requestId === null )
            self::$requestId = self::incomingRequestId() ?: 'r-' . self::ulid();
        return self::$requestId;
    }

    /**
     * The response header carrying the request id ([AuditRecordSettings] RequestIdHeader), or null when audit is
     * off or no header is configured.
     *
     * @return array|null array( name, value )
     */
    public static function responseHeader()
    {
        try
        {
            $config = expAuditConfig::get();
            if ( !$config['enabled'] || $config['requestIdHeader'] === '' || !preg_match( '/^[A-Za-z0-9-]{1,64}$/', $config['requestIdHeader'] ) )
                return null;
            self::registerHandlers();
            return array( $config['requestIdHeader'], self::requestId() );
        }
        catch ( Throwable $e )
        {
            self::failure( $e, 'the request id header' );
            return null;
        }
    }

    // ------------------------------------------------------------------ settings writes

    /**
     * Records an INI file written through expIniEditor: system.setting.write per changed variable (object: file,
     * block, variable, scope, path; before/after: the value in that file), and for audit.ini also
     * system.audit.setting.write, and system.audit.disable when Audit=disabled is written. Values of variables
     * expIniEditor::isSecret() recognises are recorded as [secret]; the unified diff is kept on the first record
     * when no secret changed. Writes of a fixture root (tests of the INI engine) are not recorded.
     *
     * @param string $file site.ini
     * @param string $scope global, siteaccess:admin, ...
     * @param string $relativePath settings/override/site.ini.append.php
     * @param string $oldContent
     * @param string $newContent
     * @param string $diff
     * @return int the number of records
     */
    public static function settingWrite( $file, $scope, $relativePath, $oldContent, $newContent, $diff = '' )
    {
        try
        {
            if ( expAuditKeys::isGenerating() )
                return 0;
            if ( !expAuditConfig::isOverridden() && class_exists( 'expIniEditor' ) && !expIniEditor::isRealRoot() )
                return 0;
            $isAuditIni = strtolower( $file ) === 'audit.ini';
            if ( !$isAuditIni && !self::isOn( 'system.setting.write' ) )
                return 0;
            $old = (string)$oldContent === '' ? array() : ( new expIniWriter( (string)$oldContent ) )->values();
            $new = ( new expIniWriter( (string)$newContent ) )->values();
            $changes = array();
            foreach ( array_unique( array_merge( array_keys( $old ), array_keys( $new ) ) ) as $block )
            {
                $o = isset( $old[$block] ) ? $old[$block] : array();
                $n = isset( $new[$block] ) ? $new[$block] : array();
                foreach ( array_unique( array_merge( array_keys( $o ), array_keys( $n ) ) ) as $var )
                {
                    $before = array_key_exists( $var, $o ) ? $o[$var] : null;
                    $after = array_key_exists( $var, $n ) ? $n[$var] : null;
                    if ( $before !== $after )
                        $changes[] = array( (string)$block, (string)$var, $before, $after );
                }
            }
            if ( !$changes )
                return 0;
            $anySecret = false;
            foreach ( $changes as $c )
                $anySecret = $anySecret || expAuditPrivacy::isSecretName( $c[1] );
            $count = 0;
            $max = 50;
            foreach ( $changes as $i => list( $block, $var, $before, $after ) )
            {
                if ( $i >= $max )
                    break;
                $secret = expAuditPrivacy::isSecretName( $var );
                $mask = function ( $v ) use ( $secret ) {
                    if ( !$secret || $v === null || $v === '' )
                        return $v;
                    return is_array( $v ) ? array_map( function () { return expAuditPrivacy::SECRET; }, $v ) : expAuditPrivacy::SECRET;
                };
                $data = array(
                    'object' => array( 'type' => 'setting', 'id' => "$file/$block/$var", 'file' => $file, 'block' => $block,
                                       'variable' => $var, 'scope' => (string)$scope, 'path' => (string)$relativePath ),
                    'before' => array( 'value' => $mask( $before ), 'set' => $before !== null ),
                    'after' => array( 'value' => $mask( $after ), 'set' => $after !== null ),
                );
                if ( $i === 0 && !$anySecret && $diff !== '' )
                    $data['after']['diff'] = (string)$diff;
                if ( $i === 0 && count( $changes ) > 1 )
                    $data['after']['variables_changed'] = count( $changes );
                if ( self::event( 'system.setting.write', $data ) !== null )
                    $count++;
                if ( $isAuditIni )
                {
                    self::event( 'system.audit.setting.write', $data );
                    if ( $block === 'AuditSettings' && $var === 'Audit' && is_string( $after ) && strtolower( trim( $after ) ) !== 'enabled' )
                        self::event( 'system.audit.disable', array( 'object' => array( 'type' => 'audit', 'id' => 'Audit' ),
                                                                    'before' => array( 'state' => $before ), 'after' => array( 'state' => $after, 'path' => (string)$relativePath ) ) );
                }
            }
            return $count;
        }
        catch ( Throwable $e )
        {
            self::failure( $e, 'recording a settings write' );
            return 0;
        }
    }

    // ------------------------------------------------------------------ the 4.x compatibility path

    /**
     * eZAudit::writeAudit( $oldName, $attributes ): the old name mapped through [AuditCompatSettings] Map[] (or
     * system.legacy.<name>), the attributes kept under after.legacy (Comment dropped, NeverRecord[] and secrets
     * never recorded), the object and target taken from the attributes the old call sites pass.
     *
     * @param string $oldName
     * @param array $attributes
     * @return string|null the event id
     */
    public static function legacy( $oldName, $attributes = array(), $caller = null )
    {
        try
        {
            self::forRequest();
            $config = expAuditConfig::get();
            if ( !$config['enabled'] )
                return null;
            $attributes = is_array( $attributes ) ? $attributes : array();
            if ( $config['legacyFiles'] && class_exists( 'eZAudit' ) )
                eZAudit::writeLegacyFile( $oldName, $attributes );

            $attrs = array();
            foreach ( $attributes as $k => $v )
                $attrs[trim( (string)$k, " :\t" )] = is_scalar( $v ) || $v === null ? $v : ( is_array( $v ) ? $v : (string)json_encode( $v ) );
            $comment = isset( $attrs['Comment'] ) ? (string)$attrs['Comment'] : '';
            unset( $attrs['Comment'] );

            $oldName = (string)$oldName;
            if ( isset( $config['compatMap'][$oldName] ) )
                $name = $config['compatMap'][$oldName];
            elseif ( $config['unmappedAsLegacy'] )
                $name = 'system.legacy.' . preg_replace( '/[^a-z0-9_]+/', '_', strtolower( str_replace( '-', '_', $oldName ) ) );
            else
                return null;

            // the code that called eZAudit::writeAudit() decides between names (content-delete); tests name it
            $caller = $caller === null ? self::legacyCaller() : (string)$caller;
            $data = array( 'x' => array( 'legacy' => array( 'name' => $oldName ) ) );
            $get = function ( $key ) use ( &$attrs ) {
                if ( !array_key_exists( $key, $attrs ) )
                    return null;
                $v = $attrs[$key];
                unset( $attrs[$key] );
                return $v;
            };
            $int = function ( $v ) { return $v === null || $v === '' ? null : ( is_numeric( $v ) ? (int)$v : (string)$v ); };

            switch ( $oldName )
            {
                case 'content-delete':
                    if ( $caller === 'eZContentObject::purge' )
                        $name = 'content.object.purge';
                    elseif ( $caller === 'eZContentObject::removeThis' )
                        $name = 'content.object.remove';
                    break;
                case 'content-hide':
                    if ( substr( $caller, -strlen( 'unhideSubTree' ) ) === 'unhideSubTree' || stripos( $comment, 'unhid' ) !== false || stripos( $comment, 'reveal' ) !== false )
                        $name = 'content.node.reveal';
                    break;
                case 'order-delete':
                    if ( $caller === 'eZOrder::cleanup' )
                        $name = 'commerce.order.purge';
                    break;
                case 'user-forgotpassword':
                    if ( array_key_exists( 'Email', $attrs ) && !array_key_exists( 'UserID', $attrs ) )
                        $name = 'access.user.password.reset.request';
                    break;
            }

            // object and target from the attributes the old call sites pass
            $nodeID = $int( $get( 'Node ID' ) );
            $objectID = $int( $get( 'Object ID' ) );
            if ( $objectID === null )
                $objectID = $int( $get( 'Content object ID' ) );
            $objectName = $get( 'Content Name' );
            if ( $objectName === null )
                $objectName = $get( 'Content object name' );
            if ( $nodeID !== null )
                $data['object'] = array_filter( array( 'type' => 'node', 'id' => $nodeID, 'object_id' => $objectID, 'name' => $objectName ), array( __CLASS__, 'notNull' ) );
            elseif ( $objectID !== null )
                $data['object'] = array_filter( array( 'type' => 'object', 'id' => $objectID, 'name' => $objectName ), array( __CLASS__, 'notNull' ) );
            $roleID = $int( $get( 'Role ID' ) );
            if ( $roleID !== null )
                $data['object'] = array_filter( array( 'type' => 'role', 'id' => $roleID, 'name' => $get( 'Role name' ) ), array( __CLASS__, 'notNull' ) );
            $orderID = $int( $get( 'Order ID' ) );
            if ( $orderID !== null )
                $data['object'] = array( 'type' => 'order', 'id' => $orderID );
            $old = $int( $get( 'Old parent node ID' ) );
            $new = $int( $get( 'New parent node ID' ) );
            if ( $new !== null )
            {
                $data['target'] = array( 'type' => 'node', 'id' => $new );
                $data['before'] = array( 'parent' => $old );
                $data['after'] = array( 'parent' => $new );
            }
            $sectionID = $int( $get( 'Section ID' ) );
            if ( $sectionID !== null )
                $data['target'] = array_filter( array( 'type' => 'section', 'id' => $sectionID, 'name' => $get( 'Section name' ) ), array( __CLASS__, 'notNull' ) );
            $assignee = $int( $get( 'Assign to content object ID' ) );
            if ( $assignee !== null )
                $data['target'] = array( 'type' => 'user', 'id' => $assignee );
            $states = $get( 'Selected State ID Array' );
            if ( $states !== null )
                $data['target'] = array( 'type' => 'state', 'id' => is_array( $states ) ? implode( ',', $states ) : (string)$states );

            $userID = $int( $get( 'User id' ) );
            if ( $userID === null )
                $userID = $int( $get( 'UserID' ) );
            $login = $get( 'Login' );
            $typedLogin = $get( 'User login' );
            $email = $get( 'Email' );

            switch ( $name )
            {
                case 'access.session.login':
                    $login = $login !== null ? $login : $typedLogin;
                    $data['object'] = array_filter( array( 'type' => 'user', 'id' => $userID, 'login' => $login ), array( __CLASS__, 'notNull' ) );
                    // the user logging in, not the anonymous user the request started as
                    if ( $userID !== null )
                        $data['actor'] = array( 'user_id' => $userID, 'login' => $login, 'roles' => self::rolesOf( $userID ) );
                    break;
                case 'access.session.login.failed':
                    $known = null;
                    if ( $userID !== null )
                        $known = array( 'type' => 'user', 'id' => $userID, 'login' => self::loginOf( $userID ) );
                    elseif ( $typedLogin !== null && $typedLogin !== '' )
                        $known = self::userByLogin( stripslashes( (string)$typedLogin ) );
                    // a login typed for no account is often a password in the wrong field: only ever hashed
                    $data['object'] = $known ? array_filter( $known, array( __CLASS__, 'notNull' ) )
                                             : array( 'type' => 'user', 'attempted_login' => (string)$typedLogin );
                    $data['result'] = 'failed';
                    $data['reason'] = stripos( $comment, 'expired' ) !== false ? 'password_expired' : ( $known ? 'credentials' : 'not_found' );
                    break;
                case 'access.user.password.change':
                case 'access.user.password.change.failed':
                case 'access.user.password.reset':
                case 'access.user.password.reset.request':
                case 'access.user.password.reset.failed':
                    if ( $oldName === 'user-password-change-self' && $userID === null )
                        $userID = self::currentUserId();
                    if ( $userID !== null || $email !== null )
                        $data['object'] = array_filter( array( 'type' => 'user', 'id' => $userID, 'login' => $login, 'email' => $email ), array( __CLASS__, 'notNull' ) );
                    if ( substr( $name, -7 ) === '.failed' )
                    {
                        $data['result'] = 'refused';
                        if ( stripos( $comment, 'HashKey not found' ) !== false )
                            $data['reason'] = 'unknown_key';
                        elseif ( stripos( $comment, 'expired' ) !== false )
                            $data['reason'] = 'expired';
                        elseif ( stripos( $comment, 'Email address not found' ) !== false )
                            $data['reason'] = 'unknown_email';
                        elseif ( stripos( $comment, 'Old password incorrect' ) !== false )
                            $data['reason'] = 'credentials';
                        else
                            $data['reason'] = 'validation';
                    }
                    if ( $name === 'access.user.password.reset.request' )
                        $data['after'] = array( 'mail_sent' => true );
                    break;
                default:
                    if ( $userID !== null && !isset( $data['object'] ) )
                        $data['object'] = array_filter( array( 'type' => 'user', 'id' => $userID, 'login' => $login ), array( __CLASS__, 'notNull' ) );
            }
            if ( $attrs )
                $data['after'] = ( isset( $data['after'] ) ? $data['after'] : array() ) + array( 'legacy' => $attrs );
            if ( $caller !== '' )
                $data['x']['legacy']['caller'] = $caller;
            return self::event( $name, $data );
        }
        catch ( Throwable $e )
        {
            self::failure( $e, 'the 4.x audit name ' . (string)$oldName );
            return null;
        }
    }

    /** @return bool */
    public static function notNull( $v )
    {
        return $v !== null;
    }

    /** @return string Class::function of the code that called eZAudit::writeAudit() */
    protected static function legacyCaller()
    {
        $trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 6 );
        foreach ( $trace as $i => $frame )
        {
            if ( isset( $frame['class'], $frame['function'] ) && strcasecmp( $frame['class'], 'eZAudit' ) === 0 && $frame['function'] === 'writeAudit' )
            {
                $c = isset( $trace[$i + 1] ) ? $trace[$i + 1] : null;
                if ( !$c || !isset( $c['function'] ) )
                    return '';
                return ( isset( $c['class'] ) ? ltrim( $c['class'], '\\' ) . '::' : '' ) . $c['function'];
            }
        }
        return '';
    }

    // ------------------------------------------------------------------ flushing and the request

    /**
     * Writes the buffer (one append per channel). Called by the cleanup handler at the end of a request and by
     * the buffer limits; $final also ends open parents (result failed, reason error).
     *
     * @param bool $final
     */
    public static function flush( $final = false )
    {
        try
        {
            if ( $final )
            {
                foreach ( array_keys( self::$open ) as $id )
                    self::end( $id, array( 'result' => 'failed', 'reason' => 'error' ) );
            }
            if ( self::$buffer === null || self::$buffer->isEmpty() )
            {
                if ( $final && self::$failed )
                    self::retryFailed();
                return;
            }
            $config = expAuditConfig::get();
            $records = self::$buffer->take();
            self::$lastFlush = self::nowFloat();
            $ms = self::elapsedMs();
            $status = self::httpStatus();
            foreach ( $records as $channel => $list )
            {
                foreach ( $list as $i => $r )
                {
                    if ( isset( $r['request']['id'] ) && $r['request']['id'] === self::$requestId )
                    {
                        $list[$i]['request']['ms'] = $ms;
                        if ( $status !== null )
                            $list[$i]['request']['status'] = $status;
                    }
                }
                self::write( $channel, $list, $config, $final );
            }
            if ( $final && self::$failed )
                self::retryFailed();
        }
        catch ( Throwable $e )
        {
            self::failure( $e, 'the flush' );
        }
    }

    /** The cleanup handler: the final flush of a request or a command. */
    public static function flushFinal()
    {
        self::flush( true );
    }

    /**
     * The fatal error handler: records system.error.fatal (error reference and file:line, never the message's
     * arguments) and flushes.
     */
    public static function flushOnFatal()
    {
        try
        {
            $error = error_get_last();
            $fatal = $error && in_array( $error['type'], array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR ), true );
            if ( $fatal )
            {
                $ref = method_exists( 'eZExecution', 'errorReference' ) ? eZExecution::errorReference() : null;
                $file = isset( $error['file'] ) ? self::relativePath( $error['file'] ) : null;
                self::event( 'system.error.fatal', array(
                    'object' => array( 'type' => 'request', 'id' => self::requestId() ),
                    'result' => 'failed', 'reason' => 'error',
                    'error' => array_filter( array( 'ref' => $ref, 'where' => $file !== null ? $file . ':' . (int)$error['line'] : null ), array( __CLASS__, 'notNull' ) ) ) );
            }
        }
        catch ( Throwable $e )
        {
        }
        self::flush( true );
    }

    /** The shutdown function of this request (a last resort; the cleanup handler normally ran already). */
    public static function shutdown()
    {
        self::flush( true );
    }

    /**
     * Starts a new request: whatever the previous request left (it ended without the cleanup handler) is
     * flushed first, then the buffer, open parents, the request id, job, run and the cached actors are cleared.
     * Called from ezpKernelWeb::__construct(), and by forRequest() when REQUEST_TIME_FLOAT changes.
     */
    public static function resetRequest()
    {
        if ( self::$resetting )
            return;
        self::$resetting = true;
        try
        {
            if ( ( self::$buffer !== null && !self::$buffer->isEmpty() ) || self::$open || self::$failed )
                self::flush( true );
        }
        catch ( Throwable $e )
        {
        }
        finally
        {
            self::$resetting = false;
        }
        self::$requestKey = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '';
        self::$requestId = null;
        self::$requestFields = null;
        self::$actors = array();
        self::$buffer = new expAuditBuffer();
        self::$open = array();
        self::$parentStack = array();
        self::$job = null;
        self::$run = null;
        self::$overflowRecorded = false;
        self::$shutdownRegistered = false;
        self::$failed = array();
        self::$lastFlush = self::nowFloat();
    }

    /** Resets the request state when REQUEST_TIME_FLOAT is not the one the state belongs to. */
    protected static function forRequest()
    {
        if ( self::$resetting )
            return;
        $key = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '';
        if ( self::$requestKey !== $key || self::$buffer === null )
            self::resetRequest();
    }

    /**
     * What a test needs to look at: the request id, the buffered count, open parents, job and run.
     *
     * @return array
     */
    public static function state()
    {
        return array( 'request_key' => self::$requestKey, 'request_id' => self::$requestId,
                      'buffered' => self::$buffer ? self::$buffer->count() : 0, 'open' => count( self::$open ),
                      'job' => self::$job, 'run' => self::$run, 'actors' => count( self::$actors ),
                      'parents' => count( self::$parentStack ), 'failed' => array_sum( array_map( 'count', self::$failed ) ) );
    }

    /**
     * Forgets everything of this process (tests): request state, handlers flag, cached keys and routing.
     */
    public static function resetAll()
    {
        self::$buffer = null;
        self::$requestKey = null;
        self::resetRequest();
        self::$handlersRegistered = false;
        expAuditTaxonomy::reset();
        expAuditKeys::reset();
    }

    /** Sets a fixed clock (tests): a microtime float, or null for the real one. */
    public static function setNow( $now )
    {
        self::$now = $now;
    }

    // ------------------------------------------------------------------ writing

    /**
     * Puts a record in the buffer, or writes it at once.
     */
    protected static function store( array $record, $immediate, array $config )
    {
        $channel = $record['channel'];
        if ( $immediate )
        {
            $record['request']['ms'] = self::elapsedMs();
            $status = self::httpStatus();
            if ( $status !== null )
                $record['request']['status'] = $status;
            self::write( $channel, array( $record ), $config, false );
            return;
        }
        self::registerHandlers();
        self::$buffer->add( $channel, $record );
        if ( self::$buffer->isFull( $config['maxEvents'], $config['maxBytes'] ) )
        {
            $count = self::$buffer->count();
            $bytes = self::$buffer->bytes();
            self::flush();
            if ( !self::$overflowRecorded )
            {
                self::$overflowRecorded = true;
                self::event( 'system.audit.overflow', array( 'object' => array( 'type' => 'buffer', 'id' => self::requestId() ),
                                                             'after' => array( 'events' => $count, 'bytes' => $bytes ) ) );
            }
        }
        elseif ( $config['flushInterval'] > 0 && !self::isWebRequest() && self::nowFloat() - self::$lastFlush >= $config['flushInterval'] )
            self::flush();
    }

    /**
     * Appends records to a channel; keeps them for one more try when that fails.
     */
    protected static function write( $channel, array $records, array $config, $final )
    {
        $keys = new expAuditKeys( $config );
        try
        {
            // the keys exist before any channel is locked (the genesis needs the installation id)
            $keys->keys();
            $writer = self::writerFor( $config, $keys );
            $writer->append( $channel, $records );
        }
        catch ( Throwable $e )
        {
            self::failure( $e, "writing the audit channel $channel" );
            if ( $final )
                self::spillToErrorLog( $channel, $records );
            else
                self::$failed[$channel] = array_merge( isset( self::$failed[$channel] ) ? self::$failed[$channel] : array(), $records );
            return;
        }
        foreach ( expAuditKeys::takeCreated() as $created )
            self::event( 'system.audit.key.create', array( 'object' => array( 'type' => 'key', 'id' => $created['key_id'] ),
                                                           'after' => $created ) );
        if ( $config['checkpoints'] && !self::$checkpointing )
            self::checkpointIfDue( $config, $keys );
    }

    /** Tries the records that could not be written once more, else writes them to error.log. */
    protected static function retryFailed()
    {
        $failed = self::$failed;
        self::$failed = array();
        $config = expAuditConfig::get();
        foreach ( $failed as $channel => $records )
            self::write( $channel, $records, $config, true );
    }

    /** Records that cannot be written: one error.log line each, never lost silently. */
    protected static function spillToErrorLog( $channel, array $records )
    {
        foreach ( $records as $r )
        {
            $line = 'AUDIT-UNWRITTEN ' . $channel . ' ' . expAuditJson::encode( $r );
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeError( $line, __METHOD__ );
            else
                error_log( $line );
        }
    }

    /**
     * @param array $config
     * @param expAuditKeys $keys
     * @return expAuditWriter
     */
    public static function writerFor( array $config, ?expAuditKeys $keys = null )
    {
        $keys = $keys ?: new expAuditKeys( $config );
        $writer = new expAuditWriter( $config, $keys, function ( $name, array $data, $channel ) use ( $config ) {
            return expAudit::internalRecord( $name, $data, $channel, $config );
        } );
        // a fixed clock (tests) also decides the day file
        if ( self::$now !== null )
        {
            $now = self::$now;
            $writer->setClock( function () use ( $now ) { return gmdate( 'Y-m-d', (int)$now ); } );
        }
        return $writer;
    }

    /**
     * A record of the audit's own (file open/close, chain repair), for a given channel.
     *
     * @param string $name
     * @param array $data
     * @param string $channel
     * @param array|null $config
     * @return array
     */
    public static function internalRecord( $name, array $data, $channel, ?array $config = null )
    {
        $config = $config ?: expAuditConfig::get();
        $data += array( 'depth' => 0 );
        $record = self::build( $name, $data, $channel, 'info', $config );
        $record['request']['ms'] = self::elapsedMs();
        return $record;
    }

    /**
     * Writes system.audit.checkpoint once a day (the first write after midnight UTC): every channel's newest file,
     * last seq and last hash, signed with the active signing key.
     */
    protected static function checkpointIfDue( array $config, expAuditKeys $keys )
    {
        $state = $config['logDir'] . '/.checkpoint';
        $today = gmdate( 'Y-m-d', (int)self::nowFloat() );
        if ( @file_get_contents( $state ) === $today )
            return;
        self::checkpoint( $config, $keys, false );
    }

    /**
     * Writes a checkpoint now.
     *
     * @param array|null $config
     * @param expAuditKeys|null $keys
     * @param bool $force false: only when today's checkpoint is missing (re-checked under the lock)
     * @return string|null the checkpoint's event id
     */
    public static function checkpoint( ?array $config = null, ?expAuditKeys $keys = null, $force = true )
    {
        $config = $config ?: expAuditConfig::get();
        $keys = $keys ?: new expAuditKeys( $config );
        if ( self::$checkpointing || !$config['enabled'] )
            return null;
        self::$checkpointing = true;
        $lock = null;
        try
        {
            if ( !expAuditWriter::ensureDirectory( $config['logDir'] ) )
                return null;
            $lockFile = $config['logDir'] . '/.checkpoint.lock';
            $created = !is_file( $lockFile );
            $lock = @fopen( $lockFile, 'c' );
            if ( !$lock )
                return null;
            if ( $created )
                expAuditWriter::ownLikeParent( $lockFile, 0640 );
            if ( !flock( $lock, $force ? LOCK_EX : LOCK_EX | LOCK_NB ) )
                return null;
            $state = $config['logDir'] . '/.checkpoint';
            $today = gmdate( 'Y-m-d', (int)self::nowFloat() );
            if ( !$force && @file_get_contents( $state ) === $today )
                return null;
            $stateCreated = !is_file( $state );
            @file_put_contents( $state, $today );
            if ( $stateCreated )
                expAuditWriter::ownLikeParent( $state, 0640 );

            $writer = self::writerFor( $config, $keys );
            $channels = array();
            foreach ( $config['channels'] as $channel )
            {
                $files = expAuditWriter::channelFiles( $config['logDir'], $channel );
                if ( !$files )
                    continue;
                $file = end( $files );
                $head = $writer->tail( $config['logDir'] . '/' . $file );
                if ( $head === null )
                    continue;
                $channels[$channel] = array( 'file' => $file, 'seq' => $head['seq'], 'hash' => $head['hash'] );
            }
            if ( !$channels )
                return null;
            $after = array( 'channels' => $channels, 'key_id' => $keys->activeKeyId(), 'time' => self::timeString( self::nowMs() ) );
            $hmac = $keys->sign( $after );
            if ( $hmac !== null )
                $after['hmac'] = $hmac;
            return self::event( 'system.audit.checkpoint', array( 'object' => array( 'type' => 'checkpoint', 'id' => $today ), 'after' => $after ) );
        }
        catch ( Throwable $e )
        {
            self::failure( $e, 'the checkpoint' );
            return null;
        }
        finally
        {
            if ( $lock )
            {
                flock( $lock, LOCK_UN );
                fclose( $lock );
            }
            self::$checkpointing = false;
        }
    }

    /**
     * Registers the cleanup and fatal error handlers once per process (eZExecution keeps its handler lists for
     * the life of a Velocity worker, and runs them every request), and the shutdown function once per request
     * (Velocity runs and forgets shutdown functions at the end of each request).
     */
    protected static function registerHandlers()
    {
        if ( !expAuditConfig::get()['handlers'] )
            return;
        if ( !self::$handlersRegistered && class_exists( 'eZExecution' ) )
        {
            self::$handlersRegistered = true;
            eZExecution::addCleanupHandler( array( __CLASS__, 'flushFinal' ) );
            eZExecution::addFatalErrorHandler( array( __CLASS__, 'flushOnFatal' ) );
        }
        if ( !self::$shutdownRegistered )
        {
            self::$shutdownRegistered = true;
            register_shutdown_function( array( __CLASS__, 'shutdown' ) );
        }
    }

    // ------------------------------------------------------------------ building a record

    /**
     * Builds a record (without seq, prev, hash) with the privacy rules applied.
     *
     * @param string $name
     * @param array $data
     * @param string $channel
     * @param string $severity the registry's
     * @param array $config
     * @return array
     */
    protected static function build( $name, array $data, $channel, $severity, array $config )
    {
        $timeMs = isset( $data['time_ms'] ) ? (int)$data['time_ms'] : self::nowMs();
        $result = isset( $data['result'] ) && in_array( $data['result'], array( 'success', 'refused', 'failed' ), true ) ? $data['result'] : 'success';
        if ( isset( $data['severity'] ) && in_array( $data['severity'], expAuditTaxonomy::$severities, true ) )
            $severity = $data['severity'];
        if ( $result === 'refused' && expAuditTaxonomy::rank( $severity ) > expAuditTaxonomy::rank( 'notice' ) )
            $severity = 'notice';
        if ( $result === 'failed' && expAuditTaxonomy::rank( $severity ) > expAuditTaxonomy::rank( 'warning' ) )
            $severity = 'warning';
        $ranks = explode( '.', $name );

        $record = array(
            'v' => self::VERSION,
            'id' => isset( $data['id'] ) && preg_match( '/^[0-9A-HJKMNP-TV-Z]{26}$/', (string)$data['id'] ) ? (string)$data['id'] : self::ulid( $timeMs ),
            'name' => $name,
            'channel' => $channel,
            'time' => self::timeString( $timeMs ),
            'severity' => $severity,
            'request' => self::requestFields( $config ),
            'actor' => self::actorFields( isset( $data['actor'] ) && is_array( $data['actor'] ) ? $data['actor'] : null, $config ),
            'verb' => isset( $data['verb'] ) ? (string)$data['verb'] : ( isset( $ranks[2] ) ? $ranks[2] : $ranks[1] ),
            'object' => self::objectOrNull( isset( $data['object'] ) ? $data['object'] : null ),
            'target' => self::objectOrNull( isset( $data['target'] ) ? $data['target'] : null ),
            'before' => self::objectOrNull( isset( $data['before'] ) ? $data['before'] : null ),
            'after' => self::objectOrNull( isset( $data['after'] ) ? $data['after'] : null ),
            'result' => $result,
            'reason' => isset( $data['reason'] ) ? (string)$data['reason'] : null,
            'error' => self::objectOrNull( isset( $data['error'] ) ? $data['error'] : null ),
            'parent' => isset( $data['parent'] ) ? (string)$data['parent'] : null,
            'depth' => isset( $data['depth'] ) ? (int)$data['depth'] : 0,
            'job' => self::$job,
            'run' => self::$run,
            'x' => self::objectOrNull( isset( $data['x'] ) ? $data['x'] : null ),
        );
        $privacy = new expAuditPrivacy( $config, new expAuditKeys( $config ) );
        $record = $privacy->apply( $record );
        return self::withoutNulls( $record );
    }

    /**
     * @param mixed $value
     * @return array|null An object-shaped value (scalars become array( 'id' => value )), null when empty
     */
    protected static function objectOrNull( $value )
    {
        if ( $value === null || $value === array() || $value === '' )
            return null;
        if ( $value instanceof stdClass )
            $value = (array)$value;
        if ( !is_array( $value ) )
            return array( 'value' => is_scalar( $value ) ? $value : (string)json_encode( $value ) );
        return self::normalise( $value );
    }

    /** Floats to strings (no floats in a record), objects to arrays, at every depth. */
    protected static function normalise( array $value )
    {
        foreach ( $value as $k => $v )
        {
            if ( is_float( $v ) )
                $value[$k] = rtrim( rtrim( sprintf( '%.6F', $v ), '0' ), '.' );
            elseif ( is_object( $v ) )
                $value[$k] = $v instanceof stdClass ? self::normalise( (array)$v ) : ( method_exists( $v, '__toString' ) ? (string)$v : get_class( $v ) );
            elseif ( is_array( $v ) )
                $value[$k] = self::normalise( $v );
            elseif ( is_resource( $v ) )
                $value[$k] = 'resource';
        }
        return $value;
    }

    /** Removes null members of the record and of its request and actor parts (absent and null differ in the hash). */
    protected static function withoutNulls( array $record )
    {
        foreach ( array( 'request', 'actor' ) as $part )
        {
            if ( isset( $record[$part] ) && is_array( $record[$part] ) )
            {
                $record[$part] = array_filter( $record[$part], array( __CLASS__, 'notNull' ) );
                if ( !$record[$part] )
                    unset( $record[$part] );
            }
        }
        return array_filter( $record, array( __CLASS__, 'notNull' ) );
    }

    /**
     * The request fields of this request (without ms and status, which the write fills in).
     *
     * @param array $config
     * @return array
     */
    protected static function requestFields( array $config )
    {
        if ( self::$requestFields !== null )
        {
            $fields = self::$requestFields;
            $module = self::moduleView();
            if ( $module !== null )
                $fields['module'] = $module;
            return $fields;
        }
        if ( isset( $config['context']['request'] ) )
            return self::$requestFields = array( 'id' => self::requestId() ) + $config['context']['request'];
        $web = self::isWebRequest();
        $fields = array( 'id' => self::requestId() );
        if ( $config['requestContext'] )
        {
            $fields['siteaccess'] = class_exists( 'eZLog' ) && method_exists( 'eZLog', 'siteAccessName' ) ? eZLog::siteAccessName() : null;
            if ( $fields['siteaccess'] === '-' )
                $fields['siteaccess'] = null;
            $fields['method'] = $web ? ( isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( substr( (string)$_SERVER['REQUEST_METHOD'], 0, 16 ) ) : 'GET' ) : 'CLI';
            $fields['url'] = $web && isset( $_SERVER['REQUEST_URI'] ) ? preg_replace( '/[\x00-\x1F\x7F]+/', '', (string)$_SERVER['REQUEST_URI'] ) : null;
            $fields['engine'] = self::engine();
            $host = function_exists( 'gethostname' ) ? gethostname() : null;
            $fields['host'] = $host !== false ? $host : null;
            $fields['pid'] = getmypid();
        }
        self::$requestFields = $fields;
        $module = self::moduleView();
        if ( $module !== null )
            $fields['module'] = $module;
        return $fields;
    }

    /** @return string|null module/view of this request, when the kernel has dispatched one */
    protected static function moduleView()
    {
        if ( !self::isWebRequest() || empty( $GLOBALS['eZRequestedModuleParams']['module_name'] ) )
            return null;
        $p = $GLOBALS['eZRequestedModuleParams'];
        return substr( $p['module_name'] . '/' . ( isset( $p['function_name'] ) ? $p['function_name'] : '' ), 0, 128 );
    }

    /** @return string apache | velocity | frankenphp | cli | the SAPI name */
    public static function engine()
    {
        if ( defined( 'QBIX_WEBSERVER' ) || isset( $_SERVER['QBIX_WORKER'] ) || isset( $_SERVER['VELOCITY'] ) || class_exists( 'Q_WebServer', false ) )
            return 'velocity';
        if ( PHP_SAPI === 'frankenphp' )
            return 'frankenphp';
        if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' )
            return 'cli';
        if ( in_array( PHP_SAPI, array( 'fpm-fcgi', 'apache2handler', 'cgi-fcgi' ), true ) )
            return 'apache';
        return PHP_SAPI;
    }

    /** @return bool A web request (Velocity's workers are CLI processes serving web requests) */
    protected static function isWebRequest()
    {
        return PHP_SAPI !== 'cli' || !empty( $_SERVER['REQUEST_URI'] ) && ( !empty( $_SERVER['HTTP_HOST'] ) || !empty( $_SERVER['SERVER_NAME'] ) );
    }

    /**
     * The actor fields: the current user (cached per user id for the request), or the given actor merged over
     * the context fields.
     *
     * @param array|null $given
     * @param array $config
     * @return array
     */
    protected static function actorFields( $given, array $config )
    {
        if ( isset( $config['context']['actor'] ) )
        {
            $actor = $config['context']['actor'];
            return $given ? $given + $actor : $actor;
        }
        $base = self::contextActor();
        if ( $given )
        {
            $actor = $given + $base;
            // the session and address are the request's; the identity is the one given
            return $actor;
        }
        $userID = self::currentUserId();
        $key = $userID === null ? 0 : $userID;
        if ( !isset( self::$actors[$key] ) )
        {
            $fields = array( 'user_id' => $userID, 'login' => null, 'roles' => null );
            if ( $userID !== null && class_exists( 'eZUser' ) )
            {
                try
                {
                    $user = eZUser::currentUser();
                    if ( $user instanceof eZUser )
                    {
                        $fields['login'] = (string)$user->attribute( 'login' );
                        $fields['roles'] = array_values( array_map( 'intval', (array)$user->roleIDList() ) );
                    }
                }
                catch ( Throwable $e )
                {
                }
            }
            self::$actors[$key] = $fields;
        }
        return self::$actors[$key] + $base;
    }

    /**
     * The parts of the actor that belong to the request, not to the user: session, address, user agent, command.
     *
     * @return array
     */
    protected static function contextActor()
    {
        $fields = array( 'session' => null, 'ip' => null, 'ua' => null, 'cli' => null );
        if ( self::isWebRequest() )
        {
            $sid = function_exists( 'session_id' ) ? @session_id() : '';
            if ( is_string( $sid ) && $sid !== '' )
                $fields['session'] = $sid;
            try
            {
                $fields['ip'] = class_exists( 'eZSys' ) ? ( eZSys::clientIP() ?: null ) : ( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null );
            }
            catch ( Throwable $e )
            {
                $fields['ip'] = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
            }
            $fields['ua'] = isset( $_SERVER['HTTP_USER_AGENT'] ) && $_SERVER['HTTP_USER_AGENT'] !== '' ? (string)$_SERVER['HTTP_USER_AGENT'] : null;
        }
        else
        {
            $osUser = null;
            if ( function_exists( 'posix_geteuid' ) && function_exists( 'posix_getpwuid' ) )
            {
                $pw = @posix_getpwuid( posix_geteuid() );
                $osUser = $pw ? $pw['name'] : (string)posix_geteuid();
            }
            $fields['cli'] = array_filter( array( 'os_user' => $osUser, 'command' => self::commandLine() ), array( __CLASS__, 'notNull' ) );
        }
        return $fields;
    }

    /** @return string|null The command line, with the values of secret-looking options masked */
    protected static function commandLine()
    {
        $argv = isset( $_SERVER['argv'] ) && is_array( $_SERVER['argv'] ) ? $_SERVER['argv'] : array();
        if ( !$argv )
            return null;
        $out = array();
        foreach ( $argv as $i => $arg )
        {
            $arg = preg_replace( '/[\x00-\x1F\x7F]+/', '', (string)$arg );
            if ( preg_match( '/^(--?)([^=]+)=(.*)$/', $arg, $m ) && expAuditPrivacy::isSecretName( $m[2] ) )
                $arg = $m[1] . $m[2] . '=' . expAuditPrivacy::SECRET;
            $out[] = $arg;
        }
        return expAuditPrivacy::cut( implode( ' ', $out ), 512 );
    }

    /** @return bool Users may be looked up (eZUser is there and no fixed test context is set) */
    protected static function canLookUp()
    {
        if ( !class_exists( 'eZUser' ) )
            return false;
        $config = expAuditConfig::get();
        return !isset( $config['context'] );
    }

    /** @return int|null The current user's id, null when there is none or it cannot be known */
    protected static function currentUserId()
    {
        if ( !self::canLookUp() )
            return null;
        try
        {
            $id = eZUser::currentUserID();
            return $id ? (int)$id : null;
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    /** @return int[]|null The role ids of a user */
    protected static function rolesOf( $userID )
    {
        try
        {
            $user = self::canLookUp() ? eZUser::fetch( (int)$userID ) : null;
            return $user instanceof eZUser ? array_values( array_map( 'intval', (array)$user->roleIDList() ) ) : null;
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    /** @return string|null The login of a user id */
    protected static function loginOf( $userID )
    {
        try
        {
            $user = self::canLookUp() ? eZUser::fetch( (int)$userID ) : null;
            return $user instanceof eZUser ? (string)$user->attribute( 'login' ) : null;
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    /** @return array|null type, id, login of the account with this login */
    protected static function userByLogin( $login )
    {
        try
        {
            $user = self::canLookUp() ? eZUser::fetchByName( $login ) : null;
            return $user instanceof eZUser ? array( 'type' => 'user', 'id' => (int)$user->attribute( 'contentobject_id' ), 'login' => (string)$user->attribute( 'login' ) ) : null;
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    /**
     * A request id accepted from a trusted front proxy ([AuditRecordSettings] TrustedRequestIdHeader), only from
     * the loopback addresses.
     *
     * @return string|null
     */
    protected static function incomingRequestId()
    {
        try
        {
            $config = expAuditConfig::get();
            $name = $config['trustedRequestIdHeader'];
            if ( $name === '' )
                return null;
            $remote = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
            if ( !in_array( $remote, array( '127.0.0.1', '::1' ), true ) )
                return null;
            $key = 'HTTP_' . str_replace( '-', '_', strtoupper( $name ) );
            $value = isset( $_SERVER[$key] ) ? (string)$_SERVER[$key] : '';
            return preg_match( '/^[A-Za-z0-9._:-]{8,40}$/', $value ) ? $value : null;
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    // ------------------------------------------------------------------ time, ids, small helpers

    /** @return float */
    protected static function nowFloat()
    {
        return self::$now !== null ? (float)self::$now : microtime( true );
    }

    /** @return int Epoch milliseconds */
    protected static function nowMs()
    {
        return (int)floor( self::nowFloat() * 1000 );
    }

    /** @return int Milliseconds since the request started */
    protected static function elapsedMs()
    {
        $start = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (float)$_SERVER['REQUEST_TIME_FLOAT'] : self::nowFloat();
        return max( 0, (int)round( ( microtime( true ) - $start ) * 1000 ) );
    }

    /** @return int|null The HTTP status of this response (null on the command line) */
    protected static function httpStatus()
    {
        if ( !self::isWebRequest() || !function_exists( 'http_response_code' ) )
            return null;
        $code = @http_response_code();
        return is_int( $code ) && $code > 0 ? $code : null;
    }

    /** @return string RFC 3339 UTC with milliseconds */
    public static function timeString( $ms )
    {
        $sec = intdiv( (int)$ms, 1000 );
        return gmdate( 'Y-m-d\TH:i:s', $sec ) . sprintf( '.%03dZ', (int)$ms % 1000 );
    }

    /**
     * A ULID: 48 bits of milliseconds and 80 random bits, Crockford base 32, 26 characters, sortable by time.
     *
     * @param int|null $ms
     * @return string
     */
    public static function ulid( $ms = null )
    {
        $ms = $ms === null ? self::nowMs() : (int)$ms;
        $a = self::ULID_ALPHABET;
        $time = '';
        for ( $i = 0; $i < 10; $i++ )
        {
            $time = $a[$ms % 32] . $time;
            $ms = intdiv( $ms, 32 );
        }
        $bits = '';
        foreach ( str_split( random_bytes( 10 ) ) as $c )
            $bits .= str_pad( decbin( ord( $c ) ), 8, '0', STR_PAD_LEFT );
        $rand = '';
        for ( $i = 0; $i < 80; $i += 5 )
            $rand .= $a[bindec( substr( $bits, $i, 5 ) )];
        return $time . $rand;
    }

    /** @return string A path relative to the installation root when inside it */
    public static function relativePath( $path )
    {
        $root = expAuditConfig::root();
        return strncmp( (string)$path, $root, strlen( $root ) ) === 0 ? substr( $path, strlen( $root ) ) : (string)$path;
    }

    /** A warning through eZDebug (never thrown). */
    protected static function warn( $message )
    {
        if ( class_exists( 'eZDebug' ) )
            eZDebug::writeWarning( $message, __CLASS__ );
    }

    /**
     * A failure of the audit: written to error.log through eZDebug, never thrown to the caller.
     *
     * @param Throwable $e
     * @param string $what
     */
    protected static function failure( Throwable $e, $what )
    {
        $message = 'Audit: ' . $what . ' failed: ' . get_class( $e ) . ': ' . $e->getMessage();
        try
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeError( $message, __CLASS__ );
            else
                error_log( $message );
        }
        catch ( Throwable $ignored )
        {
        }
    }
}
