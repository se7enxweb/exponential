<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** The administration views with real data: users, subscriptions, blacklist, mail accounts, settings, redirect safety. */
class cjwNewsletterAdminViewsTest extends cjwNewsletterTestCase
{
    private function post( $user, array $extra = array() )
    {
        return array_merge( array( 'Subscription_Email' => $user ? $user->attribute( 'email' ) : $this->newEmail( 'new' ),
            'Subscription_FirstName' => 'Edited', 'Subscription_LastName' => 'Person', 'Subscription_Salutation' => '1', 'Subscription_Note' => 'a note',
            'Subscription_IdArray' => array( self::LIST_OBJECT_ID ), 'Subscription_ListArray' => array( self::LIST_OBJECT_ID ),
            'Subscription_OutputFormatArray_' . self::LIST_OBJECT_ID => array( 0, 1 ), 'Subscription_StatusId_' . self::LIST_OBJECT_ID => CjwNewsletterSubscription::STATUS_APPROVED ), $extra );
    }

    public function testUserListShowsAndSearchesUsers()
    {
        $user = $this->newSubscriber( 'listme' );
        $r = $this->runView( 'user_list' );
        $this->assertViewOk( $r );
        $r = $this->runView( 'user_list', array(), array( 'SearchUserEmail' => 'listme' ) );
        $this->assertStringContainsString( $user->attribute( 'email' ), $r['content'] );
        $r = $this->runView( 'user_list', array(), array( 'SearchUserEmail' => 'no-such-user-text' ) );
        $this->assertStringNotContainsString( $user->attribute( 'email' ), $r['content'] );
    }

    public function testUserListSearchSurvivesQuotesAndEscapesTheEchoedValue()
    {
        $r = $this->runView( 'user_list', array(), array( 'SearchUserEmail' => "o'\"><script>alert(1)</script>" ) );
        $this->assertViewOk( $r );
        $this->assertStringNotContainsString( '<script>alert(1)</script>', $r['content'] );
    }

    public function testUserViewShowsTheUser()
    {
        $user = $this->newSubscriber( 'view' );
        $r = $this->runView( 'user_view', array( $user->attribute( 'id' ) ) );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( $user->attribute( 'email' ), $r['content'] );
    }

