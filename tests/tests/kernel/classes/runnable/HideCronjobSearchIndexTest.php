<?php
/**
 * The hide cronjob (cronjobs/hide.php, \Exponential\Cronjob\Kernel\Hide) tells the search engine about every
 * node it hides, and the content/cache event it causes carries the object ids as well as the node ids.
 *
 *  HC-01 - A node whose date in content.ini [HideSettings] HideDateAttributeList has passed is hidden, and the
 *          search engine's updateNodeVisibility() is called with the node and 'hide'
 *  HC-02 - The content/cache event of the hide gives listeners ( $nodeIDs, $objectIDs ) with the hidden object's
 *          id; a listener that takes only the node ids keeps working (with ViewCaching enabled, as only then the
 *          view caches are cleared and the event is sent)
 *  HC-03 - The cronjob ends when the process read ShowHiddenNodes=true before it (an earlier test or cronjob part):
 *          it sets ShowHiddenNodes=false and the fetches follow it, so hidden nodes are not fetched again
 *  HC-04 - After the cronjob, ShowHiddenNodes and the value showInvisibleNodes() returns are as before it, so the
 *          cronjob parts after it in the same process see the site's own setting
 *  HC-05 - A node of RootNodeList that does not exist is skipped with a warning instead of a fatal error
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
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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

    /** @var callable|null the error handler on top of PHP's stack before the test (PHPUnit's) */
    private $errorHandlerBefore;

    /** @var callable the marker pushed above it, see pushErrorHandlerMarker() */
    private $errorHandlerMarker;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$unavailable = ezpLiveInstallation::unavailableReason();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->pushErrorHandlerMarker();
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
        // a class dateClass() created inside the transaction is gone; forget it in the class caches too
        eZContentClass::expireCache();
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
        // the cronjob read ShowHiddenNodes=false into the cache; later tests read the restored setting again
        eZContentObjectTreeNode::resetShowInvisibleNodesCache();
        if ( $this->engineKey !== null )
        {
            if ( $this->engineBackup === null )
                unset( $GLOBALS[$this->engineKey] );
            else
                $GLOBALS[$this->engineKey] = $this->engineBackup[0];
        }
        eZContentObject::clearCache();
        $this->popErrorHandlersToMarker();
        parent::tearDown();
    }

    /**
     * Pushes a marker onto PHP's stack of error handlers that passes every error on to the handler below it.
     * With ViewCaching enabled the hide clears view caches, and the debug messages written then make eZDebug switch
     * its handling (eZDebug::setHandleType()): it takes the top handler off the stack, assuming it is its own, and
     * pushes its own again. Without the marker that top handler was PHPUnit's, and the test was risky ("removed error
     * handlers other than its own"). popErrorHandlersToMarker() leaves the stack as it was before the test.
     */
    private function pushErrorHandlerMarker()
    {
        $this->errorHandlerBefore = self::currentErrorHandler();
        // eZDebug's own handler on top (the kernel was started before the test, as with a throwaway database): its
        // switches take off and put back that handler in pairs, so the stack ends as it began. A marker above it
        // would be taken off in its place and eZDebug's handler pushed a second time, one handler too many.
        if ( is_array( $this->errorHandlerBefore ) && $this->errorHandlerBefore[0] instanceof eZDebug )
        {
            $this->errorHandlerMarker = null;
            return;
        }
        $before = $this->errorHandlerBefore;
        $this->errorHandlerMarker = static function () use ( $before ) {
            return is_callable( $before ) ? call_user_func_array( $before, func_get_args() ) : false;
        };
        set_error_handler( $this->errorHandlerMarker );
    }

    /**
     * Pops the error handlers the tested code left above the marker and the marker itself; stops at the handler that
     * was on top before the test when the tested code took the marker off already.
     */
    private function popErrorHandlersToMarker()
    {
        if ( $this->errorHandlerMarker === null )
            return;
        for ( $i = 0; $i < 100; $i++ )
        {
            $current = self::currentErrorHandler();
            if ( $current === $this->errorHandlerMarker )
            {
                restore_error_handler();
                break;
            }
            if ( $current === $this->errorHandlerBefore )
                break;
            restore_error_handler();
        }
        $this->errorHandlerMarker = null;
    }

    /** @return callable|null the error handler on top of PHP's stack, which stays as it is */
    private static function currentErrorHandler()
    {
        $current = set_error_handler( null );
        restore_error_handler();
        return $current;
    }

    private function setting( $file, $block, $name, $value )
    {
        $ini = eZINI::instance( $file );
        $had = $ini->hasVariable( $block, $name );
        $this->settings[] = array( $file, $block, $name, $had, $had ? $ini->variable( $block, $name ) : null );
        $ini->setVariable( $block, $name, $value );
    }

    /**
     * A class with a date or integer attribute. When the database has none (a clean install, as the throwaway
     * database of a test run), one is created; call it inside the test's transaction, so the rollback removes it.
     *
     * @return array( class identifier, attribute identifier )
     */
    private function dateClass()
    {
        if ( !eZContentClass::fetchByIdentifier( 'folder' ) )
            $this->markTestSkipped( 'needs the folder class' );
        $rows = eZDB::instance()->arrayQuery(
            "SELECT c.identifier AS class_identifier, a.identifier AS attribute_identifier
             FROM ezcontentclass c, ezcontentclass_attribute a
             WHERE a.contentclass_id = c.id AND c.version = 0 AND a.version = 0
               AND a.data_type_string IN ( 'ezdatetime', 'ezdate', 'ezinteger' )
             ORDER BY a.data_type_string DESC, c.id", array( 'limit' => 1 ) );
        if ( $rows )
            return array( $rows[0]['class_identifier'], $rows[0]['attribute_identifier'] );

        $this->assertTrue( $this->inTransaction, 'the class is created inside the test transaction only' );
        $locale = eZContentObject::defaultLanguage();
        $class = eZContentClass::create( false, array( 'name' => 'Hide cronjob test item', 'identifier' => 'hide_cronjob_test_item',
                                                       'contentobject_name' => '<name>' ), $locale );
        $class->store();
        $classID = (int)$class->attribute( 'id' );
        $groups = eZContentClassGroup::fetchList();
        eZContentClassClassGroup::create( $classID, $class->attribute( 'version' ), $groups[0]->attribute( 'id' ), $groups[0]->attribute( 'name' ) )->store();
        $attributes = array();
        foreach ( array( array( 'name', 'ezstring' ), array( 'hide_date', 'ezinteger' ) ) as $i => $def )
        {
            $attribute = eZContentClassAttribute::create( $classID, $def[1], array( 'identifier' => $def[0], 'name' => $def[0], 'placement' => $i + 1 ), $locale );
            $attribute->dataType()->initializeClassAttribute( $attribute );
            $attribute->store();
            $attributes[] = $attribute;
        }
        $class->storeDefined( $attributes );
        eZContentClass::expireCache();
        return array( 'hide_cronjob_test_item', 'hide_date' );
    }

    /** HC-01, HC-02, HC-03, HC-04 */
    public function testHideUpdatesTheSearchIndexAndSendsObjectIds()
    {
        $db = eZDB::instance();
        $db->begin();
        $this->inTransaction = true;
        list( $classIdentifier, $attributeIdentifier ) = $this->dateClass();

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
        // HC-02: eZContentCacheManager sends content/cache only when it clears view caches
        $this->setting( 'site.ini', 'ContentSettings', 'ViewCaching', 'enabled' );
        // as after an earlier fetch of this process with ShowHiddenNodes=true: the cronjob must not loop on it
        $this->setting( 'site.ini', 'SiteAccessSettings', 'ShowHiddenNodes', 'true' );
        eZContentObjectTreeNode::resetShowInvisibleNodesCache();
        $this->assertTrue( eZContentObjectTreeNode::showInvisibleNodes() );

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

        // HC-04: the setting and the cached value are as before the cronjob
        $this->assertSame( 'true', eZINI::instance()->variable( 'SiteAccessSettings', 'ShowHiddenNodes' ) );
        $this->assertTrue( eZContentObjectTreeNode::showInvisibleNodes() );

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

    /** HC-05, HC-04 */
    public function testARemovedRootNodeIsSkippedAndTheSettingIsRestored()
    {
        // the class is never looked at: there is no root node to fetch below
        list( $classIdentifier, $attributeIdentifier ) = array( 'folder', 'name' );
        $missing = (int)eZDB::instance()->arrayQuery( 'SELECT MAX( node_id ) AS m FROM ezcontentobject_tree' )[0]['m'] + 1000;
        $this->assertNull( eZContentObjectTreeNode::fetch( $missing ) );
        $this->setting( 'content.ini', 'HideSettings', 'RootNodeList', array( $missing ) );
        $this->setting( 'content.ini', 'HideSettings', 'HideDateAttributeList', array( $classIdentifier => $attributeIdentifier ) );
        $this->setting( 'site.ini', 'SiteAccessSettings', 'ShowHiddenNodes', 'true' );
        eZContentObjectTreeNode::resetShowInvisibleNodesCache();

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

        $this->assertSame( 'true', eZINI::instance()->variable( 'SiteAccessSettings', 'ShowHiddenNodes' ) );
        $this->assertTrue( eZContentObjectTreeNode::showInvisibleNodes() );
    }
}
