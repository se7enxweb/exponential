<?php
/**
 * File containing the expUpgradeStepKernelTables class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Every table of the kernel schema (share/db_schema.dba) the database does not have, created empty with its
 * indexes. The steps before it name the tables of their own releases; a database upgraded across many releases can
 * still lack an older one it never got (ezapprove_items on a site from the eZ Publish 3 era, where the approve
 * workflow event had never run), and the scripts stage after it relies on them.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepKernelTables extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-kernel-tables';
    }

    public function description()
    {
        return 'Every table of the kernel schema the database lacks (an old database can miss one, e.g. ezapprove_items)';
    }

    public function tables()
    {
        return array_keys( expKernelTableSchema::read() );
    }

    /** @return string the count, not the ~150 names of the kernel schema */
    protected function presentDetail()
    {
        return 'in place: all ' . count( $this->tables() ) . ' tables of the kernel schema';
    }
}
