<?php
/**
 * The filter cronjob/part/run of runcronjobs.php (Exponential\Command\Kernel\Runcronjobs::partMayRun()): a listener
 * can leave a cronjob part out before its scripts run, for instance while a release is deployed.
 *
 *  - Without listeners every part runs.
 *  - The listeners get ( true, part, siteaccess, scripts, single ); the part runs only when the answer is true
 *    itself, anything else (false, null, 1, 'yes') leaves it out. A single script run with --script is asked with
 *    the part '' and single true.
 *  - Listeners[]=cronjob/part/run@<callback> of [RunnableSettings] are attached for it.
 *  - run() asks the filter after it has chosen the scripts and before the first one runs, and ends without error
 *    when the part is left out.
 *
 * No database. Uses ezpEvent and eZINI (autoloaded); ezpEvent is reset around each test.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group runnable
 */

/** Runcronjobs with the settings of the fixture, which name ezpTestPartRunListener */
class ezpTestPartRunCronjobs extends \Exponential\Command\Kernel\Runcronjobs
{
    public static function settings( $rootDir = 'settings' )
    {
        return parent::settings( 'tests/tests/kernel/classes/runnable/fixtures/cronjobpartrun' );
    }
}

/** Runcronjobs with settings that name no listener, whatever the local installation has */
class ezpTestPartRunPlainCronjobs extends \Exponential\Command\Kernel\Runcronjobs
{
    public static function settings( $rootDir = 'settings' )
    {
        return parent::settings( 'tests/tests/kernel/classes/runnable/fixtures/cronjobpartrun-none' );
    }
}

/** The listener the fixture names: leaves out the parts listed in $stopped */
class ezpTestPartRunListener
{
    public static $calls = array();
    public static $stopped = array();

    public static function decide( $run, $part, $siteaccess, $scripts, $single )
    {
        self::$calls[] = array( $run, $part, $siteaccess, $scripts, $single );
        return in_array( $part, self::$stopped, true ) ? false : $run;
    }
}

class CronjobPartRunFilterTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        if ( !class_exists( 'ezpEvent' ) || !class_exists( 'eZINI' ) ||
             !class_exists( '\Exponential\Command\Kernel\Runcronjobs' ) )
            $this->markTestSkipped( 'ezpEvent, eZINI and the runcronjobs command are not autoloadable here' );
        ezpEvent::resetInstance();
        ezpTestPartRunListener::$calls = array();
        ezpTestPartRunListener::$stopped = array();
    }

    protected function tearDown(): void
    {
        if ( class_exists( 'ezpEvent', false ) )
            ezpEvent::resetInstance();
    }

    public function testEveryPartRunsWithoutListeners()
    {
        $this->assertSame( 'cronjob/part/run', \Exponential\Command\Kernel\Runcronjobs::PART_RUN_EVENT );
        $this->assertTrue( ezpTestPartRunPlainCronjobs::partMayRun( 'frequent', 'eng', array( 'notification.php' ) ) );
        $this->assertTrue( ezpTestPartRunPlainCronjobs::partMayRun( '', 'eng', array( 'workflow.php' ) ) );
        $this->assertTrue( ezpTestPartRunPlainCronjobs::partMayRun( '', 'eng', array( 'notification.php' ), true ) );
    }

    public function testListenerGetsThePartAndCanLeaveItOut()
    {
        $calls = array();
        ezpEvent::getInstance()->attach( 'cronjob/part/run', function ( $run, $part, $siteaccess, $scripts, $single ) use ( &$calls ) {
            $calls[] = array( $run, $part, $siteaccess, $scripts, $single );
            return $part === 'frequent' || ( $single && in_array( 'notification.php', $scripts, true ) ) ? false : $run;
        } );

        $this->assertFalse( ezpTestPartRunPlainCronjobs::partMayRun( 'frequent', 'eng', array( 'notification.php' ) ) );
        $this->assertTrue( ezpTestPartRunPlainCronjobs::partMayRun( 'infrequent', 'admin', array( 'linkcheck.php' ) ) );
        $this->assertFalse( ezpTestPartRunPlainCronjobs::partMayRun( '', 'eng', array( 'notification.php' ), true ) );
        $this->assertSame( array(
            array( true, 'frequent', 'eng', array( 'notification.php' ), false ),
            array( true, 'infrequent', 'admin', array( 'linkcheck.php' ), false ),
            array( true, '', 'eng', array( 'notification.php' ), true ),
        ), $calls );
    }

    public function testOnlyTrueRunsThePart()
    {
        $answer = null;
        ezpEvent::getInstance()->attach( 'cronjob/part/run', function () use ( &$answer ) {
            return $answer;
        } );
        foreach ( array( null, 1, 'yes', '1', array( true ), 0, false ) as $answer )
        {
            $this->assertFalse( ezpTestPartRunPlainCronjobs::partMayRun( 'frequent', 'eng', array() ), var_export( $answer, true ) );
        }
        $answer = true;
        $this->assertTrue( ezpTestPartRunPlainCronjobs::partMayRun( 'frequent', 'eng', array() ) );
    }

    public function testSettingsListenerIsAttached()
    {
        ezpTestPartRunListener::$stopped = array( 'frequent' );

        $this->assertFalse( ezpTestPartRunCronjobs::partMayRun( 'frequent', 'eng', array( 'notification.php' ) ) );
        $this->assertTrue( ezpTestPartRunCronjobs::partMayRun( '', 'eng', array( 'workflow.php' ) ) );
        // attached once, not once per question
        $this->assertSame( array(
            array( true, 'frequent', 'eng', array( 'notification.php' ), false ),
            array( true, '', 'eng', array( 'workflow.php' ), false ),
        ), ezpTestPartRunListener::$calls );
    }

    public function testRunAsksTheFilterBeforeTheFirstScript()
    {
        $source = file_get_contents( __DIR__ . '/../../../../../kernel/private/classes/commands/runcronjobs.php' );
        $ask = strpos( $source, 'static::partMayRun(' );
        $this->assertNotFalse( $ask, 'run() asks partMayRun()' );
        $this->assertGreaterThan( strpos( $source, "\$scripts = \$ini->variable( \$scriptGroup, 'Scripts' );" ), $ask, 'after the scripts are chosen' );
        $this->assertLessThan( strpos( $source, 'foreach ( $scripts as $cronScript )' ), $ask, 'before the first script runs' );
        $this->assertMatchesRegularExpression( '/static::partMayRun\([^;]*\)\s*\)\s*\{[^}]*\$script->shutdown\( 0 \);/s', $source,
                                               'a part left out ends without error' );
    }
}
