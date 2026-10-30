<?php
/**
 * The types of the datatypes of extensions and of the shop (doc/eng-us/features/6.0/graphql.md, "Datatypes"): map
 * location, star rating, meta data, enhanced link, enhanced selection, class list, options, multi-options, ranges,
 * multi-prices, enumerations, subscriptions and product categories, each read from a field whose content is given
 * (no database, no extension), and a schema extension that maps a datatype of its own and adds a root query and a
 * field of Location.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expGraphQLSchemaTest.php';

/** A field whose content is given. */
class expGraphQLTestAttribute extends eZContentObjectAttribute
{
    /** @var mixed */
    public $given;

    public function __construct( $datatype, $given, array $row = array() )
    {
        parent::__construct( $row + array( 'data_type_string' => $datatype, 'data_text' => '', 'data_int' => 0, 'data_float' => 0, 'language_code' => 'eng-GB',
                                           'contentobject_id' => 0, 'id' => 0, 'version' => 1 ) );
        $this->given = $given;
    }

    public function content() { return $this->given; }
    public function hasContent() { return $this->given !== null && $this->given !== array(); }
}

/** An object of a datatype's content read with attribute(). */
class expGraphQLTestValue
{
    /** @var array */
    public $values;

    public function __construct( array $values ) { $this->values = $values; }
    public function attribute( $name ) { return isset( $this->values[$name] ) ? $this->values[$name] : null; }
}

/** A schema extension mapping the datatype exptestcolor and adding the root query color and Location.colorOfLocation. */
class expGraphQLTestColorExtension implements expGraphQLSchemaExtension
{
    public function isAvailable() { return true; }
    public function name() { return 'color/1'; }
    public function datatypes() { return array( 'exptestcolor' ); }
    public function queryFields( expGraphQLTypes $types )
    {
        return array( 'color' => array( 'type' => GraphQL\Type\Definition\Type::string(), 'resolve' => function () { return 'red'; } ) );
    }
    public function locationFields( expGraphQLTypes $types )
    {
        return array( 'colorOfLocation' => array( 'type' => GraphQL\Type\Definition\Type::string(),
                                                  'resolve' => function ( expGraphQLLocation $l ) { return 'color-' . $l->id(); } ) );
    }
    public function fieldMapping( array $attribute, expGraphQLTypes $types )
    {
        return array( GraphQL\Type\Definition\Type::string(), function ( eZContentObjectAttribute $f ) { return strtoupper( (string)$f->attribute( 'data_text' ) ); } );
    }
}

class expGraphQLDatatypesTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        if ( !expGraphQL::loadLibrary() )
        {
            if ( getenv( 'CI' ) )
                self::fail( 'webonyx/graphql-php is not installed (composer.json requires it)' );
            self::markTestSkipped( 'webonyx/graphql-php is not installed; set GRAPHQL_LIBRARY_PATH' );
        }
    }

    /**
     * @param array $fields attribute identifier => array( datatype, field )
     * @param string $query the selection on the class thing
     * @param array $extensions
     * @return array the answer
     */
    protected static function read( array $fields, $query, array $extensions = array() )
    {
        $attributes = array();
        $dataMap = array();
        foreach ( $fields as $identifier => $field )
        {
            $attributes[] = array( 'id' => crc32( $identifier ), 'identifier' => $identifier, 'datatype' => $field[0], 'name' => $identifier, 'description' => '',
                                   'required' => false, 'options' => array() );
            $dataMap[$identifier] = $field[1];
        }
        $catalogue = expGraphQLClassCatalogue::filtered( array( array( 'id' => 9, 'identifier' => 'thing', 'name' => 'Thing', 'description' => '',
                                                                       'attributes' => $attributes ) ), array() );
        $builder = new expGraphQLSchemaBuilder( $catalogue, new expGraphQLNaming( 'identifier' ), $extensions );
        $repository = new expGraphQLTestRepository( $catalogue, array() );
        $repository->byNode[2] = new expGraphQLTestContent( array( 'id' => 7, 'class' => 'thing', 'name' => 'Thing',
            'location' => new expGraphQLTestLocation( array( 'id' => 2, 'parentId' => 1, 'contentId' => 7, 'depth' => 1, 'pathString' => '/1/2/', 'urlAlias' => '' ) ),
            'fields' => $dataMap ) );
        $context = new expGraphQLContext( new eZUser( array( 'contentobject_id' => 14, 'login' => 'admin' ) ), expGraphQLContext::AUTH_TOKEN, $repository );
        return ( new expGraphQLServer( $builder ) )->execute( '{ node(id: 2) { ... on thing { ' . $query . ' } } }', null, null, $context );
    }

    public function testTheDatatypesOfExtensionsHaveTypes()
    {
        $meta = new stdClass();
        $meta->title = 'Title';
        $meta->description = '';
        $meta->keywords = array( 'a', '', 'b' );
        $meta->priority = '0.8';
        $meta->change = 'daily';
        $meta->sitemap_use = '1';
        $answer = self::read( array(
            'place' => array( 'ezgmaplocation', new expGraphQLTestAttribute( 'ezgmaplocation', new expGraphQLTestValue( array( 'latitude' => '52.52', 'longitude' => '13.405', 'address' => 'Berlin' ) ) ) ),
            'stars' => array( 'ezsrrating', new expGraphQLTestAttribute( 'ezsrrating', new expGraphQLTestValue( array( 'rating_average' => 4.3333, 'rating_count' => 3 ) ) ) ),
            'unrated' => array( 'ezsrrating', new expGraphQLTestAttribute( 'ezsrrating', new expGraphQLTestValue( array( 'rating_average' => 0, 'rating_count' => 0 ) ) ) ),
            'seo' => array( 'xrowmetadata', new expGraphQLTestAttribute( 'xrowmetadata', $meta ) ),
            'external' => array( 'ngenhancedlink', new expGraphQLTestAttribute( 'ngenhancedlink', (object)array( 'reference' => 'https://example.org/', 'label' => 'Out',
                'target' => '_blank', 'suffix' => null, 'relAttribute' => 'nofollow', 'type' => 'external' ) ) ),
            'internal' => array( 'ngenhancedlink', new expGraphQLTestAttribute( 'ngenhancedlink', (object)array( 'reference' => 7, 'label' => null,
                'target' => 'link', 'suffix' => '#top', 'relAttribute' => null, 'type' => 'internal' ) ) ),
            'size' => array( 'sckenhancedselection', new expGraphQLTestAttribute( 'sckenhancedselection', array( 'small', 'large' ) ) ),
            'classes' => array( 'ngclasslist', new expGraphQLTestAttribute( 'ngclasslist', array( 'class_identifiers' => array( 'article', 'folder' ) ) ) ),
        ), 'place { latitude longitude address } stars { average count } unrated { average count }
            seo { title description keywords priority change sitemapUse }
            external { type url label target rel content { id } } internal { type url label suffix content { name } } size classes' );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $thing = $answer['data']['node'];
        $this->assertSame( array( 'latitude' => 52.52, 'longitude' => 13.405, 'address' => 'Berlin' ), $thing['place'] );
        $this->assertSame( array( 'average' => 4.33, 'count' => 3 ), $thing['stars'] );
        $this->assertSame( array( 'average' => null, 'count' => 0 ), $thing['unrated'] );
        $this->assertSame( array( 'title' => 'Title', 'description' => null, 'keywords' => array( 'a', 'b' ), 'priority' => '0.8', 'change' => 'daily',
                                  'sitemapUse' => true ), $thing['seo'] );
        $this->assertSame( array( 'type' => 'external', 'url' => 'https://example.org/', 'label' => 'Out', 'target' => '_blank', 'rel' => 'nofollow',
                                  'content' => null ), $thing['external'] );
        $this->assertSame( 'internal', $thing['internal']['type'] );
        $this->assertSame( '/#top', $thing['internal']['url'], 'the address of the linked content and the suffix' );
        $this->assertSame( array( 'name' => 'Thing' ), $thing['internal']['content'] );
        $this->assertSame( array( 'small', 'large' ), $thing['size'] );
        $this->assertSame( array( 'article', 'folder' ), $thing['classes'] );
    }

    public function testTheShopAndOptionDatatypesHaveTypes()
    {
        $price = function ( $currency, $value ) { return new expGraphQLTestValue( array( 'currency_code' => $currency, 'value' => $value, 'type' => 1 ) ); };
        $answer = self::read( array(
            'option' => array( 'ezoption', new expGraphQLTestAttribute( 'ezoption', new expGraphQLTestValue( array( 'name' => 'Colour', 'option_list' => array(
                array( 'id' => 1, 'value' => 'Red', 'additional_price' => '' ), array( 'id' => 2, 'value' => 'Blue', 'additional_price' => '2.50' ) ) ) ) ) ),
            'multi' => array( 'ezmultioption', new expGraphQLTestAttribute( 'ezmultioption', new expGraphQLTestValue( array( 'multioption_list' => array(
                array( 'name' => 'Size', 'optionlist' => array( array( 'id' => 1, 'value' => 'S', 'additional_price' => '0' ) ) ) ) ) ) ) ),
            'range' => array( 'ezrangeoption', new expGraphQLTestAttribute( 'ezrangeoption', new expGraphQLTestValue( array( 'name' => 'Length', 'start_value' => 1,
                'stop_value' => 10, 'step_value' => 0.5 ) ) ) ),
            'prices' => array( 'ezmultiprice', new expGraphQLTestAttribute( 'ezmultiprice', new expGraphQLTestValue( array( 'price_list' => array( $price( 'EUR', '9.90' ), $price( 'USD', 11 ) ) ) ) ) ),
            'grade' => array( 'ezenum', new expGraphQLTestAttribute( 'ezenum', new expGraphQLTestValue( array( 'enumobject_list' => array(
                new expGraphQLTestValue( array( 'enumelement' => 'Good', 'enumvalue' => '2' ) ) ) ) ) ) ),
            'subscribe' => array( 'ezsubtreesubscription', new expGraphQLTestAttribute( 'ezsubtreesubscription', null, array( 'data_int' => 1 ) ) ),
            'category' => array( 'ezproductcategory', new expGraphQLTestAttribute( 'ezproductcategory', new expGraphQLTestValue( array( 'name' => 'Books' ) ) ) ),
        ), 'option { name options { id value additionalPrice } } multi { name options { value additionalPrice } } range { name start stop step }
            prices { currency value type } grade { element value } subscribe category' );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $thing = $answer['data']['node'];
        $this->assertSame( array( 'name' => 'Colour', 'options' => array( array( 'id' => 1, 'value' => 'Red', 'additionalPrice' => null ),
                                                                         array( 'id' => 2, 'value' => 'Blue', 'additionalPrice' => 2.5 ) ) ), $thing['option'] );
        $this->assertSame( array( array( 'name' => 'Size', 'options' => array( array( 'value' => 'S', 'additionalPrice' => 0.0 ) ) ) ), $thing['multi'] );
        $this->assertSame( array( 'name' => 'Length', 'start' => 1.0, 'stop' => 10.0, 'step' => 0.5 ), $thing['range'] );
        $this->assertSame( array( array( 'currency' => 'EUR', 'value' => 9.9, 'type' => 1 ), array( 'currency' => 'USD', 'value' => 11.0, 'type' => 1 ) ), $thing['prices'] );
        $this->assertSame( array( array( 'element' => 'Good', 'value' => '2' ) ), $thing['grade'] );
        $this->assertTrue( $thing['subscribe'] );
        $this->assertSame( 'Books', $thing['category'] );
    }

    public function testASchemaExtensionAddsADatatypeAQueryAndALocationField()
    {
        $extension = new expGraphQLTestColorExtension();
        $answer = self::read( array( 'paint' => array( 'exptestcolor', new expGraphQLTestAttribute( 'exptestcolor', 'x', array( 'data_text' => 'teal' ) ) ) ),
                              'paint location { colorOfLocation }', array( $extension ) );
        $this->assertArrayNotHasKey( 'errors', $answer, json_encode( $answer ) );
        $this->assertSame( array( 'paint' => 'TEAL', 'location' => array( 'colorOfLocation' => 'color-2' ) ), $answer['data']['node'] );

        $catalogue = expGraphQLClassCatalogue::filtered( array(), array() );
        $plain = new expGraphQLSchemaBuilder( $catalogue, new expGraphQLNaming( 'identifier' ) );
        $extended = new expGraphQLSchemaBuilder( $catalogue, new expGraphQLNaming( 'identifier' ), array( $extension ) );
        $this->assertNotSame( $plain->schemaHash(), $extended->schemaHash(), 'the extensions are part of the schema hash' );
        $this->assertStringContainsString( 'color: String', $extended->sdl() );
        $this->assertSame( array( 'color/1' ), $extended->summary()['extensions'] );
        $this->assertSame( array(), expGraphQLSchemaExtensions::instances( array( 'expGraphQLNoSuchExtension', 'stdClass' ) ), 'not a class, not an extension: left out' );
    }
}
