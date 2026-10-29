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

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade the installation in one call\n" .
                             "Runs the stages in their fixed order: code, libraries, database, scripts, finish. Without --apply\n" .
                             "it changes nothing and lists, step by step, what it would do. With --apply it makes a backup,\n" .
                             "applies the pending steps in order, records each one and stops at the first failure; the finish\n" .
                             "stage then deploys the changes and checks that the site answers.\n" .
                             "\n" .
                             "./console exp:upgrade                                  dry run of every stage\n" .
                             "./console exp:upgrade --apply\n" .
                             "./console exp:upgrade --apply --stages=database,scripts,finish\n" .
                             "./console exp:upgrade --format=json                    the machine output exp-upgrade/1\n" .
                             "\n" .
                             "Exit codes: 0 done or nothing pending, 1 a step failed (or pending with --fail-on-pending),\n" .
                             "2 usage error, 3 database not reachable, 4 internal error.\n" .
                             "Guide: doc/eng-us/guides/upgrading-with-the-console.md",
        ) + self::SCRIPT );
        return (array)$this->startup( '[stages:][apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending][no-deploy][url:*]', '', array(
            'stages' => 'Only these stages, a comma list of code, libraries, database, scripts, finish (in that order)',
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'only' => 'Only these steps (step ids, a comma list or repeated); an optional step runs only when named here',
            'skip' => 'Never these steps (step ids, a comma list or repeated)',
            'format' => 'text (default) or json (the machine output exp-upgrade/1)',
            'no-backup' => 'Write without making a backup first',
            'backup-dir' => 'Where the backup goes (default var/backup/upgrade); a directory <time> is made in it',
            'force-order' => 'Run a stage although an earlier stage has pending steps',
            'fail-on-pending' => 'Exit 1 when steps are still pending (for CI)',
            'no-deploy' => 'Finish without exp:velocity deploy: extension autoloads and the INI, template and content caches only',
            'url' => 'The addresses the smoke check requests after the deploy (a comma list or repeated); default the front page of SiteURL',
        ) );
    }

    public function run()
    {
        $this->runStages();
    }
}

}
