<?php
/**
 * The keys of the log viewer's logs and the logs configured by key (doc/en/us/features/6.0/log-viewer.md, "Permalinks" and
 * "Web server logs"), on a temporary root.
 *
 *  LV-80  keyFor(): var/log/<name>.log is <name>, var/<site>/log/<name>.log is <site>-<name>, another directory is the
 *         directory with - then -<name>, a configured log its key; other characters become -
 *  LV-81  resolveKey(): the current file, rotations by number, a plain rotation before a .gz of the same number, a bare
 *         .gz as rotation -1; KEY_PATTERN refuses traversal, slashes, empty and long keys
 *  LV-82  configured logs: LogList[] with LogPath[] (absolute and relative to the root), label, group (default webserver,
 *         LogGroup[] override, an unknown group falls back), a missing file and an empty path are left out, the
 *         web server's keys are opt-in (empty by default)
 *  LV-83  configured ids "@<key>/<name>": only the log's file and its rotations; traversal, other names, unknown keys and
 *         a rotation that is a link leading out of the directory are refused
 *  LV-84  listFiles() lists the configured log and its rotations (group webserver and archives); the info carries key,
 *         label, configured
 *  LV-85  the old link ?file=<id> maps to the permalink of the same file: expLogPermalink::build( key, state with its rotation )
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';

class expLogKeyTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $root;

    /** @var string a directory outside the root, like /var/www/vhosts/<domain>/logs */
    protected $ws;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lvkey' );
        $this->ws = expLogViewerTestFixtures::root( 'lvws' ) . '/logs';
        mkdir( $this->ws );
        $log = $this->root . '/var/log/';
        foreach ( array( 'error.log', 'debug.log', 'cronjob-publishing.log', 'error.log.1', 'error.log.2', 'error.log.2.gz',
                         'error.log.3.gz', 'old.log.gz' ) as $name )
            file_put_contents( $log . $name, "[ Oct 17 2026 01:00:00 ] [192.0.2.1] x\n" );
        file_put_contents( $this->root . '/var/site/log/storage.log', "x\n" );
        mkdir( $this->root . '/var/other/logs', 0777, true );
        file_put_contents( $this->root . '/var/other/logs/app.log', "x\n" );
        file_put_contents( $this->ws . '/access_ssl_log', '192.0.2.9 - - [17/Oct/2026:01:00:00 +0200] "GET / HTTP/1.1" 200 1 "-" "-"' . "\n" );
        file_put_contents( $this->ws . '/access_ssl_log.1', "old\n" );
        file_put_contents( $this->ws . '/access_ssl_log.processed', "not a rotation\n" );
        file_put_contents( $this->ws . '/error_log', "[Fri Oct 17 01:00:00.1 2026] [core:error] [pid 1] x\n" );
        file_put_contents( $this->ws . '/error_log.1.gz', gzencode( "old\n" ) );
        file_put_contents( $this->ws . '/secret', "secret\n" );
        file_put_contents( $this->root . '/secret', "secret\n" );
        // a rotation that is a link to a file elsewhere
        symlink( $this->root . '/secret', $this->ws . '/error_log.2' );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
        expLogViewerTestFixtures::remove( dirname( $this->ws ) );
    }

    protected function locator( array $values = array() )
    {
        return new expLogFileLocator( expLogViewerTestFixtures::config( $this->root, $values ), 'var/log' );
    }

    protected function configured( array $more = array() )
    {
        return $this->locator( array( 'LogViewerSettings' => $more + array(
            'Directories' => array( 'var/log', 'var/*/log', 'var/other/logs' ),
            'LogPath' => array( 'webserver-access' => $this->ws . '/access_ssl_log', 'webserver-error' => $this->ws . '/error_log' ) ) ) );
    }

    /** LV-80 */
    public function testKeyFor()
    {
        $this->assertSame( 'error', expLogFileLocator::keyFor( 'var/log', 'error.log' ) );
        $this->assertSame( 'cronjob-publishing', expLogFileLocator::keyFor( 'var/log', 'cronjob-publishing.log' ) );
        $this->assertSame( 'site-storage', expLogFileLocator::keyFor( 'var/site/log', 'storage.log' ) );
        $this->assertSame( 'var-other-logs-app', expLogFileLocator::keyFor( 'var/other/logs', 'app.log' ) );
        $this->assertSame( 'webserver-access', expLogFileLocator::keyFor( '@webserver-access', 'access_ssl_log' ) );
        $this->assertSame( 'my-log-x', expLogFileLocator::keyFor( 'var/log', 'my log+x.log' ) );
        $this->assertSame( 'shouty', expLogFileLocator::keyFor( 'var/log', '...shouty.log' ), 'never a leading dot or dash' );
        $this->assertLessThanOrEqual( 64, strlen( expLogFileLocator::keyFor( 'var/log', str_repeat( 'a', 200 ) . '.log' ) ) );
        foreach ( array( 'error', 'site-storage', 'var-other-logs-app', 'cronjob-publishing' ) as $key )
            $this->assertMatchesRegularExpression( expLogFileLocator::KEY_PATTERN, $key );
        // the key of a rotation is its current file's
        $d = $this->locator()->describe( 'var/log', 'error.log.2.gz' );
        $this->assertSame( 'error', $d['key'] );
        $this->assertSame( 2, $d['rotation'] );
    }

    /** LV-81 */
    public function testResolveKey()
    {
        $l = $this->configured();
        $this->assertSame( 'var/log/error.log', $l->resolveKey( 'error' )['id'] );
        $this->assertSame( 'var/log/debug.log', $l->resolveKey( 'debug' )['id'] );
        $this->assertSame( 'var/log/error.log.1', $l->resolveKey( 'error', 1 )['id'] );
        $this->assertSame( 'var/log/error.log.2', $l->resolveKey( 'error', 2 )['id'], 'the plain rotation before the .gz of the same number' );
        $this->assertSame( 'var/log/error.log.3.gz', $l->resolveKey( 'error', 3 )['id'] );
        $this->assertSame( 'var/log/old.log.gz', $l->resolveKey( 'old', -1 )['id'], 'a bare .gz is rotation -1' );
        $this->assertNull( $l->resolveKey( 'old' ), 'no current file' );
        $this->assertNull( $l->resolveKey( 'error', 9 ) );
        $this->assertSame( 'var/site/log/storage.log', $l->resolveKey( 'site-storage' )['id'] );
        $this->assertSame( 'var/other/logs/app.log', $l->resolveKey( 'var-other-logs-app' )['id'] );
        $this->assertSame( '@webserver-access/access_ssl_log', $l->resolveKey( 'webserver-access' )['id'] );
        foreach ( array( '', '../error', 'error/../debug', '/etc/passwd', 'var/log/error.log', '.error', '-error', 'err or',
                         "error\0", str_repeat( 'a', 65 ), '..', '@webserver-access' ) as $bad )
            $this->assertNull( $l->resolveKey( $bad ), var_export( $bad, true ) );
        $this->assertNull( $l->resolveKey( array( 'error' ) ) );
        $this->assertNull( $l->resolveKey( 'nothing-here' ) );
    }

    /** LV-82 */
    public function testConfiguredLogs()
    {
        $logs = $this->configured()->configuredLogs();
        $this->assertSame( array( 'webserver-access', 'webserver-error' ), array_keys( $logs ) );
        $this->assertSame( $this->ws . '/access_ssl_log', $logs['webserver-access']['path'] );
        $this->assertSame( 'webserver', $logs['webserver-access']['group'] );
        $this->assertSame( 'Web server access log', $logs['webserver-access']['label'], 'the default label' );

        // opt-in: the default settings name the web server's keys without a path
        $this->assertSame( array(), $this->locator()->configuredLogs() );
        $this->assertNull( $this->locator()->resolveKey( 'webserver-access' ) );

        $l = $this->locator( array( 'LogViewerSettings' => array(
            'LogList' => array( 'app', 'gone', 'empty', 'bad key!', 'unlisted-ok', 'grouped', 'oddgroup' ),
            'LogPath' => array( 'app' => 'var/other/logs/app.log', 'gone' => $this->ws . '/nope', 'empty' => '',
                                'bad key!' => $this->ws . '/error_log', 'unlisted' => $this->ws . '/error_log',
                                'grouped' => $this->ws . '/error_log', 'oddgroup' => $this->ws . '/error_log' ),
            'LogLabel' => array( 'app' => 'The app' ),
            'LogGroup' => array( 'grouped' => 'kernel', 'oddgroup' => 'archives' ) ) ) );
        $logs = $l->configuredLogs();
        $this->assertSame( array( 'app', 'grouped', 'oddgroup' ), array_keys( $logs ), 'missing file, empty path, bad key and unlisted keys are left out' );
        $this->assertSame( $this->root . '/var/other/logs/app.log', $logs['app']['path'], 'a relative path is relative to the root' );
        $this->assertSame( 'The app', $logs['app']['label'] );
        $this->assertSame( 'kernel', $logs['grouped']['group'] );
        $this->assertSame( 'webserver', $logs['oddgroup']['group'], 'an unknown group falls back to webserver' );
        $info = $l->resolveKey( 'grouped' );
        $this->assertSame( 'kernel', $info['group'] );
        $this->assertTrue( $info['configured'] );
        $this->assertSame( 'The app', $l->resolveKey( 'app' )['label'] );
    }

    /** LV-83 */
    public function testConfiguredIds()
    {
        $l = $this->configured();
        $a = $l->resolve( '@webserver-access/access_ssl_log' );
        $this->assertNotNull( $a );
        $this->assertSame( 'webserver-access', $a['key'] );
        $this->assertTrue( $a['configured'] );
        $this->assertSame( 0, $a['rotation'] );
        $r = $l->resolve( '@webserver-access/access_ssl_log.1' );
        $this->assertSame( 1, $r['rotation'] );
        $this->assertTrue( $r['archive'] );
        $this->assertSame( 'webserver-access', $r['key'] );
        $g = $l->resolve( '@webserver-error/error_log.1.gz' );
        $this->assertTrue( $g['gz'] );
        $this->assertSame( 1, $g['rotation'] );
        foreach ( array( '@webserver-access/../../etc/passwd', '@webserver-access/secret', '@webserver-access/access_ssl_log.processed',
                         '@webserver-access/error_log', '@nope/access_ssl_log', '@webserver-access', '@webserver-access/',
                         '@webserver-access/access_ssl_log/x', '@../logs/access_ssl_log', '@webserver-error/error_log.2' ) as $bad )
            $this->assertNull( $l->resolve( $bad ), $bad );
        $this->assertNull( $this->locator()->resolve( '@webserver-access/access_ssl_log' ), 'not configured: not resolved' );
        $this->assertTrue( expLogFileLocator::isRotationName( 'error_log', 'error_log.12.gz' ) );
        $this->assertTrue( expLogFileLocator::isRotationName( 'error_log', 'error_log.gz' ) );
        $this->assertFalse( expLogFileLocator::isRotationName( 'error_log', 'error_log.processed' ) );
        $this->assertFalse( expLogFileLocator::isRotationName( 'a.b', 'aXb.1' ), 'the base is quoted' );
    }

    /** LV-84 */
    public function testListedWithTheirRotations()
    {
        $groups = $this->configured()->listFiles();
        $ws = array_column( $groups['webserver'], null, 'id' );
        $this->assertSame( array( '@webserver-access/access_ssl_log', '@webserver-error/error_log' ), array_keys( $ws ) );
        $this->assertSame( 'Web server error log', $ws['@webserver-error/error_log']['label'] );
        $this->assertTrue( $ws['@webserver-error/error_log']['configured'] );
        $archives = array_column( $groups['archives'], 'key', 'id' );
        $this->assertSame( 'webserver-access', $archives['@webserver-access/access_ssl_log.1'] );
        $this->assertSame( 'webserver-error', $archives['@webserver-error/error_log.1.gz'] );
        $this->assertArrayNotHasKey( '@webserver-error/error_log.2', $archives, 'a link out of the directory is not listed' );
        $this->assertArrayNotHasKey( '@webserver-access/access_ssl_log.processed', $archives );
        foreach ( $groups as $list )
            foreach ( $list as $info )
            {
                $this->assertArrayHasKey( 'key', $info );
                $this->assertArrayHasKey( 'label', $info );
                $this->assertArrayHasKey( 'configured', $info );
            }
        $error = array_column( $groups['kernel'], null, 'id' )['var/log/error.log'];
        $this->assertSame( 'error', $error['key'] );
        $this->assertFalse( $error['configured'] );
    }

    /** LV-85 */
    public function testLegacyLinkMapsToThePermalink()
    {
        $l = $this->configured();
        $map = function ( $id, array $query = array() ) use ( $l ) {
            $file = $l->resolve( $id );
            if ( $file === null )
                return null;
            $state = expLogPermalink::state( $query, false );
            $state['rotation'] = $file['rotation'];
            return expLogPermalink::build( $file['key'], $state );
        };
        $this->assertSame( 'setup/logs/view/error', $map( 'var/log/error.log' ) );
        $this->assertSame( 'setup/logs/view/error/(archive)/1', $map( 'var/log/error.log.1' ) );
        $this->assertSame( 'setup/logs/view/site-storage', $map( 'var/site/log/storage.log' ) );
        $this->assertSame( 'setup/logs/view/error/(level)/error/(search)/time~20out', $map( 'var/log/error.log', array( 'q' => 'time out', 'levels' => array( 'error' ) ) ) );
        $this->assertSame( 'setup/logs/view/webserver-error/(archive)/1', $map( '@webserver-error/error_log.1.gz' ) );
        $this->assertNull( $map( 'var/log/../../secret' ) );
        $this->assertNull( $map( 'var/log/nope.log' ) );
        // and back: the permalink's key and rotation open the same file
        foreach ( array( 'var/log/error.log', 'var/log/error.log.1', 'var/log/error.log.3.gz', '@webserver-access/access_ssl_log.1' ) as $id )
        {
            $file = $l->resolve( $id );
            $this->assertSame( $id, $l->resolveKey( $file['key'], $file['rotation'] )['id'], $id );
        }
    }
}
