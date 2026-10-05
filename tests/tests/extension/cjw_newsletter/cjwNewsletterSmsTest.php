<?php
/**
 * cjw_newsletter 4.2.0, area N5: the SMS channel.
 *
 * Phone numbers (E.164), the length counter, the file transport, the generic HTTP transport and its Twilio-style
 * preset against a stub server on 127.0.0.1 (started and stopped here), the inbound check (signatures, secrets), the
 * code double opt-in with the kernel consent, STOP, SMS sends through the queue, the throttle of area N1, the
 * hooks, the preference-page part and the views.
 *
 * Never a real provider and never a real SMS: the transports write into a directory of the test under var/tmp or
 * talk to the stub; numbers are the reserved +1 555 01xx ones, addresses nltest-*@example.invalid.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/cjwNewsletterTestCase.php';

class cjwNewsletterSmsTest extends cjwNewsletterTestCase
{
    const THROTTLE = 'nltest_sms';

    protected $smsDir = null;
    protected $savedListSms = null;
    protected $stub = null;
    protected $stubDir = null;
    protected $stubPort = 0;

    public function setUp(): void
    {
        parent::setUp();
        if ( class_exists( 'ezpLiveInstallation' ) )
            ezpLiveInstallation::requireOrSkip();
        if ( !class_exists( 'CjwNewsletterSms' ) )
            $this->markTestSkipped( 'the SMS area of cjw_newsletter is not installed' );
        $this->smsDir = 'var/tmp/nltest-sms-' . getmypid() . '-' . ( ++self::$counter );
        $this->setIni( 'cjw_newsletter.ini', 'SmsSettings', 'Sms', 'enabled' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsSettings', 'Transport', 'file' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsSettings', 'ThrottleTransport', self::THROTTLE );
        $this->setIni( 'cjw_newsletter.ini', 'SmsSettings', 'DefaultCountryCode', '49' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_file', 'Dir', $this->smsDir );
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_file', 'InboundSecret', 'nltest-secret' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_file', 'FailPattern', '' );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'Throttle', 'disabled' );
        CjwNewsletterSms::resetTransports();
    }

    public function tearDown(): void
    {
        if ( self::$bootError === null && class_exists( 'CjwNewsletterSms' ) )
        {
            $this->stopStub();
            $this->removeSmsData();
            $this->restoreListSms();
            CjwNewsletterSms::resetTransports();
        }
        parent::tearDown();
        if ( class_exists( 'CjwNewsletterSms' ) )
            CjwNewsletterSms::resetTransports();
    }

    // ------------------------------------------------------------------ helpers

    /** @return string a reserved test number +1 555 01xx */
    protected function testNumber( $n )
    {
        return '+155501' . str_pad( (string)( (int)$n % 100 ), 2, '0', STR_PAD_LEFT );
    }

    /** @return array[] the SMS the file transport wrote */
    protected function smsOutbox()
    {
        $transport = CjwNewsletterSms::transport( 'file' );
        return $transport ? $transport->outbox() : array();
    }

    /** A subscriber with a confirmed number; $consent: the kernel category sms on. */
    protected function smsSubscriber( $label, $n, $consent = true )
    {
        $user = $this->newSubscriber( $label );
        $user->setAttribute( 'phone_number', $this->testNumber( $n ) );
        $user->setAttribute( 'phone_status', CjwNewsletterSms::PHONE_CONFIRMED );
        $user->setAttribute( 'phone_confirmed', time() );
        $user->store();
        if ( $consent )
            expMailPreferences::forRecipient( CjwNewsletterSms::recipientFor( $user ) )->set( 'sms', true, expConsentContext::system( 'nltest', 'import' ) );
        return CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
    }

    protected function enableListSms( $sender = '' )
    {
        $db = eZDB::instance();
        if ( $this->savedListSms === null )
            $this->savedListSms = $db->arrayQuery( 'SELECT contentobject_attribute_id, contentobject_attribute_version, sms_enabled, sms_sender FROM cjwnl_list WHERE contentobject_id = ' . self::LIST_OBJECT_ID );
        $db->query( "UPDATE cjwnl_list SET sms_enabled = 1, sms_sender = '" . $db->escapeString( $sender ) . "' WHERE contentobject_id = " . self::LIST_OBJECT_ID );
    }

    protected function restoreListSms()
    {
        if ( $this->savedListSms === null )
            return;
        $db = eZDB::instance();
        foreach ( $this->savedListSms as $row )
            $db->query( 'UPDATE cjwnl_list SET sms_enabled = ' . (int)$row['sms_enabled'] . ", sms_sender = '" . $db->escapeString( (string)$row['sms_sender'] )
                        . "' WHERE contentobject_attribute_id = " . (int)$row['contentobject_attribute_id'] . ' AND contentobject_attribute_version = ' . (int)$row['contentobject_attribute_version'] );
        $this->savedListSms = null;
    }

    protected function removeSmsData()
    {
        $db = eZDB::instance();
        $users = $db->arrayQuery( "SELECT id, email FROM cjwnl_user WHERE email LIKE 'nltest-%@" . self::MAIL_DOMAIN . "'" );
        foreach ( (array)$users as $row )
        {
            $db->query( 'DELETE FROM cjwnl_sms_code WHERE newsletter_user_id = ' . (int)$row['id'] );
            $db->query( 'DELETE FROM cjwnl_sms_message WHERE newsletter_user_id = ' . (int)$row['id'] );
            $db->query( 'DELETE FROM cjwnl_sms_inbound WHERE newsletter_user_id = ' . (int)$row['id'] );
            $key = $db->escapeString( 'a:' . expMailSuppression::hash( strtolower( $row['email'] ) ) );
            $db->query( "DELETE FROM expmail_preference WHERE recipient_key = '$key'" );
            $db->query( "DELETE FROM expmail_pending WHERE recipient_key = '$key'" );
            $db->query( "DELETE FROM expmail_consent_log WHERE recipient_key = '$key'" );
        }
        $db->query( "DELETE FROM expmail_consent_log WHERE email LIKE 'nltest-%@" . self::MAIL_DOMAIN . "'" );
        $db->query( "DELETE FROM cjwnl_sms_inbound WHERE ( phone_number LIKE '+155501%' OR provider_message_id LIKE 'nltest-%' )" . $this->ownRows( 'cjwnl_sms_inbound' ) );
        foreach ( $this->createdObjectIds as $id )
            foreach ( (array)$db->arrayQuery( 'SELECT id FROM cjwnl_edition_send WHERE edition_contentobject_id = ' . (int)$id ) as $send )
                $db->query( 'DELETE FROM cjwnl_sms_message WHERE edition_send_id = ' . (int)$send['id'] );
        $db->query( "DELETE FROM cjwnl_throttle_state WHERE transport = '" . self::THROTTLE . "'" );
        $dir = rtrim( eZSys::rootDir(), '/' ) . '/' . $this->smsDir;
        if ( $this->smsDir && is_dir( $dir ) )
        {
            foreach ( glob( $dir . '/*' ) as $file )
                if ( is_file( $file ) )
                    unlink( $file );
            @rmdir( $dir );
        }
    }

    /** Starts PHP's built-in server with the stub router on a free port of 127.0.0.1. */
    protected function startStub( $response = null )
    {
        $this->stubDir = rtrim( eZSys::rootDir(), '/' ) . '/var/tmp/nltest-sms-stub-' . getmypid() . '-' . ( ++self::$counter );
        mkdir( $this->stubDir, 0770, true );
        if ( $response !== null )
            file_put_contents( $this->stubDir . '/response.json', json_encode( $response ) );
        $probe = stream_socket_server( 'tcp://127.0.0.1:0', $errno, $errstr );
        $this->assertNotFalse( $probe, 'a free port on 127.0.0.1' );
        $this->stubPort = (int)substr( strrchr( stream_socket_get_name( $probe, false ), ':' ), 1 );
        fclose( $probe );
        $env = array( 'CJWNL_SMS_STUB_DIR' => $this->stubDir, 'PATH' => (string)getenv( 'PATH' ) );
        $command = array( PHP_BINARY, '-S', '127.0.0.1:' . $this->stubPort, __DIR__ . '/fixtures/cjwnl_sms_stub_server.php' );
        $this->stub = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'file', $this->stubDir . '/server.log', 'a' ), 2 => array( 'file', $this->stubDir . '/server.log', 'a' ) ), $pipes, null, $env );
        $this->assertTrue( is_resource( $this->stub ), 'the stub server started' );
        for ( $i = 0; $i < 100; $i++ )
        {
            $socket = @fsockopen( '127.0.0.1', $this->stubPort, $errno, $errstr, 0.1 );
            if ( $socket )
            {
                fclose( $socket );
                return 'http://127.0.0.1:' . $this->stubPort;
            }
            usleep( 50000 );
        }
        $this->fail( 'the stub server did not answer on port ' . $this->stubPort );
    }

    protected function stopStub()
    {
        if ( is_resource( $this->stub ) )
        {
            proc_terminate( $this->stub );
            proc_close( $this->stub );
        }
        $this->stub = null;
        if ( $this->stubDir && is_dir( $this->stubDir ) )
        {
            foreach ( glob( $this->stubDir . '/*' ) as $file )
                unlink( $file );
            @rmdir( $this->stubDir );
        }
        $this->stubDir = null;
    }

    /** @return array[] the requests the stub received */
    protected function stubRequests()
    {
        $file = $this->stubDir . '/requests.jsonl';
        $out = array();
        foreach ( is_file( $file ) ? file( $file ) : array() as $line )
            $out[] = json_decode( $line, true );
        return $out;
    }

    // ------------------------------------------------------------------ numbers and length

    public function testPhoneNumbersAreNormalisedToE164AndValidated()
    {
        $this->assertSame( '+15550123', CjwNewsletterSms::normalisePhone( '+1 555 0123' ) );
        $this->assertSame( '+15550123', CjwNewsletterSms::normalisePhone( '001-555-0123' ) );
        $this->assertSame( '+4915123456789', CjwNewsletterSms::normalisePhone( '0151 / 234 567 89' ), 'a leading 0 gets the default country code' );
        $this->assertSame( '+4915123456789', CjwNewsletterSms::normalisePhone( '+49 (0)151 23456789' ), '(0) after the country code is dropped' );
        $this->assertSame( '', CjwNewsletterSms::normalisePhone( '0151 234567', '' ), 'no country code: refused' );
        $this->assertSame( '', CjwNewsletterSms::normalisePhone( '+0 555 0123' ) );
        $this->assertSame( '', CjwNewsletterSms::normalisePhone( 'call me' ) );
        $this->assertSame( '', CjwNewsletterSms::normalisePhone( '+1 555 01' ), 'too short' );
        $this->assertSame( '', CjwNewsletterSms::normalisePhone( '+1 5550 1234 5678 9012' ), 'too long' );
        $this->assertTrue( CjwNewsletterSms::validPhone( '+15550142' ) );
        $this->assertFalse( CjwNewsletterSms::validPhone( '15550142' ) );
        $this->assertSame( '+15 ••• 0142', CjwNewsletterSms::maskPhone( '+15550142' ) );
    }

    public function testTheLengthCounterKnowsGsmTheExtensionTableAndUnicode()
    {
        $this->assertSame( array( 'encoding' => 'gsm', 'units' => 0, 'segments' => 0, 'per_segment' => 160, 'remaining' => 160 ), CjwNewsletterSms::segments( '' ) );
        $one = CjwNewsletterSms::segments( str_repeat( 'a', 160 ) );
        $this->assertSame( array( 'gsm', 160, 1, 0 ), array( $one['encoding'], $one['units'], $one['segments'], $one['remaining'] ) );
        $two = CjwNewsletterSms::segments( str_repeat( 'a', 161 ) );
        $this->assertSame( array( 2, 153 ), array( $two['segments'], $two['per_segment'] ) );
        $euro = CjwNewsletterSms::segments( 'Preis 5€ [neu]' );
        $this->assertSame( 'gsm', $euro['encoding'] );
        $this->assertSame( 17, $euro['units'], '€, [ and ] count twice' );
        $this->assertSame( 'gsm', CjwNewsletterSms::segments( 'Grüße aus Köln' )['encoding'], 'ü, ß and ö are in GSM 03.38' );
        $this->assertSame( 'ucs2', CjwNewsletterSms::segments( 'ça va?' )['encoding'], 'a small ç is not' );
        $uni = CjwNewsletterSms::segments( 'Zoë ✓' );
        $this->assertSame( array( 'ucs2', 5, 1, 70 ), array( $uni['encoding'], $uni['units'], $uni['segments'], $uni['per_segment'] ) );
        $this->assertSame( 2, CjwNewsletterSms::segments( '😀' )['units'], 'a character outside the basic plane is two UCS-2 units' );
        $this->assertSame( 2, CjwNewsletterSms::segments( str_repeat( 'ä', 71 ) . 'ç' )['segments'] );
    }

    // ------------------------------------------------------------------ transports

    public function testTheFileTransportWritesEachSmsAndCanSimulateAFailure()
    {
        $transport = CjwNewsletterSms::transport( 'file' );
        $this->assertInstanceOf( 'CjwNewsletterSmsTransportFile', $transport );
        $this->assertInstanceOf( 'CjwNewsletterSmsTransport', $transport );
        $result = $transport->send( $this->testNumber( 1 ), '', 'Hello nltest' );
        $this->assertTrue( $result['ok'] );
        $this->assertStringStartsWith( 'file-', $result['id'] );
        $outbox = $this->smsOutbox();
        $this->assertCount( 1, $outbox );
        $this->assertSame( $this->testNumber( 1 ), $outbox[0]['to'] );
        $this->assertSame( 'Hello nltest', $outbox[0]['text'] );
        $this->assertSame( 'Newsletter', $outbox[0]['from'], 'the From of the group' );
        $this->assertSame( 1, $outbox[0]['segments']['segments'] );
        $this->assertStringStartsWith( rtrim( eZSys::rootDir(), '/' ) . '/var/tmp/', $outbox[0]['file'] );

        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_file', 'FailPattern', '/0102$/' );
        CjwNewsletterSms::resetTransports();
        $failed = CjwNewsletterSms::transport( 'file' )->send( $this->testNumber( 2 ), '', 'x' );
        $this->assertFalse( $failed['ok'] );
        $this->assertSame( 'simulated failure', $failed['error'] );
        $this->assertCount( 1, $this->smsOutbox() );
    }

    public function testAnUnknownTransportOrClassIsNull()
    {
        $this->assertNull( CjwNewsletterSms::transport( 'nltest_missing' ) );
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_nltestbad', 'Class', 'eZINI' );
        $this->assertNull( CjwNewsletterSms::transport( 'nltestbad' ), 'a class that is no SMS transport' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsSettings', 'Transport', 'nltest_missing' );
        $result = CjwNewsletterSms::sendTest( $this->testNumber( 3 ), 'x' );
        $this->assertFalse( $result['ok'] );
        $this->ini_removeGroup( 'SmsTransport_nltestbad' );
    }

    protected function ini_removeGroup( $group )
    {
        eZINI::instance( 'cjw_newsletter.ini' )->removeGroup( $group );
    }

    public function testTheHttpTransportMapsJsonFormsAuthenticationAndNestedFields()
    {
        $t = new CjwNewsletterSmsTransportHttp( 'nltest', array( 'Url' => 'https://sms.example.invalid/v1/{Account}/send', 'Account' => 'acc 1',
            'Auth' => 'bearer', 'AuthToken' => 'tok', 'FieldTo' => 'message.to', 'FieldText' => 'message.body', 'FieldFrom' => 'sender',
            'ExtraFields' => array( 'channel' => 'sms', 'ref' => '{Account}' ), 'Headers' => array( 'X-Test: 1', "Bad\r\nInjected: yes" ), 'From' => 'Shop' ) );
        $r = $t->buildRequest( $this->testNumber( 4 ), '', 'Hi' );
        $this->assertSame( 'POST', $r['method'] );
        $this->assertSame( 'https://sms.example.invalid/v1/acc%201/send', $r['url'], '{Key} is replaced, URL-encoded' );
        $this->assertSame( array( 'channel' => 'sms', 'ref' => 'acc%201', 'message' => array( 'to' => $this->testNumber( 4 ), 'body' => 'Hi' ), 'sender' => 'Shop' ), json_decode( $r['body'], true ) );
        $this->assertContains( 'Content-Type: application/json', $r['headers'] );
        $this->assertContains( 'Authorization: Bearer tok', $r['headers'] );
        $this->assertContains( 'X-Test: 1', $r['headers'] );
        foreach ( $r['headers'] as $h )
            $this->assertStringNotContainsString( "\n", $h, 'no header injection' );

        $form = new CjwNewsletterSmsTransportHttp( 'nltest', array( 'Url' => 'https://sms.example.invalid/send', 'Format' => 'form', 'Auth' => 'basic',
            'AuthUser' => 'u', 'AuthPassword' => 'p' ) );
        $r = $form->buildRequest( $this->testNumber( 5 ), 'NLTEST', 'a&b=c' );
        parse_str( $r['body'], $fields );
        $this->assertSame( array( 'to' => $this->testNumber( 5 ), 'from' => 'NLTEST', 'text' => 'a&b=c' ), $fields );
        $this->assertContains( 'Authorization: Basic ' . base64_encode( 'u:p' ), $r['headers'] );

        $get = new CjwNewsletterSmsTransportHttp( 'nltest', array( 'Url' => 'https://sms.example.invalid/send?key=1', 'Method' => 'GET', 'Auth' => 'header',
            'AuthHeader' => 'X-Api-Key', 'AuthHeaderValue' => 'secret' ) );
        $r = $get->buildRequest( $this->testNumber( 6 ), '', 'x y' );
        $this->assertSame( 'https://sms.example.invalid/send?key=1&to=%2B15550106&text=x+y', $r['url'] );
        $this->assertContains( 'X-Api-Key: secret', $r['headers'] );

        $insecure = new CjwNewsletterSmsTransportHttp( 'nltest', array( 'Url' => 'http://sms.example.invalid/send' ) );
        $this->assertArrayHasKey( 'error', $insecure->buildRequest( '+15550107', '', 'x' ), 'plain http only to the loopback' );
        $this->assertArrayNotHasKey( 'error', ( new CjwNewsletterSmsTransportHttp( 'nltest', array( 'Url' => 'http://127.0.0.1:9/send' ) ) )->buildRequest( '+15550107', '', 'x' ) );
        $this->assertArrayHasKey( 'error', ( new CjwNewsletterSmsTransportHttp( 'nltest', array( 'Url' => 'ftp://sms.example.invalid/' ) ) )->buildRequest( '+15550107', '', 'x' ) );
        $this->assertArrayHasKey( 'error', ( new CjwNewsletterSmsTransportHttp( 'nltest', array() ) )->buildRequest( '+15550107', '', 'x' ) );
        $this->assertSame( '***', $t->publicSettings()['AuthToken'], 'secrets are masked for the dashboard' );
    }

    public function testTheHttpTransportReadsSuccessFromTheStatusAndAJsonPath()
    {
        $t = new CjwNewsletterSmsTransportHttp( 'nltest', array( 'SuccessStatus' => '200,202', 'SuccessJsonPath' => 'result.status', 'SuccessJsonValue' => 'queued',
            'MessageIdPath' => 'result.messages.0.id', 'ErrorJsonPath' => 'error.message' ) );
        $ok = $t->interpret( array( 'status' => 202, 'body' => '{"result":{"status":"queued","messages":[{"id":"m-1"}]}}', 'error' => '' ) );
        $this->assertSame( array( 'ok' => true, 'id' => 'm-1', 'error' => '' ), $ok );
        $wrongValue = $t->interpret( array( 'status' => 200, 'body' => '{"result":{"status":"rejected"},"error":{"message":"number blocked"}}', 'error' => '' ) );
        $this->assertFalse( $wrongValue['ok'] );
        $this->assertSame( 'number blocked', $wrongValue['error'] );
        $wrongStatus = $t->interpret( array( 'status' => 201, 'body' => '{"result":{"status":"queued"}}', 'error' => '' ) );
        $this->assertFalse( $wrongStatus['ok'] );
        $this->assertSame( 'HTTP 201', $wrongStatus['error'] );
        $this->assertFalse( $t->interpret( array( 'status' => 0, 'body' => '', 'error' => 'connection refused' ) )['ok'] );
        $this->assertTrue( CjwNewsletterSmsTransportHttp::statusMatches( 204, '2xx' ) );
        $this->assertFalse( CjwNewsletterSmsTransportHttp::statusMatches( 302, '2xx,201' ) );
        $truthy = new CjwNewsletterSmsTransportHttp( 'nltest', array( 'SuccessJsonPath' => 'ok' ) );
        $this->assertTrue( $truthy->interpret( array( 'status' => 200, 'body' => '{"ok":true,"id":7}', 'error' => '' ) )['ok'] );
        $this->assertSame( '7', $truthy->interpret( array( 'status' => 200, 'body' => '{"ok":true,"id":7}', 'error' => '' ) )['id'] );
        $this->assertFalse( $truthy->interpret( array( 'status' => 200, 'body' => '{"ok":false}', 'error' => '' ) )['ok'] );
    }

    public function testTheHttpTransportTalksToALocalStubServer()
    {
        $base = $this->startStub();
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_nlteststub', 'Class', 'CjwNewsletterSmsTransportHttp' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_nlteststub', 'Url', $base . '/v1/messages' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_nlteststub', 'Auth', 'bearer' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_nlteststub', 'AuthToken', 'nltest-token' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_nlteststub', 'MessageIdPath', 'id' );
        $transport = CjwNewsletterSms::transport( 'nlteststub' );
        $result = $transport->send( $this->testNumber( 8 ), 'NLTEST', 'Stub hello' );
        $this->assertTrue( $result['ok'], 'sent: ' . $result['error'] );
        $this->assertStringStartsWith( 'stub-', $result['id'] );
        $requests = $this->stubRequests();
        $this->assertCount( 1, $requests );
        $this->assertSame( 'POST', $requests[0]['method'] );
        $this->assertSame( '/v1/messages', $requests[0]['uri'] );
        $this->assertSame( 'Bearer nltest-token', $requests[0]['headers']['authorization'] );
        $this->assertSame( array( 'to' => $this->testNumber( 8 ), 'from' => 'NLTEST', 'text' => 'Stub hello' ), json_decode( $requests[0]['body'], true ) );
        $this->assertSame( 'Authorization: ***', $transport->lastExchange['request']['headers'][2], 'the token is not kept' );

        file_put_contents( $this->stubDir . '/response.json', json_encode( array( 'status' => 400, 'body' => '{"error":"invalid number"}' ) ) );
        $failed = $transport->send( $this->testNumber( 9 ), '', 'x' );
        $this->assertFalse( $failed['ok'] );
        $this->assertSame( 'invalid number', $failed['error'] );
        eZINI::instance( 'cjw_newsletter.ini' )->removeGroup( 'SmsTransport_nlteststub' );
    }

    public function testTheTwilioStylePresetSendsAFormWithBasicAuthentication()
    {
        $base = $this->startStub( array( 'status' => 201, 'body' => '{"sid":"SMnltest0001","status":"queued"}' ) );
        $t = new CjwNewsletterSmsTransportTwilio( 'twilio', array( 'Url' => $base . '/2010-04-01/Accounts/{AccountSid}/Messages.json',
            'AccountSid' => 'ACnltest', 'AuthToken' => 'nltest-auth', 'From' => '+15550199' ) );
        $result = $t->send( $this->testNumber( 10 ), '', 'Twilio style' );
        $this->assertSame( array( 'ok' => true, 'id' => 'SMnltest0001', 'error' => '' ), $result );
        $request = $this->stubRequests()[0];
        $this->assertSame( '/2010-04-01/Accounts/ACnltest/Messages.json', $request['uri'] );
        $this->assertSame( 'Basic ' . base64_encode( 'ACnltest:nltest-auth' ), $request['headers']['authorization'] );
        $this->assertStringStartsWith( 'application/x-www-form-urlencoded', $request['headers']['content-type'] );
        parse_str( $request['body'], $fields );
        $this->assertSame( array( 'To' => $this->testNumber( 10 ), 'From' => '+15550199', 'Body' => 'Twilio style' ), $fields );

        $preset = new CjwNewsletterSmsTransportTwilio( 'twilio', array( 'AccountSid' => 'ACx', 'AuthToken' => 'y' ) );
        $this->assertSame( 'https://api.twilio.com/2010-04-01/Accounts/ACx/Messages.json', $preset->buildRequest( '+15550111', '', 'x' )['url'],
            'the preset only builds the address; nothing is sent here' );
    }

    // ------------------------------------------------------------------ inbound checks

    public function testInboundRequestsAreVerifiedPerTransport()
    {
        $body = '{"from":"+15550120","text":"STOP","id":"nltest-1"}';
        $file = CjwNewsletterSms::transport( 'file' );
        $good = array( 'url' => 'https://x.invalid/', 'params' => array(), 'headers' => array( 'x-signature' => hash_hmac( 'sha256', $body, 'nltest-secret' ) ), 'body' => $body );
        $this->assertTrue( $file->verifyInbound( $good ) );
        $this->assertTrue( $file->verifyInbound( array( 'headers' => array( 'x-signature' => 'sha256=' . hash_hmac( 'sha256', $body, 'nltest-secret' ) ) ) + $good ) );
        $this->assertFalse( $file->verifyInbound( array( 'headers' => array( 'x-signature' => hash_hmac( 'sha256', $body, 'wrong' ) ) ) + $good ), 'bad signature' );
        $this->assertFalse( $file->verifyInbound( array( 'headers' => array() ) + $good ), 'no signature' );
        $this->assertFalse( $file->verifyInbound( array( 'body' => $body . ' ' ) + $good ), 'a changed body' );
        $this->assertSame( array( 'from' => '+15550120', 'text' => 'STOP', 'id' => 'nltest-1' ), $file->parseInbound( $good ) );

        $noSecret = new CjwNewsletterSmsTransportHttp( 'nltest', array() );
        $this->assertFalse( $noSecret->verifyInbound( $good ), 'without a secret every request is refused' );
        $token = new CjwNewsletterSmsTransportHttp( 'nltest', array( 'InboundSignature' => 'token', 'InboundSecret' => 'tok-1', 'InboundFromField' => 'msisdn', 'InboundTextField' => 'message' ) );
        $this->assertTrue( $token->verifyInbound( array( 'params' => array( 'token' => 'tok-1' ) ) ) );
        $this->assertFalse( $token->verifyInbound( array( 'params' => array( 'token' => 'tok-2' ) ) ) );
        $this->assertSame( '+15550121', $token->parseInbound( array( 'params' => array( 'msisdn' => '+15550121', 'message' => 'stop' ) ) )['from'] );
        $this->assertNull( $token->parseInbound( array( 'params' => array( 'status' => 'delivered' ) ) ), 'a delivery report is no SMS' );

        $url = 'https://www.example.invalid/newsletter/sms_inbound/twilio';
        $post = array( 'From' => '+15550122', 'Body' => 'Stop', 'MessageSid' => 'SMnltest', 'AccountSid' => 'ACnltest' );
        $twilio = new CjwNewsletterSmsTransportTwilio( 'twilio', array( 'AccountSid' => 'ACnltest', 'AuthToken' => 'nltest-auth', 'InboundUrl' => $url ) );
        $signature = CjwNewsletterSmsTransportTwilio::signature( $url, $post, 'nltest-auth' );
        // the documented algorithm: base64( HMAC-SHA1( url . sorted name+value pairs ) )
        $this->assertSame( base64_encode( hash_hmac( 'sha1', $url . 'AccountSidACnltestBodyStopFrom+15550122MessageSidSMnltest', 'nltest-auth', true ) ), $signature );
        $request = array( 'url' => 'http://internal.invalid/x', 'params' => $post, 'post' => $post, 'headers' => array( 'x-twilio-signature' => $signature ), 'body' => http_build_query( $post ) );
        $this->assertTrue( $twilio->verifyInbound( $request ), 'InboundUrl is what was signed, not the internal address' );
        $this->assertFalse( $twilio->verifyInbound( array( 'post' => array( 'Body' => 'Start' ) + $post ) + $request ), 'a changed field' );
        $this->assertFalse( $twilio->verifyInbound( array( 'headers' => array( 'x-twilio-signature' => base64_encode( 'nope' ) ) ) + $request ) );
        $this->assertSame( array( 'from' => '+15550122', 'text' => 'Stop', 'id' => 'SMnltest' ), $twilio->parseInbound( $request ) );
        $answer = $twilio->inboundResponse( true );
        $this->assertSame( 200, $answer['status'] );
        $this->assertStringContainsString( '<Response></Response>', $answer['body'] );
        $this->assertSame( 403, $twilio->inboundResponse( false )['status'] );
    }

    // ------------------------------------------------------------------ code double opt-in

    public function testTheCodeDoubleOptInConfirmsTheNumberAndRecordsTheConsent()
    {
        $user = $this->newSubscriber( 'code' );
        $result = CjwNewsletterSms::requestCode( $user, '+1 555 0130', true );
        $this->assertTrue( $result['ok'], $result['error'] );
        $this->assertSame( '+15550130', $result['phone'] );
        $this->assertMatchesRegularExpression( '/^\d{6}$/', $result['code'] );
        $user = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterSms::PHONE_PENDING, (int)$user->attribute( 'phone_status' ) );
        $outbox = $this->smsOutbox();
        $this->assertCount( 1, $outbox );
        $this->assertSame( '+15550130', $outbox[0]['to'] );
        $this->assertStringContainsString( $result['code'], $outbox[0]['text'] );
        $this->assertStringContainsString( '/newsletter/sms_confirm/' . $user->attribute( 'hash' ), $outbox[0]['text'] );
        $row = CjwNewsletterSmsCode::fetchListByNewsletterUserIdAndPurpose( $user->attribute( 'id' ), 'confirm' )[0];
        $this->assertStringNotContainsString( $result['code'], $row->attribute( 'code_hash' ), 'only a hash is stored' );
        $this->assertSame( 64, strlen( $row->attribute( 'code_hash' ) ) );
        $this->assertSame( 'allow' === CjwNewsletterSms::decision( $user ), false, 'a pending number gets nothing' );

        $wrong = CjwNewsletterSms::confirmCode( $user, $result['code'] === '000000' ? '111111' : '000000' );
        $this->assertFalse( $wrong['ok'] );
        $this->assertSame( 1, (int)CjwNewsletterSmsCode::fetch( $row->attribute( 'id' ) )->attribute( 'attempts' ) );

        $context = expConsentContext::fromRequest( 'page', 'nltest wording' );
        $ok = CjwNewsletterSms::confirmCode( $user, ' ' . substr( $result['code'], 0, 3 ) . ' ' . substr( $result['code'], 3 ), $context );
        $this->assertTrue( $ok['ok'], $ok['error'] );
        $user = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterSms::PHONE_CONFIRMED, (int)$user->attribute( 'phone_status' ) );
        $this->assertGreaterThan( 0, (int)$user->attribute( 'phone_confirmed' ) );
        $recipient = CjwNewsletterSms::recipientFor( $user );
        $prefs = expMailPreferences::forRecipient( $recipient );
        $this->assertSame( 'on', $prefs->state( 'sms' ), 'the code switched the category on' );
        $log = expConsentLog::fetchForRecipient( $recipient );
        $confirm = null;
        foreach ( $log as $entry )
            if ( $entry->attribute( 'category' ) === 'sms' && $entry->attribute( 'action' ) === 'confirm' )
                $confirm = $entry;
        $this->assertNotNull( $confirm, 'the consent log has the confirmation' );
        $this->assertSame( 'confirm', $confirm->attribute( 'source' ) );
        $this->assertSame( 'nltest wording', $confirm->attribute( 'wording' ), 'the wording shown is recorded' );
        $this->assertSame( 'allow', CjwNewsletterSms::decision( $user ) );
        $this->assertFalse( CjwNewsletterSms::confirmCode( $user, $result['code'] )['ok'], 'a code works once' );
    }

    public function testCodesExpireLimitTheAttemptsAndTheRequests()
    {
        $this->setIni( 'cjw_newsletter.ini', 'SmsSettings', 'CodeMaxAttempts', '2' );
        $this->setIni( 'cjw_newsletter.ini', 'SmsSettings', 'CodeMaxPerHour', '2' );
        $user = $this->newSubscriber( 'limits' );
        $first = CjwNewsletterSms::requestCode( $user, $this->testNumber( 31 ), true );
        $second = CjwNewsletterSms::requestCode( $user, $this->testNumber( 31 ), true );
        $this->assertTrue( $second['ok'] );
        $this->assertFalse( CjwNewsletterSms::confirmCode( $user, $first['code'] === $second['code'] ? '00000000' : $first['code'] )['ok'], 'the older code is void' );
        $third = CjwNewsletterSms::requestCode( $user, $this->testNumber( 31 ), true );
        $this->assertFalse( $third['ok'], 'two codes per hour' );
        $this->assertCount( 2, $this->smsOutbox() );
        $this->assertFalse( CjwNewsletterSms::confirmCode( $user, '99999999' )['ok'] );
        $blocked = CjwNewsletterSms::confirmCode( $user, $second['code'] );
        $this->assertFalse( $blocked['ok'], 'after two wrong entries even the right code is refused' );

        $other = $this->newSubscriber( 'expired' );
        $code = CjwNewsletterSms::requestCode( $other, $this->testNumber( 32 ), true );
        $db = eZDB::instance();
        $db->query( 'UPDATE cjwnl_sms_code SET expires = ' . ( time() - 1 ) . ' WHERE newsletter_user_id = ' . (int)$other->attribute( 'id' ) );
        $this->assertFalse( CjwNewsletterSms::confirmCode( CjwNewsletterUser::fetch( $other->attribute( 'id' ) ), $code['code'] )['ok'], 'expired' );
        $this->assertFalse( CjwNewsletterSms::requestCode( $other, 'not a number' )['ok'] );
    }

    public function testACodeThatCannotBeSentIsVoid()
    {
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_file', 'FailPattern', '/0133$/' );
        CjwNewsletterSms::resetTransports();
        $user = $this->newSubscriber( 'failcode' );
        $result = CjwNewsletterSms::requestCode( $user, $this->testNumber( 33 ), true );
        $this->assertFalse( $result['ok'] );
        $row = CjwNewsletterSmsCode::fetchListByNewsletterUserIdAndPurpose( $user->attribute( 'id' ), 'confirm' )[0];
        $this->assertLessThan( time(), (int)$row->attribute( 'expires' ) );
    }

    // ------------------------------------------------------------------ STOP

    public function testStopByTheInboundEndpointStopsTheNumberAndABadSignatureChangesNothing()
    {
        $user = $this->smsSubscriber( 'stop', 40 );
        $this->assertSame( 'allow', CjwNewsletterSms::decision( $user ) );
        $body = json_encode( array( 'from' => '+1 555 0140', 'text' => 'stop please', 'id' => 'nltest-stop-1' ) );

        $bad = CjwNewsletterSms::handleInbound( 'file', array( 'url' => '', 'params' => array(), 'headers' => array( 'x-signature' => str_repeat( '0', 64 ) ), 'body' => $body ) );
        $this->assertFalse( $bad['verified'] );
        $this->assertSame( 403, $bad['response']['status'] );
        $this->assertSame( 0, CjwNewsletterSmsInbound::fetchListCount( array( 'provider_message_id' => 'nltest-stop-1' ) ), 'nothing stored' );
        $this->assertSame( 'allow', CjwNewsletterSms::decision( CjwNewsletterUser::fetch( $user->attribute( 'id' ) ) ), 'nothing changed' );
        $this->assertSame( 403, CjwNewsletterSms::handleInbound( 'nltest_missing', array( 'body' => $body ) )['response']['status'], 'an unknown transport' );

        $good = CjwNewsletterSms::handleInbound( 'file', array( 'url' => '', 'params' => array(), 'headers' => array( 'x-signature' => hash_hmac( 'sha256', $body, 'nltest-secret' ) ), 'body' => $body ) );
        $this->assertTrue( $good['verified'] );
        $this->assertSame( 'stop', $good['action'] );
        $this->assertSame( 200, $good['response']['status'] );
        $user = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterSms::PHONE_STOPPED, (int)$user->attribute( 'phone_status' ) );
        $this->assertSame( 'off', expMailPreferences::forRecipient( CjwNewsletterSms::recipientFor( $user ) )->state( 'sms' ) );
        $this->assertSame( 'stopped', CjwNewsletterSms::decision( $user ) );
        $inbound = CjwNewsletterSmsInbound::fetch( $good['inbound_id'] );
        $this->assertSame( array( '+15550140', 'STOP', 'stop', (int)$user->attribute( 'id' ) ),
            array( $inbound->attribute( 'phone_number' ), $inbound->attribute( 'keyword' ), $inbound->attribute( 'action' ), (int)$inbound->attribute( 'newsletter_user_id' ) ) );
        $again = CjwNewsletterSms::handleInbound( 'file', array( 'url' => '', 'params' => array(), 'headers' => array( 'x-signature' => hash_hmac( 'sha256', $body, 'nltest-secret' ) ), 'body' => $body ) );
        $this->assertTrue( !empty( $again['duplicate'] ), 'a retried delivery of the provider is stored once' );

        $other = CjwNewsletterSms::receive( '+15550141', 'Hello there', 'nltest-other' );
        $this->assertSame( 'ignored', $other['action'] );
        $this->assertSame( 'STOPP', CjwNewsletterSmsInbound::fetch( CjwNewsletterSms::receive( '+15550142', 'Stopp!', 'nltest-de' )['inbound_id'] )->attribute( 'keyword' ) );
    }

    // ------------------------------------------------------------------ SMS sends

    public function testAnSmsSendGoesOnlyToConfirmedNumbersWithConsentAndNeverByMail()
    {
        $this->enableListSms( 'NLTEST' );
        $allowed = $this->smsSubscriber( 'smsok', 50 );
        $noConsent = $this->smsSubscriber( 'smsnoconsent', 51, false );
        $pending = $this->newSubscriber( 'smspending' );
        CjwNewsletterSms::requestCode( $pending, $this->testNumber( 52 ) );
        $plain = $this->newSubscriber( 'smsplain' );
        $before = count( $this->smsOutbox() );

        $send = CjwNewsletterSms::createSend( $this->editionContent( $this->newEdition() ), 'Hello [[first_name]], news!' );
        $this->assertInstanceOf( 'CjwNewsletterEditionSend', $send );
        $this->assertSame( 'sms', $send->attribute( 'channel' ) );
        $this->assertSame( 'Hello [[first_name]], news!', CjwNewsletterSms::sendText( $send->attribute( 'id' ) ) );
        $this->assertFalse( CjwNewsletterSmsHooks::sendProcessAllowed( $send ), 'the mail runner leaves it alone' );

        $create = CjwNewsletterRunner::queueCreate( new CjwNewsletterJobOutput( false ), 'nltest' );
        $this->assertTrue( $create['ok'] );
        $messages = CjwNewsletterSmsMessage::fetchList( array( 'edition_send_id' => $send->attribute( 'id' ), 'status' => CjwNewsletterSms::STATUS_NEW ) );
        $this->assertCount( 1, $messages, 'one SMS: only the confirmed number with consent' );
        $this->assertSame( (int)$allowed->attribute( 'id' ), (int)$messages[0]->attribute( 'newsletter_user_id' ) );
        $this->assertSame( "Hello Test, news!\n" . CjwNewsletterSms::stopHint(), $messages[0]->attribute( 'body' ) );
        $db = eZDB::instance();
        foreach ( array( 'no consent' => $noConsent, 'pending' => $pending, 'no number' => $plain ) as $label => $other )
        {
            $rows = $db->arrayQuery( 'SELECT status FROM cjwnl_edition_send_item WHERE edition_send_id = ' . (int)$send->attribute( 'id' ) . ' AND newsletter_user_id = ' . (int)$other->attribute( 'id' ) );
            $this->assertNotEmpty( $rows, $label . ': the queue made an item' );
            foreach ( $rows as $row )
                $this->assertSame( CjwNewsletterEditionSendItem::STATUS_ABORT, (int)$row['status'], $label . ': the item is closed' );
        }

        $mailsBefore = count( $this->outbox() );
        $process = CjwNewsletterRunner::queueProcess( new CjwNewsletterJobOutput( false ), 'nltest' );
        $this->assertTrue( $process['ok'] );
        $this->assertSame( $mailsBefore, count( $this->outbox() ), 'no mail for an SMS send' );
        $outbox = array_slice( $this->smsOutbox(), $before );
        $this->assertCount( 1, $outbox );
        $this->assertSame( $this->testNumber( 50 ), $outbox[0]['to'] );
        $this->assertSame( 'NLTEST', $outbox[0]['from'], 'the sender of the list' );
        $message = CjwNewsletterSmsMessage::fetch( $messages[0]->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterSms::STATUS_SENT, (int)$message->attribute( 'status' ) );
        $this->assertSame( $outbox[0]['id'], $message->attribute( 'provider_message_id' ) );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );
        $this->assertSame( 0, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW ) );
        $this->assertGreaterThanOrEqual( 1, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_SEND ) );
    }

    public function testAnSmsSendNeedsTheListSwitchAndAShortEnoughText()
    {
        $edition = $this->editionContent( $this->newEdition() );
        $db = eZDB::instance();
        $this->enableListSms();
        $db->query( 'UPDATE cjwnl_list SET sms_enabled = 0 WHERE contentobject_id = ' . self::LIST_OBJECT_ID );
        $this->assertIsString( CjwNewsletterSms::createSend( $edition, 'x' ), 'the list does not allow SMS' );
        $this->enableListSms();
        $this->assertIsString( CjwNewsletterSms::createSend( $edition, '   ' ) );
        $this->assertIsString( CjwNewsletterSms::createSend( $edition, str_repeat( 'a', 153 * 3 ) ), 'more than MaxSegments parts' );
        $this->assertSame( array(), CjwNewsletterSms::validateText( str_repeat( 'a', 100 ) ) );
    }

    public function testAStopBetweenQueueAndSendingIsRespected()
    {
        $this->enableListSms();
        $user = $this->smsSubscriber( 'latestop', 53 );
        $send = CjwNewsletterSms::createSend( $this->editionContent( $this->newEdition() ), 'Late stop' );
        CjwNewsletterRunner::queueCreate( new CjwNewsletterJobOutput( false ), 'nltest' );
        $this->assertSame( 1, CjwNewsletterSmsMessage::fetchListCount( array( 'edition_send_id' => $send->attribute( 'id' ), 'status' => CjwNewsletterSms::STATUS_NEW ) ) );
        CjwNewsletterSms::receive( $this->testNumber( 53 ), 'STOP', 'nltest-late' );
        $this->assertSame( 0, CjwNewsletterSmsMessage::fetchListCount( array( 'edition_send_id' => $send->attribute( 'id' ), 'status' => CjwNewsletterSms::STATUS_NEW ) ) );
        $before = count( $this->smsOutbox() );
        CjwNewsletterRunner::queueProcess( new CjwNewsletterJobOutput( false ), 'nltest' );
        $this->assertCount( $before, $this->smsOutbox(), 'nothing went out' );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );
    }

    public function testTheSmsQueueKeepsToTheThrottleOfAreaN1()
    {
        if ( !class_exists( 'CjwNewsletterThrottle' ) )
            $this->markTestSkipped( 'the throttle of area N1 is not installed' );
        $this->enableListSms();
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'Throttle', 'enabled' );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'MaxPerMinute', array( self::THROTTLE => '1' ) );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'MaxPerHour', array( self::THROTTLE => '0' ) );
        $this->smsSubscriber( 'throttle1', 60 );
        $this->smsSubscriber( 'throttle2', 61 );
        $send = CjwNewsletterSms::createSend( $this->editionContent( $this->newEdition() ), 'Throttled' );
        CjwNewsletterRunner::queueCreate( new CjwNewsletterJobOutput( false ), 'nltest' );
        $this->assertSame( 2, CjwNewsletterSmsMessage::fetchListCount( array( 'edition_send_id' => $send->attribute( 'id' ), 'status' => CjwNewsletterSms::STATUS_NEW ) ) );
        $totals = CjwNewsletterSms::processQueue();
        $this->assertSame( 1, $totals['sent'], 'one per minute' );
        $this->assertSame( 1, $totals['deferred'] );
        $this->assertSame( 1, CjwNewsletterSmsMessage::fetchListCount( array( 'edition_send_id' => $send->attribute( 'id' ), 'status' => CjwNewsletterSms::STATUS_NEW ) ) );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_STARTED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ), 'resumed by a later run' );
        $this->assertSame( 0, CjwNewsletterThrottle::acquire( self::THROTTLE, 5 ), 'the throttle counted the SMS' );
        $this->assertSame( 0, CjwNewsletterSms::processQueue()['sent'], 'nothing more in this minute' );
        $this->assertFalse( CjwNewsletterSms::requestCode( $this->newSubscriber( 'throttlecode' ), $this->testNumber( 62 ) )['ok'], 'a code also waits for the rate' );

        CjwNewsletterThrottle::pause( self::THROTTLE, time() + 600 );
        $this->setIni( 'cjw_newsletter.ini', 'ThrottleSettings', 'Throttle', 'disabled' );
        $this->assertSame( 0, CjwNewsletterSms::processQueue()['sent'], 'an admin pause holds the SMS too' );
        CjwNewsletterThrottle::resume( self::THROTTLE );
        $this->assertSame( 1, CjwNewsletterSms::processQueue()['sent'] );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );
    }

    public function testAFailedSmsIsRecordedAndTheSendStillFinishes()
    {
        $this->enableListSms();
        $this->smsSubscriber( 'fail1', 70 );
        $this->setIni( 'cjw_newsletter.ini', 'SmsTransport_file', 'FailPattern', '/0170$/' );
        CjwNewsletterSms::resetTransports();
        $send = CjwNewsletterSms::createSend( $this->editionContent( $this->newEdition() ), 'Failing' );
        CjwNewsletterRunner::queueCreate( new CjwNewsletterJobOutput( false ), 'nltest' );
        $totals = CjwNewsletterSms::processQueue();
        $this->assertSame( 1, $totals['failed'] );
        $rows = CjwNewsletterSmsMessage::fetchList( array( 'edition_send_id' => $send->attribute( 'id' ), 'status' => CjwNewsletterSms::STATUS_FAILED ) );
        $this->assertCount( 1, $rows );
        $this->assertSame( 'simulated failure', $rows[0]->attribute( 'error' ) );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );
        $summary = CjwNewsletterSms::summary();
        $this->assertGreaterThanOrEqual( 1, $summary['messages']['failed'] );
        $codes = array();
        foreach ( $summary['problems'] as $problem )
            $codes[] = $problem['code'];
        $this->assertContains( 'sms_failed', $codes );
    }

    // ------------------------------------------------------------------ hooks and parts

    public function testTheListPartSetsTheSmsColumns()
    {
        $list = new CjwNewsletterList( array() );
        $http = eZHTTPTool::instance();
        $_POST = array();
        $this->assertSame( array(), CjwNewsletterSmsHooks::listAttributeInput( $list, $http, 'b_CjwNewsletterList_', '_7', null ), 'without the part nothing changes' );
        $_POST = array( 'b_CjwNewsletterList_SmsPart_7' => '1', 'b_CjwNewsletterList_SmsEnabled_7' => '1', 'b_CjwNewsletterList_SmsSender_7' => 'Shop24' );
        $this->assertSame( array(), CjwNewsletterSmsHooks::listAttributeInput( $list, $http, 'b_CjwNewsletterList_', '_7', null ) );
        $this->assertSame( 1, (int)$list->attribute( 'sms_enabled' ) );
        $this->assertSame( 'Shop24', $list->attribute( 'sms_sender' ) );
        $_POST = array( 'b_CjwNewsletterList_SmsPart_7' => '1', 'b_CjwNewsletterList_SmsSender_7' => 'a very long sender name' );
        $this->assertCount( 1, CjwNewsletterSmsHooks::listAttributeInput( $list, $http, 'b_CjwNewsletterList_', '_7', null ) );
        $this->assertSame( 0, (int)$list->attribute( 'sms_enabled' ) );
        $_POST = array();
        $this->assertSame( '', CjwNewsletterSmsHooks::senderError( '+15550199' ) );
        $this->assertSame( '', CjwNewsletterSmsHooks::senderError( 'News 1' ) );
        $this->assertNotSame( '', CjwNewsletterSmsHooks::senderError( '12345' ), 'a name needs a letter' );
        $this->assertNotSame( '', CjwNewsletterSmsHooks::senderError( '+0123' ) );
    }

    public function testTheAdminUserPartAndTheSubscribePartTakeTheNumber()
    {
        $http = eZHTTPTool::instance();
        $user = $this->newSubscriber( 'adminpart' );
        $_POST = array( 'CjwNewsletterSmsPart' => '1', 'CjwNewsletterSmsPhone' => 'nonsense' );
        $this->assertCount( 1, CjwNewsletterSmsHooks::userInput( $user, $http ) );
        $_POST = array( 'CjwNewsletterSmsPart' => '1', 'CjwNewsletterSmsPhone' => '+1 555 0180', 'CjwNewsletterSmsSendCode' => '1' );
        $this->assertSame( array(), CjwNewsletterSmsHooks::userInput( $user, $http ) );
        $this->assertSame( '+15550180', $user->attribute( 'phone_number' ) );
        $this->assertSame( CjwNewsletterSms::PHONE_NONE, (int)$user->attribute( 'phone_status' ), 'an admin cannot confirm a number' );
        $user->store();
        CjwNewsletterSmsHooks::userStored( $user, $http );
        CjwNewsletterUI::takeNotices();
        $this->assertSame( CjwNewsletterSms::PHONE_PENDING, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'phone_status' ), 'the code went to the person' );
        $_POST = array( 'CjwNewsletterSmsPart' => '1', 'CjwNewsletterSmsPhone' => '' );
        $user = CjwNewsletterUser::fetch( $user->attribute( 'id' ) );
        CjwNewsletterSmsHooks::userInput( $user, $http );
        $this->assertSame( '', $user->attribute( 'phone_number' ) );

        $_POST = array( 'CjwNewsletterSmsPhone' => 'x' );
        $this->assertCount( 1, CjwNewsletterSmsHooks::subscribeValidate( $http ) );
        $_POST = array( 'CjwNewsletterSmsPhone' => '' );
        $this->assertSame( array(), CjwNewsletterSmsHooks::subscribeValidate( $http ), 'the number is optional' );
        $new = $this->newSubscriber( 'subscribepart' );
        $_POST = array( 'CjwNewsletterSmsPhone' => '+1 555 0181' );
        CjwNewsletterSmsHooks::subscribeInput( $new, $http );
        $new = CjwNewsletterUser::fetch( $new->attribute( 'id' ) );
        $this->assertSame( array( '+15550181', CjwNewsletterSms::PHONE_PENDING ), array( $new->attribute( 'phone_number' ), (int)$new->attribute( 'phone_status' ) ) );
        $_POST = array();
    }

    public function testThePreferencePagePartSendsTheCodeAndConfirmsIt()
    {
        if ( !CjwNewsletterSms::categoryAvailable() )
            $this->markTestSkipped( 'the mail-preference category sms is not registered' );
        $user = $this->newSubscriber( 'prefpage' );
        $recipient = CjwNewsletterSms::recipientFor( $user );
        $category = expMailCategoryRegistry::instance()->get( 'sms' );
        $handler = $category->handler();
        $this->assertInstanceOf( 'CjwNewsletterSmsCategoryHandler', $handler );
        $this->assertFalse( $category->doubleOptIn, 'the double opt-in is the SMS code, not the e-mail link' );
        $this->assertSame( 'design:mailpreferences/category/sms.tpl', $handler->partTemplate( $recipient, $category, 'account' ) );
        $vars = $handler->partVariables( $recipient, $category, 'account' );
        $this->assertSame( array( true, true, 'none' ), array( $vars['enabled'], $vars['has_user'], $vars['status'] ) );

        $http = eZHTTPTool::instance();
        $context = expConsentContext::fromRequest( 'page', 'nltest' );
        $_POST = array( 'MailPreferencePart' => array( 'sms' => array( 'Phone' => 'bad' ) ) );
        $this->assertCount( 1, $handler->storePart( $recipient, $category, $http, $context ) );
        // the category is still off: the number is kept, no code yet
        $_POST = array( 'MailPreferencePart' => array( 'sms' => array( 'Phone' => '+1 555 0190' ) ) );
        $this->assertSame( array( 'changed' => 1 ), $handler->storePart( $recipient, $category, $http, $context ) );
        $this->assertCount( 0, $this->smsOutbox() );
        // switched on with the page, then the part sends the code
        $prefs = expMailPreferences::forRecipient( $recipient );
        $prefs->set( 'sms', true, $context );
        $this->assertSame( array( 'changed' => 1 ), $handler->storePart( $recipient, $category, $http, $context ) );
        $outbox = $this->smsOutbox();
        $this->assertCount( 1, $outbox );
        $this->assertSame( '+15550190', $outbox[0]['to'] );
        preg_match( '/: (\d{6})/', $outbox[0]['text'], $m );
        $this->assertNotEmpty( $m, 'the code is in the SMS' );
        $vars = $handler->partVariables( $recipient, $category, 'account' );
        $this->assertSame( array( 'pending', true ), array( $vars['status'], $vars['code_waiting'] ) );
        $_POST = array( 'MailPreferencePart' => array( 'sms' => array( 'Phone' => '+15550190', 'Code' => $m[1] ) ) );
        $this->assertSame( array( 'changed' => 1 ), $handler->storePart( $recipient, $category, $http, $context ) );
        $this->assertSame( 'confirmed', $handler->partVariables( $recipient, $category, 'account' )['status'] );
        $this->assertSame( 'allow', CjwNewsletterSms::decision( CjwNewsletterUser::fetch( $user->attribute( 'id' ) ) ) );
        // switching the category off keeps the confirmed number but stops the SMS
        $prefs->set( 'sms', false, $context );
        $this->assertSame( 'off', CjwNewsletterSms::decision( CjwNewsletterUser::fetch( $user->attribute( 'id' ) ) ) );
        $_POST = array( 'MailPreferencePart' => array( 'sms' => array( 'Phone' => '' ) ) );
        $this->assertSame( array( 'changed' => 1 ), $handler->storePart( $recipient, $category, $http, $context ) );
        $this->assertSame( '', CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'phone_number' ) );
        $_POST = array();

        // the page stores the form in a transaction: the code SMS waits until the page asks for the part afterwards
        $_POST = array( 'MailPreferencePart' => array( 'sms' => array( 'Phone' => '+15550192' ) ) );
        $prefs->set( 'sms', true, $context );
        $before = count( $this->smsOutbox() );
        $db = eZDB::instance();
        $db->begin();
        $this->assertSame( array( 'changed' => 2 ), $handler->storePart( $recipient, $category, $http, $context ) );
        $this->assertCount( $before, $this->smsOutbox(), 'nothing is sent inside the transaction' );
        $db->commit();
        $vars = $handler->partVariables( $recipient, $category, 'account' );
        $this->assertTrue( $vars['code_sent'] );
        $this->assertSame( 'pending', $vars['status'] );
        $this->assertCount( $before + 1, $this->smsOutbox() );
        $_POST = array();

        $stranger = expMailRecipient::fromAddress( $this->newEmail( 'nosub' ) );
        $_POST = array( 'MailPreferencePart' => array( 'sms' => array( 'Phone' => '+15550191' ) ) );
        $this->assertCount( 1, $handler->storePart( $stranger, $category, $http, $context ), 'no newsletter user, no number' );
        $this->assertFalse( $handler->partVariables( $stranger, $category, 'account' )['has_user'] );
        $_POST = array();
    }

    public function testTheDashboardSummaryOfTheArea()
    {
        $summary = CjwNewsletterSmsHooks::dashboardSummary( array() );
        foreach ( array( 'enabled', 'transport', 'transport_ok', 'simulated', 'category', 'category_ok', 'consents', 'messages', 'phones', 'stops', 'sends', 'problems' ) as $key )
            $this->assertArrayHasKey( $key, $summary );
        $this->assertTrue( $summary['simulated'] );
        $this->setIni( 'cjw_newsletter.ini', 'SmsSettings', 'Transport', 'nltest_missing' );
        $codes = array();
        foreach ( CjwNewsletterSms::summary()['problems'] as $problem )
            $codes[] = $problem['code'];
        $this->assertContains( 'sms_transport', $codes );
        $dashboard = CjwNewsletterDashboard::summary();
        $this->assertArrayHasKey( 'CjwNewsletterSmsHooks', $dashboard['areas'] );
    }

    // ------------------------------------------------------------------ views

    public function testTheSendViewShowsTheCounterAndCreatesAnSmsSend()
    {
        $this->enableListSms();
        $edition = $this->newEdition();
        $nodeId = (int)$edition->attribute( 'main_node_id' );
        $r = $this->runView( 'sms_send', array( $nodeId ) );
        $this->assertViewOk( $r, 'sms_send' );
        $this->assertStringContainsString( 'data-nl-sms-counter', $r['content'] );
        $this->assertStringContainsString( 'name="SmsText"', $r['content'] );
        $r = $this->runView( 'sms_send', array( $nodeId ), array( 'SmsText' => 'View send [[name]]', 'SmsSendButton' => '1' ) );
        $this->assertNotEmpty( $r['redirect'], 'redirect after the send was made' );
        CjwNewsletterUI::takeNotices();
        $sends = eZDB::instance()->arrayQuery( "SELECT id FROM cjwnl_edition_send WHERE channel = 'sms' AND edition_contentobject_id = " . (int)$edition->attribute( 'id' ) );
        $this->assertCount( 1, $sends );
        $this->assertSame( 'View send [[name]]', CjwNewsletterSms::sendText( $sends[0]['id'] ) );
        $r = $this->runView( 'sms_send', array( $nodeId ), array( 'SmsText' => 'Test text', 'SmsTestPhone' => '+1 555 0195', 'SmsTestButton' => '1' ) );
        $this->assertViewOk( $r );
        $last = $this->smsOutbox();
        $this->assertSame( '+15550195', end( $last )['to'] );
        $r = $this->runView( 'sms_send', array( 999999999 ) );
        $this->assertViewClean( $r );
    }

    public function testTheConfirmViewTakesTheCode()
    {
        $user = $this->newSubscriber( 'confirmview' );
        $code = CjwNewsletterSms::requestCode( $user, $this->testNumber( 96 ), true );
        $hash = $user->attribute( 'hash' );
        $r = $this->runView( 'sms_confirm', array( $hash ) );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'autocomplete="one-time-code"', $r['content'] );
        $this->assertStringNotContainsString( $this->testNumber( 96 ), $r['content'], 'the number is masked' );
        $r = $this->runView( 'sms_confirm', array( $hash ), array( 'SmsCode' => '00000000', 'SmsConfirmButton' => '1' ) );
        $this->assertStringContainsString( 'message-warning', $r['content'] );
        $r = $this->runView( 'sms_confirm', array( $hash ), array( 'SmsCode' => $code['code'], 'SmsConfirmButton' => '1' ) );
        $this->assertStringContainsString( 'message-feedback', $r['content'] );
        $this->assertSame( CjwNewsletterSms::PHONE_CONFIRMED, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'phone_status' ) );
        $this->assertViewClean( $this->runView( 'sms_confirm', array( 'not-a-hash' ) ) );
    }
}
