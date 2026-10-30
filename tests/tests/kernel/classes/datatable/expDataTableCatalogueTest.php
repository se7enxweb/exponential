<?php
/**
 * The catalogue of data tables (expDataTableCatalogue; doc/eng-us/features/6.0/data-tables.md, "The catalogue"). No
 * database: entryFromDefinition() and the rules are pure; instance() reads this checkout's kernel autoload array and
 * the definitions of its classes, and writes its cache file, which the test removes.
 *
 *  CT-01 - entryFromDefinition(): table, fields with type, default, required, short name; keys, increment key, sort
 *          (lower case), function attribute names, the connection ('site' is the site's own: '')
 *  CT-02 - relations from the definition's 'relations' (kind from the target class, or given), from a field's
 *          foreign_class, from the settings, and inferred from names, each marked by its source; the first source
 *          wins for a column; a relation to another table is kind table
 *  CT-03 - the table's own increment key is not inferred as a relation; inferring can be switched off
 *  CT-04 - inferRelation() and kindOfClass()
 *  CT-05 - declaresDefinition(): a class file with definition() and extends; not one without
 *  CT-06 - instance() on this checkout finds eZContentObject, eZViewCounter (node_id -> node, declared),
 *          expKernelTransferRun, expSecret and expDataConnection; no namespaced class and no class of tests/
 *  CT-07 - byTable(), byOrigin() (kernel first), pointingAt(), entry() without case
 *  CT-08 - a class whose definition() throws is listed with the error; a class that is not persistent, abstract, or
 *          inherits its parent's definition() is not listed
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDataTableTestLoader.php';

class expDataTableCatalogueTestBroken extends eZPersistentObject
{
    public static function definition()
    {
        throw new RuntimeException( 'needs a request' );
    }
}

class expDataTableCatalogueTestOk extends eZPersistentObject
{
    public static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID', 'datatype' => 'integer' ) ), 'keys' => array( 'id' ),
                      'class_name' => 'expDataTableCatalogueTestOk', 'name' => 't691_ok' );
    }
}

class expDataTableCatalogueTestChild extends expDataTableCatalogueTestOk
{
}

class expDataTableCatalogueTestPlain
{
    public static function definition()
    {
        return array( 'fields' => array( 'id' => 'ID' ), 'name' => 'x' );
    }
}

class expDataTableCatalogueTest extends PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        expDataTableCatalogue::reset();
    }

    private static function definition()
    {
        return array(
            'fields' => array(
                'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'title' => array( 'name' => 'Title', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'contentobject_attribute_version' => array( 'name' => 'Version', 'datatype' => 'integer', 'default' => 0, 'required' => true,
                                                            'short_name' => 'coa_version' ),
                'node_id' => array( 'name' => 'NodeID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'owner_id' => array( 'name' => 'OwnerID', 'datatype' => 'integer', 'default' => 0, 'required' => true,
                                     'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*' ),
                'contentobject_id' => array( 'name' => 'ObjectID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'parent_row' => array( 'name' => 'ParentRow', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'section_id' => 'SectionID',
                'note' => array( 'name' => 'Note', 'datatype' => 'text' ),
            ),
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'function_attributes' => array( 'object' => 'object', 'node' => 'node' ),
            'relations' => array( 'node_id' => array( 'class' => 'eZContentObjectTreeNode', 'field' => 'node_id' ),
                                  'parent_row' => array( 'class' => 'myOtherRow', 'field' => 'id' ),
                                  'nosuchfield' => array( 'class' => 'eZContentObject' ) ),
            'sort' => array( 'title' => 'ASC' ),
            'class_name' => 'myRow',
            'connection' => 'archive',
            'name' => 'my_row',
        );
    }

    private static function relationsByColumn( array $entry )
    {
        $out = array();
        foreach ( $entry['relations'] as $r )
            $out[$r['column']] = $r;
        return $out;
    }

    /** CT-01 */
    public function testEntryFromDefinition()
    {
        $e = expDataTableCatalogue::entryFromDefinition( 'myRow', self::definition(), 'extension/x/classes/myrow.php', 'x' );
        $this->assertSame( 'myRow', $e['class'] );
        $this->assertSame( 'my_row', $e['table'] );
        $this->assertSame( 'x', $e['origin'] );
        $this->assertSame( 'extension/x/classes/myrow.php', $e['file'] );
        $this->assertSame( array( 'id' ), $e['keys'] );
        $this->assertSame( 'id', $e['increment_key'] );
        $this->assertSame( array( 'title' => 'asc' ), $e['sort'] );
        $this->assertSame( array( 'object', 'node' ), $e['function_attributes'] );
        $this->assertSame( 'archive', $e['connection'] );
        $this->assertSame( '', $e['error'] );
        $this->assertSame( 'integer', $e['fields']['id']['datatype'] );
        $this->assertTrue( $e['fields']['title']['required'] );
        $this->assertSame( 'coa_version', $e['fields']['contentobject_attribute_version']['short_name'] );
        $this->assertSame( 'SectionID', $e['fields']['section_id']['label'], 'a field given as a plain name' );
        $this->assertSame( 'string', $e['fields']['section_id']['datatype'] );
        $this->assertNull( $e['fields']['note']['default'] );
        $definition = self::definition();
        $definition['connection'] = 'site';
        $this->assertSame( '', expDataTableCatalogue::entryFromDefinition( 'myRow', $definition )['connection'] );
        unset( $definition['connection'] );
        $this->assertSame( '', expDataTableCatalogue::entryFromDefinition( 'myRow', $definition )['connection'] );
    }

    /** CT-02 */
    public function testRelations()
    {
        $settings = array( 'contentobject_id' => array( 'kind' => 'object', 'class' => 'eZContentObject', 'field' => 'id' ),
                           'node_id' => array( 'kind' => 'user', 'class' => 'eZUser', 'field' => 'x' ) );
        $e = expDataTableCatalogue::entryFromDefinition( 'myRow', self::definition(), '', 'kernel', true, $settings );
        $r = self::relationsByColumn( $e );
        $this->assertSame( array( 'node_id', 'parent_row', 'owner_id', 'contentobject_id', 'section_id' ), array_keys( $r ) );
        $this->assertSame( array( 'column' => 'node_id', 'kind' => 'node', 'class' => 'eZContentObjectTreeNode', 'field' => 'node_id', 'source' => 'declared' ), $r['node_id'], 'the definition wins over the settings' );
        $this->assertSame( 'table', $r['parent_row']['kind'] );
        $this->assertSame( 'myOtherRow', $r['parent_row']['class'] );
        $this->assertSame( array( 'column' => 'owner_id', 'kind' => 'user', 'class' => 'eZUser', 'field' => 'contentobject_id', 'source' => 'foreign' ), $r['owner_id'] );
        $this->assertSame( 'settings', $r['contentobject_id']['source'] );
        $this->assertSame( 'object', $r['contentobject_id']['kind'] );
        $this->assertSame( array( 'kind' => 'section', 'source' => 'inferred' ), array_intersect_key( $r['section_id'], array( 'kind' => 1, 'source' => 1 ) ) );
        $this->assertArrayNotHasKey( 'nosuchfield', $r, 'a relation of a column that is not a field is left out' );
        // the declared kind is kept
        $definition = self::definition();
        $definition['relations']['parent_row']['kind'] = 'object';
        $this->assertSame( 'object', self::relationsByColumn( expDataTableCatalogue::entryFromDefinition( 'myRow', $definition ) )['parent_row']['kind'] );
    }

    /** CT-03 */
    public function testOwnKeyAndNoInference()
    {
        $definition = array( 'fields' => array( 'node_id' => array( 'datatype' => 'integer' ), 'contentobject_id' => array( 'datatype' => 'integer' ) ),
                             'keys' => array( 'node_id' ), 'increment_key' => 'node_id', 'name' => 'tree' );
        $r = self::relationsByColumn( expDataTableCatalogue::entryFromDefinition( 'Tree', $definition ) );
        $this->assertSame( array( 'contentobject_id' ), array_keys( $r ), 'the own counted key is not a relation' );
        $this->assertSame( array(), expDataTableCatalogue::entryFromDefinition( 'Tree', $definition, '', 'kernel', false )['relations'] );
        // a key that is not counted up (a link table) may still point at content
        $definition['increment_key'] = '';
        $definition['keys'] = array( 'node_id', 'contentobject_id' );
        $this->assertSame( array( 'node_id', 'contentobject_id' ), array_keys( self::relationsByColumn( expDataTableCatalogue::entryFromDefinition( 'Tree', $definition ) ) ) );
    }

    /** CT-04 */
    public function testInferAndKinds()
    {
        $cases = array( 'contentobject_id' => 'object', 'object_id' => 'object', 'from_contentobject_id' => 'object', 'related_object_id' => 'object',
                        'node_id' => 'node', 'main_node_id' => 'node', 'parent_node_id' => 'node', 'target_node_id' => 'node',
                        'contentclass_id' => 'class', 'class_id' => 'class', 'user_id' => 'user', 'creator_id' => 'user', 'owner_id' => 'user',
                        'modifier_id' => 'user', 'section_id' => 'section', 'NODE_ID' => 'node' );
        foreach ( $cases as $column => $kind )
            $this->assertSame( $kind, expDataTableCatalogue::inferRelation( $column )['kind'], $column );
        foreach ( array( 'id', 'title', 'node', 'nodes_id', 'identifier', 'class_identifier', 'version' ) as $column )
            $this->assertNull( expDataTableCatalogue::inferRelation( $column ), $column );
        $this->assertSame( array( 'kind' => 'node', 'class' => 'eZContentObjectTreeNode', 'field' => 'node_id' ), expDataTableCatalogue::inferRelation( 'node_id' ) );
        $this->assertSame( array( 'kind' => 'user', 'class' => 'eZUser', 'field' => 'contentobject_id' ), expDataTableCatalogue::inferRelation( 'user_id' ) );
        foreach ( array( 'eZContentObject' => 'object', 'ezcontentobjecttreenode' => 'node', 'eZContentClass' => 'class', 'eZUser' => 'user',
                         'eZSection' => 'section', 'expSecret' => 'table', '' => 'table' ) as $class => $kind )
            $this->assertSame( $kind, expDataTableCatalogue::kindOfClass( $class ), $class );
    }

    /** CT-05 */
    public function testDeclaresDefinition()
    {
        $this->assertTrue( expDataTableCatalogue::declaresDefinition( "<?php\nclass A extends eZPersistentObject {\n static function definition() {} }" ) );
        $this->assertTrue( expDataTableCatalogue::declaresDefinition( "class A extends B { public static function Definition() {} }" ) );
        $this->assertFalse( expDataTableCatalogue::declaresDefinition( "class A { static function definition() {} }" ) );
        $this->assertFalse( expDataTableCatalogue::declaresDefinition( "class A extends B { function other() {} }" ) );
        $this->assertFalse( expDataTableCatalogue::declaresDefinition( '' ) );
    }

    /** CT-06 */
    public function testInstanceOnThisCheckout()
    {
        $catalogue = $this->rebuilt();
        $classes = $catalogue->classes();
        foreach ( array( 'eZContentObject' => 'ezcontentobject', 'eZViewCounter' => 'ezview_counter', 'expKernelTransferRun' => 'exp_transfer_run',
                         'expSecret' => 'exp_secret', 'expDataConnection' => 'exp_data_connection' ) as $class => $table )
        {
            $this->assertArrayHasKey( $class, $classes, $class );
            $this->assertSame( $table, $classes[$class]['table'], $class );
            $this->assertSame( 'kernel', $classes[$class]['origin'], $class );
            $this->assertSame( '', $classes[$class]['error'], $class );
        }
        $viewCounter = self::relationsByColumn( $classes['eZViewCounter'] );
        $this->assertSame( 'node', $viewCounter['node_id']['kind'] );
        $this->assertSame( 'declared', $viewCounter['node_id']['source'] );
        $secret = self::relationsByColumn( $classes['expSecret'] );
        $this->assertSame( 'foreign', $secret['creator_id']['source'] );
        foreach ( $classes as $class => $entry )
        {
            $this->assertStringNotContainsString( '\\', $class );
            $this->assertStringNotContainsString( 'tests/', $entry['file'], $class );
            $this->assertStringStartsNotWith( 'expDataTableCatalogueTest', $class );
        }
        $this->assertGreaterThan( 50, count( $classes ) );
        // the second instance comes from the cache, the same entries
        expDataTableCatalogue::reset();
        $this->assertSame( $classes, expDataTableCatalogue::instance()->classes() );
    }

    /** CT-07 */
    public function testLookups()
    {
        $entries = array();
        foreach ( array( 'aRow' => array( 'a_row', 'x' ), 'bRow' => array( 'b_row', 'kernel' ), 'cRow' => array( 'a_row', 'abc' ) ) as $class => $info )
        {
            $definition = array( 'fields' => array( 'id' => array( 'datatype' => 'integer' ), 'node_id' => array( 'datatype' => 'integer' ) ),
                                 'keys' => array( 'id' ), 'increment_key' => 'id', 'name' => $info[0] );
            $entries[$class] = expDataTableCatalogue::entryFromDefinition( $class, $definition, '', $info[1] );
        }
        $catalogue = new expDataTableCatalogue( $entries );
        $this->assertSame( array( 'aRow', 'cRow' ), array_keys( $catalogue->byTable( 'a_row' ) ) );
        $this->assertSame( array( 'kernel', 'abc', 'x' ), array_keys( $catalogue->byOrigin() ) );
        $this->assertSame( array( 'aRow', 'bRow', 'cRow' ), array_keys( $catalogue->pointingAt( 'node' ) ) );
        $this->assertSame( array(), $catalogue->pointingAt( 'object' ) );
        $this->assertSame( 'bRow', $catalogue->entry( 'BROW' )['class'] );
        $this->assertNull( $catalogue->entry( 'nosuch' ) );
    }

    /** CT-08 */
    public function testDescribeClass()
    {
        $method = new ReflectionMethod( 'expDataTableCatalogue', 'describeClass' );
        $broken = $method->invoke( null, 'expDataTableCatalogueTestBroken', 'x.php', 'kernel', true, array() );
        $this->assertSame( 'needs a request', $broken['error'] );
        $this->assertSame( '', $broken['table'] );
        $ok = $method->invoke( null, 'expDataTableCatalogueTestOk', 'x.php', 'kernel', true, array() );
        $this->assertSame( 't691_ok', $ok['table'] );
        $this->assertSame( '', $ok['connection'] );
        $this->assertNull( $method->invoke( null, 'expDataTableCatalogueTestChild', 'x.php', 'kernel', true, array() ), 'inherits its parent\'s definition()' );
        $this->assertNull( $method->invoke( null, 'expDataTableCatalogueTestPlain', 'x.php', 'kernel', true, array() ), 'not persistent' );
        $this->assertNull( $method->invoke( null, 'eZPersistentObject', 'x.php', 'kernel', true, array() ), 'no table of its own' );
        $this->assertNull( $method->invoke( null, 'expNoSuchClassAtAll', 'x.php', 'kernel', true, array() ) );
    }

    /** @return expDataTableCatalogue made again, its cache file removed afterwards */
    private function rebuilt()
    {
        $method = new ReflectionMethod( 'expDataTableCatalogue', 'cacheFile' );
        $file = $method->invoke( null );
        $existed = is_file( $file );
        $catalogue = expDataTableCatalogue::instance( true );
        $this->assertFileExists( $file );
        if ( !$existed )
        {
            $test = $this;
            register_shutdown_function( function () use ( $file ) {
                if ( is_file( $file ) )
                    unlink( $file );
                $dir = dirname( $file );
                if ( is_dir( $dir ) && count( scandir( $dir ) ) === 2 )
                    rmdir( $dir );
            } );
        }
        return $catalogue;
    }
}
