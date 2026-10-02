<?php
/**
 * File containing the eZSimplifiedXMLInput class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class eZSimplifiedXMLInput extends eZXMLInputHandler
{
    public function __construct( $xmlData, $aliasedType, $contentObjectAttribute )
    {
        parent::__construct( $xmlData, $aliasedType, $contentObjectAttribute );

        $this->IsInputValid = true;
        $this->ContentObjectAttribute = $contentObjectAttribute;

        $contentIni = eZINI::instance( 'content.ini' );
    }

    /*!
      Updates URL - object links.
    */
    static function updateUrlObjectLinks( $contentObjectAttribute, $urlIDArray )
    {
        $objectAttributeID = $contentObjectAttribute->attribute( 'id' );
        $objectAttributeVersion = $contentObjectAttribute->attribute('version');

        // Ensure links that have been removed from the attribute version are removed from the object link table
        $linkObjectLinkList = eZURLObjectLink::fetchLinkObjectList( $objectAttributeID, $objectAttributeVersion );
        foreach ( $linkObjectLinkList as $linkObjectLink ) {
            if ( !in_array( $linkObjectLink->attribute( 'url_id' ), $urlIDArray ) ) {
                $linkObjectLink->remove();
            }
        }

        // Ensure links that have been added to the attribute version are added to the object link table
        foreach( $urlIDArray as $urlID )
        {
            $linkObjectLink = eZURLObjectLink::fetch( $urlID, $objectAttributeID, $objectAttributeVersion );
            if ( $linkObjectLink == null )
            {
                $linkObjectLink = eZURLObjectLink::create( $urlID, $objectAttributeID, $objectAttributeVersion );
                $linkObjectLink->store();
            }
        }
    }

    /*!
     Validates the input and returns true if the input was valid for this datatype.
    */
    function validateInput( $http, $base, $contentObjectAttribute )
    {
        $contentObjectID = $contentObjectAttribute->attribute( 'contentobject_id' );
        $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
        $contentObjectAttributeVersion = $contentObjectAttribute->attribute('version');
        if ( $http->hasPostVariable( $base . '_data_text_' . $contentObjectAttributeID ) )
        {
            $data = $http->postVariable( $base . '_data_text_' . $contentObjectAttributeID );

            // The editor posts one text; anything else (an array from a
            // crafted form) is refused instead of reaching the parser
            if ( !is_string( $data ) )
            {
                $GLOBALS['originalInput_' . $contentObjectAttributeID] = '';
                $GLOBALS['isInputValid_' . $contentObjectAttributeID] = false;
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Invalid input.' ) );
                return eZInputValidator::STATE_INVALID;
            }

            // Set original input to a global variable
            $originalInput = 'originalInput_' . $contentObjectAttributeID;
            $GLOBALS[$originalInput] = $data;

            // Set input valid true to a global variable
            $isInputValid = 'isInputValid_' . $contentObjectAttributeID;
            $GLOBALS[$isInputValid] = true;

            $text = $data;

            $text = preg_replace('/\r/', '', $text);
            $text = preg_replace('/\t/', ' ', $text);

            // first empty paragraph
            $text = preg_replace('/^\n/', '<p></p>', $text );

            eZDebugSetting::writeDebug( 'kernel-datatype-ezxmltext', $text, 'eZSimplifiedXMLInput::validateInput text' );

            $parser = new eZSimplifiedXMLInputParser( $contentObjectID, true, eZXMLInputParser::ERROR_ALL, true );
            $document = $parser->process( $text );

            if ( !is_object( $document ) )
            {
                $GLOBALS[$isInputValid] = false;
                $errorMessage = implode( ' ', $parser->getMessages() );
                $contentObjectAttribute->setValidationError( $errorMessage );
                return eZInputValidator::STATE_INVALID;
            }

            if ( $contentObjectAttribute->validateIsRequired() )
            {
                $root = $document->documentElement;
                if ( !$root->hasChildNodes() )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'Content required' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            $contentObjectAttribute->setValidationLog( $parser->getMessages() );

            $xmlString = eZXMLTextType::domString( $document );

            $urlIDArray = $parser->getUrlIDArray();

            if ( count( $urlIDArray ) > 0 )
            {
                $this->updateUrlObjectLinks( $contentObjectAttribute, $urlIDArray );
            }

            $contentObject = $contentObjectAttribute->attribute( 'object' );
            $contentObject->appendInputRelationList( $parser->getRelatedObjectIDArray(), eZContentObject::RELATION_EMBED );
            $contentObject->appendInputRelationList( $parser->getLinkedObjectIDArray(), eZContentObject::RELATION_LINK );

            $contentObjectAttribute->setAttribute( 'data_text', $xmlString );
            return eZInputValidator::STATE_ACCEPTED;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Returns the input XML representation of the datatype.
    */
    function inputXML()
    {
        $contentObjectAttribute = $this->ContentObjectAttribute;
        $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );

        $originalInput = 'originalInput_' . $contentObjectAttributeID;
        $isInputValid = 'isInputValid_' . $contentObjectAttributeID;

        if ( isset( $GLOBALS[$isInputValid] ) and $GLOBALS[$isInputValid] == false )
        {
            $output = $GLOBALS[$originalInput];
        }
        else
        {
            // Stored XML that is empty or does not parse (loadXML( '' )
            // throws, a broken document has no root) opens the editor with
            // the text it still contains instead of a fatal error
            $dom = new DOMDocument( '1.0', 'utf-8' );
            $useInternalErrors = libxml_use_internal_errors( true );
            $success = is_string( $this->XMLData ) && trim( $this->XMLData ) !== '' && $dom->loadXML( $this->XMLData );
            libxml_clear_errors();
            libxml_use_internal_errors( $useInternalErrors );
            if ( !$success || !$dom->documentElement )
            {
                return is_string( $this->XMLData ) ? trim( strip_tags( $this->XMLData ) ) : '';
            }

            $editOutput = new eZSimplifiedXMLEditOutput();
            $dom->formatOutput = true;
            if ( eZDebugSetting::isConditionTrue( 'kernel-datatype-ezxmltext', eZDebug::LEVEL_DEBUG ) )
                eZDebug::writeDebug( $dom->saveXML(), eZDebugSetting::changeLabel( 'kernel-datatype-ezxmltext', __METHOD__ . ' xml string stored in database' ) );
            $output = $editOutput->performOutput( $dom );
        }
        return $output;
    }

    public $IsInputValid;
}

?>
