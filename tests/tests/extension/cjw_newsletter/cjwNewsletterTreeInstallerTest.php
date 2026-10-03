<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * CjwNewsletterClassInstaller::installTree(): the newsletter tree (root, system, list) of a new installation.
 * Live-database style: the existing tree of the installation must be found and left alone, and a new tree is
 * created only under a throwaway folder below Media (node 43), which tearDown removes with everything below it.
 */
class cjwNewsletterTreeInstallerTest extends cjwNewsletterTestCase
{
    const MEDIA_NODE_ID = 43;

    protected $folderNodeIds = array();
    protected $settingFiles = array();

    public function tearDown(): void
    {
        if ( self::$bootError === null )
        {
            $this->loginAdmin();
            if ( $this->folderNodeIds )
                eZContentObjectTreeNode::removeSubtrees( $this->folderNodeIds, false );
            $this->folderNodeIds = array();
            foreach ( $this->settingFiles as $file )
                if ( file_exists( $file ) )
                    unlink( $file );
        }
        parent::tearDown();
    }

    protected function newFolder()
    {
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => self::MEDIA_NODE_ID,
                                                                     'class_identifier' => 'folder',
                                                                     'attributes' => array( 'name' => 'nltree-' . uniqid() ) ) );
        $this->assertNotFalse( $object );
        $this->folderNodeIds[] = (int)$object->attribute( 'main_node_id' );
        return (int)$object->attribute( 'main_node_id' );
    }

    public function testExistingTreeIsFoundAndNothingIsCreated()
    {
        $configured = (int)eZINI::instance( 'cjw_newsletter.ini' )->variable( 'NewsletterSettings', 'RootFolderNodeId' );
        $before = (int)eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject' )[0]['c'];
        $report = array();
        $root = CjwNewsletterClassInstaller::installTree( null, $report );
        $this->assertSame( $configured, $root );
        $this->assertSame( 'already present', $report['root'] );
        $this->assertSame( 'already present', $report['system'] );
        $this->assertSame( 'already present', $report['list'] );
        $this->assertSame( $before, (int)eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject' )[0]['c'] );
    }

    public function testTreeIsCreatedBelowAFolderAndASecondRunChangesNothing()
    {
        $folder = $this->newFolder();
        $report = array();
        $root = CjwNewsletterClassInstaller::installTree( $folder, $report );
        $this->assertGreaterThan( 0, $root );
        $this->assertSame( 'created', $report['root'] );
        $this->assertSame( 'created', $report['system'] );
        $this->assertSame( 'created', $report['list'] );

        $rootNode = eZContentObjectTreeNode::fetch( $root );
        $this->assertSame( 'cjw_newsletter_root', $rootNode->attribute( 'class_identifier' ) );
        $this->assertSame( $folder, (int)$rootNode->attribute( 'parent_node_id' ) );
        $system = CjwNewsletterClassInstaller::findChildNode( $root, 'cjw_newsletter_system' );
        $this->assertNotFalse( $system );
        $list = CjwNewsletterClassInstaller::findChildNode( $system->attribute( 'node_id' ), 'cjw_newsletter_list' );
        $this->assertNotFalse( $list );

        $section = eZSection::fetch( $rootNode->attribute( 'object' )->attribute( 'section_id' ) );
        $this->assertSame( 'CJW Newsletter', $section->attribute( 'name' ) );
        $this->assertSame( 'eznewsletternavigationpart', $section->attribute( 'navigation_part_identifier' ) );

        $settings = CjwNewsletterList::fetchByListObjectVersion( $list->attribute( 'contentobject_id' ), 0 );
        $this->assertInstanceOf( 'CjwNewsletterList', $settings );
        $this->assertSame( eZINI::instance( 'site.ini' )->variable( 'SiteSettings', 'DefaultAccess' ), $settings->attribute( 'main_siteaccess' ) );
        $this->assertSame( array( 0 => 'HTML', 1 => 'Text' ), $settings->attribute( 'output_format_array' ) );
        $this->assertNotSame( '', $settings->attribute( 'email_sender' ) );

        $again = array();
        $this->assertSame( $root, CjwNewsletterClassInstaller::installTree( $folder, $again ) );
        $this->assertSame( array( 'root' => 'already present', 'system' => 'already present', 'list' => 'already present' ),
                           array_intersect_key( $again, array_flip( array( 'root', 'system', 'list' ) ) ) );
    }

    public function testRootFolderSettingIsCreatedAndOtherSettingsStay()
    {
        $file = eZSys::rootDir() . '/var/tmp/nltree-' . uniqid() . '.ini.append.php';
        $this->settingFiles[] = $file;
        $this->assertSame( 'created', CjwNewsletterClassInstaller::writeRootFolderSetting( 5, $file ) );
        $this->assertStringContainsString( "[NewsletterSettings]\nRootFolderNodeId=5\n", file_get_contents( $file ) );
        $this->assertSame( 'unchanged', CjwNewsletterClassInstaller::writeRootFolderSetting( 5, $file ) );

        file_put_contents( $file, "<?php /* #?ini charset=\"utf-8\"?\n\n[NewsletterMailSettings]\nEmailSubjectPrefix=[x]\n\n*/ ?>\n" );
        $this->assertSame( 'updated', CjwNewsletterClassInstaller::writeRootFolderSetting( 7, $file ) );
        $text = file_get_contents( $file );
        $this->assertStringContainsString( "EmailSubjectPrefix=[x]", $text );
        $this->assertStringContainsString( "[NewsletterSettings]\nRootFolderNodeId=7", $text );

        $this->assertSame( 'updated', CjwNewsletterClassInstaller::writeRootFolderSetting( 9, $file ) );
        $this->assertSame( 1, substr_count( file_get_contents( $file ), 'RootFolderNodeId=' ) );
        $this->assertStringContainsString( 'RootFolderNodeId=9', file_get_contents( $file ) );
    }
}
