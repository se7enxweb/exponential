<?php
/**
 * File containing the ezp7xRestApiProvider class.
 *
 * @copyright Copyright (C) 7x and eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

class ezp7xRestApiProvider implements ezpRestProviderInterface
{
    /**
     * Returns registered versioned routes for provider
     *
     * @return array Associative array. Key is the route name (beware of name collision !). Value is the versioned route.
     */
    public function getRoutes()
    {
        $routes = array(
            'ezpListAtom' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute(
                    '/content/node/:nodeId/listAtom', 'ezpRestAtomController',
                    array( 'http-get' => 'collection' )
                ), 2
            ),
            'ezpNodeCreate' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute(
                    '/content/node/create', 'ezp7xRestContentController',
                    array( 'http-post' => 'CreateContentNode' )
                ),
                2
            ),
            'ezpNodeDelete' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute(
                    '/content/node/delete/:nodeId', 'ezp7xRestContentController',
                    array( 'http-post' => 'DeleteContentNode' )
                ),
                2
            ),
            // @TODO : Make possible to interchange optional params positions
            'ezpList' => new ezpRestVersionedRoute(
                new ezpMvcRegexpRoute(
                    '@^/content/node/(?P<nodeId>\d+)/list(?:/offset/(?P<offset>\d+))?(?:/limit/(?P<limit>\d+))?(?:/sort/(?P<sortKey>\w+)(?:/(?P<sortType>asc|desc))?)?$@',
                    'ezp7xRestContentController', array( 'http-get' => 'list' )
                ),
                2
            ),
            'ezpNode' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute(
                    '/content/node/:nodeId', 'ezp7xRestContentController',
                    array( 'http-get' => 'viewContent',
		           'http-post' => 'UpdateContentNode' )
                ),
                2
            ),
            'ezpFieldsByNode' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute(
                    '/content/node/:nodeId/fields', 'ezpRestContentController',
                    array( 'http-get' => 'viewFields' )
                ),
                2
            ),
            'ezpFieldByNode' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute(
                    '/content/node/:nodeId/field/:fieldIdentifier',
                    'ezpRestContentController',
                    array( 'http-get' => 'viewField' )
                ),
                2
            ),
            'ezpChildrenCount' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute(
                    '/content/node/:nodeId/childrenCount',
                    'ezpRestContentController',
                    array( 'http-get' => 'countChildren' )
                ),
                2
            ),
            'ezpObject' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute(
                    '/content/object/:objectId', 'ezpRestContentController',
                    array( 'http-get' => 'viewContent' )
                ),
                2
            ),
            'ezpFieldsByObject' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute(
                    '/content/object/:objectId/fields',
                    'ezpRestContentController',
                    array( 'http-get' => 'viewFields' )
                ),
                2
            ),
            'ezpFieldByObject' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute(
                    '/content/object/:objectId/field/:fieldIdentifier',
                    'ezpRestContentController',
                    array( 'http-get' => 'viewField' )
                ),
                2
            )
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
