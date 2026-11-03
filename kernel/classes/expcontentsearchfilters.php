<?php
/**
 * File containing the expContentSearchFilters class.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The filters of the administration's search page (content/search with its filter bar, which also takes in the
 * former advanced search): reads them from the request, checks every value, and turns them into the parameters
 * of the search back end that is there.
 *
 * Two back ends:
 * - eZ Find (site.ini [SearchSettings] SearchEngine=ezsolr and the ezfind fetch functions present):
 *   fetch( 'ezfind', 'search' ) with query, offset, limit, class_id, class_attribute_id, section_id,
 *   subtree_array, filter (modified, published, owner_id, language_code), facet (class_identifier, section_id,
 *   translation), sort_by and spell_check, all parameters of the eZ Find package (se7enxweb/ezfind 3.0).
 *   Extended attribute filters are only asked for when ezfind.ini [ExtendedAttributeFilters] FiltersList
 *   registers them and their class exists: ezfindallwords (every word required) and ezfindcollapse (the parts
 *   of a document, such as PDF pages, folded into the document), when an installation has it.
 * - the built-in engine: fetch( 'content', 'search' ) with text, class_id, class_attribute_id, section_id,
 *   subtree_array, publish_timestamp and sort_by. It cannot filter by modification date, owner or language, find
 *   any of the words, count facets or sort by relevance; those filters are not offered (capabilities()).
 *
 * The parameters of the former content/advancedsearch (SearchContentClassID, SearchContentClassAttributeID,
 * SearchSectionID, SearchDate, SearchTimestamp, PhraseSearchText) are read as well (mapAdvancedSearch()), so
 * its old addresses keep working.
 *
 * Everything but fetch() and the default catalogue/resolvers works without a database (expContentSearchFiltersTest).
 * The template side is design:content/search.tpl of the admin designs, through
 * fetch( 'content', 'filtered_search', hash( 'offset', ... ) ).
 */
class expContentSearchFilters
{
    const ENGINE_EZFIND = 'ezfind';
    const ENGINE_KERNEL = 'kernel';

    /** The page sizes of SearchPageLimit 1 to 5, as content/search and content/advancedsearch always had them */
    const PAGE_LIMITS = array( 1 => 5, 2 => 10, 3 => 20, 4 => 30, 5 => 50 );

    /** The periods of the date filters; custom takes a from and a to date */
    const PERIODS = array( 'day', 'week', 'month', 'year', 'custom' );

    /** The sort orders, each as field and ascending */
    const SORTS = array( 'relevance' => array( 'score', false ),
                         'name_asc' => array( 'name', true ),
                         'name_desc' => array( 'name', false ),
                         'modified_desc' => array( 'modified', false ),
                         'modified_asc' => array( 'modified', true ),
                         'published_desc' => array( 'published', false ),
                         'published_asc' => array( 'published', true ) );

    /** How many classes of a class group are shown before "Show more" */
    const CLASSES_SHOWN = 6;

    /** The filters as view parameters: request name => the name in the address (/(name)/value) */
    const VIEW_PARAMETERS = array( 'SearchText' => 'text', 'SearchMode' => 'mode', 'ClassID' => 'class',
                                   'ClassAttributeID' => 'attribute', 'SectionID' => 'section',
                                   'Modified' => 'modified', 'ModifiedFrom' => 'modified_from', 'ModifiedTo' => 'modified_to',
                                   'Published' => 'published', 'PublishedFrom' => 'published_from', 'PublishedTo' => 'published_to',
                                   'Owner' => 'owner', 'Language' => 'language', 'SubTreeArray' => 'subtree',
                                   'SortBy' => 'sort', 'SearchPageLimit' => 'limit' );

    /** The filters that are lists: one view parameter, the values joined by commas (/(class)/2,16) */
    const LIST_PARAMETERS = array( 'ClassID', 'SectionID', 'SubTreeArray' );

    /** The fields of the search forms whose POST the views answer with a 303 to the view parameters: the filter
     *  bar, the header search field and the fields of the former advanced search */
    const FORM_FIELDS = array( 'SearchText', 'SearchMode', 'ClassID', 'ClassAttributeID', 'SectionID', 'Modified',
                               'ModifiedFrom', 'ModifiedTo', 'Published', 'PublishedFrom', 'PublishedTo', 'Owner',
                               'Language', 'SubTreeArray', 'SortBy', 'SearchPageLimit', 'SearchButton',
                               'PhraseSearchText', 'SearchContentClassID', 'SearchContentClassAttributeID',
                               'SearchSectionID', 'SearchDate', 'SearchTimestamp' );

    /** @var array the request of the current view's view parameters (useViewParameters()) */
    private static $viewRequest = array();

    /** @var string */
    private $engine;
    /** @var array what the back end can do */
    private $capabilities;
    /** @var array the checked filters, see parameters() */
    private $parameters;
    /** @var array the catalogue the values were checked against */
    private $catalog;
    /** @var string[] request values that were not understood (an invalid date, an unknown user) */
    private $rejected = array();

    /**
     * @param array $request the request variables (GET and POST)
     * @param string $engine ENGINE_EZFIND or ENGINE_KERNEL
     * @param array $catalog what a value is checked against:
     *        classes        id => array( 'identifier' => , 'name' => , 'groups' => array( group id, ... ) )
     *        class_groups   id => name
     *        sections       id => name
     *        languages      locale => name
     *        extended_filters  the registered eZ Find filters that exist (names)
     *        allow_empty_search  bool: the built-in engine searches without words (site.ini AllowEmptySearch)
     *        maximum_limit  site.ini [SearchSettings] MaximumSearchLimit
     *        maximum_words, maximum_length  the limits of the search text (Search::searchTextLimits())
     *        class_attributes  callable( class id ) => array( id => name ) of the searchable attributes
     *        owner          callable( string input ) => array( 'id' => , 'name' => ) or null
     *        node           callable( int node id ) => array( 'name' => , 'path' => ) or null
     *        default_subtree  int[] searched when the request names no subtree: the node of the siteaccess's
     *                       site.ini [SiteAccessSettings] PathPrefix, as content/search does
     * @param int|null $now the time the periods count back from
     */
    public function __construct( array $request, $engine, array $catalog, $now = null )
    {
        $this->engine = $engine === self::ENGINE_EZFIND ? self::ENGINE_EZFIND : self::ENGINE_KERNEL;
        $this->catalog = $catalog + array( 'classes' => array(), 'class_groups' => array(), 'sections' => array(),
                                           'languages' => array(), 'extended_filters' => array(),
                                           'allow_empty_search' => false, 'maximum_limit' => 50,
                                           'maximum_words' => 16, 'maximum_length' => 1024,
                                           'class_attributes' => null, 'owner' => null, 'node' => null,
                                           'default_subtree' => array() );
        $this->capabilities = self::capabilitiesFor( $this->engine, $this->catalog['extended_filters'] );
        // a search without words (filters only) runs only where site.ini AllowEmptySearch allows it, also with eZ Find:
        // eZSolr refuses such a query then, and the page would show an error instead of asking for words
        $this->capabilities['empty_search'] = (bool)$this->catalog['allow_empty_search'];
        $now = $now === null ? time() : (int)$now;
        $this->parameters = $this->normalize( self::mapAdvancedSearch( $request, $now ), $now );
    }

