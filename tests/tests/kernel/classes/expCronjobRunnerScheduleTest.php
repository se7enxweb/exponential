<?php
/**
 * File containing the expCronjobRunnerScheduleTest class.
 *
 * What the cronjobs page says about a part's schedule: the five fields read from a
 * crontab line, the next time they come round, the schedule in words, the command
 * that runs a part or one script from a shell, and a script's own @description.
 * No database and no cronjob is run.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once 'kernel/setup/expcronjobrunner.php';

class expCronjobRunnerScheduleTest extends ezpTestCase
{
    private $timezone;

    protected function setUp(): void
    {
        parent::setUp();
        $this->timezone = date_default_timezone_get();
        date_default_timezone_set( 'UTC' );
    }

    protected function tearDown(): void
    {
        date_default_timezone_set( $this->timezone );
        parent::tearDown();
    }

    private function at( $text )
    {
        return strtotime( $text . ' UTC' );
    }

    public function testScheduleOfACrontabLine()
    {
        $this->assertSame( '*/5 * * * *', expCronjobRunner::scheduleOfLine( '*/5 * * * * cd /var/www && php runcronjobs.php -s site frequent >/dev/null 2>&1' ) );
        $this->assertSame( '0 * * * *', expCronjobRunner::scheduleOfLine( '@hourly cd /var/www && php runcronjobs.php' ) );
        $this->assertSame( '0 0 * * *', expCronjobRunner::scheduleOfLine( '@daily php runcronjobs.php' ) );
        $this->assertFalse( expCronjobRunner::scheduleOfLine( '@reboot php runcronjobs.php' ) );
        $this->assertFalse( expCronjobRunner::scheduleOfLine( 'MAILTO=root' ) );
    }

    public function testNextRunOfStepsAndFixedTimes()
    {
        $from = $this->at( '2026-10-05 10:02:30' );
        $this->assertSame( $this->at( '2026-10-05 10:05:00' ), expCronjobRunner::nextRun( '*/5 * * * *', $from ) );
        $this->assertSame( $this->at( '2026-10-05 10:15:00' ), expCronjobRunner::nextRun( '*/15 * * * *', $from ) );
        $this->assertSame( $this->at( '2026-10-05 10:17:00' ), expCronjobRunner::nextRun( '17 * * * *', $from ) );
        $this->assertSame( $this->at( '2026-10-06 03:30:00' ), expCronjobRunner::nextRun( '30 3 * * *', $from ) );
        $this->assertSame( $this->at( '2026-10-05 10:03:00' ), expCronjobRunner::nextRun( '* * * * *', $from ), 'the next whole minute' );
    }

    public function testNextRunNeverReturnsTheMomentItself()
    {
        $from = $this->at( '2026-10-05 10:05:00' );
        $this->assertSame( $this->at( '2026-10-05 10:10:00' ), expCronjobRunner::nextRun( '*/5 * * * *', $from ) );
    }

    public function testNextRunOfWeekdaysMonthsAndNames()
    {
        // 2026-10-05 is a Monday
        $from = $this->at( '2026-10-05 12:00:00' );
        $this->assertSame( $this->at( '2026-10-11 00:00:00' ), expCronjobRunner::nextRun( '0 0 * * 0', $from ), 'Sunday as 0' );
        $this->assertSame( $this->at( '2026-10-11 00:00:00' ), expCronjobRunner::nextRun( '0 0 * * 7', $from ), 'Sunday as 7' );
        $this->assertSame( $this->at( '2026-10-07 08:00:00' ), expCronjobRunner::nextRun( '0 8 * * wed', $from ) );
        $this->assertSame( $this->at( '2027-01-01 00:00:00' ), expCronjobRunner::nextRun( '0 0 1 jan *', $from ) );
        $this->assertSame( $this->at( '2026-10-06 09:00:00' ), expCronjobRunner::nextRun( '0 9-17/4 * * 1-5', $this->at( '2026-10-05 18:00:00' ) ), '9, 13 and 17 on weekdays' );
        $this->assertSame( $this->at( '2026-10-05 13:00:00' ), expCronjobRunner::nextRun( '0 9-17/4 * * mon-fri', $this->at( '2026-10-05 10:00:00' ) ) );
    }

    public function testNextRunWithDayOfMonthAndWeekdayMatchesEither()
    {
        // the 15th, or any Friday: Friday the 9th comes first
        $from = $this->at( '2026-10-05 12:00:00' );
        $this->assertSame( $this->at( '2026-10-09 00:00:00' ), expCronjobRunner::nextRun( '0 0 15 * 5', $from ) );
    }

    public function testNextRunOfLeapDayAndUnreadableSchedules()
    {
        $this->assertSame( $this->at( '2028-02-29 00:00:00' ), expCronjobRunner::nextRun( '0 0 29 2 *', $this->at( '2026-10-05 12:00:00' ) ) );
        $this->assertFalse( expCronjobRunner::nextRun( '0 0 31 2 *', $this->at( '2026-10-05 12:00:00' ) ), 'never comes round' );
        $this->assertFalse( expCronjobRunner::nextRun( '61 * * * *' ) );
        $this->assertFalse( expCronjobRunner::nextRun( '* * *' ) );
        $this->assertFalse( expCronjobRunner::nextRun( '*/0 * * * *' ) );
        $this->assertFalse( expCronjobRunner::nextRun( 'x * * * *' ) );
    }

    public function testScheduleInWords()
    {
        $this->assertSame( 'Every 5 minutes', expCronjobRunner::describeSchedule( '*/5 * * * *' ) );
        $this->assertSame( 'Every minute', expCronjobRunner::describeSchedule( '* * * * *' ) );
        $this->assertSame( 'Every hour at minute 17', expCronjobRunner::describeSchedule( '17 * * * *' ) );
        $this->assertSame( 'Every 6 hours at minute 0', expCronjobRunner::describeSchedule( '0 */6 * * *' ) );
        $this->assertSame( 'Every day at 03:30', expCronjobRunner::describeSchedule( '30 3 * * *' ) );
        $this->assertSame( '0 8 * * 1-5', expCronjobRunner::describeSchedule( '0 8 * * 1-5' ), 'anything else stays the expression' );
    }

    public function testCommandLineOfAPartAndOfAScript()
    {
        $root = expCronjobRunner::installationRoot();
        $global = expCronjobRunner::commandLine( 'global', 'site' );
        $this->assertStringStartsWith( 'cd ' . $root . ' && ', $global );
        $this->assertStringEndsWith( ' runcronjobs.php -s site', $global, 'the global part is run by naming no part' );
        $this->assertStringEndsWith( ' runcronjobs.php -s site frequent', expCronjobRunner::commandLine( 'frequent', 'site' ) );
        $this->assertStringEndsWith( ' runcronjobs.php -s site --script=workflow.php', expCronjobRunner::commandLine( 'frequent', 'site', 'workflow.php' ) );
    }

    public function testScriptDescriptionFromItsHeaderOrItsCodeFile()
    {
        $this->assertSame( 'Process scheduled unpublish actions on content objects',
                           expCronjobRunner::scriptDescription( 'cronjobs/unpublish.php' ) );
        $this->assertSame( '', expCronjobRunner::scriptDescription( false ) );
        $this->assertSame( '', expCronjobRunner::scriptDescription( 'cronjobs/no-such-script.php' ) );
    }

    /**
     * The crontab lines the installation guide recommends append to a log (">> var/log/... 2>&1"): the redirection
     * was read as the part name, so the global part showed "Not scheduled".
     */
    public function testPartOfCrontabLineIgnoresRedirectionsAndChains()
    {
        $cd = '*/5 * * * * cd /path/to/installation && php ';
        $this->assertSame( 'frequent', expCronjobRunner::partOfCrontabLine( $cd . 'runcronjobs.php -q -s site frequent >> var/log/cron-frequent.log 2>&1' ) );
        $this->assertSame( 'global', expCronjobRunner::partOfCrontabLine( $cd . 'runcronjobs.php -q -s site >> var/log/cron-default.log 2>&1' ) );
        $this->assertSame( 'global', expCronjobRunner::partOfCrontabLine( $cd . 'runcronjobs.php -q -s site > /dev/null 2>&1' ) );
        $this->assertSame( 'global', expCronjobRunner::partOfCrontabLine( $cd . 'runcronjobs.php -q --siteaccess site 2>&1 | logger -t cron' ) );
        $this->assertSame( 'infrequent', expCronjobRunner::partOfCrontabLine( $cd . 'runcronjobs.php -s site infrequent; echo done' ) );
        $this->assertSame( 'cache_cleanup', expCronjobRunner::partOfCrontabLine( $cd . 'runcronjobs.php cache_cleanup&>>var/log/c.log' ) );
        $this->assertSame( 'global', expCronjobRunner::partOfCrontabLine( $cd . 'runcronjobs.php' ) );
    }

    public function testASingleScriptLineSchedulesNoPart()
    {
        $this->assertNull( expCronjobRunner::partOfCrontabLine( '50 3 * * * cd /x && php runcronjobs.php -q -s site --script=session_gc.php >> var/log/c.log 2>&1' ) );
        $this->assertNull( expCronjobRunner::partOfCrontabLine( '* * * * * php bin/php/ezcache.php --clear-all' ) );
    }
}
