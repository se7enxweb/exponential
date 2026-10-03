<?php
/**
 * ezjscore/call/expfile::<service> - binary files: information, download URLs (content/download, which checks
 * content/read itself), signed time limited links, listings and statistics.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expFileServices extends expAttrServiceBase
{
    public static $services = array(
        'attributes' => array( 'summary' => 'The file attributes of a node', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'list of attribute, has_content, file' ),
        'info' => array( 'summary' => 'One file of a node: names, MIME type, size, download count', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'file information' ),
        'downloadurl' => array( 'summary' => 'The content/download URL of a node file (the download itself checks content/read)', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'url, absolute_url' ),
        'signedurl' => array( 'summary' => 'A time limited signed link to a node file, for clients without a session', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string', 'seconds' => 'int' ), 'returns' => 'url, expires, signature' ),
        'verify' => array( 'summary' => 'Whether a signed link is genuine and not expired', 'access' => 'public', 'write' => false,
            'args' => array( 'node' => 'int', 'attribute_id' => 'int', 'expires' => 'int', 'signature' => 'string' ), 'returns' => 'valid, reason' ),
        'exists' => array( 'summary' => 'Whether the stored file of a node attribute exists on disk', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'exists, size' ),
        'downloads' => array( 'summary' => 'The download count of a node file', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'download_count' ),
        'list' => array( 'summary' => 'The file objects below a parent node with their file information', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'parent' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of file nodes' ),
        'search' => array( 'summary' => 'File objects by name below a parent', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'text' => 'string', 'parent' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of file nodes' ),
        'byname' => array( 'summary' => 'The stored files with an original or stored file name (exact)', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'filename' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of file rows' ),
        'top' => array( 'summary' => 'The most downloaded files', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'limit' => 'int' ), 'returns' => 'list of file rows with download_count' ),
        'stats' => array( 'summary' => 'Number of files, total downloads, and counts per MIME group', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array(), 'returns' => 'files, downloads, groups' ),
        'bymime' => array( 'summary' => 'File counts per MIME type', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array(), 'returns' => 'list of mime_type, count' ),
        'mimegroup' => array( 'summary' => 'The MIME group (application, image, ...) of a MIME type', 'access' => 'public',
            'write' => false, 'args' => array( 'mime' => 'string' ), 'returns' => 'group' ),
        'safename' => array( 'summary' => 'Whether a file name is acceptable for upload and its safe form', 'access' => 'user',
            'write' => false, 'args' => array( 'filename' => 'string' ), 'returns' => 'safe, safe_name' ),
        'resetdownloads' => array( 'summary' => 'Sets the download count of a node file back to zero', 'access' => array( 'content', 'edit' ),
            'write' => true, 'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'download_count' ),
    );

    protected static function secret()
    {
        $ini = eZINI::instance( 'site.ini' );
        return 'expservices-file|' . ( $ini->hasVariable( 'HTMLForms', 'Secret' ) ? $ini->variable( 'HTMLForms', 'Secret' ) : '' );
    }

    protected static function sign( $nodeId, $attributeId, $expires )
    {
        return hash_hmac( 'sha256', (int)$nodeId . '|' . (int)$attributeId . '|' . (int)$expires, self::secret() );
    }

    /** @return array array( node, attribute, file ) */
    protected static function target( $args, $function = 'read' )
    {
        $node = self::node( self::arg( $args, 0, 'int' ), $function );
        $attribute = self::attributeOf( $node, 'ezbinaryfile', self::arg( $args, 1, 'string', null ) );
        $file = $attribute->content();
        if ( !$file instanceof eZBinaryFile )
            throw new expServiceException( 'The attribute has no file', 404 );
        return array( $node, $attribute, $file );
    }

    protected static function exportFile( eZContentObjectAttribute $attribute, $file )
    {
        $has = $file instanceof eZBinaryFile;
        $out = array( 'attribute' => $attribute->contentClassAttributeIdentifier(), 'attribute_id' => (int)$attribute->attribute( 'id' ),
                      'has_content' => $has );
        if ( $has )
            $out['file'] = array( 'original_filename' => $file->attribute( 'original_filename' ), 'filename' => $file->attribute( 'filename' ),
                                  'mime_type' => $file->attribute( 'mime_type' ), 'mime_group' => eZBinaryFile::mimeGroup( $file->attribute( 'mime_type' ) ),
                                  'filesize' => (int)$file->attribute( 'filesize' ), 'download_count' => (int)$file->attribute( 'download_count' ) );
        return $out;
    }

    protected static function downloadPath( eZContentObjectTreeNode $node, eZContentObjectAttribute $attribute, eZBinaryFile $file )
    {
        return 'content/download/' . (int)$node->attribute( 'contentobject_id' ) . '/' . (int)$attribute->attribute( 'id' )
             . '/version/' . (int)$attribute->attribute( 'version' ) . '/file/' . rawurlencode( $file->attribute( 'original_filename' ) );
    }

    public static function attributes( $args )
    {
        self::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $list = array();
        foreach ( self::attributesOf( $node, 'ezbinaryfile' ) as $attribute )
            $list[] = self::exportFile( $attribute, $attribute->hasContent() ? $attribute->content() : null );
        return self::ok( $list );
    }

    public static function info( $args )
    {
        self::guard( __FUNCTION__ );
        list( $node, $attribute, $file ) = self::target( $args );
        return self::ok( array_merge( self::exportNode( $node ), self::exportFile( $attribute, $file ) ) );
    }

    public static function downloadurl( $args )
    {
        self::guard( __FUNCTION__ );
        list( $node, $attribute, $file ) = self::target( $args );
        $path = self::downloadPath( $node, $attribute, $file );
        return self::ok( array( 'url' => self::moduleUrl( $path ), 'absolute_url' => self::absolute( self::moduleUrl( $path ) ) ) );
    }

    public static function signedurl( $args )
    {
        self::guard( __FUNCTION__ );
        list( $node, $attribute, $file ) = self::target( $args );
        $seconds = self::arg( $args, 2, 'int', 300 );
        if ( $seconds < 10 || $seconds > 86400 )
            throw new expServiceException( 'seconds must be between 10 and 86400', 400 );
        $expires = time() + $seconds;
        $sig = self::sign( $node->attribute( 'node_id' ), $attribute->attribute( 'id' ), $expires );
        $path = self::downloadPath( $node, $attribute, $file );
        return self::ok( array( 'url' => self::absolute( self::moduleUrl( $path ) ), 'expires' => $expires, 'expires_at' => self::iso( $expires ),
                                'node' => (int)$node->attribute( 'node_id' ), 'attribute_id' => (int)$attribute->attribute( 'id' ), 'signature' => $sig ) );
    }

    public static function verify( $args )
    {
        self::guard( __FUNCTION__ );
        $node = self::arg( $args, 0, 'int' );
        $attributeId = self::arg( $args, 1, 'int' );
        $expires = self::arg( $args, 2, 'int' );
        $sig = self::arg( $args, 3, 'string' );
        if ( !hash_equals( self::sign( $node, $attributeId, $expires ), $sig ) )
            return self::ok( array( 'valid' => false, 'reason' => 'signature' ) );
        if ( $expires < time() )
            return self::ok( array( 'valid' => false, 'reason' => 'expired' ) );
        return self::ok( array( 'valid' => true, 'reason' => '', 'expires' => $expires ) );
    }

    public static function exists( $args )
    {
        self::guard( __FUNCTION__ );
        list( , , $file ) = self::target( $args );
        $path = $file->attribute( 'filepath' );
        $ok = $path !== '' && is_file( $path );
        return self::ok( array( 'exists' => $ok, 'size' => $ok ? filesize( $path ) : 0 ) );
    }

    public static function downloads( $args )
    {
        self::guard( __FUNCTION__ );
        list( , , $file ) = self::target( $args );
        return self::ok( array( 'download_count' => (int)$file->attribute( 'download_count' ) ) );
    }

    protected static function fetchList( $args, $text )
    {
        $i = $text === null ? 0 : 1;
        $parent = self::arg( $args, $i, 'int', (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' ) );
        list( $limit, $offset ) = self::paging( $args, $i + 1, $i + 2 );
        $extra = array( 'LoadDataMap' => true );
        if ( $text !== null )
            $extra['AttributeFilter'] = array( array( 'name', 'like', '*' . $text . '*' ) );
        list( $nodes, $total ) = self::nodesBelow( $parent, self::classList( 'expservices.ini', 'Media', 'FileClasses', array( 'file', 'ng_video' ) ), $limit, $offset, $extra );
        $items = array();
        foreach ( $nodes as $node )
        {
            $out = self::exportNode( $node );
            $out['files'] = array();
            foreach ( self::attributesOf( $node, 'ezbinaryfile' ) as $attribute )
                if ( $attribute->hasContent() )
                    $out['files'][] = self::exportFile( $attribute, $attribute->content() );
            $items[] = $out;
        }
        return self::page( $items, $total, $offset, $limit );
    }

    public static function list( $args )
    {
        self::guard( __FUNCTION__ );
        return self::fetchList( $args, null );
    }

    public static function search( $args )
    {
        self::guard( __FUNCTION__ );
        $text = trim( self::arg( $args, 0, 'string' ) );
        if ( strlen( $text ) < 2 )
            throw new expServiceException( 'The search text needs at least 2 characters', 422 );
        return self::fetchList( $args, $text );
    }

    public static function byname( $args )
    {
        self::guard( __FUNCTION__ );
        $name = self::arg( $args, 0, 'string' );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $db = eZDB::instance();
        $e = $db->escapeString( $name );
        $where = "( original_filename='$e' OR filename='$e' )";
        $total = self::scalar( "SELECT COUNT(*) AS n FROM ezbinaryfile WHERE $where" );
        $rows = self::rows( "SELECT contentobject_attribute_id, version, filename, original_filename, mime_type, download_count FROM ezbinaryfile WHERE $where ORDER BY contentobject_attribute_id",
                            array( 'limit' => $limit, 'offset' => $offset ) );
        return self::page( self::exportRows( $rows ), $total, $offset, $limit );
    }

    protected static function exportRows( array $rows )
    {
        $items = array();
        foreach ( $rows as $r )
            $items[] = array( 'attribute_id' => (int)$r['contentobject_attribute_id'], 'version' => (int)$r['version'],
                              'original_filename' => $r['original_filename'], 'mime_type' => $r['mime_type'],
                              'download_count' => (int)$r['download_count'] );
        return $items;
    }

    public static function top( $args )
    {
        self::guard( __FUNCTION__ );
        list( $limit ) = self::paging( $args, 0, 99 );
        $rows = self::rows( 'SELECT contentobject_attribute_id, version, filename, original_filename, mime_type, download_count FROM ezbinaryfile ORDER BY download_count DESC, contentobject_attribute_id',
                            array( 'limit' => $limit ) );
        return self::ok( self::exportRows( $rows ), array( 'limit' => $limit ) );
    }

    public static function bymime( $args )
    {
        self::guard( __FUNCTION__ );
        $rows = self::rows( 'SELECT mime_type, COUNT(*) AS n FROM ezbinaryfile GROUP BY mime_type ORDER BY n DESC' );
        $out = array();
        foreach ( $rows as $r )
            $out[] = array( 'mime_type' => $r['mime_type'], 'count' => (int)$r['n'] );
        return self::ok( $out );
    }

    public static function stats( $args )
    {
        self::guard( __FUNCTION__ );
        $groups = array();
        foreach ( self::rows( 'SELECT mime_type, COUNT(*) AS n FROM ezbinaryfile GROUP BY mime_type' ) as $r )
        {
            $g = eZBinaryFile::mimeGroup( $r['mime_type'] );
            $groups[$g] = ( isset( $groups[$g] ) ? $groups[$g] : 0 ) + (int)$r['n'];
        }
        return self::ok( array( 'files' => self::scalar( 'SELECT COUNT(*) AS n FROM ezbinaryfile' ),
                                'downloads' => self::scalar( 'SELECT COALESCE(SUM(download_count),0) AS n FROM ezbinaryfile' ), 'groups' => $groups ) );
    }

    public static function mimegroup( $args )
    {
        self::guard( __FUNCTION__ );
        $mime = self::arg( $args, 0, 'string' );
        return self::ok( array( 'mime_type' => $mime, 'group' => eZBinaryFile::mimeGroup( $mime ) ) );
    }

    public static function safename( $args )
    {
        self::guard( __FUNCTION__ );
        $name = self::arg( $args, 0, 'string' );
        return self::ok( array( 'safe' => (bool)eZBinaryFile::isSafeFileName( $name ), 'safe_name' => eZBinaryFile::safeFileName( $name ) ) );
    }

    public static function resetdownloads( $args )
    {
        self::guard( __FUNCTION__ );
        list( $node, $attribute, $file ) = self::target( $args, 'edit' );
        $file->setAttribute( 'download_count', 0 );
        $file->store();
        return self::ok( array( 'node' => (int)$node->attribute( 'node_id' ), 'download_count' => 0 ) );
    }
}
