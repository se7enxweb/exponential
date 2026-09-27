<?php
/**
 * File containing the eZMultiOption2Type class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZMultiOption2Type ezmultioption2type.php
  \ingroup eZDatatype
  \brief A datatype which works with multiple options.

  This allows the user to add several option choices almost as if he
  was adding attributes with option datatypes.

  This class implements the interface for a datatype but passes
  most of the work over to the eZMultiOption2 class which handles
  parsing, storing and manipulation of multioption2s and options.

  This datatype supports:
  - fetch and validation of HTTP data
  - search indexing
  - product option information
  - class title
  - class serialization

*/

class eZMultiOption2Type extends eZDataType
{
    const DEFAULT_NAME_VARIABLE = "_ezmultioption2_default_name_";
    const MAX_CHILD_LEVEL = 50;
    const DATA_TYPE_STRING = "ezmultioption2";

    /*!
     Constructor to initialize the datatype.
    */
    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Multi-option2", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    /*!
     Validates the input for this datatype.
     \return True if input is valid.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     This function calles xmlString function to create xml string and then store the content.
    */
    function storeObjectAttribute( $contentObjectAttribute )
    {
        $optiongroup = $contentObjectAttribute->content();
        $contentObjectAttribute->setAttribute( "data_text", $optiongroup->xmlString() );
    }

