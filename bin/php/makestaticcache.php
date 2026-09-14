#!/usr/bin/env php
<?php
/**
 * File containing the makestaticcache.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "Exponential static cache generator\n" .
                                                        "\n" .
                                                        "./bin/makestaticcache.php --siteaccess user" ),
                                     'use-session' => false,
                                     'use-modules' => true,
                                     'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions( "[f|force][site:][max-pages:][max-depth:][keep]",
                                "",
                                array( 'force'     => "Accepted for compatibility; a run always replaces what it generates.",
                                       'site'      => "Generate only this siteaccess. Repeat for several; omit for every public one.",
                                       'max-pages' => "Stop after this many pages per site. Default 2500.",
                                       'max-depth' => "Follow links this many steps from the site root. Default 12.",
                                       'keep'      => "Add to what is already stored instead of replacing it." ),
                                false,
                                array( 'site' => true ) );

$script->initialize();

$ini = eZINI::instance();
if ( $ini->variable( 'ContentSettings', 'StaticCache' ) != 'enabled' )
{
    $cli->error( "You must first enable [ContentSettings] StaticCache in site.ini" );
    $script->shutdown( 1 );
}

// The same runner the administration interface uses, so the two cannot
// generate different sets of pages. It crawls each site from its own root
// rather than expanding the url alias table, which is the only way to get the
// urls a visitor actually requests on an installation that serves more than one
// site or roots a site below the content root with PathPrefix.
require_once 'kernel/setup/expstaticcacherunner.php';

$colours = array( 'phase' => 'cyan', 'phase-item' => 'white', 'ok' => 'green',
                  'warn' => 'yellow', 'error' => 'red', 'info' => 'gray', 'done' => 'cyan' );

$runner = new expStaticCacheRunner(
    function ( $type, $message, array $data = array() ) use ( $cli, $colours )
    {
        $colour = isset( $colours[$type] ) ? $colours[$type] : 'default';
        $cli->output( $cli->stylize( $colour, $message ) );
    },
    array( 'siteaccess' => isset( $options['site'] ) ? (array)$options['site'] : array(),
           'max_pages'  => isset( $options['max-pages'] ) ? (int)$options['max-pages'] : 2500,
           'max_depth'  => isset( $options['max-depth'] ) ? (int)$options['max-depth'] : 12,
           'purge'      => !isset( $options['keep'] ) || !$options['keep'] ) );

$runner->run();

$script->shutdown();

?>
