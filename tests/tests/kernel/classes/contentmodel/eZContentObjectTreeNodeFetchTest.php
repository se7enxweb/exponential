<?php
/**
 * Fetching nodes of a throwaway subtree with eZContentObjectTreeNode: subtree fetches and counts with depth,
 * depth operators, class filters, attribute filters, object name filters, sorting, limit and offset, several
 * parent paths at once, children, parents, paths and path names, and the node lookups by object, parent, path
 * string and remote id. The sort field names and ids, which need no database, are checked too.
 *
 * The tree (made once for the class, below a throwaway folder in the media root):
 *
 *   root
 *    +- Alpha (folder)
 *    |   +- Apple (article)
 *    |   +- Banana (article)
 *    |   +- Inner (folder)
 *    |       +- Cherry (article)
 *    +- Bravo (folder)
 *    +- Charlie (folder)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expContentModelLiveTestCase.php';

class eZContentObjectTreeNodeFetchTest extends expContentModelLiveTestCase
{
    /** @var array name => array( 'object' => int, 'node' => int ) */
    protected static $tree = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $root = static::$root['node'];
        static::$tree = array();
        static::$tree['Alpha'] = static::folder( $root, 'Alpha' );
        static::$tree['Apple'] = static::article( static::$tree['Alpha']['node'], 'Apple' );
        static::$tree['Banana'] = static::article( static::$tree['Alpha']['node'], 'Banana' );
        static::$tree['Inner'] = static::folder( static::$tree['Alpha']['node'], 'Inner' );
        static::$tree['Cherry'] = static::article( static::$tree['Inner']['node'], 'Cherry' );
        static::$tree['Bravo'] = static::folder( $root, 'Bravo' );
        static::$tree['Charlie'] = static::folder( $root, 'Charlie' );
        // priorities: Charlie first, then Alpha, then Bravo
        foreach ( array( 'Charlie' => 1, 'Alpha' => 2, 'Bravo' => 3 ) as $name => $priority )
        {
            $node = eZContentObjectTreeNode::fetch( static::$tree[$name]['node'] );
            $node->setAttribute( 'priority', $priority );
            $node->store();
        }
    }

    protected static function article( $parentNodeID, $title )
    {
        return static::createObject( 'article', $parentNodeID, array( 'title' => $title, 'intro' => '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph>Intro of ' . $title . '</paragraph></section>' ) );
    }

    private function subTree( array $params, $nodeID = null )
    {
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params, $nodeID === null ? static::$root['node'] : $nodeID );
        return is_array( $nodes ) ? $nodes : array();
    }

    private function names( array $params, $nodeID = null )
    {
        return static::namesOf( $this->subTree( $params, $nodeID ) );
    }

    // ---------------------------------------------------------------- subtree

    public function testWholeSubtreeAndItsCount()
    {
        $names = $this->names( array( 'SortBy' => array( 'name', true ) ) );
        $this->assertSame( array( 'Alpha', 'Apple', 'Banana', 'Bravo', 'Charlie', 'Cherry', 'Inner' ), $names );
        $this->assertSame( 7, eZContentObjectTreeNode::subTreeCountByNodeID( array(), static::$root['node'] ) );
        $this->assertSame( 7, eZContentObjectTreeNode::fetch( static::$root['node'] )->subTreeCount() );
    }

    public static function depthProvider()
    {
        return array(
            'depth 1' => array( 1, 'le', array( 'Alpha', 'Bravo', 'Charlie' ) ),
            'depth 2 or less' => array( 2, 'le', array( 'Alpha', 'Apple', 'Banana', 'Bravo', 'Charlie', 'Inner' ) ),
            'exactly depth 2' => array( 2, 'eq', array( 'Apple', 'Banana', 'Inner' ) ),
            'below depth 2' => array( 2, 'lt', array( 'Alpha', 'Bravo', 'Charlie' ) ),
            'exactly depth 3' => array( 3, 'eq', array( 'Cherry' ) ),
            'deeper than 1' => array( 1, 'gt', array( 'Apple', 'Banana', 'Cherry', 'Inner' ) ),
            'at least depth 2' => array( 2, 'ge', array( 'Apple', 'Banana', 'Cherry', 'Inner' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('depthProvider')]
    public function testDepthAndDepthOperator( $depth, $operator, $expected )
    {
        $params = array( 'Depth' => $depth, 'DepthOperator' => $operator, 'SortBy' => array( 'name', true ) );
        $this->assertSame( $expected, $this->names( $params ) );
        $this->assertSame( count( $expected ), eZContentObjectTreeNode::subTreeCountByNodeID( $params, static::$root['node'] ) );
    }

    public static function classFilterProvider()
    {
        return array(
            'include articles' => array( 'include', array( 'article' ), array( 'Apple', 'Banana', 'Cherry' ) ),
            'exclude articles' => array( 'exclude', array( 'article' ), array( 'Alpha', 'Bravo', 'Charlie', 'Inner' ) ),
            'include both' => array( 'include', array( 'article', 'folder' ), array( 'Alpha', 'Apple', 'Banana', 'Bravo', 'Charlie', 'Cherry', 'Inner' ) ),
            'include by id' => array( 'include', 'folder-id', array( 'Alpha', 'Bravo', 'Charlie', 'Inner' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('classFilterProvider')]
    public function testClassFilter( $type, $classes, $expected )
    {
        if ( $classes === 'folder-id' )
            $classes = array( (int)eZContentClass::classIDByIdentifier( 'folder' ) );
        $params = array( 'ClassFilterType' => $type, 'ClassFilterArray' => $classes, 'SortBy' => array( 'name', true ) );
        $this->assertSame( $expected, $this->names( $params ) );
        $this->assertSame( count( $expected ), eZContentObjectTreeNode::subTreeCountByNodeID( $params, static::$root['node'] ) );
    }

    public function testClassFilterOfAnUnknownClassFindsNothing()
    {
        $this->assertSame( array(), $this->names( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'k1c_no_such_class' ) ) ) );
    }

    public function testSortingByNamePriorityAndClass()
    {
        $this->assertSame( array( 'Charlie', 'Bravo', 'Alpha' ), $this->names( array( 'Depth' => 1, 'SortBy' => array( 'name', false ) ) ) );
        $this->assertSame( array( 'Charlie', 'Alpha', 'Bravo' ), $this->names( array( 'Depth' => 1, 'SortBy' => array( 'priority', true ) ) ) );
        $this->assertSame( array( 'Bravo', 'Alpha', 'Charlie' ), $this->names( array( 'Depth' => 1, 'SortBy' => array( 'priority', false ) ) ) );
        $byDepth = $this->names( array( 'SortBy' => array( array( 'depth', false ), array( 'name', true ) ) ) );
        $this->assertSame( array( 'Cherry', 'Apple', 'Banana', 'Inner', 'Alpha', 'Bravo', 'Charlie' ), $byDepth );
        $byClass = $this->names( array( 'Depth' => 2, 'SortBy' => array( array( 'class_identifier', true ), array( 'name', false ) ) ) );
        $this->assertSame( array( 'Banana', 'Apple', 'Inner', 'Charlie', 'Bravo', 'Alpha' ), $byClass );
    }

    public function testSortingByAnAttribute()
    {
        $this->assertSame( array( 'Cherry', 'Banana', 'Apple' ),
                           $this->names( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'article' ), 'SortBy' => array( 'attribute', false, 'article/title' ) ) ) );
    }

    public function testLimitAndOffset()
    {
        $params = array( 'SortBy' => array( 'name', true ) );
        $this->assertSame( array( 'Alpha', 'Apple' ), $this->names( $params + array( 'Limit' => 2 ) ) );
        $this->assertSame( array( 'Banana', 'Bravo', 'Charlie' ), $this->names( $params + array( 'Limit' => 3, 'Offset' => 2 ) ) );
        $this->assertSame( array( 'Inner' ), $this->names( $params + array( 'Limit' => 5, 'Offset' => 6 ) ) );
        $this->assertSame( array(), $this->names( $params + array( 'Offset' => 7, 'Limit' => 5 ) ) );
    }

    public static function attributeFilterProvider()
    {
        return array(
            'object name' => array( array( array( 'name', '=', 'Bravo' ) ), array( 'Bravo' ) ),
            // text is compared with the sort key, which is lower case
            'title equals' => array( array( array( 'article/title', '=', 'banana' ) ), array( 'Banana' ) ),
            'title equals in other case' => array( array( array( 'article/title', '=', 'Banana' ) ), array() ),
            'title like' => array( array( array( 'article/title', 'like', 'B*' ) ), array( 'Banana' ) ),
            'title not like' => array( array( array( 'article/title', 'not_like', 'B*' ) ), array( 'Apple', 'Cherry' ) ),
            'title in' => array( array( array( 'article/title', 'in', array( 'apple', 'cherry' ) ) ), array( 'Apple', 'Cherry' ) ),
            'title greater' => array( array( array( 'article/title', '>', 'apple' ) ), array( 'Banana', 'Cherry' ) ),
            'or' => array( array( 'or', array( 'article/title', '=', 'apple' ), array( 'article/title', '=', 'cherry' ) ), array( 'Apple', 'Cherry' ) ),
            'and' => array( array( 'and', array( 'article/title', 'like', '*a*' ), array( 'article/title', '!=', 'banana' ) ), array( 'Apple' ) ),
            'depth as a filter' => array( array( array( 'depth', '=', static::rootDepthPlus( 3 ) ) ), array( 'Cherry' ) ),
        );
    }

    private static function rootDepthPlus( $levels )
    {
        // resolved in the test, the provider runs before the tree exists
        return 'root+' . $levels;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('attributeFilterProvider')]
    public function testAttributeFilter( $filter, $expected )
    {
        array_walk_recursive( $filter, function ( &$value )
        {
            if ( is_string( $value ) && strpos( $value, 'root+' ) === 0 )
                $value = (int)eZContentObjectTreeNode::fetch( static::$root['node'] )->attribute( 'depth' ) + (int)substr( $value, 5 );
        } );
        $params = array( 'AttributeFilter' => $filter, 'SortBy' => array( 'name', true ) );
        $this->assertSame( $expected, $this->names( $params ) );
        $this->assertSame( count( $expected ), eZContentObjectTreeNode::subTreeCountByNodeID( $params, static::$root['node'] ) );
    }

    public function testObjectNameFilter()
    {
        $this->assertSame( array( 'Banana', 'Bravo' ), $this->names( array( 'ObjectNameFilter' => 'b', 'SortBy' => array( 'name', true ) ) ) );
        $this->assertSame( array( 'Charlie', 'Cherry' ), $this->names( array( 'ObjectNameFilter' => 'c', 'SortBy' => array( 'name', true ) ) ) );
    }

    public function testMainNodeOnlyAndAsArrays()
    {
        $rows = eZContentObjectTreeNode::subTreeByNodeID( array( 'AsObject' => false, 'Depth' => 1, 'SortBy' => array( 'name', true ), 'MainNodeOnly' => true ), static::$root['node'] );
        $this->assertCount( 3, $rows );
        $this->assertIsArray( $rows[0] );
        $this->assertSame( 'Alpha', $rows[0]['name'] );
        $this->assertSame( static::$tree['Alpha']['node'], (int)$rows[0]['node_id'] );
    }

    public function testSubTreeOfSeveralPaths()
    {
        // only the parent node given: the other node parameters and the result id are optional
        $nodes = eZContentObjectTreeNode::subTreeMultiPaths( array( array( 'ParentNodeID' => static::$tree['Inner']['node'] ),
                                                                    array( 'ParentNodeID' => static::$tree['Bravo']['node'] ) ),
                                                             array( 'SortBy' => array( 'name', true ) ) );
        $this->assertSame( array( 'Cherry' ), static::namesOf( $nodes ) );

        $nodes = eZContentObjectTreeNode::subTreeMultiPaths( array( array( 'ParentNodeID' => static::$tree['Alpha']['node'], 'Depth' => 1, 'ResultID' => 7,
                                                                           'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'article' ), 'AttributeFilter' => false ),
                                                                    array( 'ParentNodeID' => static::$root['node'], 'Depth' => 1, 'ClassFilterType' => 'exclude',
                                                                           'ClassFilterArray' => array( 'article' ), 'AttributeFilter' => false ) ),
                                                             array( 'SortBy' => array( 'name', true ), 'Limit' => 4 ) );
        $this->assertSame( array( 'Alpha', 'Apple', 'Banana', 'Bravo' ), static::namesOf( $nodes ) );
        $this->assertNull( eZContentObjectTreeNode::subTreeMultiPaths( array() ) );
        $this->assertNull( eZContentObjectTreeNode::subTreeMultiPaths( array( array( 'ParentNodeID' => 'x' ) ) ) );

        // the result id goes into the SQL: only a number is taken
        $nodes = eZContentObjectTreeNode::subTreeMultiPaths( array( array( 'ParentNodeID' => static::$tree['Inner']['node'], 'ResultID' => '1 AS x, 2' ) ) );
        $this->assertSame( array( 'Cherry' ), static::namesOf( $nodes ) );
    }

    public function testFetchOfAnUnknownNodeIsNothing()
    {
        $this->assertNull( eZContentObjectTreeNode::fetch( 999999999 ) );
        $this->assertSame( array(), $this->subTree( array(), 999999999 ) );
    }

    // ---------------------------------------------------------------- children, parents, paths

    public function testChildrenAndCounts()
    {
        $alpha = eZContentObjectTreeNode::fetch( static::$tree['Alpha']['node'] );
        $this->assertSame( 3, $alpha->childrenCount() );
        $this->assertEqualsCanonicalizing( array( 'Apple', 'Banana', 'Inner' ), static::namesOf( $alpha->children() ) );
        $this->assertSame( 0, eZContentObjectTreeNode::fetch( static::$tree['Bravo']['node'] )->childrenCount() );
        $byName = $alpha->childrenByName( 'Banana' );
        $this->assertSame( array( static::$tree['Banana']['node'] ), static::nodeIDsOf( $byName ) );
        $this->assertSame( array(), $alpha->childrenByName( 'k1c nothing' ) );
    }

    public function testParentAndPath()
    {
        $cherry = eZContentObjectTreeNode::fetch( static::$tree['Cherry']['node'] );
        $this->assertSame( static::$tree['Inner']['node'], (int)$cherry->fetchParent()->attribute( 'node_id' ) );
        $this->assertSame( static::$tree['Inner']['node'], (int)eZContentObjectTreeNode::getParentNodeId( static::$tree['Cherry']['node'] ) );

        $path = $cherry->pathArray();
        $this->assertSame( array( static::$root['node'], static::$tree['Alpha']['node'], static::$tree['Inner']['node'], static::$tree['Cherry']['node'] ),
                           array_map( 'intval', array_slice( $path, -4 ) ) );
        $this->assertSame( 1, (int)$path[0] );

        $names = static::namesOf( $cherry->fetchPath() );
        $this->assertSame( array( 'Alpha', 'Inner' ), array_slice( $names, -2 ) );

        $this->assertStringEndsWith( '/' . static::$tree['Inner']['node'] . '/' . static::$tree['Cherry']['node'] . '/', $cherry->attribute( 'path_string' ) );
        $this->assertSame( (int)eZContentObjectTreeNode::fetch( static::$root['node'] )->attribute( 'depth' ) + 3, (int)$cherry->attribute( 'depth' ) );
        $this->assertStringEndsWith( 'alpha/inner/cherry', $cherry->attribute( 'path_identification_string' ) );
    }

    public function testNodesAndClassesAlongAPathString()
    {
        $cherry = eZContentObjectTreeNode::fetch( static::$tree['Cherry']['node'] );
        $withLast = eZContentObjectTreeNode::fetchNodesByPathString( $cherry->attribute( 'path_string' ), true );
        $this->assertSame( 'Cherry', $withLast[count( $withLast ) - 1]->attribute( 'name' ) );
        $withoutLast = eZContentObjectTreeNode::fetchNodesByPathString( $cherry->attribute( 'path_string' ), false );
        $this->assertCount( count( $withLast ) - 1, $withoutLast );
        $classes = eZContentObjectTreeNode::fetchClassIdentifierListByPathString( $cherry->attribute( 'path_string' ), true );
        $this->assertSame( array( 'folder', 'folder', 'article' ), array_slice( array_column( $classes, 'class_identifier' ), -3 ) );
    }

    public function testLookupsByObjectRemoteIdAndParent()
    {
        $banana = static::$tree['Banana'];
        $nodes = eZContentObjectTreeNode::fetchByContentObjectID( $banana['object'] );
        $this->assertSame( array( $banana['node'] ), static::nodeIDsOf( $nodes ) );
        $this->assertSame( $banana['node'], (int)eZContentObjectTreeNode::fetchNode( $banana['object'], static::$tree['Alpha']['node'] )->attribute( 'node_id' ) );
        $this->assertNull( eZContentObjectTreeNode::fetchNode( $banana['object'], static::$tree['Bravo']['node'] ) );
        $this->assertSame( $banana['node'], (int)eZContentObjectTreeNode::findMainNode( $banana['object'] ) );
        $this->assertSame( $banana['node'], (int)eZContentObjectTreeNode::findMainNode( $banana['object'], true )->attribute( 'node_id' ) );

        $node = eZContentObjectTreeNode::fetch( $banana['node'] );
        $byRemote = eZContentObjectTreeNode::fetchByRemoteID( $node->attribute( 'remote_id' ) );
        $this->assertSame( $banana['node'], (int)$byRemote->attribute( 'node_id' ) );
        $byPath = eZContentObjectTreeNode::fetchByPath( $node->attribute( 'path_string' ) );
        $this->assertSame( $banana['node'], (int)$byPath->attribute( 'node_id' ) );
        $this->assertTrue( $node->isMain() );
        $this->assertSame( 'article', $node->classIdentifier() );
        $this->assertSame( (bool)eZContentClass::fetchByIdentifier( 'article' )->attribute( 'is_container' ), (bool)$node->classIsContainer() );
        $this->assertSame( 'Banana', $node->getName() );
        $this->assertSame( 'Banana', $node->attribute( 'data_map' )['title']->attribute( 'content' ) );

        $mains = eZContentObjectTreeNode::findMainNodeArray( array( $banana['object'], static::$tree['Apple']['object'] ) );
        $this->assertEqualsCanonicalizing( array( $banana['node'], static::$tree['Apple']['node'] ), static::nodeIDsOf( $mains ) );

        $parents = eZContentObjectTreeNode::getParentNodeIdListByContentObjectID( array( $banana['object'], static::$tree['Cherry']['object'] ) );
        $this->assertEqualsCanonicalizing( array( static::$tree['Alpha']['node'], static::$tree['Inner']['node'] ), array_map( 'intval', $parents ) );
        $grouped = eZContentObjectTreeNode::getParentNodeIdListByContentObjectID( array( $banana['object'] ), true );
        $this->assertSame( array( static::$tree['Alpha']['node'] ), array_map( 'intval', $grouped[$banana['object']] ) );
    }

    // ---------------------------------------------------------------- no database needed

    public static function sortFieldProvider()
    {
        return array(
            array( 1, 'path' ), array( 2, 'published' ), array( 3, 'modified' ), array( 4, 'section' ), array( 5, 'depth' ),
            array( 6, 'class_identifier' ), array( 7, 'class_name' ), array( 8, 'priority' ), array( 9, 'name' ),
            array( 10, 'modified_subnode' ), array( 11, 'node_id' ), array( 12, 'contentobject_id' ), array( 13, 'is_invisible' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('sortFieldProvider')]
    public function testSortFieldNamesAndIds( $id, $name )
    {
        $this->assertSame( $name, eZContentObjectTreeNode::sortFieldName( $id ) );
        $this->assertSame( $id, eZContentObjectTreeNode::sortFieldID( $name ) );
        $this->assertSame( array( array( $name, 1 ) ), eZContentObjectTreeNode::sortArrayBySortFieldAndSortOrder( $id, 1 ) );
        $this->assertSame( array( array( $name, 0 ) ), eZContentObjectTreeNode::sortArrayBySortFieldAndSortOrder( $id, 0 ) );
    }

    public function testUnknownSortField()
    {
        $this->assertSame( 'path', eZContentObjectTreeNode::sortFieldName( 99 ) );
        $this->assertSame( 1, eZContentObjectTreeNode::sortFieldID( 'k1c-nothing' ) );
    }
}
