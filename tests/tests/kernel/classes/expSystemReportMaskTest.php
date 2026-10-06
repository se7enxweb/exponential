<?php
/**
 * The masking rules of the system report (expSystemReportMask): what Setup > System information, its text report and
 * exp:system:info may show of a value. No database.
 *
 *  SM-01 - Values of secret names are never shown, only whether they are set
 *  SM-02 - Names that are not secret are not taken for secrets
 *  SM-03 - Passwords in addresses are cut out (by the shared expSecretRule), user and host stay
 *  SM-04 - "password=..." style pairs in free text lose their value
 *  SM-05 - Long runs of letters and digits (session ids, tokens) are replaced; names and versions stay
 *  SM-06 - Paths inside the installation become relative, web and home paths outside keep two parts, system paths stay
 *  SM-07 - A whole report: nested secrets hidden, strings masked, numbers and booleans kept
 *  SM-08 - The names agree with exp:ini, the audit log and the settings pages (expSecretRule); request names are added
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSystemReportMaskTest extends PHPUnit\Framework\TestCase
{
    const ROOT = '/var/www/vhosts/example.org/doc/site';

    /** SM-01 */
    public function testSecretNamesAreHidden()
    {
        foreach ( array( 'Password', 'TransportPassword', 'db_password', 'passwd', 'Secret', 'client_secret', 'token', 'ApiKey',
                         'api-key', 'PrivateKey', 'salt', 'credentials', 'session_id', 'SessionID', 'cookie', 'Authorization',
                         'dsn', 'key', 'Signature' ) as $name )
            $this->assertTrue( expSystemReportMask::isSecretName( $name ), $name );
        $this->assertSame( '(set, hidden)', expSystemReportMask::secret( 'hunter2' ) );
        $this->assertSame( '(not set)', expSystemReportMask::secret( '' ) );
        $this->assertSame( '(not set)', expSystemReportMask::secret( null ) );
    }

    /** SM-02 */
    public function testOrdinaryNamesAreNotSecret()
    {
        foreach ( array( 'version', 'engine', 'charset', 'tables', 'memory_limit', 'transport', 'server', 'port', 'workers',
                         'extensions', 'login', 'timezone', 'validate_timestamps', 'file_update_protection', 'cache_full' ) as $name )
            $this->assertFalse( expSystemReportMask::isSecretName( $name ), $name );
    }

    /** SM-03 */
    public function testCredentialsInAddressesAreCut()
    {
        $this->assertSame( 'mysql://site:***@db.example.org:3306/site', expSystemReportMask::dsn( 'mysql://site:s3cr3t@db.example.org:3306/site' ) );
        $this->assertSame( 'see https://admin:***@example.org/x', expSystemReportMask::dsn( 'see https://admin:pw@example.org/x' ) );
        $this->assertSame( 'redis://:***@cache:6379', expSystemReportMask::dsn( 'redis://:onlypassword@cache:6379' ) );
        // no credentials: unchanged, ports kept
        $this->assertSame( 'https://alpha.example.org:8080/admin/', expSystemReportMask::dsn( 'https://alpha.example.org:8080/admin/' ) );
        $this->assertStringNotContainsString( 's3cr3t', expSystemReportMask::text( 'pgsql://u:s3cr3t@localhost/db' ) );
    }

    /** SM-04 */
    public function testPairsLoseTheirValue()
    {
        $this->assertSame( 'Password=***', expSystemReportMask::text( 'Password=hunter2' ) );
        $this->assertSame( 'db_password: ***', expSystemReportMask::text( 'db_password: hunter2' ) );
        $this->assertSame( '?a=1&token=***&b=2', expSystemReportMask::text( '?a=1&token=abc123&b=2' ) );
        $this->assertSame( "'secret' => ***", expSystemReportMask::text( "'secret' => 'xyz'" ) );
        // a setting that only sounds alike stays
        $this->assertSame( 'opcache.file_update_protection=0', expSystemReportMask::text( 'opcache.file_update_protection=0' ) );
        $this->assertSame( 'pm = ondemand', expSystemReportMask::text( 'pm = ondemand' ) );
    }

    /** SM-05 */
    public function testLongTokensAreReplaced()
    {
        $this->assertSame( 'id ***', expSystemReportMask::text( 'id 3f9a1c0b7e2d4a6f8b1c3d5e7f9a0b2c' ) );
        $this->assertSame( 'eZSESSID=***', expSystemReportMask::text( 'eZSESSID=k2j3h4g5f6d7s8a9q0w1e2r3t4y5' ) );
        foreach ( array( 'explayouts_content_browser_core', 'exp_enhanced_link', '6.0.15stable-45f5fbb-dirty', 'v0.0.4.42+da360b4-dirty',
                         'AMD EPYC Processor (with IBPB)', 'ezpaypal 1.2.3' ) as $plain )
            $this->assertSame( $plain, expSystemReportMask::text( $plain ) );
    }

    /** SM-06 */
    public function testPaths()
    {
        $this->assertSame( '.', expSystemReportMask::path( self::ROOT, self::ROOT ) );
        $this->assertSame( 'var/site/cache', expSystemReportMask::path( self::ROOT . '/var/site/cache', self::ROOT ) );
        $this->assertSame( '…/other/site', expSystemReportMask::path( '/var/www/vhosts/other/site', self::ROOT ) );
        $this->assertSame( '…/alice/x', expSystemReportMask::path( '/home/alice/x', self::ROOT ) );
        $this->assertSame( '/etc/vc/vc.conf', expSystemReportMask::path( '/etc/vc/vc.conf', self::ROOT ) );
        $this->assertSame( '/usr/bin/php', expSystemReportMask::path( '/usr/bin/php', self::ROOT ) );
        $this->assertSame( 'the archive dist/engine.phar is in .', expSystemReportMask::paths( 'the archive ' . self::ROOT . '/dist/engine.phar is in ' . self::ROOT, self::ROOT ) );
        $this->assertSame( 'log in …/www/x.log', expSystemReportMask::paths( 'log in /var/www/www/x.log', '' ) );
    }

    /** SM-07 */
    public function testWholeReport()
    {
        $masked = expSystemReportMask::report( array(
            'database' => array( 'user' => 'site', 'password' => 'hunter2', 'file' => self::ROOT . '/var/db.sqlite', 'tables' => 136, 'connected' => true ),
            'mail' => array( 'TransportPassword' => '', 'server' => 'smtp://u:p@mail.example.org' ),
            'list' => array( 'a', 'Password=x' ),
        ), self::ROOT );
        $this->assertSame( '(set, hidden)', $masked['database']['password'] );
        $this->assertSame( 'var/db.sqlite', $masked['database']['file'] );
        $this->assertSame( 136, $masked['database']['tables'] );
        $this->assertTrue( $masked['database']['connected'] );
        $this->assertSame( '(not set)', $masked['mail']['TransportPassword'] );
        $this->assertSame( 'smtp://u:***@mail.example.org', $masked['mail']['server'] );
        $this->assertSame( array( 'a', 'Password=***' ), $masked['list'] );
        $this->assertStringNotContainsString( 'hunter2', json_encode( $masked ) );
    }

    /** SM-08 */
    public function testAgreesWithTheSharedRule()
    {
        $settings = new expSettingsSecretRule();
        foreach ( array( 'Password', 'TransportPassword', 'DbPwd', 'DatabaseDsn', 'ApiKey', 'license_key', 'Key', 'Salt', 'AccessToken',
                         'SortKey', 'KeyField', 'SiteName', 'SessionTimeout', 'AuthorizationURL', 'CookieTimeout', 'Server' ) as $name )
        {
            $shared = expSecretRule::isSecretName( $name );
            $this->assertSame( $shared, expIniEditor::isSecret( $name ), 'exp:ini ' . $name );
            $this->assertSame( $shared, $settings->isSecretName( $name ), 'settings ' . $name );
            $this->assertSame( $shared, expSystemReportMask::isSecretName( $name ), 'report ' . $name );
        }
        // the report adds the secrets of a request, as whole names only
        foreach ( array( 'session_id', 'eZSESSID', 'cookie', 'Authorization', 'Signature' ) as $name )
        {
            $this->assertFalse( expSecretRule::isSecretName( $name ), $name );
            $this->assertTrue( expSystemReportMask::isSecretName( $name ), $name );
        }
        // the same value is masked in the same places, each with its own mask text
        $value = 'mysql://site:s3cr3t@db/x?token=abc&b=2';
        $this->assertSame( str_replace( '•', '*', expSettingsSecretRule::maskInline( $value ) ), str_replace( '***', '********', expSystemReportMask::dsn( $value ) ) );
        $this->assertSame( 'mysql://site:***@db/x?token=***&b=2', expSystemReportMask::text( $value ) );
    }
}
