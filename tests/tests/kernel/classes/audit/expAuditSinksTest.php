<?php
/**
 * The audit sinks (doc/bc/6.0/audit.md, "Sinks (Q7)", acceptance test E1).
 *
 *  SK-01 — syslog: the RFC 5424 message (PRI = facility * 8 + severity, MSGID the channel, the exp@32473 element
 *          with escaped values, the record as MSG); udp carries no body; the journald datagram has the identifier
 *  SK-02 — syslog to journald for real: two test records under a test identifier are found by journalctl -t
 *  SK-03 — webhook: records are spooled at flush (a request never waits), delivered in batches of BatchSize to a
 *          receiver on 127.0.0.1 (ai/bin/one/audit_stage5_webhook_test_receiver.php, started and stopped here) that
 *          checks every signature; ids arrive once, in order
 *  SK-04 — webhook outage: a forced failure keeps the batch spooled, waits RetryBackoff (doubled), records
 *          system.audit.sink.failed after Retries, and delivers everything once the receiver is back
 *  SK-05 — the receiver's check: a wrong secret is bad_signature, an old timestamp stale_timestamp
 *  SK-06 — mail: an alert reaches the mail sink through its rule's Sinks[], is spooled, then sent through the
 *          transport (a test transport writing into the test directory: no real mail); a second alert of the
 *          same rule and group within Throttle is not mailed
 *  SK-07 — under test settings nothing leaves the process unless the test allows sinks
 *
 * Writes only into var/tmp/audit-tests/ (and two test lines to the journal in SK-02); no database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

/** The mail transport of the tests: writes each mail into the test directory. */
class expAuditTestMailTransport extends eZMailTransport
{
    public static $dir = null;

    function sendMail( eZMail $mail )
    {
        $n = count( glob( self::$dir . '/mail-*.txt' ) ) + 1;
        return file_put_contents( self::$dir . sprintf( '/mail-%02d.txt', $n ),
                                  'To: ' . implode( ', ', array_map( function ( $r ) { return $r['email']; }, $mail->receiverElements() ) ) . "\n" .
                                  'Subject: ' . $mail->subject() . "\n\n" . $mail->body() ) !== false;
    }
}

class expAuditSinksTest extends PHPUnit\Framework\TestCase
{
    protected $dir;

    /** @var resource|null the receiver process */
    protected $receiver = null;

