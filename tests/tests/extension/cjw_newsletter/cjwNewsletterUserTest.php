<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** CjwNewsletterUser: creation, lookup, status transitions, names, the check for updates, the confirmation mail. */
class cjwNewsletterUserTest extends cjwNewsletterTestCase
{
    public function testCreateStoreAndFetchByEveryKey()
    {
        $email = $this->newEmail( 'fetch' );
        $user = CjwNewsletterUser::create( $email, 1, 'Ann', 'Tester', 0, CjwNewsletterUser::STATUS_PENDING, 'test', 'c1', 'c2', 'c3', 'c4' );
        $user->store();
        $id = (int)$user->attribute( 'id' );
        $this->assertGreaterThan( 0, $id );
        $this->assertSame( $id, (int)CjwNewsletterUser::fetch( $id )->attribute( 'id' ) );
        $this->assertSame( $id, (int)CjwNewsletterUser::fetchByEmail( $email )->attribute( 'id' ) );
        $this->assertSame( $id, (int)CjwNewsletterUser::fetchByHash( $user->attribute( 'hash' ) )->attribute( 'id' ) );
        $this->assertSame( $id, (int)CjwNewsletterUser::fetchByRemoteId( $user->attribute( 'remote_id' ) )->attribute( 'id' ) );
        $this->assertStringStartsWith( 'cjwnl:test:', $user->attribute( 'remote_id' ) );
        $this->assertSame( 'c3', CjwNewsletterUser::fetch( $id )->attribute( 'custom_data_text_3' ) );
    }

    public function testHashesAreUniqueAndUnguessable()
    {
        $hashes = array();
        for ( $i = 0; $i < 50; $i++ )
            $hashes[] = CjwNewsletterUtils::generateUniqueMd5Hash( 'same' );
        $this->assertCount( 50, array_unique( $hashes ), 'the same input gives a new hash every time' );
        foreach ( $hashes as $hash )
            $this->assertMatchesRegularExpression( '/^[0-9a-f]{32}$/', $hash );
    }

    public function testFetchByEmailIsCaseInsensitiveAndKeepsApostrophes()
    {
        $local = 'nltest-' . getmypid() . '-o\'Brien';
        $email = $local . '@' . self::MAIL_DOMAIN;
        $this->extraEmails[] = $email;
        $user = CjwNewsletterUser::create( $email, 0, 'O', 'Brien', 0, CjwNewsletterUser::STATUS_CONFIRMED, 'test', '', '', '', '' );
        $user->store();
        $this->assertNotFalse( CjwNewsletterUser::fetchByEmail( $email ), 'an apostrophe is part of a valid address' );
        $this->assertNotFalse( CjwNewsletterUser::fetchByEmail( strtoupper( $email ) ), 'the lookup ignores case' );
        $this->assertNotFalse( CjwNewsletterUser::fetchByEmail( '  ' . $email . ' ' ), 'and surrounding blanks' );
    }

    public function testFetchOfUnknownKeysIsFalse()
    {
        $this->assertFalse( CjwNewsletterUser::fetchByEmail( 'nltest-nobody@' . self::MAIL_DOMAIN ) );
        $this->assertEmpty( CjwNewsletterUser::fetchByHash( 'nosuchhash' ) );
        $this->assertFalse( CjwNewsletterUser::fetchByRemoteId( 'nosuchremote' ) );
        $this->assertFalse( CjwNewsletterUser::fetchByRemoteId( '' ), 'an empty remote id matches nobody' );
        $this->assertFalse( CjwNewsletterUser::fetchByRemoteId( false ) );
        $this->assertFalse( CjwNewsletterUser::fetchByEzUserId( 0 ) );
        $this->assertNull( CjwNewsletterUser::fetch( 999999999 ) );
    }

