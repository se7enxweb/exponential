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

/**
 * package/upload: imports an uploaded package archive into its repository (the one its vendor names, "local"
 * without one). The archive is inspected first (eZPackageUploadInspector): its name, size, format, every entry's
 * path and type, and its package.xml; nothing of it is extracted unless all of that holds, and an existing package
 * is never overwritten. Needs package/import.
 *
 * POST names as before: UploadPackageButton (the file PackageBinaryFile), UploadCancelButton. Template variables:
 * package, error_list as before; upload_limits for the page's explanation.
 */
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
            return $this->viewResult( null, $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        $package = false;
        $errorList = array();
        $limits = \eZPackageUploadInspector::limits();

        if ( $module->isCurrentAction( 'UploadCancel' ) )
            return $this->viewResult( null, $module->redirectToView( 'list' ) );

        if ( $module->isCurrentAction( 'UploadPackage' ) )
        {
            $file = \eZHTTPFile::canFetch( 'PackageBinaryFile' ) ? \eZHTTPFile::fetch( 'PackageBinaryFile' ) : false;
            if ( !$file )
            {
                $errorList[] = array( 'description' => \ezpI18n::tr( 'kernel/package', 'No file was uploaded, or it was larger than the server accepts (%size).', false,
                                                                     array( '%size' => (string)ini_get( 'upload_max_filesize' ) ) ) );
            }
            else
            {
                $packageFilename = $file->attribute( 'filename' );
                $inspection = \eZPackageUploadInspector::inspect( $packageFilename, (string)$file->attribute( 'original_filename' ), $limits );
                $packageName = $inspection['name'];
                if ( !$inspection['ok'] )
                {
                    $errorList[] = array( 'description' => \eZPackageUploadInspector::message( $inspection, $limits ) );
                }
                else
                {
                    // the package's own validation before anything is imported (expPackageJson::validateDirectory():
                    // every document well-formed and, in the JSON format, against the schema; the checksums of
                    // package.json): a package that fails it is refused with what failed
                    $validation = self::validate( $packageFilename );
                    if ( !$validation['valid'] )
                    {
                        $errorList[] = array( 'description' => \ezpI18n::tr( 'kernel/package', 'The package %packagename did not pass the validation and was not imported:', false,
                                                                             array( '%packagename' => $packageName ) ),
                                              'details' => array_slice( $validation['errors'], 0, 20 ) );
                    }
                }
                if ( !$errorList )
                {
                    $package = \eZPackage::import( $packageFilename, $packageName );
                    if ( $package instanceof \eZPackage )
                    {
                        // the validation is shown on this package's next pages (install, view) until the next action on it
                        \eZHTTPTool::instance()->setSessionVariable( 'eZPackageValidation', array( 'package' => (string)$package->attribute( 'name' ), 'source' => 'upload', 'at' => time() ) + $validation );
                        // the repository the vendor names (import() reads the package back by path, as "local")
                        $viewParameters = array( 'full', $package->attribute( 'name' ) );
                        if ( $inspection['vendor_dir'] !== 'local' )
                            $viewParameters[] = $inspection['vendor_dir'];
                        if ( $package->attribute( 'install_type' ) != 'install' or !$package->attribute( 'can_install' ) )
                            return $this->viewResult( null, $module->redirectToView( 'view', $viewParameters ) );
                        return $this->viewResult( null, $module->redirectToView( 'install', array( $package->attribute( 'name' ) ) ) );
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
                        $errorList[] = array( 'description' => \ezpI18n::tr( 'kernel/package', 'The package could not be imported.' ) );
                    }
                    $package = false;
                }
            }
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'package', $package );
        $tpl->setVariable( 'error_list', $errorList );
        $tpl->setVariable( 'upload_limits', array(
            'max_archive_size' => $limits['max_archive_size'],
            'max_unpacked_size' => $limits['max_unpacked_size'],
            'max_entries' => $limits['max_entries'],
            'suffixes' => $limits['suffixes'],
            'upload_max_filesize' => (string)ini_get( 'upload_max_filesize' ),
            'post_max_size' => (string)ini_get( 'post_max_size' ),
        ) );
        $tpl->setVariable( 'upload_vendor', \eZPackageCatalog::vendorRepository() );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:package/upload.tpl" );
        $Result['path'] = array( array( 'url' => 'package/list',
                                        'text' => \ezpI18n::tr( 'kernel/package', 'Packages' ) ),
                                 array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/package', 'Upload' ) ) );

        return $this->viewResult( $Result, null );
    }

    /**
     * The validation of an uploaded archive: extracted into a private work directory (removed again) and checked
     * with expPackageJson::validateDirectory().
     *
     * @param string $archive
     * @return array valid, errors, warnings, format, documents, files, checksums (whether package.json lists them)
     */
    public static function validate( $archive )
    {
        $work = \expPackageJson::workDirectory( 'upload-validate' );
        try
        {
            if ( !\expPackageJson::extractArchive( $archive, $work . '/pkg', $error ) )
                return array( 'valid' => false, 'errors' => array( (string)$error ), 'warnings' => array(), 'format' => '', 'documents' => 0, 'files' => 0, 'checksums' => false );
            $report = \expPackageJson::validateDirectory( $work . '/pkg' );
            $report['checksums'] = $report['format'] === \eZPackage::FORMAT_JSON && !in_array( 'package.json has no checksums', $report['warnings'], true );
            return $report;
        }
        finally
        {
            \eZDir::recursiveDelete( $work );
        }
    }
}

}
