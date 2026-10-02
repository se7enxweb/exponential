<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = array( 'name' => 'eZPackage' );

$ViewList = array();
$ViewList['list'] = array(
    'functions' => array( 'list' ),
    'script' => 'list.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'ChangeRepositoryButton' => 'ChangeRepository',
                                    'InstallPackageButton' => 'InstallPackage',
                                    'RemovePackageButton' => 'RemovePackage',
                                    'ConfirmRemovePackageButton' => 'ConfirmRemovePackage',
                                    'CancelRemovePackageButton' => 'CancelRemovePackage',
                                    'CreatePackageButton' => 'CreatePackage' ),
    'post_action_parameters' => array( 'ChangeRepository' => array( 'RepositoryID' => 'RepositoryID' ),
                                       'RemovePackage' => array( 'PackageSelection' => 'PackageSelection' ),
                                       'ConfirmRemovePackage' => array( 'PackageSelection' => 'PackageSelection' ) ),
    "unordered_params" => array( "offset" => "Offset" ),
    'params' => array( 'RepositoryID' ) );

$ViewList['upload'] = array(
    'functions' => array( 'import' ),
    'script' => 'upload.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'UploadPackageButton' => 'UploadPackage',
                                    'UploadCancelButton' => 'UploadCancel' ),
    'params' => array() );

$ViewList['create'] = array(
    'functions' => array( 'create' ),
    'script' => 'create.php',
    'ui_context' => 'edit',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'CreatePackageButton' => 'CreatePackage',
                                    'PackageStep' => 'PackageStep' ),
    'post_action_parameters' => array( 'CreatePackage' => array( 'CreatorItemID' => 'CreatorItemID' ),
                                       'PackageStep' => array( 'CreatorItemID' => 'CreatorItemID',
                                                               'CreatorStepID' => 'CreatorStepID',
                                                               'PreviousStep' => 'PreviousStepButton',
                                                               'NextStep' => 'NextStepButton' ) ),
    'params' => array() );

$ViewList['export'] = array(
    'functions' => array( 'export' ),
    'script' => 'export.php',
    'ui_context' => 'edit',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array( 'PackageName' ) );

$ViewList['view'] = array(
    'functions' => array( 'read' ),
    'script' => 'view.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'InstallButton' => 'Install',
                                    'UninstallButton' => 'Uninstall',
                                    'ExportButton' => 'Export' ),
    'params' => array( 'ViewMode', 'PackageName', 'RepositoryID' ) );

// package/viewfile/<PackageName>/<FileIndex>: one raw file out of a package's own directory (the
// contents browser on package/view/full, see view.php/eZPackageFileBrowser) - an image shown
// inline, anything else offered as a download. Same policy as 'view': reading a package's own
// files is exactly what reading its metadata already allows.
$ViewList['viewfile'] = array(
    'functions' => array( 'read' ),
    'script' => 'viewfile.php',
    'params' => array( 'PackageName', 'FileIndex' ) );

// package/compare/<PackageName>: the package's content compared with the site's content tree and
// classes (eZPackageComparison), read-only; its state in view parameters, (filter)/(class)/
// (search)/(sort)/(dir)/(limit)/(offset)/(item). Same policy as 'view': it shows what the package
// carries next to what the site already shows its readers. "Compare again" (a POST) only rebuilds
// the comparison's own cache. The import actions (POSTs, always through a confirmation) need the
// package install policy on top; compare.php checks it for each of them.
$ViewList['compare'] = array(
    'functions' => array( 'read' ),
    'script' => 'compare.php',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'CompareRefreshButton' => 'Refresh',
                                    'ImportItemButton' => 'ImportItem',
                                    'ImportSelectedButton' => 'ImportSelected',
                                    'ImportFilterButton' => 'ImportFilter',
                                    'ImportViewedButton' => 'ImportViewed',
                                    'ConfirmImportButton' => 'ConfirmImport',
                                    'CancelImportButton' => 'CancelImport' ),
    'params' => array( 'PackageName' ) );

$ViewList['install'] = array(
    'functions' => array( 'install' ),
    'script' => 'install.php',
    'ui_context' => 'edit',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'HandleError' => 'HandleError',
                                    'InstallPackageButton' => 'InstallPackage',
                                    'PackageStep' => 'PackageStep',
                                    'SkipPackageButton' => 'SkipPackage' ),
    'post_action_parameters' => array( 'InstallPackage' => array( 'InstallerType' => 'InstallerType' ),
                                       'PackageStep' => array( 'InstallerType' => 'InstallerType',
                                                               'InstallStepID' => 'InstallStepID',
                                                               'PreviousStep' => 'PreviousStepButton',
                                                               'NextStep' => 'NextStepButton' ),
                                       'HandleError' => array( 'ActionID' => 'ActionID',
                                                               'RememberAction' => 'RememberAction' ) ),

    'params' => array( 'PackageName' ) );

$ViewList['uninstall'] = array(
    'functions' => array( 'install' ),
    'script' => 'uninstall.php',
    'ui_context' => 'edit',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'HandleError' => 'HandleError',
                                    'UninstallPackageButton' => 'UninstallPackage',
                                    'SkipPackageButton' => 'SkipPackage' ),
    'post_action_parameters' => array( 'HandleError' => array( 'ActionID' => 'ActionID',
                                                               'RememberAction' => 'RememberAction' ) ),
    'params' => array( 'PackageName' ) );

$TypeID = array(
    'name'=> 'Type',
    'values'=> array(),
    'class' => 'eZPackage',
    'function' => 'typeList',
    'parameter' => array(  false )
    );

$CreatorTypeID = array(
    'name'=> 'CreatorType',
    'values'=> array(),
    'class' => 'eZPackageCreationHandler',
    'function' => 'creatorLimitationList',
    'parameter' => array(  false )
    );

$RoleID = array(
    'name'=> 'Role',
    'values'=> array(),
    'class' => 'eZPackage',
    'function' => 'maintainerRoleListForRoles',
    'parameter' => array(  false )
    );


$FunctionList = array();
$FunctionList['read'] = array( 'Type' => $TypeID );
$FunctionList['list'] = array( 'Type' => $TypeID );
$FunctionList['create'] = array( 'Type' => $TypeID,
                                 'CreatorType' => $CreatorTypeID,
                                 'Role' => $RoleID );
$FunctionList['edit'] = array( 'Type' => $TypeID );
$FunctionList['remove'] = array( 'Type' => $TypeID );
$FunctionList['install'] = array( 'Type' => $TypeID );
$FunctionList['import'] = array( 'Type' => $TypeID );
$FunctionList['export'] = array( 'Type' => $TypeID );

?>
