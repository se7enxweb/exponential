<?php
/**
 * Tests of the decisions of eZPackageComparisonImport that need no database: which items of a comparison are
 * offered for import and why the others are not (identical, only on the site, a class the site lacks and the
 * package does not bring, differences an import cannot bring), and which class an object needs imported first
 * for values whose attributes the site's class lacks.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZPackageComparisonImportOfferTest extends PHPUnit\Framework\TestCase
{
    private static $built = 0;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    private static function object( $status, array $extra = array() )
    {
        return $extra + array( 'kind' => 'object', 'status' => $status, 'class_identifier' => 'article', 'name' => 'An article',
                               'fields' => 0, 'missing_fields' => 0, 'lang_package' => 0, 'lang_site' => 0, 'missing_attributes' => array() );
    }

    private static function index( array $classes )
    {
        $items = array();
        foreach ( $classes as $i => $class )
            $items[] = $class + array( 'kind' => 'class', 'index' => $i, 'name' => ucfirst( $class['class_identifier'] ), 'addable' => array() );
        $items[] = self::object( 'new' );
        // a new 'built' each time: the class lookup is cached per index
        return array( 'items' => $items, 'stamp' => 'k1', 'built' => ++self::$built );
    }

    public function testStatusReasons()
    {
        $this->assertNotSame( '', eZPackageComparisonImport::statusReason( array( 'status' => 'identical' ) ) );
        $this->assertNotSame( '', eZPackageComparisonImport::statusReason( array( 'status' => 'removed' ) ) );
        $this->assertSame( '', eZPackageComparisonImport::statusReason( array( 'status' => 'changed' ) ) );
        $this->assertSame( '', eZPackageComparisonImport::statusReason( array( 'status' => 'new' ) ) );
    }

    public function testIdenticalAndRemovedItemsAreNotOffered()
    {
        $index = self::index( array() );
        foreach ( array( 'identical', 'removed' ) as $status )
        {
            $state = eZPackageComparisonImport::offerState( self::object( $status ), $index );
            $this->assertFalse( $state['offered'], $status );
            $this->assertNotSame( '', $state['reason'] );
        }
    }

    public function testNewObjectsAndChangedClassesAreOffered()
    {
        $index = self::index( array() );
        $this->assertTrue( eZPackageComparisonImport::isOffered( self::object( 'new' ), $index ) );
        $this->assertTrue( eZPackageComparisonImport::isOffered( array( 'kind' => 'class', 'status' => 'changed' ), $index ) );
        $this->assertFalse( eZPackageComparisonImport::isOffered( array( 'kind' => 'class', 'status' => 'identical' ), $index ) );
    }

    public function testChangedObjectIsOfferedForItsOwnValuesOrNewTranslations()
    {
        $index = self::index( array() );
        $this->assertTrue( eZPackageComparisonImport::isOffered( self::object( 'changed', array( 'fields' => 2 ) ), $index ) );
        $this->assertTrue( eZPackageComparisonImport::isOffered( self::object( 'changed', array( 'lang_package' => 1 ) ), $index ) );
        $state = eZPackageComparisonImport::offerState( self::object( 'changed', array( 'lang_site' => 1 ) ), $index );
        $this->assertFalse( $state['offered'], 'only a translation the site alone has: nothing to import' );
        $this->assertNotSame( '', $state['reason'] );
    }

    public function testValuesForAttributesTheClassImportAddsNeedThatClass()
    {
        $index = self::index( array( array( 'class_identifier' => 'article', 'status' => 'changed', 'addable' => array( 'subtitle' ) ) ) );
        $item = self::object( 'changed', array( 'fields' => 1, 'missing_fields' => 1, 'missing_attributes' => array( 'subtitle' ) ) );
        $dependency = eZPackageComparisonImport::dependency( $item, $index );
        $this->assertSame( array( 'class_index' => 0, 'class_name' => 'Article', 'addable' => array( 'subtitle' ), 'blocked' => array(), 'whole_class' => false ), $dependency );
        $state = eZPackageComparisonImport::offerState( $item, $index );
        $this->assertTrue( $state['offered'] );
        $this->assertSame( 0, $state['needs_class'] );
    }

    public function testValuesNoClassImportCanAddAreNotOffered()
    {
        $index = self::index( array( array( 'class_identifier' => 'article', 'status' => 'changed', 'addable' => array() ) ) );
        $item = self::object( 'changed', array( 'fields' => 1, 'missing_fields' => 1, 'missing_attributes' => array( 'odd' ) ) );
        $dependency = eZPackageComparisonImport::dependency( $item, $index );
        $this->assertSame( array( 'odd' ), $dependency['blocked'] );
        $state = eZPackageComparisonImport::offerState( $item, $index );
        $this->assertFalse( $state['offered'] );
        $this->assertStringContainsString( 'odd', $state['reason'] );
        $this->assertSame( array( 'odd' ), $state['blocked'] );
    }

    public function testAnIdenticalClassAddsNothing()
    {
        $index = self::index( array( array( 'class_identifier' => 'article', 'status' => 'identical', 'addable' => array( 'subtitle' ) ) ) );
        $dependency = eZPackageComparisonImport::dependency( self::object( 'changed', array( 'missing_attributes' => array( 'subtitle' ) ) ), $index );
        $this->assertNull( $dependency['class_index'] );
        $this->assertSame( array( 'subtitle' ), $dependency['blocked'] );
    }

    public function testObjectWhoseClassTheSiteLacks()
    {
        $item = self::object( 'class_missing' );
        $withClass = self::index( array( array( 'class_identifier' => 'article', 'status' => 'new' ) ) );
        $dependency = eZPackageComparisonImport::dependency( $item, $withClass );
        $this->assertTrue( $dependency['whole_class'] );
        $this->assertSame( array( '*' ), $dependency['addable'] );
        $this->assertTrue( eZPackageComparisonImport::isOffered( $item, $withClass ) );

        $state = eZPackageComparisonImport::offerState( $item, self::index( array() ) );
        $this->assertFalse( $state['offered'] );
        $this->assertStringContainsString( 'article', $state['reason'] );
        $this->assertSame( 'article', eZPackageComparisonImport::dependency( $item, self::index( array() ) )['class_name'] );
    }

    public function testObjectsWithoutMissingAttributesHaveNoDependency()
    {
        $index = self::index( array() );
        $this->assertNull( eZPackageComparisonImport::dependency( self::object( 'changed', array( 'fields' => 1 ) ), $index ) );
        $this->assertNull( eZPackageComparisonImport::dependency( array( 'kind' => 'class', 'status' => 'new' ), $index ) );
    }
}
