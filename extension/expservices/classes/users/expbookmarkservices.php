<?php
/**
 * expbookmark: the bookmarks of the current user in their tree of virtual folders. Every service works on the
 * current user's own bookmarks and folders only; a folder or bookmark of somebody else does not exist (404).
 * The flat list of all bookmarks stays available as bookmarks (the same order as the content/bookmarks fetch).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expBookmarkServices extends expUsersBase
{
    public static $services = array();

    protected static function me()
    {
        return (int)eZUser::currentUserID();
    }

    protected static function exportBookmark( eZContentBrowseBookmark $b, array $path = array() )
    {
        $node = $b->attribute( 'node' );
        return array( 'type' => 'bookmark', 'id' => (int)$b->attribute( 'id' ), 'node_id' => (int)$b->attribute( 'node_id' ),
                      'name' => $b->attribute( 'name' ), 'folder_id' => (int)$b->attribute( 'folder_id' ), 'priority' => (int)$b->attribute( 'priority' ),
                      'path' => $path, 'url_alias' => $node instanceof eZContentObjectTreeNode ? $node->attribute( 'url_alias' ) : null );
    }

    protected static function exportFolder( eZContentBrowseBookmarkFolder $f )
    {
        return array( 'type' => 'folder', 'id' => (int)$f->attribute( 'id' ), 'name' => $f->attribute( 'name' ),
                      'parent_id' => (int)$f->attribute( 'parent_id' ), 'priority' => (int)$f->attribute( 'priority' ),
                      'created' => self::iso( $f->attribute( 'created' ) ) );
    }

    /** @return eZContentBrowseBookmarkFolder @throws expServiceException 404 */
    protected static function folder( $id )
    {
        $f = eZContentBrowseBookmarkFolder::fetchForUser( self::me(), (int)$id );
        if ( !$f )
            throw new expServiceException( "Bookmark folder $id does not exist", 404 );
        return $f;
    }

    /** @return eZContentBrowseBookmark @throws expServiceException 404 */
    protected static function bookmark( $id )
    {
        $b = eZContentBrowseBookmark::fetch( (int)$id );
        if ( !$b || (int)$b->attribute( 'user_id' ) !== self::me() )
            throw new expServiceException( "Bookmark $id does not exist", 404 );
        return $b;
    }

    protected static function nested( array $nodes )
    {
        $out = array();
        foreach ( $nodes as $n )
        {
            $d = self::exportFolder( $n['folder'] );
            $d['depth'] = $n['depth'];
            $d['path'] = $n['path'];
            $d['count'] = $n['count'];
            $d['bookmarks'] = array_map( function ( $b ) use ( $n ) { return self::exportBookmark( $b, $n['path'] ); }, $n['bookmarks'] );
            $d['folders'] = self::nested( $n['folders'] );
            $out[] = $d;
        }
        return $out;
    }

    // ------------------------------------------------------------------ reads

    public static function tree( array $a = array() )
    {
        self::guard( 'tree' );
        list( $folders, $top ) = eZContentBrowseBookmarkFolder::fetchTreeForUser( self::me() );
        return self::ok( array( 'folders' => self::nested( $folders ),
                                'bookmarks' => array_map( array( 'expBookmarkServices', 'exportBookmark' ), $top ) ) );
    }

    public static function rows( array $a = array() )
    {
        self::guard( 'rows' );
        $rows = array();
        foreach ( eZContentBrowseBookmarkFolder::fetchRowsForUser( self::me(), self::arg( $a, 0, 'int', 0 ) ) as $r )
        {
            if ( $r['type'] === 'folder' )
                $rows[] = array( 'type' => 'folder', 'id' => $r['id'], 'name' => $r['name'], 'parent_id' => $r['parent_id'], 'depth' => $r['depth'], 'path' => $r['path'], 'count' => $r['count'] );
            else
                $rows[] = array_merge( self::exportBookmark( $r['bookmark'], $r['path'] ), array( 'depth' => $r['depth'] ) );
        }
        return self::ok( $rows, array( 'total' => count( $rows ) ) );
    }

    public static function folders( array $a = array() )
    {
        self::guard( 'folders' );
        return self::pageOf( array_map( array( 'expBookmarkServices', 'exportFolder' ), eZContentBrowseBookmarkFolder::fetchListForUser( self::me() ) ), $a, 0, 1 );
    }

    public static function bookmarks( array $a = array() )
    {
        self::guard( 'bookmarks' );
        list( $limit, $offset ) = self::paging( $a, 0, 1 );
        $folder = self::arg( $a, 2, 'int', -1 );
        if ( $folder >= 0 )
        {
            if ( $folder > 0 )
                self::folder( $folder );
            $all = eZContentBrowseBookmark::fetchListForUserInFolder( self::me(), $folder );
            return self::page( array_map( array( 'expBookmarkServices', 'exportBookmark' ), array_slice( $all, $offset, $limit ) ), count( $all ), $offset, $limit );
        }
        // all of them, flat, the order of the content/bookmarks fetch
        $total = (int)eZPersistentObject::count( eZContentBrowseBookmark::definition(), array( 'user_id' => self::me() ) );
        $list = eZContentBrowseBookmark::fetchListForUser( self::me(), $offset, $limit );
        return self::page( array_map( array( 'expBookmarkServices', 'exportBookmark' ), (array)$list ), $total, $offset, $limit );
    }

    // ------------------------------------------------------------------ writes

    protected static function transaction( callable $fn )
    {
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $r = $fn();
            $db->commit();
            return $r;
        }
        catch ( Exception $e )
        {
            $db->rollback();
            throw $e;
        }
    }

    public static function createFolder( array $a = array() )
    {
        self::guard( 'createFolder' );
        $name = eZContentBrowseBookmarkFolder::cleanName( self::post( 'name', 'string' ) );
        $parent = self::post( 'parent_id', 'int', 0 );
        if ( $name === '' )
            throw new expServiceException( 'The folder needs a name', 422 );
        if ( $parent > 0 )
            self::folder( $parent );
        $f = self::transaction( function () use ( $name, $parent ) { return eZContentBrowseBookmarkFolder::createNew( self::me(), $name, $parent ); } );
        return self::ok( self::exportFolder( $f ) );
    }

    public static function renameFolder( array $a = array() )
    {
        self::guard( 'renameFolder' );
        $f = self::folder( self::post( 'id', 'int' ) );
        if ( !$f->rename( self::post( 'name', 'string' ) ) )
            throw new expServiceException( 'The folder needs a name', 422 );
        return self::ok( self::exportFolder( $f ) );
    }

    public static function deleteFolder( array $a = array() )
    {
        self::guard( 'deleteFolder' );
        $f = self::folder( self::post( 'id', 'int' ) );
        $id = (int)$f->attribute( 'id' );
        $with = self::post( 'delete_bookmarks', 'bool', false );
        self::transaction( function () use ( $f, $with ) { return $f->removeFolder( $with ); } );
        return self::ok( array( 'id' => $id, 'removed' => true, 'bookmarks_deleted' => (bool)$with ) );
    }

    public static function moveFolder( array $a = array() )
    {
        self::guard( 'moveFolder' );
        $f = self::folder( self::post( 'id', 'int' ) );
        $parent = self::post( 'parent_id', 'int', 0 );
        if ( $parent > 0 )
            self::folder( $parent );
        $ok = self::transaction( function () use ( $f, $parent ) { return eZContentBrowseBookmarkFolder::moveFolder( self::me(), $f->attribute( 'id' ), $parent ); } );
        if ( !$ok )
            throw new expServiceException( 'A folder cannot move into itself or into a folder below it', 409 );
        return self::ok( self::exportFolder( self::folder( $f->attribute( 'id' ) ) ) );
    }

    public static function moveBookmark( array $a = array() )
    {
        self::guard( 'moveBookmark' );
        $b = self::bookmark( self::post( 'id', 'int' ) );
        $folder = self::post( 'folder_id', 'int', 0 );
        if ( $folder > 0 )
            self::folder( $folder );
        self::transaction( function () use ( $b, $folder ) { return eZContentBrowseBookmarkFolder::moveBookmark( self::me(), $b->attribute( 'id' ), $folder ); } );
        return self::ok( self::exportBookmark( self::bookmark( $b->attribute( 'id' ) ) ) );
    }

    public static function place( array $a = array() )
    {
        self::guard( 'place' );
        $type = self::post( 'type', 'string' );
        if ( $type !== 'folder' && $type !== 'bookmark' )
            throw new expServiceException( 'type must be folder or bookmark', 400 );
        $id = self::post( 'id', 'int' );
        $type === 'folder' ? self::folder( $id ) : self::bookmark( $id );
        $parent = self::post( 'parent_id', 'int', 0 );
        if ( $parent > 0 )
            self::folder( $parent );
        $ok = self::transaction( function () use ( $type, $id, $parent ) {
            return eZContentBrowseBookmarkFolder::place( self::me(), $type, $id, $parent, self::post( 'before_id', 'int', 0 ) );
        } );
        if ( !$ok )
            throw new expServiceException( 'A folder cannot move into itself or into a folder below it', 409 );
        return self::ok( array( 'type' => $type, 'id' => $id, 'parent_id' => $parent ) );
    }

    public static function addBookmark( array $a = array() )
    {
        self::guard( 'addBookmark' );
        $node = self::node( self::post( 'node_id', 'int' ), 'read' );
        $folder = self::post( 'folder_id', 'int', 0 );
        if ( $folder > 0 )
            self::folder( $folder );
        $b = self::transaction( function () use ( $node, $folder ) {
            return eZContentBrowseBookmark::createNew( self::me(), $node->attribute( 'node_id' ), $node->attribute( 'name' ), $folder );
        } );
        return self::ok( self::exportBookmark( $b ) );
    }

    public static function removeBookmark( array $a = array() )
    {
        self::guard( 'removeBookmark' );
        $b = self::bookmark( self::post( 'id', 'int' ) );
        $id = (int)$b->attribute( 'id' );
        $b->remove();
        return self::ok( array( 'id' => $id, 'removed' => true ) );
    }
}

