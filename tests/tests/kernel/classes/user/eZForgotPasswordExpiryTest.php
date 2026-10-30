<?php
/**
 * How long a "Forgot your password?" link works (kernel/classes/datatypes/ezuser/ezforgotpassword.php, used by
 * kernel/private/classes/views/user/forgotpassword.php and the web services).
 *
 * DB-less: keys are objects made in memory and the settings are a site.ini of the test's own; nothing is stored.
 *
 *  FPK-01  ForgotPasswordKeyLifetime: 3600 when not set, empty, negative or not a number; 0 means never
 *  FPK-02  A key works until time + lifetime and not at or after it; with 0 it never expires
 *  FPK-03  isExpired() and expiresAt() of a key object
 *  FPK-04  The shipped site.ini sets the lifetime to one hour and the check on
 *  FPK-05  The page and the mail of the standard design say that a link expired and how long it works
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expWeakPasswordTestINI.php';

class eZForgotPasswordExpiryTest extends PHPUnit\Framework\TestCase
{
    private static function ini( $value = null )
    {
        return new expWeakPasswordTestINI( array( 'UserSettings' => $value === null ? array() : array( 'ForgotPasswordKeyLifetime' => $value ) ) );
    }

    public function testLifetimeSetting()
    {
        $this->assertSame( 3600, eZForgotPassword::DEFAULT_LIFETIME );
        $this->assertSame( 3600, eZForgotPassword::lifetime( self::ini() ), 'not set' );
        $this->assertSame( 3600, eZForgotPassword::lifetime( self::ini( '' ) ) );
        $this->assertSame( 3600, eZForgotPassword::lifetime( self::ini( '-5' ) ) );
        $this->assertSame( 3600, eZForgotPassword::lifetime( self::ini( 'soon' ) ) );
        $this->assertSame( 900, eZForgotPassword::lifetime( self::ini( '900' ) ) );
        $this->assertSame( 0, eZForgotPassword::lifetime( self::ini( '0' ) ), '0: until it is used, as before' );
    }

    public function testExpiryBoundary()
    {
        $made = 1700000000;
        $this->assertFalse( eZForgotPassword::isExpiredAt( $made, 3600, $made ) );
        $this->assertFalse( eZForgotPassword::isExpiredAt( $made, 3600, $made + 3599 ) );
        $this->assertTrue( eZForgotPassword::isExpiredAt( $made, 3600, $made + 3600 ) );
        $this->assertTrue( eZForgotPassword::isExpiredAt( $made, 3600, $made + 86400 * 365 ) );
        $this->assertFalse( eZForgotPassword::isExpiredAt( $made, 0, $made + 86400 * 365 ), 'never with 0' );
        $this->assertFalse( eZForgotPassword::isExpiredAt( $made, -1, $made + 86400 ), 'never with a negative lifetime' );
        $this->assertTrue( eZForgotPassword::isExpiredAt( 0, 3600, $made ), 'a row without a time is old' );
    }

    public function testKeyObject()
    {
        $now = time();
        $fresh = eZForgotPassword::createNew( 1, str_repeat( 'a', 32 ), $now - 10 );
        $old = eZForgotPassword::createNew( 1, str_repeat( 'b', 32 ), $now - 7200 );
        $this->assertFalse( $fresh->isExpired( $now, 3600 ) );
        $this->assertTrue( $old->isExpired( $now, 3600 ) );
        $this->assertFalse( $old->isExpired( $now, 0 ) );
        $this->assertTrue( $old->isExpired( $now, 60 ) );
        $this->assertSame( $now - 10 + 3600, $fresh->expiresAt( 3600 ) );
        $this->assertNull( $fresh->expiresAt( 0 ) );
    }

    public function testShippedSettings()
    {
        $text = file_get_contents( dirname( __DIR__, 5 ) . '/settings/site.ini' );
        $this->assertMatchesRegularExpression( '/^ForgotPasswordKeyLifetime=3600$/m', $text );
        $this->assertMatchesRegularExpression( '/^WeakPasswordCheck=enabled$/m', $text );
    }

    public function testTemplatesSayTheLinkExpired()
    {
        $root = dirname( __DIR__, 5 ) . '/design/standard/templates/user/';
        $page = file_get_contents( $root . 'forgotpassword.tpl' );
        $this->assertStringContainsString( '$key_expired', $page );
        $this->assertStringContainsString( 'This link has expired.', $page );
        $this->assertStringContainsString( '$key_lifetime_minutes', file_get_contents( $root . 'forgotpasswordmail.tpl' ) );
    }
}
