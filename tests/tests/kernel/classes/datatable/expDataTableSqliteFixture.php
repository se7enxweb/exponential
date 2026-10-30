<?php
/**
 * The fixture of the M2 data table tests (schema, writer, relations, fetch functions): the site's database is a
 * SQLite database in memory set as the global instance, the named connection t691m2 a SQLite file under
 * var/datatable/ (removed afterwards), the class expDTFixtureRow on that connection, and a catalogue of this one
 * class set for the process (expDataTableCatalogue::setInstance()). No installation.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDataTableTestLoader.php';

/** A row of the table t691_m2_fixture on the connection t691m2 */
class expDTFixtureRow extends eZPersistentObject
{
    /** @var bool the definition has the field note too */
    public static $note = false;

    /** @var string */
    public static $connection = 't691m2';

    public static function definition()
    {
        $fields = array(
            'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
            'remote_id' => array( 'name' => 'RemoteID', 'datatype' => 'string', 'default' => '', 'required' => true ),
            'title' => array( 'name' => 'Title', 'datatype' => 'string', 'default' => '', 'required' => true ),
            'price' => array( 'name' => 'Price', 'datatype' => 'float', 'default' => 0, 'required' => true ),
            'node_id' => array( 'name' => 'NodeID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
            'contentobject_id' => array( 'name' => 'ObjectID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
        );
        if ( self::$note )
            $fields['note'] = array( 'name' => 'Note', 'datatype' => 'text', 'default' => '', 'required' => false );
        return array(
            'fields' => $fields,
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'sort' => array( 'id' => 'asc' ),
            'class_name' => 'expDTFixtureRow',
            'name' => 't691_m2_fixture',
            'connection' => self::$connection,
            'relations' => array( 'node_id' => array( 'class' => 'eZContentObjectTreeNode', 'field' => 'node_id' ) ),
            'unique' => array( 't691_m2_fixture_remote' => array( 'remote_id' ) ),
        );
    }
}

trait expDataTableSqliteFixture
{
    /** @var string */
    private $fixtureFile;

    /** @var array|null */
    private $fixtureSavedGlobal;

    /** @var eZSQLite3DB */
    private $fixtureSite;

    /**
     * @param bool $createTable make t691_m2_fixture on the connection
     * @param array $extraEntries more catalogue entries
     */
    private function setUpFixture( $createTable = true, array $extraEntries = array() )
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        if ( !method_exists( 'eZPersistentObject', 'dbFor' ) )
            $this->markTestSkipped( 'eZPersistentObject of another checkout was loaded first (no dbFor())' );
        $this->fixtureSavedGlobal = array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null;
        $this->fixtureSite = new eZSQLite3DB( self::fixtureParameters( ':memory:' ) );
        eZDB::setInstance( $this->fixtureSite );
        expDTFixtureRow::$note = false;
        expDTFixtureRow::$connection = 't691m2';
        $relative = 'var/datatable/t691-m2-' . getmypid() . '-' . substr( md5( static::class . $this->name() ), 0, 8 ) . '.sqlite';
        $this->fixtureFile = eZSys::rootDir() . '/' . $relative;
        expDataConnections::setConnections( array( 't691m2' => array( 'engine' => 'sqlite', 'database' => $relative ) ) );
        expKernelTableSchema::reset();
        $entries = array( 'expDTFixtureRow' => expDataTableCatalogue::entryFromDefinition( 'expDTFixtureRow', expDTFixtureRow::definition(), '', 'tests' ) );
        foreach ( $extraEntries as $entry )
            $entries[$entry['class']] = $entry;
        expDataTableCatalogue::setInstance( new expDataTableCatalogue( $entries ) );
        if ( $createTable )
        {
            $result = expDataTableSchema::apply( expDataTableSchema::fromDefinition( expDTFixtureRow::definition() ), expDataConnections::db( 't691m2' ) );
            $this->assertTrue( $result['ok'], json_encode( $result['tables'] ) );
        }
    }

    private function tearDownFixture()
    {
        expDataTableCatalogue::setInstance( null );
        expDataConnections::setConnections( null );
        expDataConnections::setClassMap( null );
        expKernelTableSchema::reset();
        expDTFixtureRow::$note = false;
        expDTFixtureRow::$connection = 't691m2';
        if ( $this->fixtureSavedGlobal === null )
            unset( $GLOBALS['eZDBGlobalInstance'] );
        elseif ( isset( $this->fixtureSavedGlobal ) )
            $GLOBALS['eZDBGlobalInstance'] = $this->fixtureSavedGlobal[0];
        // the SQLite driver's writer gate leaves <file>.writer-lock beside the database
        expDataConnections::closeAll();
        foreach ( array( '', '-wal', '-shm', '-journal', '.writer-lock' ) as $suffix )
            if ( $this->fixtureFile && is_file( $this->fixtureFile . $suffix ) )
                unlink( $this->fixtureFile . $suffix );
    }

    private static function fixtureParameters( $database )
    {
        return array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $database,
                      'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                      'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                      'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                      'use_persistent_connection' => false, 'show_errors' => false );
    }

    /** @return eZDBInterface */
    private static function fixtureDB()
    {
        return expDataConnections::db( 't691m2' );
    }

    /** @return array rows of the fixture table, by remote_id */
    private static function fixtureRows()
    {
        $rows = array();
        foreach ( (array)self::fixtureDB()->arrayQuery( 'SELECT * FROM t691_m2_fixture ORDER BY id' ) as $row )
            $rows[$row['remote_id']] = $row;
        return $rows;
    }
}
