<?php
/**
 * A starting Velocity server runs from its own name for the engine archive (expVelocity::engineRunArchive()), so a
 * build renamed over dist/engine.phar never changes the archive a running server reads its classes from: the run
 * copy is a hard link (or a copy) of the archive as it was at the start, shared by the instances of one start,
 * never written over, and only the newest ENGINE_RUN_COPIES are kept.
 *
 * Files: a directory below var/tmp of the installation root, removed afterwards.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expVelocityEngineRunArchiveTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        $this->dir = getcwd() . '/var/tmp/engine-run-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0775, true );
    }

    protected function tearDown(): void
    {
        foreach ( glob( $this->dir . '/{,.}*', GLOB_BRACE ) as $f )
        {
            if ( is_file( $f ) )
                unlink( $f );
        }
        rmdir( $this->dir );
    }

    private function velocity()
    {
        return expVelocity::create( 'velocity.ini', 'qbix' );
    }

    public function testServerKeepsItsArchiveWhenABuildIsRenamedOverTheName()
    {
        $archive = $this->dir . '/engine.phar';
        file_put_contents( $archive, 'old build' );
        $run = $this->velocity()->engineRunArchive( $archive );
        $this->assertNotSame( $archive, $run );
        $this->assertStringEndsWith( '.phar', $run );
        $this->assertSame( 'old build', file_get_contents( $run ) );

        // the deploy's build: written beside it and renamed over the name
        file_put_contents( $this->dir . '/.new.tmp.phar', 'new build' );
        rename( $this->dir . '/.new.tmp.phar', $archive );
        $this->assertSame( 'old build', file_get_contents( $run ), 'the running server still reads the build it started with' );
    }

    public function testInstancesOfOneStartShareTheCopyAndItIsNotWrittenOver()
    {
        $archive = $this->dir . '/engine.phar';
        file_put_contents( $archive, 'build one' );
        $velocity = $this->velocity();
        $first = $velocity->engineRunArchive( $archive );
        file_put_contents( $this->dir . '/.next.tmp.phar', 'build two' );
        rename( $this->dir . '/.next.tmp.phar', $archive );
        $second = $velocity->engineRunArchive( $archive );
        if ( $second === $first )
            $this->assertSame( 'build one', file_get_contents( $first ), 'an existing run copy is never written over' );
        else
            $this->assertSame( 'build two', file_get_contents( $second ) );
    }

    public function testOnlyTheNewestRunCopiesAreKept()
    {
        $archive = $this->dir . '/engine.phar';
        file_put_contents( $archive, 'build' );
        for ( $i = 0; $i < expVelocity::ENGINE_RUN_COPIES + 3; ++$i )
            touch( $this->dir . '/.engine-run-2020010100000' . $i . '-1.phar' );
        $run = $this->velocity()->engineRunArchive( $archive );
        $copies = glob( $this->dir . '/.engine-run-*.phar' );
        $this->assertCount( expVelocity::ENGINE_RUN_COPIES, $copies );
        $this->assertContains( $run, $copies );
    }
}
