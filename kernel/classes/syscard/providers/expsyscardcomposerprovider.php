<?php
/**
 * File containing the expSysCardComposerProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Composer: how many packages vendor/ has (vendor/composer/installed.json) and, when the ezupdate extension is active,
 * how many of them do not agree between composer.json, composer.lock and what is installed (its inventory, from the
 * same local files; nothing is asked over the network). The inventory is kept in the cache directory for
 * syscard.ini [Updates] CacheTime seconds, so the card stays fast.
 */
class expSysCardComposerProvider extends expSysCardBaseProvider
{
    protected $id = 'composer';
    protected $section = 'site';
    protected $priority = 25;
    protected $budget = 2.0;

    public function collect( expSysCardContext $context )
    {
        $installed = json_decode( (string)$context->file( 'vendor/composer/installed.json', 16777216 ), true );
        $packages = is_array( $installed ) ? ( isset( $installed['packages'] ) && is_array( $installed['packages'] ) ? $installed['packages'] : $installed ) : null;
        if ( $packages === null && !$context->isFile( 'composer.json' ) )
            return array( $this->fact( 'packages', 'Composer', 'no composer.json' ) );
        $count = is_array( $packages ) ? count( $packages ) : 0;
        $text = $packages === null ? 'not installed (run composer install)' : self::counted( $count, 'package' );
        $severity = $packages === null ? expSysCardFact::ERROR : expSysCardFact::INFO;
        $hint = $packages === null ? 'vendor/ is missing: run composer install in the installation root before anything else (README.md).' : '';

        $issues = $context->iniEnabled( 'syscard.ini', 'Updates', 'AskUpdateInventory', 'enabled' ) ? $this->issues( $context ) : null;
        if ( $issues !== null && $packages !== null )
        {
            $text .= ', ' . ( $issues === 0 ? 'all consistent' : $issues . ' with issues' );
            if ( $issues > 0 )
            {
                $severity = expSysCardFact::WARN;
                $hint = ( $issues === 1 ? '1 Composer package or extension does not agree' : $issues . ' Composer packages or extensions do not agree' ) . ' between composer.json, composer.lock and vendor/: see Setup > Updates (ezupdate) before the next update.';
            }
        }
        return array( $this->fact( 'packages', 'Composer', $text, $severity, $hint ) );
    }

    /**
     * The number of packages with issues in ezupdate's inventory (cached), or null without ezupdate.
     *
     * @param expSysCardContext $context
     * @return int|null
     */
    protected function issues( expSysCardContext $context )
    {
        $ttl = max( 0, (int)$context->ini( 'syscard.ini', 'Updates', 'CacheTime', 3600 ) );
        $answer = $context->get( 'composer.issues', function () use ( $context, $ttl ) {
            if ( !class_exists( 'eZUpdateInventory' ) || !class_exists( 'eZUpdateManager' ) || !class_exists( 'eZSys' ) )
                return null;
            $file = eZSys::cacheDirectory() . '/syscard/update-inventory.json';
            if ( $ttl > 0 && is_file( $file ) && filemtime( $file ) > $context->now - $ttl )
            {
                $cached = json_decode( (string)file_get_contents( $file ), true );
                if ( is_array( $cached ) && isset( $cached['issues'] ) )
                    return (int)$cached['issues'];
            }
            $inventory = new eZUpdateInventory( eZUpdateManager::getInstance() );
            $built = $inventory->build();
            $issues = isset( $built['summary']['issues'] ) ? (int)$built['summary']['issues'] : 0;
            // kept only by the owner of the cache directory: a file root writes there would block the web server's
            // user from clearing the caches
            $cacheDir = eZSys::cacheDirectory();
            $owner = function_exists( 'posix_geteuid' ) && is_dir( $cacheDir ) && @fileowner( $cacheDir ) === posix_geteuid();
            if ( $ttl > 0 && $owner && ( is_dir( dirname( $file ) ) || eZDir::mkdir( dirname( $file ), eZDir::directoryPermission(), true ) ) )
                @file_put_contents( $file, json_encode( array( 'issues' => $issues, 'time' => $context->now ) ) );
            return $issues;
        } );
        return is_int( $answer ) ? $answer : null;
    }
}
