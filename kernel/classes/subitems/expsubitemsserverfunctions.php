<?php
/**
 * The ezjscore server functions of the admin subitems list ("Table options"), registered as
 * [ezjscServer_expsubitems] in extension/ezjscore/settings/ezjscore.ini:
 *
 *   expsubitems::columns::<parent node id>
 *       the columns the user may see under the parent, the defaults there, the presets and the
 *       user's saved choice
 *   expsubitems::rows::<parent>::<limit>::<offset>::<sort key>::<order 0|1>[::<name filter>]
 *       with POST/GET columns=<key,key,...>: the ezjscnode::subtree response (same list items)
 *       plus per item columns: { <key>: { v: value, h: html } } for the requested non-built-in
 *       keys the user may see
 *   expsubitems::savepreference[::<parent node id>]
 *       POST preference=<json>: saves the user's choice (see expSubitemsPreference)
 *
 * Every function needs a logged-in user with content/read who can read the parent.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsServerFunctions extends ezjscServerFunctions
{
    /**
     * Never cached by the packer.
     */
    public static function getCacheTime( $functionName )
    {
        return -1;
    }

    /**
     * The registry used by the functions (replaceable in tests).
     *
     * @var expSubitemsColumnRegistry|null
     */
    public static $registry = null;

    /** @return expSubitemsColumnRegistry */
    protected static function registry()
    {
        return self::$registry !== null ? self::$registry : expSubitemsColumnRegistry::instance();
    }

    /**
     * The parent node, after the access checks every function makes.
     *
     * @param mixed $parentNodeID
     * @return eZContentObjectTreeNode
     * @throws InvalidArgumentException
     */
    public static function parentNode( $parentNodeID )
    {
        self::requireReader();
        if ( !is_numeric( $parentNodeID ) || (int)$parentNodeID < 1 )
            throw new InvalidArgumentException( 'Parent node id is not valid' );
        $parent = eZContentObjectTreeNode::fetch( (int)$parentNodeID );
        if ( !$parent instanceof eZContentObjectTreeNode || !$parent->canRead() )
            throw new InvalidArgumentException( "Parent node '" . (int)$parentNodeID . "' is not available" );
        return $parent;
    }

    /**
     * The checks every function makes before anything else: a logged-in user with content/read.
     *
     * @throws InvalidArgumentException
     */
    public static function requireReader()
    {
        if ( !eZUser::currentUser()->isRegistered() )
            throw new InvalidArgumentException( 'You need to log in' );
        if ( !expSubitemsColumnRegistry::hasAccess( 'content', 'read' ) )
            throw new InvalidArgumentException( 'No access to content/read' );
    }

    /**
     * expsubitems::columns::<parent node id>
     *
     * @param array $args
     * @return array
     */
    public static function columns( $args )
    {
        $parent = self::parentNode( isset( $args[0] ) ? $args[0] : null );
        return self::columnsResponse( $parent, self::registry(), expSubitemsPreference::load() );
    }

    /**
     * The columns response for a parent.
     *
     * @param eZContentObjectTreeNode $parent
     * @param expSubitemsColumnRegistry $registry
     * @param array $pref the user's stored preference
     * @return array
     */
    public static function columnsResponse( eZContentObjectTreeNode $parent, expSubitemsColumnRegistry $registry, array $pref )
    {
        $available = $registry->availableColumns( $parent );

        $columns = array();
        foreach ( $available as $column )
            $columns[] = $registry->describe( $column );

        $onlyAvailable = function ( array $keys ) use ( $available ) {
            return array_values( array_filter( $keys, function ( $key ) use ( $available ) { return isset( $available[$key] ); } ) );
        };

        $defaults = $onlyAvailable( $registry->defaults( $parent ) );

        $presets = array();
        foreach ( $registry->presets() as $id => $preset )
            $presets[] = array( 'id' => (string)$id, 'name' => expSubitemsColumn::tr( $preset['name'] ),
                                'columns' => $onlyAvailable( $preset['columns'] ), 'source' => 'ini' );
        foreach ( expSubitemsPreference::userPresets( $pref ) as $preset )
        {
            $preset['columns'] = $onlyAvailable( $preset['columns'] );
            $presets[] = $preset;
        }

        $preference = expSubitemsPreference::forPart( $pref, expSubitemsPreference::navigationPart( $parent ), $defaults );
        $preference['visible'] = $onlyAvailable( $preference['visible'] );

        return array(
            'parent_node_id' => (int)$parent->attribute( 'node_id' ),
            'columns' => $columns,
            'defaults' => $defaults,
            'presets' => $presets,
            'preference' => $preference,
            'page_sizes' => $registry->pageSizes(),
            'csv_export' => $registry->csvExportEnabled(),
        );
    }

    /**
     * The column keys the request asks for: POST or GET columns=a,b,c.
     *
     * @return string
     */
    public static function requestedKeys()
    {
        $http = eZHTTPTool::instance();
        if ( $http->hasPostVariable( 'columns' ) )
            $value = $http->postVariable( 'columns' );
        else if ( $http->hasGetVariable( 'columns' ) )
            $value = $http->getVariable( 'columns' );
        else
            return '';
        if ( is_array( $value ) )
            $value = implode( ',', array_filter( $value, 'is_string' ) );
        return is_string( $value ) ? $value : '';
    }

    /**
     * expsubitems::rows::<parent>::<limit>::<offset>::<sort key>::<order 0|1>[::<name filter>]
     *
     * @param array $args
     * @return array
     */
    public static function rows( $args )
    {
        $parent = self::parentNode( isset( $args[0] ) ? $args[0] : null );
        // a persistent worker serves many requests: no value may come from an earlier one
        expSubitemsColumn::resetMemo();
        $registry = self::registry();

        $limit = isset( $args[1] ) && is_numeric( $args[1] ) ? max( 1, (int)$args[1] ) : 25;
        $offset = isset( $args[2] ) && is_numeric( $args[2] ) ? max( 0, (int)$args[2] ) : 0;
        $sortKey = isset( $args[3] ) ? (string)$args[3] : '';
        $ascending = isset( $args[4] ) ? (bool)(int)$args[4] : false;
        $nameFilter = isset( $args[5] ) ? (string)$args[5] : '';

        $hardLimit = (int)eZINI::instance( 'ezjscore.ini' )->variable( 'ezjscServer_ezjscnode', 'HardLimit' );
        if ( $hardLimit > 0 && $limit > $hardLimit )
            $limit = $hardLimit;

        $fetched = self::fetchChildren( $parent, $registry, $limit, $offset, $sortKey, $ascending, $nameFilter );
        $nodes = $fetched['nodes'];
        $columns = $registry->resolveColumns( self::requestedKeys(), $parent, false );

        // the same encoding as ezjscServerFunctionsNode::subTree, so the list items are identical
        $list = $nodes ? ezjscAjaxContent::nodeEncode( $nodes, array( 'formatDate' => 'shortdatetime',
                                                                      'fetchThumbPreview' => true,
                                                                      'fetchSection' => true,
                                                                      'fetchCreator' => true,
                                                                      'fetchClassIcon' => true ), 'raw' )
                       : array();

        $values = self::columnValues( $nodes, $columns );
        foreach ( $list as $i => $item )
            $list[$i]['columns'] = $values[$i];

        $parentNodeID = (int)$parent->attribute( 'node_id' );
        unset( $parent );

        return array( 'parent_node_id' => $parentNodeID,
                      'count' => count( $nodes ),
                      'total_count' => $fetched['total_count'],
                      'list' => $list,
                      'limit' => $limit,
                      'offset' => $offset,
                      'sort' => $fetched['sort'],
                      'order' => $ascending ? 1 : 0,
                      'sort_key' => $fetched['sort_key'],
                      'class_filter' => $fetched['class_filter'],
                      'columns' => array_keys( $columns ) );
    }

    /**
     * Fetches a page of the parent's children the way ezjscnode::subtree does (depth 1,
     * content/read applied by subTree()), sorted by a column key.
     *
     * @param eZContentObjectTreeNode $parent
     * @param expSubitemsColumnRegistry $registry
     * @param int $limit
     * @param int $offset
     * @param string $sortKey
     * @param bool $ascending
     * @param string $nameFilter
     * @return array array( 'nodes', 'total_count', 'sort', 'sort_key', 'class_filter' )
     */
    public static function fetchChildren( eZContentObjectTreeNode $parent, expSubitemsColumnRegistry $registry,
                                          $limit, $offset, $sortKey, $ascending, $nameFilter = '' )
    {
        $sort = $registry->sortFor( $sortKey, $ascending, $parent );

        $params = array( 'Depth' => 1,
                         'Limit' => (int)$limit,
                         'Offset' => (int)$offset,
                         'SortBy' => $sort['sort_by'],
                         'DepthOperator' => 'eq',
                         'ObjectNameFilter' => $nameFilter,
                         'AsObject' => true );
        if ( $sort['class_filter'] !== null )
        {
            $params['ClassFilterType'] = 'include';
            $params['ClassFilterArray'] = array( $sort['class_filter'] );
        }

        $count = $parent->subTreeCount( $params );
        $nodes = $count ? $parent->subTree( $params ) : array();

        $first = isset( $sort['sort_by'][0][0] ) ? $sort['sort_by'][0][0] : 'published';
        return array( 'nodes' => is_array( $nodes ) ? array_values( $nodes ) : array(),
                      'total_count' => (int)$count,
                      'sort' => $sort['key'] !== null ? $sort['key'] : $first,
                      'sort_key' => $sort['key'],
                      'class_filter' => $sort['class_filter'] );
    }

    /**
     * Lets every column load what it reads for the whole page at once (expSubitemsColumn::prefetch()).
     * A prefetch that fails only costs speed: the column then loads per row, as without it.
     *
     * @param eZContentObjectTreeNode[] $nodes
     * @param expSubitemsColumn[] $columns key => column
     */
    public static function prefetch( array $nodes, array $columns )
    {
        if ( !$nodes )
            return;
        foreach ( $columns as $key => $column )
        {
            try
            {
                $column->prefetch( $nodes );
            }
            catch ( Throwable $e )
            {
                eZDebug::writeWarning( "Column '$key': prefetch: " . $e->getMessage(), __METHOD__ );
            }
        }
    }

    /**
     * The value and HTML of one cell; a column that throws gives null and '' (and a debug
     * warning), so the other cells go on.
     *
     * @param expSubitemsColumn $column
     * @param eZContentObjectTreeNode $node
     * @return array array( 'v' => JSON-safe value, 'h' => html )
     */
    public static function cell( expSubitemsColumn $column, eZContentObjectTreeNode $node )
    {
        try
        {
            $value = $column->value( $node );
            $html = $column->html( $node, $value );
        }
        catch ( Exception $e )
        {
            eZDebug::writeWarning( "Column '" . $column->key() . "': " . $e->getMessage(), __METHOD__ );
            $value = null;
            $html = '';
        }
        return array( 'v' => self::jsonSafe( $value ), 'h' => (string)$html );
    }

    /**
     * The values and HTML of the given columns for each node, nothing else computed; the columns
     * load what they need for all the nodes first (prefetch()). A column that throws gives null
     * for that cell (and a debug warning), the others go on.
     *
     * @param eZContentObjectTreeNode[] $nodes
     * @param expSubitemsColumn[] $columns key => column
     * @return array per node (same index): stdClass { <key>: { v, h } }
     */
    public static function columnValues( array $nodes, array $columns )
    {
        $nodes = array_values( $nodes );
        self::prefetch( $nodes, $columns );
        $result = array();
        foreach ( $nodes as $i => $node )
        {
            $cells = new stdClass();
            foreach ( $columns as $key => $column )
                $cells->$key = self::cell( $column, $node );
            $result[$i] = $cells;
        }
        return $result;
    }

    /**
     * A value reduced to null, scalars and lists/maps of them.
     */
    protected static function jsonSafe( $value )
    {
        if ( $value === null || is_scalar( $value ) )
            return $value;
        if ( is_array( $value ) )
        {
            $out = array();
            foreach ( $value as $k => $v )
                $out[$k] = self::jsonSafe( $v );
            return $out;
        }
        if ( is_object( $value ) && method_exists( $value, '__toString' ) )
            return (string)$value;
        return null;
    }

    /**
     * expsubitems::savepreference[::<parent node id>], POST preference=<json>
     *
     * @param array $args
     * @return array the saved choice for the parent's navigation part (as columns returns it)
     */
    public static function savePreference( $args )
    {
        $http = eZHTTPTool::instance();
        if ( !$http->hasPostVariable( 'preference' ) )
            throw new InvalidArgumentException( 'POST preference=<json> is missing' );

        $parent = null;
        if ( isset( $args[0] ) && $args[0] !== '' )
            $parent = self::parentNode( $args[0] );
        else
            self::requireReader();

        $input = json_decode( (string)$http->postVariable( 'preference' ), true );
        if ( !is_array( $input ) )
            throw new InvalidArgumentException( 'preference is not a JSON object' );

        $registry = self::registry();
        $part = expSubitemsPreference::navigationPart( $parent );
        $pref = expSubitemsPreference::merge( expSubitemsPreference::load(), $input, $registry, $part );
        expSubitemsPreference::store( $pref );

        $defaults = $parent !== null ? $registry->defaults( $parent ) : array();
        return array( 'saved' => true,
                      'part' => $part,
                      'preference' => expSubitemsPreference::forPart( $pref, $part, $defaults ),
                      'presets' => expSubitemsPreference::userPresets( $pref ) );
    }
}
