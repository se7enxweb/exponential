<?php
/**
 * File containing the expUpgradeStepSqliteCompositeKeys class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * SQLite only: the tables that share/db_schema.dba keys on more than one column (ezcontentobject_attribute on
 * id and version, ezcontentclass, ezworkflow, ezrss_export ...) get that key. Before 6.0.15 the hand-written
 * kernel/sql/sqlite/schema.sql keyed them on id alone (id INTEGER PRIMARY KEY AUTOINCREMENT), so an
 * installation made from that file could not store a second version of an object, a class or a workflow: the
 * new row replaced the old one. SQLite cannot change a primary key in place, so each such table is rebuilt in
 * one transaction: a new table with the key of the .dba (columns of the old table that the .dba does not know
 * are kept), every row copied, the old table dropped, the new one renamed, its indexes created again. A table
 * whose key is already right is left alone, so a second run does nothing.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepSqliteCompositeKeys extends expUpgradeBaseStep
{
    public function id()
    {
        return '6.0.15-sqlite-composite-keys';
    }

    public function stage()
    {
        return 'database';
    }

    public function description()
    {
        return 'SQLite: the tables keyed on more than one column (ezcontentobject_attribute on id and version ...) get that key';
    }

    public function engines()
    {
        return array( 'sqlite' );
    }

    public function detect( expUpgradeContext $context )
    {
        $wrong = self::wrongKeys( $context );
        if ( !$wrong )
            return self::effect( self::PRESENT, 'every table has the primary key of share/db_schema.dba' );
        return self::effect( self::MISSING, self::describeWrong( $wrong ) );
    }

    public function plan( expUpgradeContext $context )
    {
        $plan = array();
        foreach ( self::wrongKeys( $context ) as $table => $keys )
            $plan[] = "rebuild $table with the primary key (" . implode( ', ', $keys['want'] ) . ') instead of (' . implode( ', ', $keys['have'] ) . '), rows and indexes kept';
        return $plan;
    }

    public function apply( expUpgradeContext $context )
    {
        $done = array();
        foreach ( self::wrongKeys( $context ) as $table => $keys )
        {
            self::rebuild( $context->db(), $table, $context->table( $table ) );
            $done[] = "rebuilt $table with the primary key (" . implode( ', ', $keys['want'] ) . ')';
        }
        return $done;
    }

    /**
     * The tables of the kernel schema with a key of more than one column whose key in the database differs.
     *
     * @param expUpgradeContext $context
     * @return array table => array( 'have' => string[], 'want' => string[] )
     */
    public static function wrongKeys( expUpgradeContext $context )
    {
        $db = $context->db();
        $wrong = array();
        foreach ( $context->schema() as $table => $def )
        {
            if ( !is_array( $def ) || !isset( $def['indexes'] ) )
                continue;
            $want = self::primaryFields( $def );
            if ( count( $want ) < 2 )
                continue;
            $have = self::currentKey( $db, $table );
            if ( $have === null || $have === $want )
                continue;
            $wrong[$table] = array( 'have' => $have, 'want' => $want );
        }
        return $wrong;
    }

    /**
     * The primary key columns of $table in the database, in key order; null when there is no such table.
     *
     * @param eZDBInterface $db
     * @param string $table
     * @return string[]|null
     */
    public static function currentKey( $db, $table )
    {
        $info = $db->arrayQuery( 'PRAGMA table_info(' . $table . ')' );
        if ( !is_array( $info ) || !$info )
            return null;
        $key = array();
        foreach ( $info as $row )
            if ( (int)$row['pk'] > 0 )
                $key[(int)$row['pk']] = $row['name'];
        ksort( $key );
        return array_values( $key );
    }

    /**
     * Rebuilds $table with the definition $def of the .dba, in one transaction.
     *
     * @param eZDBInterface $db
     * @param string $table
     * @param array $def
     */
    public static function rebuild( $db, $table, array $def )
    {
        $new = $table . '__exp_rekey';
        $handler = new eZSQLiteSchema( array( 'instance' => expKernelTableSchema::nullDB(), 'schema' => array() ) );
        $create = $handler->generateTableSchema( $new, array( 'fields' => $def['fields'], 'indexes' => $def['indexes'] ),
                                                 array( 'diff_friendly' => false ) );
        $create = trim( strtok( $create, ';' ) );

        // Columns the old table has and the .dba does not: kept, with their declared type.
        $oldColumns = array();
        $extra = array();
        foreach ( $db->arrayQuery( 'PRAGMA table_info(' . $table . ')' ) as $row )
        {
            $oldColumns[] = $row['name'];
            if ( !isset( $def['fields'][$row['name']] ) )
                $extra[] = '  "' . $row['name'] . '" ' . trim( (string)$row['type'] );
        }
        if ( $extra )
            $create = preg_replace( '/^(CREATE TABLE \S+ \(\n)/', '$1' . implode( ",\n", $extra ) . ",\n", $create, 1 );

        // The installation's own indexes (dropped with the table) and those of the .dba.
        $indexSql = array();
        $covered = array();
        foreach ( $db->arrayQuery( "SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = '" . $db->escapeString( $table ) . "' AND sql IS NOT NULL" ) as $row )
        {
            $indexSql[] = $row['sql'];
            $cols = array();
            foreach ( $db->arrayQuery( 'PRAGMA index_info("' . str_replace( '"', '""', $row['name'] ) . '")' ) as $c )
                $cols[(int)$c['seqno']] = $c['name'];
            ksort( $cols );
            $covered[implode( ',', $cols )] = true;
        }
        foreach ( $def['indexes'] as $name => $index )
        {
            if ( $index['type'] === 'primary' )
                continue;
            $fields = array();
            foreach ( $index['fields'] as $f )
                $fields[] = is_array( $f ) ? $f['name'] : $f;
            if ( isset( $covered[implode( ',', $fields )] ) )
                continue;
            $indexSql[] = trim( $handler->generateAddIndexSql( $table, $name, $index, array(), false ), " ;\n" );
        }

        $quoted = '"' . implode( '", "', $oldColumns ) . '"';
        $db->begin();
        try
        {
            try
            {
                foreach ( array_merge( array( $create,
                                              "INSERT INTO $new ($quoted) SELECT $quoted FROM $table",
                                              "DROP TABLE $table",
                                              "ALTER TABLE $new RENAME TO $table" ), $indexSql ) as $sql )
                {
                    if ( $db->query( $sql ) === false )
                        throw new RuntimeException( "$table: the statement failed: $sql" );
                }
            }
            catch ( Exception $e )
            {
                $db->rollback();
                throw $e;
            }
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
    }

    /**
     * @param array $def a table definition of the .dba
     * @return string[]
     */
    private static function primaryFields( array $def )
    {
        foreach ( $def['indexes'] as $index )
        {
            if ( $index['type'] !== 'primary' )
                continue;
            $fields = array();
            foreach ( $index['fields'] as $f )
                $fields[] = is_array( $f ) ? $f['name'] : $f;
            return $fields;
        }
        return array();
    }

    /**
     * @param array $wrong
     * @return string
     */
    private static function describeWrong( array $wrong )
    {
        $parts = array();
        foreach ( $wrong as $table => $keys )
            $parts[] = "$table (" . implode( ', ', $keys['have'] ) . ')';
        return count( $wrong ) . ' table(s) keyed differently from share/db_schema.dba: ' . implode( ', ', $parts );
    }
}