    public function testSqlSpecialCharactersAreHarmless()
    {
        foreach ( array( "x' OR '1'='1", 'x"; DROP TABLE cjwnl_user; --', '%', '_', "\\" ) as $text )
        {
            $this->assertFalse( CjwNewsletterUser::fetchByEmail( $text ), $text );
            $this->assertEmpty( CjwNewsletterUser::fetchByHash( $text ), $text );
            $this->assertFalse( CjwNewsletterUser::fetchByRemoteId( $text ), $text );
            $this->assertIsArray( CjwNewsletterUser::fetchList( 10, 0, $text ), $text );
            $this->assertIsNumeric( CjwNewsletterUser::fetchListCount( $text ), $text );
        }
        $this->assertGreaterThanOrEqual( 0, (int)eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM cjwnl_user' )[0]['c'] );
    }

    public function testStatusChangesSetTheirTimestamps()
    {
        $user = $this->newSubscriber( 'ts', CjwNewsletterUser::STATUS_PENDING );
        $this->assertFalse( $user->isConfirmed() );
        $user->setAttribute( 'status', CjwNewsletterUser::STATUS_CONFIRMED );
        $this->assertTrue( $user->isConfirmed() );
        $this->assertGreaterThan( 0, (int)$user->attribute( 'confirmed' ) );
        $user->setAttribute( 'status', CjwNewsletterUser::STATUS_REMOVED_SELF );
        $this->assertGreaterThan( 0, (int)$user->attribute( 'removed' ) );
        $this->assertTrue( $user->isRemovedSelf() );
        $user->setAttribute( 'status', CjwNewsletterUser::STATUS_BOUNCED_HARD );
        $this->assertGreaterThan( 0, (int)$user->attribute( 'bounced' ) );
        $user->setAttribute( 'status', CjwNewsletterUser::STATUS_BLACKLISTED );
        $this->assertGreaterThan( 0, (int)$user->attribute( 'blacklisted' ) );
    }

    public function testConfirmAllConfirmsTheUserAndEveryOpenSubscription()
    {
        $user = $this->newSubscriber( 'conf', CjwNewsletterUser::STATUS_PENDING, CjwNewsletterSubscription::STATUS_PENDING );
        $result = $user->confirmAll();
        $this->assertCount( 1, $result );
        $user = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        $this->assertTrue( $user->isConfirmed() );
        $this->assertContains( (int)$this->subscriptionOf( $user )->attribute( 'status' ), array( CjwNewsletterSubscription::STATUS_CONFIRMED, CjwNewsletterSubscription::STATUS_APPROVED ) );
    }

    public function testBlacklistingAbortsOpenSendItemsAndSubscriptions()
    {
        $user = $this->newSubscriber( 'bl' );
        $sub = $this->subscriptionOf( $user );
        $item = CjwNewsletterEditionSendItem::create( 999999, $user->attribute( 'id' ), 0, $sub->attribute( 'id' ) );
        $this->assertInstanceOf( 'CjwNewsletterEditionSendItem', $item );
        $user->setBlacklisted();
        $this->assertSame( CjwNewsletterUser::STATUS_BLACKLISTED, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'status' ) );
        $this->assertSame( CjwNewsletterSubscription::STATUS_BLACKLISTED, (int)$this->subscriptionOf( $user )->attribute( 'status' ) );
        $this->assertSame( CjwNewsletterEditionSendItem::STATUS_ABORT,
            (int)CjwNewsletterEditionSendItem::fetchByHash( $item->attribute( 'hash' ) )->attribute( 'status' ) );
        $user = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        $user->setNonBlacklisted();
        $this->assertSame( CjwNewsletterUser::STATUS_CONFIRMED, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'status' ) );
    }

    public function testBounceThresholdTurnsTheUserBouncedAndAbortsItsItems()
    {
        $threshold = (int)eZINI::instance( 'cjw_newsletter.ini' )->variable( 'BounceSettings', 'BounceThresholdValue' );
        $user = $this->newSubscriber( 'bounce' );
        $sub = $this->subscriptionOf( $user );
        $item = CjwNewsletterEditionSendItem::create( 999998, $user->attribute( 'id' ), 0, $sub->attribute( 'id' ) );
        for ( $i = 1; $i < $threshold; $i++ )
        {
            $user->setBounced( false );
            $this->assertSame( CjwNewsletterUser::STATUS_CONFIRMED, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'status' ), "below the threshold, bounce $i" );
        }
        $user->setBounced( true );
        $fresh = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterUser::STATUS_BOUNCED_HARD, (int)$fresh->attribute( 'status' ) );
        $this->assertSame( $threshold, (int)$fresh->attribute( 'bounce_count' ) );
        $this->assertSame( CjwNewsletterSubscription::STATUS_BOUNCED_HARD, (int)$this->subscriptionOf( $user )->attribute( 'status' ) );
        $this->assertSame( CjwNewsletterEditionSendItem::STATUS_ABORT,
            (int)CjwNewsletterEditionSendItem::fetchByHash( $item->attribute( 'hash' ) )->attribute( 'status' ), 'the open item is stored as aborted' );
    }

    public function testSetRemovedIsStored()
    {
        $user = $this->newSubscriber( 'rm' );
        $user->setRemoved( true );
        $this->assertSame( CjwNewsletterUser::STATUS_REMOVED_ADMIN, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'status' ) );
        $user->setRemoved( false );
        $this->assertSame( CjwNewsletterUser::STATUS_REMOVED_SELF, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'status' ) );
    }

    public function testRemoveDeletesTheUserAndItsSubscriptions()
    {
        $user = $this->newSubscriber( 'del' );
        $id = (int)$user->attribute( 'id' );
        $user->remove();
        $this->assertNull( CjwNewsletterUser::fetch( $id ) );
        $this->assertSame( array(), CjwNewsletterSubscription::fetchSubscriptionListByNewsletterUserId( $id ) );
    }

    public function testNamesAndSalutation()
    {
        $user = CjwNewsletterUser::create( $this->newEmail( 'name' ), 1, 'Ann', 'Tester', 0, CjwNewsletterUser::STATUS_CONFIRMED, 't', '', '', '', '' );
        $this->assertSame( 'Ann Tester', $user->getEmailName() );
        $this->assertStringContainsString( 'Ann', $user->getName() );
        $this->assertStringContainsString( 'Tester', $user->getName() );
        $none = CjwNewsletterUser::create( $this->newEmail( 'noname' ), 0, '', '', 0, CjwNewsletterUser::STATUS_CONFIRMED, 't', '', '', '', '' );
        $this->assertSame( '', $none->getEmailName() );
        $this->assertIsArray( CjwNewsletterUser::getAvailableSalutationNameArrayFromIni() );
        $this->assertSame( '', CjwNewsletterUser::create( $this->newEmail( 'sal' ), 99, '', '', 0, 0, 't', '', '', '', '' )->getSalutationName() );
    }

    public function testStatusStringsAreDefinedForEveryStatus()
    {
        foreach ( array( 20, 0, 1, 3, 4, 6, 7, 8 ) as $status )
        {
            $user = CjwNewsletterUser::create( $this->newEmail( 'st' ), 0, '', '', 0, $status, 't', '', '', '', '' );
            $this->assertNotSame( '-', $user->getStatusString(), "status $status" );
        }
    }

    public function testCheckIfUserCanBeUpdated()
    {
        $this->assertSame( 40, CjwNewsletterUser::checkIfUserCanBeUpdated( $this->newEmail( 'new' ), 0 ), 'a new address is created' );
        $this->assertSame( -21, CjwNewsletterUser::checkIfUserCanBeUpdated( '', 0 ), 'an empty address is refused' );
    }

    public function testCreateUpdateNewsletterUserUpdatesAnExistingAddress()
    {
        $user = $this->newSubscriber( 'upd' );
        $again = CjwNewsletterUser::createUpdateNewsletterUser( $user->attribute( 'email' ), 2, 'Changed', 'Name', 0, CjwNewsletterUser::STATUS_PENDING, 'test', 'a', 'b', 'c', 'd' );
        $this->assertSame( (int)$user->attribute( 'id' ), (int)$again->attribute( 'id' ), 'the same user, not a second one' );
        $this->assertSame( 'Changed', CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'first_name' ) );
        $this->assertSame( CjwNewsletterUser::STATUS_CONFIRMED, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'status' ), 'a confirmed user stays confirmed' );
    }

    public function testListSearchAndCounts()
    {
        $user = $this->newSubscriber( 'searchme' );
        $found = CjwNewsletterUser::fetchList( 50, 0, 'searchme' );
        $this->assertCount( 1, $found );
        $this->assertSame( 1, (int)CjwNewsletterUser::fetchListCount( 'searchme' ) );
        $this->assertCount( 0, CjwNewsletterUser::fetchList( 50, 0, 'no-such-text-at-all' ) );
        $byStatus = CjwNewsletterUser::fetchUserListByStatus( CjwNewsletterUser::STATUS_CONFIRMED, 0 );
        $this->assertGreaterThanOrEqual( 1, count( $byStatus ), 'limit 0 means no limit' );
    }

    public function testFilteredListFindsUsersBySubscription()
    {
        $user = $this->newSubscriber( 'filt' );
        $list = CjwNewsletterUser::fetchUserListByFilter( array( array( 'cjwnl_subscription.list_contentobject_id' => array( array( self::LIST_OBJECT_ID ) ) ),
                                                                  array( 'cjwnl_user.email' => array( 'like', '%' . $user->attribute( 'email' ) . '%' ) ) ), 50, 0 );
        $this->assertCount( 1, $list );
        $none = CjwNewsletterUser::fetchUserListByFilter( array( array( 'cjwnl_user.email' => array( 'like', "%x' OR '1'='1%" ) ) ), 0, 0 );
        $this->assertSame( array(), $none );
    }

    public function testConfirmationMailIsWrittenToTheOutbox()
    {
        $user = $this->newSubscriber( 'mail', CjwNewsletterUser::STATUS_PENDING );
        $result = $user->sendSubcriptionConfirmationMail();
        $this->assertTrue( $result['send_result'] );
        $this->assertSame( 'file', $result['transport_method'] );
        $files = $this->outbox();
        $this->assertCount( 1, $files );
        $text = $this->mailText( $files[0] );
        $this->assertStringContainsString( 'To: ', $text );
        $this->assertStringContainsString( $user->attribute( 'email' ), $text );
        $this->assertStringContainsString( '/newsletter/configure/' . $user->attribute( 'hash' ), $text );
    }

    public function testInformationMailCarriesTheConfigureLink()
    {
        $user = $this->newSubscriber( 'info' );
        $result = $user->sendSubcriptionInformationMail();
        $this->assertTrue( $result['send_result'] );
        $this->assertStringContainsString( $user->attribute( 'hash' ), $this->mailText( $this->outbox()[0] ) );
    }

    public function testSubscriptionArrayIsKeyedByList()
    {
        $user = $this->newSubscriber( 'arr' );
        $array = $user->getSubscriptionArray();
        $this->assertSame( array( self::LIST_OBJECT_ID ), array_keys( $array ) );
    }

    public function testBlacklistedAddressCannotBeConfirmedBack()
    {
        $user = $this->newSubscriber( 'bl2' );
        $item = CjwNewsletterBlacklistItem::create( $user->attribute( 'email' ), 'test' );
        $item->store();
        $this->assertTrue( CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->isOnBlacklist() );
        $item->remove();
        $this->assertFalse( CjwNewsletterBlacklistItem::isEmailOnBlacklist( $user->attribute( 'email' ) ) );
    }
}
