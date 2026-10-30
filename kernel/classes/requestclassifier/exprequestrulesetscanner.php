<?php
/**
 * File containing the expRequestRuleSetScanner class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Category "scanner": the user agent of a known attack or discovery tool (sqlmap, nikto, nuclei ...). The rule is
 * the tool's name. General clients are not tools: curl, wget, python-requests, a headless browser (Playwright,
 * HeadlessChrome) and search engines (Googlebot, bingbot) are never flagged by this set; the site's own checks use
 * curl and Playwright.
 *
 * @package kernel
 */
class expRequestRuleSetScanner extends expRequestPatternRuleSet
{
    public function category()
    {
        return 'scanner';
    }

    protected function builtInPatterns()
    {
        return array(
            'sqlmap' => '\bsqlmap\b',
            'nikto' => '\bnikto\b',
            'nuclei' => '\bnuclei\b',
            'masscan' => '\bmasscan\b',
            'zgrab' => '\bzgrab\b',
            'wpscan' => '\bwpscan\b',
            'dirbuster' => '\bdirbuster\b',
            'dirb' => '\bdirb/',
            'gobuster' => '\bgobuster\b',
            'feroxbuster' => '\bferoxbuster\b',
            'ffuf' => '\bffuf\b|fuzz faster u fool',
            'wfuzz' => '\bwfuzz\b',
            'nmap' => '\bnmap\b',
            'acunetix' => 'acunetix',
            'netsparker' => '\bnetsparker\b|\binvicti\b',
            'nessus' => '\bnessus\b',
            'openvas' => '\bopenvas\b',
            'w3af' => '\bw3af\b',
            'arachni' => '\barachni\b',
            'skipfish' => '\bskipfish\b',
            'jaeles' => '\bjaeles\b',
            'hydra' => '\bhydra\b',
            'zmeu' => '\bzmeu\b',
            'jorgee' => '\bjorgee\b',
        );
    }

    protected function builtInConfidence()
    {
        return array(
            'nmap' => expRequestVerdict::MEDIUM,
            'hydra' => expRequestVerdict::MEDIUM,
        );
    }

    protected function subjects( expRequestContext $context )
    {
        return array( $context->userAgent );
    }

    protected function excludeSubject( expRequestContext $context )
    {
        return '';
    }
}
