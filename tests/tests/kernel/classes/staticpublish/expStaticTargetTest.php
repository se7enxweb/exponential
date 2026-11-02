<?php
/**
 * The targets of static publishing (expStaticTarget): defaults and validation, pure (the siteaccesses are passed in).
 *
 *  TG-01 defaults fill a block; lists stay lists, unknown settings are dropped
 *  TG-02 a valid GitHub Pages target has no error and no warning
 *  TG-03 the siteaccess: missing, unknown, administration or editing (by context and by name), no SiteURL
 *  TG-04 the enumerated settings and the lists
 *  TG-05 BasePath, PublicURL, CustomDomain, Branch, Folder, Remote (no secret in it), Credential, numbers
 *  TG-06 warnings: base-path without BasePath, PublicURL path against BasePath, sitemap without PublicURL, no
 *        .nojekyll on GitHub Pages, custom domain with a base path
 *  TG-07 base path, public address, folders (Windows separators, absolute work directory)
 *  TG-08 ExcludePaths patterns
 *  TG-09 the S5 checks: Schedule, Webhook, License, Forms=endpoint without a public address
 *  TG-10 targets as an INI migration: every setting, lists as Var[] then values, credentials as names only
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expStaticTargetTest extends PHPUnit\Framework\TestCase
{
    const CONTEXT = array( 'siteaccesses' => array( 'site', 'de', 'admin', 'intranet', 'site_editor', 'nourl' ), 'admin' => array( 'admin', 'intranet' ),
                           'urls' => array( 'site' => 'www.example.com', 'de' => 'www.example.com/de', 'admin' => 'admin.example.com',
                                            'intranet' => 'intra.example.com', 'site_editor' => 'edit.example.com', 'nourl' => '' ) );

    private static function valid( array $settings = array() )
    {
        return new expStaticTarget( 'github-public', $settings + array(
            'SiteAccess' => 'site', 'Remote' => 'https://github.com/owner/my-site.git', 'BasePath' => '/my-site/',
            'PublicURL' => 'https://owner.github.io/my-site/', 'Credential' => 'token:github_pages_token',
        ) );
    }

    private static function errors( expStaticTarget $target )
    {
        return $target->validate( self::CONTEXT )['errors'];
    }

    /** TG-01 */
    public function testDefaults()
    {
        $t = new expStaticTarget( 'x', array( 'SiteAccess' => ' site ', 'Extras' => array( 'nojekyll', '', ' 404 ' ), 'Unknown' => 'y', 'Links' => array( 'oops' ) ) );
        $this->assertSame( 'site', $t->siteAccess() );
        $this->assertSame( array( 'nojekyll', '404' ), $t->extras() );
        $this->assertSame( '', $t->setting( 'Links' ), 'a list given for a single value is empty' );
        $this->assertNull( $t->setting( 'Unknown' ) );
        $this->assertSame( 'github-pages', $t->setting( 'Kind' ) );
        $this->assertSame( array( 'strip' ), $t->setting( 'PostProcess' ) );
        $this->assertSame( 'x', $t->name() );
        $this->assertSame( array_keys( expStaticTarget::DEFAULTS ), array_keys( $t->settings() ) );
        $data = $t->toArray();
        $this->assertSame( 'x', $data['id'] );
    }

    /** TG-02 */
    public function testValid()
    {
        $this->assertSame( array( 'errors' => array(), 'warnings' => array() ), self::valid()->validate( self::CONTEXT ) );
        $git = new expStaticTarget( 'internal', array( 'SiteAccess' => 'de', 'Kind' => 'git', 'Remote' => 'git@git.example.com:web/site.git',
                                                       'Credential' => 'sshkey:internal', 'Branch' => 'main', 'Links' => 'base-path', 'BasePath' => '/' ) );
        $this->assertSame( array(), self::errors( $git ) );
    }

    /** TG-03 */
    public function testSiteAccess()
    {
        $this->assertStringContainsString( 'SiteAccess is not set', self::errors( self::valid( array( 'SiteAccess' => '' ) ) )[0] );
        $this->assertStringContainsString( 'is not a siteaccess', self::errors( self::valid( array( 'SiteAccess' => 'gone' ) ) )[0] );
        foreach ( array( 'admin', 'intranet', 'site_editor' ) as $admin )
            $this->assertStringContainsString( 'administration or editing', self::errors( self::valid( array( 'SiteAccess' => $admin ) ) )[0], $admin );
        $this->assertStringContainsString( 'no SiteURL', self::errors( self::valid( array( 'SiteAccess' => 'nourl' ) ) )[0] );
        $this->assertSame( array(), self::errors( self::valid( array( 'SiteAccess' => 'nourl', 'CrawlURL' => 'http://127.0.0.1:8080/' ) ) ) );
        $this->assertTrue( expStaticTarget::adminName( 'admin' ) );
        $this->assertTrue( expStaticTarget::adminName( 'admin4' ) );
        $this->assertTrue( expStaticTarget::adminName( 'eng_admin' ) );
        $this->assertTrue( expStaticTarget::adminName( 'editor' ) );
        $this->assertFalse( expStaticTarget::adminName( 'site' ) );
        $this->assertFalse( expStaticTarget::adminName( 'credit' ) );
        $bad = new expStaticTarget( 'Bad Id', array( 'SiteAccess' => 'site' ) );
        $this->assertStringContainsString( 'target id', self::errors( $bad )[0] );
    }

    /** TG-04 */
    public function testEnumerations()
    {
        foreach ( array( 'Kind' => 'ftp', 'Delivery' => 'zip', 'Links' => 'absolute', 'Assets' => 'all', 'Forms' => 'mail', 'Search' => 'yes' ) as $name => $value )
            $this->assertNotEmpty( self::errors( self::valid( array( $name => $value ) ) ), "$name=$value" );
        foreach ( array( 'Extras' => 'zip', 'PostProcess' => 'obfuscate', 'Triggers' => 'webhook', 'Notify' => 'always' ) as $name => $value )
            $this->assertStringContainsString( "{$name}[]=$value", implode( ' ', self::errors( self::valid( array( $name => array( $value ) ) ) ) ) );
    }

    /** TG-05 */
    public function testValues()
    {
        foreach ( array( 'my-site', '/my site/', '/a/../b/', '/x', '/x/?y/' ) as $bad )
            $this->assertNotEmpty( self::errors( self::valid( array( 'BasePath' => $bad, 'PublicURL' => '' ) ) ), "BasePath=$bad" );
        foreach ( array( '/', '/my-site/', '/a/b.c_d~e/' ) as $good )
            $this->assertSame( array(), self::errors( self::valid( array( 'BasePath' => $good, 'PublicURL' => '' ) ) ), "BasePath=$good" );
        $this->assertNotEmpty( self::errors( self::valid( array( 'PublicURL' => 'owner.github.io/x' ) ) ) );
        $this->assertNotEmpty( self::errors( self::valid( array( 'CrawlURL' => 'ftp://x' ) ) ) );
        $this->assertNotEmpty( self::errors( self::valid( array( 'CustomDomain' => 'https://www.example.com' ) ) ) );
        foreach ( array( '../x', 'a..b', '-x', 'x.lock', 'a b' ) as $bad )
            $this->assertNotEmpty( self::errors( self::valid( array( 'Branch' => $bad ) ) ), "Branch=$bad" );
        $this->assertNotEmpty( self::errors( self::valid( array( 'Folder' => 'site' ) ) ), 'GitHub Pages: root or docs' );
        $this->assertSame( array(), self::errors( self::valid( array( 'Folder' => '/docs/', 'Branch' => 'main' ) ) ) );
        $this->assertNotEmpty( self::errors( self::valid( array( 'Folder' => '../x', 'Kind' => 'git' ) ) ) );
        $this->assertStringContainsString( 'must not hold a password or token',
                                           implode( ' ', self::errors( self::valid( array( 'Remote' => 'https://user:ghp_secret@github.com/o/r.git' ) ) ) ) );
        $this->assertNotEmpty( self::errors( self::valid( array( 'Remote' => 'not a remote' ) ) ) );
        foreach ( array( 'password', 'token:', 'token:a b', 'sshkey:' ) as $bad )
            $this->assertNotEmpty( self::errors( self::valid( array( 'Credential' => $bad ) ) ), "Credential=$bad" );
        foreach ( array( 'none', 'token:env:GITHUB_TOKEN', 'app:12345', 'sshkey:github-public' ) as $good )
            $this->assertSame( array(), self::errors( self::valid( array( 'Credential' => $good ) ) ), "Credential=$good" );
        foreach ( array( 'MaxPages' => '0', 'MaxDepth' => '31' ) as $name => $value )
            $this->assertNotEmpty( self::errors( self::valid( array( $name => $value ) ) ), "$name=$value" );
        $this->assertNotEmpty( self::errors( self::valid( array( 'ExcludeSections' => array( 'members' ) ) ) ) );
        $this->assertNotEmpty( self::errors( self::valid( array( 'ExcludePaths' => array( 'members' ) ) ) ) );
    }

    /** TG-06 */
    public function testWarnings()
    {
        $w = self::valid( array( 'Links' => 'base-path', 'BasePath' => '', 'PublicURL' => '' ) )->validate( self::CONTEXT )['warnings'];
        $this->assertStringContainsString( 'Links=base-path without a BasePath', implode( ' ', $w ) );
        $this->assertStringContainsString( 'needs PublicURL', implode( ' ', $w ) );
        $w = self::valid( array( 'PublicURL' => 'https://owner.github.io/other/' ) )->validate( self::CONTEXT )['warnings'];
        $this->assertStringContainsString( 'is not BasePath', implode( ' ', $w ) );
        $w = self::valid( array( 'Extras' => array( '404' ) ) )->validate( self::CONTEXT )['warnings'];
        $this->assertStringContainsString( 'Jekyll', implode( ' ', $w ) );
        $w = self::valid( array( 'CustomDomain' => 'www.example.org' ) )->validate( self::CONTEXT )['warnings'];
        $this->assertStringContainsString( 'BasePath should be empty', implode( ' ', $w ) );
    }

    /** TG-07 */
    public function testPathsAndAddresses()
    {
        $this->assertSame( '/my-site/', self::valid()->basePath() );
        $this->assertSame( '/', self::valid( array( 'BasePath' => '' ) )->basePath() );
        $this->assertSame( 'https://owner.github.io/my-site/', self::valid()->publicUrl() );
        $this->assertSame( 'https://www.example.org/', self::valid( array( 'PublicURL' => '', 'CustomDomain' => 'www.example.org' ) )->publicUrl() );
        $this->assertSame( '', self::valid( array( 'PublicURL' => '' ) )->publicUrl() );
        $this->assertSame( 'var/site/static-publish/github-public', self::valid()->directory( 'var/site' ) );
        $this->assertSame( 'var/site/out/github-public', self::valid()->directory( 'var\\site\\', 'out' ) );
        $this->assertSame( '/srv/builds/github-public', self::valid()->directory( 'var/site', '/srv/builds/' ) );
        $this->assertSame( 'D:/builds/github-public', self::valid()->directory( 'var/site', 'D:\\builds' ) );
        $this->assertSame( 'var/site/static-publish', expStaticTarget::workDirectory( 'var/site', '' ) );
    }

    /** TG-08 */
    public function testExcludePaths()
    {
        $t = self::valid( array( 'ExcludePaths' => array( '/members/*', '/shop', '/drafts*' ) ) );
        $this->assertTrue( $t->excludesPath( '/members' ) );
        $this->assertTrue( $t->excludesPath( '/members/list/a' ) );
        $this->assertTrue( $t->excludesPath( '/shop/' ) );
        $this->assertFalse( $t->excludesPath( '/shop/item' ) );
        $this->assertTrue( $t->excludesPath( '/drafts-2026' ) );
        $this->assertFalse( $t->excludesPath( '/about' ) );
    }

    /** TG-09 */
    public function testExtrasChecks()
    {
        $context = array( 'siteaccesses' => array( 'site' ), 'admin' => array(), 'urls' => array( 'site' => 'www.example.com' ) );
        $bad = new expStaticTarget( 'pub', array( 'SiteAccess' => 'site', 'Schedule' => '0 25 * * *', 'Webhook' => 'http://hooks.example.com/x' ) );
        $errors = implode( "\n", $bad->validate( $context )['errors'] );
        $this->assertStringContainsString( 'Schedule=0 25 * * * is not a cron expression', $errors );
        $this->assertStringContainsString( 'must be an https address', $errors );
        $warn = new expStaticTarget( 'pub', array( 'SiteAccess' => 'site', 'Triggers' => array( 'schedule' ), 'Extras' => array( 'nojekyll', 'license' ),
                                                   'Forms' => 'endpoint' ) );
        $check = $warn->validate( $context );
        $this->assertSame( array(), $check['errors'] );
        $warnings = implode( "\n", $check['warnings'] );
        foreach ( array( 'Triggers[]=schedule needs a Schedule', 'Extras[]=license needs License', 'Forms=endpoint needs PublicURL' ) as $w )
            $this->assertStringContainsString( $w, $warnings );
        $good = new expStaticTarget( 'pub', array( 'SiteAccess' => 'site', 'Triggers' => array( 'schedule' ), 'Schedule' => '@daily', 'Webhook' => 'https://h.example.com/x',
                                                   'Extras' => array( 'nojekyll', 'license' ), 'License' => 'CC-BY-4.0' ) );
        $this->assertSame( array(), $good->validate( $context )['errors'] );
    }

    /** TG-10 */
    public function testMigrationIni()
    {
        $ini = expStaticTarget::migrationIni( array(
            new expStaticTarget( 'pub', array( 'Name' => "Public\nsite", 'SiteAccess' => 'site', 'Credential' => 'token:github_pages_token', 'Extras' => array( 'nojekyll', '404' ) ) ),
            new expStaticTarget( 'docs', array( 'SiteAccess' => 'docs', 'ExcludePaths' => array() ) ) ) );
        $this->assertStringStartsWith( "#?ini charset=\"utf-8\"?\n# description: Static publishing targets: pub, docs\n", $ini );
        $this->assertStringContainsString( "[staticcache.ini/StaticPublishSettings]\nTargetList[]=pub\nTargetList[]=docs\n", $ini );
        $this->assertStringContainsString( "[staticcache.ini/Target_pub]\nName=Public site\nSiteAccess=site\n", $ini );
        $this->assertStringContainsString( "Credential=token:github_pages_token\n", $ini );
        $this->assertStringContainsString( "Extras[]\nExtras[]=nojekyll\nExtras[]=404\n", $ini );
        $this->assertStringContainsString( "[staticcache.ini/Target_docs]\n", $ini );
        $this->assertStringContainsString( "ExcludePaths[]\nExcludeSections[]\n", $ini );
        $this->assertStringNotContainsString( 'Enabled=', $ini, 'static publishing is not switched on by a migration' );
    }
}
