<?php
/**
 * The parts of admin4 a keyboard could not use: the node tabs follow the WAI-ARIA tabs pattern (node_tabs.js), the
 * show/hide tabs toggle is a button that reports its state, the open/close link of the content tree is a named
 * button with aria-expanded (contentstructuremenu_dynamic.js), and the calendar of the date fields opens from a
 * button. The behaviour itself (arrow keys, Home, End, focus) is in the scripts; this test holds the markup and the
 * hooks they rely on. doc/features/6.0/admin4-design.md.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminKeyboardTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminKeyboardTest extends PHPUnit\Framework\TestCase
{
    private static function file( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 3 ) . '/' . $path );
    }

    public function testTheNodeTabsFollowTheTabsPattern()
    {
        $js = self::file( 'design/admin4/javascript/node_tabs.js' );
        foreach ( array( "'role', 'tablist'", "'role', 'tab'", "'aria-selected'", "'aria-controls'", "'role', 'tabpanel'",
                         "'aria-labelledby'", "'ArrowRight'", "'ArrowLeft'", "'Home'", "'End'", "on( 'keydown', NodeTab.keydown )" ) as $needle )
            $this->assertStringContainsString( $needle, $js, $needle );
        // the clicked element may be a child of the link: the tab is the li around it
        $this->assertStringNotContainsString( 'e.target.parentNode', $js );
        $this->assertStringContainsString( "closest( 'li' )", $js );
    }

    public function testTheShowHideTabsToggleIsAButtonWithItsState()
    {
        foreach ( array( 'design/admin4/templates/window_controls.tpl', 'design/admin4/templates/node/top_node_full.tpl' ) as $path )
        {
            $tpl = self::file( $path );
            $this->assertSame( 2, preg_match_all( '#<a id="maincontent-(?:show|hide)" class="show-hide-tabs"[^>]* role="button" aria-pressed="(?:true|false)" aria-label="[^"]+" data-label-show="[^"]+" data-label-hide="[^"]+">#', $tpl ), $path );
        }
        $this->assertStringContainsString( "setAttribute( 'aria-pressed'", self::file( 'design/admin4/javascript/node_tabs.js' ) );
    }

    public function testTheTreeToggleIsANamedButtonWithItsState()
    {
        $js = self::file( 'design/admin4/javascript/contentstructuremenu_dynamic.js' );
        $this->assertStringContainsString( 'role="button" aria-expanded="false" aria-label="', $js );
        $this->assertStringContainsString( 'i18n.subitems', $js );
        // every change of the symbol goes through _toggleState, which keeps aria-expanded in step
        $this->assertSame( 0, preg_match_all( '/\.className\s*=\s*[\'"]openclose-/', $js ) );
        $this->assertGreaterThanOrEqual( 6, substr_count( $js, '_toggleState( aElement,' ) );
        $this->assertStringContainsString( '"subitems":', self::file( 'design/admin4/templates/contentstructuremenu/content_structure_menu_dynamic.tpl' ) );
    }

    public function testTheCalendarOpensFromAButton()
    {
        foreach ( array( 'ezdate' => 'date', 'ezdatetime' => 'datetime' ) as $template => $kind )
        {
            $tpl = self::file( "design/admin4/templates/content/datatype/edit/$template.tpl" );
            $this->assertMatchesRegularExpression( '#<button type="button" class="datepicker-icon a4-content-datepicker-icon" id="\{\$attribute_base\}_' . $kind . '_cal_\{\$attribute\.id\}" aria-haspopup="dialog"[^>]* aria-label="#', $tpl, $template );
        }
    }

    /** An image with onclick cannot be reached by the Tab key */
    public function testNoTemplateOfAdmin4HasAClickableImage()
    {
        $found = array();
        $base = dirname( __DIR__, 3 ) . '/design/admin4/templates/';
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            if ( substr( $file->getFilename(), -4 ) !== '.tpl' )
                continue;
            $source = preg_replace( '/\{\*.*?\*\}/s', '', (string)file_get_contents( $file->getPathname() ) );
            if ( preg_match( '/<img\b[^>]*\bonclick=/i', $source ) )
                $found[] = substr( $file->getPathname(), strlen( $base ) );
        }
        $this->assertSame( array(), $found );
    }
}
