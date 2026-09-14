<?php
/**
 * File containing the workflow event wizard view.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$Module = $Params['Module'];

require_once 'kernel/setup/expworkfloweventwizard.php';

$http = eZHTTPTool::instance();
$tpl  = eZTemplate::factory();

$input = array();
foreach ( array( 'name', 'event', 'label', 'title', 'summary', 'author', 'vendor',
                 'version', 'licence' ) as $field )
{
    if ( $http->hasPostVariable( $field ) && is_scalar( $http->postVariable( $field ) ) )
        $input[$field] = $http->postVariable( $field );
}

if ( $http->hasPostVariable( 'Triggers' ) )
    $input['triggers'] = $http->postVariable( 'Triggers' );

if ( $http->hasPostVariable( 'Submitted' ) )
{
    $input['statuses'] = $http->hasPostVariable( 'Statuses' ) && is_array( $http->postVariable( 'Statuses' ) )
                         ? $http->postVariable( 'Statuses' ) : array();

    $input['parts'] = $http->hasPostVariable( 'Parts' ) && is_array( $http->postVariable( 'Parts' ) )
                      ? $http->postVariable( 'Parts' ) : array();
}

// The settings rows arrive as parallel lists, one entry per row of the form.
$attributes = array();
if ( $http->hasPostVariable( 'AttributeName' ) && is_array( $http->postVariable( 'AttributeName' ) ) )
{
    $names   = $http->postVariable( 'AttributeName' );
    $labels  = $http->hasPostVariable( 'AttributeLabel' ) ? $http->postVariable( 'AttributeLabel' ) : array();
    $types   = $http->hasPostVariable( 'AttributeType' ) ? $http->postVariable( 'AttributeType' ) : array();
    $choices = $http->hasPostVariable( 'AttributeChoices' ) ? $http->postVariable( 'AttributeChoices' ) : array();
    $helps   = $http->hasPostVariable( 'AttributeHelp' ) ? $http->postVariable( 'AttributeHelp' ) : array();

    foreach ( $names as $index => $name )
    {
        if ( !is_scalar( $name ) || trim( $name ) === '' )
            continue;

        $attributes[] = array(
            'name'    => $name,
            'label'   => isset( $labels[$index] ) && is_scalar( $labels[$index] ) ? $labels[$index] : '',
            'type'    => isset( $types[$index] ) && is_scalar( $types[$index] ) ? $types[$index] : 'text',
            'choices' => isset( $choices[$index] ) && is_scalar( $choices[$index] ) ? $choices[$index] : '',
            'help'    => isset( $helps[$index] ) && is_scalar( $helps[$index] ) ? $helps[$index] : '' );
    }
}
$input['attributes'] = $attributes;

$settings = expWorkflowEventWizard::settings( $input );
$feedback = array();
$written  = array();

// ── An archive ──────────────────────────────────────────────────────────────
if ( $http->hasPostVariable( 'DownloadButton' ) )
{
    $archive = expWorkflowEventWizard::archive( $settings );

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

// ── Writing it ──────────────────────────────────────────────────────────────
if ( $http->hasPostVariable( 'CreateButton' ) )
{
    $result     = expWorkflowEventWizard::write( $settings );
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
    $written    = $result['written'];
}

$problems = expWorkflowEventWizard::problems( $settings );
$files    = expWorkflowEventWizard::files( $settings );

$preview = array();
foreach ( $files as $path => $contents )
    $preview[] = array( 'path'  => $path,
                        'bytes' => strlen( $contents ),
                        'lines' => $contents === '' ? 0 : substr_count( $contents, "\n" ) + 1,
                        'contents' => $contents );

// The trigger matrix, with what was ticked marked, so the form comes back the
// way it was left.
$chosen = array();
foreach ( $settings['triggers'] as $module => $operations )
    foreach ( $operations as $operation => $points )
        foreach ( $points as $point )
            $chosen[$module . '/' . $operation . '/' . $point] = true;

$matrix = array();
foreach ( expWorkflowEventWizard::triggers() as $module => $operations )
{
    $rows = array();
    foreach ( $operations as $operation => $points )
    {
        $cells = array();
        foreach ( array( 'before', 'after' ) as $point )
            $cells[$point] = array( 'available' => in_array( $point, $points, true ),
                                    'key'       => $module . '/' . $operation . '/' . $point,
                                    'chosen'    => isset( $chosen[$module . '/' . $operation . '/' . $point] ) );

        $rows[] = array( 'operation' => $operation, 'cells' => $cells );
    }

    $matrix[] = array( 'module' => $module, 'rows' => $rows, 'count' => count( $rows ) );
}

// The statuses, with what each one makes happen and whether it was ticked.
$statuses = array();
foreach ( expWorkflowEventWizard::statuses() as $key => $status )
    $statuses[] = array_merge( $status, array(
        'key'    => $key,
        'chosen' => in_array( $key, $settings['statuses'], true ),
        'fixed'  => $key === 'STATUS_ACCEPTED' ) );

// The settings rows, with at least one empty row to type into, and what is
// left of the nine columns an event has.
$rows = $settings['attributes'];
$usedInts  = 0;
$usedTexts = 0;
foreach ( $rows as $row )
    if ( $row['storage'] === 'int' ) $usedInts++; else $usedTexts++;

$tpl->setVariable( 'wizard_settings', $settings );
$tpl->setVariable( 'wizard_parts', expWorkflowEventWizard::parts() );
$tpl->setVariable( 'wizard_licences', expWorkflowEventWizard::licences() );
$tpl->setVariable( 'wizard_matrix', $matrix );
$tpl->setVariable( 'wizard_statuses', $statuses );
$tpl->setVariable( 'wizard_attribute_types', expWorkflowEventWizard::attributeTypes() );
$tpl->setVariable( 'wizard_attributes', $rows );
$tpl->setVariable( 'wizard_ints_left', 4 - $usedInts );
$tpl->setVariable( 'wizard_texts_left', 5 - $usedTexts );
$tpl->setVariable( 'wizard_trigger_sentences', expWorkflowEventWizard::triggerSentences( $settings ) );
$tpl->setVariable( 'wizard_class', expWorkflowEventWizard::className( $settings ) );
$tpl->setVariable( 'wizard_problems', $problems );
$tpl->setVariable( 'wizard_feedback', $feedback );
$tpl->setVariable( 'wizard_files', $preview );
$tpl->setVariable( 'wizard_file_count', count( $files ) );
$tpl->setVariable( 'wizard_written', $written );
$tpl->setVariable( 'wizard_can_write', expWorkflowEventWizard::canWrite() ? 1 : 0 );
$tpl->setVariable( 'wizard_can_archive', class_exists( 'ZipArchive' ) ? 1 : 0 );
$tpl->setVariable( 'wizard_ready', ( count( $files ) > 0 && count( $problems ) === 0
                                     && expWorkflowEventWizard::canWrite() ) ? 1 : 0 );
$tpl->setVariable( 'wizard_activation', expWorkflowEventWizard::activation( $settings ) );
$tpl->setVariable( 'wizard_target', 'extension/' . $settings['name'] );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/workflowevent.tpl' );
$Result['path'] = array( array( 'url' => 'setup/rad',
                                'text' => ezpI18n::tr( 'kernel/setup', 'Rapid Application Development' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Workflow event wizard' ) ) );
