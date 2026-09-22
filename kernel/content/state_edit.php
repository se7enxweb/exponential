<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */


if ( !function_exists( 'stateEditPostFetch' ) ) {
function stateEditPostFetch( $module, $class, $object, $version, $contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage, &$validation )
{
}
}

if ( !function_exists( 'stateEditPreCommit' ) ) {
function stateEditPreCommit( $module, $class, $object, $version, $contentObjectAttributes, $editVersion, $editLanguage )
{
}
}

if ( !function_exists( 'stateEditActionCheck' ) ) {
function stateEditActionCheck( $module, $class, $object, $version, $contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage )
{
    if ( $object === null )
        return;
    if ( $module->isCurrentAction( 'StateEdit' ) )
    {
        $http = eZHTTPTool::instance();
        if ( $http->hasPostVariable( 'SelectedStateIDList' ) )
        {
            $selectedStateIDList = $http->postVariable( 'SelectedStateIDList' );
            $objectID = $object->attribute( 'id' );

            if ( eZOperationHandler::operationIsAvailable( 'content_updateobjectstate' ) )
            {
                $operationResult = eZOperationHandler::execute( 'content', 'updateobjectstate',
                                                                array( 'object_id'     => $objectID,
                                                                       'state_id_list' => $selectedStateIDList ) );
            }
            else
            {
                eZContentOperationCollection::updateObjectState( $objectID, $selectedStateIDList );
            }
            $module->redirectToView( 'edit', array( $object->attribute( 'id' ), $editVersion, $editLanguage, $fromLanguage ) );
        }
    }
}
}

if ( !function_exists( 'stateEditPreTemplate' ) ) {
function stateEditPreTemplate( $module, $class, $object, $version, $contentObjectAttributes, $editVersion, $editLanguage, $tpl )
{
}
}

if ( !function_exists( 'initializeStateEdit' ) ) {
function initializeStateEdit( $module )
{
    $module->addHook( 'post_fetch', 'stateEditPostFetch' );
    $module->addHook( 'pre_commit', 'stateEditPreCommit' );
    $module->addHook( 'action_check', 'stateEditActionCheck' );
    $module->addHook( 'pre_template', 'stateEditPreTemplate' );
}
}






?>
