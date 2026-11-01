<?php
/**
 * The migration/history view: the runs of exp_migration_run, newest first, filtered by state (/(state)/failed), each
 * with its user, environment, captions, commit, tag and push; the jobs started in the administration (rollbacks have
 * no run of their own); resume (a failed or interrupted run) and rollback buttons for users with migration/run and
 * migration/rollback. Policy migration/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class History extends \Exponential\Runnable\ModuleView
{
    /** The runs read. */
    const LIMIT = 200;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $state = isset( $Params['State'] ) && in_array( (string)$Params['State'], \expMigrationAdmin::RUN_STATES, true ) ? (string)$Params['State'] : '';
        $runs = array();
        $ready = false;
        $problem = '';
        try
        {
            $db = \eZDB::instance();
            $ready = \expMigrationRecord::runTableExists( $db ) === true;
            if ( $ready )
                foreach ( \expMigrationRecord::runs( $db, self::LIMIT ) as $row )
                    $runs[] = \expMigrationStatus::runSummary( $row ) + array( 'resumable' => in_array( $row['state'], \expMigrationResume::RESUMABLE, true ) );
        }
        catch ( \Throwable $e )
        {
            $problem = $e->getMessage();
        }
        $counts = array( 'all' => count( $runs ) );
        foreach ( \expMigrationAdmin::RUN_STATES as $name )
            $counts[$name] = count( \expMigrationAdmin::filterRuns( $runs, $name ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'runs', \expMigrationAdmin::decorate( \expMigrationAdmin::filterRuns( $runs, $state ) ) );
        $tpl->setVariable( 'state', $state );
        $tpl->setVariable( 'states', \expMigrationAdmin::RUN_STATES );
        $tpl->setVariable( 'counts', $counts );
        $tpl->setVariable( 'ready', $ready );
        $tpl->setVariable( 'problem', $problem );
        $tpl->setVariable( 'jobs', \expMigrationJob::all( 20 ) );
        $tpl->setVariable( 'running', \expMigrationJob::running() );
        $tpl->setVariable( 'can_run', \expMigrationAdmin::canDo( 'run' ) );
        $tpl->setVariable( 'can_rollback', \expMigrationAdmin::canDo( 'rollback' ) );
        $tpl->setVariable( 'view', 'history' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:migration/history.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Setup' ) ),
                                 array( 'url' => 'migration/status', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Migrations' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/migration', 'History' ) ) );
        return $Result;
    }
}

}
?>
