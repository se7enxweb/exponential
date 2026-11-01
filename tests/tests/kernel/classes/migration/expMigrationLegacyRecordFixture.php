<?php
/**
 * The table expupgrade_step of a pre-release of 6.0.15, for the tests of the step that copies its rows into
 * exp_migration and drops it (6.0.15-upgrade-record-table). The kernel schema no longer has the table, so its
 * definition is kept here, exactly as share/db_schema.dba had it, and installed through the engine's schema handler
 * (MySQL/MariaDB, PostgreSQL, SQLite, MongoDB).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationLegacyRecordFixture
{
    /** @return array the schema with the one table expupgrade_step */
    public static function schema()
    {
        $fields = array();
        foreach ( array( 'applied_at' => 0, 'applied_by' => 150, 'checksum' => 64, 'duration_ms' => 0, 'engine' => 30,
                         'message' => 1000, 'stage' => 20, 'state' => 20, 'step_id' => 150, 'version' => 30 ) as $name => $length )
            $fields[$name] = $length === 0 ? array( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 )
                                           : array( 'length' => $length, 'type' => 'varchar', 'not_null' => '1', 'default' => '' );
        return array( expMigrationRecord::LEGACY_TABLE => array(
            'name' => expMigrationRecord::LEGACY_TABLE,
            'fields' => $fields,
            'indexes' => array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( 'step_id' ) ) ),
        ) );
    }

    /**
     * Makes expupgrade_step when it is missing and fills it with exactly these rows (each in the order of
     * expMigrationRecord::LEGACY_COLUMNS).
     *
     * @param eZDBInterface $db
     * @param array[] $rows
     * @return string[] the messages of the install, empty when it worked
     */
    public static function install( $db, array $rows )
    {
        $messages = array();
        if ( !expKernelTableSchema::install( array( expMigrationRecord::LEGACY_TABLE ), $db, self::schema(), $messages ) )
            return $messages ?: array( 'could not create ' . expMigrationRecord::LEGACY_TABLE );
        expKernelTableSchema::reset();
        $db->query( 'DELETE FROM ' . expMigrationRecord::LEGACY_TABLE );
        foreach ( $rows as $row )
        {
            $values = array();
            foreach ( $row as $value )
                $values[] = is_int( $value ) ? (string)$value : "'" . $db->escapeString( (string)$value ) . "'";
            $db->query( 'INSERT INTO ' . expMigrationRecord::LEGACY_TABLE . ' ( ' . implode( ', ', expMigrationRecord::LEGACY_COLUMNS )
                        . ' ) VALUES ( ' . implode( ', ', $values ) . ' )' );
        }
        return array();
    }
}
