<?php
/**
 * Example Handler= columns of the admin subitems list: plain static methods an INI block names
 * as Handler=expSubitemsColumnHandlers::<method>.
 *
 * A handler is the quickest kind of custom column: one function that gets the row's node, the
 * column's settings block and the column object, and returns the value (null, a scalar or a list
 * of scalars). The default html() and text() of the column then make the cell and the CSV text.
 * Guide: doc/bc/6.0/subitems-table-options.md ("Adding a column in 3 minutes").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsColumnHandlers
{
    /**
     * The address of the item's edit page in the admin (a link), null when the user may not edit it.
     * [Column_editlink] Handler=expSubitemsColumnHandlers::editLink, Type=link
     */
    public static function editLink( eZContentObjectTreeNode $node, array $settings, expSubitemsColumn $column )
    {
        if ( !$node->canEdit() )
            return null;
        $url = 'content/edit/' . (int)$node->attribute( 'contentobject_id' );
        // the siteaccess's index file and URI prefix, as ezurl adds them; not escaped (html() escapes)
        eZURI::transformURI( $url, false, 'relative', false );
        return $url;
    }

    /**
     * The number of whole days the item has been online (since it was first published).
     * [Column_daysonline] Handler=expSubitemsColumnHandlers::daysOnline, Type=number
     */
    public static function daysOnline( eZContentObjectTreeNode $node, array $settings, expSubitemsColumn $column )
    {
        $object = $node->attribute( 'object' );
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'published' ) <= 0 )
            return null;
        return (int)floor( ( time() - (int)$object->attribute( 'published' ) ) / 86400 );
    }
}
