<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

// Operator autoloading: the stub a .stpl is parsed as calls exp_smarty_render (see expSmartyRenderer)

$eZTemplateOperatorArray = array();

$eZTemplateOperatorArray[] = array( 'class' => 'expSmartyOperator',
                                    'operator_names' => array( 'exp_smarty_render' ) );

?>
