<?php
/**
 * The logic behind the redesigned URL administration pages, without a database: url/list
 * (\Exponential\View\Kernel\Url\ListView), search/stats (\Exponential\View\Kernel\Search\Stats),
 * content/urltranslator (\Exponential\View\Kernel\Content\UrlaliasGlobal), content/urlwildcards
 * (\Exponential\View\Kernel\Content\UrlaliasWildcard) and the new listing parameters of eZURL.
 *
 *  UA-01 - Unknown list modes, orders and filters fall back to the defaults; the known ones are kept
 *  UA-02 - A search text is one trimmed line of at most 200 characters; anything else is no search
 *  UA-03 - A posted selection keeps whole positive numbers, each once
 *  UA-04 - The link check kind follows the scheme; only web, mail and site addresses become links
 *  UA-05 - The objects using each URL are counted per URL, each object once, the first three named
 *  UA-06 - LIKE patterns match %, _ and ! as themselves; eZURL orders end in the id
 *  UA-07 - The search statistics WHERE and ORDER BY, and the MongoDB match
 *  UA-08 - An alias destination is read as module view, node or other
 *  UA-09 - Wildcard placeholders without a * are found
 *  UA-10 - A tried address is translated by the first wildcard it matches, as the wildcard cache does
 *  UA-11 - The alias and wildcard searches and kinds are query conditions, with %, _ and ! matched as themselves
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Url\ListView;
use Exponential\View\Kernel\Search\Stats;
use Exponential\View\Kernel\Content\UrlaliasGlobal;
use Exponential\View\Kernel\Content\UrlaliasWildcard;

/** escapeString of a database, without one */
class X1UrlAdminDbStandIn
{
    public function escapeString( $text )
    {
        return str_replace( "'", "''", $text );
    }
}

class expUrlAdminPagesTest extends PHPUnit\Framework\TestCase
{
    /** UA-01 */
    public function testUnknownModesOrdersAndFiltersFallBack()
    {
        $this->assertSame( 'all', ListView::viewMode( 'nonsense' ) );
        $this->assertSame( 'all', ListView::viewMode( null ) );
        foreach ( array( 'all', 'valid', 'invalid', 'unchecked' ) as $mode )
            $this->assertSame( $mode, ListView::viewMode( $mode ) );
        $this->assertSame( 'address', ListView::sortKey( 'x' ) );
        $this->assertSame( 'checked', ListView::sortKey( 'checked' ) );
        $this->assertSame( 'count', Stats::sortKey( '' ) );
        $this->assertSame( 'fewest', Stats::sortKey( 'fewest' ) );
        $this->assertSame( 'all', Stats::showKey( 'some' ) );
        $this->assertSame( 'none', Stats::showKey( 'none' ) );
        $this->assertSame( 'all', UrlaliasGlobal::kindKey( 'other' ) );
        $this->assertSame( 'redirect', UrlaliasGlobal::kindKey( 'redirect' ) );
        $this->assertSame( array( 'is_valid' => null, 'last_checked' => 'never' ), ListView::listFilter( 'unchecked' ) );
        $this->assertSame( array( 'is_valid' => false ), ListView::listFilter( 'invalid' ) );
        $this->assertSame( array( 'is_valid' => null ), ListView::listFilter( 'all' ) );
    }

    /** UA-02 */
    public function testSearchTextIsOneTrimmedLine()
    {
        $this->assertSame( 'a b', ListView::searchText( "  a\nb  " ) );
        $this->assertSame( '', ListView::searchText( array( 'x' ) ) );
        $this->assertSame( '', ListView::searchText( "\xff\xfe" ) );
        $this->assertSame( 200, mb_strlen( ListView::searchText( str_repeat( 'ä', 300 ) ) ) );
    }

    /** UA-03 */
    public function testSelectionKeepsPositiveWholeNumbersOnce()
    {
        $this->assertSame( array( 3, 7 ), ListView::selectedIDs( array( '3', '7', '3', '0', '-1', '2x', array( 1 ), '1.5' ) ) );
        $this->assertSame( array(), ListView::selectedIDs( null ) );
        $this->assertSame( array( 5 ), ListView::selectedIDs( '5' ) );
    }

    /** UA-04 */
    public function testCheckKindAndOpenable()
    {
        $this->assertSame( 'http', ListView::checkKind( 'http://example.org/' ) );
        $this->assertSame( 'https', ListView::checkKind( 'HTTPS://example.org/' ) );
        $this->assertSame( 'mailto', ListView::checkKind( 'mailto:someone@example.org' ) );
        $this->assertSame( 'internal', ListView::checkKind( '/about' ) );
        $this->assertSame( 'content', ListView::checkKind( 'ezlocation://506' ) );
        $this->assertFalse( ListView::isOpenable( 'ezobject://12' ) );
        $this->assertSame( 'other', ListView::checkKind( 'javascript:alert(1)' ) );
        $this->assertTrue( ListView::isOpenable( 'https://example.org/' ) );
        $this->assertTrue( ListView::isOpenable( '/about' ) );
        $this->assertFalse( ListView::isOpenable( 'javascript:alert(1)' ) );
        $this->assertFalse( ListView::isOpenable( 'data:text/html,x' ) );
        $this->assertFalse( ListView::isOpenable( 'file:///etc/passwd' ) );
    }

