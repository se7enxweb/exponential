<?php
/**
 * The template tree of the debug bar's Rendering tab (expRenderStats::templateEnter(), templateLeave(), tree(),
 * eZTemplate::fetch() and processURI(), expDebugBarReport::templateTreeHtml()):
 *   - nesting, inclusive times, the parent of each template
 *   - a template that left without saying so (an exception) does not break the nesting of what follows
 *   - at most MAX_TREE listed, every template counted, and the depth still right past the limit
 *   - RenderTree=disabled and debug output off record nothing
 *   - {include} in a compiled and an interpreted template
 *   - the HTML: nested lists, an override named with its file, escaping, "not compiled"
 *
 * No database. Template and compiled files go to a private directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRenderStatsTreeTest extends PHPUnit\Framework\TestCase
{
    private static $dir;

    private $savedGlobals = array();

    private $savedRenderTree;

    private $handler;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        self::$dir = 'var/tmp/phpunit-rendertree-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
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
        $ini = eZINI::instance( 'debugbar.ini' );
        $this->savedRenderTree = $ini->hasVariable( 'DebugBarSettings', 'RenderTree' ) ? $ini->variable( 'DebugBarSettings', 'RenderTree' ) : null;
        $ini->setVariable( 'DebugBarSettings', 'RenderTree', 'enabled' );
        $GLOBALS['eZDebugEnabled'] = true;
        expRenderStats::resetRequest();
        $this->handler = self::exceptionHandler();
    }

    private static function exceptionHandler()
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        return $handler;
    }

    protected function tearDown(): void
    {
        // the template engine registers eZExecution's handlers on its first use
        while ( self::exceptionHandler() !== $this->handler && self::exceptionHandler() !== null )
            restore_exception_handler();
        expRenderStats::resetRequest();
        $ini = eZINI::instance( 'debugbar.ini' );
        if ( $this->savedRenderTree === null )
            $ini->removeSetting( 'DebugBarSettings', 'RenderTree' );
        else
            $ini->setVariable( 'DebugBarSettings', 'RenderTree', $this->savedRenderTree );
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
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

    public function testNesting()
    {
        $a = expRenderStats::templateEnter( 'design:pagelayout.tpl', 'design/x/templates/pagelayout.tpl', 'pagelayout.tpl', true );
        $b = expRenderStats::templateEnter( 'design:a.tpl', null, null, true );
        $c = expRenderStats::templateEnter( 'design:b.tpl', null, null, false );
        expRenderStats::templateLeave( $c );
        expRenderStats::templateLeave( $b );
        $d = expRenderStats::templateEnter( 'design:c.tpl', null, null, true );
        expRenderStats::templateLeave( $d );
        expRenderStats::templateLeave( $a );

        $tree = expRenderStats::tree();
        $this->assertSame( array( 0, 1, 2, 1 ), array_column( $tree, 'depth' ) );
        $this->assertSame( array( -1, 0, 1, 0 ), array_column( $tree, 'parent' ) );
        $this->assertFalse( $tree[2]['compiled'] );
        $this->assertGreaterThanOrEqual( $tree[1]['ms'], $tree[0]['ms'], 'inclusive of what it included' );
        $this->assertArrayNotHasKey( 'started', $tree[0] );
        $this->assertSame( 4, expRenderStats::treeCount() );
    }

    public function testATemplateThatDidNotLeave()
    {
        $a = expRenderStats::templateEnter( 'design:a.tpl', null, null, true );
        expRenderStats::templateEnter( 'design:broken.tpl', null, null, true );
        // an exception: broken.tpl never left; a.tpl leaves and takes it from the stack
        expRenderStats::templateLeave( $a );
        $b = expRenderStats::templateEnter( 'design:b.tpl', null, null, true );
        expRenderStats::templateLeave( $b );
        $tree = expRenderStats::tree();
        $this->assertSame( 0, $tree[2]['depth'], 'b.tpl is at the top again' );
        $this->assertNull( $tree[1]['ms'], 'a template that did not end has no time' );
    }

    public function testLimit()
    {
        $outer = expRenderStats::templateEnter( 'design:outer.tpl', null, null, true );
        for ( $i = 0; $i < expRenderStats::MAX_TREE + 5; $i++ )
        {
            $x = expRenderStats::templateEnter( 'design:item.tpl', null, null, true );
            $y = expRenderStats::templateEnter( 'design:inner.tpl', null, null, true );
            expRenderStats::templateLeave( $y );
            expRenderStats::templateLeave( $x );
        }
        expRenderStats::templateLeave( $outer );
        $after = expRenderStats::templateEnter( 'design:after.tpl', null, null, true );
        $this->assertSame( -1, $after, 'past the limit: counted, not listed' );
        expRenderStats::templateLeave( $after );
        $this->assertCount( expRenderStats::MAX_TREE, expRenderStats::tree() );
        $this->assertSame( 1 + 2 * ( expRenderStats::MAX_TREE + 5 ) + 1, expRenderStats::treeCount() );
        $this->assertNotNull( expRenderStats::tree()[0]['ms'], 'the outer template still ended past the limit' );
    }

    public function testOffRecordsNothing()
    {
        eZINI::instance( 'debugbar.ini' )->setVariable( 'DebugBarSettings', 'RenderTree', 'disabled' );
        expRenderStats::resetRequest();
        $this->assertFalse( expRenderStats::templateEnter( 'design:a.tpl', null, null, true ) );
        expRenderStats::templateLeave( false );
        $this->assertSame( array(), expRenderStats::tree() );

        eZINI::instance( 'debugbar.ini' )->setVariable( 'DebugBarSettings', 'RenderTree', 'enabled' );
        expRenderStats::resetRequest();
        $GLOBALS['eZDebugEnabled'] = false;
        $this->assertFalse( expRenderStats::templateEnter( 'design:a.tpl', null, null, true ) );
        $this->assertSame( 0, expRenderStats::treeCount() );
    }

    public static function modeProvider()
    {
        return array( 'compiled' => array( true ), 'interpreted' => array( false ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'modeProvider' )]
    public function testIncludeInATemplate( $compiled )
    {
        $inner = self::$dir . '/inner-' . md5( uniqid( '', true ) ) . '.tpl';
        file_put_contents( $inner, 'inner' );
        $outer = self::$dir . '/outer-' . md5( uniqid( '', true ) ) . '.tpl';
        file_put_contents( $outer, '[{include uri="file:' . $inner . '"}]' );
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
        $this->assertSame( '[inner]', $tpl->fetch( $outer ) );

        $tree = expRenderStats::tree();
        $this->assertCount( 2, $tree );
        $this->assertSame( $outer, $tree[0]['uri'] );
        $this->assertSame( 'file:' . $inner, $tree[1]['uri'] );
        $this->assertSame( 1, $tree[1]['depth'] );
        $this->assertNotNull( $tree[1]['ms'] );
    }

    public function testHtml()
    {
        $a = expRenderStats::templateEnter( 'design:node/view/full.tpl', 'extension/x/design/y/override/templates/full/folder.tpl', 'node/view/full.tpl', true );
        $b = expRenderStats::templateEnter( 'design:parts/<b>.tpl', 'design/standard/templates/parts/<b>.tpl', null, false );
        expRenderStats::templateLeave( $b );
        expRenderStats::templateLeave( $a );
        $c = expRenderStats::templateEnter( 'design:pagelayout.tpl', 'design/standard/templates/pagelayout.tpl', null, true );
        expRenderStats::templateLeave( $c );

        $html = expDebugBarReport::renderingHtml( expDebugBarReport::renderingData() );
        $this->assertStringContainsString( '<summary>3 templates</summary>', $html );
        $this->assertStringContainsString( '<code>node/view/full.tpl</code> &rarr; <span class="exp-debug-render-override">extension/x/design/y/override/templates/full/folder.tpl</span>', $html, 'an override names its file' );
        $this->assertStringContainsString( '<code title="design/standard/templates/pagelayout.tpl">pagelayout.tpl</code>', $html, 'no override: the file is the tooltip' );
        $this->assertStringContainsString( 'parts/&lt;b&gt;.tpl', $html );
        $this->assertStringNotContainsString( '<b>.tpl', $html );
        $this->assertSame( 1, substr_count( $html, '>not compiled</span>' ) );
        // full.tpl > <b>.tpl, then pagelayout.tpl at the top again
        $this->assertMatchesRegularExpression( '#folder\.tpl</span> <span[^>]*>[^<]*</span><ul><li>.*?&lt;b&gt;.*?</li></ul></li><li><code title="design/standard/templates/pagelayout\.tpl">#s', $html );
        $this->assertSame( substr_count( $html, '<ul' ), substr_count( $html, '</ul>' ) );
        $this->assertSame( substr_count( $html, '<li>' ), substr_count( $html, '</li>' ) );
        $this->assertStringContainsString( '<details class="exp-debug-render-tree" open>', $html, 'the tree is open' );
        $this->assertStringContainsString( 'nested under the template that included it', $html, 'the section explains itself' );
    }

    public function testOverrideDetection()
    {
        foreach ( array(
            // an override below override/templates/ that has the template's name is still an override
            array( 'design:pagelayout.tpl', 'extension/x/design/y/override/templates/pagelayout.tpl', true ),
            // the template in its own place in another design
            array( 'design:pagelayout.tpl', 'extension/x/design/y/templates/pagelayout.tpl', false ),
            // a file template is its file
            array( 'file:design/standard/templates/a.tpl', 'design/standard/templates/a.tpl', false ),
        ) as $case )
        {
            expRenderStats::resetRequest();
            list( $uri, $file, $override ) = $case;
            expRenderStats::templateLeave( expRenderStats::templateEnter( $uri, $file, null, true ) );
            $html = expDebugBarReport::renderingHtml( expDebugBarReport::renderingData() );
            $this->assertSame( $override, strpos( $html, '&rarr;' ) !== false, $uri . ' in ' . $file );
        }
    }

    public function testDepthIsNormalisedForTheList()
    {
        // a template left on the stack (an exception caught by the caller) puts the next fetch one level deeper
        expRenderStats::templateEnter( 'design:stale.tpl', null, null, true );
        expRenderStats::templateEnter( 'design:stale2.tpl', null, null, true );
        $tree = array(
            array( 'uri' => 'design:a.tpl', 'file' => null, 'name' => null, 'compiled' => true, 'depth' => 2, 'parent' => -1, 'ms' => 1.0 ),
            array( 'uri' => 'design:b.tpl', 'file' => null, 'name' => null, 'compiled' => true, 'depth' => 5, 'parent' => 0, 'ms' => 1.0 ),
            array( 'uri' => 'design:c.tpl', 'file' => null, 'name' => null, 'compiled' => true, 'depth' => 1, 'parent' => -1, 'ms' => 1.0 ),
        );
        $m = new ReflectionMethod( 'expDebugBarReport', 'templateTreeHtml' );
        if ( PHP_VERSION_ID < 80100 )
            $m->setAccessible( true );
        $html = $m->invoke( null, $tree, 3 );
        $this->assertSame( substr_count( $html, '<ul' ), substr_count( $html, '</ul>' ) );
        $this->assertSame( substr_count( $html, '<li>' ), substr_count( $html, '</li>' ) );
        $this->assertDoesNotMatchRegularExpression( '#<ul[^>]*><ul#', $html, 'no list without an item' );
        $this->assertMatchesRegularExpression( '#<ul class="exp-debug-tree"><li><code>a\.tpl</code>.*?<ul><li><code>b\.tpl</code>.*?</li></ul></li><li><code>c\.tpl#s', $html );

        // leaving an index that is not on the stack keeps the stack
        expRenderStats::templateLeave( 99 );
        $d = expRenderStats::templateEnter( 'design:d.tpl', null, null, true );
        $this->assertSame( 2, expRenderStats::tree()[$d]['depth'] );
    }
}
