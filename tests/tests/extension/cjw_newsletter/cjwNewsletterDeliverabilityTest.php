<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * cjw_newsletter 4.2.0, area deliverability (N1): rate limits and batches, bounces into the kernel suppression list and
 * soft-bounce retries, test groups, subscribe and unsubscribe by e-mail, the suppression import, the admin views and
 * the command.
 *
 * Live style (cjwNewsletterTestCase): the installation's own database, throwaway data on example.invalid, the file
 * transport only (the base class switches every newsletter transport to it and the test refuses to run otherwise).
 * No mailbox is read: the messages are the fixtures in fixtures/deliverability/. Skipped where there is no
 * installation (CI).
 *
 *  DL-01..05  throttle: limits only when enabled, pause always, windows, record, transport names
 *  DL-06..10  bounces: classification, hard and complaint suppress, soft retries and reopens, the limits of a retry
 *  DL-11..13  the queue: due items, batches resume, a refused mail waits for its retry
 *  DL-14..16  test groups: validation, recipients, marked mails one per address
 *  DL-17..23  mail-in: routing, double opt-in, known addresses, unsubscribe with and without proof, rejections, listener
 *  DL-24..26  suppression import: parsing, dry run, import with consent log
 *  DL-27..29  views, dashboard, command
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class cjwNewsletterDeliverabilityTest extends cjwNewsletterTestCase
{
    const TRANSPORT = 'nltest';
    /** the addresses of this test: a domain of its own, so the cleanup of another suite running at the same time never removes them */
    const OWN_DOMAIN = 'n1.example.invalid';

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        foreach ( array( 'TransportMethodCronjob', 'TransportMethodPreview', 'TransportMethodDirectly' ) as $name )
            if ( eZINI::instance( 'cjw_newsletter.ini' )->variable( 'NewsletterMailSettings', $name ) !== 'file' )
                $this->fail( 'The newsletter transport is not the file transport: the test refuses to run.' );
        if ( !class_exists( 'CjwNewsletterThrottle' ) )
            $this->markTestSkipped( 'the deliverability classes are not loaded' );
        CjwNewsletterThrottle::$now = null;
        CjwNewsletterBounce::$now = null;
        CjwNewsletterMailin::$now = null;
    }

    public function tearDown(): void
    {
        if ( class_exists( 'CjwNewsletterThrottle' ) )
        {
            CjwNewsletterThrottle::$now = null;
            CjwNewsletterBounce::$now = null;
            CjwNewsletterMailin::$now = null;
            $db = eZDB::instance();
            $db->query( "DELETE FROM cjwnl_throttle_state WHERE transport LIKE 'nltest%'" );
            foreach ( $this->createdObjectIds as $id )
                $db->query( 'DELETE FROM cjwnl_send_batch WHERE edition_send_id IN ( SELECT id FROM cjwnl_edition_send WHERE edition_contentobject_id = ' . (int)$id . ' )' );
            $db->query( "DELETE FROM cjwnl_test_group WHERE name LIKE 'NLTEST%'" );
            $db->query( "DELETE FROM cjwnl_mailin_message WHERE mailin_address_id IN ( SELECT id FROM cjwnl_mailin_address WHERE email LIKE 'nltest-%' )" );
            $db->query( "DELETE FROM cjwnl_mailin_message WHERE message_identifier LIKE '<nltest-%'" );
            $db->query( "DELETE FROM cjwnl_mailin_address WHERE email LIKE 'nltest-%'" );
            $this->removeOwnUsers();
        }
        parent::tearDown();
        if ( class_exists( 'expConsentLog' ) && class_exists( 'CjwNewsletterMailPreferences' ) && CjwNewsletterMailPreferences::available() )
            eZDB::instance()->query( "DELETE FROM expmail_consent_log WHERE email LIKE 'nltest-%@" . self::OWN_DOMAIN . "'" );
    }

    // ------------------------------------------------------------------ helpers

    /** @return string a fresh throwaway address on this test's own domain */
    protected function newEmail( $label = '' )
    {
        $email = 'nltest-n1-' . getmypid() . '-' . ( ++self::$counter ) . ( $label !== '' ? '-' . $label : '' ) . '@' . self::OWN_DOMAIN;
        $this->extraEmails[] = $email;
        return $email;
    }

    /** Removes the users of this test's own domain and what hangs on them (the base class removes only its domain). */
    private function removeOwnUsers()
    {
        $db = eZDB::instance();
        foreach ( $db->arrayQuery( "SELECT id, email FROM cjwnl_user WHERE email LIKE 'nltest-%@" . self::OWN_DOMAIN . "'" ) as $row )
        {
            $uid = (int)$row['id'];
            $db->query( 'DELETE FROM cjwnl_edition_send_item WHERE newsletter_user_id = ' . $uid );
            $db->query( 'DELETE FROM cjwnl_subscription WHERE newsletter_user_id = ' . $uid );
            $db->query( 'DELETE FROM cjwnl_user WHERE id = ' . $uid );
            $this->extraEmails[] = (string)$row['email'];
        }
        if ( CjwNewsletterMailPreferences::available() )
            foreach ( array_unique( $this->extraEmails ) as $email )
                if ( expMailSuppression::isSuppressed( $email ) )
                    expMailSuppression::lift( $email );
        $db->query( "DELETE FROM cjwnl_blacklist_item WHERE email LIKE 'nltest-%@" . self::OWN_DOMAIN . "'" );
    }

    /**
     * A run of the queue; other processes (the cron, other test runs) may hold the lock for a moment, so the run
     * is tried again for up to 60 seconds.
     */
    private function runUnlocked( $method )
    {
        $cli = new CjwNewsletterJobOutput( false );
        for ( $i = 0; $i < 120; $i++ )
        {
            $totals = CjwNewsletterRunner::$method( $cli, 'test' );
            if ( empty( $totals['locked'] ) )
                return $totals;
            usleep( 500000 );
        }
        $this->fail( "the lock of $method was held for a minute" );
    }


    private function sendItem( $id )
    {
        return eZPersistentObject::fetchObject( CjwNewsletterEditionSendItem::definition(), null, array( 'id' => (int)$id ), true );
    }
    private function process()
    {
        return $this->runUnlocked( 'queueProcess' );
    }

    private function fixture( $name, array $values )
    {
        $text = (string)file_get_contents( __DIR__ . '/fixtures/deliverability/' . $name );
        $values += array( '{{UNIQ}}' => uniqid( '', true ) );
        return strtr( $text, $values );
    }

    /** A send of a new edition with the queue made: one item per subscriber of the list. */
    private function queuedSend()
    {
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $this->runUnlocked( 'queueCreate' );
        return CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) );
    }

    private function itemOf( $send, CjwNewsletterUser $user )
    {
        foreach ( CjwNewsletterEditionSendItem::fetchListSendIdAndStatus( $send->attribute( 'id' ), false, 0, 0 ) as $item )
            if ( (int)$item->attribute( 'newsletter_user_id' ) === (int)$user->attribute( 'id' ) )
                return $item;
        $ids = array();
        foreach ( CjwNewsletterEditionSendItem::fetchListSendIdAndStatus( $send->attribute( 'id' ), false, 0, 0 ) as $item )
            $ids[] = (int)$item->attribute( 'newsletter_user_id' );
        $this->fail( 'no item for the user ' . $user->attribute( 'id' ) . ' in ' . implode( ',', $ids ) );
    }

    private function needSuppression()
    {
        if ( !CjwNewsletterMailPreferences::available() )
            $this->markTestSkipped( 'needs the e-mail preferences of Exponential 6.0.15' );
    }

    private function mailinAddress( $action = 'both', $listId = null, $tag = '' )
    {
        $address = CjwNewsletterMailinAddress::create( array() );
        $email = 'nltest-' . getmypid() . '-' . ( ++self::$counter ) . '-in@' . self::OWN_DOMAIN;
        $errors = CjwNewsletterMailin::storeAddress( $address, array( 'email' => $email, 'plus_tag' => $tag, 'action' => $action,
            'list_contentobject_id' => $listId === null ? self::LIST_OBJECT_ID : $listId, 'mailbox_id' => 0, 'is_active' => 1 ) );
        $this->assertSame( array(), $errors );
        return $address;
    }

    private function mailin( $to, $from, $subject, $body = '', $extra = '' )
    {
        return $this->fixture( 'mailin-subscribe.eml', array( '{{TO}}' => $to, '{{FROM}}' => $from, '{{SUBJECT}}' => $subject,
                                                              '{{BODY}}' => $body, '{{EXTRA}}' => $extra ) );
    }

    // ------------------------------------------------------------------ DL-01..05 throttle

    public function testThrottleLimitsApplyOnlyWhenEnabled()
    {
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'MaxPerMinute', array( self::TRANSPORT => 5 ) );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'MaxPerHour', array( self::TRANSPORT => 8 ) );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'Throttle', 'disabled' );
        $this->assertSame( 100, CjwNewsletterThrottle::acquire( self::TRANSPORT, 100 ), 'no limit while disabled' );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'Throttle', 'enabled' );
        CjwNewsletterThrottle::$now = 1790000000; // the start of a minute window and of an hour window? use whatever it is
        $this->assertSame( 5, CjwNewsletterThrottle::acquire( self::TRANSPORT, 100 ) );
        $this->assertSame( 3, CjwNewsletterThrottle::acquire( self::TRANSPORT, 3 ), 'never more than wanted' );
        $this->assertSame( 0, CjwNewsletterThrottle::acquire( self::TRANSPORT, 0 ) );
        CjwNewsletterThrottle::record( self::TRANSPORT, 4 );
        $this->assertSame( 1, CjwNewsletterThrottle::acquire( self::TRANSPORT, 100 ), 'the minute limit counts what was sent' );
        CjwNewsletterThrottle::record( self::TRANSPORT, 1 );
        $this->assertSame( 0, CjwNewsletterThrottle::acquire( self::TRANSPORT, 100 ) );
        // the next minute: the hour limit (8) is what is left (3)
        CjwNewsletterThrottle::$now = CjwNewsletterThrottle::windowStart( 'minute', 1790000000 ) + 60;
        if ( CjwNewsletterThrottle::windowStart( 'hour', CjwNewsletterThrottle::$now ) === CjwNewsletterThrottle::windowStart( 'hour', 1790000000 ) )
            $this->assertSame( 3, CjwNewsletterThrottle::acquire( self::TRANSPORT, 100 ) );
        // the next hour: everything again
        CjwNewsletterThrottle::$now = CjwNewsletterThrottle::windowStart( 'hour', 1790000000 ) + 3600;
        $this->assertSame( 5, CjwNewsletterThrottle::acquire( self::TRANSPORT, 100 ) );
        CjwNewsletterThrottle::record( self::TRANSPORT, 2 );
        $states = CjwNewsletterThrottle::states();
        $this->assertSame( 2, $states[self::TRANSPORT]['sent_minute'] );
        $this->assertSame( 2, $states[self::TRANSPORT]['sent_hour'] );
        $this->assertSame( 5, $states[self::TRANSPORT]['limit_minute'] );
    }

    public function testPauseIsRespectedEvenWhenTheLimitsAreOff()
    {
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'Throttle', 'disabled' );
        CjwNewsletterThrottle::pause( self::TRANSPORT, time() + 600 );
        $this->assertSame( 0, CjwNewsletterThrottle::acquire( self::TRANSPORT, 10 ) );
        $this->assertTrue( CjwNewsletterThrottle::states()[self::TRANSPORT]['paused'] );
        CjwNewsletterThrottle::resume( self::TRANSPORT );
        $this->assertSame( 10, CjwNewsletterThrottle::acquire( self::TRANSPORT, 10 ) );
        CjwNewsletterThrottle::pause( self::TRANSPORT, time() - 1 );
        $this->assertSame( 10, CjwNewsletterThrottle::acquire( self::TRANSPORT, 10 ), 'a pause that is over holds nothing' );
    }

    public function testTransportNames()
    {
        $this->assertSame( 'file', CjwNewsletterThrottle::name( '' ), 'empty = the cronjob transport (file in the test)' );
        $this->assertSame( 'smtp', CjwNewsletterThrottle::name( ' SMTP ' ) );
        $this->assertSame( 'smtpx', CjwNewsletterThrottle::name( 'smtp;x' ) );
        $this->assertSame( 'sms-http', CjwNewsletterThrottle::name( 'sms-http' ) );
        $this->assertSame( 200, CjwNewsletterThrottle::batchSize() );
        $this->assertSame( 0, CjwNewsletterThrottle::limit( 'nltest-none', 'minute' ) );
    }

    // ------------------------------------------------------------------ DL-06..10 bounces

    public function testClassification()
    {
        $email = $this->newEmail( 'cls' );
        $values = array( '{{EMAIL}}' => $email, '{{SENDITEM}}' => 'x', '{{USER}}' => 'y' );
        $this->assertSame( 'hard', CjwNewsletterBounce::classify( $this->fixture( 'dsn-hard.eml', $values ) )['kind'] );
        $this->assertSame( 'soft', CjwNewsletterBounce::classify( $this->fixture( 'dsn-soft.eml', $values ) )['kind'] );
        $c = CjwNewsletterBounce::classify( $this->fixture( 'arf-complaint.eml', $values ) );
        $this->assertSame( 'complaint', $c['kind'] );
        $this->assertSame( 'abuse', $c['detail'] );
        $this->assertSame( 'soft', CjwNewsletterBounce::classify( "Subject: x\n\nblocked", '550 5.7.0' )['kind'], 'a policy refusal is no dead address' );
        $this->assertSame( 'hard', CjwNewsletterBounce::classify( "Subject: x\n\nunknown", '550 5.1.1' )['kind'] );
        $this->assertSame( 'none', CjwNewsletterBounce::classify( "Subject: hello\n\nthanks", '0' )['kind'] );
        $this->assertSame( 'soft', CjwNewsletterBounce::kindOfCode( '421 Service not available' ) );
        $this->assertSame( 'soft', CjwNewsletterBounce::kindOfCode( 'RCPT TO failed with error: 450 4.1.2 Domain not found' ), 'a 450 is temporary' );
        $this->assertSame( 'hard', CjwNewsletterBounce::kindOfCode( '(#5.1.1)' ) );
        $this->assertSame( 'none', CjwNewsletterBounce::kindOfCode( '250 2.0.0 Ok' ) );
        $headers = CjwNewsletterBounce::cjwHeaders( "Subject: x\n> X-CJWNL-User: abc\nx-cjwnl-senditem: def\n" );
        $this->assertSame( array( 'x-cjwnl-user' => 'abc', 'x-cjwnl-senditem' => 'def' ), $headers );
    }

    public function testHardBounceAndComplaintSuppressTheAddress()
    {
        $this->needSuppression();
        $user = $this->newSubscriber( 'hard' );
        $send = $this->queuedSend();
        $item = $this->itemOf( $send, $user );
        $mailbox = CjwNewsletterMailboxItem::addMailboxItem( 999001, 'nltest-' . uniqid(), 1,
            $this->fixture( 'dsn-hard.eml', array( '{{EMAIL}}' => $user->attribute( 'email' ), '{{SENDITEM}}' => $item->attribute( 'hash' ), '{{USER}}' => $user->attribute( 'hash' ) ) ) );
        $mailbox->parseMail();
        @unlink( $mailbox->getFilePath() );
        $this->assertSame( 'bounce', expMailSuppression::reason( $user->attribute( 'email' ) ), 'a hard bounce suppresses the address' );
        $log = expConsentLog::fetchForRecipient( expMailRecipient::fromAddress( $user->attribute( 'email' ) ) );
        $actions = array();
        foreach ( $log as $row )
            $actions[] = $row->attribute( 'action' ) . '/' . $row->attribute( 'source' );
        $this->assertContains( 'suppress/system', $actions, 'and the consent log says so' );
        $this->assertTrue( (bool)CjwNewsletterBlacklistItem::fetchByEmail( $user->attribute( 'email' ) ), 'the listener mirrors it on the blacklist' );
        $fresh = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        $this->assertGreaterThan( 0, (int)$fresh->attribute( 'last_bounce' ) );
        $this->assertGreaterThan( 0, (int)$this->sendItem( $item->attribute( 'id' ) )->attribute( 'bounced' ) );

        $other = $this->newSubscriber( 'arf' );
        $send2 = $this->queuedSend();
        $item2 = $this->itemOf( $send2, $other );
        $r = CjwNewsletterBounce::handle( CjwNewsletterBounce::classify( $this->fixture( 'arf-complaint.eml',
            array( '{{EMAIL}}' => $other->attribute( 'email' ), '{{SENDITEM}}' => $item2->attribute( 'hash' ) ) ) ), $item2 );
        $this->assertSame( 'suppressed', $r['action'] );
        $this->assertSame( 'complaint', expMailSuppression::reason( $other->attribute( 'email' ) ) );
    }

    public function testHardBounceOnlyMarksTheUserWhenSuppressionIsOff()
    {
        $this->needSuppression();
        $this->setIni( 'cjw_newsletter.ini', 'DeliverabilitySettings', 'SuppressHardBounces', 'disabled' );
        $user = $this->newSubscriber( 'nosup' );
        $r = CjwNewsletterBounce::handle( array( 'kind' => 'hard', 'detail' => '5.1.1' ), null, $user );
        $this->assertSame( 'bounced', $r['action'] );
        $this->assertFalse( expMailSuppression::isSuppressed( $user->attribute( 'email' ) ) );
        $this->assertSame( 1, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'bounce_count' ) );
    }

    public function testSoftBounceIsSentAgainAndReopensTheSend()
    {
        $user = $this->newSubscriber( 'soft' );
        $send = $this->queuedSend();
        $this->process();
        $item = $this->itemOf( $send, $user );
        $this->assertSame( CjwNewsletterEditionSendItem::STATUS_SEND, (int)$item->attribute( 'status' ) );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );

        $mailbox = CjwNewsletterMailboxItem::addMailboxItem( 999001, 'nltest-' . uniqid(), 1,
            $this->fixture( 'dsn-soft.eml', array( '{{EMAIL}}' => $user->attribute( 'email' ), '{{SENDITEM}}' => $item->attribute( 'hash' ), '{{USER}}' => $user->attribute( 'hash' ) ) ) );
        $mailbox->parseMail();
        @unlink( $mailbox->getFilePath() );
        $item = $this->sendItem( $item->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterEditionSendItem::STATUS_NEW, (int)$item->attribute( 'status' ), 'queued again' );
        $this->assertSame( 1, (int)$item->attribute( 'retry_count' ) );
        $this->assertGreaterThan( time() + 3000, (int)$item->attribute( 'next_retry' ) );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_STARTED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ), 'the send is open again' );
        $fresh = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        $this->assertSame( 1, (int)$fresh->attribute( 'soft_bounce_count' ) );
        if ( CjwNewsletterMailPreferences::available() )
            $this->assertFalse( expMailSuppression::isSuppressed( $user->attribute( 'email' ) ), 'a soft bounce suppresses nothing' );

        // not yet due: the run leaves it
        $this->process();
        $this->assertSame( CjwNewsletterEditionSendItem::STATUS_NEW, (int)$this->sendItem( $item->attribute( 'id' ) )->attribute( 'status' ) );
        $this->assertSame( 1, CjwNewsletterBounce::waitingRetryCount( $send->attribute( 'id' ) ) );
        // due: sent again, the send finishes, and the soft-bounce count of the user is reset
        eZDB::instance()->query( 'UPDATE cjwnl_edition_send_item SET next_retry = ' . ( time() - 1 ) . ' WHERE id = ' . (int)$item->attribute( 'id' ) );
        $before = count( $this->outbox() );
        $this->process();
        $this->assertSame( CjwNewsletterEditionSendItem::STATUS_SEND, (int)$this->sendItem( $item->attribute( 'id' ) )->attribute( 'status' ) );
        $this->assertGreaterThan( $before, count( $this->outbox() ) );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );
        $this->assertSame( 0, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'soft_bounce_count' ) );
    }

    public function testRetriesHaveLimits()
    {
        $user = $this->newSubscriber( 'lim' );
        $send = $this->queuedSend();
        $item = $this->itemOf( $send, $user );
        $this->setIni( 'cjw_newsletter.ini', 'DeliverabilitySettings', 'SoftBounceMaxRetries', '1' );
        $this->assertGreaterThan( 0, CjwNewsletterBounce::scheduleRetry( $item ) );
        $this->assertSame( 0, CjwNewsletterBounce::scheduleRetry( $item ), 'no more than SoftBounceMaxRetries' );
        $this->setIni( 'cjw_newsletter.ini', 'DeliverabilitySettings', 'SoftBounceMaxRetries', '0' );
        $this->assertFalse( CjwNewsletterBounce::canRetry( $item ) );
        $this->setIni( 'cjw_newsletter.ini', 'DeliverabilitySettings', 'SoftBounceMaxRetries', '5' );
        CjwNewsletterBounce::$now = time() + 10 * 86400;
        $this->assertFalse( CjwNewsletterBounce::canRetry( $item ), 'a send older than SoftBounceRetryMaxAge days is not opened again' );
        CjwNewsletterBounce::$now = null;
        $user->setAttribute( 'status', CjwNewsletterUser::STATUS_BLACKLISTED );
        $user->store();
        $this->assertFalse( CjwNewsletterBounce::canRetry( $item, CjwNewsletterUser::fetch( $user->attribute( 'id' ) ) ), 'never to a blacklisted user' );
    }

    // ------------------------------------------------------------------ DL-11..13 the queue

    public function testBatchesKeepToTheLimitAndResume()
    {
        $a = $this->newSubscriber( 'ba' );
        $b = $this->newSubscriber( 'bb' );
        $send = $this->queuedSend();
        $send->setAttribute( 'throttle_transport', self::TRANSPORT );
        $send->store();
        $all = count( CjwNewsletterEditionSendItem::fetchListSendIdAndStatus( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW, 0, 0 ) );
        $this->assertGreaterThanOrEqual( 2, $all );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'Throttle', 'enabled' );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'BatchSize', '1' );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'PauseBetweenBatches', '0' );
        $totals = $this->process();
        $this->assertTrue( $totals['ok'] );
        $left = CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW );
        $this->assertSame( $all - 1, $left, 'one batch of one item' );
        $batches = CjwNewsletterSendBatch::fetchList( array( 'edition_send_id' => (int)$send->attribute( 'id' ) ) );
        $this->assertCount( 1, $batches );
        $this->assertSame( 1, (int)$batches[0]->attribute( 'sent_count' ) );
        $this->assertSame( CjwNewsletterDeliverability::BATCH_DONE, (int)$batches[0]->attribute( 'status' ) );
        $sentItems = CjwNewsletterEditionSendItem::fetchListSendIdAndStatus( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_SEND, 0, 0 );
        $this->assertSame( (int)$batches[0]->attribute( 'id' ), (int)$sentItems[0]->attribute( 'batch_id' ), 'the item knows its batch' );
        $this->assertSame( 1, CjwNewsletterThrottle::states()[self::TRANSPORT]['sent_minute'], 'the batch counted against the rate' );

        // the rate limit holds the rest
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'MaxPerMinute', array( self::TRANSPORT => 1 ) );
        $this->process();
        $this->assertSame( $all - 1, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW ), 'the minute is used up' );

        // a pause between batches holds the next one
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'MaxPerMinute', array() );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'PauseBetweenBatches', '3600' );
        $this->process();
        $this->assertSame( $all - 1, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW ) );

        // and a paused transport holds the send
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'PauseBetweenBatches', '0' );
        CjwNewsletterThrottle::pause( self::TRANSPORT, time() + 600 );
        $this->assertFalse( CjwNewsletterDeliverabilityHooks::sendProcessAllowed( $send ) );
        $this->process();
        $this->assertSame( $all - 1, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW ) );
        CjwNewsletterThrottle::resume( self::TRANSPORT );

        // without limits the rest goes, batch by batch in one run
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'BatchSize', '100' );
        $this->process();
        $this->assertSame( 0, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW ) );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );
    }

    public function testDueItemsLeaveOutTheWaitingRetries()
    {
        $user = $this->newSubscriber( 'due' );
        $send = $this->queuedSend();
        $item = $this->itemOf( $send, $user );
        $before = CjwNewsletterDeliverability::dueItemCount( $send->attribute( 'id' ) );
        $item->setAttribute( 'next_retry', time() + 600 );
        $item->store();
        $this->assertSame( $before - 1, CjwNewsletterDeliverability::dueItemCount( $send->attribute( 'id' ) ) );
        foreach ( CjwNewsletterDeliverability::dueItems( $send->attribute( 'id' ), 1000 ) as $due )
            $this->assertNotSame( (int)$item->attribute( 'id' ), (int)$due->attribute( 'id' ) );
    }

    public function testARefusedMailWaitsForItsRetry()
    {
        $user = $this->newSubscriber( 'refuse' );
        $send = $this->queuedSend();
        $item = $this->itemOf( $send, $user );
        $this->assertTrue( CjwNewsletterBounce::sendFailed( $item, array( 'send_result' => false, 'send_error' => 'RCPT TO failed: 451 4.7.1 Greylisted, try later' ) ) );
        $item = $this->sendItem( $item->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterEditionSendItem::STATUS_NEW, (int)$item->attribute( 'status' ) );
        $this->assertSame( 1, (int)$item->attribute( 'retry_count' ) );
        $this->assertFalse( CjwNewsletterBounce::sendFailed( $item, array( 'send_result' => false, 'send_error' => 'Could not write the mail file' ) ),
                            'an error waiting does not help is closed by the caller as before' );
        if ( CjwNewsletterMailPreferences::available() )
        {
            $this->assertTrue( CjwNewsletterBounce::sendFailed( $item, array( 'send_result' => false, 'send_error' => 'RCPT TO failed: 550 5.1.1 User unknown' ) ) );
            $this->assertSame( CjwNewsletterEditionSendItem::STATUS_ABORT, (int)$this->sendItem( $item->attribute( 'id' ) )->attribute( 'status' ) );
            $this->assertSame( 'bounce', expMailSuppression::reason( $user->attribute( 'email' ) ) );
        }
    }

    // ------------------------------------------------------------------ DL-14..16 test groups

    public function testTestGroupValidation()
    {
        $group = CjwNewsletterTestGroup::create( array() );
        $errors = CjwNewsletterTestSend::storeGroup( $group, array( 'name' => '', 'email_list' => "not-an-address\n", 'list_contentobject_id' => '0' ) );
        $this->assertArrayHasKey( 'name', $errors );
        $this->assertArrayHasKey( 'email_list', $errors );
        $this->assertSame( 0, (int)$group->attribute( 'id' ), 'nothing stored' );
        $this->setIni( 'cjw_newsletter.ini', 'TestSendSettings', 'MaxTestGroupSize', '2' );
        $errors = CjwNewsletterTestSend::storeGroup( $group, array( 'name' => 'NLTEST big', 'email_list' => "a@example.invalid\nb@example.invalid\nc@example.invalid" ) );
        $this->assertArrayHasKey( 'email_list', $errors );
        $errors = CjwNewsletterTestSend::storeGroup( $group, array( 'name' => 'NLTEST ok', 'email_list' => "A@example.invalid; b@example.invalid,a@example.invalid", 'list_contentobject_id' => '999999999' ) );
        $this->assertArrayHasKey( 'list_contentobject_id', $errors, 'not a newsletter list' );
        $errors = CjwNewsletterTestSend::storeGroup( $group, array( 'name' => 'NLTEST ok', 'email_list' => "A@example.invalid; b@example.invalid,a@example.invalid", 'list_contentobject_id' => (string)self::LIST_OBJECT_ID ) );
        $this->assertSame( array(), $errors );
        $this->assertGreaterThan( 0, (int)$group->attribute( 'id' ) );
        $this->assertSame( array( 'a@example.invalid', 'b@example.invalid' ), CjwNewsletterTestSend::groupAddresses( $group ), 'lower case, once each' );
        $fetched = CjwNewsletterDeliverabilityFetch::fetchTestGroupList( self::LIST_OBJECT_ID );
        $names = array();
        foreach ( $fetched['result'] as $row )
            $names[$row['name']] = $row['address_count'];
        $this->assertSame( 2, $names['NLTEST ok'] );
        $other = CjwNewsletterDeliverabilityFetch::fetchTestGroupList( self::LIST_OBJECT_ID + 999999 );
        foreach ( $other['result'] as $row )
            $this->assertNotSame( 'NLTEST ok', $row['name'], 'a group of a list is not offered for another list' );
    }

    public function testTestSendGoesToTheGroupMarkedAndOneMailPerAddress()
    {
        $one = $this->newEmail( 'tg1' );
        $two = $this->newEmail( 'tg2' );
        $typed = $this->newEmail( 'typed' );
        $group = CjwNewsletterTestGroup::create( array() );
        $this->assertSame( array(), CjwNewsletterTestSend::storeGroup( $group, array( 'name' => 'NLTEST group', 'email_list' => "$one\n$two", 'list_contentobject_id' => '0' ) ) );
        $edition = $this->newEdition();
        $r = $this->runView( 'send', array( $edition->attribute( 'main_node_id' ) ),
            array( 'SendNewsletterTestButton' => '1', 'EmailReseiverTestInput' => $typed, CjwNewsletterTestSend::POST_GROUP => (string)$group->attribute( 'id' ) ) );
        $this->assertSame( eZModule::STATUS_OK, $r['exit'] );
        $files = $this->outbox();
        $formats = count( $this->editionContent( $edition )->attribute( 'list_attribute_content' )->attribute( 'output_format_array' ) );
        $this->assertCount( 3 * $formats, $files, 'one mail per address and output format' );
        $seen = array();
        foreach ( $files as $file )
        {
            $text = $this->mailText( $file );
            $this->assertMatchesRegularExpression( '/^Subject: \[Test\] /mi', $text, 'the subject is marked' );
            $this->assertMatchesRegularExpression( '/^x-cjwnl-test: 1/mi', $text, 'the header is set' );
            foreach ( array( $one, $two, $typed ) as $address )
                if ( preg_match( '/^To:.*' . preg_quote( $address, '/' ) . '/mi', $text ) )
                    $seen[$address] = isset( $seen[$address] ) ? $seen[$address] + 1 : 1;
            $this->assertLessThanOrEqual( 1, preg_match_all( '/nltest-[0-9-]+-(tg1|tg2|typed)@/', preg_replace( '/\n\n.*$/s', '', $text ) ), 'no tester sees another' );
        }
        $this->assertSame( array( $typed => $formats, $one => $formats, $two => $formats ), array_merge( array( $typed => 0, $one => 0, $two => 0 ), $seen ) );
        // a group of another list is ignored
        $this->assertSame( 'x@example.invalid', CjwNewsletterTestSend::recipients( 'x@example.invalid', null, null ) );
    }

    // ------------------------------------------------------------------ DL-17..23 mail-in

    public function testSubscribeByMailStartsTheDoubleOptIn()
    {
        $this->setIni( 'cjw_newsletter.ini', 'MailInSettings', 'MailIn', 'enabled' );
        $address = $this->mailinAddress( 'both' );
        $from = $this->newEmail( 'join' );
        $raw = $this->mailin( $address->attribute( 'email' ), $from, 'Subscribe' );
        $dry = CjwNewsletterMailin::handleRawMessage( $raw, 0, true );
        $this->assertSame( 'subscribe', $dry['action'] );
        $this->assertFalse( (bool)CjwNewsletterUser::fetchByEmail( $from ), 'a dry run changes nothing' );
        $this->assertCount( 0, $this->outbox() );

        $r = CjwNewsletterMailin::handleRawMessage( $raw );
        $this->assertSame( 'pending', $r['status'] );
        $user = CjwNewsletterUser::fetchByEmail( $from );
        $this->assertInstanceOf( 'CjwNewsletterUser', $user );
        $this->assertSame( CjwNewsletterUser::STATUS_PENDING, (int)$user->attribute( 'status' ), 'nothing is confirmed by the mail itself' );
        $sub = $this->subscriptionOf( $user );
        $this->assertSame( CjwNewsletterSubscription::STATUS_PENDING, (int)$sub->attribute( 'status' ) );
        $files = $this->outbox();
        $this->assertCount( 1, $files, 'the confirmation mail' );
        $this->assertStringContainsString( 'newsletter/configure/' . $user->attribute( 'hash' ), $this->mailText( $files[0] ) );
        $row = CjwNewsletterMailinMessage::fetch( $r['message_id'] );
        $this->assertSame( CjwNewsletterMailin::STATUS_PENDING, (int)$row->attribute( 'status' ) );
        $this->assertStringNotContainsString( $from, (string)$row->attribute( 'email_from' ), 'the sender is kept masked' );
        $this->assertSame( 'duplicate', CjwNewsletterMailin::handleRawMessage( $raw )['status'], 'a message is handled once' );
    }

    public function testSubscribeOfAKnownAddressOnlySendsItsSettingsLink()
    {
        $this->setIni( 'cjw_newsletter.ini', 'MailInSettings', 'MailIn', 'enabled' );
        $address = $this->mailinAddress( 'subscribe' );
        $user = $this->newSubscriber( 'known' );
        $r = CjwNewsletterMailin::handleRawMessage( $this->mailin( $address->attribute( 'email' ), $user->attribute( 'email' ), 'hello' ) );
        $this->assertSame( 'done', $r['status'], 'already subscribed' );
        $sub = $this->subscriptionOf( $user );
        $sub->setAttribute( 'status', CjwNewsletterSubscription::STATUS_REMOVED_SELF );
        $sub->store();
        $r = CjwNewsletterMailin::handleRawMessage( $this->mailin( $address->attribute( 'email' ), $user->attribute( 'email' ), 'hello again' ) );
        $this->assertSame( 'pending', $r['status'] );
        $this->assertSame( CjwNewsletterSubscription::STATUS_REMOVED_SELF, (int)$this->subscriptionOf( $user )->attribute( 'status' ), 'the From header changes nothing' );
        $this->assertCount( 1, $this->outbox(), 'the mail with the link to the settings page' );
    }

    public function testUnsubscribeByMailNeedsProofOrALink()
    {
        $this->setIni( 'cjw_newsletter.ini', 'MailInSettings', 'MailIn', 'enabled' );
        $address = $this->mailinAddress( 'both' );
        $user = $this->newSubscriber( 'leave' );
        $r = CjwNewsletterMailin::handleRawMessage( $this->mailin( $address->attribute( 'email' ), $user->attribute( 'email' ), 'Unsubscribe' ) );
        $this->assertSame( 'unsubscribe', $r['action'] );
        $this->assertSame( 'pending', $r['status'] );
        $this->assertSame( CjwNewsletterSubscription::STATUS_APPROVED, (int)$this->subscriptionOf( $user )->attribute( 'status' ), 'From alone unsubscribes nobody' );
        $files = $this->outbox();
        $this->assertCount( 1, $files );
        $this->assertStringContainsString( 'newsletter/unsubscribe/' . $this->subscriptionOf( $user )->attribute( 'hash' ), $this->mailText( $files[0] ) );

        // a reply that quotes the newsletter's own header of the person proves the address
        $r = CjwNewsletterMailin::handleRawMessage( $this->mailin( $address->attribute( 'email' ), $user->attribute( 'email' ), 'Re: unsubscribe',
            "Please stop.\n\n> X-Cjwnl-User: " . $user->attribute( 'hash' ) ) );
        $this->assertSame( 'done', $r['status'] );
        $this->assertSame( CjwNewsletterSubscription::STATUS_REMOVED_SELF, (int)$this->subscriptionOf( $user )->attribute( 'status' ) );

        // the code of another person proves nothing
        $other = $this->newSubscriber( 'other' );
        $r = CjwNewsletterMailin::handleRawMessage( $this->mailin( $address->attribute( 'email' ), $other->attribute( 'email' ), 'unsubscribe ' . $user->attribute( 'hash' ) ) );
        $this->assertSame( 'pending', $r['status'] );
        $this->assertSame( CjwNewsletterSubscription::STATUS_APPROVED, (int)$this->subscriptionOf( $other )->attribute( 'status' ) );
        // an unknown address learns nothing
        $r = CjwNewsletterMailin::handleRawMessage( $this->mailin( $address->attribute( 'email' ), $this->newEmail( 'nobody' ), 'unsubscribe' ) );
        $this->assertSame( 'done', $r['status'] );
    }

    public function testMailInRejectsWhatItMustNotActOn()
    {
        $address = $this->mailinAddress( 'both' );
        $from = $this->newEmail( 'rej' );
        $this->assertNull( CjwNewsletterMailin::handleRawMessage( $this->mailin( $address->attribute( 'email' ), $from, 'subscribe' ) ), 'mail-in off: nothing' );
        $this->setIni( 'cjw_newsletter.ini', 'MailInSettings', 'MailIn', 'enabled' );
        $this->assertNull( CjwNewsletterMailin::handleRawMessage( $this->mailin( 'nltest-elsewhere@' . self::OWN_DOMAIN, $from, 'subscribe' ) ), 'not a mail-in address' );
        $cases = array(
            array( 'hello there', '', 'No subscribe or unsubscribe' ),
            array( 'subscribe or unsubscribe', '', 'No subscribe or unsubscribe' ),
            array( 'subscribe', "\nAuto-Submitted: auto-replied", 'automatic' ),
            array( 'subscribe', "\nPrecedence: bulk", 'automatic' ) );
        foreach ( $cases as $case )
        {
            $r = CjwNewsletterMailin::handleRawMessage( $this->mailin( $address->attribute( 'email' ), $from, $case[0], '', $case[1] ) );
            $this->assertSame( 'rejected', $r['status'], $case[0] . $case[1] );
            $this->assertStringContainsString( $case[2], $r['note'] );
        }
        $r = CjwNewsletterMailin::handleRawMessage( $this->mailin( $address->attribute( 'email' ), 'mailer-daemon@' . self::OWN_DOMAIN, 'subscribe' ) );
        $this->assertSame( 'rejected', $r['status'], 'an ignored sender' );
        if ( CjwNewsletterMailPreferences::available() )
        {
            $sup = $this->newEmail( 'sup' );
            expMailSuppression::add( $sup, 'legal', 'test' );
            $r = CjwNewsletterMailin::handleRawMessage( $this->mailin( $address->attribute( 'email' ), $sup, 'subscribe' ) );
            $this->assertSame( 'rejected', $r['status'] );
            $this->assertFalse( (bool)CjwNewsletterUser::fetchByEmail( $sup ), 'a suppressed address gets no confirmation mail' );
        }
        $this->setIni( 'cjw_newsletter.ini', 'MailInSettings', 'MaxRequestsPerSenderPerDay', '1' );
        $many = $this->newEmail( 'many' );
        CjwNewsletterMailin::handleRawMessage( $this->mailin( $address->attribute( 'email' ), $many, 'subscribe' ) );
        $r = CjwNewsletterMailin::handleRawMessage( $this->mailin( $address->attribute( 'email' ), $many, 'subscribe' ) );
        $this->assertSame( 'rejected', $r['status'] );
        $this->assertStringContainsString( 'Too many', $r['note'] );
        $this->mailinAddress( 'unsubscribe', 0 );
        $errors = CjwNewsletterMailin::storeAddress( CjwNewsletterMailinAddress::create( array() ),
            array( 'email' => 'nltest-x@' . self::OWN_DOMAIN, 'action' => 'subscribe', 'list_contentobject_id' => 0 ) );
        $this->assertArrayHasKey( 'list_contentobject_id', $errors, 'an address of every list for subscribe is refused' );
    }

    public function testPlusAddressingAndTheKernelListener()
    {
        $this->setIni( 'cjw_newsletter.ini', 'MailInSettings', 'MailIn', 'enabled' );
        $this->setIni( 'cjw_newsletter.ini', 'MailInSettings', 'PlusAddressing', 'enabled' );
        $address = $this->mailinAddress( 'both' );
        $email = (string)$address->attribute( 'email' );
        $plus = str_replace( '@', '+subscribe@', $email );
        $from = $this->newEmail( 'plus' );
        $route = CjwNewsletterMailin::route( array( $plus ) );
        $this->assertSame( (int)$address->attribute( 'id' ), (int)$route['address']->attribute( 'id' ) );
        $this->assertSame( 'subscribe', $route['tag'] );
        $this->setIni( 'cjw_newsletter.ini', 'MailInSettings', 'PlusAddressing', 'disabled' );
        $this->assertNull( CjwNewsletterMailin::route( array( $plus ) ), 'off: the plus-address is another address' );
        $this->setIni( 'cjw_newsletter.ini', 'MailInSettings', 'PlusAddressing', 'enabled' );
        // through the kernel reader's listener, with a dry run first
        $raw = $this->mailin( $plus, $from, 'Hello', 'Please add me' );
        $this->assertTrue( CjwNewsletterMailin::mailMessage( $raw, array( 'kind' => 'none', 'addresses' => array(), 'detail' => '' ), true ) );
        $this->assertFalse( (bool)CjwNewsletterUser::fetchByEmail( $from ) );
        if ( class_exists( 'expMailBounceReader' ) )
        {
            $this->setIni( 'mailpreferences.ini', 'BounceSettings', 'MessageListeners', array( 'CjwNewsletterMailin' ) );
            $file = 'var/tmp/nltest-mailin-' . getmypid() . '.eml';
            file_put_contents( $file, $raw );
            $run = expMailBounceReader::runFiles( array( $file ), false );
            unlink( $file );
            $this->assertTrue( $run['items'][0]['handled'], 'the reader marks the message as taken' );
            $this->assertSame( CjwNewsletterSubscription::STATUS_PENDING, (int)$this->subscriptionOf( CjwNewsletterUser::fetchByEmail( $from ) )->attribute( 'status' ) );
        }
        $this->assertFalse( CjwNewsletterMailin::mailMessage( "Subject: x\n\nhi", array( 'kind' => 'hard', 'addresses' => array(), 'detail' => '5.1.1' ), true ),
                            'a bounce of another mail is not the newsletter\'s' );
        $this->assertSame( 'news+weekly@example.invalid', CjwNewsletterMailin::displayAddress( CjwNewsletterMailinAddress::create( array( 'email' => 'news@example.invalid', 'plus_tag' => 'weekly' ) ) ) );
        $this->assertSame( 'unsubscribe', CjwNewsletterMailin::keywordIn( 'Bitte abmelden' ) );
        $this->assertSame( '', CjwNewsletterMailin::keywordIn( 'subscriber news' ), 'whole words only' );
    }

    // ------------------------------------------------------------------ DL-24..26 suppression import

    public function testSuppressionImportParsing()
    {
        $p = CjwNewsletterSuppressionImport::parse( "\xEF\xBB\xBFName;E-Mail;City\nA;A@example.invalid;X\nB;not-an-address;Y\nC;a@example.invalid;Z\n\nD;d@example.invalid;W\n" );
        $this->assertSame( 'E-Mail', $p['column'] );
        $this->assertSame( 4, $p['rows'] );
        $this->assertSame( array( 'a@example.invalid', 'd@example.invalid' ), $p['emails'] );
        $this->assertSame( 1, $p['invalid_count'] );
        $this->assertSame( 3, $p['invalid'][0]['line'] );
        $this->assertSame( 1, $p['duplicates'] );
        $p = CjwNewsletterSuppressionImport::parse( "x,b@example.invalid\ny,c@example.invalid\n" );
        $this->assertSame( array( 'b@example.invalid', 'c@example.invalid' ), $p['emails'], 'no header: the first column with an address' );
        $this->setIni( 'cjw_newsletter.ini', 'DeliverabilitySettings', 'SuppressionImportMaxRows', '1' );
        $this->assertTrue( CjwNewsletterSuppressionImport::parse( "a@example.invalid\nb@example.invalid\n" )['too_many'] );
    }

    public function testSuppressionImportDryRunAndImport()
    {
        $this->needSuppression();
        $a = $this->newEmail( 'imp-a' );
        $b = $this->newEmail( 'imp-b' );
        $csv = "email\n$a\n$b\nbroken\n";
        $bad = CjwNewsletterSuppressionImport::import( $csv, 'bridge' );
        $this->assertFalse( $bad['ok'], 'the reason bridge belongs to the blacklist' );
        $dry = CjwNewsletterSuppressionImport::import( $csv, 'legal', '', true, 'list.csv' );
        $this->assertTrue( $dry['ok'] );
        $this->assertSame( 2, $dry['added'] );
        $this->assertFalse( expMailSuppression::isSuppressed( $a ), 'a dry run changes nothing' );
        expMailSuppression::add( $b, 'admin', 'before' );
        $r = CjwNewsletterSuppressionImport::import( $csv, 'legal', "Request $a", false, 'list.csv' );
        $this->assertTrue( $r['ok'] );
        $this->assertSame( 1, $r['added'] );
        $this->assertSame( 1, $r['already'] );
        $this->assertSame( 1, $r['invalid_count'] );
        $this->assertSame( 'legal', expMailSuppression::reason( $a ) );
        $this->assertSame( 'admin', expMailSuppression::reason( $b ), 'an existing entry keeps its reason' );
        $row = expMailSuppression::fetchByHash( expMailSuppression::hash( $a ) );
        $this->assertStringNotContainsString( $a, (string)$row->attribute( 'note' ), 'no address in the note' );
        $log = expConsentLog::fetchForRecipient( expMailRecipient::fromAddress( $a ) );
        $this->assertSame( 'suppress', $log[0]->attribute( 'action' ) );
        $this->assertSame( 'import', $log[0]->attribute( 'source' ) );
        $this->assertTrue( (bool)CjwNewsletterBlacklistItem::fetchByEmail( $a ), 'mirrored on the blacklist' );
        $this->assertFalse( CjwNewsletterSuppressionImport::import( '', 'legal' )['ok'] );
    }

    // ------------------------------------------------------------------ DL-27..29 views, dashboard, command

    public function testAdminViews()
    {
        foreach ( array( 'test_group_list', 'mailin_address_list', 'throttle', 'suppression_import' ) as $view )
            $this->assertViewOk( $this->runView( $view ), $view );
        $r = $this->runView( 'test_group_edit', array( 0 ), array( 'StoreButton' => '1', 'name' => 'NLTEST view', 'email_list' => $this->newEmail( 'view' ), 'list_contentobject_id' => '0' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $groups = CjwNewsletterTestGroup::fetchList( array( 'name' => 'NLTEST view' ) );
        $this->assertCount( 1, $groups );
        $r = $this->runView( 'test_group_edit', array( $groups[0]->attribute( 'id' ) ), array( 'StoreButton' => '1', 'name' => '', 'email_list' => '' ) );
        $this->assertViewOk( $r, 'errors are shown' );
        $this->assertStringContainsString( 'nl-field-error', $r['content'] );
        $r = $this->runView( 'test_group_edit', array( $groups[0]->attribute( 'id' ) ), array( 'RemoveButton' => '1' ) );
        $this->assertStringContainsString( 'ConfirmRemoveButton', $r['content'] );
        $this->runView( 'test_group_edit', array( $groups[0]->attribute( 'id' ) ), array( 'ConfirmRemoveButton' => '1' ) );
        $this->assertCount( 0, CjwNewsletterTestGroup::fetchList( array( 'name' => 'NLTEST view' ) ) );
        $this->assertViewClean( $this->runView( 'test_group_edit', array( 999999999 ) ), 'an unknown group' );

        $email = 'nltest-' . getmypid() . '-view@' . self::OWN_DOMAIN;
        $r = $this->runView( 'mailin_address_edit', array( 0 ), array( 'StoreButton' => '1', 'email' => $email, 'plus_tag' => '', 'action' => 'both',
                                                                         'list_contentobject_id' => (string)self::LIST_OBJECT_ID, 'mailbox_id' => '0', 'is_active' => '1' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $this->assertNotNull( CjwNewsletterMailinAddress::fetchByEmailAndPlusTag( $email, '' ) );
        $list = $this->runView( 'mailin_address_list' );
        $this->assertStringContainsString( $email, $list['content'] );

        $r = $this->runView( 'throttle', array(), array( 'PauseButton' => '1', 'Transport' => self::TRANSPORT, 'Minutes' => '5' ) );
        $this->assertSame( eZModule::STATUS_REDIRECT, $r['exit'] );
        $this->assertGreaterThan( time(), CjwNewsletterThrottle::pausedUntil( self::TRANSPORT ) );
        $this->assertStringContainsString( self::TRANSPORT, $this->runView( 'throttle' )['content'] );
        $this->runView( 'throttle', array(), array( 'ResumeButton' => '1', 'Transport' => self::TRANSPORT ) );
        $this->assertSame( 0, CjwNewsletterThrottle::pausedUntil( self::TRANSPORT ) );

        if ( CjwNewsletterMailPreferences::available() )
        {
            $s = $this->newEmail( 'viewsup' );
            $r = $this->runView( 'suppression_import', array(), array( 'CheckButton' => '1', 'SuppressionText' => $s, 'Reason' => 'legal' ) );
            $this->assertStringContainsString( 'message-warning', $r['content'] );
            $this->assertFalse( expMailSuppression::isSuppressed( $s ) );
            $this->runView( 'suppression_import', array(), array( 'ImportButton' => '1', 'SuppressionText' => $s, 'Reason' => 'legal' ) );
            $this->assertSame( 'legal', expMailSuppression::reason( $s ) );
        }
    }

    public function testDashboardShowsTheArea()
    {
        $summary = CjwNewsletterDashboard::summary();
        $this->assertArrayHasKey( 'CjwNewsletterDeliverabilityHooks', $summary['areas'] );
        $area = $summary['areas']['CjwNewsletterDeliverabilityHooks'];
        foreach ( array( 'throttle', 'transports', 'retries_waiting', 'suppressed', 'test_groups', 'mailin' ) as $key )
            $this->assertArrayHasKey( $key, $area );
        CjwNewsletterThrottle::pause( self::TRANSPORT, time() + 300 );
        $codes = array();
        foreach ( CjwNewsletterDashboard::summary()['problems'] as $problem )
            $codes[] = $problem['code'];
        $this->assertContains( 'transport_paused', $codes, 'a paused transport is a problem on the dashboard' );
        $r = $this->runView( 'index' );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'nl-area-deliverability', $r['content'] );
        $this->assertStringContainsString( 'newsletter/throttle', $r['content'] );
    }

    public function testCommand()
    {
        $php = PHP_BINARY;
        exec( escapeshellarg( $php ) . ' extension/cjw_newsletter/bin/php/deliverability.php status --allow-root-user 2>&1', $out, $code );
        $this->assertSame( 0, $code, implode( "\n", $out ) );
        $this->assertStringContainsString( 'PASS', implode( "\n", $out ) );
        $out = array();
        exec( escapeshellarg( $php ) . ' extension/cjw_newsletter/bin/php/deliverability.php pause --transport=' . self::TRANSPORT . ' --minutes=5 --dry-run --allow-root-user 2>&1', $out, $code );
        $this->assertSame( 0, $code, implode( "\n", $out ) );
        $this->assertStringContainsString( 'Would pause', implode( "\n", $out ) );
        $this->assertSame( 0, CjwNewsletterThrottle::pausedUntil( self::TRANSPORT ), 'a dry run pauses nothing' );
        $out = array();
        exec( escapeshellarg( $php ) . ' extension/cjw_newsletter/bin/php/deliverability.php nonsense --allow-root-user 2>&1', $out, $code );
        $this->assertSame( 1, $code );
        $out = array();
        $file = 'var/tmp/nltest-cmd-' . getmypid() . '.csv';
        file_put_contents( $file, "email\n" . $this->newEmail( 'cmd' ) . "\n" );
        exec( escapeshellarg( $php ) . ' extension/cjw_newsletter/bin/php/deliverability.php suppression-import --file=' . escapeshellarg( $file ) . ' --reason=legal --dry-run --allow-root-user 2>&1', $out, $code );
        unlink( $file );
        if ( CjwNewsletterMailPreferences::available() )
        {
            $this->assertSame( 0, $code, implode( "\n", $out ) );
            $this->assertStringContainsString( '1 would be suppressed', implode( "\n", $out ) );
        }
    }
}
