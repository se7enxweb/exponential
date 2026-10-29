<?php
/**
 * No script of the admin designs, the standard design or the extensions the kernel ships uses a jQuery API that
 * jQuery 4 removed (jQuery.trim, jQuery.isArray, jQuery.type, jQuery.isFunction ...), one jQuery 3 already removed
 * (.size(), .andSelf(), .live(), the .load(fn) event shorthand), the event binders jQuery 4 keeps only as deprecated
 * (.bind, .unbind, .delegate, .undelegate) or a jQuery-only positional selector (:first, :last, :eq() ...). ezjscore
 * ships jQuery 4 (extension/ezjscore/design/standard/javascript/jquery-4.0.0.js), so each of these throws or will.
 *
 * Reads files only. Comments are left out, so a note that names the old API ("not .size()") is fine. Vendored
 * libraries (jQuery itself, jQuery UI and Migrate, TinyMCE's sources in ezoe) are not ours to rewrite and are skipped.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminJQuery4RemovedApisTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminJQuery4RemovedApisTest extends PHPUnit\Framework\TestCase
{
    /** Where the scripts and the templates with scripts live */
    const PLACES = array(
        'design/{admin,admin3,admin4,admin4l,standard}/javascript',
        'design/{admin,admin3,admin4,admin4l,standard}/templates',
        'extension/{ezjscore,ezoe,ezformtoken,expservices}/design/*/javascript',
        'extension/{ezjscore,ezoe,ezformtoken,expservices}/design/*/templates',
    );

    /** Vendored code, matched against the path below the root */
    const VENDORED = '#(?:/jquery-(?:\d|migrate|ui)[^/]*\.js$|\.min\.js$|^extension/ezoe/design/standard/javascript/(?:tiny_mce[^/]*\.js$|classes/|utils/|plugins/|themes/|langs/))#';

    /** What jQuery 4 no longer has => what to use instead */
    const PATTERNS = array(
        '/(?<![\w$])(?:\$|jQuery)\.(?:trim|isArray|parseJSON|type|isFunction|isWindow|isNumeric|camelCase|nodeName|now|unique|holdReady)\s*\(/'
            => 'a jQuery 4-removed static (use String.prototype.trim, Array.isArray, JSON.parse, typeof, Date.now, jQuery.uniqueSort ...)',
        '/(?<![\w$])(?:\$|jQuery)\.(?:browser|cssProps|cssNumber)\b/' => 'a jQuery-removed property',
        '/\.(?:size|andSelf)\(\s*\)/' => '.size() / .andSelf() (use .length / .addBack())',
        '/\.(?:live|die)\(\s*[\'"]/' => '.live() / .die() (use .on() / .off())',
        '/\.(?:load|unload|error)\(\s*function\b/' => 'the .load(fn) / .unload(fn) / .error(fn) event shorthand (use .on())',
        '/\.(?:bind|unbind|delegate|undelegate)\(\s*[\'"]|\.unbind\(\s*\)/' => '.bind() / .unbind() / .delegate() / .undelegate() (use .on() / .off())',
        '/([\'"])(?:(?!\1)[^\n])*?:(?:(?:first|last|even|odd)(?![-\w])|(?:eq|gt|lt|nth)\()(?:(?!\1)[^\n])*\1/'
            => 'a jQuery-only positional selector (use .first(), .last(), .eq(), .slice())',
    );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** The files to check, path below the root => kind (js or tpl) */
    private static function files()
    {
        $root = self::root();
        $files = array();
        foreach ( self::PLACES as $place )
        {
            $kind = substr( $place, -10 ) === 'javascript' ? 'js' : 'tpl';
            $dirs = glob( "$root/$place", GLOB_BRACE | GLOB_ONLYDIR ) ?: array();
            foreach ( $dirs as $dir )
            {
                $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
                foreach ( $iterator as $file )
                {
                    if ( !$file->isFile() || $file->getExtension() !== $kind )
                        continue;
                    $path = substr( $file->getPathname(), strlen( $root ) + 1 );
                    if ( !preg_match( self::VENDORED, $path ) )
                        $files[$path] = $kind;
                }
            }
        }
        ksort( $files );
        return $files;
    }

    /** The code of a file without its comments; a template keeps only its script elements */
    private static function code( $path, $kind )
    {
        $source = AdminFiles::read( self::root() . '/' . $path );
        if ( $kind === 'tpl' )
        {
            $source = preg_replace( '#\{\*.*?\*\}#s', '', $source );
            preg_match_all( '#<script\b[^>]*>(.*?)</script>#is', $source, $scripts );
            $source = implode( "\n", $scripts[1] );
        }
        return preg_replace( array( '#/\*.*?\*/#s', '#(?<![:\'"\\\\])//[^\n]*#' ), '', $source );
    }

    public function testThereAreScriptsToCheck()
    {
        $files = self::files();
        // the admin4 page scripts alone are more than fifty; a wrong place or pattern would check nothing
        $this->assertGreaterThan( 100, count( $files ) );
        $this->assertArrayHasKey( 'design/admin4/javascript/page/content_edit_attribute.js', $files );
        $this->assertArrayHasKey( 'extension/ezoe/design/standard/templates/ezoe/customattributes/link.tpl', $files );
        $this->assertArrayNotHasKey( 'extension/ezjscore/design/standard/javascript/jquery-4.0.0.js', $files );
    }

    public function testNoScriptUsesAnApiJQuery4Removed()
    {
        $found = array();
        foreach ( self::files() as $path => $kind )
        {
            $code = self::code( $path, $kind );
            foreach ( self::PATTERNS as $pattern => $what )
            {
                if ( preg_match_all( $pattern, $code, $matches ) )
                {
                    foreach ( $matches[0] as $match )
                        $found[] = "$path: $what: " . trim( $match );
                }
            }
        }
        $this->assertSame( array(), $found, "scripts that use APIs jQuery 4 no longer has:\n" . implode( "\n", $found ) );
    }

    /**
     * The patterns catch what they are meant to, and leave alone what only looks like it
     */
    public function testThePatternsTellTheOldApiFromLookAlikes()
    {
        $old = array( "\$.trim( s )", "jQuery.isArray( a )", "Exp.\$.isFunction( f )", "el.size()", "\$('a').bind('click', f)",
                      "\$('a').unbind()", "\$('input:first')", "\$('tr:eq(2)')", "\$(window).load(function(){})" );
        $new = array( "s.trim()", "this.bind()", "Array.isArray( a )", "f.bind( this )", "fn.bind(this, 1)", "\$('li:first-child')",
                      "\$('li:last-of-type')", "\$('a').on('click', f)", "map.size", "\$('#x').load( url )" );
        $hits = function( $code ) {
            foreach ( self::PATTERNS as $pattern => $what )
                if ( preg_match( $pattern, $code ) )
                    return true;
            return false;
        };
        foreach ( $old as $code )
            $this->assertTrue( $hits( $code ), "not caught: $code" );
        foreach ( $new as $code )
            $this->assertFalse( $hits( $code ), "caught by mistake: $code" );
    }
}
