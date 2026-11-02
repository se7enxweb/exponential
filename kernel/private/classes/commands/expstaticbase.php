<?php
/**
 * File containing the shared code of the exp:static commands.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

/**
 * What every exp:static command (static publishing) shares: the options, the text and JSON output, the exit codes.
 * The work is in the expStatic* classes (kernel/classes/staticpublish/); these commands only parse, call and print.
 *
 * The names, options, arguments, exit codes and the exp-static/1 output are a permanent contract (contract() and
 * arguments() are the snapshot tests/tests/kernel/classes/staticpublish/expStaticCommandContractTest.php compares
 * against): options and keys may be added, never renamed or removed. The exit codes are exp:upgrade's: 0 done,
 * 1 failed, 2 usage, 3 the database unreachable, 4 internal error.
 */
abstract class ExpstaticBase extends \Exponential\Runnable\Command
{
    /**
     * Every command with its option configuration (permanent: only additions).
     *
     * @return array command => option config
     */
    public static function contract()
    {
        return array(
            'exp:static:targets' => '[format:][export:]',
            'exp:static:build' => '[full][paths:*][nodes:*][dry-run][report:][format:][crawl-url:][max-pages:][keep-staging][login:][run-id:][progress:][only-siteaccess:]',
            'exp:static:diff' => '[format:][verify][check-links][mount:*]',
            'exp:static:serve' => '[port:][host:][base-path:][format:]',
            'exp:static:publish' => '[build][nodes:*][message:][no-push][format:][login:][run-id:][progress:]',
            'exp:static:rollback' => '[apply][message:][no-push][format:][login:][run-id:][progress:]',
            'exp:static:push' => '[format:]',
            'exp:static:keys' => '[generate][replace][format:]',
            'exp:static:status' => '[limit:][format:]',
            'exp:static:github' => '[create-repo][public][private][pages:][domain:][no-https][plan][client-id:][secret:][format:]',
            'exp:static:dns' => '[owner:][format:]',
        );
    }

    /**
     * The arguments of the commands that take one (permanent).
     *
     * @return array command => argument config
     */
    public static function arguments()
    {
        return array(
            'exp:static:build' => '[target]',
            'exp:static:diff' => '[target]',
            'exp:static:serve' => '[target]',
            'exp:static:publish' => '[target]',
            'exp:static:rollback' => '[target][to]',
            'exp:static:push' => '[target]',
            'exp:static:keys' => '[target]',
            'exp:static:status' => '[target]',
            'exp:static:github' => '[action][target]',
            'exp:static:dns' => '[domain]',
        );
    }

