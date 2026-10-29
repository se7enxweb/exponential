<?php
/**
 * File containing the expUpgradeGit class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The checkouts of the code stage, through git_manager (extension/git_manager): GitManager runs git in the working
 * directory without a shell, so each call here changes into the checkout first and back afterwards. Without
 * git_manager the code stage does not apply (update the code by hand).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeGit
{
    /** @return bool git_manager's classes are there */
    public static function available()
    {
        return class_exists( 'GitManager' );
    }

    /**
     * Runs a callable inside a checkout.
     *
     * @param string $path
     * @param callable $fn GitManager -> mixed
     * @return mixed
     */
    public static function in( $path, $fn )
    {
        $before = getcwd();
        if ( !@chdir( $path ) )
            throw new RuntimeException( "cannot enter $path" );
        try
        {
            return $fn( GitManager::getInstance() );
        }
        finally
        {
            chdir( $before );
        }
    }

    /**
     * git with arguments in a checkout (GitManager::git(): no shell): array( 'ok', 'exit', 'output' ).
     *
     * @param string $path
     * @param string[] $arguments
     * @param int $seconds
     * @return array
     */
    public static function git( $path, array $arguments, $seconds = 120 )
    {
        return self::in( $path, function ( $gm ) use ( $arguments, $seconds ) {
            return $gm->git( $arguments, $seconds );
        } );
    }

    /** @return string trimmed output, '' when git failed */
    public static function value( $path, array $arguments )
    {
        $r = self::git( $path, $arguments );
        return $r['ok'] ? trim( $r['output'] ) : '';
    }

    /**
     * The state of a checkout against its target: the option to (a tag, a branch, a commit) or the upstream of the
     * branch (@{u}).
     *
     * @param string $path
     * @param string $to '' for the upstream
     * @return array head, branch, target (ref), target_commit ('' when it does not resolve), behind, ahead, dirty
     *               (changed tracked files), remote (to fetch from), subjects (of the commits to come)
     */
    public static function state( $path, $to = '' )
    {
        $state = array( 'path' => $path, 'head' => self::value( $path, array( 'rev-parse', '--verify', 'HEAD' ) ),
                        'branch' => self::value( $path, array( 'rev-parse', '--abbrev-ref', 'HEAD' ) ),
                        'target' => $to !== '' ? $to : '@{u}', 'target_commit' => '', 'behind' => 0, 'ahead' => 0,
                        'dirty' => array(), 'remote' => '', 'subjects' => array() );
        $upstream = self::value( $path, array( 'rev-parse', '--abbrev-ref', '--symbolic-full-name', '@{u}' ) );
        if ( $upstream !== '' && strpos( $upstream, '/' ) !== false )
            $state['remote'] = substr( $upstream, 0, strpos( $upstream, '/' ) );
        else
        {
            $remotes = self::in( $path, function ( $gm ) { return $gm->getRemotes(); } );
            $state['remote'] = isset( $remotes['origin'] ) ? 'origin' : (string)key( $remotes );
        }
        $state['target_commit'] = self::value( $path, array( 'rev-parse', '--verify', '--quiet', $state['target'] . '^{commit}' ) );
        if ( $state['target_commit'] !== '' && $state['head'] !== '' )
        {
            $state['behind'] = (int)self::value( $path, array( 'rev-list', '--count', 'HEAD..' . $state['target_commit'] ) );
            $state['ahead'] = (int)self::value( $path, array( 'rev-list', '--count', $state['target_commit'] . '..HEAD' ) );
            if ( $state['behind'] )
                $state['subjects'] = array_values( array_filter( explode( "\n", self::value( $path, array( 'log', '--format=%h %s', '--max-count=10', 'HEAD..' . $state['target_commit'] ) ) ), 'strlen' ) );
        }
        // --no-optional-locks: a status never rewrites the index (as another user it would leave a file of that user)
        $status = self::git( $path, array( '--no-optional-locks', 'status', '--porcelain', '--untracked-files=no' ) );
        foreach ( explode( "\n", $status['output'] ) as $line )
            if ( trim( $line ) !== '' )
                $state['dirty'][] = trim( $line );
        return $state;
    }

    /**
     * Fetches the remote of a checkout, with its tags (GitManager::fetch(), then the tags).
     *
     * @param string $path
     * @param string $remote
     * @return string[] problems
     */
    public static function fetch( $path, $remote )
    {
        if ( $remote === '' )
            return array( "$path: no remote to fetch from" );
        $problems = array();
        $r = self::in( $path, function ( $gm ) use ( $remote ) { return $gm->fetch( $remote ); } );
        if ( (int)$r['exit'] !== 0 )
            $problems[] = "$path: fetch $remote failed: " . trim( $r['output'] );
        $t = self::git( $path, array( 'fetch', '--tags', $remote ), 300 );
        if ( !$t['ok'] )
            $problems[] = "$path: fetch --tags $remote failed";
        return $problems;
    }

    /**
     * The submodules recorded in a checkout, brought to the commits it records (GitManager::updateSubmodules()).
     *
     * @param string $path
     * @return string what git said
     */
    public static function updateSubmodules( $path )
    {
        if ( !is_file( rtrim( $path, '/' ) . '/.gitmodules' ) )
            return '';
        return (string)self::in( $path, function ( $gm ) { return $gm->updateSubmodules(); } );
    }
}
