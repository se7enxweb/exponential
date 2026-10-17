<?php
/**
 * The actions of the log viewer (setup/logsaction; doc/features/6.0/log-viewer.md, "Actions"). POST only, with the
 * form token; each action must be switched on in [ActionSettings] and is audited:
 *   DownloadButton  File, From, To (bytes; empty: the last MaxDownloadBytes)   setup/logs        system.log.download
 *   RotateButton    File (a current file)                                     setup/managelogs  system.log.rotate
 *   TruncateButton  File (a current file)                                     setup/managelogs  system.log.truncate
 *   DeleteButton    File (an archived rotation)                               setup/managelogs  system.log.delete
 * Rotate, empty and remove first show a confirmation page (setup/logs_confirm.tpl); its ConfirmButton carries the
 * action on, CancelButton goes back. Then the page of the file is opened again with done=<action>&ok=1|0.
 * A download is masked as the viewer is (unmask=1 only with setup/unmasklogs) and compressed with gzip.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Setup
{

class Logsaction extends \Exponential\Runnable\ModuleView
{
    /** button => action */
    const BUTTONS = array( 'DownloadButton' => 'download', 'RotateButton' => 'rotate', 'TruncateButton' => 'truncate', 'DeleteButton' => 'delete' );

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        $viewer = \expLogViewer::forCurrentUser( $http->hasPostVariable( 'Unmask' ) && (string)$http->postVariable( 'Unmask' ) === '1' );
        if ( !$viewer->enabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );
        if ( !isset( $_SERVER['REQUEST_METHOD'] ) || $_SERVER['REQUEST_METHOD'] !== 'POST' )
            return $Module->redirectTo( '/setup/logs' );

        $fileID = $http->hasPostVariable( 'File' ) && is_string( $http->postVariable( 'File' ) ) ? (string)$http->postVariable( 'File' ) : '';
        $backFile = $fileID !== '' ? $viewer->locator()->resolve( $fileID ) : null;
        $back = $backFile !== null ? '/' . \expLogPermalink::build( $backFile['key'], array( 'rotation' => $backFile['rotation'] ) ) : '/setup/logs';
        if ( $http->hasPostVariable( 'CancelButton' ) )
            return $Module->redirectTo( $back );

        $action = '';
        foreach ( self::BUTTONS as $button => $name )
            if ( $http->hasPostVariable( $button ) )
                $action = $name;
        if ( $action === '' && $http->hasPostVariable( 'ConfirmButton' ) && $http->hasPostVariable( 'LogAction' ) )
        {
            $asked = (string)$http->postVariable( 'LogAction' );
            if ( in_array( $asked, array( 'rotate', 'truncate', 'delete' ), true ) )
                $action = $asked;
        }
        if ( $action === '' )
            return $Module->redirectTo( $back );

        $actions = $viewer->actions();
        if ( empty( $actions[$action] ) )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        if ( !\expLogViewer::checkFormToken() )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        $file = $viewer->locator()->resolve( $fileID );
        if ( $file === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        // a log configured by key (the web server's) is only downloaded, an archive only downloaded or removed
        $fileActions = \expLogViewer::actionsFor( $file, $actions );
        if ( empty( $fileActions[$action] ) )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        if ( $action === 'download' )
        {
            $config = $viewer->config();
            $size = $file['gz'] ? PHP_INT_MAX : (int)$file['size'];
            $range = \expLogActions::range( $size, $http->hasPostVariable( 'From' ) ? $http->postVariable( 'From' ) : '',
                                            $http->hasPostVariable( 'To' ) ? $http->postVariable( 'To' ) : '',
                                            $config->int( 'ActionSettings', 'MaxDownloadBytes' ) );
            if ( $file['gz'] && $range['to'] === PHP_INT_MAX )
            {
                $range['to'] = $range['from'] + $config->int( 'ActionSettings', 'MaxDownloadBytes' );
            }
            \expLogViewer::audit( 'download', $file, array( 'from' => $range['from'], 'to' => $range['to'], 'unmasked' => $viewer->unmasked() ) );
            // the audit record is written at the end of the request; cleanExit() runs that end
            \expLogActions::download( $file, $range['from'], $range['to'], $viewer->redactor() );
            \eZExecution::cleanExit();
        }

        if ( !$http->hasPostVariable( 'ConfirmButton' ) )
        {
            $token = \expLogViewer::formToken();
            $tpl = \eZTemplate::factory();
            $tpl->setVariable( 'log_action', $action );
            $tpl->setVariable( 'log_file', \expLogViewer::fileInfo( $file ) );
            $tpl->setVariable( 'log_rotations', $action === 'rotate' ? max( 1, class_exists( '\eZLog' ) ? (int)\eZLog::maxLogrotateFiles() : 3 ) : 0 );
            $tpl->setVariable( 'log_token_field', $token['field'] );
            $tpl->setVariable( 'log_token', $token['token'] );
            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:setup/logs_confirm.tpl' );
            $Result['left_menu'] = 'design:setup/logs_menu.tpl';
            $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'kernel/setup', 'Setup' ) ),
                                     array( 'url' => 'setup/logs', 'text' => \ezpI18n::tr( 'kernel/setup', 'Log files' ) ),
                                     array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Confirm' ) ) );
            return $Result;
        }

        if ( $action === 'rotate' )
            $outcome = \expLogActions::rotate( $file );
        else if ( $action === 'truncate' )
            $outcome = \expLogActions::truncate( $file );
        else
            $outcome = \expLogActions::deleteArchive( $file );
        $after = array( 'ok' => (bool)$outcome['ok'] );
        if ( $outcome['error'] !== '' )
            $after['error'] = $outcome['error'];
        if ( isset( $outcome['renamed'] ) )
            $after['renamed'] = $outcome['renamed'];
        \expLogViewer::audit( $action, $file, $after );
        // after a removed rotation, the current file of it
        $rotation = $action === 'truncate' ? $file['rotation'] : 0;
        return $Module->redirectTo( '/' . \expLogPermalink::build( $file['key'], array( 'rotation' => $rotation, 'done' => $action, 'ok' => (bool)$outcome['ok'] ) ) );
    }
}

}
