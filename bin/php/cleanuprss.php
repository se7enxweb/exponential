#!/usr/bin/env php
<?php
/**
 * File containing the cleanuprss.php script.
 *
 * Trims the content the RSS import has created, keeping the newest items of
 * each feed and removing the rest.
 *
 * Ported from the bccleanuprss extension by Brookins Consulting.
 *
 * @copyright Copyright (C) 1999 - 2011 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @package kernel
 */

require_once 'autoload.php';

$cli    = eZCLI::instance();
$script = eZScript::instance(
    array(
        'description' => "Exponential RSS Import Cleanup\n" .
                         "Keeps the newest items of each active RSS import and removes the rest.\n" .
                         "\n" .
                         "Configured in content.ini [RSSImportCleanupSettings]; it removes nothing\n" .
                         "until that names the classes it is allowed to remove.\n" .
                         "\n" .
                         "./bin/php/cleanuprss.php --dry-run",
        'use-session'    => false,
        'use-modules'    => true,
        'use-extensions' => true
    )
);

$script->startup();

$options = $script->getOptions(
    "[dry-run][keep:]",
    "",
    array( 'dry-run' => 'List what would be removed, and remove nothing',
           'keep'    => 'Keep this many items per feed instead of the configured number' ) );

$script->initialize();

$cleanup = new expCleanupRSS(
    array( 'dry-run' => (bool)$options['dry-run'],
           'keep'    => $options['keep'] !== null ? (int)$options['keep'] : null ) );

if ( !$cleanup->isEnabled() )
{
    // Not an error: an installation that has not asked for this is the normal
    // case, and the run says which setting is holding it rather than going
    // quiet and leaving the operator to guess.
    $cli->warning( 'Nothing was removed: ' . $cleanup->reason() );
    $script->shutdown( 0 );
}

$cleanup->cleanup();

$counts = $cleanup->counts();

$cli->output();
$cli->output( sprintf( '%d feed(s), %d item(s) above the limit, %d removed.',
                       $counts['feeds'], $counts['examined'], $counts['removed'] ) );

$script->shutdown( 0 );

?>
