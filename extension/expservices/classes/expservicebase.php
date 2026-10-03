<?php
/**
 * The base of every expservices domain class: one public static method per service, called as
 * ezjscore/call/exp<domain>::<method>[::arg...]. See doc/bc/6.0/backend_ezjscore_services.md.
 *
 * Every service is declared in static::$services: method => array(
 *   'summary' => '...', 'access' => array( 'module', 'function' ) | 'public' | 'user',
 *   'write' => bool (POST + form token, audited), 'args' => array( name => 'int|string|bool|json|list' ),
 *   'returns' => '...' ).
 * Every service returns ok()/page(); expServiceException (400 args, 401 login, 403 denied, 404 not found,
 * 409 conflict, 422 invalid) becomes { ok: false, error: { code, message } } through invoke(), which the
 * ezjscore router uses for every class extending this one.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expServiceException extends Exception
{
    /** @param string $message @param int $code 400, 401, 403, 404, 409, 422 (500 for faults) */
    public function __construct( $message = '', $code = 400, ?Throwable $previous = null )
    {
        parent::__construct( $message, (int)$code, $previous );
    }
}

abstract class expServiceBase extends ezjscServerFunctions
{
    /** @var array method => declaration, see the file comment. Overridden by every domain class. */
    public static $services = array();

    /** @var bool|null Overrides the POST and token checks (tests): true passes them */
    public static $trustRequest = null;

    /** @var array|null POST fields for tests: when set, post() reads it instead of $_POST */
    public static $postData = null;

    /** Services are never cached by the packer. */
    public static function getCacheTime( $functionName )
    {
        return -1;
    }

    // ------------------------------------------------------------------ calling

    /**
     * Calls a service and returns its envelope; exceptions become the error envelope. Used by the router.
     *
     * @param string $class domain class
     * @param string $method service
     * @param array $args positional URL arguments
     * @return array
     */
    public static function invoke( $class, $method, array $args = array() )
    {
        try
        {
            $ini = eZINI::instance( 'expservices.ini' );
            if ( $ini->hasVariable( 'Services', 'Enabled' ) && $ini->variable( 'Services', 'Enabled' ) !== 'enabled' )
                throw new expServiceException( 'The services are disabled (expservices.ini [Services] Enabled)', 403 );
            if ( !is_subclass_of( $class, 'expServiceBase' ) || !isset( $class::$services[$method] ) )
                throw new expServiceException( "No such service $class::$method", 404 );
            $result = call_user_func( array( $class, $method ), $args );
            $decl = $class::$services[$method];
            if ( !empty( $decl['write'] ) )
                $class::auditWrite( $class, $method );
            return is_array( $result ) && array_key_exists( 'ok', $result ) ? $result : self::ok( $result );
        }
        catch ( expServiceException $e )
        {
            return self::error( $e->getCode() ?: 400, $e->getMessage() );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( get_class( $e ) . ': ' . $e->getMessage(), $class . '::' . $method );
            return self::error( 500, 'The service failed: ' . $e->getMessage() );
        }
    }

    /** The error envelope. */
    public static function error( $code, $message )
    {
        return array( 'ok' => false, 'error' => array( 'code' => (int)$code, 'message' => (string)$message ) );
    }

    // ------------------------------------------------------------------ envelopes

    /** array( 'ok' => true, 'data' => ..., 'meta' => ... ) */
    protected static function ok( $data, array $meta = array() )
    {
        return array( 'ok' => true, 'data' => $data, 'meta' => $meta );
    }

    /** The paged list envelope: data is the items, meta total, offset, limit, count, has_more. */
    protected static function page( array $items, $total, $offset, $limit )
    {
        $total = (int)$total;
        $offset = (int)$offset;
        $limit = (int)$limit;
        return self::ok( array_values( $items ), array( 'total' => $total, 'offset' => $offset, 'limit' => $limit,
                                                        'count' => count( $items ), 'has_more' => $offset + count( $items ) < $total ) );
    }

    /**
     * Normalised paging from the positional arguments: array( limit, offset ), limit within
     * expservices.ini [Paging] DefaultLimit/MaxLimit.
     */
    protected static function paging( array $args, $limitIndex, $offsetIndex )
    {
        $ini = eZINI::instance( 'expservices.ini' );
        $default = $ini->hasVariable( 'Paging', 'DefaultLimit' ) ? (int)$ini->variable( 'Paging', 'DefaultLimit' ) : 25;
        $max = $ini->hasVariable( 'Paging', 'MaxLimit' ) ? (int)$ini->variable( 'Paging', 'MaxLimit' ) : 200;
        $limit = self::arg( $args, $limitIndex, 'int', $default );
        $offset = self::arg( $args, $offsetIndex, 'int', 0 );
        if ( $limit < 1 || $offset < 0 )
            throw new expServiceException( 'limit must be 1 or more and offset 0 or more', 400 );
        return array( min( $limit, $max ), $offset );
    }

