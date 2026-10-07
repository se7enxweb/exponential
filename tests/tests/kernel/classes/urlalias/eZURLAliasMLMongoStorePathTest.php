<?php
/**
 * eZURLAliasML::storePath() on the MongoDB driver, with a recording stand-in for the driver (no database): storing
 * a node's name takes the MongoDB branch only, sends no SQL at all, raises no warning, gives a new entry the
 * always-available bit when the object is always available, and marks the action's other original entries
 * of the language as history with its own update. Guards the MongoDB path against the SQL history step, which
 * runs for every other driver.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZURLAliasMLMongoStorePathTestDB extends eZDBInterface
{
    /** @var array[] every call: array( method, arguments ) */
    public $calls = array();
    private $nextID = 900000;

    public function __construct()
    {
        // No connection; the parent constructor would read connection parameters
        $this->IsConnected = true;
    }

    private function record( $method, $args )
    {
        $this->calls[] = array( $method, $args );
    }

    function databaseName() { return 'mongo'; }
    function aggregate( $table, $pipeline = array() ) { $this->record( __FUNCTION__, func_get_args() ); return array(); }
    function insert( $table, $doc ) { $this->record( __FUNCTION__, func_get_args() ); return true; }
    function upsert( $table, $filter, $doc ) { $this->record( __FUNCTION__, func_get_args() ); return true; }
    function mongoUpdateOne( $table, $filter, $update ) { $this->record( __FUNCTION__, func_get_args() ); return true; }
    function mongoUpdateMany( $table, $filter, $update ) { $this->record( __FUNCTION__, func_get_args() ); return true; }
    function deleteWhere( $table, $filter ) { $this->record( __FUNCTION__, func_get_args() ); return true; }
    function nextAtomicID( $counterName, $seedTable = null, $seedColumn = 'id' ) { $this->record( __FUNCTION__, func_get_args() ); return ++$this->nextID; }
    function nextSeqID( $table, $column ) { $this->record( __FUNCTION__, func_get_args() ); return ++$this->nextID; }
    function escapeString( $str ) { return addslashes( (string)$str ); }
    function arrayQuery( $sql, $params = array(), $server = false ) { $this->record( __FUNCTION__, func_get_args() ); return array(); }
    function query( $sql, $server = false ) { $this->record( __FUNCTION__, func_get_args() ); return true; }
    function bitAnd( $arg1, $arg2 ) { $this->record( __FUNCTION__, func_get_args() ); return "($arg1 & $arg2)"; }
    function bitOr( $arg1, $arg2 ) { $this->record( __FUNCTION__, func_get_args() ); return "($arg1 | $arg2)"; }
    function begin() { return true; }
    function commit() { return true; }
    function rollback() { return true; }
    function transactionCounter() { return 0; }

    public function methods()
    {
        return array_column( $this->calls, 0 );
    }
}

class eZURLAliasMLMongoStorePathTest extends PHPUnit\Framework\TestCase
{
    private $previousDB;
    private $charset;
    private $db;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->charset = $GLOBALS['eZTextCodecInternalCharsetReal'] ?? null;
        $GLOBALS['eZTextCodecInternalCharsetReal'] = 'utf-8';
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'WordSeparator', 'dash' );
        ezpINIHelper::setINISetting( 'site.ini', 'URLTranslator', 'TransformationGroup', 'urlalias' );
        unset( $GLOBALS['eZCharTransform_wordSeparator'] );
        $this->previousDB = $GLOBALS['eZDBGlobalInstance'] ?? null;
        $this->db = new eZURLAliasMLMongoStorePathTestDB();
        eZDB::setInstance( $this->db );
    }

    protected function tearDown(): void
    {
        $GLOBALS['eZDBGlobalInstance'] = $this->previousDB;
        $GLOBALS['eZTextCodecInternalCharsetReal'] = $this->charset;
        ezpINIHelper::restoreINISettings();
        unset( $GLOBALS['eZCharTransform_wordSeparator'] );
        parent::tearDown();
    }

    /**
     * Runs storePath() and returns its result; fails on any PHP notice or warning it raises.
     */
    private function storePath( $alwaysAvailable )
    {
        $language = new eZContentLanguage( array( 'id' => 2, 'locale' => 'eng-GB', 'name' => 'English', 'disabled' => 0 ) );
        $raised = array();
        set_error_handler( function ( $no, $message, $file, $line ) use ( &$raised )
        {
            $raised[] = "$message ($file:$line)";
            return true;
        } );
        try
        {
            $result = eZURLAliasML::storePath( 'Mongo Name', 'eznode:900001', $language, false, $alwaysAvailable );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $raised, 'storePath() raises no notice or warning' );
        return $result;
    }

    private function insertedLangMask()
    {
        $inserts = array_values( array_filter( $this->db->calls, function ( $c ) { return $c[0] === 'insert'; } ) );
        $this->assertCount( 1, $inserts );
        return $inserts[0][1][1]['lang_mask'];
    }

    public function testAnAlwaysAvailableEntryGetsTheAlwaysAvailableBit()
    {
        $this->assertTrue( $this->storePath( true )['status'] );
        $this->assertSame( 2 | 1, $this->insertedLangMask() );
    }

    public function testAnEntryThatIsNotAlwaysAvailableHasOnlyItsLanguageBit()
    {
        $this->assertTrue( $this->storePath( false )['status'] );
        $this->assertSame( 2, $this->insertedLangMask() );
    }

    public function testStorePathUsesOnlyTheMongoDBCalls()
    {
        $result = $this->storePath( false );

        $this->assertTrue( $result['status'] );
        $this->assertSame( 'mongo-name', $result['path'] );

        $methods = $this->db->methods();
        foreach ( array( 'arrayQuery', 'query', 'bitAnd', 'bitOr' ) as $sql )
            $this->assertNotContains( $sql, $methods, "no $sql() on MongoDB" );

        $inserts = array_values( array_filter( $this->db->calls, function ( $c ) { return $c[0] === 'insert'; } ) );
        $this->assertCount( 1, $inserts );
        $this->assertSame( 'ezurlalias_ml', $inserts[0][1][0] );
        $this->assertSame( 'mongo-name', $inserts[0][1][1]['text'] );
        $this->assertSame( 'eznode:900001', $inserts[0][1][1]['action'] );

        // The history step: the action's other original system entries of the language become history
        $history = array_values( array_filter( $this->db->calls, function ( $c )
        {
            return $c[0] === 'mongoUpdateMany' && $c[1][2] === array( '$set' => array( 'is_original' => 0 ) );
        } ) );
        $this->assertCount( 1, $history );
        $filter = $history[0][1][1];
        $this->assertSame( 'eznode:900001', $filter['action'] );
        $this->assertSame( array( '$bitsAnySet' => 2 ), $filter['lang_mask'] );
        $this->assertSame( 1, $filter['is_original'] );
        $this->assertSame( 0, $filter['is_alias'] );
    }
}
