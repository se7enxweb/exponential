<?php
/**
 * The inference of bin/php/expbackfillordersiteaccess.php (expOrderSiteaccessBackfill::infer()): an order whose
 * products all lie in the tree of exactly one siteaccess gets that siteaccess; none or several leave it unknown, and
 * so does a product no longer in the tree. Administrations are never candidates: they are split off as the siteaccess
 * menu decides it (ezpSiteAccessMenu::isAdministration()). With "prefer the deepest root", off by default, of nested
 * public sites the one with the deepest root wins, but two with the same root stay ambiguous. Fed with path strings,
 * siteaccess roots and siteaccess settings; no database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/shop/expOrderSiteaccessBackfillTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group shop
 */

use PHPUnit\Framework\Attributes\DataProvider;

class expOrderSiteaccessBackfillTest extends PHPUnit\Framework\TestCase
{
    /** public sites only (the administrations are left out before): a site on the whole tree, two shops nested in it,
        and a mobile twin of one shop with the same root */
    const ROOTS = array( 'shop_de' => '/1/2/100/', 'shop_fr' => '/1/2/200/', 'site' => '/1/2/', 'shop_de_mobile' => '/1/2/100/' );

    public static function cases()
    {
        return array(
            'one shop only' => array( array( '/1/2/200/210/' ), array( 'shop_fr' => '/1/2/200/', 'shop_de' => '/1/2/100/' ), false, 'shop_fr', 'the only siteaccess' ),
            'all items in one shop' => array( array( '/1/2/200/210/', '/1/2/200/220/221/' ), array( 'shop_fr' => '/1/2/200/', 'shop_de' => '/1/2/100/' ), false, 'shop_fr', 'the only siteaccess' ),
            'items in two shops' => array( array( '/1/2/100/110/', '/1/2/200/210/' ), array( 'shop_fr' => '/1/2/200/', 'shop_de' => '/1/2/100/' ), false, '', 'no siteaccess serves every product' ),
            'outside every tree' => array( array( '/1/43/50/' ), array( 'shop_fr' => '/1/2/200/' ), false, '', 'lies in no siteaccess' ),
            'removed product' => array( array( '/1/2/200/210/', null ), array( 'shop_fr' => '/1/2/200/' ), false, '', 'no longer in the content tree' ),
            'no items' => array( array(), array( 'shop_fr' => '/1/2/200/' ), false, '', 'no product items' ),
            'nested sites, strict' => array( array( '/1/2/200/210/' ), self::ROOTS, false, '', 'several siteaccesses serve its products: shop_fr, site' ),
            'deepest root wins' => array( array( '/1/2/200/210/' ), self::ROOTS, true, 'shop_fr', 'the deepest root' ),
            'same deepest root twice' => array( array( '/1/2/100/110/' ), self::ROOTS, true, '', 'several siteaccesses with the same root serve its products: shop_de, shop_de_mobile' ),
            'only the whole-tree site' => array( array( '/1/2/300/' ), self::ROOTS, false, 'site', 'the only siteaccess' ),
            'two shops, administrations out' => array( array( '/1/2/200/210/' ), array( 'shop_de' => '/1/2/100/', 'shop_fr' => '/1/2/200/' ), false, 'shop_fr', 'the only siteaccess' ),
            'root node itself' => array( array( '/1/2/200/' ), array( 'shop_fr' => '/1/2/200/' ), false, 'shop_fr', 'the only siteaccess' ),
            'prefix is not a sibling' => array( array( '/1/2/2000/' ), array( 'shop_fr' => '/1/2/200/' ), false, '', 'lies in no siteaccess' ),
        );
    }

    #[DataProvider( 'cases' )]
    public function testInfer( array $paths, array $roots, $preferDeepest, $siteaccess, $reason )
    {
        $result = expOrderSiteaccessBackfill::infer( $paths, $roots, $preferDeepest );
        $this->assertSame( $siteaccess, $result['siteaccess'] );
        $this->assertStringContainsString( $reason, $result['reason'] );
    }

