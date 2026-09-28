<?php
/**
 * File containing the ezpSiteAccessURLOperator class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and others. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Template operator siteaccess_url: the address of a siteaccess's front page
 * (ezpSiteAccessURL::root()), with the scheme, host, port and siteaccess path
 * the settings and the current request call for.
 *
 *   {siteaccess_url()}              the default siteaccess (DefaultAccess)
 *   {siteaccess_url( 'bold_ger' )}  a given one
 *
 * Returns an empty string for a siteaccess that does not exist.
 */
class ezpSiteAccessURLOperator
{
    public function operatorList()
    {
        return array( 'siteaccess_url' );
    }

    public function namedParameterPerOperator()
    {
        return true;
    }

    public function namedParameterList()
    {
        return array( 'siteaccess_url' => array( 'siteaccess' => array( 'type' => 'string', 'required' => false, 'default' => '' ) ) );
    }

    /**
     * The value depends on the request (scheme, host, port), so compiled
     * templates call ezpSiteAccessURL at every render. Without this the
     * compiler took a call without input for a constant and kept the address
     * of the request that compiled the template: compiled templates are shared
     * by every web server of the installation, so Apache showed Velocity's
     * port, or Velocity showed none.
     */
    public function operatorTemplateHints()
    {
        return array( 'siteaccess_url' => array( 'input' => false, 'output' => true, 'parameters' => true,
                                                 'transform-parameters' => true, 'input-as-parameter' => false,
                                                 'element-transformation' => true,
                                                 'element-transformation-func' => 'siteAccessURLTransformation' ) );
    }

    public function siteAccessURLTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                                 $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $newElements = array();
        if ( count( $parameters ) > 0 )
        {
            $values = array( $parameters[0] );
            $newElements[] = eZTemplateNodeTool::createCodePieceElement(
                "%output% = (string)ezpSiteAccessURL::root( ( (string)%1% !== '' ) ? (string)%1% : null );\n", $values );
        }
        else
        {
            $newElements[] = eZTemplateNodeTool::createCodePieceElement( "%output% = (string)ezpSiteAccessURL::root();\n" );
        }
        return $newElements;
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $operatorValue = (string)ezpSiteAccessURL::root( $namedParameters['siteaccess'] !== '' ? $namedParameters['siteaccess'] : null );
    }
}
