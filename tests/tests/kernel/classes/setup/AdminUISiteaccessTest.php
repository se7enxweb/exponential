<?php
/**
 * The adminui siteaccess the setup makes from the admin siteaccess when the exp_adminui extension is in the
 * installation: what files and settings it gets, that it is reached by URI only (no host, port or host match
 * entry), and that nothing is written without the extension. No database: the settings are written into a
 * throwaway directory under var/tmp, never into the installation's settings.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminUISiteaccessTest extends PHPUnit\Framework\TestCase
{
    /** @var string relative to the installation root, as the setup's own paths are (eZINI reads relative to it) */
    private $root;

    /** @var string */
    private $cwd;

    protected function setUp(): void
    {
        eZStepCreateSites::$AdminUISiteAccessesMade = array();
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $this->root = 'var/tmp/adminui-siteaccess-test-' . getmypid() . '-' . mt_rand();
        mkdir( $this->root . '/settings/siteaccess/admin', 0777, true );
        mkdir( $this->root . '/extension', 0777, true );
        $admin = $this->root . '/settings/siteaccess/admin';
        file_put_contents( $admin . '/site.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n"
            . "[DatabaseSettings]\nDatabaseImplementation=ezmysqli\nServer=db.example.com\nDatabase=exp\n\n"
            . "[SiteSettings]\nSiteName=Admin\nSiteURL=example.com/admin\nDefaultPage=content/dashboard\nLoginPage=custom\n\n"
            . "[SiteAccessSettings]\nRequireUserLogin=true\nRelatedSiteAccessList[]\nRelatedSiteAccessList[]=site\nRelatedSiteAccessList[]=admin\nShowHiddenNodes=true\nForceVirtualHost=true\n\n"
            . "[DesignSettings]\nSiteDesign=admin4l\nAdditionalSiteDesignList[]\nAdditionalSiteDesignList[]=admin4\nAdditionalSiteDesignList[]=admin3\n\n"
            . "[RegionalSettings]\nLocale=ger-DE\nContentObjectLocale=ger-DE\nSiteLanguageList[]\nSiteLanguageList[]=ger-DE\nSiteLanguageList[]=eng-US\n\n"
            . "[FileSettings]\nVarDir=var/site\n*/ ?>\n" );
        file_put_contents( $admin . '/icon.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[IconSettings]\nTheme=crystal-admin\n*/ ?>\n" );
        file_put_contents( $admin . '/ezoe.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[EditorSettings]\nSkinVariant=silver\n*/ ?>\n" );
        file_put_contents( $admin . '/menu.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[TopAdminMenu]\nTabs[]\nTabs[]=dashboard\nTabs[]=content\n*/ ?>\n" );
        file_put_contents( $admin . '/toolbar.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[Toolbar_admin_right]\nTool[]\nTool[]=admin_clear_cache\n*/ ?>\n" );
        file_put_contents( $admin . '/override.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[tiny_image]\nSource=content/view/tiny.tpl\n*/ ?>\n" );
        file_put_contents( $admin . '/contentstructuremenu.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[TreeMenu]\nShowClasses[]\nShowClasses[]=folder\n*/ ?>\n" );
    }

    protected function tearDown(): void
    {
        $this->removeTree( $this->root );
        chdir( $this->cwd );
    }

    private function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( scandir( $dir ) as $entry )
        {
            if ( $entry === '.' || $entry === '..' )
                continue;
            $path = $dir . '/' . $entry;
            is_dir( $path ) ? $this->removeTree( $path ) : unlink( $path );
        }
        rmdir( $dir );
    }

    private function withExtension()
    {
        mkdir( $this->root . '/extension/exp_adminui', 0777, true );
    }

    private function make()
    {
        return eZStepCreateSites::createAdminUISiteAccess( 'admin', $this->root . '/settings/siteaccess', $this->root . '/extension', false );
    }

    private function ini( $file )
    {
        return new eZINI( $file, $this->root . '/settings/siteaccess/adminui', null, false, null, true, false );
    }

    public function testNothingIsWrittenWithoutTheExtension()
    {
        $this->assertFalse( eZStepCreateSites::adminUIAvailable( $this->root . '/extension' ) );
        $this->assertNull( $this->make() );
        $this->assertDirectoryDoesNotExist( $this->root . '/settings/siteaccess/adminui' );
    }

    public function testEveryAdminFileIsThereAndIconAndEditorSettingsAreEmptied()
    {
        $this->withExtension();
        $this->assertTrue( eZStepCreateSites::adminUIAvailable( $this->root . '/extension' ) );
        $this->assertTrue( $this->make() );
        $files = array_map( 'basename', glob( $this->root . '/settings/siteaccess/adminui/*.ini.append.php' ) );
        sort( $files );
        $this->assertSame( array( 'contentstructuremenu.ini.append.php', 'ezoe.ini.append.php', 'icon.ini.append.php', 'menu.ini.append.php',
                                  'override.ini.append.php', 'site.ini.append.php', 'toolbar.ini.append.php' ), $files );
        foreach ( array( 'menu.ini.append.php', 'toolbar.ini.append.php', 'override.ini.append.php', 'contentstructuremenu.ini.append.php' ) as $file )
        {
            $this->assertFileEquals( $this->root . '/settings/siteaccess/admin/' . $file, $this->root . '/settings/siteaccess/adminui/' . $file, $file );
        }
        $this->assertFalse( $this->ini( 'icon.ini.append.php' )->hasVariable( 'IconSettings', 'Theme' ) );
        $this->assertFalse( $this->ini( 'ezoe.ini.append.php' )->hasVariable( 'EditorSettings', 'SkinVariant' ) );
    }

    public function testSiteSettingsAreTheAdminsWithTheAdminUIDesign()
    {
        $this->withExtension();
        $this->make();
        $site = $this->ini( 'site.ini.append.php' );
        $this->assertSame( 'adminui', $site->variable( 'DesignSettings', 'SiteDesign' ) );
        $this->assertSame( array( 'admin4l', 'admin4', 'admin3', 'admin2', 'admin' ), $site->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) );
        $this->assertSame( array( 'exp_adminui' ), $site->variable( 'ExtensionSettings', 'ActiveAccessExtensions' ) );
        $this->assertSame( 'Admin UI', $site->variable( 'SiteSettings', 'SiteName' ) );
        $this->assertSame( 'example.com/adminui', $site->variable( 'SiteSettings', 'SiteURL' ) );
        // as the admin has them
        $this->assertSame( 'true', $site->variable( 'SiteAccessSettings', 'RequireUserLogin' ) );
        $this->assertSame( 'true', $site->variable( 'SiteAccessSettings', 'ShowHiddenNodes' ) );
        $this->assertSame( 'custom', $site->variable( 'SiteSettings', 'LoginPage' ) );
        $this->assertSame( 'db.example.com', $site->variable( 'DatabaseSettings', 'Server' ) );
        $this->assertSame( 'exp', $site->variable( 'DatabaseSettings', 'Database' ) );
        $this->assertSame( array( 'ger-DE', 'eng-US' ), $site->variable( 'RegionalSettings', 'SiteLanguageList' ) );
        $this->assertSame( 'var/site', $site->variable( 'FileSettings', 'VarDir' ) );
        $this->assertSame( array( 'site', 'admin' ), $site->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) );
        // URI matching only: nothing in the siteaccess says host or port
        $this->assertFalse( $site->hasVariable( 'SiteAccessSettings', 'HostMatchMapItems' ) );
        $this->assertFalse( $site->hasGroup( 'PortAccessSettings' ) );
        // the admin's own files are left as they were
        $admin = new eZINI( 'site.ini.append.php', $this->root . '/settings/siteaccess/admin', null, false, null, true, false );
        $this->assertSame( 'admin4l', $admin->variable( 'DesignSettings', 'SiteDesign' ) );
    }

    public function testAMadeSiteaccessIsNotMadeTwiceInOneRequest()
    {
        $this->withExtension();
        $this->assertTrue( $this->make() );
        file_put_contents( $this->root . '/settings/siteaccess/adminui/marker.ini.append.php', "<?php /* */ ?>\n" );
        unlink( $this->root . '/settings/siteaccess/adminui/menu.ini.append.php' );
        $this->assertTrue( $this->make() );
        $this->assertFileDoesNotExist( $this->root . '/settings/siteaccess/adminui/menu.ini.append.php' );
    }

    public function testAReinstallRewritesAnEarlierAdminUISiteaccess()
    {
        $this->withExtension();
        mkdir( $this->root . '/settings/siteaccess/adminui' );
        file_put_contents( $this->root . '/settings/siteaccess/adminui/icon.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[IconSettings]\nTheme=crystal-admin\n*/ ?>\n" );
        file_put_contents( $this->root . '/settings/siteaccess/adminui/site.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[DatabaseSettings]\nServer=old.example.com\n*/ ?>\n" );
        $this->assertTrue( $this->make() );
        $this->assertSame( 'db.example.com', $this->ini( 'site.ini.append.php' )->variable( 'DatabaseSettings', 'Server' ) );
        $this->assertFalse( $this->ini( 'icon.ini.append.php' )->hasVariable( 'IconSettings', 'Theme' ) );
    }

    public function testNoAdminSiteaccessMeansFailureNotAnEmptySiteaccess()
    {
        $this->withExtension();
        $this->assertFalse( eZStepCreateSites::createAdminUISiteAccess( 'nosuchadmin', $this->root . '/settings/siteaccess', $this->root . '/extension', false ) );
        $this->assertFileDoesNotExist( $this->root . '/settings/siteaccess/adminui/site.ini.append.php' );
    }

    public function testSiteURLIsTheAdminAddressWithTheAdminUIPath()
    {
        $this->assertSame( 'example.com/adminui', eZStepCreateSites::adminUISiteURL( 'example.com/admin', 'admin' ) );
        $this->assertSame( 'example.com/adminui', eZStepCreateSites::adminUISiteURL( 'https://example.com/admin/', 'admin' ) );
        $this->assertSame( 'admin.example.com/adminui', eZStepCreateSites::adminUISiteURL( 'admin.example.com', 'admin' ) );
        $this->assertSame( 'example.com:8081/adminui', eZStepCreateSites::adminUISiteURL( 'example.com:8081', 'admin' ) );
        $this->assertSame( 'example.com/administration/adminui', eZStepCreateSites::adminUISiteURL( 'example.com/administration', 'admin' ) );
        $this->assertSame( '', eZStepCreateSites::adminUISiteURL( '', 'admin' ) );
    }

    public function testAccessExtensionsOfTheAdminAreKeptAndExpAdminUIAddedOnce()
    {
        $changes = eZStepCreateSites::adminUISiteINIChanges( 'example.com/admin', 'admin', array( 'other', 'exp_adminui', '' ) );
        $this->assertSame( array( 'other', 'exp_adminui' ), $changes['ExtensionSettings']['ActiveAccessExtensions'] );
        $this->assertArrayNotHasKey( 'HostMatchMapItems', $changes['SiteAccessSettings'] );
        $this->assertArrayNotHasKey( 'PortAccessSettings', $changes );
        $noURL = eZStepCreateSites::adminUISiteINIChanges( '', 'admin' );
        $this->assertArrayNotHasKey( 'SiteURL', $noURL['SiteSettings'] );
    }
}
