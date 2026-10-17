<?php
/**
 * File containing the expRequestRuleSetProbes class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Category "probe": a request for something an Exponential site does not have and a scanner looks for: WordPress,
 * hidden files (.env, .git), database tools, test scripts, backups and archives. Matched against the decoded
 * namespace path (the path below the siteaccess), so /site/wp-login.php and /admin/wp-login.php are both
 * /wp-login.php.
 *
 * Never flagged (Excludes): downloads (/content/download/...), stored files (var/storage, var/<site>/storage), the
 * public cache (packed scripts and styles) and the assets of designs and extensions. An archive is a probe only at the
 * namespace root (/backup.zip) or with a backup-like name (/old/site-backup.tar.gz); a copy (*.bak, *.old, *~)
 * anywhere outside the excludes.
 *
 * The names follow request-shield's (WP-*, SCAN-*) where they overlap, in lower case.
 *
 * @package kernel
 */
class expRequestRuleSetProbes extends expRequestPatternRuleSet
{
    const ARCHIVE = '(?:zip|rar|7z|tar|tgz|tbz2?|tar\.gz|tar\.bz2|tar\.xz|sql|sql\.gz|sql\.zip|sqlite3?|dump)';

    public function category()
    {
        return 'probe';
    }

    protected function builtInPatterns()
    {
        return array(
            'wp-login' => '/wp-login\.php(?:/|$)',
            'wp-admin' => '/wp-admin(?:/|$)',
            'wp-content' => '/wp-content(?:/|$)',
            'wp-includes' => '/wp-includes(?:/|$)',
            'wp-config' => '/wp-config\.php',
            'xmlrpc' => '/xmlrpc\.php(?:/|$)',
            'env' => '/\.env(?:\.[a-z0-9_.-]+)?(?:/|$)',
            'git' => '/\.git(?:/|$|config$|-credentials$)',
            'vcs' => '/\.(?:svn|hg|bzr|cvs)(?:/|$)',
            'ds-store' => '/\.ds_store$',
            'hidden' => '/\.(?:htpasswd|htaccess|aws|ssh|docker|npmrc|idea|vscode|bash_history)(?:/|$)',
            'dbtool' => '/(?:phpmyadmin[^/]*|pma|myadmin|mysqladmin|dbadmin|sqlitemanager|adminer(?:[-_.][^/]*)?)(?:/|$)',
            'phpunit' => '/vendor/phpunit(?:/|$)|/eval-stdin\.php$',
            'cgi-bin' => '/cgi-bin(?:/|$)',
            'server-status' => '/server-(?:status|info)(?:/|$)',
            'actuator' => '/actuator(?:/|$)',
            'phpinfo' => '/(?:phpinfo|php_info|info)\.php$',
            'config-file' => '/(?:composer\.(?:json|lock)|package(?:-lock)?\.json|web\.config|docker-compose\.ya?ml|id_(?:rsa|dsa|ecdsa|ed25519)(?:\.pub)?|[^/]+\.ini(?:\.append(?:\.php)?)?)$',
            'exploit' => '/(?:hnap1|boaform|gponform)(?:/|$)|/_ignition/execute-solution|/\.well-known/(?:pki-validation/)?[^/]*\.php$',
            'backup' => '^/[^/]+\.' . self::ARCHIVE . '$|/(?:[^/]*[-_.])?(?:backups?|dump|database|db|www|wwwroot|htdocs|public_html|site|old)\d*(?:[-_.][^/]*)?\.' . self::ARCHIVE . '$',
            'backup-copy' => '\.(?:bak|old|orig|save|swp|swo|backup)$|~$',
        );
    }

    protected function builtInConfidence()
    {
        return array(
            'cgi-bin' => expRequestVerdict::MEDIUM,
            'actuator' => expRequestVerdict::MEDIUM,
            'phpinfo' => expRequestVerdict::MEDIUM,
            'config-file' => expRequestVerdict::MEDIUM,
            'backup' => expRequestVerdict::MEDIUM,
            'backup-copy' => expRequestVerdict::MEDIUM,
        );
    }

    protected function builtInExcludes()
    {
        return array(
            'download' => '^/content/download/',
            'storage' => '^/var/(?:[^/]+/)?storage/',
            'public-cache' => '^/var/(?:[^/]+/)?cache/public/',
            'assets' => '^/(?:design|extension|share)/[^?]*\.(?:css|js|map|png|gif|jpe?g|svg|webp|avif|ico|woff2?|ttf|eot|otf)$',
        );
    }

    protected function subjects( expRequestContext $context )
    {
        return array( $context->decodedNamespacePath() );
    }
}
