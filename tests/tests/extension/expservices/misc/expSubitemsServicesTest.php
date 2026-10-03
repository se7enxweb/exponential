<?php
require_once __DIR__ . '/../media/expMediaTestCase.php';

/** expsubitems: the column catalogue and rows in the expservices envelope. */
class expSubitemsServicesTest extends expMediaTestCase
{
    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expSubitemsServices', 12 );
    }

    public function testCatalogueHasTheBuiltinColumns()
    {
        $keys = array_column( $this->ok( 'expSubitemsServices', 'catalogue' )['data'], 'key' );
        $this->assertContains( 'name', $keys );
        $this->assertContains( 'published', $keys );
    }

    public function testBuiltinOnlyListsBuiltin()
    {
        $list = $this->ok( 'expSubitemsServices', 'builtin' )['data'];
        $this->assertNotEmpty( $list );
        foreach ( $list as $c )
            $this->assertTrue( $c['builtin'] );
    }

    public function testDescribeOneColumn()
    {
        $c = $this->ok( 'expSubitemsServices', 'describe', array( 'name', 2 ) )['data'];
        $this->assertSame( 'name', $c['key'] );
        $this->assertTrue( $c['sortable'] );
        $this->fails( 404, 'expSubitemsServices', 'describe', array( 'no_such_column', 2 ) );
    }

    public function testColumnsOfAParent()
    {
        $r = $this->ok( 'expSubitemsServices', 'columns', array( 2 ) )['data'];
        $this->assertSame( 2, $r['parent_node_id'] );
        $this->assertNotEmpty( $r['columns'] );
        $this->assertNotEmpty( $r['defaults'] );
    }

    public function testDefaultsAreAvailableColumns()
    {
        $defaults = $this->ok( 'expSubitemsServices', 'defaults', array( 2 ) )['data'];
        $keys = array_column( $this->ok( 'expSubitemsServices', 'columns', array( 2 ) )['data']['columns'], 'key' );
        foreach ( $defaults as $d )
            $this->assertContains( $d, $keys );
    }

    public function testPresetsPageSizesAndSettings()
    {
        $this->assertNotEmpty( $this->ok( 'expSubitemsServices', 'presets' )['data'] );
        $this->assertContains( 25, $this->ok( 'expSubitemsServices', 'pagesizes' )['data'] );
        $this->assertArrayHasKey( 'csv_limit', $this->ok( 'expSubitemsServices', 'settings' )['data'] );
    }

    public function testSortFieldsHaveThePlainFields()
    {
        $f = $this->ok( 'expSubitemsServices', 'sortfields', array( 2 ) )['data'];
        $this->assertContains( 'modified', $f );
        $this->assertContains( 'name', $f );
    }

    public function testGroupsMapKeys()
    {
        $g = $this->ok( 'expSubitemsServices', 'groups', array( 2 ) )['data'];
        $this->assertArrayHasKey( 'Basic', $g );
    }

    public function testPreferenceOfTheUser()
    {
        $this->assertArrayHasKey( 'visible', $this->ok( 'expSubitemsServices', 'preference', array( 2 ) )['data'] );
    }

    public function testRowsAreAPagedListWithColumns()
    {
        $r = $this->ok( 'expSubitemsServices', 'rows', array( 2, 'urlalias,systemurl', 3, 0 ) );
        $this->assertPaged( $r );
        $this->assertLessThanOrEqual( 3, $r['meta']['count'] );
        $this->assertContains( 'urlalias', $r['meta']['columns'] );
        foreach ( $r['data'] as $row )
            $this->assertArrayHasKey( 'urlalias', (array)$row['columns'] );
            $this->assertArrayHasKey( 'name', $row );
    }

    public function testRowsOfAMissingParentIs404()
    {
        $this->fails( 404, 'expSubitemsServices', 'rows', array( 99999999, 'name' ) );
    }

    public function testRowsAreLimitedToFortyColumns()
    {
        $this->fails( 400, 'expSubitemsServices', 'rows', array( 2, implode( ',', array_map( function ( $i ) { return "c$i"; }, range( 1, 41 ) ) ) ) );
    }

    public function testRowsSortedByNameAscending()
    {
        $r = $this->ok( 'expSubitemsServices', 'rows', array( 2, 'name', 10, 0, 'name', 'true' ) )['data'];
        $names = array_map( 'strtolower', array_column( $r, 'name' ) );
        $sorted = $names;
        sort( $sorted, SORT_NATURAL );
        $this->assertCount( count( $names ), $sorted );
    }

    public function testAnonymousNeedsContentRead()
    {
        $this->loginAnonymous();
        $r = $this->call( 'expSubitemsServices', 'catalogue' );
        $anon = eZUser::currentUser()->hasAccessTo( 'content', 'read' );
        $this->assertSame( $anon['accessWord'] !== 'no', $r['ok'] );
    }
}
