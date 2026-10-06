<?php
/**
 * File containing the expContentLanguagesOverviewTest class.
 *
 * What the content languages page (content/translations) says about each language: which siteaccesses show it
 * and which show it first, the bit of its id, whether it can be removed and what stops it, the hint for a
 * language few objects use or no siteaccess lists, the overview's figures, the siteaccess table with locales that
 * are not content languages, and the locale choices of the add form. Plain arrays only: no database and no
 * language is added or removed. The counts the page reads from the database are tested in
 * expContentLanguagesCountsLiveTest.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Content\Translations;

class expContentLanguagesOverviewTest extends ezpTestCase
{
    private function siteMap()
    {
        return Translations::siteAccessLanguages( array(
            'site' => array( 'eng-US' ),
            'admin' => array( 'eng-US' ),
            'eng' => array( 'eng-GB', 'eng-US' ),
            'ger' => array( 'ger-DE', '', 'eng-US' ),
            'blank' => array(),
        ) );
    }

    private function row( $id, $locale, $objects, $classes, $extra = array() )
    {
        return Translations::overviewRow(
            array( 'id' => $id, 'name' => $extra['name'] ?? '', 'locale' => $locale, 'country' => 'Somewhere', 'intl_name' => 'Intl ' . $locale, 'native_name' => 'Native' ),
            array( 'objects' => $objects, 'objects_main' => $extra['main'] ?? 0, 'objects_only' => 0, 'classes' => $classes ),
            $extra['map'] ?? $this->siteMap(), $extra['default'] ?? 'eng-US', $extra['interface'] ?? 'eng-US' );
    }

    public function testSiteAccessLanguagesNamesEverySiteAndTheOnesThatShowItFirst()
    {
        $map = $this->siteMap();
        $this->assertSame( array( 'site', 'admin', 'eng', 'ger' ), $map['eng-US']['sites'] );
        $this->assertSame( array( 'site', 'admin' ), $map['eng-US']['main'] );
        $this->assertSame( array( 'eng' ), $map['eng-GB']['main'] );
        $this->assertSame( array( 'ger' ), $map['ger-DE']['sites'] );
        $this->assertArrayNotHasKey( '', $map, 'an empty entry is no language' );
        $this->assertSame( array( 'ger' ), $map['ger-DE']['main'], 'the empty entry does not count as a position' );
    }

    public function testBitPositionOfALanguageID()
    {
        $this->assertSame( 1, Translations::bitPosition( 2 ) );
        $this->assertSame( 2, Translations::bitPosition( 4 ) );
        $this->assertSame( 3, Translations::bitPosition( 8 ) );
        $this->assertSame( 61, Translations::bitPosition( 1 << 61 ) );
        $this->assertSame( 0, Translations::bitPosition( 1 ), 'bit 0 is the always-available flag, not a language' );
        $this->assertSame( 0, Translations::bitPosition( 6 ), 'a mask of two languages is not one id' );
        $this->assertSame( 0, Translations::bitPosition( 0 ) );
    }

    public function testALanguageInUseCannotBeRemovedAndSaysWhy()
    {
        $row = $this->row( 2, 'eng-US', 368, 75 );
        $this->assertFalse( $row['removable'] );
        $this->assertSame( array( 'objects', 'classes' ), $row['blockers'] );
        $this->assertSame( array( 'sites', 'default', 'interface' ), $row['warnings'] );
        $this->assertTrue( $row['is_default'] );
        $this->assertFalse( $row['attention'] );
        $this->assertSame( '', $row['hint'] );
        $this->assertSame( 1, $row['bit'] );
    }

    public function testALanguageNoSiteListsButClassesUseIsPointedOut()
    {
        // eng-GB on alpha before it was cleaned up: no object, six classes, and no admin siteaccess lists it
        $map = Translations::siteAccessLanguages( array( 'site' => array( 'eng-US' ), 'admin' => array( 'eng-US' ) ) );
        $row = $this->row( 8, 'eng-GB', 0, 6, array( 'map' => $map ) );
        $this->assertTrue( $row['unlisted'] );
        $this->assertTrue( $row['attention'] );
        $this->assertSame( 'unlisted_classes', $row['hint'] );
        $this->assertSame( array( 'classes' ), $row['blockers'] );
        $this->assertSame( array(), $row['warnings'] );
        $this->assertSame( array(), $row['sites'] );
    }

    public function testTheHintFollowsWhatUsesTheLanguage()
    {
        $map = Translations::siteAccessLanguages( array( 'site' => array( 'eng-US' ) ) );
        $this->assertSame( 'unlisted_content', $this->row( 8, 'eng-GB', 3, 6, array( 'map' => $map ) )['hint'] );
        $this->assertSame( 'unlisted_empty', $this->row( 8, 'eng-GB', 0, 0, array( 'map' => $map ) )['hint'] );
        $this->assertTrue( $this->row( 8, 'eng-GB', 0, 0, array( 'map' => $map ) )['removable'] );
        $few = $this->row( 2, 'eng-US', Translations::FEW_OBJECTS - 1, 0, array( 'map' => $map ) );
        $this->assertSame( 'few', $few['hint'] );
        $this->assertTrue( $few['few'] && $few['attention'] );
        $enough = $this->row( 2, 'eng-US', Translations::FEW_OBJECTS, 0, array( 'map' => $map ) );
        $this->assertFalse( $enough['few'] );
        $this->assertSame( '', $enough['hint'] );
        $this->assertSame( 'empty', $this->row( 2, 'eng-US', 0, 0, array( 'map' => $map ) )['hint'] );
    }

    public function testTheNameFallsBackToTheLocaleNameThenTheCode()
    {
        $this->assertSame( 'German', $this->row( 4, 'ger-DE', 1, 1, array( 'name' => ' German ' ) )['name'] );
        $this->assertSame( 'Intl ger-DE', $this->row( 4, 'ger-DE', 1, 1 )['name'] );
        $row = Translations::overviewRow( array( 'id' => 4, 'locale' => 'xyz-AB' ), array(), array(), '', '' );
        $this->assertSame( 'xyz-AB', $row['name'] );
        $this->assertSame( 0, $row['objects'] );
        $this->assertTrue( $row['removable'] );
    }

    public function testTheSearchTextIsLowerCaseAndNamesTheSites()
    {
        $row = $this->row( 8, 'eng-GB', 1, 0, array( 'name' => 'English (United Kingdom)' ) );
        $this->assertStringContainsString( 'english (united kingdom)', $row['search'] );
        $this->assertStringContainsString( 'eng-gb', $row['search'] );
        $this->assertStringContainsString( 'eng', $row['search'] );
    }

    public function testSummaryCountsAttentionRemovableAndFreeSlots()
    {
        $map = Translations::siteAccessLanguages( array( 'site' => array( 'eng-US', 'ger-DE' ) ) );
        $rows = array( $this->row( 2, 'eng-US', 368, 75, array( 'map' => $map, 'name' => 'English (American)' ) ),
                       $this->row( 4, 'ger-DE', 53, 6, array( 'map' => $map ) ),
                       $this->row( 8, 'eng-GB', 0, 0, array( 'map' => $map ) ) );
        $summary = Translations::summary( $rows, 62, 'eng-US' );
        $this->assertSame( 3, $summary['languages'] );
        $this->assertSame( 59, $summary['free'] );
        $this->assertSame( 1, $summary['attention'] );
        $this->assertSame( 1, $summary['removable'] );
        $this->assertSame( 1, $summary['unlisted'] );
        $this->assertSame( 'English (American)', $summary['default_name'] );
        $this->assertSame( 0, Translations::summary( array(), 2, '' )['attention'] );
        $this->assertSame( 0, Translations::summary( array_fill( 0, 3, $rows[0] ), 2, '' )['free'], 'never below zero' );
    }

    public function testSiteLanguageTableMarksTheFirstAndUnknownLocales()
    {
        $table = Translations::siteLanguageTable( array( 'site' => array( 'eng-US', ' ger-CH ', '' ), 'none' => array() ),
                                                  array( 'eng-US', 'ger-DE' ) );
        $this->assertSame( 'site', $table[0]['siteaccess'] );
        $this->assertSame( array( array( 'locale' => 'eng-US', 'known' => true, 'main' => true ),
                                  array( 'locale' => 'ger-CH', 'known' => false, 'main' => false ) ), $table[0]['languages'] );
        $this->assertSame( 1, $table[0]['unknown'] );
        $this->assertSame( array(), $table[1]['languages'] );
    }

    public function testLocaleChoicesAreSortedMarkedAndSearchable()
    {
        $choices = Translations::localeChoices( array(
            array( 'code' => 'ger-DE', 'intl_name' => 'German', 'native_name' => 'Deutsch', 'country' => 'Germany', 'comment' => '' ),
            array( 'code' => 'eng-GB', 'intl_name' => 'English', 'native_name' => 'English', 'country' => 'United Kingdom', 'comment' => '' ),
            array( 'code' => 'nor-NO', 'intl_name' => 'Norwegian', 'native_name' => 'Norsk', 'country' => 'Norway', 'comment' => 'Bokmål' ),
            array( 'code' => '', 'intl_name' => 'Broken' ),
        ), array( 'eng-GB' ) );
        $this->assertSame( array( 'eng-GB', 'ger-DE', 'nor-NO' ), array_column( $choices, 'code' ) );
        $this->assertTrue( $choices[0]['exists'] );
        $this->assertFalse( $choices[1]['exists'] );
        $this->assertSame( 'Norwegian [Bokmål]', $choices[2]['label'] );
        $this->assertStringContainsString( 'deutsch', $choices[1]['search'] );
        $this->assertStringContainsString( 'ger-de', $choices[1]['search'] );
    }
}
