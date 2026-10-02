<?php
/**
 * The code of kernel/setup/extensions.php, moved into a class (#207 stage 1). The file kernel/setup/extensions.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/extensions.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
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
}

namespace Exponential\View\Kernel\Setup
{

class Extensions extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];

        // Direct extension download via view parameters: /setup/extensions/<name>/<format>
        $downloadName   = isset( $Params['ExtensionName'] ) ? $Params['ExtensionName'] : false;
        $downloadFormat = isset( $Params['ExtensionFormat'] ) ? strtolower( $Params['ExtensionFormat'] ) : false;
        $validFormats   = array( 'tar.gz', 'tar.bz2', 'zip', 'ezpkg' );

        if ( $downloadName && $downloadFormat &&
             in_array( $downloadFormat, $validFormats ) &&
             \eZExtension::extensionPath( $downloadName ) !== false )
        {
            $temporaryExportPath = \eZPackage::temporaryExportPath();
            $archiveFile         = $temporaryExportPath . '/' . $downloadName . '.' . $downloadFormat;

            $package = \eZPackage::create( $downloadName . '_' . time() );
            $package->setAttribute( 'is_active', true );
            \eZPackage::packageHandler( 'ezextension' )->addExtension( $package, $downloadName );
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
                \eZExecution::cleanExit();
            }
        }

        // Reordering the active extensions: posted by the loading order list on the
        // page each time an extension is dropped in a new place, and answered in JSON.
        // Only the order of ActiveExtensions changes; a list that does not hold exactly
        // the extensions active now (the page was loaded before they changed) is
        // refused, so a reorder never switches one on or off.
        if ( $http->hasPostVariable( 'ReorderExtensions' ) )
        {
            $order = $http->hasPostVariable( 'ExtensionOrder' ) ? (array)$http->postVariable( 'ExtensionOrder' ) : array();
            $current = \ezpActiveExtensions::current();
            $list = \ezpActiveExtensions::reorder( $current, $order );
            $response = array( 'ok' => false );
            if ( $list === false )
            {
                $response['error'] = \ezpI18n::tr( 'design/admin/setup/extensions', 'The active extensions changed since this page was loaded. Reload the page and try again.' );
                $response['order'] = $current;
            }
            else if ( $list === $current )
            {
                $response = array( 'ok' => true, 'order' => $current, 'message' => '' );
            }
            else
            {
                $activeExtensions = new \ezpActiveExtensions();
                if ( $activeExtensions->write( $list ) )
                {
                    // The order decides which extension's settings, templates and
                    // design files win, so these caches are built on it.
                    \eZCache::clearByTag( 'ini' );
                    \eZCache::clearByID( array( 'template-override', 'design_base', 'active_extensions' ) );
                    $response = array(
                        'ok' => true,
                        'order' => \ezpActiveExtensions::current(),
                        'message' => \ezpI18n::tr( 'design/admin/setup/extensions', 'Loading order saved; a copy of the previous settings is in %file.',
                                                  null, array( '%file' => $activeExtensions->backup ) ) );
                }
                else
                {
                    $response['error'] = $activeExtensions->error;
                    $response['order'] = \ezpActiveExtensions::current();
                }
            }
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Cache-Control: no-store' );
            echo json_encode( $response );
            \eZExecution::cleanExit();
        }

        $tpl = \eZTemplate::factory();

        // Sorting. The column travels on the address as a view parameter rather than
        // in a query string, so that the pager carries it: the pager appends the offset
        // to the page address, and a query string on the end of that address would be
        // left behind - paging would silently reset the order. The old SortBy and
        // SortOrder are still read, so a bookmarked link keeps working.
        $extensionSortColumns = array( 'order', 'name', 'info_name', 'license', 'version', 'mtime' );

        $userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();

        // Sorted by the loading order by default: the order the system loads the
        // extensions in, active ones first, then the others by name.
        $sortBy = isset( $userParameters['sort'] ) ? (string)$userParameters['sort']
                : ( $http->hasGetVariable( 'SortBy' ) ? strtolower( $http->getVariable( 'SortBy' ) ) : 'order' );

        $sortOrder = isset( $userParameters['dir'] ) ? (string)$userParameters['dir']
                   : ( $http->hasGetVariable( 'SortOrder' ) ? strtolower( $http->getVariable( 'SortOrder' ) ) : 'asc' );

        $sortBy    = in_array( $sortBy, $extensionSortColumns, true ) ? $sortBy : 'order';
        $sortOrder = $sortOrder === 'desc' ? 'desc' : 'asc';

        // Use expInfo to collect and normalise all extension metadata
        $extensionInfo = \expInfo::availableExtensions();

        // The loading order: the position in ActiveExtensions, as the file on disk has
        // it. Extensions active only for a siteaccess come after them, the inactive
        // ones last.
        $extensionPositions = \ezpActiveExtensions::positions( \ezpActiveExtensions::current() );

        uasort( $extensionInfo, function( $a, $b ) use ( $sortBy, $extensionPositions ) {
            $nameA = (string) $a['extension_name'];
            $nameB = (string) $b['extension_name'];

            if ( $sortBy === 'order' )
            {
                $aVal = isset( $extensionPositions[$nameA] ) ? $extensionPositions[$nameA] : PHP_INT_MAX;
                $bVal = isset( $extensionPositions[$nameB] ) ? $extensionPositions[$nameB] : PHP_INT_MAX;
                if ( $aVal !== $bVal )
                    return $aVal < $bVal ? -1 : 1;
                return strnatcasecmp( $nameA, $nameB );
            }
            else if ( $sortBy === 'mtime' )
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
        $siteINI = \eZINI::instance();
        $siteINI->load();
        $selectedExtensionArray       = $siteINI->variable( 'ExtensionSettings', "ActiveExtensions" );
        $selectedAccessExtensionArray = $siteINI->variable( 'ExtensionSettings', "ActiveAccessExtensions" );
        $selectedExtensions           = array_merge( $selectedExtensionArray, $selectedAccessExtensionArray );
        $selectedExtensions           = array_unique( $selectedExtensions );

        // When the user clicks on "Apply changes" button in admin interface in the Extensions section
        if ( $module->isCurrentAction( 'ActivateExtensions' ) )
        {
            $ini = \eZINI::instance( 'module.ini' );
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

            // Only the extensions this page showed can be switched off: the list is
            // paged, and one on another page keeps its state. A form without the list
            // of shown extensions (an older template) is taken to have shown all.
            $shownExtensionArray = $http->hasPostVariable( 'ShownExtensionList' )
                ? (array)$http->postVariable( 'ShownExtensionList' )
                : $availableExtensionArray;

            // The list is read from settings/override/site.ini.append.php itself, and
            // only its ActiveExtensions is written: ezpActiveExtensions reads the file
            // from disk (not a cached or kept INI instance), keeps a copy, and checks
            // the file afterwards, putting the copy back if anything else changed.
            $activeExtensions = new \ezpActiveExtensions();
            $toSave = \ezpActiveExtensions::merge(
                \ezpActiveExtensions::current(),
                $selectedExtensionArray,
                $shownExtensionArray,
                $availableExtensionArray,
                $selectedAccessExtensionArray
            );

            if ( $activeExtensions->write( $toSave ) )
            {
                \eZCache::clearByTag( 'ini' );
                \eZSiteAccess::reInitialise();

                $ini = \eZINI::instance( 'module.ini' );
                $currentModules = $ini->variable( 'ModuleSettings', 'ModuleList' );
                if ( $currentModules != $oldModules )
                {
                    // ensure that evaluated policy wildcards in the user info cache
                    // will be up to date with the currently activated modules
                    \eZCache::clearByID( 'user_info_cache' );
                }

                updateAutoload( $tpl );
                $tpl->setVariable( 'save_message', \ezpI18n::tr( 'design/admin/setup/extensions', 'The active extensions were saved; a copy of the previous settings is in %file.',
                                                                null, array( '%file' => $activeExtensions->backup ) ) );
            }
            else
            {
                $tpl->setVariable( 'save_error', $activeExtensions->error );
            }
        }

        // open site.ini for reading (need to do it again to take into account the changes made to site.ini after clicking "Apply changes" button above
        $siteINI = \eZINI::instance();
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
        $pageLimit  = \expAdminPagination::limit( 'setup/extensions' );
        $pageOffset = \expAdminPagination::offset( $Params );

        $tpl->setVariable( "available_extension_array",
                           \expAdminPagination::page( $availableExtensionArray, $pageOffset, $pageLimit ) );
        $tpl->setVariable( "extension_count", $pageCount );
        $tpl->setVariable( "limit", $pageLimit );
        $tpl->setVariable( "view_parameters", array( 'offset' => $pageOffset,
                                                     'sort'   => $sortBy,
                                                     'dir'    => $sortOrder ) );
        $tpl->setVariable( "extension_sort", array( 'field'     => $sortBy,
                                                    'direction' => $sortOrder,
                                                    'opposite'  => $sortOrder === 'asc' ? 'desc' : 'asc' ) );
        $tpl->setVariable( "selected_extension_array", $selectedExtensions );
        $tpl->setVariable( "access_extension_array", array_values( array_diff( $selectedAccessExtensionArray, $selectedExtensionArray ) ) );
        // Read again: an Update above may have changed the list.
        $activeOrder = \ezpActiveExtensions::current();
        $tpl->setVariable( "active_extension_order", $activeOrder );
        $tpl->setVariable( "extension_positions", \ezpActiveExtensions::positions( $activeOrder ) );
        $tpl->setVariable( "extension_info", $extensionInfo );
        $tpl->setVariable( "sort_by", $sortBy );
        $tpl->setVariable( "sort_order", $sortOrder );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:setup/extensions.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/setup', 'Extension configuration' ) ) );


        /* expInfo::availableExtensions() now provides normalised extension metadata */

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
