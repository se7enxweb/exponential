<?php
/**
 * File containing the expBenchmarkKernel class.
 *
 * The in-process side of exp:benchmark: what the kernel spends on the parts of a page, without HTTP, a web
 * server or a response cache in between. Each probe runs a number of times and is timed with hrtime().
 *
 *   boot          a new PHP process: autoload, settings, siteaccess, extensions, database connection
 *   process       the same child process from start to exit, as the parent saw it (includes render_cold)
 *   render_cold   the full view of a node, rendered once in that fresh process
 *   render_warm   the same full view in this process, after one untimed render
 *   ini_load      content.ini read again from the INI cache
 *   content_fetch one content object and its attributes from the database
 *   node_list     the first 20 children of the site's root node
 *   db_query      one indexed SELECT on the node table
 *   cache_write   a 16 KB entry written through the cluster file handler
 *   cache_read    the same entry read back
 *   image_alias   an image attribute fetched and an image alias it already has looked up
 *
 * Read-only for content: nodes and objects are only fetched. The only writes are cache entries: the probe's
 * own entry under the cache directory (removed at the end), and whatever template cache-blocks a full view
 * fills when it renders, exactly as a visitor's request would.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expBenchmarkKernel
{
    /**
     * @return array the options and their defaults
     */
    public static function defaults()
    {
        return array(
            'repeat'      => 20,   // runs of every in-process probe
            'boot_repeat' => 5,    // child processes for boot, process and render_cold
            'node'        => 0,    // the node to render and fetch; 0 for the site's front page
            'php'         => PHP_BINARY,
            'script'      => 'bin/php/benchmark.php',
            'siteaccess'  => '',
            'probes'      => array(),   // empty for all
        );
    }

    /**
     * @return array the names of every probe, in the order they run
     */
    public static function probeNames()
    {
        return array( 'boot', 'process', 'render_cold', 'render_warm', 'ini_load', 'content_fetch', 'node_list',
                      'db_query', 'cache_write', 'cache_read', 'image_alias' );
    }

    /**
     * The node of the site's front page: [SiteSettings] IndexPage when it names a node, else
     * [NodeSettings] RootNode, else 2.
     *
     * @return int
     */
    public static function frontPageNodeID()
    {
        $ini = eZINI::instance();
        if ( $ini->hasVariable( 'SiteSettings', 'IndexPage' )
             and preg_match( '#/(\d+)\s*$#', (string)$ini->variable( 'SiteSettings', 'IndexPage' ), $m ) )
            return (int)$m[1];
        $content = eZINI::instance( 'content.ini' );
        if ( $content->hasVariable( 'NodeSettings', 'RootNode' ) )
            return (int)$content->variable( 'NodeSettings', 'RootNode' );
        return 2;
    }

    /**
     * Runs the probes.
     *
     * @param array $options see defaults()
     * @param callable|null $progress called with a line of text after every probe
     * @return array list of rows (expBenchmark::kernelRow())
     */
    public static function run( $options = array(), $progress = null )
    {
        $options = array_merge( self::defaults(), (array)$options );
        $repeat = max( 1, (int)$options['repeat'] );
        $wanted = empty( $options['probes'] ) ? self::probeNames() : (array)$options['probes'];

        $frontPageID = self::frontPageNodeID();
        $nodeID = (int)$options['node'] > 0 ? (int)$options['node'] : self::chooseNode( $frontPageID );
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        if ( !$node instanceof eZContentObjectTreeNode )
            throw new RuntimeException( "Node $nodeID does not exist; name an existing one with --node=<id>" );
        $objectID = (int)$node->attribute( 'contentobject_id' );

        $rows = array();
        $add = function ( $row ) use ( &$rows, $progress )
        {
            $rows[] = $row;
            if ( $progress )
                call_user_func( $progress, sprintf( '  %-14s %3d runs  median %s ms', $row['name'], $row['n'],
                                                    $row['median'] === null ? '-' : sprintf( '%.2f', $row['median'] ) ) );
        };

        if ( array_intersect( array( 'boot', 'process', 'render_cold' ), $wanted ) )
        {
            foreach ( self::childProbes( $nodeID, $options ) as $name => $row )
            {
                if ( in_array( $name, $wanted, true ) )
                    $add( $row );
            }
        }

        if ( in_array( 'render_warm', $wanted, true ) )
        {
            $length = 0;
            $row = self::measure( 'render_warm', $repeat, function () use ( $nodeID, &$length )
            {
                $length = self::render( eZContentObjectTreeNode::fetch( $nodeID ) );
                return $length;
            }, '', true );
            if ( $row['note'] === '' )
                $row['note'] = sprintf( 'node %d, full view, %.1f KB of HTML', $nodeID, $length / 1024 );
            $add( $row );
        }

        if ( in_array( 'ini_load', $wanted, true ) )
        {
            $add( self::measure( 'ini_load', $repeat, function ()
            {
                eZINI::resetInstance( 'content.ini' );
                return eZINI::instance( 'content.ini' )->hasGroup( 'VersionView' );
            }, 'content.ini from the INI cache' ) );
        }

        if ( in_array( 'content_fetch', $wanted, true ) )
        {
            $add( self::measure( 'content_fetch', $repeat, function () use ( $objectID )
            {
                eZContentObject::clearCache( array( $objectID ) );
                $object = eZContentObject::fetch( $objectID );
                return $object ? count( $object->dataMap() ) : false;
            }, "object $objectID with its attributes" ) );
        }

        if ( in_array( 'node_list', $wanted, true ) )
        {
            $add( self::measure( 'node_list', $repeat, function () use ( $frontPageID )
            {
                eZContentObject::clearCache();
                $list = eZContentObjectTreeNode::subTreeByNodeID(
                    array( 'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => 20, 'Limitation' => array(),
                           'SortBy' => array( 'priority', true ) ),
                    $frontPageID );
                return is_array( $list ) ? count( $list ) : false;
            }, "up to 20 children of node $frontPageID" ) );
        }

        if ( in_array( 'db_query', $wanted, true ) )
        {
            $db = eZDB::instance();
            $add( self::measure( 'db_query', $repeat, function () use ( $db, $nodeID )
            {
                $result = $db->arrayQuery( 'SELECT node_id, contentobject_id FROM ezcontentobject_tree WHERE node_id = ' . (int)$nodeID );
                return is_array( $result ) && count( $result ) === 1;
            }, 'one indexed SELECT, ' . $db->databaseName() ) );
        }

        if ( in_array( 'cache_write', $wanted, true ) or in_array( 'cache_read', $wanted, true ) )
        {
            $file = eZDir::path( array( eZSys::cacheDirectory(), 'benchmark', 'probe-' . getmypid() . '.cache' ) );
            $payload = str_repeat( 'Exponential benchmark cache probe. ', 468 );   // about 16 KB
            $handler = eZClusterFileHandler::instance( $file );
            $writeRow = self::measure( 'cache_write', $repeat, function () use ( $handler, $payload )
            {
                $handler->storeContents( $payload, 'benchmark', 'php', true );
                return true;
            }, get_class( $handler ) );
            $readRow = self::measure( 'cache_read', $repeat, function () use ( $file, $payload )
            {
                // A new handler each time, as a request has: no in-memory copy of the last read.
                return eZClusterFileHandler::instance( $file )->fetchContents() === $payload;
            }, get_class( $handler ) );
            $handler->delete();
            if ( in_array( 'cache_write', $wanted, true ) )
                $add( $writeRow );
            if ( in_array( 'cache_read', $wanted, true ) )
                $add( $readRow );
        }

        if ( in_array( 'image_alias', $wanted, true ) )
        {
            try
            {
                $image = self::findImageAttribute();
                $why = 'no published image attribute found';
            }
            catch ( Throwable $e )
            {
                $image = null;
                $why = 'failed: ' . $e->getMessage();
            }
            if ( $image === null )
            {
                $add( expBenchmark::kernelRow( 'image_alias', array(), 0, null, $why ) );
            }
            else
            {
                list( $attributeID, $version, $alias ) = $image;
                $add( self::measure( 'image_alias', $repeat, function () use ( $attributeID, $version, $alias )
                {
                    $attribute = eZContentObjectAttribute::fetch( $attributeID, $version );
                    if ( !$attribute )
                        return false;
                    $result = $attribute->content()->imageAlias( $alias );
                    return is_array( $result );
                }, "attribute $attributeID, alias $alias" ) );
            }
        }

        return $rows;
    }

    /**
     * What the child process of the boot probe does, and prints as JSON on its last line: the time from the
     * start of the process until the database is connected, and the first full view of the node.
     *
     * Called by the command in its internal "probe" mode, after the script's initialize().
     *
     * @param int $nodeID
     * @return array( 'boot' => ms, 'render_cold' => ms, 'mem_peak' => bytes, 'error' => string )
     */
    public static function childProbe( $nodeID )
    {
        $result = array( 'boot' => null, 'render_cold' => null, 'mem_peak' => null, 'error' => '' );
        try
        {
            eZDB::instance()->isConnected();
            $began = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (float)$_SERVER['REQUEST_TIME_FLOAT'] : microtime( true );
            $result['boot'] = ( microtime( true ) - $began ) * 1000.0;

            $node = eZContentObjectTreeNode::fetch( (int)$nodeID );
            if ( !$node instanceof eZContentObjectTreeNode )
                throw new RuntimeException( "Node $nodeID does not exist" );
            $start = hrtime( true );
            self::render( $node );
            $result['render_cold'] = ( hrtime( true ) - $start ) / 1e6;
            $result['mem_peak'] = memory_get_peak_usage( true );
        }
        catch ( Throwable $e )
        {
            $result['error'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * The node the render probes use when none is named: the front page, unless its full view is nearly
     * empty (a front page built from layout blocks renders them in the page layout, not in the full view);
     * then whichever of the first menu pages has the largest full view. Each candidate is rendered once,
     * untimed.
     *
     * @param int $frontPageID
     * @return int
     */
    public static function chooseNode( $frontPageID )
    {
        $best = (int)$frontPageID;
        $bestLength = -1;
        try
        {
            $front = eZContentObjectTreeNode::fetch( $best );
            $bestLength = $front ? self::render( $front ) : -1;
            if ( $bestLength >= 2048 )
                return $best;
            $children = eZContentObjectTreeNode::subTreeByNodeID(
                array( 'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => 5, 'Limitation' => array(),
                       'SortBy' => array( 'priority', true ) ),
                $best );
            foreach ( (array)$children as $child )
            {
                $length = self::render( $child );
                if ( $length > $bestLength )
                {
                    $best = (int)$child->attribute( 'node_id' );
                    $bestLength = $length;
                }
            }
        }
        catch ( Throwable $e )
        {
            // The front page it is; the probes report what fails.
        }
        return $best;
    }

    /**
     * Renders the full view of a node as content/view does, without the view cache and the page layout.
     *
     * @param eZContentObjectTreeNode $node
     * @return int the length of the HTML
     */
    public static function render( $node )
    {
        if ( !$node instanceof eZContentObjectTreeNode )
            throw new RuntimeException( 'No node to render' );
        $tpl = eZTemplate::factory();
        $result = eZNodeviewfunctions::generateNodeViewData( $tpl, $node, $node->object(), false, 'full', 0 );
        return strlen( (string)$result['content'] );
    }

    /**
     * Starts the boot child process $options['boot_repeat'] times.
     *
     * @return array name => row for boot, process and render_cold
     */
    protected static function childProbes( $nodeID, $options )
    {
        $runs = max( 1, (int)$options['boot_repeat'] );
        $boot = array();
        $process = array();
        $render = array();
        $memory = 0;
        $errors = 0;
        $lastError = '';

        $command = array( $options['php'], $options['script'], 'probe', '--node=' . (int)$nodeID, '--allow-root-user' );
        if ( $options['siteaccess'] !== '' )
            $command[] = '--siteaccess=' . $options['siteaccess'];

        for ( $i = 0; $i < $runs; $i++ )
        {
            $start = hrtime( true );
            $handle = proc_open( $command, array( 0 => array( 'file', '/dev/null', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
            if ( !is_resource( $handle ) )
            {
                $errors++;
                $lastError = 'could not start ' . $options['php'];
                continue;
            }
            $output = stream_get_contents( $pipes[1] );
            $stderr = stream_get_contents( $pipes[2] );
            fclose( $pipes[1] );
            fclose( $pipes[2] );
            proc_close( $handle );
            $elapsed = ( hrtime( true ) - $start ) / 1e6;

            $data = null;
            foreach ( array_reverse( preg_split( '/\R/', trim( (string)$output ) ) ) as $line )
            {
                if ( strpos( $line, '{"boot"' ) === 0 )
                {
                    $data = json_decode( $line, true );
                    break;
                }
            }
            if ( !is_array( $data ) or $data['error'] !== '' or $data['boot'] === null )
            {
                $errors++;
                $lastError = is_array( $data ) && $data['error'] !== '' ? $data['error']
                           : trim( substr( (string)$stderr . (string)$output, 0, 300 ) );
                continue;
            }
            $boot[] = $data['boot'];
            $process[] = $elapsed;
            if ( $data['render_cold'] !== null )
                $render[] = $data['render_cold'];
            $memory = max( $memory, (int)$data['mem_peak'] );
        }

        $note = $errors > 0 ? 'failed: ' . $lastError : '';
        return array(
            'boot'        => expBenchmark::kernelRow( 'boot', $boot, $errors, null, $note !== '' ? $note : 'new process until the database is connected' ),
            'process'     => expBenchmark::kernelRow( 'process', $process, $errors, $memory ?: null, $note !== '' ? $note : 'whole child process, start to exit' ),
            'render_cold' => expBenchmark::kernelRow( 'render_cold', $render, $errors, $memory ?: null, $note !== '' ? $note : "node $nodeID, first full view in a fresh process" ),
        );
    }

    /**
     * Runs $callback $repeat times and makes a row of it. A run that throws or returns false is an error.
     *
     * @param string $name
     * @param int $repeat
     * @param callable $callback
     * @param string $note
     * @param bool $warmUp one untimed run first
     * @return array a row
     */
    protected static function measure( $name, $repeat, $callback, $note = '', $warmUp = false )
    {
        $samples = array();
        $errors = 0;
        $lastError = '';
        if ( function_exists( 'memory_reset_peak_usage' ) )
            memory_reset_peak_usage();
        $before = memory_get_usage();

        if ( $warmUp )
        {
            try { call_user_func( $callback ); } catch ( Throwable $e ) { $lastError = $e->getMessage(); }
        }

        for ( $i = 0; $i < $repeat; $i++ )
        {
            try
            {
                $start = hrtime( true );
                $ok = call_user_func( $callback );
                $elapsed = ( hrtime( true ) - $start ) / 1e6;
                if ( $ok === false )
                {
                    $errors++;
                    continue;
                }
                $samples[] = $elapsed;
            }
            catch ( Throwable $e )
            {
                $errors++;
                $lastError = $e->getMessage();
            }
        }

        // With memory_reset_peak_usage() (PHP 8.2+) this is the probe's own peak above what was in use before
        // it; on PHP 8.0 and 8.1 it is the process's peak so far.
        $peak = function_exists( 'memory_reset_peak_usage' ) ? max( 0, memory_get_peak_usage() - $before ) : memory_get_peak_usage();
        if ( $errors > 0 and $lastError !== '' )
            $note = 'failed: ' . $lastError;
        return expBenchmark::kernelRow( $name, $samples, $errors, $peak, $note );
    }

    /**
     * A published image attribute and an alias it already has, so the lookup reads and never scales an image.
     *
     * @return array|null array( attribute id, version, alias name )
     */
    protected static function findImageAttribute()
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery(
            "SELECT a.id, a.version, a.data_text FROM ezcontentobject_attribute a, ezcontentobject o
              WHERE a.contentobject_id = o.id AND a.version = o.current_version AND o.status = 1
                AND a.data_type_string = 'ezimage' AND a.data_text LIKE '%<original%'
              ORDER BY a.id",
            array( 'limit' => 20 ) );
        foreach ( (array)$rows as $row )
        {
            $attribute = eZContentObjectAttribute::fetch( (int)$row['id'], (int)$row['version'] );
            if ( !$attribute or !$attribute->hasContent() )
                continue;
            $aliases = $attribute->content()->aliasList( false );
            if ( !isset( $aliases['original'] ) or (string)$aliases['original']['url'] === '' )
                continue;
            $manager = eZImageManager::factory();
            $name = 'original';
            foreach ( array_keys( $aliases ) as $candidate )
            {
                if ( $candidate !== 'original' and $manager->hasAlias( $candidate ) )
                {
                    $name = $candidate;
                    break;
                }
            }
            return array( (int)$row['id'], (int)$row['version'], $name );
        }
        return null;
    }
}
