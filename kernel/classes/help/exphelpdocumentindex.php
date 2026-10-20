<?php
/**
 * File containing the expHelpDocumentIndex class.
 *
 * The index of the help pages (doc/en/us/features/6.0/help-system.md, "The index"): one entry per document of the
 * allowlist (expHelpAllowlist: git-tracked files below doc/ and opted-in extension doc/ directories), with its title,
 * headings and their anchors, summary, audiences, category, type, order and keywords. The views, the search and the
 * console command exp:help read it; nothing reads a file of doc/ without asking it first.
 *
 * A page is named by its id: its path below doc/ ("guides/caches.md"), or "ext/<extension>/<path below its doc/>"
 * for an extension's page. The entry's 'path' is the path relative to the installation root ("doc/en/us/guides/caches.md").
 *
 *   $index = expHelpDocumentIndex::instance();
 *   $index->documents();                  // 'guides/caches.md' => array( 'id', 'path', 'title', 'headings', ... )
 *   $index->document( $id );              // one entry, or null: not a help page (unknown, untracked, excluded)
 *   $index->source( $id );                // the raw Markdown or text of a page, or null
 *   $index->asset( $id );                 // the absolute file name of an image of doc/, or null
 *   $index->tree();                       // the folders and pages, for the navigation
 *   $index->signature();                  // changes with any file, the git index, the manifest or the settings
 *
 * The index is cached in var/<site>/cache/exphelp/index-<signature>.php (a var_export file, so OPcache keeps it), and
 * the allowlist in allowlist-<stamp>.php. The signature covers the git index and manifest (files added or removed),
 * the size and time of every listed file (files changed) and the settings, so a stale index is never used. The look
 * at every file is reused for [IndexSettings] StatFilesInterval seconds by every process (signature.php in the cache
 * directory), so an edited page shows within that time. With [IndexSettings] StatFiles=disabled the files are not looked at on each request: a changed page is then picked up
 * by "./console exp:help index" or the next change of the git index (a deploy). When git can be run, the build also
 * reads the history of the roots once ("git log"): the date of each page's last change and the latest changes for
 * the dashboard ([IndexSettings] GitHistory=disabled leaves it out).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpDocumentIndex
{
    /** Bumped when the entries change shape, so an old cache file is not read */
    const FORMAT = 2;

    /** The longest summary, in characters */
    const SUMMARY_LENGTH = 220;

    /** How many recent changes the index keeps */
    const RECENT_KEPT = 50;

    /** The image types served by the asset view */
    const MIME_TYPES = array( 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif',
                              'webp' => 'image/webp' );

    /** @var expHelpDocumentIndex|null */
    protected static $instance = null;

    /** @var string|null the request the instance belongs to (Velocity workers live across requests) */
    protected static $instanceRequest = null;

    /** @var bool whether the instance was set by setInstance() (kept until reset) */
    protected static $instanceFixed = false;

    /** @var expHelpAllowlist */
    protected $allowlist;

    /** @var expHelpAudience */
    protected $audience;

    /** @var string|null the cache directory; null: no cache */
    protected $cacheDir;

    /** @var bool whether signature() looks at every file */
    protected $statFiles;

    /** @var int seconds for which the files' signature, once taken, is reused by every process (0: never) */
    protected $statInterval;

    /** @var bool whether the next signature() looks at the files whatever was checked before (rebuild()) */
    protected $statNow = false;

    /** The file in the cache directory that holds the last signature taken from the files and when */
    const CHECKED_SIGNATURE_FILE = 'signature.php';

    /** @var bool whether the build reads the git history */
    protected $gitHistory;

    /** @var array|null the allowlist entries, cached */
    protected $files = null;

    /** @var string|null */
    protected $signature = null;

    /** @var array|null the index: array( 'format', 'signature', 'built', 'documents', 'assets', 'sources', 'recent' ) */
    protected $data = null;

    /**
     * @param expHelpAllowlist|null $allowlist default expHelpAllowlist::fromIni()
     * @param array $options cache_dir (string, or false for no cache; default expHelp::cacheDirectory()),
     *                       audience (expHelpAudience, default from exphelp.ini), stat_files (bool, default true),
     *                       stat_interval (int seconds, default 0: look at the files on every call), git_history
     *                       (bool, default true)
     */
    public function __construct( ?expHelpAllowlist $allowlist = null, array $options = array() )
    {
        $this->allowlist = $allowlist !== null ? $allowlist : expHelpAllowlist::fromIni();
        $this->audience = isset( $options['audience'] ) && $options['audience'] instanceof expHelpAudience
            ? $options['audience'] : expHelpAudience::fromIni();
        $cacheDir = array_key_exists( 'cache_dir', $options ) ? $options['cache_dir'] : self::defaultCacheDir();
        $this->cacheDir = $cacheDir === false || $cacheDir === null || $cacheDir === '' ? null : rtrim( (string)$cacheDir, '/' );
        $this->statFiles = array_key_exists( 'stat_files', $options ) ? (bool)$options['stat_files'] : true;
        $this->statInterval = isset( $options['stat_interval'] ) ? max( 0, (int)$options['stat_interval'] ) : 0;
        $this->gitHistory = array_key_exists( 'git_history', $options ) ? (bool)$options['git_history'] : true;
    }

    /**
     * The index of the installation, configured by exphelp.ini; one per request (or the one given to setInstance()).
     *
     * @return expHelpDocumentIndex
     */
    public static function instance()
    {
        if ( self::$instanceFixed && self::$instance !== null )
            return self::$instance;
        $request = getmypid() . ':' . ( isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : '' );
        if ( self::$instance === null || self::$instanceRequest !== $request )
        {
            self::$instance = new self( expHelpAllowlist::fromIni(), array(
                'stat_files' => self::iniSetting( 'StatFiles', 'enabled' ) !== 'disabled',
                'stat_interval' => (int)self::iniSetting( 'StatFilesInterval', '0' ),
                'git_history' => self::iniSetting( 'GitHistory', 'enabled' ) !== 'disabled' ) );
            self::$instanceRequest = $request;
        }
        return self::$instance;
    }

    /**
     * Makes every instance() call return this index (tests, tools); null goes back to the configured one.
     *
     * @param expHelpDocumentIndex|null $index
     */
    public static function setInstance( ?expHelpDocumentIndex $index = null )
    {
        self::$instance = $index;
        self::$instanceFixed = $index !== null;
        self::$instanceRequest = null;
    }

    /**
     * Forgets the instance (tests, and after a rebuild in the same process).
     */
    public static function resetInstance()
    {
        self::setInstance( null );
    }

    /**
     * A setting of exphelp.ini [IndexSettings].
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    protected static function iniSetting( $name, $default )
    {
        if ( !class_exists( 'eZINI' ) || !eZINI::exists( 'exphelp.ini' ) )
            return $default;
        $ini = eZINI::instance( 'exphelp.ini' );
        return $ini->hasVariable( 'IndexSettings', $name ) ? (string)$ini->variable( 'IndexSettings', $name ) : $default;
    }

    /**
     * var/<site>/cache/exphelp.
     *
     * @return string|null
     */
    public static function defaultCacheDir()
    {
        if ( class_exists( 'expHelp' ) && class_exists( 'eZINI' ) )
            return expHelp::cacheDirectory();
        return class_exists( 'eZSys' ) ? eZSys::cacheDirectory() . '/exphelp' : null;
    }

    /**
     * @return string|null the cache directory (created on demand), null when the index is not cached
     */
    public function cacheDir()
    {
        if ( $this->cacheDir !== null && !is_dir( $this->cacheDir ) )
            self::mkdir( $this->cacheDir );
        return $this->cacheDir;
    }

    /**
     * @return expHelpAllowlist
     */
    public function allowlist()
    {
        return $this->allowlist;
    }

    /**
     * @return expHelpAudience
     */
    public function audience()
    {
        return $this->audience;
    }

    /**
     * The id of a path relative to the installation root: doc/en/us/guides/x.md -> guides/x.md,
     * extension/foo/doc/x.md -> ext/foo/x.md; other configured roots keep their path. null for a path outside the roots.
     *
     * @param string $path
     * @return string|null
     */
    public function idOf( $path )
    {
        $path = expHelpAllowlist::normalize( $path );
        if ( $path === null )
            return null;
        foreach ( $this->allowlist->roots() as $root )
        {
            if ( strncmp( $path, $root . '/', strlen( $root ) + 1 ) !== 0 )
                continue;
            $rest = substr( $path, strlen( $root ) + 1 );
            if ( $root === 'doc' )
                return $rest;
            if ( preg_match( '#^extension/([^/]+)/doc$#', $root, $m ) )
                return 'ext/' . $m[1] . '/' . $rest;
            return $path;
        }
        return null;
    }

    /**
     * The path relative to the installation root of an id (the reverse of idOf()); null when it names no root.
     * Only the syntax is checked: whether the file is a help page is document()'s and asset()'s question.
     *
     * @param string $id
     * @return string|null
     */
    public function pathOf( $id )
    {
        $id = expHelpAllowlist::normalize( $id );
        if ( $id === null )
            return null;
        $roots = $this->allowlist->roots();
        if ( preg_match( '#^ext/([^/]+)/(.+)$#', $id, $m ) && in_array( 'extension/' . $m[1] . '/doc', $roots, true ) )
            return 'extension/' . $m[1] . '/doc/' . $m[2];
        foreach ( $roots as $root )
            if ( $root !== 'doc' && strncmp( $id, $root . '/', strlen( $root ) + 1 ) === 0 )
                return $id;
        return in_array( 'doc', $roots, true ) ? 'doc/' . $id : null;
    }

    /**
     * Changes whenever a help file is added, removed or changed, or the settings change.
     *
     * @return string 40 hex characters
     */
    public function signature()
    {
        if ( $this->signature !== null )
            return $this->signature;
        $parts = array( 'format ' . self::FORMAT, $this->allowlist->stamp(), json_encode( $this->audience->config() ),
                        $this->gitHistory ? 'history' : 'no history',
                        self::rendererSlugs() ? 'renderer slug' : 'own slug' );
        // the code that builds the entries: a deploy of a changed parser or slugger builds the index again
        foreach ( array( __FILE__, ( new ReflectionClass( 'expHelpFrontMatter' ) )->getFileName(),
                         ( new ReflectionClass( 'expHelpAudience' ) )->getFileName(),
                         class_exists( 'expHelpMarkdownRenderer' ) ? ( new ReflectionClass( 'expHelpMarkdownRenderer' ) )->getFileName() : '',
                         class_exists( 'expHelpRecordRenderer' ) ? ( new ReflectionClass( 'expHelpRecordRenderer' ) )->getFileName() : '' ) as $code )
            if ( is_string( $code ) && $code !== '' )
                $parts[] = basename( $code ) . ':' . (int)@filemtime( $code ) . ':' . (int)@filesize( $code );
        if ( !$this->statFiles )
            return $this->signature = sha1( implode( "\n", $parts ) );
        // Looking at every listed file is the expensive part (thousands of stats; under Velocity each one is answered
        // by its file layer, ~40 microseconds instead of ~2). With StatFilesInterval the answer is kept in the cache
        // directory for that many seconds, for every process: a page edited meanwhile shows when the time is up.
        $base = sha1( implode( "\n", $parts ) );
        $statNow = $this->statNow;
        $this->statNow = false;
        if ( !$statNow && $this->statInterval > 0 )
        {
            $checked = $this->checkedSignature( $base );
            if ( $checked !== null )
                return $this->signature = $checked;
        }
        foreach ( $this->files() as $path => $file )
        {
            $name = $this->allowlist->rootDir() . '/' . $path;
            $parts[] = $path . ':' . (int)@filesize( $name ) . ':' . (int)@filemtime( $name );
        }
        $this->signature = sha1( implode( "\n", $parts ) );
        if ( $this->statInterval > 0 )
            $this->store( self::CHECKED_SIGNATURE_FILE, array( 'base' => $base, 'signature' => $this->signature, 'checked' => time() ),
                          self::CHECKED_SIGNATURE_FILE );
        return $this->signature;
    }

    /**
     * The signature another call took from the files less than StatFilesInterval seconds ago, for the same settings,
     * allowlist and code; null when there is none.
     *
     * @param string $base the hash of everything but the files
     * @return string|null
     */
    protected function checkedSignature( $base )
    {
        $checked = $this->read( self::CHECKED_SIGNATURE_FILE );
        if ( !is_array( $checked ) || !isset( $checked['base'], $checked['signature'], $checked['checked'] )
             || $checked['base'] !== $base || !is_string( $checked['signature'] ) )
            return null;
        $age = time() - (int)$checked['checked'];
        return $age >= 0 && $age < $this->statInterval ? $checked['signature'] : null;
    }

    /**
     * The pages: id => entry, in path order. An entry:
     *   id, path (doc/...), suffix (md, txt, ... or ''), format (markdown|text), title, headings (list of
     *   array( level, text, anchor )), summary, audience (list), category, type, order, keywords (list), featured,
     *   hidden, context (list of module/view), mtime (file), changed (last commit, else mtime), size, root
     *
     * @return array
     */
    public function documents()
    {
        return $this->load()['documents'];
    }

    /**
     * The pages that are not hidden (front matter "hidden: true"), for navigation and search.
     *
     * @return array
     */
    public function visibleDocuments()
    {
        return array_filter( $this->documents(), function ( $document ) { return empty( $document['hidden'] ); } );
    }

    /**
     * One page, or null when the id is not a help page.
     *
     * @param string $id
     * @return array|null
     */
    public function document( $id )
    {
        $id = expHelpAllowlist::normalize( $id );
        $documents = $this->documents();
        return $id !== null && isset( $documents[$id] ) ? $documents[$id] : null;
    }

    /**
     * Whether an id names a page or an image of the help.
     *
     * @param string $id
     * @return bool
     */
    public function isAllowed( $id )
    {
        $path = $this->pathOf( $id );
        return $path !== null && isset( $this->files()[$path] );
    }

    /**
     * The raw text of a page (front matter included), or null when the id is not a page of the help or the file
     * fails the checks now.
     *
     * @param string $id
     * @return string|null
     */
    public function source( $id )
    {
        $document = $this->document( $id );
        if ( $document === null )
            return null;
        $file = $this->allowlist->safePath( $document['path'], $this->files() );
        if ( $file === null )
            return null;
        $text = @file_get_contents( $file );
        return is_string( $text ) ? self::toUtf8( $text ) : null;
    }

    /**
     * The text of a page without its front matter.
     *
     * @param string $id
     * @return string|null
     */
    public function body( $id )
    {
        $text = $this->source( $id );
        return $text === null ? null : expHelpFrontMatter::split( $text )[1];
    }

    /**
     * The absolute file name of an image of the help, or null.
     *
     * @param string $id
     * @return string|null
     */
    public function asset( $id )
    {
        $info = $this->assetInfo( $id );
        return $info === null ? null : $info['file'];
    }

    /**
     * An image of the help: array( 'id', 'path', 'file' => absolute file name, 'mime', 'size' ), or null.
     *
     * @param string $id
     * @return array|null
     */
    public function assetInfo( $id )
    {
        $path = $this->pathOf( $id );
        $files = $this->files();
        if ( $path === null || !isset( $files[$path] ) || $files[$path]['kind'] !== 'asset' )
            return null;
        $suffix = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
        if ( !isset( self::MIME_TYPES[$suffix] ) )
            return null;
        $file = $this->allowlist->safePath( $path, $files );
        if ( $file === null )
            return null;
        return array( 'id' => $this->idOf( $path ), 'path' => $path, 'file' => $file, 'mime' => self::MIME_TYPES[$suffix],
                      'size' => (int)filesize( $file ) );
    }

    /**
     * The images of the help: id => array( 'id', 'path', 'mtime', 'size' ).
     *
     * @return array
     */
    public function assets()
    {
        return $this->load()['assets'];
    }

    /**
     * Which allowlist source listed each root: root => gitindex|gitcommand|manifest|given|none.
     *
     * @return array
     */
    public function sources()
    {
        return $this->load()['sources'];
    }

    /**
     * The configured categories with their page counts: id => array( 'id', 'name', 'icon', 'count' ).
     *
     * @return array
     */
    public function categories()
    {
        $categories = array();
        foreach ( $this->audience->categories() as $id => $category )
            $categories[$id] = array( 'id' => $id, 'name' => $category['name'], 'icon' => $category['icon'], 'count' => 0 );
        foreach ( $this->visibleDocuments() as $document )
            if ( isset( $categories[$document['category']] ) )
                ++$categories[$document['category']]['count'];
        return $categories;
    }

    /**
     * The audiences with their page counts: id => array( 'id', 'name', 'icon', 'count' ).
     *
     * @return array
     */
    public function audiences()
    {
        $audiences = array();
        foreach ( $this->audience->audiences() as $id => $audience )
            $audiences[$id] = array( 'id' => $id, 'name' => $audience['name'], 'icon' => $audience['icon'], 'count' => 0 );
        foreach ( $this->visibleDocuments() as $document )
            foreach ( $document['audience'] as $id )
                if ( isset( $audiences[$id] ) )
                    ++$audiences[$id]['count'];
        return $audiences;
    }

    /**
     * The types with their page counts: id => array( 'id', 'name', 'count' ).
     *
     * @return array
     */
    public function types()
    {
        $types = array();
        foreach ( $this->audience->types() as $id => $name )
            $types[$id] = array( 'id' => $id, 'name' => $name, 'count' => 0 );
        foreach ( $this->visibleDocuments() as $document )
            if ( isset( $types[$document['type']] ) )
                ++$types[$document['type']]['count'];
        return $types;
    }

    /**
     * The visible pages of a category, in their order (front matter order, then title).
     *
     * @param string $category
     * @param string|null $audience only those for this audience
     * @return array id => entry
     */
    public function inCategory( $category, $audience = null )
    {
        $documents = array_filter( $this->visibleDocuments(), function ( $document ) use ( $category, $audience ) {
            return $document['category'] === $category && ( $audience === null || in_array( $audience, $document['audience'], true ) );
        } );
        uasort( $documents, array( __CLASS__, 'compare' ) );
        return $documents;
    }

    /**
     * The visible pages with "featured: true" in their front matter, in their order.
     *
     * @return array id => entry
     */
    public function featured()
    {
        $documents = array_filter( $this->visibleDocuments(), function ( $document ) { return !empty( $document['featured'] ); } );
        uasort( $documents, array( __CLASS__, 'compare' ) );
        return $documents;
    }

    /**
     * The folder tree of the visible pages, for the navigation. A node:
     *   name, path (the folder in id space: 'features/6.0'; '' for the top), title (its README.md/index.md's title,
     *   else the humanized name), index and index_id (the id of that README.md/index.md, or null), order,
     *   children (list of nodes), documents (list of array( 'id', 'path', 'title', 'order', 'type', 'audience' ))
     * Children and documents are in order, then natural title order; a folder's README.md/index.md is its index, not
     * one of its documents.
     *
     * @param string|null $below only this folder (id space, e.g. 'guides')
     * @return array the top node (or the folder's node; an empty node for an unknown folder)
     */
    public function tree( $below = null )
    {
        $root = self::node( '', '', '' );
        $below = $below === null ? null : trim( (string)$below, '/' );
        foreach ( $this->visibleDocuments() as $id => $document )
        {
            $segments = explode( '/', $id );
            $file = array_pop( $segments );
            $node = &$root;
            $dir = '';
            foreach ( $segments as $segment )
            {
                $dir = $dir === '' ? $segment : $dir . '/' . $segment;
                if ( !isset( $node['children'][$segment] ) )
                    $node['children'][$segment] = self::node( $segment, $dir, self::humanize( $segment ) );
                $node = &$node['children'][$segment];
            }
            if ( preg_match( '/^(readme|index)\.(md|markdown|txt)$/i', $file ) && $node['index'] === null )
            {
                $node['index'] = $node['index_id'] = $id;
                if ( $document['order'] !== 0 )
                    $node['order'] = $document['order'];
                if ( !preg_match( '/^readme$/i', $document['title'] ) )
                    $node['title'] = $document['title'];
            }
            else
                $node['documents'][] = array( 'id' => $id, 'path' => $document['path'], 'title' => $document['title'],
                                              'order' => $document['order'], 'type' => $document['type'],
                                              'audience' => $document['audience'] );
            unset( $node );
        }
        $root = self::sortTree( $root );
        if ( $below === null || $below === '' )
            return $root;
        $node = $root;
        foreach ( explode( '/', $below ) as $segment )
        {
            $found = null;
            foreach ( $node['children'] as $child )
                if ( $child['name'] === $segment )
                    $found = $child;
            if ( $found === null )
                return self::node( basename( $below ), $below, self::humanize( basename( $below ) ) );
            $node = $found;
        }
        return $node;
    }

    /**
     * An empty folder node.
     *
     * @param string $name
     * @param string $path
     * @param string $title
     * @return array
     */
    protected static function node( $name, $path, $title )
    {
        return array( 'name' => $name, 'path' => $path, 'title' => $title, 'index' => null, 'index_id' => null,
                      'order' => 1000, 'children' => array(), 'documents' => array() );
    }

    /**
     * @param array $node
     * @return array the node with sorted, list-indexed children and documents
     */
    protected static function sortTree( array $node )
    {
        foreach ( $node['children'] as $name => $child )
            $node['children'][$name] = self::sortTree( $child );
        $children = array_values( $node['children'] );
        usort( $children, array( __CLASS__, 'compareFolders' ) );
        $node['children'] = $children;
        usort( $node['documents'], array( __CLASS__, 'compare' ) );
        return $node;
    }

    /**
     * Folders: order, then versions newest first (6.0 before 3.9), then natural title order.
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function compareFolders( array $a, array $b )
    {
        if ( $a['order'] !== $b['order'] )
            return $a['order'] <=> $b['order'];
        $versionA = preg_match( '/^\d/', $a['name'] );
        $versionB = preg_match( '/^\d/', $b['name'] );
        if ( $versionA && $versionB )
            return version_compare( $b['name'], $a['name'] );
        if ( $versionA !== $versionB )
            return $versionA ? 1 : -1;
        return strnatcasecmp( $a['title'], $b['title'] );
    }

    /**
     * Pages: order, then natural title order.
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function compare( array $a, array $b )
    {
        return ( $a['order'] <=> $b['order'] ) ?: strnatcasecmp( $a['title'], $b['title'] );
    }

    /**
     * The categories with their pages, for the dashboard: list of array( 'id', 'name', 'icon', 'count',
     * 'pages' => list of array( 'id', 'title', 'summary', 'audience', 'type', 'order' ) ) in the configured category
     * order, empty categories left out.
     *
     * @param string|null $audience only pages for this audience
     * @param int $limit pages per category (0: all)
     * @return array
     */
    public function categoryTree( $audience = null, $limit = 0 )
    {
        $list = array();
        foreach ( $this->categories() as $id => $category )
        {
            $pages = array();
            foreach ( $this->inCategory( $id, $audience ) as $documentId => $document )
            {
                $pages[] = array( 'id' => $documentId, 'title' => $document['title'], 'summary' => $document['summary'],
                                  'audience' => $document['audience'], 'type' => $document['type'], 'order' => $document['order'] );
                if ( $limit > 0 && count( $pages ) >= $limit )
                    break;
            }
            if ( !$pages )
                continue;
            $category['count'] = $audience === null ? $category['count'] : count( $this->inCategory( $id, $audience ) );
            $category['pages'] = $pages;
            $list[] = $category;
        }
        return $list;
    }

    /**
     * The pages before and after one in the navigation order (the tree, depth first: a folder's index, its pages,
     * then its subfolders).
     *
     * @param string $id
     * @return array array( 'prev' => entry|null, 'next' => entry|null )
     */
    public function neighbours( $id )
    {
        $order = array();
        $walk = function ( array $node ) use ( &$walk, &$order ) {
            if ( $node['index_id'] !== null )
                $order[] = $node['index_id'];
            foreach ( $node['documents'] as $document )
                $order[] = $document['id'];
            foreach ( $node['children'] as $child )
                $walk( $child );
        };
        $walk( $this->tree() );
        $position = array_search( (string)$id, $order, true );
        if ( $position === false )
            return array( 'prev' => null, 'next' => null );
        return array( 'prev' => $position > 0 ? $this->document( $order[$position - 1] ) : null,
                      'next' => isset( $order[$position + 1] ) ? $this->document( $order[$position + 1] ) : null );
    }

    /**
     * The latest changes of visible pages from the git history, newest first: list of array( 'id', 'title', 'time',
     * 'subject' ) (the commit subject). Empty without git.
     *
     * @param int $limit
     * @return array
     */
    public function recentChanges( $limit = 10 )
    {
        $documents = $this->visibleDocuments();
        $changes = array();
        foreach ( $this->load()['recent'] as $change )
        {
            if ( !isset( $documents[$change['id']] ) )
                continue;
            $changes[] = array( 'id' => $change['id'], 'title' => $documents[$change['id']]['title'],
                                'time' => $change['time'], 'subject' => $change['subject'] );
            if ( count( $changes ) >= $limit )
                break;
        }
        return $changes;
    }

    /**
     * Builds the index again and stores it in the cache, whatever is cached.
     *
     * @return array statistics: array( 'signature', 'documents', 'assets', 'seconds', 'sources', 'cached' => bool )
     */
    public function rebuild()
    {
        $start = microtime( true );
        $this->signature = null;
        $this->statNow = true;
        $this->data = $this->build();
        $cached = $this->store( 'index-' . $this->data['signature'] . '.php', $this->data, 'index-' );
        $pruned = $cached ? $this->pruneStale( (int)floor( $start ) ) : 0;
        return array( 'signature' => $this->data['signature'], 'documents' => count( $this->data['documents'] ),
                      'assets' => count( $this->data['assets'] ), 'seconds' => round( microtime( true ) - $start, 3 ),
                      'sources' => $this->data['sources'], 'cached' => $cached, 'pruned' => $pruned );
    }

    /** The cache files that belong to one index signature and are named by a hash of it: subdirectory => suffix */
    const DERIVED_CACHES = array( 'nav' => 'html', 'render' => 'json' );

    /**
     * Removes what an older index left in the cache directory: the other index-<signature>.php files and the cached
     * navigation trees (nav/) and rendered pages (render/), whose names are hashes that include the index signature,
     * so none of them is read again once the signature changed. Only files written before $before (the start of the
     * rebuild) go: what another process wrote meanwhile stays, and so does the current index.
     *
     * @param int $before a Unix time
     * @return int the number of files removed
     */
    public function pruneStale( $before )
    {
        $dir = $this->cacheDir();
        if ( $dir === null || !is_dir( $dir ) || $this->data === null )
            return 0;
        $current = $dir . '/index-' . $this->data['signature'] . '.php';
        $files = array();
        foreach ( (array)glob( $dir . '/index-*.php' ) as $file )
            if ( $file !== $current && preg_match( '/\/index-[a-f0-9]{40}\.php$/', $file ) )
                $files[] = $file;
        foreach ( self::DERIVED_CACHES as $sub => $suffix )
            foreach ( (array)glob( $dir . '/' . $sub . '/*.' . $suffix ) as $file )
                if ( preg_match( '/\/[a-f0-9]{40}\.' . $suffix . '$/', $file ) )
                    $files[] = $file;
        $removed = 0;
        foreach ( $files as $file )
        {
            clearstatcache( true, $file );
            if ( is_file( $file ) && filemtime( $file ) < (int)$before && @unlink( $file ) )
                ++$removed;
        }
        return $removed;
    }

    /**
     * The index, from the cache when it is current, else built and stored.
     *
     * @return array
     */
    protected function load()
    {
        if ( $this->data !== null )
            return $this->data;
        $signature = $this->signature();
        $cached = $this->read( 'index-' . $signature . '.php' );
        if ( is_array( $cached ) && isset( $cached['format'], $cached['documents'], $cached['signature'] )
             && $cached['format'] === self::FORMAT && $cached['signature'] === $signature )
            $this->data = $cached;
        else
            $this->rebuild();
        $this->storePageTitles();
        return $this->data;
    }

    /** The file of pageTitles(), in the cache directory */
    const PAGE_TITLES_FILE = 'pages.php';

    /**
     * Writes the small list of served pages (id => title) that pageTitles() reads, when it is missing or belongs to
     * another index. Every admin page's Help link checks its page there, never in the full index.
     *
     * @return bool whether the file is current
     */
    protected function storePageTitles()
    {
        $dir = $this->cacheDir();
        if ( $dir === null || $this->data === null )
            return false;
        $current = self::pageTitlesData( $dir );
        if ( is_array( $current ) && isset( $current['signature'] ) && $current['signature'] === $this->data['signature'] )
            return true;
        $titles = array();
        foreach ( $this->data['documents'] as $id => $document )
        {
            // the generated reference examples (type generated, ./console exp:help:examples) are never the help of an
            // admin page: leaving them out keeps this file, included by every admin page, at the size of the written help
            if ( isset( $document['type'] ) && $document['type'] === 'generated' )
                continue;
            $titles[(string)$id] = isset( $document['title'] ) && is_scalar( $document['title'] ) ? (string)$document['title'] : '';
        }
        return $this->store( self::PAGE_TITLES_FILE, array( 'signature' => $this->data['signature'], 'titles' => $titles ), self::PAGE_TITLES_FILE );
    }

    /**
     * The served pages as id => title, from the small file the last index load wrote: cheap enough for every
     * request (one stat and an opcache include). Null when there is none yet (no help page was opened and no
     * `exp:help index` ran since the cache was cleared): then nothing is known and callers should not filter.
     *
     * @param string|null $dir the cache directory, default the installation's
     * @return array|null
     */
    public static function pageTitles( $dir = null )
    {
        $data = self::pageTitlesData( $dir === null ? self::defaultCacheDir() : $dir );
        return is_array( $data ) && isset( $data['titles'] ) && is_array( $data['titles'] ) ? $data['titles'] : null;
    }

    /**
     * @param string|null $dir
     * @return array|null
     */
    protected static function pageTitlesData( $dir )
    {
        if ( !is_string( $dir ) || $dir === '' )
            return null;
        $file = rtrim( $dir, '/' ) . '/' . self::PAGE_TITLES_FILE;
        if ( !is_file( $file ) )
            return null;
        $data = @include $file;
        return is_array( $data ) ? $data : null;
    }

    /**
     * The allowlist entries, from the cache when its stamp is current.
     *
     * @return array
     */
    protected function files()
    {
        if ( $this->files !== null )
            return $this->files;
        $name = 'allowlist-' . $this->allowlist->stamp() . '.php';
        $cached = $this->read( $name );
        if ( is_array( $cached ) && isset( $cached['files'] ) && is_array( $cached['files'] ) )
        {
            $this->sourcesCache = isset( $cached['sources'] ) ? $cached['sources'] : array();
            return $this->files = $cached['files'];
        }
        $files = $this->allowlist->entries();
        $this->sourcesCache = $this->allowlist->usedSources();
        $this->store( $name, array( 'files' => $files, 'sources' => $this->sourcesCache ), 'allowlist-' );
        return $this->files = $files;
    }

    /** @var array the allowlist sources of the entries in use */
    protected $sourcesCache = array();

    /**
     * Builds the index from the files.
     *
     * @return array
     */
    protected function build()
    {
        $documents = array();
        $assets = array();
        $files = $this->files();
        $history = $this->gitHistory ? $this->allowlist->history( self::RECENT_KEPT * 4 ) : array( 'changed' => array(), 'recent' => array() );
        foreach ( $files as $path => $file )
        {
            $id = $this->idOf( $path );
            if ( $id === null )
                continue;
            if ( $file['kind'] === 'asset' )
            {
                $assets[$id] = array( 'id' => $id, 'path' => $path, 'mtime' => $file['mtime'], 'size' => $file['size'] );
                continue;
            }
            $name = $this->allowlist->safePath( $path, $files );
            $text = $name === null ? false : @file_get_contents( $name );
            if ( !is_string( $text ) )
                continue;
            $document = $this->describe( $path, $text, $file );
            $document['id'] = $id;
            if ( isset( $history['changed'][$path] ) )
                $document['changed'] = $history['changed'][$path];
            $documents[$id] = $document;
        }
        $recent = array();
        foreach ( $history['recent'] as $change )
        {
            $id = $this->idOf( $change['path'] );
            if ( $id === null || !isset( $documents[$id] ) )
                continue;
            $recent[] = array( 'id' => $id, 'time' => $change['time'], 'subject' => $change['subject'] );
            if ( count( $recent ) >= self::RECENT_KEPT )
                break;
        }
        return array( 'format' => self::FORMAT, 'signature' => $this->signature(), 'built' => time(),
                      'documents' => $documents, 'assets' => $assets, 'sources' => $this->sourcesCache, 'recent' => $recent );
    }

    /**
     * The index entry of one page (without 'id', which build() adds).
     *
     * @param string $path relative to the installation root
     * @param string $text the whole file
     * @param array $file its allowlist entry (format, mtime, size, root)
     * @return array
     */
    public function describe( $path, $text, array $file = array() )
    {
        $format = isset( $file['format'] ) ? $file['format'] : ( preg_match( '/\.(md|markdown)$/i', $path ) ? 'markdown' : 'text' );
        $text = self::toUtf8( $text );
        list( $meta, $body ) = $format === 'markdown' ? expHelpFrontMatter::split( $text ) : array( array(), $text );
        $headings = $format === 'markdown' ? self::headings( $body ) : array();

        $title = isset( $meta['title'] ) && is_string( $meta['title'] ) && trim( $meta['title'] ) !== '' ? trim( $meta['title'] ) : '';
        if ( $title === '' )
            foreach ( $headings as $heading )
                if ( $heading['level'] === 1 )
                {
                    $title = $heading['text'];
                    break;
                }
        // a plain text list of records (the release and branch history) is titled and summarized by its records
        $records = $format === 'text' && strpos( $body, "\n- " ) !== false ? ( new expHelpRecordRenderer() )->describe( $body ) : null;
        if ( $title === '' && $records !== null )
            $title = $records['title'];
        if ( $title === '' )
            $title = self::fallbackTitle( $path, $body );
        // exphelp.ini [TitleSettings]: a clearer title for a page whose own is unclear (front matter always wins)
        $ownTitle = isset( $meta['title'] ) && is_string( $meta['title'] ) && trim( $meta['title'] ) !== '';
        if ( !$ownTitle )
            $title = $this->audience->pageTitle( $path, $title );

        $summary = isset( $meta['summary'] ) && is_string( $meta['summary'] ) ? trim( $meta['summary'] )
                 : ( isset( $meta['description'] ) && is_string( $meta['description'] ) ? trim( $meta['description'] ) : '' );
        if ( $summary === '' )
            $summary = $this->audience->pageSummary( $path, '' );
        if ( $summary === '' && $records !== null )
            $summary = $records['summary'];
        if ( $summary === '' )
            $summary = self::summary( $body, $format );
        $summary = self::truncate( $summary, self::SUMMARY_LENGTH );

        $class = $this->audience->classify( $path, $meta, $summary !== '' ? $summary : substr( $body, 0, 600 ) );
        $keywords = array();
        foreach ( array( 'keywords', 'tags' ) as $key )
            if ( isset( $meta[$key] ) )
                foreach ( (array)$meta[$key] as $keyword )
                    if ( is_scalar( $keyword ) && ( $keyword = trim( (string)$keyword ) ) !== '' )
                        $keywords[] = $keyword;
        $context = array();
        if ( isset( $meta['context'] ) )
            foreach ( (array)$meta['context'] as $item )
                if ( is_string( $item ) && preg_match( '#^[a-z0-9_]+(/[a-z0-9_]+)?(\#[A-Za-z0-9_-]+)?$#', trim( $item ) ) )
                    $context[] = trim( $item );
        $base = basename( $path );
        $isIndex = (bool)preg_match( '/^(readme|index)\.(md|markdown|txt)$/i', $base );
        $dot = strrpos( $base, '.' );
        $suffix = $dot === false || $dot === 0 ? '' : strtolower( substr( $base, $dot + 1 ) );
        $mtime = isset( $file['mtime'] ) ? (int)$file['mtime'] : 0;

        return array(
            'id'       => $this->idOf( $path ),
            'path'     => $path,
            'suffix'   => $format === 'text' && !in_array( $suffix, array( 'txt', 'rst' ), true ) ? '' : $suffix,
            'format'   => $format,
            'title'    => $title,
            'headings' => $headings,
            'summary'  => $summary,
            'audience' => $class['audience'],
            'category' => $class['category'],
            'type'     => $class['type'],
            'order'    => isset( $meta['order'] ) && is_int( $meta['order'] ) ? $meta['order'] : ( $isIndex ? 0 : 1000 ),
            'keywords' => array_values( array_unique( $keywords ) ),
            'featured' => isset( $meta['featured'] ) && $meta['featured'] === true,
            'hidden'   => isset( $meta['hidden'] ) && $meta['hidden'] === true,
            'context'  => $context,
            'mtime'    => $mtime,
            'changed'  => $mtime,
            'size'     => isset( $file['size'] ) ? (int)$file['size'] : strlen( $text ),
            'root'     => isset( $file['root'] ) ? $file['root'] : strtok( $path, '/' ),
            // the major version most records of a record page (the release history) belong to; search ranks older versions lower
            'version'  => $records !== null ? $records['version'] : '',
        );
    }

    /**
     * The headings of a Markdown body (ATX "# ..." and setext "Title\n===="), outside fenced code blocks, with the
     * anchors the renderer gives them.
     *
     * @param string $body
     * @return array list of array( 'level', 'text', 'anchor' )
     */
    public static function headings( $body )
    {
        $headings = array();
        $used = array();
        $fence = null;
        $previous = '';
        $previousIsText = false;
        foreach ( preg_split( '/\r\n|\r|\n/', (string)$body ) as $line )
        {
            if ( $fence !== null )
            {
                if ( preg_match( '/^ {0,3}(`{3,}|~{3,})\s*$/', $line, $m ) && $m[1][0] === $fence[0] && strlen( $m[1] ) >= strlen( $fence ) )
                    $fence = null;
                $previousIsText = false;
                continue;
            }
            if ( preg_match( '/^ {0,3}(`{3,}|~{3,})/', $line, $m ) )
            {
                $fence = $m[1];
                $previousIsText = false;
                continue;
            }
            if ( preg_match( '/^ {0,3}(#{1,6})(?:[ \t]+(.*?))?(?:[ \t]+#+)?[ \t]*$/', $line, $m ) )
            {
                $text = self::inlineText( isset( $m[2] ) ? $m[2] : '' );
                if ( $text !== '' )
                    $headings[] = array( 'level' => strlen( $m[1] ), 'text' => $text, 'anchor' => self::slug( $text, $used ) );
                $previousIsText = false;
                continue;
            }
            if ( $previousIsText && preg_match( '/^ {0,3}(=+|-+)[ \t]*$/', $line, $m ) )
            {
                $text = self::inlineText( $previous );
                if ( $text !== '' )
                    $headings[] = array( 'level' => $m[1][0] === '=' ? 1 : 2, 'text' => $text, 'anchor' => self::slug( $text, $used ) );
                $previousIsText = false;
                continue;
            }
            // a setext underline only follows a one-line paragraph that is not a list item, quote, table or indented code
            $previousIsText = trim( $line ) !== '' && !preg_match( '/^( {4}|\t| {0,3}([-*+>|]|\d+[.)])(\s|$))/', $line );
            $previous = $line;
        }
        return $headings;
    }

    /**
     * The anchor of a heading: the renderer's (expHelpMarkdownRenderer::slug()) when it is there, else the same rule:
     * inline Markdown and HTML tags removed, lower case, only letters, digits, spaces, "-" and "_", spaces as "-",
     * repeated anchors numbered -1, -2 ...
     *
     * @param string $text
     * @param array $used the anchors given so far in the document
     * @return string
     */
    public static function slug( $text, ?array &$used = null )
    {
        if ( $used === null )
            $used = array();
        if ( self::rendererSlugs() )
            return expHelpMarkdownRenderer::slug( $text, $used );
        $slug = self::inlineText( $text );
        $slug = function_exists( 'mb_strtolower' ) ? mb_strtolower( $slug, 'UTF-8' ) : strtolower( $slug );
        $slug = preg_replace( '/[^\p{L}\p{N}\p{M} _-]+/u', '', $slug );
        // as GitHub: not trimmed, so "with `[Include ...]`" ends in "-" (the links in doc/ are written against GitHub)
        $slug = str_replace( ' ', '-', (string)$slug );
        if ( $slug === '' )
            $slug = 'section';
        if ( $used === null )
            return $slug;
        $unique = $slug;
        for ( $n = 1; isset( $used[$unique] ); ++$n )
            $unique = $slug . '-' . $n;
        $used[$unique] = true;
        return $unique;
    }

    /**
     * Whether the renderer's slug( $text, &$used ) is there (with its second parameter), so that the index, the search
     * and the rendered page give a heading the same anchor.
     *
     * @return bool
     */
    public static function rendererSlugs()
    {
        static $answer = null;
        if ( $answer === null )
        {
            $answer = false;
            if ( class_exists( 'expHelpMarkdownRenderer' ) && method_exists( 'expHelpMarkdownRenderer', 'slug' ) )
            {
                $method = new ReflectionMethod( 'expHelpMarkdownRenderer', 'slug' );
                $answer = $method->isStatic() && $method->getNumberOfParameters() >= 2;
            }
        }
        return $answer;
    }

    /**
     * Plain text of an inline Markdown fragment: code spans keep their text, emphasis marks, link targets, images and
     * HTML tags go, whitespace is collapsed.
     *
     * @param string $text
     * @return string
     */
    public static function inlineText( $text )
    {
        $text = (string)$text;
        $text = preg_replace( '/!\[([^\]]*)\]\([^)]*\)/', '$1', $text );
        $text = preg_replace( '/\[([^\]]*)\]\([^)]*\)/', '$1', $text );
        $text = preg_replace( '/\[([^\]]*)\]\[[^\]]*\]/', '$1', $text );
        $text = preg_replace( '/<[^>]*>/', '', $text );
        $text = preg_replace( '/`+([^`]*)`+/', '$1', $text );
        $text = preg_replace( '/(\*\*|__|~~)(.+?)\1/', '$2', $text );
        $text = preg_replace( '/(?<![\w*])[*_](?!\s)(.+?)(?<!\s)[*_](?![\w*])/u', '$1', $text );
        $text = preg_replace( '/\\\\([\\\\`*_{}\[\]()#+\-.!|>])/', '$1', $text );
        return trim( (string)preg_replace( '/\s+/u', ' ', (string)$text ) );
    }

    /**
     * The title of a page without a heading: the first line of a text file when it is short ("*Title: ..." of the
     * old feature pages without its label; reStructuredText comments such as ".. -*- coding: utf-8 -*-" skipped),
     * else the file name ("README" names its directory).
     *
     * @param string $path
     * @param string $body
     * @return string
     */
    public static function fallbackTitle( $path, $body )
    {
        foreach ( array_slice( preg_split( '/\r\n|\r|\n/', (string)$body ), 0, 20 ) as $line )
        {
            if ( preg_match( '/^\.\.(\s|$)/', $line ) )
                continue;
            $line = trim( $line, " \t=-*#" );
            if ( $line === '' )
                continue;
            if ( preg_match( '/^title:\s*(.+)$/i', $line, $m ) )
                $line = rtrim( trim( $m[1] ), '.' );
            // a label that opens a list ("2.9-3:", "stable/3.0:") names nothing: the file name does
            elseif ( substr( $line, -1 ) === ':' )
                break;
            if ( mb_strlen( $line, 'UTF-8' ) <= 80 && !preg_match( '/^[0-9a-f]{7,}\b|^(\$|<|\{|https?:)/', $line ) )
                return self::inlineText( $line );
            break;
        }
        $base = preg_replace( '/\.(md|markdown|txt|rst)$/i', '', basename( $path ) );
        if ( preg_match( '/^(readme|index)$/i', $base ) )
            return self::humanize( basename( dirname( $path ) ) );
        return self::humanize( $base );
    }

    /**
     * "two-factor_login" -> "Two factor login"; version-like names stay as they are.
     *
     * @param string $name
     * @return string
     */
    public static function humanize( $name )
    {
        if ( preg_match( '/^[0-9.]+$/', $name ) || preg_match( '/^[A-Z0-9_.-]+$/', $name ) && strpos( $name, '-' ) !== false )
            return $name;
        $name = trim( str_replace( array( '-', '_' ), ' ', $name ) );
        return $name === '' ? $name : ucfirst( $name );
    }

    /**
     * The first paragraph of the text after its title, as plain text.
     *
     * @param string $body
     * @param string $format markdown|text
     * @return string
     */
    public static function summary( $body, $format = 'markdown' )
    {
        $paragraph = array();
        $fence = false;
        $seenTitle = $format !== 'markdown';
        $lines = preg_split( '/\r\n|\r|\n/', (string)$body, 400 );
        foreach ( $lines as $i => $line )
        {
            if ( preg_match( '/^ {0,3}(`{3,}|~{3,})/', $line ) )
            {
                $fence = !$fence;
                if ( $paragraph )
                    break;
                continue;
            }
            if ( $fence )
                continue;
            $trimmed = trim( $line );
            // reStructuredText comments and field lists (":Date: ..."), and the bare labels of the old text pages ("*Documentation:")
            if ( preg_match( '/^\.\.(\s|$)/', $line ) || ( $format === 'text' && preg_match( '/^(\*?[A-Za-z][A-Za-z ]{0,30}:$|:[A-Za-z][A-Za-z ]{0,30}:)/', $trimmed ) ) )
            {
                if ( $paragraph )
                    break;
                continue;
            }
            $isHeading = preg_match( '/^ {0,3}#{1,6}(\s|$)/', $line ) || ( isset( $lines[$i + 1] ) && $trimmed !== '' && preg_match( '/^ {0,3}(=+|-+)\s*$/', $lines[$i + 1] ) );
            if ( $trimmed === '' || preg_match( '/^ {0,3}(=+|-+)\s*$/', $line ) )
            {
                if ( $paragraph )
                    break;
                continue;
            }
            if ( $isHeading )
            {
                if ( $paragraph )
                    break;
                $seenTitle = true;
                continue;
            }
            if ( $format === 'text' && !$paragraph && $i === 0 && mb_strlen( $trimmed, 'UTF-8' ) <= 80 )
                continue; // the first line of a text file is its title
            if ( preg_match( '/^( {0,3}[|<]|!\[|\[[^\]]+\]:)/', $line ) )
            {
                if ( $paragraph )
                    break;
                continue;
            }
            $paragraph[] = $trimmed;
            if ( count( $paragraph ) >= 8 )
                break;
        }
        $text = implode( ' ', $paragraph );
        $text = preg_replace( '/^(>\s*|[-*+]\s+|\d+[.)]\s+)/', '', $text );
        return $format === 'markdown' ? self::inlineText( $text ) : trim( (string)preg_replace( '/\s+/u', ' ', $text ) );
    }

    /**
     * Shortens a text at a word boundary, with an ellipsis.
     *
     * @param string $text
     * @param int $length
     * @return string
     */
    public static function truncate( $text, $length )
    {
        if ( mb_strlen( $text, 'UTF-8' ) <= $length )
            return $text;
        $cut = mb_substr( $text, 0, $length, 'UTF-8' );
        $space = mb_strrpos( $cut, ' ', 0, 'UTF-8' );
        if ( $space !== false && $space > $length * 0.6 )
            $cut = mb_substr( $cut, 0, $space, 'UTF-8' );
        return rtrim( $cut, " ,.;:-" ) . "\u{2026}";
    }

    /**
     * The text as UTF-8 (old changelogs are ISO-8859-1).
     *
     * @param string $text
     * @return string
     */
    public static function toUtf8( $text )
    {
        if ( preg_match( '//u', $text ) )
            return $text;
        return function_exists( 'mb_convert_encoding' ) ? (string)mb_convert_encoding( $text, 'UTF-8', 'ISO-8859-1' ) : utf8_encode( $text );
    }

    /**
     * Reads a cache file.
     *
     * @param string $name
     * @return mixed what it holds, or null
     */
    protected function read( $name )
    {
        if ( $this->cacheDir === null )
            return null;
        $file = $this->cacheDir . '/' . $name;
        if ( !is_file( $file ) )
            return null;
        $data = @include $file;
        return is_array( $data ) ? $data : null;
    }

    /**
     * Writes a cache file atomically, readable and writable by the web server and the console alike, and removes
     * the older files of the same kind.
     *
     * @param string $name
     * @param array $data
     * @param string $prefix the kind (index-, allowlist-)
     * @return bool whether it was written
     */
    protected function store( $name, array $data, $prefix )
    {
        $dir = $this->cacheDir();
        if ( $dir === null || !is_dir( $dir ) || !is_writable( $dir ) )
            return false;
        $content = "<?php\n// The help index cache (expHelpDocumentIndex). Generated, safe to remove.\nreturn " . var_export( $data, true ) . ";\n";
        $temporary = $dir . '/.' . $name . '.' . getmypid() . '.' . mt_rand() . '.tmp';
        if ( @file_put_contents( $temporary, $content, LOCK_EX ) === false )
            return false;
        @chmod( $temporary, eZFile::fileMode( self::filePermissions() ) );
        if ( !@rename( $temporary, $dir . '/' . $name ) )
        {
            @unlink( $temporary );
            return false;
        }
        if ( function_exists( 'opcache_invalidate' ) )
            @opcache_invalidate( $dir . '/' . $name, true );
        foreach ( (array)glob( $dir . '/' . $prefix . '*.php' ) as $old )
            if ( $old !== $dir . '/' . $name && is_file( $old ) && filemtime( $old ) < time() - 60 )
                @unlink( $old );
        return true;
    }

    /**
     * site.ini [FileSettings] StorageFilePermissions (0666 by default), so Velocity (root) and PHP-FPM (the site user)
     * can both replace the files.
     *
     * @return int
     */
    protected static function filePermissions()
    {
        if ( class_exists( 'eZINI' ) )
        {
            $value = eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' );
            if ( preg_match( '/^0?[0-7]{3}$/', (string)$value ) )
                return octdec( $value );
        }
        return 0666;
    }

    /**
     * Creates a directory with the storage permissions of site.ini.
     *
     * @param string $dir
     */
    protected static function mkdir( $dir )
    {
        if ( class_exists( 'eZDir' ) )
            eZDir::mkdir( $dir, false, true );
        else
            @mkdir( $dir, eZDir::dirMode( 0777 ), true );
    }
}
