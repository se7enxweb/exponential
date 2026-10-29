<?php
/**
 * File containing the expKernelServices class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The kernel service container: the static facade, and the compiled boot of the web kernel.
 *
 * For code:
 *
 *   $db = expKernelServices::get( 'db' );
 *   $site = expKernelServices::get( 'ini.site' );          // eZINI::instance( 'site.ini' )
 *   foreach ( expKernelServices::tagged( 'kernel.core' ) as $id => $service ) ...
 *
 * Services are defined in settings/kernelservices.ini ([Service_<id>] blocks; extensions add theirs in
 * extension/<name>/settings/kernelservices.ini.append.php). See doc/eng-us/bc/6.0/kernel-services.md.
 *
 * For the web kernel, with [ContainerSettings] Mode=compiled and a compiled container in
 * var/cache/kernelservices/ (exp:services:compile, the deploy, ezcache.php --clear-tag=ini):
 *
 * - bootWeb() reads the compiled index at the start of each request.
 * - The two extension activation passes of the constructor (eZExtension::activateExtensions() 'default' and
 *   'access') apply the recorded result instead of looking up 60-odd extension directories on disk, but only when
 *   the starting state is exactly the one recorded (the override directories, the settings the pass reads, no
 *   extension added at runtime). Otherwise the pass runs as always.
 * - With WorkerScope on, the INI objects of the siteaccess are kept for the worker (eZINI::attachSharedStore()):
 *   on Exponential Velocity a worker builds each settings file once instead of on every request.
 *
 * Without a compiled container, with Mode=dev or Mode=disabled, or whenever a recording does not match, the kernel
 * runs exactly as it did before the container existed.
 *
 * The compiled data is a recording of the live passes, made by expKernelServiceCompiler in a separate PHP process
 * for every siteaccess, or (AutoRebuild=enabled) by a web request of a siteaccess that has none yet.
 *
 * Not to be confused with ezpKernelWeb::getServiceContainer(), the injected container of the old legacy bridge.
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelServices
{
    /** Format of the compiled files; another one is not read. */
    const FORMAT = 1;

    /** The cache directory when kernelservices.ini does not say. */
    const DEFAULT_CACHE_DIR = 'var/cache/kernelservices';

    /** Defaults of [ContainerSettings] */
    const DEFAULT_SETTINGS = array(
        'Mode' => 'compiled',
        'CacheDir' => self::DEFAULT_CACHE_DIR,
        'AutoRebuild' => 'enabled',
        'WorkerScope' => 'auto',
    );

    /** @var bool whether the web kernel booted the container for this request */
    protected static $booted = false;

    /** @var array|null [ContainerSettings] of this request */
    protected static $settings = null;

    /** @var array|null the compiled index, when valid */
    protected static $index = null;

    /** @var array|null the compiled data of the current siteaccess, when valid */
    protected static $access = null;

    /** @var array stage => true|false: whether the recorded stage was applied in this request */
    protected static $applied = array();

    /** @var array stage => what was seen before a stage that is being recorded */
    protected static $pending = array();

    /** @var array stage => a finished recording of this request */
    protected static $recorded = array();

    /** @var bool recording for the compiler (never applies, never writes on its own) */
    protected static $compilerRecording = false;

    /** @var expKernelServiceContainer|null */
    protected static $container = null;

    /** @var array id => object set with set() for this request */
    protected static $dynamic = array();

    // ----------------------------------------------------------------------------------------------------------
    // The facade
    // ----------------------------------------------------------------------------------------------------------

    /**
     * A service by id.
     *
     * @param string $id
     * @return object
     */
    public static function get( $id )
    {
        if ( isset( self::$dynamic[$id] ) )
            return self::$dynamic[$id];
        return self::container()->get( $id );
    }

    /**
     * Whether a service of this id exists.
     *
     * @param string $id
     * @return bool
     */
    public static function has( $id )
    {
        return isset( self::$dynamic[$id] ) || self::container()->has( $id );
    }

    /**
     * The services that carry a tag, id => object.
     *
     * @param string $tag
     * @return array
     */
    public static function tagged( $tag )
    {
        return self::container()->tagged( $tag );
    }

    /**
     * Puts an object under an id for the rest of this request: the documented exception for objects that are
     * truly made at runtime. It is never kept for a worker and never compiled.
     *
     * @param string $id
     * @param object $service
     */
    public static function set( $id, $service )
    {
        if ( !is_string( $id ) || $id === '' || !is_object( $service ) )
            throw new InvalidArgumentException( 'expKernelServices::set() needs an id and an object' );
        self::$dynamic[$id] = $service;
    }

    /**
     * The container of this request: the compiled definitions of the siteaccess when there are any, else the
     * definitions read from kernelservices.ini as it stands now.
     *
     * @return expKernelServiceContainer
     */
    public static function container()
    {
        if ( self::$container === null )
        {
            $siteAccess = isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '';
            $read = expKernelServiceCompiler::definitionsFromINI( eZINI::instance( 'kernelservices.ini' ) );
            self::$container = new expKernelServiceContainer( $read['definitions'], $read['tags'], $siteAccess, false );
        }
        return self::$container;
    }

    /**
     * Forgets everything of this process: the container, the compiled data, the request state and (with $worker)
     * the worker store and the INI objects eZINI keeps for it. For tests and for code that changes the settings
     * under a running process.
     *
     * @param bool $worker
     */
    public static function reset( $worker = true )
    {
        self::$booted = false;
        self::$settings = null;
        self::$index = null;
        self::$access = null;
        self::$applied = array();
        self::$pending = array();
        self::$recorded = array();
        self::$compilerRecording = false;
        self::$container = null;
        self::$dynamic = array();
        expKernelServiceScope::clearRequestInstances();
        if ( $worker )
        {
            eZINI::releaseSharedStore();
            unset( $GLOBALS[expKernelServiceScope::WORKER_GLOBAL] );
        }
    }

    // ----------------------------------------------------------------------------------------------------------
    // Settings and state
    // ----------------------------------------------------------------------------------------------------------

    /**
     * [ContainerSettings] of kernelservices.ini, with the defaults. Read before the extensions are active: these
     * settings belong in settings/kernelservices.ini or settings/override/kernelservices.ini.append.php.
     *
     * @return array
     */
    public static function settings()
    {
        if ( self::$settings === null )
        {
            $settings = self::DEFAULT_SETTINGS;
            $ini = eZINI::instance( 'kernelservices.ini' );
            if ( $ini->hasGroup( 'ContainerSettings' ) )
            {
                foreach ( array_keys( $settings ) as $name )
                {
                    if ( $ini->hasVariable( 'ContainerSettings', $name ) )
                        $settings[$name] = trim( (string)$ini->variable( 'ContainerSettings', $name ) );
                }
            }
            if ( !in_array( $settings['Mode'], array( 'compiled', 'dev', 'disabled' ), true ) )
                $settings['Mode'] = 'disabled';
            self::$settings = $settings;
        }
        return self::$settings;
    }

    /**
     * compiled, dev or disabled.
     *
     * @return string
     */
    public static function mode()
    {
        $settings = self::settings();
        return $settings['Mode'];
    }

    /**
     * The directory of the compiled container, absolute, without a trailing slash.
     *
     * @return string
     */
    public static function cacheDirectory()
    {
        $settings = self::settings();
        $dir = rtrim( $settings['CacheDir'] !== '' ? $settings['CacheDir'] : self::DEFAULT_CACHE_DIR, '/' );
        if ( $dir[0] !== '/' )
            $dir = self::installationRoot() . '/' . $dir;
        return $dir;
    }

    /**
     * Whether the INI objects of a siteaccess are kept for the worker: WorkerScope=enabled, or auto (the default)
     * when the INI cache does not check file times either (EZP_INI_FILEMTIME_CHECK false in config.php). Then a
     * kept object is exactly what loading the file again would give, and an INI change needs what it needs today:
     * a cache clear or a deploy.
     *
     * @return bool
     */
    public static function workerScopeEnabled()
    {
        $settings = self::settings();
        if ( $settings['WorkerScope'] === 'enabled' )
            return true;
        if ( $settings['WorkerScope'] === 'auto' )
            return defined( 'EZP_INI_FILEMTIME_CHECK' ) && EZP_INI_FILEMTIME_CHECK === false;
        return false;
    }

    /**
     * Whether the web kernel booted the container for this request.
     *
     * @return bool
     */
    public static function isBooted()
    {
        return self::$booted;
    }

    /**
     * What happened in this request, for the console, the debug output and tests.
     *
     * @return array
     */
    public static function status()
    {
        return array(
            'booted' => self::$booted,
            'mode' => self::$settings !== null ? self::$settings['Mode'] : null,
            'index' => self::$index !== null,
            'generation' => self::$index !== null ? self::$index['generation'] : null,
            'siteaccess' => self::$access !== null ? self::$access['siteaccess'] : null,
            'applied' => self::$applied,
            'recorded' => array_keys( self::$recorded ),
            'ini_state' => eZINI::sharedStoreState(),
        );
    }

    /**
     * The compiled index of this request, or null.
     *
     * @return array|null
     */
    public static function compiledIndex()
    {
        return self::$index;
    }

    /**
     * The compiled data of this request's siteaccess, or null.
     *
     * @return array|null
     */
    public static function compiledSiteAccess()
    {
        return self::$access;
    }

    // ----------------------------------------------------------------------------------------------------------
    // The web kernel's boot
    // ----------------------------------------------------------------------------------------------------------

    /**
     * Called by the ezpKernelWeb constructor before it reads any settings of the request. Returns whether the
     * container takes part in this request's boot.
     *
     * @param array $kernelSettings the constructor's settings (injected settings turn the container off)
     * @return bool
     */
    public static function bootWeb( array $kernelSettings = array() )
    {
        self::$booted = false;
        self::$settings = null;
        self::$index = null;
        self::$access = null;
        self::$applied = array();
        self::$pending = array();
        self::$recorded = array();
        self::$container = null;
        self::$dynamic = array();

        // Settings injected by a caller are read at variable() time and can change what the passes do: the
        // recordings know nothing of them
        if ( !empty( $kernelSettings['injected-settings'] ) || !empty( $kernelSettings['injected-merge-settings'] ) )
            return false;
        if ( isset( $GLOBALS['eZActiveExtensions'] ) )
            return false;

        // The compiler records whatever the mode: it is what exp:services:compile was asked to do
        if ( self::$compilerRecording )
        {
            self::$booted = true;
            return true;
        }

        $mode = self::mode();
        if ( $mode !== 'compiled' )
            return false;

        self::$booted = true;
        self::$index = self::readCompiled( self::cacheDirectory() . '/index.php', null );
        if ( self::$index === null || !self::workerScopeEnabled() )
            return true;

        // The settings files read before the extensions are known (i18n.ini, site.ini, error.ini) are the same for
        // every request of this generation
        if ( eZINI::globalOverrideDirs( false ) === eZINI::defaultOverrideDirs() )
        {
            $worker = expKernelServiceScope::worker();
            $worker->sync( self::$index['generation'] );
            eZINI::attachSharedStore( $worker, 'boot|' . self::$index['generation'] );
        }
        return true;
    }

    /**
     * Called by eZExtension::activateExtensions() for the global site.ini when the container is booted, before the
     * pass. Applies the recorded result and returns true when the state before the pass is exactly the recorded
     * one; returns false otherwise (the pass then runs as always, and is recorded when a recording is wanted).
     *
     * @param string $stage 'default' or 'access'
     * @return bool
     */
    public static function applyExtensionStage( $stage )
    {
        $ini = eZINI::instance();
        $before = eZINI::globalOverrideDirs( false );
        $fingerprint = self::fingerprint( $stage, $ini );

        $data = null;
        $caches = null;
        if ( !self::$compilerRecording )
        {
            if ( $stage === 'default' && self::$index !== null )
            {
                $data = self::$index['default'];
                $caches = self::$index['extensions'];
            }
            else if ( $stage === 'access' && self::$index !== null )
            {
                self::$access = self::readSiteAccess( $fingerprint['access'] );
                if ( self::$access !== null )
                {
                    $data = self::$access['stage'];
                    $caches = self::$access['extensions'];
                }
            }
        }

        if ( $data !== null && $data['before'] === $before && $data['fingerprint'] === $fingerprint )
        {
            eZExtension::seedCaches( $caches );
            if ( $data['after'] !== $before )
            {
                $ini->setOverrideDirs( $data['after'] );
                $ini->load();
            }
            self::$applied[$stage] = true;
            return true;
        }

        self::$applied[$stage] = false;
        if ( $stage === 'access' )
            self::$access = null;
        // A web request records only what has no compiled file yet. A file that exists but does not match this
        // request is left alone: another kind of request may match it, and overwriting it on every mismatch could
        // make two kinds of requests write in turn. exp:services:compile is what replaces it.
        if ( self::recordingWanted() && ( self::$compilerRecording || $data === null ) )
            self::$pending[$stage] = array( 'before' => $before, 'fingerprint' => $fingerprint );
        return false;
    }

    /**
     * Called by eZExtension::activateExtensions() after a pass that ran live: records its result when a recording
     * was started for it, and after the access pass writes the recording (web, AutoRebuild) or keeps it for the
     * compiler.
     *
     * @param string $stage
     */
    public static function recordExtensionStage( $stage )
    {
        if ( !isset( self::$pending[$stage] ) )
            return;
        $after = eZINI::globalOverrideDirs( false );
        self::$recorded[$stage] = self::$pending[$stage] + array( 'after' => $after, 'caches' => eZExtension::exportCaches() );
        unset( self::$pending[$stage] );

        if ( $stage === 'access' && !self::$compilerRecording )
            self::writeRequestRecording();
    }

    /**
     * Called by the ezpKernelWeb constructor right after eZINI::resetAllInstances( false ): from here on the
     * settings files of this siteaccess come from the worker store (when the access pass was applied from the
     * compiled container and WorkerScope is on), and the container of the request has the compiled definitions.
     */
    public static function enterSiteAccess()
    {
        if ( !self::$booted || self::$access === null || empty( self::$applied['access'] ) )
            return;

        self::$container = new expKernelServiceContainer( self::$access['services'], self::$access['tags'],
                                                          self::$access['siteaccess'], true );
        if ( !self::workerScopeEnabled() )
            return;
        $worker = expKernelServiceScope::worker();
        $worker->sync( self::$index['generation'] );
        eZINI::attachSharedStore( $worker, 'sa:' . self::$access['siteaccess'] . '|' . self::$access['stage']['after_hash']
                                           . '|' . self::$index['generation'] );
    }

    // ----------------------------------------------------------------------------------------------------------
    // Recording (expKernelServiceCompiler, and AutoRebuild)
    // ----------------------------------------------------------------------------------------------------------

    /**
     * Starts recording for the compiler: in this process, every booted kernel records both passes and never applies
     * or writes anything itself (takeRecording()).
     */
    public static function startCompilerRecording()
    {
        self::$compilerRecording = true;
    }

    /**
     * The finished recordings of the last kernel boot (compiler), stage => recording, and forgets them.
     *
     * @return array
     */
    public static function takeRecording()
    {
        $recorded = self::$recorded;
        self::$recorded = array();
        self::$pending = array();
        return $recorded;
    }

    /**
     * Whether this request records the passes it runs live.
     *
     * @return bool
     */
    protected static function recordingWanted()
    {
        if ( self::$compilerRecording )
            return true;
        if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' )
            return false;
        $settings = self::settings();
        return $settings['AutoRebuild'] === 'enabled';
    }

    /**
     * After an access pass that ran live in a web request with AutoRebuild: writes what was recorded, so the next
     * request of this siteaccess applies it. A new index (a new generation) only when the default pass was
     * recorded too, i.e. when there was no index or it did not match.
     */
    protected static function writeRequestRecording()
    {
        if ( !isset( self::$recorded['access'] ) )
            return;
        $siteAccess = self::$recorded['access']['fingerprint']['access'];
        if ( !is_string( $siteAccess ) || !self::validSiteAccessName( $siteAccess ) )
            return;

        try
        {
            $definitions = expKernelServiceCompiler::definitionsFromINI( eZINI::create( 'kernelservices.ini' ) );
            $index = self::$index;
            if ( isset( self::$recorded['default'] ) )
            {
                // another request (or a compile) wrote one in the meantime: its generation is not known here
                clearstatcache( true, self::cacheDirectory() . '/index.php' );
                if ( file_exists( self::cacheDirectory() . '/index.php' ) )
                    return;
                $index = expKernelServiceCompiler::buildIndex( self::$recorded['default'], array( $siteAccess ), 'request' );
                if ( !expKernelServiceCompiler::writeCompiledFile( self::cacheDirectory() . '/index.php', $index ) )
                    return;
            }
            if ( $index === null )
                return;
            $data = expKernelServiceCompiler::buildSiteAccess( $siteAccess, self::$recorded['access'], $definitions,
                                                                $index['generation'], 'request' );
            expKernelServiceCompiler::writeCompiledFile( self::cacheDirectory() . '/' . $siteAccess . '.php', $data );
        }
        catch ( Exception $e )
        {
            eZDebug::writeWarning( 'Could not record the kernel services of this siteaccess: ' . $e->getMessage(), __METHOD__ );
        }
    }

    // ----------------------------------------------------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------------------------------------------------

    /**
     * What a pass reads besides the override directories: the settings of [ExtensionSettings] it depends on,
     * the current siteaccess, and for the access pass ActiveAccessExtensions.
     *
     * @param string $stage
     * @param eZINI $ini the global site.ini
     * @return array
     */
    public static function fingerprint( $stage, eZINI $ini )
    {
        $values = array();
        foreach ( array( 'ActiveExtensions', 'ExtensionDirectory', 'AdditionalExtensionDirectories', 'ExtensionOrdering' ) as $name )
            $values[$name] = $ini->hasVariable( 'ExtensionSettings', $name ) ? $ini->variable( 'ExtensionSettings', $name ) : null;
        if ( $stage === 'access' )
            $values['ActiveAccessExtensions'] = $ini->hasVariable( 'ExtensionSettings', 'ActiveAccessExtensions' )
                                              ? $ini->variable( 'ExtensionSettings', 'ActiveAccessExtensions' ) : null;
        return array(
            'stage' => $stage,
            'access' => isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : false,
            'settings' => $values,
        );
    }

    /**
     * The compiled data of one siteaccess, when it belongs to the current index.
     *
     * @param string|false $siteAccess
     * @return array|null
     */
    protected static function readSiteAccess( $siteAccess )
    {
        if ( !is_string( $siteAccess ) || !self::validSiteAccessName( $siteAccess ) || self::$index === null )
            return null;
        $data = self::readCompiled( self::cacheDirectory() . '/' . $siteAccess . '.php', self::$index['generation'] );
        if ( $data === null || !isset( $data['siteaccess'] ) || $data['siteaccess'] !== $siteAccess )
            return null;
        return $data;
    }

    /**
     * Reads a compiled file. Returns null when it is missing, of another format or generation, or not the file
     * that was written last (its time differs from the stamp inside: an opcode cache that has not seen the new
     * file yet is told to forget it, and it is read once more).
     *
     * @param string $file
     * @param string|null $generation the generation it must belong to (null: any, for the index)
     * @return array|null
     */
    public static function readCompiled( $file, $generation )
    {
        clearstatcache( true, $file );
        $mtime = @filemtime( $file );
        if ( $mtime === false )
            return null;
        $data = include $file;
        if ( is_array( $data ) && isset( $data['stamp'] ) && $data['stamp'] !== $mtime && function_exists( 'opcache_invalidate' ) )
        {
            opcache_invalidate( $file, true );
            $data = include $file;
        }
        if ( !is_array( $data ) || !isset( $data['format'], $data['stamp'], $data['generation'] )
             || $data['format'] !== self::FORMAT || $data['stamp'] !== $mtime )
            return null;
        if ( $generation !== null && $data['generation'] !== $generation )
            return null;
        return $data;
    }

    /**
     * Whether a siteaccess name can be a file name of the compiled container.
     *
     * @param string $name
     * @return bool
     */
    public static function validSiteAccessName( $name )
    {
        return is_string( $name ) && $name !== 'index' && preg_match( '/^[A-Za-z0-9_][A-Za-z0-9_-]{0,63}$/', $name ) === 1;
    }

    /**
     * The installation root, without a trailing slash.
     *
     * @return string
     */
    public static function installationRoot()
    {
        if ( defined( 'EXP_ROOT_DIR' ) )
            return rtrim( EXP_ROOT_DIR, '/' );
        return rtrim( dirname( __DIR__, 2 ), '/' );
    }
}
