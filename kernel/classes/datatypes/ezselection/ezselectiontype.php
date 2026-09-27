<?php
/**
 * File containing the eZSelectionType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class   ezselectiontype ezselectiontype.php
  \ingroup eZDatatype
  \brief   Handles the single and multiple selections.
  \date    Wednesday 23 July 2003 12:48:45 pm
  \author  B�rd Farstad

*/

class eZSelectionType extends eZDataType
{
    const DATA_TYPE_STRING = "ezselection";

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Selection", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    /*!
     Validates all variables given on content class level
     \return eZInputValidator::STATE_ACCEPTED or eZInputValidator::STATE_INVALID if
             the values are accepted or not
    */
    function validateClassAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches all variables inputed on content class level
     \return true if fetching of class attributes are successfull, false if not
    */
    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $attributeContent = $this->classAttributeContent( $classAttribute );
        $classAttributeID = $classAttribute->attribute( 'id' );
        $isMultipleSelection = false;

        if ( $http->hasPostVariable( $base . "_ezselection_ismultiple_value_" . $classAttributeID ) )
        {
            if( $http->postVariable( $base . "_ezselection_ismultiple_value_" . $classAttributeID ) != 0 )
            {
                $isMultipleSelection = true;
            }
        }

        $currentOptions = $attributeContent['options'];
        $hasPostData = false;

        if ( $http->hasPostVariable( $base . "_ezselection_option_name_array_" . $classAttributeID ) )
        {
            $nameArray = $http->postVariable( $base . "_ezselection_option_name_array_" . $classAttributeID );
            if ( !is_array( $nameArray ) )
                $nameArray = array();

            // Fill in new names for options. An option the form did not post a name
            // for (a new option added in another window, a hand-made request) keeps
            // its name, and a name must be text: DOMElement::setAttribute() refuses
            // an array with a TypeError
            foreach ( array_keys( $currentOptions ) as $key )
            {
                $name = $nameArray[$currentOptions[$key]['id']] ?? null;
                if ( is_scalar( $name ) )
                    $currentOptions[$key]['name'] = (string)$name;
            }
            $hasPostData = true;

        }

        if ( $http->hasPostVariable( $base . "_ezselection_newoption_button_" . $classAttributeID ) )
        {
            $currentCount = 0;
            foreach ( $currentOptions as $option )
            {
                $currentCount = max( $currentCount, $option['id'] );
            }
            $currentCount += 1;
            $currentOptions[] = array( 'id' => $currentCount,
                                       'name' => '' );
            $hasPostData = true;

        }

        if ( $http->hasPostVariable( $base . "_ezselection_removeoption_button_" . $classAttributeID ) )
        {
            if ( $http->hasPostVariable( $base . "_ezselection_option_remove_array_". $classAttributeID ) )
            {
                $removeArray = $http->postVariable( $base . "_ezselection_option_remove_array_". $classAttributeID );
                // A string here would have its characters read as option ids
                if ( !is_array( $removeArray ) )
                    $removeArray = array();

                foreach ( array_keys( $currentOptions ) as $key )
                {
                    if ( isset( $removeArray[$currentOptions[$key]['id']] ) and
                         $removeArray[$currentOptions[$key]['id']] )
                        unset( $currentOptions[$key] );
                }
                $hasPostData = true;
            }
        }

