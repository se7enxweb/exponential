<?php
/**
 * "Bookmark this page" (design:content/bookmark_view_action.tpl, mode toolbar) floats at the end of the breadcrumb
 * line. A block that is its own formatting context (the dashboard's grid, a card) placed beside a float is narrowed by
 * the float's width for its whole height: the dashboard lost 172px over 5,000px, an empty column down the page. The
 * float may sit beside the breadcrumb only; what follows the breadcrumb clears it, inside #fix, which is its own
 * formatting context so that the clear never waits for a column outside it.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminBookmarkPageButtonFlowTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminBookmarkPageButtonFlowTest extends PHPUnit\Framework\TestCase
{
    private static function read( $path )
    {
        return file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    public static function designs()
    {
        return array( 'admin' => array( 'admin' ), 'admin4' => array( 'admin4' ) );
    }

    /** @dataProvider designs */
    #[PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testTheContentAfterTheBreadcrumbClearsTheButton( $design )
    {
        $css = self::read( "design/$design/stylesheets/modules/content.css" );
        $this->assertMatchesRegularExpression( '/\.a4-content-bookmark-pageaction\.is-toolbar \{ float: inline-end;/', $css );
        $this->assertStringContainsString( '#fix:has(> .a4-content-bookmark-pageaction.is-toolbar) { display: flow-root; }', $css,
                                           '#fix holds the clear' );
        $this->assertStringContainsString( '#fix > .a4-content-bookmark-pageaction.is-toolbar ~ #path ~ * { clear: inline-end; }', $css,
                                           'what follows the breadcrumb starts below the button' );
    }

    /** the selectors match the markup: the button, then #path, both children of #fix, in every pagelayout */
    public function testThePagelayoutsPutTheButtonBeforeTheBreadcrumbInFix()
    {
        foreach ( array( 'admin', 'admin4', 'admin4l' ) as $design )
        {
            $tpl = self::read( "design/$design/templates/pagelayout.tpl" );
            $fix = strpos( $tpl, '<div id="fix">' );
            $button = strpos( $tpl, "{include uri='design:content/bookmark_view_action.tpl' mode='toolbar'", (int)$fix );
            $path = strpos( $tpl, '<div id="path"', (int)$button );
            $this->assertNotFalse( $fix, "$design: #fix" );
            $this->assertNotFalse( $button, "$design: the button inside #fix" );
            $this->assertNotFalse( $path, "$design: #path after the button" );
            $this->assertSame( '', trim( preg_replace( '/\{\*.*?\*\}/s', '', substr( $tpl, $fix + strlen( '<div id="fix">' ), $button - $fix - strlen( '<div id="fix">' ) ) ) ),
                               "$design: the button is the first child of #fix" );
        }
    }
}
