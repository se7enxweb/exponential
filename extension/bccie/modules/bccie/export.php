<?php
/**
 * File containing the export module view.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

$http = eZHTTPTool::instance();
$module = $Params['Module'];
$objectID = $Params['ObjectID'];

$object = false;

if ( !isset( $offset ) )
{
    $offset = false;
}

if ( is_numeric( $objectID ) )
{
    $object = eZContentObject::fetch( $objectID );
}

if ( !$object )
{
    return $module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );
}

$collections = eZInformationCollection::fetchCollectionsList(
                                      $objectID, /* object id */
                                          false, /* creator id */
                                          false, /* user identifier */
                                          array() /* limit array */
);

$numberOfCollections = eZInformationCollection::fetchCollectionsCount( $objectID );

$objects = bccieExportUtils::getObjectsWithCollectedInformation();
$numberOfInfoCollectorObjects = bccieExportUtils::getCollectorObjectsCount();

$viewParameters = array( 'offset' => $offset );
$objectName = $object->attribute( 'name' );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'module', $module );
$tpl->setVariable( 'object', $object );
$tpl->setVariable( 'object_array', $objects );
$tpl->setVariable( 'object_count', $numberOfInfoCollectorObjects );
$tpl->setVariable( 'collection_array', $collections );
$tpl->setVariable( 'collection_count', $numberOfCollections );

if( $numberOfCollections >= 1 )
{
    $createdTimestamp = $collections[0]->attribute( 'created' );
    $startDay = date( 'd', $createdTimestamp );
    $startMonth = date( 'm', $createdTimestamp );
    $startYear = date( 'Y', $createdTimestamp );

    $tpl->setVariable( 'start_day', $startDay );
    $tpl->setVariable( 'start_month', $startMonth );
    $tpl->setVariable( 'start_year', $startYear );
}

$tpl->setVariable( 'end_day', date( 'd' ) );
$tpl->setVariable( 'end_month', date( 'm' ) );
$tpl->setVariable( 'end_year', date( 'Y' ) );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:bccie/export.tpl' );
$Result['navigation_part'] = 'ezbccienavigationpart';
$Result['left_menu'] = 'design:bccie/export_menu.tpl';
$Result['path'] = array(
    array(
        'url' => '/bccie/overview',
        'text' => ezpI18n::tr( 'extension/bccie', 'Collected information export' )
    ),
    array(
        'url' => false,
        'text' => $objectName
    )
);

?>