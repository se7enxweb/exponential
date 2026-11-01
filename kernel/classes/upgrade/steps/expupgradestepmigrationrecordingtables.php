<?php
/**
 * File containing the expUpgradeStepMigrationRecordingTables class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The recordings of changes (expMigrationRecording): tables exp_migration_recording (one row per recording) and
 * exp_migration_recording_event (one row per recorded change), from share/db_schema.dba on every engine. New tables
 * only: running it again changes nothing.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepMigrationRecordingTables extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-migration-recording-tables';
    }

    public function description()
    {
        return 'The recordings of changes for migrations (tables exp_migration_recording and exp_migration_recording_event)';
    }

    public function after()
    {
        return array( '6.0.15-migration-tables' );
    }

    public function tables()
    {
        return array( expMigrationRecording::TABLE, expMigrationRecording::EVENT_TABLE );
    }
}
