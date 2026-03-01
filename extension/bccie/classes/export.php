<?php
/**
 * File containing the Export functions file.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

/*
  Export Collections
*/

function exportCollections(
    $collections,
    $dir = 'var/export',
    $format = 'csv',
    $separator = ',',
    $days = false,
    $remove = false,
    $debug = false
)
{
    foreach ( $collections as $collection_id )
    {
        if ( $debug )
        {
            print_r( "Object Collection ID For Export: $item" . "\n" );
        }
        if ( is_numeric( $collection_id ) )
        {
            exportCollection( $collection_id, $dir, $format, $separator, $days, $debug );
        }
        if ( $debug )
        {
            print_r( "Object Collections Export Finished!\n" );
        }
    }
}


/*
  Exports Object Collection to File
*/

function exportCollection(
    $objectID = false,
    $dir = 'var/export',
    $format = 'csv',
    $separator = ',',
    $days = false,
    $debug = false
)
{
    $ret = false;
    $object = false;

    // Settings
    $ini = eZINI::instance( "cie.ini" );
    $excludeAttributeID = $ini->variable( "CieSettings", "ExcludeAttributeID" );

    if ( is_numeric( $objectID ) )
    {
        $object =& eZContentObject::fetch( $objectID );
        $classID = $object->attribute( 'contentclass_id' );
    }

    // eZDebug::writeDebug( $object );

    if ( is_numeric( $classID ) )
    {
        $class =& eZContentClass::fetch( $classID );
    }

    if ( $debug == true )
    {
        echo "Object ClassID: $classID\n";
    }

    if ( is_object( $class ) )
    {
        $className = $class->attribute( 'identifier' );
        $classDataMap = $class->attribute( 'data_map' );
    }

    // Settings
    $ini = eZINI::instance( "cie.ini" );
    $excludeAttributeID = $ini->variable( "CieSettings", "ExcludeAttributeID" );

    if ( $debug == true )
    {
        echo "Exporting Collection: $objectID\n";
        echo "Output Directory: $dir\n";
        echo "Output Format: $format\n";
        echo "Output Separator: $separator\n";
        echo "Object Collection ID: $objectID\n";
        echo "Object Class Name: $className\n";
    }

    // if ( $debug == true )
    // print_r( $classDataMap );
    // print_r( $class );
    // die( );

    if ( !$object )
    {
        die( 'Encountered Non-Object, Unknown Error' );
    }

    $collections = eZInformationCollection::fetchCollectionsList(
                                          $objectID,
                                              false,
                                              false,
                                              array()
    );

    $collection_count = eZInformationCollection::fetchCollectionCountForObject( $objectID );

    if ( $debug == true )
    {
        echo "Object Collection Count: $collection_count\n\n";
        echo "Object Collection Contents: \n";
        print_r( $collections );
    }

    $attributes_to_export = array();

    // fetch collection class attributes for export
    foreach ( $classDataMap as $attribute )
    {
        // print_r( $attribute );
        if ( is_object( $attribute ) )
        {
            $is_ic = $attribute->attribute( 'is_information_collector' );
            if ( is_numeric( $is_ic ) )
            {
                if ( $is_ic )
                {
                    $id = $attribute->attribute( 'id' );
                    $name = $attribute->attribute( 'identifier' );

                    $attributes_to_export[] = $id;

                    if ( $debug )
                    {
                        print_r( "Object Class Attribute Name: $name \n" );
                        // echo "Object Class Attribute is Information Collector: $is_ic\n";
                        print_r( "Object Class Attribute ID: $id \n\n" );
                    }
                }
            }
        }
    }

    // Set output file name pattern
    if ( $days != false )
    {
        $start = mktime( 0, 0, 0, date( "m" ), date( "d" ) - $days, date( "Y" ) );
        $namePattern = "_" . date( "Y-m-d", $start ) . "_to_" . date( "Y-m-d" );
    }
    else
    {
        $namePattern = "_export_" . date( "Y-m-d_H-i" );
    }

    // Set output file name
    switch ( $format )
    {
        case 'csv':
            $filename = $object->attribute( 'name' ) . $namePattern . ".csv";
            break;
        case 'sylk':
            $filename = $object->attribute( 'name' ) . $namePattern . ".slk";
            break;
        default :
            $filename = $object->attribute( 'name' ) . $namePattern . ".csv";
            break;
    }

    $sdir = $dir . '/';
    $path = $sdir . $filename;

    if ( $debug == true )
    {
        echo "Collection Output Filename: $filename\n";
        echo "Collection Output Path: $path\n";
    }

    if ( $debug == true )
    {
        echo "Class Attributes ot Export (Array): \n";
        print_r( $attributes_to_export );
        echo "\n";
    }

    print_r( "Object information collection record entries fetch in progress...\n" );

    $parser = new Parser();
    $data = $parser->exportInformationCollection(
                   $collections,
                       $attributes_to_export,
                       $separator,
                       $format,
                       $days
    );

    if ( $debug == true )
    {
        echo "Collection Output Content:\n";
        echo( "$data\n" );
    }

    $file = new eZFile();
    $file->create( $filename, $dir, $data );

    print_r( "Object Collection Data Export File Path: $path\n" );
    print_r( "Object Collection Export Completed!\n\n" );

    // flush();
    // eZExecution::cleanExit();
}

?>