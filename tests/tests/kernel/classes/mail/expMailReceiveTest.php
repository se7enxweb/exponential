<?php
/**
 * expMailReceive: the shared rules of every mailbox reader, against fake IMAP and POP3 servers (no database, no
 * network beyond 127.0.0.1).
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../toolkit/expfakemailserver.php';

class expMailReceiveTest extends TestCase
{
    /** @var expFakeMailServer[] */
    protected $servers = array();

    protected function tearDown(): void
    {
        foreach ( $this->servers as $server )
            $server->stop();
        $this->servers = array();
        parent::tearDown();
    }

    protected function server( $protocol, array $messages )
    {
        return $this->servers[] = expFakeMailServer::start( $protocol, $messages );
    }

    protected static function fixture( $name )
    {
        return (string)file_get_contents( __DIR__ . '/../mailpreferences/fixtures/bounces/' . $name );
    }

    protected static function message( $subject, array $headers = array() )
    {
        $head = "From: Jo <jo@example.invalid>\r\nTo: list@example.invalid\r\nSubject: $subject\r\n";
        foreach ( $headers as $name => $value )
            $head .= "$name: $value\r\n";
        return $head . "\r\n" . "Body of $subject\r\n";
    }

    // ------------------------------------------------------------------ what a message is

    public function testTopHeadersJoinFoldedLinesAndIgnorePartsAndRepeats()
    {
        $h = expMailReceive::topHeaders( "Subject: one\r\n two\r\nAuto-Submitted: no\r\nAuto-Submitted: auto-replied\r\n\r\nAuto-Submitted: auto-replied\r\n" );
        $this->assertSame( 'one two', $h['subject'] );
        $this->assertSame( 'no', $h['auto-submitted'], 'the first occurrence counts, the body none' );
        $this->assertSame( array(), expMailReceive::topHeaders( '' ) );
    }

    public function testAutomaticHeaders()
    {
        $this->assertTrue( expMailReceive::isAutomaticHeaders( array( 'auto-submitted' => 'auto-replied' ) ) );
        $this->assertTrue( expMailReceive::isAutomaticHeaders( array( 'auto-submitted' => array( 'Auto-Generated' ) ) ), 'a list per name' );
        $this->assertFalse( expMailReceive::isAutomaticHeaders( array( 'auto-submitted' => ' No ' ) ) );
        foreach ( array( 'bulk', 'JUNK', 'list', 'auto_reply' ) as $p )
            $this->assertTrue( expMailReceive::isAutomaticHeaders( array( 'precedence' => $p ) ), $p );
        $this->assertFalse( expMailReceive::isAutomaticHeaders( array( 'precedence' => 'first-class' ) ) );
        $this->assertTrue( expMailReceive::isAutomaticHeaders( array( 'x-autoreply' => 'yes' ) ) );
        $this->assertTrue( expMailReceive::isAutomaticHeaders( array( 'x-autorespond' => '' ) ) );
        $this->assertFalse( expMailReceive::isAutomaticHeaders( array() ) );
    }

    public function testAutomaticReplyIsNeitherAReportNorAMailSystemsBounce()
    {
        $this->assertTrue( expMailReceive::isAutomaticReply( self::fixture( 'plain-auto-reply.eml' ) ) );
        $this->assertTrue( expMailReceive::isAutomaticReply( self::message( 'Out of office', array( 'Auto-Submitted' => 'auto-replied' ) ) ) );
        $this->assertFalse( expMailReceive::isAutomaticReply( self::message( 'Question' ) ) );
        $this->assertFalse( expMailReceive::isAutomaticReply( "From: MAILER-DAEMON@mx.example.invalid\nAuto-Submitted: auto-replied\n\n550 5.1.1\n" ) );
        $this->assertFalse( expMailReceive::isAutomaticReply( "From: postmaster@mx.example.invalid\nAuto-Submitted: auto-replied\n\nx\n" ) );
        foreach ( array( 'dsn-soft-delayed.eml', 'dsn-hard-user-unknown.eml', 'arf-abuse.eml' ) as $report )
            $this->assertFalse( expMailReceive::isAutomaticReply( self::fixture( $report ) ), $report );
        $this->assertTrue( expMailReceive::isAutomaticReply( array( 'auto-submitted' => 'auto-replied', 'from' => 'jo@example.invalid' ) ), 'headers given' );
    }

    // ------------------------------------------------------------------ one failure stops one unit

    public function testIsolateGoesOnAfterAFailureAndReportsIt()
    {
        $seen = array();
        $errors = array();
        $r = expMailReceive::isolate( array( 'a' => 1, 'b' => 2, 'c' => 3 ),
            function ( $item, $key ) use ( &$seen ) {
                $seen[] = $key;
                if ( $key === 'b' )
                    throw new RuntimeException( 'b is down' );
                return $item * 10;
            },
            function ( Throwable $e, $item, $key ) use ( &$errors ) {
                $errors[] = $key;
                throw new LogicException( 'a failing handler is ignored' );
            } );
        $this->assertSame( array( 'a', 'b', 'c' ), $seen );
        $this->assertSame( array( 'b' ), $errors );
        $this->assertSame( array( 'ok' => true, 'result' => 10, 'error' => '' ), $r['a'] );
        $this->assertSame( array( 'ok' => false, 'result' => null, 'error' => 'b is down' ), $r['b'] );
        $this->assertSame( 30, $r['c']['result'] );
    }

    // ------------------------------------------------------------------ the transports, against fake servers

    public static function protocols()
    {
        return array( 'imap' => array( 'imap' ), 'pop3' => array( 'pop3' ) );
    }

    #[DataProvider('protocols')]
    public function testReadsAndDeletesOnlyTheStoredMessages( $protocol )
    {
        $one = self::message( 'one' );
        $server = $this->server( $protocol, array( $one, self::message( 'two' ), self::message( 'three' ) ) );
        $t = expMailReceive::connect( $server->account() );
        $this->assertSame( array( 1, 2, 3 ), expMailReceive::messageNumbers( $t ) );
        $this->assertSame( str_replace( "\r\n", "\n", $one ), str_replace( "\r\n", "\n", expMailReceive::fetchRaw( $t, 1 ) ) );
        // 1 and 3 stored, 2 failed: 2 stays
        $this->assertSame( 2, expMailReceive::deleteMessages( $t, array( 1, 3, 3 ) ) );
        expMailReceive::disconnect( $t );
        $this->assertSame( array( 2 ), $server->left() );
        $commands = implode( "\n", $server->commands() );
        if ( $protocol === 'imap' )
        {
            $this->assertMatchesRegularExpression( '/STORE 1 \+FLAGS \(\\\\Deleted\)/', $commands );
            $this->assertStringContainsString( 'EXPUNGE', $commands );
            $this->assertStringContainsString( 'LOGIN ***', $commands );
        }
        else
        {
            $this->assertStringContainsString( "DELE 1\nDELE 3\nQUIT", $commands );
            $this->assertStringContainsString( 'PASS ***', $commands );
        }
        $this->assertStringNotContainsString( expFakeMailServer::PASSWORD, $commands, 'the log never holds the password' );
    }

    public function testNothingToDeleteSendsNoExpunge()
    {
        $server = $this->server( 'imap', array( self::message( 'one' ) ) );
        $t = expMailReceive::connect( $server->account() );
        $this->assertSame( 0, expMailReceive::deleteMessages( $t, array() ) );
        expMailReceive::disconnect( $t );
        $this->assertStringNotContainsString( 'EXPUNGE', implode( "\n", $server->commands() ) );
        $this->assertSame( array( 1 ), $server->left() );
    }

    public function testKeepUnreadClearsTheSeenFlag()
    {
        $server = $this->server( 'imap', array( self::message( 'one' ) ) );
        $t = expMailReceive::connect( $server->account() );
        expMailReceive::fetchRaw( $t, 1, true );
        $this->assertSame( array( 1 ), expMailReceive::messageNumbers( $t ), 'still unread' );
        expMailReceive::disconnect( $t );
        $this->assertMatchesRegularExpression( '/STORE 1 -FLAGS \(\\\\Seen\)/i', implode( "\n", $server->commands() ) );
    }

    public function testOneFailingAccountDoesNotStopTheOthers()
    {
        $good = $this->server( 'pop3', array( self::message( 'one' ), self::message( 'two' ) ) );
        $imap = $this->server( 'imap', array( self::message( 'three' ) ) );
        $accounts = array(
            'refused' => $good->account( array( 'password' => 'wrong' ) ),
            'down'    => array( 'protocol' => 'imap', 'server' => '127.0.0.1', 'port' => expFakeMailServer::freePort(), 'ssl' => false, 'timeout' => 2 ),
            'pop3'    => $good->account(),
            'imap'    => $imap->account(),
        );
        $r = expMailReceive::isolate( $accounts, function ( $account ) {
            $t = expMailReceive::connect( $account );
            try
            {
                return count( expMailReceive::messageNumbers( $t ) );
            }
            finally
            {
                expMailReceive::disconnect( $t );
            }
        } );
        $this->assertFalse( $r['refused']['ok'] );
        $this->assertNotSame( '', $r['refused']['error'] );
        $this->assertFalse( $r['down']['ok'] );
        $this->assertSame( 2, $r['pop3']['result'] );
        $this->assertSame( 1, $r['imap']['result'] );
    }

    /**
     * The kernel's bounce reader on expMailReceive: a delay notice is understood and deleted, an automatic reply is
     * not understood and stays.
     */
    #[DataProvider('protocols')]
    public function testBounceReaderDeletesWhatItUnderstood( $protocol )
    {
        $server = $this->server( $protocol, array( self::fixture( 'dsn-soft-delayed.eml' ), self::fixture( 'plain-auto-reply.eml' ) ) );
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $saved = array();
        $settings = array( 'Reader' => 'enabled', 'Protocol' => $protocol, 'Server' => '127.0.0.1', 'Port' => (string)$server->port,
                           'SSL' => 'disabled', 'User' => 'reader', 'Password' => expFakeMailServer::PASSWORD, 'Folder' => 'INBOX',
                           'AfterRead' => 'delete', 'MessageListeners' => array() );
        foreach ( $settings as $name => $value )
        {
            $saved[$name] = $ini->hasVariable( 'BounceSettings', $name ) ? $ini->variable( 'BounceSettings', $name ) : null;
            $ini->setVariable( 'BounceSettings', $name, $value );
        }
        $status = $server->dir . '/status.json';
        expMailBounceReader::setStatusFileForTest( $status );
        try
        {
            $r = expMailBounceReader::run();
        }
        finally
        {
            expMailBounceReader::setStatusFileForTest( null );
            foreach ( $saved as $name => $value )
                $ini->setVariable( 'BounceSettings', $name, $value === null ? '' : $value );
            @unlink( $status );
        }
        $this->assertTrue( $r['ok'], $r['error'] );
        $this->assertSame( 2, $r['counts']['messages'] );
        $this->assertSame( 1, $r['counts']['delayed'] );
        $this->assertSame( 1, $r['counts']['other'] );
        $this->assertSame( array( 2 ), $server->left(), 'the automatic reply stays, the delay notice is gone' );
    }

    // ------------------------------------------------------------------ cost

    /**
     * Reading what a message is costs microseconds, not milliseconds: 3000 messages (replies, reports, plain mail)
     * well under a second on any CI runner.
     */
    public function testClassificationIsCheap()
    {
        $messages = array( self::fixture( 'plain-auto-reply.eml' ), self::fixture( 'dsn-soft-delayed.eml' ),
                           self::fixture( 'arf-abuse.eml' ) );
        $start = hrtime( true );
        $automatic = 0;
        for ( $i = 0; $i < 1000; $i++ )
            foreach ( $messages as $raw )
                $automatic += expMailReceive::isAutomaticReply( $raw ) ? 1 : 0;
        $micro = ( hrtime( true ) - $start ) / 1000 / 3000;
        $this->assertSame( 1000, $automatic );
        $this->assertLessThan( 200, $micro, sprintf( '%.1f microseconds per message', $micro ) );
    }
}
