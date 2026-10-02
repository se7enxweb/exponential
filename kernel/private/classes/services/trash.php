<?php
/**
 * File containing the Exponential\Service\Trash class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

namespace Exponential\Service;

/**
 * Emptying the trash: one service for the trash view (content/trash), the command (bin/php/trashpurge.php)
 * and the cronjob part (cronjobs/trashpurge.php). Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 *   Trash::purge( $cli, false, false, $script, 100, 1, 30 )   batched, transactional, with progress (CLI, cron)
 *   Trash::emptyArchived()                                     every archived object, 100 at a time (the view's Empty button)
 *   Trash::purgeObjects( $ids )                                the selected objects (the view's Remove button)
 *   Trash::canEmpty( $user )                                   content/cleantrash
 */
class Trash
{
    /**
     * The user may empty the trash: content/cleantrash, granted fully or with limitations.
     *
     * @param \eZUser $user
     * @return bool
     */
    public static function canEmpty( $user )
    {
        $access = $user->hasAccessTo( 'content', 'cleantrash' );
        return $access['accessWord'] == 'yes' || $access['accessWord'] == 'limited';
    }

    /**
     * Purges the objects with these ids.
     *
     * @param array $objectIDs content object ids
     * @return int the number of objects purged
     */
    public static function purgeObjects( $objectIDs )
    {
        $purged = 0;
        foreach ( $objectIDs as $deleteID )
        {
            $objectList = \eZPersistentObject::fetchObjectList( \eZContentObject::definition(),
                                                               null,
                                                               array( 'id' => $deleteID ),
                                                               null,
                                                               null,
                                                               true );
            foreach ( $objectList as $object )
            {
                $object->purge();
                $purged++;
            }
        }
        return $purged;
    }

    /**
     * Purges every archived (trashed) object, $batchSize at a time to limit the size of each transaction.
     *
     * @param int $batchSize
     * @return int the number of objects purged
     */
    public static function emptyArchived( $batchSize = 100 )
    {
        $purged = 0;
        while ( true )
        {
            $objectList = \eZPersistentObject::fetchObjectList( \eZContentObject::definition(),
                                                               null,
                                                               array( 'status' => \eZContentObject::STATUS_ARCHIVED ),
                                                               null,
                                                               $batchSize,
                                                               true );
            if ( count( $objectList ) < 1 )
                break;

            foreach ( $objectList as $object )
            {
                $object->purge();
                $purged++;
            }
        }
        return $purged;
    }

    /**
     * Empties the trash as an unattended job: eZScriptTrashPurge, in batches of $iterationLimit with a
     * transaction each, $sleep seconds between them, optionally only what has been in the trash for
     * $trashedDays days, with progress on $cli.
     *
     * @param \eZCLI $cli
     * @param bool $quiet
     * @param bool $memoryMonitoring log memory use to var/log/trashpurge.log
     * @param \eZScript|null $script for the progress dots
     * @param int|null $iterationLimit null: 100
     * @param int|null $sleep null: 1
     * @param int|null $trashedDays null: everything
     * @return bool the trash was emptied
     */
    public static function purge( $cli, $quiet = true, $memoryMonitoring = false, $script = null,
                                  $iterationLimit = null, $sleep = null, $trashedDays = null )
    {
        $purgeHandler = new \eZScriptTrashPurge( $cli, $quiet, $memoryMonitoring, $script );
        return $purgeHandler->run( $iterationLimit, $sleep, $trashedDays );
    }
}
