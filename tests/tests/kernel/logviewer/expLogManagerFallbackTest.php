<?php
/**
 * Log management on the log viewer's own classes (expLogManagerFallback, expLogManagerFactory) and the guards of the
 * view setup/logsmanage (doc/en/us/features/6.0/log-viewer.md, "Managing logs"), on temporary directories, no database.
 *
 *  LV-140  logs(): size, estimated lines, archives sorted with sizes and gz, archive bytes, growth since the last
 *          rotation, writable, owner, the web server's logs listed but not managed
 *  LV-141  limits: [RotationSettings] per key, else site.ini [FileSettings], else the defaults, each with its source
 *  LV-142  select(): keys and current file ids are taken; unknown names, archives, web server logs and paths outside
 *          the allowed directories are refused with the reason
 *  LV-143  plan() rotate changes nothing (the dry run), shifts the archives oldest first, drops what is beyond Keep,
 *          compresses .1 when Compress is on; rotate all takes only the logs over their size limit
 *  LV-144  apply() rotate: the files moved as planned; a stale plan (a target that appeared, a file gone) is skipped
 *          per step, never forced; a log taken by another lock is "locked"
 *  LV-145  truncate archives first (copy to .1, then empty in place, the inode kept), compress gzips the archives and
 *          the result unpacks to the same bytes, prune deletes what is older than N days and beyond Keep, never the
 *          current file
 *  LV-146  a step naming a file outside the log's directory or another log's file is refused (not_allowed)
 *  LV-147  rotate(), prune(), compress(), truncate() on unknown and web server keys; empty when nothing to do
 *  LV-148  the factory gives the fallback while the kernel service is not there; it implements the interface
 *  LV-149  the guards of setup/logsmanage: GET shows; POST needs a known action, the form token and its switch;
 *          without ConfirmButton it previews, with it it applies; the viewer switched off answers "disabled"
 *  LV-150  the templates in both designs: identical, POST forms with the form token, no inline CSS
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';
foreach ( array( 'expLogManagerInterface' => 'explogmanagerinterface', 'expLogManagerFallback' => 'explogmanagerfallback',
                 'expLogManagerFactory' => 'explogmanagerfactory' ) as $class => $file )
    if ( !class_exists( $class ) && !interface_exists( $class ) )
        require_once dirname( __DIR__, 4 ) . '/kernel/classes/logviewer/' . $file . '.php';
if ( !class_exists( '\Exponential\View\Kernel\Setup\Logsmanage' ) )
    require_once dirname( __DIR__, 4 ) . '/kernel/private/classes/views/setup/logsmanage.php';

class expLogManagerFallbackTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $root;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lvman' );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
    }

    /** A manager on the temporary root, no audit, its own lock directory */
    protected function manager( array $values = array(), array $site = array(), $now = null )
    {
        $config = expLogViewerTestFixtures::config( $this->root, $values );
        $m = new expLogManagerFallback( $config, new expLogFileLocator( $config, 'var/log' ) );
        $m->siteFileSettings = $site;
        $m->audit = false;
        $m->lockDir = $this->root . '/lock';
        $m->now = $now;
        return $m;
    }

    protected function write( $name, $content, $mtime = null )
    {
        $path = $this->root . '/var/log/' . $name;
        file_put_contents( $path, $content );
        if ( $mtime !== null )
            touch( $path, $mtime );
        return $path;
    }

    /** LV-140 */
    public function testLogs()
    {
        $now = time();
        $this->write( 'error.log', str_repeat( "line of forty bytes ................ x\n", 100 ) );
        $this->write( 'error.log.1', 'one', $now - 7200 );
        $this->write( 'error.log.2.gz', gzencode( 'two' ), $now - 86400 );
        $this->write( 'debug.log', '' );
        $logs = $this->manager( array(), array(), $now )->logs();
        $this->assertSame( array( 'debug', 'error' ), array_keys( $logs ) );
        $e = $logs['error'];
        $this->assertSame( 'var/log/error.log', $e['id'] );
        $this->assertSame( 3900, $e['size'] );
        $this->assertSame( 100, $e['lines'] );
        $this->assertSame( array( 'error.log.1', 'error.log.2.gz' ), array_column( $e['archives'], 'name' ) );
        $this->assertSame( array( false, true ), array_column( $e['archives'], 'gz' ) );
        $this->assertSame( 3 + filesize( $this->root . '/var/log/error.log.2.gz' ), $e['archive_bytes'] );
        $this->assertSame( (int)round( 3900 / 7200 * 3600 ), $e['growth'], 'bytes an hour since .1 was written' );
        $this->assertNull( $logs['debug']['growth'] );
        $this->assertTrue( $e['writable'] );
        $this->assertNotSame( '', $e['owner'] );
        $this->assertArrayHasKey( 'owner_group', $e );
        $this->assertSame( 'kernel', $e['group'] );
        $this->assertTrue( $e['managed'] );

        $ws = $this->root . '/ws';
        mkdir( $ws );
        file_put_contents( $ws . '/access_log', "x\n" );
        $logs = $this->manager( array( 'LogViewerSettings' => array( 'LogPath' => array( 'webserver-access' => $ws . '/access_log' ) ) ) )->logs();
        $this->assertFalse( $logs['webserver-access']['managed'] );
        $this->assertSame( 'webserver', $logs['webserver-access']['group'] );
    }

    /** LV-141 */
    public function testLimits()
    {
        $this->write( 'error.log', 'x' );
        $this->write( 'debug.log', 'x' );
        $m = $this->manager( array( 'RotationSettings' => array( 'MaxSize' => array( 'error' => '1048576' ), 'Keep' => array( 'error' => '5' ),
                                                                 'Compress' => array( 'error' => 'enabled' ) ) ),
                             array( 'LogFileMaxSize' => '4194304', 'LogFileRotateCount' => '2', 'LogFileMaxAge' => '14' ) );
        $e = $m->effectiveSettings( 'error' );
        $this->assertSame( 1048576, $e['max_size'] );
        $this->assertSame( 5, $e['keep'] );
        $this->assertTrue( $e['compress'] );
        $this->assertSame( 14, $e['max_age_days'] );
        $this->assertSame( array( 'file' => 'logviewer.ini', 'section' => 'RotationSettings', 'var' => 'MaxSize[error]' ), $e['source']['max_size'] );
        $this->assertSame( array( 'file' => 'site.ini', 'section' => 'FileSettings', 'var' => 'LogFileMaxAge' ), $e['source']['max_age_days'] );
        $d = $m->effectiveSettings( 'debug' );
        $this->assertSame( 4194304, $d['max_size'] );
        $this->assertSame( 2, $d['keep'] );
        $this->assertFalse( $d['compress'] );
        $this->assertSame( 'default', $d['source']['compress']['var'] );
        $this->assertNull( $m->effectiveSettings( 'nope' ) );
        $plain = $this->manager()->effectiveSettings( 'debug' );
        $this->assertSame( array( 2097152, 3, false, 0 ), array( $plain['max_size'], $plain['keep'], $plain['compress'], $plain['max_age_days'] ) );
    }

    /** LV-142 */
    public function testSelect()
    {
        $this->write( 'error.log', 'x' );
        $this->write( 'error.log.1', 'y' );
        $ws = $this->root . '/ws';
        mkdir( $ws );
        file_put_contents( $ws . '/access_log', "x\n" );
        $m = $this->manager( array( 'LogViewerSettings' => array( 'LogPath' => array( 'webserver-access' => $ws . '/access_log' ) ) ) );
        $s = $m->select( array( 'error', 'var/log/error.log', 'var/log/error.log.1', 'nosuch', 'webserver-access', '../../etc/passwd', '/etc/passwd' ) );
        $this->assertSame( array( 'error' ), $s['keys'] );
        $this->assertSame( 'archive', $s['refused']['var/log/error.log.1'] );
        $this->assertSame( 'unknown', $s['refused']['nosuch'] );
        $this->assertSame( 'webserver', $s['refused']['webserver-access'] );
        $this->assertSame( 'outside', $s['refused']['../../etc/passwd'] );
        $this->assertSame( 'outside', $s['refused']['/etc/passwd'] );
    }

    /** LV-143 */
    public function testPlanRotateIsADryRun()
    {
        $this->write( 'error.log', 'current' );
        $this->write( 'error.log.1', 'one' );
        $this->write( 'error.log.2', 'two' );
        $this->write( 'error.log.3.gz', gzencode( 'three' ) );
        $before = scandir( $this->root . '/var/log' );
        $m = $this->manager( array( 'RotationSettings' => array( 'Keep' => array( 'error' => '3' ), 'Compress' => array( 'error' => 'enabled' ) ) ) );
        $plan = $m->plan( array( 'error' ), 'rotate', array( 'force' => true ) );
        $this->assertSame( $before, scandir( $this->root . '/var/log' ), 'a plan changes nothing' );
        $this->assertSame( array(
            array( 'delete', 'var/log/error.log.3.gz', '' ),
            array( 'rename', 'var/log/error.log.2', 'var/log/error.log.3' ),
            array( 'rename', 'var/log/error.log.1', 'var/log/error.log.2' ),
            array( 'rename', 'var/log/error.log', 'var/log/error.log.1' ),
            array( 'gzip', 'var/log/error.log.1', 'var/log/error.log.1.gz' ),
        ), array_map( function ( $s ) { return array( $s['op'], $s['from'], $s['to'] ); }, $plan['steps'] ) );
        $this->assertSame( array(), $plan['unknown'] );

        // rotate all: only the logs over their limit
        $this->write( 'big.log', str_repeat( 'x', 3000 ) );
        $all = $this->manager( array( 'RotationSettings' => array( 'MaxSize' => array( 'big' => '2048', 'error' => '1048576' ) ) ) )->plan( 'all', 'rotate' );
        $this->assertSame( array( 'big' ), array_values( array_unique( array_column( $all['steps'], 'key' ) ) ) );
        $this->assertSame( array( 'nope' ), $m->plan( array( 'nope' ), 'rotate' )['unknown'] );
    }

    /** LV-144 */
    public function testApplyRotateAndStalePlans()
    {
        $this->write( 'error.log', 'current' );
        $this->write( 'error.log.1', 'one' );
        $m = $this->manager();
        $result = $m->apply( $m->plan( array( 'error' ), 'rotate', array( 'force' => true ) ) );
        $this->assertTrue( $result['ok'] );
        $this->assertSame( 'current', file_get_contents( $this->root . '/var/log/error.log.1' ) );
        $this->assertSame( 'one', file_get_contents( $this->root . '/var/log/error.log.2' ) );
        $this->assertFileDoesNotExist( $this->root . '/var/log/error.log' );
        $this->assertSame( array( true, true ), array_column( $result['steps'], 'done' ) );

        // a stale plan: the current file is back and a target appeared meanwhile
        $this->write( 'error.log', 'new' );
        $m = $this->manager();
        $plan = $m->plan( array( 'error' ), 'rotate', array( 'force' => true ) );
        $this->write( 'error.log.3', 'appeared' );
        $result = $m->apply( $plan );
        $this->assertFalse( $result['ok'] );
        $this->assertSame( 'moved', $result['steps'][0]['error'] );
        $this->assertSame( 'appeared', file_get_contents( $this->root . '/var/log/error.log.3' ), 'never overwritten' );

        // a file gone since the plan
        $m = $this->manager();
        $plan = $m->plan( array( 'error' ), 'truncate' );
        unlink( $this->root . '/var/log/error.log' );
        $result = $m->apply( $plan );
        $this->assertSame( 'missing', end( $result['steps'] )['error'] );

        // locked by another process
        $this->write( 'error.log', 'again' );
        $m = $this->manager();
        if ( !is_dir( $this->root . '/lock' ) )
            mkdir( $this->root . '/lock' );
        $h = fopen( $this->root . '/lock/' . sha1( 'error' ) . '.lock', 'c' );
        flock( $h, LOCK_EX );
        $result = $m->rotate( 'error' );
        flock( $h, LOCK_UN );
        fclose( $h );
        $this->assertSame( 'locked', $result['error'] );
        $this->assertSame( 'again', file_get_contents( $this->root . '/var/log/error.log' ) );
    }

    /** LV-145 */
    public function testTruncateCompressPrune()
    {
        $now = time();
        $path = $this->write( 'error.log', "abc\n" );
        $ino = fileinode( $path );
        $r = $this->manager()->truncate( 'error' );
        $this->assertTrue( $r['ok'] );
        clearstatcache();
        $this->assertSame( 0, filesize( $path ) );
        $this->assertSame( $ino, fileinode( $path ), 'emptied in place' );
        $this->assertSame( "abc\n", file_get_contents( $path . '.1' ) );

        $r = $this->manager()->compress( 'error' );
        $this->assertTrue( $r['ok'] );
        $this->assertFileDoesNotExist( $path . '.1' );
        $this->assertSame( "abc\n", gzdecode( file_get_contents( $path . '.1.gz' ) ) );
        $this->assertSame( 'empty', $this->manager()->compress( 'error' )['error'], 'nothing left to compress' );

        file_put_contents( $path, 'live' );
        $this->write( 'error.log.2', 'old', $now - 40 * 86400 );
        $this->write( 'error.log.3', 'recent', $now - 3600 );
        $this->write( 'error.log.9', 'beyond keep', $now );
        $m = $this->manager( array(), array(), $now );
        $plan = $m->plan( array( 'error' ), 'prune', array( 'days' => 30 ) );
        $this->assertSame( array( 'var/log/error.log.2', 'var/log/error.log.9' ), array_column( $plan['steps'], 'from' ) );
        $this->assertTrue( $m->apply( $plan )['ok'] );
        $this->assertFileExists( $this->root . '/var/log/error.log.3' );
        $this->assertFileDoesNotExist( $this->root . '/var/log/error.log.2' );
        $this->assertSame( 'live', file_get_contents( $path ), 'the current file is never pruned' );
    }

    /** LV-146 */
    public function testStepsOutsideTheLogAreRefused()
    {
        $this->write( 'error.log', 'e' );
        $this->write( 'debug.log', 'd' );
        file_put_contents( $this->root . '/outside.log', 'keep me' );
        $m = $this->manager();
        $plan = array( 'steps' => array(
            array( 'key' => 'error', 'op' => 'delete', 'from' => 'var/log/debug.log', 'to' => '', 'bytes' => 1 ),
            array( 'key' => 'error', 'op' => 'rename', 'from' => 'var/log/error.log', 'to' => 'var/log/../outside.log', 'bytes' => 1 ),
            array( 'key' => 'error', 'op' => 'delete', 'from' => 'var/log/error.log', 'to' => '', 'bytes' => 1 ),
            array( 'key' => 'webserver-access', 'op' => 'delete', 'from' => 'var/log/error.log', 'to' => '', 'bytes' => 1 ),
        ) );
        $r = $m->apply( $plan );
        $this->assertSame( array( 'not_allowed', 'not_allowed', 'not_allowed', 'not_allowed' ), array_column( $r['steps'], 'error' ) );
        $this->assertSame( 'd', file_get_contents( $this->root . '/var/log/debug.log' ) );
        $this->assertSame( 'e', file_get_contents( $this->root . '/var/log/error.log' ) );
        $this->assertSame( 'keep me', file_get_contents( $this->root . '/outside.log' ) );
    }

    /** LV-147 */
    public function testOneLogAtATime()
    {
        $this->write( 'error.log', '' );
        $ws = $this->root . '/ws';
        mkdir( $ws );
        file_put_contents( $ws . '/access_log', "x\n" );
        $m = $this->manager( array( 'LogViewerSettings' => array( 'LogPath' => array( 'webserver-access' => $ws . '/access_log' ) ) ) );
        $this->assertSame( 'unknown', $m->rotate( 'nosuch' )['error'] );
        $this->assertSame( 'not_allowed', $m->rotate( 'webserver-access' )['error'] );
        $this->assertSame( 'not_allowed', $m->truncate( 'webserver-access' )['error'] );
        $this->assertSame( 'empty', $m->rotate( 'error' )['error'], 'an empty file is not rotated' );
        $this->assertSame( 'empty', $m->prune( 'error', 1 )['error'] );
        $this->assertSame( "x\n", file_get_contents( $ws . '/access_log' ) );
    }

    /** LV-148 */
    public function testFactory()
    {
        $config = expLogViewerTestFixtures::config( $this->root );
        $m = expLogManagerFactory::get( $config, new expLogFileLocator( $config, 'var/log' ) );
        $this->assertInstanceOf( 'expLogManagerInterface', $m );
        if ( !class_exists( 'expLogManager' ) )
            $this->assertInstanceOf( 'expLogManagerFallback', $m );
        $this->assertTrue( in_array( 'expLogManagerInterface', class_implements( 'expLogManagerFallback' ), true ) );
    }

    /** LV-149 */
    public function testViewGuards()
    {
        $on = array( 'Rotate' => true, 'Truncate' => true, 'Compress' => true, 'DeleteArchives' => true );
        $off = array( 'Rotate' => false, 'Truncate' => false, 'Compress' => false, 'DeleteArchives' => false );
        $d = function ( $method, $op, $token, $confirm, $switches, $enabled = true ) {
            return \Exponential\View\Kernel\Setup\Logsmanage::decide( $method, $op, $token, $confirm, $switches, $enabled );
        };
        $this->assertSame( 'show', $d( 'GET', '', false, false, $on ) );
        $this->assertSame( 'show', $d( 'GET', 'rotate', false, true, $on ), 'a GET never changes anything' );
        $this->assertSame( 'bad_op', $d( 'POST', 'format_disk', true, true, $on ) );
        $this->assertSame( 'denied', $d( 'POST', 'rotate', false, true, $on ), 'no form token' );
        $this->assertSame( 'off', $d( 'POST', 'rotate', true, true, $off ) );
        $this->assertSame( 'off', $d( 'POST', 'compress', true, false, array( 'Compress' => false ) + $on ) );
        $this->assertSame( 'preview', $d( 'POST', 'prune_all', true, false, $on ) );
        $this->assertSame( 'apply', $d( 'POST', 'truncate', true, true, $on ) );
        $this->assertSame( 'disabled', $d( 'POST', 'rotate', true, true, $on, false ) );

        $config = expLogViewerTestFixtures::config( $this->root );
        $sw = \Exponential\View\Kernel\Setup\Logsmanage::switches( $config );
        $this->assertSame( array( false, false, false, false, true ), array( $sw['Rotate'], $sw['Truncate'], $sw['Compress'], $sw['DeleteArchives'], $sw['Download'] ),
                           'every changing action is off by default' );

        $this->write( 'error.log', 'x' );
        $plan = \Exponential\View\Kernel\Setup\Logsmanage::planFor( $this->manager(), 'rotate', '../../etc/passwd', null );
        $this->assertSame( array(), $plan['steps'] );
        $plan = \Exponential\View\Kernel\Setup\Logsmanage::planFor( $this->manager(), 'rotate', 'error', null );
        $this->assertCount( 1, $plan['steps'] );
    }

    /** LV-150 */
    public function testTemplates()
    {
        $root = dirname( __DIR__, 4 );
        foreach ( array( 'logs_manage.tpl', 'logs_manage_confirm.tpl' ) as $name )
        {
            $a = file_get_contents( "$root/design/admin/templates/setup/$name" );
            $this->assertSame( $a, file_get_contents( "$root/design/admin4/templates/setup/$name" ), "$name is the same in both designs" );
            $this->assertDoesNotMatchRegularExpression( '/<style|\sstyle="/i', $a );
            $this->assertSame( substr_count( $a, '<form method="post"' ), substr_count( $a, 'name="{$log_token_field|wash}" value="{$log_token|wash}"' ),
                               "every form of $name carries the form token" );
            $this->assertStringNotContainsString( 'method="get"', $a );
        }
    }
}
