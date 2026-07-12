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

if ( $module->isCurrentAction( 'SelectCurrentSiteAccess' ) )
{
    if ( $http->hasPostVariable( 'CurrentSiteAccess' ) )
    {
        $http->setSessionVariable( 'eZTemplateAdminCurrentSiteAccess', $http->postVariable( 'CurrentSiteAccess' ) );
    }
}

// Fetch siteaccess settings for the selected override
// Default to first defined siteacces if none are selected
if ( !$http->hasSessionVariable( 'eZTemplateAdminCurrentSiteAccess' ) )
{
    $siteAccessList = $ini->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' );
    $http->setSessionVariable( 'eZTemplateAdminCurrentSiteAccess', $siteAccessList[0] );
}

$siteAccess = $http->sessionVariable( 'eZTemplateAdminCurrentSiteAccess' );

$overrideArray = eZTemplateDesignResource::overrideArray( $siteAccess );

if ( $module->isCurrentAction( 'NewOverride' ) )
{
    if ( $http->hasPostVariable( 'CurrentSiteAccess' ) )
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

if ( $module->isCurrentAction( 'UpdateOverride' ) )
{
    if ( $http->hasPostVariable( 'PriorityArray' ) )
    {
        $priorityArray = $http->postVariable( 'PriorityArray' );
        $matchArrayPost = $http->hasPostVariable( 'MatchArray' ) ? $http->postVariable( 'MatchArray' ) : array();
        $newMatchArray = $http->hasPostVariable( 'NewMatch' ) ? $http->postVariable( 'NewMatch' ) : array();
        $removeMatchArray = $http->hasPostVariable( 'RemoveMatchArray' ) ? $http->postVariable( 'RemoveMatchArray' ) : array();

        // Clear stale INI cache before loading override.ini so newly
        // created or reordered overrides are not lost.
        eZCache::clearByID( array( 'global_ini', 'template-override' ) );

        // Load override.ini for the current siteaccess
        $overrideINI = eZINI::instance( 'override.ini', 'settings', null, null, true );
        $overrideINI->prependOverrideDir( "siteaccess/$siteAccess", false, 'siteaccess' );
        $overrideINI->loadCache();

        // Store the user-supplied priority values and match conditions in each
        // override group. Priority controls the override order; Match controls
        // which rule applies.
        foreach ( array_keys( $overrideINI->groups() ) as $overrideName )
        {
            $priority = isset( $priorityArray[$overrideName] ) ? $priorityArray[$overrideName] : 0;
            $overrideINI->setVariable( $overrideName, 'Priority', $priority );

            // Build the Match array from existing conditions edited in the form
            // plus any new condition the user added.
            $matchArray = isset( $matchArrayPost[$overrideName] ) ? $matchArrayPost[$overrideName] : array();

            // Remove any condition keys that the user marked for removal.
            if ( isset( $removeMatchArray[$overrideName] ) )
            {
                foreach ( array_keys( $removeMatchArray[$overrideName] ) as $removeMatchKey )
                {
                    unset( $matchArray[$removeMatchKey] );
                }
            }

            if ( isset( $newMatchArray[$overrideName] ) )
            {
                $newKey = isset( $newMatchArray[$overrideName]['key'] ) ? trim( $newMatchArray[$overrideName]['key'] ) : '';
                $newValue = isset( $newMatchArray[$overrideName]['value'] ) ? $newMatchArray[$overrideName]['value'] : '';
                if ( $newKey != '' && trim( $newValue ) != '' )
                {
                    $matchArray[$newKey] = $newValue;
                }
            }

            foreach ( array_keys( $matchArray ) as $matchKey )
            {
                if ( $matchArray[$matchKey] == -1 or trim( $matchArray[$matchKey] ) == "" )
                    unset( $matchArray[$matchKey] );
            }

            if ( !empty( $matchArray ) )
            {
                $overrideINI->setVariable( $overrideName, 'Match', $matchArray );
            }
        }

        $filePermission = $ini->variable( 'FileSettings', 'StorageFilePermissions' );

        $oldumask = umask( 0 );
        $overrideINI->save( "siteaccess/$siteAccess/override.ini.append" );
        chmod( "settings/siteaccess/$siteAccess/override.ini.append", octdec( $filePermission ) );
        umask( $oldumask );

        // Clear global INI cache and template override cache so priority
        // and match changes are reflected in the override list.
        eZCache::clearByID( array( 'global_ini', 'template-override' ) );

        // Refresh the override array for the template view.
        $overrideArray = eZTemplateDesignResource::overrideArray( $siteAccess );
    }
}

$overrideINISaveFailed = false;
$notRemoved = array();

if ( $module->isCurrentAction( 'RemoveOverride' ) )
{
    if ( $http->hasPostVariable( 'RemoveOverrideArray' ) )
    {
        $removeOverrideArray = $http->postVariable( 'RemoveOverrideArray' );
        // TODO: read from correct site.ini
        $siteBase = $siteAccess;

        // Clear stale INI cache before loading so the group to be removed
        // is guaranteed to be present in the override.ini object.
        eZCache::clearByID( array( 'global_ini', 'template-override' ) );

        // Load override.ini for the current siteaccess
        $overrideINI = eZINI::instance( 'override.ini', 'settings', null, null, true );
        $overrideINI->prependOverrideDir( "siteaccess/$siteAccess", false, 'siteaccess' );
        $overrideINI->loadCache();

        $siteINI = eZINI::instance( 'site.ini', 'settings', null, null, true );
        $siteINI->prependOverrideDir( "siteaccess/$siteAccess", false, 'siteaccess' );
        $siteINI->loadCache();
        $siteBase = $siteINI->variable( 'DesignSettings', 'SiteDesign' );

        // Remove settings and file
        foreach ( $removeOverrideArray as $removeOverride )
        {
            $group = $overrideINI->group( $removeOverride );

            // Try to find the actual file path from the override array.
            $fileName = false;
            if ( isset( $overrideArray[$template]['custom_match'] ) )
            {
                foreach ( $overrideArray[$template]['custom_match'] as $customMatch )
                {
                    if ( isset( $customMatch['override_name'] ) &&
                         $customMatch['override_name'] === $removeOverride &&
                         !empty( $customMatch['match_file'] ) )
                    {
                        $fileName = $customMatch['match_file'];
                        break;
                    }
                }
            }

            // Fall back to the legacy site-design path if we cannot resolve it.
            if ( $fileName === false )
            {
                $fileName = "design/$siteBase/override/templates/" . $group['MatchFile'];
            }

            if ( $fileName !== false && file_exists( $fileName ) )
            {
                if ( !unlink( $fileName ) )
                {
                    $notRemoved[] = array( 'filename' => $fileName );
                }
            }

            $overrideINI->removeGroup( $removeOverride );
        }
        if ( $overrideINI->save( "siteaccess/$siteAccess/override.ini.append" ) == false )
        {
            $overrideINISaveFailed = true;
        }

        // Expire content view cache
        eZContentCacheManager::clearAllContentCache();

        // Clear global INI cache and template override cache so the removed
        // override.ini group is no longer shown in the list.
        eZCache::clearByID( array( 'global_ini', 'template-override' ) );

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

$tpl->setVariable( 'template_settings', $templateSettings );
$tpl->setVariable( 'current_siteaccess', $siteAccess );
$tpl->setVariable( 'not_removed', $notRemoved );
$tpl->setVariable( 'ini_not_saved', $overrideINISaveFailed );
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
