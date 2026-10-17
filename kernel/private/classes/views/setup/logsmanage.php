<?php
/**
 * Setup > Log files > Manage (setup/logsmanage; doc/features/6.0/log-viewer.md, "Managing logs"): every log with its
 * size, estimated lines, last write, growth, archives and limits, and the actions rotate now, empty (archive first),
 * compress the archives, remove the archives older than N days, rotate all, prune all, and the download of the
 * viewer (setup/logsaction).
 *
 * GET shows the logs. A POST of an action (Op: rotate | truncate | compress | prune | rotate_all | prune_all, with Key
 * and Days) shows its plan, every step, and changes nothing (the dry run); its ConfirmButton posts it again and the
 * plan is applied, each step checked against the disk again, and recorded in the audit. Every POST carries the form
 * token; the view needs setup/managelogs, and each action its switch in [ActionSettings] (Rotate, Truncate,
 * Compress, DeleteArchives). The web server's logs are never managed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Setup
{

class Logsmanage extends \Exponential\Runnable\ModuleView
{
    /** action => array( the plan's op, the [ActionSettings] switch, all logs ) */
    const OPS = array( 'rotate' => array( 'rotate', 'Rotate', false ), 'truncate' => array( 'truncate', 'Truncate', false ),
                       'compress' => array( 'compress', 'Compress', false ), 'prune' => array( 'prune', 'DeleteArchives', false ),
                       'rotate_all' => array( 'rotate', 'Rotate', true ), 'prune_all' => array( 'prune', 'DeleteArchives', true ) );

    /** The session variable that carries an outcome to the page shown after it */
    const RESULT = 'expLogManageResult';

    /**
     * What a request may do. Pure.
     *
     * @param string $method GET or POST
     * @param string $op the action asked for ('' for none)
     * @param bool $tokenOk the POST carries the form token
     * @param bool $confirmed ConfirmButton was pressed
     * @param array $switches [ActionSettings] switch => bool
     * @param bool $enabled the viewer is on
     * @return string show | preview | apply | disabled | bad_op | denied | off
     */
    public static function decide( $method, $op, $tokenOk, $confirmed, array $switches, $enabled = true )
    {
        if ( !$enabled )
            return 'disabled';
        if ( $method !== 'POST' || $op === '' )
            return 'show';
        if ( !isset( self::OPS[$op] ) )
            return 'bad_op';
        if ( !$tokenOk )
            return 'denied';
        if ( empty( $switches[self::OPS[$op][1]] ) )
            return 'off';
        return $confirmed ? 'apply' : 'preview';
    }

    /**
     * The [ActionSettings] switches.
     *
     * @param \expLogViewerConfig $config
     * @return array
     */
    public static function switches( \expLogViewerConfig $config )
    {
        $out = array();
        foreach ( array( 'Rotate', 'Truncate', 'DeleteArchives', 'Download' ) as $name )
            $out[$name] = $config->enabled( 'ActionSettings', $name );
        // Compress is newer than the configuration's defaults: read from logviewer.ini when the configuration has none
        $compress = $config->get( 'ActionSettings', 'Compress' );
        if ( $compress === null && class_exists( 'eZINI' ) && class_exists( 'eZSys', false ) && \eZINI::exists( 'logviewer.ini' ) )
        {
            $ini = \eZINI::instance( 'logviewer.ini' );
            if ( $ini->hasVariable( 'ActionSettings', 'Compress' ) )
                $compress = $ini->variable( 'ActionSettings', 'Compress' );
        }
        $out['Compress'] = in_array( strtolower( trim( (string)$compress ) ), array( 'enabled', 'true', '1', 'yes', 'on' ), true );
        return $out;
    }

    /**
     * The plan of an action.
     *
     * @param \expLogManagerInterface $manager
     * @param string $op
     * @param string $key
     * @param mixed $days
     * @return array
     */
    public static function planFor( \expLogManagerInterface $manager, $op, $key, $days )
    {
        list( $planOp, , $all ) = self::OPS[$op];
        $opts = array();
        if ( $planOp === 'prune' && $days !== null && $days !== '' && is_numeric( $days ) )
            $opts['days'] = max( 0, (int)$days );
        if ( $planOp === 'truncate' )
            $opts['archive_first'] = true;
        if ( $planOp === 'rotate' && !$all )
            $opts['force'] = true;
        if ( $all )
            return $manager->plan( 'all', $planOp, $opts );
        $selected = $manager->select( array( (string)$key ) );
        if ( !$selected['keys'] )
            return array( 'op' => $planOp, 'steps' => array(), 'unknown' => array( (string)$key ) );
        return $manager->plan( $selected['keys'], $planOp, $opts );
    }

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $config = \expLogViewerConfig::instance();
        $manager = \expLogManagerFactory::get( $config );

        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string)$_SERVER['REQUEST_METHOD'] ) : 'GET';
        $op = $http->hasPostVariable( 'Op' ) && is_string( $http->postVariable( 'Op' ) ) ? (string)$http->postVariable( 'Op' ) : '';
        if ( $http->hasPostVariable( 'CancelButton' ) )
            return $Module->redirectTo( '/setup/logsmanage' );
        $key = $http->hasPostVariable( 'Key' ) && is_string( $http->postVariable( 'Key' ) ) ? (string)$http->postVariable( 'Key' )
             : ( isset( $Params['Key'] ) ? (string)$Params['Key'] : '' );
        $days = $http->hasPostVariable( 'Days' ) ? $http->postVariable( 'Days' ) : ( isset( $Params['days'] ) ? $Params['days'] : null );
        $switches = self::switches( $config );
        $decision = self::decide( $method, $op, $method === 'POST' && \expLogViewer::checkFormToken(), $http->hasPostVariable( 'ConfirmButton' ),
                                  $switches, $config->enabled( 'LogViewerSettings', 'LogViewer' ) );
        if ( $decision === 'disabled' )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );
        if ( $decision === 'bad_op' || $decision === 'denied' || $decision === 'off' )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        $token = \expLogViewer::formToken();
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'log_token_field', $token['field'] );
        $tpl->setVariable( 'log_token', $token['token'] );
        $tpl->setVariable( 'log_switches', $switches );
        $path = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'kernel/setup', 'Setup' ) ),
                       array( 'url' => 'setup/logs', 'text' => \ezpI18n::tr( 'kernel/setup', 'Log files' ) ) );

        if ( $decision === 'preview' || $decision === 'apply' )
        {
            $plan = self::planFor( $manager, $op, $key, $days );
            if ( $decision === 'apply' )
            {
                $result = $plan['steps'] ? $manager->apply( $plan ) : array( 'ok' => false, 'error' => 'empty', 'steps' => array() );
                $http->setSessionVariable( self::RESULT, array( 'op' => $op, 'key' => $key, 'ok' => (bool)$result['ok'],
                                                                 'error' => (string)$result['error'], 'steps' => $result['steps'] ) );
                return $Module->redirectTo( '/setup/logsmanage' );
            }
            $tpl->setVariable( 'log_op', $op );
            $tpl->setVariable( 'log_key', $key );
            $tpl->setVariable( 'log_days', $days !== null && is_numeric( $days ) ? (int)$days : '' );
            $tpl->setVariable( 'log_plan', $plan );
            $bytes = 0;
            foreach ( $plan['steps'] as $s )
                $bytes += $s['op'] === 'delete' || $s['op'] === 'truncate' ? (int)$s['bytes'] : 0;
            $tpl->setVariable( 'log_freed', $bytes );
            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:setup/logs_manage_confirm.tpl' );
            $Result['left_menu'] = 'design:setup/logs_menu.tpl';
            $Result['path'] = array_merge( $path, array( array( 'url' => 'setup/logsmanage', 'text' => \ezpI18n::tr( 'kernel/setup', 'Manage logs' ) ),
                                                         array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Confirm' ) ) ) );
            return $Result;
        }

        $result = null;
        if ( $http->hasSessionVariable( self::RESULT ) )
        {
            $result = $http->sessionVariable( self::RESULT );
            $http->removeSessionVariable( self::RESULT );
        }
        $logs = $manager->logs();
        $total = 0;
        foreach ( $logs as $log )
            $total += $log['size'] + $log['archive_bytes'];
        $tpl->setVariable( 'log_logs', $logs );
        $tpl->setVariable( 'log_total', $total );
        $tpl->setVariable( 'log_result', is_array( $result ) ? $result : null );
        $tpl->setVariable( 'log_can_download', $switches['Download'] );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/logs_manage.tpl' );
        $Result['left_menu'] = 'design:setup/logs_menu.tpl';
        $Result['path'] = array_merge( $path, array( array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Manage logs' ) ) ) );
        return $Result;
    }
}

}
