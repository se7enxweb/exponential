<?php
/**
 * The bar of the help pages (viewer, topic, contents, manual) puts Help Start and the language bar on one line:
 * [Help Start] | Reading in: English | Deutsch. Both sit in one container (.a4-help-viewer-line), Help Start first,
 * so the button never takes a row of its own; a page without a translation shows the button alone in that line.
 * The line wraps (flex-wrap) and may shrink (min-width: 0), so it fits 960px at 200% zoom. No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminHelpLanguageLineTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    public static function pages()
    {
        $rows = array();
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            foreach ( array( 'viewer', 'topic', 'contents', 'manual' ) as $page )
                $rows[$design . '/' . $page] = array( $design, $page );
        }
        return $rows;
    }

    public static function designs()
    {
        return array( 'admin' => array( 'admin' ), 'admin4' => array( 'admin4' ) );
    }

    /**
     * @dataProvider pages
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'pages' )]
    public function testHelpStartAndTheLanguageBarShareOneContainer( $design, $page )
    {
        $tpl = file_get_contents( self::root() . '/design/' . $design . '/templates/exphelp/' . $page . '.tpl' );
        $this->assertSame( 1, preg_match(
            '#<div class="a4-help-viewer-line"[^>]*>\s*(<a [^>]*href=\{\'exphelp/dashboard\'\|ezurl\}[^>]*>.*?</a>)\s*(\{if [^\n]*parts/reading_language\.tpl[^\n]*\{/if\})\s*</div>#s',
            $tpl, $m ), $design . '/' . $page . ': Help Start and the language bar must be one line, Help Start first' );
        $this->assertStringContainsString( 'a4-help-viewer-start', $m[1] );
        $this->assertStringContainsString( 'is_set( $help_reading )', $m[2], 'without a translation the line shows Help Start alone' );
        // no language bar outside the line
        $this->assertSame( 1, substr_count( $tpl, "uri='design:exphelp/parts/reading_language.tpl'" ), $design . '/' . $page );
    }

    /**
     * @dataProvider designs
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'designs' )]
    public function testTheLineWrapsAndTheBarIsTight( $design )
    {
        $css = file_get_contents( self::root() . '/design/' . $design . '/stylesheets/modules/help.css' );
        $this->assertMatchesRegularExpression( '/\n\.a4-help-viewer-line \{[^}]*flex-wrap: wrap;[^}]*min-width: 0;/', $css );
        $this->assertMatchesRegularExpression( '/\n\.a4-help-viewer-line \.a4-help-reading \{[^}]*margin: 0;/', $css );
        $this->assertMatchesRegularExpression( '/\n\.a4-help-viewer-bar \{[^}]*margin: 0;[^}]*padding: 0 0 var\(--a4-space-2\);/', $css );
        // the language bar no longer forces a row of its own
        $this->assertStringNotContainsString( '.a4-help-viewer-bar .a4-help-reading { flex: 1 1 100%', $css );
    }
}
