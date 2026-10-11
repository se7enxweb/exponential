<?php
/**
 * File containing the expPackageJson class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The JSON serialization of a package (doc/bc/6.0/package-json-format.md).
 *
 * A package is the same model in either format: package.xml and every XML document of the package (the files of
 * its install items, and the object files a content object item names) are written as JSON documents of the same
 * name with ".json" instead of ".xml"; every other file (images, binary files, templates, settings, extension
 * files) is carried byte for byte. The XML of each document is mirrored element for element in JsonML
 * (http://www.jsonml.org/): an element is an array of its name, an optional object of its attributes (namespace
 * declarations included, as "xmlns:prefix") and its children; text is a string. Comments are ["#comment", text],
 * CDATA sections ["#cdata", text], processing instructions ["?target", data]. Rich text (the value of an attribute
 * whose datatype is in [PackageFormatSettings] RawXMLDatatypes[], ezxmltext by default) is kept as its XML string
 * in ["#xml", "<section ...>...</section>"].
 *
 * Because the mapping is the XML itself, no datatype or package handler needs to know about JSON: a JSON document
 * is turned back into the same DOM the XML file gives (eZPackage::fetchDOMFromFile() reads either), so a JSON
 * package installs through the same handlers, and XML -> JSON -> XML gives the same documents. White space only
 * text that contains a line break is formatting and is not part of the model.
 *
 * Every JSON document is an envelope:
 *   { "$schema": ..., "format": "exponential-package", "format-version": "1.0", "kind": "definition" | "document",
 *     "source": "<the XML file it mirrors>", "root": <JsonML>, "checksums": { "<path>": "<sha256>" } }
 * checksums only in package.json: the SHA-256 of every other file of the package.
 * The JSON Schema is share/package/schema/package-v1.json.
 */
class expPackageJson
{
    const FORMAT = 'exponential-package';
    const FORMAT_VERSION = '1.0';
    const SCHEMA_URL = 'https://raw.githubusercontent.com/se7enxweb/exponential/main/share/package/schema/package-v1.json';
    const SCHEMA_FILE = 'share/package/schema/package-v1.json';
    const DEFINITION_JSON = 'package.json';
    const DEFINITION_XML = 'package.xml';
    /** json_decode() depth: a document deeper than this is refused */
    const MAX_DEPTH = 512;

    /** @var array|null cached [PackageFormatSettings] RawXMLDatatypes[] */
    private static $rawDatatypes = null;

    /**
     * The datatypes whose value is kept as an XML string.
     * @return string[]
     */
    static function rawXMLDatatypes()
    {
        if ( self::$rawDatatypes === null )
        {
            $list = array( 'ezxmltext' );
            $ini = eZINI::instance( 'package.ini' );
            if ( $ini->hasVariable( 'PackageFormatSettings', 'RawXMLDatatypes' ) )
                $list = array_values( array_filter( (array)$ini->variable( 'PackageFormatSettings', 'RawXMLDatatypes' ) ) );
            self::$rawDatatypes = $list;
        }
        return self::$rawDatatypes;
    }

    /** Resets the cached settings (tests). */
    static function resetCache()
    {
        self::$rawDatatypes = null;
    }

    /**
     * The largest JSON document read, in bytes ([PackageFormatSettings] MaxDocumentBytes, 64 MB by default).
     * @return int
     */
    static function maxDocumentBytes()
    {
        $ini = eZINI::instance( 'package.ini' );
        if ( $ini->hasVariable( 'PackageFormatSettings', 'MaxDocumentBytes' ) )
            return max( 1024, (int)$ini->variable( 'PackageFormatSettings', 'MaxDocumentBytes' ) );
        return 67108864;
    }

    // ---------------------------------------------------------------------------------------------------------
    // XML -> JSON
    // ---------------------------------------------------------------------------------------------------------

    /**
     * Loads XML the way eZPackage::fetchDOMFromFile() does (formatting white space dropped, no network).
     * @param string $xml
     * @return DOMDocument|false
     */
    static function loadXML( $xml )
    {
        if ( !is_string( $xml ) || $xml === '' )
            return false;
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->preserveWhiteSpace = false;
        $previous = libxml_use_internal_errors( true );
        $ok = $dom->loadXML( $xml, LIBXML_NONET | LIBXML_PARSEHUGE );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        if ( !$ok || !$dom->documentElement || $dom->doctype !== null )
            return false;
        return $dom;
    }

