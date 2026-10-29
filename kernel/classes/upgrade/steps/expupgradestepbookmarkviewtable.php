<?php
/**
 * File containing the expUpgradeStepBookmarkViewTable class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The bookmarks of system pages (expbookmark_view), through expBookmarkView::install() as
 * update/common/scripts/6.0/createbookmarkviewtable.php.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepBookmarkViewTable extends expUpgradeTableInstallerStep
{
    public function id()
    {
        return '6.0.15-bookmark-view-table';
    }

    public function description()
    {
        return 'The bookmarks of system pages (table expbookmark_view)';
    }

    public function tables()
    {
        return array( 'expbookmark_view' );
    }

    public function after()
    {
        return array( '6.0.15-bookmark-folders' );
    }

    protected function install( $db, array &$messages )
    {
        return expBookmarkView::install( $db, $messages );
    }
}
