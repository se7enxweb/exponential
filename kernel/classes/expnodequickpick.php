<?php
/**
 * File containing the expNodeQuickPick class.
 *
 * Quick location buttons (doc/eng-us/features/6.0/quick-location-buttons.md): a few well-known places of the content tree,
 * found from a short key (Websites, Media, a node id, a node's remote id or a URL alias path) and the content.ini
 * settings rather than fixed node ids, plus a row of the children of one of them in the tree's own order. The content
 * checks' start location (expContentCheckScope) and the upload page's target location (ezmultiupload) draw their
 * buttons from it; each page decides what a button does and which places it may offer.
 *
 * DB-less parts (source(), label(), row(), state()) take and give plain values; nodeID() and childRows() read the tree.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expNodeQuickPick
{
    /** How many children of a node are read at most for a row */
    const ROW_SCAN = 200;

    /** The named places and how each is found */
    const NAMED = array( 'tree' => 'node:1', 'content' => 'ini:RootNode', 'media' => 'ini:MediaRootNode',
                         'users' => 'ini:UserRootNode', 'setup' => 'ini:SetupRootNode', 'design' => 'ini:DesignRootNode' );

    /**
     * How the node of a key is found.
     *
     * @param mixed $key tree | content | media | users | setup | design | a node id | remote:<node remote id> |
     *                   a URL alias path (media/images)
     * @return string|null 'node:<id>', 'ini:<content.ini [NodeSettings] setting>', 'remote:<remote id>' or
     *                     'path:<url alias>'; null for anything else
     */
    public static function source( $key )
    {
        if ( !is_string( $key ) || $key === '' || strlen( $key ) > 200 )
            return null;
        if ( isset( self::NAMED[$key] ) )
            return self::NAMED[$key];
        if ( preg_match( '/^[0-9]{1,10}$/', $key ) && (int)$key > 0 && (int)$key <= 2147483647 )
            return 'node:' . (int)$key;
        if ( preg_match( '/^remote:([A-Za-z0-9_\-]{1,100})$/', $key, $m ) )
            return 'remote:' . $m[1];
        if ( preg_match( '#^[A-Za-z0-9_\-]+(/[A-Za-z0-9_\-]+)*$#', $key ) && !preg_match( '/^[0-9]+$/', $key ) )
            return 'path:' . strtolower( $key );
        return null;
    }

    /**
     * The node id of a source; 0 when it cannot be found.
     *
     * @param string $source from source()
     * @return int
     */
    public static function nodeID( $source )
    {
        $parts = explode( ':', (string)$source, 2 );
        if ( count( $parts ) !== 2 )
            return 0;
        list( $type, $value ) = $parts;
        if ( $type === 'node' )
            return max( 0, (int)$value );
        if ( $type === 'ini' )
        {
            $ini = eZINI::instance( 'content.ini' );
            $id = $ini->hasVariable( 'NodeSettings', $value ) ? (int)$ini->variable( 'NodeSettings', $value ) : 0;
            return max( 0, $id );
        }
        if ( $type === 'remote' )
        {
            $node = eZContentObjectTreeNode::fetchByRemoteID( $value );
            return $node instanceof eZContentObjectTreeNode ? (int)$node->attribute( 'node_id' ) : 0;
        }
        if ( $type === 'path' )
        {
            $nodeID = eZURLAliasML::fetchNodeIDByPath( $value );
            return $nodeID ? (int)$nodeID : 0;
        }
        return 0;
    }

    /**
     * The label of a button: a name for the named places (the whole tree, Websites, Media, Users, Setup, Design), the
     * node's own name for anything else.
     *
     * @param mixed $key
     * @param string $nodeName
     * @return string
     */
    public static function label( $key, $nodeName )
    {
        switch ( is_string( $key ) ? $key : '' )
        {
            case 'tree': return ezpI18n::tr( 'kernel/contentcheck', 'Whole tree' );
            case 'content': return ezpI18n::tr( 'kernel/contentcheck', 'Websites' );
            case 'media': return ezpI18n::tr( 'kernel/contentcheck', 'Media' );
            case 'users': return ezpI18n::tr( 'kernel/contentcheck', 'Users' );
            case 'setup': return ezpI18n::tr( 'kernel/contentcheck', 'Setup' );
            case 'design': return ezpI18n::tr( 'kernel/contentcheck', 'Design' );
        }
        return (string)$nodeName;
    }

    /**
     * Whether a button's node is the current one, or the current one lies below it.
     *
     * @param int $nodeID the button's node
     * @param int $currentID the current node
     * @param string $currentPath the current node's path_string ('' unknown)
     * @return array hash( current => bool, within => bool )
     */
    public static function state( $nodeID, $currentID, $currentPath )
    {
        $nodeID = (int)$nodeID;
        $current = $nodeID > 0 && $nodeID === (int)$currentID;
        return array( 'current' => $current,
                      'within' => $nodeID > 0 && !$current && strpos( (string)$currentPath, '/' . $nodeID . '/' ) !== false );
    }

    /**
     * A row of buttons from the children of a node, already in their sort order: the visible ones only (not hidden,
     * not invisible) that $accept takes, the first $limit of them, and whether there are more.
     *
     * @param array $children list of hash( node_id, name, is_hidden, is_invisible ), more keys are passed to $accept
     * @param string $currentPath the path_string of the current node ('' unknown)
     * @param int $currentID the current node
     * @param int $limit
     * @param callable|null $accept function( array $child ): bool, e.g. a permission check; null takes every child
     * @return array hash( items => list of hash( node_id, name, current, within ), more => bool )
     */
    public static function row( array $children, $currentPath, $currentID, $limit, $accept = null )
    {
        $items = array();
        $more = false;
        $limit = max( 1, (int)$limit );
        $seen = array();
        foreach ( $children as $child )
        {
            if ( !is_array( $child ) || !isset( $child['node_id'] ) || (int)$child['node_id'] <= 0 || isset( $seen[(int)$child['node_id']] )
                 || !empty( $child['is_hidden'] ) || !empty( $child['is_invisible'] ) )
                continue;
            $id = (int)$child['node_id'];
            $seen[$id] = true;
            if ( $accept !== null && !call_user_func( $accept, $child ) )
                continue;
            if ( count( $items ) >= $limit )
            {
                $more = true;
                break;
            }
            $items[] = array( 'node_id' => $id, 'name' => isset( $child['name'] ) ? (string)$child['name'] : '' )
                       + self::state( $id, $currentID, $currentPath );
        }
        return array( 'items' => $items, 'more' => $more );
    }

    /**
     * The children of a node in the node's own sort order, as rows for row() (the user's read access applies); each
     * row also carries the node as 'node' for an $accept check.
     *
     * @param eZContentObjectTreeNode $parent
     * @param int $scan at most this many
     * @return array
     */
    public static function childRows( eZContentObjectTreeNode $parent, $scan = self::ROW_SCAN )
    {
        $children = $parent->subTree( array( 'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => max( 1, (int)$scan ),
                                             'SortBy' => $parent->sortArray(), 'IgnoreVisibility' => true,
                                             'LoadDataMap' => false, 'AsObject' => true ) );
        $rows = array();
        foreach ( is_array( $children ) ? $children : array() as $child )
        {
            if ( $child instanceof eZContentObjectTreeNode )
                $rows[] = array( 'node_id' => (int)$child->attribute( 'node_id' ), 'name' => (string)$child->attribute( 'name' ),
                                 'is_hidden' => (int)$child->attribute( 'is_hidden' ), 'is_invisible' => (int)$child->attribute( 'is_invisible' ),
                                 'node' => $child );
        }
        return $rows;
    }
}

?>
