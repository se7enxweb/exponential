<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** CjwNewsletterList, CjwNewsletterEdition, CjwNewsletterEditionSend, CjwNewsletterEditionSendItem, the function collection. */
class cjwNewsletterEditionTest extends cjwNewsletterTestCase
{
    public function testListContentOfTheTestList()
    {
        $list = CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, 0 );
        $this->assertInstanceOf( 'CjwNewsletterList', $list );
        $this->assertTrue( $list->isValid() );
        $this->assertSame( array( 0 => 'HTML', 1 => 'Text' ), $list->attribute( 'output_format_array' ) );
        $this->assertSame( array( 0 => 'HTML', 1 => 'Text' ), CjwNewsletterList::getAvailableOutputFormatArray() );
        $this->assertContains( 'default', $list->getAvailableSkinArray() );
        $this->assertSame( 'site', $list->attribute( 'main_siteaccess' ) );
    }

    public function testListFetchOfAnythingElseIsFalse()
    {
        $this->assertFalse( CjwNewsletterList::fetchByListObjectVersion( 999999999, 0 ) );
        $this->assertFalse( CjwNewsletterList::fetchByListObjectVersion( 1, 0 ), 'the content root has no list attribute' );
        $this->assertFalse( CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, 999 ) );
        $this->assertNull( CjwNewsletterList::fetch( 999999999, 1 ) );
    }

    public function testListSiteaccessInformation()
    {
        $list = CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, 0 );
        $info = $list->getAvailableSiteaccessList();
        $this->assertArrayHasKey( 'site', $info );
        $this->assertArrayHasKey( 'locale', $info['site'] );
        $this->assertNotSame( '-', $info['site']['locale'], 'the site ini of a siteaccess is read through the command line' );
        $list->getSiteIniObjectBySiteAccessName( 'no such access; touch x' ); // the name is one shell argument
        $this->assertFileDoesNotExist( eZSys::siteDir() . 'x' );
        $this->assertIsArray( $list->getSiteaccessSiteIniArray() );
    }

    public function testListSubscriberCounts()
    {
        $this->newSubscriber( 'cnt' );
        $list = CjwNewsletterList::fetchByListObjectVersion( self::LIST_OBJECT_ID, 0 );
        $this->assertGreaterThanOrEqual( 1, $list->getUserCount() );
        $this->assertGreaterThanOrEqual( 1, $list->getSubscriptionObjectCount( CjwNewsletterSubscription::STATUS_APPROVED ) );
        $this->assertGreaterThanOrEqual( 1, count( $list->getSubscriptionObjectArray( CjwNewsletterSubscription::STATUS_APPROVED ) ) );
        $this->assertArrayHasKey( 'approved', $list->getUserCountStatistic() );
    }

    public function testNewEditionIsADraftAndRendersAllOutputFormats()
    {
        $edition = $this->newEdition();
        $content = $this->editionContent( $edition );
        $this->assertInstanceOf( 'CjwNewsletterEdition', $content );
        $this->assertTrue( $content->isDraft() );
        $this->assertFalse( $content->isProcess() );
        $this->assertFalse( $content->isArchive() );
        $this->assertFalse( $content->isAbort() );
        $this->assertSame( CjwNewsletterEdition::STATUS_DRAFT, $content->getStatus() );
        $this->assertInstanceOf( 'CjwNewsletterList', $content->attribute( 'list_attribute_content' ) );
        foreach ( array( 0, 1 ) as $format )
        {
            $out = CjwNewsletterEdition::getOutput( $edition->attribute( 'id' ), 1, $format, 'site', 'default' );
            $this->assertArrayNotHasKey( 'error', $out, "format $format" );
            $this->assertStringContainsString( 'NLTEST', $out['subject'] );
            $this->assertStringContainsString( 'NLTEST article body line', $out['body']['text'] );
            $this->assertStringContainsString( '#_hash_unsubscribe_#', $out['body']['text'], 'the personal links are placeholders until the mail is made' );
        }
    }

    public function testGetOutputOfAMissingEditionIsAnErrorResultNotAFatal()
    {
        $out = CjwNewsletterEdition::getOutput( 999999999, 1, 0, 'site', 'default' );
        $this->assertIsArray( $out );
        $out2 = CjwNewsletterEdition::getOutput( 1, 1, 0, 'no such access', 'default' );
        $this->assertIsArray( $out2 );
    }

    public function testGetOutputKeepsShellMetacharactersOutOfTheCommand()
    {
        $marker = eZSys::siteDir() . 'var/tmp/nltest-injection-' . getmypid();
        $out = CjwNewsletterEdition::getOutput( 1, 1, 0, 'site; touch ' . $marker . ' #', 'default; touch ' . $marker );
        $this->assertIsArray( $out );
        $this->assertFileDoesNotExist( $marker );
        $this->assertFileDoesNotExist( $marker . ' #' );
    }

    public function testCreateOutputXmlHasEveryFormat()
    {
        $edition = $this->newEdition();
        $xml = $this->editionContent( $edition )->createOutputXml();
        $doc = new DOMDocument();
        $this->assertTrue( $doc->loadXML( $xml ) );
        $this->assertSame( 2, $doc->getElementsByTagName( 'output_format' )->length );
    }

    public function testEditionSendLifecycleAndStatistics()
    {
        $user = $this->newSubscriber( 'send' );
        $edition = $this->newEdition();
        $content = $this->editionContent( $edition );
        $send = $content->createNewsletterSendObject( time() - 10 );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_WAIT_FOR_SCHEDULE, (int)$send->attribute( 'status' ) );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{32}$/', $send->attribute( 'hash' ) );
        $this->assertSame( $send->attribute( 'id' ), CjwNewsletterEditionSend::fetchByHash( $send->attribute( 'hash' ) )->attribute( 'id' ) );
        $this->assertCount( 1, CjwNewsletterEditionSend::fetchByEditionContentObjectId( $edition->attribute( 'id' ) ) );
        $this->assertCount( 1, CjwNewsletterEditionSend::fetchByEditionContentObjectIdVersion( $edition->attribute( 'id' ), 1 ) );
        $this->assertCount( 1, CjwNewsletterEditionSend::fetchByEditionContentObjectIdAndStatus( $edition->attribute( 'id' ), array( CjwNewsletterEditionSend::STATUS_WAIT_FOR_SCHEDULE ) ) );
        $this->assertCount( 0, CjwNewsletterEditionSend::fetchByEditionContentObjectIdVersionAndStatus( $edition->attribute( 'id' ), 1, array( CjwNewsletterEditionSend::STATUS_ABORT ) ) );
        $this->assertTrue( $this->editionContent( $edition )->isProcess(), 'a waiting send is a process' );
        $this->assertArrayHasKey( 1, $send->getParsedOutputXml() === array() ? array( 1 => 1 ) : $send->getParsedOutputXml() );
        $parsed = $send->getParsedOutputXml();
        $this->assertSame( array( 0, 1 ), array_map( 'intval', array_keys( $parsed ) ) );
        $this->assertStringContainsString( 'NLTEST', $parsed[0]['body']['html'] );
        $this->assertSame( array( 0 => 'HTML', 1 => 'Text' ), $send->attribute( 'output_format_array' ) );

        $stat = $send->getSendItemsStatistic();
        $this->assertSame( 0, $stat['items_count'] );
        $this->assertSame( 0, $stat['items_send_in_percent'] + 0, 'no division by zero for an empty send' );

        $item = CjwNewsletterEditionSendItem::create( $send->attribute( 'id' ), $user->attribute( 'id' ), 0, $this->subscriptionOf( $user )->attribute( 'id' ) );
        $this->assertFalse( CjwNewsletterEditionSendItem::create( $send->attribute( 'id' ), $user->attribute( 'id' ), 0, 1 ), 'no duplicate item' );
        $this->assertSame( 1, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), false ) );
        $this->assertSame( 1, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW ) );
        $this->assertSame( 1, count( CjwNewsletterEditionSendItem::fetchListSendIdAndStatus( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW, 0, 0 ) ) );
        $this->assertSame( 1, CjwNewsletterEditionSendItem::fetchListByNewsletterIdCount( $user->attribute( 'id' ) ) );
        $this->assertCount( 1, CjwNewsletterEditionSendItem::fetchListByNewsletterUserId( 0, 0, $user->attribute( 'id' ) ) );
        $this->assertCount( 1, CjwNewsletterEditionSendItem::fetchListByNewsletterUserIdAndStatus( 0, 0, $user->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW ) );
        $this->assertGreaterThanOrEqual( 1, CjwNewsletterEditionSendItem::fetchListByStatusCount( CjwNewsletterEditionSendItem::STATUS_NEW ) );
        $this->assertSame( 0, CjwNewsletterEditionSendItem::fetchListByNewsletterIdCount( 'not a number' ) );
        $this->assertSame( $user->attribute( 'id' ), $item->getNewsletterUserObject()->attribute( 'id' ) );
        $this->assertSame( $edition->attribute( 'id' ), $item->getNewsletterEditionObject()->attribute( 'id' ) );
        $this->assertFalse( $item->isSubscriptionVirtual() );
        $this->assertSame( 'New', $item->getStatusString() );

        $this->assertTrue( (bool)$send->abortAllSendItems() );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_ABORT, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );
        $this->assertSame( CjwNewsletterEditionSendItem::STATUS_ABORT,
            (int)CjwNewsletterEditionSendItem::fetchByHash( $item->attribute( 'hash' ) )->attribute( 'status' ) );
        $this->assertTrue( $this->editionContent( $edition )->isAbort() );
    }

    public function testEditionSendWithDamagedOutputXmlParsesToNothing()
    {
        $send = new CjwNewsletterEditionSend( array( 'output_xml' => '' ) );
        $this->assertSame( array(), $send->getParsedOutputXml() );
        $send->setAttribute( 'output_xml', '<not xml' );
        $this->assertSame( array(), $send->getParsedOutputXml() );
        $send->setAttribute( 'output_xml', '<xml><nothing/></xml>' );
        $this->assertSame( array(), $send->getParsedOutputXml() );
    }

    public function testEditionSendStatusSetsItsTimestamps()
    {
        $send = new CjwNewsletterEditionSend( array() );
        $send->setAttribute( 'status', CjwNewsletterEditionSend::STATUS_MAILQUEUE_CREATED );
        $this->assertGreaterThan( 0, (int)$send->attribute( 'mailqueue_created' ) );
        $send->setAttribute( 'status', CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_STARTED );
        $this->assertGreaterThan( 0, (int)$send->attribute( 'mailqueue_process_started' ) );
        $send->setAttribute( 'status', CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED );
        $this->assertGreaterThan( 0, (int)$send->attribute( 'mailqueue_process_finished' ) );
        $send->setAttribute( 'status', CjwNewsletterEditionSend::STATUS_ABORT );
        $this->assertGreaterThan( 0, (int)$send->attribute( 'mailqueue_process_aborted' ) );
    }

    public function testSendItemOfAGoneSendObjectDoesNotFatal()
    {
        $user = $this->newSubscriber( 'gone' );
        $item = CjwNewsletterEditionSendItem::create( 999997, $user->attribute( 'id' ), 0, $this->subscriptionOf( $user )->attribute( 'id' ) );
        $this->assertFalse( $item->getNewsletterEditionObject() );
        $this->assertSame( 'New', $item->getStatusString() );
        $item->setAttribute( 'status', CjwNewsletterEditionSendItem::STATUS_SEND );
        $this->assertGreaterThan( 0, (int)$item->attribute( 'processed' ) );
        $item->store();
        $this->assertSame( 'Send', $item->getStatusString() );
        $this->assertSame( 1, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( 999997, CjwNewsletterEditionSendItem::STATUS_SEND ) );
        $this->assertGreaterThan( 0, $item->setBounced() );
        $this->assertSame( 0, CjwNewsletterEditionSendItem::fetchBounceCountByEditionSendId( 12345678 ) );
    }

    public function testFunctionCollectionFetches()
    {
        $this->newSubscriber( 'fc' );
        $c = 'CjwNewsletterFunctionCollection';
        foreach ( array( 'pending', 'confirmed', 'approved', 'removed', 'bounced', 'blacklisted', '' ) as $status )
        {
            $r = $c::fetchSubscriptionList( self::LIST_OBJECT_ID, 0, $status, 50, 0, true );
            $this->assertIsArray( $r['result'], $status );
            $this->assertIsNumeric( $c::fetchSubscriptionListCount( self::LIST_OBJECT_ID, 0, $status )['result'], $status );
        }
        $this->assertSame( array(), $c::fetchSubscriptionList( 999999999, 0, 'approved', 50, 0, true )['result'], 'an unknown list is an empty result and no warning' );
        $this->assertSame( 0, $c::fetchSubscriptionListCount( 999999999, 0, 'approved' )['result'] );
        $this->assertIsArray( $c::fetchUserList( 5, 0, false, true )['result'] );
        $this->assertIsNumeric( $c::fetchUserListCount( false )['result'] );
        $this->assertIsArray( $c::fetchEditonSendItemList( 5, 0, 999999999, true )['result'] );
        $this->assertSame( 0, $c::fetchEditonSendItemListCount( 999999999 )['result'] );
        $this->assertIsArray( $c::fetchImportSubscriptionList( 999999999, 5, 0, true )['result'] );
        $this->assertSame( 0, (int)$c::fetchImportSubscriptionListCount( 999999999 )['result'] );
    }
}
