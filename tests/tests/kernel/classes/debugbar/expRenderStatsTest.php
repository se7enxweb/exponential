<?php
/**
 * The collector of the debug bar's Rendering tab (kernel/classes/debugbar/exprenderstats.php) and the Rendering tab
 * itself (expDebugBarReport::renderingHtml(), renderingChips()):
 *   - nothing is noted without debug output, and resetRequest() starts a request empty
 *   - the module views in their order, the view class matched to its script, at most MAX_VIEWS of them
 *   - the content view cache: off with its reason, hit with age and size, expired then generated, uncached
 *   - eZNodeviewfunctions::contentViewRetrieve() notes a hit and a no_cache file
 *   - the tab: values escaped, long values cut with the whole value as tooltip, keys of listeners marked, the chips
 *
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRenderStatsTest extends PHPUnit\Framework\TestCase
{
    private $debugEnabled;

    private $files = array();

    protected function setUp(): void
    {
        $this->debugEnabled = isset( $GLOBALS['eZDebugEnabled'] ) ? $GLOBALS['eZDebugEnabled'] : null;
        $GLOBALS['eZDebugEnabled'] = true;
        expRenderStats::resetRequest();
    }

    protected function tearDown(): void
    {
        expRenderStats::resetRequest();
        if ( $this->debugEnabled === null )
            unset( $GLOBALS['eZDebugEnabled'] );
        else
            $GLOBALS['eZDebugEnabled'] = $this->debugEnabled;
        foreach ( $this->files as $file )
        {
            if ( is_file( $file ) )
                unlink( $file );
        }
    }

    public function testNothingIsNotedWithoutDebugOutput()
    {
        $GLOBALS['eZDebugEnabled'] = false;
        expRenderStats::noteModuleView( 'content', 'view', 'kernel/content/view.php' );
        expRenderStats::noteViewCacheOff( 'off' );
        expRenderStats::noteTemplateCompiled( 'design/standard/templates/a.tpl' );
        expRenderStats::noteTemplateExecuted();
        $this->assertSame( array(), expRenderStats::views() );
        $this->assertNull( expRenderStats::viewCache() );
        $this->assertSame( array( 'compiled' => array(), 'executed' => 0 ), expRenderStats::templates() );
    }

    public function testResetRequest()
    {
        expRenderStats::noteModuleView( 'content', 'view', 'kernel/content/view.php' );
        expRenderStats::noteViewCacheOff( 'off' );
        expRenderStats::noteTemplateExecuted();
        expRenderStats::resetRequest();
        $this->assertSame( array(), expRenderStats::views() );
        $this->assertNull( expRenderStats::viewCache() );
        $this->assertSame( 0, expRenderStats::templates()['executed'] );
    }

    public function testViewsAndTheirClass()
    {
        // eZModule notes the path relative to the root (the working directory), ModuleView::main() gets the view's
        // __FILE__: both name one file
        $script = realpath( 'kernel/content/view.php' );
        $this->assertNotFalse( $script, 'run from the installation root' );
        expRenderStats::noteModuleView( 'content', 'view', './kernel/content/view.php' );
        expRenderStats::noteModuleView( 'error', 'view', 'kernel/error/view.php' );
        expRenderStats::noteViewClass( 'Exponential\\View\\Kernel\\Content\\View', $script );
        $views = expRenderStats::views();
        $this->assertCount( 2, $views );
        $this->assertSame( 'content', $views[0]['module'] );
        $this->assertSame( 'Exponential\\View\\Kernel\\Content\\View', $views[0]['class'] );
        $this->assertNull( $views[1]['class'], 'a view without a class keeps none' );

        for ( $i = 0; $i < expRenderStats::MAX_VIEWS + 5; $i++ )
            expRenderStats::noteModuleView( 'content', 'view', $script );
        $this->assertCount( expRenderStats::MAX_VIEWS, expRenderStats::views(), 'the list does not grow without end' );
    }

    public function testViewCacheStates()
    {
        expRenderStats::noteViewCacheOff( 'the view mode line is not in [ContentSettings] CachedViewModes' );
        expRenderStats::noteViewGenerated( false );
        $cache = expRenderStats::viewCache();
        $this->assertSame( 'off', $cache['state'], 'generating the view does not hide why the cache was off' );
        $this->assertStringContainsString( 'CachedViewModes', $cache['reason'] );

        expRenderStats::resetRequest();
        expRenderStats::noteViewCacheKey( array( 'node_id' => 2, 'viewmode' => 'full' ), array(), 'abc', 'var/cache/2-abc.cache' );
        $this->assertSame( 'pending', expRenderStats::viewCache()['state'] );
        expRenderStats::noteViewCacheExpired( 'Content cache is expired by cache_ttl=60' );
        expRenderStats::noteViewGenerated( 'var/cache/2-abc.cache' );
        $cache = expRenderStats::viewCache();
        $this->assertSame( 'generated', $cache['state'] );
        $this->assertSame( 'Content cache is expired by cache_ttl=60', $cache['reason'], 'the reason the old file was not used stays' );
        $this->assertSame( 'abc', $cache['hash'] );
        $this->assertSame( array( 'node_id' => 2, 'viewmode' => 'full' ), $cache['parts'] );

        expRenderStats::resetRequest();
        expRenderStats::noteViewGenerated( false );
        $this->assertSame( 'uncached', expRenderStats::viewCache()['state'] );

        expRenderStats::resetRequest();
        expRenderStats::noteViewCacheHit();
        $cache = expRenderStats::viewCache();
        $this->assertSame( 'hit', $cache['state'] );
        $this->assertNull( $cache['age'], 'a cache that is no file (Valkey) has no age' );
        $this->assertNull( $cache['size'] );
    }

    public function testContentViewRetrieveNotesTheOutcome()
    {
        // the expiry check registers eZExecution's shutdown and exception handlers on its first call
        $handler = self::exceptionHandler();
        try
        {
            $this->retrieveNotesTheOutcome();
        }
        finally
        {
            while ( self::exceptionHandler() !== $handler && self::exceptionHandler() !== null )
                restore_exception_handler();
        }
    }

    private static function exceptionHandler()
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        return $handler;
    }

    private function retrieveNotesTheOutcome()
    {
        $args = array( 'ini' => eZINI::instance() );
        $file = $this->cacheFile( array( 'content' => 'cached', 'errorType' => 'kernel', 'errorNumber' => 1 ) );
        $result = eZNodeviewfunctions::contentViewRetrieve( $file, time() - 30, $args );
        $this->assertSame( 'cached', $result['content'] );
        $cache = expRenderStats::viewCache();
        $this->assertSame( 'hit', $cache['state'] );
        $this->assertGreaterThanOrEqual( 30, $cache['age'] );
        $this->assertSame( filesize( $file ), $cache['size'] );

        expRenderStats::resetRequest();
        $file = $this->cacheFile( array( 'content' => 'x', 'no_cache' => true ) );
        $result = eZNodeviewfunctions::contentViewRetrieve( $file, time(), $args );
        $this->assertInstanceOf( 'eZClusterFileFailure', $result );
        $cache = expRenderStats::viewCache();
        $this->assertSame( 'expired', $cache['state'] );
        $this->assertStringContainsString( 'no_cache', $cache['reason'] );
    }

    /**
     * The cluster handler passes the cache file to contentViewGenerate() also when the file said no_cache; then
     * nothing is stored, and the tab must not say so.
     */
    public function testAGenerationThatStoresNothingIsUncached()
    {
        $m = new ReflectionMethod( 'eZNodeviewfunctions', 'contentViewGenerate' );
        $this->assertSame( 'eZNodeviewfunctions', $m->getDeclaringClass()->getName() );
        expRenderStats::noteViewCacheExpired( 'Cache has been disabled for this node (no_cache)' );
        expRenderStats::noteViewGenerated( 'var/cache/2-abc.cache' );
        $this->assertSame( 'generated', expRenderStats::viewCache()['state'] );

        // contentViewGenerate() notes what it returned: a result with 'store' false is not stored
        $source = file_get_contents( $m->getFileName() );
        $this->assertStringContainsString( "noteViewGenerated( \$file !== false && !empty( \$retval['store'] ) ? \$file : false )", $source );
        expRenderStats::resetRequest();
        expRenderStats::noteViewCacheExpired( 'Cache has been disabled for this node (no_cache)' );
        expRenderStats::noteViewGenerated( false );
        $cache = expRenderStats::viewCache();
        $this->assertSame( 'uncached', $cache['state'] );
        $this->assertStringContainsString( 'no_cache', $cache['reason'], 'the reason stays' );
    }

    public function testTemplates()
    {
        expRenderStats::noteTemplateCompiled( 'design/standard/templates/a.tpl' );
        expRenderStats::noteTemplateExecuted();
        expRenderStats::noteTemplateExecuted();
        $this->assertSame( array( 'compiled' => array( 'design/standard/templates/a.tpl' ), 'executed' => 2 ), expRenderStats::templates() );
    }

    public function testTabEscapesCutsAndMarks()
    {
        $long = str_repeat( 'a', expDebugBarReport::RENDERING_VALUE_MAX + 50 );
        $html = expDebugBarReport::renderingHtml( $this->rendering( array(
            'state' => 'hit', 'reason' => '', 'age' => 12, 'size' => 2048, 'hash' => 'abc', 'path' => 'var/cache/2-abc.cache',
            'parts' => array( 'node_id' => 2, 'userroles' => '1.2', 'team' => '<b>x</b>', 'long' => $long, 'layout' => false ),
            'from_listeners' => array( 'team', 'long' ) ) ) );

        $this->assertStringContainsString( 'content/view', $html );
        $this->assertStringContainsString( 'Exponential\\View\\Kernel\\Content\\View', $html );
        $this->assertStringContainsString( 'Read from the cache', $html );
        $this->assertStringContainsString( '12 s', $html );
        $this->assertStringContainsString( '2.0 KB', $html );
        $this->assertStringContainsString( '&lt;b&gt;x&lt;/b&gt;', $html );
        $this->assertStringNotContainsString( '<b>x</b>', $html, 'a key value is escaped' );
        $this->assertSame( 2, substr_count( $html, '>listener</span>' ), 'the keys of listeners are marked, the others not' );
        $this->assertMatchesRegularExpression( '#<span title="a{' . ( expDebugBarReport::RENDERING_VALUE_MAX + 50 ) . '}">a{'
                                               . expDebugBarReport::RENDERING_VALUE_MAX . '}&hellip;</span>#', $html, 'a long value is cut' );
        $this->assertStringContainsString( '<details class="exp-debug-render-keys">', $html, 'the key is folded' );
        $this->assertStringContainsString( 'design/admin4', $html );
        $this->assertStringContainsString( 'templates/a.tpl', $html );

        $off = expDebugBarReport::renderingHtml( $this->rendering( array( 'state' => 'off', 'reason' => 'site.ini [ContentSettings] ViewCaching is disabled' ) ) );
        $this->assertStringContainsString( 'Not used (site.ini [ContentSettings] ViewCaching is disabled)', $off );
        $this->assertStringNotContainsString( 'exp-debug-render-keys', $off, 'no key when the cache was not used' );

        $none = expDebugBarReport::renderingHtml( $this->rendering( null, array() ) );
        $this->assertStringContainsString( 'No content view ran on this page.', $none );
        $this->assertStringContainsString( 'No module view ran.', $none );
    }

    public function testChips()
    {
        $chips = expDebugBarReport::renderingChips( $this->rendering( array( 'state' => 'hit', 'reason' => '' ) ) );
        $this->assertSame( array( 'rendering', 'viewcache', 'ok', 'View: hit' ), array_slice( $chips[0], 0, 4 ) );
        $this->assertSame( array( 'rendering', 'compiled', 'warn', 'Compiled 1' ), array_slice( $chips[1], 0, 4 ) );

        $chips = expDebugBarReport::renderingChips( $this->rendering( array( 'state' => 'generated', 'reason' => '' ) ) );
        $this->assertSame( array( 'warn', 'View: new' ), array_slice( $chips[0], 2, 2 ) );

        $chips = expDebugBarReport::renderingChips( $this->rendering( null ) );
        $this->assertCount( 1, $chips, 'no view chip without a content view' );
        $this->assertSame( 'compiled', $chips[0][1] );
    }

    private function rendering( $viewCache, $views = null )
    {
        if ( is_array( $viewCache ) )
        {
            $viewCache += array( 'age' => null, 'size' => null, 'parts' => array(), 'from_listeners' => array(), 'hash' => null, 'path' => null );
        }
        return array(
            'views' => $views !== null ? $views
                : array( array( 'module' => 'content', 'view' => 'view', 'script' => 'kernel/content/view.php',
                                'class' => 'Exponential\\View\\Kernel\\Content\\View' ) ),
            'view_cache' => $viewCache,
            'templates' => array( 'compiled' => array( 'design/standard/templates/a.tpl' ), 'executed' => 3 ),
            'siteaccess' => 'site', 'navigation_part' => 'ezcontentnavigationpart',
            'design' => array( 'design/admin4', 'design/standard' ),
            'settings' => array( 'ViewCaching' => 'enabled', 'TemplateCompile' => 'enabled', 'TemplateCache' => 'enabled' ),
        );
    }

    private function cacheFile( array $result )
    {
        $file = tempnam( sys_get_temp_dir(), 'exprender' );
        file_put_contents( $file, serialize( $result ) );
        $this->files[] = $file;
        return $file;
    }
}
