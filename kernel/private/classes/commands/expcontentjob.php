<?php
/**
 * The command bin/php/expcontentjob.php (./console exp:expcontentjob): content jobs, the large subtree removes
 * and copies that run in batches in the background (kernel/classes/contentjob/).
 *
 *   list [--all]                    the jobs (queued, running and failed; --all: every job)
 *   show <id> [--json]              one job: state, progress, result, the end of its log
 *   run <id>                        runs a job in the foreground (what the detached worker runs)
 *   resume <id> [--background]      a failed job, or one whose worker died, from its last checkpoint
 *   cancel <id>                     stops a job after its current batch
 *   remove <node id>[,<node id>...] [--trash|--delete] [--background]
 *   copy <src node> <dest parent> [--all-versions] [--keep-creator] [--keep-time] [--background]
 *   move <node> <new parent>, hide <node>, reveal <node>, section <node> <section id>, state <node> <state id>,
 *   addlocation <target node> <node>[,...], removelocation <node>[,...]       (all with [--background])
 *
 * remove and copy create the job as the user given with --login, else site.ini [UserSettings] UserCreatorID,
 * with the same permission checks and locks as the views, and run it in the foreground unless --background.
 * Started as root (as Velocity does), the command becomes the site user before it reads a setting or opens the
 * database, so every file and job it writes belongs to that user.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 * @description Run, list, resume and cancel content jobs: large subtree removes and copies in batches
 */

