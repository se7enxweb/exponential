<?php
/**
 * File containing the ezpKernelWeb class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

/**
 * Provides a kernel handler in web context
 *
 * Allows kernel to be executed as Controller via run()
 */
class ezpKernelWeb implements ezpWebBasedKernelHandler
{
    /**
     * @var ezpMobileDeviceDetect
     */
    private $mobileDeviceDetect;

    /**
     * @var array
     */
    private $policyCheckViewMap;

    /**
     * @var eZModule
     */
    private $module;

    /**
     * @var array
     */
    private $warningList = array();

    /**
     * @var array
     */
    private $siteBasics;

    /**
     * @var string
     */
    private $actualRequestedURI;

    /**
     * @var string
     */
    private $oldURI;

    /**
     * @var string
     */
    private $completeRequestedURI;

    /**
     * Current siteaccess data
     *
     * @var array
     */
    private $access;

    /**
     * @var eZURI
     */
    private $uri;

    /**
     * @var array
     */
    private $check;

    /**
     * @var array
     */
    private $site;

    /**
     * @see eZLocale::httpLocaleCode()
     * @var string
     */
    private $languageCode;

    /**
     * @see eZTextCodec::httpCharset()
     * @var string
     */
    private $httpCharset;

    /**
     * Indicates is request has been properly initialized
     *
     * @var bool
     */
    protected $isInitialized = false;

    /**
     * The refusal of this request's POST by the form token check, when it was
     * refused: answered with a 403 instead of the module view
     *
     * @var ezpFormTokenException|null
     */
    protected $formTokenRefusal = null;

    /**
     * Hash of settings for the web kernel handler.
     *
     * Keys can be:
     *  - siteaccess (injected siteaccess, an associative array with 'name' (string), 'type' (int) and 'uri_part' (array))
     *
     * @var array
     */
    protected $settings = array();

    /**
     * Constructs an ezpKernel instance
     */
    public function __construct( array $settings = array() )
    {
        // A new request: the query cache's memo, settings and state view start
        // empty, also in a persistent worker that served one before.
        if ( class_exists( 'eZDBQueryCache' ) )
            eZDBQueryCache::resetRequest();
        if ( method_exists( 'eZDBInterface', 'resetSQLProfile' ) )
            eZDBInterface::resetSQLProfile();
        // The audit too: what a previous request left is flushed, then its buffer, request id, open parent
        // events and cached actors are cleared (doc/bc/6.0/audit.md, "Buffering and flushing")
        if ( class_exists( 'expAudit' ) )
            expAudit::resetRequest();

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
            'siteaccess'            => null,
            'use-exceptions'        => false,
            'session'               => null,
            'service-container'     => null,
        );
        unset( $settings, $injectedSettings, $file, $section, $setting, $keySetting, $injectedSetting );

        require_once __DIR__ . '/global_functions.php';
        // Set every time: a persistent worker may have run another front controller before.
        eZDebugSettingsMode( 'web' );
        $this->setUseExceptions( $this->settings['use-exceptions'] );

        $GLOBALS['eZSiteBasics'] = array(
            'external-css' => false,
            'show-page-layout' => true,
            'module-run-required' => true,
            'policy-check-required' => true,
            'policy-check-omit-list' => array(),// List of module names which will skip policy checking
            'url-translator-allowed' => true,
            'validity-check-required' => false,
            'user-object-required' => true,
            'session-required' => true,
            'db-required' => false,
            'no-cache-adviced' => false,
            'site-design-override' => false,
            'module-repositories' => array(),// List of directories to search for modules
        );
        $this->siteBasics =& $GLOBALS['eZSiteBasics'];

        // Reads settings from i18n.ini and passes them to eZTextCodec.
        list(
            $i18nSettings['internal-charset'],
            $i18nSettings['http-charset'],
            $i18nSettings['mbstring-extension']
        ) = eZINI::instance( 'i18n.ini' )->variableMulti(
            'CharacterSettings',
                array( 'Charset', 'HTTPCharset', 'MBStringExtension' ),
                array( false, false, 'enabled' )
        );

        eZTextCodec::updateSettings( $i18nSettings );// @todo Change so code only supports utf-8 in 5.0?

        // Initialize debug settings.
        eZUpdateDebugSettings();

        // Set the different permissions/settings.
        $ini = eZINI::instance();

        // Set correct site timezone
        $timezone = $ini->variable( "TimeZoneSettings", "TimeZone");
        if ( $timezone )
        {
            date_default_timezone_set( $timezone );
        }

        list( $iniFilePermission, $iniDirPermission ) =
            $ini->variableMulti( 'FileSettings', array( 'StorageFilePermissions', 'StorageDirPermissions' ) );

        // OPTIMIZATION:
        // Sets permission array as global variable, this avoids the eZCodePage include
        $GLOBALS['EZCODEPAGEPERMISSIONS'] = array(
            'file_permission' => octdec( $iniFilePermission ),
            'dir_permission'  => octdec( $iniDirPermission ),
            'var_directory'   => eZSys::cacheDirectory()
        );
        unset( $i18nSettings, $timezone, $iniFilePermission, $iniDirPermission );

        eZExecution::addCleanupHandler(
            function()
            {
                if ( class_exists( 'eZDB', false ) && eZDB::hasInstance() )
                {
                    eZDB::instance()->setIsSQLOutputEnabled( false );
                }
            }
        );

        // Sets up the FatalErrorHandler
        $this->setupFatalErrorHandler();

        // Enable this line to get eZINI debug output
        // eZINI::setIsDebugEnabled( true );
        // Enable this line to turn off ini caching
        // eZINI::setIsCacheEnabled( false);

        if ( $ini->variable( 'RegionalSettings', 'Debug' ) === 'enabled' )
            eZLocale::setIsDebugEnabled( true );

        eZDebug::setHandleType( eZDebug::HANDLE_FROM_PHP );

        $GLOBALS['eZGlobalRequestURI'] = eZSys::serverVariable( 'REQUEST_URI' );

        // Initialize basic settings, such as vhless dirs and separators
        if ( $this->hasServiceContainer() && $this->getServiceContainer()->has( 'request' ) )
        {
            eZSys::init(
                basename( $this->getServiceContainer()->get( 'request' )->server->get( 'SCRIPT_FILENAME' ) ),
                $ini->variable( 'SiteAccessSettings', 'ForceVirtualHost' ) === 'true'
            );
        }
        else
        {
            eZSys::init(
                'index.php',
                $ini->variable( 'SiteAccessSettings', 'ForceVirtualHost' ) === 'true'
            );
        }

        // Check for extension
        eZExtension::activateExtensions( 'default' );
        // Extension check end

        // Use injected siteaccess if available or match it internally.
        $this->access = isset( $this->settings['siteaccess'] ) ?
            $this->settings['siteaccess'] :
            eZSiteAccess::match(
                eZURI::instance( eZSys::requestURI() ),
                // Host without the port. HTTP_HOST carries ":8080" whenever the
                // site is served on a non-default port, and host matching
                // compares that string against HostMatchMapItems, which name
                // hosts and not ports -- so the map never matched and the
                // siteaccess fell through to whatever came next. On this
                // installation that meant every URL on every page came out
                // prefixed with the siteaccess name, on the persistent-worker
                // server only, while the same site behind the other web server
                // on its default port was fine.
                //
                // The port is not being discarded: it is passed as the next
                // argument, which is where this function expects it and what
                // port matching uses. eZSys::serverURL() strips it in the same
                // way for the same reason.
                preg_replace( '/:\d+$/', '', (string)eZSys::hostname() ),
                eZSys::serverPort(),
                eZSys::indexFile()
            )
        ;

        eZSiteAccess::change( $this->access );
        eZDebugSetting::writeDebug( 'kernel-siteaccess', $this->access, 'current siteaccess' );

        // Check for siteaccess extension
        eZExtension::activateExtensions( 'access' );
        // Siteaccess extension check end

        // Now that all extensions are activated and siteaccess has been changed, reset
        // all eZINI instances as they may not take into account siteaccess specific settings.
        eZINI::resetAllInstances( false );

        ezpEvent::getInstance()->registerEventListeners();

