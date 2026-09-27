<?php
/**
 * File containing the eZDateTimeValidator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZDateTimeValidator ezdatetimevalidator.php
  \brief The class eZDateTimeValidator does

*/

class eZDateTimeValidator extends eZInputValidator
{
    static function validateDate( $day, $month, $year )
    {
        // checkdate() and mktime() take integers: a non-numeric string, an
        // array or null raised a TypeError (or a deprecation) instead of the
        // date being reported invalid. Numeric strings are converted as PHP
        // did before.
        if ( !self::toIntegers( $day, $month, $year ) )
            return eZInputValidator::STATE_INVALID;
        $check = checkdate( $month, $day, $year );
        $datetime = mktime( 0, 0, 0, $month, $day, $year );
        if ( !$check or
             $datetime === false )
        {
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    static function validateTime( $hour, $minute, $second = 0 )
    {
        // trim() raised a TypeError on an array and a deprecation on null.
        foreach ( array( $hour, $minute, $second ) as $part )
        {
            if ( !is_scalar( $part ) )
                return eZInputValidator::STATE_INVALID;
        }
        $hour = (string)$hour;
        $minute = (string)$minute;
        $second = (string)$second;
        if ( preg_match( '/\d+/', trim( $hour )   ) &&
             preg_match( '/\d+/', trim( $minute ) ) &&
             preg_match( '/\d+/', trim( $second ) ) &&
             $hour >= 0 && $minute >= 0 && $second >= 0 &&
             $hour < 24 && $minute < 60 && $second < 60 )
        {
            return eZInputValidator::STATE_ACCEPTED;
        }
        return eZInputValidator::STATE_INVALID;
    }

    static function validateDateTime( $day, $month, $year, $hour, $minute, $second = 0 )
    {
        // See validateDate(); the time is checked by validateTime() below,
        // mktime() only needs integers.
        if ( !self::toIntegers( $day, $month, $year ) or
             !is_scalar( $hour ) or !is_scalar( $minute ) or !is_scalar( $second ) or
             !is_numeric( $hour ) or !is_numeric( $minute ) or !is_numeric( $second ) )
            return eZInputValidator::STATE_INVALID;
        $hourNumber = (int)$hour;
        $minuteNumber = (int)$minute;
        $secondNumber = (int)$second;
        $check = checkdate( $month, $day, $year );
        $datetime = mktime( $hourNumber, $minuteNumber, $secondNumber, $month, $day, $year );
        if ( !$check or
             $datetime === false or
             eZDateTimeValidator::validateTime( $hour, $minute ) == eZInputValidator::STATE_INVALID )
        {
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     \private
     Converts each argument to an integer in place. Returns false when one of
     them is not a number (arrays, null, '', text), true otherwise.
    */
    private static function toIntegers( &...$values )
    {
        foreach ( $values as &$value )
        {
            if ( is_int( $value ) )
                continue;
            if ( !is_scalar( $value ) or is_bool( $value ) or !is_numeric( $value ) )
                return false;
            $value = (int)$value;
        }
        return true;
    }

    /// \privatesection
}

?>
