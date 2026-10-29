<?php
/**
 * Every user/forgotpassword.tpl and user/password.tpl in the tree shows the demo mode notice (doc/eng-us/bc/6.0/demo-mode.md):
 * the kernel passes demo_mode_text to the forgot-password page and demo_mode_locked with demo_mode_text to the
 * password page while the environment variable EXP_DEMO_MODE=1 is set, and a design that overrides either page must
 * show it, or the visitors of a public demo never learn why an administrator password cannot be reset. Read from the
 * files; no database.
 *
 *  DMT-01  The kernel's designs (design/<name>/templates and override/templates): every such template uses
 *          exp_demo_mode() or the demo_mode_* variables, through first_set() so an older kernel still renders it
 *  DMT-02  The installed extensions' designs: the same rule. A missing notice fails for the root repository's own
 *          extensions and for every extension of NOTICE_RELEASES that composer.json requires at that release or
 *          later (what CI installs). Any other extension (not required, or required below the release with the
 *          notice) is reported as incomplete (listed), never silently passed
 *  DMT-03  No notice brings inline CSS (the admin design rules: no style attribute, no <style> block)
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group design
 */

class DemoModeUserTemplatesTest extends PHPUnit\Framework\TestCase
{
    /** The extensions of the root repository itself: a missing notice there is a failure, never incomplete */
    const ROOT_EXTENSIONS = array( 'expservices', 'ezformtoken', 'ezjscore', 'ezoe' );

    /**
     * The first release of each extension whose user/forgotpassword.tpl and user/password.tpl overrides show the
     * notice: extension directory => array( composer package, version ). Once composer.json requires that release
     * or a later one, a missing notice in the installed copy is a failure
     */
    const NOTICE_RELEASES = array(
        'cjw_themes_jumper'   => array( 'se7enxweb/cjw_themes_jumper', '0.1.7' ),
        'exp_adminui'         => array( 'se7enxweb/exp_adminui', '1.0.0.49' ),
        'ezdemo'              => array( 'se7enxweb/ezdemo', '6.0.13' ),
        'ezwebin'             => array( 'se7enxweb/ezwebin', '6.0.23' ),
        'sevenx_themes_media' => array( 'se7enxweb/sevenx-themes-media', '1.5.71' ),
    );

    /** The metapackage through which composer.json requires the packages that need PHP 8 */
    const PHP_FLOOR = 'se7enxweb/exponential-php-floor';

    private static function root()
    {
        return realpath( __DIR__ . '/../../..' );
    }

    /** @return array decoded "require" of a composer.json, empty when the file is missing or invalid */
    private static function readRequire( $file )
    {
        $json = is_file( $file ) ? json_decode( (string)file_get_contents( $file ), true ) : null;
        return is_array( $json ) && isset( $json['require'] ) && is_array( $json['require'] ) ? $json['require'] : array();
    }

    /**
     * @return array the packages composer.json requires: its own require, plus the require of the installed
     *         se7enxweb/exponential-php-floor version when the root requires that metapackage (the root's own entry wins)
     */
    private static function composerRequires()
    {
        static $require = null;
        if ( $require === null )
        {
            $require = self::readRequire( self::root() . '/composer.json' );
            if ( isset( $require[self::PHP_FLOOR] ) )
                $require += self::readRequire( self::root() . '/vendor/' . self::PHP_FLOOR . '/composer.json' );
        }
        return $require;
    }

    /**
     * @return bool whether $package is required through se7enxweb/exponential-php-floor, which is not installed here,
     *         so its version cannot be read in this tree (the metapackage's own releases carry it)
     */
    private static function onlyThroughUninstalledFloor( $package )
    {
        $require = self::composerRequires();
        return isset( $require[self::PHP_FLOOR] ) && !isset( $require[$package] )
            && !is_file( self::root() . '/vendor/' . self::PHP_FLOOR . '/composer.json' );
    }

    /**
     * @return string|null the lowest version composer.json's require allows for $package (the first version of its
     *         constraint, without ~ ^ >= = v), null when the package is not required or only as a branch
     */
    private static function requiredFloor( $package )
    {
        $require = self::composerRequires();
        if ( !isset( $require[$package] ) )
            return null;
        if ( !preg_match( '/^\s*(?:~|\^|>=|=)?\s*v?(\d+(?:\.\d+)*)/', $require[$package], $m ) )
            return null;
        return $m[1];
    }

    /** @return bool whether composer.json requires a release of $extension that shows the notice */
    private static function noticeRequired( $extension )
    {
        if ( !isset( self::NOTICE_RELEASES[$extension] ) )
            return false;
        list( $package, $since ) = self::NOTICE_RELEASES[$extension];
        $floor = self::requiredFloor( $package );
        return $floor !== null && version_compare( $floor, $since, '>=' );
    }

