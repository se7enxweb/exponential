<?php
/**
 * File containing the expViewParameterOperator class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The template operator view_parameter: a value as the value of a view parameter, /(name)/<value>, written as
 * expViewParameters::encode() writes it ("~2F" for "/", "~28" and "~29" for the brackets, "~7E" for "~", the rest
 * readable), so that a template never encodes one by hand and the view reads it back with expViewParameters::get():
 *
 *   <a href={concat( 'settings/view/', $siteaccess, '/', $file, '/(q)/', $name|view_parameter )|ezurl}>
 *
 * Guide: doc/eng-us/guides/urls-and-aliases.md, section 9 "View parameters: how a value travels in an address".
 *
 * @package kernel
 */
class expViewParameterOperator
{
    /** @var string[] */
    public $Operators;

    /**
     * @param string $name
     */
    public function __construct( $name = 'view_parameter' )
    {
        $this->Operators = array( $name );
    }

    /**
     * @return string[]
     */
    function operatorList()
    {
        return $this->Operators;
    }

    /**
     * @return array
     */
    function namedParameterList()
    {
        return array();
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        $operatorValue = expViewParameters::encode( is_scalar( $operatorValue ) ? (string)$operatorValue : '' );
    }
}
?>
