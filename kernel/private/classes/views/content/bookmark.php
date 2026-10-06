<?php
/**
 * The code of kernel/content/bookmark.php, moved into a class (#207 stage 1). The file kernel/content/bookmark.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 * User guide of the page: doc/guides/bookmarks.md
 */
/*
 * The original header of kernel/content/bookmark.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

/**
 * My bookmarks: the bookmarks of the current user grouped by folder, with a folder (folder), an order (sort), a
 * search (?q=) and paging (offset) kept in the address. What is shown is worked out by \expBookmarkPage; the folder
 * actions are \eZContentBrowseBookmarkFolder::handleAction(). Every POST answers with a redirect back to the same
 * folder, order and search and a notice kept in the session, so a reload never repeats an action. Only the user's
 * own bookmarks and folders are read or changed.
 */
class Bookmark extends \Exponential\Runnable\ModuleView
{
    /** The nodes nodeInfo() read, by node id, so the page's items need no second fetch. */
    protected static $nodes = array();

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        $user = \eZUser::currentUser();
        $userID = (int) $user->id();

        $userParameters = isset( $Params['UserParameters'] ) ? (array) $Params['UserParameters'] : array();
        $offset = isset( $Params['Offset'] ) && is_numeric( $Params['Offset'] ) ? max( 0, (int) $Params['Offset'] ) : 0;
        $sort = \expBookmarkPage::sortKey( isset( $userParameters['sort'] ) ? $userParameters['sort'] : '' );
        $search = \expBookmarkPage::searchText( $http->hasGetVariable( 'q' ) ? $http->getVariable( 'q' ) : '' );

        $rows = \eZContentBrowseBookmarkFolder::fetchRowsForUser( $userID );
        $folders = \expBookmarkPage::folders( $rows );
        $scopeFolder = \expBookmarkPage::scope( isset( $userParameters['folder'] ) ? $userParameters['folder'] : '', $folders );
        $here = \expBookmarkPage::path( $scopeFolder, $sort, $search, $offset );

        // Folder actions: one POST field says which (create, rename, delete, move_bookmark, move_folder, place, reorder).
        if ( $http->hasPostVariable( 'MoveSelectedButton' ) )
            $http->setPostVariable( 'BookmarkFolderAction', 'move_bookmark' );
        if ( $http->hasPostVariable( 'BookmarkFolderAction' ) )
        {
            $removing = $http->postVariable( 'BookmarkFolderAction' ) === 'delete' && $scopeFolder > 0
                        && (int) $http->postVariable( 'FolderID', 0 ) === $scopeFolder;
            $notice = \eZContentBrowseBookmarkFolder::handleAction( $userID, $http );
            $http->setSessionVariable( 'BookmarkNotice', $notice );
            if ( $removing && $notice['level'] !== 'error' )
            {
                // the folder shown is gone: show the folder it was in
                $parent = $folders[$scopeFolder]['parent_id'];
                return $this->viewResult( null, $Module->redirectTo( \expBookmarkPage::path( $parent > 0 ? $parent : \expBookmarkPage::SCOPE_ALL, $sort ) ) );
            }
            return $this->viewResult( null, $Module->redirectTo( $here ) );
        }
        if ( $http->hasPostVariable( 'BookmarkShiftButton' ) )
        {
            $request = \expBookmarkPage::shiftRequest( $http->postVariable( 'BookmarkShiftButton' ) );
            $db = \eZDB::instance();
            $db->begin();
            $moved = $request && \eZContentBrowseBookmarkFolder::shift( $userID, $request['type'], $request['id'], $request['direction'] );
            $db->commit();
            $http->setSessionVariable( 'BookmarkNotice', $moved
                ? array( 'level' => 'feedback', 'text' => \ezpI18n::tr( 'kernel/content', 'The order was saved.' ) )
                : array( 'level' => 'error', 'text' => \ezpI18n::tr( 'kernel/content', 'Nothing was moved: it is first or last in its folder already.' ) ) );
            return $this->viewResult( null, $Module->redirectTo( $here ) );
        }
        // Drag and drop: the new order of the entries of one folder that the page shows (BookmarkOrderButton,
        // OrderType bookmark|folder, OrderFolderID, OrderIDs). Every id must be the user's and in that folder.
        if ( $http->hasPostVariable( 'BookmarkOrderButton' ) )
        {
            $db = \eZDB::instance();
            $db->begin();
            $saved = \eZContentBrowseBookmarkFolder::setOrder( $userID, $http->postVariable( 'OrderType', 'bookmark' ),
                                                              (int) $http->postVariable( 'OrderFolderID', 0 ), $http->postVariable( 'OrderIDs', '' ) );
            $db->commit();
            $http->setSessionVariable( 'BookmarkNotice', $saved
                ? array( 'level' => 'feedback', 'text' => \ezpI18n::tr( 'kernel/content', 'The order was saved.' ) )
                : array( 'level' => 'error', 'text' => \ezpI18n::tr( 'kernel/content', 'The order was not saved: it named an entry that is not in that folder of yours.' ) ) );
            return $this->viewResult( null, $Module->redirectTo( $here ) );
        }
        // The position fields: the Move button of one bookmark (BookmarkPositionButton = its id), or Enter in a field
        // (BookmarkPositionDefault, the form's first button), which moves the first bookmark whose field was changed.
        if ( $http->hasPostVariable( 'BookmarkPositionButton' ) || $http->hasPostVariable( 'BookmarkPositionDefault' ) )
        {
            $positions = (array) $http->postVariable( 'BookmarkPosition', array() );
            $shown = (array) $http->postVariable( 'BookmarkPositionShown', array() );
            $id = $http->hasPostVariable( 'BookmarkPositionButton' ) ? (int) $http->postVariable( 'BookmarkPositionButton' ) : 0;
            if ( !$id )
                foreach ( $positions as $key => $value )
                    if ( isset( $shown[$key] ) && (string) $shown[$key] !== (string) $value )
                    {
                        $id = (int) $key;
                        break;
                    }
            $position = ( $id && isset( $positions[$id] ) && is_scalar( $positions[$id] ) && ctype_digit( trim( (string) $positions[$id] ) ) )
                        ? (int) $positions[$id] : 0;
            $db = \eZDB::instance();
            $db->begin();
            $now = $position > 0 ? \eZContentBrowseBookmarkFolder::moveToPosition( $userID, 'bookmark', $id, $position ) : false;
            $db->commit();
            $http->setSessionVariable( 'BookmarkNotice', $now
                ? array( 'level' => 'feedback', 'text' => \ezpI18n::tr( 'kernel/content', 'The bookmark is now at position %position of its folder.', null, array( '%position' => $now ) ) )
                : array( 'level' => 'error', 'text' => \ezpI18n::tr( 'kernel/content', 'Nothing was moved: give a position as a whole number from 1.' ) ) );
            return $this->viewResult( null, $Module->redirectTo( $here ) );
        }