    /*!
     \return An eZMultiOption2 object which contains all the option data
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $optiongroup = new eZMultiOption2( "" );
        $optiongroup->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
        return $optiongroup;
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
        $editMode = self::postedString( $http, $base . "_data_edit_mode_" . $contentObjectAttribute->attribute( "id" ) );
        if( $editMode == 'rules' )
        {
            $optiongroup = $contentObjectAttribute->content();

            // All lists go through postedList(): a missing list or a single
            // value in place of one was a TypeError in foreach/count()
            $optionRulesArray = self::postedList( $http, $base . "_data_multioption_rule_for" );
            $rules = array();
            foreach( $optionRulesArray  as $ruleFor )
            {
                $parentMultioptionIDList = self::postedList( $http, $base . "_data_rule_parent_multioption_id_" . $ruleFor );
                $rule = array();
                foreach ( $parentMultioptionIDList as $parentMultioptionID )
                {
                    // A multioption id that is not in the set has no rule (it
                    // was a fatal error on null)
                    $parentMultioptionData = $optiongroup->findMultiOption( $parentMultioptionID );
                    if ( !$parentMultioptionData )
                        continue;
                    $parentMultioptionGroup = $parentMultioptionData['group'];

                    $parentMultioptionOptionList = $parentMultioptionGroup->Options[$parentMultioptionData['id']]['optionlist'];

                    $ruleData = self::postedList( $http, $base . "_data_multioption_rule_" . $ruleFor . '_' . $parentMultioptionID );
                    if ( count( $parentMultioptionOptionList ) == count( $ruleData ) )
                        continue;
                    $rule[$parentMultioptionID] = $ruleData;
                }
                if ( count( $rule ) )
                    $rules[$ruleFor] = $rule;
            }
            $optiongroup->Rules = $rules;
            $contentObjectAttribute->setContent( $optiongroup );
            return true;
        }

        $optiongroup = new eZMultiOption2( '' );

        $oldoptiongroup = $contentObjectAttribute->content();
        if ( $oldoptiongroup->Rules )
           $optiongroup->Rules = $oldoptiongroup->Rules;
        $this->fetchHTTPInputForGroup( $optiongroup, $http, $base, $contentObjectAttribute );
        $optiongroup->initCountersRecursive();
        $contentObjectAttribute->setContent( $optiongroup );
        return true;
    }

    function fetchHTTPInputForGroup( $parentOptionGroup, $http, $base, $contentObjectAttribute, $depth = 0, &$seenGroupIDs = array() )
    {

        // Every field goes through postedList()/postedString(): a missing
        // list, a single value in place of one or an array in place of a text
        // field was a TypeError, an undefined offset or "Array" in the XML
        $optionGroupIDArray = self::postedList( $http, $base . "_data_optiongroup_id_" .
                                                      $contentObjectAttribute->attribute( "id" ) . '_' .
                                                      $parentOptionGroup->attribute( 'group_id' ) );
        $optionGroupNameList = self::postedList( $http, $base . "_data_optiongroup_name_" .
                                    $contentObjectAttribute->attribute( "id" )  . '_' .
                                    $parentOptionGroup->attribute( 'group_id' ) );
        foreach ( $optionGroupIDArray as $key => $optionGroupID )
        {
            // A group id is a number; anything else would become the group
            // id in the XML and in the names of the next level's fields
            if ( !is_numeric( $optionGroupID ) )
                continue;
            // Each group is read once. The form never posts a group twice; a
            // request that lists a group under itself, or the same group many
            // times at each level, built a copy per path, and that count grows
            // exponentially with the depth
            if ( isset( $seenGroupIDs[$optionGroupID] ) )
                continue;
            $seenGroupIDs[$optionGroupID] = true;
            unset( $optionGroup );
            $optionGroup = new eZMultiOption2( isset( $optionGroupNameList[$key] ) ? $optionGroupNameList[$key] : '', 0,0,0, $optionGroupID );

            if ( $http->hasPostVariable( $base . "_data_optiongroup_id_parent_multioption_" .
                                                      $contentObjectAttribute->attribute( "id" ) . '_' .
                                                      $optionGroupID ) )
            {
               $parentMultioptionID = self::postedString( $http, $base . "_data_optiongroup_id_parent_multioption_" .
                                                             $contentObjectAttribute->attribute( "id" ) . '_' .
                                                             $optionGroupID );
               $parentOptionGroup->addChildGroup( $optionGroup, $parentMultioptionID );
            }
            else
            {
                $parentOptionGroup->addChildGroup( $optionGroup );
            }
            if ( $optionGroupID == 0 )
                continue ;

            $multioptionIDArray = self::postedList( $http, $base . "_data_multioption_id_" .
                                                          $contentObjectAttribute->attribute( "id" ) . '_' .
                                                          $optionGroupID );
            foreach( $multioptionIDArray as $multioptionID )
            {

                $multioptionName = self::postedString( $http, $base . "_data_multioption_name_" .
                                                        $contentObjectAttribute->attribute( "id" ). '_' .
                                                        $optionGroupID . '_' . $multioptionID );

                $defaultValue = self::postedString( $http, $base . "_data_default_option_" .
                                                        $contentObjectAttribute->attribute( "id" ). '_' .
                                                        $optionGroupID . '_' . $multioptionID );

                $multiOptionPriority = 0;

                $newID = $optionGroup->addMultiOption( $multioptionName, $multiOptionPriority, $defaultValue, $multioptionID  );


                $optionCountArray = self::postedList( $http, $base . "_data_option_option_id_" .
                                                            $contentObjectAttribute->attribute( "id" ) . '_' .
                                                            $optionGroupID . '_' .  $multioptionID );

                $optionValueArray = self::postedList( $http, $base . "_data_option_value_" .
                                                            $contentObjectAttribute->attribute( "id" ) . '_' .
                                                            $optionGroupID . '_' .  $multioptionID );
                $optionAdditionalPriceArray = self::postedList( $http, $base . "_data_option_additional_price_" .
                                                            $contentObjectAttribute->attribute( "id" ) . '_' .
                                                            $optionGroupID . '_' .  $multioptionID );
                for ( $i = 0; $i < count( $optionCountArray ); $i++ )
                {
                    $optionValueArray += array( $i => '' );
                    $optionAdditionalPriceArray += array( $i => '' );
                    $isSelectable = $http->hasPostVariable( $base . "_data_option_is_selectable_" .
                                                            $contentObjectAttribute->attribute( "id" ) . '_' .
                                                            $optionGroupID . '_' .  $multioptionID . '_' . $optionCountArray[$i] )
                         ? 0 : 1;
                    $objectID = $http->hasPostVariable( $base . "_data_option_object_" .
                                                            $contentObjectAttribute->attribute( "id" ) . '_' .
                                                            $optionGroupID . '_' .  $multioptionID . '_' . $optionCountArray[$i] )
                                ? self::postedString( $http, $base . "_data_option_object_" .
                                                            $contentObjectAttribute->attribute( "id" ) . '_' .
                                                            $optionGroupID . '_' .  $multioptionID . '_' . $optionCountArray[$i] ) : 0;
                    // The related image is an object id
                    $objectID = is_numeric( $objectID ) ? (int)$objectID : 0;
                    $optionGroup->addOption( $newID, $optionCountArray[$i], $optionValueArray[$i], $optionAdditionalPriceArray[$i], $isSelectable, $objectID );
                }
            }
            // A group that lists itself (or a loop of groups) in the posted
            // form nests without end: the input stops at MAX_CHILD_LEVEL. This
            // used to die(), which ended the request with a bare message
            if ( $depth >= self::MAX_CHILD_LEVEL )
                eZDebug::writeWarning( 'Option groups nested deeper than ' . self::MAX_CHILD_LEVEL . ' levels, the deeper levels are ignored', __METHOD__ );
            else
                $this->fetchHTTPInputForGroup( $optionGroup, $http, $base, $contentObjectAttribute, $depth+1, $seenGroupIDs );

            $parentOptionGroup->initCounters( $optionGroup );

        }

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
        // "<name>_<group id>_<multioption id>_<option>" from the button name;
        // the parts a hand-made request leaves out read as '' instead of
        // undefined offsets
        $actionlist = explode( "_", $action ) + array( '', '', '', '' );
        if ( $actionlist[0] == "new-option" )
        {
            $rootGroup = $contentObjectAttribute->content();
            $groupID = $actionlist[1];
            $multioptionID = $actionlist[2];
            $optionID = $rootGroup->addOptionForMultioptionID( $multioptionID,  "", "", "" );

            // No such multioption: nothing was added. No such group: the counters
            // are taken from the whole set only (initCounters( null ) was a
            // fatal error)
            if ( $optionID === null )
                return;
            $group = $rootGroup->findGroup( $groupID );
            if ( $group )
                $rootGroup->initCounters( $group );

            $rootGroup->addOptionToRules( $multioptionID, $optionID );

            $rootGroup->initCountersRecursive();
            $contentObjectAttribute->setContent( $rootGroup );
            $contentObjectAttribute->store();
        }
        else if ( $actionlist[0] == "new-sublevel" )
        {
            $rootGroup = $contentObjectAttribute->content();
            $groupID = $actionlist[1];
            $multioptionID = $actionlist[2];

            $group = $rootGroup->findGroup( $groupID );
            if ( !$group )
                return;
            $childGroup = new eZMultiOption2( '', 0, $rootGroup->getMultiOptionIDCounter(),$rootGroup->getOptionCounter() );

            $group->addChildGroup( $childGroup, $multioptionID );

            $newID = $childGroup->addMultiOption( "", 0, false , '' );
            $childGroup->addOption( $newID, "", "", "" );
            $rootGroup->initCounters( $childGroup );

            $rootGroup->initCountersRecursive();

            $contentObjectAttribute->setContent( $rootGroup );
            $contentObjectAttribute->store();
        }
        else if ( $actionlist[0] == "new-group" )
        {
            $rootGroup = $contentObjectAttribute->content();
            $childGroup = new eZMultiOption2( '', 0, $rootGroup->getMultiOptionIDCounter(),$rootGroup->getOptionCounter() );
            $rootGroup->addChildGroup( $childGroup );
            $newID = $childGroup->addMultiOption( "", 0, false , '' );
            $childGroup->addOption( $newID, "", "", "" );
            $rootGroup->initCounters( $childGroup );
            $rootGroup->initCountersRecursive();

            $contentObjectAttribute->setContent( $rootGroup );
            $contentObjectAttribute->store();
        }
        else if ( $actionlist[0] == "remove-selected-option" )
        {
            $rootGroup = $contentObjectAttribute->content();
            $groupID = $actionlist[1];
            $multioptionID = $actionlist[2];
            $postvarname = "ContentObjectAttribute" . "_data_option_remove_" . $contentObjectAttribute->attribute( "id" ) . "_" .
                            $groupID . "_" . $multioptionID;
            $array_remove = $http->hasPostVariable( $postvarname ) ? $http->postVariable( $postvarname ) : array();

            $group = $rootGroup->findGroup( $groupID );

            if ( $group )
            {
                $group->removeOptions( $array_remove, $multioptionID );
                $rootGroup->cleanupRules();
                $contentObjectAttribute->setContent( $rootGroup );
                $contentObjectAttribute->store();
            }
        }
        else if ( $actionlist[0] == "new-multioption" )
        {
            $rootGroup = $contentObjectAttribute->content();
            $groupID = $actionlist[1];
            $group = $rootGroup->findGroup( $groupID );
            if ( !$group )
                return;

            $newID = $group->addMultiOption( "" ,0,false, '' );
            $group->addOption( $newID, "", "", "" );
            $group->addOption( $newID, "" ,"", "" );
            $rootGroup->initCounters( $group );
            $rootGroup->initCountersRecursive();
            $contentObjectAttribute->setContent( $rootGroup );
            $contentObjectAttribute->store();

        }
        else if ( $actionlist[0] == "remove-selected-multioption" )
        {
            $rootGroup = $contentObjectAttribute->content();
            $groupID = $actionlist[1];
            $postvarname = "ContentObjectAttribute" . "_data_multioption_remove_" . $contentObjectAttribute->attribute( "id" ) . "_" . $groupID;
            $array_remove = $http->hasPostVariable( $postvarname ) ? $http->postVariable( $postvarname ) : array();

            $group = $rootGroup->findGroup( $groupID );

            if ( $group )
            {
                $group->removeMultiOptions( $array_remove );
                $rootGroup->cleanupRules();
                if ( count( $group->attribute( 'multioption_list') ) == 0 )
                {
                    $rootGroup->removeChildGroup( $group->attribute( 'group_id' ) );
                }
                $contentObjectAttribute->setContent( $rootGroup );
                $contentObjectAttribute->store();
            }

        }
        else if ( $actionlist[0] == "switch-mode" )
        {
            $editMode = $actionlist[1];
            $sessionVarName = 'eZEnhancedMultioption_edit_mode_' . $contentObjectAttribute->attribute( 'id' );
            $http->setSessionVariable( $sessionVarName, $editMode );
            $rootGroup = $contentObjectAttribute->content();
            $contentObjectAttribute->setContent( $rootGroup );
        }
        else if ( $actionlist[0] == "reset-rules" )
        {
            $rootGroup = $contentObjectAttribute->content();
            $rootGroup->Rules = array();
            $contentObjectAttribute->setContent( $rootGroup );
            $contentObjectAttribute->store();

        }
        else if ( $actionlist[0] == "set-object" )
        {

            if ( $http->hasPostVariable( 'BrowseActionName' ) and
                 $http->postVariable( 'BrowseActionName' ) == ( 'AddRelatedObject_' . $contentObjectAttribute->attribute( 'id' ) ) and
                 $http->hasPostVariable( "SelectedObjectIDArray" ) )
            {
                if ( !$http->hasPostVariable( 'BrowseCancelButton' ) )
                {
                    $selectedObjectIDArray = $http->postVariable( "SelectedObjectIDArray" );
                    $rootGroup = $contentObjectAttribute->content();
                    $groupID = $actionlist[1];
                    $multioptionID = $actionlist[2];
                    $optionID = $actionlist[3];

                    // The browse result is a list of object ids; a single value,
                    // an empty list or text is no object (string offset or
                    // undefined offset before)
                    $objectID = is_array( $selectedObjectIDArray ) ? reset( $selectedObjectIDArray ) : $selectedObjectIDArray;
                    if ( !is_scalar( $objectID ) || !is_numeric( $objectID ) || (int)$objectID <= 0 )
                        return;
                    $objectID = (int)$objectID;

                    $rootGroup->setObjectForOption(  $multioptionID,  $optionID, $objectID );
                    $contentObjectAttribute->setContent( $rootGroup );
                    $contentObjectAttribute->store();

                }
           }
        }
        else if ( $actionlist[0] == "browse-object" )
        {
            // The ids are carried through the browse page in a button name:
            // only numbers, as the edit form sends them
            if ( !is_numeric( $actionlist[1] ) || !is_numeric( $actionlist[2] ) || !is_numeric( $actionlist[3] ) )
                return;
            $module = $parameters['module'];

            $redirectionURI = $parameters['current-redirection-uri'];
            $ini = eZINI::instance( 'content.ini' );

            $browseType = 'AddRelatedObjectToDataType';
            $browseTypeINIVariable = $ini->variable( 'ObjectRelationDataTypeSettings', 'ClassAttributeStartNode' );
            foreach( (array)$browseTypeINIVariable as $value )
            {
                // An entry without ";" is skipped (it was an undefined offset)
                $parts = explode( ';', (string)$value, 2 );
                if ( count( $parts ) < 2 )
                    continue;
                list( $classAttributeID, $type ) = $parts;
                if ( $classAttributeID == $contentObjectAttribute->attribute( 'contentclassattribute_id' ) && strlen( $type ) > 0 )
                {
                    $browseType = $type;
                    break;
                }
            }
            eZContentBrowse::browse( array( 'action_name' => 'AddRelatedObject_' . $contentObjectAttribute->attribute( 'id' ),
                                            'type' =>  $browseType,
                                            'browse_custom_action' => array( 'name' => 'CustomActionButton[' . $contentObjectAttribute->attribute( 'id' ) .
                                                                             '_set-object_'. $actionlist[1] . '_' . $actionlist[2] . '_' .$actionlist[3] . ']',
                                                                             'value' => $contentObjectAttribute->attribute( 'id' ) ),
                                                'persistent_data' => array( 'HasObjectInput' => 0 ),
                                                'from_page' => $redirectionURI ),
                                         $module );

        }
        else if ( $actionlist[0] == "remove-object" )
        {


            $rootGroup = $contentObjectAttribute->content();
            $groupID = $actionlist[1];
            $multioptionID = $actionlist[2];
            $optionID = $actionlist[3];

            $rootGroup->removeObjectFromOption( $multioptionID, $optionID );

            $contentObjectAttribute->setContent( $rootGroup );
            $contentObjectAttribute->store();


        }
        else
        {
            eZDebug::writeError( "Unknown custom HTTP action: " . $action, "eZMultiOptionType" );
        }
    }

    /*!
     Finds the option which has the correct ID , if found it returns an option structure.
    */
    function productOptionInformation( $objectAttribute, $optionID, $productItem )
    {
        $optiongroup = $objectAttribute->attribute( 'content' );

        $option = $optiongroup->findOption( false, $optionID );
        if ( $option )
        {
            return array( 'id' => $option['option_id'],
                          'name' => $option['multioption_name'],
                          'value' => $option['value'],
                          'additional_price' => $option['additional_price'] );
        }
        return null;
    }