    /** Slices an in-memory list the way a paged service answers: page() of the window. */
    protected static function pageOf( array $all, array $args, $limitIndex, $offsetIndex )
    {
        list( $limit, $offset ) = self::paging( $args, $limitIndex, $offsetIndex );
        $all = array_values( $all );
        return self::page( array_slice( $all, $offset, $limit ), count( $all ), $offset, $limit );
    }

    // ------------------------------------------------------------------ arguments

    /**
     * A typed positional URL argument. Without a default the argument is required (400 when missing).
     *
     * @param array $args
     * @param int $i
     * @param string $type int|string|bool|json|list
     * @param mixed $default
     */
    protected static function arg( array $args, $i, $type, $default = null )
    {
        $given = isset( $args[$i] ) && $args[$i] !== '' && $args[$i] !== null;
        if ( !$given )
        {
            if ( func_num_args() < 4 )
                throw new expServiceException( 'Argument ' . ( (int)$i + 1 ) . ' is required', 400 );
            return $default;
        }
        return self::cast( $args[$i], $type, 'Argument ' . ( (int)$i + 1 ) );
    }

    /** A typed POST field (writes), same typing as arg(). Without a default the field is required. */
    protected static function post( $name, $type, $default = null )
    {
        $source = self::$postData !== null ? self::$postData : $_POST;
        $given = isset( $source[$name] ) && $source[$name] !== '' && $source[$name] !== null;
        if ( !$given )
        {
            if ( func_num_args() < 3 )
                throw new expServiceException( "POST field '$name' is required", 400 );
            return $default;
        }
        return self::cast( $source[$name], $type, "POST field '$name'" );
    }

    /** @throws expServiceException 400 when $value is not a $type */
    protected static function cast( $value, $type, $what = 'Value' )
    {
        switch ( $type )
        {
            case 'int':
                if ( is_int( $value ) || ( is_string( $value ) && preg_match( '/^-?\d{1,18}$/', $value ) ) )
                    return (int)$value;
                throw new expServiceException( "$what must be an integer", 400 );
            case 'string':
                if ( is_array( $value ) || is_object( $value ) )
                    throw new expServiceException( "$what must be a string", 400 );
                return (string)$value;
            case 'bool':
                $v = strtolower( (string)( is_bool( $value ) ? ( $value ? '1' : '0' ) : $value ) );
                if ( in_array( $v, array( '1', 'true', 'yes', 'on' ), true ) )
                    return true;
                if ( in_array( $v, array( '0', 'false', 'no', 'off' ), true ) )
                    return false;
                throw new expServiceException( "$what must be a boolean (1/0, true/false)", 400 );
            case 'json':
                if ( is_array( $value ) )
                    return $value;
                $decoded = json_decode( (string)$value, true );
                if ( json_last_error() !== JSON_ERROR_NONE )
                    throw new expServiceException( "$what must be valid JSON: " . json_last_error_msg(), 400 );
                return $decoded;
            case 'list':
                if ( is_array( $value ) )
                    return array_values( $value );
                return array_values( array_filter( array_map( 'trim', explode( ',', (string)$value ) ), 'strlen' ) );
        }
        throw new expServiceException( "Unknown argument type '$type'", 500 );
    }

    // ------------------------------------------------------------------ access

    /**
     * Checks the declaration of a service: login and policy (with limitations) as declared in access, POST and
     * the form token for writes.
     *
     * @throws expServiceException 401 login, 403 denied or wrong method/token, 404 undeclared
     */
    protected static function guard( $method )
    {
        $services = static::$services;
        if ( !isset( $services[$method] ) )
            throw new expServiceException( 'Service ' . get_called_class() . "::$method is not declared", 404 );
        $decl = $services[$method];
        $access = isset( $decl['access'] ) ? $decl['access'] : 'user';
        $user = eZUser::currentUser();
        $registered = $user instanceof eZUser && $user->isRegistered();

        if ( $access === 'public' )
        {
            // anybody
        }
        else if ( $access === 'user' )
        {
            if ( !$registered )
                throw new expServiceException( 'You need to log in', 401 );
        }
        else if ( is_array( $access ) && count( $access ) >= 2 )
        {
            $result = $user instanceof eZUser ? $user->hasAccessTo( $access[0], $access[1] ) : array( 'accessWord' => 'no' );
            $word = isset( $result['accessWord'] ) ? $result['accessWord'] : 'no';
            if ( $word === 'no' )
            {
                if ( !$registered )
                    throw new expServiceException( 'You need to log in', 401 );
                throw new expServiceException( 'No access to ' . $access[0] . '/' . $access[1], 403 );
            }
        }
        else
            throw new expServiceException( "Service $method has no valid access declaration", 500 );

        if ( !empty( $decl['write'] ) )
            self::requireWrite();
    }

