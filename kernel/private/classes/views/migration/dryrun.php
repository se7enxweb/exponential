<?php
/**
 * The migration/dryrun view: what a run would do, nothing changed. Every pending migration with the lines of its
 * plan(), the checksum guard, and what the caption flow would do (code check, captions, commit, tag, push), as
 * exp:migration:dry-run. Users with migration/run can also simulate the run (Simulate, a POST that asks "Verify it's
 * you"): the migrations really run, in a transaction that is rolled back afterwards, and the page lists what each one
 * changed in the database (PostgreSQL and SQLite; on other databases the plan is listed instead), as
 * exp:migration:dry-run --simulate --diff. Policy migration/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Dryrun extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $canRun = \expMigrationAdmin::canDo( 'run' );
        $simulate = $isPost && $http->hasPostVariable( 'SimulateButton' );
        $messages = array();
        $problem = '';
        $report = null;
        $supported = false;
        try
        {
            $db = \eZDB::instance();
            $supported = \expMigrationSimulation::supports( \expKernelTableSchema::type( $db ) );
            if ( $simulate && !$canRun )
            {
                $messages[] = array( 'kind' => 'bad', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Simulating a run needs the policy migration/run.' ) );
                $simulate = false;
            }
            if ( $simulate && \expMigrationJob::running() )
            {
                $messages[] = array( 'kind' => 'warn', 'text' => \ezpI18n::tr( 'design/admin/migration', 'A run is going on: simulate when it has ended.' ) );
                $simulate = false;
            }
            if ( $simulate )
                $report = self::simulate( $db );
            else
                $report = \expMigrationRunner::migrate( array( 'apply' => false, 'db' => $db, 'command' => 'migration/dryrun' ) );
        }
        catch ( \Throwable $e )
        {
            $problem = $e->getMessage();
        }

        if ( is_array( $report ) && isset( $report['migrations'] ) )
            $report['migrations'] = \expMigrationAdmin::decorate( (array)$report['migrations'] );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'report', $report );
        $tpl->setVariable( 'simulated', $simulate && is_array( $report ) );
        $tpl->setVariable( 'supported', $supported );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'problem', $problem );
        $tpl->setVariable( 'can_run', $canRun );
        $tpl->setVariable( 'can_rollback', \expMigrationAdmin::canDo( 'rollback' ) );
        $tpl->setVariable( 'view', 'dryrun' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:migration/dryrun.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Setup' ) ),
                                 array( 'url' => 'migration/status', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Migrations' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/migration', 'Dry run' ) ) );
        return $Result;
    }

    /**
     * The simulated run of the pending migrations, as exp:migration:dry-run --simulate --diff.
     *
     * @param \eZDBInterface $db
     * @return array the report
     */
    protected static function simulate( $db )
    {
        $context = new \expMigrationContext( $db, true, array( 'no-mirror' => true, 'login' => \eZUser::currentUser()->attribute( 'login' ) ) );
        $errors = array();
        $migrations = \expMigrationRunner::migrations( $context->environment(), $errors );
        if ( $errors )
            return array( 'mode' => 'simulate', 'migrations' => array(), 'summary' => array(), 'errors' => $errors, 'warnings' => array(), 'exit' => \expUpgrade::EXIT_FAILED );
        $simulation = new \expMigrationSimulation( $context, array( 'diff' => true, 'ignore-tables' => \expMigrationCaption::settingsFrom()['ignore-tables'] ) );
        return $simulation->run( $migrations );
    }
}

}
?>
