<?php
/**
 * The newsletter edition's preview tabs ("HTML / Text", "Text") in the admin designs, checked on the files of
 * cjw_newsletter alone (no database):
 *
 * - every preview frame is the newsletter in a sandboxed iframe (no scripts, no same-origin access to the admin
 *   page), sized by a stylesheet class rather than width/height attributes or inline CSS, with a title;
 * - the frames are shown whenever their tab is, not hidden again when the user switched the node view's tabs off;
 * - the edition's tab row follows the kernel's window_controls.tpl: an unset tabs preference means tabs on, and
 *   each preview tab can be opened by its (tab)/preview_<id> in the URL;
 * - the preview is rendered with the PHP command line of the running PHP by default (PhpCli=auto): a bare "php"
 *   was the system PHP on a host whose web server ran a newer one, the child script stopped at Composer's
 *   platform check and the frame showed an admin "not found" page instead of the newsletter.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminNewsletterPreviewTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminNewsletterPreviewTest extends PHPUnit\Framework\TestCase
{
    const INCLUDES = array( 'design/admin2/templates/includes/cjwnewsletteredition_preview.tpl',
                            'design/admin2/templates/includes/cjwnewsletteredition_preview_archive.tpl',
                            'design/standard/templates/includes/cjwnewsletteredition_preview.tpl',
                            'design/standard/templates/includes/cjwnewsletteredition_preview_archive.tpl' );

    const CALLERS = array( 'design/admin2/templates/cjw_newsletter_edition_preview.tpl',
                           'design/admin2/templates/cjw_newsletter_edition_preview_archive.tpl',
                           'design/standard/templates/cjw_newsletter_edition_preview.tpl',
                           'design/standard/templates/cjw_newsletter_edition_preview_archive.tpl' );

    private static function dir()
    {
        return dirname( __DIR__, 3 ) . '/extension/cjw_newsletter';
    }

    private function read( $file )
    {
        if ( !is_dir( self::dir() ) )
            $this->markTestSkipped( 'cjw_newsletter is not installed' );
        $this->assertFileExists( self::dir() . '/' . $file );
        // template comments are not markup
        return preg_replace( '/\{\*.*?\*\}/s', '', (string)file_get_contents( self::dir() . '/' . $file ) );
    }

    public function testPreviewFramesAreSandboxedAndStyledByClass()
    {
        foreach ( self::INCLUDES as $file )
        {
            $tpl = $this->read( $file );
            preg_match_all( '/<iframe\b[^>]*>/i', $tpl, $frames );
            $this->assertNotEmpty( $frames[0], "$file has its preview frame" );
            foreach ( $frames[0] as $frame )
            {
                $this->assertMatchesRegularExpression( '/\sclass="cjwnl-preview-frame"/', $frame, "$file: frame class" );
                $this->assertMatchesRegularExpression( '/\ssandbox="[^"]*"/', $frame, "$file: the frame is sandboxed" );
                preg_match( '/\ssandbox="([^"]*)"/', $frame, $sandbox );
                $this->assertStringNotContainsString( 'allow-scripts', $sandbox[1], "$file: no scripts in the preview" );
                $this->assertStringNotContainsString( 'allow-same-origin', $sandbox[1], "$file: the preview cannot reach the admin page" );
                $this->assertMatchesRegularExpression( '/\stitle="/', $frame, "$file: the frame has a title" );
                $this->assertDoesNotMatchRegularExpression( '/\s(width|height|style)=/', $frame, "$file: size comes from the stylesheet" );
            }
        }
        $css = (string)file_get_contents( self::dir() . '/design/standard/stylesheets/cjw_newsletter.css' );
        $this->assertMatchesRegularExpression( '/\.cjwnl-preview-frame\s*\{[^}]*height:[^}]*\}/', $css, 'the frame class sets the size' );
        // the admin designs' div.tab-content is a fixed 20em box that scrolls; the preview panels grow with the frame
        $this->assertMatchesRegularExpression( '/div\.tab-content\.cjwnl-preview-panel\s*\{[^}]*height:\s*auto;[^}]*overflow-y:\s*visible;[^}]*\}/', $css );
        $windows = $this->read( 'design/admin2/templates/cjw_newsletter_edition_windows.tpl' );
        preg_match_all( '/<div id="node-tab-preview_\{\$output_format_id\}-content" class="([^"]*)"/', $windows, $panels );
        $this->assertCount( 2, $panels[1], 'the draft and the archive preview panels' );
        foreach ( $panels[1] as $class )
            $this->assertStringStartsWith( 'tab-content cjwnl-preview-panel', $class );
    }

    public function testFramesAreNotHiddenByTheTabsPreference()
    {
        foreach ( self::CALLERS as $file )
        {
            $this->assertStringNotContainsString( "admin_navigation_content", $this->read( $file ), "$file: the frames follow their tab" );
        }
    }

    public function testEditionTabsFollowTheKernelWindowControls()
    {
        $tpl = $this->read( 'design/admin2/templates/cjw_newsletter_edition_window_controls.tpl' );
        $this->assertStringNotContainsString( "ezpreference( 'admin_navigation_content' )|not", $tpl, 'an unset preference is not "tabs off"' );
        $this->assertStringContainsString( '{if $admin_navigation_content_pref|is_string}{set $tabs_disabled = $admin_navigation_content_pref|not}{/if}', $tpl );
        $this->assertMatchesRegularExpression( "/\\\$valid_tabs\|append\( concat\( 'preview_', \\\$output_format_id \) \)/", $tpl, 'the preview tabs can be opened by URL' );
        $this->assertLessThan( strpos( $tpl, '{if $valid_tabs|contains' ), strpos( $tpl, "concat( 'preview_', \$output_format_id ) )" ), 'the preview tabs are valid before the check' );
    }

    public function testPreviewIsRenderedWithTheRunningPhp()
    {
        $ini = $this->read( 'settings/cjw_newsletter.ini' );
        $this->assertMatchesRegularExpression( '/^PhpCli=auto$/m', $ini );
        $edition = (string)file_get_contents( self::dir() . '/classes/cjwnewsletteredition.php' );
        $this->assertStringContainsString( 'static function phpCli()', $edition );
        $this->assertStringContainsString( 'expProcessTools::phpCli()', $edition );
        $this->assertSame( 1, substr_count( $edition, "'PhpCli'" ), 'PhpCli is read in one place, phpCli()' );
        $list = (string)file_get_contents( self::dir() . '/classes/cjwnewsletterlist.php' );
        $this->assertStringNotContainsString( "'PhpCli'", $list );
        $this->assertStringContainsString( 'CjwNewsletterEdition::phpCli()', $list );
    }
}
