<?php
/**
 * @description E-mail preferences retention: consent log rows of people who are gone, expired confirmations, old suppression entries
 *
 * The code of cronjobs/mailpreferences.php.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Mailpreferences extends \Exponential\Runnable\CronjobPart
{
    /**
     * Retention of the e-mail preference system (expConsentLog::cleanup()): the consent log rows of people whose
     * account (or last preference) is gone, once mailpreferences.ini [ConsentSettings] RetentionDays have passed;
     * double opt-ins whose confirmation expired (the category is off again); suppression entries older than
     * [SuppressionSettings] RetentionDays (0, the default: kept forever). Also on the console:
     * exp:mail:consent cleanup.
     */
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        if ( !\expMailPreferencesService::tableExists( 'expmail_consent_log' ) )
        {
            $cli->output( 'Skipped: the e-mail preference tables are not installed (database update)' );
            return true;
        }
        $r = \expConsentLog::cleanup();
        $cli->output( sprintf( 'E-mail preferences retention: %d consent log rows, %d expired confirmations, %d suppression entries removed',
                               $r['consent'], $r['pending'], $r['suppression'] ) );
        return true;
    }
}

}
