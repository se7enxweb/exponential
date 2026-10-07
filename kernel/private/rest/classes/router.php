<?php
/**
 * File containing rest router
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
class ezpRestRouter extends ezcMvcRouter
{
    const ROUTE_CACHE_ID = 'ezpRestRouteApcCache',
          ROUTE_CACHE_KEY = 'ezpRestRouteApcCacheKey',
          ROUTE_CACHE_PATH = 'restRouteAPC';

    /**
     * Flag to indicate if APC route cache has already been created
     * @var bool
     */
    public static $isRouteCacheCreated = false;

    /**
     * (non-PHPdoc)
     * @see lib/ezc/MvcTools/src/ezcMvcRouter::createRoutes()
     */
    public function createRoutes()
    {
        if( empty( $this->routes ) )
        {
            // Check if route caching is enabled and if APC is available
            $isRouteCacheEnabled = eZINI::instance( 'rest.ini' )->variable( 'CacheSettings', 'RouteApcCache' ) === 'enabled';
            if( $isRouteCacheEnabled && ezcBaseFeatures::hasExtensionSupport( 'apc' ) )
            {
                $this->routes = $this->getCachedRoutes();
            }
            else
            {
                $this->routes = $this->doCreateRoutes();
            }
        }

        return $this->routes;
    }

    /**
     * The first route that matches the URI and the method.
     *
     * A route whose pattern matches but which does not take the request's method no longer ends the search with
     * "405 Method Not Allowed": the routes after it are tried too, so a provider can register a read route and a
     * write route for the same path, or a general pattern before a specific one (/content/node/:nodeId before
     * /content/node/create), in any order. 405 is answered only when no route takes the method; its Allow header
     * lists the methods of every route that matched the path.
     *
     * @return ezcMvcRoutingInformation
     * @throws ezpRouteMethodNotAllowedException|ezcMvcRouteNotFoundException
     */
    public function getRoutingInformation()
    {
        try
        {
            $routes = $this->createRoutes();
        }
        catch ( ezpRestProviderNotFoundException $e )
        {
            // The first element of the path names no API provider (/api/v1/config, /api/fs/read): there is no
            // route for it, a 404 like any other unknown path, not an uncaught exception and a 500
            throw new ezcMvcRouteNotFoundException( $this->request );
        }
        $allowed = null;
        foreach ( $routes as $route )
        {
            try
            {
                $routingInformation = $route->matches( $this->request );
            }
            catch ( ezpRouteMethodNotAllowedException $e )
            {
                $allowed = array_merge( (array)$allowed, $e->getAllowedMethods() );
                continue;
            }
            if ( $routingInformation !== null )
            {
                $routingInformation->router = $this;
                return $routingInformation;
            }
        }

        if ( $allowed !== null )
            throw new ezpRouteMethodNotAllowedException( array_values( array_unique( $allowed ) ) );
        throw new ezcMvcRouteNotFoundException( $this->request );
    }

    /**
     * Do create the REST routes
     * @return array The route objects
     */
    protected function doCreateRoutes()
    {
        $providerRoutes = ezpRestProvider::getProvider( ezpRestPrefixFilterInterface::getApiProviderName() )->getRoutes();
        // the error page is reached by internal redirects that may keep the method of the failed request
        $providerRoutes['fatal'] = new ezpMvcRailsRoute( '/fatal', 'ezpRestErrorController', ezpMvcRailsRoute::anyMethod( 'show' ) );

        return ezcMvcRouter::prefix(
            eZINI::instance( 'rest.ini' )->variable( 'System', 'ApiPrefix' ),
            $providerRoutes
        );
    }

    /**
     * Extract REST routes from APC cache.
     * Cache is generated if needed
     * @return array The route objects
     */
    protected function getCachedRoutes()
    {
        $ttl = (int)eZINI::instance( 'rest.ini' )->variable( 'CacheSettings', 'RouteApcCacheTTL' );

        if( self::$isRouteCacheCreated === false )
        {
            $options = array( 'ttl' => $ttl );
            ezcCacheManager::createCache( self::ROUTE_CACHE_ID, self::ROUTE_CACHE_PATH, 'ezpRestCacheStorageApcCluster', $options );
            self::$isRouteCacheCreated = true;
        }

        $cache = ezcCacheManager::getCache( self::ROUTE_CACHE_ID );
        $cacheKey = self::ROUTE_CACHE_KEY . '_' . ezpRestPrefixFilterInterface::getApiProviderName();
        if( ( $prefixedRoutes = $cache->restore( $cacheKey ) ) === false )
        {
            try
            {
                $prefixedRoutes = $this->doCreateRoutes();
                $cache->store( $cacheKey, $prefixedRoutes );
            }
            catch( Exception $e )
            {
                // Sometimes APC can miss a write. No big deal, just log it.
                // Cache will be regenerated next time
                ezpRestDebug::getInstance()->log( $e->getMessage(), ezcLog::ERROR );
            }
        }

        return $prefixedRoutes;
    }
}
?>
