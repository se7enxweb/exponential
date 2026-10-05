<?php
/**
 * File containing the notification/job view.
 *
 * The status page's "Run now" back end (expNotificationJob), answering JSON:
 *   POST notification/job with Action=start (and DryRun=1)   -> { "id": "..." }
 *   GET  notification/job/<id>/<offset>                      -> { "events": [...], "offset": n, "done": bool }
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Notification
{

class Job extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $answer = function ( array $data, $status = 200 )
        {
            if ( $status !== 200 )
                http_response_code( $status );
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Cache-Control: no-store' );
            echo json_encode( $data );
            \eZExecution::cleanExit();
        };

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
        {
            $action = $http->hasPostVariable( 'Action' ) ? (string)$http->postVariable( 'Action' ) : '';
            if ( $action === 'start' )
            {
                $siteaccess = '';
                $access = $GLOBALS['eZCurrentAccess'] ?? array();
                if ( isset( $access['name'] ) )
                    $siteaccess = (string)$access['name'];
                $error = '';
                $id = \expNotificationJob::start( $siteaccess, $http->hasPostVariable( 'DryRun' ), $error );
                if ( $id === false )
                    $answer( array( 'error' => $error ), 500 );
                $answer( array( 'id' => $id ) );
            }
            $answer( array( 'error' => 'Unknown action.' ), 400 );
        }

        $progress = \expNotificationJob::progress( (string)$Params['JobID'], (int)$Params['Offset'] );
        if ( $progress === false )
            $answer( array( 'error' => 'Unknown run.' ), 404 );
        $answer( $progress );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
