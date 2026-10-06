<?php
/**
 * The code of kernel/package/view.php, moved into a class (#207 stage 1). The file kernel/package/view.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/package/view.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Package
{

class View extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $viewMode = $Params['ViewMode'];
        $packageName = $Params['PackageName'];
        $repositoryID = false;
        if ( isset( $Params['RepositoryID'] ) and $Params['RepositoryID'] )
            $repositoryID = $Params['RepositoryID'];

        // Only a view mode with a template, a package name that is a directory name and a repository the storage
        // has: a name such as "../7x/x" made eZPackage::fetch() read a package.xml outside the repository asked for,
        // and an unknown mode drew an empty page.
        $viewMode = \eZPackageRequestGuard::viewMode( $viewMode );
        $repository = $repositoryID !== false ? \eZPackageRequestGuard::repository( $repositoryID ) : null;
        if ( $viewMode === false || !\eZPackageRequestGuard::isSafeName( $packageName ) || $repository === false )
            return $this->viewResult( null, $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        $package = \eZPackage::fetch( $packageName, $repository ? $repository['path'] : false, false );
        if ( is_object( $package ) && $repository )
            $package->setCurrentRepositoryInformation( $repository );
        if ( !is_object( $package ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        if ( !$package->attribute( 'can_read' ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );


        if ( $module->isCurrentAction( 'Export' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->run( 'export', $repositoryID !== false ? array( $packageName, $repositoryID ) : array( $packageName ) ) );
        }
        else if ( $module->isCurrentAction( 'Install' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'install', array( $packageName ) ) );
        }
        else if ( $module->isCurrentAction( 'Uninstall' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'uninstall', array( $packageName ) ) );
        }

        $repositoryInformation = $package->currentRepositoryInformation();

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'package_name', $packageName );
        $tpl->setVariable( 'repository_id', $repositoryID );

        // The package contents browser (package/view/full only): every file the package's own directory
        // carries, paginated and filtered, kernel-only code (eZPackageFileBrowser) with no dependency on
        // any extension. $ContentsBrowser stays false for every other ViewMode (files.tpl, for instance,
        // already has its own listing).
        $ContentsBrowser = false;
        if ( $viewMode === 'full' )
        {
            // The browser's state is in view parameters, like every other paged kernel view:
            //   package/view/full/<name>/(type)/object/(search)/abc/(limit)/100/(offset)/4300/(file)/4342
            // Each is left out at its default (no type, no search, 50 per page, offset 0, no file open).
            // The kernel URL-decodes the whole path before splitting it, so the search, the one free text,
            // is written encoded twice (see $browseURL) and decoded once more here; the rest are keywords
            // and numbers. The filter form submits the old GET fields (BrowseType, BrowseSearch,
            // BrowseLimit, BrowseApply), and links made before may carry BrowseOffset/BrowseView: both are
            // answered with one redirect to the same state as view parameters, so the address bar only
            // ever shows the clean form and old links keep working.
            $http = \eZHTTPTool::instance();
            $userParameters = isset( $Params['UserParameters'] ) && is_array( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();
            $limitChoices = array( '25', '50', '100', '250', '1000', 'all' );
            $typeChoices = array( 'class', 'object', 'image', 'simplefile', 'document', 'package', 'other' );
            $normalise = function ( $type, $search, $limit, $offset, $file ) use ( $limitChoices, $typeChoices )
            {
                return array(
                    'type' => in_array( (string)$type, $typeChoices, true ) ? (string)$type : '',
                    'search' => trim( (string)$search ),
                    'limit' => in_array( (string)$limit, $limitChoices, true ) ? (string)$limit : '50',
                    'offset' => (string)$offset === 'last' ? 'last' : ( ctype_digit( (string)$offset ) ? (int)$offset : 0 ),
                    'file' => ctype_digit( (string)$file ) ? (int)$file : -1,
                );
            };
            // The path suffix for a state; only what differs from the defaults is written.
            $browseURL = function ( array $state ) use ( $packageName, $repositoryID )
            {
                $url = '/package/view/full/' . $packageName . ( $repositoryID !== false ? '/' . $repositoryID : '' );
                if ( $state['type'] !== '' )
                    $url .= '/(type)/' . $state['type'];
                if ( $state['search'] !== '' )
                    $url .= '/(search)/' . rawurlencode( rawurlencode( $state['search'] ) );
                if ( $state['limit'] !== '50' )
                    $url .= '/(limit)/' . $state['limit'];
                if ( $state['offset'] === 'last' || $state['offset'] > 0 )
                    $url .= '/(offset)/' . $state['offset'];
                if ( $state['file'] >= 0 )
                    $url .= '/(file)/' . $state['file'];
                return $url;
            };

            $legacyFields = array( 'BrowseApply', 'BrowseType', 'BrowseSearch', 'BrowseLimit', 'BrowseOffset', 'BrowseView' );
            $hasLegacy = false;
            foreach ( $legacyFields as $field )
                $hasLegacy = $hasLegacy || $http->hasGetVariable( $field );
            if ( $hasLegacy )
            {
                $get = function ( $name, $fallback ) use ( $http ) { return $http->hasGetVariable( $name ) ? $http->getVariable( $name ) : $fallback; };
                // A changed filter always starts on the first page: an old offset means nothing against a
                // newly filtered list
                $applied = $http->hasGetVariable( 'BrowseApply' );
                $state = $normalise( $get( 'BrowseType', '' ), $get( 'BrowseSearch', '' ), $get( 'BrowseLimit', '50' ),
                                     $applied ? 0 : $get( 'BrowseOffset', 0 ), $applied ? -1 : $get( 'BrowseView', -1 ) );
                // Not $module->redirectTo(): the kernel adds the request's query string to every module
                // redirect, and these very fields would then send the next request back here, for ever.
                // The path is complete and encoded already (the search twice, on purpose), so it goes out as
                // it is, with only the siteaccess prefix added.
                $target = $browseURL( $state );
                \eZURI::transformURI( $target, false, 'full' );
                \eZHTTPTool::redirect( $target, array(), '302 Found', false );
                \eZExecution::cleanExit();
            }

            $state = $normalise(
                isset( $userParameters['type'] ) ? $userParameters['type'] : '',
                isset( $userParameters['search'] ) ? rawurldecode( $userParameters['search'] ) : '',
                isset( $userParameters['limit'] ) ? $userParameters['limit'] : '50',
                isset( $userParameters['offset'] ) ? $userParameters['offset'] : 0,
                isset( $userParameters['file'] ) ? $userParameters['file'] : -1
            );
            $typeFilter = $state['type'];
            $search = $state['search'];

            $page = \eZPackageFileBrowser::filteredPage( $package, array(
                'offset' => $state['offset'], 'limit' => $state['limit'], 'type' => $typeFilter, 'search' => $search,
            ) );

            $viewedFile = null;
            $viewedContent = null;
            $viewedObject = null;
            $viewIndex = $state['file'];
            if ( $viewIndex >= 0 )
            {
                $allFiles = \eZPackageFileBrowser::allFiles( $package );
                if ( isset( $allFiles[$viewIndex] ) )
                {
                    $viewedFile = $allFiles[$viewIndex];
                    $viewedFile['index'] = $viewIndex;
                    $viewedFile['kind'] = \eZPackageFileBrowser::resolvedKind( $package, $viewedFile );
                    $realPath = \eZPackageFileBrowser::filePath( $package, $viewedFile['path'] );
                    if ( $realPath !== false && $viewedFile['kind'] !== 'image' )
                    {
                        // at most 1 MB is shown on the page; the whole file is a download
                        $bytes = (string)@file_get_contents( $realPath, false, null, 0, 1048576 );
                        $viewedFile['truncated'] = (int)$viewedFile['size'] > strlen( $bytes );
                        $viewedContent = \eZPackageFileBrowser::prettyPrintXML( $bytes );
                        if ( $viewedFile['kind'] === 'object' )
                            $viewedObject = \eZPackageFileBrowser::objectItemSummary( $bytes );
                    }
                    elseif ( $realPath === false )
                    {
                        $viewedFile = null;
                    }
                }
            }

            // Every link the template needs is built here in full, as the view's path with its view
            // parameters (the template passes each through |ezurl), rather than asking the template to do
            // arithmetic it has no operators for. The current page's own offset is the one from $page
            // (already clamped, 'last' resolved), so Close and View stay on the page being looked at.
            $limitInt = $page['limit'] === 'all' ? max( 1, $page['total_filtered'] ) : max( 1, (int)$page['limit'] );
            $here = array( 'type' => $typeFilter, 'search' => $search, 'limit' => (string)$state['limit'], 'offset' => (int)$page['offset'], 'file' => -1 );

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
                'url_first' => $browseURL( array( 'offset' => 0 ) + $here ),
                'url_prev' => $browseURL( array( 'offset' => max( 0, $page['offset'] - $limitInt ) ) + $here ),
                'url_next' => $browseURL( array( 'offset' => $page['offset'] + $limitInt ) + $here ),
                'url_last' => $browseURL( array( 'offset' => 'last' ) + $here ),
                'url_close' => $browseURL( $here ),
            );
            foreach ( $ContentsBrowser['files'] as &$fileRow )
                $fileRow['url_view'] = $browseURL( array( 'file' => (int)$fileRow['index'] ) + $here );
            unset( $fileRow );
        }
        $tpl->setVariable( 'ContentsBrowser', $ContentsBrowser );

        // The package's card (version, state, size, last change, dependencies, whether the installer takes it as a
        // source) and what it carries, read-only (eZPackageCatalog)
        $card = false;
        $contents = false;
        if ( $viewMode === 'full' )
        {
            $scan = \eZPackageCatalog::scan();
            $currentRepository = $package->currentRepositoryInformation();
            $key = $packageName . '@' . ( $currentRepository ? $currentRepository['id'] : 'local' );
            $card = isset( $scan['cards'][$key] ) ? $scan['cards'][$key] : false;
            $contents = \eZPackageCatalog::contents( $package );
        }
        $tpl->setVariable( 'package_card', $card );
        $tpl->setVariable( 'package_contents', $contents );
        $tpl->setVariable( 'package_can_remove', $package->canUsePackagePolicyFunction( 'remove' ) );
        $tpl->setVariable( 'package_repository', $package->currentRepositoryInformation() );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:package/view/$viewMode.tpl" );
        $path = array( array( 'url' => 'package/list',
                              'text' => \ezpI18n::tr( 'kernel/package', 'Packages' ) ) );
        if ( $repositoryInformation and $repositoryInformation['id'] != 'local' )
        {
            $path[] = array( 'url' => 'package/list/' . $repositoryInformation['id'],
                             'text' => $repositoryInformation['name'] );
        }
        $path[] = array( 'url' => false,
                         'text' => $package->attribute( 'name' ) );
        $Result['path'] = $path;

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
