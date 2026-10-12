<?php
/**
 * eZURI::build( $url, $full ): a link within the site in one call, the same as the ezurl template operator
 * gives (index directory, the siteaccess of a URI access, with $full the scheme and host), relative and full,
 * with the siteaccess in the URL and in the host name.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/ezutils/eZURIBuildTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit f2284944b2), Copyright (C) Mugo Web, GPL v2
 */

class eZURIBuildTest extends PHPUnit\Framework\TestCase
{
    private $accessPath;
    private $wwwDir;
    private $indexFile;
    private $server = array();

    public static function setUpBeforeClass(): void
    {
        // The cluster handler is started outside the tests: starting it installs a handler of its own
        eZClusterFileHandler::instance();
    }

    protected function setUp(): void
    {
        $sys = eZSys::instance();
        $this->accessPath = $sys->AccessPath;
        $this->wwwDir = $sys->WWWDir;
        $this->indexFile = $sys->IndexFile;
        foreach ( array( 'HTTP_HOST', 'HTTPS', 'SERVER_PORT', 'HTTP_X_FORWARDED_HOST' ) as $name )
            $this->server[$name] = array_key_exists( $name, $_SERVER ) ? $_SERVER[$name] : null;

        $sys->WWWDir = '';
        $sys->IndexFile = '';
        $_SERVER['HTTP_HOST'] = 'example.com';
        unset( $_SERVER['HTTPS'], $_SERVER['HTTP_X_FORWARDED_HOST'] );
        $_SERVER['SERVER_PORT'] = '80';
    }

    protected function tearDown(): void
    {
        $sys = eZSys::instance();
        eZSys::setAccessPath( $this->accessPath['siteaccess']['url'], $this->accessPath['siteaccess']['name'], true );
        eZSys::setAccessPath( $this->accessPath['path']['url'], $this->accessPath['path']['name'], false );
        $sys->WWWDir = $this->wwwDir;
        $sys->IndexFile = $this->indexFile;
        foreach ( $this->server as $name => $value )
        {
            if ( $value === null )
                unset( $_SERVER[$name] );
            else
                $_SERVER[$name] = $value;
        }
    }

    /** The siteaccess in the URL: /<siteaccess>/... */
    private function uriAccess()
    {
        eZSys::clearAccessPath( false );
        eZSys::setAccessPath( array( 'test' ), 'test', true );
    }

    /** The siteaccess by host name: nothing in the path */
    private function hostAccess()
    {
        eZSys::clearAccessPath( false );
        eZSys::clearAccessPath( true );
    }

    public function testRelativeUrlWithTheSiteaccessInTheUrl()
    {
        $this->uriAccess();
        $this->assertSame( '/test/my/path', eZURI::build( '/my/path' ) );
        $this->assertSame( '/test/my/path', eZURI::build( 'my/path', false ) );
    }

    public function testFullUrlWithTheSiteaccessInTheUrl()
    {
        $this->uriAccess();
        $this->assertSame( 'http://example.com/test/my/path', eZURI::build( '/my/path', true ) );
    }

    public function testRelativeAndFullUrlWithTheSiteaccessInTheHostName()
    {
        $this->hostAccess();
        $this->assertSame( '/my/path', eZURI::build( '/my/path' ) );
        $this->assertSame( 'http://example.com/my/path', eZURI::build( '/my/path', true ) );
        $this->assertSame( '/', eZURI::build( '' ) );
    }

    public function testTheSameResultAsTheEzurlOperatorsTransform()
    {
        foreach ( array( 'uriAccess', 'hostAccess' ) as $mode )
        {
            $this->$mode();
            foreach ( array( '/content/view/full/2', 'a b?x=1&y=2', '#top', '' ) as $url )
            {
                foreach ( array( false => 'relative', true => 'full' ) as $full => $serverURL )
                {
                    $href = $url;
                    eZURI::transformURI( $href, false, $serverURL );
                    $this->assertSame( $href, eZURI::build( $url, (bool)$full ), "$mode $serverURL $url" );
                }
            }
        }
    }

    public function testTheEzurlTemplateOperatorGivesTheSameLink()
    {
        $this->uriAccess();
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'url', '/my/path' );
        $operator = new eZURLOperator();
        $value = '/my/path';
        $params = array( 'quote_val' => 'no', 'server_url' => 'full' );
        $operator->modify( $tpl, 'ezurl', array(), '', '', $value, $params, array() );
        $this->assertSame( $value, eZURI::build( '/my/path', true ) );
    }

    public function testHtmlEscapingAsEzurlUnlessSwitchedOff()
    {
        $this->hostAccess();
        $this->assertSame( '/search?a=1&amp;b=2', eZURI::build( '/search?a=1&b=2' ) );
        $this->assertSame( '/search?a=1&b=2', eZURI::build( '/search?a=1&b=2', false, false ) );
    }

    public function testExternalLinksAreGivenBackUnchanged()
    {
        $this->uriAccess();
        $this->assertSame( 'https://example.org/x', eZURI::build( 'https://example.org/x', true ) );
        $this->assertSame( '//cdn.example.org/x', eZURI::build( '//cdn.example.org/x' ) );
        $this->assertSame( 'mailto:a@example.org', eZURI::build( 'mailto:a@example.org' ) );
    }

    public function testTheGivenUrlIsNotChanged()
    {
        $this->uriAccess();
        $url = '/my/path';
        eZURI::build( $url, true );
        $this->assertSame( '/my/path', $url );
    }
}