    public static function excludeCases()
    {
        return array(
            'excluded only candidate leaves it unknown' => array( array( '/1/2/200/210/' ), array( 'shop_fr' => '/1/2/200/', 'shop_de' => '/1/2/100/' ), false, array( 'shop_fr' ), '', 'only excluded siteaccesses serve its products: shop_fr', true ),
            'excluded candidate skipped, next one wins' => array( array( '/1/2/200/210/' ), self::ROOTS, false, array( 'shop_fr' ), 'site', 'the only siteaccess', false ),
            'excluding the whole-tree site resolves nesting' => array( array( '/1/2/200/210/' ), self::ROOTS, false, array( 'site' ), 'shop_fr', 'the only siteaccess', false ),
            'excluded twin resolves same deepest root' => array( array( '/1/2/100/110/' ), self::ROOTS, true, array( 'shop_de_mobile' ), 'shop_de', 'the deepest root', false ),
            'deepest excluded, next deepest wins' => array( array( '/1/2/100/110/' ), self::ROOTS, true, array( 'shop_de', 'shop_de_mobile' ), 'site', 'the only siteaccess', false ),
            'all candidates excluded' => array( array( '/1/2/100/110/' ), self::ROOTS, true, array( 'site', 'shop_de_mobile', 'shop_de' ), '', 'only excluded siteaccesses serve its products: shop_de, shop_de_mobile, site', true ),
            'excluding a non-candidate changes nothing' => array( array( '/1/2/200/210/' ), array( 'shop_fr' => '/1/2/200/', 'shop_de' => '/1/2/100/' ), false, array( 'shop_de', 'nosuch' ), 'shop_fr', 'the only siteaccess', false ),
        );
    }

    #[DataProvider( 'excludeCases' )]
    public function testInferWithExclude( array $paths, array $roots, $preferDeepest, array $exclude, $siteaccess, $reason, $excluded )
    {
        $result = expOrderSiteaccessBackfill::infer( $paths, $roots, $preferDeepest, $exclude );
        $this->assertSame( $siteaccess, $result['siteaccess'] );
        $this->assertStringContainsString( $reason, $result['reason'] );
        $this->assertSame( $excluded, !empty( $result['excluded'] ) );
    }

    public function testExcludeListIsParsedAndUnknownNamesReported()
    {
        $available = array( 'site', 'shop_de', 'shop_fr', 'admin' );
        $unknown = array( 'stale' );
        // a comma list in one value, the option repeated, blanks and duplicates dropped
        $this->assertSame( array( 'shop_de', 'shop_fr', 'site' ),
                           expOrderSiteaccessBackfill::parseExclude( array( ' shop_de, shop_fr ,', 'site,shop_de', '' ), $available, $unknown ) );
        $this->assertSame( array(), $unknown );
        // a single value as a string; unknown names are kept (they match nothing) and reported for a warning
        $this->assertSame( array( 'shop_fr', 'nosuch', 'other' ),
                           expOrderSiteaccessBackfill::parseExclude( 'shop_fr,nosuch,other', $available, $unknown ) );
        $this->assertSame( array( 'nosuch', 'other' ), $unknown );
        // the option not given
        $this->assertSame( array(), expOrderSiteaccessBackfill::parseExclude( null, $available, $unknown ) );
        $this->assertSame( array(), $unknown );
    }

    /** siteaccess => its settings as expOrderSiteaccessBackfill::administrationInfo() reads them */
    const SETTINGS = array(
        'site'       => array( 'site_design' => 'simple', 'site_designs' => array( 'simple', 'base', 'standard' ), 'front_site_access' => '' ),
        'shop_de'    => array( 'site_design' => 'shop', 'site_designs' => array( 'shop', 'standard' ), 'front_site_access' => '' ),
        'admin'      => array( 'site_design' => 'admin4', 'site_designs' => array( 'admin4', 'admin', 'standard' ), 'front_site_access' => 'site' ),
        'adminui'    => array( 'site_design' => 'adminui', 'site_designs' => array( 'adminui' ), 'front_site_access' => '' ),
        'editor'     => array( 'site_design' => 'editor', 'site_designs' => array( 'editor', 'admin4', 'admin' ), 'front_site_access' => '' ),
        'backoffice' => array( 'site_design' => 'office', 'site_designs' => array( 'office' ), 'front_site_access' => 'site' ),
    );

