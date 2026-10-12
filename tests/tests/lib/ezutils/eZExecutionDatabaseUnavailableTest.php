<?php
/**
 * eZExecution::isDatabaseUnavailable(): which exceptions show the "database unavailable" page (HTTP 503) instead
 * of an error page (500).
 *
 * The database drivers' eZDBException carries the driver's error number (getCode()); with the exception error
 * handling MySQL throws it instead of PHP's mysqli_sql_exception. A connection that went away or cannot be made is
 * recognised from that number on every driver:
 *   - MySQL/MariaDB 2002, 2003, 2006, 2013, 1040, 1045 ...; mysqli_sql_exception with the same numbers as before
 *   - PostgreSQL: SQLSTATE class 08, 57P01 to 57P03, 53300
 *   - Oracle: ORA-03113, ORA-03114, ORA-12541 ... (only with an Oracle message: the numbers overlap MySQL's)
 *   - SQLite: 14 (cannot open), 26 (not a database)
 * and an ordinary failed statement (duplicate key, missing table) is not.
 *
 * No database, no kernel bootstrap.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZExecutionDatabaseUnavailableTest extends PHPUnit\Framework\TestCase
{
    private static function exception( $message, $code )
    {
        return new eZDBException( $message, $code );
    }

    public static function unavailableCases()
    {
        return array(
            'mysql socket'          => array( 'Can\'t connect to local server through socket', 2002 ),
            'mysql tcp'             => array( 'Can\'t connect to MySQL server', 2003 ),
            'mysql gone away'       => array( 'MySQL server has gone away', 2006 ),
            'mysql lost'            => array( 'Lost connection to MySQL server during query', 2013 ),
            'mysql too many'        => array( 'Too many connections', 1040 ),
            'pgsql connection'      => array( 'ERROR:  terminating connection', '08006' ),
            'pgsql cannot connect'  => array( 'could not connect', '08001' ),
            'pgsql admin shutdown'  => array( 'FATAL:  terminating connection due to administrator command', '57P01' ),
            'pgsql starting'        => array( 'FATAL:  the database system is starting up', '57P03' ),
            'pgsql too many'        => array( 'FATAL:  sorry, too many clients already', '53300' ),
            'oracle end of file'    => array( 'ORA-03113: end-of-file on communication channel', 3113 ),
            'oracle not connected'  => array( 'ORA-03114: not connected to ORACLE', 3114 ),
            'oracle no listener'    => array( 'ORA-12541: TNS:no listener', 12541 ),
            'oracle not available'  => array( 'ORA-01034: ORACLE not available', 1034 ),
            'sqlite cannot open'    => array( 'unable to open database file', 14 ),
            'sqlite not a database' => array( 'file is not a database', 26 ),
        );
    }

    public static function availableCases()
    {
        return array(
            'mysql duplicate key'   => array( 'Duplicate entry \'1\' for key \'PRIMARY\'', 1062 ),
            'mysql missing table'   => array( 'Table \'x.y\' doesn\'t exist', 1146 ),
            'pgsql duplicate key'   => array( 'ERROR:  duplicate key value violates unique constraint', '23505' ),
            'pgsql missing table'   => array( 'ERROR:  relation "y" does not exist', '42P01' ),
            'oracle unique'         => array( 'ORA-00001: unique constraint violated', 1 ),
            'oracle missing table'  => array( 'ORA-00942: table or view does not exist', 942 ),
            'oracle number of mysql'=> array( 'ORA-02006: invalid packed decimal format string', 2006 ),
            'sqlite constraint'     => array( 'UNIQUE constraint failed: t.id', 19 ),
            'sqlite missing table'  => array( 'no such table: y', 1 ),
            'no code'               => array( 'something', 0 ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('unavailableCases')]
    public function testConnectionFailuresAreUnavailable( $message, $code ): void
    {
        $this->assertTrue( eZExecution::isDatabaseUnavailable( self::exception( $message, $code ) ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('availableCases')]
    public function testStatementFailuresAreNot( $message, $code ): void
    {
        $this->assertFalse( eZExecution::isDatabaseUnavailable( self::exception( $message, $code ) ) );
    }

    public function testOtherExceptions(): void
    {
        $this->assertTrue( eZExecution::isDatabaseUnavailable( new eZDBNoConnectionException( 'localhost', 'refused', 2002 ) ) );
        $this->assertFalse( eZExecution::isDatabaseUnavailable( new RuntimeException( 'gone', 2006 ) ) );
        if ( class_exists( 'mysqli_sql_exception' ) )
        {
            $this->assertTrue( eZExecution::isDatabaseUnavailable( new mysqli_sql_exception( 'MySQL server has gone away', 2006 ) ) );
            $this->assertFalse( eZExecution::isDatabaseUnavailable( new mysqli_sql_exception( 'Duplicate entry', 1062 ) ) );
        }
    }
}
