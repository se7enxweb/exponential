<?php
/**
 * Tests of eZSiteAccess::match(), the choice of the siteaccess for a request from site.ini: the static match, the
 * default, and each MatchOrder probe (port, server variable, uri by map, element, text and regexp, host by map
 * (strict, or by HostMatchMethod and the method of an item), element, text and regexp, host_uri and its default by
 * the browser's languages (DefaultHostUriMatchMapItems), index file by element, text and regexp), the order of the probes, names that are not
 * in AvailableSiteAccessList, the name washing, and what each match leaves of the URI. Also matchText() and
 * matchRegexp() on their own.
 *
 * No database and no siteaccess; the settings are set in memory and put back in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZSiteAccessMatchTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->set( 'SiteAccessSettings', 'StaticMatch', '' );
        $this->set( 'SiteSettings', 'DefaultAccess', 'k1default' );
        $this->set( 'SiteAccessSettings', 'AvailableSiteAccessList', array( 'k1default', 'k1eng', 'k1ger', 'k1admin', 'k1_mobile' ) );
        $this->set( 'SiteAccessSettings', 'NormalizeSANames', 'enabled' );
        $this->set( 'SiteAccessSettings', 'RedirectOnNormalize', 'disabled' );
    }

    protected function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        unset( $_SERVER['K1_SITEACCESS'], $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
    }

    private function set( $group, $name, $value )
    {
        ezpINIHelper::setINISetting( 'site.ini', $group, $name, $value );
    }

    private function match( $uriString, $host = 'www.example.invalid', $port = 80, $file = '/index.php' )
    {
        $uri = new eZURI( $uriString );
        $access = eZSiteAccess::match( $uri, $host, $port, $file );
        $access['rest'] = $uri->elements();
        return $access;
    }

    private function assertAccess( $name, $type, $access, $rest = null, $uriPart = null )
    {
        $this->assertSame( $name, $access['name'], 'name' );
        $this->assertSame( $type, $access['type'], 'type' );
        if ( $rest !== null )
            $this->assertSame( $rest, $access['rest'], 'what is left of the uri' );
        if ( $uriPart !== null )
            $this->assertSame( $uriPart, $access['uri_part'], 'uri part' );
    }

    public function testStaticMatchWinsOverEverything()
    {
        $this->set( 'SiteAccessSettings', 'StaticMatch', 'k1ger' );
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'uri' );
        $this->assertAccess( 'k1ger', eZSiteAccess::TYPE_STATIC, $this->match( 'k1eng/content' ), 'k1eng/content', array() );
    }

    public function testNoMatchOrderGivesTheDefault()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'none' );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_DEFAULT, $this->match( 'k1eng/content' ), 'k1eng/content' );
    }

    public function testPort()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'port' );
        $this->set( 'PortAccessSettings', '8081', 'k1admin' );
        $this->assertAccess( 'k1admin', eZSiteAccess::TYPE_PORT, $this->match( 'a', 'h', 8081 ) );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_DEFAULT, $this->match( 'a', 'h', 8082 ) );
    }

    public function testServerVariable()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'servervar' );
        $this->set( 'SiteAccessSettings', 'ServerVariableName', 'K1_SITEACCESS' );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_DEFAULT, $this->match( 'a' ) );
        $_SERVER['K1_SITEACCESS'] = 'k1ger';
        $this->assertAccess( 'k1ger', eZSiteAccess::TYPE_SERVER_VAR, $this->match( 'a' ) );
    }

    public function testUriElement()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'uri' );
        $this->set( 'SiteAccessSettings', 'URIMatchType', 'element' );
        $this->set( 'SiteAccessSettings', 'URIMatchElement', '1' );
        $this->assertAccess( 'k1eng', eZSiteAccess::TYPE_URI, $this->match( 'k1eng/content/view/full/2' ), 'content/view/full/2', array( 'k1eng' ) );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_URI, $this->match( 'nosuchaccess/content' ), 'nosuchaccess/content', array() );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_URI, $this->match( '' ), '' );
    }

    public function testUriTwoElementsAreJoinedWithUnderscore()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'uri' );
        $this->set( 'SiteAccessSettings', 'URIMatchType', 'element' );
        $this->set( 'SiteAccessSettings', 'URIMatchElement', '2' );
        $this->assertAccess( 'k1_mobile', eZSiteAccess::TYPE_URI, $this->match( 'k1/mobile/news' ), 'news', array( 'k1', 'mobile' ) );
    }

    public function testUriElementNamesAreWashed()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'uri' );
        $this->set( 'SiteAccessSettings', 'URIMatchType', 'element' );
        $this->set( 'SiteAccessSettings', 'URIMatchElement', '1' );
        $this->assertAccess( 'k1_mobile', eZSiteAccess::TYPE_URI, $this->match( 'k1-mobile/news' ), 'news' );

        $this->set( 'SiteAccessSettings', 'NormalizeSANames', 'disabled' );
        $this->assertSame( 'k1-mobile', $this->match( 'k1-mobile/news' )['name'], 'without normalizing the name is kept as given' );
    }

    public function testUriMap()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'uri' );
        $this->set( 'SiteAccessSettings', 'URIMatchType', 'map' );
        $this->set( 'SiteAccessSettings', 'URIMatchMapItems', array( 'en;k1eng', 'de;k1ger', 'xx;notavailable' ) );
        $this->assertAccess( 'k1ger', eZSiteAccess::TYPE_URI, $this->match( 'de/produkte' ), 'produkte', array( 'de' ) );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_URI, $this->match( 'xx/a' ), 'xx/a' );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_URI, $this->match( 'fr/a' ), 'fr/a' );
    }

    public function testUriText()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'uri' );
        $this->set( 'SiteAccessSettings', 'URIMatchType', 'text' );
        $this->set( 'SiteAccessSettings', 'URIMatchSubtextPre', 'site-' );
        $this->set( 'SiteAccessSettings', 'URIMatchSubtextPost', '/' );
        $this->assertAccess( 'k1eng', eZSiteAccess::TYPE_URI, $this->match( 'site-k1eng/news' ), 'news' );
    }

    public function testUriRegexp()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'uri' );
        $this->set( 'SiteAccessSettings', 'URIMatchType', 'regexp' );
        $this->set( 'SiteAccessSettings', 'URIMatchRegexp', '^(k1[a-z]+)/' );
        $this->set( 'SiteAccessSettings', 'URIMatchRegexpItem', '1' );
        $this->assertAccess( 'k1ger', eZSiteAccess::TYPE_URI, $this->match( 'k1ger/news' ), 'news' );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_URI, $this->match( 'news/k1ger' ), 'news/k1ger' );
    }

    public function testHostElement()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'host' );
        $this->set( 'SiteAccessSettings', 'HostMatchType', 'element' );
        $this->set( 'SiteAccessSettings', 'HostMatchElement', '0' );
        $this->assertAccess( 'k1ger', eZSiteAccess::TYPE_HTTP_HOST, $this->match( 'a/b', 'k1ger.example.invalid' ), 'a/b' );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_DEFAULT, $this->match( 'a/b', 'www.example.invalid' ) );
    }

    public function testHostMap()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'host' );
        $this->set( 'SiteAccessSettings', 'HostMatchType', 'map' );
        $this->set( 'SiteAccessSettings', 'HostMatchMapItems', array( 'admin.example.invalid;k1admin', 'de.example.invalid;k1ger' ) );
        $this->assertAccess( 'k1admin', eZSiteAccess::TYPE_HTTP_HOST, $this->match( 'x', 'admin.example.invalid' ), 'x' );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_DEFAULT, $this->match( 'x', 'ADMIN.example.invalid.other' ) );
    }

    /** A test host that only begins with the listed one matches with HostMatchMethod=start */
    public function testHostMapMatchesTheStartOfTheHostWhenAsked()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'host' );
        $this->set( 'SiteAccessSettings', 'HostMatchType', 'map' );
        $this->set( 'SiteAccessSettings', 'HostMatchMapItems', array( 'admin.example.invalid;k1admin', 'de.example.invalid;k1ger' ) );
        $this->set( 'SiteAccessSettings', 'HostMatchMethod', 'start' );
        $this->assertAccess( 'k1admin', eZSiteAccess::TYPE_HTTP_HOST, $this->match( 'x', 'admin.example.invalid.test.local' ), 'x' );
        $this->assertAccess( 'k1ger', eZSiteAccess::TYPE_HTTP_HOST, $this->match( 'x', 'de.example.invalid' ) );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_DEFAULT, $this->match( 'x', 'www.admin.example.invalid' ) );
    }

    /** The third field of an item sets its own method; the others stay strict */
    public function testHostMapItemCarriesItsOwnMethod()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'host' );
        $this->set( 'SiteAccessSettings', 'HostMatchType', 'map' );
        $this->set( 'SiteAccessSettings', 'HostMatchMethod', 'strict' );
        $this->set( 'SiteAccessSettings', 'HostMatchMapItems', array( 'admin.example.invalid;k1admin', 'example.invalid;k1ger;end', ';k1eng;part' ) );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_DEFAULT, $this->match( 'x', 'admin.example.invalid.test.local' ) );
        $this->assertAccess( 'k1ger', eZSiteAccess::TYPE_HTTP_HOST, $this->match( 'x', 'de.example.invalid' ) );
        $this->assertAccess( 'k1admin', eZSiteAccess::TYPE_HTTP_HOST, $this->match( 'x', 'admin.example.invalid' ) );
    }

    public static function hostMatchProvider()
    {
        return array(
            array( 'www.example.com', 'www.example.com', 'strict', true ),
            array( 'www.example.com.test.local', 'www.example.com', 'strict', false ),
            array( 'www.example.com.test.local', 'www.example.com', 'start', true ),
            array( 'test.www.example.com', 'www.example.com', 'start', false ),
            array( 'test.www.example.com', 'www.example.com', 'end', true ),
            array( 'www.example.com.test', 'www.example.com', 'end', false ),
            array( 'a.www.example.com.b', 'www.example.com', 'part', true ),
            array( 'www.example.org', 'www.example.com', 'part', false ),
            array( 'www.example.com', 'www.example.com', 'begins', false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider( 'hostMatchProvider' )]
    public function testHostMatches( $host, $matchHost, $method, $expected )
    {
        $this->assertSame( $expected, eZSiteAccess::hostMatches( $host, $matchHost, $method ) );
    }

    private function defaultHostUri()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'host_uri' );
        $this->set( 'SiteAccessSettings', 'HostUriMatchMethodDefault', 'strict' );
        $this->set( 'SiteAccessSettings', 'HostUriMatchMapItems', array( 'www.example.invalid;ger;k1ger', 'www.example.invalid;eng;k1eng' ) );
        $this->set( 'SiteAccessSettings', 'DefaultHostUriMatchMapItems', array( 'www.example.invalid;ger;k1ger;default;de',
                                                                                'www.example.invalid;eng;k1eng;;en',
                                                                                'www.example.invalid;eng;k1eng' ) );
    }

    /** An address with a language segment is matched by HostUriMatchMapItems, whatever the browser wants */
    public function testHostUriWithSegmentIgnoresTheBrowserLanguage()
    {
        $this->defaultHostUri();
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-DE,de;q=0.9';
        $access = $this->match( 'eng/content/view/full/2' );
        $this->assertAccess( 'k1eng', eZSiteAccess::TYPE_HTTP_HOST_URI, $access, 'content/view/full/2', array( 'eng' ) );
        $this->assertArrayNotHasKey( 'vary', $access );
    }

    /** Without segment the browser's most wanted language chooses, and the links get its segment */
    public function testHostUriDefaultFollowsTheBrowserLanguage()
    {
        $this->defaultHostUri();
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-CH,de;q=0.9,en;q=0.8';
        $access = $this->match( 'content/view/full/2' );
        $this->assertAccess( 'k1ger', eZSiteAccess::TYPE_HTTP_HOST_URI, $access, 'content/view/full/2', array( 'ger' ) );
        $this->assertSame( 'Accept-Language', $access['vary'] );
        $this->assertTrue( $access['redirect'], 'an entry of HostUriMatchMapItems takes ger, so the kernel sends the browser there' );

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'fr;q=0.9, en-GB;q=0.95, de;q=0.1';
        $this->assertAccess( 'k1eng', eZSiteAccess::TYPE_HTTP_HOST_URI, $this->match( '' ), '', array( 'eng' ) );
    }

    /** A language the entries do not name, or none at all, takes the entry without a language */
    public function testHostUriDefaultWithoutAMatchingLanguage()
    {
        $this->defaultHostUri();
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'fr-FR,fr;q=0.9,de;q=0';
        $this->assertAccess( 'k1eng', eZSiteAccess::TYPE_HTTP_HOST_URI, $this->match( '' ), '', array( 'eng' ) );
        unset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
        $this->assertAccess( 'k1eng', eZSiteAccess::TYPE_HTTP_HOST_URI, $this->match( '' ), '', array( 'eng' ) );
        // Another host has no default entry
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_DEFAULT, $this->match( '', 'other.example.invalid' ) );
    }

    /**
     * A German browser that switched to English stays in English: only the address without segment follows the
     * browser; every address with a segment, the start page of a language (/eng) among them, keeps its language and
     * is never sent on
     */
    public function testAfterSwitchingTheLanguageTheSegmentDecides()
    {
        $this->defaultHostUri();
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-DE,de;q=0.9';

        // The first visit: / follows the browser to German
        $first = $this->match( '' );
        $this->assertAccess( 'k1ger', eZSiteAccess::TYPE_HTTP_HOST_URI, $first, '', array( 'ger' ) );
        $this->assertTrue( $first['redirect'] );

        // The visitor switches to English: the English pages, their links and the English start page stay English
        foreach ( array( 'eng', 'eng/news/an-article', 'eng/content/view/full/2' ) as $address )
        {
            $access = $this->match( $address );
            $this->assertSame( 'k1eng', $access['name'], $address );
            $this->assertSame( array( 'eng' ), $access['uri_part'], $address );
            $this->assertArrayNotHasKey( 'redirect', $access, "$address is not sent on" );
            $this->assertArrayNotHasKey( 'vary', $access, "$address does not depend on the browser" );
        }

        // Only the address without segment asks the browser again
        $this->assertSame( 'k1ger', $this->match( 'news/an-article' )['name'] );
        $this->assertTrue( $this->match( 'news/an-article' )['redirect'] );
    }

    /** Without an entry for the segment the redirect would come back: the page is shown in place */
    public function testHostUriDefaultRedirectsOnlyWhereAnEntryTakesTheSegment()
    {
        $this->defaultHostUri();
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de';
        $this->set( 'SiteAccessSettings', 'HostUriMatchMapItems', array( 'www.example.invalid;eng;k1eng' ) );
        $this->assertFalse( $this->match( '' )['redirect'] );

        $this->set( 'SiteAccessSettings', 'HostUriMatchMapItems', array( 'www.example.invalid;ger;k1ger' ) );
        $this->assertTrue( $this->match( '' )['redirect'] );

        $this->set( 'SiteAccessSettings', 'DefaultHostUriRedirect', 'disabled' );
        $this->assertArrayNotHasKey( 'redirect', $this->match( '' ) );
    }

    public function testHostUriEntryExists()
    {
        $items = array( array( 'www.example.invalid', 'ger', 'k1ger' ), array( 'example.invalid', 'eng/sub', 'k1eng', 'start' ) );
        $this->assertTrue( eZSiteAccess::hostUriEntryExists( $items, 'www.example.invalid', 'ger', 'strict' ) );
        $this->assertFalse( eZSiteAccess::hostUriEntryExists( $items, 'www.example.invalid.test', 'ger', 'strict' ) );
        $this->assertTrue( eZSiteAccess::hostUriEntryExists( $items, 'www.example.invalid.test', 'ger', 'start' ) );
        $this->assertTrue( eZSiteAccess::hostUriEntryExists( $items, 'example.invalid.test', 'eng/sub', 'strict' ) );
        $this->assertFalse( eZSiteAccess::hostUriEntryExists( $items, 'www.example.invalid', 'eng', 'strict' ) );
    }

    public static function languageRedirectProvider()
    {
        return array(
            'the start page' => array( '/ger', '', '', '/ger' ),
            'a URL alias stays one' => array( '/ger', '/news/ein-artikel', '', '/ger/news/ein-artikel' ),
            'with the index file' => array( '/index.php/ger', '/content/view/full/2', '', '/index.php/ger/content/view/full/2' ),
            'the query goes along' => array( '/ger/', '/suche', '?SearchText=x&page=2', '/ger/suche?SearchText=x&page=2' ),
            'a query without path' => array( '/eng/sub', '', '?a=1', '/eng/sub?a=1' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider( 'languageRedirectProvider' )]
    public function testLanguageRedirectURI( $indexDir, $requestURI, $query, $expected )
    {
        $this->assertSame( $expected, ezpKernelWeb::languageRedirectURI( $indexDir, $requestURI, $query ) );
    }

    public static function acceptLanguageProvider()
    {
        return array(
            array( 'de-DE,de;q=0.9,en;q=0.8', array( 'de-de', 'de', 'en' ) ),
            array( 'fr;q=0.1, EN-gb;q=0.95 , de', array( 'de', 'en-gb', 'fr' ) ),
            array( 'en;q=0, *;q=0.5, de;q=0.5', array( 'de' ) ),
            array( '', array() ),
            array( 'de;q=abc, x<y>', array( 'de' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider( 'acceptLanguageProvider' )]
    public function testAcceptedLanguages( $header, $expected )
    {
        $this->assertSame( $expected, eZSiteAccess::acceptedLanguages( $header ) );
    }

    public function testDefaultHostUriEntries()
    {
        $items = array( array( 'example.invalid', 'ger', 'k1ger', 'start', 'de' ),
                        array( 'example.invalid', 'eng/sub', 'k1eng', 'start' ),
                        array( '', 'x', 'k1admin' ),
                        array( 'example.invalid', 'y' ) );
        $this->assertSame( array( 'name' => 'k1ger', 'uri_part' => array( 'ger' ), 'vary' => 'Accept-Language' ),
                           eZSiteAccess::matchDefaultHostUri( $items, 'example.invalid.test', 'strict', 'de' ) );
        $this->assertSame( array( 'name' => 'k1eng', 'uri_part' => array( 'eng', 'sub' ), 'vary' => 'Accept-Language' ),
                           eZSiteAccess::matchDefaultHostUri( $items, 'example.invalid.test', 'strict', 'it' ) );
        // Without language entries for the host the answer does not vary
        $this->assertSame( array( 'name' => 'k1eng', 'uri_part' => array( 'eng', 'sub' ) ),
                           eZSiteAccess::matchDefaultHostUri( array( $items[1] ), 'example.invalid', 'strict', 'de' ) );
        // Only language entries, none accepted: no default
        $this->assertNull( eZSiteAccess::matchDefaultHostUri( array( $items[0] ), 'example.invalid', 'strict', 'en' ) );
        $this->assertNull( eZSiteAccess::matchDefaultHostUri( $items, 'other.invalid', 'strict', 'de' ) );
    }

    /** The role-aware HTTP cache does not store a page whose siteaccess the browser's language chose */
    public function testHttpCacheDoesNotStoreAPageChosenByLanguage()
    {
        $reason = new ReflectionMethod( 'ezpHttpCacheListener', 'uncacheableReason' );
        $hadAccess = array_key_exists( 'eZCurrentAccess', $GLOBALS );
        $access = $hadAccess ? $GLOBALS['eZCurrentAccess'] : null;
        try
        {
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'k1ger', 'type' => eZSiteAccess::TYPE_HTTP_HOST_URI, 'vary' => 'Accept-Language' );
            $this->assertSame( 'varies by Accept-Language', $reason->invoke( null, '<html></html>' ) );
            unset( $GLOBALS['eZCurrentAccess']['vary'] );
            $this->assertNull( $reason->invoke( null, '<html></html>' ) );
        }
        finally
        {
            if ( $hadAccess )
                $GLOBALS['eZCurrentAccess'] = $access;
            else
                unset( $GLOBALS['eZCurrentAccess'] );
        }
    }

    public function testHostText()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'host' );
        $this->set( 'SiteAccessSettings', 'HostMatchType', 'text' );
        $this->set( 'SiteAccessSettings', 'HostMatchSubtextPre', 'www.' );
        $this->set( 'SiteAccessSettings', 'HostMatchSubtextPost', '.example.invalid' );
        $this->assertAccess( 'k1eng', eZSiteAccess::TYPE_HTTP_HOST, $this->match( 'x', 'www.k1eng.example.invalid' ) );
    }

    public function testHostRegexp()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'host' );
        $this->set( 'SiteAccessSettings', 'HostMatchType', 'regexp' );
        $this->set( 'SiteAccessSettings', 'HostMatchRegexp', '^(k1[a-z]+)\.example' );
        $this->set( 'SiteAccessSettings', 'HostMatchRegexpItem', '1' );
        $this->assertAccess( 'k1admin', eZSiteAccess::TYPE_HTTP_HOST, $this->match( 'x', 'k1admin.example.invalid' ) );
    }

    public function testIndexFile()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'index' );
        $this->set( 'SiteAccessSettings', 'IndexMatchType', 'element' );
        $this->set( 'SiteAccessSettings', 'IndexMatchElement', '1' );
        $this->assertAccess( 'k1ger', eZSiteAccess::TYPE_INDEX_FILE, $this->match( 'x', 'h', 80, 'index_k1ger.php' ) );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_DEFAULT, $this->match( 'x', 'h', 80, 'index.html' ) );

        $this->set( 'SiteAccessSettings', 'IndexMatchType', 'regexp' );
        $this->set( 'SiteAccessSettings', 'IndexMatchRegexp', 'index_([a-z0-9]+)\.php' );
        $this->set( 'SiteAccessSettings', 'IndexMatchRegexpItem', '1' );
        $this->assertAccess( 'k1eng', eZSiteAccess::TYPE_INDEX_FILE, $this->match( 'x', 'h', 80, '/index_k1eng.php' ) );
    }

    public function testProbesAreTriedInOrder()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'port;host;uri' );
        $this->set( 'SiteAccessSettings', 'HostMatchType', 'map' );
        $this->set( 'SiteAccessSettings', 'HostMatchMapItems', array( 'admin.example.invalid;k1admin' ) );
        $this->set( 'SiteAccessSettings', 'URIMatchType', 'element' );
        $this->set( 'SiteAccessSettings', 'URIMatchElement', '1' );
        $this->assertAccess( 'k1admin', eZSiteAccess::TYPE_HTTP_HOST, $this->match( 'k1eng/x', 'admin.example.invalid' ), 'k1eng/x' );
        $this->assertAccess( 'k1eng', eZSiteAccess::TYPE_URI, $this->match( 'k1eng/x', 'www.example.invalid' ), 'x' );
    }

    public function testUnknownProbeIsSkipped()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'k1nosuchprobe' );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_DEFAULT, $this->match( 'x' ) );
    }

    public static function matchTextProvider()
    {
        return array(
            'pre and a one character post' => array( 'site-k1eng/news', 'site-', '/', 'k1eng', 'news' ),
            'pre only' => array( 'site-k1eng', 'site-', '', 'k1eng', '' ),
            'pre and a longer post' => array( 'www.k1eng.example.invalid', 'www.', '.example.invalid', 'k1eng', '' ),
            'text after a longer post is kept' => array( 'site-k1eng--news/x', 'site-', '--', 'k1eng', 'news/x' ),
            'post only' => array( 'k1eng.example.invalid', '', '.example.invalid', 'k1eng', '' ),
            'post missing' => array( 'site-k1eng', 'site-', '--', null, 'site-k1eng' ),
            'neither' => array( 'k1eng', '', '', null, 'k1eng' ),
            'pre missing' => array( 'k1eng', 'site-', '', null, 'k1eng' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('matchTextProvider')]
    public function testMatchText( $text, $pre, $post, $expected, $rest )
    {
        $this->assertSame( $expected, eZSiteAccess::matchText( $text, $pre, $post ) );
        $this->assertSame( $rest, $text );
    }

    public function testUriTextWithALongerPost()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'uri' );
        $this->set( 'SiteAccessSettings', 'URIMatchType', 'text' );
        $this->set( 'SiteAccessSettings', 'URIMatchSubtextPre', 'site-' );
        $this->set( 'SiteAccessSettings', 'URIMatchSubtextPost', '--' );
        $this->assertAccess( 'k1eng', eZSiteAccess::TYPE_URI, $this->match( 'site-k1eng--news' ), 'news' );
    }

    public function testMatchRegexp()
    {
        $text = 'k1eng/news';
        $this->assertSame( 'k1eng', eZSiteAccess::matchRegexp( $text, '^(k1[a-z]+)', 1 ) );
        $this->assertSame( '/news', $text );
        $text = 'abc';
        $this->assertNull( eZSiteAccess::matchRegexp( $text, '^(x)', 1 ) );
        $this->assertNull( eZSiteAccess::matchRegexp( $text, '^(a)', 2 ), 'an item number past the groups' );
        $text = 'a/b/c';
        $this->assertSame( 'a/b', eZSiteAccess::matchRegexp( $text, '^(a/b)', 1 ), 'a slash in the pattern needs no escaping' );
    }

    public function testMatchRegexpRemovesOnlyTheMatchedText()
    {
        $text = 'k1eng/k1eng-news';
        $this->assertSame( 'k1eng', eZSiteAccess::matchRegexp( $text, '^(k1[a-z]+)', 1 ) );
        $this->assertSame( '/k1eng-news', $text );
    }

    public function testUriRegexpKeepsTheRestOfTheUri()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'uri' );
        $this->set( 'SiteAccessSettings', 'URIMatchType', 'regexp' );
        $this->set( 'SiteAccessSettings', 'URIMatchRegexp', '^(k1[a-z]+)/' );
        $this->set( 'SiteAccessSettings', 'URIMatchRegexpItem', '1' );
        $this->assertAccess( 'k1eng', eZSiteAccess::TYPE_URI, $this->match( 'k1eng/about-k1eng' ), 'about-k1eng' );
    }

    public function testHostAndIndexElementsBeyondTheEnd()
    {
        $this->set( 'SiteAccessSettings', 'MatchOrder', 'host;index' );
        $this->set( 'SiteAccessSettings', 'HostMatchType', 'element' );
        $this->set( 'SiteAccessSettings', 'HostMatchElement', '5' );
        $this->set( 'SiteAccessSettings', 'IndexMatchType', 'element' );
        $this->set( 'SiteAccessSettings', 'IndexMatchElement', '3' );
        $this->assertAccess( 'k1default', eZSiteAccess::TYPE_DEFAULT, $this->match( 'x', 'localhost', 80, 'index_k1ger.php' ) );
    }
}
