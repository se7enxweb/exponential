<?php
/**
 * Tests of eZSiteAccess::match(), the choice of the siteaccess for a request from site.ini: the static match, the
 * default, and each MatchOrder probe (port, server variable, uri by map, element, text and regexp, host by map,
 * element, text and regexp, index file by element, text and regexp), the order of the probes, names that are not
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
        unset( $_SERVER['K1_SITEACCESS'] );
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
}
