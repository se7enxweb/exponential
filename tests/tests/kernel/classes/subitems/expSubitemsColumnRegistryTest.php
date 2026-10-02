<?php
/**
 * The registry of the subitems list columns: INI parsing, the column kinds, policies, defaults
 * per subtree / parent class, presets, attribute columns and sorting. No database.
 *
 *  SR-01 — [SubitemsSettings] values, page sizes, CSV settings
 *  SR-02 — the 15 built-ins are always there; an INI block renames one but it stays built in
 *  SR-03 — Handler=, Class=, Template= give the callable, class and template columns
 *  SR-04 — broken blocks (no class, not a subclass, no handler, a template with ..) are skipped
 *  SR-05 — invalid keys are not columns
 *  SR-06 — Policy[] hides a column; every entry must be granted
 *  SR-07 — defaults: Subtree[] (parent or ancestor), ParentClassIdentifiers[], else DefaultColumns[]
 *  SR-08 — presets: INI ones with valid ids, list splitting
 *  SR-09 — attribute columns: key, settings, sortable by datatype, excluded datatypes, policies
 *  SR-10 — resolveColumns keeps order, drops unknown/refused/builtin keys
 *  SR-11 — sortFor: column keys, legacy names, attribute sort with a class filter, fallback
 *  SR-12 — availableColumns is ordered by Order=
 *  SR-13 — the real settings/subitems.ini parses and names known keys
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group subitems
 */

require_once __DIR__ . '/fixtures/expsubitemstesthelpers.php';

