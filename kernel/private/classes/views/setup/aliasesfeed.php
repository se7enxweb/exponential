<?php
/**
 * The code of kernel/setup/aliasesfeed.php: the feed of Setup > Image aliases, polled by the page's script.
 *
 *   setup/aliasesfeed/(job)/<id>                         the progress of one job of image aliases
 *   setup/aliasesfeed/(log)/1/(event)/fail/(lines)/100   the newest lines of imagealias.log, filtered as the page
 *
 * JSON, short GET requests, never held open; the options are view parameters, never a query string. Needs
 * setup/aliases. Nothing is changed here: actions are POSTs to setup/aliases.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Setup
{

class Aliasesfeed extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $answer = function ( array $data, $status = 200 )
        {
            if ( $status !== 200 )
                http_response_code( $status );
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Cache-Control: no-store' );
            header( 'X-Content-Type-Options: nosniff' );
            echo json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR );
            \eZExecution::cleanExit();
        };

        $data = array( 'time' => time() );
        $jobID = (int)\expViewParameters::text( $Params, 'job', 'Job' );
        if ( $jobID > 0 )
        {
            $job = \expImageAliasAdmin::job( $jobID );
            if ( $job === null )
                $answer( array( 'error' => 'Unknown job of image aliases.' ), 404 );
            $data['job'] = $job;
            $data['paused'] = \expImageAliasThrottle::isBulkPaused();
        }
        if ( \expViewParameters::text( $Params, 'log', 'Log' ) !== '' )
        {
            $filter = \expImageAliasAdmin::logFilter( array(
                'event' => \expViewParameters::text( $Params, 'event', 'Event' ), 'run' => \expViewParameters::text( $Params, 'run', 'Run' ),
                'alias' => \expViewParameters::text( $Params, 'alias', 'Alias' ), 'text' => \expViewParameters::text( $Params, 'text', 'Text' ),
                'lines' => \expViewParameters::text( $Params, 'lines', 'Lines' ) ?: 100 ) );
            $data['log'] = \expImageAliasAdmin::readLog( $filter );
        }
        if ( count( $data ) === 1 )
            $answer( array( 'error' => 'Ask for (job)/<id> or (log)/1.' ), 400 );
        $answer( $data );

        return $this->viewResult( null, null );
    }
}

}
