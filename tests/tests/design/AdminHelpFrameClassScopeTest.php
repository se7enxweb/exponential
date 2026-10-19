<?php
/**
 * .a4-help is two things in the admin designs: the shared hint text under a form field (components.css, in about 80
 * templates) and the frame of the help pages (modules/help.css). The help module once styled the bare class as a
 * grid with container-type: inline-size; a hint inside a flex row (the package wizard's tiles, package/create) then
 * measured 0px wide and showed one character per line. The help module's frame rules must name the frame
 * (.a4-help-frame), and every help page frame must carry data-exphelp. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminHelpFrameClassScopeTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    public static function designs()
    {
        return array( 'admin' => array( 'admin' ), 'admin4' => array( 'admin4' ) );
    }

    /**
     * @dataProvider designs
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testHelpModuleNeverStylesTheBareHintClass( $design )
    {
        $file = self::root() . '/design/' . $design . '/stylesheets/modules/help.css';
        $this->assertFileExists( $file );
        $css = preg_replace( '#/\*.*?\*/#s', '', file_get_contents( $file ) );
        // every selector of every rule, also inside @media/@container blocks
        preg_match_all( '/([^{}]+)\{/', $css, $m );
        foreach ( $m[1] as $selectorList )
        {
            if ( strpos( trim( $selectorList ), '@' ) === 0 )
                continue;
            foreach ( explode( ',', $selectorList ) as $selector )
            {
                $this->assertNotSame( '.a4-help', trim( $selector ),
                    $design . ': a rule for the bare .a4-help also styles every form hint; name the help frame (.a4-help-frame)' );
            }
        }
    }

    /**
     * @dataProvider designs
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testEveryHelpFrameCarriesTheDataHook( $design )
    {
        $files = glob( self::root() . '/design/' . $design . '/templates/exphelp/*.tpl' );
        $this->assertNotEmpty( $files );
        foreach ( $files as $file )
        {
            preg_match_all( '/<div class="a4-page a4-help\b[^"]*"[^>]*>/', file_get_contents( $file ), $m );
            foreach ( $m[0] as $tag )
            {
                $this->assertStringContainsString( 'data-exphelp', $tag, basename( $file ) . ': ' . $tag );
            }
        }
    }

    /**
     * The package wizard's tiles keep their description as a block of text that fills the tile.
     *
     * @dataProvider designs
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testPackageCreatorTilesFillTheirWidth( $design )
    {
        $css = file_get_contents( self::root() . '/design/' . $design . '/stylesheets/modules/structure.css' );
        $this->assertMatchesRegularExpression( '/\.a4-package-creator-list label > span \{[^}]*flex: 1 1 auto;/', $css );
        $this->assertMatchesRegularExpression( '/\.a4-package-creator-list label \.a4-help \{[^}]*display: block;/', $css );
    }
}
