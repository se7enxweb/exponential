<?php
/**
 * admin4's page header and breadcrumb: one title style for every page (.a4-pagehead-title, the kernel's
 * h1.context-title, the node view, the dashboard), the node view's header row built from .a4-pagehead, and the
 * breadcrumb as an ordered list in the #path navigation landmark, its separators drawn by the stylesheet, the
 * current page marked with aria-current and the names no longer cut to 18 characters.
 * doc/features/6.0/admin4-design.md, doc/bc/6.0/admin4-styling.md.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminPageHeaderTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminPageHeaderTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        $file = self::root() . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    public function testComponentsDefineThePageHeaderAndTheBreadcrumb()
    {
        foreach ( array( 'design/admin4/stylesheets/components.css', 'design/admin/stylesheets/components.css' ) as $path )
        {
            $css = self::read( $path );
            foreach ( array( '.a4-pagehead {', '.a4-pagehead-main', '.a4-pagehead-pre', '.a4-pagehead-title {',
                             '.a4-pagehead-sub', '.a4-pagehead-actions {', '.a4-crumbs {', '.a4-crumbs > li + li::before',
                             '.a4-crumbs > li.is-fold', '.a4-crumbs > li.is-middle' ) as $selector )
                $this->assertStringContainsString( $selector, $css, "$path: $selector" );
            // the separator is not read out (content with an empty alternative text)
            $this->assertStringContainsString( 'content: "/" / "";', $css, $path );
            // folded middle steps stay for screen readers and come back when they take the focus
            $this->assertStringContainsString( '.a4-crumbs > li.is-middle:not(:focus-within) {', $css, $path );
            $this->assertDoesNotMatchRegularExpression( '/li\.is-middle[^{]*\{[^}]*display: none/', $css, $path );
            // the title takes the type scale, not a size of its own
            $this->assertMatchesRegularExpression( '/\.a4-pagehead-title \{[^}]*font-size: var\(--a4-text-xl\);/', $css, $path );
        }
    }

    public function testBreadcrumbIsAnOrderedListWithoutShortenedNames()
    {
        $tpl = self::read( 'design/admin4/templates/page_toppath.tpl' );
        $this->assertStringNotContainsString( 'shorten(', $tpl, 'names are no longer cut to 18 characters' );
        $this->assertStringNotContainsString( 'class="slash"', $tpl, 'the separators come from the stylesheet' );
        $this->assertSame( 3, substr_count( $tpl, 'aria-current="page"' ), 'the last step, link, text or disabled while editing, is the current page' );
        $this->assertGreaterThanOrEqual( 3, substr_count( $tpl, 'title="{$:item.text|wash}"' ), 'a long name keeps its full name as title' );
        $this->assertStringContainsString( 'is-fold" aria-hidden="true"', $tpl );
        // Safari keeps the list semantics only with an explicit role; the fold appears from three steps on; no empty list
        $this->assertStringContainsString( '<ol class="a4-crumbs" role="list">', $tpl );
        $this->assertStringContainsString( 'eq( $:number, 1 ), gt( $:crumb_count, 2 )', $tpl );
        $this->assertStringContainsString( '{if gt( $:crumb_count, 0 )}', $tpl );

        $layout = self::read( 'design/admin4/templates/pagelayout.tpl' );
        $this->assertStringContainsString( '<div id="path" role="navigation" aria-label="{\'Breadcrumb\'|i18n( \'design/admin/pagelayout\' )|wash}">', $layout );
    }

    public function testOneTitleStyleForEveryPage()
    {
        $css = self::read( 'design/admin4/stylesheets/admin4.css' );
        $this->assertMatchesRegularExpression( '/(^|\n)h1\.context-title \{[^}]*font-size: var\(--a4-text-xl\);/', $css );
        $this->assertMatchesRegularExpression( '/\.a4-dash-hero h1 \{[^}]*font-size: var\(--a4-text-xl\);/', $css );
        $this->assertDoesNotMatchRegularExpression( '/\.a4-node-head h1\.context-title \{[^}]*font-size/', $css,
            'the node view takes the common title size' );
        // one breakpoint for the smaller title, the one of .a4-pagehead-title
        $this->assertMatchesRegularExpression( '/@media \(max-width: 600px\) \{\s*h1\.context-title,\s*\.a4-dash-hero h1 \{ font-size: var\(--a4-text-l\); \}/', $css );
        // the old #path span colour beat the breadcrumb's own colours
        $this->assertStringNotContainsString( '#path span {', self::read( 'design/admin4/stylesheets/pagelayout.css' ) );

        $node = self::read( 'design/admin4/templates/node/view/a4_full_body.tpl' );
        foreach ( array( 'a4-node-head a4-pagehead', 'a4-node-heading a4-pagehead-main', 'context-title a4-pagehead-title',
                         'a4-node-actions a4-pagehead-actions' ) as $class )
            $this->assertStringContainsString( $class, $node );
    }

    /**
     * The logo's name in the header says the release to signed-in users only: anonymous pages of the admin (the 401
     * page, user/register, user/forgotpassword) draw the same frame. The header's cache blocks are keyed by whether
     * the user is signed in, so the two names are never served from the same cache entry.
     */
    public function testTheVersionIsShownToSignedInUsersOnly()
    {
        foreach ( array( 'design/admin4/templates/page_topmenu.tpl', 'design/admin4l/templates/explayouts/block/admin_logo.tpl' ) as $path )
        {
            $tpl = preg_replace( '/\{\*.*?\*\}/s', '', self::read( $path ) );
            $this->assertSame( 1, substr_count( $tpl, "fetch( 'setup', 'version' )" ), "$path: the version is fetched once, for the brand name" );
            $this->assertStringContainsString( '{def $a4_brand_name = $current_user.is_logged_in|choose( \'Exponential\', concat( \'Exponential \', fetch( \'setup\', \'version\' ) ) )}', $tpl, $path );
            $this->assertSame( 2, substr_count( $tpl, 'title="{$a4_brand_name|wash}" aria-label="{$a4_brand_name|wash}"' ), $path );
        }
        foreach ( array( 'design/admin4/templates/pagelayout.tpl', 'design/admin4l/templates/pagelayout.tpl',
                         'design/admin/templates/pagelayout.tpl', 'design/admin3/templates/pagelayout.tpl' ) as $path )
            $this->assertMatchesRegularExpression( '/\$user_hash = concat\([^\n]*\$current_user\.is_logged_in\|choose\( \'anonymous\', \'user\' \) \)/', self::read( $path ), $path );
        // the footer's share link and the headers of admin and admin3 do the same
        foreach ( array( 'design/admin4/templates/page_copyright.tpl', 'design/admin/templates/page_copyright.tpl' ) as $path )
        {
            $tpl = self::read( $path );
            $this->assertSame( 1, substr_count( $tpl, "fetch( 'setup', 'version' )" ), $path );
            $this->assertStringContainsString( "fetch( 'user', 'current_user' ).is_logged_in|choose( '', concat( '+', fetch( 'setup', 'version' ) ) )", $tpl, $path );
        }
        foreach ( array( 'design/admin/templates/page_header.tpl' => "{if fetch( 'user', 'current_user' ).is_logged_in} {fetch( 'setup', 'version' )}{/if}",
                         'design/admin3/templates/page_topmenu.tpl' => "{if \$current_user.is_logged_in} {fetch( 'setup', 'version' )}{/if}" ) as $path => $guard )
        {
            $tpl = preg_replace( '/\{\*.*?\*\}/s', '', self::read( $path ) );
            $this->assertSame( substr_count( $tpl, "fetch( 'setup', 'version' )" ), substr_count( $tpl, $guard ), "$path: every version is guarded" );
            $this->assertGreaterThan( 0, substr_count( $tpl, $guard ), $path );
        }
    }
}
