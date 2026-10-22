<?php
/**
 * The setup/moduletable view: the module table wizard (expModuleTableWizard; doc/eng-us/features/6.0/data-tables.md,
 * "The module table wizard"). The fields come from a list typed here, a content class, an existing table or a field
 * source of an extension (/(source)/<id>/(ref)/<reference> prefills it, as a catalogue card of 7x SQLI Import links).
 * The files are shown before anything is written; then, on this page: write them (a new extension, or added to an
 * existing one), create the table on its connection, activate the extension and refresh the autoloads and caches.
 * Policy setup/setup.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Setup
{

class Moduletable extends \Exponential\Runnable\ModuleView
{
    const FIELDS = array( 'name', 'module', 'table', 'class', 'title', 'summary', 'author', 'vendor', 'version', 'licence', 'connection',
                          'source', 'ref', 'fields_text', 'content' );

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        require_once 'kernel/setup/expmoduletablewizard.php';
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';

        $input = array();
        foreach ( self::FIELDS as $field )
            if ( $http->hasPostVariable( $field ) && is_scalar( $http->postVariable( $field ) ) )
                $input[$field] = (string)$http->postVariable( $field );
        if ( !$isPost )
        {
            // a link from a catalogue card or a class page
            if ( isset( $Params['Source'] ) && is_string( $Params['Source'] ) )
                $input['source'] = $Params['Source'];
            if ( isset( $Params['Ref'] ) && is_scalar( $Params['Ref'] ) )
                $input['ref'] = rawurldecode( (string)$Params['Ref'] );
            if ( isset( $Params['ContentClass'] ) && is_string( $Params['ContentClass'] ) )
            {
                $input['source'] = 'class';
                $input['ref'] = $Params['ContentClass'];
            }
            if ( !isset( $input['fields_text'] ) )
                $input['fields_text'] = "title string required\nremote_id string(100) unique\nprice float\npublished datetime\nnotes text";
        }
        if ( $http->hasPostVariable( 'Submitted' ) )
        {
            $input['parts'] = $http->hasPostVariable( 'Parts' ) && is_array( $http->postVariable( 'Parts' ) ) ? $http->postVariable( 'Parts' ) : array();
            $input['stamps'] = $http->hasPostVariable( 'stamps' );
        }
        else
            $input['stamps'] = true;

        $settings = \expModuleTableWizard::settings( $input );
        $feedback = array();
        $written = array();

        // the fields of a source shown as the editable list ("Use these fields")
        if ( $isPost && $http->hasPostVariable( 'UseFieldsButton' ) && $settings['source'] !== 'list' )
        {
            $errors = array();
            $copy = $settings;
            $copy['content'] = 'none';
            $copy['stamps'] = false;
            $fields = \expModuleTableWizard::fields( $copy, $errors );
            if ( $fields )
            {
                $settings['fields_text'] = \expDataTableFieldSources::toList( $fields );
                $settings['source'] = 'list';
                $feedback[] = array( 'ok' => true, 'message' => \ezpI18n::tr( 'design/admin/datatable', 'The fields of %source are in the list now: change them as you need.', null, array( '%source' => $copy['source_label'] ) ) );
            }
            else
                $feedback[] = array( 'ok' => false, 'message' => implode( ' ', $errors ) );
        }

        $problems = \expModuleTableWizard::problems( $settings );
        $mayWrite = \expModuleTableWizard::canWrite();

        if ( $isPost && $http->hasPostVariable( 'DownloadButton' ) && !$problems )
        {
            $archive = \expModuleTableWizard::archive( $settings );
            if ( $archive['ok'] && is_file( $archive['path'] ) )
            {
                while ( ob_get_level() )
                    ob_end_clean();
                header( 'Content-Type: application/zip' );
                header( 'Content-Disposition: attachment; filename="' . $archive['filename'] . '"' );
                header( 'Content-Length: ' . filesize( $archive['path'] ) );
                readfile( $archive['path'] );
                @unlink( $archive['path'] );
                \eZExecution::cleanExit();
            }
            $feedback[] = array( 'ok' => false, 'message' => $archive['message'] );
        }
        if ( $isPost && $http->hasPostVariable( 'WriteButton' ) )
        {
            $result = \expModuleTableWizard::write( $settings );
            $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
            $written = $result['written'];
            if ( !empty( $result['skipped'] ) )
                $feedback[] = array( 'ok' => true, 'message' => \ezpI18n::tr( 'design/admin/datatable', 'Left as they are: %list', null, array( '%list' => implode( ', ', $result['skipped'] ) ) ) );
            $settings = \expModuleTableWizard::settings( $input );
        }
        if ( $isPost && $http->hasPostVariable( 'CreateTableButton' ) && !$problems )
        {
            $result = \expModuleTableWizard::createTable( $settings );
            $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
        }
        if ( $isPost && $http->hasPostVariable( 'ActivateButton' ) )
        {
            $result = \expModuleTableWizard::activate( $settings );
            $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
        }
        if ( $isPost && $http->hasPostVariable( 'RefreshButton' ) )
        {
            $result = \expModuleTableWizard::refresh();
            $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
        }

        $errors = array();
        $copy = $settings;
        $fields = \expModuleTableWizard::fields( $copy, $errors );
        $files = $problems ? array() : \expModuleTableWizard::files( $settings );
        $preview = array();
        $root = \expModuleTableWizard::extensionPath( $settings['name'] );
        foreach ( $files as $path => $contents )
            $preview[] = array( 'path' => $path, 'lines' => $contents === '' ? 0 : substr_count( $contents, "\n" ) + 1, 'contents' => $contents,
                                'exists' => $settings['existing'] && is_file( $root . '/' . $path ) );

        $sources = array( array( 'id' => 'list', 'name' => \ezpI18n::tr( 'design/admin/datatable', 'A list of fields typed here' ), 'refs' => array() ) );
        foreach ( \expDataTableFieldSources::all() as $id => $source )
        {
            try
            {
                $refs = method_exists( $source, 'dataTableReferences' ) ? (array)$source->dataTableReferences() : array();
            }
            catch ( \Throwable $e )
            {
                $refs = array();
            }
            $list = array();
            foreach ( $refs as $ref => $label )
                $list[] = array( 'ref' => (string)$ref, 'label' => (string)$label );
            $sources[] = array( 'id' => $id, 'name' => method_exists( $source, 'dataTableSourceName' ) ? (string)$source->dataTableSourceName() : $id, 'refs' => $list );
        }
        $connection = \expDataConnections::get( $settings['connection'] );
        $tableExists = null;
        try
        {
            if ( $settings['table'] !== '' && $connection !== null )
                $tableExists = \expKernelTableSchema::exists( $settings['table'], \expDataConnections::db( $settings['connection'] ), true );
        }
        catch ( \Throwable $e )
        {
            $tableExists = null;
        }
        $active = in_array( $settings['name'], \ezpActiveExtensions::current(), true );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'settings', $settings );
        $tpl->setVariable( 'fields', $fields );
        $tpl->setVariable( 'field_errors', $errors );
        $tpl->setVariable( 'source_label', $copy['source_label'] );
        $tpl->setVariable( 'sources', $sources );
        $tpl->setVariable( 'parts', \expModuleTableWizard::parts() );
        $tpl->setVariable( 'licences', \expModuleTableWizard::licences() );
        $tpl->setVariable( 'connections', array_values( \expDataConnections::all() ) );
        $tpl->setVariable( 'problems', \expModuleTableWizard::shownProblems( $problems, $http->hasPostVariable( 'Submitted' ) ) );
        $tpl->setVariable( 'feedback', $feedback );
        $tpl->setVariable( 'files', $preview );
        $tpl->setVariable( 'written', $written );
        $tpl->setVariable( 'can_write', $mayWrite );
        $tpl->setVariable( 'ready', !$problems && $files );
        $tpl->setVariable( 'extension_exists', is_dir( $root ) );
        $tpl->setVariable( 'extension_active', $active );
        $tpl->setVariable( 'table_exists', $tableExists );
        $tpl->setVariable( 'types', \expDataTableFieldSources::TYPES );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/moduletable.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/rad', 'text' => \ezpI18n::tr( 'kernel/setup', 'Rapid Application Development' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Module table wizard' ) ) );
        return $Result;
    }
}

}
?>
