<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();
$module = $Params['Module'];
$parameters = $Params["Parameters"];


$ini = eZINI::instance();
$tpl = eZTemplate::factory();

$template = "";

foreach ( $parameters as $param )
{
    $template .= "/$param";
}

// The siteaccesses this page may change: the related ones, which it offers.
$relatedSiteAccessList = (array)$ini->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' );

if ( $module->isCurrentAction( 'SelectCurrentSiteAccess' ) )
{
    if ( $http->hasPostVariable( 'CurrentSiteAccess' ) &&
         in_array( $http->postVariable( 'CurrentSiteAccess' ), $relatedSiteAccessList, true ) )
    {
        $http->setSessionVariable( 'eZTemplateAdminCurrentSiteAccess', $http->postVariable( 'CurrentSiteAccess' ) );
    }
}

// Fetch siteaccess settings for the selected override
// Default to first defined siteacces if none are selected
if ( !$http->hasSessionVariable( 'eZTemplateAdminCurrentSiteAccess' ) ||
     !in_array( $http->sessionVariable( 'eZTemplateAdminCurrentSiteAccess' ), $relatedSiteAccessList, true ) )
{
    $http->setSessionVariable( 'eZTemplateAdminCurrentSiteAccess', $relatedSiteAccessList[0] );
}

$siteAccess = $http->sessionVariable( 'eZTemplateAdminCurrentSiteAccess' );

$overrideArray = eZTemplateDesignResource::overrideArray( $siteAccess );

/**
 * The names of the overrides of $template, in the order they are tried.
 */
$overrideNames = function ( $overrideArray ) use ( $template )
{
    $names = array();
    if ( isset( $overrideArray[$template]['custom_match'] ) )
    {
        foreach ( isset( $overrideArray[$template]['custom_match'] ) ? $overrideArray[$template]['custom_match'] : array() as $customMatch )
            $names[] = $customMatch['override_name'];
    }
    return $names;
};

// Reordering: posted by the page each time an override is dropped in a new
// place (or moved with its arrows), answered in JSON. The order becomes the
// Priority of this template's overrides, 10, 20, 30 ..., in the siteaccess's
// own override.ini.append.php; nothing else in it changes. A list that does
// not hold exactly the overrides shown now is refused.
if ( $http->hasPostVariable( 'ReorderOverrides' ) )
{
    // The list is paged: the page posts the new order of the overrides from
    // position OverrideStart on (its own, or its own and one neighbour's for a
    // move across the edge of a page), and that stretch of the full order is
    // put in its place.
    $order = $http->hasPostVariable( 'OverrideOrder' ) ? (array)$http->postVariable( 'OverrideOrder' ) : array();
    $start = $http->hasPostVariable( 'OverrideStart' ) ? max( 0, (int)$http->postVariable( 'OverrideStart' ) ) : 0;
    $current = $overrideNames( $overrideArray );
    $stretch = ezpTemplateOverrides::reorder( array_slice( $current, $start, count( $order ) ), $order );
    $list = $stretch === false ? false : array_merge( array_slice( $current, 0, $start ), $stretch, array_slice( $current, $start + count( $stretch ) ) );
    $response = array( 'ok' => false, 'order' => $current );
    if ( $list === false )
    {
        $response['error'] = ezpI18n::tr( 'design/admin/visual/templateview', 'The overrides of this template changed since this page was loaded. Reload the page and try again.' );
    }
    else if ( $list === $current )
    {
        $response = array( 'ok' => true, 'order' => $current, 'message' => '' );
    }
    else
    {
        $overrides = new ezpTemplateOverrides( $siteAccess );
        if ( $overrides->update( ezpTemplateOverrides::priorities( $list ) ) )
        {
            ezpTemplateOverrides::expireCaches();
            $now = $overrideNames( eZTemplateDesignResource::overrideArray( $siteAccess ) );
            if ( $now === $list )
            {
                $response = array( 'ok' => true, 'order' => $now,
                                   'message' => ezpI18n::tr( 'design/admin/visual/templateview', 'Order saved; a copy of the previous settings is in %file.',
                                                             null, array( '%file' => $overrides->backup !== '' ? $overrides->backup : '-' ) ) );
            }
            else
            {
                // Written, but another settings file decides the order (an
                // extension's siteaccess settings loaded after this one).
                $response = array( 'ok' => false, 'order' => $now,
                                   'error' => ezpI18n::tr( 'design/admin/visual/templateview', 'The order was written to %file, but other settings still decide it. Check the Priority of these overrides in the extensions\' override.ini files.',
                                                           null, array( '%file' => 'settings/siteaccess/' . $siteAccess . '/override.ini.append.php' ) ) );
            }
        }
        else
        {
            $response['error'] = $overrides->error;
        }
    }
    header( 'Content-Type: application/json; charset=utf-8' );
    header( 'Cache-Control: no-store' );
    echo json_encode( $response );
    eZExecution::cleanExit();
}

