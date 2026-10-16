<?php
/**
 * The interface language list (#516): the language provider's groups from settings, the A-Z list (Intl collation and
 * the fallback without it), the allow-list built from the groups, AllowedValues[] and every locale, the switch for
 * untranslated languages, and the catalogue's translation coverage and its cache. Without a database: a fixture
 * installation in var/tmp with its own share/locale and share/translations, and the real one for the shipped list.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/uiswitch/expUISwitchLanguageProviderTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$expUISwitchRoot = dirname( __DIR__, 5 );
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitchproviderinterface.php';
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitchprovider.php';
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitchlanguagecatalogue.php';
require_once $expUISwitchRoot . '/kernel/classes/uiswitch/expuiswitchlanguageprovider.php';
unset( $expUISwitchRoot );

class expUISwitchLanguageProviderTest extends PHPUnit\Framework\TestCase
{
    /** @var string the repository */
    protected $root;

    /** @var string the fixture installation */
    protected $fixture;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 5 );
        $this->fixture = $this->root . '/var/tmp/uiswitch-language-test-' . getmypid() . '-' . mt_rand();
        expUISwitchLanguageCatalogue::resetMemory();
        $this->locale( 'eng-GB', 'English (United Kingdom)', 'English (United Kingdom)', 'en-GB' );
        $this->locale( 'eng-US', 'English (American)', 'English (American)', 'en-US' );
        $this->locale( 'ger-DE', 'Deutsch (Deutschland)', 'German', 'de-DE' );
        $this->locale( 'fre-FR', 'Français (France)', 'French (France)', 'fr-FR' );
        $this->locale( 'swa-KE', 'Kiswahili (Kenya)', 'Swahili (Kenya)', 'sw-KE' );
        $this->locale( 'cze-CZ', 'Čeština', 'Czech', 'cs-CZ' );
        $this->locale( 'ukr-UA', 'Ukrainian', 'Ukrainian', 'uk-UA' );
        $this->locale( 'eng-GB@euro', '', '', '' );
        $this->messages( 'untranslated', array( 'a' => '', 'b' => '', 'c' => '', 'd' => '' ), array( 'gone' => 'obsolete' ) );
        $this->messages( 'ger-DE', array( 'a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'old' => 'Alt' ) );
        $this->messages( 'fre-FR', array( 'a' => 'A', 'b' => '', 'c' => 'C' ), array( 'c' => 'unfinished', 'gone' => 'obsolete' ) );
    }

    protected function tearDown(): void
    {
        expUISwitchLanguageCatalogue::resetMemory();
        $this->removeTree( $this->fixture );
    }

    protected function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( scandir( $dir ) as $name )
        {
            if ( $name === '.' || $name === '..' )
                continue;
            $path = $dir . '/' . $name;
            is_dir( $path ) && !is_link( $path ) ? $this->removeTree( $path ) : unlink( $path );
        }
        rmdir( $dir );
    }

    protected function locale( $code, $native, $english, $http )
    {
        if ( !is_dir( $this->fixture . '/share/locale' ) )
            mkdir( $this->fixture . '/share/locale', 0777, true );
        $text = "#?ini charset=\"utf-8\"?\n[RegionalSettings]\n";
        if ( $native !== '' )
            $text .= "LanguageName=$native\nInternationalLanguageName=$english\n";
        if ( $http !== '' )
            $text .= "\n[HTTP]\nContentLanguage=$http\n";
        file_put_contents( $this->fixture . '/share/locale/' . $code . '.ini', $text );
    }

    /** a translation.ts of one context "ctx": source => translation, and source => type */
    protected function messages( $locale, array $messages, array $types = array() )
    {
        $dir = $this->fixture . '/share/translations/' . $locale;
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!DOCTYPE TS>\n<TS version=\"2.0\">\n<context>\n    <name>ctx</name>\n";
        foreach ( $messages + array_fill_keys( array_keys( array_diff_key( $types, $messages ) ), '' ) as $source => $translation )
        {
            $type = isset( $types[$source] ) ? ' type="' . $types[$source] . '"' : '';
            $xml .= "    <message>\n        <source>$source</source>\n        <translation$type>$translation</translation>\n    </message>\n";
        }
        $xml .= "</context>\n</TS>\n";
        file_put_contents( $dir . '/translation.ts', $xml );
    }

    protected function catalogue( $cache = '' )
    {
        return new expUISwitchLanguageCatalogue( $this->fixture, $cache );
    }

    protected function provider( array $settings, $default = 'eng-GB', $cache = '' )
    {
        return new expUISwitchLanguageProvider( 'language', $settings, $default, $this->fixture, $this->catalogue( $cache ) );
    }

    protected function grouped( array $extra = array() )
    {
        return $extra + array( 'GroupList' => array( 'english', 'major', 'Bad-Name', 'empty' ),
                               'GroupLabel' => array( 'english' => 'English', 'major' => 'Major world languages' ),
                               'GroupValues_english' => array( 'eng-US', 'eng-GB', 'eng-US' ),
                               'GroupValues_major' => array( 'ger-DE', 'fre-FR', 'swa-KE', 'xxx-XX', '../../x' ),
                               'GroupValues_empty' => array( 'zzz-ZZ' ),
                               'AllLanguages' => 'enabled', 'ListUntranslated' => 'enabled' );
    }

    // ------------------------------------------------------------------ coverage

    public function testCoverageCountsTranslatedSourceMessagesOnly()
    {
        $entries = $this->catalogue()->entries();
        $this->assertSame( 100, $entries['ger-DE']['coverage'], 'every source message; an extra message does not count' );
        $this->assertSame( 25, $entries['fre-FR']['coverage'], 'empty, unfinished and obsolete translations do not count' );
        $this->assertNull( $entries['swa-KE']['coverage'], 'no translation file: no translation' );
        $this->assertSame( 100, $entries['eng-US']['coverage'], 'English is the language of the source texts' );
        $this->assertSame( 'Deutsch (Deutschland)', $entries['ger-DE']['native'] );
        $this->assertSame( 'German', $entries['ger-DE']['english'] );
        $this->assertSame( 'de-DE', $entries['ger-DE']['http'] );
        $this->assertArrayHasKey( 'eng-GB@euro', $entries, 'variants are known, for a value named in the settings' );
    }

    public function testCoveragePercentRoundsDownSoOnlyCompleteSays100()
    {
        $source = array_fill_keys( range( 1, 1000 ), true );
        $this->assertSame( 99, expUISwitchLanguageCatalogue::coveragePercent( $source, array_fill_keys( range( 1, 999 ), true ) ) );
        $this->assertSame( 100, expUISwitchLanguageCatalogue::coveragePercent( $source, $source ) );
        $this->assertSame( 0, expUISwitchLanguageCatalogue::coveragePercent( $source, array() ) );
        $this->assertSame( 100, expUISwitchLanguageCatalogue::coveragePercent( array(), array( 'x' => true ) ) );
    }

    public function testCatalogueIsCachedAndBuiltAgainWhenAFileChanges()
    {
        $cache = $this->fixture . '/cache';
        $first = $this->catalogue( $cache );
        $this->assertSame( 25, $first->entry( 'fre-FR' )['coverage'] );
        $file = $cache . '/uiswitch/language-catalogue.json';
        $this->assertFileExists( $file );
        $data = json_decode( file_get_contents( $file ), true );
        $this->assertSame( $first->signature(), $data['signature'] );

        // a new process reads the file, not the translations
        expUISwitchLanguageCatalogue::resetMemory();
        $data['entries']['fre-FR']['coverage'] = 77;
        file_put_contents( $file, json_encode( $data ) );
        $this->assertSame( 77, $this->catalogue( $cache )->entry( 'fre-FR' )['coverage'], 'read from the cache file' );

        // a changed translation changes the signature: built again
        expUISwitchLanguageCatalogue::resetMemory();
        $this->messages( 'fre-FR', array( 'a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D' ) );
        touch( $this->fixture . '/share/translations/fre-FR/translation.ts', time() + 5 );
        $this->assertSame( 100, $this->catalogue( $cache )->entry( 'fre-FR' )['coverage'] );

        // a cache file that is not ours is ignored
        expUISwitchLanguageCatalogue::resetMemory();
        file_put_contents( $file, '{"signature":"x","entries":{"fre-FR":{"coverage":1}}}' );
        $this->assertSame( 100, $this->catalogue( $cache )->entry( 'fre-FR' )['coverage'] );
        $this->assertNull( $this->catalogue( $cache )->entry( '../x' ) );
    }

    // ------------------------------------------------------------------ allow-list

    public function testAllowListIsGroupsAllowedValuesAndEveryLocale()
    {
        $provider = $this->provider( $this->grouped( array( 'AllowedValues' => array( 'eng-GB@euro' ) ) ) );
        $allowed = $provider->allowedValues();
        $this->assertSame( 'eng-GB', $allowed[0], 'the siteaccess Locale first' );
        foreach ( array( 'eng-US', 'ger-DE', 'fre-FR', 'swa-KE', 'cze-CZ', 'ukr-UA', 'eng-GB@euro' ) as $value )
            $this->assertTrue( $provider->isValid( $value ), $value );
        foreach ( array( 'xxx-XX', 'zzz-ZZ', '../../x', 'ger-de', 'ger-DE ', '', null, array( 'ger-DE' ) ) as $bad )
            $this->assertFalse( $provider->isValid( $bad ), var_export( $bad, true ) );
        $this->assertCount( count( array_unique( $allowed ) ), $allowed, 'each value once' );
    }

    public function testVariantsAreOfferedOnlyWhenNamed()
    {
        $provider = $this->provider( $this->grouped() );
        $this->assertFalse( $provider->isValid( 'eng-GB@euro' ), 'AllLanguages leaves the format variants out' );
    }

    public function testUntranslatedLanguagesNeedTheSwitch()
    {
        $provider = $this->provider( $this->grouped( array( 'ListUntranslated' => 'disabled' ) ) );
        $this->assertFalse( $provider->isValid( 'swa-KE' ) );
        $this->assertFalse( $provider->isValid( 'cze-CZ' ) );
        $this->assertTrue( $provider->isValid( 'ger-DE' ) );
        $this->assertTrue( $provider->isValid( 'eng-US' ), 'English needs no translation' );
        $missing = $this->provider( array( 'AllowedValues' => array( 'swa-KE', 'ger-DE' ) ) );
        $this->assertSame( array( 'eng-GB', 'ger-DE' ), $missing->allowedValues(), 'off when the setting is missing, as before' );
    }

    public function testWithoutGroupsTheChoiceStaysAFlatList()
    {
        $provider = $this->provider( array( 'AllowedValues' => array( 'eng-US', 'ger-DE' ) ) );
        $this->assertFalse( $provider->isGrouped() );
        $this->assertSame( array(), $provider->groups() );
    }

    // ------------------------------------------------------------------ groups and order

    public function testGroupsKeepTheirOrderThenAllLanguagesAToZ()
    {
        $provider = $this->provider( $this->grouped() );
        $groups = $provider->groups();
        $this->assertSame( array( 'english', 'major', 'all' ), array_column( $groups, 'identifier' ), 'ill-formed and empty groups are left out' );
        $this->assertSame( array( 'eng-US', 'eng-GB' ), $groups[0]['values'], 'configured order, each once' );
        $this->assertSame( array( 'ger-DE', 'fre-FR', 'swa-KE' ), $groups[1]['values'], 'unknown locales are dropped' );
        $this->assertSame( 'Major world languages', $groups[1]['label'] );
        $this->assertSame( 'All languages (A–Z)', $groups[2]['label'] );
        $all = $groups[2]['values'];
        sort( $all );
        $expected = $provider->allowedValues();
        sort( $expected );
        $this->assertSame( $expected, $all, 'the A-Z list has every offered language' );
    }

    public function testMoreLanguagesListsOnlyWhatNoGroupHasAndTheDefault()
    {
        $provider = $this->provider( $this->grouped( array( 'AllLanguages' => 'disabled', 'AllowedValues' => array( 'cze-CZ' ) ) ) );
        $groups = $provider->groups();
        $last = end( $groups );
        $this->assertSame( 'More languages (A–Z)', $last['label'] );
        $this->assertSame( array( 'cze-CZ', 'eng-GB' ), $last['values'] );
        $this->assertFalse( $provider->isValid( 'ukr-UA' ) );
    }

    public function testLabelsShowBothNamesAndHowCompleteTheTranslationIs()
    {
        $provider = $this->provider( $this->grouped() );
        $this->assertSame( 'Deutsch (Deutschland)', $provider->label( 'ger-DE' ), 'label() stays the own name' );
        $this->assertStringStartsWith( 'Deutsch (Deutschland) — German', $provider->displayLabel( 'ger-DE', 'en-GB' ) );
        $this->assertStringEndsWith( '(100%)', $provider->displayLabel( 'ger-DE', 'en-GB' ) );
        $this->assertStringEndsWith( '(25%)', $provider->displayLabel( 'fre-FR', 'en-GB' ) );
        $this->assertStringStartsWith( 'Kiswahili (Kenya) — Swahili', $provider->displayLabel( 'swa-KE', 'en-GB' ) );
        $this->assertStringEndsWith( '(no translation yet)', $provider->displayLabel( 'swa-KE', 'en-GB' ) );
        $this->assertSame( 'English (United Kingdom) (100%)', $provider->displayLabel( 'eng-GB', 'en-GB' ), 'one name when both are the same' );
        $this->assertSame( 'xxx-XX', $provider->displayLabel( 'xxx-XX', 'en-GB' ) );
        $this->assertSame( 'de-DE', $provider->labelLanguage( 'ger-DE' ) );
    }

    public function testAFileThatOnlyRepeatsTheEnglishNameGetsTheOwnNameFromIntl()
    {
        if ( !class_exists( 'Locale' ) )
            $this->markTestSkipped( 'needs the intl extension' );
        $provider = $this->provider( $this->grouped() );
        $this->assertSame( 'Українська (Україна)', $provider->nativeName( 'ukr-UA' ) );
        $this->assertSame( 'Čeština', $provider->nativeName( 'cze-CZ' ), 'a real own name stays' );
    }

    public function testSortingUsesTheCollationOfTheReader()
    {
        $labels = array( 'b' => 'Zulu', 'a' => 'Čeština', 'c' => 'Català', 'd' => 'Deutsch', 'e' => 'english', 'f' => 'Ελληνικά' );
        $fallback = expUISwitchLanguageCatalogue::sortValuesByLabel( $labels, 'en-GB', false );
        $this->assertSame( array( 'c', 'a', 'd', 'e', 'b', 'f' ), $fallback, 'without Intl: accents and case folded, other scripts after Latin' );
        if ( !class_exists( 'Collator' ) )
            $this->markTestSkipped( 'needs the intl extension' );
        $this->assertSame( array( 'c', 'a', 'd', 'e', 'b', 'f' ), expUISwitchLanguageCatalogue::sortValuesByLabel( $labels, 'en-GB' ) );
        $czech = expUISwitchLanguageCatalogue::sortValuesByLabel( $labels, 'cs-CZ' );
        $this->assertTrue( array_search( 'a', $czech ) > array_search( 'c', $czech ) && array_search( 'a', $czech ) < array_search( 'd', $czech ),
                           'Czech sorts Č after C' );
    }

    public function testSameNamesGetTheirCodes()
    {
        $this->locale( 'srp-RS', 'Српски (Србија)', 'Serbian', 'sr-RS' );
        $this->locale( 'ser-SR', 'Српски (Србија)', 'Serbian', 'sr-RS' );
        expUISwitchLanguageCatalogue::resetMemory();
        $provider = $this->provider( $this->grouped() );
        $this->assertStringContainsString( '[srp-RS]', $provider->displayLabel( 'srp-RS', 'en-GB' ) );
        $this->assertStringContainsString( '[ser-SR]', $provider->displayLabel( 'ser-SR', 'en-GB' ) );
        $this->assertStringNotContainsString( '[', $provider->displayLabel( 'ger-DE', 'en-GB' ) );
    }

    // ------------------------------------------------------------------ the shipped settings

    public function testShippedSettingsRankAboutThirtyLanguagesAndListAll()
    {
        $cwd = getcwd();
        chdir( $this->root );
        $ini = eZINI::fetchFromFile( 'settings/uiswitch.ini' );
        chdir( $cwd );
        $settings = $ini->group( 'UISwitch_language' );
        $provider = new expUISwitchLanguageProvider( 'language', $settings, 'eng-GB', $this->root, new expUISwitchLanguageCatalogue( $this->root, '' ) );
        $this->assertSame( 'enabled', $settings['AllLanguages'] );
        $this->assertSame( 'enabled', $settings['ListUntranslated'] );
        $ranked = 0;
        foreach ( $provider->configuredGroups() as $group => $values )
        {
            $this->assertNotEmpty( $settings['GroupLabel'][$group], $group );
            foreach ( $values as $value )
                $this->assertTrue( $provider->isValid( $value ), "$group: $value has a locale file" );
            $ranked += count( $values );
        }
        $this->assertGreaterThanOrEqual( 14, $ranked );
        $this->assertLessThanOrEqual( 35, $ranked );
        $this->assertSame( array( 'eng-US', 'eng-GB' ), array_slice( $provider->configuredGroups()['english'], 0, 2 ) );
        $this->assertGreaterThan( 100, count( $provider->allowedValues() ), 'every locale of share/locale' );
    }
}
