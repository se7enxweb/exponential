<?php
/**
 * The upgrade check (setup/systemupgrade): the file consistency check and the database consistency check.
 *
 * The checks are expFileConsistencyReport and expSchemaConsistencyReport; this view only runs the one asked for,
 * hands its result to the template and answers the downloads. bin/php/checkmanifest.php reads the manifests with the
 * same class, so the page and the command line always agree.
 *
 * Nothing here writes a file or changes the database: the SQL of the database check is text to read.
 *
 * Template variables, old and new: md5_result ('ok', 'failed' or the list of paths with a problem), failure_reason,
 * upgrade_sql ('ok', 'mongo' or the SQL), mongo_check, mongo_missing_count, mongo_grouped_list, mongo_extra,
 * mongo_create_cmd; file_report, file_items, file_items_limit, schema_report, upgrade_info, upgrade_check.
 *
 * Guide: doc/guides/upgrade-check.md
 */
/*
 * The original header of kernel/setup/systemupgrade.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Setup
{

class Systemupgrade extends \Exponential\Runnable\ModuleView
{
    /** The most findings the page lists; the download has them all */
    const ITEMS_LIMIT = 2000;

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'md5_result', false );
        $tpl->setVariable( 'upgrade_sql', false );
        $tpl->setVariable( 'file_report', false );
        $tpl->setVariable( 'file_items', array() );
        $tpl->setVariable( 'file_groups', array() );
        $tpl->setVariable( 'file_items_limit', self::ITEMS_LIMIT );
        $tpl->setVariable( 'schema_report', false );
        $tpl->setVariable( 'upgrade_check', '' );

        $extensions = self::activeExtensionDirectories();

        // The downloads: the check runs again and its report is sent as a file
        if ( $Module->isCurrentAction( 'DownloadFileReport' ) )
        {
            $format = $http->hasPostVariable( 'DownloadFileReportButton' ) && $http->postVariable( 'DownloadFileReportButton' ) === 'txt' ? 'txt' : 'csv';
            $report = self::fileReport( $extensions );
            self::audit( 'md5', $report->result()['status'], $report->result()['problems'], $format );
            self::sendFile( 'exponential-file-consistency-' . date( 'Ymd-His' ) . '.' . $format,
                            $format === 'csv' ? 'text/csv; charset=utf-8' : 'text/plain; charset=utf-8',
                            function ( $out ) use ( $report, $format ) {
                                if ( $format === 'csv' )
                                    $report->writeCsv( $out );
                                else
                                    fwrite( $out, $report->text( 'Exponential ' . \eZPublishSDK::version() . ': file consistency check, ' . date( 'Y-m-d H:i:s' ) ) );
                            } );
            return $this->viewResult( null, null );
        }
        if ( $Module->isCurrentAction( 'DownloadSchemaReport' ) )
        {
            $report = \expSchemaConsistencyReport::collect();
            self::audit( 'database', $report->result()['status'], $report->result()['counts']['tables'], 'sql' );
            self::sendFile( 'exponential-database-consistency-' . date( 'Ymd-His' ) . '.sql', 'text/plain; charset=utf-8',
                            function ( $out ) use ( $report ) {
                                fwrite( $out, $report->sqlText( 'Exponential ' . \eZPublishSDK::version() . ': database consistency check, ' . date( 'Y-m-d H:i:s' ) ) );
                            } );
            return $this->viewResult( null, null );
        }

        if ( $Module->isCurrentAction( 'MD5Check' ) )
        {
            $tpl->setVariable( 'upgrade_check', 'files' );
            $report = self::fileReport( $extensions );
            $result = $report->result();
            if ( $result['status'] === \expFileConsistencyReport::STATUS_FAILED )
            {
                $tpl->setVariable( 'md5_result', 'failed' );
                if ( $result['failure'] === 'unreadable_manifest' )
                    $reason = \ezpI18n::tr( 'kernel/setup', 'File %1 cannot be read. Check that the web server may read it.', null, array( \eZMD5::CHECK_SUM_LIST_FILE ) );
                else if ( $result['failure'] === 'empty_manifest' )
                    $reason = \ezpI18n::tr( 'kernel/setup', 'File %1 lists no files. Copy it from the Exponential release this installation runs.', null, array( \eZMD5::CHECK_SUM_LIST_FILE ) );
                else
                    $reason = \ezpI18n::tr( 'kernel/setup', 'File %1 does not exist. '.
                                            'You should copy it from the recent Exponential distribution.',
                                            null, array( \eZMD5::CHECK_SUM_LIST_FILE ) );
                $tpl->setVariable( 'failure_reason', $reason );
            }
            else
            {
                $paths = $report->problemPaths();
                $tpl->setVariable( 'md5_result', $paths ? $paths : 'ok' );
            }
            $tpl->setVariable( 'file_report', $result );
            $tpl->setVariable( 'file_items', array_slice( $result['items'], 0, self::ITEMS_LIMIT ) );
            $tpl->setVariable( 'file_groups', self::groups( $result ) );
            self::audit( 'md5', $result['status'], $result['problems'] );
        }

        if ( $Module->isCurrentAction( 'DBCheck' ) )
        {
            $tpl->setVariable( 'upgrade_check', 'database' );
            $report = \expSchemaConsistencyReport::collect();
            $result = $report->result();
            $tpl->setVariable( 'schema_report', $result );
            if ( !$result['relational'] )
            {
                $tpl->setVariable( 'mongo_check', true );
                $tpl->setVariable( 'upgrade_sql', $result['status'] === \expSchemaConsistencyReport::STATUS_OK ? 'ok' : 'mongo' );
                if ( $result['status'] !== \expSchemaConsistencyReport::STATUS_OK )
                {
                    $tpl->setVariable( 'mongo_missing_count', count( $result['mongo']['missing'] ) );
                    $tpl->setVariable( 'mongo_grouped_list', $result['mongo']['groups'] );
                    $tpl->setVariable( 'mongo_extra', $result['mongo']['extra'] );
                    $tpl->setVariable( 'mongo_create_cmd', $result['mongo']['create_command'] );
                }
            }
            else
            {
                $tpl->setVariable( 'upgrade_sql', $result['sql'] !== '' ? $result['sql'] : ( $result['status'] === \expSchemaConsistencyReport::STATUS_FAILED ? false : 'ok' ) );
            }
            self::audit( 'database', $result['status'], $result['counts']['tables'] );
        }

        $tpl->setVariable( 'upgrade_info', self::info( $extensions ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:setup/systemupgrade.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/setup', 'System Upgrade' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The active extensions and their directories.
     *
     * @return string[] name => directory
     */
    public static function activeExtensionDirectories()
    {
        $directories = array();
        foreach ( \eZExtension::activeExtensions() as $extension )
        {
            $path = \eZExtension::extensionPath( $extension );
            if ( $path !== false )
                $directories[$extension] = rtrim( $path, '/' );
        }
        return $directories;
    }

    /**
     * The file report of the root manifest and the active extensions' own manifests, with the files git tracks where
     * the installation (or an extension) is a git checkout, so files missing from a manifest are found too.
     *
     * @param string[] $extensions name => directory
     * @return \expFileConsistencyReport
     */
    public static function fileReport( array $extensions )
    {
        $report = \expFileConsistencyReport::forInstallation( '.', $extensions );
        $tracked = \expFileConsistencyReport::gitTrackedFiles( '.' );
        if ( $tracked !== false )
            $report->setTrackedFiles( 'exponential', $tracked, \expFileConsistencyReport::ROOT_EXCLUDES );
        foreach ( $extensions as $name => $directory )
        {
            if ( is_file( $directory . '/' . \expFileConsistencyReport::MANIFEST_FILE ) &&
                 ( $own = \expFileConsistencyReport::gitTrackedFiles( $directory ) ) !== false )
                $report->setTrackedFiles( $name, $own, array( \expFileConsistencyReport::MANIFEST_FILE ) );
        }
        $report->run();
        return $report;
    }

    /**
     * The findings by state, in the order the page shows them, each with at most ITEMS_LIMIT items in all.
     *
     * @param array $result expFileConsistencyReport::result()
     * @return array list of array( state, count, shown, items )
     */
    public static function groups( array $result )
    {
        $byState = array_fill_keys( \expFileConsistencyReport::STATES, array() );
        $left = self::ITEMS_LIMIT;
        foreach ( $result['items'] as $item )
        {
            if ( $left <= 0 )
                break;
            $byState[$item['state']][] = $item;
            $left--;
        }
        $groups = array();
        foreach ( $byState as $state => $items )
        {
            if ( $result['counts'][$state] > 0 )
                $groups[] = array( 'state' => $state, 'count' => $result['counts'][$state], 'shown' => count( $items ),
                                   'problem' => in_array( $state, \expFileConsistencyReport::PROBLEM_STATES, true ), 'items' => $items );
        }
        return $groups;
    }

    /**
     * What the page shows before any check runs: the version, the manifest of Exponential and which active
     * extension carries a manifest of its own or is listed in the one of Exponential.
     *
     * @param string[] $extensions name => directory
     * @return array
     */
    public static function info( array $extensions )
    {
        $root = \expFileConsistencyReport::summarize( \expFileConsistencyReport::MANIFEST_FILE );
        $list = array();
        $own = 0;
        $covered = 0;
        foreach ( $extensions as $name => $directory )
        {
            $file = $directory . '/' . \expFileConsistencyReport::MANIFEST_FILE;
            $summary = is_file( $file ) ? \expFileConsistencyReport::summarize( $file ) : null;
            $inRoot = isset( $root['areas']['extension/' . $name] ) ? $root['areas']['extension/' . $name] : 0;
            $version = '';
            if ( $summary )
            {
                $ext = \ezpExtension::getInstance( $name )->getInfo();
                foreach ( is_array( $ext ) ? $ext : array() as $key => $value )
                {
                    if ( is_string( $value ) && strtolower( $key ) === 'version' )
                        $version = $value;
                }
            }
            $list[] = array( 'name' => $name, 'directory' => $directory, 'manifest' => $summary !== null,
                             'manifest_file' => $summary !== null ? $file : '',
                             'manifest_version' => $summary && isset( $summary['header']['version'] ) ? $summary['header']['version'] : '',
                             'manifest_files_count' => $summary && isset( $summary['header']['files_count'] ) ? (int)$summary['header']['files_count'] : 0,
                             'entries' => $summary ? $summary['entries'] : 0, 'mtime' => $summary ? $summary['mtime'] : 0,
                             'malformed' => $summary ? $summary['malformed'] : 0,
                             'version' => $version, 'in_root' => $inRoot );
            if ( $summary !== null )
                $own++;
            else if ( $inRoot > 0 )
                $covered++;
        }
        return array(
            'version' => \eZPublishSDK::version(),
            'manifest' => $root + array( 'file' => \expFileConsistencyReport::MANIFEST_FILE,
                                         'committed' => \expFileConsistencyReport::gitLastChange( '.', \expFileConsistencyReport::MANIFEST_FILE ) ),
            'extensions' => $list,
            'extension_count' => count( $list ),
            'with_manifest' => $own,
            'in_root_manifest' => $covered,
            'without' => count( $list ) - $own - $covered,
            'engine' => \eZDB::instance()->databaseName(),
        );
    }

    /**
     * Audit (doc/bc/6.0/audit.md, system.upgrade.run): an upgrade check that ran, with its outcome.
     */
    private static function audit( $check, $status, $differences, $download = '' )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        $outcome = $status === 'failed' ? 'failed' : ( $status === 'ok' ? 'ok' : 'differences' );
        $after = array( 'check' => $check, 'outcome' => $outcome, 'differences' => (int)$differences );
        if ( $download !== '' )
            $after['download'] = $download;
        \expAuditHook::emit( 'system.upgrade.run', array( 'object' => array( 'type' => 'upgrade', 'id' => $check . '_check' ), 'verb' => 'run',
            'result' => $outcome === 'failed' ? 'failed' : 'success', 'reason' => $outcome === 'failed' ? 'error' : null,
            'after' => $after ) );
    }

    /**
     * Sends a report as a download and ends the request.
     */
    private static function sendFile( $fileName, $type, $write )
    {
        header( 'Content-Type: ' . $type );
        header( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'Pragma: no-cache' );
        header( 'X-Content-Type-Options: nosniff' );
        while ( @ob_end_clean() );

        // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
        $out = fopen( 'php://output', 'w' );
        $write( $out );
        fclose( $out );
        \eZExecution::cleanExit();
    }
}

}
