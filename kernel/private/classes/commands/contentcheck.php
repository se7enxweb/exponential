<?php
/**
 * @description Content checks: build the reference index, list the unused assets and the broken relations below a node, list the missing files, scan the storage files (report only; --fix, --quarantine and --fix-permissions are a dry run unless --apply)
 * @alias exp:content-check
 *
 * File containing the exp:content-check command (doc/eng-us/features/6.0/content-checks.md, "On the command line").
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Contentcheck extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "Content checks (report only; --fix, --quarantine and --fix-permissions are a dry run unless --apply)\n" .
                                 "\n" .
                                 "./console exp:content-check --build-index           build the reference index (also the cronjob part contentcheck)\n" .
                                 "./console exp:content-check --unreferenced --node=2  the unused assets below node 2\n" .
                                 "./console exp:content-check --broken --node=2        the broken relations below node 2, and the layouts'\n" .
                                 "./console exp:content-check --why=123,456           what uses objects 123 and 456\n" .
                                 "./console exp:content-check --storage               scan the storage files: orphans and file problems\n" .
                                 "./console exp:content-check --storage --quarantine            which orphans would be quarantined\n" .
                                 "./console exp:content-check --storage --quarantine --apply    move them to the quarantine\n" .
                                 "./console exp:content-check --storage --fix-permissions [--apply]   set owner and mode of used files\n" .
                                 "./console exp:content-check --missing-files --node=2  files missing from the storage, stale image aliases\n" .
                                 "./console exp:content-check --missing-files --fix=regenerate   what regenerating would do\n" .
                                 "./console exp:content-check --fix=restore --items=123-4,125-2 --apply  restore two field versions\n" .
                                 "./console exp:content-check --fix=replace --items=123-4 --replace-with=photo.jpg --apply\n" .
                                 "\n" .
                                 "--fix (regenerate, restore, replace, remove, review), --quarantine and --fix-permissions are the only\n" .
                                 "options that change anything, and only with --apply: without it they show the plan and change nothing.\n" .
                                 "--dry-run means the same as no --apply; --apply together with --dry-run is a usage error (exit 2).\n" .
                                 "Without --items --fix takes every finding of --missing-files.\n" .
                                 "The lists are those of the user given by --login (default admin): what that user may read.\n" .
                                 "Exit code: 0 nothing found, 1 something found, 2 error.",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[build-index][unreferenced][broken][missing-files][problems:][versions:][fix:][items:][replace-with:][node:][why:][login:][json][storage][quarantine][fix-permissions][apply][dry-run]', '', array(
            'missing-files' => 'List the fields below --node whose files are missing, and images with missing or stale aliases',
            'problems'      => 'With --missing-files: missing,alias_missing,alias_stale,alias_absent (default: the first three)',
            'versions'      => 'With --missing-files: published,draft (default: both)',
            'fix'           => 'Resolve: regenerate, restore, replace, remove or review (the findings of --missing-files, or --items)',
            'items'         => 'With --fix: field versions as <attribute id>-<version>, separated by commas',
            'replace-with'  => 'With --fix=replace: the file put at the expected path (one item)',
            'build-index'  => 'Build the reference index and keep it in the cache',
            'unreferenced' => 'List the unused assets below --node',
            'broken'       => 'List the broken relations below --node (all pages) and in the layouts',
            'node'         => 'The node the lists start from (default: content.ini [NodeSettings] RootNode)',
            'why'          => 'Object ids: say what uses each of them',
            'login'        => 'The user whose access the lists use (default admin)',
            'json'         => 'One JSON object instead of the text',
            'storage'      => 'Scan the storage files (all of it, in batches) and keep the result for the storage view',
            'quarantine'   => 'With --storage: move the orphan files of the scan to the quarantine (each checked again)',
            'fix-permissions' => 'With --storage: set the expected owner and mode of used files with a wrong one',
            'apply'        => 'With --fix, --quarantine or --fix-permissions: do it (the default is a dry run that shows the plan)',
            'dry-run'      => 'The default, kept for scripts that pass it: show what would be done, change nothing'
        ) );

        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $this->shutdown( 2 );
            return;
        }
        $dryRun = !$apply;
        // fix() and storage() read the option: without --apply they plan and change nothing
        $options['dry-run'] = $dryRun;
        $writes = !empty( $options['fix'] ) || !empty( $options['quarantine'] ) || !empty( $options['fix-permissions'] );

        $login = !empty( $options['login'] ) ? (string)$options['login'] : 'admin';
        $user = \eZUser::fetchByName( $login );
        if ( !$user instanceof \eZUser )
        {
            $cli->error( 'FAIL: no user with the login given by --login' );
            $this->shutdown( 2 );
            return;
        }
        \eZUser::setCurrentlyLoggedInUser( $user, (int)$user->attribute( 'contentobject_id' ) );
        $nodeID = !empty( $options['node'] ) ? (int)$options['node'] : \Exponential\View\Kernel\Contentcheck\Unreferenced::rootNodeID();
        $report = array();
        $found = false;
        try
        {
            if ( !empty( $options['build-index'] ) || !empty( $options['unreferenced'] ) || !empty( $options['why'] ) )
            {
                $index = \expContentCheckReferences::build();
                \expContentCheckReferences::writeCache( $index );
                $report['index'] = array( 'seconds' => $index['seconds'], 'objects' => count( $index['objects'] ), 'nodes' => count( $index['nodes'] ),
                                          'remote' => count( $index['remote'] ), 'paths' => count( $index['aliases'] ), 'collections' => count( $index['queries'] ),
                                          'per_source' => $index['counts'], 'failed' => isset( $index['failed'] ) ? $index['failed'] : array() );
            }
            if ( !empty( $options['unreferenced'] ) )
            {
                $params = \expContentCheckUnreferenced::parameters( array( 'NodeID' => $nodeID, 'Limit' => 200 ), $nodeID );
                $rows = array();
                $count = 0;
                do
                {
                    $result = \expContentCheckUnreferenced::fetch( $params );
                    if ( $result['error'] !== '' )
                        throw new \RuntimeException( 'unreferenced: ' . $result['error'] );
                    $count = $result['count'];
                    foreach ( $result['rows'] as $row )
                        $rows[] = array( 'object_id' => $row['object_id'], 'node_id' => $row['node_id'], 'class' => $row['class_identifier'],
                                         'path' => $row['path'], 'size' => $row['size'] );
                    $params['offset'] += $params['limit'];
                }
                while ( $params['offset'] < $count );
                $report['unreferenced'] = array( 'node_id' => $nodeID, 'count' => $count, 'without_location' => $result['without_location'], 'rows' => $rows );
                $found = $found || $count > 0;
            }
            if ( !empty( $options['broken'] ) )
            {
                $params = \expContentCheckBrokenRelations::parameters( array( 'NodeID' => $nodeID, 'Limit' => 200 ), $nodeID );
                $findings = array();
                $layouts = array();
                $checked = 0;
                do
                {
                    $result = \expContentCheckBrokenRelations::scan( $params );
                    if ( $result['error'] !== '' )
                        throw new \RuntimeException( 'broken relations: ' . $result['error'] );
                    if ( (int)$params['after'] === 0 )
                        $layouts = $result['layouts'];
                    $checked += $result['checked'];
                    foreach ( $result['findings'] as $finding )
                        $findings[] = array( 'source_id' => $finding['source_id'], 'field' => $finding['field_name'], 'via' => $finding['via'],
                                             'target' => $finding['target_type'] . ' ' . $finding['target_id'], 'kind' => $finding['kind'], 'hint' => $finding['hint'] );
                    $params['after'] = $result['last'];
                }
                while ( !$result['done'] );
                $report['broken'] = array( 'node_id' => $nodeID, 'checked' => $checked, 'findings' => $findings, 'layouts' => $layouts );
                $found = $found || $findings || $layouts;
            }
            if ( !empty( $options['missing-files'] ) || ( !empty( $options['fix'] ) && empty( $options['items'] ) ) )
            {
                $report['missing_files'] = self::missingFiles( $nodeID, $options );
                $found = $found || $report['missing_files']['rows'];
            }
            if ( !empty( $options['fix'] ) )
            {
                $keys = !empty( $options['items'] ) ? \expContentCheckFiles::keyList( (string)$options['items'], \expContentCheckFileFix::maxItems() + 1 )
                                                    : \expContentCheckFiles::keyList( array_unique( array_column( $report['missing_files']['rows'], 'key' ) ),
                                                                                      \expContentCheckFileFix::maxItems() + 1 );
                $report['fix'] = self::fix( (string)$options['fix'], $keys, $options );
            }
            if ( !empty( $options['why'] ) )
                $report['why'] = \expContentCheckUnreferenced::reasons( \expContentCheck::idList( (string)$options['why'] ) );
            if ( !empty( $options['storage'] ) )
            {
                $report['storage'] = $this->storage( $options, $cli );
                $found = $found || $report['storage']['orphans'] > 0 || array_sum( $report['storage']['problems'] ) > 0;
            }
        }
        catch ( \Throwable $e )
        {
            $cli->error( 'FAIL: ' . $e->getMessage() );
            $this->shutdown( 2 );
            return;
        }

        if ( !empty( $options['json'] ) )
        {
            $cli->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            $this->shutdown( isset( $report['fix'] ) && $report['fix']['failed'] ? 2 : ( $found ? 1 : 0 ) );
            return;
        }
        if ( isset( $report['index'] ) )
            $cli->output( sprintf( 'Reference index: %d objects, %d nodes, %d remote ids, %d paths, %d collections in %.2f s%s',
                                   $report['index']['objects'], $report['index']['nodes'], $report['index']['remote'], $report['index']['paths'],
                                   $report['index']['collections'], $report['index']['seconds'],
                                   $report['index']['failed'] ? '; FAILED sources: ' . implode( ', ', $report['index']['failed'] ) : '' ) );
        if ( isset( $report['unreferenced'] ) )
        {
            $cli->output( sprintf( 'Unused assets below node %d: %d', $nodeID, $report['unreferenced']['count'] ) );
            foreach ( $report['unreferenced']['rows'] as $row )
                $cli->output( sprintf( '  object %-8d node %-8d %-12s %s', $row['object_id'], $row['node_id'], $row['class'], $row['path'] ) );
            if ( $report['unreferenced']['without_location'] )
                $cli->output( sprintf( 'Published objects without any location: %d', $report['unreferenced']['without_location'] ) );
        }
        if ( isset( $report['broken'] ) )
        {
            $cli->output( sprintf( 'Broken relations below node %d: %d in %d objects checked', $nodeID, count( $report['broken']['findings'] ), $report['broken']['checked'] ) );
            foreach ( $report['broken']['findings'] as $f )
                $cli->output( sprintf( '  object %-8d %-12s %-10s -> %-16s %-12s fix: %s', $f['source_id'], $f['via'], $f['field'], $f['target'], $f['kind'], $f['hint'] ) );
            $cli->output( sprintf( 'Layouts: %d', count( $report['broken']['layouts'] ) ) );
            foreach ( $report['broken']['layouts'] as $f )
                $cli->output( sprintf( '  layout %-6d %-9s %-6d %-20s -> %s %s  %s', $f['layout_id'], $f['where'], $f['where_id'], $f['detail'], $f['target_type'], $f['target_id'], $f['kind'] ) );
        }
        if ( isset( $report['storage'] ) )
        {
            $r = $report['storage'];
            $cli->output( sprintf( 'Storage %s: %d files (%s), %d used, %d orphans (%s), %d too new to judge, in %.2f s%s', $r['directory'], $r['files'],
                                   self::bytes( $r['bytes'] ), $r['used'], $r['orphans'], self::bytes( $r['orphan_bytes'] ), $r['recent'], $r['seconds'],
                                   $r['index'] !== 'ok' ? '; the reference index could not be read: ' . $r['index'] : '' ) );
            $cli->output( '  problems of used files: ' . implode( ', ', array_map( function ( $kind, $count ) { return $kind . ' ' . $count; },
                                                                                    array_keys( $r['problems'] ), $r['problems'] ) ) );
            foreach ( $r['actions'] as $action )
                $cli->output( sprintf( '  %s%s: %d of %d files (%s)%s', $action['action'], $action['dry_run'] ? ' (dry run)' : '', $action['count'],
                                       $action['asked'], self::bytes( $action['bytes'] ), $action['reasons'] ? '; left: ' . json_encode( $action['reasons'] ) : '' ) );
        }
        if ( isset( $report['missing_files'] ) )
        {
            $cli->output( sprintf( 'Missing files and image aliases below node %d: %d in %d objects checked', $nodeID,
                                   count( $report['missing_files']['rows'] ), $report['missing_files']['checked'] ) );
            foreach ( $report['missing_files']['rows'] as $r )
                $cli->output( sprintf( '  %-12s object %-8d %-9s %-13s %s%s', $r['key'], $r['object_id'], $r['version_status'], $r['problem'], $r['path'],
                                       $r['aliases'] ? ' (' . implode( ', ', $r['aliases'] ) . ')' : '' ) );
        }
        if ( isset( $report['fix'] ) )
        {
            $f = $report['fix'];
            $cli->output( sprintf( '%s %s: %d field versions planned, %d allowed%s', $f['dry_run'] ? 'DRY RUN' : 'Fix', $f['action'], count( $f['items'] ),
                                   $f['allowed'], $f['dry_run'] ? '' : sprintf( ', %d done, %d not done', count( $f['fixed'] ), count( $f['failed'] ) ) ) );
            foreach ( $f['items'] as $item )
                $cli->output( sprintf( '  %-12s %-10s %s%s%s', $item['key'], $item['allowed'] ? 'allowed' : 'refused', $item['allowed'] ? $item['path'] : $item['reason'],
                                       $item['source'] !== '' ? '  (' . $item['source'] . ')' : '',
                                       isset( $f['failed'][$item['key']] ) ? '  FAILED: ' . $f['failed'][$item['key']] : '' ) );
        }
        if ( isset( $report['why'] ) )
        {
            foreach ( $report['why'] as $id => $why )
                $cli->output( sprintf( '  object %-8d %s', $id, $why === null ? 'UNUSED: nothing found that uses it' : 'used: ' . $why ) );
        }
        if ( $writes && $dryRun )
            $cli->output( 'PASS: the plan above (dry run, nothing changed; --apply writes)' );
        $this->shutdown( isset( $report['fix'] ) && $report['fix']['failed'] ? 2 : ( $found ? 1 : 0 ) );
    }

    /**
     * Scans the storage (and quarantines or fixes when asked), for the report.
     *
     * @param array $options
     * @param \eZCLI $cli
     * @return array
     */
    protected function storage( array $options, $cli )
    {
        $state = \expContentCheckStorage::scanAll( function ( $state ) use ( $cli, $options ) {
            if ( empty( $options['json'] ) )
                $cli->output( sprintf( '  scanning: %d files, phase %s, %d %%', $state['counts']['files'], $state['phase'], \expContentCheckStorage::percent( $state ) ) );
        } );
        $report = array( 'directory' => $state['storage'], 'files' => $state['counts']['files'], 'bytes' => $state['counts']['bytes'],
                         'used' => $state['counts']['used'], 'orphans' => $state['counts']['orphans'], 'orphan_bytes' => $state['counts']['orphan_bytes'],
                         'recent' => $state['counts']['recent'], 'problems' => $state['counts']['problems'], 'index' => $state['index'],
                         'seconds' => $state['seconds'], 'listed' => count( $state['orphans'] ), 'actions' => array() );
        $dryRun = !empty( $options['dry-run'] );
        $todo = array();
        if ( !empty( $options['quarantine'] ) )
        {
            $paths = array();
            foreach ( $state['orphans'] as $orphan )
                $paths[] = $orphan['p'];
            $todo['quarantine'] = $paths;
        }
        if ( !empty( $options['fix-permissions'] ) )
        {
            $paths = array();
            foreach ( $state['problems'] as $problem )
            {
                if ( in_array( $problem['k'], array( 'owner', 'mode' ), true ) && !in_array( $problem['p'], $paths, true ) )
                    $paths[] = $problem['p'];
            }
            $todo['permissions'] = $paths;
        }
        foreach ( $todo as $action => $paths )
        {
            $entry = array( 'action' => $action, 'dry_run' => $dryRun, 'asked' => count( $paths ), 'count' => 0, 'bytes' => 0, 'reasons' => array() );
            foreach ( array_chunk( $paths, \expContentCheckQuarantine::batchLimit() ) as $chunk )
            {
                $plan = \expContentCheckQuarantine::plan( $action, $chunk );
                foreach ( $plan['items'] as $item )
                {
                    if ( !$item['allowed'] )
                        $entry['reasons'][$item['reason']] = isset( $entry['reasons'][$item['reason']] ) ? $entry['reasons'][$item['reason']] + 1 : 1;
                }
                if ( $dryRun )
                {
                    $entry['count'] += count( $plan['allowed'] );
                    $entry['bytes'] += $plan['bytes'];
                    continue;
                }
                $outcome = \expContentCheckQuarantine::execute( $action, $plan['allowed'], null, 'cli' );
                $entry['count'] += $outcome['count'];
                $entry['bytes'] += $outcome['bytes'];
                if ( $outcome['failed'] )
                    $entry['reasons']['failed'] = ( isset( $entry['reasons']['failed'] ) ? $entry['reasons']['failed'] : 0 ) + $outcome['failed'];
            }
            $report['actions'][] = $entry;
        }
        return $report;
    }

    /**
     * @param int $bytes
     * @return string
     */
    protected static function bytes( $bytes )
    {
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
        $i = 0;
        $value = (float)$bytes;
        while ( $value >= 1024 && $i < count( $units ) - 1 )
        {
            $value /= 1024;
            $i++;
        }
        return sprintf( $i === 0 ? '%d %s' : '%.1f %s', $value, $units[$i] );
    }

    /**
     * Every finding of the missing files check below a node, all pages.
     *
     * @param int $nodeID
     * @param array $options problems, versions
     * @return array array( 'node_id', 'checked', 'rows' => list of array( key, object_id, version_status, problem,
     *               path, aliases => alias names ) )
     */
    protected static function missingFiles( $nodeID, array $options )
    {
        $input = array( 'NodeID' => $nodeID, 'Limit' => 200 );
        if ( !empty( $options['problems'] ) )
            $input['Problem'] = preg_split( '/[\s,]+/', (string)$options['problems'], -1, PREG_SPLIT_NO_EMPTY );
        if ( !empty( $options['versions'] ) )
            $input['Version'] = preg_split( '/[\s,]+/', (string)$options['versions'], -1, PREG_SPLIT_NO_EMPTY );
        $params = \expContentCheckFiles::parameters( $input, $nodeID );
        $rows = array();
        $checked = 0;
        do
        {
            $result = \expContentCheckFiles::scan( $params );
            if ( $result['error'] !== '' )
                throw new \RuntimeException( 'missing files: ' . $result['error'] );
            $checked += $result['checked'];
            foreach ( $result['rows'] as $row )
                $rows[] = array( 'key' => $row['key'], 'object_id' => $row['object_id'], 'version_status' => $row['version_status'],
                                 'problem' => $row['problem'], 'path' => $row['path'], 'aliases' => array_column( $row['aliases'], 'alias' ) );
            $params['after'] = $result['last'];
        }
        while ( !$result['done'] );
        return array( 'node_id' => (int)$nodeID, 'checked' => $checked, 'rows' => $rows );
    }

    /**
     * Plans a resolution and, without --dry-run, does it batch by batch, as the administration does after the
     * confirmation (the same checks, the same audit events).
     *
     * @param string $action
     * @param array $keys from expContentCheckFiles::keyList()
     * @param array $options dry-run, replace-with
     * @return array array( 'action', 'dry_run', 'items' => list of array( key, allowed, reason, path ), 'allowed' => int,
     *               'fixed' => keys, 'failed' => key => why )
     */
    protected static function fix( $action, array $keys, array $options )
    {
        if ( !in_array( $action, \expContentCheckFileFix::ACTIONS, true ) )
            throw new \RuntimeException( '--fix takes ' . implode( ', ', \expContentCheckFileFix::ACTIONS ) );
        $uploads = array();
        if ( $action === 'replace' )
        {
            $file = isset( $options['replace-with'] ) ? (string)$options['replace-with'] : '';
            if ( $file === '' || !is_file( $file ) || count( $keys ) !== 1 )
                throw new \RuntimeException( '--fix=replace needs one --items and a file in --replace-with' );
            $uploads[key( $keys )] = array( 'tmp' => $file, 'name' => basename( $file ), 'uploaded' => false );
        }
        // the confirmation token of the administration, with a secret of this run
        \expContentCheck::setTestSecret( bin2hex( random_bytes( 32 ) ) );
        $dryRun = !empty( $options['dry-run'] );
        $plan = \expContentCheckFileFix::plan( $action, $keys );
        $report = array( 'action' => $action, 'dry_run' => $dryRun, 'items' => array(), 'allowed' => count( $plan['allowed'] ), 'fixed' => array(), 'failed' => array() );
        foreach ( $plan['items'] as $item )
            $report['items'][] = array( 'key' => $item['key'], 'allowed' => $item['allowed'], 'reason' => $item['reason'], 'path' => $item['path'],
                                       'source' => $item['source_path'] !== '' ? 'from version ' . $item['source_version'] . ': ' . $item['source_path'] : '' );
        if ( $dryRun )
            return $report;
        $todo = $keys;
        while ( $plan['allowed'] )
        {
            $outcome = \expContentCheckFileFix::execute( $action, $todo, \expContentCheck::confirmToken( $plan['fingerprint'] ), $uploads );
            $report['fixed'] = array_merge( $report['fixed'], $outcome['fixed'] );
            $report['failed'] = array_merge( $report['failed'], $outcome['failed'] );
            if ( !$outcome['remaining'] || $outcome['reason'] !== '' )
                break;
            $todo = \expContentCheckFiles::keyList( $outcome['remaining'], \expContentCheckFileFix::maxItems() );
            $plan = \expContentCheckFileFix::plan( $action, $todo );
        }
        \expContentCheck::setTestSecret( null );
        return $report;
    }
}

}

?>
