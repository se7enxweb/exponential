<?php
/**
 * File containing the handler wizard view.
 *
 * One address per kind of handler - /setup/handlerextension/session,
 * /setup/handlerextension/mail - so each is its own tool on the RAD page with
 * its own explanation, while they share the engine that writes them.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$Module = $Params['Module'];

require_once 'kernel/setup/exphandlerwizard.php';

$http = eZHTTPTool::instance();
$tpl  = eZTemplate::factory();

// The kind comes from the address, and from the form once it has been sent.
$input = array();
if ( isset( $Params['Kind'] ) && is_string( $Params['Kind'] ) )
    $input['kind'] = $Params['Kind'];

foreach ( array( 'kind', 'name', 'alias', 'class', 'title', 'summary', 'author',
                 'vendor', 'version', 'licence' ) as $field )
{
    if ( $http->hasPostVariable( $field ) && is_scalar( $http->postVariable( $field ) ) )
        $input[$field] = $http->postVariable( $field );
}

if ( $http->hasPostVariable( 'Submitted' ) )
    $input['parts'] = $http->hasPostVariable( 'Parts' ) && is_array( $http->postVariable( 'Parts' ) )
                      ? $http->postVariable( 'Parts' ) : array();

$settings = expHandlerWizard::settings( $input );
$recipe   = expHandlerWizard::kind( $settings['kind'] );
$feedback = array();
$written  = array();

if ( $http->hasPostVariable( 'DownloadButton' ) )
{
    $archive = expHandlerWizard::archive( $settings );

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

if ( $http->hasPostVariable( 'CreateButton' ) )
{
    $result     = expHandlerWizard::write( $settings );
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
    $written    = $result['written'];
}

$problems = expHandlerWizard::problems( $settings );
$files    = expHandlerWizard::files( $settings );

$preview = array();
foreach ( $files as $path => $contents )
    $preview[] = array( 'path'  => $path,
                        'bytes' => strlen( $contents ),
                        'lines' => $contents === '' ? 0 : substr_count( $contents, "\n" ) + 1,
                        'contents' => $contents );

// The other kinds, so one tool links to its siblings.
$others = array();
foreach ( expHandlerWizard::kinds() as $key => $other )
    $others[] = array( 'key'     => $key,
                       'title'   => $other['title'],
                       'current' => $key === $settings['kind'],
                       'url'     => '/setup/handlerextension/' . $key );

$tpl->setVariable( 'wizard_settings', $settings );
$tpl->setVariable( 'wizard_recipe', $recipe );
$tpl->setVariable( 'wizard_methods', $recipe === false ? array() : $recipe['methods'] );
$tpl->setVariable( 'wizard_kinds', $others );
$tpl->setVariable( 'wizard_parts', expHandlerWizard::parts() );
$tpl->setVariable( 'wizard_licences', expHandlerWizard::licences() );
$tpl->setVariable( 'wizard_problems', $problems );
$tpl->setVariable( 'wizard_feedback', $feedback );
$tpl->setVariable( 'wizard_files', $preview );
$tpl->setVariable( 'wizard_file_count', count( $files ) );
$tpl->setVariable( 'wizard_written', $written );
$tpl->setVariable( 'wizard_can_write', expHandlerWizard::canWrite() ? 1 : 0 );
$tpl->setVariable( 'wizard_can_archive', class_exists( 'ZipArchive' ) ? 1 : 0 );
$tpl->setVariable( 'wizard_ready', ( count( $files ) > 0 && count( $problems ) === 0
                                     && expHandlerWizard::canWrite() ) ? 1 : 0 );
$tpl->setVariable( 'wizard_activation', expHandlerWizard::activation( $settings ) );
$tpl->setVariable( 'wizard_target', 'extension/' . $settings['name'] );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/handlerextension.tpl' );
$Result['path'] = array( array( 'url' => 'setup/rad',
                                'text' => ezpI18n::tr( 'kernel/setup', 'Rapid Application Development' ) ),
                         array( 'url' => false,
                                'text' => $recipe === false
                                          ? ezpI18n::tr( 'kernel/setup', 'Handler wizard' )
                                          : $recipe['title'] ) );
