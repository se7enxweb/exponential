<?php
/*!
  \class   TemplateParseXMLOperator templateparsexmloperator.php
  \ingroup eZTemplateOperators
  \brief   Handles template operator parsexml
  \version 2.0
  \date    20 April 2009
  \author  Administrator User

  By using parsexml you can ...

  Example:
\code
{$value|parsexml|wash}
\endcode
*/

class TemplateParseXMLOperator
{
    public $Operators;

    /*!
      Constructor, does nothing by default.
    */
    function __construct()
    {
    	$this->Operators = array( 'parsexml','filecheck' );
    }

    /*!
     \return an array with the template operator name.
    */
    function operatorList()
    {
         return $this->Operators;
    }
    /*!
     \return true to tell the template engine that the parameter list exists per operator type,
             this is needed for operator classes that have multiple operators.
    */
    function namedParameterPerOperator()
    {
        return true;
    }
    /*!
     See eZTemplateOperator::namedParameterList
    */
    function namedParameterList()
    {
        return array( 'parsexml' => array( 'first_param' => array( 'type' => 'string',
                                                                    'required' => false,
                                                                    'default' => 'default text' ) ));
    }
    /*!
     Executes the PHP function for the operator cleanup and modifies \a $operatorValue.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'parsexml':
            {
                $firstParam = $namedParameters['first_param'];
                if ( trim( $operatorValue ) != '' )
        		{
        			$dom = new DOMDocument( '1.0', 'utf-8' );
        			if ($dom->loadXML( $operatorValue ))
        			{
        				$FileAttributeValue = $dom->getElementsByTagName( $firstParam )->item(0)->textContent;
        				if( !$FileAttributeValue )
        					$FileAttributeValue = $dom->getElementsByTagName( $firstParam )->item(0)->getAttribute('value');
        			}
        			$operatorValue=$FileAttributeValue;
        		}
            } break;
            case 'filecheck':
            {
        		if ( trim( $operatorValue ) != '' )
        		{
        			$dom = new DOMDocument( '1.0', 'utf-8' );
        			if ($dom->loadXML( $operatorValue ))
        			{
        				$FileAttributeValue = $dom->getElementsByTagName( 'Filename' )->item(0)->textContent;
        				if( !$FileAttributeValue )
        					$FileAttributeValue = $dom->getElementsByTagName( 'Filename' )->item(0)->getAttribute('value');
        			}
        			if(file_exists(eZSys::wwwDir().$FileAttributeValue)){
        				$operatorValue=true;
        			} else {
        				$operatorValue=false;
        			}
        		}
            } break;
        }
    }
}
?>
