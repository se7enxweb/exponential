<?php
/**
 * File containing the eZShopReceipt class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * A permanent, bookmarkable address for a placed order: its receipt.
 *
 * shop/orderview/<id> works only for the session that placed an anonymous
 * order, and its address says nothing about who may see it. A receipt lives at
 * shop/orderreceipt/<token>, where the token is
 *
 *     base64url(payload) "." base64url(bcrypt(sha256(secret . payload)))
 *     payload = {"v":1,"o":<order id>,"c":<created>,"u":<user id>}
 *
 * bcrypt at a low cost (shop.ini [OrderReceiptSettings] BcryptCost, 4-6) makes
 * a guess cost work without making a page slow. Its salt is derived from the
 * secret and the payload rather than random, so an order has exactly one
 * receipt address -- the same every time it is asked for, forever -- and
 * nothing needs to be stored. sha256 first keeps the input under bcrypt's
 * 72-byte limit.
 *
 * The token is only the address. Seeing the receipt still takes being signed
 * in as the customer who placed the order, or holding shop/administrate; an
 * order placed without an account can be seen by shop administrators only.
 *
 * @package kernel
 */
class eZShopReceipt
{
    const VERSION = 1;
    const SECRET_FILE = 'secrets/orderreceipt.key';

    /** @var string|null the signing secret, once read */
    private static $secret = null;

    /**
     * The receipt token for an order.
     *
     * @param eZOrder $order
     * @return string
     */
    public static function token( eZOrder $order )
    {
        $payload = self::encode( json_encode( array( 'v' => self::VERSION,
                                                     'o' => (int)$order->attribute( 'id' ),
                                                     'c' => (int)$order->attribute( 'created' ),
                                                     'u' => (int)$order->attribute( 'user_id' ) ) ) );
        return $payload . '.' . self::encode( self::sign( $payload ) );
    }

    /**
     * The order a token names, when the token is genuine and still describes
     * that order; null for anything else. Says nothing about who may see it.
     *
     * @param string $token
     * @return eZOrder|null
     */
    public static function orderFromToken( $token )
    {
        $token = (string)$token;
        if ( strlen( $token ) > 512 || substr_count( $token, '.' ) !== 1 )
            return null;
        list( $payload, $sig ) = explode( '.', $token );
        $hash = self::decode( $sig );
        $data = json_decode( (string)self::decode( $payload ), true );
        if ( $hash === false || !is_array( $data ) || ( $data['v'] ?? null ) !== self::VERSION
             || !isset( $data['o'], $data['c'], $data['u'] ) )
            return null;
        // password_verify reads the cost and salt from the hash itself, so a
        // receipt made at an earlier BcryptCost still opens.
        if ( !password_verify( self::preHash( $payload ), $hash ) )
            return null;
        $order = eZOrder::fetch( (int)$data['o'] );
        if ( !$order instanceof eZOrder || $order->attribute( 'is_temporary' ) )
            return null;
        // The payload has to describe the order as it is, so a genuine token
        // for one order can never be pointed at another.
        if ( (int)$order->attribute( 'created' ) !== (int)$data['c']
             || (int)$order->attribute( 'user_id' ) !== (int)$data['u'] )
            return null;
        return $order;
    }

    /**
     * Whether a user may see an order's receipt: shop administrators any
     * order; a signed-in customer their own; nobody an anonymous order but an
     * administrator.
     *
     * @param eZOrder $order
     * @param eZUser|null $user the current user when null
     * @return bool
     */
    public static function canView( eZOrder $order, $user = null )
    {
        $user = $user instanceof eZUser ? $user : eZUser::currentUser();
        $administrate = $user->hasAccessTo( 'shop', 'administrate' );
        if ( $administrate['accessWord'] != 'no' )
            return true;
        $anonymousID = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        if ( !$user->isRegistered() || (int)$user->id() === $anonymousID )
            return false;
        return (int)$order->attribute( 'user_id' ) === (int)$user->id()
            && (int)$order->attribute( 'user_id' ) !== $anonymousID;
    }

    /**
     * The internal address of an order's receipt.
     *
     * @param eZOrder $order
     * @return string
     */
    public static function receiptURL( eZOrder $order )
    {
        return '/shop/orderreceipt/' . self::token( $order );
    }

    /**
     * The internal address the system links an order to: the receipt, or the
     * session-bound order view, as shop.ini [OrderViewSettings] OrderLinkView
     * says. Pass it through ezurl (or eZURI::transformURI) before printing.
     *
     * @param eZOrder $order
     * @return string
     */
    public static function linkURL( eZOrder $order )
    {
        // An order placed without an account has no customer who could sign in
        // to its receipt (administrators aside), so it keeps the order view,
        // which works for the session that placed it.
        $anonymousID = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        if ( self::linkView() === 'orderreceipt' && (int)$order->attribute( 'user_id' ) !== $anonymousID )
            return self::receiptURL( $order );
        return '/shop/orderview/' . (int)$order->attribute( 'id' ) . '/';
    }

