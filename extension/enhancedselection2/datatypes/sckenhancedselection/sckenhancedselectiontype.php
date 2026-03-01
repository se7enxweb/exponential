<?php

class SckEnhancedSelectionType extends eZDataType
{
    const DATATYPE_STRING = 'sckenhancedselection';
    const CLASS_STORAGE_XML = 'data_text5';

    public function __construct()
    {
        parent::__construct(
            self::DATATYPE_STRING,
            ezpI18n::tr(
                'extension/enhancedselection2/datatypes',
                'Enhanced Selection 2',
                'Datatype name'
            )
        );
    }

    /**
     * Initializes the object attribute with some data.
     *
     * @param eZContentObjectAttribute $objectAttribute
     * @param int $currentVersion
     * @param eZContentObjectAttribute $originalContentObjectAttribute
     */
    public function initializeObjectAttribute( $objectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $content = $originalContentObjectAttribute->content();
            $objectAttribute->setContent( $content );
            $objectAttribute->store();
        }
    }

    /**
     * Validates the input for a class attribute and returns a validation state as defined in eZInputValidator.
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentClassAttribute $classAttribute
     *
     * @return int
     */
    public function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $id = $classAttribute->attribute( 'id' );
        $queryName = join( '_', array( $base, 'sckenhancedselection_query', $id ) );

        if ( $http->hasPostvariable( $queryName ) )
        {
            $query = trim( $http->postVariable( $queryName ) );

            if ( !empty( $query ) )
            {
                if ( $this->isDbQueryValid( $query ) !== true )
                {
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Fetches the HTTP input for the content class attribute.
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentClassAttribute $classAttribute
     *
     * @return bool
     */
    public function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $content = $classAttribute->content();
        $id = $classAttribute->attribute( 'id' );

        $idArrayName = join( '_', array( $base, 'sckenhancedselection_id', $id ) );
        $nameArrayName = join( '_', array( $base, 'sckenhancedselection_name', $id ) );
        $identifierArrayName = join( '_', array( $base, 'sckenhancedselection_identifier', $id ) );
        $priorityArrayName = join( '_', array( $base, 'sckenhancedselection_priority', $id ) );

        $multiSelectName = join( '_', array( $base, 'sckenhancedselection_multi', $id ) );
        $expandedName = join( '_', array( $base, 'sckenhancedselection_expanded', $id ) );
        $delimiterName = join( '_', array( $base, 'sckenhancedselection_delimiter', $id ) );

        $queryName = join( '_', array( $base, 'sckenhancedselection_query', $id ) );

        if ( $http->hasPostVariable( $idArrayName ) )
        {
            $idArray = $http->postVariable( $idArrayName );
            $nameArray = $http->postVariable( $nameArrayName );
            $identifierArray = $http->postVariable( $identifierArrayName );
            $priorityArray = $http->postVariable( $priorityArrayName );

            foreach ( $idArray as $index => $id )
            {
                $identifier = $identifierArray[$id];
                if ( empty( $identifier ) )
                {
                    $identifier = $this->generateIdentifier( $nameArray[$id], $identifierArray );
                }

                $content['options'][$index] = array(
                    'id' => $id,
                    'name' => $nameArray[$id],
                    'identifier' => $identifier,
                    'priority' => $priorityArray[$id]
                );
            }
        }

        if ( $http->hasPostVariable( $multiSelectName ) )
        {
            $content['is_multiselect'] = 1;
        }
        else if ( $http->hasPostVariable( 'ContentClassHasInput' ) )
        {
            $content['is_multiselect'] = 0;
        }

        if ( $http->hasPostVariable( $expandedName ) )
        {
            $content['is_expanded'] = 1;
        }
        else if ( $http->hasPostVariable( 'ContentClassHasInput' ) )
        {
            $content['is_expanded'] = 0;
        }

        if ( $http->hasPostVariable( $delimiterName ) )
        {
            $content['delimiter'] = $http->postVariable( $delimiterName );
        }

        if ( $http->hasPostVariable( $queryName ) )
        {
            $content['query'] = trim( $http->postVariable( $queryName ) );
        }

        $classAttribute->setContent( $content );
        $classAttribute->store();

        return true;
    }

    /**
     * Returns the content data for the given content class attribute.
     *
     * @param eZContentClassAttribute $classAttribute
     *
     * @return array
     */
    public function classAttributeContent( $classAttribute )
    {
        $xmlString = $classAttribute->attribute( self::CLASS_STORAGE_XML );
        $content = array();

        $this->xmlToClassContent( $xmlString, $content );

        $content['db_options'] = $this->getDbOptions( $content );

        $queryName = join( '_', array( 'ContentClass_sckenhancedselection_query', $classAttribute->attribute( 'id' ) ) );
        $http = eZHTTPTool::instance();

        if ( empty( $content['query'] ) && $http->hasPostVariable( $queryName ) )
        {
            $query = $http->postVariable( $queryName );
            $content['query'] = $query;
        }

        return $content;
    }

    /**
     * Stores the datatype data to the database which is related to the
     * class attribute. The $version parameter determines which version
     * is currently being stored, 0 is the real version while 1 is the
     * temporary version.
     *
     * @param eZContentClassAttribute $classAttribute
     * @param int $version
     */
    public function storeClassAttribute( $classAttribute, $version )
    {
        $content = $classAttribute->content();

        // Make sure this can never slip into the database
        unset( $content['db_options'] );

        $xmlString = $this->classContentToXml( $content );

        $classAttribute->setAttribute( self::CLASS_STORAGE_XML, $xmlString );
    }

    /**
     * Executes a custom action for a class attribute which was defined on the web page.
     *
     * @param eZHTTPTool $http
     * @param string $action
     * @param eZContentClassAttribute $classAttribute
     */
    public function customClassAttributeHTTPAction( $http, $action, $classAttribute )
    {
        $id = $classAttribute->attribute( 'id' );
        $base = "ContentClass";
        $content = $classAttribute->content();

        $customActionVarName = "CustomActionButton";
        $customActionKeyName = "{$id}_{$action}";

        $idArrayName = join( '_', array( $base, 'sckenhancedselection_id', $id ) );
        $idArray = array();

        if ( $http->hasPostVariable( $idArrayName ) )
        {
            $idArray = $http->postVariable( $idArrayName );
        }

        switch( $action )
        {
            case 'new_option':
                $maxID = 0;
                foreach ( $content['options'] as $option )
                {
                    if ( intval( $option['id'] ) > $maxID )
                    {
                        $maxID = intval( $option['id'] );
                    }
                }

                $maxID++;

                $content['options'][] = array(
                    'id' => $maxID,
                    'name' => '',
                    'identifier' => '',
                    'priority' => 1
                );

                break;

            case 'remove_optionlist':
                $removeArrayName = join( '_', array( $base, "sckenhancedselection_remove", $id ) );

                if ( $http->hasPostVariable( $removeArrayName ) )
                {
                    $removeArray = $http->postVariable( $removeArrayName );
                    foreach ( $removeArray as $removeID )
                    {
                        unset( $idArray[$removeID] );
                        unset( $content['options'][$removeID] );
                    }
                }

                break;

            case 'move_up':
                $customActionVar = $http->postVariable( $customActionVarName );

                // This is where the user clicked
                $customActionValue = $customActionVar[$customActionKeyName];

                // Up == swap selected row with the one above
                // Or: Move the row above below the selected one
                $this->swapRows( $customActionValue - 1, $customActionValue, $content, $idArray );

                break;

            case 'move_down':
                $customActionVar = $http->postVariable( $customActionVarName );

                // This is where the user clicked
                $customActionValue = $customActionVar[$customActionKeyName];

                // Down == swap selected row with the one below
                // Or: Move the selected row below the one below
                $this->swapRows( $customActionValue, $customActionValue + 1, $content, $idArray );

                break;

            case 'sort_optionlist':
                $sortName = join( '_', array( $base, 'sckenhancedselection_sort_order', $id ) );

                if ( $http->hasPostVariable( $sortName ) )
                {
                    $sort = $http->postVariable( $sortName );
                    $sortArray = array();
                    $sortOrder = SORT_ASC;
                    $sortType = SORT_STRING;
                    $numericSorts = array( 'prior' );

                    if ( strpos( $sort, '_' ) !== false )
                    {
                        list( $type, $ranking ) = explode( '_', $sort );
                        $currentOptions = $content['options'];

                        if ( $ranking === 'desc' )
                        {
                            $sortOrder = SORT_DESC;
                        }

                        if ( in_array( $type, $numericSorts ) )
                        {
                            $sortType = SORT_NUMERIC;
                        }

                        // Use POST priorities instead of the stored ones
                        // Otherwise you have to store new priorities before you can sort
                        $priorityArray = array();
                        if ( $type == 'prior' )
                        {
                            $priorityArray = $http->postVariable( join( '_', array( $base, 'sckenhancedselection_priority', $id ) ) );
                        }

                        foreach ( array_keys( $currentOptions ) as $key )
                        {
                            $option = $currentOptions[$key];

                            switch ( $type )
                            {
                                case 'prior':
                                    if ( isset( $priorityArray[$option['id']] ) )
                                    {
                                        $option['priority'] = $priorityArray[$option['id']];
                                    }
                                    $sortArray[] = $option['priority'];
                                    break;

                                case 'alpha':
                                default:
                                    $sortArray[] = $option['name'];
                                    break;
                            }

                            unset( $option );
                        }

                        array_multisort( $sortArray, $sortOrder, $sortType, $currentOptions );

                        $idArray = array();
                        foreach ( $currentOptions as $option )
                        {
                            $idArray[] = $option['id'];
                        }

                        $content['options'] = $currentOptions;
                    }
                    else
                    {
                        eZDebug::writeError( "Unknown sort value. Please use the form type_order (ex. alpha_asc)", "SckEnhancedSelectionType" );
                    }
                }

                break;

            default:
                eZDebug::writeError( "Unknown class HTTP action: $action", "SckEnhancedSelectionType" );
        }

        $classAttribute->setContent( $content );
        $classAttribute->store();

        $http->setPostVariable( $idArrayName, $idArray );
    }

    /**
     * Validates the input for an object attribute and returns a validation state as defined in eZInputValidator.
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $objectAttribute
     *
     * @return int
     */
    public function validateObjectAttributeHTTPInput( $http, $base, $objectAttribute )
    {
        return $this->validateAttributeHTTPInput( $http, $base, $objectAttribute, false );
    }

    /**
     * Fetches the HTTP input for the content object attribute.
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $objectAttribute
     *
     * @return bool
     */
    public function fetchObjectAttributeHTTPInput( $http, $base, $objectAttribute )
    {
        $id = $objectAttribute->attribute( 'id' );
        $classContent = $objectAttribute->classContent();
        $content = $objectAttribute->content();

        $selectionName = join( '_', array( $base, 'sckenhancedselection_selection', $id ) );

        if ( $http->hasPostVariable( $selectionName ) )
        {
            $content = $http->postVariable( $selectionName );
        }
        else if ( $classContent['is_multiselect'] == 1 )
        {
            $content = array();
        }

        $objectAttribute->setContent( $content );

        return true;
    }

    /**
     * Returns the content data for the given content object attribute.
     *
     * @param eZContentObjectAttribute $objectAttribute
     *
     * @return array
     */
    public function objectAttributeContent( $objectAttribute )
    {
        $identifiers = SckEnhancedSelection::fetchByAttribute(
            $objectAttribute->attribute( 'id' ),
            $objectAttribute->attribute( 'version' )
        );

        $stringIdentifiers = array();

        foreach ( $identifiers as $identifier )
        {
            $stringIdentifiers[] = $identifier->attribute( 'identifier' );
        }

        return $stringIdentifiers;
    }

    /**
     * Returns if data type finds any content in the attribute
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     *
     * @return bool
     */
    public function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $count = SckEnhancedSelection::countByAttribute(
            $contentObjectAttribute->attribute( 'id' ),
            $contentObjectAttribute->attribute( 'version' )
        );

        return $count > 0;
    }

    /**
     * Stores the datatype data to the database which is related to the object attribute.
     *
     * @param eZContentObjectAttribute $objectAttribute
     */
    public function storeObjectAttribute( $objectAttribute )
    {
        $content = $objectAttribute->content();

        SckEnhancedSelection::removeByAttribute(
            $objectAttribute->attribute( 'id' ),
            $objectAttribute->attribute( 'version' )
        );

        if ( !is_array( $content ) )
        {
            $content = array();
        }

        foreach ( $content as $identifier )
        {
            $sckEnhancedSelection = new SckEnhancedSelection(
                array(
                    'contentobject_attribute_id' => $objectAttribute->attribute( 'id' ),
                    'contentobject_attribute_version' => $objectAttribute->attribute( 'version' ),
                    'identifier' => $identifier
                )
            );

            $sckEnhancedSelection->store();
        }
    }

    /**
     * Deletes $objectAttribute datatype data, optionally in version $version.
     *
     * @param eZContentObjectAttribute $objectAttribute
     * @param int $version
     */
    public function deleteStoredObjectAttribute( $objectAttribute, $version = null )
    {
        SckEnhancedSelection::removeByAttribute(
            $objectAttribute->attribute( 'id' ),
            $version
        );
    }

    /**
     * Validates the input for an object attribute and returns a validation state as defined in eZInputValidator.
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $objectAttribute
     *
     * @return int
     */
    public function validateCollectionAttributeHTTPInput( $http, $base, $objectAttribute )
    {
        return $this->validateAttributeHTTPInput( $http, $base, $objectAttribute, true );
    }

    /**
     * Fetches the HTTP collected information for the content object attribute.
     *
     * @param eZInformationCollection $collection
     * @param eZInformationCollectionAttribute $collectionAttribute
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $objectAttribute
     *
     * @return bool
     */
    public function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $objectAttribute )
    {
        $id = $objectAttribute->attribute( 'id' );
        $classContent = $objectAttribute->classContent();
        $nameArray = array();

        $selectionName = join( '_', array( $base, 'sckenhancedselection_selection', $id ) );
        if ( $http->hasPostVariable( $selectionName ) )
        {
            $selection = $http->postVariable( $selectionName );

            if ( count( $selection ) > 0 )
            {
                $options = $classContent['options'];
                if ( isset( $classContent['db_options'] ) && count( $classContent['db_options'] ) > 0 )
                {
                    $options = $classContent['db_options'];
                }

                foreach ( $options as $option )
                {
                    if ( in_array( $option['identifier'], $selection ) )
                    {
                        $nameArray[] = $option['name'];
                    }
                }

                unset( $options );
            }
        }

        $delimiter = $classContent['delimiter'];

        if ( empty( $delimiter ) )
        {
            $delimiter = ', ';
        }

        $dataText = join( $delimiter, $nameArray );

        $collectionAttribute->setAttribute( 'data_text', $dataText );

        return true;
    }

    /**
     * Returns if the data type can do information collection
     *
     * @return bool
     */
    public function hasInformationCollection()
    {
        return false;
    }

    /**
     * Returns the text which should be indexed in the search engine.
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     *
     * @return string
     */
    public function metaData( $contentObjectAttribute )
    {
        $content = $contentObjectAttribute->content();
        $classContent = $contentObjectAttribute->classContent();

        if ( count( $content ) > 0 )
        {
            $metaDataArray = array();
            $options = $classContent['options'];

            if ( isset( $classContent['db_options'] ) && count( $classContent['db_options'] ) > 0 )
            {
                $options = $classContent['db_options'];
            }

            foreach ( $options as $option )
            {
                if ( in_array( $option['identifier'], $content ) )
                {
                    $metaDataArray[] = array(
                        'id' => '',
                        'text' => $option['identifier']
                    );

                    $metaDataArray[] = array(
                        'id' => '',
                        'text' => $option['name']
                    );
                }
            }

            unset( $options );

            return $metaDataArray;
        }

        return "";
    }

    /**
     * Returns the title of the current type, this is to form the title of the object.
     *
     * @param eZContentObjectAttribute $objectAttribute
     * @param string $name
     *
     * @return string
     */
    public function title( $objectAttribute, $name = null )
    {
        $content = $objectAttribute->content();
        $classContent = $objectAttribute->classContent();
        $titleArray = array();
        $titleString = "";

        if ( count( $content ) > 0 )
        {
            $options = $classContent['options'];

            if ( isset( $classContent['db_options'] ) && count( $classContent['db_options'] ) > 0 )
            {
                $options = $classContent['db_options'];
            }

            foreach ( $options as $option )
            {
                if ( in_array( $option['identifier'], $content ) )
                {
                    $titleArray[] = $option['name'];
                }
            }

            unset( $options );
        }

        if ( count( $titleArray ) > 0 )
        {
            $delimiter = $classContent['delimiter'];
            if ( empty( $delimiter ) )
            {
                $delimiter = ", ";
            }

            $titleString = join( $delimiter, $titleArray );
        }

        return $titleString;
    }

    /**
     * Returns if the datatype can be indexed
     *
     * @return bool
     */
    public function isIndexable()
    {
        return true;
    }

    /**
     * Returns if the datatype can be used as an information collector
     *
     * @return bool
     */
    public function isInformationCollector()
    {
        return true;
    }

    /**
     * Returns the sort key for the datatype. This is used for sorting on attribute level.
     *
     * @param eZContentObjectAttribute $objectAttribute
     *
     * @return string
     */
    public function sortKey( $objectAttribute )
    {
        $content = $objectAttribute->content();
        $contentString = join( ' ', $content );
        $contentString = strtolower( $contentString );

        return $contentString;
    }

    /**
     * Returns the type of sort key
     *
     * @return string
     */
    public function sortKeyType()
    {
        return 'string';
    }

    /**
     * Adds the necessary dom structure to the attribute parameters.
     *
     * @param eZContentObjectAttribute $classAttribute
     * @param DOMElement $attributeNode
     * @param DOMElement $attributeParametersNode
     */
    public function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $content = $classAttribute->content();

        $dom = $attributeParametersNode->ownerDocument;
        $optionsNode = $dom->createElement( 'options' );

        if ( is_array( $content['options'] ) && count( $content['options'] ) > 0 )
        {
            foreach ( $content['options'] as $option )
            {
                $optionNode = $dom->createElement( 'option' );

                $optionNode->setAttribute( 'id', $option['id']  );
                $optionNode->setAttribute( 'name', $option['name'] );
                $optionNode->setAttribute( 'identifier', $option['identifier'] );
                $optionNode->setAttribute( 'priority', $option['priority']  );

                $optionsNode->appendChild( $optionNode );

                unset( $optionNode );
            }
        }

        $delimiterElement = $dom->createElement( 'delimiter' );
        $delimiterElement->appendChild( $dom->createCDATASection( $content['delimiter'] ) );
        $attributeParametersNode->appendChild( $delimiterElement );

        $attributeParametersNode->appendChild( $dom->createElement( 'multiselect', $content['is_multiselect'] ) );
        $attributeParametersNode->appendChild( $dom->createElement( 'expanded', $content['is_expanded'] ) );

        $queryElement = $dom->createElement( 'query' );
        $queryElement->appendChild( $dom->createCDATASection( $content['query'] ) );
        $attributeParametersNode->appendChild( $queryElement );

        $attributeParametersNode->appendChild( $optionsNode );

        unset( $optionsNode );
    }

    /**
     * Extracts values from the attribute parameters and sets it in the class attribute.
     *
     * @param eZContentObjectAttribute $classAttribute
     * @param DOMElement $attributeNode
     * @param DOMElement $attributeParametersNode
     */
    public function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $content = array();

        $delimiter = $attributeParametersNode->getElementsByTagName( 'delimiter' )->item( 0 )->nodeValue;
        $multiselect = $attributeParametersNode->getElementsByTagName( 'multiselect' )->item( 0 )->textContent;
        $expanded = $attributeParametersNode->getElementsByTagName( 'expanded' )->item( 0 )->textContent;
        $query = $attributeParametersNode->getElementsByTagName( 'query' )->item( 0 )->nodeValue;

        $content['delimiter'] = $delimiter !== false ? $delimiter : '';
        $content['is_multiselect'] = $multiselect !== false ? intval( $multiselect ) : 0;
        $content['is_expanded'] = $expanded !== false ? intval( $expanded ) : 0;
        $content['query'] = $query !== false ? $query : '';
        $content['options'] = array();

        $optionsNode = $attributeParametersNode->getElementsByTagName( 'options' )->item( 0 );

        if ( $optionsNode instanceof DOMElement && $optionsNode->hasChildNodes() )
        {
            $children = $optionsNode->childNodes;
            foreach ( $children as $key => $child )
            {
                if ( $child instanceof DOMElement )
                {
                    $content['options'][] = array(
                        'id' => $child->getAttribute( 'id' ),
                        'name' => $child->getAttribute( 'name' ),
                        'identifier' => $child->getAttribute( 'identifier' ),
                        'priority' => $child->getAttribute( 'priority' )
                    );
                }
            }
        }

        unset( $optionsNode );

        $xmlString = $this->classContentToXml( $content );

        $classAttribute->setAttribute( self::CLASS_STORAGE_XML, $xmlString );
    }

    /**
     * Converts string representation of content object attribute data to proper value
     *
     * @param eZContentObjectAttribute $objectAttribute
     * @param $string
     */
    public function fromString( $objectAttribute, $string )
    {
        $content = unserialize( $string );
        if ( $content === false )
        {
            if ( !empty( $string ) && is_string( $string ) )
            {
                $content = array( $string );
            }
        }

        $objectAttribute->setContent( $content );
    }

    /**
     * Returns string representation of a content object attribute data for simplified export
     *
     * @param eZContentObjectAttribute $objectAttribute
     *
     * @return string
     */
    public function toString( $objectAttribute )
    {
        $content = $objectAttribute->content();
        if ( count( $content ) == 1 )
        {
            return $content[0];
        }

        return serialize( $content );
    }

