<?php
/**
 * File containing the eZSubtreeSubscriptionType class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZSubtreeSubscriptionType ezsubtreesubscriptiontype.php
  \ingroup eZDatatype
  \brief The class eZSubtreeSubscriptionType does

*/
class eZSubtreeSubscriptionType extends eZDataType
{
    const DATA_TYPE_STRING = "ezsubtreesubscription";

    public function __construct()
    {
        parent::__construct(  self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Subtree subscription", 'Datatype name' ),
                            array( 'serialize_supported' => true,
                                   'object_serialize_map' => array( 'data_int' => 'value' ) ) );
    }


    /*!
     Store content
    */
    function onPublish( $attribute, $contentObject, $publishedNodes )
    {
        $user = eZUser::currentUser();
        // The anonymous account is shared by every visitor: a subscription made
        // for it (content published through an anonymous form) would send
        // notifications to that shared account instead of to a person.
        if ( !$user instanceof eZUser || $user->isAnonymous() || !is_array( $publishedNodes ) )
        {
            return true;
        }
        $userID = $user->attribute( 'contentobject_id' );

        $nodeIDList = eZSubtreeNotificationRule::fetchNodesForUserID( $user->attribute( 'contentobject_id' ), false );

        if ( $attribute->attribute( 'data_int' ) == '1' )
        {
            $newSubscriptions = array();
            foreach ( $publishedNodes as $node )
            {
                if ( !in_array( $node->attribute( 'node_id' ), $nodeIDList ) )
                {
                    $newSubscriptions[] = $node->attribute( 'node_id' );
                }
            }

            foreach ( $newSubscriptions as $nodeID )
            {

                $rule = eZSubtreeNotificationRule::create( $nodeID, $userID );
                $rule->store();
            }
        }
        else
        {
            foreach ( $publishedNodes as $node )
            {
                if ( in_array( $node->attribute( 'node_id' ), $nodeIDList ) )
                {
                    eZSubtreeNotificationRule::removeByNodeAndUserID( $user->attribute( 'contentobject_id' ), $node->attribute( 'node_id' ) );
                }
            }
        }
        return true;
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_subtreesubscription_" . $contentObjectAttribute->attribute( "id" ) ))
        {
            $data = $http->postVariable( $base . "_data_subtreesubscription_" . $contentObjectAttribute->attribute( "id" ) );
            if ( isset( $data ) )
                $data = 1;
        }
        else
        {
            $data = 0;
        }
        $contentObjectAttribute->setAttribute( "data_int", $data );
        return true;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return true;
    }

    function toString( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_int' );
    }


    function fromString( $contentObjectAttribute, $string )
    {
        if ( $string == '' )
            return true;
        if ( !is_numeric( $string ) )
            return false;

        // The value is a yes/no flag; toString() writes 0 or 1. Another number
        // ("2", "1e3") is taken as yes, the way the view template shows it,
        // instead of being stored as it is.
        $contentObjectAttribute->setAttribute( 'data_int', self::flagValue( $string ) );
        return true;
    }

    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );
        $dom = $node->ownerDocument;

        $value = $objectAttribute->attribute( 'data_int' );
        $valueNode = $dom->createElement( 'value' );
        $valueNode->appendChild( $dom->createTextNode( $value ) );
        $node->appendChild( $valueNode );

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $valueNode = $attributeNode->getElementsByTagName( 'value' )->item( 0 );
        $value = $valueNode ? self::flagValue( $valueNode->textContent ) : 0;
        $objectAttribute->setAttribute( 'data_int', $value );
    }

    function diff( $old, $new, $options = false )
    {
        return null;
    }

    /*!
     \private
     \return 1 for a non-zero number, 0 for anything else.
    */
    static function flagValue( $value )
    {
        if ( !is_scalar( $value ) )
            return 0;
        $value = trim( (string)$value );
        return ( is_numeric( $value ) && (float)$value != 0 ) ? 1 : 0;
    }
}

eZDataType::register( eZSubtreeSubscriptionType::DATA_TYPE_STRING, "eZSubtreeSubscriptionType" );

?>