    /** UA-05 */
    public function testUsageIsCountedPerUrl()
    {
        $rows = array( array( 'url_id' => 1, 'object_id' => 10, 'name' => 'A' ),
                       array( 'url_id' => 1, 'object_id' => 10, 'name' => 'A' ),
                       array( 'url_id' => 1, 'object_id' => 11, 'name' => 'B' ),
                       array( 'url_id' => 1, 'object_id' => 12, 'name' => 'C' ),
                       array( 'url_id' => 1, 'object_id' => 13, 'name' => 'D' ),
                       array( 'url_id' => 2, 'object_id' => 10, 'name' => 'A' ),
                       array( 'url_id' => 9, 'object_id' => 10, 'name' => 'A' ) );
        $usage = ListView::usage( array( 1, 2, 3 ), $rows, array( array( 'contentobject_id' => 10, 'main_node_id' => 100 ) ), 3 );
        $this->assertSame( array( 1, 2, 3 ), array_keys( $usage ) );
        $this->assertSame( 4, $usage[1]['count'] );
        $this->assertCount( 3, $usage[1]['objects'] );
        $this->assertSame( array( 'id' => 10, 'name' => 'A', 'node_id' => 100 ), $usage[1]['objects'][0] );
        $this->assertSame( 0, $usage[1]['objects'][1]['node_id'] );
        $this->assertSame( 1, $usage[2]['count'] );
        $this->assertSame( array( 'count' => 0, 'objects' => array() ), $usage[3] );
    }

    /** UA-06 */
    public function testLikePatternsAndOrders()
    {
        $this->assertSame( '%50!%!_off!!%', eZURL::searchLikePattern( '50%_off!' ) );
        $this->assertSame( '%a!_b%', Stats::likePattern( 'a_b' ) );
        $this->assertSame( ' ORDER BY ezurl.url ASC, ezurl.id ASC', eZURL::listOrderSQL( 'address' ) );
        $this->assertStringEndsWith( 'ezurl.id ASC', eZURL::listOrderSQL( 'checked' ) );
        $this->assertStringEndsWith( 'ezurl.id ASC', eZURL::listOrderSQL( 'modified' ) );
        $this->assertSame( '', eZURL::listOrderSQL( null ) );
        $this->assertSame( '', eZURL::listOrderSQL( 'url; DROP TABLE ezurl' ) );
    }

    /** UA-07 */
    public function testSearchStatsSql()
    {
        $db = new X1UrlAdminDbStandIn();
        $this->assertSame( '', Stats::whereSQL( $db, '', 'all' ) );
        $this->assertSame( ' WHERE result_count = 0', Stats::whereSQL( $db, '', 'none' ) );
        $this->assertSame( " WHERE LOWER( phrase ) LIKE LOWER( '%o''brien%' ) ESCAPE '!' AND result_count = 0", Stats::whereSQL( $db, "o'brien", 'none' ) );
        $this->assertSame( ' ORDER BY phrase_count DESC, id ASC', Stats::orderSQL( 'count' ) );
        $this->assertSame( ' ORDER BY phrase ASC, id ASC', Stats::orderSQL( 'phrase' ) );
        $this->assertStringStartsWith( ' ORDER BY result_count / phrase_count ASC', Stats::orderSQL( 'fewest' ) );
        $this->assertSame( array(), Stats::mongoMatch( '', 'all' ) );
        $this->assertSame( array( 'phrase' => array( '$regex' => 'a\.b', '$options' => 'i' ), 'result_count' => 0 ), Stats::mongoMatch( 'a.b', 'none' ) );
    }

    /** UA-08 */
    public function testAliasDestinationsAndFilter()
    {
        $exists = function ( $name ) { return $name === 'user'; };
        $module = UrlaliasGlobal::destinationOf( 'module:user/login', $exists );
        $this->assertSame( 'module', $module['kind'] );
        $this->assertSame( 'user/login', $module['url'] );
        $this->assertSame( 'login', $module['view'] );
        $this->assertTrue( $module['module_exists'] );
        $gone = UrlaliasGlobal::destinationOf( 'module:shopx/basket', $exists );
        $this->assertFalse( $gone['module_exists'] );
        $node = UrlaliasGlobal::destinationOf( 'eznode:42', $exists );
        $this->assertSame( array( 'node', 42, 'content/view/full/42' ), array( $node['kind'], $node['node_id'], $node['url'] ) );
        $this->assertSame( 'none', UrlaliasGlobal::destinationOf( 'nop:', $exists )['kind'] );
        $this->assertSame( 'other', UrlaliasGlobal::destinationOf( 'custom:x', $exists )['kind'] );
    }

