<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/** The filter registry and the filter types that build the condition of a virtual list. */
class cjwNewsletterFilterTest extends cjwNewsletterTestCase
{
    public function testXmlRoundTripKeepsIdentifierOperationAndValues()
    {
        $filter = new CjwNewsletterFilter();
        $filter->addFilter( 'cjwnl_email', 'like', array( 'a@b', 'c@d' ) );
        $xml = $filter->toXML();
        $this->assertStringContainsString( '<identifier>cjwnl_email</identifier>', $xml );
        $other = new CjwNewsletterFilter();
        $other->fromXML( $xml );
        $active = $other->getFilterTypesActive();
        $this->assertCount( 1, $active );
        $this->assertSame( 'like', $active[0]->attribute( 'operation' ) );
        $this->assertSame( array( 'a@b', 'c@d' ), $active[0]->attribute( 'values' ) );
    }

    public function testDamagedXmlLeavesNoFilter()
    {
        $filter = new CjwNewsletterFilter();
        $filter->addFilter( 'cjwnl_email', 'eq', array( 'x' ) );
        $filter->fromXML( '<not xml' );
        $this->assertCount( 0, $filter->getFilterTypesActive() );
    }

    public function testFilterTypeAttributes()
    {
        $filter = new CjwNewsletterFilter();
        $type = $filter->getFilterTypesAvailable()['cjwnl_email'];
        foreach ( $type->attributes() as $name )
            $this->assertTrue( $type->hasAttribute( $name ) );
        $this->assertFalse( $type->hasAttribute( 'nonsense' ) );
        $this->assertSame( 'cjwnl_email', $type->attribute( 'identifier' ) );
        $this->assertNotEmpty( $type->attribute( 'operations_available' ) );
        $this->assertSame( 'cjwnl_email', $type->viewTemplate() );
        $this->assertSame( 'cjwnl_email', $type->editTemplate() );
        $this->assertNull( $type->attribute( 'nonsense' ), 'an unknown attribute is null, with a debug error only' );
    }

    public function testDbQueryPartsOfTheActiveFilters()
    {
        $filter = new CjwNewsletterFilter();
        $filter->addFilter( 'cjwnl_email', 'like', array( 'abc' ) );
        $filter->addFilter( 'cjwnl_salutation', 'eq', array( 1 ) );
        $parts = $filter->getDbQueryPartArray();
        $this->assertNotEmpty( $parts );
        foreach ( $parts as $namespace => $part )
        {
            $this->assertArrayHasKey( 'tables', $part );
            $this->assertArrayHasKey( 'conds', $part );
        }
    }

    public function testMergeOfQueryArrays()
    {
        $merged = CjwNewsletterFilter::mergeDbQueryArray( array( 'fields' => array( 'a' ), 'tables' => array( 't' ), 'conds' => array() ),
                                                          array( 'fields' => array( 'a', 'b' ), 'tables' => array( 't' ), 'conds' => array( 'c1' ) ) );
        $this->assertSame( array( 'a', 'b' ), array_values( $merged['fields'] ) );
        $this->assertSame( array( 't' ), array_values( $merged['tables'] ) );
        $this->assertSame( array( 'c1' ), $merged['conds'] );
        $this->assertSame( array( 'x' => 1 ), CjwNewsletterFilter::mergeDbQueryArray( array( 'x' => 1 ), false ) );
    }

    public function testConditionArrayForEveryOperator()
    {
        $type = ( new CjwNewsletterFilter() )->getFilterTypesAvailable()['cjwnl_email'];
        foreach ( array( 'eq', 'gt', 'ge', 'gte', 'lt', 'le', 'lte', 'ne', 'like', '*like*' ) as $operator )
        {
            $cond = $type->createConditionArray( 'cjwnl_user.email', $operator, array( 'x' ) );
            $this->assertIsArray( $cond, $operator );
        }
    }
}
