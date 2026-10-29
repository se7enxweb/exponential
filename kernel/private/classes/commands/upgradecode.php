<?php
/**
 * @description Upgrade stage 1, code: the git checkouts moved to the target release, fast-forward only, through git_manager (dry run unless --apply)
 * @alias exp:upgrade:code
 *
 * File containing the exp:upgrade:code command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgradecode extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade:code';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade stage 1: the code\n" .
                             "Moves the installation's git checkout (and each --repos checkout) to --to (a tag, a branch or a\n" .
                             "commit) or to the upstream of its branch, fast-forward only, through git_manager; the submodules\n" .
                             "follow. Never over local changes, never when the checkout has commits the target lacks, and only\n" .
                             "as the owner of the checkout. git_manager's backup is made first (unless --no-backup), and the\n" .
                             "commits before the move are kept for exp:upgrade:rollback. The dry run fetches (remote-tracking\n" .
                             "refs only). Dry run unless --apply.\n" .
                             "\n" .
                             "./console exp:upgrade:code --to=v6.0.16\n" .
                             "./console exp:upgrade:code --apply --to=v6.0.16 --repos=extension/myext\n" .
                             "Guide: doc/eng-us/guides/upgrading-with-the-console.md",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending][force-no-backup][no-caption][force-no-caption][to:][repos:*]', '', array(
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
            'to' => 'The code stage moves the checkouts to this tag, branch or commit (default: the upstream of the branch)',
            'repos' => 'More git checkouts the code stage moves, paths inside the installation (a comma list or repeated)',
        ) );
    }

    public function run()
    {
        $this->runStages( array( 'code' ) );
    }
}

}
