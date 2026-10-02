<?php
/**
 * Subitems list columns about when things happened, in the forms the built-in Published and
 * Modified columns do not show: relative ("3 days ago"), in days, and as ISO 8601 for copying.
 *
 * Field= picks the column: published_age, modified_age, days_since_published,
 * days_since_modified, published_iso, modified_iso. All read the loaded object; no query.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsDateColumn extends expSubitemsFieldColumn
{
    protected function fieldPublishedAge( eZContentObjectTreeNode $node )
    {
        return self::formatAge( $this->time( $node, 'published' ) );
    }

    protected function fieldModifiedAge( eZContentObjectTreeNode $node )
    {
        return self::formatAge( $this->time( $node, 'modified' ) );
    }

    protected function fieldDaysSincePublished( eZContentObjectTreeNode $node )
    {
        return self::days( $this->time( $node, 'published' ) );
    }

    protected function fieldDaysSinceModified( eZContentObjectTreeNode $node )
    {
        return self::days( $this->time( $node, 'modified' ) );
    }

    protected function fieldPublishedIso( eZContentObjectTreeNode $node )
    {
        $time = $this->time( $node, 'published' );
        return $time ? date( 'c', $time ) : null;
    }

    protected function fieldModifiedIso( eZContentObjectTreeNode $node )
    {
        $time = $this->time( $node, 'modified' );
        return $time ? date( 'c', $time ) : null;
    }

    /** The object's published or modified timestamp, null when unset. */
    protected function time( eZContentObjectTreeNode $node, $attribute )
    {
        $object = self::object( $node );
        $time = $object ? (int)$object->attribute( $attribute ) : 0;
        return $time > 0 ? $time : null;
    }

    /** Whole days from $time until now, null for no time. */
    public static function days( $time, $now = null )
    {
        if ( !$time )
            return null;
        $now = $now === null ? time() : (int)$now;
        return (int)floor( ( $now - (int)$time ) / 86400 );
    }
}
