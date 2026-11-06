<?php
/**
 * Round trips through every URL map format and the format registry (kernel/classes/urlmap/expurlmapformats.php and
 * every reader and writer). No database; the .xlsx and .ods parts need the PHP extension zip.
 *
 *  RT-01 - Every readable fixture (and rows of every redirect status), written by every writer and read back, gives
 *          the same rows: CSV, JSON, XLSX and ODS every column; Apache and nginx the old host, path and query, the target, the kind and the status (a rewrite rule has
 *          no scheme, siteaccess, language or note)
 *  RT-02 - Writing what was read back gives the same file again (CSV, JSON, Apache, nginx)
 *  RT-03 - The registry: names, readable and writable formats, detection by extension, by name and by content,
 *          readers and writers by name, a .tsv written with tabs, unknown formats refused
 *  RT-04 - 10,000 rows through every format in reasonable time, with every row back
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUrlMapRoundTripTest extends PHPUnit\Framework\TestCase
{
    /** @var string[] */
    private $files = array();

    protected function tearDown(): void
    {
        foreach ( $this->files as $file )
        {
            if ( is_file( $file ) )
                unlink( $file );
        }
    }

    /**
     * @return array source name => importable rows
     */
    private function sources()
    {
        $sources = array(
            'csv' => expUrlMapFormats::readFile( __DIR__ . '/fixtures/map.csv' ),
            'json' => expUrlMapFormats::readFile( __DIR__ . '/fixtures/map.json' ),
            'apache' => expUrlMapFormats::readFile( __DIR__ . '/fixtures/rules.htaccess' ),
            'nginx' => expUrlMapFormats::readFile( __DIR__ . '/fixtures/rules.nginx' ),
        );
        if ( class_exists( 'ZipArchive' ) )
            $sources['xlsx'] = expUrlMapFormats::readFile( __DIR__ . '/fixtures/map-libreoffice.xlsx' );
        // every redirect status, as kinds and in the column status
        $sources['statuses'] = array( new expUrlMapRow( '/t1', '/n1', 'redirect-temporary' ), new expUrlMapRow( '/t2', '/n2', 'redirect-temporary', '', '', '', 0, '307' ),
                                        new expUrlMapRow( '/t3', '/n3', 'redirect', '', '', '', 0, '308' ), new expUrlMapRow( '/t4/*', '/n4/{1}', 'wildcard-forward-temporary' ),
                                        new expUrlMapRow( '/t5/*', '/n5/{1}', 'wildcard-forward-temporary', '', '', '', 0, '307' ),
                                        new expUrlMapRow( '/t6', 'node:12', 'redirect-temporary', '', '', '', 0, '303' ), new expUrlMapRow( '/t7?a=1', '/n7', 'redirect-temporary' ) );
        foreach ( $sources as $name => $rows )
            $sources[$name] = array_values( array_filter( $rows, function( expUrlMapRow $row ) { return $row->isImportable(); } ) );
        return $sources;
    }

    /**
     * @param expUrlMapRow[] $rows
     * @param bool $full every column, or only what a rewrite rule keeps
     * @return array
     */
    private function comparable( array $rows, $full )
    {
        $out = array();
        foreach ( $rows as $row )
        {
            if ( $full )
                $out[] = $row->toArray();
            else
                $out[] = array( $row->oldHost(), $row->oldPath(), $row->oldQuery(), $row->newUrl(), $row->effectiveKind(), $row->redirectStatus() );
        }
        return $out;
    }

    /** RT-01, RT-02 */
    public function testEveryFormatRoundTrips()
    {
        $writable = expUrlMapFormats::writable();
        if ( !class_exists( 'ZipArchive' ) )
            $writable = array_values( array_diff( $writable, array( 'xlsx', 'ods' ) ) );
        $checked = 0;
        foreach ( $this->sources() as $source => $rows )
        {
            $this->assertNotEmpty( $rows, $source );
            foreach ( $writable as $format )
            {
                $writer = expUrlMapFormats::writer( $format );
                $written = $writer->writeString( $rows );
                $back = expUrlMapFormats::reader( $format )->readString( $written );
                $full = in_array( $format, array( 'csv', 'json', 'xlsx', 'ods' ), true );
                $expected = $this->comparable( $rows, $full );
                $actual = $this->comparable( $back, $full );
                if ( $format === 'nginx' )
                {
                    // the rows of another host are written in a server block of their own, after the others
                    sort( $expected );
                    sort( $actual );
                }
                $this->assertSame( $expected, $actual, $source . ' -> ' . $format );
                foreach ( $back as $row )
                    $this->assertSame( array(), $row->problems(), $source . ' -> ' . $format . ': ' . $row->oldUrl() );
                // a rewrite rule file keeps notes, siteaccess and language only in comments, which are not read back
                $withoutComments = function( $text ) { return preg_replace( '/^[ \t]*# .*\n/m', '', $text ); };
                if ( $format === 'csv' || $format === 'json' )
                    $this->assertSame( $written, $writer->writeString( $back ), $source . ' -> ' . $format . ' written again' );
                elseif ( $format !== 'xlsx' && $format !== 'ods' )
                    $this->assertSame( $withoutComments( $written ), $withoutComments( $writer->writeString( $back ) ), $source . ' -> ' . $format . ' written again' );
                $checked++;
            }
        }
        $this->assertGreaterThanOrEqual( 16, $checked );
    }

    /** RT-03 */
    public function testRegistry()
    {
        $this->assertSame( array( 'csv', 'json', 'xlsx', 'ods', 'apache', 'nginx' ), expUrlMapFormats::names() );
        $this->assertSame( expUrlMapFormats::names(), expUrlMapFormats::readable() );
        $this->assertSame( expUrlMapFormats::names(), expUrlMapFormats::writable() );

        $byName = array(
            'map.CSV' => 'csv', 'map.tsv' => 'csv', 'list.txt' => 'csv', 'map.json' => 'json', 'Map.xlsx' => 'xlsx',
            'map.ods' => 'ods', '.htaccess' => 'apache', '/var/www/htaccess' => 'apache', 'old.htaccess' => 'apache',
            'site.apacheconf' => 'apache', 'redirects.nginx' => 'nginx',
        );
        foreach ( $byName as $name => $format )
            $this->assertSame( $format, expUrlMapFormats::detect( $name ), $name );
        $this->assertNull( expUrlMapFormats::detect( 'redirects.conf' ) );
        $this->assertSame( 'apache', expUrlMapFormats::detect( 'redirects.conf', "<VirtualHost *:80>\n  Redirect 301 /a /b\n" ) );
        $this->assertSame( 'nginx', expUrlMapFormats::detect( 'redirects.conf', "location = /a { return 301 /b; }\n" ) );
        $this->assertSame( 'nginx', expUrlMapFormats::detect( 'redirects.conf', "rewrite ^/a$ /b permanent;\n" ) );
        $this->assertSame( 'json', expUrlMapFormats::detect( 'upload', "\xEF\xBB\xBF [{}]" ) );
        $this->assertSame( 'xlsx', expUrlMapFormats::detect( 'upload', "PK\x03\x04rest" ) );
        $this->assertSame( 'csv', expUrlMapFormats::detect( 'upload', "/a,/b\n" ) );

        $this->assertInstanceOf( 'expUrlMapNginxReader', expUrlMapFormats::reader( 'NGINX' ) );
        $this->assertInstanceOf( 'expUrlMapSpreadsheetWriter', expUrlMapFormats::writer( 'ods' ) );
        $this->assertInstanceOf( 'expUrlMapOdsReader', expUrlMapFormats::reader( 'ods' ) );
        foreach ( array( function() { expUrlMapFormats::reader( 'yaml' ); }, function() { expUrlMapFormats::writer( 'yaml' ); },
                         function() { expUrlMapFormats::writeFile( 'map.unknown', array() ); } ) as $call )
        {
            try
            {
                $call();
                $this->fail( 'no exception' );
            }
            catch ( expUrlMapFormatException $e )
            {
                $this->assertNotSame( '', $e->getMessage() );
            }
        }

        $file = expSpreadsheetWriter::temporaryFile();
        unlink( $file );
        $file .= '.tsv';
        $this->files[] = $file;
        expUrlMapFormats::writeFile( $file, array( new expUrlMapRow( '/a', '/b' ) ) );
        $this->assertStringContainsString( "/a\t/b\t", file_get_contents( $file ) );
        $rows = expUrlMapFormats::readFile( $file );
        $this->assertSame( '/b', $rows[0]->newUrl() );

        // a file whose name says nothing is read by its content
        $plain = expSpreadsheetWriter::temporaryFile();
        $this->files[] = $plain;
        file_put_contents( $plain, "RedirectPermanent /a /b\n" );
        $rows = expUrlMapFormats::readFile( $plain );
        $this->assertSame( 'redirect', $rows[0]->kind() );
    }

    /** RT-04 */
    public function testTenThousandRows()
    {
        $rows = array();
        for ( $i = 0; $i < 10000; $i++ )
        {
            if ( $i % 10 === 0 )
                $rows[] = new expUrlMapRow( '/section-' . $i . '/*', '/Section/' . $i . '/{1}', '', '', '', 'wildcard ' . $i );
            else
                $rows[] = new expUrlMapRow( 'https://old.example.com/page-' . $i . '.html', '/Pages/Page-' . $i, '', 'site', 'eng-GB', 'note, "' . $i . '"' );
        }
        $formats = array( 'csv', 'json', 'apache', 'nginx' );
        if ( class_exists( 'ZipArchive' ) )
            $formats[] = 'xlsx';
        foreach ( $formats as $format )
        {
            $start = microtime( true );
            $written = expUrlMapFormats::writer( $format )->writeString( $rows );
            $back = expUrlMapFormats::reader( $format )->readString( $written );
            $seconds = microtime( true ) - $start;
            $this->assertCount( 10000, $back, $format );
            $this->assertSame( '/Pages/Page-9999', $back[9999]->newUrl(), $format );
            $this->assertLessThan( 30, $seconds, $format . ' took ' . round( $seconds, 2 ) . ' s for 10,000 rows' );
        }
        if ( class_exists( 'ZipArchive' ) )
        {
            $ods = expUrlMapFormats::writer( 'ods' )->writeString( $rows );
            $this->assertGreaterThan( 1000, strlen( $ods ) );
        }
    }
}
