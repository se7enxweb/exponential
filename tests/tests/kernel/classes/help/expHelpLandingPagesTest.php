<?php
/**
 * The landing pages of the help's audiences (exphelp/audience/<audience>), the commit log of the developers' page
 * and the Support Exponential block (doc/en/us/features/6.0/help-system.md, "Landing pages"). No database, no network.
 *
 *  LP-01  expHelpLanding builds a page from the index: start page and lists from the settings, missing or hidden
 *         pages left out, automatic lists from featured and help pages, a search button per category, related
 *         audiences, the configured blocks; an unknown audience gives null
 *  LP-02  expHelpCommitFeed parses an Atom feed into plain-text commits: markup and scripts are text, links are built
 *         from the hash, entries without a hash are skipped, a DOCTYPE (entities) is refused, no avatars are kept
 *  LP-03  the feed is read at most once per CacheTTL, merged into the cached list (newest first, no duplicates,
 *         bounded), and a failed read keeps the cached commits, marked stale, and is retried only after RetryAfter
 *  LP-04  paging and the merge-commit filter
 *  LP-05  settings: every audience has a [Landing_<audience>] group, the developers' page lists the commit log, the
 *         feed and the sponsor addresses are the verified https addresses
 *  LP-06  module and templates: the audience view with the read function; audience.tpl and its parts are the same in
 *         admin4 and the classic admin, have no inline CSS or script, external links open with rel="noopener
 *         noreferrer"; the dashboard cards open the landing page and the dashboard shows the support block
 *  LP-07  every new string is in the complete catalogues (twenty-one) under kernel/exphelp
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpLandingPagesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

foreach ( array( 'expHelpDocumentIndex' => 'exphelpdocumentindex.php', 'expHelpLanding' => 'exphelplanding.php',
                 'expHelpCommitFeed' => 'exphelpcommitfeed.php' ) as $helpClass => $helpFile )
    if ( !class_exists( $helpClass ) )
        require_once dirname( __DIR__, 5 ) . '/kernel/classes/help/' . $helpFile;
unset( $helpClass, $helpFile );

/** An index with fixed pages, for expHelpLanding */
class expHelpLandingTestIndex extends expHelpDocumentIndex
{
    public $docs = array();
    public $recent = array();

    public function __construct( array $docs, array $recent = array() )
    {
        foreach ( $docs as $id => $doc )
            $this->docs[$id] = $doc + array( 'id' => $id, 'summary' => '', 'featured' => false, 'hidden' => false,
                                             'order' => 1000, 'type' => 'help', 'category' => 'getting-started' );
        $this->recent = $recent;
    }
    public function documents() { return $this->docs; }
    public function visibleDocuments() { return array_filter( $this->docs, function ( $d ) { return empty( $d['hidden'] ); } ); }
    public function document( $id ) { return isset( $this->docs[$id] ) ? $this->docs[$id] : null; }
    public function audiences()
    {
        $list = array();
        foreach ( array( 'developer' => 'Developers', 'sponsor' => 'Sponsors', 'partner' => 'Partners' ) as $id => $name )
            $list[$id] = array( 'id' => $id, 'name' => $name, 'icon' => 'star', 'count' => 0 );
        return $list;
    }
    public function categories()
    {
        return array( 'getting-started' => array( 'id' => 'getting-started', 'name' => 'Getting started', 'icon' => 'signpost-split', 'count' => 0 ),
                      'sponsorship' => array( 'id' => 'sponsorship', 'name' => 'Sponsorship', 'icon' => 'piggy-bank', 'count' => 0 ),
                      'development' => array( 'id' => 'development', 'name' => 'Development', 'icon' => 'code-square', 'count' => 0 ) );
    }
    public function recentChanges( $limit = 10 ) { return array_slice( $this->recent, 0, $limit ); }
}

class expHelpLandingPagesTest extends PHPUnit\Framework\TestCase
{
    const CATALOGUES = array( 'ara-SA', 'cro-HR', 'cze-CZ', 'dan-DK', 'dut-NL', 'eng-US', 'esl-ES', 'esl-MX', 'fre-FR', 'ger-DE',
                              'heb-IL', 'ita-IT', 'lat-VA', 'nor-NO', 'pol-PL', 'por-BR', 'swe-SE', 'tlh-QO', 'tur-TR', 'ukr-UA',
                              'untranslated' );

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function read( $path )
    {
        $file = self::root() . '/' . $path;
        return is_file( $file ) ? (string)file_get_contents( $file ) : '';
    }

