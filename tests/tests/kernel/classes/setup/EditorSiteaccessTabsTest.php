<?php
/**
 * The node view tabs of the editor siteaccess the setup makes from the admin siteaccess: its own list starts with
 * an empty AdditionalTabs[], and the settings of an access extension are read before the siteaccess's, so the tabs
 * that the admin's access extensions declare (expai's Intelligence tabs) are written into the editor's list. No
 * database: the extensions are written into a throwaway directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class EditorSiteaccessTabsTest extends PHPUnit\Framework\TestCase
{
    /** @var string relative to the installation root, as the setup's own paths are */
    private $root;

    /** @var string */
    private $cwd;

    protected function setUp(): void
    {
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 5 ) );
        $this->root = 'var/tmp/editor-siteaccess-tabs-test-' . getmypid() . '-' . mt_rand();
        $this->writeSettings( 'withtabs', 'admininterface.ini.append.php', "[WindowControlsSettings]\nAdditionalTabs[]=intelligence\nAdditionalTabs[]=intelligence_media\n\n"
            . "[AdditionalTab_intelligence]\nNavigationPartName=ezcontentnavigationpart\nTitle=Intelligence\n" );
        $this->writeSettings( 'notabs', 'site.ini.append.php', "[SiteSettings]\nSiteName=Unused\n" );
        $this->writeSettings( 'othersection', 'admininterface.ini.append.php', "[NavigationSettings]\nVisibleColumns[]\n" );
        $this->writeSettings( 'repeats', 'admininterface.ini.append.php', "[WindowControlsSettings]\nAdditionalTabs[]=authors\nAdditionalTabs[]=intelligence\nAdditionalTabs[]=extra\n" );
        $this->writeSettings( 'plainini', 'admininterface.ini', "[WindowControlsSettings]\nAdditionalTabs[]=plain\n" );
    }

    protected function tearDown(): void
    {
        $this->removeTree( $this->root );
        chdir( $this->cwd );
    }

    private function writeSettings( $extension, $file, $body )
    {
        $dir = $this->root . '/' . $extension . '/settings';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $text = substr( $file, -4 ) === '.php' ? "<?php /* #?ini charset=\"utf-8\"?\n\n" . $body . "*/ ?>\n" : $body;
        file_put_contents( $dir . '/' . $file, $text );
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

    public function testWithoutAccessExtensionsTheEditorKeepsItsOwnList()
    {
        $this->assertSame( eZStepCreateSites::EDITOR_ADDITIONAL_TABS, eZStepCreateSites::editorAdditionalTabs( array(), $this->root ) );
        $this->assertSame( array( 'roles', 'policies', 'eztags', 'authors' ), eZStepCreateSites::EDITOR_ADDITIONAL_TABS );
    }

    public function testTheTabsOfAnAccessExtensionAreAppended()
    {
        $this->assertSame( array( 'roles', 'policies', 'eztags', 'authors', 'intelligence', 'intelligence_media' ),
                           eZStepCreateSites::editorAdditionalTabs( array( 'withtabs' ), $this->root ) );
    }

    public function testExtensionsWithoutTabsOrMissingAddNothing()
    {
        $this->assertSame( eZStepCreateSites::EDITOR_ADDITIONAL_TABS,
                           eZStepCreateSites::editorAdditionalTabs( array( 'notabs', 'othersection', 'missing', '' ), $this->root ) );
    }

    public function testEachTabOnceInExtensionOrder()
    {
        $this->assertSame( array( 'roles', 'policies', 'eztags', 'authors', 'intelligence', 'intelligence_media', 'extra', 'plain' ),
                           eZStepCreateSites::editorAdditionalTabs( array( 'withtabs', 'repeats', 'plainini', 'withtabs' ), $this->root ) );
    }

    public function testAnExtensionNameCannotLeaveTheExtensionDirectory()
    {
        $this->assertSame( array(), eZStepCreateSites::extensionAdditionalTabs( array( '../' . basename( $this->root ) . '/withtabs', 'with/tabs' ), $this->root ) );
    }

    public function testHiddenTabsAreNotAmongTheEditorsOwnTabs()
    {
        foreach ( eZStepCreateSites::EDITOR_ADDITIONAL_TABS as $tab )
            $this->assertNotContains( $tab, eZStepCreateSites::EDITOR_HIDDEN_TABS, $tab );
    }
}