    /**
     * What a back end can do. The search page shows only these filters.
     *
     * @param string $engine
     * @param string[] $extendedFilters the eZ Find filters that are registered and exist
     * @return array
     */
    public static function capabilitiesFor( $engine, array $extendedFilters = array() )
    {
        if ( $engine === self::ENGINE_EZFIND )
        {
            // "all words", the default, is the text as typed: with the filter ezfindallwords where it is registered,
            // else eZ Find's own matching (its minimum match asks for all words of a short query). Only "any word"
            // rewrites the text, so a query reaches the engine unchanged unless the user asks for that.
            return array( 'modes' => array( 'all', 'any', 'phrase' ), 'modified' => true, 'published' => true, 'owner' => true, 'language' => true,
                          'facets' => true, 'empty_search' => true, 'collapse' => in_array( 'ezfindcollapse', $extendedFilters, true ),
                          'spell_check' => true,
                          'sorts' => array_keys( self::SORTS ) );
        }
        return array( 'modes' => array( 'all', 'phrase' ), 'modified' => false, 'published' => true, 'owner' => false,
                      'language' => false, 'facets' => false, 'empty_search' => false, 'collapse' => false,
                      'spell_check' => false,
                      'sorts' => array_values( array_diff( array_keys( self::SORTS ), array( 'relevance' ) ) ) );
    }

    /**
     * The parameters of the former content/advancedsearch as parameters of this page. A request that already
     * carries the new name keeps it.
     *
     *   SearchText + PhraseSearchText      SearchText "<phrase>" <words>, or the phrase alone with SearchMode=phrase
     *   SearchContentClassID               ClassID[]
     *   SearchContentClassAttributeID      ClassAttributeID
     *   SearchSectionID                    SectionID[]
     *   SearchDate 1, 2, 3, 5              Published=day, week, month, year
     *   SearchDate 4 (last three months)   Published=custom, PublishedFrom three months back
     *   SearchTimestamp                    Published=custom, PublishedFrom that day
     *
     * SubTreeArray, SearchPageLimit and the offset have the same names on both pages.
     *
     * @param array $request
     * @param int|null $now
     * @return array
     */
    public static function mapAdvancedSearch( array $request, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $phrase = isset( $request['PhraseSearchText'] ) && is_string( $request['PhraseSearchText'] ) ? trim( str_replace( '"', ' ', $request['PhraseSearchText'] ) ) : '';
        if ( $phrase !== '' )
        {
            $words = isset( $request['SearchText'] ) && is_string( $request['SearchText'] ) ? trim( $request['SearchText'] ) : '';
            if ( $words === '' )
            {
                $request['SearchText'] = $phrase;
                if ( !isset( $request['SearchMode'] ) )
                    $request['SearchMode'] = 'phrase';
            }
            else
            {
                $request['SearchText'] = '"' . $phrase . '" ' . $words;
            }
        }
        unset( $request['PhraseSearchText'] );

        $renames = array( 'SearchContentClassID' => 'ClassID',
                          'SearchContentClassAttributeID' => 'ClassAttributeID',
                          'SearchSectionID' => 'SectionID' );
        foreach ( $renames as $old => $new )
        {
            if ( array_key_exists( $old, $request ) )
            {
                if ( !isset( $request[$new] ) )
                    $request[$new] = $request[$old];
                unset( $request[$old] );
            }
        }

        if ( !isset( $request['Published'] ) )
        {
            $searchDate = isset( $request['SearchDate'] ) && is_scalar( $request['SearchDate'] ) ? (int)$request['SearchDate'] : 0;
            $periods = array( 1 => 'day', 2 => 'week', 3 => 'month', 5 => 'year' );
            if ( isset( $periods[$searchDate] ) )
            {
                $request['Published'] = $periods[$searchDate];
            }
            elseif ( $searchDate === 4 )
            {
                $request['Published'] = 'custom';
                $request['PublishedFrom'] = date( 'Y-m-d', strtotime( '-3 months', $now ) );
            }
            elseif ( isset( $request['SearchTimestamp'] ) && is_scalar( $request['SearchTimestamp'] ) && (int)$request['SearchTimestamp'] > 0 )
            {
                $request['Published'] = 'custom';
                $request['PublishedFrom'] = date( 'Y-m-d', (int)$request['SearchTimestamp'] );
            }
        }
        unset( $request['SearchDate'], $request['SearchTimestamp'] );
        return $request;
    }

    /**
     * The checked filters:
     *   text, mode, class_ids, class_attribute_id, section_ids, modified and published (each with period, from,
     *   to as Y-m-d, and from_time, to_time as timestamps or null), owner (input, id, name or null), language,
     *   subtree_ids, sort, page_limit (1 to 5, or null), limit, text_shortened
     *
     * @return array
     */
    public function parameters()
    {
        return $this->parameters;
    }

    /** @return string ENGINE_EZFIND or ENGINE_KERNEL */
    public function engine()
    {
        return $this->engine;
    }

    /** @return array see capabilitiesFor() */
    public function capabilities()
    {
        return $this->capabilities;
    }

    /** @return string[] the request values that were not understood: ModifiedFrom, PublishedTo, Owner ... */
    public function rejected()
    {
        return $this->rejected;
    }

    /**
     * Whether any filter is set; the words, the mode, the sorting and the page size are no filters.
     *
     * @return bool
     */
    public function hasFilters()
    {
        $p = $this->parameters;
        return $p['class_ids'] || $p['section_ids'] || $p['subtree_ids'] || $p['language'] !== ''
            || $p['owner']['id'] || $p['modified']['period'] !== '' || $p['published']['period'] !== '';
    }

