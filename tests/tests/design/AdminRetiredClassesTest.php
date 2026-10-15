<?php
/**
 * The admin designs use no class that no stylesheet draws any more. The setup pages moved to the shared admin
 * components (.a4-help, .a4-notice, .a4-setup-inline ...; doc/bc/6.0/admin4-styling.md) and their old exp-note and
 * exp-inline rules went with the inline <style> blocks; a template written for the old markup still used them
 * (the query cache's APCu reserve warning in setup/info.tpl), and its warning showed as plain text.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminRetiredClassesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminRetiredClassesTest extends PHPUnit\Framework\TestCase
{
    /** Classes no stylesheet of the admin designs defines any more, => what replaces them */
    const RETIRED = array( 'exp-note' => 'a4-help, or a4-notice is-warn|is-bad|is-ok|is-info', 'exp-inline' => 'a4-setup-inline' );

    const DESIGNS = array( 'admin4', 'admin4l', 'admin3', 'admin' );

    public function testNoStylesheetDefinesTheRetiredClasses()
    {
        $root = dirname( __DIR__, 3 );
        foreach ( self::DESIGNS as $design )
        {
            foreach ( glob( "$root/design/$design/stylesheets/{,*/}*.css", GLOB_BRACE ) ?: array() as $css )
            {
                foreach ( array_keys( self::RETIRED ) as $class )
                    $this->assertDoesNotMatchRegularExpression( '/\.' . preg_quote( $class, '/' ) . '(?![\w-])/', (string)file_get_contents( $css ),
                        "$css defines .$class again: then take it off RETIRED" );
            }
        }
    }

    public function testAdminTemplatesUseNoRetiredClass()
    {
        $root = dirname( __DIR__, 3 );
        $found = array();
        foreach ( self::DESIGNS as $design )
        {
            $dir = "$root/design/$design/templates";
            if ( !is_dir( $dir ) )
                continue;
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( substr( $file->getFilename(), -4 ) !== '.tpl' )
                    continue;
                // template comments {* ... *} are not read
                $text = preg_replace( '/\{\*.*?\*\}/s', '', (string)file_get_contents( $file->getPathname() ) );
                foreach ( array_keys( self::RETIRED ) as $class )
                {
                    if ( preg_match( '/class="[^"]*(?<![\w-])' . preg_quote( $class, '/' ) . '(?![\w-])/', $text ) )
                        $found[] = substr( $file->getPathname(), strlen( $root ) + 1 ) . ': .' . $class . ' (use ' . self::RETIRED[$class] . ')';
                }
            }
        }
        sort( $found );
        $this->assertSame( array(), $found, 'Templates use classes no stylesheet draws any more' );
    }
}
