<?php
/**
 * The helpers of URL map imports without a network or a database: the sitemap source, the suggester, the 404
 * source and the tester, with a fake HTTP client that answers from a table.
 *
 *  UH-01 sitemaps: a <urlset>, a sitemap index with its sitemaps (relative and gzip-compressed ones), a crawl list,
 *        malformed XML read for its <loc> elements, the host filter, dedupe, --limit, paths or full addresses, a
 *        sitemap that cannot be fetched, a DOCTYPE never loaded
 *  UH-02 the suggester's normalization: decoding, camelCase, transliteration, endings, index pages, stop words; the
 *        similarity of words and lists
 *  UH-03 the suggester's scores and choices: the exact page first, plural and spelling, the name of a page, the
 *        last element over the rest, --fill-above (never a tie), the note, rows with a target left alone, 5,000 rows
 *        against 10,000 pages in seconds
 *  UH-04 the 404 source: not-found log and access log lines, hits and order, --min-hits, --since, assets, bots,
 *        methods, masked queries dropped and real ones kept, the siteaccess filter, rotated and gzip files
 *  UH-05 the tester: landed (permanent and temporary), wrong-status, wrong-target, not-redirected, a target that
 *        answers 404, error, a loop and too many redirects, an alias answered in place, a wildcard with a sample,
 *        node targets with the resolver, the PathPrefix as an equivalent, the address comparison; public_only refuses
 *        private and local addresses
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUrlMapHelpersTestHttp extends expUrlMapHttp
{
    /** @var array url => array( status, location, body ) */
    public $table = array();

    /** @var string[] every address requested */
    public $requested = array();

    /** @var int[] the size of each getMany() batch */
    public $batches = array();

    public function getMany( array $urls, $concurrency = 8 )
    {
        $this->batches[] = count( $urls );
        $out = array();
        foreach ( $urls as $url )
        {
            $this->requested[] = $url;
            if ( !isset( $this->table[$url] ) )
            {
                $out[$url] = self::answer( $url, 404 );
                continue;
            }
            $a = $this->table[$url] + array( 0, null, '' );
            $out[$url] = $a[0] === 0 ? self::answer( $url, 0, null, '', 'Could not resolve host' ) : self::answer( $url, $a[0], $a[1], $a[2] );
        }
        return $out;
    }
}

class expUrlMapHelpersTest extends PHPUnit\Framework\TestCase
{
    /** @var string[] */
    private $files = array();

    protected function tearDown(): void
    {
        foreach ( $this->files as $file )
            if ( is_file( $file ) )
                unlink( $file );
    }

