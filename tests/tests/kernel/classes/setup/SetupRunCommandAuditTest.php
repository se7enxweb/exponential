<?php
/**
 * Switching maintenance on clears the response caches with a command of its own (exp:velocity cache clear). Started
 * by a setup run, that command works with the settings of before the installation, and its audit entry looked the
 * user up in the database they name: every installation logged failed connections. The command now carries the
 * setup run's id (EXP_SETUP_RUN) and looks no user up. No database: the "console" is a stand-in under var/tmp that
 * records what it was given.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SetupRunCommandAuditTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $root;

    /** @var ReflectionProperty */
    private $run;

    /** @var mixed */
    private $runBefore;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 5 ) . '/var/tmp/setup-run-command-audit-test-' . getmypid() . '-' . mt_rand();
        mkdir( $this->root, 0775, true );
        file_put_contents( $this->root . '/console', "<?php\nfile_put_contents( __DIR__ . '/given.txt', var_export( getenv( 'EXP_SETUP_RUN' ), true ) . ' ' . implode( ' ', array_slice( \$argv, 1 ) ) );\n" );
        $this->run = new ReflectionProperty( 'expSetupLog', 'run' );
        $this->run->setAccessible( true );
        $this->runBefore = $this->run->getValue();
    }

    protected function tearDown(): void
    {
        $this->run->setValue( null, $this->runBefore );
        foreach ( array( 'console', 'given.txt' ) as $file )
            @unlink( $this->root . '/' . $file );
        @rmdir( $this->root );
        putenv( 'EXP_SETUP_RUN' );
    }

    public function testTheCacheClearOfASetupRunCarriesItsId()
    {
        if ( !function_exists( 'exec' ) )
            $this->markTestSkipped( 'exec() is disabled' );
        $this->run->setValue( null, array( 'id' => '20261012-120000-abcdef', 'finished' => false, 'current' => null ) );
        expMaintenance::clearResponseCaches( $this->root );
        $this->assertSame( "'20261012-120000-abcdef' exp:velocity cache clear --allow-root-user", file_get_contents( $this->root . '/given.txt' ) );
    }

    public function testTheCacheClearOutsideASetupRunCarriesNoId()
    {
        if ( !function_exists( 'exec' ) )
            $this->markTestSkipped( 'exec() is disabled' );
        putenv( 'EXP_SETUP_RUN' );
        $this->run->setValue( null, null );
        expMaintenance::clearResponseCaches( $this->root );
        $this->assertSame( 'false exp:velocity cache clear --allow-root-user', file_get_contents( $this->root . '/given.txt' ) );
    }

    public function testACommandStartedByASetupRunLooksNoUserUp()
    {
        $this->run->setValue( null, null );
        $canLookUp = new ReflectionMethod( 'expAudit', 'canLookUp' );
        $canLookUp->setAccessible( true );
        putenv( 'EXP_SETUP_RUN' );
        $without = $canLookUp->invoke( null );
        putenv( 'EXP_SETUP_RUN=20261012-120000-abcdef' );
        $this->assertFalse( $canLookUp->invoke( null ) );
        putenv( 'EXP_SETUP_RUN=' );
        $this->assertSame( $without, $canLookUp->invoke( null ), 'an empty value is no setup run' );
    }
}