namespace Exponential\Command\Kernel
{

class Expcontentjob extends \Exponential\Runnable\Command
{
    public function run()
    {
        $root = dirname( __DIR__, 4 );
        $dropError = \expContentJobWorker::dropPrivileges( $root );

        $cli = $this->cli();
        $script = $this->script( array( 'description' => "Content jobs: large subtree removes and copies, run in batches in the background.\n\n" .
                                                         "  list [--all]                         the jobs\n" .
                                                         "  show <id> [--json]                   one job and the end of its log\n" .
                                                         "  run <id>                             run a job in the foreground\n" .
                                                         "  resume <id> [--background]           resume a failed job or one whose worker died\n" .
                                                         "  cancel <id>                          stop a job after its current batch\n" .
                                                         "  remove <node id>[,...] [--trash|--delete] [--background]\n" .
                                                         "  copy <src node> <dest parent> [--all-versions] [--keep-creator] [--keep-time] [--background]\n" .
                                                         "  move <node> <new parent> | hide <node> | reveal <node> | section <node> <section id> | state <node> <state id>\n" .
                                                         "  addlocation <target node> <node>[,...] | removelocation <node>[,...]        (all: [--background])\n",
                                        'use-session' => false,
                                        'use-modules' => true,
                                        'use-extensions' => true,
                                        'user' => true ) );
        $options = $this->startup( '[all][json][background][trash][delete][all-versions][keep-creator][keep-time]', '',
                                   array( 'all' => 'list: every job, not only the active and failed ones',
                                          'json' => 'show: the job as JSON',
                                          'background' => 'remove, copy, resume: start a detached worker and return',
                                          'trash' => 'remove: move the objects to the trash (the default of content.ini [RemoveSettings])',
                                          'delete' => 'remove: delete the objects permanently',
                                          'all-versions' => 'copy: copy all versions of each object, not only the current one',
                                          'keep-creator' => 'copy: keep the owner and creator of the copied objects',
                                          'keep-time' => 'copy: keep the creation and modification times' ),
                                   false, array( 'user' => true ) );
        if ( $dropError !== null )
        {
            $this->error( $dropError );
            $this->shutdown( 1 );
        }
        // all languages, as the cronjobs: the command finds nodes in a language the default siteaccess does not list
        \eZContentLanguage::setCronjobMode( true );
        $args = isset( $options['arguments'] ) ? array_values( $options['arguments'] ) : array();
        $sub = $args ? strtolower( (string) array_shift( $args ) ) : 'list';

        switch ( $sub )
        {
            case 'list':
                $this->shutdown( $this->listJobs( !empty( $options['all'] ) ) );
            case 'show':
                $this->shutdown( $this->showJob( $args ? $args[0] : '', !empty( $options['json'] ) ) );
            case 'run':
                $this->shutdown( $this->runJob( $args ? $args[0] : '' ) );
            case 'resume':
                $this->shutdown( $this->resumeJob( $args ? $args[0] : '', !empty( $options['background'] ) ) );
            case 'cancel':
                $this->shutdown( $this->cancelJob( $args ? $args[0] : '' ) );
            case 'remove':
                $ids = array();
                foreach ( $args as $arg )
                    foreach ( explode( ',', (string) $arg ) as $id )
                        if ( trim( $id ) !== '' )
                            $ids[] = (int) $id;
                $params = array( 'node_ids' => $ids );
                if ( !empty( $options['delete'] ) )
                    $params['move_to_trash'] = false;
                else if ( !empty( $options['trash'] ) )
                    $params['move_to_trash'] = true;
                $this->shutdown( $this->createJob( 'remove', $params, !empty( $options['background'] ) ) );
            case 'copy':
                if ( count( $args ) < 2 )
                {
                    $this->error( 'copy needs the source node and the destination parent node' );
                    $this->shutdown( 1 );
                }
                $this->shutdown( $this->createJob( 'copy', array( 'source_node_id' => (int) $args[0], 'destination_node_id' => (int) $args[1],
                                                                  'all_versions' => !empty( $options['all-versions'] ),
                                                                  'keep_creator' => !empty( $options['keep-creator'] ),
                                                                  'keep_time' => !empty( $options['keep-time'] ) ),
                                                   !empty( $options['background'] ) ) );
            case 'move':
            case 'section':
            case 'state':
                if ( count( $args ) < 2 )
                {
                    $this->error( "$sub needs the node and the " . ( $sub === 'move' ? 'new parent node' : ( $sub === 'section' ? 'section id' : 'state id' ) ) );
                    $this->shutdown( 1 );
                }
                $key = array( 'move' => 'new_parent_node_id', 'section' => 'section_id', 'state' => 'state_id' );
                $this->shutdown( $this->createJob( $sub, array( 'node_id' => (int) $args[0], $key[$sub] => (int) $args[1] ), !empty( $options['background'] ) ) );
            case 'hide':
            case 'reveal':
                $this->shutdown( $this->createJob( $sub, array( 'node_id' => $args ? (int) $args[0] : 0 ), !empty( $options['background'] ) ) );
            case 'addlocation':
                if ( count( $args ) < 2 )
                {
                    $this->error( 'addlocation needs the target node and the selected nodes' );
                    $this->shutdown( 1 );
                }
                $target = (int) array_shift( $args );
                $this->shutdown( $this->createJob( 'addlocation', array( 'target_node_id' => $target, 'node_ids' => $this->idList( $args ) ),
                                                   !empty( $options['background'] ) ) );
            case 'removelocation':
                $this->shutdown( $this->createJob( 'removelocation', array( 'node_ids' => $this->idList( $args ) ), !empty( $options['background'] ) ) );
            default:
                $this->error( "Unknown action '$sub'. Use list, show, run, resume, cancel, remove, copy, move, hide, reveal, section, state, addlocation or removelocation (--help)." );
                $this->shutdown( 1 );
        }
    }

    /** Node ids from arguments like "42,43 44". */
    protected function idList( array $args )
    {
        $ids = array();
        foreach ( $args as $arg )
            foreach ( explode( ',', (string) $arg ) as $id )
                if ( trim( $id ) !== '' )
                    $ids[] = (int) $id;
        return $ids;
    }

    protected function listJobs( $all )
    {
        $jobs = \expContentJob::listFor( null, $all ? null : array( 'queued', 'running', 'failed' ) );
        if ( !$jobs )
        {
            $this->output( $all ? 'No content jobs.' : 'No active or failed content jobs (--all lists every job).' );
            return 0;
        }
        foreach ( $jobs as $job )
        {
            $p = $job->progress();
            $state = $job->state();
            if ( $job->isActive() && !$job->workerAlive() )
                $state .= ' (no worker)';
            $this->output( sprintf( '%-26s %-6s %-20s %3d%% %7d/%-7d %s', $job->id(), $job->type(), $state, $p['percent'], $p['done'], $p['total'],
                                    $job->description() ) );
        }
        return 0;
    }

