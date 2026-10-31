<?php
/**
 * The code of bin/php/expwtmigratefromezwt.php.
 * @description Replace ezwt (the website toolbar) by expwt (the website sidebar): settings, policies, activation, schedule (dry run unless --apply)
 * @alias exp:wt:migrate-from-ezwt
 * Guide: doc/eng-us/features/6.0/website-sidebar.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expwtmigratefromezwt extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $script = $this->script(
            array(
                'description' => "Replaces ezwt, the website toolbar, by expwt, the website sidebar: copies ezwt's hidden classes\n"
                    . "(websitetoolbar.ini) to expwt.ini in settings/override and every siteaccess, gives every\n"
                    . "websitetoolbar/use policy as expwt/use, edit, create and structure with the same limitations, the\n"
                    . "Editor role those four and the Administrator role expwt/*, and puts expwt in ezwt's place in\n"
                    . "ActiveExtensions[] and ActiveAccessExtensions[]. A changed file is kept as <file>.before-expwt.\n"
                    . "The sidebar itself stays off until switched on (expwt.ini [ExpwtSettings] Enabled).\n"
                    . "It also moves the schedule of earlier expwt versions (publish later, take down) to the kernel's\n"
                    . "schedule: the wait-until-date workflow and the hide cronjob. An entry whose class has no date field\n"
                    . "the kernel reads stays and is listed as a note; run the command again once it has one.\n"
                    . "Without --apply nothing is written. The upgrade step 6.0.15-expwt-enable does the same.\n\n"
                    . "Example: php bin/php/expwtmigratefromezwt.php\n"
                    . "         php bin/php/expwtmigratefromezwt.php --apply",
                'use-session' => false,
                'use-modules' => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( "[apply][dry-run]", '',
            array( 'apply' => "Write the changes (the default is a dry run that lists them and changes nothing)",
                   'dry-run' => "The default: list what would change, change nothing" ) );
        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $script->shutdown( 2 );
        }
        if ( !\expWebsiteSidebarMigration::isInstalled() )
        {
            $cli->error( 'FAIL: expwt is not installed (extension/expwt); install se7enxweb/expwt first' );
            $script->shutdown( 1 );
        }
        $changes = \expWebsiteSidebarMigration::changes();
        foreach ( $changes as $line )
            $cli->output( ( $apply ? '' : 'would ' ) . $line );
        foreach ( \expWebsiteSidebarMigration::notes() as $note )
            $cli->warning( $note );
        if ( !$apply )
        {
            $cli->output();
            $cli->output( 'PASS: ' . count( $changes ) . ' change(s) to make (dry run, nothing changed; --apply writes)' );
            $script->shutdown( 0 );
        }
        foreach ( \expWebsiteSidebarMigration::apply() as $line )
            $cli->output( $line );
        $left = \expWebsiteSidebarMigration::changes();
        if ( $left )
        {
            $cli->error( 'FAIL: still to do: ' . implode( '; ', $left ) );
            $script->shutdown( 1 );
        }
        $cli->output();
        $cli->output( 'PASS: expwt is in place of ezwt (clear the caches: php bin/php/ezcache.php --clear-tag=ini)' );
        $script->shutdown();
    }
}

}
