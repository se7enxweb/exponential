<?php
/**
 * Fetch functions of the exphelp module (doc/features/6.0/help-system.md, "Context help").
 *
 *   fetch( 'exphelp', 'context', hash( 'module', 'content', 'view', 'edit', 'design', 'admin4' ) )
 *       the help page of a module/view: array( id, anchor, url, title, level ) (expHelpContextRegistry::lookup())
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

$FunctionList = array();
$FunctionList['context'] = array( 'name' => 'context',
                                  'operation_types' => array( 'read' ),
                                  'call_method' => array( 'class' => 'expHelpFunctionCollection',
                                                          'method' => 'fetchContext' ),
                                  'parameter_type' => 'standard',
                                  'parameters' => array( array( 'name' => 'module', 'type' => 'string', 'required' => false, 'default' => '' ),
                                                         array( 'name' => 'view', 'type' => 'string', 'required' => false, 'default' => '' ),
                                                         array( 'name' => 'design', 'type' => 'string', 'required' => false, 'default' => '' ) ) );

?>
