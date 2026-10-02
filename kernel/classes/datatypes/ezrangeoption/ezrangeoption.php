<?php
/**
 * File containing the eZRangeOption class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZRangeOption ezrangeoption.php
  \ingroup eZDatatype
  \brief The class eZRangeOption does

*/

class eZRangeOption
{
    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct( $name )
    {
        $this->Name = $name;
        $this->Options = array();
        $this->OptionCount = 0;
    }

    /*!
     Sets the name of the option
    */
    function setName( $name )
    {
        $this->Name = $name;
    }

    /*!
     Returns the name of the option set.
    */
    function name()
    {
        return $this->Name;
    }

    /*!
     \return list of supported attributes
    */
    function attributes()
    {
        return array( 'name',
                      'start_value',
                      'stop_value',
                      'step_value',
                      'option_list' );
    }

    function hasAttribute( $name )
    {
        return in_array( $name, $this->attributes() );
    }

    function attribute( $name )
    {
        switch ( $name )
        {
            case "name" :
            {
                return $this->Name;
            }break;
            case "start_value" :
            {
                return $this->StartValue;
            }break;
            case "stop_value" :
            {
                return $this->StopValue;
            }break;
            case "step_value" :
            {
                return $this->StepValue;
            }break;
            case "option_list" :
            {
                return $this->Options;
            }break;
            default:
            {
                eZDebug::writeError( "Attribute '$name' does not exist", __METHOD__ );
                return null;
            }break;
        }
    }

    function addOption( $valueArray )
    {
        $this->Options[] = array( "id" => $this->OptionCount,
                                  "value" => $valueArray['value'],
                                  'additional_price' => 0,
                                  "is_default" => false );

        $this->OptionCount += 1;
    }

    /*!
     \return A DOMDocument for \a $xmlString, or null when it is empty or not
     well-formed. The parser's complaints are collected instead of raised as
     warnings: broken stored data must read as an empty range, not as PHP
     warnings, a ValueError for '' or a fatal error on a missing documentElement.
    */
    static function loadDocument( $xmlString )
    {
        if ( !is_string( $xmlString ) || trim( $xmlString ) === '' )
            return null;
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $previous = libxml_use_internal_errors( true );
        $success = $dom->loadXML( $xmlString );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        if ( !$success || !$dom->documentElement )
            return null;
        return $dom;
    }

    /*!
     \return the number of options the range \a $start .. \a $stop in steps of
     \a $step generates, or false when it is not a usable range: a value that is
     not a finite number, a step of zero or less (the loop that builds the list
     would never end) or more values than MAX_OPTION_COUNT.
    */
    static function rangeCount( $start, $stop, $step )
    {
        foreach ( array( $start, $stop, $step ) as $value )
        {
            if ( !is_scalar( $value ) || !is_numeric( trim( (string)$value ) ) || !is_finite( (float)$value ) )
                return false;
        }
        $start = (float)$start;
        $stop = (float)$stop;
        $step = (float)$step;
        if ( $step <= 0 )
            return false;
        if ( $stop < $start )
            return 0;
        $count = floor( ( $stop - $start ) / $step ) + 1;
        if ( $count > self::MAX_OPTION_COUNT )
            return false;
        return (int)$count;
    }

    /*!
     \return \a $value as a string for the DOM: '' for null and for an array
     (form and import input can be either).
    */
    static function scalarString( $value )
    {
        return is_scalar( $value ) ? (string)$value : '';
    }

    function decodeXML( $xmlString )
    {
        // Nothing stored yet reads as what its first save stores (start, stop
        // and step 0, which the rule below makes a range of one value 0):
        // read as an empty range instead, a saved empty attribute came back
        // different from the unsaved one.
        if ( trim( (string)$xmlString ) === '' )
            $xmlString = '<?xml version="1.0" encoding="utf-8"?>' . "\n"
                       . '<ezrangeoption start_value="0" stop_value="0" step_value="0"><name/></ezrangeoption>';
        $dom = self::loadDocument( $xmlString );

        if ( $dom )
        {
            // set the name of the node
            $rangeOptionElement = $dom->documentElement;
            $startValue = $rangeOptionElement->getAttribute( 'start_value' );
            $stopValue = $rangeOptionElement->getAttribute( 'stop_value' );
            $stepValue = $rangeOptionElement->getAttribute( 'step_value' );
            if ( $stepValue == 0 )
                $stepValue = 1;
            $this->StartValue = $startValue;
            $this->StopValue = $stopValue;
            $this->StepValue = $stepValue;


            $nameNode = $dom->getElementsByTagName( "name" )->item( 0 );
            $this->setName( $nameNode ? $nameNode->textContent : '' );

            // A negative step never reaches the stop value, a value that is not
            // a number throws a TypeError in the loop and a huge range builds
            // millions of options on every read: all of them leave the option
            // list empty instead of hanging or killing the request
            if ( self::rangeCount( $startValue, $stopValue, $stepValue ) !== false )
            {
                for ( $i = $startValue; $i <= $stopValue; $i += $stepValue )
                {
                    $this->addOption( array( 'value' => $i,
                                             'additional_price' => 0 ) );
                }
            }
        }
        else
        {
            $this->StartValue = 0;
            $this->StopValue = 0;
            $this->StepValue = 0;
        }
    }

    /*!
     Will return the XML string for this option set.
    */
    function xmlString( )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );

        $root = $doc->createElement( "ezrangeoption" );
        $root->setAttribute( "start_value", self::scalarString( $this->StartValue ) );
        $root->setAttribute( "stop_value", self::scalarString( $this->StopValue ) );
        $root->setAttribute( "step_value", self::scalarString( $this->StepValue ) );
        $doc->appendChild( $root );

        // A text node rather than createElement( name, value ): the value
        // argument is not escaped, so a name with an & was cut off with a warning
        // (no text node for '' so that an empty name stays <name/>, as before)
        $name = $doc->createElement( "name" );
        if ( self::scalarString( $this->Name ) !== '' )
            $name->appendChild( $doc->createTextNode( self::scalarString( $this->Name ) ) );
        $root->appendChild( $name );

        $xml = $doc->saveXML();

        return $xml;
    }

    function setStartValue( $value )
    {
        $this->StartValue = $value;
    }

    function setStopValue( $value )
    {
        $this->StopValue = $value;
    }

    function setStepValue( $value )
    {
        $this->StepValue = $value;
    }


        /// Contains the Option name
    public $Name;

    /// Contains the Options
    public $Options;

    /// Contains the option counter value
    public $OptionCount;
    public $StartValue;
    public $StopValue;
    public $StepValue;

    /// The most options a range may generate; the list is built on every read
    const MAX_OPTION_COUNT = 10000;
}

?>
