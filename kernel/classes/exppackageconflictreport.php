<?php
/**
 * File containing the expPackageConflictReport class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What installing a package would meet on this installation, by remote id, without writing anything: for every
 * content object of the package whether an object with its remote id exists (and of which class), whether its class
 * is there, and where each of its locations would go (its own node remote id already in the tree, the parent in the
 * tree, in the package, or nowhere); for every class of the package whether a class with its remote id or
 * identifier exists. The same for packages in the XML and in the JSON format (documents are read through
 * eZPackage::fetchDOMFromFile()).
 *
 * Planned actions per object: create (new remote id), update (the remote id exists, same class: an install updates
 * it, an install as drafts adds a draft), conflict (the remote id exists with another class), error (the class is
 * neither installed nor in the package).
 */
class expPackageConflictReport
{
    /**
     * @param eZPackage $package
     * @param int $limit the most objects listed (the counts cover all)
     * @return array objects (rows), classes (rows), counts (action => n), skipped_items (drafts: what is not installed)
     */
    static function build( eZPackage $package, $limit = 5000 )
    {
        $report = array( 'package' => (string)$package->attribute( 'name' ), 'format' => (string)$package->attribute( 'format' ),
                         'objects' => array(), 'classes' => array(), 'other_items' => array(),
                         'counts' => array( 'create' => 0, 'update' => 0, 'conflict' => 0, 'error' => 0 ), 'object_total' => 0 );
        $packageClasses = array();
        $packageNodes = array();
        $objectNodes = array();
        $path = $package->path();

        foreach ( (array)$package->attribute( 'install' ) as $item )
        {
            $type = isset( $item['type'] ) ? $item['type'] : '';
            $filename = isset( $item['filename'] ) ? $item['filename'] : '';
            $sub = isset( $item['sub-directory'] ) ? $item['sub-directory'] : '';
            if ( $type !== 'ezcontentobject' && $type !== 'ezcontentclass' )
            {
                $report['other_items'][] = array( 'type' => $type, 'name' => $filename !== '' ? $filename : ( isset( $item['name'] ) ? $item['name'] : '' ) );
                continue;
            }
            if ( $filename === '' )
                continue;
            $dom = eZPackage::fetchDOMFromFile( $path . '/' . ( $sub !== '' ? $sub . '/' : '' ) . $filename . '.xml' );
            if ( !$dom )
                continue;
            if ( $type === 'ezcontentclass' )
            {
                $root = $dom->documentElement;
                $identifier = self::childText( $root, 'identifier' );
                $remoteID = self::childText( $root, 'remote-id' );
                $packageClasses[$remoteID] = $identifier;
                $packageClasses['identifier:' . $identifier] = $identifier;
                $class = $remoteID !== '' ? eZContentClass::fetchByRemoteID( $remoteID ) : null;
                if ( !$class && $identifier !== '' )
                    $class = eZContentClass::fetchByIdentifier( $identifier );
                $report['classes'][] = array( 'identifier' => $identifier, 'remote_id' => $remoteID,
                                              'exists' => (bool)$class, 'class_id' => $class ? (int)$class->attribute( 'id' ) : null,
                                              'same_remote_id' => $class ? $class->attribute( 'remote_id' ) === $remoteID : null );
                continue;
            }
            foreach ( $dom->getElementsByTagName( 'object' ) as $objectNode )
                $objectNodes[] = $objectNode;
            foreach ( $dom->getElementsByTagName( 'object-file' ) as $objectFileNode )
            {
                $objectDOM = eZPackage::fetchDOMFromFile( $path . '/ezcontentobject/' . $objectFileNode->getAttribute( 'filename' ) );
                if ( $objectDOM && $objectDOM->documentElement && $objectDOM->documentElement->localName === 'object' )
                    $objectNodes[] = $objectDOM->documentElement;
            }
        }

        // the node remote ids the package brings, for parents within it
        foreach ( $objectNodes as $objectNode )
            foreach ( $objectNode->getElementsByTagName( 'node-assignment' ) as $assignment )
                $packageNodes[$assignment->getAttribute( 'remote-id' )] = true;

        $ns = 'http://ez.no/ezobject';
        foreach ( $objectNodes as $objectNode )
        {
            ++$report['object_total'];
            $remoteID = $objectNode->getAttribute( 'remote_id' );
            $classIdentifier = $objectNode->getAttributeNS( $ns, 'class_identifier' );
            $classRemoteID = $objectNode->getAttribute( 'class_remote_id' );
            $class = $classRemoteID !== '' ? eZContentClass::fetchByRemoteID( $classRemoteID ) : null;
            if ( !$class && $classIdentifier !== '' )
                $class = eZContentClass::fetchByIdentifier( $classIdentifier );
            $classState = $class ? 'installed' : ( isset( $packageClasses[$classRemoteID] ) || isset( $packageClasses['identifier:' . $classIdentifier] ) ? 'in_package' : 'missing' );
            $existing = $remoteID !== '' ? eZContentObject::fetchByRemoteID( $remoteID ) : null;
            $row = array( 'remote_id' => $remoteID, 'name' => $objectNode->getAttribute( 'name' ),
                          'class_identifier' => $classIdentifier, 'class' => $classState,
                          'exists' => (bool)$existing, 'object_id' => $existing ? (int)$existing->attribute( 'id' ) : null,
                          'existing_class' => $existing ? (string)$existing->attribute( 'class_identifier' ) : null,
                          'current_version' => $existing ? (int)$existing->attribute( 'current_version' ) : null,
                          'modified' => $existing ? (int)$existing->attribute( 'modified' ) : null,
                          'languages' => array(), 'locations' => array() );
            foreach ( $objectNode->getElementsByTagName( 'object-translation' ) as $translation )
                $row['languages'][$translation->getAttribute( 'language' )] = true;
            $row['languages'] = array_keys( $row['languages'] );
            $seen = array();
            foreach ( $objectNode->getElementsByTagName( 'node-assignment' ) as $assignment )
            {
                $nodeRemoteID = $assignment->getAttribute( 'remote-id' );
                if ( isset( $seen[$nodeRemoteID] ) )
                    continue;
                $seen[$nodeRemoteID] = true;
                $parentRemoteID = $assignment->getAttribute( 'parent-node-remote-id' );
                $nodeObjectID = $nodeRemoteID !== '' ? self::nodeObjectID( $nodeRemoteID ) : false;
                $node = $nodeObjectID !== false;
                if ( $parentRemoteID === '' )
                    $parent = 'top';
                elseif ( self::nodeObjectID( $parentRemoteID ) !== false )
                    $parent = 'installed';
                elseif ( isset( $packageNodes[$parentRemoteID] ) )
                    $parent = 'in_package';
                else
                    $parent = 'missing';
                $row['locations'][] = array( 'remote_id' => $nodeRemoteID, 'parent_remote_id' => $parentRemoteID,
                                             'main' => $assignment->getAttribute( 'is-main-node' ) === '1',
                                             'node_exists' => (bool)$node,
                                             'node_of_other_object' => $node && ( !$existing || $nodeObjectID !== (int)$existing->attribute( 'id' ) ),
                                             'parent' => $parent );
            }
            if ( $classState === 'missing' )
                $row['action'] = 'error';
            elseif ( $existing && $class && (int)$existing->attribute( 'contentclass_id' ) !== (int)$class->attribute( 'id' ) )
                $row['action'] = 'conflict';
            elseif ( $existing )
                $row['action'] = 'update';
            else
                $row['action'] = 'create';
            foreach ( $row['locations'] as $location )
                if ( $location['node_of_other_object'] )
                    $row['action'] = 'conflict';
            ++$report['counts'][$row['action']];
            if ( count( $report['objects'] ) < $limit )
                $report['objects'][] = $row;
        }
        return $report;
    }

    /**
     * The object of the node with this remote id, read from the tree (no language filter; node 1 has object 0).
     * @param string $remoteID
     * @return int|false
     */
    private static function nodeObjectID( $remoteID )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT contentobject_id FROM ezcontentobject_tree WHERE remote_id = '" . $db->escapeString( $remoteID ) . "'", array( 'limit' => 1 ) );
        return $rows ? (int)$rows[0]['contentobject_id'] : false;
    }

    /** @return string the text of the first child element named $name */
    private static function childText( DOMElement $element, $name )
    {
        foreach ( $element->childNodes as $child )
            if ( $child instanceof DOMElement && $child->localName === $name )
                return trim( $child->textContent );
        return '';
    }
}
