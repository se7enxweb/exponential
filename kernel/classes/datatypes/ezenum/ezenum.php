<?php
/**
 * File containing the eZEnum class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZEnum ezenum.php
  \ingroup eZDatatype
  \brief The class eZEnum does

*/

class eZEnum
{
    /**
     * Constructor
     *
     * @param int $id
     * @param int $version
     */
    public function __construct( $id, $version )
    {
        $this->ClassAttributeID = $id;
        $this->ClassAttributeVersion = $version;
        $this->Enumerations = eZEnumValue::fetchAllElements( $this->ClassAttributeID, $this->ClassAttributeVersion );
        $this->IsmultipleEnum = null;
        $this->IsoptionEnum = null;
        $this->ObjectEnumerations = null;
    }

    function attributes()
    {
        return array( 'contentclass_attributeid',
                      'contentclass_attributeversion',
                      'enum_list',
                      'enumobject_list',
                      'enum_ismultiple',
                      'enum_isoption' );
    }

    function hasAttribute( $attr )
    {
        return in_array( $attr, $this->attributes() );
    }

    function attribute( $attr )
    {
        switch ( $attr )
        {
            case "contentclass_attributeid" :
            {
                return $this->ClassAttributeID;
            }break;
            case "contentclass_attributeversion" :
            {
                return $this->ClassAttributeVersion;
            }break;
            case "enum_list" :
            {
                return $this->Enumerations;
            }break;
            case "enumobject_list" :
            {
                return $this->ObjectEnumerations;
            }break;
            case "enum_ismultiple" :
            {
                return $this->IsmultipleEnum;
            }break;
            case "enum_isoption" :
            {
                return $this->IsoptionEnum;
            }break;
            default :
            {
                eZDebug::writeError( "Attribute '$attr' does not exist", __METHOD__ );
                return null;
            }break;
        }
    }

    function setObjectEnumValue( $contentObjectAttributeID, $contentObjectAttributeVersion ){
        $this->ObjectEnumerations = eZEnumObjectValue::fetchAllElements( $contentObjectAttributeID, $contentObjectAttributeVersion );
    }

    static function removeObjectEnumerations( $contentObjectAttributeID, $contentObjectAttributeVersion )
    {
         eZEnumObjectValue::removeAllElements( $contentObjectAttributeID, $contentObjectAttributeVersion );
    }

    static function storeObjectEnumeration( $contentObjectAttributeID, $contentObjectAttributeVersion, $enumID, $enumElement, $enumValue )
    {
        $enumobjectvalue = eZEnumObjectValue::create( $contentObjectAttributeID, $contentObjectAttributeVersion, $enumID, $enumElement, $enumValue );
        $enumobjectvalue->store();
    }

    function setIsmultipleValue( $value )
    {
        $this->IsmultipleEnum = $value;
    }

    function setIsoptionValue( $value )
    {
        $this->IsoptionEnum = $value;
    }

    function setValue( $array_enumid, $array_enumelement, $array_enumvalue, $version )
    {
        // The three lists come from the class edit form. Anything but arrays
        // made count() throw a TypeError, shorter lists gave undefined offsets,
        // and an id was fetched by id and version alone, so a posted id of an
        // element of another class attribute renamed that element. Only the
        // elements of this attribute are updated now, from the list it already
        // has (no query per posted id, which a huge form turned into thousands)
        if ( !is_array( $array_enumid ) or !is_array( $array_enumelement ) or !is_array( $array_enumvalue ) )
            return;
        $own = array();
        foreach ( (array)$this->Enumerations as $enum )
        {
            if ( $enum instanceof eZEnumValue and $enum->attribute( 'contentclass_attribute_version' ) == $version )
                $own[(int)$enum->attribute( 'id' )] = $enum;
        }
        $elements = array_values( $array_enumelement );
        $values = array_values( $array_enumvalue );
        $db = eZDB::instance();
        $db->begin();

        $changed = false;
        foreach ( array_values( $array_enumid ) as $i => $enumID )
        {
            if ( !is_scalar( $enumID ) or !isset( $own[(int)$enumID] ) )
                continue;
            $element = $elements[$i] ?? null;
            $value = $values[$i] ?? null;
            if ( !is_scalar( $element ) or !is_scalar( $value ) )
                continue;
            $enumvalue = $own[(int)$enumID];
            // The form limits both to 255 characters, as wide as the columns
            $enumvalue->setAttribute( "enumelement", mb_substr( (string)$element, 0, 255 ) );
            $enumvalue->setAttribute( "enumvalue", mb_substr( (string)$value, 0, 255 ) );
            $enumvalue->store();
            $changed = true;
        }
        if ( $changed )
            $this->Enumerations = eZEnumValue::fetchAllElements( $this->ClassAttributeID, $this->ClassAttributeVersion );
        $db->commit();
    }

    function setVersion( $version )
    {
        if ( $version == $this->ClassAttributeVersion )
            return;

        $db = eZDB::instance();
        $db->begin();

        eZEnumValue::removeAllElements( $this->ClassAttributeID, 0 );
        foreach( $this->Enumerations as $enum )
        {
            $oldversion = $enum->attribute ( "contentclass_attribute_version" );
            $id = $enum->attribute( "id" );
            $contentClassAttributeID = $enum->attribute( "contentclass_attribute_id" );
            $element = $enum->attribute( "enumelement" );
            $value = $enum->attribute( "enumvalue" );
            $placement = $enum->attribute( "placement" );
            $enumCopy = eZEnumValue::createCopy( $id,
                                                 $contentClassAttributeID,
                                                 0,
                                                 $element,
                                                 $value,
                                                 $placement );
            $enumCopy->store();
            if ( $oldversion != $version )
            {
                $enum->setAttribute("contentclass_attribute_version", $version );
                $enum->store();
            }
        }

        $this->Enumerations = eZEnumValue::fetchAllElements( $this->ClassAttributeID, $this->ClassAttributeVersion );

        $db->commit();
    }

    static function removeOldVersion( $id, $version )
    {
        eZEnumValue::removeAllElements( $id, $version );
    }

    /*!
     Adds an enumeration
    */
    function addEnumeration( $element )
    {
        $enumvalue = eZEnumValue::create( $this->ClassAttributeID, $this->ClassAttributeVersion, $element );
        $enumvalue->store();
        $this->Enumerations = eZEnumValue::fetchAllElements( $this->ClassAttributeID, $this->ClassAttributeVersion );
    }

    /*!
     Adds the enumeration value object \a $enumValue to the enumeration list.
    */
    function addEnumerationValue( $enumValue )
    {
        $this->Enumerations[] = $enumValue;
    }

    function removeEnumeration( $id, $enumid, $version )
    {
       eZEnumValue::removeByID( $enumid, $version );
       $this->Enumerations = eZEnumValue::fetchAllElements( $id, $version );
    }

    public $Enumerations;
    public $ObjectEnumerations;
    public $ClassAttributeID;
    public $ClassAttributeVersion;
    public $IsmultipleEnum;
    public $IsoptionEnum;
}

?>
