<?php
/**
 * Visitors the debug bar is shown to (DebugByIP, DebugIPList[]) are answered from the role-aware page cache with the
 * bar of the render that stored the page; the view parameter (exp_render)/full asks for a full render.
 *
 *  DV-01 — The switch is cut out of an address (encoded too, before the query string), and added after the path
 *  DV-02 — apply() takes it out of REQUEST_URI and PATH_INFO before anything reads them; asked() remembers it
 *  DV-03 — Who is a debug viewer: DebugOutput and DebugByIP with the list, DebugByUser; unknown without an address
 *  DV-04 — A debug viewer gets their own entry with the bar put back: "Cached", "Render now", this answer's time,
 *          every tab saying which render its figures are from, the data block saying the same
 *  DV-05 — A visitor who is not a debug viewer never gets a debug viewer's entry, nor the reverse
 *  DV-06 — The switch: a full render for a debug viewer; for anybody else it means nothing and never reaches a key
 *  DV-07 — Asked without an address (the server in front of the workers), a user with debug entries is left to a
 *          worker; one without them is answered as before
 *  DV-08 — A stale debug entry is not served; garbage collection removes the bar with its body; the bar is 0600
 *  DV-09 — Every tab of a real bar renders on a cached page, each with its "From the render at" line
 *  DV-10 — The bar of a render says "Rendered", why, and with the switch the link back to the cached view
 *
 * No database: the contract is pure PHP; DV-09 and DV-10 render the bar with a fresh eZDebug.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group httpcache
 */

require_once __DIR__ . '/../../../../../kernel/private/classes/httpcache/ezphttpcachecontract.php';
require_once __DIR__ . '/../../../../../kernel/private/classes/httpcache/exprenderswitch.php';

class ezpHttpCacheDebugViewerTest extends PHPUnit\Framework\TestCase
{
    const VIEWER_IP = '203.0.113.7';
    const OTHER_IP = '198.51.100.9';

    private $dir;
    private $sessions;
    private $server;

