<?php
/**
 * ezjscore/call/exptags::<service> - the tag tree of the eztags extension: tree, search, children, synonyms,
 * translations, related content, and writes (add, rename, delete, synonyms, attach and detach to a node), with the
 * eztags policies (tags/read, add, edit, delete, addsynonym, makesynonym). Answers 404 "not available" when the
 * extension is inactive.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expTagsServices extends expAttrServiceBase
{
    public static $services = array(
        'available' => array( 'summary' => 'Whether the eztags extension is active, and how many tags there are', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'available, tags' ),
        'roots' => array( 'summary' => 'The top level tags', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of tags' ),
        'get' => array( 'summary' => 'One tag with counts', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'tag' ),
        'children' => array( 'summary' => 'The children of a tag', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of tags' ),
        'childrencount' => array( 'summary' => 'How many children a tag has', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'count' ),
        'tree' => array( 'summary' => 'A tag subtree as nested nodes down to a depth (at most 5)', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int', 'depth' => 'int' ), 'returns' => 'nested tag nodes' ),
        'path' => array( 'summary' => 'The path of a tag from the root', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'list of tags root first' ),
        'parent' => array( 'summary' => 'The parent of a tag', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'tag or null' ),
        'search' => array( 'summary' => 'Tags whose keyword contains the text', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'text' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of tags' ),
        'suggest' => array( 'summary' => 'Autocomplete: tags whose keyword starts with the text', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'text' => 'string', 'limit' => 'int' ), 'returns' => 'list of tags' ),
        'bykeyword' => array( 'summary' => 'Tags with exactly this keyword', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'keyword' => 'string' ), 'returns' => 'list of tags' ),
        'byremote' => array( 'summary' => 'The tag with a remote id', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'remote' => 'string' ), 'returns' => 'tag' ),
        'bypath' => array( 'summary' => 'The tag at a path string (/1/5/7/)', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'path' => 'string' ), 'returns' => 'tag' ),
        'synonyms' => array( 'summary' => 'The synonyms of a tag', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'list of tags' ),
        'translations' => array( 'summary' => 'The translations (keyword per locale) of a tag', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'list of locale, keyword' ),
        'related' => array( 'summary' => 'The objects tagged with a tag that the user may read', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of objects' ),
        'relatedcount' => array( 'summary' => 'How many published objects carry a tag', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'count' ),
        'ofnode' => array( 'summary' => 'The tags attached to a node', 'access' => array( 'content', 'read' ), 'write' => false,
            'args' => array( 'node' => 'int' ), 'returns' => 'list of attribute with tags' ),
        'popular' => array( 'summary' => 'The most used tags', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int' ), 'returns' => 'list of tag, uses' ),
        'recent' => array( 'summary' => 'The most recently modified tags', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array( 'limit' => 'int' ), 'returns' => 'list of tags' ),
        'stats' => array( 'summary' => 'Counts of tags, synonyms, translations and attachments', 'access' => array( 'tags', 'read' ), 'write' => false,
            'args' => array(), 'returns' => 'tags, synonyms, translations, links, roots' ),
        'add' => array( 'summary' => 'Creates a tag below a parent (0: top level)', 'access' => array( 'tags', 'add' ), 'write' => true,
            'args' => array( 'parent' => 'int' ), 'returns' => 'the new tag; POST keyword, locale (optional)' ),
        'rename' => array( 'summary' => 'Changes the keyword of a tag in a locale', 'access' => array( 'tags', 'edit' ), 'write' => true,
            'args' => array( 'id' => 'int' ), 'returns' => 'the tag; POST keyword, locale (optional)' ),
        'addsynonym' => array( 'summary' => 'Adds a synonym to a tag', 'access' => array( 'tags', 'addsynonym' ), 'write' => true,
            'args' => array( 'id' => 'int' ), 'returns' => 'the synonym; POST keyword, locale (optional)' ),
        'delete' => array( 'summary' => 'Deletes a tag with its children and synonyms', 'access' => array( 'tags', 'delete' ), 'write' => true,
            'args' => array( 'id' => 'int' ), 'returns' => 'deleted tag ids' ),
        'attach' => array( 'summary' => 'Attaches an existing tag to a node tags attribute', 'access' => array( 'content', 'edit' ), 'write' => true,
            'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'the tags now on the attribute; POST tag (id)' ),
        'detach' => array( 'summary' => 'Removes a tag from a node tags attribute', 'access' => array( 'content', 'edit' ), 'write' => true,
            'args' => array( 'node' => 'int', 'attribute' => 'string' ), 'returns' => 'the tags now on the attribute; POST tag (id)' ),
    );

    protected static function need()
    {
        if ( !class_exists( 'eZTagsObject' ) )
            throw new expServiceException( 'The eztags extension is not available', 404 );
    }

    protected static function tagId( $args, $i = 0 )
    {
        self::need();
        return self::arg( $args, $i, 'int' );
    }

    protected static function fetchTag( $id )
    {
        $tag = eZTagsObject::fetchWithMainTranslation( (int)$id );
        if ( !$tag instanceof eZTagsObject )
            throw new expServiceException( "Tag $id does not exist", 404 );
        return $tag;
    }

    protected static function exportTag( eZTagsObject $tag, $counts = true )
    {
        $out = array( 'id' => (int)$tag->attribute( 'id' ), 'parent_id' => (int)$tag->attribute( 'parent_id' ),
                      'main_tag_id' => (int)$tag->attribute( 'main_tag_id' ), 'keyword' => $tag->attribute( 'keyword' ),
                      'depth' => (int)$tag->attribute( 'depth' ), 'path_string' => $tag->attribute( 'path_string' ),
                      'modified' => self::iso( $tag->attribute( 'modified' ) ), 'remote_id' => $tag->attribute( 'remote_id' ),
                      'is_synonym' => (int)$tag->attribute( 'main_tag_id' ) !== 0 );
        if ( $counts )
        {
            $out['children_count'] = (int)$tag->getChildrenCount();
            $out['synonyms_count'] = (int)$tag->getSynonymsCount();
        }
        return $out;
    }

    protected static function exportTags( array $tags )
    {
        $list = array();
        foreach ( $tags as $tag )
            $list[] = self::exportTag( $tag );
        return $list;
    }

    public static function available( $args )
    {
        self::guard( __FUNCTION__ );
        $has = class_exists( 'eZTagsObject' );
        return self::ok( array( 'available' => $has, 'tags' => $has ? self::scalar( 'SELECT COUNT(*) AS n FROM eztags' ) : 0 ) );
    }

    public static function roots( $args )
    {
        self::need();
        self::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        return self::page( self::exportTags( eZTagsObject::fetchByParentID( 0, $offset, $limit ) ), eZTagsObject::childrenCountByParentID( 0 ), $offset, $limit );
    }

    public static function get( $args )
    {
        self::guard( __FUNCTION__ );
        $tag = self::fetchTag( self::tagId( $args ) );
        $out = self::exportTag( $tag );
        $out['related_count'] = (int)$tag->getRelatedObjectsCount();
        $out['languages'] = array_keys( $tag->languageNameArray() );
        $out['url'] = $tag->getUrl();
        return self::ok( $out );
    }

    public static function children( $args )
    {
        self::guard( __FUNCTION__ );
        $tag = self::fetchTag( self::tagId( $args ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        return self::page( self::exportTags( $tag->getChildren( $offset, $limit ) ), $tag->getChildrenCount(), $offset, $limit );
    }

    public static function childrencount( $args )
    {
        self::guard( __FUNCTION__ );
        $tag = self::fetchTag( self::tagId( $args ) );
        return self::ok( array( 'count' => (int)$tag->getChildrenCount() ) );
    }

    protected static function subtree( eZTagsObject $tag, $depth )
    {
        $out = self::exportTag( $tag, false );
        $out['children'] = array();
        if ( $depth > 0 )
            foreach ( $tag->getChildren( 0, 200 ) as $child )
                $out['children'][] = self::subtree( $child, $depth - 1 );
        return $out;
    }

    public static function tree( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $id = self::arg( $args, 0, 'int', 0 );
        $depth = max( 0, min( 5, self::arg( $args, 1, 'int', 2 ) ) );
        if ( $id === 0 )
        {
            $nodes = array();
            foreach ( eZTagsObject::fetchByParentID( 0, 0, 200 ) as $root )
                $nodes[] = self::subtree( $root, $depth - 1 );
            return self::ok( $nodes, array( 'depth' => $depth ) );
        }
        return self::ok( self::subtree( self::fetchTag( $id ), $depth ), array( 'depth' => $depth ) );
    }

    public static function path( $args )
    {
        self::guard( __FUNCTION__ );
        $tag = self::fetchTag( self::tagId( $args ) );
        $list = array();
        foreach ( $tag->getPath() as $p )
            $list[] = self::exportTag( $p, false );
        $list[] = self::exportTag( $tag, false );
        return self::ok( $list );
    }

    public static function parent( $args )
    {
        self::guard( __FUNCTION__ );
        $tag = self::fetchTag( self::tagId( $args ) );
        $p = $tag->hasParent() ? $tag->getParent() : null;
        return self::ok( $p instanceof eZTagsObject ? self::exportTag( $p ) : null );
    }

    /** ids of tags whose keyword matches the LIKE pattern, newest keyword rows first */
    protected static function idsLike( $pattern, $limit, $offset, &$total )
    {
        $e = eZDB::instance()->escapeString( $pattern );
        $where = "keyword LIKE '$e' AND status=1";
        $total = self::scalar( "SELECT COUNT(DISTINCT keyword_id) AS n FROM eztags_keyword WHERE $where" );
        $rows = self::rows( "SELECT DISTINCT keyword_id FROM eztags_keyword WHERE $where ORDER BY keyword_id", array( 'limit' => $limit, 'offset' => $offset ) );
        $tags = array();
        foreach ( $rows as $r )
        {
            $t = eZTagsObject::fetchWithMainTranslation( (int)$r['keyword_id'] );
            if ( $t instanceof eZTagsObject )
                $tags[] = $t;
        }
        return $tags;
    }

    protected static function likeEscape( $text )
    {
        return str_replace( array( '\\', '%', '_' ), array( '\\\\', '\\%', '\\_' ), $text );
    }

    public static function search( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $text = trim( self::arg( $args, 0, 'string' ) );
        if ( $text === '' )
            throw new expServiceException( 'The search text is empty', 422 );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $total = 0;
        $tags = self::idsLike( '%' . self::likeEscape( $text ) . '%', $limit, $offset, $total );
        return self::page( self::exportTags( $tags ), $total, $offset, $limit );
    }

    public static function suggest( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $text = trim( self::arg( $args, 0, 'string' ) );
        list( $limit ) = self::paging( $args, 1, 99 );
        $total = 0;
        $tags = $text === '' ? array() : self::idsLike( self::likeEscape( $text ) . '%', $limit, 0, $total );
        return self::ok( self::exportTags( $tags ), array( 'limit' => $limit ) );
    }

    public static function bykeyword( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $list = eZTagsObject::fetchByKeyword( self::arg( $args, 0, 'string' ) );
        return self::ok( self::exportTags( is_array( $list ) ? $list : array() ) );
    }

    public static function byremote( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $tag = eZTagsObject::fetchByRemoteID( self::arg( $args, 0, 'string' ) );
        if ( !$tag instanceof eZTagsObject )
            throw new expServiceException( 'No tag with this remote id', 404 );
        return self::ok( self::exportTag( $tag ) );
    }

    public static function bypath( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $path = self::arg( $args, 0, 'string' );
        if ( !preg_match( '#^/(\d+/)+$#', $path ) )
            throw new expServiceException( 'The path must look like /1/5/7/', 400 );
        $tag = null;
        foreach ( (array)eZTagsObject::fetchByPathString( $path ) as $candidate )
            if ( $candidate->attribute( 'path_string' ) === $path )
                $tag = $candidate;
        if ( !$tag instanceof eZTagsObject )
            throw new expServiceException( 'No tag at this path', 404 );
        return self::ok( self::exportTag( $tag ) );
    }

    public static function synonyms( $args )
    {
        self::guard( __FUNCTION__ );
        $tag = self::fetchTag( self::tagId( $args ) );
        return self::ok( self::exportTags( $tag->getSynonyms() ) );
    }

    public static function translations( $args )
    {
        self::guard( __FUNCTION__ );
        $tag = self::fetchTag( self::tagId( $args ) );
        $list = array();
        foreach ( $tag->getTranslations() as $t )
            $list[] = array( 'locale' => $t->attribute( 'locale' ), 'keyword' => $t->attribute( 'keyword' ),
                             'main' => (int)$t->attribute( 'language_id' ) === (int)$tag->attribute( 'main_language_id' ) );
        return self::ok( $list );
    }

    public static function related( $args )
    {
        self::guard( __FUNCTION__ );
        $tag = self::fetchTag( self::tagId( $args ) );
        list( $limit, $offset ) = self::paging( $args, 1, 2 );
        $items = array();
        foreach ( $tag->getRelatedObjects() as $object )
        {
            if ( !$object->canRead() )
                continue;
            $main = $object->attribute( 'main_node' );
            $items[] = array( 'object_id' => (int)$object->attribute( 'id' ), 'name' => $object->attribute( 'name' ),
                              'class' => $object->attribute( 'class_identifier' ), 'node_id' => $main ? (int)$main->attribute( 'node_id' ) : 0,
                              'modified' => self::iso( $object->attribute( 'modified' ) ) );
        }
        return self::pageOf( $items, array( 1 => $limit, 2 => $offset ), 1, 2 );
    }

    public static function relatedcount( $args )
    {
        self::guard( __FUNCTION__ );
        $tag = self::fetchTag( self::tagId( $args ) );
        return self::ok( array( 'count' => (int)$tag->getRelatedObjectsCount() ) );
    }

    public static function ofnode( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $node = self::node( self::arg( $args, 0, 'int' ) );
        $list = array();
        foreach ( self::attributesOf( $node, 'eztags' ) as $identifier => $attribute )
        {
            $content = $attribute->content();
            $tags = $content instanceof eZTags ? $content->tags() : array();
            $list[] = array( 'attribute' => $identifier, 'tags' => self::exportTags( $tags ) );
        }
        return self::ok( $list );
    }

    public static function popular( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        list( $limit ) = self::paging( $args, 0, 99 );
        $rows = self::rows( 'SELECT keyword_id, COUNT(*) AS n FROM eztags_attribute_link GROUP BY keyword_id ORDER BY n DESC, keyword_id', array( 'limit' => $limit ) );
        $list = array();
        foreach ( $rows as $r )
        {
            $t = eZTagsObject::fetchWithMainTranslation( (int)$r['keyword_id'] );
            if ( $t instanceof eZTagsObject )
                $list[] = array( 'tag' => self::exportTag( $t, false ), 'uses' => (int)$r['n'] );
        }
        return self::ok( $list, array( 'limit' => $limit ) );
    }

    public static function recent( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        list( $limit ) = self::paging( $args, 0, 99 );
        $rows = self::rows( 'SELECT id FROM eztags ORDER BY modified DESC, id DESC', array( 'limit' => $limit ) );
        $tags = array();
        foreach ( $rows as $r )
        {
            $t = eZTagsObject::fetchWithMainTranslation( (int)$r['id'] );
            if ( $t instanceof eZTagsObject )
                $tags[] = $t;
        }
        return self::ok( self::exportTags( $tags ), array( 'limit' => $limit ) );
    }

    public static function stats( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        return self::ok( array( 'tags' => self::scalar( 'SELECT COUNT(*) AS n FROM eztags WHERE main_tag_id=0' ),
                                'synonyms' => self::scalar( 'SELECT COUNT(*) AS n FROM eztags WHERE main_tag_id<>0' ),
                                'translations' => self::scalar( 'SELECT COUNT(*) AS n FROM eztags_keyword' ),
                                'links' => self::scalar( 'SELECT COUNT(*) AS n FROM eztags_attribute_link' ),
                                'roots' => self::scalar( 'SELECT COUNT(*) AS n FROM eztags WHERE parent_id=0 AND main_tag_id=0' ) ) );
    }

    // ------------------------------------------------------------------ writes

    protected static function localeOf()
    {
        $code = self::post( 'locale', 'string', '' );
        $language = $code !== '' ? eZContentLanguage::fetchByLocale( $code ) : eZContentLanguage::topPriorityLanguage();
        if ( !$language instanceof eZContentLanguage )
            throw new expServiceException( "Unknown language '$code'", 422 );
        return $language;
    }

    protected static function createTag( $parentId, $mainTagId, $keyword, eZContentLanguage $language, $parent )
    {
        if ( $keyword === '' )
            throw new expServiceException( 'The keyword is empty', 422 );
        if ( eZTagsObject::exists( 0, $keyword, (int)$parentId ) )
            throw new expServiceException( 'A tag or synonym with that keyword exists at this location', 409 );
        $db = eZDB::instance();
        $db->begin();
        $mask = eZContentLanguage::maskByLocale( array( $language->attribute( 'locale' ) ), false );
        $tag = new eZTagsObject( array( 'parent_id' => (int)$parentId, 'main_tag_id' => (int)$mainTagId,
                                        'depth' => $parent instanceof eZTagsObject ? $parent->attribute( 'depth' ) + 1 : 1,
                                        'path_string' => $parent instanceof eZTagsObject ? $parent->attribute( 'path_string' ) : '/',
                                        'main_language_id' => $language->attribute( 'id' ), 'language_mask' => $mask ), $language->attribute( 'locale' ) );
        $tag->store();
        $translation = new eZTagsKeyword( array( 'keyword_id' => $tag->attribute( 'id' ), 'language_id' => $language->attribute( 'id' ),
                                                 'keyword' => $keyword, 'locale' => $language->attribute( 'locale' ), 'status' => eZTagsKeyword::STATUS_PUBLISHED ) );
        $translation->store();
        $tag->setAttribute( 'path_string', $tag->attribute( 'path_string' ) . $tag->attribute( 'id' ) . '/' );
        $tag->store();
        $tag->updateModified();
        $db->commit();
        return eZTagsObject::fetchWithMainTranslation( $tag->attribute( 'id' ) );
    }

    public static function add( $args )
    {
        self::guard( __FUNCTION__ );
        self::need();
        $parentId = self::arg( $args, 0, 'int', 0 );
        $parent = $parentId > 0 ? self::fetchTag( $parentId ) : null;
        $keyword = trim( self::post( 'keyword', 'string' ) );
        $tag = self::createTag( $parentId, 0, $keyword, self::localeOf(), $parent );
        return self::ok( self::exportTag( $tag ) );
    }

    public static function addsynonym( $args )
    {
        self::guard( __FUNCTION__ );
        $main = self::fetchTag( self::tagId( $args ) );
        if ( (int)$main->attribute( 'main_tag_id' ) !== 0 )
            throw new expServiceException( 'A synonym cannot have synonyms', 422 );
        $parent = $main->hasParent() ? $main->getParent() : null;
        $tag = self::createTag( (int)$main->attribute( 'parent_id' ), (int)$main->attribute( 'id' ), trim( self::post( 'keyword', 'string' ) ), self::localeOf(), $parent );
        return self::ok( self::exportTag( $tag ) );
    }

    public static function rename( $args )
    {
        self::guard( __FUNCTION__ );
        $tag = self::fetchTag( self::tagId( $args ) );
        $keyword = trim( self::post( 'keyword', 'string' ) );
        if ( $keyword === '' )
            throw new expServiceException( 'The keyword is empty', 422 );
        $language = self::localeOf();
        $existing = eZTagsKeyword::fetch( $tag->attribute( 'id' ), $language->attribute( 'locale' ) );
        if ( !$existing instanceof eZTagsKeyword )
            throw new expServiceException( 'The tag has no translation in ' . $language->attribute( 'locale' ), 404 );
        if ( $existing->attribute( 'keyword' ) !== $keyword && eZTagsObject::exists( $tag->attribute( 'id' ), $keyword, (int)$tag->attribute( 'parent_id' ) ) )
            throw new expServiceException( 'A tag or synonym with that keyword exists at this location', 409 );
        $existing->setAttribute( 'keyword', $keyword );
        $existing->store();
        $tag->updateModified();
        return self::ok( self::exportTag( eZTagsObject::fetchWithMainTranslation( $tag->attribute( 'id' ) ) ) );
    }

    public static function delete( $args )
    {
        self::guard( __FUNCTION__ );
        $tag = self::fetchTag( self::tagId( $args ) );
        $id = (int)$tag->attribute( 'id' );
        $tag->recursivelyDeleteTag();
        return self::ok( array( 'deleted' => $id ) );
    }

    protected static function mutate( $args, $attach )
    {
        self::need();
        $node = self::node( self::arg( $args, 0, 'int' ), 'edit' );
        $attribute = self::attributeOf( $node, 'eztags', self::arg( $args, 1, 'string', null ) );
        $tag = self::fetchTag( self::post( 'tag', 'int' ) );
        $content = $attribute->content();
        $ids = array();
        $keywords = array();
        $parents = array();
        $locales = array();
        $found = false;
        foreach ( $content instanceof eZTags ? $content->tags() : array() as $t )
        {
            if ( (int)$t->attribute( 'id' ) === (int)$tag->attribute( 'id' ) )
            {
                $found = true;
                if ( !$attach )
                    continue;
            }
            $ids[] = (int)$t->attribute( 'id' );
            $keywords[] = $t->attribute( 'keyword' );
            $parents[] = (int)$t->attribute( 'parent_id' );
            $locales[] = $t->getMainTranslation()->attribute( 'locale' );
        }
        if ( $attach && !$found )
        {
            $ids[] = (int)$tag->attribute( 'id' );
            $keywords[] = $tag->attribute( 'keyword' );
            $parents[] = (int)$tag->attribute( 'parent_id' );
            $locales[] = $tag->getMainTranslation()->attribute( 'locale' );
        }
        if ( $attach && $found )
            throw new expServiceException( 'The tag is already attached', 409 );
        if ( !$attach && !$found )
            throw new expServiceException( 'The tag is not attached', 404 );
        $new = eZTags::createFromStrings( $attribute, implode( '|#', $ids ), implode( '|#', $keywords ), implode( '|#', $parents ), implode( '|#', $locales ) );
        $attribute->setContent( $new );
        $new->store( $attribute );
        $attribute->store();
        eZContentCacheManager::clearContentCache( $attribute->attribute( 'contentobject_id' ) );
        return self::ok( array( 'node' => (int)$node->attribute( 'node_id' ), 'attribute' => $attribute->contentClassAttributeIdentifier(),
                                'tags' => self::exportTags( $new->tags() ) ) );
    }

    public static function attach( $args )
    {
        self::guard( __FUNCTION__ );
        return self::mutate( $args, true );
    }

    public static function detach( $args )
    {
        self::guard( __FUNCTION__ );
        return self::mutate( $args, false );
    }
}
