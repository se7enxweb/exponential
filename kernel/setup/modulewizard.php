<?php
/**
 * The module wizard view.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$Module = $Params['Module'];

require_once 'kernel/setup/expmodulewizard.php';

$http = eZHTTPTool::instance();
$tpl  = eZTemplate::factory();

$input = array();
foreach ( array( 'name', 'module', 'title', 'summary', 'author', 'vendor', 'version',
                 'licence', 'context', 'navigation', 'views', 'policies' ) as $field )
{
    if ( $http->hasPostVariable( $field ) && is_scalar( $http->postVariable( $field ) ) )
        $input[$field] = $http->postVariable( $field );
}

if ( $http->hasPostVariable( 'Submitted' ) )
    $input['parts'] = $http->hasPostVariable( 'Parts' ) && is_array( $http->postVariable( 'Parts' ) )
                      ? $http->postVariable( 'Parts' ) : array();

$settings = expModuleWizard::settings( $input );
$feedback = array();
$written  = array();

// ── Hand over an archive ────────────────────────────────────────────────────
if ( $http->hasPostVariable( 'DownloadButton' ) )
{
    $archive = expModuleWizard::archive( $settings );

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
    $result     = expModuleWizard::write( $settings );
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
    $written    = $result['written'];
}

$problems = expModuleWizard::problems( $settings );
$files    = expModuleWizard::files( $settings );

$preview = array();
foreach ( $files as $path => $contents )
    $preview[] = array( 'path'     => $path,
                        'basename' => basename( $path ),
                        'bytes'    => strlen( $contents ),
                        'lines'    => $contents === '' ? 0 : substr_count( $contents, "\n" ) + 1,
                        'contents' => $contents );

$raw = array();
foreach ( array( 'views', 'policies' ) as $field )
    $raw[$field] = isset( $input[$field] ) ? $input[$field] : '';

// The views with their addresses worked out, so the page can show what each
// one will answer at rather than leaving it to be imagined.
$views = array();
foreach ( $settings['views'] as $view )
    $views[] = array_merge( $view, array( 'address' => expModuleWizard::addressOf( $settings, $view ) ) );

$tpl->setVariable( 'wizard_settings', $settings );
$tpl->setVariable( 'wizard_raw', $raw );
$tpl->setVariable( 'wizard_parts', expModuleWizard::parts() );
$tpl->setVariable( 'wizard_licences', expModuleWizard::licences() );
$tpl->setVariable( 'wizard_contexts', expModuleWizard::contexts() );
$tpl->setVariable( 'wizard_navigation_parts', expModuleWizard::navigationParts() );
$tpl->setVariable( 'wizard_limitations', expModuleWizard::limitations() );
$tpl->setVariable( 'wizard_views', $views );
$tpl->setVariable( 'wizard_policies', $settings['policies'] );
$tpl->setVariable( 'wizard_problems', $problems );
$tpl->setVariable( 'wizard_feedback', $feedback );
$tpl->setVariable( 'wizard_files', $preview );
$tpl->setVariable( 'wizard_file_count', count( $files ) );
$tpl->setVariable( 'wizard_written', $written );
// As 1 or 0 rather than true or false: the template engine is happier being
// asked about a number than about a php boolean.
$tpl->setVariable( 'wizard_can_write', expModuleWizard::canWrite() ? 1 : 0 );
$tpl->setVariable( 'wizard_ready', ( count( $files ) > 0
                                     && count( $problems ) === 0
                                     && expModuleWizard::canWrite() ) ? 1 : 0 );
$tpl->setVariable( 'wizard_can_archive', class_exists( 'ZipArchive' ) ? 1 : 0 );
$tpl->setVariable( 'wizard_activation', expModuleWizard::activation( $settings ) );
$tpl->setVariable( 'wizard_target', 'extension/' . $settings['name'] );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/modulewizard.tpl' );
$Result['path'] = array( array( 'url' => 'setup/rad',
                                'text' => ezpI18n::tr( 'kernel/setup', 'Rapid Application Development' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Module wizard' ) ) );