if ( $module->isCurrentAction( 'NewOverride' ) )
{
    if ( $http->hasPostVariable( 'CurrentSiteAccess' ) &&
         in_array( $http->postVariable( 'CurrentSiteAccess' ), $relatedSiteAccessList, true ) )
    {
        $http->setSessionVariable( 'eZTemplateAdminCurrentSiteAccess', $http->postVariable( 'CurrentSiteAccess' ) );
    }

    if ( isset( $overrideArray[$template] ) && !empty( $overrideArray[$template]['base_dir'] ) )
    {
        $module->redirectTo( '/visual/templatecreate'. $template );
    }
    else
    {
        $module->redirectTo( '/visual/templatelist' );
    }
    return eZModule::HOOK_STATUS_CANCEL_RUN;
}

$saveError = '';
$saveMessage = '';

// The conditions edited on the page. Only the overrides the page showed, and
// of those only the ones whose conditions changed, are written, to the
// siteaccess's own file.
if ( $module->isCurrentAction( 'UpdateOverride' ) )
{
    $shown = array_intersect( $http->hasPostVariable( 'ShownOverrideList' ) ? (array)$http->postVariable( 'ShownOverrideList' ) : array(),
                              $overrideNames( $overrideArray ) );
    $matchArrayPost = $http->hasPostVariable( 'MatchArray' ) ? $http->postVariable( 'MatchArray' ) : array();
    $newMatchArray = $http->hasPostVariable( 'NewMatch' ) ? $http->postVariable( 'NewMatch' ) : array();
    $removeMatchArray = $http->hasPostVariable( 'RemoveMatchArray' ) ? $http->postVariable( 'RemoveMatchArray' ) : array();

    $existing = array();
    foreach ( isset( $overrideArray[$template]['custom_match'] ) ? $overrideArray[$template]['custom_match'] : array() as $customMatch )
        $existing[$customMatch['override_name']] = is_array( $customMatch['conditions'] ) ? $customMatch['conditions'] : array();

    $changedMatches = array();
    foreach ( $shown as $overrideName )
    {
        $matchArray = isset( $matchArrayPost[$overrideName] ) ? (array)$matchArrayPost[$overrideName] : array();

        // Remove any condition keys that the user marked for removal.
        if ( isset( $removeMatchArray[$overrideName] ) )
        {
            foreach ( array_keys( (array)$removeMatchArray[$overrideName] ) as $removeMatchKey )
                unset( $matchArray[$removeMatchKey] );
        }

        if ( isset( $newMatchArray[$overrideName] ) )
        {
            $newKey = isset( $newMatchArray[$overrideName]['key'] ) ? trim( $newMatchArray[$overrideName]['key'] ) : '';
            $newValue = isset( $newMatchArray[$overrideName]['value'] ) ? $newMatchArray[$overrideName]['value'] : '';
            if ( $newKey != '' && preg_match( '/^[a-z_]+$/', $newKey ) && trim( $newValue ) != '' )
                $matchArray[$newKey] = $newValue;
        }

        foreach ( array_keys( $matchArray ) as $matchKey )
        {
            if ( $matchArray[$matchKey] == -1 or trim( $matchArray[$matchKey] ) == "" )
                unset( $matchArray[$matchKey] );
        }

        $before = $existing[$overrideName];
        ksort( $before );
        $after = $matchArray;
        ksort( $after );
        if ( array_map( 'strval', $before ) !== array_map( 'strval', $after ) )
            $changedMatches[$overrideName] = $matchArray;
    }

    if ( $changedMatches )
    {
        $overrides = new ezpTemplateOverrides( $siteAccess );
        if ( $overrides->update( array(), $changedMatches ) )
        {
            ezpTemplateOverrides::expireCaches();
            $saveMessage = ezpI18n::tr( 'design/admin/visual/templateview', 'The conditions of %count overrides were saved.', null, array( '%count' => count( $changedMatches ) ) );
        }
        else
        {
            $saveError = $overrides->error;
        }
        $overrideArray = eZTemplateDesignResource::overrideArray( $siteAccess );
    }
    else
    {
        $saveMessage = ezpI18n::tr( 'design/admin/visual/templateview', 'No condition was changed.' );
    }
}

$overrideINISaveFailed = false;
$notRemoved = array();
$notOwned = array();

