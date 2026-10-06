<?php
/**
 * File containing ezpRestMvcController class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Abstract class that must be extended by every REST controller
 */
abstract class ezpRestMvcController extends ezcMvcController
{
    /**
     * Cache ID for ezcCache
     *
     * @var string
     */
    const CACHE_ID = 'ezpRestMvcController';

    /**
     * Default response groups returned by the controller
     *
     * @var array
     */
    private $defaultResponsegroups = array();

    /**
     * @var eZINI
     */
    protected $restINI;

    /**
     * Flag to indicate wether application cache has been created by ezcCacheManager or not
     *
     * @var bool
     */
    public static $isCacheCreated = false;

    /**
     * Constructor
     *
     * @param string $action
     * @param ezcMvcRequest $request
     */
    public function __construct( $action, ezcMvcRequest $request )
    {
        $this->restINI = eZINI::instance( 'rest.ini' );
        parent::__construct( $action, $request );
    }

    /**
     * Checks if a response group has been provided in the requested REST URI
     *
     * @param string $name Response group name
     *
     * @return bool
     */
    protected function hasResponseGroup( $name )
    {
        return in_array( $name, $this->defaultResponsegroups ) ||
            (
                isset( $this->request->variables['ResponseGroups'] ) &&
                in_array( $name, $this->request->variables['ResponseGroups'] )
            );
    }

    /**
     * Returns requested response groups
     *
     * @return array
     */
    protected function getResponseGroups()
    {
        $resGroups = $this->request->variables['ResponseGroups'];
        for ( $i = 0, $iMax = count( $this->defaultResponsegroups ); $i < $iMax; ++$i )
        {
            if ( !in_array( $this->defaultResponsegroups[$i], $resGroups ) )
                $resGroups[] = $this->defaultResponsegroups[$i];
        }

        return $resGroups;
    }

    /**
     * Sets default response groups
     *
     * @param array $defaultResponseGroups
     *
     * @return void
     */
    protected function setDefaultResponseGroups( array $defaultResponseGroups )
    {
        $this->defaultResponsegroups = $defaultResponseGroups;
    }

    /**
     * Checks if a content variable has been provided in requested REST URI
     *
     * @param string $name Content variable name
     *
     * @return bool
     */
    protected function hasContentVariable( $name )
    {
        return isset( $this->request->contentVariables[$name] );
    }

    /**
     * Returns requested content variable, is it set
     *
     * @param string $name Content variable name
     *
     * @return string|null
     */
    protected function getContentVariable( $name )
    {
        if ( isset( $this->request->contentVariables[$name] ) )
        {
            return $this->request->contentVariables[$name];
        }

        return null;
    }

    /**
     * Returns all provided content variables in requested REST URI
     *
     * @return array
     */
    protected function getAllContentVariables()
    {
        return $this->request->contentVariables;
    }

    /**
     * Override to add the "requestedResponseGroups" variable for every REST requests
     *
     * @see lib/ezc/MvcTools/src/interfaces/ezcMvcController::createResult()
     */
    public function createResult()
    {
        $debug = ezpRestDebug::getInstance();
        $debug->startTimer( 'GeneratingRestResult', 'RestController' );

        if ( !self::$isCacheCreated )
        {
            ezcCacheManager::createCache(
                self::CACHE_ID,
                $this->getCacheLocation(),
                'ezpRestCacheStorageClusterObject',
                array( 'ttl' => $this->getActionTTL() )
            );
            self::$isCacheCreated = true;
        }

        $cache = ezcCacheManager::getCache( self::CACHE_ID );
        $controllerCacheId = $this->generateCacheId();
        $isCacheEnabled = $this->isCacheEnabled();

        // Try to restore application cache.
        // If expired or not yet available, generate it and store it
        $cache->isCacheEnabled = $isCacheEnabled;
        if ( ( $res = $cache->restore( $controllerCacheId ) ) === false )
        {
            try
            {
                $debug->log( 'Generating cache', ezcLog::DEBUG );
                $debug->switchTimer( 'GeneratingCache', 'GeneratingRestResult' );

                $res = parent::createResult();
                $resGroups = $this->getResponseGroups();
                if ( !empty( $resGroups ) )
                {
                    $res->variables['requestedResponseGroups'] = $resGroups;
                }

                if ( $res instanceof ezpRestMvcResult )
                {
                    $res->responseGroups = $resGroups;
                }

                // Only a plain answer is kept. A result with a status object (a refusal such as 403 access_denied,
                // a 201, a 501 ...) is not: its status classes cannot be read back from the cache file
                // (var_export() writes ::__set_state() calls they do not have), and a refusal must follow the
                // user's rights of the next request anyway.
                if ( $isCacheEnabled && self::isCacheable( $res ) )
                    $cache->store( $controllerCacheId, $res );
                else if ( $isCacheEnabled )
                    $cache->abortCacheGeneration();

                $debug->stopTimer( 'GeneratingCache' );
            }
            catch ( Exception $e )
            {
                $debug->log( 'Exception caught, aborting cache generation', ezcLog::DEBUG );
                if ( $isCacheEnabled )
                    $cache->abortCacheGeneration();
                throw $e;
            }
        }

        // Add debug infos to output if debug is enabled
        $debug->stopTimer( 'GeneratingRestResult' );
        if ( ezpRestDebug::isDebugEnabled() )
        {
            $res->variables['debug'] = $debug->getReport();
        }

        return $res;
    }

