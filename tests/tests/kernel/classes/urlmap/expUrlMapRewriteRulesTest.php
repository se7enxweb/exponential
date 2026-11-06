<?php
/**
 * Apache and nginx rewrite rules as URL maps (kernel/classes/urlmap/expurlmapapachereader.php,
 * expurlmapapachewriter.php, expurlmapnginxreader.php, expurlmapnginxwriter.php, expurlmapregex.php). No database.
 *
 *  RW-01 - Regular expressions to wildcards: anchors, optional slashes, simple groups, bare .*, escapes, group
 *          numbering, and the reasons for everything else
 *  RW-02 - Targets: $N to {N}, \$, %N and variables refused, $scheme://$host dropped (nginx)
 *  RW-03 - The Apache fixture: Redirect, RedirectPermanent, RedirectTemp, RedirectMatch, RewriteRule with R, L, PT,
 *          host and query conditions, VirtualHost ServerName, continuation lines, #exp: comments, line numbers
 *  RW-04 - The nginx fixture: map, rewrite (permanent, redirect, last), location = / ~ with return, if ($args),
 *          server_name, prefix locations and return 410 not importable, #exp: comments, line numbers
 *  RW-05 - The Apache writer: RewriteRule per redirect and wildcard, host and query conditions, internal kinds and
 *          node targets as #exp: comments, rows that are not importable left out
 *  RW-06 - The nginx writer: location = with return, grouped queries, duplicates, rewrite for wildcards, server
 *          blocks per host, #exp: comments
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUrlMapRewriteRulesTest extends PHPUnit\Framework\TestCase
{
    /** RW-01 */
    public function testRegexToWildcard()
    {
        $cases = array(
            '^/old$' => array( '/old', array() ),
            '^old$' => array( '/old', array() ),
            '^/?old/?$' => array( '/old', array() ),
            '^/old/(.*)$' => array( '/old/*', array( 1 => 1 ) ),
            '^/old/(.+)/item\.html$' => array( '/old/*/item.html', array( 1 => 1 ) ),
            '^/page.html$' => array( '/page.html', array() ),
            '^/a/.*/b/(.*)$' => array( '/a/*/b/*', array( 1 => 2 ) ),
            '^/blog' => array( '/blog*', array() ),
            '^/blog(.*)' => array( '/blog*', array( 1 => 1 ) ),
            '^/a\-b\/c$' => array( '/a-b/c', array() ),
            '^/(.*?)/x$' => array( '/*/x', array( 1 => 1 ) ),
        );
        foreach ( $cases as $regex => $expected )
        {
            $result = expUrlMapRegex::toWildcard( $regex );
            $this->assertArrayNotHasKey( 'reason', $result, $regex );
            $this->assertSame( $expected[0], $result['pattern'], $regex );
            $this->assertSame( $expected[1], $result['groups'], $regex );
        }
        $result = expUrlMapRegex::toWildcard( '^/tag/([^/]+)$' );
        $this->assertSame( '/tag/*', $result['pattern'] );
        $this->assertStringContainsString( 'also matches a /', $result['notes'][0] );

        $refused = array(
            'old$' => 'not anchored',
            '^/a/(\d+)$' => '\\d',
            '^/(?:a|b)$' => 'not a simple',
            '^/a|b$' => '|',
            '^/a[0-9]$' => '[',
            '^/ab?$' => '?',
            '^/a+$' => '+',
            '^/a{2}$' => '{',
        );
        foreach ( $refused as $regex => $part )
        {
            $result = expUrlMapRegex::toWildcard( $regex );
            $this->assertArrayHasKey( 'reason', $result, $regex );
            $this->assertStringContainsString( $part, $result['reason'], $regex );
        }

        $this->assertSame( '^/?old/(.*)/x/?$', expUrlMapRegex::fromWildcard( '/old/*/x', true, true ) );
        $this->assertSame( '^/?old/?$', expUrlMapRegex::fromWildcard( '/old', true, true ) );
        $this->assertSame( '^/?$', expUrlMapRegex::fromWildcard( '/', true, true ) );
        $this->assertSame( '^/a\.b/(.*)$', expUrlMapRegex::fromWildcard( '/a.b/*', false, false ) );
    }

    /** RW-02 */
    public function testTargets()
    {
        $this->assertSame( array( 'target' => '/n/{1}/{2}' ), expUrlMapRegex::toPlaceholders( '/n/$1/$2', array( 1 => 1, 2 => 2 ), 'apache' ) );
        $this->assertSame( array( 'target' => '/n/{2}' ), expUrlMapRegex::toPlaceholders( '/n/$1', array( 1 => 2 ), 'nginx' ) );
        $this->assertSame( array( 'target' => '/price$' ), expUrlMapRegex::toPlaceholders( '/price\$', array(), 'apache' ) );
        $this->assertSame( array( 'target' => '/Contact' ), expUrlMapRegex::toPlaceholders( '$scheme://$host/Contact', array(), 'nginx' ) );
        $this->assertArrayHasKey( 'reason', expUrlMapRegex::toPlaceholders( '/n/$2', array( 1 => 1 ), 'apache' ) );
        $this->assertArrayHasKey( 'reason', expUrlMapRegex::toPlaceholders( '/n/%1', array(), 'apache' ) );
        $this->assertArrayHasKey( 'reason', expUrlMapRegex::toPlaceholders( '/n/%{HTTP_HOST}', array(), 'apache' ) );
        $this->assertArrayHasKey( 'reason', expUrlMapRegex::toPlaceholders( '/n/$arg_id', array(), 'nginx' ) );
        $this->assertSame( '/n/$1/{x}', expUrlMapRegex::fromPlaceholders( '/n/{1}/{x}', false ) );
        $this->assertSame( '/p\$/$1', expUrlMapRegex::fromPlaceholders( '/p$/{1}', true ) );
    }

    /**
     * @param expUrlMapRow[] $rows
     * @return array line => "old -> new kind" or "line: problem"
     */
    private function summary( array $rows )
    {
        $out = array();
        foreach ( $rows as $row )
        {
            $text = $row->isImportable() ? $row->oldUrl() . ' -> ' . $row->newUrl() . ' ' . $row->effectiveKind()
                                         : 'X ' . $row->problems()[0];
            $out[] = $row->line() . ': ' . $text;
        }
        return $out;
    }

    /** RW-03 */
    public function testApacheFixture()
    {
        $reader = new expUrlMapApacheReader();
        $rows = $reader->readFile( __DIR__ . '/fixtures/rules.htaccess' );
        $summary = $this->summary( $rows );
        $expected = array(
            '3: /about-us.html -> /About redirect',
            '4: /contact.php -> https://www.example.com/Contact redirect',
            '5: /temp-page -> /Temporary redirect',
            '6: X not importable: status 410 is not a redirect',
            '7: /blog/* -> /News/{1} wildcard-forward',
            '8: X not importable: the group (\d+) in ^/archive/(\d+)$ is not a simple (.*) group',
            '10: /products/* -> /Shop/{1} wildcard-forward',
            '11: /old-shop/*/item.html -> /Shop/{1} wildcard-forward',
            '12: /print/* -> /{1} wildcard-direct',
            '13: /team -> /Team alias',
            '15: //old-site.example.com/news -> /News redirect',
            '17: /index.php?id=5 -> /Home redirect',
            '19: X not importable: the condition "%{REQUEST_FILENAME} !-f" cannot be expressed',
            '20: X not importable: the flag F cannot be expressed',
            '21: X not importable: the pattern ^/?a|b$ uses |, which a wildcard cannot express',
            '22: X not importable: the target /y/%1 uses %1 (a condition back-reference or a server variable)',
            '23: /internal -> /Inside alias',
            '26: //legacy.example.com/start -> /Home redirect',
            '27: //legacy.example.com/very/long/path -> /Long redirect',
        );
        $this->assertSame( $expected, $summary );
        $this->assertStringContainsString( 'answered with 302', $rows[2]->note() );
        $this->assertStringContainsString( 'answered with 302', $rows[7]->note() );
        $this->assertSame( 'Redirect gone /removed', $rows[3]->note() );
        $this->assertSame( array(), $reader->readString( '' ) );
        $this->assertSame( array(), $reader->readString( "# nothing\nRewriteEngine On\nOptions -Indexes\n" ) );
        $this->assertSame( array( 'Rewrite', 'a b', 'c"d' ), expUrlMapApacheReader::tokenize( 'Rewrite "a b" "c\\"d"' ) );
    }

    /** RW-04 */
    public function testNginxFixture()
    {
        $reader = new expUrlMapNginxReader();
        $rows = $reader->readFile( __DIR__ . '/fixtures/rules.nginx' );
        $expected = array(
            '4: /about-us.html -> /About redirect',
            '5: /blog/* -> /News/{1} wildcard-forward',
            '6: X not importable: the group (\d+) in ^/archive/(\d+)$ is not a simple (.*) group',
            '15: //old-site.example.com/products/* -> /Shop/{1} wildcard-forward',
            '16: //old-site.example.com/temp -> /Temporary redirect',
            '17: //old-site.example.com/print/* -> /{1} wildcard-direct',
            '18: X not importable: the target /y/$host/$1 uses a variable, which a mapping cannot express',
            '21: //old-site.example.com/contact.php -> /Contact redirect',
            '24: //old-site.example.com/index.php?id=5 -> /Home redirect',
            '25: //old-site.example.com/index.php -> / redirect',
            '28: //old-site.example.com/team/* -> /Team/{1} wildcard-forward',
            '31: X not importable: a return in a prefix location matches every path that starts with /downloads; use location = or a regular expression',
            '34: X not importable: return 410 does not redirect',
            '37: //old-site.example.com/with%20space -> /Spaced redirect',
            '39: /legacy/* -> /{1} wildcard-direct',
        );
        $this->assertSame( $expected, $this->summary( $rows ) );
        $this->assertStringContainsString( 'answered with 302', $rows[4]->note() );
        $this->assertSame( array(), $reader->readString( '' ) );
        $this->assertSame( array(), $reader->readString( "server { listen 80; root /var/www; }\n" ) );
        $rows = $reader->readString( "return 301 /x;\nmap \$host \$y { a b; }\nrewrite ^/a$ /b break;\nif (\$x) { rewrite ^/c$ /d permanent; }" );
        $this->assertSame( array(
            '1: X not importable: a return outside a location answers every request of the server',
            '2: X not importable: a map of $host is not a map of paths',
            '3: /a -> /b alias',
            '4: X not importable: a rewrite inside an if cannot be expressed',
        ), $this->summary( $rows ) );
    }

    /** RW-05 */
    public function testApacheWriter()
    {
        $rows = array(
            new expUrlMapRow( '/about-us.html', '/About', '', '', '', 'old page' ),
            new expUrlMapRow( 'https://Old.example.com/news/', '/News', '', 'site', 'eng-GB' ),
            new expUrlMapRow( '/index.php?id=5', '/Home' ),
            new expUrlMapRow( '/products/*', '/Shop/{1}' ),
            new expUrlMapRow( '/Über uns', '/Ueber-uns' ),
            new expUrlMapRow( '/team', 'node:42' ),
            new expUrlMapRow( '/inside', '/Inside', 'alias' ),
            new expUrlMapRow( '/print/*', '/{1}', 'wildcard-direct' ),
            new expUrlMapRow( '', '/x' ),
        );
        $writer = new expUrlMapApacheWriter();
        $out = $writer->writeString( $rows );
        $this->assertStringContainsString( "RewriteEngine On\n", $out );
        $this->assertStringContainsString( "# old page\nRewriteRule ^/?about\\-us\\.html/?$ /About [R=301,L]\n", $out );
        $this->assertStringContainsString( "# siteaccess site; language eng-GB\nRewriteCond %{HTTP_HOST} ^old\\.example\\.com$ [NC]\nRewriteRule ^/?news/?$ /News [R=301,L]\n", $out );
        $this->assertStringContainsString( "RewriteCond %{QUERY_STRING} ^id\\=5$\nRewriteRule ^/?index\\.php/?$ /Home? [R=301,L]\n", $out );
        $this->assertStringContainsString( "RewriteRule ^/?products/(.*)$ /Shop/$1 [R=301,L]\n", $out );
        $this->assertStringContainsString( "RewriteRule \"^/?Über uns/?$\" /Ueber-uns [R=301,L]\n", $out );
        $this->assertStringContainsString( "#exp:redirect /team node:42\n", $out );
        $this->assertStringContainsString( "#exp:alias /inside /Inside\n", $out );
        $this->assertStringContainsString( "#exp:wildcard-direct /print/* /{1}\n", $out );
        $this->assertStringContainsString( "# not written: (old_url is empty)\n", $out );
    }

    /** RW-06 */
    public function testNginxWriter()
    {
        $rows = array(
            new expUrlMapRow( '/index.php?id=5', '/Home' ),
            new expUrlMapRow( '/about', '/About' ),
            new expUrlMapRow( '/index.php', '/' ),
            new expUrlMapRow( '/index.php?id=6', '/Six' ),
            new expUrlMapRow( '/about', '/Other' ),
            new expUrlMapRow( '/products/*', '/Shop/{1}' ),
            new expUrlMapRow( 'https://old.example.com/news', '/News' ),
            new expUrlMapRow( '/with space', '/Spaced' ),
            new expUrlMapRow( '/inside', '/Inside', 'alias' ),
        );
        $writer = new expUrlMapNginxWriter();
        $out = $writer->writeString( $rows );
        $this->assertStringContainsString( "location = /index.php {\n    if (\$args = \"id=5\") { return 301 /Home; }\n"
                                         . "    if (\$args = \"id=6\") { return 301 /Six; }\n    return 301 /;\n}\n", $out );
        $this->assertStringContainsString( "location = /about {\n    return 301 /About;\n}\n# not written: a second redirect of /about to /Other\n", $out );
        $this->assertStringContainsString( "rewrite ^/products/(.*)$ /Shop/$1 permanent;\n", $out );
        $this->assertStringContainsString( "location = \"/with space\" {\n    return 301 /Spaced;\n}\n", $out );
        $this->assertStringContainsString( "#exp:alias /inside /Inside\n", $out );
        $this->assertStringContainsString( "server {\n    server_name old.example.com;\n\n    location = /news {\n        return 301 /News;\n    }\n}\n", $out );
        $this->assertSame( 1, substr_count( $out, 'location = /index.php' ) );
    }
}
