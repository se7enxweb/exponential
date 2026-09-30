<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$module = $Params['Module'];
$viewMode = $Params['ViewMode'];
$packageName = $Params['PackageName'];
$repositoryID = false;
if ( isset( $Params['RepositoryID'] ) and $Params['RepositoryID'] )
    $repositoryID = $Params['RepositoryID'];

$package = eZPackage::fetch( $packageName, false, $repositoryID );
if ( !is_object( $package ) )
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

if ( !$package->attribute( 'can_read' ) )
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );


if ( $module->isCurrentAction( 'Export' ) )
{
    return $module->run( 'export', array( $packageName ) );
}
else if ( $module->isCurrentAction( 'Install' ) )
{
    return $module->redirectToView( 'install', array( $packageName ) );
}
else if ( $module->isCurrentAction( 'Uninstall' ) )
{
    return $module->redirectToView( 'uninstall', array( $packageName ) );
}

$repositoryInformation = $package->currentRepositoryInformation();

$tpl = eZTemplate::factory();

$tpl->setVariable( 'package_name', $packageName );
$tpl->setVariable( 'repository_id', $repositoryID );

// The package contents browser (package/view/full only): every file the package's own directory
// carries, paginated and filtered, kernel-only code (eZPackageFileBrowser) with no dependency on
// any extension. $ContentsBrowser stays false for every other ViewMode (files.tpl, for instance,
// already has its own listing).
$ContentsBrowser = false;
if ( $viewMode === 'full' )
{
    // Pagination (First/Previous/Next/Last) and "View" are plain GET links, each carrying the
    // exact BrowseOffset (or BrowseView) it targets - built once below, in $ContentsBrowser's own
    // query_* fields, from this same $page result, rather than reached by a submit button this
    // script would then have to turn back into an offset. The filter form (BrowseApply) is the one
    // real POST/GET distinction here: changing type/search/limit always goes back to offset 0, an
    // old offset meaning nothing against a newly filtered list.
    $http = eZHTTPTool::instance();
    $offsetParam = $http->hasVariable( 'BrowseOffset' ) ? $http->variable( 'BrowseOffset' ) : 0;
    $limitParam = $http->hasVariable( 'BrowseLimit' ) ? $http->variable( 'BrowseLimit' ) : 50;
    $typeFilter = $http->hasVariable( 'BrowseType' ) ? (string)$http->variable( 'BrowseType' ) : '';
    $search = $http->hasVariable( 'BrowseSearch' ) ? (string)$http->variable( 'BrowseSearch' ) : '';
    if ( $http->hasVariable( 'BrowseApply' ) )
        $offsetParam = 0;

    $page = eZPackageFileBrowser::filteredPage( $package, array(
        'offset' => $offsetParam, 'limit' => $limitParam, 'type' => $typeFilter, 'search' => $search,
    ) );

    $viewedFile = null;
    $viewedContent = null;
    $viewedObject = null;
    $viewIndex = $http->hasVariable( 'BrowseView' ) && ctype_digit( (string)$http->variable( 'BrowseView' ) ) ? (int)$http->variable( 'BrowseView' ) : -1;
    if ( $viewIndex >= 0 )
    {
        $allFiles = eZPackageFileBrowser::allFiles( $package );
        if ( isset( $allFiles[$viewIndex] ) )
        {
            $viewedFile = $allFiles[$viewIndex];
            $viewedFile['index'] = $viewIndex;
            $viewedFile['kind'] = eZPackageFileBrowser::resolvedKind( $package, $viewedFile );
            $realPath = eZPackageFileBrowser::filePath( $package, $viewedFile['path'] );
            if ( $realPath !== false && $viewedFile['kind'] !== 'image' )
            {
                $bytes = (string)@file_get_contents( $realPath );
                $viewedContent = eZPackageFileBrowser::prettyPrintXML( $bytes );
                if ( $viewedFile['kind'] === 'object' )
                    $viewedObject = eZPackageFileBrowser::objectItemSummary( $bytes );
            }
            elseif ( $realPath === false )
            {
                $viewedFile = null;
            }
        }
    }

    // Every link the template needs is built here, in full (base query string plus one changed
    // parameter), rather than asking the template to do arithmetic or query-string assembly it has
    // no operators for - eZ TPL's |ezurl only ever resolves a plain path, so each one below is that
    // path (added by the template, the one place a package's own name is escaped for a URL)
    // concatenated with a plain "?..." this script already built.
    $baseQuery = 'BrowseType=' . rawurlencode( $typeFilter ) . '&BrowseSearch=' . rawurlencode( $search ) . '&BrowseLimit=' . rawurlencode( (string)$page['limit'] );
    $limitInt = $page['limit'] === 'all' ? max( 1, $page['total_filtered'] ) : max( 1, (int)$page['limit'] );

    $ContentsBrowser = array(
        'files' => $page['files'],
        'total_all' => $page['total_all'],
        'total_filtered' => $page['total_filtered'],
        'offset' => $page['offset'],
        'limit' => $page['limit'],
        'page' => $page['page'],
        'pages' => $page['pages'],
        'type_filter' => $typeFilter,
        'search' => $search,
        'viewed_file' => $viewedFile,
        'viewed_content' => $viewedContent,
        'viewed_object' => $viewedObject,
        'query_first' => $baseQuery . '&BrowseOffset=0',
        'query_prev' => $baseQuery . '&BrowseOffset=' . max( 0, $page['offset'] - $limitInt ),
        'query_next' => $baseQuery . '&BrowseOffset=' . ( $page['offset'] + $limitInt ),
        'query_last' => $baseQuery . '&BrowseOffset=last',
        'query_close' => $baseQuery . '&BrowseOffset=' . $page['offset'],
    );
    foreach ( $ContentsBrowser['files'] as &$fileRow )
        $fileRow['query_view'] = $baseQuery . '&BrowseOffset=' . $page['offset'] . '&BrowseView=' . $fileRow['index'];
    unset( $fileRow );
}
$tpl->setVariable( 'ContentsBrowser', $ContentsBrowser );

$Result = array();
$Result['content'] = $tpl->fetch( "design:package/view/$viewMode.tpl" );
$path = array( array( 'url' => 'package/list',
                      'text' => ezpI18n::tr( 'kernel/package', 'Packages' ) ) );
if ( $repositoryInformation and $repositoryInformation['id'] != 'local' )
{
    $path[] = array( 'url' => 'package/list/' . $repositoryInformation['id'],
                     'text' => $repositoryInformation['name'] );
}
$path[] = array( 'url' => false,
                 'text' => $package->attribute( 'name' ) );
$Result['path'] = $path;

?>
