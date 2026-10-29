<?php
/**
 * File containing the expUpgradeStepFinishDeploy class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Makes the run's changes live: ./console exp:velocity deploy --kernel (expVelocityDeploy) in its own process, the
 * one order that works: autoloads, INI and template caches, the engine archive, the PHP-FPM reload, the Velocity
 * restart, then the output caches and the help content cache. It skips on its own what the installation does not
 * use (no Velocity, no PHP-FPM unit). With the option no-deploy (--no-deploy) only the extension autoloads and the INI,
 * template and content caches are done, in place, and the operator deploys later.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepFinishDeploy extends expUpgradeFinishStep
{
    public function id()
    {
        return '6.0.15-finish-deploy';
    }

    public function description()
    {
        return 'Make the changes live: autoloads, caches, PHP-FPM and Velocity (exp:velocity deploy --kernel; --no-deploy: autoloads and caches only)';
    }

    public function plan( expUpgradeContext $context )
    {
        if ( $context->option( 'no-deploy', false ) )
            return array( 'regenerate the extension autoloads', 'clear the INI, template and content caches', 'no deploy (--no-deploy): run ./console exp:velocity deploy --kernel afterwards' );
        $output = array();
        $code = self::php( 'bin/php/velocity.php', array( 'deploy', '--kernel', '--dry-run' ), $output );
        $lines = array( 'run ./console exp:velocity deploy --kernel' );
        foreach ( $output as $line )
            if ( preg_match( '/^\[\s*\d+\/\d+\]/', $line ) )
                $lines[] = '  ' . $line;
        if ( $code !== 0 )
            $lines[] = '  (the deploy dry run ended with exit ' . $code . ')';
        return $lines;
    }

    protected function work( expUpgradeContext $context )
    {
        if ( $context->option( 'no-deploy', false ) )
        {
            $output = array();
            if ( self::php( 'bin/php/ezpgenerateautoloads.php', array( '-e' ), $output ) !== 0 )
                throw new RuntimeException( 'the extension autoloads could not be generated: ' . implode( ' ', array_slice( $output, -3 ) ) );
            foreach ( array( 'ini', 'template', 'content' ) as $tag )
                eZCache::clearByTag( $tag );
            return array( 'extension autoloads generated', 'INI, template and content caches cleared', 'not deployed (--no-deploy): run ./console exp:velocity deploy --kernel' );
        }
        $output = array();
        $code = self::php( 'bin/php/velocity.php', array( 'deploy', '--kernel' ), $output );
        if ( $code !== 0 )
            throw new RuntimeException( 'exp:velocity deploy --kernel failed (exit ' . $code . '): ' . implode( ' | ', array_slice( $output, -5 ) ) );
        $done = array( 'exp:velocity deploy --kernel' );
        foreach ( $output as $line )
            if ( preg_match( '/^\[\s*\d+\/\d+\]/', $line ) )
                $done[] = '  ' . $line;
        return $done;
    }
}
