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
     * @return int|false The send date as Unix timestamp, false for settings that are not an array
     */
    static function setDateForItem( $item, $settings )
    {
        if ( !is_array( $settings ) )
            return false;

        $dayNum = isset( $settings['day'] ) ? $settings['day'] : false;
        $hour = $settings['hour'];
        $currentDate = getdate();
        $hoursDiff = $hour - $currentDate['hours'];

        switch ( $settings['frequency'] )
        {
            case 'day':
            {
                if ( $hoursDiff <= 0 )
                {
                    $hoursDiff += 24;
                }

                $secondsDiff = 3600 * $hoursDiff
                     - $currentDate['seconds']
                     - 60 * $currentDate['minutes'];
            } break;

            case 'week':
            {
                $daysDiff = $dayNum - $currentDate['wday'];
                if ( $daysDiff < 0 or
                     ( $daysDiff == 0 and $hoursDiff <= 0 ) )
                {
                    $daysDiff += 7;
                }

                $secondsDiff = 3600 * ( $daysDiff * 24 + $hoursDiff )
                     - $currentDate['seconds']
                     - 60 * $currentDate['minutes'];
            } break;

            case 'month':
            {
                // If the daynum the user has chosen is larger than the number of days in this month,
                // then reduce it to the number of days in this month.
                $daysInMonth = intval( date( 't', mktime( 0, 0, 0, $currentDate['mon'], 1, $currentDate['year'] ) ) );
                if ( $dayNum > $daysInMonth )
                {
                    $dayNum = $daysInMonth;
                }

                $daysDiff = $dayNum - $currentDate['mday'];
                if ( $daysDiff < 0 or
                     ( $daysDiff == 0 and $hoursDiff <= 0 ) )
                {
                    $daysDiff += $daysInMonth;
                }

                $secondsDiff = 3600 * ( $daysDiff * 24 + $hoursDiff )
                     - $currentDate['seconds']
                     - 60 * $currentDate['minutes'];
            } break;
        }

        $sendDate = time() + $secondsDiff;
        eZDebugSetting::writeDebug( 'kernel-notification', getdate( $sendDate ), "item date"  );
        if ( $item !== null )
        {
            $item->setAttribute( 'send_date', $sendDate );
        }
        return $sendDate;
    }
}

?>