        $this->mobileDeviceDetect = new ezpMobileDeviceDetect( ezpMobileDeviceDetectFilter::getFilter() );
        // eZSession::setSessionArray( $mainRequest->session );
    }

    private function setupFatalErrorHandler()
    {
        $errorINI = eZINI::instance( 'error.ini' );
        if ( $errorINI->hasVariable( 'ErrorSettings-kernel', 'FatalErrorHandler' ) && is_callable( $errorINI->variable( 'ErrorSettings-kernel', 'FatalErrorHandler' ) ) )
        {
            eZExecution::addFatalErrorHandler( $errorINI->variable( 'ErrorSettings-kernel', 'FatalErrorHandler' ) );
        }
        else
        {
            eZExecution::addFatalErrorHandler(
                function()
                {
                    // The site's own error page, with a reference that is
                    // also written to the error log beside the fatal error.
                    $reference = eZExecution::errorReference();
                    $last = error_get_last();
                    eZLog::write( 'Fatal error ' . $reference . ( $last ? ': ' . $last['message'] . ' in ' . $last['file'] . ' on line ' . $last['line'] : '' ), 'error.log' );
                    eZExecution::renderErrorPage( 500, $reference,
                        ( eZDebug::isDebugEnabled() && $last ) ? $last['message'] . ' in ' . $last['file'] . ' on line ' . $last['line'] : '' );
                    // The debug report, where debug output is switched on.
                    eZDisplayResult( null );
                }
            );
        }
        eZExecution::setCleanExit();
    }

    /**
     * Execution point for controller actions
     */
    public function run()
    {
        if ( $this->mobileDeviceDetect->isEnabled() )
        {
            $this->mobileDeviceDetect->process();

            if ( $this->mobileDeviceDetect->isMobileDevice() )
                $this->mobileDeviceDetect->redirect();
        }

        $obLevel = ob_get_level();
        ob_start();
        $this->requestInit();

        // send header information
        $headerOverrides = eZHTTPHeader::headerOverrideArray( $this->uri );

        $headerDefaults = array(
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
            'Last-Modified' => gmdate( 'D, d M Y H:i:s' ) . ' GMT',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Powered-By' => ExponentialSDK::EDITION,
            'Content-Type' => 'text/html; charset=' . $this->httpCharset,
            'Served-by' => isset( $_SERVER["SERVER_NAME"] ) ? $_SERVER['SERVER_NAME'] : null,
            'Content-language' => $this->languageCode
        ) + self::securityHeaders();

        // A siteaccess chosen by the languages the browser accepts (DefaultHostUriMatchMapItems): the same address
        // answers in another language for another browser. Vary tells a cache between; private keeps the page out
        // of shared caches that key by address alone (Velocity's response cache), also when [HTTPHeaderSettings]
        // makes pages public. Only the address without language segment is chosen this way.
        if ( !empty( $this->access['vary'] ) )
        {
            $headerOverrides['Vary'] = (string)$this->access['vary'];
            $headerOverrides['Cache-Control'] = 'private, no-cache, must-revalidate';
        }

        // Pragma is the HTTP/1.0 spelling of Cache-Control and there is no way
        // to say "cacheable" in it. So when a configured header makes a page
        // cacheable, leaving the default Pragma in place sends a response that
        // contradicts itself: "public, max-age=60" beside "no-cache". Browsers
        // resolve that in favour of Cache-Control, but a proxy is entitled to
        // read the Pragma and decline to store the page -- which is the whole
        // point of having set the header.
        //
        // Only dropped when Cache-Control was actually overridden. A response
        // that keeps the default Cache-Control keeps the matching Pragma, so
        // nothing changes for an installation that has configured nothing.
        if ( isset( $headerOverrides['Cache-Control'] ) && !isset( $headerOverrides['Pragma'] ) )
        {
            unset( $headerDefaults['Pragma'] );
        }

        // The audit's request id (audit.ini [AuditRecordSettings] RequestIdHeader), the same id the request's
        // audit records carry
        if ( class_exists( 'expAudit' ) && ( $auditHeader = expAudit::responseHeader() ) !== null )
            $headerDefaults[$auditHeader[0]] = $auditHeader[1];

        foreach ( $headerOverrides + $headerDefaults as $key => $value )
        {
            header( $key . ': ' . $value );
        }

        // An address without language segment whose siteaccess the browser's language chose
        // (DefaultHostUriMatchMapItems): send the browser on to the same address with the segment, the address as
        // it was asked for (a URL alias stays one) and its query. The redirect varies by language and is private
        // (the headers above); the page behind it has one address per language and is cached as any other.
        if ( !empty( $this->access['redirect'] ) && in_array( $_SERVER['REQUEST_METHOD'] ?? 'GET', array( 'GET', 'HEAD' ), true ) )
        {
            $this->shutdown();
            if ( ob_get_level() > $obLevel )
                ob_end_clean();
            return eZHTTPTool::redirect( self::languageRedirectURI( eZSys::indexDir(), eZSys::requestURI(), (string)eZSys::queryString() ),
                                         array(), '302 Found', true, true );
        }

        // A refused POST from a script (XHR, a JSON body, Accept: JSON) gets
        // the refusal as JSON, without running a module or the pagelayout
        if ( $this->formTokenRefusal !== null && ezpFormTokenRefusal::wantsJson() )
        {
            ezpFormTokenRefusal::sendHeaders( 'application/json; charset=utf-8' );
            // Anything printed so far would only break the JSON
            if ( ob_get_level() > $obLevel )
                ob_end_clean();
            $content = ezpFormTokenRefusal::jsonBody( $this->formTokenRefusal );
            $this->shutdown();
            return new ezpKernelResult( $content, array( 'form_token_refused' => $this->formTokenRefusal->getReason() ) );
        }

        try
        {
            $moduleResult = $this->dispatchLoop();
        }
        catch ( Throwable $e )
        {
            if ( $e instanceof Exception )
                $this->shutdown();
            // Close our buffer (a persistent worker never ends the request to do it); flushed,
            // as request end used to, so what was buffered still goes out ahead of the error
            if ( ob_get_level() > $obLevel )
                ob_end_flush();
            throw $e;
        }

        $ini = eZINI::instance();

        /**
         * Ouput an is_logged_in cookie when users are logged in for use by http cache solutions.
         *
         * @deprecated As of 4.5, since 4.4 added lazy session support (init on use)
         */
        if ( $ini->variable( "SiteAccessSettings", "CheckValidity" ) !== 'true' )
        {
            $wwwDir = eZSys::wwwDir();
            // On host based site accesses this can be empty, causing the cookie to be set for the current dir,
            // but we want it to be set for the whole eZ publish site
            $cookiePath = $wwwDir != '' ? $wwwDir : '/';

            if ( eZUser::isCurrentUserRegistered() )
            {
                // Only set the cookie if it doesnt exist. This way we are not constantly sending the set request in the headers.
                if ( !isset( $_COOKIE['is_logged_in'] ) || $_COOKIE['is_logged_in'] !== 'true' )
                {
                    setcookie( 'is_logged_in', 'true', 0, $cookiePath );
                }
            }
            else if ( isset( $_COOKIE['is_logged_in'] ) )
            {
                setcookie( 'is_logged_in', false, 0, $cookiePath );
            }
        }

        if ( $this->module->exitStatus() == eZModule::STATUS_REDIRECT )
        {
            $this->shutdown();
            $redirect = $this->redirect();
            // Close our buffer (a persistent worker never ends the request to do it), after
            // redirect() set its headers; flushed, as request end used to, so output is unchanged
            if ( ob_get_level() > $obLevel )
                ob_end_flush();
            return $redirect;
        }

        $uiContextName = $this->module->uiContextName();

        // Store the last URI for access history for login redirection
        // Only if user has session and only if there was no error or no redirects happen
        if ( eZSession::hasStarted() && $this->module->exitStatus() == eZModule::STATUS_OK )
        {
            $currentURI = $this->completeRequestedURI;
            if ( strlen( $currentURI ) > 0 && $currentURI[0] !== '/' )
                $currentURI = '/' . $currentURI;

            $lastAccessedURI = "";
            $lastAccessedViewURI = "";

            $http = eZHTTPTool::instance();

            // Fetched stored session variables
            if ( $http->hasSessionVariable( "LastAccessesURI" ) )
            {
                $lastAccessedViewURI = $http->sessionVariable( "LastAccessesURI" );
            }
            if ( $http->hasSessionVariable( "LastAccessedModifyingURI" ) )
            {
                $lastAccessedURI = $http->sessionVariable( "LastAccessedModifyingURI" );
            }

            // Update last accessed view page
            if ( $currentURI != $lastAccessedViewURI &&
                 !in_array( $uiContextName, array( 'edit', 'administration', 'ajax', 'browse', 'authentication' ) ) )
            {
                $http->setSessionVariable( "LastAccessesURI", $currentURI );
            }

            // Update last accessed non-view page
            if ( $currentURI != $lastAccessedURI && $uiContextName != 'ajax' )
            {
                $http->setSessionVariable( "LastAccessedModifyingURI", $currentURI );
            }
        }

        eZDebug::addTimingPoint( "Module end '" . $this->module->attribute( 'name' ) . "'" );
        if ( !is_array( $moduleResult ) )
        {
            eZDebug::writeError( 'Module did not return proper result: ' . $this->module->attribute( 'name' ), 'index.php' );
            $moduleResult = array();
            $moduleResult['content'] = false;
        }

        if ( !isset( $moduleResult['ui_context'] ) )
        {
            $moduleResult['ui_context'] = $uiContextName;
        }
        $moduleResult['ui_component'] = $this->module->uiComponentName();
        $moduleResult['is_mobile_device'] = $this->mobileDeviceDetect->isMobileDevice();
        $moduleResult['mobile_device_alias'] = $this->mobileDeviceDetect->getUserAgentAlias();

        $templateResult = null;

        eZDebug::setUseExternalCSS( $this->siteBasics['external-css'] );
        if ( $this->siteBasics['show-page-layout'] )
        {
            $tpl = eZTemplate::factory();
            if ( $tpl->hasVariable( 'node' ) )
                $tpl->unsetVariable( 'node' );

            if ( !isset( $moduleResult['path'] ) )
                $moduleResult['path'] = false;
            $moduleResult['uri'] = eZSys::requestURI();

            $tpl->setVariable( "module_result", $moduleResult );

            $meta = $ini->variable( 'SiteSettings', 'MetaDataArray' );

            if ( !isset( $meta['description'] ) )
            {
                $metaDescription = "";
                if ( isset( $moduleResult['path'] ) && is_array( $moduleResult['path'] ) )
                {
                    foreach ( $moduleResult['path'] as $pathPart )
                    {
                        if ( isset( $pathPart['text'] ) )
                            $metaDescription .= $pathPart['text'] . " ";
                    }
                }
                $meta['description'] = $metaDescription;
            }

            $this->site['uri'] = $this->oldURI;
            $this->site['redirect'] = false;
            $this->site['meta'] = $meta;
            $this->site['version'] = ExponentialSDK::version();
            $this->site['page_title'] = $this->module->title();

            $tpl->setVariable( "site", $this->site );

            if ( $ini->variable( 'DebugSettings', 'DisplayDebugWarnings' ) === 'enabled' )
            {
                // Make sure any errors or warnings are reported
                if ( isset( $GLOBALS['eZDebugError'] ) && $GLOBALS['eZDebugError'] )
                {
                    eZAppendWarningItem(
                        array(
                            'error' => array(
                                'type' => 'error',
                                'number' => 1 ,
                                'count' => $GLOBALS['eZDebugErrorCount']
                            ),
                            'identifier' => 'ezdebug-first-error',
                            'text' => ezpI18n::tr( 'index.php', 'Some errors occurred, see debug for more information.' )
                        )
                    );
                }

                if ( isset( $GLOBALS['eZDebugWarning'] ) && $GLOBALS['eZDebugWarning'] )
                {
                    eZAppendWarningItem(
                        array(
                            'error' => array(
                                'type' => 'warning',
                                'number' => 1,
                                'count' => $GLOBALS['eZDebugWarningCount']
                            ),
                            'identifier' => 'ezdebug-first-warning',
                            'text' => ezpI18n::tr( 'index.php', 'Some general warnings occured, see debug for more information.' )
                        )
                    );
                }
            }

            if ( $this->siteBasics['user-object-required'] )
            {
                $currentUser = eZUser::currentUser();

                $tpl->setVariable( "current_user", $currentUser );
                $tpl->setVariable( "anonymous_user_id", $ini->variable( 'UserSettings', 'AnonymousUserID' ) );
            }
            else
            {
                $tpl->setVariable( "current_user", false );
                $tpl->setVariable( "anonymous_user_id", false );
            }

            $tpl->setVariable( "access_type", $this->access );
            $tpl->setVariable( 'warning_list', !empty( $this->warningList) ? $this->warningList : false );

            $resource = "design:";
            if ( is_string( $this->siteBasics['show-page-layout'] ) )
            {
                if ( strpos( $this->siteBasics['show-page-layout'], ":" ) !== false )
                {
                    $resource = "";
                }
            }
            else
            {
                $this->siteBasics['show-page-layout'] = "pagelayout.tpl";
            }

            // Set the navigation part
            // Check for navigation part settings
            $navigationPartString = 'ezcontentnavigationpart';
            if ( isset( $moduleResult['navigation_part'] ) )
            {
                $navigationPartString = $moduleResult['navigation_part'];

                // Fetch the navigation part
            }
            $navigationPart = eZNavigationPart::fetchPartByIdentifier( $navigationPartString );

            $tpl->setVariable( 'navigation_part', $navigationPart );
            $tpl->setVariable( 'uri_string', $this->uri->uriString() );
            if ( isset( $moduleResult['requested_uri_string'] ) )
            {
                $tpl->setVariable( 'requested_uri_string', $moduleResult['requested_uri_string'] );
            }
            else
            {
                $tpl->setVariable( 'requested_uri_string', $this->actualRequestedURI );
            }

            // Set UI context and component
            $tpl->setVariable( 'ui_context', $moduleResult['ui_context'] );
            $tpl->setVariable( 'ui_component', $moduleResult['ui_component'] );

            $templateResult = $tpl->fetch( $resource . $this->siteBasics['show-page-layout'] );
        }
        else
        {
            $templateResult = $moduleResult['content'];
        }

        eZDebug::addTimingPoint( "Script end" );

        $content = trim( ob_get_clean() );

        ob_start();
        eZDB::checkTransactionCounter();
        eZDisplayResult( $templateResult );
        $content .= ob_get_clean();

        // Last, after the output filters: the form token filter marks a page
        // with tokens "private, no-cache", and a refusal is kept by nothing
        if ( $this->formTokenRefusal !== null )
            ezpFormTokenRefusal::sendHeaders();

        $this->shutdown();

        return new ezpKernelResult( $content, array( 'module_result' => $moduleResult ) );
    }

    /**
     * Runs the dispatch loop
     */
    protected function dispatchLoop()
    {
        $ini = eZINI::instance();

        if ( $this->formTokenRefusal !== null )
            return $this->formTokenRefusalResult( $this->formTokenRefusal );

        // Rewrites by request rules in this request (ezpRequestRuleKernel), against
        // loops; and their per-request state, which a persistent worker keeps otherwise
        $requestRuleRewrites = 0;
        $GLOBALS['ezpRequestRuleNoStore'] = false;
        $GLOBALS['ezpRequestRuleDecision'] = null;

        // Start the module loop
        while ( $this->siteBasics['module-run-required'] )
        {
            $objectHasMovedError = false;
            $objectHasMovedURI = false;
            $this->actualRequestedURI = $this->uri->uriString();

            // Extract user specified parameters
            $userParameters = $this->uri->userParameters();

            // Generate a URI which also includes the user parameters
            $this->completeRequestedURI = $this->uri->originalURIString();

            // How the visitor reached the view, for the request rules: the
            // address typed, and whether an alias or wildcard translated it
            $requestRoute = array(
                'typed_uri' => $this->actualRequestedURI,
                'via' => $this->uri->isEmpty() ? 'index' : 'system',
                'user_parameters' => $userParameters,
                'rewrites' => $requestRuleRewrites,
            );

            // Check for URL translation
            if ( $this->siteBasics['url-translator-allowed'] && eZURLAliasML::urlTranslationEnabledByUri( $this->uri ) )
            {
                $translateResult = eZURLAliasML::translate( $this->uri );
                if ( strcasecmp( trim( $this->uri->uriString(), '/' ), trim( $requestRoute['typed_uri'], '/' ) ) !== 0 )
                    $requestRoute['via'] = 'alias';

                if ( !is_string( $translateResult ) && $ini->variable( 'URLTranslator', 'WildcardTranslation' ) === 'enabled' )
                {
                    $translateResult = eZURLWildcard::translate( $this->uri );
                    if ( $requestRoute['via'] === 'system' && strcasecmp( trim( $this->uri->uriString(), '/' ), trim( $requestRoute['typed_uri'], '/' ) ) !== 0 )
                        $requestRoute['via'] = 'wildcard';
                }

                // Check if the URL has moved
                if ( is_string( $translateResult ) )
                {
                    $objectHasMovedURI = $translateResult;
                    foreach ( $userParameters as $name => $value )
                    {
                        $objectHasMovedURI .= '/(' . $name . ')/' . $value;
                    }

                    $objectHasMovedError = true;
                }
            }

            if ( $this->uri->isEmpty() )
            {
                $tmp_uri = new eZURI( $ini->variable( "SiteSettings", "IndexPage" ) );
                $moduleCheck = eZModule::accessAllowed( $tmp_uri );
            }
            else
            {
                $moduleCheck = eZModule::accessAllowed( $this->uri );
            }

            if ( !$moduleCheck['result'] )
            {
                if ( $ini->variable( "SiteSettings", "ErrorHandler" ) == "defaultpage" )
                {
                    $defaultPage = $ini->variable( "SiteSettings", "DefaultPage" );
                    $this->uri->setURIString( $defaultPage );
                    $moduleCheck['result'] = true;
                }
            }

            $displayMissingModule = false;
            $this->oldURI = $this->uri;

            if ( $this->uri->isEmpty() )
            {
                if ( !fetchModule( $tmp_uri, $this->check, $this->module, $moduleName, $functionName, $params ) )
                    $displayMissingModule = true;
            }
            else if ( !fetchModule( $this->uri, $this->check, $this->module, $moduleName, $functionName, $params ) )
            {
                if ( $ini->variable( "SiteSettings", "ErrorHandler" ) == "defaultpage" )
                {
                    $tmp_uri = new eZURI( $ini->variable( "SiteSettings", "DefaultPage" ) );
                    if ( !fetchModule( $tmp_uri, $this->check, $this->module, $moduleName, $functionName, $params ) )
                        $displayMissingModule = true;
                }
                else
                    $displayMissingModule = true;
            }

            if ( !$displayMissingModule && $moduleCheck['result'] && $this->module instanceof eZModule )
            {
                // Run the module/function
                eZDebug::addTimingPoint( "Module start '" . $this->module->attribute( 'name' ) . "'" );

                $moduleAccessAllowed = true;
                $omitPolicyCheck = true;
                $runModuleView = true;

                $availableViewsInModule = $this->module->attribute( 'views' );
                if (
                    !isset( $availableViewsInModule[$functionName] )
                    && !$objectHasMovedError
                    && !isset( $this->module->Module['function']['script'] )
                )
                {
                    // module and view name the missing page on the error page (error/kernel/21); check is what
                    // the exception path of eZModule::handleError() reads
                    $moduleResult = $this->module->handleError( eZError::KERNEL_MODULE_VIEW_NOT_FOUND, 'kernel',
                                                                array( "check" => $moduleCheck, 'module' => $moduleName, 'view' => $functionName ) );
                    $runModuleView = false;
                    $this->siteBasics['policy-check-required'] = false;
                    $omitPolicyCheck = true;
                }

                if ( $this->siteBasics['policy-check-required'] )
                {
                    $omitPolicyCheck = false;
                    $moduleName = $this->module->attribute( 'name' );
                    if ( in_array( $moduleName, $this->siteBasics['policy-check-omit-list'] ) )
                        $omitPolicyCheck = true;
                    else
                    {
                        $policyCheckViewMap = $this->getPolicyCheckViewMap( $this->siteBasics['policy-check-omit-list'] );
                        if ( isset( $policyCheckViewMap[$moduleName][$functionName] ) )
                            $omitPolicyCheck = true;
                    }
                }
                if ( !$omitPolicyCheck )
                {
                    $currentUser = eZUser::currentUser();
                    $siteAccessResult = $currentUser->hasAccessTo( 'user', 'login' );

                    $hasAccessToSite = false;
                    if ( $siteAccessResult[ 'accessWord' ] === 'limited' )
                    {
                        $policyChecked = false;
                        foreach ( array_keys( $siteAccessResult['policies'] ) as $key )
                        {
                            $policy = $siteAccessResult['policies'][$key];
                            if ( isset( $policy['SiteAccess'] ) )
                            {
                                $policyChecked = true;
                                $crc32AccessName = eZSys::ezcrc32( $this->access[ 'name' ] );
                                eZDebugSetting::writeDebug( 'kernel-siteaccess', $policy['SiteAccess'], $crc32AccessName );
                                if ( in_array( $crc32AccessName, $policy['SiteAccess'] ) )
                                {
                                    $hasAccessToSite = true;
                                    break;
                                }
                            }
                            if ( $hasAccessToSite )
                                break;
                        }
                        if ( !$policyChecked )
                            $hasAccessToSite = true;
                    }
                    else if ( $siteAccessResult[ 'accessWord' ] === 'yes' )
                    {
                        eZDebugSetting::writeDebug( 'kernel-siteaccess', "access is yes" );
                        $hasAccessToSite = true;
                    }
                    else if ( $siteAccessResult['accessWord'] === 'no' )
                    {
                        $accessList = $siteAccessResult['accessList'];
                    }

                    if ( $hasAccessToSite )
                    {
                        $accessParams = array();
                        $moduleAccessAllowed = $currentUser->hasAccessToView( $this->module, $functionName, $accessParams );
                        if ( isset( $accessParams['accessList'] ) )
                        {
                            $accessList = $accessParams['accessList'];
                        }
                    }
                    else
                    {
                        eZDebugSetting::writeDebug( 'kernel-siteaccess', $this->access, 'not able to get access to siteaccess' );
                        $moduleAccessAllowed = false;
                        if ( $ini->variable( "SiteAccessSettings", "RequireUserLogin" ) == "true" )
                        {
                            $this->module = eZModule::exists( 'user' );
                            if ( $this->module instanceof eZModule )
                            {
                                $moduleResult = $this->module->run(
                                    'login',
                                    array(),
                                    array(
                                        'SiteAccessAllowed' => false,
                                        'SiteAccessName' => $this->access['name']
                                    )
                                );
                                $runModuleView = false;
                            }
                        }
                    }
                }

                $GLOBALS['eZRequestedModule'] = $this->module;

                if ( $runModuleView )
                {
                    if ( $objectHasMovedError == true )
                    {
                        $moduleResult = $this->module->handleError( eZError::KERNEL_MOVED, 'kernel', array( 'new_location' => $objectHasMovedURI ) );
                    }
                    else if ( !$moduleAccessAllowed )
                    {
                        if ( isset( $availableViewsInModule[$functionName][ 'default_navigation_part' ] ) )
                        {
                            $defaultNavigationPart = $availableViewsInModule[$functionName][ 'default_navigation_part' ];
                        }

                        // The view refused is the one requested (the view does not run): what the audit's
                        // access.permission.refused and the request's module/view say, also in a persistent worker
                        // whose previous request left another module's parameters here
                        $GLOBALS['eZRequestedModuleParams'] = array( 'module_name' => $this->module->attribute( 'name' ),
                                                                     'function_name' => $functionName,
                                                                     'parameters' => array() );

                        if ( isset( $accessList ) )
                            $moduleResult = $this->module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel', array( 'AccessList' => $accessList ) );
                        else
                            $moduleResult = $this->module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );

                        if ( isset( $defaultNavigationPart ) )
                        {
                            $moduleResult['navigation_part'] = $defaultNavigationPart;
                            unset( $defaultNavigationPart );
                        }
                    }
                    else
                    {
                        if ( !isset( $userParameters ) )
                        {
                            $userParameters = false;
                        }

                        // Check if we should switch access mode (http/https) for this module view.
                        eZSSLZone::checkModuleView( $this->module->attribute( 'name' ), $functionName );

                        // A view of a sensitive admin module opened by a signed-in user (doc/bc/6.0/audit.md,
                        // access.view.sensitive, Z6 "always"; [AuditReadSettings] AlwaysModules[]); never a POST body
                        if ( class_exists( 'expAuditHook' ) )
                            self::auditSensitiveView( $this->module->attribute( 'name' ), $functionName );

                        // [AuditSettings] OnWriteFailure=refuse: a POST to a sensitive module does not run while
                        // the audit cannot record it (doc/bc/6.0/audit.md, "When the audit cannot write")
                        $moduleResult = null;
                        if ( class_exists( 'expAuditGuard' ) && !expAuditGuard::webAllows( $this->module->attribute( 'name' ), $functionName ) )
                            $moduleResult = expAuditGuard::refusedResult( $this->module->attribute( 'name' ), $functionName );

                        // The request rules (requestrules.ini) decide after the
                        // policies allowed the view and before it runs
                        if ( $moduleResult === null && class_exists( 'ezpRequestRuleKernel' ) )
                        {
                            $moduleResult = ezpRequestRuleKernel::decide( $this->module, $functionName, $params, $requestRoute );
                            if ( isset( $moduleResult['request_rule_rewrite'] ) )
                                $requestRuleRewrites++;
                        }
                        if ( $moduleResult === null )
                            $moduleResult = $this->module->run( $functionName, $params, false, $userParameters );

                        if ( $this->module->exitStatus() == eZModule::STATUS_FAILED && $moduleResult == null )
                            $moduleResult = $this->module->handleError(
                                eZError::KERNEL_MODULE_VIEW_NOT_FOUND,
                                'kernel',
                                array(
                                    'module' => $moduleName,
                                    'view' => $functionName
                                )
                            );
                    }
                }
            }
            else if ( $moduleCheck['result'] )
            {
                eZDebug::writeError( "Undefined module: $moduleName", "index" );
                $this->module = new eZModule( "", "", $moduleName );
                $GLOBALS['eZRequestedModule'] = $this->module;
                $moduleResult = $this->module->handleError( eZError::KERNEL_MODULE_NOT_FOUND, 'kernel', array( 'module' => $moduleName ) );
            }
            else
            {
                if ( $moduleCheck['view_checked'] )
                    eZDebug::writeError( "View '" . $moduleCheck['view'] . "' in module '" . $moduleCheck['module'] . "' is disabled", "index" );
                else
                    eZDebug::writeError( "Module '" . $moduleCheck['module'] . "' is disabled", "index" );
                $GLOBALS['eZRequestedModule'] = $this->module = new eZModule( "", "", $moduleCheck['module'] );
                // A module this siteaccess switches off answers as one that does
                // not exist (404), so the page does not tell that it is there.
                // [SiteAccessRules] DisabledModuleResult=disabled shows the
                // "disabled" page instead.
                $ini = eZINI::instance();
                $disabledResult = $ini->hasVariable( 'SiteAccessRules', 'DisabledModuleResult' ) ? $ini->variable( 'SiteAccessRules', 'DisabledModuleResult' ) : 'notfound';
                if ( $disabledResult === 'disabled' )
                    $moduleResult = $this->module->handleError( eZError::KERNEL_MODULE_DISABLED, 'kernel', array( 'check' => $moduleCheck ) );
                else
                    $moduleResult = $this->module->handleError( eZError::KERNEL_MODULE_NOT_FOUND, 'kernel', array( 'module' => $moduleCheck['module'] ) );
            }
            $this->siteBasics['module-run-required'] = false;
            if ( isset( $moduleResult['rerun_uri'] ) )
            {
                $this->uri = eZURI::instance( $moduleResult['rerun_uri'] );
                $this->siteBasics['module-run-required'] = true;
            }
            else if ( $this->module->exitStatus() == eZModule::STATUS_RERUN )
            {
                eZDebug::writeError( 'No rerun URI specified on eZModule::STATUS_RERUN, cannot set URI', 'index.php' );
            }

            if ( is_array( $moduleResult ) )
            {
                if ( isset( $moduleResult["pagelayout"] ) )
                {
                    $this->siteBasics['show-page-layout'] = $moduleResult["pagelayout"];
                    $GLOBALS['eZCustomPageLayout'] = $moduleResult["pagelayout"];
                }
                if ( isset( $moduleResult["external_css"] ) )
                    $this->siteBasics['external-css'] = $moduleResult["external_css"];
            }
        }

        return $moduleResult;
    }

    /**
     * Records access.view.sensitive (doc/bc/6.0/audit.md, Z6): a view of a module in [AuditReadSettings]
     * AlwaysModules[] (setup, role, user, audit, settings) opened by a signed-in user. Anonymous visitors (the
     * public login, register and password pages) are not recorded here: their actions have events of their own.
     *
     * @param string $moduleName
     * @param string $functionName
     */
    protected static function auditSensitiveView( $moduleName, $functionName )
    {
        try
        {
            if ( !expAuditHook::on( 'access.view.sensitive' ) )
                return;
            $ini = eZINI::instance( 'audit.ini' );
            $modules = $ini->hasVariable( 'AuditReadSettings', 'AlwaysModules' ) ? (array)$ini->variable( 'AuditReadSettings', 'AlwaysModules' ) : array();
            if ( !in_array( (string)$moduleName, $modules, true ) )
                return;
            $user = eZUser::currentUser();
            if ( !$user instanceof eZUser || $user->isAnonymous() )
                return;
            expAuditHook::emit( 'access.view.sensitive', array( 'object' => array( 'type' => 'view', 'id' => $moduleName . '/' . $functionName ),
                                                                'verb' => 'read' ) );
        }
        catch ( Throwable $e )
        {
        }
    }

    /**
     * The module result of a POST the form token check refused: kernel error
     * eZError::KERNEL_FORM_TOKEN_REFUSED from the error module, in the
     * context of the module that was posted to, so the pagelayout looks as it
     * does for that module. The module view itself never runs.
     *
     * @param ezpFormTokenException $e
     * @return array
     */
    protected function formTokenRefusalResult( ezpFormTokenException $e )
    {
        // A POST refused for a missing or wrong form token (doc/bc/6.0/audit.md, access.token.refused): never the
        // token
        if ( class_exists( 'expAuditHook' ) )
        {
            $uri = $this->uri;
            expAuditHook::emit( 'access.token.refused', function () use ( $e, $uri ) {
                $view = trim( (string)$uri->element( 0 ) . '/' . (string)$uri->element( 1 ), '/' );
                return array( 'object' => array( 'type' => 'view', 'id' => preg_match( '#^[A-Za-z0-9_]+(/[A-Za-z0-9_]+)?$#', $view ) ? $view : 'unknown' ),
                              'verb' => 'post', 'result' => 'refused', 'reason' => 'token',
                              'after' => array( 'refusal' => (string)$e->getReason() ) );
            } );
        }

        $this->actualRequestedURI = $this->uri->uriString();
        $this->completeRequestedURI = $this->uri->originalURIString();
        $this->oldURI = $this->uri;
        $this->siteBasics['module-run-required'] = false;

        $moduleName = (string)$this->uri->element( 0 );
        $module = $moduleName !== '' && preg_match( '/^[a-z0-9_]+$/i', $moduleName ) ? eZModule::exists( $moduleName ) : null;
        if ( !$module instanceof eZModule )
            $module = new eZModule( '', '', $moduleName !== '' ? $moduleName : 'content' );
        $this->module = $module;
        $GLOBALS['eZRequestedModule'] = $module;

        $moduleResult = $module->handleError(
            eZError::KERNEL_FORM_TOKEN_REFUSED,
            'kernel',
            ezpFormTokenRefusal::templateParameters( $e )
        );
        if ( !is_array( $moduleResult ) || !isset( $moduleResult['content'] ) )
        {
            $moduleResult = array(
                'content' => ezpFormTokenRefusal::fallbackContent( ezpFormTokenRefusal::templateParameters( $e ) ),
                'path' => array(
                    array( 'text' => ezpI18n::tr( 'kernel/error', 'Error' ), 'url' => false ),
                    array( 'text' => ezpFormTokenRefusal::pathName(), 'url' => false ),
                ),
                'title' => ezpFormTokenRefusal::pathName(),
            );
        }
        return $moduleResult;
    }

    /**
     * The address the browser is sent on to when the siteaccess of an address without language segment was chosen by
     * its language: the index with the access path of that siteaccess (/ger), then the path that was asked for, as it
     * was asked for, then the query.
     *
     * @param string $indexDir eZSys::indexDir() after the siteaccess is set, for example "/ger" or "/index.php/ger"
     * @param string $requestURI eZSys::requestURI(), for example "/news/an-article" or ""
     * @param string $queryString eZSys::queryString(), with the leading "?" or empty
     * @return string
     */
    public static function languageRedirectURI( $indexDir, $requestURI, $queryString )
    {
        $target = rtrim( (string)$indexDir, '/' );
        $path = trim( (string)$requestURI, '/' );
        if ( $path !== '' )
            $target .= '/' . $path;
        if ( $target === '' )
            $target = '/';
        $query = ltrim( (string)$queryString, '?' );
        return $query !== '' ? $target . '?' . $query : $target;
    }

    /**
     * The security headers every page is sent with, from site.ini
     * [HTTPHeaderSettings] SecurityHeaders[<name>]=<value>. An empty value
     * drops that header; Strict-Transport-Security is only sent over HTTPS.
     * Custom headers ([HTTPHeaderSettings] HeaderList) still override these.
     *
     * @return array header name => value
     */
    public static function securityHeaders()
    {
        $ini = eZINI::instance();
        $configured = $ini->hasVariable( 'HTTPHeaderSettings', 'SecurityHeaders' )
            ? $ini->variable( 'HTTPHeaderSettings', 'SecurityHeaders' )
            : array();
        if ( !is_array( $configured ) )
            return array();

        $headers = array();
        foreach ( $configured as $name => $value )
        {
            $name = trim( (string)$name );
            $value = trim( (string)$value );
            // A header name is a token; a value is one line. Anything else
            // would let a setting split the response.
            if ( $value === '' || !preg_match( '/^[A-Za-z0-9-]+$/D', $name ) || preg_match( '/[\r\n\0]/', $value ) )
                continue;
            if ( strcasecmp( $name, 'Strict-Transport-Security' ) === 0 && !eZSys::isSSLNow() )
                continue;
            $headers[$name] = $value;
        }
        return $headers;
    }

    /**
     * Returns the map for policy check view
     *
     * @param array $policyCheckOmitList
     *
     * @return array
     */
    protected function getPolicyCheckViewMap( array $policyCheckOmitList )
    {
        if ( $this->policyCheckViewMap !== null )
            return $this->policyCheckViewMap;

        $this->policyCheckViewMap = array();
        foreach ( $policyCheckOmitList as $omitItem )
        {
            $items = explode( '/', $omitItem );
            if ( count( $items ) > 1 )
            {
                $module = $items[0];
                if ( !isset( $this->policyCheckViewMap[$module] ) )
                    $this->policyCheckViewMap[$module] = array();
                $this->policyCheckViewMap[$module][$items[1]] = true;
            }
        }

        return $this->policyCheckViewMap;
    }

    /**
     * Carries the query string of the current request over a module redirect.
     *
     * Redirects to another host keep their URL untouched: a payment window
     * URL is sealed over its exact query, so tracking parameters of the shop
     * request (_gl, gclid, ...) appended to it invalidate the seal. On the
     * own host the query joins with "&" when the target already has one and
     * goes before a fragment.
     *
     * @param string $redirectURI completed redirect target, relative or absolute
     * @param string $queryString query of the current request, with or without the leading "?"
     * @param string $currentHost host of the current request, a port is ignored
     * @param array $trustedHosts further hosts that count as the own host (the configured SiteURL host)
     * @return string
     */
    public static function appendRequestQuery( $redirectURI, $queryString, $currentHost, array $trustedHosts = array() )
    {
        $redirectURI = (string)$redirectURI;
        $query = ltrim( (string)$queryString, '?' );
        if ( $query === '' )
        {
            return $redirectURI;
        }

        // Absolute (any scheme) and protocol-relative targets leave the query behind
        // unless they point at the own host.
        if ( preg_match( '#^(?:[a-z][a-z0-9+.-]*:|//)#i', $redirectURI ) )
        {
            $targetHost = self::normaliseRedirectHost( (string)parse_url( $redirectURI, PHP_URL_HOST ) );
            if ( $targetHost === '' )
            {
                return $redirectURI;
            }
            $own = false;
            foreach ( array_merge( array( $currentHost ), $trustedHosts ) as $host )
            {
                $host = self::normaliseRedirectHost( (string)$host );
                if ( $host !== '' && $host === $targetHost )
                {
                    $own = true;
                    break;
                }
            }
            if ( !$own )
            {
                return $redirectURI;
            }
        }

        $fragment = '';
        $hashPos = strpos( $redirectURI, '#' );
        if ( $hashPos !== false )
        {
            $fragment = substr( $redirectURI, $hashPos );
            $redirectURI = substr( $redirectURI, 0, $hashPos );
        }

        $separator = strpos( $redirectURI, '?' ) !== false ? '&' : '?';

        return $redirectURI . $separator . $query . $fragment;
    }

    /**
     * Lower-cased ASCII host without port, for comparing redirect targets with the own host.
     *
     * @param string $host
     * @return string
     */
    protected static function normaliseRedirectHost( $host )
    {
        $host = strtolower( trim( $host ) );
        if ( $host === '' )
        {
            return '';
        }
        if ( $host[0] === '[' )
        {
            $end = strpos( $host, ']' );
            return $end === false ? $host : substr( $host, 0, $end + 1 );
        }
        $host = rtrim( (string)preg_replace( '/:\d*$/', '', $host ), '.' );
        if ( function_exists( 'idn_to_ascii' ) && preg_match( '/[^\x00-\x7f]/', $host ) )
        {
            $ascii = idn_to_ascii( $host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46 );
            if ( $ascii !== false )
            {
                $host = $ascii;
            }
        }
        return $host;
    }

    /**
     * Performs a redirection
     */
    protected function redirect()
    {
        $GLOBALS['eZRedirection'] = true;
        $ini = eZINI::instance();
        $automaticRedirect = true;

        if ( $GLOBALS['eZDebugAllowed'] && ( $redirUri = $ini->variable( 'DebugSettings', 'DebugRedirection' ) ) !== 'disabled' )
        {
            if ( $redirUri == "enabled" )
            {
                $automaticRedirect = false;
            }
            else
            {
                $uri = eZURI::instance( eZSys::requestURI() );
                $uri->toBeginning();
                foreach ( $ini->variableArray( "DebugSettings", "DebugRedirection" ) as $redirUri )
                {
                    $redirUri = new eZURI( $redirUri );
                    if ( $redirUri->matchBase( $uri ) )
                    {
                        $automaticRedirect = false;
                        break;
                    }
                }
            }
        }

        $redirectURI = eZSys::indexDir();

        $moduleRedirectUri = $this->module->redirectURI();
        if ( $ini->variable( 'URLTranslator', 'Translation' ) === 'enabled' &&
                eZURLAliasML::urlTranslationEnabledByUri( new eZURI( $moduleRedirectUri ) ) )
        {
            $translatedModuleRedirectUri = $moduleRedirectUri;
            if ( eZURLAliasML::translate( $translatedModuleRedirectUri, true ) )
            {
                $moduleRedirectUri = $translatedModuleRedirectUri;
                if ( strlen( $moduleRedirectUri ) > 0 && $moduleRedirectUri[0] !== '/' )
                    $moduleRedirectUri = '/' . $moduleRedirectUri;
            }
        }

        if ( preg_match( '#^(\w+:)|^//#', $moduleRedirectUri ) )
        {
            $redirectURI = $moduleRedirectUri;
        }
        else
        {
            $leftSlash = strlen( $redirectURI ) > 0 && $redirectURI[strlen( $redirectURI ) - 1] === '/';
            $rightSlash = strlen( $moduleRedirectUri ) > 0 && $moduleRedirectUri[0] === '/';

            if ( !$leftSlash && !$rightSlash ) // Both are without a slash, so add one
                $moduleRedirectUri = '/' . $moduleRedirectUri;
            else if ( $leftSlash && $rightSlash ) // Both are with a slash, so we remove one
                $moduleRedirectUri = substr( $moduleRedirectUri, 1 );

            // In some cases $moduleRedirectUri can already contain $redirectURI (including the siteaccess).
            if ( !empty( $redirectURI ) && strpos( $moduleRedirectUri, $redirectURI ) === 0 )
            {
                $redirectURI = $moduleRedirectUri;
            }
            else
            {
                $redirectURI .= $moduleRedirectUri;
            }
        }

        // After the module redirect url is completed, add the queryString params so they carry over the redirect operation
        $redirectURI = self::appendRequestQuery( $redirectURI, (string)eZSys::queryString(), (string)eZSys::hostname(),
            array( (string)parse_url( 'http://' . $ini->variable( 'SiteSettings', 'SiteURL' ), PHP_URL_HOST ) ) );

        if ( $ini->variable( 'ContentSettings', 'StaticCache' ) == 'enabled' )
        {
            $staticCacheHandlerClassName = $ini->variable( 'ContentSettings', 'StaticCacheHandler' );
            $staticCacheHandlerClassName::executeActions();
        }

        eZDB::checkTransactionCounter();

        if ( !$automaticRedirect )
        {
            // Make sure any errors or warnings are reported
            if ( $ini->variable( 'DebugSettings', 'DisplayDebugWarnings' ) === 'enabled' )
            {
                if ( isset( $GLOBALS['eZDebugError'] ) && $GLOBALS['eZDebugError'] )
                {
                    eZAppendWarningItem(
                        array(
                            'error' => array(
                                'type' => 'error',
                                'number' => 1,
                                'count' => $GLOBALS['eZDebugErrorCount']
                            ),
                            'identifier' => 'ezdebug-first-error',
                            'text' => ezpI18n::tr( 'index.php', 'Some errors occurred, see debug for more information.' )
                        )
                    );
                }

                if ( isset( $GLOBALS['eZDebugWarning'] ) && $GLOBALS['eZDebugWarning'] )
                {
                    eZAppendWarningItem(
                        array(
                            'error' => array(
                                'type' => 'warning',
                                'number' => 1,
                                'count' => $GLOBALS['eZDebugWarningCount']
                            ),
                            'identifier' => 'ezdebug-first-warning',
                            'text' => ezpI18n::tr( 'index.php', 'Some general warnings occured, see debug for more information.' )
                        )
                    );
                }
            }

            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'site', $this->site );
            $tpl->setVariable( 'warning_list', !empty( $this->warningList ) ? $this->warningList : false );
            $tpl->setVariable( 'redirect_uri', eZURI::encodeURL( $redirectURI ) );
            $templateResult = $tpl->fetch( 'design:redirect.tpl' );

            eZDebug::addTimingPoint( "Script end" );

            eZDisplayResult( $templateResult );
            eZExecution::cleanExit();
        }

        return eZHTTPTool::redirect( $redirectURI, array(), $this->module->redirectStatus(), true, true );
    }

    /**
     * Initializes the session. If running, through Symfony the session
     * parameters from Symfony override the session parameter from eZ Publish.
     */
    protected function sessionInit()
    {
        if ( !isset( $this->settings['session'] ) || !$this->settings['session']['configured'] )
        {
            // running without Symfony2 or session is not configured
            // we keep the historic behaviour
            $ini = eZINI::instance();
            if ( $ini->variable( 'Session', 'ForceStart' ) === 'enabled' )
                eZSession::start();
            else
                eZSession::lazyStart();
        }
        else
        {
            $sfHandler = new ezpSessionHandlerSymfony(
                $this->settings['session']['has_previous']
                    || $this->settings['session']['started']
            );
            $sfHandler->setStorage( $this->settings['session']['storage'] );
            eZSession::init(
                $this->settings['session']['name'],
                $this->settings['session']['started'],
                $this->settings['session']['namespace'],
                $sfHandler
            );
        }

        // let session specify if db is required
        $this->siteBasics['db-required'] = eZSession::getHandlerInstance()->dbRequired();
    }

    protected function requestInit()
    {
        if ( $this->isInitialized )
            return;

        eZExecution::setCleanExit( false );
        $scriptStartTime = microtime( true );

        $GLOBALS['eZRedirection'] = false;
        $this->access = eZSiteAccess::current();

        eZDebug::setScriptStart( $scriptStartTime );

        eZDebug::addTimingPoint( "Script start" );

        $this->uri = eZURI::instance( eZSys::requestURI() );
        $GLOBALS['eZRequestedURI'] = $this->uri;

        // Be able to do general events early in process
        ezpEvent::getInstance()->notify( 'request/preinput', array( $this->uri ) );

        // Initialize module loading
        $this->siteBasics['module-repositories'] = eZModule::activeModuleRepositories();
        eZModule::setGlobalPathList( $this->siteBasics['module-repositories'] );

        // make sure we get a new $ini instance now that it has been reset
        $ini = eZINI::instance();

        // pre check, setup wizard related so needs to be before session/db init
        // TODO: Move validity check in the constructor? Setup is not meant to be launched at each (sub)request is it?
        if ( $ini->variable( 'SiteAccessSettings', 'CheckValidity' ) === 'true' )
        {
            $this->check = array( 'module' => 'setup', 'function' => 'init' );
            // Turn off some features that won't bee needed yet
            $this->siteBasics['policy-check-omit-list'][] = 'setup';
            $this->siteBasics['show-page-layout'] = $ini->variable( 'SetupSettings', 'PageLayout' );
            $this->siteBasics['validity-check-required'] = true;
            $this->siteBasics['session-required'] = $this->siteBasics['user-object-required'] = false;
            $this->siteBasics['db-required'] = $this->siteBasics['no-cache-adviced'] = $this->siteBasics['url-translator-allowed'] = false;
            $this->siteBasics['site-design-override'] = $ini->variable( 'SetupSettings', 'OverrideSiteDesign' );
            $this->access = eZSiteAccess::change( array( 'name' => 'setup', 'type' => eZSiteAccess::TYPE_URI ) );
            eZTranslatorManager::enableDynamicTranslations();
        }

        if ( $this->siteBasics['session-required'] )
        {
            // Don't initialize session on /login GET requests (unauthenticated entry point)
            // Only check this in Symfony/Platform 2.x context with service container
            // Pure legacy access (without service-container) will skip this detection and initialize session normally
            $isLoginPage = false;
            if ( isset( $this->settings['service-container'] ) && $this->settings['service-container'] !== null )
            {
                $isLoginPage = $this->actualRequestedURI === '/login' && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET';
            }
            
            if ( !$isLoginPage )
            {
                // Check if this should be run in a cronjob
                if ( $ini->variable( 'Session', 'BasketCleanup' ) !== 'cronjob' )
                {
                    eZSession::addCallback(
                        'destroy_pre',
                        function ( eZDBInterface $db, $key, $escapedKey )
                        {
                            $basket = eZBasket::fetch( $key );
                            if ( $basket instanceof eZBasket )
                                $basket->remove();
                        }
                    );
                    eZSession::addCallback(
                        'gc_pre',
                        function ( eZDBInterface $db, $time )
                        {
                            eZBasket::cleanupExpired( $time );
                        }
                    );

                    eZSession::addCallback(
                        'cleanup_pre',
                        function ( eZDBInterface $db )
                        {
                            eZBasket::cleanup();
                        }
                    );
                }

                // addCallBack to update session id for shop basket on session regenerate
                eZSession::addCallback(
                    'regenerate_post',
                    function ( eZDBInterface $db, $escNewKey, $escOldKey  )
                    {
                        $db->query( "UPDATE ezbasket SET session_id='{$escNewKey}' WHERE session_id='{$escOldKey}'" );
                    }
                );

                // TODO: Session starting should be made only once in the constructor
                $this->sessionInit();
            }
        }

        // if $this->siteBasics['db-required'], open a db connection and check that db is connected
        if ( $this->siteBasics['db-required'] && !eZDB::instance()->isConnected() )
        {
            $this->warningList[] = array(
                'error' => array(
                    'type' => 'kernel',
                    'number' => eZError::KERNEL_NO_DB_CONNECTION
                ),
                'text' => 'No database connection could be made, the system might not behave properly.'
            );
        }

        // pre check, RequireUserLogin & FORCE_LOGIN related so needs to be after session init
        if ( !isset( $this->check ) )
        {
            $this->check = eZUserLoginHandler::preCheck( $this->siteBasics, $this->uri );
        }

        // A POST the form token check refuses is answered with a 403 in place
        // of the module view (run(), dispatchLoop()); the rest of the request
        // set-up still runs, so the refusal page has its locale and design.
        $this->formTokenRefusal = null;
        try
        {
            ezpEvent::getInstance()->notify( 'request/input', array( $this->uri ) );
        }
        catch ( ezpFormTokenException $e )
        {
            $this->formTokenRefusal = $e;
            ezpFormTokenRefusal::log( $e );
        }

        // Initialize with locale settings
        // TODO: Move to constructor? Is it relevant to init the locale/charset for each (sub)requests?
        $this->languageCode = eZLocale::instance()->httpLocaleCode();
        $phpLocale = trim( $ini->variable( 'RegionalSettings', 'SystemLocale' ) );
        if ( $phpLocale != '' )
        {
            setlocale( LC_ALL, explode( ',', $phpLocale ) );
        }

        $this->httpCharset = eZTextCodec::httpCharset();

        // TODO: are these parameters supposed to vary across potential sub-requests?
        $this->site = array(
            'title' => $ini->variable( 'SiteSettings', 'SiteName' ),
            'design' => $ini->variable( 'DesignSettings', 'SiteDesign' ),
            'http_equiv' => array(
                'Content-Type' => 'text/html; charset=' . $this->httpCharset,
                'Content-language' => $this->languageCode
            )
        );

        // Read role settings
        $this->siteBasics['policy-check-omit-list'] = array_merge(
            $this->siteBasics['policy-check-omit-list'],
            $ini->variable( 'RoleSettings', 'PolicyOmitList' )
        );

        $this->isInitialized = true;

        /**
         * Check for activating Debug by user ID (Final checking. The first was in eZDebug::updateSettings())
         * @uses eZUser::instance() So needs to be executed after eZSession::start()|lazyStart()
         */
        eZDebug::checkDebugByUser();
    }

    /**
     * Run a callback function in legacy environment
     */
    public function runCallback( \Closure $callback, $postReinitialize = true )
    {
        $this->requestInit();

        try
        {
            $return = $callback();
        }
        catch ( Exception $e )
        {
            $this->shutdown( $postReinitialize );
            throw $e;
        }

        $this->shutdown( $postReinitialize );

        return $return;
    }

    /**
     * Runs the shutdown process
     */
    protected function shutdown( $reInitialize = true )
    {
        eZExecution::cleanup();
        eZExecution::setCleanExit();
        eZExpiryHandler::shutdown();
        self::writeOPcacheProfile();
        if ( $reInitialize )
            $this->isInitialized = false;
    }

    /** @var array|null OPcache's per-script hits at the end of the previous request in this process */
    private static $opcacheProfileLast = null;

    /**
     * Diagnostic, off unless var/tmp/opcache_profile.on exists (one file_exists() per request): one line per
     * request in var/tmp/opcache_profile.log saying how this request used OPcache -- the change in its hits and
     * misses, the scripts served from it most this request, and the files this process has included that OPcache
     * does not hold (each of those is compiled again whenever it is included). For comparing engines (PHP-FPM,
     * Velocity's persistent workers), whose OPcache figures belong to their own processes.
     */
    private static function writeOPcacheProfile()
    {
        $dir = dirname( __DIR__, 3 ) . '/var/tmp/';
        if ( !file_exists( $dir . 'opcache_profile.on' ) || !function_exists( 'opcache_get_status' ) )
            return;
        $status = @opcache_get_status( true );
        if ( !is_array( $status ) || empty( $status['opcache_enabled'] ) )
            return;
        $scripts = isset( $status['scripts'] ) ? $status['scripts'] : array();
        $hits = array();
        foreach ( $scripts as $path => $s )
            $hits[$path] = (int)$s['hits'];
        $rose = array();
        if ( self::$opcacheProfileLast !== null )
        {
            foreach ( $hits as $path => $h )
            {
                $before = isset( self::$opcacheProfileLast['scripts'][$path] ) ? self::$opcacheProfileLast['scripts'][$path] : 0;
                if ( $h > $before )
                    $rose[$path] = $h - $before;
            }
            arsort( $rose );
        }
        $notCached = array();
        foreach ( get_included_files() as $file )
            if ( !isset( $scripts[$file] ) )
                $notCached[] = $file;
        $stats = $status['opcache_statistics'];
        $line = array( 'time' => date( 'c' ), 'sapi' => PHP_SAPI, 'pid' => getmypid(),
                       'uri' => isset( $_SERVER['REQUEST_URI'] ) ? substr( $_SERVER['REQUEST_URI'], 0, 120 ) : '',
                       'hits' => (int)$stats['hits'], 'misses' => (int)$stats['misses'],
                       'hits_this_request' => self::$opcacheProfileLast !== null ? (int)$stats['hits'] - self::$opcacheProfileLast['hits'] : null,
                       'misses_this_request' => self::$opcacheProfileLast !== null ? (int)$stats['misses'] - self::$opcacheProfileLast['misses'] : null,
                       'cached_scripts' => count( $scripts ), 'included_files' => count( get_included_files() ),
                       'included_not_cached' => count( $notCached ), 'not_cached_sample' => array_slice( $notCached, 0, 40 ),
                       'served_most_this_request' => array_slice( $rose, 0, 10, true ) );
        // where the misses come from: the path forms OPcache holds for cache files, and for the first files it
        // does not hold their real path, age and what OPcache says about them
        $line['cwd'] = getcwd();
        $cacheKeys = array();
        foreach ( array_keys( $scripts ) as $k )
            if ( strpos( $k, '/cache/' ) !== false && count( $cacheKeys ) < 6 )
                $cacheKeys[] = $k;
        $line['cached_cache_file_sample'] = $cacheKeys;
        $line['cached_scripts_with_var_cache'] = count( array_filter( array_keys( $scripts ), function ( $k ) { return strpos( $k, '/var/cache/' ) !== false || strpos( $k, '/var/site/cache/' ) !== false; } ) );
        $probe = array();
        foreach ( array_slice( $notCached, 0, 5 ) as $f )
            $probe[] = array( 'file' => $f, 'realpath' => realpath( $f ), 'age_s' => file_exists( $f ) ? time() - filemtime( $f ) : null,
                              'is_cached' => function_exists( 'opcache_is_script_cached' ) ? opcache_is_script_cached( $f ) : null,
                              'is_cached_realpath' => function_exists( 'opcache_is_script_cached' ) && realpath( $f ) ? opcache_is_script_cached( realpath( $f ) ) : null );
        $line['not_cached_probe'] = $probe;
        $line['file_wrapper'] = in_array( 'file', stream_get_wrappers(), true ) ? ( function_exists( 'stream_get_meta_data' ) ? 'registered' : '' ) : 'none';
        $line['opcache_ini'] = array( 'enable' => ini_get( 'opcache.enable' ), 'enable_cli' => ini_get( 'opcache.enable_cli' ),
                                      'file_update_protection' => ini_get( 'opcache.file_update_protection' ), 'validate_timestamps' => ini_get( 'opcache.validate_timestamps' ),
                                      'revalidate_freq' => ini_get( 'opcache.revalidate_freq' ), 'use_cwd' => ini_get( 'opcache.use_cwd' ) );
        self::$opcacheProfileLast = array( 'hits' => (int)$stats['hits'], 'misses' => (int)$stats['misses'], 'scripts' => $hits );
        @file_put_contents( $dir . 'opcache_profile.log', json_encode( $line, JSON_UNESCAPED_SLASHES ) . "\n", FILE_APPEND );
    }

    /**
     * Sets whether to use exceptions in legacy kernel.
     *
     * @param bool $useExceptions
     */
    public function setUseExceptions( $useExceptions )
    {
        eZModule::$useExceptions = (bool)$useExceptions;
    }

    /**
     * Reinitializes the kernel environment.
     */
    public function reInitialize()
    {
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
        return isset( $this->settings['service-container'] );
    }

    /**
     * Returns the Symfony service container if it has been injected,
     * otherwise returns null.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface|null
     */
    public function getServiceContainer()
    {
        return $this->settings['service-container'];
    }

    /**
     * Allows user to avoid executing the pagelayout template when running the kernel
     *
     * @param bool $usePagelayout
     */
    public function setUsePagelayout( $usePagelayout )
    {
        $this->siteBasics['show-page-layout'] = (bool)$usePagelayout;
    }
}
