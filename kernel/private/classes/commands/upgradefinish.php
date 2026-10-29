<?php
/**
 * @description Upgrade stage 5, finish: deploy the changes (exp:velocity deploy --kernel) and check the site answers (dry run unless --apply)
 * @alias exp:upgrade:finish
 *
 * File containing the exp:upgrade:finish command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgradefinish extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade:finish';
    }

    protected function description()
    {
        return "Upgrade stage 5: finish\n"
             . "Makes the changes live with ./console exp:velocity deploy --kernel (autoloads, INI and template caches,\n"
             . "PHP-FPM, Velocity, the output caches, the help content cache), then requests the front page (or each\n"
             . "--url) and fails when it does not answer 200 to 399. --no-deploy: autoloads and caches only.\n"
             . "Run on its own it always has work; inside exp:upgrade only when the run changed something.\n"
             . "Dry run unless --apply.\n"
             . "\n"
             . "./console exp:upgrade:finish --apply\n"
             . "./console exp:upgrade:finish --apply --no-deploy\n"
             . "./console exp:upgrade:finish --apply --url=https://www.example.com/,https://admin.example.com/";
    }

    public function run()
    {
        $this->runStages( array( 'finish' ) );
    }
}

}
