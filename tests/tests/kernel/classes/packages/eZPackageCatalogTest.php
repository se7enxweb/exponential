<?php
/**
 * Tests of eZPackageCatalog, eZPackageRemovalPlan and the list view's addresses: repositories made under var/tmp
 * with a site package that requires two others, a package in the setup wizard's repository, a plain local one, a
 * broken package.xml, a package.xml naming another package and a directory without a package.xml. Checks the
 * cards (version, type, maintainers, dependencies, install state, files, size, last change), the installer
 * sources, the repository counts, the search, filters, sort and query, what a package carries, the removal plan
 * (refused values, installer sources, required packages, the confirmation check) and the list's addresses and
 * pager. Read without the database (install state "not installed"); everything is removed in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/eZPackageTestFixtures.php';

class eZPackageCatalogTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $repositories;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->dir = eZPackageTestFixtures::tempDir( 'packagecatalog' );
        $f = 'eZPackageTestFixtures';
        $f::writeFiles( $this->dir, array(
            'vend/k1_site/package.xml' => $f::definition( array( 'name' => 'k1_site', 'type' => 'site', 'install_type' => 'import',
                                                                  'requires' => array( 'k1_classes', 'k1_content' ), 'settings' => array( 'ini-site.php' ),
                                                                  'summary' => 'The site' ) ),
            'vend/k1_classes/package.xml' => $f::definition( array( 'name' => 'k1_classes', 'classes' => array( 'article', 'folder' ), 'version' => '2.0' ) ),
            'vend/k1_classes/ezcontentclass/class-article.xml' => '<content-class><identifier>article</identifier></content-class>',
            'vend/k1_classes/ezcontentclass/class-folder.xml' => '<content-class><identifier>folder</identifier></content-class>',
            'vend/k1_classes/.cache/package.php' => '<?php // the kernel cache, not counted',
            'vend/k1_leftover/files/x.txt' => 'a directory without a definition',
            'other/k1_content/package.xml' => $f::definition( array( 'name' => 'k1_content', 'type' => 'contentobject', 'objects' => 1,
                                                                     'requires' => array( 'k1_missing' ), 'maintainer' => 'Someone Else' ) ),
            'other/k1_content/ezcontentobject/contentobjects.xml' => '<content-object><object-list><object name="One"/></object-list></content-object>',
            'other/k1_content/images/a.png' => str_repeat( 'p', 300 ),
            'other/k1_broken/package.xml' => '<package><name>',
            'other/k1_named/package.xml' => $f::definition( array( 'name' => 'k1_other_name' ) ),
            'local/k1_local/package.xml' => $f::definition( array( 'name' => 'k1_local', 'type' => 'contentclass', 'extensions' => array( 'k1ext' ),
                                                                   'summary' => 'Local quotes' ) ),
        ) );
        touch( $this->dir . '/vend/k1_classes/ezcontentclass/class-folder.xml', 1800000000 );
        $this->repositories = array(
            array( 'id' => 'local', 'name' => 'Local', 'type' => 'local', 'path' => $this->dir . '/local' ),
            array( 'id' => 'vend', 'name' => 'vend', 'type' => 'global', 'path' => $this->dir . '/vend' ),
            array( 'id' => 'other', 'name' => 'other', 'type' => 'global', 'path' => $this->dir . '/other' ),
        );
    }

    protected function tearDown(): void
    {
        if ( $this->dir && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
    }

    private function scan()
    {
        return @eZPackageCatalog::scan( $this->repositories, false, 'vend' );
    }

    public function testScanFindsPackagesProblemsAndLeftovers()
    {
        $scan = $this->scan();
        $this->assertSame( array( 'k1_classes@vend', 'k1_content@other', 'k1_local@local', 'k1_site@vend' ),
                           array_values( array_map( null, array_keys( eZPackageCatalog::sort( $scan['cards'], 'name', 'asc' ) ) ) ) );
        $problems = array();
        foreach ( $scan['problems'] as $problem )
            $problems[] = $problem['repository'] . '/' . $problem['directory'] . ':' . $problem['reason'];
        sort( $problems );
        $this->assertSame( array( 'other/k1_broken:definition', 'other/k1_named:mismatch' ), $problems );
        $this->assertSame( 1, $scan['repositories']['vend']['counts']['orphans'] );
        $this->assertSame( 2, $scan['repositories']['other']['counts']['problems'] );
        $this->assertTrue( $scan['repositories']['vend']['is_vendor'] );
        $this->assertFalse( $scan['repositories']['local']['is_vendor'] );
    }

    public function testCards()
    {
        $cards = $this->scan()['cards'];
        $classes = $cards['k1_classes@vend'];
        $this->assertSame( '2.0-2', $classes['version'] );
        $this->assertSame( 'contentclass', $classes['type'] );
        $this->assertSame( 'not_installed', $classes['state'] );
        $this->assertSame( 2, $classes['install_items'] );
        $this->assertSame( 3, $classes['files'] );   // package.xml and two classes; .cache left out
        $this->assertSame( 1800000000, $classes['changed'] );
        $this->assertSame( array( array( 'name' => 'Test Maintainer', 'role' => 'lead' ) ), $classes['maintainers'] );
        $this->assertSame( array( 'k1_site' ), $classes['required_by'] );
        $this->assertSame( 'import', $cards['k1_site@vend']['state'] );
        $this->assertSame( array( true, true ), array_column( $cards['k1_site@vend']['requires'], 'present' ) );
        $this->assertSame( array( false ), array_column( $cards['k1_content@other']['requires'], 'present' ) );
        $this->assertSame( 1700000000, $cards['k1_local@local']['packaging_timestamp'] );
        $this->assertGreaterThan( 300, $cards['k1_content@other']['bytes'] );
    }

    public function testInstallerSources()
    {
        $cards = $this->scan()['cards'];
        $this->assertSame( array( 'vendor_repository', 'site_package' ), $cards['k1_site@vend']['installer_reasons'] );
        $this->assertSame( array( 'vendor_repository', 'required_by_source' ), $cards['k1_classes@vend']['installer_reasons'] );
        // in another repository, but the site package requires it
        $this->assertSame( array( 'required_by_source' ), $cards['k1_content@other']['installer_reasons'] );
        $this->assertFalse( $cards['k1_local@local']['installer_source'] );
        $scan = $this->scan();
        $totals = eZPackageCatalog::totals( $scan['repositories'] );
        $this->assertSame( 4, $totals['packages'] );
        $this->assertSame( 3, $totals['installer'] );
        $this->assertSame( 1, $totals['import'] );
        $this->assertSame( 3, $totals['not_installed'] );
        $this->assertSame( 1, $totals['orphans'] );
        $this->assertSame( 2, $totals['problems'] );
    }

    public function testQueryFilterAndSort()
    {
        $cards = $this->scan()['cards'];
        $types = eZPackageCatalog::types( $cards );
        $this->assertSame( array( 'contentclass', 'contentobject', 'site' ), $types );
        $query = eZPackageCatalog::normaliseQuery( array( 'search' => "  quotes\x01 ", 'type' => 'nonsense', 'state' => 'x', 'sort' => 'evil', 'dir' => 'up' ), $types );
        $this->assertSame( array( 'search' => 'quotes', 'type' => '', 'state' => '', 'sort' => 'name', 'dir' => 'asc' ), $query );
        $this->assertSame( 'desc', eZPackageCatalog::normaliseQuery( array( 'sort' => 'size' ) )['dir'] );

        $this->assertSame( array( 'k1_local@local' ), array_keys( eZPackageCatalog::filter( $cards, '', $query ) ) );
        $this->assertCount( 2, eZPackageCatalog::filter( $cards, 'vend', eZPackageCatalog::normaliseQuery( array() ) ) );
        $this->assertSame( array( 'k1_content@other' ), array_keys( eZPackageCatalog::filter( $cards, '', eZPackageCatalog::normaliseQuery( array( 'search' => 'someone else' ) ) ) ) );
        $this->assertSame( array( 'k1_site@vend' ), array_keys( eZPackageCatalog::filter( $cards, '', eZPackageCatalog::normaliseQuery( array( 'state' => 'import' ) ) ) ) );
        $this->assertCount( 3, eZPackageCatalog::filter( $cards, '', eZPackageCatalog::normaliseQuery( array( 'state' => 'installer' ) ) ) );
        $this->assertSame( array( 'k1_content@other' ), array_keys( eZPackageCatalog::filter( $cards, '', eZPackageCatalog::normaliseQuery( array( 'type' => 'contentobject' ), $types ) ) ) );
        $this->assertSame( array(), eZPackageCatalog::filter( $cards, '', eZPackageCatalog::normaliseQuery( array( 'search' => 'quotes article' ) ) ) );

        $bySize = array_keys( eZPackageCatalog::sort( $cards, 'size', 'desc' ) );
        $this->assertSame( 'k1_content@other', $bySize[0] );
        $byChanged = array_keys( eZPackageCatalog::sort( $cards, 'changed', 'desc' ) );
        $this->assertSame( 'k1_classes@vend', $byChanged[0] );
        $byVersion = array_keys( eZPackageCatalog::sort( $cards, 'version', 'desc' ) );
        $this->assertSame( 'k1_classes@vend', $byVersion[0] );
        $this->assertSame( 'k1_site@vend', array_keys( eZPackageCatalog::sort( $cards, 'name', 'desc' ) )[0] );
    }

    public function testContents()
    {
        $classes = eZPackage::fetch( 'k1_classes', $this->dir . '/vend', false, false );
        $contents = eZPackageCatalog::contents( $classes );
        $this->assertSame( array( 'article', 'folder' ), $contents['classes'] );
        $this->assertSame( 0, $contents['object_items'] );
        $this->assertSame( 2, $contents['kinds']['class']['files'] );
        $this->assertSame( 3, $contents['files'] );

        $content = eZPackage::fetch( 'k1_content', $this->dir . '/other', false, false );
        $contents = eZPackageCatalog::contents( $content );
        $this->assertSame( 1, $contents['object_items'] );
        $this->assertSame( 1, $contents['object_files'] );
        $this->assertSame( 300, $contents['kinds']['image']['bytes'] );

        $site = eZPackage::fetch( 'k1_site', $this->dir . '/vend', false, false );
        $this->assertSame( array( 'ini-site.php' ), eZPackageCatalog::contents( $site )['settings'] );
        $local = eZPackage::fetch( 'k1_local', $this->dir . '/local', false, false );
        $this->assertSame( array( 'k1ext' ), eZPackageCatalog::contents( $local )['extensions'] );
    }

    public function testDirectoryStatsDoesNotFollowLinks()
    {
        $link = $this->dir . '/vend/k1_classes/linked';
        symlink( realpath( $this->dir . '/other' ), $link );
        try
        {
            $stats = eZPackageCatalog::directoryStats( $this->dir . '/vend/k1_classes' );
            $this->assertSame( 3, $stats['files'] );
            $this->assertSame( 1, $stats['links'] );
            // eZDir::recursiveDelete() would empty the link's target: such a package is not offered for removal
            $cards = $this->scan()['cards'];
            $this->assertSame( 1, $cards['k1_classes@vend']['links'] );
            $plan = eZPackageRemovalPlan::build( array( 'k1_classes@vend' ), '', $cards );
            $this->assertSame( array(), $plan['items'] );
            $this->assertSame( 'links', $plan['refused'][0]['reason'] );
        }
        finally
        {
            unlink( $link );
        }
        $this->assertSame( array( 'files' => 0, 'bytes' => 0, 'changed' => false, 'links' => 0 ), eZPackageCatalog::directoryStats( $this->dir . '/missing' ) );
    }

    public function testRemovalPlan()
    {
        $cards = $this->scan()['cards'];
        $this->assertSame( array( 'k1_local', '' ), eZPackageRemovalPlan::parseValue( 'k1_local' ) );
        $this->assertSame( array( 'k1_local', 'local' ), eZPackageRemovalPlan::parseValue( 'k1_local@local' ) );
        foreach ( array( '../7x/x', 'a@b@c', 'a@../b', '', null, array() ) as $bad )
            $this->assertFalse( eZPackageRemovalPlan::parseValue( $bad ), var_export( $bad, true ) );

        $plan = eZPackageRemovalPlan::build( array( 'k1_local', 'k1_classes@vend', '../../etc', 'k1_nothere', 'k1_content@vend' ), '', $cards,
                                             null, 'var/storage/packages' );
        $this->assertSame( array( 'k1_local@local', 'k1_classes@vend' ), array_keys( $plan['items'] ) );
        $this->assertSame( 'var/storage/packages/vend/k1_classes', $plan['items']['k1_classes@vend']['path'] );
        $this->assertSame( array( 'unsafe', 'not_found', 'not_found' ), array_column( $plan['refused'], 'reason' ) );
        $this->assertSame( 1, $plan['installer_sources'] );
        $this->assertSame( 1, $plan['required_elsewhere'] );   // k1_site stays and requires k1_classes
        $this->assertSame( array( 'k1_classes@vend', 'k1_local@local' ), eZPackageRemovalPlan::values( $plan ) );

        // the site package goes with it: nothing that stays requires k1_classes
        $both = eZPackageRemovalPlan::build( array( 'k1_classes@vend', 'k1_site@vend' ), '', $cards );
        $this->assertSame( 0, $both['required_elsewhere'] );

        // a bare name is looked for in the list's repository
        $this->assertSame( 'not_found', eZPackageRemovalPlan::build( array( 'k1_local' ), 'vend', $cards )['refused'][0]['reason'] );

        // the remove policy, per package
        $policy = eZPackageRemovalPlan::build( array( 'k1_local', 'k1_site@vend' ), '', $cards, function ( $card ) { return $card['type'] !== 'site'; } );
        $this->assertSame( array( 'k1_local@local' ), array_keys( $policy['items'] ) );
        $this->assertSame( 'policy', $policy['refused'][0]['reason'] );

        // the confirmation: only what was offered, installer sources with their own tick
        $offered = eZPackageRemovalPlan::values( $plan );
        $this->assertNull( eZPackageRemovalPlan::confirmationProblem( $offered, $plan, true ) );
        $this->assertSame( 'installer_source', eZPackageRemovalPlan::confirmationProblem( $offered, $plan, false ) );
        $this->assertSame( 'not_offered', eZPackageRemovalPlan::confirmationProblem( array( 'k1_local@local' ), $plan, true ) );
        $localOnly = eZPackageRemovalPlan::build( array( 'k1_local' ), '', $cards );
        $this->assertNull( eZPackageRemovalPlan::confirmationProblem( $offered, $localOnly, false ) );
    }

    public function testListAddressesAndPager()
    {
        $list = 'Exponential\\View\\Kernel\\Package\\ListView';
        $this->assertSame( '/package/list', $list::listURI( '' ) );
        $this->assertSame( '/package/list/7x', $list::listURI( '7x' ) );
        $this->assertSame( '/package/list/7x/(search)/demo%2520content%252F/(type)/site/(sort)/size/offset/25',
                           $list::listURI( '7x', array( 'search' => 'demo content/', 'type' => 'site', 'sort' => 'size', 'dir' => 'desc' ), 25 ) );
        $this->assertSame( '/package/list/(sort)/size/(dir)/asc', $list::listURI( '', array( 'sort' => 'size', 'dir' => 'asc' ) ) );
        $this->assertSame( '/package/list', $list::listURI( '', array( 'type' => '../x', 'state' => 'a/b', 'sort' => 'evil' ) ) );

        $pager = $list::pager( '', array(), 25, 10, 47 );
        $this->assertSame( 5, $pager['pages'] );
        $this->assertSame( 3, $pager['page'] );
        $this->assertSame( '/package/list', $pager['first'] );
        $this->assertSame( '/package/list/offset/10', $pager['prev'] );
        $this->assertSame( '/package/list/offset/30', $pager['next'] );
        $this->assertSame( '/package/list/offset/40', $pager['last'] );
        $this->assertSame( array( 1, 2, 3, 4, 5 ), array_column( $pager['items'], 'number' ) );
        $this->assertSame( array( 26, 35 ), array( $pager['from'], $pager['to'] ) );
        $single = $list::pager( '', array(), 0, 10, 3 );
        $this->assertFalse( $single['next'] );
        $this->assertFalse( $single['prev'] );
    }
}