    public function testUserCreateWithAnInvalidAddressShowsTheFormAgain()
    {
        $r = $this->runView( 'user_create', array(), array( 'CreateEditButton' => 'x', 'Subscription_Email' => 'not an address' ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'] );
        $this->assertNotNull( $r['content'] );
    }

    public function testUserCreateHandsOverToEditWithAValidAddress()
    {
        $r = $this->runView( 'user_create', array(), array( 'CreateEditButton' => 'x', 'Subscription_Email' => $this->newEmail( 'cr' ) ) );
        $this->assertSame( eZModule::STATUS_RERUN, $r['exit'] );
    }

    public function testUserCreateIgnoresAnObjectInTheOldPostField()
    {
        $payload = base64_encode( serialize( new ArrayObject( array( 'x' => 'y' ) ) ) );
        $r = $this->runView( 'user_create', array(), array( 'OldPostVarSerialized' => $payload ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'] );
        $r = $this->runView( 'user_create', array(), array( 'OldPostVarSerialized' => '!!!not base64 or serialized' ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'] );
    }

    public function testUserEditStoresTheChanges()
    {
        $user = $this->newSubscriber( 'edit' );
        $r = $this->runView( 'user_edit', array( $user->attribute( 'id' ) ), $this->post( $user, array( 'StoreButton' => 'Store' ) ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $fresh = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        $this->assertSame( 'Edited', $fresh->attribute( 'first_name' ) );
        $this->assertSame( 'Person', $fresh->attribute( 'last_name' ) );
        $this->assertSame( array( 0 => 'HTML', 1 => 'Text' ), $this->subscriptionOf( $fresh )->getOutputFormatArray() );
    }

    public function testUserEditRefusesAnInvalidAddress()
    {
        $user = $this->newSubscriber( 'editbad' );
        $r = $this->runView( 'user_edit', array( $user->attribute( 'id' ) ), $this->post( null, array( 'StoreButton' => 'Store', 'Subscription_Email' => 'nope' ) ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'] );
        $this->assertNotSame( 'Edited', CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'first_name' ) );
    }

    public function testUserEditRefusesAnAddressOfAnotherUser()
    {
        $a = $this->newSubscriber( 'ea' );
        $b = $this->newSubscriber( 'eb' );
        $r = $this->runView( 'user_edit', array( $a->attribute( 'id' ) ), $this->post( $b, array( 'StoreButton' => 'Store' ) ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'], 'the form is shown again with a warning' );
        $this->assertSame( $a->attribute( 'email' ), CjwNewsletterUser::fetch( $a->attribute( 'id' ) )->attribute( 'email' ) );
    }

    public function testUserRemoveConfirmsAndRemoves()
    {
        $user = $this->newSubscriber( 'rem' );
        $id = (int)$user->attribute( 'id' );
        $this->assertViewOk( $this->runView( 'user_remove', array( $id ) ) );
        $r = $this->runView( 'user_remove', array( $id ), array( 'RemoveButton' => 'Remove' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $this->assertNull( CjwNewsletterUser::fetch( $id ) );
    }

    public function testUserRemoveCancelRedirectsBack()
    {
        $user = $this->newSubscriber( 'remc' );
        $r = $this->runView( 'user_remove', array( $user->attribute( 'id' ) ), array( 'CancelButton' => 'Cancel' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $this->assertNotNull( CjwNewsletterUser::fetch( $user->attribute( 'id' ) ) );
    }

    public function testRedirectTargetsStayOnThisSite()
    {
        $user = $this->newSubscriber( 'redir' );
        foreach ( array( 'http://evil.example/x', '//evil.example/x', 'javascript:alert(1)', "/ok\r\nLocation: http://evil", '\\\\evil' ) as $target )
        {
            $r = $this->runView( 'user_remove', array( $user->attribute( 'id' ) ), array( 'CancelButton' => 'Cancel', 'RedirectUrlActionCancel' => $target ) );
            $this->assertStringNotContainsString( 'evil', (string)$r['redirect'], $target );
        }
        $r = $this->runView( 'user_remove', array( $user->attribute( 'id' ) ), array( 'CancelButton' => 'Cancel', 'RedirectUrlActionCancel' => '/newsletter/user_list' ) );
        $this->assertStringContainsString( 'newsletter/user_list', (string)$r['redirect'] );
        $this->assertSame( '/a', CjwNewsletterUtils::localRedirectPath( '/a', '/d' ) );
        $this->assertSame( 'a/b', CjwNewsletterUtils::localRedirectPath( 'a/b', '/d' ) );
        $this->assertSame( '/d', CjwNewsletterUtils::localRedirectPath( '', '/d' ) );
        $this->assertSame( '/d', CjwNewsletterUtils::localRedirectPath( 'ftp://x', '/d' ) );
    }

    public function testSubscriptionListAndView()
    {
        $user = $this->newSubscriber( 'sl' );
        $r = $this->runView( 'subscription_list', array( self::LIST_NODE_ID ) );
        $this->assertViewOk( $r );
        $sub = $this->subscriptionOf( $user );
        $r = $this->runView( 'subscription_view', array( $sub->attribute( 'id' ) ) );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( $user->attribute( 'email' ), $r['content'] );
        $this->runView( 'subscription_view', array( $sub->attribute( 'id' ) ), array(), array( 'SubscriptionRemoveButton' => '1' ) );
        $this->assertTrue( $this->subscriptionOf( $user )->isRemoved() );
        $this->runView( 'subscription_view', array( $sub->attribute( 'id' ) ), array( 'SubscriptionApproveButton' => '1' ) );
        $this->assertSame( CjwNewsletterSubscription::STATUS_APPROVED, (int)$this->subscriptionOf( $user )->attribute( 'status' ) );
    }

    public function testSubscriptionViewOfASubscriptionWhoseListIsGoneIsAnError()
    {
        $user = $this->newSubscriber( 'orphan' );
        $sub = CjwNewsletterSubscription::create( 999999, $user->attribute( 'id' ), array( 0 ), CjwNewsletterSubscription::STATUS_APPROVED );
        $sub->store();
        $r = $this->runView( 'subscription_view', array( $sub->attribute( 'id' ) ) );
        $this->assertSame( eZModule::STATUS_FAILED, $r['exit'] );
    }

    public function testBlacklistAddListAndRemove()
    {
        $email = $this->newEmail( 'bla' );
        $r = $this->runView( 'blacklist_item_add', array(), array( 'AddButton' => 'Add', 'Email' => $email, 'Note' => 'nltest note' ) );
        $this->assertViewOk( $r );
        $this->assertTrue( CjwNewsletterBlacklistItem::isEmailOnBlacklist( $email ) );
        $list = $this->runView( 'blacklist_item_list' );
        $this->assertStringContainsString( strtolower( $email ), $list['content'] );
        $item = CjwNewsletterBlacklistItem::fetchByEmail( $email );
        $r = $this->runView( 'blacklist_item_remove', array(), array( 'BlacklistIDArray' => array( $item->attribute( 'id' ) ) ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $this->assertFalse( CjwNewsletterBlacklistItem::isEmailOnBlacklist( $email ) );
    }

    public function testBlacklistAddOfAnExistingAddressDoesNotDuplicate()
    {
        $email = $this->newEmail( 'bld' );
        $this->runView( 'blacklist_item_add', array(), array( 'AddButton' => 'Add', 'Email' => $email ) );
        $this->runView( 'blacklist_item_add', array(), array( 'AddButton' => 'Add', 'Email' => strtoupper( $email ) ) );
        $this->assertSame( 1, (int)eZDB::instance()->arrayQuery( "SELECT COUNT(*) AS c FROM cjwnl_blacklist_item WHERE email_hash = '" . md5( strtolower( $email ) ) . "'" )[0]['c'] );
    }

    public function testBlacklistAddBlacklistsAKnownUser()
    {
        $user = $this->newSubscriber( 'blk' );
        $this->runView( 'blacklist_item_add', array(), array( 'AddButton' => 'Add', 'Email' => $user->attribute( 'email' ) ) );
        $this->assertSame( CjwNewsletterUser::STATUS_BLACKLISTED, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'status' ) );
    }

    public function testBlacklistRemoveOfUnknownEntriesIsAnErrorAndMalformedInputIsHarmless()
    {
        $r = $this->runView( 'blacklist_item_remove', array(), array( 'Email' => $this->newEmail( 'none' ) ) );
        $this->assertSame( eZModule::STATUS_FAILED, $r['exit'] );
        $r = $this->runView( 'blacklist_item_remove', array(), array( 'BlacklistIDArray' => '999999999' ) );
        $this->assertSame( eZModule::STATUS_FAILED, $r['exit'] );
        $r = $this->runView( 'blacklist_item_remove', array(), array( 'RedirectURI' => 'http://evil.example/' ) );
        $this->assertStringNotContainsString( 'evil', (string)$r['redirect'] );
    }

    public function testBlacklistAddWithoutAnAddressOffersTheForm()
    {
        $r = $this->runView( 'blacklist_item_add', array(), array( 'AddButton' => 'Add' ) );
        $this->assertViewOk( $r );
        $r = $this->runView( 'blacklist_item_add', array(), array( 'DiscardButton' => '1' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
    }

    public function testMailboxViews()
    {
        $this->assertViewOk( $this->runView( 'mailbox_list' ) );
        $this->assertViewOk( $this->runView( 'mailbox_edit', array( 0 ) ) );
        $email = 'nltest-box-' . getmypid() . '@example.invalid';
        $r = $this->runView( 'mailbox_edit', array( 0 ), array( 'edit' => '1', 'PublishButton' => '1', 'redirect' => 'newsletter/mailbox_list',
            'email' => $email, 'server' => 'localhost', 'port' => '143', 'user_name' => 'u', 'password' => 'p', 'type' => 'imap',
            'is_activated' => '0', 'is_ssl' => '0', 'delete_mails_from_server' => '0' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $box = null;
        foreach ( CjwNewsletterMailbox::fetchAllMailboxes() as $m )
            if ( $m->attribute( 'email' ) === $email )
                $box = $m;
        $this->assertNotNull( $box );
        $this->assertViewOk( $this->runView( 'mailbox_edit', array( $box->attribute( 'id' ) ) ) );
        $this->assertViewOk( $this->runView( 'mailbox_item_list' ) );
    }

    public function testMailboxEditWithIncompletePostAndHostileRedirectIsHarmless()
    {
        $r = $this->runView( 'mailbox_edit', array( 0 ), array( 'PublishButton' => '1' ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'], 'a publish without data stores nothing' );
        $r = $this->runView( 'mailbox_edit', array( 0 ), array( 'DiscardButton' => '1', 'redirect' => 'http://evil.example/' ) );
        $this->assertStringNotContainsString( 'evil', (string)$r['redirect'] );
        $r = $this->runView( 'mailbox_edit', array( 999999999 ) );
        $this->assertSame( eZModule::STATUS_FAILED, $r['exit'] );
    }

    public function testMailboxItemViewShowsAnItem()
    {
        $item = CjwNewsletterMailboxItem::addMailboxItem( 999001, 'nltest-view', 1, "From: a@example.invalid\r\nSubject: s\r\n\r\nbody\r\n" );
        $path = $item->getFilePath();
        $r = $this->runView( 'mailbox_item_view', array( $item->attribute( 'id' ) ) );
        $this->assertViewOk( $r );
        if ( is_file( $path ) )
            unlink( $path );
    }

    public function testIndexAndSettingsViews()
    {
        $r = $this->runView( 'index' );
        $this->assertViewOk( $r );
        $r = $this->runView( 'settings' );
        $this->assertContains( $r['exit'], array( eZModule::STATUS_OK, eZModule::STATUS_REDIRECT ) );
    }

    public function testViewsRenderForAnonymousVisitorsWhereTheyArePublic()
    {
        $this->loginAnonymous();
        $user = $this->newSubscriber( 'pub' );
        $this->assertViewOk( $this->runView( 'subscribe' ) );
        $this->assertViewOk( $this->runView( 'subscribe_infomail' ) );
        $this->assertViewOk( $this->runView( 'configure', array( $user->attribute( 'hash' ) ) ) );
        $this->assertViewOk( $this->runView( 'unsubscribe', array( $this->subscriptionOf( $user )->attribute( 'hash' ) ) ) );
    }

    public function testSubscribeInfomailSendsTheMailOnlyForKnownAddresses()
    {
        $this->loginAnonymous();
        $user = $this->newSubscriber( 'infoview' );
        $r = $this->runView( 'subscribe_infomail', array(), array( 'SubscribeInfoMailButton' => '1', 'EmailInput' => $user->attribute( 'email' ), 'BackUrlInput' => '/' ) );
        $this->assertViewOk( $r );
        $this->assertCount( 1, $this->outbox() );
        $r = $this->runView( 'subscribe_infomail', array(), array( 'SubscribeInfoMailButton' => '1', 'EmailInput' => $this->newEmail( 'unknown' ), 'BackUrlInput' => '/' ) );
        $this->assertViewOk( $r, 'the page looks the same for an unknown address' );
        $this->assertCount( 1, $this->outbox(), 'and sends nothing' );
        $r = $this->runView( 'subscribe_infomail', array(), array( 'SubscribeInfoMailButton' => '1', 'EmailInput' => 'not an address' ) );
        $this->assertViewOk( $r );
    }

    public function testSubscribeWithMissingAndMalformedFieldsShowsWarnings()
    {
        $this->loginAnonymous();
        $r = $this->runView( 'subscribe', array(), array( 'SubscribeButton' => 'Subscribe' ) );
        $this->assertViewOk( $r );
        $r = $this->runView( 'subscribe', array(), array( 'SubscribeButton' => 'Subscribe', 'Subscription_Email' => 'bad', 'Subscription_IdArray' => 'text', 'Subscription_ListArray' => 'text' ) );
        $this->assertViewOk( $r );
        $this->assertCount( 0, $this->outbox() );
    }

    public function testSubscribeWithAListThatIsNotInTheIdArrayStillGetsAFormat()
    {
        $this->loginAnonymous();
        $email = $this->newEmail( 'fmt' );
        $r = $this->runView( 'subscribe', array(), array( 'SubscribeButton' => 'Subscribe', 'Subscription_Email' => $email, 'Subscription_FirstName' => 'A', 'Subscription_LastName' => 'B',
            'Subscription_ListArray' => array( self::LIST_OBJECT_ID ) ) );
        $this->assertViewOk( $r );
        $this->assertNotNull( $this->subscriptionOf( CjwNewsletterUser::fetchByEmail( $email ) ) );
    }

    public function testLoggedInUserSubscribingWithTheOwnAddressIsConfirmedAtOnce()
    {
        $this->loginAdmin();
        $email = eZUser::currentUser()->attribute( 'email' );
        $existing = CjwNewsletterUser::fetchByEmail( $email );
        if ( $existing )
            $this->markTestSkipped( 'the administrator already is a newsletter user' );
        $r = $this->runView( 'subscribe', array(), array( 'SubscribeButton' => 'Subscribe', 'Subscription_Email' => $email, 'Subscription_FirstName' => 'A', 'Subscription_LastName' => 'B',
            'Subscription_IdArray' => array( self::LIST_OBJECT_ID ), 'Subscription_ListArray' => array( self::LIST_OBJECT_ID ) ) );
        $this->assertViewOk( $r );
        $user = CjwNewsletterUser::fetchByEmail( $email );
        $this->assertNotFalse( $user );
        $this->assertTrue( $user->isConfirmed() );
        $this->assertCount( 0, $this->outbox(), 'no confirmation mail for an address that is known to be valid' );
        // not an nltest address: remove it by hand
        foreach ( CjwNewsletterSubscription::fetchSubscriptionListByNewsletterUserId( $user->attribute( 'id' ) ) as $s )
            $s->remove();
        $user->remove();
    }

    public function testConfigureOfBlacklistedOrRemovedUsersIsAnError()
    {
        $user = $this->newSubscriber( 'cfgbl' );
        $user->setBlacklisted();
        $this->assertSame( eZModule::STATUS_FAILED, $this->runView( 'configure', array( $user->attribute( 'hash' ) ) )['exit'] );
        $other = $this->newSubscriber( 'cfgrm' );
        $other->setRemoved( true );
        $this->assertSame( eZModule::STATUS_FAILED, $this->runView( 'configure', array( $other->attribute( 'hash' ) ) )['exit'] );
    }

    public function testUnsubscribeCancelRedirectsAndOtherHostsAreRefused()
    {
        $user = $this->newSubscriber( 'ucancel' );
        $hash = $this->subscriptionOf( $user )->attribute( 'hash' );
        $r = $this->runView( 'unsubscribe', array( $hash ), array( 'CancelButton' => 'Cancel', 'CancelUriInput' => '/some/page' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $this->assertStringContainsString( 'some/page', (string)$r['redirect'] );
        $r = $this->runView( 'unsubscribe', array( $hash ), array( 'CancelButton' => 'Cancel', 'CancelUriInput' => 'http://evil.example/' ) );
        $this->assertStringNotContainsString( 'evil', (string)$r['redirect'] );
        $this->assertFalse( $this->subscriptionOf( $user )->isRemoved() );
    }

    public function testUnsubscribeOfABlacklistedUserIsAnError()
    {
        $user = $this->newSubscriber( 'ubl' );
        CjwNewsletterBlacklistItem::create( $user->attribute( 'email' ), '' )->store();
        $r = $this->runView( 'unsubscribe', array( CjwNewsletterSubscription::fetchByListIdAndNewsletterUserId( self::LIST_OBJECT_ID, $user->attribute( 'id' ) )->attribute( 'hash' ) ) );
        $this->assertSame( eZModule::STATUS_FAILED, $r['exit'] );
    }

    public function testSendOfAnEditionThatIsInProcessIsRefused()
    {
        $this->newSubscriber( 'inproc' );
        $edition = $this->newEdition();
        $this->editionContent( $edition )->createNewsletterSendObject( time() + 86400 );
        $r = $this->runView( 'send', array( $edition->attribute( 'main_node_id' ) ), array( 'SendNewsletterButton' => '1', 'SendOutConfirmationInput' => '1',
            'CJWNL_datetime_year_noid' => date( 'Y' ), 'CJWNL_datetime_month_noid' => date( 'n' ), 'CJWNL_datetime_day_noid' => date( 'j' ),
            'CJWNL_datetime_hour_noid' => '1', 'CJWNL_datetime_minute_noid' => '1' ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'] );
        $this->assertCount( 1, CjwNewsletterEditionSend::fetchByEditionContentObjectId( $edition->attribute( 'id' ) ), 'no second send' );
    }

    public function testSendWithAnInvalidOrMissingDateDoesNotSend()
    {
        $edition = $this->newEdition();
        $r = $this->runView( 'send', array( $edition->attribute( 'main_node_id' ) ), array( 'SendNewsletterButton' => '1', 'SendOutConfirmationInput' => '1',
            'CJWNL_datetime_year_noid' => '2026', 'CJWNL_datetime_month_noid' => '13', 'CJWNL_datetime_day_noid' => '40', 'CJWNL_datetime_hour_noid' => '99', 'CJWNL_datetime_minute_noid' => '99' ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'] );
        $this->assertCount( 0, CjwNewsletterEditionSend::fetchByEditionContentObjectId( $edition->attribute( 'id' ) ) );
        $r = $this->runView( 'send', array( $edition->attribute( 'main_node_id' ) ), array( 'SendNewsletterButton' => '1', 'SendOutConfirmationInput' => '1' ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'], 'missing date fields are an invalid date, not a fatal' );
        $this->assertCount( 0, CjwNewsletterEditionSend::fetchByEditionContentObjectId( $edition->attribute( 'id' ) ) );
    }

    public function testSendTestMailGoesToTheGivenAddressThroughThePreviewTransport()
    {
        $edition = $this->newEdition();
        $to = $this->newEmail( 'test' );
        $r = $this->runView( 'send', array( $edition->attribute( 'main_node_id' ) ), array( 'SendNewsletterTestButton' => '1', 'EmailReseiverTestInput' => $to ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'] );
        $files = $this->outbox();
        $this->assertCount( 2, $files, 'one test mail per output format' );
        $this->assertStringContainsString( $to, $this->mailText( $files[0] ) );
        $this->assertStringContainsString( 'NLTEST', $this->mailText( $files[0] ) );
    }

    public function testSendAbortOfAFinishedOrAbortedSendOnlyWarns()
    {
        $this->newSubscriber( 'abw' );
        $edition = $this->newEdition();
        $send = $this->editionContent( $edition )->createNewsletterSendObject( time() - 60 );
        $send->setAttribute( 'status', CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED );
        $send->store();
        $r = $this->runView( 'send_abort', array( $send->attribute( 'id' ) ), array(), array( 'AbortSendOutButton' => '1' ) );
        $this->assertViewOk( $r );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );
        $r = $this->runView( 'send_abort', array( $send->attribute( 'id' ) ), array( 'CancelButton' => '1' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
    }
}
