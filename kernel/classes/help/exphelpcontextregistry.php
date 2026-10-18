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
 *   Map[mymodule]=ext/myextension/usage.md          (extension docs: [IndexSettings] ExtensionDocDirs[])
 *
 * A key is "module/view", "module" (every view of the module without an entry of its own), or either with a design
 * before it ("admin4l:module/view", "editor:module"), which wins over the same key without one. The value is a page
 * id (the path below doc/, "doc/" in front is accepted too, or ext/<extension>/<path>) and, after "#", the anchor of
 * one of its headings (expHelpMarkdownRenderer::slug() of the heading's text).
 *
 * PHP classes can answer first: [ContextHelp] Providers[] lists classes implementing
 * expHelpContextProviderInterface, asked in order before the map.
 *
 * The lookup goes from the most to the least specific: the providers, design:module/view, module/view,
 * design:module, module, then the help dashboard. An entry is skipped (and the next key tried) when its value is not
 * a safe page id (absolute, "..", hidden or empty segments, a scheme, not a page suffix, doxygen output) or, with
 * [ContextHelp] CheckPages=enabled (the default), when the document index does not serve that page (not written
 * yet, not git-tracked, excluded). So a wrong or early entry never leads to a 404.
 *
 * lookup() result: array( 'id' => page id|'', 'anchor' => string, 'url' => module-relative URL, 'title' => string,
 *                         'level' => view|module|dashboard, 'entry' => the key that matched|'', 'source' =>
 *                         provider class|'map'|'', 'key' => a short string for template cache-block keys )
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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

    /** The page suffixes an entry may name (the index decides the rest) */
    const PAGE_PATTERN = '#\.(md|markdown|txt)$#i';

    /**
     * @param array $map key => "page id[#anchor]"
     * @param array $providers expHelpContextProviderInterface objects or class names
     * @param callable|null $pageInfo function( $id ): array|null, the page's metadata (title) or null when no such
     *                                page is served; null = every syntactically safe page is taken as it is
     */
    public function __construct( array $map = array(), array $providers = array(), $pageInfo = null )
    {
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
        return new self( is_array( $map ) ? $map : array(),
                         is_array( $providers ) ? array_values( array_filter( $providers, 'strlen' ) ) : array(),
                         $pageInfo );
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
        return $this->memo[$memoKey] = $result;
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
     * or doxygen output. "doc/" in front is taken away. The anchor keeps letters, digits, "-" and "_"; anything else
     * drops the anchor (the page still opens).
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
        if ( strpos( $value, 'doc/' ) === 0 )
            $value = substr( $value, 4 );
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
