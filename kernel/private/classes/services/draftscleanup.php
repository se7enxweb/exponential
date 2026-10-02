<?php
/**
 * File containing the Exponential\Service\DraftsCleanup class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

namespace Exponential\Service;

/**
 * Removing drafts older than their configured lifetime: one service for the two cronjob parts that did it with
 * the same code and different settings, cronjobs/old_drafts_cleanup.php (the drafts users leave) and
 * cronjobs/internal_drafts_cleanup.php (the internal drafts an edit creates before it is saved).
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 *   DraftsCleanup::cleanup( DraftsCleanup::USER_DRAFTS )       content.ini [VersionManagement] DraftsDuration, DraftsCleanUpLimit
 *   DraftsCleanup::cleanup( DraftsCleanup::INTERNAL_DRAFTS )   InternalDraftsDuration, InternalDraftsCleanUpLimit
 *   DraftsCleanup::duration( array( 'days' => 90 ) )           the lifetime in seconds, false when none is set
 */
class DraftsCleanup
{
    /** The drafts users leave: version status draft. */
    const USER_DRAFTS = 'drafts';

    /** The internal drafts an edit creates before it is first saved: version status internal draft. */
    const INTERNAL_DRAFTS = 'internal';

    /**
     * Per kind: the version status, the content.ini [VersionManagement] settings of the limit and the lifetime,
     * and the lifetime when the setting is not there.
     *
     * @return array
     */
    public static function kinds()
    {
        return array(
            self::USER_DRAFTS => array( 'status' => \eZContentObjectVersion::STATUS_DRAFT,
                                        'limit' => 'DraftsCleanUpLimit',
                                        'duration' => 'DraftsDuration',
                                        'default' => array( 'days' => 90 ) ),
            // by default, only remove drafts older than 1 day
            self::INTERNAL_DRAFTS => array( 'status' => \eZContentObjectVersion::STATUS_INTERNAL_DRAFT,
                                            'limit' => 'InternalDraftsCleanUpLimit',
                                            'duration' => 'InternalDraftsDuration',
                                            'default' => array( 'hours' => 24 ) ),
        );
    }

    /**
     * The lifetime a duration setting describes: days, hours, minutes and seconds added up.
     *
     * @param mixed $durationSetting e.g. array( 'days' => 90 ), array( 'hours' => 24 )
     * @return int|false seconds, or false when the setting names no numeric part
     */
    public static function duration( $durationSetting )
    {
        $isDurationSet = false;
        $duration = 0;
        if ( is_array( $durationSetting ) )
        {
            if ( isset( $durationSetting[ 'days' ] ) and is_numeric( $durationSetting[ 'days' ] ) )
            {
                $duration += $durationSetting[ 'days' ] * 60 * 60 * 24;
                $isDurationSet = true;
            }
            if ( isset( $durationSetting[ 'hours' ] ) and is_numeric( $durationSetting[ 'hours' ] ) )
            {
                $duration += $durationSetting[ 'hours' ] * 60 * 60;
                $isDurationSet = true;
            }
            if ( isset( $durationSetting[ 'minutes' ] ) and is_numeric( $durationSetting[ 'minutes' ] ) )
            {
                $duration += $durationSetting[ 'minutes' ] * 60;
                $isDurationSet = true;
            }
            if ( isset( $durationSetting[ 'seconds' ] ) and is_numeric( $durationSetting[ 'seconds' ] ) )
            {
                $duration += $durationSetting[ 'seconds' ];
                $isDurationSet = true;
            }
        }
        return $isDurationSet ? $duration : false;
    }

    /**
     * Removes the drafts of one kind that are older than their lifetime, at most the configured limit of them.
     *
     * @param string $kind self::USER_DRAFTS or self::INTERNAL_DRAFTS
     * @return int|false|null the number removed (eZContentObjectVersion::removeVersions(), false for a limit that is not a
     *                        number), or null when no lifetime is set: nothing is removed then
     */
    public static function cleanup( $kind )
    {
        $kinds = self::kinds();
        if ( !isset( $kinds[$kind] ) )
            throw new \InvalidArgumentException( "Unknown kind of drafts: $kind" );
        $settings = $kinds[$kind];

        $ini = \eZINI::instance( 'content.ini' );
        $cleanUpLimit = $ini->hasVariable( 'VersionManagement', $settings['limit'] ) ?
                           $ini->variable( 'VersionManagement', $settings['limit'] ) : 0;
        $durationSetting = $ini->hasVariable( 'VersionManagement', $settings['duration'] ) ?
                              $ini->variable( 'VersionManagement', $settings['duration'] ) : $settings['default'];

        $duration = self::duration( $durationSetting );
        if ( $duration === false )
            return null;

        $expiryTime = time() - $duration;
        return \eZContentObjectVersion::removeVersions( $settings['status'], $cleanUpLimit, $expiryTime );
    }
}
