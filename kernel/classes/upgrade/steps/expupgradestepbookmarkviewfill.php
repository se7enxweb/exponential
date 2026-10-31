<?php
/**
 * File containing the expUpgradeStepBookmarkViewFill class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Fills module_name, view_name and view_parameters of the system page bookmarks written before the columns existed,
 * from their address (expBookmarkView::fillParsedColumns(), as update/common/scripts/6.0/createbookmarkviewtable.php).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepBookmarkViewFill extends expUpgradeScriptStep
{
    public function id()
    {
        return '6.0.15-bookmark-view-fill';
    }

    public function description()
    {
        return 'The parsed address of system page bookmarks made before the columns existed (createbookmarkviewtable.php)';
    }

    public function after()
    {
        return array( '6.0.15-bookmark-view-columns' );
    }

    protected function changes( expUpgradeContext $context )
    {
        expBookmarkView::reset();
        if ( expBookmarkView::exists( $context->db(), true ) !== true )
        {
            $this->notApplicable = true;
            $this->reason = 'the table expbookmark_view does not exist yet';
            return null;
        }
        if ( !expBookmarkView::hasParsedColumns( true ) )
            return array( 'fill the parsed address of system page bookmarks once the database stage added the columns' );
        $lines = array();
        foreach ( expBookmarkView::staleParsedRows() as $id => $item )
            $lines[] = "bookmark $id: " . $item['fields']['module_name'] . '/' . $item['fields']['view_name'];
        return $lines;
    }

    protected function write( expUpgradeContext $context )
    {
        $count = expBookmarkView::fillParsedColumns();
        return $count ? array( "filled the parsed address of $count bookmark(s)" ) : array();
    }
}