// Removing: the overrides defined in the siteaccess's own file, and their
// template files when those are in the installation's design directory. An
// override an extension defines is left to the extension, and so is its file.
if ( $module->isCurrentAction( 'RemoveOverride' ) )
{
    if ( $http->hasPostVariable( 'RemoveOverrideArray' ) )
    {
        $removeOverrideArray = array_intersect( (array)$http->postVariable( 'RemoveOverrideArray' ), $overrideNames( $overrideArray ) );

        $overrides = new ezpTemplateOverrides( $siteAccess );
        $own = $overrides->ownGroups();
        $files = array();
        foreach ( isset( $overrideArray[$template]['custom_match'] ) ? $overrideArray[$template]['custom_match'] : array() as $customMatch )
        {
            if ( in_array( $customMatch['override_name'], $removeOverrideArray, true ) &&
                 in_array( $customMatch['override_name'], $own, true ) &&
                 !empty( $customMatch['match_file'] ) )
            {
                $files[] = $customMatch['match_file'];
            }
        }

        if ( $overrides->remove( $removeOverrideArray, $notOwned ) )
        {
            foreach ( $files as $fileName )
            {
                $real = realpath( $fileName );
                $designRoot = realpath( 'design' );
                if ( $real !== false && $designRoot !== false && strpos( $real, $designRoot . '/' ) === 0 )
                {
                    if ( !unlink( $real ) )
                        $notRemoved[] = array( 'filename' => $fileName );
                }
            }
            ezpTemplateOverrides::expireCaches();
        }
        else
        {
            $overrideINISaveFailed = true;
            $saveError = $overrides->error;
        }

        // Refresh the override array for the template view.
        $overrideArray = eZTemplateDesignResource::overrideArray( $siteAccess );
    }
}

$templateSettings = false;
if ( isset( $overrideArray[$template] ) )
{
    $templateSettings = $overrideArray[$template];
}

if ( !isset( $templateSettings['custom_match'] ) )
    $templateSettings['custom_match'] = array();

if ( !isset( $templateSettings['template'] ) )
    $templateSettings['template'] = $template;

if ( !isset( $templateSettings['base_dir'] ) )
    $templateSettings['base_dir'] = '';

$newOverrideAllowed = ( $templateSettings['base_dir'] !== '' );

// Paged: a template can have dozens of overrides. The page knows the override
// just before and just after it, so its first and last can be moved across.
$overrideCount = count( $templateSettings['custom_match'] );
$pageLimit = expAdminPagination::limit( 'visual/templateview', 20 );
$pageOffset = expAdminPagination::offset( $Params );
if ( $pageOffset >= $overrideCount )
    $pageOffset = $overrideCount > 0 ? (int)( floor( ( $overrideCount - 1 ) / $pageLimit ) * $pageLimit ) : 0;
$allNames = array();
foreach ( $templateSettings['custom_match'] as $customMatch )
    $allNames[] = $customMatch['override_name'];
$templateSettings['custom_match'] = array_values( expAdminPagination::page( $templateSettings['custom_match'], $pageOffset, $pageLimit ) );
$tpl->setVariable( 'override_count', $overrideCount );
$tpl->setVariable( 'page_offset', $pageOffset );
$tpl->setVariable( 'page_limit', $pageLimit );
$tpl->setVariable( 'view_parameters', array( 'offset' => $pageOffset ) );
$tpl->setVariable( 'previous_override', $pageOffset > 0 ? $allNames[$pageOffset - 1] : '' );
$tpl->setVariable( 'next_override', isset( $allNames[$pageOffset + $pageLimit] ) ? $allNames[$pageOffset + $pageLimit] : '' );

// Which overrides the siteaccess's own file defines (they can be removed here)
// and which come from elsewhere (an extension), for the page to say so.
$ownOverrides = ( new ezpTemplateOverrides( $siteAccess ) )->ownGroups();

$tpl->setVariable( 'template_settings', $templateSettings );
$tpl->setVariable( 'current_siteaccess', $siteAccess );
$tpl->setVariable( 'not_removed', $notRemoved );
$tpl->setVariable( 'not_owned', $notOwned );
$tpl->setVariable( 'ini_not_saved', $overrideINISaveFailed );
$tpl->setVariable( 'save_error', $saveError );
$tpl->setVariable( 'save_message', $saveMessage );
$tpl->setVariable( 'own_overrides', $ownOverrides );
$tpl->setVariable( 'new_override_allowed', $newOverrideAllowed );

$siteINI = eZINI::instance( 'site.ini' );
if ( $siteINI->variable( 'BackwardCompatibilitySettings', 'UsingDesignAdmin34' ) == 'enabled' )
{
    $tpl->setVariable( 'custom_match', $templateSettings['custom_match'] );
}

$Result = array();
$Result['content'] = $tpl->fetch( "design:visual/templateview.tpl" );
$Result['path'] = array( array( 'url' => "/visual/templatelist/",
                                'text' => ezpI18n::tr( 'kernel/design', 'Template list' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/design', 'Template view' ) ) );
?>
