<?php
/**
 * Entry point of kernel/shop/dashboard.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/*
 * shop/dashboard: one page for the store owner. What happened (orders, revenue,
 * customers, baskets), what waits for them (open orders, stale checkouts), how this
 * particular shop is put together (VAT, currencies, discounts, statuses, payment,
 * handlers, the checkout flow) and a checklist of what to do next, all computed from
 * the database and the settings.
 *
 * Every figure comes from a small, fixed number of aggregate queries, whatever the size
 * of the shop, and every statement is engine neutral (no LIMIT, no engine functions,
 * paging through arrayQuery parameters).
 */

// The code is in kernel/private/classes/views/shop/dashboard.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Shop\Dashboard::main( __FILE__, get_defined_vars() );
