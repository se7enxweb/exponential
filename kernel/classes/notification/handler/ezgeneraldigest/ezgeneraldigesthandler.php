<?php
/**
 * File containing the eZGeneralDigestHandler class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZGeneralDigestHandler ezgeneraldigesthandler.php
  \brief The class eZGeneralDigestHandler does

*/
class eZGeneralDigestHandler extends eZNotificationEventHandler
{
    const NOTIFICATION_HANDLER_ID = 'ezgeneraldigest';

    public function __construct()
    {
        parent::__construct( self::NOTIFICATION_HANDLER_ID, "General Digest Handler" );

    }

    function attributes()
    {
        return array_merge( array( 'settings',
                                   'all_week_days',
                                   'all_month_days',
                                   'available_hours' ),
                            eZNotificationEventHandler::attributes() );
    }

    function hasAttribute( $attr )
    {
        return in_array( $attr, $this->attributes() );
    }

    function attribute( $attr )
    {
        if ( $attr == 'settings' )
        {
            return $this->settings( eZUser::currentUser() );
        }
        else if ( $attr == 'all_week_days' )
        {
            return eZLocale::instance()->attribute( 'weekday_name_list' );
        }
        else if ( $attr == 'all_month_days' )
        {
            return range( 1, 31 );
        }
        else if ( $attr == 'available_hours' )
        {
            return array( '0:00',
                          '1:00',
                          '2:00',
                          '3:00',
                          '4:00',
                          '5:00',
                          '6:00',
                          '7:00',
                          '8:00',
                          '9:00',
                          '10:00',
                          '11:00',
                          '12:00',
                          '13:00',
                          '14:00',
                          '15:00',
                          '16:00',
                          '17:00',
                          '18:00',
                          '19:00',
                          '20:00',
                          '21:00',
                          '22:00',
                          '23:00' );
        }
        return eZNotificationEventHandler::attribute( $attr );
    }

    function settings( $user = false )
    {
        if ( $user === false )
        {
            $user = eZUser::currentUser();
        }
        $settings = eZGeneralDigestUserSettings::fetchByUserId( $user->attribute( 'contentobject_id' ) );
        if ( $settings == null )
        {
            $settings = eZGeneralDigestUserSettings::create( $user->attribute( 'contentobject_id' ) );
            $settings->store();
        }
        return $settings;
    }

    function handle( $event )
    {
        eZDebugSetting::writeDebug( 'kernel-notification', $event, "trying to handle event" );
        if ( $event->attribute( 'event_type_string' ) == 'ezcurrenttime' )
        {
            $date = $event->content();
            $timestamp = $date->attribute( 'timestamp' );

            $addressArray = $this->fetchUsersForDigest( $timestamp );

            $tpl = eZTemplate::factory();
            $prevTplUsageStats = $tpl->setIsTemplatesUsageStatisticsEnabled( false );

            $transport = eZNotificationTransport::instance( 'ezmail' );
            $failedAddresses = array();
            foreach ( $addressArray as $address )
            {
                // the digest holds the items of several handlers (content: ezsubtree, collaboration: ezcollaboration);
                // the items of a category the e-mail preferences refuse for this person are dropped (switched on
                // again, only new items are mailed), and without any left no digest is rendered
                $mailCategory = 'content';
                if ( class_exists( 'expNotificationMailCategoryHandler' ) )
                {
                    $categories = array();
                    foreach ( array_keys( self::fetchHandlersForUser( $timestamp, $address['address'] ) ) as $handlerID )
                    {
                        $category = expNotificationMailCategoryHandler::categoryForHandler( $handlerID );
                        if ( $category === null )
                            $category = 'content';
                        if ( expNotificationMailCategoryHandler::allowsAddress( $address['address'], $category ) )
                            $categories[$category] = true;
                        else
                            $this->removeItemsOfAddress( $address['address'], $timestamp, $handlerID );
                    }
                    if ( !$categories )
                        continue;
                    // a digest of collaboration items alone is collaboration mail; one with content is content mail
                    $mailCategory = isset( $categories['content'] ) ? 'content' : key( $categories );
                }
                $tpl->setVariable( 'date', $date );
                $tpl->setVariable( 'address', $address['address'] );
                $result = $tpl->fetch( 'design:notification/handler/ezgeneraldigest/view/plain.tpl' );
                $subject = $tpl->variable( 'subject' );

                $parameters = array( 'mail_category' => $mailCategory );
                if ( $tpl->hasVariable( 'content_type' ) )
                    $parameters['content_type'] = $tpl->variable( 'content_type' );

                if ( !$transport->send( $address['address'], $subject, $result, null, $parameters ) )
                {
                    // the transport did not take the mail: the items of this address stay for the next run
                    $failedAddresses[] = $address['address'];
                    eZNotificationEventFilter::noteDeliveryFailure( 'digest mail' );
                }
                eZDebugSetting::writeDebug( 'kernel-notification', $result, "digest result" );
            }

            $collectionItemIDList = $tpl->hasVariable( 'collection_item_id_list' ) ? $tpl->variable( 'collection_item_id_list' ) : array();
            eZDebugSetting::writeDebug( 'kernel-notification', $collectionItemIDList, "handled items" );

            $tpl->setIsTemplatesUsageStatisticsEnabled( $prevTplUsageStats );

            if ( is_array( $collectionItemIDList ) && $failedAddresses )
            {
                $collectionItemIDList = $this->keepItemsOfFailedAddresses( $collectionItemIDList, $failedAddresses, $timestamp );
            }

            if ( is_array( $collectionItemIDList ) && count( $collectionItemIDList ) > 0 )
            {
                $ini = eZINI::instance( 'notification.ini' );
                $countElements = $ini->variable( 'RuleSettings', 'LimitDeleteElements' );
                if ( !$countElements )
                {
                    $countElements = 50;
                }
                $splited = array_chunk( $collectionItemIDList, $countElements );
                foreach ( $splited as $key => $value )
                {
                    eZPersistentObject::removeObject( eZNotificationCollectionItem::definition(), array( 'id' => array( $value, '' ) ) );
                }
            }

        }
        return true;
    }


