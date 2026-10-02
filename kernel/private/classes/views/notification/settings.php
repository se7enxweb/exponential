<?php
/**
 * The code of kernel/notification/settings.php, moved into a class (#207 stage 1). The file kernel/notification/settings.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/notification/settings.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Notification
{

class Settings extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();

        $Module = $Params['Module'];

        $user = \eZUser::currentUser();

        $availableHandlers = \eZNotificationEventFilter::availableHandlers();


        $db = \eZDB::instance();
        $db->begin();
        if ( $http->hasPostVariable( 'Store' ) )
        {
            foreach ( $availableHandlers as $handler )
            {
                $handler->storeSettings( $http, $Module );
            }

        }

        foreach ( $availableHandlers as $handler )
        {
            $handler->fetchHttpInput( $http, $Module );
        }
        $db->commit();

        $viewParameters = array( 'offset' => $Params['Offset'] );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'user', $user );
        $tpl->setVariable( 'view_parameters', $viewParameters );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:notification/settings.tpl' );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/notification', 'Notification settings' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
