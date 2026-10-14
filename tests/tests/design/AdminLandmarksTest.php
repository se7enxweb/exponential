<?php
/**
 * admin4's frame gives screen readers and keyboards its landmarks and names: a skip link first in the body that jumps
 * to the main landmark, the header as banner, the top menu, side menu, breadcrumb and footer as their landmarks, and
 * one main landmark on every page, also on the edit-type pages that bring their own #maincontent. Header cells of
 * admin4's tables say whether they head a column or a row, and the controls the audit found without a name have one.
 * doc/features/6.0/admin4-design.md, doc/bc/6.0/admin4-styling.md.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminLandmarksTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminLandmarksTest extends PHPUnit\Framework\TestCase
{
    const TEMPLATES = 'design/admin4/templates';

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function template( $name )
    {
        return (string)file_get_contents( self::root() . '/' . self::TEMPLATES . '/' . $name );
    }

    /** Every template of admin4, path relative to the templates directory => source without {* comments *} */
    private static function templates()
    {
        $base = self::root() . '/' . self::TEMPLATES . '/';
        $all = array();
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            if ( substr( $file->getFilename(), -4 ) !== '.tpl' )
                continue;
            $source = preg_replace( '/\{\*.*?\*\}/s', '', (string)file_get_contents( $file->getPathname() ) );
            $all[substr( $file->getPathname(), strlen( $base ) )] = $source;
        }
        ksort( $all );
        return $all;
    }

    public function testTheSkipLinkIsTheFirstThingInTheBodyAndJumpsToTheMainLandmark()
    {
        $layout = preg_replace( '/\{\*.*?\*\}/s', '', self::template( 'pagelayout.tpl' ) );
        $this->assertMatchesRegularExpression( '#<body>\s*<a class="a4-skip-link" href="\#maincontent">#', $layout );
        $this->assertStringContainsString( '<div id="maincontent" role="main" tabindex="-1">', $layout );
    }

    public function testTheFrameHasItsLandmarks()
    {
        $layout = self::template( 'pagelayout.tpl' );
        $this->assertStringContainsString( '<div id="header" role="banner">', $layout );
        $this->assertMatchesRegularExpression( '#<div id="path" role="navigation" aria-label="#', $layout );
        $this->assertMatchesRegularExpression( '#<div id="rightmenu" class="sidebar right" role="complementary" aria-label="#', $layout );
        $this->assertStringContainsString( 'role="contentinfo"', $layout );
        $this->assertMatchesRegularExpression( '#<div id="leftmenu" class="sidebar left" role="navigation" aria-label="#', self::template( 'page_leftmenu.tpl' ) );
        $this->assertMatchesRegularExpression( '#class="navbar-menu[^"]*" role="navigation" aria-label="#', self::template( 'page_topmenu.tpl' ) );
    }

    /** The edit-type pages (content/edit, history, version view ...) bring #maincontent themselves */
    public function testEveryMaincontentIsTheMainLandmark()
    {
        $found = 0;
        foreach ( self::templates() as $name => $source )
        {
            preg_match_all( '/<div id="maincontent"[^>]*>/', $source, $m );
            foreach ( $m[0] as $tag )
            {
                ++$found;
                $this->assertStringContainsString( 'role="main"', $tag, $name );
                $this->assertStringContainsString( 'tabindex="-1"', $tag, $name );
            }
        }
        $this->assertGreaterThan( 5, $found );
    }

    public function testTheBreadcrumbMarksTheCurrentPage()
    {
        $path = self::template( 'page_toppath.tpl' );
        $this->assertSame( 2, substr_count( $path, 'aria-current="page"' ) );
        $this->assertStringContainsString( '<span class="slash" aria-hidden="true">', $path );
    }

    public function testTheTopMenuMarksTheCurrentEntryAndItsDisabledEntries()
    {
        $item = self::template( 'page_topmenuitem.tpl' );
        $this->assertStringContainsString( 'aria-current="page"', $item );
        $this->assertStringContainsString( 'aria-disabled="true" tabindex="-1"', $item );
        $menu = self::template( 'page_topmenu.tpl' );
        $this->assertMatchesRegularExpression( '#<div class="navbar-icon" role="button" tabindex="0" aria-expanded="false" aria-controls="a4-navbar-menu" aria-label="#', $menu );
        $this->assertSame( 2, preg_match_all( '#<a class="brand" [^>]*aria-label="#', $menu ) );
        $this->assertSame( 2, preg_match_all( '#<a class="site-preview" [^>]*aria-label="#', $menu ) );
    }

    /** A header cell says what it heads: scope="col" in a header row, scope="row" before the data of a row */
    public function testEveryHeaderCellHasAScope()
    {
        $missing = array();
        foreach ( self::templates() as $name => $source )
        {
            if ( preg_match_all( '/<th(?=[\s>])(?![^>]*\bscope=)/', $source, $m ) )
                $missing[] = $name . ' (' . count( $m[0] ) . ')';
        }
        $this->assertSame( array(), $missing, 'Header cells without scope' );
    }

    public function testFramesHaveATitle()
    {
        foreach ( self::templates() as $name => $source )
        {
            preg_match_all( '/<iframe\b[^>]*>/s', $source, $m );
            foreach ( $m[0] as $tag )
                $this->assertMatchesRegularExpression( '/\btitle="/', $tag, $name );
        }
    }

    /** The controls the audit found named by their title or position only */
    public function testTheseControlsHaveAName()
    {
        $this->assertMatchesRegularExpression( '#<select id="selected-section-id" name="SelectedSectionId" aria-label="#', self::template( 'details.tpl' ) );
        $ordering = self::template( 'ordering.tpl' );
        $this->assertMatchesRegularExpression( '#<select id="ezasi-sort-field"[^>]* aria-label="#', $ordering );
        $this->assertMatchesRegularExpression( '#<select id="ezasi-sort-order"[^>]* aria-label="#', $ordering );
        $this->assertSame( 2, substr_count( self::template( 'locations.tpl' ), 'name="LocationIDSelection[]" value="{$assignment_node.node_id}" aria-label="' ) );
        $this->assertSame( 3, substr_count( self::template( 'locations.tpl' ), 'name="MainAssignmentCheck" value="{$assignment_node.node_id}" aria-label="' ) );
        $translations = self::template( 'translations.tpl' );
        $this->assertStringContainsString( 'name="LanguageID[]" value="{$Translations.item.id}" aria-label="', $translations );
        $this->assertStringContainsString( 'name="InitialLanguageID" aria-label="', $translations );
        $this->assertSame( 3, preg_match_all( '#<label for="ezcoa-#', self::template( 'content/datatype/edit/ezdate.tpl' ) ) );
    }

    public function testTheSkipLinkComponentIsInBothComponentStylesheets()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $css = (string)file_get_contents( self::root() . "/design/$design/stylesheets/components.css" );
            $this->assertStringContainsString( '.a4-skip-link {', $css, $design );
            $this->assertStringContainsString( '.a4-skip-link:focus {', $css, $design );
        }
    }
}
