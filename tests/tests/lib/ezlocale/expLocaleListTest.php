<?php
/**
 * eZLocale::localeList() and its pure helper localeCodesFromFileNames(): every call is answered with what it asks for
 * (objects or codes, with or without variations), whichever kind of call came first in the request. The single cache
 * of old kept the first answer: after one call for codes the Add language form (content/translations) got strings,
 * kept none of them and offered no language at all.
 *
 * No kernel bootstrap, no database: share/locale is read from the working copy.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezlocale
 */

class expLocaleListTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        unset( $GLOBALS['expLocaleCodeLists'] );
    }

    protected function tearDown(): void
    {
        unset( $GLOBALS['expLocaleCodeLists'] );
    }

    public function testCodesFromFileNamesKeepOnlyLocaleFilesSortedAndUnique()
    {
        $files = array( '.', '..', 'heb-IL.ini', 'lat-VA.ini', 'ger-DE.ini', 'ger-DE.ini', 'README', 'nor-NO@intl.ini', 'x.ini.bak', 'eng-GB.ini~' );
        $this->assertSame( array( 'ger-DE', 'heb-IL', 'lat-VA' ), eZLocale::localeCodesFromFileNames( $files, false ) );
        $this->assertSame( array( 'ger-DE', 'heb-IL', 'lat-VA', 'nor-NO@intl' ), eZLocale::localeCodesFromFileNames( $files, true ) );
        $this->assertSame( array(), eZLocale::localeCodesFromFileNames( array(), true ) );
    }

    public function testCodesFirstThenObjectsGivesObjects()
    {
        $codes = eZLocale::localeList( false, true );
        $this->assertNotEmpty( $codes );
        $this->assertIsString( reset( $codes ) );
        $objects = eZLocale::localeList( true, false );
        $this->assertNotEmpty( $objects );
        foreach ( $objects as $object )
            $this->assertInstanceOf( 'eZLocale', $object );
    }

    public function testObjectsFirstThenCodesGivesCodes()
    {
        $objects = eZLocale::localeList( true, false );
        $this->assertInstanceOf( 'eZLocale', reset( $objects ) );
        $codes = eZLocale::localeList( false, false );
        $this->assertIsString( reset( $codes ) );
        $this->assertCount( count( $objects ), $codes, 'one object for every code' );
    }

    public function testWithoutVariationsLeavesOutVariationFiles()
    {
        foreach ( eZLocale::localeList( false, false ) as $code )
            $this->assertStringNotContainsString( '@', $code );
        $with = eZLocale::localeList( false, true );
        $without = eZLocale::localeList( false, false );
        $this->assertSame( array(), array_values( array_diff( $without, $with ) ), 'the list without variations is part of the full one' );
    }

    public function testRightToLeftAndLatinLocalesAreListed()
    {
        $codes = eZLocale::localeList( false, false );
        foreach ( array( 'heb-IL', 'lat-VA' ) as $code )
        {
            if ( !is_file( "share/locale/$code.ini" ) )
                continue;
            $this->assertContains( $code, $codes );
        }
        $this->assertContains( 'ger-DE', $codes );
    }

    public function testACacheOfAnOtherShapeIsReadAgain()
    {
        // a long-running process (a Velocity worker) may hold the list of an older share/locale or an older shape
        $GLOBALS['expLocaleCodeLists'] = array( 'without_variations' => array( 'stamp' => -1, 'codes' => array( 'old-XX' ) ) );
        $this->assertNotContains( 'old-XX', eZLocale::localeList( false, false ) );
    }
}
