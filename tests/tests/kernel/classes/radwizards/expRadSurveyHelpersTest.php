<?php
/**
 * The parts of the RAD survey, catalogue and health check of Setup > RAD that do not need the survey of a whole
 * installation: how an ini file is read, where a class is declared, the order settings and repositories are listed
 * in, the runnable events and source check, the groups of the total; the catalogue of extension points (groups,
 * mechanisms, coverage, filters); and the health check's ordering, counting, labels and advice for a class php
 * refuses to load.
 *
 * No database. Files to read are written under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expRadWizardTestHelper.php';

class expRadSurveyHelpersTest extends PHPUnit\Framework\TestCase
{
    private $scratch;

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        $this->scratch = expRadWizardTestHelper::scratch( 'survey' );
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::removeTree( $this->scratch );
    }

    // ---------------------------------------------------------------- survey

    public function testIniFilesAreReadTheWayTheKernelReadsThem()
    {
        $file = $this->scratch . '/k1e.ini.append.php';
        file_put_contents( $file, "<?php /* #?ini charset=\"utf-8\"?\n\n# a comment\n; another\nOutside=ignored\n[Block]\nName=Value=with=equals\nList[]\nList[]=one\nList[]= two \nHash[key]=x\nHash[key]=y\nbad name=ignored\n9Bad=ignored\nNoValue\n[Other]\nA=\n*/ ?>\n" );
        $this->assertSame( array(
            'Block' => array( 'Name' => array( 'Value=with=equals' ), 'List[]' => array( 'one', 'two' ), 'Hash[key]' => array( 'x', 'y' ) ),
            'Other' => array( 'A' => array( '' ) ),
        ), expRADSurvey::parseIni( $file ) );
        $this->assertSame( array(), expRADSurvey::parseIni( $this->scratch . '/missing.ini' ) );
        $this->assertSame( array(), expRADSurvey::parseIni( $this->scratch ) );
    }

    public function testWhereAClassIsDeclared()
    {
        expRADSurvey::reset();
        $this->assertSame( 'kernel/setup/expradsurvey.php', expRADSurvey::fileOf( 'expRADSurvey' ) );
        $this->assertSame( 'kernel/setup/expradsurvey.php', expRADSurvey::fileOf( 'EXPRADSURVEY' ) );
        $this->assertSame( '', expRADSurvey::fileOf( 'k1eNoSuchClassAnywhere' ) );
        $this->assertSame( '(declared at runtime)', expRADSurvey::fileOf( __CLASS__ ) );
        $this->assertSame( '(declared at runtime)', expRADSurvey::sourceLabel( '(declared at runtime)' ) );
        $this->assertSame( 'kernel/x.php', expRADSurvey::sourceLabel( 'kernel/x.php' ) );
        expRADSurvey::reset();
    }

    public function testOrderOfSettingsAndRepositories()
    {
        $settings = array(
            array( 'ini' => 'site.ini', 'section' => 'B', 'variable' => 'x' ),
            array( 'ini' => 'content.ini', 'section' => 'Z', 'variable' => 'y' ),
            array( 'ini' => 'site.ini', 'section' => 'A', 'variable' => 'z' ),
            array( 'ini' => 'site.ini', 'section' => 'A', 'variable' => 'a' ),
        );
        usort( $settings, array( 'expRADSurvey', 'compareSettings' ) );
        $this->assertSame( array( 'content.ini/Z/y', 'site.ini/A/a', 'site.ini/A/z', 'site.ini/B/x' ),
                           array_map( function ( $s ) { return $s['ini'] . '/' . $s['section'] . '/' . $s['variable']; }, $settings ) );
        $this->assertSame( 0, expRADSurvey::compareRepositories( array( 'ini' => 'a', 'section' => 'b' ), array( 'ini' => 'a', 'section' => 'b' ) ) );
        $this->assertLessThan( 0, expRADSurvey::compareRepositories( array( 'ini' => 'a', 'section' => 'b' ), array( 'ini' => 'b', 'section' => 'a' ) ) );
    }

    public function testSettingAndContractShapes()
    {
        $this->assertTrue( expRADSurvey::isLive( array( 'exists' => true ) ) );
        $this->assertTrue( expRADSurvey::isBroken( array( 'shape' => 'unknown' ) ) );
        $this->assertFalse( expRADSurvey::isBroken( array( 'shape' => 'alias' ) ) );
        $this->assertTrue( expRADSurvey::isAlias( array( 'shape' => 'alias' ) ) );
        $this->assertTrue( expRADSurvey::isImplemented( array( 'implementations' => array( 'x' ) ) ) );
        $this->assertFalse( expRADSurvey::isImplemented( array( 'implementations' => array() ) ) );
    }

    public function testSourceTreeDoesNotEnterTestsOrDependencyTrees()
    {
        $method = new ReflectionMethod( 'expRADSurvey', 'sourceTree' );
        if ( PHP_VERSION_ID < 80100 )
            $method->setAccessible( true );
        $tree = $method->invoke( null, true );
        $this->assertNotEmpty( $tree['files'] );
        foreach ( array_merge( $tree['files'], $tree['dirs'] ) as $path )
        {
            foreach ( array( 'tests', 'vendor', 'node_modules', '.git' ) as $skipped )
                $this->assertStringNotContainsString( '/' . $skipped . '/', $path . '/', "$path is inside a $skipped directory" );
        }
    }

    public function testRunnables()
    {
        $events = expRADSurvey::runnableEvents();
        $this->assertCount( 7, $events );
        $this->assertSame( 'notify', $events['runnable/cronjob/before']['kind'] );
        $this->assertSame( 'filter', $events['cronjob/part/run']['kind'] );
        $this->assertSame( 'filter', $events['runnable/view/after']['kind'] );

        $list = array( array( 'kind' => 'command', 'owner' => 'kernel' ), array( 'kind' => 'view', 'owner' => 'kernel' ), array( 'kind' => 'command', 'owner' => 'k1e' ) );
        $this->assertCount( 2, expRADSurvey::runnablesOf( $list, 'kind', 'command' ) );
        $this->assertSame( array( array( 'kind' => 'command', 'owner' => 'k1e' ) ), expRADSurvey::runnablesOf( $list, 'owner', 'k1e' ) );

        file_put_contents( $this->scratch . '/a.php', "<?php\nclass A extends \\Exponential\\Runnable\\CronjobPart {}" );
        file_put_contents( $this->scratch . '/b.php', "<?php\nclass B extends Exponential\\Runnable\\Command {}" );
        file_put_contents( $this->scratch . '/c.php', "<?php\nclass C extends Exponential\\Runnable\\Commander {}" );
        $this->assertTrue( expRADSurvey::isRunnableSource( $this->scratch . '/a.php' ) );
        $this->assertTrue( expRADSurvey::isRunnableSource( $this->scratch . '/b.php' ) );
        $this->assertFalse( expRADSurvey::isRunnableSource( $this->scratch . '/c.php' ) );
        $this->assertFalse( @expRADSurvey::isRunnableSource( $this->scratch . '/missing.php' ) );
        $this->assertIsArray( expRADSurvey::runnableImplementations() );
    }

    public function testGroupsOfTheTotal()
    {
        $groups = expRADSurvey::totalGroups();
        $this->assertSame( array( 'settings', 'repositories', 'contracts', 'views', 'callables', 'events', 'overrides', 'replaced', 'runnables', 'registries_added' ), array_keys( $groups ) );
        $counts = array_fill_keys( array_keys( $groups ), 1 );
        $counts['registry_k1e'] = 5;
        $counts['registry_k1e_added'] = 2;
        $rows = expRADSurvey::groupCounts( array( 'counts' => $counts, 'registries' => array( 'k1e' => array( 'title' => 'K1e things' ) ) ) );
        $this->assertCount( 11, $rows );
        $last = end( $rows );
        $this->assertSame( array( 'key' => 'registry_k1e', 'label' => 'K1e things', 'count' => 5, 'section' => 'registries', 'in_total' => false, 'counted' => 3 ), $last );
        $this->assertTrue( $rows[0]['in_total'] );
        $this->assertNotEmpty( expRADSurvey::registryDescriptors() );
    }

    // ---------------------------------------------------------------- catalogue

    public function testCatalogueOfExtensionPoints()
    {
        $groups = expRADCatalogue::groups();
        $mechanisms = expRADCatalogue::mechanisms();
        $points = expRADCatalogue::points();
        $this->assertNotEmpty( $points );
        $byGroup = 0;
        foreach ( array_keys( $groups ) as $group )
            $byGroup += count( expRADCatalogue::pointsOf( $group ) );
        $this->assertSame( count( $points ), $byGroup, 'every point belongs to a known group' );
        foreach ( $points as $key => $point )
        {
            foreach ( array( 'group', 'title', 'what', 'mechanism', 'tool' ) as $field )
                $this->assertArrayHasKey( $field, $point, "$key $field" );
            $this->assertArrayHasKey( $point['mechanism'], $mechanisms, "$key mechanism" );
        }
        $this->assertSame( array(), expRADCatalogue::pointsOf( 'k1e-no-such-group' ) );

        $coverage = expRADCatalogue::coverage();
        $this->assertSame( count( $points ), $coverage['total'] );
        $counts = expRADCatalogue::filterCounts();
        $this->assertSame( array( 'all' => $coverage['total'], 'tools' => $coverage['covered'], 'docs' => $coverage['total'] - $coverage['covered'] ), $counts );
        foreach ( array( 'tools', 'docs' ) as $filter )
            $this->assertSame( $counts[$filter], count( array_filter( $points, function ( $p ) use ( $filter ) { return expRADCatalogue::matches( $p, $filter ); } ) ) );
        $this->assertTrue( expRADCatalogue::matches( array( 'tool' => false ), 'all' ) );
    }

    public static function filterProvider()
    {
        return array( array( 'tools', 'tools' ), array( 'docs', 'docs' ), array( 'all', 'all' ), array( 'tool', 'all' ), array( array( 'docs' ), 'all' ), array( null, 'all' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('filterProvider')]
    public function testCatalogueFilterFromTheAddress( $asked, $expected )
    {
        $this->assertSame( $expected, expRADCatalogue::filter( $asked ) );
        $this->assertArrayHasKey( $expected, expRADCatalogue::filters() );
    }

    // ---------------------------------------------------------------- health

    public function testFindingsAreOrderedWorstFirstAndCounted()
    {
        $findings = array(
            array( 'severity' => expRADHealth::NOTE, 'check' => 'B', 'what' => 'x' ),
            array( 'severity' => expRADHealth::BROKEN, 'check' => 'Z', 'what' => 'y' ),
            array( 'severity' => expRADHealth::ODD, 'check' => 'A', 'what' => 'z' ),
            array( 'severity' => expRADHealth::BROKEN, 'check' => 'A', 'what' => 'w' ),
        );
        usort( $findings, array( 'expRADHealth', 'compare' ) );
        $this->assertSame( array( 'A/w', 'Z/y', 'A/z', 'B/x' ), array_map( function ( $f ) { return $f['check'] . '/' . $f['what']; }, $findings ) );
        $this->assertSame( array( expRADHealth::BROKEN => 2, expRADHealth::ODD => 1, expRADHealth::NOTE => 1, 'total' => 4 ), expRADHealth::counts( $findings ) );
        $this->assertSame( array( expRADHealth::BROKEN => 0, expRADHealth::ODD => 0, expRADHealth::NOTE => 0, 'total' => 0 ), expRADHealth::counts( array() ) );
    }

    public function testLabelsAndKeys()
    {
        $this->assertSame( 'setting-names-no-class', expRADHealth::keyOf( ' Setting names no class ' ) );
        $this->assertSame( 'kernel-override-of-nothing', expRADHealth::keyOf( 'Kernel override of nothing' ) );
        $this->assertSame( 'View with no script', expRADHealth::checkLabel( 'View with no script' ) );
        $this->assertSame( 'k1e unknown check', expRADHealth::checkLabel( 'k1e unknown check' ) );
        $this->assertSame( 'php refused it.', expRADHealth::loaderKindLabel( 'php refused it.' ) );
        $this->assertSame( 'k1e kind', expRADHealth::loaderKindLabel( 'k1e kind' ) );
    }

    public static function fixProvider()
    {
        return array(
            'incompatible' => array( 'Declaration of A::x() must be compatible with B::x($y)', 4, 'signature' ),
            'missing parent' => array( 'Class "k1eBase" not found', 4, 'k1eBase' ),
            'static' => array( 'Non-static method A::b() cannot be called statically', 4, 'static' ),
            'twice' => array( 'Cannot redeclare class A', 3, 'same name' ),
            'anything else' => array( 'Something else', 3, 'checkclasses.php' ),
            'no message' => array( null, 3, 'php refused the class' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('fixProvider')]
    public function testAdviceForAClassPhpRefuses( $message, $steps, $inText )
    {
        $fix = expRADHealth::howToFix( $message === null ? array() : array( 'message' => $message ) );
        $this->assertCount( $steps, $fix );
        $this->assertStringContainsString( $inText, implode( ' ', $fix ) );
    }
}
