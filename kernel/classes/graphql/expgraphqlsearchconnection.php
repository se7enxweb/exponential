<?php
/**
 * The answer of the root query search (the type SearchResult): a page of what the search engine of the installation
 * found, how many it found, which engine answered and, from an engine that has them (Solr through ezfind), the facets.
 * The search runs once, when the first of its fields is asked for; the pagination is that of every list (limit/offset
 * and first/after, expGraphQLConnection).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLSearchConnection extends expGraphQLConnection
{
    /** @var array text, classes, subtree, sections, publishedAfter, facets, language */
    protected $search;

    /** @var array|null the answer of expGraphQLRepository::search() */
    protected $result = null;

    /**
     * @param expGraphQLRepository $repository
     * @param array $args the arguments of search
     */
    public function __construct( expGraphQLRepository $repository, array $args )
    {
        parent::__construct( $repository, null, $args );
        $this->search = array( 'text' => isset( $args['text'] ) ? trim( (string)$args['text'] ) : '',
                               'publishedBefore' => isset( $args['publishedBefore'] ) ? (string)$args['publishedBefore'] : null,
                               'classes' => isset( $args['classes'] ) ? (array)$args['classes'] : array(),
                               'subtree' => isset( $args['subtree'] ) ? array_map( 'intval', (array)$args['subtree'] ) : array(),
                               'sections' => isset( $args['sections'] ) ? array_map( 'intval', (array)$args['sections'] ) : array(),
                               'publishedAfter' => isset( $args['publishedAfter'] ) ? (string)$args['publishedAfter'] : null,
                               'facets' => isset( $args['facets'] ) ? (array)$args['facets'] : array(),
                               'language' => isset( $args['language'] ) ? (string)$args['language'] : false );
    }

    /**
     * @return array the search's answer: items (with one more when there is a next page), count, engine, facets
     */
    protected function result()
    {
        if ( $this->result === null )
        {
            $this->result = $this->limit === 0 && !$this->search['facets']
                ? array( 'items' => array(), 'count' => 0, 'engine' => '', 'facets' => array() )
                : $this->repository->search( $this->search + array( 'limit' => $this->limit + 1, 'offset' => $this->offset ) );
            $this->primeGroup( $this->result['items'] );
        }
        return $this->result;
    }

    /** @return expGraphQLContent[] */
    protected function fetched()
    {
        return $this->result()['items'];
    }

    /** @return int */
    public function totalCount()
    {
        return (int)$this->result()['count'];
    }

    /** @return string the search engine that answered */
    public function engine()
    {
        return (string)$this->result()['engine'];
    }

    /** @return array[] field, values (value, name, count) */
    public function facets()
    {
        return (array)$this->result()['facets'];
    }
}

?>
