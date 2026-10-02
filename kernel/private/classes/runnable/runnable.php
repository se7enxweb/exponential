<?php
/**
 * File containing the Exponential\Runnable\Runnable base class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

namespace Exponential\Runnable;

/**
 * The base of everything Exponential runs from a file: CLI commands (bin/), cronjob parts (cronjobs/) and
 * module views (kernel/<module>/<view>.php, extension/<ext>/modules/<module>/<view>.php). The file keeps
 * its path and becomes one call; the work is done by a class, so it can be called from inside the system,
 * subclassed and re-implemented. Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 *   Runnable
 *     Command       a CLI script            bin/php/ezcache.php       -> Exponential\Command\Kernel\EzCache
 *     CronjobPart   a cronjob part          cronjobs/workflow.php     -> Exponential\Cronjob\Kernel\Workflow
 *     ModuleView    a module view           kernel/content/view.php   -> Exponential\View\Kernel\Content\View
 *
 * Extension points (site.ini [RunnableSettings]):
 *   Implementation[<class>]=<subclass>   create() makes the subclass instead (re-implementation)
 *   Listeners[]=<event>@<callback>       attached to ezpEvent for the events of main():
 *     runnable/<kind>/before   notify( $runnable, $class, $scope )            before run()
 *     runnable/<kind>/after    filter( $result, $runnable, $class, $scope )   after run(), may change the result
 *   <kind> is command, cronjob or view; $class is the class that runs (the re-implementation, if any).
 */
abstract class Runnable
{
    /** the first part of the event names */
    const EVENT_PREFIX = 'runnable';

    /** @var \ezpEvent|null the ezpEvent instance the [RunnableSettings] Listeners[] are attached to */
    private static $listenersAttachedTo = null;

    /** @var array "<event>@<callback>" => true, the listeners attached to it */
    private static $attachedListeners = array();

    /**
     * @var string the file this runnable was moved from (the stub that calls it), for __FILE__ and __DIR__
     *             in the moved code
     */
    protected $scriptFile = '';

    /**
     * The name of an event of main().
     *
     * @param string $kind command, cronjob or view
     * @param string $moment before or after
     * @return string e.g. runnable/view/before
     */
    public static function eventName( $kind, $moment )
    {
        return self::EVENT_PREFIX . '/' . $kind . '/' . $moment;
    }

    /**
     * Runs $work between the before and after events of its kind: listeners are notified before, and may
     * change the result after (ezpEvent::filter()). A command that ends with eZScript::shutdown() exits inside
     * run(), so only its before event fires.
     *
     * @param Runnable $runnable
     * @param string $kind command, cronjob or view
     * @param array $scope the variables handed to run() (none for a command)
     * @param \Closure $work runs the runnable, returns its result
     * @return mixed the result, as the after listeners left it
     */
    protected static function runWithEvents( Runnable $runnable, $kind, array $scope, \Closure $work )
    {
        $events = self::events();
        if ( $events === null )
            return $work();
        $class = get_class( $runnable );
        $events->notify( self::eventName( $kind, 'before' ), array( $runnable, $class, $scope ) );
        $result = $work();
        return $events->filter( self::eventName( $kind, 'after' ), $result, $runnable, $class, $scope );
    }

    /**
     * The event hub, with [RunnableSettings] Listeners[] attached once per instance. Those listeners are the
     * runnables' own: [Event] Listeners[] is registered for web requests only, and stays that way.
     *
     * @return \ezpEvent|null null where the kernel's classes are not available
     */
    protected static function events()
    {
        if ( !class_exists( 'ezpEvent' ) )
            return null;
        $events = \ezpEvent::getInstance();
        if ( self::$listenersAttachedTo !== $events )
        {
            self::$listenersAttachedTo = $events;
            self::$attachedListeners = array();
        }
        // each listener once; the settings can grow during a process (a command reads settings/override
        // before its script starts, the cronjob parts it runs see the siteaccess and extension settings too)
        foreach ( self::settingsListeners() as $listener )
        {
            list( $event, $callback ) = $listener;
            if ( isset( self::$attachedListeners["$event@$callback"] ) )
                continue;
            self::$attachedListeners["$event@$callback"] = true;
            $events->attach( $event, $callback );
        }
        // The audit's ezpEvent bridge for commands and cronjob parts (a web request attaches it in
        // ezpEvent::registerEventListeners()); once per ezpEvent instance
        if ( class_exists( 'expAuditBridge' ) && ( PHP_SAPI === 'cli' && empty( $_SERVER['REQUEST_URI'] ) ) )
            \expAuditBridge::attachOnce( $events );
        return $events;
    }

