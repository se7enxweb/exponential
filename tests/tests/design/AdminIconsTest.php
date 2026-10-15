<?php
/**
 * admin4 draws its icons with Bootstrap Icons: the bootstrap-icons icon theme (share/icons/bootstrap-icons, for
 * class_icon, mimetype_icon, classgroup_icon and icon), the .a4-icon component (components.css) and the sprite
 * replacements in admin4's stylesheets all point at the same SVG files. A name in icon.ini, a class or a url() that
 * has no file shows as a broken image or an empty square; a file nothing names is dead weight; a changed file is no
 * longer the one its licence covers. Templates of admin4 draw no GIF icon, no inline SVG and no image button.
 * doc/features/6.0/admin4-design.md, doc/bc/6.0/admin4-styling.md.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminIconsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminIconsTest extends PHPUnit\Framework\TestCase
{
    const THEME = 'share/icons/bootstrap-icons';

    /** The design images admin4's templates drew with ezimage before they took .a4-icon */
    const RETIRED_IMAGES = array( 'toggle-button-16x16.gif', 'edit.gif', 'edit-disabled.gif', 'up-16x16-grey.png', 'move.gif',
                                  'button-move_up.gif', 'button-move_down.gif', 'trash-icon-16x16.gif', 'current-user.gif',
                                  'current-user-disabled.gif' );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function svgFiles()
    {
        $names = array();
        foreach ( glob( self::root() . '/' . self::THEME . '/svg/*.svg' ) ?: array() as $file )
            $names[] = basename( $file );
        sort( $names );
        return $names;
    }

    /** The values of icon.ini that name a file: Default and the *Map[] entries of every block */
    private static function iniFiles()
    {
        $names = array();
        foreach ( file( self::root() . '/' . self::THEME . '/icon.ini' ) as $line )
        {
            if ( preg_match( '/^(?:Default|\w+Map\[[^\]]*\])=(\S+\.svg)\s*$/', $line, $m ) )
                $names[] = $m[1];
        }
        return array_values( array_unique( $names ) );
    }

    /** Every stylesheet of admin4 and admin, with the theme's SVGs its url()s name, resolved from the stylesheet */
    private static function stylesheetUrls()
    {
        $found = array();
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            foreach ( glob( self::root() . "/design/$design/stylesheets/{,*/}*.css", GLOB_BRACE ) ?: array() as $css )
            {
                preg_match_all( '#url\(\s*[\'"]?([^\'")]*bootstrap-icons/svg/[^\'")]+)[\'"]?\s*\)#', (string)file_get_contents( $css ), $m );
                foreach ( $m[1] as $url )
                    $found[] = array( $css, $url );
            }
        }
        return $found;
    }

    private static function adminTemplates()
    {
        // admin4l (the admin layouts) draws its header from its own blocks, copies of admin4's page_topmenu.tpl
        $files = array();
        foreach ( array( 'design/admin4/templates', 'design/admin4l/templates' ) as $dir )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . "/$dir", FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( substr( $file->getFilename(), -4 ) === '.tpl' )
                    $files[] = $file->getPathname();
            }
        }
        sort( $files );
        return $files;
    }

    public function testTheThemeShipsItsLicence()
    {
        $licence = self::root() . '/' . self::THEME . '/LICENSE';
        $this->assertFileExists( $licence );
        $this->assertStringContainsString( 'The MIT License', (string)file_get_contents( $licence ) );
    }

    public function testEveryFileTheThemeNamesExists()
    {
        $missing = array_values( array_diff( self::iniFiles(), self::svgFiles() ) );
        $this->assertNotEmpty( self::iniFiles() );
        $this->assertSame( array(), $missing, 'icon.ini names files that are not in svg/' );
    }

    /**
     * The crystal themes that admin4 falls back to (AdditionalThemeList[]) and the classic designs use: every file
     * icon.ini names is there in every size of [IconSettings] Sizes[] (a missing size is a broken image wherever that
     * size is asked for), and every MimeMap key is one MIME type (type/subtype) or a top-level type alone
     */
    public function testEveryFileOfTheCrystalThemesIsThereInEverySize()
    {
        $problems = array();
        foreach ( array( 'share/icons/crystal-admin', 'share/icons/crystal' ) as $theme )
        {
            $ini = (string)file_get_contents( self::root() . "/$theme/icon.ini" );
            preg_match_all( '/^Sizes\[\w+\]=(\S+)\s*$/m', $ini, $sizes );
            preg_match_all( '/^(?:Default|\w+Map\[[^\]]*\])=(\S+)\s*$/m', $ini, $files );
            $this->assertNotEmpty( $sizes[1], "$theme: no sizes" );
            foreach ( $sizes[1] as $size )
            {
                foreach ( array_unique( $files[1] ) as $file )
                {
                    if ( !is_file( self::root() . "/$theme/$size/$file" ) )
                        $problems[] = "$theme/$size/$file";
                }
            }
            preg_match_all( '/^MimeMap\[([^\]]*)\]=/m', $ini, $types );
            foreach ( $types[1] as $type )
            {
                if ( !preg_match( '#^[a-z0-9][\w.+-]*(?:/[\w.+-]+)?$#i', $type ) )
                    $problems[] = "$theme: MimeMap[$type] is not one MIME type or top-level type";
            }
        }
        $this->assertMatchesRegularExpression( '/^ClassMap\[job\]=apps\/job\.png$/m', (string)file_get_contents( self::root() . '/share/icons/crystal-admin/icon.ini' ) );
        $this->assertSame( array(), $problems, 'The crystal icon themes name files or types that do not exist' );
    }

    public function testEveryUrlOfTheStylesheetsResolves()
    {
        $broken = array();
        foreach ( self::stylesheetUrls() as list( $css, $url ) )
        {
            if ( !is_file( dirname( $css ) . '/' . $url ) )
                $broken[] = substr( $css, strlen( self::root() ) + 1 ) . ': ' . $url;
        }
        $this->assertNotEmpty( self::stylesheetUrls() );
        $this->assertSame( array(), $broken, 'Stylesheets name SVGs that their relative path does not reach' );
    }

    public function testEveryIconClassHasItsFileAndEveryUsedClassExists()
    {
        $components = (string)file_get_contents( self::root() . '/design/admin4/stylesheets/components.css' );
        preg_match_all( '/^\.a4-bi-([a-z0-9-]+) \{[^}]*url\([^)]*\/svg\/([a-z0-9-]+)\.svg\)/m', $components, $m, PREG_SET_ORDER );
        $defined = array();
        foreach ( $m as $rule )
        {
            $this->assertSame( $rule[1], $rule[2], ".a4-bi-{$rule[1]} draws another file" );
            $this->assertFileExists( self::root() . '/' . self::THEME . "/svg/{$rule[1]}.svg" );
            $defined[] = $rule[1];
        }

        $this->assertGreaterThan( 30, count( $defined ), 'The .a4-bi-* rules of components.css were not read' );

        $unknown = array();
        foreach ( self::adminTemplates() as $file )
        {
            preg_match_all( '/\ba4-bi-([a-z0-9-]+)/', (string)file_get_contents( $file ), $used );
            foreach ( array_diff( $used[1], $defined ) as $name )
                $unknown[] = substr( $file, strlen( self::root() ) + 1 ) . ': a4-bi-' . $name;
        }
        $this->assertSame( array(), $unknown, 'Templates use icon classes components.css does not define' );
    }

    public function testNoFileIsLeftOver()
    {
        $named = self::iniFiles();
        foreach ( self::stylesheetUrls() as list( , $url ) )
            $named[] = basename( $url );
        $this->assertSame( array(), array_values( array_diff( self::svgFiles(), $named ) ), 'SVGs in the theme that nothing draws' );
    }

    public function testEveryFileIsAnUnchangedBootstrapIcon()
    {
        foreach ( self::svgFiles() as $name )
        {
            $svg = (string)file_get_contents( self::root() . '/' . self::THEME . '/svg/' . $name );
            $icon = substr( $name, 0, -4 );
            $this->assertStringStartsWith( '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-' . $icon . '" viewBox="0 0 16 16">', $svg, $name );
            $this->assertDoesNotMatchRegularExpression( '/<script|\son\w+\s*=|href\s*=|<foreignObject/i', $svg, $name );
        }
    }

    public function testAdminFourTemplatesDrawNoRetiredIcon()
    {
        $found = array();
        $images = implode( '|', array_map( 'preg_quote', self::RETIRED_IMAGES ) );
        foreach ( self::adminTemplates() as $file )
        {
            // template comments {* ... *} are not read
            $text = preg_replace( '/\{\*.*?\*\}/s', '', (string)file_get_contents( $file ) );
            $short = substr( $file, strlen( self::root() ) + 1 );
            if ( preg_match( "/'(?:$images)'\|ezimage/", $text ) )
                $found[] = "$short: a GIF or PNG icon (use .a4-icon)";
            if ( stripos( $text, '<svg' ) !== false )
                $found[] = "$short: an inline SVG (use .a4-icon)";
            if ( preg_match( '/type="image"/', $text ) )
                $found[] = "$short: an image button (use a <button> with .a4-icon-btn)";
        }
        $this->assertSame( array(), $found );
    }

    /**
     * admin4l's header blocks are copies of parts of admin4's page_topmenu.tpl ("keep in step"): every line of
     * their markup is in page_topmenu.tpl, so a change of the icons or names there cannot leave admin4l behind.
     * The tab menu block keeps a plain .navbar-menu wrapper; the zone's wrapper in admin4l's page layout is the landmark.
     */
    public function testAdmin4lHeaderBlocksAreInStepWithTheTopMenu()
    {
        $top = (string)file_get_contents( self::root() . '/design/admin4/templates/page_topmenu.tpl' );
        foreach ( array( 'admin_logo', 'admin_search', 'admin_theme_switch', 'admin_sidebar_toggles', 'admin_tab_menu' ) as $block )
        {
            $lines = file( self::root() . "/design/admin4l/templates/explayouts/block/$block.tpl", FILE_IGNORE_NEW_LINES );
            foreach ( $lines as $line )
            {
                if ( trim( $line ) === '' || strpos( $line, '{* Admin block (explayouts)' ) === 0 || strpos( $line, '{if is_unset( $ui_context_edit )}' ) === 0 )
                    continue;
                if ( $block === 'admin_tab_menu' && trim( $line ) === '<div class="navbar-menu {if $ui_context_edit}disabled{/if}">' )
                    continue;
                $this->assertStringContainsString( $line, $top, "design/admin4l/templates/explayouts/block/$block.tpl is not in step with page_topmenu.tpl" );
            }
        }
    }
}
