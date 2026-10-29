<?php
/**
 * @description The log viewer on the command line: list the log files, follow one, search them and their rotations, find an error reference and the lines of its request (masked by default); rotate logs and prune their archives (dry run unless --apply)
 * @alias exp:logs
 *
 * File containing the exp:logs command (doc/eng-us/features/6.0/log-viewer.md, "On the command line"). It uses the same
 * classes and settings (logviewer.ini) as Setup > Log files: the same files (the allow-list of directories), the
 * same masking, the same limits. The command line is the server's operator, so no policy is checked; one
 * system.log.read is recorded per run that reads a file. --rotate, --rotate-all, --prune, --compress and --truncate change
 * files (expLogManager, doc/eng-us/features/6.0/log-rotation.md), only with --apply (without it they print the plan), and
 * are recorded as system.log.rotate, system.log.delete and system.log.truncate.
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
                                 "Log management (doc/eng-us/features/6.0/log-rotation.md):\n" .
                                 "./console exp:logs --archives                              each log with its archives and their sizes\n" .
                                 "./console exp:logs --rotate=error,debug --apply            rotate these logs now (keys as in --archives, or ids)\n" .
                                 "./console exp:logs --rotate-all --apply                    rotate every log that is not empty\n" .
                                 "./console exp:logs --prune                                 the archives beyond site.ini [FileSettings] limits\n" .
                                 "./console exp:logs --prune=error --apply                   remove them (all logs, or these)\n" .
                                 "./console exp:logs --compress --apply                      compress the archives from .2 on\n" .
                                 "./console exp:logs --truncate=debug [--in-place] --apply   empty a log (rotated, or in place)\n" .
                                 "--rotate, --rotate-all, --prune, --compress and --truncate change files only with --apply; without it\n" .
                                 "they print the plan. --dry-run means the same as no --apply; --apply together with --dry-run is a usage\n" .
                                 "error (exit 2).\n" .
                                 "\n" .
                                 "Exit code: 0 something listed or found, 1 nothing found, 2 error.",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[list][archives][rotate:][rotate-all][force][prune;][days:][compress;][truncate:][in-place][apply][dry-run][tail:][lines:][follow][interval:][search:][regex][case][file:][scope:][rotations][level:][from:][to:][sa:][uri:][ip:][cursor:][limit:][reference:][related:][unmask][json][color:]', '', array(
            'list'       => 'List the log files the viewer may open, by group, with size, time and lines a minute',
            'archives'   => 'List the logs that can be rotated, with their archives (.1 .. .N, .gz) and sizes, and the limits',
            'rotate'     => 'Rotate these logs now, whatever their size: keys (error,debug,site-storage) or ids (var/log/error.log), comma separated',
            'rotate-all' => 'Rotate every log over its MaxSize (with --force: every log that is not empty)',
            'force'      => 'With --rotate-all: also the logs under their MaxSize',
            'prune'      => 'Remove the archives beyond the limits (Keep, MaxAgeDays): of every log, or of these keys',
            'days'       => 'With --prune: remove archives older than this many days instead of MaxAgeDays',
            'compress'   => 'Compress the plain archives from .2 on: of every log, or of these keys',
            'truncate'   => 'Empty these logs: rotate them (the content stays in .1), or with --in-place empty the file itself',
            'in-place'   => 'With --truncate: empty the file in place, keeping no copy',
            'apply'      => 'With --rotate, --rotate-all, --prune, --compress or --truncate: change the files (the default is a dry run that prints the plan)',
            'dry-run'    => 'The default, kept for scripts that pass it: only print the plan',
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

        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $this->shutdown( 2 );
            return;
        }
        $dryRun = !$apply;

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
            $listOf = function ( $value ) {
                return is_string( $value ) && $value !== '' ? self::names( $value ) : 'all';
            };
            if ( !empty( $options['archives'] ) )
                $code = $this->archives( \expLogManager::instance() );
            else if ( !empty( $options['rotate'] ) )
                $code = $this->manage( \expLogManager::instance(), 'rotate', self::names( $options['rotate'] ), $dryRun, array( 'force' => true ) );
            else if ( !empty( $options['rotate-all'] ) )
                $code = $this->manage( \expLogManager::instance(), 'rotate', 'all', $dryRun, array( 'force' => !empty( $options['force'] ) ) );
            else if ( array_key_exists( 'prune', $options ) && $options['prune'] !== null && $options['prune'] !== false )
                $code = $this->manage( \expLogManager::instance(), 'prune', $listOf( $options['prune'] ), $dryRun,
                                       array( 'days' => isset( $options['days'] ) && $options['days'] !== false && $options['days'] !== null ? (int)$options['days'] : null ) );
            else if ( array_key_exists( 'compress', $options ) && $options['compress'] !== null && $options['compress'] !== false )
                $code = $this->manage( \expLogManager::instance(), 'compress', $listOf( $options['compress'] ), $dryRun );
            else if ( !empty( $options['truncate'] ) )
                $code = $this->manage( \expLogManager::instance(), 'truncate', self::names( $options['truncate'] ), $dryRun,
                                       array( 'archive_first' => empty( $options['in-place'] ) ) );
            else if ( !empty( $options['list'] ) )
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
                $cli->error( 'Nothing to do: give --list, --tail, --search, --reference, --related, --archives, --rotate, --rotate-all, --prune, --compress or --truncate (--help lists the options)' );
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

    /** A comma separated list of names */
    protected static function names( $value )
    {
        return array_values( array_filter( array_map( 'trim', explode( ',', (string)$value ) ), 'strlen' ) );
    }

    /** --archives */
    protected function archives( \expLogManager $manager )
    {
        $logs = $manager->logs();
        if ( $this->json )
        {
            $this->printJson( array( 'logs' => $logs ) );
            return $logs ? 0 : 1;
        }
        $cli = $this->cli();
        $total = 0;
        foreach ( $logs as $key => $log )
        {
            $l = $log['limits'];
            $cli->output( sprintf( '  %-28s %-40s %10s  %d archive(s) %10s  %s  keep %d, max %s%s%s%s', $key, $log['id'], self::bytes( $log['size'] ),
                                   count( $log['archives'] ), self::bytes( $log['archive_bytes'] ),
                                   $log['growth'] === null ? '' : self::bytes( $log['growth'] ) . '/h',
                                   $l['keep'], self::bytes( $l['max_size'] ), $l['compress'] ? ', compressed' : '',
                                   $l['max_age_days'] > 0 ? ', ' . $l['max_age_days'] . ' days' : '',
                                   $log['writable'] ? '' : '  (not writable for the other user: ' . $log['owner'] . ':' . $log['owner_group'] . ')' ) );
            foreach ( $log['archives'] as $archive )
                $cli->output( sprintf( '      %-60s %10s  %s', $archive['name'], self::bytes( $archive['size'] ), date( 'Y-m-d H:i:s', $archive['mtime'] ) ) );
            $total += $log['size'] + $log['archive_bytes'];
        }
        $cli->output( sprintf( '%d log(s), %s with their archives. Limits: logviewer.ini [RotationSettings], else site.ini [FileSettings]', count( $logs ), self::bytes( $total ) ) );
        return $logs ? 0 : 1;
    }

    /**
     * --rotate | --rotate-all | --prune | --compress | --truncate, with --dry-run: a plan of expLogManager, printed or
     * applied. Exit code 0 done, 1 nothing to do, 2 an unknown name or a failed step.
     *
     * @param \expLogManager $manager
     * @param string $op rotate | prune | compress | truncate
     * @param string[]|string $names or 'all'
     * @param bool $dryRun
     * @param array $opts for plan()
     * @return int
     */
    protected function manage( \expLogManager $manager, $op, $names, $dryRun, array $opts = array() )
    {
        $cli = $this->cli();
        $plan = $manager->plan( $names, $op, $opts );
        $result = null;
        if ( !$dryRun )
            $result = $manager->apply( $plan );
        $steps = $dryRun ? $plan['steps'] : $result['steps'];
        $code = $plan['unknown'] ? 2 : ( $steps ? 0 : 1 );
        $bytes = 0;
        foreach ( $steps as $step )
        {
            $bytes += (int)$step['bytes'];
            if ( !$dryRun && !$step['done'] && !in_array( $step['error'], array( 'missing', 'moved', 'skipped' ), true ) )
                $code = 2;
            if ( !$dryRun && $step['done'] )
            {
                $action = $step['op'] === 'delete' ? 'delete' : ( $step['op'] === 'truncate' ? 'truncate' : 'rotate' );
                if ( $step['op'] !== 'compress' )
                    $this->audit( $action, $step['key'], (int)$step['bytes'], array( 'step' => $step['op'], 'file' => $step['from'], 'to' => $step['to'] ) );
            }
        }
        if ( $this->json )
        {
            $this->printJson( array( 'op' => $op, 'dry_run' => $dryRun, 'steps' => $steps, 'unknown' => $plan['unknown'], 'bytes' => $bytes ) );
            return $code;
        }
        foreach ( $steps as $step )
        {
            $what = sprintf( '%-8s %-28s %s%s  %s', $step['op'], $step['key'], $step['from'], $step['to'] !== '' && $step['to'] !== $step['from'] ? ' -> ' . $step['to'] : '',
                             self::bytes( $step['bytes'] ) . ( isset( $step['reason'] ) ? ' (' . $step['reason'] . ')' : '' ) );
            if ( $dryRun )
                $cli->output( 'would ' . $what );
            else if ( $step['done'] )
                $cli->output( 'PASS ' . $what );
            else
                $cli->error( ( in_array( $step['error'], array( 'missing', 'moved', 'skipped' ), true ) ? 'SKIP ' : 'FAIL ' ) . $what . ': ' . $step['error'] );
        }
        foreach ( $plan['unknown'] as $name )
            $cli->error( 'FAIL: not a log that can be managed: ' . $name . ' (see --archives)' );
        $cli->output( $steps ? sprintf( '%d step(s), %s%s', count( $steps ), self::bytes( $bytes ), $dryRun ? ' (dry run: nothing changed)' : '' )
                             : 'Nothing to ' . $op );
        if ( $dryRun && $code !== 2 )
            $cli->output( 'PASS: ' . count( $steps ) . " $op step(s) planned (dry run, nothing changed; --apply writes)" );
        return $code;
    }

    /** Records a change to the logs in the audit trail (system.log.rotate | system.log.delete), as the log viewer does */
    protected function audit( $action, $id, $size, array $after )
    {
        try
        {
            \expLogViewer::audit( $action, array( 'id' => (string)$id, 'size' => (int)$size ), $after + array( 'source' => 'exp:logs' ) );
        }
        catch ( \Throwable $e )
        {
            // the change is done; a failing audit trail must not turn it into a failure
        }
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
