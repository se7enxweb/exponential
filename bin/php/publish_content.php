#!/usr/bin/env php
<?php
/**
 * File containing the publish_content.php bin script
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @subpackage content
 */

/**
 * This script, given a queued contentobject_id + version, will resume the publishing operation on it
 * @package kernel
 * @subpackage content
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/publish_content.php (#207); this file is the entry point.
\Exponential\Command\Kernel\PublishContent::main( __FILE__ );
