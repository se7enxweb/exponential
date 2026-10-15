<?php
/**
 * menu.ini [TopAdminMenu] SetupTabs[]: a tab that moves into the Setup menu for a user who may open the Setup
 * overview (setup/menu) is left out of that user's top menu only; a user who may not open the overview keeps it.
 * No database: whether the user may open setup/menu is the test's answer, the tabs are the test's own.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZTopMenuOperatorSetupTabsTest extends PHPUnit\Framework\TestCase
{
    const MOVED = 'x200movedtab';
    const KEPT = 'x200kepttab';

    private $saved = array();

    protected function setUp(): void
    {
        $ini = eZINI::instance( 'menu.ini' );
        foreach ( array( array( 'TopAdminMenu', 'Tabs' ), array( 'TopAdminMenu', 'HiddenTabs' ), array( 'TopAdminMenu', 'SetupTabs' ),
                         array( 'MenuAccessSettings', 'CheckViewAccess' ) ) as $setting )
            $this->saved[] = array( $setting, $ini->hasVariable( $setting[0], $setting[1] ) ? array( $ini->variable( $setting[0], $setting[1] ) ) : null );

        $ini->setVariable( 'MenuAccessSettings', 'CheckViewAccess', 'disabled' );
        $ini->setVariable( 'TopAdminMenu', 'Tabs', array( self::MOVED, self::KEPT ) );
        $ini->setVariable( 'TopAdminMenu', 'HiddenTabs', array() );
        $ini->setVariable( 'TopAdminMenu', 'SetupTabs', array( self::MOVED ) );
        foreach ( array( self::MOVED, self::KEPT ) as $tab )
        {
            $ini->setVariable( 'Topmenu_' . $tab, 'URL', array( 'default' => "$tab/list" ) );
            $ini->setVariable( 'Topmenu_' . $tab, 'Enabled', array( 'default' => 'true' ) );
            $ini->setVariable( 'Topmenu_' . $tab, 'Shown', array( 'default' => 'true' ) );
            $ini->setVariable( 'Topmenu_' . $tab, 'NavigationPartIdentifier', 'ezcontentnavigationpart' );
            $ini->setVariable( 'Topmenu_' . $tab, 'Name', $tab );
        }
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'menu.ini' );
        $ini->removeGroup( 'Topmenu_' . self::MOVED );
        $ini->removeGroup( 'Topmenu_' . self::KEPT );
        foreach ( $this->saved as list( $setting, $value ) )
        {
            if ( $value === null )
                $ini->removeSetting( $setting[0], $setting[1] );
            else
                $ini->setVariable( $setting[0], $setting[1], $value[0] );
        }
    }

    private static function tabs( $mayOpenSetup )
    {
        $operator = new class( $mayOpenSetup ) extends eZTopMenuOperator
        {
            private $may;
            public function __construct( $may ) { parent::__construct(); $this->may = $may; }
            protected function canOpenSetupOverview() { return $this->may; }
        };
        $menu = null;
        $operator->modify( null, 'topmenu', array(), '', '', $menu, array( 'context' => 'content', 'filter_on_access' => false ), null );
        return array_column( $menu, 'url' );
    }

    public function testAUserWhoMayOpenSetupHasNoTabForASetupTab()
    {
        $this->assertSame( array( self::KEPT . '/list' ), self::tabs( true ) );
    }

    public function testAUserWhoMayNotOpenSetupKeepsTheTab()
    {
        $this->assertSame( array( self::MOVED . '/list', self::KEPT . '/list' ), self::tabs( false ) );
    }

    public function testAUserWithoutAnyTabGetsAnEmptyMenu()
    {
        eZINI::instance( 'menu.ini' )->setVariable( 'TopAdminMenu', 'SetupTabs', array( self::MOVED, self::KEPT ) );
        $this->assertSame( array(), self::tabs( true ) );
    }

    public function testTheTemplatesAskTheSameQuestion()
    {
        $root = dirname( __DIR__, 4 );
        foreach ( array( 'design/admin4/templates/parts/ini_menu.tpl', 'design/admin4/templates/parts/a4_navigation_part.tpl',
                         'design/admin4/templates/page_topmenu.tpl', 'design/admin4l/templates/explayouts/block/admin_logo.tpl' ) as $path )
        {
            $tpl = (string)file_get_contents( "$root/$path" );
            $this->assertStringContainsString( "ezini( 'TopAdminMenu', 'SetupTabs', 'menu.ini' )", $tpl, $path );
            $this->assertStringContainsString( "fetch( 'user', 'can_open', hash( 'uri', 'setup/menu' ) )", $tpl, $path );
        }
        $this->assertStringContainsString( "expViewAccess::canOpen( 'setup/menu' )", (string)file_get_contents( "$root/kernel/common/eztopmenuoperator.php" ) );
        // the header is cached per role set: who may open setup/menu is decided by the roles
        $this->assertMatchesRegularExpression( '/\$user_hash = concat\( \$current_user\.role_id_list\|implode/', (string)file_get_contents( "$root/design/admin4/templates/pagelayout.tpl" ) );
    }
}
