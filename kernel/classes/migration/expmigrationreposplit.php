<?php
/**
 * File containing the expMigrationRepoSplit class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Splits the migrations folder of the installation's git checkout into a repository of its own, with its history
 * (exp:migration:repo:split). What git subtree split does, with git's own plumbing, so no extra tool is needed: every
 * commit that changed the folder becomes a commit whose tree is the folder alone, with the same author, committer,
 * dates and message, and the same shape of history (merges kept where both sides changed the folder).
 *
 *   1. the history of the folder becomes the branch Branch (default migrations-split) of the checkout; a branch that
 *      exists is never moved
 *   2. with a target folder: a new repository there with that history as its branch (default main) and, with a
 *      remote, the remote origin (never pushed: the first push is the owner's)
 *   3. the report names what to change next: migration.ini [RepositorySettings] Mode=separate and Path, and taking
 *      the folder out of the site's checkout. Neither the settings nor the site's files are changed.
 *
 * Git runs without a shell and with a time limit (proc_open with an argument list).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRepoSplit
{
    /** The branch of the checkout the history goes to. */
    const DEFAULT_BRANCH = 'migrations-split';

    /** The branch of the new repository. */
    const TARGET_BRANCH = 'main';

    /** The seconds one git command may take. */
    const TIMEOUT = 300;

    /**
     * Plans or makes the split.
     *
     * @param array $options root (the installation), directory (the folder to split, default the first
     *                       [MigrationSettings] DirectoryList entry), branch, to (the new repository's folder), remote
     *                       (its origin), apply, ini (migration.ini)
     * @return array mode, directory, top, prefix, branch, to, remote, commits (int: commits that changed the folder),
     *               head (the last commit of the split, with apply), created (bool), next (string[]: what to do then),
     *               errors, warnings
     */
    public static function run( array $options )
    {
        $apply = !empty( $options['apply'] );
        $root = rtrim( isset( $options['root'] ) && $options['root'] !== '' ? (string)$options['root'] : (string)eZSys::rootDir(), '/' );
        $report = array( 'mode' => $apply ? 'repo-split' : 'repo-split-dry-run', 'directory' => '', 'top' => '', 'prefix' => '',
                         'branch' => isset( $options['branch'] ) && $options['branch'] !== '' ? (string)$options['branch'] : self::DEFAULT_BRANCH,
                         'to' => isset( $options['to'] ) ? (string)$options['to'] : '', 'remote' => isset( $options['remote'] ) ? (string)$options['remote'] : '',
                         'commits' => 0, 'head' => '', 'created' => false, 'next' => array(), 'errors' => array(), 'warnings' => array() );
        $directory = isset( $options['directory'] ) && $options['directory'] !== '' ? (string)$options['directory'] : '';
        if ( $directory === '' )
        {
            $settings = expMigrationLocator::settings( isset( $options['ini'] ) ? $options['ini'] : eZINI::instance( 'migration.ini' ) );
            $directory = $settings['directories'] ? (string)$settings['directories'][0] : '';
        }
        if ( $directory === '' )
        {
            $report['errors'][] = 'no migrations folder: name it with --directory or in [MigrationSettings] DirectoryList';
            return $report;
        }
        $absolute = $directory[0] === '/' ? $directory : "$root/$directory";
        $report['directory'] = expMigrationCheck::relative( $absolute, $root );
        if ( !is_dir( $absolute ) )
        {
            $report['errors'][] = $report['directory'] . ' is not a folder';
            return $report;
        }
        if ( !self::validBranch( $report['branch'] ) )
            $report['errors'][] = 'the branch ' . $report['branch'] . ' is not a valid branch name';
        if ( $report['remote'] !== '' && !self::validRemote( $report['remote'] ) )
            $report['errors'][] = 'the remote is a URL (https://, ssh://, git@host:path) or a path, without spaces';
        if ( $report['to'] !== '' )
        {
            $to = $report['to'][0] === '/' ? $report['to'] : "$root/" . $report['to'];
            if ( file_exists( $to ) && ( !is_dir( $to ) || count( array_diff( (array)scandir( $to ), array( '.', '..' ) ) ) > 0 ) )
                $report['errors'][] = $report['to'] . ' exists and is not empty: the new repository goes into a new or empty folder';
            $real = realpath( $absolute );
            if ( $real !== false && ( strpos( rtrim( $to, '/' ) . '/', rtrim( $real, '/' ) . '/' ) === 0 ) )
                $report['errors'][] = 'the new repository cannot be inside the folder it is split from';
        }
        $top = self::git( $absolute, array( 'rev-parse', '--show-toplevel' ) );
        if ( !$top['ok'] )
        {
            $report['errors'][] = $report['directory'] . ' is not in a git checkout: ' . trim( $top['output'] );
            return $report;
        }
        $report['top'] = trim( $top['output'] );
        $prefix = self::git( $absolute, array( 'rev-parse', '--show-prefix' ) );
        $report['prefix'] = rtrim( trim( $prefix['output'] ), '/' );
        if ( $report['prefix'] === '' )
            $report['errors'][] = $report['directory'] . ' is the top of its checkout already: it is a repository of its own';
        if ( $report['errors'] )
            return $report;

        $exists = self::git( $report['top'], array( 'rev-parse', '--verify', '--quiet', 'refs/heads/' . $report['branch'] ) );
        if ( $exists['ok'] )
            $report['errors'][] = 'the branch ' . $report['branch'] . ' exists already in the checkout; name another with --branch (a branch is never moved)';
        $list = self::git( $report['top'], array( 'rev-list', '--reverse', '--topo-order', '--parents', 'HEAD', '--', $report['prefix'] ) );
        if ( !$list['ok'] )
        {
            $report['errors'][] = 'git rev-list failed: ' . trim( $list['output'] );
            return $report;
        }
        $commits = array_values( array_filter( preg_split( '/\n/', trim( $list['output'] ) ), 'strlen' ) );
        $report['commits'] = count( $commits );
        if ( !$commits )
            $report['errors'][] = 'no commit changed ' . $report['prefix'] . ': there is no history to split (commit the folder first)';
        $status = self::git( $report['top'], array( 'status', '--porcelain', '--', $report['prefix'] ) );
        if ( $status['ok'] && trim( $status['output'] ) !== '' )
            $report['warnings'][] = $report['prefix'] . ' has changes that are not committed: they are not in the split';
        $report['next'] = self::nextSteps( $report );
        if ( $report['errors'] || !$apply )
            return $report;

        $map = array();
        $trees = array();
        $last = '';
        foreach ( $commits as $line )
        {
            $parts = explode( ' ', $line );
            $commit = array_shift( $parts );
            $tree = self::git( $report['top'], array( 'rev-parse', '--verify', '--quiet', $commit . ':' . $report['prefix'] ) );
            $parents = array();
            foreach ( $parts as $parent )
                if ( isset( $map[$parent] ) && $map[$parent] !== '' && !in_array( $map[$parent], $parents, true ) )
                    $parents[] = $map[$parent];
            if ( !$tree['ok'] )
            {
                // the folder is gone in this commit: the split goes on from its parent
                $map[$commit] = $parents ? $parents[0] : '';
                continue;
            }
            $tree = trim( $tree['output'] );
            if ( count( $parents ) === 1 && isset( $trees[$parents[0]] ) && $trees[$parents[0]] === $tree )
            {
                $map[$commit] = $parents[0];
                continue;
            }
            $made = self::commitLike( $report['top'], $commit, $tree, $parents );
            if ( $made === '' )
            {
                $report['errors'][] = "the commit $commit could not be rewritten";
                return $report;
            }
            $map[$commit] = $made;
            $trees[$made] = $tree;
            $last = $made;
        }
        $head = end( $commits );
        $head = $map[explode( ' ', $head )[0]] ?: $last;
        $report['head'] = $head;
        $ref = self::git( $report['top'], array( 'update-ref', 'refs/heads/' . $report['branch'], $head, '' ) );
        if ( !$ref['ok'] )
        {
            $report['errors'][] = 'the branch could not be made: ' . trim( $ref['output'] );
            return $report;
        }
        if ( $report['to'] !== '' )
        {
            $to = $report['to'][0] === '/' ? $report['to'] : "$root/" . $report['to'];
            if ( !is_dir( $to ) && !eZDir::mkdir( $to, false, true ) && !is_dir( $to ) )
            {
                $report['errors'][] = 'cannot make the folder ' . $report['to'];
                return $report;
            }
            foreach ( array( array( 'init', '--quiet', '--initial-branch=' . self::TARGET_BRANCH ),
                             array( 'pull', '--quiet', '--ff-only', $report['top'], 'refs/heads/' . $report['branch'] ) ) as $arguments )
            {
                $r = self::git( $to, $arguments );
                if ( !$r['ok'] )
                {
                    $report['errors'][] = 'git ' . $arguments[0] . ' in ' . $report['to'] . ' failed: ' . trim( $r['output'] );
                    return $report;
                }
            }
            if ( $report['remote'] !== '' )
            {
                $r = self::git( $to, array( 'remote', 'add', 'origin', $report['remote'] ) );
                if ( !$r['ok'] )
                    $report['errors'][] = 'git remote add failed: ' . trim( $r['output'] );
            }
            $report['created'] = !$report['errors'];
        }
        return $report;
    }

    /**
     * A new commit with a commit's author, committer, dates and message, on a tree and parents.
     *
     * @return string the new commit, '' on failure
     */
    protected static function commitLike( $top, $commit, $tree, array $parents )
    {
        $meta = self::git( $top, array( 'log', '-1', '--format=%an%x00%ae%x00%aI%x00%cn%x00%ce%x00%cI%x00%B', $commit ) );
        if ( !$meta['ok'] )
            return '';
        $fields = explode( "\0", $meta['output'], 7 );
        if ( count( $fields ) < 7 )
            return '';
        $env = array( 'GIT_AUTHOR_NAME' => $fields[0], 'GIT_AUTHOR_EMAIL' => $fields[1], 'GIT_AUTHOR_DATE' => $fields[2],
                      'GIT_COMMITTER_NAME' => $fields[3], 'GIT_COMMITTER_EMAIL' => $fields[4], 'GIT_COMMITTER_DATE' => $fields[5] );
        $arguments = array( 'commit-tree', $tree );
        foreach ( $parents as $parent )
        {
            $arguments[] = '-p';
            $arguments[] = $parent;
        }
        $r = self::git( $top, $arguments, rtrim( $fields[6], "\n" ) . "\n", $env );
        return $r['ok'] && preg_match( '/^[0-9a-f]{40,64}$/', trim( $r['output'] ) ) ? trim( $r['output'] ) : '';
    }

    /**
     * What to do after the split. Pure.
     *
     * @param array $report
     * @return string[]
     */
    public static function nextSteps( array $report )
    {
        $to = $report['to'] !== '' ? $report['to'] : '<the new repository>';
        $lines = array();
        if ( $report['to'] === '' )
            $lines[] = 'Make the new repository from the branch: git init --initial-branch=' . self::TARGET_BRANCH . ' ' . $to . ' && git -C ' . $to
                     . ' pull ' . $report['top'] . ' ' . $report['branch'];
        $lines[] = $report['remote'] !== '' ? 'Push it when it looks right: git -C ' . $to . ' push -u origin ' . self::TARGET_BRANCH
                                            : 'Add its remote and push it: git -C ' . $to . ' remote add origin <url> && git -C ' . $to . ' push -u origin ' . self::TARGET_BRANCH;
        $lines[] = 'Point the installation at it in settings/override/migration.ini.append.php: [RepositorySettings] Mode=separate, Path=' . $to
                 . ' (and [MigrationSettings] DirectoryList[]=' . $to . ' if the migrations move there too)';
        $lines[] = 'Take the folder out of the site\'s checkout when the installation reads the new one: git rm -r --cached ' . $report['prefix']
                 . ' and add it to .gitignore';
        return $lines;
    }

    /** @return bool a branch name git takes. Pure. */
    public static function validBranch( $name )
    {
        $name = (string)$name;
        return $name !== '' && strlen( $name ) <= 200 && preg_match( '#^[A-Za-z0-9][A-Za-z0-9._/-]*$#', $name )
            && strpos( $name, '..' ) === false && strpos( $name, '//' ) === false && substr( $name, -1 ) !== '/'
            && substr( $name, -5 ) !== '.lock';
    }

    /** @return bool a remote URL or path without spaces or options. Pure. */
    public static function validRemote( $remote )
    {
        $remote = (string)$remote;
        return $remote !== '' && $remote[0] !== '-' && !preg_match( '/[\s\x00-\x1f]/', $remote ) && strlen( $remote ) <= 500;
    }

    /**
     * git without a shell.
     *
     * @param string $path the working directory
     * @param string[] $arguments
     * @param string|null $input the standard input
     * @param array $env variables added to the environment
     * @return array ok, exit, output (standard output; standard error after a failure)
     */
    protected static function git( $path, array $arguments, $input = null, array $env = array() )
    {
        $command = array_merge( array( 'git' ), $arguments );
        $environment = $env ? array_merge( getenv(), $env ) : null;
        $pipes = array();
        $process = @proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $path, $environment );
        if ( !is_resource( $process ) )
            return array( 'ok' => false, 'exit' => -1, 'output' => 'git cannot be started' );
        if ( $input !== null )
            fwrite( $pipes[0], $input );
        fclose( $pipes[0] );
        stream_set_blocking( $pipes[1], false );
        stream_set_blocking( $pipes[2], false );
        $out = '';
        $err = '';
        $end = time() + self::TIMEOUT;
        while ( true )
        {
            $read = array( $pipes[1], $pipes[2] );
            $write = null;
            $except = null;
            if ( @stream_select( $read, $write, $except, 1 ) === false )
                break;
            foreach ( $read as $pipe )
            {
                $chunk = fread( $pipe, 65536 );
                if ( $pipe === $pipes[1] )
                    $out .= (string)$chunk;
                else
                    $err .= (string)$chunk;
            }
            if ( feof( $pipes[1] ) && feof( $pipes[2] ) )
                break;
            if ( time() > $end )
            {
                proc_terminate( $process );
                $err .= 'git took longer than ' . self::TIMEOUT . ' seconds';
                break;
            }
        }
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $exit = proc_close( $process );
        return array( 'ok' => $exit === 0, 'exit' => $exit, 'output' => $exit === 0 ? $out : trim( $err . "\n" . $out ) );
    }
}
