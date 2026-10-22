<?php
/**
 * File containing the expKernelTransferHistory class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The run history of the import and export tools (doc/eng-us/features/6.0/transfer-runs.md): the writer every tool
 * calls for its runs, and the reader of the pages that list and count them (the run list and the Statistics of
 * Imports, with their tool filter).
 *
 * Writing (any tool, any engine):
 *
 *   $id = expKernelTransferHistory::start( 'transfer.xrowextract', array(
 *       'direction' => 'export', 'kind' => 'export', 'title' => 'Articles as CSV', 'scope' => 'class',
 *       'scope_ref' => 'article', 'format' => 'csv', 'tool_ref' => $jobID,
 *       'detail_url' => 'xrowextract/jobs/(job)/' . $jobID ) );
 *   expKernelTransferHistory::progress( $id, $done, $total );
 *   expKernelTransferHistory::finish( $id, 'done', array( 'rows_done' => 1200, 'bytes' => 81234, 'file_ref' => 'export.csv' ) );
 *
 *   expKernelTransferHistory::record( 'transfer.bccie', array( ...a run that is over already... ) );
 *   expKernelTransferHistory::findByToolRef( 'transfer.bccie', $jobID );   // the row of the tool's own job
 *
 * A write never throws and never breaks the tool: without the table (an installation not updated yet), or on a
 * database error, it does nothing and returns 0 / false (with a debug warning).
 *
 * Reading: listRuns() and count() with a filter (tool, direction, kind, state, user_id, from, to), visibleFilter()
 * for what a user may see (the tools of the registry the user may use; everybody's runs only with the tool's
 * HistoryAllPermission), statisticsRows() in the shape of a sqliimport_run row (expImportStatistics::normalizeRun()
 * reads it), toolOptions() for a tool filter.
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelTransferHistory
{
    /** The most rows a read returns */
    const MAX_ROWS = 50000;

    /** The progress of a run is written at most once in this many seconds */
    const PROGRESS_INTERVAL = 2;

    /** @var array run id => last progress write */
    protected static $lastProgress = array();

    /**
     * Whether runs can be written here (the table exists).
     *
     * @return bool
     */
    public static function available()
    {
        try
        {
            return expKernelTransferRunSchema::exists() === true;
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * Starts a run: a row in state running (queued with 'state' => 'queued'), started now, as the current user.
     *
     * @param string $tool the registry id (transfer.xrowextract); a short id (xrowextract) gets the prefix
     * @param array $info any column (direction, kind, title, scope, scope_ref, format, tool_ref, job_ref, engine,
     *                    detail_url, rows_total, user_id, state, detail, ...)
     * @return int the run's id, 0 when it could not be written
     */
    public static function start( $tool, array $info = array() )
    {
        $now = time();
        $values = expKernelTransferRun::normalize( $info + array(
            'state' => expKernelTransferRun::STATE_RUNNING,
            'user_id' => self::currentUserID(),
            'siteaccess' => isset( $GLOBALS['eZCurrentAccess']['name'] ) ? (string)$GLOBALS['eZCurrentAccess']['name'] : '',
        ) );
        $values['tool'] = self::toolID( $tool );
        $values['created'] = $now;
        $values['modified'] = $now;
        if ( !isset( $values['started'] ) )
            $values['started'] = $values['state'] === expKernelTransferRun::STATE_QUEUED ? 0 : $now;
        if ( !isset( $values['direction'] ) )
            $values['direction'] = self::directionOf( $values['tool'], isset( $values['kind'] ) ? $values['kind'] : '' );
        if ( !isset( $values['kind'] ) )
            $values['kind'] = $values['direction'] === 'import' ? 'import' : 'export';
        return self::write( function () use ( $values ) {
            $row = new expKernelTransferRun( $values );
            $row->store();
            return (int)$row->attribute( 'id' );
        }, 0 );
    }

    /**
     * Changes a run (any column; the counts, the engine's job, the link ...).
     *
     * @param int $id
     * @param array $info
     * @return bool
     */
    public static function update( $id, array $info )
    {
        $values = expKernelTransferRun::normalize( $info );
        unset( $values['tool'], $values['created'] );
        if ( (int)$id <= 0 || !$values )
            return false;
        return self::write( function () use ( $id, $values ) {
            $row = expKernelTransferRun::fetch( $id );
            if ( !$row )
                return false;
            if ( isset( $values['state'] ) && $values['state'] === expKernelTransferRun::STATE_RUNNING && !(int)$row->attribute( 'started' ) )
                $values['started'] = time();
            if ( isset( $values['detail'] ) && $values['detail'] !== '' )
            {
                // detail is merged, not replaced: the writer of the progress does not drop the errors of the start
                $merged = array_replace( $row->detailArray(), (array)json_decode( $values['detail'], true ) );
                $values['detail'] = (string)json_encode( $merged, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR );
            }
            foreach ( $values as $name => $value )
                $row->setAttribute( $name, $value );
            $row->setAttribute( 'modified', time() );
            $row->store();
            return true;
        }, false );
    }

    /**
     * The progress of a run, written at most once every PROGRESS_INTERVAL seconds (unless $force).
     *
     * @param int $id
     * @param int $done
     * @param int|null $total
     * @param int|null $failed
     * @param bool $force
     * @return bool whether it was written
     */
    public static function progress( $id, $done, $total = null, $failed = null, $force = false )
    {
        $id = (int)$id;
        if ( $id <= 0 )
            return false;
        $now = microtime( true );
        if ( !$force && isset( self::$lastProgress[$id] ) && $now - self::$lastProgress[$id] < self::PROGRESS_INTERVAL )
            return false;
        self::$lastProgress[$id] = $now;
        $info = array( 'rows_done' => (int)$done, 'state' => expKernelTransferRun::STATE_RUNNING );
        if ( $total !== null )
            $info['rows_total'] = (int)$total;
        if ( $failed !== null )
            $info['rows_failed'] = (int)$failed;
        return self::update( $id, $info );
    }

    /**
     * Ends a run: its state (done, failed, cancelled), finished now, the duration from its start.
     *
     * @param int $id
     * @param string $state
     * @param array $info the final counts, bytes, file_ref, error, errors ...
     * @return bool
     */
    public static function finish( $id, $state, array $info = array() )
    {
        if ( !in_array( $state, expKernelTransferRun::FINAL_STATES, true ) )
            $state = expKernelTransferRun::STATE_DONE;
        unset( self::$lastProgress[(int)$id] );
        return self::write( function () use ( $id, $state, $info ) {
            $row = expKernelTransferRun::fetch( $id );
            if ( !$row )
                return false;
            $now = time();
            $finished = isset( $info['finished'] ) && is_numeric( $info['finished'] ) ? (int)$info['finished'] : $now;
            $started = isset( $info['started'] ) && is_numeric( $info['started'] ) ? (int)$info['started'] : (int)$row->attribute( 'started' );
            if ( $started <= 0 )
                $started = $finished;
            $info = array( 'state' => $state, 'finished' => $finished, 'started' => $started,
                           'duration' => max( 0, $finished - $started ) ) + $info;
            // a run that is done without a total handled all it had: the total is what it did
            if ( $state === expKernelTransferRun::STATE_DONE && empty( $info['rows_total'] ) && !(int)$row->attribute( 'rows_total' ) )
            {
                $done = isset( $info['rows_done'] ) ? (int)$info['rows_done'] : (int)$row->attribute( 'rows_done' );
                if ( $done > 0 )
                    $info['rows_total'] = $done;
            }
            return self::update( $id, $info );
        }, false );
    }

    /**
     * Records a run that is over already (one write).
     *
     * @param string $tool
     * @param array $info as start(), with state (done when missing), started and finished
     * @return int
     */
    public static function record( $tool, array $info )
    {
        $state = isset( $info['state'] ) && in_array( $info['state'], expKernelTransferRun::FINAL_STATES, true ) ? $info['state'] : expKernelTransferRun::STATE_DONE;
        $finished = isset( $info['finished'] ) && is_numeric( $info['finished'] ) ? (int)$info['finished'] : time();
        $started = isset( $info['started'] ) && is_numeric( $info['started'] ) && (int)$info['started'] > 0 ? (int)$info['started'] : $finished;
        $info['state'] = $state;
        $info['started'] = $started;
        $info['finished'] = $finished;
        $info['duration'] = max( 0, $finished - $started );
        return self::start( $tool, $info );
    }

    /**
     * The newest run of a tool's own job or run.
     *
     * @param string $tool
     * @param string $toolRef
     * @return expKernelTransferRun|null
     */
    public static function findByToolRef( $tool, $toolRef )
    {
        if ( (string)$toolRef === '' || !self::available() )
            return null;
        try
        {
            $rows = eZPersistentObject::fetchObjectList( expKernelTransferRun::definition(), null,
                array( 'tool' => self::toolID( $tool ), 'tool_ref' => (string)$toolRef ), array( 'id' => 'desc' ), array( 'offset' => 0, 'length' => 1 ) );
        }
        catch ( Throwable $e )
        {
            return null;
        }
        return $rows && $rows[0] instanceof expKernelTransferRun ? $rows[0] : null;
    }

    /**
     * @param int $id
     * @return expKernelTransferRun|null
     */
    public static function fetch( $id )
    {
        if ( (int)$id <= 0 || !self::available() )
            return null;
        try
        {
            return expKernelTransferRun::fetch( $id );
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    // ---- reading ---------------------------------------------------------------------------------------------------

    /**
     * The conditions of a filter (pure): tool (an id or a list), direction, kind (one or a list), state (one or a
     * list), user_id, from (included) and to (excluded) on started.
     *
     * @param array $filter
     * @return array conditions for eZPersistentObject
     */
    public static function conditions( array $filter )
    {
        $conds = array();
        if ( isset( $filter['tool'] ) )
        {
            $tools = array_values( array_unique( array_map( array( __CLASS__, 'toolID' ), array_filter( (array)$filter['tool'], 'strlen' ) ) ) );
            // an empty list matches nothing (a user who may see no tool sees no run)
            $conds['tool'] = count( $tools ) === 1 ? $tools[0] : array( $tools ?: array( '' ) );
        }
        foreach ( array( 'direction' => expKernelTransferRun::DIRECTIONS, 'kind' => expKernelTransferRun::KINDS,
                         'state' => expKernelTransferRun::STATES ) as $name => $allowed )
        {
            if ( !isset( $filter[$name] ) || $filter[$name] === '' || $filter[$name] === array() )
                continue;
            $values = array_values( array_intersect( (array)$filter[$name], $allowed ) );
            $conds[$name] = count( $values ) === 1 ? $values[0] : array( $values ?: array( '' ) );
        }
        if ( isset( $filter['user_id'] ) && is_numeric( $filter['user_id'] ) )
            $conds['user_id'] = (int)$filter['user_id'];
        $from = isset( $filter['from'] ) && is_numeric( $filter['from'] ) ? (int)$filter['from'] : null;
        $to = isset( $filter['to'] ) && is_numeric( $filter['to'] ) ? (int)$filter['to'] : null;
        if ( $from !== null && $to !== null )
            $conds['started'] = array( false, array( $from, max( $from, $to - 1 ) ) );
        elseif ( $from !== null )
            $conds['started'] = array( '>=', $from );
        elseif ( $to !== null )
            $conds['started'] = array( '<', $to );
        return $conds;
    }

    /**
     * Runs, newest first, as plain arrays (expKernelTransferRun::toArray() plus tool_name).
     *
     * @param array $filter see conditions()
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public static function listRuns( array $filter = array(), $offset = 0, $limit = 50 )
    {
        if ( !self::available() )
            return array();
        try
        {
            $rows = eZPersistentObject::fetchObjectList( expKernelTransferRun::definition(), null, self::conditions( $filter ),
                array( 'started' => 'desc', 'id' => 'desc' ),
                array( 'offset' => max( 0, (int)$offset ), 'length' => max( 1, min( self::MAX_ROWS, (int)$limit ) ) ) );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'exp_transfer_run: ' . $e->getMessage(), __METHOD__ );
            return array();
        }
        $names = self::toolNames();
        $out = array();
        foreach ( (array)$rows as $row )
        {
            if ( !$row instanceof expKernelTransferRun )
                continue;
            $run = $row->toArray();
            $run['tool_name'] = isset( $names[$run['tool']] ) ? $names[$run['tool']] : $run['tool'];
            $out[] = $run;
        }
        return $out;
    }

    /**
     * How many runs match a filter.
     *
     * @param array $filter
     * @return int
     */
    public static function count( array $filter = array() )
    {
        if ( !self::available() )
            return 0;
        try
        {
            return (int)eZPersistentObject::count( expKernelTransferRun::definition(), self::conditions( $filter ) );
        }
        catch ( Throwable $e )
        {
            return 0;
        }
    }

    /**
     * What a user may see, as filters: one per tool the user may use (registry), limited to the user's own runs
     * unless the tool's HistoryAllPermission is granted. Tools named in $only (ids) only, when given.
     *
     * @param array|string|null $only tool ids
     * @param callable|null $access (module, function) => bool; the current user's when null
     * @param int|null $userID the user's id; the current user's when null
     * @param expKernelTransferRegistry|null $registry
     * @return array list of array( tool => id, user_id => int|null )
     */
    public static function visibleFilter( $only = null, $access = null, $userID = null, $registry = null )
    {
        $registry = $registry ?: expKernelTransferRegistry::instance();
        $userID = $userID === null ? self::currentUserID() : (int)$userID;
        $wanted = $only === null ? null : array_map( array( __CLASS__, 'toolID' ), (array)$only );
        $out = array();
        foreach ( $registry->available( $access ) as $id => $tool )
        {
            if ( $wanted !== null && !in_array( $id, $wanted, true ) )
                continue;
            $out[] = array( 'tool' => $id, 'user_id' => $tool->mayReadAllRuns( $access ) ? null : $userID );
        }
        return $out;
    }

    /**
     * The runs a user may see that match a filter, newest first (visibleFilter() applied per tool).
     *
     * @param array $filter as conditions(), tool may narrow the visible tools
     * @param int $limit
     * @param callable|null $access
     * @param int|null $userID
     * @return array
     */
    public static function listVisible( array $filter = array(), $limit = 50, $access = null, $userID = null )
    {
        $runs = array();
        foreach ( self::visibleFilter( isset( $filter['tool'] ) ? $filter['tool'] : null, $access, $userID ) as $part )
        {
            $f = array( 'tool' => $part['tool'] ) + $filter;
            if ( $part['user_id'] !== null )
                $f['user_id'] = $part['user_id'];
            $runs = array_merge( $runs, self::listRuns( $f, 0, $limit ) );
        }
        usort( $runs, function ( $a, $b ) { return array( $b['started'], $b['id'] ) <=> array( $a['started'], $a['id'] ); } );
        return array_slice( $runs, 0, max( 1, (int)$limit ) );
    }

    /**
     * A run in the shape of a sqliimport_run row (pure): id, import_id 0, tool, user_id, run_kind (the kind:
     * export, import, dry_run, retry, undo), status (completed, completed_with_errors, failed, cancelled, running),
     * started_at, finished_at, the *_count counters (processed = rows done without the failed ones, failed = rows
     * failed, the others 0), and the run's own title, format, bytes, engine and detail_url.
     *
     * @param array $run expKernelTransferRun::toArray() or a row of the table
     * @return array
     */
    public static function toStatisticsRow( array $run )
    {
        $run = array_change_key_case( $run, CASE_LOWER );
        $done = isset( $run['rows_done'] ) ? (int)$run['rows_done'] : 0;
        $failed = isset( $run['rows_failed'] ) ? (int)$run['rows_failed'] : 0;
        $state = isset( $run['state'] ) ? (string)$run['state'] : '';
        $started = isset( $run['started'] ) ? (int)$run['started'] : 0;
        if ( $started <= 0 && isset( $run['created'] ) )
            $started = (int)$run['created'];
        return array(
            'id' => isset( $run['id'] ) ? (int)$run['id'] : 0,
            'import_id' => 0,
            'tool' => isset( $run['tool'] ) ? (string)$run['tool'] : '',
            'user_id' => isset( $run['user_id'] ) ? (int)$run['user_id'] : 0,
            'run_kind' => isset( $run['kind'] ) && $run['kind'] !== '' ? (string)$run['kind'] : 'export',
            'status' => expKernelTransferRun::outcomeOf( $state, $failed ),
            'started_at' => $started,
            'finished_at' => in_array( $state, expKernelTransferRun::FINAL_STATES, true ) ? (int)( isset( $run['finished'] ) ? $run['finished'] : 0 ) : 0,
            'created_count' => 0,
            'updated_count' => 0,
            'unchanged_count' => 0,
            'skipped_count' => 0,
            'failed_count' => $failed,
            'processed_count' => max( 0, $done - $failed ),
            'undone_count' => 0,
            'title' => isset( $run['title'] ) ? (string)$run['title'] : '',
            'format' => isset( $run['format'] ) ? (string)$run['format'] : '',
            'bytes' => isset( $run['bytes'] ) ? (int)$run['bytes'] : 0,
            'engine' => isset( $run['engine'] ) ? (string)$run['engine'] : '',
            'detail_url' => isset( $run['detail_url'] ) ? (string)$run['detail_url'] : '',
        );
    }

    /**
     * The runs of a period in the shape of sqliimport_run rows, for the statistics (only what the user may see).
     *
     * @param array $filter as conditions() (tool, kind, user_id, from, to ...)
     * @param callable|null $access
     * @param int|null $userID
     * @return array
     */
    public static function statisticsRows( array $filter = array(), $access = null, $userID = null )
    {
        $rows = array();
        foreach ( self::listVisible( $filter, self::MAX_ROWS, $access, $userID ) as $run )
            $rows[] = self::toStatisticsRow( $run );
        return $rows;
    }

    /**
     * The tools a tool filter offers: every tool the user may use, with how many runs it has (visible to the user).
     *
     * @param callable|null $access
     * @param int|null $userID
     * @return array list of array( id, name, runs )
     */
    public static function toolOptions( $access = null, $userID = null )
    {
        $registry = expKernelTransferRegistry::instance();
        $out = array();
        foreach ( self::visibleFilter( null, $access, $userID, $registry ) as $part )
        {
            $f = array( 'tool' => $part['tool'] );
            if ( $part['user_id'] !== null )
                $f['user_id'] = $part['user_id'];
            $tool = $registry->tool( $part['tool'] );
            $out[] = array( 'id' => $part['tool'], 'name' => $tool ? $tool->get( 'name' ) : $part['tool'], 'runs' => self::count( $f ) );
        }
        return $out;
    }

    /**
     * Removes the runs that ended more than $days days ago (transfer.ini [TransferSettings] KeepDays by default).
     *
     * @param int|null $days
     * @return int how many were removed (-1 when unknown)
     */
    public static function purge( $days = null )
    {
        $days = $days === null ? (int)expKernelTransferRunner::setting( 'KeepDays', 365 ) : (int)$days;
        if ( $days <= 0 || !self::available() )
            return 0;
        $before = time() - $days * 86400;
        $conds = array( 'finished' => array( false, array( 1, $before ) ) );
        return (int)self::write( function () use ( $conds ) {
            $n = (int)eZPersistentObject::count( expKernelTransferRun::definition(), $conds );
            if ( $n > 0 )
                eZPersistentObject::removeObject( expKernelTransferRun::definition(), $conds );
            return $n;
        }, -1 );
    }

    // ---- helpers ---------------------------------------------------------------------------------------------------

    /**
     * The registry id of a tool: transfer.<id> (a short id gets the prefix).
     *
     * @param string $tool
     * @return string
     */
    public static function toolID( $tool )
    {
        $tool = trim( (string)$tool );
        return $tool === '' || strpos( $tool, 'transfer.' ) === 0 ? $tool : 'transfer.' . $tool;
    }

    /** @return array tool id => name, of every declared tool */
    public static function toolNames()
    {
        $names = array();
        try
        {
            foreach ( expKernelTransferRegistry::instance()->tools() as $id => $tool )
                $names[$id] = (string)$tool->get( 'name' );
        }
        catch ( Throwable $e )
        {
        }
        return $names;
    }

    /**
     * The direction of a run when none was given: import for the import kinds and an import-only tool, else export.
     *
     * @param string $tool
     * @param string $kind
     * @return string
     */
    public static function directionOf( $tool, $kind )
    {
        if ( in_array( $kind, array( 'import', 'dry_run', 'retry', 'undo' ), true ) )
            return 'import';
        if ( $kind === 'export' )
            return 'export';
        try
        {
            $t = expKernelTransferRegistry::instance()->tool( $tool );
            if ( $t && $t->get( 'direction' ) === 'import' )
                return 'import';
        }
        catch ( Throwable $e )
        {
        }
        return 'export';
    }

    /** @return int the current user's id, 0 without one */
    public static function currentUserID()
    {
        try
        {
            $user = eZUser::currentUser();
            return $user instanceof eZUser ? (int)$user->attribute( 'contentobject_id' ) : 0;
        }
        catch ( Throwable $e )
        {
            return 0;
        }
    }

    /**
     * Runs a write; nothing when the table is missing, $failed on any error (a warning, never an exception).
     *
     * @param callable $code
     * @param mixed $failed
     * @return mixed
     */
    protected static function write( $code, $failed )
    {
        if ( !self::available() )
            return $failed;
        $db = eZDB::instance();
        $before = expAuditIndexSchema::errorHandling( $db );
        try
        {
            $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
            $result = $code();
        }
        catch ( Throwable $e )
        {
            eZDebug::writeWarning( 'exp_transfer_run: ' . $e->getMessage(), __METHOD__ );
            $result = $failed;
        }
        $db->setErrorHandling( $before );
        return $result;
    }
}
