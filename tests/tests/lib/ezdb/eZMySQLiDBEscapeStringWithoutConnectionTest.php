<?php
/**
 * eZMySQLiDB::escapeString() before a connection is made, and the eZCodePageCodec constructor: both called a
 * function with too few arguments, which threw ArgumentCountError on PHP 8 when the line was reached
 * (mysqli_real_escape_string() without a link, eZTextCodec::__construct() with one of six arguments).
 *
 * No MySQL server is needed: the driver is created without connecting.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezdb
 * @group php8
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('php8')]
class eZMySQLiDBEscapeStringWithoutConnectionTest extends PHPUnit\Framework\TestCase
{
    private function driverWithoutConnection()
    {
        $class = new ReflectionClass( 'eZMySQLiDB' );
        $db = $class->newInstanceWithoutConstructor();
        $db->DBConnection = false;
        $db->DBWriteConnection = false;
        $db->IsConnected = false;
        return $db;
    }

    public function testEscapesWithoutAConnection()
    {
        if ( !function_exists( 'mysqli_real_escape_string' ) )
            $this->markTestSkipped( 'The mysqli extension is not loaded' );
        $db = $this->driverWithoutConnection();
        $this->assertSame( "it\\'s", $db->escapeString( "it's" ) );
        $this->assertSame( 'a\\\\b \\"c\\" \\0 \\n \\r \\Z', $db->escapeString( "a\\b \"c\" \0 \n \r \x1a" ) );
        $this->assertSame( '', $db->escapeString( null ) );
        $this->assertSame( '12', $db->escapeString( 12 ) );
    }

    public function testFallbackLeavesMultibyteTextAlone()
    {
        $this->assertSame( 'Grüße – ok', eZMySQLiDB::escapeStringWithoutConnection( 'Grüße – ok' ) );
    }

    public function testCodePageCodecConstructs()
    {
        $codec = new eZCodePageCodec( 'iso-8859-1' );
        $this->assertFalse( $codec->isValid() );
        $this->assertSame( '', $codec->toUnicode( 'abc' ) );
    }
}
