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

    protected function description()
    {
        return "Upgrade stage 3: the database\n"
             . "The tables, columns, indexes and rows the release needs, on MySQL/MariaDB, PostgreSQL, SQLite, Oracle\n"
             . "(ezoracle) and MongoDB, written from the kernel schema (share/db_schema.dba). Each step looks at the\n"
             . "database first, so what the SQL update files already did counts. Dry run unless --apply.\n"
             . "\n"
             . "./console exp:upgrade:database\n"
             . "./console exp:upgrade:database --apply\n"
             . "./console exp:upgrade:database --apply --only=6.0.15-user-audit-table\n"
             . "Guide: doc/eng-us/guides/upgrading-with-the-console.md";
    }

    public function run()
    {
        $this->runStages( array( 'database' ) );
    }
}

}
