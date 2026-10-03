<?php
/**
 * The declarations of every commerce and community service: summary, access, write, args, returns, a method of the
 * same name; the class registered in ezjscore.ini; every write refuses a request that is not a POST with the token.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCommerceTestCase.php';

class expCommerceDeclarationsTest extends expCommerceTestCase
{
    public static $classes = array( 'product' => 'expProductServices', 'basket' => 'expBasketServices', 'order' => 'expOrderServices', 'vat' => 'expVatServices',
        'currency' => 'expCurrencyServices', 'discount' => 'expDiscountServices', 'wishlist' => 'expWishlistServices', 'shipping' => 'expShippingServices',
        'payment' => 'expPaymentServices', 'infocollection' => 'expInfoCollectionServices', 'poll' => 'expPollServices', 'forum' => 'expForumServices',
        'topic' => 'expTopicServices', 'reply' => 'expReplyServices', 'comment' => 'expCommentServices', 'feed' => 'expFeedServices' );

    public function testEveryServiceIsDeclaredCompletely()
    {
        foreach ( self::$classes as $domain => $class )
            foreach ( $class::$services as $method => $d )
            {
                $this->assertNotEmpty( $d['summary'], "$class::$method summary" );
                $this->assertTrue( $d['access'] === 'public' || $d['access'] === 'user' || ( is_array( $d['access'] ) && count( $d['access'] ) === 2 ), "$class::$method access" );
                $this->assertIsBool( $d['write'], "$class::$method write" );
                $this->assertIsArray( $d['args'], "$class::$method args" );
                $this->assertNotEmpty( $d['returns'], "$class::$method returns" );
                $this->assertTrue( method_exists( $class, $method ), "$class::$method exists" );
            }
    }

    public function testThereAreAtLeast180Services()
    {
        $n = 0;
        foreach ( self::$classes as $class )
            $n += count( $class::$services );
        $this->assertGreaterThanOrEqual( 180, $n );
    }

    public function testEveryClassIsRegisteredInIni()
    {
        $ini = eZINI::instance( 'ezjscore.ini' );
        foreach ( self::$classes as $domain => $class )
        {
            $this->assertTrue( $ini->hasGroup( 'ezjscServer_exp' . $domain ), "block for $domain" );
            $this->assertSame( $class, $ini->variable( 'ezjscServer_exp' . $domain, 'Class' ) );
            $this->assertTrue( is_subclass_of( $class, 'expServiceBase' ) );
        }
    }

    public function testTheCatalogListsTheDomains()
    {
        $r = $this->ok( 'expServicesCatalog', 'domains' );
        $found = array();
        foreach ( $r as $d )
            $found[$d['domain']] = $d['services'];
        foreach ( self::$classes as $domain => $class )
        {
            $this->assertArrayHasKey( $domain, $found, "domain $domain in the catalogue" );
            $this->assertSame( count( $class::$services ), $found[$domain] );
        }
    }

    public function testEveryWriteRefusesAGetRequest()
    {
        expServiceBase::$trustRequest = null;
        $checked = 0;
        foreach ( self::$classes as $class )
            foreach ( $class::$services as $method => $d )
            {
                if ( empty( $d['write'] ) || $d['access'] === 'public' )
                    continue;
                $r = $this->call( $class, $method );
                $this->assertFalse( $r['ok'], "$class::$method" );
                $this->assertContains( $r['error']['code'], array( 401, 403 ), "$class::$method refuses without POST: " . $r['error']['message'] );
                $checked++;
            }
        $this->assertGreaterThan( 50, $checked );
    }

    public function testEveryNonPublicServiceAsksAnonymousToLogIn()
    {
        $this->loginAnonymous();
        $checked = 0;
        foreach ( self::$classes as $class )
            foreach ( $class::$services as $method => $d )
            {
                if ( $d['access'] === 'public' || $d['access'] === array( 'shop', 'buy' ) )
                    continue;
                $r = $this->call( $class, $method );
                $this->assertFalse( $r['ok'], "$class::$method" );
                $this->assertContains( $r['error']['code'], array( 400, 401, 403 ), "$class::$method denies anonymous: " . $r['error']['message'] );
                $checked++;
            }
        $this->assertGreaterThan( 100, $checked );
    }

    public function testNoServiceMethodIsCalledThatIsNotDeclared()
    {
        $r = $this->fails( 404, 'expProductServices', 'noSuchService' );
        $this->assertFalse( $r['ok'] );
    }
}
