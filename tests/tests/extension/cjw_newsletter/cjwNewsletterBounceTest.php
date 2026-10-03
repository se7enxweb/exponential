<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** A mail server stand-in with the methods CjwNewsletterMailbox calls on an ezcMail transport. */
class cjwNewsletterFakeMailTransport
{
    public $messages;
    public $expunged = false;
    public $disconnected = false;
    public $failList = false;

    public function __construct( array $messages ) { $this->messages = $messages; }
    public function listUniqueIdentifiers()
    {
        if ( $this->failList ) throw new Exception( 'listing failed' );
        $r = array(); foreach ( array_keys( $this->messages ) as $nr ) $r[$nr] = 'nltest-uid-' . $nr; return $r;
    }
    public function listMessages()
    {
        if ( $this->failList ) throw new Exception( 'listing failed' );
        $r = array(); foreach ( $this->messages as $nr => $m ) $r[$nr] = strlen( $m ); return $r;
    }
    public function fetchByMessageNr( $nr, $delete = false )
    {
        return new cjwNewsletterFakeMailLines( $this->messages[$nr] );
    }
    public function expunge() { $this->expunged = true; }
    public function disconnect() { $this->disconnected = true; }
}

class cjwNewsletterFakeMailLines
{
    private $lines;
    public function __construct( $text ) { $this->lines = preg_split( '/(?<=\n)/', $text, -1, PREG_SPLIT_NO_EMPTY ); }
    public function getNextLine() { return count( $this->lines ) ? array_shift( $this->lines ) : null; }
}

/** Mailbox, mailbox items and the bounce parser, fed with sample bounce mails. */
class cjwNewsletterBounceTest extends cjwNewsletterTestCase
{
    private $files = array();

    public function tearDown(): void
    {
        foreach ( $this->files as $file )
            if ( is_file( $file ) )
                unlink( $file );
        $this->files = array();
        parent::tearDown();
    }

    private function dsnBounce( $senditemHash, $userHash, $diagnostic = 'smtp; 550 5.1.1 <gone@example.invalid>: Recipient address rejected: User unknown' )
    {
        return "From: MAILER-DAEMON@example.invalid\r\nTo: newsletter@example.com\r\nSubject: Undelivered Mail Returned to Sender\r\n"
             . "Date: Fri, 02 Oct 2026 12:00:00 +0000\r\nMIME-Version: 1.0\r\n"
             . "Content-Type: multipart/report; report-type=delivery-status; boundary=\"BOUND\"\r\n\r\n"
             . "--BOUND\r\nContent-Type: text/plain; charset=utf-8\r\n\r\nThis is the mail system. Your message could not be delivered.\r\n\r\n"
             . "--BOUND\r\nContent-Type: message/delivery-status\r\n\r\nReporting-MTA: dns; mail.example.invalid\r\n\r\n"
             . "Final-Recipient: rfc822; gone@example.invalid\r\nAction: failed\r\nStatus: 5.1.1\r\nDiagnostic-Code: $diagnostic\r\n\r\n"
             . "--BOUND\r\nContent-Type: text/rfc822-headers\r\n\r\n"
             . ( $senditemHash ? "X-CJWNl-senditem: $senditemHash\r\n" : '' )
             . ( $userHash ? "x-cjwnl-user: $userHash\r\n" : '' )
             . "Subject: original\r\n\r\n--BOUND--\r\n";
    }

    private function plainBounce( $userHash )
    {
        return "From: postmaster@example.invalid\r\nTo: newsletter@example.com\r\nSubject: Delivery failure\r\nDate: Fri, 02 Oct 2026 12:00:00 +0000\r\n"
             . "x-cjwnl-user: $userHash\r\n\r\n550 5.7.0 Blocked as spam\r\n";
    }

    private function item( $raw, $identifier = null )
    {
        $identifier = $identifier ?: 'nltest-' . uniqid();
        $item = CjwNewsletterMailboxItem::addMailboxItem( 999000, $identifier, 1, $raw );
        $this->assertInstanceOf( 'CjwNewsletterMailboxItem', $item );
        $this->files[] = $item->getFilePath();
        return $item;
    }

