<?php
/**
 * Which other objects point at the content a removal or a hide is about to take away: the reverse relations
 * (ezcontentobject_link) of the objects that would go, read from the published version of each referring object.
 * Used by content/removeobject (content.ini [RemoveSettings] ReverseRelationCheck = disabled | warn | block) and by
 * content/hide and the Hide action of content/action ([HideSettings] ReverseRelationCheck = disabled | warn).
 * Guide: doc/features/6.0/reverse-relation-check.md
 *
 * An object counts as "taken away" when every one of its locations lies inside the selected subtrees: an object
 * that keeps a location elsewhere stays, and so do the relations to it. A referring object that is taken away as
 * well is not counted. What counts as a reference: every row of ezcontentobject_link whose source is a published
 * object in its current version, of any kind: object relation (common), embed and link in rich text, and relation
 * fields (attribute).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Service;

class ReverseRelationCheck
{
    const DISABLED = 'disabled';
    const WARN = 'warn';
    const BLOCK = 'block';

    /** At most this many references are listed on a page; the total is counted in full */
    const LIST_LIMIT = 100;

    /** Every kind of relation: common, embed, link, attribute */
    const ALL_KINDS = 15;

    /** The POST variable a confirmation page sends back, so that it is not shown a second time */
    const CONFIRMED_POST = 'ReverseRelationCheckConfirmed';

    /**
     * The mode of content/removeobject: content.ini [RemoveSettings] ReverseRelationCheck. Without the setting
     * "warn": the list is added to the confirmation page the view shows anyway, nothing else changes.
     *
     * @return string disabled | warn | block
     */
    public static function removeMode()
    {
        return self::mode( 'RemoveSettings', array( self::DISABLED, self::WARN, self::BLOCK ), self::WARN );
    }

    /**
     * The mode of content/hide and the Hide action: content.ini [HideSettings] ReverseRelationCheck. Without the
     * setting "disabled": a hide that has no confirmation page keeps having none. A hide is never refused: "block"
     * is taken as "warn".
     *
     * @return string disabled | warn
     */
    public static function hideMode()
    {
        $mode = self::mode( 'HideSettings', array( self::DISABLED, self::WARN, self::BLOCK ), self::DISABLED );
        return $mode === self::BLOCK ? self::WARN : $mode;
    }

    /**
     * A mode from content.ini, or the default for a missing or unknown value (an unknown value is logged).
     *
     * @param string $section
     * @param string[] $allowed
     * @param string $default
     * @return string
     */
    protected static function mode( $section, array $allowed, $default )
    {
        $ini = \eZINI::instance( 'content.ini' );
        if ( !$ini->hasVariable( $section, 'ReverseRelationCheck' ) )
            return $default;
        $value = strtolower( trim( (string) $ini->variable( $section, 'ReverseRelationCheck' ) ) );
        return self::normalisedMode( $value, $allowed, $default, $section );
    }

    /**
     * @param string $value
     * @param string[] $allowed
     * @param string $default
     * @param string $section for the log line
     * @return string
     */
    public static function normalisedMode( $value, array $allowed, $default, $section = '' )
    {
        $value = strtolower( trim( (string) $value ) );
        if ( in_array( $value, $allowed, true ) )
            return $value;
        if ( $value !== '' && class_exists( 'eZDebug' ) )
            \eZDebug::writeWarning( "content.ini [$section] ReverseRelationCheck=$value is not one of " . implode( ', ', $allowed ) . ", using $default", __METHOD__ );
        return $default;
    }

    /**
     * Whether a removal must be refused: in mode block, while references exist or could not be checked.
     *
     * @param string $mode
     * @param array|null $check of forNodes()
     * @return bool
     */
    public static function refusesRemoval( $mode, $check )
    {
        return $mode === self::BLOCK && is_array( $check ) && ( $check['total'] > 0 || $check['failed'] );
    }

    /**
     * Whether a hide needs the confirmation page first: in mode warn, with references or further locations, and
     * not yet confirmed.
     *
     * @param string $mode
     * @param array|null $check of forNodes( ..., true )
     * @param bool $confirmed the page was posted back
     * @return bool
     */
    public static function hideNeedsConfirmation( $mode, $check, $confirmed )
    {
        return $mode === self::WARN && !$confirmed && is_array( $check ) && self::hasFindings( $check );
    }

    /**
     * @param array $check of forNodes()
     * @return bool references, further locations, or a check that failed
     */
    public static function hasFindings( array $check )
    {
        return $check['total'] > 0 || $check['failed'] || $check['location_count'] > 0;
    }

    /**
     * What the template gets: the items with what the user may read, the mode, and whether the action is blocked
     * or was just refused. No count leaves this method: that there are references or locations the user may not
     * read is a yes or no per item (the page has to explain a block or a warning), and that more references exist
     * than are listed is a yes or no as well, so the page cannot be used to count hidden content.
     *
     * @param string $mode
     * @param array|null $check of forNodes()
     * @param bool $refused a confirmation was refused in mode block
     * @return array|null array( 'items', 'has_references', 'more', 'failed', 'mode', 'blocked', 'refused' ), null when
     *         there is nothing to show
     */
    public static function templateData( $mode, $check, $refused = false )
    {
        if ( $mode === self::DISABLED || !is_array( $check ) || !self::hasFindings( $check ) )
            return null;
        $items = array();
        foreach ( $check['items'] as $item )
        {
            $item['unreadable'] = !empty( $item['unreadable'] );
            $item['unreadable_locations'] = !empty( $item['unreadable_locations'] );
            $items[] = $item;
        }
        $unreadable = isset( $check['unreadable'] ) ? (int) $check['unreadable'] : 0;
        return array( 'items' => $items,
                      'has_references' => $check['total'] > 0,
                      'more' => $check['total'] > $check['listed'] + $unreadable,
                      'failed' => (bool) $check['failed'],
                      'mode' => $mode,
                      'blocked' => self::refusesRemoval( $mode, $check ),
                      'refused' => (bool) $refused );
    }

    /**
     * The references to what the given nodes take away, per node, described for a page (names, links, classes,
     * fields). Only objects the current user may read are described; the others are counted, never named.
     *
     * @param int[] $nodeIDs the selected nodes, each with its subtree
     * @param bool $withLocations also list the further locations of each selected node's object (for a hide)
     * @return array of summarise()
     */
    public static function forNodes( array $nodeIDs, $withLocations = false )
    {
        $roots = array();
        foreach ( array_unique( array_map( 'intval', $nodeIDs ) ) as $nodeID )
        {
            $node = \eZContentObjectTreeNode::fetch( $nodeID );
            if ( $node instanceof \eZContentObjectTreeNode )
                $roots[] = array( 'node_id' => (int) $node->attribute( 'node_id' ),
                                  'object_id' => (int) $node->attribute( 'contentobject_id' ),
                                  'path_string' => (string) $node->attribute( 'path_string' ),
                                  'name' => (string) $node->attribute( 'name' ) );
        }
        $found = self::references( $roots );

        // read access, fetched in one go for every object the rows name
        $ids = array();
        foreach ( $found['by_root'] as $rows )
            foreach ( $rows as $row )
                $ids[$row['from_id']] = $ids[$row['to_id']] = true;
        $readable = array();
        if ( $ids )
        {
            $objects = \eZContentObject::fetchIDArray( array_keys( $ids ) );
            foreach ( is_array( $objects ) ? $objects : array() as $object )
                if ( $object instanceof \eZContentObject )
                    $readable[(int) $object->attribute( 'id' )] = (bool) $object->canRead();
        }
        $canRead = function ( $objectID ) use ( $readable )
        {
            return !empty( $readable[(int) $objectID] );
        };
        return self::summarise( $roots, $found, $canRead, $withLocations ? array( __CLASS__, 'otherLocations' ) : null );
    }

    /**
     * The check for the page, per selected item: the references from objects the user may read, described, and the
     * number of the others; the further locations the user may read and the number of the others. The totals count
     * every reference and location, so a block holds whatever the user may read.
     *
     * @param array $roots array( array( 'node_id', 'object_id', 'path_string', 'name' ) )
     * @param array $found of references()
     * @param callable $canRead function( int $objectID ): bool
     * @param callable|null $locationsOf function( int $nodeID, int $objectID ): array( 'listed' => rows, 'unreadable' => int )
     * @return array( 'items' => array( array( 'node_id', 'object_id', 'name', 'relations', 'unreadable',
     *                'locations', 'unreadable_locations' ) ), 'total' => int, 'listed' => int, 'unreadable' => int,
     *                'location_count' => int, 'failed' => bool )
     */
    public static function summarise( array $roots, array $found, callable $canRead, $locationsOf = null )
    {
        $result = array( 'items' => array(), 'total' => (int) $found['total'], 'listed' => 0, 'unreadable' => 0,
                         'location_count' => 0, 'failed' => (bool) $found['failed'] );
        foreach ( $roots as $index => $root )
        {
            $relations = array();
            $unreadable = 0;
            foreach ( isset( $found['by_root'][$index] ) ? $found['by_root'][$index] : array() as $row )
            {
                if ( $canRead( $row['from_id'] ) )
                    $relations[] = self::describe( $row, $root['object_id'], $canRead( $row['to_id'] ) );
                else
                    $unreadable++;
            }
            $locations = $locationsOf ? call_user_func( $locationsOf, $root['node_id'], $root['object_id'] )
                                      : array( 'listed' => array(), 'unreadable' => 0 );
            if ( !$relations && !$unreadable && !$locations['listed'] && !$locations['unreadable'] )
                continue;
            $result['listed'] += count( $relations );
            $result['unreadable'] += $unreadable;
            $result['location_count'] += count( $locations['listed'] ) + $locations['unreadable'];
            $result['items'][] = array_merge( $root, array( 'relations' => $relations, 'unreadable' => $unreadable,
                                                            'locations' => $locations['listed'],
                                                            'unreadable_locations' => $locations['unreadable'] ) );
        }
        return $result;
    }

    /**
     * The references to what the subtrees take away, read from the database, at most LIST_LIMIT rows, each given
     * to the first selected subtree that holds its target.
     *
     * @param array $roots array( array( 'path_string' => '/1/2/42/', ... ) ) in the order of the selection
     * @return array( 'by_root' => array( index => rows ), 'total' => int, 'failed' => bool ); a row has from_id,
     *         from_name, from_class_id, from_node_id, to_id, to_name, relation_type, attribute_id
     */
    public static function references( array $roots )
    {
        $none = array( 'by_root' => array(), 'total' => 0, 'failed' => false );
        $paths = array();
        foreach ( $roots as $index => $root )
        {
            // only digits and slashes: the path goes into LIKE as it is
            if ( isset( $root['path_string'] ) && preg_match( '#^(/[0-9]+)+/$#', $root['path_string'] ) )
                $paths[$index] = $root['path_string'];
        }
        if ( !$paths )
            return $none;
        // a subtree inside another selected one adds nothing to the query (its references go to the outer one anyway)
        $queryPaths = array();
        foreach ( array_unique( $paths ) as $path )
        {
            $nested = false;
            foreach ( $paths as $other )
                if ( $other !== $path && strpos( $path, $other ) === 0 )
                    $nested = true;
            if ( !$nested )
                $queryPaths[] = $path;
        }

        $inside = function ( $alias ) use ( $queryPaths )
        {
            $or = array();
            foreach ( $queryPaths as $path )
                $or[] = "$alias.path_string LIKE '$path%'";
            return '( ' . implode( ' OR ', $or ) . ' )';
        };
        // objects with every location inside the subtrees: the objects that go
        $gone = 'SELECT t.contentobject_id FROM ezcontentobject_tree t WHERE t.contentobject_id IS NOT NULL AND ' . $inside( 't' )
              . ' AND NOT EXISTS ( SELECT 1 FROM ezcontentobject_tree o WHERE o.contentobject_id = t.contentobject_id AND NOT '
              . $inside( 'o' ) . ' )';
        $db = \eZDB::instance();
        // a relation whose kinds were all taken back keeps its row with relation_type 0 (removeContentObjectRelation())
        $from = 'FROM ezcontentobject_link l'
              . ' JOIN ezcontentobject f ON f.id = l.from_contentobject_id AND f.current_version = l.from_contentobject_version'
              . ' AND f.status = ' . (int) \eZContentObject::STATUS_PUBLISHED
              . ' JOIN ezcontentobject tobj ON tobj.id = l.to_contentobject_id'
              . ' LEFT JOIN ezcontentobject_tree fn ON fn.contentobject_id = f.id AND fn.node_id = fn.main_node_id'
              . " WHERE l.to_contentobject_id IN ( $gone ) AND l.from_contentobject_id NOT IN ( $gone )"
              . ' AND ' . $db->bitAnd( 'l.relation_type', self::ALL_KINDS ) . ' <> 0';

        $count = self::rows( $db, "SELECT COUNT(*) AS cnt $from" );
        if ( !is_array( $count ) )
        {
            \eZDebug::writeError( 'The reverse relations could not be read', __METHOD__ );
            return array_merge( $none, array( 'failed' => true ) );
        }
        $total = $count ? (int) $count[0]['cnt'] : 0;
        if ( $total === 0 )
            return $none;
        $rows = self::rows( $db, 'SELECT l.from_contentobject_id AS from_id, f.name AS from_name, f.contentclass_id AS from_class_id,'
                           . ' fn.node_id AS from_node_id, l.to_contentobject_id AS to_id, tobj.name AS to_name,'
                           . " l.relation_type AS relation_type, l.contentclassattribute_id AS attribute_id $from"
                           . ' ORDER BY l.to_contentobject_id, l.from_contentobject_id, l.contentclassattribute_id',
                           array( 'limit' => self::LIST_LIMIT ) );
        if ( !is_array( $rows ) )
        {
            \eZDebug::writeError( 'The reverse relations could not be read', __METHOD__ );
            return array( 'by_root' => array(), 'total' => $total, 'failed' => true );
        }

        // which selected subtree each target lies in (the first in the order of the selection)
        $targetIDs = array();
        foreach ( $rows as $row )
            $targetIDs[(int) $row['to_id']] = true;
        $rootOf = array();
        $locations = self::rows( $db, 'SELECT contentobject_id, path_string FROM ezcontentobject_tree WHERE '
                                . $db->generateSQLINStatement( array_keys( $targetIDs ), 'contentobject_id', false, true, 'int' ) );
        foreach ( is_array( $locations ) ? $locations : array() as $location )
        {
            foreach ( $paths as $index => $path )
            {
                if ( strpos( $location['path_string'], $path ) === 0 )
                {
                    $id = (int) $location['contentobject_id'];
                    if ( !isset( $rootOf[$id] ) || $index < $rootOf[$id] )
                        $rootOf[$id] = $index;
                    break;
                }
            }
        }
        $byRoot = array();
        foreach ( $rows as $row )
        {
            $to = (int) $row['to_id'];
            $index = isset( $rootOf[$to] ) ? $rootOf[$to] : array_key_first( $paths );
            $byRoot[$index][] = array( 'from_id' => (int) $row['from_id'], 'from_name' => (string) $row['from_name'],
                                       'from_class_id' => (int) $row['from_class_id'],
                                       'from_node_id' => $row['from_node_id'] === null ? 0 : (int) $row['from_node_id'],
                                       'to_id' => $to, 'to_name' => (string) $row['to_name'],
                                       'relation_type' => (int) $row['relation_type'], 'attribute_id' => (int) $row['attribute_id'] );
        }
        ksort( $byRoot );
        return array( 'by_root' => $byRoot, 'total' => $total, 'failed' => false );
    }

    /**
     * The rows of a query, or false when it fails (an error or an exception of the database layer).
     *
     * @param \eZDBInterface $db
     * @param string $sql
     * @param array $params
     * @return array|false
     */
    protected static function rows( $db, $sql, array $params = array() )
    {
        try
        {
            return $db->arrayQuery( $sql, $params );
        }
        catch ( \Exception $e )
        {
            \eZDebug::writeError( get_class( $e ) . ': ' . $e->getMessage(), __METHOD__ );
            return false;
        }
    }

    /**
     * The kinds of a relation_type bit field: common, embed, link, attribute.
     *
     * @param int $type
     * @return string[]
     */
    public static function kinds( $type )
    {
        $kinds = array();
        foreach ( array( \eZContentObject::RELATION_COMMON => 'common', \eZContentObject::RELATION_EMBED => 'embed',
                         \eZContentObject::RELATION_LINK => 'link', \eZContentObject::RELATION_ATTRIBUTE => 'attribute' ) as $bit => $kind )
        {
            if ( ( (int) $type & $bit ) !== 0 )
                $kinds[] = $kind;
        }
        return $kinds ? $kinds : array( 'common' );
    }

    /**
     * A row of references() from an object the user may read, for a page: the referring object's name, class and
     * main node, the field of a relation field, the kinds, its target (named only when the user may read it) and
     * whether the target is the selected object itself.
     *
     * @param array $row
     * @param int $selectedObjectID
     * @param bool $targetReadable
     * @return array
     */
    protected static function describe( array $row, $selectedObjectID, $targetReadable )
    {
        $className = '';
        $class = $row['from_class_id'] > 0 ? \eZContentClass::fetch( $row['from_class_id'] ) : null;
        if ( $class instanceof \eZContentClass )
            $className = (string) $class->attribute( 'name' );
        $field = '';
        if ( $row['attribute_id'] > 0 )
        {
            $attribute = \eZContentClassAttribute::fetch( $row['attribute_id'] );
            if ( $attribute instanceof \eZContentClassAttribute )
                $field = (string) $attribute->attribute( 'name' );
        }
        return array( 'from_id' => $row['from_id'],
                      'from_name' => $row['from_name'],
                      'from_node_id' => $row['from_node_id'],
                      'from_class' => $className,
                      'kinds' => self::kinds( $row['relation_type'] ),
                      'field' => $field,
                      'to_id' => $row['to_id'],
                      'to_name' => $targetReadable ? $row['to_name'] : '',
                      'is_selected' => $row['to_id'] === (int) $selectedObjectID );
    }

    /**
     * The other locations of a node's object that the user may read, with their path and whether they are hidden
     * or invisible, and the number of the others.
     *
     * @param int $nodeID
     * @param int $objectID
     * @return array( 'listed' => array( array( 'node_id', 'path', 'is_hidden', 'is_invisible' ) ), 'unreadable' => int )
     */
    public static function otherLocations( $nodeID, $objectID )
    {
        $result = array( 'listed' => array(), 'unreadable' => 0 );
        $object = \eZContentObject::fetch( $objectID );
        if ( !$object instanceof \eZContentObject )
            return $result;
        foreach ( $object->assignedNodes() as $node )
        {
            if ( (int) $node->attribute( 'node_id' ) === (int) $nodeID )
                continue;
            if ( !$node->canRead() )
            {
                $result['unreadable']++;
                continue;
            }
            $result['listed'][] = array( 'node_id' => (int) $node->attribute( 'node_id' ),
                                         'path' => ContentJobDetails::pathName( $node ),
                                         'is_hidden' => (bool) $node->attribute( 'is_hidden' ),
                                         'is_invisible' => (bool) $node->attribute( 'is_invisible' ) );
        }
        return $result;
    }
}
