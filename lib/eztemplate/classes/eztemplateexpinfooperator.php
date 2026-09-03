<?php
/**
 * File containing the eZTemplateExpInfoOperator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZTemplateExpInfoOperator eztemplateexpinfooperator.php
  \ingroup eZTemplateOperators
  \brief Template operator for querying eZ Publish extension metadata.

Usage:
{expinfo()}                       - all active extensions
{expinfo( 'all' )}                - all available extensions with 'active' boolean
{expinfo( 'explayouts_ui' )}      - single extension info, or false if not found

Examples:
{def $ext=expinfo( 'explayouts_ui' )}
{if $ext}
  Name: {$ext.name}
  Version: {$ext.version}
  License: {$ext.license}
  Active: {$ext.active}
{/if}

{foreach expinfo( 'all' ) as $name => $ext}
  {$name} - {$ext.name} (active: {$ext.active})
{/foreach}
*/

class eZTemplateExpInfoOperator
{
    public function __construct( $expInfoName = 'expinfo' )
    {
        $this->ExpInfoName = $expInfoName;
        $this->Operators = array( $expInfoName );
    }

    /*! Returns the operators in this class. */
    function operatorList()
    {
        return $this->Operators;
    }

    function operatorTemplateHints()
    {
        return array( $this->ExpInfoName => array( 'input' => true,
                                                   'output' => true,
                                                   'parameters' => true,
                                                   'element-transformation' => false,
                                                   'transform-parameters' => false,
                                                   'input-as-parameter' => true,
                                                   'element-transformation-func' => false ) );
    }

    function namedParameterList()
    {
        return array();
    }

    function modify( $tpl, $operatorName, $operatorParameters,
                     $rootNamespace, $currentNamespace, &$operatorValue,
                     $namedParameters, $placement )
    {
        $mode = false;

        if ( count( $operatorParameters ) > 0 )
        {
            $mode = $tpl->elementValue( $operatorParameters[0],
                                        $rootNamespace,
                                        $currentNamespace,
                                        $placement );
        }
        else if ( is_string( $operatorValue ) && $operatorValue !== '' )
        {
            $mode = $operatorValue;
        }

        if ( $mode === false || $mode === '' || $mode === 'active' )
        {
            $operatorValue = expInfo::activeExtensions();
            return;
        }

        if ( $mode === 'all' )
        {
            $operatorValue = expInfo::availableExtensions();
            return;
        }

        if ( $mode === 'kernel' )
        {
            $operatorValue = expInfo::kernelInfo();
            return;
        }

        $operatorValue = expInfo::extensionInfo( $mode );
    }
}

?>