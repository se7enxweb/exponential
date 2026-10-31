<?php
/**
 * File containing the expUpgradeStepWebsiteSidebar class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * expwt, the website sidebar, in place of ezwt, the website toolbar (expWebsiteSidebarMigration, as
 * exp:wt:migrate-from-ezwt --apply): ezwt's settings copied to expwt, the websitetoolbar policies given as expwt
 * policies with the same limitations, the expwt policies for the Editor and Administrator roles, expwt activated where
 * ezwt was and ezwt no longer loaded. Not applicable while expwt is not in the extension directory.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepWebsiteSidebar extends expUpgradeScriptStep
{
    public function id()
    {
        return '6.0.15-expwt-enable';
    }

    public function description()
    {
        return 'expwt (the website sidebar) in place of ezwt: settings, policies, activation, schedule (exp:wt:migrate-from-ezwt)';
    }

    protected function changes( expUpgradeContext $context )
    {
        if ( !expWebsiteSidebarMigration::isInstalled() )
        {
            $this->notApplicable = true;
            $this->reason = 'expwt is not installed (extension/expwt)';
            return null;
        }
        return expWebsiteSidebarMigration::changes();
    }

    protected function write( expUpgradeContext $context )
    {
        $done = expWebsiteSidebarMigration::apply();
        foreach ( expWebsiteSidebarMigration::notes() as $note )
            $done[] = 'NOTE ' . $note;
        return $done;
    }
}
