<?php
/**
 * eZPersistentObject on SQLite never replaces a row of another version.
 *
 * eZPersistentObject::storeObject() inserts with "INSERT OR REPLACE" on SQLite. REPLACE deletes
 * every row that collides with the new one on any unique key. A schema made by the Platform
 * (Doctrine) gave ezcontentobject_attribute the key (id) alone while the legacy kernel stores a
 * row per (id, version): copying an attribute into a new version replaced the published
 * version's row, and every edit in the legacy admin lost the published content. The driver now
 * tells when REPLACE could hit another row (insertOrReplaceIsSafe()) and the insert is then made
 * without it, so a collision fails instead of deleting data.
 *
 * Database: SQLite in memory.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */
class eZSQLite3InsertOrReplaceTest extends PHPUnit\Framework\TestCase
{
    /** @var array the debug globals before the test */
    private $savedDebug = array();

    /** @var eZDBInterface|null the eZDB instance before the test */
    private $savedInstance = null;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        if ( !method_exists( 'eZDebug', 'createAccumulatorGroup' ) )
            $this->markTestSkipped( 'eZDebug is a stand-in declared by another test of this run' );
        $this->savedInstance = isset( $GLOBALS['eZDBGlobalInstance'] ) ? $GLOBALS['eZDBGlobalInstance'] : null;
        foreach ( array( 'eZDebugGlobalInstance', 'eZDebugEnabled' ) as $key )
            $this->savedDebug[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $GLOBALS['eZDebugGlobalInstance'] = new eZDebug();
        $GLOBALS['eZDebugEnabled'] = false;
    }

    protected function tearDown(): void
    {
        eZDB::setInstance( $this->savedInstance );
        foreach ( $this->savedDebug as $key => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $value[0];
        }
    }

    private function db()
    {
        $db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => ':memory:',
                                      'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false,
                                      'slave_user' => '', 'slave_password' => '', 'slave_database' => '', 'socket' => false,
                                      'charset' => 'utf-8', 'is_internal_charset' => false, 'builtin_encoding' => false,
                                      'connect_retries' => 1, 'use_persistent_connection' => false, 'show_errors' => false ) );
        $this->assertTrue( $db->isConnected(), 'an SQLite database in memory' );
        // As Doctrine made it for the Platform: the id alone is the key
        $db->query( 'CREATE TABLE platform_attr ( id INTEGER PRIMARY KEY AUTOINCREMENT, version INTEGER DEFAULT 0 NOT NULL, data_text TEXT )' );
        // As the legacy kernel's schema has it
        $db->query( 'CREATE TABLE legacy_attr ( id INTEGER NOT NULL, version INTEGER DEFAULT 0 NOT NULL, data_text TEXT, PRIMARY KEY ( id, version ) )' );
        // A unique index on a column outside the keys
        $db->query( 'CREATE TABLE remote_attr ( id INTEGER NOT NULL, version INTEGER NOT NULL, remote_id TEXT, PRIMARY KEY ( id, version ) )' );
        $db->query( 'CREATE UNIQUE INDEX remote_attr_remote_id ON remote_attr ( remote_id )' );
        eZDB::setInstance( $db );
        return $db;
    }

    /**
     * @testdox insertOrReplaceIsSafe() is true only where every unique key holds all of the keys
     */
    public function testInsertOrReplaceIsSafe(): void
    {
        $db = $this->db();
        $this->assertSame( array( array( 'id' ) ), $db->uniqueKeyColumns( 'platform_attr' ) );
        $this->assertFalse( $db->insertOrReplaceIsSafe( 'platform_attr', array( 'id', 'version' ) ) );
        $this->assertTrue( $db->insertOrReplaceIsSafe( 'platform_attr', array( 'id' ) ) );
        $this->assertTrue( $db->insertOrReplaceIsSafe( 'legacy_attr', array( 'id', 'version' ) ) );
        $this->assertFalse( $db->insertOrReplaceIsSafe( 'remote_attr', array( 'id', 'version' ) ), 'remote_id alone is unique' );
    }

    /**
     * @testdox Storing version 2 of an attribute keeps version 1, or fails, but never deletes it
     */
    public function testStoringANewVersionKeepsThePublishedOne(): void
    {
        $db = $this->db();
        foreach ( array( 'legacy_attr' => 2, 'platform_attr' => 1 ) as $table => $rowsAfter )
        {
            eZSQLite3InsertOrReplaceTestAttribute::$table = $table;
            $published = new eZSQLite3InsertOrReplaceTestAttribute( array( 'id' => 7, 'version' => 1, 'data_text' => 'published' ) );
            $published->store();
            $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
            try
            {
                $draft = new eZSQLite3InsertOrReplaceTestAttribute( array( 'id' => 7, 'version' => 2, 'data_text' => 'draft' ) );
                $draft->store();
            }
            catch ( eZDBException $e )
            {
                // the Platform key cannot hold a second version: the insert fails instead of replacing
            }
            $db->setErrorHandling( eZDB::ERROR_HANDLING_STANDARD );
            $rows = $db->arrayQuery( "SELECT version, data_text FROM $table WHERE id = 7 ORDER BY version" );
            $this->assertCount( $rowsAfter, $rows, $table );
            $this->assertSame( 'published', $rows[0]['data_text'], "$table keeps the published version" );
        }
    }
}

/**
 * A persistent object keyed by (id, version), as eZContentObjectAttribute is.
 */
class eZSQLite3InsertOrReplaceTestAttribute extends eZPersistentObject
{
    public static $table = 'legacy_attr';

    public static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                                         'version' => array( 'name' => 'Version', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                                         'data_text' => array( 'name' => 'DataText', 'datatype' => 'text', 'default' => '', 'required' => false ) ),
                      'keys' => array( 'id', 'version' ),
                      'class_name' => 'eZSQLite3InsertOrReplaceTestAttribute',
                      'name' => self::$table );
    }
}
