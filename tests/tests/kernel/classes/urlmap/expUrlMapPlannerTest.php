<?php
/**
 * The planner of URL map imports (expUrlMapPlanner, expUrlMapPlan) and the matching of the redirect store
 * (expUrlRedirect's pure parts), without a database: the installation is a fake environment.
 *
 *  UP-01 where each kind goes: a 301 redirect to a page is a global URL alias, an alias one without a redirect, a
 *        wildcard a wildcard of its type (temporary 3), and a query, a temporary status, 308, a target with a query
 *        or on another site a redirect of exp_url_redirect
 *  UP-02 the states: new, unchanged, changed, duplicate (the first row counts), conflict (a page's own address, a
 *        module address, the front page), broken target, unknown host, not importable (the row's problems, an
 *        unknown siteaccess or language, an alias with a query or to another site, /index.php without a query)
 *  UP-03 the siteaccess: a full address's host (its URI part taken off), the option hosts, the row's own, * for every
 *        siteaccess, the chosen one for bare paths; the language: the row's, the option's, the siteaccess's; a URL
 *        alias below the siteaccess's PathPrefix (not for PathPrefixExclude[])
 *  UP-04 decisions: new is imported, changed only with on_conflict overwrite; setDecision() per row; counts; the
 *        report as JSON and CSV
 *  UP-05 expUrlRedirect: the canonical query (order ignored, + is a space, a is a=), the source key (case of the
 *        path ignored, trailing slash and dot segments normalized), match() by siteaccess before every siteaccess,
 *        the location of node, path and URL targets
 *  UP-06 sameRow() and sameEntry() of the importer, and the wildcard types
 *  UP-07 a target path as the siteaccess serves it (below its PathPrefix first, PathPrefixExclude[] honored, then
 *        as it is), and the export of an alias below the prefix in the visitor's form with its siteaccess
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUrlMapPlannerTestEnvironment implements expUrlMapEnvironment
{
    public $aliases = array();
    public $wildcards = array();
    public $redirects = array();
    public $targets = array( '/about' => array( 'type' => 'node', 'action' => 'eznode:60', 'link' => 70, 'node_id' => 60 ),
                             '/news' => array( 'type' => 'node', 'action' => 'eznode:61', 'link' => 71, 'node_id' => 61 ),
                             '/content/search' => array( 'type' => 'module', 'action' => 'module:content/search', 'link' => true, 'node_id' => 0 ),
                             'node:12' => array( 'type' => 'node', 'action' => 'eznode:12', 'link' => 22, 'node_id' => 12 ) );

    public function siteaccesses() { return array( 'site', 'ger', 'admin' ); }
    public function defaultSiteaccess() { return 'site'; }
    public function siteaccessForHost( $host, $path )
    {
        if ( $host === 'www.example.com' )
        {
            if ( preg_match( '#^/ger(/.*)?$#', $path, $m ) )
                return array( 'siteaccess' => 'ger', 'path' => isset( $m[1] ) && $m[1] !== '' ? $m[1] : '/' );
            return array( 'siteaccess' => 'site', 'path' => $path );
        }
        return null;
    }
    public function defaultLanguage( $siteaccess ) { return $siteaccess === 'ger' ? 'ger-DE' : 'eng-GB'; }
    public function languageExists( $locale ) { return in_array( $locale, array( 'eng-GB', 'ger-DE' ), true ); }
    public function resolveTarget( $target, $siteaccess, $language ) { return isset( $this->targets[$target] ) ? $this->targets[$target] : null; }
    public function aliasPath( $siteaccess, $path ) { return $siteaccess === 'ger' ? expUrlMapKernelEnvironment::prefixedPath( $path, 'de', array( 'media' ) ) : $path; }
    public function isSystemPath( $path ) { return (bool)preg_match( '#^/(content|user)(/|$)#', $path ); }
    public function existingAlias( $path ) { return isset( $this->aliases[strtolower( $path )] ) ? $this->aliases[strtolower( $path )] : null; }
    public function existingWildcard( $source ) { return isset( $this->wildcards[$source] ) ? $this->wildcards[$source] : null; }
    public function existingRedirect( $siteaccess, $path, $query )
    {
        $key = $siteaccess . '|' . expUrlRedirect::sourceKey( $path, $query );
        return isset( $this->redirects[$key] ) ? $this->redirects[$key] : null;
    }
}

class expUrlMapPlannerTest extends PHPUnit\Framework\TestCase
{
    /** @var expUrlMapPlannerTestEnvironment */
    private $env;

    /** @var string[] */
    private $files = array();

    protected function setUp(): void
    {
        $this->env = new expUrlMapPlannerTestEnvironment();
    }

    protected function tearDown(): void
    {
        foreach ( $this->files as $file )
            if ( is_file( $file ) )
                unlink( $file );
    }

    /**
     * @param array $rows lists of the row's constructor arguments
     * @param array $options
     * @return expUrlMapPlan
     */
    private function plan( array $rows, array $options = array() )
    {
        $objects = array();
        foreach ( $rows as $i => $args )
        {
            $args += array( 2 => '', 3 => '', 4 => '', 5 => '', 6 => $i + 2, 7 => '' );
            ksort( $args );
            $objects[] = new expUrlMapRow( ...$args );
        }
        return ( new expUrlMapPlanner( $this->env ) )->plan( $objects, $options );
    }

    /** @return array state, store per item */
    private static function summary( expUrlMapPlan $plan )
    {
        return array_map( function ( $item ) { return $item['state'] . ' ' . $item['store']; }, $plan->items() );
    }

    /** UP-01 */
    public function testWhereEachKindGoes()
    {
        $plan = $this->plan( array(
            array( '/about-us.html', '/about' ),
            array( '/team', '/about', 'alias' ),
            array( '/old/*', '/news/{1}' ),
            array( '/tmp/*', '/news/{1}', 'wildcard-forward-temporary' ),
            array( '/print/*', '/{1}', 'wildcard-direct' ),
            array( '/index.php?id=12&lang=en', 'node:12' ),
            array( '/promo', '/about', 'redirect-temporary' ),
            array( '/moved', '/about', 'redirect', '', '', '', 0, '308' ),
            array( '/search-old', '/content/search?q=x' ),
            array( '/shop', 'https://shop.example.org/start' ),
        ) );
        $this->assertSame( array( 'new alias', 'new alias', 'new wildcard', 'new wildcard', 'new wildcard', 'new redirect', 'new redirect', 'new redirect',
                                  'new redirect', 'new redirect' ), self::summary( $plan ) );
        $items = $plan->items();
        $this->assertSame( array( 'path' => '/about-us.html', 'action' => 'eznode:60', 'link' => 70, 'redirects' => true, 'language' => 'eng-GB' ), $items[0]['write'] );
        $this->assertSame( 301, $items[0]['status'] );
        $this->assertFalse( $items[1]['write']['redirects'] );
        $this->assertSame( 0, $items[1]['status'] );
        $this->assertSame( array( 'source_url' => 'old/*', 'destination_url' => 'news/{1}', 'type' => eZURLWildcard::TYPE_FORWARD ), $items[2]['write'] );
        $this->assertSame( eZURLWildcard::TYPE_FORWARD_TEMPORARY, $items[3]['write']['type'] );
        $this->assertSame( 302, $items[3]['status'] );
        $this->assertSame( eZURLWildcard::TYPE_DIRECT, $items[4]['write']['type'] );
        $this->assertSame( array( 'path' => '/index.php', 'query' => 'id=12&lang=en', 'target' => 'node:12', 'status' => 301, 'siteaccess' => 'site', 'language' => 'eng-GB' ),
                           $items[5]['write'] );
        $this->assertSame( 302, $items[6]['write']['status'] );
        $this->assertSame( 308, $items[7]['write']['status'] );
        $this->assertSame( '/content/search?q=x', $items[8]['write']['target'] );
        $this->assertSame( 'https://shop.example.org/start', $items[9]['write']['target'] );
        // a wildcard answers 301 or 302: 307 becomes 302, with a message
        $plan = $this->plan( array( array( '/w/*', '/news/{1}', 'wildcard-forward-temporary', '', '', '', 0, '307' ) ) );
        $this->assertSame( 302, $plan->items()[0]['status'] );
        $this->assertStringContainsString( '307 is answered as 302', implode( ' ', $plan->items()[0]['messages'] ) );
    }

    /** UP-02 */
    public function testStates()
    {
        $this->env->aliases['/existing'] = array( 'action' => 'eznode:60', 'alias_redirects' => 1, 'is_alias' => 1, 'page' => false );
        $this->env->aliases['/other'] = array( 'action' => 'eznode:99', 'alias_redirects' => 1, 'is_alias' => 1, 'page' => false );
        $this->env->aliases['/a-page'] = array( 'action' => 'eznode:5', 'alias_redirects' => 0, 'is_alias' => 0, 'page' => true );
        $this->env->wildcards['w/*'] = array( 'id' => 1, 'source_url' => 'w/*', 'destination_url' => 'news/{1}', 'type' => 1 );
        $this->env->wildcards['v/*'] = array( 'id' => 2, 'source_url' => 'v/*', 'destination_url' => 'x/{1}', 'type' => 1 );
        $this->env->redirects['site|' . expUrlRedirect::sourceKey( '/p', 'b=2&a=1' )] = array( 'target' => 'node:12', 'status' => 301 );
        $plan = $this->plan( array(
            array( '/existing', '/about' ),                    // unchanged
            array( '/other', '/about' ),                       // changed
            array( '/w/*', '/news/{1}' ),                       // unchanged
            array( '/v/*', '/news/{1}' ),                       // changed
            array( '/p?a=1&b=2', 'node:12' ),                   // unchanged: the order of the query does not count
            array( '/p?a=1', 'node:12' ),                       // new
            array( '/P?a=1', '/about' ),                        // duplicate of the row before (path case ignored)
            array( '/a-page', '/about' ),                       // conflict: a page
            array( '/content/view/full/5', '/about' ),          // conflict: a module address
            array( '/', '/about' ),                             // conflict: the front page
            array( '/?p=12', '/about' ),                        // new: the front page with a query
            array( '/gone', '/does-not-exist' ),                // broken target
            array( 'https://old.example.net/x', '/about' ),     // unknown host
            array( '', '/about' ),                              // not importable: the row's own problem
            array( '/x', '/about', '', 'nosuchsite' ),          // not importable: siteaccess
            array( '/x', '/about', '', '', 'fre-FR' ),          // not importable: language
            array( '/x?y=1', '/about', 'alias' ),               // not importable: alias with a query
            array( '/x', 'https://shop.example.org/', 'alias' ), // not importable: alias to another site
            array( '/index.php', '/about' ),                    // not importable: the front controller
            array( '/index.php?id=1', '/about' ),               // new
        ) );
        $this->assertSame( array( 'unchanged', 'changed', 'unchanged', 'changed', 'unchanged', 'new', 'duplicate', 'conflict', 'conflict', 'conflict', 'new',
                                  'broken-target', 'unknown-host', 'not-importable', 'not-importable', 'not-importable', 'not-importable', 'not-importable',
                                  'not-importable', 'new' ),
                           array_column( $plan->items(), 'state' ) );
        $items = $plan->items();
        $this->assertStringContainsString( 'line 7', implode( ' ', $items[6]['messages'] ) );
        $this->assertStringContainsString( 'a page of this site', implode( ' ', $items[7]['messages'] ) );
        $this->assertSame( '', $items[7]['store'] );
        $this->assertNull( $items[11]['write'] );
        $this->assertStringContainsString( 'eznode:99', implode( ' ', $items[1]['messages'] ) );
    }

    /** UP-03 */
    public function testSiteaccessAndLanguage()
    {
        $plan = $this->plan( array(
            array( 'https://www.example.com/ger/ueber-uns', '/about' ),
            array( 'https://www.example.com/team', '/about' ),
            array( 'https://old-site.example/team', '/about' ),
            array( '/bare', '/about' ),
            array( '/bare2', '/about', '', 'ger' ),
            array( '/bare3', '/about', '', '', 'ger-DE' ),
            array( '/every?x=1', '/about', '', '*' ),
            array( 'https://www.example.com/news-old', 'https://www.example.com/news' ),
        ), array( 'hosts' => array( 'OLD-SITE.example' => 'ger' ) ) );
        $items = $plan->items();
        $this->assertSame( array( 'ger', 'site', 'ger', 'site', 'ger', 'site', '*', 'site' ), array_column( $items, 'siteaccess' ) );
        $this->assertSame( array( 'ger-DE', 'eng-GB', 'ger-DE', 'eng-GB', 'ger-DE', 'ger-DE', 'eng-GB', 'eng-GB' ), array_column( $items, 'language' ) );
        $this->assertSame( '/de/ueber-uns', $items[0]['write']['path'], 'below the PathPrefix of the siteaccess' );
        $this->assertSame( array( '/de/x', '/de/x', '/media/x', '/de', '/Other' ), array(
            expUrlMapKernelEnvironment::prefixedPath( '/x', 'de', array() ), expUrlMapKernelEnvironment::prefixedPath( '/de/x', '/de/', array() ),
            expUrlMapKernelEnvironment::prefixedPath( '/media/x', 'de', array( 'Media' ) ), expUrlMapKernelEnvironment::prefixedPath( '/', 'de', array() ),
            expUrlMapKernelEnvironment::prefixedPath( '/Other', '', array() ) ) );
        $this->assertSame( '', $items[6]['write']['siteaccess'], 'a redirect of every siteaccess is stored without one' );
        // a full target on a host of this installation is a path of this site
        $this->assertSame( 'alias', $items[7]['store'] );
        $this->assertSame( 'eznode:61', $items[7]['write']['action'] );
        // the chosen siteaccess and language for bare paths
        $plan = $this->plan( array( array( '/bare', '/about' ) ), array( 'siteaccess' => 'ger', 'language' => 'eng-GB' ) );
        $this->assertSame( array( 'ger', 'eng-GB' ), array( $plan->items()[0]['siteaccess'], $plan->items()[0]['language'] ) );
        $this->expectException( InvalidArgumentException::class );
        $this->plan( array(), array( 'on_conflict' => 'merge' ) );
    }

    /** UP-07 */
    public function testTargetPathsBelowThePathPrefix()
    {
        // the siteaccess ger has PathPrefix=de (PathPrefixExclude[]=media): its page /recipes is stored as /de/recipes
        $this->env->targets['/de/recipes'] = array( 'type' => 'node', 'action' => 'eznode:80', 'link' => 90, 'node_id' => 80 );
        $this->env->targets['/media/logo'] = array( 'type' => 'node', 'action' => 'eznode:81', 'link' => 91, 'node_id' => 81 );
        $plan = $this->plan( array(
            array( '/about-us.html', '/recipes', '', 'ger' ),      // the visitor's address of the page
            array( '/old-recipes', '/de/recipes', '', 'ger' ),     // the stored path still resolves
            array( '/old-about', '/about', '', 'ger' ),            // a page outside the prefix resolves as it is
            array( '/old-logo', '/media/logo', '', 'ger' ),        // an excluded first element is not prefixed
            array( '/elsewhere', '/recipes' ),                     // the siteaccess site has no prefix
            array( '/every', '/recipes', 'redirect-temporary', '*' ), // a row of every siteaccess has none either
        ) );
        $items = $plan->items();
        $this->assertSame( array( 'new alias', 'new alias', 'new alias', 'new alias', 'broken-target ', 'broken-target ' ), self::summary( $plan ) );
        $this->assertSame( array( 'eznode:80', 'eznode:80', 'eznode:60', 'eznode:81' ), array_column( array_column( array_slice( $items, 0, 4 ), 'write' ), 'action' ) );
        $this->assertSame( '/de/about-us.html', $items[0]['write']['path'] );

        // the export writes an alias below the prefix the way visitors use it, with its siteaccess, so importing it
        // again stores it at the same place
        $this->assertSame( array( '/x/y', null, null, null, null, null ), array(
            expUrlMapKernelEnvironment::unprefixedPath( '/de/x/y', 'de', array() ), expUrlMapKernelEnvironment::unprefixedPath( '/de', 'de', array() ),
            expUrlMapKernelEnvironment::unprefixedPath( '/other/x', 'de', array() ), expUrlMapKernelEnvironment::unprefixedPath( '/de/media/x', 'de', array( 'Media' ) ),
            expUrlMapKernelEnvironment::unprefixedPath( '/de/de/x', 'de', array() ), expUrlMapKernelEnvironment::unprefixedPath( '/de/x', '', array() ) ) );
        $exported = expUrlMapExporter::visitorRow( new expUrlMapRow( '/de/about-us.html', 'node:80', 'redirect' ), 'ger', 'de', array( 'media' ) );
        $this->assertSame( array( '/about-us.html', 'ger' ), array( $exported->oldUrl(), $exported->siteaccess() ) );
        $this->assertSame( '/media/x', expUrlMapExporter::visitorRow( new expUrlMapRow( '/media/x', 'node:80' ), 'ger', 'de', array( 'media' ) )->oldUrl() );
        $this->assertSame( '/de/x', expUrlMapExporter::visitorRow( new expUrlMapRow( '/de/x', 'node:80' ), '', 'de', array() )->oldUrl() );
        $again = $this->plan( array( array( $exported->oldUrl(), '/recipes', 'redirect', $exported->siteaccess() ) ) )->items()[0];
        $this->assertSame( '/de/about-us.html', $again['write']['path'], 'the round trip stores the alias at the same place' );
    }

    /** UP-04 */
    public function testDecisionsCountsAndReport()
    {
        $this->env->aliases['/other'] = array( 'action' => 'eznode:99', 'alias_redirects' => 1, 'is_alias' => 1, 'page' => false );
        $rows = array( array( '/new', '/about' ), array( '/other', '/about' ), array( '/gone', '/nothing' ) );
        $plan = $this->plan( $rows );
        $this->assertSame( array( 'create', 'none', 'none' ), array_column( $plan->items(), 'action' ) );
        $plan = $this->plan( $rows, array( 'on_conflict' => 'overwrite' ) );
        $this->assertSame( array( 'create', 'update', 'none' ), array_column( $plan->items(), 'action' ) );
        $this->assertTrue( $plan->setDecision( 1, 'skip' ) );
        $this->assertFalse( $plan->setDecision( 2, 'import' ), 'a broken target cannot be imported' );
        $this->assertFalse( $plan->setDecision( 9, 'skip' ) );
        $this->assertSame( array( 'create', 'none', 'none' ), array_column( $plan->items(), 'action' ) );
        $this->assertCount( 1, $plan->actionable() );
        $counts = $plan->counts();
        $this->assertSame( 3, $counts['rows'] );
        $this->assertSame( array( 1, 1, 1, 1, 0, 2 ), array( $counts['new'], $counts['changed'], $counts['broken-target'], $counts['create'], $counts['update'], $counts['skip'] ) );
        $this->assertCount( 1, $plan->itemsIn( 'broken-target' ) );

        $json = expSpreadsheetWriter::temporaryFile();
        $this->files[] = $json;
        $plan->writeReport( $json, 'json' );
        $decoded = json_decode( file_get_contents( $json ), true );
        $this->assertSame( expUrlMapPlan::SCHEMA, $decoded['schema'] );
        $this->assertSame( $plan->counts(), $decoded['counts'] );
        $this->assertCount( 3, $decoded['items'] );
        $csv = expSpreadsheetWriter::temporaryFile() . '.csv';
        $this->files[] = $csv;
        $plan->writeReport( $csv );
        $lines = explode( "\r\n", trim( file_get_contents( $csv ) ) );
        $this->assertSame( "\xEF\xBB\xBF" . implode( ',', expUrlMapPlan::reportColumns() ), $lines[0] );
        $this->assertStringContainsString( 'broken-target', $lines[3] );
    }

    /** UP-05 */
    public function testRedirectMatching()
    {
        $this->assertSame( 'a=1&b=2&b=3&c=', expUrlRedirect::canonicalQuery( '?b=3&c&a=1&b=2' ) );
        $this->assertSame( 'q=a%20b', expUrlRedirect::canonicalQuery( 'q=a+b' ) );
        $this->assertSame( expUrlRedirect::canonicalQuery( 'q=a%20b' ), expUrlRedirect::canonicalQuery( 'q=a+b' ) );
        $this->assertSame( '', expUrlRedirect::canonicalQuery( '&&' ) );
        $this->assertSame( expUrlRedirect::sourceKey( '/Old/Page/', 'b=1&a=2' ), expUrlRedirect::sourceKey( '/old/./page', 'a=2&b=1' ) );
        $this->assertNotSame( expUrlRedirect::sourceKey( '/old', '' ), expUrlRedirect::sourceKey( '/old', 'a=1' ) );
        $this->assertNotSame( expUrlRedirect::sourceKey( '/old', 'a=1' ), expUrlRedirect::sourceKey( '/old', 'a=2' ) );
        $this->assertSame( expUrlRedirect::sourceKey( '/über', '' ), expUrlRedirect::sourceKey( '/%C3%BCber', '' ) );

        $key = expUrlRedirect::sourceKey( '/index.php', 'id=12' );
        $index = array( $key => array(
            array( 'id' => 1, 'siteaccess' => '', 'language' => '', 'target' => '/everywhere', 'status' => 301 ),
            array( 'id' => 2, 'siteaccess' => 'ger', 'language' => 'ger-DE', 'target' => 'node:12', 'status' => 302 ),
        ) );
        $this->assertSame( 2, expUrlRedirect::match( 'ger', array( '/index.php' ), 'id=12', $index )['id'] );
        $this->assertSame( 1, expUrlRedirect::match( 'site', array( '/index.php' ), '?id=12', $index )['id'] );
        $this->assertNull( expUrlRedirect::match( 'site', array( '/index.php' ), '', $index ) );
        $this->assertNull( expUrlRedirect::match( 'site', array( '/index.php' ), 'id=13', $index ) );
        // the whole path or the path inside the siteaccess
        $this->assertSame( 1, expUrlRedirect::match( 'site', array( '/eng/x', '/index.php' ), 'id=12', $index )['id'] );
        $this->assertNull( expUrlRedirect::match( 'site', array(), 'id=12', $index ) );

        $this->assertSame( 'content/view/full/12', expUrlRedirect::location( 'node:12' ) );
        $this->assertSame( 'about/team?x=1', expUrlRedirect::location( '/about/team?x=1' ) );
        $this->assertSame( 'https://shop.example.org/', expUrlRedirect::location( 'https://shop.example.org/' ) );
    }

    /** UP-06 */
    public function testImporterComparisonsAndWildcardTypes()
    {
        $this->assertTrue( expUrlMapImporter::sameRow( array( 'a' => 1, 'b' => 'x' ), array( 'b' => 'x', 'a' => '1' ) ) );
        $this->assertFalse( expUrlMapImporter::sameRow( array( 'a' => 1 ), array( 'a' => 2 ) ) );
        $this->assertTrue( expUrlMapImporter::sameRow( null, null ) );
        $this->assertFalse( expUrlMapImporter::sameRow( null, array() ) );
        $this->assertTrue( expUrlMapImporter::sameEntry( array( 'action' => 'eznode:2', 'page' => false ), array( 'action' => 'eznode:2' ) ) );
        $this->assertFalse( expUrlMapImporter::sameEntry( array( 'action' => 'eznode:2' ), array( 'action' => 'eznode:3' ) ) );
        $this->assertFalse( expUrlMapImporter::sameEntry( null, array( 'action' => 'eznode:3' ) ) );

        $this->assertSame( array( 301, 302, 0, 0 ), array_map( array( 'eZURLWildcard', 'redirectStatusOfType' ), array( 1, 3, 2, 0 ) ) );
        $this->assertSame( array( 'forward', 'forward-temporary', 'direct', 'none' ), array_map( array( 'eZURLWildcard', 'typeName' ), array( 1, 3, 2, 0 ) ) );
        $this->assertTrue( eZURLWildcard::isForwardType( '3' ) );
        $this->assertFalse( eZURLWildcard::isForwardType( 2 ) );
    }
}
