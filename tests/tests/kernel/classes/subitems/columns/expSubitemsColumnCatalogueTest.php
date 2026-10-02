<?php
/**
 * The column catalogue as a whole: every [Column_*] block of settings/subitemscolumns.ini builds,
 * is in a known group, has a valid type, and gives a JSON-safe value (or null) and HTML and CSV
 * text without an exception for the content root, the media root, the users root and the admin
 * user -- for every content class those nodes have, columns that do not apply give null.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSubitemsColumnsTestCase.php';

class expSubitemsColumnCatalogueTest extends expSubitemsColumnsTestCase
{
    const GROUPS = array( 'Basic', 'Node', 'Object', 'Version', 'Location', 'URLs', 'SEO', 'Dates', 'People',
                          'Relations', 'Translations', 'Workflow', 'Users', 'Media', 'Technical', 'Custom' );
    const TYPES = array( 'text', 'html', 'number', 'date', 'datetime', 'bool', 'link', 'code', 'list', 'image' );
    const BUILTINS = array( 'thumbnail', 'name', 'visibility', 'type', 'modifier', 'modified', 'published', 'translations',
                            'section', 'nodeid', 'noderemoteid', 'objectid', 'objectremoteid', 'objectstate', 'priority' );

    /** The column blocks as the INI file writes them (key => settings). */
    protected function iniBlocks()
    {
        $ini = eZINI::instance( 'subitemscolumns.ini' );
        $blocks = array();
        foreach ( $ini->groups() as $name => $settings )
        {
            if ( strpos( $name, 'Column_' ) === 0 )
                $blocks[substr( $name, 7 )] = $settings;
        }
        return $blocks;
    }

    public function testTheRequiredColumnsExist()
    {
        $required = array( 'parentnodeid' => 'Parent node ID', 'urlalias' => 'URL alias', 'systemurl' => 'System URL',
                           'pagetitle' => 'Page title in the browser', 'childrencount' => 'Node children count',
                           'version' => 'Object version' );
        $blocks = $this->iniBlocks();
        foreach ( $required as $key => $name )
        {
            $this->assertArrayHasKey( $key, $blocks, "Column_$key is defined" );
            $this->assertSame( $name, $blocks[$key]['Name'] );
        }
    }

    public function testTheFifteenBuiltinsAreMarkedBuiltin()
    {
        $blocks = $this->iniBlocks();
        foreach ( self::BUILTINS as $key )
        {
            $this->assertArrayHasKey( $key, $blocks, "Column_$key is defined" );
            $this->assertSame( 'true', $blocks[$key]['Builtin'], "Column_$key is Builtin=true" );
            $this->assertTrue( self::$registry->isBuiltin( $key ) );
        }
        $builtins = array_keys( array_filter( $blocks, function ( $b ) { return ( $b['Builtin'] ?? '' ) === 'true'; } ) );
        sort( $builtins );
        $expected = self::BUILTINS;
        sort( $expected );
        $this->assertSame( $expected, $builtins, 'no other block claims to be built in' );
    }

    public function testEveryBlockIsWellFormed()
    {
        $keys = array();
        foreach ( $this->iniBlocks() as $key => $settings )
        {
            $this->assertMatchesRegularExpression( '/^[a-z][a-z0-9_]*$/', $key, "key $key" );
            $this->assertNotEmpty( $settings['Name'] ?? '', "Column_$key has a Name" );
            $this->assertContains( $settings['Group'] ?? '', self::GROUPS, "Column_$key has a known Group" );
            $this->assertNotEmpty( $settings['Description'] ?? '', "Column_$key has a Description" );
            $this->assertMatchesRegularExpression( '/^\d+$/', (string)( $settings['Order'] ?? '' ), "Column_$key has an Order" );
            if ( ( $settings['Builtin'] ?? '' ) !== 'true' )
            {
                $this->assertContains( $settings['Type'] ?? '', self::TYPES, "Column_$key has a known Type" );
                $kinds = array_filter( array( $settings['Class'] ?? '', $settings['Handler'] ?? '', $settings['Template'] ?? '' ), 'strlen' );
                $this->assertCount( 1, $kinds, "Column_$key names exactly one of Class, Handler, Template" );
                if ( isset( $settings['Class'] ) && is_subclass_of( $settings['Class'], 'expSubitemsFieldColumn' ) )
                {
                    $column = $this->column( $key );
                    $this->assertContains( $settings['Field'] ?? '', $column->fields(), "Column_$key Field= is one its class knows" );
                }
            }
            if ( isset( $settings['SortField'] ) && $settings['SortField'] !== '' )
                $this->assertNotFalse( $this->column( $key )->sortBy(), "Column_$key SortField={$settings['SortField']} is accepted" );
            $keys[] = $key;
        }
        $this->assertGreaterThanOrEqual( 100, count( $keys ), 'the catalogue has at least 100 columns' );
    }

    public function testOrdersAreUnique()
    {
        $orders = array();
        foreach ( $this->iniBlocks() as $key => $settings )
        {
            $this->assertArrayNotHasKey( (int)$settings['Order'], $orders, "Order of Column_$key is not also used by " . ( $orders[(int)$settings['Order']] ?? '' ) );
            $orders[(int)$settings['Order']] = $key;
        }
    }

    /** Every non-built-in column on four very different nodes: no exception, JSON-safe values, strings for HTML and CSV. */
    public function testEveryColumnIsSafeOnTheStableNodes()
    {
        $nodes = array( $this->contentRoot(), $this->mediaRoot(), $this->usersRoot(), $this->adminUserNode() );
        $checked = 0;
        foreach ( $this->iniBlocks() as $key => $settings )
        {
            if ( self::$registry->isBuiltin( $key ) )
                continue;
            $column = $this->column( $key );
            foreach ( $nodes as $node )
            {
                $value = $column->value( $node );
                $this->assertTrue( $value === null || is_scalar( $value ) || is_array( $value ), "$key value type" );
                if ( is_array( $value ) )
                {
                    foreach ( $value as $item )
                        $this->assertTrue( $item === null || is_scalar( $item ), "$key list items are scalars" );
                }
                $this->assertNotFalse( json_encode( $value ), "$key value is JSON-safe" );
                $this->assertIsString( $column->html( $node, $value ), "$key html" );
                $this->assertIsString( $column->text( $node, $value ), "$key text" );
                $this->assertStringNotContainsString( "\n", $column->text( $node, $value ), "$key CSV text is one line" );
                $checked++;
            }
        }
        $this->assertGreaterThan( 300, $checked );
    }

    /** The user columns give null for anything that is not a user, never an error. */
    public function testUserColumnsAreNullForNonUsers()
    {
        foreach ( array( 'userlogin', 'useremail', 'userenabled', 'userlocked', 'userlastvisit', 'userlogincount',
                         'userfailedlogins', 'userroles', 'userrolecount' ) as $key )
        {
            $this->assertNull( $this->value( $key, $this->contentRoot() ), "$key of the content root" );
            $this->assertNull( $this->value( $key, $this->mediaRoot() ), "$key of the media root" );
        }
    }

    /** Personal data columns carry a policy, so the registry never offers them to everybody. */
    public function testPersonalDataColumnsHaveAPolicy()
    {
        $blocks = $this->iniBlocks();
        foreach ( $blocks as $key => $settings )
        {
            if ( ( $settings['Group'] ?? '' ) === 'Users' )
                $this->assertNotEmpty( array_filter( (array)( $settings['Policy'] ?? array() ) ), "Column_$key has a Policy[]" );
        }
        $this->assertSame( array( 'role/assign' ), array_values( array_filter( (array)$blocks['useremail']['Policy'] ) ) );
    }

    /** A column whose policy is refused is not available; with the policy granted it is. */
    public function testPolicyDecidesAvailability()
    {
        $column = $this->column( 'useremail' );
        $root = $this->contentRoot();
        expSubitemsColumnRegistry::setAccessChecker( function ( $module, $function ) { return false; } );
        try
        {
            $this->assertFalse( $column->isAvailable( $root ) );
            $this->assertTrue( $this->column( 'parentnodeid' )->isAvailable( $root ), 'a column without policy stays available' );
        }
        finally
        {
            expSubitemsColumnRegistry::setAccessChecker( null );
        }
        $this->assertTrue( $column->isAvailable( $root ), 'the admin may assign roles' );
    }

    /** An unknown Field= gives null, not an error. */
    public function testUnknownFieldIsNull()
    {
        $column = new expSubitemsNodeColumn( 'x', array( 'Field' => 'no_such_field', 'Type' => 'text' ) );
        $this->assertNull( $column->value( $this->contentRoot() ) );
        $column = new expSubitemsNodeColumn( 'x', array( 'Field' => 'Bad-Field()', 'Type' => 'text' ) );
        $this->assertNull( $column->value( $this->contentRoot() ) );
        $this->assertSame( 'fieldParentNodeId', expSubitemsFieldColumn::fieldMethod( 'parent_node_id' ) );
        $this->assertFalse( expSubitemsFieldColumn::fieldMethod( '../x' ) );
    }
}
