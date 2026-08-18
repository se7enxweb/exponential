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
    protected static $createdFixtureDirectories = array();

    /**
     * Detects whether the runtime filesystem is case-sensitive.
     *
     * On a case-insensitive filesystem a path with a different case still points
     * to the same file, so case-specific assertions have to be skipped.
     */
    private static function isFilesystemCaseSensitive()
    {
        $tempFile = @tempnam( sys_get_temp_dir(), 'ezcase' );
        if ( $tempFile === false )
            return true; // default to case-sensitive if we cannot probe

        $lowerCaseFile = strtolower( $tempFile );
        $isCaseSensitive = !file_exists( $lowerCaseFile );
        @unlink( $tempFile );
        return $isCaseSensitive;
    }

    public function setUp(): void
    {
        self::createExtensionFixtures();
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
        self::removeCreatedExtensionFixtures();
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
        if ( !self::isFilesystemCaseSensitive() )
            $this->markTestSkipped( 'Case-specific path resolution cannot be verified on a case-insensitive filesystem.' );

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
        if ( !self::isFilesystemCaseSensitive() )
            $this->markTestSkipped( 'Case-specific name resolution cannot be verified on a case-insensitive filesystem.' );

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
        $nameCache->setValue( null, array() );

        $pathCache = $reflection->getProperty( 'extensionPathCache' );
        $pathCache->setValue( null, array() );
    }

    private static function createExtensionFixtures()
    {
        self::$createdFixtureDirectories = array();

        foreach ( array(
            self::$additionalExtensionDir,
            self::$baseExtensionDir . '/override_ext',
            self::$additionalExtensionDir . '/override_ext',
            self::$additionalExtensionDir . '/custom_ext',
            self::$additionalExtensionDir . '/CaseExt',
        ) as $directory )
        {
            if ( !is_dir( $directory ) )
            {
                mkdir( $directory, 0777, true );
                self::$createdFixtureDirectories[] = $directory;
            }
        }
    }

    private static function removeCreatedExtensionFixtures()
    {
        foreach ( array_reverse( self::$createdFixtureDirectories ) as $directory )
        {
            if ( is_dir( $directory ) )
                rmdir( $directory );
        }

        self::$createdFixtureDirectories = array();
    }
}
