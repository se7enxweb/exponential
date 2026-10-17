<?php
/**
 * The Setup tab and menu.ini [TopAdminMenu] SetupTabs[] ask the same question: may the user open the Setup overview
 * (setup/menu)? The overview opens with any of setup/setup, administrate, managecache, managecronjobs, preload or
 * system_info. A user with setup/system_info only, for example, has the Setup tab and finds the SetupTabs[] there;
 * before, [Topmenu_setup] PolicyList[]=setup/managecache took the Setup tab from that user while SetupTabs[] still
 * left the top menu, and the tabs were nowhere.
 *
 * No database: the user is a stub whose policies are the test's, [Topmenu_setup] is the one in settings/menu.ini,
 * and the address of the tab is checked as the kernel checks the request (expViewAccess, CheckViewAccess=enabled).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZTopMenuOperatorSetupTabAccessTest extends PHPUnit\Framework\TestCase
{
    const MOVED = 'x535movedtab';

    private $saved = array();
    private $savedUser;
    private $savedModulePaths;

    protected function setUp(): void
    {
        $menu = eZINI::instance( 'menu.ini' );
        $site = eZINI::instance();
        foreach ( array( array( $menu, 'TopAdminMenu', 'Tabs' ), array( $menu, 'TopAdminMenu', 'HiddenTabs' ), array( $menu, 'TopAdminMenu', 'SetupTabs' ),
                         array( $menu, 'MenuAccessSettings', 'CheckViewAccess' ), array( $site, 'URLTranslator', 'Translation' ),
                         array( $site, 'SiteAccessSettings', 'RequireUserLogin' ) ) as $setting )
            $this->saved[] = array( $setting, $setting[0]->hasVariable( $setting[1], $setting[2] ) ? array( $setting[0]->variable( $setting[1], $setting[2] ) ) : null );

        $this->assertTrue( $menu->hasGroup( 'Topmenu_setup' ), 'settings/menu.ini has the Setup tab' );
        $menu->setVariable( 'MenuAccessSettings', 'CheckViewAccess', 'enabled' );
        $menu->setVariable( 'TopAdminMenu', 'Tabs', array( 'setup', self::MOVED ) );
        $menu->setVariable( 'TopAdminMenu', 'HiddenTabs', array() );
        $menu->setVariable( 'TopAdminMenu', 'SetupTabs', array( self::MOVED ) );
        $menu->setVariable( 'Topmenu_' . self::MOVED, 'URL', array( 'default' => 'setup/info' ) );
        $menu->setVariable( 'Topmenu_' . self::MOVED, 'Enabled', array( 'default' => 'true' ) );
        $menu->setVariable( 'Topmenu_' . self::MOVED, 'Shown', array( 'default' => 'true' ) );
        $menu->setVariable( 'Topmenu_' . self::MOVED, 'NavigationPartIdentifier', 'ezsetupnavigationpart' );
        $menu->setVariable( 'Topmenu_' . self::MOVED, 'Name', self::MOVED );
        // no URL alias lookup (it needs the database); the stub user is signed in
        $site->setVariable( 'URLTranslator', 'Translation', 'disabled' );
        $site->setVariable( 'SiteAccessSettings', 'RequireUserLogin', 'false' );

        $this->savedUser = isset( $GLOBALS['eZUserGlobalInstance_'] ) ? $GLOBALS['eZUserGlobalInstance_'] : null;
        // the kernel modules, as eZScript would set them (setup/menu must be found)
        $this->savedModulePaths = eZModule::globalPathList();
        eZModule::setGlobalPathList( array( dirname( __DIR__, 4 ) . '/kernel' ) );
    }

    protected function tearDown(): void
    {
        $GLOBALS['eZUserGlobalInstance_'] = $this->savedUser;
        if ( $this->savedModulePaths === null )
            unset( $GLOBALS['eZModuleGlobalPathList'] );
        else
            eZModule::setGlobalPathList( $this->savedModulePaths );
        eZINI::instance( 'menu.ini' )->removeGroup( 'Topmenu_' . self::MOVED );
        foreach ( $this->saved as list( $setting, $value ) )
        {
            if ( $value === null )
                $setting[0]->removeSetting( $setting[1], $setting[2] );
            else
                $setting[0]->setVariable( $setting[1], $setting[2], $value[0] );
        }
    }

    /**
     * The top menu of a signed-in user who has exactly $granted ("module/function"), as admin4 asks for it
     * (filter_on_access): the tab addresses.
     */
    private static function tabsFor( array $granted )
    {
        $GLOBALS['eZUserGlobalInstance_'] = new class( $granted ) extends eZUser
        {
            private $granted;
            public function __construct( array $granted ) { $this->granted = $granted; }
            public function isAnonymous() { return false; }
            public function isEnabled( $useCache = true ) { return true; }
            public function attribute( $attr, $noFunction = false ) { return $attr === 'contentobject_id' ? 14000535 : null; }
            public function hasAccessTo( $module, $function = false )
            {
                return array( 'accessWord' => in_array( "$module/$function", $this->granted, true ) ? 'yes' : 'no',
                              'accessList' => array( 'FunctionRequired' => array( 'Module' => $module, 'Function' => $function ) ) );
            }
        };
        $menu = null;
        $operator = new eZTopMenuOperator();
        $operator->modify( null, 'topmenu', array(), '', '', $menu, array( 'context' => 'content', 'filter_on_access' => true ), null );
        return array_column( $menu, 'url' );
    }

    public function testAUserWithSystemInformationOnlyHasTheSetupTabThatHoldsTheSetupTabs()
    {
        $this->assertSame( array( 'setup/menu' ), self::tabsFor( array( 'user/login', 'setup/system_info' ) ) );
    }

    public function testAUserWithCacheManagementHasTheSetupTab()
    {
        $this->assertSame( array( 'setup/menu' ), self::tabsFor( array( 'user/login', 'setup/managecache' ) ) );
    }

    public function testAUserWithoutAnySetupFunctionHasNoSetupTab()
    {
        $this->assertSame( array(), self::tabsFor( array( 'user/login', 'content/read' ) ) );
    }

    public function testTheSetupTabAsksNoPolicyThatItsAddressDoesNotAsk()
    {
        // [Topmenu_setup] PolicyList[] would come on top of the view's own functions (setup/menu opens with any one
        // of six); SetupTabs[] follow who may open setup/menu, so the tab must follow it too
        $this->assertFalse( eZINI::instance( 'menu.ini' )->hasVariable( 'Topmenu_setup', 'PolicyList' ) );
    }
}
