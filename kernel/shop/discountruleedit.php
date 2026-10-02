<?php
/**
 * Entry point of kernel/shop/discountruleedit.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// TODO: it was not in the original code, but we may consider to add support for "folder with products",
//       not only products (i.e. objects with attribute of the ezprice datatype).

// The code is in kernel/private/classes/views/shop/discountruleedit.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Shop\Discountruleedit::main( __FILE__, get_defined_vars() );
