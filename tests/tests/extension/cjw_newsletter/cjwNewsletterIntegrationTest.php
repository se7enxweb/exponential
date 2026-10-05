<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * cjw_newsletter 4.2.0: the feature areas together, in the order a subscriber and an editor meet them.
 *
 *  IT-01  The whole way of one person: subscribe by e-mail (mail-in, double opt-in) -> interests, language and the
 *         statistics consent on the e-mail preference page -> mobile number confirmed by code -> an edition with
 *         picks from the article pool, a condition block and two languages -> approval through the collaboration
 *         inbox (the send is held until then) -> a scheduled send with an A/B subject test -> throttled sending in
 *         batches to the file transport -> open and click over HTTP (tracking per person, on for the test list
 *         only) -> a hard bounce -> suppression -> the subscriber export shows the state -> the erasure removes the
 *         person's rows everywhere.
 *  IT-02  A removed (not erased) subscriber takes his interests, SMS codes and statistics with him, and the repair
 *         removes interests that older removals left behind.
 *  IT-03  The eznewsletter importer reads the throwaway SQLite fixture into a fresh list: a dry run that writes only
 *         its log, then the real run; the new list's subscribers show in its export.
 *
 * Live style (cjwNewsletterTestCase): the installation's database, the file transports only, the test's own
 * addresses on n7.example.invalid and the reserved numbers +1 555 01xx; everything the test makes is removed in
 * tearDown. No test database: the old eznewsletter tables are a throwaway SQLite file under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class cjwNewsletterIntegrationTest extends cjwNewsletterTestCase
{
    const OWN_DOMAIN = 'n7.example.invalid';
    const THROTTLE = 'nltestn7';
    const PHONE = '+15550142';

    protected $savedList = array();
    protected $interestIds = array();
    protected $poolIds = array();
    protected $mailinIds = array();
    protected $urlIds = array();
    protected $runIds = array();
    protected $files = array();
    protected $smsDir = null;

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        foreach ( array( 'CjwNewsletterMailin', 'CjwNewsletterApprovalFlow', 'CjwNewsletterRendering', 'CjwNewsletterTracking',
                         'CjwNewsletterSms', 'CjwNewsletterSubscriberExport', 'CjwNewsletterEznewsletterMigration' ) as $class )
            if ( !class_exists( $class ) )
                $this->markTestSkipped( "the 4.2.0 class $class is not loaded" );
        if ( !CjwNewsletterMailPreferences::available() )
            $this->markTestSkipped( 'needs the e-mail preferences of Exponential 6.0.15' );
        foreach ( array( 'TransportMethodCronjob', 'TransportMethodPreview', 'TransportMethodDirectly' ) as $name )
            if ( eZINI::instance( 'cjw_newsletter.ini' )->variable( 'NewsletterMailSettings', $name ) !== 'file' )
                $this->fail( 'The newsletter transport is not the file transport: the test refuses to run.' );
        $this->savedList = (array)eZDB::instance()->arrayQuery( 'SELECT * FROM cjwnl_list WHERE contentobject_id = ' . self::LIST_OBJECT_ID );
        $this->smsDir = 'var/tmp/nltest-n7-sms-' . getmypid() . '-' . ( ++self::$counter );
        $this->setIni( 'cjw_newsletter.ini', 'MailInSettings', 'MailIn', 'enabled' );
        $this->setIni( 'cjw_newsletter.ini', 'TrackingSettings', 'Tracking', 'enabled' );
        $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'Notification', 'disabled' );
        $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'ApproverUserIds', array( $this->adminId() ) );
        $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'ApproverGroupIds', array() );
        $this->setIni( 'cjw_newsletter.ini', 'ApprovalSettings', 'AllowSelfApproval', 'disabled' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsSettings', 'Sms', 'enabled' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsSettings', 'Transport', 'file' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsSettings', 'ThrottleTransport', self::THROTTLE . 'sms' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_file', 'Dir', $this->smsDir );
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_file', 'FailPattern', '' );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'Throttle', 'enabled' );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'BatchSize', '2' );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'PauseBetweenBatches', '0' );
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterCsvImportSettings', 'ImportInBackground', 'disabled' );
        CjwNewsletterSms::resetTransports();
        CjwNewsletterTracking::resetCache();
        CjwNewsletterAbTester::resetCache();
        CjwNewsletterRendering::clearCache();
        CjwNewsletterInterests::clearCache();
        CjwNewsletterMailin::$now = null;
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null && class_exists( 'CjwNewsletterSms' ) )
        {
            $this->loginAdmin();
            $db = eZDB::instance();
            foreach ( $this->createdObjectIds as $id )
            {
                foreach ( CjwNewsletterApproval::fetchList( array( 'edition_contentobject_id' => (int)$id ) ) as $approval )
                {
                    CjwNewsletterApprovalFlow::removeCollaborationItem( $approval->attribute( 'collaboration_item_id' ) );
                    $approval->remove();
                }
                eZPersistentObject::removeObject( CjwNewsletterEditionArticle::definition(), array( 'edition_contentobject_id' => (int)$id ) );
                foreach ( (array)$db->arrayQuery( 'SELECT id FROM cjwnl_edition_send WHERE edition_contentobject_id = ' . (int)$id ) as $row )
                {
                    $sendId = (int)$row['id'];
                    $db->query( "DELETE FROM cjwnl_link_click WHERE link_id IN ( SELECT id FROM cjwnl_link WHERE edition_send_id = $sendId )" );
                    $db->query( "DELETE FROM cjwnl_link WHERE edition_send_id = $sendId" );
                    $db->query( "DELETE FROM cjwnl_open WHERE edition_send_id = $sendId" );
                    $db->query( "DELETE FROM cjwnl_stat_total WHERE edition_send_id = $sendId" );
                    $db->query( "DELETE FROM cjwnl_ab_variant WHERE ab_test_id IN ( SELECT id FROM cjwnl_ab_test WHERE edition_send_id = $sendId )" );
                    $db->query( "DELETE FROM cjwnl_ab_test WHERE edition_send_id = $sendId" );
                    $db->query( "DELETE FROM cjwnl_edition_send_output WHERE edition_send_id = $sendId" );
                    $db->query( "DELETE FROM cjwnl_send_batch WHERE edition_send_id = $sendId" );
                    $db->query( "DELETE FROM cjwnl_sms_message WHERE edition_send_id = $sendId" );
                }
            }
            foreach ( $this->poolIds as $id )
            {
                $pool = CjwNewsletterArticlePool::fetch( $id );
                if ( $pool )
                    $pool->removePool();
            }
            foreach ( $this->interestIds as $id )
            {
                $db->query( 'DELETE FROM cjwnl_user_interest WHERE interest_id = ' . (int)$id );
                $db->query( 'DELETE FROM cjwnl_interest WHERE id = ' . (int)$id );
            }
            foreach ( $this->mailinIds as $id )
            {
                $db->query( 'DELETE FROM cjwnl_mailin_message WHERE mailin_address_id = ' . (int)$id );
                $db->query( 'DELETE FROM cjwnl_mailin_address WHERE id = ' . (int)$id );
            }
            foreach ( $this->urlIds as $id )
            {
                $db->query( 'DELETE FROM ezurl_object_link WHERE url_id = ' . (int)$id );
                $db->query( 'DELETE FROM ezurl WHERE id = ' . (int)$id );
            }
            foreach ( $this->runIds as $runId )
                $db->query( "DELETE FROM cjwnl_migration_log WHERE run_id = '" . $db->escapeString( $runId ) . "'" );
            $db->query( "DELETE FROM cjwnl_throttle_state WHERE transport LIKE '" . self::THROTTLE . "%'" );
            // the users of this test's own domain and what hangs on them, also the kernel's rows of their addresses
            $emails = $this->extraEmails;
            foreach ( (array)$db->arrayQuery( "SELECT id, email FROM cjwnl_user WHERE email LIKE 'nltest-n7-" . getmypid() . "-%@" . self::OWN_DOMAIN . "'" ) as $row )
            {
                $uid = (int)$row['id'];
                $emails[] = (string)$row['email'];
                $db->query( "DELETE FROM cjwnl_link_click WHERE edition_send_item_id IN ( SELECT id FROM cjwnl_edition_send_item WHERE newsletter_user_id = $uid )" );
                $db->query( "DELETE FROM cjwnl_open WHERE edition_send_item_id IN ( SELECT id FROM cjwnl_edition_send_item WHERE newsletter_user_id = $uid )" );
                $db->query( "DELETE FROM cjwnl_sms_inbound WHERE newsletter_user_id = $uid" );
            }
            $db->query( "DELETE FROM cjwnl_sms_inbound WHERE phone_number = '" . self::PHONE . "'" );
            foreach ( array_unique( $emails ) as $email )
            {
                if ( substr( $email, -strlen( self::OWN_DOMAIN ) ) !== self::OWN_DOMAIN )
                    continue;
                $key = expMailRecipient::fromAddress( $email, false )->key();
                $anon = 'x:' . substr( hash( 'sha256', $key . expMailSecret::derive( 'anonymise' ) ), 0, 32 );
                foreach ( array( $key, $anon ) as $k )
                {
                    $k = $db->escapeString( $k );
                    $db->query( "DELETE FROM expmail_preference WHERE recipient_key = '$k'" );
                    $db->query( "DELETE FROM expmail_pending WHERE recipient_key = '$k'" );
                    $db->query( "DELETE FROM expmail_consent_log WHERE recipient_key = '$k'" );
                }
                if ( expMailSuppression::isSuppressed( $email ) )
                    expMailSuppression::lift( $email );
                $db->query( "DELETE FROM expmail_consent_log WHERE recipient_key = '" . $db->escapeString( $key ) . "'" );
            }
            $this->restoreList();
            $dir = rtrim( eZSys::rootDir(), '/' ) . '/' . $this->smsDir;
            if ( $this->smsDir && is_dir( $dir ) )
            {
                foreach ( glob( $dir . '/*' ) as $file )
                    if ( is_file( $file ) )
                        unlink( $file );
                @rmdir( $dir );
            }
            foreach ( $this->files as $file )
                if ( is_file( $file ) )
                    unlink( $file );
            CjwNewsletterSms::resetTransports();
            CjwNewsletterRendering::clearCache();
            CjwNewsletterInterests::clearCache();
        }
        parent::tearDown();
    }

    // ------------------------------------------------------------------ helpers

    protected function newEmail( $label = '' )
    {
        $email = 'nltest-n7-' . getmypid() . '-' . ( ++self::$counter ) . ( $label !== '' ? '-' . $label : '' ) . '@' . self::OWN_DOMAIN;
        $this->extraEmails[] = $email;
        return $email;
    }

    protected function adminId()
    {
        return (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' );
    }

    /** Sets columns of the test list (every version); restoreList() puts them back. */
    protected function setList( array $values )
    {
        $db = eZDB::instance();
        $set = array();
        foreach ( $values as $column => $value )
            $set[] = $column . ' = ' . ( is_int( $value ) ? $value : "'" . $db->escapeString( $value ) . "'" );
        $db->query( 'UPDATE cjwnl_list SET ' . implode( ', ', $set ) . ' WHERE contentobject_id = ' . self::LIST_OBJECT_ID );
        eZContentObject::clearCache();
        CjwNewsletterRendering::clearCache();
    }

    protected function restoreList()
    {
        $db = eZDB::instance();
        $columns = array( 'approval_required', 'article_pool_id', 'tracking_mode', 'interest_source', 'language_array_string', 'main_language',
                          'skin_name_array_string', 'personalize_content', 'sms_enabled', 'sms_sender' );
        foreach ( $this->savedList as $row )
        {
            $set = array();
            foreach ( $columns as $column )
                if ( array_key_exists( $column, $row ) )
                    $set[] = $column . " = '" . $db->escapeString( (string)$row[$column] ) . "'";
            if ( $set )
                $db->query( 'UPDATE cjwnl_list SET ' . implode( ', ', $set ) . ' WHERE contentobject_attribute_id = ' . (int)$row['contentobject_attribute_id']
                            . ' AND contentobject_attribute_version = ' . (int)$row['contentobject_attribute_version'] );
        }
        $this->savedList = array();
        eZContentObject::clearCache();
    }

    protected function testList()
    {
        return CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, 0 );
    }

    protected function otherLanguage()
    {
        $main = CjwNewsletterRendering::mainLanguage( $this->testList() );
        foreach ( array( 'ger-DE', 'eng-GB', 'eng-US' ) as $locale )
            if ( $locale !== $main && eZContentLanguage::fetchByLocale( $locale ) )
                return $locale;
        $this->markTestSkipped( 'The site has only one language' );
    }

    protected function xml( $body )
    {
        return '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">' . $body . '</section>';
    }

    protected function translate( eZContentObject $object, $locale, array $texts )
    {
        $version = $object->createNewVersionIn( $locale, $object->attribute( 'initial_language_code' ) );
        $map = array();
        foreach ( $version->contentObjectAttributes( $locale ) as $attribute )
            $map[$attribute->attribute( 'contentclass_attribute_identifier' )] = $attribute;
        foreach ( $texts as $identifier => $text )
        {
            $map[$identifier]->setAttribute( 'data_text', $text );
            $map[$identifier]->store();
        }
        eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $object->attribute( 'id' ), 'version' => $version->attribute( 'version' ) ) );
        eZContentObject::clearCache();
    }

    protected function runLocked( $step )
    {
        for ( $i = 0; $i < 120; $i++ )
        {
            CjwNewsletterTracking::resetCache();
            CjwNewsletterAbTester::resetCache();
            $totals = CjwNewsletterRunner::$step( new CjwNewsletterJobOutput( false ), 'nltest' );
            if ( empty( $totals['locked'] ) )
                return $totals;
            usleep( 500000 );
        }
        $this->fail( "the runner's $step stayed locked for a minute" );
    }

    /** @return array html, text, subject of a mail file, the parts decoded */
    protected function mailParts( $file )
    {
        $raw = file_get_contents( $file );
        $out = array( 'html' => '', 'text' => '', 'subject' => '' );
        if ( preg_match( '/^Subject: (.*)$/mi', preg_replace( "/\r?\n[ \t]+/", ' ', $raw ), $m ) )
            $out['subject'] = iconv_mime_decode( trim( $m[1] ), 0, 'UTF-8' );
        $boundaries = preg_match_all( '/boundary="?([^";\r\n]+)"?/i', $raw, $bm ) ? $bm[1] : array();
        $pattern = $boundaries ? '/\r?\n--(?:' . implode( '|', array_map( function ( $b ) { return preg_quote( $b, '/' ); }, $boundaries ) ) . ')(?:--)?\r?\n/' : '/\r?\n\r?\n(?=Content-Type)/';
        foreach ( preg_split( $pattern, $raw ) as $part )
        {
            $split = preg_split( "/\r?\n\r?\n/", $part, 2 );
            if ( count( $split ) < 2 )
                continue;
            list( $head, $body ) = $split;
            $type = preg_match( '#Content-Type:\s*text/(html|plain)#i', $head, $t ) ? strtolower( $t[1] ) : '';
            if ( $type === '' )
                continue;
            if ( preg_match( '/Content-Transfer-Encoding:\s*quoted-printable/i', $head ) )
                $body = quoted_printable_decode( $body );
            else if ( preg_match( '/Content-Transfer-Encoding:\s*base64/i', $head ) )
                $body = base64_decode( $body );
            $out[$type === 'html' ? 'html' : 'text'] .= $body;
        }
        $out['html'] = html_entity_decode( $out['html'], ENT_QUOTES, 'UTF-8' );
        return $out;
    }

    /** @return string[] the mail files of the outbox that went to an address */
    protected function mailsTo( $email )
    {
        $out = array();
        foreach ( $this->outbox() as $file )
            if ( preg_match( '/^To:.*' . preg_quote( $email, '/' ) . '/mi', $this->mailText( $file ) ) )
                $out[] = $file;
        return $out;
    }

    /** @return array status, headers, body of a GET to the site, or null when it cannot be reached */
    protected function http( $url )
    {
        if ( !function_exists( 'curl_init' ) )
            return null;
        $c = curl_init( preg_replace( '#^http://#', 'https://', $url ) );
        curl_setopt_array( $c, array( CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_FOLLOWLOCATION => false,
                                      CURLOPT_TIMEOUT => 20, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0 ) );
        $response = curl_exec( $c );
        if ( $response === false )
            return null;
        $size = (int)curl_getinfo( $c, CURLINFO_HEADER_SIZE );
        $headers = array();
        foreach ( preg_split( "/\r?\n/", substr( $response, 0, $size ) ) as $line )
            if ( strpos( $line, ':' ) !== false )
            {
                list( $k, $v ) = explode( ':', $line, 2 );
                $headers[strtolower( trim( $k ) )] = trim( $v );
            }
        return array( 'status' => (int)curl_getinfo( $c, CURLINFO_HTTP_CODE ), 'headers' => $headers, 'body' => substr( $response, $size ) );
    }

    /** Waits up to 5 s for the site's request to be written (its write ends with the request). */
    protected function totalsWhen( $sendId, $type, $atLeast )
    {
        for ( $i = 0; $i < 50; $i++ )
        {
            $t = CjwNewsletterStatisticsReport::totals( $sendId );
            if ( $t[$type] >= $atLeast )
                return $t;
            usleep( 100000 );
        }
        return $t;
    }

    protected function rowCount( $sql )
    {
        $rows = eZDB::instance()->arrayQuery( $sql );
        return (int)$rows[0]['c'];
    }

    protected function itemOf( $sendId, $userId )
    {
        foreach ( CjwNewsletterEditionSendItem::fetchListSendIdAndStatus( $sendId, false, 0, 0 ) as $item )
            if ( (int)$item->attribute( 'newsletter_user_id' ) === (int)$userId )
                return $item;
        return null;
    }

    protected function newInterest( $identifier, $name )
    {
        $interest = CjwNewsletterInterest::create( array( 'list_contentobject_id' => self::LIST_OBJECT_ID, 'identifier' => 'nltest_n7_' . $identifier,
            'name' => $name, 'source' => 'topic', 'eztags_id' => 0, 'is_active' => 1, 'created' => time(), 'modified' => time() ) );
        $interest->store();
        $this->interestIds[] = (int)$interest->attribute( 'id' );
        return $interest;
    }

    /** A mail-in address of the test list on this test's domain. */
    protected function mailinAddress()
    {
        $address = CjwNewsletterMailinAddress::create( array() );
        $email = 'nltest-n7-' . getmypid() . '-' . ( ++self::$counter ) . '-in@' . self::OWN_DOMAIN;
        $errors = CjwNewsletterMailin::storeAddress( $address, array( 'email' => $email, 'plus_tag' => '', 'action' => 'both',
            'list_contentobject_id' => self::LIST_OBJECT_ID, 'mailbox_id' => 0, 'is_active' => 1 ) );
        $this->assertSame( array(), $errors );
        $this->mailinIds[] = (int)$address->attribute( 'id' );
        return $address;
    }

    protected function fixture( $name, array $values )
    {
        return strtr( (string)file_get_contents( __DIR__ . '/fixtures/deliverability/' . $name ), $values + array( '{{UNIQ}}' => uniqid( '', true ) ) );
    }

    // ------------------------------------------------------------------ IT-01

    public function testTheWholeWayOfASubscriberThroughEveryArea()
    {
        $db = eZDB::instance();
        $main = CjwNewsletterRendering::mainLanguage( $this->testList() );
        $other = $this->otherLanguage();
        $this->setList( array( 'language_array_string' => ';' . $main . ';' . $other . ';', 'personalize_content' => 1, 'skin_name_array_string' => '',
                               'interest_source' => 'topics', 'tracking_mode' => CjwNewsletterTracking::MODE_PERSON, 'approval_required' => 0 ) );
        CjwNewsletterTranslatableFields::apply();

        // 1. subscribe by e-mail: a pending subscriber and the confirmation mail; the link confirms (double opt-in)
        $address = $this->mailinAddress();
        $email = $this->newEmail( 'flow' );
        $raw = $this->fixture( 'mailin-subscribe.eml', array( '{{TO}}' => $address->attribute( 'email' ), '{{FROM}}' => $email,
                                                              '{{SUBJECT}}' => 'Subscribe', '{{BODY}}' => '', '{{EXTRA}}' => '' ) );
        $r = CjwNewsletterMailin::handleRawMessage( $raw );
        $this->assertSame( 'pending', $r['status'], json_encode( $r ) );
        $user = CjwNewsletterUser::fetchByEmail( $email );
        $this->assertInstanceOf( 'CjwNewsletterUser', $user );
        $this->assertSame( CjwNewsletterUser::STATUS_PENDING, (int)$user->attribute( 'status' ), 'the mail itself confirms nothing' );
        $confirmMails = $this->mailsTo( $email );
        $this->assertCount( 1, $confirmMails, 'the confirmation mail' );
        $this->assertStringContainsString( 'newsletter/configure/' . $user->attribute( 'hash' ), $this->mailText( $confirmMails[0] ) );
        $this->loginAnonymous();
        $view = $this->runView( 'configure', array( $user->attribute( 'hash' ) ) );
        $this->assertNotNull( $view['content'], 'the confirmation page opens' );
        $this->loginAdmin();
        $user = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterUser::STATUS_CONFIRMED, (int)$user->attribute( 'status' ), 'the link confirmed the subscriber' );
        $subscription = $this->subscriptionOf( $user );
        $this->assertContains( (int)$subscription->attribute( 'status' ), array( CjwNewsletterSubscription::STATUS_CONFIRMED, CjwNewsletterSubscription::STATUS_APPROVED ) );
        if ( (int)$subscription->attribute( 'status' ) !== CjwNewsletterSubscription::STATUS_APPROVED )
        {
            // a list that needs the approval of an administrator: approved here, as an administrator would
            $subscription->setAttribute( 'status', CjwNewsletterSubscription::STATUS_APPROVED );
            $subscription->store();
        }
        $recipient = expMailRecipient::fromAddress( $email, false );
        $prefs = expMailPreferences::forRecipient( $recipient );
        $this->assertSame( expMailPreferences::ON, $prefs->state( 'newsletter' ), 'the confirmed subscription switched the newsletter category on' );

        // 2. the e-mail preference page: interests, the language, the statistics consent
        $sport = $this->newInterest( 'sport', 'NLTEST n7 Sport' );
        $this->newInterest( 'music', 'NLTEST n7 Music' );
        $_POST = array( 'MailPreferencesForm' => 'categories', 'CategoryShown' => array( 'newsletter', 'newsletter_statistics' ),
                        'Category' => array( 'newsletter' => '1', 'newsletter_statistics' => '1' ),
                        'MailPreferencePart' => array( 'newsletter' => array( 'PartShown' => '1', 'Interest' => array( (string)$sport->attribute( 'id' ) ),
                                                                              'Language' => $other ) ) );
        $notices = \Exponential\Service\MailPreferencesPage::handlePost( $prefs, eZHTTPTool::instance(), 'link' );
        $_POST = array();
        $this->assertSame( 'success', $notices[0]['type'], json_encode( $notices ) );
        $this->assertSame( array( (int)$sport->attribute( 'id' ) ), CjwNewsletterInterests::idsForUser( $user->attribute( 'id' ) ) );
        $this->assertSame( $other, CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'language' ) );
        $prefs = expMailPreferences::forRecipient( $recipient );
        $this->assertSame( expMailPreferences::ON, $prefs->state( 'newsletter_statistics' ), 'the statistics consent' );
        CjwNewsletterTracking::resetCache();

        // 3. the mobile number, confirmed with the code
        $code = CjwNewsletterSms::requestCode( CjwNewsletterUser::fetch( $user->attribute( 'id' ) ), '+1 555 0142', true );
        $this->assertTrue( $code['ok'], (string)( $code['error'] ?? '' ) );
        $this->assertSame( self::PHONE, $code['phone'] );
        $confirmed = CjwNewsletterSms::confirmCode( CjwNewsletterUser::fetch( $user->attribute( 'id' ) ), $code['code'], expConsentContext::fromRequest( 'page', 'NLTEST n7 wording' ) );
        $this->assertTrue( $confirmed['ok'], (string)( $confirmed['error'] ?? '' ) );
        $user = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterSms::PHONE_CONFIRMED, (int)$user->attribute( 'phone_status' ) );
        $this->assertSame( 'allow', CjwNewsletterSms::decision( $user ), 'the code switched the category sms on' );

        // 4. an edition: a link, a condition for the interest, a translation, picks from the pool
        $link = 'https://www.example.invalid/n7/' . getmypid() . '/read';
        $urlId = (int)eZURL::registerURL( $link );
        $this->urlIds[] = $urlId;
        $edition = $this->newEdition( 'NLTEST n7 main title', false );
        $map = $edition->dataMap();
        $condition = '<paragraph><custom name="newsletter_condition" custom:interest="nltest_n7_sport"><paragraph>NLTEST-N7-SPORT only for sport.</paragraph></custom></paragraph>'
                   . '<paragraph><custom name="newsletter_condition" custom:interest="nltest_n7_music"><paragraph>NLTEST-N7-MUSIC only for music.</paragraph></custom></paragraph>';
        $map['description']->setAttribute( 'data_text', $this->xml( '<paragraph>MAIN intro [[first_name]] <link url_id="' . $urlId . '">NLTEST read</link></paragraph>' . $condition ) );
        $map['description']->store();
        $this->newArticle( $edition, 'NLTEST n7 own article' );
        $this->translate( eZContentObject::fetch( $edition->attribute( 'id' ) ), $other, array( 'title' => 'NLTEST n7 other title', 'short_title' => 'NLTEST n7 other',
            'description' => $this->xml( '<paragraph>OTHER intro [[first_name]] <link url_id="' . $urlId . '">NLTEST read</link></paragraph>' . $condition ) ) );
        $source = $this->newEdition( 'NLTEST n7 pool source', false );
        $sourceArticle = $this->newArticle( $source, 'NLTEST n7 picked intro' );
        $pool = CjwNewsletterArticlePool::create( array( 'name' => 'NLTEST n7 pool ' . getmypid(), 'list_contentobject_id' => self::LIST_OBJECT_ID, 'max_items' => 5,
            'parent_node_id_array_string' => ';' . (int)$source->attribute( 'main_node_id' ) . ';', 'class_identifier_array_string' => ';cjw_newsletter_article;' ) );
        $pool->store();
        $this->poolIds[] = (int)$pool->attribute( 'id' );
        $found = CjwNewsletterArticlePoolFinder::find( $pool, array( 'limit' => 5 ) );
        $foundIds = array_map( function ( $n ) { return (int)$n->attribute( 'contentobject_id' ); }, $found );
        $this->assertContains( (int)$sourceArticle->attribute( 'id' ), $foundIds, 'the pool finds the article' );
        $edition = eZContentObject::fetch( $edition->attribute( 'id' ) );
        $pick = CjwNewsletterEditionBuilder::addArticle( $edition, $sourceArticle->attribute( 'main_node' ), CjwNewsletterEditionArticle::ADDED_BY_EDITOR, (int)$pool->attribute( 'id' ) );
        $this->assertInstanceOf( 'CjwNewsletterEditionArticle', $pick );
        $copy = eZContentObject::fetchByRemoteID( CjwNewsletterEditionArticle::copyRemoteId( $edition->attribute( 'id' ), $sourceArticle->attribute( 'id' ) ) );
        if ( $copy )
            $this->createdObjectIds[] = (int)$copy->attribute( 'id' );
        $edition = eZContentObject::fetch( $edition->attribute( 'id' ) );
        $version = (int)$edition->attribute( 'current_version' );

        // 5. approval: the list needs it, the send waits for it
        $this->setList( array( 'approval_required' => 1 ) );
        $this->assertNotEmpty( CjwNewsletterEditorialHooks::sendFormValidate( eZHTTPTool::instance(), $edition->attribute( 'current' ) ), 'the send form refuses' );
        $send = $this->editionContent( $edition )->createNewsletterSendObject( time() + 86400 );
        $send->setAttribute( 'throttle_transport', self::THROTTLE );
        $send->setAttribute( 'tracking_mode', CjwNewsletterTracking::MODE_PERSON );
        $send->store();
        $sendId = (int)$send->attribute( 'id' );
        $this->assertFalse( CjwNewsletterExtensionPoints::allows( 'sendProcessAllowed', array( CjwNewsletterEditionSend::fetch( $sendId ) ) ), 'held until approved' );
        $anonymousId = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        $approval = CjwNewsletterApprovalFlow::request( $edition, $version, $anonymousId, 'NLTEST n7 please' );
        $this->assertInstanceOf( 'CjwNewsletterApproval', $approval );
        $item = eZCollaborationItem::fetch( $approval->attribute( 'collaboration_item_id' ) );
        $this->assertSame( 'waiting', expCollaborationInbox::stateOf( $item ), 'the inbox shows the request as waiting' );
        $this->assertTrue( CjwNewsletterApprovalFlow::decide( $approval, true, $this->adminId(), 'NLTEST n7 fine' ) );
        $this->assertSame( 'approved', expCollaborationInbox::stateOf( eZCollaborationItem::fetch( $approval->attribute( 'collaboration_item_id' ) ) ) );
        $this->assertTrue( CjwNewsletterEditorialHooks::sendProcessAllowed( CjwNewsletterEditionSend::fetch( $sendId ) ), 'approved: it may go' );

        // 6. the A/B subject test, then the scheduled time comes
        CjwNewsletterAbTester::create( CjwNewsletterEditionSend::fetch( $sendId ), array( 'NLTEST n7 subject B' ), 10, 'open', 4 );
        $send = CjwNewsletterEditionSend::fetch( $sendId );
        $send->setAttribute( 'mailqueue_process_scheduled', time() - 5 );
        $send->store();
        $this->runLocked( 'queueCreate' );
        $this->assertNotNull( CjwNewsletterEditionSendOutput::fetchByEditionSendIdAndLanguage( $sendId, $other ), 'the other language has its own output' );
        $test = CjwNewsletterAbTester::forSend( CjwNewsletterEditionSend::fetch( $sendId ) );
        $this->assertNotNull( $test );
        $all = $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_edition_send_item WHERE edition_send_id = $sendId" );
        $this->assertGreaterThanOrEqual( 1, $all );

        // 7. throttled sending in batches: the samples, the wait, the winner, the rest
        $this->runLocked( 'queueProcess' );
        $this->runLocked( 'queueProcess' );
        $this->assertSame( CjwNewsletterAbTester::STATUS_WAITING, (int)CjwNewsletterAbTest::fetch( $test->attribute( 'id' ) )->attribute( 'status' ), 'the samples went out, the rest waits' );
        CjwNewsletterStatisticsHooks::advanceTest( CjwNewsletterAbTest::fetch( $test->attribute( 'id' ) ), time() + 4 * 3600 + 1 );
        for ( $i = 0; $i < 100 && CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $sendId, CjwNewsletterEditionSendItem::STATUS_NEW ) > 0; $i++ )
            $this->runLocked( 'queueProcess' );
        $this->assertSame( 0, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $sendId, CjwNewsletterEditionSendItem::STATUS_NEW ), 'everything went out' );
        $batches = CjwNewsletterSendBatch::fetchList( array( 'edition_send_id' => $sendId ) );
        $this->assertGreaterThanOrEqual( 1, count( $batches ), 'in batches' );
        foreach ( $batches as $batch )
            $this->assertLessThanOrEqual( 2, (int)$batch->attribute( 'sent_count' ), 'no batch is bigger than BatchSize' );
        $states = CjwNewsletterThrottle::states();
        $this->assertArrayHasKey( self::THROTTLE, $states, 'counted under the transport of the send' );

        $mails = array_values( array_diff( $this->mailsTo( $email ), $confirmMails ) );
        $this->assertCount( 1, $mails, 'one edition mail for the subscriber (besides the confirmation mail)' );
        $m = $this->mailParts( $mails[0] );
        $this->assertStringContainsString( 'NLTEST n7', $m['subject'] );
        $this->assertStringContainsString( 'OTHER intro', $m['html'], 'his language' );
        $this->assertStringContainsString( 'NLTEST-N7-SPORT only for sport.', $m['html'] . $m['text'], 'the condition of his interest' );
        $this->assertStringNotContainsString( 'NLTEST-N7-MUSIC', $m['html'] . $m['text'], 'not the other interest' );
        $this->assertStringNotContainsString( '[[', $m['html'] . $m['text'] );
        $item = $this->itemOf( $sendId, $user->attribute( 'id' ) );
        $this->assertNotNull( $item );
        $this->assertSame( $other, (string)$item->attribute( 'language' ) );

        // 8. open and click over HTTP, per person (he agreed)
        $this->assertSame( 1, preg_match( '#(https?://[^\s"\'<>]+/newsletter/o/(p[0-9a-f]+)/([0-9a-f]{24}))#', $m['html'], $pixel ), 'a personal open pixel' );
        $probe = $this->http( $pixel[1] );
        if ( $probe === null || $probe['status'] !== 200 )
            $this->markTestSkipped( 'the site does not answer the open pixel' );
        $this->assertSame( 'image/gif', $probe['headers']['content-type'] );
        $t = $this->totalsWhen( $sendId, 'unique_open', 1 );
        if ( $t['open'] === 0 )
            $this->markTestSkipped( 'the site counts nothing: its [TrackingSettings] Tracking is not enabled' );
        $this->assertSame( 1, $t['unique_open'] );
        $click = null;
        preg_match_all( '#(https?://[^\s"\'<>]+/newsletter/r/([0-9]+)/(p[0-9a-f]+)/([0-9a-f]{24}))#', $m['html'], $clicks, PREG_SET_ORDER );
        foreach ( $clicks as $candidate )
            if ( ( $stored = CjwNewsletterLink::fetch( (int)$candidate[2] ) ) && $stored->attribute( 'url' ) === $link )
                $click = $candidate;
        $this->assertNotNull( $click, 'a personal click link of the edition\'s link among ' . count( $clicks ) );
        $redirect = $this->http( $click[1] );
        $this->assertSame( 302, $redirect['status'] );
        $this->assertSame( $link, $redirect['headers']['location'], 'the stored address, nothing from the request' );
        $this->totalsWhen( $sendId, 'click', 1 );
        $item = $this->itemOf( $sendId, $user->attribute( 'id' ) );
        $this->assertSame( 1, (int)$item->attribute( 'open_count' ) );
        $this->assertSame( 1, (int)$item->attribute( 'click_count' ) );
        $itemId = (int)$item->attribute( 'id' );
        $this->assertSame( 1, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_open WHERE edition_send_item_id = $itemId" ) );
        $this->assertSame( 1, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_link_click WHERE edition_send_item_id = $itemId" ) );

        // 9. a hard bounce: the address goes on the suppression list and the newsletter blacklist
        $mailbox = CjwNewsletterMailboxItem::addMailboxItem( 999001, 'nltest-n7-' . uniqid(), 1,
            $this->fixture( 'dsn-hard.eml', array( '{{EMAIL}}' => $email, '{{SENDITEM}}' => $item->attribute( 'hash' ), '{{USER}}' => $user->attribute( 'hash' ) ) ) );
        $mailbox->parseMail();
        @unlink( $mailbox->getFilePath() );
        $this->assertSame( 'bounce', expMailSuppression::reason( $email ) );
        $this->assertTrue( (bool)CjwNewsletterBlacklistItem::fetchByEmail( $email ), 'mirrored on the blacklist' );
        $this->assertFalse( expMailPreferences::forRecipient( $recipient )->allows( 'newsletter' ), 'no optional mail any more' );
        $this->assertNotSame( 'allow', CjwNewsletterSms::decision( CjwNewsletterUser::fetch( $user->attribute( 'id' ) ) ), 'and no SMS' );

        // 10. the subscriber export shows the state
        $csv = CjwNewsletterSubscriberExport::csv( self::LIST_OBJECT_ID, CjwNewsletterSubscriberExport::cleanFilters(
            array( 'columns' => array( 'email', 'subscription_status', 'language', 'phone_number' ) ) ) );
        $row = null;
        foreach ( preg_split( "/\r?\n/", $csv ) as $line )
            if ( strpos( $line, $email ) !== false )
                $row = $line;
        $this->assertNotNull( $row, 'the subscriber is in the export' );
        $status = (int)$this->subscriptionOf( $user )->attribute( 'status' );
        $this->assertStringContainsString( ';' . CjwNewsletterSubscriberExport::statusNames()[$status] . ';', $row . ';', $row );
        $this->assertNotSame( CjwNewsletterSubscription::STATUS_APPROVED, $status, 'the bounce changed the subscription' );
        $this->assertStringContainsString( $other, $row );
        $this->assertStringContainsString( self::PHONE, $row );

        // 11. erasure: every per-person row goes; the totals, the suppression and the blacklist entry stay
        $userId = (int)$user->attribute( 'id' );
        expMailPreferences::forRecipient( $recipient )->erase( expConsentContext::system( 'NLTEST n7 erase' ) );
        $this->assertFalse( (bool)CjwNewsletterUser::fetch( $userId ), 'the newsletter user is gone' );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_subscription WHERE newsletter_user_id = $userId" ) );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_user_interest WHERE newsletter_user_id = $userId" ) );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_sms_code WHERE newsletter_user_id = $userId" ) );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_sms_message WHERE newsletter_user_id = $userId" ) );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_sms_inbound WHERE newsletter_user_id = $userId OR phone_number = '" . self::PHONE . "'" ) );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_open WHERE edition_send_item_id = $itemId" ) );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_link_click WHERE edition_send_item_id = $itemId" ) );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_edition_send_item WHERE id = $itemId AND ( open_count > 0 OR click_count > 0 OR first_opened > 0 )" ) );
        $key = $db->escapeString( $recipient->key() );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM expmail_preference WHERE recipient_key = '$key'" ) );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM expmail_consent_log WHERE recipient_key = '$key'" ), 'the consent log is anonymised' );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM expmail_consent_log WHERE email = '" . $db->escapeString( $email ) . "'" ) );
        $this->assertTrue( expMailSuppression::isSuppressed( $email ), 'the suppression keeps blocking the address' );
        $blacklisted = CjwNewsletterBlacklistItem::fetchByEmail( $email );
        $this->assertSame( 0, (int)$blacklisted->attribute( 'newsletter_user_id' ), 'the blacklist entry stays, without the user' );
        $totals = CjwNewsletterStatisticsReport::totals( $sendId );
        $this->assertGreaterThanOrEqual( 1, $totals['open'], 'the totals stay' );
        $this->assertGreaterThanOrEqual( 1, $totals['click'] );
    }

    // ------------------------------------------------------------------ IT-02

    public function testARemovedSubscriberTakesHisRowsOfTheAreasWithHimAndTheRepairFindsOldOnes()
    {
        $this->setList( array( 'interest_source' => 'topics' ) );
        $interest = $this->newInterest( 'removal', 'NLTEST n7 removal' );
        $user = $this->newSubscriber( 'removed' );
        CjwNewsletterInterests::setForUser( $user->attribute( 'id' ), array( (int)$interest->attribute( 'id' ) ), array( (int)$interest->attribute( 'id' ) ) );
        $code = CjwNewsletterSms::requestCode( $user, '+1 555 0143', true );
        $this->assertTrue( $code['ok'], (string)( $code['error'] ?? '' ) );
        $id = (int)$user->attribute( 'id' );
        $this->assertSame( 1, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_user_interest WHERE newsletter_user_id = $id" ) );
        $this->assertGreaterThanOrEqual( 1, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_sms_code WHERE newsletter_user_id = $id" ) );
        CjwNewsletterUser::fetch( $id )->remove();
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_user_interest WHERE newsletter_user_id = $id" ), 'the interests went with him' );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_sms_code WHERE newsletter_user_id = $id" ), 'and his codes' );
        $this->assertSame( 0, $this->rowCount( "SELECT COUNT(*) AS c FROM cjwnl_sms_message WHERE newsletter_user_id = $id" ), 'and his SMS' );

        // a row an older removal left behind: the repair reports it and removes it
        $orphan = CjwNewsletterUserInterest::create( array( 'newsletter_user_id' => $id, 'interest_id' => (int)$interest->attribute( 'id' ), 'created' => time() ) );
        $orphan->store();
        $orphans = CjwNewsletterRunner::orphans();
        $this->assertContains( (int)$orphan->attribute( 'id' ), $orphans['interests'] );
        $dry = CjwNewsletterRunner::repair( false, 'nltest', true );
        $this->assertSame( count( $orphans['interests'] ), $dry['interests'] );
        $this->assertSame( 1, $this->rowCount( 'SELECT COUNT(*) AS c FROM cjwnl_user_interest WHERE id = ' . (int)$orphan->attribute( 'id' ) ), 'a dry run removes nothing' );
        if ( $orphans['subscriptions'] || $orphans['send_items'] || count( $orphans['interests'] ) > 1 )
        {
            // rows of the installation itself: never removed by a test
            eZDB::instance()->query( 'DELETE FROM cjwnl_user_interest WHERE id = ' . (int)$orphan->attribute( 'id' ) );
            $this->markTestIncomplete( 'The installation has orphans of its own; the removal itself is not run here.' );
        }
        $real = CjwNewsletterRunner::repair( false, 'nltest' );
        $this->assertSame( 1, $real['interests'] );
        $this->assertSame( 0, $this->rowCount( 'SELECT COUNT(*) AS c FROM cjwnl_user_interest WHERE id = ' . (int)$orphan->attribute( 'id' ) ) );
    }

    // ------------------------------------------------------------------ IT-03

    public function testTheEznewsletterImporterFillsAFreshListFromTheSqliteFixture()
    {
        $file = eZSys::rootDir() . '/var/tmp/nltest-n7-eznewsletter-' . getmypid() . '-' . ( ++self::$counter ) . '.sqlite';
        $this->files[] = $file;
        $pdo = new PDO( 'sqlite:' . $file, null, null, array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ) );
        foreach ( array_filter( array_map( 'trim', explode( ';', preg_replace( '/^--.*$/m', '', file_get_contents( __DIR__ . '/fixtures/cjwNewsletterImportExport-eznewsletter-1.6-sqlite.sql' ) ) ) ) ) as $statement )
            $pdo->exec( $statement );
        $insert = function ( $table, $row ) use ( $pdo ) {
            $statement = $pdo->prepare( 'INSERT INTO ' . $table . ' (' . implode( ', ', array_keys( $row ) ) . ') VALUES (' . implode( ', ', array_fill( 0, count( $row ), '?' ) ) . ')' );
            $statement->execute( array_values( $row ) );
        };
        $ann = $this->newEmail( 'oldann' );
        $bob = $this->newEmail( 'oldbob' );
        $insert( 'ezsubscription_list', array( 'id' => 1, 'name' => 'NLTEST n7 old list', 'description' => '', 'status' => 1 ) );
        $insert( 'ezsubscriptionuserdata', array( 'id' => 21, 'email' => $ann, 'firstname' => 'Ann', 'name' => 'Old', 'password' => 'x', 'hash' => 'h', 'mobile' => '' ) );
        $t = gmmktime( 10, 0, 0, 1, 2, 2019 );
        foreach ( array( 201 => array( $ann, 2 ), 202 => array( $bob, 0 ) ) as $id => $data )
            $insert( 'ezsubscription', array( 'id' => $id, 'version_status' => 1, 'subscriptionlist_id' => 1, 'email' => $data[0], 'hash' => 'h' . $id,
                'status' => $data[1], 'output_format' => '0', 'created' => $t, 'confirmed' => $data[1] ? $t : 0, 'approved' => $data[1] ? $t : 0, 'removed' => 0, 'bounce_count' => 0 ) );
        $pdo = null;
        $before = md5_file( $file );
        $system = eZContentObjectTreeNode::fetch( self::LIST_NODE_ID )->attribute( 'parent' );
        $options = array( 'create_lists' => true, 'system_node_id' => (int)$system->attribute( 'node_id' ) );

        $dry = new CjwNewsletterEznewsletterMigration( CjwNewsletterEznewsletterSource::sqliteFile( $file ), $options + array( 'dry_run' => true ) );
        $this->runIds[] = $dry->runId();
        $dryTotals = $dry->run();
        $this->assertSame( '', $dryTotals['error'] );
        $this->assertSame( 1, $dryTotals['lists']['created'] );
        $this->assertFalse( (bool)CjwNewsletterUser::fetchByEmail( $ann ), 'a dry run creates nobody' );

        $migration = new CjwNewsletterEznewsletterMigration( CjwNewsletterEznewsletterSource::sqliteFile( $file ), $options );
        $this->runIds[] = $migration->runId();
        $totals = $migration->run();
        $this->assertSame( '', $totals['error'] );
        $listId = 0;
        foreach ( CjwNewsletterMigrationLog::fetchListByRunId( $totals['run_id'] ) as $row )
            if ( $row->attribute( 'source_table' ) === 'ezsubscription_list' && (int)$row->attribute( 'target_id' ) > 0 )
                $listId = (int)$row->attribute( 'target_id' );
        $this->assertGreaterThan( 0, $listId, 'a fresh list' );
        $this->createdObjectIds[] = $listId;
        $this->assertTrue( CjwNewsletterEznewsletterMigration::isListObject( $listId ) );
        $annUser = CjwNewsletterUser::fetchByEmail( $ann );
        $this->assertIsObject( $annUser );
        $sub = CjwNewsletterSubscription::fetchByListIdAndNewsletterUserId( $listId, $annUser->attribute( 'id' ) );
        $this->assertIsObject( $sub, 'the confirmed subscription is in the fresh list' );
        $this->assertSame( 'eznewsletter:201', $sub->attribute( 'remote_id' ) );
        $this->assertFalse( (bool)CjwNewsletterUser::fetchByEmail( $bob ), 'a subscription that was never confirmed is not taken over' );
        $csv = CjwNewsletterSubscriberExport::csv( $listId, CjwNewsletterSubscriberExport::cleanFilters( array( 'columns' => array( 'email', 'subscription_status' ) ) ) );
        $this->assertStringContainsString( $ann, $csv, 'the export of the fresh list shows the subscriber' );
        $this->assertSame( $before, md5_file( $file ), 'the old tables are never changed' );
        foreach ( CjwNewsletterSubscription::fetchSubscriptionListByNewsletterUserId( $annUser->attribute( 'id' ) ) as $s )
            $s->remove();
    }
}
