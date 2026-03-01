<?php
/**
 * File containing the doexport module view.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

$http = eZHTTPTool::instance();
$module = $Params['Module'];
$objectID = $Params['ObjectID'];

$cieINI = eZINI::instance( 'cie.ini' );
$exportExecutionTimeLimit = $cieINI->variable( 'CieSettings', 'ExportExecutionTimeLimit' );

set_time_limit( $exportExecutionTimeLimit );

$object = false;
$exportCreationDate = false;
$exportModificationDate = false;

if ( is_numeric( $objectID ) )
{
    $object = eZContentObject::fetch( $objectID );
}

if ( !$object )
{
    return $module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );
}

$conditions = array( 'contentobject_id' => $objectID );

$dateConditions = bccieExportUtils::getDateConditions( $http );

if ( $dateConditions['conditions'] != null )
{
    $conditions['created'] = $dateConditions['conditions'];
}

$collections = eZPersistentObject::fetchObjectList(
    eZInformationCollection::definition(),
    null,
    $conditions,
    false,
    false
);

// TODO: change error handler
if ( !$collections )
{
    return $module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );
}

$counter = 0;
$attributesToExport = array();

while ( true )
{
    $currentattribute = $http->postVariable( "field_$counter" );
    if ( !$currentattribute )
    {
        break;
    }
    $attributesToExport[] = $currentattribute;
    $counter++;
}

if ( $http->hasPostVariable( "creation_date" ) )
{
   $exportCreationDate = true;
}

if ( $http->hasPostVariable( "modification_date" ) )
{
   $exportModificationDate = true;
}

$separationCharacter = $http->postVariable( "separation_char" );
$exportFormat = $http->postVariable( "export_type" );

$filename = bccieExportUtils::getFileName( $exportFormat, $object );

$parser = new Parser( $objectID );

$export_string = $parser->exportInformationCollection(
    $collections,
    $attributesToExport,
    $separationCharacter,
    $exportFormat,
    $dateConditions['days'],
    $exportCreationDate,
    $exportModificationDate
);

$exportFormatOutputHandler = bccieExportFormatOutputHandler::instance();
$exportFormatOutputHandler->setOutputFileName( $filename );

$exportFormatOutputHandler = $exportFormatOutputHandler->output( $export_string );

flush();

eZExecution::cleanExit();

?>