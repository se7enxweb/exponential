<?php
/**
 * The csp module: where browsers send the reports of the Content Security Policy
 * (doc/en/us/features/6.0/content-security-policy.md, "Reports").
 *
 * csp/report takes a report (POST, application/csp-report or application/reports+json) and writes one line per
 * violation to var/log/csp.log; it answers with a status and no body. Browsers send reports without asking anyone
 * to sign in, so the view is open to every visitor (site.ini [SiteAccessSettings] AnonymousAccessList[] and
 * [RoleSettings] PolicyOmitList[] name csp/report) and protects itself: content types, size, rate limits per client
 * and in total, masked and bounded log lines.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$Module = array( 'name' => 'Content Security Policy',
                 'variable_params' => false );

$ViewList = array();

$ViewList['report'] = array(
    'script' => 'report.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array() );

$FunctionList = array();
