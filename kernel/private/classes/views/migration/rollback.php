<?php
/**
 * The migration/rollback view (/migration/rollback/<target>): taking migrations back, three ways, as
 * exp:migration:rollback:
 *
 *   a run          its pre caption restored (the parts database, storage, settings; a safety caption of the state
 *                  now first), with a tick to confirm
 *   a migration    its down(), when it has one and is applied, with a tick to confirm
 *   a caption      any earlier caption, for experts: the expert tick and the caption's id typed again (--expert
 *                  --confirm=<id>)
 *
 * Without a target it lists what can be taken back: the runs with a pre caption, the applied migrations that have a
 * down(), the captions. Roll back (a POST that asks "Verify it's you") starts the rollback in the background as the
 * signed-in user (expMigrationJob) and opens its page. Restoring the database also restores the sessions table: the
 * page says that a sign-in may be asked for afterwards. Policy migration/rollback.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Rollback extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $target = isset( $Params['Target'] ) ? trim( rawurldecode( (string)$Params['Target'] ) ) : '';
        if ( $isPost && $http->hasPostVariable( 'RollbackTarget' ) )
            $target = trim( (string)$http->postVariable( 'RollbackTarget' ) );
        $messages = array();
        $problem = '';

        $runs = array();
        $migrations = array();
        $captions = array();
        try
        {
            $db = \eZDB::instance();
            if ( \expMigrationRecord::runTableExists( $db ) === true )
                foreach ( \expMigrationRecord::runs( $db, 50 ) as $row )
                    $runs[$row['run_id']] = \expMigrationStatus::runSummary( $row );
            $context = new \expMigrationContext( $db, false, array( 'no-mirror' => true ) );
            $errors = array();
            foreach ( \expMigrationRunner::migrations( $context->environment(), $errors ) as $migration )
            {
                $state = \expMigration::UNKNOWN;
                try
                {
                    $state = (string)$migration->state( $context );
                }
                catch ( \Throwable $e )
                {
                }
                $migrations[$migration->id()] = array( 'id' => $migration->id(), 'source' => $migration->source(), 'description' => $migration->description(),
                                                       'state' => $state, 'reversible' => (bool)$migration->reversible() );
            }
            foreach ( \expUpgradeCaption::all() as $caption )
                $captions[$caption['id']] = $caption;
        }
        catch ( \Throwable $e )
        {
            $problem = $e->getMessage();
        }

        $kind = \expMigrationAdmin::targetKind( $target, array_keys( $runs ), array_keys( $migrations ), array_map( 'strval', array_keys( $captions ) ) );
        if ( $target !== '' && $kind === '' && $problem === '' )
            $messages[] = array( 'kind' => 'bad', 'text' => \ezpI18n::tr( 'design/admin/migration', '%target is no run, no migration and no caption of this installation.', null, array( '%target' => $target ) ) );

        if ( $isPost && $http->hasPostVariable( 'RollbackButton' ) && $kind !== '' )
        {
            $errors = array();
            $options = \expMigrationAdmin::rollbackOptions( $kind, $target, $_POST, $errors );
            if ( $kind === 'run' && $runs[$target]['caption_pre'] === '' )
                $errors[] = 'no-caption';
            if ( $kind === 'migration' && ( !$migrations[$target]['reversible'] || $migrations[$target]['state'] !== \expMigration::DONE ) )
                $errors[] = 'not-reversible';
            foreach ( $errors as $code )
                $messages[] = array( 'kind' => 'bad', 'text' => self::formError( $code, $target ) );
            if ( !$errors )
            {
                $started = \expMigrationJob::start( 'rollback', $options, (string)\eZUser::currentUser()->attribute( 'login' ) );
                if ( $started['ok'] )
                    return $Module->redirectTo( '/migration/log/' . $started['id'] );
                $messages[] = array( 'kind' => 'bad', 'text' => Run::refusal( $started ) );
            }
        }

        $reversible = array();
        foreach ( $migrations as $m )
            if ( $m['reversible'] && $m['state'] === \expMigration::DONE )
                $reversible[] = $m;
        $withCaption = array();
        foreach ( $runs as $run )
            if ( $run['caption_pre'] !== '' && $run['state'] !== 'running' )
                $withCaption[] = $run;

        $ini = \eZINI::instance( 'migration.ini' );
        $parts = $ini->hasVariable( 'CaptionSettings', 'RestoreParts' ) ? array_values( array_intersect( \expMigrationAdmin::RESTORE_PARTS, (array)$ini->variable( 'CaptionSettings', 'RestoreParts' ) ) ) : \expMigrationAdmin::RESTORE_PARTS;

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'target', $target );
        $tpl->setVariable( 'kind', $kind );
        $tpl->setVariable( 'run', $kind === 'run' ? \expMigrationAdmin::decorate( array( $runs[$target] ) )[0] : null );
        $tpl->setVariable( 'migration', $kind === 'migration' ? $migrations[$target] : null );
        $tpl->setVariable( 'caption', $kind === 'caption' ? $captions[$target] : null );
        $tpl->setVariable( 'runs', \expMigrationAdmin::decorate( $withCaption ) );
        $tpl->setVariable( 'reversible', \expMigrationAdmin::decorate( $reversible ) );
        $tpl->setVariable( 'captions', array_values( $captions ) );
        $tpl->setVariable( 'parts', \expMigrationAdmin::RESTORE_PARTS );
        $tpl->setVariable( 'default_parts', $parts ?: \expMigrationAdmin::RESTORE_PARTS );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'problem', $problem );
        $tpl->setVariable( 'job', \expMigrationJob::running() );
        $tpl->setVariable( 'can_run', \expMigrationAdmin::canDo( 'run' ) );
        $tpl->setVariable( 'can_rollback', true );
        $tpl->setVariable( 'view', 'rollback' );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:migration/rollback.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Setup' ) ),
                                 array( 'url' => 'migration/status', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Migrations' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/migration', 'Rollback' ) ) );
        return $Result;
    }

    /**
     * The message of a problem with the form.
     *
     * @param string $code
     * @param string $target
     * @return string
     */
    public static function formError( $code, $target )
    {
        switch ( $code )
        {
            case 'confirm':
                return \ezpI18n::tr( 'design/admin/migration', 'Tick "Yes, take it back" first.' );
            case 'expert':
                return \ezpI18n::tr( 'design/admin/migration', 'Restoring an earlier caption is for experts: tick "I know what this restores".' );
            case 'typed':
                return \ezpI18n::tr( 'design/admin/migration', 'Type the id of the caption again: %target', null, array( '%target' => $target ) );
            case 'parts':
                return \ezpI18n::tr( 'design/admin/migration', 'Choose at least one part to restore.' );
            case 'no-caption':
                return \ezpI18n::tr( 'design/admin/migration', 'This run has no caption taken before it: take its migrations back one by one, or restore an earlier caption.' );
            case 'not-reversible':
                return \ezpI18n::tr( 'design/admin/migration', 'This migration cannot be taken back by itself: it is not applied, or it has no down().' );
        }
        return \ezpI18n::tr( 'design/admin/migration', 'The form could not be read.' );
    }
}

}
?>
