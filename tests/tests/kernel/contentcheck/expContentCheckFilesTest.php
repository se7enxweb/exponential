<?php
/**
 * The missing files check, the parts that need no database (doc/eng-us/features/6.0/content-checks.md, "Missing files"):
 * reading the request, the files an image field names, how a field version is judged, the queries, and the target
 * of the broken relations that shows them.
 *
 *  CF-01  the parameters: problems and versions from a fixed list (defaults), keyset position, page size
 *  CF-02  field version keys: "<attribute id>-<version>" only, unique, bounded
 *  CF-03  the files of an image field: the original and each alias once; empty, broken or foreign XML names none;
 *         a path with "..", a scheme or a NUL is dropped
 *  CF-04  judging: a missing original hides its aliases; alias missing, out of date (settings, expired, older than
 *         the original), never made (only when asked); an alias image.ini no longer has is ignored
 *  CF-05  the problem filter keeps only the chosen alias states
 *  CF-06  the queries: subtree, main location, published, after an object id, the permission condition, the
 *         datatypes and version statuses; a pattern that is not a subtree path is refused
 *  CF-07  the target "Missing files" of the broken relations: ticked, alone, and the query string that keeps it;
 *         the section templates in both designs, without inline CSS
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */


class expContentCheckFilesTest extends PHPUnit\Framework\TestCase
{
    const IMAGE_XML = '<?xml version="1.0" encoding="utf-8"?>
<ezimage serial_number="1" is_valid="1" filename="a.jpg" suffix="jpg" basename="a" dirpath="var/site/storage/images/a/12-1-eng-GB"
 url="var/site/storage/images/a/12-1-eng-GB/a.jpg" original_filename="photo.jpg" mime_type="image/jpeg" width="10" height="10"
 alternative_text="" alias_key="1293033771" timestamp="1700000000">
  <original attribute_id="12" attribute_version="1" attribute_language="eng-GB"/>
  <alias name="small" filename="a_small.jpg" suffix="jpg" dirpath="var/site/storage/images/a/12-1-eng-GB"
   url="var/site/storage/images/a/12-1-eng-GB/a_small.jpg" mime_type="image/jpeg" width="5" height="5" alias_key="111" timestamp="1700000100" is_valid="1"/>
  <alias name="large" url="var/site/storage/images/a/12-1-eng-GB/a_large.jpg" alias_key="222" timestamp="1700000100" is_valid="1"/>
  <alias name="small" url="var/site/storage/images/a/12-1-eng-GB/a_small2.jpg" alias_key="111" timestamp="1" is_valid="1"/>
  <alias name="evil" url="../../etc/passwd" alias_key="333" timestamp="1" is_valid="1"/>
</ezimage>';

    /** CF-01 */
    public function testParameters()
    {
        $p = expContentCheckFiles::parameters( array(), 2 );
        $this->assertSame( 2, $p['node_id'] );
        $this->assertSame( expContentCheckFiles::DEFAULT_PROBLEMS, $p['problems'] );
        $this->assertSame( expContentCheckFiles::VERSIONS, $p['versions'] );
        $this->assertSame( 0, $p['after'] );
        $p = expContentCheckFiles::parameters( array( 'NodeID' => '58', 'Problem' => array( 'alias_absent', 'x', "1' OR 1=1" ),
                                                       'Version' => array( 'draft', 'archived' ), 'After' => '77', 'Limit' => 5000 ), 2 );
        $this->assertSame( 58, $p['node_id'] );
        $this->assertSame( array( 'alias_absent' ), $p['problems'] );
        $this->assertSame( array( 'draft' ), $p['versions'] );
        $this->assertSame( 77, $p['after'] );
        $this->assertSame( 200, $p['limit'] );
        $p = expContentCheckFiles::parameters( array( 'NodeID' => 'abc', 'After' => '-3', 'Problem' => 'missing' ), 2 );
        $this->assertSame( 2, $p['node_id'] );
        $this->assertSame( 0, $p['after'] );
        $this->assertSame( expContentCheckFiles::DEFAULT_PROBLEMS, $p['problems'] );
    }

