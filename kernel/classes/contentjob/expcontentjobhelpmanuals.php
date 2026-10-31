<?php
/**
 * File containing the expContentJobHelpManuals class.
 *
 * The content job 'helpmanuals': builds the help's manuals from the Markdown help in the background
 * (doc/eng-us/features/6.0/help-content.md, "Manuals from the Markdown help"), what
 * "./console exp:help:content build-manuals --release" does in one go. A new installation queues it
 * (expKickstarter, "build-manuals --queue"), so the installer does not wait for the 20 to 30 minutes of a first build.
 *
 * - First the help as content is installed (classes, tree, roles: idempotent), then the plan is made
 *   (expHelpManualBuilder) and expHelpManualSync works through it in small batches: [ManualSettings] JobBatchSize
 *   objects per batch, each batch a transaction of its own, then a pause of JobPause milliseconds, so the site's
 *   own writes are never held up for long; the worker runs at the lowest CPU priority (proc_nice).
 * - Safe to run twice: only one helpmanuals job is active at a time (queue() returns the active one), the job holds
 *   the subtree lock of the help root, and every batch is idempotent: a job resumed after a crash (the cronjob part
 *   'contentjobs' resumes it) finds what was made and goes on.
 * - Its progress says "Manuals are being built (n of 52)": the help's manual pages and "exp:help:content report" show
 *   it while the job runs (status()).
 *
 * Parameters: release (bool, default true), languages (locales, default [ManualSettings] Languages[]), manual (one key).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobHelpManuals implements expContentJobType
{
    const TYPE = 'helpmanuals';

    /** @var array job id => array( 'plan' => plan, 'sync' => expHelpManualSync, 'languages' => locales ): kept by the worker between batches */
    protected static $runs = array();

    protected static function tr( $text, array $arguments = array() )
    {
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( 'kernel/exphelp', $text, null, $arguments ) : strtr( $text, $arguments );
    }

    /** @return bool whether content jobs of this type can be made here */
    public static function available()
    {
        return class_exists( 'expContentJob' ) && isset( expContentJob::types()[self::TYPE] );
    }

    /**
     * The active (queued or running) build of the manuals, or null
     *
     * @return expContentJob|null
     */
    public static function activeJob()
    {
        if ( !self::available() )
            return null;
        foreach ( expContentJob::listFor( null, array( expContentJob::STATE_QUEUED, expContentJob::STATE_RUNNING ) ) as $job )
        {
            if ( $job->type() === self::TYPE )
                return $job;
        }
        return null;
    }

    /**
     * Queues the build and starts its worker, unless one is active already (then that one is returned)
     *
     * @param eZUser $user who runs it (the worker runs as this user)
     * @param array $params release, languages, manual
     * @return array array( 'job' => expContentJob, 'queued' => bool: false when an active job was found, 'started' => bool )
     * @throws expContentJobException
     */
    public static function queue( eZUser $user, array $params = array() )
    {
        $active = self::activeJob();
        if ( $active !== null )
            return array( 'job' => $active, 'queued' => false, 'started' => $active->workerAlive() );
        $job = expContentJob::create( self::TYPE, $params + array( 'mode' => 'job' ), $user );
        return array( 'job' => $job, 'queued' => true, 'started' => $job->spawn() );
    }

    /**
     * What the help shows while the manuals are built: null when no build runs
     *
     * @return array|null array( 'manuals' => n done, 'total' => manuals, 'state', 'job_id', 'message' )
     */
    public static function status()
    {
        $job = self::activeJob();
        if ( $job === null )
            return null;
        $checkpoint = (array)$job->get( 'checkpoint' );
        $done = isset( $checkpoint['manuals'] ) ? (int)$checkpoint['manuals'] : 0;
        $total = isset( $checkpoint['manuals_total'] ) ? (int)$checkpoint['manuals_total']
               : count( expHelpManualBuilder::manualsFromSettings()['manuals'] );
        return array( 'manuals' => $done, 'total' => $total, 'state' => $job->state(), 'job_id' => $job->id(),
                      'message' => self::progressMessage( $done, $total ) );
    }

    /** @return string "Manuals are being built (n of 52)" */
    public static function progressMessage( $done, $total )
    {
        return self::tr( 'Manuals are being built (%1 of %2)', array( '%1' => (int)$done, '%2' => (int)$total ) );
    }

    public function validate( array $params, eZUser $user )
    {
        $access = $user->hasAccessTo( 'content', 'create' );
        if ( $access['accessWord'] === 'no' )
            throw new expContentJobException( self::tr( 'You may not create content (content/create).' ) );
        if ( self::activeJob() !== null )
            throw new expContentJobException( self::tr( 'The manuals are already being built.' ) );
        $languages = isset( $params['languages'] ) && is_array( $params['languages'] ) ? $params['languages']
                   : (array)expHelpManualBuilder::setting( 'Languages', array() );
        $manual = isset( $params['manual'] ) ? (string)$params['manual'] : '';
        if ( $manual !== '' && !preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $manual ) )
            throw new expContentJobException( self::tr( 'There is no manual %1.', array( '%1' => $manual ) ) );
        return array( 'release' => !isset( $params['release'] ) || !empty( $params['release'] ),
                      'languages' => array_values( array_filter( array_map( 'strval', $languages ), 'strlen' ) ),
                      'manual' => $manual );
    }

    public function countNodes( array $params )
    {
        // known once the plan is made (prepare())
        return 0;
    }

    public function locks( array $params )
    {
        // the help root, while it is built: no remove or move of a content job below it meanwhile
        $root = class_exists( 'expHelpContentRepository' ) ? expHelpContentRepository::rootNode() : null;
        return $root instanceof eZContentObjectTreeNode ? array( array( 'path' => (string)$root->attribute( 'path_string' ), 'mode' => 'subtree' ) ) : array();
    }

    public function describe( array $params )
    {
        return $params['manual'] !== '' ? self::tr( 'Build the help manual %1 from the Markdown help', array( '%1' => $params['manual'] ) )
                                        : self::tr( 'Build the help manuals from the Markdown help' );
    }

    public function prepare( expContentJob $job )
    {
        // run again after a batch was rolled back (a busy database; expContentJobWorker tries it again): the sync of
        // the previous try knows objects, ids and an open transaction that the rollback took away. It is dropped
        // without a commit and a new one reads what is stored, so nothing is made twice or pointed at what is gone.
        if ( isset( self::$runs[$job->id()] ) )
        {
            self::$runs[$job->id()]['sync']->abandon();
            unset( self::$runs[$job->id()] );
        }
        // the lowest CPU priority: the site comes first
        if ( function_exists( 'proc_nice' ) )
            @proc_nice( 15 );
        $params = $job->params();
        $userId = $job->userID();
        // help as content first (classes, tree, roles; idempotent), as "exp:help:content install" does
        $installer = new expHelpContentInstaller( array( 'dry_run' => false, 'user_id' => $userId ) );
        $installer->install( array( 'classes', 'tree', 'roles' ) );
        foreach ( $installer->changes() as $change )
            $job->appendLog( 'install: ' . $change['action'] . ' ' . $change['what'] );
        expHelpContentRepository::resetInstance();

        $existing = expHelpManualSync::existingLanguages( array_values( array_diff( $params['languages'], array( (string)eZContentObject::defaultLanguage() ) ) ) );
        $translations = array();
        foreach ( $existing['missing'] as $locale )
            $job->appendLog( "$locale: the installation has no such content language; not translated" );
        foreach ( $existing['languages'] as $locale )
        {
            $source = expHelpManualSource::forLocale( $locale );
            if ( $source !== null )
                $translations[$locale] = $source;
        }
        $settings = expHelpManualBuilder::manualsFromSettings();
        $builder = new expHelpManualBuilder( new expHelpManualSource( expHelpDocumentIndex::instance() ), $translations, array(
            'edition' => expHelpContentPackage::release( '' )['version'],
            'license' => (string)expHelpManualBuilder::setting( 'License', '' ),
            'translated_titles' => expHelpManualBuilder::translatedTitlesFromSettings(),
        ) );
        $plan = $builder->plan( $settings['manuals'] );
        foreach ( $builder->notices() as $notice )
            $job->appendLog( 'notice: ' . $notice );
        $sync = new expHelpManualSync( array( 'release' => $params['release'], 'manual' => $params['manual'], 'user_id' => $userId,
                                              'language' => (string)eZContentObject::defaultLanguage() ) );
        self::$runs[$job->id()] = array( 'plan' => $plan, 'sync' => $sync, 'languages' => array_keys( $translations ) );

        $cp =& $job->checkpoint();
        if ( !isset( $cp['cursor'] ) )
            $cp['cursor'] = array( 'phase' => 'objects', 'offset' => 0 );
        $total = $params['manual'] !== '' ? 1 : count( $plan['manuals'] );
        $cp['manuals_total'] = $total;
        $cp['manuals'] = $params['manual'] !== '' ? 0 : expHelpManualSync::manualsDone( $plan, $cp['cursor'] );
        $units = count( $plan['items'] ) * 4 + 1;
        $job->setProgress( array( 'total' => $units, 'phase' => 'manuals', 'message' => self::progressMessage( $cp['manuals'], $total ) ) );
    }

    public function runBatch( expContentJob $job, $batchSize )
    {
        if ( !isset( self::$runs[$job->id()] ) )
            $this->prepare( $job );
        $run = self::$runs[$job->id()];
        $cp =& $job->checkpoint();
        $cursor = (array)$cp['cursor'];
        $count = count( $run['plan']['items'] );
        $before = array_search( $cursor['phase'], expHelpManualSync::PHASES, true ) * $count + (int)$cursor['offset'];
        $size = max( 1, min( (int)$batchSize, (int)expHelpManualBuilder::setting( 'JobBatchSize', 10 ) ) );
        $finished = $run['sync']->step( $run['plan'], $run['languages'], $cursor, $size );
        $cp['cursor'] = $cursor;
        // what could not be done is in the job's log (as the command prints it); the job goes on with the rest
        $changes = $run['sync']->changes();
        $seen = isset( self::$runs[$job->id()]['logged'] ) ? self::$runs[$job->id()]['logged'] : 0;
        foreach ( array_slice( $changes, $seen ) as $change )
        {
            if ( $change['action'] === 'error' )
                $job->appendLog( 'error: ' . $change['what'] );
        }
        self::$runs[$job->id()]['logged'] = count( $changes );
        $phase = array_search( $cursor['phase'], expHelpManualSync::PHASES, true );
        $doneTotal = $finished ? $count * 4 + 1 : $phase * $count + (int)$cursor['offset'];
        if ( $job->params()['manual'] === '' )
            $cp['manuals'] = expHelpManualSync::manualsDone( $run['plan'], $cursor );
        elseif ( $finished )
            $cp['manuals'] = 1;
        return array( 'done' => max( 0, $doneTotal - $before ), 'finished' => $finished, 'done_total' => $doneTotal,
                      'message' => self::progressMessage( $cp['manuals'], $cp['manuals_total'] ) );
    }

    public function afterBatch( expContentJob $job )
    {
        // the site's own writes get their turn between two batches
        $pause = (int)expHelpManualBuilder::setting( 'JobPause', 200 );
        if ( $pause > 0 )
            usleep( $pause * 1000 );
    }

    public function finish( expContentJob $job )
    {
        if ( isset( self::$runs[$job->id()] ) )
        {
            $sync = self::$runs[$job->id()]['sync'];
            $sync->close();
            $job->setResult( 'counts', $sync->counts() );
            $job->setResult( 'manuals', (int)$job->checkpoint()['manuals_total'] );
            unset( self::$runs[$job->id()] );
        }
        expHelpContentProvider::clearCache();
        $job->appendLog( 'the manuals are built' );
    }
}

?>
