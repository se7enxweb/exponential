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
     * @return array events (handled), removed (no one left to notify), kept (waiting for a digest), failed
     */
    static function process( $eventIDList = null )
    {
        $limit = 100;
        $result = array( 'events' => 0, 'removed' => 0, 'kept' => 0, 'failed' => 0 );
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
            return $result;
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
        return $result;
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
