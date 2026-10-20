<?php
/**
 * The health of the logs (doc/en/us/features/6.0/log-viewer.md, "Health"): expLogHealth over a test double of the log
 * manager, without a database.
 *
 *  LV-120  the budget: [HealthSettings] DiskBudget, the default 256 MB, 0 for none, nonsense falls back
 *  LV-121  the summary: totals of files and archives (archive_bytes, or summed from the archives), share and state
 *          (ok, warn from 80 %, bad from 100 %), no budget gives no share
 *  LV-122  the fastest growing (growth > 0 only, the fastest first, at most five), over their limit (size > max_size,
 *          the furthest over first; no limit is never over), not writable, mixed owners from owner:owner_group
 *  LV-123  the newest error: the last entry of level error in the last 64 KB of a file, by its label or by the file's
 *          level; none in an empty file, none without an error; a file larger than the window is read from a line
 *  LV-124  the effective settings: from the manager's effectiveSettings() with their sources, else the limits; a
 *          manager that throws leaves the limits
 *  LV-125  the template: both designs identical, every value washed or a number, no inline CSS, and the view and the
 *          ini section are there
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLogHealthTestManager
{
    public $logs = array();
    public $effective = array();
    public $throw = false;

    public function logs()
    {
        return $this->logs;
    }

    public function plan( $keys, $op, array $opts = array() )
    {
        return array( 'steps' => array(), 'unknown' => array() );
    }

    public function apply( array $plan )
    {
        return array( 'ok' => true, 'error' => '', 'steps' => array() );
    }

    public function effectiveSettings( $key )
    {
        if ( $this->throw )
            throw new RuntimeException( 'nope' );
        return isset( $this->effective[$key] ) ? $this->effective[$key] : array();
    }
}

class expLogHealthTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/exploghealth-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0700, true );
    }

    protected function tearDown(): void
    {
        foreach ( (array)glob( $this->dir . '/*' ) as $f )
            unlink( $f );
        rmdir( $this->dir );
    }

    protected function log( $key, array $over = array() )
    {
        return $over + array( 'key' => $key, 'id' => 'var/log/' . $key . '.log', 'path' => '/x/' . $key . '.log', 'name' => $key . '.log',
                              'group' => 'kernel', 'size' => 1000, 'mtime' => 1, 'archives' => array(), 'archive_bytes' => 0,
                              'growth' => null, 'limits' => array( 'max_size' => 2000, 'keep' => 3, 'compress' => false, 'max_age_days' => 0,
                                                                     'source' => array() ),
                              'writable' => true, 'owner' => 'alpha', 'owner_group' => 'psacln' );
    }

    /** LV-120 */
    public function testBudget()
    {
        $root = sys_get_temp_dir();
        $this->assertSame( expLogHealth::DEFAULT_BUDGET, expLogHealth::budget( expLogViewerConfig::fromArray( array(), $root ) ) );
        $this->assertSame( 268435456, expLogHealth::DEFAULT_BUDGET );
        $this->assertSame( 1000, expLogHealth::budget( expLogViewerConfig::fromArray( array( 'HealthSettings' => array( 'DiskBudget' => '1000' ) ), $root ) ) );
        $this->assertSame( 0, expLogHealth::budget( expLogViewerConfig::fromArray( array( 'HealthSettings' => array( 'DiskBudget' => '0' ) ), $root ) ) );
        $this->assertSame( expLogHealth::DEFAULT_BUDGET, expLogHealth::budget( expLogViewerConfig::fromArray( array( 'HealthSettings' => array( 'DiskBudget' => 'lots' ) ), $root ) ) );
        $this->assertSame( 0, expLogHealth::budget( expLogViewerConfig::fromArray( array( 'HealthSettings' => array( 'DiskBudget' => '-5' ) ), $root ) ) );
    }

    /** LV-121 */
    public function testSummaryTotalsAndState()
    {
        $logs = array( 'error' => $this->log( 'error', array( 'size' => 300, 'archive_bytes' => 200 ) ),
                       'debug' => $this->log( 'debug', array( 'size' => 100, 'archive_bytes' => null,
                                                              'archives' => array( array( 'size' => 50 ), array( 'size' => 25 ) ) ) ) );
        unset( $logs['debug']['archive_bytes'] );
        $s = expLogHealth::summary( $logs, 10000 );
        $this->assertSame( 400, $s['logs_bytes'] );
        $this->assertSame( 275, $s['archive_bytes'] );
        $this->assertSame( 675, $s['total'] );
        $this->assertSame( 'ok', $s['state'] );
        $this->assertSame( 7, $s['percent'] );
        $this->assertSame( 75, $s['rows']['debug']['archive_bytes'] );
        $this->assertSame( 'warn', expLogHealth::summary( $logs, 800 )['state'] );
        $this->assertSame( 'bad', expLogHealth::summary( $logs, 675 )['state'] );
        $none = expLogHealth::summary( $logs, 0 );
        $this->assertNull( $none['share'] );
        $this->assertNull( $none['percent'] );
        $this->assertSame( 'ok', $none['state'] );
        $empty = expLogHealth::summary( array(), 100 );
        $this->assertSame( 0, $empty['total'] );
        $this->assertSame( array(), $empty['rows'] );
    }

    /** LV-122 */
    public function testFastestOverLimitWritableOwners()
    {
        $logs = array();
        foreach ( array( 'a' => 10, 'b' => 500, 'c' => 0, 'd' => 70, 'e' => 30, 'f' => 90, 'g' => 1000 ) as $k => $g )
            $logs[$k] = $this->log( $k, array( 'growth' => $g ) );
        $logs['h'] = $this->log( 'h' );
        $logs['big'] = $this->log( 'big', array( 'size' => 5000 ) );
        $logs['bigger'] = $this->log( 'bigger', array( 'size' => 9000 ) );
        $logs['nolimit'] = $this->log( 'nolimit', array( 'size' => 99999, 'limits' => array( 'max_size' => 0 ) ) );
        $logs['root'] = $this->log( 'root', array( 'writable' => false, 'owner' => 'root', 'owner_group' => 'root' ) );
        $s = expLogHealth::summary( $logs, 0 );
        $this->assertSame( array( 'g', 'b', 'f', 'd', 'e' ), array_column( $s['fastest'], 'key' ) );
        $this->assertSame( array( 'bigger', 'big' ), array_column( $s['over_limit'], 'key' ) );
        $this->assertSame( array( 'root' ), array_column( $s['not_writable'], 'key' ) );
        $this->assertSame( array( 'alpha:psacln', 'root:root' ), $s['owners'] );
        $this->assertTrue( $s['mixed_owners'] );
        $this->assertSame( 'warn', $s['state'] );
        $one = expLogHealth::summary( array( 'a' => $this->log( 'a' ), 'b' => $this->log( 'b' ) ), 0 );
        $this->assertFalse( $one['mixed_owners'] );
        $this->assertSame( array(), $one['fastest'] );
        // the viewer's group never counts as the owner's group
        $two = expLogHealth::summary( array( 'a' => $this->log( 'a', array( 'group' => 'kernel' ) ), 'b' => $this->log( 'b', array( 'group' => 'site' ) ) ), 0 );
        $this->assertFalse( $two['mixed_owners'] );
        $this->assertSame( array( 'alpha:psacln' ), $two['owners'] );
    }

    /** LV-123 */
    public function testNewestError()
    {
        $f = $this->dir . '/error.log';
        file_put_contents( $f, '' );
        $this->assertNull( expLogHealth::newestError( $f, 'error' ) );
        $this->assertNull( expLogHealth::newestError( $this->dir . '/missing.log' ) );
        $lines = "[ Oct 17 2026 01:00:00 ] [192.0.2.1] [admin] [https://x.test/a] eZDB:\nfirst\n"
               . "[ Oct 17 2026 01:00:05 ] [192.0.2.1] [admin] [https://x.test/b] eZTemplate:\nsecond\n";
        file_put_contents( $f, $lines );
        $e = expLogHealth::newestError( $f, 'error' );
        $this->assertSame( 'error', $e['level'] );
        $this->assertStringContainsString( 'second', $e['message'] );
        $this->assertSame( strpos( $lines, '[ Oct 17 2026 01:00:05' ), $e['offset'] );

        $n = $this->dir . '/notice.log';
        file_put_contents( $n, "[ Oct 17 2026 01:00:00 ] [192.0.2.1] [admin] [https://x.test/a] Error: broken\n"
                             . "[ Oct 17 2026 01:00:01 ] [192.0.2.1] [admin] [https://x.test/a] Notice: fine\n" );
        $e = expLogHealth::newestError( $n, 'notice' );
        $this->assertStringContainsString( 'broken', $e['message'] );
        file_put_contents( $n, "[ Oct 17 2026 01:00:01 ] [192.0.2.1] [admin] [https://x.test/a] Notice: fine\n" );
        $this->assertNull( expLogHealth::newestError( $n, 'notice' ) );

        $big = $this->dir . '/big.log';
        $pad = str_repeat( "[ Oct 17 2026 01:00:00 ] [192.0.2.1] [admin] [https://x.test/a] Notice: padding padding\n", 2000 );
        file_put_contents( $big, $pad . "[ Oct 17 2026 02:00:00 ] [192.0.2.1] [admin] [https://x.test/z] Error: at the end\n" . $pad );
        $this->assertGreaterThan( expLogHealth::TAIL_BYTES, filesize( $big ) );
        $this->assertNull( expLogHealth::newestError( $big, 'notice' ), 'outside the window' );
        file_put_contents( $big, $pad . "[ Oct 17 2026 02:00:00 ] [192.0.2.1] [admin] [https://x.test/z] Error: at the end\n" );
        $e = expLogHealth::newestError( $big, 'notice' );
        $this->assertStringContainsString( 'at the end', $e['message'] );
        $this->assertFalse( $e['cont'] );
    }

    /** LV-124 */
    public function testSettings()
    {
        $m = new expLogHealthTestManager();
        $logs = array( 'error' => $this->log( 'error' ), 'debug' => $this->log( 'debug' ) );
        $m->effective['error'] = array( 'max_size' => 4096, 'keep' => 5, 'compress' => true, 'max_age_days' => 30,
            'source' => array( 'max_size' => array( 'file' => 'logviewer.ini', 'section' => 'RotationSettings', 'var' => 'MaxSize[error]' ),
                               'keep' => array( 'file' => 'site.ini', 'section' => 'FileSettings', 'var' => 'LogFileRotateCount' ) ) );
        $s = expLogHealth::settings( $logs, $m );
        $this->assertSame( array( 'max_size', 'keep', 'compress', 'max_age_days' ), array_column( $s['error'], 'name' ) );
        $this->assertSame( 4096, $s['error'][0]['value'] );
        $this->assertSame( 'logviewer.ini', $s['error'][0]['file'] );
        $this->assertSame( 'MaxSize[error]', $s['error'][0]['var'] );
        $this->assertSame( 'FileSettings', $s['error'][1]['section'] );
        $this->assertSame( '', $s['error'][2]['file'], 'no source: the built-in default' );
        $this->assertSame( 2000, $s['debug'][0]['value'], 'no effective settings: the limits' );
        $m->throw = true;
        $this->assertSame( 2000, expLogHealth::settings( $logs, $m )['error'][0]['value'] );
        $this->assertSame( 3, expLogHealth::settings( $logs, null )['error'][1]['value'] );
    }

    /** LV-125 */
    public function testTemplateViewAndSettings()
    {
        $root = dirname( __DIR__, 4 );
        $a = file_get_contents( "$root/design/admin/templates/setup/logs_health.tpl" );
        $b = file_get_contents( "$root/design/admin4/templates/setup/logs_health.tpl" );
        $this->assertSame( $a, $b, 'both designs carry the same template' );
        $this->assertDoesNotMatchRegularExpression( '/<style|style="/i', $b );
        $this->assertStringContainsString( "{'Log health'|i18n( 'design/admin/setup/logs' )}", $b );
        foreach ( array( 'message', 'time_text', 'owner' ) as $field )
            $this->assertMatchesRegularExpression( '/\.' . $field . '\|wash\}/', $b, $field . ' is washed' );
        $this->assertFileExists( "$root/kernel/private/classes/views/setup/logshealth.php" );
        $this->assertMatchesRegularExpression( '/^\[HealthSettings\]\n(#.*\n)+DiskBudget=268435456$/m', file_get_contents( "$root/settings/logviewer.ini" ) );
        $this->assertSame( file_get_contents( "$root/design/admin/stylesheets/modules/setup.css" ) !== false, true );
        $this->assertStringContainsString( '.a4-setup-loghealth-meter', file_get_contents( "$root/design/admin4/stylesheets/modules/setup.css" ) );
    }
}
