<?php
/**
 * Version services: ezjscore/call/expversion::<method>[::arg...]
 *
 * The version history of an object: list, current, published, drafts (own and all), compare two versions, and the
 * draft workflow as writes: create a draft (in a language), publish, discard, remove old versions, revert.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expVersionServices extends expContentServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'The versions of an object, newest first, paged', 'access' => array( 'content', 'versionread' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'limit' => 'int', 'offset' => 'int', 'status' => 'string' ), 'returns' => 'page of versions' ),
        'count' => array( 'summary' => 'Number of versions of an object', 'access' => array( 'content', 'versionread' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => '{count}' ),
        'get' => array( 'summary' => 'One version', 'access' => array( 'content', 'versionread' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'version' => 'int' ), 'returns' => 'version' ),
        'current' => array( 'summary' => 'The current (published) version', 'access' => array( 'content', 'versionread' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'version' ),
        'published' => array( 'summary' => 'The published version number', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => '{version}' ),
        'status' => array( 'summary' => 'The status of a version', 'access' => array( 'content', 'versionread' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'version' => 'int' ), 'returns' => '{status, status_name}' ),
        'creator' => array( 'summary' => 'The creator object of a version', 'access' => array( 'content', 'versionread' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'version' => 'int' ), 'returns' => 'object' ),
        'dataMap' => array( 'summary' => 'The attributes of a version with their values', 'access' => array( 'content', 'versionread' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'version' => 'int', 'language' => 'string' ), 'returns' => 'identifier => attribute' ),
        'translations' => array( 'summary' => 'The languages of a version', 'access' => array( 'content', 'versionread' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'version' => 'int' ), 'returns' => 'language codes' ),
        'nodeAssignments' => array( 'summary' => 'The locations a version is assigned to', 'access' => array( 'content', 'versionread' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'version' => 'int' ), 'returns' => 'assignments' ),
        'compare' => array( 'summary' => 'Differences between two versions, attribute by attribute', 'access' => array( 'content', 'diff' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'version_a' => 'int', 'version_b' => 'int', 'language' => 'string' ), 'returns' => 'attribute differences' ),
        'drafts' => array( 'summary' => 'The drafts of an object', 'access' => array( 'content', 'versionread' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'versions' ),
        'myDrafts' => array( 'summary' => 'The current user\'s drafts of all objects, paged', 'access' => array( 'content', 'edit' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of drafts' ),
        'allDrafts' => array( 'summary' => 'Drafts of all users (needs unrestricted version access), paged', 'access' => array( 'content', 'versionread' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of drafts' ),
        'pending' => array( 'summary' => 'Versions waiting in the publishing queue, paged', 'access' => array( 'content', 'pendinglist' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of versions' ),
        'hasConflicts' => array( 'summary' => 'Whether a draft is older than the published version of its language', 'access' => array( 'content', 'edit' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'version' => 'int', 'language' => 'string' ), 'returns' => '{conflicts}' ),
        'viewUrl' => array( 'summary' => 'The relative URL that previews a version', 'access' => array( 'content', 'versionread' ), 'write' => false, 'args' => array( 'object_id' => 'int', 'version' => 'int', 'language' => 'string' ), 'returns' => '{url}' ),
        'createDraft' => array( 'summary' => 'Creates a draft from the current or a given version. POST: language, copy_from_version', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int' ), 'returns' => 'version' ),
        'createDraftIn' => array( 'summary' => 'Creates a draft for a new or existing language. POST: copy_from_language', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'language' => 'string' ), 'returns' => 'version' ),
        'publish' => array( 'summary' => 'Publishes a draft', 'access' => array( 'content', 'publish' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'version' => 'int' ), 'returns' => 'object' ),
        'discard' => array( 'summary' => 'Discards a draft', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'version' => 'int' ), 'returns' => '{discarded}' ),
        'remove' => array( 'summary' => 'Removes an archived or draft version (never the published one)', 'access' => array( 'content', 'versionremove' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'version' => 'int' ), 'returns' => '{removed}' ),
        'removeArchived' => array( 'summary' => 'Removes archived versions of an object, keeping the newest N', 'access' => array( 'content', 'versionremove' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'keep' => 'int' ), 'returns' => '{removed}' ),
        'revert' => array( 'summary' => 'Creates a draft from an old version (publish it to bring the old content back). POST: language', 'access' => array( 'content', 'edit' ), 'write' => true, 'args' => array( 'object_id' => 'int', 'version' => 'int' ), 'returns' => 'version' ),
        'cleanupDrafts' => array( 'summary' => 'Removes internal (unsaved) drafts older than a number of hours from all objects. Needs unrestricted version removal', 'access' => array( 'content', 'versionremove' ), 'write' => true, 'args' => array( 'hours' => 'int' ), 'returns' => '{cleaned}' ),
    );

    protected static $statusByName = array( 'draft' => 0, 'published' => 1, 'pending' => 2, 'archived' => 3, 'rejected' => 4, 'internal_draft' => 5 );

    protected static function versionArg( $args, $objectIndex, $write = false )
    {
        $o = self::object( self::arg( $args, $objectIndex, 'int' ), $write ? 'edit' : 'read' );
        return array( $o, self::version( $o, self::arg( $args, $objectIndex + 1, 'int' ), $write ) );
    }

    public static function list( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $status = self::arg( $args, 3, 'string', '' );
        if ( $status !== '' && !isset( self::$statusByName[$status] ) )
            throw new expServiceException( 'status is one of ' . implode( ', ', array_keys( self::$statusByName ) ), 400 );
        $items = array();
        foreach ( (array)$o->versions() as $v )
        {
            if ( !$v->canVersionRead() || ( $status !== '' && (int)$v->attribute( 'status' ) !== self::$statusByName[$status] ) )
                continue;
            $items[] = self::exportVersion( $v );
        }
        usort( $items, function ( $a, $b ) { return $b['version'] - $a['version']; } );
        return self::pageOf( $items, $args, 1, 2 );
    }

    public static function count( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'count' => (int)self::object( self::arg( $args, 0, 'int' ) )->getVersionCount() ) );
    }

    public static function get( $args )
    {
        static::guard( __FUNCTION__ );
        list( $o, $v ) = self::versionArg( $args, 0 );
        return self::ok( self::exportVersion( $v ) );
    }

    public static function current( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        return self::ok( self::exportVersion( self::version( $o, $o->attribute( 'current_version' ) ) ) );
    }

    public static function published( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'version' => (int)self::object( self::arg( $args, 0, 'int' ) )->publishedVersion() ) );
    }

    public static function status( $args )
    {
        static::guard( __FUNCTION__ );
        list( $o, $v ) = self::versionArg( $args, 0 );
        $e = self::exportVersion( $v );
        return self::ok( array( 'status' => $e['status'], 'status_name' => $e['status_name'] ) );
    }

    public static function creator( $args )
    {
        static::guard( __FUNCTION__ );
        list( $o, $v ) = self::versionArg( $args, 0 );
        $c = eZContentObject::fetch( (int)$v->attribute( 'creator_id' ) );
        if ( !$c )
            throw new expServiceException( 'The creator no longer exists', 404 );
        return self::ok( array( 'id' => (int)$c->attribute( 'id' ), 'name' => $c->attribute( 'name' ), 'class' => $c->attribute( 'class_identifier' ) ) );
    }

    public static function dataMap( $args )
    {
        static::guard( __FUNCTION__ );
        list( $o, $v ) = self::versionArg( $args, 0 );
        return self::ok( self::exportDataMap( $o, $v->attribute( 'version' ), self::arg( $args, 2, 'string', false ) ) );
    }

    public static function translations( $args )
    {
        static::guard( __FUNCTION__ );
        list( $o, $v ) = self::versionArg( $args, 0 );
        $codes = array();
        foreach ( (array)$v->translationList( false, false ) as $l )
            $codes[] = is_object( $l ) ? $l->attribute( 'language_code' ) : (string)$l;
        return self::ok( $codes );
    }

    public static function nodeAssignments( $args )
    {
        static::guard( __FUNCTION__ );
        list( $o, $v ) = self::versionArg( $args, 0 );
        $out = array();
        foreach ( (array)$v->nodeAssignments() as $a )
            $out[] = array( 'id' => (int)$a->attribute( 'id' ), 'parent_node' => (int)$a->attribute( 'parent_node' ), 'is_main' => (bool)$a->attribute( 'is_main' ),
                            'op_code' => (int)$a->attribute( 'op_code' ), 'sort_field' => (int)$a->attribute( 'sort_field' ), 'priority' => (int)$a->attribute( 'priority' ) );
        return self::ok( $out );
    }

    public static function compare( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'diff' );
        $a = self::version( $o, self::arg( $args, 1, 'int' ) );
        $b = self::version( $o, self::arg( $args, 2, 'int' ) );
        $lang = self::arg( $args, 3, 'string', false ) ?: false;
        $mapA = $o->fetchDataMap( $a->attribute( 'version' ), $lang );
        $mapB = $o->fetchDataMap( $b->attribute( 'version' ), $lang );
        $out = array();
        foreach ( array_unique( array_merge( array_keys( (array)$mapA ), array_keys( (array)$mapB ) ) ) as $id )
        {
            $sa = isset( $mapA[$id] ) ? self::plain( $mapA[$id] ) : null;
            $sb = isset( $mapB[$id] ) ? self::plain( $mapB[$id] ) : null;
            $out[$id] = array( 'a' => $sa, 'b' => $sb, 'changed' => $sa !== $sb );
        }
        return self::ok( $out, array( 'changed' => count( array_filter( $out, function ( $r ) { return $r['changed']; } ) ) ) );
    }

    protected static function plain( eZContentObjectAttribute $a )
    {
        return in_array( $a->attribute( 'data_type_string' ), array( 'ezuser', 'ezpassword' ), true ) ? null : (string)$a->toString();
    }

    public static function drafts( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( (array)$o->versions() as $v )
            if ( (int)$v->attribute( 'status' ) === eZContentObjectVersion::STATUS_DRAFT && $v->canVersionRead() )
                $out[] = self::exportVersion( $v );
        return self::ok( $out );
    }

    public static function myDrafts( $args )
    {
        static::guard( __FUNCTION__ );
        $items = array();
        foreach ( (array)eZContentObjectVersion::fetchForUser( (int)eZUser::currentUser()->attribute( 'contentobject_id' ), eZContentObjectVersion::STATUS_DRAFT ) as $v )
        {
            $row = self::exportVersion( $v );
            $obj = eZContentObject::fetch( $row['object_id'] );
            $row['object_name'] = $obj ? $obj->attribute( 'name' ) : null;
            $items[] = $row;
        }
        return self::pageOf( $items, $args, 0, 1 );
    }

    public static function allDrafts( $args )
    {
        static::guard( __FUNCTION__ );
        $access = eZUser::currentUser()->hasAccessTo( 'content', 'versionread' );
        if ( $access['accessWord'] !== 'yes' )
            throw new expServiceException( 'Listing the drafts of all users needs unrestricted version access', 403 );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $db = eZDB::instance();
        $total = (int)$db->arrayQuery( 'SELECT COUNT(*) AS cnt FROM ezcontentobject_version WHERE status = ' . eZContentObjectVersion::STATUS_DRAFT )[0]['cnt'];
        $items = array();
        foreach ( (array)eZContentObjectVersion::fetchFiltered( array( 'status' => eZContentObjectVersion::STATUS_DRAFT ), $offset, $limit ) as $v )
            $items[] = self::exportVersion( $v );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function pending( $args )
    {
        static::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $db = eZDB::instance();
        $total = (int)$db->arrayQuery( 'SELECT COUNT(*) AS cnt FROM ezcontentobject_version WHERE status = ' . eZContentObjectVersion::STATUS_PENDING )[0]['cnt'];
        $items = array();
        foreach ( (array)eZContentObjectVersion::fetchFiltered( array( 'status' => eZContentObjectVersion::STATUS_PENDING ), $offset, $limit ) as $v )
            $items[] = self::exportVersion( $v );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function hasConflicts( $args )
    {
        static::guard( __FUNCTION__ );
        list( $o, $v ) = self::versionArg( $args, 0 );
        $lang = self::arg( $args, 2, 'string', false ) ?: false;
        return self::ok( array( 'conflicts' => (bool)$v->hasConflicts( $lang ) ) );
    }

    public static function viewUrl( $args )
    {
        static::guard( __FUNCTION__ );
        list( $o, $v ) = self::versionArg( $args, 0 );
        $lang = self::arg( $args, 2, 'string', '' );
        return self::ok( array( 'url' => 'content/versionview/' . (int)$o->attribute( 'id' ) . '/' . (int)$v->attribute( 'version' ) . ( $lang !== '' ? '/' . $lang : '' ) ) );
    }

    // ------------------------------------------------------------------ writes

    public static function createDraft( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $lang = self::post( 'language', 'string', '' );
        $from = self::post( 'copy_from_version', 'int', 0 );
        if ( $from )
            self::version( $o, $from );
        $v = $o->createNewVersion( $from ?: false, true, $lang !== '' ? self::languageCode( $lang, $o ) : false );
        if ( !$v instanceof eZContentObjectVersion )
            throw new expServiceException( 'The draft could not be created', 422 );
        return self::ok( self::exportVersion( $v ) );
    }

    public static function createDraftIn( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $lang = self::languageCode( self::arg( $args, 1, 'string' ), $o );
        $copy = self::post( 'copy_from_language', 'string', '' );
        $v = $o->createNewVersionIn( $lang, $copy !== '' ? self::languageCode( $copy, $o ) : false );
        if ( !$v instanceof eZContentObjectVersion )
            throw new expServiceException( 'The draft could not be created', 422 );
        return self::ok( self::exportVersion( $v ) );
    }

    public static function publish( $args )
    {
        static::guard( __FUNCTION__ );
        list( $o, $v ) = self::versionArg( $args, 0, true );
        if ( !self::publishVersion( $o, $v->attribute( 'version' ) ) )
            throw new expServiceException( 'The version was not published (a workflow may hold it)', 422 );
        eZContentObject::clearCache();
        return self::ok( self::exportObject( eZContentObject::fetch( $o->attribute( 'id' ) ), true ) );
    }

    public static function discard( $args )
    {
        static::guard( __FUNCTION__ );
        list( $o, $v ) = self::versionArg( $args, 0, true );
        if ( !$v->canVersionRemove() )
            throw new expServiceException( 'No access to remove this version', 403 );
        $nr = (int)$v->attribute( 'version' );
        $v->removeThis();
        return self::ok( array( 'discarded' => $nr ) );
    }

    public static function remove( $args )
    {
        static::guard( __FUNCTION__ );
        list( $o, $v ) = self::versionArg( $args, 0 );
        if ( (int)$v->attribute( 'status' ) === eZContentObjectVersion::STATUS_PUBLISHED || (int)$v->attribute( 'version' ) === (int)$o->attribute( 'current_version' ) )
            throw new expServiceException( 'The published version cannot be removed', 409 );
        if ( !$v->canVersionRemove() )
            throw new expServiceException( 'No access to remove this version', 403 );
        $nr = (int)$v->attribute( 'version' );
        $v->removeThis();
        return self::ok( array( 'removed' => $nr ) );
    }

    public static function removeArchived( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $keep = max( 0, self::arg( $args, 1, 'int', 5 ) );
        $archived = array();
        foreach ( (array)$o->versions() as $v )
            if ( (int)$v->attribute( 'status' ) === eZContentObjectVersion::STATUS_ARCHIVED && $v->canVersionRemove() )
                $archived[] = $v;
        usort( $archived, function ( $a, $b ) { return (int)$b->attribute( 'version' ) - (int)$a->attribute( 'version' ); } );
        $removed = array();
        foreach ( array_slice( $archived, $keep ) as $v )
        {
            $removed[] = (int)$v->attribute( 'version' );
            $v->removeThis();
        }
        return self::ok( array( 'removed' => $removed ) );
    }

    public static function revert( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ), 'edit' );
        $old = self::version( $o, self::arg( $args, 1, 'int' ) );
        $lang = self::post( 'language', 'string', '' );
        $nr = $o->copyRevertTo( (int)$old->attribute( 'version' ), $lang !== '' ? self::languageCode( $lang, $o ) : false );
        if ( !$nr )
            throw new expServiceException( 'The draft could not be created', 422 );
        return self::ok( self::exportVersion( self::version( $o, $nr ) ) );
    }

    public static function cleanupDrafts( $args )
    {
        static::guard( __FUNCTION__ );
        $access = eZUser::currentUser()->hasAccessTo( 'content', 'versionremove' );
        if ( $access['accessWord'] !== 'yes' )
            throw new expServiceException( 'Cleaning up drafts of all users needs unrestricted version removal', 403 );
        $hours = max( 1, self::arg( $args, 0, 'int', 24 ) );
        $db = eZDB::instance();
        $cut = time() - $hours * 3600;
        $n = (int)$db->arrayQuery( 'SELECT COUNT(*) AS cnt FROM ezcontentobject_version WHERE status = ' . eZContentObjectVersion::STATUS_INTERNAL_DRAFT . ' AND modified < ' . $cut )[0]['cnt'];
        eZContentObject::cleanupAllInternalDrafts( false, $hours * 3600 );
        return self::ok( array( 'cleaned' => $n ) );
    }
}
