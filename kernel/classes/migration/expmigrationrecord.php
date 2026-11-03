<?php
/**
 * File containing the expMigrationRecord class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The record of migrations: table exp_migration, one row per migration (source + id; the last result wins), and
 * table exp_migration_run, one row per run. Plain SQL through the connection, no eZPersistentObject, so it works
 * before the autoloads of extensions exist and on every engine (MySQL/MariaDB, PostgreSQL, SQLite, Oracle with
 * ezoracle, MongoDB through the driver's SQL translation).
 *
 * exp_migration also holds what exp:upgrade records (it replaces expupgrade_step): an upgrade step is a migration of
 * the source "kernel" whose id is the step id. The columns of expupgrade_step and where they went:
 *
 *   expupgrade_step   exp_migration    note
 *   ---------------   --------------   ----------------------------------------------------------------------------
 *   step_id           migration_id     the step id (6.0.15-...), or caption:<id> for a caption row
 *   (none)            source           "kernel" for every upgrade step and caption row (SOURCE_UPGRADE)
 *   stage             stage            code, libraries, database, scripts, finish, caption; "migration" otherwise
 *   version           version          the kernel version of the step; empty for a site or extension migration
 *   state             state            done, failed (a caption row: its kind pre, post, safety, manual ...)
 *   applied_at        finished_at      when the result was written
 *   (none)            started_at       finished_at minus the duration (a copied row), the start of the migration
 *   duration_ms       duration_ms
 *   applied_by        run_by           "cli <system user>" or "web <system user>", never a password
 *   engine            engine           mysql, postgresql, sqlite, oracle, mongodb
 *   checksum          checksum         SHA-256 of the step's class file / of the migration file
 *   message           message          the last message, at most 1000 characters
 *   (none)            file_path        the file of the migration (or of the step's class), relative to the root
 *   (none)            environment      [AdminEnvironment] Level of site.ini (EXP_ENV with "auto")
 *   (none)            run_id           the run (exp_migration_run) the row was written by
 *   (none)            caption_id       the caption taken before it (a caption row: the caption's own id)
 *   (none)            revision         the commit of the migrations repository after the run
 *
 * Names that differ from the plan of the tables: file is file_path, commit is commit_id and tag is tag_name, because
 * FILE is a reserved word on Oracle and COMMIT a keyword everywhere. There is no numeric id: source + migration_id is
 * the key, as step_id was.
 *
 * Until the tables exist (upgrade step 6.0.15-migration-tables) nothing is read or written: every read is empty,
 * every write returns false.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRecord
{
    const TABLE = 'exp_migration';

    const RUN_TABLE = 'exp_migration_run';

    /** The table of exp:upgrade that exp_migration replaces; its rows are copied by copyLegacy(). */
    const LEGACY_TABLE = 'expupgrade_step';

    /** The source of every row exp:upgrade writes. */
    const SOURCE_UPGRADE = 'kernel';

    /** The columns of exp_migration: name => array( type, length ) ("int" or "string" with its maximum length). */
    const COLUMNS = array(
        'source' => array( 'string', 100 ),
        'migration_id' => array( 'string', 150 ),
        'stage' => array( 'string', 20 ),
        'version' => array( 'string', 30 ),
        'file_path' => array( 'string', 255 ),
        'checksum' => array( 'string', 64 ),
        'state' => array( 'string', 20 ),
        'environment' => array( 'string', 50 ),
        'run_id' => array( 'string', 64 ),
        'started_at' => array( 'int', 0 ),
        'finished_at' => array( 'int', 0 ),
        'duration_ms' => array( 'int', 0 ),
        'run_by' => array( 'string', 150 ),
        'caption_id' => array( 'string', 150 ),
        'revision' => array( 'string', 64 ),
        'engine' => array( 'string', 30 ),
        'message' => array( 'string', 1000 ),
    );

    /** The columns of exp_migration_run, as COLUMNS. */
    const RUN_COLUMNS = array(
        'run_id' => array( 'string', 64 ),
        'state' => array( 'string', 20 ),
        'started_at' => array( 'int', 0 ),
        'finished_at' => array( 'int', 0 ),
        'run_by' => array( 'string', 150 ),
        'environment' => array( 'string', 50 ),
        'caption_pre' => array( 'string', 150 ),
        'caption_post' => array( 'string', 150 ),
        'commit_id' => array( 'string', 64 ),
        'tag_name' => array( 'string', 150 ),
        'push_state' => array( 'string', 20 ),
        'log_file' => array( 'string', 255 ),
        'message' => array( 'string', 1000 ),
    );

    /** The columns of expupgrade_step. */
    const LEGACY_COLUMNS = array( 'step_id', 'stage', 'version', 'state', 'applied_at', 'duration_ms', 'applied_by', 'engine',
                                  'checksum', 'message' );

    /**
     * Whether exp_migration exists (true, false, null when it cannot be told).
     *
     * @param eZDBInterface $db
     * @return bool|null
     */
    public static function exists( $db )
    {
        return expKernelTableSchema::exists( self::TABLE, $db, true );
    }

    /**
     * Whether exp_migration_run exists (true, false, null when it cannot be told).
     *
     * @param eZDBInterface $db
     * @return bool|null
     */
    public static function runTableExists( $db )
    {
        return expKernelTableSchema::exists( self::RUN_TABLE, $db, true );
    }

    /**
     * The rows of exp_migration, of one source or of all, sorted by source and id.
     *
     * @param eZDBInterface $db
     * @param string|null $source
     * @return array[] each a row with every column of COLUMNS
     */
    public static function all( $db, $source = null )
    {
        if ( self::exists( $db ) !== true )
            return array();
        $sql = 'SELECT * FROM ' . self::TABLE;
        if ( $source !== null )
            $sql .= " WHERE source = '" . $db->escapeString( (string)$source ) . "'";
        $rows = array();
        foreach ( (array)expKernelTableSchema::tryArrayQuery( $db, $sql ) as $row )
        {
            $row = self::normalize( array_change_key_case( (array)$row, CASE_LOWER ), self::COLUMNS );
            if ( $row['migration_id'] === '' )
                continue;
            $rows[$row['source'] . "\n" . $row['migration_id']] = $row;
        }
        ksort( $rows, SORT_STRING );
        return array_values( $rows );
    }

    /**
     * One row, or null.
     *
     * @param eZDBInterface $db
     * @param string $source
     * @param string $migrationId
     * @return array|null
     */
    public static function get( $db, $source, $migrationId )
    {
        if ( self::exists( $db ) !== true )
            return null;
        $rows = expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . self::TABLE . ' WHERE ' . self::keyWhere( $db, $source, $migrationId ) );
        if ( !$rows )
            return null;
        return self::normalize( array_change_key_case( (array)$rows[0], CASE_LOWER ), self::COLUMNS );
    }

    /**
     * Writes the row of a migration (replaces the row of the same source and id). Columns left out get their empty
     * value; unknown keys are ignored; texts are cut to the column's length.
     *
     * @param eZDBInterface $db
     * @param array $row at least source and migration_id
     * @return bool false when the table does not exist
     * @throws InvalidArgumentException without source or migration_id
     */
    public static function write( $db, array $row )
    {
        $row = self::normalize( $row, self::COLUMNS );
        if ( $row['source'] === '' || $row['migration_id'] === '' )
            throw new InvalidArgumentException( 'a migration row needs a source and an id' );
        if ( self::exists( $db ) !== true )
            return false;
        $db->begin();
        try
        {
            $db->query( 'DELETE FROM ' . self::TABLE . ' WHERE ' . self::keyWhere( $db, $row['source'], $row['migration_id'] ) );
            $db->query( self::insertSql( $db, self::TABLE, $row ) );
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
     * Removes the row of a migration (after a rollback of it).
     *
     * @param eZDBInterface $db
     * @param string $source
     * @param string $migrationId
     * @return bool false when the table does not exist
     */
    public static function remove( $db, $source, $migrationId )
    {
        if ( self::exists( $db ) !== true )
            return false;
        $db->query( 'DELETE FROM ' . self::TABLE . ' WHERE ' . self::keyWhere( $db, $source, $migrationId ) );
        return true;
    }

    /**
     * Writes the row of a run (replaces the row of the same run id).
     *
     * @param eZDBInterface $db
     * @param array $run at least run_id
     * @return bool false when the table does not exist
     * @throws InvalidArgumentException without a valid run_id
     */
    public static function writeRun( $db, array $run )
    {
        $run = self::normalize( $run, self::RUN_COLUMNS );
        if ( !self::validRunId( $run['run_id'] ) )
            throw new InvalidArgumentException( 'a run needs a run id of letters, digits, ".", "_" and "-"' );
        if ( self::runTableExists( $db ) !== true )
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
     * One run, or null.
     *
     * @param eZDBInterface $db
     * @param string $runId
     * @return array|null
     */
    public static function run( $db, $runId )
    {
        if ( self::runTableExists( $db ) !== true )
            return null;
        $rows = expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . self::RUN_TABLE . " WHERE run_id = '" . $db->escapeString( (string)$runId ) . "'" );
        return $rows ? self::normalize( array_change_key_case( (array)$rows[0], CASE_LOWER ), self::RUN_COLUMNS ) : null;
    }

    /**
     * The latest runs, newest first.
     *
     * @param eZDBInterface $db
     * @param int $limit 0 for all
     * @return array[]
     */
    public static function runs( $db, $limit = 20 )
    {
        if ( self::runTableExists( $db ) !== true )
            return array();
        $runs = array();
        foreach ( (array)expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . self::RUN_TABLE ) as $row )
            $runs[] = self::normalize( array_change_key_case( (array)$row, CASE_LOWER ), self::RUN_COLUMNS );
        usort( $runs, function ( $a, $b ) {
            return $b['started_at'] <=> $a['started_at'] ?: strcmp( $b['run_id'], $a['run_id'] );
        } );
        return $limit > 0 ? array_slice( $runs, 0, (int)$limit ) : $runs;
    }

    /**
     * How many rows of expupgrade_step are not in exp_migration yet (0 without expupgrade_step; every row of it while
     * exp_migration is missing; null when it cannot be told).
     *
     * @param eZDBInterface $db
     * @return int|null
     */
    public static function legacyPending( $db )
    {
        $legacy = self::legacyRows( $db );
        if ( $legacy === null )
            return null;
        if ( !$legacy )
            return 0;
        $exists = self::exists( $db );
        if ( $exists === null )
            return null;
        if ( !$exists )
            return count( $legacy );
        return count( array_diff_key( $legacy, self::upgradeIds( $db ) ) );
    }

    /**
     * Copies the rows of expupgrade_step that exp_migration does not have yet (source "kernel", the same id). A row
     * already in exp_migration is newer and kept, so running it again copies nothing.
     *
     * @param eZDBInterface $db
     * @return int|false the rows copied, false when exp_migration does not exist or expupgrade_step cannot be read
     */
    public static function copyLegacy( $db )
    {
        if ( self::exists( $db ) !== true )
            return false;
        $legacy = self::legacyRows( $db );
        if ( $legacy === null )
            return false;
        $missing = array_diff_key( $legacy, self::upgradeIds( $db ) );
        if ( !$missing )
            return 0;
        $db->begin();
        try
        {
            foreach ( $missing as $row )
                $db->query( self::insertSql( $db, self::TABLE, self::fromLegacy( $row ) ) );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        return count( $missing );
    }

    /**
     * A row of expupgrade_step as a row of exp_migration (see the table of the class).
     *
     * @param array $legacy
     * @return array
     */
    public static function fromLegacy( array $legacy )
    {
        $legacy = array_change_key_case( $legacy, CASE_LOWER );
        $value = function ( $name ) use ( $legacy ) {
            return isset( $legacy[$name] ) ? $legacy[$name] : '';
        };
        $appliedAt = (int)$value( 'applied_at' );
        $durationMs = (int)$value( 'duration_ms' );
        $id = (string)$value( 'step_id' );
        $stage = (string)$value( 'stage' );
        return self::normalize( array(
            'source' => self::SOURCE_UPGRADE,
            'migration_id' => $id,
            'stage' => $stage,
            'version' => $value( 'version' ),
            'state' => $value( 'state' ),
            'started_at' => $appliedAt > 0 ? max( 0, $appliedAt - (int)round( $durationMs / 1000 ) ) : 0,
            'finished_at' => $appliedAt,
            'duration_ms' => $durationMs,
            'run_by' => $value( 'applied_by' ),
            'engine' => $value( 'engine' ),
            'checksum' => $value( 'checksum' ),
            'message' => $value( 'message' ),
            'caption_id' => $stage === 'caption' && strpos( $id, 'caption:' ) === 0 ? substr( $id, 8 ) : '',
        ), self::COLUMNS );
    }

    /**
     * A row of exp_migration in the shape of a row of expupgrade_step (what expUpgradeRecord::all() returns).
     *
     * @param array $row
     * @return array
     */
    public static function toLegacy( array $row )
    {
        $row = self::normalize( array_change_key_case( $row, CASE_LOWER ), self::COLUMNS );
        return array(
            'step_id' => $row['migration_id'],
            'stage' => $row['stage'],
            'version' => $row['version'],
            'state' => $row['state'],
            'applied_at' => $row['finished_at'],
            'duration_ms' => $row['duration_ms'],
            'applied_by' => $row['run_by'],
            'engine' => $row['engine'],
            'checksum' => $row['checksum'],
            'message' => $row['message'],
        );
    }

    /**
     * The environment of this installation: [AdminEnvironment] Level of site.ini, EXP_ENV when it is "auto"; empty
     * when neither says.
     *
     * @param eZINI|null $ini
     * @return string
     */
    public static function environment( $ini = null )
    {
        try
        {
            $ini = $ini ?: eZINI::instance();
            $level = $ini->hasVariable( 'AdminEnvironment', 'Level' ) ? strtolower( trim( (string)$ini->variable( 'AdminEnvironment', 'Level' ) ) ) : '';
        }
        catch ( Throwable $e )
        {
            $level = '';
        }
        if ( $level === 'auto' )
            $level = strtolower( trim( (string)getenv( 'EXP_ENV' ) ) );
        return preg_match( '/^[a-z0-9][a-z0-9_.-]{0,49}$/', $level ) ? $level : '';
    }

    /**
     * Whether a run id can name a run (and a log file): letters, digits, ".", "_", "-", at most 64, not starting with
     * a dot.
     *
     * @param string $runId
     * @return bool
     */
    public static function validRunId( $runId )
    {
        return is_string( $runId ) && preg_match( '/^[A-Za-z0-9_-][A-Za-z0-9_.-]{0,63}$/', $runId ) === 1;
    }

    /**
     * A row with exactly the given columns: missing ones empty, integers cast, texts cut to their length.
     *
     * @param array $row
     * @param array $columns COLUMNS or RUN_COLUMNS
     * @return array
     */
    public static function normalize( array $row, array $columns )
    {
        $clean = array();
        foreach ( $columns as $name => $type )
        {
            $value = isset( $row[$name] ) ? $row[$name] : null;
            if ( $type[0] === 'int' )
                $clean[$name] = (int)$value;
            else
                $clean[$name] = mb_substr( is_scalar( $value ) ? (string)$value : '', 0, $type[1] );
        }
        return $clean;
    }

    /**
     * The rows of expupgrade_step by step id (empty without the table, null when it cannot be read).
     *
     * @param eZDBInterface $db
     * @return array|null
     */
    protected static function legacyRows( $db )
    {
        $exists = expKernelTableSchema::exists( self::LEGACY_TABLE, $db, true );
        if ( $exists === null )
            return null;
        if ( !$exists )
            return array();
        $rows = expKernelTableSchema::tryArrayQuery( $db, 'SELECT * FROM ' . self::LEGACY_TABLE );
        if ( $rows === null )
            return null;
        $legacy = array();
        foreach ( $rows as $row )
        {
            $row = array_change_key_case( (array)$row, CASE_LOWER );
            if ( isset( $row['step_id'] ) && (string)$row['step_id'] !== '' )
                $legacy[(string)$row['step_id']] = $row;
        }
        return $legacy;
    }

    /**
     * The ids of the rows of the source "kernel" in exp_migration, as keys.
     *
     * @param eZDBInterface $db
     * @return array
     */
    protected static function upgradeIds( $db )
    {
        $ids = array();
        foreach ( self::all( $db, self::SOURCE_UPGRADE ) as $row )
            $ids[$row['migration_id']] = true;
        return $ids;
    }

    /**
     * @param eZDBInterface $db
     * @param string $source
     * @param string $migrationId
     * @return string
     */
    protected static function keyWhere( $db, $source, $migrationId )
    {
        return "source = '" . $db->escapeString( (string)$source ) . "' AND migration_id = '" . $db->escapeString( (string)$migrationId ) . "'";
    }

    /**
     * @param eZDBInterface $db
     * @param string $table
     * @param array $row normalized
     * @return string
     */
    protected static function insertSql( $db, $table, array $row )
    {
        $values = array();
        foreach ( $row as $value )
            $values[] = is_int( $value ) ? (string)$value : "'" . $db->escapeString( (string)$value ) . "'";
        return 'INSERT INTO ' . $table . ' ( ' . implode( ', ', array_keys( $row ) ) . ' ) VALUES ( ' . implode( ', ', $values ) . ' )';
    }
}
