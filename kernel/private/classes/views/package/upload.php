<?php
/**
 * The code of kernel/package/upload.php, moved into a class (#207 stage 1). The file kernel/package/upload.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/package/upload.php:
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

class Upload extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];

        if ( !\eZPackage::canUsePolicyFunction( 'import' ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        $package = false;
        $installElements = false;
        $errorList = array();

        if ( $module->isCurrentAction( 'UploadPackage' ) )
        {
            if ( \eZHTTPFile::canFetch( 'PackageBinaryFile' ) )
            {
                $file = \eZHTTPFile::fetch( 'PackageBinaryFile' );
                if ( $file )
                {
                    $packageFilename = $file->attribute( 'filename' );

                    $package = \eZPackage::import( $packageFilename, $packageName );
                    if ( $package instanceof \eZPackage )
                    {
                        if ( $package->attribute( 'install_type' ) != 'install' or
                             !$package->attribute( 'can_install' ) )
                        {
                            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'view', array( 'full', $package->attribute( 'name' ) ) ) );
                        }
                        else if ( $package->attribute( 'install_type' ) == 'install' )
                        {
                            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectToView( 'install', array( $package->attribute( 'name' ) ) ) );
                        }
                    }
                    else if ( $package == \eZPackage::STATUS_ALREADY_EXISTS )
                    {
                        $errorList[] = array( 'description' => \ezpI18n::tr( 'kernel/package', 'Package %packagename already exists, cannot import the package', false, array( '%packagename' => $packageName ) ) );
                    }
                    else if ( $package == \eZPackage::STATUS_INVALID_NAME )
                    {
                        $errorList[] = array( 'description' => \ezpI18n::tr( 'kernel/package', 'The package name %packagename is invalid, cannot import the package', false, array( '%packagename' => $packageName ) ) );
                    }
                    else
                    {
                        \eZDebug::writeError( "Uploaded file is not an Exponential package" );
                    }
                }
                else
                {
                    \eZDebug::writeError( "Failed fetching upload package file" );
                }
            }
            else
            {
                \eZDebug::writeError( "No uploaded package file was found" );
            }
        }
        else if ( $module->isCurrentAction( 'UploadCancel' ) )
        {
            $module->redirectToView( 'list' );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'package', $package );
        $tpl->setVariable( 'error_list', $errorList );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:package/upload.tpl" );
        $Result['path'] = array( array( 'url' => 'package/list',
                                        'text' => \ezpI18n::tr( 'kernel/package', 'Packages' ) ),
                                 array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/package', 'Upload' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
