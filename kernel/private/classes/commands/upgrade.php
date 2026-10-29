<?php
/**
 * @description Upgrade the installation in one call: every stage in order (dry run unless --apply)
 *
 * File containing the exp:upgrade command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgrade extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade';
    }

    protected function description()
    {
        return "Upgrade the installation in one call\n"
             . "Runs the stages in their fixed order: code, libraries, database, scripts, finish. Without --apply it\n"
             . "changes nothing and lists, step by step, what it would do. With --apply it makes a backup, applies the\n"
             . "pending steps in order, records each one and stops at the first failure.\n"
             . "\n"
             . "./console exp:upgrade                         (dry run of every stage)\n"
             . "./console exp:upgrade --apply\n"
             . "./console exp:upgrade --apply --stages=database,scripts\n"
             . "./console exp:upgrade --format=json           (exp-upgrade/1)\n"
             . "\n"
             . "Exit codes: 0 done or nothing pending, 1 a step failed (or pending with --fail-on-pending),\n"
             . "2 usage error, 3 database not reachable, 4 internal error.\n"
             . "Guide: doc/eng-us/guides/upgrading-with-the-console.md";
    }

    public function run()
    {
        $this->runStages();
    }
}

}
