<?php
/**
 * A personal API token of a user for the remote services (table expservices_token). Only the SHA-256 hash of the
 * token is stored; the token itself is shown once, when it is created.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expServiceToken extends eZPersistentObject
{
    const PREFIX = 'expt_';

    public static function definition()
    {
        return array( 'fields' => array(
                'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'user_id' => array( 'name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'name' => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'token_hash' => array( 'name' => 'TokenHash', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'token_hint' => array( 'name' => 'TokenHint', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'created' => array( 'name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'last_used' => array( 'name' => 'LastUsed', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'expires' => array( 'name' => 'Expires', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'revoked' => array( 'name' => 'Revoked', 'datatype' => 'integer', 'default' => 0, 'required' => true ) ),
            'keys' => array( 'id' ), 'increment_key' => 'id', 'class_name' => 'expServiceToken', 'name' => 'expservices_token' );
    }

    /** @return string the SHA-256 hash that is stored for a token */
    public static function hash( $token )
    {
        return hash( 'sha256', (string)$token );
    }

    /** Creates and stores a token. @return array( expServiceToken, the token text, shown once ) */
    public static function issue( $userId, $name, $expires )
    {
        $token = self::PREFIX . bin2hex( random_bytes( 24 ) );
        $row = new self( array( 'user_id' => (int)$userId, 'name' => (string)$name, 'token_hash' => self::hash( $token ),
                                'token_hint' => substr( $token, 0, 9 ), 'created' => time(), 'last_used' => 0,
                                'expires' => (int)$expires, 'revoked' => 0 ) );
        $row->store();
        return array( $row, $token );
    }

    /** The row of a presented token, null when it is unknown. */
    public static function byToken( $token )
    {
        if ( !is_string( $token ) || strncmp( $token, self::PREFIX, strlen( self::PREFIX ) ) !== 0 || strlen( $token ) > 128 )
            return null;
        $row = eZPersistentObject::fetchObject( self::definition(), null, array( 'token_hash' => self::hash( $token ) ) );
        return $row instanceof self ? $row : null;
    }

    public static function fetchOfUser( $userId )
    {
        return (array)eZPersistentObject::fetchObjectList( self::definition(), null, array( 'user_id' => (int)$userId ), array( 'id' => 'asc' ) );
    }

    public static function fetchById( $id )
    {
        $row = eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ) );
        return $row instanceof self ? $row : null;
    }

    /** @return string|null why the token cannot be used, null when it is valid */
    public function problem()
    {
        if ( (int)$this->attribute( 'revoked' ) > 0 )
            return 'The token was revoked';
        $expires = (int)$this->attribute( 'expires' );
        if ( $expires > 0 && $expires < time() )
            return 'The token has expired';
        return null;
    }
}
