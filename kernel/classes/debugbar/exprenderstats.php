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

    /** Cache blocks noted at most; a block in a loop of a long list runs once per item */
    const MAX_CACHE_BLOCKS = 200;

    /** @var array list of array( 'module', 'view', 'script', 'class' ) in the order they ran */
    protected static $views = array();

    /** @var array|null what the content view cache did, see viewCache() */
    protected static $viewCache = null;

    /** @var array 'compiled' => templates compiled now, 'executed' => templates run from the compiled cache */
    protected static $templates = array( 'compiled' => array(), 'executed' => 0 );

    /** @var array list of cache blocks, see cacheBlocks() */
    protected static $cacheBlocks = array();

    /** @var array 'count', 'hits', 'lookup_ms', 'generate_ms' over every block, also those past MAX_CACHE_BLOCKS */
    protected static $cacheBlockTotals = array( 'count' => 0, 'hits' => 0, 'lookup_ms' => 0.0, 'generate_ms' => 0.0 );

    /** @var array handler id => index in $cacheBlocks of a block that is being generated */
    protected static $pendingBlocks = array();

    /** @var int blocks of the uncompiled path being generated; with $pendingBlocks they tell an outermost block */
    protected static $generating = 0;

    /** A new request: nothing noted yet. */
    public static function resetRequest()
    {
        self::$views = array();
        self::$viewCache = null;
        self::$templates = array( 'compiled' => array(), 'executed' => 0 );
        self::$cacheBlocks = array();
        self::$cacheBlockTotals = array( 'count' => 0, 'hits' => 0, 'lookup_ms' => 0.0, 'generate_ms' => 0.0 );
        self::$pendingBlocks = array();
        self::$generating = 0;
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

    /**
     * A cache-block was looked up (eZTemplateCacheBlock::handle()).
     *
     * @param int $handlerId spl_object_id() of the cache handler, which store() gets again after a miss
     * @param string $path the cache file
     * @param string|false $placement eZTemplateCacheBlock::placementString(): lines, columns and template; false from
     *                                a template compiled before the debug bar named the blocks
     * @param mixed $keys the block's keys, null without
     * @param int|false $nodeID the subtree_expiry node
     * @param int|null $ttl seconds, -1 or null without
     * @param bool $hit
     * @param float $lookupMs
     * @param float|null $generateMs when the block was generated already (the uncompiled path)
     * @param int|null $size bytes of the content read or generated
     */
    public static function noteCacheBlock( $handlerId, $path, $placement, $keys, $nodeID, $ttl, $hit, $lookupMs, $generateMs = null, $size = null )
    {
        if ( !self::enabled() )
            return;
        // a handler id is reused once its object is gone: an entry left by a block whose store never ran is stale
        unset( self::$pendingBlocks[(int)$handlerId] );
        self::$cacheBlockTotals['count']++;
        self::$cacheBlockTotals['lookup_ms'] += (float)$lookupMs;
        if ( $hit )
            self::$cacheBlockTotals['hits']++;
        // the total counts the outermost blocks only: an outer block's time holds those inside it
        if ( $generateMs !== null && self::$generating === 0 && !self::$pendingBlocks )
            self::$cacheBlockTotals['generate_ms'] += (float)$generateMs;
        if ( count( self::$cacheBlocks ) >= self::MAX_CACHE_BLOCKS )
            return;
        $template = null;
        $line = null;
        if ( is_string( $placement ) && $placement !== '' )
        {
            // start line _ start column _ end line _ end column _ template (which may itself hold underscores)
            $parts = explode( '_', $placement, 5 );
            if ( count( $parts ) === 5 )
            {
                $line = (int)$parts[0];
                $template = $parts[4];
            }
        }
        self::$cacheBlocks[] = array( 'template' => $template, 'line' => $line, 'path' => (string)$path, 'keys' => self::keysText( $keys ),
                                      'node_id' => $nodeID === false || $nodeID === null ? null : (int)$nodeID,
                                      'ttl' => $ttl === null || (int)$ttl <= 0 ? null : (int)$ttl,
                                      'state' => $hit ? 'hit' : ( $generateMs !== null ? 'generated' : 'pending' ),
                                      'lookup_ms' => (float)$lookupMs, 'generate_ms' => $generateMs === null ? null : (float)$generateMs,
                                      'size' => $size === null ? null : (int)$size, 'started' => microtime( true ) );
        if ( !$hit && $generateMs === null )
            self::$pendingBlocks[(int)$handlerId] = count( self::$cacheBlocks ) - 1;
    }

    /**
     * A cache-block that was not found was generated and stored (eZTemplateCacheBlock::store()).
     *
     * @param int $handlerId
     * @param int $bytes
     */
    public static function noteCacheBlockStored( $handlerId, $bytes )
    {
        if ( !self::enabled() || !isset( self::$pendingBlocks[(int)$handlerId] ) )
            return;
        $i = self::$pendingBlocks[(int)$handlerId];
        unset( self::$pendingBlocks[(int)$handlerId] );
        $ms = ( microtime( true ) - self::$cacheBlocks[$i]['started'] ) * 1000;
        self::$cacheBlocks[$i]['state'] = 'generated';
        self::$cacheBlocks[$i]['generate_ms'] = $ms;
        self::$cacheBlocks[$i]['size'] = (int)$bytes;
        if ( self::$generating === 0 && !self::$pendingBlocks )
            self::$cacheBlockTotals['generate_ms'] += $ms;
    }

    /** The uncompiled path starts generating a block (eZTemplateCacheFunction::generateProcessedContent()). */
    public static function cacheBlockGenerationStarted()
    {
        if ( self::enabled() )
            self::$generating++;
    }

    /** ... and has generated it. */
    public static function cacheBlockGenerationEnded()
    {
        if ( self::enabled() && self::$generating > 0 )
            self::$generating--;
    }

    /** The keys of a block as text: scalars as they are, arrays as JSON; objects by their class only */
    protected static function keysText( $keys )
    {
        if ( $keys === null )
            return null;
        if ( is_scalar( $keys ) )
            return (string)$keys;
        $plain = function ( $value ) use ( &$plain )
        {
            if ( is_array( $value ) )
                return array_map( $plain, $value );
            return is_object( $value ) ? '(' . get_class( $value ) . ')' : $value;
        };
        $text = json_encode( $plain( $keys ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR );
        return $text === false ? '' : $text;
    }

    /**
     * @return array list of template, line, path, keys (text or null), node_id, ttl, state (hit, generated, pending:
     *               not found and not stored, as an old compiled template does not report it), lookup_ms,
     *               generate_ms (inclusive of nested blocks), size
     */
    public static function cacheBlocks()
    {
        $blocks = self::$cacheBlocks;
        foreach ( $blocks as &$block )
            unset( $block['started'] );
        return $blocks;
    }

    /** @return array count, hits, lookup_ms, generate_ms (of the outermost blocks) over every block of the request */
    public static function cacheBlockTotals()
    {
        return self::$cacheBlockTotals;
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