    protected $port;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = expAuditTestFixtures::setUp( $this->name() );
        expAuditSinkRegistry::reset();
        expAuditSinkRegistry::$now = null;
    }

    protected function tearDown(): void
    {
        if ( $this->receiver )
        {
            proc_terminate( $this->receiver );
            proc_close( $this->receiver );
            $this->receiver = null;
        }
        expAuditSinkRegistry::reset();
        expAuditSinkRegistry::$now = null;
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    /** Test settings with sinks allowed. */
    protected function configure( array $settings )
    {
        expAuditTestFixtures::configure( $this->dir, $settings + array( 'sinks' => true,
            'AuditChannel_access/Sinks' => array(), 'AuditChannel_system/Sinks' => array() ) );
        expAuditSinkRegistry::reset();
    }

    /** Starts the receiver on a free port of 127.0.0.1. */
    protected function startReceiver( $secret )
    {
        mkdir( $this->dir . 'receiver' );
        $root = expAuditTestFixtures::realRoot();
        for ( $try = 0; $try < 20; $try++ )
        {
            $port = mt_rand( 18000, 18999 );
            $s = @stream_socket_client( "tcp://127.0.0.1:$port", $e, $es, 0.2 );
            if ( $s )
            {
                fclose( $s );
                continue;
            }
            $this->receiver = proc_open( array( PHP_BINARY, '-S', "127.0.0.1:$port", $root . 'ai/bin/one/audit_stage5_webhook_test_receiver.php' ),
                                         array( 0 => array( 'file', '/dev/null', 'r' ), 1 => array( 'file', $this->dir . 'receiver.log', 'a' ),
                                                2 => array( 'file', $this->dir . 'receiver.log', 'a' ) ), $pipes, $root,
                                         array( 'AUDIT_RECEIVER_DIR' => $this->dir . 'receiver', 'AUDIT_RECEIVER_SECRET' => $secret, 'PATH' => getenv( 'PATH' ) ) );
            for ( $i = 0; $i < 50; $i++ )
            {
                usleep( 50000 );
                $s = @stream_socket_client( "tcp://127.0.0.1:$port", $e, $es, 0.2 );
                if ( $s )
                {
                    fclose( $s );
                    $this->port = $port;
                    return $port;
                }
            }
            proc_terminate( $this->receiver );
            proc_close( $this->receiver );
            $this->receiver = null;
        }
        $this->fail( 'The test receiver did not start' );
    }

    /** @return array[] The batches the receiver got */
    protected function received()
    {
        $out = array();
        foreach ( glob( $this->dir . 'receiver/batch-*.json' ) as $f )
            $out[] = json_decode( file_get_contents( $f ), true );
        return $out;
    }

    protected function failedLogins( $n, $ipLast = 7 )
    {
        $ids = array();
        for ( $i = 0; $i < $n; $i++ )
            $ids[] = expAudit::event( 'access.session.login.failed', array( 'object' => array( 'type' => 'user', 'id' => 14 ),
                                                                         'result' => 'failed', 'reason' => 'credentials' ) );
        return $ids;
    }

    /** SK-01 */
    public function testSyslogMessage()
    {
        $sink = new expAuditSyslogSink( 'syslog', array( 'Transport' => 'udp', 'Host' => '127.0.0.1', 'Facility' => 'authpriv', 'AppName' => 'exponential' ) );
        $r = array( 'id' => '01J9ZK3M7Q8R2T4V6X8Z0B2D4F', 'name' => 'access.session.login.failed', 'channel' => 'access', 'seq' => 211,
                    'time' => '2026-10-02T13:30:01.123Z', 'severity' => 'notice', 'result' => 'failed',
                    'request' => array( 'id' => 'r-01J9ZK', 'host' => 'web1', 'pid' => 991876 ),
                    'actor' => array( 'login' => 'a"b]c\\d', 'ip' => '203.0.113.0/24' ), 'hash' => 'sha256:41aa' );
        $m = $sink->message( $r );
        $this->assertStringStartsWith( '<85>1 2026-10-02T13:30:01.123Z web1 exponential 991876 access [exp@32473 id="01J9ZK3M7Q8R2T4V6X8Z0B2D4F" '
                                       . 'name="access.session.login.failed" seq="211" channel="access" user="a\\"b\\]c\\\\d" ip="203.0.113.0/24" '
                                       . 'result="failed" request="r-01J9ZK" hash="sha256:41aa"] {', $m );
        $this->assertEquals( $r, json_decode( substr( $m, strpos( $m, '] {' ) + 2 ), true ), 'the record is the MSG' );
        $this->assertSame( expAuditJson::encode( $r ), substr( $m, strpos( $m, '] {' ) + 2 ), 'in canonical form' );
        $this->assertStringEndsWith( 'hash="sha256:41aa"]', $sink->message( $r, false ), 'udp: header and structured data only' );
        $this->assertTrue( $sink->isSpooled(), 'network transports go through the spool' );

        $j = new expAuditSyslogSink( 'syslog', array( 'Transport' => 'journald', 'AppName' => 'exp-test' ) );
        $d = $j->journalDatagram( $r );
        $this->assertStringContainsString( "SYSLOG_IDENTIFIER=exp-test\n", $d );
        $this->assertStringContainsString( "PRIORITY=5\n", $d );
        $this->assertStringContainsString( "SYSLOG_FACILITY=10\n", $d );
        $this->assertStringContainsString( "EXP_AUDIT_ID=01J9ZK3M7Q8R2T4V6X8Z0B2D4F\n", $d );
        $this->assertStringContainsString( "MESSAGE=1 2026-10-02T13:30:01.123Z web1 exp-test", $d );
        $this->assertFalse( $j->isSpooled(), 'a local socket is written at flush time' );
    }

    /** SK-02 */
    public function testSyslogReachesJournald()
    {
        if ( !file_exists( expAuditSyslogSink::JOURNAL_SOCKET ) || !is_executable( '/usr/bin/journalctl' ) )
            $this->markTestSkipped( 'no journald here' );
        $tag = 'exponential-test-' . bin2hex( random_bytes( 3 ) );
        $this->configure( array( 'AuditChannel_access/Sinks' => array( 'syslog' ), 'AuditSink_syslog/Transport' => 'local',
                                 'AuditSink_syslog/AppName' => $tag ) );
        $ids = $this->failedLogins( 2 );
        $this->assertCount( 0, glob( $this->dir . 'log/spool/*' ) ?: array(), 'nothing spooled: delivered at once' );
        $found = '';
        for ( $i = 0; $i < 20 && substr_count( $found, 'EXP_AUDIT_ID' ) < 2; $i++ )
        {
            usleep( 250000 );
            $found = (string)shell_exec( '/usr/bin/journalctl -t ' . escapeshellarg( $tag ) . ' -o json --no-pager 2>/dev/null' );
        }
        foreach ( $ids as $id )
            $this->assertStringContainsString( '"EXP_AUDIT_ID":"' . $id . '"', $found, "journalctl -t $tag shows $id" );
        $this->assertStringContainsString( '"SYSLOG_IDENTIFIER":"' . $tag . '"', $found );
        $this->assertStringContainsString( '"PRIORITY":"4"', $found, 'a failed login (result failed) is at least a warning' );
    }

    /** SK-03, SK-04, SK-05 */
    public function testWebhookBatchesRetriesAndSignature()
    {
        $secret = 'test-secret-' . bin2hex( random_bytes( 4 ) );
        $port = $this->startReceiver( $secret );
        $this->configure( array( 'AuditChannel_access/Sinks' => array( 'webhook' ), 'AuditSink_webhook/URL' => "http://127.0.0.1:$port/hook",
                                 'AuditSink_webhook/SigningSecret' => $secret, 'AuditSink_webhook/BatchSize' => '3',
                                 'AuditSink_webhook/BatchSeconds' => '0', 'AuditSink_webhook/Retries' => '2',
                                 'AuditSink_webhook/RetryBackoff' => '30', 'AuditSink_webhook/MinSeverity' => 'info' ) );
        $this->assertSame( '', expAuditSinkRegistry::get( 'webhook' )->problem() );

        // SK-03: spooled at flush, delivered by the spool runner in batches of three
        $ids = $this->failedLogins( 7 );
        $spool = expAuditSinkRegistry::spool( 'webhook' );
        $this->assertSame( 7, $spool->count(), 'the request only appends to the spool' );
        $this->assertSame( array(), $this->received() );
        $r = expAuditSinkRegistry::deliverSpools();
        $this->assertSame( 7, $r['webhook']['delivered'] );
        $this->assertSame( 0, $spool->count() );
        $batches = $this->received();
        $this->assertCount( 3, $batches, '3 + 3 + 1' );
        $got = array();
        foreach ( $batches as $b )
        {
            $this->assertSame( 'ok', $b['check'], 'the receiver verified the signature' );
            $this->assertSame( 'application/json', $b['content_type'] );
            $this->assertSame( 1, $b['body']['v'] );
            $this->assertSame( expAuditTestFixtures::INSTALLATION, $b['body']['installation'] );
            $this->assertSame( $b['batch'], $b['body']['batch'] );
            foreach ( $b['body']['events'] as $e )
            {
                $got[] = $e['id'];
                $this->assertSame( expAuditWriter::hashOf( $e ), $e['hash'], 'the record arrives as written' );
            }
        }
        $this->assertSame( $ids, $got, 'every id once, in order' );

        // SK-04: an outage
        file_put_contents( $this->dir . 'receiver/fail', '1' );
        $t = time();
        expAuditSinkRegistry::$now = $t;
        $ids2 = $this->failedLogins( 2 );
        $r = expAuditSinkRegistry::deliverSpool( 'webhook' );
        $this->assertTrue( $r['failed'] );
        $this->assertSame( 'HTTP 500', $r['error'] );
        $this->assertSame( 2, $r['remaining'], 'the batch stays spooled' );
        $state = $spool->state();
        $this->assertSame( 1, $state['attempts'] );
        $this->assertSame( $t + 30, $state['next'], 'RetryBackoff before the first retry' );
        $r = expAuditSinkRegistry::deliverSpool( 'webhook' );
        $this->assertNotNull( $r['waiting'], 'no retry before the backoff' );
        $this->assertCount( 4, $this->received(), 'and nothing was sent' );
        expAuditSinkRegistry::$now = $t + 31;
        $r = expAuditSinkRegistry::deliverSpool( 'webhook' );
        $this->assertTrue( $r['failed'] );
        $state = $spool->state();
        $this->assertSame( 2, $state['attempts'] );
        $this->assertSame( $t + 31 + 60, $state['next'], 'the backoff doubled' );
        $failed = array_values( array_filter( $this->systemRecords(), function ( $r ) { return $r['name'] === 'system.audit.sink.failed'; } ) );
        $this->assertCount( 1, $failed, 'system.audit.sink.failed after Retries' );
        $this->assertSame( 'webhook', $failed[0]['after']['sink'] );
        $this->assertSame( 2, $failed[0]['after']['spooled'] );
        file_put_contents( $this->dir . 'receiver/fail', '0' );
        expAuditSinkRegistry::$now = $t + 92;
        $r = expAuditSinkRegistry::deliverSpool( 'webhook' );
        $this->assertFalse( $r['failed'] );
        $this->assertSame( 2, $r['delivered'] );
        $this->assertSame( 0, $spool->count() );
        $last = $this->received();
        $last = end( $last );
        $this->assertSame( 'ok', $last['check'] );
        $this->assertSame( $ids2, array_column( $last['body']['events'], 'id' ), 'delivered after the outage' );
        $this->assertSame( 0, $spool->state()['attempts'] );

        // SK-05
        $sink = expAuditSinkRegistry::get( 'webhook' );
        $sink->timestamp = time();
        list( $body, $headers ) = $sink->request( array( array( 'id' => 'x', 'name' => 'access.session.login' ) ) );
        $sig = substr( $headers[4], strlen( 'X-Exponential-Signature: ' ) );
        $this->assertSame( '', expAuditWebhookSink::verify( $secret, (string)$sink->timestamp, $body, $sig ) );
        $this->assertSame( 'bad_signature', expAuditWebhookSink::verify( 'other', (string)$sink->timestamp, $body, $sig ) );
        $this->assertSame( 'bad_signature', expAuditWebhookSink::verify( $secret, (string)$sink->timestamp, $body . ' ', $sig ) );
        $this->assertSame( 'stale_timestamp', expAuditWebhookSink::verify( $secret, (string)( $sink->timestamp - 301 ), $body, $sig ) );
    }

    /** SK-06 */
    public function testMailThroughTheTransportThrottled()
    {
        expAuditTestMailTransport::$dir = $this->dir . 'mail';
        mkdir( $this->dir . 'mail' );
        $this->configure( array( 'AuditSink_mail/Receivers' => array( 'auditor@example.com' ), 'AuditSink_mail/Transport' => 'expAuditTestMailTransport',
                                 'AuditSink_syslog/Transport' => 'udp', 'AuditSink_syslog/Host' => '' ) );
        $this->assertSame( '', expAuditSinkRegistry::get( 'mail' )->problem() );
        // 20 failed logins from one network: brute_force fires (Sinks[] syslog, mail)
        expAudit::setNow( 1790946000 );
        $this->failedLogins( 20 );
        // another firing of the same rule and group: its count doubled
        $this->failedLogins( 20 );
        // brute_force fired at 20 and 40; brute_force_user (10 per account) at 10, 20 and 40
        $mailSpool = expAuditSinkRegistry::spool( 'mail' );
        $this->assertSame( 5, $mailSpool->count(), 'five alerts spooled for mail; nothing sent from the request' );
        $this->assertSame( array(), glob( $this->dir . 'mail/mail-*.txt' ) );
        $r = expAuditSinkRegistry::deliverSpools( true, 'mail' );
        $this->assertSame( 5, $r['mail']['delivered'] );
        $mails = glob( $this->dir . 'mail/mail-*.txt' );
        $this->assertCount( 2, $mails, 'one per rule and group: the repeats within Throttle are not mailed' );
        $texts = array_map( 'file_get_contents', $mails );
        // the subject is encoded by eZMail; the body names the rule
        $text = strpos( $texts[0], 'Rule:     brute_force -' ) !== false ? $texts[0] : $texts[1];
        $other = $text === $texts[0] ? $texts[1] : $texts[0];
        $this->assertStringContainsString( 'Rule:     brute_force_user -', $other );
        $this->assertStringContainsString( 'Group:    14', $other );
        $this->assertStringContainsString( 'To: auditor@example.com', $text );
        $this->assertStringContainsString( 'Rule:     brute_force -', $text );
        $this->assertStringContainsString( 'Group:    203.0.113.0/24', $text );
        $this->assertStringContainsString( 'Count:    20 within 300 s', $text );
        $this->assertStringNotContainsString( 'sess-0123456789abcdef', $text, 'no session id' );
    }

    /** SK-07 */
    public function testNothingLeavesUnderTestSettings()
    {
        expAuditTestFixtures::configure( $this->dir, array( 'AuditChannel_access/Sinks' => array( 'webhook' ),
                                                            'AuditSink_webhook/URL' => 'http://127.0.0.1:9/' ) );
        expAuditSinkRegistry::reset();
        $this->failedLogins( 3 );
        $this->assertSame( 0, expAuditSinkRegistry::spool( 'webhook' )->count() );
        $this->assertSame( array(), expAuditSinkRegistry::deliverSpools( true ) );
    }

    /** @return array[] The records of the test system channel */
    protected function systemRecords()
    {
        $out = array();
        foreach ( expAuditWriter::channelFiles( $this->dir . 'log', 'system' ) as $f )
            foreach ( file( $this->dir . 'log/' . $f, FILE_IGNORE_NEW_LINES ) as $l )
                $out[] = json_decode( $l, true );
        return $out;
    }
}
