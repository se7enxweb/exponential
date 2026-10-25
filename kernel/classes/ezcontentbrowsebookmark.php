<?php
/**
 * File containing the eZContentBrowseBookmark class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZContentBrowseBookmark ezcontentbrowsebookmark.php
  \brief Handles bookmarking nodes for users

  Allows the creation and fetching of bookmark lists for users.
  The bookmark list is used in the browse page to allow quick navigation and selection.

  Creating a new bookmark item is done with
\code
$userID = eZUser::currentUserID();
$nodeID = 2;
$nodeName = 'Node';
eZContentBrowseBookmark::createNew( $userID, $nodeID, $nodeName )
\endcode

  Fetching the list is done with
\code
$userID = eZUser::currentUserID();
eZContentBrowseBookmark::fetchListForUser( $userID )
\endcode

*/

class eZContentBrowseBookmark extends eZPersistentObject
{
    static function definition()
    {
        return array( "fields" => array( "id" => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         "user_id" => array( 'name' => 'UserID',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true,
                                                             'foreign_class' => 'eZUser',
                                                             'foreign_attribute' => 'contentobject_id',
                                                             'multiplicity' => '1..*' ),
                                         "node_id" => array( 'name' => "NodeID",
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true,
                                                             'foreign_class' => 'eZContentObjectTreeNode',
                                                             'foreign_attribute' => 'node_id',
                                                             'multiplicity' => '1..*' ),
                                         "name" => array( 'name' => "Name",
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ),
                                         // the virtual folder of the bookmark, 0 is the top level
                                         // (see eZContentBrowseBookmarkFolder)
                                         "folder_id" => array( 'name' => "FolderID",
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         // the order within the folder, ascending
                                         "priority" => array( 'name' => "Priority",
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true ) ),
                      "keys" => array( "id" ),
                      "function_attributes" => array( 'node' => 'fetchNode',
                                                      'contentobject_id' => 'contentObjectID',
                                                      // 'node', or 'view' for a bookmark of a system page (node_id 0,
                                                      // its address in expbookmark_view, see expBookmarkView)
                                                      'kind' => 'kind',
                                                      'module_uri' => 'moduleURI' ),
                      "increment_key" => "id",
                      "sort" => array( "id" => "asc" ),
                      "class_name" => "eZContentBrowseBookmark",
                      "name" => "ezcontentbrowsebookmark" );

    }

    /*!
     \static
     \return the bookmark item \a $bookmarkID.
    */
    static function fetch( $bookmarkID )
    {
        return eZPersistentObject::fetchObject( eZContentBrowseBookmark::definition(),
                                                null, array( 'id' => $bookmarkID ), true );
    }

    /*!
     \static
     \return the bookmark list for user \a $userID: the bookmarks of nodes, newest first. This is the list the browse
     page and the content/bookmarks fetch have always had; the bookmarks of system pages (node_id 0, see
     expBookmarkView) are in it only with \a $nodesOnly false.
    */
    static function fetchListForUser( $userID, $offset = false, $limit = false, $nodesOnly = true )
    {
        $conditions = array( 'user_id' => $userID );
        if ( $nodesOnly )
            $conditions['node_id'] = array( '>', 0 );
        $objectList = eZPersistentObject::fetchObjectList( eZContentBrowseBookmark::definition(),
                                                            null,
                                                            $conditions,
                                                            array( 'id' => 'desc' ),
                                                            array( 'offset' => $offset, 'length' => $limit ),
                                                            true );
        return $objectList;
    }

    /*!
     \static
     \return the bookmarks of user \a $userID in the order of the folder tree: by priority, newest first
     within the same priority. Used to build the tree, see eZContentBrowseBookmarkFolder::fetchTreeForUser().
    */
    static function fetchTreeListForUser( $userID )
    {
        $objectList = eZPersistentObject::fetchObjectList( eZContentBrowseBookmark::definition(),
                                                            null,
                                                            array( 'user_id' => $userID ),
                                                            array( 'priority' => 'asc', 'id' => 'desc' ),
                                                            null,
                                                            true );
        return $objectList ? $objectList : array();
    }

    /*!
     \static
     \return the bookmarks of user \a $userID that sit directly in folder \a $folderID (0 is the top level).
    */
    static function fetchListForUserInFolder( $userID, $folderID = 0 )
    {
        $objectList = eZPersistentObject::fetchObjectList( eZContentBrowseBookmark::definition(),
                                                            null,
                                                            array( 'user_id' => $userID, 'folder_id' => (int) $folderID ),
                                                            array( 'priority' => 'asc', 'id' => 'desc' ),
                                                            null,
                                                            true );
        return $objectList ? $objectList : array();
    }

    /*!
     \return the folder this bookmark sits in, or null at the top level.
    */
    function folder()
    {
        $folderID = (int) $this->attribute( 'folder_id' );
        return $folderID ? eZContentBrowseBookmarkFolder::fetch( $folderID ) : null;
    }

    /*!
     \static
     Creates a new bookmark item for user \a $userID with node id \a $nodeID and name \a $nodeName.
     The new item is returned. The optional \a $folderID puts it into that folder of the user (0 is the top level);
     a bookmark that exists for the node is replaced and keeps its folder when no folder is given.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    static function createNew( $userID, $nodeID, $nodeName, $folderID = false )
    {
        $db = eZDB::instance();
        $db->begin();
        $userID =(int) $userID;
        $nodeID =(int) $nodeID;
        $nodeName = $db->escapeString( $nodeName );
        $previous = $db->arrayQuery( "SELECT folder_id, priority FROM ezcontentbrowsebookmark WHERE node_id=$nodeID and user_id=$userID" );
        if ( $folderID === false || $folderID === null )
            $folderID = $previous ? (int) $previous[0]['folder_id'] : 0;
        $folderID = (int) $folderID;
        if ( $folderID && !eZContentBrowseBookmarkFolder::fetchForUser( $userID, $folderID ) )
            $folderID = 0;
        $db->query( "DELETE FROM ezcontentbrowsebookmark WHERE node_id=$nodeID and user_id=$userID" );
        $bookmark = new eZContentBrowseBookmark( array( 'user_id' => $userID,
                                                        'node_id' => $nodeID,
                                                        'name' => $nodeName,
                                                        'folder_id' => $folderID,
                                                        'priority' => ( $previous && (int) $previous[0]['folder_id'] === $folderID )
                                                                      ? (int) $previous[0]['priority']
                                                                      : eZContentBrowseBookmarkFolder::nextBookmarkPriority( $userID, $folderID ) ) );
        $bookmark->store();
        $db->commit();
        return $bookmark;
    }

    /*!
     \return the tree node which this item refers to, null for a bookmark of a system page.
    */
    function fetchNode()
    {
        $nodeID = (int) $this->attribute( 'node_id' );
        return $nodeID > 0 ? eZContentObjectTreeNode::fetch( $nodeID ) : null;
    }

    /*!
     \return 'node', or 'view' for a bookmark of a system page (a module view, see expBookmarkView).
    */
    function kind()
    {
        return (int) $this->attribute( 'node_id' ) > 0 ? 'node' : 'view';
    }

    /*!
     \return the address of the system page this bookmark points to ("setup/systemupgrade"), '' for a node bookmark.
    */
    function moduleURI()
    {
        if ( (int) $this->attribute( 'node_id' ) > 0 || !class_exists( 'expBookmarkView' ) )
            return '';
        $uris = expBookmarkView::urisForUser( (int) $this->attribute( 'user_id' ) );
        $id = (int) $this->attribute( 'id' );
        return isset( $uris[$id] ) ? $uris[$id] : '';
    }

    /*!
     Removes the bookmark; the address of a system page bookmark goes with it.
     \note Transaction unsafe.
    */
    public function remove( $conditions = null, $extraConditions = null )
    {
        $isView = $conditions === null && (int) $this->attribute( 'node_id' ) === 0;
        $id = (int) $this->attribute( 'id' );
        parent::remove( $conditions, $extraConditions );
        if ( $isView && class_exists( 'expBookmarkView' ) )
            expBookmarkView::removeForBookmarks( array( $id ) );
    }

    /*!
     \return the content object ID of the tree node which this item refers to.
    */
    function contentObjectID()
    {
        $node = $this->fetchNode();
        if ( $node )
        {
            return $node->attribute( 'contentobject_id' );
        }

        return false;
    }

    /*!
     \static
     Removes all bookmark entries for all users.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    static function cleanup()
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM ezcontentbrowsebookmark" );
        if ( class_exists( 'expBookmarkView' ) )
            expBookmarkView::cleanupAll();
        eZContentBrowseBookmarkFolder::cleanup();
    }

    /*!
     \static
     Removes all bookmark entries for node.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    static function removeByNodeID( $nodeID )
    {
        $db = eZDB::instance();
        $nodeID =(int) $nodeID;
        // node_id 0 is every bookmark of a system page, never a node
        if ( $nodeID <= 0 )
            return;
        $db->query( "DELETE FROM ezcontentbrowsebookmark WHERE node_id=$nodeID" );
    }
}

?>
