<?php
/**
 * The Copy button of the administration interface's full view (node/view/full.tpl of design/admin,
 * node/view/a4_full_body.tpl of design/admin4).
 *
 *  CB-01 - with create access at the parent: an enabled button that submits to content/copy/<object id>, the view
 *          where the editor chooses where the copy goes, labelled "Copy" with its title
 *  CB-02 - without it: a disabled button with no address and the "no permission" title
 *  CB-03 - the button sits between Move and Remove, in both designs
 *
 * The button's {if}...{/if} block is taken from each template as it is and rendered by eZTemplate::factory() (the
 * kernel's operators, i18n and ezurl included; no database), with the node as an array.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 4d56593614), Copyright (C) Mugo Web, GPL v2
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/content/eZAdminFullViewCopyButtonTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZAdminFullViewCopyButtonTest extends PHPUnit\Framework\TestCase
{
    const TEMPLATES = array( 'admin' => 'design/admin/templates/node/view/full.tpl',
                             'admin4' => 'design/admin4/templates/node/view/a4_full_body.tpl' );

    /** @var string private directory of the extracted templates */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-copy-button-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( glob( self::$dir . '/*' ) as $file )
            unlink( $file );
        rmdir( self::$dir );
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
        $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
    }

    public static function designProvider()
    {
        return array( 'admin' => array( 'admin' ), 'admin4' => array( 'admin4' ) );
    }

    /** The {if $node.parent.can_create} ... {/if} block of the Copy button, as written in the template */
    private static function block( $design )
    {
        $source = file_get_contents( self::TEMPLATES[$design] );
        $start = strpos( $source, '{if $node.parent.can_create}' );
        self::assertNotFalse( $start, "$design: the Copy button is in the template" );
        $end = strpos( $source, '{/if}', $start );
        return substr( $source, $start, $end + strlen( '{/if}' ) - $start );
    }

    private function render( $design, $canCreate )
    {
        $file = self::$dir . "/$design.tpl";
        file_put_contents( $file, self::block( $design ) );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'node', array( 'contentobject_id' => 4242, 'parent' => array( 'can_create' => $canCreate ) ) );
        $html = trim( $tpl->fetch( $file ) );
        $tpl->unsetVariable( 'node' );
        return $html;
    }

    /** CB-01 */
    #[\PHPUnit\Framework\Attributes\DataProvider('designProvider')]
    public function testEnabledWithCreateAccessAtTheParent( $design )
    {
        $html = $this->render( $design, true );
        $this->assertSame( 1, substr_count( $html, '<input' ), $html );
        $this->assertSame( 1, preg_match( '#formaction="([^"]*)"#', $html, $m ), $html );
        $this->assertStringEndsWith( '/content/copy/4242', $m[1] );
        $this->assertStringContainsString( 'class="button"', $html );
        $this->assertStringContainsString( 'type="submit"', $html );
        $this->assertStringContainsString( 'value="Copy"', $html );
        $this->assertStringContainsString( 'title="Copy this item to another location."', $html );
        $this->assertStringNotContainsString( 'disabled', $html );
    }

    /** CB-02 */
    #[\PHPUnit\Framework\Attributes\DataProvider('designProvider')]
    public function testDisabledWithoutIt( $design )
    {
        $html = $this->render( $design, false );
        $this->assertSame( 1, substr_count( $html, '<input' ), $html );
        $this->assertStringContainsString( 'class="button-disabled"', $html );
        $this->assertStringContainsString( 'disabled="disabled"', $html );
        $this->assertStringContainsString( 'title="You do not have permission to copy this item."', $html );
        $this->assertStringNotContainsString( 'formaction', $html );
        $this->assertStringNotContainsString( 'content/copy', $html );
    }

    /** CB-03 */
    #[\PHPUnit\Framework\Attributes\DataProvider('designProvider')]
    public function testBetweenMoveAndRemove( $design )
    {
        $source = file_get_contents( self::TEMPLATES[$design] );
        $move = strpos( $source, 'name="MoveNodeButton"' );
        $copy = strpos( $source, '{if $node.parent.can_create}' );
        $remove = strpos( $source, 'name="ActionRemove"' );
        $this->assertNotFalse( $move );
        $this->assertNotFalse( $remove );
        $this->assertGreaterThan( $move, $copy );
        $this->assertLessThan( $remove, $copy );
    }
}
