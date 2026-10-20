<?php
/**
 * expHelpSearch hit addresses (doc/eng-us/features/6.0/help-system.md, "Search"): 'href' carries the siteaccess of
 * the current request on every engine. No database.
 *
 *  HSH-01  a web request served by a PHP running in the CLI SAPI (Velocity, the built-in server): php_sapi_name() is
 *          'cli' there, yet the request has a method and a Host header, so 'href' gets the siteaccess prefix
 *          (/admin/exphelp/viewer/...); before, every hit on Velocity kept /exphelp/viewer/...
 *  HSH-02  built per call from the current access path: a second request in the same process (a persistent worker)
 *          with another siteaccess gets that one, never the first one's
 *  HSH-03  a shell script (no request method, no Host): 'href' stays the module-relative 'url'
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpSearchHrefTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

// a worktree whose vendor/ is a link to another checkout autoloads from that checkout's class map
if ( !class_exists( 'expHelpSearch' ) )
    require_once dirname( __DIR__, 5 ) . '/kernel/classes/help/exphelpsearch.php';

class expHelpSearchHrefTest extends PHPUnit\Framework\TestCase
{
    private $server = array();
    private $accessPath = null;

    protected function setUp(): void
    {
        parent::setUp();
        foreach ( array( 'REQUEST_METHOD', 'HTTP_HOST' ) as $key )
            $this->server[$key] = array_key_exists( $key, $_SERVER ) ? array( $_SERVER[$key] ) : null;
        $this->accessPath = eZSys::instance()->AccessPath;
    }

    protected function tearDown(): void
    {
        foreach ( $this->server as $key => $value )
        {
            if ( $value === null )
                unset( $_SERVER[$key] );
            else
                $_SERVER[$key] = $value[0];
        }
        $path = is_array( $this->accessPath ) ? $this->accessPath : array();
        eZSys::setAccessPath( isset( $path['siteaccess']['url'] ) ? (array)$path['siteaccess']['url'] : array(),
                              isset( $path['siteaccess']['name'] ) ? (string)$path['siteaccess']['name'] : '' );
        eZSys::setAccessPath( isset( $path['path']['url'] ) ? (array)$path['path']['url'] : array(),
                              isset( $path['path']['name'] ) ? (string)$path['path']['name'] : '', false );
        parent::tearDown();
    }

    private static function search()
    {
        return expHelpSearch::fromDocuments( array(
            'help/start.md' => array(
                'title' => 'Getting started', 'audience' => array( 'admin' ), 'category' => 'getting-started', 'type' => 'help',
                'text' => "# Getting started\n\nSign in and open the dashboard.\n\n## Velocity\n\nThe application server.\n" ),
            'help/cache.md' => array(
                'title' => 'Clearing caches', 'audience' => array( 'admin' ), 'category' => 'administration', 'type' => 'help',
                'text' => "# Clearing caches\n\nVelocity has a response cache.\n" ),
        ) );
    }

    /** A request as Velocity hands it to the kernel: CLI SAPI, a method, a Host header, the siteaccess in the path. */
    private static function webRequest( $siteaccess )
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTP_HOST'] = 'example.invalid';
        eZSys::clearAccessPath( false );
        eZSys::setAccessPath( array( $siteaccess ), $siteaccess );
    }

    /** The prefix the kernel puts before a module address in this request (index.php, if any, and the siteaccess). */
    private static function prefix()
    {
        $url = '/exphelp/viewer/x.md';
        eZURI::transformURI( $url );
        return substr( $url, 0, -strlen( '/exphelp/viewer/x.md' ) );
    }

    /** HSH-01, browse and query both */
    public function testHrefCarriesTheSiteaccessInAWebRequestOfTheCliSapi()
    {
        self::webRequest( 'admin' );
        $this->assertStringEndsWith( '/admin', self::prefix() );
        foreach ( array( 'velocity', '' ) as $q )
        {
            $result = self::search()->search( $q );
            $this->assertNotEmpty( $result['hits'], "hits for '$q'" );
            foreach ( $result['hits'] as $hit )
            {
                $this->assertSame( self::prefix() . explode( '#', $hit['url'] )[0], explode( '#', $hit['href'] )[0], "href of {$hit['id']} for '$q'" );
                $this->assertMatchesRegularExpression( '#/admin/exphelp/viewer/#', $hit['href'] );
            }
        }
    }

    /** HSH-02 */
    public function testHrefFollowsTheSiteaccessOfEachRequest()
    {
        $search = self::search();
        self::webRequest( 'admin' );
        $first = $search->search( 'velocity' );
        self::webRequest( 'editor' );
        $second = $search->search( 'velocity' );
        $this->assertStringContainsString( '/admin/exphelp/viewer/', $first['hits'][0]['href'] );
        $this->assertStringContainsString( '/editor/exphelp/viewer/', $second['hits'][0]['href'] );
        $this->assertStringNotContainsString( '/admin/', $second['hits'][0]['href'] );
    }

    /** HSH-03 */
    public function testHrefStaysModuleRelativeInAShellScript()
    {
        unset( $_SERVER['REQUEST_METHOD'], $_SERVER['HTTP_HOST'] );
        eZSys::setAccessPath( array( 'admin' ), 'admin' );
        foreach ( self::search()->search( 'velocity' )['hits'] as $hit )
            $this->assertSame( $hit['url'], $hit['href'] );
    }
}
