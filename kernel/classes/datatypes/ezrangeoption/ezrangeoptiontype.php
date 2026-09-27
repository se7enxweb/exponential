<?php
/**
 * File containing the eZRangeOptionType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZRangeOptionType ezrangeoptiontype.php
  \ingroup eZDatatype
  \brief The class eZRangeOptionType does

*/
class eZRangeOptionType extends eZDataType
{
    const DEFAULT_NAME_VARIABLE = "_ezrangeoption_default_name_";

    const DATA_TYPE_STRING = "ezrangeoption";

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Range option", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();

        if ( $http->hasPostVariable( $base . "_data_rangeoption_name_" . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . '_data_rangeoption_start_value_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_data_rangeoption_stop_value_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_data_rangeoption_step_value_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $name = $http->postVariable( $base . "_data_rangeoption_name_" . $contentObjectAttribute->attribute( "id" ) );
            $startValue = $http->postVariable( $base . '_data_rangeoption_start_value_' . $contentObjectAttribute->attribute( 'id' ) );
            $stopValue = $http->postVariable( $base . '_data_rangeoption_stop_value_' . $contentObjectAttribute->attribute( 'id' ) );
            $stepValue = $http->postVariable( $base . '_data_rangeoption_step_value_' . $contentObjectAttribute->attribute( 'id' ) );

            // Each field is a single text input: an array (name[]=) is not
            // input this form produces and would be stored as ''
            foreach ( array( $name, $startValue, $stopValue, $stepValue ) as $value )
            {
                if ( !is_scalar( $value ) )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                     'Missing range option input.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }

            if ( $name == '' or
                 $startValue == '' or
                 $stopValue == '' or
                 $stepValue == '' )
            {
                if ( ( !$classAttribute->attribute( 'is_information_collector' ) and
                       $contentObjectAttribute->validateIsRequired() ) )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                     'Missing range option input.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
                else
                    return eZInputValidator::STATE_ACCEPTED;
            }

            // A value that is not a number, a step of zero or less or a range of
            // more than eZRangeOption::MAX_OPTION_COUNT values would be stored
            // and then give an empty option list on every read
            if ( eZRangeOption::rangeCount( $startValue, $stopValue, $stepValue ) === false )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                 'The start, stop and step values must be numbers, the step greater than zero, and the range may have at most %1 values.' ),
                                                 eZRangeOption::MAX_OPTION_COUNT );
                return eZInputValidator::STATE_INVALID;
            }
            return eZInputValidator::STATE_ACCEPTED;
        }
        else if ( !$classAttribute->attribute( 'is_information_collector' ) and $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Missing range option input.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        else
        {
            return eZInputValidator::STATE_ACCEPTED;
        }


    }

    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {

        // A missing field or an array in place of a text field is read as ''
        // (the validation has already refused it where the attribute needs input)
        $id = $contentObjectAttribute->attribute( "id" );
        $optionName = self::scalarPostVariable( $http, $base . "_data_rangeoption_name_" . $id );
        $optionStartValue = self::scalarPostVariable( $http, $base . "_data_rangeoption_start_value_" . $id );
        $optionStopValue = self::scalarPostVariable( $http, $base . "_data_rangeoption_stop_value_" . $id );
        $optionStepValue = self::scalarPostVariable( $http, $base . "_data_rangeoption_step_value_" . $id );

        $option = new eZRangeOption( $optionName );

        $option->setStartValue( $optionStartValue );
        $option->setStopValue( $optionStopValue );
        $option->setStepValue( $optionStepValue );

        $contentObjectAttribute->setContent( $option );
        return true;
    }

    /*!
     \static
     \return the post variable \a $name as a string, '' when it is missing or
     is not a single value.
    */
    static function scalarPostVariable( $http, $name )
    {
        if ( !$http->hasPostVariable( $name ) )
            return '';
        $value = $http->postVariable( $name );
        return is_scalar( $value ) ? (string)$value : '';
    }

    function storeObjectAttribute( $contentObjectAttribute )
    {
        $option = $contentObjectAttribute->content();
        $contentObjectAttribute->setAttribute( "data_text", $option->xmlString() );
    }

    function objectAttributeContent( $contentObjectAttribute )
    {
        $option = new eZRangeOption( "" );
        $option->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
        return $option;
    }

    function toString( $contentObjectAttribute )
    {

        $option = $contentObjectAttribute->attribute( 'content' );
        $optionArray = array();
        $optionArray[] = $option->attribute( 'name' );
        $optionArray[] = $option->attribute( 'start_value' );
        $optionArray[] = $option->attribute( 'stop_value' );
        $optionArray[] = $option->attribute( 'step_value' );

        return implode( '|', $optionArray );
    }


    function fromString( $contentObjectAttribute, $string )
    {
        if ( $string == '' )
            return true;

        $optionArray = explode( '|', $string );

        $option = new eZRangeOption( '' );

        // toString() writes name|start|stop|step without escaping, so a name
        // with a | in it comes back as several parts: the last three are the
        // numbers and everything before them is the name
        if ( count( $optionArray ) > 4 )
        {
            $numbers = array_splice( $optionArray, -3 );
            $optionArray = array_merge( array( implode( '|', $optionArray ) ), $numbers );
        }

        $option->Name = array_shift( $optionArray );
        $option->StartValue = array_shift( $optionArray );
        $option->StopValue = array_shift( $optionArray );
        $option->StepValue = array_shift( $optionArray );


        $contentObjectAttribute->setAttribute( "data_text", $option->xmlString() );

        return $option;

    }
    /*!
     Finds the option which has the ID that matches \a $optionID, if found it returns
     an option structure.
    */
    function productOptionInformation( $objectAttribute, $optionID, $productItem )
    {
        $option = $objectAttribute->attribute( 'content' );
        foreach( $option->attribute( 'option_list' ) as $optionArray )
        {
            if ( $optionArray['id'] == $optionID )
            {
                return array( 'id' => $optionArray['id'],
                              'name' => $option->attribute( 'name' ),
                              'value' => $optionArray['value'],
                              'additional_price' => $optionArray['additional_price'] );
            }
        }
        return false;
    }

    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    function title( $contentObjectAttribute, $name = null )
    {
        $option = new eZRangeOption( "" );
        $option->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
        return $option->attribute('name');
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return true;
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion == false )
        {
            $option = $contentObjectAttribute->content();
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            if ( !$option )
            {
                $option = new eZRangeOption( $contentClassAttribute->attribute( 'data_text1' ) );
            }
            else
            {
                $option->setName( $contentClassAttribute->attribute( 'data_text1' ) );
            }
            $contentObjectAttribute->setAttribute( "data_text", $option->xmlString() );
            $contentObjectAttribute->setContent( $option );
        }
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $defaultValueName = $base . self::DEFAULT_NAME_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $defaultValueName ) )
        {
            $defaultValueValue = $http->postVariable( $defaultValueName );

            // A text field: an array in its place is stored as ''
            if ( !is_scalar( $defaultValueValue ) || $defaultValueValue == "" ){
                $defaultValueValue = "";
            }
            $classAttribute->setAttribute( 'data_text1', $defaultValueValue );
            return true;
        }
        return false;
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $defaultName = $classAttribute->attribute( 'data_text1' );
        $dom = $attributeParametersNode->ownerDocument;
        $defaultNameNode = $dom->createElement( 'default-name' );
        $defaultNameNode->appendChild( $dom->createTextNode( $defaultName ) );
        $attributeParametersNode->appendChild( $defaultNameNode );
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        // A package made without the element imports as an empty default name
        // instead of a fatal error on null
        $defaultNameNode = $attributeParametersNode ? $attributeParametersNode->getElementsByTagName( 'default-name' )->item( 0 ) : null;
        $defaultName = $defaultNameNode ? $defaultNameNode->textContent : '';
        $classAttribute->setAttribute( 'data_text1', $defaultName );
    }

    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        // Empty or broken stored XML exports the attribute without content
        // (loadXML( '' ) throws a ValueError, a broken one has no root to import)
        $domDocument = eZRangeOption::loadDocument( $objectAttribute->attribute( 'data_text' ) );
        if ( $domDocument )
        {
            $importedRoot = $node->ownerDocument->importNode( $domDocument->documentElement, true );
            $node->appendChild( $importedRoot );
        }

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $rootNode = $attributeNode->getElementsByTagName( 'ezrangeoption' )->item( 0 );
        $xmlString = $rootNode ? $rootNode->ownerDocument->saveXML( $rootNode ) : '';
        $objectAttribute->setAttribute( 'data_text', $xmlString );
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }

    function batchInitializeObjectAttributeData( $classAttribute )
    {
        $option = new eZRangeOption( $classAttribute->attribute( 'data_text1' ) );
        // The value goes into SQL as it is: a default name with a ' in it broke
        // the statement that initialises the attribute for existing objects
        $db = eZDB::instance();
        return array( 'data_text' => "'" . $db->escapeString( $option->xmlString() ) . "'" );
    }
}

eZDataType::register( eZRangeOptionType::DATA_TYPE_STRING, "eZRangeOptionType" );

?>