    /**
     * linkURL() for an order id; the order view when there is no such order.
     *
     * @param int $orderID
     * @return string
     */
    public static function linkURLForID( $orderID )
    {
        $order = eZOrder::fetch( (int)$orderID );
        return $order instanceof eZOrder ? self::linkURL( $order ) : '/shop/orderview/' . (int)$orderID . '/';
    }

    /**
     * orderview or orderreceipt.
     *
     * @return string
     */
    public static function linkView()
    {
        $ini = eZINI::instance( 'shop.ini' );
        $view = $ini->hasVariable( 'OrderViewSettings', 'OrderLinkView' )
            ? strtolower( trim( $ini->variable( 'OrderViewSettings', 'OrderLinkView' ) ) ) : 'orderview';
        return $view === 'orderreceipt' ? 'orderreceipt' : 'orderview';
    }

    /** bcrypt of the pre-hashed payload, with a salt derived from it. */
    private static function sign( $payload )
    {
        $cost = self::cost();
        // 22 characters of bcrypt's own alphabet, derived from the secret and
        // the payload: the same order always gets the same receipt address.
        $salt = substr( strtr( rtrim( base64_encode( hash_hmac( 'sha256', 'salt|' . $payload, self::secret(), true ) ), '=' ),
                               '+', '.' ), 0, 22 );
        $hash = crypt( self::preHash( $payload ), sprintf( '$2y$%02d$', $cost ) . $salt );
        if ( !is_string( $hash ) || strlen( $hash ) !== 60 )
            throw new RuntimeException( 'bcrypt is not available to sign order receipts' );
        return $hash;
    }

    private static function preHash( $payload )
    {
        return hash( 'sha256', self::secret() . '|' . $payload );
    }

    private static function cost()
    {
        $ini = eZINI::instance( 'shop.ini' );
        $cost = $ini->hasVariable( 'OrderReceiptSettings', 'BcryptCost' )
            ? (int)$ini->variable( 'OrderReceiptSettings', 'BcryptCost' ) : 5;
        return max( 4, min( 6, $cost ) );
    }

    /**
     * The signing secret: shop.ini [OrderReceiptSettings] Secret when set,
     * otherwise a random one generated for this installation on first use and
     * kept in <VarDir>/secrets/orderreceipt.key (mode 0600). Changing it
     * changes every receipt address.
     *
     * @return string
     */
    public static function secret()
    {
        if ( self::$secret !== null )
            return self::$secret;
        $ini = eZINI::instance( 'shop.ini' );
        $configured = $ini->hasVariable( 'OrderReceiptSettings', 'Secret' )
            ? trim( (string)$ini->variable( 'OrderReceiptSettings', 'Secret' ) ) : '';
        if ( strlen( $configured ) >= 32 )
            return self::$secret = $configured;
        return self::$secret = self::fileSecret();
    }

    private static function fileSecret()
    {
        $dir = eZSys::varDirectory() . '/' . dirname( self::SECRET_FILE );
        $file = eZSys::varDirectory() . '/' . self::SECRET_FILE;
        $secret = is_file( $file ) ? trim( (string)file_get_contents( $file ) ) : '';
        if ( strlen( $secret ) >= 32 )
            return $secret;
        if ( !is_dir( $dir ) && !@mkdir( $dir, 0700, true ) && !is_dir( $dir ) )
            throw new RuntimeException( "cannot create $dir for the order receipt secret" );
        $secret = bin2hex( random_bytes( 32 ) );
        // Created exclusively, so two first requests at once cannot each keep
        // a different secret: the loser reads the winner's.
        $fh = @fopen( $file, 'x' );
        if ( $fh === false )
        {
            clearstatcache( true, $file );
            $existing = is_file( $file ) ? trim( (string)file_get_contents( $file ) ) : '';
            if ( strlen( $existing ) >= 32 )
                return $existing;
            throw new RuntimeException( "cannot write the order receipt secret to $file" );
        }
        fwrite( $fh, $secret . "\n" );
        fclose( $fh );
        @chmod( $file, 0600 );
        // Made by a server running as root, it must still be readable by the
        // web server's own user, who owns the var directory.
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
        {
            $owner = @fileowner( eZSys::varDirectory() );
            $group = @filegroup( eZSys::varDirectory() );
            if ( $owner !== false )
            {
                @chown( $dir, $owner ); @chown( $file, $owner );
                @chgrp( $dir, $group ); @chgrp( $file, $group );
            }
        }
        return $secret;
    }

    private static function encode( $bytes )
    {
        return rtrim( strtr( base64_encode( $bytes ), '+/', '-_' ), '=' );
    }

    private static function decode( $text )
    {
        if ( !preg_match( '/^[A-Za-z0-9_-]*$/', (string)$text ) )
            return false;
        return base64_decode( strtr( $text, '-_', '+/' ), true );
    }
}
?>
