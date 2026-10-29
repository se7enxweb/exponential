<?php
/**
 * The SQLite driver answers MySQL's MOD() function.
 *
 * eZ Tags reads the main language bit of a language mask with MOD( language_mask, 2 ). SQLite
 * builds without the math functions (3.34 among them) have no MOD, so the statement failed with
 * "no such function: MOD" and took the surrounding transaction with it: editing content with a
 * tags field in the legacy admin ended in a "database transaction failed" page. The driver now
 * registers MOD with MySQL's answers: NULL for a NULL argument or a zero divisor, the sign of the
 * dividend, a fraction for decimal arguments.
 *
 * Database: SQLite in memory.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */
class eZSQLite3ModFunctionTest extends PHPUnit\Framework\TestCase
{
    /** @var array the debug globals before the test */
    private $savedDebug = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        if ( !method_exists( 'eZDebug', 'createAccumulatorGroup' ) )
            $this->markTestSkipped( 'eZDebug is a stand-in declared by another test of this run' );
        foreach ( array( 'eZDebugGlobalInstance', 'eZDebugEnabled' ) as $key )
            $this->savedDebug[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $GLOBALS['eZDebugGlobalInstance'] = new eZDebug();
        $GLOBALS['eZDebugEnabled'] = false;
    }

    protected function tearDown(): void
    {
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
        return $db;
    }

    private function value( eZSQLite3DB $db, $expression )
    {
        $rows = $db->arrayQuery( "SELECT $expression AS v" );
        $this->assertIsArray( $rows, "SELECT $expression runs" );
        return $rows[0]['v'];
    }

    /**
     * @testdox MOD() answers as MySQL does for integers, decimals, NULL and a zero divisor
     */
    public function testModAnswersAsMySQL(): void
    {
        $db = $this->db();
        $this->assertSame( 1, (int)$this->value( $db, 'MOD( 5, 2 )' ) );
        $this->assertSame( 0, (int)$this->value( $db, 'mod( 6, 3 )' ), 'the name is not case sensitive' );
        $this->assertSame( -1, (int)$this->value( $db, 'MOD( -5, 2 )' ), 'the sign follows the dividend' );
        $this->assertEqualsWithDelta( 1.5, (float)$this->value( $db, 'MOD( 5.5, 2 )' ), 0.0001 );
        $this->assertNull( $this->value( $db, 'MOD( 5, 0 )' ) );
        $this->assertNull( $this->value( $db, 'MOD( NULL, 2 )' ) );
    }

    /**
     * @testdox The eZ Tags language mask query runs: the main language of each tag
     */
    public function testTagsLanguageMaskQuery(): void
    {
        $db = $this->db();
        $db->query( 'CREATE TABLE eztags ( id INTEGER PRIMARY KEY, main_language_id INTEGER NOT NULL, language_mask INTEGER NOT NULL )' );
        $db->query( 'CREATE TABLE eztags_keyword ( keyword_id INTEGER NOT NULL, language_id INTEGER NOT NULL, keyword TEXT )' );
        // tag 1: main language 2, always available (bit 0); tag 2: main language 4, not always available
        $db->query( 'INSERT INTO eztags VALUES ( 1, 2, 7 ), ( 2, 4, 4 )' );
        $db->query( "INSERT INTO eztags_keyword VALUES ( 1, 3, 'always' ), ( 1, 4, 'other' ), ( 2, 4, 'main' )" );
        $rows = $db->arrayQuery( 'SELECT eztags.id, eztags_keyword.keyword FROM eztags, eztags_keyword'
                               . ' WHERE eztags.id = eztags_keyword.keyword_id'
                               . ' AND eztags.main_language_id + MOD( eztags.language_mask, 2 ) = eztags_keyword.language_id'
                               . ' ORDER BY eztags.id' );
        $this->assertSame( array( 'always', 'main' ), array_column( $rows, 'keyword' ) );
    }
}
