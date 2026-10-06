<?php
/**
 * File containing the expBookmarkPage class.
 *
 * What the bookmark page (content/bookmark) shows, worked out from plain arrays: the folders with their counts,
 * which bookmarks a folder, a search and an order select, how they are grouped by folder and paged, the overview
 * figures, and the address that keeps all of it. Nothing here reads the database or the session, so every rule can
 * be tested without an installation (tests/tests/kernel/classes/bookmarks/expBookmarkPageTest.php). The view
 * (kernel/private/classes/views/content/bookmark.php) reads the rows and the nodes and hands them over.
 *
 * Guide: doc/guides/bookmarks.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expBookmarkPage
{
    /// The orders, the first is the default: the user's own order (the one of the Bookmarks box), then by name,
    /// the most recently added first (bookmarks have no date of their own: a later bookmark has a higher id),
    /// by type (class name, then name), and the most recently modified item first.
    const SORTS = array( 'own', 'name', 'added', 'type', 'modified' );

    /// The longest search text taken, in characters.
    const MAX_SEARCH_LENGTH = 100;

    /// The scope "every bookmark" (a folder id is a positive number, 0 is "not in a folder").
    const SCOPE_ALL = -1;

    /**
     * @param mixed $raw the (sort) parameter of the address
     * @return string one of SORTS
     */
    static function sortKey( $raw )
    {
        return ( is_string( $raw ) && in_array( $raw, self::SORTS, true ) ) ? $raw : self::SORTS[0];
    }

    /**
     * The search text: trimmed, inner white space collapsed, at most MAX_SEARCH_LENGTH characters.
     *
     * @param mixed $raw
     * @return string
     */
    static function searchText( $raw )
    {
        if ( !is_string( $raw ) )
            return '';
        $text = trim( preg_replace( '/\s+/u', ' ', $raw ) ?? '' );
        if ( function_exists( 'mb_substr' ) )
            return mb_substr( $text, 0, self::MAX_SEARCH_LENGTH, 'UTF-8' );
        return substr( $text, 0, self::MAX_SEARCH_LENGTH );
    }

    /**
     * The scope the (folder) parameter asks for: SCOPE_ALL, 0 for the bookmarks that are in no folder ("top"),
     * or the id of one of the user's folders. Anything else (another user's folder, a removed one, garbage) is
     * SCOPE_ALL.
     *
     * @param mixed $raw
     * @param array $folders the result of folders()
     * @return int
     */
    static function scope( $raw, array $folders )
    {
        if ( $raw === 'top' || $raw === 0 || $raw === '0' )
            return 0;
        if ( ( is_int( $raw ) || ( is_string( $raw ) && ctype_digit( $raw ) ) ) && isset( $folders[(int) $raw] ) )
            return (int) $raw;
        return self::SCOPE_ALL;
    }

    /**
     * The folders of the user, keyed by id in display order (depth first), from the rows of
     * eZContentBrowseBookmarkFolder::fetchRowsForUser(). Each is
     *   array( 'id', 'name', 'parent_id', 'depth', 'path' (names above it), 'count' (bookmarks inside, with the
     *          subfolders), 'direct' (bookmarks right in it), 'subfolders' (folders below it, at any depth),
     *          'children' (ids of the folders right in it), 'subtree' (its id and the ids of all folders below it) )
     *
     * @param array $rows
     * @return array
     */
    static function folders( array $rows )
    {
        $folders = array();
        foreach ( $rows as $row )
        {
            if ( $row['type'] !== 'folder' )
                continue;
            $id = (int) $row['id'];
            $folders[$id] = array( 'id' => $id, 'name' => (string) $row['name'], 'parent_id' => (int) $row['parent_id'],
                                   'depth' => (int) $row['depth'], 'path' => array_values( (array) $row['path'] ),
                                   'count' => (int) $row['count'], 'direct' => 0, 'subfolders' => 0,
                                   'children' => array(), 'subtree' => array( $id ) );
        }
        // a parent that is not in the list (damaged data) is shown at the top level by the tree; do the same here
        foreach ( $folders as $id => $folder )
            if ( $folder['parent_id'] && !isset( $folders[$folder['parent_id']] ) )
                $folders[$id]['parent_id'] = 0;
        foreach ( $folders as $id => $folder )
            if ( $folder['parent_id'] )
                $folders[$folder['parent_id']]['children'][] = $id;
        foreach ( $rows as $row )
            if ( $row['type'] === 'bookmark' && isset( $folders[(int) $row['folder_id']] ) )
                ++$folders[(int) $row['folder_id']]['direct'];
        foreach ( array_keys( $folders ) as $id )
        {
            $subtree = array();
            $queue = array( $id );
            while ( $queue )
            {
                $next = array_shift( $queue );
                if ( isset( $subtree[$next] ) )
                    continue;
                $subtree[$next] = $next;
                foreach ( $folders[$next]['children'] as $child )
                    $queue[] = $child;
            }
            $folders[$id]['subtree'] = array_values( $subtree );
            $folders[$id]['subfolders'] = count( $subtree ) - 1;
        }
        return $folders;
    }

    /**
     * One bookmark row of fetchRowsForUser() with what is known of its node.
     *
     * @param array $row a bookmark row ('id', 'name', 'folder_id', 'bookmark' => eZContentBrowseBookmark or array)
     * @param array|null $node null when the node was not found, else
     *        array( 'name', 'class_identifier', 'class_name', 'is_hidden', 'is_invisible', 'modified', 'path' (names),
     *               'is_container', 'contentobject_id', optionally 'can_read' )
     * @param int $position the place of the row in the user's own order
     * @return array
     */
    static function item( array $row, $node, $position )
    {
        $bookmark = $row['bookmark'];
        $nodeID = (int) ( is_object( $bookmark ) ? $bookmark->attribute( 'node_id' ) : $bookmark['node_id'] );
        $state = 'ok';
        if ( $node && array_key_exists( 'can_read', $node ) && !$node['can_read'] )
        {
            // the user may no longer read it: nothing of the node is shown or searched, only the bookmark's own name
            $node = null;
            $state = 'denied';
        }
        else if ( !$node )
            $state = 'gone';
        else if ( !empty( $node['is_hidden'] ) )
            $state = 'hidden';
        else if ( !empty( $node['is_invisible'] ) )
            $state = 'invisible';
        return array( 'id' => (int) $row['id'],
                      'node_id' => $nodeID,
                      'folder_id' => (int) $row['folder_id'],
                      'position' => (int) $position,
                      'name' => ( $node && (string) $node['name'] !== '' ) ? (string) $node['name'] : (string) $row['name'],
                      'class_identifier' => $node ? (string) $node['class_identifier'] : '',
                      'class_name' => $node ? (string) $node['class_name'] : '',
                      'modified' => $node ? (int) $node['modified'] : 0,
                      'path' => $node ? array_values( (array) $node['path'] ) : array(),
                      'is_container' => $node ? (bool) $node['is_container'] : false,
                      'contentobject_id' => $node ? (int) $node['contentobject_id'] : 0,
                      'state' => $state );
    }

    /**
     * The items of every bookmark row, in the user's own order.
     *
     * @param array $rows fetchRowsForUser()
     * @param array $nodes node arrays (see item()) keyed by node id
     * @return array
     */
    static function items( array $rows, array $nodes )
    {
        $items = array();
        $position = 0;
        foreach ( $rows as $row )
        {
            if ( $row['type'] !== 'bookmark' )
                continue;
            $bookmark = $row['bookmark'];
            $nodeID = (int) ( is_object( $bookmark ) ? $bookmark->attribute( 'node_id' ) : $bookmark['node_id'] );
            $items[] = self::item( $row, isset( $nodes[$nodeID] ) ? $nodes[$nodeID] : null, $position++ );
        }
        return $items;
    }

    /**
     * Whether an item matches the search text: its name, its type, the names of the items above it, or the folder
     * it is in. Upper and lower case are the same.
     */
    static function matches( array $item, $search, array $folders )
    {
        if ( $search === '' )
            return true;
        $haystack = array( $item['name'], $item['class_name'], implode( ' / ', $item['path'] ) );
        if ( isset( $folders[$item['folder_id']] ) )
            $haystack[] = implode( ' / ', array_merge( $folders[$item['folder_id']]['path'], array( $folders[$item['folder_id']]['name'] ) ) );
        $needle = self::lower( $search );
        foreach ( $haystack as $text )
            if ( $text !== '' && strpos( self::lower( $text ), $needle ) !== false )
                return true;
        return false;
    }

    /**
     * The bookmarks of a scope that match a search, grouped by folder (the bookmarks in no folder first, then the
     * folders in display order) and in the asked order within each group.
     *
     * @return array the items, each with 'group' (folder id, 0 for none)
     */
    static function select( array $items, array $folders, $scope, $search, $sort )
    {
        $sort = self::sortKey( $sort );
        $inScope = array();
        if ( $scope > 0 && isset( $folders[$scope] ) )
            foreach ( $folders[$scope]['subtree'] as $id )
                $inScope[$id] = true;

        $groups = array( 0 => array() );
        foreach ( array_keys( $folders ) as $id )
            $groups[$id] = array();
        foreach ( $items as $item )
        {
            $group = isset( $folders[$item['folder_id']] ) ? $item['folder_id'] : 0;
            if ( $scope === 0 && $group !== 0 )
                continue;
            if ( $scope > 0 && !isset( $inScope[$group] ) )
                continue;
            if ( !self::matches( $item, $search, $folders ) )
                continue;
            $item['group'] = $group;
            $groups[$group][] = $item;
        }
        $list = array();
        foreach ( $groups as $group )
        {
            usort( $group, function ( $a, $b ) use ( $sort ) { return expBookmarkPage::compare( $a, $b, $sort ); } );
            foreach ( $group as $item )
                $list[] = $item;
        }
        return $list;
    }

    /**
     * The comparison of two items in an order; ties fall back to the user's own order, so the result is stable.
     */
    static function compare( array $a, array $b, $sort )
    {
        switch ( $sort )
        {
            case 'name':
                $c = strnatcasecmp( $a['name'], $b['name'] );
                break;
            case 'added':
                $c = $b['id'] <=> $a['id'];
                break;
            case 'type':
                // the items without a type (not found) last
                $c = ( $a['class_name'] === '' ) <=> ( $b['class_name'] === '' );
                if ( !$c )
                    $c = strnatcasecmp( $a['class_name'], $b['class_name'] );
                if ( !$c )
                    $c = strnatcasecmp( $a['name'], $b['name'] );
                break;
            case 'modified':
                $c = $b['modified'] <=> $a['modified'];
                break;
            default:
                $c = 0;
        }
        return $c ? $c : ( $a['position'] <=> $b['position'] );
    }

    /**
     * One page of a selection, each item with 'group_start' (the first of its group on this page), 'group_count'
     * (the items of its group in the whole selection) and 'group_continued' (its group began on an earlier page).
     *
     * @return array( 'items' => ..., 'offset' => the offset used (moved back to 0 when it lay behind the end), 'count' )
     */
    static function page( array $list, $offset, $limit )
    {
        $count = count( $list );
        $limit = max( 1, (int) $limit );
        $offset = max( 0, (int) $offset );
        if ( $offset >= $count )
            $offset = 0;
        $groupCount = array();
        foreach ( $list as $item )
            $groupCount[$item['group']] = ( isset( $groupCount[$item['group']] ) ? $groupCount[$item['group']] : 0 ) + 1;
        $page = array_slice( $list, $offset, $limit );
        $previous = null;
        foreach ( $page as $i => $item )
        {
            $start = $item['group'] !== $previous;
            $page[$i]['group_start'] = $start;
            $page[$i]['group_count'] = $groupCount[$item['group']];
            $page[$i]['group_continued'] = $start && $i === 0 && $offset > 0 && $list[$offset - 1]['group'] === $item['group'];
            $previous = $item['group'];
        }
        return array( 'items' => $page, 'offset' => $offset, 'count' => $count );
    }

    /**
     * The overview figures of every bookmark of the user.
     *
     * @return array( 'bookmarks', 'folders', 'unfiled', 'hidden' (hidden or under a hidden item), 'gone' (not found, or no longer readable) )
     */
    static function summary( array $items, array $folders )
    {
        $summary = array( 'bookmarks' => count( $items ), 'folders' => count( $folders ), 'unfiled' => 0, 'hidden' => 0, 'gone' => 0 );
        foreach ( $items as $item )
        {
            if ( !isset( $folders[$item['folder_id']] ) )
                ++$summary['unfiled'];
            if ( $item['state'] === 'hidden' || $item['state'] === 'invisible' )
                ++$summary['hidden'];
            else if ( $item['state'] === 'gone' || $item['state'] === 'denied' )
                ++$summary['gone'];
        }
        return $summary;
    }

    /**
     * Where an entry lands when it moves one place up (-1) or down (+1) among its siblings: the id to put it in
     * front of (0 is the end), or false when it is first (up) or last (down) already or not among them.
     *
     * @param int[] $siblingIDs the entries of the folder in their order
     */
    static function shiftBefore( array $siblingIDs, $id, $direction )
    {
        $siblingIDs = array_values( array_map( 'intval', $siblingIDs ) );
        $at = array_search( (int) $id, $siblingIDs, true );
        if ( $at === false )
            return false;
        if ( $direction < 0 )
            return $at === 0 ? false : $siblingIDs[$at - 1];
        if ( $at === count( $siblingIDs ) - 1 )
            return false;
        return isset( $siblingIDs[$at + 2] ) ? $siblingIDs[$at + 2] : 0;
    }

    /**
     * The ids of an order posted by the page: "3,1,2" or an array; positive whole numbers, each once.
     *
     * @return int[]|false false for anything else (a repeated id, a word, nothing)
     */
    static function orderIDs( $raw )
    {
        $parts = is_array( $raw ) ? $raw : ( is_string( $raw ) ? explode( ',', $raw ) : array() );
        $ids = array();
        foreach ( $parts as $part )
        {
            $part = is_scalar( $part ) ? trim( (string) $part ) : '';
            if ( $part === '' || !ctype_digit( $part ) || (int) $part < 1 || strlen( $part ) > 10 )
                return false;
            if ( isset( $ids[(int) $part] ) )
                return false;
            $ids[(int) $part] = (int) $part;
        }
        return $ids ? array_values( $ids ) : false;
    }

    /**
     * A new order of a folder from a posted order of some of its entries: the posted entries take the places they
     * held in the folder, in the posted order; every other entry keeps its place. So a page that shows a part of a
     * folder can send just the order of what it shows.
     *
     * @param int[] $current the ids of the folder in their order
     * @param int[] $posted  some of those ids in the wanted order
     * @return int[]|false the whole new order, or false when a posted id is not in the folder or is posted twice
     */
    static function reorderSlots( array $current, array $posted )
    {
        $current = array_values( array_map( 'intval', $current ) );
        $posted = array_values( array_map( 'intval', $posted ) );
        if ( !$posted || count( array_unique( $posted ) ) !== count( $posted ) )
            return false;
        $slots = array();
        foreach ( $current as $index => $id )
            if ( in_array( $id, $posted, true ) )
                $slots[] = $index;
        if ( count( $slots ) !== count( $posted ) )
            return false; // a posted id is not in the folder
        $order = $current;
        foreach ( $slots as $n => $index )
            $order[$index] = $posted[$n];
        return $order;
    }

    /**
     * A new order of a folder with one entry moved to a position (1 is the first; out of range is the nearest end).
     *
     * @return int[]|false false when the entry is not in the folder
     */
    static function moveToPosition( array $current, $id, $position )
    {
        $current = array_values( array_map( 'intval', $current ) );
        $at = array_search( (int) $id, $current, true );
        if ( $at === false )
            return false;
        $position = max( 1, min( count( $current ), (int) $position ) );
        array_splice( $current, $at, 1 );
        array_splice( $current, $position - 1, 0, array( (int) $id ) );
        return $current;
    }

    /**
     * The place of every bookmark in its folder, in the user's own order: id => array( 'position' (1 is the first),
     * 'count' (the bookmarks of the folder) ). Bookmarks of a folder that is not there count as not in a folder.
     */
    static function folderPositions( array $items, array $folders )
    {
        $byFolder = array();
        foreach ( $items as $item )
            $byFolder[isset( $folders[$item['folder_id']] ) ? $item['folder_id'] : 0][] = $item['id'];
        $positions = array();
        foreach ( $byFolder as $ids )
            foreach ( $ids as $index => $id )
                $positions[$id] = array( 'position' => $index + 1, 'count' => count( $ids ) );
        return $positions;
    }

    /**
     * The address of the page in a scope, order, search and offset, built only from checked values (a number, a
     * word of SORTS, an encoded search), so it is always a path of this view.
     */
    static function path( $scope, $sort = 'own', $search = '', $offset = 0 )
    {
        $path = 'content/bookmark';
        if ( $scope === 0 )
            $path .= '/(folder)/top';
        else if ( $scope > 0 )
            $path .= '/(folder)/' . (int) $scope;
        $sort = self::sortKey( $sort );
        if ( $sort !== self::SORTS[0] )
            $path .= '/(sort)/' . $sort;
        if ( (int) $offset > 0 )
            $path .= '/(offset)/' . (int) $offset;
        $search = self::searchText( $search );
        if ( $search !== '' )
            $path .= '?q=' . rawurlencode( $search );
        return $path;
    }

    /**
     * The folders a bookmark can be moved to, for a select: every folder with its depth and its path in words.
     *
     * @return array of array( 'id', 'name', 'depth', 'label' )
     */
    static function targets( array $folders, array $exclude = array() )
    {
        $list = array();
        foreach ( $folders as $folder )
        {
            if ( in_array( $folder['id'], $exclude, true ) )
                continue;
            $list[] = array( 'id' => $folder['id'], 'name' => $folder['name'], 'depth' => $folder['depth'],
                             'label' => implode( ' / ', array_merge( $folder['path'], array( $folder['name'] ) ) ) );
        }
        return $list;
    }

    /**
     * The selected bookmark ids of a form: positive whole numbers, each once.
     */
    static function selectedIDs( $raw )
    {
        $ids = array();
        foreach ( (array) $raw as $id )
            if ( is_scalar( $id ) && ctype_digit( (string) $id ) && (int) $id > 0 )
                $ids[(int) $id] = (int) $id;
        return array_values( $ids );
    }

    /**
     * A shift button's value, "up-12" or "down-12" for bookmarks, "fup-3" or "fdown-3" for folders.
     *
     * @return array( 'type' => 'bookmark'|'folder', 'id' => int, 'direction' => -1|1 ) or false
     */
    static function shiftRequest( $raw )
    {
        if ( !is_string( $raw ) || !preg_match( '/^(f?)(up|down)-([1-9][0-9]{0,9})$/', $raw, $m ) )
            return false;
        return array( 'type' => $m[1] === 'f' ? 'folder' : 'bookmark', 'id' => (int) $m[3], 'direction' => $m[2] === 'up' ? -1 : 1 );
    }

    private static function lower( $text )
    {
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );
    }
}
