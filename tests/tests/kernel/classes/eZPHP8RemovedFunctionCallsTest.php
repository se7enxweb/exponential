<?php
/**
 * No kernel, library, script or cronjob code calls a function PHP 8 removed, nor uses the preg /e modifier, the
 * cases of the LoveStack (mugoweb/ezpublish-legacy) PHP 7 commits 0513dbae32 (each()), cb8a3a1c58 (preg_replace
 * /e, mysql_escape_string()) and bd4fa8600d. A call to one of them is a fatal "Call to undefined function" on
 * PHP 8; the scan reads the tokens of every file, so comments and strings do not count.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group php8
 */

class eZPHP8RemovedFunctionCallsTest extends PHPUnit\Framework\TestCase
{
    /** functions removed in PHP 7.0 or 8.0 */
    private static $removed = array( 'each', 'create_function', 'ereg', 'eregi', 'ereg_replace', 'eregi_replace',
                                     'split', 'spliti', 'sql_regcase', 'mysql_escape_string', 'mysql_real_escape_string',
                                     'mysql_query', 'mysql_connect', 'money_format', 'convert_cyr_string', 'hebrevc',
                                     'restore_include_path', 'ezmlm_hash', 'fgetss', 'image2wbmp', 'jpeg2wbmp',
                                     'png2wbmp', 'call_user_method', 'call_user_method_array' );

    private static function phpFiles()
    {
        $root = dirname( __DIR__, 4 );
        $files = array();
        foreach ( array( 'bin/php', 'kernel', 'lib', 'cronjobs', 'update' ) as $dir )
        {
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $iterator as $file )
            {
                if ( $file->isFile() && substr( $file->getFilename(), -4 ) === '.php' )
                    $files[] = $file->getPathname();
            }
        }
        sort( $files );
        return array( $root, $files );
    }

    public function testNoCallToARemovedFunction()
    {
        list( $root, $files ) = self::phpFiles();
        $removed = array_flip( self::$removed );
        $found = array();
        foreach ( $files as $file )
        {
            $tokens = array_values( array_filter( token_get_all( file_get_contents( $file ) ), function ( $t )
            {
                return !is_array( $t ) || !in_array( $t[0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true );
            } ) );
            foreach ( $tokens as $i => $token )
            {
                if ( !is_array( $token ) || $token[0] !== T_STRING || !isset( $removed[strtolower( $token[1] )] ) )
                    continue;
                $next = $tokens[$i + 1] ?? null;
                $prev = $tokens[$i - 1] ?? null;
                if ( $next !== '(' )
                    continue;
                // a method, a declaration or a call guarded by function_exists() elsewhere is not a call to the function
                if ( is_array( $prev ) && in_array( $prev[0], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION, T_NEW, T_NULLSAFE_OBJECT_OPERATOR ), true ) )
                    continue;
                $found[] = substr( $file, strlen( $root ) + 1 ) . ':' . $token[2] . ' ' . $token[1] . '()';
            }
        }
        $this->assertSame( array(), $found );
    }

    public function testNoPregEModifier()
    {
        list( $root, $files ) = self::phpFiles();
        $found = array();
        foreach ( $files as $file )
        {
            $tokens = array_values( array_filter( token_get_all( file_get_contents( $file ) ), function ( $t )
            {
                return !is_array( $t ) || !in_array( $t[0], array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ), true );
            } ) );
            foreach ( $tokens as $i => $token )
            {
                if ( !is_array( $token ) || $token[0] !== T_STRING || strtolower( $token[1] ) !== 'preg_replace' )
                    continue;
                $pattern = $tokens[$i + 2] ?? null;
                if ( ( $tokens[$i + 1] ?? null ) !== '(' || !is_array( $pattern ) || $pattern[0] !== T_CONSTANT_ENCAPSED_STRING )
                    continue;
                $literal = substr( $pattern[1], 1, -1 );
                if ( $literal === '' )
                    continue;
                $delimiter = $literal[0];
                $closing = array( '(' => ')', '{' => '}', '[' => ']', '<' => '>' )[$delimiter] ?? $delimiter;
                $end = strrpos( $literal, $closing );
                if ( $end !== false && $end > 0 && strpos( substr( $literal, $end + 1 ), 'e' ) !== false )
                    $found[] = substr( $file, strlen( $root ) + 1 ) . ':' . $token[2] . ' ' . $pattern[1];
            }
        }
        $this->assertSame( array(), $found );
    }
}
