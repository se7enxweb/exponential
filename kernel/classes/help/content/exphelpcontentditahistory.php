<?php
/**
 * The change history of the help in git (doc/eng-us/specifications/6.0/help-system.md, delivery path 4: "the DITA export
 * runs regularly into its own git repository. The content lives in the CMS, the change history still ends up in git").
 *
 * exphelp.ini [ContentSettings] DitaHistoryDirectory names a directory outside the web root; the cronjob part
 * exphelp (cronjobs/exphelpdita.php) writes the DITA export of every manual into it (expHelpContentDita::write() writes
 * only the files that changed and removes those that are gone) and commits the result: one commit per run that
 * changed something, none otherwise. The directory becomes a git repository on the first run (git init). A remote is
 * the site's business: the job never pushes. "./console exp:help:content dita --output=<dir> --commit" does the same
 * by hand.
 *
 * git is run without a shell (an argument list), with the author of DitaHistoryAuthor ("Name <mail>") when it is set,
 * else the repository's own configuration.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentDitaHistory
{
    /** @var string */
    protected $dir;

    /** @var string the git binary */
    protected $git;

    /**
     * @param string $dir
     * @param string $git
     */
    public function __construct( $dir, $git = 'git' )
    {
        $this->dir = rtrim( (string)$dir, '/' );
        $this->git = (string)$git !== '' ? (string)$git : 'git';
    }

    /** @return string the configured directory ('' none) */
    public static function directory()
    {
        return trim( (string)expHelpContentSchema::setting( 'DitaHistoryDirectory', '' ) );
    }

    /**
     * The author of the commits: array( name, email ) from "Name <mail>", or null (the repository's configuration)
     *
     * @param string|null $setting default DitaHistoryAuthor
     * @return array|null
     */
    public static function author( $setting = null )
    {
        $setting = trim( (string)( $setting === null ? expHelpContentSchema::setting( 'DitaHistoryAuthor', '' ) : $setting ) );
        if ( !preg_match( '/^([^<>]+?)\s*<([^<>\s]+@[^<>\s]+)>$/', $setting, $m ) )
            return null;
        return array( trim( $m[1] ), $m[2] );
    }

    /**
     * Whether the directory is (the top of) a git repository
     *
     * @return bool
     */
    public function isRepository()
    {
        return is_dir( $this->dir . '/.git' ) || is_file( $this->dir . '/.git' );
    }

    /**
     * Commits everything in the directory that changed since the last commit
     *
     * @param string $message
     * @param bool $init make the directory a repository when it is not one
     * @param bool $dryRun only say whether there is something to commit
     * @return array array( status (committed|no-change|would-commit|not-a-repository|error), message, commit ('' none) )
     */
    public function commit( $message, $init = false, $dryRun = false )
    {
        if ( !is_dir( $this->dir ) )
            return array( 'status' => 'error', 'message' => 'no directory ' . $this->dir, 'commit' => '' );
        if ( !$this->isRepository() )
        {
            if ( !$init )
                return array( 'status' => 'not-a-repository', 'message' => $this->dir . ' is not a git repository', 'commit' => '' );
            if ( $dryRun )
                return array( 'status' => 'would-commit', 'message' => 'git init and a first commit', 'commit' => '' );
            $run = $this->git( array( 'init', '--quiet' ) );
            if ( $run['code'] !== 0 )
                return array( 'status' => 'error', 'message' => 'git init: ' . trim( $run['output'] ), 'commit' => '' );
        }
        $status = $this->git( array( 'status', '--porcelain', '--untracked-files=all' ) );
        if ( $status['code'] !== 0 )
            return array( 'status' => 'error', 'message' => 'git status: ' . trim( $status['output'] ), 'commit' => '' );
        if ( trim( $status['output'] ) === '' )
            return array( 'status' => 'no-change', 'message' => 'No change.', 'commit' => '' );
        $changed = count( preg_split( '/\n/', trim( $status['output'] ) ) ) . ' files changed';
        if ( $dryRun )
            return array( 'status' => 'would-commit', 'message' => $changed, 'commit' => '' );
        $add = $this->git( array( 'add', '--all', '.' ) );
        if ( $add['code'] !== 0 )
            return array( 'status' => 'error', 'message' => 'git add: ' . trim( $add['output'] ), 'commit' => '' );
        $args = array();
        $author = self::author();
        if ( $author !== null )
            $args = array( '-c', 'user.name=' . $author[0], '-c', 'user.email=' . $author[1] );
        $commit = $this->git( array_merge( $args, array( 'commit', '--quiet', '--no-verify', '-m', (string)$message ) ) );
        if ( $commit['code'] !== 0 )
            return array( 'status' => 'error', 'message' => 'git commit: ' . trim( $commit['output'] ), 'commit' => '' );
        $head = $this->git( array( 'rev-parse', '--short', 'HEAD' ) );
        return array( 'status' => 'committed', 'message' => $changed, 'commit' => trim( $head['output'] ) );
    }

    /**
     * Runs git in the directory, without a shell
     *
     * @param string[] $args
     * @return array array( code, output )
     */
    protected function git( array $args )
    {
        $command = array_merge( array( $this->git, '-C', $this->dir ), $args );
        $process = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'redirect', 1 ) ), $pipes );
        if ( !is_resource( $process ) )
            return array( 'code' => 127, 'output' => 'git could not be started' );
        fclose( $pipes[0] );
        $output = (string)stream_get_contents( $pipes[1] );
        fclose( $pipes[1] );
        return array( 'code' => proc_close( $process ), 'output' => $output );
    }
}

?>
