<?php
/**
 * Builds the GraphQL schema of a siteaccess from its content classes (expGraphQLClassCatalogue): a type per class
 * implementing the interface Content, a field per attribute (expGraphQLFieldMapper), and the root queries:
 *
 *   node(id | remoteId | path | urlAlias, language)   the content at a location
 *   content(id | remoteId, version, language)         a content object at its main location (or a version of it)
 *   preview(token)                                    the version a preview token names
 *   children(parentId, classes, sortField, sortOrder, depth, limit, offset, first, after)
 *   currentUser, siteaccess, translations
 *
 * and the mutations of expGraphQLMutations, unless graphql.ini MutationsEnabled (enabled by default) switches them off.
 *
 * The schema hash (schemaHash()) changes when a class, an attribute, the naming or the mutations change; it is sent with every
 * answer (header X-GraphQL-Schema, siteaccess { schemaHash }). Loaded only after webonyx/graphql-php.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;

class expGraphQLSchemaBuilder
{
    /** The kernel's own schema: raised when its types or the mapping of a datatype change, so the hash does. */
    const VERSION = 3;

    /** @var expGraphQLClassCatalogue */
    protected $catalogue;

    /** @var expGraphQLNaming */
    protected $naming;

    /** @var expGraphQLTypes */
    protected $types;

    /** @var expGraphQLFieldMapper */
    protected $mapper;

    /** @var ObjectType[] class identifier => type */
    protected $classTypes = array();

    /** @var array type name => class identifier */
    protected $typeNames = array();

    /** @var Schema|null */
    protected $schema = null;

    /** @var expGraphQLSchemaExtension[] */
    protected $extensions = array();

    /**
     * @param expGraphQLClassCatalogue $catalogue
     * @param expGraphQLNaming $naming
     * @param expGraphQLSchemaExtension[] $extensions the schema extensions (graphql.ini SchemaExtensionList[])
     */
    public function __construct( expGraphQLClassCatalogue $catalogue, expGraphQLNaming $naming, array $extensions = array() )
    {
        $this->catalogue = $catalogue;
        $this->naming = $naming;
        $this->extensions = $extensions;
        $builder = $this;
        $this->types = new expGraphQLTypes( function ( $classIdentifier ) use ( $builder ) { return $builder->classType( $classIdentifier ); }, $extensions );
        $this->mapper = new expGraphQLFieldMapper( $this->types, $extensions );
        // the type names first: two classes whose names come out the same get _2, _3 ... in the order of the catalogue
        foreach ( $catalogue->identifiers() as $identifier )
        {
            $name = $naming->typeName( $identifier );
            $unique = $name;
            for ( $n = 2; isset( $this->typeNames[$unique] ); $n++ )
                $unique = $name . '_' . $n;
            $this->typeNames[$unique] = $identifier;
        }
    }

    /**
     * The schema of the current siteaccess: its classes from the database, the naming of graphql.ini.
     *
     * @return self
     */
    public static function fromSettings()
    {
        return new self( expGraphQLClassCatalogue::cached(), expGraphQLNaming::fromSettings(), expGraphQLSchemaExtensions::fromSettings() );
    }

    /** @return expGraphQLClassCatalogue */
    public function catalogue()
    {
        return $this->catalogue;
    }

    /**
     * @return string 12 hexadecimal digits: changes when a class, an attribute or the naming changes
     */
    public function schemaHash()
    {
        $hashed = self::VERSION . '|' . $this->catalogue->hash() . '|' . $this->naming->style() . '|' . json_encode( $this->typeNames )
                  . ( expGraphQLMutations::enabled() ? '|mutations' : '' );
        if ( $this->extensions )
            $hashed .= '|' . implode( ',', expGraphQLSchemaExtensions::names( $this->extensions ) );
        return substr( sha1( $hashed ), 0, 12 );
    }

    /**
     * @return expGraphQLSchemaExtension[] the schema extensions of this schema
     */
    public function extensions()
    {
        return $this->extensions;
    }

    /**
     * @return array type name => class identifier
     */
    public function typeNames()
    {
        return $this->typeNames;
    }

    /**
     * @param string $classIdentifier
     * @return ObjectType|null the type of the class (null: not in the schema)
     */
    public function classType( $classIdentifier )
    {
        if ( isset( $this->classTypes[$classIdentifier] ) )
            return $this->classTypes[$classIdentifier];
        $class = $this->catalogue->classByIdentifier( $classIdentifier );
        $name = array_search( $classIdentifier, $this->typeNames, true );
        if ( $class === null || $name === false )
            return null;
        $types = $this->types;
        $mapper = $this->mapper;
        $naming = $this->naming;
        $this->classTypes[$classIdentifier] = new ObjectType( array(
            'name' => $name,
            'description' => trim( $class['name'] . ( $class['description'] !== '' ? ': ' . $class['description'] : '' ) . ' (class ' . $classIdentifier . ')' ),
            'interfaces' => function () use ( $types ) { return array( $types->content() ); },
            'fields' => function () use ( $types, $mapper, $naming, $class ) {
                $fields = $types->contentFields();
                foreach ( $class['attributes'] as $attribute )
                {
                    $fieldName = $naming->fieldName( $class['identifier'], $attribute['identifier'] );
                    $unique = $fieldName;
                    for ( $n = 2; isset( $fields[$unique] ); $n++ )
                        $unique = $fieldName . '_' . $n;
                    $fields[$unique] = $mapper->field( $attribute );
                }
                return $fields;
            },
        ) );
        return $this->classTypes[$classIdentifier];
    }

    /**
     * @return Schema
     */
    public function schema()
    {
        if ( $this->schema !== null )
            return $this->schema;
        $builder = $this;
        $types = $this->types;
        $byArgs = array( 'language' => array( 'type' => Type::string(), 'description' => 'The translation to read (eng-GB); default: the languages of the siteaccess.' ) );
        $query = new ObjectType( array(
            'name' => 'Query',
            'fields' => function () use ( $builder, $types, $byArgs ) {
                return array(
                    'node' => array(
                        'type' => $types->content(),
                        'description' => 'The content at a location, by one of: the node id, the remote id of the location, the path string (/1/2/58/) or the URL alias (about/company).',
                        'args' => array( 'id' => array( 'type' => Type::int() ), 'remoteId' => array( 'type' => Type::string() ),
                                         'path' => array( 'type' => Type::string() ), 'urlAlias' => array( 'type' => Type::string() ) ) + $byArgs,
                        'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                            $language = isset( $args['language'] ) ? (string)$args['language'] : false;
                            $given = array_intersect_key( $args, array( 'id' => 1, 'remoteId' => 1, 'path' => 1, 'urlAlias' => 1 ) );
                            if ( count( $given ) !== 1 )
                                throw new expGraphQLError( expGraphQLMessage::tr( 'node needs exactly one of id, remoteId, path and urlAlias.' ), expGraphQLError::BAD_USER_INPUT );
                            if ( isset( $args['id'] ) )
                                return $ctx->repository->contentByNodeId( $args['id'], $language );
                            if ( isset( $args['remoteId'] ) )
                                return $ctx->repository->contentByNodeRemoteId( $args['remoteId'], $language );
                            if ( isset( $args['path'] ) )
                                return $ctx->repository->contentByPathString( $args['path'], $language );
                            return $ctx->repository->contentByUrlAlias( $args['urlAlias'], $language );
                        },
                    ),
                    'content' => array(
                        'type' => $types->content(),
                        'description' => 'A content object at its main location, by its id or its remote id; with version, that version (a draft) for a user who may read it.',
                        'args' => array( 'id' => array( 'type' => Type::int() ), 'remoteId' => array( 'type' => Type::string() ),
                                         'version' => array( 'type' => Type::int(), 'description' => 'A version number (content/versionread, or a draft of your own); with id only.' ) ) + $byArgs,
                        'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                            $language = isset( $args['language'] ) ? (string)$args['language'] : false;
                            $given = array_intersect_key( $args, array( 'id' => 1, 'remoteId' => 1 ) );
                            if ( count( $given ) !== 1 )
                                throw new expGraphQLError( expGraphQLMessage::tr( 'content needs exactly one of id and remoteId.' ), expGraphQLError::BAD_USER_INPUT );
                            if ( isset( $args['version'] ) )
                            {
                                if ( !isset( $args['id'] ) )
                                    throw new expGraphQLError( expGraphQLMessage::tr( 'content takes version with id only.' ), expGraphQLError::BAD_USER_INPUT );
                                return $ctx->writer()->version( $args['id'], $args['version'], $language );
                            }
                            return isset( $args['id'] ) ? $ctx->repository->contentById( $args['id'], $language )
                                                        : $ctx->repository->contentByRemoteId( $args['remoteId'], $language );
                        },
                    ),
                    'preview' => array(
                        'type' => $types->content(),
                        'description' => 'The version a preview token names (createPreviewToken), until the token expires; no sign-in needed.',
                        'args' => array( 'token' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The token of createPreviewToken.' ) ),
                        'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                            $tokens = $ctx->previewTokens();
                            $data = $tokens !== null ? $tokens->verify( (string)$args['token'] ) : null;
                            if ( $data === null )
                                throw new expGraphQLError( expGraphQLMessage::tr( 'The preview token is not valid or has expired.' ), expGraphQLError::FORBIDDEN );
                            return $ctx->writer()->preview( $data );
                        },
                    ),
                    'children' => array(
                        'args' => array( 'parentId' => array( 'type' => Type::nonNull( Type::int() ), 'description' => 'The node id of the parent location.' ) )
                                  + $types->listArguments() + $byArgs,
                        'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                            $language = isset( $args['language'] ) ? (string)$args['language'] : false;
                            return new expGraphQLConnection( $ctx->repository, $ctx->repository->locationById( $args['parentId'], $language ), $args );
                        },
                    ) + $types->childrenField( function ( $root ) { return null; }, 'The content below a location (the user must be allowed to read the location).' ),
                    'search' => array(
                        'type' => Type::nonNull( $types->searchResult() ),
                        'description' => 'Content found by the search engine of the installation (the kernel\'s, or Solr through ezfind), as the user may read it: by text and filters.',
                        'args' => array(
                            'text' => array( 'type' => Type::string(), 'description' => 'The words to look for (the engine\'s syntax).' ),
                            'classes' => array( 'type' => Type::listOf( Type::nonNull( Type::string() ) ), 'description' => 'Only content of these class identifiers.' ),
                            'subtree' => array( 'type' => Type::listOf( Type::nonNull( Type::int() ) ), 'description' => 'Only content below these node ids.' ),
                            'sections' => array( 'type' => Type::listOf( Type::nonNull( Type::int() ) ), 'description' => 'Only content of these section ids.' ),
                            'publishedAfter' => array( 'type' => Type::string(), 'description' => 'Only content published at or after this date (ISO 8601).' ),
                            'publishedBefore' => array( 'type' => Type::string(), 'description' => 'Only content published at or before this date (ISO 8601).' ),
                            'facets' => array( 'type' => Type::listOf( Type::nonNull( Type::string() ) ),
                                               'description' => 'Facets to count (class, section, author, keywords, year ...), with an engine that has them.' ),
                            'limit' => array( 'type' => Type::int(), 'description' => 'At most this many (graphql.ini DefaultListLimit, at most MaxListLimit).' ),
                            'offset' => array( 'type' => Type::int(), 'description' => 'Skip this many.' ),
                            'first' => array( 'type' => Type::int(), 'description' => 'Cursor pagination: at most this many (instead of limit).' ),
                            'after' => array( 'type' => Type::string(), 'description' => 'Cursor pagination: the items after this cursor (instead of offset).' ),
                        ) + $byArgs,
                        'complexity' => function ( $childrenComplexity, $args ) { return 10 + $childrenComplexity * expGraphQLConnection::complexityCount( $args ); },
                        'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                            $text = isset( $args['text'] ) ? trim( (string)$args['text'] ) : '';
                            if ( $text === '' && empty( $args['classes'] ) && empty( $args['subtree'] ) && empty( $args['sections'] )
                                 && empty( $args['publishedAfter'] ) && empty( $args['publishedBefore'] ) )
                                throw new expGraphQLError( expGraphQLMessage::tr( 'search needs a text or a filter (classes, subtree, sections, publishedAfter, publishedBefore).' ),
                                                           expGraphQLError::BAD_USER_INPUT );
                            return new expGraphQLSearchConnection( $ctx->repository, $args );
                        },
                    ),
                    'currentUser' => array(
                        'type' => Type::nonNull( $types->user() ),
                        'description' => 'The user the request runs as, and how it was signed in (anonymous, token, apikey, session, console).',
                        'resolve' => function ( $root, $args, expGraphQLContext $ctx ) {
                            $user = $ctx->user;
                            return array( 'id' => $user instanceof eZUser ? (int)$user->id() : 0,
                                          'login' => $user instanceof eZUser && !$user->isAnonymous() ? (string)$user->attribute( 'login' ) : null,
                                          'name' => function () use ( $user ) {
                                              $object = $user instanceof eZUser ? $user->contentObject() : null;
                                              return $object instanceof eZContentObject ? (string)$object->attribute( 'name' ) : null;
                                          },
                                          'anonymous' => !$user instanceof eZUser || $user->isAnonymous(),
                                          'signedInBy' => $ctx->authMode );
                        },
                    ),
                    'siteaccess' => array(
                        'type' => Type::nonNull( $types->siteAccess() ),
                        'description' => 'The siteaccess that answers: its name, its languages, its root node and the hash of the schema.',
                        'resolve' => function ( $root, $args, expGraphQLContext $ctx ) use ( $builder ) {
                            return $ctx->repository->siteAccess() + array( 'schemaHash' => $builder->schemaHash() );
                        },
                    ),
                    'translations' => array(
                        'type' => Type::nonNull( Type::listOf( Type::nonNull( $types->language() ) ) ),
                        'description' => 'The content languages of the installation.',
                        'resolve' => function ( $root, $args, expGraphQLContext $ctx ) { return $ctx->repository->languages(); },
                    ),
                ) + $builder->extensionQueryFields();
            },
        ) );
        $this->schema = new Schema( array(
            'query' => $query,
            // the mutations unless they are switched off (graphql.ini MutationsEnabled, on by default): a schema without them
            // tells a client so, and the hash changes with them
            'mutation' => expGraphQLMutations::enabled() ? expGraphQLMutations::type( $types ) : null,
            'types' => function () use ( $builder ) {
                $all = array();
                foreach ( $builder->catalogue()->identifiers() as $identifier )
                {
                    $type = $builder->classType( $identifier );
                    if ( $type !== null )
                        $all[] = $type;
                }
                return $all;
            },
        ) );
        return $this->schema;
    }

    /**
     * @return array the root queries of the schema extensions (a name the kernel uses is not replaced)
     */
    public function extensionQueryFields()
    {
        $fields = array();
        foreach ( $this->extensions as $extension )
            $fields += (array)$extension->queryFields( $this->types );
        return $fields;
    }

    /**
     * The schema in the GraphQL schema language (SDL).
     *
     * @return string
     */
    public function sdl()
    {
        return \GraphQL\Utils\SchemaPrinter::doPrint( $this->schema() );
    }

    /**
     * What the schema is made of, for exp:graphql:schema --format=json and exp:graphql:status.
     *
     * @return array schemaHash, naming, classes (identifier, type, fields: attribute, field, datatype, mapped)
     */
    public function summary()
    {
        $classes = array();
        foreach ( $this->typeNames as $typeName => $identifier )
        {
            $class = $this->catalogue->classByIdentifier( $identifier );
            $fields = array();
            $used = array_flip( expGraphQLNaming::CONTENT_FIELDS );
            foreach ( $class['attributes'] as $attribute )
            {
                $name = $this->naming->fieldName( $identifier, $attribute['identifier'] );
                $unique = $name;
                for ( $n = 2; isset( $used[$unique] ); $n++ )
                    $unique = $name . '_' . $n;
                $used[$unique] = true;
                $fields[] = array( 'attribute' => $attribute['identifier'], 'field' => $unique, 'datatype' => $attribute['datatype'],
                                   'mapped' => $this->mapper->maps( $attribute['datatype'] ) );
            }
            $classes[] = array( 'identifier' => $identifier, 'type' => $typeName, 'fields' => $fields );
        }
        return array( 'schemaHash' => $this->schemaHash(), 'naming' => $this->naming->style(),
                      'extensions' => expGraphQLSchemaExtensions::names( $this->extensions ), 'classes' => $classes );
    }
}

?>
