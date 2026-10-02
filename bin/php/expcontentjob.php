#!/usr/bin/env php
<?php
/**
 * Content jobs: large subtree removes and copies that run in batches in the background, resumable after any
 * interruption. Lists, shows, runs, resumes and cancels them, and creates them for scripts.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 * @description Run, list, resume and cancel content jobs: large subtree removes and copies in batches
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/expcontentjob.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Expcontentjob::main( __FILE__ );
