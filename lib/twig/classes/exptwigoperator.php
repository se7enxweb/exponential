<?php
/**
 * The template operator exp_twig_render( '<file>' ): the stub a .twig is parsed as (expTwigRenderer::stubFor()).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigOperator
{
    function operatorList()
    {
        return array( 'exp_twig_render' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array(
            'exp_twig_render' => array(
                'file' => array( 'type' => 'string', 'required' => true, 'default' => '' ),
            ),
        );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        $operatorValue = expTwigRenderer::render( $tpl, (string)$namedParameters['file'], $rootNamespace, $currentNamespace );
    }
}