    /*!
      \return \c true if there are more than one multioption in the list.
    */
    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $groups = $contentObjectAttribute->content();
        $grouplist = $groups->attribute( 'optiongroup_list' );
        return count( $grouplist ) > 0;
    }

    /*!
     Sets default multioption values.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion == false )
        {
            $optiongroup = $contentObjectAttribute->content();
            if ( $optiongroup )
            {
                $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
                $optiongroup->setName( $contentClassAttribute->attribute( 'data_text1' ) );
                $contentObjectAttribute->setAttribute( "data_text", $optiongroup->xmlString() );
                $contentObjectAttribute->setContent( $optiongroup );
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
        return false;
    }


    /*!
     Validates the input for an object attribute during add to basket process
     and returns a validation state as defined in eZInputValidator.
    */
    function validateAddToBasket( $objectAttribute, $data, &$errors )
    {

        $optiongroup = $objectAttribute->attribute( 'content' );
        $rules = $optiongroup->Rules;

        $validationErrors = array();


        // $data is the eZOption[attribute id] array a shop visitor posts with
        // "add to basket": anything else than a list of multioption id =>
        // option id is refused. A multioption or option that is not in the set
        // was a fatal error on null and warnings on false, for any visitor
        if ( !is_array( $data ) )
            $data = array();
        foreach( $data as $moptionID => $selectedItem )
        {
            $failedOptionData = is_scalar( $selectedItem ) ? $optiongroup->findMultiOption( $moptionID ) : null;
            $failedMultiOption = $failedOptionData ? $failedOptionData['group']->Options[$failedOptionData['id']] : null;
            $failedOption = $failedMultiOption ? $optiongroup->findOption( $failedMultiOption, $selectedItem ) : false;
            if ( !$failedOption )
            {
                $objectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                              "You cannot choose option value \"%1\" from \"%2\" because it is unselectable " ),
                                                      '',
                                                      $failedMultiOption ? $failedMultiOption['name'] : '' );
                $validationErrors[] = $objectAttribute->attribute( 'validation_error' );
                continue;
            }
            if ( $failedOption['is_selectable'] != 1 )
            {
                $objectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                              "You cannot choose option value \"%1\" from \"%2\" because it is unselectable " ),
                                                      $failedOption['value'],
                                                      $failedMultiOption['name'] );
                $validationErrors[] = $objectAttribute->attribute( 'validation_error' );
            }
            if ( ! isset( $rules[$selectedItem] ) )
                continue;

            $rulesForOption = $rules[$selectedItem];
            foreach( array_keys( $rulesForOption ) as $ruleKey )
            {

                if ( isset( $data[$ruleKey] ) )
                {
                    $selectedMultioptionValue = $data[$ruleKey];
                    $canSelectIfIdArray = $rulesForOption[$ruleKey];
                    if ( !in_array( $data[$ruleKey], (array)$canSelectIfIdArray  ) )
                    {
                        $wrongParentData =  $optiongroup->findMultiOption( $ruleKey );
                        $wrongParentMultiOption = $wrongParentData ? $wrongParentData['group']->Options[$wrongParentData['id']] : array( 'name' => '' );

                        $wrongParentOptionID = $data[$ruleKey];
                        $wrongParentOption = $wrongParentData ? $optiongroup->findOption( $wrongParentMultiOption, $wrongParentOptionID ) : false;
                        if ( !$wrongParentOption )
                            $wrongParentOption = array( 'value' => '' );

                        $objectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                      "You cannot choose option value \"%1\" from \"%2\"  \n if you selected option \"%3\" from \"%4\" " ),
                                                              $failedOption['value'],
                                                              $failedMultiOption['name'],
                                                              $wrongParentOption['value'],
                                                              $wrongParentMultiOption['name'] );
                        $validationErrors[] = $objectAttribute->attribute( 'validation_error' );

                    }
                }
            }
        }

        if ( count( $validationErrors ) > 0 )
        {
            $errors = $validationErrors;
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     \return true if the datatype requires validation during add to basket procedure
    */
    function isAddToBasketValidationRequired()
    {
        return true;
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
                $importedNode = $node->ownerDocument->importNode( $dom->documentElement, true );
                $node->appendChild( $importedNode );
            }
        }

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        // Without an <ezmultioption2> element the attribute is imported empty.
        // saveXML( null ) saved the whole package document instead, which was
        // then stored as this attribute's data
        $rootNode = $attributeNode->getElementsByTagName( 'ezmultioption2' )->item( 0 );
        $xmlString = $rootNode ? $attributeNode->ownerDocument->saveXML( $rootNode ) : '';
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

    /*!
     \return the template name to use for editing the attribute.
     \note Default is to return the datatype string which is OK
           for most datatypes, if you want dynamic templates
           reimplement this function and return a template name.
     \note The returned template name does not include the .tpl extension.
     \sa viewTemplate, informationTemplate
    */
    function editTemplate( $contentObjectAttribute )
    {
        $http = eZHTTPTool::instance();
        $sessionVarName = 'eZEnhancedMultioption_edit_mode_' . $contentObjectAttribute->attribute( 'id' );
        if ( $http->hasSessionVariable( $sessionVarName )  && $http->sessionVariable( $sessionVarName )  == 'rules' )
        {
            return $this->DataTypeString . '_rules';
        }
        return $this->DataTypeString;
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }

    function batchInitializeObjectAttributeData( $classAttribute )
    {
        $optionGroup = new eZMultiOption2( $classAttribute->attribute( 'data_text1' ) );
        $db = eZDB::instance();
        return array( 'data_text' => "'" . $db->escapeString( $optionGroup->xmlString() ) . "'" );
    }
}

eZDataType::register( eZMultiOption2Type::DATA_TYPE_STRING, "eZMultiOption2Type" );

?>
