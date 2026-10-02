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

    /** The file's real kind, resolving 'unknown-xml' from fileKind() by actually reading it (peekXMLKind()); every other kind is already final. */
    static function resolvedKind( eZPackage $package, array $fileRow )
    {
        if ( $fileRow['kind'] !== 'unknown-xml' )
            return $fileRow['kind'];
        $real = self::filePath( $package, $fileRow['path'] );
        return $real !== false ? self::peekXMLKind( $real ) : 'other';
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
