<?php
/**
 * @description Upgrade stage 3, database: the schema and data steps on every engine (dry run unless --apply)
 * @alias exp:upgrade:database
 *
 * File containing the exp:upgrade:database command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Upgradedatabase extends UpgradeBase
{
    protected function name()
    {
        return 'exp:upgrade:database';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Upgrade stage 3: the database\n" .
                             "The tables, columns, indexes and rows the release needs, on MySQL/MariaDB, PostgreSQL, SQLite,\n" .
                             "Oracle (ezoracle) and MongoDB, written from the kernel schema (share/db_schema.dba). Each step\n" .
                             "looks at the database first, so what the SQL update files already did counts. Dry run unless\n" .
                             "--apply.\n" .
                             "\n" .
                             "./console exp:upgrade:database\n" .
                             "./console exp:upgrade:database --apply\n" .
                             "./console exp:upgrade:database --apply --only=6.0.15-user-audit-table\n" .
                             "Guide: doc/eng-us/guides/upgrading-with-the-console.md",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][only:*][skip:*][format:][no-backup][backup-dir:][force-order][fail-on-pending][force-no-backup]', '', array(
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
        ) );
    }

    public function run()
    {
        $this->runStages( array( 'database' ) );
    }
}

}
