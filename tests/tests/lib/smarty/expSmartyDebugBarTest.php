<?php
/**
 * Smarty templates in the debug bar's Rendering tab (expRenderStats):
 *   - a .stpl that an {expinclude} renders as a Smarty subtemplate (expSmartyRenderer::renderSubtemplate()) is an
 *     entry of the template tree, below the template that included it, also two levels down; nothing is noted
 *     without debug output;
 *   - an {expcacheblock} row names its .stpl file and its id (placement stpl:<file>:<id>, with and without keys),
 *     in the list and in the tab's HTML.
 *
 * Smarty 5 as in expSmartyTemplateRenderTest (Composer, or SMARTY_LIBRARY_PATH); without it the cases are skipped.
 * No database and no siteaccess. The cache files the blocks write are removed afterwards.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group smarty
 */

require_once __DIR__ . '/expSmartyOperatorParityTest.php';

class expSmartyDebugBarTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files */
    private static $dir;

    private $savedGlobals = array();

    private $savedSettings = array();

    private $handler;

    /** @var string[] cache files to remove */
    private $files = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-smarty-debugbar-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
        expSmartyOperatorParityTest::trustTestDirectory( self::$dir );
        $libraryPath = getenv( 'SMARTY_LIBRARY_PATH' );
        if ( !class_exists( '\Smarty\Smarty' ) && $libraryPath )
            eZINI::instance( 'smarty.ini' )->setVariable( 'SmartySettings', 'LibraryPath', $libraryPath );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( glob( self::$dir . '/*' ) as $file )
            unlink( $file );
        rmdir( self::$dir );
    }

    protected function setUp(): void
    {
        if ( !expSmartyRenderer::loadLibrary() )
            $this->markTestSkipped( 'Smarty 5 is not installed (composer require smarty/smarty, or SMARTY_LIBRARY_PATH)' );
        $this->savedGlobals['eZDebugEnabled'] = array_key_exists( 'eZDebugEnabled', $GLOBALS ) ? array( $GLOBALS['eZDebugEnabled'] ) : null;
        $debugbar = eZINI::instance( 'debugbar.ini' );
        $site = eZINI::instance();
        $this->savedSettings = array(
            'RenderTree' => $debugbar->hasVariable( 'DebugBarSettings', 'RenderTree' ) ? $debugbar->variable( 'DebugBarSettings', 'RenderTree' ) : null,
            'TemplateCache' => $site->variable( 'TemplateSettings', 'TemplateCache' ),
        );
        $debugbar->setVariable( 'DebugBarSettings', 'RenderTree', 'enabled' );
        $GLOBALS['eZDebugEnabled'] = true;
        expRenderStats::resetRequest();
        $this->handler = self::exceptionHandler();
    }

    protected function tearDown(): void
    {
        // the template engine and the cluster handler register eZExecution's handlers on their first use
        while ( self::exceptionHandler() !== $this->handler && self::exceptionHandler() !== null )
            restore_exception_handler();
        expRenderStats::resetRequest();
        $debugbar = eZINI::instance( 'debugbar.ini' );
        if ( $this->savedSettings['RenderTree'] === null )
            $debugbar->removeSetting( 'DebugBarSettings', 'RenderTree' );
        else
            $debugbar->setVariable( 'DebugBarSettings', 'RenderTree', $this->savedSettings['RenderTree'] );
        eZINI::instance()->setVariable( 'TemplateSettings', 'TemplateCache', $this->savedSettings['TemplateCache'] );
        if ( $this->savedGlobals['eZDebugEnabled'] === null )
            unset( $GLOBALS['eZDebugEnabled'] );
        else
            $GLOBALS['eZDebugEnabled'] = $this->savedGlobals['eZDebugEnabled'][0];
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

    /** A template file of its own name, $name.stpl, so a test can refer to it by name */
    private function file( $name, $source )
    {
        $file = self::$dir . '/' . $name . '-' . md5( $source ) . '.stpl';
        if ( !file_exists( $file ) )
            file_put_contents( $file, $source );
        return $file;
    }

    public function testSubtemplateIsInTheTemplateTree()
    {
        $leaf = $this->file( 'leaf', 'leaf {$v}' );
        $child = $this->file( 'child', "child [{expinclude uri='file:$leaf'}]" );
        $parent = $this->file( 'parent', "parent [{expinclude uri='file:$child' v=2}]" );

        $this->assertSame( 'parent [child [leaf 2]]', eZTemplate::factory()->fetch( $parent ) );
        $tree = expRenderStats::tree();
        $this->assertSame( array( $parent, $child, $leaf ), array_column( $tree, 'file' ) );
        $this->assertSame( array( 0, 1, 2 ), array_column( $tree, 'depth' ) );
        $this->assertSame( array( -1, 0, 1 ), array_column( $tree, 'parent' ) );
        $this->assertSame( "file:$child", $tree[1]['uri'] );
        foreach ( $tree as $entry )
            $this->assertNotNull( $entry['ms'], 'every template ended' );

        // compiled now the first time, from Smarty's compiled file afterwards
        expRenderStats::resetRequest();
        eZTemplate::factory()->fetch( $parent );
        $tree = expRenderStats::tree();
        $this->assertTrue( $tree[1]['compiled'] );
        $this->assertTrue( $tree[2]['compiled'] );
    }

    public function testNothingIsNotedWithoutDebugOutput()
    {
        $GLOBALS['eZDebugEnabled'] = false;
        $child = $this->file( 'quiet-child', 'quiet' );
        $parent = $this->file( 'quiet-parent', "[{expinclude uri='file:$child'}]" );
        $this->assertSame( '[quiet]', eZTemplate::factory()->fetch( $parent ) );
        $this->assertSame( array(), expRenderStats::tree() );
        $this->assertSame( 0, expRenderStats::treeCount() );
    }

    public function testCacheBlockRowsNameTheTemplateAndTheId()
    {
        eZINI::instance()->setVariable( 'TemplateSettings', 'TemplateCache', 'enabled' );
        $unique = uniqid( '', true );
        $file = $this->file( 'blocks', "{expcacheblock id=7 keys=['k', '$unique']}seven{/expcacheblock}"
                                     . "{expcacheblock id='side'}side $unique{/expcacheblock}" );

        $this->assertStringStartsWith( 'seven', eZTemplate::factory()->fetch( $file ) );
        $blocks = expRenderStats::cacheBlocks();
        foreach ( $blocks as $block )
            $this->files[] = $block['path'];
        $this->assertCount( 2, $blocks );
        $this->assertSame( array( 'generated', 'generated' ), array_column( $blocks, 'state' ) );
        $this->assertSame( array( '7', 'side' ), array_column( $blocks, 'block_id' ) );
        $this->assertSame( array( null, null ), array_column( $blocks, 'line' ) );
        $this->assertStringEndsWith( $file, $blocks[0]['template'] );
        $this->assertStringEndsWith( $file, $blocks[1]['template'] );
        $this->assertSame( json_encode( array( 'k', $unique ) ), $blocks[0]['keys'] );
        $this->assertNull( $blocks[1]['keys'], 'a block without keys' );

        $html = expDebugBarReport::renderingHtml( expDebugBarReport::renderingData() );
        $this->assertStringContainsString( $file . ' id=7', $html );
        $this->assertStringContainsString( $file . ' id=side', $html );
        $this->assertStringNotContainsString( 'unknown (custom code', $html );

        expRenderStats::resetRequest();
        eZTemplate::factory()->fetch( $file );
        $this->assertSame( array( 'hit', 'hit' ), array_column( expRenderStats::cacheBlocks(), 'state' ) );
        $this->assertSame( array( '7', 'side' ), array_column( expRenderStats::cacheBlocks(), 'block_id' ) );
    }

    public function testPlacementOfASmartyBlockInRetrieve()
    {
        $this->assertTrue( eZTemplateCacheBlock::isSmartyPlacement( 'stpl:/a/b.stpl:3' ) );
        $this->assertTrue( eZTemplateCacheBlock::isSmartyPlacement( 'stpl:c:/a/b.stpl:3' ) );
        $this->assertFalse( eZTemplateCacheBlock::isSmartyPlacement( 'stpl:3' ) );
        $this->assertFalse( eZTemplateCacheBlock::isSmartyPlacement( '3_1_9_14_design/standard/templates/x.tpl' ) );

        list( $handler, ) = eZTemplateCacheBlock::retrieve( array( 'stpl:/x/y.stpl:4', 'siteaccess' ), null, 60 );
        $this->files[] = $handler->name();
        list( $handler, ) = eZTemplateCacheBlock::retrieve( array( array( 'a' ), 'stpl:/x/y.stpl:5', 'siteaccess' ), null, 60 );
        $this->files[] = $handler->name();
        $blocks = expRenderStats::cacheBlocks();
        $this->assertSame( array( '/x/y.stpl', '/x/y.stpl' ), array_column( $blocks, 'template' ) );
        $this->assertSame( array( '4', '5' ), array_column( $blocks, 'block_id' ) );
        $this->assertSame( array( null, '["a"]' ), array_column( $blocks, 'keys' ) );
    }
}