        if ( $Module->isCurrentAction( 'Remove' ) )
        {
            $removed = 0;
            if ( $Module->hasActionParameter( 'DeleteIDArray' ) )
            {
                $db = \eZDB::instance();
                $db->begin();
                foreach ( \expBookmarkPage::selectedIDs( $Module->actionParameter( 'DeleteIDArray' ) ) as $deleteID )
                {
                    $bookmark = \eZContentBrowseBookmark::fetch( $deleteID );
                    if ( $bookmark !== null && (int) $bookmark->attribute( 'user_id' ) === $userID )
                    {
                        $bookmark->remove();
                        ++$removed;
                    }
                }
                $db->commit();
            }
            if ( $http->hasPostVariable( 'NeedRedirectBack' ) )
            {
                // the Remove bookmark of the context menu: back to the page it was chosen on, when that is a page of this site
                $back = \eZRedirectManager::safeURI( $http->postVariable( 'RedirectURI', $http->sessionVariable( 'LastAccessesURI', '/' ) ) );
                return $this->viewResult( null, $Module->redirectTo( $back !== false ? $back : '/' ) );
            }
            $http->setSessionVariable( 'BookmarkNotice', $removed
                ? array( 'level' => 'feedback', 'text' => \ezpI18n::tr( 'kernel/content', 'Removed %count bookmark(s). The items themselves are not changed.', null, array( '%count' => $removed ) ) )
                : array( 'level' => 'error', 'text' => \ezpI18n::tr( 'kernel/content', 'No bookmark was selected. Tick the bookmarks to remove first.' ) ) );
            return $this->viewResult( null, $Module->redirectTo( $here ) );
        }
        else if ( $Module->isCurrentAction( 'Add' ) )
        {
            return $this->viewResult( null, \eZContentBrowse::browse( array( 'action_name' => 'AddBookmark',
                                                                              'description_template' => 'design:content/browse_bookmark.tpl',
                                                                              'from_page' => '/' . \expBookmarkPage::path( $scopeFolder, $sort ) ),
                                                                       $Module ) );
        }
        else if ( $Module->isCurrentAction( 'AddBookmark' ) )
        {
            $nodeList = \eZContentBrowse::result( 'AddBookmark' );
            $added = 0;
            if ( $nodeList )
            {
                // into the folder the page shows; on "every bookmark" an existing bookmark keeps its folder
                $folderID = $scopeFolder >= 0 ? $scopeFolder : false;
                $db = \eZDB::instance();
                $db->begin();
                foreach ( $nodeList as $nodeID )
                {
                    $node = \eZContentObjectTreeNode::fetch( (int) $nodeID );
                    if ( $node && $node->canRead() )
                    {
                        \eZContentBrowseBookmark::createNew( $userID, (int) $nodeID, $node->attribute( 'name' ), $folderID );
                        ++$added;
                    }
                }
                $db->commit();
            }
            if ( $added )
                $http->setSessionVariable( 'BookmarkNotice', array( 'level' => 'feedback', 'text' => \ezpI18n::tr( 'kernel/content', 'Added %count bookmark(s).', null, array( '%count' => $added ) ) ) );
            return $this->viewResult( null, $Module->redirectTo( \expBookmarkPage::path( $scopeFolder, $sort ) ) );
        }

