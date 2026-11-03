<?php
/**
 * File containing the migration/trigger view.
 *
 * The signed HTTP trigger (expMigrationTrigger): a source installation that reaches this one by FTP only runs its
 * deployed migrations here. The view answers a program, never a person: JSON with the HTTP status of
 * expMigrationTrigger::handle(), without page layout or debug report, never kept by a cache. It reads nothing of the
 * user and releases the session at once; the signature, the source address (REMOTE_ADDR) and the nonce are the
 * permission (migration.ini [TriggerSettings]). Each request that reaches the checks is audited as system.migration.deploy (verb trigger-<action>, object migration_trigger).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Trigger extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        \eZDebug::updateSettings( array( 'debug-enabled' => false ) );
        \Exponential\View\Kernel\User\Megamenu::releaseSession();

        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? (string)$_SERVER['REQUEST_METHOD'] : 'GET';
        $address = isset( $_SERVER['REMOTE_ADDR'] ) ? (string)$_SERVER['REMOTE_ADDR'] : '';
        $signature = isset( $_SERVER[\expMigrationTrigger::SERVER_KEY] ) && is_string( $_SERVER[\expMigrationTrigger::SERVER_KEY] ) ? $_SERVER[\expMigrationTrigger::SERVER_KEY] : null;
        $body = '';
        if ( strtoupper( $method ) === 'POST' )
        {
            $stream = @fopen( 'php://input', 'rb' );
            if ( $stream )
            {
                $body = (string)stream_get_contents( $stream, \expMigrationTrigger::MAX_BODY + 1 );
                fclose( $stream );
            }
        }
        $trigger = new \expMigrationTrigger( \expMigrationTrigger::settings() );
        $answer = $trigger->handle( $method, $address, $signature, $body );
        if ( $answer['status'] !== 405 && $answer['status'] !== 404 )
        {
            $ok = $answer['status'] === 200 && isset( $answer['body']['exit'] ) && (int)$answer['body']['exit'] === \expUpgrade::EXIT_OK;
            \expUpgrade::audit( 'system.migration.deploy', array( 'verb' => 'trigger-' . ( $answer['action'] !== '' ? $answer['action'] : 'refused' ),
                                                                  'object' => array( 'type' => 'migration_trigger', 'id' => $answer['action'] ),
                                                                  'result' => $ok ? 'success' : 'failure',
                                                                  'x' => array( 'status' => $answer['status'], 'address' => $address,
                                                                                'error' => isset( $answer['body']['error'] ) ? (string)$answer['body']['error'] : '' ) ) );
        }
        return $this->send( $answer['status'], $answer['body'] );
    }

    /**
     * Sends the answer and ends the request (eZExecution::cleanExit(): never inside a catch of \Throwable).
     *
     * @param int $status
     * @param array $body
     * @return mixed never returns in a web request
     */
    protected function send( $status, array $body )
    {
        \eZExecution::discardOutputBuffers();
        if ( !headers_sent() )
        {
            http_response_code( (int)$status );
            header( 'Cache-Control: no-store' );
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'X-Content-Type-Options: nosniff' );
            if ( $status === 405 )
                header( 'Allow: POST' );
            if ( $status === 429 )
                header( 'Retry-After: 60' );
        }
        echo json_encode( $body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        \eZExecution::cleanExit();
        return null;
    }
}

}
