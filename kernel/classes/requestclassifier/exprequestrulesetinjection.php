<?php
/**
 * File containing the expRequestRuleSetInjection class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Category "injection": signatures of SQL, command, template (SSTI) and script (XSS) injection in the query or the
 * path, decoded once (%3Cscript in the decoded text is a double-encoded one). Only the URL is looked at, never a
 * posted body.
 *
 * Written against false positives: a search for "union station", O'Reilly, "select or drop" or <b> is no
 * injection; the signatures need the shape of an attack (union ... select, ' or '1'='1, sleep(5, ;cat /, {{7*7}}).
 * The names follow request-shield's (ATK-*) where they overlap, in lower case.
 *
 * @package kernel
 */
class expRequestRuleSetInjection extends expRequestPatternRuleSet
{
    public function category()
    {
        return 'injection';
    }

    protected function builtInPatterns()
    {
        $gap = '(?:\s|/\*[^*]*\*/)+';
        return array(
            'sql-union' => '\bunion' . $gap . '(?:all' . $gap . '|distinct' . $gap . ')?select\b',
            'sql-tautology' => '[\'"]\s*(?:or|and|\|\||&&)\s+[\'"]?\d+[\'"]?\s*(?:=|<|>|like)\s*[\'"]?\d+|[\'"]\s*(?:or|and)\s+[\'"][a-z0-9_]*[\'"]\s*=\s*[\'"]',
            'sql-boolean' => '\b(?:or|and)\s+(\d+)\s*=\s*\d+\s*(?:--|#|/\*|$)',
            'sql-time' => '\b(?:sleep|benchmark|pg_sleep)\s*\(\s*\d|\bwaitfor\s+delay\s+\'',
            'sql-schema' => '\binformation_schema\b|\bmysql\.user\b|\bsqlite_master\b|\bpg_(?:shadow|catalog)\b',
            'sql-stacked' => ';\s*(?:drop|truncate|alter)\s+table\b|;\s*shutdown\s*(?:--|#|;|$)',
            'sql-function' => '\b(?:load_file|extractvalue|updatexml)\s*\(|\binto\s+(?:out|dump)file\b',
            'cmd-file' => '/etc/(?:passwd|shadow|group|hosts)\b|/proc/self/|\b(?:boot|win)\.ini\b',
            'cmd-chain' => '(?:;|\||&&|\$\(|`)\s*(?:id|whoami|uname|cat|head|tail|ls|wget|curl|nc|ncat|bash|sh|ping|nslookup|python3?|perl|sleep)\b(?![-=])',
            'jndi' => '\$\{[^}]{0,40}(?:jndi|j\$\{|:-j)|\$\{(?:env|sys|java|base64|lower|upper|ctx|main|spring):',
            'ssti' => '\{\{[^}]{0,80}(?:\*|__|\(|_self\b)[^}]{0,80}\}\}|\$\{[^}]{0,80}(?:\*|\()[^}]{0,80}\}|#\{[^}]{0,80}(?:\*|\()[^}]{0,80}\}|<%=?[^%>]{0,80}%>',
            'xss-script' => '<\s*/?\s*script\b|%3c\s*/?\s*script\b',
            'xss-event' => '<[^<>]*\bon(?:error|load|mouseover|focus|blur|click|toggle|begin|animationstart|pointerenter)\s*=|<svg[\s/]+on[a-z]+|[\'"]\s*on(?:error|load|mouseover|focus)\s*=',
            'xss-url' => '(?:^|[=&\s"\'(])\s*(?:javascript|vbscript|livescript)\s*:|(?:^|[=&])\s*data\s*:\s*text/html',
            'php-code' => '<\?(?:php|=)|\b(?:base64_decode|shell_exec|passthru|proc_open|pcntl_exec)\s*\(|\b(?:php|phar|expect|glob)://',
        );
    }

    protected function builtInConfidence()
    {
        return array(
            'sql-boolean' => expRequestVerdict::MEDIUM,
            'cmd-chain' => expRequestVerdict::MEDIUM,
            'ssti' => expRequestVerdict::MEDIUM,
            'xss-url' => expRequestVerdict::MEDIUM,
        );
    }

    protected function subjects( expRequestContext $context )
    {
        return array( $context->decodedQuery(), $context->decodedPath() );
    }

    protected function excludeSubject( expRequestContext $context )
    {
        return '';
    }
}
