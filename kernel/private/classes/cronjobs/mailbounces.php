<?php
/**
 * @description The bounce mailbox: hard bounces and spam complaints go on the suppression list (mailpreferences.ini [BounceSettings])
 *
 * The code of cronjobs/mailbounces.php.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Mailbounces extends \Exponential\Runnable\CronjobPart
{
    /**
     * Reads the bounce mailbox (expMailBounceReader::run()): hard bounces and complaints put the address on the
     * suppression list. Does nothing while [BounceSettings] Reader is disabled or no server is set (the default).
     * Also on the console: exp:mail:bounces (with --dry-run).
     */
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        if ( !\expMailBounceReader::enabled() )
        {
            $cli->output( 'Skipped: the bounce mailbox is not configured or disabled (mailpreferences.ini [BounceSettings])' );
            return true;
        }
        if ( !\expMailPreferencesService::tableExists( 'expmail_suppression' ) )
        {
            $cli->output( 'Skipped: the e-mail preference tables are not installed (database update)' );
            return true;
        }
        $r = \expMailBounceReader::run();
        $c = $r['counts'];
        $cli->output( sprintf( 'Bounce mailbox: %d messages, %d hard bounces, %d soft bounces, %d complaints, %d addresses suppressed',
                               $c['messages'], $c['hard'], $c['soft'], $c['complaints'], $c['suppressed'] ) );
        if ( !$r['ok'] )
            $cli->error( 'Bounce mailbox: ' . $r['error'] );
        return $r['ok'];
    }
}

}
