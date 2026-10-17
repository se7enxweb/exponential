<?php
/**
 * File containing the eZXMLInputHandler class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZXMLInputHandler ezxmlinputhandler.php
  \ingroup eZDatatype
  \brief The class eZXMLInputHandler does

*/

class eZXMLInputHandler
{
    /**
     * Constructor
     *
     * @param string $xmlData
     * @param string $aliasedType
     * @param eZContentObjectAttribute $contentObjectAttribute
     */
    public function __construct( $xmlData, $aliasedType, $contentObjectAttribute )
    {
        // XML 1.0 also allows #x10000-#x10FFFF. Without that range every character beyond U+FFFF vanished without a
        // replacement when the text was opened in the editor: "&lt;mi&gt;\u{1D465}&lt;/mi&gt;" in custom:mathml became
        // "&lt;mi&gt;&lt;/mi&gt;", "\u{1D538} = \mathbb{A}" in a formula " = \mathbb{A}", "a \u{1F600} b" became "a b".
        // The range is kept only where the database can store it, see supplementaryCharactersAllowed().
        $this->XMLData = self::replaceCharactersXMLDoesNotAllow( $xmlData, '', $count );
        if ( $count > 0 )
        {
            eZDebug::writeWarning( "$count invalid character(s) detected. They have been removed from input.", __METHOD__ );
        }
        $this->ContentObjectAttribute = $contentObjectAttribute;
        $this->AliasedHandler = null;
        // use of $aliasedType is deprecated as of 4.1 and setting is ignored  in aliased_handler
        $this->AliasedType = $aliasedType;
    }

    /*!
     \return an array with attribute names.
    */
    function attributes()
    {
        return array( 'input_xml',
                      'aliased_type',
                      'aliased_handler',
                      'edit_template_name',
                      'information_template_name' );
    }

    /*!
     \return true if the attribute \a $name exists.
    */
    function hasAttribute( $name )
    {
        return in_array( $name, $this->attributes() );
    }

    /*!
     \return the value of the attribute \a $name if it exists, if not returns \c null.
    */
    function attribute( $name )
    {
        switch ( $name )
        {
            case 'input_xml':
            {
                return $this->inputXML();
            } break;
            case 'edit_template_name':
            {
                return $this->editTemplateName();
            }break;
            case 'information_template_name':
            {
                return $this->informationTemplateName();
            }break;
            case 'aliased_type':
            {
                eZDebug::writeWarning( "'aliased_type' is deprecated as of 4.1 and not in use anymore, meaning it will always return false.", __METHOD__ );
                return $this->AliasedType;
            }break;
            case 'aliased_handler':
            {
                if ( $this->AliasedHandler === null )
                {
                    $this->AliasedHandler = eZXMLText::inputHandler( $this->XMLData,
                                                                     $this->AliasedType,
                                                                     false,
                                                                     $this->ContentObjectAttribute );
                }
                return $this->AliasedHandler;
            }break;
            default:
            {
                eZDebug::writeError( "Attribute '$name' does not exist", __METHOD__ );
                return null;
            }break;
        }
    }

    /*!
     \return the template name for this input handler, includes the edit suffix if any.
    */
    function editTemplateName()
    {
        $name = 'ezxmltext';
        $suffix = $this->editTemplateSuffix( $this->ContentObjectAttribute );
        if ( $suffix !== false )
        {
            $name .= '_' . $suffix;
        }
        return $name;
    }

    /*!
     \return the template name for this input handler, includes the information suffix if any.
    */
    function informationTemplateName()
    {
        $name = 'ezxmltext';
        $suffix = $this->informationTemplateSuffix( $this->ContentObjectAttribute );
        if ( $suffix !== false )
        {
            $name .= '_' . $suffix;
        }
        return $name;
    }

