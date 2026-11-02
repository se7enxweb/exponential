<?php
/**
 * exp:migration:repo:split against throwaway git checkouts (needs git, no database).
 *
 *  MS-1 the dry run counts the commits that changed the folder and changes nothing
 *  MS-2 the split: only the commits of the folder, its files at the top, authors, dates and messages kept, the
 *       branch made; a new repository with the branch main and the remote origin, never pushed
 *  MS-3 refusals: an existing branch (never moved), a target that is not empty, a folder outside a checkout, an
 *       invalid branch name or remote
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationRepoSplitTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $dir = '';

    protected function setUp(): void
    {
        parent::setUp();
        exec( 'git --version 2>&1', $out, $rc );
        if ( $rc !== 0 )
            $this->markTestSkipped( 'git is not installed' );
        $this->dir = eZSys::rootDir() . '/var/tmp/expmigrationreposplittest-' . getmypid();
        eZDir::mkdir( $this->dir . '/site/migrations', false, true );
        $site = $this->dir . '/site';
        $this->git( $site, 'init', '--quiet', '--initial-branch=main' );
        $this->commit( $site, 'README', 'site', 'Added: the site', 'Ann Author <ann@example.com>', '2026-01-01T10:00:00+00:00' );
        $this->commit( $site, 'migrations/20260101100000_one.yml', "steps: []\n", 'Added: the first migration', 'Bob Builder <bob@example.com>', '2026-01-02T10:00:00+00:00' );
        $this->commit( $site, 'README', 'site 2', 'Updated: the site', 'Ann Author <ann@example.com>', '2026-01-03T10:00:00+00:00' );
        $this->commit( $site, 'migrations/20260104100000_two.yml', "steps: []\n", 'Added: the second migration', 'Cleo Coder <cleo@example.com>', '2026-01-04T10:00:00+00:00' );
    }

    protected function tearDown(): void
    {
        if ( $this->dir !== '' && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
        parent::tearDown();
    }

    protected function git( $path, ...$arguments )
    {
        $command = 'git -C ' . escapeshellarg( $path ) . ' -c user.name=Test -c user.email=test@example.com';
        foreach ( $arguments as $argument )
            $command .= ' ' . escapeshellarg( $argument );
        exec( $command . ' 2>&1', $out, $rc );
        $this->assertSame( 0, $rc, implode( "\n", $out ) );
        return trim( implode( "\n", $out ) );
    }

    protected function commit( $site, $file, $content, $message, $author, $date )
    {
        file_put_contents( "$site/$file", $content );
        $this->git( $site, 'add', $file );
        putenv( "GIT_AUTHOR_DATE=$date" );
        putenv( "GIT_COMMITTER_DATE=$date" );
        $this->git( $site, 'commit', '--quiet', '-m', $message, '--author=' . $author );
        putenv( 'GIT_AUTHOR_DATE' );
        putenv( 'GIT_COMMITTER_DATE' );
    }

    /** MS-1 */
    public function testDryRun()
    {
        $site = $this->dir . '/site';
        $report = expMigrationRepoSplit::run( array( 'root' => $site, 'directory' => 'migrations', 'to' => '../split' ) );
        $this->assertSame( array(), $report['errors'] );
        $this->assertSame( 'repo-split-dry-run', $report['mode'] );
        $this->assertSame( 'migrations', $report['prefix'] );
        $this->assertSame( 2, $report['commits'] );
        $this->assertSame( '', $report['head'] );
        $this->assertNotEmpty( $report['next'] );
        $this->assertSame( '', $this->git( $site, 'branch', '--list', 'migrations-split' ) );
        $this->assertDirectoryDoesNotExist( $this->dir . '/split' );
    }

    /** MS-2 */
    public function testSplit()
    {
        $site = $this->dir . '/site';
        $report = expMigrationRepoSplit::run( array( 'root' => $site, 'directory' => 'migrations', 'to' => $this->dir . '/split',
                                                     'remote' => 'git@example.com:site/migrations.git', 'apply' => true ) );
        $this->assertSame( array(), $report['errors'] );
        $this->assertTrue( $report['created'] );
        $split = $this->dir . '/split';
        $this->assertSame( "Added: the second migration\nAdded: the first migration", $this->git( $split, 'log', '--format=%s' ) );
        $this->assertSame( 'Cleo Coder <cleo@example.com> ' . strtotime( '2026-01-04T10:00:00+00:00' ) . "\nBob Builder <bob@example.com> " . strtotime( '2026-01-02T10:00:00+00:00' ),
                           $this->git( $split, 'log', '--format=%an <%ae> %at' ) );
        $this->assertSame( "20260101100000_one.yml\n20260104100000_two.yml", $this->git( $split, 'ls-files' ) );
        $this->assertSame( 'main', $this->git( $split, 'rev-parse', '--abbrev-ref', 'HEAD' ) );
        $this->assertSame( 'git@example.com:site/migrations.git', $this->git( $split, 'remote', 'get-url', 'origin' ) );
        $this->assertSame( $report['head'], $this->git( $site, 'rev-parse', 'migrations-split' ) );
        $this->assertSame( $this->git( $split, 'rev-parse', 'HEAD' ), $report['head'] );
        $this->assertSame( "site 2", file_get_contents( "$site/README" ), 'the site is left as it is' );

        // the same history gives the same commits (dates and authors are kept)
        $again = expMigrationRepoSplit::run( array( 'root' => $site, 'directory' => 'migrations', 'branch' => 'again', 'apply' => true ) );
        $this->assertSame( $report['head'], $again['head'] );
    }

    /** MS-3 */
    public function testRefusals()
    {
        $site = $this->dir . '/site';
        $this->git( $site, 'branch', 'migrations-split' );
        $report = expMigrationRepoSplit::run( array( 'root' => $site, 'directory' => 'migrations', 'apply' => true ) );
        $this->assertNotEmpty( preg_grep( '/exists already/', $report['errors'] ) );
        $this->assertSame( $this->git( $site, 'rev-parse', 'HEAD' ), $this->git( $site, 'rev-parse', 'migrations-split' ), 'never moved' );

        eZDir::mkdir( $this->dir . '/full', false, true );
        file_put_contents( $this->dir . '/full/x', 'x' );
        $report = expMigrationRepoSplit::run( array( 'root' => $site, 'directory' => 'migrations', 'branch' => 'b2', 'to' => $this->dir . '/full' ) );
        $this->assertNotEmpty( preg_grep( '/not empty/', $report['errors'] ) );
        $report = expMigrationRepoSplit::run( array( 'root' => $site, 'directory' => 'migrations', 'branch' => 'b..d', 'remote' => '--upload-pack=x' ) );
        $this->assertCount( 2, $report['errors'] );
        eZDir::mkdir( $this->dir . '/plain', false, true );
        $report = expMigrationRepoSplit::run( array( 'root' => $this->dir, 'directory' => '/nonexistent-folder-' . getmypid() ) );
        $this->assertNotEmpty( $report['errors'] );
        $this->assertTrue( expMigrationRepoSplit::validBranch( 'migrations/split-1' ) );
        $this->assertFalse( expMigrationRepoSplit::validBranch( 'x.lock' ) );
        $this->assertFalse( expMigrationRepoSplit::validRemote( 'a b' ) );
    }
}
