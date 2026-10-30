<?php
/**
 * File containing the expBookmarkType class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A kind of system page bookmark (a bookmark of a module view, see expBookmarkView): which module views it takes,
 * which query string keys their bookmarks keep, and the icon and the label they are listed with.
 *
 * The kinds are listed in bookmark.ini [BookmarkTypeSettings] TypeList[] with their class in TypeClass[]; they are
 * asked in that order and the first whose accepts() is true decides. A view no kind takes is a "System page"
 * (expBookmarkTypeSystemPage), whose query string keys come from [ViewBookmarkSettings] QueryParameters[] and whose
 * icon from ModuleIcons[]. An extension adds a kind with a class extending this one and two settings:
 *
 * \code
 * // extension/mylayouts/classes/mylayoutbookmarktype.php
 * class myLayoutBookmarkType extends expBookmarkType
 * {
 *     public function identifier() { return 'layout'; }
 *     public function accepts( $module, $view ) { return $module === 'mylayouts' && $view === 'edit'; }
 *     public function icon( $uri ) { return 'layout-sidebar-inset'; }
 *     public function label() { return ezpI18n::tr( 'extension/mylayouts', 'Layout' ); }
 * }
 * // extension/mylayouts/settings/bookmark.ini.append.php
 * // [BookmarkTypeSettings]
 * // TypeList[]=layout
 * // TypeClass[layout]=myLayoutBookmarkType
 * \endcode
 *
 * A kind never makes a view bookmarkable that bookmark.ini [ViewBookmarkSettings] NonBookmarkableViews[] or the
 * module definition ('bookmarkable' => false) excludes, and whether the user may open the page is always decided by
 * expViewAccess.
 *
 * Guide: doc/eng-us/features/6.0/bookmarks-of-system-pages.md ("Adding a kind of bookmark")
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expBookmarkType
{
    /**
     * The kind's name, as in TypeList[] (letters, digits, - and _).
     *
     * @return string
     */
    abstract public function identifier();

    /**
     * Whether bookmarks of this module view are of this kind.
     *
     * @param string $module
     * @param string $view
     * @return bool
     */
    abstract public function accepts( $module, $view );

    /**
     * The query string keys a bookmark of this view keeps (everything else is dropped). None by default: a module
     * view takes its parameters in its address.
     *
     * @param string $module
     * @param string $view
     * @return string[]
     */
    public function queryParameters( $module, $view )
    {
        return array();
    }

    /**
     * The icon of a bookmark: the name of an icon of the administration designs (a4-bi-<name>).
     *
     * @param string $uri the stored address
     * @return string
     */
    public function icon( $uri )
    {
        return 'gear';
    }

    /**
     * The label of the kind, shown as the type of its bookmarks on My bookmarks (translated).
     *
     * @return string
     */
    public function label()
    {
        return ezpI18n::tr( 'kernel/content', 'System page' );
    }
}
