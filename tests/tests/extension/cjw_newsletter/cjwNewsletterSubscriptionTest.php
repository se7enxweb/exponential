<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** CjwNewsletterSubscription and CjwNewsletterBlacklistItem. */
class cjwNewsletterSubscriptionTest extends cjwNewsletterTestCase
{
    public function testArrayAndStringConversionsRoundTrip()
    {
        $this->assertSame( ';0;1;', CjwNewsletterSubscription::arrayToString( array( 0, 1 ) ) );
        $this->assertSame( array( '', '0', '1', '' ), CjwNewsletterSubscription::stringToArray( ';0;1;' ) );
        $this->assertSame( ';;', CjwNewsletterSubscription::arrayToString( array() ) );
        $this->assertSame( ';0;1;', CjwNewsletterList::arrayToString( array( 0, 1 ) ) );
        $this->assertContains( '1', CjwNewsletterList::stringToArray( ';1;' ) );
    }

    public function testCreateStoresAHashedSubscription()
    {
        $user = $this->newSubscriber( 'c' );
        $sub = $this->subscriptionOf( $user );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{32}$/', $sub->attribute( 'hash' ) );
        $this->assertSame( $sub->attribute( 'id' ), CjwNewsletterSubscription::fetchByHash( $sub->attribute( 'hash' ) )->attribute( 'id' ) );
        $this->assertSame( $sub->attribute( 'id' ), CjwNewsletterSubscription::fetch( $sub->attribute( 'id' ) )->attribute( 'id' ) );
        $this->assertSame( array( 0 => 'HTML' ), $sub->getOutputFormatArray() );
        $this->assertFalse( $sub->isVirtual() );
        $this->assertSame( $user->attribute( 'id' ), $sub->getNewsletterUserObject()->attribute( 'id' ) );
    }

    public function testFetchOfUnknownSubscriptionsIsEmpty()
    {
        $this->assertEmpty( CjwNewsletterSubscription::fetchByHash( 'nosuchhash' ) );
        $this->assertEmpty( CjwNewsletterSubscription::fetch( 999999999 ) );
        $this->assertFalse( CjwNewsletterSubscription::fetchByListIdAndNewsletterUserId( self::LIST_OBJECT_ID, 999999999 ) );
        $this->assertSame( array(), CjwNewsletterSubscription::fetchSubscriptionListByNewsletterUserId( 999999999 ) );
    }

    public function testPendingBecomesConfirmedOnConfirm()
    {
        $user = $this->newSubscriber( 'conf', CjwNewsletterUser::STATUS_PENDING, CjwNewsletterSubscription::STATUS_PENDING );
        $sub = $this->subscriptionOf( $user );
        $this->assertSame( CjwNewsletterSubscription::STATUS_PENDING, (int)$sub->attribute( 'status' ) );
        $this->assertTrue( $sub->confirm() );
        $this->assertNotSame( CjwNewsletterSubscription::STATUS_PENDING, (int)$this->subscriptionOf( $user )->attribute( 'status' ) );
        $this->assertFalse( CjwNewsletterSubscription::fetch( $sub->attribute( 'id' ) )->isRemoved() );
    }

    public function testUnsubscribeRemovesOnceAndOnlyOnce()
    {
        $user = $this->newSubscriber( 'unsub' );
        $sub = $this->subscriptionOf( $user );
        $this->assertTrue( $sub->unsubscribe() );
        $sub = $this->subscriptionOf( $user );
        $this->assertTrue( $sub->isRemoved() );
        $this->assertTrue( $sub->isRemovedSelf() );
        $this->assertFalse( $sub->unsubscribe(), 'a second unsubscribe changes nothing' );
    }

    public function testAdminRemovalAndApproval()
    {
        $user = $this->newSubscriber( 'adm' );
        $sub = $this->subscriptionOf( $user );
        $sub->removeByAdmin();
        $this->assertSame( CjwNewsletterSubscription::STATUS_REMOVED_ADMIN, (int)$this->subscriptionOf( $user )->attribute( 'status' ) );
        $this->subscriptionOf( $user )->approveByAdmin();
        $this->assertSame( CjwNewsletterSubscription::STATUS_APPROVED, (int)$this->subscriptionOf( $user )->attribute( 'status' ) );
        CjwNewsletterSubscription::removeSubscriptionByAdmin( self::LIST_OBJECT_ID, $user->attribute( 'id' ) );
        $this->assertTrue( $this->subscriptionOf( $user )->isRemoved() );
        $this->assertFalse( (bool)CjwNewsletterSubscription::removeSubscriptionByNewsletterUserSelf( self::LIST_OBJECT_ID, 999999999 ) );
    }

