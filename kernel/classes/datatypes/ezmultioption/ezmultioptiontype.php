<?php
/**
 * File containing the eZOptionType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZMultiOptionType ezmultioptiontype.php
  \ingroup eZDatatype
  \brief A datatype which works with multiple options.

  This allows the user to add several option choices almost as if he
  was adding attributes with option datatypes.

  This class implements the interface for a datatype but passes
  most of the work over to the eZMultiOption class which handles
  parsing, storing and manipulation of multioptions and options.

  This datatype supports:
  - fetch and validation of HTTP data
  - search indexing
  - product option information
  - class title
  - class serialization

*/

class eZMultiOptionType extends eZDataType
{
    const DEFAULT_NAME_VARIABLE = "_ezmultioption_default_name_";
    const DATA_TYPE_STRING = "ezmultioption";

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Multi-option", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    /*!
     Validates the input for this datatype.
     \return True if input is valid.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $count = 0;
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        if ( $http->hasPostVariable( $base . "_data_multioption_id_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            // Every list is normalised by postedList(): a missing list, a single
            // value in place of one or a nested array was a TypeError in
            // foreach/count()/trim() or an undefined offset
            $multioptionIDArray = self::postedList( $http, $base . "_data_multioption_id_" . $contentObjectAttribute->attribute( "id" ) );

            foreach ( $multioptionIDArray as $id )
            {
                $optionIDArray = self::postedList( $http, $base . "_data_option_id_" . $contentObjectAttribute->attribute( "id" ) . '_' . $id );
                $optionValueArray = self::postedList( $http, $base . "_data_option_value_" . $contentObjectAttribute->attribute( "id" ) . '_' . $id );
                $optionAdditionalPriceArray = self::postedList( $http, $base . "_data_option_additional_price_" . $contentObjectAttribute->attribute( "id" ) . '_' . $id );
                for ( $i = 0; $i < count( $optionIDArray ); $i++ )
                {
                    $optionValueArray += array( $i => '' );
                    $optionAdditionalPriceArray += array( $i => '' );
                    if ( $contentObjectAttribute->validateIsRequired() and !$classAttribute->attribute( 'is_information_collector' ) )
                    {
                        if ( trim( $optionValueArray[$i] ) == "" )
                        {
                            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                                 'The option value must be provided.' ) );
                            return eZInputValidator::STATE_INVALID;
                        }
                        else
                            ++$count;
                    }

                    if ( trim( $optionValueArray[$i] ) != "" )
                    {
                        if ( strlen( $optionAdditionalPriceArray[$i] ) && !preg_match( "#^[-|+]?[0-9]+(\.){0,1}[0-9]{0,2}$#", $optionAdditionalPriceArray[$i] ) )
                        {
                            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                                 'The additional price for the multioption value is not valid.' ) );
                            return eZInputValidator::STATE_INVALID;
                        }
                    }

                }
            }
        }
        if ( $contentObjectAttribute->validateIsRequired() and
                 !$classAttribute->attribute( 'is_information_collector' ) )
        {
            if ( $count == 0 )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'At least one option is required.' ) );
                return eZInputValidator::STATE_INVALID;
            }

            $optionSetName = self::postedString( $http, $base . "_data_optionset_name_" . $contentObjectAttribute->attribute( "id" ) );
            if ( trim( $optionSetName ) == '' )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'Option set name is required.' ) );
                return eZInputValidator::STATE_INVALID;
            }
        }


        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     This function calles xmlString function to create xml string and then store the content.
    */
    function storeObjectAttribute( $contentObjectAttribute )
    {
        $multioption = $contentObjectAttribute->content();
        $contentObjectAttribute->setAttribute( "data_text", $multioption->xmlString() );
    }

    /*!
     \return An eZMultiOption object which contains all the option data
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $multioption = new eZMultiOption( "" );
        $multioption->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
        return $multioption;
    }

    function isIndexable()
    {
        return true;
    }

    /*!
     \return The internal XML text.
    */
    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        // All input goes through postedList()/postedString(): a missing field,
        // a single value in place of a list or an array in place of a text
        // field was a TypeError, an undefined offset or "Array" in the XML
        $multioptionIDArray = self::postedList( $http, $base . "_data_multioption_id_" . $contentObjectAttribute->attribute( "id" ) );
        $optionSetName = self::postedString( $http, $base . "_data_optionset_name_" . $contentObjectAttribute->attribute( "id" ) );
        $multioption = new eZMultiOption( $optionSetName );
        foreach ( $multioptionIDArray as $id )
        {
            $multioptionName = self::postedString( $http, $base . "_data_multioption_name_" . $contentObjectAttribute->attribute( "id" ) . '_' . $id );
            $optionIDArray = self::postedList( $http, $base . "_data_option_id_" . $contentObjectAttribute->attribute( "id" ) . '_' . $id );

            // The priority only orders the multioptions: text sorts as 0
            $optionPriority = self::postedString( $http, $base . "_data_multioption_priority_" . $contentObjectAttribute->attribute( "id" ) . '_' . $id );
            $optionPriority = is_numeric( $optionPriority ) ? $optionPriority : 0;
            // check to prevent PHP warning if the default choice is specified (no radio button selected)
            $optionDefaultValue = self::postedString( $http, $base . "_data_radio_checked_" . $contentObjectAttribute->attribute("id") . '_' . $id );
            $newID = $multioption->addMultiOption( $multioptionName,$optionPriority, $optionDefaultValue );

            $optionCountArray = self::postedList( $http, $base . "_data_option_option_id_" . $contentObjectAttribute->attribute( "id" ) . '_' . $id );
            $optionValueArray = self::postedList( $http, $base . "_data_option_value_" . $contentObjectAttribute->attribute( "id" ) . '_' . $id );
            $optionAdditionalPriceArray = self::postedList( $http, $base . "_data_option_additional_price_" . $contentObjectAttribute->attribute( "id" ) . '_' . $id );

            for ( $i = 0; $i < count( $optionIDArray ); $i++ )
                $multioption->addOption( $newID,
                                         isset( $optionCountArray[$i] ) ? $optionCountArray[$i] : '',
                                         isset( $optionValueArray[$i] ) ? $optionValueArray[$i] : '',
                                         isset( $optionAdditionalPriceArray[$i] ) ? $optionAdditionalPriceArray[$i] : '' );
        }

        $multioption->sortMultiOptions();
        $multioption->resetOptionCounter();
        $contentObjectAttribute->setContent( $multioption );
        return true;
    }

    /*!
     Fetches the http post variables for collected information
    */
    function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        // data_int is an integer column: a missing field, an array or text is no choice
        $multioptionValue = self::postedString( $http, $base . "_data_multioption_value_" . $contentObjectAttribute->attribute( "id" ) );
        if ( !is_numeric( $multioptionValue ) )
            return false;
        $collectionAttribute->setAttribute( 'data_int', (int)$multioptionValue );
        return true;
    }

    /*!
     This function performs specific actions.

     It has some special actions with parameters which is done by exploding
     $action into several parts with delimeter '_'.
     The first element is the name of specific action to perform.
     The second element will contain the key value or id.

     The various operation's that is performed by this function are as follow.
     - new-option - A new option is added to a multioption.
     - remove-selected-option - Removes a selected option.
     - new_multioption - Adds a new multioption.
     - remove_selected_multioption - Removes all multioptions given by a selection list
    */
    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        // The action is "<name>_<multioption id>" from the button name; the id
        // part may be missing or not a number in a hand-made request, which
        // was an undefined offset and a TypeError in "id - 1"
        $actionlist = explode( "_", $action ) + array( '', '' );
        if ( $actionlist[0] == "new-option" )
        {
            $multioption = $contentObjectAttribute->content();

            // Looked up by the multioption's id, which the button carries; that
            // is the key id - 1 the old code used after sortMultiOptions()
            $key = $multioption->multiOptionKey( $actionlist[1] );
            if ( $key === false )
                return;
            $multioption->addOption( $key, "", "", "");
            $contentObjectAttribute->setContent( $multioption );
            $contentObjectAttribute->store();
        }
        else if ( $actionlist[0] == "remove-selected-option" )
        {
            $multioption = $contentObjectAttribute->content();
            $key = $multioption->multiOptionKey( $actionlist[1] );
            if ( $key === false )
                return;
            $postvarname = "ContentObjectAttribute" . "_data_option_remove_" . $contentObjectAttribute->attribute( "id" ) . "_" . $actionlist[1];
            $array_remove = $http->hasPostVariable( $postvarname ) ? $http->postVariable( $postvarname ) : array();
            $multioption->removeOptions( $array_remove, $key );
            $contentObjectAttribute->setContent( $multioption );
            $contentObjectAttribute->store();
        }
        else
        {
            switch ( $action )
            {
                case "new_multioption" :
                {
                    $multioption = $contentObjectAttribute->content();
                    $newID = $multioption->addMultiOption( "" ,0,false );
                    $multioption->addOption( $newID, "", "", "" );
                    $multioption->addOption( $newID, "" ,"", "" );
                    $contentObjectAttribute->setContent( $multioption );
                    $contentObjectAttribute->store();
                } break;

                case "remove_selected_multioption":
                {
                    $multioption = $contentObjectAttribute->content();
                    $postvarname = "ContentObjectAttribute" . "_data_multioption_remove_" . $contentObjectAttribute->attribute( "id" );
                    $array_remove = $http->hasPostVariable( $postvarname )? $http->postVariable( $postvarname ) : array();
                    $multioption->removeMultiOptions( $array_remove );
                    $contentObjectAttribute->setContent( $multioption );
                    $contentObjectAttribute->store();
                } break;

                default:
                {
                    eZDebug::writeError( "Unknown custom HTTP action: " . $action, "eZMultiOptionType" );
                } break;
            }
        }
    }

    /*!
     Finds the option which has the correct ID , if found it returns an option structure.

     \param $optionString must contain the multioption ID an underscore (_) and a the option ID.
    */
    function productOptionInformation( $objectAttribute, $optionID, $productItem )
    {
        $multioption = $objectAttribute->attribute( 'content' );

        foreach ( $multioption->attribute( 'multioption_list' ) as $multioptionElement )
        {
            foreach ( $multioptionElement['optionlist'] as $option )
            {
                if ( $option['option_id'] != $optionID )
                    continue;

                return array( 'id' => $option['option_id'],
                              'name' => $multioptionElement['name'],
                              'value' => $option['value'],
                              'additional_price' => $option['additional_price'] );
            }
        }
    }

    function title( $contentObjectAttribute, $name = "name" )
    {
        $multioption = $contentObjectAttribute->content();
        return $multioption->attribute( $name );
    }

    /*!
      \return \c true if there are more than one multioption in the list.
    */
    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $multioption = $contentObjectAttribute->content();
        $multioptions = $multioption->attribute( 'multioption_list' );
        return count( $multioptions ) > 0;
    }

    /*!
     Sets default multioption values.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion == false )
        {
            $multioption = $contentObjectAttribute->content();
            if ( $multioption )
            {
                $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
                $multioption->setName( $contentClassAttribute->attribute( 'data_text1' ) );
                $contentObjectAttribute->setAttribute( "data_text", $multioption->xmlString() );
                $contentObjectAttribute->setContent( $multioption );
            }
        }
        else
        {
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $defaultValueName = $base . self::DEFAULT_NAME_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $defaultValueName ) )
        {
            $defaultValueValue = $http->postVariable( $defaultValueName );

            // A text field: an array in its place is stored as ''
            if ( !is_scalar( $defaultValueValue ) || $defaultValueValue == "" )
            {
                $defaultValueValue = "";
            }
            $classAttribute->setAttribute( 'data_text1', $defaultValueValue );
            return true;
        }
        return false;
    }

    function toString( $contentObjectAttribute )
    {

        $content = $contentObjectAttribute->attribute( 'content' );

        $multioptionArray = array();

        $setName = $content->attribute( 'name' );
        $multioptionArray[] = $setName;

        $multioptionList = $content->attribute( 'multioption_list' );

        foreach ( $multioptionList as $key => $option )
        {
            $optionArray = array();
            $optionArray[] = $option['name'];
            $optionArray[] = $option['default_option_id'];
            foreach ( $option['optionlist'] as $key => $value )
            {
                $optionArray[] = $value['value'];
                $optionArray[] = $value['additional_price'];
            }
            $multioptionArray[] = eZStringUtils::implodeStr( $optionArray, '|' );
        }
        return eZStringUtils::implodeStr( $multioptionArray, "&" );
    }


    function fromString( $contentObjectAttribute, $string )
    {
        if ( $string == '' )
            return true;

        $multioptionArray = eZStringUtils::explodeStr( $string, '&' );

        $multioption = new eZMultiOption( "" );

        $multioption->OptionCounter = 0;
        $multioption->Options = array();
        $multioption->Name = array_shift( $multioptionArray );
        $priority = 1;
        foreach ( $multioptionArray as $multioptionStr )
        {
            $optionArray = eZStringUtils::explodeStr( $multioptionStr, '|' );


            $newID = $multioption->addMultiOption( array_shift( $optionArray ),
                                            $priority,
                                            array_shift( $optionArray ) );
            $count = count( $optionArray );
            for ( $i = 0; $i < $count; $i +=2 )
            {
                // The option ids come from the set's counter, as they do when
                // the options are entered in the edit form. The old count from
                // 0 in each multioption gave the options of every multioption
                // the same ids, so productOptionInformation() (the shop) found
                // the option of the first multioption for any of them
                $multioption->addOption( $newID, '', array_shift( $optionArray ), array_shift( $optionArray ) );
            }
            $priority++;
        }

        $contentObjectAttribute->setAttribute( "data_text", $multioption->xmlString() );

        return $multioption;

    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $defaultValue = $classAttribute->attribute( 'data_text1' );
        $dom = $attributeParametersNode->ownerDocument;
        $defaultValueNode = $dom->createElement( 'default-value' );
        $defaultValueNode->appendChild( $dom->createTextNode( $defaultValue ) );
        $attributeParametersNode->appendChild( $defaultValueNode );
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        // A package made without the element imports as an empty default
        // instead of a fatal error on null
        $defaultValueNode = $attributeParametersNode ? $attributeParametersNode->getElementsByTagName( 'default-value' )->item( 0 ) : null;
        $defaultValue = $defaultValueNode ? $defaultValueNode->textContent : '';
        $classAttribute->setAttribute( 'data_text1', $defaultValue );
    }

    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        // Empty or broken stored XML exports the attribute without content:
        // loadXML( '' ) is a ValueError and a broken document has no root
        $xmlString = $objectAttribute->attribute( 'data_text' );
        if ( is_string( $xmlString ) && trim( $xmlString ) !== '' )
        {
            $dom = new DOMDocument( '1.0', 'utf-8' );
            $previous = libxml_use_internal_errors( true );
            $success = $dom->loadXML( $xmlString );
            libxml_clear_errors();
            libxml_use_internal_errors( $previous );
            if ( $success && $dom->documentElement )
            {
                $importedRoot = $node->ownerDocument->importNode( $dom->documentElement, true );
                $node->appendChild( $importedRoot );
            }
        }

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $rootNode = $attributeNode->getElementsByTagName( 'ezmultioption' )->item( 0 );
        $xmlString = $rootNode ? $rootNode->ownerDocument->saveXML( $rootNode ) : '';
        $objectAttribute->setAttribute( 'data_text', $xmlString );
    }

    /*!
     \static
     \return the post variable \a $name as a list of strings, indexed from 0:
     an empty list when it is missing, a one-element list for a single value,
     and '' for an element that is itself an array.
    */
    static function postedList( $http, $name )
    {
        if ( !$http->hasPostVariable( $name ) )
            return array();
        $value = $http->postVariable( $name );
        if ( !is_array( $value ) )
            $value = array( $value );
        $list = array();
        foreach ( $value as $item )
            $list[] = is_scalar( $item ) ? (string)$item : '';
        return $list;
    }

    /*!
     \static
     \return the post variable \a $name as a string, '' when it is missing or
     is not a single value.
    */
    static function postedString( $http, $name )
    {
        if ( !$http->hasPostVariable( $name ) )
            return '';
        $value = $http->postVariable( $name );
        return is_scalar( $value ) ? (string)$value : '';
    }
}

eZDataType::register( eZMultiOptionType::DATA_TYPE_STRING, "eZMultiOptionType" );

?>
