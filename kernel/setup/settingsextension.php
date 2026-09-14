<?php
/**
 * The settings extension wizard view.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$Module = $Params['Module'];

require_once 'kernel/setup/expsettingsextensionwizard.php';

$http = eZHTTPTool::instance();
$tpl  = eZTemplate::factory();

$input = array();
foreach ( array( 'name', 'class', 'title', 'summary', 'author', 'vendor', 'version', 'licence',
                 'siteaccess', 'aliases', 'rules', 'forms', 'operations', 'overrides' ) as $field )
{
    if ( $http->hasPostVariable( $field ) && is_scalar( $http->postVariable( $field ) ) )
        $input[$field] = $http->postVariable( $field );
}

if ( $http->hasPostVariable( 'Submitted' ) )
{
    $input['parts']  = $http->hasPostVariable( 'Parts' ) && is_array( $http->postVariable( 'Parts' ) )
                       ? $http->postVariable( 'Parts' ) : array();
    $input['events'] = $http->hasPostVariable( 'Events' ) && is_array( $http->postVariable( 'Events' ) )
                       ? $http->postVariable( 'Events' ) : array();
}

$settings = expSettingsExtensionWizard::settings( $input );
$feedback = array();
$written  = array();

// ── Hand over an archive ────────────────────────────────────────────────────
if ( $http->hasPostVariable( 'DownloadButton' ) )
{
    $archive = expSettingsExtensionWizard::archive( $settings );

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
    $result     = expSettingsExtensionWizard::write( $settings );
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
    $written    = $result['written'];
}

$problems = expSettingsExtensionWizard::problems( $settings );
$files    = expSettingsExtensionWizard::files( $settings );

$preview = array();
foreach ( $files as $path => $contents )
    $preview[] = array( 'path'     => $path,
                        'basename' => basename( $path ),
                        'bytes'    => strlen( $contents ),
                        'lines'    => $contents === '' ? 0 : substr_count( $contents, "\n" ) + 1,
                        'contents' => $contents );

// The boxes are posted back as typed rather than as the wizard read them, so a
// half typed list is not rewritten under the cursor.
$raw = array();
foreach ( array( 'aliases', 'rules', 'forms', 'operations', 'overrides' ) as $field )
    $raw[$field] = isset( $input[$field] ) ? $input[$field] : '';

$topics = array();
foreach ( expSettingsExtensionWizard::topics() as $key => $topic )
    $topics[] = array_merge( $topic, array(
        'key'    => $key,
        'chosen' => !empty( $settings['parts'][$key] ) ? 1 : 0 ) );

// The events, grouped so that two dozen checkboxes read as a few short lists
// rather than as one long one.
$events = array();
foreach ( expSettingsExtensionWizard::events() as $event => $about )
{
    $group = strtok( $event, '/' );

    $events[$group][] = array_merge( $about, array(
        'event'  => $event,
        'method' => expSettingsExtensionWizard::methodFor( $event ),
        'chosen' => in_array( $event, $settings['events'], true ) ? 1 : 0 ) );
}

$eventGroups = array();
foreach ( $events as $group => $list )
    $eventGroups[] = array( 'group' => $group, 'events' => $list );

$tpl->setVariable( 'wizard_settings', $settings );
$tpl->setVariable( 'wizard_raw', $raw );
$tpl->setVariable( 'wizard_parts', expSettingsExtensionWizard::parts() );
$tpl->setVariable( 'wizard_licences', expSettingsExtensionWizard::licences() );
$tpl->setVariable( 'wizard_topics', $topics );
$tpl->setVariable( 'wizard_event_groups', $eventGroups );
$tpl->setVariable( 'wizard_filters', expSettingsExtensionWizard::filters() );
$tpl->setVariable( 'wizard_methods', expSettingsExtensionWizard::clearMethods() );
$tpl->setVariable( 'wizard_types', expSettingsExtensionWizard::collectTypes() );
$tpl->setVariable( 'wizard_chosen', expSettingsExtensionWizard::chosenTopics( $settings ) );
$tpl->setVariable( 'wizard_problems', $problems );
$tpl->setVariable( 'wizard_feedback', $feedback );
$tpl->setVariable( 'wizard_files', $preview );
$tpl->setVariable( 'wizard_file_count', count( $files ) );
$tpl->setVariable( 'wizard_written', $written );
// As 1 or 0 rather than true or false: the template engine is happier being
// asked about a number than about a php boolean.
$tpl->setVariable( 'wizard_can_write', expSettingsExtensionWizard::canWrite() ? 1 : 0 );
$tpl->setVariable( 'wizard_ready', ( count( $files ) > 0
                                     && count( $problems ) === 0
                                     && expSettingsExtensionWizard::canWrite() ) ? 1 : 0 );
$tpl->setVariable( 'wizard_can_archive', class_exists( 'ZipArchive' ) ? 1 : 0 );
$tpl->setVariable( 'wizard_activation', expSettingsExtensionWizard::activation( $settings ) );
$tpl->setVariable( 'wizard_target', 'extension/' . $settings['name'] );
$tpl->setVariable( 'wizard_siteaccess_list',
                   (array) eZINI::instance( 'site.ini' )->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/settingsextension.tpl' );
$Result['path'] = array( array( 'url' => 'setup/rad',
                                'text' => ezpI18n::tr( 'kernel/setup', 'Rapid Application Development' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Settings extension wizard' ) ) );
