<?php
/**
 * The order export of the commerce services (expCommerceExport::order()) names the siteaccess (store or site) the
 * order was placed in, null when it is not known: an order placed before it was recorded, Oracle's NULL, or a
 * database without the column. No database: the orders are stand-ins that answer the export's questions.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** An order that needs no database: no account, no currency, fixed totals. */
class expCommerceExportSiteaccessTestOrder extends eZOrder
{
    function accountName() { return 'Test Customer'; }
    function currencyCode( $collection = false ) { return 'EUR'; }
    function totalIncVAT() { return 25.0; }
    function totalExVAT() { return 21.01; }
    function statusName() { return 'Pending'; }
}

class expCommerceExportSiteaccessTest extends PHPUnit\Framework\TestCase
{
    /** @var bool|null eZOrder's kept answer of hasSiteaccessColumn(), put back after each test */
    private $savedColumn;

    protected function setUp(): void
    {
        $this->savedColumn = ( new ReflectionProperty( 'eZOrder', 'siteaccessColumn' ) )->getValue();
        ( new ReflectionProperty( 'eZOrder', 'siteaccessColumn' ) )->setValue( null, true );
    }

    protected function tearDown(): void
    {
        ( new ReflectionProperty( 'eZOrder', 'siteaccessColumn' ) )->setValue( null, $this->savedColumn );
    }

    private function order( $siteaccess )
    {
        return new expCommerceExportSiteaccessTestOrder( array( 'id' => 7, 'order_nr' => 3, 'user_id' => 10, 'created' => 1700000000,
                                                                'status_id' => 1, 'is_temporary' => 0, 'siteaccess' => $siteaccess ) );
    }

    public function testTheExportNamesTheSiteaccess()
    {
        $out = expCommerceExport::order( $this->order( 'shop_de' ) );
        $this->assertSame( 'shop_de', $out['siteaccess'] );
        $this->assertSame( 25.0, $out['total_inc_vat'], 'the rest of the export is unchanged' );
    }

    public function testUnknownIsNull()
    {
        $this->assertNull( expCommerceExport::order( $this->order( '' ) )['siteaccess'] );
        $this->assertNull( expCommerceExport::order( $this->order( null ) )['siteaccess'], "Oracle stores '' as NULL" );

        // before the database update the order has no such field at all
        ( new ReflectionProperty( 'eZOrder', 'siteaccessColumn' ) )->setValue( null, false );
        $this->assertNull( expCommerceExport::order( $this->order( 'shop_de' ) )['siteaccess'] );
    }
}
