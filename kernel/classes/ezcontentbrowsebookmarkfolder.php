<?php
/**
 * File containing the eZContentBrowseBookmarkFolder class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZContentBrowseBookmarkFolder ezcontentbrowsebookmarkfolder.php
  \brief A virtual folder that organises the bookmarks of one user in a tree.

  A folder belongs to a user (user_id), sits in another folder (parent_id, 0 is the top level)
  and has a name and a priority (ascending order within its parent). The depth is unlimited.
  A bookmark sits in a folder through eZContentBrowseBookmark::folder_id (0 is the top level).

  Everything that was written before folders existed keeps its meaning: a bookmark without a
  folder is a top level bookmark and eZContentBrowseBookmark::fetchListForUser() still returns all
  bookmarks of the user as one flat list.

\code
$userID = eZUser::currentUserID();
$folder = eZContentBrowseBookmarkFolder::createNew( $userID, 'Projects' );          // top level
$sub    = eZContentBrowseBookmarkFolder::createNew( $userID, 'Press', $folder->attribute( 'id' ) );
eZContentBrowseBookmarkFolder::moveBookmark( $userID, $bookmarkID, $sub->attribute( 'id' ) );
$rows   = eZContentBrowseBookmarkFolder::fetchRowsForUser( $userID );               // the whole tree, depth first
\endcode
*/

class eZContentBrowseBookmarkFolder extends eZPersistentObject
{
    /// Folder names are cut to this length (the column is varchar(255)).
    const MAX_NAME_LENGTH = 255;

