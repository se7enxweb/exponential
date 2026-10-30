<?php
/**
 * A list of content below a location, as the field children answers it (the type ContentConnection): the page is
 * fetched when items, edges or pageInfo is asked for, the count only when totalCount is. Both pagination styles
 * work on the same field: limit/offset, and first/after with cursors (first and after win when given).
 *
 * A cursor is the position of an item in the whole list, base64 encoded; after a cursor the list goes on at the
 * next position. Cursors stay valid while the list does not change.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLConnection
{
    /** @var expGraphQLRepository */
    protected $repository;

    /** @var expGraphQLLocation|null */
    protected $location;

    /** @var array classes, sortField, sortOrder, depth */
    protected $params;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /** @var expGraphQLContent[]|null the page, with one more item when there is a next page */
    protected $fetched = null;

    /** @var int|null */
    protected $count = null;

    /**
     * @param expGraphQLRepository $repository
     * @param expGraphQLLocation|null $location null: an empty list
     * @param array $args the field's arguments
     */
    public function __construct( expGraphQLRepository $repository, $location, array $args )
    {
        $this->repository = $repository;
        $this->location = $location;
        $this->params = array( 'classes' => isset( $args['classes'] ) ? (array)$args['classes'] : array(),
                               'sortField' => isset( $args['sortField'] ) ? $args['sortField'] : null,
                               'sortOrder' => isset( $args['sortOrder'] ) ? $args['sortOrder'] : null,
                               'depth' => isset( $args['depth'] ) ? max( 1, (int)$args['depth'] ) : 1 );
        list( $this->limit, $this->offset ) = self::page( $args );
    }

    /**
     * @param array $args limit, offset, first, after
     * @return int[] array( limit, offset )
     */
    public static function page( array $args )
    {
        $max = expGraphQL::intSetting( 'MaxListLimit', 100 );
        $default = min( $max, expGraphQL::intSetting( 'DefaultListLimit', 25 ) );
        if ( isset( $args['first'] ) )
            $limit = (int)$args['first'];
        else if ( isset( $args['limit'] ) )
            $limit = (int)$args['limit'];
        else
            $limit = $default;
        $limit = max( 0, min( $max, $limit ) );
        if ( isset( $args['after'] ) && $args['after'] !== '' )
        {
            $position = self::decodeCursor( $args['after'] );
            if ( $position === null )
                throw new expGraphQLError( expGraphQLMessage::tr( 'The cursor of after is not one this API gave.' ), expGraphQLError::BAD_USER_INPUT );
            $offset = $position + 1;
        }
        else
            $offset = isset( $args['offset'] ) ? max( 0, (int)$args['offset'] ) : 0;
        return array( $limit, $offset );
    }

    /**
     * The number a list field multiplies the complexity of its fields with.
     *
     * @param array $args
     * @return int
     */
    public static function complexityCount( array $args )
    {
        $max = expGraphQL::intSetting( 'MaxListLimit', 100 );
        if ( isset( $args['first'] ) )
            return max( 1, min( $max, (int)$args['first'] ) );
        if ( isset( $args['limit'] ) )
            return max( 1, min( $max, (int)$args['limit'] ) );
        return max( 1, min( $max, expGraphQL::intSetting( 'DefaultListLimit', 25 ) ) );
    }

    /**
     * @param int $position
     * @return string
     */
    public static function encodeCursor( $position )
    {
        return base64_encode( 'exp:' . (int)$position );
    }

    /**
     * @param string $cursor
     * @return int|null
     */
    public static function decodeCursor( $cursor )
    {
        $decoded = base64_decode( (string)$cursor, true );
        if ( !is_string( $decoded ) || !preg_match( '/^exp:(\d{1,9})$/', $decoded, $m ) )
            return null;
        return (int)$m[1];
    }

    /** @return expGraphQLContent[] */
    protected function fetched()
    {
        if ( $this->fetched === null )
        {
            $this->fetched = ( $this->location === null || $this->limit === 0 ) ? array()
                : $this->repository->children( $this->location, $this->params + array( 'limit' => $this->limit + 1, 'offset' => $this->offset ) );
            // url or urlAlias of one item makes the aliases of the whole page (two queries, not two per item)
            $repository = $this->repository;
            $items = $this->fetched;
            $done = false;
            $primer = function () use ( $repository, $items, &$done ) {
                if ( !$done )
                {
                    $done = true;
                    $repository->primeUrlAliases( $items );
                }
            };
            foreach ( $this->fetched as $item )
            {
                if ( $item instanceof expGraphQLContent )
                    $item->urlPrimer = $primer;
            }
        }
        return $this->fetched;
    }

    /** @return expGraphQLContent[] the items of the page */
    public function items()
    {
        return array_slice( $this->fetched(), 0, $this->limit );
    }

    /** @return array[] cursor, node */
    public function edges()
    {
        $edges = array();
        foreach ( $this->items() as $index => $item )
            $edges[] = array( 'cursor' => self::encodeCursor( $this->offset + $index ), 'node' => $item );
        return $edges;
    }

    /** @return array hasNextPage, hasPreviousPage, startCursor, endCursor */
    public function pageInfo()
    {
        $items = $this->items();
        $count = count( $items );
        return array( 'hasNextPage' => count( $this->fetched() ) > $this->limit,
                      'hasPreviousPage' => $this->offset > 0,
                      'startCursor' => $count ? self::encodeCursor( $this->offset ) : null,
                      'endCursor' => $count ? self::encodeCursor( $this->offset + $count - 1 ) : null );
    }

    /** @return int */
    public function totalCount()
    {
        if ( $this->count === null )
            $this->count = $this->location === null ? 0 : $this->repository->childrenCount( $this->location, $this->params );
        return $this->count;
    }
}

?>
