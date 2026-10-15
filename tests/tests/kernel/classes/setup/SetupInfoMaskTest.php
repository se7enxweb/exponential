<?php
/**
 * What Setup > System information and Setup > Site data and Sessions show of secrets. Pure helpers of the views;
 * no database.
 *
 *  IM-01 - setup/info/php (HTML): the web server's request headers and environment are left out, the value of a
 *          password directive is hidden, a password inside a value is masked, the rest stays
 *  IM-02 - setup/info/php (text of the command line): the same for the text form
 *  IM-03 - the view buffers phpinfo() and prints it through maskPhpInfo()
 *  SD-01 - setup/sitedata: a value whose name is a secret is only "set"; a password inside another value is masked
 *  SS-01 - setup/session: a row given to the template carries no session key
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Setup\Info;
use Exponential\View\Kernel\Setup\Session;
use Exponential\View\Kernel\Setup\Sitedata;

class SetupInfoMaskTest extends PHPUnit\Framework\TestCase
{
    private static function htmlInfo()
    {
        return '<!DOCTYPE html><html><head><style type="text/css">body {background-color: #fff;}</style></head><body><div class="center">'
            . '<table><tr class="h"><td><h1 class="p">PHP Version 8.5.4</h1></td></tr></table>'
            . '<h2>Configuration</h2>'
            . '<h2><a name="module_apache2handler">apache2handler</a></h2>'
            . '<table><tr><td class="e">Server Root </td><td class="v">/etc/apache2 </td></tr></table>'
            . '<h2>Apache Environment</h2>'
            . '<table><tr class="h"><th>Variable</th><th>Value</th></tr><tr><td class="e">DB_PASSWORD </td><td class="v">envsecret1 </td></tr></table>'
            . '<h2>HTTP Headers Information</h2>'
            . '<table><tr class="h"><th colspan="2">HTTP Request Headers</th></tr>'
            . '<tr><td class="e">Cookie </td><td class="v">eZSESSID=cookiesecret2 </td></tr>'
            . '<tr><td class="e">Authorization </td><td class="v">Basic YWRtaW46c2VjcmV0 </td></tr></table>'
            . '<h2><a name="module_mysqli">mysqli</a></h2>'
            . '<table><tr class="h"><th>Directive</th><th>Local Value</th><th>Master Value</th></tr>'
            . '<tr><td class="e">mysqli.default_pw</td><td class="v">dbsecret3</td><td class="v">dbsecret3</td></tr>'
            . '<tr><td class="e">mysqli.default_port</td><td class="v">3306</td><td class="v">3306</td></tr></table>'
            . '<h2><a name="module_session">session</a></h2>'
            . '<table><tr><td class="e">session.save_path</td><td class="v">tcp://cache:redissecret4@redis.example.invalid:6379</td><td class="v">no value</td></tr></table>'
            . '<h2>PHP License</h2><table><tr class="v"><td><p>This program is free software</p></td></tr></table>'
            . '</div></body></html>';
    }

    /** IM-01 */
    public function testTheHtmlFormLeavesOutTheRequestAndMasksSecrets()
    {
        $out = Info::maskPhpInfo( self::htmlInfo(), true );
        foreach ( array( 'envsecret1', 'cookiesecret2', 'YWRtaW46c2VjcmV0', 'dbsecret3', 'redissecret4', 'Apache Environment', 'HTTP Headers Information' ) as $secret )
            $this->assertStringNotContainsString( $secret, $out, $secret );
        foreach ( array( 'PHP Version 8.5.4', 'apache2handler', '/etc/apache2', 'mysqli.default_port', '3306', 'redis.example.invalid:6379',
                         'This program is free software', 'body {background-color: #fff;}', '</div></body></html>' ) as $kept )
            $this->assertStringContainsString( $kept, $out, $kept );
        $this->assertStringContainsString( '<td class="e">mysqli.default_pw</td><td class="v">***</td><td class="v">***</td>', $out );
    }

    /** IM-02 */
    public function testTheTextFormIsMaskedToo()
    {
        $text = "phpinfo()\nPHP Version => 8.5.4\n\nmysqli\n\nDirective => Local Value => Master Value\nmysqli.default_pw => dbsecret3 => dbsecret3\n"
              . "mysqli.default_port => 3306 => 3306\n\nsession\n\nsession.save_path => tcp://cache:redissecret4@redis.example.invalid => no value\n\n"
              . "Environment\n\nVariable => Value\nDB_PASSWORD => envsecret1\nHOME => /root\n\nPHP License\nThis program is free software\n";
        $out = Info::maskPhpInfo( $text, false );
        foreach ( array( 'dbsecret3', 'redissecret4', 'envsecret1', 'HOME => /root' ) as $secret )
            $this->assertStringNotContainsString( $secret, $out, $secret );
        $this->assertStringContainsString( 'mysqli.default_pw => *** => ***', $out );
        $this->assertStringContainsString( 'mysqli.default_port => 3306 => 3306', $out );
        $this->assertStringContainsString( 'This program is free software', $out );
    }

    /** IM-01, text that is not valid UTF-8 is kept, with the bad bytes replaced */
    public function testInvalidUtf8IsNotDropped()
    {
        $out = Info::maskPhpInfo( '<table><tr><td class="e">open_basedir</td><td class="v">/srv/a' . "\xff" . 'b</td></tr></table>', true );
        $this->assertStringContainsString( "/srv/a\u{FFFD}b", $out );
    }

    /** IM-01, the names phpinfo() and extension packages treat as secret */
    public function testSecretSettingNames()
    {
        foreach ( array( 'mysqli.default_pw', 'db_pass', 'smtp.passwd', 'ProxyAuth', 'http_auth', 'Bearer', 'Authorization', 'ApiKey', 'MailPassword' ) as $name )
            $this->assertTrue( expSystemReportMask::isSecretSettingName( $name ), $name );
        foreach ( array( 'Bypass', 'Compass', 'OAuthServer', 'mysqli.default_port', 'AuthenticationMethod', 'session.name' ) as $name )
            $this->assertFalse( expSystemReportMask::isSecretSettingName( $name ), $name );
        $this->assertTrue( Info::isPhpInfoSecretName( 'db_pass' ) );
    }

    /** IM-03 */
    public function testTheViewPrintsTheMaskedOutput()
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/private/classes/views/setup/info.php' );
        $this->assertMatchesRegularExpression( '/ob_start\(\);\s*phpinfo\( [^;]+\);\s*echo self::maskPhpInfo\( \(string\)ob_get_clean\(\)/', $source );
    }

    /** SD-01 */
    public function testSiteDataHidesSecrets()
    {
        $this->assertSame( '(set, hidden)', Sitedata::shownValue( 'api_token', 'abc123' ) );
        $this->assertSame( '(not set)', Sitedata::shownValue( 'smtp-password', '' ) );
        $this->assertSame( 'mysql://user:***@db/x', Sitedata::shownValue( 'mirror', 'mysql://user:pw7@db/x' ) );
        $this->assertSame( '6.0.15', Sitedata::shownValue( 'ezpublish-version', '6.0.15' ) );
    }

    /** SS-01 */
    public function testASessionRowCarriesNoKey()
    {
        $key = 'abcdef0123456789abcdef0123';
        $row = Session::describeRow( array( 'user_id' => 14, 'expiration_time' => 1800000000, 'session_key' => $key, 'count' => 1 ), 1800000000, 259200 );
        $this->assertArrayNotHasKey( 'session_key', $row );
        foreach ( $row as $field => $value )
            $this->assertStringNotContainsString( $key, is_scalar( $value ) ? (string)$value : json_encode( $value ), $field );
        $this->assertSame( Session::sessionRef( $key ), $row['ref'] );

        $tpl = file_get_contents( dirname( __DIR__, 5 ) . '/design/standard/templates/setup/session.tpl' );
        $this->assertStringNotContainsString( 'session.session_key', $tpl );
        $this->assertStringNotContainsString( 'SessionKeyArray', $tpl, 'the select-all script ticks the references' );
    }
}
