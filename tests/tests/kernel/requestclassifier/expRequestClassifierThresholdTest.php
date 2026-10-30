<?php
/**
 * The threshold rule set of the request classifier: more than [ThresholdSettings] Max404PerMinute not-found answers
 * to one client within a minute. DB-less; the counter files go to a temporary directory.
 *
 *  RC-20  under the limit no verdict, over it threshold/404-rate (high above twice the limit); other clients and
 *         other statuses are not counted; a new window starts from zero; 0 switches the rule off
 *  RC-21  the counter is shared: two classifier instances, and separate PHP processes counting at the same time,
 *         add up in the same files without losing a count
 *  RC-22  the files hold a salted hash of the address, never the address; a new file is created with the log file
 *         permissions; a counter that cannot write never flags anybody
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRequestClassifierThresholdTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $dir;

    protected function setUp(): void
    {
        expRequestClassifier::reset( true );
        $this->dir = sys_get_temp_dir() . '/rct-' . bin2hex( random_bytes( 6 ) );
        mkdir( $this->dir, 0700, true );
    }

    protected function tearDown(): void
    {
        self::remove( $this->dir );
        expRequestClassifier::reset( true );
    }

    protected static function remove( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( scandir( $dir ) as $name )
        {
            if ( $name === '.' || $name === '..' )
                continue;
            $path = $dir . '/' . $name;
            if ( is_dir( $path ) && !is_link( $path ) )
                self::remove( $path );
            else
                unlink( $path );
        }
        rmdir( $dir );
    }

    protected function classifier( $max = 5 )
    {
        return new expRequestClassifier( array(
            'ruleSets' => array( 'threshold' ),
            'threshold' => array( 'max' => $max ),
            'compileCache' => false,
            'salt' => 'test-salt',
            'cacheDir' => $this->dir . '/cache',
        ) );
    }

    protected function context( $ip, $status = 404, $uri = '/no-such-page' )
    {
        return expRequestContext::create( array( 'uri' => $uri, 'ip' => $ip, 'status' => $status, 'siteaccess' => 'site' ) );
    }

    /** RC-20 */
    public function testUnderAndOverTheLimit()
    {
        $classifier = $this->classifier( 5 );
        for ( $i = 1; $i <= 5; $i++ )
            $this->assertNull( $classifier->classify( $this->context( '198.51.100.1' ) ), "404 number $i" );
        // 200s, 403s and 500s are not counted, and do not open a file
        foreach ( array( 200, 301, 403, 500 ) as $status )
            $this->assertNull( $classifier->classify( $this->context( '198.51.100.1', $status ) ) );
        $this->assertNull( $classifier->classify( expRequestContext::create( array( 'uri' => '/x', 'ip' => '198.51.100.1' ) ) ), 'status unknown' );

        $v = $classifier->classify( $this->context( '198.51.100.1', 410 ) );
        $this->assertNotNull( $v );
        $this->assertSame( 'threshold/404-rate', $v->key() );
        $this->assertSame( 'medium', $v->confidence );
        $this->assertSame( '6 in 60s', $v->detail );
        for ( $i = 7; $i <= 10; $i++ )
            $classifier->classify( $this->context( '198.51.100.1' ) );
        $this->assertSame( 'high', $classifier->classify( $this->context( '198.51.100.1' ) )->confidence, 'more than twice the limit' );

        // another client has its own count
        $this->assertNull( $classifier->classify( $this->context( '198.51.100.2' ) ) );

        // a new window starts from zero
        $counter = new expRequestThresholdCounter( $this->dir . '/cache/counter', 60 );
        $key = $classifier->ipKey( '198.51.100.1' );
        $this->assertSame( 11, $counter->count( $key ) );
        $this->assertSame( 1, $counter->hit( $key, time() + 120 ) );
        $this->assertSame( 0, $counter->count( $classifier->ipKey( '198.51.100.9' ) ) );

        // 0 switches the rule off
        $this->assertNull( $this->classifier( 0 )->classify( $this->context( '198.51.100.1' ) ) );
    }

    /** RC-21: two instances (as two requests of two workers), each starting with nothing in memory */
    public function testSharedBetweenInstances()
    {
        $a = $this->classifier( 4 );
        $b = $this->classifier( 4 );
        $this->assertNull( $a->classify( $this->context( '192.0.2.10' ) ) );
        $this->assertNull( $b->classify( $this->context( '192.0.2.10' ) ) );
        expRequestClassifier::reset( true );
        $this->assertNull( $a->classify( $this->context( '192.0.2.10' ) ) );
        $this->assertNull( $this->classifier( 4 )->classify( $this->context( '192.0.2.10' ) ) );
        $this->assertSame( 'threshold/404-rate', $b->classify( $this->context( '192.0.2.10' ) )->key() );
    }

    /** RC-21: separate PHP processes counting at the same time lose no count */
    public function testSharedBetweenProcesses()
    {
        if ( !function_exists( 'proc_open' ) )
            $this->markTestSkipped( 'proc_open is not available' );
        $root = dirname( __DIR__, 4 );
        $script = $this->dir . '/hit.php';
        file_put_contents( $script, '<?php
require ' . var_export( $root . '/kernel/classes/requestclassifier/exprequestthresholdcounter.php', true ) . ';
require ' . var_export( $root . '/kernel/classes/requestclassifier/exprequestclassifier.php', true ) . ';
$counter = new expRequestThresholdCounter( $argv[1], 60 );
for ( $i = 0; $i < (int)$argv[3]; $i++ )
    $counter->hit( $argv[2] );
' );
        $key = sha1( 'shared-client' );
        // the bare processes have no eZDir: the counter's directory is there before they start
        if ( !is_dir( $this->dir . '/cache/counter' ) )
            mkdir( $this->dir . '/cache/counter', 0777, true );
        $processes = array();
        for ( $p = 0; $p < 3; $p++ )
        {
            $cmd = array( ...expTestBarePhpCommand(), $script, $this->dir . '/cache/counter', $key, '40' );
            $processes[] = proc_open( $cmd, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
            $this->assertIsResource( end( $processes ) );
        }
        foreach ( $processes as $process )
            $this->assertSame( 0, proc_close( $process ) );
        unlink( $script );

        $counter = new expRequestThresholdCounter( $this->dir . '/cache/counter', 60 );
        $count = $counter->count( $key );
        // a process that ran over a minute boundary counts into the next window: then the total is split
        if ( $count < 120 && (int)date( 's' ) < 5 )
            $this->markTestSkipped( 'the processes ran over a minute boundary' );
        $this->assertSame( 120, $count );
    }

    /** RC-22 */
    public function testFilesHoldNoAddress()
    {
        $classifier = $this->classifier( 5 );
        $classifier->classify( $this->context( '203.0.113.77' ) );
        $classifier->classify( $this->context( '2001:db8::77' ) );
        $files = glob( $this->dir . '/cache/counter/counter-*.json' );
        $this->assertNotEmpty( $files );
        $all = '';
        foreach ( $files as $file )
            $all .= file_get_contents( $file );
        $this->assertStringNotContainsString( '203.0.113.77', $all );
        $this->assertStringNotContainsString( '2001:db8::77', $all );
        $this->assertStringContainsString( $classifier->ipKey( '203.0.113.77' ), $all );
        $this->assertSame( sha1( '203.0.113.77' . "\n" . 'test-salt' ), $classifier->ipKey( '203.0.113.77' ) );
        $this->assertNotSame( sha1( '203.0.113.77' ), $classifier->ipKey( '203.0.113.77' ) );

        // a new file gets the log file permissions (0666, limited by the installation's file mode limit)
        if ( class_exists( 'eZINI' ) && class_exists( 'eZFile' ) )
        {
            $expected = eZFile::fileMode( octdec( eZINI::instance()->variable( 'FileSettings', 'LogFilePermissions' ) ) );
            $this->assertSame( $expected, fileperms( $files[0] ) & 0777 );
        }

        // a counter that cannot write never flags anybody
        $blocked = $this->dir . '/blocked';
        file_put_contents( $blocked, 'a file where the directory should be' );
        $counter = new expRequestThresholdCounter( $blocked . '/counter', 60 );
        $this->assertSame( 0, @$counter->hit( 'abc' ) );
        unlink( $blocked );
    }

    /** RC-22: the salt kept in the cache directory when none is configured */
    public function testSaltFile()
    {
        $make = function () {
            return new expRequestClassifier( array( 'ruleSets' => array(), 'compileCache' => false, 'cacheDir' => $this->dir . '/cache' ) );
        };
        $first = $make()->ipKey( '192.0.2.1' );
        $this->assertFileExists( $this->dir . '/cache/salt.txt' );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{64}$/', file_get_contents( $this->dir . '/cache/salt.txt' ) );
        $this->assertSame( $first, $make()->ipKey( '192.0.2.1' ), 'the same salt for every instance' );
        $this->assertNotSame( sha1( '192.0.2.1' ), $first );
    }
}