    /**
     * Default action to handle OPTIONS requests.
     *
     * @return ezcMvcResult
     */
    public function doHttpOptions()
    {
        $methods = implode( ', ', $this->supported_http_methods );
        $result = new ezpRestMvcResult;
        $result->status = new ezpRestStatusResponse(
            ezpHttpResponseCodes::OK,
            'Allowed methods are: ' . $methods,
            array( 'Allow' => $methods )
        );
        return $result;
    }

    /**
     * Returns cache location for current API/version/controller/action
     *
     * @return string Path in the cluster
     */
    public function getCacheLocation()
    {
        $routingInfos = $this->getRouter()->getRoutingInformation();
        return ezpRestPrefixFilterInterface::getApiProviderName() .
            '/v' . ezpRestPrefixFilterInterface::getApiVersion() .
            '/' . str_replace( "\\", ".", $routingInfos->controllerClass ) .
            '/' . $routingInfos->action;
    }

    /**
     * Returns cache TTL value for current action as set in rest.ini
     *
     * Default value will be [CacheSettings].DefaultCacheTTL.
     * This can be refined by setting a [<controllerClass>_<action>_CacheSettings] section (see comments in rest.ini).
     *
     * @return int TTL in seconds
     */
    private function getActionTTL()
    {
        $routingInfos = $this->getRouter()->getRoutingInformation();

        // Check if we have TTL settings for this controller/action
        $actionSectionName = $routingInfos->controllerClass . '_' . $routingInfos->action . '_CacheSettings';
        if ( $this->restINI->hasVariable( $actionSectionName, 'CacheTTL' ) )
        {
            return (int)$this->restINI->variable( $actionSectionName, 'CacheTTL' );
        }

        $controllerSectionName = $routingInfos->controllerClass . '_CacheSettings';
        if ( $this->restINI->hasVariable( $controllerSectionName, 'CacheTTL' ) ) // Nothing at controller/action level, check at controller level
        {
            return (int)$this->restINI->variable( $controllerSectionName, 'CacheTTL' );
        }

        return (int)$this->restINI->variable( 'CacheSettings', 'DefaultCacheTTL' );
    }

    /**
     * Whether a result may go into the answer cache: an ezcMvcResult without a status object.
     *
     * @param mixed $result
     * @return bool
     */
    public static function isCacheable( $result )
    {
        return $result instanceof ezcMvcResult && !is_object( $result->status );
    }

    /**
     * The user part of the cache ID: the id of the current user (the anonymous user shares one entry).
     *
     * @return int
     */
    protected function cacheUser()
    {
        return (int)eZUser::currentUserID();
    }

    /**
     * Generates unique cache ID for current request.
     *
     * The cache ID is a MD5 hash and takes into account :
     *  - API Name
     *  - API Version
     *  - Controller class
     *  - Action
     *  - The current user (results follow the user's policies)
     *  - Internal variables (passed parameters, ResponseGroups...)
     *  - Content variables (Translation...)
     *
     * @return string
     */
    private function generateCacheId()
    {
        $routingInfos = $this->getRouter()->getRoutingInformation();

        $aCacheId = array(
            ezpRestPrefixFilterInterface::getApiProviderName(),
            ezpRestPrefixFilterInterface::getApiVersion(),
            $routingInfos->controllerClass,
            $routingInfos->action,
            // The answer depends on who asks: an action checks content/read and the other policies of the current
            // user, so the result of one user must never be served to another
            'user=' . $this->cacheUser()
        );
        // Add internal variables, caught in the URL. See ezpRestHttpRequestParser::fillVariables()
        // Also add content variables
        foreach ( $this->request->contentVariables + $this->request->variables as $name => $val )
        {
            $aCacheId[] = $name . '=' . ( is_array( $val ) ? implode( ',', $val ) : $val );
        }

        return md5( implode( '-', $aCacheId ) );
    }

    /**
     * Checks if application cache is enabled for this controller/action, as set in rest.ini
     *
     * Default value will be [CacheSettings].ApplicationCache
     * This can be refined by setting a [<controllerClass>_<action>_CacheSettings] section (see comments in rest.ini).
     *
     * @return bool
     */
    private function isCacheEnabled()
    {
        // Global switch
        if ( $this->restINI->variable( 'CacheSettings', 'ApplicationCache' ) !== 'enabled' )
        {
            return false;
        }

        // Only reads are kept: a cached create or delete would answer the second call without doing anything
        if ( !in_array( $this->request->protocol, array( 'http-get', 'http-head' ), true ) )
        {
            return false;
        }

        $routingInfos = $this->getRouter()->getRoutingInformation();

        // Check if we have a specific setting for this controller/action
        $actionSectionName = $routingInfos->controllerClass . '_' . $routingInfos->action . '_CacheSettings';
        if ( $this->restINI->hasVariable( $actionSectionName, 'ApplicationCache' ) )
        {
            return $this->restINI->variable( $actionSectionName, 'ApplicationCache' ) === 'enabled';
        }

        // Nothing at controller/action level, check at controller level
        $controllerSectionName = $routingInfos->controllerClass . '_CacheSettings';
        if ( $this->restINI->hasVariable( $controllerSectionName, 'ApplicationCache' ) )
        {
            return $this->restINI->variable( $controllerSectionName, 'ApplicationCache' ) === 'enabled';
        }

        // Nothing at controller level, take the default value
        return $this->restINI->variable( 'CacheSettings', 'ApplicationCacheDefault' ) === 'enabled';
    }
}
?>
