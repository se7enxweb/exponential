<?php
/**
 * The built-in search engine (eZSearchEngine) on throwaway objects with words made for the run: indexing on
 * publish, finding by one or more words (all of them must match), limits and offsets, the class and subtree
 * filters, wildcards, a phrase in quotes, an update that changes the indexed words, and removal from the index.
 * The text helpers (splitting, normalising, phrases) need no database and are checked as well.
 *
 * Skipped where another search engine (Solr, ...) is configured.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expContentModelLiveTestCase.php';

class eZSearchEngineLiveTest extends expContentModelLiveTestCase
{
    /** @var string a word nobody else has, made for the run */
    protected static $word;
    protected static $other;
    protected static $items = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( !eZSearch::getEngine() instanceof eZSearchEngine )
            self::markTestSkipped( 'the site uses another search engine' );
        if ( eZINI::instance()->variable( 'SearchSettings', 'DelayedIndexing' ) !== 'disabled' )
            self::markTestSkipped( 'objects are indexed later (DelayedIndexing)' );
        $letters = '';
        foreach ( str_split( substr( md5( uniqid( '', true ) ), 0, 8 ) ) as $c )
            $letters .= chr( ord( 'a' ) + hexdec( $c ) );
        static::$word = 'kqzc' . $letters;
        static::$other = 'kqzd' . strrev( $letters );
        $w = static::$word;
        $o = static::$other;
        static::$items = array();
        static::$items['both'] = static::folder( static::$root['node'], "Folder $w $o" );
        static::$items['word'] = static::folder( static::$root['node'], "Folder $w only" );
        static::$items['inside'] = static::folder( static::$items['both']['node'], "Inner $w" );
        static::$items['article'] = static::createObject( 'article', static::$root['node'], array( 'title' => "Article $w", 'intro' =>
            '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph>' . "$o first $w second" . '</paragraph></section>' ) );
    }

    private function search( $text, array $params = array() )
    {
        $result = eZSearch::search( $text, $params + array( 'SearchLimit' => 50 ) );
        $this->assertIsArray( $result );
        $ids = array();
        foreach ( $result['SearchResult'] as $node )
            $ids[] = (int)$node->attribute( 'contentobject_id' );
        sort( $ids );
        return array( $ids, (int)$result['SearchCount'] );
    }

    private function idsOf( array $names )
    {
        $ids = array();
        foreach ( $names as $name )
            $ids[] = static::$items[$name]['object'];
        sort( $ids );
        return $ids;
    }

    public function testOneWordFindsEveryObjectWithIt()
    {
        list( $ids, $count ) = $this->search( static::$word );
        $this->assertSame( $this->idsOf( array( 'both', 'word', 'inside', 'article' ) ), $ids );
        $this->assertSame( 4, $count );
        list( $ids, $count ) = $this->search( strtoupper( static::$word ) );
        $this->assertSame( 4, $count, 'case does not matter' );
    }

    public function testAllWordsMustMatch()
    {
        list( $ids ) = $this->search( static::$word . ' ' . static::$other );
        $this->assertSame( $this->idsOf( array( 'both', 'article' ) ), $ids );
        list( $ids, $count ) = $this->search( static::$word . ' kaznothingthere' );
        $this->assertSame( array(), $ids );
        $this->assertSame( 0, $count );
    }

    public function testLimitAndOffset()
    {
        $result = eZSearch::search( static::$word, array( 'SearchLimit' => 2, 'SearchOffset' => 0 ) );
        $this->assertCount( 2, $result['SearchResult'] );
        $this->assertSame( 4, (int)$result['SearchCount'], 'the count is of all matches' );
        $result = eZSearch::search( static::$word, array( 'SearchLimit' => 2, 'SearchOffset' => 3 ) );
        $this->assertCount( 1, $result['SearchResult'] );
    }

    public function testClassAndSubtreeFilters()
    {
        list( $ids ) = $this->search( static::$word, array( 'SearchContentClassID' => eZContentClass::classIDByIdentifier( 'article' ) ) );
        $this->assertSame( $this->idsOf( array( 'article' ) ), $ids );
        list( $ids ) = $this->search( static::$word, array( 'SearchContentClassID' => array( eZContentClass::classIDByIdentifier( 'folder' ) ) ) );
        $this->assertSame( $this->idsOf( array( 'both', 'word', 'inside' ) ), $ids );
        list( $ids ) = $this->search( static::$word, array( 'SearchSubTreeArray' => array( static::$items['both']['node'] ) ) );
        $this->assertSame( $this->idsOf( array( 'both', 'inside' ) ), $ids );
    }

    public function testWildcard()
    {
        if ( eZINI::instance()->variable( 'SearchSettings', 'EnableWildcard' ) !== 'true' )
            $this->markTestSkipped( 'wildcards are switched off' );
        list( $ids ) = $this->search( substr( static::$word, 0, 7 ) . '*' );
        $this->assertSame( $this->idsOf( array( 'both', 'word', 'inside', 'article' ) ), $ids );
    }

    public function testPhraseInQuotes()
    {
        // "<word> only" is the name of one folder; the article has both words, but not next to each other
        list( $ids ) = $this->search( '"' . static::$word . ' only"' );
        $this->assertSame( $this->idsOf( array( 'word' ) ), $ids );
        list( $ids ) = $this->search( '"' . static::$other . ' first"' );
        $this->assertSame( $this->idsOf( array( 'article' ) ), $ids );
        // the same words in another order are not the phrase
        list( $ids ) = $this->search( '"' . static::$word . ' folder"' );
        $this->assertSame( array(), $ids );
    }

    public function testUpdateChangesTheIndexAndRemovalTakesItOut()
    {
        $item = static::folder( static::$root['node'], 'Changing ' . static::$word . 'x' );
        list( $ids ) = $this->search( static::$word . 'x' );
        $this->assertSame( array( $item['object'] ), $ids );

        $this->assertTrue( eZContentFunctions::updateAndPublishObject( eZContentObject::fetch( $item['object'] ), array( 'attributes' => array( 'name' => 'Changed ' . static::$word . 'y' ) ) ) );
        list( $ids ) = $this->search( static::$word . 'x' );
        $this->assertSame( array(), $ids, 'the old word is gone' );
        list( $ids ) = $this->search( static::$word . 'y' );
        $this->assertSame( array( $item['object'] ), $ids );

        eZSearch::removeObjectById( $item['object'] );
        list( $ids ) = $this->search( static::$word . 'y' );
        $this->assertSame( array(), $ids );
    }

    public function testEmptySearch()
    {
        $result = eZSearch::search( '   ', array( 'AllowEmptySearch' => false ) );
        $this->assertSame( 0, (int)$result['SearchCount'] );
        $this->assertSame( array(), $result['SearchResult'] );
    }

    // ---------------------------------------------------------------- text helpers, no database

    public function testTextHelpers()
    {
        $engine = new eZSearchEngine();
        $this->assertSame( array( 'a', 'b', 'c' ), $engine->splitString( '  a  "b"   c ' ) );
        $this->assertSame( array(), $engine->splitString( '  " ' ) );
        $this->assertSame( 'hello world', $engine->normalizeText( "Hello   World" ) );
        $this->assertSame( 'a b', $engine->normalizeText( 'A*B', true ), 'a star in indexed text is no wildcard' );
        $this->assertSame( 'say "hi there"', $engine->normalizeText( 'Say "Hi there"' ), 'a phrase keeps its quotes in a search' );
        $this->assertSame( 'say "hi there"', $engine->normalizeText( "Say \u{201C}Hi there\u{201D}" ), 'typographic quotes are straight ones' );
        $this->assertSame( 'it s', $engine->normalizeText( "It's" ) );
        $this->assertSame( 'say hi there', $engine->normalizeText( 'Say "Hi there"', true ), 'indexed text loses every quote' );
        $phrases = $engine->getPhrases( 'one "two three" four "five"' );
        $this->assertSame( array( 'two three', 'five' ), $phrases['phrases'] );
        $this->assertSame( 'one  four ', $phrases['nonPhraseText'] );
        $odd = $engine->getPhrases( 'one "two' );
        $this->assertSame( array(), $odd['phrases'], 'an odd number of quotes is no phrase' );
        $types = $engine->supportedSearchTypes();
        $this->assertArrayHasKey( 'types', $types );
    }
}