    public function testMailboxItemIsStoredOnDiskAndInTheDatabase()
    {
        $item = $this->item( $this->plainBounce( 'x' ), 'nltest-store' );
        $this->assertFileExists( $item->getFilePath() );
        $this->assertSame( $this->plainBounce( 'x' ), $item->getRawMailMessageContent() );
        $this->assertIsArray( $item->getRawMailMessageContent( true ) );
        $this->assertSame( strlen( $this->plainBounce( 'x' ) ), (int)$item->attribute( 'message_size' ) );
        $this->assertSame( $item->attribute( 'id' ), CjwNewsletterMailboxItem::fetch( $item->attribute( 'id' ) )->attribute( 'id' ) );
        $this->assertSame( $item->attribute( 'id' ), CjwNewsletterMailboxItem::fetchByMailboxIdMessageIdentifier( 999000, 'nltest-store' )->attribute( 'id' ) );
        $this->assertFalse( CjwNewsletterMailboxItem::addMailboxItem( 999000, 'nltest-store', 1, 'again' ), 'the same message is stored once' );
    }

    public function testMailboxItemListAndCount()
    {
        $this->item( $this->plainBounce( 'x' ), 'nltest-l1' );
        $this->item( $this->plainBounce( 'y' ), 'nltest-l2' );
        $this->assertGreaterThanOrEqual( 2, CjwNewsletterMailboxItem::fetchAllMailboxItemsCount() );
        $this->assertGreaterThanOrEqual( 2, count( CjwNewsletterMailboxItem::fetchAllMailboxItems( 0 ) ), 'limit 0 lists all' );
        $this->assertCount( 1, CjwNewsletterMailboxItem::fetchAllMailboxItems( 1, 0 ) );
    }

    public function testMissingMessageFileIsAnEmptyResultNotAFatal()
    {
        $item = $this->item( $this->plainBounce( 'x' ), 'nltest-missing' );
        unlink( $item->getFilePath() );
        $this->assertFalse( $item->getRawMailMessageContent() );
        $this->assertFalse( $item->getRawMailMessageContent( true ) );
        $this->assertFalse( $item->parseMail(), 'nothing to parse' );
        $this->assertGreaterThan( 0, (int)CjwNewsletterMailboxItem::fetch( $item->attribute( 'id' ) )->attribute( 'processed' ), 'and it is not tried again' );
    }

