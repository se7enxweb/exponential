<?php
/**
 * File containing the eZNotificationEventFilter class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZNotificationEventFilter eznotificationeventfilter.php
  \brief The class eZNotificationEventFilter does

*/
class eZNotificationEventFilter
{
    /**
     * Handles the pending notification events: every available handler sees each event, the messages it
     * schedules are sent now or kept for a digest, and an event nothing is waiting for is removed.
     *
     * @note Transaction unsafe. If you call several transaction unsafe methods you must enclose the calls
     *       within a db transaction; thus within db->begin and db->commit.
     *
     * @param array|null $eventIDList only these events (null: all pending ones); a handled event that is
     *        asked for again is not handled twice, since only pending events are fetched
     * @return array events (handled), removed (no one left to notify), kept (waiting for a digest), failed (handlers that threw),
     *         send_failed (mails the transport refused: kept and tried again next run), dropped (given up), retried (sent after an earlier
     *         failure), notes (short texts of the failures)
     */
    /** @var array what this pass could not deliver: send_failed (mails the transport refused), dropped (given up), retried (sent after an earlier failure), notes (short texts) */
    private static $delivery = array( 'send_failed' => 0, 'dropped' => 0, 'retried' => 0, 'notes' => array() );

    /** A handler reports a mail the transport did not take; its items are kept. */
    static function noteDeliveryFailure( $what = '' )
    {
        ++self::$delivery['send_failed'];
        if ( $what !== '' && count( self::$delivery['notes'] ) < 20 )
            self::$delivery['notes'][] = 'failed: ' . $what;
        eZDebug::writeWarning( 'The mail transport did not take a notification: ' . $what . '; it is kept for the next run', __METHOD__ );
    }

    /** A message was given up (too old, or an address that cannot be mailed); its items are removed. */
    static function noteDropped( $what = '' )
    {
        ++self::$delivery['dropped'];
        if ( $what !== '' && count( self::$delivery['notes'] ) < 20 )
            self::$delivery['notes'][] = 'dropped: ' . $what;
        eZDebug::writeWarning( 'A notification was given up: ' . $what, __METHOD__ );
    }

    /** [RuleSettings] RetryHours of notification.ini: how long a message that could not be sent is tried again (default 72). */
    static function retryHours()
    {
        $ini = eZINI::instance( 'notification.ini' );
        $hours = $ini->hasVariable( 'RuleSettings', 'RetryHours' ) ? (int)$ini->variable( 'RuleSettings', 'RetryHours' ) : 72;
        return $hours > 0 ? $hours : 72;
    }

