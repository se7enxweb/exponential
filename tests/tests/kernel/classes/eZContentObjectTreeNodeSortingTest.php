<?php
/**
 * The sort key "trashed" of eZContentObjectTreeNode::createSortingSQLStrings(): the trash list is sorted by the date
 * an object was moved to the trash, as the trash view and fetch( 'content', 'trash_object_list' ) pass it.
 *
 *  TS-01 - array( 'trashed', true ) sorts by <tree table>.trashed ascending (LoveStack's case)
 *  TS-02 - false and '0' sort descending; the short form array( 'trashed', '1' ) is one sort key
 *  TS-03 - the column is taken from the table the caller names (ezcontentobject_trash for the trash list)
 *  TS-04 - trashed combines with another key, in the given order
 *
 * Exponential has the trashed column and sorting by it (the same upstream change LoveStack carries); this brings
 * LoveStack's test of the sort string into the PHPUnit suite. No database: the trashed key is plain SQL text.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit e280ff5d64), Copyright (C) Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/eZContentObjectTreeNodeSortingTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZContentObjectTreeNodeSortingTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    private static function fields( $sortList, $table = 'ezcontentobject_tree' )
    {
        $result = eZContentObjectTreeNode::createSortingSQLStrings( $sortList, $table );
        return strtolower( trim( $result['sortingFields'] ) );
    }

    /** TS-01 */
    public function testSortingByTrashed()
    {
        $this->assertSame( 'ezcontentobject_tree.trashed asc', self::fields( array( array( 'trashed', true ) ) ) );
    }

    /** TS-02 */
    public function testDirectionAndShortForm()
    {
        $this->assertSame( 'ezcontentobject_tree.trashed desc', self::fields( array( array( 'trashed', false ) ) ) );
        $this->assertSame( 'ezcontentobject_tree.trashed desc', self::fields( array( 'trashed', '0' ) ) );
        $this->assertSame( 'ezcontentobject_tree.trashed asc', self::fields( array( 'trashed', '1' ) ) );
        $result = eZContentObjectTreeNode::createSortingSQLStrings( array( 'trashed', '1' ) );
        $this->assertSame( 1, $result['sortCount'] );
        $this->assertSame( 0, $result['attributeJoinCount'], 'no attribute join for the trashed key' );
    }

    /** TS-03 */
    public function testTheTableOfTheCaller()
    {
        $this->assertSame( 'ezcontentobject_trash.trashed asc', self::fields( array( array( 'trashed', true ) ), 'ezcontentobject_trash' ) );
    }

    /** TS-04 */
    public function testTogetherWithAnotherKey()
    {
        $this->assertSame( 'ezcontentobject_trash.trashed desc, ezcontentobject_trash.priority asc',
                           self::fields( array( array( 'trashed', false ), array( 'priority', true ) ), 'ezcontentobject_trash' ) );
    }
}
