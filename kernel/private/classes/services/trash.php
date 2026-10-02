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
 *   Trash::emptyTrash()                                        the view's Empty button: purgeInBatches(), then emptyArchived()
 *   Trash::purgeInBatches()                                    the batches, each in a transaction, a pause between them
 *   Trash::emptyArchived()                                     every archived object, 100 at a time
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
     * Purges what is in the trash in batches: $iterationLimit trash items at a time, each batch in a transaction
     * of its own, the content cache cleared and $sleep seconds of pause between batches. The loop of
     * eZScriptTrashPurge (the command and the cronjob part) and of the trash view's Empty button.
     *
     * The trash list is read with the current user's permissions, as content/trash shows it.
     *
     * @param int $iterationLimit trash items per batch
     * @param int $sleep seconds between batches
     * @param int|null $trashed only what was trashed before this timestamp, null for everything
     * @param callable|null $onPurged called after each object is purged, with the object
     * @param callable|null $onBatch called before ('start') and after ('end') each batch
     * @return array( 'ok' => bool, 'purged' => int ); ok is false when a batch could not be committed
     */
    public static function purgeInBatches( $iterationLimit = 100, $sleep = 1, $trashed = null, $onPurged = null, $onBatch = null )
    {
        $db = \eZDB::instance();
        $purged = 0;
        $trashCount = \eZContentObjectTrashNode::trashListCount( array( 'Trashed' => $trashed ) );
        while ( $trashCount > 0 )
        {
            if ( $onBatch )
                call_user_func( $onBatch, 'start' );
            $trashList = \eZContentObjectTrashNode::trashList( array( 'Limit' => $iterationLimit, 'Trashed' => $trashed ), false );

            $db->begin();
            foreach ( $trashList as $trashNode )
            {
                $object = $trashNode->attribute( 'object' );
                $object->purge();
                $purged++;
                if ( $onPurged )
                    call_user_func( $onPurged, $object );
            }
            if ( !$db->commit() )
                return array( 'ok' => false, 'purged' => $purged );

            $trashCount = \eZContentObjectTrashNode::trashListCount( array( 'Trashed' => $trashed ) );
            if ( $trashCount > 0 )
            {
                // an empty batch while the count says more: stop rather than loop for ever
                if ( !$trashList )
                    $trashCount = 0;
                else
                {
                    \eZContentObject::clearCache();
                    if ( $sleep > 0 )
                        sleep( $sleep );
                }
            }
            if ( $onBatch )
                call_user_func( $onBatch, 'end' );
        }
        return array( 'ok' => true, 'purged' => $purged );
    }

    /**
     * The trash view's Empty button: purgeInBatches() with the command's batch size and pause, then whatever
     * archived object is left without a trash entry (emptyArchived()), so the trash is empty afterwards.
     *
     * @param int $iterationLimit
     * @param int $sleep
     * @return array( 'ok' => bool, 'purged' => int )
     */
    public static function emptyTrash( $iterationLimit = 100, $sleep = 1 )
    {
        $result = self::purgeInBatches( $iterationLimit, $sleep );
        if ( $result['ok'] )
            $result['purged'] += self::emptyArchived( $iterationLimit );
        return $result;
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
