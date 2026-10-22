<?php
/**
 * File containing the expKernelServiceScope class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The scopes of the kernel service container (expKernelServices), and the store of the worker scope.
 *
 * - worker: made once and shared for the life of the process. On Apache/PHP-FPM that is one request. On a
 *   persistent worker (Exponential Velocity) it is every request the worker serves: the store is one object in the
 *   global named by WORKER_GLOBAL, which expVelocity::DEFAULT_KEEP_GLOBALS keeps across requests. Instances are kept
 *   per siteaccess. A worker-scoped object must never hold the state of one request (visitor, session, URI).
 * - request: made again for each request. resetRequest() drops them at the start of every request.
 * - prototype: a new object each time it is asked for.
 *
 * resetRequest() is called once at the start of each web request, by the ezpKernelWeb constructor. It runs the
 * kernel's request reset hooks (the seven resets the constructor used to list by hand, in the same order, each only
 * when its class is there), then the hooks registered with onRequestReset(), then forgets the request-scoped
 * services.
 *
 * An instance of this class is the worker store itself: its public arrays are read and written by
 * expKernelServices, expKernelServiceContainer and eZINI (attachSharedStore()).
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelServiceScope
{
    const WORKER = 'worker';
    const REQUEST = 'request';
    const PROTOTYPE = 'prototype';

    /**
     * The global that holds the worker store; listed in expVelocity::DEFAULT_KEEP_GLOBALS. Nothing request-scoped
     * may ever be put there.
     */
    const WORKER_GLOBAL = 'expKernelServicesWorker';

    /**
     * The kernel's own request resets, in the order the web kernel always ran them: name => array( class, method ).
     * Each runs only when its class (and method) exists, as before.
     */
    const DEFAULT_RESET_HOOKS = array(
        // the query cache's memo, settings and state view start empty, also in a persistent worker
        'querycache'  => array( 'eZDBQueryCache', 'resetRequest' ),
        'sqlprofile'  => array( 'eZDBInterface', 'resetSQLProfile' ),
        // the Content Security Policy nonce of the request before is not reused
        'csp'         => array( 'expContentSecurityPolicy', 'reset' ),
        // the audit: what a previous request left is flushed, then its buffer, request id, open parent events and
        // cached actors are cleared (doc/eng-us/bc/6.0/audit.md, "Buffering and flushing")
        'audit'       => array( 'expAudit', 'resetRequest' ),
        // what the debug bar's Rendering tab shows of the previous request
        'renderstats' => array( 'expRenderStats', 'resetRequest' ),
        // the request log (404.log, security.log): the reason and the verdict of the previous request are gone
        'requestlog'  => array( 'expRequestLog', 'reset' ),
        // and the classifier is built again from this request's siteaccess settings when it is first asked
        'classifier'  => array( 'expRequestClassifier', 'reset' ),
    );

    /**
     * INI objects of the worker scope: [state][instance key] => eZINI (see eZINI::attachSharedStore()).
     *
     * @var array
     */
    public $ini = array();

    /**
     * Worker-scoped services: [siteaccess][service id] => object.
     *
     * @var array
     */
    public $services = array();

    /**
     * Request reset hooks registered with onRequestReset(): name => callable. Kept with the worker, so a hook
     * registered during one request runs at the start of the next one on a persistent worker too.
     *
     * @var array
     */
    public $resetHooks = array();

    /**
     * The generation of the compiled container the stored objects belong to; another generation empties the store.
     *
     * @var string|null
     */
    public $generation = null;

    /**
     * Requests this worker store has seen (resetRequest() calls).
     *
     * @var int
     */
    public $requests = 0;

    /**
     * Request-scoped services of this request: [service id] => object.
     *
     * @var array
     */
    protected static $requestInstances = array();

    /**
     * The worker store, created on first use.
     *
     * @return expKernelServiceScope
     */
    public static function worker()
    {
        if ( !isset( $GLOBALS[self::WORKER_GLOBAL] ) || !$GLOBALS[self::WORKER_GLOBAL] instanceof expKernelServiceScope )
            $GLOBALS[self::WORKER_GLOBAL] = new expKernelServiceScope();
        return $GLOBALS[self::WORKER_GLOBAL];
    }

    /**
     * Whether a worker store exists in this process (without creating one).
     *
     * @return bool
     */
    public static function hasWorker()
    {
        return isset( $GLOBALS[self::WORKER_GLOBAL] ) && $GLOBALS[self::WORKER_GLOBAL] instanceof expKernelServiceScope;
    }

    /**
     * Makes the store belong to a generation of the compiled container: another one than before empties it, so no
     * object built under older settings survives a compile or an INI cache clear.
     *
     * @param string|null $generation
     * @return bool true when the store was emptied
     */
    public function sync( $generation )
    {
        if ( $this->generation === $generation )
            return false;
        $this->ini = array();
        $this->services = array();
        $this->generation = $generation;
        return true;
    }

    /**
     * Empties the store (INI objects and services of every siteaccess); registered hooks stay.
     */
    public function flush()
    {
        $this->ini = array();
        $this->services = array();
    }

    /**
     * Starts a new request: runs the kernel's request reset hooks, then the registered ones, and forgets the
     * request-scoped services. Called by the ezpKernelWeb constructor; the behavior of the seven kernel resets is
     * exactly what the constructor did before.
     */
    public static function resetRequest()
    {
        foreach ( self::DEFAULT_RESET_HOOKS as $hook )
        {
            if ( class_exists( $hook[0] ) && method_exists( $hook[0], $hook[1] ) )
                call_user_func( $hook );
        }

        if ( self::hasWorker() )
        {
            $worker = self::worker();
            $worker->requests++;
            foreach ( $worker->resetHooks as $name => $hook )
            {
                if ( is_callable( $hook ) )
                    call_user_func( $hook );
            }
        }

        self::$requestInstances = array();
    }

    /**
     * Registers a request reset hook, run at the start of every later request by resetRequest(). Registering the
     * same name again replaces the hook, so code that registers on every request does not pile them up.
     *
     * @param string $name
     * @param callable $hook
     */
    public static function onRequestReset( $name, $hook )
    {
        if ( !is_string( $name ) || $name === '' || !is_callable( $hook ) )
            throw new InvalidArgumentException( 'onRequestReset() needs a name and a callable' );
        self::worker()->resetHooks[$name] = $hook;
    }

    /**
     * Removes a registered request reset hook.
     *
     * @param string $name
     */
    public static function removeRequestReset( $name )
    {
        if ( self::hasWorker() )
            unset( self::worker()->resetHooks[$name] );
    }

    /**
     * The names of the reset hooks that run at the start of a request: the kernel's, then the registered ones.
     *
     * @return array
     */
    public static function resetHookNames()
    {
        $names = array_keys( self::DEFAULT_RESET_HOOKS );
        if ( self::hasWorker() )
            $names = array_merge( $names, array_keys( self::worker()->resetHooks ) );
        return $names;
    }

    /**
     * A request-scoped service of this request, or null.
     *
     * @param string $id
     * @return object|null
     */
    public static function requestInstance( $id )
    {
        return isset( self::$requestInstances[$id] ) ? self::$requestInstances[$id] : null;
    }

    /**
     * Keeps a request-scoped service for the rest of this request.
     *
     * @param string $id
     * @param object $instance
     */
    public static function setRequestInstance( $id, $instance )
    {
        self::$requestInstances[$id] = $instance;
    }

    /**
     * Forgets the request-scoped services (tests; resetRequest() does it at every request).
     */
    public static function clearRequestInstances()
    {
        self::$requestInstances = array();
    }

    /**
     * Whether a scope name is one of worker, request or prototype.
     *
     * @param string $scope
     * @return bool
     */
    public static function isValid( $scope )
    {
        return in_array( $scope, array( self::WORKER, self::REQUEST, self::PROTOTYPE ), true );
    }
}
