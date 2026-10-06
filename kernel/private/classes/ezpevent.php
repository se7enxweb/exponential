<?php
/**
 * File containing the ezpEvent class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * This class handles internal kernel events in eZ Publish, aka hooks.
 *
 * @internal
 * @since 4.5.0
 * @version //autogentag//
 * @package kernel
 */
class ezpEvent
{
    /**
     * Contains all registered listeners (callbacks)
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Count of listeners, used to generate listener id
     * Global to make sure it's unique.
     *
     * @var int
     */
    protected static $listenerIdNumber = 0;

    /**
     * Holds the instance of this class
     *
     * @var null|ezpEvent
     */
    protected static $instance = null;

    /**
     * Load global events from ini settings or not
     *
     * @var bool
     */
    protected $loadGlobalEvents;

    /**
     * array( name, id ) of every listener registerEventListeners() attached,
     * so the next call replaces them rather than adding a second set.
     *
     * @var array
     */
    protected $globalListenerIds = array();

    /** @var bool attach() is being called from registerEventListeners() */
    protected $recordingGlobal = false;

    /**
     * The messages about listeners this process has logged already (logOnce()). A mistake in site.ini is the same
     * on every request; under a persistent worker (Velocity) it is logged once, not once per request.
     *
     * @var array
     */
    protected static $loggedOnce = array();

    /**
     * Constructer
     * In most cases you would want to use {@see getInstance()} instead
     *
     * @param bool $loadGlobalEvents Load global events from ini settings
     */
    public function __construct( $loadGlobalEvents = true )
    {
        $this->loadGlobalEvents = $loadGlobalEvents;
    }

    /**
     * Registers the event listeners defined the site.ini files.
     */
    public function registerEventListeners()
    {
        if ( $this->loadGlobalEvents )
        {
            // Called once per web request. Under a persistent worker the
            // instance outlives the request (and a warm-up may already have
            // registered them), so the set attached last time is replaced,
            // not added to: every listener ran twice under Velocity -- the
            // form token filter wrote its meta tags into each page twice.
            foreach ( $this->globalListenerIds as $attached )
                $this->detach( $attached[0], $attached[1] );
            $this->globalListenerIds = array();
            $this->recordingGlobal = true;

            $listeners = eZINI::instance()->variable( 'Event', 'Listeners' );
            $seen = array();
            foreach ( is_array( $listeners ) ? $listeners : array() as $listener )
            {
                // $listener may be empty if some override logic has been involved
                if ( !is_string( $listener ) || trim( $listener ) === '' )
                {
                    continue;
                }

                // The format from ini is <event>@<callback>. An entry without both parts would attach a listener
                // that can never be called, or one to an event named '', so it is logged and left out. Blanks
                // around either part (a line written "content/view @ myClass::method") are dropped.
                $parts = explode( '@', trim( $listener ), 2 );
                $name = count( $parts ) === 2 ? trim( $parts[0] ) : '';
                $callback = count( $parts ) === 2 ? trim( $parts[1] ) : '';
                if ( $name === '' || $callback === '' )
                {
                    self::logOnce( "site.ini [Event] Listeners[]=$listener is not of the form <event>@<callback>; skipped", __METHOD__ );
                    continue;
                }
                if ( !self::isCallbackName( $callback ) )
                {
                    self::logOnce( "site.ini [Event] Listeners[]=$listener: '$callback' is not the name of a function or of a Class::method; skipped", __METHOD__ );
                    continue;
                }
                // The same listener listed twice (by the site and by an extension, say) is attached once: it ran
                // twice for every event
                if ( isset( $seen["$name@$callback"] ) )
                {
                    self::logOnce( "site.ini [Event] Listeners[]=$name@$callback is listed more than once; attached once", __METHOD__, 'notice' );
                    continue;
                }
                $seen["$name@$callback"] = true;
                $this->attach( $name, $callback );
            }

            // The role-aware HTTP cache attaches itself only when it is
            // switched on (httpcache.ini, disabled by default).
            // Switched off, it still tells the early exit so (contract()).
            if ( class_exists( 'ezpHttpCacheListener' ) )
            {
                if ( eZINI::instance( 'httpcache.ini' )->variable( 'HttpCacheSettings', 'Enabled' ) === 'enabled' )
                    ezpHttpCacheListener::registerListeners( $this );
                else if ( is_file( eZSys::cacheDirectory() . '/exphttpcache/contract.php' ) )
                    ezpHttpCacheListener::contract();
            }

            // The audit's ezpEvent bridge ([AuditBridgeSettings] Bridge[] of audit.ini): replaced with the rest
            // each request, so a persistent worker never records an event twice
            if ( class_exists( 'expAuditBridge' ) )
                expAuditBridge::attach( $this );
            $this->recordingGlobal = false;
        }
    }

