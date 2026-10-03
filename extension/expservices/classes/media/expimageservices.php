<?php
/**
 * ezjscore/call/expimage::<service> - images and their aliases (image.ini), per node and attribute.
 * Reads follow content/read on the node; the writes are POST + form token + content/edit on the node.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expImageServices extends expAttrServiceBase
{
    public static $services = array(
        'aliases' => array( 'summary' => 'The configured image aliases (image.ini AliasList) with size and filters', 'access' => 'public',
            'write' => false, 'args' => array(), 'returns' => 'list of name, width, height, reference, filters' ),
        'alias' => array( 'summary' => 'One configured image alias', 'access' => 'public', 'write' => false,
            'args' => array( 'name' => 'string' ), 'returns' => 'alias definition' ),
        'aliasnames' => array( 'summary' => 'Only the names of the aliases', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'list of strings' ),
        'formats' => array( 'summary' => 'The image MIME types the converters support', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'list of MIME types' ),
        'filters' => array( 'summary' => 'The image filters (image.ini [ImageMagick]/[GD]) known to the manager', 'access' => 'public',
            'write' => false, 'args' => array(), 'returns' => 'list of filter names' ),
        'quality' => array( 'summary' => 'The configured output quality per MIME type', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'map of mime => quality' ),
        'attributes' => array( 'summary' => 'The image attributes of a node with their original file', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int' ), 'returns' => 'list of attribute, has_content, original' ),
        'info' => array( 'summary' => 'The original image of a node attribute: file name, size, dimensions, MIME type, alt text', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'image information' ),
        'url' => array( 'summary' => 'The URL of one alias of a node image (generated when missing)', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string', 'alias' => 'string' ), 'returns' => 'url, absolute_url, width, height, mime_type' ),
        'urls' => array( 'summary' => 'The URLs of every configured alias of a node image', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'map alias => url description' ),
        'generate' => array( 'summary' => 'Makes sure an alias file exists for a node image (derived data, no content change)', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string', 'alias' => 'string' ), 'returns' => 'url, is_valid' ),
        'alt' => array( 'summary' => 'The alternative text of a node image', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'alternative_text' ),
        'files' => array( 'summary' => 'The image files registered for the object of a node (ezimagefile)', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of files' ),
        'list' => array( 'summary' => 'The image objects below a parent node with their original and one alias', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'parent' => 'int', 'limit' => 'int', 'offset' => 'int', 'alias' => 'string' ), 'returns' => 'paged list of image nodes' ),
        'search' => array( 'summary' => 'Image objects by name below a parent', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'text' => 'string', 'parent' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of image nodes' ),
        'stats' => array( 'summary' => 'How many image files and images the installation holds', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array(), 'returns' => 'files, objects, attributes, formats' ),
        'bymime' => array( 'summary' => 'Image file counts per MIME type of the original files', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array(), 'returns' => 'list of mime_type, count' ),
        'purge' => array( 'summary' => 'Removes the alias files of a node image (they are generated again on demand)', 'access' => array( 'content', 'edit' ),
            'write' => true, 'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'purged' ),
    );

    // ------------------------------------------------------------------ configuration

    protected static function manager()
    {
        return eZImageManager::factory();
    }

    protected static function exportAlias( $name, $alias )
    {
        $filters = array();
        foreach ( isset( $alias['filters'] ) && is_array( $alias['filters'] ) ? $alias['filters'] : array() as $f )
            $filters[] = is_array( $f ) ? ( isset( $f['name'] ) ? $f['name'] . ( !empty( $f['data'] ) ? '=' . implode( ',', (array)$f['data'] ) : '' ) : json_encode( $f ) ) : (string)$f;
        $width = isset( $alias['width'] ) ? $alias['width'] : null;
        $height = isset( $alias['height'] ) ? $alias['height'] : null;
        foreach ( isset( $alias['filters'] ) && is_array( $alias['filters'] ) ? $alias['filters'] : array() as $f )
            if ( $width === null && is_array( $f ) && isset( $f['name'], $f['data'] ) && strpos( $f['name'], 'geometry/' ) === 0 && count( (array)$f['data'] ) >= 2 )
            {
                $d = array_values( (array)$f['data'] );
                $width = (int)$d[0];
                $height = (int)$d[1];
            }
        return array( 'name' => $name, 'reference' => isset( $alias['reference'] ) && $alias['reference'] ? $alias['reference'] : null,
                      'width' => $width, 'height' => $height,
                      'mime_type' => isset( $alias['mime_type'] ) ? $alias['mime_type'] : null, 'filters' => $filters );
    }

    public static function aliases( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( self::manager()->aliasList() as $name => $alias )
            $list[] = self::exportAlias( $name, $alias );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function alias( $args )
    {
        self::guard( __FUNCTION__ );
        $name = self::arg( $args, 0, 'string' );
        $manager = self::manager();
        if ( !$manager->hasAlias( $name ) )
            throw new expServiceException( "No image alias '$name'", 404 );
        return self::ok( self::exportAlias( $name, $manager->alias( $name ) ) );
    }

    public static function aliasnames( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array_keys( self::manager()->aliasList() ) );
    }

    public static function formats( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = eZINI::instance( 'image.ini' );
        $list = $ini->hasVariable( 'MIMETypeSettings', 'ConversionRules' ) ? (array)$ini->variable( 'MIMETypeSettings', 'ConversionRules' ) : array();
        $supported = array();
        foreach ( array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif', 'image/svg+xml', 'image/bmp', 'image/tiff' ) as $mime )
            $supported[$mime] = true;
        return self::ok( array( 'conversion_rules' => array_values( $list ), 'common' => array_keys( $supported ) ) );
    }

    public static function filters( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = eZINI::instance( 'image.ini' );
        $names = array();
        foreach ( array( 'ImageMagick', 'GD', 'ImageConverterSettings' ) as $group )
            if ( $ini->hasGroup( $group ) )
                foreach ( array( 'Filters', 'Filter' ) as $setting )
                    if ( $ini->hasVariable( $group, $setting ) )
                        foreach ( (array)$ini->variable( $group, $setting ) as $k => $v )
                            $names[] = is_string( $k ) ? $k : strtok( (string)$v, '=' );
        return self::ok( array_values( array_unique( $names ) ) );
    }

    public static function quality( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = eZINI::instance( 'image.ini' );
        $q = $ini->hasVariable( 'MIMETypeSettings', 'Quality' ) ? (array)$ini->variable( 'MIMETypeSettings', 'Quality' ) : array();
        $map = array();
        foreach ( $q as $row )
        {
            $parts = explode( ';', (string)$row );
            if ( count( $parts ) >= 2 )
                $map[$parts[0]] = (int)$parts[1];
        }
        return self::ok( $map );
    }

    // ------------------------------------------------------------------ one node's images

    /** @return array array( node, attribute, handler ) */
    protected static function target( $args, $nodeIndex = 0, $function = 'read' )
    {
        $node = self::node( self::arg( $args, $nodeIndex, 'int' ), $function );
        $attribute = self::attributeOf( $node, 'ezimage', self::arg( $args, $nodeIndex + 1, 'string', null ) );
        $handler = $attribute->content();
        if ( !$handler instanceof eZImageAliasHandler )
            throw new expServiceException( 'The attribute has no image', 404 );
        return array( $node, $attribute, $handler );
    }

    protected static function exportVariation( $v )
    {
        if ( !is_array( $v ) )
            return null;
        $url = isset( $v['url'] ) ? $v['url'] : '';
        return array( 'url' => $url, 'absolute_url' => $url !== '' ? self::absolute( $url ) : '',
                      'width' => isset( $v['width'] ) ? (int)$v['width'] : 0, 'height' => isset( $v['height'] ) ? (int)$v['height'] : 0,
                      'mime_type' => isset( $v['mime_type'] ) ? $v['mime_type'] : '',
                      'filename' => isset( $v['filename'] ) ? $v['filename'] : '',
                      'filesize' => isset( $v['filesize'] ) ? (int)$v['filesize'] : 0,
                      'alias_key' => isset( $v['alias_key'] ) ? $v['alias_key'] : null,
                      'is_valid' => isset( $v['is_valid'] ) ? (bool)$v['is_valid'] : true );
    }

    protected static function describeAttribute( eZContentObjectAttribute $attribute )
    {
        $handler = $attribute->content();
        $has = $attribute->hasContent() && $handler instanceof eZImageAliasHandler;
        return array( 'attribute' => $attribute->contentClassAttributeIdentifier(), 'attribute_id' => (int)$attribute->attribute( 'id' ),
                      'has_content' => $has,
                      'original' => $has ? self::exportVariation( $handler->attribute( 'original' ) ) : null,
                      'alternative_text' => $has ? (string)$handler->attribute( 'alternative_text' ) : '' );
    }

    public static function attributes( $args )
    {
        self::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $list = array();
        foreach ( self::attributesOf( $node, 'ezimage' ) as $attribute )
            $list[] = self::describeAttribute( $attribute );
        return self::ok( $list, array( 'node' => (int)$node->attribute( 'node_id' ) ) );
    }

    public static function info( $args )
    {
        self::guard( __FUNCTION__ );
        list( $node, $attribute ) = self::target( $args );
        return self::ok( array_merge( self::exportNode( $node ), self::describeAttribute( $attribute ) ) );
    }

    public static function alt( $args )
    {
        self::guard( __FUNCTION__ );
        list( , , $handler ) = self::target( $args );
        return self::ok( array( 'alternative_text' => (string)$handler->attribute( 'alternative_text' ) ) );
    }

    /** @throws expServiceException 404 when the alias is not configured */
    protected static function aliasVariation( eZImageAliasHandler $handler, $alias )
    {
        if ( !self::manager()->hasAlias( $alias ) )
            throw new expServiceException( "No image alias '$alias'", 404 );
        $v = $handler->attribute( $alias );
        $out = self::exportVariation( $v );
        if ( $out === null )
            throw new expServiceException( "The image has no '$alias' variation", 404 );
        return $out;
    }

    public static function url( $args )
    {
        self::guard( __FUNCTION__ );
        list( , , $handler ) = self::target( $args );
        return self::ok( self::aliasVariation( $handler, self::arg( $args, 2, 'string' ) ) );
    }

    public static function generate( $args )
    {
        self::guard( __FUNCTION__ );
        list( , , $handler ) = self::target( $args );
        $v = self::aliasVariation( $handler, self::arg( $args, 2, 'string' ) );
        $v['exists'] = $v['url'] !== '' && is_file( ltrim( $v['url'], '/' ) );
        return self::ok( $v );
    }

    public static function urls( $args )
    {
        self::guard( __FUNCTION__ );
        list( , , $handler ) = self::target( $args );
        $map = array();
        foreach ( array_keys( self::manager()->aliasList() ) as $name )
        {
            $v = self::exportVariation( $handler->attribute( $name ) );
            if ( $v !== null )
                $map[$name] = $v;
        }
        return self::ok( $map, array( 'count' => count( $map ) ) );
    }

    public static function files( $args )
    {
        self::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $id = (int)$node->attribute( 'contentobject_id' );
        $total = self::scalar( "SELECT COUNT(*) AS n FROM ezimagefile WHERE contentobject_attribute_id IN ( SELECT id FROM ezcontentobject_attribute WHERE contentobject_id=$id AND data_type_string='ezimage' )" );
        $rows = self::rows( "SELECT id, contentobject_attribute_id, filepath FROM ezimagefile WHERE contentobject_attribute_id IN ( SELECT id FROM ezcontentobject_attribute WHERE contentobject_id=$id AND data_type_string='ezimage' ) ORDER BY id",
                            array( 'limit' => $limit, 'offset' => $offset ) );
        $items = array();
        foreach ( $rows as $r )
            $items[] = array( 'id' => (int)$r['id'], 'attribute_id' => (int)$r['contentobject_attribute_id'], 'path' => $r['filepath'] );
        return self::page( $items, $total, $offset, $limit );
    }

    protected static function exportImageNode( eZContentObjectTreeNode $node, $alias )
    {
        $out = self::exportNode( $node );
        $out['image'] = null;
        foreach ( self::attributesOf( $node, 'ezimage' ) as $attribute )
        {
            $handler = $attribute->content();
            if ( $attribute->hasContent() && $handler instanceof eZImageAliasHandler )
            {
                $out['image'] = array( 'attribute' => $attribute->contentClassAttributeIdentifier(),
                                       'original' => self::exportVariation( $handler->attribute( 'original' ) ),
                                       'alias' => $alias, 'variation' => self::manager()->hasAlias( $alias ) ? self::exportVariation( $handler->attribute( $alias ) ) : null,
                                       'alternative_text' => (string)$handler->attribute( 'alternative_text' ) );
                break;
            }
        }
        return $out;
    }

    public static function list( $args )
    {
        self::guard( 'list' );
        return self::fetchList( $args, 'list', null );
    }

    protected static function fetchList( $args, $method, $text )
    {
        $i = $text === null ? 0 : 1;
        $parent = self::arg( $args, $i, 'int', (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' ) );
        list( $limit, $offset ) = self::paging( $args, $i + 1, $i + 2 );
        $alias = self::arg( $args, $i + 3, 'string', 'small' );
        $extra = array( 'LoadDataMap' => true );
        if ( $text !== null )
            $extra['AttributeFilter'] = array( array( 'name', 'like', '*' . $text . '*' ) );
        list( $nodes, $total ) = self::nodesBelow( $parent, self::classList( 'expservices.ini', 'Media', 'ImageClasses', array( 'image' ) ), $limit, $offset, $extra );
        $items = array();
        foreach ( $nodes as $node )
            $items[] = self::exportImageNode( $node, $alias );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function search( $args )
    {
        self::guard( __FUNCTION__ );
        $text = trim( self::arg( $args, 0, 'string' ) );
        if ( strlen( $text ) < 2 )
            throw new expServiceException( 'The search text needs at least 2 characters', 422 );
        return self::fetchList( $args, 'search', $text );
    }

    public static function stats( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array(
            'files' => self::scalar( 'SELECT COUNT(*) AS n FROM ezimagefile' ),
            'attributes' => self::scalar( "SELECT COUNT(*) AS n FROM ezcontentobject_attribute WHERE data_type_string='ezimage' AND version IN ( SELECT current_version FROM ezcontentobject WHERE id=contentobject_id )" ),
            'objects' => self::scalar( "SELECT COUNT(DISTINCT contentobject_id) AS n FROM ezcontentobject_attribute WHERE data_type_string='ezimage'" ),
            'aliases' => count( self::manager()->aliasList() ) ) );
    }

    public static function bymime( $args )
    {
        self::guard( __FUNCTION__ );
        $counts = array();
        foreach ( self::rows( 'SELECT filepath FROM ezimagefile', array( 'limit' => 20000 ) ) as $r )
        {
            $ext = strtolower( pathinfo( $r['filepath'], PATHINFO_EXTENSION ) );
            $counts[$ext] = isset( $counts[$ext] ) ? $counts[$ext] + 1 : 1;
        }
        arsort( $counts );
        $list = array();
        foreach ( $counts as $ext => $n )
            $list[] = array( 'extension' => $ext, 'count' => $n );
        return self::ok( $list );
    }

    public static function purge( $args )
    {
        self::guard( __FUNCTION__ );
        list( $node, $attribute, $handler ) = self::target( $args, 0, 'edit' );
        $handler->purgeAllAliases();
        return self::ok( array( 'node' => (int)$node->attribute( 'node_id' ), 'attribute' => $attribute->contentClassAttributeIdentifier(), 'purged' => true ) );
    }
}
