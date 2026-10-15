<?php
/**
 * @description Content checks: build the reference index, list the unused assets and the broken relations below a node (report only, changes nothing)
 * @alias exp:content-check
 *
 * File containing the exp:content-check command (doc/features/6.0/content-checks.md, "On the command line").
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
                'description' => "Content checks (report only: nothing is changed or moved to the trash)\n" .
                                 "\n" .
                                 "./console exp:content-check --build-index           build the reference index (also the cronjob part contentcheck)\n" .
                                 "./console exp:content-check --unreferenced --node=2  the unused assets below node 2\n" .
                                 "./console exp:content-check --broken --node=2        the broken relations below node 2, and the layouts'\n" .
                                 "./console exp:content-check --why=123,456           what uses objects 123 and 456\n" .
                                 "\n" .
                                 "The lists are those of the user given by --login (default admin): what that user may read.\n" .
                                 "Exit code: 0 nothing found, 1 something found, 2 error.",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[build-index][unreferenced][broken][node:][why:][login:][json]', '', array(
            'build-index'  => 'Build the reference index and keep it in the cache',
            'unreferenced' => 'List the unused assets below --node',
            'broken'       => 'List the broken relations below --node (all pages) and in the layouts',
            'node'         => 'The node the lists start from (default: content.ini [NodeSettings] RootNode)',
            'why'          => 'Object ids: say what uses each of them',
            'login'        => 'The user whose access the lists use (default admin)',
            'json'         => 'One JSON object instead of the text'
        ) );

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
            if ( !empty( $options['why'] ) )
                $report['why'] = \expContentCheckUnreferenced::reasons( \expContentCheck::idList( (string)$options['why'] ) );
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
            $this->shutdown( $found ? 1 : 0 );
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
        if ( isset( $report['why'] ) )
        {
            foreach ( $report['why'] as $id => $why )
                $cli->output( sprintf( '  object %-8d %s', $id, $why === null ? 'UNUSED: nothing found that uses it' : 'used: ' . $why ) );
        }
        $this->shutdown( $found ? 1 : 0 );
    }
}

}

?>
