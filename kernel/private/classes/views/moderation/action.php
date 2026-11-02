<?php
/**
 * The moderation/action view: an action of Dashboard > Recent content on the selected objects (expModerationAction).
 *
 *   ApplyButton               from the list: the action, the selected ids (ObjectIDList[]) and what the action needs
 *                             (Reason, SectionID, StateID). A destructive action (trash, delete) shows its confirmation;
 *                             any other runs.
 *   ConfirmDestructiveButton  from the confirmation: runs the destructive action. It asks "Verify it's you"
 *                             (site.ini [ReauthGroup_delete]).
 *   CancelButton              back to the list.
 *
 * Up to [ModerationSettings] JobThreshold objects the action runs in the request and the page shows the result per
 * object; more run as the content job 'moderation', whose progress page (content/job/<id>) opens, and whose result is
 * moderation/report/<id>. Each object's own policies decide; the action is recorded in the audit. Policy
 * moderation/act.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Moderation
{

class Action extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $return = self::returnURL( $http->hasPostVariable( 'ReturnURL' ) ? $http->postVariable( 'ReturnURL' ) : '' );
        if ( strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) !== 'POST' || $Module->isCurrentAction( 'Cancel' ) )
            return $Module->redirectTo( '/' . $return );

        $form = \expModerationAction::validate( array(
            'action' => $http->hasPostVariable( 'ModerationAction' ) ? $http->postVariable( 'ModerationAction' ) : '',
            'ids' => $http->hasPostVariable( 'ObjectIDList' ) ? (array)$http->postVariable( 'ObjectIDList' ) : array(),
            'reason' => $http->hasPostVariable( 'Reason' ) ? $http->postVariable( 'Reason' ) : '',
            'section_id' => $http->hasPostVariable( 'SectionID' ) ? $http->postVariable( 'SectionID' ) : '',
            'state_id' => $http->hasPostVariable( 'StateID' ) ? $http->postVariable( 'StateID' ) : '' ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'return_url', $return );
        $tpl->setVariable( 'form', $form );
        $tpl->setVariable( 'action_name', self::actionName( $form['action'] ) );
        $tpl->setVariable( 'job', null );
        $tpl->setVariable( 'report', array() );
        $tpl->setVariable( 'counts', \expModerationAction::counts( array() ) );
        $tpl->setVariable( 'errors', array_map( array( __CLASS__, 'formError' ), $form['errors'] ) );
        $tpl->setVariable( 'extra', self::extraText( $form ) );
        $path = array( array( 'url' => 'content/dashboard', 'text' => \ezpI18n::tr( 'design/admin/moderation', 'Dashboard' ) ),
                       array( 'url' => $return, 'text' => \ezpI18n::tr( 'design/admin/moderation', 'Recent content' ) ) );

        if ( $form['errors'] )
        {
            $path[] = array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/moderation', 'Nothing done' ) );
            return array( 'content' => $tpl->fetch( 'design:moderation/report.tpl' ), 'path' => $path );
        }

        if ( \expModerationAction::isDestructive( $form['action'] ) && !$Module->isCurrentAction( 'Confirm' ) )
        {
            $names = array();
            foreach ( array_slice( $form['ids'], 0, 1000 ) as $id )
            {
                $row = \eZContentObject::fetch( (int)$id, false );
                $names[] = array( 'id' => (int)$id, 'name' => is_array( $row ) ? (string)$row['name'] : '' );
            }
            $tpl->setVariable( 'objects', $names );
            $settings = \expModerationList::settings();
            $tpl->setVariable( 'background', \expModerationAction::runsAsJob( count( $form['ids'] ), $settings['job_threshold'] ) );
            $path[] = array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/moderation', 'Confirm' ) );
            return array( 'content' => $tpl->fetch( 'design:moderation/confirm.tpl' ), 'path' => $path );
        }

        $settings = \expModerationList::settings();
        if ( \expModerationAction::runsAsJob( count( $form['ids'] ), $settings['job_threshold'] ) && \expContentJobModeration::available() )
        {
            $params = $form;
            unset( $params['errors'] );
            $params['mode'] = 'job';
            try
            {
                $job = \expContentJob::create( 'moderation', $params, \eZUser::currentUser() );
            }
            catch ( \expContentJobException $e )
            {
                $tpl->setVariable( 'errors', array( $e->getMessage() ) );
                $path[] = array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/moderation', 'Nothing done' ) );
                return array( 'content' => $tpl->fetch( 'design:moderation/report.tpl' ), 'path' => $path );
            }
            $job->set( 'gui_return_url', 'moderation/report/' . $job->id() );
            $job->save();
            if ( class_exists( 'Exponential\\View\\Kernel\\Content\\Job' ) )
                \Exponential\View\Kernel\Content\Job::spawnWithoutSession( $job );
            else
                $job->spawn();
            return $Module->redirectTo( '/content/job/' . $job->id() );
        }

        $report = \expModerationAction::run( $form, $form['ids'] );
        \expModerationAction::audit( $form, $report, 'now' );
        $tpl->setVariable( 'report', self::translateReport( $report ) );
        $tpl->setVariable( 'counts', \expModerationAction::counts( $report ) );
        $path[] = array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/moderation', 'Result' ) );
        return array( 'content' => $tpl->fetch( 'design:moderation/report.tpl' ), 'path' => $path );
    }

    /**
     * The address to go back to: a page of the list, else the list. Pure.
     *
     * @param mixed $url
     * @return string without the leading slash
     */
    public static function returnURL( $url )
    {
        $url = ltrim( is_string( $url ) ? $url : '', '/' );
        return preg_match( '#^moderation/recent(/[A-Za-z0-9_()%.:/-]*)?$#', $url ) ? $url : 'moderation/recent';
    }

    /** The name of an action in the interface */
    public static function actionName( $action )
    {
        $names = array( 'release' => 'Release the hold', 'hold' => 'Hold for review', 'hide' => 'Hide', 'reveal' => 'Reveal',
                        'trash' => 'Move to trash', 'delete' => 'Delete', 'section' => 'Assign section', 'state' => 'Assign state' );
        return isset( $names[$action] ) ? \ezpI18n::tr( 'design/admin/moderation', $names[$action] ) : '';
    }

    /** What the action sets, in words (the section's or the state's name) */
    protected static function extraText( array $form )
    {
        if ( $form['action'] === 'section' && $form['section_id'] )
        {
            $section = \eZSection::fetch( $form['section_id'] );
            return $section ? (string)$section->attribute( 'name' ) : '';
        }
        if ( $form['action'] === 'state' && $form['state_id'] )
        {
            $state = \eZContentObjectState::fetchById( $form['state_id'] );
            return $state ? (string)$state->attribute( 'current_translation' )->attribute( 'name' ) : '';
        }
        return '';
    }

    /** The message of a problem with the form */
    public static function formError( $code )
    {
        switch ( $code )
        {
            case 'action':
                return \ezpI18n::tr( 'design/admin/moderation', 'Choose an action.' );
            case 'ids':
                return \ezpI18n::tr( 'design/admin/moderation', 'Select at least one item.' );
            case 'too_many':
                return \ezpI18n::tr( 'design/admin/moderation', 'Select at most 1000 items at a time.' );
            case 'reason':
                return \ezpI18n::tr( 'design/admin/moderation', 'Say why the items are held.' );
            case 'section':
                return \ezpI18n::tr( 'design/admin/moderation', 'Choose the section.' );
            case 'state':
                return \ezpI18n::tr( 'design/admin/moderation', 'Choose the state.' );
        }
        return \ezpI18n::tr( 'design/admin/moderation', 'The form could not be read.' );
    }

    /**
     * The report with its messages in the interface language (the messages of expModerationAction and
     * expReviewHoldAuto are English sentences, translated in the context kernel/moderation).
     *
     * @param array $report
     * @return array
     */
    public static function translateReport( array $report )
    {
        foreach ( $report as $i => $entry )
            $report[$i]['message'] = \ezpI18n::tr( 'kernel/moderation', (string)$entry['message'] );
        return $report;
    }
}

}
?>
