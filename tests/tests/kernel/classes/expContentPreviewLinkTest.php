<?php
/**
 * Share links to the preview of a draft (expContentPreviewLink): the signature, the token's form, the lifetime, the
 * state of a link and when its row goes, the stored form, the view's settings and its place in the omit list.
 *
 * No database, no files.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expContentPreviewLinkTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expContentPreviewLinkTest extends PHPUnit\Framework\TestCase
{
    private static function link( array $change = array() )
    {
        return $change + array( 'id' => str_repeat( 'ab', 12 ), 'object' => 58, 'version' => 4, 'language' => 'eng-GB',
                                'expires' => 2000000000, 'created' => 1999990000, 'creator' => 14, 'note' => '',
                                'revoked' => 0, 'revoker' => 0, 'opened' => 0, 'lastOpened' => 0 );
    }

    public function testTokenIsTheIdAndASignatureThatParsesBack()
    {
        $token = expContentPreviewLink::token( 'key', self::link() );
        $this->assertMatchesRegularExpression( expContentPreviewLink::TOKEN_PATTERN, $token );
        $parts = expContentPreviewLink::parse( $token );
        $this->assertSame( str_repeat( 'ab', 12 ), $parts['id'] );
        $this->assertTrue( expContentPreviewLink::signatureMatches( 'key', self::link(), $parts['signature'] ) );
    }

    public function testTheSignatureCoversObjectVersionLanguageAndExpiry()
    {
        $signature = expContentPreviewLink::sign( 'key', self::link() );
        foreach ( array( array( 'object' => 59 ), array( 'version' => 5 ), array( 'language' => 'ger-DE' ), array( 'expires' => 2000000001 ),
                         array( 'id' => str_repeat( 'ac', 12 ) ) ) as $change )
            $this->assertFalse( expContentPreviewLink::signatureMatches( 'key', self::link( $change ), $signature ), json_encode( $change ) );
        $this->assertFalse( expContentPreviewLink::signatureMatches( 'other key', self::link(), $signature ), 'another key' );
        $this->assertFalse( expContentPreviewLink::signatureMatches( '', self::link(), $signature ), 'no key' );
    }

    public function testParseRefusesAnythingElse()
    {
        $good = str_repeat( 'ab', 12 ) . '.' . str_repeat( 'A', 43 );
        $this->assertNotNull( expContentPreviewLink::parse( $good ) );
        foreach ( array( '', 'x', str_repeat( 'AB', 12 ) . '.' . str_repeat( 'A', 43 ), str_repeat( 'ab', 12 ) . '.' . str_repeat( 'A', 42 ),
                         $good . '/', '../' . $good, str_repeat( 'ab', 12 ) . '.' . str_repeat( '+', 43 ), null, 42, array() ) as $bad )
            $this->assertNull( expContentPreviewLink::parse( $bad ), var_export( $bad, true ) );
    }

    public function testLifetimeIsBetweenFiveMinutesAndTheLongest()
    {
        $settings = array( 'defaultLifetime' => 86400, 'maxLifetime' => 604800 );
        $this->assertSame( 86400, expContentPreviewLink::lifetime( null, $settings ) );
        $this->assertSame( 3600, expContentPreviewLink::lifetime( 3600, $settings ) );
        $this->assertSame( 604800, expContentPreviewLink::lifetime( 99999999, $settings ) );
        $this->assertSame( 300, expContentPreviewLink::lifetime( 1, $settings ) );
        $this->assertSame( 300, expContentPreviewLink::lifetime( -5, $settings ) );
        $this->assertSame( 300, expContentPreviewLink::lifetime( null, array( 'defaultLifetime' => 86400, 'maxLifetime' => 0 ) ) );
    }

    public function testStateAndWhenAnEndedLinkGoes()
    {
        $now = 2000000000;
        $this->assertSame( 'active', expContentPreviewLink::state( self::link( array( 'expires' => $now + 1 ) ), $now ) );
        $this->assertSame( 'expired', expContentPreviewLink::state( self::link( array( 'expires' => $now ) ), $now ) );
        $this->assertSame( 'revoked', expContentPreviewLink::state( self::link( array( 'expires' => $now + 100, 'revoked' => $now - 5 ) ), $now ) );
        $this->assertFalse( expContentPreviewLink::prunable( self::link( array( 'expires' => $now + 1 ) ), $now ), 'active stays' );
        $this->assertFalse( expContentPreviewLink::prunable( self::link( array( 'expires' => $now - 3600 ) ), $now ), 'ended an hour ago stays' );
        $this->assertTrue( expContentPreviewLink::prunable( self::link( array( 'expires' => $now - 604801 ) ), $now ) );
        $this->assertTrue( expContentPreviewLink::prunable( self::link( array( 'expires' => $now + 99, 'revoked' => $now - 604801 ) ), $now ) );
    }

    public function testDecodeReadsTheStoredFormAndRefusesOthers()
    {
        $link = expContentPreviewLink::decode( json_encode( self::link( array( 'opened' => 3 ) ) ) );
        $this->assertSame( 58, $link['object'] );
        $this->assertSame( 3, $link['opened'] );
        $this->assertNull( expContentPreviewLink::decode( '' ) );
        $this->assertNull( expContentPreviewLink::decode( '{"id":"zz","object":1,"version":1,"language":"x","expires":1}' ) );
        $this->assertNull( expContentPreviewLink::decode( json_encode( array( 'id' => str_repeat( 'ab', 12 ) ) ) ) );
        $this->assertLessThanOrEqual( 60, strlen( expContentPreviewLink::ROW_PREFIX . str_repeat( 'ab', 12 ) ), 'ezsite_data.name is 60 characters' );
    }

    public function testTheViewNeedsNoLoginAndItsSettingsAreShipped()
    {
        $root = dirname( __DIR__, 4 ) . '/';
        $site = file_get_contents( $root . 'settings/site.ini' );
        $this->assertStringContainsString( "\nPolicyOmitList[]=content/sharedpreview\n", $site );
        $content = file_get_contents( $root . 'settings/content.ini' );
        foreach ( array( 'Enabled=true', 'DefaultLifetime=86400', 'MaxLifetime=604800', 'MaxActivePerVersion=20' ) as $line )
            $this->assertStringContainsString( "\n" . $line . "\n", $content );
        // module.php reads the object state groups from the database, so its text is checked
        $module = file_get_contents( $root . 'kernel/content/module.php' );
        $this->assertMatchesRegularExpression( '/\$ViewList\[\'sharedpreview\'\] = array\([^;]*\'script\' => \'sharedpreview\.php\',[^;]*\'params\' => array\( \'Token\' \) \);/s', $module );
        $view = file_get_contents( $root . 'kernel/private/classes/views/content/sharedpreview.php' );
        foreach ( array( 'keepOutOfSharedCaches', 'X-Robots-Tag: noindex, nofollow', 'Referrer-Policy: no-referrer' ) as $needle )
            $this->assertStringContainsString( $needle, $view );
    }
}
