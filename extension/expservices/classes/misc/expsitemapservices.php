<?php
/**
 * ezjscore/call/expsitemap::<service> - sitemap data: the entries of a subtree as a sitemap lists them (location,
 * last modification, change frequency, priority), the settings of the bcgooglesitemaps extension, and the
 * generated sitemap files. Entries only list what the user may read.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expSitemapServices extends expAttrServiceBase
{
    public static $services = array(
        'config' => array( 'summary' => 'The sitemap settings (root node, protocol, file name, class filter)', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'settings' ),
        'available' => array( 'summary' => 'Whether the sitemap extensions are active', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'bcgooglesitemaps, xrowmetadata' ),
        'rootnode' => array( 'summary' => 'The node the sitemap starts from', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'node' ),
        'entries' => array( 'summary' => 'Sitemap entries of a subtree: loc, lastmod, changefreq, priority (class filter of the settings applied)', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of entries' ),
        'count' => array( 'summary' => 'How many entries a subtree has', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'count, pages (of 50000 entries)' ),
        'entry' => array( 'summary' => 'The sitemap entry of one node', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'entry' ),
        'recent' => array( 'summary' => 'The most recently changed entries (a news sitemap)', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int', 'limit' => 'int' ), 'returns' => 'list of entries' ),
        'index' => array( 'summary' => 'The sitemap index: the pages of 50000 entries with their entry window', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'list of page, offset, limit' ),
        'files' => array( 'summary' => 'The generated sitemap files (name, size, modified)', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'list of files' ),
        'classes' => array( 'summary' => 'The classes the sitemap includes or excludes', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'type, classes' ),
        'robots' => array( 'summary' => 'The robots.txt Sitemap line for the generated sitemap', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'line' ),
    );

    const PAGE = 50000;

    protected static function setting( $name, $default = '' )
    {
        $ini = eZINI::instance( 'bcgooglesitemaps.ini' );
        foreach ( array( 'BCGoogleSitemapSettings', 'Classes' ) as $group )
            if ( $ini->hasVariable( $group, $name ) )
                return $ini->variable( $group, $name );
        return $default;
    }

    protected static function rootId()
    {
        $id = (int)self::setting( 'SitemapRootNodeID', 0 );
        return $id > 0 ? $id : (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' );
    }

    protected static function classFilter()
    {
        $type = self::setting( 'ClassFilterType', 'exclude' ) === 'include' ? 'include' : 'exclude';
        $classes = array_values( array_filter( (array)self::setting( 'ClassFilterArray', array() ), 'strlen' ) );
        return array( $type, $classes );
    }

    protected static function makeEntry( eZContentObjectTreeNode $node )
    {
        $object = $node->object();
        $depth = (int)$node->attribute( 'depth' );
        $modified = $object ? (int)$object->attribute( 'modified' ) : 0;
        $age = time() - $modified;
        $freq = $age < 86400 * 2 ? 'daily' : ( $age < 86400 * 30 ? 'weekly' : ( $age < 86400 * 365 ? 'monthly' : 'yearly' ) );
        $protocol = self::setting( 'Protocol', 'https' );
        $host = eZINI::instance( 'site.ini' )->variable( 'SiteSettings', 'SiteURL' );
        return array( 'node_id' => (int)$node->attribute( 'node_id' ), 'loc' => $protocol . '://' . $host . '/' . ltrim( $node->attribute( 'url_alias' ), '/' ),
                      'lastmod' => self::iso( $modified ), 'changefreq' => $freq, 'priority' => round( max( 0.1, 1.0 - 0.1 * max( 0, $depth - 1 ) ), 1 ) );
    }

    protected static function params( $limit = null, $offset = null )
    {
        list( $type, $classes ) = self::classFilter();
        $p = array( 'SortBy' => array( 'modified', false ), 'LoadDataMap' => false, 'IgnoreVisibility' => false );
        if ( $classes )
        {
            $p['ClassFilterType'] = $type;
            $p['ClassFilterArray'] = $classes;
        }
        if ( $limit !== null )
        {
            $p['Limit'] = $limit;
            $p['Offset'] = $offset;
        }
        return $p;
    }

    protected static function rootArg( $args, $i )
    {
        return self::arg( $args, $i, 'int', self::rootId() );
    }

    public static function config( $args )
    {
        self::guard( __FUNCTION__ );
        list( $type, $classes ) = self::classFilter();
        return self::ok( array( 'root_node' => self::rootId(), 'protocol' => self::setting( 'Protocol', 'https' ), 'filename' => self::setting( 'Filename', 'sitemap' ),
                                'suffix' => self::setting( 'Filesuffix', '.xml' ), 'class_filter_type' => $type, 'class_filter' => $classes,
                                'site_url' => eZINI::instance( 'site.ini' )->variable( 'SiteSettings', 'SiteURL' ) ) );
    }

    public static function available( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'bcgooglesitemaps' => in_array( 'bcgooglesitemaps', eZExtension::activeExtensions(), true ), 'xrowmetadata' => in_array( 'xrowmetadata', eZExtension::activeExtensions(), true ) ) );
    }

    public static function rootnode( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( self::exportNode( self::node( self::rootId() ) ) );
    }

    public static function entries( $args )
    {
        self::guard( __FUNCTION__ );
        $parent = self::node( self::rootArg( $args, 0 ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $id = (int)$parent->attribute( 'node_id' );
        $total = eZContentObjectTreeNode::subTreeCountByNodeID( self::params(), $id );
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( self::params( $limit, $offset ), $id );
        $items = array();
        foreach ( is_array( $nodes ) ? $nodes : array() as $n )
            $items[] = self::makeEntry( $n );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function count( $args )
    {
        self::guard( __FUNCTION__ );
        $parent = self::node( self::rootArg( $args, 0 ) );
        $total = (int)eZContentObjectTreeNode::subTreeCountByNodeID( self::params(), (int)$parent->attribute( 'node_id' ) );
        return self::ok( array( 'count' => $total, 'pages' => (int)ceil( $total / self::PAGE ) ) );
    }

    public static function entry( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( self::makeEntry( self::node( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function recent( $args )
    {
        self::guard( __FUNCTION__ );
        $parent = self::node( self::rootArg( $args, 0 ) );
        list( $limit ) = self::paging( $args, 1, 99 );
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( self::params( $limit, 0 ), (int)$parent->attribute( 'node_id' ) );
        $items = array();
        foreach ( is_array( $nodes ) ? $nodes : array() as $n )
            $items[] = self::makeEntry( $n );
        return self::ok( $items, array( 'limit' => $limit ) );
    }

    public static function index( $args )
    {
        self::guard( __FUNCTION__ );
        $parent = self::node( self::rootArg( $args, 0 ) );
        $total = (int)eZContentObjectTreeNode::subTreeCountByNodeID( self::params(), (int)$parent->attribute( 'node_id' ) );
        $list = array();
        for ( $i = 0; $i * self::PAGE < max( 1, $total ); $i++ )
            $list[] = array( 'page' => $i + 1, 'offset' => $i * self::PAGE, 'limit' => self::PAGE );
        return self::ok( $list, array( 'entries' => $total ) );
    }

    public static function files( $args )
    {
        self::guard( __FUNCTION__ );
        $path = self::setting( 'Path', '' );
        if ( $path === '' )
            $path = eZINI::instance( 'site.ini' )->variable( 'FileSettings', 'VarDir' );
        $name = preg_replace( '/[^A-Za-z0-9_.-]/', '', self::setting( 'Filename', 'sitemap' ) );
        $list = array();
        foreach ( glob( rtrim( $path, '/' ) . '/' . $name . '*' . self::setting( 'Filesuffix', '.xml' ) ) ?: array() as $f )
            $list[] = array( 'name' => basename( $f ), 'size' => (int)filesize( $f ), 'modified' => self::iso( filemtime( $f ) ) );
        return self::ok( $list );
    }

    public static function classes( $args )
    {
        self::guard( __FUNCTION__ );
        list( $type, $classes ) = self::classFilter();
        return self::ok( array( 'type' => $type, 'classes' => $classes ) );
    }

    public static function robots( $args )
    {
        self::guard( __FUNCTION__ );
        $host = eZINI::instance( 'site.ini' )->variable( 'SiteSettings', 'SiteURL' );
        return self::ok( array( 'line' => 'Sitemap: ' . self::setting( 'Protocol', 'https' ) . '://' . $host . '/' . self::setting( 'Filename', 'sitemap' ) . self::setting( 'Filesuffix', '.xml' ) ) );
    }
}