    /**
     * Takes the items of the addresses whose mail failed out of the list of items to remove, so the next run
     * sends them again. An item that has been due for longer than [RuleSettings] RetryHours is given up: it is
     * removed and counted (eZNotificationEventFilter::noteDropped()).
     *
     * @param array $itemIDList ids of the items that went into a digest
     * @param array $failedAddresses
     * @param int $timestamp the time of the time event
     * @return array the ids to remove
     */
    function keepItemsOfFailedAddresses( array $itemIDList, array $failedAddresses, $timestamp )
    {
        $db = eZDB::instance();
        $hours = eZNotificationEventFilter::retryHours();
        $in = 'address IN ( ' . implode( ', ', array_map( function ( $a ) use ( $db ) { return "'" . $db->escapeString( $a ) . "'"; }, $failedAddresses ) ) . ' )';
        $rows = $db->arrayQuery( "SELECT id, send_date FROM eznotificationcollection_item WHERE $in AND send_date != 0 AND send_date <= " . (int)$timestamp );
        $keep = array();
        foreach ( $rows as $row )
        {
            if ( (int)$row['send_date'] < (int)$timestamp - $hours * 3600 )
                eZNotificationEventFilter::noteDropped( 'digest item ' . $row['id'] . ' due since ' . date( 'Y-m-d H:i', (int)$row['send_date'] ) );
            else
                $keep[(int)$row['id']] = true;
        }
        return array_values( array_filter( $itemIDList, function ( $id ) use ( $keep ) { return !isset( $keep[(int)$id] ); } ) );
    }

    /**
     * Removes the digest items of an address that are due by $timestamp (the person's e-mail preferences refuse
     * them).
     *
     * @param string $address
     * @param int $timestamp
     * @param string|null $handlerID only the items of this handler (ezsubtree, ezcollaboration); null: all
     */
    function removeItemsOfAddress( $address, $timestamp, $handlerID = null )
    {
        if ( $handlerID !== null )
            $items = self::fetchItemsForUser( $timestamp, $address, $handlerID );
        else
            $items = eZPersistentObject::fetchObjectList( eZNotificationCollectionItem::definition(), null,
                                                          array( 'address' => (string)$address, 'send_date' => array( '', array( 1, (int)$timestamp ) ) ),
                                                          null, null, true );
        foreach ( (array)$items as $item )
            eZPersistentObject::removeObject( eZNotificationCollectionItem::definition(), array( 'id' => (int)$item->attribute( 'id' ) ) );
    }

    function fetchUsersForDigest( $timestamp )
    {
        return eZPersistentObject::fetchObjectList( eZNotificationCollectionItem::definition(),
                                                    array(), array( 'send_date' => array( '', array( 1, $timestamp ) ) ),
                                                    array( 'address' => 'asc' ),null,
                                                    false,false,array( array( 'operation' => 'distinct address' ) ) );

    }

