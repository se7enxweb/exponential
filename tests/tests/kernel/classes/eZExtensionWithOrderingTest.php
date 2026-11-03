<?php
/**
 * File containing the eZExtensionWithOrderingTest class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZExtensionWithOrderingTest extends ezpTestCase
{
    private $exceptionHandler;

    public function setUp(): void
    {
        $this->pushExceptionHandlerMarker();
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'ExtensionDirectory', 'tests/tests/kernel/classes/extensions/' );
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'ExtensionOrdering', 'enabled' );
        self::clearActiveExtensionsCache();
    }

    public function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        self::clearActiveExtensionsCache();
        $this->popExceptionHandlersToMarker();
    }

    /**
     * Starts the test without an exception handler: pushes a marker of its own and then null onto PHP's stack of
     * exception handlers. Paired with popExceptionHandlersToMarker(), which leaves the stack as it was, so PHPUnit
     * sees neither a handler left behind nor one of its own removed (a set_exception_handler( $saved ) in tearDown()
     * pushed one more instead, and the test was risky).
     */
    private function pushExceptionHandlerMarker()
    {
        $this->exceptionHandler = static function ( $e ) {};
        set_exception_handler( $this->exceptionHandler );
        set_exception_handler( null );
    }

    /** Pops every exception handler the test and the tested code pushed, the marker included. */
    private function popExceptionHandlersToMarker()
    {
        for ( $i = 0; $i < 100; $i++ )
        {
            $current = set_exception_handler( null );
            restore_exception_handler();
            restore_exception_handler();
            if ( $current === $this->exceptionHandler )
                return;
        }
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
            array( 'ezjscore', 'ezfind' ),
            eZExtension::activeExtensions() );
    }

    public function testSimpleReorderingKeepInitialDummies()
    {
        self::setExtensions( array( 'dummy1', 'dummy2', 'dummy3', 'ezfind', 'ezjscore' ) );
        $this->assertSame(
            array( 'dummy1', 'dummy2', 'dummy3', 'ezjscore', 'ezfind' ),
            eZExtension::activeExtensions() );
    }

    public function testComplexReordering()
    {
        self::setExtensions( array( 'ezfind', 'ezflow', 'ezgmaplocation', 'ezjscore', 'ezmultiupload', 'ezoe', 'ezwebin', 'ezwt' ) );
        $activeExtensions = eZExtension::activeExtensions();
        foreach ( array( 'ezfind', 'ezflow', 'ezgmaplocation', 'ezjscore', 'ezmultiupload', 'ezoe', 'ezwebin', 'ezwt' ) as $extension )
            $$extension = array_search( $extension, $activeExtensions );

        $this->assertLessThan( $ezwt, $ezmultiupload, 'ezwt should have had lower extension position than ezmultiupload' );
        $this->assertLessThan( $ezflow, $ezfind, 'ezflow should have had lower extension position than ezfind' );
        $this->assertLessThan( $ezwebin, $ezfind, 'ezwebin should have had lower extension position than ezfind' );
        $this->assertLessThan( $ezwebin, $ezflow, 'ezwebin should have had lower extension position than ezflow' );
        $this->assertLessThan( $ezwebin, $ezjscore, 'ezwebin should have had lower extension position than ezjscore' );
        $this->assertLessThan( $ezgmaplocation, $ezjscore, 'ezgmaplocation should have had lower extension position than ezjscore' );
        $this->assertLessThan( $ezoe, $ezjscore, 'ezoe should have had lower extension position than ezjscore' );
        $this->assertLessThan( $ezfind, $ezjscore, 'ezfind should have had lower extension position than ezjscore' );
    }

    public function testCycleInvolvesNoReordering1()
    {
        self::setExtensions( array( 'cycle1', 'cycle2' ) );
        $this->assertSame(
            array( 'cycle1', 'cycle2' ),
            eZExtension::activeExtensions() );
    }

    public function testCycleInvolvesNoReordering2()
    {
        self::setExtensions( array( 'cycle2', 'cycle1' ) );
        $this->assertSame(
            array( 'cycle2', 'cycle1' ),
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
