<?php
/**
 * The CSV of the subitems list, without a database.
 *
 *  CSV-01 — a UTF-8 BOM, the header of column names, CRLF line ends
 *  CSV-02 — one row per node, each column's text(), quoting by fputcsv
 *  CSV-03 — formula-like cells get a leading apostrophe; numbers stay numbers
 *  CSV-04 — a column that throws gives an empty cell
 *  CSV-05 — the file name comes from the node name, made safe
 *  CSV-06 — the view is registered in kernel/content/module.php with content/read
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group subitems
 */

require_once __DIR__ . '/fixtures/expsubitemstesthelpers.php';

class expSubitemsCSVExportTest extends ezpTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        expSubitemsTestFixtures::warmUp();
    }

    public function setUp(): void
    {
        parent::setUp();
        expSubitemsColumnRegistry::setAccessChecker( function () { return true; } );
    }

    public function tearDown(): void
    {
        expSubitemsColumnRegistry::setAccessChecker( null );
        parent::tearDown();
    }

    private function csv( array $nodes, array $columns, $bom = true )
    {
        $h = fopen( 'php://memory', 'w+' );
        expSubitemsCSVExport::writeHeader( $h, $columns, $bom );
        expSubitemsCSVExport::writeRows( $h, $nodes, $columns );
        rewind( $h );
        $out = stream_get_contents( $h );
        fclose( $h );
        return $out;
    }

    public function testHeaderAndRows()
    {
        $r = expSubitemsTestFixtures::registry();
        $parent = expSubitemsTestFixtures::parent();
        $columns = $r->resolveColumns( 'nodeid,classcol,handlercol,priority', $parent, true );
        $out = $this->csv( expSubitemsTestFixtures::children( 2 ), $columns );

        $this->assertStringStartsWith( expSubitemsCSVExport::BOM, $out );
        $lines = explode( "\r\n", substr( $out, 3 ) );
        $this->assertSame( '"Node ID","Class column","Handler column",Priority', $lines[0] );
        $this->assertSame( array( 'Node ID', 'Class column', 'Handler column', 'Priority' ), str_getcsv( $lines[0], ',', '"', '' ) );
        $this->assertSame( array( '101', 'classcol:101', '<b>Handler column</b> #101', '1' ), str_getcsv( $lines[1], ',', '"', '' ) );
        $this->assertSame( array( '102', 'classcol:102', '<b>Handler column</b> #102', '2' ), str_getcsv( $lines[2], ',', '"', '' ) );
        $this->assertSame( '', $lines[3], 'ends with CRLF' );
        $this->assertCount( 4, $lines );
    }

    public function testNoBom()
    {
        $r = expSubitemsTestFixtures::registry();
        $out = $this->csv( array(), $r->resolveColumns( 'nodeid', expSubitemsTestFixtures::parent(), true ), false );
        $this->assertSame( "\"Node ID\"\r\n", $out );
    }

    public function testQuotingAndFormulas()
    {
        $this->assertSame( "'=SUM(A1)", expSubitemsCSVExport::cell( '=SUM(A1)' ) );
        $this->assertSame( "'@x", expSubitemsCSVExport::cell( '@x' ) );
        $this->assertSame( "'+x", expSubitemsCSVExport::cell( '+x' ) );
        $this->assertSame( '-12', expSubitemsCSVExport::cell( '-12' ), 'a negative number stays a number' );
        $this->assertSame( 'plain', expSubitemsCSVExport::cell( 'plain' ) );
        $this->assertSame( '', expSubitemsCSVExport::cell( '' ) );

        $h = fopen( 'php://memory', 'w+' );
        expSubitemsCSVExport::writeLine( $h, array( 'a,b', 'say "hi"', "two\nlines", 'Ärger' ) );
        rewind( $h );
        $line = stream_get_contents( $h );
        $this->assertSame( "\"a,b\",\"say \"\"hi\"\"\",\"two\nlines\",Ärger\r\n", $line );
    }

    public function testThrowingColumnGivesEmptyCell()
    {
        $columns = array( 'bad' => new expSubitemsTestThrowingColumn( 'bad', array( 'Name' => 'Bad' ) ),
                          'good' => new expSubitemsTestCountingColumn( 'good', array( 'Name' => 'Good' ) ) );
        $out = $this->csv( expSubitemsTestFixtures::children( 1 ), $columns, false );
        $lines = explode( "\r\n", $out );
        $this->assertSame( array( '', 'good:101' ), str_getcsv( $lines[1], ',', '"', '' ) );
    }

    public function testFileName()
    {
        $this->assertStringEndsWith( '.csv', expSubitemsCSVExport::fileName( 'Fit & Healthy' ) );
        $this->assertDoesNotMatchRegularExpression( '/[^A-Za-z0-9._\-]/', expSubitemsCSVExport::fileName( 'Ä "quoted" / \\ name' ) );
        $this->assertSame( 'subitems.csv', expSubitemsCSVExport::fileName( '' ) );
        $this->assertSame( 'subitems.csv', expSubitemsCSVExport::fileName( '"/\\' ) );
        $this->assertLessThanOrEqual( 104, strlen( expSubitemsCSVExport::fileName( str_repeat( 'a', 500 ) ) ) );
    }

    public function testViewRegistered()
    {
        $ViewList = array();
        $FunctionList = array();
        include 'kernel/content/module.php';
        $this->assertArrayHasKey( 'subitemsexport', $ViewList );
        $this->assertSame( array( 'read' ), $ViewList['subitemsexport']['functions'] );
        $this->assertSame( 'subitemsexport.php', $ViewList['subitemsexport']['script'] );
        $this->assertSame( array( 'NodeID' ), $ViewList['subitemsexport']['params'] );
        $this->assertFileExists( 'kernel/content/subitemsexport.php' );
        $this->assertTrue( class_exists( '\Exponential\View\Kernel\Content\Subitemsexport' ) );
        $this->assertTrue( is_subclass_of( '\Exponential\View\Kernel\Content\Subitemsexport', '\Exponential\Runnable\ModuleView' ) );
    }
}
