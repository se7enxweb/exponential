<?php
/**
 * The edit form's sticky button bar (#controlbar-top) neither moves under the pointer nor covers the editor:
 *
 *  EB-01 - the draft's save status stands in the bar's line, on one line that cannot grow (its text appears when a
 *          field loses the focus, between the press and the release of a click on a button), and on phones on a
 *          line of its own under the buttons
 *  EB-02 - admin4.js keeps --a4-sticky-edge on <html>: the header's height, on edit pages the bottom of the bar
 *  EB-03 - the page scrolls fields to below that edge, and the rich text editor's toolbar (TinyMCE 3 and 8) stays
 *          below it while the editor is in view
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminEditBarTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminEditBarTest extends PHPUnit\Framework\TestCase
{
    private static function file( $path )
    {
        $file = dirname( __DIR__, 3 ) . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    /** The declarations of every rule for exactly $selector in $css, comments removed */
    private static function rule( $css, $selector )
    {
        $css = (string)preg_replace( '#/\*.*?\*/#s', '', $css );
        self::assertGreaterThan( 0, preg_match_all( '/(?:^|\})\s*' . preg_quote( $selector, '/' ) . '\s*\{([^}]*)\}/', $css, $m ), "no rule $selector" );
        return preg_replace( '/\s+/', ' ', implode( ' ', $m[1] ) );
    }

    /** EB-01 */
    public function testTheSaveStatusCannotMoveTheButtons()
    {
        $css = self::file( 'design/admin4/stylesheets/admin4.css' );
        $this->assertStringContainsString( 'display: flex;', self::rule( $css, '#controlbar-top .box-bc .box-ml' ) );
        $right = self::rule( $css, '#controlbar-top .button-right' );
        foreach ( array( 'float: none;', 'min-width: 0;', 'overflow: hidden;' ) as $declaration )
            $this->assertStringContainsString( $declaration, $right );
        $place = self::rule( $css, '#controlbar-top #ez-as-place' );
        foreach ( array( 'float: none;', 'white-space: nowrap;', 'overflow: hidden;', 'text-overflow: ellipsis;' ) as $declaration )
            $this->assertStringContainsString( $declaration, $place );
        // phones: the status has its line from the start, so its text does not change how the buttons wrap
        $this->assertMatchesRegularExpression( '/#controlbar-top \.button-right \{ flex: 1 0 100%; min-height: 1\.5em; \}/', $css );
    }

    /** EB-02 */
    public function testAdmin4KeepsTheStickyEdge()
    {
        $js = self::file( 'design/admin4/javascript/admin4.js' );
        $this->assertStringContainsString( "document.documentElement.style.setProperty('--a4-sticky-edge', edge);", $js );
        $this->assertStringContainsString( "bar = document.getElementById('controlbar-top')", $js );
        $this->assertStringContainsString( 'bar.getBoundingClientRect().height', $js );
        $this->assertStringContainsString( 'if (bar) observer.observe(bar);', $js, 'the edge follows the bar when it wraps' );
    }

    /** EB-03 */
    public function testFieldsAndTheEditorToolbarStayBelowTheEdge()
    {
        $css = self::file( 'design/admin4/stylesheets/admin4.css' );
        $this->assertStringContainsString( 'scroll-padding-top: var(--a4-sticky-edge, 0px);', self::rule( $css, 'html' ) );
        $toolbar = self::rule( $css, '#editform table.mceLayout td.mceToolbar' );
        foreach ( array( 'position: sticky;', 'top: var(--a4-sticky-edge, 0px);', 'z-index: var(--a4-z-sticky);', 'background-color: var(--a4-card);' ) as $declaration )
            $this->assertStringContainsString( $declaration, $toolbar );
        // under the bar's own layer: the bar stays over the toolbar where they meet
        $this->assertMatchesRegularExpression( '/--a4-z-sticky: (\d+);/', self::file( 'design/admin4/stylesheets/components.css' ) );

        $tpl = self::file( 'extension/ezoe/design/standard/templates/content/datatype/edit/ezxmltext_ezoe_tinymce8.tpl' );
        $this->assertStringContainsString( 'toolbar_sticky: true,', $tpl );
        $this->assertStringContainsString( 'toolbar_sticky_offset: eZOe8StickyOffset(),', $tpl );
        $this->assertStringContainsString( "getPropertyValue( '--a4-sticky-edge' )", $tpl );
    }
}
