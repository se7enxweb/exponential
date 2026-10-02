<?php
/**
 * An order's permanent receipt: shop/orderreceipt/<token>.
 *
 * The address never expires and can be bookmarked. Its signed token is the
 * key: whoever holds a genuine one sees the receipt, from any browser or
 * device, no sign-in needed (see eZShopReceipt::canView()).
 * ?download=1 answers the same receipt as a self-contained HTML file.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/views/shop/orderreceipt.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Shop\Orderreceipt::main( __FILE__, get_defined_vars() );
