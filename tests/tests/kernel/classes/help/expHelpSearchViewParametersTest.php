<?php
/**
 * exphelp/search with view parameters (doc/features/6.0/help-system.md, "Search"): the canonical address
 * (expHelp::searchUrl()), the parsing and checking of view parameters and GET values, the redirect of GET form
 * requests (never for JSON), facet and pager links, and that module.php declares the parameters. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpSearchViewParametersTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Exphelp\Search;

class expHelpSearchViewParametersTest extends PHPUnit\Framework\TestCase
{
    private static $known = array( 'category' => array( 'layouts', 'editing', 'getting-started' ),
                                   'audience' => array( 'editor', 'admin', 'developer', 'user' ),
                                   'type' => array( 'help', 'guide', 'feature' ) );

    public function testCanonicalAddressOrderAndEncoding()
    {
        $this->assertSame( '/exphelp/search', expHelp::searchUrl( array() ) );
        $this->assertSame( '/exphelp/search/(q)/upload/(category)/layouts/(audience)/editor/(type)/help/(offset)/20',
            expHelp::searchUrl( array( 'offset' => 20, 'type' => 'help', 'audience' => array( 'editor' ), 'category' => 'layouts', 'q' => 'upload' ) ) );
        $this->assertSame( '/exphelp/search/(q)/upload+image', expHelp::searchUrl( array( 'q' => "  upload \t image " ) ) );
        // "/", "(" and ")" would split or start a parameter: spaces; everything else percent-encoded
        $this->assertSame( '/exphelp/search/(q)/a+b+c', expHelp::searchUrl( array( 'q' => 'a/(b)c' ) ) );
        $this->assertSame( '/exphelp/search/(q)/C%2B%2B+%3Cscript%3E+%22x%22', expHelp::searchUrl( array( 'q' => 'C++ <script> "x"' ) ) );
        $this->assertSame( '/exphelp/search/(q)/%C3%BCbersicht', expHelp::searchUrl( array( 'q' => 'übersicht' ) ) );
        // offset 0, the default limit, empty and malformed facet values are left out
        $this->assertSame( '/exphelp/search/(audience)/admin,developer/(limit)/10',
            expHelp::searchUrl( array( 'offset' => 0, 'limit' => 10, 'audience' => 'admin,developer,<x>', 'category' => '', 'type' => array() ) ) );
        $this->assertSame( '/exphelp/search', expHelp::searchUrl( array( 'limit' => 20, 'q' => ' ' ) ) );
        $this->assertSame( 200, mb_strlen( expHelp::searchWords( str_repeat( 'ä', 500 ) ) ) );
        $this->assertSame( '', expHelp::searchWords( array( 'x' ) ) );
    }

    public function testViewParametersAreRead()
    {
        $params = array( 'Query' => 'upload+image', 'Category' => 'layouts', 'Audience' => false, 'Type' => false,
                         'Offset' => '20', 'Limit' => false, 'Format' => false, 'UserParameters' => array( 'audience' => 'editor' ) );
        $view = Search::viewParameters( $params );
        $this->assertSame( 'upload image', $view['q'] );
        $this->assertSame( 'layouts', $view['category'] );
        $this->assertSame( 'editor', $view['audience'], 'falls back to UserParameters' );
        $this->assertFalse( $view['type'] );

        $state = Search::state( $view, self::$known );
        $this->assertSame( array( 'q' => 'upload image', 'category' => array( 'layouts' ), 'audience' => array( 'editor' ),
                                  'type' => array(), 'limit' => 20, 'offset' => 20 ), $state );
        $this->assertSame( '/exphelp/search/(q)/upload+image/(category)/layouts/(audience)/editor/(offset)/20', expHelp::searchUrl( $state ) );
    }

    public function testOnlyKnownFacetValuesAndBoundedNumbers()
    {
        $state = Search::state( array( 'q' => str_repeat( 'a', 5000 ), 'category' => 'LAYOUTS,../settings,<script>,layouts,nope',
                                       'audience' => 'editor,admin', 'type' => array( 'help' ), 'offset' => '-5', 'limit' => '999' ), self::$known );
        $this->assertSame( 200, mb_strlen( $state['q'] ) );
        $this->assertSame( array( 'layouts' ), $state['category'] );
        $this->assertSame( array( 'editor', 'admin' ), $state['audience'] );
        $this->assertSame( array(), $state['type'], 'not a string' );
        $this->assertSame( 0, $state['offset'] );
        $this->assertSame( 50, $state['limit'] );
        $state = Search::state( array( 'offset' => '99999999', 'limit' => '0' ), self::$known );
        $this->assertSame( 10000, $state['offset'] );
        $this->assertSame( 1, $state['limit'] );
    }

    public function testGetFormRequestsAreRedirectedJsonNever()
    {
        $this->assertTrue( Search::redirectsGet( array( 'q' => '', 'audience' => '', 'category' => 'layouts', 'type' => '' ), 'GET' ) );
        $this->assertTrue( Search::redirectsGet( array( 'q' => 'x' ), 'head' ) );
        $this->assertFalse( Search::redirectsGet( array(), 'GET' ), 'the canonical address itself' );
        $this->assertFalse( Search::redirectsGet( array( 'unrelated' => '1' ), 'GET' ) );
        $this->assertFalse( Search::redirectsGet( array( 'q' => 'x' ), 'POST' ) );

        // the address the owner's example form request goes to
        $get = array( 'q' => '', 'audience' => '', 'category' => 'layouts', 'type' => '' );
        $state = Search::state( array_merge( Search::viewParameters( array() ), Search::getState( $get ) ), self::$known );
        $this->assertSame( '/exphelp/search/(category)/layouts', expHelp::searchUrl( $state ) );
        $get = array( 'q' => 'upload image', 'audience' => 'editor', 'offset' => '20' );
        $state = Search::state( array_merge( Search::viewParameters( array() ), Search::getState( $get ) ), self::$known );
        $this->assertSame( '/exphelp/search/(q)/upload+image/(audience)/editor/(offset)/20', expHelp::searchUrl( $state ) );

        $this->assertTrue( Search::wantsJson( array(), array( 'format' => 'json', 'q' => 'x' ), array() ) );
        $this->assertTrue( Search::wantsJson( array( 'format' => 'json' ), array(), array() ) );
        $this->assertTrue( Search::wantsJson( array(), array( 'q' => 'x' ), array( 'HTTP_ACCEPT' => 'application/json' ) ) );
        $this->assertFalse( Search::wantsJson( array(), array( 'q' => 'x' ), array( 'HTTP_ACCEPT' => 'text/html,application/json;q=0.9' ) ) );
    }

    public function testFacetLinksAndPager()
    {
        $state = Search::state( array( 'q' => 'block', 'category' => 'layouts', 'offset' => '40' ), self::$known );
        $urls = Search::facetUrls( $state, self::$known );
        $this->assertSame( '/exphelp/search/(q)/block', $urls['category'][''], 'clears the category and the offset' );
        $this->assertSame( '/exphelp/search/(q)/block/(category)/editing', $urls['category']['editing'] );
        $this->assertSame( '/exphelp/search/(q)/block/(category)/layouts/(audience)/editor', $urls['audience']['editor'] );
        $this->assertArrayNotHasKey( 'nope', $urls['type'] );

        $pager = Search::pager( $state, 95 );
        $this->assertSame( '/exphelp/search/(q)/block/(category)/layouts/(offset)/20', $pager['prev'] );
        $this->assertSame( '/exphelp/search/(q)/block/(category)/layouts/(offset)/60', $pager['next'] );
        $this->assertSame( 41, $pager['from'] );
        $this->assertSame( 60, $pager['to'] );
        $this->assertSame( array( 1, 2, 3, 4, 5 ), array_column( $pager['pages'], 'number' ) );
        $this->assertSame( array( false, false, true, false, false ), array_column( $pager['pages'], 'current' ) );
        $this->assertSame( '/exphelp/search/(q)/block/(category)/layouts', $pager['pages'][0]['url'] );

        $first = Search::pager( Search::state( array(), self::$known ), 10 );
        $this->assertFalse( $first['prev'] );
        $this->assertFalse( $first['next'] );
        $this->assertSame( array(), $first['pages'] );
    }

    public function testTheRequestOfWaveOneStillBounds()
    {
        $r = Search::request( array( 'q' => 'a', 'audience' => 'editor,admin', 'category' => '<x>', 'format' => 'json' ) );
        $this->assertSame( array( 'audience' => 'editor,admin' ), $r['filters'] );
        $this->assertSame( 'json', $r['format'] );
    }

    public function testModuleDeclaresTheViewParameters()
    {
        $Module = null;
        $ViewList = array();
        $FunctionList = array();
        include dirname( __DIR__, 5 ) . '/kernel/exphelp/module.php';
        $this->assertSame( array( 'q' => 'Query', 'category' => 'Category', 'audience' => 'Audience', 'type' => 'Type',
                                  'offset' => 'Offset', 'limit' => 'Limit', 'format' => 'Format' ), $ViewList['search']['unordered_params'] );
        $this->assertSame( Search::VIEW_PARAMETERS, $ViewList['search']['unordered_params'] );
        $this->assertSame( array( 'read' ), $ViewList['search']['functions'] );
    }

    /**
     * The canonical redirect is sent by the view itself: a module redirect gets the request's query string appended by
     * the kernel (ezpKernelWeb::appendRequestQuery()), so /search?q=x went to /search/(q)/x?q=x and redirected to itself
     * until the browser gave up.
     */
    public function testTheCanonicalRedirectDoesNotCarryTheQueryString()
    {
        $source = file_get_contents( __DIR__ . '/../../../../../kernel/private/classes/views/exphelp/search.php' );
        $this->assertStringNotContainsString( 'return $Module->redirectTo(', $source );
        $this->assertStringContainsString( "\\eZHTTPTool::redirect( \$target, array(), '302 Found', false );", $source );
        $this->assertStringContainsString( '\eZExecution::cleanExit();', $source );
        // the target never holds a query string of its own
        $state = Search::state( array_merge( Search::viewParameters( array() ), Search::getState( array( 'q' => 'img', 'category' => '' ) ) ), self::$known );
        $this->assertSame( '/exphelp/search/(q)/img', expHelp::searchUrl( $state ) );
    }
}
