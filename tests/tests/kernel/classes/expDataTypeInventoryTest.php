<?php
/**
 * File containing the expDataTypeInventoryTest class.
 *
 * The list of class/datatype without a database: a registry and counts of the test's own go through build(),
 * filter() and summary(); the location of a datatype file, its provider, the view parameters of the list, the
 * flags of a datatype object and the reading of a datatype's source.
 *
 *     php vendor/bin/phpunit --filter expDataTypeInventoryTest
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\ContentClass\Datatype;

class expDataTypeInventoryTestType extends eZDataType
{
    public function __construct()
    {
        parent::__construct( 'exptestdt', 'Test type', array( 'translation_allowed' => false, 'serialize_supported' => true ) );
    }

    function isIndexable() { return true; }
    function sortKeyType() { return 'string'; }
    public function isRelationType() { return true; }
}

class expDataTypeInventoryTest extends ezpTestCase
{
    private static function registered( $string, $provider, $class = 'eZStringType' )
    {
        return array( 'string' => $string, 'name' => ucfirst( $string ), 'class' => $class,
                      'file' => ( $provider === 'kernel' ? 'kernel/classes/datatypes' : "extension/$provider/datatypes" ) . "/$string/{$string}type.php",
                      'provider' => $provider, 'registered' => true, 'created' => true, 'flags' => expDataTypeInventory::flagsOf( null ) );
    }

    private function fixture()
    {
        $registry = array(
            'ezstring' => self::registered( 'ezstring', 'kernel' ),
            'ezfloat' => self::registered( 'ezfloat', 'kernel' ),
            'eztags' => self::registered( 'eztags', 'eztags' ),
            'ezgmaplocation' => self::registered( 'ezgmaplocation', 'ezgmaplocation' ),
            'ezgone' => array( 'string' => 'ezgone', 'name' => '', 'class' => false, 'file' => '', 'provider' => '',
                               'registered' => false, 'created' => false, 'flags' => expDataTypeInventory::flagsOf( null ) ),
        );
        $classUsage = array( 'ezstring' => array( 'class_attributes' => 10, 'classes' => 4 ),
                             'eztags' => array( 'class_attributes' => 2, 'classes' => 2 ),
                             'oldtype' => array( 'class_attributes' => 3, 'classes' => 1 ) );
        $objectUsage = array( 'ezstring' => array( 'object_attributes' => 500, 'objects' => 120 ),
                              'oldtype' => array( 'object_attributes' => 40, 'objects' => 20 ),
                              'objectonly' => array( 'object_attributes' => 5, 'objects' => 5 ) );
        return expDataTypeInventory::build( $registry, $classUsage, $objectUsage );
    }

    public function testGroupsComeKernelExtensionsThenOrphans()
    {
        $built = $this->fixture();
        $this->assertSame( array( 'kernel', 'ezgmaplocation', 'eztags', 'orphans' ), array_keys( $built['groups'] ) );
        $this->assertSame( 'kernel', $built['groups']['kernel']['kind'] );
        $this->assertSame( 'extension', $built['groups']['eztags']['kind'] );
        $this->assertSame( 'orphans', $built['groups']['orphans']['kind'] );
    }

    public function testStatusAndCounts()
    {
        $built = $this->fixture();
        $rows = array();
        foreach ( $built['groups'] as $group )
            foreach ( $group['rows'] as $row )
                $rows[$row['string']] = $row;
        $this->assertSame( 'used', $rows['ezstring']['status'] );
        $this->assertSame( 500, $rows['ezstring']['object_attributes'] );
        $this->assertSame( 120, $rows['ezstring']['objects'] );
        $this->assertSame( 4, $rows['ezstring']['classes'] );
        $this->assertSame( 'unused', $rows['ezfloat']['status'] );
        $this->assertSame( 'used', $rows['eztags']['status'] );
        $this->assertSame( 'unused', $rows['ezgmaplocation']['status'] );
        $this->assertSame( 'missing', $rows['ezgone']['status'] );
        $this->assertSame( 'orphan', $rows['oldtype']['status'] );
        $this->assertSame( 'orphan', $rows['objectonly']['status'] );
        $this->assertFalse( $rows['oldtype']['listed'] );
        $this->assertStringContainsString( 'eztags', $rows['eztags']['search'] );
        $this->assertSame( array( 'ezgone', 'objectonly', 'oldtype' ), array_column( $built['groups']['orphans']['rows'], 'string' ) );
    }

    public function testSummary()
    {
        $summary = $this->fixture()['summary'];
        $this->assertSame( 7, $summary['datatypes'] );
        $this->assertSame( 4, $summary['registered'] );
        $this->assertSame( 2, $summary['used'] );
        $this->assertSame( 2, $summary['unused'] );
        $this->assertSame( 2, $summary['orphans'] );
        $this->assertSame( 1, $summary['missing'] );
        $this->assertSame( 2, $summary['extensions'] );
        $this->assertSame( 15, $summary['class_attributes'] );
        $this->assertSame( 545, $summary['object_attributes'] );
        $this->assertSame( 3, $summary['orphan_class_attributes'] );
        $this->assertSame( 45, $summary['orphan_object_attributes'] );
    }

    public function testFilter()
    {
        $groups = $this->fixture()['groups'];
        $used = expDataTypeInventory::filter( $groups, 'used' );
        $this->assertSame( array( 'kernel', 'eztags' ), array_keys( $used ) );
        $this->assertSame( 1, $used['kernel']['shown'] );
        $this->assertSame( 2, $used['kernel']['count'] );
        $unused = expDataTypeInventory::filter( $groups, 'unused' );
        $this->assertSame( array( 'kernel', 'ezgmaplocation', 'orphans' ), array_keys( $unused ) );
        $this->assertSame( array( 'ezgone' ), array_column( $unused['orphans']['rows'], 'string' ) );
        $orphans = expDataTypeInventory::filter( $groups, 'orphans' );
        $this->assertSame( array( 'orphans' ), array_keys( $orphans ) );
        $this->assertSame( 3, $orphans['orphans']['shown'] );
        $this->assertSame( array( 'eztags' ), array_keys( expDataTypeInventory::filter( $groups, 'all', 'eztags' ) ) );
        $this->assertSame( array_keys( $groups ), array_keys( expDataTypeInventory::filter( $groups, 'nonsense' ) ) );
        $this->assertSame( array(), expDataTypeInventory::filter( $groups, 'used', 'ezgmaplocation' ) );
    }

    public function testListParametersAndURL()
    {
        $keys = array( 'kernel', 'eztags', 'orphans' );
        $this->assertSame( array( 'filter' => 'all', 'group' => '', 'refresh' => false ), Datatype::listParameters( array(), $keys ) );
        $this->assertSame( array( 'filter' => 'unused', 'group' => 'eztags', 'refresh' => true ),
                           Datatype::listParameters( array( 'filter' => 'unused', 'group' => 'eztags', 'refresh' => '1' ), $keys ) );
        $this->assertSame( array( 'filter' => 'all', 'group' => '', 'refresh' => false ),
                           Datatype::listParameters( array( 'filter' => '<script>', 'group' => '../etc' ), $keys ) );
        $this->assertSame( 'class/datatype/list', Datatype::listURL( 'all', '' ) );
        $this->assertSame( 'class/datatype/list/(filter)/orphans/(group)/kernel', Datatype::listURL( 'orphans', 'kernel' ) );
    }

    public function testLocateAndProvider()
    {
        $dirs = array( array( 'dir' => 'kernel/classes/datatypes', 'provider' => 'kernel' ),
                       array( 'dir' => 'extension/eztags/datatypes', 'provider' => 'eztags' ) );
        $files = array( 'extension/eztags/datatypes/eztags/eztagstype.php' => true, 'kernel/classes/datatypes/ezstring/ezstringtype.php' => true );
        $exists = function ( $path ) use ( $files ) { return isset( $files[$path] ); };
        $this->assertSame( array( 'file' => 'kernel/classes/datatypes/ezstring/ezstringtype.php', 'provider' => 'kernel' ),
                           expDataTypeInventory::locate( 'ezstring', $dirs, $exists ) );
        $this->assertSame( 'eztags', expDataTypeInventory::locate( 'eztags', $dirs, $exists )['provider'] );
        $this->assertFalse( expDataTypeInventory::locate( 'ezgone', $dirs, $exists ) );
        $this->assertSame( 'kernel', expDataTypeInventory::providerOfPath( 'kernel/classes/datatypes' ) );
        $this->assertSame( 'cjw_newsletter', expDataTypeInventory::providerOfPath( '/srv/site/extension/cjw_newsletter/datatypes/x/xtype.php' ) );
        $this->assertSame( 'myext', expDataTypeInventory::directories( array( 'extension/myext/datatypes', 'kernel/classes/datatypes', 'kernel/classes/datatypes' ), array() )[0]['provider'] );
        $this->assertCount( 2, expDataTypeInventory::directories( array( 'extension/myext/datatypes', 'kernel/classes/datatypes', 'kernel/classes/datatypes' ), array() ) );
    }

    public function testFlagsOfADatatypeObject()
    {
        $flags = expDataTypeInventory::flagsOf( new expDataTypeInventoryTestType() );
        $this->assertFalse( $flags['translatable'] );
        $this->assertTrue( $flags['indexable'] );
        $this->assertSame( 'string', $flags['sortable'] );
        $this->assertTrue( $flags['relation'] );
        $this->assertTrue( $flags['serialize'] );
        $this->assertFalse( $flags['information_collector'] );
        $none = expDataTypeInventory::flagsOf( null );
        $this->assertSame( '', $none['sortable'] );
        $this->assertFalse( $none['translatable'] );
        $row = expDataTypeInventory::describe( 'exptestdt', array( 'file' => 'extension/x/datatypes/exptestdt/exptestdttype.php', 'provider' => 'x' ),
                                               'expDataTypeInventoryTestType', new expDataTypeInventoryTestType() );
        $this->assertSame( 'Test type', $row['name'] );
        $this->assertTrue( $row['created'] );
        $this->assertSame( 'kernel', $row['provider'], 'the class file of the test type is not in an extension' );
    }

    public function testReadSource()
    {
        $source = "\$a->setAttribute( 'data_int1', 3 ); \$b = \$c->attribute( 'data_text' ); 'sort_key_string';\n"
                . "\$db->query( 'DELETE FROM ezimagefile WHERE id = 1' ); \$db->arrayQuery( 'SELECT * FROM ezcontentobject_attribute' );\n"
                . "return array( 'name' => 'eztags_attribute_link' ); 'name' => 'Not a table';";
        $read = expDataTypeInventory::readSource( $source );
        $this->assertSame( array( 'data_int1' ), $read['settings'] );
        $this->assertSame( array( 'data_text', 'sort_key_string' ), $read['content'] );
        $this->assertSame( array( 'ezimagefile', 'eztags_attribute_link' ), $read['tables'] );
        $this->assertSame( array( 'settings' => array(), 'content' => array(), 'tables' => array() ), expDataTypeInventory::readSource( '' ) );
    }

    public function testNodeParametersDefaultsAndChecks()
    {
        $this->assertSame( array( 'sort' => 'modified', 'order' => 'desc', 'offset' => 0, 'limit' => 25, 'class' => 0 ),
                           expDataTypeInventory::nodeParameters( array(), array( 3, 7 ) ) );
        $this->assertSame( array( 'sort' => 'name', 'order' => 'asc', 'offset' => 50, 'limit' => 50, 'class' => 7 ),
                           expDataTypeInventory::nodeParameters( array( 'sort' => 'name', 'offset' => '50', 'limit' => '50', 'class' => '7' ), array( 3, 7 ) ) );
        $this->assertSame( array( 'sort' => 'published', 'order' => 'asc', 'offset' => 0, 'limit' => 100, 'class' => 0 ),
                           expDataTypeInventory::nodeParameters( array( 'sort' => 'published', 'order' => 'ASC', 'limit' => '100' ), array( 3 ) ) );
        // anything else falls back: an unknown sort, order or page size, a negative offset, a class that does not use the datatype
        $this->assertSame( array( 'sort' => 'modified', 'order' => 'desc', 'offset' => 0, 'limit' => 25, 'class' => 0 ),
                           expDataTypeInventory::nodeParameters( array( 'sort' => 'owner', 'order' => 'up', 'offset' => '-5', 'limit' => '33', 'class' => '9' ), array( 3, 7 ) ) );
        $this->assertSame( 'asc', expDataTypeInventory::nodeParameters( array( 'sort' => 'class' ), array() )['order'] );
    }

    public function testNodeListURLsCarryOnlyWhatDiffersFromTheDefaults()
    {
        $default = expDataTypeInventory::nodeParameters( array(), array( 3, 7 ) );
        $this->assertSame( 'class/datatype/view/xrowmetadata', expDataTypeInventory::nodeListURL( 'xrowmetadata', $default ) );
        $this->assertSame( array(), expDataTypeInventory::nodeViewParameters( $default ) );
        $p = expDataTypeInventory::nodeParameters( array( 'sort' => 'name', 'order' => 'desc', 'limit' => '10', 'class' => '7', 'offset' => '20' ), array( 3, 7 ) );
        $this->assertSame( 'class/datatype/view/xrowmetadata/(sort)/name/(order)/desc/(limit)/10/(class)/7', expDataTypeInventory::nodeListURL( 'xrowmetadata', $p ) );
        $this->assertSame( 'class/datatype/view/xrowmetadata/(sort)/name/(order)/desc/(limit)/10/(class)/7/(offset)/30',
                           expDataTypeInventory::nodeListURL( 'xrowmetadata', $p, array( 'offset' => 30 ) ) );
        $this->assertSame( 'class/datatype/view/xrowmetadata/(class)/7', expDataTypeInventory::nodeListURL( 'xrowmetadata', $p, array( 'sort' => 'modified', 'order' => 'desc', 'limit' => 25 ) ) );
    }

    public function testNodeSortLinksReverseTheCurrentColumn()
    {
        $p = expDataTypeInventory::nodeParameters( array( 'class' => '3' ), array( 3 ) );
        $links = expDataTypeInventory::nodeSortLinks( 'ezstring', $p );
        $this->assertSame( array( 'modified', 'published', 'name', 'class' ), array_keys( $links ) );
        $this->assertTrue( $links['modified']['current'] );
        $this->assertSame( 'class/datatype/view/ezstring/(sort)/modified/(order)/asc/(class)/3', $links['modified']['url'] );
        $this->assertFalse( $links['name']['current'] );
        $this->assertSame( 'class/datatype/view/ezstring/(sort)/name/(order)/asc/(class)/3', $links['name']['url'] );
        $this->assertSame( 'class/datatype/view/ezstring/(sort)/published/(order)/desc/(class)/3', $links['published']['url'] );
    }

    public function testNodeFetchParameters()
    {
        $p = expDataTypeInventory::nodeParameters( array( 'sort' => 'class', 'offset' => '25' ), array( 3, 7 ) );
        $fetch = expDataTypeInventory::nodeFetchParameters( array( 3, 7 ), $p, array() );
        $this->assertSame( array( 3, 7 ), $fetch['ClassFilterArray'] );
        $this->assertTrue( $fetch['MainNodeOnly'] );
        $this->assertSame( array( array( 'class_name', true ), array( 'name', true ) ), $fetch['SortBy'] );
        $this->assertSame( 25, $fetch['Offset'] );
        $this->assertSame( 25, $fetch['Limit'] );
        $this->assertSame( array(), $fetch['Limitation'] );
        $own = expDataTypeInventory::nodeFetchParameters( array( 3, 7 ), expDataTypeInventory::nodeParameters( array( 'class' => '7' ), array( 3, 7 ) ) );
        $this->assertSame( array( 7 ), $own['ClassFilterArray'] );
        $this->assertArrayNotHasKey( 'Limitation', $own, 'without a limitation given the read check of the current user applies' );
        $this->assertSame( array( array( 'modified', false ), array( 'node_id', false ) ), $own['SortBy'] );
    }

    public function testTheViewIsInTheModule()
    {
        $Module = null;
        $ViewList = array();
        include __DIR__ . '/../../../../kernel/class/module.php';
        $this->assertSame( 'datatype.php', $ViewList['datatype']['script'] );
        $this->assertSame( array( 'Action', 'DataTypeString' ), $ViewList['datatype']['params'] );
        $this->assertFileExists( __DIR__ . '/../../../../kernel/class/datatype.php' );
    }
}
