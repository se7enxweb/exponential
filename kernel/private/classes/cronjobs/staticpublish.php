<?php
/**
 * The cronjob part staticpublish (cronjobs/staticpublish.php; [CronjobPart-staticpublish] in cronjob.ini): pushes the
 * static publishing targets whose runs could not be pushed (exp_static_run.push_state pending) again
 * (expStaticPublisher::retry(): the branch with every commit and the target's tags, never forced); then, for the
 * targets with Triggers[]=publish, builds the pages a content publish marked stale (expStaticPublishQueue, once
 * [StaticPublishSettings] PublishDebounce seconds passed since the last change; a full build above 200 nodes) and
 * publishes them; then builds in full and publishes the targets with Triggers[]=schedule whose Schedule matched
 * since their last scheduled run (expStaticSchedule; remembered in <WorkDir>/.schedule.json). Does nothing while
 * static publishing is switched off or nothing waits.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Staticpublish extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $quiet = !empty( $scope['isQuiet'] );
        if ( !\expStaticTarget::enabled() )
            return 0;
        $db = \eZDB::instance();
        if ( !$db || !$db->isConnected() || \expStaticStatus::exists( $db ) !== true )
            return 0;
        $pushed = 0;
        foreach ( \expStaticTarget::all() as $id => $target )
        {
            if ( !\expStaticStatus::pending( $db, $id ) )
                continue;
            $report = ( new \expStaticPublisher( $target, array( 'db' => $db, 'root' => defined( 'EXP_ROOT_DIR' ) ? EXP_ROOT_DIR : (string)getcwd() ) ) )->retry();
            if ( $report['push_state'] === \expStaticRepository::PUSHED )
                $pushed++;
            if ( !$quiet )
            {
                $cli->output( sprintf( 'Static publishing: %s, %d run(s) waited, push %s', $id, count( $report['runs'] ), $report['push_state'] ) );
                foreach ( array_merge( $report['messages'], $report['errors'] ) as $message )
                    $cli->output( '  ' . $message );
            }
        }
        return $pushed + $this->queued( $db, $cli, $quiet ) + $this->scheduled( $db, $cli, $quiet );
    }

    /** The targets with Triggers[]=publish whose changed pages waited long enough: built (only those) and published. */
    protected function queued( $db, $cli, $quiet )
    {
        $ini = \eZINI::instance( 'staticcache.ini' );
        $debounce = $ini->hasVariable( 'StaticPublishSettings', 'PublishDebounce' ) ? (int)$ini->variable( 'StaticPublishSettings', 'PublishDebounce' ) : \expStaticPublishQueue::DEBOUNCE;
        $n = 0;
        foreach ( \expStaticPublishQueue::targets() as $id )
        {
            $due = \expStaticPublishQueue::due( $db, $id, $debounce );
            $target = \expStaticTarget::get( $id );
            if ( $due === null || $target === null )
                continue;
            $started = time();
            $build = $due['full'] ? array( 'full' => true ) : array( 'nodes' => $due['nodes'] );
            $report = \expStaticAdmin::publisher( $target, array( 'by' => 'cronjob staticpublish', 'build' => $build ) )->publish( array( 'build' => true ) );
            if ( !$report['errors'] )
            {
                \expStaticPublishQueue::done( $db, $id, $started );
                $n++;
            }
            if ( !$quiet )
                $cli->output( sprintf( 'Static publishing: %s, %s after a content publish (%s), %s', $id, $due['full'] ? 'a full build' : count( $due['nodes'] ) . ' node(s)',
                                       $report['state'], $report['errors'] ? implode( '; ', $report['errors'] ) : 'push ' . $report['push_state'] ) );
        }
        return $n;
    }

    /** The targets with Triggers[]=schedule whose Schedule matched since their last scheduled run: built in full and published. */
    protected function scheduled( $db, $cli, $quiet )
    {
        $n = 0;
        foreach ( \expStaticTarget::all() as $id => $target )
        {
            $expression = (string)$target->setting( 'Schedule' );
            if ( !in_array( 'schedule', (array)$target->setting( 'Triggers' ), true ) || $expression === '' )
                continue;
            $publisher = \expStaticAdmin::publisher( $target, array( 'by' => 'cronjob staticpublish', 'build' => array( 'full' => true ) ) );
            $stateFile = $publisher->workDirectory() . '/.schedule.json';
            $state = is_file( $stateFile ) ? json_decode( (string)file_get_contents( $stateFile ), true ) : array();
            $state = is_array( $state ) ? $state : array();
            $last = isset( $state[$id] ) ? (int)$state[$id] : 0;
            if ( !\expStaticSchedule::due( $expression, $last ) )
                continue;
            // remembered before the run: a failed run waits for the next time of the schedule
            $state[$id] = time();
            if ( !is_dir( dirname( $stateFile ) ) )
                \eZDir::mkdir( dirname( $stateFile ), false, true );
            file_put_contents( $stateFile, json_encode( $state ), LOCK_EX );
            $report = $publisher->publish( array( 'build' => true ) );
            if ( !$report['errors'] )
                $n++;
            if ( !$quiet )
                $cli->output( sprintf( 'Static publishing: %s, scheduled (%s): %s', $id, $expression, $report['errors'] ? implode( '; ', $report['errors'] ) : $report['state'] . ', push ' . $report['push_state'] ) );
        }
        return $n;
    }
}

}
?>
