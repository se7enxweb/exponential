<?php
/**
 * File containing the expUpgradeStepBookmarkFolders class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Bookmark folders: ezcontentbrowsebookmark.folder_id and priority, their index, and the table expbookmark_folder.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepBookmarkFolders extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-bookmark-folders';
    }

    public function description()
    {
        return 'Bookmark folders: ezcontentbrowsebookmark.folder_id, priority, their index and the table expbookmark_folder';
    }

    public function tables()
    {
        return array( 'expbookmark_folder' );
    }

    public function fields()
    {
        return array( 'ezcontentbrowsebookmark' => array( 'folder_id', 'priority' ) );
    }

    public function indexes()
    {
        return array( 'ezcontentbrowsebookmark' => array( 'ezcontentbrowsebookmark_folder' ) );
    }
}
