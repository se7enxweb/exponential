<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * The newsletter from subscription to unsubscription through the simulated (file) transport:
 * subscribe, confirm, configure, edition, preview, send, cronjobs, outbox, unsubscribe, abort, archive.
 */
class cjwNewsletterFlowTest extends cjwNewsletterTestCase
{
    private function subscribePost( $email )
    {
        return array(
            'SubscribeButton' => 'Subscribe',
            'Subscription_Email' => $email, 'Subscription_FirstName' => 'Ann', 'Subscription_LastName' => 'Tester',
            'Subscription_IdArray' => array( self::LIST_OBJECT_ID ), 'Subscription_ListArray' => array( self::LIST_OBJECT_ID ),
            'Subscription_OutputFormatArray_' . self::LIST_OBJECT_ID => array( 0 ) );
    }

    public function testSubscribeSendsConfirmationAndConfirmLinkWorks()
    {
        $email = $this->newEmail( 'sub' );
        $this->loginAnonymous();
        $r = $this->runView( 'subscribe', array(), $this->subscribePost( $email ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'] );
        $user = CjwNewsletterUser::fetchByEmail( $email );
        $this->assertInstanceOf( 'CjwNewsletterUser', $user );
        $this->assertSame( CjwNewsletterUser::STATUS_PENDING, (int)$user->attribute( 'status' ) );

        $files = $this->outbox();
        $this->assertCount( 1, $files, 'one confirmation mail' );
        $mail = $this->mailText( $files[0] );
        $this->assertStringContainsString( $email, $mail );
        $hashes = $this->linkHashes( $mail, '/newsletter/configure' );
        $this->assertSame( array( $user->attribute( 'hash' ) ), array_values( array_unique( $hashes ) ), 'the link carries the user hash' );

        $r = $this->runView( 'configure', array( $hashes[0] ) );
        $this->assertViewOk( $r, 'configure' );
        $user = CjwNewsletterUser::fetchByEmail( $email );
        $this->assertTrue( (bool)$user->attribute( 'is_confirmed' ), 'following the link confirms the user' );
        $this->assertContains( (int)$this->subscriptionOf( $user )->attribute( 'status' ),
            array( CjwNewsletterSubscription::STATUS_CONFIRMED, CjwNewsletterSubscription::STATUS_APPROVED ) );
    }

    public function testSubscribingAnExistingAddressSendsTheInformationMail()
    {
        $user = $this->newSubscriber( 'exists' );
        $this->loginAnonymous();
        $r = $this->runView( 'subscribe', array(), $this->subscribePost( $user->attribute( 'email' ) ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'] );
        $files = $this->outbox();
        $this->assertCount( 1, $files );
        $this->assertStringContainsString( $user->attribute( 'hash' ), $this->mailText( $files[0] ) );
    }

    public function testConfigureStoresNameAndRemovesSubscription()
    {
        $user = $this->newSubscriber( 'cfg' );
        $r = $this->runView( 'configure', array( $user->attribute( 'hash' ) ), array(
            'ConfirmButton' => 'Confirm', 'Subscription_FirstName' => 'Changed', 'Subscription_LastName' => 'Name',
            'Subscription_IdArray' => array( self::LIST_OBJECT_ID ), 'Subscription_ListArray' => array() ) );
        $this->assertViewOk( $r );
        $user = CjwNewsletterUser::fetchByEmail( $user->attribute( 'email' ) );
        $this->assertSame( 'Changed', $user->attribute( 'first_name' ) );
        $sub = $this->subscriptionOf( $user );
        $this->assertTrue( (bool)$sub->attribute( 'is_removed' ), 'unticking the list removes the subscription' );
    }

    public function testPreviewRendersEveryOutputFormat()
    {
        $edition = $this->newEdition();
        foreach ( array( 0, 1 ) as $format )
        {
            $r = $this->runView( 'preview', array( $edition->attribute( 'id' ), 1, $format, 'site', 'default' ), array(), array( 'Debug' => '1' ) );
            $this->assertViewOk( $r, "preview format $format" );
            $this->assertStringContainsString( 'NLTEST', $r['content'] );
        }
    }

    public function testSendCronjobsWriteOneMailPerSubscriberAndOutputFormat()
    {
        $a = $this->newSubscriber( 'a' );
        $b = $this->newSubscriber( 'b', null, null, array( 0, 1 ) );
        $edition = $this->newEdition();
        $nodeId = (int)$edition->attribute( 'main_node_id' );

        $r = $this->runView( 'send', array( $nodeId ), array(
            'SendNewsletterButton' => 'Send', 'SendOutConfirmationInput' => '1',
            'CJWNL_datetime_year_noid' => date( 'Y', time() - 3600 ), 'CJWNL_datetime_month_noid' => date( 'n', time() - 3600 ),
            'CJWNL_datetime_day_noid' => date( 'j', time() - 3600 ), 'CJWNL_datetime_hour_noid' => date( 'G', time() - 3600 ),
            'CJWNL_datetime_minute_noid' => date( 'i', time() - 3600 ) ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r["exit"], "a successful send redirects to the edition" );
        $sends = CjwNewsletterEditionSend::fetchByEditionContentObjectId( $edition->attribute( 'id' ) );
        $this->assertCount( 1, $sends, 'the send button created one edition send' );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_WAIT_FOR_SCHEDULE, (int)$sends[0]->attribute( 'status' ) );

        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->runCronjob( 'cjw_newsletter_mailqueue_process' );

        $send = CjwNewsletterEditionSend::fetch( $sends[0]->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED, (int)$send->attribute( 'status' ) );
        $files = $this->outbox();
        $mine = array();
        foreach ( $files as $file )
        {
            $text = $this->mailText( $file );
            foreach ( array( $a, $b ) as $u )
                if ( strpos( $text, $u->attribute( 'email' ) ) !== false )
                    $mine[$u->attribute( 'email' )][] = $text;
        }
        $this->assertCount( 1, $mine[$a->attribute( 'email' )], 'one mail for the one-format subscriber' );
        $this->assertCount( 2, $mine[$b->attribute( 'email' )], 'two mails for the two-format subscriber' );
        $text = $mine[$a->attribute( 'email' )][0];
        $this->assertStringContainsString( 'NLTEST', $text );
        $this->assertStringNotContainsString( '#_hash_unsubscribe_#', $text, 'the placeholders are replaced' );
        $this->assertStringNotContainsString( '#_hash_configure_#', $text );
        $subHash = $this->subscriptionOf( $a )->attribute( 'hash' );
        $this->assertContains( $subHash, $this->linkHashes( $text, '/newsletter/unsubscribe' ), 'the unsubscribe link carries the subscription hash' );

        // a second run sends nothing more
        $count = count( $this->outbox() );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        $this->assertCount( $count, $this->outbox() );

        // the unsubscribe link works
        $r = $this->runView( 'unsubscribe', array( $subHash ) );
        $this->assertViewOk( $r, 'unsubscribe form' );
        $r = $this->runView( 'unsubscribe', array( $subHash ), array( 'SubscribeButton' => 'Unsubscribe' ) );
        $this->assertViewOk( $r, 'unsubscribe' );
        $this->assertTrue( (bool)$this->subscriptionOf( $a )->attribute( 'is_removed' ) );
        $r = $this->runView( 'unsubscribe', array( $subHash ) );
        $this->assertStringContainsString( '', (string)$r['content'] );
        $this->assertViewOk( $r, 'unsubscribe again' );
    }

    public function testScheduledSendWaitsUntilItsTime()
    {
        $this->newSubscriber( 'later' );
        $edition = $this->newEdition();
        $content = $this->editionContent( $edition );
        $send = $content->createNewsletterSendObject( time() + 86400 );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        $send = CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_WAIT_FOR_SCHEDULE, (int)$send->attribute( 'status' ) );
        $this->assertCount( 0, $this->outbox() );
    }

    public function testAbortStopsASendInProgress()
    {
        $user = $this->newSubscriber( 'abort' );
        $edition = $this->newEdition();
        $content = $this->editionContent( $edition );
        $send = $content->createNewsletterSendObject( time() - 60 );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $send = CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_CREATED, (int)$send->attribute( 'status' ) );

        $nodeId = (int)$edition->attribute( 'main_node_id' );
        $r = $this->runView( 'send_abort', array( $send->attribute( 'id' ) ) );
        $this->assertViewOk( $r, 'send_abort form' );
        $r = $this->runView( 'send_abort', array( $send->attribute( 'id' ) ), array( 'AbortSendOutButton' => 'Abort' ) );
        $send = CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_ABORT, (int)$send->attribute( 'status' ) );
        $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        $this->assertCount( 0, $this->outbox(), 'an aborted send writes nothing' );
    }

    public function testArchiveListsASentEdition()
    {
        $this->newSubscriber( 'arch' );
        $edition = $this->newEdition();
        $content = $this->editionContent( $edition );
        $send = $content->createNewsletterSendObject( time() - 60 );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        $this->runCronjob( 'cjw_newsletter_mailqueue_process' );
        $content = $this->editionContent( $edition );
        $this->assertTrue( (bool)$content->attribute( 'is_archive' ) );
        $r = $this->runView( 'archive', array( $send->attribute( 'hash' ), 0 ), array(), array( 'Debug' => '1' ) );
        $this->assertViewOk( $r, 'archive' );
        $r = $this->runView( 'preview_archive', array( $send->attribute( 'id' ), 0, 0 ), array(), array( 'Debug' => '1' ) );
        $this->assertViewClean( $r, 'preview_archive' );
    }
}
