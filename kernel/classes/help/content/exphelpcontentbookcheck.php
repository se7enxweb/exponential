<?php
/**
 * The checks of an EPUB of a help manual (doc/eng-us/specifications/6.0/help-system.md, delivery path 3: "epubcheck +
 * Ace by DAISY"; doc/eng-us/specifications/6.0/help-standards.md, "EPUB Accessibility 1.1"): epubcheck (the W3C's EPUB
 * validator, Java) and Ace by DAISY (the accessibility checker, Node.js) are not shipped with Exponential, but a site
 * that has them names their commands and every e-book is checked with them:
 *
 *   exphelp.ini [ContentSettings]
 *     EpubCheckCommand=java -jar /opt/epubcheck/epubcheck.jar      the EPUB file is added as the last argument
 *     AceCommand=ace                                               --outdir <dir> --force and the file are added
 *     BookChecks=report|required|disabled
 *
 * report (the default): the checks run when a command is set, and their result is printed (export --check) and logged
 * (stored e-books); required: an e-book that fails a check is not stored (and export --check exits 1); disabled: no
 * check runs. A command that is not set is "skipped", never "passed": the dcterms:conformsTo claim of the book is
 * verified only by a check that ran.
 *
 * epubcheck passes with exit code 0. Ace passes when its report.json has no assertion with the outcome "fail" (a
 * violation of EPUB Accessibility or WCAG); its own exit code only says whether it ran.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentBookCheck
{
    const PASS = 'pass';
    const FAIL = 'fail';
    const SKIPPED = 'skipped';
    const ERROR = 'error';

    /** Seconds a check may take */
    const TIMEOUT = 300;

    /** @var array tool => command ('' none) */
    protected $commands;

    /** @var string report, required or disabled */
    protected $mode;

    /**
     * @param array $options epubcheck, ace (commands; default the settings), mode (default BookChecks)
     */
    public function __construct( array $options = array() )
    {
        $this->commands = array(
            'epubcheck' => trim( (string)( array_key_exists( 'epubcheck', $options ) ? $options['epubcheck'] : expHelpContentSchema::setting( 'EpubCheckCommand', '' ) ) ),
            'ace' => trim( (string)( array_key_exists( 'ace', $options ) ? $options['ace'] : expHelpContentSchema::setting( 'AceCommand', '' ) ) ),
        );
        $mode = (string)( isset( $options['mode'] ) ? $options['mode'] : expHelpContentSchema::setting( 'BookChecks', 'report' ) );
        $this->mode = in_array( $mode, array( 'report', 'required', 'disabled' ), true ) ? $mode : 'report';
    }

    /** @return string */
    public function mode()
    {
        return $this->mode;
    }

    /**
     * Checks an EPUB file with every configured tool
     *
     * @param string $file
     * @return array list of array( tool, status (pass|fail|skipped|error), message, findings (int) )
     */
    public function check( $file )
    {
        $results = array();
        foreach ( $this->commands as $tool => $command )
        {
            if ( $this->mode === 'disabled' )
                $results[] = array( 'tool' => $tool, 'status' => self::SKIPPED, 'message' => 'BookChecks=disabled', 'findings' => 0 );
            elseif ( $command === '' )
                $results[] = array( 'tool' => $tool, 'status' => self::SKIPPED,
                                    'message' => ( $tool === 'ace' ? 'AceCommand' : 'EpubCheckCommand' ) . ' is not set', 'findings' => 0 );
            elseif ( !is_file( $file ) )
                $results[] = array( 'tool' => $tool, 'status' => self::ERROR, 'message' => 'no file to check', 'findings' => 0 );
            else
                $results[] = $tool === 'ace' ? $this->runAce( $command, $file ) : $this->runEpubCheck( $command, $file );
        }
        return $results;
    }

    /**
     * Whether the results keep a book from being stored: BookChecks=required and a check failed or could not run
     *
     * @param array $results check()
     * @return bool
     */
    public function refuses( array $results )
    {
        if ( $this->mode !== 'required' )
            return false;
        foreach ( $results as $result )
        {
            if ( in_array( $result['status'], array( self::FAIL, self::ERROR ), true ) )
                return true;
        }
        return false;
    }

    /**
     * One line per result, for the command line and the log
     *
     * @param array $results
     * @return string[]
     */
    public static function lines( array $results )
    {
        $lines = array();
        foreach ( $results as $result )
            $lines[] = sprintf( '%-9s %-7s %s', $result['tool'], strtoupper( $result['status'] ), $result['message'] );
        return $lines;
    }

    protected function runEpubCheck( $command, $file )
    {
        $run = self::run( $command . ' ' . escapeshellarg( $file ) );
        if ( $run === null )
            return array( 'tool' => 'epubcheck', 'status' => self::ERROR, 'message' => 'the command could not be started', 'findings' => 0 );
        $findings = (int)preg_match_all( '/^(ERROR|FATAL)\b/m', $run['output'] );
        if ( $run['timeout'] )
            return array( 'tool' => 'epubcheck', 'status' => self::ERROR, 'message' => 'timed out after ' . self::TIMEOUT . ' s', 'findings' => $findings );
        return array( 'tool' => 'epubcheck', 'status' => $run['code'] === 0 ? self::PASS : self::FAIL,
                      'message' => self::lastLine( $run['output'], $run['code'] === 0 ? 'no errors' : 'exit code ' . $run['code'] ),
                      'findings' => $findings );
    }

    protected function runAce( $command, $file )
    {
        $dir = rtrim( expHelp::cacheDirectory(), '/' ) . '/ebooks/ace-' . getmypid() . '-' . mt_rand();
        eZDir::mkdir( $dir, false, true );
        $run = self::run( $command . ' --outdir ' . escapeshellarg( $dir ) . ' --force ' . escapeshellarg( $file ) );
        $report = $dir . '/report.json';
        $result = null;
        if ( $run === null )
            $result = array( 'tool' => 'ace', 'status' => self::ERROR, 'message' => 'the command could not be started', 'findings' => 0 );
        elseif ( $run['timeout'] )
            $result = array( 'tool' => 'ace', 'status' => self::ERROR, 'message' => 'timed out after ' . self::TIMEOUT . ' s', 'findings' => 0 );
        elseif ( !is_file( $report ) )
            $result = array( 'tool' => 'ace', 'status' => self::ERROR, 'message' => self::lastLine( $run['output'], 'no report.json written' ), 'findings' => 0 );
        else
        {
            $data = json_decode( (string)file_get_contents( $report ), true );
            $fails = is_array( $data ) ? self::aceFailures( $data ) : -1;
            $result = $fails < 0
                ? array( 'tool' => 'ace', 'status' => self::ERROR, 'message' => 'report.json could not be read', 'findings' => 0 )
                : array( 'tool' => 'ace', 'status' => $fails === 0 ? self::PASS : self::FAIL,
                         'message' => $fails === 0 ? 'no violations' : "$fails violations", 'findings' => $fails );
        }
        eZDir::recursiveDelete( $dir );
        return $result;
    }

    /**
     * The violations in an Ace report: the assertions whose outcome is "fail", at any depth (pure, for the tests)
     *
     * @param array $report the decoded report.json
     * @return int
     */
    public static function aceFailures( array $report )
    {
        $count = 0;
        if ( isset( $report['earl:result']['earl:outcome'] ) && $report['earl:result']['earl:outcome'] === 'fail'
             && !isset( $report['assertions'] ) )
            $count++;
        foreach ( $report as $value )
        {
            if ( is_array( $value ) )
                $count += self::aceFailures( $value );
        }
        return $count;
    }

    /**
     * Runs a command with a time limit
     *
     * @param string $command
     * @return array|null array( code, output, timeout ), null when it could not be started
     */
    protected static function run( $command )
    {
        $process = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'redirect', 1 ) ), $pipes );
        if ( !is_resource( $process ) )
            return null;
        fclose( $pipes[0] );
        stream_set_blocking( $pipes[1], false );
        $output = '';
        $start = time();
        $timeout = false;
        while ( true )
        {
            $chunk = fread( $pipes[1], 65536 );
            if ( is_string( $chunk ) && $chunk !== '' )
                $output .= strlen( $output ) < 1048576 ? $chunk : '';
            $status = proc_get_status( $process );
            if ( !$status['running'] )
            {
                $output .= (string)stream_get_contents( $pipes[1] );
                fclose( $pipes[1] );
                proc_close( $process );
                return array( 'code' => (int)$status['exitcode'], 'output' => $output, 'timeout' => false );
            }
            if ( time() - $start > self::TIMEOUT )
            {
                $timeout = true;
                break;
            }
            usleep( 50000 );
        }
        proc_terminate( $process );
        fclose( $pipes[1] );
        proc_close( $process );
        return array( 'code' => -1, 'output' => $output, 'timeout' => $timeout );
    }

    protected static function lastLine( $output, $default )
    {
        $lines = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string)$output ) ), 'strlen' ) );
        return $lines ? mb_substr( end( $lines ), 0, 200 ) : $default;
    }
}

?>
