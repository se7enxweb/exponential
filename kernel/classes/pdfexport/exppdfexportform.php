<?php
/**
 * File containing the expPDFExportForm class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Reads and checks the form of pdf/edit. The field names are the ones the form has always sent: Title,
 * DisplayFrontpage, IntroText, SubText, ShowFooter, FooterText, SourceNode, ExportType, ClassList,
 * DestinationType, DestinationFile.
 *
 * read() works on plain values and reads nothing, so it is tested without a database. What it cannot know (whether
 * a node exists, which classes there are, whether another stored export writes the same file) is passed in.
 * Guide: doc/guides/pdf-exports.md
 */
class expPDFExportForm
{
    /** The longest title and footer line the table keeps */
    const MAX_TITLE = 255;

    /**
     * The values of the form, made into the export's attributes, with what is wrong with them.
     *
     * @param array $input the posted values by field name; a tick box that is not ticked is missing
     * @param array $current the export's current attributes: export_classes, pdf_filename, source_node_id
     * @param array $options 'check' (bool, default true): report errors, as OK does; Browse stores without;
     *                       'node_exists' (callable( int ) bool): whether a node exists;
     *                       'class_ids' (int[]|null): the classes there are, null to accept any id;
     *                       'name_taken' (callable( string ) bool): whether another stored export writes that file
     * @return array attributes (by attribute name), errors (field name => key of message()), file_name (as typed)
     */
    public static function read( array $input, array $current = array(), array $options = array() )
    {
        $options = array_merge( array( 'check' => true, 'node_exists' => null, 'class_ids' => null, 'name_taken' => null ), $options );
        $errors = array();
        $value = function ( $name ) use ( $input )
        {
            return isset( $input[$name] ) && is_string( $input[$name] ) ? $input[$name] : '';
        };

        $title = trim( $value( 'Title' ) );
        if ( $title === '' )
            $errors['Title'] = 'title_empty';
        elseif ( mb_strlen( $title ) > self::MAX_TITLE )
            $errors['Title'] = 'title_long';

        $footer = trim( $value( 'FooterText' ) );
        if ( mb_strlen( $footer ) > self::MAX_TITLE )
            $errors['FooterText'] = 'footer_long';

        $structure = $value( 'ExportType' ) === 'node' ? 'node' : 'tree';
        $classes = isset( $current['export_classes'] ) ? (string)$current['export_classes'] : '';
        if ( $structure === 'tree' )
        {
            $ids = array();
            foreach ( isset( $input['ClassList'] ) && is_array( $input['ClassList'] ) ? $input['ClassList'] : array() as $id )
            {
                if ( !is_string( $id ) && !is_int( $id ) ) continue;
                $id = (string)$id;
                if ( !ctype_digit( $id ) || (int)$id <= 0 ) continue;
                if ( is_array( $options['class_ids'] ) && !in_array( (int)$id, $options['class_ids'] ) ) continue;
                if ( !in_array( (string)(int)$id, $ids, true ) ) $ids[] = (string)(int)$id;
            }
            $classes = implode( ':', $ids );
            if ( !$ids )
                $errors['ClassList'] = 'classes_empty';
            elseif ( strlen( $classes ) > self::MAX_TITLE )
                $errors['ClassList'] = 'classes_long';
        }

        $stored = $value( 'DestinationType' ) !== 'download';
        $typed = trim( $value( 'DestinationFile' ) );
        $fileName = expPDFExportFile::normalizeName( $typed );
        if ( $fileName === false )
        {
            if ( $stored || $typed !== '' )
                $errors['DestinationFile'] = 'file_' . expPDFExportFile::nameProblem( $typed );
            // Kept as typed, so the form shows it again; never used as a path (expPDFExportFile::path() refuses it).
            $fileName = $typed === '' ? 'file.pdf' : basename( str_replace( '\\', '/', $typed ) );
        }
        elseif ( $stored && is_callable( $options['name_taken'] ) && call_user_func( $options['name_taken'], $fileName ) )
        {
            $errors['DestinationFile'] = 'file_taken';
        }

        $nodeRaw = $value( 'SourceNode' );
        $nodeID = ctype_digit( $nodeRaw ) ? (int)$nodeRaw : ( isset( $current['source_node_id'] ) ? (int)$current['source_node_id'] : 0 );
        if ( $nodeID <= 0 )
            $errors['SourceNode'] = 'source_empty';
        elseif ( is_callable( $options['node_exists'] ) && !call_user_func( $options['node_exists'], $nodeID ) )
            $errors['SourceNode'] = 'source_missing';

        $attributes = array(
            'title' => mb_substr( $title, 0, self::MAX_TITLE ),
            'show_frontpage' => isset( $input['DisplayFrontpage'] ) ? 1 : 0,
            'intro_text' => $value( 'IntroText' ),
            'sub_text' => $value( 'SubText' ),
            'show_footer' => isset( $input['ShowFooter'] ) ? 1 : 0,
            'footer_text' => mb_substr( $footer, 0, self::MAX_TITLE ),
            'export_structure' => $structure,
            'export_classes' => $classes,
            'pdf_filename' => mb_substr( $fileName, 0, self::MAX_TITLE ),
            'status' => $stored ? eZPDFExport::CREATE_ONCE : eZPDFExport::CREATE_ONFLY,
            'source_node_id' => $nodeID,
        );

        return array( 'attributes' => $attributes, 'errors' => $options['check'] ? $errors : array(), 'file_name' => $typed );
    }

    /**
     * The words for an error key of read().
     *
     * @param string $key
     * @return string
     */
    public static function message( $key )
    {
        $c = 'design/admin/pdf/edit';
        switch ( $key )
        {
            case 'title_empty':     return ezpI18n::tr( $c, 'Give the export a title.' );
            case 'title_long':      return ezpI18n::tr( $c, 'The title is longer than 255 characters.' );
            case 'footer_long':     return ezpI18n::tr( $c, 'The footer text is longer than 255 characters.' );
            case 'classes_empty':   return ezpI18n::tr( $c, 'Choose at least one class to include below the source node, or export the source node only.' );
            case 'classes_long':    return ezpI18n::tr( $c, 'Too many classes are chosen to be stored. Choose fewer classes.' );
            case 'file_empty':     return ezpI18n::tr( $c, 'A stored export needs a file name.' );
            case 'file_too_long':   return ezpI18n::tr( $c, 'The file name is longer than 100 characters.' );
            case 'file_path':       return ezpI18n::tr( $c, 'The file name must be a name only, without a folder, "..", or a leading dot.' );
            case 'file_characters': return ezpI18n::tr( $c, 'Use only letters, digits, dots, dashes and underscores in the file name.' );
            case 'file_taken':      return ezpI18n::tr( $c, 'Another stored export already writes a file of that name.' );
            case 'source_empty':    return ezpI18n::tr( $c, 'Choose the source node with Browse.' );
            case 'source_missing':  return ezpI18n::tr( $c, 'The chosen source node no longer exists. Choose another one with Browse.' );
        }
        return ezpI18n::tr( $c, 'This value is not valid.' );
    }

    /**
     * The errors of read() with their words, in the order of the form, for the template.
     *
     * @param array $errors field name => key
     * @return array[] field, message
     */
    public static function messages( array $errors )
    {
        $out = array();
        foreach ( array( 'Title', 'FooterText', 'SourceNode', 'ClassList', 'DestinationFile' ) as $field )
        {
            if ( isset( $errors[$field] ) )
                $out[$field] = array( 'field' => $field, 'message' => self::message( $errors[$field] ) );
        }
        return $out;
    }
}

?>