    static function fetchHandlersForUser( $time, $address )
    {
        $db = eZDB::instance();

        $time = (int)$time;
        $address = $db->escapeString( $address );

        $query = "select distinct handler
                  from eznotificationcollection,
                       eznotificationcollection_item
                  where eznotificationcollection_item.collection_id = eznotificationcollection.id and
                        address='$address' and
                        send_date != 0 and
                        send_date < $time";
        $handlerResult = $db->arrayQuery( $query );
        $handlers = array();
        $availableHandlers = eZNotificationEventFilter::availableHandlers();
        foreach ( $handlerResult as $handlerName )
        {
            // the collection's handler names the handler (ezcollaboration), the settings list it by its class
            // (ezcollaborationnotification); a handler that is no longer available has nothing to show
            if ( isset( $availableHandlers[$handlerName['handler']] ) )
                $handlers[$handlerName['handler']] = $availableHandlers[$handlerName['handler']];
            else
                foreach ( $availableHandlers as $available )
                    if ( $available->attribute( 'id_string' ) == $handlerName['handler'] )
                        $handlers[$handlerName['handler']] = $available;
        }
        return $handlers;
    }

    static function fetchItemsForUser( $time, $address, $handler )
    {
        $db = eZDB::instance();

        $time = (int)$time;
        $address = $db->escapeString( $address );
        $handler = $db->escapeString( $handler );

        $query = "select eznotificationcollection_item.*
                  from eznotificationcollection,
                       eznotificationcollection_item
                  where eznotificationcollection_item.collection_id = eznotificationcollection.id and
                        address='$address' and
                        send_date != 0 and
                        send_date < $time and
                        handler = '$handler'
                        order by eznotificationcollection_item.event_id";
        $itemResult = $db->arrayQuery( $query );
        $items = array();
        foreach ( $itemResult as $itemRow )
        {
            $items[] = new eZNotificationCollectionItem( $itemRow );
        }
        return $items;
    }

    function storeSettings( $http, $module )
    {
        $user = eZUser::currentUser();
        $settings = eZGeneralDigestUserSettings::fetchByUserId( $user->attribute( 'contentobject_id' ) );
        if ( !$settings instanceof eZGeneralDigestUserSettings )
        {
            // a user who never opened the settings page has no row yet
            $settings = eZGeneralDigestUserSettings::create( $user->attribute( 'contentobject_id' ) );
        }

        if ( $http->hasPostVariable( 'ReceiveDigest_' . self::NOTIFICATION_HANDLER_ID ) )
        {
            $id = self::NOTIFICATION_HANDLER_ID;
            // what the form sends is checked: a digest type that is not one of the three, a time that is not
            // one of the offered hours, a day outside the week or the month would later schedule nothing or
            // fail in eZNotificationSchedule
            $digestType = (int)$http->postVariable( 'DigestType_' . $id, eZGeneralDigestUserSettings::TYPE_DAILY );
            if ( !in_array( $digestType, array( eZGeneralDigestUserSettings::TYPE_WEEKLY, eZGeneralDigestUserSettings::TYPE_MONTHLY,
                                                eZGeneralDigestUserSettings::TYPE_DAILY ), true ) )
                $digestType = eZGeneralDigestUserSettings::TYPE_DAILY;
            $time = (string)$http->postVariable( 'Time_' . $id, '0:00' );
            if ( !in_array( $time, $this->attribute( 'available_hours' ), true ) )
                $time = '0:00';
            $day = $settings->attribute( 'day' );
            if ( $digestType == eZGeneralDigestUserSettings::TYPE_WEEKLY )
            {
                $day = (string)$http->postVariable( 'Weekday_' . $id, '' );
                $weekDays = $this->attribute( 'all_week_days' );
                if ( !in_array( $day, $weekDays, true ) )
                    $day = (string)reset( $weekDays );
            }
            else if ( $digestType == eZGeneralDigestUserSettings::TYPE_MONTHLY )
            {
                $day = (int)$http->postVariable( 'Monthday_' . $id, 1 );
                $day = (string)min( 31, max( 1, $day ) );
            }
            $settings->setAttribute( 'receive_digest', 1 );
            $settings->setAttribute( 'digest_type', $digestType );
            $settings->setAttribute( 'day', $day );
            $settings->setAttribute( 'time', $time );
            $settings->store();
        }
        else
        {
            $settings->setAttribute( 'receive_digest', 0 );
            $settings->store();
        }
    }

    function cleanup()
    {
        eZGeneralDigestUserSettings::cleanup();
    }

}

?>
