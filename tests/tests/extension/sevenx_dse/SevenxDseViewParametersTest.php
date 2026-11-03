<?php
/**
 * The DS Editor keeps its state in view parameters (/dse/dashboard/(sqlite)/main/(db)/~dmFy...), never in a query
 * string: extension/sevenx_dse/classes/sevenxdseviewparameters.php is the boundary between AdminNeo, which keeps its
 * state in $_GET and prints query-string links, and the address the browser sees.
 *
 * - DB-less: values round-trip (paths with slashes, empty values, values that look like the markers); parameters with
 *   bracketed names travel together in (qs); "&page=2" appended by a script is read; a request address splits at the
 *   view.
 * - The pages AdminNeo draws for the dashboard (the server's database list, the navigation and the db selector) and
 *   for a table (select page: links, the GET search form, script addresses, the load-more link, a JSON answer) are
 *   rewritten so no link, form action or script string addresses dse with "?key=", every link keeps its parameters,
 *   and GET forms are posted with the marker field dse/dashboard redirects.
 * - The main database is named by its key: no login goes into a view parameter.
 *
 * Skips when extension/sevenx_dse is not installed; SEVENX_DSE_EXTENSION_DIR=<dir> checks a clone instead.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/sevenx_dse/SevenxDseViewParametersTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SevenxDseViewParametersTest extends PHPUnit\Framework\TestCase
{
    const BASE = '/admin/dse/dashboard';

    private static $ext;

    public static function setUpBeforeClass(): void
    {
        self::$ext = getenv( 'SEVENX_DSE_EXTENSION_DIR' ) ?: dirname( __DIR__, 4 ) . '/extension/sevenx_dse';
    }

    protected function setUp(): void
    {
        if ( !is_file( self::$ext . '/classes/sevenxdseviewparameters.php' ) )
        {
            $this->markTestSkipped( 'extension/sevenx_dse with sevenxdseviewparameters.php is not installed' );
        }
        if ( !class_exists( 'sevenxDseViewParameters', false ) )
        {
            require_once self::$ext . '/classes/sevenxdseviewparameters.php';
        }
    }

    /** The parameters of a rewritten address, as dse/dashboard reads them */
    private function params( $url )
    {
        $this->assertStringStartsWith( self::BASE, $url );
        return sevenxDseViewParameters::fromPath( substr( $url, strlen( self::BASE ) ) );
    }

    public function testValuesRoundTrip()
    {
        foreach ( array( 'main', '', 'var/storage/sqlite3/sqlite.db', '~x', '..', '.', 'a b&c=d', '0', 'ü/ß', '(x)' ) as $value )
        {
            $segment = sevenxDseViewParameters::encodeValue( $value );
            $this->assertDoesNotMatchRegularExpression( '~[/?&#%=()]~', $segment, $value );
            $this->assertNotContains( $segment, array( '.', '..' ) );
            $this->assertSame( $value, sevenxDseViewParameters::decodeValue( $segment ) );
        }
        $this->assertSame( 'main', sevenxDseViewParameters::encodeValue( 'main' ) );
        $this->assertSame( '~', sevenxDseViewParameters::encodeValue( '' ) );
    }

    public function testQueryBecomesViewParametersAndBack()
    {
        $query = 'sqlite=main&username=&db=var%2Fstorage%2Fsqlite3%2Fsqlite.db&select=ezsection'
            . '&where%5B0%5D%5Bcol%5D=name&where%5B0%5D%5Bop%5D=LIKE&where%5B0%5D%5Bval%5D=%25a%2Fb%25&columns%5B%5D=id&page=2';
        $path = sevenxDseViewParameters::queryToPath( $query );
        $this->assertStringStartsWith( '/(qs)/~', $path, 'bracketed names travel together, first' );
        $this->assertStringContainsString( '/(sqlite)/main/(username)/~/(db)/~', $path );
        $this->assertStringEndsWith( '/(page)/2', $path, 'page stays last for the load-more link' );
        $this->assertStringNotContainsString( '?', $path );
        $this->assertStringNotContainsString( '%', $path );

        parse_str( $query, $expected );
        $this->assertEquals( $expected, sevenxDseViewParameters::fromPath( $path ), 'the same parameters (the bundle comes first)' );

        // A script appends parameters to a link it found on the page.
        $tail = false;
        $params = sevenxDseViewParameters::fromPath( $path . '&page=3&script=db', $tail );
        $this->assertTrue( $tail );
        $this->assertSame( '3', $params['page'] );
        $this->assertSame( 'db', $params['script'] );
        $this->assertSame( 'var/storage/sqlite3/sqlite.db', $params['db'] );
    }

    public function testRequestSplitsAtTheView()
    {
        $this->assertSame( array( '/admin/dse/dashboard', '/(sqlite)/main', 'x=1' ),
            sevenxDseViewParameters::splitRequest( '/admin/dse/dashboard/(sqlite)/main?x=1' ) );
        $this->assertSame( array( '/dse/dashboard', '', '' ), sevenxDseViewParameters::splitRequest( '/dse/dashboard' ) );
    }

    public function testDashboardAndTablePagesHaveNoQueryLinkToDse()
    {
        $me = self::BASE . '?sqlite=main&amp;username=&amp;db=var%2Fstorage%2Fsqlite3%2Fsqlite.db&amp;';
        $meJs = str_replace( '/', '\\/', self::BASE ) . '?sqlite=main&username=&db=var%2Fstorage%2Fsqlite3%2Fsqlite.db&';

        // The database page of the dashboard: navigation, table list, db selector (a GET form), the row counts script.
        $dashboard = "<div id='menu'><p class='links'><a href='{$me}sql='>SQL command</a> <a href='{$me}dump='>Export</a>"
            . "<a href='{$me}create='>Create table</a></p>"
            . "<div class='db-selector'><form action=''><input type='hidden' name='sqlite' value='main'>"
            . "<input type='hidden' name='username' value=''><input id='database-select' class='input' name='db' value='var/storage/sqlite3/sqlite.db'></form></div>"
            . "<ul id='tables'><li><a href=\"{$me}select=ezsection\" class='select'>select</a> <a href=\"{$me}table=ezsection\">ezsection</a></li></ul>"
            . "<form action='' method='post'><input type='hidden' name='token' value='1'></form>"
            . "<script nonce='n'>ajaxSetHtml('{$meJs}script=db');</script>"
            . "<script nonce='n'>const jushLinks = { sqlite: [ '{$meJs}table=\$&', /\\b(ezsection)\\b/g ] };</script>"
            . "<script nonce='n'>verifyVersion('{$meJs}', 'tok');</script>"
            . "<div class='footer'><form action='' method='post'><input type='submit' name='logout' value='Logout'></form></div>";

        // A table page (select): the GET search form, sort links, paging and load more, an edit link with a where.
        $table = "<form action='' id='form'><div><input type='hidden' name='sqlite' value='main'><input type='hidden' name='username' value=''>"
            . "<input type='hidden' name='db' value='var/storage/sqlite3/sqlite.db'><input type='hidden' name='select' value='ezsection'></div>"
            . "<input type='search' name='where[0][val]'><input type='submit' value='Select'></form>"
            . "<a href='{$me}select=ezsection&amp;order%5B0%5D=name&amp;desc%5B0%5D=1'>name</a>"
            . "<a href='{$me}edit=ezsection&amp;where%5Bid%5D=3'>edit</a>"
            . "<a href='{$me}select=ezsection&amp;page=1' class='loadmore'>Load more data</a>"
            . "<form action='' method='post' enctype='multipart/form-data'><input type='submit' name='export' value='Export'></form>"
            . "<script nonce='n'>ajax('{$meJs}script=kill', function() {});</script>";

        // A script's JSON answer with an HTML fragment.
        $json = json_encode( array( 'tables' => "<a href='" . self::BASE . "?sqlite=main&amp;username=&amp;db=x&amp;select=t'>t</a>" ) );

        foreach ( array( 'dashboard' => $dashboard, 'table' => $table, 'json' => $json ) as $page => $html )
        {
            $out = sevenxDseViewParameters::rewriteHtml( $html, self::BASE );
            $this->assertStringNotContainsString( 'dashboard?', $out, $page . ': no ?key= address of dse' );
            $this->assertStringNotContainsString( 'dashboard\\/?', $out, $page );
            $this->assertDoesNotMatchRegularExpression( '~dashboard[^\'"\s<>]*\?~', $out, $page );
            foreach ( array( 'password', 'Password' ) as $credential )
            {
                $this->assertDoesNotMatchRegularExpression( '~\(' . $credential . '\)~', $out, $page . ': no login in a view parameter' );
            }
            // GET forms post the marker; POST forms stay as they are.
            $this->assertSame( substr_count( $html, '<form' ), substr_count( $out, '<form' ) );
            $this->assertSame( substr_count( $out, "<form" ), substr_count( $out, 'method="post"' ) + substr_count( $out, "method='post'" ), $page . ': every form posts' );
        }

        $out = sevenxDseViewParameters::rewriteHtml( $dashboard, self::BASE );
        $this->assertSame( 1, substr_count( $out, 'name="' . sevenxDseViewParameters::GET_FORM . '"' ), 'the db selector (a GET form) posts the marker' );
        $this->assertSame( 1, preg_match( '~href="(' . preg_quote( self::BASE, '~' ) . '/[^"]*)">ezsection</a>~', $out, $m ) );
        $this->assertSame( array( 'sqlite' => 'main', 'username' => '', 'db' => 'var/storage/sqlite3/sqlite.db', 'table' => 'ezsection' ),
            $this->params( html_entity_decode( $m[1] ) ) );
        // Script prefixes keep what the script appends readable: '...&' + 'script=db', '...&table=$&'.
        $this->assertStringContainsString( "ajaxSetHtml('\\/admin\\/dse\\/dashboard\\/(sqlite)\\/main\\/(username)\\/~\\/(db)\\/~", $out );
        $this->assertSame( 1, preg_match( "~jushLinks = \{ sqlite: \[ '([^']+)'~", $out, $m ) );
        $this->assertStringEndsWith( '&table=$&', $m[1] );
        $this->assertSame( 1, preg_match( "~verifyVersion\('([^']+)'~", $out, $m ) );
        $this->assertStringEndsWith( '&', $m[1] );
        $this->assertSame( 'main', $this->params( str_replace( '\\/', '/', $m[1] ) . 'script=version' )['sqlite'] );

        $out = sevenxDseViewParameters::rewriteHtml( $table, self::BASE );
        $this->assertSame( 1, preg_match( '~href=\'([^\']+)\'>edit</a>~', $out, $m ) );
        $this->assertSame( array( 'id' => '3' ), $this->params( html_entity_decode( $m[1] ) )['where'] );
        $this->assertSame( 1, preg_match( '~href=\'([^\']+)\' class=\'loadmore\'~', $out, $m ) );
        $this->assertStringEndsWith( '/(page)/1', $m[1], 'the load-more script counts up the trailing page number' );

        $out = sevenxDseViewParameters::rewriteHtml( $json, self::BASE );
        $decoded = json_decode( $out, true );
        $this->assertIsArray( $decoded );
        $this->assertSame( 1, preg_match( "~href='([^']+)'~", $decoded['tables'], $m ) );
        $this->assertSame( 't', $this->params( html_entity_decode( $m[1] ) )['select'] );
    }

    public function testRedirectsAndOtherAddressesAreRewrittenOrLeftAlone()
    {
        $this->assertSame( self::BASE . '/(sqlite)/main/(username)/~/(db)/~' . rtrim( strtr( base64_encode( 'var/storage/sqlite3/sqlite.db' ), '+/', '-_' ), '=' ),
            sevenxDseViewParameters::rewriteLocation( self::BASE . '?sqlite=main&username=&db=var%2Fstorage%2Fsqlite3%2Fsqlite.db', self::BASE ) );
        $this->assertSame( '/admin/content/view/full/2?x=1', sevenxDseViewParameters::rewriteUrl( '/admin/content/view/full/2?x=1', self::BASE ) );
        $this->assertSame( self::BASE, sevenxDseViewParameters::rewriteUrl( self::BASE, self::BASE ) );
    }
}
