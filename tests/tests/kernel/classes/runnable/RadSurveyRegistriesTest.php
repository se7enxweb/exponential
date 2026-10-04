<?php
/**
 * The registries in the extension point survey (setup/rad, setup/radsurvey), and the survey's statics under a
 * long-running worker.
 *
 *  RR-01 — Every registry of the installation's own settings is read entry by entry: the subitems columns (every
 *          Class, Handler and Template column, no built-in one), the nine content job types, the exp:ini actions
 *          and scope providers, the ezjscore server functions; none of them is broken
 *  RR-02 — No point is counted twice: an entry naming a class is a setting already, only the others (template and
 *          Handler=<class>::<method> columns that the settings group does not have) are added, and the total is the
 *          sum of totalGroups()
 *  RR-03 — registryProblem(): a missing class, a class that does not extend the contract, a missing method and a
 *          missing template are each told apart from a good entry
 *  RR-04 — A registry descriptor added by a subclass is read from the ini files with no other change (the hook the
 *          debug bar's registry uses)
 *  RR-05 — The cached survey belongs to one request: a new REQUEST_TIME_FLOAT (the next request of a persistent
 *          worker) makes survey() read everything again, the same one returns the cached array
 *  RR-06 — The catalogue explains the two new mechanisms; setup/rad (admin, admin4) shows the per-group counts and
 *          setup/radsurvey has the registries section
 *
 * Reads the live installation's files and settings; no test database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group runnable
 */

require_once __DIR__ . '/../../../../../kernel/setup/expradsurvey.php';

class ezpTestRadSurveyWithExtraRegistry extends expRADSurvey
{
    public static function registryDescriptors()
    {
        return array( 'radtest' => array( 'title' => 'Test registry', 'ini' => 'ini.ini', 'section' => 'IniCommandSettings',
                                          'variables' => array( 'ScopeProviders' => 'expIniScopeProvider' ) ) );
    }
}

