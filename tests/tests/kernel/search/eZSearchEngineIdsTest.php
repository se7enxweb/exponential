<?php
/**
 * The ids the built-in search engine (eZSearchEngine, used without eZ Find) writes into its SQL are integers.
 *
 *  SI-01 - subTreeNodeIDList(): each subtree cast to an integer, those above 0, no doubles; "1e3", " 5" and the
 *          like never reach the SQL as they were sent, and an id that is not above 0 leaves no stray comma
 *  SI-02 - The class attribute and the subtrees go into the query as integers
 *
 * No database. Run: php vendor/bin/phpunit tests/tests/kernel/search/eZSearchEngineIdsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZSearchEngineIdsTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    /** SI-01 */
    public function testSubtreesAreIntegers()
    {
        $this->assertSame( array( 2, 43 ), eZSearchEngine::subTreeNodeIDList( array( '2', 43, '2' ) ) );
        $this->assertSame( array( 1000, 5 ), eZSearchEngine::subTreeNodeIDList( array( '1e3', ' 5', '0', '-4', 'x', array( 7 ), null ) ) );
        $this->assertSame( array( 12 ), eZSearchEngine::subTreeNodeIDList( '12' ) );
        $this->assertSame( array(), eZSearchEngine::subTreeNodeIDList( array( '0', '-1' ) ) );
        $this->assertSame( ' 2, 5', ' ' . implode( ', ', eZSearchEngine::subTreeNodeIDList( array( '2', '0', '5' ) ) ), 'no stray comma' );
    }

    /** SI-02 */
    public function testTheQueryTakesIntegers()
    {
        $code = (string)file_get_contents( 'kernel/search/plugins/ezsearchengine/ezsearchengine.php' );
        $this->assertStringContainsString( "\"ezsearch_object_word_link.contentclass_attribute_id = '\" . (int)\$searchContentClassAttributeID . \"' AND \"", $code );
        $this->assertStringNotContainsString( "contentclass_attribute_id = '\$searchContentClassAttributeID'", $code );
        $this->assertStringContainsString( '$subTreeNodeIDList = self::subTreeNodeIDList( $subTreeArray );', $code );
        $this->assertStringNotContainsString( '$subTreeNodeSQL .= " $nodeID";', $code );
    }
}
