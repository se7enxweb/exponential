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
 *   {siteaccess_menu()}      hash enabled, front and groups: the siteaccesses
 *                            with their addresses, see ezpSiteAccessMenu::current()
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

    public function namedParameterList()
    {
        return array();
    }

    public function operatorTemplateHints()
    {
        $hint = array( 'input' => false, 'output' => true, 'parameters' => false,
                       'element-transformation' => true,
                       'element-transformation-func' => 'siteAccessMenuTransformation' );
        return array( 'siteaccess_menu' => $hint, 'siteaccess_menu_key' => $hint );
    }

    public function siteAccessMenuTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $call = $operatorName === 'siteaccess_menu_key' ? 'ezpSiteAccessMenu::key()' : 'ezpSiteAccessMenu::current()';
        return array( eZTemplateNodeTool::createCodePieceElement( "%output% = $call;\n" ) );
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $operatorValue = $operatorName === 'siteaccess_menu_key' ? ezpSiteAccessMenu::key() : ezpSiteAccessMenu::current();
    }
}
