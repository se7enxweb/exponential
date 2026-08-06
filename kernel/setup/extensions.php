<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$http = eZHTTPTool::instance();
$module = $Params['Module'];

// Direct extension download via view parameters: /setup/extensions/<name>/<format>
$downloadName   = isset( $Params['ExtensionName'] ) ? $Params['ExtensionName'] : false;
$downloadFormat = isset( $Params['ExtensionFormat'] ) ? strtolower( $Params['ExtensionFormat'] ) : false;
$validFormats   = array( 'tar.gz', 'tar.bz2', 'zip', 'ezpkg' );

if ( $downloadName && $downloadFormat &&
     in_array( $downloadFormat, $validFormats ) &&
     eZExtension::extensionPath( $downloadName ) !== false )
{
    $temporaryExportPath = eZPackage::temporaryExportPath();
    $archiveFile         = $temporaryExportPath . '/' . $downloadName . '.' . $downloadFormat;

    $package = eZPackage::create( $downloadName . '_' . time() );
    $package->setAttribute( 'is_active', true );
    eZPackage::packageHandler( 'ezextension' )->addExtension( $package, $downloadName );
    $package->exportToArchive( $archiveFile, $downloadFormat );

    $mimeTypes = array(
        'tar.gz'  => 'application/x-gzip',
        'tar.bz2' => 'application/x-bzip2',
        'zip'     => 'application/zip',
        'ezpkg'   => 'application/octet-stream',
    );
    $contentType = isset( $mimeTypes[$downloadFormat] ) ? $mimeTypes[$downloadFormat] : 'application/octet-stream';
    $downloadFileName = preg_replace( '/[^a-zA-Z0-9_-]/', '_', $downloadName ) . '.' . $downloadFormat;

    if ( file_exists( $archiveFile ) )
    {
        header( 'Content-Type: ' . $contentType );
        header( 'Content-Disposition: attachment; filename="' . $downloadFileName . '"' );
        header( 'Content-Length: ' . filesize( $archiveFile ) );
        readfile( $archiveFile );
        unlink( $archiveFile );
        eZExecution::cleanExit();
    }
}

$tpl = eZTemplate::factory();

// Sorting: default A-Z by name, overridable via GET for click-to-sort headers
$sortBy    = 'name';
$sortOrder = 'asc';
if ( $http->hasGetVariable( 'SortBy' ) )
{
    $sortBy = strtolower( $http->getVariable( 'SortBy' ) );
}
if ( $http->hasGetVariable( 'SortOrder' ) )
{
    $sortOrder = strtolower( $http->getVariable( 'SortOrder' ) );
}
$sortBy    = in_array( $sortBy, array( 'name', 'version', 'mtime' ) ) ? $sortBy : 'name';
$sortOrder = in_array( $sortOrder, array( 'asc', 'desc' ) ) ? $sortOrder : 'asc';

$availableExtensionArray = array();
foreach ( eZExtension::extensionRootDirectories() as $extensionDir )
{
    if ( !is_dir( $extensionDir ) )
        continue;

    foreach ( eZDir::findSubItems( $extensionDir, 'dl' ) as $extensionName )
    {
        // Later roots override earlier roots
        $availableExtensionArray[$extensionName] = $extensionName;
    }
}

// Collect metadata for sorting and display
$extensionInfo = array();
foreach ( $availableExtensionArray as $extensionName )
{
    $extensionInfo[$extensionName] = eZExtension::extensionInfo( $extensionName );
}

uasort( $extensionInfo, function( $a, $b ) use ( $sortBy ) {
    if ( $sortBy === 'mtime' )
    {
        $aVal = (int) $a['mtime'];
        $bVal = (int) $b['mtime'];
        if ( $aVal === $bVal ) return 0;
        return $aVal < $bVal ? -1 : 1;
    }
    else if ( $sortBy === 'version' )
    {
        $aVal = isset( $a['version'] ) && is_string( $a['version'] ) ? $a['version'] : '0';
        $bVal = isset( $b['version'] ) && is_string( $b['version'] ) ? $b['version'] : '0';
        return version_compare( $aVal, $bVal );
    }
    else
    {
        $aVal = isset( $a['name'] ) ? (string) $a['name'] : '';
        $bVal = isset( $b['name'] ) ? (string) $b['name'] : '';
    }
    if ( $aVal === $bVal ) return 0;
    return strnatcasecmp( $aVal, $bVal );
} );

if ( $sortOrder === 'desc' )
{
    $extensionInfo = array_reverse( $extensionInfo, true );
}

$availableExtensionArray = array_keys( $extensionInfo );

