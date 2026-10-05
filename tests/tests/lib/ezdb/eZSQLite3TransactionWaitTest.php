<?php
/**
 * The SQLite driver's write-lock wait stays below Velocity's request timeout.
 *
 * A transaction waits [DatabaseSettings] SQLiteTransactionWait seconds at its
 * start for the writers ahead of it. Velocity ends a request after its engine
 * setting Q.webserver.requestTimeout, 30 seconds unless changed, with a 504.
 * A wait at or above that lets the server kill a queued publish before the
 * driver can report "database is busy", so both the shipped setting and the
 * driver's fallback when the setting is missing must be below it.
 *
 * No database: reads settings/site.ini and the driver source.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */
class eZSQLite3TransactionWaitTest extends PHPUnit\Framework\TestCase
{
    /** Velocity's default Q.webserver.requestTimeout, in seconds. */
    const VELOCITY_REQUEST_TIMEOUT = 30;

    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    /**
     * @testdox settings/site.ini ships SQLiteTransactionWait below Velocity's 30 s request timeout
     */
    public function testShippedSettingIsBelowVelocityRequestTimeout(): void
    {
        $ini = file_get_contents( self::root() . '/settings/site.ini' );
        $this->assertNotFalse( $ini );
        $this->assertSame( 1, preg_match( '/^\[DatabaseSettings\]\s*$(.*?)^\[/ms', $ini, $section ),
            'settings/site.ini has a [DatabaseSettings] block' );
        $this->assertSame( 1, preg_match( '/^SQLiteTransactionWait=(\d+)\s*$/m', $section[1], $m ),
            '[DatabaseSettings] sets SQLiteTransactionWait' );
        $this->assertLessThan( self::VELOCITY_REQUEST_TIMEOUT, (int)$m[1] );
        $this->assertGreaterThanOrEqual( 1, (int)$m[1] );
    }

    /**
     * @testdox the driver's fallback, with the setting missing, is below Velocity's request timeout too
     */
    public function testDriverFallbackIsBelowVelocityRequestTimeout(): void
    {
        $source = file_get_contents( self::root() . '/lib/ezdb/classes/ezsqlite3db.php' );
        $this->assertNotFalse( $source );
        $this->assertSame( 1,
            preg_match( '/function transactionWaitMs\(\)\s*\{\s*\$seconds = (\d+);/', $source, $m ),
            'transactionWaitMs() starts from a literal default' );
        $this->assertLessThan( self::VELOCITY_REQUEST_TIMEOUT, (int)$m[1] );
    }
}