    /** @throws expServiceException unless this is a POST with the right form token */
    protected static function requireWrite()
    {
        if ( self::$trustRequest === true )
            return;
        if ( !isset( $_SERVER['REQUEST_METHOD'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' )
            throw new expServiceException( 'Changes are sent with POST', 403 );
        $ini = eZINI::instance( 'expservices.ini' );
        if ( $ini->hasVariable( 'Writes', 'RequireToken' ) && $ini->variable( 'Writes', 'RequireToken' ) !== 'enabled' )
            return;
        $expected = self::formToken();
        if ( $expected === null )
            return;
        $given = isset( $_POST['ezxform_token'] ) ? (string)$_POST['ezxform_token']
               : ( isset( $_SERVER['HTTP_X_CSRF_TOKEN'] ) ? (string)$_SERVER['HTTP_X_CSRF_TOKEN'] : '' );
        if ( $given === '' || !hash_equals( $expected, $given ) )
            throw new expServiceException( 'The form token is missing or wrong (field ezxform_token or header X-CSRF-Token; see expsession::token)', 403 );
    }

    /** The form token of this session, null when the form token protection is not active. */
    public static function formToken()
    {
        if ( !class_exists( 'ezxFormToken' ) || !ezxFormToken::isEnabled() )
            return null;
        return ezxFormToken::getToken();
    }

    /** Records the audit event of a write: service.<domain>.<method>. */
    protected static function audit( $name, array $event )
    {
        if ( !class_exists( 'expAudit' ) )
            return null;
        try
        {
            return expAudit::event( $name, $event );
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    /** The automatic audit event of a successful write, written by invoke(). */
    public static function auditWrite( $class, $method )
    {
        $ini = eZINI::instance( 'expservices.ini' );
        if ( $ini->hasVariable( 'Writes', 'Audit' ) && $ini->variable( 'Writes', 'Audit' ) !== 'enabled' )
            return null;
        $domain = strtolower( preg_replace( '/^exp|Services?$/i', '', $class ) );
        return self::audit( 'service.' . ( $domain !== '' ? $domain : 'core' ) . '.' . strtolower( $method ),
                            array( 'verb' => 'service', 'x' => array( 'class' => $class, 'method' => $method ) ) );
    }

    /**
     * Fetches a node and checks the current user's right on it.
     *
     * @param int $nodeId
     * @param string $function read, edit, remove, create, move, hide ... (eZContentObjectTreeNode::can<Function>)
     * @return eZContentObjectTreeNode
     * @throws expServiceException 404 no such node, 403 denied
     */
    protected static function node( $nodeId, $function = 'read' )
    {
        if ( !is_numeric( $nodeId ) || (int)$nodeId < 1 )
            throw new expServiceException( 'The node id must be a positive integer', 400 );
        $node = eZContentObjectTreeNode::fetch( (int)$nodeId );
        if ( !$node instanceof eZContentObjectTreeNode )
            throw new expServiceException( "Node $nodeId does not exist", 404 );
        $check = 'can' . ucfirst( $function );
        if ( !method_exists( $node, $check ) )
            throw new expServiceException( "Unknown node right '$function'", 500 );
        if ( !$node->$check() )
            throw new expServiceException( "No $function access to node $nodeId", 403 );
        return $node;
    }

    // ------------------------------------------------------------------ helpers shared by the domains

    /** Whether the current user has module/function (accessWord other than no). */
    protected static function can( $module, $function )
    {
        $user = eZUser::currentUser();
        if ( !$user instanceof eZUser )
            return false;
        $access = $user->hasAccessTo( $module, $function );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    /** A timestamp as ISO 8601, null for 0/empty. */
    protected static function iso( $timestamp )
    {
        return $timestamp ? gmdate( 'c', (int)$timestamp ) : null;
    }
}
