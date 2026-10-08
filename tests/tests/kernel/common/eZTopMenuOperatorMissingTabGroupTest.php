<?php
/**
 * The topmenu operator with a tab in [TopAdminMenu] Tabs[] that has no [Topmenu_<tab>] group in menu.ini (a
 * siteaccess naming the tab of an extension that is not active there): the tab is left out, the others are built,
 * and the operator raises no PHP warning ("Trying to access array offset on false"). No database: the tab that is
 * built is the test's own, without a PolicyList.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZTopMenuOperatorMissingTabGroupTest extends PHPUnit\Framework\TestCase
{
    const KNOWN = 'x316knowntab';
    const MISSING = 'x316nosuchtab';

    private $tabs;
    private $hidden;
    private $checkViewAccess;

    protected function setUp(): void
    {
        $ini = eZINI::instance( 'menu.ini' );
        $this->tabs = $ini->hasVariable( 'TopAdminMenu', 'Tabs' ) ? $ini->variable( 'TopAdminMenu', 'Tabs' ) : null;
        $this->hidden = $ini->hasVariable( 'TopAdminMenu', 'HiddenTabs' ) ? $ini->variable( 'TopAdminMenu', 'HiddenTabs' ) : null;
        $this->checkViewAccess = $ini->hasVariable( 'MenuAccessSettings', 'CheckViewAccess' ) ? $ini->variable( 'MenuAccessSettings', 'CheckViewAccess' ) : null;

        $ini->setVariable( 'MenuAccessSettings', 'CheckViewAccess', 'disabled' );
        $ini->setVariable( 'TopAdminMenu', 'Tabs', array( self::MISSING, self::KNOWN ) );
        $ini->setVariable( 'TopAdminMenu', 'HiddenTabs', array() );
        $ini->setVariable( 'Topmenu_' . self::KNOWN, 'URL', array( 'default' => 'content/view/full/2' ) );
        $ini->setVariable( 'Topmenu_' . self::KNOWN, 'Enabled', array( 'default' => 'true' ) );
        $ini->setVariable( 'Topmenu_' . self::KNOWN, 'Shown', array( 'default' => 'true' ) );
        $ini->setVariable( 'Topmenu_' . self::KNOWN, 'NavigationPartIdentifier', 'ezcontentnavigationpart' );
        $ini->setVariable( 'Topmenu_' . self::KNOWN, 'Name', 'Known' );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'menu.ini' );
        $ini->removeGroup( 'Topmenu_' . self::KNOWN );
        if ( $this->tabs === null )
            $ini->removeSetting( 'TopAdminMenu', 'Tabs' );
        else
            $ini->setVariable( 'TopAdminMenu', 'Tabs', $this->tabs );
        if ( $this->hidden === null )
            $ini->removeSetting( 'TopAdminMenu', 'HiddenTabs' );
        else
            $ini->setVariable( 'TopAdminMenu', 'HiddenTabs', $this->hidden );
        if ( $this->checkViewAccess === null )
            $ini->removeSetting( 'MenuAccessSettings', 'CheckViewAccess' );
        else
            $ini->setVariable( 'MenuAccessSettings', 'CheckViewAccess', $this->checkViewAccess );
    }

    public function testTabWithoutGroupIsLeftOutWithoutPhpWarning()
    {
        $this->assertFalse( eZINI::instance( 'menu.ini' )->hasGroup( 'Topmenu_' . self::MISSING ) );

        $warnings = array();
        set_error_handler( function ( $errno, $errstr, $errfile ) use ( &$warnings ) {
            if ( strpos( $errfile, 'eztopmenuoperator.php' ) !== false )
                $warnings[] = $errstr;
            return true;
        }, E_WARNING | E_NOTICE | E_DEPRECATED );
        try
        {
            $operator = new eZTopMenuOperator();
            $menu = null;
            $operator->modify( null, 'topmenu', array(), '', '', $menu, array( 'context' => 'content', 'filter_on_access' => false ), null );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $warnings );
        $this->assertIsArray( $menu );
        $this->assertCount( 1, $menu, 'only the tab that has its group' );
        $this->assertSame( 'content/view/full/2', $menu[0]['url'] );
    }
}
