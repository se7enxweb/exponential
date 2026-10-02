<?php
/**
 * The three worked examples of the guide ("Adding a column in 3 minutes": a Handler, a Class and
 * a Template column), loaded from fixtures/subitemscolumns.ini.append.php into a registry and
 * computed the way the rows server function computes them.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSubitemsColumnsTestCase.php';

class expSubitemsGuideExamplesTest extends expSubitemsColumnsTestCase
{
    protected function exampleRegistry()
    {
        $ini = new eZINI( 'subitemscolumns.ini.append.php', 'tests/tests/kernel/classes/subitems/columns/fixtures', null, false, null, true );
        return new expSubitemsColumnRegistry( null, $ini );
    }

    public function testTheExamplesAreOfferedAndComputed()
    {
        $registry = $this->exampleRegistry();
        $root = $this->contentRoot();
        $available = $registry->availableColumns( $root );
        foreach ( array( 'mydaysonline' => 'expSubitemsCallableColumn', 'myreadingtime' => 'expSubitemsReadingTimeColumn',
                         'mystatus' => 'expSubitemsTemplateColumn' ) as $key => $class )
        {
            $this->assertArrayHasKey( $key, $available, "$key is offered" );
            $this->assertInstanceOf( $class, $available[$key] );
        }
        $mine = array_values( array_intersect( array_keys( $available ), array( 'mydaysonline', 'myreadingtime', 'mystatus' ) ) );
        $this->assertSame( array( 'mydaysonline', 'myreadingtime', 'mystatus' ), $mine, 'sorted by Order' );
        $this->assertArrayNotHasKey( 'parentnodeid', $available, 'a registry of this file alone has only its columns and the built-ins' );

        $children = $root->subTree( array( 'Depth' => 1, 'DepthOperator' => 'eq', 'Limit' => 5 ) );
        $this->assertNotEmpty( $children );
        $values = expSubitemsServerFunctions::columnValues( $children, $registry->resolveColumns( array( 'mydaysonline', 'myreadingtime', 'mystatus' ), $root ) );
        foreach ( array_values( $children ) as $i => $child )
        {
            $cells = (array)$values[$i]; // one cell object per row, in the rows' order
            $published = (int)$child->attribute( 'object' )->attribute( 'published' );
            $this->assertSame( (int)floor( ( time() - $published ) / 86400 ), $cells['mydaysonline']['v'] );
            $this->assertContains( $cells['mystatus']['v'], array( 'Visible', 'Hidden', 'Hidden by a parent', 'Locked' ) );
            $this->assertStringContainsString( 'exp-subitems-badge', $cells['mystatus']['h'] );
            $this->assertTrue( $cells['myreadingtime']['v'] === null || $cells['myreadingtime']['v'] >= 1 );
        }
    }

    public function testReadingTimeUsesItsSetting()
    {
        $node = $this->nodeWithDataType( 'ezxmltext' );
        $words = $this->value( 'wordcount', $node );
        if ( !$words )
            $this->markTestSkipped( 'no main text' );
        $column = $this->exampleRegistry()->definedColumn( 'myreadingtime' );
        $this->assertSame( max( 1, (int)ceil( $words / 250 ) ), $column->value( $node ) );
        $this->assertSame( max( 1, (int)ceil( $words / 200 ) ), $this->value( 'readingtime', $node ), 'the shipped block reads 200 a minute' );
        $this->assertStringEndsWith( ' min', $column->html( $node, $column->value( $node ) ) );
        $this->assertNull( $column->value( $this->adminUserNode() ) );
    }
}
