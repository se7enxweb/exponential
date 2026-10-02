<?php
//
// Definition of Session_GC Cronjob
/**
 * @description Garbage-collect expired user sessions from the session store
 *
 * File containing the session_gc.php cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Cronjob to garbage collect expired sessions as defined by site.ini[Session]SessionTimeout
 * (the expiry time is calculated when session is created / updated)
 * These are normally automatically removed by the session gc in php, but on some linux distroes
 * based on debian this does not work because the custom way session gc is handled.
 *
 * Also make sure you run basket_cleanup if you use the shop!
 *
 * @package eZCronjob
 * @see eZsession
 */


// Functions for session to make sure baskets are cleaned up

// The code is in kernel/private/classes/cronjobs/session_gc.php (#207); this file is the entry point.
return \Exponential\Cronjob\Kernel\SessionGc::main( __FILE__, get_defined_vars() );
