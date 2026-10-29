<?php
/**
 * @description Upgrade stage 2, libraries: Composer install from composer.lock (or update of named packages) with a security audit (dry run unless --apply)
 * @alias exp:upgrade:libraries
 *
 * File containing the exp:upgrade:libraries command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgradelibraries extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade:libraries';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade stage 2: the libraries\n" .
                             "Makes vendor/ hold what composer.lock names (composer install, with the dev packages only when\n" .
                             "vendor/ had them), or with --update composer update of the named packages first. The security\n" .
                             "audit (composer audit) runs before and after and reports; it never stops the run. Refused when\n" .
                             "Composer reports local changes in vendor/ (--allow-vendor-changes installs over them) and when\n" .
                             "the process is not the owner of the installation. Dry run unless --apply.\n" .
                             "\n" .
                             "./console exp:upgrade:libraries\n" .
                             "./console exp:upgrade:libraries --apply\n" .
                             "./console exp:upgrade:libraries --apply --update=guzzlehttp/guzzle\n" .
                             "Guide: doc/eng-us/guides/upgrading-with-the-console.md",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending][force-no-backup][update:*][composer:][no-audit][allow-vendor-changes]', '', array(
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
            'update' => 'composer update these packages, with their dependencies (a comma list or repeated)',
            'composer' => 'The Composer binary or composer.phar (default: composer on the PATH, else composer.phar in the root)',
            'no-audit' => 'Skip the security audit of the libraries (composer audit) before and after',
            'allow-vendor-changes' => 'Install the libraries although Composer reports local changes in vendor/',
        ) );
    }

    public function run()
    {
        $this->runStages( array( 'libraries' ) );
    }
}

}
