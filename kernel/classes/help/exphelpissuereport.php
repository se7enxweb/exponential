<?php
/**
 * Reporting an error (workstream H): the trackers ([IssueTrackers]), prefilled GitHub issue URLs and a safe
 * environment summary (versions, PHP, engine, design, siteaccess; never secrets, database hosts, credentials,
 * personal data or paths beyond the install root). Skeleton: H replaces the bodies.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpIssueReport
{
    /** @return array key => array( key, label, description, url, new_issue_url ) */
    public function trackers() { return array(); }

    /** @return array label => value, safe to publish */
    public function environment() { return array(); }

    /** @return string the environment as Markdown, for an issue body */
    public function environmentText() { return ''; }

    /**
     * @param string $tracker
     * @param string $title
     * @param string $body
     * @return string the new-issue URL with title and body, '' for an unknown tracker
     */
    public function issueUrl( $tracker, $title = '', $body = '' ) { return ''; }

    /** @return array array( 'email' => string, 'policy_url' => string ) the private disclosure path */
    public function securityContact() { return array( 'email' => '', 'policy_url' => '' ); }
}

?>
