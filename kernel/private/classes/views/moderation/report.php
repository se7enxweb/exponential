<?php
/**
 * The moderation/report view: the result per object (done, refused, failed, and why) of an action of Dashboard > Recent
 * content that ran as the content job 'moderation'. Its owner, and users with content/jobs, see it; the job's progress
 * page (content/job/<id>) opens it when the job is done. Policy moderation/act.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Moderation
{

class Report extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $id = isset( $Params['JobID'] ) ? (string)$Params['JobID'] : '';
        $job = preg_match( '/^[A-Za-z0-9_-]{1,64}$/', $id ) ? \expContentJob::fetch( $id ) : null;
        $user = \eZUser::currentUser();
        if ( !$job || $job->type() !== 'moderation' )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $mayAccess = (int)$job->userID() === (int)$user->attribute( 'contentobject_id' ) && !$user->isAnonymous();
        if ( !$mayAccess && class_exists( 'Exponential\\View\\Kernel\\Content\\Job' ) )
            $mayAccess = \Exponential\View\Kernel\Content\Job::canSeeAll( $user );
        if ( !$mayAccess )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        $params = $job->params();
        $result = $job->result();
        $report = isset( $result['report'] ) ? (array)$result['report'] : array();
        $form = array( 'action' => isset( $params['action'] ) ? (string)$params['action'] : '', 'ids' => isset( $params['ids'] ) ? (array)$params['ids'] : array(),
                       'reason' => isset( $params['reason'] ) ? (string)$params['reason'] : '', 'section_id' => 0, 'state_id' => 0, 'errors' => array() );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'return_url', 'moderation/recent' );
        $tpl->setVariable( 'form', $form );
        $tpl->setVariable( 'action_name', Action::actionName( $form['action'] ) );
        $tpl->setVariable( 'job', array( 'id' => $job->id(), 'state' => $job->state(), 'active' => $job->isActive(), 'created' => $job->created(),
                                         'finished' => $job->finished(), 'error' => $job->error() ) );
        $tpl->setVariable( 'report', Action::translateReport( $report ) );
        $tpl->setVariable( 'counts', \expModerationAction::counts( $report ) );
        $tpl->setVariable( 'errors', array() );
        $tpl->setVariable( 'extra', '' );

        return array( 'content' => $tpl->fetch( 'design:moderation/report.tpl' ),
                      'path' => array( array( 'url' => 'content/dashboard', 'text' => \ezpI18n::tr( 'design/admin/moderation', 'Dashboard' ) ),
                                       array( 'url' => 'moderation/recent', 'text' => \ezpI18n::tr( 'design/admin/moderation', 'Recent content' ) ),
                                       array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/moderation', 'Result' ) ) ) );
    }
}

}
?>
