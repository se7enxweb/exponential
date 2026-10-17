<?php
/**
 * The cache blocks of the debug bar's Rendering tab (expRenderStats::noteCacheBlock(), noteCacheBlockStored(),
 * eZTemplateCacheBlock::handle(), retrieve(), store(), and the code eZTemplateCacheFunction compiles):
 *   - a hit, a miss that is stored with its generation time and size, nested blocks matched by their handler
 *   - the template and line from the placement, also with underscores in the path; custom keys are no placement
 *   - keys as text, objects by their class; at most MAX_CACHE_BLOCKS listed, the totals count every block
 *   - a {cache-block} in a compiled and in an interpreted template, missed and then read, with its template
 *   - the tab's section and the chip
 *
 * No database. The template and compiled files go to a private directory under var/tmp; the cache files the
 * blocks write are removed afterwards.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRenderStatsCacheBlockTest extends PHPUnit\Framework\TestCase
{
    private static $dir;

    private $savedGlobals = array();

    private $files = array();

    private $handler;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        self::$dir = 'var/tmp/phpunit-renderstats-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics', 'eZDebugEnabled' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $GLOBALS['eZDebugEnabled'] = true;
        expRenderStats::resetRequest();
        $this->handler = self::exceptionHandler();
    }

    protected function tearDown(): void
    {
        // the cluster handler and the expiry check register eZExecution's handlers on their first call
        while ( self::exceptionHandler() !== $this->handler && self::exceptionHandler() !== null )
            restore_exception_handler();
        expRenderStats::resetRequest();
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
        foreach ( $this->files as $file )
        {
            if ( is_file( $file ) )
                unlink( $file );
        }
    }

    private static function exceptionHandler()
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        return $handler;
    }

    private static function removeTree( $path )
    {
        if ( !$path || !file_exists( $path ) )
            return;
        if ( is_dir( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    self::removeTree( $path . '/' . $entry );
            }
            rmdir( $path );
        }
        else
            unlink( $path );
    }

    public function testNothingIsNotedWithoutDebugOutput()
    {
        $GLOBALS['eZDebugEnabled'] = false;
        expRenderStats::noteCacheBlock( 1, 'a.cache', false, null, false, 7200, true, 0.1 );
        $this->assertSame( array(), expRenderStats::cacheBlocks() );
        $this->assertSame( 0, expRenderStats::cacheBlockTotals()['count'] );
    }

    public function testHitMissAndNested()
    {
        expRenderStats::noteCacheBlock( 10, 'outer.cache', '46_1_60_15_design/admin4/templates/page_left_menu.tpl', array( 'a', 2 ), 2, 7200, false, 0.5 );
        expRenderStats::noteCacheBlock( 11, 'inner.cache', '50_5_55_20_extension/my_ext/design/x/templates/a_b.tpl', 'key', false, -1, false, 0.2 );
        expRenderStats::noteCacheBlockStored( 11, 100 );
        expRenderStats::noteCacheBlock( 12, 'hit.cache', false, null, false, null, true, 0.1, null, 42 );
        expRenderStats::noteCacheBlockStored( 10, 2048 );
        expRenderStats::noteCacheBlockStored( 99, 1 );

        $blocks = expRenderStats::cacheBlocks();
        $this->assertCount( 3, $blocks );
        $this->assertSame( 'design/admin4/templates/page_left_menu.tpl', $blocks[0]['template'] );
        $this->assertSame( 46, $blocks[0]['line'] );
        $this->assertSame( 'generated', $blocks[0]['state'] );
        $this->assertSame( 2048, $blocks[0]['size'], 'the outer block gets its own size, not the inner one\'s' );
        $this->assertSame( '["a",2]', $blocks[0]['keys'] );
        $this->assertSame( 2, $blocks[0]['node_id'] );
        $this->assertSame( 7200, $blocks[0]['ttl'] );
        $this->assertGreaterThanOrEqual( $blocks[1]['generate_ms'], $blocks[0]['generate_ms'], 'the outer time holds the inner one' );
        $this->assertSame( 'extension/my_ext/design/x/templates/a_b.tpl', $blocks[1]['template'], 'underscores in the path' );
        $this->assertNull( $blocks[1]['ttl'], 'no expiry' );
        $this->assertSame( 100, $blocks[1]['size'] );
        $this->assertSame( 'hit', $blocks[2]['state'] );
        $this->assertNull( $blocks[2]['template'], 'no placement' );
        $this->assertSame( 42, $blocks[2]['size'] );
        $this->assertArrayNotHasKey( 'started', $blocks[0] );

        $totals = expRenderStats::cacheBlockTotals();
        $this->assertEqualsWithDelta( $blocks[0]['generate_ms'], $totals['generate_ms'], 0.0001, 'the nested block is not counted twice' );
        $this->assertSame( 3, $totals['count'] );
        $this->assertSame( 1, $totals['hits'] );
        $this->assertEqualsWithDelta( 0.8, $totals['lookup_ms'], 0.0001 );
    }

    public function testKeysAndLimit()
    {
        expRenderStats::noteCacheBlock( 1, 'a.cache', false, array( 'user' => new stdClass(), 'list' => array( 1, '<b>' ) ), false, 0, true, 0.1 );
        $this->assertSame( '{"user":"(stdClass)","list":[1,"<b>"]}', expRenderStats::cacheBlocks()[0]['keys'] );

        expRenderStats::resetRequest();
        for ( $i = 0; $i < expRenderStats::MAX_CACHE_BLOCKS + 10; $i++ )
            expRenderStats::noteCacheBlock( $i, 'a.cache', false, null, false, 0, $i % 2 === 0, 0.1 );
        $this->assertCount( expRenderStats::MAX_CACHE_BLOCKS, expRenderStats::cacheBlocks() );
        $this->assertSame( expRenderStats::MAX_CACHE_BLOCKS + 10, expRenderStats::cacheBlockTotals()['count'], 'the totals count every block' );

        // a block whose store never ran (an exception in its body) leaves nothing a later block with its id could fill
        expRenderStats::resetRequest();
        expRenderStats::noteCacheBlock( 7, 'broken.cache', false, null, false, 0, false, 0.1 );
        expRenderStats::noteCacheBlock( 7, 'later.cache', false, null, false, 0, true, 0.1 );
        expRenderStats::noteCacheBlockStored( 7, 99 );
        $this->assertSame( array( 'pending', 'hit' ), array_column( expRenderStats::cacheBlocks(), 'state' ) );
        $this->assertNull( expRenderStats::cacheBlocks()[0]['size'] );
    }

    public function testHandleAndStore()
    {
        $path = self::$dir . '/blocks/' . md5( uniqid( '', true ) ) . '.cache';
        list( $handler, $content ) = eZTemplateCacheBlock::handle( $path, false, 7200, true, '3_1_9_14_design/standard/templates/x.tpl' );
        $this->assertInstanceOf( 'eZClusterFileFailure', $content );
        eZTemplateCacheBlock::store( $handler, 'generated text' );
        list( , $content ) = eZTemplateCacheBlock::handle( $path, false, 7200, true, '3_1_9_14_design/standard/templates/x.tpl' );
        $this->assertSame( 'generated text', $content );

        $blocks = expRenderStats::cacheBlocks();
        $this->assertSame( array( 'generated', 'hit' ), array_column( $blocks, 'state' ) );
        $this->assertSame( 14, $blocks[0]['size'] );
        $this->assertSame( 14, $blocks[1]['size'] );
        $this->assertSame( 'design/standard/templates/x.tpl', $blocks[1]['template'] );
        $this->assertSame( 3, $blocks[1]['line'] );

        // custom code with three keys of its own is no template
        expRenderStats::resetRequest();
        list( $handler, ) = eZTemplateCacheBlock::retrieve( array( 'my_code', 'two', 3 ), null, 60 );
        $this->files[] = $handler->name();
        $this->assertNull( expRenderStats::cacheBlocks()[0]['template'] );
        $this->assertSame( '["my_code","two",3]', expRenderStats::cacheBlocks()[0]['keys'] );
    }

    public static function modeProvider()
    {
        return array( 'compiled' => array( true ), 'interpreted' => array( false ) );
    }

    /**
     * @dataProvider modeProvider
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'modeProvider' )]
    public function testCacheBlockInATemplate( $compiled )
    {
        if ( eZINI::instance()->variable( 'TemplateSettings', 'TemplateCache' ) != 'enabled' )
            $this->markTestSkipped( 'TemplateCache is disabled' );
        $source = "first line\n{cache-block keys=array( 'k', \$v ) expiry=600}[{\$v}]{/cache-block}\n{cache-block}plain{/cache-block}";
        $file = self::$dir . '/' . md5( $source . uniqid( '', true ) ) . '.tpl';
        file_put_contents( $file, $source );

        $this->assertSame( "first line[5]plain", $this->render( $file, $compiled ), "the engine drops the line break before a tag" );
        $blocks = expRenderStats::cacheBlocks();
        foreach ( $blocks as $block )
            $this->files[] = $block['path'];
        $this->assertSame( array( 'generated', 'generated' ), array_column( $blocks, 'state' ) );
        $this->assertSame( $file, $blocks[0]['template'] );
        $this->assertSame( 2, $blocks[0]['line'] );
        $this->assertSame( '["k",5]', $blocks[0]['keys'] );
        $this->assertSame( 600, $blocks[0]['ttl'] );
        $this->assertSame( 3, $blocks[0]['size'] );
        $this->assertNull( $blocks[1]['keys'], 'a block without keys' );
        $this->assertSame( 3, $blocks[1]['line'] );

        expRenderStats::resetRequest();
        $this->assertSame( "first line[5]plain", $this->render( $file, $compiled ), "the engine drops the line break before a tag" );
        $this->assertSame( array( 'hit', 'hit' ), array_column( expRenderStats::cacheBlocks(), 'state' ) );
        $this->assertSame( 2, expRenderStats::cacheBlockTotals()['hits'] );
    }

    private function render( $file, $compiled )
    {
        if ( $compiled )
        {
            unset( $GLOBALS['eZSiteBasics'] );
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
            $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = self::$dir . '/compiled';
        }
        else
        {
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
            $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        }
        $tpl = new eZTemplate();
        $tpl->autoload();
        $tpl->setVariable( 'v', 5 );
        return $tpl->fetch( $file );
    }

    public function testTabAndChip()
    {
        expRenderStats::noteCacheBlock( 1, 'var/cache/a.cache', '46_1_60_15_design/admin4/templates/pagelayout.tpl', array( '<i>x</i>' ), 2, 7200, false, 0.5 );
        expRenderStats::noteCacheBlockStored( 1, 2048 );
        expRenderStats::noteCacheBlock( 2, 'var/cache/b.cache', false, null, false, null, true, 0.1, null, 10 );
        $data = expDebugBarReport::renderingData();
        $html = expDebugBarReport::renderingHtml( $data );
        $this->assertStringContainsString( '1 of 2 read from the cache', $html );
        $this->assertStringContainsString( 'design/admin4/templates/pagelayout.tpl:46', $html );
        $this->assertStringContainsString( 'subtree 2', $html );
        $this->assertStringContainsString( '2.0 KB', $html );
        $this->assertStringContainsString( '&lt;i&gt;x&lt;/i&gt;', $html );
        $this->assertStringNotContainsString( '<i>x</i>', $html, 'keys are escaped' );
        $this->assertStringContainsString( 'unknown (custom code, or a template compiled before)', $html );

        $chips = expDebugBarReport::renderingChips( $data );
        $chip = array_values( array_filter( $chips, function ( $c ) { return $c[1] === 'cacheblocks'; } ) );
        $this->assertSame( array( 'rendering', 'cacheblocks', 'warn', 'CB 1/2' ), array_slice( $chip[0], 0, 4 ) );

        expRenderStats::resetRequest();
        $data = expDebugBarReport::renderingData();
        $this->assertStringContainsString( 'No cache-block ran on this page.', expDebugBarReport::renderingHtml( $data ) );
        $this->assertSame( array(), array_filter( expDebugBarReport::renderingChips( $data ), function ( $c ) { return $c[1] === 'cacheblocks'; } ),
                           'no chip without blocks' );
    }
}
