<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Read-only support for the package contents browser on package/view/full/<name>: every file a
 * package's own directory carries, paginated and filtered, and one file's own content -
 * pretty-printed if it is XML, with a friendlier attribute/translation table for a content-object
 * or content-class item. Kernel-only: nothing here reads from, or requires, any extension.
 */
if ( !class_exists( 'eZPackageFileBrowser', false ) ) {
class eZPackageFileBrowser
{
    /** Every regular file under the package's own directory, recursively, sorted by path. No limit here: paginate the result, do not slice it in this method. */
    static function allFiles( eZPackage $package )
    {
        $out = array();
        $base = rtrim( (string)$package->path(), '/' );
        if ( !is_dir( $base ) )
            return $out;
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $iterator as $file )
        {
            if ( !$file->isFile() )
                continue;
            $relative = ltrim( str_replace( $base, '', $file->getPathname() ), '/' );
            // A hidden file or one under a hidden directory (.cache/, ...) is the kernel's own
            // bookkeeping for the package, not one of its own install items - never part of what
            // this browser shows, downloadable or otherwise, even though it is still safely inside
            // the package's own directory (filePath() would allow reading it either way).
            if ( strpos( $relative, '/.' ) !== false || $relative[0] === '.' )
                continue;
            $out[] = array(
                'path' => $relative,
                'size' => $file->getSize(),
                'kind' => self::fileKind( $relative ),
            );
        }
        usort( $out, function ( $a, $b ) { return strcasecmp( $a['path'], $b['path'] ); } );
        return $out;
    }

    /**
     * 'class', 'object', 'image', 'simplefile', 'document' or 'other'. Directory-based for
     * everything but a bare .xml item (simplefiles/, images/, documents/ are the kernel's own
     * package layout, eZPackage::simpleFilesDirectory()/documentDirectory() and so on); a .xml
     * item's own root element says which of 'class'/'object' it is - the item's own file naming is
     * not itself a safe way to tell (an inline object-list item and a lone content-class item can
     * both sit at the package's own top level).
     */
    static function fileKind( $relativePath )
    {
        $ext = strtolower( (string)pathinfo( $relativePath, PATHINFO_EXTENSION ) );
        $topDir = strstr( $relativePath, '/', true );
        if ( $topDir === 'images' )
            return 'image';
        if ( $topDir === 'simplefiles' )
            return 'simplefile';
        if ( $topDir === 'documents' )
            return 'document';
        if ( $ext === 'json' )
            return 'unknown-json'; // a document of the JSON format (doc/bc/6.0/package-json-format.md), resolved by peekJSONKind()
        if ( $ext !== 'xml' )
            return 'other';
        return 'unknown-xml'; // resolved by peekXMLKind() once the caller has the package to read the file from
    }

    /**
     * A .xml item's real kind, by its root element - a cheap peek (a few hundred bytes), not a
     * full parse: 'class' (<content-class), 'object' (<object-list, an inline item carrying one or
     * several objects) or 'package' (package.xml itself). 'other' if none of those.
     */
    static function peekXMLKind( $realPath )
    {
        $sample = @file_get_contents( $realPath, false, null, 0, 4096 );
        if ( $sample === false )
            return 'other';
        $sample = ltrim( preg_replace( '/^\xEF\xBB\xBF/', '', $sample ) );
        // An optional "prefix:" before the element's own local name: a lone per-object file's own
        // root is not plain "<object", it is "<ezremote:object ..." (eZContentObjectVersion's own
        // XML serializer always writes it namespaced, kernel/classes/ezcontentobjectversion.php).
        if ( preg_match( '/<(?:\w+:)?content-class[\s>]/', $sample ) )
            return 'class';
        // "<content-object" is the item file that lists per-object files (object-files-list) or
        // carries them inline (object-list); a per-object file's own root element is the
        // "<ezremote:object " above (STORE_OBJECTS_TO_SEPARATE_FILES_THRESHOLD - a package past 100
        // objects writes one such file per object) - checked after "object-list" so that is not
        // mistaken for this bare/prefixed form (both start with "object").
        if ( preg_match( '/<(?:\w+:)?content-object[\s>]/', $sample )
             || preg_match( '/<(?:\w+:)?object-list[\s>]/', $sample )
             || preg_match( '/<(?:\w+:)?object[\s>]/', $sample ) )
            return 'object';
        if ( preg_match( '/<(?:\w+:)?package[\s>]/', $sample ) )
            return 'package';
        return 'other';
    }

    /**
     * Resolves $relativePath against $package's own directory, refusing an absolute path, a ".."
     * component, or anything realpath() follows (a symlink included) outside it. Returns the real,
     * safe, absolute path to an existing regular file, or false.
     */
    static function filePath( eZPackage $package, $relativePath )
    {
        $relativePath = ltrim( (string)$relativePath, '/' );
        if ( $relativePath === '' || strpos( $relativePath, "\0" ) !== false || preg_match( '#(^|/)\.\.(/|$)#', $relativePath ) )
            return false;
        $base = realpath( (string)$package->path() );
        if ( $base === false )
            return false;
        $real = realpath( $base . '/' . $relativePath );
        if ( $real === false || !is_file( $real ) || strpos( $real . '/', $base . '/' ) !== 0 )
            return false;
        return $real;
    }

    /** The file's real kind, resolving 'unknown-xml' and 'unknown-json' from fileKind() by actually reading it (peekXMLKind(), peekJSONKind()); every other kind is already final. */
    static function resolvedKind( eZPackage $package, array $fileRow )
    {
        if ( $fileRow['kind'] !== 'unknown-xml' && $fileRow['kind'] !== 'unknown-json' )
            return $fileRow['kind'];
        $real = self::filePath( $package, $fileRow['path'] );
        if ( $real === false )
            return 'other';
        return $fileRow['kind'] === 'unknown-json' ? self::peekJSONKind( $real ) : self::peekXMLKind( $real );
    }

    /**
     * A JSON document's kind, by the element its "root" mirrors (the JSON format keeps the XML document as JsonML,
     * so the first string of "root" is the XML root element): a cheap peek at the first bytes, as peekXMLKind().
     * package.json is 'package'; a .json file that is not a document of the format is 'other'.
     */
    static function peekJSONKind( $realPath )
    {
        $sample = @file_get_contents( $realPath, false, null, 0, 4096 );
        if ( $sample === false || !preg_match( '/"root"\s*:\s*\[\s*"([^"]+)"/', $sample, $matches ) )
            return 'other';
        $element = '<' . $matches[1] . ' ';
        return self::peekXMLKindOfSample( $element );
    }

    /** The kind of a sample of XML text (peekXMLKind() without the file). */
    static function peekXMLKindOfSample( $sample )
    {
        if ( preg_match( '/<(?:\w+:)?content-class[\s>]/', $sample ) )
            return 'class';
        if ( preg_match( '/<(?:\w+:)?content-object[\s>]/', $sample )
             || preg_match( '/<(?:\w+:)?object-list[\s>]/', $sample )
             || preg_match( '/<(?:\w+:)?object[\s>]/', $sample ) )
            return 'object';
        if ( preg_match( '/<(?:\w+:)?package[\s>]/', $sample ) )
            return 'package';
        return 'other';
    }

    /** @return bool whether a file row is a document of a package (the definition, a class or an object item) */
    static function isDocument( array $fileRow )
    {
        return in_array( $fileRow['kind'], array( 'class', 'object', 'package' ), true );
    }

    /**
     * A document's text in either format, the two being 1:1 (doc/bc/6.0/package-json-format.md): a JSON document
     * as it is (JSON) or as the XML it mirrors (XML); an XML document pretty-printed (XML) or as the JSON format
     * would write it (JSON). Anything else is returned as it is.
     *
     * @param string $bytes the file's content
     * @param string $path the file's path in the package (its name tells the format; the JSON source names the XML)
     * @param string $as 'xml' or 'json'
     * @return array text, format (the one shown), native (the file's own), error ('' or why the other format failed)
     */
    static function documentText( $bytes, $path, $as )
    {
        $native = strtolower( (string)pathinfo( $path, PATHINFO_EXTENSION ) ) === 'json' ? 'json' : 'xml';
        $as = in_array( $as, array( 'xml', 'json' ), true ) ? $as : $native;
        $out = array( 'text' => (string)$bytes, 'format' => $native, 'native' => $native, 'error' => '' );
        if ( $as === $native )
        {
            if ( $native === 'xml' )
                $out['text'] = self::prettyPrintXML( $bytes );
            return $out;
        }
        if ( $native === 'json' )
        {
            $dom = expPackageJson::toDOM( (string)$bytes, $error );
            if ( !$dom )
            {
                $out['error'] = (string)$error;
                return $out;
            }
            $out['text'] = self::prettyPrintXML( $dom->saveXML() );
            $out['format'] = 'xml';
            return $out;
        }
        $dom = new DOMDocument( '1.0', 'utf-8' );
        if ( !@$dom->loadXML( (string)$bytes ) || !$dom->documentElement )
        {
            $out['error'] = 'not well-formed XML';
            $out['text'] = self::prettyPrintXML( $bytes );
            return $out;
        }
        $out['text'] = expPackageJson::encode( expPackageJson::envelope( $dom, (string)$path ) );
        $out['format'] = 'json';
        return $out;
    }


    /** the largest image shown inline (bigger ones are a download) */
    const INLINE_IMAGE_MAX_BYTES = 10485760;

    /**
     * What a file is, by its content (never by its name alone): an image a browser shows safely (png, jpeg, gif,
     * webp: 'image'), an SVG (markup that may carry script: 'svg', shown as escaped text or downloaded, never
     * inline), text (UTF-8 without NUL bytes: 'text', which XML and JSON are), or anything else ('binary': a card
     * and a download, never dumped as text). The MIME type from fileinfo when it is there, else from the magic bytes.
     *
     * @param string $realPath
     * @return array media (image|svg|text|binary), mime, inline (whether package/viewfile shows it inline)
     */
    static function sniff( $realPath )
    {
        $head = (string)@file_get_contents( $realPath, false, null, 0, 8192 );
        $mime = '';
        if ( class_exists( 'finfo', false ) )
        {
            // the finfo object is freed with the variable (finfo_close() is deprecated as of PHP 8.5)
            $finfo = new finfo( FILEINFO_MIME_TYPE );
            $mime = (string)@$finfo->file( $realPath );
        }
        $magic = array( "\x89PNG\r\n\x1a\n" => 'image/png', "\xFF\xD8\xFF" => 'image/jpeg', 'GIF87a' => 'image/gif', 'GIF89a' => 'image/gif' );
        $byMagic = '';
        foreach ( $magic as $bytes => $type )
            if ( strncmp( $head, $bytes, strlen( $bytes ) ) === 0 )
                $byMagic = $type;
        if ( substr( $head, 0, 4 ) === 'RIFF' && substr( $head, 8, 4 ) === 'WEBP' )
            $byMagic = 'image/webp';
        $images = array( 'image/png', 'image/jpeg', 'image/gif', 'image/webp' );
        // an image only when its bytes say so (fileinfo and the magic agree, or the magic alone without fileinfo)
        if ( $byMagic !== '' && ( $mime === '' || $mime === $byMagic ) )
            return array( 'media' => 'image', 'mime' => $byMagic, 'inline' => (int)@filesize( $realPath ) <= self::INLINE_IMAGE_MAX_BYTES );
        $isText = $head === '' || ( strpos( $head, "\0" ) === false && preg_match( '//u', self::cutUTF8( $head ) ) === 1 );
        if ( $isText && ( $mime === 'image/svg+xml' || preg_match( '/^\s*(<\?xml[^>]*>\s*)?(<!--.*?-->\s*)*(<!DOCTYPE svg[^>]*>\s*)?<svg[\s>]/is', $head ) ) )
            return array( 'media' => 'svg', 'mime' => 'image/svg+xml', 'inline' => false );
        if ( $isText )
            return array( 'media' => 'text', 'mime' => $mime !== '' && strpos( $mime, 'text/' ) === 0 || in_array( $mime, array( 'application/json', 'application/xml' ), true ) ? $mime : 'text/plain', 'inline' => false );
        return array( 'media' => 'binary', 'mime' => $mime !== '' && !in_array( $mime, $images, true ) ? $mime : 'application/octet-stream', 'inline' => false );
    }

    /** $bytes without an UTF-8 sequence cut off at its end (a sample of a file stops anywhere) */
    private static function cutUTF8( $bytes )
    {
        for ( $i = 1; $i <= 3 && $i <= strlen( $bytes ); ++$i )
        {
            $byte = ord( $bytes[strlen( $bytes ) - $i] );
            if ( $byte < 0x80 )
                break;
            if ( $byte >= 0xC0 )
                return substr( $bytes, 0, strlen( $bytes ) - $i );
        }
        return $bytes;
    }

    /**
     * Whether a file matches the checksum package.json lists for it: 'match', 'mismatch', or 'none' (a classic
     * package, a package.json without checksums, or a file it does not list).
     *
     * @param eZPackage $package
     * @param string $relativePath
     * @param string $sha256 the file's own
     * @return string
     */
    static function checksumState( eZPackage $package, $relativePath, $sha256 )
    {
        $definition = rtrim( (string)$package->path(), '/' ) . '/package.json';
        if ( !is_file( $definition ) )
            return 'none';
        $data = expPackageJson::decode( (string)file_get_contents( $definition ) );
        if ( !is_array( $data ) || !isset( $data['checksums'][$relativePath] ) )
            return 'none';
        return $data['checksums'][$relativePath] === $sha256 ? 'match' : 'mismatch';
    }
    /** The XML of a document file in either format (for objectItemSummary()), or '' when it has none. */
    static function documentXML( $bytes, $path )
    {
        if ( strtolower( (string)pathinfo( $path, PATHINFO_EXTENSION ) ) !== 'json' )
            return (string)$bytes;
        $dom = expPackageJson::toDOM( (string)$bytes );
        return $dom ? (string)$dom->saveXML() : '';
    }

    /**
     * Filters (by resolved kind, and a plain substring search over the path) then paginates
     * allFiles()'s own list. $limit === 'all' turns pagination off (every filtered file on the one
     * page). Returns array( 'files' (this page's slice, each with 'index' into the whole list and
     * its resolved 'kind'), 'total_all', 'total_filtered', 'offset', 'limit', 'page', 'pages' ).
     */
    static function filteredPage( eZPackage $package, array $options = array() )
    {
        $typeFilter = isset( $options['type'] ) ? (string)$options['type'] : '';
        $search = isset( $options['search'] ) ? trim( (string)$options['search'] ) : '';
        $offsetOption = isset( $options['offset'] ) ? $options['offset'] : 0;
        $offset = $offsetOption === 'last' ? 0 : max( 0, (int)$offsetOption );
        $limit = isset( $options['limit'] ) ? $options['limit'] : 50;

        $all = self::allFiles( $package );
        $totalAll = count( $all );
        $filtered = array();
        foreach ( $all as $i => $row )
        {
            $row['index'] = $i;
            $row['kind'] = self::resolvedKind( $package, $row );
            if ( $typeFilter !== '' && $row['kind'] !== $typeFilter )
                continue;
            if ( $search !== '' && stripos( $row['path'], $search ) === false )
                continue;
            $filtered[] = $row;
        }
        $totalFiltered = count( $filtered );

        if ( $limit === 'all' )
        {
            $slice = $filtered;
            $limitInt = max( 1, $totalFiltered );
            $offset = 0;
        }
        else
        {
            $limitInt = max( 1, (int)$limit );
            if ( $offsetOption === 'last' )
                $offset = $totalFiltered > 0 ? (int)( floor( ( $totalFiltered - 1 ) / $limitInt ) * $limitInt ) : 0;
            elseif ( $offset >= $totalFiltered && $totalFiltered > 0 )
                $offset = (int)( floor( ( $totalFiltered - 1 ) / $limitInt ) * $limitInt );
            $slice = array_slice( $filtered, $offset, $limitInt );
        }

        return array(
            'files' => $slice,
            'total_all' => $totalAll,
            'total_filtered' => $totalFiltered,
            'offset' => $offset,
            'limit' => $limit,
            'page' => $totalFiltered > 0 ? (int)floor( $offset / $limitInt ) + 1 : 1,
            'pages' => $totalFiltered > 0 ? (int)ceil( $totalFiltered / $limitInt ) : 1,
        );
    }

    /** Pretty-printed XML (indented, no run-together text nodes), or the original bytes unchanged if they do not parse as XML. */
    static function prettyPrintXML( $bytes )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        if ( !@$dom->loadXML( (string)$bytes ) )
            return (string)$bytes;
        $pretty = $dom->saveXML();
        return $pretty !== false ? $pretty : (string)$bytes;
    }

    /**
     * A content-object item's attributes and translations, read straight from its own XML (no
     * database, no datatype classes - a plain, generic DOM read, so this works for any datatype
     * without depending on one): array( 'name', 'remote_id', 'class_identifier', 'modified',
     * 'translations' => array( language => array( identifier => array( 'type', 'text' ) ) ) ).
     * 'text' is the attribute's own child element text content, concatenated when it has more than
     * one (an ezxmltext's structured markup, for instance) - not a rendering of it, a readable
     * enough one for a contents browser. Returns null if $bytes is not a content-object item.
     */
    static function objectItemSummary( $bytes )
    {
        $dom = new DOMDocument();
        if ( !@$dom->loadXML( (string)$bytes ) )
            return null;
        $root = $dom->documentElement;
        if ( !$root )
            return null;
        // localName, not tagName: a package's own serializer picks its own prefix for these
        // elements ("ezremote:object", as a package the kernel's own package/create wizard built
        // straight from live content does; unprefixed "object", as one xrowextract builds does) -
        // tagName carries whichever prefix that was, localName never does.
        $isObjectList = $root->localName === 'object-list';
        if ( !$isObjectList && $root->localName !== 'object' )
            return null; // not a content-object item at all (a class item, package.xml, ...)
        $objectNode = $isObjectList ? self::firstByLocalName( $root, 'object' ) : $root;
        if ( $objectNode === null )
            return null;

        $summary = array(
            'name' => $objectNode->getAttribute( 'name' ),
            'remote_id' => $objectNode->getAttribute( 'remote_id' ),
            'class_identifier' => $objectNode->getAttributeNS( 'http://ez.no/ezobject', 'class_identifier' ),
            'modified' => $objectNode->getAttributeNS( 'http://ez.no/ezobject', 'modified' ),
            'translations' => array(),
            'more_objects' => $isObjectList ? max( 0, self::countByLocalName( $root, 'object' ) - 1 ) : 0,
        );
        $versionListNode = self::firstByLocalName( $objectNode, 'version-list' );
        if ( !$versionListNode )
            return $summary;
        foreach ( self::allByLocalName( $versionListNode, 'object-translation' ) as $translationNode )
        {
            $language = $translationNode->getAttribute( 'language' );
            $attributes = array();
            foreach ( $translationNode->getElementsByTagNameNS( 'http://ez.no/object/', 'attribute' ) as $attrNode )
            {
                $identifier = $attrNode->getAttributeNS( 'http://ez.no/ezobject', 'identifier' );
                if ( $identifier === '' )
                    continue;
                $text = trim( (string)$attrNode->textContent );
                $attributes[$identifier] = array(
                    'type' => $attrNode->getAttribute( 'type' ),
                    'text' => mb_strlen( $text ) > 400 ? mb_substr( $text, 0, 400 ) . '…' : $text,
                );
            }
            $summary['translations'][$language] = $attributes;
        }
        return $summary;
    }

    /**
     * A DOM element's own direct/descendant elements by local name alone, ignoring whatever prefix
     * their own namespace happens to be written with (getElementsByTagName() matches the literal
     * "prefix:name" string instead, and a package's own serializer chooses its own prefix). Used
     * only for the two element names a content-object item nests one level differently depending
     * on which serializer wrote it (version-list, object-translation); every attribute lookup above
     * already goes through getElementsByTagNameNS()/getAttributeNS() with the one namespace URI
     * every serializer this kernel ships uses for them.
     */
    static function allByLocalName( DOMNode $node, $localName )
    {
        $matches = array();
        foreach ( $node->childNodes as $child )
        {
            if ( $child instanceof DOMElement && $child->localName === $localName )
                $matches[] = $child;
            elseif ( $child instanceof DOMElement )
                $matches = array_merge( $matches, self::allByLocalName( $child, $localName ) );
        }
        return $matches;
    }

    static function firstByLocalName( DOMNode $node, $localName )
    {
        $matches = self::allByLocalName( $node, $localName );
        return isset( $matches[0] ) ? $matches[0] : null;
    }

    static function countByLocalName( DOMNode $node, $localName )
    {
        return count( self::allByLocalName( $node, $localName ) );
    }
}
}