    public function testStatusStringsAndTheStatusList()
    {
        $names = CjwNewsletterSubscription::availableStatusIdNameArray();
        $this->assertGreaterThanOrEqual( 5, count( $names ) );
        foreach ( $names as $id => $name )
            $this->assertNotSame( '', (string)$name );
        $sub = $this->subscriptionOf( $this->newSubscriber( 'str' ) );
        $this->assertNotSame( '', $sub->getStatusString() );
    }

    public function testCreateUpdateKeepsOneSubscriptionPerUserAndList()
    {
        $user = $this->newSubscriber( 'one' );
        CjwNewsletterSubscription::createUpdateNewsletterSubscription( self::LIST_OBJECT_ID, $user->attribute( 'id' ), array( 0, 1 ), CjwNewsletterSubscription::STATUS_PENDING );
        $this->assertCount( 1, CjwNewsletterSubscription::fetchSubscriptionListByNewsletterUserId( $user->attribute( 'id' ) ) );
        $this->assertSame( array( 0 => 'HTML', 1 => 'Text' ), $this->subscriptionOf( $user )->getOutputFormatArray() );
    }

    public function testCreateSubscriptionByArraySubscribesAndRemoves()
    {
        $email = $this->newEmail( 'arr' );
        $result = CjwNewsletterSubscription::createSubscriptionByArray( array(
            'email' => $email, 'first_name' => 'A', 'last_name' => 'B', 'salutation' => 0,
            'id_array' => array( self::LIST_OBJECT_ID ), 'list_array' => array( self::LIST_OBJECT_ID ),
            'list_output_format_array' => array( self::LIST_OBJECT_ID => array( 0 ) ) ), CjwNewsletterUser::STATUS_PENDING, false, 'test' );
        $this->assertIsArray( $result );
        $this->assertArrayHasKey( self::LIST_OBJECT_ID, $result['list_subscribe'] );
        $user = CjwNewsletterUser::fetchByEmail( $email );
        $this->assertNotNull( $this->subscriptionOf( $user ) );
        // the list is in id_array but no longer in list_array: removed by the user
        $result = CjwNewsletterSubscription::createSubscriptionByArray( array(
            'email' => $email, 'first_name' => 'A', 'last_name' => 'B', 'salutation' => 0,
            'id_array' => array( self::LIST_OBJECT_ID ), 'list_array' => array(),
            'list_output_format_array' => array() ), CjwNewsletterUser::STATUS_PENDING, false, 'configure' );
        $this->assertTrue( $this->subscriptionOf( $user )->isRemoved() );
    }

    public function testCreateSubscriptionByArrayRefusesAnObjectThatIsNoList()
    {
        $email = $this->newEmail( 'nolist' );
        $result = CjwNewsletterSubscription::createSubscriptionByArray( array(
            'email' => $email, 'first_name' => 'A', 'last_name' => 'B', 'salutation' => 0,
            'id_array' => array( 1, 999999999 ), 'list_array' => array( 1, 999999999 ),
            'list_output_format_array' => array() ), CjwNewsletterUser::STATUS_PENDING, true, 'test' );
        $this->assertSame( array(), $result['list_subscribe'], 'neither the content root nor a missing object is a list' );
        $this->assertNotEmpty( $result['errors'] );
        $this->assertSame( array(), CjwNewsletterSubscription::fetchSubscriptionListByNewsletterUserId( CjwNewsletterUser::fetchByEmail( $email )->attribute( 'id' ) ) );
    }

    public function testCreateSubscriptionByArrayForAnExistingAddressInSubscribeContextReturnsFalse()
    {
        $user = $this->newSubscriber( 'dup' );
        $this->assertFalse( CjwNewsletterSubscription::createSubscriptionByArray( array(
            'email' => $user->attribute( 'email' ), 'first_name' => 'A', 'last_name' => 'B',
            'id_array' => array(), 'list_array' => array(), 'list_output_format_array' => array() ), CjwNewsletterUser::STATUS_PENDING, true, 'subscribe' ) );
    }

