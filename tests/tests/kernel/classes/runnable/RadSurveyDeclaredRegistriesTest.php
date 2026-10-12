<?php
/**
 * Registries declared in rad.ini (settings/rad.ini, extension/<name>/settings/rad.ini.append.php) in the extension
 * point survey (setup/rad, setup/radsurvey/(show)/registries).
 *
 *  RD-01 — A declared registry is read from its ini blocks: a good entry is ok, an entry whose class does not
 *          implement what the registry asks for and a value outside one-of: are broken, each with its reason
 *  RD-02 — section: and listener checks: an entry naming a block that is there is ok, one naming a missing block
 *          is broken; a listener whose method exists is ok, one whose method is missing is broken; InTotal=disabled
 *          is kept on the registry
 *  RD-03 — Declaration problems are reported, not dropped: a key without its block, a block without a Title, a
 *          List= whose constant does not exist; none of them is an entry
 *  RD-04 — The installation's own declarations: the kernel's event listeners and those of expai, expqueue and
 *          expmcp (when active) are read, have entries and none of their entries is broken; their entries reach
 *          the total once, and an InTotal=disabled registry adds nothing to it
 *  RD-05 — The event sweep finds events announced on a variable or under a variable name (package/install/drafts,
 *          content/pre_rendering)
 *
 * Reads the live installation's classes and settings for RD-04 and RD-05; RD-01 to RD-03 read the fixture files
 * in fixtures/radregistry. No test database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group runnable
 */

require_once __DIR__ . '/../../../../../kernel/setup/expradsurvey.php';

