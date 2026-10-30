<?php
/**
 * The SQLite driver counts its statements in the accumulator sqlite3_query, as the MySQL and PostgreSQL drivers do,
 * so the debug output and the debug bar's summary give the number of queries and their time also without
 * site.ini [DatabaseSettings] SQLOutput (lib/ezdb/classes/ezsqlite3db.php, query() and arrayQuery()):
 *
 *  QC-01 - Without SQLOutput every statement is counted, none is listed
 *  QC-02 - With SQLOutput, a statement faster than SlowSQLTimeout is counted too (the accumulator was left running)
 *  QC-03 - Without debug output nothing is counted
 *
 * No installation database: an SQLite database in memory.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezdb
 */

class eZSQLite3DBQueryCountTest extends PHPUnit\Framework\TestCase
{
    /** @var array the debug globals before the test */
    private $saved = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        foreach ( array( 'eZDebugGlobalInstance', 'eZDebugEnabled' ) as $key )
            $this->saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
    }

    protected function tearDown(): void
    {
        foreach ( $this->saved as $key => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $value[0];
        }
    }

    /**
     * A database in memory with a table of three rows, and a fresh debug instance.
     */
    private function db( $outputSQL, $debugEnabled = true, $slowSQLTimeout = 0 )
    {
        $GLOBALS['eZDebugGlobalInstance'] = new eZDebug();
        $GLOBALS['eZDebugEnabled'] = $debugEnabled;
        $db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => ':memory:',
                                      'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false,
                                      'slave_user' => '', 'slave_password' => '', 'slave_database' => '', 'socket' => false,
                                      'charset' => 'utf-8', 'is_internal_charset' => false, 'builtin_encoding' => false,
                                      'connect_retries' => 1, 'use_persistent_connection' => false, 'show_errors' => false ) );
        $db->OutputSQL = $outputSQL;
        $db->SlowSQLTimeout = $slowSQLTimeout;
        $this->assertTrue( $db->isConnected(), 'an SQLite database in memory' );
        $db->query( 'CREATE TABLE t ( id INTEGER PRIMARY KEY, name TEXT )' );
        foreach ( array( 'a', 'b', 'c' ) as $name )
            $db->query( "INSERT INTO t ( name ) VALUES ( '$name' )" );
        return $db;
    }

    /** @return int statements counted so far, 0 without the accumulator */
    private static function counted()
    {
        $accumulator = self::accumulator();
        return $accumulator === null ? 0 : $accumulator['count'];
    }

    /** @return array the accumulator sqlite3_query, or null */
    private static function accumulator()
    {
        $list = eZDebug::instance()->TimeAccumulatorList;
        return isset( $list['sqlite3_query'] ) ? $list['sqlite3_query'] : null;
    }

    /** @return int statements listed in the debug output */
    private static function listed()
    {
        $count = 0;
        foreach ( (array)eZDebug::instance()->DebugStrings as $entry )
        {
            if ( isset( $entry['Label'] ) && strpos( (string)$entry['Label'], 'SQLite3DB::query(' ) === 0 )
                $count++;
        }
        return $count;
    }

    public function testEveryStatementIsCountedWithoutSqlOutput()
    {
        $db = $this->db( false );
        $before = self::counted();
        $this->assertGreaterThanOrEqual( 4, $before, 'the table and its three rows' );
        $time = self::accumulator()['time'];
        $rows = $db->arrayQuery( 'SELECT name FROM t ORDER BY id' );
        $this->assertCount( 3, $rows );
        $db->arrayQuery( 'SELECT COUNT(*) AS c FROM t' );
        $db->query( "UPDATE t SET name = 'd' WHERE id = 1" );

        $this->assertSame( $before + 3, self::counted(), 'two selects and an update' );
        $this->assertGreaterThan( $time, self::accumulator()['time'] );
        $this->assertSame( 0, self::listed(), 'without SQLOutput no statement is listed' );
    }

    public function testAFastStatementIsCountedWithSqlOutput()
    {
        $db = $this->db( true, true, 1000 );
        $before = self::counted();
        $db->arrayQuery( 'SELECT name FROM t' );
        $db->query( "UPDATE t SET name = 'd' WHERE id = 1" );

        $this->assertSame( $before + 2, self::counted(), 'every statement, also those faster than SlowSQLTimeout' );
        $this->assertSame( 0, self::listed(), 'only statements slower than SlowSQLTimeout are listed' );
    }

    public function testNothingIsCountedWithoutDebugOutput()
    {
        $db = $this->db( false, false );
        $db->arrayQuery( 'SELECT name FROM t' );
        $this->assertNull( self::accumulator() );
    }
}
