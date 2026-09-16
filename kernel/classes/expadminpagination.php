<?php
/**
 * File containing the expAdminPagination class.
 *
 * One place to ask how many rows a list in the administration interface shows.
 *
 * The page sizes used to be written into whichever module drew the list, and
 * sometimes into its template as well, so changing one meant editing code and
 * finding every copy of the number. They are settings now, and they are all in
 * one block rather than scattered across a dozen ini files, because the
 * question an administrator asks is "where do I change how long these lists
 * are" and it should have one answer.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

class expAdminPagination
{
    const INI_FILE  = 'admininterface.ini';
    const INI_BLOCK = 'PaginationSettings';

    /** Used when nothing is configured and nothing can be read. */
    const FALLBACK = 25;

    /**
     * How many rows the named list shows at once.
     *
     * $view is the module and view it is drawn by, 'section/list' and the like,
     * so a setting can be found from the address of the page it governs.
     *
     * @param string $view
     * @param int|false $default what to use when the setting names no size
     * @return int
     */
    static function limit( $view, $default = false )
    {
        $ini = eZINI::instance( self::INI_FILE );

        $sizes = $ini->hasVariable( self::INI_BLOCK, 'ItemsPerPage' )
               ? (array)$ini->variable( self::INI_BLOCK, 'ItemsPerPage' )
               : array();

        if ( isset( $sizes[$view] ) && (int)$sizes[$view] > 0 )
            return (int)$sizes[$view];

        if ( $default !== false && (int)$default > 0 )
            return (int)$default;

        if ( $ini->hasVariable( self::INI_BLOCK, 'DefaultItemsPerPage' ) )
        {
            $fallback = (int)$ini->variable( self::INI_BLOCK, 'DefaultItemsPerPage' );
            if ( $fallback > 0 )
                return $fallback;
        }

        return self::FALLBACK;
    }

    /**
     * One page of a list that is already in memory.
     *
     * Second best, and used where the model has no fetch that takes an offset
     * and a limit. It bounds what is drawn - which is what takes a page of
     * hundreds of rows down - but not what is read, so a model that grows a
     * limited fetch should be given one and used instead of this.
     *
     * @param array $items
     * @param int $offset
     * @param int $limit
     * @return array
     */
    static function page( $items, $offset, $limit )
    {
        if ( !is_array( $items ) )
            return array();

        $offset = (int)$offset;
        $limit  = (int)$limit;

        if ( $offset < 0 ) $offset = 0;
        if ( $limit < 1 )  return $items;

        return array_slice( $items, $offset, $limit, true );
    }

    /**
     * The offset a page was asked for, off the address.
     *
     * Modules whose view does not declare an Offset parameter still receive
     * '(offset)/25' among the user parameters, so a list can be paged without
     * changing the module definition it belongs to.
     *
     * @param array $params the module's $Params
     * @param string $name
     * @return int
     */
    static function offset( $params, $name = 'offset' )
    {
        if ( isset( $params['Offset'] ) && $params['Offset'] !== false && $name === 'offset' )
            return max( 0, (int)$params['Offset'] );

        $user = isset( $params['UserParameters'] ) ? (array)$params['UserParameters'] : array();

        return isset( $user[$name] ) ? max( 0, (int)$user[$name] ) : 0;
    }

    /**
     * The sizes a list with an items per page selector offers.
     *
     * The list is also what bounds the query: a size that is not on it cannot
     * be asked for, so lengthening the list is how a longer page is allowed.
     *
     * @param string $view
     * @param array $default used when the setting names none
     * @return int[]
     */
    static function sizes( $view, array $default = array( 10, 25, 50 ) )
    {
        $ini = eZINI::instance( self::INI_FILE );

        $key   = 'ItemsPerPageList_' . str_replace( '/', '_', $view );
        $sizes = $ini->hasVariable( self::INI_BLOCK, $key )
               ? (array)$ini->variable( self::INI_BLOCK, $key )
               : array();

        $clean = array();
        foreach ( $sizes as $size )
        {
            $size = (int)$size;
            if ( $size > 0 && !in_array( $size, $clean, true ) )
                $clean[] = $size;
        }

        // A setting emptied or filled with nonsense still has to answer with a
        // usable size: a list with none in it divides by zero further down.
        return $clean ? $clean : $default;
    }

    /**
     * The size a list with a selector should use, from the viewer's choice.
     *
     * The preference holds the position in the list of sizes counting from one,
     * which is what the administration interface has always stored, so the
     * sizes can be changed without resetting anybody's choice.
     *
     * @param string $view
     * @param string $preference name of the eZPreferences key
     * @param array $default sizes to offer when the setting names none
     * @return array( int $limit, int $choice, int[] $sizes )
     */
    static function chosen( $view, $preference, array $default = array( 10, 25, 50 ) )
    {
        $sizes  = self::sizes( $view, $default );
        $choice = (int)eZPreferences::value( $preference );

        if ( $choice < 1 || $choice > count( $sizes ) )
            $choice = 1;

        return array( $sizes[$choice - 1], $choice, $sizes );
    }
}

?>
