<?php
/**
 * File containing the expStaticStatus class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The record of static publishing in the database:
 *
 *   exp_static_run   one row per publish or rollback of a target: its state, the counts and bytes of the build, the
 *                    commit, the tag static/<target>/<n>, the push state, who ran it, when
 *   exp_static_page  one row per page of a target as last published: its path, node, content hash, size, when it
 *                    was built and whether it is stale (a content publish after it marks it so)
 *
 * Both are made by the upgrade step 6.0.15-static-publish-tables (share/db_schema.dba, every engine). Until they
 * exist nothing is read or written: a read is empty, a write returns false; publishing works all the same.
 * The SQL is the plain SELECT, INSERT and DELETE every engine's driver takes (MongoDB through expMongoDB too).
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticStatus
{
    const RUN_TABLE = 'exp_static_run';
    const PAGE_TABLE = 'exp_static_page';

    /** The columns of exp_static_run: name => array( type, length ) */
    const RUN_COLUMNS = array(
        'run_id' => array( 'string', 64 ),
        'target' => array( 'string', 50 ),
        'kind' => array( 'string', 20 ),
        'state' => array( 'string', 20 ),
        'pages' => array( 'int', 0 ),
        'files' => array( 'int', 0 ),
        'bytes' => array( 'int', 0 ),
        'added' => array( 'int', 0 ),
        'changed' => array( 'int', 0 ),
        'removed' => array( 'int', 0 ),
        'commit_id' => array( 'string', 64 ),
        'tag_name' => array( 'string', 150 ),
        'push_state' => array( 'string', 20 ),
        'run_by' => array( 'string', 150 ),
        'started_at' => array( 'int', 0 ),
        'finished_at' => array( 'int', 0 ),
        'message' => array( 'string', 1000 ),
    );

    /** The columns of exp_static_page */
    const PAGE_COLUMNS = array(
        'target' => array( 'string', 50 ),
        'path' => array( 'string', 255 ),
        'node_id' => array( 'int', 0 ),
        'hash' => array( 'string', 64 ),
        'page_bytes' => array( 'int', 0 ),
        'built_at' => array( 'int', 0 ),
        'stale' => array( 'int', 0 ),
    );

    /** The kinds of a run */
    const PUBLISH = 'publish';
    const ROLLBACK = 'rollback';

    /** The states of a run: committed, nothing to commit, failed */
    const DONE = 'done';
    const UNCHANGED = 'unchanged';
    const FAILED = 'failed';

    /**
     * Whether both tables exist (true, false, null when it cannot be told).
     *
     * @param eZDBInterface $db
     * @return bool|null
     */
    public static function exists( $db )
    {
        $run = expKernelTableSchema::exists( self::RUN_TABLE, $db, true );
        $page = expKernelTableSchema::exists( self::PAGE_TABLE, $db, true );
        if ( $run === null || $page === null )
            return null;
        return $run && $page;
    }

    /**
     * Writes a run (a second write of the same run id replaces it).
     *
     * @param eZDBInterface|null $db
     * @param array $run
     * @return bool false without the tables
     */
    public static function writeRun( $db, array $run )
    {
        $run = self::normalize( $run, self::RUN_COLUMNS );
        if ( !preg_match( '/^[A-Za-z0-9._-]{1,64}$/', $run['run_id'] ) )
            throw new InvalidArgumentException( 'a run needs a run id of letters, digits, ".", "_" and "-"' );
        if ( !$db || self::exists( $db ) !== true )
            return false;
        $db->begin();
        try
        {
            $db->query( 'DELETE FROM ' . self::RUN_TABLE . " WHERE run_id = '" . $db->escapeString( $run['run_id'] ) . "'" );
            $db->query( self::insertSql( $db, self::RUN_TABLE, $run ) );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        return true;
    }

    /**
     * The runs of a target (or of all), newest first.
     *
     * @param eZDBInterface|null $db
     * @param string|null $target
     * @param int $limit 0 for all
     * @return array[]
     */
    public static function runs( $db, $target = null, $limit = 20 )
    {
        if ( !$db || self::exists( $db ) !== true )
            return array();
        $sql = 'SELECT * FROM ' . self::RUN_TABLE . ( $target !== null ? " WHERE target = '" . $db->escapeString( (string)$target ) . "'" : '' );
        $runs = array();
        foreach ( (array)expKernelTableSchema::tryArrayQuery( $db, $sql ) as $row )
            $runs[] = self::normalize( array_change_key_case( (array)$row, CASE_LOWER ), self::RUN_COLUMNS );
        usort( $runs, function ( $a, $b ) {
            return $b['started_at'] <=> $a['started_at'] ?: strcmp( $b['run_id'], $a['run_id'] );
        } );
        return $limit > 0 ? array_slice( $runs, 0, (int)$limit ) : $runs;
    }

    /**
     * The runs whose push failed (push_state pending), oldest first.
     *
     * @param eZDBInterface|null $db
     * @param string|null $target
     * @return array[]
     */
    public static function pending( $db, $target = null )
    {
        return array_values( array_reverse( array_filter( self::runs( $db, $target, 0 ), function ( $run ) {
            return $run['push_state'] === expStaticRepository::PENDING;
        } ) ) );
    }

    /**
     * Sets the push state of runs.
     *
     * @param eZDBInterface|null $db
     * @param string[] $runIds
     * @param string $state
     * @return int rows changed
     */
    public static function setPushState( $db, array $runIds, $state )
    {
        if ( !$db || !$runIds || self::exists( $db ) !== true )
            return 0;
        $n = 0;
        foreach ( self::runs( $db, null, 0 ) as $run )
            if ( in_array( $run['run_id'], $runIds, true ) && $run['push_state'] !== $state )
            {
                $run['push_state'] = $state;
                self::writeRun( $db, $run );
                $n++;
            }
        return $n;
    }

    /**
     * Replaces the pages of a target with the pages of a manifest (after a publish or a rollback): every page as
     * built, none stale.
     *
     * @param eZDBInterface|null $db
     * @param string $target
     * @param expStaticManifest $manifest
     * @return bool false without the tables
     */
    public static function writePages( $db, $target, expStaticManifest $manifest )
    {
        if ( !$db || self::exists( $db ) !== true )
            return false;
        $meta = $manifest->meta();
        $builtAt = isset( $meta['built_at'] ) ? (int)strtotime( (string)$meta['built_at'] ) : time();
        // a content change after the build began stays queued (expStaticPublishQueue): its mark is kept
        $late = array();
        foreach ( self::pages( $db, $target ) as $path => $row )
            if ( $row['stale'] > 0 && $row['stale'] >= ( $builtAt ?: time() ) )
                $late[$path] = $row;
        $db->begin();
        try
        {
            $db->query( 'DELETE FROM ' . self::PAGE_TABLE . " WHERE target = '" . $db->escapeString( (string)$target ) . "'" );
            foreach ( $manifest->entries() as $path => $entry )
            {
                if ( !isset( $entry['kind'] ) || $entry['kind'] !== 'page' || strlen( $path ) > 255 )
                    continue;
                $db->query( self::insertSql( $db, self::PAGE_TABLE, self::normalize( array(
                    'target' => (string)$target, 'path' => $path, 'node_id' => isset( $entry['node'] ) ? (int)$entry['node'] : 0,
                    'hash' => isset( $entry['sha256'] ) ? (string)$entry['sha256'] : '', 'page_bytes' => isset( $entry['size'] ) ? (int)$entry['size'] : 0,
                    'built_at' => $builtAt ?: time(), 'stale' => isset( $late[$path] ) ? $late[$path]['stale'] : 0 ), self::PAGE_COLUMNS ) ) );
                unset( $late[$path] );
            }
            foreach ( $late as $row )
                if ( strpos( (string)$row['path'], expStaticPublishQueue::QUEUE_PREFIX ) === 0 )
                    $db->query( self::insertSql( $db, self::PAGE_TABLE, $row ) );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        return true;
    }

    /**
     * The pages of a target, by path.
     *
     * @param eZDBInterface|null $db
     * @param string $target
     * @return array[] path => row
     */
    public static function pages( $db, $target )
    {
        if ( !$db || self::exists( $db ) !== true )
            return array();
        $rows = array();
        foreach ( (array)expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . self::PAGE_TABLE . " WHERE target = '" . $db->escapeString( (string)$target ) . "'" ) as $row )
        {
            $row = self::normalize( array_change_key_case( (array)$row, CASE_LOWER ), self::PAGE_COLUMNS );
            $rows[$row['path']] = $row;
        }
        ksort( $rows );
        return $rows;
    }

    /**
     * A row with every column, each of its type and length. Pure.
     *
     * @param array $row
     * @param array $columns
     * @return array
     */
    public static function normalize( array $row, array $columns )
    {
        $clean = array();
        foreach ( $columns as $name => $type )
        {
            $value = isset( $row[$name] ) ? $row[$name] : null;
            $clean[$name] = $type[0] === 'int' ? (int)$value : mb_substr( is_scalar( $value ) ? (string)$value : '', 0, $type[1] );
        }
        return $clean;
    }

    /** @return string */
    protected static function insertSql( $db, $table, array $row )
    {
        $values = array();
        foreach ( $row as $value )
            $values[] = is_int( $value ) ? (string)$value : "'" . $db->escapeString( (string)$value ) . "'";
        return 'INSERT INTO ' . $table . ' ( ' . implode( ', ', array_keys( $row ) ) . ' ) VALUES ( ' . implode( ', ', $values ) . ' )';
    }
}
