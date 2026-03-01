<?php
/**
 * File containing the bccieExportUtils class
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

class bccieExportUtils
{
    public static function getObjectsWithCollectedInformation( $offset = 0, $limit = 10 )
    {
        $db = eZDB::instance();
        $objects = $db->arrayQuery(
            'SELECT DISTINCT ezinfocollection.contentobject_id,
                             ezcontentobject.name,
                             ezcontentobject_tree.main_node_id,
                             ezcontentclass.*,
                             ezcontentclass.identifier AS class_identifier
            FROM ezinfocollection,
                 ezcontentobject,
                 ezcontentobject_tree,
                 ezcontentclass
            WHERE ezinfocollection.contentobject_id=ezcontentobject.id
            AND ezcontentobject.contentclass_id=ezcontentclass.id
            AND ezinfocollection.contentobject_id=ezcontentobject_tree.contentobject_id',
            array(
                'limit' => (int)$limit,
                'offset' => (int)$offset
            )
        );

        foreach ( array_keys( $objects ) as $i )
        {
            $collections = eZInformationCollection::fetchCollectionsList(
                (int)$objects[$i]['contentobject_id'], /* object id */
                false, /* creator id */
                false, /* user identifier */
                false, /* limitArray */
                false, /* sortArray */
                false /* asObject */
            );
            $class = new eZContentClass( $objects[$i] );
            $objects[$i]['class_name'] = $class->attribute( 'name' );
            $first = $collections[0]['created'];
            $last = $first;

            for ( $j = 0; $j < count( $collections ); $j++ )
            {
                $current = $collections[$j]['created'];

                if ( $current < $first )
                {
                    $first = $current;
                }

                if ( $current > $last )
                {
                    $last = $current;
                }
            }

            $objects[$i]['first_collection'] = $first;
            $objects[$i]['last_collection'] = $last;
            $objects[$i]['collections'] = count( $collections );
        }

        return $objects;
    }

    public static function getCollectorObjectsCount()
    {
        $db = eZDB::instance();
        $infoCollectorObjectsQuery = $db->arrayQuery(
            'SELECT COUNT( DISTINCT ezinfocollection.contentobject_id ) as count
            FROM ezinfocollection,
                 ezcontentobject,
                 ezcontentobject_tree
            WHERE ezinfocollection.contentobject_id=ezcontentobject.id
            AND ezinfocollection.contentobject_id=ezcontentobject_tree.contentobject_id'
        );

        $numberOfInfoCollectorObjects = 0;

        if ( $infoCollectorObjectsQuery )
        {
            $numberOfInfoCollectorObjects = $infoCollectorObjectsQuery[0]['count'];
        }

        return $numberOfInfoCollectorObjects;
    }

    public static function getDateConditions( eZHTTPTool $http )
    {
        $start = false;
        $end = false;
        $days = false;
        $condition = null;

        $cieINI = eZINI::instance( 'cie.ini' );
        $exportUsingDaysCalcualation = $cieINI->variable( 'CieSettings', 'ExportUsingDaysCalcualation' ) == 'enabled' ? true : false;

        if ( $http->hasPostVariable( "start_year" ) && $http->postVariable( "start_year" ) != '' )
        {
            $start = mktime(
                0,
                0,
                0,
                (int)$http->postVariable( "start_month" ),
                (int)$http->postVariable( "start_day" ),
                (int)$http->postVariable( "start_year" )
            );
        }

        if ( $http->hasPostVariable( "end_year" ) && $http->postVariable( "end_year" ) != '' )
        {
            $end = mktime(
                23,
                59,
                59,
                (int)$http->postVariable( "end_month" ),
                (int)$http->postVariable( "end_day" ),
                (int)$http->postVariable( "end_year" )
            );
        }

        if ( $exportUsingDaysCalcualation && ( $start !== false and $end !== false ) )
        {
            $days = round( abs( $start - $end ) / 86400 );
        }

        if ( $start !== false and $end !== false )
        {
            $condition = array( false, array( $start, $end ) );
        }
        elseif ( $start !== false and $end === false )
        {
            $condition = array( '>', $start );
        }
        elseif ( $start === false and $end !== false )
        {
            $condition = array( '<', $end );
        }

        return array( 'conditions' => $condition,
                      'days'       => $days );
    }

    public static function getFileName( $exportFormat, $object )
    {
        $cieINI = eZINI::instance( 'cie.ini' );
        $exportFileName = $cieINI->variable( 'CieSettings', 'ExportFileName' );
        $exportFileNameDateFormat = $cieINI->variable( 'CieSettings', 'ExportFileNameDateFormat' );

        $exportContentObjectName = strtolower( str_replace( ' ', '_', $object->attribute( 'name' ) ) );
        $exportDateTimeString = date( $exportFileNameDateFormat );

        switch ( $exportFormat )
        {
            case 'csv':
                $filename = $exportFileName . $exportContentObjectName . '-on-' . $exportDateTimeString . ".csv";
                break;
            case 'sylk':
                $filename = $exportFileName . $exportContentObjectName . '-on-' . $exportDateTimeString . ".slk";
                break;
            default :
                $filename = $exportFileName . $exportContentObjectName . '-on-' . $exportDateTimeString . ".csv";
                break;
        }

        return $filename;
    }
}

?>