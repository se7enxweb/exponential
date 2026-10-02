<?php
/**
 * The code of bin/php/install.php, moved into a class (#207 stage 1). The file bin/php/install.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/install.php:
 *
 *
 * @description One-command installation: builds the kickstart configuration from options (SQLite by default, admin password "publish") and installs, no kickstart.ini needed.
 * @package   kernel
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   GNU General Public License v2.0 (or any later version)
 *
 * ./console exp:install [options]
 *
 * The kickstarter installs from a kickstart.ini that has to be written first,
 * by hand or with "exp:kickstarter ini". This command builds that
 * configuration from its options, with a default for every one of them, and
 * runs the same installation steps in the same process. Any kickstart.ini
 * already there is put back afterwards, untouched; the configuration that was
 * used is kept in var/log with its passwords masked.
 *
 */

namespace Exponential\Command\Kernel
{

class Install extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'exitCode', 'flags', 'forward', 'rootDir', 'runner' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $forward = array( '--force' );
        if ( $flags['dry-run'] )
            $forward = array( '--dry-run' );
        if ( $flags['allow-root-user'] )
            $forward[] = '--allow-root-user';

        $runner = new \expKickstarter( $rootDir, $forward );
        $exitCode = $runner->run();

        exit( $exitCode );
    }
}

}
