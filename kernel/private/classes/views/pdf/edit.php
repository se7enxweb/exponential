<?php
/**
 * The code of kernel/pdf/edit.php, moved into a class (#207 stage 1). The file kernel/pdf/edit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * pdf/edit/<id>            the form of an export (a draft of it while it is open); pdf/edit makes a new one
 * pdf/edit/<id>/generate   the PDF: made on the fly, or the stored file of an export generated once
 *
 * The export logic is in kernel/classes/pdfexport: the form is read and checked by expPDFExportForm, the PDF is
 * made by expPDFExportGenerator, the stored file is expPDFExportFile. Guide: doc/guides/pdf-exports.md
 */
/*
 * The original header of ./kernel/pdf/edit.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
if ( !function_exists( 'generatePDF' ) ) {
/*!
 \generate and output PDF data, either to file or stream

 Kept for code that calls it; the work is done by expPDFExportGenerator.

 \param PDF export object
 \param toFile, false if generate to stream, $
                filename if generate to file
*/
function generatePDF( $pdfExport, $toFile = false )
{
    if ( $pdfExport == null )
        return;

    if ( $toFile === false )
    {
        if ( expPDFExportGenerator::stream( $pdfExport ) === true )
            eZExecution::cleanExit();
        return;
    }
    expPDFExportGenerator::render( $pdfExport, $toFile );
}
}
}

namespace Exponential\View\Kernel\Pdf
{

class Edit extends \Exponential\Runnable\ModuleView
{
    /** The session variable that carries the result of a store or a generation over the redirect to the list. */
    const FEEDBACK_KEY = 'eZPDFListFeedback';

    /** The form fields, as the module hands them over (post_action_parameters of kernel/pdf/module.php). */
    const FIELDS = array( 'Title', 'DisplayFrontpage', 'IntroText', 'SubText', 'ShowFooter', 'FooterText', 'SourceNode',
                          'ExportType', 'ClassList', 'SiteAccess', 'DestinationType', 'DestinationFile' );

    /**
     * The submitted form, by field name, as expPDFExportForm::read() takes it: a tick box that was not ticked is left
     * out.
     *
     * @param \eZModule $module
     * @return array
     */
    public static function input( $module )
    {
        $input = array();
        foreach ( self::FIELDS as $field )
        {
            if ( $module->hasActionParameter( $field ) )
                $input[$field] = $module->actionParameter( $field );
        }
        return $input;
    }

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $exportID = isset( $Params['PDFExportID'] ) && ctype_digit( (string)$Params['PDFExportID'] ) ? (int)$Params['PDFExportID'] : 0;

        // ---- pdf/edit/<id>/generate: the PDF itself ----
        if ( isset( $Params['PDFGenerate'] ) && $Params['PDFGenerate'] == 'generate' )
        {
            $pdfExport = $exportID > 0 ? \eZPDFExport::fetch( $exportID ) : null;
            if ( !$pdfExport instanceof \eZPDFExport )
                return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

            $sent = false;
            if ( (int)$pdfExport->attribute( 'status' ) === \eZPDFExport::CREATE_ONFLY )
            {
                $sent = \expPDFExportGenerator::stream( $pdfExport );
            }
            else
            {
                // An export generated once answers with its stored file; it used to answer with an empty page.
                $path = \expPDFExportFile::path( (string)$pdfExport->attribute( 'pdf_filename' ) );
                $sent = $path !== false && \expPDFExportFile::send( $path, (string)$pdfExport->attribute( 'pdf_filename' ) )
                      ? true : 'not_generated';
            }
            if ( $sent === true )
            {
                // No catch block around this: under Velocity cleanExit() throws to end the request.
                \eZExecution::cleanExit();
            }
            $http->setSessionVariable( self::FEEDBACK_KEY, array( 'type' => 'generate_failed', 'id' => $exportID,
                                                                  'name' => (string)$pdfExport->attribute( 'title' ),
                                                                  'problem' => $sent ) );
            $Module->redirectTo( '/pdf/list' );
            return $this->viewResult( null, null );
        }

        $user = \eZUser::currentUser();
        $draftOther = false;

