<?php
/**
 * File containing the expUpgradeStepRecordTable class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The older record of exp:upgrade, table expupgrade_step, is retired: exp_migration (step 6.0.15-migration-tables)
 * replaced it before 6.0.15 was released, so no released version has it and a new installation never gets it. Only
 * an installation that ran a pre-release of 6.0.15 still has the table. On such an installation the step copies the
 * rows exp_migration does not have yet (expMigrationRecord::copyLegacy(), a row exp_migration has already is newer
 * and kept), checks that every row of expupgrade_step is now in exp_migration, and only then drops the table (on
 * MongoDB the collection).
 *
 * Without the table the step is in place, so it runs once and a second run finds nothing to do.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepRecordTable extends expUpgradeBaseStep
{
    public function id()
    {
        return '6.0.15-upgrade-record-table';
    }

    public function stage()
    {
        return 'database';
    }

    public function description()
    {
        return 'The older record of the upgrade steps (table expupgrade_step): its rows copied into exp_migration, then the table dropped';
    }

    public function after()
    {
        return array( '6.0.15-migration-tables' );
    }

    public function detect( expUpgradeContext $context )
    {
        $exists = expKernelTableSchema::exists( expMigrationRecord::LEGACY_TABLE, $context->db(), true );
        if ( $exists === null )
            return self::effect( self::UNKNOWN, 'cannot tell whether the table ' . expMigrationRecord::LEGACY_TABLE . ' exists' );
        if ( !$exists )
            return self::effect( self::PRESENT, 'in place: no table ' . expMigrationRecord::LEGACY_TABLE );
        $pending = expMigrationRecord::legacyPending( $context->db() );
        $rows = $pending === null ? 'cannot tell how many of its rows are not in ' . expMigrationRecord::TABLE
                                  : "$pending row(s) not in " . expMigrationRecord::TABLE . ' yet';
        return self::effect( self::MISSING, 'missing: the table ' . expMigrationRecord::LEGACY_TABLE . " is still there ($rows)" );
    }

    public function plan( expUpgradeContext $context )
    {
        $db = $context->db();
        if ( expKernelTableSchema::exists( expMigrationRecord::LEGACY_TABLE, $db, true ) !== true )
            return array();
        $lines = array();
        foreach ( self::pendingIds( $db ) as $id )
            $lines[] = 'copy the row ' . $id . ' of ' . expMigrationRecord::LEGACY_TABLE . ' into ' . expMigrationRecord::TABLE;
        $lines[] = 'check that every row of ' . expMigrationRecord::LEGACY_TABLE . ' is in ' . expMigrationRecord::TABLE;
        $lines[] = 'drop table ' . expMigrationRecord::LEGACY_TABLE . ': ' . self::dropSql( $context->engine() );
        return $lines;
    }

    public function apply( expUpgradeContext $context )
    {
        $db = $context->db();
        $table = expMigrationRecord::LEGACY_TABLE;
        $exists = expKernelTableSchema::exists( $table, $db, true );
        if ( $exists === null )
            throw new RuntimeException( "cannot tell whether the table $table exists" );
        if ( !$exists )
            return array();
        if ( expMigrationRecord::exists( $db ) !== true )
            throw new RuntimeException( 'the table ' . expMigrationRecord::TABLE . " does not exist: $table is kept (run 6.0.15-migration-tables first)" );
        $done = array();
        $copied = expMigrationRecord::copyLegacy( $db );
        if ( $copied === false )
            throw new RuntimeException( "could not copy the rows of $table into " . expMigrationRecord::TABLE . ": $table is kept" );
        if ( $copied > 0 )
            $done[] = "copied $copied row(s) of $table into " . expMigrationRecord::TABLE;
        $pending = expMigrationRecord::legacyPending( $db );
        if ( $pending !== 0 )
            throw new RuntimeException( ( $pending === null ? 'cannot tell whether every row' : "$pending row(s)" ) . " of $table "
                                        . ( $pending === null ? 'is' : 'are not' ) . ' in ' . expMigrationRecord::TABLE . ": $table is kept" );
        $done[] = "every row of $table is in " . expMigrationRecord::TABLE;
        expUpgradeCatalog::execute( $db, self::dropSql( $context->engine() ) . "\n" );
        expKernelTableSchema::reset();
        if ( expKernelTableSchema::exists( $table, $db, true ) !== false )
            throw new RuntimeException( "the table $table is still there after DROP TABLE" );
        $done[] = "dropped table $table";
        return $done;
    }

    /**
     * The ids of the rows of expupgrade_step that exp_migration does not have yet (what apply() copies).
     *
     * @param eZDBInterface $db
     * @return string[]
     */
    public static function pendingIds( $db )
    {
        $legacy = array();
        foreach ( (array)expKernelTableSchema::tryArrayQuery( $db, 'SELECT step_id FROM ' . expMigrationRecord::LEGACY_TABLE ) as $row )
        {
            $row = array_change_key_case( (array)$row, CASE_LOWER );
            if ( isset( $row['step_id'] ) && (string)$row['step_id'] !== '' )
                $legacy[(string)$row['step_id']] = true;
        }
        foreach ( expMigrationRecord::all( $db, expMigrationRecord::SOURCE_UPGRADE ) as $row )
            unset( $legacy[$row['migration_id']] );
        $ids = array_keys( $legacy );
        sort( $ids );
        return array_map( 'strval', $ids );
    }

    /**
     * The statement that drops the table (Oracle also purges it from the recycle bin).
     *
     * @param string $engine
     * @return string
     */
    protected static function dropSql( $engine )
    {
        return 'DROP TABLE ' . expMigrationRecord::LEGACY_TABLE . ( $engine === 'oracle' ? ' PURGE' : '' ) . ';';
    }
}
