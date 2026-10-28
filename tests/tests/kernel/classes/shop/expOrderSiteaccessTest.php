<?php
/**
 * Orders record the siteaccess (store or site) they were placed in (expOrderSiteaccess, ezorder.siteaccess), and the
 * order lists show and filter by it.
 *
 *  - eZOrder's definition has the column; before the database update (hasSiteaccessColumn() false) it leaves it
 *    out, so an existing installation keeps storing and reading orders.
 *  - Recording: the name of the current siteaccess, '' when none is known.
 *  - The filter: the (siteaccess) view parameter, '-' for the orders without one, and the SQL and document
 *    conditions built from it, a name escaped.
 *  - Every schema of the kernel and the 6.0.0 to 6.0.15 update of each engine have the column; the templates show
 *    it with messages the catalogues translate.
 *
 * No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/shop/expOrderSiteaccessTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group shop
 */

class expOrderSiteaccessTest extends PHPUnit\Framework\TestCase
{
    /** @var string the repository */
    private $root;

    /** @var bool|null eZOrder's kept answer of hasSiteaccessColumn(), put back after each test */
    private $savedColumn;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 5 );
        $this->savedColumn = $this->columnFlag()->getValue();
    }

    protected function tearDown(): void
    {
        $this->columnFlag()->setValue( null, $this->savedColumn );
    }

    private function columnFlag()
    {
        return new ReflectionProperty( 'eZOrder', 'siteaccessColumn' );
    }

    public function testDefinitionHasTheColumn()
    {
        $this->columnFlag()->setValue( null, true );
        $definition = eZOrder::definition();
        $this->assertArrayHasKey( 'siteaccess', $definition['fields'] );
        $this->assertSame( 'Siteaccess', $definition['fields']['siteaccess']['name'] );
        $this->assertSame( 'string', $definition['fields']['siteaccess']['datatype'] );
        $this->assertSame( '', $definition['fields']['siteaccess']['default'] );
        $this->assertSame( 'siteaccessName', $definition['function_attributes']['siteaccess_name'] );
        $this->assertSame( 'storeName', $definition['function_attributes']['store_name'] );
    }

    public function testDefinitionLeavesTheColumnOutBeforeTheUpdate()
    {
        $this->columnFlag()->setValue( null, false );
        $definition = eZOrder::definition();
        $this->assertArrayNotHasKey( 'siteaccess', $definition['fields'] );
        // every other column is still there
        $all = eZOrder::definitionWithAllColumns();
        unset( $all['fields']['siteaccess'] );
        $this->assertSame( array_keys( $all['fields'] ), array_keys( $definition['fields'] ) );

        // an order read from the old table has no siteaccess, and the templates can still ask for it
        $order = new eZOrder( array( 'id' => 7, 'order_nr' => 3 ) );
        $this->assertFalse( $order->hasAttribute( 'siteaccess' ) );
        $this->assertSame( '', $order->attribute( 'siteaccess_name' ) );
    }

    public function testOrderKeepsItsSiteaccess()
    {
        $this->columnFlag()->setValue( null, true );
        $order = new eZOrder( array( 'id' => 7, 'siteaccess' => 'shop_de' ) );
        $this->assertSame( 'shop_de', $order->attribute( 'siteaccess' ) );
        $this->assertSame( 'shop_de', $order->attribute( 'siteaccess_name' ) );
        // an order placed before keeps the default
        $old = new eZOrder( array( 'id' => 8 ) );
        $this->assertSame( '', $old->attribute( 'siteaccess_name' ) );
        $this->assertSame( '', $old->attribute( 'store_name' ), 'no store for an order without a siteaccess' );
    }

    public function testListsSortBySiteaccess()
    {
        $this->assertArrayHasKey( 'siteaccess', eZOrder::sortColumnsForList() );
    }

    public function testRecordsTheCurrentSiteaccess()
    {
        $this->assertSame( 'shop_de', expOrderSiteaccess::current( array( 'name' => 'shop_de', 'type' => 2 ) ) );
        $this->assertSame( 'shop_de', expOrderSiteaccess::current( array( 'name' => ' shop_de ' ) ) );
        $this->assertSame( '', expOrderSiteaccess::current( array() ), 'no siteaccess known' );
        $this->assertSame( '', expOrderSiteaccess::current( array( 'name' => array( 'x' ) ) ) );
        $this->assertSame( 255, strlen( expOrderSiteaccess::current( array( 'name' => str_repeat( 'a', 300 ) ) ) ) );
    }

    public function testCreateOrderRecordsIt()
    {
        $source = file_get_contents( $this->root . '/kernel/classes/ezbasket.php' );
        $this->assertMatchesRegularExpression( "/'siteaccess' => expOrderSiteaccess::current\(\)/", $source );
    }

    public static function parameters()
    {
        return array(
            'none'          => array( null, null ),
            'empty'         => array( '', null ),
            'unknown'       => array( '-', '' ),
            'a name'        => array( 'shop_de', 'shop_de' ),
            'with a dot'    => array( 'shop.de-2', 'shop.de-2' ),
            'a space'       => array( 'shop de', null ),
            'a quote'       => array( "x' OR 1=1 --", null ),
            'a slash'       => array( 'a/b', null ),
            'an array'      => array( array( 'x' ), null ),
            'too long'      => array( str_repeat( 'a', 256 ), null ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'parameters' )]
    public function testFilterFromParameter( $parameter, $filter )
    {
        $this->assertSame( $filter, expOrderSiteaccess::filterFromParameter( $parameter ) );
    }

    public function testParameterRoundTrip()
    {
        foreach ( array( null, '', 'shop_de' ) as $filter )
            $this->assertSame( $filter, expOrderSiteaccess::filterFromParameter( expOrderSiteaccess::parameterFromFilter( $filter ) ) );
        $this->assertSame( '', expOrderSiteaccess::uriSuffix( null ) );
        $this->assertSame( '/(siteaccess)/-', expOrderSiteaccess::uriSuffix( '' ) );
        $this->assertSame( '/(siteaccess)/shop_de', expOrderSiteaccess::uriSuffix( 'shop_de' ) );
        $this->assertSame( '/shop/orderlist/(sort)/created/(dir)/asc/(siteaccess)/shop_de',
                           expOrderSiteaccess::filterUri( '/shop/orderlist/(sort)/created/(dir)/asc', 'shop_de' ) );
        $this->assertSame( '/shop/archivelist', expOrderSiteaccess::filterUri( '/shop/archivelist', '' ), 'all orders' );
        $this->assertSame( '/shop/archivelist', expOrderSiteaccess::filterUri( '/shop/archivelist', "x'y" ), 'no name: all orders' );
        $this->assertSame( '/shop/archivelist/(siteaccess)/-', expOrderSiteaccess::filterUri( '/shop/archivelist', '-' ) );
    }

    public function testConditions()
    {
        $db = new class {
            public function escapeString( $s ) { return str_replace( "'", "''", $s ); }
        };
        $this->assertSame( '', expOrderSiteaccess::sqlCondition( null, $db ) );
        $this->assertSame( " AND siteaccess = ''", expOrderSiteaccess::sqlCondition( '', $db ) );
        $this->assertSame( " AND o.siteaccess = 'shop_de'", expOrderSiteaccess::sqlCondition( 'shop_de', $db, 'o.siteaccess' ) );
        $this->assertSame( " AND siteaccess = 'a''b'", expOrderSiteaccess::sqlCondition( "a'b", $db ), 'escaped' );

        $this->assertSame( array(), expOrderSiteaccess::conditions( null ) );
        $this->assertSame( array( 'siteaccess' => '' ), expOrderSiteaccess::conditions( '' ) );
        $this->assertSame( array( 'siteaccess' => 'shop_de' ), expOrderSiteaccess::conditions( 'shop_de' ) );
    }

    public function testKnownSiteaccesses()
    {
        $this->assertSame( array( 'admin', 'shop_de', 'shop_fr', 'site2', 'site10' ),
                           expOrderSiteaccess::mergeKnown( array( 'shop_de', 'admin', 'site10', 'site2' ), array( '', 'shop_fr', 'shop_de', null ) ) );
        $this->assertSame( array(), expOrderSiteaccess::mergeKnown( array(), array( '' ) ) );
    }

    public function testSchemasHaveTheColumn()
    {
        $this->assertMatchesRegularExpression( "/CREATE TABLE ezorder \([^;]*\n  siteaccess varchar\(255\) NOT NULL default '',/",
                                               file_get_contents( $this->root . '/kernel/sql/mysql/kernel_schema.sql' ) );
        $this->assertMatchesRegularExpression( "/CREATE TABLE ezorder \([^;]*\n    siteaccess character varying\(255\) DEFAULT ''::character varying NOT NULL,/",
                                               file_get_contents( $this->root . '/kernel/sql/postgresql/kernel_schema.sql' ) );
        $this->assertMatchesRegularExpression( "/CREATE TABLE `ezorder` \([^;]*\n,  `siteaccess` varchar\(255\) NOT NULL DEFAULT ''/",
                                               file_get_contents( $this->root . '/kernel/sql/sqlite/schema.sql' ) );

        $schema = null;
        include $this->root . '/share/db_schema.dba';
        $this->assertSame( array( 'length' => 255, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
                           $schema['ezorder']['fields']['siteaccess'] );
    }

    public function testUpdateAddsTheColumn()
    {
        $strip = function ( $file ) {
            return preg_replace( '/^\s*--.*$/m', '', file_get_contents( $this->root . '/' . $file ) );
        };
        $mysql = $strip( 'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertSame( 1, substr_count( $mysql, "'ALTER TABLE ezorder ADD siteaccess varchar(255) NOT NULL DEFAULT '''''" ), 'one guarded ADD' );
        $this->assertStringContainsString( "AND TABLE_NAME = 'ezorder'\n        AND COLUMN_NAME = 'siteaccess' ) = 0", $mysql );

        $postgresql = $strip( 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertSame( 1, substr_count( $postgresql, "ALTER TABLE ezorder ADD siteaccess character varying(255) DEFAULT ''::character varying NOT NULL;" ) );
        $this->assertStringContainsString( "AND column_name = 'siteaccess' ) THEN", $postgresql );

        $sqlite = $strip( 'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertSame( 1, substr_count( $sqlite, "ALTER TABLE ezorder ADD COLUMN siteaccess varchar(255) NOT NULL DEFAULT '';" ) );
    }

    /** the messages the templates use, each one a catalogue translates already */
    public static function templateMessages()
    {
        return array(
            array( 'design/admin/content/job', 'Siteaccess' ),
            array( 'design/admin/content/job', 'All' ),
            array( 'design/admin/content/job', 'Filter' ),
            array( 'design/admin/settings', 'Unknown' ),
            array( 'design/admin/parts/shop/menu', 'Shop' ),
        );
    }

    public function testTemplatesShowTheSiteaccess()
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $dir = $this->root . '/design/' . $design . '/templates/shop/';
            foreach ( array( 'orderlist', 'archivelist', 'orderview', 'customerorderview' ) as $name )
                $this->assertStringContainsString( '.siteaccess_name', file_get_contents( $dir . $name . '.tpl' ), "$design $name" );
            foreach ( array( 'orderlist', 'archivelist' ) as $name )
                $this->assertStringContainsString( "design:shop/siteaccessfilter.tpl", file_get_contents( $dir . $name . '.tpl' ), "$design $name" );
            $this->assertStringContainsString( "key='siteaccess'", file_get_contents( $dir . 'orderlist.tpl' ), 'a sortable column' );
            $filter = file_get_contents( $dir . 'siteaccessfilter.tpl' );
            $this->assertStringContainsString( 'name="SiteaccessFilter"', $filter );
            $this->assertStringContainsString( 'method="get"', $filter );
        }
        foreach ( array( 'standard', 'base' ) as $design )
            $this->assertStringContainsString( '$order.store_name', file_get_contents( $this->root . '/design/' . $design . '/templates/shop/orderview.tpl' ) );
    }

    public function testMessagesAreTranslated()
    {
        $catalogues = array();
        foreach ( array( 'untranslated', 'ger-DE' ) as $language )
            $catalogues[$language] = file_get_contents( $this->root . '/share/translations/' . $language . '/translation.ts' );
        foreach ( self::templateMessages() as list( $context, $source ) )
        {
            foreach ( $catalogues as $language => $ts )
            {
                $this->assertSame( 1, preg_match( '#<context>\s*<name>' . preg_quote( $context, '#' ) . '</name>(.*?)</context>#s', $ts, $block ), "$language $context" );
                $pattern = $language === 'untranslated'
                    ? '#<source>' . preg_quote( $source, '#' ) . '</source>#'
                    : '#<source>' . preg_quote( $source, '#' ) . '</source>\s*<translation>[^<]+</translation>#';
                $this->assertSame( 1, preg_match( $pattern, $block[1] ), "$language: $context / $source" );
            }
        }
    }
}
