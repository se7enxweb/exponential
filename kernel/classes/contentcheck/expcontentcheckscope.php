<?php
/**
 * File containing the expContentCheckScope class.
 *
 * The start location of the content checks (doc/features/6.0/content-checks.md, "Where a check starts"): any node,
 * from the whole tree (node 1) to one folder. A check takes it from its filter form (GET NodeID), else from the
 * address (contentcheck/<view>/<node id>), else from the signed-in user's preference admin_contentcheck_scope,
 * else the content tree's top node (content.ini [NodeSettings] RootNode). A node given in the form or the address
 * becomes the preference, so the next check opens where the last one was.
 *
 * The presets (contentcheck.ini [ScopeSettings] PresetList[]) name the usual places: the whole tree, the content,
 * Media, Media/Images and Users; any other node is chosen with the shared node picker (content/browse) through the
 * view contentcheck/scope. The views that take a start location are listed in ViewList[].
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentCheckScope
{
    /** The preference that keeps the last start location of the signed-in user */
    const PREFERENCE = 'admin_contentcheck_scope';

    /** The action name of the node picker */
    const BROWSE_ACTION = 'ContentCheckScope';

    /** The presets without a setting */
    const DEFAULT_PRESETS = array( 'tree', 'content', 'media', 'media/images', 'users' );

    /** The views without a setting */
    const DEFAULT_VIEWS = array( 'unreferenced', 'brokenrelations', 'storage' );

    /** @var array|null preference value for tests (false: none) */
    protected static $testPreference = null;

    /**
     * The views that take a start location (and that the node picker may return to).
     *
     * @return string[]
     */
    public static function views()
    {
        $views = expContentCheck::setting( 'ScopeSettings', 'ViewList', self::DEFAULT_VIEWS );
        $views = is_array( $views ) ? $views : array();
        $clean = array();
        foreach ( $views as $view )
        {
            if ( is_string( $view ) && preg_match( '/^[a-z][a-z0-9_]{0,40}$/', $view ) && !in_array( $view, $clean, true ) )
                $clean[] = $view;
        }
        return $clean;
    }

    /**
     * @param mixed $view
     * @return string|null the view when it takes a start location
     */
    public static function view( $view )
    {
        return is_string( $view ) && in_array( $view, self::views(), true ) ? $view : null;
    }

    /**
     * The start location of a request: the form's node, else the address's, else the preference, else $default.
     * Only whole positive numbers are read.
     *
     * @param mixed $addressNodeID the view parameter NodeID (null: none)
     * @param mixed $formNodeID GET NodeID (null: none)
     * @param int|null $default null: content.ini RootNode
     * @return array array( 'node_id' => int, 'given' => bool: from the form or the address )
     */
    public static function choose( $addressNodeID, $formNodeID, $default = null )
    {
        foreach ( array( $formNodeID, $addressNodeID ) as $value )
        {
            // one whole number and nothing else ("1 OR 1=1" is no node id)
            if ( is_scalar( $value ) && preg_match( '/^\s*[0-9]{1,10}\s*$/', (string)$value ) && (int)$value > 0 && (int)$value <= 2147483647 )
                return array( 'node_id' => (int)$value, 'given' => true );
        }
        $preference = self::preference();
        if ( $preference > 0 )
            return array( 'node_id' => $preference, 'given' => false );
        return array( 'node_id' => $default === null ? self::rootNodeID() : (int)$default, 'given' => false );
    }

    /**
     * Shortcut of choose() that also remembers a node given in the form or the address.
     *
     * @param mixed $addressNodeID
     * @param mixed $formNodeID
     * @return int
     */
    public static function resolve( $addressNodeID, $formNodeID )
    {
        $choice = self::choose( $addressNodeID, $formNodeID );
        if ( $choice['given'] )
            self::remember( $choice['node_id'] );
        return $choice['node_id'];
    }

    /**
     * The content tree's top node (content.ini [NodeSettings] RootNode).
     *
     * @return int
     */
    public static function rootNodeID()
    {
        return self::iniNode( 'RootNode', 2 );
    }

    /**
     * @param string $name a setting of content.ini [NodeSettings]
     * @param int $default
     * @return int
     */
    protected static function iniNode( $name, $default )
    {
        $ini = eZINI::instance( 'content.ini' );
        $value = $ini->hasVariable( 'NodeSettings', $name ) ? (int)$ini->variable( 'NodeSettings', $name ) : (int)$default;
        return $value > 0 ? $value : (int)$default;
    }

    /**
     * The remembered start location of the signed-in user, 0 for none.
     *
     * @return int
     */
    public static function preference()
    {
        if ( self::$testPreference !== null )
            return self::$testPreference === false ? 0 : (int)self::$testPreference;
        if ( !class_exists( 'eZPreferences' ) || !eZUser::isCurrentUserRegistered() )
            return 0;
        $ids = expContentCheck::idList( (string)eZPreferences::value( self::PREFERENCE ), 1 );
        return $ids ? $ids[0] : 0;
    }

    /**
     * Remembers a start location for the signed-in user (nothing for anonymous, nothing when unchanged).
     *
     * @param int $nodeID
     * @return bool whether it was stored
     */
    public static function remember( $nodeID )
    {
        $nodeID = (int)$nodeID;
        if ( self::$testPreference !== null )
        {
            self::$testPreference = $nodeID;
            return true;
        }
        if ( $nodeID <= 0 || !eZUser::isCurrentUserRegistered() || self::preference() === $nodeID )
            return false;
        eZPreferences::setValue( self::PREFERENCE, (string)$nodeID );
        return true;
    }

    /**
     * @param int|false|null $value a remembered node for tests, false for none, null for the real preference
     */
    public static function setTestPreference( $value )
    {
        self::$testPreference = $value;
    }

    /**
     * The presets as configured: key => how its node is found ('node:<id>', 'ini:<setting>' or 'path:<url alias>').
     *
     * @return array
     */
    public static function presetSources()
    {
        $list = expContentCheck::setting( 'ScopeSettings', 'PresetList', self::DEFAULT_PRESETS );
        $sources = array();
        foreach ( is_array( $list ) ? $list : array() as $key )
        {
            $source = self::presetSource( $key );
            if ( $source !== null )
                $sources[(string)$key] = $source;
        }
        return $sources;
    }

    /**
     * How the node of a preset is found.
     *
     * @param mixed $key tree | content | media | users | setup | design | a node id | a URL alias path (media/images)
     * @return string|null
     */
    public static function presetSource( $key )
    {
        if ( !is_string( $key ) || $key === '' || strlen( $key ) > 200 )
            return null;
        $named = array( 'tree' => 'node:1', 'content' => 'ini:RootNode', 'media' => 'ini:MediaRootNode',
                        'users' => 'ini:UserRootNode', 'setup' => 'ini:SetupRootNode', 'design' => 'ini:DesignRootNode' );
        if ( isset( $named[$key] ) )
            return $named[$key];
        if ( preg_match( '/^[0-9]{1,10}$/', $key ) && (int)$key > 0 )
            return 'node:' . (int)$key;
        if ( preg_match( '#^[A-Za-z0-9_\-]+(/[A-Za-z0-9_\-]+)*$#', $key ) && !preg_match( '/^[0-9]+$/', $key ) )
            return 'path:' . strtolower( $key );
        return null;
    }

    /**
     * The node id of a preset source; 0 when it cannot be found.
     *
     * @param string $source from presetSource()
     * @return int
     */
    public static function presetNodeID( $source )
    {
        list( $type, $value ) = explode( ':', (string)$source, 2 );
        if ( $type === 'node' )
            return (int)$value;
        if ( $type === 'ini' )
            return self::iniNode( $value, 0 );
        if ( $type === 'path' )
        {
            $nodeID = eZURLAliasML::fetchNodeIDByPath( $value );
            return $nodeID ? (int)$nodeID : 0;
        }
        return 0;
    }

    /**
     * What the scope selector (design:contentcheck/scope.tpl) shows: the node, its path, the presets the user may
     * read, and the address of the node picker.
     *
     * @param int $nodeID the start location
     * @param string $view the view the selector belongs to (its links and the picker return there)
     * @return array hash( node_id, node_name, path, view, presets => list of hash( key, node_id, name, current ), browse_url )
     */
    public static function forTemplate( $nodeID, $view )
    {
        $nodeID = (int)$nodeID;
        $view = self::view( $view ) ?: '';
        $node = expContentCheck::readableNode( $nodeID );
        $scope = array( 'node_id' => $nodeID, 'node_name' => self::nodeName( $node ), 'path' => self::nodePath( $node ),
                        'view' => $view, 'presets' => array(),
                        'browse_url' => $view !== '' ? '/contentcheck/scope/' . $view . '/' . $nodeID : '' );
        $seen = array();
        foreach ( self::presetSources() as $key => $source )
        {
            $presetID = self::presetNodeID( $source );
            if ( $presetID <= 0 || isset( $seen[$presetID] ) )
                continue;
            $presetNode = expContentCheck::readableNode( $presetID );
            if ( !$presetNode )
                continue;
            $seen[$presetID] = true;
            $scope['presets'][] = array( 'key' => (string)$key, 'node_id' => $presetID, 'name' => self::nodeName( $presetNode ),
                                         'path' => self::nodePath( $presetNode ), 'current' => $presetID === $nodeID );
        }
        return $scope;
    }

    /**
     * @param eZContentObjectTreeNode|null $node
     * @return string
     */
    protected static function nodeName( $node )
    {
        if ( !$node instanceof eZContentObjectTreeNode )
            return '';
        if ( (int)$node->attribute( 'node_id' ) === 1 )
            return ezpI18n::tr( 'kernel/contentcheck', 'Whole tree' );
        return (string)$node->attribute( 'name' );
    }

    /**
     * The names of the node and the nodes above it, without the root, e.g. "Media / Images".
     *
     * @param eZContentObjectTreeNode|null $node
     * @return string
     */
    protected static function nodePath( $node )
    {
        if ( !$node instanceof eZContentObjectTreeNode || (int)$node->attribute( 'node_id' ) === 1 )
            return '';
        $names = array();
        $ids = array_slice( array_filter( explode( '/', (string)$node->attribute( 'path_string' ) ) ), 1 );
        foreach ( $ids as $id )
        {
            $item = (int)$id === (int)$node->attribute( 'node_id' ) ? $node : eZContentObjectTreeNode::fetch( (int)$id );
            if ( $item instanceof eZContentObjectTreeNode && $item->canRead() )
                $names[] = (string)$item->attribute( 'name' );
        }
        return implode( ' / ', $names );
    }
}

?>
