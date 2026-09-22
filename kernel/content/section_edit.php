<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */


if ( !function_exists( 'sectionEditPostFetch' ) ) {
function sectionEditPostFetch( $module, $class, $object, $version, $contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage, &$validation )
{
}
}

if ( !function_exists( 'sectionEditPreCommit' ) ) {
function sectionEditPreCommit( $module, $class, $object, $version, $contentObjectAttributes, $editVersion, $editLanguage )
{
}
}

if ( !function_exists( 'sectionEditActionCheck' ) ) {
function sectionEditActionCheck( $module, $class, $object, $version, $contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage )
{
    if ( !$module->isCurrentAction( 'SectionEdit' ) )
        return;

    $http = eZHTTPTool::instance();
    if ( !$http->hasPostVariable( 'SelectedSectionId' ) )
        return;

    $selectedSection = eZSection::fetch( (int)$http->postVariable( 'SelectedSectionId' ) );
    if ( !$selectedSection instanceof eZSection )
        return;

    $selectedSection->applyTo( $object );
                    eZContentCacheManager::clearContentCacheIfNeeded( $object->attribute( 'id' ) );
    $module->redirectToView( 'edit', array( $object->attribute( 'id' ), $editVersion, $editLanguage, $fromLanguage ) );
}
}

if ( !function_exists( 'sectionEditPreTemplate' ) ) {
function sectionEditPreTemplate( $module, $class, $object, $version, $contentObjectAttributes, $editVersion, $editLanguage, $tpl )
{
}
}

if ( !function_exists( 'initializeSectionEdit' ) ) {
function initializeSectionEdit( $module )
{
    $module->addHook( 'post_fetch', 'sectionEditPostFetch' );
    $module->addHook( 'pre_commit', 'sectionEditPreCommit' );
    $module->addHook( 'action_check', 'sectionEditActionCheck' );
    $module->addHook( 'pre_template', 'sectionEditPreTemplate' );
}
}






?>
