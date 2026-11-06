<?php
/**
 * The row of a URL map file (kernel/classes/urlmap/expurlmaprow.php, expurlmaptable.php). No database.
 *
 *  UR-01 - Old addresses are normalized: scheme and host split and in lower case, default port dropped, slashes,
 *          dot segments, trailing slash, percent-encoding (UTF-8 decoded; / ? # % space stay encoded), fragment
 *  UR-02 - Targets: paths as old addresses, node:<id> and eznode://<id> become node:<id>, a host gets https
 *  UR-03 - Kinds: canonical spellings, auto by "*", unknown kinds are kept and reported
 *  UR-04 - Validation: empty fields, * without a wildcard kind, placeholders, self-redirects, siteaccess, language
 *  UR-05 - Rows that are not importable keep their text and the reason
 *  UR-06 - Table headers: synonyms (English and German), header detection, positional columns, extra columns
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUrlMapRowTest extends PHPUnit\Framework\TestCase
{
    /** UR-01 */
    public function testOldUrlNormalization()
    {
        $cases = array(
            '/about-us' => '/about-us',
            'about-us/' => '/about-us',
            '  /a//b///c/  ' => '/a/b/c',
            '/a/./b/../c' => '/a/c',
            '/' => '/',
            '/../..' => '/',
            '/About-Us' => '/About-Us',
            '/%C3%9Cber%20uns' => '/Über%20uns',
            '/Über uns' => '/Über%20uns',
            '/a%2fb' => '/a%2Fb',
            '/100%25' => '/100%25',
            '/x%ff' => '/x%FF',
            '/page#section' => '/page',
            '/page.php?id=5&x=1' => '/page.php?id=5&x=1',
            'https://Old-Site.Example.com:443/News/' => 'https://old-site.example.com/News',
            'http://old.example.com:80' => 'http://old.example.com/',
            'http://old.example.com:8080/a' => 'http://old.example.com:8080/a',
            'https://old.example.com./a' => 'https://old.example.com/a',
            '//old.example.com/a' => '//old.example.com/a',
            'www.old.example.com/a' => '//www.old.example.com/a',
            '/products/*' => '/products/*',
        );
        foreach ( $cases as $input => $expected )
        {
            $row = new expUrlMapRow( $input, '/target' );
            $this->assertSame( $expected, $row->oldUrl(), var_export( $input, true ) );
        }
        $row = new expUrlMapRow( 'HTTPS://Old.example.com/a/b?x=1', '/t' );
        $this->assertSame( 'https', $row->oldScheme() );
        $this->assertSame( 'old.example.com', $row->oldHost() );
        $this->assertSame( '/a/b', $row->oldPath() );
        $this->assertSame( 'x=1', $row->oldQuery() );
        $this->assertSame( array(), $row->problems() );
    }

    /** UR-01 */
    public function testBadOldUrls()
    {
        $row = new expUrlMapRow( 'ftp://old.example.com/a', '/t' );
        $this->assertStringContainsString( 'not http or https', implode( ' ', $row->problems() ) );
        $row = new expUrlMapRow( 'https://user:secret@old.example.com/a', '/t' );
        $this->assertStringContainsString( 'user name or password', implode( ' ', $row->problems() ) );
        $this->assertSame( 'https://old.example.com/a', $row->oldUrl() );
    }

    /** UR-02 */
    public function testNewUrlNormalization()
    {
        $cases = array(
            '/About/' => '/About',
            'About' => '/About',
            'node:42' => 'node:42',
            'NODE: 42' => 'node:42',
            'eznode://42' => 'node:42',
            '//www.example.com/Contact' => 'https://www.example.com/Contact',
            'http://www.example.com/Contact' => 'http://www.example.com/Contact',
        );
        foreach ( $cases as $input => $expected )
        {
            $row = new expUrlMapRow( '/old', $input );
            $this->assertSame( $expected, $row->newUrl(), $input );
        }
        $row = new expUrlMapRow( '/old', 'node:abc' );
        $this->assertStringContainsString( 'node:<number>', implode( ' ', $row->problems() ) );
    }

    /** UR-03 */
    public function testKinds()
    {
        $this->assertSame( 'wildcard-forward', expUrlMapRow::normalizeKind( 'Wildcard_Forward' ) );
        $this->assertSame( 'wildcard-direct', expUrlMapRow::normalizeKind( 'wildcard direct' ) );
        $this->assertSame( '', expUrlMapRow::normalizeKind( 'auto' ) );
        $this->assertNull( expUrlMapRow::normalizeKind( '301' ) );

        $row = new expUrlMapRow( '/a', '/b' );
        $this->assertSame( '', $row->kind() );
        $this->assertSame( 'redirect', $row->effectiveKind() );
        $this->assertTrue( $row->isForward() );
        $this->assertFalse( $row->isWildcard() );

        $row = new expUrlMapRow( '/a/*', '/b/{1}' );
        $this->assertSame( 'wildcard-forward', $row->effectiveKind() );
        $this->assertTrue( $row->isWildcard() );
        $this->assertTrue( $row->isImportable() );

        $row = new expUrlMapRow( '/a', '/b', 'ALIAS' );
        $this->assertSame( 'alias', $row->kind() );
        $this->assertFalse( $row->isForward() );

        $row = new expUrlMapRow( '/a', '/b', 'moved' );
        $this->assertSame( 'moved', $row->kind() );
        $this->assertStringContainsString( 'unknown kind "moved"', implode( ' ', $row->problems() ) );
    }

    /** UR-04 */
    public function testValidation()
    {
        $problems = function( $old, $new, $kind = '', $siteaccess = '', $language = '' )
        {
            $row = new expUrlMapRow( $old, $new, $kind, $siteaccess, $language );
            return implode( ' | ', $row->problems() );
        };
        $this->assertStringContainsString( 'old_url is empty', $problems( '', '/b' ) );
        $this->assertStringContainsString( 'new_url is empty', $problems( '/a', ' ' ) );
        $this->assertStringContainsString( 'is not a wildcard', $problems( '/a/*', '/b', 'redirect' ) );
        $this->assertStringContainsString( 'needs a * in old_url', $problems( '/a', '/b', 'wildcard-forward' ) );
        $this->assertStringContainsString( 'only a wildcard can fill', $problems( '/a', '/b/{1}' ) );
        $this->assertStringContainsString( 'the placeholder {2} has no *', $problems( '/a/*', '/b/{2}' ) );
        $this->assertSame( '', $problems( '/a/*/b/*', '/c/{2}/{1}' ) );
        $this->assertStringContainsString( 'the same address', $problems( '/a/', '/a' ) );
        $this->assertSame( '', $problems( 'https://old.example.com/a', '/a' ) );
        $this->assertStringContainsString( 'only allowed in the path', $problems( 'https://*.example.com/a', '/a' ) );
        $this->assertStringContainsString( 'cannot have a query', $problems( '/a/*?x=1', '/b' ) );
        $this->assertStringContainsString( 'not a siteaccess name', $problems( '/a', '/b', '', 'my site' ) );
        $this->assertStringContainsString( 'not a locale', $problems( '/a', '/b', '', '', 'english' ) );
        $this->assertSame( '', $problems( '/a', '/b', '', 'site_en', 'eng-GB' ) );
    }

    /** UR-05 */
    public function testNotImportable()
    {
        $row = expUrlMapRow::notImportable( '^/a/(\d+)$', '/b/$1', 'the pattern uses \d', 7, 'RedirectMatch ...' );
        $this->assertFalse( $row->isImportable() );
        $this->assertSame( '^/a/(\d+)$', $row->oldUrl() );
        $this->assertSame( '/b/$1', $row->newUrl() );
        $this->assertSame( 7, $row->line() );
        $this->assertSame( array( 'not importable: the pattern uses \d' ), $row->problems() );
        $this->assertSame( 'RedirectMatch ...', $row->note() );
    }

    /** UR-06 */
    public function testTableHeaders()
    {
        $this->assertSame( 'old_url', expUrlMapTable::columnOf( 'Old URL' ) );
        $this->assertSame( 'old_url', expUrlMapTable::columnOf( 'Alte URL' ) );
        $this->assertSame( 'new_url', expUrlMapTable::columnOf( 'Ziel' ) );
        $this->assertSame( 'kind', expUrlMapTable::columnOf( 'Typ' ) );
        $this->assertSame( 'note', expUrlMapTable::columnOf( 'Bemerkung' ) );
        $this->assertNull( expUrlMapTable::columnOf( 'hits' ) );

        // a header in another order, with a column that is not ours
        $rows = expUrlMapTable::rowsFromTable( array(
            1 => array( 'Ziel', 'hits', 'Quelle' ),
            2 => array( '/new', '17', '/old' ),
            4 => array( '', '', '' ),
            5 => array( '# a comment' ),
        ) );
        $this->assertCount( 1, $rows );
        $this->assertSame( '/old', $rows[0]->oldUrl() );
        $this->assertSame( '/new', $rows[0]->newUrl() );
        $this->assertSame( 2, $rows[0]->line() );

        // no header: canonical positions; an extra filled column is a problem
        $rows = expUrlMapTable::rowsFromTable( array(
            1 => array( '/a', '/b', 'alias' ),
            2 => array( '/c', '/d', '', '', '', '', '', 'extra' ),
        ) );
        $this->assertSame( 'alias', $rows[0]->kind() );
        $this->assertTrue( $rows[0]->isImportable() );
        $this->assertStringContainsString( 'more than 7 columns', implode( ' ', $rows[1]->problems() ) );

        // sparse cells (spreadsheets)
        $rows = expUrlMapTable::rowsFromTable( array( 3 => array( 0 => '/a', 5 => 'note' ) ) );
        $this->assertSame( 'new_url is empty', $rows[0]->problems()[0] );
        $this->assertSame( 'note', $rows[0]->note() );
    }
}
