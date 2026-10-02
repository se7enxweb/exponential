<?php
/**
 * The "Reading time" column of the admin subitems list: the minutes a reader needs for the
 * item's main text, at WordsPerMinute= words a minute (default 200). Null when there is no text.
 *
 * It is the guide's example of a Class= column: a subclass of expSubitemsColumn with value()
 * and, here, its own html(). Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsReadingTimeColumn extends expSubitemsColumn
{
    /** Whole minutes, at least 1 for any text; null without a main text. */
    public function value( eZContentObjectTreeNode $node )
    {
        $words = new expSubitemsObjectColumn( $this->key, array( 'Field' => 'word_count' ) + $this->settings );
        $count = $words->value( $node );
        if ( !$count )
            return null;
        $perMinute = max( 1, (int)$this->setting( 'WordsPerMinute', 200 ) );
        return max( 1, (int)ceil( $count / $perMinute ) );
    }

    /** "4 min" in the cell; the CSV keeps the number. */
    public function html( eZContentObjectTreeNode $node, $value )
    {
        return $value === null ? '' : self::escape( $value . ' ' . ezpI18n::tr( 'design/admin/node/view/full', 'min' ) );
    }
}
