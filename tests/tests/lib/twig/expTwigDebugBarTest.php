<?php
/**
 * The debug bar with Twig templates: a .twig included by a .twig is an entry of the template tree below the template
 * that included it, nothing is noted without debug output, and a {% expcacheblock %} is a row of the cache blocks with
 * its template and id (placement twig:<template>:<id>).
 *
 * Twig 3 from Composer, or TWIG_LIBRARY_PATH; without it the cases are skipped. No database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group twig
 */

class expTwigDebugBarTest extends PHPUnit\Framework\TestCase
{
    private static $dir;
    private $savedGlobals = array();
    private $savedSettings = array();
    private $handler;
    private $files = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-twig-debugbar-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
        $libraryPath = getenv( 'TWIG_LIBRARY_PATH' );
        if ( !class_exists( '\Twig\Environment' ) && $libraryPath )
            eZINI::instance( 'twig.ini' )->setVariable( 'TwigSettings', 'LibraryPath', $libraryPath );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( glob( self::$dir . '/*' ) as $file )
            unlink( $file );
        rmdir( self::$dir );
    }

    protected function setUp(): void
    {
        if ( !expTwigRenderer::loadLibrary() )
            $this->markTestSkipped( 'Twig 3 is not installed (composer require twig/twig, or TWIG_LIBRARY_PATH)' );
        $this->savedGlobals['eZDebugEnabled'] = array_key_exists( 'eZDebugEnabled', $GLOBALS ) ? array( $GLOBALS['eZDebugEnabled'] ) : null;
        $debugbar = eZINI::instance( 'debugbar.ini' );
        $this->savedSettings = array(
            'RenderTree' => $debugbar->hasVariable( 'DebugBarSettings', 'RenderTree' ) ? $debugbar->variable( 'DebugBarSettings', 'RenderTree' ) : null,
            'TemplateCache' => eZINI::instance()->variable( 'TemplateSettings', 'TemplateCache' ),
        );
        $debugbar->setVariable( 'DebugBarSettings', 'RenderTree', 'enabled' );
        $GLOBALS['eZDebugEnabled'] = true;
        expRenderStats::resetRequest();
        $this->handler = self::exceptionHandler();
    }

    protected function tearDown(): void
    {
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

    private function file( $name, $source )
    {
        $file = self::$dir . '/' . $name . '-' . md5( $source ) . '.twig';
        if ( !file_exists( $file ) )
            file_put_contents( $file, $source );
        return $file;
    }

    public function testSubtemplateIsInTheTemplateTree()
    {
        $leaf = $this->file( 'leaf', 'leaf {{ v }}' );
        $child = $this->file( 'child', "child [{% expinclude uri='file:$leaf' %}]" );
        $parent = $this->file( 'parent', "parent [{% expinclude uri='file:$child' v=2 %}]" );
        $this->assertSame( 'parent [child [leaf 2]]', eZTemplate::factory()->fetch( $parent ) );
        $tree = expRenderStats::tree();
        $this->assertCount( 3, $tree );
        $this->assertSame( array( 0, 1, 2 ), array_column( $tree, 'depth' ) );
        $this->assertSame( "file:$child", $tree[1]['uri'] );
        $this->assertStringEndsWith( $leaf, $tree[2]['file'] );
        foreach ( $tree as $entry )
            $this->assertNotNull( $entry['ms'], 'every template ended' );
    }

    public function testNothingIsNotedWithoutDebugOutput()
    {
        $GLOBALS['eZDebugEnabled'] = false;
        $child = $this->file( 'quiet-child', 'quiet' );
        $parent = $this->file( 'quiet-parent', "[{% expinclude uri='file:$child' %}]" );
        $this->assertSame( '[quiet]', eZTemplate::factory()->fetch( $parent ) );
        $this->assertSame( array(), expRenderStats::tree() );
    }

    public function testCacheBlockRowsNameTheTemplateAndTheId()
    {
        eZINI::instance()->setVariable( 'TemplateSettings', 'TemplateCache', 'enabled' );
        $unique = uniqid( '', true );
        $file = $this->file( 'blocks', "{% expcacheblock id=7 keys=['k', '$unique'] %}seven{% endexpcacheblock %}{% expcacheblock id='side' %}side $unique{% endexpcacheblock %}" );
        $this->assertStringStartsWith( 'seven', eZTemplate::factory()->fetch( $file ) );
        $blocks = expRenderStats::cacheBlocks();
        foreach ( $blocks as $block )
            $this->files[] = $block['path'];
        $this->assertCount( 2, $blocks );
        $this->assertSame( array( 'generated', 'generated' ), array_column( $blocks, 'state' ) );
        $this->assertSame( array( '7', 'side' ), array_column( $blocks, 'block_id' ) );
        $this->assertStringEndsWith( $file, $blocks[0]['template'] );
        $this->assertSame( json_encode( array( 'k', $unique ) ), $blocks[0]['keys'] );
        $this->assertTrue( eZTemplateCacheBlock::isSmartyPlacement( 'twig:a.twig:1' ) );

        // the second time from the cache
        expRenderStats::resetRequest();
        $this->assertStringStartsWith( 'seven', eZTemplate::factory()->fetch( $file ) );
        $this->assertSame( array( 'hit', 'hit' ), array_column( expRenderStats::cacheBlocks(), 'state' ) );
    }
}
