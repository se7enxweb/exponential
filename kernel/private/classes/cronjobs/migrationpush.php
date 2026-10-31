<?php
/**
 * The cronjob part migrationpush (cronjobs/migrationpush.php; [CronjobPart-migrationpush] in cronjob.ini): pushes the
 * commits and tags of the migration runs whose push failed (exp_migration_run.push_state pending) to the remote of
 * the migrations repository again (expMigrationRepository::retryPushes()). Does nothing while no run waits.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Migrationpush extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $quiet = !empty( $scope['isQuiet'] );
        $db = \eZDB::instance();
        if ( !$db || !$db->isConnected() || \expMigrationRecord::runTableExists( $db ) !== true )
            return 0;
        $result = \expMigrationRepository::fromSettings()->retryPushes( $db );
        if ( !$quiet && $result['tried'] )
        {
            $cli->output( sprintf( 'Migrations: %d run(s) pushed again, %d still waiting', $result['pushed'], $result['pending'] ) );
            foreach ( $result['messages'] as $message )
                $cli->output( '  ' . $message );
        }
        else if ( !$quiet && $result['messages'] )
            foreach ( $result['messages'] as $message )
                $cli->warning( 'Migrations: ' . $message );
        return $result['pushed'];
    }
}

}
?>
