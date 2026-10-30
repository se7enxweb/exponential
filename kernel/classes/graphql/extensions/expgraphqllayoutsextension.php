<?php
/**
 * The schema extension of explayouts, the page builder (graphql.ini SchemaExtensionList[]; doc/eng-us/features/6.0/
 * graphql.md, "Layouts"), available while explayouts is active:
 *
 *   layout(id | identifier | urlAlias): Layout   a published layout, or the one the rules give a page
 *   Location.layout: Layout                      the layout of the page of a location
 *
 * A Layout has zones (a zone linked to a shared layout shows the shared zone's blocks), a zone has blocks, a block
 * its definition, view types, parameters, the content of its manual collection (as the user may read it) and the
 * blocks inside it. Only published layouts of the site are answered, never a draft or a layout of the
 * administration. The parameters and collections of a level of blocks come in one batch each.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class expGraphQLLayoutsExtension implements expGraphQLSchemaExtension
{
    /** @var expGraphQLLayoutsSource */
    protected $source;

    /**
     * @param expGraphQLLayoutsSource|null $source null: explayouts
     */
    public function __construct( $source = null )
    {
        $this->source = $source instanceof expGraphQLLayoutsSource ? $source : new expGraphQLLayoutsSource();
    }

    public function isAvailable()
    {
        return $this->source->isAvailable();
    }

    public function name()
    {
        return 'explayouts/1';
    }

    public function datatypes()
    {
        return array();
    }

    public function fieldMapping( array $attribute, expGraphQLTypes $types )
    {
        return null;
    }

    public function queryFields( expGraphQLTypes $types )
    {
        $source = $this->source;
        return array(
            'layout' => array(
                'type' => $this->layoutType( $types ),
                'description' => 'A published layout of the page builder (explayouts), by exactly one of: its id, its identifier, or the URL alias of a page (the layout its rules give that page; "" is the front page).',
                'args' => array( 'id' => array( 'type' => Type::int() ), 'identifier' => array( 'type' => Type::string() ),
                                 'urlAlias' => array( 'type' => Type::string() ) ),
                'resolve' => function ( $root, $args ) use ( $source ) {
                    $given = array_intersect_key( $args, array( 'id' => 1, 'identifier' => 1, 'urlAlias' => 1 ) );
                    if ( count( $given ) !== 1 )
                        throw new expGraphQLError( expGraphQLMessage::tr( 'layout needs exactly one of id, identifier and urlAlias.' ), expGraphQLError::BAD_USER_INPUT );
                    if ( isset( $args['id'] ) )
                        return $source->layoutById( $args['id'] );
                    if ( isset( $args['identifier'] ) )
                        return $source->layoutByIdentifier( $args['identifier'] );
                    return $source->layoutForPath( $args['urlAlias'] );
                },
            ),
        );
    }

    public function locationFields( expGraphQLTypes $types )
    {
        $source = $this->source;
        return array(
            'layout' => array(
                'type' => $this->layoutType( $types ),
                'description' => 'The layout of the page builder the rules give the page of this location (explayouts).',
                'resolve' => function ( expGraphQLLocation $l ) use ( $source ) { return $source->layoutForPath( $l->urlAlias() ); },
            ),
        );
    }

    /**
     * @param expGraphQLTypes $types
     * @return ObjectType
     */
    public function layoutType( expGraphQLTypes $types )
    {
        $source = $this->source;
        $zone = $this->zoneType( $types );
        return $types->register( 'Layout', function () use ( $source, $zone ) {
            $value = function ( $key ) { return function ( array $v ) use ( $key ) { return $v[$key]; }; };
            return new ObjectType( array(
                'name' => 'Layout',
                'description' => 'A published layout of the page builder (explayouts): its zones and their blocks.',
                'fields' => array(
                    'id' => array( 'type' => Type::nonNull( Type::int() ), 'resolve' => $value( 'id' ) ),
                    'identifier' => array( 'type' => Type::nonNull( Type::string() ), 'resolve' => $value( 'identifier' ) ),
                    'name' => array( 'type' => Type::string(), 'resolve' => $value( 'name' ) ),
                    'type' => array( 'type' => Type::string(), 'description' => 'The layout type (explayouts.ini [LayoutType_*]).', 'resolve' => $value( 'type' ) ),
                    'shared' => array( 'type' => Type::nonNull( Type::boolean() ), 'description' => 'Its zones are linked into other layouts (a header, a footer).',
                                       'resolve' => $value( 'shared' ) ),
                    'modified' => array( 'type' => Type::string(), 'resolve' => function ( array $v ) { return expGraphQLTypes::isoDate( $v['modified'] ); } ),
                    'zones' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( $zone ) ) ),
                                      'resolve' => function ( array $v ) use ( $source ) { return $source->zones( $v['id'] ); } ),
                    'zone' => array( 'type' => $zone, 'args' => array( 'identifier' => array( 'type' => Type::nonNull( Type::string() ) ) ),
                                     'description' => 'One zone, by its identifier (main, left ...).',
                                     'resolve' => function ( array $v, $args ) use ( $source ) {
                                         foreach ( $source->zones( $v['id'] ) as $zone )
                                             if ( $zone['identifier'] === $args['identifier'] )
                                                 return $zone;
                                         return null;
                                     } ),
                ),
            ) );
        } );
    }

    /**
     * @param expGraphQLTypes $types
     * @return ObjectType
     */
    protected function zoneType( expGraphQLTypes $types )
    {
        $source = $this->source;
        $block = $this->blockType( $types );
        return $types->register( 'LayoutZone', function () use ( $source, $block ) {
            $value = function ( $key ) { return function ( array $v ) use ( $key ) { return $v[$key]; }; };
            return new ObjectType( array(
                'name' => 'LayoutZone',
                'description' => 'A zone of a layout and its blocks; a zone linked to a zone of a shared layout shows that zone\'s blocks.',
                'fields' => array(
                    'identifier' => array( 'type' => Type::nonNull( Type::string() ), 'resolve' => $value( 'identifier' ) ),
                    'linkedLayoutId' => array( 'type' => Type::int(), 'description' => 'The shared layout it is linked to.', 'resolve' => $value( 'linkedLayoutId' ) ),
                    'linkedZone' => array( 'type' => Type::string(), 'description' => 'The zone of the shared layout it shows.', 'resolve' => $value( 'linkedZone' ) ),
                    'blocks' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( $block ) ) ),
                                       'resolve' => function ( array $v ) use ( $source ) { return $source->blocks( $v['sourceZoneId'] ); } ),
                ),
            ) );
        } );
    }

    /**
     * @param expGraphQLTypes $types
     * @return ObjectType
     */
    protected function blockType( expGraphQLTypes $types )
    {
        $source = $this->source;
        return $types->register( 'LayoutBlock', function () use ( $source, $types ) {
            $parameters = function ( expGraphQLContext $ctx ) use ( $source ) {
                return $ctx->loader( 'layoutBlockParameters', false, function ( array $ids ) use ( $source ) { return $source->parametersOf( $ids ); } );
            };
            $collections = function ( expGraphQLContext $ctx ) use ( $source ) {
                return $ctx->loader( 'layoutBlockCollections', false, function ( array $ids ) use ( $source ) { return $source->collectionsOf( $ids ); } );
            };
            return new ObjectType( array(
                'name' => 'LayoutBlock',
                'description' => 'A block of a zone: what it shows (its definition and view type), its parameters, the content of its collection, and the blocks in it.',
                'fields' => function () use ( $source, $types, $parameters, $collections ) {
                    $block = $types->register( 'LayoutBlock', function () {} );
                    $parameter = $types->register( 'LayoutBlockParameter', function () {
                        return new ObjectType( array( 'name' => 'LayoutBlockParameter', 'description' => 'A parameter of a block: its name and its value as stored.',
                                                      'fields' => array( 'name' => array( 'type' => Type::nonNull( Type::string() ), 'resolve' => function ( array $p ) { return $p['name']; } ),
                                                                         'value' => array( 'type' => Type::string(), 'resolve' => function ( array $p ) { return $p['value']; } ) ) ) );
                    } );
                    $value = function ( $key ) { return function ( array $v ) use ( $key ) { return $v[$key] !== '' ? $v[$key] : null; }; };
                    $collection = function ( array $b, expGraphQLContext $ctx, callable $then ) use ( $collections ) {
                        $loader = $collections( $ctx );
                        $loader->want( $b['id'] );
                        return new \GraphQL\Deferred( function () use ( $loader, $b, $then ) { return $then( $loader->value( $b['id'] ) ); } );
                    };
                    return array(
                        'id' => array( 'type' => Type::nonNull( Type::int() ), 'resolve' => function ( array $b ) { return $b['id']; } ),
                        'definition' => array( 'type' => Type::nonNull( Type::string() ), 'description' => 'The block definition (explayouts.ini [BlockDefinition_*]): list, title, html ...',
                                               'resolve' => function ( array $b ) { return $b['definition']; } ),
                        'viewType' => array( 'type' => Type::string(), 'resolve' => $value( 'viewType' ) ),
                        'itemViewType' => array( 'type' => Type::string(), 'description' => 'How the block shows each of its items.', 'resolve' => $value( 'itemViewType' ) ),
                        'name' => array( 'type' => Type::string(), 'resolve' => $value( 'name' ) ),
                        'position' => array( 'type' => Type::int(), 'resolve' => function ( array $b ) { return $b['position']; } ),
                        'placeholder' => array( 'type' => Type::string(), 'description' => 'The placeholder of its parent container it sits in.', 'resolve' => $value( 'placeholder' ) ),
                        'parameters' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( $parameter ) ) ),
                                               'resolve' => function ( array $b, $args, expGraphQLContext $ctx ) use ( $parameters ) {
                                                   $loader = $parameters( $ctx );
                                                   $loader->want( $b['id'] );
                                                   return new \GraphQL\Deferred( function () use ( $loader, $b ) {
                                                       $list = array();
                                                       foreach ( (array)$loader->value( $b['id'] ) as $name => $value )
                                                           $list[] = array( 'name' => (string)$name, 'value' => $value );
                                                       return $list;
                                                   } );
                                               } ),
                        'parameter' => array( 'type' => Type::string(), 'args' => array( 'name' => array( 'type' => Type::nonNull( Type::string() ) ) ),
                                              'description' => 'One parameter\'s value, by its name.',
                                              'resolve' => function ( array $b, $args, expGraphQLContext $ctx ) use ( $parameters ) {
                                                  $loader = $parameters( $ctx );
                                                  $loader->want( $b['id'] );
                                                  return new \GraphQL\Deferred( function () use ( $loader, $b, $args ) {
                                                      $all = (array)$loader->value( $b['id'] );
                                                      return isset( $all[$args['name']] ) ? $all[$args['name']] : null;
                                                  } );
                                              } ),
                        'collectionType' => array( 'type' => Type::string(), 'description' => 'manual or dynamic; null for a block without a collection.',
                                                   'resolve' => function ( array $b, $args, expGraphQLContext $ctx ) use ( $collection ) {
                                                       return $collection( $b, $ctx, function ( $c ) { return $c ? $c['type'] : null; } );
                                                   } ),
                        'items' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( $types->content() ) ) ),
                                          'description' => 'The content of the block\'s collection placed by hand, at their locations, as the user may read it (a dynamic collection\'s query runs when the page renders).',
                                          'resolve' => function ( array $b, $args, expGraphQLContext $ctx ) use ( $collection ) {
                                              return $collection( $b, $ctx, function ( $c ) use ( $ctx ) {
                                                  $loader = $ctx->loader( 'node' );
                                                  $ids = $c ? $c['nodeIds'] : array();
                                                  foreach ( $ids as $id )
                                                      $loader->want( $id );
                                                  $loader->flush();
                                                  $items = array();
                                                  foreach ( $ids as $id )
                                                  {
                                                      $item = $loader->value( $id );
                                                      if ( $item !== null )
                                                          $items[] = $item;
                                                  }
                                                  return $items;
                                              } );
                                          } ),
                        'children' => array( 'type' => Type::nonNull( Type::listOf( Type::nonNull( $block ) ) ), 'description' => 'The blocks inside it (a container).',
                                             'resolve' => function ( array $b ) use ( $source ) { return $source->children( $b['id'] ); } ),
                    );
                },
            ) );
        } );
    }
}

?>
