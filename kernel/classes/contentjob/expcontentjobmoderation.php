<?php
/**
 * File containing the expContentJobModeration class.
 *
 * The content job 'moderation': an action of Dashboard > Recent content (moderation/recent, expModerationAction) on more
 * objects than [ModerationSettings] JobThreshold, in the background. Each batch runs the action on the next BatchSize
 * objects as the requesting user, with the same per-object code as an action in the request (the kernel's own
 * operations, each object's own policy), and adds their outcome (done, refused, failed, why) to the report, which
 * moderation/report/<job id> shows. At the end the action is recorded in the audit (content.moderation.action).
 *
 * An object whose batch is run again after a crash between its commit and its checkpoint is acted on again: the
 * kernel's operations look at the database first, so nothing changes twice (the report then says "held already",
 * "not hidden" ...).
 *
 * Parameters: action, ids (object ids), reason, section_id, state_id (expModerationAction::validate()).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobModeration implements expContentJobType
{
    /** @return bool the job type is registered here */
    public static function available()
    {
        return class_exists( 'expContentJob' ) && isset( expContentJob::types()['moderation'] );
    }

    protected static function tr( $text, array $args = array() )
    {
        if ( is_array( expContentJob::$settings ) )
        {
            foreach ( $args as $k => $v )
                $text = str_replace( $k, (string)$v, $text );
            return $text;
        }
        return ezpI18n::tr( 'kernel/contentjob', $text, null, $args );
    }

    public function validate( array $params, eZUser $user )
    {
        $access = $user->hasAccessTo( 'moderation', 'act' );
        if ( !isset( $access['accessWord'] ) || $access['accessWord'] === 'no' )
            throw new expContentJobException( self::tr( 'You may not run moderation actions (moderation/act).' ) );
        $form = expModerationAction::validate( $params );
        if ( $form['errors'] )
            throw new expContentJobException( self::tr( 'The moderation action is not complete: %errors.', array( '%errors' => implode( ', ', $form['errors'] ) ) ) );
        unset( $form['errors'] );
        return $form;
    }

    public function countNodes( array $params )
    {
        return isset( $params['ids'] ) ? count( (array)$params['ids'] ) : 0;
    }

    public function locks( array $params )
    {
        // object by object, each through the kernel's operation: no subtree is locked
        return array();
    }

    public function describe( array $params )
    {
        return self::tr( 'Moderation: %action on %count objects', array( '%action' => (string)$params['action'], '%count' => count( (array)$params['ids'] ) ) );
    }

    public function prepare( expContentJob $job )
    {
        $cp =& $job->checkpoint();
        if ( !$cp )
        {
            $cp = array( 'cursor' => 0, 'report' => array() );
            $job->setProgress( array( 'total' => count( (array)$job->params()['ids'] ), 'phase' => 'objects' ) );
        }
    }

    public function runBatch( expContentJob $job, $batchSize )
    {
        $cp =& $job->checkpoint();
        $params = $job->params();
        $ids = array_values( (array)$params['ids'] );
        $slice = array_slice( $ids, (int)$cp['cursor'], max( 1, (int)$batchSize ) );
        $report = $slice ? expModerationAction::run( $params, $slice ) : array();
        $cp['report'] = array_merge( (array)$cp['report'], $report );
        $cp['cursor'] += count( $slice );
        $c = expModerationAction::counts( $cp['report'] );
        return array( 'done' => count( $slice ), 'done_total' => (int)$cp['cursor'], 'finished' => $cp['cursor'] >= count( $ids ),
                      'message' => self::tr( '%done of %total objects: %ok done, %refused refused, %failed failed',
                                             array( '%done' => $cp['cursor'], '%total' => count( $ids ), '%ok' => $c['done'], '%refused' => $c['refused'], '%failed' => $c['failed'] ) ) );
    }

    public function afterBatch( expContentJob $job )
    {
        $cp = $job->checkpoint();
        $job->setResult( 'counts', expModerationAction::counts( (array)$cp['report'] ) );
        $job->setResult( 'report', (array)$cp['report'] );
    }

    public function finish( expContentJob $job )
    {
        $this->afterBatch( $job );
        $cp = $job->checkpoint();
        $params = $job->params();
        expModerationAction::audit( $params, (array)$cp['report'], 'job', $job->id() );
        $c = expModerationAction::counts( (array)$cp['report'] );
        $job->setProgress( array( 'phase' => 'done', 'message' => self::tr( '%ok done, %refused refused, %failed failed',
                                                                              array( '%ok' => $c['done'], '%refused' => $c['refused'], '%failed' => $c['failed'] ) ) ) );
    }
}
