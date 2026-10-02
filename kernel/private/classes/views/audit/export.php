<?php
/**
 * The audit/export view (doc/bc/6.0/audit.md, "Views"): the records of the console's filter as a download, newest
 * first: /(format)/csv (one row per record, the index columns), /(format)/jsonl (the records as written, one per
 * line, so the hashes can be checked) or /(format)/json (one array). Without a format it shows the choice and how
 * many records the filter has. At most [AuditConsoleSettings] MaxExportRecords records; a larger filter is cut and
 * says so (exp:audit export has no limit). Policy audit/read with its Channel limitation. Recorded as
 * system.audit.export with the filter, format, count and the sha256 of what was sent.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Audit
{

class Export extends \Exponential\Runnable\ModuleView
{
    const FORMATS = array( 'csv' => 'text/csv', 'jsonl' => 'application/x-ndjson', 'json' => 'application/json' );

    const CSV_COLUMNS = array( 'time_utc', 'id', 'name', 'channel', 'seq', 'severity', 'result', 'reason', 'login', 'user_id', 'ip',
                               'object_type', 'object_id', 'object', 'target_type', 'target_id', 'request', 'siteaccess', 'engine',
                               'module_view', 'job', 'run', 'parent', 'file' );

    /** Rows read from the index at a time */
    const CHUNK = 500;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !class_exists( 'expAuditConsole' ) || !\expAuditConsole::available() )
            return $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

        $raw = \expAuditConsole::rawFilters( $Params );
        $allowed = \expAuditConsole::allowedChannels();
        if ( isset( $raw['channel'] ) && !\expAuditConsole::channelAllowed( $raw['channel'], $allowed ) )
            return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        $settings = \expAuditIndexSettings::get();
        $filters = \expAuditQuery::normalise( $raw );
        $format = isset( $Params['Format'] ) && isset( self::FORMATS[$Params['Format']] ) ? $Params['Format'] : null;

        \expAuditConsole::refreshIndex();
        $first = \expAuditConsole::page( $filters, $allowed, 0, 1 );
        $total = (int)$first['total'];

        if ( $format === null )
        {
            $tpl = \eZTemplate::factory();
            $tpl->setVariable( 'filters', $raw );
            $tpl->setVariable( 'total', $total );
            $tpl->setVariable( 'max', $settings['maxExport'] );
            $tpl->setVariable( 'console_url', \expAuditConsole::url( 'audit/console', $raw ) );
            $urls = array();
            foreach ( array_keys( self::FORMATS ) as $f )
                $urls[$f] = \expAuditConsole::url( 'audit/export', $raw, array( 'format' => $f ) );
            $tpl->setVariable( 'urls', $urls );
            \expAuditConsole::recordRead( 'audit/export', $raw, $total );
            return \expAuditConsole::result( $tpl->fetch( 'design:audit/export.tpl' ), \ezpI18n::tr( 'design/admin/audit', 'Export' ) );
        }

        $limit = min( $total, $settings['maxExport'] );
        $name = 'audit-' . date( 'Ymd-His' ) . '.' . $format;
        header( 'Content-Type: ' . self::FORMATS[$format] . '; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $name . '"' );
        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'Pragma: no-cache' );
        header( 'X-Content-Type-Options: nosniff' );
        if ( $total > $limit )
            header( 'X-Exp-Audit-Export-Cut: ' . $limit . ' of ' . $total );
        while ( @ob_end_clean() );

        // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
        $hash = hash_init( 'sha256' );
        $out = fopen( 'php://output', 'w' );
        $emit = function ( $s ) use ( $out, $hash ) {
            hash_update( $hash, $s );
            fwrite( $out, $s );
        };
        if ( $format === 'csv' )
            $emit( "\xEF\xBB\xBF" . self::csvLine( self::CSV_COLUMNS ) );
        elseif ( $format === 'json' )
            $emit( "[\n" );
        $count = 0;
        for ( $offset = 0; $offset < $limit; $offset += self::CHUNK )
        {
            $page = \expAuditConsole::page( $filters, $allowed, $offset, min( self::CHUNK, $limit - $offset ) );
            if ( !$page['rows'] )
                break;
            foreach ( $page['rows'] as $row )
            {
                if ( $format === 'csv' )
                {
                    $v = \expAuditConsole::view( $row );
                    $line = array();
                    foreach ( self::CSV_COLUMNS as $c )
                        $line[] = isset( $v[$c] ) ? $v[$c] : '';
                    $emit( self::csvLine( $line ) );
                }
                elseif ( $format === 'jsonl' )
                    $emit( rtrim( (string)$row['record'], "\n" ) . "\n" );
                else
                    $emit( ( $count ? ",\n" : '' ) . rtrim( (string)$row['record'], "\n" ) );
                $count++;
            }
        }
        if ( $format === 'json' )
            $emit( "\n]\n" );
        fclose( $out );

        \expAudit::event( 'system.audit.export', array(
            'object' => array( 'type' => 'view', 'id' => 'audit/export' ),
            'after' => array( 'filters' => $raw ?: null, 'format' => $format, 'count' => $count, 'total' => $total,
                              'sha256' => hash_final( $hash ) ) ) );
        \expAudit::flush();
        \eZExecution::cleanExit();
        return $this->viewResult( null, null );
    }

    /**
     * One CSV line; a value starting with = + - @ gets a leading apostrophe, so a spreadsheet does not run it.
     *
     * @param array $values
     * @return string
     */
    public static function csvLine( array $values )
    {
        $cells = array();
        foreach ( $values as $v )
        {
            $v = (string)$v;
            if ( $v !== '' && strpos( '=+-@', $v[0] ) !== false && !is_numeric( $v ) )
                $v = "'" . $v;
            $cells[] = '"' . str_replace( '"', '""', $v ) . '"';
        }
        return implode( ',', $cells ) . "\r\n";
    }
}

}
