<?php
/**
 * The hide cronjob (cronjobs/hide.php, \Exponential\Cronjob\Kernel\Hide) tells the search engine about every
 * node it hides, and the content/cache event it causes carries the object ids as well as the node ids.
 *
 *  HC-01 - A node whose date in content.ini [HideSettings] HideDateAttributeList has passed is hidden, and the
 *          search engine's updateNodeVisibility() is called with the node and 'hide'
 *  HC-02 - The content/cache event of the hide gives listeners ( $nodeIDs, $objectIDs ) with the hidden object's
 *          id; a listener that takes only the node ids keeps working
 *
 * Live database, nothing kept: a throwaway folder and one object below it are created inside a database
 * transaction that is rolled back at the end, and the cronjob runs only below that folder (RootNodeList), so no
 * content of the installation is hidden. The search engine is a recording double for the test.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits 3298b53584, 89a5a73877), Copyright (C)
 * Mugo Web, GPL v2.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/runnable/HideCronjobSearchIndexTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class HideCronjobSearchIndexTest extends PHPUnit\Framework\TestCase
{
    /** @var string|null */
    private static $unavailable;

    /** @var bool */
    private $inTransaction = false;

    /** @var array settings changed by the test: array( file, block, name, had, old value ) */
    private $settings = array();

    /** @var string */
    private $engineKey;

    /** @var array|null array( engine ) or null when there was none */
    private $engineBackup;

    /** @var int[] content/cache listener ids */
    private $listeners = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$unavailable = ezpLiveInstallation::unavailableReason();
    }

    protected function setUp(): void
    {
        parent::setUp();
        if ( self::$unavailable !== null )
            $this->markTestSkipped( self::$unavailable );
        $admin = eZUser::fetchByName( 'admin' );
        if ( !$admin instanceof eZUser )
            $this->markTestSkipped( 'needs the admin user' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        $this->engineKey = 'eZSearchPlugin_' . $GLOBALS['eZCurrentAccess']['name'];
        $this->engineBackup = isset( $GLOBALS[$this->engineKey] ) ? array( $GLOBALS[$this->engineKey] ) : null;
    }

    protected function tearDown(): void
    {
        if ( $this->inTransaction )
        {
            eZDB::instance()->rollback();
            $this->inTransaction = false;
        }
        foreach ( $this->listeners as $id )
            ezpEvent::getInstance()->detach( 'content/cache', $id );
        $this->listeners = array();
        foreach ( array_reverse( $this->settings ) as $s )
        {
            list( $file, $block, $name, $had, $old ) = $s;
            $ini = eZINI::instance( $file );
            if ( $had )
                $ini->setVariable( $block, $name, $old );
            else
                $ini->removeSetting( $block, $name );
        }
        $this->settings = array();
        if ( $this->engineKey !== null )
        {
            if ( $this->engineBackup === null )
                unset( $GLOBALS[$this->engineKey] );
            else
                $GLOBALS[$this->engineKey] = $this->engineBackup[0];
        }
        eZContentObject::clearCache();
        parent::tearDown();
    }

    private function setting( $file, $block, $name, $value )
    {
        $ini = eZINI::instance( $file );
        $had = $ini->hasVariable( $block, $name );
        $this->settings[] = array( $file, $block, $name, $had, $had ? $ini->variable( $block, $name ) : null );
        $ini->setVariable( $block, $name, $value );
    }

    /** @return array( class identifier, attribute identifier ) of a class with a date or integer attribute */
    private function dateClass()
    {
        $rows = eZDB::instance()->arrayQuery(
            "SELECT c.identifier AS class_identifier, a.identifier AS attribute_identifier
             FROM ezcontentclass c, ezcontentclass_attribute a
             WHERE a.contentclass_id = c.id AND c.version = 0 AND a.version = 0
               AND a.data_type_string IN ( 'ezdatetime', 'ezdate', 'ezinteger' )
             ORDER BY a.data_type_string DESC, c.id", array( 'limit' => 1 ) );
        if ( !$rows || !eZContentClass::fetchByIdentifier( 'folder' ) )
            $this->markTestSkipped( 'needs the folder class and a class with a date or integer attribute' );
        return array( $rows[0]['class_identifier'], $rows[0]['attribute_identifier'] );
    }

    /** HC-01, HC-02 */
    public function testHideUpdatesTheSearchIndexAndSendsObjectIds()
    {
        list( $classIdentifier, $attributeIdentifier ) = $this->dateClass();
        $db = eZDB::instance();
        $db->begin();
        $this->inTransaction = true;

        $folder = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => 2, 'class_identifier' => 'folder',
            'attributes' => array( 'name' => 'Hide cronjob test ' . getmypid() ) ) );
        $this->assertInstanceOf( 'eZContentObject', $folder );
        $folderNodeID = (int)$folder->attribute( 'main_node_id' );
        $item = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => $folderNodeID, 'class_identifier' => $classIdentifier,
            'attributes' => array( $attributeIdentifier => (string)( time() - 3600 ) ) ) );
        $this->assertInstanceOf( 'eZContentObject', $item );
        $itemNodeID = (int)$item->attribute( 'main_node_id' );
        $itemObjectID = (int)$item->attribute( 'id' );

        $this->setting( 'content.ini', 'HideSettings', 'RootNodeList', array( $folderNodeID ) );
        $this->setting( 'content.ini', 'HideSettings', 'HideDateAttributeList', array( $classIdentifier => $attributeIdentifier ) );
        $this->setting( 'site.ini', 'SiteAccessSettings', 'ShowHiddenNodes', eZINI::instance()->variable( 'SiteAccessSettings', 'ShowHiddenNodes' ) );

        $engine = new class extends eZSearchEngine {
            public $visibility = array();
            public function updateNodeVisibility( $nodeID, $action )
            {
                $this->visibility[] = array( (int)$nodeID, $action );
                return true;
            }
        };
        $GLOBALS[$this->engineKey] = $engine;

        $withObjects = array();
        $nodesOnly = array();
        $this->listeners[] = ezpEvent::getInstance()->attach( 'content/cache', function ( $nodeIDs, $objectIDs = null ) use ( &$withObjects ) {
            $withObjects[] = array( (array)$nodeIDs, (array)$objectIDs );
        } );
        $this->listeners[] = ezpEvent::getInstance()->attach( 'content/cache', function ( $nodeIDs ) use ( &$nodesOnly ) {
            $nodesOnly[] = (array)$nodeIDs;
        } );

        $cli = eZCLI::instance();
        $quiet = $cli->isQuiet();
        $cli->setIsQuiet( true );
        try
        {
            $part = \Exponential\Cronjob\Kernel\Hide::create( 'cronjobs/hide.php' );
            $part->run( array( 'cli' => $cli, 'isQuiet' => true ) );
        }
        finally
        {
            $cli->setIsQuiet( $quiet );
        }

        eZContentObject::clearCache();
        $node = eZContentObjectTreeNode::fetch( $itemNodeID );
        $this->assertSame( 1, (int)$node->attribute( 'is_hidden' ) );
        $this->assertSame( array( array( $itemNodeID, 'hide' ) ), $engine->visibility );

        $this->assertNotEmpty( $withObjects );
        $objectIDs = array();
        foreach ( $withObjects as $call )
            $objectIDs = array_merge( $objectIDs, array_map( 'intval', $call[1] ) );
        $this->assertContains( $itemObjectID, $objectIDs );
        $this->assertCount( count( $withObjects ), $nodesOnly );
        $nodeIDs = array();
        foreach ( $nodesOnly as $call )
            $nodeIDs = array_merge( $nodeIDs, array_map( 'intval', $call ) );
        $this->assertContains( $itemNodeID, $nodeIDs );

        $db->rollback();
        $this->inTransaction = false;
        eZContentObject::clearCache();
        $this->assertNull( eZContentObjectTreeNode::fetch( $folderNodeID ) );
        $this->assertNull( eZContentObject::fetch( $itemObjectID ) );
    }
}
