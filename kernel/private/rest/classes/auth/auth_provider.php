<?php
/**
 * File containing ezpRestAuthProvider class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
class ezpRestAuthProvider implements ezpRestProviderInterface
{
    /**
     * @see ezpRestProviderInterface::getRoutes()
     */
    public function getRoutes()
    {
        // basicAuth and oauthLogin are reached by internal redirects that keep the method of the refused request:
        // they answer every method, so a refused POST or DELETE gets its 401 or 403 and not a 405
        $routes = array(
            'basicAuth'    => new ezpMvcRailsRoute( '/http-basic-auth', 'ezpRestAuthController', ezpMvcRailsRoute::anyMethod( 'basicAuth' ) ),
            'oauthLogin'   => new ezpMvcRailsRoute( '/oauth/login', 'ezpRestAuthController', ezpMvcRailsRoute::anyMethod( 'oauthRequired' ) ),
            'oauthToken'   => new ezpMvcRailsRoute( '/oauth/token', 'ezpRestOauthTokenController', array( 'http-post' => 'handleRequest' ))
        );
        return $routes;
    }

	/**
     * Returns associated with provider view controller
     *
     * @return ezpRestViewController
     */
    public function getViewController()
    {
        return new ezpRestApiViewController();
    }
}
?>
