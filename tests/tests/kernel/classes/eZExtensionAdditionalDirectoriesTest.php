<?php
/**
 * File containing the eZExtensionAdditionalDirectoriesTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZExtensionAdditionalDirectoriesTest extends ezpTestCase
{
    protected static $baseExtensionDir = 'tests/tests/kernel/classes/extensions';
    protected static $additionalExtensionDir = 'tests/tests/kernel/classes/extension_src';

    public function setUp(): void
    {
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'ExtensionDirectory', self::$baseExtensionDir );
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'AdditionalExtensionDirectories', array( self::$additionalExtensionDir ) );
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'ExtensionOrdering', 'disabled' );
        eZExtension::clearActiveExtensionsMemoryCache();
        self::clearExtensionCaches();
    }

    public function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        eZExtension::clearActiveExtensionsMemoryCache();
        self::clearExtensionCaches();
    }

    public function testExtensionRootDirectoriesReturnsBaseAndAdditional()
    {
        $roots = eZExtension::extensionRootDirectories();
        $this->assertSame(
            array( self::$baseExtensionDir, self::$additionalExtensionDir ),
            $roots );
    }

    public function testExtensionPathReturnsAdditionalRootWhenOverridden()
    {
        $path = eZExtension::extensionPath( 'override_ext' );
        $this->assertSame( self::$additionalExtensionDir . '/override_ext', $path );
    }

    public function testExtensionPathFallsBackToBaseRoot()
    {
        $path = eZExtension::extensionPath( 'ezfind' );
        $this->assertSame( self::$baseExtensionDir . '/ezfind', $path );
    }

    public function testExtensionNameResolvesCaseFromAdditionalRoot()
    {
        $this->assertSame( 'CaseExt', eZExtension::extensionName( 'caseext' ) );
    }

    public function testActiveExtensionsFindsExtensionInAdditionalRoot()
    {
        self::setExtensions( array( 'custom_ext' ) );
        $this->assertSame(
            array( 'custom_ext' ),
            eZExtension::activeExtensions() );
    }

    public function testActiveExtensionsPrefersAdditionalRootForSameName()
    {
        self::setExtensions( array( 'override_ext' ) );
        $this->assertSame(
            array( 'override_ext' ),
            eZExtension::activeExtensions() );

        $this->assertSame(
            self::$additionalExtensionDir . '/override_ext',
            eZExtension::extensionPath( 'override_ext' ) );
    }

    /**
     * Sets the active extensions
     *
     * @param string $type ActiveExtensions or ActiveAccessExtensions
     * @param array $extensions Extensions to set as active ones
     */
    private static function setExtensions( $extensions, $type = 'ActiveExtensions' )
    {
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', $type, $extensions );
        self::clearExtensionCaches();
    }

    /**
     * Clears the static extension name and path caches so each test starts
     * with a clean state.
     */
    private static function clearExtensionCaches()
    {
        $reflection = new ReflectionClass( 'eZExtension' );

        $nameCache = $reflection->getProperty( 'extensionNameCache' );
        $nameCache->setAccessible( true );
        $nameCache->setValue( null, array() );

        $pathCache = $reflection->getProperty( 'extensionPathCache' );
        $pathCache->setAccessible( true );
        $pathCache->setValue( null, array() );
    }
}
