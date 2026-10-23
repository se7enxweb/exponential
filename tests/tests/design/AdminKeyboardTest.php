<?php
/**
 * The parts of admin4 a keyboard could not use: the node tabs follow the WAI-ARIA tabs pattern (node_tabs.js), the
 * show/hide tabs toggle is a button that reports its state, the open/close link of the content tree is a named
 * button with aria-expanded (contentstructuremenu_dynamic.js), and the calendar of the date fields opens from a
 * button. The behaviour itself (arrow keys, Home, End, focus) is in the scripts; this test holds the markup and the
 * hooks they rely on. doc/eng-us/features/6.0/admin4-design.md.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminKeyboardTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminKeyboardTest extends PHPUnit\Framework\TestCase
{
    private static function file( $path )
    {
        return AdminFiles::read( dirname( __DIR__, 3 ) . '/' . $path );
    }

    public function testTheNodeTabsFollowTheTabsPattern()
    {
        $js = self::file( 'design/admin4/javascript/node_tabs.js' );
        foreach ( array( "'role', 'tablist'", "'role', 'tab'", "'aria-selected'", "'aria-controls'", "'role', 'tabpanel'",
                         "'aria-labelledby'", "'ArrowRight'", "'ArrowLeft'", "'Home'", "'End'", "addEventListener( 'keydown', NodeTab.keydown )" ) as $needle )
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
            // one name in both states; aria-pressed carries the state, the title what a press does
            $this->assertSame( 2, preg_match_all( '#<a id="maincontent-(?:show|hide)" class="show-hide-tabs"[^>]* role="button" aria-pressed="(?:true|false)" aria-label="\{\'Show tabs\'\|i18n\( \'design/admin/node/view/full\' \)\|wash\}" data-title-show="[^"]+" data-title-hide="[^"]+">#', $tpl ), $path );
        }
        $js = self::file( 'design/admin4/javascript/node_tabs.js' );
        $this->assertStringContainsString( "setAttribute( 'aria-pressed'", $js );
        $this->assertStringNotContainsString( "setAttribute( 'aria-label'", $js );
    }

    /**
     * The switch shows the state it saved: the server draws "with tabs" and "without tabs" with different markup, so
     * after saving the preference the page is loaded again (as the switch's link, a GET, always did); classes alone
     * left an empty view until a manual reload
     */
    public function testTheShowHideTabsToggleShowsTheSavedState()
    {
        $js = self::file( 'design/admin4/javascript/node_tabs.js' );
        $this->assertStringContainsString( "NodeTab.saveTabState( 1, link );", $js );
        $this->assertStringContainsString( "NodeTab.saveTabState( 0, link );", $js );
        $this->assertStringContainsString( "window.a4Pref.set( 'admin_navigation_content', String( intBool ), undefined, false, function( saved )", $js );
        $this->assertMatchesRegularExpression( '/saveTabState : function\( intBool, link \)[\s\S]{0,900}window\.location\.reload\(\)/', $js );
        $this->assertMatchesRegularExpression( '/saveTabState : function\( intBool, link \)[\s\S]{0,900}window\.location\.href = fallback/', $js );
        $classic = self::file( 'design/admin/javascript/node_tabs.js' );
        $this->assertMatchesRegularExpression( '/saveTabState : function\( intBool \)[\s\S]{0,600}\.always\( function\(\)\{ window\.location\.reload\(\); \} \)/', $classic );
    }

    /** With no selected tab the first tab is the Tab stop: the list never drops out of the Tab order */
    public function testATabListWithoutSelectionKeepsATabStop()
    {
        $js = self::file( 'design/admin4/javascript/node_tabs.js' );
        $this->assertStringContainsString( "if ( a.length && li.classList.contains( 'selected' ) ) noneSelected = false;", $js );
        $this->assertStringContainsString( "stop = selected || ( noneSelected && a === first );", $js );
        $this->assertStringContainsString( "a.setAttribute( 'tabindex', stop ? '0' : '-1' );", $js );
    }

    public function testTheTreeToggleIsANamedButtonWithItsState()
    {
        $js = self::file( 'design/admin4/javascript/contentstructuremenu_dynamic.js' );
        $this->assertStringContainsString( 'role="button" aria-expanded="false" aria-label="', $js );
        $this->assertStringContainsString( 'i18n.subitems', $js );
        // a node name with $& or $1 in it must not be read as a replacement pattern (name: item.name decoded once)
        $this->assertStringContainsString( ".split( '%name' ).join( name )", $js );
        // the button keeps the focus after a press, so the keyboard can press it again
        $this->assertSame( 1, preg_match( '#role="button" aria-expanded="false".*?<\\\\/a>#s', $js, $m ) );
        $this->assertStringNotContainsString( 'blur', $m[0] );
        // every change of the symbol goes through _toggleState, which keeps aria-expanded in step
        $this->assertSame( 0, preg_match_all( '/\.className\s*=\s*[\'"]openclose-/', $js ) );
        $this->assertGreaterThanOrEqual( 6, substr_count( $js, '_toggleState( aElement,' ) );
        $this->assertStringContainsString( "'subitems', 'Items below %name'", self::file( 'design/admin4/templates/contentstructuremenu/content_structure_menu_dynamic.tpl' ) );
    }

    /** The content tree is the same script and settings in the admin and admin4 designs */
    public function testTheTreeIsTheSameInAdminAndAdmin4()
    {
        foreach ( array( 'javascript/contentstructuremenu_dynamic.js', 'templates/contentstructuremenu/content_structure_menu_dynamic.tpl' ) as $path )
            $this->assertSame( self::file( "design/admin4/$path" ), self::file( "design/admin/$path" ), $path );
    }

    public function testTheCalendarOpensFromAButton()
    {
        foreach ( array( 'ezdate' => 'date', 'ezdatetime' => 'datetime' ) as $template => $kind )
        {
            $tpl = self::file( "design/admin4/templates/content/datatype/edit/$template.tpl" );
            $this->assertMatchesRegularExpression( '#<button type="button" class="a4-btn a4-btn-icon a4-content-datepicker-icon" id="\{\$attribute_base\}_' . $kind . '_cal_\{\$attribute\.id\}" aria-haspopup="dialog"[^>]* aria-label="#', $tpl, $template );
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
            $source = preg_replace( '/\{\*.*?\*\}/s', '', AdminFiles::read( $file->getPathname() ) );
            if ( preg_match( '/<img\b[^>]*\bonclick=/i', $source ) )
                $found[] = substr( $file->getPathname(), strlen( $base ) );
        }
        $this->assertSame( array(), $found );
    }
}