/**********
* HELPERS *
**********/

    /**
     * Converts class attribute content to XML
     *
     * @param array $content
     *
     * @return string
     */
    protected function classContentToXml( $content )
    {
        $doc = new DOMDocument();
        $root = $doc->createElement( 'content' );

        $optionsNode = $doc->createElement( 'options' );

        if ( isset( $content['options'] ) && count( $content['options'] ) > 0 )
        {
            foreach ( $content['options'] as $option )
            {
                $optionNode = $doc->createElement( 'option' );

                $optionNode->setAttribute( 'id', $option['id'] );
                $optionNode->setAttribute( 'name', $option['name'] );
                $optionNode->setAttribute( 'identifier', $option['identifier'] );
                $optionNode->setAttribute( 'priority', $option['priority'] );

                $optionsNode->appendChild( $optionNode );

                unset( $optionNode );
            }
        }

        $root->appendChild( $optionsNode );

        // Multiselect
        if ( isset( $content['is_multiselect'] ) )
        {
            $multiSelectNode = $doc->createElement( 'multiselect', $content['is_multiselect'] );
            $root->appendChild( $multiSelectNode );
        }

        // Expanded
        if ( isset( $content['is_expanded'] ) )
        {
            $expandedNode = $doc->createElement( 'expanded', $content['is_expanded'] );
            $root->appendChild( $expandedNode );
        }

        // Delimiter
        if ( isset( $content['delimiter'] ) )
        {
            $delimiterElement = $doc->createElement( 'delimiter' );
            $delimiterElement->appendChild( $doc->createCDATASection( $content['delimiter'] ) );
            $root->appendChild( $delimiterElement );
        }

        // DB Query
        if ( isset( $content['query'] ) )
        {
            $queryElement = $doc->createElement( 'query' );
            $queryElement->appendChild( $doc->createCDATASection( $content['query'] ) );
            $root->appendChild( $queryElement );
        }

        $doc->appendChild( $root );

        return $doc->saveXML();
    }

    /**
     * Converts XML string to class attribute content
     *
     * @param string $xmlString
     * @param array $content
     */
    protected function xmlToClassContent( $xmlString, &$content )
    {
        if ( !empty( $xmlString ) )
        {
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;

            if ( $dom->loadXML( $xmlString ) )
            {
                $optionsNode = $dom->getElementsByTagName( 'options' )->item( 0 );
                $content['options'] = array();

                if ( $optionsNode instanceof DOMElement && $optionsNode->hasChildNodes() )
                {
                    $children = $optionsNode->childNodes;
                    foreach ( $children as $child )
                    {
                        /** @var DOMElement $child */
                        $content['options'][] = array(
                            'id' => $child->getAttribute( 'id' ),
                            'name' => $child->getAttribute( 'name' ),
                            'identifier' => $child->getAttribute( 'identifier' ),
                            'priority' => $child->getAttribute( 'priority' )
                        );
                    }
                }

                $multiSelectNode = $dom->getElementsByTagName( 'multiselect' )->item( 0 );
                $content['is_multiselect'] = 0;

                if ( $multiSelectNode instanceof DOMElement )
                {
                    $content['is_multiselect'] = intval( $multiSelectNode->textContent );
                }

                $expandedNode = $dom->getElementsByTagName( 'expanded' )->item( 0 );
                $content['is_expanded'] = 0;

                if ( $expandedNode instanceof DOMElement )
                {
                    $content['is_expanded'] = intval( $expandedNode->textContent );
                }

                $delimiterNode = $dom->getElementsByTagName( 'delimiter' )->item( 0 );
                $content['delimiter'] = '';

                if ( $delimiterNode instanceof DOMElement )
                {
                    $content['delimiter'] = $delimiterNode->nodeValue;
                }

                $queryNode = $dom->getElementsByTagName( 'query' )->item( 0 );
                $content['query'] = '';

                if ( $queryNode instanceof DOMElement )
                {
                    $content['query'] = trim( $queryNode->nodeValue );
                }
            }
            else
            {
                $content['options'] = array();
                $content['is_multiselect'] = 0;
                $content['is_expanded'] = 0;
                $content['delimiter'] = '';
                $content['query'] = '';
            }
        }
    }

    /**
     * Generates an identifier from provided name
     *
     * @param string $name
     * @param array $identifierArray
     *
     * @return string
     */
    protected function generateIdentifier( $name, $identifierArray = array() )
    {
        if ( empty( $name ) )
        {
            return '';
        }

        $identifier = $name;
        $generatedIdentifier = eZCharTransform::instance()->transformByGroup( $identifier, 'identifier' );

        // We have $generatedIdentifier now, check for existence
        if ( is_array( $identifierArray ) && !empty( $identifierArray ) && in_array( $generatedIdentifier, $identifierArray ) )
        {
            $highestNumber = 0;
            foreach ( $identifierArray as $identifierItem )
            {
                if ( preg_match( '/^' . $generatedIdentifier . '__(\d+)$/', $identifierItem, $matchArray ) )
                {
                    if ( $matchArray[1] > $highestNumber )
                    {
                        $highestNumber = $matchArray[1];
                    }
                }
            }

            $generatedIdentifier .= "__" . ++$highestNumber;
        }

        return $generatedIdentifier;
    }

    /**
     * Checks if a custom action ( combination of $attributeId and $action ) has fired
     *
     * @param int $attributeId The attribute ID used to make the custom action unique (class or object level)
     * @param eZHTTPTool $http Instance of the eZHTTPTool class
     * @param bool $action The name of the action if you want to check for a specific action
     *
     * @return bool
     */
    protected function hasCustomAction( $attributeId, $http, $action = false )
    {
        if ( $http->hasPostVariable( 'CustomActionButton' ) )
        {
            $keys = array_keys( $http->postVariable( 'CustomActionButton' ) );

            if ( $action !== false )
            {
                $attributeId .= "_$action";
            }

            foreach ( $keys as $key )
            {
                // Begins with the $attributeId
                if ( strpos( $key, "$attributeId" ) === 0 )
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Swaps rows in content class attribute
     *
     * @param int $highest
     * @param int $lowest
     * @param array $content
     * @param array $postVar
     */
    protected function swapRows( $highest, $lowest, &$content, &$postVar )
    {
        if ( isset( $content['options'][$highest] ) && isset( $content['options'][$lowest] ) )
        {
            // Ok to proceed
            $tmp = $content['options'][$highest];
            $content['options'][$highest] = $content['options'][$lowest];
            $content['options'][$lowest] = $tmp;

            // Make sure the post var follows
            $tmp = $postVar[$highest];
            $postVar[$highest] = $postVar[$lowest];
            $postVar[$lowest] = $tmp;
        }
    }

    /**
     * Checks if DB query is valid
     *
     * @param string $sql
     *
     * @return bool
     */
    protected function isDbQueryValid( $sql )
    {
        $db = eZDB::instance();
        $result = $db->arrayQuery( $sql, array( 'limit' => 1 ) );

        if ( is_array( $result ) && count( $result ) == 1 )
        {
            if ( isset( $result[0]['name'] ) && isset( $result[0]['identifier'] ) )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the list of names and identifiers from the database
     *
     * @param array $classContent
     *
     * @return array
     */
    protected function getDbOptions( $classContent )
    {
        if ( isset( $classContent['query'] ) && !empty( $classContent['query'] ) && $this->isDbQueryValid( $classContent['query'] ) )
        {
            $db = eZDB::instance();
            $result = $db->arrayQuery( $classContent['query'] );

            if ( is_array( $result ) && count( $result ) > 0 )
            {
                if ( $classContent['is_multiselect'] == 0 )
                {
                    return array_merge( array( array( 'name' => '', 'identifier' => '' ) ), $result );
                }
                else
                {
                    return $result;
                }
            }
        }

        return array();
    }

    /**
     * Validates content object attribute HTTP input
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param bool $isInformationCollection
     *
     * @return int
     */
    protected function validateAttributeHTTPInput( $http, $base, $contentObjectAttribute, $isInformationCollection = false )
    {
        /** @var eZContentClassAttribute $classAttribute */
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        $classContent = $classAttribute->content();
        $infoCollectionCheck = ( $isInformationCollection == $classAttribute->attribute( 'is_information_collector' ) );

        $isRequired = $contentObjectAttribute->validateIsRequired();

        $selectionName = join( '_', array( $base, 'sckenhancedselection_selection', $contentObjectAttribute->attribute( 'id' ) ) );

        if ( $http->hasPostVariable( $selectionName ) )
        {
            $selection = $http->postVariable( $selectionName );

            if ( $infoCollectionCheck )
            {
                switch ( true )
                {
                    case $isRequired === true && count( $selection ) == 0:
                    case $isRequired === true && count( $selection ) == 1 && empty( $selection[0] ):
                        $contentObjectAttribute->setValidationError(
                            ezpI18n::tr(
                                'extension/enhancedselection2/datatypes',
                                'This is a required field.'
                            )
                        );

                        return eZInputValidator::STATE_INVALID;
                }
            }
        }
        else
        {
            if ( $infoCollectionCheck && $isRequired && $classContent['is_multiselect'] == 1 )
            {
                $contentObjectAttribute->setValidationError(
                    ezpI18n::tr(
                        'extension/enhancedselection2/datatypes',
                        'This is a required field.'
                    )
                );
            }
            else if ( $infoCollectionCheck && $isRequired )
            {
                $contentObjectAttribute->setValidationError(
                    ezpI18n::tr(
                        'extension/enhancedselection2/datatypes',
                        'No POST variable. Please check your configuration.'
                    )
                );
            }
            else
            {
                return eZInputValidator::STATE_ACCEPTED;
            }

            return eZInputValidator::STATE_INVALID;
        }

        return eZInputValidator::STATE_ACCEPTED;
    }
}

eZDataType::register( SckEnhancedSelectionType::DATATYPE_STRING, "SckEnhancedSelectionType" );
