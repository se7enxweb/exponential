<?php
/**
 * Subitems list columns about when things happened, in the forms the built-in Published and
 * Modified columns do not show: relative ("3 days ago"), in days, and as ISO 8601 for copying.
 *
 * Field= picks the column: published_age, modified_age, days_since_published,
 * days_since_modified, published_iso, modified_iso. All read the loaded object; no query. The
 * formatting (formatAge(), days()) is the base class's, shared with every other column.
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
        return self::iso( $this->time( $node, 'published' ) );
    }

    protected function fieldModifiedIso( eZContentObjectTreeNode $node )
    {
        return self::iso( $this->time( $node, 'modified' ) );
    }

    /** The object's published or modified timestamp, null when unset. */
    protected function time( eZContentObjectTreeNode $node, $attribute )
    {
        $time = (int)self::objectInt( $node, $attribute );
        return $time > 0 ? $time : null;
    }

    /** A timestamp as ISO 8601 (2026-10-02T11:04:36-07:00), null for no time. */
    protected static function iso( $time )
    {
        return $time ? date( 'c', $time ) : null;
    }
}
