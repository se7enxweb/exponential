<?php
/**
 * The no-inline-CSS rule of the admin designs, extended to the front designs (doc/eng-us/bc/6.0/admin4-styling.md, "Front designs"),
 * checked on the files alone; no database, no installation.
 *
 * - No <style> block and no style="" attribute in the templates of the front designs: every design/<name> that is
 *   not an admin design (AdminStylingRulesTest::DESIGNS) and every extension/<ext>/design/<name> of this repository
 *   whose name does not start with "admin". Only the templates this repository tracks count (git ls-files): extensions
 *   Composer installs into extension/ are packages of their own, with their own CI. Without a git checkout, the
 *   extensions listed in vendor/composer/installed.json are left out instead. Template comments {* ... *} are not read.
 * - The templates that still carry inline CSS are in ALLOWLIST, which only shrinks: a listed file that has no inline
 *   CSS any more, or no longer exists, fails the test until it is taken off the list. A new template, or a template
 *   not on the list, with inline CSS fails the test. Never add an entry to make a change pass: move the CSS to the
 *   design's stylesheets (a per-element value goes in a data-* attribute or a class).
 *
 * Run: php vendor/bin/phpunit tests/tests/design/FrontStylingRulesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';
require_once __DIR__ . '/AdminStylingRulesTest.php';

class FrontStylingRulesTest extends PHPUnit\Framework\TestCase
{
    /**
     * Front templates that carried inline CSS when the rule was extended to the front designs (2026-11-26). This
     * list only shrinks; it is empty when the work is done.
     */
    const ALLOWLIST = array(
        'design/base/override/templates/collectedinfo/poll_result.tpl',
        'design/base/override/templates/embed/image.tpl',
        'design/base/templates/loginpagelayout.tpl',
        'design/base/templates/pagelayout.tpl',
        'design/mysite/override/templates/embed_image.tpl',
        'design/plain/templates/pagelayout.tpl',
        'design/standard/templates/class/classlist.tpl',
        'design/standard/templates/class/grouplist.tpl',
        'design/standard/templates/content/browse.tpl',
        'design/standard/templates/content/datatype/view/ezimage.tpl',
        'design/standard/templates/content/datatype/view/ezxmltags/table.tpl',
        'design/standard/templates/content/datatype/view/ezxmltags/td.tpl',
        'design/standard/templates/content/edit.tpl',
        'design/standard/templates/content/job.tpl',
        'design/standard/templates/content/job_mode_choice.tpl',
        'design/standard/templates/content/job_summary.tpl',
        'design/standard/templates/content/jobs.tpl',
        'design/standard/templates/error/parts/formtoken.tpl',
        'design/standard/templates/ezinfo/about.tpl',
        'design/standard/templates/loginpagelayout.tpl',
        'design/standard/templates/mail/layout.tpl',
        'design/standard/templates/mailpreferences/mail/footer_html.tpl',
        'design/standard/templates/package/create/info.tpl',
        'design/standard/templates/package/creators/ezextension/extension.tpl',
        'design/standard/templates/package/navigator.tpl',
        'design/standard/templates/page_head_displaystyles.tpl',
        'design/standard/templates/pagelayout.tpl',
        'design/standard/templates/role/view.tpl',
        'design/standard/templates/setup/init/navigation.tpl',
        'design/standard/templates/setup/init/site_details.tpl',
        'design/standard/templates/setup/page_copyright.tpl',
        'design/standard/templates/setup_pagelayout.tpl',
        'design/standard/templates/shop/orderreceipt_document.tpl',
        'design/standard/templates/shop/orderreceipt_style.tpl',
        'design/standard/templates/state/edit.tpl',
        'design/standard/templates/state/group_edit.tpl',
        'design/standard/templates/user/edit.tpl',
        'design/standard/templates/user/login.tpl',
        'design/standard/templates/visual/templateedit.tpl',
        'extension/ezoe/design/standard/templates/ezoe/box_bookmarks.tpl',
        'extension/ezoe/design/standard/templates/ezoe/box_browse.tpl',
        'extension/ezoe/design/standard/templates/ezoe/box_search.tpl',
        'extension/ezoe/design/standard/templates/ezoe/customattributes.tpl',
        'extension/ezoe/design/standard/templates/ezoe/customattributes/color.tpl',
        'extension/ezoe/design/standard/templates/ezoe/customattributes/cssborder.tpl',
        'extension/ezoe/design/standard/templates/ezoe/help.tpl',
        'extension/ezoe/design/standard/templates/ezoe/merge_cells.tpl',
        'extension/ezoe/design/standard/templates/ezoe/popup_pagelayout.tpl',
        'extension/ezoe/design/standard/templates/ezoe/tag_anchor.tpl',
        'extension/ezoe/design/standard/templates/ezoe/tag_custom.tpl',
        'extension/ezoe/design/standard/templates/ezoe/tag_embed_files.tpl',
        'extension/ezoe/design/standard/templates/ezoe/tag_embed_images.tpl',
        'extension/ezoe/design/standard/templates/ezoe/tag_embed_objects.tpl',
        'extension/ezoe/design/standard/templates/ezoe/tag_general.tpl',
        'extension/ezoe/design/standard/templates/ezoe/tag_header.tpl',
        'extension/ezoe/design/standard/templates/ezoe/tag_link.tpl',
        'extension/ezoe/design/standard/templates/ezoe/tag_table.tpl',
        'extension/ezoe/design/standard/templates/ezoe/tag_table_cell.tpl',
        'extension/ezoe/design/standard/templates/ezoe/upload/common_attributes.tpl',
        'extension/ezoe/design/standard/templates/ezoe/upload_files.tpl',
        'extension/ezoe/design/standard/templates/ezoe/upload_images.tpl',
        'extension/ezoe/design/standard/templates/ezoe/upload_objects.tpl',
    );

    private static $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 3 );
    }

    /**
     * Returns the template directories of the front designs (paths from the root)
     */
    public static function frontDesignDirectories()
    {
        $dirs = array();
        foreach ( (array)glob( self::$root . '/design/*', GLOB_ONLYDIR ) as $dir )
        {
            if ( !in_array( basename( $dir ), AdminStylingRulesTest::DESIGNS, true ) )
            {
                $dirs[] = substr( $dir, strlen( self::$root ) + 1 );
            }
        }
        $installed = self::composerInstalledExtensions();
        foreach ( (array)glob( self::$root . '/extension/*/design/*', GLOB_ONLYDIR ) as $dir )
        {
            if ( strpos( basename( $dir ), 'admin' ) !== 0 && !isset( $installed[basename( dirname( dirname( $dir ) ) )] ) )
            {
                $dirs[] = substr( $dir, strlen( self::$root ) + 1 );
            }
        }
        sort( $dirs );
        return $dirs;
    }

    /**
     * Returns the extensions Composer installed into extension/ (name => true). They are packages of their own, with
     * their own CI, and not files of this repository: a checkout with its dependencies installed (CI, a site) holds
     * them next to the extensions this repository ships, which are the ones this test checks.
     */
    public static function composerInstalledExtensions()
    {
        $names = array();
        $file = self::$root . '/vendor/composer/installed.json';
        $data = is_file( $file ) ? json_decode( (string)file_get_contents( $file ), true ) : null;
        $packages = is_array( $data ) ? ( isset( $data['packages'] ) ? $data['packages'] : $data ) : array();
        foreach ( $packages as $package )
        {
            if ( !is_array( $package ) || !isset( $package['install-path'] ) )
            {
                continue;
            }
            $path = realpath( self::$root . '/vendor/composer/' . $package['install-path'] );
            if ( $path !== false && dirname( $path ) === realpath( self::$root . '/extension' ) )
            {
                $names[basename( $path )] = true;
            }
        }
        return $names;
    }

    /**
     * Returns the front templates (paths from the root) that carry inline CSS
     */
    /**
     * Returns the .tpl files this repository tracks (path => true), from git ls-files; null without a git checkout
     * (an archive), where the scan falls back to leaving out the extensions Composer installed.
     */
    public static function trackedTemplates()
    {
        if ( !is_dir( self::$root . '/.git' ) && !is_file( self::$root . '/.git' ) )
        {
            return null;
        }
        $out = array();
        $code = 1;
        exec( 'git -C ' . escapeshellarg( self::$root ) . " ls-files -z -- '*.tpl' 2>/dev/null", $out, $code );
        if ( $code !== 0 )
        {
            return null;
        }
        $tracked = array();
        foreach ( explode( "\0", implode( "\n", $out ) ) as $path )
        {
            if ( $path !== '' )
            {
                $tracked[$path] = true;
            }
        }
        return $tracked;
    }

    private static function templatesWithInlineCss()
    {
        $tracked = self::trackedTemplates();
        $found = array();
        foreach ( self::frontDesignDirectories() as $dir )
        {
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::$root . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $files as $file )
            {
                $path = substr( $file->getPathname(), strlen( self::$root ) + 1 );
                if ( substr( $file->getFilename(), -4 ) === '.tpl' && ( $tracked === null || isset( $tracked[$path] ) )
                     && AdminStylingRulesTest::inlineCssCount( AdminFiles::read( $file->getPathname() ) ) > 0 )
                {
                    $found[] = $path;
                }
            }
        }
        sort( $found );
        return $found;
    }

    public function testTheFrontDesignsAreScanned()
    {
        $dirs = self::frontDesignDirectories();
        foreach ( array( 'design/standard', 'design/base', 'extension/ezoe/design/standard' ) as $expected )
        {
            $this->assertContains( $expected, $dirs );
        }
        foreach ( $dirs as $dir )
        {
            $this->assertStringNotContainsString( 'design/admin', $dir, 'admin designs are AdminStylingRulesTest\'s' );
        }
    }

    public function testFrontTemplatesCarryNoNewInlineCss()
    {
        $new = array_values( array_diff( self::templatesWithInlineCss(), self::ALLOWLIST ) );
        $this->assertSame( array(), $new,
            "These front templates have a <style> block or a style=\"\" attribute. Move the CSS to the design's " .
            "stylesheets (a per-element value goes in a data-* attribute or a class); never add them to ALLOWLIST." );
    }

    public function testAllowlistOnlyShrinks()
    {
        $stale = array_values( array_diff( self::ALLOWLIST, self::templatesWithInlineCss() ) );
        $this->assertSame( array(), $stale,
            'These templates no longer carry inline CSS (or are gone): take them off FrontStylingRulesTest::ALLOWLIST.' );
        $this->assertSame( count( self::ALLOWLIST ), count( array_unique( self::ALLOWLIST ) ), 'ALLOWLIST lists a file twice' );
    }
}
