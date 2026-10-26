<?php
/**
 * The allowed classes of the object relation datatype (ezobjectrelation) in a package: they were not
 * exported, and a class imported from a package allowed every class.
 *
 *  OR-01 - the allowed classes are exported as <class-constraints>, the format of ezobjectrelationlist
 *  OR-02 - and read back on import
 *  OR-03 - a package from before (no <class-constraints>) keeps the class attribute's allowed classes
 *
 * No database: the class attribute is held in memory (see eZDatatypeTestFixtures.php).
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group datatypes
 */
require_once __DIR__ . '/eZDatatypeTestFixtures.php';

class eZObjectRelationClassConstraintsTest extends eZDatatypeTestCase
{
    private function relationClass( array $allowed )
    {
        $type = $this->dataType( 'ezobjectrelation' );
        $classAttribute = $this->classAttribute( 'ezobjectrelation', array( 'data_int1' => 2, 'data_int3' => 1 ) );
        $type->storeClassAttributeContent( $classAttribute, array( 'class_constraint_list' => $allowed ) );
        return $classAttribute;
    }

    /** OR-01 */
    public function testAllowedClassesAreExported()
    {
        list( , $xml ) = $this->roundTripClassParameters( $this->relationClass( array( 'article', 'image' ) ) );
        $this->assertStringContainsString( '<class-constraints><class-constraint class-identifier="article"/>'
                                           . '<class-constraint class-identifier="image"/></class-constraints>', $xml );
        list( , $xml ) = $this->roundTripClassParameters( $this->relationClass( array() ) );
        $this->assertStringContainsString( '<class-constraints/>', $xml );
    }

    /** OR-02 */
    public function testAllowedClassesAreImported()
    {
        list( $copy ) = $this->roundTripClassParameters( $this->relationClass( array( 'article', 'image' ) ) );
        $content = $copy->content();
        $this->assertSame( array( 'article', 'image' ), $content['class_constraint_list'] );
        $this->assertEquals( 2, $content['selection_type'] );
        $this->assertEquals( 1, $content['fuzzy_match'] );
    }

    /** OR-03 */
    public function testAnOlderPackageKeepsTheAllowedClasses()
    {
        $type = $this->dataType( 'ezobjectrelation' );
        $classAttribute = $this->relationClass( array( 'folder' ) );
        $dom = new DOMDocument();
        $dom->loadXML( '<attribute><datatype-parameters><selection-type id="0"/><fuzzy-match id="0"/></datatype-parameters></attribute>' );
        $type->unserializeContentClassAttribute( $classAttribute, $dom->documentElement, $dom->documentElement->firstChild );
        $this->assertSame( array( 'folder' ), $classAttribute->content()['class_constraint_list'] );
    }
}
