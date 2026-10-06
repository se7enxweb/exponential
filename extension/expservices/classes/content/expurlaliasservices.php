<?php
/**
 * URL alias and wildcard services: ezjscore/call/expurlalias::<method>[::arg...]
 *
 * The nice URLs of the site: the aliases of nodes and objects, resolving a path to its action, normalising text
 * into URL form, global aliases and redirects, and URL wildcards. Writes create and remove custom aliases and
 * wildcards.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expUrlAliasServices extends expContentServiceBase
{
    public static $services = array(
        'forNode' => array( 'summary' => 'The URL aliases of a node. type: alias (custom), name (generated) or all', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int', 'type' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of aliases' ),
        'forObject' => array( 'summary' => 'The generated URL of every location of an object', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'nodes with url' ),
        'path' => array( 'summary' => 'The URL path of a node (generated alias)', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '{path}' ),
        'resolve' => array( 'summary' => 'Resolves a URL path to its action and node', 'access' => 'user', 'write' => false, 'args' => array( 'path' => 'string' ), 'returns' => '{action, node}' ),
        'exists' => array( 'summary' => 'Whether a path is taken by an alias', 'access' => array( 'content', 'read' ), 'write' => false, 'args' => array( 'path' => 'string' ), 'returns' => '{exists}' ),
        'children' => array( 'summary' => 'The alias elements directly below a path (empty path: the top)', 'access' => array( 'content', 'urltranslator' ), 'write' => false, 'args' => array( 'path' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of aliases' ),
        'normalize' => array( 'summary' => 'Turns text into the form the URL transformation rules give it', 'access' => 'user', 'write' => false, 'args' => array( 'text' => 'string' ), 'returns' => '{alias}' ),
        'normalizePath' => array( 'summary' => 'Normalises every element of a path', 'access' => 'user', 'write' => false, 'args' => array( 'path' => 'string' ), 'returns' => '{path}' ),
        'list' => array( 'summary' => 'All custom aliases (type alias), name or all, paged', 'access' => array( 'content', 'urltranslator' ), 'write' => false, 'args' => array( 'type' => 'string', 'limit' => 'int', 'offset' => 'int', 'text' => 'string' ), 'returns' => 'page of aliases' ),
        'count' => array( 'summary' => 'Number of aliases of a type', 'access' => array( 'content', 'urltranslator' ), 'write' => false, 'args' => array( 'type' => 'string' ), 'returns' => '{count}' ),
        'redirects' => array( 'summary' => 'Aliases that redirect to the real URL, paged', 'access' => array( 'content', 'urltranslator' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of aliases' ),
        'byAction' => array( 'summary' => 'The aliases of an action such as eznode:43 or module:search', 'access' => array( 'content', 'urltranslator' ), 'write' => false, 'args' => array( 'action' => 'string' ), 'returns' => 'aliases' ),
        'pathPrefix' => array( 'summary' => 'The path prefix of the siteaccess', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => '{prefix}' ),
        'wildcards' => array( 'summary' => 'The URL wildcards, paged', 'access' => array( 'content', 'urltranslator' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'page of wildcards' ),
        'wildcardCount' => array( 'summary' => 'Number of wildcards', 'access' => array( 'content', 'urltranslator' ), 'write' => false, 'args' => array(), 'returns' => '{count}' ),
        'wildcard' => array( 'summary' => 'One wildcard by id', 'access' => array( 'content', 'urltranslator' ), 'write' => false, 'args' => array( 'wildcard_id' => 'int' ), 'returns' => 'wildcard' ),
        'wildcardBySource' => array( 'summary' => 'A wildcard by its source URL', 'access' => array( 'content', 'urltranslator' ), 'write' => false, 'args' => array( 'source' => 'string' ), 'returns' => 'wildcard' ),
        'wildcardMatches' => array( 'summary' => 'Whether a path is caught by any wildcard', 'access' => array( 'content', 'urltranslator' ), 'write' => false, 'args' => array( 'path' => 'string' ), 'returns' => '{matches}' ),
        'createAlias' => array( 'summary' => 'Adds a custom URL alias to a node. POST: alias, language, parent_is_root, redirects', 'access' => array( 'content', 'urltranslator' ), 'write' => true, 'args' => array( 'node_id' => 'int' ), 'returns' => '{alias, path}' ),
        'removeAlias' => array( 'summary' => 'Removes custom aliases of a node. POST: elements (list of parent.md5.language from forNode)', 'access' => array( 'content', 'urltranslator' ), 'write' => true, 'args' => array( 'node_id' => 'int' ), 'returns' => '{removed}' ),
        'removeAllAliases' => array( 'summary' => 'Removes every custom alias of a node', 'access' => array( 'content', 'urltranslator' ), 'write' => true, 'args' => array( 'node_id' => 'int' ), 'returns' => '{removed}' ),
        'createWildcard' => array( 'summary' => 'Adds a wildcard. POST: source, destination, type (forward or direct)', 'access' => array( 'content', 'urltranslator' ), 'write' => true, 'args' => array(), 'returns' => 'wildcard' ),
        'removeWildcard' => array( 'summary' => 'Removes a wildcard', 'access' => array( 'content', 'urltranslator' ), 'write' => true, 'args' => array( 'wildcard_id' => 'int' ), 'returns' => '{removed}' ),
        'removeWildcards' => array( 'summary' => 'Removes several wildcards. POST: ids', 'access' => array( 'content', 'urltranslator' ), 'write' => true, 'args' => array(), 'returns' => '{removed}' ),
    );

    protected static function exportAlias( $e )
    {
        $lang = $e->attribute( 'language_object' );
        $locale = $lang ? $lang->attribute( 'locale' ) : null;
        $md5 = $e->attribute( 'text_md5' );
        return array( 'id' => (int)$e->attribute( 'id' ), 'parent' => (int)$e->attribute( 'parent' ), 'text' => $e->attribute( 'text' ), 'path' => $e->attribute( 'path' ),
                      'action' => $e->attribute( 'action' ), 'is_alias' => (bool)$e->attribute( 'is_alias' ), 'is_original' => (bool)$e->attribute( 'is_original' ),
                      'redirects' => (bool)$e->attribute( 'alias_redirects' ), 'language' => $locale, 'always_available' => (bool)$e->attribute( 'always_available' ),
                      'ref' => (int)$e->attribute( 'parent' ) . '.' . $md5 . '.' . $locale );
    }

    protected static function typeArg( $value )
    {
        if ( !in_array( $value, array( 'alias', 'name', 'all' ), true ) )
            throw new expServiceException( 'type is alias, name or all', 400 );
        return $value;
    }

    protected static function query( $type, ?array $actions = null, $limit = false, $offset = 0, $text = null )
    {
        $q = new eZURLAliasQuery();
        $q->type = $type;
        $q->languages = false;
        if ( $actions !== null )
            $q->actions = $actions;
        $q->limit = $limit;
        $q->offset = $offset;
        if ( $text !== null && $text !== '' )
            $q->text = $text;
        return $q;
    }

    protected static function pageOfQuery( eZURLAliasQuery $q, $limit, $offset )
    {
        $total = (int)$q->count();
        $items = array();
        foreach ( (array)$q->fetchAll() as $e )
            $items[] = self::exportAlias( $e );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function forNode( $args )
    {
        static::guard( __FUNCTION__ );
        $n = self::node( self::arg( $args, 0, 'int' ) );
        $type = self::typeArg( self::arg( $args, 1, 'string', 'all' ) );
        list( $limit, $offset ) = self::paging( $args, 2, 3 );
        return self::pageOfQuery( self::query( $type, array( 'eznode:' . (int)$n->attribute( 'node_id' ) ), $limit, $offset ), $limit, $offset );
    }

    public static function forObject( $args )
    {
        static::guard( __FUNCTION__ );
        $o = self::object( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( (array)$o->assignedNodes() as $n )
            $out[] = array( 'node_id' => (int)$n->attribute( 'node_id' ), 'url_alias' => $n->urlAlias(), 'is_main' => (int)$n->attribute( 'node_id' ) === (int)$n->attribute( 'main_node_id' ) );
        return self::ok( $out );
    }

    public static function path( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'path' => self::node( self::arg( $args, 0, 'int' ) )->urlAlias() ) );
    }

    public static function resolve( $args )
    {
        static::guard( __FUNCTION__ );
        $path = eZURLAliasML::cleanURL( self::arg( $args, 0, 'string' ) );
        $rows = $path === '' ? array() : (array)eZURLAliasML::fetchByPath( $path );
        if ( !$rows )
            throw new expServiceException( "No alias at '$path'", 404 );
        $e = $rows[0];
        $action = $e->attribute( 'action' );
        $nodeId = eZURLAliasML::nodeIDFromAction( $action );
        $node = null;
        if ( $nodeId )
        {
            $n = eZContentObjectTreeNode::fetch( (int)$nodeId );
            if ( !$n || !$n->canRead() )
                throw new expServiceException( "No alias at '$path'", 404 );
            $node = self::exportNode( $n );
        }
        return self::ok( array( 'path' => $e->attribute( 'path' ), 'action' => $action, 'is_alias' => (bool)$e->attribute( 'is_alias' ), 'node' => $node ) );
    }

    public static function exists( $args )
    {
        static::guard( __FUNCTION__ );
        $path = eZURLAliasML::cleanURL( self::arg( $args, 0, 'string' ) );
        return self::ok( array( 'exists' => $path !== '' && count( (array)eZURLAliasML::fetchByPath( $path ) ) > 0 ) );
    }

    public static function children( $args )
    {
        static::guard( __FUNCTION__ );
        $path = eZURLAliasML::cleanURL( self::arg( $args, 0, 'string', '' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $parent = 0;
        if ( $path !== '' )
        {
            $rows = (array)eZURLAliasML::fetchByPath( $path );
            if ( !$rows )
                throw new expServiceException( "No alias at '$path'", 404 );
            $parent = (int)$rows[0]->attribute( 'id' );
        }
        $items = array();
        foreach ( (array)eZURLAliasML::fetchByParentID( $parent, true, false, false ) as $e )
            $items[] = self::exportAlias( $e );
        return self::pageOf( $items, $args, 1, 2 );
    }

    public static function normalize( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'alias' => eZURLAliasML::convertToAlias( self::arg( $args, 0, 'string' ) ) ) );
    }

    public static function normalizePath( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'path' => eZURLAliasML::convertPathToAlias( self::arg( $args, 0, 'string' ) ) ) );
    }

    public static function list( $args )
    {
        static::guard( __FUNCTION__ );
        $type = self::typeArg( self::arg( $args, 0, 'string', 'alias' ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        return self::pageOfQuery( self::query( $type, null, $limit, $offset, self::arg( $args, 3, 'string', '' ) ), $limit, $offset );
    }

    public static function count( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'count' => (int)self::query( self::typeArg( self::arg( $args, 0, 'string', 'alias' ) ) )->count() ) );
    }

    public static function redirects( $args )
    {
        static::guard( __FUNCTION__ );
        $q = self::query( 'alias', null, 1000, 0 );
        $items = array();
        foreach ( (array)$q->fetchAll() as $e )
            if ( $e->attribute( 'alias_redirects' ) )
                $items[] = self::exportAlias( $e );
        return self::pageOf( $items, $args, 0, 1 );
    }

    public static function byAction( $args )
    {
        static::guard( __FUNCTION__ );
        $action = self::arg( $args, 0, 'string' );
        if ( !preg_match( '/^[a-z]+:[A-Za-z0-9_\/.-]+$/', $action ) )
            throw new expServiceException( 'An action looks like eznode:43', 400 );
        $out = array();
        foreach ( (array)self::query( 'all', array( $action ) )->fetchAll() as $e )
            $out[] = self::exportAlias( $e );
        return self::ok( $out );
    }

    public static function pathPrefix( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'prefix' => (string)eZURLAliasML::getPathPrefix() ) );
    }

    protected static function exportWildcard( eZURLWildcard $w )
    {
        $type = (int)$w->attribute( 'type' );
        return array( 'id' => (int)$w->attribute( 'id' ), 'source' => $w->attribute( 'source_url' ), 'destination' => $w->attribute( 'destination_url' ),
                      'type' => $type === eZURLWildcard::TYPE_FORWARD ? 'forward' : ( $type === eZURLWildcard::TYPE_DIRECT ? 'direct' : 'none' ) );
    }

    public static function wildcards( $args )
    {
        static::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $items = array();
        foreach ( (array)eZURLWildcard::fetchList( $offset, $limit ) as $w )
            $items[] = self::exportWildcard( $w );
        return self::page( $items, (int)eZURLWildcard::fetchListCount(), $offset, $limit );
    }

    public static function wildcardCount( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'count' => (int)eZURLWildcard::fetchListCount() ) );
    }

    public static function wildcard( $args )
    {
        static::guard( __FUNCTION__ );
        $w = eZURLWildcard::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$w )
            throw new expServiceException( 'No such wildcard', 404 );
        return self::ok( self::exportWildcard( $w ) );
    }

    public static function wildcardBySource( $args )
    {
        static::guard( __FUNCTION__ );
        $w = eZURLWildcard::fetchBySourceURL( ltrim( self::arg( $args, 0, 'string' ), '/' ) );
        if ( !$w )
            throw new expServiceException( 'No such wildcard', 404 );
        return self::ok( self::exportWildcard( $w ) );
    }

    public static function wildcardMatches( $args )
    {
        static::guard( __FUNCTION__ );
        return self::ok( array( 'matches' => (bool)eZURLWildcard::wildcardExists( eZURLAliasML::cleanURL( self::arg( $args, 0, 'string' ) ) ) ) );
    }

    public static function createAlias( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'edit' );
        $text = trim( self::post( 'alias', 'string' ) );
        if ( $text === '' )
            throw new expServiceException( 'The alias is empty', 422 );
        $language = eZContentLanguage::fetchByLocale( self::languageCode( self::post( 'language', 'string', '' ) ) );
        $parentId = 0;
        $linkId = 0;
        $existing = (array)self::query( 'name', array( 'eznode:' . (int)$node->attribute( 'node_id' ) ) )->fetchAll();
        if ( $existing )
        {
            $parentId = (int)$existing[0]->attribute( 'parent' );
            $linkId = (int)$existing[0]->attribute( 'id' );
        }
        if ( self::post( 'parent_is_root', 'bool', false ) )
            $parentId = 0;
        $always = (int)$node->object()->attribute( 'language_mask' ) & 1;
        $result = eZURLAliasML::storePath( $text, 'eznode:' . (int)$node->attribute( 'node_id' ), $language, $linkId, $always, $parentId, true, false, false, self::post( 'redirects', 'bool', true ) );
        if ( $result['status'] === eZURLAliasML::LINK_ALREADY_TAKEN )
            throw new expServiceException( 'That alias is taken by another node: ' . $result['path'], 409 );
        if ( $result['status'] !== true )
            throw new expServiceException( 'The alias could not be created (status ' . $result['status'] . ')', 422 );
        ezpEvent::getInstance()->notify( 'content/cache', array( array( (int)$node->attribute( 'node_id' ) ) ) );
        return self::ok( array( 'alias' => $text, 'path' => $result['path'] ) );
    }

    public static function removeAlias( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'edit' );
        $removed = array();
        $mine = array();
        foreach ( (array)self::query( 'alias', array( 'eznode:' . (int)$node->attribute( 'node_id' ) ) )->fetchAll() as $e )
            $mine[self::exportAlias( $e )['ref']] = true;
        foreach ( self::post( 'elements', 'list' ) as $element )
        {
            if ( !preg_match( '#^([0-9]+)\.([a-fA-F0-9]+)\.([a-zA-Z0-9-]+)$#', $element, $m ) )
                throw new expServiceException( "Bad element '$element' (parent.md5.language)", 400 );
            if ( !isset( $mine[$element] ) )
                throw new expServiceException( "Element '$element' is not a custom alias of this node", 404 );
            eZURLAliasML::removeSingleEntry( (int)$m[1], $m[2], $m[3] );
            $removed[] = $element;
        }
        ezpEvent::getInstance()->notify( 'content/cache', array( array( (int)$node->attribute( 'node_id' ) ) ) );
        return self::ok( array( 'removed' => $removed ) );
    }

    public static function removeAllAliases( $args )
    {
        static::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ), 'edit' );
        $removed = 0;
        while ( true )
        {
            $q = self::query( 'alias', array( 'eznode:' . (int)$node->attribute( 'node_id' ) ), 50, 0 );
            $list = (array)$q->fetchAll();
            if ( !$list )
                break;
            foreach ( $list as $e )
            {
                eZURLAliasML::removeSingleEntry( (int)$e->attribute( 'parent' ), $e->attribute( 'text_md5' ), $e->attribute( 'language_object' ) );
                $removed++;
            }
        }
        ezpEvent::getInstance()->notify( 'content/cache', array( array( (int)$node->attribute( 'node_id' ) ) ) );
        return self::ok( array( 'removed' => $removed ) );
    }

    public static function createWildcard( $args )
    {
        static::guard( __FUNCTION__ );
        $source = ltrim( trim( self::post( 'source', 'string' ) ), '/' );
        $destination = trim( self::post( 'destination', 'string' ) );
        $type = self::post( 'type', 'string', 'direct' );
        if ( !in_array( $type, array( 'forward', 'direct' ), true ) )
            throw new expServiceException( 'type is forward or direct', 400 );
        if ( $source === '' || $destination === '' )
            throw new expServiceException( 'source and destination are required', 400 );
        if ( eZURLWildcard::fetchBySourceURL( $source ) )
            throw new expServiceException( 'A wildcard with that source exists', 409 );
        $w = new eZURLWildcard( array( 'source_url' => $source, 'destination_url' => $destination,
                                       'type' => $type === 'forward' ? eZURLWildcard::TYPE_FORWARD : eZURLWildcard::TYPE_DIRECT ) );
        $w->store();
        eZURLWildcard::expireCache();
        return self::ok( self::exportWildcard( eZURLWildcard::fetchBySourceURL( $source ) ) );
    }

    public static function removeWildcard( $args )
    {
        static::guard( __FUNCTION__ );
        $id = self::arg( $args, 0, 'int' );
        if ( !eZURLWildcard::fetch( $id ) )
            throw new expServiceException( 'No such wildcard', 404 );
        eZURLWildcard::removeByIDs( array( $id ) );
        eZURLWildcard::expireCache();
        return self::ok( array( 'removed' => array( $id ) ) );
    }

    public static function removeWildcards( $args )
    {
        static::guard( __FUNCTION__ );
        $ids = array_map( 'intval', self::post( 'ids', 'list' ) );
        foreach ( $ids as $id )
            if ( !eZURLWildcard::fetch( $id ) )
                throw new expServiceException( "No wildcard $id", 404 );
        eZURLWildcard::removeByIDs( $ids );
        eZURLWildcard::expireCache();
        return self::ok( array( 'removed' => $ids ) );
    }
}
