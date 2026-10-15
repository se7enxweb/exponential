#!/usr/bin/env php
<?php
/**
 * @description Create the user group for deactivated accounts ("Deactivate and move") ahead of first use.
 *
 * Looks for the group ([UserSettings] DeactivatedUserGroupNodeID, else the remote id exp_deactivated_users_group)
 * and, with --commit, creates it below the user root node: hidden, without roles. A dry run by default. Prints the
 * node id and the settings line; it never writes an INI file.
 *
 *   php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess>
 *   php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess> --commit [--name="Former staff"] [--section-id=2]
 *
 * Guide: doc/features/6.0/user-hygiene.md, section "Deactivate and move"
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezcreatedeactivatedusergroup.php; this file is the entry point.
\Exponential\Command\Kernel\Ezcreatedeactivatedusergroup::main( __FILE__ );
