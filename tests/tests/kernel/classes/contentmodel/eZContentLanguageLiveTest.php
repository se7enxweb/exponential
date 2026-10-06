<?php
/**
 * The content languages of the installation as masks and lists: ids by locale, masks of locale lists, languages
 * of a mask (by id order and by priority), the top priority language of a mask or a locale list, decoding a mask
 * (also one that names a language that no longer exists), the JavaScript list of a mask, the SQL filters and the
 * object and class counts. Nothing is written.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZContentLanguageLiveTest extends PHPUnit\Framework\TestCase
{
    /** @var eZContentLanguage[] locale => language */
    private static $languages;

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        self::$languages = array();
        foreach ( eZContentLanguage::fetchList( true ) as $language )
            self::$languages[$language->attribute( 'locale' )] = $language;
        if ( count( self::$languages ) < 2 )
            self::markTestSkipped( 'needs two content languages' );
    }

    protected function setUp(): void
    {
        parent::setUp();
        // the cron job mode (other tests switch it on) makes every language a prioritized one
        eZContentLanguage::clearCronjobMode();
        eZContentLanguage::clearPrioritizedLanguages();
    }

    protected function tearDown(): void
    {
        eZContentLanguage::clearPrioritizedLanguages();
        parent::tearDown();
    }

    private function locales()
    {
        return array_keys( self::$languages );
    }

    public function testIdsAndMasks()
    {
        $mask = 0;
        foreach ( self::$languages as $locale => $language )
        {
            $id = (int)$language->attribute( 'id' );
            $this->assertSame( $id, eZContentLanguage::idByLocale( $locale ) );
            $this->assertSame( 0, $id & ( $id - 1 ), "the id of $locale is one bit" );
            $this->assertSame( $id, eZContentLanguage::maskByLocale( $locale ) );
            $this->assertSame( $id | 1, eZContentLanguage::maskByLocale( array( $locale ), true ) );
            $this->assertSame( $locale, eZContentLanguage::fetch( $id )->attribute( 'locale' ) );
            $this->assertSame( $locale, $language->localeObject()->localeFullCode() );
            $mask |= $id;
        }
        $this->assertSame( $mask, eZContentLanguage::maskByLocale( $this->locales() ) );
        $this->assertSame( 0, eZContentLanguage::maskByLocale( array() ) );
        $this->assertSame( 0, eZContentLanguage::maskByLocale( 'k1c-XX' ) );
        $this->assertFalse( eZContentLanguage::idByLocale( 'k1c-XX' ) );
        $this->assertFalse( (bool)eZContentLanguage::fetchByLocale( 'k1c-XX' ) );
        $this->assertSame( $mask | 1, (int)eZContentLanguage::maskForRealLanguages(), 'every language and the always available bit' );
        $this->assertEqualsCanonicalizing( $this->locales(), eZContentLanguage::fetchLocaleList() );
    }

    public function testLanguagesOfAMask()
    {
        list( $first, $second ) = $this->locales();
        $mask = eZContentLanguage::maskByLocale( array( $first, $second ) );
        $this->assertEqualsCanonicalizing( array( $first, $second ), array_keys( eZContentLanguage::languagesByMask( $mask ) ) );
        $this->assertSame( array( $first ), array_keys( eZContentLanguage::languagesByMask( eZContentLanguage::idByLocale( $first ) | 1 ) ) );
        $this->assertSame( array(), eZContentLanguage::languagesByMask( 1 ) );

        eZContentLanguage::setPrioritizedLanguages( array( $second, $first ) );
        $this->assertSame( array( $second, $first ), array_keys( eZContentLanguage::prioritizedLanguagesByMask( $mask ) ) );
        $this->assertSame( $second, eZContentLanguage::topPriorityLanguageByMask( $mask )->attribute( 'locale' ) );
        $this->assertSame( $first, eZContentLanguage::topPriorityLanguageByMask( eZContentLanguage::idByLocale( $first ) )->attribute( 'locale' ) );
        $this->assertSame( $second, eZContentLanguage::topPriorityLanguage()->attribute( 'locale' ) );
        $this->assertSame( array( $second, $first ), eZContentLanguage::prioritizedLanguageCodes() );
        $this->assertSame( $first, eZContentLanguage::topPriorityLanguageByLocaleList( array( $first, 'k1c-XX' ) )->attribute( 'locale' ) );
        $this->assertFalse( eZContentLanguage::topPriorityLanguageByLocaleList( array( 'k1c-XX' ) ) );
        $this->assertFalse( eZContentLanguage::topPriorityLanguageByLocaleList( array() ) );
        $this->assertSame( array( $second, $first ), array_keys( eZContentLanguage::prioritizedLanguagesByLocaleList( array( $first, $second ) ) ) );
        $this->assertSame( array(), eZContentLanguage::prioritizedLanguagesByLocaleList( 'x' ) );

        $js = json_decode( eZContentLanguage::jsArrayByMask( $mask ), true );
        $this->assertSame( array( $second, $first ), array_column( $js, 'locale' ) );
    }

    public function testDecodeLanguageMask()
    {
        list( $first, $second ) = $this->locales();
        $a = eZContentLanguage::idByLocale( $first );
        $b = eZContentLanguage::idByLocale( $second );
        $decoded = eZContentLanguage::decodeLanguageMask( $a | $b | 1 );
        $this->assertSame( 1, $decoded['always_available'] );
        $this->assertEqualsCanonicalizing( array( $a, $b ), $decoded['language_list'] );
        $decoded = eZContentLanguage::decodeLanguageMask( $a | $b, true );
        $this->assertSame( 0, $decoded['always_available'] );
        $this->assertEqualsCanonicalizing( array( $first, $second ), $decoded['language_list'] );
    }

    public function testDecodingABitOfALanguageThatIsGone()
    {
        // a bit no language has (left in object masks after a language was removed)
        $unused = 0;
        for ( $bit = 2; $bit < ( 1 << 30 ); $bit <<= 1 )
        {
            if ( !eZContentLanguage::fetch( $bit ) )
            {
                $unused = $bit;
                break;
            }
        }
        $this->assertNotSame( 0, $unused );
        $first = $this->locales()[0];
        $mask = eZContentLanguage::idByLocale( $first ) | $unused;
        $ids = eZContentLanguage::decodeLanguageMask( $mask );
        $this->assertContains( $unused, $ids['language_list'], 'the bit is reported as it is stored' );
        $locales = eZContentLanguage::decodeLanguageMask( $mask, true );
        $this->assertSame( array( $first ), $locales['language_list'], 'a locale only for a language that exists' );
    }

    public function testSqlFiltersAndCounts()
    {
        $filter = eZContentLanguage::languagesSQLFilter( 'ezcontentobject' );
        $this->assertMatchesRegularExpression( '/ezcontentobject\.language_mask & \d+ > 0|bitand\( ezcontentobject\.language_mask, \d+ \) > 0/', trim( $filter ) );
        $sql = eZContentLanguage::sqlFilter( 'ezcontentobject_name', 'ezcontentobject' );
        $this->assertStringContainsString( 'ezcontentobject_name.language_id', $sql );
        $rows = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject WHERE ' . $filter );
        $this->assertGreaterThan( 0, (int)$rows[0]['c'], 'the filter is valid SQL that finds objects' );

        $language = self::$languages[eZContentLanguage::topPriorityLanguage()->attribute( 'locale' )];
        $this->assertGreaterThan( 0, (int)$language->objectCount() );
        $this->assertGreaterThanOrEqual( 0, (int)$language->classCount() );
        $this->assertGreaterThanOrEqual( 0, (int)$language->objectInitialCount() );
    }
}
