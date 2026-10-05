<?php
/**
 * File containing the expMailRecipient class.
 *
 * Identifies the person whose preferences are meant: a user account, or an e-mail address without account. An
 * address that belongs to an account is that account (one set of preferences per person).
 *
 * key() is what the tables store: 'u:<user id>' for an account, 'a:<hash>' for an address without account, where the
 * hash is expMailSuppression::hash( $email ) (sha256 of the address and the site secret), so the preference table
 * holds no readable address.
 *
 * \code
 * $r = expMailRecipient::fromUser( eZUser::currentUser() );
 * $r = expMailRecipient::fromAddress( 'someone@example.com' );
 * $r = expMailRecipient::fromToken( $token );            // null when the token is not valid
 * \endcode
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailRecipient
{
    /** @var int 0 without account */
    protected $userId = 0;

    /** @var string the address, lower case; '' when not known */
    protected $email = '';

    /** @var string */
    protected $key = '';

    protected function __construct()
    {
    }

    /**
     * @param eZUser $user
     * @return expMailRecipient
     */
    public static function fromUser( eZUser $user )
    {
        $r = new self();
        $r->userId = (int)$user->attribute( 'contentobject_id' );
        $r->email = self::normalise( (string)$user->attribute( 'email' ) );
        $r->key = 'u:' . $r->userId;
        return $r;
    }

    /**
     * @param int $userId
     * @return expMailRecipient|null
     */
    public static function fromUserId( $userId )
    {
        $user = eZUser::fetch( (int)$userId );
        return $user instanceof eZUser ? self::fromUser( $user ) : null;
    }

    /**
     * An address; the account it belongs to when there is one.
     *
     * @param string $email
     * @param bool $lookupAccount false: never the account (the address on its own)
     * @return expMailRecipient|null null for an address that is not valid
     */
    public static function fromAddress( $email, $lookupAccount = true )
    {
        $email = self::normalise( $email );
        if ( $email === '' || !eZMail::validate( $email ) )
            return null;
        if ( $lookupAccount )
        {
            $user = eZUser::fetchByEmail( $email );
            if ( $user instanceof eZUser )
                return self::fromUser( $user );
        }
        $r = new self();
        $r->email = $email;
        $r->key = 'a:' . expMailSuppression::hash( $email );
        return $r;
    }

    /**
     * The recipient of a link (any purpose).
     *
     * @param string $token
     * @param string|null $purpose the purpose the token must have; null: any
     * @return expMailRecipient|null
     */
    public static function fromToken( $token, $purpose = null )
    {
        $payload = expMailToken::decode( $token );
        if ( $payload === null || ( $purpose !== null && $payload['purpose'] !== $purpose ) )
            return null;
        return self::fromPayload( $payload );
    }

    /**
     * @param array $payload a verified token payload (expMailToken)
     * @return expMailRecipient|null
     */
    public static function fromPayload( array $payload )
    {
        if ( !empty( $payload['user'] ) )
        {
            $r = self::fromUserId( (int)$payload['user'] );
            if ( $r !== null )
                return $r;
            // the account is gone: the address the link was made for, if it carried one
        }
        if ( isset( $payload['email'] ) && $payload['email'] !== '' )
            return self::fromAddress( $payload['email'] );
        return null;
    }

    /**
     * A recipient by its stored key (for the admin and the console): 'u:<id>' gives the account; 'a:<hash>' gives a
     * recipient without address (only its key is known).
     *
     * @param string $key
     * @return expMailRecipient|null
     */
    public static function fromKey( $key )
    {
        $key = (string)$key;
        if ( preg_match( '/^u:(\d+)$/', $key, $m ) )
            return self::fromUserId( (int)$m[1] );
        if ( preg_match( '/^a:[0-9a-f]{64}$/', $key ) )
        {
            $r = new self();
            $r->key = $key;
            return $r;
        }
        return null;
    }

    /** @return string lower case, trimmed */
    public static function normalise( $email )
    {
        return strtolower( trim( (string)$email ) );
    }

    /** @return string 'u:<user id>' or 'a:<address hash>' */
    public function key()
    {
        return $this->key;
    }

    /** @return string the address ('' when only the key is known) */
    public function email()
    {
        return $this->email;
    }

    /** @return int the user id, 0 without account */
    public function userId()
    {
        return $this->userId;
    }

    /** @return bool */
    public function hasAccount()
    {
        return $this->userId > 0;
    }

    /** @return string a form for logs that does not show the address: 'u:14' or 'a:1f2e3d4c5b6a' */
    public function logKey()
    {
        return $this->userId > 0 ? $this->key : substr( $this->key, 0, 14 );
    }
}
