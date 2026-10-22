<?php
/**
 * File containing the fetch functions of the expimageeditor module
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expimageeditor
 */

$FunctionList = array();

// fetch( 'expimageeditor', 'aspect_ratios' ): the aspect ratios the crop tool offers
$FunctionList['aspect_ratios'] = array( 'name' => 'aspect_ratios',
                                        'operation_types' => array( 'read' ),
                                        'call_method' => array( 'class' => 'expImageEditorFunctionCollection',
                                                                'method' => 'fetchAspectRatios' ),
                                        'parameter_type' => 'standard',
                                        'parameters' => array() );
