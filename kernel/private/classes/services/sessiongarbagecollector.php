<?php
/**
 * File containing the Exponential\Service\SessionGarbageCollector class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

namespace Exponential\Service;

/**
 * Removing expired sessions: one service for the sessions view (setup/session, "Remove timed out sessions"),
 * the command (bin/php/ezsessiongc.php) and the cronjob part (cronjobs/session_gc.php).
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 *   SessionGarbageCollector::collect()         expired sessions and the baskets they leave (view, command, cronjob part)
 *   SessionGarbageCollector::collect( false )  expired sessions only
 */
class SessionGarbageCollector
{
    /** @var bool the basket hook is registered in this process */
    private static $basketHookAdded = false;

    /**
     * Removes the sessions older than site.ini [Session] SessionTimeout.
     *
     * @param bool $cleanBaskets also remove the baskets of the removed sessions (eZBasket::cleanupExpired())
     * @return bool what eZSession::garbageCollector() returns
     */
    public static function collect( $cleanBaskets = true )
    {
        if ( $cleanBaskets )
            self::addBasketHook();
        return \eZSession::garbageCollector();
    }

    /**
     * Registers the gc_pre hook that removes the baskets of expired sessions, once per process.
     */
    public static function addBasketHook()
    {
        if ( self::$basketHookAdded )
            return;
        \eZSession::addCallback( 'gc_pre', array( __CLASS__, 'cleanupBaskets' ) );
        self::$basketHookAdded = true;
    }

    /**
     * The gc_pre hook: removes the baskets that expired before $time.
     *
     * @param \eZDBInterface $db
     * @param int $time
     */
    public static function cleanupBaskets( $db, $time )
    {
        \eZBasket::cleanupExpired( $time );
    }
}
