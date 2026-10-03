<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** Virtual lists: a list whose subscribers are chosen by filters over the users of its parent list. */
class cjwNewsletterVirtualListTest extends cjwNewsletterTestCase
{
    /** @return array( eZContentObject, CjwNewsletterListVirtual ) a published virtual list under the test list */
    private function newVirtualList( $filterXml = '' )
    {
        $class = eZContentClass::fetchByIdentifier( 'cjw_newsletter_list_virtual' );
        $object = $class->instantiate( (int)eZUser::currentUserID(), 0, 0 );
        $object->store();
        eZNodeAssignment::create( array( 'contentobject_id' => $object->attribute( 'id' ), 'contentobject_version' => 1,
            'parent_node' => self::LIST_NODE_ID, 'is_main' => 1, 'sort_field' => 2, 'sort_order' => 0 ) )->store();
        $map = $object->currentVersion()->dataMap();
        $map['title']->setAttribute( 'data_text', 'NLTEST virtual list' );
        $map['title']->store();
        $attr = null;
        foreach ( $map as $a )
            if ( $a->attribute( 'data_type_string' ) === 'cjwnewsletterlistvirtual' )
                $attr = $a;
        $this->assertNotNull( $attr, 'the virtual list class has its datatype' );
        $row = new CjwNewsletterListVirtual( array(
            'contentobject_attribute_id' => $attr->attribute( 'id' ), 'contentobject_attribute_version' => 1,
            'contentobject_id' => $object->attribute( 'id' ), 'contentclass_id' => $class->attribute( 'id' ),
            'main_siteaccess' => 'site', 'siteaccess_array_string' => ';site;', 'output_format_array_string' => ';0;1;',
            'email_sender' => 'newsletter@example.com', 'email_sender_name' => 'S', 'email_reply_to' => '', 'email_return_path' => '',
            'email_receiver_test' => 'nltest@example.invalid', 'skin_name' => 'default', 'virtual_filter' => $filterXml ) );
        $row->store();
        $this->createdObjectIds[] = (int)$object->attribute( 'id' );
        eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $object->attribute( 'id' ), 'version' => 1 ) );
        eZDB::instance()->query( 'DELETE FROM cjwnl_list WHERE contentobject_id = ' . (int)$object->attribute( 'id' ) . ' AND is_virtual = 0' );
        return array( eZContentObject::fetch( $object->attribute( 'id' ) ), CjwNewsletterListVirtual::fetch( $attr->attribute( 'id' ), 1 ) );
    }

    public function testVirtualListKnowsItsParentList()
    {
        list( $object, $list ) = $this->newVirtualList();
        $this->assertInstanceOf( 'CjwNewsletterListVirtual', $list );
        $this->assertSame( 1, (int)$list->attribute( 'is_virtual' ) );
        $this->assertSame( self::LIST_OBJECT_ID, (int)$list->getParentListContentObjectId() );
        $this->assertSame( self::LIST_OBJECT_ID, (int)$list->attribute( 'parent_list_contentobject_id' ) );
        $this->assertSame( 0, (int)$list->getParentListContentObjectIdByListId( 999999999 ) );
    }

    public function testVirtualListSubscribersAreTheParentsFilteredByTheirFilters()
    {
        $a = $this->newSubscriber( 'va' );
        $a->setAttribute( 'salutation', 1 );
        $a->store();
        $b = $this->newSubscriber( 'vb' );
        $b->setAttribute( 'salutation', 2 );
        $b->store();
        list( $object, $list ) = $this->newVirtualList();
        $all = $list->getSubscriptionObjectArray( CjwNewsletterSubscription::STATUS_APPROVED );
        $emails = array();
        foreach ( $all as $sub )
            $emails[] = $sub->attribute( 'newsletter_user' )->attribute( 'email' );
        $this->assertContains( $a->attribute( 'email' ), $emails );
        $this->assertContains( $b->attribute( 'email' ), $emails );
        $this->assertSame( count( $all ), (int)$list->getSubscriptionObjectCount( CjwNewsletterSubscription::STATUS_APPROVED ) );
        $this->assertTrue( $all[0]->isVirtual() );
        $this->assertGreaterThanOrEqual( 2, (int)$list->getUserCount() );
        $stat = $list->getUserCountStatistic();
        $this->assertGreaterThanOrEqual( 2, $stat['all'] );
    }

    public function testFilterXmlNarrowsTheSubscribers()
    {
        $a = $this->newSubscriber( 'fa' );
        $b = $this->newSubscriber( 'fb' );
        $filter = new CjwNewsletterFilter();
        $filter->addFilter( 'cjwnl_email', 'eq', array( $a->attribute( 'email' ) ) );
        list( $object, $list ) = $this->newVirtualList( $filter->toXML() );
        $subs = $list->getSubscriptionObjectArray( CjwNewsletterSubscription::STATUS_APPROVED );
        $this->assertCount( 1, $subs );
        $this->assertSame( $a->attribute( 'email' ), $subs[0]->attribute( 'newsletter_user' )->attribute( 'email' ) );
        $this->assertSame( 1, (int)$list->getSubscriptionObjectCount( CjwNewsletterSubscription::STATUS_APPROVED ) );
        $this->assertStringContainsString( 'cjwnl_email', $list->generateFilterXML() );
    }

    public function testVirtualSubscriptionOfAUserAndAnEditionSend()
    {
        $user = $this->newSubscriber( 'vs' );
        list( $object, $list ) = $this->newVirtualList();
        $edition = $this->newEdition();
        $send = $this->editionContent( $edition )->createNewsletterSendObject( time() + 86400 );
        $send->setAttribute( 'list_contentobject_id', $object->attribute( 'id' ) );
        $send->store();
        $virtual = CjwNewsletterSubscriptionVirtual::createByUserIdAndEditionSendId( $user->attribute( 'id' ), $send->attribute( 'id' ) );
        $this->assertInstanceOf( 'CjwNewsletterSubscriptionVirtual', $virtual );
        $this->assertTrue( $virtual->isVirtual() );
        $this->assertSame( 'v.' . $object->attribute( 'id' ) . '.' . $user->attribute( 'id' ), $virtual->attribute( 'id' ) );
        $this->assertStringStartsWith( 'v.' . $object->attribute( 'id' ) . '.', $virtual->attribute( 'hash' ) );
        $this->assertFalse( CjwNewsletterSubscriptionVirtual::createByUserIdAndEditionSendId( 999999999, $send->attribute( 'id' ) ) );
        $this->assertFalse( CjwNewsletterSubscriptionVirtual::createByUserIdAndEditionSendId( $user->attribute( 'id' ), 999999999 ) );
    }

    public function testRowsBecomeVirtualSubscriptions()
    {
        $user = $this->newSubscriber( 'rows' );
        $row = CjwNewsletterUser::fetch( $user->attribute( 'id' ), false );
        $list = CjwNewsletterSubscriptionVirtual::createFromUserRowArray( array( $row ), 4711, true );
        $this->assertCount( 1, $list );
        $raw = CjwNewsletterSubscriptionVirtual::createFromUserRowArray( array( $row ), 4711, false );
        $this->assertSame( ';0;', $raw[0]['output_format_array_string'] );
        $this->assertSame( array(), CjwNewsletterSubscriptionVirtual::createFromUserRowArray( 'nothing', 4711, true ) );
        $this->assertInstanceOf( 'CjwNewsletterSubscriptionVirtual', CjwNewsletterSubscriptionVirtual::createFromUserRow( $row, 4711, true ) );
    }

    public function testSeveralExternalFiltersAreJoinedWithAndExists()
    {
        $filter = array( 'fields' => array( 'x.id' ), 'tables' => array( 'x' ), 'conds' => array( array( 'x.id' => array( '=', '1' ) ) ) );
        $sql = CjwNewsletterSubscriptionVirtual::getFilterExternalSql( array( $filter, $filter ) );
        $this->assertSame( 2, substr_count( $sql, 'AND EXISTS' ) );
        $this->assertStringNotContainsString( '),', $sql, 'no comma between the conditions' );
        $this->assertSame( '', CjwNewsletterSubscriptionVirtual::getFilterExternalSql( array() ) );
    }

    public function testFilterTextEscapesValues()
    {
        $text = CjwNewsletterSubscriptionVirtual::filterText( array( 'cjwnl_user.email' => array( 'like', "x' OR '1'='1" ) ) );
        $this->assertStringContainsString( "x'' OR ''1''=''1", $text, 'the quote is doubled, the value stays one string' );
    }
}
