<?php
/**
 * The code of kernel/package/export.php, moved into a class (#207 stage 1). The file kernel/package/export.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/package/export.php:
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

/**
 * package/export/<PackageName>[/<RepositoryID>]: the package as an .ezpkg archive (gzip compressed tar), built in
 * the user's own export directory, sent in pieces by eZPackageDownload and removed again. The package itself is
 * only read. Needs package/export for the package's type.
 */
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
        $repositoryID = isset( $Params['RepositoryID'] ) && $Params['RepositoryID'] ? $Params['RepositoryID'] : false;
        $repository = $repositoryID !== false ? \eZPackageRequestGuard::repository( $repositoryID ) : null;
        if ( !\eZPackageRequestGuard::isSafeName( $packageName ) || $repository === false )
            return $this->viewResult( null, $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        $package = \eZPackage::fetch( $packageName, $repository ? $repository['path'] : false );
        if ( !$package )
            return $this->viewResult( null, $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        if ( $repository )
            $package->setCurrentRepositoryInformation( $repository );

        if ( !$package->attribute( 'can_export' ) )
            return $this->viewResult( null, $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        // Building the archive is the only part that may fail with an exception; sending it and ending the request
        // stay outside the try: under Exponential Velocity eZExecution::cleanExit() ends a request by throwing,
        // and a catch around it would render the page on after the file.
        $exportName = $package->exportName();
        $fileName = false;
        try
        {
            $exportDirectory = \eZPackage::temporaryExportPath();
            if ( !is_dir( $exportDirectory ) )
                \eZDir::mkdir( $exportDirectory, false, true );
            $fileName = $package->exportToArchive( $exportDirectory . '/' . $exportName );
        }
        catch ( \Exception $e )
        {
            \eZDebug::writeError( 'Exporting package ' . $packageName . ' failed: ' . $e->getMessage(), __METHOD__ );
            $fileName = false;
        }
        if ( !$fileName || !is_file( $fileName ) )
            return $this->viewResult( null, $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        // Audit (doc/bc/6.0/audit.md, data.export.package)
        if ( class_exists( 'expAuditHook' ) )
            \expAuditHook::emit( 'data.export.package', array( 'object' => array( 'type' => 'package', 'id' => (string)$package->attribute( 'name' ) ),
                'verb' => 'export', 'after' => array( 'name' => (string)$package->attribute( 'name' ), 'file' => (string)$exportName,
                                                      'sha256' => hash_file( 'sha256', $fileName ) ) ) );

        header( 'X-Powered-By: ' . \ExponentialSDK::EDITION );
        \eZPackageDownload::send( $fileName, $exportName, 'application/octet-stream' );
        @unlink( $fileName );
        \eZExecution::cleanExit();

        return $this->viewResult( null, null );
    }
}

}
