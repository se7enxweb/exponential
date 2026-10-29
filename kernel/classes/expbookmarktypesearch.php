<?php
/**
 * File containing the expBookmarkTypeSearch class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Saved searches: a bookmark of content/search keeps the search text, the section and the subtree of the search
 * (the query string keys of bookmark.ini [ViewBookmarkSettings] QueryParameters[content/search]), so opening it runs
 * the same search again. Listed with a magnifier and the type "Search". The example of a kind of bookmark
 * (expBookmarkType), enabled in bookmark.ini [BookmarkTypeSettings] TypeList[]=search.
 *
 * @since 6.0.15
 * @package kernel
 */
class expBookmarkTypeSearch extends expBookmarkType
{
    public function identifier()
    {
        return 'search';
    }

    public function accepts( $module, $view )
    {
        return $module === 'content' && $view === 'search';
    }

    public function queryParameters( $module, $view )
    {
        return expBookmarkView::configuredQueryParameters( $module, $view );
    }

    public function icon( $uri )
    {
        return 'search';
    }

    public function label()
    {
        return ezpI18n::tr( 'kernel/content', 'Search' );
    }
}
