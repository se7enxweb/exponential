<?php
/**
 * ezjscore/call/expsubitems::<service> - the subitems column catalogue and rows (the admin's "Table options"),
 * reusing expSubitemsColumnRegistry, expSubitemsServerFunctions and expSubitemsPreference. The services of the
 * original ezjscServer_expsubitems stay as they are; these are the same data in the expservices envelope, with
 * the columns of a request given as an argument instead of a form field.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expSubitemsServices extends expServiceBase
{
    public static $services = array(
        'catalogue' => array( 'summary' => 'Every column known to the registry (built-in and INI defined), without the per-parent attribute columns',
            'access' => array( 'content', 'read' ), 'write' => false, 'args' => array(), 'returns' => 'list of columns' ),
        'builtin' => array( 'summary' => 'The built-in columns', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of columns' ),
        'describe' => array( 'summary' => 'One column by key', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'key' => 'string', 'parent' => 'int' ), 'returns' => 'column' ),
        'columns' => array( 'summary' => 'The columns the user may see under a parent, defaults, presets and the saved choice', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'parent' => 'int' ), 'returns' => 'parent_node_id, columns, defaults, presets, preference, page_sizes' ),
        'defaults' => array( 'summary' => 'The default column keys under a parent', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'parent' => 'int' ), 'returns' => 'list of keys' ),
        'presets' => array( 'summary' => 'The column presets of the INI', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of id, name, columns' ),
        'pagesizes' => array( 'summary' => 'The page sizes the list offers', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'list of ints' ),
        'sortfields' => array( 'summary' => 'The sort keys a list accepts', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'parent' => 'int' ), 'returns' => 'list of keys: plain sort fields and sortable columns' ),
        'groups' => array( 'summary' => 'The columns grouped by their Group under a parent', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'parent' => 'int' ), 'returns' => 'map group => keys' ),
        'preference' => array( 'summary' => 'The user\'s own saved column choice for the parent\'s navigation part', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'parent' => 'int' ), 'returns' => 'preference' ),
        'rows' => array( 'summary' => 'The children of a parent with the columns asked for (comma list)', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'parent' => 'int', 'columns' => 'list', 'limit' => 'int', 'offset' => 'int', 'sort' => 'string', 'ascending' => 'bool', 'filter' => 'string' ),
            'returns' => 'paged list of nodes with columns { key: { v, h } }' ),
        'settings' => array( 'summary' => 'The registry settings: attribute columns, CSV export, CSV limit', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'attribute_columns, csv_export, csv_limit' ),
    );

    protected static function need()
    {
        if ( !class_exists( 'expSubitemsColumnRegistry' ) )
            throw new expServiceException( 'The subitems columns are not available', 404 );
    }

    protected static function registry()
    {
        self::need();
        return expSubitemsColumnRegistry::instance();
    }

    public static function catalogue( $args )
    {
        self::guard( __FUNCTION__ );
        $registry = self::registry();
        $list = array();
        foreach ( array_keys( $registry->definitions() ) as $key )
        {
            $column = $registry->definedColumn( $key );
            if ( $column !== null )
                $list[] = $registry->describe( $column );
        }
        usort( $list, function ( $a, $b ) { return array( $a['order'], $a['key'] ) <=> array( $b['order'], $b['key'] ); } );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function builtin( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( self::catalogue( array() )['data'] as $c )
            if ( $c['builtin'] )
                $list[] = $c;
        return self::ok( $list );
    }

    public static function describe( $args )
    {
        self::guard( __FUNCTION__ );
        $registry = self::registry();
        $key = self::arg( $args, 0, 'string' );
        $parent = self::node( self::arg( $args, 1, 'int', (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' ) ) );
        $column = $registry->column( $key, $parent );
        if ( $column === null )
            throw new expServiceException( "No column '$key' for this parent", 404 );
        return self::ok( $registry->describe( $column ) );
    }

    public static function columns( $args )
    {
        self::guard( __FUNCTION__ );
        $registry = self::registry();
        $parent = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( expSubitemsServerFunctions::columnsResponse( $parent, $registry, expSubitemsPreference::load() ) );
    }

    public static function defaults( $args )
    {
        self::guard( __FUNCTION__ );
        $registry = self::registry();
        return self::ok( array_values( $registry->defaults( self::node( self::arg( $args, 0, 'int' ) ) ) ) );
    }

    public static function presets( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( self::registry()->presets() as $id => $preset )
            $list[] = array( 'id' => (string)$id, 'name' => expSubitemsColumn::tr( $preset['name'] ), 'columns' => $preset['columns'] );
        return self::ok( $list );
    }

    public static function pagesizes( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array_values( self::registry()->pageSizes() ) );
    }

    public static function sortfields( $args )
    {
        self::guard( __FUNCTION__ );
        $registry = self::registry();
        $parent = self::node( self::arg( $args, 0, 'int' ) );
        $keys = expSubitemsColumn::sortFields();
        foreach ( $registry->availableColumns( $parent ) as $key => $column )
            if ( $column->sortBy() !== false )
                $keys[] = $key;
        return self::ok( array_values( array_unique( $keys ) ) );
    }

    public static function groups( $args )
    {
        self::guard( __FUNCTION__ );
        $registry = self::registry();
        $map = array();
        foreach ( $registry->availableColumns( self::node( self::arg( $args, 0, 'int' ) ) ) as $key => $column )
            $map[(string)$column->setting( 'Group', 'Custom' )][] = $key;
        return self::ok( $map );
    }

    public static function preference( $args )
    {
        self::guard( __FUNCTION__ );
        $registry = self::registry();
        $parent = self::node( self::arg( $args, 0, 'int' ) );
        $defaults = $registry->defaults( $parent );
        return self::ok( expSubitemsPreference::forPart( expSubitemsPreference::load(), expSubitemsPreference::navigationPart( $parent ), $defaults ) );
    }

    public static function rows( $args )
    {
        self::guard( __FUNCTION__ );
        $registry = self::registry();
        $parent = self::node( self::arg( $args, 0, 'int' ) );
        $keys = self::arg( $args, 1, 'list', array() );
        list( $limit, $offset ) = self::paging( $args, 2, 3 );
        $sort = self::arg( $args, 4, 'string', '' );
        $ascending = self::arg( $args, 5, 'bool', false );
        $filter = self::arg( $args, 6, 'string', '' );
        if ( count( $keys ) > 40 )
            throw new expServiceException( 'At most 40 columns', 400 );
        expSubitemsColumn::resetMemo();
        $fetched = expSubitemsServerFunctions::fetchChildren( $parent, $registry, $limit, $offset, $sort, $ascending, $filter );
        $nodes = $fetched['nodes'];
        $columns = $registry->resolveColumns( implode( ',', $keys ), $parent, false );
        $values = expSubitemsServerFunctions::columnValues( $nodes, $columns );
        $items = array();
        foreach ( $nodes as $i => $node )
        {
            $object = $node->object();
            $items[] = array( 'node_id' => (int)$node->attribute( 'node_id' ), 'object_id' => (int)$node->attribute( 'contentobject_id' ),
                              'name' => $node->attribute( 'name' ), 'class' => $object ? $object->attribute( 'class_identifier' ) : '',
                              'priority' => (int)$node->attribute( 'priority' ), 'hidden' => (bool)$node->attribute( 'is_hidden' ),
                              'columns' => isset( $values[$i] ) ? (object)$values[$i] : (object)array() );
        }
        $res = self::page( $items, $fetched['total_count'], $offset, $limit );
        $res['meta']['columns'] = array_keys( $columns );
        $res['meta']['sort'] = $fetched['sort'];
        return $res;
    }

    public static function settings( $args )
    {
        self::guard( __FUNCTION__ );
        $registry = self::registry();
        return self::ok( array( 'attribute_columns' => (bool)$registry->attributeColumnsEnabled(), 'csv_export' => (bool)$registry->csvExportEnabled(),
                                'csv_limit' => (int)$registry->csvLimit() ) );
    }
}
