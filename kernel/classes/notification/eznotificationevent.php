<?php
/**
 * File containing the eZNotificationEvent class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZNotificationEvent eznotificationevent.php
  \brief The class eZNotificationEvent does

*/
class eZNotificationEvent extends eZPersistentObject
{
    public $TypeString;
    public $EventType;
    const STATUS_CREATED = 0;
    const STATUS_HANDLED = 1;

    public function __construct( $row = array() )
    {
        parent::__construct( $row );
        $this->TypeString = $this->attribute( 'event_type_string' );
    }

    static function definition()
    {
        return array( "fields" => array( "id" => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         "status" => array( 'name' => 'Status',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         "event_type_string" => array( 'name' => "EventTypeString",
                                                                       'datatype' => 'string',
                                                                       'default' => '',
                                                                       'required' => true ),
                                         "data_int1" => array( 'name' => "DataInt1",
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         "data_int2" => array( 'name' => "DataInt2",
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         "data_int3" => array( 'name' => "DataInt3",
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         "data_int4" => array( 'name' => "DataInt4",
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         "data_text1" => array( 'name' => "DataText1",
                                                                'datatype' => 'text',
                                                                'default' => '',
                                                                'required' => true ),
                                         "data_text2" => array( 'name' => "DataText2",
                                                                'datatype' => 'text',
                                                                'default' => '',
                                                                'required' => true ),
                                         "data_text3" => array( 'name' => "DataText3",
                                                                'datatype' => 'text',
                                                                'default' => '',
                                                                'required' => true ),
                                         "data_text4" => array( 'name' => "DataText4",
                                                                'datatype' => 'text',
                                                                'default' => '',
                                                                'required' => true ) ),
                      "keys" => array( "id" ),
                      "function_attributes" => array( 'content' => 'content' ),
                      "increment_key" => "id",
                      "sort" => array( "id" => "asc" ),
                      "class_name" => "eZNotificationEvent",
                      "name" => "eznotificationevent" );
    }

    static function create( $type, $params = array() )
    {
        $row = array(
            "id" => null,
            'event_type_string' => $type,
            'data_int1' => 0,
            'data_int2' => 0,
            'data_int3' => 0,
            'data_int4' => 0,
            'data_text1' => '',
            'data_text2' => '',
            'data_text3' => '',
            'data_text4' => '' );
        $event = new eZNotificationEvent( $row );
        eZDebugSetting::writeDebug( 'kernel-notification', $event, "event" );
        $event->initializeEventType( $params );
        return $event;
    }

    function initializeEventType( $params = array() )
    {
        $eventType = $this->eventType();
        $eventType->initializeEvent( $this, $params );
        eZDebugSetting::writeDebug( 'kernel-notification', $this, 'event after initialization' );
    }

    function eventType()
    {
        if ( ! isset ( $this->EventType ) )
        {
            $this->EventType = eZNotificationEventType::create( $this->TypeString );
        }
        return $this->EventType;
    }


    /*!
     Returns the content for this event.
    */
    function content()
    {
        if ( $this->Content === null )
        {
            $eventType = $this->eventType();
            $this->Content = $eventType->eventContent( $this );
        }
        return $this->Content;
    }

    /*!
     Sets the content for the current event
    */
    function setContent( $content )
    {
        $this->Content = $content;
    }

    /**
     * Fetches notification events as objects, and returns them in an array.
     *
     * The optional $limit can be used to set an offset and a limit for the fetch. It is
     * passed to {@link eZPersistentObject::fetchObjectList()} and should be used in the same way.
     *
     * @static
     * @param array $limit An associative array with limitiations, can contain
     *                     - offset - Numerical value defining the start offset for the fetch
     *                     - length - Numerical value defining the max number of items to return
     * @return array An array of eZNotificationEvent objects
     */
    static function fetchList( $limit = null )
    {
        return eZPersistentObject::fetchObjectList( eZNotificationEvent::definition(),
                                                    null,  null, null, $limit,
                                                    true );
    }

    static function fetch( $eventID )
    {
        return eZPersistentObject::fetchObject( eZNotificationEvent::definition(),
                                                null,
                                                array( 'id' => $eventID ) );
    }

    /**
     * Fetches unhandled notification events as objects, and returns them in an array.
     *
     * The optional $limit can be used to set an offset and a limit for the fetch. It is
     * passed to {@link eZPersistentObject::fetchObjectList()} and should be used in the same way.
     *
     * @static
     * @param array $limit An associative array with limitiations, can contain
     *                     - offset - Numerical value defining the start offset for the fetch
     *                     - length - Numerical value defining the max number of items to return
     * @return array An array of eZNotificationEvent objects
     */
    static function fetchUnhandledList( $limit = null )
    {
        return eZPersistentObject::fetchObjectList( eZNotificationEvent::definition(),
                                                    null, array( 'status' => self::STATUS_CREATED ), null, $limit,
                                                    true );
    }

    /**
     * When the event was made. The table has no date of its own, so it is read from what the event is
     * about: the time of a time event, the creation of the published version, the creation of the
     * collaboration item.
     *
     * @return int|false a timestamp, false when it cannot be told (the content is gone, an unknown type)
     */
    function createdAt()
    {
        $db = eZDB::instance();
        switch ( $this->attribute( 'event_type_string' ) )
        {
            case 'ezcurrenttime':
            {
                $time = (int)$this->attribute( 'data_int1' );
                return $time > 0 ? $time : false;
            } break;

            case 'ezpublish':
            {
                $rows = $db->arrayQuery( 'SELECT created, modified FROM ezcontentobject_version WHERE contentobject_id=' . (int)$this->attribute( 'data_int1' ) .
                                         ' AND version=' . (int)$this->attribute( 'data_int2' ) );
                if ( !$rows )
                    return false;
                $time = max( (int)$rows[0]['created'], (int)$rows[0]['modified'] );
                return $time > 0 ? $time : false;
            } break;

            case 'ezcollaboration':
            {
                $rows = $db->arrayQuery( 'SELECT created, modified FROM ezcollab_item WHERE id=' . (int)$this->attribute( 'data_int1' ) );
                if ( !$rows )
                    return false;
                $time = max( (int)$rows[0]['created'], (int)$rows[0]['modified'] );
                return $time > 0 ? $time : false;
            } break;
        }
        return false;
    }

    /**
     * Removes the events that are older than $timestamp (see createdAt()). Events whose age cannot be told
     * are left alone unless $unknown is true. Collection items of a removed event are removed with it.
     *
     * @param int $timestamp
     * @param int|null $status only events in this status (STATUS_CREATED or STATUS_HANDLED); null: both
     * @param bool $unknown also remove events whose age is unknown
     * @param bool $dryRun count, do not remove
     * @return array removed (count), kept (count), unknown (count), ids (the removed ones)
     */
    static function removeOlderThan( $timestamp, $status = null, $unknown = false, $dryRun = false )
    {
        $result = array( 'removed' => 0, 'kept' => 0, 'unknown' => 0, 'ids' => array() );
        $conditions = $status === null ? null : array( 'status' => (int)$status );
        $lastID = 0;
        $db = eZDB::instance();
        do
        {
            $cond = is_array( $conditions ) ? $conditions : array();
            $cond['id'] = array( '>', $lastID );
            $events = eZPersistentObject::fetchObjectList( self::definition(), null, $cond, array( 'id' => 'asc' ),
                                                           array( 'offset' => 0, 'length' => 200 ), true );
            foreach ( $events as $event )
            {
                $lastID = (int)$event->attribute( 'id' );
                $created = $event->createdAt();
                if ( $created === false )
                {
                    ++$result['unknown'];
                    if ( !$unknown )
                        continue;
                }
                else if ( $created >= $timestamp )
                {
                    ++$result['kept'];
                    continue;
                }
                $result['ids'][] = $lastID;
                ++$result['removed'];
            }
        } while ( count( $events ) == 200 );

        if ( !$dryRun && $result['ids'] )
        {
            $db->begin();
            foreach ( array_chunk( $result['ids'], 100 ) as $chunk )
            {
                $list = implode( ',', array_map( 'intval', $chunk ) );
                $db->query( "DELETE FROM eznotificationcollection_item WHERE event_id IN ( $list )" );
                $db->query( "DELETE FROM eznotificationcollection WHERE event_id IN ( $list )" );
                $db->query( "DELETE FROM eznotificationevent WHERE id IN ( $list )" );
            }
            $db->commit();
        }
        return $result;
    }

    /**
     * Removes the handled events that nothing is waiting for any more. An event whose messages were kept for a
     * digest stays handled until the digest is sent; after that nothing removed it, so they piled up.
     *
     * @param bool $dryRun only count
     * @return int the number of events
     */
    static function cleanupHandled( $dryRun = false )
    {
        $db = eZDB::instance();
        $where = 'status = ' . self::STATUS_HANDLED . ' AND id NOT IN ( SELECT event_id FROM eznotificationcollection_item )';
        if ( $dryRun )
        {
            $rows = $db->arrayQuery( "SELECT COUNT(*) AS n FROM eznotificationevent WHERE $where" );
            return (int)$rows[0]['n'];
        }
        $rows = $db->arrayQuery( "SELECT COUNT(*) AS n FROM eznotificationevent WHERE $where" );
        $count = (int)$rows[0]['n'];
        if ( $count > 0 )
            $db->query( "DELETE FROM eznotificationevent WHERE $where" );
        return $count;
    }

    /*!
     \static
     Removes all notification events.
    */
    static function cleanup()
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM eznotificationevent" );
    }

    public $Content = null;
}

?>
