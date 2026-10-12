<?php
/**
 * File containing the expContentJobAuditExport class.
 *
 * The content job 'auditexport': the export of the audit console (audit/export) in the background, for a filter
 * too large for a download in the browser (doc/bc/6.0/audit.md, "Export"). The worker writes the file next to the
 * job (var/<site>/jobs/content/<id>.export, site user only, no web access) in batches of RECORDS_PER_UNIT records per
 * unit of content.ini [ContentJobSettings] BatchSize (5000 with the default 50, at least one page of 500),
 * with the same bytes the download of audit/export sends for the same filter (expAuditExportStream); the export
 * page lists the user's background exports and offers the file when the job is done
 * (audit/export/(download)/<id>, for the user who asked for it). The file goes with the job when finished jobs are
 * purged (content.ini [ContentJobSettings] KeepDays).
 *
 * Parameters: filters (the console's raw filters), format (csv, jsonl, json); validate() adds channels (the
 * channels the user may read, null for all), total (the records the filter had) and limit (at most
 * [AuditConsoleSettings] MaxBackgroundExportRecords).
 *
 * A batch reads only; the checkpoint keeps the stream's state and the length of the file after the last batch, so
 * a job resumed after a crash cuts what a batch wrote after that and writes it again: no record twice, none lost.
 * The result: file (base name), name (the download's file name), format, count, total, cut, bytes, sha256. Recorded
 * as system.audit.export when the file is complete, with via "background" and the job.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expContentJobAuditExport implements expContentJobType
{
    /** Records written per batch, per unit of BatchSize */
    const RECORDS_PER_UNIT = 100;

    /** The suffix of the export file in the job store */
    const SUFFIX = '.export';

    /** @return bool whether background exports are possible here (the job type is registered) */
    public static function available()
    {
        return class_exists( 'expContentJob' ) && isset( expContentJob::types()['auditexport'] );
    }

    /** @return string */
    protected static function tr( $text, array $arguments = array() )
    {
        return ezpI18n::tr( 'kernel/audit', $text, null, $arguments );
    }

    /**
     * The export file of a job.
     *
     * @param expContentJob|string $job
     * @return string
     */
    public static function file( $job )
    {
        return expContentJobStore::path( is_object( $job ) ? $job->id() : (string)$job, self::SUFFIX );
    }

    public function validate( array $params, eZUser $user )
    {
        $format = isset( $params['format'] ) ? (string)$params['format'] : '';
        if ( !isset( expAuditExportStream::FORMATS[$format] ) )
            throw new expContentJobException( self::tr( 'Unknown export format %1.', array( '%1' => $format ) ) );
        $access = $user->hasAccessTo( 'audit', 'read' );
        if ( $access['accessWord'] === 'no' )
            throw new expContentJobException( self::tr( 'You may not read the audit records (audit/read).' ) );
        $channels = expAuditConsole::allowedChannels( $user );
        $raw = array();
        foreach ( isset( $params['filters'] ) ? (array)$params['filters'] : array() as $k => $v )
            if ( is_string( $k ) && isset( expAuditConsole::FILTERS[$k] ) && is_scalar( $v ) && trim( (string)$v ) !== '' )
                $raw[$k] = trim( (string)$v );
        unset( $raw['offset'], $raw['limit'] );
        if ( isset( $raw['channel'] ) && !expAuditConsole::channelAllowed( $raw['channel'], $channels ) )
            throw new expContentJobException( self::tr( 'You may not read the audit channel %1.', array( '%1' => $raw['channel'] ) ) );
        $total = expAuditExportStream::total( $raw, $channels );
        return array( 'filters' => $raw, 'format' => $format, 'channels' => $channels, 'total' => $total,
                      'limit' => min( $total, (int)expAuditIndexSettings::get()['maxBackgroundExport'] ) );
    }

    public function countNodes( array $params )
    {
        return isset( $params['limit'] ) ? (int)$params['limit'] : 0;
    }

    public function locks( array $params )
    {
        // reads only
        return array();
    }

    public function describe( array $params )
    {
        $names = array( 'csv' => 'CSV', 'jsonl' => 'JSON lines', 'json' => 'JSON' );
        $format = isset( $names[$params['format']] ) ? $names[$params['format']] : $params['format'];
        return isset( $params['filters']['channel'] )
            ? self::tr( 'Export %1 audit events of the channel %2 as %3', array( '%1' => (int)$params['limit'], '%2' => $params['filters']['channel'], '%3' => $format ) )
            : self::tr( 'Export %1 audit events as %2', array( '%1' => (int)$params['limit'], '%2' => $format ) );
    }

    public function prepare( expContentJob $job )
    {
        $cp =& $job->checkpoint();
        $file = self::file( $job );
        // a checkpoint without its file (removed meanwhile): from the start again
        if ( !isset( $cp['stream'] ) || !is_file( $file ) || @filesize( $file ) < (int)$cp['bytes'] )
        {
            $cp['stream'] = null;
            $cp['bytes'] = 0;
        }
    }

    public function runBatch( expContentJob $job, $batchSize )
    {
        $cp =& $job->checkpoint();
        $params = $job->params();
        $stream = $cp['stream']
                ? expAuditExportStream::fromState( $cp['stream'] )
                : new expAuditExportStream( (array)$params['filters'], $params['channels'], (string)$params['format'], (int)$params['limit'] );
        $file = self::file( $job );
        $new = !is_file( $file );
        $fh = @fopen( $file, 'c' );
        if ( !$fh )
            throw new expContentJobException( "The export file $file cannot be written." );
        if ( $new )
        {
            @chmod( $file, eZFile::fileMode( 0660 ) );
            expContentJobStore::fixOwner( $file );
        }
        // what a batch wrote after the last checkpoint (a crash before it was saved) is written again
        ftruncate( $fh, (int)$cp['bytes'] );
        fseek( $fh, 0, SEEK_END );
        $before = $stream->count();
        $finished = $stream->run( function ( $s ) use ( $fh, $file ) {
            if ( fwrite( $fh, $s ) !== strlen( $s ) )
                throw new expContentJobException( "The export file $file could not be written completely (disk full?)." );
        }, max( expAuditExportStream::CHUNK, (int)$batchSize * self::RECORDS_PER_UNIT ) );
        fflush( $fh );
        $bytes = ftell( $fh );
        fclose( $fh );
        $cp['stream'] = $stream->state();
        $cp['bytes'] = $bytes;
        $count = $stream->count();
        return array( 'done' => $count - $before, 'finished' => $finished, 'done_total' => $count,
                      'message' => self::tr( '%1 of %2 audit events', array( '%1' => $count, '%2' => (int)$params['limit'] ) ) );
    }

    public function afterBatch( expContentJob $job )
    {
    }

    public function finish( expContentJob $job )
    {
        $params = $job->params();
        $cp = $job->checkpoint();
        $file = self::file( $job );
        $sha = is_file( $file ) ? hash_file( 'sha256', $file ) : '';
        $count = isset( $cp['stream']['count'] ) ? (int)$cp['stream']['count'] : 0;
        $job->setResult( 'file', basename( $file ) );
        $job->setResult( 'name', expAuditExportStream::fileName( $params['format'] ) );
        $job->setResult( 'format', (string)$params['format'] );
        $job->setResult( 'count', $count );
        $job->setResult( 'total', (int)$params['total'] );
        $job->setResult( 'cut', (int)$params['total'] > (int)$params['limit'] );
        $job->setResult( 'bytes', is_file( $file ) ? (int)filesize( $file ) : 0 );
        $job->setResult( 'sha256', $sha );
        if ( class_exists( 'expAudit' ) )
            expAudit::event( 'system.audit.export', array(
                'object' => array( 'type' => 'view', 'id' => 'audit/export' ),
                'after' => array( 'filters' => $params['filters'] ?: null, 'format' => $params['format'], 'count' => $count,
                                  'total' => (int)$params['total'], 'sha256' => $sha, 'via' => 'background', 'job' => $job->id() ) ) );
    }

    /**
     * The background exports of a user, newest first (for the export page).
     *
     * @param eZUser $user
     * @param int $limit
     * @return expContentJob[]
     */
    public static function jobsOf( eZUser $user, $limit = 5 )
    {
        if ( !self::available() )
            return array();
        $jobs = array();
        foreach ( expContentJob::listFor( $user ) as $job )
            if ( $job->type() === 'auditexport' )
                $jobs[] = $job;
        usort( $jobs, function ( $a, $b ) { return $b->created() <=> $a->created(); } );
        return array_slice( $jobs, 0, max( 1, (int)$limit ) );
    }

    /**
     * Whether a user may download the file of a job: the user who asked for it, still allowed to read what it
     * holds (every channel of a job made with all channels, or the channels it was made with).
     *
     * @param expContentJob $job
     * @param eZUser $user
     * @return bool
     */
    public static function mayDownload( expContentJob $job, eZUser $user )
    {
        if ( $job->type() !== 'auditexport' || $job->userID() !== (int)$user->attribute( 'contentobject_id' ) )
            return false;
        $now = expAuditConsole::allowedChannels( $user );
        if ( $now === null )
            return true;
        $then = isset( $job->params()['channels'] ) ? $job->params()['channels'] : null;
        if ( $then === null )
            return false;
        $filter = isset( $job->params()['filters']['channel'] ) ? $job->params()['filters']['channel'] : null;
        if ( $filter !== null )
            return expAuditConsole::channelAllowed( $filter, $now );
        return !array_diff( (array)$then, $now );
    }
}
