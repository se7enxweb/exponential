<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Compares what a package carries with what the site has, for package/compare/<name>. Read-only:
 * nothing is installed, stored or written, apart from the comparison's own cache file.
 *
 * Content-class items are matched by identifier. The site's class is serialized by the same code
 * that writes a class into a package (eZContentClassPackageHandler::classDOMTree()), so both sides
 * are read by one parser: class names and properties, and per attribute its datatype, names, flags,
 * placement and datatype parameters.
 *
 * Content-object items are matched by remote id, every object of an item (inline or one file per
 * object). The site's attributes of the current version are serialized by each datatype's own
 * serializeContentObjectAttribute(), the code that writes an object into a package, with
 * eZPackageComparisonFileCollector standing in for the package, so both values have the same form.
 * Both are then normalised per datatype (normalizeValue()): plain values as text, ezxmltext as text
 * with its markup compared as canonical XML, relations by the remote ids they point to, files by
 * name, size and checksum, anything else by its serialized XML. Node placement is compared by node
 * and parent node remote ids. Objects of the site under a top node of the package that the package
 * does not carry are listed as only on the site.
 *
 * The whole comparison of a large package is built once and kept in the cache directory, keyed by
 * the package's files and the site's content and class state (stamp()), then paged through
 * (filteredPage()). The one item opened is compared again in full, with a word level difference per
 * value (itemDetail()), which is always current and keeps the cache small.
 *
 * Kernel-only: nothing here reads from, or requires, any extension.
 */
class eZPackageComparison
{
    /** Raised whenever the cached index changes form, so an old cache file is never read. */
    const CACHE_VERSION = 3;

    /** Objects read, fetched and compared together while the index is built. */
    const BATCH_SIZE = 200;

    const NS_REMOTE = 'http://ez.no/ezobject';
    const NS_OBJECT = 'http://ez.no/object/';

    /**
     * Whether normalizeValue() also makes the texts shown to a reader (ezxmltext as text, indented
     * XML). Off while the index is built, which needs only what is compared.
     */
    protected static $WithText = true;

    /** Item statuses, in the order the summary bar shows them. */
    static function statuses()
    {
        return array( 'new', 'changed', 'removed', 'identical', 'class_missing' );
    }

    // ------------------------------------------------------------------ cache

    /**
     * The comparison index of $package, from the cache when its stamp still matches, else built
     * (and cached). $refresh builds it again whatever the cache holds.
     */
    static function cachedIndex( eZPackage $package, $refresh = false )
    {
        $name = (string)$package->attribute( 'name' );
        $stamp = self::stamp( $package );
        $file = self::cacheFilePath( $name, $stamp );
        if ( !$refresh && is_file( $file ) )
        {
            $data = @unserialize( (string)@file_get_contents( $file ), array( 'allowed_classes' => false ) );
            if ( is_array( $data ) && isset( $data['cache_version'] ) && $data['cache_version'] === self::CACHE_VERSION )
            {
                $data['from_cache'] = true;
                return $data;
            }
        }

        $index = self::buildIndex( $package );
        $index['stamp'] = $stamp;
        $index['cache_version'] = self::CACHE_VERSION;
        self::forget( $name );
        $directory = dirname( $file );
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        eZFile::create( basename( $file ), $directory, serialize( $index ), true );
        $index['from_cache'] = false;
        return $index;
    }

    /** Removes every cached comparison of the package $packageName. */
    static function forget( $packageName )
    {
        $pattern = self::cacheDirectory() . '/' . self::safeName( $packageName ) . '-*.cache';
        foreach ( (array)glob( $pattern ) as $old )
        {
            if ( is_file( $old ) )
                @unlink( $old );
        }
    }

    static function cacheDirectory()
    {
        return eZSys::cacheDirectory() . '/packagecompare';
    }

    static function cacheFilePath( $packageName, $stamp )
    {
        return self::cacheDirectory() . '/' . self::safeName( $packageName ) . '-' . $stamp . '.cache';
    }

    protected static function safeName( $packageName )
    {
        return preg_replace( '/[^A-Za-z0-9_.-]/', '_', (string)$packageName );
    }

    /**
     * What the cached comparison depends on: every file of the package (path, size and time) and
     * the site's content and class state - the newest object modification and node change, the
     * number of objects and nodes, the newest class modification and the number of class attributes.
     * Any install, publish, move, removal or class edit changes it.
     */
    static function stamp( eZPackage $package )
    {
        $parts = array( 'v' . self::CACHE_VERSION, (string)$package->attribute( 'name' ) );
        $base = rtrim( (string)$package->path(), '/' );
        $count = 0;
        $size = 0;
        $newest = 0;
        $pathHash = '';
        if ( is_dir( $base ) )
        {
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $iterator as $file )
            {
                if ( !$file->isFile() )
                    continue;
                $relative = substr( $file->getPathname(), strlen( $base ) + 1 );
                if ( $relative === '' || $relative[0] === '.' || strpos( $relative, '/.' ) !== false )
                    continue;
                ++$count;
                $size += $file->getSize();
                $newest = max( $newest, $file->getMTime() );
                $pathHash = md5( $pathHash . $relative );
            }
        }
        $parts[] = "$count:$size:$newest:" . substr( md5( $pathHash ), 0, 8 );

