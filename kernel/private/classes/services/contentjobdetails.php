<?php
/**
 * What the content job pages show about a job beyond its progress: who started it (and on whose behalf), the
 * times, the duration and an estimate of the time left, the source and target with their full paths, the
 * counts, the batches (size, average, slowest, peak memory), where and how it runs (siteaccess, server, worker,
 * heartbeat), the options, the lock it holds, the error with the failing node, cancel and resume history, and
 * where the result is. Only data the engine records is shown; what it does not record is left out.
 * Used by content/job/<id> and content/jobs. Guide: doc/bc/6.0/content-jobs.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Service;

class ContentJobDetails
{
    /**
     * The batch lines of a job's log: "batch N: X done (...) in S s, peak P MB, ...".
     *
     * @param \expContentJob $job
     * @return array( 'count', 'seconds', 'average', 'slowest', 'slowest_batch', 'peak_mb' )
     */
    public static function batchStats( $job )
    {
        $stats = array( 'count' => 0, 'seconds' => 0.0, 'average' => 0.0, 'slowest' => 0.0, 'slowest_batch' => 0, 'peak_mb' => 0.0 );
        $recorded = $job->get( 'batches' );
        if ( is_array( $recorded ) && $recorded )
        {
            foreach ( $recorded as $b )
                self::addBatch( $stats, isset( $b['n'] ) ? (int) $b['n'] : 0, isset( $b['seconds'] ) ? (float) $b['seconds'] : 0.0,
                                isset( $b['peak_mb'] ) ? (float) $b['peak_mb'] : 0.0 );
        }
        else
        {
            foreach ( $job->log( 5000 ) as $line )
                if ( preg_match( '/batch (\d+): \d+ done .*? in ([0-9.]+) s, peak ([0-9.]+) MB/', $line, $m ) )
                    self::addBatch( $stats, (int) $m[1], (float) $m[2], (float) $m[3] );
        }
        if ( $stats['count'] )
            $stats['average'] = $stats['seconds'] / $stats['count'];
        return $stats;
    }

    protected static function addBatch( array &$stats, $n, $seconds, $peak )
    {
        $stats['count']++;
        $stats['seconds'] += $seconds;
        if ( $seconds >= $stats['slowest'] )
        {
            $stats['slowest'] = $seconds;
            $stats['slowest_batch'] = $n;
        }
        $stats['peak_mb'] = max( $stats['peak_mb'], $peak );
    }

    /**
     * A node's full path of names ("Home / Media / Folder"), or ''.
     *
     * @param \eZContentObjectTreeNode|null $node
     * @return string
     */
    public static function pathName( $node )
    {
        if ( !$node instanceof \eZContentObjectTreeNode )
            return '';
        $names = array();
        foreach ( $node->attribute( 'path' ) as $p )
            $names[] = $p->attribute( 'name' );
        $names[] = $node->attribute( 'name' );
        return implode( ' / ', $names );
    }

    /**
     * "2 min 5 s" for a number of seconds.
     */
    public static function duration( $seconds )
    {
        $seconds = max( 0, (int) round( $seconds ) );
        if ( $seconds < 60 )
            return \ezpI18n::tr( 'design/admin/content/job', '%s s', null, array( '%s' => $seconds ) );
        if ( $seconds < 3600 )
            return \ezpI18n::tr( 'design/admin/content/job', '%m min %s s', null, array( '%m' => intdiv( $seconds, 60 ), '%s' => $seconds % 60 ) );
        return \ezpI18n::tr( 'design/admin/content/job', '%h h %m min', null, array( '%h' => intdiv( $seconds, 3600 ), '%m' => intdiv( $seconds % 3600, 60 ) ) );
    }

    /**
     * The groups of rows the job page shows: array( array( 'id', 'title', 'rows' => array( array( 'label', 'text',
     * 'url' (or ''), 'mono' (bool), 'key' ) ) ) ).
     *
     * @param \expContentJob $job
     * @param array $info Job::info()
     * @return array
     */
    public static function groups( $job, array $info )
    {
        $tr = function ( $text, $args = array() ) { return \ezpI18n::tr( 'design/admin/content/job', $text, null, $args ); };
        $row = function ( $key, $label, $text, $url = '', $mono = false ) {
            return array( 'key' => $key, 'label' => $label, 'text' => (string) $text, 'url' => (string) $url, 'mono' => $mono );
        };
        $params = $job->params();
        $result = (array) $job->result();
        $now = time();
        $groups = array();

        // who
        $rows = array();
        $owner = \eZContentObject::fetch( (int) $job->userID() );
        $ownerUser = \eZUser::fetch( (int) $job->userID() );
        $rows[] = $row( 'user', $tr( 'Started by' ), $owner ? $owner->attribute( 'name' ) : '#' . $job->userID(),
                        $owner && $owner->attribute( 'main_node_id' ) ? 'content/view/full/' . $owner->attribute( 'main_node_id' ) : '' );
        if ( $ownerUser )
            $rows[] = $row( 'login', $tr( 'Login' ), $ownerUser->attribute( 'login' ), '', true );
        $createdBy = (int) $job->get( 'created_by' );
        if ( $createdBy && $createdBy !== (int) $job->userID() )
        {
            $by = \eZContentObject::fetch( $createdBy );
            $rows[] = $row( 'on_behalf', $tr( 'Created by, on behalf of the user above' ), $by ? $by->attribute( 'name' ) : '#' . $createdBy );
        }
        foreach ( self::history( $job ) as $h )
            $rows[] = $row( $h['key'], $h['label'], $h['text'] );
        $groups[] = array( 'id' => 'who', 'title' => $tr( 'Who' ), 'rows' => $rows );

        // what
        $rows = array( $row( 'operation', $tr( 'Operation' ), $info['operation'] ) );
        $source = $info['node_id'] ? \eZContentObjectTreeNode::fetch( $info['node_id'] ) : null;
        $rows[] = $row( 'source', $job->type() === 'copy' ? $tr( 'Source' ) : $tr( 'Subtree' ),
                        $source ? self::pathName( $source ) : $info['name'], $source ? 'content/view/full/' . $info['node_id'] : '' );
        if ( $source )
        {
            $modifier = \eZContentObject::fetch( (int) $source->object()->attribute( 'current' )->attribute( 'creator_id' ) );
            $rows[] = $row( 'source_modified', $tr( 'Last modified' ),
                            \eZLocale::instance()->formatShortDateTime( $source->object()->attribute( 'modified' ) )
                            . ( $modifier ? ' - ' . $modifier->attribute( 'name' ) : '' ) );
        }
        if ( $info['target_node_id'] )
        {
            $target = \eZContentObjectTreeNode::fetch( $info['target_node_id'] );
            $rows[] = $row( 'target', $tr( 'Target' ), $target ? self::pathName( $target ) : $info['target_name'],
                            'content/view/full/' . $info['target_node_id'] );
        }
        if ( $job->type() === 'move' && $job->get( 'gui_old_path' ) )
            $rows[] = $row( 'old_path', $tr( 'Old place' ), $job->get( 'gui_old_path' ) );
        if ( !empty( $result['new_path'] ) )
            $rows[] = $row( 'new_path', $tr( 'New place' ), is_array( $result['new_path'] ) ? implode( ' / ', $result['new_path'] ) : $result['new_path'] );
        foreach ( self::options( $job ) as $o )
            $rows[] = $row( $o['key'], $o['label'], $o['text'] );
        if ( $job->state() === 'done' || $job->state() === 'cancelled' )
        {
            if ( $job->type() === 'copy' && !empty( $result['new_root_node_id'] ) && \eZContentObjectTreeNode::fetch( (int) $result['new_root_node_id'] ) )
                $rows[] = $row( 'new_copy', $tr( 'The copy' ), self::pathName( \eZContentObjectTreeNode::fetch( (int) $result['new_root_node_id'] ) ),
                                'content/view/full/' . (int) $result['new_root_node_id'] );
            if ( $job->type() === 'remove' && !empty( $params['move_to_trash'] ) )
                $rows[] = $row( 'went', $tr( 'Where the items went' ), $tr( 'To the trash, where they can be restored' ), 'content/trash' );
        }
        $groups[] = array( 'id' => 'what', 'title' => $tr( 'What' ), 'rows' => $rows );

        // counts
        $rows = array( $row( 'total', $tr( 'Items' ), $tr( '%done of %total done', array( '%done' => $info['done'], '%total' => $info['total'] ) ) ) );
        $labels = array( 'removed' => $tr( 'Removed' ), 'trashed' => $tr( 'Moved to the trash' ), 'removed_nodes' => $tr( 'Locations removed' ),
                         'removed_objects' => $tr( 'Objects removed' ), 'trashed_objects' => $tr( 'Objects moved to the trash' ),
                         'location_only' => $tr( 'Locations removed, object kept (it has other locations)' ),
                         'locations_only' => $tr( 'Locations removed, object kept (it has other locations)' ),
                         'copied_nodes' => $tr( 'Locations copied' ), 'copied_objects' => $tr( 'Objects copied' ),
                         'copied_versions' => $tr( 'Versions copied' ), 'failed' => $tr( 'Failed' ), 'skipped' => $tr( 'Skipped' ),
                         'hidden' => $tr( 'Hidden' ), 'changed' => $tr( 'Changed' ), 'warning_count' => $tr( 'Warnings' ) );
        foreach ( $result as $key => $value )
        {
            if ( is_scalar( $value ) && $key !== 'new_root_node_id' )
                $rows[] = $row( 'count_' . $key, isset( $labels[$key] ) ? $labels[$key] : $key, $value );
            else if ( $key === 'warnings' && is_array( $value ) && !isset( $result['warning_count'] ) )
                $rows[] = $row( 'count_warnings', $tr( 'Warnings' ), count( $value ) );
        }
        $groups[] = array( 'id' => 'counts', 'title' => $tr( 'Counts' ), 'rows' => $rows );

        // when
        $rows = array( $row( 'created', $tr( 'Created' ), self::time( $job->created() ) ) );
        if ( $job->started() )
        {
            $rows[] = $row( 'started', $tr( 'Started' ), self::time( $job->started() ) );
            $rows[] = $row( 'waited', $tr( 'Waited for a worker' ), self::duration( $job->started() - $job->created() ) );
        }
        if ( $job->finished() )
            $rows[] = $row( 'finished', $tr( 'Finished' ), self::time( $job->finished() ) );
        if ( $job->started() )
            $rows[] = $row( 'duration', $tr( 'Duration' ), self::duration( ( $job->finished() ?: $now ) - $job->started() ) );
        $stats = self::batchStats( $job );
        if ( $job->isActive() && $stats['seconds'] > 0 && $info['done'] > 0 && $info['total'] > $info['done'] )
            $rows[] = $row( 'eta', $tr( 'Estimated time left' ),
                            self::duration( ( $info['total'] - $info['done'] ) * $stats['seconds'] / $info['done'] ) );
        $groups[] = array( 'id' => 'when', 'title' => $tr( 'When' ), 'rows' => $rows );

        // batches
        $ini = \eZINI::instance( 'content.ini' );
        $rows = array( $row( 'batch_size', $tr( 'Batch size' ), (int) $ini->variable( 'ContentJobSettings', 'BatchSize' ) ),
                       $row( 'batch', $tr( 'Batch' ), $info['batch'] ) );
        if ( $stats['count'] )
        {
            $rows[] = $row( 'batch_average', $tr( 'Average batch' ), sprintf( '%.2f s', $stats['average'] ) );
            $rows[] = $row( 'batch_slowest', $tr( 'Slowest batch' ), sprintf( '%.2f s (%s %d)', $stats['slowest'], $tr( 'batch' ), $stats['slowest_batch'] ) );
            $rows[] = $row( 'batch_peak', $tr( 'Peak memory' ), sprintf( '%.1f MB', $stats['peak_mb'] ) );
        }
        $groups[] = array( 'id' => 'batches', 'title' => $tr( 'Batches' ), 'rows' => $rows );

        // where it runs
        $rows = array();
        if ( $job->siteaccess() !== '' )
            $rows[] = $row( 'siteaccess', $tr( 'Siteaccess' ), $job->siteaccess(), '', true );
        $server = $job->get( 'server' );
        if ( is_array( $server ) )
        {
            foreach ( array( 'created' => $tr( 'Started from' ), 'worker' => $tr( 'Worker runs on' ) ) as $k => $label )
                if ( !empty( $server[$k] ) && is_array( $server[$k] ) )
                    $rows[] = $row( 'server_' . $k, $label, self::serverText( $server[$k] ) );
        }
        else if ( $server )
            $rows[] = $row( 'server', $tr( 'Server' ), (string) $server );
        if ( $job->get( 'worker_pid' ) )
            $rows[] = $row( 'pid', $tr( 'Worker process' ), (int) $job->get( 'worker_pid' ), '', true );
        if ( $job->isActive() )
        {
            $alive = $job->workerAlive();
            $age = $job->heartbeat() ? $now - $job->heartbeat() : 0;
            if ( $alive )
                $text = $tr( 'alive, last heartbeat %age ago', array( '%age' => self::duration( $age ) ) );
            else if ( $job->state() === 'queued' && !$job->isStale() )
                $text = $tr( 'not started yet; the cronjob part contentjobs starts it if the server could not' );
            else
                $text = $tr( 'not running (stalled); the cronjob part contentjobs or Resume starts it again' );
            $rows[] = $row( 'worker', $tr( 'Worker' ), $text );
        }
        $rows[] = $row( 'attempts', $tr( 'Worker starts' ), $job->attempts() );
        foreach ( self::lockRows( $job ) as $l )
            $rows[] = $row( 'lock', $tr( 'Lock held' ), $l['text'], $l['url'] );
        $groups[] = array( 'id' => 'where', 'title' => $tr( 'Where it runs' ), 'rows' => $rows );

        return $groups;
    }

    /**
     * "Velocity on web1, pid 123, as alpha" from the engine's server record { server, sapi, host, pid, user }.
     *
     * @param array $s
     * @return string
     */
    public static function serverText( array $s )
    {
        $names = array( 'apache-fpm' => 'Apache / PHP-FPM', 'velocity' => 'Exponential Velocity', 'cli' => \ezpI18n::tr( 'design/admin/content/job', 'command line' ) );
        $text = isset( $s['server'] ) ? ( isset( $names[$s['server']] ) ? $names[$s['server']] : (string) $s['server'] ) : '';
        if ( !empty( $s['host'] ) )
            $text .= ' ' . \ezpI18n::tr( 'design/admin/content/job', 'on %host', null, array( '%host' => $s['host'] ) );
        if ( !empty( $s['pid'] ) )
            $text .= ', pid ' . (int) $s['pid'];
        if ( !empty( $s['user'] ) )
            $text .= ', ' . \ezpI18n::tr( 'design/admin/content/job', 'as %user', null, array( '%user' => $s['user'] ) );
        return $text;
    }

    /**
     * The short server name for the jobs list: where the job was started from.
     *
     * @param \expContentJob $job
     * @return string
     */
    public static function serverShort( $job )
    {
        $s = $job->get( 'server' );
        if ( is_array( $s ) )
        {
            $names = array( 'apache-fpm' => 'Apache', 'velocity' => 'Velocity', 'cli' => 'CLI' );
            $k = isset( $s['created']['server'] ) ? $s['created']['server'] : '';
            return isset( $names[$k] ) ? $names[$k] : (string) $k;
        }
        return is_string( $s ) ? $s : '';
    }

    protected static function time( $t )
    {
        return $t ? \eZLocale::instance()->formatShortDateTime( $t ) : '';
    }

    /**
     * The options the job runs with, as rows.
     */
    public static function options( $job )
    {
        $tr = function ( $text ) { return \ezpI18n::tr( 'design/admin/content/job', $text ); };
        $p = $job->params();
        $rows = array();
        if ( $job->type() === 'remove' )
            $rows[] = array( 'key' => 'opt_trash', 'label' => $tr( 'Removed items' ),
                             'text' => !empty( $p['move_to_trash'] ) ? $tr( 'moved to the trash (can be restored)' ) : $tr( 'deleted permanently' ) );
        if ( $job->type() === 'copy' )
        {
            $rows[] = array( 'key' => 'opt_versions', 'label' => $tr( 'Versions' ), 'text' => !empty( $p['all_versions'] ) ? $tr( 'all versions' ) : $tr( 'the published version only' ) );
            $rows[] = array( 'key' => 'opt_creator', 'label' => $tr( 'Creator' ), 'text' => !empty( $p['keep_creator'] ) ? $tr( 'kept' ) : $tr( 'set to the user who copies' ) );
            $rows[] = array( 'key' => 'opt_time', 'label' => $tr( 'Time' ), 'text' => !empty( $p['keep_time'] ) ? $tr( 'kept' ) : $tr( 'set to now' ) );
        }
        if ( $job->get( 'mode' ) )
            $rows[] = array( 'key' => 'opt_mode', 'label' => $tr( 'Chosen mode' ), 'text' => (string) $job->get( 'mode' ) );
        return $rows;
    }

    /**
     * Who cancelled or resumed the job and when: the engine's record when it keeps one, else its log lines.
     */
    public static function history( $job )
    {
        $tr = function ( $text, $args = array() ) { return \ezpI18n::tr( 'design/admin/content/job', $text, null, $args ); };
        $rows = array();
        $name = function ( $id ) {
            if ( !$id )
                return \ezpI18n::tr( 'design/admin/content/job', 'the system (cronjob or command line)' );
            $o = \eZContentObject::fetch( (int) $id );
            return $o ? $o->attribute( 'name' ) : '#' . (int) $id;
        };
        if ( $job->get( 'cancelled_at' ) )
            $rows[] = array( 'key' => 'cancelled', 'label' => $tr( 'Cancelled' ),
                             'text' => self::time( $job->get( 'cancelled_at' ) ) . ' - ' . $name( $job->get( 'cancelled_by' ) ) );
        $resumed = $job->get( 'resumed' );
        if ( is_array( $resumed ) && $resumed )
        {
            foreach ( $resumed as $r )
                $rows[] = array( 'key' => 'resumed', 'label' => $tr( 'Resumed' ),
                                 'text' => self::time( isset( $r['at'] ) ? $r['at'] : 0 ) . ' - ' . $name( isset( $r['by'] ) ? $r['by'] : 0 ) );
        }
        if ( !$rows )
        {
            foreach ( $job->log( 5000 ) as $line )
            {
                if ( preg_match( '/^\[([^\]]+)\] (resumed|cancelled)\b/', $line, $m ) )
                    $rows[] = array( 'key' => $m[2] === 'resumed' ? 'resumed' : 'cancelled',
                                     'label' => $m[2] === 'resumed' ? $tr( 'Resumed' ) : $tr( 'Cancelled' ), 'text' => $m[1] );
            }
        }
        return $rows;
    }

    /**
     * The subtrees the job locks while it is not finished, with their names.
     */
    public static function lockRows( $job )
    {
        if ( !in_array( $job->state(), array( 'queued', 'running', 'failed' ), true ) )
            return array();
        $rows = array();
        try
        {
            foreach ( \expContentJob::handler( $job->type() )->locks( $job->params() ) as $lock )
            {
                $ids = array_values( array_filter( explode( '/', $lock['path'] ) ) );
                $nodeID = (int) end( $ids );
                $node = $nodeID ? \eZContentObjectTreeNode::fetch( $nodeID ) : null;
                $rows[] = array( 'text' => ( $node ? self::pathName( $node ) : $lock['path'] )
                                           . ( $lock['mode'] === 'node' ? ' ' . \ezpI18n::tr( 'design/admin/content/job', '(this node only)' ) : '' ),
                                 'url' => $node ? 'content/view/full/' . $nodeID : '' );
            }
        }
        catch ( \Exception $e )
        {
        }
        return $rows;
    }

    /**
     * What an operation on these subtrees touches, for the confirmation pages: locations, objects, objects that
     * also have locations outside (they keep existing when only these locations go), the classes involved, who
     * last modified each root and when, and the content jobs holding a lock on any of them.
     *
     * @param int[] $nodeIDs the roots
     * @return array( 'locations', 'objects', 'outside', 'classes' => array( array( name, count ) ),
     *                'roots' => array( array( node_id, name, path, modified, modifier ) ), 'jobs' => array( Job::info() ) )
     */
    public static function subtreeSummary( array $nodeIDs )
    {
        $db = \eZDB::instance();
        $summary = array( 'locations' => 0, 'objects' => 0, 'outside' => 0, 'classes' => array(), 'roots' => array(), 'jobs' => array() );
        $paths = array();
        foreach ( array_unique( array_map( 'intval', $nodeIDs ) ) as $id )
        {
            $node = \eZContentObjectTreeNode::fetch( $id );
            if ( !$node instanceof \eZContentObjectTreeNode )
                continue;
            $object = $node->object();
            $modifier = \eZContentObject::fetch( (int) $object->attribute( 'current' )->attribute( 'creator_id' ) );
            $summary['roots'][] = array( 'node_id' => $id, 'name' => $node->attribute( 'name' ), 'path' => self::pathName( $node ),
                                         'modified' => (int) $object->attribute( 'modified' ),
                                         'modifier' => $modifier ? $modifier->attribute( 'name' ) : '' );
            $paths[] = $node->attribute( 'path_string' );
        }
        if ( !$paths )
            return $summary;
        $or = array();
        foreach ( $paths as $p )
            $or[] = "t.path_string LIKE '" . $db->escapeString( $p ) . "%'";
        $in = '( ' . implode( ' OR ', $or ) . ' )';
        $r = $db->arrayQuery( "SELECT COUNT(*) AS l, COUNT(DISTINCT t.contentobject_id) AS o FROM ezcontentobject_tree t WHERE $in" );
        $summary['locations'] = (int) $r[0]['l'];
        $summary['objects'] = (int) $r[0]['o'];
        $notIn = array();
        foreach ( $paths as $p )
            $notIn[] = "o2.path_string NOT LIKE '" . $db->escapeString( $p ) . "%'";
        $r = $db->arrayQuery( "SELECT COUNT(DISTINCT t.contentobject_id) AS c FROM ezcontentobject_tree t, ezcontentobject_tree o2
                               WHERE $in AND o2.contentobject_id = t.contentobject_id AND " . implode( ' AND ', $notIn ) );
        $summary['outside'] = (int) $r[0]['c'];
        $rows = $db->arrayQuery( "SELECT o.contentclass_id AS class_id, COUNT(DISTINCT o.id) AS c FROM ezcontentobject_tree t, ezcontentobject o
                                  WHERE $in AND o.id = t.contentobject_id GROUP BY o.contentclass_id ORDER BY c DESC" );
        foreach ( $rows as $row )
        {
            $class = \eZContentClass::fetch( (int) $row['class_id'] );
            $summary['classes'][] = array( 'name' => $class ? $class->attribute( 'name' ) : '#' . $row['class_id'], 'count' => (int) $row['c'] );
        }
        if ( class_exists( 'expContentJob' ) )
        {
            foreach ( \expContentJob::listFor( null, array( 'queued', 'running', 'failed' ) ) as $job )
            {
                $hit = false;
                try
                {
                    foreach ( \expContentJob::handler( $job->type() )->locks( $job->params() ) as $lock )
                        foreach ( $paths as $p )
                            $hit = $hit || strpos( $lock['path'], $p ) === 0 || strpos( $p, $lock['path'] ) === 0;
                }
                catch ( \Exception $e )
                {
                }
                if ( $hit )
                    $summary['jobs'][] = \Exponential\View\Kernel\Content\Job::info( $job );
            }
        }
        return $summary;
    }

    /**
     * The node a failed job names in its error (the engine's error_node_id, else "node <id>" in the message).
     */
    public static function errorNodeID( $job )
    {
        if ( (int) $job->get( 'error_node_id' ) )
            return (int) $job->get( 'error_node_id' );
        return preg_match( '/node (?:ID = )?(\d+)/i', $job->error(), $m ) ? (int) $m[1] : 0;
    }
}
