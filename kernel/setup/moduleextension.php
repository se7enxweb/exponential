<?php
/**
 * File containing the module extension wizard view.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$Module = $Params['Module'];

require_once 'kernel/setup/expmoduleextensionwizard.php';

$http = eZHTTPTool::instance();
$tpl  = eZTemplate::factory();

$input = array();
foreach ( array( 'name', 'module', 'prefix', 'title', 'summary', 'author', 'vendor', 'version',
                 'licence', 'source', 'db_type', 'db_server', 'db_port', 'db_name', 'db_file',
                 'db_user', 'db_password', 'db_sample' ) as $field )
{
    if ( $http->hasPostVariable( $field ) && is_scalar( $http->postVariable( $field ) ) )
        $input[$field] = $http->postVariable( $field );
}

if ( $http->hasPostVariable( 'Tables' ) )
    $input['tables'] = $http->postVariable( 'Tables' );

if ( $http->hasPostVariable( 'Submitted' ) )
    $input['parts'] = $http->hasPostVariable( 'Parts' ) && is_array( $http->postVariable( 'Parts' ) )
                      ? $http->postVariable( 'Parts' )
                      : array();

$settings = expModuleExtensionWizard::settings( $input );
$feedback = array();
$written  = array();

// ── The database, and the tables it can see ─────────────────────────────────
//
// Read on every request rather than remembered between them: a page that shows
// a table list from a connection it no longer has is a page that lies.
$connection = expModuleExtensionWizard::connection( $settings );
$connection['sample'] = $settings['db_sample'];
$schema     = expModuleExtensionWizard::schema( $connection );

// A document store holds collections rather than tables, and the page says so
// rather than calling them the same thing.
$capabilities   = expModuleExtensionWizard::databaseCapabilities();
$connectionKind = $settings['source'] === 'external' && isset( $capabilities[$settings['db_type']] )
                  ? $capabilities[$settings['db_type']]['kind']
                  : 'relational';

if ( $http->hasPostVariable( 'ConnectButton' ) )
    $feedback[] = array( 'ok' => $connection['ok'], 'message' => $connection['message'] );
else if ( !$connection['ok'] && $settings['source'] === 'external' )
    $feedback[] = array( 'ok' => false, 'message' => $connection['message'] );

// A table that has gone since the form was drawn is dropped rather than carried
// into the generator, where it would simply produce nothing.
$settings['tables'] = array_values( array_intersect( $settings['tables'], array_keys( $schema ) ) );

// ── Hand over an archive ────────────────────────────────────────────────────
if ( $http->hasPostVariable( 'DownloadButton' ) )
{
    $archive = expModuleExtensionWizard::archive( $settings );

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
    $result     = expModuleExtensionWizard::write( $settings );
    $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
    $written    = $result['written'];
}

$problems = expModuleExtensionWizard::problems( $settings );
$files    = count( $problems ) === 0 || count( $settings['tables'] )
            ? expModuleExtensionWizard::files( $settings )
            : array();

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

// What each chosen table became, so the page can show the mapping rather than
// only the files it produced.
$mapping = array();
foreach ( $settings['tables'] as $table )
{
    if ( !isset( $schema[$table] ) )
        continue;

    $columns = expModuleExtensionWizard::columns( $schema[$table] );
    $mapping[] = array( 'table'     => $table,
                        'class'     => expModuleExtensionWizard::className( $settings, $table ),
                        'keys'      => implode( ', ', expModuleExtensionWizard::keys( $columns ) ),
                        'increment' => expModuleExtensionWizard::incrementKey( $columns ),
                        'columns'   => $columns );
}

$tpl->setVariable( 'wizard_settings', $settings );
$tpl->setVariable( 'wizard_parts', expModuleExtensionWizard::parts() );
$tpl->setVariable( 'wizard_licences', expModuleExtensionWizard::licences() );
$tpl->setVariable( 'wizard_sources', expModuleExtensionWizard::sources() );
$tpl->setVariable( 'wizard_db_types', expModuleExtensionWizard::databaseTypeLabels() );
$tpl->setVariable( 'wizard_db_capabilities', expModuleExtensionWizard::databaseCapabilities() );
$tpl->setVariable( 'wizard_fields', expModuleExtensionWizard::fieldsFor( $settings['db_type'] ) );
$tpl->setVariable( 'wizard_sqlite_files', expModuleExtensionWizard::sqliteCandidates() );
$tpl->setVariable( 'wizard_via', isset( $connection['via'] ) ? $connection['via'] : '' );
$tpl->setVariable( 'wizard_kind', $connectionKind );
$tpl->setVariable( 'wizard_connected', $connection['ok'] ? 1 : 0 );
$tpl->setVariable( 'wizard_connection_message', $connection['message'] );
$tpl->setVariable( 'wizard_tables', expModuleExtensionWizard::summaries(
                       $connection['ok'] ? $connection['db'] : false, $schema, $settings['tables'] ) );
$tpl->setVariable( 'wizard_table_count', count( $schema ) );
$tpl->setVariable( 'wizard_mapping', $mapping );
$tpl->setVariable( 'wizard_problems', $problems );
$tpl->setVariable( 'wizard_feedback', $feedback );
$tpl->setVariable( 'wizard_files', $preview );
$tpl->setVariable( 'wizard_file_count', count( $files ) );
$tpl->setVariable( 'wizard_written', $written );
$tpl->setVariable( 'wizard_can_write', expModuleExtensionWizard::canWrite() ? 1 : 0 );
$tpl->setVariable( 'wizard_can_archive', class_exists( 'ZipArchive' ) ? 1 : 0 );
$tpl->setVariable( 'wizard_ready', ( count( $files ) > 0
                                     && count( $problems ) === 0
                                     && expModuleExtensionWizard::canWrite() ) ? 1 : 0 );
$tpl->setVariable( 'wizard_activation', expModuleExtensionWizard::activation( $settings ) );
$tpl->setVariable( 'wizard_target', 'extension/' . $settings['name'] );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/moduleextension.tpl' );
$Result['path'] = array( array( 'url' => 'setup/rad',
                                'text' => ezpI18n::tr( 'kernel/setup', 'Rapid Application Development' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Module extension wizard' ) ) );
