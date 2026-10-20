<?php
/**
 * Setup > Log files > Health (setup/logshealth; doc/en/us/features/6.0/log-viewer.md, "Health"): the disk the logs and their
 * archives use against [HealthSettings] DiskBudget, the fastest growing logs, the logs over their size limit, the files
 * the web server's user cannot write (mixed ownership of root and the site user), the newest error of each log linked
 * to its line in the viewer, and the effective rotation settings of each log with where each one is set (read only).
 * Policy setup/logs. Nothing on the page changes a file.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Setup
{

class Logshealth extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];

        $config = \expLogViewerConfig::instance();
        if ( !$config->enabled( 'LogViewerSettings', 'LogViewer' ) )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );
        $locator = new \expLogFileLocator( $config );
        $viewer = new \expLogViewer( $config, $locator, false );

        $manager = class_exists( 'expLogManagerFactory' ) ? \expLogManagerFactory::get( $config, $locator ) : null;
        $logs = array();
        if ( $manager !== null )
        {
            try
            {
                $logs = (array)$manager->logs();
            }
            catch ( \Exception $e )
            {
                \eZDebug::writeError( 'The log manager could not list the logs: ' . $e->getMessage(), __METHOD__ );
                $logs = array();
            }
        }

        $budget = \expLogHealth::budget( $config );
        $summary = \expLogHealth::summary( $logs, $budget );

        // the newest error of each log, masked as the viewer masks, linked to its line in the viewer
        $errors = array();
        foreach ( $summary['rows'] as $key => $row )
        {
            $file = isset( $row['id'] ) ? $locator->resolve( (string)$row['id'] ) : null;
            if ( $file === null || !empty( $file['archive'] ) )
                continue;
            $entry = \expLogHealth::newestError( $file['path'], $file['level'] );
            if ( $entry === null )
                continue;
            $message = trim( preg_replace( '/\s+/', ' ', $viewer->redactor()->text( (string)$entry['message'] ) ) );
            if ( strlen( $message ) > 240 )
                $message = substr( $message, 0, 240 ) . '…';
            $errors[$key] = array( 'time' => $entry['time'], 'time_text' => (string)$entry['time_text'], 'message' => $message,
                                   'source' => (string)$entry['source'],
                                   'permalink' => \expLogPermalink::build( $file['key'], array( 'offset' => (int)$entry['offset'], 'follow' => false ) ) );
        }
        $settings = \expLogHealth::settings( $logs, $manager );
        $viewLinks = array();
        foreach ( $summary['rows'] as $key => $row )
            $viewLinks[$key] = \expLogPermalink::build( (string)$key );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'log_health', $summary );
        $tpl->setVariable( 'log_health_errors', $errors );
        $tpl->setVariable( 'log_health_settings', $settings );
        $tpl->setVariable( 'log_health_links', $viewLinks );
        $tpl->setVariable( 'log_health_manager', $manager !== null );
        $tpl->setVariable( 'log_can_manage', \expLogViewer::can( 'managelogs' ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/logs_health.tpl' );
        $Result['left_menu'] = 'design:setup/logs_menu.tpl';
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'kernel/setup', 'Setup' ) ),
                                 array( 'url' => 'setup/logs', 'text' => \ezpI18n::tr( 'kernel/setup', 'Log files' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Log health' ) ) );
        return $Result;
    }
}

}