class RadSurveyDeclaredRegistriesTest extends PHPUnit\Framework\TestCase
{
    private static $fixture;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        if ( !class_exists( 'eZINI' ) || !class_exists( 'expIniActionGet' ) )
            $this->markTestSkipped( 'the survey needs the kernel classes' );
        if ( self::$fixture === null )
        {
            $dir = 'tests/tests/kernel/classes/runnable/fixtures/radregistry';
            $files = array( array( 'path' => $dir . '/rad.ini', 'ini' => 'rad.ini', 'origin' => 'kernel' ),
                            array( 'path' => $dir . '/radfixture.ini', 'ini' => 'radfixture.ini', 'origin' => 'kernel' ) );
            $all = expRADSurvey::registries( $files, array() );
            self::$fixture = array_filter( $all, function ( $key ) { return strpos( $key, 'radfixture_' ) === 0; }, ARRAY_FILTER_USE_KEY );
        }
    }

    private static function byValue( array $registry )
    {
        $entries = array();
        foreach ( $registry['entries'] as $entry )
            $entries[$entry['variable'] . '=' . $entry['value']] = $entry;
        return $entries;
    }

    /** RD-01 */
    public function testEntriesAreChecked()
    {
        $this->assertArrayHasKey( 'radfixture_actions', self::$fixture );
        $actions = self::$fixture['radfixture_actions'];
        $this->assertTrue( $actions['declared'] );
        $this->assertSame( 'Fixture actions', $actions['title'] );
        $entries = self::byValue( $actions );
        $this->assertCount( 4, $entries );
        $this->assertTrue( $entries['Class=expIniActionGet']['ok'] );
        $this->assertSame( 'get', $entries['Class=expIniActionGet']['name'] );
        $this->assertTrue( $entries['Mode=fast']['ok'] );
        $this->assertSame( 'the class does not extend or implement expIniAction', $entries['Class=expSubitemsDateColumn']['why'] );
        $this->assertSame( 'the value is not one of the words allowed', $entries['Mode=sometimes']['why'] );
        $this->assertCount( 2, $actions['broken'] );
        $this->assertSame( array(), $actions['problems'] );
        $this->assertTrue( $actions['in_total'] );
    }

    /** RD-02 */
    public function testSectionAndListenerChecks()
    {
        $listed = self::$fixture['radfixture_listed'];
        $entries = self::byValue( $listed );
        $this->assertSame( '', $entries['Actions[]=get']['why'] );
        $this->assertSame( 'the ini file has no such block', $entries['Actions[]=nosuchaction']['why'] );
        $this->assertSame( '', $entries['Listeners[]=request/input@expRADSurvey::reset']['why'] );
        $this->assertSame( 'the class has no such method', $entries['Listeners[]=request/input@expRADSurvey::noSuchMethod']['why'] );
        $this->assertFalse( $listed['in_total'] );
        $this->assertSame( 'not <event>@<callback>', expRADSurvey::registryProblem( 'request/input', 'listener' ) );
        $this->assertSame( '', expRADSurvey::registryProblem( 'anything', 'name' ) );
        $this->assertSame( '', expRADSurvey::registryProblem( 'settings', 'in:expRADSurvey::totalGroups' ), 'a key of the list' );
        $this->assertSame( 'the value is not in the list', expRADSurvey::registryProblem( 'nothing', 'in:expRADSurvey::totalGroups' ) );
        $this->assertSame( 'the list it is checked against cannot be read', expRADSurvey::registryProblem( 'x', 'in:expRADSurvey::NOT_A_NAME' ), 'a constant that is no list' );
    }

    /** RD-03 */
    public function testDeclarationProblems()
    {
        $why = function ( $key ) { return array_column( self::$fixture[$key]['problems'], 'why' ); };
        $this->assertSame( array( 'the class has no such constant or method' ), $why( 'radfixture_code' ) );
        $this->assertSame( array( 'the registry has no block of its own' ), $why( 'radfixture_undeclared' ) );
        $this->assertSame( array( 'the registry has no Title' ), $why( 'radfixture_notitle' ) );
        foreach ( array( 'radfixture_code', 'radfixture_undeclared' ) as $key )
        {
            $this->assertSame( array(), self::$fixture[$key]['entries'], $key );
            $this->assertCount( 1, self::$fixture[$key]['broken'], $key );
        }
        $this->assertCount( 2, self::$fixture['radfixture_notitle']['entries'], 'a block without a title still has its entries' );
    }

    /** RD-04 */
    public function testInstallationDeclarations()
    {
        ezpLiveInstallation::requireOrSkip();
        $survey = expRADSurvey::survey();
        $registries = $survey['registries'];
        $this->assertArrayHasKey( 'eventlisteners', $registries );
        $this->assertTrue( $registries['eventlisteners']['declared'] );
        $this->assertNotEmpty( $registries['eventlisteners']['entries'] );
        $expected = array( 'expai' => 'expai_providers', 'expqueue' => 'expqueue_job_types', 'expmcp' => 'expmcp_tools' );
        foreach ( $expected as $extension => $key )
            if ( expRADSurvey::isActiveOrigin( $extension ) )
            {
                $this->assertArrayHasKey( $key, $registries, $extension );
                $this->assertNotEmpty( $registries[$key]['entries'], $key );
            }
        foreach ( $registries as $key => $registry )
            if ( !empty( $registry['declared'] ) && $key !== 'auditsources' )
                $this->assertSame( array(), array_column( $registry['broken'], 'why' ), $key );

        $added = 0;
        foreach ( $registries as $key => $registry )
        {
            $notCounted = count( array_filter( $registry['entries'], function ( $e ) { return !$e['counted']; } ) );
            $this->assertSame( $registry['in_total'] ? $notCounted : 0, $survey['counts']['registry_' . $key . '_added'], $key );
            $added += $registry['in_total'] ? $notCounted : 0;
        }
        $this->assertSame( $added, $survey['counts']['registries_added'] );
        foreach ( $survey['settings'] as $setting )
            $this->assertNotSame( 'rad.ini', $setting['ini'], 'a declaration is no setting' );
    }

    /** RD-05 */
    public function testEventSweepFindsHeldEvents()
    {
        ezpLiveInstallation::requireOrSkip();
        $events = expRADSurvey::events();
        foreach ( array( 'package/install/drafts', 'content/pre_rendering', 'httpcache/store/headers', 'cluster/fileExists' ) as $event )
            $this->assertArrayHasKey( $event, $events, $event );
        $this->assertSame( 'filter', $events['httpcache/store/headers']['kind'] );
        $this->assertSame( 'notify', $events['package/install/drafts']['kind'] );
        $this->assertArrayNotHasKey( 'notifications/initialized', $events, 'a notify() of a class of its own is no event' );
    }
}
