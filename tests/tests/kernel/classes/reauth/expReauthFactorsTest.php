<?php
/**
 * "Verify it's you" (doc/eng-us/guides/verify-its-you.md): which ways a user confirms with, [ReauthSettings] Factors[],
 * without a database.
 *
 *  RF-01 the shipped order (read from settings/site.ini): password+passkey, password+code, password, passkey
 *  RF-02 parseFactors(): case and spaces do not matter, unknown ways and duplicates are left out, an empty or unusable
 *        list gives the shipped order
 *  RF-03 combinations(): the first combination the user has, and the later ones of as many ways; never a weaker one
 *  RF-04 an account with a password and a passkey is asked for both; with the app as well, the app is offered beside
 *        the passkey; with only a password, the password; without a password, a passkey; with nothing, nothing
 *  RF-05 chosenCombination(): a form names one of those offered or gets the first; a weaker one it names is refused
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expReauthFactorsTest extends PHPUnit\Framework\TestCase
{
    /** @return string[] the Factors[] lines of the shipped settings/site.ini */
    private static function shippedLines()
    {
        $ini = file_get_contents( dirname( __DIR__, 5 ) . '/settings/site.ini' );
        $lines = array();
        $inBlock = false;
        foreach ( explode( "\n", $ini ) as $line )
        {
            $line = trim( $line );
            if ( preg_match( '/^\[(.+)\]$/', $line, $m ) )
            {
                $inBlock = $m[1] === 'ReauthSettings';
                continue;
            }
            if ( $inBlock && preg_match( '/^Factors\[\]=(.+)$/', $line, $m ) )
                $lines[] = $m[1];
        }
        return $lines;
    }

    public function testShippedOrder()
    {
        $this->assertSame( array( 'password+passkey', 'password+code', 'password', 'passkey' ), self::shippedLines() );
        $this->assertSame( expReauth::parseFactors( array() ), expReauth::parseFactors( self::shippedLines() ) );
    }

    public function testParseFactors()
    {
        $this->assertSame( array( array( 'password', 'code' ), array( 'passkey' ) ),
                           expReauth::parseFactors( array( ' Password + CODE ', 'passkey', 'passkey', 'password+sms', '' ) ) );
        $this->assertSame( array( array( 'passkey' ) ), expReauth::parseFactors( array( 'passkey+passkey' ) ) );
        $default = expReauth::parseFactors( array( 'fingerprint', '+' ) );
        $this->assertSame( array( 'password', 'passkey' ), $default[0] );
        $this->assertCount( 4, $default );
    }

    public function testCombinationsNeverWeaker()
    {
        $f = expReauth::parseFactors( array() );
        $this->assertSame( array( array( 'password', 'passkey' ) ), expReauth::combinations( array( 'password', 'passkey' ), $f ) );
        $this->assertSame( array( array( 'password', 'passkey' ), array( 'password', 'code' ) ),
                           expReauth::combinations( array( 'password', 'passkey', 'code' ), $f ) );
        $this->assertSame( array( array( 'password', 'code' ) ), expReauth::combinations( array( 'password', 'code' ), $f ) );
        $this->assertSame( array( array( 'password' ) ), expReauth::combinations( array( 'password' ), $f ) );
        $this->assertSame( array( array( 'passkey' ) ), expReauth::combinations( array( 'passkey' ), $f ) );
        $this->assertSame( array(), expReauth::combinations( array( 'code' ), $f ) );
        $this->assertSame( array(), expReauth::combinations( array(), $f ) );
    }

    public function testChosenCombination()
    {
        $offered = array( array( 'password', 'passkey' ), array( 'password', 'code' ) );
        $this->assertSame( array( 'password', 'passkey' ), expReauth::chosenCombination( $offered, null ) );
        $this->assertSame( array( 'password', 'code' ), expReauth::chosenCombination( $offered, 'password+code' ) );
        // a weaker way than those offered is not taken: the first one is asked for
        $this->assertSame( array( 'password', 'passkey' ), expReauth::chosenCombination( $offered, 'password' ) );
        $this->assertSame( array( 'password', 'passkey' ), expReauth::chosenCombination( $offered, array( 'password' ) ) );
        $this->assertNull( expReauth::chosenCombination( array(), 'password' ) );
    }
}
