<?php
/**
 * File containing the ezpKernelRest class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

class ezpKernelRest implements ezpKernelHandler
{
    /**
     * Indicates is request has been properly initialized
     *
     * @var bool
     */
    protected $isInitialized = false;

    /**
     * Hash of internal settings
     *
     * @var array
     */
    protected $settings;

    /**
     * Current siteaccess data
     *
     * @var array
     */
    protected $access;

    /**
     * @var eZURI
     */
    protected $uri;

    /**
     * Custom ZetaComponents MvcTools ResponseWriter class name.
     * Used to get a usable Response from the REST API's controllers.
     *
     * Must reference an implementation of ezpRestHttpResponseWriter.
     *
     * @var string
     */
    private $responseWriterClass;

    private static $response;

    /**
     * @param array $settings
     * @param null $responseWriterClass Name of the ezpRestHttpResponseWriter implementation to use during request
     */
    public function __construct( array $settings = array(), $responseWriterClass = null )
    {
        $this->responseWriterClass = $responseWriterClass;

        if ( isset( $settings['injected-settings'] ) )
        {
            $injectedSettings = array();
            foreach ( $settings['injected-settings'] as $keySetting => $injectedSetting )
            {
                list( $file, $section, $setting ) = explode( '/', $keySetting );
                $injectedSettings[$file][$section][$setting] = $injectedSetting;
            }
            // Those settings override anything else in local .ini files and their overrides
            eZINI::injectSettings( $injectedSettings );
        }
        if ( isset( $settings['injected-merge-settings'] ) )
        {
            $injectedSettings = array();
            foreach ( $settings['injected-merge-settings'] as $keySetting => $injectedSetting )
            {
                list( $file, $section, $setting ) = explode( '/', $keySetting );
                $injectedSettings[$file][$section][$setting] = $injectedSetting;
            }
            // Those settings override anything else in local .ini files and their overrides
            eZINI::injectMergeSettings( $injectedSettings );
        }
        $this->settings = $settings + array(
            'use-cache-headers'         => true,
            'max-age'                   => 86400,
            'siteaccess'                => null,
            'use-exceptions'            => false
        );
        unset( $settings, $injectedSettings, $file, $section, $setting, $keySetting, $injectedSetting );

        // lazy loaded database driver. Once only: the autoload map points at the
        // same file, and a persistent worker constructs this kernel once per request.
        if ( !class_exists( 'ezpRestDbConfig', false ) )
        {
            require_once __DIR__ . '/lazy.php';
        }
        // Every time: such a worker also resets ezcBaseInit's callback map.
        ezpRestDbConfig::registerCallbacks();

        $this->setUseExceptions( $this->settings['use-exceptions'] );

        // Tweaks ini filetime checks if not defined!
        // This makes ini system not check modified time so
        // that index_treemenu.php can assume that index.php does
        // this regular enough, set in config.php to override.
        if ( !defined( 'EZP_INI_FILEMTIME_CHECK' ) )
        {
            define( 'EZP_INI_FILEMTIME_CHECK', false );
        }

        eZExecution::addFatalErrorHandler(
            function ()
            {
                if ( !headers_sent() )
                {
                    // http_response_code() writes the request's protocol under every SAPI;
                    // SERVER_PROTOCOL is not set on the command line
                    http_response_code( 500 );
                }
            }
        );
        eZDebug::setHandleType( eZDebug::HANDLE_FROM_PHP );

        // Trick to get eZSys working with a script other than index.php (while index.php still used in generated URLs):
        $_SERVER['SCRIPT_FILENAME'] = str_replace( '/index_rest.php', '/index.php', $_SERVER['SCRIPT_FILENAME'] );
        $_SERVER['PHP_SELF'] = str_replace( '/index_rest.php', '/index.php', $_SERVER['PHP_SELF'] );

        // The kernel service container (settings/kernelservices.ini), booted as the web kernel boots it: with a
        // compiled container the two extension activation passes below apply what was recorded for this installation
        // instead of reading the settings directory of every extension on every request (most of the start of a
        // request: about 14 of 26 ms with alpha's 90 extensions). Without a compiled container, with another Mode, or
        // when this request does not start from exactly the recorded state, the passes run as they always did.
        if ( class_exists( 'expKernelServices' ) )
            expKernelServices::bootWeb( $this->settings );

        $ini = eZINI::instance();
        $timezone = $ini->variable( 'TimeZoneSettings', 'TimeZone' );
        if ( $timezone )
        {
            putenv( "TZ=$timezone" );
        }

        eZDebug::setHandleType( eZDebug::HANDLE_NONE );
        $GLOBALS['eZGlobalRequestURI'] = eZSys::serverVariable( 'REQUEST_URI' );
        $ini = eZINI::instance();
        eZSys::init( 'index_rest.php', $ini->variable( 'SiteAccessSettings', 'ForceVirtualHost' ) == 'true' );
        $uri = eZURI::instance( eZSys::requestURI() );
        $GLOBALS['eZRequestedURI'] = $uri;

        // load extensions
        eZExtension::activateExtensions( 'default' );

        require_once __DIR__ . '/restkernel_functions.php';
        // Set every time: a persistent worker may have run another front controller before.
        eZDebugSettingsMode( 'rest' );

        // set siteaccess from X-Siteaccess header if given and exists
        if ( isset( $_SERVER['HTTP_X_SITEACCESS'] ) && eZSiteAccess::exists( $_SERVER['HTTP_X_SITEACCESS'] ) )
        {
            $access = array( 'name' => $_SERVER['HTTP_X_SITEACCESS'], 'type' => eZSiteAccess::TYPE_STATIC );
        }
        else
        {
            $access = eZSiteAccess::match( $uri, eZSys::hostname(), eZSys::serverPort(), eZSys::indexFile() );
        }

        eZSiteAccess::change( $access );

        // load siteaccess extensions
        eZExtension::activateExtensions( 'access' );

        // Now that all extensions are activated and siteaccess has been changed, reset
        // all eZINI instances as they may not take into account siteaccess specific settings.
        eZINI::resetAllInstances( false );
        // With the compiled container applied: the container of the request has the compiled definitions, and the
        // settings files of this siteaccess come from the worker scope, as in the web kernel
        if ( class_exists( 'expKernelServices', false ) )
            expKernelServices::enterSiteAccess();

        // The listeners of site.ini [Event] Listeners[], as in the web kernel: without this none of them ran for REST
        ezpEvent::getInstance()->registerEventListeners();

        if( ezpRestDebug::isDebugEnabled() )
        {
            $debug = ezpRestDebug::getInstance();
            $debug->updateDebugSettings();
        }
    }

    /**
     * @return mixed
     */
    public static function getResponse()
    {
        return self::$response;
    }

    /**
     * @param mixed $response
     */
    public static function setResponse( $response )
    {
        self::$response = $response;
    }

    /**
     * Execution point for controller actions.
     * Returns false if not supported
     *
     * @return ezpKernelResult
     */
    public function run()
    {
        // CORS (rest.ini [CorsSettings], off by default): a preflight is answered here and the request is not run;
        // an actual request gets its Access-Control-* headers before the response writer sends the rest.
        if ( expRestCors::fromIni( eZINI::instance( 'rest.ini' ) )->handle( $_SERVER ) )
        {
            $this->shutdown();
            return new ezpKernelResult( '', array( 'cors_preflight' => true ) );
        }

        $mvcConfig = new ezpMvcConfiguration( $this->responseWriterClass );
        $frontController = new ezpMvcConfigurableDispatcher( $mvcConfig );
        $result = $frontController->run();
        $this->shutdown();
        return $result;
    }

    /**
     * Not supported by ezpKernelTreeMenu
     *
     * @throws \RuntimeException
     */
    public function runCallback( \Closure $callback, $postReinitialize = true )
    {
        throw new \RuntimeException( 'runCallback() method is not supported by ezpKernelRest' );
    }

    /**
     * Sets whether to use exceptions inside the kernel.
     *
     * @param bool $useExceptions
     */
    public function setUseExceptions( $useExceptions )
    {
        eZModule::$useExceptions = (bool)$useExceptions;
    }

    /**
     * Reinitializes the kernel environment.
     *
     * @return void
     */
    public function reInitialize()
    {
        $this->isInitialized = false;
    }

    /**
     * Runs the shutdown process
     */
    protected function shutdown( $reInitialize = true )
    {
        eZExecution::cleanup();
        eZExecution::setCleanExit();
        eZExpiryHandler::shutdown();
        if ( $reInitialize )
            $this->isInitialized = false;
    }

    /**
     * Checks whether the kernel handler has the Symfony service container
     * container or not.
     *
     * @return bool
     */
    public function hasServiceContainer()
    {
        return false;
    }

    /**
     * Returns the Symfony service container if it has been injected,
     * otherwise returns null.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface|null
     */
    public function getServiceContainer()
    {
        return;
    }
}
