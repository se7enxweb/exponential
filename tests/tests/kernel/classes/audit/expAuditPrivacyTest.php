<?php
/**
 * The privacy rules of audit records (doc/bc/6.0/audit.md, "Fields", acceptance test B5).
 *
 *  AP-01 — defaults: IPv4 to /24, IPv6 to /48, the session hashed (h: + 16 hex), the user agent shortened, the URL
 *          without query values, the login in full
 *  AP-02 — every field x full / truncate / hash / off; the session cannot be written in full
 *  AP-03 — the hash is HMAC-SHA-256 with the pseudonym key: the same input gives the same output, another key
 *          another output
 *  AP-04 — SecretPathViews[]: user/activate/<hash>, user/forgotpassword/<hash>, userpaex/forgotpassword/<hash> lose
 *          their parameters, also behind a siteaccess prefix and with full URLs; secret query names lose values
 *  AP-05 — never recorded: NeverRecord[] keys, secrets ([secret]), at every depth of object, target, before, after
 *          and x; a failed login of an unknown user writes the attempted login hashed
 *  AP-06 — BeforeAfter=keys keeps the keys with sha256 values; =disabled drops before and after; MaxValueLength
 *          cuts long values with "…"
 *  AP-07 — the user agent families
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

class expAuditPrivacyTest extends PHPUnit\Framework\TestCase
{
    protected $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = expAuditTestFixtures::setUp( $this->name() );
    }

    protected function tearDown(): void
    {
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    protected function privacy( array $settings = array() )
    {
        expAuditTestFixtures::configure( $this->dir, $settings );
        $config = expAuditConfig::get();
        return new expAuditPrivacy( $config, new expAuditKeys( $config ) );
    }

    protected function lastAccess()
    {
        $events = expAuditTestFixtures::events( $this->dir, 'access' );
        return end( $events );
    }

    /** AP-01 */
    public function testDefaults()
    {
        expAudit::event( 'access.session.login', array( 'object' => array( 'type' => 'user', 'id' => 14 ) ) );
        $r = $this->lastAccess();
        $this->assertSame( '203.0.113.0/24', $r['actor']['ip'] );
        $this->assertMatchesRegularExpression( '/^h:[0-9a-f]{16}$/', $r['actor']['session'] );
        $this->assertSame( 'Firefox 131 / Linux', $r['actor']['ua'] );
        $this->assertSame( 'editor1', $r['actor']['login'] );
        $this->assertSame( '/content/action?NodeID=…&ezxform_token=…', $r['request']['url'] );
        $this->assertStringNotContainsString( 'sess-0123456789abcdef', json_encode( $r ) );
    }

    /** AP-02 */
    public function testOptionsPerField()
    {
        $actor = array( 'login' => 'editor1', 'ip' => '2001:db8:12:3:4::1', 'ua' => 'curl/8.5.0', 'session' => 'S', 'cli' => array( 'os_user' => 'alpha' ) );
        $record = array( 'actor' => $actor, 'request' => array( 'url' => '/a/b?c=d', 'host' => 'web1' ), 'object' => array( 'type' => 'node', 'name' => str_repeat( 'n', 100 ) ) );

        $p = $this->privacy();
        $r = $p->apply( $record );
        $this->assertSame( '2001:db8:12::/48', $r['actor']['ip'] );
        $this->assertSame( 'curl 8', $r['actor']['ua'] );
        $this->assertSame( 'alpha', $r['actor']['cli']['os_user'] );
        $this->assertSame( 'web1', $r['request']['host'] );
        $this->assertSame( 100, strlen( $r['object']['name'] ) );

        $full = array( 'actor.login' => 'full', 'actor.ip' => 'full', 'actor.ua' => 'full', 'actor.session' => 'full', 'request.url' => 'full',
                       'object.name' => 'truncate' );
        $r = $this->privacy( array( 'AuditPrivacySettings/Field' => $full ) )->apply( $record );
        $this->assertSame( '2001:db8:12:3:4::1', $r['actor']['ip'] );
        $this->assertSame( 'curl/8.5.0', $r['actor']['ua'] );
        $this->assertMatchesRegularExpression( '/^h:/', $r['actor']['session'], 'the session is a token: never in full' );
        $this->assertSame( '/a/b?c=d', $r['request']['url'] );
        $this->assertSame( str_repeat( 'n', 64 ) . '…', $r['object']['name'] );

        $hash = array_fill_keys( array( 'actor.login', 'actor.ip', 'actor.ua', 'actor.session', 'actor.cli.os_user', 'request.url', 'request.host', 'object.name' ), 'hash' );
        $r = $this->privacy( array( 'AuditPrivacySettings/Field' => $hash ) )->apply( $record );
        foreach ( array( $r['actor']['login'], $r['actor']['ip'], $r['actor']['ua'], $r['actor']['session'], $r['actor']['cli']['os_user'],
                         $r['request']['url'], $r['request']['host'], $r['object']['name'] ) as $v )
            $this->assertMatchesRegularExpression( '/^h:[0-9a-f]{16}$/', $v );

        $off = array_fill_keys( array_keys( $hash ), 'off' );
        $r = $this->privacy( array( 'AuditPrivacySettings/Field' => $off ) )->apply( $record );
        $this->assertSame( array(), array_diff( array_keys( $r['actor'] ), array( 'cli' ) ) );
        $this->assertSame( array(), $r['actor']['cli'] );
        $this->assertSame( array(), $r['request'] );
        $this->assertSame( array( 'type' => 'node' ), $r['object'] );
    }

    /** AP-03 */
    public function testHashIsKeyed()
    {
        $p = $this->privacy();
        $this->assertSame( 'h:' . substr( hash_hmac( 'sha256', 'editor1', str_repeat( "\xa5", 32 ) ), 0, 16 ), $p->hash( 'editor1' ) );
        $this->assertSame( $p->hash( 'editor1' ), $p->hash( 'editor1' ) );
        $first = $p->hash( 'editor1' );
        $other = expAuditTestFixtures::keys();
        $other['pseudonym'] = str_repeat( 'x', 32 );
        expAuditKeys::setKnown( $other, $this->dir . 'keys' );
        $this->assertNotSame( $first, $this->privacy()->hash( 'editor1' ) );
        // the truncate of IPv4 at other prefixes
        $p = $this->privacy( array( 'AuditPrivacySettings/IPv4Prefix' => '16', 'AuditPrivacySettings/IPv6Prefix' => '32' ) );
        $this->assertSame( '203.0.0.0/16', $p->truncateIp( '203.0.113.7' ) );
        $this->assertSame( '2001:db8::/32', $p->truncateIp( '2001:db8:12:3::1' ) );
    }

    /** AP-04 */
    public function testSecretPaths()
    {
        $p = $this->privacy();
        $this->assertSame( '/user/activate/…', $p->truncateUrl( '/user/activate/0123abcd/14' ) );
        $this->assertSame( '/admin/user/forgotpassword/…', $p->truncateUrl( '/admin/user/forgotpassword/feedbeef' ) );
        $this->assertSame( '/userpaex/forgotpassword/…', $p->secretPath( '/userpaex/forgotpassword/abc' ) );
        $this->assertSame( '/user/login?Password=…&next=/x', $p->secretPath( '/user/login?Password=secret1&next=/x' ) );
        $this->assertSame( '/content/view/full/2', $p->secretPath( '/content/view/full/2' ) );
        $r = $this->privacy( array( 'AuditPrivacySettings/Field' => array( 'request.url' => 'full' ) ) )
                  ->apply( array( 'request' => array( 'url' => '/user/activate/0123abcd/14?token=t' ) ) );
        $this->assertSame( '/user/activate/…?token=…', $r['request']['url'], 'full still never writes a token' );
    }

    /** AP-05 */
    public function testNeverRecorded()
    {
        $id = expAudit::event( 'access.user.password.change', array(
            'object' => array( 'type' => 'user', 'id' => 14, 'Password' => 'v-p1', 'nested' => array( 'HashKey' => 'v-k', 'api_token' => 'v-t' ) ),
            'before' => array( 'password_hash' => 'v-h', 'SigningKey' => 'v-s', 'value' => 'ok' ),
            'after' => array( 'PasswordConfirm' => 'v-p1', 'ezxform_token' => 'v-x', 'deep' => array( 'deeper' => array( 'secret' => 'v-z' ) ) ),
            'x' => array( 'myext' => array( 'ApiKey' => 'v-a', 'poll' => 12 ) ) ) );
        $this->assertNotNull( $id );
        $r = $this->lastAccess();
        $json = json_encode( $r );
        foreach ( array( 'v-p1', 'v-k', 'v-t', 'v-h', 'v-s', 'v-x', 'v-z', 'v-a' ) as $value )
            $this->assertStringNotContainsString( $value, $json, $value );
        $this->assertArrayNotHasKey( 'Password', $r['object'] );
        $this->assertArrayNotHasKey( 'HashKey', $r['object']['nested'] );
        $this->assertSame( '[secret]', $r['object']['nested']['api_token'] );
        $this->assertSame( '[secret]', $r['before']['SigningKey'] );
        $this->assertSame( 'ok', $r['before']['value'] );
        $this->assertSame( '[secret]', $r['x']['myext']['ApiKey'] );
        $this->assertSame( 12, $r['x']['myext']['poll'] );

        expAudit::legacy( 'user-failed-login', array( 'User login' => 'MySecretPassw0rd', 'Comment' => 'Failed login attempt' ) );
        $r = $this->lastAccess();
        $this->assertSame( 'access.session.login.failed', $r['name'] );
        $this->assertMatchesRegularExpression( '/^h:[0-9a-f]{16}$/', $r['object']['attempted_login'] );
        $this->assertStringNotContainsString( 'MySecretPassw0rd', json_encode( $r ) );
        $this->assertSame( 'not_found', $r['reason'] );
    }

    /** AP-06 */
    public function testBeforeAfterModes()
    {
        $data = array( 'object' => array( 'type' => 'node', 'id' => 1 ), 'before' => array( 'parent' => 2 ), 'after' => array( 'parent' => 89, 'long' => str_repeat( 'x', 600 ) ) );
        expAuditTestFixtures::configure( $this->dir, array( 'AuditRecordSettings/BeforeAfter' => 'keys' ) );
        expAudit::event( 'access.role.change', $data );
        $r = $this->lastAccess();
        $this->assertSame( 'sha256:' . hash( 'sha256', '2' ), $r['before']['parent'] );
        expAuditTestFixtures::configure( $this->dir, array( 'AuditRecordSettings/BeforeAfter' => 'disabled' ) );
        expAudit::event( 'access.role.change', $data );
        $r = $this->lastAccess();
        $this->assertArrayNotHasKey( 'before', $r );
        $this->assertArrayNotHasKey( 'after', $r );
        expAuditTestFixtures::configure( $this->dir, array( 'AuditRecordSettings/MaxValueLength' => '100' ) );
        expAudit::event( 'access.role.change', $data );
        $r = $this->lastAccess();
        $this->assertSame( str_repeat( 'x', 100 ) . '…', $r['after']['long'] );
    }

    /** AP-07 */
    public function testUserAgents()
    {
        $cases = array(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36 Edg/129.0.2792.79' => 'Edge 129 / Windows',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.6 Safari/605.1.15' => 'Safari 17 / macOS',
            'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Mobile Safari/537.36' => 'Chrome 129 / Android',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.6 Mobile/15E148 Safari/604.1' => 'Safari 17 / iOS',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/130.0.0.0 Safari/537.36' => 'HeadlessChrome 130 / Linux',
            'python-requests/2.31.0' => 'python-requests 2',
            'SomeBot/3.1' => 'SomeBot 3',
        );
        foreach ( $cases as $ua => $expected )
            $this->assertSame( $expected, expAuditPrivacy::truncateUserAgent( $ua ), $ua );
    }
}
