#!/usr/bin/env php
<?php
/**
 * File containing the expusercreate.php script.
 *
 * Create a user account through the content publish, as the administration does (its workflows and the approval of new accounts run): login, e-mail, names, groups, and a password from standard input, a generated one or the activation mail (a dry run unless --apply).
 *
 * @alias exp:user:create
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expusercreate.php; this file is the entry point.
\Exponential\Command\Kernel\Expusercreate::main( __FILE__ );
