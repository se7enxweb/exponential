<?php
/**
 * Where the tags schema extension (expGraphQLTagsExtension) reads tags from: eztags (eZTagsObject), as plain arrays
 * the types read. A test extends it to answer without a database.
 *
 * A tag: id, parentId, mainTagId, keyword, remoteId, depth, pathString, modified, url (the module address of the tag,
 * tags/view/<keywords>), language (the translation of the keyword), languages.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLTagsSource
{
    /**
     * @return bool eztags is active here
     */
    public function isAvailable()
    {
        return class_exists( 'eZTagsObject' ) && class_exists( 'eZTags' );
    }

    /**
     * @param eZTagsObject $tag
     * @param string|false $language
     * @return array
     */
    public function value( $tag, $language = false )
    {
        $keyword = (string)$tag->getKeyword( $language ? $language : false );
        if ( $keyword === '' )
            $keyword = (string)$tag->getKeyword();
        return array( 'id' => (int)$tag->attribute( 'id' ), 'parentId' => (int)$tag->attribute( 'parent_id' ),
                      'mainTagId' => (int)$tag->attribute( 'main_tag_id' ), 'keyword' => $keyword,
                      'remoteId' => (string)$tag->attribute( 'remote_id' ), 'depth' => (int)$tag->attribute( 'depth' ),
                      'pathString' => (string)$tag->attribute( 'path_string' ), 'modified' => (int)$tag->attribute( 'modified' ),
                      'url' => (string)$tag->getUrl(), 'language' => $language,
                      'languages' => array_values( array_map( 'strval', (array)$tag->getAvailableLanguages() ) ) );
    }

    /**
     * @param array $tags eZTagsObject[]
     * @param string|false $language
     * @return array[]
     */
    protected function values( $tags, $language )
    {
        $values = array();
        foreach ( is_array( $tags ) ? $tags : array() as $tag )
        {
            if ( $tag instanceof eZTagsObject )
                $values[] = $this->value( $tag, $language );
        }
        return $values;
    }

    /**
     * @param int $id
     * @param string|false $language
     * @return array|null
     */
    public function byId( $id, $language = false )
    {
        $tag = (int)$id > 0 ? eZTagsObject::fetch( (int)$id ) : null;
        return $tag instanceof eZTagsObject ? $this->value( $tag, $language ) : null;
    }

    /**
     * @param int[] $ids
     * @param string|false $language
     * @return array id => tag (a batch: parents, main tags)
     */
    public function byIds( array $ids, $language = false )
    {
        $ids = array_values( array_unique( array_filter( array_map( 'intval', $ids ) ) ) );
        $answer = array_fill_keys( $ids, null );
        if ( !$ids )
            return $answer;
        foreach ( $this->values( eZTagsObject::fetchList( array( 'id' => array( $ids ) ) ), $language ) as $tag )
            $answer[$tag['id']] = $tag;
        return $answer;
    }

    /**
     * @param string $remoteId
     * @param string|false $language
     * @return array|null
     */
    public function byRemoteId( $remoteId, $language = false )
    {
        $tag = eZTagsObject::fetchByRemoteID( (string)$remoteId );
        return $tag instanceof eZTagsObject ? $this->value( $tag, $language ) : null;
    }

    /**
     * @param string $path the keywords from the top, food/pasta
     * @param string|false $language
     * @return array|null
     */
    public function byPath( $path, $language = false )
    {
        $tag = eZTagsObject::fetchByUrl( trim( (string)$path, '/' ) );
        return $tag instanceof eZTagsObject ? $this->value( $tag, $language ) : null;
    }

    /**
     * @param string $keyword
     * @param string|false $language
     * @return array[] every tag with this keyword
     */
    public function byKeyword( $keyword, $language = false )
    {
        return $this->values( eZTagsObject::fetchByKeyword( (string)$keyword ), $language );
    }

    /**
     * @param int $parentId 0: the top tags
     * @param int $limit
     * @param int $offset
     * @param string|false $language
     * @return array[]
     */
    public function children( $parentId, $limit, $offset, $language = false )
    {
        return $this->values( eZTagsObject::fetchByParentID( (int)$parentId, (int)$offset, (int)$limit ), $language );
    }

    /**
     * @param int $parentId
     * @return int
     */
    public function childrenCount( $parentId )
    {
        return (int)eZTagsObject::childrenCountByParentID( (int)$parentId );
    }

    /**
     * @param int $mainTagId
     * @param string|false $language
     * @return array[]
     */
    public function synonyms( $mainTagId, $language = false )
    {
        return $this->values( eZTagsObject::fetchSynonyms( (int)$mainTagId ), $language );
    }

    /**
     * @param eZContentObjectAttribute $field an eztags field
     * @return array[] its tags, in the translation of the field
     */
    public function tagsOfField( eZContentObjectAttribute $field )
    {
        $tags = $field->hasContent() ? $field->content() : null;
        return is_object( $tags ) ? $this->values( $tags->tags(), $field->attribute( 'language_code' ) ) : array();
    }

    /**
     * @param eZUser|null $user
     * @return bool the user may read tags (the policy tags/read)
     */
    public function mayRead( $user )
    {
        if ( !$user instanceof eZUser )
            return false;
        $access = $user->hasAccessTo( 'tags', 'read' );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }
}

?>
