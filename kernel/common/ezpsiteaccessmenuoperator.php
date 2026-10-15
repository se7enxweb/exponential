<?php
/**
 * File containing the ezpSiteAccessMenuOperator class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Template operators of the administration's site menu (ezpSiteAccessMenu):
 *
 *   {siteaccess_menu( $current_user )}  hash enabled, front and groups: the
 *                            siteaccesses with their addresses, see
 *                            ezpSiteAccessMenu::current(); with a user, only
 *                            the siteaccesses that user may log in to (besides
 *                            the site the globe opens)
 *   {siteaccess_menu_key()}  a string for the keys of the cache-blocks that
 *                            draw the menu, see ezpSiteAccessMenu::key()
 *
 * Both are worked out at every render, compiled or not: the addresses depend
 * on the scheme, host and port of the request (see ezpSiteAccessURLOperator).
 */
class ezpSiteAccessMenuOperator
{
    public function operatorList()
    {
        return array( 'siteaccess_menu', 'siteaccess_menu_key' );
    }

    public function namedParameterPerOperator()
    {
        return true;
    }

    public function namedParameterList()
    {
        return array( 'siteaccess_menu' => array( 'user' => array( 'type' => 'object', 'required' => false, 'default' => null ) ),
                      'siteaccess_menu_key' => array() );
    }

    public function operatorTemplateHints()
    {
        $hint = array( 'input' => false, 'output' => true, 'parameters' => true,
                       'transform-parameters' => true, 'input-as-parameter' => false,
                       'element-transformation' => true,
                       'element-transformation-func' => 'siteAccessMenuTransformation' );
        return array( 'siteaccess_menu' => $hint, 'siteaccess_menu_key' => array( 'parameters' => false ) + $hint );
    }

    public function siteAccessMenuTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        if ( $operatorName === 'siteaccess_menu_key' )
            return array( eZTemplateNodeTool::createCodePieceElement( "%output% = ezpSiteAccessMenu::key();\n" ) );
        if ( count( $parameters ) > 0 )
            return array( eZTemplateNodeTool::createCodePieceElement( "%output% = ezpSiteAccessMenu::current( %1% );\n", array( $parameters[0] ) ) );
        return array( eZTemplateNodeTool::createCodePieceElement( "%output% = ezpSiteAccessMenu::current();\n" ) );
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $operatorValue = $operatorName === 'siteaccess_menu_key'
                       ? ezpSiteAccessMenu::key()
                       : ezpSiteAccessMenu::current( isset( $namedParameters['user'] ) ? $namedParameters['user'] : null );
    }
}
