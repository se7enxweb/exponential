<?php
use PHPUnit\Framework\Attributes\DataProvider;
/**
 * File containing the expContentExportEntryTest class.
 *
 * The entry of the content tree's Advanced > Export menu into the content object package wizard
 * (package/create/(creator)/ezcontentobject/(node)/<id> ...): how its parameters are read and checked, and which
 * policies let a user see and use it. No database needed: the policies are given as hasAccessTo() results.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expContentExportEntryTest extends ezpTestCase
{
    /** The view's parameters as eZModule gives them: false for an unordered parameter that was not in the URL */
    private static function params( array $given )
    {
        return array_merge( array( 'Creator' => false, 'NodeID' => false, 'Subtree' => false,
                                   'IncludeClasses' => false, 'Versions' => false ), $given );
    }

    public function testANodeAlone()
    {
        $entry = eZContentObjectPackageCreator::entryParameters( self::params( array( 'Creator' => 'ezcontentobject', 'NodeID' => '42' ) ) );
        $this->assertSame( array(), $entry['errors'] );
        $this->assertSame( 42, $entry['node_id'] );
        $this->assertFalse( $entry['subtree'] );
        $this->assertSame( array( array( 'id' => 42, 'type' => 'node' ) ), $entry['node_list'] );
        $this->assertSame( array(), $entry['object_options'], 'without choices the wizard keeps its own defaults' );
    }

    public function testASubtreeWithItsChoices()
    {
        $entry = eZContentObjectPackageCreator::entryParameters( self::params( array(
            'Creator' => 'ezcontentobject', 'NodeID' => '2', 'Subtree' => '1', 'IncludeClasses' => '0', 'Versions' => 'all' ) ) );
        $this->assertSame( array(), $entry['errors'] );
        $this->assertSame( array( array( 'id' => 2, 'type' => 'subtree' ) ), $entry['node_list'] );
        $this->assertSame( array( 'include_classes' => 0, 'versions' => 'all' ), $entry['object_options'] );

        $entry = eZContentObjectPackageCreator::entryParameters( self::params( array(
            'Creator' => 'ezcontentobject', 'NodeID' => '2', 'Subtree' => '0', 'IncludeClasses' => '1', 'Versions' => 'current' ) ) );
        $this->assertSame( array(), $entry['errors'] );
        $this->assertSame( 'node', $entry['node_list'][0]['type'] );
        $this->assertSame( array( 'include_classes' => 1, 'versions' => 'current' ), $entry['object_options'] );
    }

    #[DataProvider('invalidEntries')]
    public function testInvalidValuesAreRefused( array $given, $error )
    {
        $entry = eZContentObjectPackageCreator::entryParameters( self::params( $given ) );
        $this->assertContains( $error, $entry['errors'] );
    }

    public static function invalidEntries()
    {
        $node = array( 'Creator' => 'ezcontentobject', 'NodeID' => '5' );
        return array(
            'another wizard'          => array( array( 'Creator' => 'ezextension', 'NodeID' => '5' ), 'creator' ),
            'no wizard'               => array( array( 'NodeID' => '5' ), 'creator' ),
            'no node'                 => array( array( 'Creator' => 'ezcontentobject' ), 'node' ),
            'a node name'             => array( array( 'Creator' => 'ezcontentobject', 'NodeID' => 'abc' ), 'node' ),
            'node zero'               => array( array( 'Creator' => 'ezcontentobject', 'NodeID' => '0' ), 'node' ),
            'a negative node'         => array( array( 'Creator' => 'ezcontentobject', 'NodeID' => '-5' ), 'node' ),
            'a leading zero'          => array( array( 'Creator' => 'ezcontentobject', 'NodeID' => '05' ), 'node' ),
            'a fraction'              => array( array( 'Creator' => 'ezcontentobject', 'NodeID' => '5.5' ), 'node' ),
            'past the largest id'     => array( array( 'Creator' => 'ezcontentobject', 'NodeID' => '2147483648' ), 'node' ),
            'eleven digits'           => array( array( 'Creator' => 'ezcontentobject', 'NodeID' => '12345678901' ), 'node' ),
            'a space'                 => array( array( 'Creator' => 'ezcontentobject', 'NodeID' => ' 5' ), 'node' ),
            'the name without value'  => array( array( 'Creator' => 'ezcontentobject', 'NodeID' => '' ), 'node' ),
            'subtree yes'             => array( $node + array( 'Subtree' => 'yes' ), 'subtree' ),
            'classes 2'               => array( $node + array( 'IncludeClasses' => '2' ), 'classes' ),
            'versions some'           => array( $node + array( 'Versions' => 'some' ), 'versions' ),
            'versions in other case'  => array( $node + array( 'Versions' => 'ALL' ), 'versions' ),
        );
    }

    public function testAnEntryIsToldByAnyOfItsParameters()
    {
        $this->assertFalse( eZContentObjectPackageCreator::isEntry( self::params( array() ) ) );
        $this->assertFalse( eZContentObjectPackageCreator::isEntry( array( 'Module' => null ) ) );
        $this->assertTrue( eZContentObjectPackageCreator::isEntry( self::params( array( 'NodeID' => '5' ) ) ) );
        $this->assertTrue( eZContentObjectPackageCreator::isEntry( self::params( array( 'Versions' => 'all' ) ) ) );
    }

    public function testPositiveID()
    {
        $this->assertSame( 1, eZContentObjectPackageCreator::positiveID( '1' ) );
        $this->assertSame( 2147483647, eZContentObjectPackageCreator::positiveID( '2147483647' ) );
        $this->assertSame( 7, eZContentObjectPackageCreator::positiveID( 7 ) );
        foreach ( array( 0, -1, 2147483648, '0', '', '1e3', '0x1', null, true, array( 1 ) ) as $value )
            $this->assertSame( 0, eZContentObjectPackageCreator::positiveID( $value ), var_export( $value, true ) );
    }

    /** The wizard's content step: Remove selected with nothing ticked keeps the list (a TypeError before), with rows ticked removes those */
    public function testRemoveSelectedTakesOutOnlyTheTickedRows()
    {
        $tpl = new class { public $vars = array(); public function setVariable( $name, $value ) { $this->vars[$name] = $value; } };
        $module = null;
        $creator = new eZContentObjectPackageCreator( 'ezcontentobject' );
        $data = array( 'node_list' => array( array( 'id' => 42, 'type' => 'node' ), array( 'id' => 43, 'type' => 'subtree' ) ) );
        $saved = $_POST;
        try
        {
            $_POST = array( 'RemoveSelected' => '1' );
            $creator->loadObjectList( false, eZHTTPTool::instance(), 'object', $data, $tpl, $module );
            $this->assertCount( 2, $data['node_list'] );
            $_POST = array( 'RemoveSelected' => '1', 'DeleteIDArray' => array( '43' ) );
            $creator->loadObjectList( false, eZHTTPTool::instance(), 'object', $data, $tpl, $module );
            $this->assertSame( array( array( 'id' => 42, 'type' => 'node' ) ), array_values( $data['node_list'] ) );
            $this->assertSame( $data['node_list'], $tpl->vars['node_list'] );
        }
        finally
        {
            $_POST = $saved;
        }
    }

    #[DataProvider('policies')]
    public function testThePoliciesThatAllowContentExport( array $create, array $export, $expected )
    {
        $this->assertSame( $expected, eZContentObjectPackageCreator::policyAllowsContentExport( $create, $export ) );
    }

    public static function policies()
    {
        $yes = array( 'accessWord' => 'yes' );
        $no = array( 'accessWord' => 'no' );
        $limited = function ( array $policies ) { return array( 'accessWord' => 'limited', 'policies' => $policies ); };
        return array(
            'all functions'                     => array( $yes, $yes, true ),
            'no create'                         => array( $no, $yes, false ),
            'no export (the download)'          => array( $yes, $no, false ),
            'neither'                           => array( $no, $no, false ),
            'missing access word'               => array( array(), $yes, false ),
            'create limited to this wizard'     => array( $limited( array( array( 'CreatorType' => array( 'ezcontentobject' ) ) ) ), $yes, true ),
            'create limited to other wizards'   => array( $limited( array( array( 'CreatorType' => array( 'ezcontentclass', 'ezextension' ) ) ) ), $yes, false ),
            'one of two policies fits'          => array( $limited( array( array( 'CreatorType' => array( 'ezstyle' ) ),
                                                                            array( 'CreatorType' => array( 'ezcontentobject' ) ) ) ), $yes, true ),
            'create limited to content packages'=> array( $limited( array( array( 'Type' => array( 'contentobject' ) ) ) ), $yes, true ),
            'create limited to class packages'  => array( $limited( array( array( 'Type' => array( 'contentclass' ) ) ) ), $yes, false ),
            'wizard fits, type does not'        => array( $limited( array( array( 'CreatorType' => array( 'ezcontentobject' ), 'Type' => array( 'site' ) ) ) ), $yes, false ),
            'a maintainer role only'            => array( $limited( array( array( 'Role' => array( 'lead' ) ) ) ), $yes, true ),
            'export limited to content'         => array( $yes, $limited( array( array( 'Type' => array( 'contentobject' ) ) ) ), true ),
            'export limited to classes'         => array( $yes, $limited( array( array( 'Type' => array( 'contentclass' ) ) ) ), false ),
            'limited without policies'          => array( array( 'accessWord' => 'limited' ), $yes, false ),
        );
    }
}
