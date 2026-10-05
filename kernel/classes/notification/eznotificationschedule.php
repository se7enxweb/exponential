<?php
/**
 * File containing the eZNotificationSchedule class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZNotificationSchedule eznotificationschedule.php
  \brief The class eZNotificationSchedule does

*/

class eZNotificationSchedule
{
    /**
     * Computes the next send date of a digest and sets it as send_date of $item.
     *
     * @param eZNotificationCollectionItem|null $item Item to set the date on; null only computes the date, for a
     *                                               digest handler that needs the date without an item
     * @param array $settings 'frequency' (day, week or month), 'hour' and, for week and month, 'day'
     * @param int|null $now The time to compute from (Unix timestamp); null for now. For tests.
     * @return int|false The send date as Unix timestamp, false for settings that are not an array
     */
    static function setDateForItem( $item, $settings, $now = null )
    {
        if ( !is_array( $settings ) )
            return false;

        $now = $now === null ? time() : (int)$now;
        $sendDate = self::nextSendDate( $settings, $now );
        eZDebugSetting::writeDebug( 'kernel-notification', getdate( $sendDate ), "item date"  );
        if ( $item !== null )
        {
            $item->setAttribute( 'send_date', $sendDate );
        }
        return $sendDate;
    }

    /**
     * The next send date of a digest after $now, at the full hour 'hour' in the server's local time.
     *
     * The date is built from calendar fields (mktime), not by adding seconds, so a digest across a change between
     * summer and winter time still goes out at the chosen hour; a monthly day the next month does not have (31 in
     * February) falls on that month's last day.
     *
     * @param array $settings 'frequency' (day, week or month), 'hour' and, for week and month, 'day'
     * @param int $now Unix timestamp
     * @return int Unix timestamp; $now for an unknown frequency
     */
    static function nextSendDate( $settings, $now )
    {
        $hour = (int)$settings['hour'];
        $dayNum = isset( $settings['day'] ) ? (int)$settings['day'] : 0;
        $current = getdate( $now );
        // the chosen hour of today is still ahead
        $laterToday = $hour > $current['hours'];

        switch ( $settings['frequency'] )
        {
            case 'day':
            {
                return mktime( $hour, 0, 0, $current['mon'], $current['mday'] + ( $laterToday ? 0 : 1 ), $current['year'] );
            }

            case 'week':
            {
                $daysDiff = $dayNum - $current['wday'];
                if ( $daysDiff < 0 or ( $daysDiff == 0 and !$laterToday ) )
                {
                    $daysDiff += 7;
                }
                return mktime( $hour, 0, 0, $current['mon'], $current['mday'] + $daysDiff, $current['year'] );
            }

            case 'month':
            {
                // a chosen day larger than the month has falls on its last day
                $day = min( $dayNum, (int)date( 't', mktime( 0, 0, 0, $current['mon'], 1, $current['year'] ) ) );
                if ( $day > $current['mday'] or ( $day == $current['mday'] and $laterToday ) )
                {
                    return mktime( $hour, 0, 0, $current['mon'], $day, $current['year'] );
                }
                $nextMonth = mktime( 0, 0, 0, $current['mon'] + 1, 1, $current['year'] );
                $day = min( $dayNum, (int)date( 't', $nextMonth ) );
                return mktime( $hour, 0, 0, (int)date( 'n', $nextMonth ), $day, (int)date( 'Y', $nextMonth ) );
            }
        }
        return $now;
    }
}

?>
