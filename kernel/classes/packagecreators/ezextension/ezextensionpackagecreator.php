<?php
/**
 * File containing the eZStylePackageCreator class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \ingroup package
  \class eZStylePackageCreator ezstylepackagecreator.php
  \brief The class eZStylePackageCreator does

*/

class eZExtensionPackageCreator extends eZPackageCreationHandler
{
    public function __construct( $id )
    {
        $steps = array();
        $steps[] = array( 'id' => 'extensionlist',
                          'name' => ezpI18n::tr( 'kernel/package', 'Extensions to include' ),
                          'methods' => array( 'initialize' => 'initializeExtensionName',
                                              'load' => 'loadExtensionName',
                                              'validate' => 'validateExtensionName',
                                              'commit' => 'commitExtensionName' ),
                          'template' => 'extension.tpl' );
        $steps[] = $this->packageInformationStep();
        $steps[] = $this->packageMaintainerStep();
        $steps[] = $this->packageChangelogStep();
        parent::__construct( $id, ezpI18n::tr( 'kernel/package', 'Extension export' ), $steps );
    }

    function finalize( &$package, $http, &$persistentData )
    {
        $this->createPackage( $package, $http, $persistentData, $cleanupFiles, false );

        $extensionHandler = eZPackage::packageHandler( 'ezextension' );

        $extensionList = $persistentData['extensionlist'];
        try
        {
            foreach ( $extensionList as $extensionName )
            {
                // the policy as well: the extension step may lie behind the visitor
                $refusal = eZExtensionPackageHandler::exportRefusal( (string)$extensionName, true );
                if ( $refusal !== '' )
                    throw new RuntimeException( $refusal );
                $extensionHandler->addExtension( $package, $extensionName );
            }
        }
        catch ( RuntimeException $e )
        {
            // an extension refused while the package was made (changed since the extension step): nothing of the
            // package stays, and the wizard shows the reason (kernel/package/create)
            if ( $package instanceof eZPackage )
                eZPackage::removeFiles( $package->path() );
            throw $e;
        }

        $package->setAttribute( 'is_active', true );
        $package->store();
    }

    /*!
     \return \c 'import'
    */
    function packageInstallType( $package, &$persistentData )
    {
        return 'install';
    }

    /*!
     Returns \c 'stable', site style packages are always stable.
    */
    function packageInitialState( $package, &$persistentData )
    {
        return 'stable';
    }

    /*!
     \return \c 'sitestyle'.
    */
    function packageType( $package, &$persistentData )
    {
        return 'extension';
    }

    function initializeExtensionName( $package, $http, $step, &$persistentData, $tpl )
    {
    }

    function loadExtensionName( $package, $http, $step, &$persistentData, $tpl )
    {
        $extensionList = array();
        foreach ( eZExtension::extensionRootDirectories() as $extensionDir )
        {
            if ( !is_dir( $extensionDir ) )
                continue;

            foreach ( eZDir::findSubItems( $extensionDir, 'dl' ) as $extensionName )
            {
                $extensionList[$extensionName] = $extensionName;
            }
        }
        natcasesort( $extensionList );

        $extensionInfo = array();
        foreach ( $extensionList as $extensionName )
        {
            $extensionInfo[$extensionName] = eZExtension::extensionInfo( $extensionName );
        }

        $extensionList = array_values( $extensionList );
        $tpl->setVariable( 'extension_list', $extensionList );
        $tpl->setVariable( 'extension_info', $extensionInfo );
    }

    function validateExtensionName( $package, $http, $currentStepID, &$stepMap, &$persistentData, &$errorList )
    {
        $extensionList = array();

        if ( !$http->hasPostVariable( 'PackageExtensionNames' ) ||
             count( $http->postVariable( 'PackageExtensionNames' ) ) == 0 )
        {
            $errorList[] = array( 'field' => ezpI18n::tr( 'kernel/package', 'Extension list' ),
                                  'description' => ezpI18n::tr( 'kernel/package', 'You must select at least one extension' ) );
            return false;
        }
        // an extension that is not one, or holds secrets, is refused here, before the package is made
        $valid = true;
        foreach ( (array)$http->postVariable( 'PackageExtensionNames' ) as $extensionName )
        {
            $refusal = is_string( $extensionName ) ? eZExtensionPackageHandler::exportRefusal( $extensionName, true ) : ezpI18n::tr( 'kernel/package', 'You must select at least one extension' );
            if ( $refusal === '' )
                $refusal = eZExtensionPackageHandler::packRefusal( $extensionName );
            if ( $refusal !== '' )
            {
                $errorList[] = array( 'field' => ezpI18n::tr( 'kernel/package', 'Extension list' ), 'description' => $refusal );
                $valid = false;
            }
        }
        return $valid;
    }

    function commitExtensionName( $package, $http, $step, &$persistentData, $tpl )
    {
        $persistentData['extensionlist'] = $http->postVariable( 'PackageExtensionNames' );
    }

    /*!
     Fetches the selected content classes and generates a name, summary and description from the selection.
    */
    function generatePackageInformation( &$packageInformation, $package, $http, $step, &$persistentData )
    {
        $extensionList = $persistentData['extensionlist'];
        $extensionCount = count( $extensionList );

        if ( $extensionCount == 1 )
        {
            $extensionName = $extensionList[0];
            $packageInformation['name'] = $extensionName;
            $packageInformation['summary'] = "$extensionName extension";
            $packageInformation['description'] = "This package contains the $extensionName Exponential extension";
        }
        else if ( $extensionCount > 1 )
        {
            $packageInformation['name'] = "$extensionCount Extensions";
            $packageInformation['summary'] = "Export of $extensionCount extensions";
            $description = "This package contains the following Exponential extensions: \n";
            foreach ( $extensionList as $extensionName )
            {
                $description .= "- $extensionName\n";
            }
            $packageInformation['description'] = $description;
        }
    }
}

?>
