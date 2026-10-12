<?php
/**
 * The audit/export view (doc/bc/6.0/audit.md, "Views"): the records of the console's filter as a download, newest
 * first: /(format)/csv (one row per record, the index columns; with (channel) also the columns the channel's source
 * declares, expAuditSource::exportColumns()), /(format)/jsonl (the records as written, one per
 * line, so the hashes can be checked) or /(format)/json (one array). Without a format it shows the choice, how
 * many records the filter has, and the user's background exports. At most [AuditConsoleSettings] MaxExportRecords
 * records; a larger filter is cut and says so. Policy audit/read with its Channel limitation. Recorded as
 * system.audit.export with the filter, format, count and the sha256 of what was sent.
 *
 * The download is streamed (#393): sent while it is read (expAuditExportStream), in pieces of FLUSH_BYTES, the
 * session closed first so the user's other pages do not wait for it; X-Accel-Buffering: no makes Exponential
 * Velocity (and a proxy) send it as it is written instead of collecting the whole response first.
 *
 * In the background (POST BackgroundExportButton with BackgroundFormat): the content job 'auditexport'
 * (expContentJobAuditExport) writes the file, up to [AuditConsoleSettings] MaxBackgroundExportRecords records;
 * its progress is on content/job/<id>, and audit/export/(download)/<id> sends the file to the user who asked for
 * it once the job is done.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Audit
{

class Export extends \Exponential\Runnable\ModuleView
{
    const FORMATS = \expAuditExportStream::FORMATS;

    const CSV_COLUMNS = \expAuditExportStream::CSV_COLUMNS;

    /** Rows read from the index at a time */
    const CHUNK = \expAuditExportStream::CHUNK;

    /** Bytes collected before they are sent */
    const FLUSH_BYTES = 65536;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !class_exists( 'expAuditConsole' ) || !\expAuditConsole::available() || !class_exists( 'expAuditExportStream' ) )
            return $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

        if ( isset( $Params['Download'] ) && is_string( $Params['Download'] ) && $Params['Download'] !== '' )
            return $this->download( $Module, $Params['Download'] );

        $raw = \expAuditConsole::rawFilters( $Params );
        $allowed = \expAuditConsole::allowedChannels();
        if ( isset( $raw['channel'] ) && !\expAuditConsole::channelAllowed( $raw['channel'], $allowed ) )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        $settings = \expAuditIndexSettings::get();
        $format = isset( $Params['Format'] ) && isset( self::FORMATS[$Params['Format']] ) ? $Params['Format'] : null;

        $http = \eZHTTPTool::instance();
        $error = '';
        if ( $format === null && $http->hasPostVariable( 'BackgroundExportButton' ) )
        {
            $started = $this->startBackground( $Module, $raw, (string)$http->postVariable( 'BackgroundFormat' ), $error );
            if ( $started )
                return $started;
        }

        \expAuditConsole::refreshIndex();
        $total = \expAuditExportStream::total( $raw, $allowed );

        if ( $format === null )
        {
            $tpl = \eZTemplate::factory();
            $tpl->setVariable( 'filters', $raw );
            $tpl->setVariable( 'total', $total );
            $tpl->setVariable( 'max', $settings['maxExport'] );
            $tpl->setVariable( 'max_background', $settings['maxBackgroundExport'] );
            $tpl->setVariable( 'console_url', \expAuditConsole::url( 'audit/console', $raw ) );
            $urls = array();
            foreach ( array_keys( self::FORMATS ) as $f )
                $urls[$f] = \expAuditConsole::url( 'audit/export', $raw, array( 'format' => $f ) );
            $tpl->setVariable( 'urls', $urls );
            $tpl->setVariable( 'export_url', \expAuditConsole::url( 'audit/export', $raw ) );
            $tpl->setVariable( 'background', class_exists( 'expContentJobAuditExport' ) && \expContentJobAuditExport::available() );
            $tpl->setVariable( 'background_jobs', self::backgroundJobs() );
            $tpl->setVariable( 'background_error', $error );
            $tpl->setVariable( 'audit_sources', \expAuditConsole::sourceLinks( 'audit/export', $raw, $allowed ) );
            $owner = isset( $raw['channel'] ) ? \expAuditSourceRegistry::forChannel( $raw['channel'] ) : null;
            $tpl->setVariable( 'source_columns', array( 'label' => $owner ? \expAuditSourceRegistry::status()[$owner[0]]['label'] : '',
                                                        'columns' => isset( $raw['channel'] ) ? array_keys( \expAuditSourceRegistry::exportColumns( $raw['channel'], self::CSV_COLUMNS ) ) : array() ) );
            \expAuditConsole::recordRead( 'audit/export', $raw, $total );
            return \expAuditConsole::result( $tpl->fetch( 'design:audit/export.tpl' ), \ezpI18n::tr( 'design/admin/audit', 'Export' ) );
        }

        $limit = min( $total, $settings['maxExport'] );
        $stream = new \expAuditExportStream( $raw, $allowed, $format, $limit );
        self::sendHeaders( $format, \expAuditExportStream::fileName( $format ), $total > $limit ? $limit . ' of ' . $total : null );

        // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
        $hash = hash_init( 'sha256' );
        $emit = self::emitter( $hash );
        $stream->run( $emit );
        $emit( null );

        \expAudit::event( 'system.audit.export', array(
            'object' => array( 'type' => 'view', 'id' => 'audit/export' ),
            'after' => array( 'filters' => $raw ?: null, 'format' => $format, 'count' => $stream->count(), 'total' => $total,
                              'sha256' => hash_final( $hash ) ) ) );
        \expAudit::flush();
        \eZExecution::cleanExit();
        return $this->viewResult( null, null );
    }

    /**
     * The headers of a download, then the session closed and the output buffers ended, so what follows is sent as
     * it is written.
     *
     * @param string $format
     * @param string $name the file name
     * @param string|null $cut "100000 of 250000" when the export is cut
     */
    protected static function sendHeaders( $format, $name, $cut = null )
    {
        header( 'Content-Type: ' . self::FORMATS[$format] . '; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $name . '"' );
        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'Pragma: no-cache' );
        header( 'X-Content-Type-Options: nosniff' );
        // sent as it is written: Exponential Velocity streams a response with this header, and a proxy does not hold it
        header( 'X-Accel-Buffering: no' );
        if ( $cut !== null )
            header( 'X-Exp-Audit-Export-Cut: ' . $cut );
        // the user's other pages do not wait for the download: the session file is not held while it runs
        if ( class_exists( 'eZSession' ) )
            \eZSession::stop();
        while ( @ob_end_clean() );
    }

    /**
     * A function that sends what it is given in pieces of FLUSH_BYTES (the first piece at once, so the download
     * starts), adding it to $hash; null sends the rest.
     *
     * @param \HashContext|null $hash
     * @return \Closure
     */
    protected static function emitter( $hash = null )
    {
        $buffer = '';
        $sent = false;
        return function ( $s ) use ( &$buffer, &$sent, $hash ) {
            if ( $s !== null )
            {
                if ( $hash )
                    hash_update( $hash, $s );
                $buffer .= $s;
                if ( $sent && strlen( $buffer ) < self::FLUSH_BYTES )
                    return;
            }
            if ( $buffer !== '' || !$sent )
            {
                echo $buffer;
                $buffer = '';
                flush();
                $sent = true;
            }
        };
    }

    /**
     * Starts a background export of the filter, and sends the user to its progress page.
     *
     * @param \eZModule $Module
     * @param array $raw
     * @param string $format
     * @param string &$error why it was refused
     * @return mixed the redirect, or null when refused
     */
    protected function startBackground( $Module, array $raw, $format, &$error )
    {
        if ( !class_exists( 'expContentJobAuditExport' ) || !\expContentJobAuditExport::available() )
        {
            $error = \ezpI18n::tr( 'design/admin/audit', 'Background exports are not available here.' );
            return null;
        }
        try
        {
            $job = \expContentJob::create( 'auditexport', array( 'filters' => $raw, 'format' => $format, 'mode' => 'job' ), \eZUser::currentUser() );
        }
        catch ( \expContentJobException $e )
        {
            $error = $e->getMessage();
            return null;
        }
        $job->set( 'gui_return_url', \expAuditConsole::url( 'audit/export', $raw ) );
        $job->save();
        if ( class_exists( 'Exponential\\View\\Kernel\\Content\\Job' ) )
            \Exponential\View\Kernel\Content\Job::spawnWithoutSession( $job );
        else
            $job->spawn();
        $Module->redirectTo( '/content/job/' . $job->id() );
        // not empty: the module's redirect is what is sent
        return array( 'content' => '', 'path' => array() );
    }

    /** @return array[] the current user's background exports, newest first, for the page */
    protected static function backgroundJobs()
    {
        if ( !class_exists( 'expContentJobAuditExport' ) || !\expContentJobAuditExport::available() )
            return array();
        $out = array();
        foreach ( \expContentJobAuditExport::jobsOf( \eZUser::currentUser(), 5 ) as $job )
        {
            $p = $job->progress();
            $r = $job->result();
            $params = $job->params();
            $out[] = array( 'id' => $job->id(), 'state' => $job->state(), 'description' => $job->description(),
                            'created' => $job->created(), 'percent' => $p['percent'], 'done' => $p['done'], 'total' => $p['total'],
                            'format' => isset( $params['format'] ) ? $params['format'] : '',
                            'count' => isset( $r['count'] ) ? (int)$r['count'] : 0, 'bytes' => isset( $r['bytes'] ) ? (int)$r['bytes'] : 0,
                            'cut' => !empty( $r['cut'] ), 'job_url' => 'content/job/' . $job->id(),
                            'download_url' => $job->state() === \expContentJob::STATE_DONE ? 'audit/export/(download)/' . $job->id() : '' );
        }
        return $out;
    }

    /**
     * Sends the file of a background export to the user who asked for it.
     *
     * @param \eZModule $Module
     * @param string $id the job
     * @return mixed
     */
    protected function download( $Module, $id )
    {
        if ( !class_exists( 'expContentJobAuditExport' ) || !\expContentJobStore::validID( $id ) )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $job = \expContentJob::fetch( $id );
        if ( !$job || $job->type() !== 'auditexport' )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        if ( !\expContentJobAuditExport::mayDownload( $job, \eZUser::currentUser() ) )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        if ( $job->state() !== \expContentJob::STATE_DONE )
        {
            $Module->redirectTo( '/content/job/' . $job->id() );
            return array( 'content' => '', 'path' => array() );
        }
        $file = \expContentJobAuditExport::file( $job );
        $fh = is_file( $file ) ? @fopen( $file, 'rb' ) : false;
        if ( !$fh )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $params = $job->params();
        $result = $job->result();
        $format = isset( self::FORMATS[$params['format']] ) ? $params['format'] : 'csv';
        self::sendHeaders( $format, isset( $result['name'] ) ? (string)$result['name'] : \expAuditExportStream::fileName( $format, $job->finished() ),
                           !empty( $result['cut'] ) ? (int)$result['count'] . ' of ' . (int)$result['total'] : null );
        // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
        $emit = self::emitter();
        while ( !feof( $fh ) )
        {
            $s = fread( $fh, self::FLUSH_BYTES );
            if ( $s === false || $s === '' )
                break;
            $emit( $s );
        }
        fclose( $fh );
        $emit( null );
        \expAuditConsole::recordRead( 'audit/export', isset( $params['filters'] ) ? (array)$params['filters'] : array(),
                                      isset( $result['count'] ) ? (int)$result['count'] : null,
                                      array( 'format' => $format, 'job' => $job->id(), 'sha256' => isset( $result['sha256'] ) ? $result['sha256'] : null ) );
        \expAudit::flush();
        \eZExecution::cleanExit();
        return $this->viewResult( null, null );
    }

    /**
     * One CSV line (expAuditExportStream::csvLine()).
     *
     * @param array $values
     * @return string
     */
    public static function csvLine( array $values )
    {
        return \expAuditExportStream::csvLine( $values );
    }
}

}
