<?php
/**
 * @description Upgrade stage 4, scripts: the PHP upgrade steps, policies and repairs (dry run unless --apply)
 * @alias exp:upgrade:scripts
 *
 * File containing the exp:upgrade:scripts command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgradescripts extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade:scripts';
    }

    protected function description()
    {
        return "Upgrade stage 4: the scripts\n"
             . "The PHP upgrade steps: the scripts of update/common/scripts/ and the setup commands (editor policies,\n"
             . "member management, user approval, ...), run through the same classes with their default options.\n"
             . "Refused while the database stage has pending steps (--force-order runs anyway). Dry run unless --apply.\n"
             . "\n"
             . "./console exp:upgrade:scripts\n"
             . "./console exp:upgrade:scripts --apply --skip=6.0.15-editor-policies\n"
             . "Guide: doc/eng-us/guides/upgrading-with-the-console.md";
    }

    public function run()
    {
        $this->runStages( array( 'scripts' ) );
    }
}

}