    /**
     * Attach an event listener at run time on demand.
     *
     * @param string $name In the form "content/delete/1" or "content/delete"
     * @param array|string $listener A valid PHP callback {@see http://php.net/manual/en/language.pseudo-types.php#language.types.callback}
     * @return int Listener id, can be used to detach a listener later {@see detach()}
     */
    public function attach( $name, $listener )
    {
        $id = self::$listenerIdNumber++;
        // explode callback if static class string, workaround for PHP < 5.2.3
        if ( is_string( $listener ) && strpos( $listener, '::' ) !== false )
        {
            $listener = explode( '::', $listener );
        }

        $this->listeners[$name][$id] = $listener;
        if ( $this->recordingGlobal )
            $this->globalListenerIds[] = array( $name, $id );
        return $id;
    }

    /**
     * Detach an event listener by id given when it was added.
     *
     * @param string $name
     * @param int $id The unique id given by {@see attach()}
     * @return bool True if the listener has been correctly detached
     */
    public function detach( $name, $id )
    {
        if ( !isset( $this->listeners[$name][$id] ) )
        {
            return false;
        }

        unset( $this->listeners[$name][$id] );
        return true;
    }

    /**
     * Whether any listener is attached to the event $name. For a caller whose context is costly to gather (the
     * current user, a fetch) and that can skip it when nobody listens.
     *
     * @param string $name
     * @return bool
     */
    public function hasListeners( $name )
    {
        return !empty( $this->listeners[$name] );
    }

    /**
     * Notify all listeners of an event
     *
     * @param string $name In the form "content/delete/1", "content/delete", "content/read"
     * @param array $params The arguments for the specific event as simple array structure (not hash)
     * @return bool True if some listener where called
     */
    public function notify( $name, array $params = array() )
    {
        if ( empty( $this->listeners[$name] ) )
        {
            return false;
        }

        foreach ( $this->listeners[$name] as $listener )
        {
            if ( !self::callable( $name, $listener ) )
                continue;
            call_user_func_array( $listener, $params );
        }
        return true;
    }

    /**
     * Call all listeners of an event and allow them to filter (change) first value
     *
     * @param string $name In the form "content/delete/1", "content/delete", "content/read"
     * @param array|string|numeric $value The value you want to let listeners filter
     * @param array|string|numeric $value,... Optional additional values provided to listeners 
     * @return mixed First $value param after being filtered by filters, or unmodified if no filters
     */
    public function filter( $name, $value )
    {
        if ( empty( $this->listeners[$name] ) )
        {
            return $value;
        }

        $params = func_get_args();
        // We delete the first param, which is the name of the filter
        // in order to retrieve only params for the listener
        array_shift( $params );

        foreach ( $this->listeners[$name] as $listener )
        {
            if ( !self::callable( $name, $listener ) )
                continue;
            $params[0] = call_user_func_array( $listener, $params );
        }
        return $params[0];
    }

    /**
     * Whether a listener can be called. One that cannot (its class is missing,
     * or a long-running server's workers predate it) is logged and skipped:
     * a listener must never take the page down with it.
     */
    private static function callable( $name, $listener )
    {
        if ( is_callable( $listener ) )
            return true;
        $label = is_array( $listener ) && count( $listener ) === 2 && isset( $listener[0], $listener[1] )
            ? ( is_object( $listener[0] ) ? get_class( $listener[0] ) : (string)$listener[0] ) . '::' . ( is_scalar( $listener[1] ) ? (string)$listener[1] : gettype( $listener[1] ) )
            : ( is_string( $listener ) ? $listener : gettype( $listener ) );
        // A missing class or method stays missing for the life of the process: logged once, skipped every time
        self::logOnce( "Listener $label for event $name cannot be called (no such class, method or function); skipped", __METHOD__ );
        return false;
    }

    /**
     * Whether $callback is written as the name of a function or of a static method ("myClass::method",
     * "My\Name\Space\myClass::method"). It is not looked up: the class is autoloaded when the event is sent.
     *
     * @param string $callback
     * @return bool
     */
    protected static function isCallbackName( $callback )
    {
        $name = '[A-Za-z_\x80-\xff][A-Za-z0-9_\x80-\xff]*';
        return (bool)preg_match( "/^\\\\?$name(\\\\$name)*(::$name)?$/", $callback );
    }

    /**
     * Logs $message once in the life of the process (see $loggedOnce).
     *
     * @param string $message
     * @param string $method
     * @param string $level 'error' or 'notice'
     */
    protected static function logOnce( $message, $method, $level = 'error' )
    {
        if ( isset( self::$loggedOnce[$message] ) )
        {
            return;
        }
        if ( count( self::$loggedOnce ) > 500 )
        {
            // a listener that keeps producing new messages must not grow the worker's memory without end
            self::$loggedOnce = array();
        }
        self::$loggedOnce[$message] = true;
        if ( $level === 'notice' )
        {
            eZDebug::writeNotice( $message, $method );
        }
        else
        {
            eZDebug::writeError( $message, $method );
        }
    }

    /**
     * Gets instance
     *
     * @return ezpEvent
     */
    public static function getInstance()
    {
        if ( !self::$instance instanceof ezpEvent )
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Resets instance
     */
    public static function resetInstance()
    {
        self::$instance = null;
    }
}

?>
