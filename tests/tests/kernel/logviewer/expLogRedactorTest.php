<?php
/**
 * What the log viewer masks (doc/eng-us/features/6.0/log-viewer.md, "Privacy"); pure, synthetic values only.
 *
 *  LV-20  e-mail addresses
 *  LV-21  IPv4 addresses (not versions such as 6.0.15.1), IPv6 addresses (not times such as 01:54:58), the ip field
 *  LV-22  secrets: passwords in addresses, password= / token= / api_key= pairs, Bearer credentials, JSON web tokens,
 *         session ids and cookies, long keys
 *  LV-23  the values of secret settings are masked in every case, unmasked too; short values are ignored; a password
 *         in an address is never shown
 *  LV-24  unmasked: e-mail and IP addresses as written; masking() and fromConfig()
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLogRedactorTest extends PHPUnit\Framework\TestCase
{
    /** LV-20 */
    public function testEmails()
    {
        $r = new expLogRedactor();
        $this->assertSame( 'mail to j***@***.com failed', $r->text( 'mail to jane.doe@example.com failed' ) );
        $this->assertSame( 'a***@***.de, b***@***.org', $r->text( 'a.b+tag@mail.example.de, bob@sub.example.org' ) );
        $this->assertSame( 'no address @ here', $r->text( 'no address @ here' ) );
    }

    /** LV-21 */
    public function testIPs()
    {
        $r = new expLogRedactor();
        $this->assertSame( 'from 192.168.x.x now', $r->text( 'from 192.168.10.20 now' ) );
        $this->assertSame( '[203.0.x.x]', $r->text( '[203.0.113.7]' ) );
        $this->assertSame( 'release v6.0.15.1 and 1.2.3.4.5', $r->text( 'release v6.0.15.1 and 1.2.3.4.5' ) );
        $this->assertSame( 'not an address 300.1.1.1', $r->text( 'not an address 300.1.1.1' ) );
        $this->assertSame( 'at 2001:db8:x:x end', $r->text( 'at 2001:db8:85a3::8a2e:370:7334 end' ) );
        $this->assertSame( 'Oct 17 2026 01:54:58', $r->text( 'Oct 17 2026 01:54:58' ) );
        $this->assertSame( 'loopback ::x', $r->text( 'loopback ::1' ) );
        $this->assertSame( '198.51.x.x', $r->ip( '198.51.100.7' ) );
        $this->assertSame( 'web01', $r->ip( 'web01' ) );
    }

    /** LV-22 */
    public function testSecrets()
    {
        $r = new expLogRedactor();
        $out = $r->text( 'connect mysql://user:pw123secret@db.example.test/site' );
        $this->assertStringNotContainsString( 'pw123secret', $out );
        $this->assertStringContainsString( 'mysql://user:***@', $out );
        $out = $r->text( 'GET /x?password=hunter2&token=abc123&api_key=XYZ987 client_secret: s3cr3t' );
        foreach ( array( 'hunter2', 'abc123', 'XYZ987', 's3cr3t' ) as $secret )
            $this->assertStringNotContainsString( $secret, $out );
        $this->assertStringContainsString( 'Bearer ***', $r->text( 'Authorization: Bearer abc123def456ghi' ) );
        $this->assertSame( 'Token mismatch', $r->text( 'Token mismatch' ), 'a word after Token is no credential' );
        $this->assertSame( 'jwt *** end', $r->text( 'jwt eyJhbGciOiJIUzI1.eyJzdWIiOiIxMjM0.SflKxwRJSMeKKF2QT4 end' ) );
        $this->assertStringNotContainsString( 'abcdef123456', $r->text( 'cookie eZSESSID=abcdef123456; path=/' ) );
        $this->assertStringNotContainsString( 'a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5', $r->text( 'key a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5 used' ) );
        $this->assertSame( 'explayouts_content_browser_core', $r->text( 'explayouts_content_browser_core' ) );
    }

    /** LV-23 */
    public function testSettingSecretsAlways()
    {
        $masked = new expLogRedactor( true, true, true, array( 'Sup3rS3cretDbPass', 'abc' ) );
        $plain = new expLogRedactor( false, false, false, array( 'Sup3rS3cretDbPass', 'abc' ) );
        foreach ( array( $masked, $plain ) as $r )
        {
            $out = $r->text( 'login with Sup3rS3cretDbPass failed for abc' );
            $this->assertStringNotContainsString( 'Sup3rS3cretDbPass', $out );
            $this->assertStringContainsString( 'abc', $out, 'a value shorter than four characters is not masked' );
            $this->assertStringNotContainsString( 'pw999', $r->text( 'mysql://u:pw999@h/db' ), 'a password in an address never shows' );
        }
        $this->assertSame( '', $masked->text( '' ) );
    }

    /** LV-24 */
    public function testUnmasked()
    {
        $r = new expLogRedactor( false, false, false );
        $this->assertFalse( $r->masking() );
        $this->assertSame( 'jane@example.com from 192.0.2.5', $r->text( 'jane@example.com from 192.0.2.5' ) );
        $this->assertSame( '192.0.2.5', $r->ip( '192.0.2.5' ) );
        $this->assertTrue( ( new expLogRedactor() )->masking() );

        $config = expLogViewerConfig::fromArray( array( 'RedactionSettings' => array( 'MaskIPs' => 'disabled', 'SecretSettings' => array(), 'SecretGroups' => array() ) ), sys_get_temp_dir() );
        $r = expLogRedactor::fromConfig( $config );
        $this->assertSame( 'j***@***.com 192.0.2.5', $r->text( 'jane@example.com 192.0.2.5' ) );
        $r = expLogRedactor::fromConfig( $config, true );
        $this->assertSame( 'jane@example.com 192.0.2.5', $r->text( 'jane@example.com 192.0.2.5' ) );
    }

    /** LV-25 a block of many lines is masked as each line on its own (the download masks blocks, the viewer lines) */
    public function testBlockMaskedLikeLines()
    {
        $lines = array( 'connect mysql://app:hunter2@db.example.test/site failed', 'plain line without anything',
                        'user:secretpw@db.example.test', 'Error: password=abc123 for jane@example.com from 192.0.2.7',
                        'token: zzz9 session_id=ABCDEF1234567890ABCDEF1234567890', 'eyJhbGciOiJIUzI1.eyJzdWIiOiIxMjM0.SflKxwRJSMeKKF2QT4' );
        $r = new expLogRedactor( true, true, true, array( 'hunter2' ) );
        $one = array_map( array( $r, 'text' ), $lines );
        $this->assertSame( implode( "\n", $one ), $r->text( implode( "\n", $lines ) ) );
        $this->assertStringNotContainsString( 'hunter2', $r->text( implode( "\n", $lines ) ) );
        $this->assertStringNotContainsString( 'secretpw', $r->text( implode( "\n", $lines ) ) );
    }
}
