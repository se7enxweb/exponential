<?php
/**
 * The names of the GraphQL schema: a type per content class, a field per class attribute (graphql.ini
 * [GraphQLSettings] NamingStyle):
 *
 *   identifier  (the default) the administration's identifiers as they are: ng_article, short_title
 *   pascal      types in PascalCase (NgArticle), fields in camelCase (shortTitle)
 *   map         as identifier, but [GraphQLNaming] TypeNames[<class>] and FieldNames[<class>/<attribute>] win
 *
 * A name is made valid for GraphQL (/[_A-Za-z][_0-9A-Za-z]*\/): other characters become _, a leading digit gets a _
 * in front, a leading __ (reserved) becomes a single _. A type name that is one of the schema's own types gets the
 * suffix Content (an image class in pascal style is ImageContent, as Image is the type of an image field); an
 * attribute named like a field every content has (CONTENT_FIELDS) gets the suffix _field.
 *
 * No database; the same input gives the same names (the schema's names are part of a client's queries).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLNaming
{
    const STYLE_IDENTIFIER = 'identifier';
    const STYLE_PASCAL = 'pascal';
    const STYLE_MAP = 'map';

    /** The types of the schema itself, and GraphQL's: no class type may take these names. */
    const RESERVED_TYPES = array( 'Query', 'Mutation', 'Subscription', 'Content', 'Location', 'ContentConnection', 'ContentEdge',
                                  'PageInfo', 'ContentSortField', 'SortOrder', 'Image', 'ImageVariation', 'RichText', 'BinaryFile',
                                  'Price', 'Link', 'SelectionOption', 'Country', 'Author', 'Matrix', 'User', 'SiteAccess', 'Language',
                                  'Int', 'Float', 'String', 'Boolean', 'ID',
                                  'GeoLocation', 'Rating', 'Metadata', 'EnhancedLink', 'Option', 'OptionList', 'OptionGroup', 'Range',
                                  'CurrencyPrice', 'EnumValue', 'SearchResult', 'SearchFacet', 'SearchFacetValue', 'Tag', 'Layout',
                                  'LayoutZone', 'LayoutBlock', 'LayoutBlockParameter',
                                  'Upload', 'FieldInput', 'FieldFormat', 'MutationStatus', 'ContentPayload', 'PreviewToken' );

    /** The fields every content type has (the interface Content): an attribute of the same name gets _field. */
    const CONTENT_FIELDS = array( 'id', 'remoteId', 'name', 'classIdentifier', 'className', 'published', 'modified', 'language',
                                  'languages', 'mainLanguage', 'ownerId', 'ownerName', 'sectionId', 'url', 'urlAlias', 'location',
                                  'mainLocation', 'locations', 'parent', 'children' );

    /** @var string */
    protected $style;

    /** @var array class identifier => type name (style map) */
    protected $typeNames;

    /** @var array class/attribute => field name (style map) */
    protected $fieldNames;

    /**
     * @param string $style identifier, pascal or map (anything else: identifier)
     * @param array $typeNames [GraphQLNaming] TypeNames[]
     * @param array $fieldNames [GraphQLNaming] FieldNames[]
     */
    public function __construct( $style = self::STYLE_IDENTIFIER, array $typeNames = array(), array $fieldNames = array() )
    {
        $this->style = in_array( $style, array( self::STYLE_IDENTIFIER, self::STYLE_PASCAL, self::STYLE_MAP ), true ) ? $style : self::STYLE_IDENTIFIER;
        $this->typeNames = $typeNames;
        $this->fieldNames = $fieldNames;
    }

    /**
     * The naming of graphql.ini.
     *
     * @return self
     */
    public static function fromSettings()
    {
        $ini = expGraphQL::ini();
        $typeNames = $ini->hasVariable( 'GraphQLNaming', 'TypeNames' ) ? (array)$ini->variable( 'GraphQLNaming', 'TypeNames' ) : array();
        $fieldNames = $ini->hasVariable( 'GraphQLNaming', 'FieldNames' ) ? (array)$ini->variable( 'GraphQLNaming', 'FieldNames' ) : array();
        return new self( (string)expGraphQL::setting( 'NamingStyle', self::STYLE_IDENTIFIER ), $typeNames, $fieldNames );
    }

    /** @return string */
    public function style()
    {
        return $this->style;
    }

    /**
     * @param string $classIdentifier
     * @return string the type of the class
     */
    public function typeName( $classIdentifier )
    {
        $classIdentifier = (string)$classIdentifier;
        if ( $this->style === self::STYLE_MAP && isset( $this->typeNames[$classIdentifier] ) && $this->typeNames[$classIdentifier] !== '' )
            $name = self::valid( $this->typeNames[$classIdentifier] );
        else if ( $this->style === self::STYLE_PASCAL )
            $name = self::valid( self::pascal( $classIdentifier ) );
        else
            $name = self::valid( $classIdentifier );
        if ( in_array( $name, self::RESERVED_TYPES, true ) )
            $name .= 'Content';
        return $name;
    }

    /**
     * @param string $classIdentifier
     * @param string $attributeIdentifier
     * @return string the field of the attribute in the type of its class
     */
    public function fieldName( $classIdentifier, $attributeIdentifier )
    {
        $key = $classIdentifier . '/' . $attributeIdentifier;
        if ( $this->style === self::STYLE_MAP && isset( $this->fieldNames[$key] ) && $this->fieldNames[$key] !== '' )
            $name = self::valid( $this->fieldNames[$key] );
        else if ( $this->style === self::STYLE_PASCAL )
            $name = self::valid( lcfirst( self::pascal( $attributeIdentifier ) ) );
        else
            $name = self::valid( $attributeIdentifier );
        if ( in_array( $name, self::CONTENT_FIELDS, true ) )
            $name .= '_field';
        return $name;
    }

    /**
     * @param string $name
     * @return string the name with every character GraphQL does not allow replaced by _
     */
    public static function valid( $name )
    {
        $name = preg_replace( '/[^_0-9A-Za-z]/', '_', (string)$name );
        if ( $name === '' )
            return '_';
        if ( ctype_digit( $name[0] ) )
            $name = '_' . $name;
        // __ is reserved for GraphQL's introspection
        if ( strncmp( $name, '__', 2 ) === 0 )
            $name = '_' . ltrim( $name, '_' );
        return $name;
    }

    /**
     * @param string $identifier
     * @return string ng_article -> NgArticle
     */
    public static function pascal( $identifier )
    {
        $parts = preg_split( '/[^0-9A-Za-z]+/', (string)$identifier, -1, PREG_SPLIT_NO_EMPTY );
        $name = '';
        foreach ( $parts as $part )
            $name .= ucfirst( $part );
        return $name;
    }
}

?>