    /** CF-07 */
    public function testTheTargetOfTheBrokenRelations()
    {
        $this->assertSame( array( 'enabled' => false, 'only' => false ), expContentCheckFiles::target( array( 'deleted', 'trash' ) ) );
        $this->assertSame( array( 'enabled' => false, 'only' => false ), expContentCheckFiles::target( null ) );
        $this->assertSame( array( 'enabled' => false, 'only' => false ), expContentCheckFiles::target( 'missing_file' ), 'a list only' );
        $this->assertSame( array( 'enabled' => true, 'only' => false ), expContentCheckFiles::target( array( 'deleted', 'missing_file' ) ) );
        $this->assertSame( array( 'enabled' => true, 'only' => true ), expContentCheckFiles::target( array( 'missing_file', 'x', array( 'deleted' ) ) ) );

        // every target ticked is named, so that missing_file alone keeps meaning "only the missing files"
        $defaults = expContentCheckFiles::parameters( array(), 2 );
        $this->assertSame( '/(exclude)/5/(kind)/deleted,trash,missing_file',
                           expContentCheckFiles::querySuffix( '/(exclude)/5/(kind)/deleted', array( 'deleted', 'trash' ), $defaults ) );
        $this->assertSame( '/(kind)/missing_file', expContentCheckFiles::querySuffix( '', array(), $defaults ) );
        $this->assertSame( '/(kind)/missing_file/(problem)/none/(version)/none',
                           expContentCheckFiles::querySuffix( '', array(), array( 'problems' => array(), 'versions' => array() ) ) );
        $this->assertSame( '/(kind)/missing_file/(problem)/missing/(version)/draft',
                           expContentCheckFiles::querySuffix( '', array(), expContentCheckFiles::parameters( array( 'Problem' => array( 'missing' ), 'Version' => array( 'draft' ) ), 2 ) ) );
        $this->assertNotContains( expContentCheckFiles::TARGET, expContentCheckBrokenRelations::KINDS, 'never a state of a relation target' );

        $root = dirname( __DIR__, 4 ) . '/';
        foreach ( array( 'missingfiles', 'missingfiles_filter', 'file_row' ) as $name )
        {
            $admin4 = $root . 'design/admin4/templates/contentcheck/' . $name . '.tpl';
            $this->assertFileExists( $admin4 );
            $this->assertFileEquals( $admin4, $root . 'design/admin/templates/contentcheck/' . $name . '.tpl', $name . ': the same in both designs' );
            $this->assertDoesNotMatchRegularExpression( '/<style|style="/i', (string)file_get_contents( $admin4 ), $name . ': no inline CSS' );
        }
        $page = (string)file_get_contents( $root . 'design/admin4/templates/contentcheck/brokenrelations.tpl' );
        $this->assertStringContainsString( 'value="missing_file"', $page, 'the target is offered on the broken relations' );
        $this->assertStringContainsString( "design:contentcheck/missingfiles.tpl", $page );
    }

    /** CF-02 */
    public function testKeyList()
    {
        $this->assertSame( array( '12-3' => array( 12, 3 ), '14-1' => array( 14, 1 ) ),
                           expContentCheckFiles::keyList( array( '12-3', ' 14-1 ', '12-3', '12', '0-1', '5-0', 'a-1', '1-2-3', array( '9-9' ), "1-1' --" ) ) );
        $this->assertSame( array( '1-1' => array( 1, 1 ), '2-2' => array( 2, 2 ) ), expContentCheckFiles::keyList( '1-1, 2-2;3-3', 2 ) );
        $this->assertSame( array(), expContentCheckFiles::keyList( null ) );
        $this->assertSame( '12-3', expContentCheckFiles::key( '12', 3 ) );
        $this->assertSame( 'contentcheck.review.12.3', expContentCheckFiles::reviewName( '12-3' ) );
    }

