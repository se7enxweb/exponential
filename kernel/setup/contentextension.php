<?php
/**
 * The content extension wizard view.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$Module = $Params['Module'];

require_once 'kernel/setup/expcontentextensionwizard.php';

$http = eZHTTPTool::instance();
$tpl  = eZTemplate::factory();

$input = array();
foreach ( array( 'name', 'title', 'summary', 'author', 'vendor', 'version', 'licence',
                 'class', 'class_name', 'class_group', 'pattern', 'locale',
                 'attributes', 'tags', 'strings' ) as $field )
{
    if ( $http->hasPostVariable( $field ) && is_scalar( $http->postVariable( $field ) ) )
        $input[$field] = $http->postVariable( $field );
}

if ( $http->hasPostVariable( 'Submitted' ) )
    $input['parts'] = $http->hasPostVariable( 'Parts' ) && is_array( $http->postVariable( 'Parts' ) )
                      ? $http->postVariable( 'Parts' ) : array();

$settings = expContentExtensionWizard::settings( $input );
$feedback = array();
$written  = array();

// ── Hand over an archive ────────────────────────────────────────────────────
if ( $http->hasPostVariable( 'DownloadButton' ) )
{
    $archive = expContentExtensionWizard::archive( $settings );

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
    $result     = expContentExtensionWizard::write( $settings );
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
    $written    = $result['written'];
}

$problems = expContentExtensionWizard::problems( $settings );
$files    = expContentExtensionWizard::files( $settings );

$preview = array();
foreach ( $files as $path => $contents )
    $preview[] = array( 'path'     => $path,
                        'basename' => basename( $path ),
                        'bytes'    => strlen( $contents ),
                        'lines'    => $contents === '' ? 0 : substr_count( $contents, "\n" ) + 1,
                        'contents' => $contents );

// Posted back as typed, so a half typed list is not rewritten under the cursor.
$raw = array();
foreach ( array( 'attributes', 'tags', 'strings' ) as $field )
    $raw[$field] = isset( $input[$field] ) ? $input[$field] : '';

$topics = array();
foreach ( expContentExtensionWizard::topics() as $key => $topic )
    $topics[] = array_merge( $topic, array( 'key'    => $key,
                                            'chosen' => !empty( $settings['parts'][$key] ) ? 1 : 0 ) );

$tpl->setVariable( 'wizard_settings', $settings );
$tpl->setVariable( 'wizard_raw', $raw );
$tpl->setVariable( 'wizard_parts', expContentExtensionWizard::parts() );
$tpl->setVariable( 'wizard_licences', expContentExtensionWizard::licences() );
$tpl->setVariable( 'wizard_topics', $topics );
$tpl->setVariable( 'wizard_datatypes', expContentExtensionWizard::datatypes() );
$tpl->setVariable( 'wizard_groups', expContentExtensionWizard::groups() );
$tpl->setVariable( 'wizard_attributes', $settings['attributes'] );
$tpl->setVariable( 'wizard_problems', $problems );
$tpl->setVariable( 'wizard_feedback', $feedback );
$tpl->setVariable( 'wizard_files', $preview );
$tpl->setVariable( 'wizard_file_count', count( $files ) );
$tpl->setVariable( 'wizard_written', $written );
// As 1 or 0 rather than true or false: the template engine is happier being
// asked about a number than about a php boolean.
$tpl->setVariable( 'wizard_can_write', expContentExtensionWizard::canWrite() ? 1 : 0 );
$tpl->setVariable( 'wizard_ready', ( count( $files ) > 0
                                     && count( $problems ) === 0
                                     && expContentExtensionWizard::canWrite() ) ? 1 : 0 );
$tpl->setVariable( 'wizard_can_archive', class_exists( 'ZipArchive' ) ? 1 : 0 );
$tpl->setVariable( 'wizard_activation', expContentExtensionWizard::activation( $settings ) );
$tpl->setVariable( 'wizard_target', 'extension/' . $settings['name'] );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/contentextension.tpl' );
$Result['path'] = array( array( 'url' => 'setup/rad',
                                'text' => ezpI18n::tr( 'kernel/setup', 'Rapid Application Development' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Content extension wizard' ) ) );
