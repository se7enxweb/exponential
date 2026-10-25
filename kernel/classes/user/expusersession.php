<?php
/**
 * File containing the expUserSession class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One sign-in of a user (table expuser_session): when it started and was last used, from where (the address as the
 * request gave it), with what (the User-Agent header and what it names), how (the sign-in method, Remember me) and,
 * once it is over, when and why it ended (signed out, revoked by the user or an administrator, replaced, expired).
 *
 * The row is found by the hash (SHA-256) of a random token kept in the session itself (expUserSessionTracker::
 * SESSION_KEY); neither the token nor the session id is stored. A session whose row is ended or gone is signed out
 * on its next request, whatever session handler the site uses. See expUserSessionTracker.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUserSession extends eZPersistentObject
{
    const TABLE = 'expuser_session';

    /** The sign-in methods a row can have */
    const METHODS = array( 'password', 'two_factor', 'rest', 'soap', 'webdav', 'single_sign_on', 'unknown' );

    /** Why a row ended */
    const REASONS = array( 'signout', 'revoked', 'revoked_admin', 'revoked_others', 'replaced', 'password_change', 'expired' );

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $definition = array(
                'fields' => array(
                    'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'user_id' => array( 'name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true,
                                        'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*' ),
                    'token_hash' => array( 'name' => 'TokenHash', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'created' => array( 'name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'last_active' => array( 'name' => 'LastActive', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'first_ip' => array( 'name' => 'FirstIP', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'last_ip' => array( 'name' => 'LastIP', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'user_agent' => array( 'name' => 'UserAgent', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'browser' => array( 'name' => 'Browser', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'os' => array( 'name' => 'OS', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'device_type' => array( 'name' => 'DeviceType', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'method' => array( 'name' => 'Method', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'remember_until' => array( 'name' => 'RememberUntil', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'siteaccess' => array( 'name' => 'SiteAccess', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'ended' => array( 'name' => 'Ended', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'ended_by' => array( 'name' => 'EndedBy', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'end_reason' => array( 'name' => 'EndReason', 'datatype' => 'string', 'default' => '', 'required' => true ),
                ),
                'keys' => array( 'id' ),
                'increment_key' => 'id',
                'function_attributes' => array( 'is_active' => 'isActive', 'parsed' => 'parsed', 'approximate_ip' => 'approximateIP',
                                                'approximate_first_ip' => 'approximateFirstIP', 'label' => 'label' ),
                'sort' => array( 'last_active' => 'desc' ),
                'class_name' => 'expUserSession',
                'name' => self::TABLE,
            );
        }
        return $definition;
    }

    /**
     * @param string $tokenHash
     * @return expUserSession|null
     */
    public static function fetchByTokenHash( $tokenHash )
    {
        $row = self::fetchObject( self::definition(), null, array( 'token_hash' => (string)$tokenHash ) );
        return $row instanceof self ? $row : null;
    }

    /**
     * @param int $id
     * @return expUserSession|null
     */
    public static function fetchByID( $id )
    {
        $row = self::fetchObject( self::definition(), null, array( 'id' => (int)$id ) );
        return $row instanceof self ? $row : null;
    }

    /**
     * The sessions of a user, the most recently used first.
     *
     * @param int $userID
     * @param bool $activeOnly only those not ended
     * @param int $limit
     * @return expUserSession[]
     */
    public static function fetchForUser( $userID, $activeOnly = true, $limit = 200 )
    {
        $conditions = array( 'user_id' => (int)$userID );
        if ( $activeOnly )
            $conditions['ended'] = 0;
        $list = self::fetchObjectList( self::definition(), null, $conditions, array( 'last_active' => 'desc', 'id' => 'desc' ),
                                       array( 'offset' => 0, 'length' => (int)$limit ) );
        return is_array( $list ) ? $list : array();
    }

    /** @return bool not ended */
    public function isActive()
    {
        return (int)$this->attribute( 'ended' ) === 0;
    }

    /** @return array what the User-Agent header names (expUserAgent::parse()) */
    public function parsed()
    {
        return expUserAgent::parse( $this->attribute( 'user_agent' ) );
    }

    /** @return string the network of the last address */
    public function approximateIP()
    {
        return expUserAgent::approximateIP( $this->attribute( 'last_ip' ) );
    }

    /** @return string the network of the address of the sign-in */
    public function approximateFirstIP()
    {
        return expUserAgent::approximateIP( $this->attribute( 'first_ip' ) );
    }

    /** @return string "Firefox 131 on Windows 10/11" */
    public function label()
    {
        return expUserAgent::label( $this->parsed() );
    }

    /**
     * Ends the row (not removed: it stays for the history and the "new device" check until the retention removes it).
     *
     * @param string $reason one of REASONS
     * @param int $byUserID who ended it (0: the system)
     * @param int|null $time
     */
    public function end( $reason, $byUserID = 0, $time = null )
    {
        $this->setAttribute( 'ended', $time === null ? time() : (int)$time );
        $this->setAttribute( 'ended_by', (int)$byUserID );
        $this->setAttribute( 'end_reason', in_array( $reason, self::REASONS, true ) ? $reason : 'revoked' );
        $this->store( array( 'ended', 'ended_by', 'end_reason' ) );
    }
}
?>
