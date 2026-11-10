<?php
/**
 * The URL map screens of the administration (content/urlimport, content/urlimporthistory, content/urlexport,
 * content/urlredirects) and the store of an import between its requests (expUrlMapImportSession), without a
 * database.
 *
 *  UV-01 the store keeps the file, the options and the plan with the decisions taken; only the user who made it opens
 *        it, an expired one or a token of the wrong shape is not opened; a dry run of an earlier plan is forgotten
 *  UV-02 the options of a form are checked (conflict policy, siteaccess, language, hosts), and the old hosts of a text
 *        area are read as host => siteaccess
 *  UV-03 the preview: the state filter, a page of rows (an offset past the end is the first page), the rows that
 *        can be ticked, the hosts the planner did not know
 *  UV-04 the ticks of a page: a shown row that can be imported is imported when ticked and skipped when not, other
 *        rows keep their decision, posted values that are not whole numbers are ignored
 *  UV-05 the export's options and file name, the ids a removal of redirects takes, what an entry of an import leads to
 *  UV-06 the module: the views, their policy functions and view parameters, and the function urltranslator_import
 *  UV-07 the helper tabs: the form of a helper within its limits, the tab addresses, the site address the test after
 *        an import requests, the kept results of an import (dry run, import, test) and its rows
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expUrlMapPlannerTest.php';

use Exponential\View\Kernel\Content\UrlImport;
use Exponential\View\Kernel\Content\UrlImportHistory;
use Exponential\View\Kernel\Content\UrlExport;
use Exponential\View\Kernel\Content\UrlRedirects;

/** The store under a directory of the test's own */
class expUrlMapAdminViewsTestSession extends expUrlMapImportSession
{
    /** @var string */
    public static $base = '';

    public static function baseDirectory()
    {
        return self::$base;
    }
}

class expUrlMapAdminViewsTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        expUrlMapAdminViewsTestSession::$base = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-urlimport-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
    }

    protected function tearDown(): void
    {
        $base = expUrlMapAdminViewsTestSession::$base;
        foreach ( is_dir( $base ) ? (array)glob( $base . '/*/*' ) : array() as $file )
            unlink( $file );
        foreach ( is_dir( $base ) ? (array)glob( $base . '/*', GLOB_ONLYDIR ) : array() as $dir )
            rmdir( $dir );
        if ( is_dir( $base ) )
            rmdir( $base );
    }

    /** @return expUrlMapPlanner */
    private static function planner()
    {
        $env = new expUrlMapPlannerTestEnvironment();
        $env->wildcards['old/*'] = array( 'id' => 5, 'source_url' => 'old/*', 'destination_url' => 'elsewhere/{1}', 'type' => 1 );
        return new expUrlMapPlanner( $env );
    }

    const CSV = "old_url,new_url\n/about-us.html,/about\nhttps://www.unknown.org/x,/about\n/old/*,/news/{1}\n/gone,/nothing-here\n/about-us.html,/news\n";

    /** UV-01 */
    public function testTheStoreKeepsThePlanForItsUser()
    {
        $session = expUrlMapAdminViewsTestSession::create( 14, self::CSV, 'old.csv', 'csv', array( 'siteaccess' => 'site', 'on_conflict' => 'skip' ) );
        $this->assertTrue( expUrlMapImportSession::isToken( $session->token() ) );
        $this->assertNull( expUrlMapAdminViewsTestSession::load( $session->token(), 15 ), 'another user' );
        $this->assertNull( expUrlMapAdminViewsTestSession::load( '../' . $session->token(), 14 ) );
        $this->assertNull( expUrlMapAdminViewsTestSession::load( strtoupper( $session->token() ), 14 ) );

        $plan = $session->makePlan( null, self::planner() );
        $this->assertSame( array( 'new', 'unknown-host', 'changed', 'broken-target', 'duplicate' ), array_column( $plan->items(), 'state' ) );
        $this->assertTrue( $plan->setDecision( 0, 'skip' ) );
        $session->savePlan( $plan );
        $session->saveResult( 'dryrun', array( 'batch_id' => 0, 'counts' => array( 'created' => 1 ) ) );

        $again = expUrlMapAdminViewsTestSession::load( $session->token(), 14 );
        $this->assertSame( 'old.csv', $again->meta()['source_name'] );
        $this->assertSame( array( 'skip', 'skip', 'skip', 'skip', 'skip' ), array_column( $again->plan()->items(), 'decision' ), 'the decision taken is kept' );
        $this->assertSame( 1, $again->result( 'dryrun' )['counts']['created'] );
        $this->assertNull( $again->result( 'result' ) );
        $again->savePlan( $again->plan() );
        $this->assertNull( $again->result( 'dryrun' ), 'a dry run of an earlier plan no longer counts' );

        // planned again with the host mapped and overwrite: the decisions are those of the new plan
        $plan = $again->makePlan( array( 'siteaccess' => 'site', 'on_conflict' => 'overwrite', 'hosts' => array( 'www.unknown.org' => 'site' ) ), self::planner() );
        $this->assertSame( array( 'new', 'new', 'changed', 'broken-target', 'duplicate' ), array_column( $plan->items(), 'state' ) );
        $this->assertSame( array( 'import', 'import', 'import', 'skip', 'skip' ), array_column( $plan->items(), 'decision' ) );

        // expired
        $meta = json_decode( file_get_contents( expUrlMapAdminViewsTestSession::$base . '/' . $session->token() . '/meta.json' ), true );
        $meta['created'] = time() - expUrlMapImportSession::TTL - 1;
        file_put_contents( expUrlMapAdminViewsTestSession::$base . '/' . $session->token() . '/meta.json', json_encode( $meta ) );
        $this->assertNull( expUrlMapAdminViewsTestSession::load( $session->token(), 14 ) );
        $this->assertSame( 1, expUrlMapAdminViewsTestSession::purgeExpired() );
        $this->assertFalse( is_dir( expUrlMapAdminViewsTestSession::$base . '/' . $session->token() ) );
    }

    /** UV-02 */
    public function testFormOptionsAreChecked()
    {
        $this->assertSame( array( 'siteaccess' => '', 'language' => '', 'on_conflict' => 'skip', 'hosts' => array() ),
                           expUrlMapImportSession::cleanOptions( array( 'siteaccess' => '../x', 'language' => 'english', 'on_conflict' => 'drop', 'hosts' => 'a' ) ) );
        $this->assertSame( array( 'siteaccess' => 'site', 'language' => 'ger-DE', 'on_conflict' => 'overwrite', 'hosts' => array( 'www.old.com' => 'site' ) ),
                           expUrlMapImportSession::cleanOptions( array( 'siteaccess' => 'site', 'language' => 'ger-DE', 'on_conflict' => 'overwrite',
                                                                        'hosts' => array( 'WWW.Old.com ' => 'site', 'bad host' => 'site', 'x.org' => 'no site' ) ) ) );
        $this->assertSame( array( 'www.old-site.com' => 'site', 'shop.old.com:8080' => 'ger', 'b.org' => 'site' ),
                           expUrlMapImportSession::parseHosts( "www.old-site.com = site\r\nhttps://Shop.Old.com:8080/ => ger\nnot a line\nb.org=site, junk" ) );
    }

    /** UV-03 */
    public function testThePreviewPage()
    {
        $plan = self::planner()->plan( expUrlMapFormats::reader( 'csv' )->readString( self::CSV ) );
        $this->assertSame( 'all', UrlImport::stateKey( 'nonsense' ) );
        $this->assertSame( 'broken-target', UrlImport::stateKey( 'broken-target' ) );
        $this->assertSame( 'content/urlimport/(plan)/abc/(state)/new/(offset)/50', UrlImport::planURL( 'abc', 'new', 50 ) );
        $this->assertSame( 'content/urlimport/(plan)/abc', UrlImport::planURL( 'abc' ) );

        $page = UrlImport::page( $plan->items(), 'all', 0, 2 );
        $this->assertSame( 5, $page['count'] );
        $this->assertSame( array( 2, 3 ), array_column( $page['rows'], 'line' ) );
        $this->assertSame( array( true, false ), array_column( $page['rows'], 'selectable' ) );
        $page = UrlImport::page( $plan->items(), 'broken-target', 99 );
        $this->assertSame( array( 1, 0 ), array( $page['count'], $page['offset'] ), 'past the end: the first page' );
        $this->assertSame( '/gone', $page['rows'][0]['old_url'] );
        $this->assertSame( array( 'www.unknown.org' ), UrlImport::unknownHosts( $plan->items() ) );
    }

    /** UV-04 */
    public function testTheTicksOfAPage()
    {
        $plan = self::planner()->plan( expUrlMapFormats::reader( 'csv' )->readString( self::CSV ) );
        $this->assertSame( array( 'import', 'skip', 'skip', 'skip', 'skip' ), array_column( $plan->items(), 'decision' ) );
        // rows 0 to 2 shown, row 2 (changed) ticked, row 0 not: row 1 (unknown host) cannot be imported
        $this->assertSame( 2, UrlImport::applyDecisions( $plan, array( '0', '1', '2', 'x', '-' ), array( '2', '1', 'y' ) ) );
        $this->assertSame( array( 'skip', 'skip', 'import', 'skip', 'skip' ), array_column( $plan->items(), 'decision' ) );
        $this->assertSame( array( 'none', 'none', 'update', 'none', 'none' ), array_column( $plan->items(), 'action' ) );
        $this->assertSame( 0, UrlImport::applyDecisions( $plan, array( '3', '4' ), array( '3', '4' ) ), 'rows that cannot be imported keep their decision' );
        $this->assertSame( 0, UrlImport::applyDecisions( $plan, 'nonsense', null ) );
    }

    /** UV-05 */
    public function testExportRedirectsAndHistoryHelpers()
    {
        $o = UrlExport::options( array( 'Format' => 'xlsx', 'Kinds' => array( 'wildcard', 'evil', array( 'x' ) ), 'SiteAccess' => 'site', 'Language' => 'ger-DE',
                                        'IncludeNodeAliases' => '1' ), array( 'site' ), array( 'ger-DE' ) );
        $this->assertSame( array( 'format' => 'xlsx', 'kinds' => array( 'wildcard' ), 'siteaccess' => 'site', 'language' => 'ger-DE', 'include_node_aliases' => true ), $o );
        $o = UrlExport::options( array( 'Format' => 'exe', 'SiteAccess' => 'other', 'Language' => 'xx' ), array( 'site' ), array( 'ger-DE' ) );
        $this->assertSame( array( 'format' => 'csv', 'kinds' => array(), 'siteaccess' => '', 'language' => '', 'include_node_aliases' => false ), $o );
        $this->assertSame( 'url-map-2026-11-06.htaccess', UrlExport::fileName( 'apache', mktime( 12, 0, 0, 11, 6, 2026 ) ) );

        $this->assertSame( array( 7 ), UrlRedirects::removeIDs( array( 'RemoveRedirectButton' => '7', 'RedirectIDList' => array( '8' ) ) ) );
        $this->assertSame( array( 8, 9 ), UrlRedirects::removeIDs( array( 'RemoveSelectedRedirectsButton' => '1', 'RedirectIDList' => array( '8', '9', '8', '0', '-1', 'x', array() ) ) ) );
        $this->assertSame( array(), UrlRedirects::removeIDs( array( 'RedirectIDList' => array( '8' ) ) ), 'no button: nothing' );

        $this->assertSame( 'eznode:60 (301)', UrlImportHistory::targetText( 'ezurlalias_ml', array( 'action' => 'eznode:60', 'alias_redirects' => 1 ) ) );
        $this->assertSame( 'news/{1} (302)', UrlImportHistory::targetText( 'ezurlwildcard', array( 'destination_url' => 'news/{1}', 'type' => 3 ) ) );
        $this->assertSame( 'shop/{1}', UrlImportHistory::targetText( 'ezurlwildcard', array( 'destination_url' => 'shop/{1}', 'type' => 2 ) ) );
        $this->assertSame( 'node:12 (307)', UrlImportHistory::targetText( 'exp_url_redirect', array( 'target' => 'node:12', 'status' => 307 ) ) );
        $this->assertSame( '', UrlImportHistory::targetText( 'ezurlwildcard', null ) );
    }

    /** UV-06 */
    /** UV-07 */
    public function testTheHelperTabs()
    {
        $sas = array( 'site', 'ger' );
        $this->assertSame( array( 'address' => '', 'limit' => 1000, 'any_host' => false, 'min_hits' => 2, 'days' => 30, 'suggest' => false, 'fill_above' => 90,
                                  'siteaccess' => 'site' ), UrlImport::helperForm( array(), $sas, 'site' ) );
        $form = UrlImport::helperForm( array( 'Address' => ' https://old.example/sitemap.xml ', 'Limit' => '999999', 'AnyHost' => '1', 'MinHits' => '0',
                                              'Days' => 'x', 'Suggest' => '1', 'FillAbove' => '101', 'SiteAccess' => 'ger' ), $sas, 'site' );
        $this->assertSame( array( 'https://old.example/sitemap.xml', UrlImport::HELPER_MAX_ROWS, true, 1, 30, true, 100, 'ger' ),
                           array_values( $form ) );
        $this->assertSame( 'site', UrlImport::helperForm( array( 'SiteAccess' => 'admin' ), $sas, 'site' )['siteaccess'], 'only a siteaccess of the list' );
        $this->assertSame( 'site', UrlImport::helperForm( array( 'SiteAccess' => array( 'ger' ) ), $sas, 'site' )['siteaccess'] );
        $this->assertSame( 'content/urlimport/(source)/404', UrlImport::sourceURL( '404' ) );
        $this->assertSame( array( 'sitemap', '404', 'suggest' ), UrlImport::SOURCES );

        $this->assertSame( array( 'https://www.example.com', 'http://www.example.com/site', 'http://x.example', '' ), array(
            UrlImport::baseFromSiteURL( 'www.example.com/', true ), UrlImport::baseFromSiteURL( 'www.example.com/site', false ),
            UrlImport::baseFromSiteURL( 'http://x.example/', true ), UrlImport::baseFromSiteURL( ' ', true ) ) );

        $this->assertSame( array( 'dryrun.json', 'result.json', 'test.json', 'dryrun.json' ), array(
            expUrlMapImportSession::resultFile( 'dryrun' ), expUrlMapImportSession::resultFile( 'result' ), expUrlMapImportSession::resultFile( 'test' ),
            expUrlMapImportSession::resultFile( '../x' ) ) );
        $session = expUrlMapAdminViewsTestSession::create( 14, self::CSV, 'old.csv', 'csv', array( 'siteaccess' => 'site', 'on_conflict' => 'skip' ) );
        $session->saveResult( 'test', array( 'base' => 'https://www.example.com', 'counts' => array( 'landed' => 3 ) ) );
        $again = expUrlMapAdminViewsTestSession::load( $session->token(), 14 );
        $this->assertSame( 3, $again->result( 'test' )['counts']['landed'] );
        $this->assertNull( $again->result( 'result' ) );
        $this->assertSame( array( '/about-us.html', 'https://www.unknown.org/x', '/old/*', '/gone', '/about-us.html' ),
                           array_map( function ( $row ) { return $row->oldUrl(); }, $again->rows() ) );
    }

    public function testTheModuleDeclaresTheViews()
    {
        // module.php reads the state groups from the database, so its text is read
        $root = dirname( __DIR__, 5 );
        $module = (string)file_get_contents( $root . '/kernel/content/module.php' );
        $this->assertStringContainsString( "\$FunctionList['urltranslator_import'] = array();", $module );
        $expected = array( 'urlimport' => array( 'urltranslator_import', "'plan' => 'Plan', 'state' => 'State', 'offset' => 'Offset', 'report' => 'Report', 'source' => 'Source', 'example' => 'Example', 'use' => 'Use'" ),
                           'urlimporthistory' => array( 'urltranslator_import', "'offset' => 'Offset', 'done' => 'Done'" ),
                           'urlexport' => array( 'urltranslator', null ),
                           'urlredirects' => array( 'urltranslator', "'offset' => 'Offset', 'removed' => 'Removed'" ) );
        foreach ( $expected as $view => $want )
        {
            $this->assertSame( 1, preg_match( '/\$ViewList\[\'' . $view . '\'\] = array\((.*?)\);\n/s', $module, $m ), $view );
            $this->assertStringContainsString( "'functions' => array( '{$want[0]}' )", $m[1], $view );
            $this->assertStringContainsString( "'script' => '$view.php'", $m[1], $view );
            if ( $want[1] !== null )
                $this->assertStringContainsString( "'unordered_params' => array( {$want[1]} )", $m[1], $view );
            $this->assertFileExists( "$root/kernel/content/$view.php" );
            $this->assertFileExists( "$root/kernel/private/classes/views/content/$view.php" );
        }
        $this->assertSame( 1, preg_match( "/'script' => 'urlimporthistory.php',.*?'params' => array\( 'BatchID' \)/s", $module ) );
    }
}