    /**
     * @param string $pattern glob below the root
     * @return array relative paths of user/forgotpassword.tpl and user/password.tpl found under $pattern
     */
    private static function templates( $pattern )
    {
        $root = self::root();
        $out = array();
        foreach ( array( 'forgotpassword.tpl', 'password.tpl' ) as $name )
        {
            foreach ( array( 'templates', 'override/templates' ) as $dir )
            {
                foreach ( glob( $root . '/' . $pattern . '/' . $dir . '/user/' . $name ) ?: array() as $file )
                    $out[] = substr( $file, strlen( $root ) + 1 );
            }
        }
        sort( $out );
        return $out;
    }

    /** @return string|null why the template does not show the notice, null when it does */
    private static function problem( $path )
    {
        $tpl = file_get_contents( self::root() . '/' . $path );
        $isForgot = substr( $path, -strlen( 'forgotpassword.tpl' ) ) === 'forgotpassword.tpl';
        $uses = strpos( $tpl, 'exp_demo_mode()' ) !== false
                || ( $isForgot ? strpos( $tpl, '$demo_mode_text' ) !== false
                               : ( strpos( $tpl, '$demo_mode_locked' ) !== false && strpos( $tpl, '$demo_mode_text' ) !== false ) );
        if ( !$uses )
            return $isForgot ? 'does not show $demo_mode_text' : 'does not show $demo_mode_locked / $demo_mode_text';
        if ( strpos( $tpl, 'exp_demo_mode()' ) === false && !preg_match( '/first_set\(\s*\$demo_mode_(text|locked)/', $tpl ) )
            return 'reads the demo_mode_* variables without first_set() (an older kernel does not set them)';
        return null;
    }

    // ------------------------------------------------------------------ DMT-01

    public function testTheKernelDesignsShowTheNotice()
    {
        $files = self::templates( 'design/*' );
        $this->assertContains( 'design/standard/templates/user/forgotpassword.tpl', $files );
        $this->assertContains( 'design/standard/templates/user/password.tpl', $files );
        $this->assertContains( 'design/admin4/templates/user/password.tpl', $files );
        foreach ( $files as $path )
            $this->assertNull( self::problem( $path ), $path );
    }

    // ------------------------------------------------------------------ DMT-02

    public function testTheExtensionDesignsShowTheNotice()
    {
        $files = self::templates( 'extension/*/design/*' );
        if ( !$files )
            $this->markTestSkipped( 'no extension in this tree overrides user/forgotpassword.tpl or user/password.tpl' );
        $pending = array();
        foreach ( $files as $path )
        {
            $problem = self::problem( $path );
            if ( $problem === null )
            {
                $this->addToAssertionCount( 1 );
                continue;
            }
            $extension = explode( '/', $path )[1];
            $this->assertNotContains( $extension, self::ROOT_EXTENSIONS, "$path: $problem" );
            if ( self::noticeRequired( $extension ) )
            {
                list( $package, $since ) = self::NOTICE_RELEASES[$extension];
                $this->fail( "$path: $problem (composer.json requires $package at $since or later, which shows the "
                             . 'notice: the installed copy is older than that release or was changed)' );
            }
            $pending[] = "$path: $problem";
        }
        if ( $pending )
            $this->markTestIncomplete( "Extension releases without the demo mode notice yet:\n" . implode( "\n", $pending ) );
    }

    /** DMT-02 is only as strict as composer.json: every release with the notice must still be what it requires */
    public function testComposerRequiresTheReleasesWithTheNotice()
    {
        foreach ( self::NOTICE_RELEASES as $extension => $release )
        {
            if ( self::onlyThroughUninstalledFloor( $release[0] ) )
            {
                $this->addToAssertionCount( 1 );
                continue;
            }
            $this->assertTrue( self::noticeRequired( $extension ),
                "composer.json must require {$release[0]} at {$release[1]} or later (the first release with the notice)" );
        }
    }

    // ------------------------------------------------------------------ DMT-03

    public function testTheNoticesBringNoInlineCss()
    {
        foreach ( array_merge( self::templates( 'design/*' ), self::templates( 'extension/*/design/*' ) ) as $path )
        {
            $tpl = file_get_contents( self::root() . '/' . $path );
            foreach ( preg_split( '/\R/', $tpl ) as $line )
            {
                if ( strpos( $line, 'demo_mode' ) === false )
                    continue;
                $this->assertDoesNotMatchRegularExpression( '/\sstyle\s*=|<style/i', $line, $path );
            }
        }
    }
}
?>