    /**
     * Whether a search runs: with words, or with a filter where the back end searches without words. An empty
     * page (no words, no filter) runs none: it would list every object.
     *
     * @return bool
     */
    public function isSearchRequested()
    {
        if ( trim( $this->parameters['text'] ) !== '' )
            return true;
        if ( !$this->hasFilters() )
            return false;
        return $this->capabilities['empty_search'] || $this->catalog['allow_empty_search'];
    }

    /**
     * The search text as the back end gets it: the words for all of them, joined by OR for any of them, in quotes
     * for the phrase.
     *
     * @return string
     */
    public function queryText()
    {
        $text = trim( $this->parameters['text'] );
        if ( $text === '' )
            return '';
        switch ( $this->parameters['mode'] )
        {
            case 'phrase':
                return '"' . trim( preg_replace( '/\s+/u', ' ', str_replace( '"', ' ', $text ) ) ) . '"';
            case 'any':
                preg_match_all( '/"[^"]*"|[^\s"]+/u', $text, $m );
                $words = array();
                $excluded = array();
                $negate = false;
                foreach ( $m[0] as $word )
                {
                    if ( $word === 'NOT' )
                    {
                        $negate = true;
                        continue;
                    }
                    // the other operators themselves are no words
                    if ( in_array( $word, array( 'OR', 'AND', '"', '""' ), true ) )
                        continue;
                    // an excluded word stays excluded: "foo NOT bar" and "foo -bar" do not become "foo OR bar"
                    if ( $negate || ( strlen( $word ) > 1 && $word[0] === '-' ) )
                        $excluded[] = $negate ? 'NOT ' . $word : $word;
                    else
                        $words[] = $word;
                    $negate = false;
                }
                return trim( implode( ' OR ', $words ) . ( $excluded ? ' ' . implode( ' ', $excluded ) : '' ) );
        }
        return $text;
    }

    /**
     * The parameters of fetch( 'ezfind', 'search' ).
     *
     * @param int $offset
     * @return array
     */
    public function ezfindParameters( $offset )
    {
        $p = $this->parameters;
        $params = array( 'query' => $this->queryText(),
                         'offset' => max( 0, (int)$offset ),
                         'limit' => $p['limit'],
                         'sort_by' => self::ezfindSort( $p['sort'] ),
                         'facet' => array( array( 'field' => 'class_identifier', 'limit' => 500, 'mincount' => 1 ),
                                           array( 'field' => 'section_id', 'limit' => 200, 'mincount' => 1 ),
                                           array( 'field' => 'translation', 'limit' => 100, 'mincount' => 1 ) ) );
        if ( $p['text'] !== '' )
            $params['spell_check'] = array( true );
        if ( $p['class_ids'] )
            $params['class_id'] = $p['class_ids'];
        if ( $p['class_attribute_id'] )
            $params['class_attribute_id'] = $p['class_attribute_id'];
        if ( $p['section_ids'] )
            $params['section_id'] = $p['section_ids'];
        if ( $p['subtree_ids'] )
            $params['subtree_array'] = $p['subtree_ids'];
        elseif ( $this->catalog['default_subtree'] )
            $params['subtree_array'] = $this->catalog['default_subtree'];   // the siteaccess's PathPrefix tree

        // Only checked values get here: dates as Solr dates, ids as integers, a locale of the language list
        $filter = array();
        foreach ( array( 'modified', 'published' ) as $field )
        {
            $range = $p[$field];
            if ( $range['from_time'] !== null || $range['to_time'] !== null )
                $filter[] = $field . ':[' . self::solrDate( $range['from_time'] ) . ' TO ' . self::solrDate( $range['to_time'] ) . ']';
        }
        if ( $p['owner']['id'] )
            $filter[] = 'owner_id:' . (int)$p['owner']['id'];
        if ( $p['language'] !== '' )
            $filter[] = 'language_code:' . $p['language'];
        if ( $filter )
            $params['filter'] = $filter;

        $extended = array();
        if ( $this->capabilities['collapse'] )
            $extended[] = array( 'id' => 'ezfindcollapse', 'params' => array() );
        if ( $p['mode'] === 'all' && in_array( 'ezfindallwords', $this->catalog['extended_filters'], true ) )
            $extended[] = array( 'id' => 'ezfindallwords', 'params' => array() );
        if ( $extended )
            $params['extended_attribute_filter'] = $extended;
        return $params;
    }

    /**
     * The parameters of fetch( 'content', 'search' ) for the built-in engine.
     *
     * @param int $offset
     * @return array
     */
    public function kernelParameters( $offset )
    {
        $p = $this->parameters;
        list( $field, $ascending ) = self::SORTS[$p['sort']];
        $params = array( 'text' => $this->queryText(),
                         'offset' => max( 0, (int)$offset ),
                         'limit' => $p['limit'],
                         'sort_by' => array( array( $field, $ascending ) ) );
        if ( $p['class_ids'] )
            $params['class_id'] = count( $p['class_ids'] ) === 1 ? $p['class_ids'][0] : $p['class_ids'];
        if ( $p['class_attribute_id'] )
            $params['class_attribute_id'] = $p['class_attribute_id'];
        if ( $p['section_ids'] )
            $params['section_id'] = count( $p['section_ids'] ) === 1 ? $p['section_ids'][0] : $p['section_ids'];
        if ( $p['subtree_ids'] )
            $params['subtree_array'] = $p['subtree_ids'];
        elseif ( $this->catalog['default_subtree'] )
            $params['subtree_array'] = $this->catalog['default_subtree'];   // the siteaccess's PathPrefix tree
        $published = $p['published'];
        if ( $published['to_time'] !== null )
            $params['publish_timestamp'] = array( (int)$published['from_time'], $published['to_time'] );
        elseif ( $published['from_time'] !== null )
            $params['publish_timestamp'] = $published['from_time'];
        return $params;
    }

    /**
     * The query string of this search, without the page (offset): for the pager, the sort order and the chips.
     * Arrays are written as Name[]=value, once per value.
     *
     * @param array $without request names to leave out, or name => value to leave out one value of a list
     * @param array $with request names to set instead
     * @return string without the leading ?
     */
    public function queryString( array $without = array(), array $with = array() )
    {
        $parts = array();
        foreach ( $this->pairs( $without, $with ) as $name => $value )
        {
            foreach ( (array)$value as $item )
                $parts[] = urlencode( is_array( $value ) ? $name . '[]' : $name ) . '=' . urlencode( (string)$item );
        }
        return implode( '&', $parts );
    }

