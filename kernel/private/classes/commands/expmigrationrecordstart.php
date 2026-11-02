<?php
/**
 * @description Start a recording of changes: one user's own changes (a session recording) or every change of the site (--site-wide), kept as the steps of a YAML migration; a dry run unless --apply
 * @alias exp:migration:record:start
 *
 * File containing the exp:migration:record:start command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expmigrationrecordstart extends ExpmigrationrecordBase
{
    protected function name()
    {
        return 'exp:migration:record:start';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Start a recording of changes\n" .
                             "From now until exp:migration:record:stop, what is changed is recorded at the kernel's operation level\n" .
                             "(content published, moved, hidden, removed or translated, classes stored, roles, policies and their\n" .
                             "assignments, sections, users and groups, URL aliases, settings written, workflow triggers), as the\n" .
                             "steps of a YAML migration with remote ids and identifiers instead of numeric ids. A session recording\n" .
                             "keeps the changes of one user (--user, by login; in the administration: the signed-in user), made\n" .
                             "anywhere: the administration, the website sidebar, or a command run as that user. --site-wide opens\n" .
                             "the site-wide window instead: every change of every user and of the console. One session recording\n" .
                             "per user and one site-wide window at a time. The account of a user (login, e-mail, password) and the\n" .
                             "fields of user objects are never recorded. Without --apply it says what it would start.\n" .
                             "\n" .
                             "./console exp:migration:record:start --label=\"news section\" --apply\n" .
                             "./console exp:migration:record:start --user=editor --apply\n" .
                             "./console exp:migration:record:start --site-wide --label=\"release 2.4 setup\" --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[apply][dry-run][format:][site-wide][label:][user:]', '', array(
            'apply' => 'Make the changes (the default is a dry run that lists them and changes nothing)',
            'dry-run' => 'The default, accepted for scripts that pass it: list what would change, change nothing',
            'format' => 'text (default) or json (the machine output exp-migration/1)',
            'site-wide' => 'The site-wide recording: every change of every user and of the console (without it: one user\'s own changes)',
            'label' => 'The label of the caption or of the recording (a caption without one: "manual caption" with the time)',
            'user' => 'The user, by login, whose own changes a session recording keeps (default: [RunSettings] RunAsUser of migration.ini)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $siteWide = !empty( $options['site-wide'] );
        if ( $siteWide && isset( $options['user'] ) && $options['user'] !== false && $options['user'] !== '' )
            $this->usage( '--user names the user of a session recording; a site-wide recording records every user' );
        $label = isset( $options['label'] ) && $options['label'] !== false ? trim( preg_replace( '/\s+/', ' ', (string)$options['label'] ) ) : '';
        $db = $this->database( $format );
        $this->tables( $db, $format );
        $user = $siteWide ? null : $this->recordingUser( $options );
        $kind = $siteWide ? \expMigrationRecording::MODE_SITE : \expMigrationRecording::MODE_SESSION;
        $userId = $user ? (int)$user->attribute( 'contentobject_id' ) : 0;
        $report = array( 'mode' => $apply ? 'record-start' : 'record-start-dry-run', 'kind' => $kind, 'user_id' => $userId,
                         'user' => $user ? (string)$user->attribute( 'login' ) : '', 'label' => $label, 'recording' => null,
                         'errors' => array(), 'warnings' => array() );
        if ( !\expMigrationRecorder::settings()['enabled'] )
        {
            $report['errors'][] = 'recording is switched off: [RecordingSettings] Recording=disabled in migration.ini';
            $this->finish( $report, $format );
        }
        $errors = \expMigrationRecording::startProblems( $kind, $siteWide ? 1 : $userId, $label );
        foreach ( \expMigrationRecording::running( $db ) as $row )
        {
            if ( $row['kind'] === $kind && ( $siteWide || (int)$row['user_id'] === $userId ) )
                $errors[] = ( $siteWide ? 'a site-wide recording is already running: ' : 'this user is already recording: ' ) . $row['recording_id'];
        }
        if ( !$errors && $apply )
        {
            $row = \expMigrationRecording::start( $db, $kind, $userId, $label, $errors );
            $report['recording'] = $row;
        }
        $report['errors'] = $errors;
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $who = $report['kind'] === \expMigrationRecording::MODE_SITE ? 'every change of every user (site-wide)' : 'the changes of ' . $report['user'];
        $lines = array( 'Exponential migrations: ' . ( $report['mode'] === 'record-start' ? 'start a recording' : 'start a recording, dry run (--apply starts it)' ) );
        if ( $report['recording'] )
        {
            $lines[] = 'Recording ' . $report['recording']['recording_id'] . ' started: ' . $who . ( $report['label'] !== '' ? ', "' . $report['label'] . '"' : '' );
            $lines[] = 'Next: make the changes, then ./console exp:migration:record:stop' . ( $report['kind'] === \expMigrationRecording::MODE_SITE ? ' --site-wide' : ' --user=' . $report['user'] ) . ' --apply';
        }
        else if ( !$report['errors'] )
            $lines[] = 'Would record ' . $who . ( $report['label'] !== '' ? ', "' . $report['label'] . '"' : '' );
        return $lines;
    }
}

}
