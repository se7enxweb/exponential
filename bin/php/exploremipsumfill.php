#!/usr/bin/env php
<?php
/**
 * File containing the exploremipsumfill.php script.
 *
 * Fills the translation of objects in a test language (Lorem Ipsum, qlo-ZZ) with lorem ipsum text per datatype.
 * Only for a test language that is already a content language; it never adds one. Has --dry-run.
 * Guide: doc/guides/test-language-lorem-ipsum.md
 *
 * @alias exp:loremipsum
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/exploremipsumfill.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Exploremipsumfill::main( __FILE__ );