        if ( $exportID > 0 )
        {
            $pdfExport = \eZPDFExport::fetch( $exportID, true, \eZPDFExport::VERSION_DRAFT );

            if ( $pdfExport )
            {
                $contentIni = \eZINI::instance( 'content.ini' );
                $timeOut = (int)$contentIni->variable( 'PDFExportSettings', 'DraftTimeout' );
                if ( $pdfExport->attribute( 'modifier_id' ) != $user->attribute( 'contentobject_id' ) &&
                     $pdfExport->attribute( 'modified' ) + $timeOut > time() )
                {
                    // Somebody else has the export open. The form says so; saving takes over their draft.
                    $draftOther = array( 'name' => \expPDFExportInfo::userName( (int)$pdfExport->attribute( 'modifier_id' ) ),
                                         'modified' => (int)$pdfExport->attribute( 'modified' ) );
                }
                else if ( $timeOut > 0 && $pdfExport->attribute( 'modified' ) + $timeOut < time() )
                {
                    $pdfExport->remove();
                    $pdfExport = false;
                }
            }
            if ( !$pdfExport )
            {
                $pdfExport = \eZPDFExport::fetch( $exportID );
                if( !$pdfExport ) // user requested a non existent export
                {
                    return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
                }
                $pdfExport->setAttribute( 'version', \eZPDFExport::VERSION_DRAFT );
                $pdfExport->store();
            }
        }
        else
        {
            // Drafts of new exports that were left without Cancel and not touched for the draft timeout go first.
            \expPDFExportInfo::removeExpiredUnfinished( time() );
            $pdfExport = \eZPDFExport::create( $user->attribute( 'contentobject_id' ) );
            $pdfExport->store();
            // The address of the form is that of this export from now on, so a reload does not make another one.
            $Module->redirectTo( '/pdf/edit/' . (int)$pdfExport->attribute( 'id' ) );
            return $this->viewResult( null, null );
        }

        // Back from the browse page with a source node
        if ( $http->hasPostVariable( 'SelectedNodeIDArray' ) && !$http->hasPostVariable( 'BrowseCancelButton' ) )
        {
            $selected = \expPDFExportInfo::idList( $http->postVariable( 'SelectedNodeIDArray' ) );
            if ( $selected )
            {
                $pdfExport->setAttribute( 'source_node_id', $selected[0] );
                $pdfExport->store();
            }
        }

        $published = \eZPDFExport::fetch( $pdfExport->attribute( 'id' ) );
        $classArray = \eZContentClass::fetchList();
        usort( $classArray, function ( $a, $b ) { return strnatcasecmp( $a->attribute( 'name' ), $b->attribute( 'name' ) ); } );

        $validation = array();
        $errors = array();
        $inputValidated = true;

        if ( $Module->isCurrentAction( 'BrowseSource' ) || $Module->isCurrentAction( 'Export' ) )
        {
            $isExport = $Module->isCurrentAction( 'Export' );
            $classIDs = array();
            foreach ( $classArray as $class )
                $classIDs[] = (int)$class->attribute( 'id' );
            $exportForName = $pdfExport;
            $form = \expPDFExportForm::read( self::input( $Module ),
                                             array( 'export_classes' => $pdfExport->attribute( 'export_classes' ),
                                                    'pdf_filename' => $pdfExport->attribute( 'pdf_filename' ),
                                                    'source_node_id' => $pdfExport->attribute( 'source_node_id' ) ),
                                             array( 'check' => $isExport,
                                                    'class_ids' => $classIDs,
                                                    'node_exists' => function ( $nodeID )
                                                    {
                                                        return \eZContentObjectTreeNode::fetch( $nodeID ) instanceof \eZContentObjectTreeNode;
                                                    },
                                                    'name_taken' => function ( $name ) use ( $exportForName )
                                                    {
                                                        return $exportForName->countGeneratingOnceExports( $name ) - self::ownStoredRow( $exportForName, $name ) > 0;
                                                    } ) );
            // Browse keeps the source node the export has; the form's hidden field is only read by OK.
            if ( !$isExport )
                unset( $form['attributes']['source_node_id'] );
            foreach ( $form['attributes'] as $name => $value )
                $pdfExport->setAttribute( $name, $value );

            if ( $form['errors'] )
            {
                $inputValidated = false;
                $errors = \expPDFExportForm::messages( $form['errors'] );
                // The variable the form has always read for its messages
                $validation = array( 'processed' => true, 'placement' => array() );
                foreach ( $errors as $error )
                    $validation['placement'][] = array( 'text' => $error['message'] );
            }
            else
            {
                $pdfExport->store();
            }
        }

        if ( $Module->isCurrentAction( 'BrowseSource' ) )
        {
            \eZContentBrowse::browse( array( 'action_name' => 'ExportSourceBrowse',
                                            'description_template' => 'design:content/browse_export.tpl',
                                            'from_page' => '/pdf/edit/'. $pdfExport->attribute( 'id' ) ),
                                     $Module );
            return $this->viewResult( null, null );
        }
        else if ( $Module->isCurrentAction( 'Export' ) && $inputValidated )
        {
            // The old file goes when the export stops writing it: another name, or made on the fly from now on.
            if ( $published instanceof \eZPDFExport && (int)$published->attribute( 'status' ) === \eZPDFExport::CREATE_ONCE )
            {
                $oldName = (string)$published->attribute( 'pdf_filename' );
                if ( (int)$pdfExport->attribute( 'status' ) !== \eZPDFExport::CREATE_ONCE || $oldName !== (string)$pdfExport->attribute( 'pdf_filename' ) )
                    \expPDFExportFile::remove( $oldName );
            }

            $pdfExport->store( true );
            $feedback = array( 'type' => 'stored', 'id' => (int)$pdfExport->attribute( 'id' ),
                               'name' => (string)$pdfExport->attribute( 'title' ), 'is_new' => !$published );
            if ( (int)$pdfExport->attribute( 'status' ) === \eZPDFExport::CREATE_ONCE )
            {
                $stored = \eZPDFExport::fetch( $pdfExport->attribute( 'id' ) );
                $result = \expPDFExportGenerator::generateFile( $stored ? $stored : $pdfExport );
                $feedback['generated'] = $result['ok'];
                $feedback['problem'] = $result['problem'];
                $feedback['size'] = $result['facts']['size'];
            }
            $http->setSessionVariable( self::FEEDBACK_KEY, $feedback );
            $Module->redirectTo( '/pdf/list' );
            return $this->viewResult( null, null );
        }
        else if ( $Module->isCurrentAction( 'Discard' ) )
        {
            $pdfExport->remove();
            $Module->redirectTo( \eZRedirectManager::returnURI( $Module, '/pdf/list', \eZRedirectManager::formReturnURIs() ) );
            return $this->viewResult( null, null );
        }

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'set_warning', false );
        $tpl->setVariable( 'pdf_export', $pdfExport );
        $tpl->setVariable( 'export_type' , $pdfExport->attribute( 'status' ) );
        $tpl->setVariable( 'export_class_array', $classArray );
        $tpl->setVariable( 'pdfexport_list', \eZPDFExport::fetchList() );
        if ( !$inputValidated )
        {
            $tpl->setVariable( 'validation', $validation );
        }

