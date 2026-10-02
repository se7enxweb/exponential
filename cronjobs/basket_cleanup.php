<?php
/**
 * @description Remove abandoned shopping baskets older than the configured threshold
 *
 * File containing the basket_cleanup.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/basket_cleanup.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\BasketCleanup::main( __FILE__, get_defined_vars() );
