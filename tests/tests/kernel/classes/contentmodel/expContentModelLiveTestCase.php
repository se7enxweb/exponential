<?php
/**
 * Base class of the content model tests in this directory, which run against the installation's own database
 * (the kernel started on the admin siteaccess by ezpLiveInstallation, skipped where there is none, as on CI).
 *
 * Each test class gets a throwaway folder below the media root, named "k1c <class> <id>", made in
 * setUpBeforeClass(); everything a test creates goes below it or is tracked with track(). tearDownAfterClass()
 * removes the folder's subtree and every tracked object, from the trash too, and checks nothing is left.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

abstract class expContentModelLiveTestCase extends PHPUnit\Framework\TestCase
{
    /** @var array( 'object' => int, 'node' => int ) the throwaway root of the running class */
    protected static $root;

    /** @var int[] objects created by the running class */
    protected static $createdObjects = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        ezpLiveInstallation::requireOrSkip();
        static::loginAdmin();
        eZContentLanguage::setCronjobMode( true );
        foreach ( array( 'folder', 'article' ) as $identifier )
        {
            if ( !eZContentClass::fetchByIdentifier( $identifier ) )
                self::markTestSkipped( "needs the content class '$identifier' of the standard installation" );
        }
        static::$createdObjects = array();
        static::$root = static::createObject( 'folder', static::mediaRootID(), array( 'name' => 'k1c ' . static::class . ' ' . uniqid() ) );
    }

    public static function tearDownAfterClass(): void
    {
        if ( static::$createdObjects )
        {
            static::removeObjects( static::$createdObjects );
            $db = eZDB::instance();
            $left = $db->arrayQuery( 'SELECT id FROM ezcontentobject WHERE ' . $db->generateSQLINStatement( array_map( 'intval', static::$createdObjects ), 'id', false, true, 'int' ) );
            if ( $left )
                throw new RuntimeException( static::class . ' left test objects behind: ' . implode( ', ', array_column( $left, 'id' ) ) );
        }
        static::$createdObjects = array();
        static::$root = null;
        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();
        static::loginAdmin();
    }

    protected function tearDown(): void
    {
        // A test that failed inside a transaction leaves it open; everything after it, the removal of the test
        // content included, would then be rolled back at the end of the run
        $db = eZDB::instance();
        $open = (int)$db->transactionCounter();
        while ( $db->transactionCounter() > 0 )
            $db->rollback();
        parent::tearDown();
        if ( $open > 0 )
            $this->fail( "the test left $open database transaction(s) open; rolled back" );
    }

    protected static function loginAdmin()
    {
        $admin = eZUser::fetchByName( 'admin' );
        if ( $admin instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
    }

    protected static function mediaRootID()
    {
        return (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' );
    }

    /**
     * Creates and publishes an object and tracks it.
     *
     * @return array( 'object' => int, 'node' => int )
     */
    protected static function createObject( $classIdentifier, $parentNodeID, array $attributes, array $extra = array() )
    {
        $params = array_merge( array( 'parent_node_id' => $parentNodeID, 'class_identifier' => $classIdentifier,
                                      'creator_id' => eZUser::currentUserID(), 'attributes' => $attributes ), $extra );
        $object = eZContentFunctions::createAndPublishObject( $params );
        if ( !$object instanceof eZContentObject )
            throw new RuntimeException( "could not create the $classIdentifier object below node $parentNodeID" );
        static::$createdObjects[] = (int)$object->attribute( 'id' );
        eZContentObject::clearCache();
        return array( 'object' => (int)$object->attribute( 'id' ), 'node' => (int)$object->attribute( 'main_node_id' ) );
    }

    /**
     * @return array( 'object' => int, 'node' => int )
     */
    protected static function folder( $parentNodeID, $name, array $more = array() )
    {
        return static::createObject( 'folder', $parentNodeID, array_merge( array( 'name' => $name ), $more ) );
    }

    protected static function track( $objectID )
    {
        static::$createdObjects[] = (int)$objectID;
    }

    /**
     * Removes the objects with every location, and from the trash.
     */
    protected static function removeObjects( array $ids )
    {
        $ids = array_values( array_unique( array_map( 'intval', $ids ) ) );
        if ( !$ids )
            return;
        $db = eZDB::instance();
        $roots = array();
        foreach ( $db->arrayQuery( 'SELECT node_id FROM ezcontentobject_tree WHERE ' . $db->generateSQLINStatement( $ids, 'contentobject_id', false, true, 'int' ) ) as $r )
            $roots[] = (int)$r['node_id'];
        if ( $roots )
            eZContentObjectTreeNode::removeSubtrees( $roots, false );
        eZContentObject::clearCache();
        foreach ( $db->arrayQuery( 'SELECT id, status FROM ezcontentobject WHERE ' . $db->generateSQLINStatement( $ids, 'id', false, true, 'int' ) ) as $r )
        {
            $db->begin();
            if ( (int)$r['status'] === eZContentObject::STATUS_ARCHIVED )
                \Exponential\Service\Trash::purgeObjects( array( (int)$r['id'] ) );
            else if ( $object = eZContentObject::fetch( (int)$r['id'] ) )
                $object->purge();
            $db->commit();
        }
        eZContentObject::clearCache();
    }

    protected static function nodeIDsOf( array $nodes )
    {
        $ids = array();
        foreach ( $nodes as $node )
            $ids[] = (int)$node->attribute( 'node_id' );
        return $ids;
    }

    protected static function namesOf( array $nodes )
    {
        $names = array();
        foreach ( $nodes as $node )
            $names[] = $node->attribute( 'name' );
        return $names;
    }
}
