<?php
/**
 * The navigation tree of the help (doc/eng-us/features/6.0/help-system.md, "Viewer"): every page the index serves, in its
 * folders, as nested lists. Folders are native <details> (they open and close without JavaScript) with the number of
 * pages below them; a folder's README.md or index.md is its "Overview" link. The viewer opens the folders on the way
 * to the current page and marks it with aria-current; the contents page (exphelp/contents) opens them all.
 *
 * The HTML of the whole tree is built once per index signature, siteaccess and language and cached in
 * <help cache>/nav/; a request only marks its current page and open folders in it, so a tree of 1 200 pages costs a
 * file read. Nothing is capped: every visible document of expHelpDocumentIndex::tree() is listed, at any depth.
 * That whole tree is the contents page's (html()). The viewer shows only the way to its page (pathHtml()): the
 * other folders are closed and their lists are loaded when they are opened (exphelp/navigation, folderHtml()).
 *
 * Folder names: exphelp.ini [NavigationSettings] FolderNames[<path>], else the title of the folder's README/index,
 * else the built-in names below, else the humanized directory name.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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
        // the documentation of the active extensions (ext/<name>/...), one folder per extension
        'ext' => 'Extensions',
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
    const VERSION = 3;

    /** Where a folded folder's pages go once the current page is inside it (never in a page: built here only) */
    const FOLD_MARK = '<!--exphelp-fold:%s-->';

    /** @var string[]|null [NavigationSettings] FoldedFolders[], read once */
    protected $folded = null;

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
        $this->navigationBase = expHelpLinks::absolute( expHelp::url( 'navigation' ) );
    }

    /** @var string the address of the folder listings with the siteaccess prefix, a folder is appended to it */
    protected $navigationBase;

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
            return self::stripFoldMarks( str_replace( '<details class="a4-help-nav-details" data-nav-path=', '<details open class="a4-help-nav-details" data-nav-path=', $html ) );
        $currentId = trim( (string)$currentId, '/' );
        if ( $currentId === '' )
            return self::stripFoldMarks( $html );
        $segments = explode( '/', $currentId );
        array_pop( $segments );
        $dir = '';
        foreach ( $segments as $segment )
        {
            $dir = $dir === '' ? $segment : $dir . '/' . $segment;
            // a folded folder on the way to the page: its own pages are put in (folded folders below it stay folded)
            $mark = sprintf( self::FOLD_MARK, self::escape( $dir ) );
            if ( strpos( $html, $mark ) !== false )
                $html = str_replace( $mark, $this->cachedFolder( $dir ), $html );
            $html = str_replace( '<details class="a4-help-nav-details" data-nav-path="' . self::escape( $dir ) . '">',
                                 '<details open class="a4-help-nav-details" data-nav-path="' . self::escape( $dir ) . '">', $html );
        }
        $href = 'href="' . self::escape( $this->pageUrl( $currentId ) ) . '"';
        return self::stripFoldMarks( str_replace( $href, $href . ' aria-current="page"', $html ) );
    }

    /** Where the list items of a folder's level start and end in a cached level (never in a page: built here only) */
    const LEVEL_MARK = '<!--exphelp-level:%s-->';
    const LEVEL_END_MARK = '<!--/exphelp-level:%s-->';

    /**
     * The tree of the viewer: only the way to $currentId is open. The top-level folders are listed, each folder on
     * the way to the page is open with its own pages and folders, and the page is marked with aria-current. Every
     * other folder is a closed <details> with its overview link and a link to its own listing (exphelp/navigation/
     * <folder>), whose list exphelp.js loads into it when it is opened (data-nav-lazy, the fragment address). A page
     * of the help carries a few dozen links instead of every page (6 500 on alpha, 1.5 MB on every page).
     *
     * Each level (a folder's direct folders and pages) is built once per index signature and cached in <help cache>/nav/,
     * so a request reads a handful of small files and never walks the tree.
     *
     * @param string $currentId the page shown ('' for none)
     * @return string HTML (safe: every text and address is escaped)
     */
    public function pathHtml( $currentId = '' )
    {
        $currentId = trim( (string)$currentId, '/' );
        $html = '<ul class="a4-help-nav-list is-top" role="list">' . $this->cachedLevel( '' ) . '</ul>';
        if ( $currentId !== '' )
        {
            $segments = explode( '/', $currentId );
            array_pop( $segments );
            $dir = '';
            foreach ( $segments as $segment )
            {
                $dir = $dir === '' ? $segment : $dir . '/' . $segment;
                $expanded = self::expand( $html, $dir, $this->cachedLevel( $dir ) );
                if ( $expanded === null )
                    break;
                $html = $expanded;
            }
            $href = 'href="' . self::escape( $this->pageUrl( $currentId ) ) . '"';
            $html = str_replace( $href, $href . ' aria-current="page"', $html );
        }
        return self::stripLevelMarks( $html );
    }

    /**
     * The list items of one folder (its overview link, its folders closed and loaded on demand, its pages): the
     * fragment exphelp/navigation/<folder>/(format)/fragment answers with, and the listing of that view.
     *
     * @param string $path the folder, '' for the top
     * @param string $currentId a page to mark with aria-current ('' for none)
     * @return string|null HTML, null when the tree has no such folder
     */
    public function folderHtml( $path, $currentId = '' )
    {
        $path = trim( (string)$path, '/' );
        $html = $this->cachedLevel( $path );
        if ( $html === null )
            return null;
        if ( $currentId !== '' )
        {
            $href = 'href="' . self::escape( $this->pageUrl( trim( (string)$currentId, '/' ) ) ) . '"';
            $html = str_replace( $href, $href . ' aria-current="page"', $html );
        }
        return self::stripLevelMarks( $html );
    }

    /** @var array|null expHelpDocumentIndex::tree(), walked at most once per instance */
    protected $tree = null;

    /**
     * @return array the index's tree, built once for this instance (it walks every page)
     */
    protected function tree()
    {
        if ( $this->tree === null )
            $this->tree = $this->index->tree();
        return $this->tree;
    }

    /**
     * The tree's node of a folder, its title and its page count, or null when there is none.
     *
     * @param string $path
     * @return array|null array( 'path', 'title', 'count', 'index_id' )
     */
    public function folderInfo( $path )
    {
        $node = self::findNode( $this->tree(), trim( (string)$path, '/' ) );
        if ( $node === null )
            return null;
        return array( 'path' => (string)$node['path'], 'title' => $node['path'] === '' ? '' : self::folderTitle( $node, self::folderNames() ),
                      'count' => self::countPages( $node ), 'index_id' => empty( $node['index_id'] ) ? '' : (string)$node['index_id'] );
    }

    /**
     * Opens the closed folder $dir in $html and puts its level in.
     *
     * @param string $html
     * @param string $dir
     * @param string|null $level cachedLevel( $dir )
     * @return string|null null when $html has no such closed folder
     */
    protected static function expand( $html, $dir, $level )
    {
        $escaped = self::escape( $dir );
        $start = sprintf( self::LEVEL_MARK, $escaped );
        $end = sprintf( self::LEVEL_END_MARK, $escaped );
        $from = strpos( $html, $start );
        $to = $from === false ? false : strpos( $html, $end, $from );
        if ( $level === null || $from === false || $to === false )
            return null;
        $html = substr( $html, 0, $from ) . $level . substr( $html, $to + strlen( $end ) );
        $open = '<details class="a4-help-nav-details" data-nav-path="' . $escaped . '" data-nav-lazy="';
        $at = strpos( $html, $open );
        if ( $at === false )
            return $html;
        $close = strpos( $html, '">', $at + strlen( $open ) );
        return substr( $html, 0, $at ) . '<details open class="a4-help-nav-details" data-nav-path="' . $escaped . '">' . substr( $html, $close + 2 );
    }

    /**
     * @param string $html
     * @return string without the level marks
     */
    protected static function stripLevelMarks( $html )
    {
        return strpos( $html, '<!--' ) === false ? $html : (string)preg_replace( '#<!--/?exphelp-level:[^>]*-->#', '', $html );
    }

    /**
     * A folder's node in the tree.
     *
     * @param array $tree
     * @param string $path '' for the top
     * @return array|null
     */
    public static function findNode( array $tree, $path )
    {
        $node = $tree;
        if ( $path === '' )
            return $node;
        $dir = '';
        foreach ( explode( '/', $path ) as $segment )
        {
            $dir = $dir === '' ? $segment : $dir . '/' . $segment;
            $found = null;
            foreach ( isset( $node['children'] ) ? $node['children'] : array() as $child )
                if ( isset( $child['path'] ) && (string)$child['path'] === $dir )
                {
                    $found = $child;
                    break;
                }
            if ( $found === null )
                return null;
            $node = $found;
        }
        return $node;
    }

    /**
     * One level of the viewer's tree, from the cache or built and stored: the top's "About" link or a folder's
     * overview link, its folders closed (each with its own level marks around its overview and listing links, so
     * pathHtml() can open it) and its pages.
     *
     * @param string $path '' for the top
     * @return string|null null when the tree has no such folder (or it lists no page)
     */
    protected function cachedLevel( $path )
    {
        $file = $this->cacheFile( 'level' . "\0" . $path );
        if ( is_file( $file ) && is_string( $html = @file_get_contents( $file ) ) && $html !== '' )
            return $html === '-' ? null : $html;
        $node = self::findNode( $this->tree(), $path );
        $html = $node === null || ( $path !== '' && self::countPages( $node ) === 0 ) ? null : $this->buildLazyLevel( $node );
        $this->store( $file, $html === null ? '-' : $html );
        return $html;
    }

    /**
     * @param array $node
     * @return string the list items of one level, its folders closed and loaded on demand
     */
    protected function buildLazyLevel( array $node )
    {
        $names = self::folderNames();
        $overview = ezpI18n::tr( expHelp::I18N_CONTEXT, 'Overview' );
        $out = '';
        if ( !empty( $node['index_id'] ) )
            $out .= '<li class="a4-help-nav-page"><a class="a4-help-nav-link is-index" href="' . self::escape( $this->pageUrl( $node['index_id'] ) ) . '">'
                . self::escape( (string)$node['path'] === '' ? ezpI18n::tr( expHelp::I18N_CONTEXT, 'About this documentation' ) : $overview ) . '</a></li>';
        foreach ( isset( $node['children'] ) ? $node['children'] : array() as $child )
        {
            $count = self::countPages( $child );
            if ( $count === 0 )
                continue;
            $path = (string)$child['path'];
            $escaped = self::escape( $path );
            $listing = $this->folderUrl( $path );
            $out .= '<li class="a4-help-nav-folder"><details class="a4-help-nav-details" data-nav-path="' . $escaped . '" data-nav-lazy="'
                . self::escape( $listing . '/(format)/fragment' ) . '">'
                . '<summary class="a4-help-nav-summary"><span class="a4-help-nav-text" dir="auto">' . self::escape( self::folderTitle( $child, $names ) ) . '</span>'
                . '<span class="a4-help-nav-count" aria-hidden="true">' . (int)$count . '</span><span class="a4-sr-only"> ' . self::escape( ezpI18n::tr( expHelp::I18N_CONTEXT, '%count pages', null, array( '%count' => $count ) ) ) . '</span></summary>'
                . '<ul class="a4-help-nav-list" role="list">' . sprintf( self::LEVEL_MARK, $escaped );
            if ( !empty( $child['index_id'] ) )
                $out .= '<li class="a4-help-nav-page"><a class="a4-help-nav-link is-index" href="' . self::escape( $this->pageUrl( $child['index_id'] ) ) . '">'
                    . self::escape( $overview ) . '</a></li>';
            $out .= '<li class="a4-help-nav-page a4-help-nav-more"><a class="a4-help-nav-link is-more" href="' . self::escape( $listing ) . '">'
                . self::escape( ezpI18n::tr( expHelp::I18N_CONTEXT, 'All pages of this folder' ) ) . '</a></li>'
                . sprintf( self::LEVEL_END_MARK, $escaped ) . '</ul></details></li>';
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
     * @param string $path a folder
     * @return string the address of its listing (exphelp/navigation/<folder>), with the siteaccess prefix
     */
    protected function folderUrl( $path )
    {
        return $this->navigationBase . ( $path === '' ? '' : '/' . implode( '/', array_map( 'rawurlencode', explode( '/', ltrim( (string)$path, '/' ) ) ) ) );
    }

    /**
     * Sets the folded folders instead of [NavigationSettings] FoldedFolders[] (tests, callers with their own list).
     *
     * @param string[] $folders paths below doc/
     * @return $this
     */
    public function setFoldedFolders( array $folders )
    {
        $this->folded = array_values( array_filter( array_map( function ( $f ) { return trim( (string)$f, '/ ' ); }, $folders ), 'strlen' ) );
        return $this;
    }

    /**
     * Whether a folder is folded: exphelp.ini [NavigationSettings] FoldedFolders[] names it or a folder above it.
     * A folded folder shows its overview link and its page count; its pages are listed only while the current page
     * is inside it (the folder's index page links all of them). For the large generated reference trees, which
     * would otherwise put thousands of links on every page.
     *
     * @param string $path
     * @return bool
     */
    public function isFolded( $path )
    {
        if ( $this->folded === null )
        {
            $this->folded = array();
            $configured = expHelp::setting( 'NavigationSettings', 'FoldedFolders', array() );
            foreach ( is_array( $configured ) ? $configured : array() as $folder )
                if ( is_string( $folder ) && trim( $folder, '/ ' ) !== '' )
                    $this->folded[] = trim( $folder, '/ ' );
        }
        foreach ( $this->folded as $folder )
            if ( $path === $folder || strpos( $path, $folder . '/' ) === 0 )
                return true;
        return false;
    }

    /**
     * The list items of a folded folder (its overview link excluded), from the cache or built and stored.
     *
     * @param string $path
     * @return string
     */
    protected function cachedFolder( $path )
    {
        $file = $this->cacheFile( 'folder' . "\0" . $path );
        if ( is_file( $file ) && is_string( $html = @file_get_contents( $file ) ) )
            return $html;
        $node = $this->index->tree();
        foreach ( explode( '/', $path ) as $segment )
        {
            $found = null;
            foreach ( isset( $node['children'] ) ? $node['children'] : array() as $child )
                if ( (string)$child['path'] === ( $node['path'] === '' || !isset( $node['path'] ) ? $segment : $node['path'] . '/' . $segment ) )
                    $found = $child;
            if ( $found === null )
                return '';
            $node = $found;
        }
        $html = $this->buildLevel( $node, self::folderNames(), array( 'overview' => ezpI18n::tr( expHelp::I18N_CONTEXT, 'Overview' ) ) );
        $this->store( $file, $html );
        return $html;
    }

    /**
     * @param string $html
     * @return string without the marks of folded folders that stay folded
     */
    protected static function stripFoldMarks( $html )
    {
        return strpos( $html, '<!--exphelp-fold:' ) === false ? $html : preg_replace( '/<!--exphelp-fold:[^>]*-->/', '', $html );
    }

    /**
     * @param string $key what is cached besides the whole tree
     * @return string the cache file of the tree ($key '') or of a part of it
     */
    protected function cacheFile( $key = '' )
    {
        $dir = rtrim( (string)$this->index->cacheDir(), '/' ) . '/nav';
        $locale = class_exists( 'eZLocale' ) ? eZLocale::instance()->localeFullCode() : '';
        return $dir . '/' . sha1( self::VERSION . "\0" . $this->index->signature() . "\0" . $this->viewerBase . "\0" . $locale
                                  . "\0" . serialize( self::folderNames() ) . "\0" . serialize( $this->folded ) . "\0" . $key ) . '.html';
    }

    /**
     * Writes a cache file atomically (the tree is shared by every request).
     *
     * @param string $file
     * @param string $html
     */
    protected function store( $file, $html )
    {
        $dir = dirname( $file );
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        if ( is_dir( $dir ) && is_writable( $dir ) )
        {
            $tmp = $file . '.' . getmypid() . '.tmp';
            if ( @file_put_contents( $tmp, $html, LOCK_EX ) !== false )
            {
                $permissions = eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' );
                @chmod( $tmp, eZFile::fileMode( preg_match( '/^0?[0-7]{3}$/', (string)$permissions ) ? octdec( $permissions ) : 0664 ) );
                if ( !@rename( $tmp, $file ) )
                    @unlink( $tmp );
            }
        }
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
        $this->isFolded( '' );
        $file = $this->cacheFile();
        if ( is_file( $file ) )
        {
            $html = @file_get_contents( $file );
            if ( is_string( $html ) && $html !== '' )
                return $html;
        }
        $html = $this->build( $this->index->tree() );
        $this->store( $file, $html );
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
            // a folded folder lists its pages only on the way to the current page (html() puts them in)
            $out .= ( $this->isFolded( $path ) && !empty( $child['index_id'] ) ? sprintf( self::FOLD_MARK, self::escape( $path ) ) : $this->buildLevel( $child, $names, $labels ) )
                . '</ul></details></li>';
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