    /**
     * The JsonML of an element and everything below it.
     * @param DOMElement $element
     * @param DOMXPath $xpath
     * @return array
     */
    static function elementToJsonML( DOMElement $element, DOMXPath $xpath )
    {
        $node = array( $element->nodeName );
        $attributes = array();
        // namespace declarations of this element itself, in the order they were written
        $declared = array();
        foreach ( $xpath->query( 'namespace::*', $element ) as $ns )
        {
            if ( $ns->prefix === 'xml' )
                continue;
            $name = ( $ns->prefix === '' || $ns->prefix === null ) ? 'xmlns' : 'xmlns:' . $ns->prefix;
            if ( $element->hasAttribute( $name ) )
                $declared[$name] = $ns->nodeValue;
        }
        foreach ( array_reverse( $declared, true ) as $name => $uri )
            $attributes[$name] = $uri;
        foreach ( $element->attributes as $attribute )
            $attributes[$attribute->nodeName] = $attribute->value;
        if ( $attributes )
            $node[] = $attributes;

        $raw = $element->localName === 'attribute' && $element->hasAttribute( 'type' ) &&
               in_array( $element->getAttribute( 'type' ), self::rawXMLDatatypes(), true );
        foreach ( $element->childNodes as $child )
        {
            switch ( $child->nodeType )
            {
                case XML_ELEMENT_NODE:
                    $node[] = $raw ? array( '#xml', $element->ownerDocument->saveXML( $child ) ) : self::elementToJsonML( $child, $xpath );
                    break;
                case XML_TEXT_NODE:
                    if ( self::isFormatting( $child->nodeValue ) )
                        break;
                    $node[] = $child->nodeValue;
                    break;
                case XML_CDATA_SECTION_NODE:
                    $node[] = array( '#cdata', $child->nodeValue );
                    break;
                case XML_COMMENT_NODE:
                    $node[] = array( '#comment', $child->nodeValue );
                    break;
                case XML_PI_NODE:
                    $node[] = array( '?' . $child->target, $child->data );
                    break;
            }
        }
        return $node;
    }

    /**
     * Whether a text is formatting: white space only, with a line break.
     * @param string $text
     * @return bool
     */
    static function isFormatting( $text )
    {
        return $text !== '' && trim( $text ) === '' && ( strpos( $text, "\n" ) !== false || strpos( $text, "\r" ) !== false );
    }

    /**
     * The JSON envelope of an XML document of a package.
     * @param DOMDocument $dom
     * @param string $source the XML file name (relative to the package directory) the document mirrors
     * @param array $checksums path => sha256 (package.json only)
     * @return array
     */
    static function envelope( DOMDocument $dom, $source, array $checksums = array() )
    {
        $kind = basename( $source ) === self::DEFINITION_XML && strpos( $source, '/' ) === false ? 'definition' : 'document';
        $data = array( '$schema' => self::SCHEMA_URL,
                       'format' => self::FORMAT,
                       'format-version' => self::FORMAT_VERSION,
                       'kind' => $kind,
                       'source' => $source,
                       'root' => self::elementToJsonML( $dom->documentElement, new DOMXPath( $dom ) ) );
        if ( $kind === 'definition' && $checksums )
        {
            ksort( $checksums, SORT_STRING );
            $data['checksums'] = $checksums;
        }
        return $data;
    }

    /**
     * The text of a JSON document: indented, slashes and Unicode as they are.
     * @param array $data
     * @return string
     */
    static function encode( array $data )
    {
        return self::encodeValue( $data, '' ) . "\n";
    }

    const JSON_FLAGS = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION;
    /** a value that fits into this many characters is written on one line */
    const LINE_WIDTH = 120;

    /**
     * Writes a value indented: whatever fits on a line (LINE_WIDTH) on one line; an element (JsonML) with its name
     * and attributes on its first line and one child per line; an object with one member per line.
     * @param mixed $value
     * @param string $indent
     * @return string
     */
    static function encodeValue( $value, $indent )
    {
        $compact = json_encode( $value, self::JSON_FLAGS );
        if ( !is_array( $value ) || $value === array() || strlen( $indent ) + strlen( $compact ) <= self::LINE_WIDTH )
            return $compact;
        // ["#xml", "..."], ["#comment", "..."]: one line however long
        if ( array_is_list( $value ) && isset( $value[0] ) && is_string( $value[0] ) && $value[0] !== '' && ( $value[0][0] === '#' || $value[0][0] === '?' ) )
            return $compact;
        $inner = $indent . '    ';
        if ( array_is_list( $value ) )
        {
            $parts = array();
            $head = '';
            $rest = $value;
            if ( is_string( $value[0] ) )
            {
                // an element: its name and attributes stay on the first line
                $head = json_encode( array_shift( $rest ), self::JSON_FLAGS );
                if ( $rest && is_array( $rest[0] ) && !array_is_list( $rest[0] ) )
                    $head .= ',' . json_encode( array_shift( $rest ), self::JSON_FLAGS );
                if ( !$rest )
                    return '[' . $head . ']';
            }
            foreach ( $rest as $item )
                $parts[] = $inner . self::encodeValue( $item, $inner );
            return '[' . $head . ( $head !== '' ? ',' : '' ) . "\n" . implode( ",\n", $parts ) . "\n" . $indent . ']';
        }
        $parts = array();
        foreach ( $value as $key => $item )
            $parts[] = $inner . json_encode( (string)$key, self::JSON_FLAGS ) . ': ' . self::encodeValue( $item, $inner );
        return "{\n" . implode( ",\n", $parts ) . "\n" . $indent . '}';
    }

