#!/usr/bin/env php
<?php
/**
 * File containing the convertprice2multiprice.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Convert content objects with 'price' datatype attributes to 'multiprice'
 * @long-description Iterates over all content objects using the deprecated 'price' datatype and converts their attributes to the newer 'multiprice' datatype, preserving all existing pricing data.
 */

// file  bin/php/convertprice2multiprice.php

// description: the script will go through all classes and objects with 'ezprice'
//              datatype changing it to the 'ezmultiprice' datatype.
//              Note: the IDs and indentifiers of the classes, class attributes,
//                    objects, object attributes will not be changed.
//              Resulting 'ezmultiprice' will have 1 'custom' price(with value of
//              'ezprice') in currency of the current locale(the 'currency' object
//              will be created if it doesn't exist).


// script initializing
require_once 'autoload.php';

// The code is in kernel/private/classes/commands/convertprice2multiprice.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Convertprice2multiprice::main( __FILE__ );
