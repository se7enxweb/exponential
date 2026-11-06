<?php
/**
 * Spreadsheet URL maps and the spreadsheet classes (kernel/classes/spreadsheet/expspreadsheetreader.php,
 * expspreadsheetwriter.php, kernel/classes/urlmap/expurlmapxlsxreader.php, expurlmapspreadsheetwriter.php).
 * No database; needs the PHP extension zip.
 *
 *  SS-01 - A workbook saved by LibreOffice (shared strings) reads as the same rows as the CSV it came from
 *  SS-02 - Cells of every kind: inline strings, rich-text shared strings, numbers, booleans, errors, formulas'
 *          results, sparse cells, rows and cells without r, a prefixed (x:) namespace, only the first sheet
 *  SS-03 - The written .xlsx is a minimal valid package: the parts, content types, relationships, well-formed XML,
 *          every cell an inline string, the header bold and frozen; it reads back as the same rows
 *  SS-04 - The written .ods: mimetype first and stored, the manifest, well-formed content, spaces, tabs and line
 *          breaks as OpenDocument elements
 *  SS-05 - Files that are not workbooks throw: not a ZIP, no workbook, a DOCTYPE, empty input gives no rows
 *  SS-06 - With LibreOffice on the host: the written .xlsx and .ods converted to CSV by soffice give the same rows
 *  SS-07 - A spreadsheet saved by LibreOffice as .ods reads as the same rows as the CSV it came from; the format is
 *          found by the extension and by content (ZIP with the OpenDocument mimetype)
 *  SS-08 - .ods cells: text:s, text:tab, text:line-break, spans and links, several paragraphs, repeated rows and
 *          columns (an empty row repeated a million times only moves on), covered cells, values without paragraphs,
 *          header row groups, annotations left out; the written .ods reads back as the same rows
 *  SS-09 - Files that are not .ods throw: another OpenDocument type, no content.xml, a DOCTYPE
 *  SS-10 - Temporary files go below the installation (var/...), not the system directory
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUrlMapSpreadsheetTest extends PHPUnit\Framework\TestCase
{
    /** @var string[] files to remove after the test */
    private $files = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'ZipArchive' ) )
            $this->markTestSkipped( 'The PHP extension zip is not installed' );
    }

    protected function tearDown(): void
    {
        foreach ( array_reverse( $this->files ) as $file )
        {
            if ( is_dir( $file ) )
                $this->removeTree( $file );
            elseif ( is_file( $file ) )
                unlink( $file );
        }
    }

    /**
     * @param string $suffix
     * @return string a new temporary file name
     */
    private function temp( $suffix )
    {
        $file = expSpreadsheetWriter::temporaryFile();
        unlink( $file );
        $file .= $suffix;
        $this->files[] = $file;
        return $file;
    }

    /**
     * @param string $directory
     */
    private function removeTree( $directory )
    {
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ) as $item )
            $item->isDir() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
        rmdir( $directory );
    }

    /**
     * @param expUrlMapRow[] $rows
     * @return array
     */
    private function arrays( array $rows )
    {
        return array_map( function( expUrlMapRow $row ) { return $row->toArray(); }, $rows );
    }

    /**
     * @param array $parts name => content
     * @return string path of the ZIP
     */
    private function zipOf( array $parts )
    {
        $path = $this->temp( '.xlsx' );
        $zip = new ZipArchive();
        $zip->open( $path, ZipArchive::CREATE | ZipArchive::OVERWRITE );
        foreach ( $parts as $name => $content )
            $zip->addFromString( $name, $content );
        $zip->close();
        return $path;
    }

    /** SS-01 */
    public function testLibreOfficeWorkbook()
    {
        $csv = new expUrlMapCsvReader();
        $xlsx = new expUrlMapXlsxReader();
        $expected = $this->arrays( $csv->readFile( __DIR__ . '/fixtures/map.csv' ) );
        $rows = $xlsx->readFile( __DIR__ . '/fixtures/map-libreoffice.xlsx' );
        $this->assertSame( $expected, $this->arrays( $rows ) );
        $this->assertSame( 2, $rows[0]->line() );
        $this->assertSame( 8, $rows[6]->line() );
        $this->assertSame( $expected, $this->arrays( $xlsx->readString( file_get_contents( __DIR__ . '/fixtures/map-libreoffice.xlsx' ) ) ) );
    }

    /** SS-02 */
    public function testCellKinds()
    {
        $main = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
        $rel = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        $path = $this->zipOf( array(
            '[Content_Types].xml' => '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"/>',
            '_rels/.rels' => '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="' . $rel . '/officeDocument" Target="/xl/workbook.xml"/></Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0"?><x:workbook xmlns:x="' . $main . '" xmlns:r="' . $rel . '"><x:sheets>'
                . '<x:sheet name="Map" sheetId="7" r:id="rId9"/><x:sheet name="Other" sheetId="8" r:id="rId8"/></x:sheets></x:workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId8" Type="' . $rel . '/worksheet" Target="worksheets/other.xml"/>'
                . '<Relationship Id="rId9" Type="' . $rel . '/worksheet" Target="worksheets/map.xml"/></Relationships>',
            'xl/sharedStrings.xml' => '<?xml version="1.0"?><x:sst xmlns:x="' . $main . '"><x:si><x:t>old_url</x:t></x:si>'
                . '<x:si><x:r><x:t>new</x:t></x:r><x:r><x:rPr><x:b/></x:rPr><x:t xml:space="preserve">_url</x:t></x:r><x:rPh><x:t>ignored</x:t></x:rPh></x:si></x:sst>',
            'xl/worksheets/map.xml' => '<?xml version="1.0"?><x:worksheet xmlns:x="' . $main . '"><x:sheetData>'
                . '<x:row r="1"><x:c r="A1" t="s"><x:v>0</x:v></x:c><x:c r="B1" t="s"><x:v>1</x:v></x:c><x:c r="F1" t="inlineStr"><x:is><x:t>note</x:t></x:is></x:c></x:row>'
                . '<x:row r="3"><x:c r="A3" t="inlineStr"><x:is><x:t>/a</x:t></x:is></x:c><x:c r="B3" t="str"><x:f>"/b"</x:f><x:v>/b</x:v></x:c><x:c r="F3"><x:v>42</x:v></x:c></x:row>'
                . '<x:row><x:c t="inlineStr"><x:is><x:t>/c</x:t></x:is></x:c><x:c t="inlineStr"><x:is><x:t>/d</x:t></x:is></x:c>'
                . '<x:c/><x:c/><x:c/><x:c t="b"><x:v>1</x:v></x:c></x:row>'
                . '<x:row r="9"><x:c r="A9" t="inlineStr"><x:is><x:t>/e</x:t></x:is></x:c><x:c r="B9" t="e"><x:v>#REF!</x:v></x:c><x:c r="F9"><x:v>1.5E+3</x:v></x:c></x:row>'
                . '</x:sheetData></x:worksheet>',
            'xl/worksheets/other.xml' => '<?xml version="1.0"?><worksheet xmlns="' . $main . '"><sheetData><row r="1"><c r="A1" t="inlineStr"><is><t>wrong sheet</t></is></c></row></sheetData></worksheet>',
        ) );
        $table = expSpreadsheetReader::readXlsxFile( $path );
        $this->assertSame( array(
            1 => array( 0 => 'old_url', 1 => 'new_url', 5 => 'note' ),
            3 => array( 0 => '/a', 1 => '/b', 5 => '42' ),
            4 => array( 0 => '/c', 1 => '/d', 5 => 'TRUE' ),
            9 => array( 0 => '/e', 5 => '1500' ),
        ), $table );
        $reader = new expUrlMapXlsxReader();
        $rows = $reader->readFile( $path );
        $this->assertCount( 3, $rows );
        $this->assertSame( 3, $rows[0]->line() );
        $this->assertSame( '42', $rows[0]->note() );
        $this->assertSame( 9, $rows[2]->line() );
        $this->assertStringContainsString( 'new_url is empty', implode( ' ', $rows[2]->problems() ) );

        $this->assertSame( 0, expSpreadsheetReader::columnIndex( 'A1' ) );
        $this->assertSame( 27, expSpreadsheetReader::columnIndex( 'AB12' ) );
        $this->assertSame( 'AB', expSpreadsheetWriter::columnName( 27 ) );
        $this->assertSame( 'Z', expSpreadsheetWriter::columnName( 25 ) );
        $this->assertSame( 'AAA', expSpreadsheetWriter::columnName( 702 ) );
    }

    /** SS-03 */
    public function testWrittenXlsx()
    {
        $csv = new expUrlMapCsvReader();
        $rows = $csv->readFile( __DIR__ . '/fixtures/map.csv' );
        $rows[] = new expUrlMapRow( '/x', '/y', '', '', '', "  leading spaces, <tags> & \x01control" );
        $path = $this->temp( '.xlsx' );
        $writer = new expUrlMapSpreadsheetWriter( array( 'type' => 'xlsx' ) );
        $writer->writeFile( $path, $rows );

        $zip = new ZipArchive();
        $this->assertTrue( $zip->open( $path ) );
        $names = array();
        for ( $i = 0; $i < $zip->numFiles; $i++ )
            $names[] = $zip->getNameIndex( $i );
        sort( $names );
        $this->assertSame( array( '[Content_Types].xml', '_rels/.rels', 'xl/_rels/workbook.xml.rels', 'xl/styles.xml',
                                  'xl/workbook.xml', 'xl/worksheets/sheet1.xml' ), $names );
        foreach ( $names as $name )
        {
            $document = new DOMDocument();
            $this->assertTrue( $document->loadXML( $zip->getFromName( $name ) ), $name . ' is well-formed' );
        }
        $sheet = $zip->getFromName( 'xl/worksheets/sheet1.xml' );
        $types = $zip->getFromName( '[Content_Types].xml' );
        $zip->close();
        $this->assertStringContainsString( 'spreadsheetml.sheet.main+xml', $types );
        $this->assertStringContainsString( '<pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>', $sheet );
        $this->assertStringContainsString( '<c r="A1" t="inlineStr" s="1"><is><t>old_url</t></is></c>', $sheet );
        $this->assertStringNotContainsString( 't="s"', $sheet );
        $this->assertStringContainsString( '<t>leading spaces, &lt;tags&gt; &amp; control</t>', $sheet );

        $reader = new expUrlMapXlsxReader();
        $back = $reader->readFile( $path );
        $expected = $this->arrays( $rows );
        $expected[7]['note'] = 'leading spaces, <tags> & control';
        $this->assertSame( $expected, $this->arrays( $back ) );
        $this->assertSame( $this->arrays( $back ), $this->arrays( $reader->readString( $writer->writeString( $rows ) ) ) );
    }

    /** SS-04 */
    public function testWrittenOds()
    {
        $path = $this->temp( '.ods' );
        expSpreadsheetWriter::writeOdsFile( $path, array( array( 'a', 'b' ), array( "x  y", "line1\nline2\ttab", '', ' lead' ) ), array( 'sheet' => 'URL map' ) );
        $zip = new ZipArchive();
        $this->assertTrue( $zip->open( $path ) );
        $first = $zip->statIndex( 0 );
        $this->assertSame( 'mimetype', $first['name'] );
        $this->assertSame( ZipArchive::CM_STORE, $first['comp_method'] );
        $this->assertSame( 'application/vnd.oasis.opendocument.spreadsheet', $zip->getFromName( 'mimetype' ) );
        // the mimetype is readable at a fixed place, as OpenDocument requires
        $this->assertSame( 'mimetypeapplication/vnd.oasis.opendocument.spreadsheet', substr( file_get_contents( $path ), 30, 54 ) );
        $content = $zip->getFromName( 'content.xml' );
        $manifest = $zip->getFromName( 'META-INF/manifest.xml' );
        $zip->close();
        foreach ( array( $content, $manifest ) as $xml )
        {
            $document = new DOMDocument();
            $this->assertTrue( $document->loadXML( $xml ) );
        }
        $this->assertStringContainsString( 'manifest:full-path="content.xml"', $manifest );
        $this->assertStringContainsString( '<table:table table:name="URL map">', $content );
        $this->assertStringContainsString( '<table:table-column table:number-columns-repeated="4"/>', $content );
        $this->assertStringContainsString( '<text:p>x<text:s text:c="2"/>y</text:p>', $content );
        $this->assertStringContainsString( '<text:p>line1</text:p><text:p>line2<text:tab/>tab</text:p>', $content );
        $this->assertStringContainsString( '<table:table-cell/><table:table-cell office:value-type="string"><text:p><text:s/>lead</text:p>', $content );
        $this->assertStringContainsString( 'table:style-name="ceHeader"', $content );

        $writer = new expUrlMapSpreadsheetWriter( array( 'type' => 'ods' ) );
        $this->assertSame( 'PK', substr( $writer->writeString( array( new expUrlMapRow( '/a', '/b' ) ) ), 0, 2 ) );
    }

    /** SS-05 */
    public function testNotAWorkbook()
    {
        $reader = new expUrlMapXlsxReader();
        $this->assertSame( array(), $reader->readString( '' ) );
        $cases = array(
            'not a ZIP' => 'old_url,new_url',
            'no xl/workbook.xml' => file_get_contents( $this->zipOf( array( 'a.txt' => 'x' ) ) ),
            'DOCTYPE' => file_get_contents( $this->zipOf( array(
                'xl/workbook.xml' => '<?xml version="1.0"?><!DOCTYPE x [<!ENTITY a "aaaa">]><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"/>' ) ) ),
            'not well-formed' => file_get_contents( $this->zipOf( array( 'xl/workbook.xml' => '<workbook' ) ) ),
        );
        foreach ( $cases as $expected => $data )
        {
            try
            {
                $reader->readString( $data );
                $this->fail( 'no exception: ' . $expected );
            }
            catch ( expUrlMapFormatException $e )
            {
                $this->assertStringContainsString( $expected, $e->getMessage() );
            }
        }
    }

    /** SS-06 */
    public function testLibreOfficeOpensTheWrittenFiles()
    {
        $soffice = trim( (string)shell_exec( 'command -v soffice 2>/dev/null' ) );
        if ( $soffice === '' )
            $this->markTestSkipped( 'LibreOffice (soffice) is not installed' );
        $csv = new expUrlMapCsvReader();
        $rows = $csv->readFile( __DIR__ . '/fixtures/map.csv' );
        $directory = $this->temp( '-lo' );
        mkdir( $directory );
        foreach ( array( 'xlsx', 'ods' ) as $type )
        {
            $file = $directory . '/map-' . $type . '.' . $type;
            expUrlMapFormats::writeFile( $file, $rows );
            $command = 'cd ' . escapeshellarg( $directory ) . ' && HOME=' . escapeshellarg( $directory )
                     . ' timeout 120 ' . escapeshellarg( $soffice ) . ' -env:UserInstallation=file://' . $directory . '/profile'
                     . ' --headless --convert-to "csv:Text - txt - csv (StarCalc):44,34,76,1" --outdir ' . escapeshellarg( $directory )
                     . ' ' . escapeshellarg( $file ) . ' 2>&1';
            exec( $command, $output, $status );
            $converted = $directory . '/map-' . $type . '.csv';
            if ( !is_file( $converted ) )
                $this->markTestSkipped( 'soffice did not convert: ' . implode( ' ', $output ) );
            $back = $csv->readFile( $converted );
            $this->assertSame( $this->arrays( $rows ), $this->arrays( $back ), $type . ' through LibreOffice' );
        }
    }

    /** SS-07 */
    public function testLibreOfficeOds()
    {
        $csv = new expUrlMapCsvReader();
        $ods = new expUrlMapOdsReader();
        $expected = $this->arrays( $csv->readFile( __DIR__ . '/fixtures/map.csv' ) );
        $rows = $ods->readFile( __DIR__ . '/fixtures/map-libreoffice.ods' );
        $this->assertSame( $expected, $this->arrays( $rows ) );
        $this->assertSame( 2, $rows[0]->line() );
        $data = file_get_contents( __DIR__ . '/fixtures/map-libreoffice.ods' );
        $this->assertSame( $expected, $this->arrays( $ods->readString( $data ) ) );
        $this->assertSame( 'ods', expUrlMapFormats::detect( 'old-site.ods' ) );
        $this->assertSame( 'ods', expUrlMapFormats::sniff( $data ) );
        $this->assertSame( 'xlsx', expUrlMapFormats::sniff( file_get_contents( __DIR__ . '/fixtures/map-libreoffice.xlsx' ) ) );
        $this->assertSame( $expected, $this->arrays( expUrlMapFormats::readFile( __DIR__ . '/fixtures/map-libreoffice.ods' ) ) );
        $this->assertContains( 'ods', expUrlMapFormats::readable() );
    }

    /** SS-08 */
    public function testOdsCells()
    {
        $t = 'urn:oasis:names:tc:opendocument:xmlns:table:1.0';
        $content = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:table="' . $t . '"'
            . ' xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:xlink="http://www.w3.org/1999/xlink"><office:body><office:spreadsheet>'
            . '<table:table table:name="A"><table:table-column table:number-columns-repeated="3"/>'
            . '<table:table-header-rows><table:table-row><table:table-cell><text:p>old_url</text:p></table:table-cell>'
            . '<table:table-cell><text:p>new_url</text:p></table:table-cell><table:table-cell table:number-columns-repeated="2"/>'
            . '<table:table-cell><text:p>note</text:p></table:table-cell></table:table-row></table:table-header-rows>'
            . '<table:table-row><table:table-cell><text:p><text:a xlink:href="http://x">/a</text:a></text:p></table:table-cell>'
            . '<table:table-cell office:value-type="string"><text:p><text:span>/b</text:span></text:p></table:table-cell>'
            . '<table:table-cell table:number-columns-repeated="2"/><table:table-cell><office:annotation><text:p>hidden</text:p></office:annotation>'
            . '<text:p>a<text:s text:c="3"/>b<text:tab/>c<text:line-break/>d</text:p><text:p>second</text:p></table:table-cell></table:table-row>'
            . '<table:table-row table:number-rows-repeated="1048000"><table:table-cell table:number-columns-repeated="1024"/></table:table-row>'
            . '<table:table-row table:number-rows-repeated="2"><table:table-cell office:value-type="float" office:value="12"/>'
            . '<table:covered-table-cell/><table:table-cell><text:p>z</text:p></table:table-cell></table:table-row>'
            . '</table:table><table:table table:name="B"><table:table-row><table:table-cell><text:p>other sheet</text:p></table:table-cell></table:table-row></table:table>'
            . '</office:spreadsheet></office:body></office:document-content>';
        $table = expSpreadsheetReader::odsTable( $content );
        $this->assertSame( array(
            1 => array( 0 => 'old_url', 1 => 'new_url', 4 => 'note' ),
            2 => array( 0 => '/a', 1 => '/b', 4 => "a   b\tc\nd\nsecond" ),
            1048003 => array( 0 => '12', 2 => 'z' ),
            1048004 => array( 0 => '12', 2 => 'z' ),
        ), $table );

        // the written .ods reads back as the same rows
        $rows = array( new expUrlMapRow( '/a', '/b', '', '', '', '  two  spaces' ), new expUrlMapRow( '/c', '/d', 'redirect-temporary', 'site', 'eng-GB', "x\ty\nz", 0, '307' ) );
        $writer = new expUrlMapSpreadsheetWriter( array( 'type' => 'ods' ) );
        $back = ( new expUrlMapOdsReader() )->readString( $writer->writeString( $rows ) );
        $this->assertSame( array_map( function( $r ) { $a = $r->toArray(); $a['note'] = trim( $a['note'] ); return $a; }, $rows ), $this->arrays( $back ) );
        $this->assertSame( 307, $back[1]->redirectStatus() );
    }

    /** SS-09 */
    public function testNotAnOds()
    {
        $reader = new expUrlMapOdsReader();
        $this->assertSame( array(), $reader->readString( '' ) );
        $cases = array(
            'not an .ods spreadsheet (not a ZIP' => 'old_url,new_url',
            'not a spreadsheet' => file_get_contents( $this->zipOf( array( 'mimetype' => 'application/vnd.oasis.opendocument.text', 'content.xml' => '<x/>' ) ) ),
            'no content.xml' => file_get_contents( $this->zipOf( array( 'mimetype' => 'application/vnd.oasis.opendocument.spreadsheet' ) ) ),
            'DOCTYPE' => file_get_contents( $this->zipOf( array( 'content.xml' => '<?xml version="1.0"?><!DOCTYPE x [<!ENTITY a "aaaa">]><x/>' ) ) ),
            'not well-formed' => file_get_contents( $this->zipOf( array( 'content.xml' => '<x' ) ) ),
        );
        foreach ( $cases as $expected => $data )
        {
            try
            {
                $reader->readString( $data );
                $this->fail( 'no exception: ' . $expected );
            }
            catch ( expUrlMapFormatException $e )
            {
                $this->assertStringContainsString( $expected, $e->getMessage() );
            }
        }
    }

    /** SS-10 */
    public function testTemporaryDirectory()
    {
        $directory = expSpreadsheetWriter::temporaryDirectory();
        $this->assertNotSame( realpath( sys_get_temp_dir() ), realpath( $directory ) );
        $this->assertMatchesRegularExpression( '#(^|/)var/#', $directory );
        $file = expSpreadsheetWriter::temporaryFile();
        $this->assertSame( realpath( $directory ), realpath( dirname( $file ) ) );
        unlink( $file );
        $saved = expSpreadsheetWriter::$temporaryDirectory;
        expSpreadsheetWriter::$temporaryDirectory = $directory . '/does-not-exist-' . getmypid();
        try
        {
            expSpreadsheetWriter::temporaryFile();
            $this->fail( 'a file outside the directory given' );
        }
        catch ( expSpreadsheetException $e )
        {
            $this->assertStringContainsString( 'No temporary file can be made', $e->getMessage() );
        }
        finally
        {
            expSpreadsheetWriter::$temporaryDirectory = $saved;
        }
    }
}
