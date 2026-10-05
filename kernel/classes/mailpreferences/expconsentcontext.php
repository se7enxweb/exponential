<?php
/**
 * File containing the expConsentContext class.
 *
 * Where a change of a preference comes from, as the consent log records it: the source (page, link, admin, import,
 * signup, bridge, confirm, system), the exact wording the person saw, the IP address, the user who acted (an admin
 * changing someone else's preferences) and the siteaccess.
 *
 * \code
 * $context = expConsentContext::fromRequest( 'page', $wordingShownOnThePage );
 * $context = expConsentContext::system( 'Bounce reader: hard bounce' );
 * \endcode
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expConsentContext
{
    const SOURCES = array( 'page', 'link', 'admin', 'import', 'signup', 'bridge', 'confirm', 'system' );

    /** @var string */
    public $source;
    /** @var string */
    public $wording;
    /** @var string */
    public $ip;
    /** @var int */
    public $actorUserId;
    /** @var string */
    public $siteaccess;
    /** @var bool false: set() does not send the double opt-in mail itself (the caller sends it) */
    public $sendConfirmation = true;

    /**
     * @param string $source one of SOURCES
     * @param string $wording
     * @param string $ip
     * @param int $actorUserId
     * @param string $siteaccess
     */
    public function __construct( $source, $wording = '', $ip = '', $actorUserId = 0, $siteaccess = '' )
    {
        if ( !in_array( $source, self::SOURCES, true ) )
            throw new InvalidArgumentException( "Not a consent source: '$source'" );
        $this->source = $source;
        $this->wording = (string)$wording;
        $this->ip = substr( (string)$ip, 0, 64 );
        $this->actorUserId = (int)$actorUserId;
        $this->siteaccess = substr( (string)$siteaccess, 0, 100 );
    }

    /**
     * The context of the current request: client IP, the logged in user, the siteaccess.
     *
     * @param string $source
     * @param string $wording the exact text the person saw
     * @return expConsentContext
     */
    public static function fromRequest( $source, $wording = '' )
    {
        $ip = '';
        if ( class_exists( 'eZSys' ) && isset( $_SERVER['REMOTE_ADDR'] ) )
            $ip = (string)eZSys::clientIP();
        $actor = 0;
        if ( class_exists( 'eZUser' ) )
        {
            $user = eZUser::currentUser();
            if ( $user instanceof eZUser && $user->isRegistered() )
                $actor = (int)$user->attribute( 'contentobject_id' );
        }
        return new self( $source, $wording, $ip, $actor, self::currentSiteAccess() );
    }

    /**
     * A change the system makes (bounce reader, console, cleanup).
     *
     * @param string $wording
     * @param string $source
     * @return expConsentContext
     */
    public static function system( $wording = '', $source = 'system' )
    {
        $actor = 0;
        if ( class_exists( 'eZUser' ) && eZUser::currentUserID() )
        {
            $user = eZUser::currentUser();
            if ( $user instanceof eZUser && $user->isRegistered() )
                $actor = (int)$user->attribute( 'contentobject_id' );
        }
        return new self( $source, $wording, '', $actor, self::currentSiteAccess() );
    }

    /** @return string */
    public static function currentSiteAccess()
    {
        if ( isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            return (string)$GLOBALS['eZCurrentAccess']['name'];
        return PHP_SAPI === 'cli' ? 'cli' : '';
    }

    /** @return array */
    public function toArray()
    {
        return array( 'source' => $this->source, 'wording' => $this->wording, 'ip' => $this->ip,
                      'actor_user_id' => $this->actorUserId, 'siteaccess' => $this->siteaccess );
    }
}
