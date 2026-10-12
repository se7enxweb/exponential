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
 *  RD-06 — A line "Registries[]" without a value in a later file empties the list, as eZINI reads it: the earlier
 *          files' registries and the lines above it in the same file are dropped, the ones after it kept (also for
 *          Sections[] in a block); a file that only adds keeps the others (fixtures/radregistry-reset)
 *  RD-07 — ezjscore and ezoe declare their registries in their own rad.ini.append.php: the survey's code names
 *          neither, the registries are declared by them, read only from active extensions, and nothing is broken
 *
 * Reads the live installation's classes and settings for RD-04 and RD-05; RD-01 to RD-03 read the fixture files
 * in fixtures/radregistry, RD-06 those in fixtures/radregistry-reset. No test database.
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

    /** RD-06 */
    public function testAResetLineEmptiesTheList()
    {
        $dir = 'tests/tests/kernel/classes/runnable/fixtures/radregistry-reset';
        $later = array( 'path' => $dir . '/later.ini.append.php', 'ini' => 'rad.ini', 'origin' => 'override' );
        $added = array( 'path' => $dir . '/added.ini.append.php', 'ini' => 'rad.ini', 'origin' => 'override' );
        $first = array( 'path' => $dir . '/rad.ini', 'ini' => 'rad.ini', 'origin' => 'kernel' );

        $this->assertSame( array( 'radreset_first', 'radreset_second', 'radreset_fourth' ),
                           array_keys( expRADSurvey::declaredRegistryDescriptors( array( $first, $added ) ) ), 'a list without a reset adds' );

        $descriptors = expRADSurvey::declaredRegistryDescriptors( array( $first, $later, $added ) );
        $this->assertSame( array( 'radreset_third', 'radreset_fourth' ), array_keys( $descriptors ),
                           'the reset drops the earlier file\'s registries and the line above it, and keeps what follows it' );
        $this->assertSame( array( 'Action_*' ), $descriptors['radreset_third']['sections'], 'a reset of Sections[] inside a block too' );
        $this->assertSame( array(), $descriptors['radreset_third']['problems'] );

        $this->assertSame( array( 'RegistrySettings' => array( 'Registries[]' => 1 ), 'Registry_radreset_third' => array( 'Sections[]' => 1 ) ),
                           expRADSurvey::iniResets( $dir . '/later.ini.append.php' ) );
        $this->assertSame( array( 'RegistrySettings' => array( 'Registries[]' => 0 ) ), expRADSurvey::iniResets( $dir . '/rad.ini' ) );
        $this->assertSame( array(), expRADSurvey::iniResets( $dir . '/added.ini.append.php' ) );
        $this->assertSame( array(), expRADSurvey::iniResets( $dir . '/no-such-file.ini' ) );
    }

    /** RD-07 */
    public function testEzjscoreAndEzoeDeclareTheirRegistries()
    {
        ezpLiveInstallation::requireOrSkip();
        $this->assertArrayNotHasKey( 'ezjscserver', expRADSurvey::registryDescriptors(), 'the survey no longer names ezjscore' );
        $this->assertArrayNotHasKey( 'ezoeengines', expRADSurvey::registryDescriptors(), 'nor ezoe' );
        $survey = expRADSurvey::survey();
        $registries = $survey['registries'];
        foreach ( array( 'ezjscserver' => 'ezjscore', 'ezoeengines' => 'ezoe' ) as $key => $extension )
        {
            if ( !expRADSurvey::isActiveOrigin( $extension ) )
            {
                $this->assertArrayNotHasKey( $key, $registries, $extension . ' is not active' );
                continue;
            }
            $this->assertTrue( $registries[$key]['declared'], $key );
            $this->assertSame( $extension, $registries[$key]['origin'], $key );
            $this->assertSame( array(), $registries[$key]['broken'], $key );
            $this->assertSame( array(), array_filter( $registries[$key]['entries'], function ( $e ) { return !expRADSurvey::isActiveOrigin( $e['origin'] ); } ),
                               $key . ': only the settings of active extensions' );
        }
        if ( isset( $registries['ezjscserver'] ) )
        {
            $this->assertContains( 'expSubitemsServerFunctions', array_column( $registries['ezjscserver']['entries'], 'value' ) );
            $this->assertArrayHasKey( 'expservices', $registries, 'the remote services are still read from the server functions' );
        }
        if ( isset( $registries['ezoeengines'] ) )
            $this->assertContains( 'tinymce3', array_column( $registries['ezoeengines']['entries'], 'name' ) );
    }
}