    /** @return array option => help text for every option of the family */
    public static function optionHelp()
    {
        return array(
            'format' => 'text (default) or json (the machine output exp-static/1)',
            'export' => 'print these targets (ids separated by commas, or all) as an INI migration and change nothing',
            'full' => 'Crawl the whole site (the default without --paths or --nodes); not with --paths or --nodes',
            'paths' => 'Build only these pages, by their path in the copy (/about-us, /ger/about-us for a siteaccess in a folder; a comma list or repeated); the rest of the last build stays',
            'nodes' => 'Build only the pages of these nodes (node ids; a comma list or repeated), in each siteaccess of the target; the rest of the last build stays',
            'dry-run' => 'Build into the staging folder, report what would change and leave the target folder as it is',
            'report' => 'Also write the report (exp-static/1, JSON) to this file',
            'crawl-url' => 'Fetch the pages from this address instead of the target\'s CrawlURL or the siteaccess\'s SiteURL (http://127.0.0.1:8080)',
            'max-pages' => 'Stop the crawl after this many pages (default the target\'s MaxPages)',
            'keep-staging' => 'Keep the staging folder of the build (its path is in the report) instead of removing it',
            'verify' => 'Compare the folder with its manifest: files missing, changed since the build, or not listed',
            'check-links' => 'Check every link and file of the folder as a browser would, served below each --mount',
            'mount' => 'The path the folder is served at for --check-links (default the target\'s BasePath; a comma list or repeated: /,/repo/)',
            'port' => 'The port of the preview server (default 8090)',
            'host' => 'The address the preview server listens on (default 127.0.0.1, this computer only)',
            'base-path' => 'Serve the folder below this path (default the target\'s BasePath; / for the root)',
            'build' => 'Build the target first (as exp:static:build); nothing is committed when the build does not finish',
            'message' => 'The commit message (default: what was published, or which publish is restored)',
            'no-push' => 'Commit and tag, but do not push (exp:static:push pushes later)',
            'apply' => 'Make the rollback commit; without it the command only lists what would change',
            'generate' => 'Make a new deploy key (ed25519) and store its private half in the secret store',
            'replace' => 'With --generate: replace the target\'s deploy key (the old one stops working)',
            'limit' => 'How many of the last runs to list (default 10)',
            'create-repo' => 'setup: create the repository when it does not exist (empty; the first publish fills it)',
            'public' => 'With --create-repo: a public repository (the default; GitHub Pages on a free plan needs one)',
            'private' => 'With --create-repo: a private repository (GitHub Pages serves it only on a paid plan)',
            'pages' => 'setup: branch or actions instead of the target\'s Delivery, or off to leave GitHub Pages as it is',
            'domain' => 'setup: this custom domain instead of the target\'s CustomDomain (none removes it)',
            'no-https' => 'setup: do not enforce HTTPS for the custom domain',
            'plan' => 'setup: only say what would be done, change nothing',
            'client-id' => 'signin: the client id of the OAuth App (default [StaticPublishSettings] GitHubOAuthClientId)',
            'secret' => 'signin: the name of the secret the token is kept as (Setup > Secrets); the target names it as Credential=token:<secret>',
            'owner' => 'The GitHub account or organization that owns the repository (the CNAME must name <owner>.github.io)',
            'login' => 'Run as this user (the administration starts its jobs so); recorded as who ran it',
            'run-id' => 'The id of the run (date-time-6 hex digits) instead of a new one (the administration passes its job\'s id)',
            'progress' => 'Also append the progress, one JSON object per line, to this file (the administration follows it)',
            'only-siteaccess' => 'Build only this one of the target\'s siteaccesses (a target of several, SiteAccessList[]); the others\' pages of the last build stay',
        );
    }

    /**
     * The publisher of a target with the installation's root and database.
     *
     * @param \expStaticTarget $target
     * @param \eZDBInterface|null $db
     * @param array $options more options of expStaticPublisher
     * @return \expStaticPublisher
     */
    protected function publisher( \expStaticTarget $target, $db, array $options = array() )
    {
        return new \expStaticPublisher( $target, $options + array( 'root' => defined( 'EXP_ROOT_DIR' ) ? EXP_ROOT_DIR : (string)getcwd(), 'db' => $db, 'notify' => true ) );
    }

    /** @var string the file of --progress, '' without it */
    protected $progressFile = '';