    /*!
     \pure
     Handles custom actions for input handler.
     \note Default does nothing, reimplement to check actions.
    */
    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute )
    {
    }

    /*!
     \virtual
     \return true if the input handler is considered valid, if not the handler will not be used.
     \note Default returns true
    */
    function isValid()
    {
        return true;
    }

    /*!
     \pure
     \return the suffix for the attribute template, if false it is ignored.
    */
    function editTemplateSuffix( &$contentobjectAttribute )
    {
        return false;
    }

    /*!
     \pure
     \return the suffix for the attribute template, if false it is ignored.
    */
    function informationTemplateSuffix( &$contentobjectAttribute )
    {
        return false;
    }

    /*!
     \return the xml data as text.
    */
    function xmlData()
    {
        return $this->XMLData;
    }

    /*!
     \pure
     Validates user input and returns whether it can be used or not.
    */
    function validateInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_INVALID;
    }

    /*!
     \pure
     Converts text input \a $text into an XML structure and returns it.
     \return an array where index 0 is the xml structure and index 1 is a message.
    */
    function convertInput( $text )
    {
        return null;
    }

    /*!
     \pure
     Returns the text representation of the XML structure, implement this to turn
     XML back into user input.
    */
    function inputXML()
    {
        return null;
    }

    /**
     * Replaces each run of characters XML 1.0 does not allow in $text with $replacement
     * (see http://www.w3.org/TR/REC-xml/#charsets).
     *
     * Characters above U+FFFF (#x10000-#x10FFFF) are kept when supplementaryCharactersAllowed() says the database
     * can store them, and replaced otherwise. Text that is not valid UTF-8 made preg_replace() return null, which lost
     * the whole text; each invalid byte sequence now becomes U+FFFD first and counts as one replacement.
     *
     * @param string $text
     * @param string $replacement
     * @param int $count Set to the number of replaced runs
     * @return string
     */
    public static function replaceCharactersXMLDoesNotAllow( $text, $replacement, &$count = 0 )
    {
        $pattern = self::supplementaryCharactersAllowed() ?
                   '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]+/u' :
                   '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u';
        $text = (string)$text;
        $count = 0;
        $result = preg_replace( $pattern, $replacement, $text, -1, $count );
        if ( $result === null )
        {
            $text = function_exists( 'mb_scrub' ) ? mb_scrub( $text, 'UTF-8' ) : (string)@iconv( 'UTF-8', 'UTF-8//IGNORE', $text );
            $result = preg_replace( $pattern, $replacement, $text, -1, $count );
            ++$count;
        }
        return $result === null ? '' : $result;
    }

    /**
     * Whether rich text may keep characters above U+FFFF (emoji, mathematical alphanumerics such as U+1D538).
     *
     * ezxml.ini [InputSettings] SupplementaryCharacters: 'enabled', 'disabled' or 'auto' (the default). 'auto' keeps
     * them unless the database is MySQL or MariaDB on a connection charset other than utf8mb4: Exponential maps
     * the charset utf-8 to MySQL's utf8 (three bytes at most), where such a character is stored as "????", or the
     * whole save fails in strict SQL mode. There they are replaced as before.
     *
     * @return bool
     */
    public static function supplementaryCharactersAllowed()
    {
        $ini = eZINI::instance( 'ezxml.ini' );
        $setting = $ini->hasVariable( 'InputSettings', 'SupplementaryCharacters' ) ?
                   strtolower( trim( (string)$ini->variable( 'InputSettings', 'SupplementaryCharacters' ) ) ) : 'auto';
        if ( $setting === 'enabled' || $setting === 'true' )
        {
            return true;
        }
        if ( $setting === 'disabled' || $setting === 'false' )
        {
            return false;
        }

        if ( eZDB::hasInstance() )
        {
            $db = eZDB::instance();
            $databaseName = (string)$db->databaseName();
            $charset = $db->charset();
        }
        else
        {
            $siteIni = eZINI::instance();
            $databaseName = (string)$siteIni->variable( 'DatabaseSettings', 'DatabaseImplementation' );
            $charset = $siteIni->hasVariable( 'DatabaseSettings', 'Charset' ) ?
                       $siteIni->variable( 'DatabaseSettings', 'Charset' ) : '';
        }
        if ( stripos( $databaseName, 'mysql' ) === false )
        {
            return true;
        }
        if ( !$charset )
        {
            $charset = eZTextCodec::internalCharset();
        }
        return eZMySQLCharset::mapTo( eZCharsetInfo::realCharsetCode( $charset ) ) === 'utf8mb4';
    }

    /// \privatesection
    /// Contains the XML data as text
    public $XMLData;
    public $AliasedType;
    public $AliasedHandler;
    public $ContentObjectAttribute;
}

?>
