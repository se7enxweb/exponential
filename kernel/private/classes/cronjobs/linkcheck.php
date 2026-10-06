<?php
/**
 * The code of cronjobs/linkcheck.php, moved into a class (#207 stage 1). The file cronjobs/linkcheck.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 * User guide of the link check: doc/guides/urls-and-aliases.md (section "How links are checked")
 */
/*
 * The original header of cronjobs/linkcheck.php:
 *
 *
 * @description Check all internal and external links in published content for broken URLs
 *
 * File containing the linkcheck.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\Cronjob\Kernel
{

class Linkcheck extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $cli->output( "Checking link ..." );

        // What is decided, and how, is expLinkCheck: https is tested like http, links to content by their
        // target, private addresses are not requested; settings in cronjob.ini [linkCheckSettings]
        $settings = \expLinkCheck::settingsFromIni();
        $checker = new \expLinkCheck( $settings );
        $started = microtime( true );
        $now = time();
        $counts = array( \expLinkCheck::VALID => 0, \expLinkCheck::INVALID => 0, \expLinkCheck::UNKNOWN => 0, 'skipped' => 0, 'changed' => 0 );

        $linkList = \eZURL::fetchList( array( 'only_published' => true, 'sort' => 'checked', 'as_object' => false ) );
        // the links checked longest ago first, so a run cut short by MaxURLsPerRun moves on next time
        $linkList = array_reverse( is_array( $linkList ) ? $linkList : array() );
        $checked = 0;
        foreach ( $linkList as $link )
        {
            $linkID = (int)$link['id'];
            $url = (string)$link['url'];
            $isValid = (bool)$link['is_valid'];
            if ( self::isRecent( (int)$link['last_checked'], $now, (int)$settings['RecheckInterval'] ) )
            {
                $counts['skipped']++;
                continue;
            }
            if ( $settings['MaxURLsPerRun'] > 0 && $checked >= $settings['MaxURLsPerRun'] )
            {
                $counts['skipped']++;
                continue;
            }
            $checked++;

            $answer = $checker->check( $url );
            $counts[$answer['result']]++;
            if ( $answer['result'] === \expLinkCheck::VALID && !$isValid )
            {
                \eZURL::setIsValid( $linkID, true );
                $counts['changed']++;
            }
            else if ( $answer['result'] === \expLinkCheck::INVALID && $isValid )
            {
                \eZURL::setIsValid( $linkID, false );
                $counts['changed']++;
            }
            \eZURL::setLastChecked( $linkID );

            $style = $answer['result'] === \expLinkCheck::VALID ? 'success' : ( $answer['result'] === \expLinkCheck::INVALID ? 'warning' : 'notice' );
            $cli->output( "check-" . $cli->stylize( 'emphasize', $url ) . " " . $cli->stylize( $style, $answer['result'] ) . " (" . $answer['reason'] . ")" );
        }

        $cli->output( sprintf( "All links have been checked! %d valid, %d invalid, %d not decided, %d skipped (checked recently or over MaxURLsPerRun), %d changed, %d requests, %.1f s",
                               $counts[\expLinkCheck::VALID], $counts[\expLinkCheck::INVALID], $counts[\expLinkCheck::UNKNOWN],
                               $counts['skipped'], $counts['changed'], $checker->requests, microtime( true ) - $started ) );
    }

    /**
     * Whether a link was checked less than $interval seconds ago (never, when the interval is 0 or it was never
     * checked).
     *
     * @param int $lastChecked
     * @param int $now
     * @param int $interval
     * @return bool
     */
    public static function isRecent( $lastChecked, $now, $interval )
    {
        return $interval > 0 && $lastChecked > 0 && $lastChecked > $now - $interval;
    }
}

}
