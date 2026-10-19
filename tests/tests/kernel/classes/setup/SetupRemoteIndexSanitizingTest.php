<?php
/**
 * The setup wizard's site package step shows entries of a remote index.xml, data from another server:
 *   - eZStepSiteTypes::sanitizeRemotePackageAttributes() skips an entry without a valid name and drops a url or
 *     thumbnail_url that is not an http(s) URL (javascript:, data:, file://, relative, with quotes or spaces)
 *   - setup/init/site_types.tpl escapes every remote field it prints (description, summary, version, url,
 *     thumbnail_url, requirement names) and shows a thumbnail only for an http(s) URL
 *   - package/view/full.tpl (design/standard) escapes the maintainer and changelog e-mail addresses
 *
 * No database, no network.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SetupRemoteIndexSanitizingTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    public function testValidEntryIsKept()
    {
        $entry = array( 'name' => 'sevenx_site', 'type' => 'site', 'version' => '1.0.0-0',
                        'summary' => 'Site', 'description' => 'A <b>site</b>',
                        'url' => 'https://packages.example.invalid/6.0/6.0.15/sevenx_site.ezpkg',
                        'thumbnail_url' => 'http://packages.example.invalid/thumbnails/sevenx_site.png' );
        $this->assertSame( $entry, eZStepSiteTypes::sanitizeRemotePackageAttributes( $entry ) );
    }

    public function testEntryWithoutValidNameIsSkipped()
    {
        foreach ( array( null, '', '../evil', 'a b', 'x"><script>', '.hidden', 'name/with/slash' ) as $name )
        {
            $entry = array( 'type' => 'site' );
            if ( $name !== null )
                $entry['name'] = $name;
            $this->assertFalse( eZStepSiteTypes::sanitizeRemotePackageAttributes( $entry ), var_export( $name, true ) );
        }
    }

    public function testNonHttpURLsAreDropped()
    {
        foreach ( array( 'javascript:alert(1)', 'JaVaScRiPt:alert(1)', 'data:image/svg+xml;base64,PHN2Zz4=', 'file:///etc/passwd',
                         '/relative/thumb.png', '//example.invalid/thumb.png', 'https://example.invalid/a" onerror="x',
                         ' https://example.invalid/a.png', 'https://example.invalid/a b.png', 'ftp://example.invalid/a.png',
                         'https:///nohost.png' ) as $url )
        {
            $result = eZStepSiteTypes::sanitizeRemotePackageAttributes( array( 'name' => 'p', 'url' => $url, 'thumbnail_url' => $url ) );
            $this->assertArrayNotHasKey( 'url', $result, $url );
            $this->assertArrayNotHasKey( 'thumbnail_url', $result, $url );
            $this->assertSame( 'p', $result['name'] );
        }
        $this->assertTrue( eZStepSiteTypes::isHttpURL( 'HTTPS://example.invalid/a.png' ) );
    }

    public function testSiteTypesTemplateEscapesRemoteFields()
    {
        $tpl = file_get_contents( self::root() . '/design/standard/templates/setup/init/site_types.tpl' );
        $this->assertStringContainsString( '{$package_info.description|wash|nl2br}', $tpl );
        $this->assertStringContainsString( 'src="{$package_info.thumbnail_url|wash}"', $tpl );
        $this->assertStringContainsString( "'%summary', \$package_info.summary|wash, '%version', \$package_info.version|wash", $tpl );
        $this->assertStringContainsString( "'%req_name', \$req_name|wash, '%version', \$req_info.version|wash", $tpl );
        $this->assertStringContainsString( 'value="{$package_info.name|wash}|{$package_info.url|wash}"', $tpl );
        $this->assertStringContainsString( '<a href="{$index_url|wash}">', $tpl );
        // every $package_info field printed directly is washed
        preg_match_all( '/\{\$package_info\.[a-z_]+([^}]*)\}/', $tpl, $m, PREG_SET_ORDER );
        $this->assertNotEmpty( $m );
        foreach ( $m as $match )
        {
            // thumbnail_path is a local file of an imported package, printed through ezroot (quoted)
            if ( strpos( $match[0], '{$package_info.thumbnail_path|ezroot}' ) === 0 )
                continue;
            $this->assertStringContainsString( '|wash', $match[1], $match[0] . ' is printed without |wash' );
        }
    }

    public function testStandardPackageViewEscapesEmails()
    {
        $tpl = file_get_contents( self::root() . '/design/standard/templates/package/view/full.tpl' );
        $this->assertStringNotContainsString( 'mailto:{$maintainer.item.email}', $tpl );
        $this->assertStringNotContainsString( 'mailto:{$log.item.email}', $tpl );
        $this->assertStringContainsString( 'mailto:{$maintainer.item.email|wash}', $tpl );
        $this->assertStringContainsString( 'mailto:{$log.item.email|wash}', $tpl );
    }
}