    /** CF-03 */
    public function testImageFiles()
    {
        $files = expContentCheckFileImage::filesOfXML( self::IMAGE_XML );
        $this->assertSame( array( 'original', 'alias', 'alias', 'alias' ), array_column( $files, 'role' ) );
        $this->assertSame( array( '', 'small', 'large', 'evil' ), array_column( $files, 'alias' ) );
        $this->assertSame( 'var/site/storage/images/a/12-1-eng-GB/a.jpg', $files[0]['path'] );
        $this->assertSame( 'photo.jpg', $files[0]['name'] );
        $this->assertSame( 'var/site/storage/images/a/12-1-eng-GB/a_small.jpg', $files[1]['path'], 'the first entry of a name counts' );
        $this->assertSame( '', $files[3]['path'], 'a path out of the installation is dropped' );
        $this->assertSame( array(), expContentCheckFileImage::filesOfXML( '' ) );
        $this->assertSame( array(), expContentCheckFileImage::filesOfXML( '<ezimage url=' ) );
        $this->assertSame( array(), expContentCheckFileImage::filesOfXML( '<?xml version="1.0"?><ezimage url=""/>' ), 'an empty image field' );
        $this->assertSame( array(), expContentCheckFileImage::filesOfXML( '<?xml version="1.0"?><other url="a.jpg"/>' ) );
        $this->assertSame( '', expContentCheckFileImage::cleanPath( 'http://example.com/a.jpg' ) );
        $this->assertSame( '', expContentCheckFileImage::cleanPath( "var/a\0.jpg" ) );
        $this->assertSame( '', expContentCheckFileImage::cleanPath( 'var/../../a.jpg' ) );
        $this->assertSame( 'var/a..b.jpg', expContentCheckFileImage::cleanPath( '/var/a..b.jpg' ) );
    }

    /**
     * @param array $present path => mtime
     * @return array the two lookups
     */
    protected function storage( array $present )
    {
        return array( function ( $path ) use ( $present ) { return isset( $present[$path] ); },
                      function ( $path ) use ( $present ) { return isset( $present[$path] ) ? $present[$path] : null; } );
    }

    /** CF-04 */
    public function testJudging()
    {
        $files = expContentCheckFileImage::filesOfXML( self::IMAGE_XML );
        $dir = 'var/site/storage/images/a/12-1-eng-GB/';
        $keys = array( 'small' => '111', 'large' => '222', 'medium' => '444' );

        // the original is missing: one problem, the aliases are not looked at
        list( $exists, $mtime ) = $this->storage( array( $dir . 'a_small.jpg' => 5 ) );
        $p = expContentCheckFiles::judge( $files, $exists, $mtime, $keys, array( 'medium' ), 0 );
        $this->assertCount( 1, $p );
        $this->assertSame( 'missing', $p[0]['problem'] );
        $this->assertSame( $dir . 'a.jpg', $p[0]['path'] );

        // everything there and fresh: nothing
        list( $exists, $mtime ) = $this->storage( array( $dir . 'a.jpg' => 100, $dir . 'a_small.jpg' => 200, $dir . 'a_large.jpg' => 200 ) );
        $this->assertSame( array(), expContentCheckFiles::judge( $files, $exists, $mtime, $keys, array(), 0 ) );

        // one alias missing, one never made (asked for), "evil" is not in image.ini and ignored
        list( $exists, $mtime ) = $this->storage( array( $dir . 'a.jpg' => 100, $dir . 'a_small.jpg' => 200 ) );
        $p = expContentCheckFiles::judge( $files, $exists, $mtime, $keys, array( 'medium', 'small' ), 0 );
        $this->assertSame( 'alias_missing', $p[0]['problem'] );
        $this->assertSame( array( array( 'alias' => 'large', 'state' => 'missing', 'why' => '', 'path' => $dir . 'a_large.jpg' ),
                                  array( 'alias' => 'medium', 'state' => 'absent', 'why' => '', 'path' => '' ) ), $p[0]['aliases'] );

        // other settings now, then expired, then older than the original
        list( $exists, $mtime ) = $this->storage( array( $dir . 'a.jpg' => 100, $dir . 'a_small.jpg' => 200, $dir . 'a_large.jpg' => 200 ) );
        $p = expContentCheckFiles::judge( $files, $exists, $mtime, array( 'small' => '999', 'large' => '222' ), array(), 0 );
        $this->assertSame( 'alias_stale', $p[0]['problem'] );
        $this->assertSame( array( 'small' ), array_column( $p[0]['aliases'], 'alias' ) );
        $this->assertSame( 'settings', $p[0]['aliases'][0]['why'] );
        $p = expContentCheckFiles::judge( $files, $exists, $mtime, $keys, array(), 1700000100 );
        $this->assertSame( array( 'expired', 'expired' ), array_column( $p[0]['aliases'], 'why' ) );
        list( $exists, $mtime ) = $this->storage( array( $dir . 'a.jpg' => 300, $dir . 'a_small.jpg' => 200, $dir . 'a_large.jpg' => 400 ) );
        $p = expContentCheckFiles::judge( $files, $exists, $mtime, $keys, array(), 0 );
        $this->assertSame( array( array( 'alias' => 'small', 'state' => 'stale', 'why' => 'older', 'path' => $dir . 'a_small.jpg' ) ), $p[0]['aliases'] );

        // a file without aliases (binary file, media)
        $file = array( array( 'role' => 'original', 'alias' => '', 'path' => 'var/site/storage/original/application/x.pdf', 'alias_key' => '', 'timestamp' => 0, 'name' => 'x.pdf' ) );
        list( $exists, $mtime ) = $this->storage( array() );
        $this->assertSame( 'missing', expContentCheckFiles::judge( $file, $exists, $mtime, array(), array(), 0 )[0]['problem'] );
        list( $exists, $mtime ) = $this->storage( array( 'var/site/storage/original/application/x.pdf' => 1 ) );
        $this->assertSame( array(), expContentCheckFiles::judge( $file, $exists, $mtime, array(), array(), 0 ) );
        $this->assertSame( array(), expContentCheckFiles::judge( array(), $exists, $mtime, array(), array(), 0 ) );
    }

