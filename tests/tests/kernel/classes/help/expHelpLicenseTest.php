<?php
/**
 * The license of the documentation (doc/en/us/README-LICENSE.md, exphelp.ini [License], expHelpLicense): read from the
 * files; no database.
 *
 *  LIC-01  doc/en/us/LICENSE-GFDL-1.3.txt is the unmodified GFDL 1.3 text (the SHA-256 of gnu.org's fdl-1.3.txt)
 *  LIC-02  exphelp.ini [License] names the license, its version, its URLs and the pages, and the pages exist
 *  LIC-03  the help allowlist serves the license text, the notice and the explanation for readers
 *  LIC-04  every help template of admin and admin4 carries the license block, from the setting, without inline CSS
 *  LIC-05  expHelpLicense tells project pages, inherited pages and extension pages apart
 *  LIC-06  the notice page holds the standard GFDL notice; the explanation is for every audience
 *  LIC-07  every string of the license parts is in the complete catalogues
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpLicenseTest extends PHPUnit\Framework\TestCase
{
    const DESIGNS = array( 'admin', 'admin4' );

    public static function setUpBeforeClass(): void
    {
        // a vendor/ whose class map predates the class (a worktree sharing another checkout's vendor/)
        if ( !class_exists( 'expHelpLicense' ) )
            require_once self::root() . '/kernel/classes/help/exphelplicense.php';
    }

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    /**
     * The values of a setting of settings/exphelp.ini, read as text (a list for Name[], else one value).
     *
     * @param string $group
     * @param string $name
     * @return string|string[]|null
     */
    private static function ini( $group, $name )
    {
        $text = (string)file_get_contents( self::root() . '/settings/exphelp.ini' );
        if ( !preg_match( '/^\[' . preg_quote( $group, '/' ) . '\]\s*$(.*?)(?=^\[|\z)/ms', $text, $m ) )
            return null;
        $values = array();
        $single = null;
        foreach ( preg_split( '/\R/', $m[1] ) as $line )
        {
            if ( preg_match( '/^' . preg_quote( $name, '/' ) . '\[\]=(.*)$/', $line, $v ) )
                $values[] = trim( $v[1] );
            elseif ( preg_match( '/^' . preg_quote( $name, '/' ) . '=(.*)$/', $line, $v ) )
                $single = trim( $v[1] );
        }
        return $single !== null ? $single : ( $values ? $values : null );
    }

    /** LIC-01 */
    public function testLicenseTextIsTheOfficialText()
    {
        $file = self::root() . '/doc/en/us/LICENSE-GFDL-1.3.txt';
        $this->assertFileExists( $file );
        $this->assertSame( '110535522396708cea37c72a802c5e7e81391139f5f7985631c93ef242b206a4', hash_file( 'sha256', $file ),
                           'doc/en/us/LICENSE-GFDL-1.3.txt must be https://www.gnu.org/licenses/fdl-1.3.txt, unmodified' );
        $this->assertSame( expHelpLicense::GFDL_13_SHA256, hash_file( 'sha256', $file ) );
        $this->assertTrue( expHelpLicense::isOfficialText( $file ) );
        $this->assertStringContainsString( 'Version 1.3, 3 November 2008', (string)file_get_contents( $file ) );
    }

    /** LIC-02 */
    public function testSettings()
    {
        $this->assertSame( 'GNU Free Documentation License', self::ini( 'License', 'Name' ) );
        $this->assertSame( '1.3', self::ini( 'License', 'Version' ) );
        $this->assertSame( 'true', self::ini( 'License', 'OrLater' ) );
        $this->assertSame( 'https://www.gnu.org/licenses/fdl-1.3.html', self::ini( 'License', 'URL' ) );
        $this->assertSame( 'https://www.gnu.org/licenses/fdl-1.3.txt', self::ini( 'License', 'TextURL' ) );
        $this->assertNotEmpty( self::ini( 'License', 'CopyrightHolder' ) );
        foreach ( array( 'TextPage', 'NoticePage', 'ExplainerPage' ) as $page )
        {
            $id = self::ini( 'License', $page );
            $this->assertIsString( $id, $page );
            $this->assertFileExists( self::root() . '/doc/' . $id, $page );
        }
        $this->assertIsArray( self::ini( 'License', 'OtherLicensePathRules' ) );
        // the class falls back to the same list when the setting is missing
        $defaults = new ReflectionProperty( 'expHelpLicense', 'defaults' );
        $value = $defaults->getValue();
        $this->assertSame( self::ini( 'License', 'OtherLicensePathRules' ), $value['OtherLicensePathRules'] );
        $this->assertSame( self::ini( 'License', 'CopyrightHolder' ), $value['CopyrightHolder'] );
        $this->assertSame( self::ini( 'License', 'OtherCopyright' ), $value['OtherCopyright'] );
        // the help lists the license text for everyone
        $this->assertContains( 'doc/en/us/LICENSE-GFDL-1.3.txt;editor,admin,developer,user', (array)self::ini( 'AudienceSettings', 'PathRules' ) );
    }

    /** LIC-03 */
    public function testServedByTheAllowlist()
    {
        $paths = array( 'doc/en/us/LICENSE-GFDL-1.3.txt', 'doc/en/us/README-LICENSE.md', 'doc/en/us/help/free-documentation.md' );
        $config = array( 'roots' => (array)self::ini( 'IndexSettings', 'Roots' ),
                         'documentSuffixes' => (array)self::ini( 'IndexSettings', 'DocumentSuffixes' ),
                         'excludePatterns' => (array)self::ini( 'IndexSettings', 'ExcludePatterns' ),
                         // the tracked files, as git and the manifest list them (a checkout without git: the manifest)
                         'tracked' => self::trackedDocFiles() );
        $allowlist = new expHelpAllowlist( self::root(), $config );
        foreach ( $paths as $path )
            $this->assertTrue( $allowlist->allows( $path ), $path . ' must be served by the help' );
        $this->assertFalse( $allowlist->allows( 'LICENSE' ), 'the root LICENSE is not in doc/' );
    }

    /** @return string[] the tracked files below doc/: git when it is there, else the manifest */
    private static function trackedDocFiles()
    {
        $out = array();
        $rc = 1;
        @exec( 'git -C ' . escapeshellarg( self::root() ) . ' ls-files -- doc 2>/dev/null', $out, $rc );
        if ( $rc === 0 && $out )
            return $out;
        $files = array();
        foreach ( (array)@file( self::root() . '/share/filelist.md5', FILE_IGNORE_NEW_LINES ) as $line )
            if ( preg_match( '/^[0-9a-f]{32}\s+(doc\/.+)$/', $line, $m ) )
                $files[] = $m[1];
        return $files;
    }

    /** LIC-04 */
    public function testTemplatesCarryTheLicenseBlock()
    {
        foreach ( self::DESIGNS as $design )
        {
            $dir = self::root() . '/design/' . $design . '/templates/exphelp';
            $notice = (string)file_get_contents( $dir . '/parts/license_notice.tpl' );
            $dashboard = (string)file_get_contents( $dir . '/parts/license_dashboard.tpl' );
            $this->assertStringContainsString( "fetch( 'exphelp', 'license'", $notice, $design );
            $this->assertStringContainsString( "fetch( 'exphelp', 'license'", $dashboard, $design );
            $this->assertStringContainsString( 'Free documentation: use it to help yourself and others', $dashboard, $design );
            $this->assertStringContainsString( 'licensed under the %license %version or later', $notice, $design );
            foreach ( array( 'notice' => $notice, 'dashboard' => $dashboard ) as $part => $text )
            {
                // name, version and address come from exphelp.ini [License], never from the template
                $this->assertStringNotContainsString( 'https://www.gnu.org', $text, $design . ' ' . $part );
                $this->assertStringNotContainsString( 'Free Documentation License', $text, $design . ' ' . $part );
                $this->assertDoesNotMatchRegularExpression( '/<style\b|\sstyle="|\son[a-z]+=|<script\b/i', $text, $design . ' ' . $part );
                $this->assertStringContainsString( 'text_page', $text, $design . ' ' . $part );
                $this->assertStringContainsString( '$lic.url', $text, $design . ' ' . $part );
                $this->assertStringContainsString( 'explainer_page', $text, $design . ' ' . $part );
            }
            $this->assertMatchesRegularExpression( "#\{include uri='design:exphelp/parts/license_notice\.tpl' license_page_id=\\\$help_page\.id\}#",
                                                   (string)file_get_contents( $dir . '/viewer.tpl' ), $design . ' viewer' );
            $this->assertStringContainsString( "design:exphelp/parts/license_dashboard.tpl", (string)file_get_contents( $dir . '/dashboard.tpl' ), $design . ' dashboard' );
            foreach ( array( 'search', 'report' ) as $view )
                $this->assertStringContainsString( "design:exphelp/parts/license_notice.tpl", (string)file_get_contents( $dir . '/' . $view . '.tpl' ), $design . ' ' . $view );
            $css = (string)file_get_contents( self::root() . '/design/' . $design . '/stylesheets/modules/help.css' );
            $this->assertStringContainsString( '.a4-help-license', $css, $design );
            $this->assertStringContainsString( '.a4-help-freedoc-list', $css, $design );
        }
        // the classic admin carries the same parts as admin4
        foreach ( array( 'license_notice.tpl', 'license_dashboard.tpl' ) as $part )
            $this->assertFileEquals( self::root() . '/design/admin4/templates/exphelp/parts/' . $part,
                                     self::root() . '/design/admin/templates/exphelp/parts/' . $part );
    }

    /** LIC-05 */
    public function testPagesAndTheirLicense()
    {
        foreach ( array( 'help/getting-started.md', 'guides/README.md', 'features/6.0/help-system.md', 'bc/6.0/repair.md',
                         'changelogs/6.0/6.0.15.md', 'glossary.md', 'LICENSE-GFDL-1.3.txt', 'branch-overview.md' ) as $id )
            $this->assertFalse( expHelpLicense::isOtherLicense( $id ), $id );
        foreach ( array( 'features/3.8/foo.txt', 'features/4.4/extension_xml.txt', 'bc/5.4/changes.txt', 'changelogs/2.9/CHANGELOG-2.9-7',
                         'specifications/trunk/x.txt', 'shop/index.html', 'release-overview',
                         'standards/header.txt', 'changelogs/README.md', 'doc/en/us/features/3.9/x.txt' ) as $id )
            $this->assertTrue( expHelpLicense::isOtherLicense( $id ), $id );

        $free = expHelpLicense::forPage( 'help/getting-started.md' );
        $this->assertTrue( $free['free'] );
        $this->assertSame( 'GNU Free Documentation License', $free['name'] );
        $this->assertSame( '1.3', $free['version'] );
        $this->assertTrue( $free['or_later'] );
        $this->assertSame( 'https://www.gnu.org/licenses/fdl-1.3.html', $free['url'] );
        $this->assertStringContainsString( 'Version 1.3 or any later version published by the Free Software Foundation; with no Invariant Sections, no Front-Cover Texts, and no Back-Cover Texts.', $free['notice'] );
        $this->assertStringStartsWith( 'Copyright (C) ', $free['copyright'] );
        $this->assertStringContainsString( 'GNU Free Documentation License 1.3 or later', expHelpLicense::noticeText( 'help/getting-started.md' ) );

        $old = expHelpLicense::forPage( 'features/3.8/foo.txt' );
        $this->assertFalse( $old['free'] );
        $this->assertStringContainsString( 'eZ Systems', $old['other_copyright'] );
        $this->assertStringNotContainsString( 'Permission is granted', expHelpLicense::noticeText( 'features/3.8/foo.txt' ) );

        $ext = expHelpLicense::forPage( 'ext/someext/guide.md' );
        $this->assertFalse( $ext['free'] );
        $this->assertSame( 'someext', $ext['extension'] );
    }

    /** LIC-07 every string of the two parts is in the complete catalogues (context kernel/exphelp) */
    public function testStringsAreTranslated()
    {
        $dir = self::root() . '/design/admin4/templates/exphelp/parts/';
        $text = file_get_contents( $dir . 'license_notice.tpl' ) . file_get_contents( $dir . 'license_dashboard.tpl' );
        preg_match_all( "/\{?'((?:[^'\\\\]|\\\\.)+)'\|i18n\( \\\$lic_ctx/", $text, $m );
        $strings = array_unique( array_map( 'stripslashes', $m[1] ) );
        $this->assertGreaterThanOrEqual( 20, count( $strings ) );
        foreach ( array( 'ger-DE', 'fre-FR', 'esl-ES', 'esl-MX', 'ukr-UA', 'heb-IL', 'lat-VA', 'tlh-QO', 'ara-SA' ) as $lang )
        {
            $ts = (string)file_get_contents( self::root() . '/share/translations/' . $lang . '/translation.ts' );
            $this->assertMatchesRegularExpression( '#<name>kernel/exphelp</name>#', $ts, $lang );
            foreach ( $strings as $source )
                $this->assertStringContainsString( '<source>' . htmlspecialchars( $source, ENT_QUOTES | ENT_XML1 ) . '</source>', $ts, $lang . ': ' . $source );
        }
    }

    /** LIC-06 */
    public function testNoticeAndExplanationPages()
    {
        $notice = (string)file_get_contents( self::root() . '/doc/en/us/README-LICENSE.md' );
        $this->assertStringContainsString( 'Permission is granted to copy, distribute and/or modify this document under the terms of the GNU Free'
            . "\nDocumentation License, Version 1.3 or any later version published by the Free Software Foundation; with no"
            . "\nInvariant Sections, no Front-Cover Texts, and no Back-Cover Texts.", $notice );
        $this->assertStringContainsString( '## GNU Free Documentation License', $notice, 'the section the notice names' );
        $this->assertStringContainsString( '(LICENSE-GFDL-1.3.txt)', $notice );

        $explain = (string)file_get_contents( self::root() . '/doc/en/us/help/free-documentation.md' );
        $this->assertMatchesRegularExpression( '/^audience: editor, admin, developer, user$/m', $explain );
        $this->assertStringContainsString( '(../LICENSE-GFDL-1.3.txt)', $explain );
        $this->assertStringContainsString( '(../README-LICENSE.md)', $explain );
        $this->assertStringContainsString( 'https://www.gnu.org/licenses/fdl-1.3.html', $explain );
    }
}

?>
