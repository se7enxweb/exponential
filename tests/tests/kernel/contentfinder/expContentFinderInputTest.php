<?php
/**
 * What the content finder makes of its search text (expContentFinderInput, doc/features/6.0/content-finder.md,
 * "Input types"): recognition, normalisation, and the texts it refuses.
 *
 *  CF-01  a number is tried as every kind of id; ids are whole numbers from 1 to 2147483647
 *  CF-02  a named type ("node:42", "n:42") and the type selector take the text as that type only
 *  CF-03  URLs: a system URL gives its node or attribute id, a file URL its path below var/, any other a URL alias
 *  CF-04  file paths: decoded once, slashes made one, exact or (trailing / or *) prefix; only below var/
 *  CF-05  path traversal is refused: '..' and '.' segments (also percent-encoded), backslashes, other schemes
 *  CF-06  SQL-looking texts are never ids; quotes and wildcards stay text, the LIKE prefix escapes % _ and !
 *  CF-07  control characters, invalid UTF-8, empty and overlong texts are refused
 *
 * No database, no settings.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expContentFinderInputTest extends PHPUnit\Framework\TestCase
{
    /** @return array type => value of the queries of a parse */
    private function queries( $text, $type = 'auto' )
    {
        $parsed = expContentFinderInput::parse( $text, $type );
        $out = array();
        foreach ( $parsed['queries'] as $query )
            $out[] = array( $query['type'], $query['value'] );
        return $out;
    }

    private function error( $text, $type = 'auto' )
    {
        return expContentFinderInput::parse( $text, $type )['error'];
    }

    /** CF-01 */
    public function testANumberIsEveryKindOfID()
    {
        $this->assertSame( array( array( 'object', 42 ), array( 'node', 42 ), array( 'attribute', 42 ), array( 'version', 42 ) ),
                           $this->queries( '42' ) );
        $this->assertSame( $this->queries( '42' ), $this->queries( '  #42 ' ) );
        $this->assertSame( $this->queries( '42' ), $this->queries( '00042' ) );
        $this->assertSame( 2147483647, expContentFinderInput::id( '2147483647' ) );
        foreach ( array( '0', '000', '2147483648', '99999999999', '-1', '4.2', '1e3', '0x2A', '', ' ', '42 43' ) as $bad )
            $this->assertFalse( expContentFinderInput::id( $bad ), var_export( $bad, true ) );
        $this->assertFalse( expContentFinderInput::id( array( 42 ) ) );
        $this->assertFalse( expContentFinderInput::id( null ) );
    }

    /** CF-02 */
    public function testANamedTypeIsThatTypeOnly()
    {
        $this->assertSame( array( array( 'node', 42 ) ), $this->queries( 'node:42' ) );
        $this->assertSame( array( array( 'node', 42 ) ), $this->queries( 'N: 42' ) );
        $this->assertSame( array( array( 'version', 7 ) ), $this->queries( 'v:7' ) );
        $this->assertSame( array( array( 'remote', 'abc' ) ), $this->queries( 'remote:abc' ) );
        $this->assertSame( array( array( 'urlalias', 'Fit-Healthy/News' ) ), $this->queries( 'alias:/Fit-Healthy/News/' ) );
        $this->assertSame( array( array( 'object', 42 ) ), $this->queries( '42', 'object' ) );
        $this->assertSame( array( array( 'remote', '42' ) ), $this->queries( '42', 'remote' ) );
        $this->assertSame( 'invalid', $this->error( 'abc', 'object' ) );
        $this->assertSame( 'invalid', $this->error( 'node:abc' ) );
        $this->assertSame( 'auto', expContentFinderInput::type( 'sql' ) );
        $this->assertSame( 'auto', expContentFinderInput::type( array( 'node' ) ) );
        $this->assertSame( 'file', expContentFinderInput::type( 'file' ) );
    }

    /** CF-03 */
    public function testURLs()
    {
        $this->assertSame( array( array( 'node', 42 ) ), $this->queries( 'https://www.example.invalid/admin/content/view/full/42' ) );
        $this->assertSame( array( array( 'node', 42 ) ), $this->queries( '/content/view/line/42/(offset)/10' ) );
        $this->assertSame( array( array( 'attribute', 345 ) ), $this->queries( 'https://www.example.invalid/content/download/12/345/file/report.pdf' ) );
        $this->assertSame( array( array( 'file', array( 'path' => 'var/site/storage/images/a/b/123-1-eng-GB/b.jpg', 'prefix' => false ) ) ),
                           $this->queries( 'https://www.example.invalid/var/site/storage/images/a/b/123-1-eng-GB/b.jpg?x=1#top' ) );
        $this->assertSame( array( array( 'urlalias', 'Fit-Healthy/News' ) ), $this->queries( 'https://www.example.invalid/Fit-Healthy/News?page=2' ) );
        $this->assertSame( array( array( 'urlalias', 'Fit-Healthy/News' ) ), $this->queries( '/Fit-Healthy//News/' ) );
        // one word: a remote id or a URL alias
        $this->assertSame( array( array( 'remote', 'Fit-Healthy' ), array( 'urlalias', 'Fit-Healthy' ) ), $this->queries( 'Fit-Healthy' ) );
        $this->assertSame( array( array( 'remote', 'd41d8cd98f00b204e9800998ecf8427e' ) ), $this->queries( 'd41d8cd98f00b204e9800998ecf8427e' ) );
        // a host without a path is no alias
        $this->assertSame( 'invalid', $this->error( 'https://www.example.invalid/' ) );
    }

    /** CF-04 */
    public function testFilePaths()
    {
        $this->assertSame( array( 'path' => 'var/site/storage/images/a b.jpg', 'prefix' => false ), expContentFinderInput::filePath( '/var/site/storage/images/a%20b.jpg' ) );
        $this->assertSame( array( 'path' => 'var/site/storage/images/a/', 'prefix' => true ), expContentFinderInput::filePath( 'var//site/storage/images/a/' ) );
        $this->assertSame( array( 'path' => 'var/site/storage/original/application/', 'prefix' => true ), expContentFinderInput::filePath( 'var/site/storage/original/application/*' ) );
        $this->assertSame( array( 'path' => 'var/site/storage/original/application/abc', 'prefix' => true ), expContentFinderInput::filePath( 'var/site/storage/original/application/abc*' ) );
        // decoded once only: %2541 stays %41
        $this->assertSame( array( 'path' => 'var/site/storage/x%41.jpg', 'prefix' => false ), expContentFinderInput::filePath( 'var/site/storage/x%2541.jpg' ) );
        // only below var/, and a prefix must name at least var/<dir>/storage
        foreach ( array( 'etc/passwd', '/etc/passwd', 'var/', 'var', 'storage/images/a.jpg', 'var/site/*', 'var/*', 'var/si*te/a.jpg', 'var/site/storage/a*b*' ) as $bad )
            $this->assertFalse( expContentFinderInput::filePath( $bad ), $bad );
        $this->assertSame( array( array( 'file', array( 'path' => 'var/site/storage/images/a.jpg', 'prefix' => false ) ) ), $this->queries( 'var/site/storage/images/a.jpg' ) );
        $this->assertSame( array( array( 'file', array( 'path' => 'var/storage/original/text/a.txt', 'prefix' => false ) ) ), $this->queries( 'file:var/storage/original/text/a.txt' ) );
    }

    /** CF-05 */
    public function testPathTraversalIsRefused()
    {
        foreach ( array( 'var/site/../../etc/passwd', 'var/site/storage/%2e%2e/%2e%2e/settings/override/site.ini.append.php',
                         'var/site/./storage/a.jpg', 'var\\site\\storage\\a.jpg', 'var/site/storage/..%5c..%5cetc', 'file:///etc/passwd',
                         'php://filter/resource=var/site/a', 'javascript:alert(1)//var/', 'var/site/storage/a%00.jpg' ) as $bad )
        {
            $this->assertFalse( expContentFinderInput::filePath( $bad ), $bad );
            $this->assertNotContains( 'file', array_column( expContentFinderInput::parse( $bad, 'auto' )['queries'], 'type' ), $bad );
        }
        $this->assertSame( 'invalid', $this->error( 'var/site/../../etc/passwd', 'file' ) );
        $this->assertSame( 'invalid', $this->error( '../../etc/passwd', 'urlalias' ) );
        $this->assertFalse( expContentFinderInput::urlAlias( 'News/%2e%2e/Secret' ) );
    }

    /** CF-06 */
    public function testSQLLookingTextsAreText()
    {
        foreach ( array( '1 OR 1=1', "1' OR '1'='1", '1; DROP TABLE ezcontentobject', '1 UNION SELECT password_hash FROM ezuser', '42--' ) as $text )
        {
            $types = array_column( expContentFinderInput::parse( $text )['queries'], 'type' );
            foreach ( expContentFinderInput::ID_TYPES as $idType )
                $this->assertNotContains( $idType, $types, $text );
            $this->assertSame( 'invalid', $this->error( $text, 'object' ), $text );
            $this->assertSame( 'invalid', $this->error( $text, 'node' ), $text );
        }
        // a quote is kept as it is: the database escapes it
        $this->assertSame( "it's", expContentFinderInput::remoteID( "it's" ) );
        $this->assertSame( 'var/site/storage/a!!b!%c!_d%', expContentFinderInput::likePrefix( 'var/site/storage/a!b%c_d' ) );
        $this->assertFalse( expContentFinderInput::remoteID( str_repeat( 'x', 101 ) ) );
    }

    /** CF-07 */
    public function testUnprintableEmptyAndLongTexts()
    {
        $this->assertSame( 'empty', $this->error( '' ) );
        $this->assertSame( 'empty', $this->error( "  \t " ) );
        $this->assertSame( 'empty', $this->error( array( '42' ) ) );
        $this->assertSame( 'too_long', $this->error( str_repeat( '1', 1025 ) ) );
        // what is shown back of a long text is its start in whole characters
        $long = expContentFinderInput::parse( str_repeat( "\xC3\xA4", 600 ) );
        $this->assertSame( 'too_long', $long['error'] );
        $this->assertSame( 1, preg_match( '//u', $long['text'] ) );
        $this->assertSame( str_repeat( "\xC3\xA4", 32 ), $long['text'] );
        $this->assertSame( 'invalid', $this->error( "42\0" ) );
        $this->assertSame( 'invalid', $this->error( "var/site/storage/a\x01.jpg" ) );
        $this->assertSame( 'invalid', $this->error( "abc\xC2\x85def" ) );
        $this->assertSame( 'invalid', $this->error( "abc\xFF" ) );
        $this->assertSame( '', expContentFinderInput::parse( "\xC3\xA4rger" )['error'] );
        $this->assertSame( '', expContentFinderInput::parse( "abc\xFF" )['text'], 'an invalid text is not echoed' );
    }
}
