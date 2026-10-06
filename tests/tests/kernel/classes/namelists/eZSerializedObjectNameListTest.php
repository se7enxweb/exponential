<?php
/**
 * Tests of eZSerializedObjectNameList, the translated names of content classes, class attributes and object states
 * stored as a serialized array with an "always-available" entry: reading and writing the stored string, names by
 * locale, the always available name, broken stored strings, merging, removing, appending to every name and the
 * language mapping of normalize() that the package installer uses. Only what works without the languages of the
 * database (explicit locales).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZSerializedObjectNameListTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    private function names( array $names )
    {
        return new eZSerializedObjectNameList( serialize( $names ) );
    }

    public function testStoredStringIsReadAndWrittenBack()
    {
        $names = array( 'eng-GB' => 'Article', 'ger-DE' => 'Artikel', 'always-available' => 'eng-GB' );
        $list = $this->names( $names );
        $this->assertSame( $names, $list->nameList() );
        $this->assertSame( serialize( $names ), $list->serializeNames() );
        $this->assertFalse( $list->hasDirtyData() );
        $this->assertFalse( $list->isEmpty() );
    }

    public static function brokenProvider()
    {
        return array( 'empty' => array( '' ), 'not serialized' => array( 'Article' ), 'serialized string' => array( serialize( 'Article' ) ),
                      'false' => array( false ), 'truncated' => array( 'a:1:{s:6:"eng-GB";s:7:"Art' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('brokenProvider')]
    public function testBrokenStoredStringsGiveAnEmptyList( $stored )
    {
        $list = new eZSerializedObjectNameList();
        $list->initFromSerializedList( $stored );
        $this->assertSame( array(), $list->nameList() );
        $this->assertTrue( $list->isEmpty() );
        $this->assertSame( '', $list->name( 'eng-GB' ) );
    }

    public function testAListWithoutAStoredStringIsEmpty()
    {
        $list = new eZSerializedObjectNameList();
        $this->assertTrue( $list->isEmpty() );
        $this->assertSame( array(), $list->languageLocaleList() );
    }

    public function testNamesByLocale()
    {
        $list = $this->names( array( 'eng-GB' => 'Article', 'ger-DE' => 'Artikel', 'always-available' => 'ger-DE' ) );
        $this->assertSame( 'Article', $list->name( 'eng-GB' ) );
        $this->assertSame( 'Artikel', $list->nameByLanguageLocale( 'ger-DE' ) );
        $this->assertSame( '', $list->name( 'fre-FR' ) );
        $this->assertTrue( $list->hasNameInLocale( 'eng-GB' ) );
        $this->assertFalse( $list->hasNameInLocale( 'fre-FR' ) );
        $this->assertFalse( $list->hasNameInLocale( '' ) );
        $this->assertSame( 'Artikel', $list->alwaysAvailableName() );
        $this->assertSame( 'ger-DE', $list->alwaysAvailableLanguageLocale() );
        $this->assertSame( array( 'eng-GB', 'ger-DE' ), $list->languageLocaleList() );
        $this->assertSame( array( 'eng-GB' => 'Article', 'ger-DE' => 'Artikel' ), $list->cleanNameList() );
        $this->assertSame( 'Article', eZSerializedObjectNameList::nameFromSerializedString( $list->serializeNames(), 'eng-GB' ) );
    }

    public function testWithoutAlwaysAvailableEntry()
    {
        $list = $this->names( array( 'eng-GB' => 'Article' ) );
        $this->assertFalse( $list->alwaysAvailableLanguageLocale() );
        $this->assertSame( '', $list->alwaysAvailableName() );
        $this->assertFalse( $list->alwaysAvailableLanguage() );
    }

    public function testSettingNamesMakesTheListDirty()
    {
        $list = $this->names( array( 'eng-GB' => 'Article', 'always-available' => 'eng-GB' ) );
        $this->assertSame( 'Article', $list->setName( 'Blog post', 'eng-GB' ), 'setName() returns the name it replaced' );
        $this->assertTrue( $list->hasDirtyData() );
        $list->setNameByLanguageLocale( 'Billet', 'fre-FR' );
        $list->setNameByLanguageLocale( 'nowhere', '' );
        $this->assertSame( array( 'eng-GB' => 'Blog post', 'always-available' => 'eng-GB', 'fre-FR' => 'Billet' ), $list->nameList() );

        $list->setHasDirtyData( false );
        $list->removeName( 'nor-NO' );
        $this->assertFalse( $list->hasDirtyData(), 'removing a name that is not there changes nothing' );
        $list->removeName( 'fre-FR' );
        $this->assertTrue( $list->hasDirtyData() );
        $this->assertFalse( $list->hasNameInLocale( 'fre-FR' ) );
    }

    public function testAlwaysAvailableLanguageCanBeSetAndRemoved()
    {
        $list = $this->names( array( 'eng-GB' => 'A', 'ger-DE' => 'B', 'always-available' => 'eng-GB' ) );
        $list->setAlwaysAvailableLanguage( 'ger-DE' );
        $this->assertSame( 'B', $list->alwaysAvailableName() );
        $list->setAlwaysAvailableLanguage( false );
        $this->assertArrayNotHasKey( 'always-available', $list->nameList() );
    }

    public function testUpdateAlwaysAvailableFallsBackToTheFirstName()
    {
        $list = $this->names( array( 'ger-DE' => 'B', 'eng-GB' => 'A', 'always-available' => 'fre-FR' ) );
        $list->updateAlwaysAvailable();
        $this->assertSame( 'ger-DE', $list->alwaysAvailableLanguageLocale() );

        $list->updateAlwaysAvailable( 'eng-GB' );
        $this->assertSame( 'eng-GB', $list->alwaysAvailableLanguageLocale() );
    }

    public function testMergeNameListAddsAndReplaces()
    {
        $list = $this->names( array( 'eng-GB' => 'A', 'always-available' => 'eng-GB' ) );
        $list->mergeNameList( array( 'eng-GB' => 'A2', 'ger-DE' => 'B' ) );
        $this->assertSame( array( 'eng-GB' => 'A2', 'always-available' => 'eng-GB', 'ger-DE' => 'B' ), $list->nameList() );
        $this->assertTrue( $list->hasDirtyData() );
    }

    public function testAppendGroupNameAppendsToEveryName()
    {
        $list = $this->names( array( 'eng-GB' => 'Users', 'ger-DE' => 'Benutzer', 'always-available' => 'eng-GB' ) );
        $list->appendGroupName( ' (copy)' );
        $this->assertSame( array( 'eng-GB' => 'Users (copy)', 'ger-DE' => 'Benutzer (copy)', 'always-available' => 'eng-GB' ), $list->nameList() );
    }

    public function testCopyAndClone()
    {
        $list = $this->names( array( 'eng-GB' => 'A', 'always-available' => 'eng-GB' ) );
        $list->setDefaultLanguage( new eZContentLanguage( array( 'id' => 2, 'locale' => 'eng-GB', 'name' => 'English' ) ) );
        $list->setHasDirtyData();
        $copy = new eZSerializedObjectNameList();
        $list->copy( $copy );
        $this->assertSame( $list->nameList(), $copy->nameList() );
        $this->assertTrue( $copy->hasDirtyData() );
        $this->assertSame( 'eng-GB', $copy->defaultLanguageLocale() );

        $clone = clone $list;
        $clone->setNameByLanguageLocale( 'Changed', 'eng-GB' );
        $this->assertSame( 'A', $list->name( 'eng-GB' ), 'a clone does not share its names' );
    }

    public function testInitFromStringWithALocale()
    {
        $list = new eZSerializedObjectNameList();
        $list->initFromString( 'Folder', 'nor-NO' );
        $this->assertSame( array( 'nor-NO' => 'Folder', 'always-available' => 'nor-NO' ), $list->nameList() );
        $this->assertFalse( $list->hasDirtyData() );
    }

    public function testResetNameList()
    {
        $list = $this->names( array( 'eng-GB' => 'A' ) );
        $list->resetNameList();
        $this->assertTrue( $list->isEmpty() );
    }
}
