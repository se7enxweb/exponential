<?php
/**
 * Subitems list columns about what the object is related to, and what relates to it: object
 * relations, tags (eztags) and ratings (ezstarrating).
 *
 * Field= picks the column: related_count, related_names, reverse_related_count,
 * reverse_related_names, tags, tag_count, rating, rating_count.
 * The counts are one count query each and include every relation type (common, embedded,
 * linked, attribute). The name lists fetch at most Limit= objects (default 10) and leave out
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
        return $object ? (int)$object->relatedObjectCount( false, false, false, array( 'AllRelations' => true ) ) : null;
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
        return $object ? (int)$object->relatedObjectCount( false, false, true, array( 'AllRelations' => true ) ) : null;
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
        $hasTagsAttribute = false;
        foreach ( self::dataMap( $node ) as $attribute )
        {
            if ( $attribute->attribute( 'data_type_string' ) === 'eztags' )
                $hasTagsAttribute = true;
        }
        if ( !$hasTagsAttribute )
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
