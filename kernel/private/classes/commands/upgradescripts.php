<?php
/**
 * @description Upgrade stage 4, scripts: the PHP upgrade steps, policies and repairs (dry run unless --apply)
 * @alias exp:upgrade:scripts
 *
 * File containing the exp:upgrade:scripts command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade stage 4: the scripts\n" .
                             "The PHP upgrade steps: the scripts of update/common/scripts/ and the setup commands (editor\n" .
                             "policies, member management, user approval, ...), run through the same classes with their\n" .
                             "default options. Refused while the database stage has pending steps (--force-order runs\n" .
                             "anyway). Dry run unless --apply.\n" .
                             "\n" .
                             "./console exp:upgrade:scripts\n" .
                             "./console exp:upgrade:scripts --apply --skip=6.0.15-editor-policies\n" .
                             "Guide: doc/eng-us/guides/upgrading-with-the-console.md",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending][force-no-backup][no-caption][force-no-caption]', '', array(
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
        ) );
    }

    public function run()
    {
        $this->runStages( array( 'scripts' ) );
    }
}

}
