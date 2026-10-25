<?php
/**
 * File containing the expPasskey class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A passkey of a user: one row of exppasskey (doc/eng-us/guides/passkeys.md).
 *
 * Stored: the credential id (base64url) and its SHA-256 (hex, unique, what a sign-in looks the passkey up by), the
 * public key as the COSE bytes the authenticator sent (base64url) and its algorithm, the signature counter, the
 * user handle the browser keeps with a discoverable passkey (32 random bytes per account, base64url, the same for
 * all passkeys of the account; never the account id), the authenticator model (AAGUID), whether the passkey can be
 * and is backed up (synced passkeys), the transports the browser named, a name the user chooses, when it was made
 * and last used, and the address of that use. Nothing secret: a public key is all the server ever has.
 *
 * Registration, sign-in and re-authentication are expPasskeyService's; this class is the row.
 */
class expPasskey extends eZPersistentObject
{
    /** Names are cut to this length */
    const MAX_NAME_LENGTH = 100;

    /** Bytes of a user handle */
    const USER_HANDLE_BYTES = 32;

    public static function definition()
    {
        static $definition = null;
        if ( $definition === null )
        {
            $definition = array(
                'fields' => array(
                    'id' => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'user_id' => array( 'name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true,
                                        'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id',
                                        'multiplicity' => '1..*' ),
                    'name' => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'credential_id' => array( 'name' => 'CredentialID', 'datatype' => 'text', 'default' => '', 'required' => true ),
                    'credential_hash' => array( 'name' => 'CredentialHash', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'public_key' => array( 'name' => 'PublicKey', 'datatype' => 'text', 'default' => '', 'required' => true ),
                    'algorithm' => array( 'name' => 'Algorithm', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'sign_count' => array( 'name' => 'SignCount', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'user_handle' => array( 'name' => 'UserHandle', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'aaguid' => array( 'name' => 'AAGUID', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'transports' => array( 'name' => 'Transports', 'datatype' => 'string', 'default' => '', 'required' => true ),
                    'backup_eligible' => array( 'name' => 'BackupEligible', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'backup_state' => array( 'name' => 'BackupState', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'created' => array( 'name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'last_used' => array( 'name' => 'LastUsed', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                    'last_ip' => array( 'name' => 'LastIP', 'datatype' => 'string', 'default' => '', 'required' => true ),
                ),
                'keys' => array( 'id' ),
                'increment_key' => 'id',
                'function_attributes' => array(
                    'algorithm_name' => 'algorithmName',
                    'synced' => 'isSynced',
                    'transport_list' => 'transportList',
                    'never_used' => 'isNeverUsed',
                ),
                'sort' => array( 'created' => 'asc', 'id' => 'asc' ),
                'class_name' => 'expPasskey',
                'name' => 'exppasskey',
            );
        }
        return $definition;
    }

    // ------------------------------------------------------------------------------------------------------------
    // Values (no database)
    // ------------------------------------------------------------------------------------------------------------

    /**
     * The lookup key of a credential id.
     *
     * @param string $credentialId raw bytes
     * @return string 64 hex characters
     */
    public static function hashCredential( $credentialId )
    {
        return hash( 'sha256', (string)$credentialId );
    }

    /**
     * A name as it is stored: one line, no control characters, at most MAX_NAME_LENGTH characters; empty is
     * replaced by $default.
     *
     * @param mixed $name
     * @param string $default
     * @return string
     */
    public static function cleanName( $name, $default = 'Passkey' )
    {
        $name = is_string( $name ) ? $name : '';
        $name = preg_replace( '/[\x00-\x1f\x7f]+/u', ' ', $name );
        $name = trim( preg_replace( '/\s+/u', ' ', (string)$name ) );
        if ( function_exists( 'mb_substr' ) )
            $name = mb_substr( $name, 0, self::MAX_NAME_LENGTH, 'UTF-8' );
        else
            $name = substr( $name, 0, self::MAX_NAME_LENGTH );
        return $name === '' ? (string)$default : $name;
    }

    /**
     * The transports a browser named (getTransports()), the known ones only, as stored.
     *
     * @param mixed $value an array or a comma separated string
     * @return string
     */
    public static function cleanTransports( $value )
    {
        $known = array( 'usb', 'nfc', 'ble', 'smart-card', 'hybrid', 'internal' );
        $list = is_array( $value ) ? $value : explode( ',', (string)$value );
        $out = array();
        foreach ( $list as $t )
        {
            $t = strtolower( trim( (string)$t ) );
            if ( in_array( $t, $known, true ) && !in_array( $t, $out, true ) )
                $out[] = $t;
        }
        return implode( ',', $out );
    }

    // ------------------------------------------------------------------------------------------------------------
    // Attributes
    // ------------------------------------------------------------------------------------------------------------

    /** @return string ES256, RS256 ... */
    public function algorithmName()
    {
        return expWebAuthnCose::name( (int)$this->attribute( 'algorithm' ) );
    }

    /** @return bool a synced passkey (backed up by its provider) */
    public function isSynced()
    {
        return (int)$this->attribute( 'backup_state' ) === 1;
    }

    /** @return string[] */
    public function transportList()
    {
        $t = (string)$this->attribute( 'transports' );
        return $t === '' ? array() : explode( ',', $t );
    }

    /** @return bool */
    public function isNeverUsed()
    {
        return (int)$this->attribute( 'last_used' ) === 0;
    }

    /** @return string the credential id, raw bytes */
    public function credentialBytes()
    {
        $b = expWebAuthn::base64UrlDecode( (string)$this->attribute( 'credential_id' ) );
        return $b === false ? '' : $b;
    }

    /** @return string the COSE public key, raw bytes */
    public function publicKeyBytes()
    {
        $b = expWebAuthn::base64UrlDecode( (string)$this->attribute( 'public_key' ) );
        return $b === false ? '' : $b;
    }

    // ------------------------------------------------------------------------------------------------------------
    // Database
    // ------------------------------------------------------------------------------------------------------------

    /**
     * @param int $id
     * @return expPasskey|null
     */
    public static function fetch( $id )
    {
        $row = eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ) );
        return $row instanceof self ? $row : null;
    }

    /**
     * @param string $credentialId raw bytes
     * @return expPasskey|null
     */
    public static function fetchByCredential( $credentialId )
    {
        if ( !is_string( $credentialId ) || $credentialId === '' )
            return null;
        $row = eZPersistentObject::fetchObject( self::definition(), null, array( 'credential_hash' => self::hashCredential( $credentialId ) ) );
        return $row instanceof self ? $row : null;
    }

    /**
     * @param int $userID
     * @return expPasskey[]
     */
    public static function fetchByUser( $userID )
    {
        $rows = eZPersistentObject::fetchObjectList( self::definition(), null, array( 'user_id' => (int)$userID ),
                                                     array( 'created' => 'asc', 'id' => 'asc' ) );
        return is_array( $rows ) ? $rows : array();
    }

    /**
     * @param int $userID
     * @return int
     */
    public static function countByUser( $userID )
    {
        return count( self::fetchByUser( $userID ) );
    }

    /**
     * The user handle of an account: the one its passkeys carry, or a new random one for its first passkey.
     *
     * @param int $userID
     * @return string base64url
     */
    public static function userHandleFor( $userID )
    {
        foreach ( self::fetchByUser( $userID ) as $row )
            if ( (string)$row->attribute( 'user_handle' ) !== '' )
                return (string)$row->attribute( 'user_handle' );
        return expWebAuthn::base64UrlEncode( random_bytes( self::USER_HANDLE_BYTES ) );
    }

    /**
     * Stores a new passkey from a checked registration (expWebAuthn::verifyRegistration()).
     *
     * @param int $userID
     * @param array $registration
     * @param string $name
     * @param string $userHandle base64url
     * @param mixed $transports
     * @return expPasskey
     */
    public static function create( $userID, array $registration, $name, $userHandle, $transports )
    {
        $row = new self( array(
            'user_id' => (int)$userID,
            'name' => self::cleanName( $name ),
            'credential_id' => expWebAuthn::base64UrlEncode( $registration['credential_id'] ),
            'credential_hash' => self::hashCredential( $registration['credential_id'] ),
            'public_key' => expWebAuthn::base64UrlEncode( $registration['public_key'] ),
            'algorithm' => (int)$registration['alg'],
            'sign_count' => (int)$registration['sign_count'],
            'user_handle' => (string)$userHandle,
            'aaguid' => (string)$registration['aaguid'],
            'transports' => self::cleanTransports( $transports ),
            'backup_eligible' => $registration['backup_eligible'] ? 1 : 0,
            'backup_state' => $registration['backup_state'] ? 1 : 0,
            'created' => time(),
            'last_used' => 0,
            'last_ip' => '',
        ) );
        if ( !self::inTransaction( function () use ( $row ) { $row->store(); } ) || (int)$row->attribute( 'id' ) <= 0 )
            return null;
        return $row;
    }

    /**
     * Runs a write in a transaction of its own: on a busy database (SQLite) a transaction waits for the other writer
     * where a lone statement fails, and a failed write is reported instead of taken as done.
     *
     * @param callable $write
     * @return bool committed
     */
    public static function inTransaction( $write )
    {
        $db = eZDB::instance();
        $db->begin();
        try
        {
            call_user_func( $write );
        }
        catch ( Exception $e )
        {
            $db->rollback();
            eZDebug::writeError( 'Passkey write failed: ' . $e->getMessage(), __METHOD__ );
            return false;
        }
        return (bool)$db->commit();
    }

    /**
     * Records a use: the new counter, backup state, time and address.
     *
     * @param array $assertion expWebAuthn::verifyAssertion()
     * @return bool written
     */
    public function recordUse( array $assertion )
    {
        $this->setAttribute( 'sign_count', (int)$assertion['sign_count'] );
        $this->setAttribute( 'backup_state', !empty( $assertion['backup_state'] ) ? 1 : 0 );
        $this->setAttribute( 'last_used', time() );
        $this->setAttribute( 'last_ip', substr( (string)eZSys::clientIP(), 0, 64 ) );
        $row = $this;
        return self::inTransaction( function () use ( $row ) { $row->store(); } );
    }

    /** Removes every passkey of an account (the account is removed). */
    public static function removeByUser( $userID )
    {
        eZPersistentObject::removeObject( self::definition(), array( 'user_id' => (int)$userID ) );
    }
}