    /**
     * This search as view parameters, without the page (offset): the address part after content/search for the
     * pager, the sort order, the chips and the spelling suggestion, "/(text)/easter/(class)/2,16". The page never
     * writes a query string (?Name=value), and the views never read one (expViewParameters).
     *
     * @param array $without request names to leave out, or name => value to leave out one value of a list
     * @param array $with request names to set instead
     * @return string '' when nothing is set
     */
    public function viewSuffix( array $without = array(), array $with = array() )
    {
        $options = array();
        foreach ( $this->pairs( $without, $with ) as $name => $value )
        {
            if ( isset( self::VIEW_PARAMETERS[$name] ) )
                $options[self::VIEW_PARAMETERS[$name]] = is_array( $value ) ? implode( ',', $value ) : (string)$value;
        }
        return expViewParameters::suffix( $options );
    }

    /**
     * The request (request name => value, lists as arrays) that the view parameters of an address give, for the
     * constructor: the inverse of viewSuffix().
     *
     * @param array $params the view's $Params (its UserParameters)
     * @return array
     */
    public static function requestFromViewParameters( array $params )
    {
        $request = array();
        foreach ( self::VIEW_PARAMETERS as $name => $parameter )
        {
            $value = expViewParameters::get( $params, $parameter );
            if ( $value === false || $value === '' )
                continue;
            $request[$name] = in_array( $name, self::LIST_PARAMETERS, true ) ? explode( ',', $value ) : $value;
        }
        return $request;
    }

    /**
     * The view parameters of the current content/search or content/advancedsearch request, which the template
     * fetch reads (fromCurrentRequest()). Set by the view before it renders the page.
     *
     * @param array $params the view's $Params
     */
    public static function useViewParameters( array $params )
    {
        self::$viewRequest = self::requestFromViewParameters( $params );
    }

    /**
     * The search a request of the search forms asks for (the filter bar, the header field, the former advanced
     * search), checked against the catalogue of this installation: the views answer such a POST with a 303 to
     * content/search and viewSuffix().
     *
     * @param array $request usually $_POST
     * @return expContentSearchFilters
     */
    public static function fromRequest( array $request )
    {
        return new self( $request, self::currentEngine(), self::currentCatalog() );
    }

    /**
     * The request values of this search with some left out or set instead, the class attribute only with exactly
     * one class.
     *
     * @param array $without
     * @param array $with
     * @return array
     */
    private function pairs( array $without, array $with )
    {
        $pairs = $this->requestValues();
        foreach ( $without as $name => $value )
        {
            if ( is_int( $name ) )
            {
                unset( $pairs[$value] );
            }
            elseif ( isset( $pairs[$name] ) && is_array( $pairs[$name] ) )
            {
                $pairs[$name] = array_values( array_diff( $pairs[$name], array( (string)$value ) ) );
                if ( !$pairs[$name] )
                    unset( $pairs[$name] );
            }
        }
        foreach ( $with as $name => $value )
            $pairs[$name] = $value;
        // the class attribute belongs to exactly one class
        if ( isset( $pairs['ClassAttributeID'] ) && ( !isset( $pairs['ClassID'] ) || count( (array)$pairs['ClassID'] ) !== 1 ) )
            unset( $pairs['ClassAttributeID'] );
        return $pairs;
    }

    /**
     * The filters set, each one to remove on its own: type, label (the value as a person reads it; the template
     * puts the translated name of the filter in front) and query (this search without it).
     *
     * @return array[]
     */
    public function activeFilters()
    {
        $p = $this->parameters;
        $chips = array();
        if ( $p['mode'] !== $this->capabilities['modes'][0] && trim( $p['text'] ) !== '' )
            $chips[] = array( 'type' => 'mode', 'value' => $p['mode'], 'label' => $p['mode'], 'query' => $this->viewSuffix( array( 'SearchMode' ) ) );
        foreach ( $p['class_ids'] as $classID )
        {
            $name = isset( $this->catalog['classes'][$classID] ) ? $this->catalog['classes'][$classID]['name'] : (string)$classID;
            $chips[] = array( 'type' => 'class', 'value' => $classID, 'label' => $name, 'query' => $this->viewSuffix( array( 'ClassID' => $classID ) ) );
        }
        if ( $p['class_attribute_id'] )
        {
            $attributes = $this->classAttributes();
            $name = isset( $attributes[$p['class_attribute_id']] ) ? $attributes[$p['class_attribute_id']] : (string)$p['class_attribute_id'];
            $chips[] = array( 'type' => 'class_attribute', 'value' => $p['class_attribute_id'], 'label' => $name, 'query' => $this->viewSuffix( array( 'ClassAttributeID' ) ) );
        }
        foreach ( $p['section_ids'] as $sectionID )
        {
            $name = isset( $this->catalog['sections'][$sectionID] ) ? $this->catalog['sections'][$sectionID] : (string)$sectionID;
            $chips[] = array( 'type' => 'section', 'value' => $sectionID, 'label' => $name, 'query' => $this->viewSuffix( array( 'SectionID' => $sectionID ) ) );
        }
        foreach ( array( 'modified' => 'Modified', 'published' => 'Published' ) as $field => $name )
        {
            $range = $p[$field];
            if ( $range['period'] === '' )
                continue;
            $label = $range['period'] === 'custom' ? trim( $range['from'] . ' – ' . $range['to'], ' –' ) : $range['period'];
            if ( $range['period'] === 'custom' && $range['from'] !== '' && $range['to'] === '' )
                $label = '≥ ' . $range['from'];
            if ( $range['period'] === 'custom' && $range['from'] === '' && $range['to'] !== '' )
                $label = '≤ ' . $range['to'];
            $chips[] = array( 'type' => $field, 'value' => $range['period'], 'label' => $label,
                              'query' => $this->viewSuffix( array( $name, $name . 'From', $name . 'To' ) ) );
        }
        if ( $p['owner']['id'] )
            $chips[] = array( 'type' => 'owner', 'value' => $p['owner']['id'], 'label' => $p['owner']['name'], 'query' => $this->viewSuffix( array( 'Owner' ) ) );
        if ( $p['language'] !== '' )
        {
            $name = isset( $this->catalog['languages'][$p['language']] ) ? $this->catalog['languages'][$p['language']] : $p['language'];
            $chips[] = array( 'type' => 'language', 'value' => $p['language'], 'label' => $name, 'query' => $this->viewSuffix( array( 'Language' ) ) );
        }
        foreach ( $p['subtree_ids'] as $nodeID )
        {
            $node = $this->node( $nodeID );
            $chips[] = array( 'type' => 'subtree', 'value' => $nodeID, 'label' => $node ? $node['path'] : '#' . $nodeID,
                              'query' => $this->viewSuffix( array( 'SubTreeArray' => $nodeID ) ) );
        }
        return $chips;
    }

