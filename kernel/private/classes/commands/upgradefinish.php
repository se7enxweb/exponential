<?php
/**
 * @description Upgrade stage 5, finish: deploy the changes (exp:velocity deploy --kernel) and check the site answers (dry run unless --apply)
 * @alias exp:upgrade:finish
 *
 * File containing the exp:upgrade:finish command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade stage 5: finish\n" .
                             "Makes the changes live with ./console exp:velocity deploy --kernel (autoloads, INI and template\n" .
                             "caches, PHP-FPM, Velocity, the output caches, the help content cache), then requests the front\n" .
                             "page (or each --url) and fails when it does not answer 200 to 399. --no-deploy: autoloads and\n" .
                             "caches only. Run on its own it always has work; inside exp:upgrade only when the run changed\n" .
                             "something. Dry run unless --apply.\n" .
                             "\n" .
                             "./console exp:upgrade:finish --apply\n" .
                             "./console exp:upgrade:finish --apply --no-deploy\n" .
                             "./console exp:upgrade:finish --apply --url=https://www.example.com/,https://admin.example.com/",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending][force-no-backup][no-caption][force-no-caption][no-deploy][url:*]', '', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'only' => 'Only these steps (step ids, a comma list or repeated); an optional step runs only when named here',
            'skip' => 'Never these steps (step ids, a comma list or repeated)',
            'format' => 'text (default) or json (the machine output exp-upgrade/1)',
            'no-backup' => 'Write without making a backup first',
            'backup-dir' => 'Where the backup goes (default var/backup/upgrade); a directory <time> is made in it',
            'force-order' => 'Run a stage although an earlier stage has pending steps',
            'fail-on-pending' => 'Exit 1 when steps are still pending (for CI)',
            'force-no-backup' => 'With --no-backup on a production installation: write without a backup all the same',
            'no-caption' => 'Write without taking a caption first (the same as --no-backup); refused on production without --force-no-caption',
            'force-no-caption' => 'With --no-caption on a production installation: write without a caption all the same',
            'no-deploy' => 'Finish without exp:velocity deploy: extension autoloads and the INI, template and content caches only',
            'url' => 'The addresses the smoke check requests after the deploy (a comma list or repeated); default the front page of SiteURL',
        ) );
    }

    public function run()
    {
        $this->runStages( array( 'finish' ) );
    }
}

}
