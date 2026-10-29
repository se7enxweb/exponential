<?php
/**
 * File containing the expUserSessionInfo class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What a signed-in session adds to its row of expuser_session (table expuser_session_info, one row per session,
 * keyed by the session's row id): the host the sign-in was made on, the country and place a local GeoIP database
 * names for its address (expUserGeoIP; empty without one) and the second step of the sign-in (auth_detail: totp,
 * email, recovery, passkey; empty for a sign-in without one).
 *
 * A table of its own, so an installation that has expuser_session from an earlier 6.0.15 build keeps tracking its
 * sessions until the update creates this one (update/common/scripts/6.0/createusersessiontable.php): without it the
 * sessions are listed without these details and nothing fails. Removed with its session (expUserSessionTracker::
 * cleanup(), removeForUser()).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUserSessionInfo extends eZPersistentObject
{
    const TABLE = 'expuser_session_info';

    /** The second steps a sign-in can name */
    const AUTH_DETAILS = array( 'totp', 'email', 'recovery', 'passkey', 'oauth' );

    /** @var array|null array( time, bool ) whether the table was there, and when that was asked */
    protected static $tableKnown = null;

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $definition = array(
                'fields' => array(
                    'session_id' => array( 'name' => 'SessionID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'user_id' => array( 'name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'host' => array( 'name' => 'Host', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'country' => array( 'name' => 'Country', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'location' => array( 'name' => 'Location', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'auth_detail' => array( 'name' => 'AuthDetail', 'datatype' => 'string', 'default' => '', 'required' => true ),
                ),
                'keys' => array( 'session_id' ),
                'class_name' => 'expUserSessionInfo',
                'name' => self::TABLE,
            );
        }
        return $definition;
    }

    /**
     * Whether the table is there (asked again after five minutes when it was not, so the update reaches a running
     * worker without a restart).
     *
     * @return bool
     */
    public static function available()
    {
        $now = time();
        if ( self::$tableKnown !== null && ( self::$tableKnown[1] || $now - self::$tableKnown[0] < 300 ) )
            return self::$tableKnown[1];
        $exists = false;
        try
        {
            if ( class_exists( 'expKernelTableSchema' ) )
                $exists = expKernelTableSchema::exists( self::TABLE, null, true ) === true;
        }
        catch ( Throwable $e )
        {
            $exists = false;
        }
        self::$tableKnown = array( $now, $exists );
        return $exists;
    }

    /** Forgets whether the table is there (tests, after the update). */
    public static function reset()
    {
        self::$tableKnown = null;
    }

    /**
     * The values of a row as they are stored: cut to their columns, the second step one of AUTH_DETAILS.
     *
     * @param array $values host, country, location, auth_detail
     * @return array
     */
    public static function clean( array $values )
    {
        $str = function ( $key, $len ) use ( $values ) {
            $v = isset( $values[$key] ) && is_scalar( $values[$key] ) ? trim( (string)$values[$key] ) : '';
            $v = preg_replace( '/[\x00-\x1F\x7F]/u', '', $v );
            return mb_substr( (string)$v, 0, $len );
        };
        $country = strtoupper( $str( 'country', 2 ) );
        $detail = $str( 'auth_detail', 32 );
        return array(
            'host' => strtolower( preg_replace( '/[^A-Za-z0-9.\-:\[\]]/', '', $str( 'host', 255 ) ) ),
            'country' => preg_match( '/^[A-Z]{2}$/', $country ) ? $country : '',
            'location' => $str( 'location', 128 ),
            'auth_detail' => in_array( $detail, self::AUTH_DETAILS, true ) ? $detail : '',
        );
    }

    /**
     * Stores the details of a session (nothing when the table is missing or there is nothing to store).
     *
     * @param int $sessionID the row of expuser_session
     * @param int $userID
     * @param array $values host, country, location, auth_detail
     * @return expUserSessionInfo|null
     */
    public static function record( $sessionID, $userID, array $values )
    {
        if ( (int)$sessionID <= 0 || !self::available() )
            return null;
        $values = self::clean( $values );
        if ( implode( '', $values ) === '' )
            return null;
        $row = new self( array( 'session_id' => (int)$sessionID, 'user_id' => (int)$userID ) + $values );
        $row->store();
        return $row;
    }

    /**
     * The details of some sessions.
     *
     * @param int[] $sessionIDs
     * @return array session id => array( host, country, location, auth_detail )
     */
    public static function fetchForSessions( array $sessionIDs )
    {
        $ids = array_values( array_unique( array_filter( array_map( 'intval', $sessionIDs ) ) ) );
        if ( !$ids || !self::available() )
            return array();
        $out = array();
        foreach ( array_chunk( $ids, 200 ) as $chunk )
        {
            $rows = self::fetchObjectList( self::definition(), null, array( 'session_id' => array( $chunk ) ), null, null, false );
            foreach ( (array)$rows as $row )
                $out[(int)$row['session_id']] = self::clean( $row );
        }
        return $out;
    }

    /**
     * The countries the sessions of a user were signed in from (the "new location" check).
     *
     * @param int $userID
     * @param int $exceptSessionID
     * @return string[]
     */
    public static function countriesOfUser( $userID, $exceptSessionID = 0 )
    {
        if ( !self::available() )
            return array();
        $rows = self::fetchObjectList( self::definition(), array( 'session_id', 'country' ), array( 'user_id' => (int)$userID ), null, array( 'offset' => 0, 'length' => 500 ), false );
        $out = array();
        foreach ( (array)$rows as $row )
            if ( (int)$row['session_id'] !== (int)$exceptSessionID && (string)$row['country'] !== '' )
                $out[(string)$row['country']] = true;
        return array_keys( $out );
    }

    /**
     * Removes the details of one session.
     *
     * @param int $sessionID
     */
    public static function removeForSession( $sessionID )
    {
        if ( (int)$sessionID > 0 && self::available() )
            self::removeObject( self::definition(), array( 'session_id' => (int)$sessionID ) );
    }

    /**
     * Removes the details of every session of a user.
     *
     * @param int $userID
     */
    public static function removeForUser( $userID )
    {
        if ( (int)$userID > 0 && self::available() )
            self::removeObject( self::definition(), array( 'user_id' => (int)$userID ) );
    }
}
?>
