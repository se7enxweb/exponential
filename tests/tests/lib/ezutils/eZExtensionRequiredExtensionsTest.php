<?php
/**
 * site.ini [ExtensionSettings] RequiredExtensions[]: the extensions the kernel's own designs need (expui: the admin
 * designs request its exp:: script packs). Each one that is installed is active in every siteaccess, ahead of
 * ActiveExtensions[], so an override that resets ActiveExtensions[] (a Platform Legacy project's) keeps it; one that
 * is not installed is left out without an error, so a site whose kernel predates the setting is not affected.
 *
 * A fixture extension root under var/tmp; every test runs in its own process because it changes the global site.ini.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezutils/eZExtensionRequiredExtensionsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class eZExtensionRequiredExtensionsTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugLogFileEnabled' );
    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = getcwd() . '/var/tmp/extension-required-test-' . uniqid();
        foreach ( array( 'w630ui', 'w630jscore', 'w630oe' ) as $name )
            mkdir( $this->dir . '/' . $name, 0777, true );

        $ini = eZINI::instance();
        $ini->setVariable( 'ExtensionSettings', 'ExtensionDirectory', $this->dir );
        $ini->setVariable( 'ExtensionSettings', 'AdditionalExtensionDirectories', array() );
        $ini->setVariable( 'ExtensionSettings', 'ExtensionOrdering', 'disabled' );
        // A project's override: the list reset, without the required extension
        $ini->setVariable( 'ExtensionSettings', 'ActiveExtensions', array( 'w630jscore', 'w630oe' ) );
        $ini->setVariable( 'ExtensionSettings', 'ActiveAccessExtensions', array() );
        $ini->setVariable( 'ExtensionSettings', 'RequiredExtensions', array( 'w630ui', 'w630ghost' ) );
        unset( $GLOBALS['eZActiveExtensions'] );

        $debug = new eZDebug();
        $debug->ShowTypes = eZDebug::SHOW_ALL;
        $GLOBALS['eZDebugGlobalInstance'] = $debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
        eZDebug::setLogFileEnabled( false );
    }

    protected function tearDown(): void
    {
        if ( $this->dir && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
        parent::tearDown();
    }

    public function testTheShippedSettingsRequireExponentialUI()
    {
        $ini = new eZINI( 'site.ini', 'settings', null, false, null, true );
        $this->assertSame( array( 'expui' ), $ini->variable( 'ExtensionSettings', 'RequiredExtensions' ) );
    }

    public function testAnInstalledRequiredExtensionIsActiveAheadOfActiveExtensions()
    {
        $this->assertSame( array( 'w630ui', 'w630jscore', 'w630oe' ), array_values( eZExtension::activeExtensions( 'default' ) ) );
        $this->assertSame( array( 'w630ui', 'w630jscore', 'w630oe' ), array_values( eZExtension::activeExtensions() ) );
    }

    public function testAnAccessListDoesNotRepeatIt()
    {
        $this->assertSame( array(), array_values( eZExtension::activeExtensions( 'access' ) ) );
    }

    public function testListedInActiveExtensionsTooItIsActiveOnceAndFirst()
    {
        eZINI::instance()->setVariable( 'ExtensionSettings', 'ActiveExtensions', array( 'w630jscore', 'w630ui', 'w630oe' ) );
        $this->assertSame( array( 'w630ui', 'w630jscore', 'w630oe' ), array_values( eZExtension::activeExtensions( 'default' ) ) );
    }

    public function testOneThatIsNotInstalledIsLeftOutWithoutAnError()
    {
        $this->assertSame( array( 'w630ui' ), eZExtension::requiredExtensions() );
        $this->assertNotContains( 'w630ghost', eZExtension::activeExtensions() );
        // ExtensionOrdering's check ("Extension '...' does not exist") never sees it either
        $this->assertSame( array(), eZExtension::extensionLists( null, false )['missing'] );
        $errors = array();
        foreach ( eZDebug::instance()->DebugStrings as $entry )
            if ( $entry['Level'] === eZDebug::LEVEL_ERROR )
                $errors[] = $entry['String'];
        $this->assertSame( array(), $errors );
    }

    public function testExtensionListsCountItAsActiveEverywhere()
    {
        $lists = eZExtension::extensionLists( null, array( 'admin' => array() ) );
        $this->assertSame( array( 'w630ui', 'w630jscore', 'w630oe' ), $lists['global'] );
        $this->assertSame( array(), $lists['missing'] );
        $this->assertSame( array(), $lists['inactive'] );
    }
}
