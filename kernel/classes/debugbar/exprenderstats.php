<?php
/**
 * File containing the expRenderStats class: how the page was rendered, for the Rendering tab of the Exp Debug bar.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Collects, for the request being served, what the debug bar's Rendering tab shows: the controller (module, view,
 * script and view class), what the content view cache did and with which key, and how many templates came from the
 * compiled cache or were compiled now.
 *
 * The kernel notes these where it decides them (eZModule::run(), ModuleView::main(), the content/view view,
 * eZNodeviewfunctions, eZTemplateCompiler). Every note returns at once without debug output, so a site without
 * the bar pays one static call. ezpKernelWeb starts each request with resetRequest(), also in a persistent worker.
 */
class expRenderStats
{
    /** Views noted at most: an error page or a forward runs a few, a loop must not grow the list without end */
    const MAX_VIEWS = 20;

    /** @var array list of array( 'module', 'view', 'script', 'class' ) in the order they ran */
    protected static $views = array();

    /** @var array|null what the content view cache did, see viewCache() */
    protected static $viewCache = null;

    /** @var array 'compiled' => templates compiled now, 'executed' => templates run from the compiled cache */
    protected static $templates = array( 'compiled' => array(), 'executed' => 0 );

    /** A new request: nothing noted yet. */
    public static function resetRequest()
    {
        self::$views = array();
        self::$viewCache = null;
        self::$templates = array( 'compiled' => array(), 'executed' => 0 );
    }

    protected static function enabled()
    {
        // what eZDebug::isDebugEnabled() reads, without loading eZDebug
        return !empty( $GLOBALS['eZDebugEnabled'] );
    }

    /**
     * A module view starts (eZModule::run()).
     *
     * @param string $module
     * @param string $view
     * @param string $script the view's file
     */
    public static function noteModuleView( $module, $view, $script )
    {
        if ( !self::enabled() || count( self::$views ) >= self::MAX_VIEWS )
            return;
        self::$views[] = array( 'module' => (string)$module, 'view' => (string)$view, 'script' => (string)$script, 'class' => null );
    }

    /**
     * The view of the last noted module view runs as a class (ModuleView::main()).
     *
     * @param string $class
     * @param string $script the view's entry file
     */
    public static function noteViewClass( $class, $script )
    {
        if ( !self::enabled() )
            return;
        for ( $i = count( self::$views ) - 1; $i >= 0; $i-- )
        {
            if ( self::$views[$i]['class'] === null && realpath( self::$views[$i]['script'] ) === realpath( $script ) )
            {
                self::$views[$i]['class'] = (string)$class;
                return;
            }
        }
    }

    /**
     * The content view cache is not used for this view, and why.
     *
     * @param string $reason
     */
    public static function noteViewCacheOff( $reason )
    {
        if ( !self::enabled() )
            return;
        self::$viewCache = array( 'state' => 'off', 'reason' => (string)$reason ) + self::viewCacheDefaults();
    }

    /**
     * The key of the content view cache (eZNodeviewfunctions::generateViewCacheFile()).
     *
     * @param array $parts name => value, in the order they are hashed
     * @param array $fromListeners names a content/view/cachekeys listener added
     * @param string $hash
     * @param string $path the cache file
     */
    public static function noteViewCacheKey( array $parts, array $fromListeners, $hash, $path )
    {
        if ( !self::enabled() )
            return;
        $current = is_array( self::$viewCache ) ? self::$viewCache : array( 'state' => 'pending', 'reason' => '' ) + self::viewCacheDefaults();
        self::$viewCache = array_merge( $current, array( 'parts' => $parts, 'from_listeners' => array_values( $fromListeners ),
                                                        'hash' => (string)$hash, 'path' => (string)$path ) );
    }

    /**
     * The cache file was read and used (eZNodeviewfunctions::contentViewRetrieve()).
     *
     * @param string|null $file null when the cache is not a file (Valkey)
     * @param int|null $mtime
     */
    public static function noteViewCacheHit( $file = null, $mtime = null )
    {
        if ( !self::enabled() )
            return;
        self::viewCacheSet( array( 'state' => 'hit', 'reason' => '', 'age' => $mtime === null ? null : max( 0, time() - (int)$mtime ),
                                   'size' => $file !== null && is_file( $file ) ? (int)filesize( $file ) : null ) );
    }

    /**
     * The cache file was there but not used (expired, no_cache, unexpected content).
     *
     * @param string $reason
     */
    public static function noteViewCacheExpired( $reason )
    {
        if ( !self::enabled() )
            return;
        self::viewCacheSet( array( 'state' => 'expired', 'reason' => (string)$reason ) );
    }

    /**
     * The view was generated (eZNodeviewfunctions::contentViewGenerate()): for the cache when $file is given, else
     * without it.
     *
     * @param string|false $file
     */
    public static function noteViewGenerated( $file )
    {
        if ( !self::enabled() )
            return;
        if ( is_array( self::$viewCache ) && self::$viewCache['state'] === 'off' )
            return;
        $reason = is_array( self::$viewCache ) && self::$viewCache['state'] === 'expired' ? self::$viewCache['reason'] : '';
        self::viewCacheSet( array( 'state' => $file === false ? 'uncached' : 'generated', 'reason' => $reason ) );
    }

    protected static function viewCacheSet( array $values )
    {
        $current = is_array( self::$viewCache ) ? self::$viewCache : self::viewCacheDefaults();
        self::$viewCache = array_merge( $current, $values );
    }

    protected static function viewCacheDefaults()
    {
        return array( 'state' => null, 'reason' => '', 'age' => null, 'size' => null, 'parts' => array(), 'from_listeners' => array(),
                      'hash' => null, 'path' => null );
    }

    /**
     * A template was compiled in this request (eZTemplateCompiler::compileTemplate()).
     *
     * @param string $file the template
     */
    public static function noteTemplateCompiled( $file )
    {
        if ( !self::enabled() || count( self::$templates['compiled'] ) >= 500 )
            return;
        self::$templates['compiled'][] = (string)$file;
    }

    /** A template ran from the compiled cache (eZTemplateCompiler::executeCompilation()). */
    public static function noteTemplateExecuted()
    {
        if ( !self::enabled() )
            return;
        self::$templates['executed']++;
    }

    /** @return array list of array( 'module', 'view', 'script', 'class' ) */
    public static function views()
    {
        return self::$views;
    }

    /**
     * @return array|null state (hit, generated, uncached, expired, off, pending), reason, age (s), size (bytes),
     *                    parts, from_listeners, hash, path; null when no content view ran
     */
    public static function viewCache()
    {
        return self::$viewCache;
    }

    /** @return array 'compiled' => list of files, 'executed' => count */
    public static function templates()
    {
        return self::$templates;
    }
}
