<?php
/**
 * File containing the expDemoModeOperator class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The template operator exp_demo_mode(): true while demo mode is on (the environment variable EXP_DEMO_MODE=1,
 * expDemoMode). The administration draws its demo banner with it:
 *
 *   {if exp_demo_mode()}{include uri='design:parts/exp_demo_mode_banner.tpl'}{/if}
 *
 * It is worked out each time the template runs, never when it is compiled, so a compiled template and a persistent
 * worker always show the mode the environment has now. Guide: doc/eng-us/bc/6.0/demo-mode.md.
 *
 * @package kernel
 */
class expDemoModeOperator
{
    /** @var string[] */
    public $Operators;

    /**
     * @param string $name
     */
    public function __construct( $name = 'exp_demo_mode' )
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
        $operatorValue = class_exists( 'expDemoMode' ) && expDemoMode::enabled();
    }
}
?>
