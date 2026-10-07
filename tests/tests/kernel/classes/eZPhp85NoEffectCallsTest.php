<?php
/**
 * kernel/ and lib/ call no function PHP 8.5 deprecates for having no effect
 * (curl_close(), imagedestroy(), xml_parser_free(), finfo_close(),
 * Reflection*::setAccessible()) unless the call is guarded by PHP_VERSION_ID
 * for the old versions where it still did something, and pass fgetcsv(),
 * fputcsv() and str_getcsv() their escape character (PHP 8.4 deprecates the
 * default).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZPhp85NoEffectCallsTest extends PHPUnit\Framework\TestCase
{
    private static function phpFiles()
    {
        $root = realpath( __DIR__ . '/../../../..' );
        $files = array();
        foreach ( array( 'kernel', 'lib' ) as $dir )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $f )
                if ( substr( $f->getFilename(), -4 ) === '.php' )
                    $files[substr( $f->getPathname(), strlen( $root ) + 1 )] = $f->getPathname();
        }
        return $files;
    }

    public function testNoEffectCallsAreGuarded()
    {
        $bad = array();
        foreach ( self::phpFiles() as $name => $path )
        {
            $lines = file( $path );
            foreach ( $lines as $i => $line )
            {
                $code = preg_replace( '#\s//\s.*$#', '', $line );
                if ( !preg_match( '/(?<![\w>:$])(curl_close|imagedestroy|xml_parser_free|finfo_close)\s*\(|->setAccessible\s*\(/', $code ) )
                    continue;
                if ( preg_match( '#^\s*(//|\*|/\*)#', $line ) )
                    continue;
                $context = implode( '', array_slice( $lines, max( 0, $i - 2 ), min( 3, $i + 1 ) ) );
                if ( strpos( $context, 'PHP_VERSION_ID' ) === false )
                    $bad[] = $name . ':' . ( $i + 1 );
            }
        }
        $this->assertSame( array(), $bad );
    }

    public function testCsvCallsPassTheirEscapeCharacter()
    {
        $bad = array();
        foreach ( self::phpFiles() as $name => $path )
        {
            $source = file_get_contents( $path );
            if ( !preg_match_all( '/(?<![\w>:$])(fgetcsv|fputcsv|str_getcsv)\s*\(/', $source, $m, PREG_OFFSET_CAPTURE ) )
                continue;
            foreach ( $m[0] as $hit )
            {
                $lineStart = strrpos( substr( $source, 0, $hit[1] ), "\n" );
                $before = substr( $source, $lineStart === false ? 0 : $lineStart + 1, $hit[1] - ( $lineStart === false ? 0 : $lineStart + 1 ) );
                if ( preg_match( '#(//|^\s*\*|/\*)#', $before ) )
                    continue;
                // the arguments up to the matching parenthesis
                $start = $hit[1] + strlen( $hit[0] );
                $depth = 1; $args = 1; $quote = null;
                for ( $p = $start; $p < strlen( $source ) && $depth > 0; $p++ )
                {
                    $c = $source[$p];
                    if ( $quote !== null )
                    {
                        if ( $c === '\\' ) { $p++; continue; }
                        if ( $c === $quote ) $quote = null;
                        continue;
                    }
                    if ( $c === '"' || $c === "'" ) $quote = $c;
                    elseif ( $c === '(' || $c === '[' ) $depth++;
                    elseif ( $c === ')' || $c === ']' ) $depth--;
                    elseif ( $c === ',' && $depth === 1 ) $args++;
                }
                $needed = $hit[0][0] === 's' ? 4 : 5;
                if ( $args < $needed )
                    $bad[] = $name . ':' . ( substr_count( substr( $source, 0, $hit[1] ), "\n" ) + 1 );
            }
        }
        $this->assertSame( array(), $bad );
    }
}
