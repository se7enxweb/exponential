<?php
/**
 * The migration/deployment view (/migration/deployment/<deploy id>): one deploy to remotes. While its job runs in the
 * background, page/migration_deploy.js follows it over migration/stream/<job id> (the progress events of
 * expMigrationProgress, with the state of every remote) and updates the row of each remote as it goes; when the job
 * has ended the page shows the report: every remote with its stage, state, the files sent, the migrations applied,
 * its problems, and the phases.
 *
 * A staged deploy that waits at its gate shows the next stage's remotes and Approve (ApproveButton, ConfirmApprove
 * ticked, "Verify it's you"), which starts exp:migration:deploy --continue=<deploy id> as a job. A deploy that some
 * remotes applied can be rolled back (RollbackButton, ConfirmRollback ticked, "Verify it's you"):
 * exp:migration:deploy --rollback=<deploy id> takes every one of them back to the caption taken before its run.
 * Policy migration/read; approving and rolling back need migration/deploy.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Deployment extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $deployId = isset( $Params['DeployID'] ) ? (string)$Params['DeployID'] : '';
        if ( !\expMigrationRecord::validRunId( $deployId ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $post = $isPost ? $_POST : array();
        $canDeploy = \expMigrationAdmin::canDo( 'deploy' );
        $login = (string)\eZUser::currentUser()->attribute( 'login' );
        $messages = array();

        $stored = \expMigrationRemoteDeploy::load( $deployId );
        $job = \expMigrationJob::forDeploy( $deployId );
        if ( $stored === null && $job === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $report = $stored;
        if ( $job && !$job['running'] && is_array( $job['report'] ) && isset( $job['report']['deploy'] ) && ( $report === null || $job['action'] === 'dry-run' ) )
            $report = $job['report']['deploy'];

        if ( $isPost && ( isset( $post['ApproveButton'] ) || isset( $post['RollbackButton'] ) ) )
        {
            if ( !$canDeploy )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $approve = isset( $post['ApproveButton'] );
            $actions = $stored !== null ? \expMigrationRemoteDeploy::actions( $stored ) : array( 'continue' => false, 'rollback' => false );
            if ( $job && $job['running'] )
                $messages[] = array( 'kind' => 'bad', 'text' => \expMigrationRemoteAdmin::tr( 'The deploy is still going on. Wait for it to end.' ) );
            else if ( $approve && !$actions['continue'] )
                $messages[] = array( 'kind' => 'bad', 'text' => \expMigrationRemoteAdmin::tr( 'This deploy does not wait at a gate.' ) );
            else if ( !$approve && !$actions['rollback'] )
                $messages[] = array( 'kind' => 'bad', 'text' => \expMigrationRemoteAdmin::tr( 'No remote applied this deploy: there is nothing to roll back.' ) );
            else if ( empty( $post[$approve ? 'ConfirmApprove' : 'ConfirmRollback'] ) )
                $messages[] = array( 'kind' => 'bad', 'text' => $approve ? \expMigrationRemoteAdmin::tr( 'Tick "Yes, deploy the next stage now" first.' )
                                                                         : \expMigrationRemoteAdmin::tr( 'Tick "Yes, roll this deploy back now" first.' ) );
            else
            {
                $started = \expMigrationJob::start( 'deploy', array( $approve ? 'continue' : 'rollback' => $deployId ), $login );
                if ( $started['ok'] )
                    return $Module->redirectTo( '/migration/deployment/' . $deployId );
                $messages[] = array( 'kind' => 'bad', 'text' => Run::refusal( $started ) );
            }
        }

        $progress = \expMigrationProgress::fold( array() );
        $offset = 0;
        if ( $job )
        {
            $read = \expMigrationProgress::read( $job['progress'], 0 );
            $progress = \expMigrationProgress::fold( $read['events'] );
            $offset = $read['offset'];
        }
        $decorated = $report !== null ? \expMigrationRemoteAdmin::decorate( $report ) : null;
        // the remotes the live table shows while the job runs (before its report): those of the progress
        $live = array();
        foreach ( $progress['hosts'] as $id => $host )
            $live[] = array( 'id' => (string)$id, 'state' => $host['state'], 'phase' => $host['phase'], 'kind' => \expMigrationRemoteAdmin::stateKind( $host['state'] ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'deploy_id', $deployId );
        $tpl->setVariable( 'report', $decorated );
        $tpl->setVariable( 'stored', $stored !== null );
        $tpl->setVariable( 'job', $job );
        $tpl->setVariable( 'job_running', $job ? (bool)$job['running'] : false );
        $tpl->setVariable( 'job_errors', $job && !$job['running'] && !$job['reported'] ? $job['errors'] : '' );
        $tpl->setVariable( 'progress', $progress );
        $tpl->setVariable( 'meter', \expMigrationProgress::meterStep( $progress['percent'] ) );
        $tpl->setVariable( 'progress_offset', $offset );
        $tpl->setVariable( 'live', $live );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'can_deploy', $canDeploy );
        $tpl->setVariable( 'can_run', \expMigrationAdmin::canDo( 'run' ) );
        $tpl->setVariable( 'can_rollback', \expMigrationAdmin::canDo( 'rollback' ) );
        $tpl->setVariable( 'view', 'deploy' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:migration/deployment.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Setup' ) ),
                                 array( 'url' => 'migration/status', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Migrations' ) ),
                                 array( 'url' => 'migration/deploy', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Deploys' ) ),
                                 array( 'url' => false, 'text' => $deployId ) );
        return $Result;
    }
}

}
?>
