<?php
/**
 * The providers of the system card (exp:expfetch, exp:info, exp:about), each with a fake context: made-up files, INI
 * settings, environment and values. No database, no web server, nothing read from this machine.
 *
 *  SCP-01 - Host: OS from os-release, uptime, load, CPU and cores, memory and disk with their warnings, shell, terminal
 *  SCP-02 - Host: exp:info lists every local disk of /proc/mounts (one per device)
 *  SCP-03 - PHP: version and its security support (ok, warn near the end, error after it), OPcache, JIT, extensions
 *  SCP-04 - Exponential: version and line; branch and commit of a checkout, a worktree and packed refs
 *  SCP-05 - Velocity: version, running, workers live and configured, ports and TLS, envvar names never values, demo mode
 *  SCP-06 - Web servers: Apache, nginx and PHP-FPM masters and pool workers from the process list
 *  SCP-07 - Database: type, version, host only (never user, password or name), no connection is an error
 *  SCP-08 - Site: siteaccesses, extensions, demo mode, debug for everyone, mail transport type only
 *  SCP-09 - Caches, help, cron and Composer: their states and hints
 *  SCP-10 - exp:info providers: extensions with versions, siteaccess details, the settings allowlist without secrets, queue
 *  SCP-11 - exp:about: the about text is parsed into paragraphs and rows
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSysCardProvidersTest extends PHPUnit\Framework\TestCase
{
    const NOW = 1792483200; // 2026-10-20 08:00 UTC
    const ROOT = '/srv/exponential';

    /**
     * @param expSysCardFact[] $facts
     * @return array key => expSysCardFact
     */
    private function byKey( array $facts )
    {
        $map = array();
        foreach ( $facts as $fact )
            $map[$fact->key] = $fact;
        return $map;
    }

    private function context( array $values, $mode = 'card' )
    {
        return expSysCardContext::fake( $values + array( 'now' => self::NOW, 'root' => self::ROOT ), $mode );
    }

    public function testHostFacts()
    {
        $context = $this->context( array(
            'files' => array(
                '/etc/os-release' => "NAME=\"AlmaLinux\"\nPRETTY_NAME=\"AlmaLinux 9.8 (Olive Jaguar)\"\n",
                '/proc/uptime' => "93784.12 100000.00\n",
                '/proc/cpuinfo' => "processor : 0\nmodel name : AMD EPYC 7B13 Processor\nprocessor : 1\nmodel name : AMD EPYC 7B13 Processor\n",
                '/proc/meminfo' => "MemTotal:       16384000 kB\nMemFree:  100 kB\nMemAvailable:    819200 kB\n",
            ),
            'host.kernel' => 'Linux 5.14.0 x86_64',
            'host.name' => 'web.example.org',
            'host.load' => array( 5.5, 2.0, 1.0 ),
            'host.disk:' . self::ROOT => array( 100.0 * 1073741824, 2.0 * 1073741824 ),
            'host.terminal_size' => '120x40',
            'env' => array( 'SHELL' => '/bin/zsh', 'TERM' => 'xterm-256color' ),
        ) );
        $facts = $this->byKey( ( new expSysCardHostProvider() )->collect( $context ) );
        $this->assertSame( 'AlmaLinux 9.8 (Olive Jaguar)', $facts['host.os']->value );
        $this->assertSame( 'Linux 5.14.0 x86_64', $facts['host.kernel']->value );
        $this->assertSame( 'web.example.org', $facts['host.name']->value );
        $this->assertSame( '1d 2h', $facts['host.uptime']->value );
        $this->assertSame( 'AMD EPYC 7B13 (2 cores)', $facts['host.cpu']->value );
        $this->assertSame( '5.50 2.00 1.00', $facts['host.load']->value );
        $this->assertSame( expSysCardFact::WARN, $facts['host.load']->severity, 'a load above twice the cores warns' );
        $this->assertStringContainsString( '95%', $facts['host.memory']->value );
        $this->assertSame( expSysCardFact::ERROR, $facts['host.memory']->severity );
        $this->assertSame( expSysCardFact::ERROR, $facts['host.disk']->severity, '98% full is an error' );
        $this->assertStringContainsString( 'full', $facts['host.disk']->hint );
        $this->assertSame( 'zsh', $facts['host.shell']->value );
        $this->assertSame( 'xterm-256color (120x40)', $facts['host.terminal']->value );
        $this->assertSame( 'system', $facts['host.os']->section );
    }

    public function testHostHealthyAndInfoDisks()
    {
        $context = $this->context( array(
            'files' => array(
                '/proc/meminfo' => "MemTotal: 1000000 kB\nMemAvailable: 900000 kB\n",
                '/proc/mounts' => "/dev/sda4 / xfs rw 0 0\n/dev/sda2 /boot xfs rw 0 0\nproc /proc proc rw 0 0\ntmpfs /run tmpfs rw 0 0\n",
            ),
            'host.os' => 'Linux',
            'host.load' => array( 0.5, 0.4, 0.3 ),
            'host.disk:' . self::ROOT => array( 1000.0, 600.0 ),
            'host.disk:/' => array( 1000.0, 600.0 ),
            'host.disk:/boot' => array( 2000.0, 1000.0 ),
        ), 'info' );
        $facts = $this->byKey( ( new expSysCardHostProvider() )->collect( $context ) );
        $this->assertSame( expSysCardFact::INFO, $facts['host.memory']->severity );
        $this->assertSame( '', $facts['host.memory']->hint );
        $this->assertSame( expSysCardFact::INFO, $facts['host.load']->severity );
        $this->assertArrayHasKey( 'host.disk', $facts );
        $this->assertArrayNotHasKey( 'host.disk:/', $facts, 'the root file system is the installation\'s disk: listed once' );
        $this->assertArrayHasKey( 'host.disk:/boot', $facts );
        $this->assertArrayNotHasKey( 'host.disk:/proc', $facts );
        $this->assertArrayNotHasKey( 'host.terminal', $facts, 'no TERM, no terminal fact' );
    }

    private function phpContext( $version, array $more = array() )
    {
        return $this->context( $more + array(
            'php.version' => $version,
            'php.sapi' => 'cli',
            'php.extensions' => array( 'Core', 'intl', 'mbstring', 'curl', 'gd', 'xsl', 'sodium', 'apcu', 'mysqli', 'Zend OPcache' ),
            'php.ini:opcache.enable' => '1',
            'php.ini:opcache.enable_cli' => '0',
            'php.ini:opcache.memory_consumption' => '256',
            'php.ini:opcache.jit' => 'tracing',
            'php.ini:opcache.jit_buffer_size' => '64M',
            'php.ini:memory_limit' => '512M',
            'ini' => array( 'syscard.ini' => array( 'Php' => array( 'SecuritySupportEnds' => array( '8.1' => '2025-12-31', '8.2' => '2026-12-31', '8.5' => '2029-12-31' ), 'WarnDays' => '180' ) ) ),
        ) );
    }

    public function testPhpVersionSupport()
    {
        $ok = $this->byKey( ( new expSysCardPhpProvider() )->collect( $this->phpContext( '8.5.11' ) ) );
        $this->assertSame( '8.5.11 (cli)', $ok['php.version']->value );
        $this->assertSame( expSysCardFact::OK, $ok['php.version']->severity );
        $near = $this->byKey( ( new expSysCardPhpProvider() )->collect( $this->phpContext( '8.2.29' ) ) );
        $this->assertSame( expSysCardFact::WARN, $near['php.version']->severity, '72 days before the end of support' );
        $this->assertStringContainsString( '2026-12-31', $near['php.version']->hint );
        $old = $this->byKey( ( new expSysCardPhpProvider() )->collect( $this->phpContext( '8.1.30' ) ) );
        $this->assertSame( expSysCardFact::ERROR, $old['php.version']->severity );
        $this->assertStringContainsString( 'no security fixes', $old['php.version']->hint );
        $unknown = $this->byKey( ( new expSysCardPhpProvider() )->collect( $this->phpContext( '9.0.0' ) ) );
        $this->assertSame( expSysCardFact::INFO, $unknown['php.version']->severity, 'a branch the table does not know is not judged' );
    }

    public function testPhpOpcacheJitExtensionsMemory()
    {
        $facts = $this->byKey( ( new expSysCardPhpProvider() )->collect( $this->phpContext( '8.5.11' ) ) );
        $this->assertSame( 'on, 256 MiB', $facts['php.opcache']->value );
        $this->assertSame( 'tracing, 64.0 MiB', $facts['php.jit']->value );
        $this->assertSame( 'intl mbstring curl gd xsl sodium apcu mysqli', $facts['php.extensions']->value );
        $this->assertSame( expSysCardFact::INFO, $facts['php.extensions']->severity );
        $this->assertSame( '512M', $facts['php.memory_limit']->value );

        $off = $this->byKey( ( new expSysCardPhpProvider() )->collect( $this->phpContext( '8.5.11', array(
            'php.ini:opcache.enable' => '0', 'php.ini:memory_limit' => '128M', 'php.extensions' => array( 'Core', 'mbstring', 'Zend OPcache' ) ) ) ) );
        $this->assertSame( 'off', $off['php.opcache']->value );
        $this->assertSame( expSysCardFact::WARN, $off['php.opcache']->severity );
        $this->assertStringContainsString( 'OPcache is off', $off['php.opcache']->hint );
        $this->assertSame( 'off', $off['php.jit']->value, 'no JIT without OPcache' );
        $this->assertSame( expSysCardFact::WARN, $off['php.extensions']->severity );
        $this->assertStringContainsString( 'intl', $off['php.extensions']->hint );
        $this->assertSame( expSysCardFact::WARN, $off['php.memory_limit']->severity );

        $missing = $this->byKey( ( new expSysCardPhpProvider() )->collect( $this->phpContext( '8.5.11', array( 'php.extensions' => array( 'Core' ) ) ) ) );
        $this->assertSame( 'not loaded', $missing['php.opcache']->value );
        $this->assertArrayNotHasKey( 'php.jit', $missing );
    }

    public function testExponentialVersionAndGit()
    {
        $values = array( 'exponential.version' => '6.0.15', 'exponential.state' => 'stable', 'exponential.alias' => '6.0',
                         'files' => array( self::ROOT . '/.git/HEAD' => "ref: refs/heads/main\n",
                                           self::ROOT . '/.git/refs/heads/main' => str_repeat( 'ab', 20 ) . "\n" ) );
        $facts = $this->byKey( ( new expSysCardExponentialProvider() )->collect( $this->context( $values ) ) );
        $this->assertSame( 'Exponential 6.0.15 (stable, 6.0 line)', $facts['exponential.version']->value );
        $this->assertSame( 'main @ ababababab', $facts['exponential.git']->value );

        // a worktree: .git is a file naming the git directory, whose commondir holds the refs
        $worktree = array( 'files' => array(
            self::ROOT . '/.git' => "gitdir: /repo/.git/worktrees/w1\n",
            '/repo/.git/worktrees/w1/HEAD' => "ref: refs/heads/feature\n",
            '/repo/.git/worktrees/w1/commondir' => "../..\n",
            '/repo/.git/worktrees/w1/../../packed-refs' => "# pack-refs\n" . str_repeat( 'cd', 20 ) . " refs/heads/feature\n",
        ) );
        $git = expSysCardExponentialProvider::git( $this->context( $worktree ) );
        $this->assertSame( array( 'branch' => 'feature', 'commit' => str_repeat( 'cd', 20 ) ), $git );

        $this->assertSame( array( 'branch' => '', 'commit' => str_repeat( '12', 20 ) ),
                           expSysCardExponentialProvider::git( $this->context( array( 'files' => array( self::ROOT . '/.git/HEAD' => str_repeat( '12', 20 ) ) ) ) ),
                           'a detached HEAD' );
        $this->assertNull( expSysCardExponentialProvider::git( $this->context( array() ) ), 'not a checkout' );
        $none = $this->byKey( ( new expSysCardExponentialProvider() )->collect( $this->context( array() ) ) );
        $this->assertArrayNotHasKey( 'exponential.git', $none );
    }

    public function testVelocity()
    {
        $pidFile = self::ROOT . '/var/vc/qbix/run/server.pid';
        $values = array(
            'velocity.version' => 'v0.0.4.54',
            'ini' => array( 'velocity.ini' => array( 'ServerSettings' => array( 'PidFile' => 'var/vc/qbix/run/server.pid', 'Workers' => '4', 'ForkPerRequest' => 'disabled' ) ) ),
            'files' => array(
                $pidFile => "1293\n",
                $pidFile . '.json' => json_encode( array( 'pid' => 1293, 'started' => self::NOW - 7200,
                                                          'args' => array( '--conf-dir=/etc/vc', '--port=8088', '--https-port=8080', '--workers=8' ),
                                                          'env' => array( 'DB_PASSWORD' => 'hunter2' ) ) ),
                '/proc/1293/status' => "Name: php\n",
                '/proc/1293/task/1293/children' => '2001 2002 2003',
                '/proc/2001/task/2001/children' => '3001',
                '/etc/vc/envvars' => "# Environment for the Velocity server process\nexport EXP_DEMO_MODE=1\nSECRET_TOKEN=abc123\n# export COMMENTED=1\n",
            ),
        );
        $facts = $this->byKey( ( new expSysCardVelocityProvider() )->collect( $this->context( $values ) ) );
        $this->assertSame( 'v0.0.4.54, running, up 2h 0m', $facts['velocity.server']->value );
        $this->assertSame( expSysCardFact::OK, $facts['velocity.server']->severity );
        $this->assertSame( '4 live / 8 configured', $facts['velocity.workers']->value );
        $this->assertSame( '8088 (HTTP), 8080 (HTTPS, TLS)', $facts['velocity.ports']->value );
        $this->assertSame( '2 variables', $facts['velocity.envvars']->value );
        // in the shared envvars demo mode is refused by the workers (every installation's server reads that file)
        $this->assertSame( 'refused (shared envvars)', $facts['velocity.demo']->value );
        $this->assertSame( expSysCardFact::WARN, $facts['velocity.demo']->severity );
        // in this installation's own site envvars (beside the site file it was started with) it is on
        $site = $values;
        $site['files'][$pidFile . '.json'] = json_encode( array( 'pid' => 1293, 'started' => self::NOW - 7200,
            'args' => array( '--conf-dir=/etc/vc', '--config=/etc/vc/sites-enabled/k1.example.invalid.conf', '--port=8088' ) ) );
        $site['files']['/etc/vc/envvars'] = "SECRET_TOKEN=abc123\n";
        $site['files']['/etc/vc/sites-available/k1.example.invalid.envvars'] = "export EXP_DEMO_MODE=1\n";
        $site['files']['/etc/vc/sites-available/other.example.invalid.envvars'] = "export EXP_DEMO_MODE=0\n";
        $siteFacts = $this->byKey( ( new expSysCardVelocityProvider() )->collect( $this->context( $site ) ) );
        $this->assertSame( 'on (Velocity)', $siteFacts['velocity.demo']->value );
        $this->assertSame( expSysCardFact::WARN, $siteFacts['velocity.demo']->severity );
        $site['files']['/etc/vc/sites-available/k1.example.invalid.envvars'] = "# nothing\n";
        $this->assertArrayNotHasKey( 'velocity.demo', $this->byKey( ( new expSysCardVelocityProvider() )->collect( $this->context( $site ) ) ) );
        $this->assertNull( expSysCardVelocityProvider::demoValue( null ) );
        $this->assertFalse( expSysCardVelocityProvider::demoValue( "EXP_DEMO_MODE=true\n" ) );
        $this->assertTrue( expSysCardVelocityProvider::demoValue( "export EXP_DEMO_MODE='1'\r\n" ) );
        $all = json_encode( array_map( function ( $f ) { return $f->toArray(); }, $facts ) );
        $this->assertStringNotContainsString( 'hunter2', $all );
        $this->assertStringNotContainsString( 'abc123', $all, 'envvar values are never shown' );

        $info = $this->byKey( ( new expSysCardVelocityProvider() )->collect( $this->context( $values, 'info' ) ) );
        $this->assertSame( 'EXP_DEMO_MODE, SECRET_TOKEN', $info['velocity.envvars']->value, 'exp:info names them' );
        $this->assertSame( 'persistent workers', $info['velocity.model']->value );
        $this->assertSame( 'pid 1293, 4 workers', $info['velocity.instance0']->value );

        $stopped = $values;
        unset( $stopped['files']['/proc/1293/status'] );
        $facts = $this->byKey( ( new expSysCardVelocityProvider() )->collect( $this->context( $stopped ) ) );
        $this->assertSame( 'v0.0.4.54, stopped', $facts['velocity.server']->value );
        $this->assertSame( '8 configured', $facts['velocity.workers']->value );

        $absent = $this->byKey( ( new expSysCardVelocityProvider() )->collect( $this->context( array() ) ) );
        $this->assertSame( 'not installed', $absent['velocity.server']->value );
    }

    public function testWebServers()
    {
        $files = array(
            '/proc/10/comm' => "httpd\n", '/proc/11/comm' => "httpd\n", '/proc/12/comm' => "nginx\n",
            '/proc/20/comm' => "php-fpm\n", '/proc/20/cmdline' => "php-fpm: master process (/opt/plesk/php/8.5/etc/php-fpm.conf)\0",
            '/proc/21/comm' => "php-fpm\n", '/proc/21/cmdline' => "php-fpm: pool www.example.org\0",
            '/proc/22/comm' => "php-fpm\n", '/proc/22/cmdline' => "php-fpm: pool shop.example.org\0",
            '/proc/30/comm' => "php-fpm\n", '/proc/30/cmdline' => "php-fpm: master process (/etc/php-fpm.conf)\0",
            '/proc/40/comm' => "bash\n",
        );
        $values = array( 'files' => $files, 'dirs' => array( '/proc' => array( '10', '11', '12', '20', '21', '22', '30', '40', 'self', 'meminfo' ) ) );
        $facts = $this->byKey( ( new expSysCardWebServerProvider() )->collect( $this->context( $values ) ) );
        $this->assertSame( 'running (2 processes)', $facts['webserver.apache']->value );
        $this->assertSame( 'running (1 process)', $facts['webserver.nginx']->value );
        $this->assertSame( '2 masters (8.5, system), 2 pool workers', $facts['webserver.fpm']->value );
        $this->assertArrayNotHasKey( 'webserver.pools', $facts );
        $info = $this->byKey( ( new expSysCardWebServerProvider() )->collect( $this->context( $values, 'info' ) ) );
        $this->assertSame( 'shop.example.org, www.example.org', $info['webserver.pools']->value );
        $none = $this->byKey( ( new expSysCardWebServerProvider() )->collect( $this->context( array() ) ) );
        $this->assertArrayHasKey( 'webserver.none', $none );
    }

    public function testDatabaseShowsHostOnly()
    {
        $ini = array( 'site.ini' => array( 'DatabaseSettings' => array( 'DatabaseImplementation' => 'ezmysqli', 'Server' => 'db.internal', 'Port' => '3306',
                                                                        'User' => 'siteuser', 'Password' => 's3cretPass', 'Database' => 'sitedb' ) ) );
        $facts = $this->byKey( ( new expSysCardDatabaseProvider() )->collect( $this->context( array( 'ini' => $ini, 'database.version' => '10.11.6-MariaDB-log' ) ) ) );
        $this->assertSame( 'MySQL/MariaDB 10.11.6-MariaDB', $facts['database.type']->value );
        $this->assertSame( expSysCardFact::OK, $facts['database.type']->severity );
        $this->assertSame( 'db.internal:3306', $facts['database.host']->value );
        $all = json_encode( array_map( function ( $f ) { return $f->toArray(); }, $facts ) );
        foreach ( array( 'siteuser', 's3cretPass', 'sitedb' ) as $secret )
            $this->assertStringNotContainsString( $secret, $all );

        $down = $this->byKey( ( new expSysCardDatabaseProvider() )->collect( $this->context( array( 'ini' => $ini, 'database.version' => false ) ) ) );
        $this->assertSame( expSysCardFact::ERROR, $down['database.type']->severity );
        $this->assertStringContainsString( 'does not answer', $down['database.type']->hint );

        $this->assertSame( 'mongo1.internal:27017', expSysCardDatabaseProvider::hostOf( 'MongoDB', 'mongodb://admin:pw@mongo1.internal:27017/site?authSource=admin' ) );
        $this->assertSame( 'local file', expSysCardDatabaseProvider::hostOf( 'SQLite', 'var/storage/site.db' ) );
        $this->assertSame( 'local socket', expSysCardDatabaseProvider::hostOf( 'MySQL/MariaDB', 'localhost', '', '/var/lib/mysql/mysql.sock' ) );
        $this->assertSame( 'db', expSysCardDatabaseProvider::hostOf( 'Oracle', 'user@db' ) );

        $off = array( 'ini' => $ini + array( 'syscard.ini' => array( 'Database' => array( 'AskVersion' => 'disabled' ) ) ) );
        $facts = $this->byKey( ( new expSysCardDatabaseProvider() )->collect( $this->context( $off ) ) );
        $this->assertSame( 'MySQL/MariaDB', $facts['database.type']->value, 'no question asked' );
    }

    public function testSite()
    {
        $ini = array( 'site.ini' => array(
            'SiteAccessSettings' => array( 'AvailableSiteAccessList' => array( 'site', 'admin', 'site' ) ),
            'SiteSettings' => array( 'DefaultAccess' => 'site' ),
            'DebugSettings' => array( 'DebugOutput' => 'enabled', 'DebugByIP' => 'disabled', 'DebugByUser' => 'disabled' ),
            'MailSettings' => array( 'Transport' => 'SMTP', 'TransportServer' => 'smtp.example.org', 'TransportPassword' => 'mailpw' ),
        ) );
        $facts = $this->byKey( ( new expSysCardSiteProvider() )->collect( $this->context( array( 'ini' => $ini, 'site.extensions' => array( 'a', 'b', 'c' ), 'site.demo' => true ) ) ) );
        $this->assertSame( '2, default site', $facts['site.siteaccesses']->value );
        $this->assertSame( '3 active', $facts['site.extensions']->value );
        $this->assertSame( 'on', $facts['site.demo']->value );
        $this->assertSame( expSysCardFact::WARN, $facts['site.demo']->severity );
        $this->assertSame( 'on, for everyone', $facts['site.debug']->value );
        $this->assertSame( expSysCardFact::WARN, $facts['site.debug']->severity );
        $this->assertSame( 'smtp', $facts['site.mail']->value, 'the transport type only' );
        $all = json_encode( array_map( function ( $f ) { return $f->toArray(); }, $facts ) );
        $this->assertStringNotContainsString( 'smtp.example.org', $all );
        $this->assertStringNotContainsString( 'mailpw', $all );

        $quiet = $this->byKey( ( new expSysCardSiteProvider() )->collect( $this->context( array( 'site.demo' => null, 'env' => array( 'EXP_DEMO_MODE' => '0' ) ) ) ) );
        $this->assertSame( 'off', $quiet['site.demo']->value );
        $this->assertSame( 'off', $quiet['site.debug']->value );
        $this->assertSame( 'sendmail', $quiet['site.mail']->value );
    }

    /** SCP-09 the helpers behind the wording: a duration without "ago" for a sentence, a count with its noun */
    public function testElapsedAgoAndCounted()
    {
        $this->assertSame( '5 days', expSysCardBaseProvider::elapsed( self::NOW - 5 * 86400, self::NOW ) );
        $this->assertSame( '5 days ago', expSysCardBaseProvider::ago( self::NOW - 5 * 86400, self::NOW ) );
        $this->assertSame( '3 h', expSysCardBaseProvider::elapsed( self::NOW - 3 * 3600, self::NOW ) );
        $this->assertSame( '5 min ago', expSysCardBaseProvider::ago( self::NOW - 300, self::NOW ) );
        $this->assertSame( '1 part', expSysCardBaseProvider::counted( 1, 'part' ) );
        $this->assertSame( '0 parts', expSysCardBaseProvider::counted( 0, 'part' ) );
        $this->assertSame( '1,200 files', expSysCardBaseProvider::counted( 1200, 'file' ) );
        $this->assertSame( '2 processes', expSysCardBaseProvider::counted( 2, 'process', 'processes' ) );
    }

    public function testCachesHelpCronComposer()
    {
        $caches = $this->byKey( ( new expSysCardCacheProvider() )->collect( $this->context( array( 'ini' => array( 'site.ini' => array( 'ContentSettings' => array( 'ViewCaching' => 'disabled' ) ) ) ) ) ) );
        $this->assertSame( 'templates on; view, static off', $caches['caches.state']->value );
        $this->assertSame( expSysCardFact::WARN, $caches['caches.state']->severity );

        $sizes = $this->byKey( ( new expSysCardCacheProvider() )->collect( $this->context( array(
            'caches.dir' => 'var/site/cache',
            'caches.size:' . self::ROOT . '/var/site/cache' => array( 'bytes' => 3 * 1048576, 'files' => 1200, 'complete' => false,
                                                                      'entries' => array( 'template' => 2 * 1048576, 'ini' => 1048576 ) ) ), 'info' ) ) );
        $this->assertSame( 'at least 3.0 MiB in 1,200 files', $sizes['caches.size']->value );
        $this->assertSame( 'at least 2.0 MiB', $sizes['caches.dir:template']->value );
        $this->assertSame( 'caches', $sizes['caches.dir:template']->section );

        $help = $this->byKey( ( new expSysCardHelpProvider() )->collect( $this->context( array( 'help.state' => array( 'pages' => 7083, 'current' => false, 'languages' => array( 'eng-us', 'ger-de' ) ),
                                                                                              'help.coverage' => array( 'eng-us' => array( 'pages' => 7083, 'total' => 7083 ), 'ger-de' => array( 'pages' => 785, 'total' => 7083 ) ) ), 'info' ) ) );
        $this->assertSame( '7,083 pages, eng-us ger-de (index stale)', $help['help.state']->value );
        $this->assertSame( expSysCardFact::WARN, $help['help.state']->severity );
        $this->assertSame( '785 / 7,083 pages (11%)', $help['help.coverage:ger-de']->value );
        // a fresh index of every language: the count of exp:help index, no stale mark and no hint
        $fresh = $this->byKey( ( new expSysCardHelpProvider() )->collect( $this->context( array( 'help.state' => array( 'pages' => 7615, 'current' => true, 'stale' => array(), 'languages' => array( 'eng-us', 'ger-de' ) ) ) ) ) );
        $this->assertSame( '7,615 pages, eng-us ger-de', $fresh['help.state']->value );
        $this->assertSame( expSysCardFact::INFO, $fresh['help.state']->severity );
        $this->assertSame( '', $fresh['help.state']->hint );
        // one language's index out of date: that language is named
        $oneStale = $this->byKey( ( new expSysCardHelpProvider() )->collect( $this->context( array( 'help.state' => array( 'pages' => 7615, 'current' => false, 'stale' => array( 'ger-de' ), 'languages' => array( 'eng-us', 'ger-de' ) ) ) ) ) );
        $this->assertSame( '7,615 pages, eng-us ger-de (index stale: ger-de)', $oneStale['help.state']->value );
        $this->assertSame( expSysCardFact::WARN, $oneStale['help.state']->severity );
        $off = $this->byKey( ( new expSysCardHelpProvider() )->collect( $this->context( array( 'ini' => array( 'exphelp.ini' => array( 'HelpSettings' => array( 'Enabled' => 'false' ) ) ) ) ) ) );
        $this->assertSame( 'off', $off['help.state']->value );

        $log = self::ROOT . '/var/log/cronjob-frequent.log';
        $cronValues = array( 'files' => array( $log => 'ok' ), 'cron.mtime:' . $log => self::NOW - 300,
                             'ini' => array( 'cronjob.ini' => array( 'CronjobSettings' => array( 'Scripts' => array( 'unpublish.php' ) ),
                                                                     'CronjobPart-frequent' => array( 'Scripts' => array( 'notification.php', 'workflow.php' ) ) ) ) );
        $cron = $this->byKey( ( new expSysCardCronProvider() )->collect( $this->context( $cronValues, 'info' ) ) );
        $this->assertSame( 'last run 5 min ago, 2 parts', $cron['cron.last']->value );
        $this->assertSame( expSysCardFact::OK, $cron['cron.last']->severity );
        $this->assertSame( 'notification, workflow', $cron['cron.part:frequent']->value );
        $cronValues['cron.mtime:' . $log] = self::NOW - 5 * 86400;
        $stale = $this->byKey( ( new expSysCardCronProvider() )->collect( $this->context( $cronValues ) ) );
        $this->assertSame( expSysCardFact::WARN, $stale['cron.last']->severity );
        $this->assertSame( 'last run 5 days ago, 2 parts', $stale['cron.last']->value );
        $this->assertSame( 'No cronjob has run for 5 days: check the crontab and the cronjob logs.', $stale['cron.last']->hint );
        $cronValues['ini']['cronjob.ini'] = array( 'CronjobPart-frequent' => array( 'Scripts' => array( 'workflow.php' ) ) );
        $onePart = $this->byKey( ( new expSysCardCronProvider() )->collect( $this->context( $cronValues ) ) );
        $this->assertSame( 'last run 5 days ago, 1 part', $onePart['cron.last']->value );
        $never = $this->byKey( ( new expSysCardCronProvider() )->collect( $this->context( array() ) ) );
        $this->assertStringContainsString( 'no run found', $never['cron.last']->value );

        $installed = json_encode( array( 'packages' => array( array( 'name' => 'a/b' ), array( 'name' => 'c/d' ) ) ) );
        $composer = $this->byKey( ( new expSysCardComposerProvider() )->collect( $this->context( array( 'files' => array( self::ROOT . '/vendor/composer/installed.json' => $installed ), 'composer.issues' => 3 ) ) ) );
        $this->assertSame( '2 packages, 3 with issues', $composer['composer.packages']->value );
        $this->assertSame( expSysCardFact::WARN, $composer['composer.packages']->severity );
        $this->assertStringStartsWith( '3 Composer packages or extensions do not agree', $composer['composer.packages']->hint );
        $single = json_encode( array( 'packages' => array( array( 'name' => 'a/b' ) ) ) );
        $one = $this->byKey( ( new expSysCardComposerProvider() )->collect( $this->context( array( 'files' => array( self::ROOT . '/vendor/composer/installed.json' => $single ), 'composer.issues' => 1 ) ) ) );
        $this->assertSame( '1 package, 1 with issues', $one['composer.packages']->value );
        $this->assertStringStartsWith( '1 Composer package or extension does not agree', $one['composer.packages']->hint );
        $noVendor = $this->byKey( ( new expSysCardComposerProvider() )->collect( $this->context( array( 'files' => array( self::ROOT . '/composer.json' => '{}' ) ) ) ) );
        $this->assertSame( expSysCardFact::ERROR, $noVendor['composer.packages']->severity );
        $this->assertStringContainsString( 'composer install', $noVendor['composer.packages']->hint );
    }

    public function testInfoProviders()
    {
        $extensions = $this->byKey( ( new expSysCardExtensionsProvider() )->collect( $this->context( array( 'extensions.list' => array(
            array( 'extension' => 'ezjscore', 'version' => '1.4.0' ), array( 'extension' => 'local_ext', 'version' => '' ) ) ), 'info' ) ) );
        $this->assertSame( '1.4.0', $extensions['extensions.ext:ezjscore']->value );
        $this->assertSame( 'no version', $extensions['extensions.ext:local_ext']->value );

        $siteaccesses = $this->byKey( ( new expSysCardSiteAccessProvider() )->collect( $this->context( array(
            'ini' => array( 'site.ini' => array( 'SiteAccessSettings' => array( 'MatchOrder' => 'host;uri', 'AvailableSiteAccessList' => array( 'site', 'admin' ) ) ) ),
            'siteaccess:site' => array( 'design' => 'media', 'locale' => 'eng-US', 'url' => 'https://user:pw@www.example.org/' ),
            'siteaccess:admin' => array( 'design' => 'admin4', 'locale' => 'eng-US', 'url' => 'edit.example.org' ) ), 'info' ) ) );
        $this->assertSame( 'host, uri', $siteaccesses['siteaccess.match']->value );
        $this->assertSame( 'design media, eng-US, www.example.org', $siteaccesses['siteaccess.sa:site']->value );
        $this->assertSame( 'design admin4, eng-US, edit.example.org', $siteaccesses['siteaccess.sa:admin']->value );

        $settings = ( new expSysCardSettingsProvider() )->collect( $this->context( array( 'ini' => array(
            'syscard.ini' => array( 'Inventory' => array( 'Settings' => array( 'site.ini/SiteSettings/SiteName', 'site.ini/DatabaseSettings/Password',
                                                                               'site.ini/DatabaseSettings/User', 'site.ini/MailSettings/TransportPassword',
                                                                               'site.ini/RegionalSettings/SiteLanguageList', 'broken' ) ) ),
            'site.ini' => array( 'SiteSettings' => array( 'SiteName' => 'Example' ), 'DatabaseSettings' => array( 'Password' => 'dbpw', 'User' => 'dbuser' ),
                                 'MailSettings' => array( 'TransportPassword' => 'mailpw' ), 'RegionalSettings' => array( 'SiteLanguageList' => array( 'eng-US', 'ger-DE' ) ) ) ) ), 'info' ) );
        $map = $this->byKey( $settings );
        $this->assertSame( 'Example', $map['settings.ini:site.ini/SiteSettings/SiteName']->value );
        $this->assertSame( 'eng-US, ger-DE', $map['settings.ini:site.ini/RegionalSettings/SiteLanguageList']->value );
        $this->assertCount( 2, $settings, 'passwords and users are never shown, even when listed' );

        $queue = $this->byKey( ( new expSysCardQueueProvider() )->collect( $this->context( array( 'queue.summary' => array( 'unit' => 'expqueue.service', 'installed' => true, 'workers' => 0, 'last_action' => 'start', 'at' => self::NOW - 60 ) ), 'info' ) ) );
        $this->assertSame( 'expqueue.service, installed', $queue['queue.service']->value );
        $this->assertSame( expSysCardFact::WARN, $queue['queue.workers']->severity );
        $this->assertSame( 'start, 60 s ago', $queue['queue.last']->value );
        $noQueue = $this->byKey( ( new expSysCardQueueProvider() )->collect( $this->context( array(), 'info' ) ) );
        $this->assertSame( 'expqueue not active', $noQueue['queue.state']->value );
    }

    public function testAboutText()
    {
        $text = "# a comment\n@ about | What it is | from README.md\nLine one\nline two.\n\n@ links | Links\n- Website: https://example.org\n- GitHub: https://github.com/x/y\n";
        $blocks = expSysCardAboutProvider::parse( $text );
        $this->assertCount( 2, $blocks );
        $this->assertSame( 'What it is', $blocks[0]['heading'] );
        $this->assertSame( 'README.md', $blocks[0]['source'] );
        $this->assertSame( array( 'Line one', 'line two.' ), $blocks[0]['lines'] );
        $this->assertSame( array( array( 'Website', 'https://example.org' ), array( 'GitHub', 'https://github.com/x/y' ) ), $blocks[1]['rows'] );

        $facts = ( new expSysCardAboutProvider() )->collect( $this->context( array( 'files' => array( self::ROOT . '/share/syscard/about.txt' => $text ) ), 'about' ) );
        $this->assertSame( expSysCardFact::KIND_TEXT, $facts[0]->kind );
        $this->assertSame( 'Line one line two.', $facts[0]->value );
        $this->assertSame( 'links', $facts[1]->section );
        $this->assertSame( 'https://example.org', $facts[1]->value );

        // the shipped text is parsed into the sections exp:about promises
        $shipped = (string)file_get_contents( __DIR__ . '/../../../../../share/syscard/about.txt' );
        $sections = array_unique( array_map( function ( $b ) { return $b['section']; }, expSysCardAboutProvider::parse( $shipped ) ) );
        $this->assertSame( array( 'about', 'project', 'links', 'contribute', 'credits' ), array_values( $sections ) );
        $this->assertStringContainsString( 'GNU General Public License v2.0 (or any later version)', $shipped );
        $this->assertStringContainsString( 'Become an Exponential Partner', $shipped );
        $this->assertStringContainsString( 'Velocity', $shipped );
    }
}
