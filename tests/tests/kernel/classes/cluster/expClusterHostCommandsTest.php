<?php
/**
 * The commands that run on the hosts of settings/cluster.ini over ssh (exp:host:run, exp:cluster:run,
 * exp:cluster:cache, exp:cluster:deploy, exp:cluster:status, exp:cluster:hosts), without a database and without ssh:
 *
 *  CH-01  the settings: hosts in the order of HostList[], defaults from [ClusterSettings], every mistake named
 *         (no block, no Root, no Ssh, a target or option that could be read as an ssh option, a bad port, user or
 *         engine); --hosts picks hosts in the settings' order and names the unknown ones
 *  CH-02  the command lines: ssh with BatchMode=yes, no standard input, no terminal, the port, the extra options and
 *         -- before the target; cd into the root, sudo -n for User=, the PHP binary, --allow-root-user once; a word
 *         with quotes, spaces or shell characters reaches the host as one word (run through a real sh)
 *  CH-03  the run, with a fake ssh: the steps of a host stop at its first failure, the other hosts go on; PASS/FAIL
 *         per host, exit 1 when one failed, ssh's 255 explained; hosts with Ssh=local run for real, one after the
 *         other and all at once, and a host over its time limit is stopped
 *  CH-04  the ready-made operations: cache (every cache, then Velocity's response cache on a Velocity host; tags,
 *         ids, purge; --full as a deploy), deploy (the host's options, --no-velocity on an fpm host, as the deploy
 *         user), status (key=value lines, read back)
 *  CH-05  the commands: the split at --, the step of exp:host:run and exp:cluster:run, the options of
 *         exp:cluster:cache and exp:cluster:deploy, the status table; the scripts, their aliases and the settings file
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/cluster/expClusterHostCommandsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\Command\Kernel\Expclusterbase;
use Exponential\Command\Kernel\Expclustercache;
use Exponential\Command\Kernel\Expclusterdeploy;
use Exponential\Command\Kernel\Expclusterstatus;
use Exponential\Command\Kernel\Exphostrun;

class expClusterHostCommandsTest extends PHPUnit\Framework\TestCase
{
    const SCRIPTS = array( 'exphostrun' => 'exp:host:run', 'expclusterrun' => 'exp:cluster:run', 'expclustercache' => 'exp:cluster:cache',
                           'expclusterdeploy' => 'exp:cluster:deploy', 'expclusterstatus' => 'exp:cluster:status', 'expclusterhosts' => 'exp:cluster:hosts' );

    /** @var string|null */
    protected $dir;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 5 ) );
    }

    protected function tearDown(): void
    {
        expClusterRemote::setRunner( null );
        if ( $this->dir !== null && is_dir( $this->dir ) )
        {
            foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ) as $file )
                $file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() );
            rmdir( $this->dir );
        }
        $this->dir = null;
    }

    /** Two hosts: web (root login, fpm) and app (site user, velocity, port, deploy options). */
    protected function settings( array $more = array() )
    {
        return array_replace_recursive( array(
            'ClusterSettings' => array( 'HostList' => array( 'web', 'app' ), 'DefaultPhp' => 'php', 'ConnectTimeout' => '15', 'Timeout' => '600',
                                        'Mode' => 'sequential', 'AllowRootUser' => 'enabled', 'SshOptions' => array( 'StrictHostKeyChecking=accept-new' ),
                                        'LogFile' => 'cluster.log' ),
            'Host_web' => array( 'Ssh' => 'root@web.example.com', 'Root' => '/var/www/site/', 'Engine' => 'fpm' ),
            'Host_app' => array( 'Ssh' => 'app-alias', 'Port' => '7822', 'Root' => '/srv/site', 'Php' => '/opt/php85/bin/php', 'User' => 'site',
                                 'DeployUser' => 'deployer', 'Engine' => 'velocity', 'DeployOptions' => array( '--packer' ), 'AllowRootUser' => 'disabled' ),
        ), $more );
    }

    protected function hosts( array $more = array() )
    {
        $hosts = expClusterHosts::fromSettings( $this->settings( $more ) );
        $this->assertSame( array(), $hosts->errors() );
        return $hosts;
    }

    /** A host with Ssh=local whose "PHP" is a program given here. */
    protected function localHost( $name, $php, $root = null )
    {
        return array( 'name' => $name, 'ssh' => 'local', 'port' => 0, 'root' => $root !== null ? $root : $this->tempDir(), 'php' => $php, 'user' => '',
                      'deploy_user' => '', 'engine' => 'fpm', 'deploy_options' => array(), 'allow_root_user' => false );
    }

    protected function tempDir()
    {
        if ( $this->dir === null )
        {
            $this->dir = sys_get_temp_dir() . '/expclusterhosts-' . getmypid() . '-' . mt_rand();
            mkdir( $this->dir, 0700, true );
        }
        return $this->dir;
    }

    // ------------------------------------------------------------------ CH-01

    public function testSettingsAndDefaults()
    {
        $hosts = $this->hosts();
        $this->assertSame( array( 'web', 'app' ), $hosts->names() );
        $web = $hosts->host( 'web' );
        $this->assertSame( '/var/www/site', $web['root'], 'the trailing slash goes' );
        $this->assertSame( 'php', $web['php'], 'DefaultPhp' );
        $this->assertSame( 0, $web['port'] );
        $this->assertSame( '', $web['user'] );
        $this->assertTrue( $web['allow_root_user'], '[ClusterSettings] AllowRootUser' );
        $app = $hosts->host( 'app' );
        $this->assertSame( 7822, $app['port'] );
        $this->assertSame( 'site', $app['user'] );
        $this->assertSame( 'deployer', $app['deploy_user'] );
        $this->assertSame( array( '--packer' ), $app['deploy_options'] );
        $this->assertFalse( $app['allow_root_user'], 'the host block wins' );
        $settings = $hosts->settings();
        $this->assertSame( 15, $settings['connect_timeout'] );
        $this->assertSame( 600, $settings['timeout'] );
        $this->assertSame( 'sequential', $settings['mode'] );

        $empty = expClusterHosts::fromSettings( array() );
        $this->assertSame( array(), $empty->hosts() );
        $this->assertSame( array(), $empty->errors() );
        $this->assertSame( 1800, $empty->settings()['timeout'] );
        $this->assertTrue( $empty->settings()['allow_root_user'] );
    }

    public function testSettingsMistakesAreNamed()
    {
        $hosts = expClusterHosts::fromSettings( array(
            'ClusterSettings' => array( 'HostList' => array( 'web', 'web', 'gone', 'bad name', 'b1', 'b2', 'b3', 'b4' ), 'SshOptions' => array( 'ProxyCommand sh' ) ),
            'Host_web' => array( 'Ssh' => 'web', 'Root' => '/srv' ),
            'Host_b1' => array( 'Ssh' => '-oProxyCommand=evil', 'Root' => 'relative' ),
            'Host_b2' => array( 'Root' => '/srv', 'Port' => '99999', 'Engine' => 'iis' ),
            'Host_b3' => array( 'Ssh' => 'b3', 'Root' => '/srv; rm -rf /x', 'User' => 'a b', 'Php' => 'php -d x=1' ),
            'Host_b4' => array( 'Ssh' => 'b4', 'DeployOptions' => array( '--kernel; reboot' ) ),
        ) );
        $errors = implode( "\n", $hosts->errors() );
        foreach ( array( 'HostList[]=web is listed twice', 'HostList[]=gone has no block [Host_gone]', 'HostList[]=bad name: a host name',
                         'SshOptions[]=ProxyCommand sh is not Name=value', '[Host_b1] Ssh=-oProxyCommand=evil is not an ssh target',
                         '[Host_b1] Root=relative must be an absolute path', '[Host_b2] has no Ssh=', '[Host_b2] Port=99999 is not a port number',
                         '[Host_b2] Engine=iis: fpm or velocity', '[Host_b3] Root=/srv; rm -rf /x must be an absolute path', '[Host_b3] User=a b is not a user name',
                         '[Host_b3] Php=php -d x=1 must be a program name or path', '[Host_b4] has no Root=',
                         '[Host_b4] DeployOptions[]=--kernel; reboot is not an option' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
    }

    public function testSelectKeepsTheSettingsOrder()
    {
        $hosts = $this->hosts();
        $this->assertSame( array( 'web', 'app' ), array_keys( $hosts->select( null ) ) );
        $this->assertSame( array( 'web', 'app' ), array_keys( $hosts->select( 'app,web' ) ) );
        $this->assertSame( array( 'app' ), array_keys( $hosts->select( array( ' app ' ) ) ) );
        $this->assertSame( array( 'web' ), array_keys( $hosts->select( 'web,nope,other', $unknown ) ) );
        $this->assertSame( array( 'nope', 'other' ), $unknown );
    }

    // ------------------------------------------------------------------ CH-02

    public function testSshCommandLine()
    {
        $hosts = $this->hosts();
        $remote = new expClusterRemote( $hosts->settings() );
        $step = array( 'label' => 'x', 'console' => array( 'exp:cache', 'status' ) );

        $argv = $remote->argv( $hosts->host( 'web' ), $step );
        $this->assertSame( array( 'ssh', '-n', '-T', '-o', 'BatchMode=yes', '-o', 'ConnectTimeout=15', '-o', 'StrictHostKeyChecking=accept-new', '--',
                                  'root@web.example.com', 'cd /var/www/site && exec php bin/php/console exp:cache status --allow-root-user' ), $argv );

        $argv = $remote->argv( $hosts->host( 'app' ), $step );
        $this->assertSame( array( '-p', '7822' ), array_slice( $argv, 7, 2 ) );
        $this->assertSame( 'app-alias', $argv[count( $argv ) - 2] );
        $this->assertSame( 'cd /srv/site && exec sudo -n -H -u site -- /opt/php85/bin/php bin/php/console exp:cache status', end( $argv ),
                           'User= through sudo -n; AllowRootUser=disabled' );

        $deploy = expClusterRemote::remoteLine( $hosts->host( 'app' ), array( 'label' => 'd', 'as' => 'deploy', 'console' => array( 'exp:velocity', 'deploy' ) ) );
        $this->assertStringContainsString( 'sudo -n -H -u deployer --', $deploy );
        $login = expClusterRemote::remoteLine( $hosts->host( 'app' ), array( 'label' => 'l', 'as' => 'login', 'console' => array( 'exp:x' ) ) );
        $this->assertStringNotContainsString( 'sudo', $login );

        $once = expClusterRemote::remoteLine( $hosts->host( 'web' ), array( 'label' => 'x', 'console' => array( 'exp:x', '--allow-root-user' ) ) );
        $this->assertSame( 1, substr_count( $once, '--allow-root-user' ) );

        $shell = expClusterRemote::remoteLine( $hosts->host( 'web' ), array( 'label' => 's', 'shell' => 'echo "$1" $2' ) );
        $this->assertSame( "cd /var/www/site && exec sh -c 'echo \"\$1\" \$2' sh php --allow-root-user", $shell );

        $local = $remote->argv( $this->localHost( 'here', 'php', '/srv' ), $step );
        $this->assertSame( array( 'sh', '-c', 'cd /srv && exec php bin/php/console exp:cache status' ), $local );
    }

    public function testQuoting()
    {
        $this->assertSame( 'exp:cache', expClusterRemote::quote( 'exp:cache' ) );
        $this->assertSame( '--clear-tag=a,b', expClusterRemote::quote( '--clear-tag=a,b' ) );
        $this->assertSame( "''", expClusterRemote::quote( '' ) );
        $this->assertSame( "'a b'", expClusterRemote::quote( 'a b' ) );
        $this->assertSame( "'it'\\''s'", expClusterRemote::quote( "it's" ) );
        $this->assertSame( "'\$(id)'", expClusterRemote::quote( '$(id)' ) );
    }

    /** The words reach the host's program one by one, whatever they hold: a real sh runs the line. */
    public function testWordsArriveWhole()
    {
        $dir = $this->tempDir();
        $printer = $dir . '/printer';
        file_put_contents( $printer, "#!/bin/sh\nfor a in \"\$@\"; do printf '[%s]\\n' \"\$a\"; done\n" );
        chmod( $printer, 0755 );
        $words = array( 'exp:x', "it's", 'a b', '$(touch ' . $dir . '/owned)', '`id`', '; echo no', '"q"', '*' );
        $remote = new expClusterRemote( array() );
        $results = $remote->run( array( 'here' => $this->localHost( 'here', $printer, $dir ) ), array( array( 'label' => 'p', 'console' => $words ) ) );
        $this->assertTrue( $results['here']['ok'], $results['here']['steps'][0]['stderr'] );
        $expected = '';
        foreach ( array_merge( array( 'bin/php/console' ), $words ) as $word )
            $expected .= '[' . $word . "]\n";
        $this->assertSame( $expected, $results['here']['steps'][0]['stdout'] );
        $this->assertFileDoesNotExist( $dir . '/owned' );
    }

    // ------------------------------------------------------------------ CH-03

    public function testFakeTransportStopsAHostAtItsFirstFailure()
    {
        $hosts = $this->hosts();
        $calls = array();
        expClusterRemote::setRunner( function ( array $argv, array $host, array $step, $timeout ) use ( &$calls ) {
            $calls[] = $host['name'] . ':' . $step['label'];
            $this->assertSame( 'ssh', $argv[0] );
            $this->assertContains( 'BatchMode=yes', $argv );
            if ( $host['name'] === 'web' && $step['label'] === 'two' )
                return array( 3, 'partial', 'broken' );
            return array( 0, 'ok ' . $step['label'], '' );
        } );
        $steps = array( array( 'label' => 'one', 'console' => array( 'exp:a' ) ), array( 'label' => 'two', 'console' => array( 'exp:b' ) ),
                        array( 'label' => 'three', 'console' => array( 'exp:c' ) ) );
        $seen = array();
        $results = ( new expClusterRemote( $hosts->settings() ) )->run( $hosts->hosts(), $steps, true, function ( $name ) use ( &$seen ) { $seen[] = $name; } );
        $this->assertSame( array( 'web:one', 'web:two', 'app:one', 'app:two', 'app:three' ), $calls );
        $this->assertSame( array( 'web', 'app' ), $seen );
        $this->assertFalse( $results['web']['ok'] );
        $this->assertSame( 3, $results['web']['exit'] );
        $this->assertSame( 'broken', $results['web']['steps'][1]['stderr'] );
        $this->assertTrue( $results['app']['ok'] );

        $summary = expClusterRemote::summary( $results );
        $this->assertSame( 1, $summary['exit'] );
        $this->assertStringStartsWith( 'FAIL web', $summary['lines'][0] );
        $this->assertStringContainsString( 'two: exit 3', $summary['lines'][0] );
        $this->assertStringStartsWith( 'PASS app', $summary['lines'][1] );
        $this->assertSame( 'FAIL 1 of 2 host(s) passed', end( $summary['lines'] ) );
    }

    public function testSummaryExplainsSsh255AndAllPass()
    {
        $hosts = $this->hosts();
        expClusterRemote::setRunner( function ( array $argv, array $host ) {
            return $host['name'] === 'app' ? array( 255, '', 'Permission denied (publickey).' ) : array( 0, '', '' );
        } );
        $results = ( new expClusterRemote( $hosts->settings() ) )->run( $hosts->hosts(), array( 'web' => array( array( 'label' => 'w', 'console' => array( 'exp:w' ) ) ),
                                                                                              'app' => array( array( 'label' => 'a', 'console' => array( 'exp:a' ) ) ) ) );
        $summary = expClusterRemote::summary( $results );
        $this->assertStringContainsString( 'ssh could not connect or log in', $summary['lines'][1] );

        expClusterRemote::setRunner( function () { return array( 0, '', '' ); } );
        $summary = expClusterRemote::summary( ( new expClusterRemote( $hosts->settings() ) )->run( $hosts->hosts(), array( array( 'label' => 'x', 'console' => array( 'exp:x' ) ) ) ) );
        $this->assertSame( 0, $summary['exit'] );
        $this->assertSame( 'PASS 2 of 2 host(s) passed', end( $summary['lines'] ) );
        $this->assertSame( 1, expClusterRemote::summary( array() )['exit'], 'no host is no success' );
    }

    /** Local hosts run for real: one after the other and all at once give the same results; a failing host fails alone. */
    public function testLocalProcessesSequentialAndParallel()
    {
        $dir = $this->tempDir();
        $hosts = array( 'a' => $this->localHost( 'a', 'php', $dir ), 'b' => $this->localHost( 'b', 'php', $dir ), 'c' => $this->localHost( 'c', 'php', $dir ) );
        $steps = array(
            'a' => array( array( 'label' => 'say', 'shell' => 'echo "a in $(pwd)"; echo warn >&2' ), array( 'label' => 'again', 'shell' => 'echo a2' ) ),
            'b' => array( array( 'label' => 'fail', 'shell' => 'echo b; exit 7' ), array( 'label' => 'never', 'shell' => 'echo never' ) ),
            'c' => array( array( 'label' => 'php', 'shell' => 'echo "$1|$2"' ) ),
        );
        foreach ( array( false, true ) as $parallel )
        {
            $results = ( new expClusterRemote( array( 'timeout' => 60 ) ) )->run( $hosts, $steps, $parallel );
            $this->assertTrue( $results['a']['ok'] );
            $this->assertSame( 'a in ' . realpath( $dir ) . "\n", str_replace( $dir, realpath( $dir ), $results['a']['steps'][0]['stdout'] ) );
            $this->assertSame( "warn\n", $results['a']['steps'][0]['stderr'] );
            $this->assertSame( "a2\n", $results['a']['steps'][1]['stdout'] );
            $this->assertFalse( $results['b']['ok'] );
            $this->assertSame( 7, $results['b']['exit'] );
            $this->assertCount( 1, $results['b']['steps'], 'the step after a failure does not run' );
            $this->assertSame( "php|\n", $results['c']['steps'][0]['stdout'], 'the PHP binary is $1, no root flag is $2' );
        }
    }

    public function testTimeoutStopsAHost()
    {
        $dir = $this->tempDir();
        foreach ( array( false, true ) as $parallel )
        {
            $hosts = array( 'slow' => $this->localHost( 'slow', 'php', $dir ), 'quick' => $this->localHost( 'quick', 'php', $dir ) );
            $steps = array( 'slow' => array( array( 'label' => 'sleep', 'shell' => 'sleep 30' ) ), 'quick' => array( array( 'label' => 'ok', 'shell' => 'true' ) ) );
            $started = microtime( true );
            $results = ( new expClusterRemote( array( 'timeout' => 1 ) ) )->run( $hosts, $steps, $parallel );
            $this->assertLessThan( 15, microtime( true ) - $started );
            $this->assertSame( -1, $results['slow']['exit'] );
            $this->assertStringContainsString( 'timed out after 1 s', $results['slow']['steps'][0]['stderr'] );
            $this->assertTrue( $results['quick']['ok'] );
        }
    }

    public function testPlanRunsNothing()
    {
        $hosts = $this->hosts();
        expClusterRemote::setRunner( function () { throw new RuntimeException( 'a plan must not run anything' ); } );
        $plan = ( new expClusterRemote( $hosts->settings() ) )->plan( $hosts->hosts(), array( array( 'label' => 'x', 'console' => array( 'exp:a', 'b c' ) ) ) );
        $this->assertSame( array( 'web', 'app' ), array_keys( $plan ) );
        $this->assertSame( 'x', $plan['web'][0]['label'] );
        $this->assertStringStartsWith( 'ssh -n -T -o BatchMode=yes', $plan['web'][0]['command'] );
        $this->assertStringContainsString( "root@web.example.com 'cd /var/www/site && exec php bin/php/console exp:a '\\''b c'\\'' --allow-root-user'", $plan['web'][0]['command'] );
    }

    // ------------------------------------------------------------------ CH-04

    public function testCacheSteps()
    {
        $hosts = $this->hosts();
        $web = expClusterOperations::cacheSteps( $hosts->host( 'web' ) );
        $this->assertCount( 1, $web, 'an fpm host has no response cache of Velocity' );
        $this->assertSame( array( 'exp:ezcache', '--clear-all' ), $web[0]['console'] );
        $app = expClusterOperations::cacheSteps( $hosts->host( 'app' ) );
        $this->assertSame( array( 'exp:velocity', 'cache', 'clear', '--apply' ), $app[1]['console'], 'the response cache last' );

        $some = expClusterOperations::cacheSteps( $hosts->host( 'web' ), array( 'tags' => array( 'template', 'content' ), 'ids' => array( 'ini' ), 'purge' => true ) );
        $this->assertSame( array( 'exp:ezcache', '--clear-tag=template,content', '--clear-id=ini', '--purge' ), $some[0]['console'] );

        $full = expClusterOperations::cacheSteps( $hosts->host( 'web' ), array( 'full' => true ) );
        $this->assertSame( array( 'exp:velocity', 'deploy', '--all-caches', '--no-autoload', '--no-velocity' ), $full[0]['console'] );
        $this->assertSame( 'deploy', $full[0]['as'] );
        $full = expClusterOperations::cacheSteps( $hosts->host( 'app' ), array( 'full' => true ) );
        $this->assertSame( array( 'exp:velocity', 'deploy', '--all-caches', '--no-autoload', '--packer' ), $full[0]['console'] );
    }

    public function testDeploySteps()
    {
        $hosts = $this->hosts();
        $web = expClusterOperations::deploySteps( $hosts->host( 'web' ), array( '--kernel' ) );
        $this->assertSame( array( 'exp:velocity', 'deploy', '--kernel', '--no-velocity' ), $web[0]['console'] );
        $this->assertSame( 'deploy', $web[0]['as'] );
        $app = expClusterOperations::deploySteps( $hosts->host( 'app' ), array( '--packer', '--dry-run' ) );
        $this->assertSame( array( 'exp:velocity', 'deploy', '--packer', '--dry-run' ), $app[0]['console'], 'each option once' );
        $line = expClusterRemote::remoteLine( $hosts->host( 'app' ), $app[0] );
        $this->assertStringContainsString( 'sudo -n -H -u deployer --', $line );
    }

    public function testStatusStepAndParse()
    {
        $hosts = $this->hosts();
        $web = expClusterOperations::statusSteps( $hosts->host( 'web' ) );
        $this->assertStringNotContainsString( 'velocity', $web[0]['shell'] );
        $app = expClusterOperations::statusSteps( $hosts->host( 'app' ) );
        $this->assertStringContainsString( 'exp:velocity status', $app[0]['shell'] );
        $this->assertStringNotContainsString( "'", $app[0]['shell'] . $web[0]['shell'], 'no single quote, so the line quotes cleanly' );

        $parsed = expClusterOperations::parseStatus( "noise\nversion=console (Exponential) 6.0.15alpha8\nphp=PHP 8.5.1 (cli) (built: x)\ngit_head=abc1234\n"
                                                    . "git_branch=main\ngit_date=2026-11-27T10:00:00-08:00\nmaintenance=off\nvelocity=running\n" );
        $this->assertSame( array( 'version' => '6.0.15alpha8', 'php' => '8.5.1', 'git_head' => 'abc1234', 'git_branch' => 'main',
                                  'git_date' => '2026-11-27T10:00:00-08:00', 'maintenance' => 'off', 'velocity' => 'running' ), $parsed );
    }

    /** The status script runs in a real sh against a stand-in installation. */
    public function testStatusScriptRunsInSh()
    {
        $dir = $this->tempDir();
        mkdir( $dir . '/bin/php', 0700, true );
        mkdir( $dir . '/var', 0700, true );
        touch( $dir . '/var/maintenance.json' );
        $php = $dir . '/fakephp';
        file_put_contents( $php, "#!/bin/sh\nif [ \"\$1\" = -v ]; then echo 'PHP 8.4.2 (cli)'; else echo 'console (Exponential) 6.0.15'; fi\n" );
        chmod( $php, 0755 );
        $host = $this->localHost( 'here', $php, $dir );
        $results = ( new expClusterRemote( array() ) )->run( array( 'here' => $host ), expClusterOperations::statusSteps( $host ) );
        $status = expClusterOperations::parseStatus( $results['here']['steps'][0]['stdout'] );
        $this->assertSame( '6.0.15', $status['version'] );
        $this->assertSame( '8.4.2', $status['php'] );
        $this->assertSame( 'on', $status['maintenance'] );
        $this->assertArrayHasKey( 'git_head', $status );
    }

    // ------------------------------------------------------------------ CH-05

    public function testSplitAtDoubleDash()
    {
        $this->assertSame( array( array( 'web', '--dry-run' ), array( 'exp:cache', 'status', '--', '-q' ), true ),
                           Expclusterbase::splitArguments( array( 'web', '--dry-run', '--', 'exp:cache', 'status', '--', '-q' ) ) );
        $this->assertSame( array( array( 'web' ), array(), false ), Expclusterbase::splitArguments( array( 'web' ) ) );
    }

    public function testRemoteStep()
    {
        $this->assertSame( array( 'label' => 'exp:cache status', 'console' => array( 'exp:cache', 'status' ), 'as' => 'user' ),
                           Exphostrun::remoteStep( array( 'exp:cache', 'status' ), false, 'user' ) );
        $this->assertSame( array( 'label' => 'uptime -p', 'shell' => 'uptime -p', 'as' => 'login' ), Exphostrun::remoteStep( array( 'uptime', '-p' ), true, 'login' ) );
        $this->assertNull( Exphostrun::remoteStep( array(), false, 'user', $error ) );
        $this->assertStringContainsString( 'after --', $error );
        $this->assertNull( Exphostrun::remoteStep( array( '--help' ), false, 'user', $error ) );
        $this->assertStringContainsString( 'starts with its name', $error );
        $this->assertNull( Exphostrun::remoteStep( array( 'exp:x' ), false, 'root', $error ) );
        $this->assertStringContainsString( '--as must be user, deploy or login', $error );
    }

    public function testCacheAndDeployOptions()
    {
        $this->assertSame( array( 'tags' => array( 'template', 'ini' ), 'ids' => array(), 'purge' => false, 'full' => false ),
                           Expclustercache::request( 'clear', array( 'tag' => 'template, ini' ) ) );
        $this->assertNull( Expclustercache::request( 'wipe', array(), $error ) );
        $this->assertNull( Expclustercache::request( 'clear', array( 'tag' => 'a;b' ), $error ) );
        $this->assertStringContainsString( 'Not a cache tag or id', $error );
        $this->assertNull( Expclustercache::request( 'clear', array( 'full' => true, 'tag' => 'ini' ), $error ) );
        $this->assertSame( array( '--kernel', '--packer', '--dry-run' ), Expclusterdeploy::deployOptions( array( 'packer' => true, 'kernel' => true, 'remote-dry-run' => true,
                                                                                                             'dry-run' => true ) ),
                           'the order of the flags, and --dry-run of this command is not handed on' );
    }

    public function testStatusTable()
    {
        $result = function ( $stdout, $ok = true ) {
            return array( 'ok' => $ok, 'exit' => $ok ? 0 : 255, 'ms' => 1, 'steps' => array( array( 'label' => 'status', 'exit' => $ok ? 0 : 255, 'stdout' => $stdout, 'stderr' => '', 'ms' => 1 ) ) );
        };
        $lines = Expclusterstatus::table( array(
            'web' => $result( "version=6.0.15\nphp=PHP 8.5.1\ngit_head=aaa1111\ngit_branch=main\nmaintenance=off\n" ),
            'app' => $result( "version=6.0.15\nphp=PHP 8.5.1\ngit_head=bbb2222\ngit_branch=main\nmaintenance=on\nvelocity=running\n" ),
            'gone' => $result( '', false ),
        ) );
        $this->assertMatchesRegularExpression( '/^HOST\s+VERSION\s+PHP\s+GIT\s+BRANCH\s+MAINTENANCE\s+VELOCITY$/', $lines[0] );
        $this->assertMatchesRegularExpression( '/^web\s+6\.0\.15\s+8\.5\.1\s+aaa1111\s+main\s+off\s+-$/', $lines[1] );
        $this->assertMatchesRegularExpression( '/^gone\s+no answer/', $lines[3] );
        $this->assertSame( 'WARNING the hosts are on different git heads: aaa1111 (web), bbb2222 (app)', $lines[4] );
    }

    public function testScriptsAliasesAndSettingsFile()
    {
        foreach ( self::SCRIPTS as $script => $alias )
        {
            $file = "bin/php/$script.php";
            $this->assertFileExists( $file );
            $this->assertTrue( is_executable( $file ), "$file is executable" );
            $this->assertStringStartsWith( "#!/usr/bin/env php\n", file_get_contents( $file ) );
            foreach ( array( $file, "kernel/private/classes/commands/$script.php" ) as $source )
            {
                preg_match_all( '/^\s*\*\s*@alias\s+(\S+)$/m', file_get_contents( $source ), $m );
                $this->assertSame( array( $alias ), $m[1], $source );
            }
            $this->assertTrue( class_exists( 'Exponential\\Command\\Kernel\\' . ucfirst( $script ) ), "$script is in the class map" );
        }
        $ini = file_get_contents( 'settings/cluster.ini' );
        foreach ( array( '[ClusterSettings]', 'HostList[]', 'DefaultPhp=php', 'ConnectTimeout=20', 'Timeout=1800', 'Mode=sequential', 'AllowRootUser=enabled',
                         'SshOptions[]', 'LogFile=cluster.log' ) as $line )
            $this->assertStringContainsString( "\n$line\n", $ini );
        $this->assertDoesNotMatchRegularExpression( '/^\s*(Password|Key|Secret)\s*=/mi', $ini, 'no secret in the settings' );
    }
}