class expSubitemsColumnRegistryTest extends ezpTestCase
{
    /** @var array module/function => granted */
    private $granted = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        expSubitemsTestFixtures::warmUp();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->granted = array( 'content/read' => true );
        $granted = &$this->granted;
        expSubitemsColumnRegistry::setAccessChecker( function ( $module, $function ) use ( &$granted ) {
            return !empty( $granted["$module/$function"] );
        } );
        expSubitemsTestCountingColumn::$calls = array();
    }

    public function tearDown(): void
    {
        expSubitemsColumnRegistry::setAccessChecker( null );
        expSubitemsColumnRegistry::resetInstance();
        parent::tearDown();
    }

    public function testSettings()
    {
        $r = expSubitemsTestFixtures::registry();
        $this->assertTrue( $r->attributeColumnsEnabled() );
        $this->assertTrue( $r->csvExportEnabled() );
        $this->assertSame( 250, $r->csvLimit() );
        $this->assertSame( array( 10, 20 ), $r->pageSizes() );
        $this->assertSame( array( 'ezuser' ), $r->excludedDataTypes() );
        $this->assertSame( array( 'user/register' ), $r->dataTypePolicies( 'ezemail' ) );
        $this->assertSame( array(), $r->dataTypePolicies( 'ezstring' ) );
    }

    public function testBuiltinsAlwaysPresentAndRenamable()
    {
        $r = expSubitemsTestFixtures::registry();
        foreach ( expSubitemsBuiltinColumn::keys() as $key )
        {
            $this->assertTrue( $r->isBuiltin( $key ), $key );
            $this->assertInstanceOf( 'expSubitemsBuiltinColumn', $r->definedColumn( $key ), $key );
        }
        $this->assertCount( 15, expSubitemsBuiltinColumn::keys() );
        $defs = $r->definitions();
        $this->assertSame( 'Title', $defs['name']['Name'], 'the INI renames it' );
        $this->assertSame( 15, (int)$defs['name']['Order'] );
        $this->assertInstanceOf( 'expSubitemsBuiltinColumn', $r->definedColumn( 'name' ), 'Class= is ignored for a built-in' );
        $this->assertSame( 'name', $r->definedColumn( 'name' )->sortBy() );
    }

    public function testColumnKinds()
    {
        $r = expSubitemsTestFixtures::registry();
        $this->assertInstanceOf( 'expSubitemsCallableColumn', $r->definedColumn( 'handlercol' ) );
        $this->assertInstanceOf( 'expSubitemsTestCountingColumn', $r->definedColumn( 'classcol' ) );
        $this->assertInstanceOf( 'expSubitemsTemplateColumn', $r->definedColumn( 'tplcol' ) );
        $this->assertSame( 'classcol', $r->definedColumn( 'classcol' )->key() );
        $this->assertSame( 'right', $r->definedColumn( 'classcol' )->setting( 'Align' ) );
        $this->assertSame( array(), $r->definedColumn( 'classcol' )->setting( 'Policy' ) );
        $this->assertFalse( $r->isBuiltin( 'classcol' ) );
    }

    public function testBrokenBlocksAreSkipped()
    {
        $r = expSubitemsTestFixtures::registry();
        foreach ( array( 'broken', 'nohandler', 'badtpl', 'nothing' ) as $key )
            $this->assertNull( $r->definedColumn( $key ), $key );
        $this->assertNull( $r->column( 'broken', expSubitemsTestFixtures::parent() ) );
        $this->assertArrayNotHasKey( 'broken', $r->availableColumns( expSubitemsTestFixtures::parent() ) );
    }

    public function testInvalidKeys()
    {
        $r = expSubitemsTestFixtures::registry();
        $defs = $r->definitions();
        $this->assertArrayNotHasKey( 'Bad-Key', $defs );
        $this->assertFalse( $r->isKnownKey( 'Bad-Key' ) );
        $this->assertFalse( $r->isKnownKey( 'nosuchcolumn' ) );
        $this->assertFalse( $r->isKnownKey( array( 'name' ) ) );
        $this->assertTrue( $r->isKnownKey( 'handlercol' ) );
        $this->assertTrue( $r->isKnownKey( 'attr:article/title' ) );
        $this->assertFalse( $r->isKnownKey( 'attr:article/nosuch' ) );
        $this->assertFalse( $r->isKnownKey( 'attr:../x' ) );
        $this->assertFalse( expSubitemsColumnRegistry::isValidDefinedKey( 'a b' ) );
        $this->assertTrue( expSubitemsColumnRegistry::isValidDefinedKey( 'parentnodeid' ) );
    }

    public function testPoliciesHideColumns()
    {
        $r = expSubitemsTestFixtures::registry();
        $parent = expSubitemsTestFixtures::parent();

        $this->assertNull( $r->column( 'secret', $parent ) );
        $this->assertArrayNotHasKey( 'secret', $r->availableColumns( $parent ) );
        $this->granted['user/selfedit'] = true;
        $this->assertNotNull( $r->column( 'secret', $parent ) );
        $this->assertArrayHasKey( 'secret', $r->availableColumns( $parent ) );

        // all entries must be granted
        $this->assertNull( $r->column( 'twopolicies', $parent ) );
        $this->granted['setup/system_info'] = true;
        $this->assertNotNull( $r->column( 'twopolicies', $parent ) );
        $this->granted['content/read'] = false;
        $this->assertNull( $r->column( 'twopolicies', $parent ) );
    }

    public function testBrokenPolicyEntryRefuses()
    {
        $column = new expSubitemsTestCountingColumn( 'x', array( 'Policy' => array( 'nofunction' ) ) );
        $this->assertFalse( $column->isAvailable( expSubitemsTestFixtures::parent() ) );
        $column = new expSubitemsTestCountingColumn( 'x', array( 'Policy' => array( 'content/read;user/selfedit' ) ) );
        $this->assertFalse( $column->isAvailable( expSubitemsTestFixtures::parent() ) );
        $this->granted['user/selfedit'] = true;
        $this->assertTrue( $column->isAvailable( expSubitemsTestFixtures::parent() ) );
    }

    public function testDefaults()
    {
        $r = expSubitemsTestFixtures::registry();
        // nothing matches: DefaultColumns[] without the unknown key
        $this->assertSame( array( 'name', 'published', 'priority' ), $r->defaults( expSubitemsTestFixtures::parent( 60 ) ) );
        // the media root itself, and a node below it
        $this->assertSame( array( 'thumbnail', 'name' ), $r->defaults( expSubitemsTestFixtures::parent( 43, 'folder', '/1/43/' ) ) );
        $this->assertSame( array( 'thumbnail', 'name' ), $r->defaults( expSubitemsTestFixtures::parent( 51, 'folder', '/1/43/51/' ) ) );
        // by the parent's class
        $this->assertSame( array( 'name', 'handlercol' ), $r->defaults( expSubitemsTestFixtures::parent( 70, 'gallery' ) ) );
        // the first matching block wins
        $this->assertSame( array( 'thumbnail', 'name' ), $r->defaults( expSubitemsTestFixtures::parent( 52, 'gallery', '/1/43/52/' ) ) );
    }

    public function testPresets()
    {
        $r = expSubitemsTestFixtures::registry();
        $presets = $r->presets();
        $this->assertSame( array( 'seo', 'tech' ), array_keys( $presets ), 'Preset_BadId has an invalid id' );
        $this->assertSame( 'SEO', $presets['seo']['name'] );
        $this->assertSame( array( 'name', 'handlercol', 'secret' ), $presets['seo']['columns'] );
        $this->assertSame( 'tech', $presets['tech']['name'], 'no Name: the id' );
        $this->assertSame( array( 'nodeid', 'objectid' ), $presets['tech']['columns'], '; splits a list' );
        $this->assertSame( array( 'a', 'b', 'c' ), expSubitemsColumnRegistry::splitList( array( 'a;b', ' c ', 'a', '' ) ) );
        $this->assertSame( array(), expSubitemsColumnRegistry::splitList( null ) );
    }

    private function attributeColumn( $r, $classIdentifier, $identifier, $dataType, $sortKeyType = 'string', $placement = 1 )
    {
        $class = new expSubitemsTestClass( array( 'identifier' => $classIdentifier, 'name' => ucfirst( $classIdentifier ) ) );
        $attribute = new expSubitemsTestClassAttribute( array( 'identifier' => $identifier, 'name' => ucfirst( $identifier ),
                                                               'data_type_string' => $dataType, 'placement' => $placement ), $sortKeyType );
        return $r->makeAttributeColumn( $class, $attribute );
    }

    public function testAttributeColumns()
    {
        $r = expSubitemsTestFixtures::registry();
        $title = $this->attributeColumn( $r, 'article', 'title', 'ezstring', 'string', 1 );
        $this->assertInstanceOf( 'expSubitemsAttributeColumn', $title );
        $this->assertSame( 'attr:article/title', $title->key() );
        $this->assertSame( 'Title', $title->name() );
        $this->assertSame( 'Attributes: Article', $title->setting( 'Group' ) );
        $this->assertSame( array( 'attribute', 'article/title' ), $title->sortBy() );
        $this->assertSame( 100001, $title->setting( 'Order' ) );

        $unsortable = $this->attributeColumn( $r, 'article', 'body', 'ezxmltext', false );
        $this->assertFalse( $unsortable->sortBy() );

        $this->assertNull( $this->attributeColumn( $r, 'user', 'user_account', 'ezuser' ), 'ezuser is excluded' );

        $email = $this->attributeColumn( $r, 'user', 'email', 'ezemail' );
        $this->assertSame( array( 'user/register' ), $email->setting( 'Policy' ) );
        $this->assertFalse( $email->isAvailable( expSubitemsTestFixtures::parent() ) );
        $this->granted['user/register'] = true;
        $this->assertTrue( $email->isAvailable( expSubitemsTestFixtures::parent() ) );

        $this->assertSame( array( 'article', 'title' ), expSubitemsAttributeColumn::parseKey( 'attr:article/title' ) );
        $this->assertNull( expSubitemsAttributeColumn::parseKey( 'attr:Article/title' ) );
        $this->assertNull( expSubitemsAttributeColumn::parseKey( 'article/title' ) );
    }

    public function testAttributeColumnValue()
    {
        $r = expSubitemsTestFixtures::registry();
        $column = $this->attributeColumn( $r, 'article', 'title', 'ezstring' );
        $nodes = expSubitemsTestFixtures::children( 1 );
        $this->assertSame( 'Title 1', $column->value( $nodes[0] ) );
        $this->assertSame( '&lt;b&gt;', $column->html( $nodes[0], '<b>' ), 'escaped' );

        // another class: no value
        $folder = expSubitemsTestFixtures::children( 1, 'folder' );
        $this->assertNull( $column->value( $folder[0] ) );

        // toString() without markup when there is no title; long text is cut
        $long = str_repeat( 'x', 400 );
        $object = new expSubitemsTestObject( array( 'class_identifier' => 'article' ),
                                             array( 'title' => new expSubitemsTestObjectAttribute( '', "<p>Hello <b>World</b> $long</p>" ) ) );
        $node = new expSubitemsTestNode( array( 'node_id' => 5 ), $object );
        $value = $column->value( $node );
        $this->assertStringStartsWith( 'Hello World x', $value );
        $this->assertSame( expSubitemsAttributeColumn::MAX_LENGTH + 1, mb_strlen( $value, 'UTF-8' ) );

        // no content: null
        $object = new expSubitemsTestObject( array( 'class_identifier' => 'article' ),
                                             array( 'title' => new expSubitemsTestObjectAttribute( 'x', 'x', false ) ) );
        $this->assertNull( $column->value( new expSubitemsTestNode( array( 'node_id' => 6 ), $object ) ) );
    }

    public function testAttributeColumnsOffOrDisabled()
    {
        $r = expSubitemsTestFixtures::registry();
        $r->fakeAttributeColumns = array( 'attr:article/title' => $this->attributeColumn( $r, 'article', 'title', 'ezstring' ) );
        $parent = expSubitemsTestFixtures::parent();
        $this->assertArrayHasKey( 'attr:article/title', $r->availableColumns( $parent ) );
        $this->assertNotNull( $r->column( 'attr:article/title', $parent ) );
        $this->assertNull( $r->column( 'attr:article/intro', $parent ), 'not among the children\'s columns' );

        $groups = array( 'SubitemsSettings' => array( 'AttributeColumns' => 'disabled' ) );
        $off = new expSubitemsTestRegistryFromGroups( $groups, array() );
        $off->fakeAttributeColumns = $r->fakeAttributeColumns;
        $this->assertFalse( $off->attributeColumnsEnabled() );
        $this->assertNull( $off->column( 'attr:article/title', $parent ) );
        $this->assertFalse( $off->isKnownKey( 'attr:article/title' ) );
    }

    public function testResolveColumns()
    {
        $r = expSubitemsTestFixtures::registry();
        $parent = expSubitemsTestFixtures::parent();
        $resolved = $r->resolveColumns( 'tplcol,name,nosuch,secret,classcol,classcol,Bad-Key,broken', $parent );
        $this->assertSame( array( 'tplcol', 'classcol' ), array_keys( $resolved ), 'order kept; builtin, unknown, refused and broken dropped' );
        $withBuiltin = $r->resolveColumns( array( 'name', 'classcol', 'nodeid' ), $parent, true );
        $this->assertSame( array( 'name', 'classcol', 'nodeid' ), array_keys( $withBuiltin ) );

        $many = implode( ',', array_fill( 0, 300, 'classcol' ) );
        $this->assertCount( 1, $r->resolveColumns( $many, $parent ) );
    }

    public function testSortFor()
    {
        $r = expSubitemsTestFixtures::registry();
        $parent = expSubitemsTestFixtures::parent();

        $s = $r->sortFor( 'handlercol', true, $parent );
        $this->assertSame( array( 'key' => 'handlercol', 'sort_by' => array( array( 'depth', true ) ), 'class_filter' => null ), $s );

        $s = $r->sortFor( 'published_date', false, $parent );
        $this->assertSame( 'published', $s['key'], 'legacy name mapped to the built-in' );
        $this->assertSame( array( array( 'published', false ) ), $s['sort_by'] );

        $s = $r->sortFor( 'hidden_status_string', true, $parent );
        $this->assertSame( array( array( 'visibility', true ) ), $s['sort_by'] );

        $s = $r->sortFor( 'attrsort', true, $parent );
        $this->assertSame( array( array( 'attribute', true, 'article/title' ) ), $s['sort_by'] );
        $this->assertSame( 'article', $s['class_filter'] );

        foreach ( array( 'classcol', 'nosuch', '', 'translations', 'secret', 'path); DROP TABLE x' ) as $key )
        {
            $s = $r->sortFor( $key, true, $parent );
            $this->assertNull( $s['key'], $key );
            $this->assertSame( array( array( 'path', true ) ), $s['sort_by'], "$key falls back to the parent's sort" );
        }
    }

    public function testSortByFromSettings()
    {
        $c = new expSubitemsTestCountingColumn( 'x', array( 'SortField' => 'attribute:article/title' ) );
        $this->assertSame( array( 'attribute', 'article/title' ), $c->sortBy() );
        $c = new expSubitemsTestCountingColumn( 'x', array( 'SortField' => 'attribute:../etc' ) );
        $this->assertFalse( $c->sortBy() );
        $c = new expSubitemsTestCountingColumn( 'x', array( 'SortField' => 'nosuchfield' ) );
        $this->assertFalse( $c->sortBy() );
        $c = new expSubitemsTestCountingColumn( 'x', array() );
        $this->assertFalse( $c->sortBy() );
        $c = new expSubitemsTestCountingColumn( 'x', array( 'SortField' => 'modified' ) );
        $this->assertSame( 'modified', $c->sortBy() );
    }

    public function testAvailableColumnsOrder()
    {
        $r = expSubitemsTestFixtures::registry();
        $keys = array_keys( $r->availableColumns( expSubitemsTestFixtures::parent() ) );
        $this->assertSame( 'thumbnail', $keys[0] );
        $this->assertSame( 'name', $keys[1], 'Order=15 from the INI' );
        $this->assertLessThan( array_search( 'tplcol', $keys ), array_search( 'classcol', $keys ) );
        $this->assertLessThan( array_search( 'handlercol', $keys ), array_search( 'classcol', $keys ) );
    }

    public function testDescribe()
    {
        $r = expSubitemsTestFixtures::registry();
        $d = $r->describe( $r->definedColumn( 'handlercol' ) );
        $this->assertSame( array( 'key', 'name', 'group', 'type', 'sortable', 'align', 'description', 'copy', 'builtin', 'order' ), array_keys( $d ) );
        $this->assertTrue( $d['sortable'] );
        $this->assertTrue( $d['copy'] );
        $this->assertFalse( $d['builtin'] );
        $this->assertSame( 300, $d['order'] );
        $this->assertTrue( $r->describe( $r->definedColumn( 'nodeid' ) )['builtin'] );
    }

    public function testRealSubitemsIni()
    {
        $ini = eZINI::fetchFromFile( 'settings/subitems.ini' );
        $r = new expSubitemsTestRegistry( $ini, eZINI::fetchFromFile( expSubitemsTestFixtures::dir() . '/subitemscolumns.ini' ) );
        $this->assertSame( array( 'name', 'published', 'translations', 'priority' ), $r->defaults( expSubitemsTestFixtures::parent( 60 ) ) );
        $this->assertSame( array( 'thumbnail', 'name', 'visibility', 'type' ), $r->defaults( expSubitemsTestFixtures::parent( 43, 'folder', '/1/43/' ) ) );
        $this->assertSame( array( 'name', 'modifier', 'published' ), $r->defaults( expSubitemsTestFixtures::parent( 12, 'user_group', '/1/5/12/' ) ) );
        $this->assertSame( array( 'seo', 'editorial', 'technical', 'translations' ), array_keys( $r->presets() ) );
        $this->assertSame( 5000, $r->csvLimit() );
        $contract = array( 'parentnodeid', 'urlalias', 'systemurl', 'pagetitle', 'childrencount', 'version' );
        foreach ( $r->presets() as $id => $preset )
            foreach ( $preset['columns'] as $key )
                $this->assertTrue( in_array( $key, expSubitemsBuiltinColumn::keys(), true ) || in_array( $key, $contract, true ), "$id: $key" );
    }

    public function testRealIniFilesTogether()
    {
        if ( !file_exists( 'settings/subitemscolumns.ini' ) )
            $this->markTestSkipped( 'settings/subitemscolumns.ini is not there yet' );
        $r = new expSubitemsTestRegistry( eZINI::fetchFromFile( 'settings/subitems.ini' ), eZINI::fetchFromFile( 'settings/subitemscolumns.ini' ) );
        $defs = $r->definitions();
        foreach ( $r->presets() as $id => $preset )
            foreach ( $preset['columns'] as $key )
                $this->assertArrayHasKey( $key, $defs, "preset $id names $key" );
        foreach ( array( array( 60, '/1/2/60/' ), array( 43, '/1/43/' ), array( 5, '/1/5/' ) ) as $p )
            foreach ( $r->defaults( expSubitemsTestFixtures::parent( $p[0], 'folder', $p[1] ) ) as $key )
                $this->assertArrayHasKey( $key, $defs, "default $key" );
    }
}

/** A registry from INI groups given as arrays. */
class expSubitemsTestRegistryFromGroups extends expSubitemsTestRegistry
{
    public function __construct( array $subitemsGroups, array $columnGroups )
    {
        $this->load( $subitemsGroups, $columnGroups );
    }
}
