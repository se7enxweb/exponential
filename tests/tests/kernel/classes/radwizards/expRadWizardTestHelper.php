<?php
/**
 * Shared by the tests of the Setup > RAD wizards: the working directory, a scratch directory under var/tmp, the
 * cache directory pointed there for the length of a test, and checks of generated files (php that parses, ini files
 * that are one php comment, nothing typed reaching code).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class expRadWizardTestINI extends eZINI
{
    public static function injected()
    {
        return self::$injectedSettings;
    }
}

class expRadWizardTestHelper
{
    public static function root()
    {
        return dirname( __DIR__, 5 );
    }

    public static function boot()
    {
        chdir( self::root() );
        eZExecution::registerShutdownHandler();
    }

    /**
     * A new, empty directory under var/tmp.
     *
     * @return string absolute path
     */
    public static function scratch( $label )
    {
        $dir = self::root() . '/var/tmp/k1e-' . $label . '-' . getmypid() . '-' . mt_rand();
        mkdir( $dir, 0777, true );
        return $dir;
    }

    public static function removeTree( $path )
    {
        if ( is_link( $path ) || is_file( $path ) )
        {
            unlink( $path );
            return;
        }
        if ( !is_dir( $path ) )
            return;
        foreach ( scandir( $path ) as $entry )
        {
            if ( $entry !== '.' && $entry !== '..' )
                self::removeTree( $path . '/' . $entry );
        }
        rmdir( $path );
    }

    /**
     * Injects site.ini settings for one test; returns what was injected before, for restoreInjected().
     */
    public static function injectSiteIni( array $blocks )
    {
        $before = expRadWizardTestINI::injected();
        $settings = $before;
        foreach ( $blocks as $block => $values )
            foreach ( $values as $name => $value )
                $settings['site.ini'][$block][$name] = $value;
        eZINI::injectSettings( $settings );
        return $before;
    }

    public static function restoreInjected( $before )
    {
        eZINI::injectSettings( $before );
    }

    public static function assertPhpParses( PHPUnit\Framework\TestCase $test, $code, $label )
    {
        try
        {
            token_get_all( $code, TOKEN_PARSE );
        }
        catch ( ParseError $e )
        {
            $test->fail( "$label does not parse: " . $e->getMessage() . "\n" . $code );
        }
        $test->addToAssertionCount( 1 );
    }

    /**
     * The php names a file calls or mentions outside comments and strings.
     */
    public static function codeWords( $code )
    {
        $words = array();
        foreach ( token_get_all( $code ) as $token )
        {
            if ( is_array( $token ) && $token[0] === T_STRING )
                $words[] = strtolower( $token[1] );
        }
        return $words;
    }

    /**
     * Checks every generated file: php parses, an ini file is one comment, nothing called $marker became code.
     */
    public static function assertFilesAreSafe( PHPUnit\Framework\TestCase $test, array $files, $marker = 'k1e_injected' )
    {
        foreach ( $files as $path => $contents )
        {
            $test->assertIsString( $contents, $path );
            if ( substr( $path, -4 ) === '.php' )
            {
                self::assertPhpParses( $test, $contents, $path );
                $test->assertNotContains( strtolower( $marker ), self::codeWords( $contents ), "$path runs typed text" );
            }
            if ( substr( $path, -15 ) === '.ini.append.php' || substr( $path, -8 ) === '.ini.php' )
            {
                $tokens = token_get_all( $contents );
                $kinds = array();
                foreach ( $tokens as $token )
                {
                    if ( is_array( $token ) && $token[0] !== T_WHITESPACE )
                        $kinds[] = token_name( $token[0] );
                    else if ( !is_array( $token ) )
                        $kinds[] = $token;
                }
                $test->assertSame( 'T_OPEN_TAG', $kinds[0], "$path starts with <?php" );
                foreach ( array_slice( $kinds, 1 ) as $kind )
                    $test->assertContains( $kind, array( 'T_COMMENT', 'T_DOC_COMMENT', 'T_CLOSE_TAG', 'T_INLINE_HTML' ), "$path is one php comment" );
            }
            if ( substr( $path, -4 ) === '.xml' )
            {
                $dom = new DOMDocument();
                $test->assertTrue( @$dom->loadXML( $contents ), "$path is well formed" );
            }
            if ( substr( $path, -5 ) === '.json' )
                $test->assertIsArray( json_decode( $contents, true ), "$path is json" );
        }
    }
}
