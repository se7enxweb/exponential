<?php
/**
 * Every user/forgotpassword.tpl and user/password.tpl in the tree shows the demo mode notice (doc/bc/6.0/demo-mode.md):
 * the kernel passes demo_mode_text to the forgot-password page and demo_mode_locked with demo_mode_text to the
 * password page while the environment variable EXP_DEMO_MODE=1 is set, and a design that overrides either page must
 * show it, or the visitors of a public demo never learn why an administrator password cannot be reset. Read from the
 * files; no database.
 *
 *  DMT-01  The kernel's designs (design/<name>/templates and override/templates): every such template uses
 *          exp_demo_mode() or the demo_mode_* variables, through first_set() so an older kernel still renders it
 *  DMT-02  The installed extensions' designs: the same rule. An extension that comes from its own repository and
 *          has not been released with the notice yet is reported as incomplete (listed), never silently passed
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

    private static function root()
    {
        return realpath( __DIR__ . '/../../..' );
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
            $pending[] = "$path: $problem";
        }
        if ( $pending )
            $this->markTestIncomplete( "Extension releases without the demo mode notice yet:\n" . implode( "\n", $pending ) );
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
