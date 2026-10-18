<?php
/**
 * exphelp/report (doc/features/6.0/help-system.md, "Reporting an error"): which tracker, how to write a good report,
 * a prefilled issue, the safe diagnostics to copy, and the private path for security issues.
 * Template design:exphelp/report.tpl: help_trackers, help_extension_trackers, help_tracker (the chosen key), help_chosen
 * (its tracker or false), help_draft (title, what, steps, expected), help_drafted, help_environment (label => value),
 * help_environment_text, help_issue_urls (key => URL), help_community (label, url), help_security (email, policy_url,
 * advisory_url), help_can_logs, help_logs_url, help_logs_digest_url, help_report_url.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Report extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Module = $scope['Params']['Module'];
        if ( !\expHelp::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $report = new \expHelpIssueReport();
        $trackers = $report->trackers();
        $extensionTrackers = $report->extensionTrackers();
        $all = $trackers + $extensionTrackers;
        // the report form is sent with POST (its text stays out of addresses, the history and the access log; the
        // form token is checked by ezformtoken), the tracker can also come from the address (?tracker=<key>)
        $posted = isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : array();
        $requested = isset( $posted['tracker'] ) ? $posted['tracker'] : ( isset( $_GET['tracker'] ) ? $_GET['tracker'] : '' );
        $chosen = is_string( $requested ) && isset( $all[$requested] ) ? $requested : '';
        $draft = $report->draft( $posted );
        $drafted = implode( '', $draft ) !== '';
        $urls = array();
        foreach ( $trackers as $key => $tracker )
            $urls[$key] = $report->draftIssueUrl( $key, $draft );
        if ( $chosen !== '' && !isset( $urls[$chosen] ) )
            $urls[$chosen] = $report->draftIssueUrl( $chosen, $draft );

        $user = \eZUser::currentUser();
        $logsAccess = $user instanceof \eZUser ? $user->hasAccessTo( 'setup', 'logs' ) : array();
        $canLogs = isset( $logsAccess['accessWord'] ) && $logsAccess['accessWord'] !== 'no';

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_trackers', $trackers );
        $tpl->setVariable( 'help_extension_trackers', $extensionTrackers );
        $tpl->setVariable( 'help_tracker', $chosen );
        $tpl->setVariable( 'help_chosen', $chosen !== '' ? $all[$chosen] : false );
        $tpl->setVariable( 'help_draft', $draft );
        $tpl->setVariable( 'help_drafted', $drafted );
        $tpl->setVariable( 'help_environment', $report->environment() );
        $tpl->setVariable( 'help_environment_text', $report->environmentText() );
        $tpl->setVariable( 'help_issue_urls', $urls );
        $tpl->setVariable( 'help_community', $report->communityLinks() );
        $tpl->setVariable( 'help_security', $report->securityContact() );
        $tpl->setVariable( 'help_can_logs', $canLogs );
        $tpl->setVariable( 'help_logs_url', '/setup/logs' );
        $tpl->setVariable( 'help_logs_digest_url', '/setup/logsdigest' );
        $tpl->setVariable( 'help_report_url', \expHelp::url( 'report' ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/report.tpl' );
        $Result['path'] = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Report an error' ) ) );
        return $Result;
    }
}

}
