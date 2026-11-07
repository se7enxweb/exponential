<?php
/**
 * File containing the expUrlMapSuggester class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Suggests new targets for the old addresses of a URL map: for each row without a new_url, the pages of this site
 * whose address or name is most like the old address, with a score from 0 to 100.
 *
 *   $suggester = new expUrlMapSuggester( expUrlMapSuggester::candidatesFromDatabase( array( 'siteaccess' => 'site' ) ) );
 *   $result = $suggester->apply( $rows, array( 'fill_above' => 80 ) );
 *   expUrlMapFormats::writeFile( 'var/tmp/map.csv', $result['rows'] );
 *
 * Text is compared normalized (normalize(), tokens()): percent-decoded, camelCase split, transliterated to ASCII,
 * lower case, file endings such as .html or .php and index pages dropped, split on everything that is not a letter
 * or a digit, short and very common words left out. A candidate is scored on its last path element and its name
 * against the old last element (most), the two last elements as text (spelling), and the whole paths; tokens that
 * differ only in an ending (recipe, recipes) count most of a match.
 *
 * The candidates are indexed once (token => candidates), so each row is compared with the candidates that share a
 * token with it, not with every page: 5,000 rows against 10,000 pages take seconds.
 *
 * A candidate is an array: path (as visitors use it: the target written to new_url), node_id, name.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUrlMapSuggester
{
    /** Words that say nothing about a page */
    const STOPWORDS = array( 'a', 'an', 'and', 'the', 'of', 'to', 'in', 'on', 'for', 'with', 'by', 'at', 'or', 'is',
                             'der', 'die', 'das', 'und', 'oder', 'mit', 'von', 'zu', 'im', 'in', 'am', 'fuer', 'ein', 'eine',
                             'le', 'la', 'les', 'de', 'des', 'du', 'et', 'el', 'los', 'y',
                             'www', 'http', 'https', 'html', 'htm', 'php', 'asp', 'aspx', 'index', 'default', 'page', 'id' );

    /** File endings dropped from the last path element */
    const ENDINGS = '/\.(?:s?html?|php[0-9]?|aspx?|jsp|cfm|cgi|pl|do|action)$/i';

    /** @var array[] the candidates */
    protected $candidates = array();

    /** @var array[] per candidate: last (tokens), path (tokens), name (tokens), text (the last element as text) */
    protected $features = array();

    /** @var array token => candidate indexes */
    protected $index = array();

    /** @var array options */
    protected $options;

    /**
     * @param array[] $candidates path, node_id, name
     * @param array $options top (suggestions per row, default 3), min_score (default 30), max_compare (candidates
     *                       scored per row, default 15)
     */
    public function __construct( array $candidates, array $options = array() )
    {
        $this->options = $options + array( 'top' => 3, 'min_score' => 30, 'max_compare' => 15 );
        foreach ( array_values( $candidates ) as $i => $candidate )
        {
            $candidate += array( 'path' => '', 'node_id' => 0, 'name' => '' );
            $path = '/' . trim( (string)$candidate['path'], '/' );
            $this->candidates[$i] = array( 'path' => $path, 'node_id' => (int)$candidate['node_id'], 'name' => (string)$candidate['name'] );
            $segments = self::segments( $path );
            $last = $segments ? end( $segments ) : '';
            $f = array( 'last' => self::tokens( $last ), 'path' => self::tokens( implode( ' ', $segments ) ), 'name' => self::tokens( $candidate['name'] ),
                        'text' => implode( '-', self::tokens( $last ) ) );
            $this->features[$i] = $f;
            foreach ( array_unique( array_merge( $f['last'], $f['path'], $f['name'] ) ) as $token )
            {
                foreach ( self::keys( $token ) as $key )
                    $this->index[$key][$i] = true;
            }
        }
    }

    /** @return int how many candidates there are */
    public function count()
    {
        return count( $this->candidates );
    }

    /**
     * Text in the form it is compared in: decoded, camelCase split, ASCII, lower case, with single spaces. Pure.
     *
     * @param string $text
     * @return string
     */
    public static function normalize( $text )
    {
        $text = rawurldecode( str_replace( '+', ' ', (string)$text ) );
        $text = preg_replace( '/(?<=[a-z])(?=[A-Z])/', ' ', $text );
        $text = self::transliterate( $text );
        $text = strtolower( $text );
        return trim( (string)preg_replace( '/[^a-z0-9]+/', ' ', $text ) );
    }

    /**
     * ASCII for a text: German umlauts as ae, oe, ue and ß as ss, other letters without their accents. Pure.
     *
     * @param string $text
     * @return string
     */
    public static function transliterate( $text )
    {
        $text = strtr( (string)$text, array( 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ß' => 'ss',
                                             'æ' => 'ae', 'Æ' => 'Ae', 'ø' => 'oe', 'Ø' => 'Oe', 'œ' => 'oe', 'Œ' => 'Oe', 'å' => 'aa', 'Å' => 'Aa' ) );
        if ( !preg_match( '/[\x80-\xff]/', $text ) )
            return $text;
        if ( class_exists( 'Transliterator' ) )
        {
            $t = Transliterator::create( 'Any-Latin; Latin-ASCII' );
            if ( $t )
            {
                $out = $t->transliterate( $text );
                if ( is_string( $out ) )
                    return $out;
            }
        }
        $out = function_exists( 'iconv' ) ? @iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $text ) : false;
        return is_string( $out ) ? $out : (string)preg_replace( '/[\x80-\xff]+/', ' ', $text );
    }

    /**
     * The words of a text, normalized, without stop words and one-letter words. Pure.
     *
     * @param string $text
     * @return string[]
     */
    public static function tokens( $text )
    {
        $text = preg_replace( self::ENDINGS, '', trim( (string)$text ) );
        $out = array();
        foreach ( explode( ' ', self::normalize( $text ) ) as $token )
        {
            if ( strlen( $token ) < 2 || in_array( $token, self::STOPWORDS, true ) )
                continue;
            $out[] = $token;
        }
        return $out;
    }

    /**
     * The path elements of an address that say something: without query and fragment, the file ending of the last
     * one and an index page (index.html, default.aspx) dropped. Pure.
     *
     * @param string $url
     * @return string[]
     */
    public static function segments( $url )
    {
        $parts = expUrlMapRow::parseUrl( (string)$url );
        $segments = array_values( array_filter( explode( '/', trim( $parts['path'], '/' ) ), 'strlen' ) );
        if ( $segments )
        {
            $last = count( $segments ) - 1;
            $segments[$last] = (string)preg_replace( self::ENDINGS, '', $segments[$last] );
            if ( preg_match( '/^(index|default|home|start)$/i', $segments[$last] ) || $segments[$last] === '' )
                array_pop( $segments );
        }
        return $segments;
    }

    /**
     * How alike two words are: 1 the same, 0.8 one is the other with an ending (recipe, recipes; categorie,
     * category), 0.6 one letter apart, else 0. Pure.
     *
     * @param string $a
     * @param string $b
     * @return float
     */
    public static function tokenSimilarity( $a, $b )
    {
        if ( $a === $b )
            return 1.0;
        $la = strlen( $a );
        $lb = strlen( $b );
        if ( $la >= 4 && $lb >= 4 )
        {
            $common = min( $la, $lb ) - 1;
            if ( abs( $la - $lb ) <= 3 && $common >= 3 && strncmp( $a, $b, $common ) === 0 )
                return 0.8;
            if ( abs( $la - $lb ) <= 1 && levenshtein( $a, $b ) === 1 )
                return 0.6;
        }
        return 0.0;
    }

    /**
     * How alike two lists of words are, from 0 to 1: every word's best match in the other list, both ways. Pure.
     *
     * @param string[] $a
     * @param string[] $b
     * @return float
     */
    public static function overlap( array $a, array $b )
    {
        if ( !$a || !$b )
            return 0.0;
        $sum = 0.0;
        foreach ( array( array( $a, $b ), array( $b, $a ) ) as $pair )
        {
            foreach ( $pair[0] as $x )
            {
                $best = 0.0;
                foreach ( $pair[1] as $y )
                {
                    $best = max( $best, self::tokenSimilarity( $x, $y ) );
                    if ( $best >= 1.0 )
                        break;
                }
                $sum += $best;
            }
        }
        return $sum / ( count( $a ) + count( $b ) );
    }

    /**
     * The score of a candidate for an old address, 0 to 100. Pure.
     *
     * @param string $oldUrl
     * @param array $candidate path, name
     * @return int
     */
    public static function score( $oldUrl, array $candidate )
    {
        $s = new self( array( $candidate ) );
        return $s->scoreIndex( self::rowFeatures( $oldUrl ), 0 );
    }

    /**
     * The best candidates for an old address, best first.
     *
     * @param string $oldUrl
     * @return array[] path, node_id, name, score
     */
    public function suggest( $oldUrl )
    {
        $features = self::rowFeatures( $oldUrl );
        $votes = array();
        $n = max( 1, count( $this->candidates ) );
        $keys = array();
        foreach ( array_unique( array_merge( $features['last'], $features['path'] ) ) as $token )
        {
            $weightOf = in_array( $token, $features['last'], true ) ? 2.0 : 1.0;
            foreach ( self::keys( $token ) as $k => $key )
            {
                if ( !isset( $this->index[$key] ) )
                    continue;
                // a rare word says more than a common one
                $df = count( $this->index[$key] );
                $keys[] = array( $key, $df, $weightOf * ( $k === 0 ? 1.0 : 0.6 ) * log( 1 + $n / $df ) );
            }
        }
        // the rare words first; a common word (in many pages) only adds to the pages the rarer ones found, so a row
        // costs what its rare words cost, not a pass over half the site
        usort( $keys, function ( $a, $b ) { return $a[1] - $b[1]; } );
        $common = max( 100, (int)( $n / 20 ) );
        foreach ( $keys as $entry )
        {
            list( $key, $df, $weight ) = $entry;
            if ( $df <= $common || !$votes )
            {
                foreach ( $this->index[$key] as $i => $true )
                    $votes[$i] = ( isset( $votes[$i] ) ? $votes[$i] : 0 ) + $weight;
            }
            else
            {
                foreach ( $votes as $i => $vote )
                    if ( isset( $this->index[$key][$i] ) )
                        $votes[$i] = $vote + $weight;
            }
        }
        arsort( $votes );
        $out = array();
        foreach ( array_slice( array_keys( $votes ), 0, max( 1, (int)$this->options['max_compare'] ) ) as $i )
        {
            $score = $this->scoreIndex( $features, $i );
            if ( $score >= (int)$this->options['min_score'] )
                $out[] = $this->candidates[$i] + array( 'score' => $score );
        }
        usort( $out, function ( $x, $y )
        {
            return $y['score'] - $x['score'] ?: strlen( $x['path'] ) - strlen( $y['path'] ) ?: strcmp( $x['path'], $y['path'] );
        } );
        // one suggestion per target
        $seen = array();
        $unique = array();
        foreach ( $out as $suggestion )
        {
            $key = strtolower( $suggestion['path'] );
            if ( isset( $seen[$key] ) )
                continue;
            $seen[$key] = true;
            $unique[] = $suggestion;
        }
        return array_slice( $unique, 0, max( 1, (int)$this->options['top'] ) );
    }

    /**
     * Suggestions for every row without a new_url. A row whose best suggestion scores fill_above or more (and is not
     * tied with the next one) gets it as its new_url; the others keep an empty new_url and get the suggestions in
     * their note ("suggested: /recipes (87), /news (61)").
     *
     * @param expUrlMapRow[] $rows
     * @param array $options fill_above (0..100, null: never fill), notes (default true: suggestions in the note)
     * @return array rows (expUrlMapRow[], every row, in order), report (per row without a target: line, old_url,
     *               new_url, filled, suggestions), counts (rows, open, filled, suggested, none)
     */
    public function apply( array $rows, array $options = array() )
    {
        $options += array( 'fill_above' => null, 'notes' => true );
        $fill = $options['fill_above'] === null || $options['fill_above'] === '' ? null : (int)$options['fill_above'];
        $out = array();
        $report = array();
        $counts = array( 'rows' => count( $rows ), 'open' => 0, 'filled' => 0, 'suggested' => 0, 'none' => 0 );
        foreach ( $rows as $row )
        {
            if ( !$row instanceof expUrlMapRow || $row->newUrl() !== '' || $row->oldUrl() === '' || $row->isWildcard() )
            {
                $out[] = $row;
                continue;
            }
            $counts['open']++;
            $suggestions = $this->suggest( $row->oldUrl() );
            $best = $suggestions ? $suggestions[0] : null;
            $tied = $best && isset( $suggestions[1] ) && $suggestions[1]['score'] === $best['score'];
            $filled = $best && $fill !== null && $best['score'] >= $fill && !$tied;
            $note = $row->note();
            if ( $suggestions && !$filled && $options['notes'] )
            {
                $list = array();
                foreach ( $suggestions as $s )
                    $list[] = $s['path'] . ' (' . $s['score'] . ')';
                $note = ( $note !== '' ? $note . ' | ' : '' ) . 'suggested: ' . implode( ', ', $list );
            }
            elseif ( $filled )
                $note = ( $note !== '' ? $note . ' | ' : '' ) . 'suggested target, score ' . $best['score'];
            $out[] = new expUrlMapRow( $row->oldUrl(), $filled ? $best['path'] : '', $row->kind(), $row->siteaccess(), $row->language(), $note, $row->line(), $row->status() );
            $counts[$filled ? 'filled' : ( $suggestions ? 'suggested' : 'none' )]++;
            $report[] = array( 'line' => $row->line(), 'old_url' => $row->oldUrl(), 'new_url' => $filled ? $best['path'] : '', 'filled' => $filled,
                               'suggestions' => $suggestions );
        }
        return array( 'rows' => $out, 'report' => $report, 'counts' => $counts );
    }

    /**
     * The pages of this installation as candidates: every node's URL alias (each language's), as visitors of the
     * siteaccess use it (without its PathPrefix), with the node's name; only published, visible nodes below the
     * siteaccess's content root. Reads three tables once.
     *
     * @param array $options siteaccess (default the default siteaccess), root_node (default the siteaccess's
     *                       content.ini [NodeSettings] RootNode), include_hidden
     * @return array[] path, node_id, name
     */
    public static function candidatesFromDatabase( array $options = array() )
    {
        $environment = new expUrlMapKernelEnvironment();
        $siteaccess = isset( $options['siteaccess'] ) && $options['siteaccess'] !== '' ? (string)$options['siteaccess'] : $environment->defaultSiteaccess();
        $root = isset( $options['root_node'] ) ? (int)$options['root_node'] : 0;
        if ( !$root )
        {
            $ini = $siteaccess !== '' && in_array( $siteaccess, $environment->siteaccesses(), true ) ? eZSiteAccess::getIni( $siteaccess, 'content.ini' ) : null;
            $ini = $ini ?: eZINI::instance( 'content.ini' );
            $root = (int)$ini->variable( 'NodeSettings', 'RootNode' ) ?: 2;
        }
        $prefix = $environment->pathPrefix( $siteaccess );
        $db = eZDB::instance();

        $nodes = array();
        $rootMark = '/' . $root . '/';
        foreach ( (array)$db->arrayQuery( 'SELECT node_id, contentobject_id, path_string, is_hidden, is_invisible FROM ezcontentobject_tree' ) as $row )
        {
            if ( strpos( (string)$row['path_string'], $rootMark ) === false )
                continue;
            if ( empty( $options['include_hidden'] ) && ( (int)$row['is_hidden'] || (int)$row['is_invisible'] ) )
                continue;
            $nodes[(int)$row['node_id']] = (int)$row['contentobject_id'];
        }
        $names = array();
        foreach ( (array)$db->arrayQuery( 'SELECT id, name FROM ezcontentobject WHERE status = 1' ) as $row )
            $names[(int)$row['id']] = (string)$row['name'];

        $elements = array();
        $leaves = array();
        foreach ( (array)$db->arrayQuery( 'SELECT id, parent, text, action FROM ezurlalias_ml WHERE is_original = 1 AND is_alias = 0' ) as $row )
        {
            $id = (int)$row['id'];
            if ( !isset( $elements[$id] ) )
                $elements[$id] = array( (int)$row['parent'], (string)$row['text'] );
            if ( strpos( (string)$row['action'], 'eznode:' ) === 0 )
                $leaves[] = array( (int)substr( (string)$row['action'], 7 ), (int)$row['parent'], (string)$row['text'] );
        }
        $paths = array();
        $pathOf = function ( $id ) use ( &$pathOf, &$paths, $elements )
        {
            if ( $id === 0 )
                return '';
            if ( isset( $paths[$id] ) )
                return $paths[$id];
            $paths[$id] = false; // a loop ends here
            if ( !isset( $elements[$id] ) )
                return false;
            $parent = $pathOf( $elements[$id][0] );
            $paths[$id] = $parent === false ? false : $parent . '/' . $elements[$id][1];
            return $paths[$id];
        };
        $out = array();
        $seen = array();
        foreach ( $leaves as $leaf )
        {
            list( $nodeId, $parent, $text ) = $leaf;
            if ( !isset( $nodes[$nodeId] ) || !isset( $names[$nodes[$nodeId]] ) )
                continue;
            $parentPath = $pathOf( $parent );
            if ( $parentPath === false )
                continue;
            $path = $parentPath . '/' . $text;
            $visitor = expUrlMapKernelEnvironment::unprefixedPath( $path, $prefix['prefix'], $prefix['exclude'] );
            $path = $visitor !== null ? $visitor : $path;
            $key = strtolower( $path );
            if ( isset( $seen[$key] ) )
                continue;
            $seen[$key] = true;
            $out[] = array( 'path' => $path, 'node_id' => $nodeId, 'name' => $names[$nodes[$nodeId]] );
        }
        return $out;
    }

    /**
     * @param string $oldUrl
     * @return array last, path, text
     */
    protected static function rowFeatures( $oldUrl )
    {
        $segments = self::segments( $oldUrl );
        $last = array();
        // the last element that has words (/news/2019/ -> 2019 has one; /products/item.php?id=3 -> item)
        for ( $i = count( $segments ) - 1; $i >= 0 && !$last; $i-- )
            $last = self::tokens( $segments[$i] );
        return array( 'last' => $last, 'path' => self::tokens( implode( ' ', $segments ) ), 'text' => implode( '-', $last ) );
    }

    /**
     * @param array $row the old address's features
     * @param int $i a candidate
     * @return int 0..100
     */
    protected function scoreIndex( array $row, $i )
    {
        $f = $this->features[$i];
        if ( !$row['last'] && !$row['path'] )
            return 0;
        $element = max( self::overlap( $row['last'], $f['last'] ), self::overlap( $row['last'], $f['name'] ) );
        $text = 0.0;
        if ( $row['text'] !== '' && $f['text'] !== '' )
        {
            similar_text( $row['text'], $f['text'], $percent );
            $text = $percent / 100;
        }
        $path = self::overlap( $row['path'], $f['path'] );
        return (int)round( 100 * ( 0.55 * $element + 0.2 * $text + 0.25 * $path ) );
    }

    /**
     * The index keys of a word: itself, and its first four letters for words of five or more (recipe, recipes).
     *
     * @param string $token
     * @return string[]
     */
    protected static function keys( $token )
    {
        return strlen( $token ) >= 5 ? array( $token, '~' . substr( $token, 0, 4 ) ) : array( $token );
    }
}
