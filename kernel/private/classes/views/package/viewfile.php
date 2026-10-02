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

        $package = \eZPackage::fetch( $packageName );
        if ( !is_object( $package ) || !$package->attribute( 'can_read' ) || $fileIndex < 0 )
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

        $kind = \eZPackageFileBrowser::resolvedKind( $package, $fileRow );
        $mimeMap = array(
            'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
        );
        $ext = strtolower( (string)pathinfo( $fileRow['path'], PATHINFO_EXTENSION ) );
        // Every kind but a real image is offered as data, never as a type a browser would try to render
        // as markup or execute - an .xml/.txt item included, even though this same file is shown
        // pretty-printed as text on the contents browser page itself.
        $type = ( $kind === 'image' && isset( $mimeMap[$ext] ) ) ? $mimeMap[$ext] : 'application/octet-stream';

        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'Pragma: no-cache' );
        header( 'X-Content-Type-Options: nosniff' );
        // A package is uploaded content: an SVG in it may carry script. Inside the browser's <img> it never
        // runs, but opened directly under the admin's own origin it would; the sandbox stops that.
        header( "Content-Security-Policy: default-src 'none'; img-src 'self' data:; style-src 'unsafe-inline'; sandbox" );
        header( 'Content-Type: ' . $type );
        header( 'Content-Length: ' . filesize( $realPath ) );
        if ( $kind !== 'image' )
            header( 'Content-Disposition: attachment; filename="' . str_replace( array( '"', '\\' ), '_', basename( $fileRow['path'] ) ) . '"' );

        while ( @ob_end_clean() );

        $fh = fopen( $realPath, 'rb' );
        while ( $fh && !feof( $fh ) )
            echo fread( $fh, 1048576 );
        if ( $fh )
            fclose( $fh );

        \eZExecution::cleanExit();

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
