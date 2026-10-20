<?php
/**
 * The migration hub of the help and its guides (doc/en/us/install/migration/). No database.
 *
 *  MH-01  the hub exists, is featured (front matter and settings/exphelp.ini [FeaturedSettings]) and is linked from
 *         the installation book's contents (doc/en/us/install/README.md)
 *  MH-02  every guide of the folder is linked from the hub, and every guide the hub links exists
 *  MH-03  every page has front matter: title = first heading, an audience, category install-upgrade, type install,
 *         a summary; and ends with the documentation license notice
 *  MH-04  every relative link leads to an existing file under doc/ and every #anchor to a heading of that file;
 *         external links use https; no internal host names, no raw script, style or iframe
 *  MH-05  the product table names every Exponential distribution repository it describes, and the hub never states
 *         a version trap without its warning (Platform Legacy v5.0.3)
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpMigrationHubTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

// a worktree whose vendor/ is a link to another checkout autoloads from that checkout's class map
foreach ( array( 'expHelpFrontMatter' => 'exphelpfrontmatter.php', 'expHelpMarkdownRenderer' => 'exphelpmarkdownrenderer.php' ) as $helpClass => $helpFile )
    if ( !class_exists( $helpClass ) )
        require_once dirname( __DIR__, 5 ) . '/kernel/classes/help/' . $helpFile;
unset( $helpClass, $helpFile );

class expHelpMigrationHubTest extends PHPUnit\Framework\TestCase
{
    const FOLDER = 'doc/en/us/install/migration';
    const HUB = 'doc/en/us/install/migration/README.md';

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function read( $path )
    {
        $file = self::root() . '/' . $path;
        return is_file( $file ) ? (string)file_get_contents( $file ) : '';
    }

    /** The pages of the folder, as paths from the installation root */
    private static function pages()
    {
        $pages = array();
        foreach ( glob( self::root() . '/' . self::FOLDER . '/*.md' ) as $file )
            $pages[] = substr( $file, strlen( self::root() ) + 1 );
        sort( $pages );
        return $pages;
    }

    /** The lines of one section of an INI file */
    private static function iniSection( $path, $section )
    {
        $ini = self::read( $path );
        $start = strpos( $ini, "\n[" . $section . "]" );
        if ( $start === false )
            return '';
        $end = strpos( $ini, "\n[", $start + 1 );
        return substr( $ini, $start, $end === false ? null : $end - $start );
    }

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

    /** The relative link targets of a page (path below the root, without anchor) */
    private static function localLinks( $path, $text )
    {
        preg_match_all( '/\]\(([^)\s]+)\)/', $text, $m );
        $links = array();
        foreach ( $m[1] as $href )
        {
            if ( preg_match( '#^[a-z]+:#i', $href ) )
                continue;
            $parts = explode( '#', $href, 2 );
            $links[] = $parts[0] === '' ? $path : self::normalize( dirname( $path ) . '/' . $parts[0] );
        }
        return $links;
    }

    /** MH-01 */
    public function testHubIsFeaturedAndLinked()
    {
        $hub = self::read( self::HUB );
        $this->assertNotSame( '', $hub, 'the hub exists' );
        list( $meta ) = expHelpFrontMatter::split( $hub );
        $this->assertTrue( isset( $meta['featured'] ) && ( $meta['featured'] === true || $meta['featured'] === 'true' ), 'featured' );
        $this->assertMatchesRegularExpression( '#^PageList\[\]=install/migration/README\.md\s*$#m',
                                               self::iniSection( 'settings/exphelp.ini', 'FeaturedSettings' ) );
        $this->assertStringContainsString( '](migration/README.md)', self::read( 'doc/en/us/install/README.md' ) );
    }

    /** MH-02 */
    public function testHubLinksEveryGuide()
    {
        $pages = self::pages();
        $this->assertGreaterThanOrEqual( 20, count( $pages ) );
        $linked = array_unique( self::localLinks( self::HUB, self::read( self::HUB ) ) );
        foreach ( $pages as $page )
        {
            if ( $page !== self::HUB )
                $this->assertContains( $page, $linked, $page . ' is linked from the hub' );
        }
    }

    /** MH-03 */
    public function testFrontMatter()
    {
        foreach ( self::pages() as $path )
        {
            $text = self::read( $path );
            list( $meta, $body ) = expHelpFrontMatter::split( $text );
            $this->assertNotEmpty( $meta['audience'] ?? '', $path . ': audience' );
            $this->assertSame( 'install-upgrade', $meta['category'] ?? null, $path );
            $this->assertSame( 'install', $meta['type'] ?? null, $path );
            $this->assertNotSame( '', trim( (string)( $meta['summary'] ?? '' ) ), $path . ': summary' );
            $this->assertStringStartsWith( '# ' . $meta['title'] . "\n", ltrim( $body ), $path . ': title is the first heading' );
            $this->assertStringContainsString( 'GNU Free Documentation License', $text, $path . ': license notice' );
            $this->assertStringContainsString( '](../../README-LICENSE.md)', $text, $path . ': license link' );
        }
    }

    /** MH-04 */
    public function testLinks()
    {
        $checked = 0;
        foreach ( self::pages() as $path )
        {
            $text = self::read( $path );
            $this->assertDoesNotMatchRegularExpression( '/\b[a-z0-9.-]*alpha\.se7enx\.com\b/i', $text, $path . ': internal host' );
            $this->assertDoesNotMatchRegularExpression( '/<(?:script|style|iframe)\b/i', preg_replace( '/`[^`\n]*`/', '', $text ), $path );
            preg_match_all( '/\]\(([^)\s]+)\)|<(https?:[^>\s]+)>/', $text, $m );
            foreach ( $m[1] as $i => $href )
            {
                if ( $href === '' )
                    $href = $m[2][$i];
                if ( preg_match( '#^[a-z]+:#i', $href ) )
                {
                    $this->assertStringStartsWith( 'https://', $href, $path . ': external links use https' );
                    continue;
                }
                $parts = explode( '#', $href, 2 );
                $target = $parts[0] === '' ? $path : self::normalize( dirname( $path ) . '/' . $parts[0] );
                $this->assertStringStartsWith( 'doc/', $target, $path . ': ' . $href );
                $this->assertFileExists( self::root() . '/' . $target, $path . ': ' . $href );
                if ( isset( $parts[1] ) && $parts[1] !== '' && preg_match( '/\.(md|markdown)$/', $target ) )
                    $this->assertContains( $parts[1], self::anchors( self::read( $target ) ), $path . ': ' . $href . ': no such heading' );
                ++$checked;
            }
        }
        $this->assertGreaterThan( 200, $checked );
    }

    /** MH-05 */
    public function testProductTable()
    {
        $hub = self::read( self::HUB );
        foreach ( array( 'se7enxweb/exponential', 'se7enxweb/exponential-platform', 'se7enxweb/exponential-platform-nexus',
                         'se7enxweb/exponential-platform-legacy', 'se7enxweb/exponential-platform-dxp',
                         'se7enxweb/exponential-platform-dxp-skeleton', 'se7enxweb/exponential-platform-v4x-dxp-skeleton',
                         'se7enxweb/exponentialbasic', 'se7enxweb/oss' ) as $repository )
            $this->assertStringContainsString( '`' . $repository . '`', $hub, $repository );
        $this->assertStringContainsString( 'is not a v5 release', $hub, 'the v5.0.3 trap is explained' );
        $this->assertStringContainsString( 'Not verifiable', $hub, 'what could not be verified says so' );
    }
}
