<?php
/**
 * File containing the expMailSuppression class.
 *
 * The suppression list (table expmail_suppression): addresses no optional mail goes to (hard bounces, complaints,
 * "unsubscribe from all", legal requests, admin entries, the cjw_newsletter blacklist through the bridge). Only a
 * hash is stored: sha256( lowercase( trim( address ) ) . site secret ), so the address cannot be read from the table;
 * an admin finds an entry by entering the address. Entries are kept forever unless
 * mailpreferences.ini [SuppressionSettings] RetentionDays says otherwise.
 *
 * \code
 * expMailSuppression::add( 'someone@example.com', 'bounce', 'Hard bounce 550 5.1.1' );
 * expMailSuppression::isSuppressed( 'someone@example.com' );   // true
 * expMailSuppression::lift( 'someone@example.com' );           // or the hash
 * \endcode
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailSuppression extends eZPersistentObject
{
    const REASONS = array( 'bounce', 'complaint', 'unsubscribe_all', 'legal', 'admin', 'bridge' );

    static function definition()
    {
        $int = array( 'datatype' => 'integer', 'default' => 0, 'required' => true );
        $str = array( 'datatype' => 'string', 'default' => '', 'required' => true );
        return array( 'fields' => array( 'id' => array( 'name' => 'ID' ) + $int,
                                         'email_hash' => array( 'name' => 'EmailHash' ) + $str,
                                         'reason' => array( 'name' => 'Reason' ) + $str,
                                         'note' => array( 'name' => 'Note' ) + $str,
                                         'created' => array( 'name' => 'Created' ) + $int,
                                         'created_by' => array( 'name' => 'CreatedBy' ) + $int ),
                      'keys' => array( 'id' ),
                      'increment_key' => 'id',
                      'sort' => array( 'created' => 'desc', 'id' => 'desc' ),
                      'class_name' => 'expMailSuppression',
                      'name' => 'expmail_suppression' );
    }

    /**
     * @param string $email
     * @return string 64 hex characters
     */
    public static function hash( $email )
    {
        return hash( 'sha256', strtolower( trim( (string)$email ) ) . expMailSecret::derive( 'hash' ) );
    }

    /** @return bool the value is a hash, not an address */
    public static function isHash( $value )
    {
        return is_string( $value ) && preg_match( '/^[0-9a-f]{64}$/', $value ) === 1;
    }

    /**
     * @param string $email
     * @return bool
     */
    public static function isSuppressed( $email )
    {
        return self::fetchByHash( self::hash( $email ) ) !== null;
    }

    /**
     * @param string $email
     * @return string|null the reason of the entry, null when the address is not suppressed
     */
    public static function reason( $email )
    {
        $row = self::fetchByHash( self::hash( $email ) );
        return $row ? (string)$row->attribute( 'reason' ) : null;
    }

    /**
     * @param string $hash
     * @return expMailSuppression|null
     */
    public static function fetchByHash( $hash )
    {
        if ( !self::isHash( $hash ) )
            return null;
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'email_hash' => $hash ), true );
    }

    /**
     * Suppresses an address; an existing entry keeps its date and gets the new reason and note.
     *
     * @param string $email
     * @param string $reason one of REASONS
     * @param string $note free text for the admins (never the address)
     * @param int|null $actorUserId
     * @return bool
     */
    public static function add( $email, $reason, $note = '', $actorUserId = null )
    {
        if ( !in_array( $reason, self::REASONS, true ) )
            throw new InvalidArgumentException( "Not a suppression reason: '$reason'" );
        $email = strtolower( trim( (string)$email ) );
        if ( $email === '' )
            return false;
        $hash = self::hash( $email );
        $note = str_ireplace( $email, '[address]', (string)$note );
        $row = self::fetchByHash( $hash );
        if ( !$row )
            $row = new self( array( 'email_hash' => $hash, 'created' => time(),
                                    'created_by' => $actorUserId !== null ? (int)$actorUserId : (int)eZUser::currentUserID() ) );
        $row->setAttribute( 'reason', $reason );
        $row->setAttribute( 'note', mb_substr( $note, 0, 1000 ) );
        $row->store();
        self::audit( 'access.user.mail.suppress', $hash, $reason );
        return true;
    }

    /**
     * @param string $hashOrEmail
     * @return bool an entry was removed
     */
    public static function lift( $hashOrEmail )
    {
        $hash = self::isHash( $hashOrEmail ) ? $hashOrEmail : self::hash( $hashOrEmail );
        $row = self::fetchByHash( $hash );
        if ( !$row )
            return false;
        $reason = (string)$row->attribute( 'reason' );
        $row->remove();
        self::audit( 'access.user.mail.unsuppress', $hash, $reason );
        return true;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param string|null $reason only entries of this reason
     * @return expMailSuppression[]
     */
    public static function fetchList( $offset = 0, $limit = 50, $reason = null )
    {
        return (array)eZPersistentObject::fetchObjectList( self::definition(), null, $reason !== null ? array( 'reason' => (string)$reason ) : null,
                                                           array( 'created' => 'desc', 'id' => 'desc' ),
                                                           array( 'offset' => (int)$offset, 'length' => (int)$limit ), true );
    }

    /** @return int */
    public static function countList( $reason = null )
    {
        return (int)eZPersistentObject::count( self::definition(), $reason !== null ? array( 'reason' => (string)$reason ) : null );
    }

    /**
     * Removes entries older than [SuppressionSettings] RetentionDays (0, the default: none).
     *
     * @return int removed
     */
    public static function cleanup()
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $days = $ini->hasVariable( 'SuppressionSettings', 'RetentionDays' ) ? (int)$ini->variable( 'SuppressionSettings', 'RetentionDays' ) : 0;
        if ( $days <= 0 )
            return 0;
        $db = eZDB::instance();
        $before = time() - $days * 86400;
        $rows = $db->arrayQuery( 'SELECT COUNT(*) AS c FROM expmail_suppression WHERE created < ' . (int)$before );
        $n = isset( $rows[0]['c'] ) ? (int)$rows[0]['c'] : 0;
        if ( $n > 0 )
            $db->query( 'DELETE FROM expmail_suppression WHERE created < ' . (int)$before );
        return $n;
    }

    protected static function audit( $name, $hash, $reason )
    {
        if ( class_exists( 'expAudit' ) )
            expAudit::event( $name, array( 'object' => array( 'type' => 'mail_address', 'id' => substr( $hash, 0, 16 ) ),
                                           'reason' => $reason ) );
    }
}
