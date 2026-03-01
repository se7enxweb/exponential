<?php

class ezfSolrDocumentFieldxrowMetadata extends ezfSolrDocumentFieldBase
{
    /**
     * Contains the definition of subattributes for this given datatype.
     * This associative array takes as key the name of the field, and as value
     * the type. The type must be picked amongst the value present as keys in the
     * following array :
     * ezfSolrDocumentFieldName::$FieldTypeMap
     *
     * WARNING : this definition *must* contain the default attribute's one as well.
     *
     * @see ezfSolrDocumentFieldName::$FieldTypeMap
     * @var array
     */
    public static $subattributesDefinition = array(
        self::DEFAULT_SUBATTRIBUTE => 'text',
        'description' => 'text',
        'title' => 'text',
        'sitemap_use' => 'text'
    );

    /**
     * The name of the default subattribute. It will be used when
     * this field is requested with no subfield refinement.
     *
     * @see ezfSolrDocumentFieldDummyExample::$subattributesDefinition
     * @var string
     */
    const DEFAULT_SUBATTRIBUTE = 'metainfo';

    /**
     * @see ezfSolrDocumentFieldBase::__construct()
     */
    function __construct( eZContentObjectAttribute $attribute )
    {
        parent::__construct( $attribute );
    }

    /**
     * @see ezfSolrDocumentFieldBase::getData()
     */
    public function getData()
    {
        // @TODO : Extract data from the attribute, and format it as described in the doc link above.
        //         Dummy content here, for testing purposes.
        $data = array();
        $contentClassAttribute = $this->ContentObjectAttribute->attribute( 'contentclass_attribute' );
        if($this->ContentObjectAttribute->attribute( 'has_content' ))
        {
            $data[self::getFieldName( $contentClassAttribute, 'title' )] = $this->ContentObjectAttribute->attribute( 'content' )->attribute( 'title' );
            $data[self::getFieldName( $contentClassAttribute, 'description' )] = $this->ContentObjectAttribute->attribute( 'content' )->attribute( 'description' );
            $data[self::getFieldName( $contentClassAttribute, 'sitemap_use' )] = $this->ContentObjectAttribute->attribute( 'content' )->attribute( 'sitemap_use' );
            $data[self::getFieldName( $contentClassAttribute, self::DEFAULT_SUBATTRIBUTE )] = $this->ContentObjectAttribute->attribute( 'content' )->attribute( 'keywords' );
        }
        return $data;
    }

    /**
     * @see ezfSolrDocumentFieldBase::getFieldName()
     */
    public static function getFieldName( eZContentClassAttribute $classAttribute, $subAttribute = null, $context = 'search' )
    {
        // article/location/ longitude
        if ( $subAttribute and $subAttribute !== '' and array_key_exists( $subAttribute, self::$subattributesDefinition ) and $subAttribute != self::DEFAULT_SUBATTRIBUTE )
        {
            // A subattribute was passed
            return parent::generateSubattributeFieldName( $classAttribute, $subAttribute, self::$subattributesDefinition[$subAttribute] );
        }
        else
        {
            // return the default field name here.
            return parent::generateAttributeFieldName( $classAttribute, self::$subattributesDefinition[self::DEFAULT_SUBATTRIBUTE] );
        }
    }

    /**
     * @see ezfSolrDocumentFieldBase::getFieldNameList()
     */
    public static function getFieldNameList( eZContentClassAttribute $classAttribute, $exclusiveTypeFilter = array() )
    {
        // Generate the list of subfield names.
        $subfields = array();

        //   Handle first the default subattribute
        $subattributesDefinition = self::$subattributesDefinition;
        if ( ! in_array( $subattributesDefinition[self::DEFAULT_SUBATTRIBUTE], $exclusiveTypeFilter ) )
        {
            $subfields[] = parent::generateAttributeFieldName( $classAttribute, $subattributesDefinition[self::DEFAULT_SUBATTRIBUTE] );
        }
        unset( $subattributesDefinition[self::DEFAULT_SUBATTRIBUTE] );

        //   Then hanlde all other subattributes
        foreach ( $subattributesDefinition as $name => $type )
        {
            if ( empty( $exclusiveTypeFilter ) or ! in_array( $type, $exclusiveTypeFilter ) )
            {
                $subfields[] = parent::generateSubattributeFieldName( $classAttribute, $name, $type );
            }
        }
        return $subfields;
    }

    /**
     * @see ezfSolrDocumentFieldBase::getClassAttributeType()
     */
    static function getClassAttributeType( eZContentClassAttribute $classAttribute, $subAttribute = null, $context = 'search' )
    {
        if ( $subAttribute and $subAttribute !== '' and array_key_exists( $subAttribute, self::$subattributesDefinition ) )
        {
            // If a subattribute's type is being explicitly requested :
            return self::$subattributesDefinition[$subAttribute];
        }
        else
        {
            // If no subattribute is passed, return the default subattribute's type :
            return self::$subattributesDefinition[self::DEFAULT_SUBATTRIBUTE];
        }
    }
}
