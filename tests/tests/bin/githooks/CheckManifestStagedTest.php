<?php
/**
 * bin/php/checkmanifest.php --staged, the manifest check of the pre-commit hook.
 *
 * Each test builds a throwaway git repository in its own temporary directory with a copy
 * of the script and of the class it reads the manifest with, and runs the script there:
 * - nothing staged (a commit that only amends its message, git commit --allow-empty) passes;
 *   the script used to read git's empty output as "git is not available" and refuse it;
 * - a staged file listed with its checksum passes, one not listed or with another checksum
 *   fails;
 * - outside a git repository the script still says git is not available.
 *
 * Needs git; skipped without it.
 *
 * Run: php vendor/bin/phpunit tests/tests/bin/githooks/CheckManifestStagedTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class CheckManifestStagedTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    private $repo;

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

        $this->repo = sys_get_temp_dir() . '/exp-check-manifest-staged-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        foreach ( array( 'bin/php', 'kernel/private/classes', 'share', 'lib' ) as $dir )
        {
            mkdir( $this->repo . '/' . $dir, 0700, true );
        }
        copy( self::$root . '/bin/php/checkmanifest.php', $this->repo . '/bin/php/checkmanifest.php' );
        copy( self::$root . '/kernel/private/classes/expfileconsistencyreport.php', $this->repo . '/kernel/private/classes/expfileconsistencyreport.php' );
        file_put_contents( $this->repo . '/lib/one.php', "<?php\n" );
        $this->writeManifest( array( 'lib/one.php' ) );
        $this->git( 'init -q -b main' );
        $this->git( 'add -A' );
        $this->git( '-c user.name=Test -c user.email=test@example.org commit -q --no-verify -m "Added: The test repository"' );
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

    public function testNothingStagedPasses()
    {
        list( $rc, $out ) = $this->check();
        $this->assertSame( 0, $rc, $out );
        $this->assertStringNotContainsString( 'git is not available', $out );
    }

    public function testAStagedFileListedWithItsChecksumPasses()
    {
        file_put_contents( $this->repo . '/lib/one.php', "<?php\n// changed\n" );
        $this->writeManifest( array( 'lib/one.php' ) );
        $this->git( 'add lib/one.php share/filelist.md5' );

        list( $rc, $out ) = $this->check();
        $this->assertSame( 0, $rc, $out );
    }

    public function testAStagedFileNotListedOrStaleFails()
    {
        file_put_contents( $this->repo . '/lib/two.php', "<?php\n" );
        file_put_contents( $this->repo . '/lib/one.php', "<?php\n// changed\n" );
        $this->git( 'add lib/one.php lib/two.php' );

        list( $rc, $out ) = $this->check();
        $this->assertSame( 1, $rc, $out );
        $this->assertStringContainsString( 'lib/two.php is not listed', $out );
        $this->assertStringContainsString( 'lib/one.php has checksum', $out );
    }

    public function testOutsideARepositoryGitIsNotAvailable()
    {
        $this->removeGitDirectory();

        list( $rc, $out ) = $this->check();
        $this->assertSame( 1, $rc, $out );
        $this->assertStringContainsString( 'git is not available', $out );
    }

    private function writeManifest( array $paths )
    {
        $lines = '';
        foreach ( $paths as $path )
        {
            $lines .= md5_file( $this->repo . '/' . $path ) . '  ' . $path . "\n";
        }
        file_put_contents( $this->repo . '/share/filelist.md5', $lines );
    }

    private function removeGitDirectory()
    {
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $this->repo . '/.git', FilesystemIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ( $items as $item )
        {
            $item->isDir() && !$item->isLink() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
        }
        rmdir( $this->repo . '/.git' );
    }

    private function git( $arguments )
    {
        exec( 'cd ' . escapeshellarg( $this->repo ) . ' && git ' . $arguments . ' 2>&1', $out, $rc );
        $this->assertSame( 0, $rc, "git $arguments: " . implode( "\n", $out ) );
        return trim( implode( "\n", $out ) );
    }

    private function check()
    {
        // GIT_DIR and friends from a hook that runs this test must not reach the throwaway repository
        $env = 'env -u GIT_DIR -u GIT_INDEX_FILE -u GIT_WORK_TREE ';
        exec( 'cd ' . escapeshellarg( $this->repo ) . ' && ' . $env . escapeshellarg( PHP_BINARY ) . ' bin/php/checkmanifest.php --staged 2>&1', $out, $rc );
        return array( $rc, implode( "\n", $out ) );
    }
}
