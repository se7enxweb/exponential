<?php
/**
 * The help search (workstream C): a tokenized inverted index over the documents of expHelpDocumentIndex (cached
 * next to it), title and heading boosts, snippets with highlighting, filters. Skeleton: C replaces the bodies.
 *
 * search() result: array( 'query' => string, 'total' => int, 'offset' => int, 'limit' => int,
 *   'hits' => array of ( id, title, url, anchor, snippet (HTML, escaped, <mark>), audience[], category, type, score ),
 *   'facets' => array( 'audience' => id => count, 'category' => id => count, 'type' => id => count ) )
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpSearch
{
    /** @var expHelpDocumentIndex */
    protected $index;

    /** @param expHelpDocumentIndex|null $index */
    public function __construct( expHelpDocumentIndex $index = null ) { $this->index = $index ?: expHelpDocumentIndex::instance(); }

    /**
     * @param string $query
     * @param array $filters array( 'audience' => string, 'category' => string, 'type' => string )
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function search( $query, array $filters = array(), $limit = 20, $offset = 0 )
    {
        return array( 'query' => (string)$query, 'total' => 0, 'offset' => (int)$offset, 'limit' => (int)$limit, 'hits' => array(),
                      'facets' => array( 'audience' => array(), 'category' => array(), 'type' => array() ) );
    }

    /** @param string $text @return string[] */
    public static function tokenize( $text ) { return array(); }
}

?>
