<?php
/**
 * @description User audit log: removes the rows of expuser_audit older than audit.ini [UserAuditSettings] RetentionDays
 *
 * The code of cronjobs/useraudit.php.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Useraudit extends \Exponential\Runnable\CronjobPart
{
    /**
     * Retention of the user audit log (expUserAudit::cleanup()): rows older than [UserAuditSettings] RetentionDays
     * go (0 keeps them all). The audit files and their archives are not touched; their own retention is
     * [AuditChannel_access] LiveDays and ArchiveDays.
     */
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $removed = \expUserAudit::cleanup();
        if ( $removed === false )
        {
            $cli->output( 'Skipped: the table expuser_audit is not installed (update/common/scripts/6.0/createuseraudittable.php)' );
            return true;
        }
        $days = \expUserAudit::settings()['retention_days'];
        $cli->output( $days > 0 ? sprintf( 'User audit log: %d rows older than %d days removed', $removed, $days )
                                : 'User audit log: kept in full (RetentionDays=0)' );
        return true;
    }
}

}
