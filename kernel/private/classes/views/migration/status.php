<?php
/**
 * The migration/status view (Setup > Migrations): every migration of the installation with its source, state and
 * file, what is pending, who holds the installation lock, the run going on in the background, the latest runs, and the
 * warnings of the status (changed files, orphans, pushes waiting). expMigrationStatus::report(), as
 * exp:migration:status. Policy migration/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Status extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $problem = '';
        try
        {
            $report = \expMigrationStatus::report( array( 'runs' => 5 ) );
        }
        catch ( \Throwable $e )
        {
            $report = array( 'migrations' => array(), 'summary' => array(), 'errors' => array(), 'warnings' => array(), 'runs' => array(), 'lock' => null, 'table' => false );
            $problem = $e->getMessage();
        }
        $report['migrations'] = \expMigrationAdmin::decorate( isset( $report['migrations'] ) ? $report['migrations'] : array() );
        $report['runs'] = \expMigrationAdmin::decorate( isset( $report['runs'] ) ? $report['runs'] : array() );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'report', $report );
        $tpl->setVariable( 'problem', $problem );
        $tpl->setVariable( 'pending', self::count( $report, array( \expMigration::PENDING, \expMigration::FAILED ) ) );
        $tpl->setVariable( 'lock_text', !empty( $report['lock'] ) ? \expMigrationLock::describe( $report['lock'] ) : '' );
        $tpl->setVariable( 'job', \expMigrationJob::running() );
        $tpl->setVariable( 'can_run', \expMigrationAdmin::canDo( 'run' ) );
        $tpl->setVariable( 'can_rollback', \expMigrationAdmin::canDo( 'rollback' ) );
        $tpl->setVariable( 'view', 'status' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:migration/status.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Setup' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/migration', 'Migrations' ) ) );
        return $Result;
    }

    /**
     * The migrations of a report in these states.
     *
     * @param array $report
     * @param string[] $states
     * @return int
     */
    public static function count( array $report, array $states )
    {
        $n = 0;
        // a kernel upgrade step of the stages code, libraries or finish is run by exp:upgrade (deferred), not here
        foreach ( isset( $report['migrations'] ) ? $report['migrations'] : array() as $m )
            if ( in_array( $m['state'], $states, true ) && ( !isset( $m['action'] ) || $m['action'] !== 'deferred' ) )
                $n++;
        return $n;
    }
}

}
?>
