<?php
/**
 * File containing the eZExtensionWithoutOrderingTest class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZExtensionWithoutOrderingTest extends ezpTestCase
{
    public function setUp(): void
    {
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'ExtensionDirectory', 'tests/tests/kernel/classes/extensions/' );
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'ExtensionOrdering', 'disabled' );
        self::clearActiveExtensionsCache();
    }
    public function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        self::clearActiveExtensionsCache();
    }

    public function testUnrelatedKeepOrder1()
    {
        self::setExtensions( array( 'ezmultiupload', 'ezfind' ) );
        $this->assertSame(
            array( 'ezmultiupload', 'ezfind' ),
            eZExtension::activeExtensions() );
    }

    public function testUnrelatedKeepOrder2()
    {
        self::setExtensions( array( 'ezfind', 'ezmultiupload' ) );
        $this->assertSame(
            array( 'ezfind', 'ezmultiupload' ),
            eZExtension::activeExtensions() );
    }

    public function testSimpleNoReordering()
    {
        self::setExtensions( array( 'ezjscore', 'ezfind' ) );
        $this->assertSame(
            array( 'ezjscore', 'ezfind' ),
            eZExtension::activeExtensions() );
    }

    public function testSimpleReordering()
    {
        self::setExtensions( array( 'ezfind', 'ezjscore' ) );
        $this->assertSame(
            array( 'ezfind', 'ezjscore' ),
            eZExtension::activeExtensions() );
    }

    public function testSimpleReorderingKeepInitialDummies()
    {
        self::setExtensions( array( 'dummy1', 'dummy2', 'dummy3', 'ezfind', 'ezjscore' ) );
        $this->assertSame(
            array( 'dummy1', 'dummy2', 'dummy3', 'ezfind', 'ezjscore' ),
            eZExtension::activeExtensions() );
    }

    public function testComplexReordering()
    {
        self::setExtensions( array( 'ezfind', 'ezflow', 'ezgmaplocation', 'ezjscore', 'ezmultiupload', 'ezoe', 'ezwebin', 'ezwt' ) );
        $this->assertSame(
            array( 'ezfind', 'ezflow', 'ezgmaplocation', 'ezjscore', 'ezmultiupload', 'ezoe', 'ezwebin', 'ezwt' ),
            eZExtension::activeExtensions() );
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
        self::clearActiveExtensionsCache();
    }

    /**
     * @todo Move to a common extension testing class
     */
    private static function clearActiveExtensionsCache()
    {
        // Removes the cache files and the in-memory copy (eZCache::clearActiveExtensions()),
        // so no wait for the expiry timestamp is needed: the two seconds slept here per call
        // added more than a minute to every run of the suite.
        eZCache::clearByID( 'active_extensions' );
    }
}

?>
