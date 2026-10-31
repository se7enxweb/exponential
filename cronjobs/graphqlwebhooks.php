<?php
/**
 * @description GraphQL webhooks: send the events of the journal to the end points that want them, with retries
 *
 * GraphQL webhooks cronjob part ([CronjobPart-graphqlwebhooks], also in the frequent group)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

// The code is in kernel/private/classes/cronjobs/graphqlwebhooks.php; this file is the entry point.
if ( !class_exists( 'Exponential\\Cronjob\\Kernel\\Graphqlwebhooks' ) )
    return 0;
return \Exponential\Cronjob\Kernel\Graphqlwebhooks::main( __FILE__, get_defined_vars() );