    /**
     * The JSON text of an XML file.
     * @param string $xmlFile
     * @param string $source
     * @param array $checksums
     * @return string|false
     */
    static function xmlFileToJson( $xmlFile, $source, array $checksums = array() )
    {
        $dom = self::loadXML( is_file( $xmlFile ) ? (string)file_get_contents( $xmlFile ) : '' );
        if ( !$dom )
            return false;
        return self::encode( self::envelope( $dom, $source, $checksums ) );
    }

    // ---------------------------------------------------------------------------------------------------------
    // JSON -> XML
    // ---------------------------------------------------------------------------------------------------------

    /**
     * Decodes a JSON document and checks its envelope (not the schema, see validate()).
     * @param string $json
     * @param string|null $error set to the reason when false is returned
     * @return array|false
     */
    static function decode( $json, &$error = null )
    {
        $error = null;
        if ( !is_string( $json ) || $json === '' )
        {
            $error = 'empty';
            return false;
        }
        if ( strlen( $json ) > self::maxDocumentBytes() )
        {
            $error = 'too large';
            return false;
        }
        $data = json_decode( $json, true, self::MAX_DEPTH );
        if ( !is_array( $data ) )
        {
            $error = 'not JSON: ' . json_last_error_msg();
            return false;
        }
        if ( !isset( $data['format'] ) || $data['format'] !== self::FORMAT )
        {
            $error = 'not an Exponential package document (format)';
            return false;
        }
        if ( !isset( $data['format-version'] ) || !is_string( $data['format-version'] ) || strpos( $data['format-version'], '1.' ) !== 0 )
        {
            $error = 'unsupported format-version';
            return false;
        }
        if ( !isset( $data['root'] ) || !is_array( $data['root'] ) )
        {
            $error = 'no root element';
            return false;
        }
        return $data;
    }

    /**
     * Whether a name is an XML name (QName with at most one prefix).
     * @param string $name
     * @return bool
     */
    static function isXMLName( $name )
    {
        return is_string( $name ) && preg_match( '/^[A-Za-z_][A-Za-z0-9._\-]*(:[A-Za-z_][A-Za-z0-9._\-]*)?$/', $name ) === 1;
    }

