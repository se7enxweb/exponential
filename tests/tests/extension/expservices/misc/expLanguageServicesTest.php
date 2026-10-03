<?php
require_once __DIR__ . '/../media/expMediaTestCase.php';

/** explanguage: content languages, masks, locales and translation files. */
class expLanguageServicesTest extends expMediaTestCase
{
    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expLanguageServices', 14 );
    }

    public function testLanguagesCountObjects()
    {
        $r = $this->ok( 'expLanguageServices', 'languages' );
        $this->assertNotEmpty( $r['data'] );
        $this->assertArrayHasKey( 'object_count', $r['data'][0] );
    }

    public function testOneLanguageAndTheTop()
    {
        $top = $this->ok( 'expLanguageServices', 'top' )['data'];
        $one = $this->ok( 'expLanguageServices', 'language', array( $top['locale'] ) )['data'];
        $this->assertSame( $top['id'], $one['id'] );
        $this->fails( 404, 'expLanguageServices', 'language', array( 'xxx-XX' ) );
        $this->assertSame( $top['locale'], $this->ok( 'expLanguageServices', 'prioritized' )['data'][0]['locale'] );
    }

    public function testMaskRoundTrip()
    {
        $top = $this->ok( 'expLanguageServices', 'top' )['data'];
        $mask = $this->ok( 'expLanguageServices', 'maskfor', array( $top['locale'], 'false' ) )['data']['mask'];
        $d = $this->ok( 'expLanguageServices', 'decode', array( $mask ) )['data'];
        $this->assertSame( array( $top['locale'] ), $d['locales'] );
        $this->assertFalse( $d['always_available'] );
        $this->assertTrue( $this->ok( 'expLanguageServices', 'decode', array( $mask + 1 ) )['data']['always_available'] );
    }

    public function testMaskForUnknownLocaleIs404()
    {
        $this->fails( 404, 'expLanguageServices', 'maskfor', array( 'xxx-XX' ) );
        $this->fails( 400, 'expLanguageServices', 'decode', array( -4 ) );
    }

    public function testLocalesArePaged()
    {
        $r = $this->ok( 'expLanguageServices', 'locales', array( 10, 0 ) );
        $this->assertPaged( $r );
        $this->assertGreaterThan( 10, $r['meta']['total'] );
    }

    public function testLocaleInfo()
    {
        $l = $this->ok( 'expLanguageServices', 'localeinfo', array( 'eng-GB' ) )['data'];
        $this->assertSame( 'eng-GB', $l['locale'] );
        $this->assertSame( 'eng', $l['language_code'] );
        $this->assertNotSame( '', $l['currency_symbol'] );
    }

    public function testLocaleInfoValidatesTheCode()
    {
        $this->fails( 400, 'expLanguageServices', 'localeinfo', array( '../etc' ) );
        $this->fails( 404, 'expLanguageServices', 'localeinfo', array( 'zzz-ZZ' ) );
    }

    public function testCountries()
    {
        $this->assertContains( 'GB', $this->ok( 'expLanguageServices', 'countries' )['data'] );
    }

    public function testTranslationsList()
    {
        $list = $this->ok( 'expLanguageServices', 'translations' )['data'];
        $this->assertContains( 'ger-DE', array_column( $list, 'locale' ) );
    }

    public function testTranslationStats()
    {
        $s = $this->ok( 'expLanguageServices', 'translationstats', array( 'ger-DE' ) )['data'];
        $this->assertGreaterThan( 1000, $s['messages'] );
        $this->assertLessThanOrEqual( 100, $s['percent'] );
        $this->assertSame( $s['messages'], $s['finished'] + $s['unfinished'] + $s['obsolete'] );
    }

    public function testTranslationFileNamesAreValidated()
    {
        $this->fails( 400, 'expLanguageServices', 'translationstats', array( '../../settings' ) );
        $this->fails( 404, 'expLanguageServices', 'translationstats', array( 'zzz-ZZ' ) );
    }

    public function testTranslationContextsArePaged()
    {
        $r = $this->ok( 'expLanguageServices', 'translationcontexts', array( 'ger-DE', 5, 0 ) );
        $this->assertPaged( $r );
        $this->assertArrayHasKey( 'context', $r['data'][0] );
    }

    public function testSiteaccessAndObjectCounts()
    {
        $s = $this->ok( 'expLanguageServices', 'siteaccess' )['data'];
        $this->assertNotEmpty( $s['content_languages'] );
        $this->assertNotEmpty( $this->ok( 'expLanguageServices', 'objectcounts' )['data'] );
    }

    public function testPublicLanguagesForAnonymous()
    {
        $this->loginAnonymous();
        $this->assertTrue( $this->call( 'expLanguageServices', 'languages' )['ok'] );
        $this->assertFalse( $this->call( 'expLanguageServices', 'translations' )['ok'] );
    }
}