class RadSurveyRegistriesTest extends PHPUnit\Framework\TestCase
{
    private static $survey;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        ezpLiveInstallation::requireOrSkip(); // the registries are read through the installed site's settings and classes
        if ( class_exists( 'eZINI' ) && class_exists( 'ezpI18n' ) )
            self::$survey = expRADSurvey::survey();
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        if ( self::$survey === null )
            $this->markTestSkipped( 'the survey needs the kernel classes' );
    }

    /** RR-01 */
    public function testRegistriesAreRead()
    {
        $registries = self::$survey['registries'];
        foreach ( array( 'subitemscolumns', 'contentjobtypes', 'inicommand', 'ezjscserver' ) as $key )
        {
            $this->assertArrayHasKey( $key, $registries );
            $this->assertSame( array(), $registries[$key]['broken'], $key );
        }

        $columns = $registries['subitemscolumns']['entries'];
        $byKind = array_count_values( array_column( $columns, 'what' ) );
        $this->assertGreaterThan( 100, $byKind['expSubitemsColumn'], 'the Class columns' );
        $this->assertGreaterThanOrEqual( 2, $byKind['callable'], 'the Handler columns' );
        $this->assertGreaterThanOrEqual( 2, $byKind['template'], 'the Template columns' );
        $names = array_column( $columns, 'name' );
        foreach ( array( 'thumbnail', 'name', 'visibility', 'priority' ) as $builtin )
            $this->assertNotContains( $builtin, $names, 'a built-in column is no point' );
        $this->assertContains( 'statusbadge', $names );
        $this->assertContains( 'daysonline', $names );

        $jobs = self::$survey['content_jobs'];
        $this->assertSame( array( 'remove', 'copy', 'move', 'hide', 'reveal', 'section', 'state', 'addlocation', 'removelocation' ),
                           array_column( $jobs['types'], 'name' ) );
        $this->assertSame( array(), $jobs['broken'] );
        $this->assertSame( 9, self::$survey['counts']['content_job_types'] );
        $this->assertSame( 0, self::$survey['counts']['content_job_types_registered'] );

        $ini = array_column( $registries['inicommand']['entries'], 'value' );
        $this->assertContains( 'expIniActionGet', $ini );
        $this->assertContains( 'expIniExtensionScopeProvider', $ini );
        $this->assertContains( 'expSubitemsServerFunctions', array_column( $registries['ezjscserver']['entries'], 'value' ) );
    }

    /** RR-02 */
    public function testNothingIsCountedTwice()
    {
        $c = self::$survey['counts'];
        $settings = array();
        foreach ( self::$survey['settings'] as $s )
            $settings[$s['ini'] . '|' . $s['section'] . '|' . $s['variable'] . '|' . $s['value']] = true;

        $added = 0;
        foreach ( self::$survey['registries'] as $registry )
            foreach ( $registry['entries'] as $entry )
            {
                $inSettings = isset( $settings[$registry['ini'] . '|' . $entry['section'] . '|' . $entry['variable'] . '|' . $entry['value']] );
                $this->assertSame( $inSettings, $entry['counted'], $entry['section'] . ' ' . $entry['variable'] );
                if ( !$inSettings )
                    $added++;
                // a class entry is always a setting already
                if ( $entry['what'] !== 'template' && $entry['what'] !== 'callable' && strpos( $entry['what'], 'debugbar-' ) !== 0 )
                    $this->assertTrue( $entry['counted'], $entry['value'] . ' names a class and is a setting' );
            }
        $this->assertSame( $added, $c['registries_added'] );
        foreach ( array( 'contentjobtypes', 'inicommand', 'ezjscserver' ) as $key )
            $this->assertSame( 0, $c['registry_' . $key . '_added'], $key . ' entries are all settings already' );
        $this->assertGreaterThanOrEqual( 2, $c['registry_subitemscolumns_added'], 'the template columns are added' );

        $sum = 0;
        foreach ( array_keys( expRADSurvey::totalGroups() ) as $group )
            $sum += $c[$group];
        $this->assertSame( $sum, $c['total'] );
        $this->assertSame( $c['settings'] + $c['repositories'] + $c['contracts'] + $c['views'] + $c['callables']
                           + $c['events'] + $c['overrides'] + $c['replaced'] + $c['runnables'] + $c['registries_added'], $c['total'] );

        $groups = expRADSurvey::groupCounts( self::$survey );
        $inTotal = array_sum( array_column( array_filter( $groups, function ( $g ) { return $g['in_total']; } ), 'count' ) );
        $this->assertSame( $c['total'], $inTotal );
        $this->assertContains( 'registry_subitemscolumns', array_column( $groups, 'key' ) );
    }

    /** RR-03 */
    public function testRegistryProblem()
    {
        $this->assertSame( '', expRADSurvey::registryProblem( 'expSubitemsDateColumn', 'expSubitemsColumn' ) );
        $this->assertSame( '', expRADSurvey::registryProblem( 'expContentJobCopySubtree', 'expContentJobType' ) );
        $this->assertSame( 'the class does not exist', expRADSurvey::registryProblem( 'ezpTestRadNoSuchClass', 'expSubitemsColumn' ) );
        $this->assertSame( 'the class does not extend or implement expContentJobType',
                           expRADSurvey::registryProblem( 'expSubitemsDateColumn', 'expContentJobType' ) );
        $this->assertSame( '', expRADSurvey::registryProblem( 'expSubitemsColumnHandlers::daysOnline', 'callable' ) );
        $this->assertSame( 'the class has no such method', expRADSurvey::registryProblem( 'expSubitemsColumnHandlers::noSuchMethod', 'callable' ) );
        $this->assertSame( '', expRADSurvey::registryProblem( 'design:subitems/columns/teaser.tpl', 'template' ) );
        $this->assertSame( 'no design has the template', expRADSurvey::registryProblem( 'design:subitems/columns/no_such_column.tpl', 'template' ) );
    }

    /** RR-04 */
    public function testAddedDescriptorIsRead()
    {
        $registries = ezpTestRadSurveyWithExtraRegistry::registries( null, self::$survey['settings'] );
        $this->assertSame( array( 'radtest' ), array_keys( $registries ) );
        $values = array_column( $registries['radtest']['entries'], 'value' );
        $this->assertContains( 'expIniCoreScopeProvider', $values );
        $this->assertContains( 'expIniExtensionScopeProvider', $values );
        foreach ( $registries['radtest']['entries'] as $entry )
            $this->assertTrue( $entry['counted'] && $entry['ok'] );
    }

    /** RR-05 */
    public function testSurveyIsPerRequest()
    {
        $saved = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : null;
        try
        {
            // Not a second full survey: that reaches kernel code that installs its own error handlers. The cache
            // is poked instead, and forRequest() is asked what survey() asks it first.
            $survey  = new ReflectionProperty( 'expRADSurvey', 'Survey' );
            $classes = new ReflectionProperty( 'expRADSurvey', 'Classes' );
            $events  = new ReflectionProperty( 'expRADSurvey', 'Events' );

            $_SERVER['REQUEST_TIME_FLOAT'] = 1000.25;
            expRADSurvey::forRequest();
            $stale = self::$survey;
            $stale['counts']['total'] = -1;
            $survey->setValue( null, $stale );
            $classes->setValue( null, array( 'ezptestradstale' => 'stale.php' ) );
            $events->setValue( null, array( 'stale/event' => array() ) );
            $this->assertSame( -1, expRADSurvey::survey()['counts']['total'], 'the same request reads the cache' );
            $this->assertSame( 'stale.php', expRADSurvey::fileOf( 'ezpTestRadStale' ) );

            // the next request of the same worker
            $_SERVER['REQUEST_TIME_FLOAT'] = 1000.5;
            expRADSurvey::forRequest();
            $this->assertNull( $survey->getValue(), 'the next request reads the survey again' );
            $this->assertNull( $classes->getValue() );
            $this->assertNull( $events->getValue() );
            $this->assertSame( '', expRADSurvey::fileOf( 'ezpTestRadStale' ), 'and the autoload maps' );
            $survey->setValue( null, self::$survey );
        }
        finally
        {
            if ( $saved === null )
                unset( $_SERVER['REQUEST_TIME_FLOAT'] );
            else
                $_SERVER['REQUEST_TIME_FLOAT'] = $saved;
        }
    }

    /** RR-06 */
    public function testCatalogueViewTemplates()
    {
        $catalogue = (string) file_get_contents( 'kernel/setup/expradcatalogue.php' );
        $this->assertStringContainsString( "'subitemscolumn' => array(", $catalogue );
        $this->assertStringContainsString( "'contentjobtype' => array(", $catalogue );
        $view = (string) file_get_contents( 'kernel/private/classes/views/setup/radsurvey.php' );
        $this->assertStringContainsString( "case 'registries':", $view );
        $this->assertStringContainsString( 'rad_survey_groups', (string) file_get_contents( 'kernel/private/classes/views/setup/rad.php' ) );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $this->assertStringContainsString( 'rad_survey_groups', (string) file_get_contents( "design/$design/templates/setup/rad.tpl" ) );
            $this->assertStringContainsString( 'survey_counts.registries', (string) file_get_contents( "design/$design/templates/setup/radsurvey.tpl" ) );
        }
    }
}
