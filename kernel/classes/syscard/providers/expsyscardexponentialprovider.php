<?php
/**
 * File containing the expSysCardExponentialProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Exponential itself: the version and its line, and, for a git checkout, the branch and the commit. The git facts are
 * read from the files of .git (HEAD, the ref, packed-refs; a worktree's commondir too); git is never run.
 */
class expSysCardExponentialProvider extends expSysCardBaseProvider
{
    protected $id = 'exponential';
    protected $section = 'exponential';
    protected $priority = 85;
    protected $budget = 0.5;

    public function collect( expSysCardContext $context )
    {
        $facts = array();
        $version = (string)$context->get( 'exponential.version', function () { return class_exists( 'ExponentialSDK' ) ? ExponentialSDK::version( true, false, false ) : ''; }, '' );
        $state = (string)$context->get( 'exponential.state', function () { return class_exists( 'ExponentialSDK' ) ? (string)ExponentialSDK::state() : ''; }, '' );
        $line = (string)$context->get( 'exponential.alias', function () { return class_exists( 'ExponentialSDK' ) ? (string)ExponentialSDK::alias() : ''; }, '' );
        $details = array_values( array_filter( array( $state, $line !== '' ? $line . ' line' : '' ), 'strlen' ) );
        $facts[] = $this->fact( 'version', 'Version', $version !== '' ? 'Exponential ' . $version . ( $details ? ' (' . implode( ', ', $details ) . ')' : '' ) : 'n/a' );
        $git = self::git( $context );
        if ( $git !== null )
            $facts[] = $this->fact( 'git', 'Git', ( $git['branch'] !== '' ? $git['branch'] : 'detached' ) . ( $git['commit'] !== '' ? ' @ ' . substr( $git['commit'], 0, 10 ) : '' ) );
        return $facts;
    }

    /**
     * The branch and commit of the checkout at the root, or null when it is not one.
     *
     * @param expSysCardContext $context
     * @return array|null array( 'branch' => string, 'commit' => string )
     */
    public static function git( expSysCardContext $context )
    {
        $gitDir = $context->root . '/.git';
        $pointer = $context->file( $gitDir, 4096 );
        if ( $pointer !== null && preg_match( '/^gitdir:\s*(.+)$/m', $pointer, $m ) )
        {
            $gitDir = trim( $m[1] );
            if ( $gitDir !== '' && $gitDir[0] !== '/' )
                $gitDir = $context->root . '/' . $gitDir;
        }
        $head = $context->file( $gitDir . '/HEAD', 4096 );
        if ( $head === null )
            return null;
        $head = trim( $head );
        $common = $gitDir;
        $commonDir = $context->file( $gitDir . '/commondir', 4096 );
        if ( $commonDir !== null && trim( $commonDir ) !== '' )
        {
            $common = trim( $commonDir );
            if ( $common[0] !== '/' )
                $common = $gitDir . '/' . $common;
        }
        if ( preg_match( '/^[0-9a-f]{40}$/', $head ) )
            return array( 'branch' => '', 'commit' => $head );
        if ( !preg_match( '#^ref:\s*(refs/\S+)$#', $head, $m ) )
            return null;
        $ref = $m[1];
        $branch = preg_replace( '#^refs/heads/#', '', $ref );
        foreach ( array( $gitDir, $common ) as $dir )
        {
            $sha = $context->file( $dir . '/' . $ref, 4096 );
            if ( $sha !== null && preg_match( '/^[0-9a-f]{40}/', trim( $sha ) ) )
                return array( 'branch' => $branch, 'commit' => substr( trim( $sha ), 0, 40 ) );
        }
        $packed = (string)$context->file( $common . '/packed-refs', 4194304 );
        if ( preg_match( '/^([0-9a-f]{40}) ' . preg_quote( $ref, '/' ) . '$/m', $packed, $m ) )
            return array( 'branch' => $branch, 'commit' => $m[1] );
        return array( 'branch' => $branch, 'commit' => '' );
    }
}
