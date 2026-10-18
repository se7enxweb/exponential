<?php
/**
 * A log key of digits only: var/log/404.log has the key "404", which PHP turns into the integer 404 as soon as it is
 * an array key. Every part of the log viewer must still treat it as the string it is (no database, no real log):
 *   - expLogFileLocator: listFiles() names it "404"; labelOf() and resolveKey() take "404" and 404 alike, also for a
 *     rotation (the archive viewer, /setup/logs/view/404/(rotation)/1)
 *   - expLogViewerConfig::hash(): LogLabel[404] is kept, not taken for a list position
 *   - expLogManager and expLogManagerFallback: logs() gives the key as "404" with its label (the link name of
 *     setup/logsmanage), its lines and managed; select(), plan() (also 'all') and apply() name it "404"
 *   - both managers give the same fields for a log, so the template sees no missing one
 *   - expLogPermalink: the permalink stays setup/logs/view/404
 *   - expLogHealth: summary() and settings() rows and their keys
 *   - expLogDigest: LogLevels[404] and GroupBy[404]
 *
 * Covers kernel/classes/logviewer/explogfilelocator.php, kernel/classes/logviewer/explogmanager.php,
 * kernel/classes/logviewer/explogmanagerfallback.php, kernel/classes/logviewer/exploghealth.php,
 * kernel/classes/logviewer/explogpermalink.php, kernel/classes/logviewer/explogdigest.php and
 * kernel/private/classes/views/setup/logshealth.php.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group logviewer
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';
foreach ( array( 'expLogManagerInterface' => 'explogmanagerinterface', 'expLogManagerFallback' => 'explogmanagerfallback',
                 'expLogManager' => 'explogmanager', 'expLogHealth' => 'exploghealth', 'expLogDigest' => 'explogdigest',
                 'expLogPermalink' => 'explogpermalink' ) as $class => $file )
    if ( !class_exists( $class ) && !interface_exists( $class ) )
        require_once dirname( __DIR__, 4 ) . '/kernel/classes/logviewer/' . $file . '.php';

class expLogNumericKeyTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $root;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lvnum' );
        file_put_contents( $this->root . '/var/log/404.log', "[ Oct 17 2026 01:54:58 ] [203.0.113.9] 404 GET /nothing siteaccess=site reason=module:nothing\n" );
        file_put_contents( $this->root . '/var/log/404.log.1', "older\n" );
        file_put_contents( $this->root . '/var/log/error.log', "e\n" );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
    }

    private function config( $label = true )
    {
        $values = array( 'LogViewerSettings' => array( 'Directories' => array( 'var/log' ) ) );
        if ( $label )
            $values['LogViewerSettings']['LogLabel'] = array( '404' => 'Not found requests (404)', 'error' => 'Errors' );
        $values['RotationSettings'] = array( 'Keep' => array( '404' => '5' ) );
        $values['DigestSettings'] = array( 'LogLevels' => array( '404' => 'error,warning,notice' ), 'GroupBy' => array( '404' => 'reason' ) );
        return expLogViewerTestFixtures::config( $this->root, $values );
    }

    private function locator( $label = true )
    {
        return new expLogFileLocator( $this->config( $label ), 'var/log' );
    }

    private function manager()
    {
        $config = $this->config();
        return new expLogManager( $config, new expLogFileLocator( $config, 'var/log' ),
                                  array( 'keep' => 2, 'max_size' => 1000, 'compress' => false, 'max_age_days' => 0,
                                         'overrides' => array( 'Keep' => array( '404' => '5' ) ) ) );
    }

    private function fallback()
    {
        $config = $this->config();
        $m = new expLogManagerFallback( $config, new expLogFileLocator( $config, 'var/log' ) );
        $m->siteFileSettings = array();
        $m->audit = false;
        $m->lockDir = $this->root . '/lock';
        return $m;
    }

    public function testConfigKeepsALabelOfDigits()
    {
        $labels = $this->config()->hash( 'LogViewerSettings', 'LogLabel' );
        $this->assertSame( 'Not found requests (404)', $labels['404'] );
        $this->assertSame( array( '404', 'error' ), array_map( 'strval', array_keys( $labels ) ) );
    }

    public function testLocatorListsAndResolvesTheKeyAsAString()
    {
        $l = $this->locator();
        $keys = array();
        foreach ( $l->listFiles() as $list )
            foreach ( $list as $info )
                if ( $info['name'] === '404.log' )
                    $keys[] = $info['key'];
        $this->assertSame( array( '404' ), $keys, 'the key is the string "404"' );
        $this->assertSame( 'Not found requests (404)', $l->labelOf( '404' ) );
        $this->assertSame( 'Not found requests (404)', $l->labelOf( 404 ), 'an integer key from an array finds the label' );
        foreach ( array( '404', 404 ) as $key )
        {
            $file = $l->resolveKey( $key );
            $this->assertNotNull( $file, var_export( $key, true ) );
            $this->assertSame( 'var/log/404.log', $file['id'] );
            $this->assertSame( '404', $file['key'] );
            $this->assertSame( 'Not found requests (404)', $file['label'] );
            $this->assertSame( 'var/log/404.log.1', $l->resolveKey( $key, 1 )['id'], 'the archive viewer' );
        }
        $this->assertNull( $l->resolveKey( 405 ) );
        $this->assertNull( $l->resolveKey( 4.04 ), 'only a string or an integer is a key' );
        $this->assertSame( '404.log', $this->locator( false )->resolveKey( '404' )['label'], 'no label: the file name' );
    }

    public function testManagerLogsGiveTheKeyAsAStringWithItsLabel()
    {
        foreach ( array( 'kernel' => $this->manager(), 'fallback' => $this->fallback() ) as $which => $manager )
        {
            $logs = $manager->logs();
            $this->assertArrayHasKey( '404', $logs, $which );
            $log = $logs['404'];
            $this->assertSame( '404', $log['key'], $which . ': the key field is a string' );
            $this->assertSame( 'Not found requests (404)', $log['label'], $which . ': the link name of setup/logsmanage' );
            $this->assertSame( 'Errors', $logs['error']['label'], $which );
            $this->assertTrue( $log['managed'], $which );
            $this->assertGreaterThanOrEqual( 1, $log['lines'], $which );
            $this->assertSame( array( 'var/log/404.log.1' ), array_column( $log['archives'], 'id' ), $which );
            $this->assertSame( 5, $log['limits']['keep'], $which . ': Keep[404]' );
            foreach ( $logs as $key => $one )
                $this->assertSame( (string)$key, $one['key'], $which . ': every key field matches its array key as a string' );
        }
    }

    public function testBothManagersGiveTheSameFields()
    {
        $kernel = $this->manager()->logs()['404'];
        $fallback = $this->fallback()->logs()['404'];
        $missing = array_diff( array_keys( $fallback ), array_keys( $kernel ) );
        $this->assertSame( array(), array_values( $missing ), 'expLogManager gives every field the template reads' );
        foreach ( array( 'label', 'lines', 'managed', 'key', 'id', 'size', 'archives', 'limits' ) as $field )
            $this->assertArrayHasKey( $field, $kernel, $field );
    }

    public function testSelectPlanAndApplyNameTheKeyAsAString()
    {
        $manager = $this->manager();
        $selection = $manager->select( array( '404' ) );
        $this->assertSame( array(), $selection['unknown'] );
        $this->assertSame( array( '404' ), array_map( 'strval', array_keys( $selection['found'] ) ) );

        $plan = $manager->plan( array( '404' ), 'rotate' );
        $this->assertSame( array( '404' ), array_column( $plan['steps'], 'key' ) );
        $all = $manager->plan( 'all', 'rotate', array( 'force' => true ) );
        $this->assertContains( '404', array_column( $all['steps'], 'key' ) );
        foreach ( $all['steps'] as $step )
            $this->assertIsString( $step['key'] );
        $result = $manager->apply( $plan );
        $this->assertTrue( $result['ok'], 'rotated' );
        $this->assertSame( 0, filesize( $this->root . '/var/log/404.log' ) );

        $fallback = $this->fallback();
        file_put_contents( $this->root . '/var/log/404.log', "again\n" );
        $plan = $fallback->plan( 'all', 'rotate', array( 'force' => true ) );
        $this->assertContains( '404', array_column( $plan['steps'], 'key' ), 'the fallback plans 404 for every log' );
        foreach ( $plan['steps'] as $step )
            $this->assertIsString( $step['key'] );
        $this->assertSame( array(), $plan['unknown'] );
        $result = $fallback->apply( $plan );
        $this->assertTrue( $result['ok'] );
        $this->assertSame( "again\n", file_get_contents( $this->root . '/var/log/404.log.1' ) );
    }

    public function testPermalinkStaysView404()
    {
        $this->assertSame( 'setup/logs/view/404', expLogPermalink::build( '404' ) );
        $this->assertSame( 'setup/logs/view/404', expLogPermalink::build( 404 ), 'an integer from an array key' );
        $this->assertStringStartsWith( 'setup/logs/view/404/', expLogPermalink::build( 404, array( 'rotation' => 1 ) ) );
    }

    public function testHealthRowsAndSettings()
    {
        $manager = $this->manager();
        $logs = $manager->logs();
        $summary = expLogHealth::summary( $logs, 0 );
        $this->assertArrayHasKey( '404', $summary['rows'] );
        $this->assertSame( '404', $summary['rows']['404']['key'] );
        $this->assertSame( 'Not found requests (404)', $summary['rows']['404']['label'] );
        $settings = expLogHealth::settings( $logs, $manager );
        $keep = array_values( array_filter( $settings['404'], function ( $s ) { return $s['name'] === 'keep'; } ) )[0];
        $this->assertSame( 5, $keep['value'] );
        $this->assertSame( 'Keep[404]', $keep['var'] );
    }

    public function testDigestSettingsByKey()
    {
        $digest = new expLogDigest( $this->config() );
        foreach ( array( '404', 404 ) as $key )
        {
            $this->assertSame( 'reason', $digest->groupingFor( $key ) );
            $this->assertContains( 'notice', $digest->levelsFor( $key ) );
        }
        $out = $digest->digestFile( $this->locator()->resolveKey( '404' ), 0 );
        $this->assertSame( '404', $out['key'] );
        $this->assertSame( 'Not found requests (404)', $out['label'] );
    }
}
