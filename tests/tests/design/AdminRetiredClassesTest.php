<?php
/**
 * The admin designs use no class that no stylesheet draws any more. The setup pages moved to the shared admin
 * components (.a4-help, .a4-notice, .a4-setup-inline ...; doc/eng-us/bc/6.0/admin4-styling.md) and their old exp-note and
 * exp-inline rules went with the inline <style> blocks; a template written for the old markup still used them
 * (the query cache's APCu reserve warning in setup/info.tpl), and its warning showed as plain text.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminRetiredClassesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

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
            // admin4l carries no stylesheets of its own (it inherits admin4's); admin4 must have some
            $pattern = "$root/design/$design/stylesheets/{,*/}*.css";
            $files = $design === 'admin4l' ? ( glob( $pattern, GLOB_BRACE ) ?: array() ) : AdminFiles::files( $pattern, GLOB_BRACE );
            foreach ( $files as $css )
            {
                foreach ( array_keys( self::RETIRED ) as $class )
                    $this->assertDoesNotMatchRegularExpression( '/\.' . preg_quote( $class, '/' ) . '(?![\w-])/', AdminFiles::read( $css ),
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
            if ( $design === 'admin4' )
                $this->assertDirectoryExists( $dir, 'the loop would check nothing without admin4 templates' );
            if ( !is_dir( $dir ) )
                continue;
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( substr( $file->getFilename(), -4 ) !== '.tpl' )
                    continue;
                // template comments {* ... *} are not read
                $text = preg_replace( '/\{\*.*?\*\}/s', '', AdminFiles::read( $file->getPathname() ) );
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