    static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'user_id' => array( 'name' => 'UserID',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true,
                                                             'foreign_class' => 'eZUser',
                                                             'foreign_attribute' => 'contentobject_id',
                                                             'multiplicity' => '1..*' ),
                                         'parent_id' => array( 'name' => 'ParentID',
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         'name' => array( 'name' => 'Name',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ),
                                         'priority' => array( 'name' => 'Priority',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true ),
                                         'created' => array( 'name' => 'Created',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ) ),
                      'keys' => array( 'id' ),
                      'increment_key' => 'id',
                      'sort' => array( 'priority' => 'asc', 'id' => 'asc' ),
                      'class_name' => 'eZContentBrowseBookmarkFolder',
                      'name' => 'expbookmark_folder' );
    }

    /*!
     \static
     \return the folder \a $folderID, or null.
    */
    static function fetch( $folderID )
    {
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int) $folderID ), true );
    }

    /*!
     \static
     \return the folder \a $folderID when it belongs to user \a $userID, else null.
    */
    static function fetchForUser( $userID, $folderID )
    {
        $folder = self::fetch( $folderID );
        return ( $folder && (int) $folder->attribute( 'user_id' ) === (int) $userID ) ? $folder : null;
    }

    /*!
     \static
     \return all folders of user \a $userID as a flat list (priority, then id).
    */
    static function fetchListForUser( $userID )
    {
        $list = eZPersistentObject::fetchObjectList( self::definition(), null,
                                                      array( 'user_id' => (int) $userID ),
                                                      array( 'priority' => 'asc', 'id' => 'asc' ),
                                                      null, true );
        return $list ? $list : array();
    }

    /*!
     \static
     \return the folders directly inside \a $parentID (0 is the top level) of user \a $userID.
    */
    static function fetchChildren( $userID, $parentID = 0 )
    {
        $list = eZPersistentObject::fetchObjectList( self::definition(), null,
                                                      array( 'user_id' => (int) $userID, 'parent_id' => (int) $parentID ),
                                                      array( 'priority' => 'asc', 'id' => 'asc' ),
                                                      null, true );
        return $list ? $list : array();
    }

    /*!
     \static
     Creates a folder named \a $name inside \a $parentID (0 is the top level) of user \a $userID.
     \return the new folder, or false when the name is empty or the parent is not a folder of the user.
     \note Transaction unsafe.
    */
    static function createNew( $userID, $name, $parentID = 0 )
    {
        $name = self::cleanName( $name );
        $userID = (int) $userID;
        $parentID = (int) $parentID;
        if ( $name === '' )
            return false;
        if ( $parentID && !self::fetchForUser( $userID, $parentID ) )
            return false;

        $db = eZDB::instance();
        $row = $db->arrayQuery( "SELECT MAX(priority) AS p FROM expbookmark_folder WHERE user_id=$userID AND parent_id=$parentID" );
        $priority = ( $row && $row[0]['p'] !== null ) ? (int) $row[0]['p'] + 1 : 0;

        $folder = new eZContentBrowseBookmarkFolder( array( 'user_id' => $userID,
                                                            'parent_id' => $parentID,
                                                            'name' => $name,
                                                            'priority' => $priority,
                                                            'created' => time() ) );
        $folder->store();
        return $folder;
    }

    /*!
     \static
     Names are trimmed, tags and control characters removed, and cut to MAX_NAME_LENGTH characters.
    */
    static function cleanName( $name )
    {
        $name = trim( preg_replace( '/[\x00-\x1F\x7F]+/u', ' ', strip_tags( (string) $name ) ) );
        if ( function_exists( 'mb_substr' ) )
            return mb_substr( $name, 0, self::MAX_NAME_LENGTH, 'UTF-8' );
        return substr( $name, 0, self::MAX_NAME_LENGTH );
    }

    /*!
     Renames the folder. \return true, or false when the name is empty.
    */
    function rename( $name )
    {
        $name = self::cleanName( $name );
        if ( $name === '' )
            return false;
        $this->setAttribute( 'name', $name );
        $this->store();
        return true;
    }

    /*!
     \return the ids of the folder and of all folders below it.
    */
    function subtreeIDs()
    {
        $byParent = array();
        foreach ( self::fetchListForUser( $this->attribute( 'user_id' ) ) as $f )
            $byParent[(int) $f->attribute( 'parent_id' )][] = (int) $f->attribute( 'id' );

        $ids = array();
        $queue = array( (int) $this->attribute( 'id' ) );
        while ( $queue )
        {
            $id = array_shift( $queue );
            if ( isset( $ids[$id] ) )
                continue; // a cycle in damaged data: every folder is visited once
            $ids[$id] = $id;
            if ( isset( $byParent[$id] ) )
                $queue = array_merge( $queue, $byParent[$id] );
        }
        return array_values( $ids );
    }

    /*!
     \static
     \return true when \a $folderID is \a $ancestorID or lies below it (cycle guard for moves).
    */
    static function isInside( $userID, $folderID, $ancestorID )
    {
        $folderID = (int) $folderID;
        $ancestorID = (int) $ancestorID;
        $seen = array();
        while ( $folderID )
        {
            if ( $folderID === $ancestorID )
                return true;
            if ( isset( $seen[$folderID] ) )
                return false; // damaged data with a cycle
            $seen[$folderID] = true;
            $f = self::fetchForUser( $userID, $folderID );
            if ( !$f )
                return false;
            $folderID = (int) $f->attribute( 'parent_id' );
        }
        return false;
    }

    /*!
     \static
     Moves folder \a $folderID of user \a $userID into \a $parentID (0 is the top level), as the last one.
     \return true, or false when the folder or the target is not the user's or the target is the folder itself or lies below it.
     \note Transaction unsafe.
    */
    static function moveFolder( $userID, $folderID, $parentID )
    {
        $folder = self::fetchForUser( $userID, $folderID );
        $parentID = (int) $parentID;
        if ( !$folder )
            return false;
        if ( $parentID && ( !self::fetchForUser( $userID, $parentID ) || self::isInside( $userID, $parentID, $folderID ) ) )
            return false;
        if ( (int) $folder->attribute( 'parent_id' ) === $parentID )
            return true;

        $db = eZDB::instance();
        $row = $db->arrayQuery( "SELECT MAX(priority) AS p FROM expbookmark_folder WHERE user_id=" . (int) $userID . " AND parent_id=$parentID" );
        $folder->setAttribute( 'parent_id', $parentID );
        $folder->setAttribute( 'priority', ( $row && $row[0]['p'] !== null ) ? (int) $row[0]['p'] + 1 : 0 );
        $folder->store();
        return true;
    }

    /*!
     \static
     Moves bookmark \a $bookmarkID of user \a $userID into folder \a $folderID (0 is the top level), as the last one.
     \return true, or false when the bookmark or the folder is not the user's.
     \note Transaction unsafe.
    */
    static function moveBookmark( $userID, $bookmarkID, $folderID )
    {
        $folderID = (int) $folderID;
        $bookmark = eZContentBrowseBookmark::fetch( $bookmarkID );
        if ( !$bookmark || (int) $bookmark->attribute( 'user_id' ) !== (int) $userID )
            return false;
        if ( $folderID && !self::fetchForUser( $userID, $folderID ) )
            return false;
        if ( (int) $bookmark->attribute( 'folder_id' ) === $folderID )
            return true;

        $bookmark->setAttribute( 'folder_id', $folderID );
        $bookmark->setAttribute( 'priority', self::nextBookmarkPriority( $userID, $folderID ) );
        $bookmark->store();
        return true;
    }

    /*!
     \static
     \return the priority after the last bookmark of folder \a $folderID.
    */
    static function nextBookmarkPriority( $userID, $folderID )
    {
        $db = eZDB::instance();
        $row = $db->arrayQuery( "SELECT MAX(priority) AS p FROM ezcontentbrowsebookmark WHERE user_id=" . (int) $userID . " AND folder_id=" . (int) $folderID );
        return ( $row && $row[0]['p'] !== null ) ? (int) $row[0]['p'] + 1 : 0;
    }

    /*!
     \static
     Sets the order within one folder: \a $type is 'folder' or 'bookmark', \a $ids the ids in the wanted order
     (ids that are not the user's are ignored).
     \note Transaction unsafe.
    */
    static function reorder( $userID, $type, array $ids )
    {
        $db = eZDB::instance();
        $userID = (int) $userID;
        $table = $type === 'folder' ? 'expbookmark_folder' : 'ezcontentbrowsebookmark';
        $position = 0;
        foreach ( $ids as $id )
        {
            $db->query( "UPDATE $table SET priority=" . $position++ . " WHERE id=" . (int) $id . " AND user_id=$userID" );
        }
        return true;
    }

    /*!
     \static
     Puts a folder or a bookmark of user \a $userID into folder \a $parentID (0 is the top level) in front of
     the entry \a $beforeID of the same kind (0 puts it last) and numbers the entries of that folder.
     This is what drag and drop and the up/down buttons do. A folder cannot go into itself or below itself.
     \param $type 'folder' or 'bookmark'
     \return true, or false when something is not the user's or the move would make a cycle.
     \note Transaction unsafe.
    */
    static function place( $userID, $type, $id, $parentID, $beforeID = 0 )
    {
        $userID = (int) $userID;
        $id = (int) $id;
        $parentID = (int) $parentID;
        $beforeID = (int) $beforeID;
        $isFolder = $type === 'folder';

        if ( $isFolder )
        {
            if ( !self::moveFolder( $userID, $id, $parentID ) )
                return false;
            $siblings = array_map( function ( $f ) { return (int) $f->attribute( 'id' ); }, self::fetchChildren( $userID, $parentID ) );
        }
        else
        {
            if ( !self::moveBookmark( $userID, $id, $parentID ) )
                return false;
            $siblings = array_map( function ( $b ) { return (int) $b->attribute( 'id' ); },
                                   eZContentBrowseBookmark::fetchListForUserInFolder( $userID, $parentID ) );
        }
        // the moved entry is in the list already (last): take it out and put it before $beforeID
        $siblings = array_values( array_diff( $siblings, array( $id ) ) );
        $at = $beforeID ? array_search( $beforeID, $siblings, true ) : false;
        if ( $at === false )
            $siblings[] = $id;
        else
            array_splice( $siblings, $at, 0, array( $id ) );
        self::reorder( $userID, $type, $siblings );
        return true;
    }
    /*!
     Removes the folder. Without \a $deleteBookmarks the bookmarks and the subfolders move up one level
     (to the parent of this folder) and no bookmark is deleted. With \a $deleteBookmarks the folder, all
     folders below it and all bookmarks in them are deleted.
     \note Transaction unsafe.
    */
    function removeFolder( $deleteBookmarks = false )
    {
        $db = eZDB::instance();
        $id = (int) $this->attribute( 'id' );
        $userID = (int) $this->attribute( 'user_id' );
        $parentID = (int) $this->attribute( 'parent_id' );

        if ( $deleteBookmarks )
        {
            $ids = implode( ',', $this->subtreeIDs() );
            $db->query( "DELETE FROM ezcontentbrowsebookmark WHERE user_id=$userID AND folder_id IN ($ids)" );
            $db->query( "DELETE FROM expbookmark_folder WHERE user_id=$userID AND id IN ($ids)" );
            return true;
        }

        // behind the existing entries of the parent, in their old order
        $nextFolder = (int) self::nextFolderPriority( $userID, $parentID );
        foreach ( self::fetchChildren( $userID, $id ) as $child )
        {
            $child->setAttribute( 'parent_id', $parentID );
            $child->setAttribute( 'priority', $nextFolder++ );
            $child->store();
        }
        $nextBookmark = (int) self::nextBookmarkPriority( $userID, $parentID );
        $list = eZPersistentObject::fetchObjectList( eZContentBrowseBookmark::definition(), null,
                                                      array( 'user_id' => $userID, 'folder_id' => $id ),
                                                      array( 'priority' => 'asc', 'id' => 'desc' ), null, true );
        foreach ( $list ? $list : array() as $bookmark )
        {
            $bookmark->setAttribute( 'folder_id', $parentID );
            $bookmark->setAttribute( 'priority', $nextBookmark++ );
            $bookmark->store();
        }
        $this->remove();
        return true;
    }

    static function nextFolderPriority( $userID, $parentID )
    {
        $db = eZDB::instance();
        $row = $db->arrayQuery( "SELECT MAX(priority) AS p FROM expbookmark_folder WHERE user_id=" . (int) $userID . " AND parent_id=" . (int) $parentID );
        return ( $row && $row[0]['p'] !== null ) ? (int) $row[0]['p'] + 1 : 0;
    }

    /*!
     \static
     The whole tree of user \a $userID from two queries (the folders and the bookmarks).
     \return a list of top level nodes; every folder node is
       array( 'type' => 'folder', 'id', 'name', 'parent_id', 'depth', 'path' => names from the top, 'folder' => object,
              'folders' => child folder nodes, 'bookmarks' => bookmark objects, 'count' => bookmarks inside incl. subfolders )
     Folders whose parent is missing or that lie in a cycle (damaged data) are shown at the top level.
     The top level bookmarks are returned in the second element: array( $folderNodes, $topBookmarks ).
    */
    static function fetchTreeForUser( $userID )
    {
        $folders = self::fetchListForUser( $userID );
        $bookmarks = eZContentBrowseBookmark::fetchTreeListForUser( $userID );

        $nodes = array();
        foreach ( $folders as $f )
        {
            $fid = (int) $f->attribute( 'id' );
            $nodes[$fid] = array( 'type' => 'folder', 'id' => $fid, 'name' => $f->attribute( 'name' ),
                                  'parent_id' => (int) $f->attribute( 'parent_id' ), 'depth' => 0, 'path' => array(),
                                  'folder' => $f, 'folders' => array(), 'bookmarks' => array(), 'count' => 0 );
        }

        // find the folders that are reachable from the top; the others (orphans, cycles) are moved to the top
        $top = array();
        foreach ( $nodes as $fid => &$n )
        {
            $pid = $n['parent_id'];
            $seen = array( $fid => true );
            $ok = true;
            while ( $pid )
            {
                if ( !isset( $nodes[$pid] ) || isset( $seen[$pid] ) )
                {
                    $ok = false;
                    break;
                }
                $seen[$pid] = true;
                $pid = $nodes[$pid]['parent_id'];
            }
            if ( !$ok )
                $n['parent_id'] = 0;
            if ( !$n['parent_id'] )
                $top[] = $fid;
        }
        unset( $n );
        foreach ( $nodes as $fid => $n )
        {
            if ( $n['parent_id'] )
                $nodes[$n['parent_id']]['folders'][] = $fid;
        }

        $topBookmarks = array();
        foreach ( $bookmarks as $b )
        {
            $fid = (int) $b->attribute( 'folder_id' );
            if ( $fid && isset( $nodes[$fid] ) )
                $nodes[$fid]['bookmarks'][] = $b;
            else
                $topBookmarks[] = $b;
        }

        $build = function ( $fid, $depth, $path ) use ( &$build, &$nodes )
        {
            $n = $nodes[$fid];
            $n['depth'] = $depth;
            $n['path'] = array_merge( $path, array( $n['name'] ) );
            $n['count'] = count( $n['bookmarks'] );
            $children = array();
            foreach ( $n['folders'] as $cid )
            {
                $child = $build( $cid, $depth + 1, $n['path'] );
                $n['count'] += $child['count'];
                $children[] = $child;
            }
            $n['folders'] = $children;
            return $n;
        };
        $tree = array();
        foreach ( $top as $fid )
            $tree[] = $build( $fid, 0, array() );

        return array( $tree, $topBookmarks );
    }

    /*!
     \static
     The tree of user \a $userID as one flat list in display order (depth first, folders before the
     bookmarks of their level), so that a template needs no recursion. Every row is
       array( 'type' => 'folder'|'bookmark', 'depth', 'id', 'name', 'parent_id' (folder rows) / 'folder_id' (bookmark rows),
              'path' => folder names above the row, 'count' (folder rows), 'bookmark' (bookmark rows: the object) )
     \a $onlyFolderID restricts the list to the content of that folder (0 for everything).
    */
    static function fetchRowsForUser( $userID, $onlyFolderID = 0 )
    {
        list( $tree, $topBookmarks ) = self::fetchTreeForUser( $userID );
        $rows = array();
        $emit = function ( $nodes, $bookmarks, $depth, $path ) use ( &$emit, &$rows )
        {
            foreach ( $nodes as $n )
            {
                $rows[] = array( 'type' => 'folder', 'depth' => $depth, 'id' => $n['id'], 'name' => $n['name'],
                                 'parent_id' => $n['parent_id'], 'path' => $path, 'count' => $n['count'],
                                 'folder' => $n['folder'] );
                $emit( $n['folders'], $n['bookmarks'], $depth + 1, $n['path'] );
            }
            foreach ( $bookmarks as $b )
            {
                $rows[] = array( 'type' => 'bookmark', 'depth' => $depth, 'id' => (int) $b->attribute( 'id' ),
                                 'name' => $b->attribute( 'name' ), 'folder_id' => (int) $b->attribute( 'folder_id' ),
                                 'path' => $path, 'bookmark' => $b );
            }
        };

        $onlyFolderID = (int) $onlyFolderID;
        if ( $onlyFolderID )
        {
            $find = function ( $nodes ) use ( &$find, $onlyFolderID )
            {
                foreach ( $nodes as $n )
                {
                    if ( $n['id'] === $onlyFolderID )
                        return $n;
                    if ( $hit = $find( $n['folders'] ) )
                        return $hit;
                }
                return null;
            };
            $n = $find( $tree );
            if ( $n )
                $emit( $n['folders'], $n['bookmarks'], 0, $n['path'] );
            return $rows;
        }
        $emit( $tree, $topBookmarks, 0, array() );
        return $rows;
    }

    /*!
     \static
     Runs the folder action posted from the bookmark page (field BookmarkFolderAction):
     create (FolderName, ParentFolderID), rename (FolderID, FolderName), delete (FolderID, DeleteBookmarks),
     move_bookmark (BookmarkID or BookmarkIDArray[], FolderID), move_folder (FolderID, ParentFolderID),
     reorder (Type folder|bookmark, IDs = comma separated ids in the new order),
     place (Type folder|bookmark, ID, ParentFolderID, BeforeID: puts the entry into a folder in front of another).
     Only the folders and bookmarks of user \a $userID are touched.
     \return array( 'level' => 'feedback'|'error', 'text' => translated message )
    */
    static function handleAction( $userID, $http )
    {
        $tr = function ( $text, $args = null ) { return ezpI18n::tr( 'kernel/content', $text, null, $args ); };
        $action = (string) $http->postVariable( 'BookmarkFolderAction' );
        $post = function ( $name, $default = '' ) use ( $http ) { return $http->hasPostVariable( $name ) ? $http->postVariable( $name ) : $default; };
        $error = function ( $text ) { return array( 'level' => 'error', 'text' => $text ); };
        $ok = function ( $text ) { return array( 'level' => 'feedback', 'text' => $text ); };

        $db = eZDB::instance();
        $db->begin();
        try
        {
            switch ( $action )
            {
                case 'create':
                    $folder = self::createNew( $userID, $post( 'FolderName' ), (int) $post( 'ParentFolderID', 0 ) );
                    $result = $folder
                        ? $ok( $tr( 'The folder "%name" was created.', array( '%name' => $folder->attribute( 'name' ) ) ) )
                        : $error( $tr( 'The folder could not be created: it needs a name and an existing parent folder.' ) );
                    break;
                case 'rename':
                    $folder = self::fetchForUser( $userID, (int) $post( 'FolderID' ) );
                    $result = ( $folder && $folder->rename( $post( 'FolderName' ) ) )
                        ? $ok( $tr( 'The folder was renamed to "%name".', array( '%name' => $folder->attribute( 'name' ) ) ) )
                        : $error( $tr( 'The folder could not be renamed: it needs a name.' ) );
                    break;
                case 'delete':
                    $folder = self::fetchForUser( $userID, (int) $post( 'FolderID' ) );
                    if ( !$folder )
                    {
                        $result = $error( $tr( 'The folder does not exist.' ) );
                        break;
                    }
                    $name = $folder->attribute( 'name' );
                    $withBookmarks = (bool) $post( 'DeleteBookmarks', false );
                    $folder->removeFolder( $withBookmarks );
                    $result = $ok( $withBookmarks
                        ? $tr( 'The folder "%name" and everything in it were deleted.', array( '%name' => $name ) )
                        : $tr( 'The folder "%name" was deleted, its bookmarks and folders moved up one level.', array( '%name' => $name ) ) );
                    break;
                case 'move_bookmark':
                    $ids = $http->hasPostVariable( 'BookmarkIDArray' ) ? (array) $http->postVariable( 'BookmarkIDArray' )
                         : ( $http->hasPostVariable( 'DeleteIDArray' ) ? (array) $http->postVariable( 'DeleteIDArray' ) // the checkboxes of the bookmark page
                                                                        : array( $post( 'BookmarkID' ) ) );
                    $moved = 0;
                    foreach ( $ids as $id )
                        if ( self::moveBookmark( $userID, (int) $id, (int) $post( 'FolderID', 0 ) ) )
                            ++$moved;
                    $result = $moved
                        ? $ok( $tr( 'Moved %count bookmark(s).', array( '%count' => $moved ) ) )
                        : $error( $tr( 'Nothing was moved: choose a bookmark and one of your folders.' ) );
                    break;
                case 'move_folder':
                    $result = self::moveFolder( $userID, (int) $post( 'FolderID' ), (int) $post( 'ParentFolderID', 0 ) )
                        ? $ok( $tr( 'The folder was moved.' ) )
                        : $error( $tr( 'The folder cannot be moved there: not into itself or into a folder below it.' ) );
                    break;
                case 'place':
                    $result = self::place( $userID, $post( 'Type' ) === 'folder' ? 'folder' : 'bookmark', (int) $post( 'ID' ),
                                           (int) $post( 'ParentFolderID', 0 ), (int) $post( 'BeforeID', 0 ) )
                        ? $ok( $tr( 'The entry was moved.' ) )
                        : $error( $tr( 'It cannot be moved there: not into itself or into a folder below it.' ) );
                    break;
                case 'reorder':
                    $ids = array_filter( array_map( 'intval', explode( ',', (string) $post( 'IDs' ) ) ) );
                    self::reorder( $userID, $post( 'Type' ) === 'folder' ? 'folder' : 'bookmark', $ids );
                    $result = $ok( $tr( 'The order was saved.' ) );
                    break;
                default:
                    $result = $error( $tr( 'Unknown bookmark action.' ) );
            }
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollback();
            $result = $error( $e->getMessage() );
        }
        return $result;
    }
    /*!
     \static
     Removes all folders of all users.
     \note Transaction unsafe.
    */
    static function cleanup()
    {
        eZDB::instance()->query( 'DELETE FROM expbookmark_folder' );
    }
}

?>
