<?php
/**
 * File containing the expUpgradeRecord class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The record of what exp:upgrade applied, one row per step id (the last result wins). The record is information, not
 * the truth: a step's state comes from detect(), the record only tells "done" (applied by exp:upgrade) from
 * "not-needed" (in place without it) and keeps the last failure's message.
 *
 * Where it is kept: the migration record exp_migration (expMigrationRecord), as migrations of the source "kernel".
 * Step 0, 6.0.15-migration-tables, makes that table. Until it exists nothing is read or written (a write returns
 * false) and every step without a record counts as never run, so the very first steps of an upgrade work without it.
 * The older table expupgrade_step of a pre-release of 6.0.15 is never read here: the step
 * 6.0.15-upgrade-record-table copies its rows into exp_migration and drops it.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeRecord
{
    /** The table the record is written to once it exists. */
    const TABLE = expMigrationRecord::TABLE;

    /**
     * Whether the record table exists (true, false, null when it cannot be told).
     *
     * @param eZDBInterface $db
     * @return bool|null
     */
    public static function exists( $db )
    {
        return expMigrationRecord::exists( $db );
    }

    /**
     * The table the record is written to: exp_migration, or null while it does not exist.
     *
     * @param eZDBInterface $db
     * @return string|null
     */
    public static function table( $db )
    {
        return expMigrationRecord::exists( $db ) === true ? self::TABLE : null;
    }

    /**
     * Every row: step id => array( step_id, stage, version, state, applied_at, duration_ms, applied_by, engine,
     * checksum, message ).
     *
     * @param eZDBInterface $db
     * @return array
     */
    public static function all( $db )
    {
        $all = array();
        foreach ( expMigrationRecord::all( $db, expMigrationRecord::SOURCE_UPGRADE ) as $row )
            $all[$row['migration_id']] = expMigrationRecord::toLegacy( $row );
        return $all;
    }

    /**
     * Writes the result of a step (replaces its row).
     *
     * @param eZDBInterface $db
     * @param expUpgradeStep $step
     * @param string $state done or failed
     * @param int $durationMs
     * @param string $message
     * @return bool false when no record table exists
     */
    public static function write( $db, expUpgradeStep $step, $state, $durationMs, $message = '' )
    {
        $now = time();
        $file = self::file( $step );
        return self::store( $db, array(
            'migration_id' => $step->id(),
            'stage' => $step->stage(),
            'version' => $step->version(),
            'state' => (string)$state,
            'started_at' => max( 0, $now - (int)round( (int)$durationMs / 1000 ) ),
            'finished_at' => $now,
            'duration_ms' => (int)$durationMs,
            'run_by' => self::by(),
            'engine' => expKernelTableSchema::type( $db ),
            'checksum' => $file !== '' ? hash_file( 'sha256', $file ) : '',
            'file_path' => self::relative( $file ),
            'message' => (string)$message,
        ) );
    }

    /**
     * Records a caption (expUpgradeCaption::take()): a row "caption:<id>" with the stage "caption", the kind as its
     * state (pre, post, safety, manual) and the label and source as its message.
     *
     * @param eZDBInterface $db
     * @param string $id
     * @param string $kind
     * @param string $label
     * @param string $source
     * @return bool false when no record table exists
     */
    public static function writeCaption( $db, $id, $kind, $label, $source )
    {
        $now = time();
        return self::store( $db, array( 'migration_id' => 'caption:' . $id, 'stage' => 'caption', 'version' => '', 'state' => (string)$kind,
                                        'started_at' => $now, 'finished_at' => $now, 'duration_ms' => 0, 'run_by' => self::by(),
                                        'engine' => expKernelTableSchema::type( $db ), 'checksum' => '', 'caption_id' => (string)$id,
                                        'message' => "$label ($source)" ) );
    }

    /**
     * The SHA-256 of the file that defines the step's class, so a record says which code applied it.
     *
     * @param expUpgradeStep $step
     * @return string
     */
    public static function checksum( expUpgradeStep $step )
    {
        $file = self::file( $step );
        return $file !== '' ? hash_file( 'sha256', $file ) : '';
    }

    /**
     * Who runs the upgrade: the system user and, in a web request, the signed-in login. Never a password.
     *
     * @return string
     */
    public static function by()
    {
        $who = function_exists( 'posix_geteuid' ) && function_exists( 'posix_getpwuid' ) ? posix_getpwuid( posix_geteuid() ) : false;
        $name = is_array( $who ) && isset( $who['name'] ) ? $who['name'] : (string)get_current_user();
        return php_sapi_name() === 'cli' ? 'cli ' . $name : 'web ' . $name;
    }

    /**
     * Writes a row (in the columns of exp_migration) to exp_migration.
     *
     * @param eZDBInterface $db
     * @param array $row
     * @return bool false while exp_migration does not exist
     */
    protected static function store( $db, array $row )
    {
        if ( self::table( $db ) === null )
            return false;
        $row['source'] = expMigrationRecord::SOURCE_UPGRADE;
        $row['environment'] = expMigrationRecord::environment();
        return expMigrationRecord::write( $db, $row );
    }

    /**
     * The file of the step's class, or ''.
     *
     * @param expUpgradeStep $step
     * @return string
     */
    protected static function file( expUpgradeStep $step )
    {
        $file = ( new ReflectionClass( $step ) )->getFileName();
        return $file && is_readable( $file ) ? $file : '';
    }

    /**
     * A path relative to the root of the installation (the path as it is when it lies elsewhere).
     *
     * @param string $file
     * @return string
     */
    protected static function relative( $file )
    {
        if ( $file === '' )
            return '';
        $root = rtrim( str_replace( '\\', '/', class_exists( 'eZSys' ) ? (string)eZSys::rootDir() : getcwd() ), '/' ) . '/';
        $file = str_replace( '\\', '/', $file );
        return strpos( $file, $root ) === 0 ? substr( $file, strlen( $root ) ) : $file;
    }
}
