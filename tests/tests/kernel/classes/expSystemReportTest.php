<?php
/**
 * The system report of Setup > System information and exp:system:info (expSystemReport), from facts built by hand:
 * no database, no web server.
 *
 *  SR-01 - The kind of server follows the SAPI, SERVER_SOFTWARE and whether Velocity runs the process
 *  SR-02 - php.ini sizes are read in bytes; sizes are shown in units
 *  SR-03 - A healthy installation has no warnings and no failures
 *  SR-04 - PHP below the minimum fails; a branch without security support warns
 *  SR-05 - Missing extensions: required (the database driver too) fail, recommended warn
 *  SR-06 - OPcache: off warns; on with hidden statistics is ok and says why; under Velocity file_update_protection warns
 *  SR-07 - A var directory that is not writable fails; little free disk fails or warns
 *  SR-08 - Debug for everyone fails, by IP is a note; development settings and display_errors warn
 *  SR-09 - Caches off, a placeholder SiteURL, old or missing cronjobs and an SMTP without server are reported
 *  SR-10 - Checks are sorted fail, warn, note, ok; the summary counts them
 *  SR-11 - Cards name the server that answered and its worker model; Apache and Velocity differ
 *  SR-12 - The report, its JSON and its text carry no secret and no installation path
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSystemReportTest extends PHPUnit\Framework\TestCase
{
    const ROOT = '/var/www/vhosts/example.org/doc/site';
    const NOW = 1791273600; // 2026-10-06 08:00 UTC

    /**
     * The facts of a healthy Apache + PHP-FPM installation; $changes are merged per part.
     */
    private function facts( array $changes = array() )
    {
        $facts = array(
            'root' => self::ROOT,
            'time' => self::NOW,
            'exponential' => array( 'name' => 'Exponential', 'version' => '6.0.15', 'state' => 'stable', 'alias' => '6.0', 'schema' => '6.0.15', 'build' => '' ),
            'site' => array( 'siteaccess' => 'admin', 'public_siteaccess' => 'site', 'site_url' => 'www.example.org', 'site_url_placeholder' => false ),
            'server' => array( 'kind' => 'apache-fpm', 'sapi' => 'fpm-fcgi', 'software' => 'Apache', 'name' => 'Apache', 'version' => '', 'tls' => true, 'port' => 443,
                               'workers' => array( 'model' => 'process-per-request', 'pool' => 'example.org', 'manager' => 'ondemand', 'active' => 1, 'idle' => 2, 'total' => 3,
                                                   'max_children_reached' => 0, 'accepted' => 10, 'since' => self::NOW - 100 ) ),
            'php' => array( 'version' => '8.5.11', 'minimum' => '8.0.0', 'sapi' => 'fpm-fcgi', 'zts' => false, 'os' => 'Linux', 'uname' => 'Linux 5.14 x86_64',
                            'memory_limit' => '512M', 'max_execution_time' => 120, 'upload_max_filesize' => '64M', 'post_max_size' => '64M', 'max_file_uploads' => 20,
                            'max_input_vars' => 5000, 'file_uploads' => true, 'display_errors' => false, 'open_basedir' => false, 'date_timezone' => 'Europe/Berlin',
                            'disabled_functions' => array(),
                            'extensions' => array( 'Core', 'ctype', 'curl', 'dom', 'fileinfo', 'gd', 'iconv', 'intl', 'json', 'libxml', 'mbstring', 'mysqli', 'pcre',
                                                   'session', 'SimpleXML', 'SPL', 'sqlite3', 'xml', 'xsl', 'zlib', 'Zend OPcache', 'apcu' ) ),
            'opcache' => array( 'loaded' => true, 'enabled' => true, 'status' => true, 'status_note' => '', 'cli' => false, 'enable_cli' => false, 'memory' => 268435456,
                                'validate_timestamps' => true, 'revalidate_freq' => 2, 'file_update_protection' => 2, 'jit' => 'tracing', 'jit_buffer' => 67108864,
                                'jit_on' => true, 'hits' => 100000, 'misses' => 1000, 'hit_rate' => 99.0, 'scripts' => 2000, 'max_keys' => 16229,
                                'used' => 104857600, 'free' => 157286400, 'wasted' => 6291456, 'restarts' => 0, 'cache_full' => false ),
            'apcu' => array( 'loaded' => true, 'enabled' => true, 'version' => '5.1.28', 'entries' => 10, 'hit_rate' => 90.0, 'size' => 33554432, 'free' => 30000000 ),
            'database' => array( 'implementation' => 'ezmysqli', 'server' => 'db.internal', 'port' => '3306', 'socket' => '', 'name' => 'site', 'user' => 'siteuser',
                                 'connected' => true, 'engine' => 'mysql', 'class' => 'eZMySQLiDB', 'version' => '10.11.6', 'charset' => 'utf8mb4',
                                 'tables' => 160, 'size' => null, 'file' => '', 'slave' => false ),
            'storage' => array( 'var' => self::ROOT . '/var/site', 'var_writable' => true, 'cache' => self::ROOT . '/var/site/cache', 'storage' => self::ROOT . '/var/site/storage',
                                'cache_writable' => true, 'storage_writable' => true, 'log_writable' => true, 'disk_free' => 400 * 1073741824,
                                'disk_total' => 2000 * 1073741824, 'var_size' => null, 'var_counted_all' => true ),
            'caches' => array( 'view' => true, 'template_compile' => true, 'template_cache' => true, 'override' => true, 'static' => false, 'http' => true,
                               'query' => 'shared', 'response' => null, 'dirs' => array( 'content' => null, 'template' => null ) ),
            'cronjobs' => array( 'last' => self::NOW - 3600, 'source' => 'log', 'part' => 'frequent' ),
            'mail' => array( 'transport' => 'smtp', 'server' => 'mail.example.org', 'port' => '587', 'encryption' => 'tls', 'login' => true, 'sender_set' => true ),
            'locale' => array( 'locale' => 'ger-DE', 'content_locale' => 'ger-DE', 'languages' => array( 'ger-DE', 'eng-GB' ), 'timezone_setting' => '',
                               'timezone' => 'Europe/Berlin', 'php_timezone' => 'Europe/Berlin', 'now' => '2026-10-06 10:00:00 CEST' ),
            'debug' => array( 'output' => false, 'by_ip' => false, 'by_user' => false, 'template' => false, 'used_templates' => false, 'sql' => false, 'display_errors' => false ),
            'extensions' => array( array( 'extension' => 'ezjscore', 'name' => 'ezjscore', 'version' => '1.5.4' ), array( 'extension' => 'local_thing', 'name' => 'local_thing', 'version' => '' ) ),
            'host' => array( 'cpu' => 'AMD EPYC', 'cpus' => 12, 'memory' => 50000000000, 'load' => array( 0.5, 0.4, 0.3 ) ),
        );
        foreach ( $changes as $part => $values )
            $facts[$part] = is_array( $values ) && isset( $facts[$part] ) && is_array( $facts[$part] ) && array_keys( $values ) !== range( 0, count( $values ) - 1 )
                          ? array_replace( $facts[$part], $values ) : $values;
        return $facts;
    }

    private function report( array $changes = array() )
    {
        return new expSystemReport( $this->facts( $changes ) );
    }

    private function check( expSystemReport $report, $id )
    {
        foreach ( $report->checks() as $check )
            if ( $check['id'] === $id )
                return $check;
        return null;
    }

    private function velocity()
    {
        return array(
            'server' => array( 'kind' => 'velocity', 'sapi' => 'cli', 'software' => 'QbixServer/1.5.0', 'name' => 'Exponential Velocity', 'version' => 'v0.0.4.42',
                               'port' => 8080, 'workers' => array( 'model' => 'persistent', 'configured' => 148, 'spare' => 12, 'archive' => true, 'pid' => 4711 ) ),
            'php' => array( 'sapi' => 'cli', 'max_execution_time' => 0 ),
            'opcache' => array( 'cli' => true, 'enable_cli' => true, 'hit_rate' => 10.3, 'file_update_protection' => 0 ),
            'caches' => array( 'response' => true ),
        );
    }

    /** SR-01 */
    public function testServerKind()
    {
        $this->assertSame( 'velocity', expSystemReport::serverKind( 'cli', 'QbixServer/1.5.0', true ) );
        $this->assertSame( 'apache-fpm', expSystemReport::serverKind( 'fpm-fcgi', 'Apache', false ) );
        $this->assertSame( 'nginx-fpm', expSystemReport::serverKind( 'fpm-fcgi', 'nginx/1.26.0', false ) );
        $this->assertSame( 'fpm', expSystemReport::serverKind( 'fpm-fcgi', '', false ) );
        $this->assertSame( 'apache-mod_php', expSystemReport::serverKind( 'apache2handler', 'Apache/2.4.62', false ) );
        $this->assertSame( 'frankenphp', expSystemReport::serverKind( 'frankenphp', 'FrankenPHP', false ) );
        $this->assertSame( 'cli-server', expSystemReport::serverKind( 'cli-server', 'PHP 8.5 Development Server', false ) );
        $this->assertSame( 'cli', expSystemReport::serverKind( 'cli', '', false ) );
        $this->assertSame( 'other', expSystemReport::serverKind( 'litespeed', 'LiteSpeed', false ) );
    }

    /** SR-02 */
    public function testSizes()
    {
        $this->assertSame( 134217728, expSystemReport::bytes( '128M' ) );
        $this->assertSame( 1073741824, expSystemReport::bytes( '1G' ) );
        $this->assertSame( 2048, expSystemReport::bytes( '2k' ) );
        $this->assertSame( 512, expSystemReport::bytes( '512' ) );
        $this->assertSame( 268435456, expSystemReport::bytes( '256', 1048576 ) );
        $this->assertSame( -1, expSystemReport::bytes( '-1' ) );
        $this->assertSame( 0, expSystemReport::bytes( '' ) );
        $this->assertSame( '1.5 GB', expSystemReport::size( 1610612736 ) );
        $this->assertSame( '512 B', expSystemReport::size( 512 ) );
        $this->assertSame( '385 MB', expSystemReport::size( 403701760 ) );
        $this->assertSame( 'unlimited', expSystemReport::size( -1 ) );
        $this->assertSame( '', expSystemReport::size( null ) );
    }

    /** SR-03 */
    public function testHealthyInstallation()
    {
        $summary = $this->report()->summary();
        $this->assertSame( 0, $summary['fail'], json_encode( $this->report()->checks() ) );
        $this->assertSame( 0, $summary['warn'], json_encode( $this->report()->checks() ) );
        $this->assertSame( 'ok', $summary['worst'] );
    }

    /** SR-04 */
    public function testPhpVersion()
    {
        $this->assertSame( 'fail', $this->check( $this->report( array( 'php' => array( 'version' => '7.4.33' ) ) ), 'php_version' )['state'] );
        $this->assertSame( 'warn', $this->check( $this->report( array( 'php' => array( 'version' => '8.1.30' ) ) ), 'php_version' )['state'] );
        $this->assertSame( 'ok', $this->check( $this->report( array( 'php' => array( 'version' => '8.2.20' ) ) ), 'php_version' )['state'] );
        $this->assertSame( 'ok', $this->check( $this->report( array( 'php' => array( 'version' => '8.9.0' ) ) ), 'php_version' )['state'] );
    }

    /** SR-05 */
    public function testExtensions()
    {
        $facts = $this->facts();
        $without = function ( array $names ) use ( $facts ) { return array_values( array_diff( $facts['php']['extensions'], $names ) ); };
        $check = $this->check( $this->report( array( 'php' => array( 'extensions' => $without( array( 'dom' ) ) ) ) ), 'php_extensions' );
        $this->assertSame( 'fail', $check['state'] );
        $this->assertStringContainsString( 'dom', $check['title'] );
        $check = $this->check( $this->report( array( 'php' => array( 'extensions' => $without( array( 'mysqli' ) ) ) ) ), 'php_extensions' );
        $this->assertSame( 'fail', $check['state'] );
        $this->assertStringContainsString( 'mysqli', $check['title'] );
        $check = $this->check( $this->report( array( 'php' => array( 'extensions' => $without( array( 'intl', 'gd' ) ) ) ) ), 'php_extensions_recommended' );
        $this->assertSame( 'warn', $check['state'] );
        $this->assertStringContainsString( 'intl', $check['title'] );
        $this->assertStringContainsString( 'gd / imagick', $check['title'] );
    }

    /** SR-06 */
    public function testOpcache()
    {
        $this->assertSame( 'warn', $this->check( $this->report( array( 'opcache' => array( 'enabled' => false, 'status' => false ) ) ), 'opcache' )['state'] );
        $check = $this->check( $this->report( array( 'opcache' => array( 'status' => false, 'status_note' => 'disabled_function' ) ) ), 'opcache' );
        $this->assertSame( 'ok', $check['state'] );
        $this->assertStringContainsString( 'disable_functions', $check['detail'] );
        $this->assertSame( 'warn', $this->check( $this->report( array( 'opcache' => array( 'hit_rate' => 60.0 ) ) ), 'opcache' )['state'] );
        $this->assertSame( 'warn', $this->check( $this->report( array( 'opcache' => array( 'cache_full' => true ) ) ), 'opcache' )['state'] );
        // Velocity: a low hit rate of a young worker is normal, file_update_protection is not
        $velocity = $this->velocity();
        $this->assertSame( 'ok', $this->check( $this->report( $velocity ), 'opcache' )['state'] );
        $this->assertNull( $this->check( $this->report( $velocity ), 'opcache_file_update_protection' ) );
        $velocity['opcache']['file_update_protection'] = 2;
        $check = $this->check( $this->report( $velocity ), 'opcache_file_update_protection' );
        $this->assertSame( 'warn', $check['state'] );
        $this->assertStringContainsString( 'velocity.ini', $check['fix'] );
        // a command line without OPcache is no problem
        $this->assertNull( $this->check( $this->report( array( 'server' => array( 'kind' => 'cli' ), 'opcache' => array( 'enabled' => false ) ) ), 'opcache' ) );
    }

    /** SR-07 */
    public function testStorage()
    {
        $this->assertSame( 'fail', $this->check( $this->report( array( 'storage' => array( 'var_writable' => false ) ) ), 'var_writable' )['state'] );
        $this->assertSame( 'fail', $this->check( $this->report( array( 'storage' => array( 'cache_writable' => false ) ) ), 'var_writable' )['state'] );
        $this->assertSame( 'fail', $this->check( $this->report( array( 'storage' => array( 'disk_free' => 536870912 ) ) ), 'disk_free' )['state'] );
        $this->assertSame( 'warn', $this->check( $this->report( array( 'storage' => array( 'disk_free' => 150 * 1073741824 ) ) ), 'disk_free' )['state'] );
        $this->assertSame( 'ok', $this->check( $this->report(), 'disk_free' )['state'] );
    }

    /** SR-08 */
    public function testDebug()
    {
        $this->assertSame( 'fail', $this->check( $this->report( array( 'debug' => array( 'output' => true ) ) ), 'debug_output' )['state'] );
        $this->assertSame( 'info', $this->check( $this->report( array( 'debug' => array( 'output' => true, 'by_ip' => true ) ) ), 'debug_output' )['state'] );
        $check = $this->check( $this->report( array( 'debug' => array( 'template' => true, 'sql' => true ) ) ), 'debug_settings' );
        $this->assertSame( 'warn', $check['state'] );
        $this->assertStringContainsString( 'SQLOutput', $check['title'] );
        $this->assertSame( 'warn', $this->check( $this->report( array( 'php' => array( 'display_errors' => true ) ) ), 'display_errors' )['state'] );
    }

    /** SR-09 */
    public function testCachesSiteCronMail()
    {
        $check = $this->check( $this->report( array( 'caches' => array( 'template_cache' => false ) ) ), 'caches' );
        $this->assertSame( 'warn', $check['state'] );
        $this->assertStringContainsString( 'TemplateCache', $check['title'] );
        $this->assertSame( 'warn', $this->check( $this->report( array( 'site' => array( 'site_url_placeholder' => true ) ) ), 'site_url' )['state'] );
        $this->assertSame( 'warn', $this->check( $this->report( array( 'cronjobs' => array( 'last' => null ) ) ), 'cronjobs' )['state'] );
        $check = $this->check( $this->report( array( 'cronjobs' => array( 'last' => self::NOW - 5 * 86400 ) ) ), 'cronjobs' );
        $this->assertSame( 'warn', $check['state'] );
        $this->assertStringContainsString( '5 days ago', $check['title'] );
        $this->assertSame( 'fail', $this->check( $this->report( array( 'mail' => array( 'server' => '' ) ) ), 'mail' )['state'] );
        $this->assertSame( 'info', $this->check( $this->report( array( 'mail' => array( 'transport' => 'file' ) ) ), 'mail' )['state'] );
        $this->assertSame( 'fail', $this->check( $this->report( array( 'database' => array( 'connected' => false ) ) ), 'database' )['state'] );
        $this->assertSame( 'warn', $this->check( $this->report( array( 'database' => array( 'charset' => 'latin1' ) ) ), 'database_charset' )['state'] );
    }

    /** SR-10 */
    public function testOrderAndSummary()
    {
        $report = $this->report( array( 'debug' => array( 'output' => true, 'by_ip' => true ), 'storage' => array( 'var_writable' => false ),
                                        'caches' => array( 'view' => false ) ) );
        $states = array_column( $report->checks(), 'state' );
        $this->assertSame( 'fail', $states[0] );
        $this->assertSame( 'warn', $states[1] );
        $this->assertSame( 'info', $states[2] );
        $this->assertSame( 'ok', end( $states ) );
        $summary = $report->summary();
        $this->assertSame( 1, $summary['fail'] );
        $this->assertSame( 1, $summary['warn'] );
        $this->assertSame( 1, $summary['info'] );
        $this->assertSame( 'fail', $summary['worst'] );
    }

    /** SR-11 */
    public function testCardsNameTheServer()
    {
        $apache = $this->report();
        $this->assertSame( 'Apache + PHP-FPM', $apache->serverLabel() );
        $this->assertStringContainsString( 'clean state for every request', $apache->workerModel() );
        $velocity = $this->report( $this->velocity() );
        $this->assertSame( 'Exponential Velocity', $velocity->serverLabel() );
        $this->assertStringContainsString( 'persistent workers', $velocity->workerModel() );

        $rows = function ( expSystemReport $report, $id ) {
            foreach ( $report->cards() as $card )
                if ( $card['id'] === $id )
                    return array_column( $card['rows'], 'value', 'label' );
            return array();
        };
        $server = $rows( $apache, 'server' );
        $this->assertSame( 'example.org (pm = ondemand)', $server['PHP-FPM pool'] );
        $this->assertArrayNotHasKey( 'Workers', $server );
        $server = $rows( $velocity, 'server' );
        $this->assertSame( 'Exponential Velocity v0.0.4.42', $server['Answered by'] );
        $this->assertSame( '148 configured, 12 spare', $server['Workers'] );
        $this->assertSame( 'from the engine archive', $server['Engine'] );
        $this->assertSame( '0 s', $rows( $velocity, 'opcache' )['file_update_protection'] );
        $this->assertSame( 'no limit', $rows( $velocity, 'php' )['max_execution_time'] );
        $this->assertSame( '120 s', $rows( $apache, 'php' )['max_execution_time'] );
        // hidden statistics say so instead of "not installed"
        $hidden = $rows( $this->report( array( 'opcache' => array( 'status' => false, 'status_note' => 'disabled_function' ) ) ), 'opcache' );
        $this->assertSame( 'on', $hidden['Status'] );
        $this->assertStringContainsString( 'opcache_get_status is disabled', $hidden['Statistics'] );
        $this->assertSame( 'var/site · writable', $rows( $apache, 'storage' )['var directory'] );
    }

    /** SR-12 */
    public function testNoSecretsInTheReport()
    {
        $report = $this->report( array(
            'database' => array( 'user' => 'siteuser', 'server' => 'mysql://siteuser:hunter2@db.internal' ),
            'mail' => array( 'server' => 'smtp://mailer:pw123456@mail.example.org' ),
            'exponential' => array( 'build' => 'session_id=0123456789abcdef0123456789abcdef' ),
        ) );
        $json = json_encode( $report->toArray() );
        $text = $report->toText();
        foreach ( array( 'hunter2', 'pw123456', '0123456789abcdef0123456789abcdef', '"siteuser"', self::ROOT ) as $secret )
        {
            $this->assertStringNotContainsString( $secret, $json, $secret );
            $this->assertStringNotContainsString( $secret, $text, $secret );
        }
        $this->assertStringStartsWith( "Exponential system information\n", $text );
        $this->assertStringContainsString( 'Answered by: Apache + PHP-FPM', $text );
        $this->assertStringContainsString( 'local_thing', $text );
    }
}