    /**
     * Writes the XML of a JsonML node.
     * @param mixed $node
     * @param string|null $indent the indentation of the node, null inside mixed content (written as it is)
     * @param int $depth
     * @return string
     * @throws InvalidArgumentException when the node is not JsonML of this format
     */
    static function jsonMLToXML( $node, $indent = '', $depth = 0 )
    {
        if ( $depth > self::MAX_DEPTH )
            throw new InvalidArgumentException( 'nested too deep' );
        if ( is_string( $node ) )
            return self::escapeText( $node );
        if ( !is_array( $node ) || !isset( $node[0] ) || !is_string( $node[0] ) )
            throw new InvalidArgumentException( 'a node is neither a string nor an array starting with a name' );
        $name = $node[0];
        if ( $name === '' )
            throw new InvalidArgumentException( 'an element without a name' );
        if ( $name === '#comment' || $name === '#cdata' || $name === '#xml' || $name[0] === '?' )
        {
            $text = isset( $node[1] ) ? $node[1] : '';
            if ( !is_string( $text ) || count( $node ) > 2 )
                throw new InvalidArgumentException( "$name takes one string" );
            if ( $name === '#comment' )
            {
                if ( strpos( $text, '--' ) !== false || substr( $text, -1 ) === '-' )
                    throw new InvalidArgumentException( 'a comment with "--"' );
                return '<!--' . $text . '-->';
            }
            if ( $name === '#cdata' )
                return '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $text ) . ']]>';
            if ( $name === '#xml' )
            {
                if ( preg_match( '/<!DOCTYPE|<!ENTITY|<\?xml/i', $text ) )
                    throw new InvalidArgumentException( 'raw XML with a declaration, DOCTYPE or entity' );
                return $text;
            }
            $target = substr( $name, 1 );
            if ( !self::isXMLName( $target ) || strtolower( $target ) === 'xml' || strpos( $text, '?>' ) !== false )
                throw new InvalidArgumentException( 'an invalid processing instruction' );
            return '<?' . $target . ( $text !== '' ? ' ' . $text : '' ) . '?>';
        }
        if ( !self::isXMLName( $name ) )
            throw new InvalidArgumentException( 'an invalid element name: ' . substr( $name, 0, 60 ) );
        $xml = '<' . $name;
        $children = array_slice( $node, 1 );
        if ( $children && is_array( $children[0] ) && !array_is_list( $children[0] ) )
        {
            foreach ( array_shift( $children ) as $attributeName => $value )
            {
                if ( !self::isXMLName( (string)$attributeName ) )
                    throw new InvalidArgumentException( 'an invalid attribute name: ' . substr( (string)$attributeName, 0, 60 ) );
                if ( !is_string( $value ) )
                    throw new InvalidArgumentException( "the attribute $attributeName is not a string" );
                $xml .= ' ' . $attributeName . '="' . self::escapeAttribute( $value ) . '"';
            }
        }
        elseif ( $children && is_array( $children[0] ) && $children[0] === array() )
        {
            // an empty attribute object decodes as an empty array
            array_shift( $children );
        }
        if ( !$children )
            return $xml . '/>';
        $elementsOnly = $indent !== null;
        foreach ( $children as $child )
        {
            if ( !$elementsOnly )
                break;
            if ( !is_array( $child ) || ( isset( $child[0] ) && ( $child[0] === '#xml' || $child[0] === '#cdata' ) ) )
            {
                $elementsOnly = false;
                break;
            }
        }
        $xml .= '>';
        $childIndent = $indent . '  ';
        foreach ( $children as $child )
        {
            if ( $elementsOnly )
                $xml .= "\n" . $childIndent;
            $xml .= self::jsonMLToXML( $child, $elementsOnly ? $childIndent : null, $depth + 1 );
        }
        if ( $elementsOnly )
            $xml .= "\n" . $indent;
        return $xml . '</' . $name . '>';
    }

    /** @return string */
    static function escapeText( $text )
    {
        return str_replace( array( '&', '<', '>', "\r" ), array( '&amp;', '&lt;', '&gt;', '&#13;' ), $text );
    }

    /** @return string */
    static function escapeAttribute( $text )
    {
        return str_replace( array( '&', '<', '>', '"', "\t", "\n", "\r" ), array( '&amp;', '&lt;', '&gt;', '&quot;', '&#9;', '&#10;', '&#13;' ), $text );
    }

    /**
     * The XML text of a decoded JSON document.
     * @param array $data
     * @return string
     * @throws InvalidArgumentException
     */
    static function toXMLString( array $data )
    {
        $encoding = 'utf-8';
        return '<?xml version="1.0" encoding="' . $encoding . '"?>' . "\n" . self::jsonMLToXML( $data['root'] ) . "\n";
    }

    /**
     * The DOM of a JSON document, as eZPackage::fetchDOMFromFile() gives it for the XML file.
     * @param string $json
     * @param string|null $error
     * @return DOMDocument|false
     */
    static function toDOM( $json, &$error = null )
    {
        $data = self::decode( $json, $error );
        if ( !$data )
            return false;
        try
        {
            $xml = self::toXMLString( $data );
        }
        catch ( InvalidArgumentException $e )
        {
            $error = $e->getMessage();
            return false;
        }
        $dom = self::loadXML( $xml );
        if ( !$dom )
        {
            $error = 'the document does not give well-formed XML';
            return false;
        }
        return $dom;
    }

    /**
     * Reads a JSON document file into a DOM.
     * @param string $file
     * @param string|null $error
     * @return DOMDocument|false
     */
    static function fetchDOMFromFile( $file, &$error = null )
    {
        if ( !is_file( $file ) )
        {
            $error = 'no file';
            return false;
        }
        if ( filesize( $file ) > self::maxDocumentBytes() )
        {
            $error = 'too large';
            return false;
        }
        return self::toDOM( (string)file_get_contents( $file ), $error );
    }

    /**
     * Whether a file is a JSON package document, by its content (the first character is "{").
     * @param string $file
     * @return bool
     */
    static function isJSONFile( $file )
    {
        if ( !is_file( $file ) )
            return false;
        $handle = @fopen( $file, 'rb' );
        if ( !$handle )
            return false;
        $head = (string)fread( $handle, 64 );
        fclose( $handle );
        $head = ltrim( preg_replace( '/^\xEF\xBB\xBF/', '', $head ) );
        return $head !== '' && $head[0] === '{';
    }

    /**
     * The JSON name of an XML document name: "a/b.xml" -> "a/b.json".
     * @param string $path
     * @return string
     */
    static function jsonName( $path )
    {
        return preg_replace( '/\.xml$/i', '', $path ) . '.json';
    }

    /**
     * The XML name of a JSON document name.
     * @param string $path
     * @return string
     */
    static function xmlName( $path )
    {
        return preg_replace( '/\.json$/i', '', $path ) . '.xml';
    }

    // ---------------------------------------------------------------------------------------------------------
    // Package directories
    // ---------------------------------------------------------------------------------------------------------

    /**
     * The format of a package directory: 'xml' (package.xml), 'json' (package.json) or false.
     * @param string $dir
     * @return string|false
     */
    static function directoryFormat( $dir )
    {
        if ( is_file( $dir . '/' . self::DEFINITION_XML ) )
            return 'xml';
        if ( is_file( $dir . '/' . self::DEFINITION_JSON ) )
            return 'json';
        return false;
    }

    /**
     * Reads a DOM of a document of a package directory by its XML name, from the XML or the JSON file.
     * @param string $dir
     * @param string $xmlPath relative, ending in .xml
     * @return DOMDocument|false
     */
    static function documentDOM( $dir, $xmlPath )
    {
        $xml = $dir . '/' . $xmlPath;
        if ( is_file( $xml ) )
            return self::isJSONFile( $xml ) ? self::fetchDOMFromFile( $xml ) : self::loadXML( (string)file_get_contents( $xml ) );
        $json = $dir . '/' . self::jsonName( $xmlPath );
        if ( is_file( $json ) )
            return self::fetchDOMFromFile( $json );
        return false;
    }

    /**
     * The XML documents of a package directory, by their XML names (relative paths), whether they are written
     * as XML or as JSON: package.xml, the file of every install and uninstall item, and the object files a
     * content object item names (<object-file filename="..."/>, in the directory ezcontentobject).
     * @param string $dir
     * @param array $missing set to the documents the definition names that are in neither format
     * @return string[]
     */
    static function modelDocuments( $dir, &$missing = array() )
    {
        $missing = array();
        $definition = self::documentDOM( $dir, self::DEFINITION_XML );
        if ( !$definition )
            return array();
        $documents = array( self::DEFINITION_XML );
        $xpath = new DOMXPath( $definition );
        foreach ( $xpath->query( '/package/install/item | /package/uninstall/item' ) as $item )
        {
            $filename = $item->getAttribute( 'filename' );
            if ( $filename === '' )
                continue;
            $sub = $item->getAttribute( 'sub-directory' );
            $path = ( $sub !== '' ? $sub . '/' : '' ) . $filename . '.xml';
            if ( !self::isSafeRelativePath( $path ) || in_array( $path, $documents, true ) )
                continue;
            $dom = self::documentDOM( $dir, $path );
            if ( !$dom )
            {
                // an item of a handler that keeps no XML document of its own (the file is payload)
                if ( !is_file( $dir . '/' . $path ) && !is_file( $dir . '/' . self::jsonName( $path ) ) )
                    $missing[] = $path;
                continue;
            }
            $documents[] = $path;
            foreach ( $dom->getElementsByTagName( 'object-file' ) as $objectFile )
            {
                $objectPath = 'ezcontentobject/' . $objectFile->getAttribute( 'filename' );
                if ( !preg_match( '/\.xml$/i', $objectPath ) || !self::isSafeRelativePath( $objectPath ) || in_array( $objectPath, $documents, true ) )
                    continue;
                if ( is_file( $dir . '/' . $objectPath ) || is_file( $dir . '/' . self::jsonName( $objectPath ) ) )
                    $documents[] = $objectPath;
                else
                    $missing[] = $objectPath;
            }
        }
        return $documents;
    }

    /**
     * A relative path without "..", a leading slash, a back slash or control characters.
     * @param string $path
     * @return bool
     */
    static function isSafeRelativePath( $path )
    {
        return $path !== '' && $path[0] !== '/' && strpos( $path, '\\' ) === false && !preg_match( '/[\x00-\x1F]/', $path ) &&
               !in_array( '..', explode( '/', $path ), true );
    }

    /**
     * Every file of a directory, relative, sorted (the package cache directory .cache is left out).
     * @param string $dir
     * @return string[]
     */
    static function fileList( $dir )
    {
        $files = array();
        if ( !is_dir( $dir ) )
            return $files;
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $iterator as $file )
        {
            if ( !$file->isFile() || $file->isLink() )
                continue;
            $relative = substr( $file->getPathname(), strlen( $dir ) + 1 );
            if ( $relative === '.cache' || strpos( $relative, '.cache/' ) === 0 )
                continue;
            $files[] = str_replace( DIRECTORY_SEPARATOR, '/', $relative );
        }
        sort( $files, SORT_STRING );
        return $files;
    }

    /**
     * Converts a package directory into another directory in the format $to ('json' or 'xml'). The documents of
     * the model are converted, every other file is copied as it is; package.json gets the checksums of all other
     * files. $target must not exist yet or be empty.
     *
     * @param string $source
     * @param string $target
     * @param string $to 'json' or 'xml'
     * @return array report: ok (bool), documents (int), files (int), errors (string[])
     */
    static function convertDirectory( $source, $target, $to )
    {
        $report = array( 'ok' => false, 'from' => self::directoryFormat( $source ), 'to' => $to, 'documents' => 0, 'files' => 0, 'errors' => array() );
        if ( !in_array( $to, array( 'xml', 'json' ), true ) )
        {
            $report['errors'][] = 'unknown format ' . $to;
            return $report;
        }
        if ( !$report['from'] )
        {
            $report['errors'][] = 'no package.xml or package.json in ' . $source;
            return $report;
        }
        $documents = self::modelDocuments( $source, $missing );
        foreach ( $missing as $path )
            $report['errors'][] = 'a document the package names is missing: ' . $path;
        $documentFiles = array();
        foreach ( $documents as $path )
        {
            $documentFiles[$path] = true;
            $documentFiles[self::jsonName( $path )] = true;
        }
        if ( !is_dir( $target ) )
            eZDir::mkdir( $target, false, true );

        // payload first: package.json carries the checksums of every other file
        foreach ( self::fileList( $source ) as $file )
        {
            if ( isset( $documentFiles[$file] ) )
                continue;
            if ( !is_dir( dirname( $target . '/' . $file ) ) )
                eZDir::mkdir( dirname( $target . '/' . $file ), false, true );
            if ( !copy( $source . '/' . $file, $target . '/' . $file ) )
                $report['errors'][] = 'could not copy ' . $file;
            ++$report['files'];
        }
        foreach ( $documents as $path )
        {
            if ( $path === self::DEFINITION_XML )
                continue;
            $dom = self::documentDOM( $source, $path );
            if ( !$dom )
            {
                $report['errors'][] = 'could not read ' . $path;
                continue;
            }
            $out = $to === 'json' ? self::jsonName( $path ) : $path;
            if ( !is_dir( dirname( $target . '/' . $out ) ) )
                eZDir::mkdir( dirname( $target . '/' . $out ), false, true );
            $text = $to === 'json' ? self::encode( self::envelope( $dom, $path ) ) : self::domToXMLText( $dom );
            if ( file_put_contents( $target . '/' . $out, $text ) === false )
                $report['errors'][] = 'could not write ' . $out;
            ++$report['documents'];
        }
        $definition = self::documentDOM( $source, self::DEFINITION_XML );
        if ( !$definition )
            $report['errors'][] = 'could not read the package definition';
        else
        {
            if ( $to === 'json' )
                $text = self::encode( self::envelope( $definition, self::DEFINITION_XML, self::checksums( $target ) ) );
            else
                $text = self::domToXMLText( $definition );
            if ( file_put_contents( $target . '/' . ( $to === 'json' ? self::DEFINITION_JSON : self::DEFINITION_XML ), $text ) === false )
                $report['errors'][] = 'could not write the package definition';
            ++$report['documents'];
        }
        $report['ok'] = !$report['errors'];
        return $report;
    }

    /**
     * The XML text of a document as the package writers write it (indented).
     * @param DOMDocument $dom
     * @return string
     */
    static function domToXMLText( DOMDocument $dom )
    {
        // through the JsonML writer: the same indentation as a document converted from JSON, rich text untouched
        return self::toXMLString( array( 'root' => self::elementToJsonML( $dom->documentElement, new DOMXPath( $dom ) ) ) );
    }

    /**
     * The SHA-256 of every file of a package directory but package.json and package.xml.
     * @param string $dir
     * @return array path => sha256
     */
    static function checksums( $dir )
    {
        $sums = array();
        foreach ( self::fileList( $dir ) as $file )
        {
            if ( $file === self::DEFINITION_JSON || $file === self::DEFINITION_XML )
                continue;
            $sums[$file] = hash_file( 'sha256', $dir . '/' . $file );
        }
        return $sums;
    }

    /**
     * Extracts a package archive (.ezpkg: gzip compressed tar) into a directory, after the same checks
     * eZPackage::import() makes (only plain files and directories with safe relative paths).
     *
     * @param string $archivePath
     * @param string $dir created when missing
     * @param string|null $error
     * @return bool
     */
    static function extractArchive( $archivePath, $dir, &$error = null )
    {
        $error = null;
        if ( !is_file( $archivePath ) )
        {
            $error = 'no file ' . $archivePath;
            return false;
        }
        try
        {
            $archive = ezcArchive::open( "compress.zlib://$archivePath", eZPackage::archiveTarType( $archivePath ),
                                         new ezcArchiveOptions( array( 'readOnly' => true ) ) );
            $problem = eZPackageUploadInspector::entriesProblem( $archive );
            if ( $problem !== null )
            {
                $error = "the archive was refused ({$problem['code']}: {$problem['path']})";
                return false;
            }
            if ( !is_dir( $dir ) )
                eZDir::mkdir( $dir, false, true );
            $archive->extract( $dir );
        }
        catch ( Exception $e )
        {
            $error = 'the archive could not be read: ' . $e->getMessage();
            return false;
        }
        if ( !self::directoryFormat( $dir ) )
        {
            $error = 'the archive has no package.xml or package.json at its top';
            return false;
        }
        return true;
    }

    /**
     * A private working directory under the cache directory (removed by the caller with eZDir::recursiveDelete()).
     * @param string $label
     * @return string
     */
    static function workDirectory( $label )
    {
        $dir = eZDir::path( array( eZSys::cacheDirectory(), 'packages', 'convert-' . preg_replace( '/[^a-z0-9_-]/i', '', $label ) . '-' . bin2hex( random_bytes( 6 ) ) ) );
        eZDir::mkdir( $dir, false, true );
        return $dir;
    }

    /**
     * Converts a package (an .ezpkg archive or a package directory) into the format $to, written as an archive
     * (a target ending in .ezpkg, .tar.gz or .tgz) or a directory (any other target, which must not exist).
     *
     * @param string $source
     * @param string $target
     * @param string|false $to 'json', 'xml' or false (the other format than the source's)
     * @return array report of convertDirectory() plus target
     */
    static function convert( $source, $target, $to = false )
    {
        $report = array( 'ok' => false, 'from' => false, 'to' => $to, 'documents' => 0, 'files' => 0, 'errors' => array(), 'target' => $target );
        $work = self::workDirectory( 'cli' );
        try
        {
            if ( is_dir( $source ) )
                $sourceDir = rtrim( $source, '/' );
            else
            {
                $sourceDir = $work . '/in';
                if ( !self::extractArchive( $source, $sourceDir, $error ) )
                {
                    $report['errors'][] = $error;
                    return $report;
                }
            }
            $from = self::directoryFormat( $sourceDir );
            if ( !$from )
            {
                $report['errors'][] = 'no package.xml or package.json in ' . $source;
                return $report;
            }
            if ( $to === false )
                $to = $from === 'json' ? 'xml' : 'json';
            $toArchive = preg_match( '/\.(ezpkg|tar\.gz|tgz)$/i', $target ) === 1;
            if ( !$toArchive && file_exists( $target ) )
            {
                $report['errors'][] = 'the target directory exists: ' . $target;
                return $report;
            }
            $outDir = $toArchive ? $work . '/out' : $target;
            $report = array_merge( $report, self::convertDirectory( $sourceDir, $outDir, $to ) );
            $report['target'] = $target;
            if ( $report['ok'] && $toArchive )
            {
                if ( !eZPackage::archiveDirectory( $outDir, $target, 'tar.gz' ) || !is_file( $target ) )
                {
                    $report['errors'][] = 'could not write ' . $target;
                    $report['ok'] = false;
                }
            }
            return $report;
        }
        finally
        {
            eZDir::recursiveDelete( $work );
        }
    }

    // ---------------------------------------------------------------------------------------------------------
    // Validation and equivalence
    // ---------------------------------------------------------------------------------------------------------

    /**
     * The JSON Schema of the format, decoded.
     * @return array
     */
    static function schema()
    {
        static $schema = null;
        if ( $schema === null )
        {
            $file = self::SCHEMA_FILE;
            if ( !is_file( $file ) )
                $file = __DIR__ . '/../../' . self::SCHEMA_FILE;
            $schema = is_file( $file ) ? json_decode( (string)file_get_contents( $file ), true ) : array();
        }
        return is_array( $schema ) ? $schema : array();
    }

    /**
     * Validates a package directory in either format: every JSON document against the schema and as XML, the
     * definition as the kernel reads it, every document the definition names, and the checksums of package.json.
     *
     * @param string $dir
     * @return array errors (string[]), warnings (string[]), format, documents (int), files (int), name, valid (bool)
     */
    static function validateDirectory( $dir )
    {
        $report = array( 'valid' => false, 'format' => self::directoryFormat( $dir ), 'name' => '', 'documents' => 0, 'files' => 0,
                         'errors' => array(), 'warnings' => array() );
        if ( !$report['format'] )
        {
            $report['errors'][] = 'no package.xml or package.json';
            return $report;
        }
        $schema = self::schema();
        if ( !$schema )
            $report['warnings'][] = 'the JSON Schema ' . self::SCHEMA_FILE . ' was not found; documents were checked as XML only';
        $documents = self::modelDocuments( $dir, $missing );
        foreach ( $missing as $path )
            $report['errors'][] = 'missing document: ' . $path;
        foreach ( $documents as $path )
        {
            ++$report['documents'];
            $xmlFile = $dir . '/' . $path;
            $jsonFile = $dir . '/' . self::jsonName( $path );
            $file = is_file( $xmlFile ) ? $xmlFile : $jsonFile;
            $isJSON = self::isJSONFile( $file );
            if ( $isJSON )
            {
                $data = self::decode( (string)file_get_contents( $file ), $error );
                if ( !$data )
                {
                    $report['errors'][] = basename( $file ) === self::DEFINITION_JSON ? "package.json: $error" : self::relative( $dir, $file ) . ": $error";
                    continue;
                }
                if ( $schema )
                {
                    foreach ( expJsonSchemaValidator::validate( $data, $schema ) as $schemaError )
                        $report['errors'][] = self::relative( $dir, $file ) . ': ' . $schemaError;
                }
                $expectedKind = $path === self::DEFINITION_XML ? 'definition' : 'document';
                if ( isset( $data['kind'] ) && $data['kind'] !== $expectedKind )
                    $report['errors'][] = self::relative( $dir, $file ) . ": kind is {$data['kind']}, expected $expectedKind";
                if ( isset( $data['source'] ) && $data['source'] !== $path )
                    $report['warnings'][] = self::relative( $dir, $file ) . ": source names {$data['source']}, the document is $path";
                if ( $path === self::DEFINITION_XML && isset( $data['checksums'] ) && is_array( $data['checksums'] ) )
                {
                    foreach ( $data['checksums'] as $checksumPath => $sum )
                    {
                        if ( !self::isSafeRelativePath( (string)$checksumPath ) || !is_file( $dir . '/' . $checksumPath ) )
                            $report['errors'][] = "checksums: $checksumPath is missing";
                        else if ( hash_file( 'sha256', $dir . '/' . $checksumPath ) !== $sum )
                            $report['errors'][] = "checksums: $checksumPath does not match";
                    }
                    foreach ( array_diff( array_keys( self::checksums( $dir ) ), array_keys( $data['checksums'] ) ) as $extra )
                        $report['warnings'][] = "checksums: $extra is not listed";
                }
                elseif ( $path === self::DEFINITION_XML )
                    $report['warnings'][] = 'package.json has no checksums';
            }
            $dom = self::documentDOM( $dir, $path );
            if ( !$dom )
            {
                $report['errors'][] = self::relative( $dir, $file ) . ': not well-formed';
                continue;
            }
            if ( $path === self::DEFINITION_XML )
            {
                if ( $dom->documentElement->nodeName !== 'package' )
                {
                    $report['errors'][] = 'the definition\'s root element is not <package>';
                    continue;
                }
                $package = new eZPackage();
                if ( !$package->parseDOMTree( $dom ) )
                    $report['errors'][] = 'the definition has no name';
                else
                {
                    $report['name'] = (string)$package->attribute( 'name' );
                    if ( !eZPackage::isValidName( $report['name'] ) )
                        $report['errors'][] = 'the package name is not valid: ' . $report['name'];
                }
            }
        }
        $report['files'] = count( self::fileList( $dir ) );
        $report['valid'] = !$report['errors'];
        return $report;
    }

    /** @return string */
    private static function relative( $dir, $file )
    {
        return strpos( $file, $dir . '/' ) === 0 ? substr( $file, strlen( $dir ) + 1 ) : $file;
    }

    /**
     * The canonical form of a document for comparing two of them: C14N of the DOM the kernel reads, without the
     * white space only text that contains a line break (formatting).
     * @param DOMDocument $dom
     * @return string
     */
    static function canonical( DOMDocument $dom )
    {
        $copy = new DOMDocument( '1.0', 'utf-8' );
        $copy->appendChild( $copy->importNode( $dom->documentElement, true ) );
        $xpath = new DOMXPath( $copy );
        $remove = array();
        foreach ( $xpath->query( '//text()' ) as $text )
        {
            if ( $text->nodeType === XML_TEXT_NODE && self::isFormatting( $text->nodeValue ) )
                $remove[] = $text;
        }
        foreach ( $remove as $text )
            $text->parentNode->removeChild( $text );
        return (string)$copy->C14N();
    }
}
