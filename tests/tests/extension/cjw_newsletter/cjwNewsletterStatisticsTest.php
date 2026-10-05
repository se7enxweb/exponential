<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';
/**
 * cjw_newsletter 4.2.0, area N4 Statistics: open pixel and click redirect, consent per person, anonymous totals,
 * retention, erasure, the A/B subject test, reports, the CSV export and the parts of the forms.
 *
 * Live style (cjwNewsletterTestCase): the installation's own database, throwaway subscribers n4test-*@example.invalid,
 * the file transport. The pixel and the redirect are also called over HTTP on the site (Apache), with the tokens of
 * the mails a test send wrote; these tests are skipped when the site cannot be reached or its tracking switch is off.
 * Everything a test made (links, clicks, opens, totals, A/B rows, consent rows of the test addresses) is removed in
 * tearDown.
 *
 *  ST-01  Links are rewritten, mailto:, anchors and personal links are not; the pixel is added; links are stored
 *  ST-02  A recipient without consent gets an anonymous key, one with consent a personal key
 *  ST-03  Over HTTP: the pixel is a no-store GIF and counts; the redirect goes to the stored URL and counts
 *  ST-04  Over HTTP: a bad signature or a key of another send answers 404 (pixel: the GIF) and counts nothing
 *  ST-05  In-process: per person only with consent; consent withdrawn later counts anonymously
 *  ST-06  Withdrawal and erasure remove the per-person rows at once, the totals stay
 *  ST-07  Retention: rows older than PersonRetentionMonths go (ext:cjw_newsletter:statistics --cleanup, --at, --dry-run)
 *  ST-08  A/B: samples first, the rest waits, winner by open rate, the rest gets the winner's subject
 *  ST-09  A/B: winner by clicks when only anonymous totals are kept
 *  ST-10  Form validation, the send form and the list attribute store the tracking mode
 *  ST-11  Reports, the dashboard block, the article box and the CSV export (no address, formulas defused)
 *  ST-12  The consent category: optional, off by default, its part on the preference page
 *  ST-13  SMS sends and sends with the site switch off are not tracked
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class cjwNewsletterStatisticsTest extends cjwNewsletterTestCase
{
    const LINK = 'https://www.example.invalid/nltest/article?a=1&b=2';
    const LINK2 = 'https://www.example.invalid/nltest/other';

    /** @var string[] addresses whose consent rows are removed in tearDown */
    protected $consentEmails = array();

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( !class_exists( 'CjwNewsletterTracking' ) )
            $this->markTestSkipped( 'the statistics classes are not loaded' );
        $this->setIni( 'cjw_newsletter.ini', 'TrackingSettings', 'Tracking', 'enabled' );
        CjwNewsletterTracking::resetCache();
        CjwNewsletterAbTester::resetCache();
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            $db = eZDB::instance();
            foreach ( $this->createdObjectIds as $id )
                foreach ( $db->arrayQuery( 'SELECT id FROM cjwnl_edition_send WHERE edition_contentobject_id = ' . (int)$id ) as $row )
                    $this->removeStatisticsOfSend( (int)$row['id'] );
            foreach ( array_unique( $this->consentEmails ) as $email )
            {
                $r = expMailRecipient::fromAddress( $email, false );
                $key = $r->key();
                $anon = 'x:' . substr( hash( 'sha256', $key . expMailSecret::derive( 'anonymise' ) ), 0, 32 );
                foreach ( array( $key, $anon ) as $k )
                {
                    $k = $db->escapeString( $k );
                    $db->query( "DELETE FROM expmail_preference WHERE recipient_key = '$k'" );
                    $db->query( "DELETE FROM expmail_pending WHERE recipient_key = '$k'" );
                    $db->query( "DELETE FROM expmail_consent_log WHERE recipient_key = '$k'" );
                }
            }
            $this->consentEmails = array();
            $this->removeOwnSubscribers();
        }
        parent::tearDown();
    }

    protected function removeStatisticsOfSend( $sendId )
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM cjwnl_link_click WHERE link_id IN ( SELECT id FROM cjwnl_link WHERE edition_send_id = $sendId )" );
        $db->query( "DELETE FROM cjwnl_link WHERE edition_send_id = $sendId" );
        $db->query( "DELETE FROM cjwnl_open WHERE edition_send_id = $sendId" );
        $db->query( "DELETE FROM cjwnl_stat_total WHERE edition_send_id = $sendId" );
        $db->query( "DELETE FROM cjwnl_ab_variant WHERE ab_test_id IN ( SELECT id FROM cjwnl_ab_test WHERE edition_send_id = $sendId )" );
        $db->query( "DELETE FROM cjwnl_ab_test WHERE edition_send_id = $sendId" );
        if ( class_exists( 'CjwNewsletterEditionSendOutput' ) )
            $db->query( "DELETE FROM cjwnl_edition_send_output WHERE edition_send_id = $sendId" );
    }

    // ------------------------------------------------------------------ helpers

    /** @return string a throwaway address of this test class (not nltest-*: other test runs remove those in their tearDown) */
    protected function newEmail( $label = '' )
    {
        $email = 'n4test-' . getmypid() . '-' . ( ++self::$counter ) . ( $label !== '' ? '-' . $label : '' ) . '@' . self::MAIL_DOMAIN;
        $this->extraEmails[] = $email;
        return $email;
    }

    /** Removes the subscribers of this run (n4test-<pid>-*). */
    protected function removeOwnSubscribers()
    {
        $db = eZDB::instance();
        foreach ( $db->arrayQuery( "SELECT id FROM cjwnl_user WHERE email LIKE 'n4test-" . getmypid() . "-%@" . self::MAIL_DOMAIN . "'" ) as $row )
        {
            $uid = (int)$row['id'];
            $db->query( "DELETE FROM cjwnl_link_click WHERE edition_send_item_id IN ( SELECT id FROM cjwnl_edition_send_item WHERE newsletter_user_id = $uid )" );
            $db->query( "DELETE FROM cjwnl_open WHERE edition_send_item_id IN ( SELECT id FROM cjwnl_edition_send_item WHERE newsletter_user_id = $uid )" );
            $db->query( 'DELETE FROM cjwnl_edition_send_item WHERE newsletter_user_id = ' . $uid );
            $db->query( 'DELETE FROM cjwnl_subscription WHERE newsletter_user_id = ' . $uid );
            $db->query( 'DELETE FROM cjwnl_user WHERE id = ' . $uid );
        }
    }

    /** Switches the statistics consent of a subscriber on or off. */
    protected function consent( CjwNewsletterUser $user, $on )
    {
        $this->consentEmails[] = (string)$user->attribute( 'email' );
        $r = CjwNewsletterMailPreferences::recipientForNewsletterUser( $user );
        expMailPreferences::forRecipient( $r )->set( CjwNewsletterTracking::consentCategory(), $on, expConsentContext::system( 'NLTEST statistics consent', 'page' ) );
        CjwNewsletterTracking::resetCache();
    }

    /** Puts links into the HTML and text parts of the send's output (an edition of the test has none of its own). */
    protected function injectLinks( CjwNewsletterEditionSend $send )
    {
        $doc = new DOMDocument();
        $doc->loadXML( $send->attribute( 'output_xml' ) );
        foreach ( $doc->getElementsByTagName( 'type' ) as $type )
        {
            $body = $type->nodeValue;
            if ( $type->getAttribute( 'name' ) === 'html' )
                $body = str_ireplace( '</body>', '<p><a href="' . htmlspecialchars( self::LINK ) . '">Read</a> <a href="' . self::LINK2 . '">Other</a> '
                    . '<a href="mailto:nltest@example.invalid">Mail</a> <a href="#top">Top</a> <a href="https://www.example.invalid/newsletter/unsubscribe/#_hash_unsubscribe_#">Unsub</a></p></body>', $body );
            else
                $body .= "\nRead: " . self::LINK2 . ".\n";
            while ( $type->firstChild )
                $type->removeChild( $type->firstChild );
            $type->appendChild( $doc->createCDATASection( $body ) );
        }
        $send->setAttribute( 'output_xml', $doc->saveXML() );
        $send->store();
    }

    /**
     * A send of a new edition to the subscribers, with links, the tracking mode and (optionally) an A/B test, queued.
     *
     * @return CjwNewsletterEditionSend
     */
    protected function trackedSend( $mode, $ab = null )
    {
        // made for tomorrow, so that a queue run of another process does not take it before it is set up
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() + 86400 );
        $send->setAttribute( 'tracking_mode', $mode );
        $send->store();
        $this->injectLinks( $send );
        if ( $ab !== null )
            CjwNewsletterAbTester::create( $send, $ab['subjects'], $ab['percent'], $ab['criterion'], $ab['hours'] );
        $send = CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) );
        $send->setAttribute( 'mailqueue_process_scheduled', time() - 5 );
        $send->store();
        $this->runLocked( 'queueCreate' );
        return CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) );
    }

    protected function process()
    {
        CjwNewsletterTracking::resetCache();
        CjwNewsletterAbTester::resetCache();
        return $this->runLocked( 'queueProcess' );
    }

    /** Runs a step of the runner; while another process (another test run) holds its lock, waits and tries again. */
    protected function runLocked( $step )
    {
        for ( $i = 0; $i < 60; $i++ )
        {
            $totals = CjwNewsletterRunner::$step( new CjwNewsletterJobOutput( false ), 'nltest' );
            if ( empty( $totals['locked'] ) )
                return $totals;
            usleep( 500000 );
        }
        $this->fail( "the runner's $step stayed locked" );
    }

    /** @return string the decoded content of a mail file */
    protected function decodedMail( $file )
    {
        $raw = file_get_contents( $file );
        $text = quoted_printable_decode( preg_replace( "/=\r?\n/", '', $raw ) );
        // base64 parts
        if ( preg_match_all( "/Content-Transfer-Encoding: base64\r?\n(?:[^\r\n]+\r?\n)*\r?\n([A-Za-z0-9+\/=\r\n]+)/", $raw, $m ) )
            foreach ( $m[1] as $b64 )
                $text .= "\n" . base64_decode( preg_replace( '/\s+/', '', $b64 ) );
        return html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
    }

    /** @return array email => decoded mail text, for the mails of the outbox */
    protected function mailsByRecipient( array $users )
    {
        $out = array();
        foreach ( $this->outbox() as $file )
            foreach ( $users as $u )
                if ( strpos( basename( $file ), (string)$u->attribute( 'email' ) ) !== false )
                    $out[(string)$u->attribute( 'email' )] = $this->decodedMail( $file );
        return $out;
    }

    /** @return array clicks (url, link_id, key, sig), pixel (url, key, sig) found in a mail */
    protected function trackingUrls( $text )
    {
        $r = array( 'clicks' => array(), 'pixel' => null );
        preg_match_all( '#(https?://[^\s"\'<>]+/newsletter/r/([0-9]+)/([ap][0-9a-fx]+)/([0-9a-f]{24}))#', $text, $m, PREG_SET_ORDER );
        foreach ( $m as $x )
            $r['clicks'][] = array( 'url' => $x[1], 'link_id' => (int)$x[2], 'key' => $x[3], 'sig' => $x[4] );
        if ( preg_match( '#(https?://[^\s"\'<>]+/newsletter/o/([ap][0-9a-fx]+)/([0-9a-f]{24}))#', $text, $p ) )
            $r['pixel'] = array( 'url' => $p[1], 'key' => $p[2], 'sig' => $p[3] );
        return $r;
    }

    /** @return array status, headers (lower case name => value), body, or null when the site cannot be reached */
    protected function http( $url )
    {
        if ( !function_exists( 'curl_init' ) )
            return null;
        // the site answers http with a redirect to https
        $url = preg_replace( '#^http://#', 'https://', $url );
        $c = curl_init( $url );
        curl_setopt_array( $c, array( CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_FOLLOWLOCATION => false,
                                      CURLOPT_TIMEOUT => 20, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0 ) );
        $response = curl_exec( $c );
        if ( $response === false )
            return null;
        $status = (int)curl_getinfo( $c, CURLINFO_HTTP_CODE );
        $size = (int)curl_getinfo( $c, CURLINFO_HEADER_SIZE );
        $headers = array();
        foreach ( preg_split( "/\r?\n/", substr( $response, 0, $size ) ) as $line )
            if ( strpos( $line, ':' ) !== false )
            {
                list( $k, $v ) = explode( ':', $line, 2 );
                $headers[strtolower( trim( $k ) )] = trim( $v );
            }
        return array( 'status' => $status, 'headers' => $headers, 'body' => substr( $response, $size ) );
    }

    /** Skips unless the site answers and counts (its [TrackingSettings] Tracking is enabled). */
    protected function requireSiteTracking( $pixelUrl )
    {
        $probe = $this->http( preg_replace( '#/newsletter/o/.*$#', '/newsletter/o/x/y', $pixelUrl ) );
        if ( $probe === null || $probe['status'] !== 200 )
            $this->markTestSkipped( 'the site does not answer the open pixel: ' . $pixelUrl );
    }

    protected function totals( $sendId )
    {
        return CjwNewsletterStatisticsReport::totals( $sendId );
    }

    /** The totals once $type reached $atLeast (the site's write ends with its request), at most 5 s later. */
    protected function totalsWhen( $sendId, $type, $atLeast )
    {
        for ( $i = 0; $i < 50; $i++ )
        {
            $t = $this->totals( $sendId );
            if ( $t[$type] >= $atLeast )
                return $t;
            usleep( 100000 );
        }
        return $t;
    }

    protected function item( CjwNewsletterEditionSend $send, CjwNewsletterUser $user )
    {
        return CjwNewsletterEditionSendItem::fetchBySendIdAndNewsletterUserIdAndOutputFormatId( $send->attribute( 'id' ), $user->attribute( 'id' ), 0 );
    }

    protected function rowCount( $sql )
    {
        $rows = eZDB::instance()->arrayQuery( $sql );
        return (int)$rows[0]['c'];
    }

    // ------------------------------------------------------------------ ST-01, ST-02

    public function testLinksAreRewrittenExceptMailtoAnchorsAndPersonalLinksAndThePixelIsAdded()
    {
        $this->newSubscriber( 'rw' );
        $send = $this->trackedSend( CjwNewsletterTracking::MODE_ANONYMOUS );
        $xml = (string)$send->attribute( 'output_xml' );
        $links = CjwNewsletterLink::fetchList( array( 'edition_send_id' => (int)$send->attribute( 'id' ) ) );
        $urls = array_map( function ( $l ) { return $l->attribute( 'url' ); }, $links );
        $this->assertContains( self::LINK, $urls, 'the link is stored with its entities decoded' );
        $this->assertContains( self::LINK2, $urls, 'the link of the text part too, without the full stop' );
        foreach ( $urls as $u )
        {
            $this->assertStringStartsNotWith( 'mailto:', $u );
            $this->assertStringNotContainsString( '#_', $u, 'personal links are never rewritten' );
        }
        $this->assertSame( count( $urls ), count( array_unique( $urls ) ), 'one row per URL' );
        $this->assertStringContainsString( 'href="mailto:nltest@example.invalid"', $xml );
        $this->assertStringContainsString( 'href="#top"', $xml );
        $this->assertStringContainsString( '/newsletter/unsubscribe/#_hash_unsubscribe_#', $xml );
        $this->assertStringNotContainsString( 'href="' . htmlspecialchars( self::LINK ) . '"', $xml, 'the article link is rewritten' );
        $this->assertMatchesRegularExpression( '#/newsletter/r/[0-9]+/' . preg_quote( CjwNewsletterTracking::PLACEHOLDER, '#' ) . '#', $xml );
        $this->assertMatchesRegularExpression( '#<img src="[^"]+/newsletter/o/' . preg_quote( CjwNewsletterTracking::PLACEHOLDER, '#' ) . '"#', $xml, 'the open pixel' );
        // queueing again does not rewrite twice
        $state = array();
        $again = CjwNewsletterTracking::rewriteOutputXml( $xml, $send->attribute( 'id' ), $state );
        $this->assertSame( substr_count( $xml, '/newsletter/o/' ), substr_count( $again, '/newsletter/o/' ), 'one pixel per HTML part, not added twice' );
        $this->assertSame( substr_count( $xml, '<type name="html"' ), substr_count( $xml, '/newsletter/o/' ) );
    }

    public function testWithoutConsentTheKeyIsAnonymousAndWithConsentPersonal()
    {
        $with = $this->newSubscriber( 'with' );
        $without = $this->newSubscriber( 'without' );
        $this->consent( $with, true );
        $send = $this->trackedSend( CjwNewsletterTracking::MODE_PERSON );
        $this->process();
        $mails = $this->mailsByRecipient( array( $with, $without ) );
        $this->assertCount( 2, $mails );
        $a = $this->trackingUrls( $mails[$with->attribute( 'email' )] );
        $b = $this->trackingUrls( $mails[$without->attribute( 'email' )] );
        $this->assertNotEmpty( $a['clicks'] );
        $this->assertSame( 'p' . $this->item( $send, $with )->attribute( 'hash' ), $a['pixel']['key'], 'personal key with consent' );
        $this->assertSame( 'a' . $send->attribute( 'id' ) . 'x0', $b['pixel']['key'], 'anonymous key without consent' );
        foreach ( array_merge( $a['clicks'], $b['clicks'] ) as $c )
            $this->assertTrue( CjwNewsletterTracking::verify( $c['key'], $c['sig'] ) );
        $this->assertStringNotContainsString( CjwNewsletterTracking::PLACEHOLDER, $mails[$with->attribute( 'email' )], 'no placeholder left' );
        $this->assertStringNotContainsString( (string)$without->attribute( 'email' ), $b['pixel']['url'], 'no address in a URL' );
    }

    // ------------------------------------------------------------------ ST-03, ST-04 over HTTP

    public function testPixelAndRedirectOverHttpCountPerPersonAndAnonymously()
    {
        $with = $this->newSubscriber( 'hwith' );
        $without = $this->newSubscriber( 'hwithout' );
        $this->consent( $with, true );
        $send = $this->trackedSend( CjwNewsletterTracking::MODE_PERSON );
        $this->process();
        $mails = $this->mailsByRecipient( array( $with, $without ) );
        $a = $this->trackingUrls( $mails[$with->attribute( 'email' )] );
        $b = $this->trackingUrls( $mails[$without->attribute( 'email' )] );
        $this->requireSiteTracking( $a['pixel']['url'] );
        $sendId = (int)$send->attribute( 'id' );

        $pixel = $this->http( $a['pixel']['url'] );
        $this->assertSame( 200, $pixel['status'] );
        $this->assertSame( 'image/gif', $pixel['headers']['content-type'] );
        $this->assertStringContainsString( 'no-store', $pixel['headers']['cache-control'] );
        $this->assertArrayNotHasKey( 'set-cookie', $pixel['headers'], 'no session' );
        $this->assertSame( CjwNewsletterTracking::pixel(), $pixel['body'] );
        $t = $this->totalsWhen( $sendId, 'open', 1 );
        if ( $t['open'] === 0 )
            $this->markTestSkipped( 'the site counts nothing: its [TrackingSettings] Tracking is not enabled' );
        $this->assertSame( 1, $t['open'] );
        $this->assertSame( 1, $t['unique_open'] );
        $item = $this->item( $send, $with );
        $this->assertSame( 1, (int)$item->attribute( 'open_count' ) );
        $this->assertGreaterThan( 0, (int)$item->attribute( 'first_opened' ) );
        $this->assertSame( 1, $this->rowCount( 'SELECT COUNT(*) AS c FROM cjwnl_open WHERE edition_send_item_id = ' . (int)$item->attribute( 'id' ) ) );

        // the person without consent: totals only, no row of the person
        $anonymous = $this->http( $b['pixel']['url'] );
        $t = $this->totalsWhen( $sendId, 'open', 2 );
        $this->assertSame( 2, $t['open'], 'the anonymous open of ' . $b['pixel']['url'] . ' answered ' . $anonymous['status'] );
        $this->assertSame( 1, $t['unique_open'], 'an anonymous open is not unique' );
        $this->assertSame( 0, (int)$this->item( $send, $without )->attribute( 'open_count' ) );
        $this->assertSame( 1, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_open WHERE edition_send_id = $sendId" ), 'no row without consent' );

        // the click: 302 to the stored URL
        $click = null;
        foreach ( $a['clicks'] as $c )
            if ( CjwNewsletterLink::fetch( $c['link_id'] )->attribute( 'url' ) === self::LINK )
                $click = $c;
        $this->assertNotNull( $click );
        $r = $this->http( $click['url'] );
        $this->assertSame( 302, $r['status'] );
        $this->assertSame( self::LINK, $r['headers']['location'] );
        $this->assertStringContainsString( 'no-store', $r['headers']['cache-control'] );
        $this->assertArrayNotHasKey( 'set-cookie', $r['headers'] );
        $this->totalsWhen( $sendId, 'click', 1 );
        $this->assertSame( 1, (int)CjwNewsletterLink::fetch( $click['link_id'] )->attribute( 'click_count' ) );
        $this->assertSame( 1, (int)$this->item( $send, $with )->attribute( 'click_count' ) );
        $this->http( $click['url'] );
        $t = $this->totalsWhen( $sendId, 'click', 2 );
        $this->assertSame( 2, $t['click'] );
        $this->assertSame( 1, $t['unique_click'] );
        $this->assertSame( 2, $this->rowCount( 'SELECT COUNT(*) AS c FROM cjwnl_link_click WHERE link_id = ' . (int)$click['link_id'] ) );
    }

    public function testOverHttpABadSignatureOrAForeignKeyAnswers404AndCountsNothing()
    {
        $user = $this->newSubscriber( 'bad' );
        $send = $this->trackedSend( CjwNewsletterTracking::MODE_ANONYMOUS );
        $this->process();
        $mails = $this->mailsByRecipient( array( $user ) );
        $u = $this->trackingUrls( reset( $mails ) );
        $this->requireSiteTracking( $u['pixel']['url'] );
        $sendId = (int)$send->attribute( 'id' );
        $click = $u['clicks'][0];
        $base = substr( $click['url'], 0, strpos( $click['url'], '/newsletter/r/' ) );

        $bad = substr( $click['sig'], 0, 23 ) . ( substr( $click['sig'], -1 ) === '0' ? '1' : '0' );
        $this->assertSame( 404, $this->http( "$base/newsletter/r/{$click['link_id']}/{$click['key']}/$bad" )['status'], 'bad signature' );
        $this->assertSame( 404, $this->http( "$base/newsletter/r/{$click['link_id']}/{$click['key']}" )['status'], 'missing signature' );
        $foreign = 'a' . ( $sendId + 100000 ) . 'x0';
        $this->assertSame( 404, $this->http( "$base/newsletter/r/{$click['link_id']}/$foreign/" . CjwNewsletterTracking::sign( $foreign ) )['status'], 'a valid key of another send' );
        $this->assertSame( 404, $this->http( "$base/newsletter/r/999999999/{$click['key']}/{$click['sig']}" )['status'], 'unknown link' );
        $this->assertSame( 404, $this->http( "$base/newsletter/r/{$click['link_id']}/" . rawurlencode( 'https://evil.invalid/' ) . '/x' )['status'], 'never a URL of the request' );
        $pixel = $this->http( "$base/newsletter/o/{$u['pixel']['key']}/$bad" );
        $this->assertSame( 200, $pixel['status'], 'the pixel always answers the image' );
        $this->assertSame( 'image/gif', $pixel['headers']['content-type'] );
        $this->assertStringContainsString( 'no-store', $pixel['headers']['cache-control'] );
        usleep( 1000000 ); // a write of the site would be visible by now
        $t = $this->totals( $sendId );
        $this->assertSame( 0, $t['open'] + $t['click'], 'nothing counted' );
        $this->assertSame( 0, (int)CjwNewsletterLink::fetch( $click['link_id'] )->attribute( 'click_count' ) );

        // the web archive's link (no key): the stored URL, not counted
        $archive = $this->http( "$base/newsletter/r/{$click['link_id']}/" );
        $this->assertSame( 302, $archive['status'] );
        $this->assertSame( CjwNewsletterLink::fetch( $click['link_id'] )->attribute( 'url' ), $archive['headers']['location'] );
        $this->assertSame( 0, (int)CjwNewsletterLink::fetch( $click['link_id'] )->attribute( 'click_count' ) );
    }

    // ------------------------------------------------------------------ ST-05, ST-06

    public function testPerPersonOnlyWithConsentAndAWithdrawalCountsAnonymouslyFromThenOn()
    {
        $user = $this->newSubscriber( 'later' );
        $this->consent( $user, true );
        $send = $this->trackedSend( CjwNewsletterTracking::MODE_PERSON );
        $this->process();
        $item = $this->item( $send, $user );
        $key = CjwNewsletterTracking::personKey( $item );
        $sig = CjwNewsletterTracking::sign( $key );
        $this->assertSame( 'person', CjwNewsletterTracking::recordOpen( $key, $sig ) );
        $this->consent( $user, false );
        $this->assertSame( 'anonymous', CjwNewsletterTracking::recordOpen( $key, $sig ), 'consent is checked on every open' );
        $this->assertSame( 0, (int)$this->item( $send, $user )->attribute( 'open_count' ), 'the withdrawal reset the counts' );
        $t = $this->totals( $send->attribute( 'id' ) );
        $this->assertSame( 2, $t['open'], 'both opens are in the totals' );
        $this->assertSame( 1, $t['unique_open'], 'the totals stay after the withdrawal' );
        // a send that counts anonymously never counts per person, even with consent
        $this->consent( $user, true );
        $send->setAttribute( 'tracking_mode', CjwNewsletterTracking::MODE_ANONYMOUS );
        $send->store();
        $this->assertSame( 'anonymous', CjwNewsletterTracking::recordOpen( $key, $sig ) );
        $send->setAttribute( 'tracking_mode', CjwNewsletterTracking::MODE_OFF );
        $send->store();
        $this->assertSame( 'off', CjwNewsletterTracking::recordOpen( $key, $sig ) );
        $this->assertSame( 'invalid', CjwNewsletterTracking::recordOpen( $key, str_repeat( '0', 24 ) ) );
    }

    public function testWithdrawalAndErasureRemoveThePersonRowsAtOnceAndTheTotalsStay()
    {
        $a = $this->newSubscriber( 'wd' );
        $b = $this->newSubscriber( 'er' );
        $this->consent( $a, true );
        $this->consent( $b, true );
        $send = $this->trackedSend( CjwNewsletterTracking::MODE_PERSON );
        $this->process();
        $link = CjwNewsletterLink::fetchByEditionSendIdAndUrlHash( $send->attribute( 'id' ), hash( 'sha256', self::LINK ) );
        foreach ( array( $a, $b ) as $u )
        {
            $key = CjwNewsletterTracking::personKey( $this->item( $send, $u ) );
            CjwNewsletterTracking::recordOpen( $key, CjwNewsletterTracking::sign( $key ) );
            $r = CjwNewsletterTracking::recordClick( $link->attribute( 'id' ), $key, CjwNewsletterTracking::sign( $key ) );
            $this->assertSame( 'person', $r['status'] );
            $this->assertSame( self::LINK, $r['url'] );
        }
        $rowsOf = function ( $u ) use ( $send ) {
            $id = (int)$this->item( $send, $u )->attribute( 'id' );
            return $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_open WHERE edition_send_item_id = $id" )
                 + $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_link_click WHERE edition_send_item_id = $id" );
        };
        $this->assertSame( 2, $rowsOf( $a ) );
        $this->assertSame( 2, $rowsOf( $b ) );

        $this->consent( $a, false );
        $this->assertSame( 0, $rowsOf( $a ), 'withdrawn: gone at once' );
        $this->assertSame( 0, (int)$this->item( $send, $a )->attribute( 'click_count' ) );
        $this->assertSame( 2, $rowsOf( $b ), 'the other person is untouched' );

        expMailPreferences::forRecipient( CjwNewsletterMailPreferences::recipientForNewsletterUser( $b ) )->erase( expConsentContext::system( 'NLTEST erase' ) );
        $this->assertSame( 0, $rowsOf( $b ), 'erased: gone at once (the kernel calls erased())' );

        $t = $this->totals( $send->attribute( 'id' ) );
        $this->assertSame( 2, $t['open'] );
        $this->assertSame( 2, $t['unique_open'] );
        $this->assertSame( 2, $t['click'] );
        $this->assertSame( 2, (int)CjwNewsletterLink::fetch( $link->attribute( 'id' ) )->attribute( 'click_count' ), 'link totals stay' );
    }

    // ------------------------------------------------------------------ ST-07

    public function testRetentionCleanupRemovesExpiredPersonRowsAndKeepsTotalsWithAtAndDryRun()
    {
        $user = $this->newSubscriber( 'ret' );
        $this->consent( $user, true );
        $send = $this->trackedSend( CjwNewsletterTracking::MODE_PERSON );
        $this->process();
        $item = $this->item( $send, $user );
        $key = CjwNewsletterTracking::personKey( $item );
        CjwNewsletterTracking::recordOpen( $key, CjwNewsletterTracking::sign( $key ) );
        $itemId = (int)$item->attribute( 'id' );
        $db = eZDB::instance();

        // --at in 13 months, dry run: the row would go, nothing is removed
        $cmd = PHP_BINARY . ' extension/cjw_newsletter/bin/php/statistics.php --allow-root-user --cleanup --dry-run --at=' . escapeshellarg( '@' . strtotime( '+13 months' ) ) . ' 2>&1';
        $out = shell_exec( $cmd );
        $this->assertStringContainsString( 'DRY RUN', (string)$out, $cmd );
        $this->assertMatchesRegularExpression( '/[1-9][0-9]* opens/', (string)$out );
        $this->assertSame( 1, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_open WHERE edition_send_item_id = $itemId" ), 'a dry run removes nothing' );

        // the row and the send made 13 months ago: the normal cleanup removes the row and resets the item
        $old = strtotime( '-13 months' );
        $db->query( "UPDATE cjwnl_open SET created = $old WHERE edition_send_item_id = $itemId" );
        $db->query( "UPDATE cjwnl_edition_send SET created = $old WHERE id = " . (int)$send->attribute( 'id' ) );
        $out = shell_exec( PHP_BINARY . ' extension/cjw_newsletter/bin/php/statistics.php --allow-root-user --cleanup 2>&1' );
        $this->assertStringContainsString( 'PASS', (string)$out );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_open WHERE edition_send_item_id = $itemId" ) );
        $this->assertSame( 0, (int)$this->item( $send, $user )->attribute( 'open_count' ) );
        $this->assertSame( 0, (int)$this->item( $send, $user )->attribute( 'first_opened' ) );
        $t = $this->totals( $send->attribute( 'id' ) );
        $this->assertSame( 1, $t['open'], 'the totals stay' );
        $this->assertSame( 1, $t['unique_open'] );
        $this->assertNotNull( CjwNewsletterStatisticsRetention::lastRun() );
        $this->assertFalse( \Exponential\Command\Extension\CjwNewsletter\Statistics::parseAt( 'not a date at all' ) );
        $this->assertSame( 1700000000, \Exponential\Command\Extension\CjwNewsletter\Statistics::parseAt( '1700000000' ) );
        $this->assertSame( strtotime( '2026-01-02' ), \Exponential\Command\Extension\CjwNewsletter\Statistics::parseAt( '2026-01-02' ) );
    }

    public function testRetentionCutoffFollowsTheSetting()
    {
        $this->setIni( 'cjw_newsletter.ini', 'TrackingSettings', 'PersonRetentionMonths', '3' );
        $now = mktime( 12, 0, 0, 6, 15, 2026 );
        $this->assertSame( mktime( 12, 0, 0, 3, 15, 2026 ), CjwNewsletterStatisticsRetention::cutoff( $now ) );
        $dry = CjwNewsletterStatisticsRetention::cleanup( $now, true );
        $this->assertTrue( $dry['dry_run'] );
    }

    // ------------------------------------------------------------------ ST-08, ST-09

    /** @return CjwNewsletterUser[] */
    protected function subscribers( $n, $label, $consent )
    {
        $users = array();
        for ( $i = 0; $i < $n; $i++ )
        {
            $users[$i] = $this->newSubscriber( $label . $i );
            if ( $consent )
                $this->consent( $users[$i], true );
        }
        return $users;
    }

    protected function subjectsOfOutbox()
    {
        $subjects = array();
        foreach ( $this->outbox() as $file )
            if ( preg_match( '/^Subject: (.*)$/m', $this->mailText( $file ), $m ) )
                $subjects[] = trim( mb_decode_mimeheader( $m[1] ) );
        return $subjects;
    }

    public function testAbTestSendsTheSamplesFirstWaitsAndSendsTheWinnerByOpenRateToTheRest()
    {
        $users = $this->subscribers( 20, 'ab', true );
        $before = (int)CjwNewsletterEditionSendItem::fetchListByStatusCount( CjwNewsletterEditionSendItem::STATUS_NEW );
        $send = $this->trackedSend( CjwNewsletterTracking::MODE_PERSON, array( 'subjects' => array( 'NLTEST subject B' ), 'percent' => 10, 'criterion' => 'open', 'hours' => 4 ) );
        $test = CjwNewsletterAbTester::forSend( $send );
        $this->assertNotNull( $test );
        $variants = CjwNewsletterAbTester::variants( $test );
        $this->assertCount( 2, $variants );
        $sendId = (int)$send->attribute( 'id' );
        $all = $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_edition_send_item WHERE edition_send_id = $sendId" );
        $samples = $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_edition_send_item WHERE edition_send_id = $sendId AND ab_variant_id > 0" );
        $this->assertSame( 2 * max( 1, (int)floor( $all * 10 / 100 ) ), $samples, '10 % per variant' );
        $maxSample = $this->rowCount( "SELECT MAX( id ) AS c FROM cjwnl_edition_send_item WHERE edition_send_id = $sendId AND ab_variant_id > 0" );
        $minRest = $this->rowCount( "SELECT MIN( id ) AS c FROM cjwnl_edition_send_item WHERE edition_send_id = $sendId AND ab_variant_id = 0" );
        $this->assertGreaterThan( $maxSample, $minRest, 'the rest is behind the samples' );

        $this->process();
        $this->assertCount( $samples, $this->outbox(), 'only the samples went out' );
        $test = CjwNewsletterAbTest::fetch( $test->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterAbTester::STATUS_WAITING, (int)$test->attribute( 'status' ) );
        $this->assertFalse( CjwNewsletterStatisticsHooks::sendProcessAllowed( CjwNewsletterEditionSend::fetch( $sendId ) ), 'held during the wait' );
        $subjects = $this->subjectsOfOutbox();
        $this->assertContains( 'NLTEST subject B', array_map( function ( $s ) { return preg_replace( '/^\[[^\]]*\]\s*/', '', $s ); }, $subjects ), implode( ' | ', $subjects ) );
        $this->process();
        $this->assertCount( $samples, $this->outbox(), 'nothing more during the wait' );

        // variant B is opened more often
        $b = $variants[1];
        $rows = eZDB::instance()->arrayQuery( "SELECT hash FROM cjwnl_edition_send_item WHERE edition_send_id = $sendId AND ab_variant_id = " . (int)$b->attribute( 'id' ) );
        foreach ( $rows as $row )
            CjwNewsletterTracking::recordOpen( 'p' . $row['hash'], CjwNewsletterTracking::sign( 'p' . $row['hash'] ) );
        $this->assertSame( CjwNewsletterAbTester::STATUS_WAITING, CjwNewsletterStatisticsHooks::advanceTest( $test, time() ), 'not before the wait is over' );
        $this->assertSame( CjwNewsletterAbTester::STATUS_DECIDED, CjwNewsletterStatisticsHooks::advanceTest( CjwNewsletterAbTest::fetch( $test->attribute( 'id' ) ), time() + 4 * 3600 + 1 ) );
        $test = CjwNewsletterAbTest::fetch( $test->attribute( 'id' ) );
        $this->assertSame( (int)$b->attribute( 'id' ), (int)$test->attribute( 'winner_variant_id' ), 'B wins by open rate' );
        $this->assertSame( 'open', (string)$test->attribute( 'criterion' ) );

        $this->process();
        $this->assertCount( $all, $this->outbox(), 'the rest went out' );
        $rest = array_slice( $this->subjectsOfOutbox(), 0 );
        $countB = count( array_filter( $rest, function ( $s ) { return strpos( $s, 'NLTEST subject B' ) !== false; } ) );
        $this->assertSame( $all - $samples / 2, $countB, 'the rest and the B samples got subject B' );
        $this->process();
        $this->assertSame( CjwNewsletterAbTester::STATUS_DONE, (int)CjwNewsletterAbTest::fetch( $test->attribute( 'id' ) )->attribute( 'status' ) );
        $summary = CjwNewsletterAbTester::summary( CjwNewsletterAbTest::fetch( $test->attribute( 'id' ) ) );
        $this->assertSame( $samples / 2, $summary['variants'][1]['sent'] );
        $this->assertTrue( $summary['variants'][1]['winner'] );
        unset( $before, $users );
    }

    public function testAbTestWithAnonymousTotalsOnlyIsDecidedByClicks()
    {
        $this->subscribers( 10, 'abc', false );
        $send = $this->trackedSend( CjwNewsletterTracking::MODE_ANONYMOUS, array( 'subjects' => array( 'NLTEST subject B' ), 'percent' => 20, 'criterion' => 'open', 'hours' => 0 ) );
        $this->process();
        $test = CjwNewsletterAbTester::forSend( $send );
        $variants = CjwNewsletterAbTester::variants( $test );
        $link = CjwNewsletterLink::fetchByEditionSendIdAndUrlHash( $send->attribute( 'id' ), hash( 'sha256', self::LINK ) );
        // anonymous clicks of variant A
        $key = CjwNewsletterTracking::anonymousKey( $send->attribute( 'id' ), $variants[0]->attribute( 'id' ) );
        $r = CjwNewsletterTracking::recordClick( $link->attribute( 'id' ), $key, CjwNewsletterTracking::sign( $key ) );
        $this->assertSame( 'anonymous', $r['status'] );
        $this->assertSame( 'click', CjwNewsletterAbTester::effectiveCriterion( CjwNewsletterAbTest::fetch( $test->attribute( 'id' ) ), $send ) );
        CjwNewsletterStatisticsHooks::advanceTest( CjwNewsletterAbTest::fetch( $test->attribute( 'id' ) ), time() + 1 );
        $test = CjwNewsletterAbTest::fetch( $test->attribute( 'id' ) );
        $this->assertSame( (int)$variants[0]->attribute( 'id' ), (int)$test->attribute( 'winner_variant_id' ), 'A wins by clicks' );
        $this->assertSame( 'click', (string)$test->attribute( 'criterion' ) );
    }

    public function testAbTestCanBeCancelledAndTheRestGetsTheEditionSubject()
    {
        $this->subscribers( 10, 'abx', false );
        $send = $this->trackedSend( CjwNewsletterTracking::MODE_ANONYMOUS, array( 'subjects' => array( 'NLTEST subject B' ), 'percent' => 10, 'criterion' => 'click', 'hours' => 4 ) );
        $this->process();
        $test = CjwNewsletterAbTester::forSend( $send );
        $this->assertTrue( CjwNewsletterAbTester::cancel( $test ) );
        $this->assertFalse( CjwNewsletterAbTester::cancel( CjwNewsletterAbTest::fetch( $test->attribute( 'id' ) ) ) );
        $this->process();
        $subjects = $this->subjectsOfOutbox();
        $this->assertCount( 1, array_filter( $subjects, function ( $s ) { return strpos( $s, 'NLTEST subject B' ) !== false; } ), 'only the B sample' );
    }

    // ------------------------------------------------------------------ ST-10

    public function testValidationOfTheAbValuesAndTheTrackingOfTheSendFormAndTheList()
    {
        $this->assertNotEmpty( CjwNewsletterAbTester::validate( array( '' ), 10, 4, 2 ), 'no other subject' );
        $this->assertNotEmpty( CjwNewsletterAbTester::validate( array( 'B' ), 50, 4, 2 ), '2 x 50 % leaves nobody' );
        $this->assertNotEmpty( CjwNewsletterAbTester::validate( array( 'B' ), 10, 400, 2 ), 'wait too long' );
        $this->assertNotEmpty( CjwNewsletterAbTester::validate( array( 'B' ), 10, 4, 0 ), 'needs tracking' );
        $this->assertSame( array(), CjwNewsletterAbTester::validate( array( 'B', 'C' ), 10, 4, 1 ) );
        $d = CjwNewsletterAbTester::defaults();
        $this->assertSame( array( 10, 2, 'open' ), array( $d['sample_percent'], $d['variants'], $d['criterion'] ) );
        $this->assertEquals( 4, $d['wait_hours'] );

        $http = eZHTTPTool::instance();
        $_POST = array( 'CjwNewsletterStatistics' => array( 'TrackingMode' => '1', 'AbTest' => '1', 'AbSubject' => array( 'NLTEST B' ), 'AbSamplePercent' => '10', 'AbWaitHours' => '2', 'AbCriterion' => 'click' ) );
        $this->assertSame( array(), CjwNewsletterStatisticsHooks::sendFormValidate( $http, null ) );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() + 86400 );
        CjwNewsletterStatisticsHooks::sendFormStored( $send, $http, null );
        $send = CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) );
        $this->assertSame( 1, (int)$send->attribute( 'tracking_mode' ) );
        $test = CjwNewsletterAbTester::forSend( $send );
        $this->assertSame( 7200, (int)$test->attribute( 'wait_seconds' ) );
        $this->assertSame( 'click', (string)$test->attribute( 'criterion' ) );
        $_POST = array( 'CjwNewsletterStatistics' => array( 'TrackingMode' => 'x' ) );
        $this->assertNotEmpty( CjwNewsletterStatisticsHooks::sendFormValidate( $http, null ) );
        $_POST = array();

        $list = new CjwNewsletterList( array() );
        $_POST = array( 'P_CjwNewsletterList_TrackingMode_7' => '2' );
        $this->assertSame( array(), CjwNewsletterStatisticsHooks::listAttributeInput( $list, $http, 'P_CjwNewsletterList_', '_7', null ) );
        $this->assertSame( 2, (int)$list->attribute( 'tracking_mode' ) );
        $_POST = array( 'P_CjwNewsletterList_TrackingMode_7' => '5' );
        $this->assertNotEmpty( CjwNewsletterStatisticsHooks::listAttributeInput( $list, $http, 'P_CjwNewsletterList_', '_7', null ) );
        $_POST = array();
    }

    // ------------------------------------------------------------------ ST-11

    public function testReportDashboardArticleBoxAndCsvShowTotalsOnly()
    {
        $user = $this->newSubscriber( 'rep' );
        $this->consent( $user, true );
        $send = $this->trackedSend( CjwNewsletterTracking::MODE_PERSON );
        $this->process();
        $key = CjwNewsletterTracking::personKey( $this->item( $send, $user ) );
        CjwNewsletterTracking::recordOpen( $key, CjwNewsletterTracking::sign( $key ) );
        $link = CjwNewsletterLink::fetchByEditionSendIdAndUrlHash( $send->attribute( 'id' ), hash( 'sha256', self::LINK ) );
        CjwNewsletterTracking::recordClick( $link->attribute( 'id' ), $key, CjwNewsletterTracking::sign( $key ) );
        $sendId = (int)$send->attribute( 'id' );

        $r = CjwNewsletterStatisticsReport::send( CjwNewsletterEditionSend::fetch( $sendId ) );
        $this->assertSame( 1, $r['sent'] );
        $this->assertSame( 1, $r['opens'] );
        $this->assertSame( 1, $r['unique_clicks'] );
        $this->assertEquals( 100, $r['open_rate'] );
        $this->assertSame( self::LINK, $r['links'][0]['url'] );
        $this->assertCount( 1, $r['days'] );

        $view = $this->runView( 'report', array( $sendId ) );
        $this->assertViewOk( $view );
        $this->assertStringContainsString( 'nl-stat-kpis', $view['content'] );
        $this->assertStringContainsString( htmlspecialchars( self::LINK ), $view['content'] );
        $this->assertStringNotContainsString( (string)$user->attribute( 'email' ), $view['content'], 'no address on the report' );
        $list = $this->runView( 'report' );
        $this->assertViewOk( $list );
        $this->assertStringContainsString( 'newsletter/report/' . $sendId, $list['content'] );
        $this->assertSame( eZModule::STATUS_FAILED, $this->runView( 'report', array( 999999999 ) )['exit'] );

        $index = $this->runView( 'index' );
        $this->assertStringContainsString( 'nl-area-statistics', $index['content'], 'the dashboard block' );

        $csv = \Exponential\View\Extension\CjwNewsletter\Newsletter\StatisticsExport::export( $sendId, 'sends' );
        $this->assertStringStartsWith( "\xEF\xBB\xBF" . 'edition_send_id,', $csv['csv'] );
        $this->assertStringNotContainsString( (string)$user->attribute( 'email' ), $csv['csv'] );
        $links = \Exponential\View\Extension\CjwNewsletter\Newsletter\StatisticsExport::export( $sendId, 'links' );
        $this->assertStringContainsString( self::LINK, $links['csv'] );
        $days = \Exponential\View\Extension\CjwNewsletter\Newsletter\StatisticsExport::export( $sendId, 'days' );
        $this->assertStringContainsString( date( 'Y-m-d' ) . ',1,1', $days['csv'] );
        $this->assertSame( "'=1+1,'@x,plain\n", CjwNewsletterStatisticsReport::csv( array( array( '=1+1', '@x', 'plain' ) ) ), 'formulas are defused' );

        // the article box of an object linked from the mail
        $link = CjwNewsletterLink::fetch( $link->attribute( 'id' ) );
        $link->setAttribute( 'contentobject_id', (int)$send->attribute( 'edition_contentobject_id' ) );
        $link->store();
        $a = CjwNewsletterStatisticsReport::article( (int)$send->attribute( 'edition_contentobject_id' ) );
        $this->assertSame( 1, $a['clicks'] );
        $this->assertSame( 1, $a['sends'] );
        $fetched = ( new CjwNewsletterStatistics() )->fetchArticleStatistics( (int)$send->attribute( 'edition_contentobject_id' ) );
        $this->assertSame( 1, $fetched['result']['clicks'] );
        // an article written in the edition: its edition is found from the node tree
        $articleRows = eZDB::instance()->arrayQuery( 'SELECT o.id FROM ezcontentobject o, ezcontentobject_tree t, ezcontentobject_tree p WHERE t.contentobject_id = o.id AND t.parent_node_id = p.node_id AND p.contentobject_id = ' . (int)$send->attribute( 'edition_contentobject_id' ) );
        $this->assertNotEmpty( $articleRows );
        $article = CjwNewsletterStatisticsReport::article( (int)$articleRows[0]['id'] );
        $this->assertCount( 1, $article['editions'] );
        $this->assertSame( 1, $article['editions'][0]['sends'][0]['sent'] );

        $ab = $this->runView( 'ab_test', array( $sendId ) );
        $this->assertSame( eZModule::STATUS_FAILED, $ab['exit'], 'no A/B test: not found' );
    }

    public function testAbTestPageShowsTheVariants()
    {
        $this->subscribers( 10, 'abp', false );
        $send = $this->trackedSend( CjwNewsletterTracking::MODE_ANONYMOUS, array( 'subjects' => array( 'NLTEST subject B' ), 'percent' => 10, 'criterion' => 'open', 'hours' => 4 ) );
        $view = $this->runView( 'ab_test', array( (int)$send->attribute( 'id' ) ) );
        $this->assertViewOk( $view );
        $this->assertStringContainsString( 'NLTEST subject B', $view['content'] );
        $this->assertStringContainsString( 'ChooseWinnerButton', $view['content'] );
    }

    // ------------------------------------------------------------------ ST-12

    public function testTheConsentCategoryIsOptionalOffByDefaultWithItsPart()
    {
        $cat = expMailCategoryRegistry::instance()->get( 'newsletter_statistics' );
        $this->assertNotNull( $cat, 'the category is set up (mailpreferences.ini.append.php)' );
        $this->assertFalse( $cat->essential );
        $this->assertFalse( $cat->defaultOn );
        $this->assertFalse( $cat->doubleOptIn );
        $this->assertSame( 'CjwNewsletterStatisticsCategoryHandler', $cat->handlerClass );
        $user = $this->newSubscriber( 'cat' );
        $this->consentEmails[] = (string)$user->attribute( 'email' );
        $r = CjwNewsletterMailPreferences::recipientForNewsletterUser( $user );
        $this->assertSame( expMailPreferences::OFF, expMailPreferences::forRecipient( $r )->state( 'newsletter_statistics' ), 'off for a new person' );
        $this->assertFalse( CjwNewsletterTracking::hasConsent( $user ) );
        $this->consent( $user, true );
        $this->assertTrue( CjwNewsletterTracking::hasConsent( $user ) );
        $log = expConsentLog::fetchForRecipient( $r );
        $this->assertSame( 'newsletter_statistics', $log[0]->attribute( 'category' ), 'logged in the consent log' );
        $this->assertSame( 'on', $log[0]->attribute( 'action' ) );
        $handler = $cat->handler();
        $this->assertSame( 'design:mailpreferences/category/newsletter_statistics.tpl', $handler->partTemplate( $r, $cat, 'account' ) );
        $vars = $handler->partVariables( $r, $cat, 'account' );
        $this->assertSame( 12, $vars['retention_months'] );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'part', $vars );
        $tpl->setVariable( 'category', array( 'identifier' => 'newsletter_statistics' ) );
        $tpl->setVariable( 'email', '' );
        $tpl->setVariable( 'mode', 'account' );
        $html = $tpl->fetch( 'design:mailpreferences/category/newsletter_statistics.tpl' );
        $this->reinstallCollector();
        $this->assertStringContainsString( 'mp-hint', $html );
        $this->assertStringContainsString( '12', $html );
    }

    // ------------------------------------------------------------------ ST-13

    public function testSmsSendsAndASiteWithTrackingOffAreNotTracked()
    {
        $this->newSubscriber( 'sms' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $send->setAttribute( 'tracking_mode', CjwNewsletterTracking::MODE_PERSON );
        $send->setAttribute( 'channel', 'sms' );
        $send->store();
        $this->injectLinks( $send );
        CjwNewsletterStatisticsHooks::sendQueueCreated( CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) ), false );
        $this->assertSame( 0, CjwNewsletterLink::fetchListCount( array( 'edition_send_id' => (int)$send->attribute( 'id' ) ) ), 'SMS: nothing rewritten' );
        $this->assertSame( CjwNewsletterTracking::MODE_OFF, CjwNewsletterTracking::sendMode( $send ) );

        $send->setAttribute( 'channel', 'email' );
        $send->store();
        $this->setIni( 'cjw_newsletter.ini', 'TrackingSettings', 'Tracking', 'disabled' );
        $this->assertSame( CjwNewsletterTracking::MODE_OFF, CjwNewsletterTracking::sendMode( $send ) );
        CjwNewsletterStatisticsHooks::sendQueueCreated( CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) ), false );
        $this->assertSame( 0, CjwNewsletterLink::fetchListCount( array( 'edition_send_id' => (int)$send->attribute( 'id' ) ) ), 'site switch off: nothing rewritten' );
        $key = CjwNewsletterTracking::anonymousKey( $send->attribute( 'id' ) );
        $this->assertSame( 'off', CjwNewsletterTracking::recordOpen( $key, CjwNewsletterTracking::sign( $key ) ) );
    }

    public function testTrackableAndKeys()
    {
        $this->assertTrue( CjwNewsletterTracking::trackable( 'https://www.example.invalid/a' ) );
        foreach ( array( 'mailto:a@example.invalid', '#top', '/relative', 'javascript:alert(1)', 'https://x.invalid/newsletter/unsubscribe/#_hash_unsubscribe_#',
                         'https://x.invalid/mailpreferences/manage/m1abc', 'https://x.invalid/newsletter/r/1/a1x0/abc', 'https://x.invalid/a?[[name]]' ) as $url )
            $this->assertFalse( CjwNewsletterTracking::trackable( $url ), $url );
        $this->setIni( 'cjw_newsletter.ini', 'TrackingSettings', 'NoTrackPatterns', array( '#^https://shop\.example\.invalid/#' ) );
        $this->assertFalse( CjwNewsletterTracking::trackable( 'https://shop.example.invalid/cart' ) );
        $this->assertNull( CjwNewsletterTracking::parseKey( 'a0x0' ) );
        $this->assertNull( CjwNewsletterTracking::parseKey( 'p123' ) );
        $this->assertNull( CjwNewsletterTracking::parseKey( "a1x0\n" ) );
        $this->assertSame( array( 'type' => 'anonymous', 'send_id' => 12, 'variant_id' => 3 ), CjwNewsletterTracking::parseKey( 'a12x3' ) );
        $this->assertFalse( CjwNewsletterTracking::verify( 'a12x3', 'zz' ) );
        $this->assertTrue( CjwNewsletterTracking::verify( 'a12x3', CjwNewsletterTracking::sign( 'a12x3' ) ) );
        $this->assertFalse( CjwNewsletterTracking::safeUrl( "https://x.invalid/\r\nSet-Cookie: a" ) );
        $this->assertSame( array( 'status' => 'not_found', 'url' => '' ), \Exponential\View\Extension\CjwNewsletter\Newsletter\R::handle( 'abc', '', '' ) );
    }
}
