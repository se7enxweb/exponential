<?php
/**
 * File containing the handler wizard view.
 *
 * One address per kind of handler - /setup/handlerextension/session,
 * /setup/handlerextension/mail - so each is its own tool on the RAD page with
 * its own explanation, while they share the engine that writes them.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

// The code is in kernel/private/classes/views/setup/handlerextension.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Setup\Handlerextension::main( __FILE__, get_defined_vars() );
