<?php
/**
 * @description Count the page views of the web server's access logs into the view counter (ezview_counter), for one siteaccess or several
 * @alias exp:viewcount:update
 *
 * File containing the exp:viewcount:update command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

/**
 * The view counter on the command line: the work of the cronjob updateviewcount.php (expViewCountUpdater), with
 * a dry run, a choice of siteaccesses, the maintenance of the resume state and a JSON report.
 * Guide: doc/eng-us/guides/view-count-statistics.md
 */
class Expviewcountupdate extends \Exponential\Runnable\Command
{
    /** Exit code of a usage error. */
    const EXIT_USAGE = 2;

    public function run()
    {
        $this->script( array(
            'description' => "Count the page views of the web server's access logs into the view counter (ezview_counter)\n" .
                             "Reads each siteaccess's access log (logfile.ini [AccessLogFileSettings]) from where the last run\n" .
                             "stopped, keeps the page views (logfile.ini [ViewCountSettings]) and adds them to the nodes they\n" .
                             "showed. Several siteaccesses sharing one log are told apart by the kernel's siteaccess matching.\n" .
                             "\n" .
                             "./console exp:viewcount:update --dry-run --top=20          what a run would count, nothing written\n" .
                             "./console exp:viewcount:update                              count (as the cronjob updateviewcount.php)\n" .
                             "./console exp:viewcount:update --all-siteaccesses           every siteaccess with a log, not the administrations\n" .
                             "./console exp:viewcount:update --siteaccess-list=site,bold  these siteaccesses\n" .
                             "./console exp:viewcount:update --dry-run --from-start       the whole log again, as a preview only\n" .
                             "./console exp:viewcount:update --skip-to-end                start counting from now: mark the logs as read\n" .
                             "./console exp:viewcount:update --reset-state                forget where the runs stopped (the next run reads\n" .
                             "                                                            the logs from their first line)\n" .
                             "./console exp:viewcount:update --dry-run --format=json      the report as JSON (exp-viewcount/1)\n" .
                             "./console exp:viewcount:update --all-siteaccesses --dry-run --log-file=/var/log/httpd/access_log.1 \\\n" .
                             "      --state-dir=var/tmp/viewcount-trial                    a trial on another log, with a state of its own\n" .
                             "\n" .
                             "Exit code: 0 fine, 1 an error (a log missing, the database refused the counts), 2 usage error,\n" .
                             "3 another run held the lock of a log.",
            'use-session' => false,
            'use-modules' => false,
            'use-extensions' => true,
        ) );
        $options = $this->startup( '[dry-run][all-siteaccesses][siteaccess-list:][log-file:][state-dir:][from-start][skip-to-end][reset-state][top:][format:]', '', array(
            'dry-run' => 'Read and report, write neither the counts nor the resume state',
            'all-siteaccesses' => 'Every siteaccess of AvailableSiteAccessList with an access log, except the administrations',
            'siteaccess-list' => 'These siteaccesses, comma separated (default: logfile.ini [ViewCountSettings] SiteAccessList[], else the siteaccess of -s)',
            'log-file' => 'Read this access log for every counted siteaccess, in place of logfile.ini [AccessLogFileSettings]',
            'state-dir' => 'Keep the resume state here, in place of logfile.ini [ViewCountSettings] StateDir',
            'from-start' => 'With --dry-run: read the logs from their first line, ignoring the resume state',
            'skip-to-end' => 'Count nothing; set the resume state to the end of the logs, so the next run counts only newer lines',
            'reset-state' => 'Count nothing; remove the resume state of the siteaccesses, so the next run reads the logs from their first line',
            'top' => 'List the most viewed nodes of the run (default 0, none)',
            'format' => 'text (default) or json',
        ) );
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( !in_array( $format, array( 'text', 'json' ), true ) )
            $this->usage( "--format must be text or json, not $format" );
        $top = isset( $options['top'] ) && $options['top'] !== false && $options['top'] !== null ? (string)$options['top'] : '0';
        if ( !ctype_digit( $top ) )
            $this->usage( "--top must be a number, not $top" );
        $actions = array_filter( array( !empty( $options['skip-to-end'] ), !empty( $options['reset-state'] ), !empty( $options['from-start'] ) ) );
        if ( count( $actions ) > 1 )
            $this->usage( '--from-start, --skip-to-end and --reset-state exclude each other' );
        if ( !empty( $options['from-start'] ) && empty( $options['dry-run'] ) )
            $this->usage( '--from-start reads lines that may have been counted already: use it with --dry-run' );
        $list = null;
        if ( isset( $options['siteaccess-list'] ) && $options['siteaccess-list'] )
        {
            $list = array();
            foreach ( explode( ',', (string)$options['siteaccess-list'] ) as $name )
                if ( trim( $name ) !== '' )
                    $list[] = trim( $name );
            if ( !$list )
                $this->usage( '--siteaccess-list names no siteaccess' );
        }
        if ( $list !== null && !empty( $options['all-siteaccesses'] ) )
            $this->usage( '--siteaccess-list and --all-siteaccesses exclude each other' );

        \eZDB::instance()->setIsSQLOutputEnabled( false );
        $config = \expViewCountConfiguration::fromINI( $list, !empty( $options['all-siteaccesses'] ), array(
            'log_file' => isset( $options['log-file'] ) && $options['log-file'] ? (string)$options['log-file'] : '',
            'state_dir' => isset( $options['state-dir'] ) && $options['state-dir'] ? (string)$options['state-dir'] : '',
        ) );
        $quiet = $this->isQuiet() || $format === 'json';
        $updater = new \expViewCountUpdater( $config, array(
            'dry_run' => !empty( $options['dry-run'] ),
            'from_start' => !empty( $options['from-start'] ),
            'skip_to_end' => !empty( $options['skip-to-end'] ),
            'reset_state' => !empty( $options['reset-state'] ),
            'top' => (int)$top,
            'progress' => $quiet ? null : function ( $message ) { $this->output( $message ); },
        ) );
        $report = $updater->run();
        if ( $format === 'json' )
            $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        else
        {
            foreach ( \expViewCountUpdater::lines( $report ) as $line )
            {
                if ( strpos( $line, 'Error: ' ) === 0 )
                    $this->error( $line );
                else
                    $this->output( $line );
            }
        }
        $this->shutdown( (int)$report['exit_code'] );
    }

    /**
     * A usage error: the message and exit 2.
     *
     * @param string $message
     */
    protected function usage( $message )
    {
        $this->error( 'Error: ' . $message );
        $this->shutdown( self::EXIT_USAGE );
    }
}

}
