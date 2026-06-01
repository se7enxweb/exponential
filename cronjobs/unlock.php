<?php
/**
 * File containing the unlock.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$cli->output( 'Fetching objects with status : "locked"' );

$lockedObjectIDList = fetchLockedObjects();

if( !$lockedObjectIDList )
{
    $cli->output( 'No locked objects.' );
    $cli->output( 'Done' );
    return;
}

foreach( $lockedObjectIDList as $lockedContentObjectID )
{
    $object = eZContentObject::fetch( $lockedContentObjectID );

    $cli->output( 'Removing lock of '                                       , false );
    $cli->output( $cli->stylize( 'emphasize', $object->attribute( 'name' ) ), false );
    $cli->output( ' ... '                                                   , false );

    $status = unlockObject( $lockedContentObjectID );

    $statusString = 'Failed';
    $statusColor  = 'red';

    if( $status )
    {
        $statusString = 'Success';
        $statusColor  = 'green';
    }

    $cli->output( $cli->stylize( $statusColor, $statusString ) );
}

$cli->output( 'Done' );

function fetchLockedObjects()
{
    $db = eZDB::instance();

    if ( $db->databaseName() === 'mongo' )
    {
        $lockedStateRows = $db->aggregate( 'ezcobj_state', [
            [ '$match'   => [ 'identifier' => 'locked' ] ],
            [ '$project' => [ '_id' => 0, 'id' => 1 ] ],
        ] );
        if ( empty( $lockedStateRows ) ) return false;
        $lockedStateID = (int)$lockedStateRows[0]['id'];
        $rows = $db->aggregate( 'ezcobj_state_link', [
            [ '$match'   => [ 'contentobject_state_id' => $lockedStateID ] ],
            [ '$project' => [ '_id' => 0, 'contentobject_id' => 1 ] ],
        ] );
    }
    else
    {
        $sql = "SELECT ezcobj_state_link.contentobject_id
                FROM ezcobj_state_link, ezcobj_state
                WHERE ezcobj_state_link.contentobject_state_id = ezcobj_state.id
                  AND ezcobj_state.identifier = 'locked'";
        $rows = $db->arrayQuery( $sql );
    }

    if( $rows )
    {
        $contentObjectIDList = array();
        foreach( $rows as $row )
            $contentObjectIDList[] = $row['contentobject_id'];

        return $contentObjectIDList;
    }

    return false;
}

function unlockObject( $contentObjectID )
{
    $db  = eZDB::instance();

    if ( $db->databaseName() === 'mongo' )
    {
        // MongoDB: remove the locked-state link and insert the not-locked link
        $db->deleteWhere( 'ezcobj_state_link', [
            'contentobject_id'       => (int)$contentObjectID,
            'contentobject_state_id' => 2,
        ] );
        return $db->insert( 'ezcobj_state_link', [
            'contentobject_id'       => (int)$contentObjectID,
            'contentobject_state_id' => 1,
        ] );
    }

    $sql = 'UPDATE ezcobj_state_link
            SET contentobject_state_id = 1
            WHERE contentobject_id       = '. $db->escapeString( $contentObjectID ) .'
             AND  contentobject_state_id = 2';

    return $db->query( $sql );
}
?>
