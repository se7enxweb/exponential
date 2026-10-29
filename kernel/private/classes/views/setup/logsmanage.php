<?php
/**
 * Setup > Log files > Manage (setup/logsmanage; doc/eng-us/features/6.0/log-viewer.md, "Managing logs"): every log with its
 * size, estimated lines, last write, growth, archives and limits, and the actions rotate now, empty (archive first),
 * compress the archives, remove the archives older than N days, rotate all, prune all, and the download of the
 * viewer (setup/logsaction).
 *
 * GET shows the logs. A POST with DownloadAllButton (IncludeArchives, Unredacted) sends one archive of every log
 * (expLogBundle; Unredacted needs setup/unmasklogs). A POST of an action (Op: rotate | truncate | compress | prune |
 * rotate_all | prune_all, with Key
 * and Days) shows its plan, every step, and changes nothing (the dry run); its ConfirmButton posts it again and the
 * plan is applied, each step checked against the disk again, and recorded in the audit. Every POST carries the form
 * token; the view needs setup/managelogs, and each action its switch in [ActionSettings] (Rotate, Truncate,
 * Compress, DeleteArchives). The web server's logs are never managed.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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

    /** The session variable that carries a refused bundle (too large) to the page shown after it */
    const BUNDLE_ERROR = 'expLogBundleError';

    /**
     * What a "Download all logs" request may do. Pure. The view itself needs setup/managelogs (module.php); an
     * unredacted bundle also needs setup/unmasklogs.
     *
     * @param string $method GET or POST
     * @param bool $tokenOk the POST carries the form token
     * @param bool $canManage setup/managelogs
     * @param bool $download [ActionSettings] Download
     * @param bool $unredacted asked for unredacted contents
     * @param bool $canUnmask setup/unmasklogs
     * @return string build | denied | off | unmask_denied
     */
    public static function decideBundle( $method, $tokenOk, $canManage, $download, $unredacted, $canUnmask )
    {
        if ( $method !== 'POST' || !$tokenOk || !$canManage )
            return 'denied';
        if ( !$download )
            return 'off';
        if ( $unredacted && !$canUnmask )
            return 'unmask_denied';
        return 'build';
    }

    /**
     * Builds the bundle of every log and sends it, then removes it. Ends the request (never inside a catch: the
     * clean exit of a persistent worker throws on purpose).
     *
     * @param \eZModule $Module
     * @param \expLogViewerConfig $config
     * @param bool $archives
     * @param bool $unredacted
     * @return array|null a page result only when nothing was sent
     */
    protected function sendBundle( $Module, \expLogViewerConfig $config, $archives, $unredacted )
    {
        $locator = new \expLogFileLocator( $config );
        $files = \expLogBundle::files( $locator, $archives );
        $dir = \expLogBundle::workDirectory();
        if ( $dir === null )
        {
            \eZHTTPTool::instance()->setSessionVariable( self::BUNDLE_ERROR, array( 'error' => 'write', 'total' => 0, 'cap' => 0 ) );
            return $Module->redirectTo( '/setup/logsmanage' );
        }
        // removed after the send below, and also when the request ends early (the visitor left, an error)
        register_shutdown_function( array( '\expLogBundle', 'cleanup' ), $dir );
        $format = \expLogBundle::format();
        $name = \expLogBundle::fileName( \expLogBundle::host(), time(), $format );
        $redactor = $unredacted ? null : \expLogRedactor::fromConfig( $config, false );
        $built = \expLogBundle::build( $files, $redactor, $dir, $name, \expLogBundle::cap( $config ), $format );
        if ( !$built['ok'] )
        {
            \expLogBundle::cleanup( $dir );
            \eZHTTPTool::instance()->setSessionVariable( self::BUNDLE_ERROR, array( 'error' => $built['error'], 'total' => $built['total'], 'cap' => $built['cap'] ) );
            return $Module->redirectTo( '/setup/logsmanage' );
        }
        \expLogViewer::audit( 'download', array( 'id' => $name, 'size' => $built['size'] ),
                              array( 'bundle' => true, 'files' => $built['files'], 'bytes' => $built['bytes'], 'unredacted' => (bool)$unredacted ) );
        ignore_user_abort( true );
        if ( method_exists( '\eZExecution', 'discardOutputBuffers' ) )
            \eZExecution::discardOutputBuffers();
        header( 'Content-Type: ' . ( $format === 'zip' ? 'application/zip' : 'application/gzip' ) );
        header( 'Content-Disposition: attachment; filename="' . $name . '"' );
        header( 'Content-Length: ' . $built['size'] );
        header( 'Cache-Control: no-store' );
        header( 'X-Content-Type-Options: nosniff' );
        $in = @fopen( $built['path'], 'rb' );
        if ( $in !== false )
        {
            while ( !feof( $in ) )
            {
                $chunk = fread( $in, \expLogBundle::CHUNK );
                if ( $chunk === false || $chunk === '' )
                    break;
                echo $chunk;
                flush();
                if ( connection_aborted() )
                    break;
            }
            fclose( $in );
        }
        \expLogBundle::cleanup( $dir );
        \eZExecution::cleanExit();
        return null;
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
        if ( $method === 'POST' && $http->hasPostVariable( 'DownloadAllButton' ) && $config->enabled( 'LogViewerSettings', 'LogViewer' ) )
        {
            $unredacted = $http->hasPostVariable( 'Unredacted' ) && (string)$http->postVariable( 'Unredacted' ) === '1';
            $bundle = self::decideBundle( $method, \expLogViewer::checkFormToken(), \expLogViewer::can( 'managelogs' ), $switches['Download'],
                                          $unredacted, \expLogViewer::can( 'unmasklogs' ) );
            if ( $bundle !== 'build' )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            // without the box ticked the field is absent: the form sends IncludeArchives=0 in a hidden field first
            $archives = !$http->hasPostVariable( 'IncludeArchives' ) || (string)$http->postVariable( 'IncludeArchives' ) !== '0';
            return $this->sendBundle( $Module, $config, $archives, $unredacted );
        }
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
        $bundleError = null;
        if ( $http->hasSessionVariable( self::BUNDLE_ERROR ) )
        {
            $bundleError = $http->sessionVariable( self::BUNDLE_ERROR );
            $http->removeSessionVariable( self::BUNDLE_ERROR );
        }
        $tpl->setVariable( 'log_bundle_error', is_array( $bundleError ) ? $bundleError : null );
        $tpl->setVariable( 'log_bundle_cap', \expLogBundle::cap( $config ) );
        $tpl->setVariable( 'log_bundle_format', \expLogBundle::format() );
        $tpl->setVariable( 'log_can_unmask', \expLogViewer::can( 'unmasklogs' ) );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/logs_manage.tpl' );
        $Result['left_menu'] = 'design:setup/logs_menu.tpl';
        $Result['path'] = array_merge( $path, array( array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Manage logs' ) ) ) );
        return $Result;
    }
}

}
