<?php
/**
 * The GraphQL field of a class attribute, by datatype: its type, and how its value is read from the field of a
 * content object. A datatype without a mapping here is a String with the field's text form (toString()), so
 * every attribute is in the schema (exp:graphql:schema --format=json lists which datatypes fall back).
 *
 *   ezstring eztext ezemail ezisbn ezidentifier   String
 *   ezinteger Int, ezfloat Float, ezboolean Boolean
 *   ezdate (2026-10-29), ezdatetime (ISO 8601), eztime (13:45:00)   String
 *   ezxmltext RichText (html, text, xml, excerpt), ezimage Image (url(alias:), variations)
 *   ezbinaryfile ezmedia BinaryFile, ezprice Price, ezurl Link, ezselection [SelectionOption], ezcountry [Country],
 *   ezkeyword [String], ezauthor [Author], ezmatrix Matrix
 *   ezobjectrelation Content, ezobjectrelationlist [Content] (only what the user may read)
 *
 * Loaded only after webonyx/graphql-php.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

use GraphQL\Type\Definition\Type;

class expGraphQLFieldMapper
{
    /** The datatypes with a mapping of their own (the rest falls back to String). */
    const MAPPED = array( 'ezstring', 'eztext', 'ezemail', 'ezisbn', 'ezidentifier', 'ezinteger', 'ezfloat', 'ezboolean', 'ezdate',
                          'ezdatetime', 'eztime', 'ezxmltext', 'ezimage', 'ezbinaryfile', 'ezmedia', 'ezprice', 'ezurl', 'ezselection',
                          'ezcountry', 'ezkeyword', 'ezauthor', 'ezmatrix', 'ezobjectrelation', 'ezobjectrelationlist' );

    /** @var expGraphQLTypes */
    protected $types;

    /**
     * @param expGraphQLTypes $types
     */
    public function __construct( expGraphQLTypes $types )
    {
        $this->types = $types;
    }

    /**
     * @param string $datatype
     * @return bool the datatype has a mapping of its own
     */
    public static function isMapped( $datatype )
    {
        return in_array( $datatype, self::MAPPED, true );
    }

    /**
     * A list of values of a batch loader, in the order of the keys, without the keys that have none: every key is
     * asked for now (so the whole level of the query is fetched together), the list is made when it is read.
     *
     * @param expGraphQLBatchLoader $loader
     * @param array $keys
     * @return \GraphQL\Deferred
     */
    public static function loadList( expGraphQLBatchLoader $loader, array $keys )
    {
        foreach ( $keys as $key )
            $loader->want( $key );
        return new \GraphQL\Deferred( function () use ( $loader, $keys ) {
            $values = array();
            foreach ( $keys as $key )
            {
                $value = $loader->value( $key );
                if ( $value !== null )
                    $values[] = $value;
            }
            return $values;
        } );
    }

    /**
     * The field config of an attribute.
     *
     * @param array $attribute an attribute of expGraphQLClassCatalogue
     * @return array type, description, resolve (and args)
     */
    public function field( array $attribute )
    {
        $identifier = $attribute['identifier'];
        $description = trim( $attribute['name'] . ( $attribute['description'] !== '' ? ': ' . $attribute['description'] : '' ) . ' (' . $attribute['datatype'] . ')' );
        $read = function ( $resolver ) use ( $identifier ) {
            return function ( expGraphQLContent $content, $args, expGraphQLContext $ctx ) use ( $resolver, $identifier ) {
                $field = $content->attribute( $identifier );
                if ( !$field instanceof eZContentObjectAttribute )
                    return null;
                return $resolver( $field, $ctx, $args );
            };
        };
        $config = $this->mapping( $attribute );
        return array( 'type' => $config[0], 'description' => $description, 'resolve' => $read( $config[1] ) );
    }

    /**
     * @param array $attribute
     * @return array array( type, function( eZContentObjectAttribute $field, expGraphQLContext $ctx, array $args ) )
     */
    protected function mapping( array $attribute )
    {
        $types = $this->types;
        $text = function ( eZContentObjectAttribute $f ) { return $f->hasContent() ? (string)$f->toString() : null; };
        switch ( $attribute['datatype'] )
        {
            case 'ezstring':
            case 'eztext':
            case 'ezemail':
            case 'ezisbn':
            case 'ezidentifier':
                return array( Type::string(), $text );
            case 'ezinteger':
                return array( Type::int(), function ( eZContentObjectAttribute $f ) { return $f->hasContent() ? (int)$f->attribute( 'data_int' ) : null; } );
            case 'ezfloat':
                return array( Type::float(), function ( eZContentObjectAttribute $f ) { return $f->hasContent() ? (float)$f->attribute( 'data_float' ) : null; } );
            case 'ezboolean':
                return array( Type::boolean(), function ( eZContentObjectAttribute $f ) { return (bool)$f->attribute( 'data_int' ); } );
            case 'ezdate':
                return array( Type::string(), function ( eZContentObjectAttribute $f ) { return expGraphQLTypes::isoDate( $f->attribute( 'data_int' ), true ); } );
            case 'ezdatetime':
                return array( Type::string(), function ( eZContentObjectAttribute $f ) { return expGraphQLTypes::isoDate( $f->attribute( 'data_int' ) ); } );
            case 'eztime':
                return array( Type::string(), function ( eZContentObjectAttribute $f ) {
                    if ( !$f->hasContent() )
                        return null;
                    $seconds = (int)$f->attribute( 'data_int' );
                    return sprintf( '%02d:%02d:%02d', intdiv( $seconds, 3600 ) % 24, intdiv( $seconds, 60 ) % 60, $seconds % 60 );
                } );
            case 'ezxmltext':
                return array( $types->richText(), function ( eZContentObjectAttribute $f ) {
                    return $f->hasContent() ? new expGraphQLRichText( (string)$f->attribute( 'data_text' ), $f ) : null;
                } );
            case 'ezimage':
                return array( $types->image(), function ( eZContentObjectAttribute $f, expGraphQLContext $ctx ) {
                    return $f->hasContent() ? new expGraphQLImage( $f, $ctx ) : null;
                } );
            case 'ezbinaryfile':
            case 'ezmedia':
                return array( $types->binaryFile(), function ( eZContentObjectAttribute $f, expGraphQLContext $ctx ) {
                    $file = $f->hasContent() ? $f->content() : null;
                    if ( !is_object( $file ) )
                        return null;
                    $name = (string)$file->attribute( 'original_filename' );
                    return array( 'fileName' => $name, 'mimeType' => (string)$file->attribute( 'mime_type' ),
                                  'size' => (int)$file->attribute( 'filesize' ),
                                  'url' => $ctx->siteUrl( 'content/download/' . (int)$f->attribute( 'contentobject_id' ) . '/' . (int)$f->attribute( 'id' ) . '/file/' . rawurlencode( $name ) ) );
                } );
            case 'ezprice':
                return array( $types->price(), function ( eZContentObjectAttribute $f ) {
                    $price = $f->content();
                    if ( !is_object( $price ) )
                        return null;
                    return array( 'price' => (float)$price->attribute( 'price' ), 'incVat' => (float)$price->attribute( 'inc_vat_price' ),
                                  'exVat' => (float)$price->attribute( 'ex_vat_price' ), 'vatPercent' => (float)$price->attribute( 'vat_percent' ) );
                } );
            case 'ezurl':
                return array( $types->link(), function ( eZContentObjectAttribute $f ) {
                    if ( !$f->hasContent() )
                        return null;
                    return array( 'url' => (string)$f->content(), 'text' => (string)$f->attribute( 'data_text' ) );
                } );
            case 'ezselection':
                $options = isset( $attribute['options'] ) ? (array)$attribute['options'] : array();
                return array( Type::nonNull( Type::listOf( Type::nonNull( $types->selectionOption() ) ) ), function ( eZContentObjectAttribute $f ) use ( $options ) {
                    $chosen = array();
                    foreach ( (array)$f->content() as $id )
                    {
                        if ( $id === '' || $id === null )
                            continue;
                        $chosen[] = array( 'id' => (int)$id, 'name' => isset( $options[(int)$id] ) ? $options[(int)$id] : null );
                    }
                    return $chosen;
                } );
            case 'ezcountry':
                return array( Type::nonNull( Type::listOf( Type::nonNull( $types->country() ) ) ), function ( eZContentObjectAttribute $f ) {
                    $content = $f->content();
                    if ( is_array( $content ) && isset( $content['value'] ) && is_array( $content['value'] ) )
                        $content = $content['value'];
                    $countries = array();
                    foreach ( is_array( $content ) ? $content : array() as $code => $country )
                    {
                        if ( is_array( $country ) && !empty( $country['Alpha2'] ) )
                            $countries[] = array( 'code' => (string)$country['Alpha2'], 'name' => isset( $country['Name'] ) ? (string)$country['Name'] : null );
                        else if ( is_string( $code ) && $code !== '' && $country !== '' )
                            $countries[] = array( 'code' => $code, 'name' => null );
                    }
                    return $countries;
                } );
            case 'ezkeyword':
                return array( Type::nonNull( Type::listOf( Type::nonNull( Type::string() ) ) ), function ( eZContentObjectAttribute $f ) {
                    $keyword = $f->content();
                    return is_object( $keyword ) ? array_values( array_map( 'strval', (array)$keyword->keywordArray() ) ) : array();
                } );
            case 'ezauthor':
                return array( Type::nonNull( Type::listOf( Type::nonNull( $types->author() ) ) ), function ( eZContentObjectAttribute $f ) {
                    $author = $f->content();
                    $authors = array();
                    foreach ( is_object( $author ) ? (array)$author->attribute( 'author_list' ) : array() as $entry )
                        $authors[] = array( 'name' => isset( $entry['name'] ) ? (string)$entry['name'] : null );
                    return $authors;
                } );
            case 'ezmatrix':
                return array( $types->matrix(), function ( eZContentObjectAttribute $f ) {
                    $matrix = $f->content();
                    if ( !is_object( $matrix ) )
                        return null;
                    $columns = $matrix->attribute( 'columns' );
                    $rows = $matrix->attribute( 'rows' );
                    $names = array();
                    foreach ( isset( $columns['sequential'] ) ? $columns['sequential'] : array() as $column )
                        $names[] = (string)$column['name'];
                    $values = array();
                    foreach ( isset( $rows['sequential'] ) ? $rows['sequential'] : array() as $row )
                        $values[] = array_values( array_map( 'strval', (array)$row['columns'] ) );
                    return array( 'columns' => $names, 'rows' => $values );
                } );
            case 'ezobjectrelation':
                return array( $types->content(), function ( eZContentObjectAttribute $f, expGraphQLContext $ctx ) {
                    $id = (int)$f->attribute( 'data_int' );
                    return $id > 0 ? $ctx->loader( 'content', $f->attribute( 'language_code' ) )->load( $id ) : null;
                } );
            case 'ezobjectrelationlist':
                return array( Type::nonNull( Type::listOf( Type::nonNull( $types->content() ) ) ), function ( eZContentObjectAttribute $f, expGraphQLContext $ctx ) {
                    $content = $f->content();
                    $ids = array();
                    foreach ( isset( $content['relation_list'] ) ? (array)$content['relation_list'] : array() as $relation )
                        $ids[] = (int)$relation['contentobject_id'];
                    return expGraphQLFieldMapper::loadList( $ctx->loader( 'content', $f->attribute( 'language_code' ) ), $ids );
                } );
        }
        return array( Type::string(), $text );
    }
}

?>
