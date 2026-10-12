<?php
/**
 * A SQLite database shaped like one that LoveStack (mugoweb/ezpublish-legacy, master e61e9fdf0c)
 * has upgraded: Exponential's SQLite kernel schema and clean data, then LoveStack's schema steps
 * (update/database/mysql/lovestack/1.sql to 4.sql, run by its update/run.php) in SQLite terms:
 *
 *   1  ezsite_data ezpublish-version = 'lovestack'
 *   2  ezuser.password_hash VARCHAR(255)        (Exponential has it already)
 *   3  ezcontentobject_trash.trashed            (Exponential has it already)
 *   4  UNIQUE INDEX ezuser_login ( login ), and data_text NULLs set to '' with the column rebuilt
 *      as data_text longtext NOT NULL DEFAULT ''
 *   -  ezsite_data db-schema-version = 5, the counter update/run.php keeps
 *
 * Thanks to Mugo Web for LoveStack's documented schema steps, which this fixture follows.
 * Used by expLoveStackCheckTest and by the fixture runs of the kernel against such a database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expLoveStackFixture
{
    /**
     * Creates the database file (replacing one that is there).
     *
     * @param string $file the SQLite file
     * @param bool $loveStack false: Exponential's own schema and data only, for comparison
     * @param string|null $root the installation root (kernel/sql/sqlite/)
     * @return SQLite3
     */
    public static function create( $file, $loveStack = true, $root = null )
    {
        $root = $root !== null ? $root : dirname( __DIR__, 4 );
        if ( is_file( $file ) )
            unlink( $file );
        $db = new SQLite3( $file );
        $db->enableExceptions( true );
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $name )
        {
            $sql = file_get_contents( $root . '/kernel/sql/sqlite/' . $name );
            if ( $sql === false )
                throw new RuntimeException( "kernel/sql/sqlite/$name cannot be read" );
            $db->exec( 'BEGIN' );
            $db->exec( $sql );
            $db->exec( 'COMMIT' );
        }
        // The version Exponential writes on installation (cleandata.sql may carry an older one)
        $db->exec( "UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version'" );
        if ( $loveStack )
            self::applyLoveStackSteps( $db );
        else
            self::usePlainLoginIndex( $db );
        return $db;
    }

    /**
     * The plain index on ezuser.login that Exponential databases installed before 6.0.15 have,
     * whatever the current schema file says.
     *
     * @param SQLite3 $db
     */
    public static function usePlainLoginIndex( SQLite3 $db )
    {
        foreach ( self::indexNames( $db, 'ezuser' ) as $name )
        {
            if ( self::indexColumns( $db, $name ) === array( 'login' ) )
                $db->exec( 'DROP INDEX "' . $name . '"' );
        }
        $db->exec( 'CREATE INDEX "idx_ezuser_ezuser_login" ON "ezuser" ( login )' );
    }

    /**
     * LoveStack's schema steps on an Exponential SQLite database.
     *
     * @param SQLite3 $db
     */
    public static function applyLoveStackSteps( SQLite3 $db )
    {
        $db->exec( 'BEGIN' );
        // step 1
        $db->exec( "UPDATE ezsite_data SET value='lovestack' WHERE name='ezpublish-version'" );
        // step 4: unique login (the plain index of Exponential's schema is replaced)
        foreach ( self::indexNames( $db, 'ezuser' ) as $name )
        {
            if ( self::indexColumns( $db, $name ) === array( 'login' ) )
                $db->exec( 'DROP INDEX "' . $name . '"' );
        }
        $db->exec( 'CREATE UNIQUE INDEX ezuser_login ON ezuser ( login )' );
        // step 4: data_text NOT NULL DEFAULT '' (SQLite cannot change a column: the table is rebuilt)
        $db->exec( "UPDATE ezcontentobject_attribute SET data_text = '' WHERE data_text IS NULL" );
        $create = $db->querySingle( "SELECT sql FROM sqlite_master WHERE type='table' AND name='ezcontentobject_attribute'" );
        $indexes = array();
        $result = $db->query( "SELECT sql FROM sqlite_master WHERE type='index' AND tbl_name='ezcontentobject_attribute' AND sql IS NOT NULL" );
        while ( $row = $result->fetchArray( SQLITE3_ASSOC ) )
            $indexes[] = $row['sql'];
        $newCreate = preg_replace( '/`data_text`\s+longtext(?!\s+NOT NULL)/i', "`data_text` longtext NOT NULL DEFAULT ''", $create, 1, $count );
        if ( $count !== 1 )
            throw new RuntimeException( 'ezcontentobject_attribute.data_text not found in the schema' );
        $db->exec( 'ALTER TABLE ezcontentobject_attribute RENAME TO ezcontentobject_attribute_before_lovestack' );
        $db->exec( $newCreate );
        $db->exec( 'INSERT INTO ezcontentobject_attribute SELECT * FROM ezcontentobject_attribute_before_lovestack' );
        $db->exec( 'DROP TABLE ezcontentobject_attribute_before_lovestack' );
        foreach ( $indexes as $sql )
            $db->exec( $sql );
        // the counter of update/run.php after 4.sql
        $db->exec( "INSERT INTO ezsite_data ( name, value ) VALUES ( 'db-schema-version', '5' )" );
        $db->exec( 'COMMIT' );
    }

    /**
     * @param SQLite3 $db
     * @param string $table
     * @return string[]
     */
    protected static function indexNames( SQLite3 $db, $table )
    {
        $names = array();
        $result = $db->query( "PRAGMA index_list('$table')" );
        while ( $row = $result->fetchArray( SQLITE3_ASSOC ) )
            $names[] = $row['name'];
        return $names;
    }

    /**
     * @param SQLite3 $db
     * @param string $index
     * @return string[]
     */
    protected static function indexColumns( SQLite3 $db, $index )
    {
        $columns = array();
        $result = $db->query( "PRAGMA index_info('" . str_replace( "'", "''", $index ) . "')" );
        while ( $row = $result->fetchArray( SQLITE3_ASSOC ) )
            $columns[] = strtolower( $row['name'] );
        return $columns;
    }
}
