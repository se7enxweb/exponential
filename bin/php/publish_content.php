#!/usr/bin/env php
<?php
/**
 * File containing the publish_content.php bin script
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * This script, given a queued contentobject_id + version, will resume the publishing operation on it
 * @package kernel
 * @subpackage content
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/publish_content.php (#207); this file is the entry point.
\Exponential\Command\Kernel\PublishContent::main( __FILE__ );
