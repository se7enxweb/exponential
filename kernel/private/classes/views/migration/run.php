<?php
/**
 * The migration/run view: the form to run the pending migrations, and the start of the run. The form lists every
 * pending or failed migration (all chosen; fewer runs only those, as --only), the flags of the caption flow (no
 * caption, no restore, keep changes not committed, no commit, no push, no tag) and, when a file changed after it was
 * applied, accepting the change with a reason (--force --reason). Run (a POST that asks "Verify it's you") starts the
 * run in the background as the signed-in user (expMigrationJob: exp:migration:migrate --apply in a process of its own)
 * and opens its page, migration/log/<run id>, which shows it live. Resume (from the history or a run's page) starts
 * exp:migration:resume for a failed or interrupted run the same way. Policy migration/run.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Run extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $messages = array();
        $problem = '';
        $post = $isPost ? $_POST : array();
        $form = array( 'chosen' => null, 'flags' => array(), 'force' => false, 'reason' => '', 'long' => array_fill_keys( \expMigrationAdmin::LONG_RUN_FIELDS, '' ) );

        try
        {
            $report = \expMigrationStatus::report( array( 'runs' => 0 ) );
        }
        catch ( \Throwable $e )
        {
            $report = array( 'migrations' => array(), 'summary' => array(), 'errors' => array(), 'warnings' => array(), 'lock' => null, 'table' => false );
            $problem = $e->getMessage();
        }
        $pending = array();
        $changed = array();
        foreach ( $report['migrations'] as $m )
        {
            // a kernel upgrade step of the stages code, libraries or finish is run by exp:upgrade, not here (deferred)
            if ( in_array( $m['state'], array( \expMigration::PENDING, \expMigration::FAILED ), true ) && $m['action'] !== 'deferred' )
                $pending[] = $m;
            if ( !empty( $m['changed'] ) )
                $changed[] = $m;
        }
        $pendingIds = array_map( function ( $m ) { return $m['id']; }, $pending );
        $login = (string)\eZUser::currentUser()->attribute( 'login' );

        if ( $isPost && $http->hasPostVariable( 'ResumeButton' ) )
        {
            $resume = trim( (string)$http->postVariable( 'ResumeRunID', '' ) );
            if ( !\expMigrationRecord::validRunId( $resume ) )
                $messages[] = array( 'kind' => 'bad', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Choose the run to resume.' ) );
            else
            {
                $started = \expMigrationJob::start( 'resume', array( 'target' => $resume ), $login );
                if ( $started['ok'] )
                    return $Module->redirectTo( '/migration/log/' . $started['id'] );
                $messages[] = array( 'kind' => 'bad', 'text' => self::refusal( $started ) );
            }
        }
        else if ( $isPost && $http->hasPostVariable( 'RunButton' ) )
        {
            $errors = array();
            $options = \expMigrationAdmin::runOptions( $post, $pendingIds, $errors );
            $form = array( 'chosen' => isset( $post['MigrationOnly'] ) ? array_map( 'strval', (array)$post['MigrationOnly'] ) : ( empty( $post['MigrationChoice'] ) ? $pendingIds : array() ),
                           'flags' => isset( $post['RunFlag'] ) ? array_keys( array_filter( (array)$post['RunFlag'] ) ) : array(),
                           'force' => !empty( $post['ForceChanged'] ), 'reason' => isset( $post['ForceReason'] ) ? (string)$post['ForceReason'] : '',
                           'long' => $form['long'] );
            foreach ( \expMigrationAdmin::LONG_RUN_FIELDS as $name )
                if ( isset( $post['LongRun'][$name] ) && is_scalar( $post['LongRun'][$name] ) )
                    $form['long'][$name] = mb_substr( trim( (string)$post['LongRun'][$name] ), 0, 20 );
            if ( empty( $post['ConfirmRun'] ) )
                $errors[] = 'confirm';
            if ( !$pending && !$changed )
                $errors[] = 'nothing';
            if ( $changed && empty( $options['force'] ) && !in_array( 'reason', $errors, true ) )
                $errors[] = 'changed';
            foreach ( array_unique( array_map( array( __CLASS__, 'formError' ), $errors ) ) as $text )
                $messages[] = array( 'kind' => 'bad', 'text' => $text );
            if ( !$errors )
            {
                $started = \expMigrationJob::start( 'migrate', $options, $login );
                if ( $started['ok'] )
                    return $Module->redirectTo( '/migration/log/' . $started['id'] );
                $messages[] = array( 'kind' => 'bad', 'text' => self::refusal( $started ) );
            }
        }
        if ( $form['chosen'] === null )
            $form['chosen'] = $pendingIds;

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'report', $report );
        $tpl->setVariable( 'pending', \expMigrationAdmin::decorate( $pending ) );
        $tpl->setVariable( 'changed', $changed );
        $tpl->setVariable( 'form', $form );
        $tpl->setVariable( 'flags', \expMigrationAdmin::RUN_FLAGS );
        $tpl->setVariable( 'long_run', \expMigrationAdmin::LONG_RUN_FIELDS );
        $tpl->setVariable( 'long_run_defaults', \expMigrationAdmin::longRunDefaults() );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'problem', $problem );
        $tpl->setVariable( 'job', \expMigrationJob::running() );
        $tpl->setVariable( 'lock_text', !empty( $report['lock'] ) ? \expMigrationLock::describe( $report['lock'] ) : '' );
        $tpl->setVariable( 'login', $login );
        $tpl->setVariable( 'can_run', true );
        $tpl->setVariable( 'can_rollback', \expMigrationAdmin::canDo( 'rollback' ) );
        $tpl->setVariable( 'view', 'run' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:migration/run.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Setup' ) ),
                                 array( 'url' => 'migration/status', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Migrations' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/migration', 'Run' ) ) );
        return $Result;
    }

    /**
     * The message of a problem with the form.
     *
     * @param string $code
     * @return string
     */
    public static function formError( $code )
    {
        switch ( $code )
        {
            case 'confirm':
                return \ezpI18n::tr( 'design/admin/migration', 'Tick "Yes, run these migrations now" first.' );
            case 'nothing':
                return \ezpI18n::tr( 'design/admin/migration', 'No migration is pending.' );
            case 'only':
                return \ezpI18n::tr( 'design/admin/migration', 'Choose at least one migration.' );
            case 'changed':
                return \ezpI18n::tr( 'design/admin/migration', 'A migration changed after it was applied: accept the change with a reason, or put the file back.' );
            case 'reason':
                return \ezpI18n::tr( 'design/admin/migration', 'Give the reason why the changed migrations are accepted.' );
            case 'reason-length':
                return \ezpI18n::tr( 'design/admin/migration', 'The reason is at most 500 characters.' );
            case 'max-migrations':
            case 'max-time':
            case 'throttle':
            case 'nice':
                return \ezpI18n::tr( 'design/admin/migration', 'A long-run field is a whole number from 1 (the pause: seconds from 0 to 60, the priority: 1 to 19), or empty.' );
        }
        return \ezpI18n::tr( 'design/admin/migration', 'The form could not be read.' );
    }

    /**
     * The message of a job that was not started.
     *
     * @param array $started expMigrationJob::start()
     * @return string
     */
    public static function refusal( array $started )
    {
        $detail = array( '%detail' => $started['detail'] );
        switch ( $started['refused'] )
        {
            case \expMigrationJob::REFUSED_BUSY:
                return \ezpI18n::tr( 'design/admin/migration', 'A run started here is still going on (%detail). Wait for it to end.', null, $detail );
            case \expMigrationJob::REFUSED_LOCKED:
                return \ezpI18n::tr( 'design/admin/migration', 'Another run holds the lock of the installation: %detail', null, $detail );
            case \expMigrationJob::REFUSED_USER:
                return \ezpI18n::tr( 'design/admin/migration', 'The run needs a signed-in user that exists.' );
            case \expMigrationJob::REFUSED_PHP:
                return \ezpI18n::tr( 'design/admin/migration', 'No php command line binary was found. Set migration.ini [AdminSettings] PhpCliPath to its full path.' );
            case \expMigrationJob::REFUSED_PROC_OPEN:
                return \ezpI18n::tr( 'design/admin/migration', 'proc_open is disabled, so a run cannot be started here. Run ./console exp:migration:migrate --apply on the server.' );
            case \expMigrationJob::REFUSED_DIRECTORY:
                return \ezpI18n::tr( 'design/admin/migration', 'The folder of the runs cannot be written: %detail', null, $detail );
            case \expMigrationJob::REFUSED_PID:
                return \ezpI18n::tr( 'design/admin/migration', 'The run was started but did not report its process id. Check %detail.', null, $detail );
            case \expMigrationJob::REFUSED_TARGET:
                return \ezpI18n::tr( 'design/admin/migration', 'Name what to take back.' );
        }
        return \ezpI18n::tr( 'design/admin/migration', 'The run could not be started.' );
    }
}

}
?>
