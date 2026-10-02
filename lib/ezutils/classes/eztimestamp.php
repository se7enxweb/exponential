<?php
/**
 * File containing the eZTimestamp class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

class eZTimestamp
{
    /*!
     \return a timestamp in UTC
    */
    public static function getUtcTimestampFromLocalTimestamp( $localTimestamp ) {

        // setTimestamp() takes an integer: text that is not a number (or an
        // array) raised a TypeError; it is treated like a missing timestamp.
        if ( $localTimestamp === null || $localTimestamp === '' || !is_numeric( $localTimestamp ) )
        {
            return null;
        }

        $utcTimezone = new \DateTimeZone( 'UTC' );
        $localTimezone = new \DateTimeZone( date_default_timezone_get() );

        // 'now' rather than null: passing null to DateTime is deprecated. The
        // value is replaced by setTimestamp() on the next line either way.
        $localDate = new \DateTime( 'now', $localTimezone );
        $localDate->setTimestamp( (int)$localTimestamp );
        $utcDate = new \DateTime( $localDate->format( 'Y-m-d H:i:s' ), $utcTimezone );

        return $utcDate->getTimestamp();
    }

    /*!
     \return a timestamp in timezone defined in php.ini
    */
    public static function getLocalTimestampFromUtcTimestamp( $utcTimestamp ) {

        // See getUtcTimestampFromLocalTimestamp().
        if ( $utcTimestamp === null || $utcTimestamp === '' || !is_numeric( $utcTimestamp ) )
        {
            return null;
        }

        $utcTimezone = new \DateTimeZone( 'UTC' );
        $localTimezone = new \DateTimeZone( date_default_timezone_get() );

        $utcDate = new \DateTime( 'now', $utcTimezone );
        $utcDate->setTimestamp( (int)$utcTimestamp );
        $localDate = new \DateTime( $utcDate->format( 'Y-m-d H:i:s' ), $localTimezone );

        return $localDate->getTimestamp();
    }
}
?>
