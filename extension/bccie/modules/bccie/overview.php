<?php
/**
 * File containing the overview module view.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

$http = eZHTTPTool::instance();
$module = $Params['Module'];
$offset = $Params['Offset'];

if ( !is_numeric( $offset ) )
{
    $offset = 0;
}


if ( $module->isCurrentAction( 'RemoveObjectCollection' )
     && $http->hasPostVariable(
             'ObjectIDArray'
    )
)
{
    $objectIDArray = $http->postVariable( 'ObjectIDArray' );
    $http->setSessionVariable( 'ObjectIDArray', $objectIDArray );

    $collections = 0;

    foreach ( $objectIDArray as $objectID )
    {
        $collections += eZInformationCollection::fetchCollectionCountForObject( $objectID );
    }

    $tpl = eZTemplate::factory();
    $tpl->setVariable( 'module', $module );
    $tpl->setVariable( 'collections', $collections );
    $tpl->setVariable( 'remove_type', 'objects' );

    $Result = array();
    $Result['content'] = $tpl->fetch( 'design:infocollector/confirmremoval.tpl' );
    $Result['path'] = array(
        array(
            'url' => false,
            'text' => ezpI18n::tr( 'kernel/infocollector', 'Collected information' )
        )
    );

    return;
}


if ( $module->isCurrentAction( 'ConfirmRemoval' ) )
{

    $objectIDArray = $http->sessionVariable( 'ObjectIDArray' );

    if ( is_array( $objectIDArray ) )
    {
        foreach ( $objectIDArray as $objectID )
        {
            eZInformationCollection::removeContentObject( $objectID );
        }
    }
}


if ( eZPreferences::value( 'admin_infocollector_list_limit' ) )
{
    switch ( eZPreferences::value( 'admin_infocollector_list_limit' ) )
    {
        case '2':
        {
            $limit = 25;
        }
            break;
        case '3':
        {
            $limit = 50;
        }
            break;
        default:
            {
            $limit = 10;
            }
            break;
    }
}
else
{
    $limit = 10;
}


$objects = bccieExportUtils::getObjectsWithCollectedInformation( $offset , $limit);
$numberOfInfoCollectorObjects = bccieExportUtils::getCollectorObjectsCount();

$viewParameters = array( 'offset' => $offset );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'module', $module );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'object_array', $objects );
$tpl->setVariable( 'object_count', $numberOfInfoCollectorObjects );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:bccie/overview.tpl' );
$Result['navigation_part'] = 'ezbccienavigationpart';
$Result['left_menu'] = 'design:bccie/export_menu.tpl';
$Result['path'] = array(
    array(
        'url' => false,
        'text' => ezpI18n::tr( 'extension/bccie', 'Collected information export' )
    )
);

?>