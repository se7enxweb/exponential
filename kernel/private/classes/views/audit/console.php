<?php
/**
 * The audit/console view (doc/bc/6.0/audit.md, "Views"): the timeline of the audit records, newest first, with
 * filters, search and paging, and the state of each channel's hash chain at the top. Reads the index (brought up
 * to date when the view opens; the records of the live files not indexed yet are merged into the first page), or
 * the files when the index is off or not installed. Policy audit/read; the Channel limitation narrows the
 * channels. Opening it is recorded (system.audit.read).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Audit
{

class Console extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !class_exists( 'expAuditConsole' ) || !\expAuditConsole::available() )
            return $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

        $raw = \expAuditConsole::rawFilters( $Params );
        // "Verify now" (audit/manage): the full chain of every readable channel, then back to the same page.
        // A plain view never walks the files: it shows the stored result of the last verification.
        $http = \eZHTTPTool::instance();
        if ( $http->hasPostVariable( 'AuditVerifyNowButton' ) )
        {
            if ( !\expAuditConsole::canManage() )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            // ReauthForManage: the password again first (the form posts back here with this button)
            $form = \expAuditReauth::gate( $Module, 'AuditVerifyNowButton', \expAuditConsole::url( 'audit/console', $raw ),
                                           \ezpI18n::tr( 'design/admin/audit', 'Verify now' ) );
            if ( $form !== null )
                return $form;
            if ( class_exists( 'expAuditGuard' ) && !\expAuditGuard::allows( 'system.audit.verify' ) )
                return \expAuditGuard::refusedResult( 'audit', 'console' );
            \expAuditConsole::chainStates( \expAuditConsole::allowedChannels(), true );
            return $Module->redirectTo( '/' . \expAuditConsole::url( 'audit/console', $raw ) );
        }
        if ( \expAuditConsole::isFormRequest() )
            return $Module->redirectTo( '/' . \expAuditConsole::url( 'audit/console', $raw ) );

        $allowed = \expAuditConsole::allowedChannels();
        if ( isset( $raw['channel'] ) && !\expAuditConsole::channelAllowed( $raw['channel'], $allowed ) )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        $settings = \expAuditIndexSettings::get();
        $config = \expAuditConfig::get();
        $filters = \expAuditQuery::normalise( $raw, $config );
        $limit = isset( $Params['Limit'] ) && ctype_digit( (string)$Params['Limit'] ) ? min( \expAuditQuery::MAX_LIMIT, max( 1, (int)$Params['Limit'] ) ) : $settings['pageSize'];
        $offset = isset( $Params['Offset'] ) && ctype_digit( (string)$Params['Offset'] ) ? (int)$Params['Offset'] : 0;

        $indexRun = \expAuditConsole::refreshIndex();
        $page = \expAuditConsole::page( $filters, $allowed, $offset, $limit );
        $events = array();
        foreach ( $page['rows'] as $row )
            $events[] = \expAuditConsole::view( $row );

        \expAuditConsole::recordRead( 'audit/console', $raw, $page['total'], array( 'offset' => $offset ?: null ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'events', $events );
        $tpl->setVariable( 'total', (int)$page['total'] );
        $tpl->setVariable( 'offset', $offset );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'source', $page['source'] );
        $tpl->setVariable( 'fulltext', $page['fulltext'] );
        $tpl->setVariable( 'unindexed', $page['unindexed'] );
        $tpl->setVariable( 'index_run', $indexRun ?: array() );
        $tpl->setVariable( 'filters', $raw );
        // a malformed filter is not dropped (that would show everything): the page says what is wrong
        $tpl->setVariable( 'invalid_filters', isset( $filters['invalid'] ) ? $filters['invalid'] : array() );
        $active = array();
        foreach ( $raw as $k => $v )
        {
            $rest = $raw;
            unset( $rest[$k] );
            $active[] = array( 'key' => $k, 'value' => $v, 'remove_url' => \expAuditConsole::url( 'audit/console', $rest ) );
        }
        $tpl->setVariable( 'active_filters', $active );
        $tpl->setVariable( 'filter_url', \expAuditConsole::url( 'audit/console', $raw ) );
        $tpl->setVariable( 'export_url', \expAuditConsole::url( 'audit/export', $raw ) );
        $tpl->setVariable( 'charts_url', \expAuditConsole::url( 'audit/charts', $raw ) );
        $tpl->setVariable( 'newer_url', $offset > 0 ? \expAuditConsole::url( 'audit/console', $raw, array( 'offset' => max( 0, $offset - $limit ), 'limit' => $limit !== $settings['pageSize'] ? $limit : null ) ) : '' );
        $tpl->setVariable( 'older_url', $offset + $limit < $page['total'] ? \expAuditConsole::url( 'audit/console', $raw, array( 'offset' => $offset + $limit, 'limit' => $limit !== $settings['pageSize'] ? $limit : null ) ) : '' );
        $tpl->setVariable( 'page', (int)floor( $offset / $limit ) + 1 );
        $tpl->setVariable( 'pages', max( 1, (int)ceil( $page['total'] / $limit ) ) );
        $tpl->setVariable( 'channel_names', \expAuditConsole::channelNames( $allowed ) );
        $tpl->setVariable( 'chains', \expAuditConsole::chainStates( $allowed ) );
        $tpl->setVariable( 'severities', \expAuditIndexRow::SEVERITIES );
        $tpl->setVariable( 'names', \expAuditConsole::knownNames( $allowed ) );
        $tpl->setVariable( 'audit_enabled', $config['enabled'] );
        $tpl->setVariable( 'can_manage', \expAuditConsole::canManage() );
        $tpl->setVariable( 'limited_to', $allowed === null ? array() : $allowed );

        return \expAuditConsole::result( $tpl->fetch( 'design:audit/console.tpl' ), \ezpI18n::tr( 'design/admin/audit', 'Console' ) );
    }
}

}
