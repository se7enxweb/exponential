<?php
/**
 * File containing the expUpgradeStepCodeUpdate class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The code: the installation's git checkout (and each checkout named with the option repos, --repos=extension/x,...)
 * moved to its target, fast-forward only, through git_manager. The target is the option to (--to=v6.0.16: a tag, a
 * branch or a commit) or the upstream of the checked-out branch. The submodules the root records follow it.
 *
 * Never over local changes: a checkout with changed tracked files is refused, and so is one that cannot be
 * fast-forwarded (it has commits the target lacks). Before the first move git_manager makes its backup (database and
 * var, BackupManager::createFullCaption()) unless --no-backup, and the commits every checkout was on are written to
 * code.json in the upgrade's backup, which exp:upgrade:rollback moves them back to.
 *
 * Status and detection read the refs of the last fetch; the dry run and the run fetch first (the remote-tracking refs
 * only, never the working tree), and only as the owner of the checkout.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepCodeUpdate extends expUpgradeBaseStep
{
    public function id()
    {
        return '6.0.15-code-update';
    }

    public function stage()
    {
        return 'code';
    }

    public function description()
    {
        return 'The git checkouts moved to the target release, fast-forward only, through git_manager (--to=, --repos=)';
    }

    /**
     * The checkouts: the root, then each of the option repos (relative to the root).
     *
     * @param expUpgradeContext $context
     * @return string[] path => label
     */
    public static function repos( expUpgradeContext $context )
    {
        $root = (string)$context->option( 'root' );
        $repos = array( $root => 'root' );
        foreach ( expUpgrade::listOption( $context->option( 'repos', array() ) ) as $rel )
        {
            if ( $rel === 'root' )
                continue;
            if ( strpos( $rel, '..' ) !== false || $rel[0] === '/' || !preg_match( '#^[A-Za-z0-9_][A-Za-z0-9._/-]*$#', $rel ) )
                throw new InvalidArgumentException( "--repos: not a path inside the installation: $rel" );
            $repos[$root . '/' . $rel] = $rel;
        }
        return $repos;
    }

    /** @return array label => state */
    protected function states( expUpgradeContext $context )
    {
        $out = array();
        foreach ( self::repos( $context ) as $path => $label )
        {
            if ( !file_exists( "$path/.git" ) )
                throw new RuntimeException( "$label is not a git checkout" );
            $out[$label] = expUpgradeGit::state( $path, (string)$context->option( 'to', '' ) );
        }
        return $out;
    }

    public function detect( expUpgradeContext $context )
    {
        $root = (string)$context->option( 'root' );
        if ( !file_exists( "$root/.git" ) )
            return self::effect( self::NOT_APPLICABLE, 'the installation is not a git checkout: update the code by hand' );
        if ( !expUpgradeGit::available() )
            return self::effect( self::NOT_APPLICABLE, 'git_manager is not active: update the code by hand (or activate extension/git_manager)' );
        // another user's checkout: git itself refuses it (safe.directory), and a run as this user must not write it
        $owners = array();
        foreach ( self::repos( $context ) as $path => $label )
            if ( ( $p = expUpgradeComposer::ownerProblem( "$path/.git" ) ) !== '' )
                $owners[] = "$label: $p";
        if ( $owners )
            return self::effect( $context->option( 'mode', 'status' ) === 'apply' ? self::MISSING : self::UNKNOWN, implode( '; ', $owners ) );
        // a dry run and a run fetch first, once (status reads the refs of the last fetch)
        $fetched = array();
        if ( $context->option( 'mode', 'status' ) !== 'status' && !$context->option( 'code-fetched', false ) )
        {
            $fetched = $this->fetchAll( $context );
            $context->setOption( 'code-fetched', true );
            $context->setOption( 'code-fetch-lines', $fetched );
        }
        $behind = array();
        $unknown = array();
        foreach ( $this->states( $context ) as $label => $s )
        {
            if ( $s['target_commit'] === '' )
                $unknown[] = "$label: {$s['target']} does not resolve" . ( $s['target'] === '@{u}' ? ' (no upstream: give --to=)' : ' (not fetched yet: the dry run fetches)' );
            else if ( $s['behind'] > 0 )
                $behind[] = "$label {$s['behind']} commit(s) behind {$s['target']}" . ( $s['ahead'] ? ", {$s['ahead']} ahead: cannot fast-forward" : '' )
                          . ( $s['dirty'] ? ', has local changes' : '' );
        }
        if ( $behind )
            return self::effect( self::MISSING, implode( '; ', $behind ) );
        if ( $unknown )
            return self::effect( self::UNKNOWN, implode( '; ', $unknown ) );
        return self::effect( self::PRESENT, 'every checkout is at its target (as of the last fetch)' );
    }

    /**
     * Fetches every checkout, as the owner only.
     *
     * @return string[] what happened
     */
    protected function fetchAll( expUpgradeContext $context )
    {
        $lines = array();
        foreach ( $this->states( $context ) as $label => $s )
        {
            $problem = expUpgradeComposer::ownerProblem( $s['path'] . '/.git' );
            if ( $problem !== '' )
            {
                $lines[] = "$label: not fetched ($problem)";
                continue;
            }
            foreach ( expUpgradeGit::fetch( $s['path'], $s['remote'] ) as $p )
                $lines[] = $p;
        }
        return $lines;
    }

    public function plan( expUpgradeContext $context )
    {
        $lines = (array)$context->option( 'code-fetch-lines', array() );
        foreach ( $this->states( $context ) as $label => $s )
        {
            if ( $s['target_commit'] === '' )
            {
                $lines[] = "$label: {$s['target']} does not resolve";
                continue;
            }
            if ( !$s['behind'] )
            {
                $lines[] = "$label: at {$s['target']}";
                continue;
            }
            $lines[] = sprintf( '%s: %s %s -> %s (%s, %d commit(s))%s%s', $label, $s['branch'], substr( $s['head'], 0, 10 ),
                                substr( $s['target_commit'], 0, 10 ), $s['target'], $s['behind'],
                                $s['ahead'] ? " REFUSED: {$s['ahead']} local commit(s) the target lacks" : '',
                                $s['dirty'] ? ' REFUSED: local changes in ' . implode( ', ', array_slice( $s['dirty'], 0, 5 ) ) : '' );
            foreach ( $s['subjects'] as $subject )
                $lines[] = "    $subject";
        }
        return $lines;
    }

    public function apply( expUpgradeContext $context )
    {
        $lines = (array)$context->option( 'code-fetch-lines', array() );
        $states = $this->states( $context );
        // refuse before anything moves
        foreach ( $states as $label => $s )
        {
            $problem = expUpgradeComposer::ownerProblem( $s['path'] . '/.git' );
            if ( $problem !== '' )
                throw new RuntimeException( "$label: $problem" );
            if ( $s['behind'] && $s['dirty'] )
                throw new RuntimeException( "$label has local changes, nothing was moved: " . implode( ', ', array_slice( $s['dirty'], 0, 10 ) ) );
            if ( $s['behind'] && $s['ahead'] )
                throw new RuntimeException( "$label has {$s['ahead']} commit(s) {$s['target']} lacks: not a fast-forward, nothing was moved" );
            if ( $s['target_commit'] === '' )
                throw new RuntimeException( "$label: {$s['target']} does not resolve, nothing was moved" );
        }
        $lines = array_merge( $lines, $this->backup( $context, $states ) );
        foreach ( $states as $label => $s )
        {
            if ( !$s['behind'] )
                continue;
            $r = expUpgradeGit::git( $s['path'], array( 'merge', '--ff-only', $s['target_commit'] ), 300 );
            if ( !$r['ok'] )
                throw new RuntimeException( "$label: git merge --ff-only failed: " . trim( $r['output'] ) );
            $lines[] = sprintf( '%s: %s -> %s (%d commit(s), %s)', $label, substr( $s['head'], 0, 10 ), substr( $s['target_commit'], 0, 10 ), $s['behind'], $s['target'] );
            $sub = trim( expUpgradeGit::updateSubmodules( $s['path'] ) );
            if ( $sub !== '' )
                $lines[] = "$label: submodules: " . preg_replace( '/\s+/', ' ', $sub );
        }
        $context->setOption( 'code-moved', true );
        return $lines;
    }

    /**
     * git_manager's backup (unless no-backup) and the commits every checkout is on now (code.json).
     *
     * @param expUpgradeContext $context
     * @param array $states
     * @return string[]
     */
    protected function backup( expUpgradeContext $context, array $states )
    {
        $lines = array();
        $dir = (string)$context->option( 'backup-path', '' );
        if ( $dir === '' )
        {
            $dir = rtrim( (string)$context->option( 'backup-dir', '' ) ?: expUpgradeBackup::baseDir(), '/' ) . '/' . date( 'Ymd-His' ) . '-code';
            @mkdir( $dir, 0700, true );
        }
        $heads = array( 'format' => 'exp-upgrade-code/1', 'created' => date( 'c' ), 'repos' => array() );
        foreach ( $states as $label => $s )
            $heads['repos'][$label] = array( 'path' => $s['path'], 'head' => $s['head'], 'branch' => $s['branch'], 'target' => $s['target'] );
        if ( file_put_contents( "$dir/code.json", json_encode( $heads, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" ) === false )
            throw new RuntimeException( "cannot write $dir/code.json, nothing was moved" );
        @chmod( "$dir/code.json", 0600 );
        $lines[] = "the commits before the move: $dir/code.json";
        // git_manager's caption dumps MySQL/MariaDB only; on the other engines the upgrade's own backup holds the database
        if ( !$context->option( 'no-backup', false ) && class_exists( 'BackupManager' ) && $context->engine() !== 'mysql' )
            $lines[] = 'git_manager backup: not made (it dumps MySQL/MariaDB only); the upgrade backup holds the database';
        else if ( !$context->option( 'no-backup', false ) && class_exists( 'BackupManager' ) )
        {
            $manager = new BackupManager();
            $result = $manager->createFullCaption( 'exp:upgrade: before the code moves to ' . (string)( $context->option( 'to', '' ) ?: 'the upstream' ) );
            if ( empty( $result['success'] ) )
                throw new RuntimeException( 'the git_manager backup failed, nothing was moved: ' . ( isset( $result['message'] ) ? $result['message'] : '' ) );
            $lines[] = 'git_manager backup: ' . $result['message'];
        }
        return $lines;
    }
}
