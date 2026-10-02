<?php
/**
 * Test doubles for the subitems tests: nodes, objects, attributes and classes that need no
 * database, a counting column, handler functions and a registry reading the fixture INI files.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSubitemsTestNode extends eZContentObjectTreeNode
{
    public $fake;
    public $fakeObject;

    public function __construct( array $fake = array(), $object = null )
    {
        parent::__construct( array() );
        $this->fake = $fake;
        $this->fakeObject = $object;
    }

    public function attribute( $attr, $noFunction = false )
    {
        return array_key_exists( $attr, $this->fake ) ? $this->fake[$attr] : null;
    }

    public function hasAttribute( $attr )
    {
        return array_key_exists( $attr, $this->fake );
    }

    public function object()
    {
        return $this->fakeObject;
    }

    public function sortArray()
    {
        return array( array( 'path', true ) );
    }
}

class expSubitemsTestObject extends eZContentObject
{
    public $fake;
    public $fakeDataMap;

    public function __construct( array $fake = array(), array $dataMap = array() )
    {
        parent::__construct( array() );
        $this->fake = $fake;
        $this->fakeDataMap = $dataMap;
    }

    public function attribute( $attr, $noFunction = false )
    {
        return array_key_exists( $attr, $this->fake ) ? $this->fake[$attr] : null;
    }

    public function dataMap()
    {
        return $this->fakeDataMap;
    }
}

class expSubitemsTestObjectAttribute extends eZContentObjectAttribute
{
    public $fakeTitle;
    public $fakeString;
    public $fakeHasContent;

    public function __construct( $title, $string, $hasContent = true )
    {
        parent::__construct( array() );
        $this->fakeTitle = $title;
        $this->fakeString = $string;
        $this->fakeHasContent = $hasContent;
    }

    public function attribute( $attr, $noFunction = false )
    {
        return $attr === 'has_content' ? $this->fakeHasContent : null;
    }

    function title()
    {
        return $this->fakeTitle;
    }

    function toString()
    {
        return $this->fakeString;
    }
}

class expSubitemsTestClass extends eZContentClass
{
    public $fake;

    public function __construct( array $fake )
    {
        parent::__construct( array() );
        $this->fake = $fake;
    }

    public function attribute( $attr, $noFunction = false )
    {
        return array_key_exists( $attr, $this->fake ) ? $this->fake[$attr] : null;
    }
}

class expSubitemsTestDataType extends eZDataType
{
    public $sortKeyType;

    public function __construct( $dataTypeString, $sortKeyType )
    {
        parent::__construct( $dataTypeString, $dataTypeString );
        $this->sortKeyType = $sortKeyType;
    }

    function sortKeyType()
    {
        return $this->sortKeyType;
    }
}

class expSubitemsTestClassAttribute extends eZContentClassAttribute
{
    public $fake;
    public $fakeDataType;

    public function __construct( array $fake, $sortKeyType = 'string' )
    {
        parent::__construct( array() );
        $this->fake = $fake;
        $this->fakeDataType = new expSubitemsTestDataType( $fake['data_type_string'], $sortKeyType );
    }

    public function attribute( $attr, $noFunction = false )
    {
        return array_key_exists( $attr, $this->fake ) ? $this->fake[$attr] : null;
    }

    function dataType()
    {
        return $this->fakeDataType;
    }
}

/** A column that counts how often its value is computed. */
class expSubitemsTestCountingColumn extends expSubitemsColumn
{
    public static $calls = array();

    public function value( eZContentObjectTreeNode $node )
    {
        self::$calls[$this->key] = ( isset( self::$calls[$this->key] ) ? self::$calls[$this->key] : 0 ) + 1;
        return $this->key . ':' . $node->attribute( 'node_id' );
    }
}

/** A column that throws. */
class expSubitemsTestThrowingColumn extends expSubitemsColumn
{
    public function value( eZContentObjectTreeNode $node )
    {
        throw new RuntimeException( 'broken on purpose' );
    }
}

class expSubitemsTestFixtures
{
    public static $handlerCalls = 0;

    public static function handlerValue( eZContentObjectTreeNode $node, array $settings, expSubitemsColumn $column )
    {
        self::$handlerCalls++;
        return '<b>' . $settings['Name'] . '</b> #' . $node->attribute( 'node_id' );
    }

    public static function linkValue( eZContentObjectTreeNode $node, array $settings, expSubitemsColumn $column )
    {
        return $node->attribute( 'link' );
    }

    /**
     * Builds the kernel objects once, so the shutdown and exception handlers the kernel registers
     * on first use (eZExecution) are not reported against a single test.
     */
    public static function warmUp()
    {
        new expSubitemsTestClassAttribute( array( 'identifier' => 'x', 'data_type_string' => 'ezstring' ) );
        new expSubitemsTestClass( array() );
        self::children( 1 );
        eZLocale::instance();
        ezpI18n::tr( 'design/admin/node/view/full', 'Name' );
    }

    /** The fixture directory, relative to the installation root (eZINI reads relative paths). */
    public static function dir()
    {
        return 'tests/tests/kernel/classes/subitems/fixtures';
    }

    /**
     * A registry on the fixture INI files.
     *
     * @return expSubitemsTestRegistry
     */
    public static function registry()
    {
        return new expSubitemsTestRegistry( eZINI::fetchFromFile( self::dir() . '/subitems.ini' ),
                                            eZINI::fetchFromFile( self::dir() . '/subitemscolumns.ini' ) );
    }

    /**
     * A parent node under /1/2/<id>/.
     */
    public static function parent( $nodeID = 60, $classIdentifier = 'folder', $path = null )
    {
        return new expSubitemsTestNode( array( 'node_id' => $nodeID, 'path_string' => $path !== null ? $path : "/1/2/$nodeID/",
                                               'class_identifier' => $classIdentifier, 'name' => 'Parent' ) );
    }

    /**
     * Child nodes 101.. with objects of a class.
     */
    public static function children( $count, $classIdentifier = 'article' )
    {
        $nodes = array();
        for ( $i = 1; $i <= $count; $i++ )
        {
            $object = new expSubitemsTestObject( array( 'class_identifier' => $classIdentifier, 'name' => "Item $i" ),
                                                 array( 'title' => new expSubitemsTestObjectAttribute( "Title $i", "Title $i" ) ) );
            $nodes[] = new expSubitemsTestNode( array( 'node_id' => 100 + $i, 'name' => "Item $i", 'priority' => $i,
                                                       'contentobject_id' => 200 + $i, 'remote_id' => "r$i",
                                                       'link' => "/item-$i", 'hidden_status_string' => 'Visible' ), $object );
        }
        return $nodes;
    }
}

/** The registry without database lookups: attribute columns and class attributes are given. */
class expSubitemsTestRegistry extends expSubitemsColumnRegistry
{
    public $fakeAttributeColumns = array();
    public $fakeClassAttributes = array( 'article/title', 'article/intro' );

    public function attributeColumns( eZContentObjectTreeNode $parent )
    {
        return $this->attributeColumnsEnabled() ? $this->fakeAttributeColumns : array();
    }

    protected function classAttributeExists( $classIdentifier, $attributeIdentifier )
    {
        return in_array( "$classIdentifier/$attributeIdentifier", $this->fakeClassAttributes, true );
    }
}
