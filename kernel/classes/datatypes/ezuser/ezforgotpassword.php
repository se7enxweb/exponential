<?php
/**
 * File containing the eZForgotPassword class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZForgotPassword ezforgotpassword.php
  \ingroup eZDatatype
  \brief The class eZForgotPassword does

*/

class eZForgotPassword extends eZPersistentObject
{
    static function definition()
    {
        return array( "fields" => array( "id" => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         "user_id" => array( 'name' => "UserID",
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true,
                                                             'foreign_class' => 'eZUser',
                                                             'foreign_attribute' => 'contentobject_id',
                                                             'multiplicity' => '0..*' ),
                                         "hash_key" => array( 'name' => "HashKey",
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => true ),
                                         "time" => array( 'name' => "Time",
                                                          'datatype' => 'integer',
                                                          'default' => 0,
                                                          'required' => true ) ),
                      "keys" => array( "id" ),
                      "increment_key" => "id",
                      "sort" => array( "id" => "asc" ),
                      "class_name" => "eZForgotPassword",
                      "name" => "ezforgot_password" );
    }

    static function createNew( $userID, $hashKey, $time)
    {
        return new eZForgotPassword( array( "user_id" => $userID,
                                            "hash_key" => $hashKey,
                                            "time" => $time ) );
    }

    static function fetchByKey( $hashKey )
    {
        return eZPersistentObject::fetchObject( eZForgotPassword::definition(),
                                                null,
                                                array( "hash_key" => $hashKey ),
                                                true );
    }

    /** How long a forgot-password link works when site.ini says nothing: one hour */
    const DEFAULT_LIFETIME = 3600;

    /**
     * How long a forgot-password key works after it was made: site.ini [UserSettings] ForgotPasswordKeyLifetime, in
     * seconds. 0 switches the expiry off (the behavior before 6.0.15: a key works until it is used); a missing or
     * negative value is the default of one hour.
     *
     * @param eZINI|null $ini site.ini (tests pass their own)
     * @return int seconds, 0 for never
     */
    public static function lifetime( $ini = null )
    {
        if ( !$ini instanceof eZINI )
            $ini = eZINI::instance();
        if ( !$ini->hasVariable( 'UserSettings', 'ForgotPasswordKeyLifetime' ) )
            return self::DEFAULT_LIFETIME;
        $value = trim( (string)$ini->variable( 'UserSettings', 'ForgotPasswordKeyLifetime' ) );
        if ( $value === '' || !is_numeric( $value ) || (int)$value < 0 )
            return self::DEFAULT_LIFETIME;
        return (int)$value;
    }

    /**
     * @param int $time when the key was made (the row's time)
     * @param int $lifetime seconds, 0 for never
     * @param int $now
     * @return bool the key no longer works
     */
    public static function isExpiredAt( $time, $lifetime, $now )
    {
        $lifetime = (int)$lifetime;
        if ( $lifetime <= 0 )
            return false;
        return (int)$now >= (int)$time + $lifetime;
    }

    /**
     * @param int|null $now default: time()
     * @param int|null $lifetime default: lifetime()
     * @return bool this key no longer works
     */
    public function isExpired( $now = null, $lifetime = null )
    {
        return self::isExpiredAt( (int)$this->attribute( 'time' ), $lifetime === null ? self::lifetime() : (int)$lifetime,
                                  $now === null ? time() : (int)$now );
    }

    /**
     * @param int|null $lifetime default: lifetime()
     * @return int|null the Unix time until which this key works, null when keys do not expire
     */
    public function expiresAt( $lifetime = null )
    {
        $lifetime = $lifetime === null ? self::lifetime() : (int)$lifetime;
        return $lifetime > 0 ? (int)$this->attribute( 'time' ) + $lifetime : null;
    }

    /**
     * Removes the keys that no longer work. Called by user/forgotpassword on every request (one DELETE on a small
     * table), so no cronjob is needed; nothing happens when keys do not expire.
     *
     * @param int|null $lifetime default: lifetime()
     * @param int|null $now default: time()
     * @return bool something was asked to be removed
     */
    public static function removeExpired( $lifetime = null, $now = null )
    {
        $lifetime = $lifetime === null ? self::lifetime() : (int)$lifetime;
        if ( $lifetime <= 0 )
            return false;
        $limit = ( $now === null ? time() : (int)$now ) - $lifetime;
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
            $db->deleteWhere( 'ezforgot_password', array( 'time' => array( '$lte' => $limit ) ) );
        else
            eZPersistentObject::removeObject( self::definition(), array( 'time' => array( '<=', $limit ) ) );
        return true;
    }

    /*!
     \static
     Removes all password reminders in the database.
    */
    static function cleanup()
    {
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            $db->deleteWhere( 'ezforgot_password', [] );
        }
        else
        {
            $db->query( "DELETE FROM ezforgot_password" );
        }
    }

    /*!
     Remove forgot password entries belonging to user \a $userID
    */
    static function removeByUserID( $userID )
    {
        eZPersistentObject::removeObject( eZForgotPassword::definition(),
                                          array( 'user_id' => $userID ) );
    }
}

?>
