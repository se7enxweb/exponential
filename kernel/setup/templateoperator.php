<?php
/**
 * The template extension wizard view.
 *
 * Replaces the three step download-a-file wizard that stood here since 2003.
 * That one asked five questions, ignored three of the answers, and handed back
 * one php file. This one writes a working extension carrying any mixture of
 * operators, functions, fetch functions and fetch aliases, with the
 * registration that makes each of them reachable.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$Module = $Params['Module'];

require_once 'kernel/setup/exptemplateextensionwizard.php';

$http = eZHTTPTool::instance();
$tpl  = eZTemplate::factory();

$input = array();
foreach ( array( 'name', 'class', 'title', 'summary', 'author', 'vendor', 'version',
                 'licence', 'module', 'operators', 'functions', 'fetches', 'aliases',
                 'parameters' ) as $field )
{
    if ( $http->hasPostVariable( $field ) && is_scalar( $http->postVariable( $field ) ) )
        $input[$field] = $http->postVariable( $field );
}

// Unticked boxes post nothing, so these are only read once the form has been
// sent at least once. Otherwise a first visit would arrive with everything
// switched off rather than at its defaults.
if ( $http->hasPostVariable( 'Submitted' ) )
{
    $input['submitted'] = true;
    $input['parts'] = $http->hasPostVariable( 'Parts' ) && is_array( $http->postVariable( 'Parts' ) )
                      ? $http->postVariable( 'Parts' ) : array();
    $input['hints'] = $http->hasPostVariable( 'Hints' ) && is_array( $http->postVariable( 'Hints' ) )
                      ? $http->postVariable( 'Hints' ) : array();
    $input['input']    = $http->hasPostVariable( 'UseInput' );
    $input['output']   = $http->hasPostVariable( 'UseOutput' );
    $input['children'] = $http->hasPostVariable( 'HasChildren' );
}

$settings = expTemplateExtensionWizard::settings( $input );
$feedback = array();
$written  = array();

// ── Hand over an archive ────────────────────────────────────────────────────
//
// This answers with a file rather than a page, so it is done before anything is
// drawn and the request ends here.
if ( $http->hasPostVariable( 'DownloadButton' ) )
{
    $archive = expTemplateExtensionWizard::archive( $settings );

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
    $result     = expTemplateExtensionWizard::write( $settings );
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
    $written    = $result['written'];
}

$problems = expTemplateExtensionWizard::problems( $settings );
$files    = expTemplateExtensionWizard::files( $settings );

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

// The two name boxes are posted back as typed rather than as the wizard read
// them, so that a half typed list is not rewritten under the cursor.
$raw = array();
foreach ( array( 'operators', 'functions', 'fetches', 'aliases', 'parameters' ) as $field )
    $raw[$field] = isset( $input[$field] ) ? $input[$field] : '';

$hints = array();
foreach ( expTemplateExtensionWizard::hints() as $key => $hint )
    $hints[] = array_merge( $hint, array( 'key'    => $key,
                                          'chosen' => !empty( $settings['hints'][$key] ) ? 1 : 0 ) );

$tpl->setVariable( 'wizard_settings', $settings );
$tpl->setVariable( 'wizard_raw', $raw );
$tpl->setVariable( 'wizard_parts', expTemplateExtensionWizard::parts() );
$tpl->setVariable( 'wizard_licences', expTemplateExtensionWizard::licences() );
$tpl->setVariable( 'wizard_hints', $hints );
$tpl->setVariable( 'wizard_types', expTemplateExtensionWizard::parameterTypes() );
$tpl->setVariable( 'wizard_usage', expTemplateExtensionWizard::usage( $settings ) );
$tpl->setVariable( 'wizard_problems', $problems );
$tpl->setVariable( 'wizard_feedback', $feedback );
$tpl->setVariable( 'wizard_files', $preview );
$tpl->setVariable( 'wizard_file_count', count( $files ) );
$tpl->setVariable( 'wizard_directories', expTemplateExtensionWizard::directories( $files ) );
$tpl->setVariable( 'wizard_written', $written );
// As 1 or 0 rather than true or false: the template engine is happier being
// asked about a number than about a php boolean.
$tpl->setVariable( 'wizard_can_write', expTemplateExtensionWizard::canWrite() ? 1 : 0 );
$tpl->setVariable( 'wizard_ready', ( count( $files ) > 0
                                     && count( $problems ) === 0
                                     && expTemplateExtensionWizard::canWrite() ) ? 1 : 0 );
$tpl->setVariable( 'wizard_can_archive', class_exists( 'ZipArchive' ) ? 1 : 0 );
$tpl->setVariable( 'wizard_activation', expTemplateExtensionWizard::activation( $settings ) );
$tpl->setVariable( 'wizard_target', 'extension/' . $settings['name'] );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/templateoperator.tpl' );
$Result['path'] = array( array( 'url' => 'setup/rad',
                                'text' => ezpI18n::tr( 'kernel/setup', 'Rapid Application Development' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Template extension wizard' ) ) );