    /**
     * The class groups with their classes for the filter bar: per class: id, identifier, name, count (null when
     * unknown), selected, more (behind "Show more"). Chosen classes and classes with hits first.
     *
     * @param array $classCounts class id => hits, from the facets
     * @return array[] id, name, classes, more (how many are behind "Show more")
     */
    public function classGroups( array $classCounts = array() )
    {
        $groups = array();
        foreach ( $this->catalog['class_groups'] as $groupID => $groupName )
            $groups[$groupID] = array( 'id' => $groupID, 'name' => $groupName, 'classes' => array(), 'more' => 0 );
        foreach ( $this->catalog['classes'] as $classID => $class )
        {
            $entry = array( 'id' => $classID, 'identifier' => $class['identifier'], 'name' => $class['name'],
                            'count' => array_key_exists( $classID, $classCounts ) ? (int)$classCounts[$classID] : null,
                            'selected' => in_array( $classID, $this->parameters['class_ids'], true ), 'more' => false );
            // a class is listed once, in its first group
            $groupIDs = isset( $class['groups'] ) ? (array)$class['groups'] : array();
            $groupID = $groupIDs ? reset( $groupIDs ) : 0;
            if ( !isset( $groups[$groupID] ) )
                $groups[$groupID] = array( 'id' => $groupID, 'name' => '', 'classes' => array(), 'more' => 0 );
            $groups[$groupID]['classes'][] = $entry;
        }
        foreach ( $groups as $groupID => $group )
        {
            if ( !$group['classes'] )
            {
                unset( $groups[$groupID] );
                continue;
            }
            usort( $group['classes'], function ( $a, $b ) {
                if ( $a['selected'] !== $b['selected'] )
                    return $a['selected'] ? -1 : 1;
                if ( (int)$a['count'] !== (int)$b['count'] )
                    return (int)$b['count'] - (int)$a['count'];
                return strcasecmp( $a['name'], $b['name'] );
            } );
            foreach ( $group['classes'] as $index => $class )
            {
                if ( $index >= self::CLASSES_SHOWN && !$class['selected'] )
                {
                    $group['classes'][$index]['more'] = true;
                    $group['more']++;
                }
            }
            $groups[$groupID] = $group;
        }
        return array_values( $groups );
    }

    /**
     * The searchable attributes of the one class chosen, id => name; empty unless exactly one class is chosen.
     *
     * @return array
     */
    public function classAttributes()
    {
        if ( count( $this->parameters['class_ids'] ) !== 1 || !is_callable( $this->catalog['class_attributes'] ) )
            return array();
        return (array)call_user_func( $this->catalog['class_attributes'], $this->parameters['class_ids'][0] );
    }

    /**
     * The facets of an eZ Find search, as class id => count, section id => count and locale => count. The facets
     * come in the order ezfindParameters() asks for them.
     *
     * @param mixed $facetFields SearchExtras.facet_fields
     * @return array 'class', 'section', 'language'
     */
    public function facetCounts( $facetFields )
    {
        $counts = array( 'class' => array(), 'section' => array(), 'language' => array() );
        if ( !is_array( $facetFields ) )
            return $counts;
        $identifiers = array();
        foreach ( $this->catalog['classes'] as $classID => $class )
            $identifiers[$class['identifier']] = $classID;
        foreach ( array_values( $facetFields ) as $index => $facet )
        {
            $list = isset( $facet['countList'] ) && is_array( $facet['countList'] ) ? $facet['countList'] : array();
            foreach ( $list as $value => $count )
            {
                if ( $index === 0 && isset( $identifiers[$value] ) )
                    $counts['class'][$identifiers[$value]] = (int)$count;
                elseif ( $index === 1 && ctype_digit( (string)$value ) )
                    $counts['section'][(int)$value] = (int)$count;
                elseif ( $index === 2 )
                    $counts['language'][(string)$value] = (int)$count;
            }
        }
        return $counts;
    }

    /**
     * Runs the search and gathers what the page shows.
     *
     * @param int $offset
     * @param callable|null $execute ( module, function, parameters ) => result; defaults to eZFunctionHandler::execute()
     * @return array
     */
    public function fetch( $offset, $execute = null )
    {
        if ( $execute === null )
            $execute = function ( $module, $function, $parameters ) { return eZFunctionHandler::execute( $module, $function, $parameters ); };
        $offset = max( 0, (int)$offset );
        $search = false;
        if ( $this->isSearchRequested() )
        {
            $search = $this->engine === self::ENGINE_EZFIND
                    ? call_user_func( $execute, 'ezfind', 'search', $this->ezfindParameters( $offset ) )
                    : call_user_func( $execute, 'content', 'search', $this->kernelParameters( $offset ) );
        }
        $extras = is_array( $search ) && isset( $search['SearchExtras'] ) ? $search['SearchExtras'] : null;
        $facets = array( 'class' => array(), 'section' => array(), 'language' => array() );
        $failed = false;
        $spellcheck = null;
        $textShortened = $this->parameters['text_shortened'];
        if ( is_object( $extras ) && method_exists( $extras, 'attribute' ) )
        {
            $failed = (bool)$extras->attribute( 'error' );
            if ( !$failed && $this->capabilities['facets'] )
                $facets = $this->facetCounts( $extras->attribute( 'facet_fields' ) );
            // eZ Find's spelling suggestion: the same search with the suggested words
            $collation = !$failed && $this->capabilities['spell_check'] ? $extras->attribute( 'spellcheck_collation' ) : null;
            if ( is_string( $collation ) && trim( $collation ) !== '' && trim( $collation ) !== trim( $this->parameters['text'] ) )
                $spellcheck = array( 'text' => $collation, 'query' => $this->viewSuffix( array(), array( 'SearchText' => $collation ) ) );
            // eZ Find shortens a text to the words its query fields allow
            if ( !$failed && $this->capabilities['spell_check'] && $extras->attribute( 'text_shortened' ) )
                $textShortened = true;
        }
        $p = $this->parameters;

        $sections = array();
        foreach ( $this->catalog['sections'] as $sectionID => $name )
            $sections[] = array( 'id' => $sectionID, 'name' => $name, 'selected' => in_array( $sectionID, $p['section_ids'], true ),
                                 'count' => array_key_exists( $sectionID, $facets['section'] ) ? $facets['section'][$sectionID] : null );
        $languages = array();
        foreach ( $this->catalog['languages'] as $locale => $name )
            $languages[] = array( 'locale' => $locale, 'name' => $name, 'selected' => $p['language'] === $locale,
                                  'count' => array_key_exists( $locale, $facets['language'] ) ? $facets['language'][$locale] : null );
        $attributes = array();
        foreach ( $this->classAttributes() as $id => $name )
            $attributes[] = array( 'id' => $id, 'name' => $name, 'selected' => $id === $p['class_attribute_id'] );
        $subtrees = array();
        foreach ( $p['subtree_ids'] as $nodeID )
        {
            $node = $this->node( $nodeID );
            $subtrees[] = array( 'node_id' => $nodeID, 'name' => $node ? $node['name'] : '#' . $nodeID, 'path' => $node ? $node['path'] : '#' . $nodeID );
        }

        return array( 'engine' => $this->engine,
                      'capabilities' => $this->capabilities,
                      'filters' => $p,
                      'has_filters' => $this->hasFilters(),
                      'requested' => $this->isSearchRequested(),
                      'rejected' => $this->rejected,
                      'failed' => $failed,
                      'spellcheck' => $spellcheck,
                      'text_shortened' => $textShortened,
                      'result' => is_array( $search ) && isset( $search['SearchResult'] ) ? $search['SearchResult'] : array(),
                      'count' => is_array( $search ) && isset( $search['SearchCount'] ) ? (int)$search['SearchCount'] : 0,
                      'stop_words' => is_array( $search ) && isset( $search['StopWordArray'] ) ? $search['StopWordArray'] : array(),
                      'extras' => $extras,
                      'search_data' => $search,
                      'chips' => $this->activeFilters(),
                      'query' => $this->viewSuffix(),
                      'class_groups' => $this->classGroups( $facets['class'] ),
                      'class_attributes' => $attributes,
                      'sections' => $sections,
                      'languages' => $languages,
                      'subtrees' => $subtrees,
                      'sorts' => $this->capabilities['sorts'],
                      'page_limits' => self::PAGE_LIMITS,
                      'offset' => $offset );
    }

