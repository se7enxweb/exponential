<?php
/**
 * Context help: which help page answers "help for this page", for a module, view and design.
 *
 * The map is exphelp.ini [ContextHelp] Map[], one entry per key, merged from the kernel's exphelp.ini and every
 * active extension's settings/exphelp.ini.append.php, so an extension registers the help of its own modules there:
 *
 *   [ContextHelp]
 *   Map[setup/cronjobs]=guides/cronjobs.md
 *   Map[setup/logsdigest]=features/6.0/log-viewer.md#error-digest
 *   Map[role]=guides/roles-and-policies.md
 *   Map[editor:content/history]=help/publishing-and-versions.md
 *   Map[mymodule]=ext/myextension/usage.md          (an active extension's doc/usage.md)
 *
 * A key is "module/view", "module" (every view of the module without an entry of its own), or either with a design
 * before it ("admin4l:module/view", "editor:module"), which wins over the same key without one. The value is a page
 * id (the path below doc/eng-us/, the same in every language; a file name such as doc/eng-us/<id> is accepted too;
 * or ext/<extension>/<path>) and, after "#", the anchor of one of its headings (expHelpMarkdownRenderer::slug() of
 * the heading's text).
 *
 * PHP classes can answer first: [ContextHelp] Providers[] lists classes implementing
 * expHelpContextProviderInterface, asked in order before the map.
 * A provider that also implements expHelpContextPageInterface may answer "content/<help_id>": a help topic
 * maintained as content (doc/eng-us/features/6.0/help-content.md); it gives the address and title of that page itself.
 *
 * The lookup goes from the most to the least specific: the providers, design:module/view, module/view,
 * design:module, module, then the help dashboard. An entry is skipped (and the next key tried) when its value is not
 * a safe page id (absolute, "..", hidden or empty segments, a scheme, not a page suffix, doxygen output) or, with
 * [ContextHelp] CheckPages=enabled (the default), when the document index does not serve that page (not written
 * yet, not git-tracked, excluded). So a wrong or early entry never leads to a 404.
 *
 * lookup() result: array( 'id' => page id|'', 'anchor' => string, 'url' => module-relative URL, 'title' => string,
 *                         'level' => view|module|dashboard, 'entry' => the key that matched|'', 'source' =>
 *                         provider class|'map'|'', 'key' => a short string for template cache-block keys: the page
 *                         and the module )
 *
 * menu() gives the header's Help menu its related topics and quick links ([ContextHelp] MenuTopics, MenuLinks[],
 * [FeaturedSettings] PageList[]).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContextRegistry
{
    /** @var expHelpContextRegistry|null */
    protected static $instance = null;

    /** @var array key => array( 'id' => ..., 'anchor' => ... ) */
    protected $entries = array();

    /** @var array key => the raw value, for map() */
    protected $raw = array();

    /** @var expHelpContextProviderInterface[] */
    protected $providers = array();

    /** @var callable|null id => array( 'title' => ... ) or null when the page is not served */
    protected $pageInfo = null;

    /** @var array lookup() results of this request */
    protected $memo = array();

    /** @var array the Help menu's settings: featured (page ids), links (key => target), topics (int) */
    protected $menu = array( 'featured' => array(), 'links' => array(), 'topics' => 6 );

    /** @var callable|false|null module => page ids the transfer registry adds to its Help menu; false: none; null: the registry's */
    protected $transferTopics = null;

    /** The page suffixes an entry may name (the index decides the rest) */
    const PAGE_PATTERN = '#\.(md|markdown|txt)$#i';

    /** The id of a help topic maintained as content, answered by an expHelpContextPageInterface provider */
    const CONTENT_PAGE_PATTERN = '#^content/[a-z0-9.-]{1,80}$#';

    /**
     * @param array $map key => "page id[#anchor]"
     * @param array $providers expHelpContextProviderInterface objects or class names
     * @param callable|null $pageInfo function( $id ): array|null, the page's metadata (title) or null when no such
     *                                page is served; null = every syntactically safe page is taken as it is
     * @param array $menu the header's Help menu: 'featured' => page ids ([FeaturedSettings] PageList[]), 'links' =>
     *                    key => page id[#anchor] or an exphelp view ("exphelp/report") ([ContextHelp] MenuLinks[]),
     *                    'topics' => how many related topics it lists ([ContextHelp] MenuTopics)
     */
    public function __construct( array $map = array(), array $providers = array(), $pageInfo = null, array $menu = array() )
    {
        if ( isset( $menu['featured'] ) && is_array( $menu['featured'] ) )
            $this->menu['featured'] = array_values( array_filter( $menu['featured'], 'is_string' ) );
        if ( isset( $menu['links'] ) && is_array( $menu['links'] ) )
            $this->menu['links'] = $menu['links'];
        if ( isset( $menu['topics'] ) && is_numeric( $menu['topics'] ) )
            $this->menu['topics'] = max( 0, min( 12, (int)$menu['topics'] ) );
        foreach ( $map as $key => $value )
        {
            $key = self::normalizeKey( $key );
            if ( $key === '' || !is_scalar( $value ) )
                continue;
            $target = self::parseTarget( (string)$value );
            if ( $target === null )
            {
                if ( class_exists( 'eZDebug', false ) )
                    eZDebug::writeWarning( "exphelp.ini [ContextHelp] Map[$key]: not a page of the help: " . $value, __METHOD__ );
                continue;
            }
            $this->entries[$key] = $target;
            $this->raw[$key] = (string)$value;
        }
        foreach ( $providers as $provider )
        {
            if ( is_string( $provider ) && $provider !== '' && class_exists( $provider ) )
                $provider = new $provider();
            if ( $provider instanceof expHelpContextProviderInterface )
                $this->providers[] = $provider;
        }
        $this->pageInfo = is_callable( $pageInfo ) ? $pageInfo : null;
        if ( isset( $menu['transfer'] ) && ( is_callable( $menu['transfer'] ) || $menu['transfer'] === false ) )
            $this->transferTopics = $menu['transfer'];
    }

    /**
     * The help topics the transfer registry adds to a module's Help menu (expKernelTransferRegistry::helpTopicsForModule()):
     * nothing for a module that is no import or export tool's.
     *
     * @param string $module
     * @return array page ids
     */
    protected function transferTopics( $module )
    {
        if ( $this->transferTopics === false )
            return array();
        if ( is_callable( $this->transferTopics ) )
            return (array)call_user_func( $this->transferTopics, $module );
        // a Velocity worker started before the registry existed has no such class: no topics, no error
        if ( !class_exists( 'expKernelTransferRegistry' ) )
            return array();
        return expKernelTransferRegistry::instance()->helpTopicsForModule( $module );
    }

    /**
     * The registry of the current siteaccess, from exphelp.ini [ContextHelp]
     *
     * @return expHelpContextRegistry
     */
    public static function instance()
    {
        if ( self::$instance === null )
            self::$instance = self::fromSettings( expHelp::ini() );
        return self::$instance;
    }

    /**
     * Forgets instance() (tests, a worker after the settings changed)
     */
    public static function resetInstance()
    {
        self::$instance = null;
    }

    /**
     * @param eZINI $ini exphelp.ini
     * @return expHelpContextRegistry
     */
    public static function fromSettings( $ini )
    {
        $read = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'ContextHelp', $name ) ? $ini->variable( 'ContextHelp', $name ) : $default;
        };
        $map = $read( 'Map', array() );
        $providers = $read( 'Providers', array() );
        $pageInfo = null;
        if ( $read( 'CheckPages', 'enabled' ) !== 'disabled' )
        {
            // Every admin page asks this for its Help link: it reads the small list of served pages the last index
            // load wrote (expHelpDocumentIndex::pageTitles(), one include), never the index itself (seconds).
            // No list yet: nothing is known, the page is taken as it is.
            $titles = false;
            $pageInfo = function ( $id ) use ( &$titles ) {
                try
                {
                    if ( $titles === false )
                        $titles = expHelpDocumentIndex::pageTitles();
                    if ( $titles === null )
                        return array();
                    $id = expHelpAllowlist::normalize( $id );
                    return $id !== null && isset( $titles[$id] ) ? array( 'title' => $titles[$id] ) : null;
                }
                catch ( Exception $e )
                {
                    eZDebug::writeWarning( 'The help index is not available: ' . $e->getMessage(), __METHOD__ );
                    return null;
                }
            };
        }
        $featured = $ini->hasVariable( 'FeaturedSettings', 'PageList' ) ? $ini->variable( 'FeaturedSettings', 'PageList' ) : array();
        $links = $read( 'MenuLinks', array() );
        return new self( is_array( $map ) ? $map : array(),
                         is_array( $providers ) ? array_values( array_filter( $providers, 'strlen' ) ) : array(),
                         $pageInfo,
                         array( 'featured' => is_array( $featured ) ? $featured : array(),
                                'links' => is_array( $links ) ? $links : array(),
                                'topics' => $read( 'MenuTopics', 6 ) ) );
    }

    /**
     * The help page of a module, view and design (see the class description)
     *
     * @param string $module
     * @param string $view
     * @param string $design the siteaccess's design (admin4, admin4l, admin, editor, adminui ...), '' for none
     * @return array
     */
    public function lookup( $module, $view = '', $design = '' )
    {
        $module = self::normalizePart( $module );
        $view = $module === '' ? '' : self::normalizePart( $view );
        $design = self::normalizePart( $design );
        $memoKey = $module . '/' . $view . '/' . $design;
        if ( isset( $this->memo[$memoKey] ) )
            return $this->memo[$memoKey];

        $result = null;
        if ( $module !== '' )
        {
            foreach ( $this->providers as $provider )
            {
                $answer = $provider->helpContext( $module, $view, $design );
                if ( !is_string( $answer ) || $answer === '' )
                    continue;
                // a help topic maintained as content ("content/<help_id>"): only a provider that also serves such
                // pages (expHelpContextPageInterface) may answer with one, and it says the page's address and title
                if ( preg_match( self::CONTENT_PAGE_PATTERN, $answer ) )
                {
                    $page = $provider instanceof expHelpContextPageInterface ? $provider->contextPage( $answer ) : null;
                    if ( is_array( $page ) && isset( $page['url'] ) && is_string( $page['url'] ) && strpos( $page['url'], '/exphelp/' ) === 0 )
                    {
                        $title = isset( $page['title'] ) && is_scalar( $page['title'] ) ? (string)$page['title'] : '';
                        $result = array( 'id' => $answer, 'anchor' => '', 'url' => $page['url'], 'title' => $title,
                                         'level' => $view !== '' ? 'view' : 'module', 'entry' => '', 'source' => get_class( $provider ),
                                         'key' => $answer . '#' );
                        break;
                    }
                    continue;
                }
                $target = self::parseTarget( $answer );
                $info = $target ? $this->info( $target['id'] ) : null;
                if ( $info !== null )
                {
                    $result = $this->result( $target, $info, $view !== '' ? 'view' : 'module', '', get_class( $provider ) );
                    break;
                }
            }
        }
        if ( $result === null )
        {
            foreach ( self::candidates( $module, $view, $design ) as $candidate )
            {
                list( $key, $level ) = $candidate;
                if ( !isset( $this->entries[$key] ) )
                    continue;
                $info = $this->info( $this->entries[$key]['id'] );
                if ( $info !== null )
                {
                    $result = $this->result( $this->entries[$key], $info, $level, $key, 'map' );
                    break;
                }
            }
        }
        if ( $result === null )
        {
            $result = array( 'id' => '', 'anchor' => '', 'url' => expHelp::url( 'dashboard' ), 'title' => '',
                             'level' => 'dashboard', 'entry' => '', 'source' => '', 'key' => 'dashboard' );
        }
        // the key of the template cache-blocks that draw the link and the Help menu: the page, and the module, whose
        // related topics the menu lists
        $result['key'] .= '|' . $module;
        return $this->memo[$memoKey] = $result;
    }

    /**
     * The header's Help menu for a module, view and design: the topics related to the page and the quick links.
     * Only pages the help serves are listed; it reads the small list of served pages, never the index.
     *
     * Topics, at most [ContextHelp] MenuTopics, without the page lookup() gives (the menu's first entry) and
     * without repeats: the other entries of the same module (design-specific ones first, in the map's order), the
     * module's own entry, then the featured pages ([FeaturedSettings] PageList[]).
     * Links: [ContextHelp] MenuLinks[], key => a page id[#anchor] or an exphelp view (exphelp/report).
     *
     * @param string $module
     * @param string $view
     * @param string $design
     * @return array array( 'topics' => list of array( id, anchor, url, title ), 'links' => list of array( key, id,
     *               anchor, url, title ) )
     */
    public function menu( $module, $view = '', $design = '' )
    {
        $module = self::normalizePart( $module );
        $design = self::normalizePart( $design );
        $current = $this->lookup( $module, $view, $design );
        $seen = array();
        if ( $current['id'] !== '' )
            $seen[$current['id']] = true;
        $topics = array();
        $add = function ( array $target ) use ( &$topics, &$seen ) {
            if ( isset( $seen[$target['id']] ) || count( $topics ) >= $this->menu['topics'] )
                return;
            $info = $this->info( $target['id'] );
            if ( $info === null )
                return;
            $seen[$target['id']] = true;
            $topics[] = $this->entry( $target, $info );
        };
        if ( $module !== '' )
        {
            $own = array();
            $general = array();
            foreach ( $this->entries as $key => $target )
            {
                $design_ = '';
                $rest = $key;
                if ( strpos( $key, ':' ) !== false )
                    list( $design_, $rest ) = explode( ':', $key, 2 );
                if ( $design_ !== '' && $design_ !== $design )
                    continue;
                $parts = explode( '/', $rest, 2 );
                if ( $parts[0] !== $module || !isset( $parts[1] ) )
                    continue;
                if ( $design_ !== '' )
                    $own[] = $target;
                else
                    $general[] = $target;
            }
            foreach ( array_merge( $own, $general ) as $target )
                $add( $target );
            foreach ( array( $design !== '' ? $design . ':' . $module : null, $module ) as $key )
            {
                if ( $key !== null && isset( $this->entries[$key] ) )
                    $add( $this->entries[$key] );
            }
            // the page of an import or export tool: "Which import or export tool do I need?" and the tool's own
            // help (the transfer registry, doc/eng-us/features/6.0/transfer-registry.md)
            foreach ( $this->transferTopics( $module ) as $id )
            {
                $target = self::parseTarget( $id );
                if ( $target !== null )
                    $add( $target );
            }
        }
        foreach ( $this->menu['featured'] as $id )
        {
            $target = self::parseTarget( $id );
            if ( $target !== null )
                $add( $target );
        }

        $links = array();
        foreach ( $this->menu['links'] as $key => $value )
        {
            $key = strtolower( trim( (string)$key ) );
            if ( !preg_match( '#^[a-z0-9_]+$#', $key ) || !is_scalar( $value ) )
                continue;
            $value = trim( (string)$value );
            if ( preg_match( '#^exphelp/(dashboard|report|search)$#', $value, $m ) )
            {
                $links[] = array( 'key' => $key, 'id' => '', 'anchor' => '', 'url' => expHelp::url( $m[1] ), 'title' => '' );
                continue;
            }
            $target = self::parseTarget( $value );
            $info = $target === null ? null : $this->info( $target['id'] );
            if ( $info === null )
                continue;
            $links[] = array( 'key' => $key ) + $this->entry( $target, $info );
        }
        return array( 'topics' => $topics, 'links' => $links );
    }

    /**
     * The map as it is read: key => "page id#anchor" (only entries whose value is a safe page id)
     *
     * @return array
     */
    public function map()
    {
        return $this->raw;
    }

    /**
     * The map parsed: key => array( 'id' => ..., 'anchor' => ... ) (exp:help check, the tests)
     *
     * @return array
     */
    public function entries()
    {
        return $this->entries;
    }

    /**
     * The keys lookup() tries, most specific first, each with its level
     *
     * @param string $module
     * @param string $view
     * @param string $design
     * @return array of array( key, level )
     */
    public static function candidates( $module, $view, $design )
    {
        $candidates = array();
        if ( $module === '' )
            return $candidates;
        if ( $view !== '' )
        {
            if ( $design !== '' )
                $candidates[] = array( $design . ':' . $module . '/' . $view, 'view' );
            $candidates[] = array( $module . '/' . $view, 'view' );
        }
        if ( $design !== '' )
            $candidates[] = array( $design . ':' . $module, 'module' );
        $candidates[] = array( $module, 'module' );
        return $candidates;
    }

    /**
     * Splits "id#anchor" and checks the id. Null when it is not a safe page id: absolute or with a scheme, "." or
     * ".." or hidden or empty segments, a backslash or other characters outside [A-Za-z0-9_.-/], not a page suffix,
     * or doxygen output. A file name (doc/<id>, doc/eng-us/<id>, doc/<language>/<translated path>) becomes its id.
     * The anchor keeps letters, digits, "-" and "_"; anything else drops the anchor (the page still opens).
     *
     * @param string $value
     * @return array|null array( 'id' => ..., 'anchor' => ... )
     */
    public static function parseTarget( $value )
    {
        $value = trim( (string)$value );
        $anchor = '';
        $hash = strpos( $value, '#' );
        if ( $hash !== false )
        {
            $anchor = substr( $value, $hash + 1 );
            $value = substr( $value, 0, $hash );
        }
        // a file name instead of an id: doc/<id>, doc/eng-us/<id> or a translation's doc/ger-de/... (its id)
        if ( strpos( $value, 'doc/' ) === 0 )
            $value = class_exists( 'expHelpLanguage' ) ? expHelpLanguage::fromIni()->idOfValue( $value ) : substr( $value, 4 );
        if ( $value === '' || !preg_match( '#^[A-Za-z0-9_.][A-Za-z0-9_./-]*$#', $value ) || !preg_match( self::PAGE_PATTERN, $value ) )
            return null;
        foreach ( explode( '/', $value ) as $segment )
        {
            if ( $segment === '' || $segment[0] === '.' )
                return null;
        }
        if ( strpos( $value, 'doxygen/' ) === 0 )
            return null;
        if ( strpos( $value, 'ext/' ) === 0 && substr_count( $value, '/' ) < 2 )
            return null;
        if ( $anchor !== '' && !preg_match( '#^[\p{L}\p{N}_-]{1,200}$#u', $anchor ) )
            $anchor = '';
        return array( 'id' => $value, 'anchor' => $anchor );
    }

    /**
     * A map key in lower case, '' when it is not "[design:]module[/view]" of [a-z0-9_]
     *
     * @param string $key
     * @return string
     */
    public static function normalizeKey( $key )
    {
        $key = strtolower( trim( (string)$key ) );
        return preg_match( '#^([a-z0-9_]+:)?[a-z0-9_]+(/[a-z0-9_]+)?$#', $key ) ? $key : '';
    }

    /**
     * @param string $part a module, view or design name
     * @return string in lower case, '' when it is not [a-z0-9_]
     */
    protected static function normalizePart( $part )
    {
        $part = strtolower( trim( (string)$part ) );
        return preg_match( '#^[a-z0-9_]+$#', $part ) ? $part : '';
    }

    /**
     * The page's metadata, null when it is not served
     *
     * @param string $id
     * @return array|null
     */
    protected function info( $id )
    {
        if ( $this->pageInfo === null )
            return array();
        $info = call_user_func( $this->pageInfo, $id );
        return is_array( $info ) ? $info : null;
    }

    /**
     * A menu entry: id, anchor, url, title (the page's title, else its file name made readable)
     */
    protected function entry( array $target, array $info )
    {
        $title = isset( $info['title'] ) && is_scalar( $info['title'] ) ? trim( (string)$info['title'] ) : '';
        if ( $title === '' )
            $title = ucfirst( str_replace( array( '-', '_' ), ' ', preg_replace( '#\.[a-z]+$#i', '', basename( $target['id'] ) ) ) );
        return array( 'id' => $target['id'], 'anchor' => $target['anchor'],
                      'url' => expHelp::url( 'viewer', $target['id'], $target['anchor'] ), 'title' => $title );
    }

    protected function result( array $target, array $info, $level, $entry, $source )
    {
        return array(
            'id' => $target['id'],
            'anchor' => $target['anchor'],
            'url' => expHelp::url( 'viewer', $target['id'], $target['anchor'] ),
            'title' => isset( $info['title'] ) && is_scalar( $info['title'] ) ? (string)$info['title'] : '',
            'level' => $level,
            'entry' => $entry,
            'source' => $source,
            'key' => $target['id'] . '#' . $target['anchor'],
        );
    }
}

?>