        // What the redesigned form shows beyond the fields
        $source = false;
        $nodeID = (int)$pdfExport->attribute( 'source_node_id' );
        $node = $nodeID > 0 ? \eZContentObjectTreeNode::fetch( $nodeID ) : null;
        if ( $nodeID > 0 )
        {
            $source = array( 'node_id' => $nodeID, 'exists' => $node instanceof \eZContentObjectTreeNode );
            if ( $source['exists'] )
            {
                $section = \eZSection::fetch( (int)$node->attribute( 'object' )->attribute( 'section_id' ) );
                $source += array( 'name' => (string)$node->attribute( 'name' ),
                                  'url' => (string)$node->attribute( 'url_alias' ),
                                  'class_name' => (string)$node->attribute( 'class_name' ),
                                  'class_identifier' => (string)$node->attribute( 'class_identifier' ),
                                  'section' => $section ? (string)$section->attribute( 'name' ) : '',
                                  'children' => (int)$node->attribute( 'children_count' ),
                                  'path' => self::pathText( $node ) );
            }
        }
        $tpl->setVariable( 'pdf_source', $source );
        $tpl->setVariable( 'pdf_errors', $errors );
        $tpl->setVariable( 'pdf_is_new', !$published );
        $tpl->setVariable( 'pdf_draft_other', $draftOther );
        $tpl->setVariable( 'pdf_selected_classes', \expPDFExportGenerator::classArray( $pdfExport->attribute( 'export_classes' ) ) );
        $tpl->setVariable( 'pdf_published_file', $published instanceof \eZPDFExport && (int)$published->attribute( 'status' ) === \eZPDFExport::CREATE_ONCE
                                                 ? \expPDFExportFile::facts( (string)$published->attribute( 'pdf_filename' ) ) + array( 'name' => (string)$published->attribute( 'pdf_filename' ) )
                                                 : false );
        $tpl->setVariable( 'pdf_storage_directory', \expPDFExportFile::directory() );
        $tpl->setVariable( 'redirect_if_discarded', \eZRedirectManager::formReturnURI( $Module ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:pdf/edit.tpl' );
        $Result['path'] = array( array( 'url' => 'pdf/list',
                                        'text' => \ezpI18n::tr( 'kernel/pdf', 'PDF Export' ) ),
                                 array( 'url' => false,
                                        'text' => $published ? (string)$published->attribute( 'title' ) : \ezpI18n::tr( 'design/admin/pdf/edit', 'New PDF export' ) ) );

        return $this->viewResult( $Result, null );
    }

    /**
     * 1 when the stored row of $export itself writes $name, so that keeping one's own file name is not "taken".
     * countGeneratingOnceExports() with a name counts every stored export of that name, this one included.
     *
     * @param \eZPDFExport $export
     * @param string $name
     * @return int
     */
    public static function ownStoredRow( $export, $name )
    {
        $own = \eZPDFExport::fetch( $export->attribute( 'id' ) );
        return $own instanceof \eZPDFExport && (int)$own->attribute( 'status' ) === \eZPDFExport::CREATE_ONCE
               && (string)$own->attribute( 'pdf_filename' ) === (string)$name ? 1 : 0;
    }

    /**
     * Where a node is, as the names of its ancestors below the root ("Home / Recipes / Soups").
     *
     * @param \eZContentObjectTreeNode $node
     * @return string
     */
    public static function pathText( $node )
    {
        $names = array();
        foreach ( (array)$node->attribute( 'path' ) as $ancestor )
        {
            if ( (int)$ancestor->attribute( 'depth' ) >= 1 )
                $names[] = (string)$ancestor->attribute( 'name' );
        }
        $names[] = (string)$node->attribute( 'name' );
        return implode( ' / ', $names );
    }
}

}
