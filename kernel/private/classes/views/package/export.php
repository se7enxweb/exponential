<?php
/**
 * The code of kernel/package/export.php, moved into a class (#207 stage 1). The file kernel/package/export.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/package/export.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Package
{

class Export extends \Exponential\Runnable\ModuleView
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

        $package = \eZPackage::fetch( $packageName );
        if ( !$package )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        if ( !$package->attribute( 'can_export' ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );


        $exportDirectory = \eZPackage::temporaryExportPath();
        $exportName = $package->exportName();
        $exportPath = $exportDirectory . '/' . $exportName;
        $exportPath = $package->exportToArchive( $exportPath );

        //return $module->redirectToView( 'view', array( 'full', $package->attribute( 'name' ) ) );

        $fileName = $exportPath;
        if ( $fileName != "" and file_exists( $fileName ) )
        {
            clearstatcache();
            $fileSize = filesize( $fileName );
            $mimeType =  'application/octet-stream';
            $originalFileName = $exportName;
            $contentLength = $fileSize;
            $fileOffset = false;
            $fileLength = false;
            if ( isset( $_SERVER['HTTP_RANGE'] ) )
            {
                $httpRange = trim( $_SERVER['HTTP_RANGE'] );
                if ( preg_match( "/^bytes=([0-9]+)-$/", $httpRange, $matches ) )
                {
                    $fileOffset = $matches[1];
                    header( "Content-Range: bytes $fileOffset-" . $fileSize - 1 . "/$fileSize" );
                    header( "HTTP/1.1 206 Partial Content" );
                    $contentLength -= $fileOffset;
                }
            }

            header( "Pragma: " );
            header( "Cache-Control: " );
            header( "Content-Length: $contentLength" );
            header( "Content-Type: $mimeType" );
            header( "X-Powered-By: " . \ExponentialSDK::EDITION );
            header( "Content-disposition: attachment; filename=$originalFileName" );
            header( "Content-Transfer-Encoding: binary" );
            header( "Accept-Ranges: bytes" );

            $fh = fopen( $fileName, "rb" );
            if ( $fileOffset )
            {
                \eZDebug::writeDebug( $fileOffset, "seeking to fileoffset" );
                fseek( $fh, $fileOffset );
            }

            ob_end_clean();
            fpassthru( $fh );
            fflush( $fh );
            fclose( $fh );
            unlink( $fileName );
            \eZExecution::cleanExit();
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
