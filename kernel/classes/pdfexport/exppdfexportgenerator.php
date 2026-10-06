<?php
/**
 * File containing the expPDFExportGenerator class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Generates the PDF of an export: into its stored file, or into a temporary file that is then sent and removed.
 *
 * The PDF is drawn by two templates, as it always was: design:node/view/pdf.tpl writes the definition of the
 * document for the source node (and, for a tree, its children of the chosen classes), and
 * design:node/view/execute_pdf.tpl turns it into a file with pdf(close). Both can be overridden as before; the
 * variables they get are unchanged (node, generate_toc, tree_traverse, class_array, show_frontpage, show_footer,
 * footer_text, intro_text, sub_intro_text, generate_file, filename, pdf_definition, pdf_root_template).
 *
 * What changed (6.0.15): the second template was handed a string where the template engine appends to an array,
 * which is a fatal error since PHP 8, after the file was written: generating a stored export wrote the file and then
 * failed, so the export was never stored. A PDF made on the fly was streamed from inside the template
 * (pdf(stream) echoes the whole file and calls cleanExit), without a file name and with ob_clean() on a buffer
 * that may not exist. It is now written to a temporary file and sent like a stored one, in chunks, with its name.
 * Guide: doc/guides/pdf-exports.md
 */
class expPDFExportGenerator
{
    /** The directory below the cache directory that holds a PDF made on the fly while it is sent */
    const STREAM_DIRECTORY = 'pdfexport-stream';

    /**
     * Why an export cannot be generated, as a key: 'no_source' (none chosen), 'source_missing' (the node is gone),
     * 'bad_name' (a stored export whose file name is not safe), or false when it can.
     *
     * @param int $sourceNodeID
     * @param bool $sourceExists
     * @param bool $stored generated once into a file
     * @param string $fileName
     * @return string|false
     */
    public static function problemOf( $sourceNodeID, $sourceExists, $stored, $fileName )
    {
        if ( (int)$sourceNodeID <= 0 )
            return 'no_source';
        if ( !$sourceExists )
            return 'source_missing';
        if ( $stored && !expPDFExportFile::isSafeName( $fileName ) )
            return 'bad_name';
        return false;
    }

    /**
     * Why $export cannot be generated (see problemOf()), or false when it can.
     *
     * @param eZPDFExport $export
     * @return string|false
     */
    public static function problem( $export )
    {
        $nodeID = (int)$export->attribute( 'source_node_id' );
        $node = $nodeID > 0 ? eZContentObjectTreeNode::fetch( $nodeID ) : null;
        return self::problemOf( $nodeID, $node instanceof eZContentObjectTreeNode,
                                (int)$export->attribute( 'status' ) === eZPDFExport::CREATE_ONCE,
                                (string)$export->attribute( 'pdf_filename' ) );
    }

    /**
     * Draws the PDF of $export into the file $path (through the cluster file handler).
     *
     * @param eZPDFExport $export
     * @param string $path
     * @return bool false when the source node is gone or nothing was written
     */
    public static function render( $export, $path )
    {
        $node = $export->attribute( 'source_node' );
        if ( !$node instanceof eZContentObjectTreeNode )
            return false;
        $object = $node->attribute( 'object' );
        if ( !$object instanceof eZContentObject )
            return false;

        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'node', $node );
        $tpl->setVariable( 'generate_toc', 1 );
        $tpl->setVariable( 'tree_traverse', $export->attribute( 'export_structure' ) == 'tree' ? 1 : 0 );
        $tpl->setVariable( 'class_array', self::classArray( $export->attribute( 'export_classes' ) ) );
        $tpl->setVariable( 'show_frontpage', $export->attribute( 'show_frontpage' ) );
        // The footer line, as this export wants it. Left empty the shipped wording is used; switched off there is
        // no line of text at all.
        $tpl->setVariable( 'show_footer', $export->attribute( 'show_footer' ) );
        $tpl->setVariable( 'footer_text', (string)$export->attribute( 'footer_text' ) );
        if ( $export->attribute( 'show_frontpage' ) == 1 )
        {
            $tpl->setVariable( 'intro_text', $export->attribute( 'intro_text' ) );
            $tpl->setVariable( 'sub_intro_text', $export->attribute( 'sub_text' ) );
        }
        $tpl->setVariable( 'generate_file', 1 );
        $tpl->setVariable( 'filename', $path );