    public function testAdministrationsAreLeftOutAsTheSiteaccessMenuDecides()
    {
        $info = function ( $name ) { return self::SETTINGS[$name]; };
        $names = array_keys( self::SETTINGS );

        // AdminSiteAccessList[] empty: by the design (the editor falls back to admin designs) and FrontSiteAccess
        $this->assertSame( array( 'sites' => array( 'site', 'shop_de' ), 'administrations' => array( 'admin', 'adminui', 'editor', 'backoffice' ) ),
                           expOrderSiteaccessBackfill::splitAdministrations( $names, array(), $info ) );
        // AdminSiteAccessList[] set: it alone decides
        $this->assertSame( array( 'sites' => array( 'site', 'shop_de', 'admin', 'adminui', 'editor' ), 'administrations' => array( 'backoffice' ) ),
                           expOrderSiteaccessBackfill::splitAdministrations( $names, array( 'backoffice' ), $info ) );
        // the menu's own decision, which the split reuses
        $this->assertTrue( ezpSiteAccessMenu::isAdministration( 'editor', array(), self::SETTINGS['editor'] ) );
        $this->assertFalse( ezpSiteAccessMenu::isAdministration( 'editor', array(), array( 'site_design' => 'editor', 'front_site_access' => '' ) ),
                            'without its design list the editor is decided by SiteDesign alone, as the menu does' );
        $this->assertFalse( ezpSiteAccessMenu::isAdministration( 'shop_de', array(), self::SETTINGS['shop_de'] ) );
    }

    public function testPathsAreNormalized()
    {
        $this->assertSame( '/1/2/61/', expOrderSiteaccessBackfill::normalizePath( '/1/2/61' ) );
        $this->assertSame( '/1/2/61/', expOrderSiteaccessBackfill::normalizePath( '1/2/61/' ) );
        $this->assertSame( '', expOrderSiteaccessBackfill::normalizePath( '' ) );
        $this->assertSame( '', expOrderSiteaccessBackfill::normalizePath( null ) );
        $this->assertSame( '', expOrderSiteaccessBackfill::normalizePath( '/1/x/' ) );
        // a root given without the trailing slash still serves only its own subtree
        $this->assertSame( array( 'shop_fr' => 3 ), expOrderSiteaccessBackfill::siteaccessesServing( '/1/2/200/5/', array( 'shop_fr' => '/1/2/200', 'other' => '/1/2/20' ) ) );
    }

    public function testTheScriptNeverOverwritesAndDefaultsToADryRun()
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/commands/expbackfillordersiteaccess.php' );
        $this->assertStringContainsString( '"[apply][prefer-deepest-root][exclude:*]"', $source );
        $this->assertStringContainsString( 'is not in [SiteAccessSettings] AvailableSiteAccessList', $source, 'an unknown --exclude name is a warning' );
        $this->assertStringContainsString( "'excluded siteaccesses: '", $source, 'the summary names the excluded siteaccesses' );
        $this->assertStringContainsString( '--exclude=site_mobile,site_preview', $source, '--help shows an --exclude example' );
        $this->assertStringContainsString( 'Off by default.', $source, '--help says --prefer-deepest-root is off by default' );
        $this->assertStringContainsString( 'an administration, never takes orders', $source, 'the dry run names the administrations it left out' );
        $this->assertMatchesRegularExpression( "/if \( \\\$order->siteaccessName\(\) !== '' \)\s*\{\s*\\\$counts\['kept'\]\+\+;\s*continue;/", $source );
        $this->assertMatchesRegularExpression( '/if \( \$apply \)\s*\{\s*\$db->begin\(\);/', $source );
    }
}
