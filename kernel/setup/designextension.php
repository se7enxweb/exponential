<?php
/**
 * File containing the design extension wizard view.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$Module = $Params['Module'];

require_once 'kernel/setup/expdesignextensionwizard.php';

$http = eZHTTPTool::instance();
$tpl  = eZTemplate::factory();

// What the form said, or nothing at all on a first visit.
$input = array();
foreach ( array( 'name', 'title', 'summary', 'author', 'vendor', 'version',
                 'licence', 'base_design', 'siteaccess' ) as $field )
{
    if ( $http->hasPostVariable( $field ) && is_scalar( $http->postVariable( $field ) ) )
        $input[$field] = $http->postVariable( $field );
}

// An unticked box posts nothing, so the parts are only read once the form has
// been sent at least once - otherwise a first visit would arrive with all of
// them switched off rather than at their defaults.
if ( $http->hasPostVariable( 'Submitted' ) )
    $input['parts'] = $http->hasPostVariable( 'Parts' ) && is_array( $http->postVariable( 'Parts' ) )
                      ? $http->postVariable( 'Parts' )
                      : array();

$settings = expDesignExtensionWizard::settings( $input );
$feedback = array();
$written  = array();

// ── Build and hand over an archive ──────────────────────────────────────────
//
// This one answers with a file rather than a page, so it is done before
// anything is drawn and the request ends here.
if ( $http->hasPostVariable( 'DownloadButton' ) )
{
    $archive = expDesignExtensionWizard::archive( $settings );

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
    $result   = expDesignExtensionWizard::write( $settings );
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
    $written  = $result['written'];
}

$problems = expDesignExtensionWizard::problems( $settings );
$files    = $settings['name'] !== '' ? expDesignExtensionWizard::files( $settings ) : array();

// The preview shows the files as a tree as well as a list, because a design is
// a shape on disk and a flat list does not look like one.
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

$tpl->setVariable( 'wizard_settings', $settings );
$tpl->setVariable( 'wizard_parts', expDesignExtensionWizard::parts() );
$tpl->setVariable( 'wizard_licences', expDesignExtensionWizard::licences() );
$tpl->setVariable( 'wizard_base_designs', expDesignExtensionWizard::baseDesigns() );
$tpl->setVariable( 'wizard_problems', $problems );
$tpl->setVariable( 'wizard_feedback', $feedback );
$tpl->setVariable( 'wizard_files', $preview );
$tpl->setVariable( 'wizard_file_count', count( $files ) );
$tpl->setVariable( 'wizard_directories', expDesignExtensionWizard::directories( $files ) );
$tpl->setVariable( 'wizard_written', $written );
$tpl->setVariable( 'wizard_can_write', expDesignExtensionWizard::canWrite() ? 1 : 0 );
// As 1 or 0 rather than true or false: the template engine is happier being
// asked about a number than about a php boolean.
$tpl->setVariable( 'wizard_ready', ( count( $files ) > 0
                                     && count( $problems ) === 0
                                     && expDesignExtensionWizard::canWrite() ) ? 1 : 0 );
$tpl->setVariable( 'wizard_can_archive', class_exists( 'ZipArchive' ) ? 1 : 0 );
$tpl->setVariable( 'wizard_activation', expDesignExtensionWizard::activation( $settings ) );
$tpl->setVariable( 'wizard_target', 'extension/' . $settings['name'] );
$tpl->setVariable( 'wizard_siteaccess_list',
                   (array) eZINI::instance( 'site.ini' )->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/designextension.tpl' );
$Result['path'] = array( array( 'url' => 'setup/rad',
                                'text' => ezpI18n::tr( 'kernel/setup', 'Rapid Application Development' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Design extension wizard' ) ) );
