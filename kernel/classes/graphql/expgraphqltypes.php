<?php
/**
 * The types of the GraphQL schema that do not come from a content class: the interface Content, Location, the list
 * connection (ContentConnection, ContentEdge, PageInfo), the enums of sorting, the values of fields (Image,
 * ImageVariation, RichText, BinaryFile, Price, Link, SelectionOption, Country, Author, Matrix) and the types of the
 * root queries (User, SiteAccess, Language). One instance per schema: a type object must not be shared between two
 * schemas. Loaded only after webonyx/graphql-php.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class expGraphQLTypes
{
    /** @var array name => type */
    protected $types = array();

    /** @var callable class identifier => ObjectType|null (the schema builder's class types) */
    protected $classTypeResolver;

    /** @var expGraphQLSchemaExtension[] */
    protected $extensions;

    /**
     * @param callable $classTypeResolver function( string $classIdentifier ): ObjectType|null
     * @param expGraphQLSchemaExtension[] $extensions the schema extensions (their fields of Location)
     */
    public function __construct( callable $classTypeResolver, array $extensions = array() )
    {
        $this->classTypeResolver = $classTypeResolver;
        $this->extensions = $extensions;
    }

    /**
     * @return array the fields the schema extensions add to Location
     */
    public function extensionLocationFields()
    {
        $fields = array();
        foreach ( $this->extensions as $extension )
            $fields += (array)$extension->locationFields( $this );
        return $fields;
    }

    /**
     * @param string $name
     * @param callable $factory
     * @return Type
     */
    protected function once( $name, callable $factory )
    {
        if ( !isset( $this->types[$name] ) )
            $this->types[$name] = $factory();
        return $this->types[$name];
    }

    /**
     * The fields of every content: the interface Content, and the start of each class type.
     *
     * @return array
     */
    public function contentFields()
    {
        $types = $this;
        return array(
            'id' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The content object id.',
                           'resolve' => function ( expGraphQLContent $c ) { return $c->id(); } ),
            'remoteId' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The remote id of the content object.',
                                 'resolve' => function ( expGraphQLContent $c ) { return $c->remoteId(); } ),
            'name' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The name, in the translation read.',
                             'resolve' => function ( expGraphQLContent $c ) { return $c->name(); } ),
            'classIdentifier' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The identifier of the content class.',
                                        'resolve' => function ( expGraphQLContent $c ) { return $c->classIdentifier(); } ),
            'className' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The name of the content class.',
                                  'resolve' => function ( expGraphQLContent $c ) { return $c->className(); } ),
            'published' => array( 'type' => Type::string(), 'description' => 'When it was first published, ISO 8601.',
                                  'resolve' => function ( expGraphQLContent $c ) { return expGraphQLTypes::isoDate( $c->published() ); } ),
            'modified' => array( 'type' => Type::string(), 'description' => 'When it was last modified, ISO 8601.',
                                 'resolve' => function ( expGraphQLContent $c ) { return expGraphQLTypes::isoDate( $c->modified() ); } ),
            'language' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The translation the fields are read in (eng-GB).',
                                 'resolve' => function ( expGraphQLContent $c ) { return $c->language(); } ),
            'languages' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( Type::string() ) ) ), 'description' => 'Its translations.',
                                  'resolve' => function ( expGraphQLContent $c ) { return $c->languages(); } ),
            'mainLanguage' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The language it was first written in.',
                                     'resolve' => function ( expGraphQLContent $c ) { return $c->mainLanguage(); } ),
            'ownerId' => array( 'type' => Type::int(), 'description' => 'The content object id of the owner.',
                                'resolve' => function ( expGraphQLContent $c ) { return $c->ownerId(); } ),
            'ownerName' => array( 'type' => Type::string(), 'description' => 'The name of the owner.',
                                  'resolve' => function ( expGraphQLContent $c, $args, expGraphQLContext $ctx ) {
                                      // the owners of a whole page in one query
                                      return $c->ownerId() > 0 ? $ctx->loader( 'names' )->load( $c->ownerId() ) : null;
                                  } ),
            'sectionId' => array( 'type' => Type::int(), 'description' => 'The id of its section.',
                                  'resolve' => function ( expGraphQLContent $c ) { return $c->sectionId(); } ),
            'url' => array( 'type' => Type::string(), 'description' => 'The address of the location it was reached by, in this siteaccess.',
                            'resolve' => function ( expGraphQLContent $c, $args, expGraphQLContext $ctx ) {
                                $c->primeUrl();
                                $location = $c->location();
                                return $location ? $ctx->siteUrl( $location->urlAlias() ) : null;
                            } ),
            'urlAlias' => array( 'type' => Type::string(), 'description' => 'The URL alias of the location it was reached by (about/company).',
                                 'resolve' => function ( expGraphQLContent $c ) {
                                     $c->primeUrl();
                                     $location = $c->location();
                                     return $location ? $location->urlAlias() : null;
                                 } ),
            'location' => array( 'type' => function () use ( $types ) { return $types->location(); },
                                 'description' => 'The location it was reached by: the node a query or a list named, else the main location.',
                                 'resolve' => function ( expGraphQLContent $c ) { return $c->location(); } ),
            'mainLocation' => array( 'type' => function () use ( $types ) { return $types->location(); }, 'description' => 'The main location.',
                                     'resolve' => function ( expGraphQLContent $c ) { return $c->mainLocation(); } ),
            'locations' => array( 'type' => function () use ( $types ) { return Type::nonNull( Type::listOf( Type::nonNull( $types->location() ) ) ); },
                                  'description' => 'Every location the user may read.',
                                  'resolve' => function ( expGraphQLContent $c ) { return $c->locations(); } ),
            'parent' => array( 'type' => function () use ( $types ) { return $types->content(); },
                               'description' => 'The content of the parent of the location it was reached by.',
                               'resolve' => function ( expGraphQLContent $c, $args, expGraphQLContext $ctx ) {
                                   $location = $c->location();
                                   return $location ? expGraphQLTypes::parentOf( $location, $ctx ) : null;
                               } ),
            'children' => $this->childrenField( function ( expGraphQLContent $c ) { return $c->location(); },
                                                'The content below the location it was reached by.' ),
        );
    }

    /**
     * A list field of content below a location: a connection with offset/limit and cursors.
     *
     * @param callable $locationOf function( $value ): expGraphQLLocation|null
     * @param string $description
     * @return array
     */
    public function childrenField( callable $locationOf, $description )
    {
        $types = $this;
        return array(
            'type' => function () use ( $types ) { return Type::nonNull( $types->connection() ); },
            'description' => $description,
            'args' => $this->listArguments(),
            'complexity' => function ( $childrenComplexity, $args ) { return 1 + $childrenComplexity * expGraphQLConnection::complexityCount( $args ); },
            'resolve' => function ( $value, $args, expGraphQLContext $ctx ) use ( $locationOf ) {
                $location = $locationOf( $value );
                return new expGraphQLConnection( $ctx->repository, $location, $args );
            },
        );
    }

    /**
     * @return array the arguments of a list of content
     */
    public function listArguments()
    {
        return array(
            'classes' => array( 'type' => Type::listOf( Type::nonNull( Type::string() ) ), 'description' => 'Only content of these class identifiers.' ),
            'sortField' => array( 'type' => $this->sortField(), 'description' => 'Sort by this field (default: the sorting of the parent location).' ),
            'sortOrder' => array( 'type' => $this->sortOrder(), 'description' => 'ASC (the default) or DESC, with sortField.' ),
            'depth' => array( 'type' => Type::int(), 'defaultValue' => 1, 'description' => 'How deep below the location (1: the children).' ),
            'limit' => array( 'type' => Type::int(), 'description' => 'At most this many (graphql.ini DefaultListLimit, at most MaxListLimit).' ),
            'offset' => array( 'type' => Type::int(), 'description' => 'Skip this many.' ),
            'first' => array( 'type' => Type::int(), 'description' => 'Cursor pagination: at most this many (instead of limit).' ),
            'after' => array( 'type' => Type::string(), 'description' => 'Cursor pagination: the items after this cursor (instead of offset).' ),
        );
    }

    /** @return InterfaceType */
    public function content()
    {
        $types = $this;
        $resolver = $this->classTypeResolver;
        return $this->once( 'Content', function () use ( $types, $resolver ) {
            return new InterfaceType( array(
                'name' => 'Content',
                'description' => 'A content object, read in one translation, reached by one of its locations. Every content class is a type implementing it.',
                'fields' => function () use ( $types ) { return $types->contentFields(); },
                'resolveType' => function ( $value ) use ( $resolver ) {
                    return $value instanceof expGraphQLContent ? $resolver( $value->classIdentifier() ) : null;
                },
            ) );
        } );
    }

    /** @return ObjectType */
    public function location()
    {
        $types = $this;
        return $this->once( 'Location', function () use ( $types ) {
            return new ObjectType( array(
                'name' => 'Location',
                'description' => 'A location of the content tree (a node).',
                'fields' => function () use ( $types ) {
                    return array(
                        'id' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The node id.',
                                       'resolve' => function ( expGraphQLLocation $l ) { return $l->id(); } ),
                        'remoteId' => array( 'type' => Type::string(), 'description' => 'The remote id of the location.',
                                             'resolve' => function ( expGraphQLLocation $l ) { return $l->remoteId(); } ),
                        'parentId' => array( 'type' => Type::int(), 'description' => 'The node id of the parent.',
                                             'resolve' => function ( expGraphQLLocation $l ) { return $l->parentId(); } ),
                        'depth' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The depth in the tree (the root is 1).',
                                          'resolve' => function ( expGraphQLLocation $l ) { return $l->depth(); } ),
                        'pathString' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The node ids from the root, /1/2/58/.',
                                               'resolve' => function ( expGraphQLLocation $l ) { return $l->pathString(); } ),
                        'priority' => array( 'type' => Type::int(), 'description' => 'The priority among its siblings.',
                                             'resolve' => function ( expGraphQLLocation $l ) { return $l->priority(); } ),
                        'hidden' => array( 'type' => Type::nonNull( Type::boolean() ), 'description' => 'Hidden itself.',
                                           'resolve' => function ( expGraphQLLocation $l ) { return $l->hidden(); } ),
                        'invisible' => array( 'type' => Type::nonNull( Type::boolean() ), 'description' => 'Hidden, or below a hidden location.',
                                              'resolve' => function ( expGraphQLLocation $l ) { return $l->invisible(); } ),
                        'urlAlias' => array( 'type' => Type::string(), 'description' => 'The URL alias (about/company).',
                                             'resolve' => function ( expGraphQLLocation $l ) { return $l->urlAlias(); } ),
                        'url' => array( 'type' => Type::string(), 'description' => 'The address in this siteaccess.',
                                        'resolve' => function ( expGraphQLLocation $l, $args, expGraphQLContext $ctx ) { return $ctx->siteUrl( $l->urlAlias() ); } ),
                        'content' => array( 'type' => function () use ( $types ) { return $types->content(); }, 'description' => 'The content at the location.',
                                            'resolve' => function ( expGraphQLLocation $l, $args, expGraphQLContext $ctx ) { return $ctx->repository->contentAtLocation( $l ); } ),
                        'parent' => array( 'type' => function () use ( $types ) { return $types->content(); }, 'description' => 'The content of the parent location.',
                                           'resolve' => function ( expGraphQLLocation $l, $args, expGraphQLContext $ctx ) { return expGraphQLTypes::parentOf( $l, $ctx ); } ),
                        'children' => $types->childrenField( function ( expGraphQLLocation $l ) { return $l; }, 'The content below the location.' ),
                    ) + $types->extensionLocationFields();
                },
            ) );
        } );
    }

    /** @return ObjectType */
    public function connection()
    {
        $types = $this;
        return $this->once( 'ContentConnection', function () use ( $types ) {
            return new ObjectType( array(
                'name' => 'ContentConnection',
                'description' => 'A list of content: the items (nodes, or edges with cursors), how many there are and where the page is.',
                'fields' => function () use ( $types ) {
                    return array(
                        'totalCount' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'How many there are in all (one count query).',
                                               'resolve' => function ( expGraphQLConnection $c ) { return $c->totalCount(); } ),
                        'nodes' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( $types->content() ) ) ), 'description' => 'The items of the page.',
                                          'resolve' => function ( expGraphQLConnection $c ) { return $c->items(); } ),
                        'edges' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( $types->edge() ) ) ), 'description' => 'The items of the page with their cursors.',
                                          'resolve' => function ( expGraphQLConnection $c ) { return $c->edges(); } ),
                        'pageInfo' => array( 'type' => Type::nonNull( $types->pageInfo() ), 'description' => 'Where the page is.',
                                             'resolve' => function ( expGraphQLConnection $c ) { return $c->pageInfo(); } ),
                    );
                },
            ) );
        } );
    }

    /** @return ObjectType */
    public function edge()
    {
        $types = $this;
        return $this->once( 'ContentEdge', function () use ( $types ) {
            return new ObjectType( array(
                'name' => 'ContentEdge',
                'description' => 'An item of a list with its cursor.',
                'fields' => function () use ( $types ) {
                    return array(
                        'cursor' => array( 'type' => Type::nonNull( Type::string() ), 'resolve' => function ( array $e ) { return $e['cursor']; } ),
                        'node' => array( 'type' => Type::nonNull( $types->content() ), 'resolve' => function ( array $e ) { return $e['node']; } ),
                    );
                },
            ) );
        } );
    }

    /** @return ObjectType */
    public function pageInfo()
    {
        return $this->once( 'PageInfo', function () {
            return new ObjectType( array(
                'name' => 'PageInfo',
                'description' => 'Where a page of a list is.',
                'fields' => array(
                    'hasNextPage' => array( 'type' => Type::nonNull( Type::boolean() ) ),
                    'hasPreviousPage' => array( 'type' => Type::nonNull( Type::boolean() ) ),
                    'startCursor' => array( 'type' => Type::string() ),
                    'endCursor' => array( 'type' => Type::string() ),
                ),
            ) );
        } );
    }

    /** @return EnumType */
    public function sortField()
    {
        return $this->once( 'ContentSortField', function () {
            $values = array();
            foreach ( array_keys( expGraphQLRepository::SORT_FIELDS ) as $name )
                $values[$name] = array( 'value' => $name );
            return new EnumType( array( 'name' => 'ContentSortField', 'description' => 'What a list is sorted by.', 'values' => $values ) );
        } );
    }

    /** @return EnumType */
    public function sortOrder()
    {
        return $this->once( 'SortOrder', function () {
            return new EnumType( array( 'name' => 'SortOrder', 'values' => array( 'ASC' => array( 'value' => 'ASC' ), 'DESC' => array( 'value' => 'DESC' ) ) ) );
        } );
    }

    /** @return ObjectType */
    public function image()
    {
        $types = $this;
        return $this->once( 'Image', function () use ( $types ) {
            $aliasArg = array( 'alias' => array( 'type' => Type::string(), 'defaultValue' => 'original', 'description' => 'An image alias of image.ini.' ) );
            return new ObjectType( array(
                'name' => 'Image',
                'description' => 'An image field. Every size is an image alias of image.ini (original, small, large ...), made when first asked for.',
                'fields' => function () use ( $types, $aliasArg ) {
                    return array(
                        'url' => array( 'type' => Type::string(), 'args' => $aliasArg, 'description' => 'The address of the image in this alias.',
                                        'resolve' => function ( expGraphQLImage $i, $args ) { return $i->value( $args['alias'], 'url' ); } ),
                        'width' => array( 'type' => Type::int(), 'args' => $aliasArg,
                                          'resolve' => function ( expGraphQLImage $i, $args ) { return $i->value( $args['alias'], 'width' ); } ),
                        'height' => array( 'type' => Type::int(), 'args' => $aliasArg,
                                           'resolve' => function ( expGraphQLImage $i, $args ) { return $i->value( $args['alias'], 'height' ); } ),
                        'mimeType' => array( 'type' => Type::string(), 'args' => $aliasArg,
                                             'resolve' => function ( expGraphQLImage $i, $args ) { return $i->value( $args['alias'], 'mimeType' ); } ),
                        'alt' => array( 'type' => Type::string(), 'description' => 'The alternative text.',
                                        'resolve' => function ( expGraphQLImage $i ) { return $i->alternativeText(); } ),
                        'variations' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( $types->imageVariation() ) ) ),
                                               'args' => array( 'aliases' => array( 'type' => Type::listOf( Type::nonNull( Type::string() ) ),
                                                                                    'description' => 'The aliases (default: graphql.ini ImageAliasList[]).' ) ),
                                               'description' => 'The image in several aliases.',
                                               'resolve' => function ( expGraphQLImage $i, $args ) {
                                                   $aliases = isset( $args['aliases'] ) ? $args['aliases'] : expGraphQL::listSetting( 'ImageAliasList' );
                                                   return $i->variations( $aliases );
                                               } ),
                    );
                },
            ) );
        } );
    }

    /** @return ObjectType */
    public function imageVariation()
    {
        return $this->once( 'ImageVariation', function () {
            $field = function ( $type, $key ) {
                return array( 'type' => $type, 'resolve' => function ( array $v ) use ( $key ) { return $v[$key]; } );
            };
            return new ObjectType( array(
                'name' => 'ImageVariation',
                'description' => 'An image in one alias.',
                'fields' => array( 'alias' => $field( Type::nonNull( Type::string() ), 'alias' ), 'url' => $field( Type::string(), 'url' ),
                                   'width' => $field( Type::int(), 'width' ), 'height' => $field( Type::int(), 'height' ),
                                   'mimeType' => $field( Type::string(), 'mimeType' ) ),
            ) );
        } );
    }

    /** @return ObjectType */
    public function richText()
    {
        return $this->once( 'RichText', function () {
            return new ObjectType( array(
                'name' => 'RichText',
                'description' => 'A rich text field (XML block) in several formats.',
                'fields' => array(
                    'html' => array( 'type' => Type::string(), 'description' => 'Rendered HTML, as the site shows it.',
                                     'resolve' => function ( expGraphQLRichText $r ) { return $r->html(); } ),
                    'text' => array( 'type' => Type::string(), 'description' => 'Plain text.',
                                     'resolve' => function ( expGraphQLRichText $r ) { return $r->text(); } ),
                    'xml' => array( 'type' => Type::string(), 'description' => 'The stored XML.',
                                    'resolve' => function ( expGraphQLRichText $r ) { return $r->xml(); } ),
                    'excerpt' => array( 'type' => Type::string(), 'description' => 'The start of the plain text, cut at a word.',
                                        'args' => array( 'length' => array( 'type' => Type::int(), 'defaultValue' => 200 ) ),
                                        'resolve' => function ( expGraphQLRichText $r, $args ) { return $r->excerpt( $args['length'] ); } ),
                ),
            ) );
        } );
    }

    /**
     * An object type of plain values read from an array.
     *
     * @param string $name
     * @param string $description
     * @param array $fields field => array( type, key )
     * @return ObjectType
     */
    protected function arrayType( $name, $description, array $fields )
    {
        return $this->once( $name, function () use ( $name, $description, $fields ) {
            $config = array();
            foreach ( $fields as $field => $spec )
            {
                $key = $spec[1];
                // a value may be a closure, read only when the field is asked for
                $config[$field] = array( 'type' => $spec[0], 'resolve' => function ( array $v ) use ( $key ) {
                    $value = isset( $v[$key] ) ? $v[$key] : null;
                    return $value instanceof Closure ? $value() : $value;
                } );
            }
            return new ObjectType( array( 'name' => $name, 'description' => $description, 'fields' => $config ) );
        } );
    }

    /** @return ObjectType */
    public function binaryFile()
    {
        return $this->arrayType( 'BinaryFile', 'A file field.', array(
            'fileName' => array( Type::string(), 'fileName' ), 'mimeType' => array( Type::string(), 'mimeType' ),
            'size' => array( Type::int(), 'size' ), 'url' => array( Type::string(), 'url' ) ) );
    }

    /** @return ObjectType */
    public function price()
    {
        return $this->arrayType( 'Price', 'A price field.', array(
            'price' => array( Type::float(), 'price' ), 'incVat' => array( Type::float(), 'incVat' ),
            'exVat' => array( Type::float(), 'exVat' ), 'vatPercent' => array( Type::float(), 'vatPercent' ) ) );
    }

    /** @return ObjectType */
    public function link()
    {
        return $this->arrayType( 'Link', 'A URL field.', array( 'url' => array( Type::string(), 'url' ), 'text' => array( Type::string(), 'text' ) ) );
    }

    /** @return ObjectType */
    public function selectionOption()
    {
        return $this->arrayType( 'SelectionOption', 'A chosen option of a selection field.',
                                 array( 'id' => array( Type::nonNull( Type::int() ), 'id' ), 'name' => array( Type::string(), 'name' ) ) );
    }

    /** @return ObjectType */
    public function country()
    {
        return $this->arrayType( 'Country', 'A country.', array( 'code' => array( Type::nonNull( Type::string() ), 'code' ), 'name' => array( Type::string(), 'name' ) ) );
    }

    /** @return ObjectType */
    public function author()
    {
        return $this->arrayType( 'Author', 'An author of an authors field.', array( 'name' => array( Type::string(), 'name' ) ) );
    }

    /** @return ObjectType */
    public function matrix()
    {
        return $this->arrayType( 'Matrix', 'A matrix field: its column names and its rows.', array(
            'columns' => array( Type::nonNull( Type::listOf( Type::nonNull( Type::string() ) ) ), 'columns' ),
            'rows' => array( Type::nonNull( Type::listOf( Type::nonNull( Type::listOf( Type::string() ) ) ) ), 'rows' ) ) );
    }

    /** @return ObjectType */
    public function geoLocation()
    {
        return $this->arrayType( 'GeoLocation', 'A map location field (ezgmaplocation).', array(
            'latitude' => array( Type::float(), 'latitude' ), 'longitude' => array( Type::float(), 'longitude' ),
            'address' => array( Type::string(), 'address' ) ) );
    }

    /** @return ObjectType */
    public function rating()
    {
        return $this->arrayType( 'Rating', 'A star rating field (ezsrrating): the average of the votes and how many there are.', array(
            'average' => array( Type::float(), 'average' ), 'count' => array( Type::nonNull( Type::int() ), 'count' ) ) );
    }

    /** @return ObjectType */
    public function metadata()
    {
        return $this->arrayType( 'Metadata', 'A meta data field (xrowmetadata): what search engines and sitemaps read.', array(
            'title' => array( Type::string(), 'title' ), 'description' => array( Type::string(), 'description' ),
            'keywords' => array( Type::nonNull( Type::listOf( Type::nonNull( Type::string() ) ) ), 'keywords' ),
            'priority' => array( Type::string(), 'priority' ), 'change' => array( Type::string(), 'change' ),
            'sitemapUse' => array( Type::boolean(), 'sitemapUse' ), 'canonicalUrl' => array( Type::string(), 'canonicalUrl' ) ) );
    }

    /** @return ObjectType */
    public function enhancedLink()
    {
        $types = $this;
        return $this->once( 'EnhancedLink', function () use ( $types ) {
            return new ObjectType( array(
                'name' => 'EnhancedLink',
                'description' => 'An enhanced link field (ngenhancedlink): a link to content of the site or to an address outside it.',
                'fields' => function () use ( $types ) {
                    $value = function ( $key ) { return function ( array $v ) use ( $key ) { return isset( $v[$key] ) ? $v[$key] : null; }; };
                    return array(
                        'type' => array( 'type' => Type::string(), 'description' => 'internal or external.', 'resolve' => $value( 'type' ) ),
                        'url' => array( 'type' => Type::string(), 'description' => 'The address: of the content in this siteaccess, or the external one.',
                                        'resolve' => function ( array $v, $args, expGraphQLContext $ctx ) {
                                            if ( $v['type'] === 'external' )
                                                return $v['reference'] !== '' ? (string)$v['reference'] : null;
                                            $id = (int)$v['reference'];
                                            if ( $id <= 0 )
                                                return null;
                                            return new \GraphQL\Deferred( function () use ( $ctx, $id, $v ) {
                                                $content = $ctx->loader( 'content' )->value( $id );
                                                $location = $content ? $content->location() : null;
                                                return $location ? $ctx->siteUrl( $location->urlAlias() ) . (string)$v['suffix'] : null;
                                            } );
                                        } ),
                        'label' => array( 'type' => Type::string(), 'resolve' => $value( 'label' ) ),
                        'target' => array( 'type' => Type::string(), 'description' => 'How the link opens (link, link_new_tab, modal, embed).', 'resolve' => $value( 'target' ) ),
                        'suffix' => array( 'type' => Type::string(), 'description' => 'What is added to the address of internal content (#part, ?query).', 'resolve' => $value( 'suffix' ) ),
                        'rel' => array( 'type' => Type::string(), 'resolve' => $value( 'rel' ) ),
                        'content' => array( 'type' => function () use ( $types ) { return $types->content(); },
                                            'description' => 'The linked content (internal links), when the user may read it.',
                                            'resolve' => function ( array $v, $args, expGraphQLContext $ctx ) {
                                                return $v['type'] !== 'external' && (int)$v['reference'] > 0 ? $ctx->loader( 'content' )->load( (int)$v['reference'] ) : null;
                                            } ),
                    );
                },
            ) );
        } );
    }

    /** @return ObjectType */
    public function option()
    {
        return $this->arrayType( 'Option', 'A choice of an option field (ezoption).', array(
            'id' => array( Type::int(), 'id' ), 'value' => array( Type::string(), 'value' ), 'additionalPrice' => array( Type::float(), 'additionalPrice' ) ) );
    }

    /** @return ObjectType */
    public function optionList()
    {
        $types = $this;
        return $this->arrayType( 'OptionList', 'An option field (ezoption): its name and its choices.', array(
            'name' => array( Type::string(), 'name' ),
            'options' => array( Type::nonNull( Type::listOf( Type::nonNull( $types->option() ) ) ), 'options' ) ) );
    }

    /** @return ObjectType */
    public function optionGroup()
    {
        $types = $this;
        return $this->arrayType( 'OptionGroup', 'A group of a multi-option field (ezmultioption).', array(
            'name' => array( Type::string(), 'name' ),
            'options' => array( Type::nonNull( Type::listOf( Type::nonNull( $types->option() ) ) ), 'options' ) ) );
    }

    /** @return ObjectType */
    public function range()
    {
        return $this->arrayType( 'Range', 'A range option field (ezrangeoption).', array(
            'name' => array( Type::string(), 'name' ), 'start' => array( Type::float(), 'start' ), 'stop' => array( Type::float(), 'stop' ),
            'step' => array( Type::float(), 'step' ) ) );
    }

    /** @return ObjectType */
    public function currencyPrice()
    {
        return $this->arrayType( 'CurrencyPrice', 'A price of a multi-price field (ezmultiprice) in one currency.', array(
            'currency' => array( Type::nonNull( Type::string() ), 'currency' ), 'value' => array( Type::float(), 'value' ),
            'type' => array( Type::int(), 'type' ) ) );
    }

    /** @return ObjectType */
    public function enumValue()
    {
        return $this->arrayType( 'EnumValue', 'A chosen value of an enumeration field (ezenum).', array(
            'element' => array( Type::string(), 'element' ), 'value' => array( Type::string(), 'value' ) ) );
    }

    /**
     * A type made once per schema by a schema extension (expGraphQLSchemaExtension).
     *
     * @param string $name
     * @param callable $factory function(): Type
     * @return Type
     */
    public function register( $name, callable $factory )
    {
        return $this->once( $name, $factory );
    }

    /** @return ObjectType */
    public function user()
    {
        return $this->arrayType( 'User', 'The user a request runs as.', array(
            'id' => array( Type::nonNull( Type::int() ), 'id' ), 'login' => array( Type::string(), 'login' ), 'name' => array( Type::string(), 'name' ),
            'anonymous' => array( Type::nonNull( Type::boolean() ), 'anonymous' ),
            'signedInBy' => array( Type::nonNull( Type::string() ), 'signedInBy' ) ) );
    }

    /** @return ObjectType */
    public function siteAccess()
    {
        return $this->arrayType( 'SiteAccess', 'The siteaccess that answers.', array(
            'name' => array( Type::nonNull( Type::string() ), 'name' ),
            'languages' => array( Type::nonNull( Type::listOf( Type::nonNull( Type::string() ) ) ), 'languages' ),
            'rootNodeId' => array( Type::int(), 'rootNodeId' ),
            'schemaHash' => array( Type::nonNull( Type::string() ), 'schemaHash' ) ) );
    }

    /** @return ObjectType */
    public function language()
    {
        return $this->arrayType( 'Language', 'A content language of the installation.',
                                 array( 'code' => array( Type::nonNull( Type::string() ), 'code' ), 'name' => array( Type::string(), 'name' ) ) );
    }

    /**
     * The content of the parent of a location, through the batch loader: the parents of a page in one query.
     *
     * @param expGraphQLLocation $location
     * @param expGraphQLContext $ctx
     * @return \GraphQL\Deferred|null
     */
    public static function parentOf( expGraphQLLocation $location, expGraphQLContext $ctx )
    {
        if ( $location->depth() <= 1 )
            return null;
        return $ctx->loader( 'node', $location->language() )->load( $location->parentId() );
    }

    /**
     * @param int $time a Unix time
     * @param bool $dateOnly
     * @return string|null ISO 8601, null for 0
     */
    public static function isoDate( $time, $dateOnly = false )
    {
        $time = (int)$time;
        if ( $time <= 0 )
            return null;
        return $dateOnly ? date( 'Y-m-d', $time ) : date( 'c', $time );
    }

    /**
     * @return Type[] every type built so far, by name
     */
    public function built()
    {
        return $this->types;
    }
}

?>
