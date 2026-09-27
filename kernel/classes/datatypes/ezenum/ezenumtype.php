<?php
/**
 * File containing the eZEnumtype class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZEnumType ezenumtype.php
  \ingroup eZDatatype

*/

class eZEnumType extends eZDataType
{
    const DATA_TYPE_STRING = 'ezenum';
    const IS_MULTIPLE_FIELD = 'data_int1';
    const IS_MULTIPLE_VARIABLE = '_ezenum_ismultiple_value_';
    const IS_OPTION_FIELD = 'data_int2';
    const IS_OPTION_VARIABLE = '_ezenum_isoption_value_';

    public function __construct()
    {
         parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', 'Enum', 'Datatype name' ),
                            array( 'serialize_supported' => true ) );
    }

    /*!
     Sets value according to current version
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $originalContentObjectAttributeID = $originalContentObjectAttribute->attribute( 'id' );
            $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
            $contentObjectAttributeVersion = $contentObjectAttribute->attribute( 'version' );

            $db = eZDB::instance();
            $db->begin();

            // Delete stored object attributes when initialize translated attribute.
            if ( $originalContentObjectAttributeID != $contentObjectAttributeID )
                $this->deleteStoredObjectAttribute( $contentObjectAttribute, $currentVersion );

            $newVersionEnumObject = eZEnumObjectValue::fetchAllElements( $originalContentObjectAttributeID, $currentVersion );

            for ( $i = 0; $i < count( $newVersionEnumObject ); ++$i )
            {
                $enumobjectvalue =  $newVersionEnumObject[$i];
                $enumobjectvalue->setAttribute( 'contentobject_attribute_id', $contentObjectAttribute->attribute( 'id' ) );
                $enumobjectvalue->setAttribute( 'contentobject_attribute_version',  $contentObjectAttributeVersion );
                $enumobjectvalue->store();
            }

            $db->commit();
        }
    }

    function cloneClassAttribute( $oldClassAttribute, $newClassAttribute )
    {
        $oldContentClassAttributeID = $oldClassAttribute->attribute( 'id' );
        $oldEnums = eZEnumValue::fetchAllElements( $oldContentClassAttributeID, 0 );

        $db = eZDB::instance();
        $db->begin();

        foreach ( $oldEnums as $oldEnum )
        {
            $enum = clone $oldEnum;
            $enum->setAttribute( 'contentclass_attribute_id', $newClassAttribute->attribute( 'id' ) );
            $enum->setAttribute( 'contentclass_attribute_version', $newClassAttribute->attribute( 'version' ) );
            $enum->store();
        }

        $db->commit();
    }

    /*!
     Set class attribute value for template version
    */
    function initializeClassAttribute( $classAttribute )
    {
        $contentClassAttributeID = $classAttribute->attribute( 'id' );
        $enums = eZEnumValue::fetchAllElements( $contentClassAttributeID, 1 );

        if ( count ( $enums ) == 0 )
        {
            $enums = eZEnumValue::fetchAllElements( $contentClassAttributeID, 0 );
            $db = eZDB::instance();
            $db->begin();
            foreach ( $enums as $enum )
            {
                $enum->setAttribute( 'contentclass_attribute_version', 1 );
                $enum->store();
            }
            $db->commit();
        }
    }

    /*!
     Delete stored object attribute
    */
    function deleteStoredObjectAttribute( $contentObjectAttribute, $version = null )
    {
        $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
        eZEnumObjectValue::removeAllElements( $contentObjectAttributeID, $version );

    }

    /*!
     Delete stored class attribute
    */
    function deleteStoredClassAttribute( $contentClassAttribute, $version = null )
    {
        $contentClassAttributeID = $contentClassAttribute->attribute( 'id' );
        eZEnumValue::removeAllElements( $contentClassAttributeID, $version );

    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
        $contentObjectAttributeVersion = $contentObjectAttribute->attribute( 'version' );
        $enumID =  $base . '_data_enumid_' . $contentObjectAttributeID;
        $enumElement = $base . '_data_enumelement_' . $contentObjectAttributeID;
        $enumValue = $base . '_data_enumvalue_' . $contentObjectAttributeID;
        $selectedEnumElement = $base . '_select_data_enumelement_' . $contentObjectAttributeID;
        if ( $http->hasPostVariable( $enumID ) &&
             $http->hasPostVariable( $enumElement ) &&
             $http->hasPostVariable( $enumValue ) )
        {
            $array_enumID = $http->postVariable( $enumID );
            $array_enumElement = $http->postVariable( $enumElement );
            $array_enumValue = $http->postVariable( $enumValue );

            if ( $http->hasPostVariable( $selectedEnumElement ) )
                $array_selectedEnumElement = $http->postVariable( $selectedEnumElement );
            else
                $array_selectedEnumElement = null;

            $db = eZDB::instance();
            $db->begin();

            // Remove stored enumerations before we store new enumerations
            eZEnum::removeObjectEnumerations( $contentObjectAttributeID, $contentObjectAttributeVersion );

            // The element list, its ids and values are hidden fields of the
            // form, so what was stored used to be whatever was posted: any id,
            // any text, and a selected element once per time it was posted,
            // compared in a loop of elements x selections (a huge form kept the
            // request busy). The selections are now matched against the
            // elements the class really has, and each is stored with the id,
            // name and value the class gives it, in the class's order, once.
            if ( is_array( $array_selectedEnumElement ) )
            {
                $selected = array();
                foreach ( $array_selectedEnumElement as $selectedElement )
                {
                    if ( is_scalar( $selectedElement ) )
                        $selected[(string)$selectedElement] = true;
                }
                $classAttribute = $contentObjectAttribute->contentClassAttribute();
                $isMultiple = $classAttribute ? $classAttribute->attribute( self::IS_MULTIPLE_FIELD ) : 1;
                $classEnum = $classAttribute ? $classAttribute->content() : null;
                $classElements = $classEnum instanceof eZEnum ? (array)$classEnum->attribute( 'enum_list' ) : array();
                foreach ( $classElements as $classElement )
                {
                    $eElement = (string)$classElement->attribute( 'enumelement' );
                    if ( !isset( $selected[$eElement] ) )
                        continue;
                    eZEnum::storeObjectEnumeration( $contentObjectAttributeID,
                                                    $contentObjectAttributeVersion,
                                                    $classElement->attribute( 'id' ),
                                                    $eElement,
                                                    $classElement->attribute( 'enumvalue' ) );
                    // Radio buttons and a single select give one choice
                    if ( !$isMultiple )
                        break;
                }
            }
            $db->commit();
            return true;
        }
        return false;
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_data_enumid_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $array_enumID = $http->postVariable( $base . '_data_enumid_' . $contentObjectAttribute->attribute( 'id' ) );
            $classAttribute = $contentObjectAttribute->contentClassAttribute();

            if ( $contentObjectAttribute->validateIsRequired() )
            {
                if ( !$http->hasPostVariable( $base . '_select_data_enumelement_' . $contentObjectAttribute->attribute( 'id' ) ) )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'At least one field should be chosen.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Does nothing since it has been stored.
     See fetchObjectAttributeHTTPInput for the actual storing.
    */
    function storeObjectAttribute( $contentObjectAttribute )
    {
    }

    /*!
     Returns actual the class attribute content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
        $contentObjectAttributeVersion = $contentObjectAttribute->attribute( 'version' );
        $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
        $id = $contentClassAttribute->attribute( 'id' );
        $version = $contentClassAttribute->attribute( 'version' );
        $ismultiple = $contentClassAttribute->attribute( 'data_int1' );
        $isoption = $contentClassAttribute->attribute( 'data_int2' );
        $enum = new eZEnum( $id, $version );
        $enum->setIsmultipleValue( $ismultiple );
        $enum->setIsoptionValue( $isoption );
        $enum->setObjectEnumValue( $contentObjectAttributeID, $contentObjectAttributeVersion );
        return $enum;
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateClassAttributeHTTPInput( $http, $base, $contentClassAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchClassAttributeHTTPInput( $http, $base, $contentClassAttribute )
    {
        $ismultiple = $base . self::IS_MULTIPLE_VARIABLE . $contentClassAttribute->attribute( 'id' );
        $isoption = $base . self::IS_OPTION_VARIABLE . $contentClassAttribute->attribute( 'id' );
        $enumID =  $base . '_data_enumid_' . $contentClassAttribute->attribute( 'id' );
        $enumElement = $base . '_data_enumelement_' . $contentClassAttribute->attribute( 'id' );
        $enumValue = $base . '_data_enumvalue_' . $contentClassAttribute->attribute( 'id' );
        $enumRemove = $base . '_data_enumremove_' . $contentClassAttribute->attribute( 'id' );
        $version = $contentClassAttribute->attribute( 'version' );

        if ( $http->hasPostVariable( $isoption ) )
        {
            $ismultipleValue = $http->hasPostVariable( $ismultiple ) ? 1 : 0;
            $contentClassAttribute->setAttribute( self::IS_MULTIPLE_FIELD, $ismultipleValue );
            $optionValue = $http->postVariable( $isoption );
            $optionValueSet = $optionValue == 1 ? '1' : '0';
            $contentClassAttribute->setAttribute( self::IS_OPTION_FIELD, $optionValueSet );
        }

        if ( $http->hasPostVariable( $enumID ) &&
             $http->hasPostVariable( $enumElement ) &&
             $http->hasPostVariable( $enumValue ) &&
             !($http->hasPostVariable( $enumRemove ) ) )
        {
            $array_enumID = $http->postVariable(  $enumID );
            $array_enumElement = $http->postVariable( $enumElement );
            $array_enumValue = $http->postVariable( $enumValue );
            $enum = $contentClassAttribute->content();
            $enum->setValue( $array_enumID, $array_enumElement, $array_enumValue, $version );
            $contentClassAttribute->setContent( $enum );
        }
    }

    function storeClassAttribute( $contentClassAttribute, $version )
    {
        $contentClassAttribute->content()->setVersion( $version );
    }

    function storeDefinedClassAttribute( $contentClassAttribute )
    {
        $contentClassAttribute->content()->setVersion( eZContentClass::VERSION_STATUS_DEFINED );
    }

    function storeModifiedClassAttribute( $contentClassAttribute )
    {
        $contentClassAttribute->content()->setVersion( eZContentClass::VERSION_STATUS_MODIFIED );
    }

    /*!
     Returns the content.
    */
    function classAttributeContent( $contentClassAttribute )
    {
        $id = $contentClassAttribute->attribute( 'id' );
        $version = $contentClassAttribute->attribute( 'version' );
        $enum = new eZEnum( $id, $version );
        return $enum;
    }

    function customClassAttributeHTTPAction( $http, $action, $contentClassAttribute )
    {
        $id = $contentClassAttribute->attribute( 'id' );
        switch ( $action )
        {
            case 'new_enumelement' :
            {
                $enum = $contentClassAttribute->content( );
                $enum->addEnumeration('');
                $contentClassAttribute->setContent( $enum );
            }break;
            case 'remove_selected' :
            {
                $enum = $contentClassAttribute->content( );
                $version = $contentClassAttribute->attribute( 'version' );
                $postvarname = 'ContentClass' . '_data_enumremove_' . $contentClassAttribute->attribute( 'id' );
                $array_remove = $http->hasPostVariable( $postvarname ) ? $http->postVariable( $postvarname ) : array();
                // A string made foreach() warn, and an element is removed by id
                // and version alone: only the ids of this attribute's own
                // elements are removed, not those of another class attribute
                $ownIDs = array();
                foreach ( (array)$enum->attribute( 'enum_list' ) as $enumValue )
                    $ownIDs[(int)$enumValue->attribute( 'id' )] = true;
                foreach( ( is_array( $array_remove ) ? $array_remove : array() ) as $enumid )
                {
                    if ( is_scalar( $enumid ) and isset( $ownIDs[(int)$enumid] ) )
                    {
                        $enum->removeEnumeration( $id, (int)$enumid, $version );
                        unset( $ownIDs[(int)$enumid] );
                    }
                }
            }break;
            default :
            {
                eZDebug::writeError( 'Unknown custom HTTP action: ' . $action, 'eZEnumType' );
            }break;
        }
    }

    /*!
     Returns the object attribute title.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        $enum = $this->objectAttributeContent( $contentObjectAttribute );

        $enumObjectList = $enum->attribute( 'enumobject_list' );

        $value = '';

        foreach ( $enumObjectList as $count => $enumObjectValue )
        {
            if ( $count != 0 )
                $value .= ', ';
            $value .= $enumObjectValue->attribute( 'enumelement' );
        }

        return $value;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
        $contentObjectAttributeVersion = $contentObjectAttribute->attribute( 'version' );
        $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
        $id = $contentClassAttribute->attribute( 'id' );
        $version = $contentClassAttribute->attribute( 'version' );
        $ismultiple = $contentClassAttribute->attribute( 'data_int1' );
        $isoption = $contentClassAttribute->attribute( 'data_int2' );

        $enum = new eZEnum( $id, $version );
        $enum->setIsmultipleValue( $ismultiple );
        $enum->setIsoptionValue( $isoption );
        $enum->setObjectEnumValue( $contentObjectAttributeID, $contentObjectAttributeVersion );

        $return = '';
        foreach ( $enum->attribute( 'enumobject_list' ) as $enumElement )
        {
            $return .= $enumElement->attribute( 'enumvalue' ) . ' ';
            $return .= $enumElement->attribute( 'enumelement' ) . ' ';
        }
        return $return;
    }

    /*!
     Sets \c grouped_input to \c true when checkboxes or radiobuttons are used.
    */
    function objectDisplayInformation( $objectAttribute, $mergeInfo = false )
    {
        $classAttribute = $objectAttribute->contentClassAttribute();
        $isOption = $classAttribute->attribute( 'data_int2' );

        $editGrouped = ( $isOption == false );
        $info = array( 'edit' => array( 'grouped_input' => $editGrouped ),
                       'collection' => array( 'grouped_input' => $editGrouped ) );
        return eZDataType::objectDisplayInformation( $objectAttribute, $info );
    }

    function isIndexable()
    {
        return true;
    }

    /*!
     \return a DOM representation of the content object attribute
    */

    function serializeContentObjectAttribute( $package, $contentObjectAttribute )
    {
        $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
        $contentObjectAttributeVersion = $contentObjectAttribute->attribute( 'version' );

        $node = $this->createContentObjectAttributeDOMNode( $contentObjectAttribute );

        $enumElements = eZEnumObjectValue::fetchAllElements( $contentObjectAttributeID, $contentObjectAttributeVersion );

        foreach ( $enumElements as $enumElement )
        {
            $elementNode = $node->ownerDocument->createElement( 'enum-element' );

            $elementNode->setAttribute( 'id', $enumElement->attribute( 'enumid' ) );
            $elementNode->setAttribute( 'value', $enumElement->attribute( 'enumvalue' ) );
            $elementNode->setAttribute( 'element', $enumElement->attribute( 'enumelement' ) );
            $node->appendChild( $elementNode );
        }

        return $node;
    }


    /*!
     Unserialize contentobject attribute

     \param package
     \param contentobject attribute object
     \param ezdomnode object
    */
    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        if ( $attributeNode->hasChildNodes() )
        {
            $contentObjectAttributeID = $objectAttribute->attribute( 'id' );
            $contentObjectAttributeVersion = $objectAttribute->attribute( 'version' );

            $enumNodes = $attributeNode->childNodes;
            foreach ( $enumNodes as $enumNode )
            {
                // A package formatted with indentation has text nodes between
                // the elements; DOMText has no getAttribute() (a fatal error)
                if ( !$enumNode instanceof DOMElement )
                    continue;
                $eID      = $enumNode->getAttribute( 'id' );
                $eValue   = $enumNode->getAttribute( 'value' );
                $eElement = $enumNode->getAttribute( 'element' );

                eZEnum::storeObjectEnumeration( $contentObjectAttributeID,
                                                $contentObjectAttributeVersion,
                                                $eID,
                                                $eElement,
                                                $eValue );
            }
        }
        else
        {
            eZDebug::writeError( "Can't find attributes for enumeration", __METHOD__ );
        }
    }


    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return true;
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $isOption = $classAttribute->attribute( self::IS_OPTION_FIELD );
        $isMultiple = $classAttribute->attribute( self::IS_MULTIPLE_FIELD );
        $content = $classAttribute->attribute( 'content' );
        $enumList = $content->attribute( 'enum_list' );
        $attributeParametersNode->setAttribute( 'is-option', $isOption ? 'true' : 'false' );
        $attributeParametersNode->setAttribute( 'is-multiple', $isMultiple ? 'true' : 'false' );

        $elementListNode = $attributeParametersNode->ownerDocument->createElement( 'elements' );
        $attributeParametersNode->appendChild( $elementListNode );
        foreach( $enumList as $enumElement )
        {
            $elementNode = $attributeParametersNode->ownerDocument->createElement( 'element' );
            $elementNode->setAttribute( 'id', $enumElement->attribute( 'id' ) );
            $elementNode->setAttribute( 'name', $enumElement->attribute( 'enumelement' ) );
            $elementNode->setAttribute( 'value', $enumElement->attribute( 'enumvalue' ) );
            $elementListNode->appendChild( $elementNode );
        }
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $isOption = strtolower( $attributeParametersNode->getAttribute( 'is-option' ) ) == 'true';
        $isMultiple = strtolower( $attributeParametersNode->getAttribute( 'is-multiple' ) ) == 'true';
        $classAttribute->setAttribute( self::IS_OPTION_FIELD, $isOption );
        $classAttribute->setAttribute( self::IS_MULTIPLE_FIELD, $isMultiple );

        $enum = new eZEnum( $classAttribute->attribute( 'id' ), $classAttribute->attribute( 'version' ) );
        $elementListNode = $attributeParametersNode->getElementsByTagName( 'elements' )->item( 0 );
        if ( $elementListNode )
        {
            $elementList = $elementListNode->getElementsByTagName( 'element' );
            foreach ( $elementList as $element )
            {
                $elementID = $element->getAttribute( 'id' );
                $elementName = $element->getAttribute( 'name' );
                $elementValue = $element->getAttribute( 'value' );
                $value = eZEnumValue::create( $classAttribute->attribute( 'id' ),
                                               $classAttribute->attribute( 'version' ),
                                               $elementName );
                $value->setAttribute( 'enumvalue', $elementValue );
                $value->store();
                $enum->addEnumerationValue( $value );
            }
        }
    }

    function diff( $old, $new, $options = false )
    {
        return null;
    }
}
eZDataType::register( eZEnumType::DATA_TYPE_STRING, 'eZEnumType' );

?>
