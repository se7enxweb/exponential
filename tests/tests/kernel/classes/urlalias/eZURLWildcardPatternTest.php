<?php
/**
 * Tests of how eZURLWildcard turns a wildcard into the patterns of its cache, without the database: the source
 * URL becomes a case insensitive regular expression with one group per "*" (special characters taken literally),
 * the destination's {1}, {2} refer to those groups, and the two together translate a request URL the way
 * translateWithCache() does.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZURLWildcardPatternTestProbe extends eZURLWildcard
{
    public static function regexp( $wildcard )
    {
        return self::matchRegexpCode( $wildcard );
    }

    public static function replace( $wildcard )
    {
        return self::matchReplaceCode( $wildcard );
    }
}

class eZURLWildcardPatternTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    private function translate( $source, $destination, $url )
    {
        $wildcard = array( 'source_url' => $source, 'destination_url' => $destination, 'type' => eZURLWildcard::TYPE_DIRECT );
        $regexp = eZURLWildcardPatternTestProbe::regexp( $wildcard );
        if ( !preg_match( $regexp, $url ) )
            return false;
        $replace = eZURLWildcardPatternTestProbe::replace( $wildcard );
        return preg_replace( $regexp, $replace['uri'], $url );
    }

    public function testSourceBecomesARegexpWithOneGroupPerStar()
    {
        $this->assertSame( '#^news/(.*)#i', eZURLWildcardPatternTestProbe::regexp( array( 'source_url' => 'news/*' ) ) );
        $this->assertSame( '#^a/(.*)/b/(.*)#i', eZURLWildcardPatternTestProbe::regexp( array( 'source_url' => 'a/*/b/*' ) ) );
        $this->assertSame( '#^#i', eZURLWildcardPatternTestProbe::regexp( array() ) );
    }

    public function testSpecialCharactersOfTheSourceAreLiteral()
    {
        $regexp = eZURLWildcardPatternTestProbe::regexp( array( 'source_url' => 'c++/(x)#.?/*' ) );
        $this->assertSame( 1, preg_match( $regexp, 'c++/(x)#.?/tail' ) );
        $this->assertSame( 0, preg_match( $regexp, 'cc/x/tail' ) );
    }

    public function testDestinationRefersToTheGroups()
    {
        $replace = eZURLWildcardPatternTestProbe::replace( array( 'destination_url' => 'archive/{2}/{1}', 'type' => 1 ) );
        $this->assertSame( array( 'destination_url' => 'archive/{2}/{1}', 'type' => 1 ), $replace['info'] );
        $this->assertSame( '', eZURLWildcardPatternTestProbe::replace( array() )['uri'] );
    }

    public static function translateProvider()
    {
        return array(
            'one star' => array( 'news/*', 'content/view/full/{1}', 'news/42', 'content/view/full/42' ),
            'deeper path in the star' => array( 'old/*', 'new/{1}', 'old/a/b/c', 'new/a/b/c' ),
            'two stars swapped' => array( 'a/*/b/*', 'x/{2}/{1}', 'a/one/b/two', 'x/two/one' ),
            'case insensitive' => array( 'News/*', 'n/{1}', 'NEWS/Item', 'n/Item' ),
            'no match' => array( 'news/*', 'n/{1}', 'blog/news/1', false ),
            'star may be empty' => array( 'news/*', 'n/{1}', 'news/', 'n/' ),
            'unused group' => array( 'shop/*', 'products', 'shop/anything', 'products' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('translateProvider')]
    public function testTranslate( $source, $destination, $url, $expected )
    {
        $this->assertSame( $expected, $this->translate( $source, $destination, $url ) );
    }

    public function testAsArray()
    {
        $wildcard = new eZURLWildcard( array( 'id' => 7, 'source_url' => 'a/*', 'destination_url' => 'b/{1}', 'type' => eZURLWildcard::TYPE_FORWARD ) );
        $this->assertSame( array( 'id' => 7, 'source_url' => 'a/*', 'destination_url' => 'b/{1}', 'type' => 1 ), $wildcard->asArray() );
    }
}
