<?php
/**
 * The code of kernel/user/audit.php. The file kernel/user/audit.php is one call to it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\User
{

/**
 * user/audit, "User audit log": the security events about user accounts and user groups (expUserAudit, the table
 * expuser_audit), newest first, narrowed by a GET form (user: an id or a login, matching the account or the user who
 * acted; action: an event name or its start, e.g. access.user; result; from and to as Y-m-d), paged ((offset),
 * limit). ExportButton (a POST, with the form token where ezformtoken is active) sends the CSV of the whole filtered
 * list and records data.export.csv with the number of rows, never the values.
 *
 * The view opens with the policy user/audit; whoever holds it sees the events of every account, so grant it only to
 * roles that may (administrators, a data protection officer). Without the table the page says how to create it.
 * Guide: doc/eng-us/features/6.0/user-audit.md
 */
class Audit extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $tpl = \eZTemplate::factory();
        $db = \eZDB::instance();

        $filters = \expUserAudit::normaliseFilters( $_GET );
        $query = \expUserAudit::queryString( $filters );
        $supported = $db->databaseName() !== 'mongo';
        $installed = $supported && \expUserAudit::exists( $db ) === true;

        if ( $installed && \eZSys::serverVariable( 'REQUEST_METHOD', true ) === 'POST' && $http->hasPostVariable( 'ExportButton' ) )
            return $this->export( $filters );

        $offset = isset( $Params['Offset'] ) ? max( 0, (int)$Params['Offset'] ) : 0;
        $count = 0;
        $rows = array();
        $names = array();
        if ( $installed )
        {
            $count = \expUserAudit::count( $filters );
            if ( $offset >= $count )
                $offset = $count > 0 ? (int)( floor( ( $count - 1 ) / $filters['limit'] ) * $filters['limit'] ) : 0;
            $rows = $count > 0 ? \expUserAudit::fetch( $filters, $offset, $filters['limit'] ) : array();
            foreach ( (array)$db->arrayQuery( 'SELECT DISTINCT event_name FROM ' . \expUserAudit::TABLE . ' ORDER BY event_name' ) as $row )
                $names[] = (string)$row['event_name'];
        }
        $settings = \expUserAudit::settings();

        $tpl->setVariable( 'audit_filters', $filters );
        $tpl->setVariable( 'audit_query', $query );
        $tpl->setVariable( 'audit_supported', $supported );
        $tpl->setVariable( 'audit_installed', $installed );
        $tpl->setVariable( 'audit_enabled', $settings['enabled'] );
        $tpl->setVariable( 'audit_retention_days', $settings['retention_days'] );
        $tpl->setVariable( 'audit_rows', $rows );
        $tpl->setVariable( 'audit_count', $count );
        $tpl->setVariable( 'audit_event_names', $names );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );
        $tpl->setVariable( 'module', $Module );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:user/audit.tpl' );
        $Result['path'] = array(
            array( 'text' => \ezpI18n::tr( 'kernel/user', 'User' ), 'url' => false ),
            array( 'text' => \ezpI18n::tr( \expUserAudit::CONTEXT, 'User audit log' ), 'url' => 'user/audit' ),
        );
        return $this->viewResult( $Result, $Result );
    }

    /**
     * Sends the CSV of the filtered list and ends the request.
     */
    protected function export( array $filters )
    {
        $fileName = 'user-audit-' . gmdate( 'Ymd-His' ) . '.csv';
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'Pragma: no-cache' );
        header( 'X-Content-Type-Options: nosniff' );
        while ( @ob_end_clean() );

        // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
        $out = fopen( 'php://output', 'w' );
        $rows = \expUserAudit::writeCSV( $out, $filters );
        fclose( $out );

        if ( class_exists( 'expAuditHook' ) )
            \expAuditHook::emit( 'data.export.csv', array( 'object' => array( 'type' => 'view', 'id' => 'user/audit' ), 'verb' => 'export',
                'after' => array( 'rows' => $rows, 'user' => $filters['user'] !== '', 'action' => $filters['action'],
                                  'result' => $filters['result'], 'from' => $filters['from'], 'to' => $filters['to'],
                                  'file' => $fileName, 'source' => 'user_audit' ) ) );

        \eZExecution::cleanExit();
        return $this->viewResult( null, null );
    }
}

}
