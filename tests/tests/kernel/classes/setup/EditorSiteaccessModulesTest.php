<?php
/**
 * The editor siteaccess the setup wizard creates keeps content editors away from site administration: its top menu
 * hides the administration tabs, and the modules behind them answer 404 there. Syndication (feed export and
 * import) is one of them. No database: only the lists the wizard writes are checked.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class EditorSiteaccessModulesTest extends PHPUnit\Framework\TestCase
{
    public function testSyndicationIsHiddenAndRefused()
    {
        $this->assertContains( 'syndication', eZStepCreateSites::EDITOR_HIDDEN_TABS );
        $this->assertContains( 'syndication', eZStepCreateSites::EDITOR_DISABLED_MODULES );
    }

    public function testAdministrationModulesAreRefused()
    {
        foreach ( array( 'setup', 'visual', 'git_manager', 'bccie', 'xrowextract', 'explayouts_ui', 'explayouts_ui_api' ) as $module )
        {
            $this->assertContains( $module, eZStepCreateSites::EDITOR_DISABLED_MODULES, $module );
        }
    }

    public function testEditingModulesStayAvailable()
    {
        foreach ( array( 'content', 'user', 'newsletter', 'ezoe', 'tags', 'shop', 'collaboration', 'notification' ) as $module )
        {
            $this->assertNotContains( $module, eZStepCreateSites::EDITOR_DISABLED_MODULES, $module );
        }
    }

    /**
     * A tab that is hidden but whose module still answers is a door left open: every hidden extension tab's module is
     * refused too. (The setup and design tabs belong to kernel modules that the rules or the policies keep away.)
     */
    public function testEveryHiddenExtensionTabHasItsModuleRefused()
    {
        $tabModule = array( 'gitmanager' => 'git_manager', 'xrowextract' => 'xrowextract', 'bccie_overview' => 'bccie',
                            'syndication' => 'syndication', 'explayouts_ui_dashboard' => 'explayouts_ui',
                            'setup' => 'setup' );
        foreach ( eZStepCreateSites::EDITOR_HIDDEN_TABS as $tab )
        {
            if ( $tab === 'design' )
            {
                continue;
            }
            $this->assertArrayHasKey( $tab, $tabModule, "hidden tab $tab has no known module" );
            $this->assertContains( $tabModule[$tab], eZStepCreateSites::EDITOR_DISABLED_MODULES, $tab );
        }
    }

    public function testListsHaveNoDuplicates()
    {
        $this->assertSame( array_values( array_unique( eZStepCreateSites::EDITOR_DISABLED_MODULES ) ), eZStepCreateSites::EDITOR_DISABLED_MODULES );
        $this->assertSame( array_values( array_unique( eZStepCreateSites::EDITOR_HIDDEN_TABS ) ), eZStepCreateSites::EDITOR_HIDDEN_TABS );
    }
}
