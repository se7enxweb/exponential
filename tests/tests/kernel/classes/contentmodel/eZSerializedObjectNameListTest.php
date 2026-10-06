<?php
/**
 * eZSerializedObjectNameList, the translated names of content classes, class attributes, class groups and states
 * as they are stored (a serialized array of locale => name with an 'always-available' entry): reading and writing
 * the stored string, names per locale, the always-available language, dirty tracking, appending a group name,
 * the language map of a package import (normalize()) and keeping the always-available entry valid.
 *
 * Only the methods that need no content language from the database are called, with explicit locales.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZSerializedObjectNameListTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        // the kernel's handlers are registered on first use; outside a test, so no test is reported for them
        chdir( dirname( __DIR__, 5 ) );
        eZExecution::registerShutdownHandler();
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    private static function stored( array $names )
    {
        return serialize( $names );
    }

    public function testStoredStringIsReadAndWrittenBack()
    {
        $stored = self::stored( array( 'eng-GB' => 'Article', 'ger-DE' => 'Artikel', 'always-available' => 'eng-GB' ) );
        $list = new eZSerializedObjectNameList( $stored );
        $this->assertSame( $stored, $list->serializeNames() );
        $this->assertSame( 'Article', $list->name( 'eng-GB' ) );
        $this->assertSame( 'Artikel', $list->nameByLanguageLocale( 'ger-DE' ) );
        $this->assertSame( '', $list->nameByLanguageLocale( 'fre-FR' ) );
        $this->assertSame( 'Article', $list->alwaysAvailableName() );
        $this->assertSame( 'eng-GB', $list->alwaysAvailableLanguageLocale() );
        $this->assertSame( array( 'eng-GB', 'ger-DE' ), $list->languageLocaleList() );
        $this->assertSame( array( 'eng-GB' => 'Article', 'ger-DE' => 'Artikel' ), $list->cleanNameList() );
        $this->assertSame( 3, $list->nameListCount() );
        $this->assertFalse( $list->isEmpty() );
        $this->assertFalse( $list->hasDirtyData() );
        $this->assertTrue( $list->hasNameInLocale( 'ger-DE' ) );
        $this->assertFalse( $list->hasNameInLocale( 'fre-FR' ) );
        $this->assertFalse( $list->hasNameInLocale( false ) );
        $this->assertSame( 'Artikel', eZSerializedObjectNameList::nameFromSerializedString( $stored, 'ger-DE' ) );
    }

    public static function brokenStoredProvider()
    {
        return array( 'empty' => array( '' ), 'false' => array( false ), 'not serialized' => array( 'Article' ), 'serialized string' => array( serialize( 'x' ) ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('brokenStoredProvider')]
    public function testBrokenStoredStringIsAnEmptyList( $stored )
    {
        $list = new eZSerializedObjectNameList();
        $list->initFromSerializedList( $stored );
        $this->assertTrue( $list->isEmpty() );
        $this->assertSame( array(), $list->nameList() );
        $this->assertSame( '', $list->alwaysAvailableName() );
        $this->assertFalse( $list->alwaysAvailableLanguageLocale() );
        $this->assertSame( array(), $list->languageLocaleList() );
    }

    public function testListMadeWithoutAStringIsEmpty()
    {
        $list = new eZSerializedObjectNameList();
        $this->assertTrue( $list->isEmpty() );
        $this->assertSame( 0, $list->nameListCount() );
        $this->assertSame( array(), $list->languageLocaleList() );
    }

    public function testInitFromStringWithALocale()
    {
        $list = new eZSerializedObjectNameList();
        $list->initFromString( 'Folder', 'nor-NO' );
        $this->assertSame( array( 'nor-NO' => 'Folder', 'always-available' => 'nor-NO' ), $list->nameList() );
        $this->assertFalse( $list->hasDirtyData() );
    }

    public function testSettingNamesMarksTheListDirty()
    {
        $list = new eZSerializedObjectNameList( self::stored( array( 'eng-GB' => 'A', 'always-available' => 'eng-GB' ) ) );
        $list->setNameByLanguageLocale( 'B', 'ger-DE' );
        $this->assertTrue( $list->hasDirtyData() );
        $this->assertSame( 'B', $list->name( 'ger-DE' ) );
        $this->assertSame( 'A', $list->setName( 'C', 'eng-GB' ), 'setName() gives the old name' );
        $this->assertSame( 'C', $list->name( 'eng-GB' ) );

        $list->setHasDirtyData( false );
        $list->removeName( 'fre-FR' );
        $this->assertFalse( $list->hasDirtyData(), 'removing a name that is not there changes nothing' );
        $list->removeName( 'ger-DE' );
        $this->assertTrue( $list->hasDirtyData() );
        $this->assertFalse( $list->hasNameInLocale( 'ger-DE' ) );
    }

    public function testMergeNameList()
    {
        $list = new eZSerializedObjectNameList( self::stored( array( 'eng-GB' => 'A', 'always-available' => 'eng-GB' ) ) );
        $list->mergeNameList( array( 'eng-GB' => 'A2', 'ger-DE' => 'B' ) );
        $this->assertSame( array( 'eng-GB' => 'A2', 'always-available' => 'eng-GB', 'ger-DE' => 'B' ), $list->nameList() );
        $this->assertTrue( $list->hasDirtyData() );
    }

    public function testAlwaysAvailableLanguageCanBeSetAndRemoved()
    {
        $list = new eZSerializedObjectNameList( self::stored( array( 'eng-GB' => 'A', 'ger-DE' => 'B', 'always-available' => 'eng-GB' ) ) );
        $list->setAlwaysAvailableLanguage( 'ger-DE' );
        $this->assertSame( 'B', $list->alwaysAvailableName() );
        $list->setAlwaysAvailableLanguage( false );
        $this->assertFalse( $list->alwaysAvailableLanguageLocale() );
        $this->assertSame( '', $list->alwaysAvailableName() );
        $this->assertSame( array( 'eng-GB' => 'A', 'ger-DE' => 'B' ), $list->nameList() );
    }

    public function testAppendGroupNameLeavesTheAlwaysAvailableEntry()
    {
        $list = new eZSerializedObjectNameList( self::stored( array( 'eng-GB' => 'Content', 'ger-DE' => 'Inhalt', 'always-available' => 'eng-GB' ) ) );
        $list->appendGroupName( ' (copy)' );
        $this->assertSame( array( 'eng-GB' => 'Content (copy)', 'ger-DE' => 'Inhalt (copy)', 'always-available' => 'eng-GB' ), $list->nameList() );
    }

    public function testCopyAndClone()
    {
        $list = new eZSerializedObjectNameList( self::stored( array( 'eng-GB' => 'A', 'always-available' => 'eng-GB' ) ) );
        $list->setNameByLanguageLocale( 'B', 'ger-DE' );
        // copy() takes the default language along; given here, it is not looked up in the database
        $language = new eZContentLanguage( array( 'id' => 2, 'locale' => 'eng-GB', 'name' => 'English' ) );
        $list->setDefaultLanguage( $language );
        $other = new eZSerializedObjectNameList();
        $list->copy( $other );
        $this->assertSame( $language, $other->defaultLanguage() );
        $this->assertSame( 'eng-GB', $other->defaultLanguageLocale() );
        $this->assertSame( $list->nameList(), $other->nameList() );
        $this->assertTrue( $other->hasDirtyData() );

        $clone = clone $list;
        $clone->setNameByLanguageLocale( 'X', 'eng-GB' );
        $this->assertSame( 'A', $list->name( 'eng-GB' ), 'a clone has its own names' );
        $list->resetNameList();
        $this->assertTrue( $list->isEmpty() );
        $this->assertNotSame( $list, $list->create( false ), 'create() makes a new list' );
    }

    public function testUpdateAlwaysAvailableKeepsAnExistingName()
    {
        $list = new eZSerializedObjectNameList( self::stored( array( 'eng-GB' => 'A', 'ger-DE' => 'B', 'always-available' => 'ger-DE' ) ) );
        $list->updateAlwaysAvailable();
        $this->assertSame( 'ger-DE', $list->alwaysAvailableLanguageLocale() );
        $list->updateAlwaysAvailable( 'eng-GB' );
        $this->assertSame( 'eng-GB', $list->alwaysAvailableLanguageLocale() );
    }

    public function testUpdateAlwaysAvailableFallsBackToTheFirstName()
    {
        $list = new eZSerializedObjectNameList( self::stored( array( 'eng-GB' => 'A', 'ger-DE' => 'B', 'always-available' => 'fre-FR' ) ) );
        $list->updateAlwaysAvailable();
        $this->assertSame( 'eng-GB', $list->alwaysAvailableLanguageLocale() );
    }

    public function testUpdateAlwaysAvailableNeverPointsAtItself()
    {
        // the always-available entry stored first, as a package or a hand-written definition may have it
        $list = new eZSerializedObjectNameList( self::stored( array( 'always-available' => 'fre-FR', 'eng-GB' => 'A', 'ger-DE' => 'B' ) ) );
        $list->updateAlwaysAvailable();
        $this->assertSame( 'eng-GB', $list->alwaysAvailableLanguageLocale() );
        $this->assertSame( 'A', $list->alwaysAvailableName() );
    }

    public function testUpdateAlwaysAvailableOfAListWithoutNames()
    {
        $list = new eZSerializedObjectNameList( self::stored( array( 'always-available' => 'fre-FR' ) ) );
        $list->updateAlwaysAvailable();
        $this->assertFalse( $list->alwaysAvailableLanguageLocale() );
        $this->assertSame( array(), $list->nameList() );
    }

    public function testNormalizeMapsAndSkipsLanguages()
    {
        $list = new eZSerializedObjectNameList( self::stored( array( 'eng-GB' => 'Article', 'ger-DE' => 'Artikel', 'nor-NO' => 'Artikkel', 'always-available' => 'eng-GB' ) ) );
        $list->normalize( array( 'map_table' => array( 'eng-GB' => 'eng-US', 'ger-DE' => 'skip_language' ) ) );
        $this->assertSame( array( 'eng-US' => 'Article', 'nor-NO' => 'Artikkel', 'always-available' => 'eng-US' ), $list->nameList() );
    }

    public function testNormalizeOfTheSkippedAlwaysAvailableLanguage()
    {
        $list = new eZSerializedObjectNameList( self::stored( array( 'eng-GB' => 'Article', 'ger-DE' => 'Artikel', 'always-available' => 'eng-GB' ) ) );
        $list->normalize( array( 'map_table' => array( 'eng-GB' => 'skip_language' ) ) );
        $this->assertSame( array( 'ger-DE' => 'Artikel', 'always-available' => 'ger-DE' ), $list->nameList() );
    }

    public function testNormalizeDoesNotDependOnTheOrderOfTheMap()
    {
        $names = self::stored( array( 'eng-GB' => 'E', 'ger-DE' => 'G', 'always-available' => 'eng-GB' ) );
        $a = new eZSerializedObjectNameList( $names );
        $a->normalize( array( 'map_table' => array( 'ger-DE' => 'skip_language', 'eng-GB' => 'ger-DE' ) ) );
        $b = new eZSerializedObjectNameList( $names );
        $b->normalize( array( 'map_table' => array( 'eng-GB' => 'ger-DE', 'ger-DE' => 'skip_language' ) ) );
        $this->assertSame( array( 'ger-DE' => 'E', 'always-available' => 'ger-DE' ), $a->nameList() );
        $this->assertSame( $a->nameList(), $b->nameList() );
    }

    public function testNormalizeWithoutAMapChangesNothing()
    {
        $names = array( 'eng-GB' => 'E', 'always-available' => 'eng-GB' );
        $list = new eZSerializedObjectNameList( self::stored( $names ) );
        $list->normalize( array() );
        $list->normalize( 'x' );
        $this->assertSame( $names, $list->nameList() );
    }
}