    public function testListFetchesAndStatistic()
    {
        $this->newSubscriber( 'l1' );
        $this->newSubscriber( 'l2', null, CjwNewsletterSubscription::STATUS_PENDING );
        $list = CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, 0 );
        $this->assertInstanceOf( 'CjwNewsletterList', $list );
        $approved = CjwNewsletterSubscription::fetchSubscriptionListByListId( $list, CjwNewsletterSubscription::STATUS_APPROVED, 0, 0 );
        $this->assertGreaterThanOrEqual( 1, count( $approved ) );
        $this->assertSame( count( $approved ), (int)CjwNewsletterSubscription::fetchSubscriptionListByListIdCount( $list, CjwNewsletterSubscription::STATUS_APPROVED ) );
        $stat = CjwNewsletterSubscription::fetchSubscriptionListStatistic( $list );
        foreach ( array( 'all', 'pending', 'confirmed', 'approved', 'removed', 'bounced', 'blacklisted' ) as $key )
            $this->assertArrayHasKey( $key, $stat );
        $this->assertGreaterThanOrEqual( 2, $stat['all'] );
        $this->assertGreaterThanOrEqual( 1, count( CjwNewsletterSubscription::fetchSubscriptionListByListIdAndStatus( self::LIST_OBJECT_ID, CjwNewsletterSubscription::STATUS_PENDING, 0, 0 ) ) );
    }

    public function testActiveSubscriptionListLeavesOutRemovedAndBlacklisted()
    {
        $user = $this->newSubscriber( 'act' );
        $this->assertCount( 1, CjwNewsletterSubscription::fetchListNotRemovedOrBlacklistedByNewsletterUserId( $user->attribute( 'id' ) ) );
        $this->subscriptionOf( $user )->unsubscribe();
        $this->assertCount( 0, CjwNewsletterSubscription::fetchListNotRemovedOrBlacklistedByNewsletterUserId( $user->attribute( 'id' ) ) );
    }

    public function testRemovingASubscriptionRaisesNoWarning()
    {
        $user = $this->newSubscriber( 'rm' );
        $this->subscriptionOf( $user )->remove();
        $this->assertFalse( $this->subscriptionOf( $user ) );
    }

    // ------------------------------------------------------------------ blacklist

    public function testBlacklistItemLifecycle()
    {
        $email = $this->newEmail( 'bl' );
        $this->assertFalse( CjwNewsletterBlacklistItem::isEmailOnBlacklist( $email ) );
        $item = CjwNewsletterBlacklistItem::create( strtoupper( $email ), 'a note' );
        $item->store();
        $this->assertTrue( CjwNewsletterBlacklistItem::isEmailOnBlacklist( $email ), 'the lookup ignores case' );
        $this->assertSame( strtolower( $email ), CjwNewsletterBlacklistItem::fetchByEmail( $email )->attribute( 'email' ) );
        $this->assertSame( md5( strtolower( $email ) ), CjwNewsletterBlacklistItem::generateEmailHash( '  ' . strtoupper( $email ) ) );
        $this->assertGreaterThanOrEqual( 1, CjwNewsletterBlacklistItem::fetchAllBlacklistItemsCount() );
        $this->assertNotEmpty( CjwNewsletterBlacklistItem::fetchAllBlacklistItems( 0 ), 'limit 0 lists them all' );
        $this->assertNotEmpty( CjwNewsletterBlacklistItem::fetchAllBlacklistItems( 5, 0 ) );
        $this->assertSame( $item->attribute( 'id' ), CjwNewsletterBlacklistItem::fetch( $item->attribute( 'id' ) )->attribute( 'id' ) );
        $item->remove();
        $this->assertFalse( CjwNewsletterBlacklistItem::isEmailOnBlacklist( $email ) );
    }

    public function testBlacklistingAKnownAddressBlacklistsTheUser()
    {
        $user = $this->newSubscriber( 'blu' );
        $item = CjwNewsletterBlacklistItem::create( $user->attribute( 'email' ), '' );
        $this->assertSame( $user->attribute( 'id' ), $item->attribute( 'newsletter_user_id' ) );
        $item->store();
        $this->assertSame( CjwNewsletterUser::STATUS_BLACKLISTED, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'status' ) );
        $this->assertSame( $user->attribute( 'id' ), $item->getNewsletterUserObject()->attribute( 'id' ) );
        $item->remove();
        $this->assertNotSame( CjwNewsletterUser::STATUS_BLACKLISTED, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'status' ) );
    }

    public function testBlacklistLookupOfUnknownAddressIsFalse()
    {
        $this->assertFalse( CjwNewsletterBlacklistItem::fetchByEmail( $this->newEmail( 'unknown' ) ) );
        $this->assertFalse( CjwNewsletterBlacklistItem::create( $this->newEmail( 'x' ), '' )->getNewsletterUserObject() );
    }
}
