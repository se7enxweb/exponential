<?php
/**
 * expHelpSearch: the full-text search of the help system (doc/features/6.0/help-system.md, "Search").
 *
 * The index is built from the documents expHelpDocumentIndex lists and kept as a PHP file in the help cache
 * (var/<site>/cache/exphelp/search-<key>.php). OPcache keeps that file in shared memory, so a query costs a few
 * array lookups plus reading the sources of the hits it shows. The key follows the document index's signature
 * (git HEAD, file times, settings) and the search settings, so a change to either builds a new index.
 *
 * Ranking: BM25 over weighted fields (title, headings, path, keywords, text: [SearchSettings] TitleBoost,
 * HeadingBoost, PathBoost, KeywordBoost, BodyBoost), times a boost per type (TypeBoost[]: help pages and guides
 * rank higher, specifications, changelogs and history lower), times VersionBoost[] for pages below the version
 * directory of an older major version (features/3.8/), times TitleMatchBoost when every query word is in the
 * title. Every word must match; when no page has all of them the search relaxes to any word and says so
 * ('relaxed'). The last word also matches as a prefix (search while typing) unless the query ends with a space;
 * "quoted words" are marked together; -word excludes.
 * A hit carries its best matching section (heading and anchor) and a snippet with the query words marked, as
 * escaped HTML ('snippet') and as parts ('snippet_parts': list of array( text, isMatch )) for clients that build
 * the DOM with text nodes.
 *
 * search() result: array( 'query', 'terms', 'total', 'offset', 'limit', 'took_ms', 'relaxed',
 *   'hits' (also as 'results', the same list) => list of array( id, path, title, url, href, section, anchor, snippet,
 *                            snippet_parts, audience[], category, type, score ),
 *   'facets' => array( 'audience' => value => count, 'category' => ..., 'type' => ... ), 'suggest' => words )
 * A facet is counted with the other facets' filters applied, not its own, so a client can show what each choice
 * would give. A query without words gives no hits and the facets of the whole (filtered) set.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpSearch
{
    /** Version of the index file format; a change builds a new index. */
    const FORMAT = 2;

    const DEFAULT_LIMIT = 20;
    const MAX_LIMIT = 50;
    const MAX_QUERY_LENGTH = 200;
    const MAX_TERMS = 12;
    const PREFIX_EXPANSIONS = 40;
    const SNIPPET_LENGTH = 220;

    /** BM25 parameters. */
    const K1 = 1.2;
    const B = 0.6;

    /** @var expHelpDocumentIndex|null the documents, unless given directly */
    protected $documentIndex = null;

    /** @var array|null the loaded search index */
    protected $index = null;

    /** @var array fieldWeights, typeBoost, titleBoost, stopWords, cacheDir */
    protected $settings;

    /** @var callable|null fn( string $id ): ?string, the source of a document when given directly */
    protected $sourceReader = null;

    /** @var array|null the documents, when given directly (tests, the CLI) */
    protected $documents = null;

    /** @var string|null signature of the documents given directly */
    protected $signature = null;

    protected static $defaultStopWords = array(
        'a', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'for', 'from', 'has', 'have', 'how', 'i', 'in', 'is', 'it',
        'its', 'of', 'on', 'or', 'that', 'the', 'this', 'to', 'was', 'were', 'what', 'when', 'where', 'which', 'with',
        'you', 'your', 'can', 'do', 'does', 'not', 'will', 'if', 'into', 'than', 'then', 'there', 'these', 'they',
        'der', 'die', 'das', 'und', 'ist', 'ein', 'eine', 'den', 'mit', 'von', 'zu', 'im', 'auf', 'für', 'nicht',
    );

    /**
     * @param expHelpDocumentIndex|null $index the documents (default: expHelpDocumentIndex::instance())
     * @param array $settings overrides of the settings from exphelp.ini: fieldWeights (title, headings, path,
     *        keywords, body => float), typeBoost (type => float), titleBoost, stopWords, cacheDir (null: no file)
     */
    public function __construct( ?expHelpDocumentIndex $index = null, array $settings = array() )
    {
        $this->documentIndex = $index;
        $this->settings = $settings + self::settingsFromIni();
        if ( !array_key_exists( 'cacheDir', $settings ) )
            $this->settings['cacheDir'] = $this->documents()->cacheDir();
    }

    /**
     * The search over a fixed set of documents instead of expHelpDocumentIndex (tests, the CLI). No cache file
     * unless $settings['cacheDir'] names one.
     *
     * @param array $documents id => array( title, headings, audience, category, type, keywords, text )
     *                         ('text' is the source; 'headings' a list of array( level, text, anchor ))
     */
    public static function fromDocuments( array $documents, array $settings = array() )
    {
        $search = new self( null, $settings + array( 'cacheDir' => null ) );
        $search->documents = $documents;
        $search->signature = md5( serialize( $documents ) );
        $search->sourceReader = function ( $id ) use ( $documents )
        {
            return isset( $documents[$id]['text'] ) ? (string)$documents[$id]['text'] : null;
        };
        return $search;
    }

    /** @return expHelpDocumentIndex */
    protected function documents()
    {
        if ( $this->documentIndex === null )
            $this->documentIndex = expHelpDocumentIndex::instance();
        return $this->documentIndex;
    }

    /**
     * The settings of [SearchSettings] in exphelp.ini, with safe defaults for anything missing or out of range.
     */
    public static function settingsFromIni()
    {
        $settings = array(
            'fieldWeights' => array( 'title' => 8.0, 'headings' => 3.0, 'path' => 2.0, 'keywords' => 4.0, 'body' => 1.0 ),
            'typeBoost' => array( 'help' => 1.5, 'guide' => 1.3, 'specification' => 0.8, 'changelog' => 0.45, 'history' => 0.6 ),
            // pages below a version directory (features/3.8/, bc/4.1/): the older the major version, the lower
            'versionBoost' => array( '3' => 0.5, '4' => 0.6, '5' => 0.75 ),
            'titleBoost' => 1.4,
            'stopWords' => self::$defaultStopWords,
            'cacheDir' => null,
        );
        if ( !class_exists( 'eZINI' ) || !eZINI::exists( 'exphelp.ini' ) )
            return $settings;
        $ini = eZINI::instance( 'exphelp.ini' );
        $number = function ( $name ) use ( $ini )
        {
            if ( !$ini->hasVariable( 'SearchSettings', $name ) )
                return null;
            $value = $ini->variable( 'SearchSettings', $name );
            return is_numeric( $value ) && $value >= 0 && $value <= 100 ? (float)$value : null;
        };
        foreach ( array( 'TitleBoost' => 'title', 'HeadingBoost' => 'headings', 'PathBoost' => 'path',
                         'KeywordBoost' => 'keywords', 'BodyBoost' => 'body' ) as $name => $field )
        {
            $value = $number( $name );
            if ( $value !== null )
                $settings['fieldWeights'][$field] = $value;
        }
        $value = $number( 'TitleMatchBoost' );
        if ( $value !== null )
            $settings['titleBoost'] = $value;
        foreach ( array( 'TypeBoost' => 'typeBoost', 'VersionBoost' => 'versionBoost' ) as $name => $key )
        {
            if ( !$ini->hasVariable( 'SearchSettings', $name ) )
                continue;
            foreach ( (array)$ini->variable( 'SearchSettings', $name ) as $what => $boost )
            {
                if ( ( is_string( $what ) || is_int( $what ) ) && (string)$what !== '' && is_numeric( $boost ) && $boost >= 0 && $boost <= 100 )
                    $settings[$key][(string)$what] = (float)$boost;
            }
        }
        if ( $ini->hasVariable( 'SearchSettings', 'StopWords' ) )
        {
            $words = array_filter( array_map( 'trim', (array)$ini->variable( 'SearchSettings', 'StopWords' ) ) );
            if ( $words )
                $settings['stopWords'] = array_values( array_map( array( __CLASS__, 'lower' ), $words ) );
        }
        return $settings;
    }


    // ------------------------------------------------------------------ query

    /**
     * Searches.
     *
     * @param string $query the text typed; "quoted words" are kept together in the snippet, -word excludes
     * @param array $filters audience (string|list), category (string|list), type (string|list)
     * @return array query, terms, total, offset, limit, took_ms, relaxed, results, facets, suggest
     */
    public function search( $query, array $filters = array(), $limit = self::DEFAULT_LIMIT, $offset = 0 )
    {
        $start = microtime( true );
        $limit = max( 1, min( self::MAX_LIMIT, (int)$limit ) );
        $offset = max( 0, min( 10000, (int)$offset ) );
        $query = self::cleanQuery( $query );
        $filters = self::cleanFilters( $filters );
        $index = $this->index();

        $parsed = $this->parseQuery( $query );
        $result = array(
            'query' => $query, 'terms' => $parsed['terms'], 'total' => 0, 'offset' => $offset, 'limit' => $limit,
            'took_ms' => 0.0, 'relaxed' => false, 'hits' => array(), 'results' => array(), 'facets' => array(), 'suggest' => array(),
        );

        if ( !$parsed['terms'] )
        {
            // no terms: the facets of the whole set (or of the filtered set), no results
            $all = array_fill_keys( array_keys( $index['docs'] ), 0.0 );
            $result['facets'] = $this->facets( $all, $filters );
            $result['took_ms'] = round( ( microtime( true ) - $start ) * 1000, 2 );
            return $result;
        }

        $expansions = $this->expandTerms( $parsed, $index );
        $scores = $this->score( $expansions, $index, true );
        if ( !$scores && count( $expansions ) > 1 )
        {
            $scores = $this->score( $expansions, $index, false );
            $result['relaxed'] = (bool)$scores;
        }
        foreach ( $parsed['exclude'] as $term )
        {
            if ( isset( $index['terms'][$term] ) )
            {
                foreach ( self::postings( $index['terms'][$term] ) as $doc => $w )
                    unset( $scores[$doc] );
            }
        }
        foreach ( $scores as $doc => $score )
        {
            $type = $index['docs'][$doc][4];
            if ( isset( $this->settings['typeBoost'][$type] ) )
                $score *= $this->settings['typeBoost'][$type];
            $major = $index['docs'][$doc][6];
            if ( $major !== '' && isset( $this->settings['versionBoost'][$major] ) )
                $score *= $this->settings['versionBoost'][$major];
            // every group matches a word of the title: the page is about the query
            $titleWords = array();
            foreach ( self::tokenize( $index['docs'][$doc][1] ) as $word )
                $titleWords[$word] = $titleWords[self::stem( $word )] = true;
            $all = true;
            foreach ( $expansions as $group )
            {
                if ( !array_intersect_key( $group, $titleWords ) )
                {
                    $all = false;
                    break;
                }
            }
            $scores[$doc] = $all ? $score * $this->settings['titleBoost'] : $score;
        }

        $result['facets'] = $this->facets( $scores, $filters );
        $scores = $this->applyFilters( $scores, $filters, $index );
        arsort( $scores );
        $result['total'] = count( $scores );

        $marks = array();
        foreach ( $expansions as $group )
            $marks = array_merge( $marks, array_keys( $group ) );
        foreach ( array_slice( $scores, $offset, $limit, true ) as $doc => $score )
            $result['hits'][] = $this->hit( $doc, $score, $marks, $parsed['phrases'], $index );
        $result['results'] = $result['hits'];

        if ( $parsed['prefix'] !== null )
            $result['suggest'] = array_slice( array_keys( end( $expansions ) ), 0, 8 );
        $result['took_ms'] = round( ( microtime( true ) - $start ) * 1000, 2 );
        return $result;
    }

    /**
     * Terms of the query: list of groups, each term => weight (the exact term 1.0, prefix expansions less).
     */
    protected function expandTerms( array $parsed, array $index )
    {
        $groups = array();
        $count = count( $parsed['terms'] );
        foreach ( $parsed['terms'] as $i => $term )
        {
            $group = array();
            if ( isset( $index['terms'][$term] ) )
                $group[$term] = 1.0;
            $stem = self::stem( $term );
            if ( $stem !== $term && isset( $index['terms'][$stem] ) )
                $group[$stem] = 0.9;
            if ( $i === $count - 1 && $parsed['prefix'] !== null )
            {
                foreach ( $this->prefixTerms( $term, $index['vocabulary'] ) as $candidate )
                {
                    if ( !isset( $group[$candidate] ) )
                        $group[$candidate] = 0.6;
                }
            }
            $groups[] = $group;
        }
        return $groups;
    }

    /**
     * Scores per document: sum over groups of the best weighted posting; $all requires every group to match.
     */
    protected function score( array $groups, array $index, $all )
    {
        $total = count( $index['docs'] );
        $scores = null;
        // rarest group first, so the AND intersection shrinks early
        $sized = array();
        foreach ( $groups as $g => $group )
        {
            $df = 0;
            foreach ( $group as $term => $w )
                $df += self::df( $index['terms'][$term] );
            $sized[$g] = $df;
        }
        asort( $sized );
        foreach ( array_keys( $sized ) as $g )
        {
            $groupScores = array();
            foreach ( $groups[$g] as $term => $weight )
            {
                $entry = $index['terms'][$term];
                $df = self::df( $entry );
                $idf = log( 1 + ( $total - $df + 0.5 ) / ( $df + 0.5 ) );
                foreach ( self::postings( $entry, $all && $scores !== null ? $scores : null ) as $doc => $tf )
                {
                    $s = $idf * $tf * $weight;
                    if ( !isset( $groupScores[$doc] ) || $groupScores[$doc] < $s )
                        $groupScores[$doc] = $s;
                }
            }
            if ( $scores === null )
                $scores = $groupScores;
            elseif ( $all )
            {
                $next = array();
                foreach ( $groupScores as $doc => $s )
                {
                    if ( isset( $scores[$doc] ) )
                        $next[$doc] = $scores[$doc] + $s;
                }
                $scores = $next;
            }
            else
            {
                foreach ( $groupScores as $doc => $s )
                    $scores[$doc] = ( isset( $scores[$doc] ) ? $scores[$doc] : 0.0 ) + $s;
            }
            if ( $all && !$scores )
                return array();
        }
        return $scores ? $scores : array();
    }

    protected function applyFilters( array $scores, array $filters, array $index, $skip = null )
    {
        foreach ( $filters as $facet => $values )
        {
            if ( $facet === $skip || !$values )
                continue;
            $wanted = array_flip( $values );
            foreach ( $scores as $doc => $s )
            {
                if ( !array_intersect_key( $wanted, array_flip( self::facetValues( $index['docs'][$doc], $facet ) ) ) )
                    unset( $scores[$doc] );
            }
        }
        return $scores;
    }

    /**
     * Counts per facet value; each facet is counted with the other facets' filters applied, not its own, so the
     * client can show how many results each choice would give.
     */
    protected function facets( array $scores, array $filters )
    {
        $index = $this->index();
        $facets = array();
        foreach ( array( 'audience', 'category', 'type' ) as $facet )
        {
            $counts = array();
            foreach ( $this->applyFilters( $scores, $filters, $index, $facet ) as $doc => $s )
            {
                foreach ( self::facetValues( $index['docs'][$doc], $facet ) as $value )
                    $counts[$value] = ( isset( $counts[$value] ) ? $counts[$value] : 0 ) + 1;
            }
            arsort( $counts );
            $facets[$facet] = $counts;
        }
        return $facets;
    }

    protected static function facetValues( array $doc, $facet )
    {
        switch ( $facet )
        {
            case 'audience':
                return $doc[2];
            case 'category':
                return $doc[3] !== '' ? array( $doc[3] ) : array();
            case 'type':
                return $doc[4] !== '' ? array( $doc[4] ) : array();
        }
        return array();
    }

    /**
     * One result: the document, its best section (heading, anchor) and a snippet.
     */
    protected function hit( $doc, $score, array $marks, array $phrases, array $index )
    {
        list( $id, $title, $audience, $category, $type, $path ) = $index['docs'][$doc];
        $section = null;
        $anchor = null;
        $parts = array();
        $source = $this->source( $id );
        if ( $source !== null )
        {
            $sections = self::sections( $source, false, self::isMarkdown( $id ) );
            $best = $this->bestSection( $sections, $marks );
            if ( $best !== null )
            {
                $section = $sections[$best]['heading'];
                $anchor = $sections[$best]['anchor'];
                $text = self::plain( $sections[$best]['text'] );
                $parts = self::snippetParts( $text, $marks, $phrases );
                // a section without a match in its text (the title or a heading matched): its opening words
                if ( !$parts && $text === '' && isset( $sections[$best + 1] ) )
                    $parts = self::snippetParts( self::plain( $sections[$best + 1]['text'] ), $marks, $phrases );
            }
        }
        return array(
            'id' => $id,
            'path' => $path,
            'title' => $title,
            'section' => $section,
            'anchor' => $anchor,
            'url' => self::viewerUrl( $id, (string)$anchor ),
            'href' => self::absoluteUrl( self::viewerUrl( $id, (string)$anchor ) ),
            'snippet' => self::snippetHtml( $parts ),
            'snippet_parts' => $parts,
            'audience' => $audience,
            'category' => $category,
            'type' => $type,
            'score' => round( $score, 3 ),
        );
    }

    /** The source of a document; only what the document index allows (it returns null for anything else). */
    protected function source( $id )
    {
        $text = $this->sourceReader ? call_user_func( $this->sourceReader, $id ) : $this->documents()->source( $id );
        return is_string( $text ) ? $text : null;
    }

    /** The module-relative viewer address of a page (and heading). */
    protected static function viewerUrl( $id, $anchor = '' )
    {
        if ( class_exists( 'expHelp' ) )
            return expHelp::url( 'viewer', $id, $anchor );
        return '/exphelp/viewer/' . implode( '/', array_map( 'rawurlencode', explode( '/', $id ) ) ) . ( $anchor !== '' ? '#' . rawurlencode( $anchor ) : '' );
    }

    /** The address with the siteaccess prefix, for the client (outside a web request: unchanged). */
    protected static function absoluteUrl( $url )
    {
        if ( class_exists( 'expHelpLinks' ) && class_exists( 'eZURI' ) && class_exists( 'eZSys' ) && !eZSys::isShellExecution() )
            return expHelpLinks::absolute( $url );
        return $url;
    }

    /**
     * The key of the section that matches the most query terms (a match in the heading counts more, repeated
     * matches a little); the first section (the introduction) wins ties. Counted on the lower-cased source,
     * which is fast enough for documents of hundreds of kilobytes.
     */
    protected function bestSection( array $sections, array $marks )
    {
        $best = null;
        $bestScore = -1.0;
        foreach ( $sections as $i => $section )
        {
            $score = 0.0;
            $heading = $section['heading'] !== null ? self::lower( $section['heading'] ) : '';
            $text = strtolower( $section['text'] );
            foreach ( $marks as $mark )
            {
                if ( $heading !== '' && strpos( $heading, $mark ) !== false )
                    $score += 4;
                $count = substr_count( $text, $mark );
                if ( $count )
                    $score += 2 + min( 5, $count ) * 0.2;
            }
            if ( $score > $bestScore )
            {
                $best = $i;
                $bestScore = $score;
            }
        }
        return $best;
    }

    // ------------------------------------------------------------------ text

    /**
     * The sections of a Markdown or text source: list of array( heading|null, anchor|null, text ). With $plain
     * the text is plain text (Markdown syntax removed, white space collapsed), else the Markdown of the section.
     *
     * Headings are found as expHelpDocumentIndex::headings() finds them (ATX "# ..." and setext "Title\n====",
     * outside fenced code, a heading without text is no heading) and get the same text and anchor, so a hit links
     * to the id the viewer gives the heading. A source that is not Markdown ($markdown false) is one section.
     */
    public static function sections( $source, $plain = true, $markdown = true )
    {
        $source = str_replace( array( "\r\n", "\r" ), "\n", (string)$source );
        $source = self::stripFrontMatter( $source );
        if ( !$markdown )
        {
            $text = $plain ? self::plain( $source ) : $source;
            return trim( $text ) === '' ? array() : array( array( 'heading' => null, 'anchor' => null, 'text' => $text ) );
        }
        $sections = array();
        $current = array( 'heading' => null, 'anchor' => null, 'lines' => array() );
        $fence = null;
        $slugs = array();
        $previousIsText = false;
        foreach ( explode( "\n", $source ) as $line )
        {
            // only lines that start with #, `, ~, = or - (after up to three spaces) can change the structure
            $first = ltrim( substr( $line, 0, 4 ), ' ' );
            $c = $first !== '' ? $first[0] : '';
            if ( $c !== '#' && $c !== '`' && $c !== '~' && $c !== '=' && $c !== '-' )
            {
                $current['lines'][] = $line;
                $previousIsText = $fence === null;
                continue;
            }
            if ( $fence !== null )
            {
                if ( preg_match( '/^ {0,3}(`{3,}|~{3,})\s*$/', $line, $m ) && $m[1][0] === $fence[0] && strlen( $m[1] ) >= strlen( $fence ) )
                    $fence = null;
                $current['lines'][] = $line;
                $previousIsText = false;
                continue;
            }
            if ( preg_match( '/^ {0,3}(`{3,}|~{3,})/', $line, $m ) )
            {
                $fence = $m[1];
                $current['lines'][] = $line;
                $previousIsText = false;
                continue;
            }
            if ( preg_match( '/^ {0,3}(#{1,6})(?:[ \t]+(.*?))?(?:[ \t]+#+)?[ \t]*$/', $line, $m ) )
            {
                $heading = self::headingText( isset( $m[2] ) ? $m[2] : '' );
                $previousIsText = false;
                if ( $heading !== '' )
                {
                    $sections[] = $current;
                    $current = array( 'heading' => $heading, 'anchor' => self::anchor( $heading, $slugs ), 'lines' => array() );
                }
                continue;
            }
            if ( $previousIsText && $current['lines'] && preg_match( '/^ {0,3}(=+|-+)[ \t]*$/', $line ) )
            {
                $previous = (string)end( $current['lines'] );
                // a setext underline follows a line that is not a list item, quote, table or indented code
                if ( trim( $previous ) !== '' && !preg_match( '/^( {4}|\t| {0,3}([-*+>|]|\d+[.)])(\s|$))/', $previous ) )
                {
                    $heading = self::headingText( $previous );
                    $previousIsText = false;
                    if ( $heading !== '' )
                    {
                        array_pop( $current['lines'] );
                        $sections[] = $current;
                        $current = array( 'heading' => $heading, 'anchor' => self::anchor( $heading, $slugs ), 'lines' => array() );
                    }
                    continue;
                }
            }
            $current['lines'][] = $line;
            $previousIsText = true;
        }
        $sections[] = $current;
        $out = array();
        foreach ( $sections as $section )
        {
            $text = implode( "\n", $section['lines'] );
            if ( $plain )
                $text = self::plain( $text );
            if ( trim( $text ) === '' && $section['heading'] === null )
                continue;
            $out[] = array( 'heading' => $section['heading'], 'anchor' => $section['anchor'], 'text' => $text );
        }
        return $out;
    }

    /** Whether a page is Markdown (rendered with heading anchors) rather than plain text, by its suffix. */
    public static function isMarkdown( $id )
    {
        return (bool)preg_match( '/\.(?:md|markdown)$/i', (string)$id );
    }

    /** Markdown to one line of plain text. */
    public static function plain( $markdown )
    {
        $markdown = preg_replace( '/^\s{0,3}(?:`{3,}|~{3,}).*$/m', ' ', $markdown ); // fence lines, not their code
        return trim( preg_replace( '/\s+/u', ' ', self::inlineText( $markdown ) ) );
    }

    /** The text of a heading, as the document index gives it. */
    protected static function headingText( $markdown )
    {
        if ( is_callable( array( 'expHelpDocumentIndex', 'inlineText' ) ) )
            return trim( (string)expHelpDocumentIndex::inlineText( $markdown ) );
        return trim( preg_replace( '/\s+/u', ' ', self::inlineText( $markdown ) ) );
    }

    /**
     * The anchor of a heading, unique in the document ($used): the document index's (which is the renderer's),
     * else the GitHub rule.
     */
    public static function anchor( $heading, array &$used )
    {
        if ( is_callable( array( 'expHelpDocumentIndex', 'slug' ) ) )
            return (string)expHelpDocumentIndex::slug( $heading, $used );
        if ( is_callable( array( 'expHelpMarkdownRenderer', 'slug' ) ) )
            return (string)expHelpMarkdownRenderer::slug( $heading, $used );
        $slug = self::lower( $heading );
        $slug = preg_replace( '/[^\p{L}\p{N}\p{M} _-]+/u', '', $slug );
        $slug = str_replace( ' ', '-', (string)$slug );
        if ( $slug === '' )
            $slug = 'section';
        $base = $slug;
        $n = 0;
        while ( isset( $used[$slug] ) )
            $slug = $base . '-' . ( ++$n );
        $used[$slug] = true;
        return $slug;
    }

    protected static function stripFrontMatter( $source )
    {
        if ( strncmp( $source, "---\n", 4 ) === 0 )
        {
            $end = strpos( $source, "\n---", 4 );
            if ( $end !== false && $end < 4000 )
            {
                $after = strpos( $source, "\n", $end + 4 );
                return $after === false ? '' : (string)substr( $source, $after + 1 );
            }
        }
        return $source;
    }

    /** Markdown inline syntax and HTML tags removed: the words a reader sees. */
    public static function inlineText( $text )
    {
        $text = preg_replace( '/<!--.*?-->/s', ' ', $text );
        $text = preg_replace( '/!\[([^\]]*)\]\([^)]*\)/', '$1', $text );           // images: alt text
        $text = preg_replace( '/\[([^\]]+)\]\([^)]*\)/', '$1', $text );             // links: their text
        $text = preg_replace( '/\[([^\]]+)\]\[[^\]]*\]/', '$1', $text );            // reference links
        $text = preg_replace( '/^\s{0,3}\[[^\]]+\]:\s+\S+.*$/m', ' ', $text );      // link definitions
        // layout tags Markdown files use (a, img, br, details ...); anything else is text the reader sees, escaped later
        $text = preg_replace( '/<\/?(?:a|abbr|b|br|center|code|dd|del|details|div|dl|dt|em|h[1-6]|hr|i|img|ins|kbd|li|ol|p|picture|pre|s|small|source|span|strong|sub|summary|sup|table|tbody|td|th|thead|tr|u|ul)(?:\s[^<>]*)?\/?>/i', ' ', $text );
        $text = preg_replace( '/^\s{0,3}(?:>\s?)+/m', '', $text );                  // quotes
        $text = preg_replace( '/^\s*(?:[-*+]|\d+[.)])\s+/m', '', $text );           // list markers
        $text = preg_replace( '/^\s*\|?(?:\s*:?-{2,}:?\s*\|)+\s*:?-*:?\s*$/m', ' ', $text ); // table rules
        $text = str_replace( '|', ' ', $text );
        $text = preg_replace( '/(\*\*|__|~~)(?=\S)([^\n]{1,200}?)(?<=\S)\1/', '$2', $text );
        $text = preg_replace( '/(?<![\w*])([*_])(?=\S)([^\n*_]{1,200}?)(?<=\S)\1(?![\w*])/', '$2', $text );
        $text = str_replace( '`', '', $text );
        $text = preg_replace( '/^\s*(?:-{3,}|\*{3,}|_{3,}|={3,})\s*$/m', ' ', $text );
        $text = preg_replace( '/:::\s*\w*/', ' ', $text );
        return html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    }

    /**
     * A window of the text around the densest run of query terms, as parts: list of array( text, isMatch ).
     * The client builds the snippet from them with text nodes and <mark>, never as HTML.
     */
    public static function snippetParts( $text, array $marks, array $phrases = array(), $length = self::SNIPPET_LENGTH )
    {
        $text = (string)$text;
        if ( $text === '' )
            return array();
        $patterns = self::markPatterns( $marks, $phrases );
        $hits = array();
        foreach ( $patterns as $pattern )
        {
            if ( preg_match_all( $pattern, $text, $m, PREG_OFFSET_CAPTURE ) )
            {
                foreach ( $m[0] as $match )
                    $hits[] = array( $match[1], strlen( $match[0] ) );
            }
        }
        usort( $hits, function ( $a, $b ) { return $a[0] - $b[0] ?: $b[1] - $a[1]; } );

        // the window (in bytes) that holds the most distinct hits
        $bytes = strlen( $text );
        $from = 0;
        $bestCount = 0;
        $j = 0;
        $count = count( $hits );
        for ( $i = 0; $i < $count; $i++ )
        {
            while ( $j < $count && $hits[$j][0] + $hits[$j][1] - $hits[$i][0] <= $length )
                $j++;
            if ( $j - $i > $bestCount )
            {
                $bestCount = $j - $i;
                $from = $hits[$i][0];
            }
        }
        if ( $bestCount )
            $from = max( 0, $from - (int)( $length / 4 ) );
        $start = $from;
        if ( $start > 0 )
        {
            // start on a word boundary, never inside a UTF-8 sequence
            $space = strpos( $text, ' ', $start );
            $start = ( $space !== false && $space - $start < 30 ) ? $space + 1 : $start;
            while ( $start < $bytes && ( ord( $text[$start] ) & 0xC0 ) === 0x80 )
                $start++;
        }
        $end = min( $bytes, $start + $length );
        if ( $end < $bytes )
        {
            $space = strrpos( substr( $text, $start, $end - $start ), ' ' );
            if ( $space !== false && $space > $length / 2 )
                $end = $start + $space;
            while ( $end > $start && $end < $bytes && ( ord( $text[$end] ) & 0xC0 ) === 0x80 )
                $end--;
        }

        // the parts: plain text and matches, inside [start, end)
        $parts = array();
        $plain = $start > 0 ? '… ' : '';
        $pos = $start;
        foreach ( $hits as $hit )
        {
            list( $at, $len ) = $hit;
            if ( $at < $pos || $at + $len > $end )
                continue;
            $plain .= substr( $text, $pos, $at - $pos );
            if ( $plain !== '' )
                $parts[] = array( $plain, false );
            $parts[] = array( substr( $text, $at, $len ), true );
            $plain = '';
            $pos = $at + $len;
        }
        $plain .= substr( $text, $pos, $end - $pos ) . ( $end < $bytes ? ' …' : '' );
        if ( $plain !== '' )
            $parts[] = array( $plain, false );
        return $parts;
    }

    /**
     * The snippet as HTML: every character escaped, the matches in <mark>.
     *
     * @param array $parts from snippetParts()
     */
    public static function snippetHtml( array $parts )
    {
        $out = '';
        foreach ( $parts as $part )
            $out .= $part[1] ? '<mark>' . self::escape( $part[0] ) . '</mark>' : self::escape( $part[0] );
        return $out;
    }

    /** snippetHtml( snippetParts( ... ) ). */
    public static function snippet( $text, array $marks, array $phrases = array(), $length = self::SNIPPET_LENGTH )
    {
        return self::snippetHtml( self::snippetParts( $text, $marks, $phrases, $length ) );
    }

    protected static function markPatterns( array $marks, array $phrases )
    {
        $patterns = array();
        foreach ( $phrases as $phrase )
        {
            $words = array_map( function ( $w ) { return preg_quote( $w, '/' ); }, $phrase );
            $patterns[] = '/(?<![\p{L}\p{N}])' . implode( '[^\p{L}\p{N}]+', $words ) . '(?![\p{L}\p{N}])/iu';
        }
        $marks = array_unique( $marks );
        // longest first, so "configuration" is marked rather than "config" within it
        usort( $marks, function ( $a, $b ) { return strlen( $b ) - strlen( $a ); } );
        $alts = array();
        foreach ( array_slice( $marks, 0, 40 ) as $mark )
            $alts[] = preg_quote( $mark, '/' );
        if ( $alts )
            $patterns[] = '/(?<![\p{L}\p{N}])(?:' . implode( '|', $alts ) . ')[\p{L}\p{N}]{0,3}(?![\p{L}\p{N}])/iu';
        return $patterns;
    }

    protected static function escape( $text )
    {
        return htmlspecialchars( $text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8' );
    }

    // ------------------------------------------------------------------ tokens

    public static function lower( $text )
    {
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );
    }

    /**
     * Words of a text, lower-cased: letters, digits and _ (so ezcache, eZContentObject and Site_Access are one
     * word each); a dotted name such as ezcache.php also gives its parts.
     *
     * @return string[]
     */
    public static function tokenize( $text )
    {
        $text = self::lower( (string)$text );
        if ( !preg_match_all( '/[\p{L}\p{N}_]+/u', $text, $m ) )
            return array();
        $out = array();
        foreach ( $m[0] as $word )
        {
            $word = trim( $word, '_' );
            $len = strlen( $word );
            if ( $len < 2 || $len > 40 )
                continue;
            $out[] = $word;
            if ( strpos( $word, '_' ) !== false )
            {
                foreach ( explode( '_', $word ) as $part )
                {
                    if ( strlen( $part ) >= 2 )
                        $out[] = $part;
                }
            }
        }
        return $out;
    }

    /** A light English stemmer: plurals and the most common endings, enough for "caches" to find "cache". */
    public static function stem( $word )
    {
        $len = strlen( $word );
        if ( $len <= 4 || preg_match( '/[^a-z]/', $word ) )
            return $word;
        if ( substr( $word, -3 ) === 'ies' )
            return substr( $word, 0, -3 ) . 'y';
        if ( substr( $word, -4 ) === 'sses' )
            return substr( $word, 0, -2 );
        if ( substr( $word, -1 ) === 's' && substr( $word, -2 ) !== 'ss' && substr( $word, -2 ) !== 'us' && substr( $word, -2 ) !== 'is' )
            return substr( $word, 0, -1 );
        return $word;
    }

    protected function parseQuery( $query )
    {
        $phrases = array();
        $terms = array();
        $exclude = array();
        $stop = array_flip( $this->settings['stopWords'] );
        if ( preg_match_all( '/"([^"]+)"/u', $query, $m ) )
        {
            foreach ( $m[1] as $phrase )
            {
                $words = self::tokenize( $phrase );
                if ( count( $words ) > 1 )
                    $phrases[] = $words;
            }
        }
        $plain = preg_replace( '/"[^"]*"?/u', ' $0 ', $query );
        foreach ( preg_split( '/\s+/u', trim( $plain ) ) as $raw )
        {
            $negative = strlen( $raw ) > 1 && $raw[0] === '-';
            foreach ( self::tokenize( $raw ) as $token )
            {
                if ( $negative )
                    $exclude[] = $token;
                elseif ( !isset( $stop[$token] ) )
                    $terms[] = $token;
            }
        }
        $terms = array_values( array_unique( $terms ) );
        if ( !$terms )
        {
            // a query of stop words only searches for them
            foreach ( self::tokenize( $plain ) as $token )
                $terms[] = $token;
            $terms = array_values( array_unique( $terms ) );
        }
        $terms = array_slice( $terms, 0, self::MAX_TERMS );
        // prefix matching for the word being typed: the last term, unless the query ends with a space or quote
        $prefix = null;
        if ( $terms && !preg_match( '/[\s"]$/u', $query ) )
            $prefix = end( $terms );
        return array( 'terms' => $terms, 'phrases' => $phrases, 'exclude' => array_values( array_unique( $exclude ) ), 'prefix' => $prefix );
    }

    protected function prefixTerms( $prefix, array $vocabulary )
    {
        if ( strlen( $prefix ) < 2 )
            return array();
        // binary search for the first term >= prefix in the sorted vocabulary
        $lo = 0;
        $hi = count( $vocabulary );
        while ( $lo < $hi )
        {
            $mid = ( $lo + $hi ) >> 1;
            if ( strcmp( $vocabulary[$mid], $prefix ) < 0 )
                $lo = $mid + 1;
            else
                $hi = $mid;
        }
        $out = array();
        $len = strlen( $prefix );
        for ( $i = $lo, $n = count( $vocabulary ); $i < $n && count( $out ) < self::PREFIX_EXPANSIONS; $i++ )
        {
            if ( strncmp( $vocabulary[$i], $prefix, $len ) !== 0 )
                break;
            if ( $vocabulary[$i] !== $prefix )
                $out[] = $vocabulary[$i];
        }
        return $out;
    }

    public static function cleanQuery( $query )
    {
        $query = is_string( $query ) ? $query : '';
        if ( !mb_check_encoding( $query, 'UTF-8' ) )
            $query = mb_convert_encoding( $query, 'UTF-8', 'UTF-8' );
        $query = preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', $query );
        return mb_substr( ltrim( $query ), 0, self::MAX_QUERY_LENGTH, 'UTF-8' );
    }

    public static function cleanFilters( array $filters )
    {
        $out = array();
        foreach ( array( 'audience', 'category', 'type' ) as $facet )
        {
            $values = isset( $filters[$facet] ) ? $filters[$facet] : array();
            if ( is_string( $values ) )
                $values = explode( ',', $values );
            $clean = array();
            foreach ( (array)$values as $value )
            {
                if ( is_string( $value ) && preg_match( '/^[a-z0-9][a-z0-9_.-]{0,63}$/i', trim( $value ) ) )
                    $clean[] = strtolower( trim( $value ) );
            }
            $out[$facet] = array_values( array_unique( array_slice( $clean, 0, 10 ) ) );
        }
        return $out;
    }

    // ------------------------------------------------------------------ index

    /**
     * The index (see build()), from the cache file when it is current, else built and stored. One process builds
     * at a time: the others wait for its lock and then read its file, so a change of the documents does not make
     * every concurrent request build the index.
     */
    public function index()
    {
        if ( $this->index !== null )
            return $this->index;
        $signature = $this->signature();
        $file = $this->cacheFile( $signature );
        $index = $this->load( $file, $signature );
        if ( $index !== null )
            return $this->index = $index;

        $lock = null;
        if ( $file !== null && ( is_dir( dirname( $file ) ) || @mkdir( dirname( $file ), eZDir::dirMode( 0775 ), true ) ) )
        {
            $lock = @fopen( dirname( $file ) . '/search.lock', 'c' );
            if ( $lock && @flock( $lock, LOCK_EX ) )
            {
                $index = $this->load( $file, $signature );
                if ( $index !== null )
                {
                    flock( $lock, LOCK_UN );
                    fclose( $lock );
                    return $this->index = $index;
                }
            }
        }
        $index = $this->build( $this->documentList() );
        $index['signature'] = $signature;
        if ( $file !== null && $this->store( $file, $index ) )
            $this->removeOtherFiles( $file );
        if ( $lock )
        {
            @flock( $lock, LOCK_UN );
            fclose( $lock );
        }
        return $this->index = $index;
    }

    /** The cached index of a signature, or null. */
    protected function load( $file, $signature )
    {
        if ( $file === null || !is_file( $file ) )
            return null;
        $index = include $file;
        if ( is_array( $index ) && isset( $index['format'], $index['signature'], $index['docs'], $index['terms'], $index['vocabulary'] )
             && $index['format'] === self::FORMAT && $index['signature'] === $signature )
            return $index;
        return null;
    }

    /**
     * Builds the index again, whatever the cache holds (the CLI: exp:help index).
     *
     * @return array stats()
     */
    public function rebuild()
    {
        $this->index = null;
        $file = $this->cacheFile( $this->signature() );
        if ( $file !== null && is_file( $file ) )
            @unlink( $file );
        $this->index();
        return $this->stats();
    }

    /** Removes the index files of other signatures (a reader that has one open keeps reading it). */
    protected function removeOtherFiles( $file )
    {
        foreach ( (array)glob( dirname( $file ) . '/search-*.php' ) as $old )
        {
            if ( $old !== $file && preg_match( '/\/search-[a-f0-9]{32}\.php$/', $old ) )
                @unlink( $old );
        }
    }

    /**
     * What the cached index depends on: the documents (their signature) and the settings that change the index.
     */
    protected function signature()
    {
        $documents = $this->signature !== null ? $this->signature : (string)$this->documents()->signature();
        return md5( self::FORMAT . "\n" . $documents . "\n" . serialize( array( $this->settings['fieldWeights'], $this->settings['stopWords'] ) ) );
    }

    /** id => document with its source in 'text'. */
    protected function documentList()
    {
        if ( $this->documents !== null )
            return $this->documents;
        $index = $this->documents();
        $out = array();
        foreach ( $index->documents() as $key => $doc )
        {
            $id = isset( $doc['id'] ) && is_string( $doc['id'] ) && $doc['id'] !== '' ? $doc['id'] : (string)$key;
            $doc['text'] = $index->source( $id );
            if ( $doc['text'] === null )
                continue;
            $out[$id] = $doc;
        }
        return $out;
    }

    protected function cacheFile( $signature )
    {
        if ( empty( $this->settings['cacheDir'] ) )
            return null;
        return rtrim( $this->settings['cacheDir'], '/' ) . '/search-' . preg_replace( '/[^a-zA-Z0-9]/', '', $signature ) . '.php';
    }

    protected function store( $file, array $index )
    {
        $dir = dirname( $file );
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0775 ), true ) && !is_dir( $dir ) )
            return false;
        $tmp = $file . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        $php = "<?php\n// help search index, generated by expHelpSearch; rebuilt when the documents change\nreturn " . var_export( $index, true ) . ";\n";
        if ( @file_put_contents( $tmp, $php, LOCK_EX ) === false )
            return false;
        @chmod( $tmp, eZFile::fileMode( 0664 ) );
        if ( !@rename( $tmp, $file ) )
        {
            @unlink( $tmp );
            return false;
        }
        if ( function_exists( 'opcache_invalidate' ) )
            @opcache_invalidate( $file, true );
        return true;
    }

    /**
     * Builds the index from documents.
     *
     * @param array $documents id => array( title, headings, audience, category, type, keywords, path, text )
     * @return array docs (list of array( id, title, audience[], category, type, path, major version of the
     *               version directory the page is in or "" ), terms
     *               (term => "<document frequency>|<doc>:<tf x 100> ..."), vocabulary (the terms, sorted)
     */
    public function build( array $documents )
    {
        $weights = $this->settings['fieldWeights'];
        $stop = array_flip( $this->settings['stopWords'] );
        ksort( $documents, SORT_STRING );
        $docs = array();
        $tfs = array();
        $lengths = array();
        $stems = array();
        foreach ( $documents as $key => $doc )
        {
            $key = (string)$key;
            $path = isset( $doc['path'] ) && is_string( $doc['path'] ) && $doc['path'] !== '' ? $doc['path'] : 'doc/' . $key;
            $id = count( $docs );
            $title = isset( $doc['title'] ) && $doc['title'] !== '' ? (string)$doc['title'] : basename( $key );
            $docs[$id] = array(
                $key,
                $title,
                array_values( array_map( 'strval', isset( $doc['audience'] ) ? (array)$doc['audience'] : array() ) ),
                isset( $doc['category'] ) ? (string)$doc['category'] : '',
                isset( $doc['type'] ) ? (string)$doc['type'] : '',
                $path,
                preg_match( '#(?:^|/)(\d{1,2})\.\d+(?:\.\d+)?/#', $key, $version ) ? (string)(int)$version[1] : '',
            );
            $text = isset( $doc['text'] ) ? (string)$doc['text'] : '';
            $headings = array();
            if ( isset( $doc['headings'] ) )
            {
                foreach ( (array)$doc['headings'] as $heading )
                    $headings[] = is_array( $heading ) ? ( isset( $heading['text'] ) ? $heading['text'] : '' ) : (string)$heading;
            }
            else
            {
                foreach ( self::sections( $text, true, self::isMarkdown( $key ) ) as $section )
                {
                    if ( $section['heading'] !== null )
                        $headings[] = $section['heading'];
                }
            }
            $fields = array(
                'title' => $title,
                'headings' => implode( ' ', $headings ),
                'path' => preg_replace( '/\.(md|txt)$/', '', $key ),
                'keywords' => implode( ' ', isset( $doc['keywords'] ) ? (array)$doc['keywords'] : array() ),
                'body' => self::inlineText( self::stripFrontMatter( str_replace( array( "\r\n", "\r" ), "\n", $text ) ) ),
            );
            $tf = array();
            $length = 0;
            foreach ( $fields as $field => $value )
            {
                $w = isset( $weights[$field] ) ? (float)$weights[$field] : 1.0;
                if ( $w <= 0 )
                    continue;
                foreach ( self::tokenize( $value ) as $token )
                {
                    if ( isset( $stop[$token] ) )
                        continue;
                    $tf[$token] = ( isset( $tf[$token] ) ? $tf[$token] : 0 ) + $w;
                    $stem = isset( $stems[$token] ) ? $stems[$token] : ( $stems[$token] = self::stem( $token ) );
                    if ( $stem !== $token )
                        $tf[$stem] = ( isset( $tf[$stem] ) ? $tf[$stem] : 0 ) + $w * 0.9;
                    if ( $field === 'body' )
                        $length++;
                }
            }
            $tfs[$id] = $tf;
            $lengths[$id] = $length;
        }
        $avg = $lengths ? max( 1, array_sum( $lengths ) / count( $lengths ) ) : 1;

        // postings: BM25 term frequency part per document, x100 as an integer
        $postings = array();
        foreach ( $tfs as $id => $tf )
        {
            $norm = self::K1 * ( 1 - self::B + self::B * $lengths[$id] / $avg );
            foreach ( $tf as $term => $f )
            {
                $value = (int)round( 100 * $f * ( self::K1 + 1 ) / ( $f + $norm ) );
                $postings[$term][] = $id . ':' . max( 1, $value );
            }
        }
        $terms = array();
        foreach ( $postings as $term => $list )
            $terms[(string)$term] = count( $list ) . '|' . implode( ' ', $list );
        ksort( $terms, SORT_STRING );
        return array(
            'format' => self::FORMAT,
            'signature' => null,
            'built' => time(),
            'docs' => $docs,
            'terms' => $terms,
            'vocabulary' => array_map( 'strval', array_keys( $terms ) ),
        );
    }

    protected static function df( $entry )
    {
        return (int)substr( $entry, 0, strpos( $entry, '|' ) );
    }

    /**
     * The postings of a term: doc id => tf part (float); with $only, just the documents in it.
     */
    protected static function postings( $entry, ?array $only = null )
    {
        $list = substr( $entry, strpos( $entry, '|' ) + 1 );
        $out = array();
        foreach ( explode( ' ', $list ) as $pair )
        {
            $colon = strpos( $pair, ':' );
            $doc = (int)substr( $pair, 0, $colon );
            if ( $only !== null && !isset( $only[$doc] ) )
                continue;
            $out[$doc] = (int)substr( $pair, $colon + 1 ) / 100;
        }
        return $out;
    }

    /** Statistics for the CLI and the tests. */
    public function stats()
    {
        $index = $this->index();
        return array( 'documents' => count( $index['docs'] ), 'terms' => count( $index['terms'] ), 'built' => $index['built'], 'signature' => $index['signature'] );
    }
}
