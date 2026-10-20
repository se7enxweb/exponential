<?php
/**
 * The "Develop with Git" and "Update with Git" help pages and the "Grow from user to developer" band of the help dashboard
 * (doc/en/us/help/develop-with-git.md, design/<admin4|admin>/templates/exphelp/parts/dashboard_grow.tpl). No database.
 *
 *  HG-01  the page exists with its front matter: audiences user and developer, category development, featured
 *  HG-02  the help serves it: the allowlist of the real installation allows it (a git-tracked file below doc/)
 *  HG-03  the dashboard features it ([FeaturedSettings] PageList in settings/exphelp.ini)
 *  HG-04  every relative link of the page leads to an existing file, and every #anchor to a heading of its page
 *  HG-05  the dashboard part exists in admin4 and the classic admin, the same file in both, included by both
 *         dashboards, linking the page and the installation chapter, with no inline CSS or script
 *  HG-06  every string of the part is in the eng-US and ger-DE catalogues under kernel/exphelp
 *  HG-07  the commands the page recommends are the ones that were verified (clone URL, preferred-install, keep-vcs)
 *  HG-08  help/update-with-git.md: front matter, the verified update commands per schedule, linked from the band, served
 *  HG-09  command line or administration: the decision box first, then Track A and Track B with the same seven steps in
 *         order, screenshots in both (webp, alt text), the views, policies and checklist of Track B, the safe order of
 *         mixing them, and no host name, server path or credential
 *  HG-10  Track A: the verified commands, each step with its terminal capture
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpDevelopWithGitTest extends PHPUnit\Framework\TestCase
{
    const PAGE = 'doc/en/us/help/develop-with-git.md';
    const PART = 'templates/exphelp/parts/dashboard_grow.tpl';
    const UPDATE_PAGE = 'doc/en/us/help/update-with-git.md';

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function read( $path )
    {
        $file = self::root() . '/' . $path;
        return is_file( $file ) ? (string)file_get_contents( $file ) : '';
    }

    /** HG-01 */
    public function testPageAndFrontMatter()
    {
        $text = self::read( self::PAGE );
        $this->assertNotSame( '', $text, self::PAGE . ' exists' );
        list( $meta, $body ) = expHelpFrontMatter::split( $text );
        $audience = is_array( $meta['audience'] ) ? $meta['audience'] : array_map( 'trim', explode( ',', (string)$meta['audience'] ) );
        $this->assertContains( 'user', $audience );
        $this->assertContains( 'developer', $audience );
        $this->assertSame( 'development', $meta['category'] );
        $this->assertTrue( $meta['featured'] === true || $meta['featured'] === 'true' );
        $this->assertStringStartsWith( '# Develop with Git', ltrim( $body ) );
        $this->assertStringNotContainsStringIgnoringCase( 'ez publish', $text );
    }

    /** HG-02 */
    public function testServedByTheAllowlist()
    {
        $out = array();
        $rc = 1;
        @exec( 'git -C ' . escapeshellarg( self::root() ) . ' ls-files -- ' . escapeshellarg( self::PAGE ) . ' 2>/dev/null', $out, $rc );
        if ( $rc !== 0 )
            $this->markTestSkipped( 'not a git checkout' );
        $this->assertSame( array( self::PAGE ), $out, self::PAGE . ' is tracked' );
        $allow = new expHelpAllowlist( array( 'root' => self::root() ) );
        $this->assertTrue( $allow->allows( self::PAGE ) );
        $this->assertContains( self::PAGE, $allow->files() );
    }

    /** HG-03 */
    public function testFeaturedOnTheDashboard()
    {
        $ini = self::read( 'settings/exphelp.ini' );
        $start = strpos( $ini, "\n[FeaturedSettings]" );
        $this->assertNotFalse( $start );
        $end = strpos( $ini, "\n[", $start + 1 );
        $section = substr( $ini, $start, $end === false ? null : $end - $start );
        $this->assertMatchesRegularExpression( '/^PageList\[\]=help\/develop-with-git\.md\s*$/m', $section );
    }

    /**
     * The anchors of a Markdown file's headings, as the renderer makes them (code fences skipped)
     */
    private static function anchors( $text )
    {
        list( , $body ) = expHelpFrontMatter::split( $text );
        $anchors = array();
        $used = array();
        $fence = false;
        foreach ( preg_split( '/\r?\n/', $body ) as $line )
        {
            if ( preg_match( '/^\s*(```|~~~)/', $line ) )
            {
                $fence = !$fence;
                continue;
            }
            if ( !$fence && preg_match( '/^#{1,6}\s+(.+?)\s*#*\s*$/', $line, $m ) )
                $anchors[] = expHelpMarkdownRenderer::slug( $m[1], $used );
        }
        return $anchors;
    }

    /** HG-04 */
    public function testLinksAndAnchors()
    {
        $this->assertGreaterThan( 10, $this->checkLinks( self::PAGE ) );
        $this->assertGreaterThan( 8, $this->checkLinks( self::UPDATE_PAGE ) );
    }

    private function checkLinks( $page )
    {
        $text = self::read( $page );
        preg_match_all( '/\]\(([^)\s]+)\)/', $text, $m );
        $this->assertNotEmpty( $m[1] );
        $checked = 0;
        foreach ( $m[1] as $href )
        {
            if ( preg_match( '#^[a-z]+:#i', $href ) )
            {
                $this->assertStringStartsWith( 'https://', $href, 'external links use https' );
                continue;
            }
            $parts = explode( '#', $href, 2 );
            $target = $parts[0] === '' ? $page : self::normalize( dirname( $page ) . '/' . $parts[0] );
            // a help page, or a project file of the root that the help shows too (CONTRIBUTING.md)
            $this->assertMatchesRegularExpression( '#^(doc/|[A-Za-z0-9_-]+\.md$)#', $target, $href );
            $this->assertFileExists( self::root() . '/' . $target, $href );
            if ( isset( $parts[1] ) && $parts[1] !== '' )
                $this->assertContains( $parts[1], self::anchors( self::read( $target ) ), $href . ': no such heading' );
            ++$checked;
        }
        return $checked;
    }

    private static function normalize( $path )
    {
        $out = array();
        foreach ( explode( '/', $path ) as $segment )
        {
            if ( $segment === '..' )
                array_pop( $out );
            elseif ( $segment !== '.' && $segment !== '' )
                $out[] = $segment;
        }
        return implode( '/', $out );
    }

    /** HG-05 */
    public function testDashboardPart()
    {
        $admin4 = self::read( 'design/admin4/' . self::PART );
        $this->assertNotSame( '', $admin4 );
        $this->assertSame( $admin4, self::read( 'design/admin/' . self::PART ), 'the classic admin has the same part' );
        foreach ( array( 'admin4', 'admin' ) as $design )
            $this->assertStringContainsString( "{include uri='design:exphelp/parts/dashboard_grow.tpl'}",
                self::read( 'design/' . $design . '/templates/exphelp/dashboard.tpl' ), $design );
        $this->assertStringContainsString( "'/exphelp/viewer/help/develop-with-git.md'|ezurl", $admin4 );
        $this->assertStringContainsString( "'/exphelp/viewer/install/03-getting-the-code.md'|ezurl", $admin4 );
        $this->assertFileExists( self::root() . '/doc/en/us/install/03-getting-the-code.md' );
        $this->assertDoesNotMatchRegularExpression( '/<style|style\s*=|<script|\son[a-z]+\s*=/i', $admin4 );
        foreach ( array( 'admin4', 'admin' ) as $design )
            $this->assertStringContainsString( '.a4-help-grow {', self::read( 'design/' . $design . '/stylesheets/modules/help.css' ) );
    }

    /** HG-06 */
    public function testStringsTranslated()
    {
        preg_match_all( "/\\{'((?:[^'\\\\]|\\\\.)+)'\\|i18n\\( \\\$help_grow_ctx \\)/", self::read( 'design/admin4/' . self::PART ), $m );
        $this->assertCount( 14, $m[1] );
        foreach ( array( 'eng-US', 'ger-DE' ) as $lang )
        {
            $ts = self::read( 'share/translations/' . $lang . '/translation.ts' );
            $this->assertMatchesRegularExpression( '#<context>\s*<name>kernel/exphelp</name>#', $ts );
            $start = strpos( $ts, '<name>kernel/exphelp</name>' );
            $context = substr( $ts, $start, strpos( $ts, '</context>', $start ) - $start );
            foreach ( $m[1] as $source )
            {
                $source = htmlspecialchars( stripslashes( $source ), ENT_QUOTES | ENT_XML1 );
                $this->assertMatchesRegularExpression( '#<source>' . preg_quote( $source, '#' ) . '</source>\s*<translation>[^<]+</translation>#',
                    $context, $lang . ': ' . $source );
            }
        }
    }

    /** HG-08 */
    public function testUpdatePage()
    {
        $text = self::read( self::UPDATE_PAGE );
        $this->assertNotSame( '', $text, self::UPDATE_PAGE . ' exists' );
        list( $meta, $body ) = expHelpFrontMatter::split( $text );
        $this->assertSame( 'development', $meta['category'] );
        $this->assertStringStartsWith( '# Update with Git', ltrim( $body ) );
        foreach ( array(
            'git fetch origin --tags',
            'git merge origin/main',
            "git merge --no-edit \"\$(git tag -l 'v6.*' --sort=version:refname | tail -1)\"",
            'composer update',
            'git log --oneline ORIG_HEAD..HEAD',
            'exp:velocity deploy',
            'OnCalendar=daily',
            '15 4 * * *  EXP_SITE=/var/www/exponential /usr/local/bin/exp-update release',
        ) as $command )
            $this->assertStringContainsString( $command, $text );
        $this->assertStringContainsString( "'/exphelp/viewer/help/update-with-git.md'|ezurl", self::read( 'design/admin4/' . self::PART ) );
        $out = array();
        $rc = 1;
        @exec( 'git -C ' . escapeshellarg( self::root() ) . ' ls-files -- ' . escapeshellarg( self::UPDATE_PAGE ) . ' 2>/dev/null', $out, $rc );
        if ( $rc === 0 && $out )
            $this->assertTrue( ( new expHelpAllowlist( array( 'root' => self::root() ) ) )->allows( self::UPDATE_PAGE ) );
    }

    /**
     * The text of one "## " section of the update page (up to the next "## ")
     */
    private static function updateSection( $heading )
    {
        $text = self::read( self::UPDATE_PAGE );
        $start = strpos( $text, "\n## " . $heading . "\n" );
        if ( $start === false )
            return false;
        $end = strpos( $text, "\n## ", $start + 5 );
        return substr( $text, $start, $end === false ? null : $end - $start );
    }

    /**
     * Every image of a section: webp below doc/en/us/help/images, a describing alt text, on disk; returns how many
     */
    private function assertSectionImages( $section )
    {
        preg_match_all( '/!\[([^\]]*)\]\((images\/[^)]+)\)/', $section, $m, PREG_SET_ORDER );
        foreach ( $m as $image )
        {
            $this->assertGreaterThan( 40, strlen( $image[1] ), $image[2] . ' has a describing alt text' );
            $this->assertStringEndsWith( '.webp', $image[2] );
            $this->assertFileExists( self::root() . '/doc/en/us/help/' . $image[2] );
        }
        return count( $m );
    }

    /** HG-09 */
    public function testCommandLineOrAdministrationInTwoParallelTracks()
    {
        $decision = self::updateSection( 'Command line or administration?' );
        $this->assertNotFalse( $decision, 'the decision box comes first' );
        $text = self::read( self::UPDATE_PAGE );
        $this->assertLessThan( strpos( $text, '## Track A: the command line' ), strpos( $text, '## Command line or administration?' ) );
        foreach ( array( 'release tag', 'Git Manager', 'Updates and packages', 'Run it for real', 'DS Editor', 'restart Velocity' ) as $what )
            $this->assertStringContainsStringIgnoringCase( $what, $decision );
        $a = self::updateSection( 'Track A: the command line' );
        $b = self::updateSection( 'Track B: the administration' );
        $this->assertNotFalse( $a );
        $this->assertNotFalse( $b );
        // the same seven steps in both tracks, in order
        foreach ( array( 'A' => $a, 'B' => $b ) as $track => $section )
        {
            $last = 0;
            for ( $step = 1; $step <= 7; ++$step )
            {
                $at = strpos( $section, '### ' . $track . $step . '. ' );
                $this->assertNotFalse( $at, $track . $step );
                $this->assertGreaterThan( $last, $at, $track . $step . ' in order' );
                $last = $at;
            }
            $this->assertGreaterThanOrEqual( 6, $this->assertSectionImages( $section ), 'Track ' . $track . ' has screenshots' );
        }
        // Track B: the views and the policy a role needs, and the checklist
        foreach ( array( 'setup/maintenance', 'git_manager/dashboard', 'update/dashboard', 'setup/systemupgrade', 'setup/cache', 'setup/logs',
                         '`setup/administrate`', '`git_manager/git_manager`', '`update/ezupdate`', '`update/manage`',
                         '`setup/setup`', '`setup/managecache`', '`setup/logs`', '`dse/dse`' ) as $name )
            $this->assertStringContainsString( $name, $b );
        $this->assertGreaterThanOrEqual( 9, substr_count( $b, '- [ ] ' ), 'the checklist' );
        // mixing: the safe order
        $mixing = self::updateSection( 'Mixing the two tracks' );
        $this->assertNotFalse( $mixing );
        $this->assertStringContainsString( 'Git first, then Composer', $mixing );
        // no host name, server path or credential of the machines the pictures were made on
        $this->assertDoesNotMatchRegularExpression( '/alpha\.se7enx|vhosts|\/web\/vh|github-as-7x|w612p|publishing\$|sandbox3/i', $text );
    }

    /** HG-10 */
    public function testCommandLineStepsShowCommandsAndCapturedOutput()
    {
        $a = self::updateSection( 'Track A: the command line' );
        foreach ( array( 'php bin/php/maintenance.php on', 'git fetch origin --tags', 'git merge --no-edit origin/main',
                         'composer update', 'php bin/php/checkmanifest.php --all', 'php bin/php/ezcache.php --clear-all',
                         'exp:velocity deploy', 'git log --oneline ORIG_HEAD..HEAD', 'php bin/php/maintenance.php off' ) as $command )
            $this->assertStringContainsString( $command, $a );
        // each terminal capture is a real file and is shown
        foreach ( array( 'a1-maintenance', 'a2-waiting', 'a3-kernel', 'a4-composer', 'a5-check', 'a6-caches', 'a7-leave' ) as $capture )
        {
            $this->assertStringContainsString( '(images/update-cli-' . $capture . '.webp)', $a );
            $this->assertFileExists( self::root() . '/doc/en/us/help/images/update-cli-' . $capture . '.webp' );
        }
    }

    /** HG-07 */
    public function testRecommendedCommands()
    {
        $text = self::read( self::PAGE );
        foreach ( array(
            "composer config --global 'preferred-install.se7enxweb/*' source",
            'git clone https://github.com/se7enxweb/exponential.git exponential',
            'composer create-project se7enxweb/exponential exponential --prefer-source --keep-vcs',
            "composer reinstall 'se7enxweb/*'",
            'composer status -v',
        ) as $command )
            $this->assertStringContainsString( $command, $text );
        foreach ( array( 'doc/en/us/install/03-getting-the-code.md', 'doc/en/us/INSTALL.md' ) as $doc )
            $this->assertStringContainsString( "composer config --global 'preferred-install.se7enxweb/*' source", self::read( $doc ), $doc );
    }
}