    protected function setUp(): void
    {
        $base = dirname( __DIR__, 6 ) . '/var/tmp';
        $this->dir = $base . '/httpcache-debug-test-' . getmypid() . '-' . mt_rand();
        $this->sessions = $this->dir . '-sessions';
        mkdir( $this->dir, 0770, true );
        mkdir( $this->sessions, 0770, true );
        $this->server = $_SERVER;
        expRenderSwitch::reset();
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        expRenderSwitch::reset();
        foreach ( array( $this->dir, $this->sessions ) as $d )
        {
            if ( !is_dir( $d ) )
                continue;
            foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $d, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ) as $f )
                $f->isDir() ? rmdir( $f->getPathname() ) : unlink( $f->getPathname() );
            rmdir( $d );
        }
    }

    private function contract( array $debug = array() )
    {
        return new ezpHttpCacheContract( array(
            'enabled' => true, 'secret' => str_repeat( 'k', 64 ), 'dir' => $this->dir,
            'hosts' => array( 'example.org' => 'site' ), 'sessionCookie' => array( 'site' => 'eZSESSID' ),
            'sessionSavePath' => $this->sessions, 'formTokenSecret' => 'secret', 'maxAge' => 3600, 'swr' => 60,
            'apcu' => false,
            'debug' => $debug + array( 'output' => true, 'byIP' => true, 'ipList' => array( self::VIEWER_IP . ' ; laptop' ),
                                       'byUser' => false, 'userIDs' => array(), 'clientIpHeader' => '' ),
        ) );
    }

    private function request( $uri = '/page', $ip = self::VIEWER_IP, array $cookies = array() )
    {
        $r = array( 'scheme' => 'https', 'host' => 'example.org', 'uri' => $uri, 'method' => 'GET',
                    'cookies' => $cookies, 'acceptEncoding' => '', 'ifNoneMatch' => null, 'start' => microtime( true ) );
        if ( $ip !== null )
            $r['remoteAddr'] = $ip;
        return $r;
    }

    private function signIn( ezpHttpCacheContract $c, $userID )
    {
        $sid = 'sess' . str_repeat( (string)$userID, 30 );
        file_put_contents( $this->sessions . '/sess_' . $sid, 'eZUserLoggedInID|i:' . $userID . ';' );
        $c->storeRecord( $userID, $c->roleContext( array( 1 ), array(), 'site' ) );
        return array( 'eZSESSID' => $sid );
    }

    /** A bar as expDebugBarReport writes it: the marked "Rendered" chip and page time, three tabs, the data block */
    private function bar()
    {
        $served = array( 'by' => 'render', 'full_asked' => false, 'reason' => 'no entry', 'render_ms' => 41.0,
                         'form_token' => 'storedsessiontoken0123456789',
                         'urls' => array( 'cached' => '/page', 'full' => '/page/(exp_render)/full' ),
                         'labels' => array( 'render' => 'Rendered', 'render_title' => 'Rendered by the kernel: x', 'cache' => 'Cached',
                                            'cache_title' => 'Served from the page cache in %ms ms. The page was stored at %stored by a render of %render ms, %age s ago.',
                                            'render_now' => 'Render now', 'back' => 'Back to the cached view',
                                            'from_render' => 'From the render at %stored (%render ms)',
                                            'this_request' => 'This request: served from the page cache in %ms ms.' ) );
        $html = '<div id="debug" class="exp-debug"><h2><a href="#debug-end">Exp Debug (By IP Address)</a>'
              . '<!--exp-served--><span class="exp-debug-served" data-served="render">Rendered</span><!--/exp-served-->'
              . '<span class="exp-debug-summary"><!--exp-served-time--><button type="button" class="exp-debug-chip" data-tab="timing" data-key="time" aria-label="Page time: 0.041 s (normal)">0.041 s</button><!--/exp-served-time-->'
              . '<button type="button" class="exp-debug-chip" data-tab="sql" data-key="sql">2 SQL / 1 ms</button></span></h2>';
        foreach ( array( 'messages' => 'Messages', 'timing' => 'Timing', 'sql' => 'SQL' ) as $id => $title )
            $html .= '<section class="exp-debug-panel" role="tabpanel" id="exp-debug-panel-' . $id . '" tabindex="0"><h3 class="exp-debug-panel-title">' . $title . '</h3><p>render data ' . $id . '</p>'
                   . ( $id === 'messages' ? '<form><input type="hidden" name="ezxform_token" value="storedsessiontoken0123456789"></form>' : '' ) . '</section>';
        $html .= '</div><a id="debug-end"></a><script type="application/json" id="exp-debug-data">'
               . json_encode( array( 'version' => 2, 'summary' => array( 'time' => 0.041 ), 'server_summary' => array( 'time' => array( 'total_ms' => 41.0 ) ), 'served' => $served ) )
               . '</script>';
        return array( $html, $served );
    }

    /** Stores /page for $context: the page without the bar, the bar beside it (as the listener does) */
    private function storeWithBar( ezpHttpCacheContract $c, $context, $userID, $uri = '/page', $page = '<html><body>page TOKEN<!--BAR--></body></html>' )
    {
        list( $bar, $served ) = $this->bar();
        $offset = strpos( $page, '<!--BAR-->' );
        $page = str_replace( '<!--BAR-->', '', $page );
        $token = $c->formToken( 'sess' . str_repeat( (string)$userID, 30 ) );
        $placeholders = array();
        $before = 0;
        if ( ( $at = strpos( $page, 'TOKEN' ) ) !== false )
        {
            $page = substr_replace( $page, '', $at, 5 );
            $placeholders[] = array( $at, 40, 'form_token' );
            if ( $at < $offset )
            {
                $offset -= 5;
                $before = 1;
            }
        }
        $key = $c->entryKey( 'https', 'example.org', 'site', $uri, $c->debugContext( $context, $userID ) );
        $meta = $c->storeEntry( $key, 200, array( 'Content-Type' => 'text/html' ), $page, array( 'ez-all' ), $placeholders );
        $this->assertTrue( $c->storeDebugReport( $key, $meta, $offset, $bar, $served, $userID, $before ) );
        return array( $key, $meta, $token );
    }

    /** DV-01 */
    public function testSwitchIsCutOutAndAdded()
    {
        $this->assertSame( array( '/fitness', true ), expRenderSwitch::strip( '/fitness/(exp_render)/full' ) );
        $this->assertSame( array( '/', true ), expRenderSwitch::strip( '/(exp_render)/full' ) );
        $this->assertSame( array( '/twig', true ), expRenderSwitch::strip( '/twig/(exp_render)/full' ) );
        $this->assertSame( array( '/a/(offset)/10?x=1', true ), expRenderSwitch::strip( '/a/(exp_render)/full/(offset)/10?x=1' ) );
        $this->assertSame( array( '/a', true ), expRenderSwitch::strip( '/a/%28exp_render%29/full' ) );
        $this->assertSame( array( '/a/(exp_render)/other', false ), expRenderSwitch::strip( '/a/(exp_render)/other' ) );
        $this->assertSame( array( '/a?(exp_render)/full', false ), expRenderSwitch::strip( '/a?(exp_render)/full' ), 'the query string is not a path' );
        $this->assertSame( array( '/a/(exp_render)/fuller', false ), expRenderSwitch::strip( '/a/(exp_render)/fuller' ) );
        $this->assertSame( '/twig/(exp_render)/full', expRenderSwitch::add( '/twig/' ) );
        $this->assertSame( '/(exp_render)/full', expRenderSwitch::add( '/' ) );
        $this->assertSame( '/a/(exp_render)/full?x=1', expRenderSwitch::add( '/a?x=1' ) );
        $this->assertSame( '/a/(exp_render)/full', expRenderSwitch::add( '/a/(exp_render)/full' ), 'never twice' );
    }

    /** DV-02 */
    public function testApplyTakesItOutOfTheRequestBeforeAnythingReadsIt()
    {
        $_SERVER['REQUEST_TIME_FLOAT'] = microtime( true );
        $_SERVER['REQUEST_URI'] = '/twig/fitness/(exp_render)/full';
        $_SERVER['PATH_INFO'] = '/fitness/(exp_render)/full';
        $this->assertTrue( expRenderSwitch::apply() );
        $this->assertSame( '/twig/fitness', $_SERVER['REQUEST_URI'] );
        $this->assertSame( '/fitness', $_SERVER['PATH_INFO'] );
        $this->assertTrue( expRenderSwitch::asked() );
        $this->assertTrue( expRenderSwitch::apply(), 'a second call in the request changes nothing' );
        $this->assertSame( '/twig/fitness', $_SERVER['REQUEST_URI'] );

        // the next request (another REQUEST_TIME_FLOAT, as in a persistent worker)
        $_SERVER['REQUEST_TIME_FLOAT'] += 1;
        $_SERVER['REQUEST_URI'] = '/twig/fitness';
        unset( $_SERVER['PATH_INFO'] );
        $this->assertFalse( expRenderSwitch::apply() );
        $this->assertFalse( expRenderSwitch::asked() );
        $this->assertSame( '/twig/fitness', $_SERVER['REQUEST_URI'] );
    }

    /** DV-03 */
    public function testWhoIsADebugViewer()
    {
        $c = $this->contract();
        $this->assertTrue( $c->debugViewer( $this->request( '/page', self::VIEWER_IP ), 0 ) );
        $this->assertFalse( $c->debugViewer( $this->request( '/page', self::OTHER_IP ), 0 ) );
        $this->assertNull( $c->debugViewer( $this->request( '/page', null ), 0 ), 'no address: unknown' );
        $this->assertTrue( $this->contract( array( 'ipList' => array( '203.0.113.0/24' ) ) )->debugViewer( $this->request(), 0 ), 'a network' );
        $this->assertFalse( $this->contract( array( 'output' => false ) )->debugViewer( $this->request(), 0 ), 'DebugOutput off' );
        $this->assertFalse( $this->contract( array( 'byIP' => false ) )->debugViewer( $this->request(), 0 ), 'neither by IP nor by user: everybody, nothing changes' );
        $byUser = $this->contract( array( 'byIP' => false, 'byUser' => true, 'userIDs' => array( 14 ) ) );
        $this->assertTrue( $byUser->debugViewer( $this->request( '/page', null ), 14 ) );
        $this->assertFalse( $byUser->debugViewer( $this->request( '/page', null ), 10 ) );
        $this->assertSame( self::VIEWER_IP, $c->clientAddress( array( 'remoteAddr' => self::VIEWER_IP ) ) );
    }

    /** DV-04 */
    public function testDebugViewerGetsTheirEntryWithTheBarOfTheRender()
    {
        $c = $this->contract();
        $cookies = $this->signIn( $c, 14 );
        list( , , $token ) = $this->storeWithBar( $c, $c->roleContext( array( 1 ), array(), 'site' ), 14 );
        $response = $c->serve( $this->request( '/page', self::VIEWER_IP, $cookies ) );
        $this->assertIsArray( $response, $c->lastReason );
        list( $status, $headers, $body ) = $response;
        $this->assertSame( 200, $status );
        $this->assertStringStartsWith( 'HIT (debug viewer', $headers['X-Exp-Cache'] );
        $this->assertSame( 'private, no-store', $headers['Cache-Control'] );
        $this->assertArrayNotHasKey( 'ETag', $headers );
        // the page, the visitor's form token where it was, the bar where it stood
        $this->assertStringStartsWith( '<html><body>page ' . $token . '<div id="debug"', $body );
        $this->assertStringEndsWith( '</script></body></html>', $body );
        $this->assertSame( strlen( $body ), (int)$headers['Content-Length'] );
        $this->assertStringContainsString( 'data-served="cache"', $body );
        $this->assertStringNotContainsString( 'data-served="render"', $body );
        $this->assertStringContainsString( '<a class="exp-debug-served-action" href="/page/(exp_render)/full">Render now</a>', $body );
        $this->assertDoesNotMatchRegularExpression( '#>0\.041 s<#', $body, 'the page time chip gives this answer\'s time' );
        $this->assertMatchesRegularExpression( '#data-key="time" aria-label="Page time: \d+\.\d{3} s \(normal\)">\d+\.\d{3} s<#', $body );
        $this->assertSame( 3, substr_count( $body, 'class="exp-debug-hint exp-debug-from-render">From the render at ' ), 'every tab' );
        $this->assertSame( 1, substr_count( $body, 'data-this-request="1"' ), 'the Timing tab: this request' );
        $this->assertStringContainsString( 'render data sql', $body, 'the render\'s figures stay' );
        $this->assertStringNotContainsString( 'storedsessiontoken0123456789', $body, 'the form token of the session that stored the bar is gone' );
        $this->assertStringContainsString( 'name="ezxform_token" value="' . $token . '"', $body, 'the bar\'s forms carry this visitor\'s token' );
        preg_match( '#<script type="application/json" id="exp-debug-data">(.*?)</script>#s', $body, $m );
        $data = json_decode( $m[1], true );
        $this->assertSame( 'cache', $data['served']['by'] );
        $this->assertSame( 0.041, $data['served']['render_time'] );
        $this->assertLessThan( 0.041, $data['summary']['time'] );
        $this->assertSame( $data['summary']['time'], $data['served']['time'] );
    }

    /** DV-05 */
    public function testDebugAndOtherEntriesNeverMix()
    {
        $c = $this->contract();
        $ctx = $c->anonymousContext( 'site' );
        $c->storeEntry( $c->entryKey( 'https', 'example.org', 'site', '/page', $ctx ), 200, array(), 'plain page', array( 'ez-all' ), array() );
        $this->assertNull( $c->serve( $this->request( '/page', self::VIEWER_IP ) ), 'the viewer has no entry of their own yet' );
        $this->assertSame( 'no entry', $c->lastReason );
        $this->storeWithBar( $c, $ctx, 0, '/page', '<html>viewer page<!--BAR--></html>' );
        $this->assertStringContainsString( 'viewer page', $c->serve( $this->request( '/page', self::VIEWER_IP ) )[2] );
        $other = $c->serve( $this->request( '/page', self::OTHER_IP ) );
        $this->assertSame( 'plain page', $other[2] );
        $this->assertStringNotContainsString( 'debug', $other[2] );
    }

    /** DV-06 */
    public function testSwitchFullRenderForAViewerNothingForOthers()
    {
        $c = $this->contract();
        $ctx = $c->anonymousContext( 'site' );
        $c->storeEntry( $c->entryKey( 'https', 'example.org', 'site', '/page', $ctx ), 200, array(), 'plain page', array( 'ez-all' ), array() );
        $this->storeWithBar( $c, $ctx, 0 );
        $this->assertNull( $c->serve( $this->request( '/page/(exp_render)/full', self::VIEWER_IP ) ) );
        $this->assertSame( 'full render asked', $c->lastReason );
        // anybody else: the same page as without it, from the same entry
        $this->assertSame( 'plain page', $c->serve( $this->request( '/page/(exp_render)/full', self::OTHER_IP ) )[2] );
        $this->assertSame( $c->entryKey( 'https', 'example.org', 'site', '/page', $ctx ),
                           $c->entryKey( 'https', 'example.org', 'site', expRenderSwitch::strip( '/page/(exp_render)/full' )[0], $ctx ) );
    }

    /** DV-07 */
    public function testWithoutAnAddressADebugUserIsLeftToAWorker()
    {
        $c = $this->contract();
        $ctx = $c->roleContext( array( 1 ), array(), 'site' );
        $c->storeEntry( $c->entryKey( 'https', 'example.org', 'site', '/page', $ctx ), 200, array(), 'role page', array( 'ez-all' ), array() );
        $cookies = $this->signIn( $c, 14 );
        $this->assertSame( 'role page', $c->serve( $this->request( '/page', null, $cookies ) )[2], 'no debug entries yet: as before' );
        $this->storeWithBar( $c, $ctx, 14 );
        $this->assertFileExists( $c->debugMarkerPath( 14 ) );
        $this->assertNull( $c->serve( $this->request( '/page', null, $cookies ) ) );
        $this->assertSame( 'debug viewer, address not known here', $c->lastReason );
        $this->assertIsArray( $c->serve( $this->request( '/page', self::VIEWER_IP, $cookies ) ) );
    }

    /** DV-08 */
    public function testStaleNotServedGarbageCollectedAndPrivate()
    {
        $c = $this->contract();
        list( $key, $meta ) = $this->storeWithBar( $c, $c->anonymousContext( 'site' ), 0 );
        $dbg = glob( $this->dir . '/e/*/' . $key . '.*.dbg' );
        $this->assertCount( 1, $dbg );
        $this->assertSame( '600', substr( sprintf( '%o', fileperms( $dbg[0] ) ), -3 ), 'the bar holds SQL and settings' );
        // expired: within the stale window a page would be served stale, a debug viewer gets a render
        $metaFile = $this->dir . '/e/' . substr( $key, 0, 2 ) . '/' . $key . '.meta';
        $m = json_decode( file_get_contents( $metaFile ), true );
        $m['created'] -= 3610;
        file_put_contents( $metaFile, json_encode( $m ) );
        $this->assertNull( $c->serve( $this->request() ) );
        $this->assertSame( 'expired', $c->lastReason );
        // past the window: gc removes meta, body and bar
        $m['created'] -= 100;
        file_put_contents( $metaFile, json_encode( $m ) );
        foreach ( glob( $this->dir . '/e/*/*' ) as $f )
            touch( $f, time() - 300 );
        $c->gc( 120 );
        $this->assertCount( 0, glob( $this->dir . '/e/*/' . $key . '.*' ) );
    }

    /** A real bar of a fresh eZDebug (as the kernel prints it) */
    private function realBar()
    {
        if ( !class_exists( 'eZDebug' ) || !method_exists( 'eZDebug', 'printReport' ) || !class_exists( 'expDebugBarReport' ) )
            $this->markTestSkipped( 'the debug bar needs the kernel classes' );
        $keys = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugAlwaysLog', 'eZDebugLogFileEnabled' );
        $saved = array();
        foreach ( $keys as $key )
            $saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $logs = $this->dir . '/logs/';
        mkdir( $logs, 0777, true );
        try
        {
            unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'] );
            $debug = new eZDebug();
            foreach ( $debug->LogFiles as $level => $file )
                $debug->LogFiles[$level] = array( $logs, $file[1] );
            $GLOBALS['eZDebugGlobalInstance'] = $debug;
            $GLOBALS['eZDebugEnabled'] = true;
            $GLOBALS['eZDebugLogOnly'] = false;
            ob_start();
            $report = eZDebug::printReport( false, true, true );
            ob_end_clean();
        }
        finally
        {
            foreach ( $saved as $key => $value )
            {
                if ( $value === null )
                    unset( $GLOBALS[$key] );
                else
                    $GLOBALS[$key] = $value[0];
            }
        }
        return (string)$report;
    }

    /** DV-09 */
    public function testEveryTabOfARealBarOnACachedPage()
    {
        $bar = $this->realBar();
        preg_match( '#<script type="application/json" id="exp-debug-data">(.*?)</script>#s', $bar, $m );
        $data = json_decode( $m[1], true );
        $this->assertSame( 'render', $data['served']['by'] );
        preg_match_all( '#id="exp-debug-panel-([a-z0-9_-]+)"#', $bar, $panels );
        $tabs = array_unique( $panels[1] );
        foreach ( array( 'messages', 'settings', 'cache', 'timing', 'sql', 'templates', 'includes', 'memory', 'velocity' ) as $tab )
            $this->assertContains( $tab, $tabs, "the bar has the $tab tab" );

        $c = $this->contract();
        $served = $data['served'];
        $served['render_ms'] = 41.0;
        $cached = $c->debugBar( $bar, $served, microtime( true ) - 5, microtime( true ), '/page' );
        foreach ( $tabs as $tab )
        {
            $this->assertMatchesRegularExpression( '#id="exp-debug-panel-' . preg_quote( $tab, '#' ) . '"[^>]*>\s*<h3 class="exp-debug-panel-title">[^<]*</h3>(<p[^>]*data-this-request="1"[^>]*>[^<]*</p>)?<p class="exp-debug-hint exp-debug-from-render">From the render at #',
                $cached, "the $tab tab renders on the cached page with whose figures they are" );
            // and the rest of the tab is the render's, unchanged
            preg_match( '#id="exp-debug-panel-' . $tab . '".*?</section>#s', $bar, $before );
            preg_match( '#id="exp-debug-panel-' . $tab . '".*?</section>#s', $cached, $after );
            $this->assertSame( preg_replace( '#<p class="exp-debug-hint exp-debug-from-render"[^>]*>[^<]*</p>#', '', $after[0] ), $before[0] );
        }
        $this->assertSame( substr_count( $bar, 'class="exp-debug-tab"' ), substr_count( $cached, 'class="exp-debug-tab"' ), 'every tab button' );
        $this->assertSame( substr_count( $bar, 'class="exp-debug-chip' ), substr_count( $cached, 'class="exp-debug-chip' ), 'every chip' );
        $this->assertStringContainsString( 'data-served="cache"', $cached );
        $this->assertStringContainsString( 'href="/page/(exp_render)/full"', $cached );
    }

    /** DV-10 */
    public function testBarOfARenderSaysRenderedAndWhy()
    {
        $_SERVER['REQUEST_TIME_FLOAT'] = microtime( true );
        $_SERVER['REQUEST_URI'] = '/page/(exp_render)/full';
        expRenderSwitch::apply();
        if ( !class_exists( 'expDebugBarReport' ) )
            $this->markTestSkipped( 'the debug bar needs the kernel classes' );
        $served = expDebugBarReport::servedInfo();
        $this->assertSame( 'render', $served['by'] );
        $this->assertTrue( $served['full_asked'] );
        $this->assertSame( '/page', $served['urls']['cached'] );
        $this->assertSame( '/page/(exp_render)/full', $served['urls']['full'] );
        $html = expDebugBarReport::servedHtml( $served );
        $this->assertStringStartsWith( expDebugBarReport::SERVED_START, $html );
        $this->assertStringEndsWith( expDebugBarReport::SERVED_END, $html );
        $this->assertStringContainsString( 'data-served="render"', $html );
        $this->assertStringContainsString( '<a class="exp-debug-served-action" href="/page">', $html, 'back to the cached view' );
        $this->assertStringNotContainsString( 'exp_render', expDebugBarReport::servedHtml( array( 'full_asked' => false ) + $served ), 'no switch in a rendered bar without it' );
    }
}
