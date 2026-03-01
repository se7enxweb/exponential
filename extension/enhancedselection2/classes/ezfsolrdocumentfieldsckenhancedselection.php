<?php
class ezfSolrDocumentFieldSckEnhancedSelection extends ezfSolrDocumentFieldBase
{
    protected static $subattributesDefinition = array(
        self::DEFAULT_SUBATTRIBUTE => 'lckeyword',
        'identifiers' => 'lckeyword',
        'values' => 'text'
    );

    /**
     * The name of the default subattribute. It will be used when
     * this field is requested with no subfield refinement.
     *
     * @see ezfSolrDocumentFieldDummyExample::$subattributesDefinition
     * @var string
     */
    const DEFAULT_SUBATTRIBUTE = 'options';

    /**
     * Get data to index, and field name to use.
     *
     * @return array Associative array with field name and field value.
     *               Field value can be an array.
     */
    public function getData()
    {
        $data = array();

        $contentClassAttribute = $this->ContentObjectAttribute->attribute( 'contentclass_attribute' );

        if ( $this->ContentObjectAttribute->attribute( 'has_content' ) )
        {
            $xmlString = $contentClassAttribute->attribute( 'data_text5' );
            $options = array();
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->loadXML( $xmlString );

            if ( $dom )
            {
                $optionsNode = $dom->getElementsByTagName( 'options' )->item( 0 );
                $options['options'] = array();

                if ( $optionsNode instanceof DOMElement && $optionsNode->hasChildNodes() == true )
                {
                    $children = $optionsNode->childNodes;
                    foreach ( $children as $child )
                    {
                        /** @var \DOMElement $child */
                        $options['options'][$child->getAttribute( 'identifier' )] = $child->getAttribute( 'name' );
                    }
                }
            }

            $content = $this->ContentObjectAttribute->content();

            $selectedValues = array();
            foreach ( $content as $identifier )
            {
                $selectedValues[$identifier] = $options['options'][$identifier];
            }

            $data[self::getFieldName( $contentClassAttribute )] = implode(
                ', ',
                $this->ContentObjectAttribute->content()
            );

            $data[self::getFieldName( $contentClassAttribute, 'identifiers' )] = array_keys(
                $selectedValues
            );

            $data[self::getFieldName( $contentClassAttribute, 'values' )] = array_values(
                $selectedValues
            );
        }

        return $data;
    }

    /**
     * Get field name
     *
     * @param eZContentClassAttribute $classAttribute Instance of eZContentClassAttribute
     * @param mixed $subAttribute Typically the 'subattribute' name
     * @param string $context
     *
     * @return string Fully qualified Solr field name
     */
    public static function getFieldName( eZContentClassAttribute $classAttribute, $subAttribute = null, $context = 'search' )
    {
        if ( !empty( $subAttribute ) && array_key_exists( $subAttribute, self::$subattributesDefinition ) && $subAttribute !== self::DEFAULT_SUBATTRIBUTE )
        {
            // A subattribute was passed
            return self::generateSubattributeFieldName(
                $classAttribute,
                $subAttribute,
                self::$subattributesDefinition[$subAttribute]
            );
        }
        else
        {
            // return the default field name here.
            return parent::generateAttributeFieldName(
                $classAttribute,
                self::$subattributesDefinition[self::DEFAULT_SUBATTRIBUTE]
            );
        }
    }

    /**
     * Gets the list of solr fields for the given content class attribute. Delegates
     * the action to the datatype-specific handler, if any. If none, the datatype has one
     * field only, hence the delegation to the local getFieldName.
     *
     * @param eZContentClassAttribute $classAttribute
     * @param array $exclusiveTypeFilter Array of types ( strings ) which should be excluded from the result
     *
     * @return array Array of applicable solr field names
     */
    public static function getFieldNameList( eZContentClassAttribute $classAttribute, $exclusiveTypeFilter = array() )
    {
        // Generate the list of subfield names.
        $subfields = array();

        //   Handle first the default subattribute
        $subattributesDefinition = self::$subattributesDefinition;
        if ( !in_array( $subattributesDefinition[self::DEFAULT_SUBATTRIBUTE], $exclusiveTypeFilter ) )
        {
            $subfields[] = parent::generateAttributeFieldName(
                $classAttribute,
                $subattributesDefinition[self::DEFAULT_SUBATTRIBUTE]
            );
        }

        unset( $subattributesDefinition[self::DEFAULT_SUBATTRIBUTE] );

        //   Then handle all other subattributes
        foreach ( $subattributesDefinition as $name => $type )
        {
            if ( empty( $exclusiveTypeFilter ) || !in_array( $type, $exclusiveTypeFilter ) )
            {
                $subfields[] = self::generateSubattributeFieldName( $classAttribute, $name, $type );
            }
        }

        return $subfields;
    }

    /**
     * Get Solr schema field type from eZContentClassAttribute
     *
     * @param eZContentClassAttribute $classAttribute Instance of eZContentClassAttribute.
     * @param string $subAttribute In case the type of a datatype's sub-attribute is requested,
     *                             the subattribute's name is passed here.
     * @param string $context
     *
     * @return string Field type. Null if no field type is defined.
     */
    static function getClassAttributeType( eZContentClassAttribute $classAttribute, $subAttribute = null, $context = 'search' )
    {
        if ( !empty( $subAttribute ) && array_key_exists( $subAttribute, self::$subattributesDefinition ) )
        {
            // If a subattribute's type is being explicitly requested
            return self::$subattributesDefinition[$subAttribute];
        }
        else
        {
            // If no subattribute is passed, return the default subattribute's type
            return self::$subattributesDefinition[self::DEFAULT_SUBATTRIBUTE];
        }
    }
}
