<?php
/**
 * File containing the expBookmarkTypeSystemPage class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The kind of every system page bookmark no other kind takes (see expBookmarkType): its query string keys come from
 * bookmark.ini [ViewBookmarkSettings] QueryParameters[module/view], its icon from ModuleIcons[module] (DefaultIcon
 * when the module is not listed).
 *
 * @since 6.0.15
 * @package kernel
 */
class expBookmarkTypeSystemPage extends expBookmarkType
{
    public function identifier()
    {
        return 'view';
    }

    public function accepts( $module, $view )
    {
        return true;
    }

    public function queryParameters( $module, $view )
    {
        return expBookmarkView::configuredQueryParameters( $module, $view );
    }

    public function icon( $uri )
    {
        return expBookmarkView::moduleIcon( expBookmarkView::parts( $uri )['module'] );
    }
}