    /** UA-09 */
    public function testUnknownPlaceholders()
    {
        $this->assertSame( array(), UrlaliasWildcard::unknownPlaceholders( 'developer/*', 'dev/{1}' ) );
        $this->assertSame( array( 2 ), UrlaliasWildcard::unknownPlaceholders( 'developer/*', 'dev/{1}/{2}' ) );
        $this->assertSame( array( 0, 3 ), UrlaliasWildcard::unknownPlaceholders( 'a/*/*', '{3}/{0}/{3}/{2}' ) );
        $this->assertSame( array(), UrlaliasWildcard::unknownPlaceholders( 'old', 'new' ) );
    }

    /** UA-10 */
    public function testFirstMatchTranslates()
    {
        $wildcards = array( array( 'id' => 1, 'source_url' => 'news/*', 'destination_url' => 'articles/{1}', 'type' => 2 ),
                            array( 'id' => 2, 'source_url' => 'news/*/*', 'destination_url' => 'never/{1}/{2}', 'type' => 1 ),
                            array( 'id' => 3, 'source_url' => 'a.b/*', 'destination_url' => 'x/{1}2', 'type' => 1 ) );
        $hit = UrlaliasWildcard::firstMatch( $wildcards, '/News/2026/october/' );
        $this->assertSame( 1, $hit['id'] );
        $this->assertSame( 'articles/2026/october', $hit['destination'] );
        $this->assertSame( 2, $hit['type'] );
        $this->assertSame( 'x/y2', UrlaliasWildcard::firstMatch( $wildcards, 'a.b/y' )['destination'] );
        $this->assertFalse( UrlaliasWildcard::firstMatch( $wildcards, 'axb/y' ) );
        $this->assertFalse( UrlaliasWildcard::firstMatch( $wildcards, 'other' ) );
        $this->assertSame( '#^news/(.*)#i', UrlaliasWildcard::patternRegexp( 'news/*' ) );
        $this->assertSame( 'dev/${1}', UrlaliasWildcard::destinationReplacement( 'dev/{1}' ) );
    }

    /** UA-11 */
    public function testAliasAndWildcardFiltersAreQueryConditions()
    {
        $db = new X1UrlAdminDbStandIn();
        $this->assertSame( array(), eZURLAliasQuery::filterConditionsSQL( $db, null, null ) );
        $this->assertSame( array( "( LOWER( text ) LIKE LOWER( '%50!%!_o''k%' ) ESCAPE '!' OR LOWER( action ) LIKE LOWER( '%50!%!_o''k%' ) ESCAPE '!' )", 'alias_redirects = 1' ),
                           eZURLAliasQuery::filterConditionsSQL( $db, "50%_o'k", true ) );
        $this->assertSame( '%a!_b!!%', eZURLAliasQuery::searchLikePattern( 'a_b!' ) );
        $this->assertSame( array( 'alias_redirects = 0' ), eZURLAliasQuery::filterConditionsSQL( $db, '', false ) );
        $this->assertSame( array( '$or' => array( array( 'text' => array( '$regex' => 'a\.b', '$options' => 'i' ) ),
                                                  array( 'action' => array( '$regex' => 'a\.b', '$options' => 'i' ) ) ),
                                  'alias_redirects' => 0 ),
                           eZURLAliasQuery::filterMongoMatch( 'a.b', false ) );

        $this->assertSame( '', eZURLWildcard::filterWhereSQL( $db, null, null ) );
        $this->assertSame( " WHERE ( LOWER( source_url ) LIKE LOWER( '%new!_s%' ) ESCAPE '!' OR LOWER( destination_url ) LIKE LOWER( '%new!_s%' ) ESCAPE '!' ) AND type = 1",
                           eZURLWildcard::filterWhereSQL( $db, 'new_s', eZURLWildcard::TYPE_FORWARD ) );
        $this->assertSame( array( 'type' => 2 ), eZURLWildcard::filterMongoMatch( '', eZURLWildcard::TYPE_DIRECT ) );
        $this->assertSame( eZURLWildcard::TYPE_FORWARD, UrlaliasWildcard::typeOfKind( 'redirect' ) );
        $this->assertSame( eZURLWildcard::TYPE_DIRECT, UrlaliasWildcard::typeOfKind( 'direct' ) );
        $this->assertNull( UrlaliasWildcard::typeOfKind( 'all' ) );
    }
}
