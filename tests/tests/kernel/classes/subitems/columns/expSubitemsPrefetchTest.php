<?php
/**
 * The page prefetch of the subitems columns (expSubitemsColumn::prefetch()): what the columns load
 * for a whole page in one query each must give exactly the values they load row by row, for the
 * admin and for a user with limited read access, and after it the rows need no query of their own.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/subitems/columns/expSubitemsPrefetchTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSubitemsColumnsTestCase.php';

class expSubitemsPrefetchTest extends expSubitemsColumnsTestCase
{
    /** The columns whose prefetch must leave the rows without a query of their own. */
    const NO_QUERY_COLUMNS = array( 'childrencount', 'childclasses', 'viewcount', 'versioncount', 'draftcount', 'versioncreated',
                                    'locationcount', 'otherlocations', 'aliascount', 'historycount', 'customaliascount',
                                    'relatedcount', 'reverserelatedcount', 'workflowprocesses', 'attributecount' );

    public function testPrefetchedValuesEqualTheRowByRowValuesForTheAdmin()
    {
        $this->assertPrefetchChangesNothing( (int)$this->contentRoot()->attribute( 'node_id' ) );
        $this->assertPrefetchChangesNothing( (int)$this->mediaRoot()->attribute( 'node_id' ) );
        $this->assertPrefetchChangesNothing( (int)$this->usersRoot()->attribute( 'node_id' ) );
    }

    public function testPrefetchedValuesEqualTheRowByRowValuesForAnonymous()
    {
        $anonymous = eZUser::fetch( (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        if ( !$anonymous instanceof eZUser )
            $this->markTestSkipped( 'no anonymous user' );
        eZUser::setCurrentlyLoggedInUser( $anonymous, $anonymous->attribute( 'contentobject_id' ) );
        try
        {
            $this->assertPrefetchChangesNothing( (int)$this->contentRoot()->attribute( 'node_id' ) );
        }
        finally
        {
            self::loginAdmin();
        }
    }

    public function testRowsNeedNoQueryAfterThePrefetch()
    {
        $parent = $this->contentRoot();
        $nodes = $this->page( (int)$parent->attribute( 'node_id' ) );
        $db = eZDB::instance();
        $output = $db->OutputSQL;
        foreach ( self::NO_QUERY_COLUMNS as $key )
        {
            $column = $this->column( $key );
            expSubitemsColumn::resetMemo();
            $column->prefetch( $nodes );
            $db->OutputSQL = true; // makes the driver count its queries in NumQueries
            $before = $db->NumQueries;
            foreach ( $nodes as $node )
                $column->value( $node );
            $queries = $db->NumQueries - $before;
            $db->OutputSQL = $output;
            $this->assertSame( 0, $queries, "column $key: no query per row after prefetch()" );
        }
    }

    public function testPrefetchIsHarmlessForNodesWithoutAnObject()
    {
        $node = new eZContentObjectTreeNode( array( 'node_id' => 0, 'contentobject_id' => 0 ) );
        foreach ( self::$registry->definitions() as $key => $settings )
        {
            if ( self::$registry->isBuiltin( $key ) )
                continue;
            $column = $this->column( $key );
            $column->prefetch( array() );
            $column->prefetch( array( $node ) );
        }
        $this->addToAssertionCount( 1 );
    }

    /**
     * The cells of every non-built-in column and of the parent's attribute columns, for a page of
     * nodes below $parentID: once row by row (fresh objects, empty memo), once through
     * columnValues(), which prefetches. Time-relative columns ("3 days ago") are left out.
     */
    protected function assertPrefetchChangesNothing( $parentID )
    {
        $parent = $this->node( $parentID );
        $columns = array();
        foreach ( self::$registry->definitions() as $key => $settings )
        {
            if ( self::$registry->isBuiltin( $key ) || preg_match( '/_age$/', (string)( $settings['Field'] ?? '' ) ) )
                continue;
            $columns[$key] = $this->column( $key );
        }
        $columns += self::$registry->attributeColumns( $parent );

        $rowByRow = array();
        foreach ( $this->page( $parentID ) as $node )
        {
            foreach ( $columns as $key => $column )
                $rowByRow[(int)$node->attribute( 'node_id' )][$key] = expSubitemsServerFunctions::cell( $column, $node );
        }

        $nodes = $this->page( $parentID );
        $cells = expSubitemsServerFunctions::columnValues( $nodes, $columns );
        $prefetched = array();
        foreach ( $nodes as $i => $node )
            $prefetched[(int)$node->attribute( 'node_id' )] = (array)$cells[$i];

        $this->assertNotEmpty( $rowByRow, "node $parentID has nodes below it" );
        $this->assertSame( $rowByRow, $prefetched, "below node $parentID the prefetched cells are the row by row cells" );
    }

    /** Up to 60 nodes below $parentID (all depths), freshly loaded, with an empty memo. */
    protected function page( $parentID )
    {
        expSubitemsColumn::resetMemo();
        eZContentObject::clearCache();
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( array( 'Limit' => 60, 'SortBy' => array( array( 'node_id', true ) ) ), $parentID );
        return is_array( $nodes ) ? array_values( $nodes ) : array();
    }
}
