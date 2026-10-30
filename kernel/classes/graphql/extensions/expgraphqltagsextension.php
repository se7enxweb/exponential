<?php
/**
 * The schema extension of eztags (graphql.ini SchemaExtensionList[]; doc/eng-us/features/6.0/graphql.md, "Tags"),
 * available while eztags is active:
 *
 *   tag(id | remoteId | path, language): Tag       one tag (path: the keywords from the top, food/pasta)
 *   tags(parentId, keyword, limit, offset, language): [Tag!]!   the tags below a tag (0: the top), or by keyword
 *   the eztags fields of content: [Tag!]!
 *
 * A Tag has its keyword in a translation, its place in the tree (parent, children, path), its synonyms and main tag,
 * and content: the content tagged with it (or a synonym of it), a list as every list, as the user may read it. The
 * root queries answer only for a user with the policy tags/read; the tags of a content's field are part of the
 * content and need none.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class expGraphQLTagsExtension implements expGraphQLSchemaExtension
{
    /** @var expGraphQLTagsSource */
    protected $source;

    /**
     * @param expGraphQLTagsSource|null $source null: eztags
     */
    public function __construct( $source = null )
    {
        $this->source = $source instanceof expGraphQLTagsSource ? $source : new expGraphQLTagsSource();
    }

    public function isAvailable()
    {
        return $this->source->isAvailable();
    }

    public function name()
    {
        return 'eztags/1';
    }

    public function datatypes()
    {
        return array( 'eztags' );
    }

    public function fieldMapping( array $attribute, expGraphQLTypes $types )
    {
        $source = $this->source;
        return array( Type::nonNull( Type::listOf( Type::nonNull( $this->tagType( $types ) ) ) ),
                      function ( eZContentObjectAttribute $field ) use ( $source ) { return $source->tagsOfField( $field ); } );
    }

    public function locationFields( expGraphQLTypes $types )
    {
        return array();
    }

    public function queryFields( expGraphQLTypes $types )
    {
        $source = $this->source;
        $language = array( 'language' => array( 'type' => Type::string(), 'description' => 'The translation of the keywords (eng-GB); default: the main one.' ) );
        return array(
            'tag' => array(
                'type' => $this->tagType( $types ),
                'description' => 'A tag (eztags), by exactly one of: its id, its remote id, or its path of keywords from the top (food/pasta).',
                'args' => array( 'id' => array( 'type' => Type::int() ), 'remoteId' => array( 'type' => Type::string() ),
                                 'path' => array( 'type' => Type::string() ) ) + $language,
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) use ( $source ) {
                    $given = array_intersect_key( $args, array( 'id' => 1, 'remoteId' => 1, 'path' => 1 ) );
                    if ( count( $given ) !== 1 )
                        throw new expGraphQLError( expGraphQLMessage::tr( 'tag needs exactly one of id, remoteId and path.' ), expGraphQLError::BAD_USER_INPUT );
                    if ( !$source->mayRead( $ctx->user ) )
                        return null;
                    $language = isset( $args['language'] ) ? (string)$args['language'] : false;
                    if ( isset( $args['id'] ) )
                        return $source->byId( $args['id'], $language );
                    if ( isset( $args['remoteId'] ) )
                        return $source->byRemoteId( $args['remoteId'], $language );
                    return $source->byPath( $args['path'], $language );
                },
            ),
            'tags' => array(
                'type' => Type::nonNull( Type::listOf( Type::nonNull( $this->tagType( $types ) ) ) ),
                'description' => 'Tags (eztags): those below a tag (parentId; 0, the default: the top tags), or every tag with a keyword.',
                'args' => array( 'parentId' => array( 'type' => Type::int(), 'defaultValue' => 0 ), 'keyword' => array( 'type' => Type::string() ),
                                 'limit' => array( 'type' => Type::int() ), 'offset' => array( 'type' => Type::int() ) ) + $language,
                'complexity' => function ( $childrenComplexity, $args ) { return 1 + $childrenComplexity * expGraphQLConnection::complexityCount( $args ); },
                'resolve' => function ( $root, $args, expGraphQLContext $ctx ) use ( $source ) {
                    if ( !$source->mayRead( $ctx->user ) )
                        return array();
                    $language = isset( $args['language'] ) ? (string)$args['language'] : false;
                    if ( isset( $args['keyword'] ) && $args['keyword'] !== '' )
                        return $source->byKeyword( $args['keyword'], $language );
                    list( $limit, $offset ) = expGraphQLConnection::page( $args );
                    return $source->children( (int)$args['parentId'], $limit, $offset, $language );
                },
            ),
        );
    }

    /**
     * @param expGraphQLTypes $types
     * @return ObjectType
     */
    public function tagType( expGraphQLTypes $types )
    {
        $source = $this->source;
        return $types->register( 'Tag', function () use ( $types, $source ) {
            $tagOf = function ( $ctx, $id, $language ) use ( $source ) {
                return $ctx->loader( 'tag', $language, function ( array $ids ) use ( $source, $language ) { return $source->byIds( $ids, $language ); } )->load( $id );
            };
            return new ObjectType( array(
                'name' => 'Tag',
                'description' => 'A tag of eztags: a keyword in a tree of keywords, with synonyms.',
                'fields' => function () use ( $types, $source, $tagOf ) {
                    $tag = $types->register( 'Tag', function () {} );
                    $value = function ( $key ) { return function ( array $t ) use ( $key ) { return $t[$key]; }; };
                    return array(
                        'id' => array( 'type' => Type::nonNull( Type::int() ), 'resolve' => $value( 'id' ) ),
                        'keyword' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The keyword, in the translation asked for when it has one.',
                                            'resolve' => $value( 'keyword' ) ),
                        'remoteId' => array( 'type' => Type::string(), 'resolve' => $value( 'remoteId' ) ),
                        'parentId' => array( 'type' => Type::int(), 'description' => '0 for a top tag.', 'resolve' => $value( 'parentId' ) ),
                        'mainTagId' => array( 'type' => Type::int(), 'description' => 'The tag this one is a synonym of (0: none).', 'resolve' => $value( 'mainTagId' ) ),
                        'isSynonym' => array( 'type' => Type::nonNull( Type::boolean() ), 'resolve' => function ( array $t ) { return $t['mainTagId'] > 0; } ),
                        'depth' => array( 'type' => Type::int(), 'resolve' => $value( 'depth' ) ),
                        'pathString' => array( 'type' => Type::string(), 'description' => 'The tag ids from the top, /1/7/.', 'resolve' => $value( 'pathString' ) ),
                        'modified' => array( 'type' => Type::string(), 'resolve' => function ( array $t ) { return expGraphQLTypes::isoDate( $t['modified'] ); } ),
                        'languages' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( Type::string() ) ) ), 'resolve' => $value( 'languages' ) ),
                        'url' => array( 'type' => Type::string(), 'description' => 'The address of the tag\'s page (tags/view) in this siteaccess.',
                                        'resolve' => function ( array $t, $args, expGraphQLContext $ctx ) {
                                            return $t['url'] !== '' ? $ctx->siteUrl( $t['url'] ) : null;
                                        } ),
                        'parent' => array( 'type' => $tag, 'resolve' => function ( array $t, $args, expGraphQLContext $ctx ) use ( $tagOf ) {
                            return $t['parentId'] > 0 ? $tagOf( $ctx, $t['parentId'], $t['language'] ) : null;
                        } ),
                        'mainTag' => array( 'type' => $tag, 'description' => 'The tag this one is a synonym of.',
                                            'resolve' => function ( array $t, $args, expGraphQLContext $ctx ) use ( $tagOf ) {
                                                return $t['mainTagId'] > 0 ? $tagOf( $ctx, $t['mainTagId'], $t['language'] ) : null;
                                            } ),
                        'path' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( $tag ) ) ), 'description' => 'The tags from the top down to its parent.',
                                         'resolve' => function ( array $t, $args, expGraphQLContext $ctx ) use ( $source ) {
                                             $ids = array_slice( array_values( array_filter( array_map( 'intval', explode( '/', trim( $t['pathString'], '/' ) ) ) ) ), 0, -1 );
                                             return expGraphQLFieldMapper::loadList( $ctx->loader( 'tag', $t['language'], function ( array $keys ) use ( $source, $t ) {
                                                 return $source->byIds( $keys, $t['language'] );
                                             } ), $ids );
                                         } ),
                        'children' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( $tag ) ) ),
                                             'args' => array( 'limit' => array( 'type' => Type::int() ), 'offset' => array( 'type' => Type::int() ) ),
                                             'complexity' => function ( $childrenComplexity, $args ) { return 1 + $childrenComplexity * expGraphQLConnection::complexityCount( $args ); },
                                             'resolve' => function ( array $t, $args ) use ( $source ) {
                                                 list( $limit, $offset ) = expGraphQLConnection::page( $args );
                                                 return $source->children( $t['id'], $limit, $offset, $t['language'] );
                                             } ),
                        'childrenCount' => array( 'type' => Type::nonNull( Type::int() ),
                                                  'resolve' => function ( array $t ) use ( $source ) { return $source->childrenCount( $t['id'] ); } ),
                        'synonyms' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( $tag ) ) ),
                                             'resolve' => function ( array $t ) use ( $source ) { return $t['mainTagId'] > 0 ? array() : $source->synonyms( $t['id'], $t['language'] ); } ),
                        'content' => array( 'resolve' => function ( array $t, $args, expGraphQLContext $ctx ) {
                                         $root = $ctx->repository->locationById( (int)$ctx->repository->siteAccess()['rootNodeId'] );
                                         return new expGraphQLConnection( $ctx->repository, $root, $args,
                                                                          array( 'depth' => 0, 'tagIds' => array( $t['mainTagId'] > 0 ? $t['mainTagId'] : $t['id'] ) ) );
                                     } ) + $types->childrenField( function ( array $t ) { return null; },
                                                                  'The content tagged with this tag or one of its synonyms, below the root of the siteaccess, as the user may read it (depth is ignored).' ),
                    );
                },
            ) );
        } );
    }
}

?>
