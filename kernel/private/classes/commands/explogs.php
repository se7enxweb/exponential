<?php
/**
 * @description The log viewer on the command line: list the log files, follow one, search them and their rotations, find an error reference and the lines of its request (read only, masked by default)
 * @alias exp:logs
 *
 * File containing the exp:logs command (doc/features/6.0/log-viewer.md, "On the command line"). It uses the same
 * classes and settings (logviewer.ini) as Setup > Log files: the same files (the allow-list of directories), the
 * same masking, the same limits. The command line is the server's operator, so no policy is checked; one
 * system.log.read is recorded per run that reads a file.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Explogs extends \Exponential\Runnable\Command
{
    /** ANSI colors of the levels */
    const COLORS = array( 'error' => "\033[31;1m", 'warning' => "\033[33m", 'notice' => "\033[36m", 'debug' => "\033[90m" );
    const RESET = "\033[0m";

    /** @var bool */
    protected $color = false;

    /** @var bool */
    protected $json = false;

    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "The log viewer on the command line (read only; e-mail and IP addresses and secrets masked unless --unmask)\n" .
                                 "\n" .
                                 "./console exp:logs --list                                  the log files: group, size, time, lines a minute\n" .
                                 "./console exp:logs --tail=var/log/error.log --lines=50     the last 50 entries\n" .
                                 "./console exp:logs --tail=var/log/error.log --follow       follow it (Ctrl-C ends)\n" .
                                 "./console exp:logs --search=timeout --file=var/log/error.log --rotations --level=error,warning\n" .
                                 "./console exp:logs --search='/content/view/\\d+' --regex --scope=kernel --from='2026-10-17 01:00' --to='2026-10-17 02:00'\n" .
                                 "./console exp:logs --reference=ERR-0123456789          where an error page's reference is, and its request\n" .
                                 "./console exp:logs --related=var/log/error.log:123456      the lines of the request of the entry at that offset\n" .
                                 "\n" .
                                 "Exit code: 0 something listed or found, 1 nothing found, 2 error.",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[list][tail:][lines:][follow][interval:][search:][regex][case][file:][scope:][rotations][level:][from:][to:][sa:][uri:][ip:][cursor:][limit:][reference:][related:][unmask][json][color:]', '', array(
            'list'       => 'List the log files the viewer may open, by group, with size, time and lines a minute',
            'tail'       => 'The last entries of a file (its path relative to the root, e.g. var/log/error.log)',
            'lines'      => 'With --tail: how many entries (default 20)',
            'follow'     => 'With --tail: go on printing what the file gains, polling with backoff (Ctrl-C ends)',
            'interval'   => 'With --follow: the shortest poll interval in milliseconds (default [TailSettings] PollInterval)',
            'search'     => 'Search for a text (literal, or a regular expression with --regex)',
            'regex'      => 'With --search: the text is a regular expression (bounded by [SearchSettings] BacktrackLimit and TimeLimit)',
            'case'       => 'With --search: case sensitive',
            'file'       => 'With --search: the file (default: [LogViewerSettings] DefaultFile)',
            'scope'      => 'With --search: file (default), kernel (every kernel log) or all (every file listed)',
            'rotations'  => 'With --search: the rotations (.1 .. .N, .gz) too',
            'level'      => 'Only these levels: error,warning,notice,debug',
            'from'       => 'Only entries at or after this time (e.g. 2026-10-17 01:00)',
            'to'         => 'Only entries at or before this time (a date alone means the whole day)',
            'sa'         => 'Only entries of this siteaccess',
            'uri'        => 'Only entries whose request contains this text',
            'ip'         => 'Only entries whose client address starts with this',
            'cursor'     => 'With --search: go on from where an earlier search stopped (printed as "More: --cursor=...")',
            'limit'      => 'With --search: print at most this many entries',
            'reference'  => 'Find an error reference (ERR-XXXXXXXXXX) in the kernel logs and their rotations, with its request',
            'related'    => 'The lines of the same request as the entry at <file>:<offset>',
            'unmask'     => 'Show e-mail addresses, IP addresses and secrets unmasked (the values of secret settings stay hidden)',
            'json'       => 'One JSON object instead of the text',
            'color'      => 'Color the levels: auto (default: when the output is a terminal), always, never'
        ) );

        $config = \expLogViewerConfig::instance();
        if ( !$config->enabled( 'LogViewerSettings', 'LogViewer' ) )
        {
            $cli->error( 'FAIL: the log viewer is switched off ([LogViewerSettings] LogViewer)' );
            $this->shutdown( 2 );
            return;
        }
        $viewer = new \expLogViewer( $config, null, !empty( $options['unmask'] ), 'cli|' . getmypid() . '|' . microtime( true ) );
        $this->json = !empty( $options['json'] );
        $colorMode = isset( $options['color'] ) && is_string( $options['color'] ) ? strtolower( $options['color'] ) : 'auto';
        $this->color = !$this->json && ( $colorMode === 'always'
                       || ( $colorMode === 'auto' && function_exists( 'posix_isatty' ) && @posix_isatty( STDOUT ) ) );

        try
        {
            if ( !empty( $options['list'] ) )
                $code = $this->listFiles( $viewer );
            else if ( !empty( $options['tail'] ) )
                $code = $this->tail( $viewer, (string)$options['tail'], $options );
            else if ( isset( $options['search'] ) && $options['search'] !== null && $options['search'] !== false
                      || !empty( $options['level'] ) || !empty( $options['from'] ) || !empty( $options['to'] ) || !empty( $options['sa'] )
                      || !empty( $options['uri'] ) || !empty( $options['ip'] ) )
                $code = $this->search( $viewer, $options );
            else if ( !empty( $options['reference'] ) )
                $code = $this->reference( $viewer, (string)$options['reference'] );
            else if ( !empty( $options['related'] ) )
                $code = $this->related( $viewer, (string)$options['related'] );
            else
            {
                $cli->error( 'Nothing to do: give --list, --tail, --search, --reference or --related (--help lists the options)' );
                $code = 2;
            }
        }
        catch ( \Exception $e )
        {
            $cli->error( 'FAIL: ' . $e->getMessage() );
            $code = 2;
        }
        $this->shutdown( $code );
    }

    /** --list */
    protected function listFiles( \expLogViewer $viewer )
    {
        $groups = $viewer->navigator();
        if ( $this->json )
        {
            $this->printJson( array( 'groups' => $groups ) );
            return 0;
        }
        $count = 0;
        foreach ( $groups as $group => $files )
        {
            if ( !$files )
                continue;
            $this->cli()->output( ucfirst( $group ) . ':' );
            foreach ( $files as $f )
            {
                $rate = $f['rate'] === null ? '-' : ( $f['archive'] ? '' : $f['rate'] . '/min' );
                $this->cli()->output( sprintf( '  %-48s %10s  %s  %s%s', $f['id'], self::bytes( $f['size'] ), date( 'Y-m-d H:i:s', $f['mtime'] ),
                                               $rate, $f['readable'] ? '' : '  (not readable)' ) );
                $count++;
            }
        }
        return $count > 0 ? 0 : 1;
    }

    /** --tail [--lines] [--follow] */
    protected function tail( \expLogViewer $viewer, $id, array $options )
    {
        $file = $viewer->locator()->resolve( $id );
        if ( $file === null )
        {
            $this->cli()->error( 'FAIL: not a log file the viewer may open: ' . $id . ' (see --list)' );
            return 2;
        }
        \expLogViewer::audit( 'read', $file, array( 'cli' => true, 'unmasked' => $viewer->unmasked() ) );
        $lines = isset( $options['lines'] ) && is_numeric( $options['lines'] ) ? max( 1, (int)$options['lines'] ) : 20;
        $payload = $viewer->tail( $file['id'], -1 );
        $entries = array_slice( $payload['entries'], -$lines );
        if ( $this->json && empty( $options['follow'] ) )
        {
            $payload['entries'] = $entries;
            $this->printJson( $payload );
            return $entries ? 0 : 1;
        }
        $this->printEntries( $entries );
        if ( empty( $options['follow'] ) )
            return $entries ? 0 : 1;
        if ( !$payload['live'] )
        {
            $this->cli()->warning( 'An archive is not followed.' );
            return 0;
        }

        $client = $viewer->config()->clientSettings();
        $min = isset( $options['interval'] ) && is_numeric( $options['interval'] ) ? max( 100, (int)$options['interval'] ) : $client['poll_interval'];
        $max = max( $min, $client['max_poll_interval'] );
        $interval = $min;
        $next = $payload['next'];
        $ino = $payload['ino'];
        $stop = false;
        if ( function_exists( 'pcntl_async_signals' ) && function_exists( 'pcntl_signal' ) )
        {
            pcntl_async_signals( true );
            pcntl_signal( SIGINT, function () use ( &$stop ) { $stop = true; } );
            pcntl_signal( SIGTERM, function () use ( &$stop ) { $stop = true; } );
        }
        while ( !$stop )
        {
            usleep( $interval * 1000 );
            if ( $stop )
                break;
            $poll = $viewer->tail( $file['id'], $next, $ino );
            if ( $poll === null )
            {
                $this->cli()->warning( 'The file is no longer there; waiting for it.' );
                $interval = $max;
                continue;
            }
            if ( $poll['reset'] )
                $this->cli()->warning( '--- the file was rotated or emptied; reading it from its start ---' );
            if ( $this->json )
                foreach ( $poll['entries'] as $e )
                    $this->printJson( $e, false );
            else
                $this->printEntries( $poll['entries'] );
            $next = $poll['next'];
            $ino = $poll['ino'];
            if ( $poll['more'] )
                $interval = 0;
            else if ( $poll['entries'] )
                $interval = $min;
            else
                $interval = min( $max, max( $min, $interval * 2 ) );
        }
        return 0;
    }

    /** The criteria of the options */
    protected static function criteriaOf( \expLogViewer $viewer, array $options )
    {
        $in = array();
        foreach ( array( 'search' => 'q', 'level' => 'levels', 'from' => 'from', 'to' => 'to', 'sa' => 'siteaccess', 'uri' => 'uri', 'ip' => 'ip' ) as $opt => $field )
            if ( isset( $options[$opt] ) && is_string( $options[$opt] ) )
                $in[$field] = $options[$opt];
        $in['regex'] = !empty( $options['regex'] );
        $in['case'] = !empty( $options['case'] );
        return \expLogSearch::criteria( $in, $viewer->config()->int( 'SearchSettings', 'MaxPatternLength' ) );
    }

    /** --search and the filters */
    protected function search( \expLogViewer $viewer, array $options )
    {
        $criteria = self::criteriaOf( $viewer, $options );
        if ( $criteria['invalid'] )
        {
            foreach ( $criteria['invalid'] as $field => $why )
                $this->cli()->error( 'FAIL: ' . $field . ': ' . $why );
            return 2;
        }
        $id = isset( $options['file'] ) && is_string( $options['file'] ) ? $options['file'] : (string)$viewer->locator()->defaultID();
        $scope = isset( $options['scope'] ) && in_array( $options['scope'], array( 'file', 'kernel', 'all' ), true ) ? $options['scope'] : 'file';
        $files = $viewer->searchFiles( $id, $scope, !empty( $options['rotations'] ) );
        if ( !$files )
        {
            $this->cli()->error( 'FAIL: not a log file the viewer may open: ' . $id . ' (see --list)' );
            return 2;
        }
        $this->auditFiles( $viewer, $files );
        $cursor = isset( $options['cursor'] ) && is_string( $options['cursor'] ) ? $options['cursor'] : '';
        $result = $viewer->search( $files, $criteria, $cursor );
        if ( isset( $options['limit'] ) && is_numeric( $options['limit'] ) )
            $result['entries'] = array_slice( $result['entries'], 0, max( 1, (int)$options['limit'] ) );
        if ( $this->json )
        {
            $this->printJson( $result );
            return $result['error'] !== '' && $result['error'] !== 'regex_limit' ? 2 : ( $result['entries'] ? 0 : 1 );
        }
        if ( $result['error'] === 'regex_limit' )
            $this->cli()->warning( 'The regular expression needed too much backtracking on a line and was stopped ([SearchSettings] BacktrackLimit).' );
        else if ( $result['error'] !== '' )
        {
            $this->cli()->error( 'FAIL: ' . $result['error'] );
            return 2;
        }
        $this->printEntries( $result['entries'], true );
        $this->cli()->output( sprintf( '%d found, %s read in %d ms%s', count( $result['entries'] ), self::bytes( $result['scanned'] ), $result['ms'],
                                       $result['stopped'] !== '' && $result['stopped'] !== 'regex_limit' ? ' (stopped: ' . $result['stopped'] . ')' : '' ) );
        if ( $result['cursor'] !== '' )
            $this->cli()->output( 'More: --cursor=' . $result['cursor'] );
        return $result['entries'] ? 0 : 1;
    }

    /** --reference */
    protected function reference( \expLogViewer $viewer, $reference )
    {
        $result = $viewer->reference( $reference );
        if ( $result['error'] === 'invalid' )
        {
            $this->cli()->error( 'FAIL: not an error reference (ERR- and ten hexadecimal digits): ' . $reference );
            return 2;
        }
        $files = $viewer->searchFiles( '', 'kernel', true );
        $this->auditFiles( $viewer, $files );
        if ( $this->json )
        {
            $this->printJson( $result );
            return $result['entries'] ? 0 : 1;
        }
        if ( !$result['entries'] )
        {
            $this->cli()->output( 'Not found in the kernel logs and their rotations' . ( $result['stopped'] !== '' ? ' (stopped: ' . $result['stopped'] . ')' : '' ) . '.' );
            return 1;
        }
        $this->printEntries( $result['entries'], true );
        if ( $result['related'] !== null )
            $this->printRelated( $result['related'] );
        return 0;
    }

    /** --related=<file>:<offset> */
    protected function related( \expLogViewer $viewer, $spec )
    {
        $pos = strrpos( $spec, ':' );
        if ( $pos === false || !ctype_digit( substr( $spec, $pos + 1 ) ) )
        {
            $this->cli()->error( 'FAIL: --related=<file>:<offset>, e.g. var/log/error.log:123456' );
            return 2;
        }
        $id = substr( $spec, 0, $pos );
        $file = $viewer->locator()->resolve( $id );
        if ( $file === null )
        {
            $this->cli()->error( 'FAIL: not a log file the viewer may open: ' . $id . ' (see --list)' );
            return 2;
        }
        \expLogViewer::audit( 'read', $file, array( 'cli' => true, 'unmasked' => $viewer->unmasked() ) );
        $result = $viewer->related( $id, (int)substr( $spec, $pos + 1 ) );
        if ( $this->json )
        {
            $this->printJson( $result );
            return $result['entries'] ? 0 : 1;
        }
        if ( $result['entry'] === null )
        {
            $this->cli()->output( 'No entry with a time starts at that offset.' );
            return 1;
        }
        $this->printRelated( $result );
        return $result['entries'] ? 0 : 1;
    }

    /** The related lines and the audit window */
    protected function printRelated( array $related )
    {
        if ( $related['window'] === null )
            return;
        $this->cli()->output( sprintf( 'The same request (%s .. %s): %d entries', $related['window']['from'], $related['window']['to'], count( $related['entries'] ) ) );
        $this->printEntries( $related['entries'], true );
        $this->cli()->output( sprintf( 'Audit of the time: ./console exp:audit or audit/console/(from)/%s/(to)/%s', $related['audit']['from'], $related['audit']['to'] ) );
    }

    /** One system.log.read for the files a run reads */
    protected function auditFiles( \expLogViewer $viewer, array $files )
    {
        if ( !$files )
            return;
        $ids = array();
        foreach ( array_slice( $files, 0, 50 ) as $f )
            $ids[] = $f['id'];
        \expLogViewer::audit( 'read', $files[0], array( 'cli' => true, 'unmasked' => $viewer->unmasked(), 'files' => $ids ) );
    }

    /**
     * Prints entries: time, level, source, siteaccess, client, request; the message below, indented.
     *
     * @param array $entries presented entries
     * @param bool $withFile print the file and offset (search results)
     */
    protected function printEntries( array $entries, $withFile = false )
    {
        foreach ( $entries as $e )
        {
            $message = '';
            foreach ( $e['p'] as $part )
                $message .= $part[0];
            $message = rtrim( $message, "\n" );
            if ( !empty( $e['c'] ) )
            {
                $this->cli()->output( '  ' . str_replace( "\n", "\n  ", $message ) );
                continue;
            }
            $level = strtoupper( str_pad( $e['lv'], 7 ) );
            if ( $this->color && isset( self::COLORS[$e['lv']] ) )
                $level = self::COLORS[$e['lv']] . $level . self::RESET;
            $head = array();
            foreach ( array( 'src', 'sa', 'ip', 'u' ) as $k )
                if ( $e[$k] !== '' )
                    $head[] = $k === 'src' ? $e[$k] : '[' . $e[$k] . ']';
            $where = $withFile && isset( $e['f'] ) ? '  (' . $e['f'] . ':' . $e['o'] . ')' : '';
            $this->cli()->output( trim( $e['ts'] . ' ' . $level . ' ' . implode( ' ', $head ) ) . $where );
            $lines = explode( "\n", $message );
            // eZDebug's first line is the label the header already shows
            if ( count( $lines ) > 1 && trim( $lines[0], " :" ) === $e['src'] )
                array_shift( $lines );
            foreach ( $lines as $line )
                if ( $line !== '' )
                    $this->cli()->output( '    ' . $line );
        }
    }

    /** @param mixed $data */
    protected function printJson( $data, $pretty = true )
    {
        $this->cli()->output( json_encode( $data, ( $pretty ? JSON_PRETTY_PRINT : 0 ) | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                                                  | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE ) );
    }

    /** A size for people */
    protected static function bytes( $n )
    {
        $n = (float)$n;
        foreach ( array( 'B', 'KB', 'MB', 'GB' ) as $unit )
        {
            if ( $n < 1024 || $unit === 'GB' )
                return $unit === 'B' ? (int)$n . ' B' : sprintf( '%.1f %s', $n, $unit );
            $n /= 1024;
        }
        return (int)$n . ' B';
    }
}

}
