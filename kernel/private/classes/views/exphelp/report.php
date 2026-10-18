<?php
/**
 * exphelp/report (doc/features/6.0/help-system.md, "Reporting an error"): which tracker, how to write a good report,
 * a prefilled issue, the safe diagnostics to copy, and the private path for security issues.
 * Template design:exphelp/report.tpl: help_trackers, help_tracker (the chosen key), help_environment (label => value),
 * help_environment_text, help_issue_urls (key => URL), help_security (email, policy_url), help_logs_url.
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
        $chosen = isset( $_GET['tracker'] ) && is_string( $_GET['tracker'] ) && isset( $trackers[$_GET['tracker']] ) ? $_GET['tracker'] : '';
        $urls = array();
        foreach ( $trackers as $key => $tracker )
            $urls[$key] = $report->issueUrl( $key );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_trackers', $trackers );
        $tpl->setVariable( 'help_tracker', $chosen );
        $tpl->setVariable( 'help_environment', $report->environment() );
        $tpl->setVariable( 'help_environment_text', $report->environmentText() );
        $tpl->setVariable( 'help_issue_urls', $urls );
        $tpl->setVariable( 'help_security', $report->securityContact() );
        $tpl->setVariable( 'help_logs_url', '/setup/logs' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/report.tpl' );
        $Result['path'] = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Report an error' ) ) );
        return $Result;
    }
}

}
