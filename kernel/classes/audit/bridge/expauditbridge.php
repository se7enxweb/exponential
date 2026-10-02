<?php
/**
 * The ezpEvent bridge of the audit (doc/bc/6.0/audit.md, "The ezpEvent bridge", Z3): kernel and extension events
 * that already exist (ezpEvent::getInstance()->notify() / filter()) are recorded as audit events without touching
 * their code, by INI mapping:
 *
 *   [AuditBridgeSettings]
 *   Bridge[session/regenerate]=access.session.regenerate
 *   Bridge[myext/vote]=content.myext_poll.vote
 *
 * attach() adds one listener per mapped name. ezpEvent::registerEventListeners() calls it for web requests (the
 * listeners are kept with the ones from site.ini, so a persistent Velocity worker replaces them each request
 * instead of adding a second set), and the runnables (commands, cronjob parts) call it once per ezpEvent
 * instance. The listener records the audit name with after.args, the event's arguments made scalar: numbers and
 * short strings as they are, objects as "class#id", arrays of those; the arguments of session/* events are
 * session ids and only ever recorded hashed. A filter event's value is passed through unchanged; a failure never
 * reaches the code that raised the event.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditBridge
{
    /** @var array spl_object_id of an ezpEvent => true: the runnables attached the bridge to it */
    protected static $attachedTo = array();

    /** @var array spl_object_id of an ezpEvent => list of array( event, listener id ) the bridge attached to it */
    protected static $listenerIds = array();

    /**
     * The mapping: ezpEvent name => audit name, the valid entries of [AuditBridgeSettings] Bridge[].
     *
     * @return array
     */
    public static function mapping()
    {
        try
        {
            if ( !class_exists( 'eZINI' ) )
                return array();
            $ini = eZINI::instance( 'audit.ini' );
            if ( !$ini->hasVariable( 'AuditBridgeSettings', 'Bridge' ) )
                return array();
            $map = array();
            foreach ( (array)$ini->variable( 'AuditBridgeSettings', 'Bridge' ) as $event => $name )
            {
                if ( !is_string( $event ) || $event === '' || !is_string( $name ) || $name === '' )
                    continue;
                if ( class_exists( 'expAuditTaxonomy' ) && !expAuditTaxonomy::isValidName( $name ) )
                    continue;
                $map[$event] = $name;
            }
            return $map;
        }
        catch ( Throwable $e )
        {
            return array();
        }
    }

    /**
     * Attaches one listener per mapped event (ezpEvent::registerEventListeners() for web requests, which replaces
     * them each request).
     *
     * @param ezpEvent $events
     * @return int listeners attached
     */
    public static function attach( $events )
    {
        $count = 0;
        try
        {
            // the listeners of an earlier attach to this instance go first, so an event is never recorded twice
            $key = spl_object_id( $events );
            foreach ( isset( self::$listenerIds[$key] ) ? self::$listenerIds[$key] : array() as $attached )
                $events->detach( $attached[0], $attached[1] );
            self::$listenerIds[$key] = array();
            if ( !class_exists( 'expAudit' ) || !expAudit::isEnabled() )
                return 0;
            foreach ( self::mapping() as $event => $name )
            {
                $id = $events->attach( $event, function () use ( $event, $name ) {
                    $args = func_get_args();
                    expAuditBridge::record( $event, $name, $args );
                    return $args ? $args[0] : null;
                } );
                self::$listenerIds[$key][] = array( $event, $id );
                $count++;
            }
        }
        catch ( Throwable $e )
        {
        }
        return $count;
    }

    /**
     * attach() once per ezpEvent instance (the runnables: commands and cronjob parts, which do not run
     * ezpEvent::registerEventListeners()).
     *
     * @param ezpEvent $events
     * @return int
     */
    public static function attachOnce( $events )
    {
        $key = spl_object_id( $events );
        if ( isset( self::$attachedTo[$key] ) )
            return 0;
        self::$attachedTo[$key] = true;
        return self::attach( $events );
    }

    /**
     * Records one bridged event. Never throws.
     *
     * @param string $event the ezpEvent name
     * @param string $name the audit name
     * @param array $args the event's arguments
     * @return string|null the audit event id
     */
    public static function record( $event, $name, array $args )
    {
        try
        {
            if ( !class_exists( 'expAuditHook' ) || !expAuditHook::on( $name ) )
                return null;
            $secret = strpos( (string)$event, 'session/' ) === 0;
            $scalar = array();
            foreach ( array_values( $args ) as $i => $arg )
                $scalar[] = self::scalar( $arg, $secret, 0 );
            $ranks = explode( '.', $name );
            return expAuditHook::emit( $name, array(
                'object' => array( 'type' => isset( $ranks[1] ) ? $ranks[1] : 'event', 'id' => (string)$event ),
                'after' => array( 'args' => $scalar ),
                'x' => array( 'bridge' => array( 'event' => (string)$event ) ) ) );
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    /**
     * An argument made scalar for a record.
     *
     * @param mixed $value
     * @param bool $secret hash every string (session ids)
     * @param int $depth
     * @return mixed
     */
    public static function scalar( $value, $secret = false, $depth = 0 )
    {
        if ( $value === null || is_bool( $value ) || is_int( $value ) )
            return $value;
        if ( is_float( $value ) )
            return (string)$value;
        if ( is_string( $value ) )
        {
            if ( $secret )
                return self::hash( $value );
            return strlen( $value ) > 64 ? substr( $value, 0, 64 ) . '…' : $value;
        }
        if ( is_array( $value ) )
        {
            if ( $depth >= 2 )
                return 'array(' . count( $value ) . ')';
            $out = array();
            foreach ( array_slice( $value, 0, 50, true ) as $k => $v )
                $out[$k] = self::scalar( $v, $secret, $depth + 1 );
            return $out;
        }
        if ( is_object( $value ) )
        {
            $id = null;
            foreach ( array( 'attribute' ) as $method )
            {
                if ( method_exists( $value, 'hasAttribute' ) && method_exists( $value, $method ) )
                {
                    foreach ( array( 'id', 'node_id', 'contentobject_id' ) as $key )
                    {
                        if ( $value->hasAttribute( $key ) )
                        {
                            $id = $value->attribute( $key );
                            break;
                        }
                    }
                }
            }
            return get_class( $value ) . ( $id !== null ? '#' . $id : '' );
        }
        return gettype( $value );
    }

    /**
     * The privacy option "hash" of the audit (h: + 16 hex digits of an HMAC with the installation's pseudonym
     * key), or the string "[secret]" when no key is there.
     *
     * @param string $value
     * @return string
     */
    protected static function hash( $value )
    {
        try
        {
            $config = expAuditConfig::get();
            $privacy = new expAuditPrivacy( $config, new expAuditKeys( $config ) );
            $h = $privacy->hash( (string)$value );
            return $h !== null ? $h : expAuditPrivacy::SECRET;
        }
        catch ( Throwable $e )
        {
            return '[secret]';
        }
    }
}
