#!/usr/bin/env php
<?php
/**
 * File containing the i18n.php script.
 *
 * The translation fallbacks: per locale the chain of translations it falls back to (i18n.ini [TranslationSettings]),
 * and how much of the kernel and of each extension it shows translated. "check" exits 1 on a cycle, a chain longer
 * than FallbackMaxDepth, a missing target or a rule that is none.
 *
 * @alias exp:i18n
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/i18n.php; this file is the entry point.
\Exponential\Command\Kernel\I18n::main( __FILE__ );