    /**
     * The search of the request with the catalogue of this installation: the template fetch
     * fetch( 'content', 'filtered_search', hash( 'offset', ... ) ).
     *
     * @param int $offset
     * @return array see fetch()
     */
    public static function fromCurrentRequest( $offset = 0 )
    {
        // the view parameters the view handed over (useViewParameters()); a query string is never read
        $filters = new self( self::$viewRequest, self::currentEngine(), self::currentCatalog() );
        return $filters->fetch( $offset );
    }

    /**
     * eZ Find when it is the search engine and its fetch functions are there; else the built-in engine.
     *
     * @return string
     */
    public static function currentEngine()
    {
        $ini = eZINI::instance();
        if ( $ini->variable( 'SearchSettings', 'SearchEngine' ) === 'ezsolr' && class_exists( 'ezfModuleFunctionCollection' ) && class_exists( 'eZSolr' ) )
            return self::ENGINE_EZFIND;
        return self::ENGINE_KERNEL;
    }

    /**
     * The catalogue of this installation (classes, groups, sections, languages, limits, resolvers).
     *
     * @return array
     */
    public static function currentCatalog()
    {
        $ini = eZINI::instance();
        // the groups of every class in one query, not one per class
        $classGroups = array();
        foreach ( (array)eZPersistentObject::fetchObjectList( eZContentClassClassGroup::definition(), array( 'contentclass_id', 'group_id' ),
                                                              array( 'contentclass_version' => eZContentClass::VERSION_STATUS_DEFINED ),
                                                              array( 'group_id' => 'asc' ), null, false ) as $row )
            $classGroups[(int)$row['contentclass_id']][] = (int)$row['group_id'];
        $classes = array();
        // without a sort by name: that joins the class names of the current language and leaves out every class
        // without a name in it
        foreach ( eZContentClass::fetchList( eZContentClass::VERSION_STATUS_DEFINED, true ) as $class )
        {
            $classID = (int)$class->attribute( 'id' );
            $name = (string)$class->attribute( 'name' );
            $classes[$classID] = array( 'identifier' => $class->attribute( 'identifier' ),
                                        'name' => $name !== '' ? $name : $class->attribute( 'identifier' ),
                                        'groups' => isset( $classGroups[$classID] ) ? $classGroups[$classID] : array() );
        }
        uasort( $classes, function ( $a, $b ) { return strcasecmp( $a['name'], $b['name'] ); } );
        $groups = array();
        foreach ( eZContentClassGroup::fetchList( false, true ) as $group )
            $groups[(int)$group->attribute( 'id' )] = $group->attribute( 'name' );
        $sections = array();
        foreach ( eZSection::fetchList() as $section )
            $sections[(int)$section->attribute( 'id' )] = $section->attribute( 'name' );
        $languages = array();
        foreach ( eZContentLanguage::fetchList() as $language )
            $languages[$language->attribute( 'locale' )] = $language->attribute( 'name' );

        $extendedFilters = array();
        if ( class_exists( 'eZSolr' ) )
        {
            $findINI = eZINI::instance( 'ezfind.ini' );
            $registered = $findINI->hasVariable( 'ExtendedAttributeFilters', 'FiltersList' ) ? (array)$findINI->variable( 'ExtendedAttributeFilters', 'FiltersList' ) : array();
            foreach ( $registered as $name => $className )
            {
                if ( is_string( $name ) && is_string( $className ) && $className !== '' && class_exists( $className ) )
                    $extendedFilters[] = $name;
            }
        }
        list( $maximumWords, $maximumLength ) = \Exponential\View\Kernel\Content\Search::searchTextLimits( $ini );

        return array( 'classes' => $classes,
                      'class_groups' => $groups,
                      'sections' => $sections,
                      'languages' => $languages,
                      'extended_filters' => $extendedFilters,
                      'default_subtree' => $ini->hasVariable( 'SiteAccessSettings', 'PathPrefix' )
                          ? \Exponential\View\Kernel\Content\Search::pathPrefixSubTreeArray( $ini->variable( 'SiteAccessSettings', 'PathPrefix' ) )
                          : array(),
                      'allow_empty_search' => $ini->variable( 'SearchSettings', 'AllowEmptySearch' ) === 'enabled',
                      'maximum_limit' => (int)$ini->variable( 'SearchSettings', 'MaximumSearchLimit' ),
                      'maximum_words' => $maximumWords,
                      'maximum_length' => $maximumLength,
                      'class_attributes' => function ( $classID ) {
                          $class = eZContentClass::fetch( $classID );
                          $attributes = array();
                          if ( $class instanceof eZContentClass )
                              foreach ( $class->fetchSearchableAttributes() as $attribute )
                                  $attributes[(int)$attribute->attribute( 'id' )] = $attribute->attribute( 'name' );
                          return $attributes;
                      },
                      'owner' => array( __CLASS__, 'resolveOwner' ),
                      'node' => function ( $nodeID ) {
                          $node = eZContentObjectTreeNode::fetch( $nodeID );
                          if ( !$node instanceof eZContentObjectTreeNode )
                              return null;
                          // a node the user may not read keeps its filter (the engine applies the permissions) but
                          // shows only its number: its name and its path would tell what the user may not see
                          if ( !$node->canRead() )
                              return array( 'name' => '#' . (int)$nodeID, 'path' => '' );
                          $names = array();
                          foreach ( $node->attribute( 'path' ) as $pathNode )
                              $names[] = $pathNode->attribute( 'name' );
                          $names[] = $node->attribute( 'name' );
                          // the root node ("Top Level Nodes") is no part of a path a person reads
                          if ( count( $names ) > 1 && $node->attribute( 'depth' ) > 1 )
                              array_shift( $names );
                          return array( 'name' => $node->attribute( 'name' ), 'path' => implode( ' / ', $names ) );
                      } );
    }

