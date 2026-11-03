<?php
/**
 * File containing the expMigrationDeployRecord class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The record of the deploys of this installation's migrations to remotes (expMigrationRemoteDeploy), on the source:
 * one row per deploy in exp_migration_deploy and one per remote of it in exp_migration_deploy_host. The stored report
 * (var/<site>/migration/deploy/reports/<deploy id>.json) stays the full account; the rows let the administration and
 * other installations' tools list and filter the deploys without reading the files. A second write of the same
 * deploy (the next stage of a staged deploy, a rollback) replaces its rows.
 *
 * The tables come with the upgrade step 6.0.15-migration-deploy-tables (expUpgradeStepMigrationDeployTables); without
 * them nothing is written or read, and deploys work as before. The SQL is the plain SELECT, INSERT and DELETE every
 * engine's driver takes (MongoDB through expMongoDB too).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationDeployRecord
{
    const DEPLOY_TABLE = 'exp_migration_deploy';
    const HOST_TABLE = 'exp_migration_deploy_host';

    /** The columns of exp_migration_deploy: name => array( type, length ) */
    const DEPLOY_COLUMNS = array(
        'deploy_id' => array( 'string', 64 ),
        'mode' => array( 'string', 20 ),
        'state' => array( 'string', 20 ),
        'in_sync' => array( 'int', 0 ),
        'stages' => array( 'int', 0 ),
        'next_stage' => array( 'int', 0 ),
        'run_by' => array( 'string', 150 ),
        'started_at' => array( 'int', 0 ),
        'finished_at' => array( 'int', 0 ),
        'message' => array( 'string', 1000 ),
    );

    /** The columns of exp_migration_deploy_host */
    const HOST_COLUMNS = array(
        'deploy_id' => array( 'string', 64 ),
        'remote_id' => array( 'string', 64 ),
        'environment' => array( 'string', 50 ),
        'transport' => array( 'string', 20 ),
        'address' => array( 'string', 255 ),
        'stage' => array( 'int', 0 ),
        'state' => array( 'string', 20 ),
        'applied' => array( 'int', 0 ),
        'caption' => array( 'string', 150 ),
        'message' => array( 'string', 1000 ),
    );

    /**
     * Whether both tables exist (true, false, null when it cannot be told).
     *
     * @param eZDBInterface $db
     * @return bool|null
     */
    public static function exists( $db )
    {
        $deploy = expKernelTableSchema::exists( self::DEPLOY_TABLE, $db, true );
        $host = expKernelTableSchema::exists( self::HOST_TABLE, $db, true );
        if ( $deploy === null || $host === null )
            return null;
        return $deploy && $host;
    }

    /**
     * The rows of a report. Pure.
     *
     * @param array $report exp-migration-deploy/1
     * @return array deploy (the row of exp_migration_deploy), hosts (the rows of exp_migration_deploy_host)
     */
    public static function rows( array $report )
    {
        $stageOf = array();
        foreach ( isset( $report['stages'] ) ? (array)$report['stages'] : array() as $index => $ids )
            foreach ( (array)$ids as $id )
                $stageOf[(string)$id] = $index + 1;
        $time = function ( $value ) {
            $ts = is_string( $value ) && $value !== '' ? strtotime( $value ) : false;
            return $ts === false ? 0 : (int)$ts;
        };
        $deploy = self::normalize( array(
            'deploy_id' => isset( $report['deploy_id'] ) ? $report['deploy_id'] : '',
            'mode' => isset( $report['mode'] ) ? $report['mode'] : '',
            'state' => isset( $report['state'] ) ? $report['state'] : '',
            'in_sync' => !isset( $report['in_sync'] ) || $report['in_sync'] === null ? -1 : ( $report['in_sync'] ? 1 : 0 ),
            'stages' => isset( $report['stages'] ) ? count( (array)$report['stages'] ) : 0,
            'next_stage' => isset( $report['next_stage'] ) && $report['next_stage'] !== null ? (int)$report['next_stage'] + 1 : 0,
            'run_by' => isset( $report['user'] ) ? $report['user'] : '',
            'started_at' => $time( isset( $report['started_at'] ) ? $report['started_at'] : '' ),
            'finished_at' => $time( isset( $report['finished_at'] ) ? $report['finished_at'] : '' ),
            'message' => isset( $report['errors'][0] ) ? $report['errors'][0] : '',
        ), self::DEPLOY_COLUMNS );
        $hosts = array();
        foreach ( isset( $report['remotes'] ) ? (array)$report['remotes'] : array() as $id => $remote )
            $hosts[] = self::normalize( array(
                'deploy_id' => $deploy['deploy_id'],
                'remote_id' => (string)$id,
                'environment' => isset( $remote['remote']['environment'] ) ? $remote['remote']['environment'] : '',
                'transport' => isset( $remote['remote']['transport'] ) ? $remote['remote']['transport'] : '',
                'address' => isset( $remote['remote']['address'] ) ? $remote['remote']['address'] : '',
                'stage' => isset( $stageOf[(string)$id] ) ? $stageOf[(string)$id] : 0,
                'state' => isset( $remote['state'] ) ? $remote['state'] : '',
                'applied' => isset( $remote['applied'] ) ? count( (array)$remote['applied'] ) : 0,
                'caption' => isset( $remote['caption'] ) ? $remote['caption'] : '',
                'message' => isset( $remote['problems'] ) && $remote['problems'] ? implode( '; ', (array)$remote['problems'] ) : '',
            ), self::HOST_COLUMNS );
        return array( 'deploy' => $deploy, 'hosts' => $hosts );
    }

    /**
     * Writes a deploy and its remotes (a second write replaces them).
     *
     * @param eZDBInterface|null $db
     * @param array $report
     * @return bool false without the tables
     */
    public static function write( $db, array $report )
    {
        $rows = self::rows( $report );
        if ( !expMigrationRecord::validRunId( $rows['deploy']['deploy_id'] ) )
            throw new InvalidArgumentException( 'a deploy record needs the deploy id of its report' );
        if ( !$db || self::exists( $db ) !== true )
            return false;
        $id = "'" . $db->escapeString( $rows['deploy']['deploy_id'] ) . "'";
        $db->begin();
        try
        {
            $db->query( 'DELETE FROM ' . self::HOST_TABLE . ' WHERE deploy_id = ' . $id );
            $db->query( 'DELETE FROM ' . self::DEPLOY_TABLE . ' WHERE deploy_id = ' . $id );
            $db->query( self::insertSql( $db, self::DEPLOY_TABLE, $rows['deploy'] ) );
            foreach ( $rows['hosts'] as $host )
                $db->query( self::insertSql( $db, self::HOST_TABLE, $host ) );
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
     * The deploys, newest first.
     *
     * @param eZDBInterface|null $db
     * @param int $limit 0 for all
     * @return array[]
     */
    public static function deploys( $db, $limit = 20 )
    {
        if ( !$db || self::exists( $db ) !== true )
            return array();
        $rows = array();
        foreach ( (array)expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . self::DEPLOY_TABLE ) as $row )
            $rows[] = self::normalize( array_change_key_case( (array)$row, CASE_LOWER ), self::DEPLOY_COLUMNS );
        usort( $rows, function ( $a, $b ) {
            return $b['started_at'] <=> $a['started_at'] ?: strcmp( $b['deploy_id'], $a['deploy_id'] );
        } );
        return $limit > 0 ? array_slice( $rows, 0, (int)$limit ) : $rows;
    }

    /**
     * The remotes of a deploy, or the deploys of a remote.
     *
     * @param eZDBInterface|null $db
     * @param string|null $deployId
     * @param string|null $remoteId
     * @return array[] in the order of their stage and id
     */
    public static function hosts( $db, $deployId = null, $remoteId = null )
    {
        if ( !$db || self::exists( $db ) !== true )
            return array();
        $where = array();
        if ( $deployId !== null )
            $where[] = "deploy_id = '" . $db->escapeString( (string)$deployId ) . "'";
        if ( $remoteId !== null )
            $where[] = "remote_id = '" . $db->escapeString( (string)$remoteId ) . "'";
        $rows = array();
        foreach ( (array)expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . self::HOST_TABLE . ( $where ? ' WHERE ' . implode( ' AND ', $where ) : '' ) ) as $row )
            $rows[] = self::normalize( array_change_key_case( (array)$row, CASE_LOWER ), self::HOST_COLUMNS );
        usort( $rows, function ( $a, $b ) {
            return strcmp( $b['deploy_id'], $a['deploy_id'] ) ?: ( $a['stage'] <=> $b['stage'] ?: strcmp( $a['remote_id'], $b['remote_id'] ) );
        } );
        return $rows;
    }

    /**
     * A row with every column, each of its type and cut to its length. Pure.
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
