<?php
/**
 * ezjscore/call/expmedia::<service> - audio and video media files (the ezmedia datatype): player settings,
 * file information, URLs, listings and statistics.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expMediaServices extends expAttrServiceBase
{
    public static $services = array(
        'attributes' => array( 'summary' => 'The media attributes of a node', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'list of attribute, has_content, media' ),
        'info' => array( 'summary' => 'One media file of a node: file, MIME type, player dimensions and flags', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'media information' ),
        'player' => array( 'summary' => 'Only the player settings (width, height, controls, autoplay, loop, quality)', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'player settings' ),
        'url' => array( 'summary' => 'The download/stream URL of a node media file', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'url, absolute_url' ),
        'mimegroup' => array( 'summary' => 'The MIME group (audio, video, ...) of a MIME type', 'access' => 'public',
            'write' => false, 'args' => array( 'mime' => 'string' ), 'returns' => 'group' ),
        'safename' => array( 'summary' => 'Whether a media file name is acceptable and its safe form', 'access' => 'user',
            'write' => false, 'args' => array( 'filename' => 'string' ), 'returns' => 'safe, safe_name' ),
        'list' => array( 'summary' => 'The media objects below a parent node', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'parent' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of media nodes' ),
        'byobject' => array( 'summary' => 'The media rows stored for an object (all versions)', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'object' => 'int' ), 'returns' => 'list of media rows' ),
        'stats' => array( 'summary' => 'Number of media files and per MIME group', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array(), 'returns' => 'files, groups' ),
        'bymime' => array( 'summary' => 'Media file counts per MIME type', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array(), 'returns' => 'list of mime_type, count' ),
    );

    protected static function target( $args )
    {
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $attribute = self::attributeOf( $node, 'ezmedia', self::arg( $args, 1, 'string', null ) );
        $media = $attribute->content();
        if ( !$media instanceof eZMedia )
            throw new expServiceException( 'The attribute has no media file', 404 );
        return array( $node, $attribute, $media );
    }

    protected static function exportMedia( eZContentObjectAttribute $attribute, $media )
    {
        $has = $media instanceof eZMedia;
        $out = array( 'attribute' => $attribute->contentClassAttributeIdentifier(), 'attribute_id' => (int)$attribute->attribute( 'id' ), 'has_content' => $has );
        if ( $has )
            $out['media'] = array( 'original_filename' => $media->attribute( 'original_filename' ), 'mime_type' => $media->attribute( 'mime_type' ),
                                   'mime_group' => eZMedia::mimeGroup( $media->attribute( 'mime_type' ) ),
                                   'width' => (int)$media->attribute( 'width' ), 'height' => (int)$media->attribute( 'height' ),
                                   'controls' => (bool)$media->attribute( 'controls' ), 'autoplay' => (bool)$media->attribute( 'is_autoplay' ),
                                   'loop' => (bool)$media->attribute( 'is_loop' ), 'quality' => $media->attribute( 'quality' ),
                                   'filesize' => (int)$media->attribute( 'filesize' ) );
        return $out;
    }

    public static function attributes( $args )
    {
        self::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $list = array();
        foreach ( self::attributesOf( $node, 'ezmedia' ) as $attribute )
            $list[] = self::exportMedia( $attribute, $attribute->hasContent() ? $attribute->content() : null );
        return self::ok( $list );
    }

    public static function info( $args )
    {
        self::guard( __FUNCTION__ );
        list( $node, $attribute, $media ) = self::target( $args );
        return self::ok( array_merge( self::exportNode( $node ), self::exportMedia( $attribute, $media ) ) );
    }

    public static function player( $args )
    {
        self::guard( __FUNCTION__ );
        list( , $attribute, $media ) = self::target( $args );
        $info = self::exportMedia( $attribute, $media );
        return self::ok( $info['media'] );
    }

    public static function url( $args )
    {
        self::guard( __FUNCTION__ );
        list( $node, $attribute, $media ) = self::target( $args );
        $path = 'content/download/' . (int)$node->attribute( 'contentobject_id' ) . '/' . (int)$attribute->attribute( 'id' )
              . '/version/' . (int)$attribute->attribute( 'version' ) . '/file/' . rawurlencode( $media->attribute( 'original_filename' ) );
        return self::ok( array( 'url' => self::moduleUrl( $path ), 'absolute_url' => self::absolute( self::moduleUrl( $path ) ) ) );
    }

    public static function mimegroup( $args )
    {
        self::guard( __FUNCTION__ );
        $mime = self::arg( $args, 0, 'string' );
        return self::ok( array( 'mime_type' => $mime, 'group' => eZMedia::mimeGroup( $mime ) ) );
    }

    public static function safename( $args )
    {
        self::guard( __FUNCTION__ );
        $name = self::arg( $args, 0, 'string' );
        return self::ok( array( 'safe' => (bool)eZMedia::isSafeFileName( $name ), 'safe_name' => eZMedia::safeFileName( $name ) ) );
    }

    public static function list( $args )
    {
        self::guard( __FUNCTION__ );
        $parent = self::arg( $args, 0, 'int', (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $classes = self::classList( 'expservices.ini', 'Media', 'MediaClasses', array( 'video', 'quicktime', 'real_video', 'silverlight', 'windows_media', 'flash' ) );
        list( $nodes, $total ) = self::nodesBelow( $parent, $classes, $limit, $offset, array( 'LoadDataMap' => true ) );
        $items = array();
        foreach ( $nodes as $node )
        {
            $out = self::exportNode( $node );
            $out['media'] = array();
            foreach ( self::attributesOf( $node, 'ezmedia' ) as $attribute )
                if ( $attribute->hasContent() )
                    $out['media'][] = self::exportMedia( $attribute, $attribute->content() );
            $items[] = $out;
        }
        return self::page( $items, $total, $offset, $limit );
    }

    public static function byobject( $args )
    {
        self::guard( __FUNCTION__ );
        $id = self::arg( $args, 0, 'int' );
        $object = eZContentObject::fetch( $id );
        if ( !$object || !$object->canRead() )
            throw new expServiceException( $object ? "No read access to object $id" : "Object $id does not exist", $object ? 403 : 404 );
        $rows = self::rows( "SELECT m.contentobject_attribute_id, m.version, m.filename, m.original_filename, m.mime_type, m.width, m.height FROM ezmedia m, ezcontentobject_attribute a WHERE a.id=m.contentobject_attribute_id AND a.version=m.version AND a.contentobject_id=$id ORDER BY m.version, m.contentobject_attribute_id" );
        $items = array();
        foreach ( $rows as $r )
            $items[] = array( 'attribute_id' => (int)$r['contentobject_attribute_id'], 'version' => (int)$r['version'],
                              'original_filename' => $r['original_filename'], 'mime_type' => $r['mime_type'], 'width' => (int)$r['width'], 'height' => (int)$r['height'] );
        return self::ok( $items );
    }

    public static function stats( $args )
    {
        self::guard( __FUNCTION__ );
        $groups = array();
        foreach ( self::rows( 'SELECT mime_type, COUNT(*) AS n FROM ezmedia GROUP BY mime_type' ) as $r )
        {
            $g = eZMedia::mimeGroup( $r['mime_type'] );
            $groups[$g] = ( isset( $groups[$g] ) ? $groups[$g] : 0 ) + (int)$r['n'];
        }
        return self::ok( array( 'files' => self::scalar( 'SELECT COUNT(*) AS n FROM ezmedia' ), 'groups' => $groups ) );
    }

    public static function bymime( $args )
    {
        self::guard( __FUNCTION__ );
        $out = array();
        foreach ( self::rows( 'SELECT mime_type, COUNT(*) AS n FROM ezmedia GROUP BY mime_type ORDER BY n DESC' ) as $r )
            $out[] = array( 'mime_type' => $r['mime_type'], 'count' => (int)$r['n'] );
        return self::ok( $out );
    }
}
