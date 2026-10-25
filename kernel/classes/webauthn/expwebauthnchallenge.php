<?php
/**
 * File containing the expWebAuthnChallenge class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The WebAuthn challenges a session was given: each request for options gets a new random challenge, kept on the
 * server (in the session) with what it is for (register, login, reauth), the user it was issued to (0 before sign-in)
 * and when it ends. An answer is accepted only for a challenge of the same purpose and user that has not ended, and
 * the challenge is used up by the first answer that names it, right or wrong, so no answer can be replayed.
 *
 * A session keeps at most MAX challenges (several tabs, or a sign-in page opened twice); the oldest goes first.
 * The list logic is static and takes the list and the time as arguments, so it is tested without a session.
 */
class expWebAuthnChallenge
{
    const SESSION_KEY = 'ExpWebAuthnChallenges';

    /** Challenges kept per session */
    const MAX = 8;

    const PURPOSE_REGISTER = 'register';
    const PURPOSE_LOGIN = 'login';
    const PURPOSE_REAUTH = 'reauth';

    /**
     * Issues a challenge and remembers it in the session.
     *
     * @param string $purpose
     * @param int $userID 0 for a visitor not signed in
     * @param int $seconds how long it may be answered
     * @return string the challenge (raw bytes)
     */
    public static function issue( $purpose, $userID = 0, $seconds = 300 )
    {
        $challenge = expWebAuthn::newChallenge();
        $http = eZHTTPTool::instance();
        $list = $http->hasSessionVariable( self::SESSION_KEY ) ? $http->sessionVariable( self::SESSION_KEY ) : array();
        $list = self::add( is_array( $list ) ? $list : array(), $purpose, $challenge, $userID, time() + max( 30, (int)$seconds ), time() );
        $http->setSessionVariable( self::SESSION_KEY, $list );
        return $challenge;
    }

    /**
     * Uses up a challenge the browser answered.
     *
     * @param string $purpose
     * @param string $challenge raw bytes, from clientDataJSON
     * @param int $userID
     * @return bool it was issued for this purpose and user and has not ended
     */
    public static function consume( $purpose, $challenge, $userID = 0 )
    {
        $http = eZHTTPTool::instance();
        if ( !$http->hasSessionVariable( self::SESSION_KEY ) )
            return false;
        $list = $http->sessionVariable( self::SESSION_KEY );
        if ( !is_array( $list ) )
            return false;
        $ok = self::take( $list, $purpose, $challenge, $userID, time() );
        $http->setSessionVariable( self::SESSION_KEY, $list );
        return $ok;
    }

    /**
     * Adds a challenge to a list, dropping ended ones and the oldest beyond MAX.
     *
     * @param array $list
     * @param string $purpose
     * @param string $challenge raw bytes
     * @param int $userID
     * @param int $until
     * @param int $now
     * @return array
     */
    public static function add( array $list, $purpose, $challenge, $userID, $until, $now )
    {
        $kept = array();
        foreach ( $list as $entry )
            if ( is_array( $entry ) && isset( $entry['until'] ) && (int)$entry['until'] > $now )
                $kept[] = $entry;
        $kept[] = array( 'purpose' => (string)$purpose, 'challenge' => expWebAuthn::base64UrlEncode( $challenge ),
                         'user' => (int)$userID, 'until' => (int)$until );
        return array_slice( $kept, -self::MAX );
    }

    /**
     * Finds and removes a challenge. Every entry with the same value is removed, whether it matches or not.
     *
     * @param array $list changed in place
     * @param string $purpose
     * @param string $challenge raw bytes
     * @param int $userID
     * @param int $now
     * @return bool
     */
    public static function take( array &$list, $purpose, $challenge, $userID, $now )
    {
        if ( !is_string( $challenge ) || $challenge === '' )
            return false;
        $wanted = expWebAuthn::base64UrlEncode( $challenge );
        $ok = false;
        $kept = array();
        foreach ( $list as $entry )
        {
            if ( !is_array( $entry ) || !isset( $entry['challenge'], $entry['purpose'], $entry['user'], $entry['until'] ) )
                continue;
            if ( hash_equals( (string)$entry['challenge'], $wanted ) )
            {
                $ok = $ok || ( $entry['purpose'] === $purpose && (int)$entry['user'] === (int)$userID && (int)$entry['until'] > $now );
                continue;
            }
            if ( (int)$entry['until'] > $now )
                $kept[] = $entry;
        }
        $list = $kept;
        return $ok;
    }
}
