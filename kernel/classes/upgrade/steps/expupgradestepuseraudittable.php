<?php
/**
 * File containing the expUpgradeStepUserAuditTable class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The user audit log (expuser_audit), through expUserAudit::install() as
 * update/common/scripts/6.0/createuseraudittable.php. Not on MongoDB: the audit files keep every event there.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepUserAuditTable extends expUpgradeTableInstallerStep
{
    public function id()
    {
        return '6.0.15-user-audit-table';
    }

    public function description()
    {
        return 'The user audit log (table expuser_audit)';
    }

    public function engines()
    {
        return array( 'mysql', 'postgresql', 'sqlite', 'oracle' );
    }

    public function tables()
    {
        return array( 'expuser_audit' );
    }

    protected function install( $db, array &$messages )
    {
        return expUserAudit::install( $db, $messages );
    }
}
