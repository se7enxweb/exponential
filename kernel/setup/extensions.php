<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */


if ( !function_exists( 'updateAutoload' ) ) {
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
}

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
        $package->removeFiles( $package->path() );
        eZExecution::cleanExit();
    }
}

$tpl = eZTemplate::factory();

// Sorting. The column travels on the address as a view parameter rather than
// in a query string, so that the pager carries it: the pager appends the offset
// to the page address, and a query string on the end of that address would be
// left behind - paging would silently reset the order. The old SortBy and
// SortOrder are still read, so a bookmarked link keeps working.
$extensionSortColumns = array( 'name', 'info_name', 'license', 'version', 'mtime' );

$userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();

$sortBy = isset( $userParameters['sort'] ) ? (string)$userParameters['sort']
        : ( $http->hasGetVariable( 'SortBy' ) ? strtolower( $http->getVariable( 'SortBy' ) ) : 'name' );

$sortOrder = isset( $userParameters['dir'] ) ? (string)$userParameters['dir']
           : ( $http->hasGetVariable( 'SortOrder' ) ? strtolower( $http->getVariable( 'SortOrder' ) ) : 'asc' );

$sortBy    = in_array( $sortBy, $extensionSortColumns, true ) ? $sortBy : 'name';
$sortOrder = $sortOrder === 'desc' ? 'desc' : 'asc';

// Use expInfo to collect and normalise all extension metadata
$extensionInfo = expInfo::availableExtensions();

uasort( $extensionInfo, function( $a, $b ) use ( $sortBy ) {
    $nameA = (string) $a['extension_name'];
    $nameB = (string) $b['extension_name'];

    if ( $sortBy === 'mtime' )
    {
        $aVal = (int) $a['mtime'];
        $bVal = (int) $b['mtime'];
        if ( $aVal !== $bVal )
            return $aVal < $bVal ? -1 : 1;
        return strnatcasecmp( $nameA, $nameB );
    }
    else if ( $sortBy === 'version' )
    {
        $aVal = isset( $a['version'] ) && is_string( $a['version'] ) ? $a['version'] : '0';
        $bVal = isset( $b['version'] ) && is_string( $b['version'] ) ? $b['version'] : '0';
        $cmp = version_compare( $aVal, $bVal );
        if ( $cmp !== 0 )
            return $cmp;
        return strnatcasecmp( $nameA, $nameB );
    }
    else if ( $sortBy === 'info_name' )
    {
        // The name the extension gives itself, which carries spaces and
        // capitals, so it is compared the way a reader would read it. An
        // extension that declares no name falls back to its directory, which
        // is what the column shows in its place.
        $aVal = isset( $a['name'] ) && trim( (string)$a['name'] ) !== '' ? (string)$a['name'] : $nameA;
        $bVal = isset( $b['name'] ) && trim( (string)$b['name'] ) !== '' ? (string)$b['name'] : $nameB;

        $cmp = strnatcasecmp( trim( $aVal ), trim( $bVal ) );
        return $cmp !== 0 ? $cmp : strnatcasecmp( $nameA, $nameB );
    }
    else if ( $sortBy === 'license' )
    {
        // Extensions with no license declared sort together at the end rather
        // than at the front, where an empty string would put them.
        $aVal = isset( $a['license'] ) ? trim( (string)$a['license'] ) : '';
        $bVal = isset( $b['license'] ) ? trim( (string)$b['license'] ) : '';

        if ( ( $aVal === '' ) !== ( $bVal === '' ) )
            return $aVal === '' ? 1 : -1;

        $cmp = strnatcasecmp( $aVal, $bVal );
        return $cmp !== 0 ? $cmp : strnatcasecmp( $nameA, $nameB );
    }
    else
    {
        // Sort by the extension (directory) name, which matches the visible "Name" column
        return strnatcasecmp( $nameA, $nameB );
    }
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

// Paged. Every extension the installation can see was drawn on one screen,
// and an installation with a hundred of them is not unusual.
$pageCount  = count( $availableExtensionArray );
$pageLimit  = expAdminPagination::limit( 'setup/extensions' );
$pageOffset = expAdminPagination::offset( $Params );

$tpl->setVariable( "available_extension_array",
                   expAdminPagination::page( $availableExtensionArray, $pageOffset, $pageLimit ) );
$tpl->setVariable( "extension_count", $pageCount );
$tpl->setVariable( "limit", $pageLimit );
$tpl->setVariable( "view_parameters", array( 'offset' => $pageOffset,
                                             'sort'   => $sortBy,
                                             'dir'    => $sortOrder ) );
$tpl->setVariable( "extension_sort", array( 'field'     => $sortBy,
                                            'direction' => $sortOrder,
                                            'opposite'  => $sortOrder === 'asc' ? 'desc' : 'asc' ) );
$tpl->setVariable( "selected_extension_array", $selectedExtensions );
$tpl->setVariable( "extension_info", $extensionInfo );
$tpl->setVariable( "sort_by", $sortBy );
$tpl->setVariable( "sort_order", $sortOrder );

$Result = array();
$Result['content'] = $tpl->fetch( "design:setup/extensions.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Extension configuration' ) ) );


/* expInfo::availableExtensions() now provides normalised extension metadata */

?>

