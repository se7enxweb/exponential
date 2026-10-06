<?php
/**
 * File containing the ezpRestVersionedRoute class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Route wrapping around existing instance of ezcMvcRoute providing multiple versions of it.
 *
 * The version is one number, or a list of them: new ezpRestVersionedRoute( $route, array( 1, 2 ) ) answers at
 * /v1/... and /v2/..., so a provider that adds a version keeps the routes of the old one without registering each
 * twice. getVersions() tells a provider that the kernel takes a list.
 */
class ezpRestVersionedRoute implements ezcMvcRoute, ezcMvcReversibleRoute
{
    /**
     * @var ezcMvcRoute Contained route implementing ezcMvcRoute interface
     */
    protected $route;

    /**
     * @var int The version number (the first one, when the route answers for several)
     */
    protected $version;

    /**
     * @var int[] Every version the route answers for
     */
    protected $versions;

    /**
     * @param ezcMvcRoute $route
     * @param int|int[] $version one version, or the list of versions the route answers for
     */
    public function __construct( ezcMvcRoute $route, $version )
    {
        $this->route = $route;
        $versions = array();
        foreach ( is_array( $version ) ? $version : array( $version ) as $one )
            $versions[] = (int)$one;
        $versions = array_values( array_unique( $versions ) );
        if ( !$versions )
            $versions = array( 1 );
        $this->versions = $versions;
        $this->version = $versions[0];
    }

    /**
     * The versions the route answers for.
     *
     * @return int[]
     */
    public function getVersions()
    {
        // a route kept in the route cache before the list existed carries only $version
        return is_array( $this->versions ) && $this->versions ? $this->versions : array( (int)$this->version );
    }

    public function matches( ezcMvcRequest $request )
    {
        // The version token was taken out of the URI by the prefix filter (/api/ezp/v2/foo -> /api/foo), which
        // remembers it: the route matches only when that version is one of its own.
        if ( in_array( (int)ezpRestPrefixFilterInterface::getApiVersion(), $this->getVersions(), true ) )
            return $this->route->matches( $request );
        return null;
    }

    /**
     * Adds a prefix to the route.
     *
     * @param mixed $prefix Prefix to add, for example: '/blog'
     * @return void
     */
    public function prefix( $prefix )
    {
        $this->route->prefix( $prefix );
    }

    /**
     * Generates an URL back out of a route, including possible arguments
     *
     * The URL carries the version of the current request when the route answers for it (a link made while
     * answering /v1/... stays in v1), else the route's first version.
     *
     * @param array $arguments
     */
    public function generateUrl( ?array $arguments = null )
    {
        // ezpRestPrefixFilterInterface::getScheme() ==> '/v'
        $apiPrefix = ezpRestPrefixFilterInterface::getApiPrefix() . '/';
        $apiProviderName = ezpRestPrefixFilterInterface::getApiProviderName();
        $current = (int)ezpRestPrefixFilterInterface::getApiVersion();
        $versions = $this->getVersions();
        $version = in_array( $current, $versions, true ) ? $current : $versions[0];

        return $apiPrefix . ( !$apiProviderName ? ''  : $apiProviderName . '/' ) . 'v' . $version . '/' . str_replace( $apiPrefix, '', $this->route->generateUrl( $arguments ) );
    }
}
