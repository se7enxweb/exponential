<?php
/**
 * The migration/recording view: recordings of changes. Without a parameter the list: start and stop a recording of
 * the signed-in user's own changes, open and close the site-wide window (every user's changes; asks "Verify it's you",
 * site.ini [ReauthGroup_system]), and every recording with its kind, state and number of events. With a recording
 * id (migration/recording/<id>) the events it captured as the migration steps they stand for, the steps a migration of
 * it would have, and discard. The bar "Recording..." that the administration shows on every page while the user's
 * changes are recorded (design:parts/exp_migration_recording_bar.tpl) posts its Stop here with a RedirectURI back.
 * Policy migration/edit.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Recording extends \Exponential\Runnable\ModuleView
{
    /** The events a page lists at most. */
    const EVENT_LIMIT = 1000;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $recordingId = isset( $Params['RecordingID'] ) && \expMigrationRecording::validId( (string)$Params['RecordingID'] ) ? (string)$Params['RecordingID'] : '';
        $userId = (int)\eZUser::currentUserID();
        $messages = array();
        $problem = '';
        $db = null;
        $ready = false;
        try
        {
            $db = \eZDB::instance();
            $ready = \expMigrationRecording::tablesExist( $db ) === true;
            // a window that has ended is stopped before the page shows it
            if ( $ready )
                \expMigrationRecording::closeEnded( $db );
        }
        catch ( \Throwable $e )
        {
            $problem = $e->getMessage();
        }

        if ( $isPost && $ready )
        {
            $redirect = self::redirectTarget( (string)$http->postVariable( 'RedirectURI', '' ) );
            $errors = array();
            $done = null;
            if ( $http->hasPostVariable( 'StartButton' ) || $http->hasPostVariable( 'StartSiteButton' ) )
            {
                $site = $http->hasPostVariable( 'StartSiteButton' );
                $label = trim( preg_replace( '/\s+/', ' ', (string)$http->postVariable( 'RecordingLabel', '' ) ) );
                $row = null;
                $until = self::windowEnd($site ? (string)$http->postVariable( 'RecordingMinutes', '' ) : '', time() );
                if ( $until === null )
                    $errors[] = 'minutes';
                else
                    $row = \expMigrationRecording::start( $db, $site ? \expMigrationRecording::MODE_SITE : \expMigrationRecording::MODE_SESSION,
                                                          $userId, mb_substr( $label, 0, 255 ), $errors, $until );
                // a window of time: the page says when it closes by itself
                if ( $row !== null )
                    $done = $site ? \ezpI18n::tr( 'design/admin/migration', 'The site-wide recording has started: every change of every user is recorded until you stop it.' )
                                  : \ezpI18n::tr( 'design/admin/migration', 'Recording has started: your changes are recorded until you stop it.' );
            }
            else if ( $http->hasPostVariable( 'StopButton' ) )
            {
                $stop = trim( (string)$http->postVariable( 'StopRecordingID', '' ) );
                $row = \expMigrationRecording::validId( $stop ) ? \expMigrationRecording::get( $db, $stop ) : null;
                if ( $row === null )
                    $errors[] = 'no recording';
                else if ( !self::mayStop( $row, $userId ) )
                    $errors[] = 'not yours';
                else if ( \expMigrationRecording::stop( $db, $stop, $errors ) && !$errors )
                    $done = \ezpI18n::tr( 'design/admin/migration', 'Recording stopped. What it captured stays until you discard it.' );
            }
            else if ( $http->hasPostVariable( 'DiscardButton' ) )
            {
                $discard = trim( (string)$http->postVariable( 'DiscardRecordingID', '' ) );
                if ( empty( $_POST['ConfirmDiscard'] ) )
                    $errors[] = 'confirm';
                else if ( \expMigrationRecording::validId( $discard ) && \expMigrationRecording::discard( $db, $discard, $errors ) )
                {
                    $done = \ezpI18n::tr( 'design/admin/migration', 'The recording and its events are removed.' );
                    $recordingId = '';
                    $redirect = '/migration/recording';
                }
            }
            else if ( $http->hasPostVariable( 'GenerateButton' ) && $recordingId !== '' )
            {
                $name = trim( (string)$http->postVariable( 'MigrationName', '' ) );
                $report = \expMigrationRecordingGenerator::fromRecording( $db, $recordingId, array( 'name' => $name, 'apply' => true ) );
                if ( !empty( $report['written'] ) )
                    $done = \ezpI18n::tr( 'design/admin/migration', 'Wrote the migration %file. Changes no step can make are named at the top of the file, to be made by hand.',
                                          null, array( '%file' => $report['generated']['path'] ) );
                else
                    foreach ( $report['errors'] as $error )
                        $messages[] = array( 'kind' => 'bad', 'text' => \ezpI18n::tr( 'design/admin/migration', 'No migration was written: %reason', null, array( '%reason' => $error ) ) );
            }
            if ( $done !== null && $redirect !== '' )
            {
                $http->setSessionVariable( 'ExpMigrationRecordingMessage', $done );
                return $Module->redirectTo( $redirect );
            }
            if ( $done !== null )
                $messages[] = array( 'kind' => 'ok', 'text' => $done );
            foreach ( $errors as $error )
                $messages[] = array( 'kind' => 'bad', 'text' => self::errorText( $error ) );
        }
        if ( $http->hasSessionVariable( 'ExpMigrationRecordingMessage' ) )
        {
            $messages[] = array( 'kind' => 'ok', 'text' => (string)$http->sessionVariable( 'ExpMigrationRecordingMessage' ) );
            $http->removeSessionVariable( 'ExpMigrationRecordingMessage' );
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'ready', $ready );
        $tpl->setVariable( 'problem', $problem );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'enabled', \expMigrationRecorder::settings()['enabled'] );
        $tpl->setVariable( 'can_run', \expMigrationAdmin::canDo( 'run' ) );
        $tpl->setVariable( 'can_rollback', \expMigrationAdmin::canDo( 'rollback' ) );
        $tpl->setVariable( 'view', 'recording' );
        $path = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Setup' ) ),
                       array( 'url' => 'migration/status', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Migrations' ) ) );

        $recording = $recordingId !== '' && $ready ? \expMigrationRecording::get( $db, $recordingId ) : null;
        if ( $recording !== null )
        {
            $events = \expMigrationRecording::events( $db, $recordingId );
            $plan = \expMigrationRecordingGenerator::plan( array_values( array_filter( array_column( $events, 'event' ) ) ) );
            $tpl->setVariable( 'recording', self::decorate( \expMigrationRecording::summary( $recording, $events ) ) );
            $tpl->setVariable( 'events', self::eventRows( array_slice( $events, 0, self::EVENT_LIMIT ) ) );
            $tpl->setVariable( 'more_events', max( 0, count( $events ) - self::EVENT_LIMIT ) );
            $tpl->setVariable( 'plan', self::planRows( $plan ) );
            $tpl->setVariable( 'missing_step_types', \expMigrationRecordingGenerator::missingStepTypes( $plan ) );
            $tpl->setVariable( 'may_stop', self::mayStop( $recording, $userId ) );
            $path[] = array( 'url' => 'migration/recording', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Recording' ) );
            $path[] = array( 'url' => false, 'text' => $recordingId );
            $Result = array( 'content' => $tpl->fetch( 'design:migration/recording_view.tpl' ), 'path' => $path );
            return $Result;
        }
        if ( $recordingId !== '' )
            $messages[] = array( 'kind' => 'bad', 'text' => \ezpI18n::tr( 'design/admin/migration', 'There is no recording %id.', null, array( '%id' => $recordingId ) ) );
        $tpl->setVariable( 'messages', $messages );
        $rows = $ready ? \expMigrationRecording::all( $db, 200 ) : array();
        $mine = null;
        $site = null;
        foreach ( $rows as $row )
        {
            if ( $row['state'] !== \expMigrationRecording::RECORDING )
                continue;
            if ( $row['kind'] === \expMigrationRecording::MODE_SITE )
                $site = $row;
            else if ( (int)$row['user_id'] === $userId )
                $mine = $row;
        }
        $tpl->setVariable( 'recordings', array_map( array( __CLASS__, 'decorate' ), $rows ) );
        $tpl->setVariable( 'mine', $mine ? self::decorate( $mine ) : null );
        $tpl->setVariable( 'site', $site ? self::decorate( $site ) : null );
        $path[] = array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/migration', 'Recording' ) );
        return array( 'content' => $tpl->fetch( 'design:migration/recording.tpl' ), 'path' => $path );
    }

    /**
     * Whether a user may stop a recording: their own, or the site-wide one (every holder of migration/edit may close
     * the window). Pure.
     *
     * @param array $row
     * @param int $userId
     * @return bool
     */
    public static function mayStop( array $row, $userId )
    {
        return $row['kind'] === \expMigrationRecording::MODE_SITE || (int)$row['user_id'] === (int)$userId;
    }

    /**
     * A path inside the installation to go back to after the bar's Stop, or '' (anything else is ignored). Pure.
     *
     * @param string $uri
     * @return string
     */
    public static function redirectTarget( $uri )
    {
        $uri = trim( (string)$uri );
        if ( $uri === '' || $uri[0] !== '/' || strpos( $uri, '//' ) === 0 || preg_match( '#[\\\\\r\n]#', $uri ) || strpos( $uri, '://' ) !== false )
            return '';
        return $uri;
    }

    /**
     * A recording for the templates: the user's name for a session recording (the name of the content object, never
     * the login or the e-mail address).
     *
     * @param array $row
     * @return array
     */
    public static function decorate( array $row )
    {
        $row['user_name'] = '';
        if ( $row['kind'] === \expMigrationRecording::MODE_SESSION && (int)$row['user_id'] > 0 )
        {
            $object = \eZContentObject::fetch( (int)$row['user_id'] );
            $row['user_name'] = $object ? (string)$object->attribute( 'name' ) : '#' . (int)$row['user_id'];
        }
        return $row;
    }

    /**
     * The events for the template: time, audit name, step, subject, left out and notes.
     *
     * @param array[] $events expMigrationRecording::events()
     * @return array[]
     */
    public static function eventRows( array $events )
    {
        $rows = array();
        foreach ( $events as $e )
        {
            $event = is_array( $e['event'] ) ? $e['event'] : array();
            $rows[] = array( 'recorded_at' => $e['recorded_at'], 'audit_name' => $e['audit_name'], 'step' => $e['step_type'] . '/' . $e['step_mode'],
                             'subject' => $e['subject'], 'omitted' => isset( $event['omitted'] ) ? (array)$event['omitted'] : array(),
                             'notes' => isset( $event['notes'] ) ? (array)$event['notes'] : array() );
        }
        return $rows;
    }

    /**
     * The steps of a plan for the template.
     *
     * @param array[] $plan expMigrationRecordingGenerator::plan()
     * @return array[]
     */
    public static function planRows( array $plan )
    {
        $rows = array();
        foreach ( $plan as $p )
            $rows[] = array( 'step' => $p['step']['type'] . '/' . $p['step']['mode'], 'subject' => $p['subject'],
                             'audit' => implode( ', ', array_unique( $p['audit'] ) ), 'omitted' => $p['omitted'] );
        return $rows;
    }

    /**
     * The message of a refusal.
     *
     * @param string $error
     * @return string
     */
    /**
     * The end of a site-wide window from the minutes of the form: 0 without, null when it is no number of minutes
     * from 1 to 44640 (31 days). Pure.
     *
     * @param string $minutes
     * @param int $now
     * @return int|null
     */
    public static function windowEnd( $minutes, $now )
    {
        $minutes = trim( (string)$minutes );
        if ( $minutes === '' )
            return 0;
        if ( !ctype_digit( $minutes ) || (int)$minutes < 1 || (int)$minutes * 60 > \expMigrationRecording::MAX_WINDOW )
            return null;
        return (int)$now + 60 * (int)$minutes;
    }

    public static function errorText( $error )
    {
        switch ( $error )
        {
            case 'minutes':
                return \ezpI18n::tr( 'design/admin/migration', 'The minutes are a whole number from 1 to 44640 (31 days), or empty.' );
            case 'confirm':
                return \ezpI18n::tr( 'design/admin/migration', 'Tick "Yes, discard this recording" first.' );
            case 'not yours':
                return \ezpI18n::tr( 'design/admin/migration', 'Only the user who recorded may stop a session recording.' );
            case 'no recording':
                return \ezpI18n::tr( 'design/admin/migration', 'Choose the recording to stop.' );
        }
        if ( strpos( $error, 'already' ) !== false )
            return \ezpI18n::tr( 'design/admin/migration', 'A recording of this kind is running already.' );
        if ( strpos( $error, 'tables' ) !== false )
            return \ezpI18n::tr( 'design/admin/migration', 'The recording tables do not exist yet: run the upgrade (exp:upgrade --apply) first.' );
        return \ezpI18n::tr( 'design/admin/migration', 'The recording could not be changed: %reason', null, array( '%reason' => $error ) );
    }
}

}
?>
