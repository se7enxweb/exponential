<?php
/**
 * expSQLServerErrorMap (no server needed):
 *
 *  ER-01 - category(): deadlock 1205, lock timeout 1222, duplicate 2627/2601, FK 547, NOT NULL 515, truncation
 *          8152/2628, conversion, overflow, 8003 too many parameters, syntax, unknown objects, read-only 3906,
 *          lost connections (numbers and SQLSTATE 08xxx), Azure transient errors; SQLSTATE fallbacks
 *  ER-02 - isRetryable(): deadlocks, transient and snapshot conflicts only (never a lock timeout or a duplicate)
 *  ER-03 - endsTransaction(): with XACT_ABORT ON every runtime error, a deadlock always
 *  ER-04 - parseMessage(): the native number in PDO_ODBC, pdo_sqlsrv and pdo_dblib messages
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSQLServerTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('sqlserver')]
class expSQLServerErrorMapTest extends PHPUnit\Framework\TestCase
{
    public static function numbers()
    {
        $list = array();
        foreach ( expSQLServerErrorMap::NUMBERS as $number => $category )
            $list["$number $category"] = array( $number, null, $category );
        $list['sqlstate deadlock'] = array( null, '40001', 'deadlock' );
        $list['sqlstate integrity'] = array( 0, '23000', 'duplicate_key' );
        $list['sqlstate truncation'] = array( null, '22001', 'truncation' );
        $list['sqlstate cast'] = array( null, '22018', 'conversion' );
        $list['sqlstate link failure'] = array( null, '08S01', 'connection' );
        $list['sqlstate no connection'] = array( null, '08003', 'connection' );
        $list['sqlstate cannot connect'] = array( null, '08001', 'connection' );
        $list['sqlstate query timeout'] = array( null, 'HYT00', 'timeout' );
        $list['sqlstate connection timeout'] = array( null, 'HYT01', 'connection' );
        $list['sqlstate count field'] = array( null, '07002', 'too_many_parameters' );
        $list['unknown'] = array( 99999, '42000', 'other' );
        $list['nothing'] = array( null, null, 'other' );
        $list['number wins over state'] = array( 1205, '08S01', 'deadlock' );
        return $list;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('numbers')]
    public function testCategory( $number, $state, $category )
    {
        $this->assertSame( $category, expSQLServerErrorMap::category( $number, $state ) );
    }

    public static function retryable()
    {
        return array( array( 'deadlock', true ), array( 'transient', true ), array( 'snapshot_conflict', true ), array( 'lock_timeout', false ),
                      array( 'duplicate_key', false ), array( 'connection', false ), array( 'syntax', false ), array( 'other', false ),
                      array( 'timeout', false ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('retryable')]
    public function testIsRetryable( $category, $expected )
    {
        $this->assertSame( $expected, expSQLServerErrorMap::isRetryable( $category ) );
    }

    public static function endings()
    {
        return array(
            array( 'deadlock', true, true ), array( 'deadlock', false, true ), array( 'connection', false, true ),
            array( 'duplicate_key', true, true ), array( 'duplicate_key', false, false ), array( 'lock_timeout', true, true ),
            array( 'lock_timeout', false, false ), array( 'syntax', true, false ), array( 'unknown_object', true, false ),
            array( 'transient', false, true ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('endings')]
    public function testEndsTransaction( $category, $xactAbort, $expected )
    {
        $this->assertSame( $expected, expSQLServerErrorMap::endsTransaction( $category, $xactAbort ) );
    }

    public function testClientSideParameterRefusal()
    {
        $this->assertSame( 'too_many_parameters', expSQLServerErrorMap::category( null, 'IMSSP', 'Tried to bind parameter number 2101.  SQL Server supports a maximum of 2100 parameters.' ) );
        $this->assertSame( 'other', expSQLServerErrorMap::category( null, 'IMSSP', 'Something else' ) );
    }

    public function testConnectionLost()
    {
        $this->assertTrue( expSQLServerErrorMap::isConnectionLost( 'connection' ) );
        $this->assertFalse( expSQLServerErrorMap::isConnectionLost( 'deadlock' ) );
    }

    public static function messages()
    {
        return array(
            'pdo_odbc' => array( "SQLSTATE[23000]: Integrity constraint violation: 2627 [Microsoft][ODBC Driver 18 for SQL Server][SQL Server]Violation of PRIMARY KEY constraint (SQLExecute[2627] at ext/pdo_odbc/odbc_stmt.c:263)", 2627, '23000' ),
            'pdo_sqlsrv' => array( "SQLSTATE[40001]: [Microsoft][ODBC Driver 18 for SQL Server][SQL Server]Transaction (Process ID 57) was deadlocked", null, '40001' ),
            'pdo_sqlsrv with number' => array( 'SQLSTATE[23000]: Integrity constraint violation: 2601 Cannot insert duplicate key row', 2601, '23000' ),
            'pdo_dblib' => array( 'SQLSTATE[HY000]: General error: 1205 General SQL Server error: Check messages from the SQL Server [1205] (severity 13)', 1205, 'HY000' ),
            'odbc direct' => array( 'SQLSTATE[42S02]: Base table or view not found: 208 [Microsoft][ODBC Driver 18 for SQL Server][SQL Server]Invalid object name (SQLExecDirect[208] at x)', 208, '42S02' ),
            'nothing' => array( 'something failed', null, null ),
            'odbc driver error has no number' => array( 'SQLSTATE[22001]: String data, right truncated: 0 [Microsoft][ODBC Driver 18 for SQL Server]String data, right truncation (SQLExecute[0] at x)', null, '22001' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('messages')]
    public function testParseMessage( $message, $number, $state )
    {
        $this->assertSame( array( $number, $state ), expSQLServerErrorMap::parseMessage( $message ) );
    }
}
