<?php
/**
 * File containing the csp/report view.
 *
 * Takes a report of the Content Security Policy from a browser and writes its violations to var/log/csp.log
 * (expContentSecurityPolicyReport). The view answers a browser, never a person: every answer is a bare status
 * without a body, page layout or debug report, never kept by a cache:
 *
 *   204  the report was taken (its violations written; one without a violation is taken too)
 *   400  the body is not a report
 *   405  not a POST (Allow: POST)
 *   413  the body is bigger than [ContentSecurityPolicy] MaxReportBytes
 *   415  not application/csp-report, application/reports+json or application/json
 *   429  over the rate limits (Retry-After: 60)
 *
 * The view is open to every visitor (AnonymousAccessList[], PolicyOmitList[]), reads nothing of the user and
 * releases the session at once. A signed-in browser may send its session cookie with a report; the form token check
 * (ezformtoken) leaves a POST to csp/report with a report content type alone, since no form of another site can send
 * one of these content types.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Csp
{

class Report extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // an answer for a browser: no debug report appended to it
        \eZDebug::updateSettings( array( 'debug-enabled' => false ) );
        \Exponential\View\Kernel\User\Megamenu::releaseSession();

        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? (string)$_SERVER['REQUEST_METHOD'] : 'GET';
        $contentType = \expContentSecurityPolicyReport::requestContentType();
        $report = new \expContentSecurityPolicyReport();
        $settings = $report->settings();

        // the body is read only for a POST of a report content type, and never more than the limit allows
        $body = '';
        if ( strtoupper( $method ) === 'POST' && \expContentSecurityPolicyReport::isReportContentType( $contentType ) )
            $body = \expContentSecurityPolicyReport::readBody( $settings['MaxReportBytes'] );

        $outcome = $report->handle( $method, $contentType, $body, \expRequestContext::fromGlobals() );
        return $this->send( $outcome['status'] );
    }

    /**
     * Sends the status and ends the request (eZExecution::cleanExit(), which a persistent worker turns into the end
     * of the request: never call this inside a catch of \Exception or \Throwable).
     *
     * @param int $status
     * @return mixed never returns in a web request
     */
    protected function send( $status )
    {
        \eZExecution::discardOutputBuffers();
        if ( !headers_sent() )
        {
            http_response_code( (int)$status );
            header( 'Cache-Control: no-store' );
            header( 'Content-Type: text/plain; charset=utf-8' );
            header( 'X-Content-Type-Options: nosniff' );
            if ( $status === 405 )
                header( 'Allow: POST' );
            if ( $status === 429 )
                header( 'Retry-After: 60' );
        }
        \eZExecution::cleanExit();
        return null;
    }
}

}
