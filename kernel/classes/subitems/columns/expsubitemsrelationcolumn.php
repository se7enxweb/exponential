<?php
/**
 * Subitems list columns about what the object is related to, and what relates to it: object
 * relations, tags (eztags) and ratings (ezstarrating).
 *
 * Field= picks the column: related_count, related_names, reverse_related_count,
 * reverse_related_names, tags, tag_count, rating, rating_count.
 * The counts are one count query each (one grouped query for the page, see prefetch()) and
 * include every relation type (common, embedded, linked, attribute). The name lists fetch at most Limit= objects (default 10) and leave out
 * those the current user may not read. tags and tag_count need the eztags extension, rating and
 * rating_count the ezstarrating extension; without them (or for classes without such an
 * attribute) the value is null.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsRelationColumn extends expSubitemsFieldColumn
{
    /** Objects this object relates to (current version, all relation types). */
    protected function fieldRelatedCount( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        if ( !$object )
            return null;
        return self::memo( 'relatedcount', (int)$object->attribute( 'id' ), function () use ( $object )
        {
            return (int)$object->relatedObjectCount( false, false, false, array( 'AllRelations' => true ) );
        } );
    }

    protected function fieldRelatedNames( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        if ( !$object )
            return null;
        return $this->names( $object->relatedObjects( false, false, false, false,
                                                      array( 'AllRelations' => true, 'Limit' => $this->limit(), 'SortBy' => array( 'name', true ) ) ) );
    }

    /** Objects that relate to this one (their current versions). */
    protected function fieldReverseRelatedCount( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        if ( !$object )
            return null;
        return self::memo( 'reverserelatedcount', (int)$object->attribute( 'id' ), function () use ( $object )
        {
            return (int)$object->relatedObjectCount( false, false, true, array( 'AllRelations' => true ) );
        } );
    }

    protected function fieldReverseRelatedNames( eZContentObjectTreeNode $node )
    {
        $object = self::object( $node );
        if ( !$object )
            return null;
        return $this->names( $object->relatedObjects( false, false, false, false,
                                                      array( 'AllRelations' => true, 'Limit' => $this->limit(), 'SortBy' => array( 'name', true ) ),
                                                      true ) );
    }

    /** The keywords of the tags on the current version, in their order. */
    protected function fieldTags( eZContentObjectTreeNode $node )
    {
        $tags = $this->tagList( $node );
        if ( $tags === null )
            return null;
        $keywords = array();
        foreach ( $tags as $tag )
            $keywords[] = (string)$tag->attribute( 'keyword' );
        return $keywords;
    }

    protected function fieldTagCount( eZContentObjectTreeNode $node )
    {
        $tags = $this->tagList( $node );
        return $tags === null ? null : count( $tags );
    }

    /** The average star rating (one decimal) over the object's rating attributes; null when never rated. */
    protected function fieldRating( eZContentObjectTreeNode $node )
    {
        $stats = $this->ratingStats( $node );
        return $stats === null ? null : round( (float)$stats['rating_average'], 1 );
    }

    /** How many ratings were given. */
    protected function fieldRatingCount( eZContentObjectTreeNode $node )
    {
        $stats = $this->ratingStats( $node );
        return $stats === null ? null : (int)$stats['rating_count'];
    }

    protected static function prefetchSets()
    {
        return array( 'RelatedCount' => array( 'related_count' ),
                      'ReverseRelatedCount' => array( 'reverse_related_count' ),
                      'DataMap' => array( 'tags', 'tag_count' ) );
    }

    /** The relation counts of the page's objects, one grouped query (see prefetchRelationCounts()). */
    protected function prefetchRelatedCount( array $nodes )
    {
        self::prefetchRelationCounts( 'relatedcount', $nodes, false );
    }

    /** The reverse relation counts of the page's objects, one grouped query. */
    protected function prefetchReverseRelatedCount( array $nodes )
    {
        self::prefetchRelationCounts( 'reverserelatedcount', $nodes, true );
    }

    /**
     * Counts the relations of the page's objects as eZContentObject::relatedObjectCount( false,
     * false, $reverse, array( 'AllRelations' => true ) ) does for one object (its SQL, every
     * relation type, both ends published), grouped by object, and remembers them in $bucket.
     *
     * @param string $bucket
     * @param eZContentObjectTreeNode[] $nodes
     * @param bool $reverse false: the objects each one relates to (from its current version);
     *                      true: the objects relating to each one (from their current versions)
     *
     * The forward count reads the current version as the database has it, which is the version
     * the page's objects were just loaded with.
     */
    protected static function prefetchRelationCounts( $bucket, array $nodes, $reverse )
    {
        $db = self::sqlDatabase();
        $objects = array();
        foreach ( $nodes as $node )
        {
            $object = self::object( $node );
            if ( $object && (int)$object->attribute( 'id' ) > 0 && !self::isMemoised( $bucket, (int)$object->attribute( 'id' ) ) )
                $objects[(int)$object->attribute( 'id' )] = $object;
        }
        if ( !$objects || !$db )
            return;

        $mask = (int)eZContentObject::relationTypeMask( true );
        $maskCondition = $db->databaseName() === 'oracle'
            ? "bitand( inner_link.relation_type, $mask ) <> 0"
            : "( inner_link.relation_type & $mask ) <> 0";
        // relatedObjectCount() reads the links from the object's current version (forward) or from
        // the current versions of the objects relating to it (reverse)
        $groupBy = $reverse ? 'inner_link.to_contentobject_id' : 'inner_link.from_contentobject_id';
        $outerJoin = $reverse ? 'outer_object.id = outer_link.from_contentobject_id' : 'outer_object.id = outer_link.to_contentobject_id';
        $objectCondition = $db->generateSQLINStatement( array_keys( $objects ), $groupBy, false, false, 'int' )
                         . ' AND inner_link.from_contentobject_version = inner_object.current_version';

        $rows = $db->arrayQuery(
            "SELECT $groupBy AS object_id, COUNT( outer_object.id ) AS relation_count
               FROM ezcontentobject outer_object, ezcontentobject inner_object, ezcontentobject_link outer_link
              INNER JOIN ezcontentobject_link inner_link ON outer_link.id = inner_link.id
              WHERE $outerJoin
                AND outer_object.status = " . eZContentObject::STATUS_PUBLISHED . "
                AND inner_object.id = inner_link.from_contentobject_id
                AND inner_object.status = " . eZContentObject::STATUS_PUBLISHED . "
                AND $objectCondition
                AND $maskCondition
              GROUP BY $groupBy" );
        if ( !is_array( $rows ) )
            return;

        $counts = array_fill_keys( array_keys( $objects ), 0 );
        foreach ( $rows as $row )
            $counts[(int)$row['object_id']] = (int)$row['relation_count'];
        foreach ( $counts as $id => $count )
            self::remember( $bucket, $id, $count );
    }

    protected function limit()
    {
        $limit = (int)$this->setting( 'Limit', 10 );
        return $limit > 0 ? min( $limit, 100 ) : 10;
    }

    /** The names of the objects in $list the current user may read. */
    protected function names( $list )
    {
        $names = array();
        foreach ( is_array( $list ) ? $list : array() as $object )
        {
            if ( $object instanceof eZContentObject && $object->canRead() )
                $names[] = (string)$object->attribute( 'name' );
        }
        return $names;
    }

    /** The eZTagsObject list of the node's current version, null without eztags or for classes without tags. */
    protected function tagList( eZContentObjectTreeNode $node )
    {
        if ( !class_exists( 'eZTagsAttributeLinkObject' ) || !class_exists( 'eZTagsObject' ) )
            return null;
        $object = self::object( $node );
        if ( !$object )
            return null;
        if ( !self::attributesOfType( $node, array( 'eztags' ) ) )
            return null;

        $key = $object->attribute( 'id' ) . '/' . $object->attribute( 'current_version' );
        return self::memo( 'tags', $key, function () use ( $object )
        {
            $links = eZPersistentObject::fetchObjectList( eZTagsAttributeLinkObject::definition(), null,
                                                          array( 'object_id' => (int)$object->attribute( 'id' ),
                                                                 'objectattribute_version' => (int)$object->attribute( 'current_version' ) ),
                                                          array( 'priority' => 'asc' ), null, false );
            $ids = array();
            foreach ( is_array( $links ) ? $links : array() as $link )
                $ids[(int)$link['keyword_id']] = (int)$link['keyword_id'];
            if ( !$ids )
                return array();
            $byID = array();
            foreach ( eZTagsObject::fetchList( array( 'id' => array( array_values( $ids ) ) ) ) as $tag )
                $byID[(int)$tag->attribute( 'id' )] = $tag;
            $tags = array();
            foreach ( $ids as $id )
            {
                if ( isset( $byID[$id] ) )
                    $tags[] = $byID[$id];
            }
            return $tags;
        } );
    }

    /** ezstarrating's stats for the object (count, average), null when unrated or the extension is missing. */
    protected function ratingStats( eZContentObjectTreeNode $node )
    {
        if ( !class_exists( 'ezsrRatingObject' ) )
            return null;
        $object = self::object( $node );
        if ( !$object )
            return null;
        $stats = ezsrRatingObject::stats( (int)$object->attribute( 'id' ) );
        if ( !is_array( $stats ) || !isset( $stats['rating_count'] ) || (int)$stats['rating_count'] === 0 )
            return null;
        return $stats;
    }
}
