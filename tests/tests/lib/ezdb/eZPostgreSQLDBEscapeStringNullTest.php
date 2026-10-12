<?php
/**
 * eZPostgreSQLDB::escapeString() with null, the case LoveStack (mugoweb/ezpublish-legacy) commit 1bd250d4c6
 * guarded in eZPersistentObject: an attribute without a value reaches the driver as null, and PHP 8.1 deprecates
 * passing null to str_replace() and pg_escape_string(). The MySQLi, SQLite3 and MongoDB drivers give '' for it.
 *
 * No PostgreSQL server is needed: the driver is created without connecting, which is all the null case needs.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdb
 * @group php8
 */

class eZPostgreSQLDBEscapeStringNullTest extends PHPUnit\Framework\TestCase
{
    private $messages = array();

    protected function setUp(): void
    {
        if ( !function_exists( 'pg_escape_string' ) )
            $this->markTestSkipped( 'The pgsql extension is not loaded' );
        $this->messages = array();
        set_error_handler( function ( $errno, $errstr ) {
            $this->messages[] = $errstr;
            return true;
        } );
    }

    protected function tearDown(): void
    {
        restore_error_handler();
    }

    private function driverWithoutConnection()
    {
        $class = new ReflectionClass( 'eZPostgreSQLDB' );
        $db = $class->newInstanceWithoutConstructor();
        $db->DBConnection = false;
        $db->IsConnected = false;
        return $db;
    }

    public function testNullIsAnEmptyStringWithoutDeprecation()
    {
        $this->assertSame( '', $this->driverWithoutConnection()->escapeString( null ) );
        $this->assertSame( array(), $this->messages );
    }
}
