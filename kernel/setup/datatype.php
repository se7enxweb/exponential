<?php
/**
 * The datatype wizard view.
 *
 * Replaces the three step download-a-file wizard that stood here since 2003.
 * That one asked four questions and handed back one php file with a class in
 * it; this one asks what the datatype has to do, shows every file it would
 * write, and writes a working extension.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$Module = $Params['Module'];

require_once 'kernel/setup/expdatatypewizard.php';

$http = eZHTTPTool::instance();
$tpl  = eZTemplate::factory();

$input = array();
foreach ( array( 'name', 'type', 'class', 'title', 'summary', 'author',
                 'vendor', 'version', 'licence', 'group' ) as $field )
{
    if ( $http->hasPostVariable( $field ) && is_scalar( $http->postVariable( $field ) ) )
        $input[$field] = $http->postVariable( $field );
}

// Unticked boxes post nothing, so these are only read once the form has been
// sent at least once. Otherwise a first visit would arrive with everything
// switched off rather than at its defaults.
if ( $http->hasPostVariable( 'Submitted' ) )
{
    foreach ( array( 'parts' => 'Parts', 'capabilities' => 'Capabilities', 'storage' => 'Storage' ) as $key => $variable )
        $input[$key] = $http->hasPostVariable( $variable ) && is_array( $http->postVariable( $variable ) )
                       ? $http->postVariable( $variable )
                       : array();

    if ( $http->hasPostVariable( 'ClassSettingNames' ) && is_array( $http->postVariable( 'ClassSettingNames' ) ) )
        $input['class_setting_names'] = $http->postVariable( 'ClassSettingNames' );
}

$settings = expDatatypeWizard::settings( $input );
$feedback = array();
$written  = array();

// ── Hand over an archive ────────────────────────────────────────────────────
//
// This answers with a file rather than a page, so it is done before anything is
// drawn and the request ends here.
if ( $http->hasPostVariable( 'DownloadButton' ) )
{
    $archive = expDatatypeWizard::archive( $settings );

    if ( $archive['ok'] && file_exists( $archive['path'] ) )
    {
        while ( ob_get_level() )
            ob_end_clean();

        header( 'Content-Type: application/zip' );
        header( 'Content-Disposition: attachment; filename="' . $archive['filename'] . '"' );
        header( 'Content-Length: ' . filesize( $archive['path'] ) );
        header( 'X-Powered-By: ' . eZPublishSDK::EDITION );

        readfile( $archive['path'] );
        @unlink( $archive['path'] );

        eZExecution::cleanExit();
    }

    $feedback[] = array( 'ok' => false, 'message' => $archive['message'] );
}

// ── Write it into extension/ ────────────────────────────────────────────────
if ( $http->hasPostVariable( 'CreateButton' ) )
{
    $result     = expDatatypeWizard::write( $settings );
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
    $written    = $result['written'];
}

$problems = expDatatypeWizard::problems( $settings );
$files    = expDatatypeWizard::files( $settings );

$preview = array();
foreach ( $files as $path => $contents )
{
    $preview[] = array( 'path'     => $path,
                        'depth'    => substr_count( $path, '/' ),
                        'basename' => basename( $path ),
                        'bytes'    => strlen( $contents ),
                        'lines'    => $contents === '' ? 0 : substr_count( $contents, "\n" ) + 1,
                        'contents' => $contents );
}

// The capability list, each knowing whether it is on and how much it writes.
$capabilities = array();
foreach ( expDatatypeWizard::capabilities() as $key => $capability )
    $capabilities[] = array_merge( $capability, array(
        'key'     => $key,
        'chosen'  => !empty( $settings['capabilities'][$key] ) ? 1 : 0,
        'locked'  => !empty( $capability['required'] ) ? 1 : 0,
        'count'   => count( $capability['methods'] ) ) );

$storage = array();
foreach ( expDatatypeWizard::storageColumns() as $key => $column )
    $storage[] = array_merge( $column, array(
        'key'    => $key,
        'chosen' => !empty( $settings['storage'][$key] ) ? 1 : 0 ) );

$classSettings = array();
foreach ( expDatatypeWizard::settingColumns() as $key => $column )
    $classSettings[] = array_merge( $column, array(
        'key'   => $key,
        'value' => isset( $settings['settings_used'][$key] ) ? $settings['settings_used'][$key] : '' ) );

$methods = expDatatypeWizard::chosenMethods( $settings );

$tpl->setVariable( 'wizard_settings', $settings );
$tpl->setVariable( 'wizard_parts', expDatatypeWizard::parts() );
$tpl->setVariable( 'wizard_licences', expDatatypeWizard::licences() );
$tpl->setVariable( 'wizard_capabilities', $capabilities );
$tpl->setVariable( 'wizard_storage', $storage );
$tpl->setVariable( 'wizard_class_settings', $classSettings );
$tpl->setVariable( 'wizard_methods', $methods );
$tpl->setVariable( 'wizard_method_count', count( $methods ) );
$tpl->setVariable( 'wizard_problems', $problems );
$tpl->setVariable( 'wizard_feedback', $feedback );
$tpl->setVariable( 'wizard_files', $preview );
$tpl->setVariable( 'wizard_file_count', count( $files ) );
$tpl->setVariable( 'wizard_directories', expDatatypeWizard::directories( $files ) );
$tpl->setVariable( 'wizard_written', $written );
$tpl->setVariable( 'wizard_existing', expDatatypeWizard::existingTypes() );
// As 1 or 0 rather than true or false: the template engine is happier being
// asked about a number than about a php boolean.
$tpl->setVariable( 'wizard_can_write', expDatatypeWizard::canWrite() ? 1 : 0 );
$tpl->setVariable( 'wizard_ready', ( count( $files ) > 0
                                     && count( $problems ) === 0
                                     && expDatatypeWizard::canWrite() ) ? 1 : 0 );
$tpl->setVariable( 'wizard_can_archive', class_exists( 'ZipArchive' ) ? 1 : 0 );
$tpl->setVariable( 'wizard_activation', expDatatypeWizard::activation( $settings ) );
$tpl->setVariable( 'wizard_target', 'extension/' . $settings['name'] );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/datatype.tpl' );
$Result['path'] = array( array( 'url' => 'setup/rad',
                                'text' => ezpI18n::tr( 'kernel/setup', 'Rapid Application Development' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Datatype wizard' ) ) );