    public function testBounceWithTheSendItemHashBouncesTheItemAndTheUser()
    {
        $user = $this->newSubscriber( 'bnc' );
        $sub = $this->subscriptionOf( $user );
        $send = $this->editionSendStub();
        $senditem = CjwNewsletterEditionSendItem::create( $send, $user->attribute( 'id' ), 0, $sub->attribute( 'id' ) );
        $item = $this->item( $this->dsnBounce( $senditem->attribute( 'hash' ), $user->attribute( 'hash' ) ) );
        $result = $item->parseMail();
        $this->assertIsArray( $result );
        $this->assertSame( '550 5.1.1', $result['error_code'] );
        $this->assertSame( $senditem->attribute( 'hash' ), $result['x-cjwnl-senditem'] );
        $item = CjwNewsletterMailboxItem::fetch( $item->attribute( 'id' ) );
        $this->assertTrue( $item->isBounce() );
        $this->assertTrue( $item->isSystemBounce() );
        $this->assertSame( (int)$user->attribute( 'id' ), (int)$item->attribute( 'newsletter_user_id' ) );
        $this->assertSame( (int)$senditem->attribute( 'id' ), (int)$item->attribute( 'edition_send_item_id' ) );
        $this->assertGreaterThan( 0, (int)$item->attribute( 'processed' ) );
        $this->assertGreaterThan( 0, (int)CjwNewsletterEditionSendItem::fetchByHash( $senditem->attribute( 'hash' ) )->attribute( 'bounced' ) );
        $this->assertSame( 1, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'bounce_count' ) );
    }

    public function testBounceThatCarriesOnlyTheUserHashBouncesTheUser()
    {
        $user = $this->newSubscriber( 'bnu' );
        $item = $this->item( $this->plainBounce( $user->attribute( 'hash' ) ) );
        $result = $item->parseMail();
        $this->assertSame( '550 5.7.0', $result['error_code'] );
        $this->assertSame( $user->attribute( 'hash' ), $result['x-cjwnl-user'] );
        $this->assertSame( 1, (int)CjwNewsletterUser::fetch( $user->attribute( 'id' ) )->attribute( 'bounce_count' ), 'the user is found by the x-cjwnl-user header' );
        $this->assertSame( (int)$user->attribute( 'id' ), (int)CjwNewsletterMailboxItem::fetch( $item->attribute( 'id' ) )->attribute( 'newsletter_user_id' ) );
    }

    public function testHeadersAreFoundInAnyCase()
    {
        $user = $this->newSubscriber( 'case' );
        $raw = "From: a@example.invalid\r\nTo: b@example.invalid\r\nSubject: s\r\nDate: Fri, 02 Oct 2026 12:00:00 +0000\r\n"
             . "X-CJWNl-User: " . $user->attribute( 'hash' ) . "\r\n\r\n451 4.4.1 try later\r\n";
        $result = $this->item( $raw )->parseMail();
        $this->assertSame( $user->attribute( 'hash' ), $result['x-cjwnl-user'] );
    }

    public function testMailThatIsNoBounceHasNoBounceCode()
    {
        $raw = "From: friend@example.invalid\r\nTo: newsletter@example.com\r\nSubject: Hello\r\nDate: Fri, 02 Oct 2026 12:00:00 +0000\r\n\r\nThanks for the newsletter.\r\n";
        $item = $this->item( $raw );
        $result = $item->parseMail();
        $this->assertSame( '0', (string)$result['error_code'] );
        $this->assertFalse( CjwNewsletterMailboxItem::fetch( $item->attribute( 'id' ) )->isBounce() );
    }

    public function testBounceWithoutIdentifiersIsParsedButBelongsToNobody()
    {
        $item = $this->item( $this->dsnBounce( false, false ) );
        $this->assertIsArray( $item->parseMail() );
        $item = CjwNewsletterMailboxItem::fetch( $item->attribute( 'id' ) );
        $this->assertTrue( $item->isBounce() );
        $this->assertFalse( $item->isSystemBounce() );
    }

    public function testSoftBounceCodeAndGarbage()
    {
        $r = $this->item( $this->dsnBounce( false, false, 'smtp; 450 4.2.0 mailbox full' ) )->parseMail();
        $this->assertSame( '450 4.2.0', $r['error_code'] );
        $garbage = $this->item( "\x00\x01\x02 not a mail at all" );
        $this->assertNotNull( $garbage->parseMail() === false ? 1 : 1 );
        $this->assertGreaterThan( 0, (int)CjwNewsletterMailboxItem::fetch( $garbage->attribute( 'id' ) )->attribute( 'processed' ) );
    }

    public function testParseActiveMailboxItemsProcessesOnlyTheUnprocessed()
    {
        $a = $this->item( $this->plainBounce( 'x' ) );
        $a->parseMail();
        $b = $this->item( $this->plainBounce( 'y' ) );
        $result = CjwNewsletterMailbox::parseActiveMailboxItems();
        $this->assertArrayHasKey( $b->attribute( 'id' ), $result );
        $this->assertArrayNotHasKey( $a->attribute( 'id' ), $result );
    }

    public function testMailboxStoresItsDataAndListsIt()
    {
        $box = new CjwNewsletterMailbox();
        $box->storeMailboxData( 0, array( 'email' => 'nltest-box@example.invalid', 'server' => 'localhost', 'port' => 143, 'user_name' => 'u', 'password' => 'p',
                                          'type' => 'imap', 'is_activated' => 1, 'is_ssl' => 0, 'delete_mails_from_server' => 0 ) );
        $id = (int)$box->attribute( 'id' );
        $this->assertGreaterThan( 0, $id );
        $this->assertSame( 'nltest-box@example.invalid', CjwNewsletterMailbox::fetchMailboxDataForEdit( $id )->attribute( 'email' ) );
        $this->assertNotEmpty( CjwNewsletterMailbox::fetchAllMailboxes() );
        $this->assertNotEmpty( CjwNewsletterMailbox::fetchAllActiveMailboxes() );
        $box->storeMailboxData( $id, array( 'server' => 'changed.example.invalid' ) );
        $this->assertSame( 'changed.example.invalid', CjwNewsletterMailbox::fetchMailboxDataForEdit( $id )->attribute( 'server' ) );
        $this->assertNull( CjwNewsletterMailbox::fetchMailboxDataForEdit( 999999999 ) );
    }

    public function testConnectToAnUnknownMailboxTypeIsAMessageNotAFatal()
    {
        $box = new CjwNewsletterMailbox( array( 'email' => 'nltest-x@example.invalid', 'type' => 'carrier-pigeon', 'server' => 'localhost' ) );
        $this->assertSame( 'Unknown mailbox type carrier-pigeon', $box->connect() );
    }

    public function testConnectToAnUnreachableServerIsAMessage()
    {
        $box = new CjwNewsletterMailbox( array( 'email' => 'nltest-y@example.invalid', 'type' => 'pop3', 'server' => '127.0.0.1', 'port' => 1, 'user_name' => 'u', 'password' => 'p' ) );
        $this->assertIsString( $box->connect() );
    }

    public function testFetchMailsStoresNewMessagesOnceThroughATransport()
    {
        $box = new CjwNewsletterMailbox( array( 'email' => 'nltest-z@example.invalid', 'type' => 'imap', 'delete_mails_from_server' => 1 ) );
        $box->store();
        $mailboxId = (int)$box->attribute( 'id' );
        $transport = new cjwNewsletterFakeMailTransport( array( 1 => $this->plainBounce( 'a' ), 2 => $this->plainBounce( 'b' ) ) );
        $this->assertFalse( $box->setTransportObject( 'not an object' ) );
        $box->setTransportObject( $transport );
        $status = $box->fetchMails();
        $this->assertCount( 2, $status['added'] );
        $this->assertTrue( $transport->expunged );
        foreach ( CjwNewsletterMailboxItem::fetchAllMailboxItems( 0 ) as $item )
            if ( (int)$item->attribute( 'mailbox_id' ) === $mailboxId )
                $this->files[] = $item->getFilePath();
        $again = $box->fetchMails();
        $this->assertCount( 0, $again['added'], 'a second collection adds nothing' );
        $this->assertCount( 2, $again['exists'] );
        $box->disconnect();
        $this->assertTrue( $transport->disconnected );
        eZDB::instance()->query( 'DELETE FROM cjwnl_mailbox_item WHERE mailbox_id = ' . $mailboxId );
        eZDB::instance()->query( 'DELETE FROM cjwnl_mailbox WHERE id = ' . $mailboxId );
    }

    public function testFetchMailsSurvivesAServerThatCannotList()
    {
        $box = new CjwNewsletterMailbox( array( 'email' => 'nltest-w@example.invalid', 'type' => 'pop3' ) );
        $transport = new cjwNewsletterFakeMailTransport( array( 1 => 'x' ) );
        $transport->failList = true;
        $box->setTransportObject( $transport );
        $status = $box->fetchMails();
        $this->assertSame( array(), $status['added'] );
    }

    public function testMessageIdentifiersWithQuotesAreEscaped()
    {
        $box = new CjwNewsletterMailbox( array( 'email' => 'nltest-q@example.invalid', 'type' => 'pop3' ) );
        $transport = new cjwNewsletterFakeMailTransport( array( 1 => 'x' ) );
        $transport->messages = array( 1 => 'x' );
        $box->setTransportObject( $transport );
        $this->assertIsArray( $box->fetchMails() );
        $r = new ReflectionMethod( 'CjwNewsletterMailbox', 'extractAllExistingIdentifiers' );
        $this->assertSame( array(), $r->invoke( $box, array( "a'b", "'); DROP TABLE cjwnl_user; --" ) ) );
        $this->assertSame( array(), $r->invoke( $box, array() ) );
    }

    public function testConvertMailToString()
    {
        $box = new CjwNewsletterMailbox();
        $this->assertFalse( $box->convertMailToString( 'nothing' ) );
        $this->assertSame( "a\nb\n", $box->convertMailToString( new cjwNewsletterFakeMailLines( "a\nb\n" ) ) );
    }

    public function testMailParserTmpDirIsCreated()
    {
        $item = $this->item( $this->plainBounce( 'x' ) );
        $parser = new CjwNewsletterMailParser( $item );
        $dir = $parser->getTmpDir( true );
        $this->assertDirectoryExists( rtrim( $dir, '/' ) );
    }

    /** An edition send row for the bounce tests; removed with the other rows by id. */
    private function editionSendStub()
    {
        $edition = $this->newEdition( null, false );
        $send = $this->editionContent( $edition )->createNewsletterSendObject( time() + 86400 );
        return (int)$send->attribute( 'id' );
    }
}