        if ( $hasPostData )
        {

            // Serialize XML
            $doc = new DOMDocument( '1.0', 'utf-8' );
            $root = $doc->createElement( "ezselection" );
            $doc->appendChild( $root );

            $options = $doc->createElement( "options" );

            $root->appendChild( $options );
            foreach ( $currentOptions as $optionArray )
            {
                unset( $optionNode );
                $optionNode = $doc->createElement( "option" );
                $optionNode->setAttribute( 'id', $optionArray['id'] );
                $optionNode->setAttribute( 'name', $optionArray['name'] );

                $options->appendChild( $optionNode );
            }

            $xml = $doc->saveXML();

            $classAttribute->setAttribute( "data_text5", $xml );

            if ( $isMultipleSelection == true )
                $classAttribute->setAttribute( "data_int1", 1 );
            else
                $classAttribute->setAttribute( "data_int1", 0 );
        }
        return true;
    }
    /*!
     \private
     \return the option ids in \a $posted (the posted _ezselect_selected_array_)
     that are options of \a $classAttribute, in the posted order, once each, and
     only the first for a single selection. The ids are stored joined by '-', so
     a posted "1-2", an id no option has, or a nested array (implode() warned
     "Array to string conversion" and stored "Array") must not reach data_text.
    */
    function selectedOptionIDs( $posted, $classAttribute )
    {
        if ( !is_array( $posted ) )
            return array();
        $content = $this->classAttributeContent( $classAttribute );
        $known = array();
        foreach ( $content['options'] as $option )
            $known[(string)$option['id']] = true;
        $ids = array();
        foreach ( $posted as $id )
        {
            if ( !is_scalar( $id ) )
                continue;
            $id = (string)$id;
            if ( isset( $known[$id] ) and !in_array( $id, $ids, true ) )
                $ids[] = $id;
        }
        if ( !$content['is_multiselect'] )
            $ids = array_slice( $ids, 0, 1 );
        return $ids;
    }

    /*!
     Validates input on content object level
     \return eZInputValidator::STATE_ACCEPTED or eZInputValidator::STATE_INVALID if
             the values are accepted or not
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();

        if ( $http->hasPostVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) );
            // Only what fetching will store counts: a selection of no existing
            // option is no selection, and a required one is then missing
            if ( !$this->selectedOptionIDs( $data, $classAttribute ) )
                $data = "";

            if ( $data == "" )
            {
                if ( !$classAttribute->attribute( 'is_information_collector' ) &&
                     $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }
        else if ( !$classAttribute->attribute( 'is_information_collector' ) && $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Input required.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches all variables from the object
     \return true if fetching of class attributes are successfull, false if not
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $selectOptions = $http->postVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) );
            $idString = implode( '-', $this->selectedOptionIDs( $selectOptions, $contentObjectAttribute->contentClassAttribute() ) );
            $contentObjectAttribute->setAttribute( 'data_text', $idString );
            return true;
        }
        return false;
    }

    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) );
            if ( !$this->selectedOptionIDs( $data, $contentObjectAttribute->contentClassAttribute() ) )
                $data = "";

            if ( $data == "" && $contentObjectAttribute->validateIsRequired() )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Input required.' ) );
                return eZInputValidator::STATE_INVALID;
            }
            else
            {
                return eZInputValidator::STATE_ACCEPTED;
            }
        }
        else
        {
            return eZInputValidator::STATE_INVALID;
        }
    }

   /*!
    Fetches the http post variables for collected information
   */
    function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $selectOptions = $http->postVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) );
            $idString = implode( '-', $this->selectedOptionIDs( $selectOptions, $contentObjectAttribute->contentClassAttribute() ) );
            $collectionAttribute->setAttribute( 'data_text', $idString );
            return true;
        }
        return false;
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $idString = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $idString );
            $contentObjectAttribute->store();
        }
    }

    /*!
     Returns the selected options by id.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        // data_text is NULL for an attribute never stored with a selection
        $idString = explode( '-', (string)$contentObjectAttribute->attribute( 'data_text' ) );
        return $idString;
    }

    /*!
     Returns the content data for the given content class attribute.
    */
    function classAttributeContent( $classAttribute )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $xmlString = $classAttribute->attribute( 'data_text5' );
        $optionArray = array();
        if ( $xmlString != '' )
        {
            // Broken stored XML is no options, not a page of libxml warnings
            $useInternalErrors = libxml_use_internal_errors( true );
            $success = $dom->loadXML( $xmlString );
            libxml_clear_errors();
            libxml_use_internal_errors( $useInternalErrors );
            if ( $success )
            {
                $options = $dom->getElementsByTagName( 'option' );

                foreach ( $options as $optionNode )
                {
                    $optionArray[] = array( 'id' => $optionNode->getAttribute( 'id' ),
                                            'name' => $optionNode->getAttribute( 'name' ) );
                }
            }
        }

        if ( count( $optionArray ) == 0 )
        {
            $optionArray[] = array( 'id' => 0,
                                    'name' => '' );
        }
        $attrValue = array( 'options' => $optionArray,
                            'is_multiselect' => $classAttribute->attribute( 'data_int1' ) );
        return $attrValue;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        $selected = $this->objectAttributeContent( $contentObjectAttribute );
        $classContent = $this->classAttributeContent( $contentObjectAttribute->attribute( 'contentclass_attribute' ) );
        $return = '';
        if ( count( $selected ) == 0)
        {
            return '';
        }

        $count = 0;
        $optionArray = $classContent['options'];
        foreach ( $selected as $id )
        {
            if ( $count++ != 0 )
                $return .= ' ';
            foreach ( $optionArray as $option )
            {
                $optionID = $option['id'];
                if ( $optionID == $id )
                    $return .= $option['name'];
            }
        }
        return $return;
    }

    function toString( $contentObjectAttribute )
    {
        $selected = $this->objectAttributeContent( $contentObjectAttribute );
        $classContent = $this->classAttributeContent( $contentObjectAttribute->attribute( 'contentclass_attribute' ) );

        if ( count( $selected ) )
        {
            $returnData = array();
            $optionArray = $classContent['options'];
            foreach ( $selected as $id )
            {
                foreach ( $optionArray as $option )
                {
                    $optionID = $option['id'];
                    if ( $optionID == $id )
                        $returnData[] = $option['name'];
                }
            }
            return eZStringUtils::implodeStr( $returnData, '|' );
        }
        return '';
    }


    function fromString( $contentObjectAttribute, $string )
    {
        if ( $string == '' )
            return true;
        $selectedNames = eZStringUtils::explodeStr( $string, '|' );
        $selectedIDList = array();
        $classContent = $this->classAttributeContent( $contentObjectAttribute->attribute( 'contentclass_attribute' ) );
        $optionArray = $classContent['options'];
        foreach ( $selectedNames as $name )
        {
            foreach ( $optionArray as $option )
            {
                $optionName = $option['name'];
                if ( $optionName == $name )
                    $selectedIDList[] = $option['id'];
            }
        }
        $idString = ( is_array( $selectedIDList ) ? implode( '-', $selectedIDList ) : "" );
        $contentObjectAttribute->setAttribute( 'data_text', $idString );
        return true;
    }

    /*!
     Returns the value as it will be shown if this attribute is used in the object name pattern.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        $selected = $this->objectAttributeContent( $contentObjectAttribute );
        $classContent = $this->classAttributeContent( $contentObjectAttribute->attribute( 'contentclass_attribute' ) );
        $return = '';
        if ( count( $selected ) )
        {
            $selectedNames = array();
            foreach ( $classContent['options'] as $option )
            {
                if ( in_array( $option['id'], $selected ) )
                    $selectedNames[] = $option['name'];
            }
            $return = implode( ', ', $selectedNames );
        }
        return $return;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $selected = $this->objectAttributeContent( $contentObjectAttribute );
        return isset( $selected[0] ) && $selected[0] != '';
    }

    function sortKey( $contentObjectAttribute )
    {
        return strtolower( (string)$contentObjectAttribute->attribute( 'data_text' ) );
    }

    function sortKeyType()
    {
        return 'string';
    }

    /*!
     \return true if the datatype can be indexed
    */
    function isIndexable()
    {
        return true;
    }

    function isInformationCollector()
    {
        return true;
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $isMultipleSelection = $classAttribute->attribute( 'data_int1'  );
        $xmlString = $classAttribute->attribute( 'data_text5' );

        $selectionDom = new DOMDocument( '1.0', 'utf-8' );
        $useInternalErrors = libxml_use_internal_errors( true );
        $success = $xmlString != '' && $selectionDom->loadXML( $xmlString );
        libxml_clear_errors();
        libxml_use_internal_errors( $useInternalErrors );
        $domRoot = $success ? $selectionDom->documentElement : null;
        $options = $domRoot ? $domRoot->getElementsByTagName( 'options' )->item( 0 ) : null;

        $dom = $attributeParametersNode->ownerDocument;

        // A class attribute with no (or broken) option XML exports an empty
        // <options/>, where it used to stop the export with a fatal error
        $importedOptionsNode = $options ? $dom->importNode( $options, true ) : $dom->createElement( 'options' );
        $attributeParametersNode->appendChild( $importedOptionsNode );
        $isMultiSelectNode = $dom->createElement( 'is-multiselect' );
        $isMultiSelectNode->appendChild( $dom->createTextNode( $isMultipleSelection ) );
        $attributeParametersNode->appendChild( $isMultiSelectNode );
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $options = $attributeParametersNode->getElementsByTagName( 'options' )->item( 0 );

        $doc = new DOMDocument( '1.0', 'utf-8' );
        $root = $doc->createElement( 'ezselection' );
        $doc->appendChild( $root );

        // A package without <options> imports a selection without options
        $importedOptions = $options ? $doc->importNode( $options, true ) : $doc->createElement( 'options' );
        $root->appendChild( $importedOptions );

        $xml = $doc->saveXML();
        $classAttribute->setAttribute( 'data_text5', $xml );

        $isMultiSelectNode = $attributeParametersNode->getElementsByTagName( 'is-multiselect' )->item( 0 );
        if ( !$isMultiSelectNode or $isMultiSelectNode->textContent == 0 )
            $classAttribute->setAttribute( 'data_int1', 0 );
        else
            $classAttribute->setAttribute( 'data_int1', 1 );
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }
}

eZDataType::register( eZSelectionType::DATA_TYPE_STRING, "eZSelectionType" );
?>