        // The override keys of the source node, so that pdf.tpl can be overridden per class, node or section; the
        // keys that were set before are put back afterwards.
        $keys = array( array( 'object', $object->attribute( 'id' ) ),
                       array( 'node', $node->attribute( 'node_id' ) ),
                       array( 'parent_node', $node->attribute( 'parent_node_id' ) ),
                       array( 'class', $object->attribute( 'contentclass_id' ) ),
                       array( 'class_identifier', $object->attribute( 'class_identifier' ) ),
                       array( 'depth', $node->attribute( 'depth' ) ),
                       array( 'url_alias', $node->attribute( 'url_alias' ) ) );
        $res = eZTemplateDesignResource::instance();
        $previous = $res->keys();
        $res->setKeys( $keys );

        $textElements = array();
        $uri = 'design:node/view/pdf.tpl';
        $tpl->setVariable( 'pdf_root_template', 1 );
        eZTemplateIncludeFunction::handleInclude( $textElements, $uri, $tpl, '', '' );
        $definition = str_replace( array( ' ', "\r\n", "\t", "\n" ), '', implode( '', $textElements ) );
        $tpl->setVariable( 'pdf_definition', $definition );

        // An array, as the template engine appends to it: a string here is a fatal error since PHP 8.
        $textElements = array();
        $uri = 'design:node/view/execute_pdf.tpl';
        eZTemplateIncludeFunction::handleInclude( $textElements, $uri, $tpl, '', '' );

        foreach ( $keys as $key )
        {
            if ( array_key_exists( $key[0], $previous ) )
                $res->setKeys( array( array( $key[0], $previous[$key[0]] ) ) );
            else
                $res->removeKey( $key[0] );
        }

        return eZClusterFileHandler::instance( $path )->exists();
    }

    /**
     * Writes the stored file of $export anew. The old file is replaced only once the new one is complete.
     *
     * @param eZPDFExport $export
     * @return array ok (bool), problem (false or a key of problemOf(), or 'failed'), facts (of the file)
     */
    public static function generateFile( $export )
    {
        $name = (string)$export->attribute( 'pdf_filename' );
        $problem = self::problem( $export );
        if ( $problem === false && (int)$export->attribute( 'status' ) !== eZPDFExport::CREATE_ONCE )
            $problem = 'not_stored';
        if ( $problem !== false )
            return array( 'ok' => false, 'problem' => $problem, 'facts' => expPDFExportFile::facts( $name ) );

        $path = expPDFExportFile::path( $name );
        $partial = self::temporaryPath();
        $ok = self::render( $export, $partial );
        if ( $ok )
        {
            eZDir::mkdir( dirname( $path ), false, true );
            $ok = eZClusterFileHandler::instance( $partial )->move( $path ) !== false
                  && eZClusterFileHandler::instance( $path )->exists();
        }
        else
        {
            self::discard( $partial );
        }
        return array( 'ok' => $ok, 'problem' => $ok ? false : 'failed', 'facts' => expPDFExportFile::facts( $name ) );
    }

    /**
     * Generates $export and sends it as the whole answer, under its download name. The caller ends the request
     * with eZExecution::cleanExit() when this returns true, outside any catch block.
     *
     * @param eZPDFExport $export
     * @return string|true true when the PDF was sent, else why not (a key of problemOf(), or 'failed')
     */
    public static function stream( $export )
    {
        $problem = self::problemOf( (int)$export->attribute( 'source_node_id' ),
                                    $export->attribute( 'source_node' ) instanceof eZContentObjectTreeNode, false, '' );
        if ( $problem !== false )
            return $problem;
        $path = self::temporaryPath();
        if ( !self::render( $export, $path ) )
        {
            self::discard( $path );
            return 'failed';
        }
        $sent = expPDFExportFile::send( $path, expPDFExportFile::downloadName( (string)$export->attribute( 'pdf_filename' ),
                                                                              (string)$export->attribute( 'title' ) ) );
        self::discard( $path );
        return $sent ? true : 'failed';
    }

    /**
     * The classes a tree export includes below its source, as pdf.tpl compares them: class ids as strings.
     *
     * @param string $exportClasses "1:16:23"
     * @return string[]
     */
    public static function classArray( $exportClasses )
    {
        $ids = array();
        foreach ( explode( ':', (string)$exportClasses ) as $id )
        {
            $id = trim( $id );
            if ( $id !== '' && ctype_digit( $id ) && !in_array( $id, $ids, true ) )
                $ids[] = $id;
        }
        return $ids;
    }

    /**
     * A file name of its own in the cache directory, for a PDF that is being made.
     *
     * @return string
     */
    public static function temporaryPath()
    {
        return eZSys::cacheDirectory() . '/' . self::STREAM_DIRECTORY . '/' . bin2hex( random_bytes( 12 ) ) . '.pdf';
    }

    /**
     * Removes a temporary file, when it is there.
     *
     * @param string $path
     */
    public static function discard( $path )
    {
        $file = eZClusterFileHandler::instance( $path );
        if ( $file->exists() )
            $file->delete();
    }
}

?>