    /**
     * Sends again the messages that were made for an event already handled and could not be sent then (their items
     * have no send date and the event is handled). A message older than RetryHours, or for an address that cannot be
     * mailed, is given up.
     *
     * @note Transaction unsafe.
     */
    static function retryUnsent()
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT i.id, i.address, i.collection_id, i.event_id FROM eznotificationcollection_item i, eznotificationevent e
                                  WHERE i.send_date = 0 AND e.id = i.event_id AND e.status = ' . eZNotificationEvent::STATUS_HANDLED . ' ORDER BY i.collection_id, i.id' );
        $byCollection = array();
        foreach ( $rows as $row )
            $byCollection[(int)$row['collection_id']][] = $row;
        $limit = time() - self::retryHours() * 3600;
        $transport = eZNotificationTransport::instance( 'ezmail' );
        foreach ( $byCollection as $collectionID => $items )
        {
            $collection = eZPersistentObject::fetchObject( eZNotificationCollection::definition(), null, array( 'id' => $collectionID ) );
            $event = eZNotificationEvent::fetch( (int)$items[0]['event_id'] );
            $created = $event ? $event->createdAt() : false;
            $ids = array();
            $addresses = array();
            foreach ( $items as $item )
            {
                if ( !eZMail::validate( $item['address'] ) )
                {
                    self::noteDropped( 'item ' . $item['id'] . ' has an address that cannot be mailed' );
                    eZPersistentObject::removeObject( eZNotificationCollectionItem::definition(), array( 'id' => (int)$item['id'] ) );
                    continue;
                }
                $ids[] = (int)$item['id'];
                $addresses[] = $item['address'];
            }
            if ( !$ids )
                continue;
            if ( !$collection || $created === false || $created < $limit )
            {
                foreach ( $ids as $id )
                    eZPersistentObject::removeObject( eZNotificationCollectionItem::definition(), array( 'id' => $id ) );
                self::noteDropped( count( $ids ) . ' message(s) of event ' . (int)$items[0]['event_id'] . ' not sent for more than ' . self::retryHours() . ' hours' );
                continue;
            }
            if ( $transport->send( $addresses, $collection->attribute( 'data_subject' ), $collection->attribute( 'data_text' ) ) )
            {
                foreach ( $ids as $id )
                    eZPersistentObject::removeObject( eZNotificationCollectionItem::definition(), array( 'id' => $id ) );
                self::$delivery['retried'] += count( $ids );
            }
            else
                self::noteDeliveryFailure( 'retry of ' . count( $ids ) . ' message(s) of event ' . (int)$items[0]['event_id'] );
        }
    }

    static function process( $eventIDList = null )
    {
        $limit = 100;
        self::$delivery = array( 'send_failed' => 0, 'dropped' => 0, 'retried' => 0, 'notes' => array() );
        $result = array( 'events' => 0, 'removed' => 0, 'kept' => 0, 'failed' => 0 );
        self::retryUnsent();
        $availableHandlers = eZNotificationEventFilter::availableHandlers();
        if ( is_array( $eventIDList ) )
        {
            $eventIDList = array_map( 'intval', $eventIDList );
            if ( count( $eventIDList ) == 0 )
                return $result;
            foreach ( array_chunk( $eventIDList, 100 ) as $chunk )
            {
                $eventList = eZPersistentObject::fetchObjectList( eZNotificationEvent::definition(), null,
                                                                  array( 'status' => eZNotificationEvent::STATUS_CREATED,
                                                                         'id' => array( $chunk ) ),
                                                                  array( 'id' => 'asc' ), null, true );
                foreach ( $eventList as $event )
                    self::processEvent( $event, $availableHandlers, $result );
                eZContentObject::clearCache();
            }
            eZNotificationCollection::removeEmpty();
            eZNotificationEvent::cleanupHandled();
            return array_merge( $result, self::$delivery );
        }
        do
        {
            // a handled event leaves the pending list, so the first window is always the next one
            $eventList = eZNotificationEvent::fetchUnhandledList( array( 'offset' => 0, 'length' => $limit ) );
            foreach( $eventList as $event )
                self::processEvent( $event, $availableHandlers, $result );
            eZContentObject::clearCache();
        } while ( count( $eventList ) == $limit ); // If less than limit, we're on the last iteration

        eZNotificationCollection::removeEmpty();
        eZNotificationEvent::cleanupHandled();
        return array_merge( $result, self::$delivery );
    }

    private static function processEvent( $event, $availableHandlers, array &$result )
    {
        $db = eZDB::instance();
        $db->begin();
        $failed = false;
        foreach( $availableHandlers as $handlerKey => $handler )
        {
            if ( $handler === false )
            {
                eZDebug::writeError( "Notification handler does not exist: $handlerKey", __METHOD__ );
                continue;
            }
            try
            {
                $handler->handle( $event );
            }
            catch ( Throwable $e )
            {
                // one handler's failure must not stop the others; the event is not retried, as a
                // handler that has sent already would send again
                $failed = true;
                eZDebug::writeError( "Notification handler $handlerKey failed on event " . $event->attribute( 'id' ) . ': ' . $e->getMessage(), __METHOD__ );
            }
        }
        if ( $failed )
            ++$result['failed'];
        ++$result['events'];
        if ( eZNotificationCollectionItem::fetchCountForEvent( $event->attribute( 'id' ) ) == 0 )
        {
            $event->remove();
            ++$result['removed'];
        }
        else
        {
            $event->setAttribute( 'status', eZNotificationEvent::STATUS_HANDLED );
            $event->store();
            ++$result['kept'];
        }
        $db->commit();
    }

    static function availableHandlers()
    {
        $notificationINI = eZINI::instance( 'notification.ini' );
        $availableHandlers = $notificationINI->variable( 'NotificationEventHandlerSettings', 'AvailableNotificationEventTypes' );
        $repositoryDirectories = array();
        $extensionDirectories = $notificationINI->variable( 'NotificationEventHandlerSettings', 'ExtensionDirectories' );
        foreach ( $extensionDirectories as $extensionDirectory )
        {
            $extensionBase = eZExtension::extensionPath( $extensionDirectory );
            if ( $extensionBase === false )
                continue;

            $extensionPath = $extensionBase . '/notification/handler';
            if ( file_exists( $extensionPath ) )
                $repositoryDirectories[] = $extensionPath;
        }
        $handlers = array();
        foreach( $availableHandlers as $handlerString )
        {
            $eventHandler = eZNotificationEventFilter::loadHandler( $repositoryDirectories, $handlerString );
            if ( is_object( $eventHandler ) )
                $handlers[$handlerString] = $eventHandler;
        }
        return $handlers;
    }

    static function loadHandler( $directories, $handlerString )
    {
        $foundHandler = false;
        $includeFile = '';

        $notificationINI = eZINI::instance( 'notification.ini' );
        $repositoryDirectories = $notificationINI->variable( 'NotificationEventHandlerSettings', 'RepositoryDirectories' );
        $extensionDirectories = $notificationINI->variable( 'NotificationEventHandlerSettings', 'ExtensionDirectories' );
        foreach ( $extensionDirectories as $extensionDirectory )
        {
            $extensionBase = eZExtension::extensionPath( $extensionDirectory );
            if ( $extensionBase === false )
                continue;

            $extensionPath = $extensionBase . '/notification/handler/';
            if ( file_exists( $extensionPath ) )
                $repositoryDirectories[] = $extensionPath;
        }

        foreach ( $repositoryDirectories as $repositoryDirectory )
        {
            $repositoryDirectory = trim( $repositoryDirectory, '/' );
            $includeFile = "{$repositoryDirectory}/{$handlerString}/{$handlerString}handler.php";
            if ( file_exists( $includeFile ) )
            {
                $foundHandler = true;
                break;
            }
        }
        if ( !$foundHandler  )
        {
            eZDebug::writeError( "Notification handler does not exist: $handlerString", __METHOD__ );
            return false;
        }
        include_once( $includeFile );
        $className = $handlerString . "handler";
        return new $className();
    }

    /*!
     \static
     Goes through all event handlers and tells them to cleanup.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    static function cleanup()
    {
        $availableHandlers = eZNotificationEventFilter::availableHandlers();

        $db = eZDB::instance();
        $db->begin();
        foreach( $availableHandlers as $handler )
        {
            if ( $handler !== false )
            {
                $handler->cleanup();
            }
        }
        $db->commit();
    }
}

?>