    private function tempFile( $content, $suffix = '' )
    {
        $dir = dirname( __DIR__, 5 ) . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0755, true );
        $file = $dir . '/urlmap-helpers-' . bin2hex( random_bytes( 6 ) ) . $suffix;
        file_put_contents( $file, $content );
        $this->files[] = $file;
        return $file;
    }

    private static function urlset( array $locs )
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ( $locs as $loc )
            $xml .= '<url><loc>' . htmlspecialchars( $loc, ENT_XML1 ) . '</loc><lastmod>2026-10-02</lastmod></url>';
        return $xml . '</urlset>';
    }

    private static function olds( array $rows )
    {
        return array_map( function ( $row ) { return $row->oldUrl(); }, $rows );
    }

    /** UH-01 */
    public function testSitemaps()
    {
        $http = new expUrlMapHelpersTestHttp();
        $http->table['https://old.example/sitemap.xml'] = array( 200, null,
            '<?xml version="1.0"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . '<sitemap><loc>https://old.example/sitemap-pages.xml</loc></sitemap>'
            . '<sitemap><loc>/sitemap-news.xml.gz</loc></sitemap>'
            . '<sitemap><loc>https://old.example/missing.xml</loc></sitemap></sitemapindex>' );
        $http->table['https://old.example/sitemap-pages.xml'] = array( 200, null, self::urlset( array(
            'https://old.example/', 'https://old.example/about-us.html', 'https://OLD.example/about-us.html/', 'https://old.example/products?id=12&x=1',
            'https://other.example/elsewhere', 'mailto:someone@old.example' ) ) );
        $http->table['https://old.example/sitemap-news.xml.gz'] = array( 200, null, gzencode( self::urlset( array( 'https://old.example/news/2019/summer-sale.php' ) ) ) );
        $source = new expUrlMapSitemapSource( array(), $http );
        $rows = $source->rows( 'https://old.example/sitemap.xml' );
        $this->assertSame( array( '/', '/about-us.html', '/products?id=12&x=1', '/news/2019/summer-sale.php' ), self::olds( $rows ) );
        $this->assertSame( '', $rows[1]->newUrl() );
        $c = $source->counts();
        $this->assertSame( array( 3, 4, 1, 1, 1 ), array( $c['sitemaps'], $c['rows'], $c['duplicates'], $c['other_hosts'], $c['skipped'] ) );
        $this->assertCount( 1, $source->warnings() );
        $this->assertStringContainsString( 'missing.xml: HTTP 404', $source->warnings()[0] );

        // full addresses, every host, duplicates kept, the limit
        $rows = ( new expUrlMapSitemapSource( array( 'full_urls' => true, 'any_host' => true ), $http ) )->rows( 'https://old.example/sitemap-pages.xml' );
        $this->assertSame( array( 'https://old.example/', 'https://old.example/about-us.html', 'https://old.example/products?id=12&x=1', 'https://other.example/elsewhere' ), self::olds( $rows ) );
        $rows = ( new expUrlMapSitemapSource( array( 'dedupe' => false ), $http ) )->rows( 'https://old.example/sitemap-pages.xml' );
        $this->assertCount( 4, $rows );
        $limited = new expUrlMapSitemapSource( array( 'limit' => 2 ), $http );
        $this->assertCount( 2, $limited->rows( 'https://old.example/sitemap.xml' ) );
        $this->assertTrue( $limited->counts()['limited'] );
        $only = ( new expUrlMapSitemapSource( array( 'hosts' => array( 'other.example' ) ), $http ) )->rows( 'https://old.example/sitemap-pages.xml' );
        $this->assertSame( array( '/elsewhere' ), self::olds( $only ) );

        // a crawl list file (gzip-compressed), its first host kept
        $file = $this->tempFile( gzencode( "# crawl of old.example\nhttps://old.example/a\n\nhttps://old.example/b\t200\thtml\nhttps://cdn.example/x.png\n/c\n" ), '.txt.gz' );
        $source = new expUrlMapSitemapSource( array(), $http );
        $this->assertSame( array( '/a', '/b', '/c' ), self::olds( $source->rows( $file ) ) );
        $this->assertSame( array( 'old.example' ), $source->hosts() );

        // malformed XML: the <loc> elements are read as text, with a warning
        $parsed = expUrlMapSitemapSource::parse( '<urlset><url><loc>https://old.example/x?a=1&amp;b=2</loc></url><url><loc><![CDATA[https://old.example/y]]></loc></url><broken' );
        $this->assertSame( array( 'https://old.example/x?a=1&b=2', 'https://old.example/y' ), $parsed['locs'] );
        $this->assertCount( 1, $parsed['warnings'] );
        // a document with a DOCTYPE is never given to the XML parser (no entities)
        $parsed = expUrlMapSitemapSource::parse( '<?xml version="1.0"?><!DOCTYPE x [<!ENTITY e SYSTEM "file:///etc/passwd">]><urlset><url><loc>https://old.example/&e;</loc></url></urlset>' );
        $this->assertSame( array( 'https://old.example/&e;' ), $parsed['locs'] );
        $this->assertSame( 'list', expUrlMapSitemapSource::parse( "\x1f\x8bnot gzip" )['type'] );
        $this->assertSame( array( 'https://a.example/1', '/2' ), expUrlMapSitemapSource::parse( "\xEF\xBB\xBF\"https://a.example/1\";x\r\n# no\r\n/2" )['locs'] );

        // a source that cannot be fetched or read is an error
        $http->table['https://down.example/sitemap.xml'] = array( 0 );
        try
        {
            ( new expUrlMapSitemapSource( array(), $http ) )->rows( 'https://down.example/sitemap.xml' );
            $this->fail( 'an unreachable sitemap is an error' );
        }
        catch ( expUrlMapFormatException $e )
        {
            $this->assertStringContainsString( 'Could not resolve host', $e->getMessage() );
        }
        $this->expectException( expUrlMapFormatException::class );
        ( new expUrlMapSitemapSource( array(), $http ) )->rows( dirname( __DIR__ ) . '/no-such-sitemap.xml' );
    }

    /** UH-02 */
    public function testNormalization()
    {
        $this->assertSame( 'ueber uns', expUrlMapSuggester::normalize( '%C3%9Cber-Uns' ) );
        $this->assertSame( 'about us', expUrlMapSuggester::normalize( 'AboutUs' ) );
        $this->assertSame( 'strasse cafe', expUrlMapSuggester::normalize( 'Straße_Café' ) );
        $this->assertSame( array( 'summer', 'sale' ), expUrlMapSuggester::tokens( 'the-summer_sale.html' ) );
        $this->assertSame( array( 'kontakt' ), expUrlMapSuggester::tokens( 'kontakt.php5' ) );
        $this->assertSame( array( 'news', '2019', 'summer' ), expUrlMapSuggester::segments( '/news/2019/summer.aspx?id=3' ) );
        $this->assertSame( array( 'shop' ), expUrlMapSuggester::segments( '/shop/index.html' ) );
        $this->assertSame( array(), expUrlMapSuggester::segments( '/' ) );
        $this->assertSame( array( 1.0, 0.8, 0.8, 0.6, 0.0, 0.0 ), array(
            expUrlMapSuggester::tokenSimilarity( 'recipe', 'recipe' ), expUrlMapSuggester::tokenSimilarity( 'recipe', 'recipes' ),
            expUrlMapSuggester::tokenSimilarity( 'category', 'categories' ), expUrlMapSuggester::tokenSimilarity( 'summer', 'sommer' ),
            expUrlMapSuggester::tokenSimilarity( 'cat', 'cats' ), expUrlMapSuggester::tokenSimilarity( 'news', 'shop' ) ) );
        $this->assertSame( 1.0, expUrlMapSuggester::overlap( array( 'a1', 'b2' ), array( 'b2', 'a1' ) ) );
        $this->assertSame( 0.5, expUrlMapSuggester::overlap( array( 'aa', 'bb' ), array( 'aa', 'cc' ) ) );
        $this->assertSame( 0.0, expUrlMapSuggester::overlap( array(), array( 'aa' ) ) );
    }

    /** UH-03 */
    public function testSuggestions()
    {
        $candidates = array(
            array( 'path' => '/about-us', 'node_id' => 60, 'name' => 'About us' ),
            array( 'path' => '/recipes', 'node_id' => 61, 'name' => 'Recipes' ),
            array( 'path' => '/recipes/pasta-with-tomato-sauce', 'node_id' => 62, 'name' => 'Pasta with tomato sauce' ),
            array( 'path' => '/news/summer-sale-2019', 'node_id' => 63, 'name' => 'Summer sale' ),
            array( 'path' => '/kontakt', 'node_id' => 64, 'name' => 'Kontakt' ),
            array( 'path' => '/team/ueber-uns', 'node_id' => 65, 'name' => 'Über uns' ),
            array( 'path' => '/shop/twin-a', 'node_id' => 66, 'name' => 'Twin' ),
            array( 'path' => '/shop/twin-b', 'node_id' => 67, 'name' => 'Twin' ),
        );
        $s = new expUrlMapSuggester( $candidates );
        $this->assertSame( 8, $s->count() );
        $this->assertSame( '/about-us', $s->suggest( '/about-us.html' )[0]['path'] );
        $this->assertSame( 100, $s->suggest( '/about-us.html' )[0]['score'] );
        $this->assertSame( '/recipes/pasta-with-tomato-sauce', $s->suggest( '/kitchen/recipe/pasta_tomato_sauce.php' )[0]['path'] );
        $this->assertSame( '/news/summer-sale-2019', $s->suggest( 'https://old.example/aktuelles/SummerSale.htm' )[0]['path'] );
        $this->assertSame( '/team/ueber-uns', $s->suggest( '/%C3%BCber-uns/' )[0]['path'], 'transliterated' );
        $this->assertSame( '/kontakt', $s->suggest( '/contact/kontakt.php' )[0]['path'] );
        $this->assertSame( array(), $s->suggest( '/' ) );
        $this->assertSame( array(), $s->suggest( '/zzzz-qqqq' ) );
        $this->assertGreaterThan( expUrlMapSuggester::score( '/recipes/pasta.html', $candidates[0] ), expUrlMapSuggester::score( '/recipes/pasta.html', $candidates[2] ) );
        $this->assertLessThanOrEqual( 3, count( $s->suggest( '/shop/twin' ) ) );

        $rows = array( new expUrlMapRow( '/about-us.html', '', '', '', '', 'old menu', 2 ), new expUrlMapRow( '/shop/twin', '', '', '', '', '', 3 ),
                       new expUrlMapRow( '/kitchen/recipe/pasta_tomato_sauce.php', '', '', '', '', '', 4 ), new expUrlMapRow( '/kept', '/recipes', '', '', '', '', 5 ),
                       new expUrlMapRow( '/zzzz-qqqq', '', '', '', '', '', 6 ), new expUrlMapRow( '/old/*', '', '', '', '', '', 7 ) );
        $result = $s->apply( $rows, array( 'fill_above' => 90 ) );
        $out = $result['rows'];
        $this->assertSame( array( '/about-us', '', '/recipes/pasta-with-tomato-sauce', '/recipes', '', '' ), array_map( function ( $r ) { return $r->newUrl(); }, $out ) );
        $this->assertSame( 'old menu | suggested target, score 100', $out[0]->note() );
        $this->assertMatchesRegularExpression( '#^suggested: /shop/twin-a \(\d+\), /shop/twin-b \(\d+\)#', $out[1]->note(), 'a tie is never filled' );
        $this->assertStringStartsWith( 'suggested target, score ', $out[2]->note() );
        $this->assertSame( array( 'rows' => 6, 'open' => 4, 'filled' => 2, 'suggested' => 1, 'none' => 1 ), $result['counts'] );
        $this->assertCount( 4, $result['report'] );
        $this->assertTrue( $result['report'][0]['filled'] );
        $this->assertSame( 4, $out[2]->line() );
        // without fill_above nothing is filled
        $this->assertSame( 0, $s->apply( $rows )['counts']['filled'] );

        // fast enough: 5,000 rows against 10,000 pages
        $words = array( 'alpha', 'bravo', 'charlie', 'delta', 'echo', 'foxtrot', 'golf', 'hotel', 'india', 'juliet', 'kilo', 'lima', 'mike', 'november',
                        'oscar', 'papa', 'quebec', 'romeo', 'sierra', 'tango', 'uniform', 'victor', 'whiskey', 'xray', 'yankee', 'zulu' );
        $many = array();
        for ( $i = 0; $i < 10000; $i++ )
            $many[] = array( 'path' => '/' . $words[$i % 26] . '/' . $words[( $i * 7 ) % 26] . '-' . $words[( $i * 11 ) % 26] . '-' . $i, 'node_id' => $i + 100, 'name' => $words[( $i * 3 ) % 26] . ' ' . $i );
        $big = new expUrlMapSuggester( $many );
        $rows = array();
        for ( $i = 0; $i < 5000; $i++ )
            $rows[] = new expUrlMapRow( '/old/' . $words[( $i * 7 ) % 26] . '_' . $words[( $i * 11 ) % 26] . '_' . $i . '.html', '' );
        $started = microtime( true );
        $result = $big->apply( $rows, array( 'fill_above' => 95 ) );
        $seconds = microtime( true ) - $started;
        $this->assertSame( 5000, $result['counts']['open'] );
        $this->assertLessThan( 60, $seconds, '5,000 rows against 10,000 pages take seconds, not minutes' );
        $this->assertGreaterThan( 4000, $result['counts']['filled'] + $result['counts']['suggested'] );
    }

    /** UH-04 */
    public function test404Source()
    {
        $nf = function ( $time, $status, $method, $uri, $siteaccess = 'site', $ua = 'Mozilla/5.0 (X11; Linux x86_64)' )
        {
            return '[ ' . $time . ' ] [203.0.x.x] ' . $status . ' ' . $method . ' ' . $uri . ' siteaccess=' . $siteaccess
                 . ' reason=module:x node=- referrer=https://www.google.com/ ua=3f2a9c1b "' . $ua . '"';
        };
        $lines = array(
            $nf( 'Nov 01 2026 10:00:00', 404, 'GET', '/old-page.html' ),
            $nf( 'Nov 02 2026 10:00:00', 404, 'GET', '/old-page.html' ),
            $nf( 'Nov 03 2026 10:00:00', 410, 'HEAD', '/Old-Page.html' ),
            $nf( 'Nov 03 2026 11:00:00', 404, 'GET', '/products?id=***' ),
            $nf( 'Nov 03 2026 11:00:00', 404, 'GET', '/products?id=***&x=***' ),
            $nf( 'Oct 01 2026 11:00:00', 404, 'GET', '/ancient' ),
            $nf( 'Nov 04 2026 11:00:00', 404, 'POST', '/form' ),
            $nf( 'Nov 04 2026 11:00:00', 404, 'GET', '/var/site/storage/images/x.jpg' ),
            $nf( 'Nov 04 2026 11:00:00', 404, 'GET', '/favicon.ico' ),
            $nf( 'Nov 04 2026 11:00:00', 404, 'GET', '/bot-page', 'site', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google' ),
            $nf( 'Nov 04 2026 11:00:00', 404, 'GET', '/other-sa', 'ger' ),
            '203.0.113.9 - - [04/Nov/2026:12:00:00 +0000] "GET /index.php?id=12 HTTP/1.1" 404 512 "-" "Mozilla/5.0 (Windows NT 10.0)"',
            '203.0.113.9 - - [04/Nov/2026:12:00:01 +0000] "GET /fine HTTP/1.1" 200 512 "-" "Mozilla/5.0"',
            'not a log line',
            '',
        );
        $this->assertSame( array( 404, 'GET', '/old-page.html', 'site', 'https://www.google.com/', 'Mozilla/5.0 (X11; Linux x86_64)', 'notfound' ),
                           array_values( array_diff_key( expUrlMap404Source::parseLine( $lines[0] ), array( 'time' => 1 ) ) ) );
        $access = expUrlMap404Source::parseLine( $lines[11] );
        $this->assertSame( array( 404, '/index.php?id=12', 'access', gmmktime( 12, 0, 0, 11, 4, 2026 ) ), array( $access['status'], $access['uri'], $access['source'], $access['time'] ) );
        $this->assertNull( expUrlMap404Source::parseLine( 'nothing' ) );
        $this->assertTrue( expUrlMap404Source::isAsset( '/design/x/style.css?v=1' ) );
        $this->assertTrue( expUrlMap404Source::isAsset( '/extension/foo' ) );
        $this->assertFalse( expUrlMap404Source::isAsset( '/about.html' ) );
        $this->assertTrue( expUrlMap404Source::isBot( '' ) );
        $this->assertTrue( expUrlMap404Source::isBot( 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)' ) );
        $this->assertFalse( expUrlMap404Source::isBot( 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)' ) );

        $since = mktime( 0, 0, 0, 11, 1, 2026 );
        $source = new expUrlMap404Source( array( 'since' => $since ) );
        $entries = $source->aggregate( $lines );
        $this->assertSame( array( '/old-page.html', '/products', '/index.php?id=12', '/other-sa' ), array_column( $entries, 'path' ) );
        $this->assertSame( array( 3, 2, 1, 1 ), array_column( $entries, 'hits' ) );
        $this->assertSame( array( 'https://www.google.com/' ), $entries[0]['referrers'] );
        $c = $source->counts();
        $this->assertSame( array( 14, 12, 2, 1, 1, 1, 1 ), array( $c['lines'], $c['not_found'], $c['assets'], $c['bots'], $c['methods'], $c['outside'], $c['unparsed'] ) );

        $this->assertSame( array( '/old-page.html', '/products' ), array_column( ( new expUrlMap404Source( array( 'min_hits' => 2 ) ) )->aggregate( $lines ), 'path' ) );
        $this->assertSame( array( '/old-page.html' ), array_column( ( new expUrlMap404Source( array( 'limit' => 1 ) ) )->aggregate( $lines ), 'path' ) );
        $withAll = array_column( ( new expUrlMap404Source( array( 'include_assets' => true, 'include_bots' => true ) ) )->aggregate( $lines ), 'path' );
        foreach ( array( '/favicon.ico', '/bot-page', '/var/site/storage/images/x.jpg', '/ancient' ) as $path )
            $this->assertContains( $path, $withAll );
        $this->assertNotContains( '/other-sa', array_column( ( new expUrlMap404Source( array( 'siteaccess' => 'site' ) ) )->aggregate( $lines ), 'path' ) );

        $rows = expUrlMap404Source::toRows( array_slice( $entries, 0, 1 ) );
        $this->assertSame( array( '/old-page.html', '' ), array( $rows[0]->oldUrl(), $rows[0]->newUrl() ) );
        $this->assertSame( '404: 3 hits, last 2026-11-03, siteaccess site', $rows[0]->note() );

        // files: the log and its rotated ones, plain and gzip, oldest first
        $log = $this->tempFile( implode( "\n", array_slice( $lines, 0, 2 ) ) . "\n" );
        $this->files[] = $log . '.1';
        $this->files[] = $log . '.2.gz';
        file_put_contents( $log . '.1', $lines[2] . "\n" );
        file_put_contents( $log . '.2.gz', gzencode( $lines[11] . "\n" ) );
        $this->assertSame( array( $log . '.2.gz', $log . '.1', $log ), expUrlMap404Source::logFiles( $log ) );
        $rows = ( new expUrlMap404Source() )->rows( expUrlMap404Source::logFiles( $log ) );
        $this->assertSame( array( '/Old-Page.html', '/index.php?id=12' ), self::olds( $rows ), 'the spelling seen first (the oldest file first)' );
    }

    /** UH-05 */
    public function testTester()
    {
        $b = 'https://new.example';
        $http = new expUrlMapHelpersTestHttp();
        $t = array(
            "$b/about-us.html" => array( 301, '/about-us' ), "$b/about-us" => array( 200 ),
            "$b/sale" => array( 302, "$b/offers" ), "$b/offers" => array( 200 ),
            "$b/sale-perm" => array( 301, "$b/offers" ),
            "$b/elsewhere" => array( 301, '/recipes' ), "$b/recipes" => array( 200 ),
            "$b/page" => array( 200 ),
            "$b/to-missing" => array( 301, '/gone' ), "$b/gone" => array( 404 ),
            "$b/loop-a" => array( 301, '/loop-b' ), "$b/loop-b" => array( 301, '/loop-a' ),
            "$b/down" => array( 0 ),
            "$b/alias-old" => array( 200 ),
            "$b/old/exp-url-test" => array( 301, '/new/exp-url-test' ), "$b/new/exp-url-test" => array( 200 ),
            "$b/node-old" => array( 301, '/fit-healthy/recipes/pasta' ), "$b/fit-healthy/recipes/pasta" => array( 200 ),
            "$b/q?id=12" => array( 307, '/about-us' ),
            "$b/far" => array( 301, 'https://partner.example/x' ), 'https://partner.example/x' => array( 200 ),
        );
        for ( $i = 1; $i <= 7; $i++ )
            $t["$b/chain$i"] = array( 301, '/chain' . ( $i + 1 ) );
        $http->table = $t;
        $rows = array(
            new expUrlMapRow( '/about-us.html', '/about-us', '', '', '', '', 2 ),                // landed
            new expUrlMapRow( '/sale', '/offers', 'redirect-temporary', '', '', '', 3 ),        // landed, temporary
            new expUrlMapRow( '/sale-perm', '/offers', '', '', '', '', 4, '302' ),              // wrong-status: 301 where 302 is asked
            new expUrlMapRow( '/elsewhere', '/about-us', '', '', '', '', 5 ),                   // wrong-target
            new expUrlMapRow( '/page', '/about-us', '', '', '', '', 6 ),                        // not-redirected
            new expUrlMapRow( '/to-missing', '/gone', '', '', '', '', 7 ),                      // wrong-target: 404
            new expUrlMapRow( '/loop-a', '/x', '', '', '', '', 8 ),                             // error: loop
            new expUrlMapRow( '/down', '/x', '', '', '', '', 9 ),                               // error: no answer
            new expUrlMapRow( '/chain1', '/x', '', '', '', '', 10 ),                            // error: too many redirects
            new expUrlMapRow( '/alias-old', '/about-us', 'alias', '', '', '', 11 ),             // landed in place
            new expUrlMapRow( '/old/*', '/new/{1}', '', '', '', '', 12 ),                       // landed, wildcard sample
            new expUrlMapRow( '/node-old', 'node:62', '', '', '', '', 13 ),                     // landed, node target, prefix equivalent
            new expUrlMapRow( 'https://old.example/q?id=12', '/about-us', '', '', '', '', 14, '307' ), // landed, query, host replaced
            new expUrlMapRow( '/far', 'https://partner.example/x', '', '', '', '', 15 ),        // landed, another site
            new expUrlMapRow( '/no-target', '', '', '', '', '', 16 ),                           // skipped
            new expUrlMapRow( '/node-unknown', 'node:99', '', '', '', '', 17 ),                 // skipped: not resolvable
            new expUrlMapRow( '/about-us', '/about-us/', '', '', '', '', 18 ),                  // landed: the page kept its address
        );
        $tester = new expUrlMapTester( $b . '/', array( 'resolver' => function ( $t ) { return $t === 'node:62' ? '/recipes/pasta' : null; },
                                                       'equivalent_prefixes' => array( '/fit-healthy' ) ), $http );
        $result = $tester->test( $rows );
        $states = array_column( $result['results'], 'state' );
        $this->assertSame( array( 'landed', 'landed', 'wrong-status', 'wrong-target', 'not-redirected', 'wrong-target', 'error', 'error', 'error', 'landed',
                                  'landed', 'landed', 'landed', 'landed', 'skipped', 'skipped', 'landed' ), $states );
        $r = $result['results'];
        $this->assertSame( array( 301, "$b/about-us" ), array( $r[0]['status'], $r[0]['landed'] ) );
        $this->assertSame( array( array( 'status' => 301, 'url' => "$b/about-us.html" ), array( 'status' => 200, 'url' => "$b/about-us" ) ), $r[0]['chain'] );
        $this->assertStringContainsString( 'redirect loop', $r[6]['message'] );
        $this->assertStringContainsString( 'more than 5 redirects', $r[8]['message'] );
        $this->assertStringContainsString( 'Could not resolve host', $r[7]['message'] );
        $this->assertSame( "$b/q?id=12", $r[12]['request'] );
        $this->assertSame( array( 'landed' => 8, 'wrong-status' => 1, 'wrong-target' => 2, 'not-redirected' => 1, 'error' => 3, 'skipped' => 2, 'rows' => 17 ), $result['counts'] );
        // the hops of all rows go out together: one batch per hop, not one request at a time
        $this->assertSame( 17 - 2, $http->batches[0] );
        $this->assertLessThanOrEqual( 7, count( $http->batches ) );

        $this->assertTrue( expUrlMapTester::sameAddress( 'https://a.example/Über/', 'https://a.example/%C3%9Cber' ) );
        $this->assertTrue( expUrlMapTester::sameAddress( 'https://a.example/x?b=2&a=1', '/x?a=1&b=2' ) );
        $this->assertFalse( expUrlMapTester::sameAddress( 'https://a.example/x', 'https://b.example/x' ) );
        $this->assertFalse( expUrlMapTester::sameAddress( '/x?a=1', '/x' ) );
        $this->assertTrue( expUrlMapTester::sameAddress( '/de/x', '/x', array( '/de' ) ) );
        $this->assertFalse( expUrlMapTester::sameAddress( '/dex', '/x', array( '/de' ) ) );
        $this->assertSame( "$b/x", expUrlMapHttp::absolute( "$b/a/b", '/x' ) );
        $this->assertSame( "$b/a/y", expUrlMapHttp::absolute( "$b/a/b", 'y' ) );
        $this->assertSame( 'https://c.example/z', expUrlMapHttp::absolute( "$b/a", '//c.example/z' ) );
        $this->assertNotNull( expUrlMapHttp::problem( 'file:///etc/passwd' ) );
        // public_only: every address of the host must be public; the connection is pinned to the checked one
        $resolver = function ( $host ) { return array( 'old.example' => array( '93.184.216.34' ), 'mixed.example' => array( '93.184.216.34', '10.0.0.5' ) )[$host] ?? array(); };
        $this->assertSame( array( 'host' => 'old.example', 'port' => 443, 'ip' => '93.184.216.34' ), expUrlMapHttp::publicAddress( 'https://old.example/sitemap.xml', $resolver ) );
        $this->assertSame( 8080, expUrlMapHttp::publicAddress( 'http://old.example:8080/', $resolver )['port'] );
        foreach ( array( 'https://mixed.example/', 'http://127.0.0.1/', 'http://[::1]/', 'http://169.254.169.254/latest/', 'https://unknown.example/' ) as $blocked )
            $this->assertIsString( expUrlMapHttp::publicAddress( $blocked, $resolver ), $blocked );
        $blockedAnswer = ( new expUrlMapHttp( array( 'public_only' => true, 'resolver' => $resolver ) ) )->get( 'http://127.0.0.1/sitemap.xml' );
        $this->assertSame( array( 0, 'the host 127.0.0.1 has a private or local address, which is not fetched' ), array( $blockedAnswer['status'], $blockedAnswer['error'] ) );
        $this->expectException( InvalidArgumentException::class );
        new expUrlMapTester( 'ftp://x.example' );
    }
}
