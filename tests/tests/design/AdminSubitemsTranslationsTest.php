<?php
/**
 * The sub-items table of the node view speaks the admin's language: column names, the groups and descriptions of the
 * table options, relative times and the sub item count of the remove confirmation.
 *
 *  ST-01 - every text of the column registry (settings/subitemscolumns.ini Name, Group and Description, the
 *          built-in columns, the units of the relative times) is in ger-DE and eng-US, once
 *  ST-02 - the columns server function hands out the group and the description translated, like the name; an
 *          attribute column's group names its class, its description stays the identifiers
 *  ST-03 - relative times ("2 minutes ago", "just now") are German under ger-DE
 *  ST-04 - the remove confirmation counts sub items in German
 *
 * No database: the registry reads the settings, the texts are looked up in share/translations.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSubitemsTranslationsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminSubitemsTranslationsTest extends PHPUnit\Framework\TestCase
{
    const CONTEXT = 'design/admin/node/view/full';

    private $savedSettings = array();
    private $savedGlobals = array();

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        foreach ( $this->savedSettings as $name => $value )
            $ini->setVariable( 'RegionalSettings', $name, $value );
        foreach ( $this->savedGlobals as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        $this->savedSettings = $this->savedGlobals = array();
    }

    /** Switches the translation to ger-DE for this test (restored in tearDown), without the translation cache. */
    private function german()
    {
        $ini = eZINI::instance();
        foreach ( array( 'Locale', 'TranslationCache' ) as $name )
            $this->savedSettings[$name] = $ini->variable( 'RegionalSettings', $name );
        foreach ( array( 'eZLocaleStringDefault', 'eZTranslatorManagerInstance', 'eZTSTranslationTables' ) as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $ini->setVariable( 'RegionalSettings', 'Locale', 'ger-DE' );
        $ini->setVariable( 'RegionalSettings', 'TranslationCache', 'disabled' );
        unset( $GLOBALS['eZLocaleStringDefault'], $GLOBALS['eZTranslatorManagerInstance'], $GLOBALS['eZTSTranslationTables'] );
        if ( eZLocale::instance()->localeFullCode() !== 'ger-DE' )
            $this->markTestSkipped( 'the locale ger-DE cannot be loaded here' );
    }

    /** source => list of translations of $context in the translation file of $locale */
    private static function messages( $locale, $context )
    {
        $dom = new DOMDocument();
        self::assertTrue( $dom->load( self::root() . "/share/translations/$locale/translation.ts" ) );
        $messages = array();
        foreach ( ( new DOMXPath( $dom ) )->query( "//context[name='$context']/message" ) as $message )
        {
            $translation = $message->getElementsByTagName( 'translation' )->item( 0 );
            $messages[$message->getElementsByTagName( 'source' )->item( 0 )->textContent][] = $translation ? $translation->textContent : '';
        }
        return $messages;
    }

    /** The texts the column registry translates in the columns' context */
    private static function registryTexts()
    {
        $texts = array();
        foreach ( file( self::root() . '/settings/subitemscolumns.ini' ) as $line )
            if ( preg_match( '/^(Name|Group|Description)=(.+)$/', rtrim( $line, "\r\n" ), $m ) )
                $texts[trim( $m[2] )] = 'subitemscolumns.ini ' . $m[1];
        foreach ( expSubitemsBuiltinColumn::definitions() as $key => $definition )
        {
            $texts[$definition['Name']] = "built-in $key";
            $texts[$definition['Group']] = "built-in $key";
        }
        foreach ( array( 'year', 'years', 'month', 'months', 'week', 'weeks', 'day', 'days', 'hour', 'hours', 'minute',
                         'minutes', '%1 ago', 'in %1', 'just now', 'Yes', 'No', 'Ascending', 'Descending', 'min',
                         'Custom', 'Attributes: %1' ) as $text )
            $texts[$text] = 'expsubitemscolumn.php';
        return $texts;
    }

    /** ST-01 */
    public function testEveryRegistryTextIsTranslatedOnce()
    {
        $texts = self::registryTexts();
        $this->assertGreaterThan( 250, count( $texts ) );
        foreach ( array( 'Owner', 'Owner ID', 'First author', 'Contributors', 'Published (age)', 'Subtree modified', 'People' ) as $known )
            $this->assertArrayHasKey( $known, $texts, 'the extraction finds ' . $known );
        $german = self::messages( 'ger-DE', self::CONTEXT );
        $english = self::messages( 'eng-US', self::CONTEXT );
        foreach ( $texts as $text => $where )
        {
            $this->assertNotSame( '', isset( $german[$text] ) ? trim( $german[$text][0] ) : '', "ger-DE: $text ($where)" );
            $this->assertArrayHasKey( $text, $english, "eng-US: $text ($where)" );
        }
        foreach ( array( 'ger-DE' => $german, 'eng-US' => $english ) as $locale => $messages )
            foreach ( $messages as $source => $translations )
                $this->assertCount( 1, $translations, "$locale has \"$source\" once in " . self::CONTEXT );
    }

    /** ST-02 */
    public function testTheColumnsAreDescribedInTheAdminsLanguage()
    {
        $this->german();
        $registry = new expSubitemsColumnRegistry();
        $owner = $registry->definedColumn( 'owner' );
        $this->assertInstanceOf( 'expSubitemsColumn', $owner );
        $described = $registry->describe( $owner );
        $this->assertSame( 'Eigentümer', $described['name'] );
        $this->assertSame( 'Personen', $described['group'] );
        $this->assertSame( 'Der Benutzer, der das Objekt angelegt hat (sein Eigentümer).', $described['description'] );

        $attribute = new expSubitemsAttributeColumn( 'attr:article/title', array( 'Name' => 'Titel', 'Group' => 'Attributes: Artikel',
            'ClassName' => 'Artikel', 'Description' => 'article/title (ezstring)' ) );
        $described = $registry->describe( $attribute );
        $this->assertSame( 'Titel', $described['name'] );
        $this->assertSame( 'Attribute: Artikel', $described['group'] );
        $this->assertSame( 'article/title (ezstring)', $described['description'] );
    }

    /** ST-03 */
    public function testRelativeTimesAreGerman()
    {
        $this->german();
        $now = 1760000000;
        $this->assertSame( 'vor 2 Minuten', expSubitemsColumn::formatAge( $now - 125, $now ) );
        $this->assertSame( 'vor 1 Tag', expSubitemsColumn::formatAge( $now - 86400, $now ) );
        $this->assertSame( 'in 3 Stunden', expSubitemsColumn::formatAge( $now + 3 * 3600, $now ) );
        $this->assertSame( 'gerade eben', expSubitemsColumn::formatAge( $now - 20, $now ) );
    }

    /** ST-04 */
    public function testTheRemoveConfirmationCountsSubItemsInGerman()
    {
        $german = self::messages( 'ger-DE', 'design/admin/content/removeobject' );
        $this->assertSame( array( '%child_count Element' ), $german['%child_count item'] );
        $this->assertSame( array( '%child_count Elemente' ), $german['%child_count items'] );
        $template = (string)file_get_contents( self::root() . '/design/admin4/templates/node/removeobject.tpl' );
        $this->assertStringContainsString( "'%child_count items'\n         |i18n( 'design/admin/content/removeobject'", $template );
    }
}
