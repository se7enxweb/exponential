<?php
/**
 * The fields a mutation of the GraphQL API writes (FieldInput: identifier, value, format, upload), checked against
 * the class as the schema shows it (expGraphQLClassCatalogue) before anything is written:
 *
 *   - the identifier is an attribute of the class the schema shows: a hidden field (HiddenFieldList[]) or datatype
 *     (HiddenDatatypeList[]: the user account, the password) cannot be written either;
 *   - a field of a file (ezimage, ezbinaryfile, ezmedia and FileDatatypeList[]) takes an upload, never a value: a
 *     path in a value would let a client import any file of the server;
 *   - every other field takes a value in the string format of its datatype (the format of the CSV import and of
 *     eZContentObjectAttribute::fromString()); a rich text field (ezxmltext) also takes plain text (format TEXT)
 *     or simple HTML (format HTML);
 *   - a new object gets every required attribute.
 *
 * Needs no database: the checks and the conversion of text and HTML are tested without one.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLFieldInput
{
    /** The datatypes whose value is a file. */
    const FILE_DATATYPES = array( 'ezimage', 'ezbinaryfile', 'ezmedia' );

    /** The datatypes of rich text that take the formats TEXT and HTML. */
    const RICH_TEXT_DATATYPES = array( 'ezxmltext' );

    /** The formats of a value. */
    const FORMAT_RAW = 'RAW';
    const FORMAT_TEXT = 'TEXT';
    const FORMAT_HTML = 'HTML';

    /** The start of a rich text document of the kernel. */
    const XML_START = '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">';

    /**
     * @return string[] the datatypes whose value is a file (FILE_DATATYPES and graphql.ini FileDatatypeList[])
     */
    public static function fileDatatypes()
    {
        return array_values( array_unique( array_merge( self::FILE_DATATYPES, expGraphQL::listSetting( 'FileDatatypeList' ) ) ) );
    }

    /**
     * Checks the fields of a mutation against a class.
     *
     * @param array $class a class of expGraphQLClassCatalogue (identifier, attributes)
     * @param array $inputs FieldInput values: identifier, value, format, upload
     * @param bool $creating a new object: every required attribute must be given
     * @return array attribute identifier => datatype, value (string|null), format, upload (expGraphQLUpload|null)
     * @throws expGraphQLError BAD_USER_INPUT
     */
    public static function prepare( array $class, array $inputs, $creating )
    {
        $attributes = array();
        foreach ( $class['attributes'] as $attribute )
            $attributes[$attribute['identifier']] = $attribute;
        $files = self::fileDatatypes();
        $prepared = array();
        foreach ( $inputs as $input )
        {
            $identifier = isset( $input['identifier'] ) ? (string)$input['identifier'] : '';
            if ( !isset( $attributes[$identifier] ) )
                throw new expGraphQLError( expGraphQLMessage::tr( 'The class %class has no field %field that may be written.',
                                                                  array( '%class' => $class['identifier'], '%field' => $identifier ) ), expGraphQLError::BAD_USER_INPUT );
            if ( isset( $prepared[$identifier] ) )
                throw new expGraphQLError( expGraphQLMessage::tr( 'The field %field is given twice.', array( '%field' => $identifier ) ), expGraphQLError::BAD_USER_INPUT );
            $datatype = (string)$attributes[$identifier]['datatype'];
            $upload = isset( $input['upload'] ) && $input['upload'] instanceof expGraphQLUpload ? $input['upload'] : null;
            $value = isset( $input['value'] ) ? (string)$input['value'] : null;
            $format = isset( $input['format'] ) && is_string( $input['format'] ) ? $input['format'] : self::FORMAT_RAW;
            if ( in_array( $datatype, $files, true ) )
            {
                if ( $upload === null )
                    throw new expGraphQLError( expGraphQLMessage::tr( 'The field %field takes a file: send it as upload (a multipart request).', array( '%field' => $identifier ) ),
                                               expGraphQLError::BAD_USER_INPUT );
                // the value of a file field is its alternative text (ezimage) or its file name (ezbinaryfile)
            }
            else
            {
                if ( $upload !== null )
                    throw new expGraphQLError( expGraphQLMessage::tr( 'The field %field takes no file.', array( '%field' => $identifier ) ), expGraphQLError::BAD_USER_INPUT );
                if ( $value === null )
                    throw new expGraphQLError( expGraphQLMessage::tr( 'The field %field needs a value.', array( '%field' => $identifier ) ), expGraphQLError::BAD_USER_INPUT );
                if ( $format !== self::FORMAT_RAW && !in_array( $datatype, self::RICH_TEXT_DATATYPES, true ) )
                    throw new expGraphQLError( expGraphQLMessage::tr( 'The format %format is only for rich text fields; %field takes its value as it is (RAW).',
                                                                      array( '%format' => $format, '%field' => $identifier ) ), expGraphQLError::BAD_USER_INPUT );
            }
            $prepared[$identifier] = array( 'datatype' => $datatype, 'value' => $value, 'format' => $format, 'upload' => $upload );
        }
        if ( $creating )
        {
            foreach ( $attributes as $identifier => $attribute )
            {
                if ( !empty( $attribute['required'] ) && !isset( $prepared[$identifier] ) )
                    throw new expGraphQLError( expGraphQLMessage::tr( 'The field %field is required.', array( '%field' => $identifier ) ), expGraphQLError::BAD_USER_INPUT );
            }
        }
        return $prepared;
    }

    /**
     * The string a prepared field is written with (eZContentObjectAttribute::fromString()).
     *
     * @param array $field of prepare()
     * @param string|null $filePath where its upload was put (a file field)
     * @param int $objectId the object (links in HTML)
     * @return string
     */
    public static function stringOf( array $field, $filePath, $objectId )
    {
        if ( $field['upload'] instanceof expGraphQLUpload )
        {
            $extra = $field['value'] !== null ? str_replace( '|', ' ', $field['value'] ) : '';
            if ( $field['datatype'] === 'ezbinaryfile' && $extra === '' )
                $extra = $field['upload']->safeName();
            return (string)$filePath . ( $extra !== '' ? '|' . $extra : '' );
        }
        if ( $field['format'] === self::FORMAT_TEXT )
            return self::textToXml( (string)$field['value'] );
        if ( $field['format'] === self::FORMAT_HTML )
            return self::htmlToXml( (string)$field['value'], $objectId );
        return (string)$field['value'];
    }

    /**
     * Plain text as a rich text document: a paragraph per block of text (blocks are parted by an empty line).
     *
     * @param string $text
     * @return string
     */
    public static function textToXml( $text )
    {
        $xml = self::XML_START;
        foreach ( preg_split( '/\R\s*\R/u', trim( str_replace( "\r", '', (string)$text ) ) ) as $block )
        {
            $block = trim( preg_replace( '/\s+/u', ' ', $block ) );
            if ( $block !== '' )
                $xml .= '<paragraph>' . htmlspecialchars( $block, ENT_XML1 | ENT_QUOTES, 'UTF-8' ) . '</paragraph>';
        }
        return $xml . '</section>';
    }

    /**
     * Simple HTML (p, h1-h6, strong, em, a, ul, ol, li, table ...) as a rich text document, through the kernel's
     * parser of the simplified input (as the RSS import does); markup it does not know is dropped.
     *
     * @param string $html
     * @param int $objectId
     * @return string
     * @throws expGraphQLError BAD_USER_INPUT when the HTML cannot be read
     */
    public static function htmlToXml( $html, $objectId )
    {
        $parser = new eZSimplifiedXMLInputParser( (int)$objectId, false, 0, false );
        $document = $parser->process( str_replace( array( "\r", "\n", "\t" ), array( '', ' ', ' ' ), (string)$html ) );
        if ( !is_object( $document ) )
            throw new expGraphQLError( expGraphQLMessage::tr( 'The HTML could not be read.' ), expGraphQLError::BAD_USER_INPUT );
        return (string)eZXMLTextType::domString( $document );
    }

    /**
     * What the audit records of the fields: their identifiers only, never a value (it may be personal data).
     *
     * @param array $prepared
     * @return string[]
     */
    public static function auditList( array $prepared )
    {
        return array_keys( $prepared );
    }
}

?>
