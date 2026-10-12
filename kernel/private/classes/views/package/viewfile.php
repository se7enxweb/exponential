<?php
/**
 * The code of kernel/package/viewfile.php, moved into a class (#207 stage 1). The file kernel/package/viewfile.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/package/viewfile.php:
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

class Viewfile extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $packageName = $Params['PackageName'];
        $fileIndex = isset( $Params['FileIndex'] ) && ctype_digit( (string)$Params['FileIndex'] ) ? (int)$Params['FileIndex'] : -1;
        $repositoryID = isset( $scope['Params']['RepositoryID'] ) && $scope['Params']['RepositoryID'] ? $scope['Params']['RepositoryID'] : false;
        $repository = $repositoryID !== false ? \eZPackageRequestGuard::repository( $repositoryID ) : null;

        // A package name that is a directory name and a repository the storage has, as on package/view
        $package = false;
        if ( \eZPackageRequestGuard::isSafeName( $packageName ) && $repository !== false && $fileIndex >= 0 )
        {
            $package = \eZPackage::fetch( $packageName, $repository ? $repository['path'] : false );
            if ( $package && $repository )
                $package->setCurrentRepositoryInformation( $repository );
        }
        if ( !is_object( $package ) || !$package->attribute( 'can_read' ) )
        {
            header( 'HTTP/1.1 404 Not Found' );
            \eZExecution::cleanExit();
        }

        $allFiles = \eZPackageFileBrowser::allFiles( $package );
        $fileRow = isset( $allFiles[$fileIndex] ) ? $allFiles[$fileIndex] : null;
        $realPath = $fileRow ? \eZPackageFileBrowser::filePath( $package, $fileRow['path'] ) : false;
        if ( $realPath === false )
        {
            header( 'HTTP/1.1 404 Not Found' );
            \eZExecution::cleanExit();
        }

        // What the file is by its content (eZPackageFileBrowser::sniff(), not its name): only a png, jpeg, gif or
        // webp image up to INLINE_IMAGE_MAX_BYTES is sent inline with its own type; everything else - an SVG
        // (markup that may carry script), an .xml/.json/.txt item, any other binary - is an attachment of type
        // application/octet-stream, never a type a browser would render as markup or execute. Every answer carries
        // X-Content-Type-Options: nosniff (eZPackageDownload).
        $sniff = \eZPackageFileBrowser::sniff( $realPath );
        $image = $sniff['media'] === 'image' && $sniff['inline'];
        $type = $image ? $sniff['mime'] : 'application/octet-stream';

        // A package is uploaded content: an SVG in it may carry script. Inside the browser's <img> it never
        // runs, but opened directly under the admin's own origin it would; the sandbox stops that.
        header( "Content-Security-Policy: default-src 'none'; img-src 'self' data:; style-src 'unsafe-inline'; sandbox" );
        // in pieces, never read whole (eZPackageDownload); the request ends outside any try/catch
        \eZPackageDownload::send( $realPath, basename( $fileRow['path'] ), $type, $image );
        \eZExecution::cleanExit();

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
