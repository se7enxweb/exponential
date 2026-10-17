<?php
/**
 * The siteaccess map of Setup > System information (expSiteAccessOverview::build(), #552). No database, no INI files.
 *
 *  SA-01 - The names are AvailableSiteAccessList, then the related-only ones, each once
 *  SA-02 - The rules follow MatchOrder, with the entries of the URI element, URI map, host map, host-URI map and port
 *  SA-03 - A request matched by the host map marks that rule and that entry; the URI type with no URI part is the default
 *  SA-04 - A request matched by a URI element marks the entry of its URI part
 *  SA-05 - Each siteaccess lists the entries that reach it; one that none reaches and that is not the default says so
 *  SA-06 - The kind: SiteAccessType first, then editor and admin designs, then RequireUserLogin
 *  SA-07 - Related-only siteaccesses get no Open link; unsafe names get no settings link
 *  SA-08 - MatchOrder=none has no rules; StaticMatch is reported
 *  SA-09 - The template, the view and the report use the map; no inline CSS in the new markup
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expSiteAccessOverviewTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        if ( !class_exists( 'expSiteAccessOverview' ) )
            require_once __DIR__ . '/../../../../kernel/classes/expsiteaccessoverview.php';
    }

    protected function settings( array $more = array() )
    {
        return $more + array(
            'AvailableSiteAccessList' => array( 'site', 'admin', 'editor', 'ger', 'classic' ),
            'RelatedSiteAccessList' => array( 'site', 'admin', 'archive' ),
            'MatchOrder' => 'uri;host',
            'DefaultAccess' => 'site',
            'URIMatchType' => 'element',
            'URIMatchElement' => '1',
            'HostMatchType' => 'map',
            'HostMatchMapItems' => array( 'admin.example.org;admin', 'example.org;site', 'edit.example.org;editor', 'beta.example.org;ger;start' ),
            'HostMatchMethod' => 'strict',
        );
    }

    protected function siteaccesses()
    {
        return array(
            'site' => array( 'site_design' => 'media', 'additional_designs' => array( 'standard' ), 'locale' => 'eng-GB',
                             'languages' => array( 'eng-GB' ), 'readable' => true, 'url' => 'https://example.org/site/' ),
            'admin' => array( 'site_design' => 'admin4', 'require_user_login' => 'true', 'show_hidden_nodes' => 'true',
                              'access_extensions' => array( 'exp_adminui' ), 'readable' => true, 'url' => 'https://admin.example.org/' ),
            'editor' => array( 'site_design' => 'editor', 'additional_designs' => array( 'admin4' ), 'require_user_login' => 'true', 'readable' => true ),
            'archive' => array( 'site_design' => 'media', 'readable' => true, 'url' => 'https://example.org/archive/' ),
        );
    }

    protected function rule( array $map, $probe )
    {
        foreach ( $map['rules'] as $rule )
            if ( $rule['probe'] === $probe )
                return $rule;
        $this->fail( "no rule $probe" );
    }

    protected function row( array $map, $name )
    {
        foreach ( $map['siteaccesses'] as $row )
            if ( $row['name'] === $name )
                return $row;
        $this->fail( "no siteaccess $name" );
    }

    /** SA-01 */
    public function testNamesAreAvailableThenRelatedOnly()
    {
        $map = expSiteAccessOverview::build( $this->settings(), array(), array() );
        $this->assertSame( array( 'site', 'admin', 'editor', 'ger', 'classic', 'archive' ), array_column( $map['siteaccesses'], 'name' ) );
        $this->assertSame( 6, $map['count'] );
        $this->assertSame( 5, $map['available_count'] );
        $this->assertSame( '6 (default: site; match: uri, host)', expSiteAccessOverview::summary( $map ) );
    }

    /** SA-02 */
    public function testRulesFollowMatchOrderWithTheirEntries()
    {
        $map = expSiteAccessOverview::build( $this->settings( array(
            'MatchOrder' => 'host_uri;port;uri;host',
            'HostUriMatchMapItems' => array( 'example.org;de;ger', 'example.org;;site;end' ),
            'PortAccessSettings' => array( '8081' => 'classic' ),
        ) ), array(), array() );
        $this->assertSame( array( 'host_uri', 'port', 'uri', 'host' ), array_column( $map['rules'], 'probe' ) );
        $this->assertSame( array( 1, 2, 3, 4 ), array_column( $map['rules'], 'position' ) );
        $hostUri = $this->rule( $map, 'host_uri' );
        $this->assertSame( array( 'example.org/de/', 'example.org' ), array_column( $hostUri['entries'], 'match' ) );
        $this->assertSame( 'end', $hostUri['entries'][1]['host_method'] );
        $this->assertSame( array( ':8081' ), array_column( $this->rule( $map, 'port' )['entries'], 'match' ) );
        $uri = $this->rule( $map, 'uri' );
        $this->assertSame( '/admin/', $uri['entries'][1]['match'] );
        $this->assertCount( 5, $uri['entries'], 'only available siteaccesses are reached by a URI element' );
        $host = $this->rule( $map, 'host' );
        $this->assertSame( 'start', $host['entries'][3]['host_method'] );

        $map = expSiteAccessOverview::build( $this->settings( array( 'URIMatchType' => 'map', 'URIMatchMapItems' => array( 'de;ger', 'broken' ) ) ), array(), array() );
        $this->assertSame( array( '/de/' ), array_column( $this->rule( $map, 'uri' )['entries'], 'match' ) );
    }

    /** SA-03 */
    public function testHostMatchMarksTheRuleAndEntry()
    {
        $map = expSiteAccessOverview::build( $this->settings(), array(), array( 'name' => 'admin', 'type' => 4, 'host' => 'admin.example.org:8080' ) );
        $this->assertSame( 'host', $map['current']['probe'] );
        $this->assertSame( 'admin.example.org', $map['current']['entry'] );
        $this->assertTrue( $this->rule( $map, 'host' )['wins'] );
        $this->assertFalse( $this->rule( $map, 'uri' )['wins'] );

        // eZSiteAccess::match() types the default as URI when MatchOrder has uri: no URI part means the default chose it
        $map = expSiteAccessOverview::build( $this->settings(), array(), array( 'name' => 'site', 'type' => 2, 'uri_part' => array() ) );
        $this->assertSame( 'default', $map['current']['probe'] );
        $this->assertNull( $map['current']['entry'] );
        $this->assertSame( array( false, false ), array_column( $map['rules'], 'wins' ) );
    }

    /** SA-04 */
    public function testUriMatchMarksTheEntryOfItsPart()
    {
        $map = expSiteAccessOverview::build( $this->settings(), array(), array( 'name' => 'ger', 'type' => 2, 'uri_part' => array( 'ger' ) ) );
        $this->assertSame( 'uri', $map['current']['probe'] );
        $this->assertSame( '/ger/', $map['current']['entry'] );
        $winners = array_filter( $this->rule( $map, 'uri' )['entries'], function ( $e ) { return !empty( $e['wins'] ); } );
        $this->assertSame( array( 'ger' ), array_values( array_column( $winners, 'siteaccess' ) ) );
        $this->assertTrue( $this->row( $map, 'ger' )['is_current'] );
    }

    /** SA-05 */
    public function testEachSiteaccessListsWhatReachesIt()
    {
        $map = expSiteAccessOverview::build( $this->settings( array( 'MatchOrder' => 'host' ) ), $this->siteaccesses(), array() );
        $this->assertSame( array( array( 'probe' => 'host', 'match' => 'admin.example.org' ) ), $this->row( $map, 'admin' )['matched_by'] );
        $this->assertTrue( $this->row( $map, 'site' )['reachable'] );
        $this->assertFalse( $this->row( $map, 'classic' )['reachable'], 'no host maps classic and it is not the default' );
        $this->assertTrue( $this->row( $map, 'site' )['is_default'] );
    }

    /** SA-06 */
    public function testKind()
    {
        $map = expSiteAccessOverview::build( $this->settings(), $this->siteaccesses(), array() );
        $this->assertSame( 'site', $this->row( $map, 'site' )['kind'] );
        $this->assertSame( 'admin', $this->row( $map, 'admin' )['kind'] );
        $this->assertSame( 'editor', $this->row( $map, 'editor' )['kind'] );
        $this->assertSame( 'admin', expSiteAccessOverview::kindOf( array( 'site_design' => 'adminui', 'additional_designs' => array( 'admin4l' ), 'require_user_login' => 'true' ) ) );
        $this->assertSame( 'admin', expSiteAccessOverview::kindOf( array( 'site_design' => 'backoffice', 'additional_designs' => array( 'admin4' ), 'require_user_login' => 'true' ) ) );
        $this->assertSame( 'login', expSiteAccessOverview::kindOf( array( 'site_design' => 'intranet', 'require_user_login' => 'true' ) ) );
        $this->assertSame( 'shop', expSiteAccessOverview::kindOf( array( 'site_design' => 'admin4', 'explicit_type' => 'Shop' ) ) );
        $this->assertSame( 'admin', expSiteAccessOverview::kindOf( array( 'site_design' => 'admin4', 'explicit_type' => '<b>' ) ), 'an unsafe type is ignored' );
    }

    /** SA-07 */
    public function testLinks()
    {
        $map = expSiteAccessOverview::build( $this->settings( array( 'RelatedSiteAccessList' => array( 'archive', '../etc' ) ) ), $this->siteaccesses(), array() );
        $this->assertSame( 'https://admin.example.org/', $this->row( $map, 'admin' )['url'] );
        $this->assertSame( 'settings/view/admin/site.ini', $this->row( $map, 'admin' )['settings_path'] );
        $archive = $this->row( $map, 'archive' );
        $this->assertFalse( $archive['available'] );
        $this->assertSame( '', $archive['url'], 'a related-only siteaccess cannot be opened' );
        $this->assertSame( '', $this->row( $map, '../etc' )['settings_path'] );
        $this->assertFalse( expSiteAccessOverview::isPlainName( '../etc' ) );
        $this->assertTrue( expSiteAccessOverview::isPlainName( 'bold_ger' ) );
    }

    /** SA-08 */
    public function testNoneAndStatic()
    {
        $map = expSiteAccessOverview::build( $this->settings( array( 'MatchOrder' => 'none', 'StaticMatch' => 'admin' ) ), array(), array( 'name' => 'admin', 'type' => 6 ) );
        $this->assertSame( array(), $map['rules'] );
        $this->assertSame( array( 'none' ), $map['match_order'] );
        $this->assertSame( 'admin', $map['static'] );
        $this->assertSame( 'static', $map['current']['probe'] );
        $this->assertTrue( $this->row( $map, 'admin' )['is_static'] );
        $this->assertSame( array( 'uri', 'host' ), expSiteAccessOverview::matchOrder( ' uri ;; host' ) );
        $this->assertSame( array( 'uri', 'host' ), expSiteAccessOverview::matchOrder( array( 'uri', 'host' ) ) );
    }

    /** SA-09 */
    public function testTemplateViewAndReportUseTheMap()
    {
        $root = __DIR__ . '/../../../..';
        foreach ( array( 'design/admin', 'design/admin4' ) as $design )
        {
            $tpl = file_get_contents( "$root/$design/templates/setup/info.tpl" );
            $this->assertStringContainsString( 'id="exp-sysinfo-siteaccesses"', $tpl, $design );
            $start = strpos( $tpl, '{* Siteaccesses (#552)' );
            $block = substr( $tpl, $start, strpos( $tpl, '{* The opcode cache and APCu' ) - $start );
            $this->assertDoesNotMatchRegularExpression( '/<style|\sstyle="/i', $block, $design );
            $this->assertStringContainsString( '<details class="a4-fold a4-setup-sa"', $block );
        }
        $this->assertStringContainsString( 'expSiteAccessOverview::gather()', file_get_contents( "$root/kernel/private/classes/views/setup/info.php" ) );
        $this->assertStringContainsString( "'siteaccess_count'", file_get_contents( "$root/kernel/classes/expsystemreport.php" ) );
        $this->assertFileEquals( "$root/design/admin4/stylesheets/modules/setup.css", "$root/design/admin/stylesheets/modules/setup.css" );
    }
}
