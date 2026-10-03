<?php
/**
 * Search services: ezjscore/call/expsearch::<method>[::arg...]
 *
 * Full text search through the configured search engine, with paging, filters (class, section, subtree, date) and
 * facets over the matches; the index (suggestions, statistics, one object's status) and writes that index or
 * remove one object. Results respect the read policies of the current user.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expSearchServices extends expContentServiceBase
{
    public static $services = array(
        'search' => array( 'summary' => 'Searches the content. filter: class[], section, subtree[], date (day, week, month, 3months, year)', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'text' => 'string', 'limit' => 'int', 'offset' => 'int', 'filter' => 'json' ), 'returns' => 'page of nodes' ),
        'count' => array( 'summary' => 'Number of matches of a search', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'text' => 'string', 'filter' => 'json' ), 'returns' => '{count}' ),
        'facets' => array( 'summary' => 'Counts of the matches per class and per section (over the first 500 matches)', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'text' => 'string', 'filter' => 'json' ), 'returns' => '{classes, sections}' ),
        'byClass' => array( 'summary' => 'Searches within classes', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'text' => 'string', 'classes' => 'list', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'inSubtree' => array( 'summary' => 'Searches below a node', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'text' => 'string', 'node_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'bySection' => array( 'summary' => 'Searches within a section', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'text' => 'string', 'section_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'byAttribute' => array( 'summary' => 'Searches the values of one class attribute', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'text' => 'string', 'class_attribute_id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of nodes' ),
        'similar' => array( 'summary' => 'Objects similar to an object (searches with its name)', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'limit' => 'int' ), 'returns' => 'nodes' ),
        'suggest' => array( 'summary' => 'Words of the index that start with a prefix, most used first', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'prefix' => 'string', 'limit' => 'int' ), 'returns' => 'words' ),
        'normalize' => array( 'summary' => 'The text as the search engine normalises it', 'access' => 'user', 'write' => false, 'args' => array( 'text' => 'string' ), 'returns' => '{text}' ),
        'engine' => array( 'summary' => 'The search engine in use and its flags', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => '{engine, disabled, needs_commit}' ),
        'searchableClasses' => array( 'summary' => 'Classes with searchable attributes', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'classes' ),
        'indexStatus' => array( 'summary' => 'Whether an object has words in the index', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => '{indexed, words}' ),
        'stats' => array( 'summary' => 'Index size: words, links, objects with words', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'counts' ),
        'topPhrases' => array( 'summary' => 'The most frequent search phrases of the site, with their average hits', 'access' => array( 'setup', 'administrate' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of phrases' ),
        'reindex' => array( 'summary' => 'Indexes an object again', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => '{indexed}' ),
        'removeFromIndex' => array( 'summary' => 'Removes an object from the index', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => '{removed}' ),
        'clearPhrases' => array( 'summary' => 'Clears the stored search phrase statistics. POST: confirm=yes', 'access' => array( 'setup', 'administrate' ), 'write' => true, 'args' => array(), 'returns' => '{cleared}' ),
    );

    protected static $dateCodes = array( 'day' => 1, 'week' => 2, 'month' => 3, '3months' => 4, 'year' => 5 );

    /** Runs the search with a filter map and returns the engine's answer: array( nodes, count, stopwords ). */
    protected static function run( $text, $limit, $offset, $filter )
    {
        $text = trim( $text );
        if ( $text === '' )
            throw new expServiceException( 'The search text is empty', 400 );
        if ( mb_strlen( $text ) > 250 )
            throw new expServiceException( 'The search text is too long (250 characters)', 400 );
        if ( eZSearch::isDisabled() )
            throw new expServiceException( 'Search is disabled', 403 );
        $params = array( 'SearchOffset' => $offset, 'SearchLimit' => $limit );
        $filter = is_array( $filter ) ? $filter : array();
        if ( !empty( $filter['class'] ) )
        {
            $ids = array();
            foreach ( (array)$filter['class'] as $c )
                $ids[] = (int)self::contentClass( $c )->attribute( 'id' );
            $params['SearchContentClassID'] = count( $ids ) === 1 ? $ids[0] : $ids;
        }
        if ( isset( $filter['section'] ) )
            $params['SearchSectionID'] = (int)$filter['section'];
        if ( !empty( $filter['subtree'] ) )
            $params['SearchSubTreeArray'] = array_map( 'intval', (array)$filter['subtree'] );
        if ( !empty( $filter['date'] ) )
        {
            if ( !isset( self::$dateCodes[$filter['date']] ) )
                throw new expServiceException( 'date is one of ' . implode( ', ', array_keys( self::$dateCodes ) ), 400 );
            $params['SearchDate'] = self::$dateCodes[$filter['date']];
        }
        if ( !empty( $filter['attribute'] ) )
            $params['SearchContentClassAttributeID'] = (int)$filter['attribute'];
        $result = eZSearch::search( $text, $params, array( 'general' => array() ) );
        if ( !is_array( $result ) )
            return array( array(), 0, array() );
        return array( (array)$result['SearchResult'], (int)$result['SearchCount'], isset( $result['StopWordArray'] ) ? $result['StopWordArray'] : array() );
    }

    protected static function resultPage( $text, $limit, $offset, $filter )
    {
        list( $nodes, $count, $stop ) = self::run( $text, $limit, $offset, $filter );
        $items = array();
        foreach ( $nodes as $n )
            if ( $n instanceof eZContentObjectTreeNode && $n->canRead() )
                $items[] = self::exportNode( $n );
        $page = self::page( $items, $count, $offset, $limit );
        $meta = (array)$page['meta'];
        $meta['stop_words'] = array_values( array_map( function ( $w ) { return is_array( $w ) && isset( $w['word'] ) ? $w['word'] : (string)( is_array( $w ) ? json_encode( $w ) : $w ); }, (array)$stop ) );
        $meta['text'] = trim( $text );
        return self::ok( $page['data'], $meta );
    }

    public static function search( $args )
    {
        static::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        return self::resultPage( self::arg( $args, 0, 'string' ), $limit, $offset, self::arg( $args, 3, 'json', array() ) );
    }

    public static function count( $args )
    {
        static::guard( __FUNCTION__ );
        list( $nodes, $count ) = self::run( self::arg( $args, 0, 'string' ), 1, 0, self::arg( $args, 1, 'json', array() ) );
        return self::ok( array( 'count' => $count ) );
    }

    public static function facets( $args )
    {
        static::guard( __FUNCTION__ );
        list( $nodes, $count ) = self::run( self::arg( $args, 0, 'string' ), 500, 0, self::arg( $args, 1, 'json', array() ) );
        $classes = $sections = array();
        foreach ( $nodes as $n )
        {
            if ( !$n instanceof eZContentObjectTreeNode || !$n->canRead() )
                continue;
            $c = $n->attribute( 'class_identifier' );
            $classes[$c] = ( isset( $classes[$c] ) ? $classes[$c] : 0 ) + 1;
            $s = (int)$n->object()->attribute( 'section_id' );
            $sections[$s] = ( isset( $sections[$s] ) ? $sections[$s] : 0 ) + 1;
        }
        arsort( $classes );
        arsort( $sections );
        return self::ok( array( 'classes' => (object)$classes, 'sections' => (object)$sections ), array( 'total' => $count, 'sampled' => count( $nodes ) ) );
    }

    public static function byClass( $args )
    {
        static::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 2, 3 );
        return self::resultPage( self::arg( $args, 0, 'string' ), $limit, $offset, array( 'class' => self::arg( $args, 1, 'list' ) ) );
    }

    public static function inSubtree( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 1, 'int' ) );
        list( $limit, $offset ) = self::paging( $args, 2, 3 );
        return self::resultPage( self::arg( $args, 0, 'string' ), $limit, $offset, array( 'subtree' => array( (int)$node->attribute( 'node_id' ) ) ) );
    }

    public static function bySection( $args )
    {
        static::guard( __FUNCTION__ );
        $s = eZSection::fetch( self::arg( $args, 1, 'int' ) );
        if ( !$s )
            throw new expServiceException( 'The section does not exist', 404 );
        list( $limit, $offset ) = self::paging( $args, 2, 3 );
        return self::resultPage( self::arg( $args, 0, 'string' ), $limit, $offset, array( 'section' => (int)$s->attribute( 'id' ) ) );
    }

    public static function byAttribute( $args )
    {
        static::guard( __FUNCTION__ );
        $a = eZContentClassAttribute::fetch( self::arg( $args, 1, 'int' ) );
        if ( !$a )
            throw new expServiceException( 'The class attribute does not exist', 404 );
        list( $limit, $offset ) = self::paging( $args, 2, 3 );
        return self::resultPage( self::arg( $args, 0, 'string' ), $limit, $offset, array( 'attribute' => (int)$a->attribute( 'id' ) ) );
    }

    public static function similar( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $limit = min( 50, max( 1, self::arg( $args, 1, 'int', 5 ) ) );
        $name = trim( preg_replace( '/[^\p{L}\p{N} ]+/u', ' ', (string)$o->attribute( 'name' ) ) );
        if ( $name === '' )
            return self::ok( array() );
        list( $nodes ) = self::run( $name, $limit + 1, 0, array( 'class' => array( $o->attribute( 'class_identifier' ) ) ) );
        $out = array();
        foreach ( $nodes as $n )
            if ( $n instanceof eZContentObjectTreeNode && $n->canRead() && (int)$n->attribute( 'contentobject_id' ) !== (int)$o->attribute( 'id' ) && count( $out ) < $limit )
                $out[] = self::exportNode( $n );
        return self::ok( $out );
    }

    public static function suggest( $args )
    {
        static::guard( __FUNCTION__ );
        $prefix = mb_strtolower( trim( self::arg( $args, 0, 'string' ) ) );
        if ( mb_strlen( $prefix ) < 2 )
            throw new expServiceException( 'Give at least two letters', 400 );
        $limit = min( 50, max( 1, self::arg( $args, 1, 'int', 10 ) ) );
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
            return self::ok( array() );
        $p = $db->escapeString( preg_replace( '/[%_]/', '', $prefix ) );
        $rows = $db->arrayQuery( "SELECT word, object_count FROM ezsearch_word WHERE word LIKE '$p%' ORDER BY object_count DESC", array( 'limit' => $limit ) );
        $out = array();
        foreach ( (array)$rows as $r )
            $out[] = array( 'word' => $r['word'], 'objects' => (int)$r['object_count'] );
        return self::ok( $out );
    }

    public static function normalize( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'text' => eZSearch::normalizeText( self::arg( $args, 0, 'string' ) ) ) );
    }

    public static function engine( $args )
    {
        static::guard( __FUNCTION__ );
        $e = eZSearch::getEngine();
        return self::ok( array( 'engine' => is_object( $e ) ? get_class( $e ) : null, 'disabled' => (bool)eZSearch::isDisabled(), 'needs_commit' => (bool)eZSearch::needCommit() ) );
    }

    public static function searchableClasses( $args )
    {
        static::guard( __FUNCTION__ );
        $out = array();
        foreach ( (array)eZContentClass::fetchAllClasses( true, false ) as $c )
        {
            $n = 0;
            foreach ( (array)$c->fetchSearchableAttributes() as $a )
                $n++;
            if ( $n )
                $out[] = array( 'id' => (int)$c->attribute( 'id' ), 'identifier' => $c->attribute( 'identifier' ), 'name' => $c->attribute( 'name' ), 'searchable_attributes' => $n );
        }
        return self::ok( $out );
    }

    public static function indexStatus( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
            return self::ok( array( 'indexed' => null, 'words' => null ) );
        $n = (int)$db->arrayQuery( 'SELECT COUNT(*) AS cnt FROM ezsearch_object_word_link WHERE contentobject_id = ' . (int)$o->attribute( 'id' ) )[0]['cnt'];
        return self::ok( array( 'indexed' => $n > 0, 'words' => $n ) );
    }

    public static function stats( $args )
    {
        static::guard( __FUNCTION__ );
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
            return self::ok( array( 'words' => null, 'links' => null, 'objects' => null ) );
        $count = function ( $sql ) use ( $db ) { return (int)$db->arrayQuery( $sql )[0]['cnt']; };
        return self::ok( array( 'words' => $count( 'SELECT COUNT(*) AS cnt FROM ezsearch_word' ), 'links' => $count( 'SELECT COUNT(*) AS cnt FROM ezsearch_object_word_link' ),
                                'objects' => $count( 'SELECT COUNT(DISTINCT contentobject_id) AS cnt FROM ezsearch_object_word_link' ) ) );
    }

    public static function topPhrases( $args )
    {
        static::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
            return self::page( array(), 0, $offset, $limit );
        $total = (int)$db->arrayQuery( 'SELECT COUNT(*) AS cnt FROM ezsearch_search_phrase' )[0]['cnt'];
        $items = array();
        foreach ( (array)eZSearchLog::mostFrequentPhraseArray( array( 'limit' => $limit, 'offset' => $offset ) ) as $r )
            $items[] = array( 'phrase' => $r['phrase'], 'searches' => (int)$r['phrase_count'], 'average_hits' => round( (float)$r['result_count'], 2 ) );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function reindex( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        if ( eZSearch::isDisabled() )
            throw new expServiceException( 'Search is disabled', 403 );
        eZSearch::removeObject( $o );
        $ok = eZSearch::addObject( $o );
        return self::ok( array( 'indexed' => $ok !== false ) );
    }

    public static function removeFromIndex( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        if ( eZSearch::isDisabled() )
            throw new expServiceException( 'Search is disabled', 403 );
        eZSearch::removeObject( $o );
        return self::ok( array( 'removed' => (int)$o->attribute( 'id' ) ) );
    }

    public static function clearPhrases( $args )
    {
        static::guard( __FUNCTION__ );
        if ( self::post( 'confirm', 'string', '' ) !== 'yes' )
            throw new expServiceException( 'Clearing the statistics needs the POST field confirm=yes', 400 );
        eZSearchLog::removeStatistics();
        return self::ok( array( 'cleared' => true ) );
    }
}
