<?php
/**
 * The shared rule of what is a secret (expSecretRule), used by exp:ini, the audit log, the settings pages and the
 * system report. No database.
 *
 *  SE-01 - Names with password, secret, token, salt, credential, private key or API key, and names ending in pwd,
 *          dsn, Key, _key or -key are secrets
 *  SE-02 - Names that only look alike are not: SortKey, KeyField, Keywords, SessionTimeout, AuthorizationURL
 *  SE-03 - The password of user:password@ is replaced, with or without a scheme or a user; user and host stay
 *  SE-04 - The values of password=, pwd=, token: and 'secret' => pairs are replaced, also with a prefix
 *  SE-05 - Values without a secret are left exactly as they are; each caller chooses its mask text
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSecretRuleTest extends PHPUnit\Framework\TestCase
{
    /** SE-01 */
    public function testSecretNames()
    {
        foreach ( array( 'Password', 'DatabasePassword', 'SMTPPasswd', 'Passphrase', 'ClientSecret', 'ApiToken', 'Salt',
                         'Credentials', 'PrivateKey', 'ApiKey', 'apikey', 'LicenseKey', 'license_key', 'LICENSE-KEY', 'Key',
                         'key', 'DbPwd', 'db_pwd', 'DSN', 'DatabaseDsn', 'cache_dsn', 'Password[]' ) as $name )
            $this->assertTrue( expSecretRule::isSecretName( $name ), $name );
    }

    /** SE-02 */
    public function testNotSecretNames()
    {
        foreach ( array( '', 'User', 'Server', 'SiteName', 'SortKey', 'CacheKey', 'PrimaryKey', 'HotKey', 'KeyField', 'Keywords',
                         'KeywordList', 'MonkeyList', 'SessionTimeout', 'SessionNameHandler', 'AuthorizationURL', 'CookieTimeout',
                         'InboundSignatureHeader', 'DSNList' ) as $name )
            $this->assertFalse( expSecretRule::isSecretName( $name ), $name );
    }

    /** SE-03 */
    public function testPasswordsInAddresses()
    {
        $this->assertSame( 'mysql://root:#@db.example/xa', expSecretRule::maskInline( 'mysql://root:s3cr3t@db.example/xa', '#' ) );
        $this->assertSame( 'smtp://:#@mail:25', expSecretRule::maskInline( 'smtp://:pw@mail:25', '#' ) );
        $this->assertSame( 'user:#@host:3306/db', expSecretRule::maskInline( 'user:pw@host:3306/db', '#' ) );
        $this->assertSame( 'see https://a:***@example.org/x and more', expSecretRule::maskInline( 'see https://a:b@example.org/x and more' ) );
    }

    /** SE-04 */
    public function testPairs()
    {
        $this->assertSame( 'Server=db;Password=#;Port=1', expSecretRule::maskInline( 'Server=db;Password=x1;Port=1', '#' ) );
        $this->assertSame( 'host=a pwd=#', expSecretRule::maskInline( 'host=a pwd=zz', '#' ) );
        $this->assertSame( 'db_password: #', expSecretRule::maskInline( 'db_password: hunter2', '#' ) );
        $this->assertSame( '?a=1&access_token=#&b=2', expSecretRule::maskInline( '?a=1&access_token=abc&b=2', '#' ) );
        $this->assertSame( "'secret' => #", expSecretRule::maskInline( "'secret' => 'xyz'", '#' ) );
        $this->assertSame( 'DbPassword=#', expSecretRule::maskInline( 'DbPassword="a b"', '#' ) );
    }

    /** SE-05 */
    public function testPlainValuesStay()
    {
        foreach ( array( 'https://example.com/path', 'https://alpha.example.org:8080/admin/', 'info@example.com', 'mysql',
                         'a=b;c=d', 'design:node/view/full.tpl', 'opcache.file_update_protection=0', 'pm = ondemand',
                         'bypass=1', 'passenger=2', 'IniOptions[]=opcache.enable_cli=1' ) as $plain )
        {
            $this->assertSame( $plain, expSecretRule::maskInline( $plain ), $plain );
            $this->assertFalse( expSecretRule::hasInlineSecret( $plain ), $plain );
        }
        $this->assertTrue( expSecretRule::hasInlineSecret( 'pgsql://u:p@h/d' ) );
        $this->assertSame( 'pgsql://u:$1@h/d', expSecretRule::maskInline( 'pgsql://u:p@h/d', '$1' ) );
    }
}