    protected function showJob( $id, $json )
    {
        $job = \expContentJob::fetch( $id );
        if ( !$job )
        {
            $this->error( "No content job '$id'." );
            return 3;
        }
        $data = $job->toArray( 30 );
        $data['worker_alive'] = $job->workerAlive();
        $data['last_batch'] = $job->get( 'last_batch' );
        $data['locks'] = \expContentJobLock::locksOf( $job->id() );
        if ( $json )
        {
            $this->cli()->output( json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            return 0;
        }
        $p = $data['progress'];
        $this->output( 'Job:       ' . $job->id() . ' (' . $job->type() . ')' );
        $this->output( 'What:      ' . $job->description() );
        $this->output( 'State:     ' . $job->state() . ( $data['worker_alive'] ? ', a worker is running it' : '' )
                       . ( $job->cancelRequested() && $job->isActive() ? ', cancel requested' : '' ) );
        $this->output( 'Progress:  ' . $p['percent'] . '% (' . $p['done'] . '/' . $p['total'] . ', ' . $p['batch'] . ' batches' . ( $p['phase'] ? ', ' . $p['phase'] : '' ) . ')' );
        if ( $p['message'] !== '' )
            $this->output( 'Message:   ' . $p['message'] );
        if ( $job->error() !== '' )
            $this->output( 'Error:     ' . $job->error() );
        foreach ( $job->result() as $key => $value )
            if ( !is_array( $value ) )
                $this->output( sprintf( '%-10s %s', $key . ':', $value ) );
        $this->output( 'Log:' );
        foreach ( $data['log'] as $line )
            $this->output( '  ' . $line );
        return 0;
    }

    protected function runJob( $id )
    {
        $worker = new \expContentJobWorker();
        if ( !$this->isQuiet() )
            $worker->output = function ( $line ) { $this->output( $line ); };
        return $worker->run( $id );
    }

    protected function resumeJob( $id, $background )
    {
        $job = \expContentJob::fetch( $id );
        if ( !$job )
        {
            $this->error( "No content job '$id'." );
            return 3;
        }
        if ( $job->workerAlive() )
        {
            $this->output( "Job $id is running in a worker already." );
            return 2;
        }
        if ( !$job->canResume() )
        {
            $this->error( "Job $id is " . $job->state() . ' and cannot be resumed.' );
            return 2;
        }
        $job->resume( false );
        if ( $background )
        {
            $ok = $job->spawn();
            $this->output( $ok ? "Job $id resumed in the background." : "Job $id is queued; the cronjob part contentjobs will start it." );
            return 0;
        }
        return $this->runJob( $id );
    }

    protected function cancelJob( $id )
    {
        $job = \expContentJob::fetch( $id );
        if ( !$job )
        {
            $this->error( "No content job '$id'." );
            return 3;
        }
        if ( !$job->cancel() )
        {
            $this->error( "Job $id is " . $job->state() . ' and cannot be cancelled.' );
            return 2;
        }
        $job = \expContentJob::fetch( $id );
        $this->output( $job->state() === 'cancelled' ? "Job $id cancelled." : "Job $id will stop after its current batch." );
        return 0;
    }

    protected function createJob( $type, array $params, $background )
    {
        $user = \eZUser::currentUser();
        if ( !$user || $user->isAnonymous() )
        {
            $userID = \eZINI::instance()->variable( 'UserSettings', 'UserCreatorID' );
            $user = \eZUser::fetch( $userID );
            if ( !$user )
            {
                $this->error( "Cannot get the user $userID (site.ini [UserSettings] UserCreatorID); give one with --login." );
                return 1;
            }
            \eZUser::setCurrentlyLoggedInUser( $user, $userID, \eZUser::NO_SESSION_REGENERATE );
        }
        try
        {
            $job = \expContentJob::create( $type, $params, $user );
        }
        catch ( \expContentJobException $e )
        {
            $this->error( $e->getMessage() );
            return 1;
        }
        $this->output( 'Job ' . $job->id() . ' created: ' . $job->description() . ' (' . $job->progress()['total'] . ' nodes)' );
        if ( $background )
        {
            $ok = $job->spawn();
            $this->output( $ok ? 'Running in the background: php bin/php/expcontentjob.php show ' . $job->id()
                               : 'Queued; the cronjob part contentjobs will start it.' );
            return 0;
        }
        return $this->runJob( $job->id() );
    }
}

}
