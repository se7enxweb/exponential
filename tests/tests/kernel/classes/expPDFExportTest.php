<?php
/**
 * What the PDF export pages (pdf/list, pdf/edit) work out before they show or store anything. No database: exports
 * are rows of plain values, nodes and files are given.
 *
 *  PE-01 - File names: what is accepted, ".pdf" added, paths and hidden files refused with a reason
 *  PE-02 - The path of a stored file stays in <storage>/pdf; the download name and its header are safe
 *  PE-03 - Why an export cannot be generated; the class list pdf.tpl compares
 *  PE-04 - A card: status, missing source, missing file, a tree without classes, downloads and regenerating
 *  PE-05 - The overview figures, the search and the filters
 *  PE-06 - The orders the list offers, and equal rows by title
 *  PE-07 - Drafts of new exports that were never saved
 *  PE-08 - The edit form: a title, a source, classes for a tree, a safe and free file name
 *  PE-09 - The list: page sizes, the offset after a removal, the search as typed, its address
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Pdf\ListView as PdfList;

class expPDFExportTest extends PHPUnit\Framework\TestCase
{
    private static function row( array $values = array() )
    {
        return $values + array( 'id' => 7, 'title' => 'Handbook', 'status' => eZPDFExport::CREATE_ONCE, 'source_node_id' => 42,
                                'export_structure' => 'node', 'export_classes' => '', 'pdf_filename' => 'handbook.pdf',
                                'show_frontpage' => 1, 'show_footer' => 1, 'footer_text' => '', 'modified' => 100, 'created' => 50 );
    }

    private static function node()
    {
        return array( 'name' => 'Recipes', 'url' => 'Recipes', 'class_name' => 'Folder' );
    }

    private static function file( $exists = true, $size = 2048, $mtime = 200 )
    {
        return array( 'exists' => $exists, 'size' => $exists ? $size : 0, 'mtime' => $exists ? $mtime : 0 );
    }

    /** PE-01 */
    public function testFileNames()
    {
        $this->assertSame( 'handbook.pdf', expPDFExportFile::normalizeName( ' handbook.pdf ' ) );
        $this->assertSame( 'handbook.pdf', expPDFExportFile::normalizeName( 'handbook' ) );
        $this->assertSame( 'Report_2026-10.PDF', expPDFExportFile::normalizeName( 'Report_2026-10.PDF' ) );
        $this->assertSame( 'notes.txt.pdf', expPDFExportFile::normalizeName( 'notes.txt' ) );
        foreach ( array( '', '   ', '../evil.php', 'a/b.pdf', 'a\\b.pdf', '.htaccess', 'x..pdf', 'name with space.pdf', "x\0.pdf", 'ü.pdf', null, array() ) as $bad )
            $this->assertFalse( expPDFExportFile::normalizeName( $bad ), var_export( $bad, true ) );
        $this->assertFalse( expPDFExportFile::normalizeName( str_repeat( 'a', 101 ) ) );

        $this->assertTrue( expPDFExportFile::isSafeName( 'a.pdf' ) );
        $this->assertFalse( expPDFExportFile::isSafeName( 'a' ) );
        $this->assertFalse( expPDFExportFile::isSafeName( '' ) );

        $this->assertFalse( expPDFExportFile::nameProblem( 'handbook' ) );
        $this->assertSame( 'empty', expPDFExportFile::nameProblem( ' ' ) );
        $this->assertSame( 'path', expPDFExportFile::nameProblem( '../evil.php' ) );
        $this->assertSame( 'path', expPDFExportFile::nameProblem( '.hidden.pdf' ) );
        $this->assertSame( 'characters', expPDFExportFile::nameProblem( 'a b.pdf' ) );
        $this->assertSame( 'too_long', expPDFExportFile::nameProblem( str_repeat( 'a', 120 ) ) );
    }

    /** PE-02 */
    public function testPathsAndDownloadNames()
    {
        $this->assertSame( 'var/site/storage/pdf', expPDFExportFile::directory( 'var/site/storage/' ) );
        $this->assertSame( 'var/site/storage/pdf/a.pdf', expPDFExportFile::path( 'a.pdf', 'var/site/storage' ) );
        $this->assertFalse( expPDFExportFile::path( '../a.pdf', 'var/site/storage' ) );
        $this->assertFalse( expPDFExportFile::path( '', 'var/site/storage' ) );

        $this->assertSame( 'handbook.pdf', expPDFExportFile::downloadName( 'handbook.pdf', 'Anything' ) );
        $this->assertSame( 'Product-sheet-2026.pdf', expPDFExportFile::downloadName( 'file.pdf', 'Product sheet / 2026' ) );
        $this->assertSame( 'file.pdf', expPDFExportFile::downloadName( 'file.pdf', '!!!' ) );
        $this->assertSame( 'export.pdf', expPDFExportFile::downloadName( '../x', '' ) );

        $header = expPDFExportFile::dispositionHeader( 'a"b;c.pdf' );
        $this->assertStringStartsWith( 'attachment; filename="a_b_c.pdf"', $header );
        $this->assertStringNotContainsString( "\n", $header );
        $this->assertStringStartsWith( 'inline;', expPDFExportFile::dispositionHeader( 'a.pdf', false ) );
    }

    /** PE-03 */
    public function testGenerationProblems()
    {
        $this->assertFalse( expPDFExportGenerator::problemOf( 42, true, true, 'a.pdf' ) );
        $this->assertFalse( expPDFExportGenerator::problemOf( 42, true, false, '' ) );
        $this->assertSame( 'no_source', expPDFExportGenerator::problemOf( 0, false, true, 'a.pdf' ) );
        $this->assertSame( 'source_missing', expPDFExportGenerator::problemOf( 42, false, true, 'a.pdf' ) );
        $this->assertSame( 'bad_name', expPDFExportGenerator::problemOf( 42, true, true, '../a.pdf' ) );

        $this->assertSame( array( '1', '16', '23' ), expPDFExportGenerator::classArray( '1:16::x:23:16' ) );
        $this->assertSame( array(), expPDFExportGenerator::classArray( '' ) );
    }

    /** PE-04 */
    public function testCardInfo()
    {
        $info = expPDFExportInfo::infoOf( self::row(), self::node(), self::file(), array() );
        $this->assertSame( 'stored', $info['status'] );
        $this->assertTrue( $info['source_exists'] );
        $this->assertTrue( $info['file_exists'] );
        $this->assertSame( 2048, $info['file_size'] );
        $this->assertTrue( $info['can_download'] );
        $this->assertTrue( $info['can_regenerate'] );
        $this->assertFalse( $info['attention'] );
        $this->assertStringContainsString( 'recipes', $info['search'] );

        $gone = expPDFExportInfo::infoOf( self::row(), null, self::file(), array() );
        $this->assertSame( 'source_missing', $gone['problem'] );
        $this->assertFalse( $gone['can_regenerate'] );
        $this->assertTrue( $gone['can_download'], 'the file made earlier can still be downloaded' );
        $this->assertTrue( $gone['attention'] );

        $nofile = expPDFExportInfo::infoOf( self::row(), self::node(), self::file( false ), array() );
        $this->assertFalse( $nofile['can_download'] );
        $this->assertTrue( $nofile['can_regenerate'] );
        $this->assertCount( 1, $nofile['warnings'] );

        $fly = expPDFExportInfo::infoOf( self::row( array( 'status' => eZPDFExport::CREATE_ONFLY ) ), self::node(), self::file(), array() );
        $this->assertSame( 'onthefly', $fly['status'] );
        $this->assertFalse( $fly['file_exists'], 'a stray file does not count for an export made on the fly' );
        $this->assertTrue( $fly['can_download'] );
        $this->assertFalse( $fly['can_regenerate'] );
        $flyGone = expPDFExportInfo::infoOf( self::row( array( 'status' => eZPDFExport::CREATE_ONFLY ) ), null, self::file( false ), array() );
        $this->assertFalse( $flyGone['can_download'] );

        $tree = expPDFExportInfo::infoOf( self::row( array( 'export_structure' => 'tree', 'export_classes' => '' ) ), self::node(), self::file(), array() );
        $this->assertTrue( $tree['attention'], 'a tree without classes exports the source node only' );
        $tree = expPDFExportInfo::infoOf( self::row( array( 'export_structure' => 'tree', 'export_classes' => '1:99' ) ), self::node(), self::file(),
                                          array( '1' => 'Folder', '99' => '' ) );
        $this->assertSame( array( 'Folder', '#99' ), array_column( $tree['classes'], 'name' ) );
        $this->assertCount( 1, $tree['warnings'], 'one class no longer exists' );

        $bad = expPDFExportInfo::infoOf( self::row( array( 'pdf_filename' => '../x' ) ), self::node(), self::file( false ), array() );
        $this->assertSame( 'bad_name', $bad['problem'] );
        $this->assertFalse( $bad['can_regenerate'] );

        $this->assertSame( 'unknown', expPDFExportInfo::statusKey( 0 ) );
        $this->assertSame( array( 3, 1 ), expPDFExportInfo::idList( array( '3', '1', '3', '0', '-2', 'x', 4.0 ) ) );
    }

    /** PE-05 */
    public function testSummaryAndFilters()
    {
        $infos = array(
            1 => expPDFExportInfo::infoOf( self::row( array( 'id' => 1, 'title' => 'Alpha guide' ) ), self::node(), self::file( true, 1000 ), array() ),
            2 => expPDFExportInfo::infoOf( self::row( array( 'id' => 2, 'title' => 'Beta sheet', 'status' => 2 ) ), self::node(), self::file( false ), array() ),
            3 => expPDFExportInfo::infoOf( self::row( array( 'id' => 3, 'title' => 'Gamma', 'pdf_filename' => 'gamma.pdf' ) ), null, self::file( true, 500 ), array() ),
        );
        $summary = expPDFExportInfo::summaryOf( $infos, 2 );
        $this->assertSame( array( 'total' => 3, 'stored' => 2, 'onthefly' => 1, 'generated' => 2, 'size' => 1500,
                                  'missing_source' => 1, 'attention' => 1, 'unfinished' => 2 ), $summary );
        $this->assertSame( 0, expPDFExportInfo::summaryOf( array(), -4 )['unfinished'] );

        $this->assertSame( array( 1, 3 ), array_keys( expPDFExportInfo::filterOf( $infos, '', 'stored' ) ) );
        $this->assertSame( array( 2 ), array_keys( expPDFExportInfo::filterOf( $infos, '', 'onthefly' ) ) );
        $this->assertSame( array( 1, 3 ), array_keys( expPDFExportInfo::filterOf( $infos, '', 'generated' ) ) );
        $this->assertSame( array( 3 ), array_keys( expPDFExportInfo::filterOf( $infos, '', 'attention' ) ) );
        $this->assertSame( array( 1 ), array_keys( expPDFExportInfo::filterOf( $infos, 'ALPHA  recipes', '' ) ) );
        $this->assertSame( array( 3 ), array_keys( expPDFExportInfo::filterOf( $infos, 'gamma.pdf', '' ) ) );
        $this->assertSame( array(), expPDFExportInfo::filterOf( $infos, 'alpha zzz', '' ) );
    }

    /** PE-06 */
    public function testSorting()
    {
        $this->assertSame( array( 'field' => 'title', 'direction' => 'asc', 'opposite' => 'desc' ), expPDFExportInfo::sortOf( 'title; drop', 'x' ) );
        $this->assertSame( 'desc', expPDFExportInfo::sortOf( 'size', null )['direction'] );
        $this->assertSame( 'asc', expPDFExportInfo::sortOf( 'size', 'asc' )['direction'] );

        $infos = array(
            1 => array( 'id' => 1, 'title' => 'b', 'modified' => 5, 'file_mtime' => 0, 'file_size' => 10 ),
            2 => array( 'id' => 2, 'title' => 'A', 'modified' => 9, 'file_mtime' => 0, 'file_size' => 10 ),
            3 => array( 'id' => 3, 'title' => 'c10', 'modified' => 1, 'file_mtime' => 0, 'file_size' => 30 ),
            4 => array( 'id' => 4, 'title' => 'c9', 'modified' => 1, 'file_mtime' => 0, 'file_size' => 0 ),
        );
        $this->assertSame( array( 2, 1, 4, 3 ), array_keys( expPDFExportInfo::sortList( $infos, expPDFExportInfo::sortOf( 'title', 'asc' ) ) ) );
        $this->assertSame( array( 2, 1, 4, 3 ), array_keys( expPDFExportInfo::sortList( $infos, expPDFExportInfo::sortOf( 'modified', 'desc' ) ) ) );
        $this->assertSame( array( 3, 2, 1, 4 ), array_keys( expPDFExportInfo::sortList( $infos, expPDFExportInfo::sortOf( 'size', 'desc' ) ) ) );
        $this->assertSame( array( 4, 3, 2, 1 ), array_keys( expPDFExportInfo::sortList( $infos, expPDFExportInfo::sortOf( 'id', 'desc' ) ) ) );
    }

    /** PE-07 */
    public function testUnfinishedDrafts()
    {
        $drafts = array( array( 'id' => 1, 'modified' => 100 ), array( 'id' => 2, 'modified' => 100 ), array( 'id' => 3, 'modified' => 900 ) );
        $this->assertSame( array( 2, 3 ), expPDFExportInfo::unfinishedOf( $drafts, array( 1 ) ) );
        $this->assertSame( array( 2 ), expPDFExportInfo::unfinishedOf( $drafts, array( '1' ), 500 ) );
        $this->assertSame( array(), expPDFExportInfo::unfinishedOf( array(), array() ) );
    }

    /** PE-08 */
    public function testEditForm()
    {
        $valid = array( 'Title' => ' Handbook ', 'SourceNode' => '42', 'ExportType' => 'tree', 'ClassList' => array( '16', '1', 'x', '16', '999' ),
                        'DestinationType' => 'url', 'DestinationFile' => 'handbook', 'DisplayFrontpage' => 'on', 'FooterText' => ' Ours ' );
        $options = array( 'class_ids' => array( 1, 16 ), 'node_exists' => function ( $id ) { return $id === 42; },
                          'name_taken' => function ( $name ) { return $name === 'taken.pdf'; } );
        $form = expPDFExportForm::read( $valid, array(), $options );
        $this->assertSame( array(), $form['errors'] );
        $a = $form['attributes'];
        $this->assertSame( 'Handbook', $a['title'] );
        $this->assertSame( '16:1', $a['export_classes'] );
        $this->assertSame( 'handbook.pdf', $a['pdf_filename'] );
        $this->assertSame( eZPDFExport::CREATE_ONCE, $a['status'] );
        $this->assertSame( 1, $a['show_frontpage'] );
        $this->assertSame( 0, $a['show_footer'] );
        $this->assertSame( 'Ours', $a['footer_text'] );
        $this->assertSame( 42, $a['source_node_id'] );

        $form = expPDFExportForm::read( array( 'Title' => '', 'SourceNode' => '7', 'ExportType' => 'tree', 'DestinationType' => 'url',
                                               'DestinationFile' => '../evil.php' ), array(), $options );
        $this->assertSame( array( 'Title' => 'title_empty', 'ClassList' => 'classes_empty', 'DestinationFile' => 'file_path',
                                  'SourceNode' => 'source_missing' ), $form['errors'] );
        $this->assertSame( 'evil.php', $form['attributes']['pdf_filename'], 'kept as typed without its folder, never a path' );
        $this->assertSame( array( 'Title', 'SourceNode', 'ClassList', 'DestinationFile' ), array_keys( expPDFExportForm::messages( $form['errors'] ) ) );

        $form = expPDFExportForm::read( array( 'Title' => 'x', 'SourceNode' => '', 'ExportType' => 'node', 'DestinationType' => 'url',
                                               'DestinationFile' => 'taken' ), array( 'export_classes' => '5:6', 'source_node_id' => 42 ), $options );
        $this->assertSame( array( 'DestinationFile' => 'file_taken' ), $form['errors'] );
        $this->assertSame( '5:6', $form['attributes']['export_classes'], 'the classes of a tree are kept while exporting a node' );
        $this->assertSame( 42, $form['attributes']['source_node_id'] );

        // on the fly: the name is optional, a taken one is fine (nothing is written), a bad one is not
        $form = expPDFExportForm::read( array( 'Title' => 'x', 'SourceNode' => '42', 'ExportType' => 'node', 'DestinationType' => 'download',
                                               'DestinationFile' => '' ), array(), $options );
        $this->assertSame( array(), $form['errors'] );
        $this->assertSame( eZPDFExport::CREATE_ONFLY, $form['attributes']['status'] );
        $this->assertSame( 'file.pdf', $form['attributes']['pdf_filename'] );
        $form = expPDFExportForm::read( array( 'Title' => 'x', 'SourceNode' => '42', 'DestinationType' => 'download', 'ExportType' => 'node',
                                               'DestinationFile' => 'a/b' ), array(), $options );
        $this->assertSame( array( 'DestinationFile' => 'file_path' ), $form['errors'] );

        // Browse stores without checking
        $form = expPDFExportForm::read( array( 'Title' => '' ), array(), array( 'check' => false ) + $options );
        $this->assertSame( array(), $form['errors'] );

        $this->assertSame( 'classes_long', expPDFExportForm::read( array( 'Title' => 'x', 'SourceNode' => '42', 'ExportType' => 'tree',
            'ClassList' => array_map( 'strval', range( 1000, 1100 ) ), 'DestinationFile' => 'a' ) )['errors']['ClassList'] );
        $this->assertNotSame( '', expPDFExportForm::message( 'nonsense' ) );
    }

    /** PE-09 */
    public function testListParameters()
    {
        $this->assertSame( array( 10, 25, 30, 50 ), PdfList::sizesOf( 30, array( 10, 25, 50 ) ) );
        $this->assertSame( 50, PdfList::limitOf( '50', '10', 25, array( 10, 25, 50 ) ) );
        $this->assertSame( 10, PdfList::limitOf( '7', '10', 25, array( 10, 25, 50 ) ) );
        $this->assertSame( 25, PdfList::limitOf( 'x', false, 25, array( 10, 25, 50 ) ) );

        $this->assertSame( 0, PdfList::offsetOf( -5, 25, 100 ) );
        $this->assertSame( 25, PdfList::offsetOf( 30, 25, 100 ) );
        $this->assertSame( 50, PdfList::offsetOf( 75, 25, 51 ), 'the last page after a removal' );
        $this->assertSame( 0, PdfList::offsetOf( 75, 25, 0 ) );

        $this->assertSame( 'a b', PdfList::searchOf( " a\x00\nb " ) );
        $this->assertSame( '', PdfList::searchOf( array( 'x' ) ) );
        $this->assertSame( 100, mb_strlen( PdfList::searchOf( str_repeat( 'ä', 300 ) ) ) );

        $this->assertSame( '/pdf/list', PdfList::listURI( array( 'offset' => 0, 'filter' => '', 'search' => '' ) ) );
        $this->assertSame( '/pdf/list/(sort)/size/(dir)/desc/(offset)/25?search=a%20%26%22b%2F',
                           PdfList::listURI( array( 'sort' => 'size', 'dir' => 'desc', 'offset' => 25, 'search' => 'a &"b/' ) ) );
    }
}
