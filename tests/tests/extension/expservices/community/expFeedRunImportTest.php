<?php
/**
 * expfeed::runImport. The source is an RSS export of a test folder under the Media root, served by this site
 * itself; the import runs into a second test folder. Everything is removed again in tearDown.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../commerce/expCommerceTestCase.php';

class expFeedRunImportTest extends expCommerceTestCase
{
    protected $exportIds = array();
    protected $importIds = array();

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            foreach ( $this->exportIds as $id )
                if ( $e = eZRSSExport::fetch( $id ) )
                    $e->removeThis();
            foreach ( $this->importIds as $id )
                eZPersistentObject::removeObject( eZRSSImport::definition(), array( 'id' => $id ) );
        }
        parent::tearDown();
    }

    /** Source folder with two child folders and an export of it; returns array( import id, destination node ) */
    protected function fixture( $active = 1 )
    {
        $src = $this->testFolder();
        for ( $i = 1; $i <= 2; $i++ )
            $this->createObject( $src, 'folder', array( 'name' => "Feed item $i " . uniqid() ) );
        $dest = (int)$this->createObject( $src, 'folder', array( 'name' => 'Import destination' ) )->attribute( 'main_node_id' );
        $e = $this->ok( 'expFeedServices', 'createExport', array(), array( 'title' => 'runimport', 'access_url' => 'exptest_run' . uniqid(), 'source_node_id' => $src ) );
        $this->exportIds[] = $e['id'];
        // the export of an RSS item needs the class attributes its title and description come from
        foreach ( eZPersistentObject::fetchObjectList( eZRSSExportItem::definition(), null, array( 'rssexport_id' => $e['id'] ) ) as $item )
        {
            $item->setAttribute( 'title', 'name' );
            $item->setAttribute( 'description', 'name' );
            $item->store();
        }
        $site = 'alpha.se7enx.com'; // the admin siteaccess's SiteURL is not a host that resolves
        $class = eZContentClass::fetchByIdentifier( 'folder' );
        $nameAttr = null;
        foreach ( $class->fetchAttributes() as $a )
            if ( $a->attribute( 'identifier' ) === 'name' )
                $nameAttr = (int)$a->attribute( 'id' );
        $i = eZRSSImport::create( (int)eZUser::currentUser()->attribute( 'contentobject_id' ) );
        $i->setAttribute( 'name', 'Test run import' );
        $i->setAttribute( 'url', 'https://' . $site . '/rss/feed/' . $e['access_url'] );
        $i->setAttribute( 'status', eZRSSImport::STATUS_VALID );
        $i->setAttribute( 'active', $active );
        $i->setAttribute( 'destination_node_id', $dest );
        $i->setAttribute( 'class_id', (int)$class->attribute( 'id' ) );
        $i->setImportDescription( array( 'rss_version' => '2.0', 'class_attributes' => array( $nameAttr => 'item - elements - title' ),
                                         'object_attributes' => array() ) );
        $i->store();
        $this->importIds[] = (int)$i->attribute( 'id' );
        return array( (int)$i->attribute( 'id' ), $dest );
    }

    public function testDryRunReportsAndImportsNothing()
    {
        list( $id, $dest ) = $this->fixture();
        $r = $this->ok( 'expFeedServices', 'runImport', array(), array( 'id' => $id, 'dry_run' => 'true' ) );
        $this->assertTrue( $r['dry_run'] );
        $this->assertSame( '2.0', $r['version'] );
        $this->assertGreaterThanOrEqual( 2, $r['found'] );
        $this->assertSame( $r['found'], $r['new'] );
        $this->assertSame( 0, $r['created'] );
        $this->assertContains( 'would_import', array_column( $r['items'], 'state' ) );
        $this->assertSame( 0, $this->ok( 'expFeedServices', 'importStatus', array( $id ) )['objects'] );
    }

    public function testRunImportCreatesTheItemsOnceOnly()
    {
        list( $id, $dest ) = $this->fixture();
        $r = $this->ok( 'expFeedServices', 'runImport', array(), array( 'id' => $id, 'max_items' => 2 ) );
        $this->assertFalse( $r['dry_run'] );
        $this->assertSame( 2, $r['created'], json_encode( $r ) );
        $status = $this->ok( 'expFeedServices', 'importStatus', array( $id ) );
        $this->assertSame( 2, $status['objects'] );
        // the imported objects are removed with the destination: remember them for tearDown
        $db = eZDB::instance();
        foreach ( $db->arrayQuery( "SELECT id FROM ezcontentobject WHERE remote_id LIKE 'RSSImport!_" . (int)$id . "!_%' ESCAPE '!'" ) as $row )
            $this->createdObjects[] = (int)$row['id'];
        $again = $this->ok( 'expFeedServices', 'runImport', array(), array( 'id' => $id, 'dry_run' => 'true' ) );
        $this->assertContains( 'exists', array_column( $again['items'], 'state' ), 'imported items are known the second time' );
    }

    public function testRunImportIsGuarded()
    {
        $this->fails( 404, 'expFeedServices', 'runImport', array(), array( 'id' => 99999999 ) );
        $this->fails( 400, 'expFeedServices', 'runImport', array(), array() );
        list( $id ) = $this->fixture();
        $i = eZRSSImport::fetch( $id );
        $i->setAttribute( 'url', 'file:///etc/passwd' );
        $i->store();
        $this->fails( 422, 'expFeedServices', 'runImport', array(), array( 'id' => $id, 'dry_run' => '1' ) );
        $this->loginAnonymous();
        $this->fails( 401, 'expFeedServices', 'runImport', array(), array( 'id' => $id ) );
        $this->assertTrue( expFeedServices::$services['runImport']['write'] );
    }
}
