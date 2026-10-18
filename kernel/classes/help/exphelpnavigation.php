<?php
/**
 * The navigation tree of the help (doc/features/6.0/help-system.md, "Viewer"): every page the index serves, in its
 * folders, as nested lists. Folders are native <details> (they open and close without JavaScript) with the number of
 * pages below them; a folder's README.md or index.md is its "Overview" link. The viewer opens the folders on the way
 * to the current page and marks it with aria-current; the contents page (exphelp/contents) opens them all.
 *
 * The HTML of the whole tree is built once per index signature, siteaccess and language and cached in
 * <help cache>/nav/; a request only marks its current page and open folders in it, so a tree of 1 200 pages costs a
 * file read. Nothing is capped: every visible document of expHelpDocumentIndex::tree() is listed, at any depth.
 *
 * Folder names: exphelp.ini [NavigationSettings] FolderNames[<path>], else the title of the folder's README/index,
 * else the built-in names below, else the humanized directory name.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpNavigation
{
    /**
     * Readable names of the folders of doc/ that have no README title of their own; exphelp.ini overrides them.
     */
    const FOLDER_NAMES = array(
        'bc' => 'Backward compatibility',
        'changelogs' => 'Changelogs',
        'examples' => 'Examples',
        'features' => 'Features',
        'guides' => 'Guides',
        'help' => 'Using the administration',
        'history' => 'History',
        'install' => 'Installation',
        'shop' => 'Shop',
        'specifications' => 'Specifications',
        'standards' => 'Standards',
    );

    /** Bumped when the markup changes, so cached trees of an older layout are not served */
    const VERSION = 1;

    /** @var expHelpDocumentIndex */
    protected $index;

    /** @var string the viewer's address with the siteaccess prefix, a page id is appended to it */
    protected $viewerBase;

    /**
     * @param expHelpDocumentIndex|null $index
     */
    public function __construct( $index = null )
    {
        $this->index = $index !== null ? $index : expHelpDocumentIndex::instance();
        $this->viewerBase = expHelpLinks::absolute( expHelp::url( 'viewer' ) );
    }

    /**
     * The tree with the folders on the way to $currentId open and its link marked as the current page.
     *
     * @param string $currentId the page shown ('' for none)
     * @param bool $openAll open every folder (the contents page)
     * @return string HTML (safe: every text and address is escaped)
     */
    public function html( $currentId = '', $openAll = false )
    {
        $html = $this->cachedTree();
        if ( $openAll )
            return str_replace( '<details class="a4-help-nav-details" data-nav-path=', '<details open class="a4-help-nav-details" data-nav-path=', $html );
        $currentId = trim( (string)$currentId, '/' );
        if ( $currentId === '' )
            return $html;
        $segments = explode( '/', $currentId );
        array_pop( $segments );
        $dir = '';
        foreach ( $segments as $segment )
        {
            $dir = $dir === '' ? $segment : $dir . '/' . $segment;
            $html = str_replace( '<details class="a4-help-nav-details" data-nav-path="' . self::escape( $dir ) . '">',
                                 '<details open class="a4-help-nav-details" data-nav-path="' . self::escape( $dir ) . '">', $html );
        }
        $href = 'href="' . self::escape( $this->pageUrl( $currentId ) ) . '"';
        return str_replace( $href, $href . ' aria-current="page"', $html );
    }

    /**
     * The number of pages the tree lists.
     *
     * @return int
     */
    public function count()
    {
        return self::countPages( $this->index->tree() );
    }

    /**
     * The tree's HTML without a current page, from the cache or built and stored.
     *
     * @return string
     */
    protected function cachedTree()
    {
        $dir = rtrim( (string)$this->index->cacheDir(), '/' ) . '/nav';
        $locale = class_exists( 'eZLocale' ) ? eZLocale::instance()->localeFullCode() : '';
        $file = $dir . '/' . sha1( self::VERSION . "\0" . $this->index->signature() . "\0" . $this->viewerBase . "\0" . $locale
                                   . "\0" . serialize( self::folderNames() ) ) . '.html';
        if ( is_file( $file ) )
        {
            $html = @file_get_contents( $file );
            if ( is_string( $html ) && $html !== '' )
                return $html;
        }
        $html = $this->build( $this->index->tree() );
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        if ( is_dir( $dir ) && is_writable( $dir ) )
        {
            $tmp = $file . '.' . getmypid() . '.tmp';
            if ( @file_put_contents( $tmp, $html, LOCK_EX ) !== false )
            {
                $permissions = eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' );
                @chmod( $tmp, preg_match( '/^0?[0-7]{3}$/', (string)$permissions ) ? octdec( $permissions ) : 0664 );
                if ( !@rename( $tmp, $file ) )
                    @unlink( $tmp );
            }
        }
        return $html;
    }

    /**
     * @param array $tree the root node of expHelpDocumentIndex::tree()
     * @return string
     */
    public function build( array $tree )
    {
        $names = self::folderNames();
        $labels = array(
            'overview' => ezpI18n::tr( expHelp::I18N_CONTEXT, 'Overview' ),
        );
        $out = '<ul class="a4-help-nav-list is-top" role="list">';
        if ( !empty( $tree['index_id'] ) )
            $out .= '<li class="a4-help-nav-page"><a class="a4-help-nav-link is-index" href="' . self::escape( $this->pageUrl( $tree['index_id'] ) ) . '">'
                . self::escape( ezpI18n::tr( expHelp::I18N_CONTEXT, 'About this documentation' ) ) . '</a></li>';
        $out .= $this->buildLevel( $tree, $names, $labels ) . '</ul>';
        return $out;
    }

    /**
     * The folders, then the pages of one node, as list items.
     *
     * @param array $node
     * @param array $names
     * @param array $labels
     * @return string
     */
    protected function buildLevel( array $node, array $names, array $labels )
    {
        $out = '';
        foreach ( isset( $node['children'] ) ? $node['children'] : array() as $child )
        {
            $count = self::countPages( $child );
            if ( $count === 0 )
                continue;
            $path = (string)$child['path'];
            $title = self::folderTitle( $child, $names );
            $out .= '<li class="a4-help-nav-folder"><details class="a4-help-nav-details" data-nav-path="' . self::escape( $path ) . '">'
                . '<summary class="a4-help-nav-summary"><span class="a4-help-nav-text" dir="auto">' . self::escape( $title ) . '</span>'
                . '<span class="a4-help-nav-count" aria-hidden="true">' . (int)$count . '</span><span class="a4-sr-only"> ' . self::escape( ezpI18n::tr( expHelp::I18N_CONTEXT, '%count pages', null, array( '%count' => $count ) ) ) . '</span></summary>'
                . '<ul class="a4-help-nav-list" role="list">';
            if ( !empty( $child['index_id'] ) )
                $out .= '<li class="a4-help-nav-page"><a class="a4-help-nav-link is-index" href="' . self::escape( $this->pageUrl( $child['index_id'] ) ) . '">'
                    . self::escape( $labels['overview'] ) . '</a></li>';
            $out .= $this->buildLevel( $child, $names, $labels ) . '</ul></details></li>';
        }
        foreach ( isset( $node['documents'] ) ? $node['documents'] : array() as $document )
        {
            $title = isset( $document['title'] ) && trim( (string)$document['title'] ) !== '' ? $document['title'] : basename( (string)$document['id'] );
            $out .= '<li class="a4-help-nav-page"><a class="a4-help-nav-link" href="' . self::escape( $this->pageUrl( $document['id'] ) ) . '" dir="auto">'
                . self::escape( $title ) . '</a></li>';
        }
        return $out;
    }

    /**
     * @param string $id
     * @return string the viewer address of a page, with the siteaccess prefix
     */
    protected function pageUrl( $id )
    {
        return $this->viewerBase . '/' . implode( '/', array_map( 'rawurlencode', explode( '/', ltrim( (string)$id, '/' ) ) ) );
    }

    /**
     * @param array $node
     * @return int the pages below a node, its own README/index included
     */
    public static function countPages( array $node )
    {
        $count = ( empty( $node['index_id'] ) ? 0 : 1 ) + ( isset( $node['documents'] ) ? count( $node['documents'] ) : 0 );
        foreach ( isset( $node['children'] ) ? $node['children'] : array() as $child )
            $count += self::countPages( $child );
        return $count;
    }

    /**
     * A folder's name: exphelp.ini [NavigationSettings] FolderNames[<path>], else the title of its README/index, else
     * the built-in name (FOLDER_NAMES, translated), else the humanized directory name.
     *
     * @param array $node
     * @param array $names path => name, configured
     * @return string
     */
    public static function folderTitle( array $node, array $names )
    {
        $path = (string)$node['path'];
        if ( isset( $names[$path] ) && $names[$path] !== '' )
            return $names[$path];
        $title = isset( $node['title'] ) ? trim( (string)$node['title'] ) : '';
        $humanized = expHelpDocumentIndex::humanize( isset( $node['name'] ) ? (string)$node['name'] : basename( $path ) );
        if ( $title !== '' && $title !== $humanized )
            return $title;
        if ( isset( self::FOLDER_NAMES[$path] ) )
            return ezpI18n::tr( expHelp::I18N_CONTEXT, self::FOLDER_NAMES[$path] );
        return $title !== '' ? $title : $humanized;
    }

    /**
     * The configured folder names, exphelp.ini [NavigationSettings] FolderNames[<path>]=<name>.
     *
     * @return array path => name
     */
    public static function folderNames()
    {
        $names = array();
        $configured = expHelp::setting( 'NavigationSettings', 'FolderNames', array() );
        foreach ( is_array( $configured ) ? $configured : array() as $path => $name )
            if ( is_string( $path ) && trim( (string)$name ) !== '' )
                $names[trim( $path, '/' )] = trim( (string)$name );
        return $names;
    }

    /**
     * @param string $text
     * @return string
     */
    protected static function escape( $text )
    {
        return htmlspecialchars( (string)$text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
    }
}

?>
