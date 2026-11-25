<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** The mailbox class: what its log may show, and how it fetches. */
class cjwNewsletterMailboxTest extends cjwNewsletterTestCase
{
    const SECRET = 's3cret-test';

    /** Starts a stand-in IMAP server on a free port that refuses every login; returns array( process, pipes, port ). */
    private function startRefusingImapServer()
    {
        $code = '$s = stream_socket_server( "tcp://127.0.0.1:0", $en, $es ); '
            . 'echo stream_socket_get_name( $s, false ), "\n"; fflush( STDOUT ); '
            . '$c = @stream_socket_accept( $s, 20 ); if ( !$c ) exit; '
            . 'fwrite( $c, "* OK nltest ready\r\n" ); '
            . 'while ( ( $l = fgets( $c ) ) !== false ) { $t = strtok( $l, " " ); fwrite( $c, $t . " NO login refused\r\n" ); }';
        $proc = proc_open( array( PHP_BINARY, '-r', $code ), array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        $name = trim( (string)fgets( $pipes[1] ) );
        return array( $proc, $pipes, (int)substr( strrchr( $name, ':' ), 1 ) );
    }

    public function testLoggableSettingsMasksOnlyANonEmptyPassword()
    {
        $box = new CjwNewsletterMailbox();
        $masked = $box->loggableSettings( array( 'user_name' => 'u', 'password' => self::SECRET ) );
        $this->assertSame( '***', $masked['password'] );
        $this->assertSame( 'u', $masked['user_name'] );
        $this->assertSame( '', $box->loggableSettings( array( 'password' => '' ) )['password'] );
    }

    public function testFailedLoginLeavesNoPasswordInTheLog()
    {
        list( $proc, $pipes, $port ) = $this->startRefusingImapServer();
        $this->assertGreaterThan( 0, $port, 'stand-in server did not start' );
        $logDir = eZDir::path( array( eZSys::varDirectory(), eZINI::instance()->variable( 'FileSettings', 'LogDir' ) ) );
        $files = array( $logDir . '/cjw_newsletter_error.log', $logDir . '/cjw_newsletter_cli_error.log' );
        $before = array();
        foreach ( $files as $f ) $before[$f] = file_exists( $f ) ? filesize( $f ) : 0;

        $box = new CjwNewsletterMailbox( array( 'id' => 0, 'type' => 'imap', 'server' => '127.0.0.1', 'port' => $port,
            'user_name' => 'nltest-user', 'password' => self::SECRET, 'is_ssl' => 0, 'delete_mails_from_server' => 0 ) );
        $result = $box->connect();
        proc_terminate( $proc );
        proc_close( $proc );
        $this->assertIsString( $result, 'the login should have failed' );

        $added = '';
        foreach ( $files as $f )
            if ( file_exists( $f ) )
                $added .= substr( (string)file_get_contents( $f ), $before[$f] );
        $this->assertStringContainsString( 'authenticate-failed', $added );
        $this->assertStringContainsString( '***', $added );
        $this->assertStringNotContainsString( self::SECRET, $added );
    }

    public function testOneFailingMailboxDoesNotStopTheOthers()
    {
        $bad = new cjwNewsletterFakeMailbox( array( 'id' => 901 ), 'throw' );
        $broken = new cjwNewsletterFakeMailbox( array( 'id' => 902 ), 'connect-throw' );
        $good = new cjwNewsletterFakeMailbox( array( 'id' => 903 ), array( 'new' => 2 ) );
        cjwNewsletterFakeMailboxCollector::$boxes = array( $bad, $broken, $good );

        $result = cjwNewsletterFakeMailboxCollector::collectMailsFromActiveMailboxes();

        $this->assertIsArray( $result );
        $this->assertSame( array( 901, 902, 903 ), array_keys( $result ) );
        $this->assertStringStartsWith( 'fetch failed:', $result[901] );
        $this->assertStringStartsWith( 'connect failed:', $result[902] );
        $this->assertSame( array( 'new' => 2 ), $result[903] );
    }

    /** Runs fetchMails() on a store-controlled mailbox; returns array( status, transport ). */
    private function fetchWith( $type, $deleteFlag, array $storeResults, array $sizes = array( 1 => 100, 2 => 100, 3 => 100 ), $uids = array( 1 => 'u1', 2 => 'u2', 3 => 'u3' ) )
    {
        $box = new cjwNewsletterStoreMailbox( array( 'id' => 0, 'type' => $type, 'delete_mails_from_server' => $deleteFlag ? 1 : 0 ) );
        $box->results = $storeResults;
        $transport = new cjwNewsletterRecordingTransport( $sizes, $uids );
        $box->setTransportObject( $transport );
        return array( $box->fetchMails(), $transport );
    }

    public function testStoringFailureKeepsTheMessage()
    {
        list( $status, $t ) = $this->fetchWith( 'imap', true, array( 1 => 'stored', 2 => 'known', 3 => 'throw' ) );
        $this->assertArrayHasKey( 3, $status['failed'] );
        $this->assertNotContains( 3, $t->deleted );
    }

    public function testStoredAndKnownAreDeletedAfterAllFetches()
    {
        list( $status, $t ) = $this->fetchWith( 'imap', true, array( 1 => 'stored', 2 => 'known', 3 => 'null' ) );
        $this->assertSame( array( 1, 2 ), $t->deleted );
        $this->assertSame( array( false ), array_values( array_unique( $t->fetchFlags ) ) );
        $this->assertSame( 1, $t->expunged );
        $this->assertArrayHasKey( 1, $status['added'] );
        $this->assertArrayHasKey( 2, $status['exists'] );
        $this->assertArrayHasKey( 3, $status['failed'] );
    }

    public function testNothingIsDeletedWhenTheMailboxKeepsMails()
    {
        list( , $t ) = $this->fetchWith( 'imap', false, array( 1 => 'stored', 2 => 'known', 3 => 'stored' ) );
        $this->assertSame( array(), $t->deleted );
        $this->assertSame( 0, $t->expunged );
    }

    public function testPop3HasNoExpunge()
    {
        list( , $t ) = $this->fetchWith( 'pop3', true, array( 1 => 'stored', 2 => 'stored', 3 => 'stored' ) );
        $this->assertSame( array( 1, 2, 3 ), $t->deleted );
        $this->assertSame( 0, $t->expunged );
    }

    public function testKnownMessagesAreDeletedWhenNothingIsNew()
    {
        $box = new cjwNewsletterStoreMailbox( array( 'id' => 0, 'type' => 'imap', 'delete_mails_from_server' => 1 ) );
        $box->existing = array( 'u1' => 'item_1', 'u2' => 'item_2', 'u3' => 'stub_3' );
        $t = new cjwNewsletterRecordingTransport( array( 1 => 100, 2 => 100, 3 => 100 ), array( 1 => 'u1', 2 => 'u2', 3 => 'u3' ) );
        $box->setTransportObject( $t );
        $box->fetchMails();
        $this->assertSame( array( 1, 2 ), $t->deleted, 'an oversized stub (3) stays on the server' );
        $this->assertSame( 1, $t->expunged );
    }

    public function testOnlyAnOversizedMessageIsTakenForAStub()
    {
        $mailboxId = 990000 + getmypid() % 9999;
        $stub = CjwNewsletterMailboxItem::addOversizedMailboxItem( $mailboxId, 'nltest-big', 1, CjwNewsletterMailParser::maxMessageSize() + 1 );
        $spoof = new CjwNewsletterMailboxItem( array( 'created' => time(), 'mailbox_id' => $mailboxId, 'message_id' => 2,
                                                      'message_identifier' => 'nltest-small', 'message_size' => 100,
                                                      'email_subject' => '(not fetched: larger than MaxMessageSize)' ) );
        $spoof->store();
        try
        {
            $box = new cjwNewsletterIdentifierMailbox( array( 'id' => $mailboxId ) );
            $existing = $box->existing( array( 'nltest-big', 'nltest-small' ) );
            $this->assertSame( 'stub_' . $stub->attribute( 'id' ), $existing['nltest-big'] );
            $this->assertSame( 'item_' . $spoof->attribute( 'id' ), $existing['nltest-small'], 'a subject alone makes no stub' );
        }
        finally
        {
            $stub->remove();
            $spoof->remove();
        }
    }

    public function testMessageWithoutUidIsDeletedAfterStore()
    {
        list( $status, $t ) = $this->fetchWith( 'pop3', true, array( 1 => 'stored' ), array( 1 => 100 ), array() );
        $this->assertSame( array( 1 ), $t->deleted );
        $this->assertArrayHasKey( 1, $status['added'] );
    }
}

/** Mailbox that shows which of its stored messages it takes for oversized stubs. */
class cjwNewsletterIdentifierMailbox extends CjwNewsletterMailbox
{
    public function existing( array $identifiers ) { return $this->extractAllExistingIdentifiers( $identifiers ); }
}

/** Mailbox whose storeItem() answers from $results by message number: stored, known, null, throw. */
class cjwNewsletterStoreMailbox extends CjwNewsletterMailbox
{
    public $results = array();
    protected function storeItem( $mailboxId, $identifier, $messageNumber, $raw )
    {
        $r = isset( $this->results[$messageNumber] ) ? $this->results[$messageNumber] : 'stored';
        if ( $r === 'throw' )
            throw new Exception( 'store boom' );
        if ( $r === 'null' )
            return null;
        return $r === 'stored' ? new stdClass() : false;
    }
    public $existing = array();
    protected function extractAllExistingIdentifiers( $a )
    {
        return $this->existing;
    }
}

class cjwNewsletterRecordingTransport
{
    public $deleted = array();
    public $fetchFlags = array();
    public $expunged = 0;
    private $sizes;
    private $uids;
    public function __construct( $sizes, $uids )
    {
        $this->sizes = $sizes;
        $this->uids = $uids;
    }
    public function listUniqueIdentifiers() { return $this->uids; }
    public function listMessages() { return $this->sizes; }
    public function fetchByMessageNr( $n, $delete = false )
    {
        $this->fetchFlags[] = $delete;
        return new cjwNewsletterLineSet( "Subject: m$n\r\n\r\nbody $n\r\n" );
    }
    public function delete( $n ) { $this->deleted[] = $n; }
    public function expunge() { $this->expunged++; }
}

class cjwNewsletterLineSet
{
    private $lines;
    public function __construct( $s ) { $this->lines = array( $s ); }
    public function getNextLine() { return array_shift( $this->lines ); }
}

/** A mailbox that needs no server: $mode is 'throw' (fetch fails), 'connect-throw', or the status array fetchMails() returns. */
class cjwNewsletterFakeMailbox extends CjwNewsletterMailbox
{
    private $mode;
    public function __construct( $row, $mode )
    {
        parent::__construct( $row );
        $this->mode = $mode;
    }
    public function connect()
    {
        if ( $this->mode === 'connect-throw' )
            throw new Exception( 'boom connect' );
        return new stdClass();
    }
    public function fetchMails()
    {
        if ( $this->mode === 'throw' )
            throw new Exception( 'boom fetch' );
        return $this->mode;
    }
    public function disconnect()
    {
    }
}

class cjwNewsletterFakeMailboxCollector extends CjwNewsletterMailbox
{
    public static $boxes = array();
    protected static function activeMailboxes()
    {
        return self::$boxes;
    }
}
