<?php
/**
 * The "Develop with Git" help page and the "Grow from user to developer" band of the help dashboard
 * (doc/help/develop-with-git.md, design/<admin4|admin>/templates/exphelp/parts/dashboard_grow.tpl). No database.
 *
 *  HG-01  the page exists with its front matter: audiences user and developer, category development, featured
 *  HG-02  the help serves it: the allowlist of the real installation allows it (a git-tracked file below doc/)
 *  HG-03  the dashboard features it ([FeaturedSettings] PageList in settings/exphelp.ini)
 *  HG-04  every relative link of the page leads to an existing file, and every #anchor to a heading of its page
 *  HG-05  the dashboard part exists in admin4 and the classic admin, the same file in both, included by both
 *         dashboards, linking the page and the installation chapter, with no inline CSS or script
 *  HG-06  every string of the part is in the eng-US and ger-DE catalogues under kernel/exphelp
 *  HG-07  the commands the page recommends are the ones that were verified (clone URL, preferred-install, keep-vcs)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpDevelopWithGitTest extends PHPUnit\Framework\TestCase
{
    const PAGE = 'doc/help/develop-with-git.md';
    const PART = 'templates/exphelp/parts/dashboard_grow.tpl';

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
        $text = self::read( self::PAGE );
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
            $target = $parts[0] === '' ? self::PAGE : self::normalize( dirname( self::PAGE ) . '/' . $parts[0] );
            $this->assertStringStartsWith( 'doc/', $target, $href );
            $this->assertFileExists( self::root() . '/' . $target, $href );
            if ( isset( $parts[1] ) && $parts[1] !== '' )
                $this->assertContains( $parts[1], self::anchors( self::read( $target ) ), $href . ': no such heading' );
            ++$checked;
        }
        $this->assertGreaterThan( 10, $checked );
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
        $this->assertFileExists( self::root() . '/doc/install/03-getting-the-code.md' );
        $this->assertDoesNotMatchRegularExpression( '/<style|style\s*=|<script|\son[a-z]+\s*=/i', $admin4 );
        foreach ( array( 'admin4', 'admin' ) as $design )
            $this->assertStringContainsString( '.a4-help-grow {', self::read( 'design/' . $design . '/stylesheets/modules/help.css' ) );
    }

    /** HG-06 */
    public function testStringsTranslated()
    {
        preg_match_all( "/\\{'((?:[^'\\\\]|\\\\.)+)'\\|i18n\\( \\\$help_grow_ctx \\)/", self::read( 'design/admin4/' . self::PART ), $m );
        $this->assertCount( 10, $m[1] );
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
        foreach ( array( 'doc/install/03-getting-the-code.md', 'doc/INSTALL.md' ) as $doc )
            $this->assertStringContainsString( "composer config --global 'preferred-install.se7enxweb/*' source", self::read( $doc ), $doc );
    }
}