        // What the page shows
        $items = \expBookmarkPage::items( $rows, self::nodeInfo( $rows ) );
        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( 'content/bookmark', 'admin_bookmark_list_limit', array( 25, 50, 100 ) );
        $selection = \expBookmarkPage::select( $items, $folders, $scopeFolder, $search, $sort );
        $page = \expBookmarkPage::page( $selection, $offset, $limit );
        $pageItems = self::withPermissions( $page['items'] );
        $positions = \expBookmarkPage::folderPositions( $items, $folders );
        foreach ( $pageItems as $i => $item )
        {
            $pageItems[$i]['folder_position'] = isset( $positions[$item['id']] ) ? $positions[$item['id']]['position'] : 0;
            $pageItems[$i]['folder_count'] = isset( $positions[$item['id']] ) ? $positions[$item['id']]['count'] : 0;
        }

        $viewParameters = array( 'offset' => $page['offset'] );
        if ( $scopeFolder === 0 )
            $viewParameters['folder'] = 'top';
        else if ( $scopeFolder > 0 )
            $viewParameters['folder'] = $scopeFolder;
        if ( $sort !== \expBookmarkPage::SORTS[0] )
            $viewParameters['sort'] = $sort;

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'view_parameters', $viewParameters );
        if ( $http->hasSessionVariable( 'BookmarkNotice' ) )
        {
            $tpl->setVariable( 'bookmark_notice', $http->sessionVariable( 'BookmarkNotice' ) );
            $http->removeSessionVariable( 'BookmarkNotice' );
        }
        $current = $scopeFolder > 0 ? $folders[$scopeFolder] : false;
        $tpl->setVariable( 'bookmark_page', array(
            'items' => $pageItems,
            'count' => $page['count'],
            'offset' => $page['offset'],
            'limit' => $limit,
            'limit_choices' => $limitChoices,
            'summary' => \expBookmarkPage::summary( $items, $folders ),
            'folders' => array_values( $folders ),
            'folder_names' => self::folderNames( $folders ),
            'targets' => \expBookmarkPage::targets( $folders ),
            'scope' => $scopeFolder,
            'scope_key' => $scopeFolder === \expBookmarkPage::SCOPE_ALL ? 'all' : ( $scopeFolder === 0 ? 'top' : 'folder' ),
            'current' => $current,
            'current_parent' => ( $current && $current['parent_id'] ) ? $folders[$current['parent_id']] : false,
            'current_targets' => $current ? \expBookmarkPage::targets( $folders, $current['subtree'] ) : array(),
            'current_siblings' => $current ? self::siblingPosition( $folders, $current ) : array( 'first' => true, 'last' => true ),
            'sort' => $sort,
            'search' => $search,
            'here' => $here,
            'base' => \expBookmarkPage::path( $scopeFolder ),
            'base_sorted' => \expBookmarkPage::path( $scopeFolder, $sort ),
            'search_suffix' => $search !== '' ? '?q=' . rawurlencode( $search ) : '',
            'order_buttons' => $sort === 'own' && $search === '',
            'own_path' => \expBookmarkPage::path( $scopeFolder ),
            'first_last' => self::firstLast( $items ),
        ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/bookmark.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/content', 'My bookmarks' ),
                                        'url' => false ) );

        return $this->viewResult( $Result, null );
    }

    /**
     * What the page needs of the nodes of all bookmarks, from two queries (the nodes, the names of the nodes above
     * them), keyed by node id. A node the user may not read is marked so; nothing else of it is passed on.
     */
    protected static function nodeInfo( array $rows )
    {
        self::$nodes = array();
        $nodeIDs = array();
        foreach ( $rows as $row )
            if ( $row['type'] === 'bookmark' )
                $nodeIDs[(int) $row['bookmark']->attribute( 'node_id' )] = true;
        if ( !$nodeIDs )
            return array();
        $nodes = \eZContentObjectTreeNode::fetch( array_keys( $nodeIDs ) );
        if ( $nodes instanceof \eZContentObjectTreeNode )
            $nodes = array( $nodes );
        $nodes = is_array( $nodes ) ? $nodes : array();

        $above = array();
        foreach ( $nodes as $node )
            foreach ( self::ancestorIDs( $node ) as $id )
                $above[$id] = true;
        $names = array();
        if ( $above )
        {
            $ancestors = \eZContentObjectTreeNode::fetch( array_keys( $above ) );
            if ( $ancestors instanceof \eZContentObjectTreeNode )
                $ancestors = array( $ancestors );
            foreach ( is_array( $ancestors ) ? $ancestors : array() as $ancestor )
                $names[(int) $ancestor->attribute( 'node_id' )] = (string) $ancestor->attribute( 'name' );
        }

        $info = array();
        foreach ( $nodes as $node )
        {
            self::$nodes[(int) $node->attribute( 'node_id' )] = $node;
            $path = array();
            foreach ( self::ancestorIDs( $node ) as $id )
                if ( isset( $names[$id] ) )
                    $path[] = $names[$id];
            $info[(int) $node->attribute( 'node_id' )] = array(
                'name' => (string) $node->attribute( 'name' ),
                'class_identifier' => (string) $node->attribute( 'class_identifier' ),
                'class_name' => (string) $node->attribute( 'class_name' ),
                'is_hidden' => (bool) $node->attribute( 'is_hidden' ),
                'is_invisible' => (bool) $node->attribute( 'is_invisible' ),
                'modified' => self::objectModified( $node ),
                'path' => $path,
                'is_container' => (bool) $node->attribute( 'is_container' ),
                'contentobject_id' => (int) $node->attribute( 'contentobject_id' ),
                'can_read' => (bool) $node->canRead() );
        }
        return $info;
    }

    /** The ids of the nodes above a node, without the root node (1). */
    protected static function ancestorIDs( \eZContentObjectTreeNode $node )
    {
        $ids = array_filter( array_map( 'intval', explode( '/', trim( (string) $node->attribute( 'path_string' ), '/' ) ) ) );
        array_pop( $ids ); // the node itself
        return array_values( array_filter( $ids, function ( $id ) { return $id > 1; } ) );
    }

    /** The object's modification time, from the row the node fetch already read (no query). */
    protected static function objectModified( \eZContentObjectTreeNode $node )
    {
        $object = $node->object();
        return $object ? (int) $object->attribute( 'modified' ) : 0;
    }

    /** Whether the user may edit each item of the page (one object per item, the page only). */
    protected static function withPermissions( array $items )
    {
        foreach ( $items as $i => $item )
        {
            $items[$i]['can_edit'] = false;
            if ( $item['state'] === 'gone' || $item['state'] === 'denied' || !$item['contentobject_id'] )
                continue;
            $node = isset( self::$nodes[$item['node_id']] ) ? self::$nodes[$item['node_id']] : null;
            $items[$i]['can_edit'] = $node ? (bool) $node->canEdit() : false;
        }
        return $items;
    }

    /** The names of the folders with their path, by id, for the group headings. */
    protected static function folderNames( array $folders )
    {
        $names = array();
        foreach ( $folders as $id => $folder )
            $names[$id] = array( 'name' => $folder['name'], 'path' => $folder['path'], 'count' => $folder['count'], 'direct' => $folder['direct'] );
        return $names;
    }

    /** Whether a folder is the first and the last of its siblings (for its Move up and Move down). */
    protected static function siblingPosition( array $folders, array $folder )
    {
        $siblings = array();
        foreach ( $folders as $id => $f )
            if ( $f['parent_id'] === $folder['parent_id'] )
                $siblings[] = $id;
        return array( 'first' => reset( $siblings ) === $folder['id'], 'last' => end( $siblings ) === $folder['id'] );
    }

    /** The first and the last bookmark of every folder in the user's own order, for the Move up and Move down buttons. */
    protected static function firstLast( array $items )
    {
        $byFolder = array();
        foreach ( $items as $item )
            $byFolder[$item['folder_id']][] = $item['id'];
        $result = array( 'first' => array(), 'last' => array() );
        foreach ( $byFolder as $ids )
        {
            $result['first'][] = reset( $ids );
            $result['last'][] = end( $ids );
        }
        return $result;
    }
}

}