        $db = eZDB::instance();
        $queries = array(
            'SELECT MAX( modified ) AS a, COUNT( * ) AS b FROM ezcontentobject',
            'SELECT MAX( modified_subnode ) AS a, COUNT( * ) AS b FROM ezcontentobject_tree',
            'SELECT MAX( modified ) AS a, COUNT( * ) AS b FROM ezcontentclass',
            'SELECT MAX( id ) AS a, COUNT( * ) AS b FROM ezcontentclass_attribute',
        );
        foreach ( $queries as $sql )
        {
            $rows = $db->arrayQuery( $sql );
            $parts[] = isset( $rows[0] ) ? (int)$rows[0]['a'] . ':' . (int)$rows[0]['b'] : '-';
        }
        return substr( sha1( implode( '|', $parts ) ), 0, 16 );
    }

    // ------------------------------------------------------------------ building the index

    /**
     * The comparison of every class and object the package carries, one summary row per item
     * (no values: an opened item is compared again, see itemDetail()), plus the objects of the
     * site under the package's top nodes that the package does not carry.
     *
     * Returns array( 'items' => list, 'built' => timestamp, 'build_seconds' => float, 'top_nodes' => list,
     * 'object_count', 'class_count' ). Each item: 'kind' ('class'|'object'), 'status', 'name',
     * 'remote_id', 'class_identifier', 'file' (package-relative path, null for a site-only object),
     * 'position' (the object's place in an inline object list), 'site_id', 'fields' (values that
     * differ), 'lang_package' / 'lang_site' (translations on one side only), 'placement' (bool),
     * 'class_changed' (bool), 'attributes' (class: array of counts).
     */
    static function buildIndex( eZPackage $package )
    {
        $start = microtime( true );
        $db = eZDB::instance();
        // A safety net on top of the read-only code: whatever a datatype's serializer might do while
        // the site's values are read, nothing of it is kept
        $db->begin();
        $withText = self::$WithText;
        self::$WithText = false;
        // Classes read afresh: a long-running process (and an import just before) may hold old ones
        self::siteClasses( true );
        self::siteClassAttributes( true );

        $items = array();
        $sources = self::packageSources( $package );

        foreach ( $sources['classes'] as $classSource )
        {
            $packageClass = self::readPackageClass( $package, $classSource );
            if ( $packageClass === null )
                continue;
            $siteClass = self::siteClassData( $packageClass['identifier'] );
            $result = self::compareClassData( $packageClass, $siteClass, false );
            $items[] = array(
                'kind' => 'class',
                'status' => $result['status'],
                'name' => $packageClass['name'],
                'remote_id' => $packageClass['remote_id'],
                'class_identifier' => $packageClass['identifier'],
                'file' => $classSource,
                'position' => 0,
                'site_id' => $siteClass ? $siteClass['id'] : null,
                'fields' => $result['fields'],
                'lang_package' => 0,
                'lang_site' => 0,
                'placement' => false,
                'class_changed' => false,
                'attributes' => $result['attributes'],
            );
        }

        $classes = self::siteClasses();
        $packageClassIdentifiers = array();
        foreach ( $items as $item )
            $packageClassIdentifiers[$item['class_identifier']] = true;

        $packageRemoteIDs = array();
        $objectCount = 0;
        foreach ( array_chunk( $sources['objects'], self::BATCH_SIZE ) as $batch )
        {
            $packageObjects = array();
            $documents = array();
            foreach ( $batch as $source )
            {
                $node = self::packageObjectNode( $package, $source, $documents );
                if ( !$node )
                    continue;
                $data = self::packageObjectData( $package, $node, $sources['top_nodes'] );
                if ( $data['remote_id'] === '' )
                    continue;
                $data['source'] = $source;
                $packageObjects[] = $data;
            }
            unset( $documents );

            $remoteIDs = array();
            foreach ( $packageObjects as $data )
                $remoteIDs[] = $data['remote_id'];
            $siteObjects = self::siteObjectsData( $remoteIDs, 'remote_id' );

            foreach ( $packageObjects as $data )
            {
                ++$objectCount;
                $packageRemoteIDs[$data['remote_id']] = true;
                $site = isset( $siteObjects[$data['remote_id']] ) ? $siteObjects[$data['remote_id']] : null;
                $result = self::compareObjectData( $data, $site, false );
                if ( $site === null && !self::siteHasClass( $classes, $data['class_identifier'], $data['class_remote_id'] ) )
                    $result['status'] = 'class_missing';
                $items[] = array(
                    'kind' => 'object',
                    'status' => $result['status'],
                    'name' => $data['name'],
                    'remote_id' => $data['remote_id'],
                    'class_identifier' => $data['class_identifier'],
                    'file' => $data['source']['file'],
                    'position' => $data['source']['position'],
                    'site_id' => $site ? $site['id'] : null,
                    'fields' => $result['fields'],
                    'lang_package' => $result['lang_package'],
                    'lang_site' => $result['lang_site'],
                    'placement' => $result['placement'],
                    'class_changed' => $result['class_changed'],
                    'attributes' => null,
                );
            }
            unset( $packageObjects, $siteObjects );
            eZContentObject::clearCache();
        }

        // The site's objects under the package's top nodes that the package does not carry
        foreach ( self::siteSubtreeObjects( $sources['top_nodes'] ) as $row )
        {
            if ( isset( $packageRemoteIDs[$row['remote_id']] ) )
                continue;
            $packageRemoteIDs[$row['remote_id']] = true;
            $items[] = array(
                'kind' => 'object',
                'status' => 'removed',
                'name' => (string)$row['name'],
                'remote_id' => (string)$row['remote_id'],
                'class_identifier' => isset( $classes['by_id'][(int)$row['contentclass_id']] ) ? $classes['by_id'][(int)$row['contentclass_id']]['identifier'] : '',
                'file' => null,
                'position' => 0,
                'site_id' => (int)$row['id'],
                'fields' => 0,
                'lang_package' => 0,
                'lang_site' => 0,
                'placement' => false,
                'class_changed' => false,
                'attributes' => null,
            );
        }

        $db->rollback();
        self::$WithText = $withText;

        foreach ( $items as $i => &$item )
            $item['index'] = $i;
        unset( $item );

        return array(
            'items' => $items,
            'built' => time(),
            'build_seconds' => round( microtime( true ) - $start, 2 ),
            'top_nodes' => array_values( $sources['top_nodes'] ),
            'object_count' => $objectCount,
            'class_count' => count( $sources['classes'] ),
        );
    }

    /**
     * Where the package's classes and objects are: array( 'classes' => list of package-relative
     * class files, 'objects' => list of array( 'file', 'position' ), 'top_nodes' => node remote ids ).
     */
    static function packageSources( eZPackage $package )
    {
        $out = array( 'classes' => array(), 'objects' => array(), 'top_nodes' => array() );
        $base = rtrim( (string)$package->path(), '/' );
        $installItems = $package->installItemsList();
        if ( !is_array( $installItems ) )
            return $out;
        foreach ( $installItems as $item )
        {
            if ( empty( $item['filename'] ) )
                continue;
            $relative = ( !empty( $item['sub-directory'] ) ? $item['sub-directory'] . '/' : '' ) . $item['filename'] . '.xml';
            if ( eZPackageFileBrowser::filePath( $package, $relative ) === false )
                continue;
            if ( $item['type'] === 'ezcontentclass' )
            {
                $out['classes'][] = $relative;
                continue;
            }
            if ( $item['type'] !== 'ezcontentobject' )
                continue;

            $dom = self::loadDocument( $base . '/' . $relative );
            if ( !$dom )
                continue;
            $root = $dom->documentElement;
            foreach ( self::childrenByLocalName( $root, 'top-node-list' ) as $topList )
            {
                foreach ( self::childrenByLocalName( $topList, 'top-node' ) as $topNode )
                {
                    $remoteID = (string)$topNode->getAttribute( 'remote-id' );
                    if ( $remoteID !== '' )
                        $out['top_nodes'][$remoteID] = $remoteID;
                }
            }
            foreach ( self::childrenByLocalName( $root, 'object-list' ) as $objectList )
            {
                $position = 0;
                foreach ( self::childrenByLocalName( $objectList, 'object' ) as $objectNode )
                    $out['objects'][] = array( 'file' => $relative, 'position' => $position++ );
            }
            $directory = !empty( $item['sub-directory'] ) ? $item['sub-directory'] : 'ezcontentobject';
            foreach ( self::childrenByLocalName( $root, 'object-files-list' ) as $fileList )
            {
                foreach ( self::childrenByLocalName( $fileList, 'object-file' ) as $fileNode )
                {
                    // Checked (and a missing or unsafe one skipped) when it is read, packageObjectNode()
                    $objectFile = $directory . '/' . basename( (string)$fileNode->getAttribute( 'filename' ) );
                    $out['objects'][] = array( 'file' => $objectFile, 'position' => -1 );
                }
            }
        }
        return $out;
    }

    /**
     * The object element for $source (array( 'file', 'position' ), position -1 for a file of its own),
     * reading each inline object list only once per batch ($documents).
     */
    static function packageObjectNode( eZPackage $package, array $source, array &$documents = array() )
    {
        $path = eZPackageFileBrowser::filePath( $package, $source['file'] );
        if ( $path === false )
            return null;
        if ( !isset( $documents[$path] ) )
            $documents[$path] = self::loadDocument( $path );
        $dom = $documents[$path];
        if ( !$dom || !$dom->documentElement )
            return null;
        $root = $dom->documentElement;
        if ( $source['position'] < 0 )
            return $root->localName === 'object' ? $root : null;
        $position = 0;
        foreach ( self::childrenByLocalName( $root, 'object-list' ) as $objectList )
        {
            foreach ( self::childrenByLocalName( $objectList, 'object' ) as $objectNode )
            {
                if ( $position++ === (int)$source['position'] )
                    return $objectNode;
            }
        }
        return null;
    }

    /** A package XML file, white space kept (normalizeValue() takes the indentation out itself). */
    static function loadDocument( $path )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $previous = libxml_use_internal_errors( true );
        $ok = $dom->load( $path, LIBXML_NONET | LIBXML_PARSEHUGE );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        return $ok ? $dom : null;
    }

    // ------------------------------------------------------------------ package side

    /**
     * One package object, read into the form compareObjectData() takes: array( 'remote_id', 'name',
     * 'class_identifier', 'class_remote_id', 'modified', 'languages' => array( language =>
     * array( identifier => value ) ), 'names' => array( identifier => attribute name ),
     * 'placement' => list of array( 'node_remote_id', 'parent_remote_id', 'main', 'top' ) ).
     */
    static function packageObjectData( eZPackage $package, DOMElement $objectNode, array $topNodes )
    {
        $data = array(
            'remote_id' => (string)$objectNode->getAttribute( 'remote_id' ),
            'name' => (string)$objectNode->getAttribute( 'name' ),
            'class_identifier' => (string)$objectNode->getAttributeNS( self::NS_REMOTE, 'class_identifier' ),
            'class_remote_id' => (string)$objectNode->getAttribute( 'class_remote_id' ),
            'modified' => (string)$objectNode->getAttributeNS( self::NS_REMOTE, 'modified' ),
            'languages' => array(),
            'names' => array(),
            'placement' => array(),
        );
        $version = null;
        foreach ( self::childrenByLocalName( $objectNode, 'version-list' ) as $versionList )
        {
            $active = (string)$versionList->getAttribute( 'active_version' );
            foreach ( self::childrenByLocalName( $versionList, 'version' ) as $versionNode )
            {
                // The active version when the list says which one it is, else the last one
                if ( $version === null || $active === '' || (string)$versionNode->getAttributeNS( self::NS_REMOTE, 'version' ) === $active )
                    $version = $versionNode;
                if ( $active !== '' && (string)$versionNode->getAttributeNS( self::NS_REMOTE, 'version' ) === $active )
                    break;
            }
        }
        if ( !$version )
            return $data;

        $fileInfo = function ( $key ) use ( $package ) { return eZPackageComparison::packageFileInfo( $package, $key ); };
        foreach ( self::childrenByLocalName( $version, 'object-translation' ) as $translation )
        {
            $language = (string)$translation->getAttribute( 'language' );
            if ( $language === '' )
                continue;
            $values = array();
            foreach ( self::childrenByLocalName( $translation, 'attribute' ) as $attributeNode )
            {
                $identifier = (string)$attributeNode->getAttributeNS( self::NS_REMOTE, 'identifier' );
                if ( $identifier === '' )
                    continue;
                $datatype = (string)$attributeNode->getAttribute( 'type' );
                $data['names'][$identifier] = (string)$attributeNode->getAttribute( 'name' );
                $values[$identifier] = self::normalizeValue( $datatype, $attributeNode, $fileInfo );
            }
            $data['languages'][$language] = $values;
        }
        foreach ( self::childrenByLocalName( $version, 'node-assignment-list' ) as $assignmentList )
        {
            foreach ( self::childrenByLocalName( $assignmentList, 'node-assignment' ) as $assignment )
            {
                $nodeRemoteID = (string)$assignment->getAttribute( 'remote-id' );
                $data['placement'][] = array(
                    'node_remote_id' => $nodeRemoteID,
                    'parent_remote_id' => (string)$assignment->getAttribute( 'parent-node-remote-id' ),
                    'main' => (bool)$assignment->getAttribute( 'is-main-node' ),
                    'top' => isset( $topNodes[$nodeRemoteID] ),
                );
            }
        }
        return $data;
    }

    /** A file the package carries for $key (a simple file: image, binary file, media): its name, size and checksum, or null. */
    static function packageFileInfo( eZPackage $package, $key )
    {
        $key = (string)$key;
        if ( $key === '' )
            return null;
        $list = $package->attribute( 'simple-file-list' );
        if ( !is_array( $list ) || !isset( $list[$key]['package-path'] ) )
            return array( 'name' => '', 'size' => null, 'md5' => null, 'missing' => true );
        $path = eZPackageFileBrowser::filePath( $package, $list[$key]['package-path'] );
        $name = isset( $list[$key]['original-path'] ) && $list[$key]['original-path'] !== '' ? basename( $list[$key]['original-path'] ) : basename( $list[$key]['package-path'] );
        if ( $path === false )
            return array( 'name' => $name, 'size' => null, 'md5' => null, 'missing' => true );
        return array( 'name' => $name, 'size' => filesize( $path ), 'md5' => md5_file( $path ), 'missing' => false );
    }

    /** A file of the site (a path as a datatype stores it): its name, size and checksum where the file is at hand. */
    static function siteFileInfo( $path )
    {
        $path = (string)$path;
        if ( $path === '' )
            return null;
        $name = basename( $path );
        if ( is_file( $path ) )
            return array( 'name' => $name, 'size' => filesize( $path ), 'md5' => md5_file( $path ), 'missing' => false );
        // A clustered site keeps the file in the cluster, not on this disk: its size only, nothing fetched
        $handler = eZClusterFileHandler::instance( $path );
        if ( $handler->exists() )
            return array( 'name' => $name, 'size' => (int)$handler->size(), 'md5' => null, 'missing' => false );
        return array( 'name' => $name, 'size' => null, 'md5' => null, 'missing' => true );
    }

    // ------------------------------------------------------------------ site side

    /** The site's classes (defined version): array( 'by_id' => id => row, 'by_identifier' => identifier => row, 'by_remote_id' => ... ). */
    static function siteClasses( $reset = false )
    {
        static $cache = null;
        if ( $cache !== null && !$reset )
            return $cache;
        $cache = array( 'by_id' => array(), 'by_identifier' => array(), 'by_remote_id' => array() );
        $rows = eZDB::instance()->arrayQuery( 'SELECT id, identifier, remote_id FROM ezcontentclass WHERE version = ' . (int)eZContentClass::VERSION_STATUS_DEFINED );
        foreach ( (array)$rows as $row )
        {
            $row['id'] = (int)$row['id'];
            $cache['by_id'][$row['id']] = $row;
            $cache['by_identifier'][$row['identifier']] = $row;
            if ( $row['remote_id'] !== '' )
                $cache['by_remote_id'][$row['remote_id']] = $row;
        }
        return $cache;
    }

    static function siteHasClass( array $classes, $identifier, $remoteID )
    {
        return ( $identifier !== '' && isset( $classes['by_identifier'][$identifier] ) )
            || ( $remoteID !== '' && isset( $classes['by_remote_id'][$remoteID] ) );
    }

    /** The site's class attributes (defined version): id => array( 'identifier', 'name', 'datatype', 'class_id' ). */
    static function siteClassAttributes( $reset = false )
    {
        static $cache = null;
        if ( $cache !== null && !$reset )
            return $cache;
        $cache = array();
        $rows = eZDB::instance()->arrayQuery( 'SELECT id, contentclass_id, identifier, serialized_name_list, data_type_string FROM ezcontentclass_attribute WHERE version = ' . (int)eZContentClass::VERSION_STATUS_DEFINED );
        foreach ( (array)$rows as $row )
        {
            $nameList = new eZSerializedObjectNameList( $row['serialized_name_list'] );
            $cache[(int)$row['id']] = array(
                'identifier' => $row['identifier'],
                'name' => (string)$nameList->name(),
                'datatype' => $row['data_type_string'],
                'class_id' => (int)$row['contentclass_id'],
            );
        }
        return $cache;
    }

    /**
     * The site's objects in the form compareObjectData() takes (see packageObjectData()), for a list
     * of remote ids ($key 'remote_id') or object ids ($key 'id'), keyed the same way. Three queries
     * per call, whatever the number of objects: objects, attributes of their current versions, nodes.
     */
    static function siteObjectsData( array $keys, $key = 'remote_id' )
    {
        $out = array();
        if ( !$keys )
            return $out;
        $db = eZDB::instance();
        if ( $key === 'id' )
            $where = 'id IN ( ' . implode( ', ', array_map( 'intval', $keys ) ) . ' )';
        else
        {
            $quoted = array();
            foreach ( $keys as $remoteID )
                $quoted[] = "'" . $db->escapeString( (string)$remoteID ) . "'";
            $where = 'remote_id IN ( ' . implode( ', ', $quoted ) . ' )';
        }
        $objectRows = $db->arrayQuery( "SELECT id, remote_id, current_version, contentclass_id, name, modified, status, initial_language_id FROM ezcontentobject WHERE $where" );
        if ( !$objectRows )
            return $out;

        $classes = self::siteClasses();
        $classAttributes = self::siteClassAttributes();
        $byID = array();
        foreach ( $objectRows as $row )
        {
            $id = (int)$row['id'];
            $classID = (int)$row['contentclass_id'];
            $byID[$id] = array(
                'id' => $id,
                'remote_id' => (string)$row['remote_id'],
                'name' => (string)$row['name'],
                'class_identifier' => isset( $classes['by_id'][$classID] ) ? $classes['by_id'][$classID]['identifier'] : '',
                'class_id' => $classID,
                'version' => (int)$row['current_version'],
                'modified' => (int)$row['modified'],
                'status' => (int)$row['status'],
                'languages' => array(),
                'names' => array(),
                'placement' => array(),
                'main_node_id' => null,
            );
        }
        // Single-table queries only (no joins), so that every database handler answers them the same
        $ids = implode( ', ', array_keys( $byID ) );
        $versions = array();
        foreach ( $byID as $data )
            $versions[$data['version']] = $data['version'];
        $attributeRows = $db->arrayQuery( "SELECT * FROM ezcontentobject_attribute
                                           WHERE contentobject_id IN ( $ids ) AND version IN ( " . implode( ', ', $versions ) . ' )' );
        $currentRows = array();
        $urlIDs = array();
        $keywordAttributeIDs = array();
        foreach ( (array)$attributeRows as $row )
        {
            $objectID = (int)$row['contentobject_id'];
            if ( !isset( $byID[$objectID] ) || (int)$row['version'] !== $byID[$objectID]['version'] || !isset( $classAttributes[(int)$row['contentclassattribute_id']] ) )
                continue;
            $currentRows[] = $row;
            if ( $row['data_type_string'] === 'ezurl' && (int)$row['data_int'] > 0 )
                $urlIDs[(int)$row['data_int']] = (int)$row['data_int'];
            elseif ( $row['data_type_string'] === 'ezkeyword' )
                $keywordAttributeIDs[(int)$row['id']] = (int)$row['id'];
        }
        // What the ezurl and ezkeyword serializers would each fetch for one attribute, for all at once
        $prefetched = array( 'urls' => array(), 'keywords' => array() );
        foreach ( array_chunk( $urlIDs, 500 ) as $chunk )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT id, url FROM ezurl WHERE id IN ( ' . implode( ', ', $chunk ) . ' )' ) as $row )
                $prefetched['urls'][(int)$row['id']] = (string)$row['url'];
        }
        $keywordIDs = array();
        $links = array();
        foreach ( array_chunk( $keywordAttributeIDs, 500 ) as $chunk )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT id, objectattribute_id, keyword_id FROM ezkeyword_attribute_link WHERE objectattribute_id IN ( ' . implode( ', ', $chunk ) . ' )' ) as $row )
            {
                $links[] = $row;
                $keywordIDs[(int)$row['keyword_id']] = (int)$row['keyword_id'];
            }
        }
        $keywords = array();
        foreach ( array_chunk( $keywordIDs, 500 ) as $chunk )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT id, keyword FROM ezkeyword WHERE id IN ( ' . implode( ', ', $chunk ) . ' )' ) as $row )
                $keywords[(int)$row['id']] = (string)$row['keyword'];
        }
        usort( $links, function ( $a, $b ) { return (int)$a['id'] - (int)$b['id']; } );
        foreach ( $links as $link )
        {
            if ( isset( $keywords[(int)$link['keyword_id']] ) )
                $prefetched['keywords'][(int)$link['objectattribute_id']][] = $keywords[(int)$link['keyword_id']];
        }

        usort( $currentRows, function ( $a, $b ) {
            return (int)$a['contentobject_id'] - (int)$b['contentobject_id'] ?: strcmp( $a['language_code'], $b['language_code'] ) ?: (int)$a['id'] - (int)$b['id'];
        } );
        foreach ( $currentRows as $row )
        {
            $objectID = (int)$row['contentobject_id'];
            $classAttribute = $classAttributes[(int)$row['contentclassattribute_id']];
            $identifier = $classAttribute['identifier'];
            $language = (string)$row['language_code'];
            $byID[$objectID]['names'][$identifier] = $classAttribute['name'];
            $byID[$objectID]['languages'][$language][$identifier] = self::siteValue( $row, $classAttribute, $prefetched );
        }

        $nodeRows = (array)$db->arrayQuery( "SELECT contentobject_id, node_id, main_node_id, remote_id, parent_node_id FROM ezcontentobject_tree
                                             WHERE contentobject_id IN ( $ids )" );
        $parentIDs = array();
        foreach ( $nodeRows as $row )
            $parentIDs[(int)$row['parent_node_id']] = (int)$row['parent_node_id'];
        $parentRemoteIDs = array();
        foreach ( array_chunk( $parentIDs, 500 ) as $chunk )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT node_id, remote_id FROM ezcontentobject_tree WHERE node_id IN ( ' . implode( ', ', $chunk ) . ' )' ) as $row )
                $parentRemoteIDs[(int)$row['node_id']] = (string)$row['remote_id'];
        }
        usort( $nodeRows, function ( $a, $b ) { return (int)$a['node_id'] - (int)$b['node_id']; } );
        foreach ( $nodeRows as $row )
        {
            $objectID = (int)$row['contentobject_id'];
            if ( !isset( $byID[$objectID] ) )
                continue;
            $isMain = (int)$row['node_id'] === (int)$row['main_node_id'];
            if ( $isMain )
                $byID[$objectID]['main_node_id'] = (int)$row['node_id'];
            $byID[$objectID]['placement'][] = array(
                'node_remote_id' => (string)$row['remote_id'],
                'parent_remote_id' => isset( $parentRemoteIDs[(int)$row['parent_node_id']] ) ? $parentRemoteIDs[(int)$row['parent_node_id']] : '',
                'main' => $isMain,
                'top' => false,
            );
        }

        foreach ( $byID as $data )
            $out[$key === 'id' ? $data['id'] : $data['remote_id']] = $data;
        return $out;
    }

    /**
     * One of the site's attribute values in normalised form: serialized by its datatype's own
     * package serializer (with a file collector standing in for the package), then normalizeValue().
     */
    static function siteValue( array $row, array $classAttribute, array $prefetched = null )
    {
        $datatypeString = (string)$row['data_type_string'];
        // ezimage: read from its own stored XML (the image's original file), not through its
        // content object, which may build a missing image alias - and so write - when asked for one
        if ( $datatypeString === 'ezimage' )
            return self::siteImageValue( $row );
        // ezurl and ezkeyword: what their serializers write, from what siteObjectsData() fetched
        // for the whole batch (each serializer would run a query of its own per attribute)
        if ( $prefetched !== null && $datatypeString === 'ezurl' )
        {
            $url = isset( $prefetched['urls'][(int)$row['data_int']] ) ? trim( $prefetched['urls'][(int)$row['data_int']] ) : '';
            return self::urlValue( $url, (string)$row['data_text'] );
        }
        if ( $prefetched !== null && $datatypeString === 'ezkeyword' )
        {
            $words = isset( $prefetched['keywords'][(int)$row['id']] ) ? array_unique( $prefetched['keywords'][(int)$row['id']] ) : array();
            return self::keywordValue( implode( ', ', $words ) );
        }

        $datatype = eZDataType::create( $datatypeString );
        if ( !$datatype )
        {
            $raw = (string)$row['data_text'] !== '' ? (string)$row['data_text'] : (string)$row['data_int'];
            return array( 'datatype' => $datatypeString, 'kind' => 'xml', 'compare' => 'raw:' . $raw, 'text' => $raw,
                          'raw' => null, 'ids' => array(), 'note' => 'datatype_missing' );
        }
        $attribute = new eZContentObjectAttribute( $row );
        $attribute->setContentClassAttributeIdentifier( $classAttribute['identifier'] );
        $attribute->setContentClassAttributeName( $classAttribute['name'] );
        $collector = new eZPackageComparisonFileCollector();
        try
        {
            $node = $datatype->serializeContentObjectAttribute( $collector, $attribute );
        }
        catch ( Exception $e )
        {
            $node = null;
        }
        catch ( Error $e )
        {
            $node = null;
        }
        if ( !$node instanceof DOMElement )
        {
            return array( 'datatype' => $datatypeString, 'kind' => 'xml', 'compare' => 'unserializable', 'text' => '',
                          'raw' => null, 'ids' => array(), 'note' => 'site_unreadable' );
        }
        $fileInfo = function ( $key ) use ( $collector ) {
            $path = $collector->simpleFilePath( $key );
            return $path === false ? null : eZPackageComparison::siteFileInfo( $path );
        };
        return self::normalizeValue( $datatypeString, $node, $fileInfo );
    }

    /** An ezimage value of the site, in the same form normalizeValue() gives the package's. */
    static function siteImageValue( array $row )
    {
        $url = '';
        $alternativeText = '';
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $previous = libxml_use_internal_errors( true );
        if ( (string)$row['data_text'] !== '' && $dom->loadXML( (string)$row['data_text'] ) && $dom->documentElement )
        {
            $root = $dom->documentElement;
            $alternativeText = (string)$root->getAttribute( 'alternative_text' );
            $url = (string)$root->getAttribute( 'url' );
            if ( $url === '' && $root->getAttribute( 'dirpath' ) !== '' && $root->getAttribute( 'filename' ) !== '' )
                $url = $root->getAttribute( 'dirpath' ) . '/' . $root->getAttribute( 'filename' );
        }
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        return self::fileValue( 'ezimage', $url !== '' ? self::siteFileInfo( $url ) : null, array( 'alternative_text' => $alternativeText ) );
    }

    /** Object id, remote id, name and class of every object whose main node is in the subtree of one of the node remote ids $topNodeRemoteIDs. */
    static function siteSubtreeObjects( array $topNodeRemoteIDs )
    {
        $rows = array();
        if ( !$topNodeRemoteIDs )
            return $rows;
        $db = eZDB::instance();
        $quoted = array();
        foreach ( $topNodeRemoteIDs as $remoteID )
            $quoted[] = "'" . $db->escapeString( (string)$remoteID ) . "'";
        $tops = $db->arrayQuery( 'SELECT path_string FROM ezcontentobject_tree WHERE remote_id IN ( ' . implode( ', ', $quoted ) . ' )' );
        $objectIDs = array();
        foreach ( (array)$tops as $top )
        {
            $path = $db->escapeString( (string)$top['path_string'] );
            if ( $path === '' )
                continue;
            $nodes = $db->arrayQuery( "SELECT node_id, main_node_id, contentobject_id, path_string FROM ezcontentobject_tree WHERE path_string LIKE '$path%'" );
            usort( $nodes, function ( $a, $b ) { return strcmp( $a['path_string'], $b['path_string'] ); } );
            foreach ( (array)$nodes as $node )
            {
                if ( (int)$node['node_id'] === (int)$node['main_node_id'] )
                    $objectIDs[(int)$node['contentobject_id']] = (int)$node['contentobject_id'];
            }
        }
        foreach ( array_chunk( $objectIDs, 500 ) as $chunk )
        {
            $found = array();
            foreach ( (array)$db->arrayQuery( 'SELECT id, remote_id, name, contentclass_id FROM ezcontentobject WHERE id IN ( ' . implode( ', ', $chunk ) . ' )' ) as $row )
                $found[(int)$row['id']] = $row;
            foreach ( $chunk as $id )
            {
                if ( isset( $found[$id] ) )
                    $rows[] = $found[$id];
            }
        }
        return $rows;
    }

    // ------------------------------------------------------------------ normalising values

    /**
     * An attribute's serialized XML (<ezobject:attribute type="...">, as a package carries it, or as
     * the site's datatype just wrote it) reduced to what is compared and shown: array( 'datatype',
     * 'kind' (text|xmltext|relation|file|xml|empty), 'compare' (equal when the values are equal),
     * 'text' (what the diff shows), 'raw' (the XML, kept for ezxmltext and serialized values),
     * 'ids' (relations: the remote ids pointed to), 'note' (a code, see noteText()) ).
     * $fileInfo( $key ) tells a file's name/size/checksum for a file key.
     */
    static function normalizeValue( $datatype, DOMElement $attributeNode, $fileInfo )
    {
        self::unformat( $attributeNode );
        $children = array();
        foreach ( $attributeNode->childNodes as $child )
        {
            if ( $child instanceof DOMElement )
                $children[] = $child;
        }
        $value = array( 'datatype' => (string)$datatype, 'kind' => 'text', 'compare' => '', 'text' => '', 'raw' => null, 'ids' => array(), 'note' => '' );

        switch ( $datatype )
        {
            case 'ezxmltext':
            {
                $section = isset( $children[0] ) ? $children[0] : null;
                $value['kind'] = 'xmltext';
                if ( !$section || !self::hasElementChildren( $section ) && trim( $section->textContent ) === '' )
                    return $value; // no text at all, however the empty section is written
                $value['compare'] = self::canonicalXML( $section );
                if ( self::$WithText )
                {
                    $value['text'] = self::xmlText( $section );
                    $value['raw'] = self::prettyXML( $section );
                }
                return $value;
            }
            case 'ezurl':
            {
                $url = '';
                $text = '';
                foreach ( $children as $child )
                {
                    if ( $child->localName === 'url' )
                        $url = urldecode( $child->textContent );
                    elseif ( $child->localName === 'text' )
                        $text = $child->textContent;
                }
                return self::urlValue( $url, $text );
            }
            case 'ezkeyword':
            {
                return self::keywordValue( isset( $children[0] ) ? $children[0]->textContent : '' );
            }
            case 'ezobjectrelation':
            case 'ezobjectrelationlist':
            {
                $ids = array();
                if ( $datatype === 'ezobjectrelation' )
                {
                    foreach ( $children as $child )
                    {
                        if ( $child->localName === 'related-object-remote-id' && trim( $child->textContent ) !== '' )
                            $ids[] = trim( $child->textContent );
                    }
                }
                else
                {
                    $items = array();
                    $order = 0;
                    foreach ( $attributeNode->getElementsByTagName( 'relation-item' ) as $relationItem )
                    {
                        $remoteID = (string)$relationItem->getAttribute( 'contentobject-remote-id' );
                        if ( $remoteID !== '' )
                            $items[] = array( (int)$relationItem->getAttribute( 'priority' ), $order++, $remoteID );
                    }
                    usort( $items, function ( $a, $b ) { return $a[0] === $b[0] ? $a[1] - $b[1] : $a[0] - $b[0]; } );
                    foreach ( $items as $item )
                        $ids[] = $item[2];
                }
                $value['kind'] = 'relation';
                $value['ids'] = $ids;
                $value['compare'] = implode( "\n", $ids );
                $value['text'] = implode( "\n", $ids );
                return $value;
            }
            case 'ezimage':
            {
                $key = (string)$attributeNode->getAttribute( 'image-file-key' );
                return self::fileValue( $datatype, $key !== '' ? call_user_func( $fileInfo, $key ) : null,
                                        array( 'alternative_text' => (string)$attributeNode->getAttribute( 'alternative-text' ) ) );
            }
            case 'ezbinaryfile':
            case 'ezmedia':
            {
                $fileNode = isset( $children[0] ) ? $children[0] : null;
                if ( !$fileNode )
                    return self::fileValue( $datatype, null, array() );
                $info = call_user_func( $fileInfo, (string)$fileNode->getAttribute( 'filekey' ) );
                $extra = array(
                    'original_filename' => (string)$fileNode->getAttribute( 'original-filename' ),
                    'mime_type' => (string)$fileNode->getAttribute( 'mime-type' ),
                );
                if ( $datatype === 'ezmedia' )
                {
                    foreach ( array( 'width', 'height', 'has-controller', 'controls', 'is-autoplay', 'plugins-page', 'quality', 'is-loop' ) as $name )
                        $extra[$name] = (string)$fileNode->getAttribute( $name );
                }
                if ( $info === null && $fileNode->getAttribute( 'filesize' ) !== '' )
                    $info = array( 'name' => $extra['original_filename'], 'size' => (int)$fileNode->getAttribute( 'filesize' ), 'md5' => null, 'missing' => true );
                return self::fileValue( $datatype, $info, $extra );
            }
            case 'ezuser':
            {
                $account = isset( $children[0] ) ? $children[0] : null;
                if ( !$account )
                    return $value;
                $lines = array(
                    ezpI18n::tr( 'design/admin/package', 'Login' ) . ': ' . $account->getAttribute( 'login' ),
                    ezpI18n::tr( 'design/admin/package', 'Email' ) . ': ' . $account->getAttribute( 'email' ),
                    ezpI18n::tr( 'design/admin/package', 'Enabled' ) . ': ' . ( $account->getAttribute( 'is_enabled' ) ? ezpI18n::tr( 'design/admin/package', 'Yes' ) : ezpI18n::tr( 'design/admin/package', 'No' ) ),
                    // Never the hash itself: a short fingerprint of it tells equal from different
                    ezpI18n::tr( 'design/admin/package', 'Password' ) . ': ' . substr( sha1( $account->getAttribute( 'password_hash_type' ) . ':' . $account->getAttribute( 'password_hash' ) ), 0, 8 ),
                );
                $value['text'] = implode( "\n", $lines );
                $value['compare'] = $value['text'];
                return $value;
            }
        }

        if ( !$children )
        {
            $value['kind'] = 'empty';
            return $value;
        }

        // Plain values - one or more elements holding only text (ezstring's <text>, ezinteger's
        // <value>, ezkeyword's <keyword-string>, ezdatetime's <date_time>, ...) - are compared as that text
        $simple = true;
        foreach ( $children as $child )
        {
            if ( self::hasElementChildren( $child ) || $child->attributes->length > 0 )
            {
                $simple = false;
                break;
            }
        }
        if ( $simple )
        {
            if ( count( $children ) === 1 )
            {
                $value['text'] = $children[0]->textContent;
                $value['compare'] = $children[0]->localName . '=' . $value['text'];
            }
            else
            {
                $lines = array();
                foreach ( $children as $child )
                    $lines[] = $child->localName . ': ' . $child->textContent;
                $value['text'] = implode( "\n", $lines );
                $value['compare'] = $value['text'];
            }
            return $value;
        }

        // Anything else by its serialized XML. An empty element (no content, no attributes) is left
        // out of what is compared: a datatype that learnt a new field writes it empty for every value
        // it had before, which is no difference in the value
        $compare = '';
        $raw = array();
        foreach ( $children as $child )
        {
            if ( self::$WithText )
                $raw[] = self::prettyXML( $child );
            $copy = $child->cloneNode( true );
            self::pruneEmptyElements( $copy );
            $compare .= self::canonicalXML( $copy );
        }
        $value['kind'] = 'xml';
        $value['compare'] = $compare;
        $value['text'] = implode( "\n", $raw );
        $value['raw'] = $value['text'];
        $value['note'] = 'serialized';
        return $value;
    }

    /** An ezurl value: its address and its link text. */
    static function urlValue( $url, $text )
    {
        return array( 'datatype' => 'ezurl', 'kind' => 'text', 'compare' => $url . "\n" . $text,
                      'text' => trim( $url . ( $text !== '' ? "\n" . $text : '' ) ), 'raw' => null, 'ids' => array(), 'note' => '' );
    }

    /** An ezkeyword value: its keywords as written, compared in any order. */
    static function keywordValue( $keywordString )
    {
        $words = array_filter( array_map( 'trim', explode( ',', (string)$keywordString ) ), 'strlen' );
        $sorted = $words;
        sort( $sorted, SORT_STRING );
        return array( 'datatype' => 'ezkeyword', 'kind' => 'text', 'compare' => implode( "\n", array_unique( $sorted ) ),
                      'text' => implode( ', ', $words ), 'raw' => null, 'ids' => array(), 'note' => '' );
    }

    /**
     * Whether two normalised values are equal. Mostly their 'compare' strings; a file is the same
     * file when both checksums are known and equal, or, when either side has no checksum (a file
     * not at hand), when name and size are equal - and its other properties in 'compare' match.
     */
    static function valuesEqual( array $a, array $b )
    {
        if ( $a['compare'] !== $b['compare'] )
            return false;
        if ( $a['kind'] !== 'file' || $b['kind'] !== 'file' )
            return $a['kind'] !== 'file' && $b['kind'] !== 'file';
        $fileA = isset( $a['file'] ) ? $a['file'] : null;
        $fileB = isset( $b['file'] ) ? $b['file'] : null;
        if ( !$fileA || !$fileB )
            return !$fileA && !$fileB;
        if ( $fileA['md5'] !== null && $fileB['md5'] !== null )
            return $fileA['md5'] === $fileB['md5'];
        return $fileA['name'] === $fileB['name'] && (string)$fileA['size'] === (string)$fileB['size'];
    }

    /** A file value (ezimage, ezbinaryfile, ezmedia): the file itself is compared by valuesEqual(), its other properties through 'compare'. */
    static function fileValue( $datatype, $info, array $extra )
    {
        $value = array( 'datatype' => (string)$datatype, 'kind' => 'file', 'compare' => '', 'text' => '', 'raw' => null, 'ids' => array(), 'note' => '',
                        'file' => $info );
        $lines = array();
        $compare = array();
        if ( $info )
        {
            $name = isset( $extra['original_filename'] ) && $extra['original_filename'] !== '' ? $extra['original_filename'] : $info['name'];
            $info['name'] = $name;
            $value['file'] = $info;
            $lines[] = $name;
            if ( $info['size'] !== null )
                $lines[] = ezpI18n::tr( 'design/admin/package', 'Size' ) . ': ' . $info['size'] . ' B';
            if ( $info['md5'] !== null )
                $lines[] = 'MD5: ' . $info['md5'];
            if ( $info['missing'] )
                $value['note'] = 'file_missing';
        }
        foreach ( $extra as $name => $extraValue )
        {
            if ( $name === 'original_filename' )
                continue;
            if ( $name === 'alternative_text' )
                $lines[] = ezpI18n::tr( 'design/admin/package', 'Alternative text' ) . ': ' . $extraValue;
            elseif ( $name === 'mime_type' )
                $lines[] = ezpI18n::tr( 'design/admin/package', 'MIME type' ) . ': ' . $extraValue;
            elseif ( $extraValue !== '' )
                $lines[] = $name . ': ' . $extraValue;
            $compare[] = $name . '=' . $extraValue;
        }
        if ( !$info && implode( '', $extra ) === '' )
        {
            $value['kind'] = 'empty';
            return $value;
        }
        $value['text'] = implode( "\n", $lines );
        $value['compare'] = implode( "\n", $compare );
        return $value;
    }

    /**
     * Takes out the white space a pretty-printing serializer puts between elements (a text node of
     * white space with a line break, in an element whose every text child is such white space), and
     * nothing else - a space between two inline elements of an ezxmltext stays. Applied to both
     * sides, a package's indented file and the site's unindented value then read alike.
     */
    static function unformat( DOMNode $node )
    {
        if ( !$node->hasChildNodes() )
            return;
        $texts = array();
        $hasElement = false;
        $onlyFormatting = true;
        foreach ( $node->childNodes as $child )
        {
            if ( $child instanceof DOMCdataSection )
                $onlyFormatting = false;
            elseif ( $child instanceof DOMText )
            {
                $texts[] = $child;
                if ( trim( $child->data ) !== '' || strpos( $child->data, "\n" ) === false )
                    $onlyFormatting = false;
            }
            elseif ( $child instanceof DOMElement )
                $hasElement = true;
        }
        if ( $hasElement && $onlyFormatting )
        {
            foreach ( $texts as $text )
                $node->removeChild( $text );
        }
        foreach ( $node->childNodes as $child )
        {
            if ( $child instanceof DOMElement )
                self::unformat( $child );
        }
    }

    /** Removes, recursively, every descendant element of $node with no attributes and no content at all. */
    static function pruneEmptyElements( DOMNode $node )
    {
        $children = array();
        foreach ( $node->childNodes as $child )
            $children[] = $child;
        foreach ( $children as $child )
        {
            if ( !$child instanceof DOMElement )
                continue;
            self::pruneEmptyElements( $child );
            if ( !$child->hasChildNodes() && $child->attributes->length === 0 )
                $node->removeChild( $child );
        }
    }

    static function hasElementChildren( DOMNode $node )
    {
        foreach ( $node->childNodes as $child )
        {
            if ( $child instanceof DOMElement )
                return true;
        }
        return false;
    }

    /** An ezxmltext's text for reading and diffing: block elements on lines of their own, embedded objects as a marker. */
    static function xmlText( DOMNode $node )
    {
        $text = self::xmlTextPart( $node );
        $text = preg_replace( "/[ \t]+\n/", "\n", $text );
        $text = preg_replace( "/\n{3,}/", "\n\n", $text );
        return trim( $text );
    }

    protected static function xmlTextPart( DOMNode $node )
    {
        static $blocks = array( 'paragraph' => true, 'header' => true, 'li' => true, 'tr' => true, 'line' => true, 'section' => true,
                                'table' => true, 'ul' => true, 'ol' => true, 'literal' => true, 'custom' => false );
        $out = '';
        foreach ( $node->childNodes as $child )
        {
            if ( $child instanceof DOMText )
            {
                $out .= $child->data;
                continue;
            }
            if ( !$child instanceof DOMElement )
                continue;
            $name = $child->localName;
            if ( $name === 'embed' || $name === 'embed-inline' || $name === 'object' )
            {
                $target = '';
                foreach ( array( 'object_remote_id', 'node_remote_id', 'href', 'object_id', 'node_id' ) as $attributeName )
                {
                    if ( $child->getAttribute( $attributeName ) !== '' )
                    {
                        $target = $child->getAttribute( $attributeName );
                        break;
                    }
                }
                $out .= '[' . $name . ( $target !== '' ? ' ' . $target : '' ) . ']';
                continue;
            }
            if ( $name === 'td' || $name === 'th' )
            {
                $out .= self::xmlTextPart( $child ) . "\t";
                continue;
            }
            $inner = self::xmlTextPart( $child );
            $out .= !empty( $blocks[$name] ) ? "\n" . $inner . "\n" : $inner;
        }
        return $out;
    }

    /**
     * $node's canonical XML (exclusive C14N, no comments): equal for equal content however it was
     * written. The node is copied into a document of its own first: C14N() of a node that is not
     * part of its document's tree (a datatype's freshly serialized attribute, a clone) gives an
     * empty string, which would make every such value look equal.
     */
    static function canonicalXML( DOMNode $node )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->appendChild( $dom->importNode( $node, true ) );
        $canonical = $dom->documentElement->C14N( true, false );
        return $canonical === false ? (string)$dom->saveXML( $dom->documentElement ) : $canonical;
    }

    /** $node's XML, indented, on its own (namespaces it uses declared). */
    static function prettyXML( DOMNode $node )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->formatOutput = true;
        $dom->appendChild( $dom->importNode( $node, true ) );
        return trim( (string)$dom->saveXML( $dom->documentElement ) );
    }

    // ------------------------------------------------------------------ comparing objects

    /**
     * Compares a package object with the site's (either may be null: an object only in the package,
     * or only on the site). Returns array( 'status', 'fields', 'lang_package', 'lang_site',
     * 'placement', 'class_changed' ), and with $withDetail also 'languages' => array( language =>
     * array( 'state' (both|package|site), 'rows' => list ) ) and 'placement_rows'.
     */
    static function compareObjectData( $package, $site, $withDetail )
    {
        $result = array( 'status' => 'identical', 'fields' => 0, 'lang_package' => 0, 'lang_site' => 0,
                         'placement' => false, 'class_changed' => false, 'languages' => array(), 'placement_rows' => array() );
        if ( $package === null && $site === null )
            return $result;
        if ( $site === null )
            $result['status'] = 'new';
        elseif ( $package === null )
            $result['status'] = 'removed';

        $packageLanguages = $package ? $package['languages'] : array();
        $siteLanguages = $site ? $site['languages'] : array();
        $languages = array_unique( array_merge( array_keys( $packageLanguages ), array_keys( $siteLanguages ) ) );
        foreach ( $languages as $language )
        {
            $inPackage = isset( $packageLanguages[$language] );
            $inSite = isset( $siteLanguages[$language] );
            if ( $package && $site )
            {
                if ( $inPackage && !$inSite )
                    ++$result['lang_package'];
                elseif ( $inSite && !$inPackage )
                    ++$result['lang_site'];
            }
            $packageValues = $inPackage ? $packageLanguages[$language] : array();
            $siteValues = $inSite ? $siteLanguages[$language] : array();
            $rows = array();
            foreach ( array_unique( array_merge( array_keys( $packageValues ), array_keys( $siteValues ) ) ) as $identifier )
            {
                $packageValue = isset( $packageValues[$identifier] ) ? $packageValues[$identifier] : null;
                $siteValue = isset( $siteValues[$identifier] ) ? $siteValues[$identifier] : null;
                if ( $packageValue !== null && $siteValue !== null )
                    $state = self::valuesEqual( $packageValue, $siteValue ) ? 'identical' : 'changed';
                else
                    $state = $packageValue !== null ? 'package_only' : 'site_only';
                // Values of a translation only one side has are counted as that translation, not one by one
                if ( $state !== 'identical' && $package && $site && $inPackage && $inSite )
                    ++$result['fields'];
                if ( $withDetail )
                {
                    $name = $package && isset( $package['names'][$identifier] ) && $package['names'][$identifier] !== '' ? $package['names'][$identifier]
                          : ( $site && isset( $site['names'][$identifier] ) ? $site['names'][$identifier] : $identifier );
                    $rows[] = array( 'identifier' => $identifier, 'name' => $name, 'state' => $state,
                                     'package' => $packageValue, 'site' => $siteValue );
                }
            }
            if ( $withDetail )
                $result['languages'][$language] = array( 'state' => $inPackage && $inSite ? 'both' : ( $inPackage ? 'package' : 'site' ), 'rows' => $rows );
        }

        if ( $package && $site )
        {
            $result['class_changed'] = $package['class_identifier'] !== '' && $package['class_identifier'] !== $site['class_identifier'];
            $packagePlaces = self::placementLines( $package['placement'], array() );
            $sitePlaces = self::placementLines( $site['placement'], $package['placement'] );
            $result['placement'] = $packagePlaces !== $sitePlaces;
            if ( $withDetail )
                $result['placement_rows'] = array( 'package' => $packagePlaces, 'site' => $sitePlaces );
            if ( $result['fields'] > 0 || $result['lang_package'] > 0 || $result['lang_site'] > 0 || $result['placement'] || $result['class_changed'] )
                $result['status'] = 'changed';
        }
        elseif ( $withDetail )
        {
            $result['placement_rows'] = array( 'package' => $package ? self::placementLines( $package['placement'], array() ) : array(),
                                               'site' => $site ? self::placementLines( $site['placement'], array() ) : array() );
        }
        return $result;
    }

    /**
     * Node placements as sorted lines "node <remote id> under <parent remote id> (main)". A top node
     * of the package is placed where the installer is told to put it, so its parent is left out on
     * both sides ($packagePlacement says, for the site's lines, which node remote ids are top nodes).
     */
    static function placementLines( array $placement, array $packagePlacement )
    {
        $top = array();
        foreach ( $packagePlacement as $place )
        {
            if ( $place['top'] )
                $top[$place['node_remote_id']] = true;
        }
        $lines = array();
        foreach ( $placement as $place )
        {
            $isTop = $place['top'] || isset( $top[$place['node_remote_id']] );
            $lines[] = array( 'node_remote_id' => $place['node_remote_id'],
                              'parent_remote_id' => $isTop ? '' : $place['parent_remote_id'],
                              'main' => (bool)$place['main'], 'top' => $isTop );
        }
        usort( $lines, function ( $a, $b ) { return strcmp( $a['node_remote_id'] . $a['parent_remote_id'], $b['node_remote_id'] . $b['parent_remote_id'] ); } );
        return $lines;
    }

    // ------------------------------------------------------------------ classes

    /** A package's class file read into the form compareClassData() takes, or null. */
    static function readPackageClass( eZPackage $package, $relativePath )
    {
        $path = eZPackageFileBrowser::filePath( $package, $relativePath );
        if ( $path === false )
            return null;
        $dom = self::loadDocument( $path );
        if ( !$dom || !$dom->documentElement || $dom->documentElement->localName !== 'content-class' )
            return null;
        return self::classData( $dom->documentElement );
    }

    /** The site's class $identifier, serialized as a package would carry it and read by the same parser, or null if the site has no such class. */
    static function siteClassData( $identifier )
    {
        if ( (string)$identifier === '' )
            return null;
        $class = eZContentClass::fetchByIdentifier( $identifier );
        if ( !$class instanceof eZContentClass )
            return null;
        $node = eZContentClassPackageHandler::classDOMTree( $class );
        if ( !$node instanceof DOMElement )
            return null;
        $data = self::classData( $node );
        $data['id'] = (int)$class->attribute( 'id' );
        return $data;
    }

    /** A <content-class> element: its identifier, remote id, names and properties, and its attributes by identifier. */
    static function classData( DOMElement $root )
    {
        self::unformat( $root );
        $child = function ( DOMElement $parent, $name )
        {
            foreach ( eZPackageComparison::childrenByLocalName( $parent, $name ) as $node )
                return $node;
            return null;
        };
        $text = function ( DOMElement $parent, $name ) use ( $child )
        {
            $node = $child( $parent, $name );
            return $node ? (string)$node->textContent : '';
        };
        $names = self::nameList( $text( $root, 'serialized-name-list' ) );
        $data = array(
            'identifier' => $text( $root, 'identifier' ),
            'remote_id' => $text( $root, 'remote-id' ),
            'name' => $names ? reset( $names ) : $text( $root, 'identifier' ),
            'properties' => array(
                'names' => self::linesOf( $names ),
                'descriptions' => self::linesOf( self::nameList( $text( $root, 'serialized-description-list' ) ) ),
                'object_name_pattern' => $text( $root, 'object-name-pattern' ),
                'url_alias_pattern' => $text( $root, 'url-alias-pattern' ),
                'is_container' => (string)$root->getAttribute( 'is-container' ),
                'always_available' => (string)$root->getAttribute( 'always-available' ),
                'sort' => $root->getAttribute( 'sort-field' ) . ' ' . $root->getAttribute( 'sort-order' ),
                'remote_id' => $text( $root, 'remote-id' ),
            ),
            'attributes' => array(),
        );
        foreach ( self::childrenByLocalName( $root, 'attributes' ) as $attributesNode )
        {
            foreach ( self::childrenByLocalName( $attributesNode, 'attribute' ) as $attributeNode )
            {
                $identifier = $text( $attributeNode, 'identifier' );
                if ( $identifier === '' )
                    continue;
                $parameters = $child( $attributeNode, 'datatype-parameters' );
                $parameterXML = '';
                $parameterText = array();
                if ( $parameters )
                {
                    foreach ( $parameters->childNodes as $parameter )
                    {
                        if ( $parameter instanceof DOMElement )
                        {
                            $parameterXML .= self::canonicalXML( $parameter );
                            $parameterText[] = self::prettyXML( $parameter );
                        }
                    }
                }
                $attributeNames = self::nameList( $text( $attributeNode, 'serialized-name-list' ) );
                $data['attributes'][$identifier] = array(
                    'datatype' => (string)$attributeNode->getAttribute( 'datatype' ),
                    'names' => self::linesOf( $attributeNames ),
                    'name' => $attributeNames ? reset( $attributeNames ) : $identifier,
                    'descriptions' => self::linesOf( self::nameList( $text( $attributeNode, 'serialized-description-list' ) ) ),
                    'required' => (string)$attributeNode->getAttribute( 'required' ),
                    'searchable' => (string)$attributeNode->getAttribute( 'searchable' ),
                    'information_collector' => (string)$attributeNode->getAttribute( 'information-collector' ),
                    'translatable' => (string)$attributeNode->getAttribute( 'translatable' ),
                    'category' => $text( $attributeNode, 'category' ),
                    'placement' => $text( $attributeNode, 'placement' ),
                    'parameters' => $parameterXML,
                    'parameters_text' => implode( "\n", $parameterText ),
                );
            }
        }
        return $data;
    }

    /** A serialized name list (class or attribute names, descriptions): language => text, without the 'always-available' marker, sorted by language. */
    static function nameList( $serialized )
    {
        $list = @unserialize( (string)$serialized, array( 'allowed_classes' => false ) );
        if ( !is_array( $list ) )
            return array();
        $out = array();
        foreach ( $list as $language => $name )
        {
            // An empty entry (a description nobody wrote in that language) says nothing either way
            if ( is_string( $language ) && $language !== 'always-available' && is_scalar( $name ) && (string)$name !== '' )
                $out[$language] = (string)$name;
        }
        ksort( $out );
        return $out;
    }

    protected static function linesOf( array $list )
    {
        $lines = array();
        foreach ( $list as $language => $text )
            $lines[] = $language . ': ' . $text;
        return implode( "\n", $lines );
    }

    /**
     * Compares a package class with the site's class of the same identifier (null: the site has none).
     * Returns array( 'status' (new|changed|identical), 'fields' (differences), 'attributes' =>
     * array( 'added', 'site_only', 'datatype', 'names', 'other' ) counts ), and with $withDetail also
     * 'property_rows' and 'attribute_rows' (each array( 'identifier', 'name', 'state', 'package', 'site', 'aspects' )).
     */
    static function compareClassData( array $package, $site, $withDetail )
    {
        $result = array( 'status' => $site ? 'identical' : 'new', 'fields' => 0,
                         'attributes' => array( 'added' => 0, 'site_only' => 0, 'datatype' => 0, 'names' => 0, 'other' => 0 ),
                         'property_rows' => array(), 'attribute_rows' => array() );
        $labels = self::classPropertyLabels();
        foreach ( $package['properties'] as $property => $packageValue )
        {
            $siteValue = $site ? $site['properties'][$property] : null;
            $state = $site === null ? 'package_only' : ( $siteValue === $packageValue ? 'identical' : 'changed' );
            if ( $state === 'changed' )
                ++$result['fields'];
            if ( $withDetail )
                $result['property_rows'][] = array( 'identifier' => $property, 'name' => $labels[$property], 'state' => $state,
                                                    'package' => self::textValue( $packageValue ), 'site' => $siteValue === null ? null : self::textValue( $siteValue ) );
        }

        $siteAttributes = $site ? $site['attributes'] : array();
        foreach ( array_unique( array_merge( array_keys( $package['attributes'] ), array_keys( $siteAttributes ) ) ) as $identifier )
        {
            $packageAttribute = isset( $package['attributes'][$identifier] ) ? $package['attributes'][$identifier] : null;
            $siteAttribute = isset( $siteAttributes[$identifier] ) ? $siteAttributes[$identifier] : null;
            $aspects = array();
            if ( $packageAttribute && $siteAttribute )
            {
                foreach ( $packageAttribute as $aspect => $aspectValue )
                {
                    if ( $aspect !== 'name' && $aspect !== 'parameters_text' && $aspectValue !== $siteAttribute[$aspect] )
                        $aspects[] = $aspect;
                }
                $state = $aspects ? 'changed' : 'identical';
            }
            else
                $state = $packageAttribute ? 'package_only' : 'site_only';

            if ( $site )
            {
                if ( $state === 'package_only' )
                    ++$result['attributes']['added'];
                elseif ( $state === 'site_only' )
                    ++$result['attributes']['site_only'];
                elseif ( $state === 'changed' )
                {
                    if ( in_array( 'datatype', $aspects, true ) )
                        ++$result['attributes']['datatype'];
                    elseif ( in_array( 'names', $aspects, true ) )
                        ++$result['attributes']['names'];
                    else
                        ++$result['attributes']['other'];
                }
                if ( $state !== 'identical' )
                    ++$result['fields'];
            }
            if ( $withDetail )
            {
                $result['attribute_rows'][] = array(
                    'identifier' => $identifier,
                    'name' => $packageAttribute ? $packageAttribute['name'] : $siteAttribute['name'],
                    'state' => $state,
                    'aspects' => $aspects,
                    'package' => $packageAttribute ? self::textValue( self::classAttributeText( $packageAttribute ) ) : null,
                    'site' => $siteAttribute ? self::textValue( self::classAttributeText( $siteAttribute ) ) : null,
                );
            }
        }
        if ( $site && $result['fields'] > 0 )
            $result['status'] = 'changed';
        return $result;
    }

    protected static function classPropertyLabels()
    {
        return array(
            'names' => ezpI18n::tr( 'design/admin/package', 'Name' ),
            'descriptions' => ezpI18n::tr( 'design/admin/package', 'Description' ),
            'object_name_pattern' => ezpI18n::tr( 'design/admin/package', 'Object name pattern' ),
            'url_alias_pattern' => ezpI18n::tr( 'design/admin/package', 'URL alias name pattern' ),
            'is_container' => ezpI18n::tr( 'design/admin/package', 'Container' ),
            'always_available' => ezpI18n::tr( 'design/admin/package', 'Always available' ),
            'sort' => ezpI18n::tr( 'design/admin/package', 'Default sorting of children' ),
            'remote_id' => ezpI18n::tr( 'design/admin/package', 'Remote ID' ),
        );
    }

    /** A class attribute's definition as lines of text, for the side by side view. */
    protected static function classAttributeText( array $attribute )
    {
        $yes = ezpI18n::tr( 'design/admin/package', 'Yes' );
        $no = ezpI18n::tr( 'design/admin/package', 'No' );
        $flag = function ( $value ) use ( $yes, $no ) { return $value === 'true' ? $yes : $no; };
        $lines = array(
            ezpI18n::tr( 'design/admin/package', 'Datatype' ) . ': ' . $attribute['datatype'],
            ezpI18n::tr( 'design/admin/package', 'Name' ) . ":\n" . $attribute['names'],
        );
        if ( $attribute['descriptions'] !== '' )
            $lines[] = ezpI18n::tr( 'design/admin/package', 'Description' ) . ":\n" . $attribute['descriptions'];
        $lines[] = ezpI18n::tr( 'design/admin/package', 'Required' ) . ': ' . $flag( $attribute['required'] );
        $lines[] = ezpI18n::tr( 'design/admin/package', 'Searchable' ) . ': ' . $flag( $attribute['searchable'] );
        $lines[] = ezpI18n::tr( 'design/admin/package', 'Information collector' ) . ': ' . $flag( $attribute['information_collector'] );
        $lines[] = ezpI18n::tr( 'design/admin/package', 'Translatable' ) . ': ' . $flag( $attribute['translatable'] );
        if ( $attribute['category'] !== '' )
            $lines[] = ezpI18n::tr( 'design/admin/package', 'Category' ) . ': ' . $attribute['category'];
        $lines[] = ezpI18n::tr( 'design/admin/package', 'Placement' ) . ': ' . $attribute['placement'];
        if ( $attribute['parameters_text'] !== '' )
            $lines[] = ezpI18n::tr( 'design/admin/package', 'Datatype parameters' ) . ":\n" . $attribute['parameters_text'];
        return implode( "\n", $lines );
    }

    /** A plain text as a value of the form normalizeValue() gives. */
    protected static function textValue( $text )
    {
        return array( 'datatype' => '', 'kind' => 'text', 'compare' => (string)$text, 'text' => (string)$text, 'raw' => null, 'ids' => array(), 'note' => '' );
    }

    // ------------------------------------------------------------------ paging and details

    /**
     * Filters and pages the index. $options: 'status', 'class', 'search' (name or remote id,
     * case-insensitive), 'offset' (a number or 'last'), 'limit'. Returns array( 'items' (this page),
     * 'total_all', 'total_filtered', 'offset', 'limit', 'page', 'pages', 'counts' (per status, over
     * the items the class and search filters leave), 'classes' (identifier => count, all items) ).
     */
    static function filteredPage( array $index, array $options )
    {
        $status = isset( $options['status'] ) ? (string)$options['status'] : '';
        $class = isset( $options['class'] ) ? (string)$options['class'] : '';
        $search = isset( $options['search'] ) ? trim( (string)$options['search'] ) : '';
        $offsetOption = isset( $options['offset'] ) ? $options['offset'] : 0;
        $limit = max( 1, isset( $options['limit'] ) ? (int)$options['limit'] : 50 );

        $counts = array_fill_keys( self::statuses(), 0 );
        $classes = array();
        $filtered = array();
        foreach ( $index['items'] as $item )
        {
            if ( $item['class_identifier'] !== '' )
                $classes[$item['class_identifier']] = isset( $classes[$item['class_identifier']] ) ? $classes[$item['class_identifier']] + 1 : 1;
            if ( $class !== '' && $item['class_identifier'] !== $class )
                continue;
            if ( $search !== '' && mb_stripos( $item['name'], $search ) === false && stripos( $item['remote_id'], $search ) === false )
                continue;
            ++$counts[$item['status']];
            if ( $status !== '' && $item['status'] !== $status )
                continue;
            $filtered[] = $item;
        }
        ksort( $classes );
        $sort = isset( $options['sort'] ) && in_array( $options['sort'], self::sortFields(), true ) ? $options['sort'] : '';
        if ( $sort !== '' )
        {
            $descending = isset( $options['dir'] ) && $options['dir'] === 'desc';
            $statusOrder = array_flip( self::statuses() );
            // Stable: equal keys keep the package's own order (the item's index), in both directions
            usort( $filtered, function ( $a, $b ) use ( $sort, $descending, $statusOrder )
            {
                switch ( $sort )
                {
                    case 'status':
                        $cmp = $statusOrder[$a['status']] - $statusOrder[$b['status']];
                        break;
                    case 'name':
                        $cmp = strcasecmp( $a['name'], $b['name'] );
                        break;
                    case 'class':
                        $cmp = strcmp( $a['class_identifier'], $b['class_identifier'] );
                        break;
                    default:
                        $cmp = eZPackageComparison::differenceCount( $a ) - eZPackageComparison::differenceCount( $b );
                }
                if ( $descending )
                    $cmp = -$cmp;
                return $cmp !== 0 ? $cmp : $a['index'] - $b['index'];
            } );
        }
        $total = count( $filtered );
        if ( $offsetOption === 'last' )
            $offset = $total > 0 ? (int)( floor( ( $total - 1 ) / $limit ) * $limit ) : 0;
        else
        {
            $offset = max( 0, (int)$offsetOption );
            if ( $offset >= $total && $total > 0 )
                $offset = (int)( floor( ( $total - 1 ) / $limit ) * $limit );
        }
        return array(
            'items' => array_slice( $filtered, $offset, $limit ),
            'total_all' => count( $index['items'] ),
            'total_filtered' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'page' => $total > 0 ? (int)floor( $offset / $limit ) + 1 : 1,
            'pages' => $total > 0 ? (int)ceil( $total / $limit ) : 1,
            'counts' => $counts,
            'classes' => $classes,
            'filtered_indices' => array_map( function ( $item ) { return $item['index']; }, $filtered ),
        );
    }

    /** The columns filteredPage() sorts by. */
    static function sortFields()
    {
        return array( 'status', 'name', 'class', 'differences' );
    }

    /**
     * How many differences an item has, for sorting: values, translations only on one side,
     * placement and class for an object; changed settings and attributes for a class. An item that
     * is new, only on the site or without its class counts none (it has nothing to compare with).
     */
    static function differenceCount( array $item )
    {
        if ( $item['status'] !== 'changed' )
            return 0;
        return (int)$item['fields'] + (int)$item['lang_package'] + (int)$item['lang_site'] + ( $item['placement'] ? 1 : 0 ) + ( $item['class_changed'] ? 1 : 0 );
    }

    /**
     * The index of $package as last cached, whatever the stamp it was cached under (after an import
     * the site's stamp has moved on, and refreshItems() brings the index up to date), or null.
     */
    static function latestCachedIndex( eZPackage $package )
    {
        $newest = null;
        foreach ( (array)glob( self::cacheDirectory() . '/' . self::safeName( $package->attribute( 'name' ) ) . '-*.cache' ) as $file )
        {
            if ( is_file( $file ) && ( $newest === null || filemtime( $file ) > filemtime( $newest ) ) )
                $newest = $file;
        }
        if ( $newest === null )
            return null;
        $data = @unserialize( (string)@file_get_contents( $newest ), array( 'allowed_classes' => false ) );
        return is_array( $data ) && isset( $data['cache_version'] ) && $data['cache_version'] === self::CACHE_VERSION ? $data : null;
    }

    /**
     * Compares the items $indices of the cached index again - after an import changed exactly those
     * on the site - and caches the index under the site's new stamp, so the next page is not a
     * full rebuild. Every other item keeps what it had. With no cached index nothing is done (the
     * next page builds one). Returns the refreshed index or null.
     */
    static function refreshItems( eZPackage $package, array $indices )
    {
        $index = self::latestCachedIndex( $package );
        if ( $index === null )
            return null;
        $db = eZDB::instance();
        $db->begin();
        $sources = self::packageSources( $package );
        // The import may have added classes and class attributes in this very request
        $classes = self::siteClasses( true );
        self::siteClassAttributes( true );
        $objects = array();
        foreach ( array_unique( array_map( 'intval', $indices ) ) as $i )
        {
            if ( !isset( $index['items'][$i] ) )
                continue;
            $item = $index['items'][$i];
            if ( $item['kind'] === 'class' )
            {
                $packageClass = self::readPackageClass( $package, $item['file'] );
                if ( $packageClass === null )
                    continue;
                $siteClass = self::siteClassData( $packageClass['identifier'] );
                $result = self::compareClassData( $packageClass, $siteClass, false );
                $index['items'][$i]['status'] = $result['status'];
                $index['items'][$i]['fields'] = $result['fields'];
                $index['items'][$i]['attributes'] = $result['attributes'];
                $index['items'][$i]['site_id'] = $siteClass ? $siteClass['id'] : null;
            }
            elseif ( $item['file'] !== null )
                $objects[$i] = $item;
        }
        $documents = array();
        $packageData = array();
        foreach ( $objects as $i => $item )
        {
            $node = self::packageObjectNode( $package, array( 'file' => $item['file'], 'position' => $item['position'] ), $documents );
            if ( $node )
                $packageData[$i] = self::packageObjectData( $package, $node, $sources['top_nodes'] );
        }
        $remoteIDs = array();
        foreach ( $packageData as $data )
            $remoteIDs[] = $data['remote_id'];
        $siteObjects = array();
        foreach ( array_chunk( $remoteIDs, self::BATCH_SIZE ) as $chunk )
            $siteObjects += self::siteObjectsData( $chunk, 'remote_id' );
        foreach ( $packageData as $i => $data )
        {
            $site = isset( $siteObjects[$data['remote_id']] ) ? $siteObjects[$data['remote_id']] : null;
            $result = self::compareObjectData( $data, $site, false );
            if ( $site === null && !self::siteHasClass( $classes, $data['class_identifier'], $data['class_remote_id'] ) )
                $result['status'] = 'class_missing';
            foreach ( array( 'status', 'fields', 'lang_package', 'lang_site', 'placement', 'class_changed' ) as $key )
                $index['items'][$i][$key] = $result[$key];
            $index['items'][$i]['site_id'] = $site ? $site['id'] : null;
        }
        $db->rollback();

        $index['stamp'] = self::stamp( $package );
        $index['built'] = time();
        self::forget( $package->attribute( 'name' ) );
        $file = self::cacheFilePath( $package->attribute( 'name' ), $index['stamp'] );
        eZFile::create( basename( $file ), dirname( $file ), serialize( $index ), true );
        $index['from_cache'] = false;
        return $index;
    }

    /**
     * One item of the index compared again in full, for the viewer: every value of both sides with
     * a word level difference for each changed one (site value first: red is what the site has and
     * the package does not, green what the package brings). Returns null if the item is gone.
     */
    static function itemDetail( eZPackage $package, array $item )
    {
        self::siteClasses( true );
        self::siteClassAttributes( true );
        if ( $item['kind'] === 'class' )
        {
            $packageClass = self::readPackageClass( $package, $item['file'] );
            if ( $packageClass === null )
                return null;
            $siteClass = self::siteClassData( $packageClass['identifier'] );
            $result = self::compareClassData( $packageClass, $siteClass, true );
            $sections = array(
                array( 'title' => ezpI18n::tr( 'design/admin/package', 'Class' ), 'language' => '', 'state' => 'both',
                       'rows' => self::detailRows( $result['property_rows'] ) ),
                array( 'title' => ezpI18n::tr( 'design/admin/package', 'Attributes' ), 'language' => '', 'state' => 'both',
                       'rows' => self::detailRows( $result['attribute_rows'] ) ),
            );
            return array(
                'kind' => 'class',
                'status' => $result['status'],
                'site' => $siteClass ? array( 'id' => $siteClass['id'], 'name' => $siteClass['name'] ) : null,
                'sections' => $sections,
                'placement' => null,
                'notes' => array(),
            );
        }

        $db = eZDB::instance();
        $db->begin();
        $packageData = null;
        $topNodes = array();
        if ( $item['file'] !== null )
        {
            $sources = self::packageSources( $package );
            $topNodes = $sources['top_nodes'];
            $documents = array();
            $node = self::packageObjectNode( $package, array( 'file' => $item['file'], 'position' => $item['position'] ), $documents );
            if ( $node )
                $packageData = self::packageObjectData( $package, $node, $topNodes );
            if ( !$packageData || $packageData['remote_id'] !== $item['remote_id'] )
            {
                $db->rollback();
                return null;
            }
            $siteObjects = self::siteObjectsData( array( $item['remote_id'] ), 'remote_id' );
            $site = isset( $siteObjects[$item['remote_id']] ) ? $siteObjects[$item['remote_id']] : null;
        }
        else
        {
            $siteObjects = self::siteObjectsData( array( (int)$item['site_id'] ), 'id' );
            $site = isset( $siteObjects[(int)$item['site_id']] ) ? $siteObjects[(int)$item['site_id']] : null;
        }
        $db->rollback();
        $result = self::compareObjectData( $packageData, $site, true );
        if ( $packageData && !$site && !self::siteHasClass( self::siteClasses(), $packageData['class_identifier'], $packageData['class_remote_id'] ) )
            $result['status'] = 'class_missing';

        // Relations are compared by remote id and shown with the name of the object pointed to
        $names = self::relationNames( $result['languages'] );
        $sections = array();
        foreach ( $result['languages'] as $language => $languageResult )
        {
            foreach ( $languageResult['rows'] as &$row )
            {
                foreach ( array( 'package', 'site' ) as $side )
                {
                    if ( $row[$side] && $row[$side]['kind'] === 'relation' )
                    {
                        $lines = array();
                        foreach ( $row[$side]['ids'] as $remoteID )
                            $lines[] = ( isset( $names[$remoteID] ) ? $names[$remoteID] . ' ' : '' ) . '[' . $remoteID . ']';
                        $row[$side]['text'] = implode( "\n", $lines );
                    }
                }
            }
            unset( $row );
            $sections[] = array( 'title' => $language, 'language' => $language, 'state' => $languageResult['state'],
                                 'rows' => self::detailRows( $languageResult['rows'] ) );
        }

        $notes = array();
        if ( $result['class_changed'] )
            $notes[] = ezpI18n::tr( 'design/admin/package', 'The package has this object as a %package object, the site as a %site object.', null,
                                    array( '%package' => $packageData['class_identifier'], '%site' => $site['class_identifier'] ) );
        if ( $site && $site['status'] === eZContentObject::STATUS_ARCHIVED )
            $notes[] = ezpI18n::tr( 'design/admin/package', 'The object is in the trash on the site.' );
        if ( $result['status'] === 'class_missing' )
            $notes[] = ezpI18n::tr( 'design/admin/package', 'The site has no class "%class"; installing the package needs it (the package may bring it).', null,
                                    array( '%class' => $packageData['class_identifier'] ) );

        $placement = self::placementDetail( $result['placement_rows'] );
        return array(
            'kind' => 'object',
            'status' => $result['status'],
            'site' => $site ? array( 'id' => $site['id'], 'name' => $site['name'], 'node_id' => $site['main_node_id'],
                                     'class_identifier' => $site['class_identifier'], 'modified' => $site['modified'],
                                     'languages' => array_keys( $site['languages'] ) ) : null,
            'package_info' => $packageData ? array( 'modified' => $packageData['modified'], 'languages' => array_keys( $packageData['languages'] ),
                                                    'class_identifier' => $packageData['class_identifier'] ) : null,
            'sections' => $sections,
            'placement' => $placement,
            'notes' => $notes,
        );
    }

    /** Names of the site's objects every relation value in $languages points to: remote id => name, one query. */
    protected static function relationNames( array $languages )
    {
        $ids = array();
        foreach ( $languages as $languageResult )
        {
            foreach ( $languageResult['rows'] as $row )
            {
                foreach ( array( 'package', 'site' ) as $side )
                {
                    if ( $row[$side] && $row[$side]['kind'] === 'relation' )
                    {
                        foreach ( $row[$side]['ids'] as $remoteID )
                            $ids[$remoteID] = true;
                    }
                }
            }
        }
        $names = array();
        if ( !$ids )
            return $names;
        $db = eZDB::instance();
        $quoted = array();
        foreach ( array_slice( array_keys( $ids ), 0, 1000 ) as $remoteID )
            $quoted[] = "'" . $db->escapeString( (string)$remoteID ) . "'";
        foreach ( (array)$db->arrayQuery( 'SELECT remote_id, name FROM ezcontentobject WHERE remote_id IN ( ' . implode( ', ', $quoted ) . ' )' ) as $row )
            $names[$row['remote_id']] = $row['name'];
        return $names;
    }

    /** Rows for the viewer: each with 'state', the two texts, the word level runs where they differ, the raw XML and note texts. */
    protected static function detailRows( array $rows )
    {
        $out = array();
        foreach ( $rows as $row )
        {
            $packageValue = $row['package'];
            $siteValue = $row['site'];
            $packageText = $packageValue ? $packageValue['text'] : '';
            $siteText = $siteValue ? $siteValue['text'] : '';
            $detail = array(
                'identifier' => $row['identifier'],
                'name' => $row['name'],
                'state' => $row['state'],
                'datatype' => $packageValue && $packageValue['datatype'] !== '' ? $packageValue['datatype'] : ( $siteValue ? $siteValue['datatype'] : '' ),
                'site_datatype' => $siteValue ? $siteValue['datatype'] : '',
                'aspects' => isset( $row['aspects'] ) ? $row['aspects'] : array(),
                'package_text' => $packageText,
                'site_text' => $siteText,
                'package_raw' => $packageValue ? $packageValue['raw'] : null,
                'site_raw' => $siteValue ? $siteValue['raw'] : null,
                'site_runs' => array(),
                'package_runs' => array(),
                'notes' => array(),
            );
            if ( $row['state'] === 'changed' )
            {
                $sides = eZPackageComparisonDiff::sides( $siteText, $packageText );
                $detail['site_runs'] = $sides['old'];
                $detail['package_runs'] = $sides['new'];
                if ( $packageText === $siteText )
                    $detail['notes'][] = $packageValue && $packageValue['kind'] === 'xmltext'
                                       ? ezpI18n::tr( 'design/admin/package', 'The text is the same; its markup differs (see the XML).' )
                                       : ezpI18n::tr( 'design/admin/package', 'The shown values are the same; the stored values differ.' );
            }
            if ( $packageValue && $siteValue && $packageValue['datatype'] !== '' && $siteValue['datatype'] !== '' && $packageValue['datatype'] !== $siteValue['datatype'] )
                $detail['notes'][] = ezpI18n::tr( 'design/admin/package', 'Datatype in the package: %package, on the site: %site.', null,
                                                  array( '%package' => $packageValue['datatype'], '%site' => $siteValue['datatype'] ) );
            foreach ( array( $packageValue, $siteValue ) as $value )
            {
                if ( $value && $value['note'] !== '' )
                {
                    $note = self::noteText( $value['note'] );
                    if ( !in_array( $note, $detail['notes'], true ) )
                        $detail['notes'][] = $note;
                }
            }
            $out[] = $detail;
        }
        return $out;
    }

    static function noteText( $code )
    {
        switch ( $code )
        {
            case 'serialized':
                return ezpI18n::tr( 'design/admin/package', 'Compared by the serialized XML of the value.' );
            case 'datatype_missing':
                return ezpI18n::tr( 'design/admin/package', 'The datatype is not available on the site; its stored value is shown.' );
            case 'site_unreadable':
                return ezpI18n::tr( 'design/admin/package', 'The site value could not be serialized by its datatype.' );
            case 'file_missing':
                return ezpI18n::tr( 'design/admin/package', 'The file itself is not at hand; compared by name and size.' );
        }
        return (string)$code;
    }

    /** The placement lines of both sides as display rows: array( 'changed' => bool, 'package' => lines, 'site' => lines ), each line array( 'text', 'state' ). */
    protected static function placementDetail( array $placementRows )
    {
        if ( !$placementRows )
            return null;
        $format = function ( array $line )
        {
            $text = ezpI18n::tr( 'design/admin/package', 'Node %node', null, array( '%node' => $line['node_remote_id'] ) );
            if ( $line['top'] )
                $text .= ' - ' . ezpI18n::tr( 'design/admin/package', 'top node, placed where the installer is told' );
            elseif ( $line['parent_remote_id'] !== '' )
                $text .= ' - ' . ezpI18n::tr( 'design/admin/package', 'under node %parent', null, array( '%parent' => $line['parent_remote_id'] ) );
            if ( $line['main'] )
                $text .= ' (' . ezpI18n::tr( 'design/admin/package', 'main' ) . ')';
            return $text;
        };
        $packageLines = array_map( $format, $placementRows['package'] );
        $siteLines = array_map( $format, $placementRows['site'] );
        $out = array( 'changed' => $packageLines !== $siteLines && $placementRows['package'] && $placementRows['site'], 'package' => array(), 'site' => array() );
        foreach ( $packageLines as $line )
            $out['package'][] = array( 'text' => $line, 'state' => in_array( $line, $siteLines, true ) || !$siteLines ? 'identical' : 'added' );
        foreach ( $siteLines as $line )
            $out['site'][] = array( 'text' => $line, 'state' => in_array( $line, $packageLines, true ) || !$packageLines ? 'identical' : 'removed' );
        return $out;
    }

    /** Direct child elements of $node by local name, whatever namespace prefix they are written with. */
    static function childrenByLocalName( DOMNode $node, $localName )
    {
        $out = array();
        foreach ( $node->childNodes as $child )
        {
            if ( $child instanceof DOMElement && $child->localName === $localName )
                $out[] = $child;
        }
        return $out;
    }
}

?>