    /**
     * [RunnableSettings] Listeners[] as array( event, callback ) pairs; entries that are empty or have no "@"
     * are skipped.
     *
     * @return array
     */
    public static function settingsListeners()
    {
        $list = array();
        $ini = static::settings();
        if ( $ini === null )
            return $list;
        try
        {
            if ( !$ini->hasVariable( 'RunnableSettings', 'Listeners' ) )
                return $list;
            foreach ( (array) $ini->variable( 'RunnableSettings', 'Listeners' ) as $listener )
            {
                if ( !is_string( $listener ) || strpos( $listener, '@' ) === false )
                    continue;
                list( $event, $callback ) = explode( '@', $listener, 2 );
                if ( $event !== '' && $callback !== '' )
                    $list[] = array( $event, $callback );
            }
        }
        catch ( \Throwable $e )
        {
            // no settings yet (early in a script): no listeners
        }
        return $list;
    }

    /**
     * The runnable to use for this class: the class named in the settings when a site or an extension
     * re-implements it ([RunnableSettings] Implementation[<class>]=<subclass> in site.ini), else itself.
     *
     * @return string class name
     */
    public static function implementation()
    {
        $class = static::class;
        $ini = static::settings();
        if ( $ini !== null )
        {
            try
            {
                if ( $ini->hasVariable( 'RunnableSettings', 'Implementation' ) )
                {
                    $map = $ini->variable( 'RunnableSettings', 'Implementation' );
                    $key = ltrim( $class, '\\' );
                    if ( is_array( $map ) && isset( $map[$key] ) && is_subclass_of( $map[$key], $class ) )
                        return $map[$key];
                }
            }
            catch ( \Throwable $e )
            {
                // unreadable settings: the class itself
            }
        }
        return $class;
    }

    /** @var array rootDir => eZINI, the private reads of settings() */
    private static $privateSettings = array();

    /**
     * The settings the extension points are read from: site.ini as the kernel has set it up (eZINI::instance())
     * for a view or a cronjob part. A command's main() runs before its script has set up the settings, so it
     * reads settings/site.ini and settings/override/site.ini.append.php on its own, once per process, without
     * creating the instance the script sets up later (no siteaccess or extension settings exist at that point).
     *
     * @param string $rootDir the settings directory of the private read
     * @return \eZINI|null null where the kernel's classes are not available
     */
    public static function settings( $rootDir = 'settings' )
    {
        try
        {
            if ( !class_exists( 'eZINI' ) )
                return null;
            // the kernel's site.ini, once it exists (a view, a cronjob part, anything after a script's startup)
            if ( $rootDir === 'settings' && \eZINI::isLoaded() )
                return \eZINI::instance();
            if ( !isset( self::$privateSettings[$rootDir] ) )
                self::$privateSettings[$rootDir] = new \eZINI( 'site.ini', $rootDir, null, false, true );
            return self::$privateSettings[$rootDir];
        }
        catch ( \Throwable $e )
        {
            // no settings (early in a script, or no kernel)
        }
        return null;
    }

    /**
     * @param string $scriptFile the stub's __FILE__
     * @return static
     */
    public static function create( $scriptFile = '' )
    {
        $class = static::implementation();
        $runnable = new $class();
        $runnable->scriptFile = (string) $scriptFile;
        return $runnable;
    }

    /** @return string the original script's path (what __FILE__ was) */
    public function scriptFile()
    {
        return $this->scriptFile;
    }

    /** @return string the original script's directory (what __DIR__ was) */
    public function scriptDir()
    {
        return dirname( $this->scriptFile );
    }
}