expBookmarkServices::$services = expUsersBase::specs( array(
    'tree' => array( 'The whole bookmark tree of the current user in one call: nested folders (with depth, path, count of bookmarks inside) and the top level bookmarks', 'user', 'r', '', 'folders, bookmarks' ),
    'rows' => array( 'The tree as one flat list in display order (folders before the bookmarks of their level); optionally only the content of a folder', 'user', 'r', 'folder:int', 'list of folder and bookmark rows with depth' ),
    'folders' => array( 'The folders of the current user as a flat list, paged', 'user', 'r', 'limit:int,offset:int', 'paged folders' ),
    'bookmarks' => array( 'The bookmarks, paged: all of them flat (the order of the content bookmarks fetch), or only those of one folder (0 is the top level)', 'user', 'r', 'limit:int,offset:int,folder:int', 'paged bookmarks' ),
    'createFolder' => array( 'Create a folder (POST name, parent_id: 0 or missing is the top level)', 'user', 'w', 'name:string,parent_id:int', 'folder' ),
    'renameFolder' => array( 'Rename a folder (POST id, name)', 'user', 'w', 'id:int,name:string', 'folder' ),
    'deleteFolder' => array( 'Delete a folder (POST id, delete_bookmarks): its bookmarks and folders move up one level; with delete_bookmarks everything inside is deleted', 'user', 'w', 'id:int,delete_bookmarks:bool', 'id, removed' ),
    'moveFolder' => array( 'Move a folder into another folder or to the top level (POST id, parent_id); 409 into itself or below itself', 'user', 'w', 'id:int,parent_id:int', 'folder' ),
    'moveBookmark' => array( 'Move a bookmark into a folder or to the top level (POST id, folder_id)', 'user', 'w', 'id:int,folder_id:int', 'bookmark' ),
    'place' => array( 'Put a folder or bookmark into a folder in front of another entry of the same kind (POST type folder|bookmark, id, parent_id, before_id; 0 is last)', 'user', 'w', 'type:string,id:int,parent_id:int,before_id:int', 'type, id, parent_id' ),
    'addBookmark' => array( 'Bookmark a node, optionally in a folder (POST node_id, folder_id)', 'user', 'w', 'node_id:int,folder_id:int', 'bookmark' ),
    'removeBookmark' => array( 'Remove a bookmark (POST id)', 'user', 'w', 'id:int', 'id, removed' ),
) );
