<?php
/**
 * File containing the expUpgradeStepMigrationTables class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The migration record: tables exp_migration (one row per migration, and the record of exp:upgrade from now on) and
 * exp_migration_run (one row per run), then the rows of expupgrade_step copied into exp_migration as migrations of
 * the source "kernel" (expMigrationRecord::copyLegacy()). A row exp_migration has already is kept, so the step can run
 * again and copies nothing; it is missing while a row of expupgrade_step has not been copied.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepMigrationTables extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-migration-tables';
    }

    public function description()
    {
        return 'The migration record (tables exp_migration and exp_migration_run), with the rows of expupgrade_step copied over';
    }

    public function after()
    {
        return array( '6.0.15-upgrade-record-table' );
    }

    public function tables()
    {
        return array( expMigrationRecord::TABLE, expMigrationRecord::RUN_TABLE );
    }

    public function detect( expUpgradeContext $context )
    {
        $effect = parent::detect( $context );
        if ( $effect['effect'] !== self::PRESENT )
            return $effect;
        $pending = expMigrationRecord::legacyPending( $context->db() );
        if ( $pending === null )
            return self::effect( self::UNKNOWN, 'cannot tell: rows of ' . expMigrationRecord::LEGACY_TABLE );
        if ( $pending > 0 )
            return self::effect( self::MISSING, "missing: $pending row(s) of " . expMigrationRecord::LEGACY_TABLE . ' not copied to ' . expMigrationRecord::TABLE );
        return $effect;
    }

    public function plan( expUpgradeContext $context )
    {
        $lines = parent::plan( $context );
        $pending = expMigrationRecord::legacyPending( $context->db() );
        if ( $pending )
            $lines[] = "copy $pending row(s) of " . expMigrationRecord::LEGACY_TABLE . ' into ' . expMigrationRecord::TABLE;
        return $lines;
    }

    public function apply( expUpgradeContext $context )
    {
        $done = parent::apply( $context );
        $copied = expMigrationRecord::copyLegacy( $context->db() );
        if ( $copied === false )
            throw new RuntimeException( 'could not copy the rows of ' . expMigrationRecord::LEGACY_TABLE . ' into ' . expMigrationRecord::TABLE );
        if ( $copied > 0 )
            $done[] = "copied $copied row(s) of " . expMigrationRecord::LEGACY_TABLE . ' into ' . expMigrationRecord::TABLE;
        return $done;
    }
}