    /**
     * The options of a job the administration starts (--login, --run-id, --progress): checked, the user signed in, the
     * progress file kept for the end event. A usage error ends the command.
     *
     * @param array $options
     * @return array by (who runs it, '' for the default), run_id ('' for a new one), progress (an emit callable or null)
     */
    protected function jobOptions( array $options )
    {
        $out = array( 'by' => '', 'run_id' => '', 'progress' => null );
        $login = isset( $options['login'] ) && $options['login'] !== false ? trim( (string)$options['login'] ) : '';
        if ( $login !== '' )
        {
            $user = \eZUser::fetchByName( $login );
            if ( !$user instanceof \eZUser )
                $this->usage( "--login names no user: $login" );
            \eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ), \eZUser::NO_SESSION_REGENERATE );
            $out['by'] = $login;
        }
        $runId = isset( $options['run-id'] ) && $options['run-id'] !== false ? trim( (string)$options['run-id'] ) : '';
        if ( $runId !== '' && !\expStaticJob::validId( $runId ) )
            $this->usage( "--run-id must look like 20261101-120000-a1b2c3, not $runId" );
        $out['run_id'] = $runId;
        $file = isset( $options['progress'] ) && $options['progress'] !== false ? (string)$options['progress'] : '';
        if ( $file !== '' )
        {
            if ( !is_dir( dirname( $file ) ) || is_dir( $file ) )
                $this->usage( "--progress must name a file in a folder that exists, not $file" );
            $this->progressFile = $file;
            $out['progress'] = \expStaticProgress::writer( $file );
        }
        return $out;
    }

    /**
     * Two emit callbacks as one (either may be null).
     *
     * @param callable|null $a
     * @param callable|null $b
     * @return callable|null
     */
    protected static function bothEmit( $a, $b )
    {
        if ( $a === null || $b === null )
            return $a !== null ? $a : $b;
        return function ( $type, $message = '', array $data = array() ) use ( $a, $b )
        {
            call_user_func( $a, $type, $message, $data );
            call_user_func( $b, $type, $message, $data );
        };
    }

    /** Ends with exit 1 when static publishing is switched off. */
    protected function requireEnabled( \expStaticTarget $target, $mode, $format )
    {
        if ( !\expStaticTarget::enabled() )
            $this->finish( array( 'mode' => $mode, 'target' => $target->id(), 'warnings' => array(),
                                  'errors' => array( 'Static publishing is switched off: staticcache.ini [StaticPublishSettings] Enabled=disabled' ) ), $format );
    }

    /**
     * The lines of a publish, rollback or push report. Pure.
     *
     * @param array $report
     * @return string[]
     */
    public static function gitLines( array $report )
    {
        if ( !isset( $report['push_state'], $report['directory'], $report['messages'] ) )
            return array();
        $lines = array();
        if ( !empty( $report['build'] ) )
            $lines = self::buildLines( $report['build'] );
        $lines[] = '';
        $lines[] = sprintf( 'Static publishing: %s of %s, branch %s in %s', $report['mode'], $report['target'], $report['branch'] !== '' ? $report['branch'] : 'gh-pages',
                            $report['directory'] );
        foreach ( $report['messages'] as $message )
            $lines[] = '  ' . $message;
        if ( isset( $report['files'] ) )
            foreach ( array_slice( $report['files'], 0, 50 ) as $file )
                $lines[] = '    ' . $file;
        if ( $report['commit_id'] !== '' )
            $lines[] = sprintf( '  Commit: %s, tag %s', substr( $report['commit_id'], 0, 12 ), $report['tag_name'] !== '' ? $report['tag_name'] : '(none)' );
        if ( $report['mode'] !== 'rollback' || !empty( $report['apply'] ) )
            $lines[] = sprintf( '  Push:   %s (%s, credential %s)', $report['push_state'], $report['remote'] !== '' ? $report['remote'] : 'no remote', $report['credential'] );
        return $lines;
    }

    /** @return string the command's name, e.g. exp:static:build */
    abstract protected function name();

    /**
     * The command's work: parse with begin(), call the expStatic* class, print with finish(). Every command
     * overrides it; this one only starts the script and ends with a usage error.
     */
    public function run()
    {
        $this->begin();
        $this->usage( $this->name() . ' has no work of its own' );
    }

    /**
     * Starts the script: $this->script( array( 'description' => "...", ... ) ) and $this->startup( '<config>',
     * '<arguments>', <help> ), written out literally in each command, so ./console exp:help:examples reads the help
     * text and the options from the code. The option configuration must equal contract() and every help text
     * optionHelp() (expStaticCommandContractTest checks both).
     *
     * @return array the options
     */
    abstract protected function begin();

    /** The script settings every command shares besides its description. */
    const SCRIPT = array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true );

    /**
     * The output format; after an error (not text or json) the command ends with exit 2.
     *
     * @param array $options
     * @return string
     */
    protected function format( array $options )
    {
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( !in_array( $format, array( 'text', 'json' ), true ) )
            $this->usage( "--format must be text or json, not $format" );
        return $format;
    }

    /**
     * A usage error: the message and exit 2.
     *
     * @param string $message
     */
    protected function usage( $message )
    {
        $this->error( 'FAIL: ' . $message );
        $this->shutdown( \expUpgrade::EXIT_USAGE );
    }

    /**
     * The target named by the first argument; a usage error without one, exit 1 when TargetList[] does not name it.
     *
     * @param array $options
     * @param string $format
     * @return \expStaticTarget
     */
    protected function target( array $options, $format )
    {
        $id = isset( $options['arguments'][0] ) ? trim( (string)$options['arguments'][0] ) : '';
        if ( $id === '' )
            $this->usage( 'name the target: ' . $this->name() . ' <target> (exp:static:targets lists them)' );
        $target = \expStaticTarget::get( $id );
        if ( $target === null )
            $this->finish( array( 'mode' => 'target', 'target' => $id, 'warnings' => array(),
                                  'errors' => array( "staticcache.ini [StaticPublishSettings] TargetList[] names no target $id" ) ), $format );
        return $target;
    }

    /**
     * A list option (a comma list or repeated) as its values.
     *
     * @param mixed $value
     * @return string[]
     */
    protected static function listOption( $value )
    {
        $out = array();
        foreach ( (array)$value as $item )
            foreach ( explode( ',', (string)$item ) as $part )
                if ( trim( $part ) !== '' )
                    $out[] = trim( $part );
        return array_values( array_unique( $out ) );
    }

    /**
     * Ends with exit 3 when the database is not reachable.
     *
     * @param string $format
     * @return \eZDBInterface
     */
    protected function database( $format )
    {
        $problem = '';
        try
        {
            $db = \eZDB::instance();
        }
        catch ( \Throwable $e )
        {
            $db = null;
            $problem = ': ' . $e->getMessage();
        }
        if ( !$db || !$db->isConnected() )
            $this->finish( array( 'mode' => 'unreachable', 'errors' => array( 'the database is not reachable' . $problem ), 'warnings' => array(),
                                  'exit' => \expUpgrade::EXIT_UNREACHABLE ), $format );
        return $db;
    }

    /**
     * Prints a report and ends with its exit code.
     *
     * @param array $report
     * @param string $format
     */
    protected function finish( array $report, $format )
    {
        $report = array( 'schema' => \expStaticBuild::REPORT, 'command' => $this->name() ) + $report;
        if ( !isset( $report['exit'] ) )
            $report['exit'] = !empty( $report['errors'] ) ? \expUpgrade::EXIT_FAILED : \expUpgrade::EXIT_OK;
        if ( $this->progressFile !== '' )
        {
            $writer = \expStaticProgress::writer( $this->progressFile );
            $writer( 'end', (string)(int)$report['exit'] );
        }
        if ( $format === 'json' )
            $this->cli()->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        else
            $this->printText( $report );
        $this->shutdown( (int)$report['exit'] );
    }

    /**
     * A report as text: the command's own lines (lines()), then warnings, errors and PASS or FAIL.
     *
     * @param array $report
     */
    protected function printText( array $report )
    {
        foreach ( $this->lines( $report ) as $line )
            $this->output( $line );
        foreach ( isset( $report['warnings'] ) ? $report['warnings'] : array() as $w )
            $this->output( 'WARN ' . $w );
        foreach ( isset( $report['errors'] ) ? $report['errors'] : array() as $e )
            $this->error( 'FAIL: ' . $e );
        $this->output( (int)$report['exit'] === \expUpgrade::EXIT_OK ? 'PASS' : 'FAIL (exit ' . (int)$report['exit'] . ')' );
    }

    /**
     * The command's lines of a report.
     *
     * @param array $report
     * @return string[]
     */
    protected function lines( array $report )
    {
        return array();
    }

    /**
     * The lines of a build report. Pure.
     *
     * @param array $report
     * @return string[]
     */
    public static function buildLines( array $report )
    {
        $lines = array();
        if ( !isset( $report['counts'] ) )
            return $lines;
        $c = $report['counts'];
        $lines[] = '';
        $lines[] = sprintf( 'Static publishing: %s of %s (%s, %s), run %s in %.1fs', $report['mode'] === 'dry-run' ? 'dry run' : 'build',
                            $report['target'], !empty( $report['siteaccesses'] ) ? implode( ', ', (array)$report['siteaccesses'] ) : $report['siteaccess'], $report['scope'], $report['run_id'], $report['duration_ms'] / 1000 );
        $lines[] = sprintf( '  Pages:  %d exported, %d refused, %d skipped, %d excluded', $c['pages'], $c['pages_refused'], $c['pages_skipped'], $c['pages_excluded'] );
        $lines[] = sprintf( '  Files:  %d named by the pages, %d of the storage, %d extras, %d could not be collected', $c['files'], $c['storage'], $c['extras'], $c['files_failed'] )
                 . ( !empty( $c['storage_missing'] ) ? sprintf( ' (%d image file(s) the image table lists are missing from the storage)', $c['storage_missing'] ) : '' );
        $lines[] = sprintf( '  Links:  %d rewritten, %d to the live site, %d left as they are', $c['links_rewritten'], $c['links_live'], $c['links_external'] )
                 . ( !empty( $c['links_removed_outside'] ) || !empty( $c['links_removed_admin'] )
                     ? sprintf( '; links removed: %d outside, %d admin', isset( $c['links_removed_outside'] ) ? $c['links_removed_outside'] : 0,
                                isset( $c['links_removed_admin'] ) ? $c['links_removed_admin'] : 0 ) : '' );
        $lines[] = sprintf( '  Size:   %s in %d files', \expStaticBuild::bytes( $c['bytes'] ), $c['pages'] + $c['files'] + $c['storage'] + $c['extras'] );
        if ( !empty( $c['stripped'] ) )
        {
            $parts = array();
            foreach ( $c['stripped'] as $what => $n )
                $parts[] = "$n $what";
            $lines[] = '  Taken out: ' . implode( ', ', $parts );
        }
        if ( !empty( $report['diff'] ) )
            $lines[] = sprintf( '  Diff:   %d added, %d changed, %d removed, %d unchanged', $report['diff']['added'], $report['diff']['changed'],
                                $report['diff']['removed'], $report['diff']['unchanged'] );
        foreach ( isset( $report['refused'] ) ? $report['refused'] : array() as $r )
            $lines[] = '  refused ' . $r['path'] . ': ' . implode( ', ', $r['reasons'] );
        foreach ( array_slice( isset( $report['files_failed'] ) ? $report['files_failed'] : array(), 0, 20 ) as $f )
            $lines[] = '  missing ' . $f['url'] . ': ' . $f['error'];
        if ( !empty( $report['link_check'] ) )
        {
            $l = $report['link_check'];
            $lines[] = sprintf( '  Link check under %s: %d links in %d files, %d broken', $l['mount'], $l['links'], $l['files'], $l['broken_count'] );
            foreach ( array_slice( $l['broken'], 0, 20 ) as $b )
                $lines[] = '    broken ' . $b['link'] . ' in ' . $b['file'];
        }
        $lines[] = !empty( $report['applied'] ) ? '  Written to ' . $report['directory']
                 : '  The target folder ' . $report['directory'] . ' was not changed';
        return $lines;
    }
}

}
