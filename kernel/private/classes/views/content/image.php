<?php
/**
 * The view content/image: an image alias made on demand.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Content
{

/**
 * content/image/<token>/<file name> and the other URL forms of expImageAliasUrl: verifies the URL, serves the alias,
 * makes it within [AliasSettings] MaxWait, or answers the placeholder (expImageAliasServer). The arguments are module
 * view parameters only; the answer is sent here and the request ends (no page layout, no session).
 */
class Image extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $params = isset( $scope['Params'] ) && is_array( $scope['Params'] ) ? $scope['Params'] : array();
        $parts = array();
        foreach ( array( 'Part1', 'Part2', 'Part3', 'Part4', 'Part5', 'Part6' ) as $name )
        {
            if ( isset( $params[$name] ) && $params[$name] !== '' && $params[$name] !== false )
                $parts[] = (string)$params[$name];
        }
        // More parts than any form has: no form
        if ( isset( $params['Parameters'] ) && is_array( $params['Parameters'] ) && count( array_filter( $params['Parameters'], 'strlen' ) ) > 6 )
            $parts = array();

        $server = new \expImageAliasServer();
        \expImageAliasServer::emit( $server->handleParams( $parts ) );
        \eZExecution::cleanExit();
        return null;
    }
}

}
