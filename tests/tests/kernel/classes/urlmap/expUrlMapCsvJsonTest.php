<?php
/**
 * CSV and JSON URL map files (kernel/classes/urlmap/expurlmapcsvreader.php, expurlmapcsvwriter.php,
 * expurlmapjsonreader.php, expurlmapjsonwriter.php). No database.
 *
 *  UC-01 - The CSV fixture: header, quoted fields with delimiters, doubled quotes and line breaks, line numbers
 *  UC-02 - Delimiter sniffing (, ; tab), a UTF-8 byte order mark, UTF-16, Windows-1252, CRLF and CR line ends
 *  UC-03 - Empty files, blank and comment lines, files without a header
 *  UC-04 - Malformed rows are reported with their line: an unclosed quote, text after a closing quote, bad kinds
 *  UC-05 - The CSV writer: RFC 4180 quoting, CRLF, the byte order mark as an option, another delimiter
 *  UC-06 - The JSON fixture: the schema header, other key names, entries that are not objects, entry numbers
 *  UC-07 - JSON that is not a URL map throws: invalid JSON, another schema, an object without rows
 *  UC-08 - The JSON writer: schema header, all keys, unescaped slashes and Unicode
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUrlMapCsvJsonTest extends PHPUnit\Framework\TestCase
{
    /**
     * @param string $data
     * @param array $options
     * @return expUrlMapRow[]
     */
    private function csv( $data, array $options = array() )
    {
        $reader = new expUrlMapCsvReader( $options );
        return $reader->readString( $data );
    }

    /** UC-01 */
    public function testCsvFixture()
    {
        $reader = new expUrlMapCsvReader();
        $rows = $reader->readFile( __DIR__ . '/fixtures/map.csv' );
        $this->assertCount( 7, $rows );
        $this->assertSame( array( 'old_url' => '/about-us.html', 'new_url' => '/About', 'kind' => '', 'siteaccess' => '',
                                  'language' => '', 'note' => 'old about page' ), $rows[0]->toArray() );
        $this->assertSame( 2, $rows[0]->line() );
        $this->assertSame( 'https://old-site.example.com/news/2019', $rows[1]->oldUrl() );
        $this->assertSame( 'eng-GB', $rows[1]->language() );
        $this->assertSame( 'catalogue, all pages', $rows[2]->note() );
        $this->assertSame( 'wildcard-direct', $rows[3]->kind() );
        $this->assertSame( "two\nlines", $rows[4]->note() );
        $this->assertSame( 6, $rows[4]->line() );
        $this->assertSame( '/Über%20uns', $rows[5]->oldUrl() );
        $this->assertSame( 8, $rows[5]->line() );
        $this->assertSame( 'say "hello"', $rows[6]->note() );
        foreach ( $rows as $row )
            $this->assertSame( array(), $row->problems(), $row->oldUrl() );
    }

    /** UC-02 */
    public function testDelimitersAndEncodings()
    {
        $this->assertSame( ';', expUrlMapCsvReader::sniffDelimiter( "a;b;c\n/x;/y;\n" ) );
        $this->assertSame( "\t", expUrlMapCsvReader::sniffDelimiter( "a\tb\n/x\t/y\n" ) );
        $this->assertSame( ',', expUrlMapCsvReader::sniffDelimiter( "a,b\n/x,/y\n" ) );
        $this->assertSame( ';', expUrlMapCsvReader::sniffDelimiter( "\"a,b\";c\n\"/x,1\";/y\n" ) );
        $this->assertSame( ',', expUrlMapCsvReader::sniffDelimiter( "/only-one-column\n" ) );

        // German Excel: byte order mark, semicolons, German header, CRLF
        $rows = $this->csv( "\xEF\xBB\xBFAlte URL;Neue URL;Bemerkung\r\n/alt;/neu;\"a;b\"\r\n/über;/ueber;\r\n" );
        $this->assertCount( 2, $rows );
        $this->assertSame( '/alt', $rows[0]->oldUrl() );
        $this->assertSame( 'a;b', $rows[0]->note() );
        $this->assertSame( '/über', $rows[1]->oldUrl() );
        $this->assertSame( 3, $rows[1]->line() );

        // Excel "Unicode text": UTF-16LE with a byte order mark and tabs
        $utf16 = "\xFF\xFE" . mb_convert_encoding( "old_url\tnew_url\r\n/ä\t/b\r\n", 'UTF-16LE', 'UTF-8' );
        $rows = $this->csv( $utf16 );
        $this->assertSame( '/ä', $rows[0]->oldUrl() );
        $this->assertSame( '/b', $rows[0]->newUrl() );

        // Windows-1252 bytes
        $rows = $this->csv( "/caf\xE9,/cafe\r" );
        $this->assertSame( '/café', $rows[0]->oldUrl() );

        // old Mac line ends and an explicit delimiter
        $rows = $this->csv( "/a|/b\r/c|/d", array( 'delimiter' => '|' ) );
        $this->assertCount( 2, $rows );
        $this->assertSame( '/d', $rows[1]->newUrl() );
    }

    /** UC-03 */
    public function testEmptyAndComments()
    {
        $this->assertSame( array(), $this->csv( '' ) );
        $this->assertSame( array(), $this->csv( "\xEF\xBB\xBF" ) );
        $this->assertSame( array(), $this->csv( "\n\n  \n" ) );
        $this->assertSame( array(), $this->csv( "old_url,new_url\r\n" ) );
        $rows = $this->csv( "# exported 2026\n\n/a,/b\n,,\n# end\n/c,/d,alias" );
        $this->assertCount( 2, $rows );
        $this->assertSame( 3, $rows[0]->line() );
        $this->assertSame( 6, $rows[1]->line() );
        $this->assertSame( 'alias', $rows[1]->kind() );
    }

    /** UC-04 */
    public function testMalformedRowsAreReportedWithTheirLine()
    {
        $rows = $this->csv( "old_url,new_url,kind\n/a,/b,moved\n/c,\"/d\"x\n/e\n/f,\"/g\n/h,/i\n" );
        $this->assertCount( 4, $rows );
        $this->assertSame( 2, $rows[0]->line() );
        $this->assertStringContainsString( 'unknown kind "moved"', implode( ' ', $rows[0]->problems() ) );
        $this->assertSame( 3, $rows[1]->line() );
        $this->assertStringContainsString( 'text after a closing quote on line 3', implode( ' ', $rows[1]->problems() ) );
        $this->assertSame( 4, $rows[2]->line() );
        $this->assertStringContainsString( 'new_url is empty', implode( ' ', $rows[2]->problems() ) );
        $this->assertSame( 5, $rows[3]->line() );
        $this->assertStringContainsString( 'starts on line 5 is not closed', implode( ' ', $rows[3]->problems() ) );
    }

    /** UC-05 */
    public function testCsvWriter()
    {
        $rows = array(
            new expUrlMapRow( '/a', '/b', '', '', '', 'x, "y"' ),
            new expUrlMapRow( '/c', '/d', 'alias', 'site', 'eng-GB', "two\nlines" ),
        );
        $writer = new expUrlMapCsvWriter();
        $this->assertSame( "\xEF\xBB\xBFold_url,new_url,kind,siteaccess,language,note\r\n/a,/b,,,,\"x, \"\"y\"\"\"\r\n"
                         . "/c,/d,alias,site,eng-GB,\"two\nlines\"\r\n", $writer->writeString( $rows ) );
        $writer = new expUrlMapCsvWriter( array( 'bom' => false, 'delimiter' => ';' ) );
        $out = $writer->writeString( array( new expUrlMapRow( '/a', '/b', '', '', '', 'a;b' ) ) );
        $this->assertSame( "old_url;new_url;kind;siteaccess;language;note\r\n/a;/b;;;;\"a;b\"\r\n", $out );
        $this->assertSame( "\"  padded\"", expUrlMapCsvWriter::field( '  padded', ',' ) );
        $this->assertSame( "\xEF\xBB\xBFold_url,new_url,kind,siteaccess,language,note\r\n", ( new expUrlMapCsvWriter() )->writeString( array() ) );
    }

    /** UC-06 */
    public function testJsonFixture()
    {
        $reader = new expUrlMapJsonReader();
        $rows = $reader->readFile( __DIR__ . '/fixtures/map.json' );
        $this->assertCount( 6, $rows );
        $this->assertSame( '/about-us.html', $rows[0]->oldUrl() );
        $this->assertSame( 1, $rows[0]->line() );
        $this->assertSame( 'site', $rows[1]->siteaccess() );
        $this->assertSame( 'wildcard-forward', $rows[2]->effectiveKind() );
        $this->assertSame( 'node:42', $rows[3]->newUrl() );
        $this->assertSame( 'alias', $rows[3]->kind() );
        $this->assertStringContainsString( 'only a wildcard can fill', implode( ' ', $rows[4]->problems() ) );
        $this->assertSame( 5, $rows[4]->line() );
        $this->assertSame( array( 'not importable: entry 6 is not an object' ), $rows[5]->problems() );

        $rows = $reader->readString( '{"schema":"exp-url-map/1","rows":[{"old_url":"/a","new_url":"/b","note":5,"kind":["x"]}]}' );
        $this->assertSame( '5', $rows[0]->note() );
        $this->assertStringContainsString( 'kind is not a string', implode( ' ', $rows[0]->problems() ) );
        $this->assertSame( array(), $reader->readString( '[]' ) );
        $this->assertSame( array(), $reader->readString( "\xEF\xBB\xBF \n" ) );
        $this->assertSame( array(), $reader->readString( '[{"schema":"exp-url-map/1"}]' ) );
    }

    /** UC-07 */
    public function testJsonThatIsNotAMap()
    {
        $reader = new expUrlMapJsonReader();
        foreach ( array( '[{"old_url":', '[{"schema":"exp-url-map/2"}]', '{"old_url":"/a","new_url":"/b"}', '"text"' ) as $json )
        {
            try
            {
                $reader->readString( $json );
                $this->fail( 'no exception for ' . $json );
            }
            catch ( expUrlMapFormatException $e )
            {
                $this->assertNotSame( '', $e->getMessage() );
            }
        }
    }

    /** UC-08 */
    public function testJsonWriter()
    {
        $writer = new expUrlMapJsonWriter();
        $json = $writer->writeString( array( new expUrlMapRow( '/über', '/b/c' ) ) );
        $this->assertStringContainsString( '"old_url": "/über"', $json );
        $this->assertStringContainsString( '"new_url": "/b/c"', $json );
        $this->assertSame( "\n", substr( $json, -1 ) );
        $decoded = json_decode( $json, true );
        $this->assertSame( array( 'schema' => 'exp-url-map/1' ), $decoded[0] );
        $this->assertSame( expUrlMapRow::COLUMNS, array_keys( $decoded[1] ) );
    }
}
