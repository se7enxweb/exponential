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
 *   - the data of its relation fields (ezobjectrelation, ezobjectrelationlist, ngenhancedlink), which name a target
 *     also where no relation row was written (an import; an enhanced link never writes one).
 * With the first page, for a user who may read all content: the layouts (layoutFindings()), whose manual
 * collection items, dynamic collection parents, block links and rule targets name a node or object.
 * Each finding carries a hint (hint()): remove the reference, restore, reveal or publish the target, or give it a
 * location.
 * A target is reported as deleted (no object or node with the id), in the trash, hidden (every location hidden or
 * invisible), unpublished (a draft that was never published) or without location. For a user whose content/read is
 * limited, every target the user may not read (no readable location) is reported as unavailable, whatever its state,
 * and without its id: deleted, trashed, hidden and fine targets the user may not read look the same, so the list says
 * nothing about content the user may not see. Sources the user may not read are left out of the query itself, and
 * so of the pages, the number of objects checked and the paging.
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
    const KINDS = array( 'deleted', 'trash', 'hidden', 'unpublished', 'no_location', 'unavailable' );

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
     * @param int $upTo when above 0: only objects up to this id (the range a page checked)
     * @return string
     */
    public static function sourceQuery( $subtreePattern, array $excludePatterns, array $permission, $after, $upTo = 0 )
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
        if ( (int)$upTo > 0 )
            $where[] = 'ezcontentobject.id <= ' . (int)$upTo;
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
        $result = array( 'node' => null, 'excluded' => array(), 'findings' => array(), 'layouts' => array(), 'checked' => 0,
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
            // only a node the user may read: an id of one the user may not read is ignored like an unknown one
            $excluded = expContentCheck::readableNode( (int)$excludeID );
            if ( !$excluded instanceof eZContentObjectTreeNode )
                continue;
            $excludePattern = expContentCheck::subtreePattern( $excluded->attribute( 'path_string' ) );
            if ( $excludePattern === null )
                continue;
            $excludePatterns[] = $excludePattern;
            $result['excluded'][] = array( 'node_id' => (int)$excludeID, 'name' => (string)$excluded->attribute( 'name' ) );
        }

        $chunk = expContentCheck::intSetting( 'BrokenRelationsSettings', 'ChunkSize', 200, 10, 1000 );
        $scanLimit = expContentCheck::intSetting( 'BrokenRelationsSettings', 'ScanLimit', 5000, $chunk, 100000 );
        $db = eZDB::instance();
        $permission = expContentCheckUnreferenced::permission( $node->attribute( 'path_string' ), (int)$node->attribute( 'node_id' ) );
        $read = expContentCheck::readPermission();
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
                foreach ( self::collect( $sources, $params, $read ) as $finding )
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
            expContentCheck::dropPermission( $read );
        }
        $result['findings'] = self::describeSources( $result['findings'] );
        // the layouts are global and not content: with the first page, for a user who may read all content
        if ( (int)$params['after'] === 0 && empty( $read['limited'] ) )
            $result['layouts'] = self::layoutFindings( $params['kinds'] );
        return $result;
    }

    /**
     * The broken references of a chunk of sources: three queries for the sources' relations and XML fields, three
     * for the state of everything they refer to.
     *
     * @param array $sources rows of sourceQuery()
     * @param array $params
     * @param array|null $read from expContentCheck::readPermission(); null: the user may read every target
     * @return array list of array( 'source_id', 'source_name', 'source_node_id', 'source_path', 'class_id',
     *               'attribute_id' (class attribute, 0 for a relation of the object), 'language', 'relation' (xml|object),
     *               'via' (link, embed, embed-inline, relation, attribute), 'target_type' (object|node), 'target_id',
     *               'kind' )
     */
    public static function collect( array $sources, array $params, $read = null )
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
        if ( in_array( 'object', $params['relations'], true ) )
        {
            // relation fields by their data: a relation list or an enhanced link names its target there, also when
            // no relation row was written (an import, an enhanced link never writes one)
            $attributes = $db->arrayQuery( 'SELECT ezcontentobject_attribute.contentobject_id AS source_id,' .
                                           ' ezcontentobject_attribute.contentclassattribute_id AS attribute_id,' .
                                           ' ezcontentobject_attribute.language_code AS language, ezcontentobject_attribute.data_type_string AS datatype,' .
                                           ' ezcontentobject_attribute.data_int AS data_int, ezcontentobject_attribute.data_text AS data_text' .
                                           ' FROM ezcontentobject_attribute, ezcontentobject' .
                                           ' WHERE ezcontentobject_attribute.contentobject_id IN ( ' . $in . ' )' .
                                           ' AND ezcontentobject.id = ezcontentobject_attribute.contentobject_id' .
                                           ' AND ezcontentobject_attribute.version = ezcontentobject.current_version' .
                                           " AND ezcontentobject_attribute.data_type_string IN ( 'ezobjectrelation', 'ezobjectrelationlist', 'ngenhancedlink' )" );
            foreach ( is_array( $attributes ) ? $attributes : array() as $attribute )
            {
                foreach ( self::attributeTargets( (string)$attribute['datatype'], (int)$attribute['data_int'], (string)$attribute['data_text'] ) as $target )
                    $references[] = array( 'source_id' => (int)$attribute['source_id'], 'attribute_id' => (int)$attribute['attribute_id'],
                                           'language' => (string)$attribute['language'], 'relation' => 'object', 'via' => 'attribute',
                                           'target_type' => $target['type'], 'target_id' => $target['id'], 'from_table' => false );
            }
        }
        if ( !$references )
            return array();

        // an enhanced link's id may name an object or a node: it is judged as whichever exists
        foreach ( $references as $i => $reference )
        {
            if ( $reference['target_type'] !== 'either' )
                continue;
            $exists = $db->arrayQuery( 'SELECT id FROM ezcontentobject WHERE id = ' . (int)$reference['target_id'] );
            if ( !$exists && $db->arrayQuery( 'SELECT node_id FROM ezcontentobject_tree WHERE node_id = ' . (int)$reference['target_id'] ) )
                $references[$i]['target_type'] = 'node';
            else
                $references[$i]['target_type'] = 'object';
        }

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

        // for a user with a limited content/read: the targets the user may read (a readable location), in two queries
        $limited = is_array( $read ) && !empty( $read['limited'] );
        $readableObjects = array();
        $readableNodes = array();
        if ( $limited )
        {
            foreach ( array_chunk( array_keys( $objectIDs ), 500 ) as $ids )
            {
                foreach ( (array)$db->arrayQuery( 'SELECT DISTINCT ezcontentobject.id AS id FROM ezcontentobject, ezcontentobject_tree' . $read['from'] .
                                                  ' WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id' .
                                                  ' AND ezcontentobject.id IN ( ' . expContentCheck::inList( $ids ) . ' ) ' . $read['where'] ) as $row )
                    $readableObjects[(int)$row['id']] = true;
            }
            foreach ( array_chunk( array_keys( $nodeIDs ), 500 ) as $ids )
            {
                foreach ( (array)$db->arrayQuery( 'SELECT ezcontentobject_tree.node_id AS id FROM ezcontentobject_tree, ezcontentobject' . $read['from'] .
                                                  ' WHERE ezcontentobject.id = ezcontentobject_tree.contentobject_id' .
                                                  ' AND ezcontentobject_tree.node_id IN ( ' . expContentCheck::inList( $ids ) . ' ) ' . $read['where'] ) as $row )
                    $readableNodes[(int)$row['id']] = true;
            }
        }

        $findings = array();
        foreach ( $references as $reference )
        {
            $targetID = $reference['target_id'];
            if ( $reference['target_type'] === 'object' && $targetID === $reference['source_id'] )
                continue; // the object itself: a page that embeds itself is not broken by that
            $readable = $reference['target_type'] === 'object' ? isset( $readableObjects[$targetID] ) : isset( $readableNodes[$targetID] );
            if ( $limited && !$readable )
                $kind = 'unavailable';
            else if ( $reference['target_type'] === 'object' )
                $kind = self::objectState( isset( $objects[$targetID] ) ? $objects[$targetID] : null, isset( $visibility[$targetID] ) ? $visibility[$targetID] : null );
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
            // a target in the trash or hidden is shown by its id and state only
            if ( $finding['target_type'] === 'object' && !in_array( $finding['kind'], array( 'deleted', 'trash', 'hidden', 'unavailable' ), true ) )
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
        // the name of a target (never published, without location) only when the user may read it; else its id alone
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
            // a target the user may not read: neither its id nor whether it was an object or a node
            if ( $finding['kind'] === 'unavailable' )
            {
                $finding['target_id'] = 0;
                $finding['target_type'] = '';
                $finding['target_name'] = '';
            }
            $finding['can_edit'] = !empty( $canEdit[$finding['source_id']] );
            $finding['hint'] = self::hint( $finding['kind'] );
            $described[] = $finding;
        }
        return $described;
    }

    /**
     * The targets a relation field names by its data (no database access).
     *
     * @param string $datatype ezobjectrelation, ezobjectrelationlist or ngenhancedlink
     * @param int $dataInt
     * @param string $dataText
     * @return array list of array( 'type' => object|either, 'id' => int ); either: an id that may be an object or a node
     */
    public static function attributeTargets( $datatype, $dataInt, $dataText )
    {
        $targets = array();
        if ( $datatype === 'ezobjectrelation' && (int)$dataInt > 0 )
            $targets[] = array( 'type' => 'object', 'id' => (int)$dataInt );
        else if ( $datatype === 'ezobjectrelationlist' && preg_match_all( '/\bcontentobject-id="([0-9]{1,10})"/', (string)$dataText, $m ) )
        {
            foreach ( array_unique( $m[1] ) as $id )
                $targets[] = array( 'type' => 'object', 'id' => (int)$id );
        }
        else if ( $datatype === 'ngenhancedlink' )
        {
            $data = json_decode( (string)$dataText, true );
            if ( is_array( $data ) && ( !isset( $data['type'] ) || $data['type'] === 'internal' ) && isset( $data['id'] ) && (int)$data['id'] > 0 )
                $targets[] = array( 'type' => 'either', 'id' => (int)$data['id'] );
        }
        return $targets;
    }

    /**
     * What to do about a finding: a key the page turns into a sentence.
     *
     * @param string $kind
     * @return string remove | restore | reveal | publish | locate | ask
     */
    public static function hint( $kind )
    {
        $hints = array( 'deleted' => 'remove', 'trash' => 'restore', 'hidden' => 'reveal', 'unpublished' => 'publish',
                        'no_location' => 'locate', 'unavailable' => 'ask' );
        return isset( $hints[$kind] ) ? $hints[$kind] : 'remove';
    }

    /**
     * The layouts that point at a node or object a visitor cannot reach: manual collection items (value_id of a
     * location item is a node id), the parent node of a dynamic collection, links in block parameters
     * (exp-remote-location://, ezlocation://) and rule targets by node or subtree. Only for a user who may read
     * all content (layouts are not content and have no read condition); the page shows them with the first page.
     *
     * @param string[] $kinds the kinds asked for
     * @return array list of array( 'layout_id', 'layout_name', 'where' => item|query|parameter|rule, 'where_id', 'detail',
     *               'target_type' => object|node|remote, 'target_id' (int or remote id), 'kind', 'hint' )
     */
    public static function layoutFindings( array $kinds )
    {
        if ( !expContentCheckReferences::tableExists( 'explayouts_block' ) || !expContentCheckReferences::tableExists( 'explayouts_layout' ) )
            return array();
        $db = eZDB::instance();
        $layouts = array();
        foreach ( (array)$db->arrayQuery( 'SELECT id, name, status FROM explayouts_layout' ) as $row )
            $layouts[(int)$row['id']] = (string)$row['name'];
        $blockLayout = array();
        foreach ( (array)$db->arrayQuery( 'SELECT id, layout_id FROM explayouts_block' ) as $row )
            $blockLayout[(int)$row['id']] = (int)$row['layout_id'];

        // what the layouts refer to: array( layout, where, where id, detail, type, id )
        $refs = array();
        if ( expContentCheckReferences::tableExists( 'explayouts_collection_item' ) )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT i.id AS id, i.value_id AS value_id, i.value_type AS value_type, c.block_id AS block_id' .
                                              ' FROM explayouts_collection_item i, explayouts_collection c WHERE c.id = i.collection_id' ) as $row )
            {
                $type = strpos( strtolower( (string)$row['value_type'] ), 'content' ) !== false ? 'object' : 'node';
                if ( preg_match( '/^[0-9]{1,10}$/', trim( (string)$row['value_id'] ) ) )
                    $refs[] = array( (int)$row['block_id'], 'item', (int)$row['id'], (string)$row['value_type'], $type, (int)$row['value_id'] );
            }
        }
        if ( expContentCheckReferences::tableExists( 'explayouts_collection_query' ) )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT q.id AS id, q.parameters AS parameters, c.block_id AS block_id' .
                                              ' FROM explayouts_collection_query q, explayouts_collection c WHERE c.id = q.collection_id' ) as $row )
            {
                $params = json_decode( (string)$row['parameters'], true );
                if ( !is_array( $params ) || !empty( $params['use_current_location'] ) || empty( $params['parent_location_id'] ) )
                    continue;
                $raw = (int)$params['parent_location_id'];
                $mapped = expContentCheckReferences::remapNode( $raw );
                $refs[] = array( (int)$row['block_id'], 'query', (int)$row['id'], 'parent_location_id', 'node', $mapped ? $mapped : $raw );
            }
        }
        if ( expContentCheckReferences::tableExists( 'explayouts_block_parameter' ) )
        {
            foreach ( (array)$db->arrayQuery( "SELECT id, block_id, name, value FROM explayouts_block_parameter WHERE value LIKE '%://%'" ) as $row )
            {
                $value = (string)$row['value'];
                $decoded = json_decode( $value, true );
                $links = array();
                if ( is_array( $decoded ) )
                    array_walk_recursive( $decoded, function ( $item ) use ( &$links ) { if ( is_string( $item ) ) $links[] = $item; } );
                else
                    $links[] = $value;
                foreach ( $links as $link )
                {
                    $link = trim( $link );
                    if ( preg_match( '#^exp-remote-location://(\S+)$#i', $link, $m ) )
                        $refs[] = array( (int)$row['block_id'], 'parameter', (int)$row['id'], (string)$row['name'], 'remote', $m[1] );
                    else if ( preg_match( '#^ezlocation://([0-9]{1,10})$#i', $link, $m ) )
                        $refs[] = array( (int)$row['block_id'], 'parameter', (int)$row['id'], (string)$row['name'], 'node', (int)$m[1] );
                    else if ( preg_match( '#^ibexa-?location://([0-9]{1,10})$#i', $link, $m ) )
                        $refs[] = array( (int)$row['block_id'], 'parameter', (int)$row['id'], (string)$row['name'], 'node', expContentCheckReferences::remapNode( (int)$m[1] ) ?: (int)$m[1] );
                    else if ( preg_match( '#^(?:ezcontent|ezobject)://([0-9]{1,10})$#i', $link, $m ) )
                        $refs[] = array( (int)$row['block_id'], 'parameter', (int)$row['id'], (string)$row['name'], 'object', (int)$m[1] );
                }
            }
        }
        $ruleLayout = array();
        if ( expContentCheckReferences::tableExists( 'explayouts_rule_target' ) && expContentCheckReferences::tableExists( 'explayouts_rule' ) )
        {
            foreach ( (array)$db->arrayQuery( 'SELECT id, layout_id FROM explayouts_rule' ) as $row )
                $ruleLayout[(int)$row['id']] = (int)$row['layout_id'];
            foreach ( (array)$db->arrayQuery( "SELECT id, rule_id, target_type, target_value FROM explayouts_rule_target WHERE target_type IN ( 'node', 'subtree', 'location' )" ) as $row )
            {
                if ( preg_match( '/^[0-9]{1,10}$/', trim( (string)$row['target_value'] ) ) )
                    $refs[] = array( -(int)$row['rule_id'], 'rule', (int)$row['id'], (string)$row['target_type'], 'node', (int)$row['target_value'] );
            }
        }

        $findings = array();
        foreach ( $refs as $ref )
        {
            list( $owner, $where, $whereID, $detail, $type, $id ) = $ref;
            $kind = self::layoutTargetState( $type, $id );
            if ( $kind === null || !in_array( $kind, $kinds, true ) )
                continue;
            $layoutID = $owner < 0 ? ( isset( $ruleLayout[-$owner] ) ? $ruleLayout[-$owner] : 0 ) : ( isset( $blockLayout[$owner] ) ? $blockLayout[$owner] : 0 );
            $key = $layoutID . '|' . $where . '|' . $whereID . '|' . $type . '|' . $id;
            $findings[$key] = array( 'layout_id' => $layoutID, 'layout_name' => isset( $layouts[$layoutID] ) ? $layouts[$layoutID] : '',
                                     'where' => $where, 'where_id' => $whereID, 'block_id' => $owner > 0 ? $owner : 0,
                                     'rule_id' => $owner < 0 ? -$owner : 0, 'detail' => $detail,
                                     'target_type' => $type, 'target_id' => $id, 'kind' => $kind, 'hint' => self::hint( $kind ) );
        }
        usort( $findings, function ( $a, $b ) {
            return array( $a['layout_id'], $a['where'], $a['where_id'] ) <=> array( $b['layout_id'], $b['where'], $b['where_id'] );
        } );
        return array_values( $findings );
    }

    /**
     * The state of a node, object or remote id a layout names.
     *
     * @param string $type node|object|remote
     * @param int|string $id
     * @return string|null a kind, or null when it is fine
     */
    protected static function layoutTargetState( $type, $id )
    {
        $db = eZDB::instance();
        if ( $type === 'remote' )
        {
            $rows = $db->arrayQuery( "SELECT node_id FROM ezcontentobject_tree WHERE remote_id = '" . $db->escapeString( (string)$id ) . "'" );
            if ( !$rows )
                return 'deleted';
            $type = 'node';
            $id = (int)$rows[0]['node_id'];
        }
        if ( $type === 'object' )
        {
            $object = $db->arrayQuery( 'SELECT status FROM ezcontentobject WHERE id = ' . (int)$id );
            $visibility = $db->arrayQuery( 'SELECT COUNT(*) AS locations, SUM( CASE WHEN is_hidden = 0 AND is_invisible = 0 THEN 1 ELSE 0 END ) AS visible' .
                                           ' FROM ezcontentobject_tree WHERE contentobject_id = ' . (int)$id );
            return self::objectState( $object ? array( 'status' => (int)$object[0]['status'] ) : null,
                                      $visibility && (int)$visibility[0]['locations'] > 0 ? $visibility[0] : null );
        }
        if ( (int)$id <= 0 )
            return 'deleted';
        $node = $db->arrayQuery( 'SELECT is_hidden, is_invisible FROM ezcontentobject_tree WHERE node_id = ' . (int)$id );
        $trash = $node ? array() : $db->arrayQuery( 'SELECT node_id FROM ezcontentobject_trash WHERE node_id = ' . (int)$id );
        return self::nodeState( $node ? $node[0] : null, (bool)$trash );
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
