<?php
/**
 * ezjscore/call/exppdf::<service> - PDF output: whether a node can be exported (content/pdf policy), the link that
 * produces the PDF of a node, and the PDF export definitions of the pdf module (pdf/create, pdf/edit). The PDF itself
 * is downloaded from the link (content/pdf/<node>), which checks the policy again.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expPdfServices extends expAttrServiceBase
{
    public static $services = array(
        'available' => array( 'summary' => 'Whether PDF export is available (classes present) and how many export definitions exist', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'available, exports' ),
        'canpdf' => array( 'summary' => 'Whether the user may export a node as PDF', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'can_pdf' ),
        'link' => array( 'summary' => 'The URL that downloads the PDF of a node (optional language)', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int', 'language' => 'string' ), 'returns' => 'url, absolute_url' ),
        'links' => array( 'summary' => 'PDF links for several nodes at once (those the user may export)', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'nodes' => 'list' ), 'returns' => 'list of node, url' ),
        'exports' => array( 'summary' => 'The PDF export definitions', 'access' => array( 'pdf', 'edit' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of exports' ),
        'export' => array( 'summary' => 'One PDF export definition', 'access' => array( 'pdf', 'edit' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'export' ),
        'exportcount' => array( 'summary' => 'How many PDF export definitions exist', 'access' => array( 'pdf', 'edit' ), 'write' => false,
            'args' => array(), 'returns' => 'count' ),
        'exportsfor' => array( 'summary' => 'The export definitions whose source node is a node or one of its ancestors', 'access' => array( 'pdf', 'edit' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'list of exports' ),
        'exportfile' => array( 'summary' => 'Whether the generated file of a stored export exists and how large it is', 'access' => array( 'pdf', 'edit' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'exists, size, modified' ),
        'statuses' => array( 'summary' => 'The export modes: created once or on the fly', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'map code => name' ),
    );

    protected static function need()
    {
        if ( !class_exists( 'eZPDFExport' ) )
            throw new expServiceException( 'PDF export is not available', 404 );
    }

    protected static function exportRow( eZPDFExport $e )
    {
        return array( 'id' => (int)$e->attribute( 'id' ), 'title' => $e->attribute( 'title' ), 'source_node_id' => (int)$e->attribute( 'source_node_id' ),
                      'site_access' => $e->attribute( 'site_access' ), 'pdf_filename' => $e->attribute( 'pdf_filename' ),
                      'mode' => (int)$e->attribute( 'status' ) === eZPDFExport::CREATE_ONFLY ? 'on_the_fly' : 'once',
                      'show_frontpage' => (bool)$e->attribute( 'show_frontpage' ), 'show_footer' => (bool)$e->attribute( 'show_footer' ),
                      'export_structure' => $e->attribute( 'export_structure' ),
                      'export_classes' => array_values( array_filter( explode( ':', (string)$e->attribute( 'export_classes' ) ), 'strlen' ) ),
                      'modified' => self::iso( $e->attribute( 'modified' ) ), 'created' => self::iso( $e->attribute( 'created' ) ) );
    }

    protected static function fetchExport( $id )
    {
        self::need();
        $e = eZPDFExport::fetch( (int)$id );
        if ( !$e instanceof eZPDFExport )
            throw new expServiceException( "PDF export $id does not exist", 404 );
        return $e;
    }

    protected static function linkFor( eZContentObjectTreeNode $node, $language )
    {
        $uri = 'content/pdf/' . (int)$node->attribute( 'node_id' ) . ( $language !== '' ? '/(language)/' . $language : '' );
        $url = self::moduleUrl( $uri );
        return array( 'node' => (int)$node->attribute( 'node_id' ), 'url' => $url, 'absolute_url' => self::absolute( $url ) );
    }

    protected static function language( $args, $i )
    {
        $l = self::arg( $args, $i, 'string', '' );
        if ( $l !== '' && !eZContentLanguage::fetchByLocale( $l ) instanceof eZContentLanguage )
            throw new expServiceException( "'$l' is not a content language", 404 );
        return $l;
    }

    public static function available( $args )
    {
        self::guard( __FUNCTION__ );
        $has = class_exists( 'eZPDFExport' );
        return self::ok( array( 'available' => $has, 'exports' => $has ? count( eZPDFExport::fetchList() ) : 0 ) );
    }

    public static function canpdf( $args )
    {
        self::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'node' => (int)$node->attribute( 'node_id' ), 'can_pdf' => (bool)$node->canPdf() ) );
    }

    public static function link( $args )
    {
        self::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        if ( !$node->canPdf() )
            throw new expServiceException( 'No PDF export access to node ' . $node->attribute( 'node_id' ), 403 );
        return self::ok( self::linkFor( $node, self::language( $args, 1 ) ) );
    }

    public static function links( $args )
    {
        self::guard( __FUNCTION__ );
        $ids = self::arg( $args, 0, 'list' );
        if ( !$ids || count( $ids ) > 100 )
            throw new expServiceException( 'Give 1 to 100 node ids', 400 );
        $list = array();
        foreach ( $ids as $id )
        {
            if ( !ctype_digit( (string)$id ) )
                throw new expServiceException( "'$id' is not a node id", 400 );
            $node = eZContentObjectTreeNode::fetch( (int)$id );
            if ( $node instanceof eZContentObjectTreeNode && $node->canRead() && $node->canPdf() )
                $list[] = self::linkFor( $node, '' );
        }
        return self::ok( $list, array( 'asked' => count( $ids ), 'allowed' => count( $list ) ) );
    }

    public static function exports( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $all = array();
        foreach ( eZPDFExport::fetchList() as $e )
            $all[] = self::exportRow( $e );
        return self::pageOf( $all, $args, 0, 1 );
    }

    public static function export( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( self::exportRow( self::fetchExport( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function exportcount( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        return self::ok( array( 'count' => count( eZPDFExport::fetchList() ) ) );
    }

    public static function exportsfor( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $path = array_filter( explode( '/', $node->attribute( 'path_string' ) ), 'strlen' );
        $list = array();
        foreach ( eZPDFExport::fetchList() as $e )
            if ( in_array( (string)$e->attribute( 'source_node_id' ), $path, true ) )
                $list[] = self::exportRow( $e );
        return self::ok( $list );
    }

    public static function exportfile( $args )
    {
        self::guard( __FUNCTION__ );
        $e = self::fetchExport( self::arg( $args, 0, 'int' ) );
        $name = basename( (string)$e->attribute( 'pdf_filename' ) );
        $file = eZSys::storageDirectory() . '/pdf/' . $name;
        $exists = $name !== '' && is_file( $file );
        return self::ok( array( 'exists' => $exists, 'size' => $exists ? (int)filesize( $file ) : 0, 'modified' => $exists ? self::iso( filemtime( $file ) ) : null ) );
    }

    public static function statuses( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( eZPDFExport::CREATE_ONCE => 'once', eZPDFExport::CREATE_ONFLY => 'on_the_fly' ) );
    }
}