// open site.ini for reading
$siteINI = eZINI::instance();
$siteINI->load();
$selectedExtensionArray       = $siteINI->variable( 'ExtensionSettings', "ActiveExtensions" );
$selectedAccessExtensionArray = $siteINI->variable( 'ExtensionSettings', "ActiveAccessExtensions" );
$selectedExtensions           = array_merge( $selectedExtensionArray, $selectedAccessExtensionArray );
$selectedExtensions           = array_unique( $selectedExtensions );

// When the user clicks on "Apply changes" button in admin interface in the Extensions section
if ( $module->isCurrentAction( 'ActivateExtensions' ) )
{
    $ini = eZINI::instance( 'module.ini' );
    $oldModules = $ini->variable( 'ModuleSettings', 'ModuleList' );

    if ( $http->hasPostVariable( "ActiveExtensionList" ) )
    {
        $selectedExtensionArray = $http->postVariable( "ActiveExtensionList" );
        if ( !is_array( $selectedExtensionArray ) )
            $selectedExtensionArray = array( $selectedExtensionArray );
    }
    else
    {
        $selectedExtensionArray = array();
    }

    // The file settings/override/site.ini.append.php is updated like this:
    // - take the existing list of extensions from site.ini.append.php (to preserve their order)
    // - remove from the list the extensions that the user unchecked in the admin interface
    // - add to the list the extensions checked by the user in the admin interface, but to the end of the list
    $intersection = array_intersect( $selectedExtensions, $selectedExtensionArray );
    $difference = array_diff( $selectedExtensionArray, $selectedExtensions );
    $toSave = array_merge( $intersection, $difference );
    $toSave = array_unique( $toSave );

    // open settings/override/site.ini.append[.php] for writing
    $writeSiteINI = eZINI::instance( 'site.ini.append', 'settings/override', null, null, false, true );
    $writeSiteINI->setVariable( "ExtensionSettings", "ActiveExtensions", $toSave );
    $writeSiteINI->save( 'site.ini.append', '.php', false, false );
    eZCache::clearByTag( 'ini' );

    eZSiteAccess::reInitialise();

    $ini = eZINI::instance( 'module.ini' );
    $currentModules = $ini->variable( 'ModuleSettings', 'ModuleList' );
    if ( $currentModules != $oldModules )
    {
        // ensure that evaluated policy wildcards in the user info cache
        // will be up to date with the currently activated modules
        eZCache::clearByID( 'user_info_cache' );
    }

    updateAutoload( $tpl );
}

// open site.ini for reading (need to do it again to take into account the changes made to site.ini after clicking "Apply changes" button above
$siteINI = eZINI::instance();
$siteINI->load();
$selectedExtensionArray       = $siteINI->variable( 'ExtensionSettings', "ActiveExtensions" );
$selectedAccessExtensionArray = $siteINI->variable( 'ExtensionSettings', "ActiveAccessExtensions" );
$selectedExtensions           = array_merge( $selectedExtensionArray, $selectedAccessExtensionArray );
$selectedExtensions           = array_unique( $selectedExtensions );

if ( $module->isCurrentAction( 'GenerateAutoloadArrays' ) )
{
    updateAutoload( $tpl );
}

$tpl->setVariable( "available_extension_array", $availableExtensionArray );
$tpl->setVariable( "selected_extension_array", $selectedExtensions );
$tpl->setVariable( "extension_info", $extensionInfo );
$tpl->setVariable( "sort_by", $sortBy );
$tpl->setVariable( "sort_order", $sortOrder );

$Result = array();
$Result['content'] = $tpl->fetch( "design:setup/extensions.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Extension configuration' ) ) );

function updateAutoload( $tpl = null )
{
    $autoloadGenerator = new eZAutoloadGenerator();
    try
    {
        $autoloadGenerator->buildAutoloadArrays();

        $messages = $autoloadGenerator->getMessages();
        foreach( $messages as $message )
        {
            eZDebug::writeNotice( $message, 'eZAutoloadGenerator' );
        }

        $warnings = $autoloadGenerator->getWarnings();
        foreach ( $warnings as &$warning )
        {
            eZDebug::writeWarning( $warning, "eZAutoloadGenerator" );

            // For web output we want to mark some of the important parts of
            // the message
            $pattern = '@^Class\s+(\w+)\s+.* file\s(.+\.php).*\n(.+\.php)\s@';
            preg_match( $pattern, $warning, $m );

            if ( isset( $m[1], $m[2], $m[3] ) )
            {
                $warning = str_replace( $m[1], '<strong>'.$m[1].'</strong>', $warning );
                $warning = str_replace( $m[2], '<em>'.$m[2].'</em>', $warning );
                $warning = str_replace( $m[3], '<em>'.$m[3].'</em>', $warning );
            }
        }

        if ( $tpl !== null )
        {
            $tpl->setVariable( 'warning_messages', $warnings );
        }
    }
    catch ( Exception $e )
    {
        eZDebug::writeError( $e->getMessage() );
    }
}

?>