    /** CF-05 */
    public function testProblemFilter()
    {
        $problem = array( 'problem' => 'alias_missing', 'path' => 'p', 'name' => '', 'aliases' => array(
            array( 'alias' => 'a', 'state' => 'missing', 'why' => '', 'path' => 'p' ),
            array( 'alias' => 'b', 'state' => 'stale', 'why' => 'older', 'path' => 'q' ),
            array( 'alias' => 'c', 'state' => 'absent', 'why' => '', 'path' => '' ) ) );
        $kept = expContentCheckFiles::filterProblem( $problem, array( 'alias_stale' ) );
        $this->assertSame( 'alias_stale', $kept['problem'] );
        $this->assertSame( array( 'b' ), array_column( $kept['aliases'], 'alias' ) );
        $this->assertNull( expContentCheckFiles::filterProblem( $problem, array( 'missing' ) ) );
        $this->assertSame( 'alias_absent', expContentCheckFiles::filterProblem( $problem, array( 'alias_absent' ) )['problem'] );
        $missing = array( 'problem' => 'missing', 'path' => 'p', 'name' => '', 'aliases' => array() );
        $this->assertSame( $missing, expContentCheckFiles::filterProblem( $missing, array( 'missing' ) ) );
        $this->assertNull( expContentCheckFiles::filterProblem( $missing, array( 'alias_missing' ) ) );
    }

    /** CF-06 */
    public function testQueries()
    {
        $sql = expContentCheckFiles::sourceQuery( '/1/2/58/%', array( 'from' => ', ezcontentobject_section_x', 'where' => 'AND ( 1 = 1 )' ), 99 );
        $this->assertStringContainsString( "path_string LIKE '/1/2/58/%'", $sql );
        $this->assertStringContainsString( 'ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id', $sql );
        $this->assertStringContainsString( 'ezcontentobject.status = 1', $sql );
        $this->assertStringContainsString( 'ezcontentobject.id > 99', $sql );
        $this->assertStringContainsString( 'AND ( 1 = 1 )', $sql );
        $this->assertStringContainsString( ', ezcontentobject_section_x', $sql );
        $this->assertStringEndsWith( 'ORDER BY ezcontentobject.id ASC', $sql );
        try
        {
            expContentCheckFiles::sourceQuery( "/1/2/' OR 1=1 --", array(), 0 );
            $this->fail( 'a pattern that is not a subtree path' );
        }
        catch ( InvalidArgumentException $e )
        {
            $this->assertTrue( true );
        }
        $sql = expContentCheckFiles::attributeQuery( array( 3, 4 ), array( 'ezimage', "x'y", 'ezbinaryfile' ), array( 1, 0 ) );
        $this->assertStringContainsString( 'a.contentobject_id IN ( 3, 4 )', $sql );
        $this->assertStringContainsString( "a.data_type_string IN ( 'ezimage', 'ezbinaryfile' )", $sql );
        $this->assertStringContainsString( 'v.status IN ( 1, 0 )', $sql );
        $this->assertSame( array( 1 ), expContentCheckFiles::versionStatuses( array( 'published' ) ) );
        $this->assertSame( array( 1, 0, 2, 5 ), expContentCheckFiles::versionStatuses( array( 'published', 'draft' ) ) );
    }

}
