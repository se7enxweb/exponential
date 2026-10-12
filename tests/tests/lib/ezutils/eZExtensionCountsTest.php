<?php
/**
 * eZExtension::extensionLists() and extensionCounts(): the one definition of how many extensions there are, used by
 * the admin dashboard, Info > About and the update dashboard, so the same word means the same number on each.
 *
 *  - on_disk:  every directory in every extension root, each name once (a later root wins), active or not
 *  - global:   ActiveExtensions found on disk
 *  - here:     ActiveExtensions + this siteaccess's ActiveAccessExtensions, found on disk
 *  - anywhere: active in at least one siteaccess, found on disk
 *  - inactive: on disk, active nowhere
 *  - missing:  named in the settings, not on disk
 *
 * Two fixture extension roots under var/tmp; every test runs in its own process because it changes the global
 * site.ini.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezutils/eZExtensionCountsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class eZExtensionCountsTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = getcwd() . '/var/tmp/extension-counts-test-' . uniqid();
        // first root: four extensions, a hidden directory and a plain file, which are no extensions
        foreach ( array( 'one/t402alpha', 'one/T402Beta', 'one/t402gamma', 'one/t402epsilon', 'one/.git', 'two/t402beta', 'two/t402delta' ) as $path )
            mkdir( $this->dir . '/' . $path, 0777, true );
        file_put_contents( $this->dir . '/one/README', "not an extension\n" );

        $ini = eZINI::instance();
        $ini->setVariable( 'ExtensionSettings', 'ExtensionDirectory', $this->dir . '/one' );
        $ini->setVariable( 'ExtensionSettings', 'AdditionalExtensionDirectories', array( $this->dir . '/two' ) );
        $ini->setVariable( 'ExtensionSettings', 'ExtensionOrdering', 'disabled' );
        // listed twice, in another case, and one that is not on disk
        $ini->setVariable( 'ExtensionSettings', 'ActiveExtensions', array( 't402alpha', 'T402BETA', 't402alpha', 't402ghost' ) );
        $ini->setVariable( 'ExtensionSettings', 'ActiveAccessExtensions', array( 't402delta' ) );
        unset( $GLOBALS['eZActiveExtensions'] );
    }

    protected function tearDown(): void
    {
        if ( $this->dir && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
        parent::tearDown();
    }

    private function accessLists()
    {
        return array( 'admin' => array( 't402delta' ), 'other' => array( 't402gamma', 't402ghost2' ), 'empty' => array() );
    }

    public function testOnDiskCountsEveryDirectoryOfEveryRootOnce()
    {
        $lists = eZExtension::extensionLists( null, $this->accessLists() );
        // T402Beta of the first root is t402beta of the second: the later root wins, as in extensionPath()
        $this->assertSame( array( 't402alpha', 't402beta', 't402delta', 't402epsilon', 't402gamma' ), $lists['on_disk'] );
    }

    public function testGlobalIsActiveExtensionsFoundOnDiskEachOnce()
    {
        $lists = eZExtension::extensionLists( null, $this->accessLists() );
        $this->assertSame( array( 't402alpha', 't402beta' ), $lists['global'] );
    }

    public function testHereAddsTheSiteAccessExtensions()
    {
        $lists = eZExtension::extensionLists( null, $this->accessLists() );
        $this->assertSame( array( 't402alpha', 't402beta', 't402delta' ), $lists['here'] );
    }

    public function testHereAddsExtensionsSwitchedOnAtRunTime()
    {
        $GLOBALS['eZActiveExtensions'] = array( 't402epsilon' );
        $lists = eZExtension::extensionLists( null, false );
        $this->assertSame( array( 't402alpha', 't402beta', 't402delta', 't402epsilon' ), $lists['here'] );
    }

    public function testAnywhereAddsEverySiteAccessAndInactiveIsTheRest()
    {
        $lists = eZExtension::extensionLists( null, $this->accessLists() );
        $this->assertSame( array( 't402alpha', 't402beta', 't402delta', 't402gamma' ), $lists['anywhere'] );
        $this->assertSame( array( 't402epsilon' ), $lists['inactive'] );
        $this->assertSame( array( 't402ghost', 't402ghost2' ), $lists['missing'] );
    }

    public function testOnDiskIsAnywherePlusInactive()
    {
        $counts = eZExtension::extensionCounts( null, $this->accessLists() );
        $this->assertSame( $counts['on_disk'], $counts['anywhere'] + $counts['inactive'] );
        $this->assertSame( array( 'on_disk' => 5, 'global' => 2, 'here' => 3, 'anywhere' => 4, 'inactive' => 1, 'missing' => 2 ), $counts );
    }

    public function testWithoutSiteAccessListsTheKindsThatNeedThemAreNull()
    {
        $counts = eZExtension::extensionCounts( null, false );
        $this->assertSame( array( 'on_disk' => 5, 'global' => 2, 'here' => 3, 'anywhere' => null, 'inactive' => null, 'missing' => 1 ), $counts );
    }

    public function testSetupFetchGivesTheSameCounts()
    {
        $collection = new eZSetupFunctionCollection();
        $this->assertSame( array( 'result' => eZExtension::extensionCounts( null, false ) ), $collection->fetchExtensionCounts() );
    }
}
