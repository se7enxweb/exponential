<?php
require_once __DIR__ . '/../media/expMediaTestCase.php';

/** expnewsletter: the cjw_newsletter services; the installation may hold no lists, then they answer empty or 404. */
class expNewsletterServicesTest extends expMediaTestCase
{
    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expNewsletterServices', 13 );
    }

    public function testAvailable()
    {
        $r = $this->ok( 'expNewsletterServices', 'available' )['data'];
        $this->assertSame( class_exists( 'CjwNewsletterList' ), $r['available'] );
    }

    public function testStatusNames()
    {
        $s = $this->ok( 'expNewsletterServices', 'statuses' )['data'];
        $this->assertSame( 'pending', $s[0] );
        $this->assertSame( 'blacklisted', $s[8] );
    }

    public function testListsArePaged()
    {
        $this->assertPaged( $this->ok( 'expNewsletterServices', 'lists', array( 5, 0 ) ) );
    }

    public function testANodeThatIsNoListIs404()
    {
        $this->fails( 404, 'expNewsletterServices', 'list', array( 2 ) );
        $this->fails( 404, 'expNewsletterServices', 'subscriberscount', array( 2 ) );
    }

    public function testMySubscriptionsIsAList()
    {
        $this->assertIsArray( $this->ok( 'expNewsletterServices', 'mysubscriptions' )['data'] );
    }

    public function testSubscribeNeedsPostAndALogin()
    {
        $this->fails( 403, 'expNewsletterServices', 'subscribe', array( 2 ) );
        $this->loginAnonymous();
        $this->assertSame( 401, $this->call( 'expNewsletterServices', 'mysubscriptions' )['error']['code'] );
    }

    public function testSubscribeToANonListIs404()
    {
        $this->fails( 404, 'expNewsletterServices', 'subscribe', array( 2 ), array( 'format' => 'html' ) );
        $this->fails( 404, 'expNewsletterServices', 'unsubscribe', array( 2 ), array( 'confirm' => '1' ) );
    }

    public function testUsersAreAdministration()
    {
        $this->assertPaged( $this->ok( 'expNewsletterServices', 'users', array( 5, 0 ) ) );
        $this->assertArrayHasKey( 'count', $this->ok( 'expNewsletterServices', 'usercount' )['data'] );
        $this->loginAnonymous();
        $this->assertFalse( $this->call( 'expNewsletterServices', 'users', array( 5, 0 ) )['ok'] );
    }
}
