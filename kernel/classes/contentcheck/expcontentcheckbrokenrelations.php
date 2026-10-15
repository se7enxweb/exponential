<?php
/**
 * File containing the expContentCheckBrokenRelations class.
 *
 * Visible, published content that points at something a visitor cannot reach (doc/features/6.0/content-checks.md,
 * "Broken relations"). Read only.
 *
 * Sources: published objects whose main location is visible (not hidden, not below a hidden node), below the
 * chosen node and not below an excluded one, that the current user may read. What is checked, in each source's
 * current version:
 *   - its relations in ezcontentobject_link (object relations, relation attributes, and the embed and link
 *     relations the rich text editor registers);
 *   - the links and embeds in its XML fields (XmlDatatypes[], ezxmltext by default): object_id="..." and
 *     node_id="..." of <link>, <embed> and <embed-inline>. These still name an object or node after it has been
 *     deleted, when its relation rows are gone with it.
 * A target is reported as deleted (no object or node with the id), in the trash, hidden (every location hidden or
 * invisible), unpublished (a draft that was never published) or without location.
 *
 * The scan walks the sources by object id in chunks (ChunkSize objects, a fixed number of queries per chunk) and
 * stops after a page of findings or after ScanLimit objects; the next page goes on after the last object checked.
 * No query reads more than one chunk of sources, so a large installation is checked page by page, never as a whole.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentCheckBrokenRelations
{
    /** The kinds of broken targets */
    const KINDS = array( 'deleted', 'trash', 'hidden', 'unpublished', 'no_location' );

    /** The kinds of references: in the text of an XML field, or an object relation (also a relation attribute) */
    const RELATIONS = array( 'xml', 'object' );

    /** At most this many subtrees are excluded */
    const MAX_EXCLUDED = 20;

    /**
     * The parameters of a scan from the request, each checked.
     *
     * @param array $input NodeID, Exclude (node ids, array or text), Kind (array), Relation (array), After (object id), Limit
     * @param int $defaultNodeID the node checked when the request names none
     * @return array array( 'node_id', 'exclude', 'kinds', 'relations', 'after', 'limit' )
     */
    public static function parameters( array $input, $defaultNodeID = 1 )
    {
        $kinds = isset( $input['Kind'] ) && is_array( $input['Kind'] ) ? array_values( array_intersect( self::KINDS, $input['Kind'] ) ) : array();
        $relations = isset( $input['Relation'] ) && is_array( $input['Relation'] ) ? array_values( array_intersect( self::RELATIONS, $input['Relation'] ) ) : array();
        $limit = isset( $input['Limit'] ) ? (int)$input['Limit'] : 25;
        return array( 'node_id' => expContentCheck::id( isset( $input['NodeID'] ) ? $input['NodeID'] : null, $defaultNodeID ),
                      'exclude' => expContentCheck::idList( isset( $input['Exclude'] ) ? $input['Exclude'] : array(), self::MAX_EXCLUDED ),
                      'kinds' => $kinds ? $kinds : self::KINDS,
                      'relations' => $relations ? $relations : self::RELATIONS,
                      'after' => isset( $input['After'] ) && is_scalar( $input['After'] ) && preg_match( '/^[0-9]{1,10}$/', (string)$input['After'] ) ? (int)$input['After'] : 0,
                      'limit' => max( 1, min( 200, $limit ) ) );
    }

    /**
     * The query of one chunk of sources (no database access).
     *
     * @param string $subtreePattern from expContentCheck::subtreePattern(); '/1/%' is the whole tree
     * @param string[] $excludePatterns subtree patterns left out
     * @param array $permission array( 'from', 'where' ) of the content/read condition
     * @param int $after the last object id already checked
     * @return string
     */
    public static function sourceQuery( $subtreePattern, array $excludePatterns, array $permission, $after )
    {
        foreach ( array_merge( array( $subtreePattern ), $excludePatterns ) as $pattern )
        {
            if ( !preg_match( '#^/([0-9]+/)+%$#', (string)$pattern ) )
                throw new InvalidArgumentException( 'not a subtree pattern' );
        }
        $where = array( 'ezcontentobject_tree.contentobject_id = ezcontentobject.id',
                        'ezcontentobject_tree.node_id = ezcontentobject_tree.main_node_id',
                        "ezcontentobject_tree.path_string LIKE '" . $subtreePattern . "'",
                        'ezcontentobject_tree.is_invisible = 0',
                        'ezcontentobject_tree.is_hidden = 0',
                        'ezcontentobject.status = ' . eZContentObject::STATUS_PUBLISHED,
                        'ezcontentobject.id > ' . max( 0, (int)$after ) );
        foreach ( $excludePatterns as $pattern )
            $where[] = "ezcontentobject_tree.path_string NOT LIKE '" . $pattern . "'";
        return 'SELECT ezcontentobject.id AS object_id, ezcontentobject.current_version AS current_version,' .
               ' ezcontentobject.name AS object_name, ezcontentobject.contentclass_id AS class_id,' .
               ' ezcontentobject_tree.node_id AS node_id, ezcontentobject_tree.path_identification_string AS node_path' .
               ' FROM ezcontentobject, ezcontentobject_tree' . ( isset( $permission['from'] ) ? (string)$permission['from'] : '' ) .
               ' WHERE ' . implode( ' AND ', $where ) . ( isset( $permission['where'] ) ? ' ' . (string)$permission['where'] : '' ) .
               ' ORDER BY ezcontentobject.id ASC';
    }

    /**
     * The links and embeds in the text of an XML field.
     *
     * @param string $xml
     * @return array list of array( 'type' => object|node, 'id' => int, 'tag' => link|embed|embed-inline ), each once
     */
    public static function xmlReferences( $xml )
    {
        $xml = (string)$xml;
        if ( $xml === '' || ( strpos( $xml, 'object_id=' ) === false && strpos( $xml, 'node_id=' ) === false ) )
            return array();
        $found = array();
        if ( preg_match_all( '/<(link|embed-inline|embed)\b([^>]*)>/', $xml, $tags, PREG_SET_ORDER ) )
        {
            foreach ( $tags as $tag )
            {
                if ( !preg_match_all( '/(?<![A-Za-z0-9_:-])(object_id|node_id)\s*=\s*"([0-9]{1,10})"/', $tag[2], $ids, PREG_SET_ORDER ) )
                    continue;
                foreach ( $ids as $id )
                {
                    $type = $id[1] === 'object_id' ? 'object' : 'node';
                    $key = $type . ':' . (int)$id[2] . ':' . $tag[1];
                    if ( (int)$id[2] > 0 && !isset( $found[$key] ) )
                        $found[$key] = array( 'type' => $type, 'id' => (int)$id[2], 'tag' => $tag[1] );
                }
            }
        }
        return array_values( $found );
    }

    /**
     * The state of an object a source refers to (no database access).
     *
     * @param array|null $object array( 'status' => int ) or null when there is no such object
     * @param array|null $visibility array( 'locations' => int, 'visible' => int ) of its nodes, or null for none
     * @return string|null a kind of KINDS, or null when it is fine
     */
    public static function objectState( $object, $visibility )
    {
        if ( !is_array( $object ) )
            return 'deleted';
        $status = (int)$object['status'];
        if ( $status === eZContentObject::STATUS_ARCHIVED )
            return 'trash';
        if ( $status !== eZContentObject::STATUS_PUBLISHED )
            return 'unpublished';
        if ( !is_array( $visibility ) || (int)$visibility['locations'] === 0 )
            return 'no_location';
        if ( (int)$visibility['visible'] === 0 )
            return 'hidden';
        return null;
    }

    /**
     * The state of a node a source refers to (no database access).
     *
     * @param array|null $node array( 'is_hidden', 'is_invisible' ) or null when there is no such node
     * @param bool $inTrash whether the trash holds the node
     * @return string|null
     */
    public static function nodeState( $node, $inTrash )
    {
        if ( !is_array( $node ) )
            return $inTrash ? 'trash' : 'deleted';
        if ( (int)$node['is_hidden'] === 1 || (int)$node['is_invisible'] === 1 )
            return 'hidden';
        return null;
    }

    /**
     * Whether the relation types of a row of ezcontentobject_link are of the kind asked for. The type is a bit mask:
     * 1 common, 2 embed, 4 link, 8 attribute; 0 is a row from before the types, an object relation.
     *
     * @param int $relationType
     * @param string[] $relations
     * @return bool
     */
    public static function relationMatches( $relationType, array $relations )
    {
        $relationType = (int)$relationType;
        $xml = ( $relationType & ( eZContentObject::RELATION_EMBED | eZContentObject::RELATION_LINK ) ) !== 0;
        $object = $relationType === 0 || ( $relationType & ( eZContentObject::RELATION_COMMON | eZContentObject::RELATION_ATTRIBUTE ) ) !== 0;
        return ( $xml && in_array( 'xml', $relations, true ) ) || ( $object && in_array( 'object', $relations, true ) );
    }

    /**
     * One page of the scan.
     *
     * @param array $params from parameters()
     * @return array array( 'node' => start node or null, 'excluded' => nodes, 'findings' => list (see collect()),
     *               'checked' => objects checked, 'last' => last object id checked, 'done' => true when no object
     *               is left after it, 'error' => '' | not_found | database )
     */
    public static function scan( array $params )
    {
        $result = array( 'node' => null, 'excluded' => array(), 'findings' => array(), 'checked' => 0,
                         'last' => (int)$params['after'], 'done' => true, 'error' => '' );
        if ( !expContentCheck::isSupportedDatabase() )
            return array_merge( $result, array( 'error' => 'database' ) );
        $node = expContentCheck::readableNode( $params['node_id'] );
        $pattern = $node ? expContentCheck::subtreePattern( $node->attribute( 'path_string' ) ) : null;
        if ( !$node || $pattern === null )
            return array_merge( $result, array( 'error' => 'not_found' ) );
        $result['node'] = $node;
        // the pattern takes in the start node itself: '/1/2/58/%' matches '/1/2/58/'

        $excludePatterns = array();
        foreach ( $params['exclude'] as $excludeID )
        {
            $excluded = eZContentObjectTreeNode::fetch( (int)$excludeID );
            if ( !$excluded instanceof eZContentObjectTreeNode )
                continue;
            $excludePattern = expContentCheck::subtreePattern( $excluded->attribute( 'path_string' ) );
            if ( $excludePattern === null )
                continue;
            $excludePatterns[] = $excludePattern;
            $result['excluded'][] = array( 'node_id' => (int)$excludeID, 'name' => $excluded->canRead() ? (string)$excluded->attribute( 'name' ) : '' );
        }

        $chunk = expContentCheck::intSetting( 'BrokenRelationsSettings', 'ChunkSize', 200, 10, 1000 );
        $scanLimit = expContentCheck::intSetting( 'BrokenRelationsSettings', 'ScanLimit', 5000, $chunk, 100000 );
        $db = eZDB::instance();
        $permission = expContentCheckUnreferenced::permission( $node->attribute( 'path_string' ), (int)$node->attribute( 'node_id' ) );
        try
        {
            $after = (int)$params['after'];
            while ( true )
            {
                $sources = $db->arrayQuery( self::sourceQuery( $pattern, $excludePatterns, $permission, $after ),
                                            array( 'offset' => 0, 'limit' => $chunk ) );
                $sources = is_array( $sources ) ? array_values( $sources ) : array();
                if ( !$sources )
                {
                    $result['done'] = true;
                    break;
                }
                // a page ends after a whole object, never in the middle of the findings of one source
                $bySource = array();
                foreach ( self::collect( $sources, $params ) as $finding )
                    $bySource[$finding['source_id']][] = $finding;
                $stopped = false;
                $index = 0;
                foreach ( $sources as $index => $source )
                {
                    $after = (int)$source['object_id'];
                    $result['checked']++;
                    if ( isset( $bySource[$after] ) )
                        $result['findings'] = array_merge( $result['findings'], $bySource[$after] );
                    if ( count( $result['findings'] ) >= $params['limit'] || $result['checked'] >= $scanLimit )
                    {
                        $stopped = true;
                        break;
                    }
                }
                $result['last'] = $after;
                $lastOfAll = count( $sources ) < $chunk && $index === count( $sources ) - 1;
                if ( $stopped || $lastOfAll )
                {
                    $result['done'] = $lastOfAll;
                    break;
                }
            }
        }
        finally
        {
            if ( !empty( $permission['temp_tables'] ) )
                $db->dropTempTableList( $permission['temp_tables'] );
        }
        $result['findings'] = self::describeSources( $result['findings'] );
        return $result;
    }

    /**
     * The broken references of a chunk of sources: three queries for the sources' relations and XML fields, three
     * for the state of everything they refer to.
     *
     * @param array $sources rows of sourceQuery()
     * @param array $params
     * @return array list of array( 'source_id', 'source_name', 'source_node_id', 'source_path', 'class_id',
     *               'attribute_id' (class attribute, 0 for a relation of the object), 'language', 'relation' (xml|object),
     *               'via' (link, embed, embed-inline, relation, attribute), 'target_type' (object|node), 'target_id',
     *               'kind' )
     */
    public static function collect( array $sources, array $params )
    {
        if ( !$sources )
            return array();
        $db = eZDB::instance();
        $sourceByID = array();
        foreach ( $sources as $source )
            $sourceByID[(int)$source['object_id']] = $source;
        $in = expContentCheck::inList( array_keys( $sourceByID ) );

        // what each source refers to, keyed so that one target is reported once per source, field and way
        $references = array();
        if ( in_array( 'object', $params['relations'], true ) || in_array( 'xml', $params['relations'], true ) )
        {
            $links = $db->arrayQuery( 'SELECT ezcontentobject_link.from_contentobject_id AS source_id, ezcontentobject_link.to_contentobject_id AS target_id,' .
                                      ' ezcontentobject_link.contentclassattribute_id AS attribute_id, ezcontentobject_link.relation_type AS relation_type' .
                                      ' FROM ezcontentobject_link, ezcontentobject' .
                                      ' WHERE ezcontentobject_link.from_contentobject_id IN ( ' . $in . ' )' .
                                      ' AND ezcontentobject.id = ezcontentobject_link.from_contentobject_id' .
                                      ' AND ezcontentobject_link.from_contentobject_version = ezcontentobject.current_version' );
            foreach ( is_array( $links ) ? $links : array() as $link )
            {
                $type = (int)$link['relation_type'];
                if ( !self::relationMatches( $type, $params['relations'] ) )
                    continue;
                $isXml = ( $type & ( eZContentObject::RELATION_EMBED | eZContentObject::RELATION_LINK ) ) !== 0
                         && ( $type & ( eZContentObject::RELATION_COMMON | eZContentObject::RELATION_ATTRIBUTE ) ) === 0;
                $via = ( $type & eZContentObject::RELATION_ATTRIBUTE ) ? 'attribute'
                     : ( $isXml ? ( ( $type & eZContentObject::RELATION_EMBED ) ? 'embed' : 'link' ) : 'relation' );
                $references[] = array( 'source_id' => (int)$link['source_id'], 'attribute_id' => (int)$link['attribute_id'],
                                       'language' => '', 'relation' => $isXml ? 'xml' : 'object', 'via' => $via,
                                       'target_type' => 'object', 'target_id' => (int)$link['target_id'], 'from_table' => true );
            }
        }
        if ( in_array( 'xml', $params['relations'], true ) )
        {
            $types = array();
            foreach ( self::xmlDatatypes() as $type )
                $types[] = "'" . $db->escapeString( $type ) . "'";
            $attributes = $types ? $db->arrayQuery( 'SELECT ezcontentobject_attribute.contentobject_id AS source_id,' .
                                                    ' ezcontentobject_attribute.contentclassattribute_id AS attribute_id,' .
                                                    ' ezcontentobject_attribute.language_code AS language, ezcontentobject_attribute.data_text AS data_text' .
                                                    ' FROM ezcontentobject_attribute, ezcontentobject' .
                                                    ' WHERE ezcontentobject_attribute.contentobject_id IN ( ' . $in . ' )' .
                                                    ' AND ezcontentobject.id = ezcontentobject_attribute.contentobject_id' .
                                                    ' AND ezcontentobject_attribute.version = ezcontentobject.current_version' .
                                                    ' AND ezcontentobject_attribute.data_type_string IN ( ' . implode( ', ', $types ) . ' )' .
                                                    " AND ( ezcontentobject_attribute.data_text LIKE '%object_id=%' OR ezcontentobject_attribute.data_text LIKE '%node_id=%' )" ) : array();
            foreach ( is_array( $attributes ) ? $attributes : array() as $attribute )
            {
                foreach ( self::xmlReferences( (string)$attribute['data_text'] ) as $reference )
                    $references[] = array( 'source_id' => (int)$attribute['source_id'], 'attribute_id' => (int)$attribute['attribute_id'],
                                           'language' => (string)$attribute['language'], 'relation' => 'xml', 'via' => $reference['tag'],
                                           'target_type' => $reference['type'], 'target_id' => $reference['id'], 'from_table' => false );
            }
        }
        if ( !$references )
            return array();

        // the state of every target, in three queries
        $objectIDs = array();
        $nodeIDs = array();
        foreach ( $references as $reference )
        {
            if ( $reference['target_type'] === 'object' )
                $objectIDs[$reference['target_id']] = true;
            else
                $nodeIDs[$reference['target_id']] = true;
        }
        $objects = array();
        $visibility = array();
        $nodes = array();
        $trashed = array();
        foreach ( array_chunk( array_keys( $objectIDs ), 500 ) as $ids )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT id, status FROM ezcontentobject WHERE id IN ( ' . expContentCheck::inList( $ids ) . ' )' ) as $row )
                $objects[(int)$row['id']] = array( 'status' => (int)$row['status'] );
            foreach ( (array)$db->arrayQuery( 'SELECT contentobject_id, COUNT(*) AS locations,' .
                                              ' SUM( CASE WHEN is_hidden = 0 AND is_invisible = 0 THEN 1 ELSE 0 END ) AS visible' .
                                              ' FROM ezcontentobject_tree WHERE contentobject_id IN ( ' . expContentCheck::inList( $ids ) . ' )' .
                                              ' GROUP BY contentobject_id' ) as $row )
                $visibility[(int)$row['contentobject_id']] = array( 'locations' => (int)$row['locations'], 'visible' => (int)$row['visible'] );
        }
        foreach ( array_chunk( array_keys( $nodeIDs ), 500 ) as $ids )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT node_id, is_hidden, is_invisible FROM ezcontentobject_tree WHERE node_id IN ( ' . expContentCheck::inList( $ids ) . ' )' ) as $row )
                $nodes[(int)$row['node_id']] = $row;
            foreach ( (array)$db->arrayQuery( 'SELECT node_id FROM ezcontentobject_trash WHERE node_id IN ( ' . expContentCheck::inList( $ids ) . ' )' ) as $row )
                $trashed[(int)$row['node_id']] = true;
        }

        $findings = array();
        foreach ( $references as $reference )
        {
            $targetID = $reference['target_id'];
            if ( $reference['target_type'] === 'object' )
            {
                // the object itself: a page that embeds itself is not broken by that
                if ( $targetID === $reference['source_id'] )
                    continue;
                $kind = self::objectState( isset( $objects[$targetID] ) ? $objects[$targetID] : null, isset( $visibility[$targetID] ) ? $visibility[$targetID] : null );
            }
            else
                $kind = self::nodeState( isset( $nodes[$targetID] ) ? $nodes[$targetID] : null, isset( $trashed[$targetID] ) );
            if ( $kind === null || !in_array( $kind, $params['kinds'], true ) )
                continue;
            $key = $reference['source_id'] . '|' . $reference['target_type'] . '|' . $targetID . '|' . $reference['relation'];
            // the relation row of an XML link and the link in the text are one finding; the text says which field
            if ( isset( $findings[$key] ) )
            {
                if ( !$reference['from_table'] && $findings[$key]['from_table'] )
                    $findings[$key] = array_merge( $reference, array( 'kind' => $kind ) );
                continue;
            }
            $findings[$key] = array_merge( $reference, array( 'kind' => $kind ) );
        }

        $list = array();
        foreach ( $findings as $finding )
        {
            $source = $sourceByID[$finding['source_id']];
            unset( $finding['from_table'] );
            $list[] = array_merge( $finding, array( 'source_name' => (string)$source['object_name'],
                                                    'source_node_id' => (int)$source['node_id'],
                                                    'source_path' => (string)$source['node_path'],
                                                    'class_id' => (int)$source['class_id'] ) );
        }
        usort( $list, function ( $a, $b ) {
            return array( $a['source_id'], $a['attribute_id'], $a['target_id'] ) <=> array( $b['source_id'], $b['attribute_id'], $b['target_id'] );
        } );
        return $list;
    }

    /**
     * The findings of a page with the names the page shows: the class and the field of the source, the name of a
     * target that still exists, and whether the user may edit the source. A fixed number of queries per page.
     *
     * @param array $findings from collect()
     * @return array
     */
    public static function describeSources( array $findings )
    {
        if ( !$findings )
            return array();
        $classNames = expContentCheckUnreferenced::classNames();
        $attributeIDs = array();
        $sourceIDs = array();
        $targetObjectIDs = array();
        foreach ( $findings as $finding )
        {
            if ( $finding['attribute_id'] > 0 )
                $attributeIDs[$finding['attribute_id']] = true;
            $sourceIDs[$finding['source_id']] = true;
            if ( $finding['target_type'] === 'object' && $finding['kind'] !== 'deleted' )
                $targetObjectIDs[$finding['target_id']] = true;
        }
        $db = eZDB::instance();
        $fieldNames = array();
        if ( $attributeIDs )
        {
            $attributes = eZContentClassAttribute::fetchFilteredList( array( 'id' => array( array_keys( $attributeIDs ) ),
                                                                              'version' => eZContentClass::VERSION_STATUS_DEFINED ) );
            foreach ( is_array( $attributes ) ? $attributes : array() as $attribute )
                $fieldNames[(int)$attribute->attribute( 'id' )] = (string)$attribute->attribute( 'name' );
        }
        // the name of a target only when the user may read it; else its id alone
        $targetNames = array();
        if ( $targetObjectIDs )
        {
            $targets = eZContentObject::fetchIDArray( array_keys( $targetObjectIDs ) );
            foreach ( is_array( $targets ) ? $targets : array() as $target )
            {
                if ( $target->canRead() )
                    $targetNames[(int)$target->attribute( 'id' )] = (string)$target->attribute( 'name' );
            }
        }
        $canEdit = array();
        $objects = eZContentObject::fetchIDArray( array_keys( $sourceIDs ) );
        foreach ( is_array( $objects ) ? $objects : array() as $object )
            $canEdit[(int)$object->attribute( 'id' )] = (bool)$object->canEdit();

        $described = array();
        foreach ( $findings as $finding )
        {
            $finding['class_name'] = isset( $classNames[$finding['class_id']] ) ? $classNames[$finding['class_id']] : '';
            $finding['field_name'] = isset( $fieldNames[$finding['attribute_id']] ) ? $fieldNames[$finding['attribute_id']] : '';
            $finding['target_name'] = $finding['target_type'] === 'object' && isset( $targetNames[$finding['target_id']] ) ? $targetNames[$finding['target_id']] : '';
            $finding['can_edit'] = !empty( $canEdit[$finding['source_id']] );
            $described[] = $finding;
        }
        return $described;
    }

    /**
     * The datatypes whose text is read for links and embeds.
     *
     * @return string[]
     */
    public static function xmlDatatypes()
    {
        $types = expContentCheck::setting( 'BrokenRelationsSettings', 'XmlDatatypes', array( 'ezxmltext' ) );
        $clean = array();
        foreach ( is_array( $types ) ? $types : array() as $type )
        {
            if ( is_string( $type ) && preg_match( '/^[a-z0-9_]{1,50}$/', $type ) )
                $clean[] = $type;
        }
        return array_values( array_unique( $clean ) );
    }
}

?>
