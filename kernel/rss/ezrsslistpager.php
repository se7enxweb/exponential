<?php
/**
 * File containing the eZRSSListPager class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */


if ( !class_exists( 'eZRSSListPager', false ) ) {
/**
 * Works out one page of a list, and the links that reach the other pages.
 *
 * The RSS list shows two lists on one page, so the arithmetic is done here
 * once rather than twice in the view, and the view file stays free of function
 * definitions - it is included afresh on every request and a plain function in
 * it could not be declared twice.
 *
 * Nothing here touches the database. It is given a total and returns which
 * slice to ask for, so it can be tested on its own.
 */
class eZRSSListPager
{
    /**
     * The page sizes offered, smallest first. The first is the default.
     *
     * @return array of int
     */
    /**
     * The page sizes this list offers, from
     * content.ini [RSSListSettings] ItemsPerPageList.
     *
     * Configured rather than written here, so a site can offer the sizes its
     * own editors want. The list is also what bounds the query: limit() will
     * not return a size that is not on it, whatever the url asks for.
     *
     * @return int[]
     */
    public static function limits()
    {
        $ini = eZINI::instance( 'content.ini' );

        $limits = $ini->hasVariable( 'RSSListSettings', 'ItemsPerPageList' )
                ? (array)$ini->variable( 'RSSListSettings', 'ItemsPerPageList' )
                : array();

        $clean = array();
        foreach ( $limits as $limit )
        {
            $limit = (int)$limit;
            if ( $limit > 0 && !in_array( $limit, $clean, true ) )
                $clean[] = $limit;
        }

        // A setting emptied or filled with nonsense must not leave the list
        // with no page size at all, which would divide by zero further down.
        return $clean ? $clean : array( 25, 50, 250 );
    }

    /**
     * Settles on a page size.
     *
     * A size asked for in the URL wins, then one remembered from last time,
     * then the smallest offered. Anything else - a missing value, a word, a
     * number nobody offered - is ignored rather than obeyed, so the page can
     * never be talked into fetching the whole table.
     *
     * @param mixed $requested  what the URL asked for, or false.
     * @param mixed $remembered what the user chose last time, or false.
     * @return int one of limits().
     */
    public static function limit( $requested, $remembered = false )
    {
        $limits = self::limits();

        foreach ( array( $requested, $remembered ) as $candidate )
        {
            if ( $candidate === false || $candidate === null || $candidate === '' )
                continue;
            if ( !is_numeric( $candidate ) )
                continue;
            if ( in_array( (int) $candidate, $limits, true ) )
                return (int) $candidate;
        }

        return $limits[0];
    }

    /**
     * Keeps an offset inside the list.
     *
     * Deleting the last rows of the last page, or an offset typed by hand,
     * would otherwise leave the list looking empty when it is not. The offset
     * is also pulled back onto a page boundary, so page 3 of 50 is row 100 and
     * never row 101.
     *
     * @param mixed $offset the offset asked for.
     * @param int   $limit  the page size.
     * @param int   $count  how many rows there are in total.
     * @return int an offset that exists.
     */
    public static function offset( $offset, $limit, $count )
    {
        if ( !is_numeric( $offset ) || $offset < 0 )
            $offset = 0;

        $offset = (int) $offset;
        $limit  = max( 1, (int) $limit );
        $count  = max( 0, (int) $count );

        if ( $count === 0 )
            return 0;

        if ( $offset >= $count )
            $offset = ( (int) ceil( $count / $limit ) - 1 ) * $limit;

        return (int) ( floor( $offset / $limit ) * $limit );
    }

    /**
     * Everything the template needs to draw the navigator.
     *
     * @param int    $count      rows in the whole list.
     * @param int    $limit      rows per page.
     * @param int    $offset     first row of the page being shown.
     * @param string $offsetName the url parameter this list pages with, so the
     *                           two lists on the page can move independently.
     * @param string $suffix     the other url parameters, already written out,
     *                           that every link must carry along.
     * @param int    $window     how many numbered pages to show around the
     *                           current one.
     * @return array
     */
    public static function data( $count, $limit, $offset, $offsetName = 'offset', $suffix = '', $window = 9 )
    {
        $count  = max( 0, (int) $count );
        $limit  = max( 1, (int) $limit );
        $offset = self::offset( $offset, $limit, $count );

        $pageCount = (int) ceil( $count / $limit );
        $page      = $pageCount > 0 ? (int) floor( $offset / $limit ) : 0;

        // A window of numbered pages around this one, held to $window entries
        // even at either end of the list, so the navigator does not change
        // width as it is paged through.
        $half  = (int) floor( $window / 2 );
        $first = max( 0, $page - $half );
        $last  = min( $pageCount - 1, $first + $window - 1 );
        $first = max( 0, $last - $window + 1 );

        $pages = array();
        for ( $i = $first; $i <= $last; $i++ )
        {
            $pages[] = array( 'number'  => $i + 1,
                              'offset'  => $i * $limit,
                              'current' => $i === $page );
        }

        return array(
            'count'       => $count,
            'limit'       => $limit,
            'offset'      => $offset,
            'offset_name' => $offsetName,
            'suffix'      => $suffix,
            'page'        => $page + 1,
            'page_count'  => $pageCount,
            'pages'       => $pages,
            // 1-based, for "Showing 26 to 50 of 4001".
            'from'        => $count > 0 ? $offset + 1 : 0,
            'to'          => min( $count, $offset + $limit ),
            // Offsets are always numbers, and whether a step exists is always a
            // separate flag: offset 0 is a real page, and a template asking
            // whether 0 is false would refuse to link back to it.
            'first'        => 0,
            'previous'     => $page > 0 ? ( $page - 1 ) * $limit : 0,
            'next'         => $page + 1 < $pageCount ? ( $page + 1 ) * $limit : $offset,
            'last'         => $pageCount > 0 ? ( $pageCount - 1 ) * $limit : 0,
            'has_previous' => $page > 0,
            'has_next'     => $page + 1 < $pageCount,
            'needed'       => $pageCount > 1 );
    }

    /**
     * Settles on a column to sort by, and which way round.
     *
     * The column has to be one the list offers. A name that is not on the list
     * is ignored rather than passed to the database, so a sort order typed into
     * the address bar can only ever reorder the list, never reach past it.
     *
     * @param mixed $field     the column asked for, or false.
     * @param mixed $direction 'asc' or 'desc'; anything else means ascending.
     * @param array $allowed   the columns this list can be sorted by.
     * @param string $default  the column to use when nothing valid was asked for.
     * @return array field, direction, opposite, and the sort clause to fetch with.
     */
    public static function sort( $field, $direction, array $allowed, $default )
    {
        if ( !is_string( $field ) || !in_array( $field, $allowed, true ) )
            $field = $default;

        $direction = is_string( $direction ) && strtolower( $direction ) === 'desc' ? 'desc' : 'asc';

        // Rows that tie on the sorted column would otherwise come back in an
        // order the database is free to change between pages, so the same row
        // could show up twice, or not at all, while paging. The id settles it.
        $sorts = array( $field => $direction );
        if ( $field !== 'id' )
            $sorts['id'] = 'asc';

        return array( 'field'     => $field,
                      'direction' => $direction,
                      'opposite'  => $direction === 'asc' ? 'desc' : 'asc',
                      'sorts'     => $sorts );
    }

    /**
     * The page's state, minus the parameters a particular link sets itself.
     *
     * A sort link sets the column and the direction and drops the offset -
     * page nine of the old order means nothing in the new one - but it still
     * has to carry the other list's position, or that list would jump back to
     * its first page.
     *
     * @param array $state the whole page's parameters, name => value.
     * @param array $names the ones this link writes itself.
     * @return string
     */
    public static function suffixExcept( array $state, array $names )
    {
        foreach ( $names as $name )
            unset( $state[$name] );

        return self::suffix( $state );
    }

    /**
     * Writes url parameters out in the form the module reads them back in.
     *
     * Values that are empty, or that are the list's own starting point, are
     * left out so the first page's address stays short.
     *
     * @param array $parameters name => value.
     * @return string
     */
    public static function suffix( array $parameters )
    {
        $suffix = '';
        foreach ( $parameters as $name => $value )
        {
            if ( $value === false || $value === null || $value === '' || $value === 0 || $value === '0' )
                continue;
            $suffix .= '/(' . $name . ')/' . $value;
        }
        return $suffix;
    }
}
}

