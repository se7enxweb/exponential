<?php
/**
 * The ids the ezjscore search passes to the search engine are positive integers: subtrees, sections, classes, class
 * attributes and the time limit, whether posted as a list or as a comma separated text; and the offset and the
 * number of results are never below 0 and 1. The posted values are set in-process and put back afterwards. No
 * database, no search engine: a search without a text returns before searching.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscServerFunctionsJsSearchIDsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezjscServerFunctionsJsSearchIDsTest extends PHPUnit\Framework\TestCase
{
    private $post;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->post = $_POST;
    }

    protected function tearDown(): void
    {
        $_POST = $this->post;
    }

    private function idArray( $key )
    {
        $method = new ReflectionMethod( 'ezjscServerFunctionsJs', 'makePostIDArray' );
        return $method->invoke( null, eZHTTPTool::instance(), $key );
    }

    public function testIdsArePositiveIntegers()
    {
        $_POST['SearchSubTreeArray'] = array( '2', '43', '-5', '0', 'abc', '7 OR other', array( 9 ) );
        $this->assertSame( array( 2, 43, 7 ), $this->idArray( 'SearchSubTreeArray' ) );

        $_POST['SearchSectionID'] = '1,3,x';
        $this->assertSame( array( 1, 3 ), $this->idArray( 'SearchSectionID' ) );

        $_POST['SearchContentClassAttributeID'] = '12';
        $this->assertSame( array( 12 ), $this->idArray( 'SearchContentClassAttributeID' ) );

        $_POST['SearchContentClassID'] = '';
        $this->assertSame( array(), $this->idArray( 'SearchContentClassID' ) );

        unset( $_POST['SearchTimestamp'] );
        $this->assertSame( array(), $this->idArray( 'SearchTimestamp' ) );
    }

    public function testOffsetAndLimitAreNeverBelowTheirMinimum()
    {
        $_POST = array( 'SearchStr' => '', 'SearchOffset' => '-10', 'SearchLimit' => '-5' );
        $result = ezjscServerFunctionsJs::search( array() );
        $this->assertSame( 0, $result['SearchOffset'] );
        $this->assertSame( 10, $result['SearchLimit'] );
        $this->assertSame( 0, $result['SearchCount'] );
    }

    public function testATextThatIsNoTextIsNoSearch()
    {
        $_POST = array( 'SearchStr' => array( 'a', 'b' ) );
        $result = ezjscServerFunctionsJs::search( array() );
        $this->assertSame( '', $result['SearchString'] );
        $this->assertSame( array(), $result['SearchResult'] );
    }

    public function testEveryIdParameterGoesThroughTheIdFilter()
    {
        $code = (string)file_get_contents( 'extension/ezjscore/classes/ezjscserverfunctionsjs.php' );
        foreach ( array( 'SearchContentClassAttributeID', 'SearchSubTreeArray', 'SearchSectionID', 'SearchTimestamp' ) as $key )
        {
            $this->assertStringContainsString( "self::makePostIDArray( \$http, '$key' )", $code, $key );
            $this->assertStringNotContainsString( "self::makePostArray( \$http, '$key' )", $code, $key );
        }
        // classes: ids or identifiers, both ending as integers
        foreach ( array( 'SearchContentClassID', 'SearchContentClassIdentifier' ) as $key )
        {
            $this->assertStringContainsString( "self::makePostClassIDArray( \$http, '$key' )", $code, $key );
            $this->assertStringNotContainsString( "self::makePostArray( \$http, '$key' )", $code, $key );
        }
    }

    public function testClassIdentifiersAreLookedUpToTheirIds()
    {
        $method = new ReflectionMethod( 'ezjscServerFunctionsJsSearchIDsTestClasses', 'makePostClassIDArray' );
        $_POST['SearchContentClassID'] = array( '2', 'article', 'folder', 'unknown', '-3', '1e5', ' 4 ', 'a b', '7 OR other', array( 9 ), '2' );
        $this->assertSame( array( 2, 4, 16, 1 ), $method->invoke( null, eZHTTPTool::instance(), 'SearchContentClassID' ) );
        $_POST['SearchContentClassIdentifier'] = 'folder,article';
        $this->assertSame( array( 1, 16 ), $method->invoke( null, eZHTTPTool::instance(), 'SearchContentClassIdentifier' ) );
    }
}

/** Class lookups without a database: article is 16, folder 1, a lookup that answers a non-id is ignored */
class ezjscServerFunctionsJsSearchIDsTestClasses extends ezjscServerFunctionsJs
{
    protected static function classIDsByIdentifiers( array $identifiers )
    {
        $map = array( 'article' => 16, 'folder' => 1, 'unknown' => '1 OR 1' );
        $ids = array();
        foreach ( $identifiers as $identifier )
            if ( isset( $map[$identifier] ) )
                $ids[] = $map[$identifier];
        return $ids;
    }
}
