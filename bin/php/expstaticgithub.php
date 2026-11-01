#!/usr/bin/env php
<?php
/**
 * File containing the expstaticgithub.php script.
 *
 * Set a static publishing target up on GitHub (the repository, GitHub Pages from the branch or Actions, the custom domain, HTTPS), show its state there, or sign in with GitHub.
 *
 * @alias exp:static:github
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expstaticgithub.php; this file is the entry point.
\Exponential\Command\Kernel\Expstaticgithub::main( __FILE__ );
