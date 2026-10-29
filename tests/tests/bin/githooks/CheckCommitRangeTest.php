<?php
/**
 * The commit message policy of .githooks/check-commit-range, the script behind the CI step
 * "Commit messages" (Quality workflow) and the local gates.
 *
 * Each test builds a throwaway git repository in its own temporary directory, with the
 * hooks of this checkout, makes commits by a project author (.githooks/commit-msg-authors)
 * and by a contributor, and runs the script on the range:
 * - a project commit with a bad subject fails;
 * - a contributor's commit with a bad subject only warns, so a merged pull request never
 *   turns main red;
 * - merge commits are not checked;
 * - EXP_COMMIT_MSG_STRICT_ALL=1 makes every author strict;
 * - --github prints ::error:: and ::warning:: annotations.
 *
 * Needs git and sh; skipped without them.
 *
 * Run: php vendor/bin/phpunit tests/tests/bin/githooks/CheckCommitRangeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class CheckCommitRangeTest extends PHPUnit\Framework\TestCase
{
    const PROJECT = 'info@se7enx.com';
    const CONTRIBUTOR = 'contributor@example.org';

    private static $root;

    private $repo;

    private $base;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 4 );
    }

    protected function setUp(): void
    {
        exec( 'git --version 2>&1', $out, $rc );
        if ( $rc !== 0 )
        {
            $this->markTestSkipped( 'needs git' );
        }

        $this->repo = sys_get_temp_dir() . '/exp-check-commit-range-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        mkdir( $this->repo . '/.githooks', 0700, true );
        foreach ( array( 'check-commit-range', 'commit-msg', 'commit-msg-authors' ) as $hook )
        {
            copy( self::$root . '/.githooks/' . $hook, $this->repo . '/.githooks/' . $hook );
        }
        $this->git( 'init -q -b main' );
        $this->commit( self::PROJECT, 'Added: The hooks of the test repository' );
        $this->base = $this->git( 'rev-parse HEAD' );
    }

    protected function tearDown(): void
    {
        if ( $this->repo && is_dir( $this->repo ) )
        {
            $items = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $this->repo, FilesystemIterator::SKIP_DOTS ),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ( $items as $item )
            {
                $item->isDir() && !$item->isLink() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
            }
            rmdir( $this->repo );
        }
    }

    public function testProjectCommitsWithTheProjectPrefixesPass()
    {
        $this->commit( self::PROJECT, 'Updated: One' );
        $this->commit( self::PROJECT, 'Fixed: Two' );
        $this->commit( self::PROJECT, 'Renamed: Three' );

        list( $rc, $out ) = $this->check();
        $this->assertSame( 0, $rc, $out );
        $this->assertStringContainsString( '3 commit(s), 0 error(s), 0 contributor warning(s)', $out );
    }

    public function testProjectCommitWithABadSubjectFails()
    {
        $this->commit( self::PROJECT, 'Changed: Something' );

        list( $rc, $out ) = $this->check();
        $this->assertSame( 1, $rc, $out );
        $this->assertStringContainsString( 'bad subject', $out );
        $this->assertStringContainsString( 'Changed: Something', $out );
    }

    public function testProjectAuthorIsMatchedWithoutRegardToCase()
    {
        $this->commit( 'Info@SE7ENX.com', 'Fix something' );

        list( $rc, $out ) = $this->check();
        $this->assertSame( 1, $rc, $out );
    }

    public function testContributorCommitWithABadSubjectOnlyWarns()
    {
        $this->commit( self::CONTRIBUTOR, 'Changed: ManagedByDeployment also refuses something' );
        $this->commit( self::CONTRIBUTOR, 'Fix typo' );
        $this->commit( self::PROJECT, 'Updated: The refinement on top' );

        list( $rc, $out ) = $this->check();
        $this->assertSame( 0, $rc, $out );
        $this->assertStringContainsString( '3 commit(s), 0 error(s), 2 contributor warning(s)', $out );
        $this->assertStringContainsString( 'kept as written', $out );
    }

    public function testContributorCommitWithTheProjectPrefixesDoesNotWarn()
    {
        $this->commit( self::CONTRIBUTOR, 'Added: A feature' );

        list( $rc, $out ) = $this->check();
        $this->assertSame( 0, $rc, $out );
        $this->assertStringContainsString( '0 contributor warning(s)', $out );
    }

    public function testMergedContributorBranchKeepsMainGreen()
    {
        // the shape of main 683869bb6c: a contributor's "Changed:" commit merged with a merge commit
        $this->git( 'checkout -q -b feature' );
        $this->commit( self::CONTRIBUTOR, 'Changed: A contributor change' );
        $this->git( 'checkout -q main' );
        $this->commit( self::PROJECT, 'Updated: Meanwhile on main' );
        $this->git( '-c user.name=7x -c user.email=' . self::PROJECT . ' merge -q --no-ff -m ' . escapeshellarg( 'Merged: The contributor branch' ) . ' feature' );

        list( $rc, $out ) = $this->check();
        $this->assertSame( 0, $rc, $out );
        $this->assertStringContainsString( '2 commit(s), 0 error(s), 1 contributor warning(s)', $out );
    }

    public function testStrictAllMakesContributorsStrict()
    {
        $this->commit( self::CONTRIBUTOR, 'Changed: Something' );

        list( $rc, $out ) = $this->check( array(), 'EXP_COMMIT_MSG_STRICT_ALL=1 ' );
        $this->assertSame( 1, $rc, $out );
    }

    public function testGithubAnnotations()
    {
        $this->commit( self::CONTRIBUTOR, 'Changed: A contributor change' );
        $this->commit( self::PROJECT, 'Changed: A project change' );

        list( $rc, $out ) = $this->check( array( '--github' ) );
        $this->assertSame( 1, $rc, $out );
        $this->assertMatchesRegularExpression( '/^::warning::Contributor commit [0-9a-f]+ Changed: A contributor change/m', $out );
        $this->assertMatchesRegularExpression( '/^::error::Commit [0-9a-f]+ Changed: A project change does not start with/m', $out );
    }

    public function testUsage()
    {
        list( $rc, $out ) = $this->run_( 'sh .githooks/check-commit-range 2>&1' );
        $this->assertSame( 2, $rc, $out );
        $this->assertStringContainsString( 'usage:', $out );
    }

    private function check( array $options = array(), $env = '' )
    {
        $args = array_merge( $options, array( $this->base, 'HEAD' ) );
        return $this->run_( $env . 'sh .githooks/check-commit-range ' . implode( ' ', array_map( 'escapeshellarg', $args ) ) . ' 2>&1' );
    }

    private function commit( $email, $subject )
    {
        static $n = 0;
        $n++;
        file_put_contents( $this->repo . '/file' . $n . '.txt', $n . "\n" );
        $this->git( 'add -A' );
        $this->git( '-c user.name=Tester -c user.email=' . escapeshellarg( $email ) . ' commit -q --no-verify -m ' . escapeshellarg( $subject ) );
    }

    private function git( $args )
    {
        list( $rc, $out ) = $this->run_( 'git -c init.defaultBranch=main -c commit.gpgsign=false ' . $args . ' 2>&1' );
        $this->assertSame( 0, $rc, "git $args: $out" );
        return trim( $out );
    }

    private function run_( $command )
    {
        $cwd = getcwd();
        chdir( $this->repo );
        exec( 'TMPDIR=' . escapeshellarg( $this->repo ) . ' ' . $command, $out, $rc );
        chdir( $cwd );
        return array( $rc, implode( "\n", $out ) );
    }
}
