<?php
/**
 * The filters of the administration's search page (expContentSearchFilters), without a database: request checking,
 * the mapping of the former advanced search, the parameters per back end, the query strings and the chips.
 *
 *  SF-01 - Every value is checked against the catalogue: unknown classes, sections, languages and owners are dropped
 *  SF-02 - The parameters of content/advancedsearch map to the new names
 *  SF-03 - eZ Find gets only parameters of the eZ Find package, the extended filters only when registered
 *  SF-04 - The built-in engine gets fetch( content, search ) parameters and is not offered what it cannot do
 *  SF-05 - Dates: periods from the start of the day, custom ranges inclusive, invalid dates rejected
 *  SF-06 - Query strings: Name[]=value; the chips and the page as view parameters, each chip without exactly its
 *          value, defaults not written
 *  SF-14 - View parameters: viewSuffix() and requestFromViewParameters() give the same search back; a query string
 *          is never read
 *  SF-07 - Search modes: all, any (OR) and the phrase
 *  SF-08 - The page size and the sort order
 *  SF-09 - Facet counts and the class groups with "Show more"
 *  SF-10 - fetch() runs no search without words or filters, and the right back end otherwise
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expContentSearchFiltersTest extends PHPUnit\Framework\TestCase
{
    /** 2026-11-02 12:00:00 local time */
    private static function now()
    {
        return mktime( 12, 0, 0, 11, 2, 2026 );
    }

    private static function catalog( array $extra = array() )
    {
        return $extra + array(
            'classes' => array( 1 => array( 'identifier' => 'folder', 'name' => 'Folder', 'groups' => array( 1 ) ),
                                2 => array( 'identifier' => 'article', 'name' => 'Article', 'groups' => array( 1 ) ),
                                4 => array( 'identifier' => 'user', 'name' => 'User', 'groups' => array( 2 ) ) ),
            'class_groups' => array( 1 => 'Content', 2 => 'Users' ),
            'sections' => array( 1 => 'Standard', 3 => 'Media' ),
            'languages' => array( 'eng-GB' => 'English', 'ger-DE' => 'Deutsch' ),
            'extended_filters' => array( 'ezfindallwords', 'ezfindcollapse' ),
            'maximum_limit' => 30,
            'class_attributes' => function ( $classID ) { return $classID === 2 ? array( 10 => 'Title', 11 => 'Body' ) : array(); },
            'owner' => function ( $input ) { return in_array( $input, array( '14', 'admin' ), true ) ? array( 'id' => 14, 'name' => 'Administrator User' ) : null; },
            'node' => function ( $nodeID ) { return $nodeID === 2 ? array( 'name' => 'Home', 'path' => 'Home' ) : null; } );
    }

    private static function filters( array $request, $engine = expContentSearchFilters::ENGINE_EZFIND, array $catalog = array() )
    {
        return new expContentSearchFilters( $request, $engine, self::catalog( $catalog ), self::now() );
    }

    /** SF-01 */
    public function testValuesAreCheckedAgainstTheCatalogue()
    {
        $p = self::filters( array( 'SearchText' => 'x', 'ClassID' => array( '2', 'folder', '99', 'nope', '2', array( 1 ) ),
                                   'SectionID' => array( '3', '7', '-1', '1 OR 2' ), 'Language' => 'fre-FR',
                                   'SubTreeArray' => array( '2', 'x', '0', '5,6' ), 'Owner' => 'nobody' ) )->parameters();
        $this->assertSame( array( 2, 1 ), $p['class_ids'] );
        $this->assertSame( array( 3 ), $p['section_ids'] );
        $this->assertSame( '', $p['language'] );
        $this->assertSame( array( 2, 5, 6 ), $p['subtree_ids'] );
        $this->assertSame( 0, $p['owner']['id'] );
        $this->assertSame( array( 'Owner' ), self::filters( array( 'Owner' => 'nobody' ) )->rejected() );
        $this->assertSame( array(), self::filters( array( 'SubTreeArray' => '1' ) )->parameters()['subtree_ids'], 'the root is all content' );
        $this->assertSame( 'ger-DE', self::filters( array( 'Language' => 'ger-DE' ) )->parameters()['language'] );
        $this->assertSame( 14, self::filters( array( 'Owner' => 'admin' ) )->parameters()['owner']['id'] );
    }

    /** SF-01 */
    public function testTheClassAttributeNeedsExactlyOneClass()
    {
        $this->assertSame( 10, self::filters( array( 'ClassID' => '2', 'ClassAttributeID' => '10' ) )->parameters()['class_attribute_id'] );
        $this->assertSame( 0, self::filters( array( 'ClassID' => '2', 'ClassAttributeID' => '12' ) )->parameters()['class_attribute_id'], 'not of this class' );
        $this->assertSame( 0, self::filters( array( 'ClassID' => array( '1', '2' ), 'ClassAttributeID' => '10' ) )->parameters()['class_attribute_id'] );
        $this->assertSame( array( 10 => 'Title', 11 => 'Body' ), self::filters( array( 'ClassID' => '2' ) )->classAttributes() );
        $this->assertSame( array(), self::filters( array() )->classAttributes() );
    }

    /** SF-02 */
    public function testTheAdvancedSearchParametersMapToTheNewNames()
    {
        $map = expContentSearchFilters::mapAdvancedSearch( array( 'SearchText' => 'b c', 'PhraseSearchText' => 'a "x"',
                                                                  'SearchContentClassID' => '2', 'SearchContentClassAttributeID' => '10',
                                                                  'SearchSectionID' => '3', 'SearchDate' => '2', 'SubTreeArray' => array( '2' ),
                                                                  'SearchPageLimit' => '3' ), self::now() );
        $this->assertSame( array( 'SearchText' => '"a  x" b c', 'SubTreeArray' => array( '2' ), 'SearchPageLimit' => '3',
                                  'ClassID' => '2', 'ClassAttributeID' => '10', 'SectionID' => '3', 'Published' => 'week' ), $map );

        $map = expContentSearchFilters::mapAdvancedSearch( array( 'PhraseSearchText' => 'one two', 'SearchText' => '' ), self::now() );
        $this->assertSame( 'one two', $map['SearchText'] );
        $this->assertSame( 'phrase', $map['SearchMode'] );

        $map = expContentSearchFilters::mapAdvancedSearch( array( 'SearchDate' => '4' ), self::now() );
        $this->assertSame( array( 'Published' => 'custom', 'PublishedFrom' => '2026-08-02' ), $map );
        $map = expContentSearchFilters::mapAdvancedSearch( array( 'SearchTimestamp' => (string)mktime( 8, 0, 0, 1, 15, 2026 ) ), self::now() );
        $this->assertSame( array( 'Published' => 'custom', 'PublishedFrom' => '2026-01-15' ), $map );
        $map = expContentSearchFilters::mapAdvancedSearch( array( 'SearchDate' => '-1', 'SearchSectionID' => '-1' ), self::now() );
        $this->assertSame( array( 'SectionID' => '-1' ), $map, 'any time is no filter; an invalid section is dropped later' );
        // a request with the new name keeps it
        $map = expContentSearchFilters::mapAdvancedSearch( array( 'ClassID' => array( '1' ), 'SearchContentClassID' => '2' ), self::now() );
        $this->assertSame( array( 'ClassID' => array( '1' ) ), $map );

        $p = self::filters( array( 'SearchText' => 'x', 'SearchContentClassID' => 'article', 'SearchSectionID' => '1', 'SearchDate' => '5' ) )->parameters();
        $this->assertSame( array( 2 ), $p['class_ids'] );
        $this->assertSame( array( 1 ), $p['section_ids'] );
        $this->assertSame( 'year', $p['published']['period'] );
    }

    /** SF-03 */
    public function testEzFindGetsThePackagesParameters()
    {
        $filters = self::filters( array( 'SearchText' => 'solr', 'ClassID' => array( '2' ), 'ClassAttributeID' => '10', 'SectionID' => '1',
                                         'SubTreeArray' => array( '2' ), 'Modified' => 'custom', 'ModifiedFrom' => '2026-10-01', 'ModifiedTo' => '2026-10-31',
                                         'Published' => 'week', 'Owner' => '14', 'Language' => 'ger-DE', 'SortBy' => 'name_asc' ) );
        $params = $filters->ezfindParameters( 20 );
        $upstream = array( 'query', 'offset', 'limit', 'facet', 'filter', 'sort_by', 'class_id', 'section_id', 'subtree_array',
                           'spell_check', 'extended_attribute_filter', 'class_attribute_id' );
        $this->assertSame( array(), array_diff( array_keys( $params ), $upstream ), 'only parameters of fetch( ezfind, search ) of se7enxweb/ezfind 3.0' );
        $this->assertSame( 'solr', $params['query'] );
        $this->assertSame( 20, $params['offset'] );
        $this->assertSame( array( 2 ), $params['class_id'] );
        $this->assertSame( 10, $params['class_attribute_id'] );
        $this->assertSame( array( 1 ), $params['section_id'] );
        $this->assertSame( array( 2 ), $params['subtree_array'] );
        $this->assertSame( array( 'name' => 'asc' ), $params['sort_by'] );
        $this->assertSame( array( 'modified:[' . gmdate( 'Y-m-d\TH:i:s\Z', mktime( 0, 0, 0, 10, 1, 2026 ) ) . ' TO ' . gmdate( 'Y-m-d\TH:i:s\Z', mktime( 23, 59, 59, 10, 31, 2026 ) ) . ']',
                                  'published:[' . gmdate( 'Y-m-d\TH:i:s\Z', mktime( 0, 0, 0, 10, 26, 2026 ) ) . ' TO *]',
                                  'owner_id:14', 'language_code:ger-DE' ), $params['filter'] );
        $this->assertSame( array( 'class_identifier', 'section_id', 'translation' ), array_column( $params['facet'], 'field' ) );
        $this->assertSame( array( 'ezfindcollapse', 'ezfindallwords' ), array_column( $params['extended_attribute_filter'], 'id' ) );

        // the package without our filters: none asked for; "all words", the default, passes the text on unchanged
        $plain = self::filters( array( 'SearchText' => 'solr NOT lucene' ), expContentSearchFilters::ENGINE_EZFIND, array( 'extended_filters' => array() ) );
        $this->assertArrayNotHasKey( 'extended_attribute_filter', $plain->ezfindParameters( 0 ) );
        $this->assertSame( array( 'all', 'any', 'phrase' ), $plain->capabilities()['modes'] );
        $this->assertSame( 'solr NOT lucene', $plain->ezfindParameters( 0 )['query'] );
        $this->assertFalse( $plain->capabilities()['collapse'] );
        // with only ezfindallwords (the package 3.0.15)
        $allWords = self::filters( array( 'SearchText' => 'solr' ), expContentSearchFilters::ENGINE_EZFIND, array( 'extended_filters' => array( 'ezfindallwords' ) ) );
        $this->assertSame( array( array( 'id' => 'ezfindallwords', 'params' => array() ) ), $allWords->ezfindParameters( 0 )['extended_attribute_filter'] );

        $minimal = self::filters( array( 'SearchText' => 'solr' ) )->ezfindParameters( -5 );
        $this->assertSame( 0, $minimal['offset'] );
        $this->assertSame( array( 'score' => 'desc' ), $minimal['sort_by'], 'relevance by default' );
        foreach ( array( 'filter', 'class_id', 'section_id', 'subtree_array', 'class_attribute_id' ) as $name )
            $this->assertArrayNotHasKey( $name, $minimal, $name );
    }

    /** SF-04 */
    public function testTheBuiltInEngineGetsWhatItCanDo()
    {
        $kernel = expContentSearchFilters::ENGINE_KERNEL;
        $filters = self::filters( array( 'SearchText' => 'solr', 'SearchMode' => 'any', 'ClassID' => array( '2', '1' ), 'SectionID' => '1',
                                         'Modified' => 'week', 'Owner' => '14', 'Language' => 'ger-DE', 'SortBy' => 'relevance',
                                         'Published' => 'custom', 'PublishedFrom' => '2026-01-01', 'PublishedTo' => '2026-01-31' ), $kernel );
        $capabilities = $filters->capabilities();
        foreach ( array( 'modified', 'owner', 'language', 'facets', 'empty_search' ) as $name )
            $this->assertFalse( $capabilities[$name], $name );
        $this->assertSame( array( 'all', 'phrase' ), $capabilities['modes'] );
        $this->assertNotContains( 'relevance', $capabilities['sorts'] );

        $p = $filters->parameters();
        $this->assertSame( 'all', $p['mode'], 'any is not offered: the default' );
        $this->assertSame( '', $p['modified']['period'] );
        $this->assertSame( 0, $p['owner']['id'] );
        $this->assertSame( '', $p['language'] );
        $this->assertSame( 'modified_desc', $p['sort'] );

        $params = $filters->kernelParameters( 10 );
        $this->assertSame( array( 'text', 'offset', 'limit', 'sort_by', 'class_id', 'section_id', 'publish_timestamp' ), array_keys( $params ) );
        $this->assertSame( array( array( 'modified', false ) ), $params['sort_by'] );
        $this->assertSame( array( 2, 1 ), $params['class_id'] );
        $this->assertSame( 1, $params['section_id'] );
        $this->assertSame( array( mktime( 0, 0, 0, 1, 1, 2026 ), mktime( 23, 59, 59, 1, 31, 2026 ) ), $params['publish_timestamp'] );
        $this->assertSame( mktime( 0, 0, 0, 10, 26, 2026 ), self::filters( array( 'Published' => 'week' ), $kernel )->kernelParameters( 0 )['publish_timestamp'] );
        $this->assertSame( array( array( 'name', true ) ), self::filters( array( 'SortBy' => 'name_asc' ), $kernel )->kernelParameters( 0 )['sort_by'] );

        // no words: only with AllowEmptySearch
        $this->assertFalse( self::filters( array( 'ClassID' => '2' ), $kernel )->isSearchRequested() );
        $this->assertTrue( self::filters( array( 'ClassID' => '2' ), $kernel, array( 'allow_empty_search' => true ) )->isSearchRequested() );
    }

    /** SF-05 */
    public function testDates()
    {
        $p = self::filters( array( 'Modified' => 'day', 'Published' => 'year' ) )->parameters();
        $this->assertSame( mktime( 0, 0, 0, 11, 1, 2026 ), $p['modified']['from_time'] );
        $this->assertNull( $p['modified']['to_time'] );
        $this->assertSame( '2025-11-02', $p['published']['from'] );

        $p = self::filters( array( 'Modified' => 'custom', 'ModifiedFrom' => '2026-02-30', 'ModifiedTo' => '2026-03-01' ) );
        $this->assertSame( array( 'ModifiedFrom' ), $p->rejected() );
        $this->assertSame( array( 'period' => 'custom', 'from' => '', 'to' => '2026-03-01', 'from_time' => null, 'to_time' => mktime( 23, 59, 59, 3, 1, 2026 ) ), $p->parameters()['modified'] );

        $p = self::filters( array( 'Modified' => 'custom', 'ModifiedFrom' => '2026-05-10', 'ModifiedTo' => '2026-05-01' ) )->parameters()['modified'];
        $this->assertSame( array( '2026-05-01', '2026-05-10' ), array( $p['from'], $p['to'] ), 'swapped' );

        $this->assertSame( '', self::filters( array( 'Modified' => 'custom' ) )->parameters()['modified']['period'], 'custom without a date is no filter' );
        $this->assertSame( '', self::filters( array( 'Modified' => 'decade' ) )->parameters()['modified']['period'] );
    }

    /** SF-06 */
    public function testQueryStringsAndChips()
    {
        $filters = self::filters( array( 'SearchText' => 'a&b', 'ClassID' => array( '2', '1' ), 'SectionID' => '1', 'SubTreeArray' => array( '2' ),
                                         'Published' => 'custom', 'PublishedFrom' => '2026-01-01', 'Language' => 'ger-DE', 'Owner' => 'admin',
                                         'SortBy' => 'relevance', 'SearchPageLimit' => '2' ) );
        $this->assertSame( 'SearchText=a%26b&ClassID%5B%5D=2&ClassID%5B%5D=1&SectionID%5B%5D=1&Published=custom&PublishedFrom=2026-01-01&Owner=admin&Language=ger-DE&SubTreeArray%5B%5D=2',
                           $filters->queryString(), 'no default sort and page size' );
        $chips = $filters->activeFilters();
        $this->assertSame( array( 'class', 'class', 'section', 'published', 'owner', 'language', 'subtree' ), array_column( $chips, 'type' ) );
        $this->assertSame( array( 'Article', 'Folder', 'Standard', '≥ 2026-01-01', 'Administrator User', 'Deutsch', 'Home' ), array_column( $chips, 'label' ) );
        $this->assertSame( '/(text)/a%26b/(class)/1/(section)/1/(published)/custom/(published_from)/2026-01-01/(owner)/admin/(language)/ger-DE/(subtree)/2', $chips[0]['query'], 'a chip is the search without its value, as view parameters' );
        $this->assertStringNotContainsString( '(published', $chips[3]['query'] );
        foreach ( $chips as $chip )
            $this->assertStringNotContainsString( 'offset', $chip['query'] );

        // the class attribute goes with its class
        $one = self::filters( array( 'ClassID' => '2', 'ClassAttributeID' => '11' ) );
        $this->assertSame( 'ClassID%5B%5D=2&ClassAttributeID=11', $one->queryString() );
        $this->assertSame( '', $one->queryString( array( 'ClassID' => 2 ) ) );
        $this->assertSame( 'ClassID%5B%5D=2&ClassAttributeID=11&SortBy=name_desc', $one->queryString( array(), array( 'SortBy' => 'name_desc' ) ) );
        $this->assertSame( array( 'class', 'class_attribute' ), array_column( $one->activeFilters(), 'type' ) );
        $this->assertSame( 'Body', $one->activeFilters()[1]['label'] );
    }

    /** SF-07 */
    public function testSearchModes()
    {
        $this->assertSame( 'red car', self::filters( array( 'SearchText' => 'red car' ) )->queryText() );
        $this->assertSame( 'all', self::filters( array( 'SearchText' => 'red car' ) )->parameters()['mode'] );
        $this->assertSame( 'red OR car OR "big boat"', self::filters( array( 'SearchText' => 'red  OR car "big boat"', 'SearchMode' => 'any' ) )->queryText() );
        // "any word" keeps what is excluded: NOT and a leading minus are no words to join with OR
        $this->assertSame( 'red OR car NOT bus -van', self::filters( array( 'SearchText' => 'red car NOT bus -van', 'SearchMode' => 'any' ) )->queryText() );
        $this->assertSame( '"red car"', self::filters( array( 'SearchText' => ' red "car" ', 'SearchMode' => 'phrase' ) )->queryText() );
        $this->assertArrayNotHasKey( 'extended_attribute_filter', array_filter( self::filters( array( 'SearchText' => 'a b', 'SearchMode' => 'any' ),
            expContentSearchFilters::ENGINE_EZFIND, array( 'extended_filters' => array( 'ezfindallwords' ) ) )->ezfindParameters( 0 ) ) );
        $chips = self::filters( array( 'SearchText' => 'a b', 'SearchMode' => 'phrase' ) )->activeFilters();
        $this->assertSame( 'mode', $chips[0]['type'] );
        $this->assertSame( '/(text)/a%20b', $chips[0]['query'] );
        // the text is cut to the limits of the view
        $long = self::filters( array( 'SearchText' => 'a b c d' ), expContentSearchFilters::ENGINE_EZFIND, array( 'maximum_words' => 2 ) )->parameters();
        $this->assertSame( 'a b', $long['text'] );
        $this->assertTrue( $long['text_shortened'] );
    }

    /** SF-08 */
    public function testPageSizeAndSorting()
    {
        $this->assertSame( 10, self::filters( array() )->parameters()['limit'] );
        $this->assertSame( 20, self::filters( array( 'SearchPageLimit' => '3' ) )->parameters()['limit'] );
        $this->assertSame( 30, self::filters( array( 'SearchPageLimit' => '5' ) )->parameters()['limit'], 'never more than MaximumSearchLimit' );
        $this->assertSame( 10, self::filters( array( 'SearchPageLimit' => '9' ) )->parameters()['limit'] );
        $header = self::filters( array( 'BrowsePageLimit' => '25' ) )->parameters();
        $this->assertSame( 25, $header['limit'] );
        $this->assertSame( 3, $header['page_limit'] );
        $this->assertSame( 'relevance', self::filters( array( 'SortBy' => 'evil' ) )->parameters()['sort'] );
        $this->assertSame( array( 'modified' => 'asc' ), self::filters( array( 'SortBy' => 'modified_asc' ) )->ezfindParameters( 0 )['sort_by'] );
    }

    /** SF-09 */
    public function testFacetCountsAndClassGroups()
    {
        $filters = self::filters( array( 'SectionID' => '3' ) );
        $facets = array( array( 'field' => 'meta_class_identifier_ms', 'countList' => array( 'article' => 5, 'unknown' => 2 ) ),
                         array( 'field' => 'meta_section_id_si', 'countList' => array( '1' => 7 ) ),
                         array( 'field' => 'translation', 'countList' => array( 'ger-DE' => 4 ) ) );
        $counts = $filters->facetCounts( $facets );
        $this->assertSame( array( 'class' => array( 2 => 5 ), 'section' => array( 1 => 7 ), 'language' => array( 'ger-DE' => 4 ) ), $counts );
        $this->assertSame( array( 'class' => array(), 'section' => array(), 'language' => array() ), $filters->facetCounts( null ) );

        $groups = $filters->classGroups( $counts['class'] );
        $this->assertSame( array( 'Content', 'Users' ), array_column( $groups, 'name' ) );
        $this->assertSame( array( 'article', 'folder' ), array_column( $groups[0]['classes'], 'identifier' ), 'hits first' );
        $this->assertSame( 5, $groups[0]['classes'][0]['count'] );
        $this->assertNull( $groups[0]['classes'][1]['count'] );

        $many = array();
        for ( $i = 1; $i <= 9; $i++ )
            $many[$i] = array( 'identifier' => "c$i", 'name' => "Class $i", 'groups' => array( 1 ) );
        $groups = self::filters( array( 'ClassID' => '9' ), expContentSearchFilters::ENGINE_EZFIND, array( 'classes' => $many ) )->classGroups();
        $this->assertSame( 'c9', $groups[0]['classes'][0]['identifier'], 'the chosen class first' );
        $this->assertSame( 3, $groups[0]['more'] );
        $this->assertSame( array( false, false, false, false, false, false, true, true, true ), array_column( $groups[0]['classes'], 'more' ) );
    }

    /** SF-11: without a subtree in the request the siteaccess's PathPrefix tree is searched, as content/search does */
    public function testThePathPrefixTreeIsTheDefaultSubtree()
    {
        foreach ( array( expContentSearchFilters::ENGINE_EZFIND, expContentSearchFilters::ENGINE_KERNEL ) as $engine )
        {
            $method = $engine === expContentSearchFilters::ENGINE_EZFIND ? 'ezfindParameters' : 'kernelParameters';
            $prefixed = self::filters( array( 'SearchText' => 'solr' ), $engine, array( 'default_subtree' => array( 61 ) ) );
            $this->assertSame( array( 61 ), $prefixed->$method( 0 )['subtree_array'], $engine );
            $asked = self::filters( array( 'SearchText' => 'solr', 'SubTreeArray' => array( '2' ) ), $engine, array( 'default_subtree' => array( 61 ) ) );
            $this->assertSame( array( 2 ), $asked->$method( 0 )['subtree_array'], $engine . ': a subtree in the request wins' );
            $this->assertArrayNotHasKey( 'subtree_array', self::filters( array( 'SearchText' => 'solr' ), $engine )->$method( 0 ), $engine );
        }
    }

    /** SF-12: the search statistics count what the filtered page searched, also with SearchViewHandling=default */
    public function testTheViewsLogTheTemplatesSearch()
    {
        foreach ( array( 'search', 'advancedsearch' ) as $view )
        {
            $code = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/views/content/' . $view . '.php' );
            $this->assertStringContainsString( '$searchData = $tpl->hasVariable( "search_data" ) ? $tpl->variable( "search_data" ) : false;', $code, $view );
            $this->assertStringContainsString( 'if ( $useSearchCode && !is_array( $searchData ) )', $code, $view );
        }
        $this->assertStringContainsString( '{if is_set( $search_data )}{set search_data = $page.search_data}{/if}',
                                           file_get_contents( dirname( __DIR__, 4 ) . '/design/standard/templates/content/search_page.tpl' ) );
    }

    /** SF-13: the view leaves its own search out only when the design's template is the filtered page */
    public function testTheViewKnowsWhenItsTemplateSearchesItself()
    {
        $root = sys_get_temp_dir() . '/exp-sf13-' . getmypid();
        foreach ( array( 'ext/templates/content', 'own/templates/content', 'old/templates/content' ) as $dir )
            @mkdir( "$root/$dir", 0777, true );
        file_put_contents( "$root/own/templates/content/search.tpl", "{* the page *}\n{include uri='design:content/search_page.tpl'}\n" );
        file_put_contents( "$root/old/templates/content/search.tpl", "{foreach \$search_result as \$r}{/foreach}\n" );
        try
        {
            $search = '\Exponential\View\Kernel\Content\Search';
            $this->assertTrue( $search::templateSearchesItself( 'content/search.tpl', array( "$root/ext", "$root/own", "$root/old" ) ) );
            // the first design that has the template wins, as in the template resource (an extension's own page)
            $this->assertFalse( $search::templateSearchesItself( 'content/search.tpl', array( "$root/old", "$root/own" ) ) );
            $this->assertFalse( $search::templateSearchesItself( 'content/advancedsearch.tpl', array( "$root/own" ) ), 'no such template' );
        }
        finally
        {
            foreach ( array( 'own', 'old' ) as $design )
                @unlink( "$root/$design/templates/content/search.tpl" );
            foreach ( array( 'ext', 'own', 'old' ) as $design )
            {
                @rmdir( "$root/$design/templates/content" );
                @rmdir( "$root/$design/templates" );
                @rmdir( "$root/$design" );
            }
            @rmdir( $root );
        }
        // both views ask before they search, and the admin templates include the page
        foreach ( array( 'search' => 'content/search.tpl', 'advancedsearch' => 'content/advancedsearch.tpl' ) as $view => $template )
        {
            $code = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/views/content/' . $view . '.php' );
            $this->assertStringContainsString( "templateSearchesItself( '$template' )", $code, $view );
        }
        // design/admin for every admin design; admin4 (and so admin4l and adminui) only search.tpl, before eZ Find's
        foreach ( array( 'admin/templates/content/search.tpl', 'admin/templates/content/advancedsearch.tpl', 'admin4/templates/content/search.tpl' ) as $file )
            $this->assertStringContainsString( "{include uri='design:content/search_page.tpl'}",
                file_get_contents( dirname( __DIR__, 4 ) . "/design/$file" ), $file );
    }

    /** SF-10 */
    public function testFetchRunsTheRightSearch()
    {
        $calls = array();
        $execute = function ( $module, $function, $params ) use ( &$calls ) {
            $calls[] = "$module/$function";
            return array( 'SearchResult' => array( 'node' ), 'SearchCount' => 1, 'StopWordArray' => array() );
        };
        $page = self::filters( array() )->fetch( 0, $execute );
        $this->assertSame( array(), $calls, 'an empty page runs no search' );
        $this->assertFalse( $page['requested'] );

        // filters alone search only where site.ini AllowEmptySearch allows it, with eZ Find too
        $page = self::filters( array( 'ClassID' => '2' ) )->fetch( 10, $execute );
        $this->assertSame( array(), $calls, 'without AllowEmptySearch, filters alone run no search' );
        $this->assertFalse( $page['requested'] );
        $page = self::filters( array( 'ClassID' => '2' ), expContentSearchFilters::ENGINE_EZFIND, array( 'allow_empty_search' => true ) )->fetch( 10, $execute );
        $this->assertSame( array( 'ezfind/search' ), $calls, 'eZ Find searches with filters alone' );
        $this->assertSame( 1, $page['count'] );
        $this->assertSame( 10, $page['offset'] );
        $this->assertSame( '/(class)/2', $page['query'] );

        $page = self::filters( array( 'SearchText' => 'x' ), expContentSearchFilters::ENGINE_KERNEL )->fetch( 0, $execute );
        $this->assertSame( array( 'ezfind/search', 'content/search' ), $calls );
        $this->assertSame( array( array( 'node' ) ), array( $page['result'] ) );
        $this->assertFalse( $page['failed'] );
        $this->assertSame( array( 'Standard', 'Media' ), array_column( $page['sections'], 'name' ) );
    }

    /** SF-14 */
    public function testTheSearchTravelsAsViewParameters()
    {
        $filters = self::filters( array( 'SearchText' => 'a/b (c) 100%', 'ClassID' => array( '2', '1' ), 'SectionID' => '1',
                                         'SubTreeArray' => array( '2' ), 'Modified' => 'custom', 'ModifiedFrom' => '2026-01-01',
                                         'ModifiedTo' => '2026-02-01', 'Owner' => 'admin', 'Language' => 'ger-DE',
                                         'SortBy' => 'name_desc', 'SearchPageLimit' => '3', 'SearchMode' => 'any' ) );
        $suffix = $filters->viewSuffix();
        $this->assertStringNotContainsString( '?', $suffix );
        $this->assertStringNotContainsString( '&', $suffix );
        $this->assertStringContainsString( '/(class)/2,1/', $suffix, 'a list is one parameter, its values joined by commas' );

        // the kernel decodes the address and hands the (name)/value pairs to the view as UserParameters
        $user = array();
        $segments = explode( '/', ltrim( $suffix, '/' ) );
        for ( $i = 0; $i + 1 < count( $segments ); $i += 2 )
            $user[trim( $segments[$i], '()' )] = rawurldecode( $segments[$i + 1] );
        $request = expContentSearchFilters::requestFromViewParameters( array( 'UserParameters' => $user ) );
        $this->assertSame( 'a/b (c) 100%', $request['SearchText'] );
        $this->assertSame( array( '2', '1' ), $request['ClassID'] );
        $again = self::filters( $request );
        $this->assertSame( $filters->parameters(), $again->parameters(), 'the address gives the same search back' );
        $this->assertSame( $suffix, $again->viewSuffix() );

        // a query string is not a view parameter; the page reads only what the view hands over
        $this->assertSame( array(), expContentSearchFilters::requestFromViewParameters( array( 'UserParameters' => array(), 'SearchText' => 'x' ) ) );
        $this->assertSame( '', self::filters( array() )->viewSuffix() );
        $this->assertContains( 'PhraseSearchText', expContentSearchFilters::FORM_FIELDS, 'a posted advanced search is redirected too' );
        $this->assertContains( 'SearchText', expContentSearchFilters::FORM_FIELDS );
    }
}