    /**
     * A user by object id, login or e-mail address, if the current user may read that user's content object:
     * otherwise the field would tell any searcher which name belongs to an e-mail address.
     *
     * @param string $input
     * @return array|null 'id' (the user's content object id), 'name'
     */
    public static function resolveOwner( $input )
    {
        $input = trim( (string)$input );
        if ( $input === '' )
            return null;
        $user = null;
        if ( ctype_digit( $input ) )
            $user = eZUser::fetch( (int)$input );
        if ( !$user instanceof eZUser )
            $user = eZUser::fetchByName( $input );
        if ( !$user instanceof eZUser && strpos( $input, '@' ) !== false )
            $user = eZUser::fetchByEmail( $input );
        if ( !$user instanceof eZUser )
            return null;
        $object = $user->attribute( 'contentobject' );
        if ( !$object instanceof eZContentObject || !$object->canRead() )
            return null;
        return array( 'id' => (int)$user->attribute( 'contentobject_id' ),
                      'name' => $object instanceof eZContentObject ? $object->attribute( 'name' ) : $user->attribute( 'login' ) );
    }

    // ---- internals -----------------------------------------------------------------------------------------

    private function normalize( array $r, $now )
    {
        $text = isset( $r['SearchText'] ) && is_string( $r['SearchText'] ) ? $r['SearchText'] : '';
        $limited = \Exponential\View\Kernel\Content\Search::limitSearchText( $text, $this->catalog['maximum_words'], $this->catalog['maximum_length'] );

        $mode = isset( $r['SearchMode'] ) && is_string( $r['SearchMode'] ) ? $r['SearchMode'] : '';
        if ( !in_array( $mode, $this->capabilities['modes'], true ) )
            $mode = $this->capabilities['modes'][0];

        $classIDs = array();
        foreach ( self::listValues( $r, 'ClassID' ) as $value )
        {
            $id = $this->classID( $value );
            if ( $id && !in_array( $id, $classIDs, true ) )
                $classIDs[] = $id;
        }

        $sectionIDs = array();
        foreach ( self::listValues( $r, 'SectionID' ) as $value )
        {
            $id = is_scalar( $value ) && ctype_digit( trim( (string)$value ) ) ? (int)$value : 0;
            if ( $id > 0 && isset( $this->catalog['sections'][$id] ) && !in_array( $id, $sectionIDs, true ) )
                $sectionIDs[] = $id;
        }

        $subtreeIDs = array();
        foreach ( self::listValues( $r, 'SubTreeArray' ) as $value )
        {
            foreach ( explode( ',', is_scalar( $value ) ? (string)$value : '' ) as $part )
            {
                $id = ctype_digit( trim( $part ) ) ? (int)$part : 0;
                if ( $id > 0 && !in_array( $id, $subtreeIDs, true ) )
                    $subtreeIDs[] = $id;
            }
        }
        // All content is no limit (the root node, as the search form's "All content" sends it)
        if ( $subtreeIDs === array( 1 ) )
            $subtreeIDs = array();

        $parameters = array( 'text' => $limited,
                             'text_shortened' => $limited !== $text,
                             'mode' => $mode,
                             'class_ids' => $classIDs,
                             'class_attribute_id' => 0,
                             'section_ids' => $sectionIDs,
                             'subtree_ids' => $subtreeIDs,
                             'modified' => $this->capabilities['modified'] ? $this->range( $r, 'Modified', $now ) : self::emptyRange(),
                             'published' => $this->range( $r, 'Published', $now ),
                             'owner' => array( 'input' => '', 'id' => 0, 'name' => '' ),
                             'language' => '',
                             'sort' => '',
                             'page_limit' => null,
                             'limit' => 10 );
        $this->parameters = $parameters;

        // the class attribute of the one class chosen
        if ( count( $classIDs ) === 1 && isset( $r['ClassAttributeID'] ) && is_scalar( $r['ClassAttributeID'] ) )
        {
            $attributeID = (int)$r['ClassAttributeID'];
            if ( $attributeID > 0 && array_key_exists( $attributeID, $this->classAttributes() ) )
                $parameters['class_attribute_id'] = $attributeID;
        }

        if ( $this->capabilities['owner'] && isset( $r['Owner'] ) && is_string( $r['Owner'] ) && trim( $r['Owner'] ) !== '' )
        {
            $parameters['owner']['input'] = trim( $r['Owner'] );
            $owner = is_callable( $this->catalog['owner'] ) ? call_user_func( $this->catalog['owner'], $parameters['owner']['input'] ) : null;
            if ( is_array( $owner ) && !empty( $owner['id'] ) )
            {
                $parameters['owner']['id'] = (int)$owner['id'];
                $parameters['owner']['name'] = isset( $owner['name'] ) ? (string)$owner['name'] : (string)$owner['id'];
            }
            else
            {
                $this->rejected[] = 'Owner';
            }
        }

        if ( $this->capabilities['language'] && isset( $r['Language'] ) && is_string( $r['Language'] ) && isset( $this->catalog['languages'][$r['Language']] ) )
            $parameters['language'] = $r['Language'];

        $sort = isset( $r['SortBy'] ) && is_string( $r['SortBy'] ) ? $r['SortBy'] : '';
        // without (or with an unknown) SortBy: relevance with eZ Find, the newest change first with the built-in
        // engine, as the search always listed it
        $parameters['sort'] = in_array( $sort, $this->capabilities['sorts'], true ) ? $sort : $this->defaultSort();

        $limit = 10;
        if ( isset( $r['SearchPageLimit'] ) && is_scalar( $r['SearchPageLimit'] ) && isset( self::PAGE_LIMITS[(int)$r['SearchPageLimit']] ) )
        {
            $parameters['page_limit'] = (int)$r['SearchPageLimit'];
            $limit = self::PAGE_LIMITS[$parameters['page_limit']];
        }
        elseif ( isset( $r['BrowsePageLimit'] ) && is_scalar( $r['BrowsePageLimit'] ) && (int)$r['BrowsePageLimit'] > 0 )
        {
            $limit = (int)$r['BrowsePageLimit'];
        }
        $maximum = (int)$this->catalog['maximum_limit'];
        $parameters['limit'] = $maximum > 0 ? min( $limit, $maximum ) : $limit;
        // the page size as a choice of the filter bar: the one of the request, else the one nearest to the limit
        if ( $parameters['page_limit'] === null )
            $parameters['page_limit'] = self::nearestPageLimit( $parameters['limit'] );

        return $parameters;
    }

