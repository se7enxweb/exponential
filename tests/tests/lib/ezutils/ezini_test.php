<?php
/**
 * File containing the eZINITest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZINITest extends ezpTestCase
{

    public function testSavePreservesCommentsInDirectAccessMode()
    {
        $rootPath = realpath( __DIR__ . '/../../../../' );
        $tmpRelDir = 'var/tmp/ezini_roundtrip_' . uniqid( '', true );
        $tmpDir = $rootPath . '/' . $tmpRelDir;
        mkdir( $tmpDir, 0777, true );

        $fileName = 'commented.ini.append.php';
        $filePath = $tmpDir . '/' . $fileName;
        $content = "<?php /* #?ini charset=\"utf8\"?\n\n"
                 . "# top comment\n"
                 . "[SiteSettings]\n"
                 . "# keep this comment\n"
                 . "SiteName=old ## inline explanation\n"
                 . "SiteList[]=one\n"
                 . "# keep section tail\n"
                 . "*/ ?>";
        file_put_contents( $filePath, $content );

        $ini = new eZINI( $fileName, $tmpRelDir, null, false, false, true, false, true );
        $ini->setVariable( 'SiteSettings', 'SiteName', 'new' );
        $ini->setVariable( 'SiteSettings', 'SiteList', array( 'one', 'two' ) );

        $this->assertTrue( $ini->save() );

        $saved = file_get_contents( $filePath );
        $this->assertStringContainsString( '# top comment', $saved );
        $this->assertStringContainsString( '# keep this comment', $saved );
        $this->assertStringContainsString( '# keep section tail', $saved );
        $this->assertStringContainsString( 'SiteName=new', $saved );
        $this->assertStringContainsString( 'SiteList[]=one', $saved );
        $this->assertStringContainsString( 'SiteList[]=two', $saved );

        $this->cleanupTmpDir( $tmpDir );
    }

    public function testSaveAppendsNewSettingWithoutDroppingComments()
    {
        $rootPath = realpath( __DIR__ . '/../../../../' );
        $tmpRelDir = 'var/tmp/ezini_roundtrip_' . uniqid( '', true );
        $tmpDir = $rootPath . '/' . $tmpRelDir;
        mkdir( $tmpDir, 0777, true );

        $fileName = 'append_comment.ini.append.php';
        $filePath = $tmpDir . '/' . $fileName;
        $content = "<?php /* #?ini charset=\"utf8\"?\n\n"
                 . "# file intro\n"
                 . "[SiteSettings]\n"
                 . "SiteName=legacy\n"
                 . "# comment between settings\n"
                 . "*/ ?>";
        file_put_contents( $filePath, $content );

        $ini = new eZINI( $fileName, $tmpRelDir, null, false, false, true, false, true );
        $ini->setVariable( 'SiteSettings', 'Additional', 'enabled' );

        $this->assertTrue( $ini->save() );

        $saved = file_get_contents( $filePath );
        $this->assertStringContainsString( '# file intro', $saved );
        $this->assertStringContainsString( '# comment between settings', $saved );
        $this->assertStringContainsString( 'SiteName=legacy', $saved );
        $this->assertStringContainsString( 'Additional=enabled', $saved );

        $this->cleanupTmpDir( $tmpDir );
    }

    public function testSaveRemovesDeletedSettingAndKeepsSectionComments()
    {
        $rootPath = realpath( __DIR__ . '/../../../../' );
        $tmpRelDir = 'var/tmp/ezini_roundtrip_' . uniqid( '', true );
        $tmpDir = $rootPath . '/' . $tmpRelDir;
        mkdir( $tmpDir, 0777, true );

        $fileName = 'delete_comment.ini.append.php';
        $filePath = $tmpDir . '/' . $fileName;
        $content = "<?php /* #?ini charset=\"utf8\"?\n\n"
                 . "[SiteSettings]\n"
                 . "# survives rewrite\n"
                 . "KeepMe=yes\n"
                 . "DeleteMe=gone\n"
                 . "*/ ?>";
        file_put_contents( $filePath, $content );

        $ini = new eZINI( $fileName, $tmpRelDir, null, false, false, true, false, true );
        $ini->removeSetting( 'SiteSettings', 'DeleteMe' );

        $this->assertTrue( $ini->save() );

        $saved = file_get_contents( $filePath );
        $this->assertStringContainsString( '# survives rewrite', $saved );
        $this->assertStringContainsString( 'KeepMe=yes', $saved );
        $this->assertStringNotContainsString( 'DeleteMe=gone', $saved );

        $this->cleanupTmpDir( $tmpDir );
    }

    public function testSavePreservesCommentsWhenLoadedFromCache()
    {
        $rootPath = realpath( __DIR__ . '/../../../../' );
        $tmpRelDir = 'var/tmp/ezini_roundtrip_' . uniqid( '', true );
        $tmpDir = $rootPath . '/' . $tmpRelDir;
        mkdir( $tmpDir, 0777, true );

        $fileName = 'cached_roundtrip.ini.append.php';
        $filePath = $tmpDir . '/' . $fileName;
        $content = "<?php /* #?ini charset=\"utf8\"?\n\n"
                 . "# cache path comment\n"
                 . "[SiteSettings]\n"
                 . "SiteName=old\n"
                 . "*/ ?>";
        file_put_contents( $filePath, $content );

        // First load warms up ini cache for this file.
        $iniWarmup = new eZINI( $fileName, $tmpRelDir, null, true, false, true, false, true );
        $this->assertEquals( 'old', $iniWarmup->variable( 'SiteSettings', 'SiteName' ) );

        // New instance should be able to preserve comments even if values are restored from cache.
        $ini = new eZINI( $fileName, $tmpRelDir, null, true, false, true, false, true );
        $ini->setVariable( 'SiteSettings', 'SiteName', 'new' );

        $this->assertTrue( $ini->save() );

        $saved = file_get_contents( $filePath );
        $this->assertStringContainsString( '# cache path comment', $saved );
        $this->assertStringContainsString( 'SiteName=new', $saved );

        $this->cleanupTmpDir( $tmpDir );
    }

    public function testSaveRetainsRealSiteIniStructureWhenUpdatingExtensions()
    {
        $rootPath = realpath( __DIR__ . '/../../../../' );
        $tmpRelDir = 'var/tmp/ezini_roundtrip_' . uniqid( '', true );
        $tmpDir = $rootPath . '/' . $tmpRelDir;
        mkdir( $tmpDir, 0777, true );

        $fileName = 'site.ini.append.php';
        $filePath = $tmpDir . '/' . $fileName;
        $content = "<?php /* #?ini charset=\"utf-8\"?\n\n"
                 . "[DebugSettings]\n"
                 . "#DebugOutput=enabled\n\n"
                 . "[TemplateSettings]\n"
                 . "#ShowUsedTemplates=enabled\n\n"
                 . "[FileSettings]\n"
                 . "AllowedDeletionDirs[]=/var/www/vhosts/alpha.se7enx.com/doc/alpha.se7enx.com/var\n"
                 . "VarDir=var/site\n\n"
                 . "[ExtensionSettings]\n"
                 . "ActiveExtensions[]=ezjscore\n"
                 . "ActiveExtensions[]=ezoe\n"
                 . "ActiveExtensions[]=ezformtoken\n"
                 . "ActiveExtensions[]=ezwt\n\n"
                 . "[SiteSettings]\n"
                 . "DefaultAccess=sevenx_site_user\n"
                 . "SiteList[]=sevenx_site_user\n"
                 . "SiteList[]=sevenx_site_admin\n"
                 . "RootNodeDepth=1\n\n"
                 . "[MailSettings]\n"
                 . "Transport=sendmail\n"
                 . "AdminEmail=info@se7enx.com\n"
                 . "EmailSender=\n"
                 . "*/ ?>";
        file_put_contents( $filePath, $content );

        $ini = new eZINI( 'site.ini.append', $tmpRelDir, null, true, false, true, false, true );
        $selectedExtensions = $ini->variable( 'ExtensionSettings', 'ActiveExtensions' );
        $toSave = array_unique( array_merge( $selectedExtensions, array( 'nxc_powercontent' ) ) );

        $ini->setVariable( 'ExtensionSettings', 'ActiveExtensions', $toSave );
        $this->assertTrue( $ini->save( 'site.ini.append', '.php', false, false ) );

        $saved = file_get_contents( $filePath );
        $this->assertStringContainsString( '[DebugSettings]', $saved );
        $this->assertStringContainsString( '#DebugOutput=enabled', $saved );
        $this->assertStringContainsString( '[TemplateSettings]', $saved );
        $this->assertStringContainsString( '#ShowUsedTemplates=enabled', $saved );
        $this->assertStringContainsString( 'ActiveExtensions[]=nxc_powercontent', $saved );
        $this->assertStringContainsString( '[SiteSettings]', $saved );
        $this->assertStringContainsString( 'EmailSender=', $saved );
        $this->assertStringContainsString( '*/ ?>', $saved );

        $this->cleanupTmpDir( $tmpDir );
    }

    public function testSaveRetainsCurrentSiteIniAppendOutsideExtensionSettings()
    {
        $rootPath = realpath( __DIR__ . '/../../../../' );
        $sourcePath = $rootPath . '/settings/override/site.ini.append.php';
        $this->assertFileExists( $sourcePath );

        $tmpRelDir = 'var/tmp/ezini_roundtrip_' . uniqid( '', true );
        $tmpDir = $rootPath . '/' . $tmpRelDir;
        mkdir( $tmpDir, 0777, true );

        $tmpPath = $tmpDir . '/site.ini.append.php';
        copy( $sourcePath, $tmpPath );

        $before = file_get_contents( $tmpPath );

        $ini = new eZINI( 'site.ini.append', $tmpRelDir, null, true, false, true, false, true );
        $selectedExtensions = $ini->variable( 'ExtensionSettings', 'ActiveExtensions' );
        $toSave = array_unique( array_merge( $selectedExtensions, array( 'test_roundtrip_extension' ) ) );

        $ini->setVariable( 'ExtensionSettings', 'ActiveExtensions', $toSave );
        $this->assertTrue( $ini->save( 'site.ini.append', '.php', false, false ) );

        $after = file_get_contents( $tmpPath );
        $beforeWithoutExtensions = $this->stripExtensionSettingsBody( $before );
        $afterWithoutExtensions = $this->stripExtensionSettingsBody( $after );

        $this->assertSame( $beforeWithoutExtensions, $afterWithoutExtensions );
        $this->assertStringContainsString( 'ActiveExtensions[]=test_roundtrip_extension', $after );

        $this->cleanupTmpDir( $tmpDir );
    }

    private function stripExtensionSettingsBody( $contents )
    {
        return preg_replace(
            '/(\[ExtensionSettings\]\n)(.*?)(?=\n\[[^\]]+\]|\n\*\/ \?>|\z)/s',
            '$1<EXTENSION_SETTINGS_BODY>',
            $contents
        );
    }

    private function cleanupTmpDir( $tmpDir )
    {
        if ( !is_dir( $tmpDir ) )
            return;

        foreach ( glob( $tmpDir . '/*' ) as $tmpFile )
        {
            if ( is_file( $tmpFile ) )
                unlink( $tmpFile );
        }

        rmdir( $tmpDir );
    }

    /**
     * Test to make sure default override dirs only contain 'override' folder
     */
    public function testDefaultOverrideDirs()
    {
        $ini = new eZINI( 'site.ini', 'settings', null, null, true );
        $ini->resetOverrideDirs();

        // test that we only get one override dir and it's value is 'settings/override'
        $overrideDirs = $ini->overrideDirs();
        self::assertEquals( 1, count( $overrideDirs ) );
        self::assertEquals( 'override', $overrideDirs[0][0] );
        self::assertFalse( $overrideDirs[0][1] );
    }

    /**
     * Test to make sure default override dirs are same as raw structure after reset
     */
    public function testRawOverrideDirs()
    {
        $ini = new eZINI( 'site.ini', 'settings', null, null, true );
        $ini->resetOverrideDirs();

        // make sure raw structure is same as default structure
        self::assertEquals( eZINI::defaultOverrideDirs(), $ini->overrideDirs( false ), 'Override array should be same as default override array structure' );
    }

    /**
     * Test prepending siteaccess dirs
     */
    public function testOverrideDirScopesSiteaccess()
    {
        $ini = new eZINI( 'site.ini', 'settings', null, null, true );
        $ini->resetOverrideDirs();

        $ini->prependOverrideDir( "siteaccess/eng", false, 'siteaccess' );
        $ini->prependOverrideDir( "extension/ext1/settings/siteaccess/eng", true );
        $ini->appendOverrideDir( "extension/ext3/settings/siteaccess/eng", true );
        $ini->prependOverrideDir( "siteaccess/nor", false, 'siteaccess' );// will override first dir

        $overrideDirs = $ini->overrideDirs( false );
        self::assertEquals( 4, count( $ini->overrideDirs() ), 'There should have been three override dirs in total in this ini instance.' );
        self::assertEquals( 3, count( $overrideDirs['siteaccess'] ), 'There should have been two override dirs in siteaccess scope.' );

        self::assertTrue( $overrideDirs['siteaccess'][0][1], "This override dir '" . $overrideDirs['siteaccess'][0][0] . "' should have been global(true)" );

        self::assertEquals( "siteaccess/nor", $overrideDirs['siteaccess']['siteaccess'][0], "Siteaccess should have been overridden by identifier" );

        self::assertEquals( "extension/ext3/settings/siteaccess/eng", $overrideDirs['siteaccess'][1][0] );
    }

    /**
     * Test prepending extension dirs
     */
    public function testOverrideDirScopesExtension()
    {
        $ini = new eZINI( 'site.ini', 'settings', null, null, true );
        $ini->resetOverrideDirs();

        $ini->prependOverrideDir( "extension/ext1/settings", true, 'extension:ext1', 'extension' );
        $ini->prependOverrideDir( "extension/ext2/settings", true, 'extension:ext2', 'extension' );
        $ini->prependOverrideDir( "extension/ext3/settings", true, 'extension:ext3', 'extension' );
        // should override prev use of :ext1
        $ini->prependOverrideDir( "extension/ext1/settings", true, 'extension:ext1', 'extension' );
        // will not be part of override dirs in output as it uses same identifier as an extension
        $ini->prependOverrideDir( "extension/ext1/settings", true, 'extension:ext1', 'sa-extension' );

        $overrideDirs = $ini->overrideDirs( false );
        self::assertEquals( 4, count( $ini->overrideDirs() ), 'There should have been four override dirs in total in this ini instance.' );
        self::assertEquals( 3, count( $overrideDirs['extension'] ), 'There should have been three override dirs in extension scope.' );
        self::assertEquals( 1, count( $overrideDirs['sa-extension'] ), 'There should have been one override dir in sa-extension scope.' );
    }

    /**
     * Test prepending extension dirs and removing
     */
    public function testOverrideDirScopesExtensionRemove()
    {
        $ini = new eZINI( 'site.ini', 'settings', null, null, true );
        $ini->resetOverrideDirs();

        $ini->prependOverrideDir( "extension/ext1/settings", true, 'extension:ext1', 'extension' );
        $ini->prependOverrideDir( "extension/ext2/settings", true, 'extension:ext2', 'extension' );
        $ini->prependOverrideDir( "extension/ext3/settings", true, 'extension:ext3', 'extension' );
        $ini->prependOverrideDir( "extension/ext1/settings", true, 'extension:ext1', 'sa-extension' );

        $ini->removeOverrideDir( 'extension:ext1' );
        $success = $ini->removeOverrideDir( 'extension:ext1', 'sa-extension' );
        $failed  = $ini->removeOverrideDir( 'extension:ext8' );

        $overrideDirs = $ini->overrideDirs( false );
        self::assertEquals( 3, count( $ini->overrideDirs() ), 'There should have been three override dirs in total in this ini instance.' );
        self::assertEquals( 2, count( $overrideDirs['extension'] ), 'There should have been two override dirs in extension scope.' );
        self::assertEquals( 0, count( $overrideDirs['sa-extension'] ), 'There should have been 0 override dirs in sa-extension scope.' );
        self::assertTrue( $success, '$ini->removeOverrideDir( \'extension:ext1\', \'sa-extension\' ) should have been returned true as identifier does exist.' );
        self::assertFalse( $failed, '$ini->removeOverrideDir( \'extension:ext8\' ) should have been returned false as identifier does not exist.' );
    }
}

// Compatibility alias for PHPUnit runners that infer class names from *_test.php filenames.
if ( !class_exists( 'ezini_test', false ) )
{
    class ezini_test extends eZINITest
    {
    }
}

?>
