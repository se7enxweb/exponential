<?php
/**
 * The log viewer's facade without a database (doc/eng-us/features/6.0/log-viewer.md, "The feed"): what the page and the
 * feed receive, on a temporary root with synthetic lines.
 *
 *  LV-60  present(): masked fields and message, links as parts, the request key equal for one request and not the
 *         address itself, the file of a search result
 *  LV-61  tail(): the end of a file, then only what it gained; a compressed rotation is not followed (live = false);
 *         a file the viewer may not open gives null
 *  LV-62  related(): the lines of the same request (address and client, within RequestWindow) across the kernel logs,
 *         not those of another request; the audit window
 *  LV-63  reference(): an error reference in a rotation is found, with its request's lines
 *  LV-64  searchFiles(): the file and its rotations; every kernel log; actions() follow the settings and the policy
 *  LV-65  search(): a masked viewer's request and client address filters compare what the viewer is shown, an
 *         unmasked viewer's compare what is written
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';

class expLogViewerTest extends PHPUnit\Framework\TestCase
{
    const START = 1792195200;

    /** @var string */
    protected $root;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lvview' );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
    }

    protected function viewer( $unmasked = false, array $values = array() )
    {
        $values = array_replace_recursive( array( 'TailSettings' => array( 'ChunkCacheTTL' => 0, 'InitialBytes' => 1024 ),
                                                  'RedactionSettings' => array( 'SecretSettings' => array(), 'SecretGroups' => array() ) ), $values );
        $config = expLogViewerTestFixtures::config( $this->root, $values );
        return new expLogViewer( $config, new expLogFileLocator( $config, 'var/log' ), $unmasked, 'test-salt' );
    }

    protected function line( $time, $ip, $url, $message )
    {
        return '[ ' . date( 'M d Y H:i:s', $time ) . " ] [$ip] [admin] [$url] $message\n";
    }

    /** LV-60 */
    public function testPresent()
    {
        $v = $this->viewer();
        $entries = expLogParser::parse( $this->line( self::START, '192.0.2.10', 'https://example.test/x?mail=jane@example.com', 'eZDB:' )
                                        . "user jane@example.com node_id=42 token=abc123\n", 0, 'error' );
        $p = $v->present( $entries[0] + array( 'file' => 'var/log/error.log' ) );
        $this->assertSame( '192.0.x.x', $p['ip'] );
        $this->assertStringNotContainsString( 'jane@', $p['u'] );
        $json = json_encode( $p );
        $this->assertStringNotContainsString( 'jane@example.com', $json );
        $this->assertStringNotContainsString( 'abc123', $json );
        $this->assertStringNotContainsString( '192.0.2.10', $json );
        $this->assertContains( array( '42', 'node', '42' ), $p['p'] );
        $this->assertSame( 'error', $p['lv'] );
        $this->assertSame( 'eZDB', $p['src'] );
        $this->assertSame( 'var/log/error.log', $p['f'] );
        $this->assertSame( 12, strlen( $p['rk'] ) );
        $this->assertSame( self::START, $p['t'] );

        $other = expLogParser::parse( $this->line( self::START + 1, '192.0.2.10', 'https://example.test/x?mail=jane@example.com', 'again' ) );
        $this->assertSame( $p['rk'], $v->present( $other[0] )['rk'], 'one request, one key' );
        $third = expLogParser::parse( $this->line( self::START, '192.0.2.11', 'https://example.test/x?mail=jane@example.com', 'again' ) );
        $this->assertNotSame( $p['rk'], $v->present( $third[0] )['rk'] );

        $plain = $this->viewer( true )->present( $entries[0] );
        $this->assertSame( '192.0.2.10', $plain['ip'] );
        $this->assertTrue( $this->viewer( true )->unmasked() );
    }

    /** LV-61 */
    public function testTail()
    {
        $log = $this->root . '/var/log/error.log';
        file_put_contents( $log, expLogViewerTestFixtures::debugLines( 50, self::START ) );
        $v = $this->viewer();
        $first = $v->tail( 'var/log/error.log', -1 );
        $this->assertTrue( $first['live'] );
        $this->assertNotEmpty( $first['entries'] );
        $this->assertSame( filesize( $log ), $first['next'] );
        $this->assertSame( 'error', $first['file']['level'] );
        $this->assertArrayHasKey( 'ms', $first['cost'] );

        $again = $v->tail( 'var/log/error.log', $first['next'], $first['ino'] );
        $this->assertSame( array(), $again['entries'] );
        $this->assertSame( 0, $again['cost']['open'] );
        file_put_contents( $log, $this->line( self::START + 100, '192.0.2.1', 'https://example.test/new', 'eZTemplate:' ) . "new one\n", FILE_APPEND );
        $more = $v->tail( 'var/log/error.log', $first['next'], $first['ino'] );
        $this->assertCount( 1, $more['entries'] );
        $this->assertSame( 'https://example.test/new', $more['entries'][0]['u'] );

        file_put_contents( $log . '.2.gz', gzencode( expLogViewerTestFixtures::debugLines( 20, self::START ) ) );
        $gz = $v->tail( 'var/log/error.log.2.gz', -1 );
        $this->assertFalse( $gz['live'] );
        $this->assertNotEmpty( $gz['entries'] );
        $this->assertSame( array(), $v->tail( 'var/log/error.log.2.gz', 100 )['entries'] );

        $this->assertNull( $v->tail( '../etc/passwd', -1 ) );
        $this->assertNull( $v->tail( 'var/log/none.log', -1 ) );
    }

    /** LV-62 */
    public function testRelated()
    {
        $url = 'https://example.test/admin/content/edit/12';
        file_put_contents( $this->root . '/var/log/error.log',
            $this->line( self::START - 100, '192.0.2.5', $url, 'old: same address, too early' )
            . $this->line( self::START, '192.0.2.5', $url, 'Error: the failure' )
            . $this->line( self::START, '192.0.2.6', $url, 'other client' ) );
        file_put_contents( $this->root . '/var/log/debug.log',
            $this->line( self::START - 1, '192.0.2.5', $url, 'Debug: before' )
            . $this->line( self::START + 1, '192.0.2.5', 'https://example.test/other', 'Debug: another request' )
            . $this->line( self::START + 2, '192.0.2.5', $url, 'Debug: after' ) );
        $v = $this->viewer();
        $content = file_get_contents( $this->root . '/var/log/error.log' );
        $offset = strpos( $content, '[ ' . date( 'M d Y H:i:s', self::START ) );
        $r = $v->related( 'var/log/error.log', $offset );
        $this->assertSame( '', $r['error'] );
        $texts = array_map( function ( $e ) { return $e['p'][0][0]; }, $r['entries'] );
        $this->assertSame( array( 'Debug: before', 'Error: the failure', 'Debug: after' ), $texts );
        $this->assertSame( array( 'var/log/debug.log', 'var/log/error.log', 'var/log/debug.log' ), array_column( $r['entries'], 'f' ) );
        $this->assertSame( date( 'Y-m-d\TH:i:s', self::START - 2 ), $r['window']['from'] );
        $this->assertSame( date( 'Y-m-d\TH:i:s', self::START + 2 ), $r['window']['to'] );
        $this->assertSame( date( 'Y-m-d\TH:i:s', self::START - 62 ), $r['audit']['from'] );
        $this->assertSame( 'var/log/error.log', $r['entry']['f'] );

        $this->assertSame( 'no_entry', $v->related( 'var/log/error.log', 999999 )['error'] );
        $this->assertNull( $v->related( 'var/log/../../x.log', 0 ) );
    }

    /** LV-63 */
    public function testReference()
    {
        $url = 'https://example.test/site/broken';
        file_put_contents( $this->root . '/var/log/error.log', $this->line( self::START + 500, '192.0.2.9', 'https://example.test/', 'fine' ) );
        file_put_contents( $this->root . '/var/log/error.log.1',
            $this->line( self::START, '192.0.2.8', $url, 'Error: uncaught, reference ERR-0A1B2C3D4E' )
            . $this->line( self::START, '192.0.2.8', $url, 'Error: second line of it' ) );
        $v = $this->viewer();
        $r = $v->reference( 'err-0a1b2c3d4e' );
        $this->assertCount( 1, $r['entries'] );
        $this->assertSame( 'var/log/error.log.1', $r['entries'][0]['f'] );
        $this->assertSame( array( 'ERR-0A1B2C3D4E' ), $r['entries'][0]['refs'] );
        $this->assertNotNull( $r['related'] );
        $this->assertCount( 2, $r['related']['entries'] );
        $this->assertSame( 'invalid', $v->reference( 'nonsense' )['error'] );
        $this->assertSame( array(), $v->reference( 'ERR-FFFFFFFFFF' )['entries'] );
    }

    /** LV-64 */
    public function testSearchFilesAndActions()
    {
        foreach ( array( 'error.log', 'error.log.1', 'error.log.2.gz', 'warning.log', 'expmcp.log' ) as $name )
            file_put_contents( $this->root . '/var/log/' . $name, "x\n" );
        $v = $this->viewer();
        $this->assertSame( array( 'var/log/error.log' ), array_column( $v->searchFiles( 'var/log/error.log' ), 'id' ) );
        $this->assertSame( array( 'var/log/error.log', 'var/log/error.log.1', 'var/log/error.log.2.gz' ),
                           array_column( $v->searchFiles( 'var/log/error.log', 'file', true ), 'id' ) );
        $this->assertSame( array( 'var/log/error.log' ), array_column( $this->viewer( false, array( 'SearchSettings' => array( 'Rotations' => 'disabled' ) ) )
                                                                       ->searchFiles( 'var/log/error.log', 'file', true ), 'id' ) );
        $kernel = array_column( $v->searchFiles( '', 'kernel', true ), 'id' );
        $this->assertSame( array( 'var/log/error.log', 'var/log/warning.log', 'var/log/error.log.1', 'var/log/error.log.2.gz' ), $kernel );
        $this->assertNotContains( 'var/log/expmcp.log', $kernel );
        $this->assertContains( 'var/log/expmcp.log', array_column( $v->searchFiles( '', 'all', false ), 'id' ) );
        $this->assertSame( array(), $v->searchFiles( '../x.log' ) );

        $this->assertSame( array( 'download' => true, 'rotate' => false, 'truncate' => false, 'delete' => false ), $v->actions( true ) );
        $on = $this->viewer( false, array( 'ActionSettings' => array( 'Rotate' => 'enabled', 'Truncate' => 'enabled', 'DeleteArchives' => 'enabled' ) ) );
        $this->assertSame( array( 'download' => true, 'rotate' => true, 'truncate' => true, 'delete' => true ), $on->actions( true ) );
        $this->assertSame( array( 'download' => true, 'rotate' => false, 'truncate' => false, 'delete' => false ), $on->actions( false ), 'managelogs is needed' );
        $this->assertTrue( $v->enabled() );
    }

    /** LV-65 */
    public function testSearchComparesWhatTheViewerIsShown()
    {
        file_put_contents( $this->root . '/var/log/error.log',
                           $this->line( self::START, '192.0.2.10', 'https://example.test/reset?token=abc123def456&mail=jane@example.com', 'Error: reset failed' )
                           . $this->line( self::START + 1, '192.0.200.20', 'https://example.test/news', 'Error: other' ) );
        $urls = function ( expLogViewer $v, array $in ) {
            $r = $v->search( $v->searchFiles( 'var/log/error.log' ), expLogSearch::criteria( $in ) );
            $this->assertSame( '', $r['error'] );
            return array_column( $r['entries'], 'u' );
        };
        $masked = $this->viewer();
        $shown = 'https://example.test/reset?token=***&mail=j***@***.com';
        $this->assertSame( array( $shown, 'https://example.test/news' ), $urls( $masked, array( 'ip' => '192.0.' ) ) );
        $this->assertSame( array(), $urls( $masked, array( 'ip' => '192.0.2' ) ), 'shown as 192.0.x.x: the hidden part is not an oracle' );
        $this->assertSame( array(), $urls( $masked, array( 'ip' => '192.0.2.10' ) ) );
        $this->assertSame( array(), $urls( $masked, array( 'uri' => 'token=abc' ) ) );
        $this->assertSame( array(), $urls( $masked, array( 'uri' => 'jane@' ) ) );
        $this->assertSame( array( $shown ), $urls( $masked, array( 'uri' => 'reset?token=' ) ) );

        // unmasked: the address and the e-mail address as written; a token in an address stays hidden, and unfound
        $plain = $this->viewer( true );
        $written = 'https://example.test/reset?token=***&mail=jane@example.com';
        $this->assertSame( array( $written ), $urls( $plain, array( 'ip' => '192.0.2.10' ) ) );
        $this->assertSame( array( $written ), $urls( $plain, array( 'ip' => '192.0.2.' ) ) );
        $this->assertSame( array( $written ), $urls( $plain, array( 'uri' => 'jane@example.com' ) ) );
        $this->assertSame( array(), $urls( $plain, array( 'uri' => 'token=abc' ) ) );
    }
}