    private static function nearestPageLimit( $limit )
    {
        $best = 2;
        foreach ( self::PAGE_LIMITS as $index => $size )
        {
            if ( $size <= $limit )
                $best = $index;
        }
        return $best;
    }

    private function classID( $value )
    {
        if ( !is_scalar( $value ) )
            return 0;
        $value = trim( (string)$value );
        if ( ctype_digit( $value ) )
            return isset( $this->catalog['classes'][(int)$value] ) ? (int)$value : 0;
        foreach ( $this->catalog['classes'] as $id => $class )
        {
            if ( $class['identifier'] === $value )
                return (int)$id;
        }
        return 0;
    }

    private static function listValues( array $r, $name )
    {
        if ( !isset( $r[$name] ) )
            return array();
        $values = is_array( $r[$name] ) ? $r[$name] : array( $r[$name] );
        return array_values( array_filter( $values, 'is_scalar' ) );
    }

    private static function emptyRange()
    {
        return array( 'period' => '', 'from' => '', 'to' => '', 'from_time' => null, 'to_time' => null );
    }

    private function range( array $r, $name, $now )
    {
        $range = self::emptyRange();
        $period = isset( $r[$name] ) && is_string( $r[$name] ) ? $r[$name] : '';
        if ( !in_array( $period, self::PERIODS, true ) )
            return $range;
        $range['period'] = $period;
        if ( $period !== 'custom' )
        {
            $back = array( 'day' => '-1 day', 'week' => '-7 days', 'month' => '-1 month', 'year' => '-1 year' );
            // from the start of that day: the same search gives the same hits all day long
            $range['from_time'] = strtotime( date( 'Y-m-d', strtotime( $back[$period], $now ) ) . ' 00:00:00' );
            $range['from'] = date( 'Y-m-d', $range['from_time'] );
            return $range;
        }
        foreach ( array( 'from' => 'From', 'to' => 'To' ) as $key => $suffix )
        {
            $value = isset( $r[$name . $suffix] ) && is_string( $r[$name . $suffix] ) ? trim( $r[$name . $suffix] ) : '';
            if ( $value === '' )
                continue;
            $date = DateTime::createFromFormat( '!Y-m-d', $value );
            if ( !$date || $date->format( 'Y-m-d' ) !== $value )
            {
                $this->rejected[] = $name . $suffix;
                continue;
            }
            $range[$key] = $value;
            $range[$key . '_time'] = $key === 'from' ? $date->getTimestamp() : $date->getTimestamp() + 86399;
        }
        // from after to: swapped, as a person meant it
        if ( $range['from_time'] !== null && $range['to_time'] !== null && $range['from_time'] > $range['to_time'] )
        {
            list( $range['from'], $range['to'] ) = array( $range['to'], $range['from'] );
            $range['from_time'] = strtotime( $range['from'] . ' 00:00:00' );
            $range['to_time'] = strtotime( $range['to'] . ' 00:00:00' ) + 86399;
        }
        if ( $range['from_time'] === null && $range['to_time'] === null )
            $range['period'] = '';
        return $range;
    }

    private function node( $nodeID )
    {
        return is_callable( $this->catalog['node'] ) ? call_user_func( $this->catalog['node'], $nodeID ) : null;
    }

    private function requestValues()
    {
        $p = $this->parameters;
        $pairs = array();
        if ( $p['text'] !== '' )
            $pairs['SearchText'] = $p['text'];
        if ( $p['mode'] !== $this->capabilities['modes'][0] )
            $pairs['SearchMode'] = $p['mode'];
        if ( $p['class_ids'] )
            $pairs['ClassID'] = array_map( 'strval', $p['class_ids'] );
        if ( $p['class_attribute_id'] )
            $pairs['ClassAttributeID'] = (string)$p['class_attribute_id'];
        if ( $p['section_ids'] )
            $pairs['SectionID'] = array_map( 'strval', $p['section_ids'] );
        foreach ( array( 'modified' => 'Modified', 'published' => 'Published' ) as $field => $name )
        {
            if ( $p[$field]['period'] === '' )
                continue;
            $pairs[$name] = $p[$field]['period'];
            if ( $p[$field]['period'] === 'custom' )
            {
                if ( $p[$field]['from'] !== '' )
                    $pairs[$name . 'From'] = $p[$field]['from'];
                if ( $p[$field]['to'] !== '' )
                    $pairs[$name . 'To'] = $p[$field]['to'];
            }
        }
        if ( $p['owner']['id'] )
            $pairs['Owner'] = $p['owner']['input'];
        if ( $p['language'] !== '' )
            $pairs['Language'] = $p['language'];
        if ( $p['subtree_ids'] )
            $pairs['SubTreeArray'] = array_map( 'strval', $p['subtree_ids'] );
        if ( $p['sort'] !== $this->defaultSort() )
            $pairs['SortBy'] = $p['sort'];
        if ( $p['page_limit'] !== 2 )
            $pairs['SearchPageLimit'] = (string)$p['page_limit'];
        return $pairs;
    }

    /** @return string the sort order without SortBy */
    public function defaultSort()
    {
        return $this->engine === self::ENGINE_KERNEL ? 'modified_desc' : 'relevance';
    }

    private static function ezfindSort( $sort )
    {
        list( $field, $ascending ) = self::SORTS[$sort];
        return array( $field => $ascending ? 'asc' : 'desc' );
    }

    private static function solrDate( $timestamp )
    {
        return $timestamp === null ? '*' : gmdate( 'Y-m-d\TH:i:s\Z', (int)$timestamp );
    }
}
