<?php
/**
 * The pagelayout hook at the end of the body (design:pagelayout/hooks/body_end.tpl, site.ini [PageLayoutHooks]
 * BodyEnd[]): every pagelayout of the kernel's designs includes it once, just before </body>, and the hook template
 * includes the registered templates and nothing else.
 *
 * Reads files only.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/PageLayoutHooksTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group design
 */

class PageLayoutHooksTest extends PHPUnit\Framework\TestCase
{
    const HOOK = "{include uri='design:pagelayout/hooks/body_end.tpl'}";

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** @return string[] the main pagelayout of every kernel design that has one */
    public static function pagelayouts()
    {
        $out = array();
        foreach ( glob( self::root() . '/design/*/templates/pagelayout.tpl' ) as $file )
            $out[basename( dirname( dirname( $file ) ) )] = array( $file );
        return $out;
    }

    /** @dataProvider pagelayouts */
    public function testPagelayoutIncludesTheHookOnceBeforeTheBodyEnds( $file )
    {
        $text = file_get_contents( $file );
        $this->assertSame( 1, substr_count( $text, self::HOOK ), "$file: the hook once" );
        $this->assertMatchesRegularExpression( '#' . preg_quote( self::HOOK, '#' ) . '\s*</body>#', $text, "$file: right before </body>" );
    }

    public function testEveryKernelDesignWithAPagelayoutIsCovered()
    {
        $designs = array_keys( self::pagelayouts() );
        foreach ( array( 'admin', 'admin3', 'admin4', 'admin4l', 'base', 'mysite', 'plain', 'standard' ) as $design )
            $this->assertContains( $design, $designs );
    }

    public function testHookTemplateIncludesTheRegisteredTemplates()
    {
        $text = file_get_contents( self::root() . '/design/standard/templates/pagelayout/hooks/body_end.tpl' );
        $code = preg_replace( '/\{\*.*?\*\}/s', '', $text );
        $this->assertStringContainsString( "ezini( 'PageLayoutHooks', 'BodyEnd', 'site.ini' )", $code );
        $this->assertStringContainsString( "{include uri=concat( 'design:', \$exp_body_end_hook )}", $code );
        $this->assertStringNotContainsString( '<', trim( preg_replace( '/\{[^}]*\}/', '', $code ) ), 'prints nothing of its own' );
    }

    public function testSiteIniDeclaresAnEmptyList()
    {
        $ini = file_get_contents( self::root() . '/settings/site.ini' );
        $this->assertMatchesRegularExpression( '/^\[PageLayoutHooks\]\n(?:#[^\n]*\n)*BodyEnd\[\]\n/m', $ini );
    }
}
