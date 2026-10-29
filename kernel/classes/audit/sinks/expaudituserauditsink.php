<?php
/**
 * The sink of the user audit log (expUserAudit): copies the audit records about a user account or a user group into
 * the table expuser_audit, at flush time, in the request that recorded them (doc/eng-us/features/6.0/user-audit.md).
 *
 *   [AuditSinkSettings] SinkClasses[useraudit]=expAuditUserAuditSink
 *   [AuditChannel_access] Sinks[]=useraudit
 *
 * Which records are kept is expUserAudit's choice ([UserAuditSettings] Events[], a user or group named), not
 * [AuditSink_useraudit] Events[]. A batch the database refused waits in the spool and is tried again by the audit
 * cronjob part; a record stored already is not stored twice.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditUserAuditSink extends expAuditSinkBase
{
    public function problem()
    {
        if ( !class_exists( 'eZDB' ) || !eZDB::hasInstance() )
            return 'no database connection';
        return '';
    }

    public function wants( array $record )
    {
        $settings = expUserAudit::settings();
        return $settings['enabled'] && isset( $record['name'] ) && expUserAudit::wantsName( (string)$record['name'], $settings['events'] )
               && expUserAudit::subjectOf( $record ) !== null;
    }

    /** Written at flush time: a local database insert, nothing to wait for. */
    public function isSpooled()
    {
        return false;
    }

    public function deliver( array $records )
    {
        try
        {
            $n = expUserAudit::storeRecords( $records );
            // no table yet (an installation before 6.0.15): nothing to keep, nothing to spool
            return $n === false ? count( $records ) : (int)$n;
        }
        catch ( Throwable $e )
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeError( 'User audit: ' . $e->getMessage(), __METHOD__ );
            return 0;
        }
    }
}
