<?php
/**
 * Where the cache preloader finds a siteaccess (expPreloadAddress), from settings handed in: no database, no
 * site.ini, no request.
 *
 *  PA-01 a SiteURL without a scheme gets https://, an empty one is no address
 *  PA-02 a siteaccess in the host map is reached at the root of its host, one matched by uri at /<name>
 *  PA-03 HostMatchMapItems with a third field: start, end and part reach the siteaccess as a request does, strict
 *        does not, and HostMatchMethod is the default of an entry without one
 *  PA-04 MatchOrder decides: a host matched before the uri sends /<name> to the host's siteaccess
 *  PA-05 URIMatchType=map and host_uri entries give their own prefixes
 *  PA-06 a SiteURL that already ends in the prefix does not get it twice
 *  PA-07 nothing reaches the siteaccess: the older prefix is kept, and the address is marked as not confirmed
 *  PA-08 the starting pages: root plus URLTranslationKeyword sections, or the paths asked for, one address each
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expPreloadAddressTest extends PHPUnit\Framework\TestCase
{
    /** alpha's own settings, with example hosts */
    private static function alpha( array $more = array() )
    {
        return new expPreloadAddress( $more + array(
            'match_order' => array( 'uri', 'host' ),
            'uri_match_type' => 'element',
            'host_match_type' => 'map',
            'host_match_map' => array( 'admin.example.test;admin', 'example.test;site', 'edit.example.test;editor' ),
            'siteaccesses' => array( 'site', 'admin', 'bold', 'bold_ger', 'editor' ),
            'default_access' => 'site',
        ) );
    }

    /** PA-01 */
    public function testBaseUrl()
    {
        $this->assertSame( 'https://example.test', expPreloadAddress::baseUrl( 'example.test' ) );
        $this->assertSame( 'https://example.test/site', expPreloadAddress::baseUrl( ' example.test/site/ ' ) );
        $this->assertSame( 'http://example.test:8088', expPreloadAddress::baseUrl( 'http://example.test:8088/' ) );
        $this->assertFalse( expPreloadAddress::baseUrl( '' ) );
        $this->assertFalse( expPreloadAddress::baseUrl( ' / ' ) );
    }

    /** PA-02 */
    public function testHostAndUriMatching()
    {
        $address = self::alpha();
        $this->assertSame( array( 'prefix' => '', 'reached' => true, 'how' => 'no_prefix' ), $address->prefix( 'site', 'https://example.test' ) );
        $this->assertSame( array( 'prefix' => '', 'reached' => true, 'how' => 'no_prefix' ), $address->prefix( 'editor', 'https://edit.example.test' ) );
        $this->assertSame( array( 'prefix' => '/bold', 'reached' => true, 'how' => 'uri' ), $address->prefix( 'bold', 'https://example.test' ) );
        $this->assertSame( '/bold_ger', $address->prefix( 'bold_ger', 'https://example.test' )['prefix'] );
        $this->assertSame( array( 'prefix' => '', 'reached' => true, 'how' => 'no_prefix' ), $address->prefix( '', 'https://example.test' ) );
        $this->assertSame( 'site', $address->match( 'example.test', 'news/today' ) );
        $this->assertSame( 'bold', $address->match( 'example.test', 'bold/news' ) );
        $this->assertSame( 'admin', $address->match( 'admin.example.test', '' ) );
        $this->assertSame( 'site', $address->match( 'unknown.example.test', '' ), 'DefaultAccess when nothing matches' );
    }

    /** PA-03 */
    public function testHostMapWithAThirdField()
    {
        $address = self::alpha( array( 'host_match_map' => array(
            'example.test;site;start', 'shop.example;shop;end', 'intranet;intra;part', 'strict.example.test;strict;strict' ),
            'siteaccesses' => array( 'site', 'shop', 'intra', 'strict' ), 'default_access' => '' ) );
        $this->assertSame( 'site', $address->match( 'example.test.local', '' ), 'start: a test host that begins with the entry' );
        $this->assertSame( 'shop', $address->match( 'www.shop.example', '' ), 'end' );
        $this->assertSame( 'intra', $address->match( 'my.intranet.example', '' ), 'part' );
        $this->assertSame( '', $address->match( 'www.strict.example.test', '' ), 'strict: only the host as listed' );
        $this->assertSame( 'strict', $address->match( 'strict.example.test', '' ) );

        $this->assertSame( array( 'prefix' => '', 'reached' => true, 'how' => 'no_prefix' ), $address->prefix( 'site', 'https://example.test.local' ) );
        $this->assertSame( array( 'prefix' => '', 'reached' => true, 'how' => 'no_prefix' ), $address->prefix( 'shop', 'https://www.shop.example' ) );
        // the third field is not part of the siteaccess name
        $this->assertSame( '', $address->prefix( 'intra', 'https://intranet' )['prefix'] );

        $defaulted = self::alpha( array( 'host_match_map' => array( 'example.test;site' ), 'host_match_method' => 'start',
                                         'siteaccesses' => array( 'site' ), 'default_access' => '' ) );
        $this->assertSame( 'site', $defaulted->match( 'example.test.local', '' ), 'HostMatchMethod for an entry without a third field' );
        $this->assertSame( '', self::alpha( array( 'host_match_map' => array( 'example.test;site' ), 'default_access' => '' ) )->match( 'example.test.local', '' ) );
        $this->assertSame( '', self::alpha( array( 'host_match_map' => array( 'example.test;site;nonsense' ), 'default_access' => '' ) )->match( 'example.test', '' ), 'an unknown method matches nothing' );
    }

    /** PA-04 */
    public function testMatchOrderDecides()
    {
        $hostFirst = self::alpha( array( 'match_order' => array( 'host', 'uri' ) ) );
        $this->assertSame( 'site', $hostFirst->match( 'example.test', 'bold' ), 'the host matches before the uri is looked at' );
        $result = $hostFirst->prefix( 'bold', 'https://example.test' );
        $this->assertFalse( $result['reached'] );
        $this->assertSame( '/bold', $result['prefix'], 'the prefix of the older preloader is kept' );
    }

    /** PA-05 */
    public function testUriMapAndHostUri()
    {
        $map = self::alpha( array( 'uri_match_type' => 'map', 'uri_match_map' => array( 'fett;bold', 'admin;admin' ) ) );
        $this->assertSame( array( 'prefix' => '/fett', 'reached' => true, 'how' => 'uri' ), $map->prefix( 'bold', 'https://example.test' ) );
        $this->assertSame( 'site', $map->match( 'example.test', 'bold' ), 'with a map the name itself is no prefix' );

        $hostUri = new expPreloadAddress( array(
            'match_order' => array( 'host_uri' ),
            'host_uri_match_map' => array( 'example.test;de;site_de', 'example;en;site_en;start', 'example.test;;site' ),
            'siteaccesses' => array( 'site', 'site_de', 'site_en' ),
        ) );
        $this->assertSame( array( 'prefix' => '/de', 'reached' => true, 'how' => 'host_uri' ), $hostUri->prefix( 'site_de', 'https://example.test' ) );
        $this->assertSame( array( 'prefix' => '', 'reached' => true, 'how' => 'no_prefix' ), $hostUri->prefix( 'site', 'https://example.test' ) );
        $this->assertSame( '/en', $hostUri->prefix( 'site_en', 'https://example.test' )['prefix'] );
        $this->assertSame( 'site_de', $hostUri->match( 'example.test', 'de/news' ) );
        $this->assertSame( 'site', $hostUri->match( 'example.test', 'dex' ), 'a uri entry matches whole path elements' );
    }

    /** PA-06 */
    public function testPrefixNotRepeated()
    {
        $address = self::alpha( array( 'host_match_map' => array() ) );
        $result = $address->prefix( 'bold', 'https://demo.example/bold' );
        $this->assertSame( array( 'prefix' => '', 'reached' => true, 'how' => 'no_prefix' ), $result, 'the path of SiteURL already selects it' );
        $this->assertSame( array( 'https://demo.example/bold' ), expPreloadAddress::startUrls( 'https://demo.example/bold', '/bold' ) );
        $this->assertSame( array( 'https://demo.example/bold' ), expPreloadAddress::startUrls( 'https://demo.example', '/bold' ) );
    }

    /** PA-07 */
    public function testNotReached()
    {
        $hostOnly = new expPreloadAddress( array( 'match_order' => array( 'host' ), 'host_match_map' => array( 'example.test;site' ),
                                                  'siteaccesses' => array( 'site', 'bold' ), 'default_access' => 'site' ) );
        $this->assertSame( array( 'prefix' => '', 'reached' => false, 'how' => 'guess' ), $hostOnly->prefix( 'bold', 'https://example.test' ),
                           'no uri matching: no prefix to add' );
    }

    /** PA-08 */
    public function testStartUrls()
    {
        $this->assertSame( array( 'https://example.test/', 'https://example.test/news', 'https://example.test/about' ),
                           expPreloadAddress::startUrls( 'https://example.test', '', 'news; /about/ ;;news' ) );
        $this->assertSame( array( 'https://example.test/bold', 'https://example.test/bold/news' ),
                           expPreloadAddress::startUrls( 'https://example.test', '/bold', 'news' ) );
        $this->assertSame( array( 'https://example.test/bold/a', 'https://example.test/bold' ),
                           expPreloadAddress::startUrls( 'https://example.test', '/bold', 'ignored', array( '/a/', 'a', '/' ) ) );
        $this->assertSame( 'https://example.test/', expPreloadAddress::normalise( 'https://example.test/index.php' ) );
        $this->assertSame( 'https://example.test/news?next=/a/b', expPreloadAddress::normalise( 'https://example.test/news/?next=/a/b' ) );
        $this->assertSame( 'https://example.test/?a=1', expPreloadAddress::normalise( 'https://example.test?a=1' ) );
    }
}