    private static function iniSection( $section )
    {
        $ini = self::read( 'settings/exphelp.ini' );
        $start = strpos( $ini, "\n[" . $section . "]" );
        if ( $start === false )
            return '';
        $end = strpos( $ini, "\n[", $start + 1 );
        return substr( $ini, $start, $end === false ? null : $end - $start );
    }

    private static function settings( array $values )
    {
        return function ( $group, $name, $default ) use ( $values ) {
            return isset( $values[$group][$name] ) ? $values[$group][$name] : $default;
        };
    }

    private function scratch()
    {
        $dir = self::root() . '/var/tmp/phpunit-exphelp-landing-' . getmypid() . '-' . mt_rand();
        mkdir( $dir, 0777, true );
        return $dir;
    }

    private static function removeDir( $dir )
    {
        foreach ( (array)glob( $dir . '/{,.}[!.]*', GLOB_BRACE ) as $file )
            is_dir( $file ) ? self::removeDir( $file ) : unlink( $file );
        @rmdir( $dir );
    }

    private static function feed( array $entries )
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
             . '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/"><title>Recent Commits</title>';
        foreach ( $entries as $e )
            $xml .= '<entry><id>tag:github.com,2008:Grit::Commit/' . $e[0] . '</id>'
                  . '<link type="text/html" rel="alternate" href="' . ( isset( $e[4] ) ? $e[4] : 'https://github.com/x/y/commit/' . $e[0] ) . '"/>'
                  . '<title>' . $e[1] . '</title><updated>' . $e[2] . '</updated>'
                  . '<media:thumbnail height="30" width="30" url="https://avatars.example.invalid/u/1"/>'
                  . '<author><name>' . $e[3] . '</name></author>'
                  . '<content type="html">&lt;pre style=&#39;x&#39;&gt;' . $e[1] . '&lt;/pre&gt;</content></entry>';
        return $xml . '</feed>';
    }

    /** LP-01 */
    public function testLandingFromIndex()
    {
        $index = new expHelpLandingTestIndex( array(
            'help/sponsors/start.md' => array( 'title' => 'Sponsors: start here', 'audience' => array( 'sponsor' ), 'featured' => true, 'order' => 1, 'category' => 'sponsorship', 'summary' => 'Begin.' ),
            'help/sponsors/why.md' => array( 'title' => 'Why sponsor', 'audience' => array( 'sponsor' ), 'order' => 2, 'category' => 'sponsorship', 'summary' => 'Reasons.' ),
            'help/sponsors/hidden.md' => array( 'title' => 'Hidden', 'audience' => array( 'sponsor' ), 'hidden' => true ),
            'help/sponsors/tax.md' => array( 'title' => 'Tax', 'audience' => array( 'sponsor', 'partner' ), 'order' => 3, 'category' => 'sponsorship', 'summary' => 'Receipts.' ),
            'changelogs/x.md' => array( 'title' => 'Changes', 'audience' => array( 'sponsor' ), 'type' => 'changelog', 'category' => 'development' ),
            'guides/dev.md' => array( 'title' => 'Dev guide', 'audience' => array( 'developer' ), 'type' => 'guide', 'category' => 'development' ),
        ), array( array( 'id' => 'help/sponsors/tax.md', 'title' => 'Tax', 'time' => 2, 'subject' => 'Added: tax' ),
                  array( 'id' => 'guides/dev.md', 'title' => 'Dev guide', 'time' => 1, 'subject' => 'x' ) ) );
        $landing = new expHelpLanding( $index, self::settings( array(
            'Landing_sponsor' => array( 'StartPage' => 'help/sponsors/missing.md', 'Related' => array( 'partner', 'sponsor', 'nobody' ),
                                        'KeyPages' => array( 'help/sponsors/hidden.md', 'help/sponsors/tax.md', 'help/sponsors/missing.md' ),
                                        'Blocks' => array( 'hero', 'support', 'start', 'bogus' ) ),
        ) ) );
        $this->assertNull( $landing->build( 'nobody' ) );
        $this->assertNull( $landing->build( '../etc' ) );

        $page = $landing->build( 'sponsor' );
        $this->assertSame( 'sponsor', $page['id'] );
        $this->assertSame( 4, $page['count'], 'hidden pages are not counted' );
        $this->assertSame( array( 'hero', 'support', 'start' ), $page['blocks'], 'unknown blocks are dropped' );
        $this->assertSame( 'help/sponsors/start.md', $page['start']['id'], 'a missing start page falls back to the first featured help page' );
        $this->assertSame( array( 'help/sponsors/start.md', 'help/sponsors/why.md', 'help/sponsors/tax.md' ), array_column( $page['reading'], 'id' ),
                           'the reading path: the start page, then help pages in order; no changelog' );
        $this->assertSame( array( 'help/sponsors/tax.md' ), array_column( $page['key'], 'id' ), 'configured key pages: hidden and missing ones left out' );
        $this->assertSame( array( 'help/sponsors/tax.md' ), array_column( $page['latest'], 'id' ) );
        $this->assertSame( array( 'partner' ), array_column( $page['related'], 'id' ), 'related: itself and unknown ones left out' );
        $this->assertSame( '/exphelp/audience/partner', $page['related'][0]['url'] );
        $this->assertSame( array( 'sponsorship', 'development' ), array_column( $page['categories'], 'id' ) );
        $this->assertSame( 3, $page['categories'][0]['count'] );
        if ( class_exists( 'expHelp' ) )
        {
            $this->assertSame( '/exphelp/search/(category)/sponsorship/(audience)/sponsor', $page['categories'][0]['url'] );
            $this->assertSame( '/exphelp/search/(audience)/sponsor', $page['search_url'] );
        }
        $this->assertNotSame( '', $page['summary'] );

        $default = ( new expHelpLanding( $index, self::settings( array() ) ) )->build( 'developer' );
        $this->assertSame( expHelpLanding::DEFAULT_BLOCKS, $default['blocks'] );
        $this->assertSame( '/exphelp/audience/app-creator', expHelpLanding::url( 'app-creator' ) );
    }

    /** LP-02 */
    public function testFeedParsing()
    {
        $hash = str_repeat( 'a1', 20 );
        $feed = new expHelpCommitFeed( array( 'cacheDir' => null, 'commitBase' => 'https://github.com/se7enxweb/exponential/commit/' ) );
        $commits = $feed->parse( self::feed( array(
            array( $hash, 'Added: &lt;script&gt;alert(1)&lt;/script&gt; &amp; more', '2026-10-18T10:00:00Z', 'se7enxweb', 'javascript:alert(1)' ),
            array( 'not-a-hash', 'No hash', '2026-10-18T09:00:00Z', 'x' ),
        ) ) );
        $this->assertCount( 1, $commits );
        $c = $commits[0];
        $this->assertSame( $hash, $c['hash'] );
        $this->assertSame( substr( $hash, 0, 10 ), $c['short'] );
        $this->assertSame( 'https://github.com/se7enxweb/exponential/commit/' . $hash, $c['url'], 'the link is built from the hash, never taken from the feed' );
        $this->assertStringNotContainsString( '<', $c['title'], 'markup is removed: the template escapes the rest' );
        $this->assertStringContainsString( '& more', $c['title'] );
        $this->assertSame( 'se7enxweb', $c['author'] );
        $this->assertSame( strtotime( '2026-10-18T10:00:00Z' ), $c['time'] );
        $this->assertStringNotContainsString( 'avatars', serialize( $commits ), 'no remote images are kept' );
        $this->assertStringNotContainsString( '<pre', $c['message'] );

        $this->assertNull( $feed->parse( '<?xml version="1.0"?><!DOCTYPE feed [<!ENTITY x SYSTEM "file:///etc/passwd">]><feed xmlns="http://www.w3.org/2005/Atom"><entry><title>&x;</title></entry></feed>' ) );
        $this->assertNull( $feed->parse( '<html><body>not a feed</body></html>' ) );
        $this->assertNull( $feed->parse( 'garbage' ) );
        $this->assertSame( str_repeat( 'x', 9 ) . '…', expHelpCommitFeed::plain( str_repeat( 'x', 50 ), 10, true ) );
        $this->assertSame( 'a b', expHelpCommitFeed::plain( "a\x07\n  b", 10, true ) );
    }

    /** LP-03 */
    public function testCacheMergeAndOffline()
    {
        $dir = $this->scratch();
        try
        {
            $calls = 0;
            $body = self::feed( array( array( str_repeat( 'b', 40 ), 'Second', '2026-10-18T11:00:00Z', 'a' ),
                                       array( str_repeat( 'a', 40 ), 'First', '2026-10-18T10:00:00Z', 'a' ) ) );
            $fetch = function () use ( &$calls, &$body ) { ++$calls; return $body; };
            $options = array( 'cacheDir' => $dir, 'ttl' => 600, 'retry' => 120, 'max' => 3, 'enabled' => true );
            $now = 1000000;

            $first = ( new expHelpCommitFeed( $options, $fetch ) )->commits( $now );
            $this->assertSame( 1, $calls );
            $this->assertSame( array( 'Second', 'First' ), array_column( $first['commits'], 'title' ) );
            $this->assertFalse( $first['stale'] );
            ( new expHelpCommitFeed( $options, $fetch ) )->commits( $now + 300 );
            $this->assertSame( 1, $calls, 'within CacheTTL the cache answers' );

            // a newer feed is merged in front, duplicates once, at most "max"
            $body = self::feed( array( array( str_repeat( 'd', 40 ), 'Fourth', '2026-10-18T13:00:00Z', 'a' ),
                                       array( str_repeat( 'c', 40 ), 'Third', '2026-10-18T12:00:00Z', 'a' ),
                                       array( str_repeat( 'b', 40 ), 'Second', '2026-10-18T11:00:00Z', 'a' ) ) );
            $merged = ( new expHelpCommitFeed( $options, $fetch ) )->commits( $now + 700 );
            $this->assertSame( 2, $calls );
            $this->assertSame( array( 'Fourth', 'Third', 'Second' ), array_column( $merged['commits'], 'title' ) );

            // offline: the cached commits stay, marked stale; no new read before RetryAfter
            $body = false;
            $offline = ( new expHelpCommitFeed( $options, $fetch ) )->commits( $now + 1400 );
            $this->assertSame( 3, $calls );
            $this->assertTrue( $offline['stale'] );
            $this->assertSame( 'unreachable', $offline['error'] );
            $this->assertCount( 3, $offline['commits'] );
            $this->assertSame( $now + 700, $offline['fetched'] );
            ( new expHelpCommitFeed( $options, $fetch ) )->commits( $now + 1450 );
            $this->assertSame( 3, $calls, 'a failed read is not repeated before RetryAfter' );
            ( new expHelpCommitFeed( $options, $fetch ) )->commits( $now + 1600 );
            $this->assertSame( 4, $calls );

            // not a feed: unreadable, cache kept
            $body = '<html>rate limited</html>';
            $bad = ( new expHelpCommitFeed( $options, $fetch ) )->commits( $now + 1800 );
            $this->assertSame( 'unreadable', $bad['error'] );
            $this->assertCount( 3, $bad['commits'] );

            // switched off: no read at all
            $off = ( new expHelpCommitFeed( array( 'enabled' => false ) + $options, $fetch ) )->commits( $now + 99999 );
            $this->assertSame( 5, $calls );
            $this->assertCount( 3, $off['commits'] );
        }
        finally
        {
            self::removeDir( $dir );
        }
    }

    /** LP-04 */
    public function testPagingAndMerges()
    {
        $p = expHelpCommitFeed::paging( 25, 13, 10 );
        $this->assertSame( array( 10, 2, 3, 11, 20, 0, 20 ), array( $p['offset'], $p['page'], $p['page_count'], $p['from'], $p['to'], $p['previous_offset'], $p['next_offset'] ) );
        $p = expHelpCommitFeed::paging( 25, 999, 10 );
        $this->assertSame( array( 20, 3, false ), array( $p['offset'], $p['page'], $p['next_offset'] ) );
        $p = expHelpCommitFeed::paging( 0, 0, 10 );
        $this->assertSame( array( 0, 0, 1 ), array( $p['from'], $p['to'], $p['page_count'] ) );
        $this->assertTrue( expHelpCommitFeed::isMerge( "Merge remote-tracking branch 'origin/main' into w612-Y1" ) );
        $this->assertTrue( expHelpCommitFeed::isMerge( 'Merge pull request #12 from x/y' ) );
        $this->assertFalse( expHelpCommitFeed::isMerge( 'Added: Merge fields to the editor' ) );

        $dir = $this->scratch();
        try
        {
            $entries = array();
            for ( $i = 0; $i < 12; ++$i )
                $entries[] = array( sprintf( '%040x', $i + 1 ), $i % 3 === 0 ? 'Merge branch x' : 'Added: thing ' . $i, sprintf( '2026-10-18T10:%02d:00Z', 59 - $i ), 'a' );
            $fetch = function () use ( $entries ) { return self::feed( $entries ); };
            $feed = new expHelpCommitFeed( array( 'cacheDir' => $dir, 'pageSize' => 5, 'enabled' => true ), $fetch );
            $page = $feed->page( 5, null, false, 1000 );
            $this->assertSame( 8, $page['total'], 'merges left out' );
            $this->assertCount( 3, $page['commits'] );
            $this->assertSame( 2, $page['paging']['page'] );
            $all = $feed->page( 0, null, true, 1000 );
            $this->assertSame( 12, $all['total'] );
            $this->assertCount( 5, $all['commits'] );
        }
        finally
        {
            self::removeDir( $dir );
        }
    }

    /** LP-05 */
    public function testSettings()
    {
        $audiences = array();
        preg_match_all( '/^Audiences\[\]=([a-z-]+)\s*$/m', self::iniSection( 'AudienceSettings' ), $m );
        $this->assertGreaterThanOrEqual( 14, count( $m[1] ) );
        foreach ( $m[1] as $id )
        {
            $group = self::iniSection( 'Landing_' . $id );
            $this->assertNotSame( '', $group, 'landing settings for ' . $id );
            $this->assertMatchesRegularExpression( '#^StartPage=help/[a-z0-9/._-]+\.md\s*$#m', $group, $id );
            preg_match_all( '/^Related\[\]=([a-z-]+)\s*$/m', $group, $r );
            foreach ( $r[1] as $rel )
                $this->assertContains( $rel, $m[1], $id . ' relates to a known audience' );
        }
        $this->assertMatchesRegularExpression( '/^Blocks\[\]=commits\s*$/m', self::iniSection( 'Landing_developer' ) );
        $this->assertMatchesRegularExpression( '/^Blocks\[\]=support\s*$/m', self::iniSection( 'Landing_sponsor' ) );
        // the customers (site owners and their teams) start at their own page and point to editors, admins and partners
        $this->assertContains( 'customer', $m[1] );
        $customer = self::iniSection( 'Landing_customer' );
        $this->assertMatchesRegularExpression( '#^StartPage=help/customers/customers-start-here\.md\s*$#m', $customer );
        foreach ( array( 'editor', 'admin', 'hosting', 'partner' ) as $rel )
            $this->assertMatchesRegularExpression( '/^Related\[\]=' . $rel . '\s*$/m', $customer );
        $this->assertSame( '/exphelp/audience/customer', expHelpLanding::url( 'customer' ) );
        $feed = self::iniSection( 'CommitFeedSettings' );
        $this->assertStringContainsString( "\nFeedURL=https://github.com/se7enxweb/exponential/commits/main.atom\n", $feed );
        $this->assertStringContainsString( "\nCacheTTL=600\n", $feed );
        $this->assertStringContainsString( "\nURL=https://github.com/sponsors/se7enxweb\n", self::iniSection( 'SupportOrganization_7x' ) );
        $this->assertStringContainsString( "\nURL=https://github.com/sponsors/exponentialcms\n", self::iniSection( 'SupportOrganization_foundation' ) );
        $this->assertMatchesRegularExpression( '/^Organizations\[\]=7x\s*$/m', self::iniSection( 'SupportSettings' ) );
        $this->assertMatchesRegularExpression( '/^Organizations\[\]=foundation\s*$/m', self::iniSection( 'SupportSettings' ) );
    }

    /** LP-06 */
    public function testModuleAndTemplates()
    {
        $module = self::read( 'kernel/exphelp/module.php' );
        $this->assertMatchesRegularExpression( "#\\\$ViewList\\['audience'\\] = array\\(\\s*'default_navigation_part' => 'ezsetupnavigationpart',\\s*'script' => 'audience.php',\\s*'functions' => array\\( 'read' \\),\\s*'params' => array\\( 'Audience' \\)#", $module );
        $this->assertFileExists( self::root() . '/kernel/exphelp/audience.php' );
        foreach ( array( 'audience.tpl', 'parts/landing_commits.tpl', 'parts/support.tpl', 'dashboard.tpl' ) as $file )
        {
            $tpl = self::read( 'design/admin4/templates/exphelp/' . $file );
            $this->assertNotSame( '', $tpl, $file );
            $this->assertSame( $tpl, self::read( 'design/admin/templates/exphelp/' . $file ), $file . ' is the same in both designs' );
            $this->assertDoesNotMatchRegularExpression( '/<style|\sstyle\s*=|<script|\son[a-z]+\s*=/i', $tpl, $file );
            $this->assertDoesNotMatchRegularExpression( '/<img\b/i', $tpl, $file . ': no images (no remote avatars)' );
            preg_match_all( '/<a\b[^>]*target="_blank"[^>]*>/', $tpl, $links );
            foreach ( $links[0] as $link )
                $this->assertStringContainsString( 'noopener', $link, $file );
        }
        $commits = self::read( 'design/admin4/templates/exphelp/parts/landing_commits.tpl' );
        $this->assertStringContainsString( '{$commit.title|wash}', $commits );
        $this->assertStringContainsString( '{$commit.author|wash}', $commits );
        $this->assertStringContainsString( '{$commit.message|wash}', $commits );
        $this->assertStringContainsString( "uri='design:navigator/google.tpl'", $commits, 'the shared pager' );
        $dashboard = self::read( 'design/admin4/templates/exphelp/dashboard.tpl' );
        $this->assertStringContainsString( "concat( '/exphelp/audience/', \$aid )", $dashboard );
        $this->assertStringContainsString( "{include uri='design:exphelp/parts/support.tpl' support=\$help_support}", $dashboard );
        $this->assertStringContainsString( "'help_support'", self::read( 'kernel/private/classes/views/exphelp/dashboard.php' ) );
        $grow = self::read( 'design/admin4/templates/exphelp/parts/dashboard_grow.tpl' );
        $this->assertSame( $grow, self::read( 'design/admin/templates/exphelp/parts/dashboard_grow.tpl' ) );
        $this->assertStringContainsString( "{'Contribute extensions back'|i18n( \$help_grow_ctx )}", $grow );
        $this->assertStringContainsString( "\$help_grow_extensions_page|ne( '' )", $grow, 'shown only when its page is in the help' );
        $this->assertStringContainsString( "'help/contribute-extensions-back.md'", self::read( 'kernel/private/classes/views/exphelp/dashboard.php' ) );
        $css = self::read( 'design/admin4/stylesheets/modules/help.css' );
        foreach ( array( '.a4-help-landing-hero {', '.a4-help-commits-list {', '.a4-help-support {' ) as $selector )
            $this->assertStringContainsString( $selector, $css );
    }

    /** LP-07 */
    public function testStringsTranslated()
    {
        $sources = array();
        foreach ( array( 'audience.tpl', 'parts/landing_commits.tpl', 'parts/support.tpl' ) as $file )
        {
            preg_match_all( "/\\{'((?:[^'\\\\]|\\\\.)+)'\\|i18n\\(/", self::read( 'design/admin4/templates/exphelp/' . $file ), $m );
            foreach ( $m[1] as $s )
                $sources[] = stripslashes( $s );
        }
        foreach ( array( 'SupportSettings', 'SupportOrganization_7x', 'SupportOrganization_foundation' ) as $section )
        {
            preg_match_all( '/^(?:Title|Text|Button|Mission|Funds\[\])=(.+)$/m', self::iniSection( $section ), $m );
            foreach ( $m[1] as $s )
                $sources[] = trim( $s );
        }
        $sources = array_values( array_unique( $sources ) );
        $this->assertGreaterThan( 30, count( $sources ) );
        foreach ( self::CATALOGUES as $lang )
        {
            $ts = self::read( 'share/translations/' . $lang . '/translation.ts' );
            $start = strpos( $ts, '<name>kernel/exphelp</name>' );
            $this->assertNotFalse( $start, $lang );
            $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
            foreach ( $sources as $source )
            {
                $pattern = '#<source>' . preg_quote( htmlspecialchars( $source, ENT_NOQUOTES | ENT_XML1 ), '#' ) . '</source>\s*<translation'
                         . ( $lang === 'untranslated' ? '[^>]*>' : '>[^<]+' ) . '</translation>#';
                $this->assertMatchesRegularExpression( $pattern, $context, $lang . ': ' . $source );
            }
        }
    }
}
