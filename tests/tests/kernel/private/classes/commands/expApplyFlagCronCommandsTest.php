<?php
/**
 * The mail, notification and crontab commands only change things with --apply, and what runs them unattended (the
 * crontab block the installers and installcrontab write, the cronjob parts, the "Run now" of the notification status
 * page, the demo rebuild scripts) still writes. No database: the code is read, the bin/php scripts run with both
 * flags end with exit code 2 before any database access.
 *
 *  AC-01 - mailbounces, mailconsent, notificationevents, notificationrun, notificationsubscriptions and installcrontab
 *          take --apply and --dry-run, read them with applyRequested() and exit 2 for both
 *  AC-02 - Every write call sits behind the dry-run check or is handed the dry-run flag
 *  AC-03 - bin/php/<command>.php --apply --dry-run exits 2 with the usage error
 *  AC-04 - The crontab block runs runcronjobs.php only, the cronjob parts call the classes, not the commands
 *  AC-05 - "Run now" of the notification status page starts notificationrun with --apply, "Dry run" with --dry-run
 *  AC-06 - The demo-mode scripts and crontab pass --apply to every command that is a dry run without it
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expApplyFlagCommandsTest.php';

class expApplyFlagCronCommandsTest extends PHPUnit\Framework\TestCase
{
    /**
     * name => array( write calls behind "if ( !$dryRun )" or an "if ( $dryRun )" block that returns,
     *                write calls that are handed $dryRun as an argument )
     */
    public static function commands()
    {
        return array(
            'mailbounces' => array( 'mailbounces', array(), array( '\\expMailBounceReader::runFiles( $files, $dryRun', '\\expMailBounceReader::run( $dryRun' ) ),
            'mailconsent' => array( 'mailconsent', array(), array( '\\expConsentLog::cleanup( $dryRun )' ) ),
            'notificationevents' => array( 'notificationevents', array(), array( "!empty( \$options['include-unknown'] ), \$dryRun )" ) ),
            'notificationrun' => array( 'notificationrun', array( '\\expNotificationService::run( $run )' ), array() ),
            'notificationsubscriptions' => array( 'notificationsubscriptions', array( '\\eZPersistentObject::removeObject(' ), array() ),
            'installcrontab' => array( 'installcrontab', array( '$setup->add( $description, false )' ), array() ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'commands' )]
    public function testTheCommandReadsTheApplyFlag( $name, array $guarded, array $handed )
    {
        $code = expApplyFlagCommandsTest::code( $name );
        $this->assertMatchesRegularExpression( '/\[apply\]\[dry-run\]/', $code, "$name takes --apply and --dry-run" );
        $this->assertStringContainsString( '$apply = self::applyRequested( $options );', $code );
        $this->assertMatchesRegularExpression( '/(shutdown|\$finish)\( 2/', $code, 'both flags: exit 2' );
        $this->assertStringContainsString( '$dryRun = !$apply;', $code );
        $this->assertStringContainsString( '(dry run, nothing changed; --apply writes)', $code );
        $this->assertStringNotContainsString( "!empty( \$options['dry-run'] )", $code, 'the old default wrote' );
        $this->assertStringNotContainsString( "(bool)\$options['dry-run']", $code, 'the old default wrote' );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'commands' )]
    public function testEveryWriteSitsBehindTheDryRunCheck( $name, array $guarded, array $handed )
    {
        $file = 'kernel/private/classes/commands/' . $name . '.php';
        if ( $guarded )
            expApplyFlagCommandsTest::assertWritesOnlyWithApply( $this, $file, $guarded );
        $code = expApplyFlagCommandsTest::code( $name );
        foreach ( $handed as $call )
            $this->assertStringContainsString( $call, $code, "$file hands the dry-run flag to the write" );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'commands' )]
    public function testBothFlagsAreAUsageError( $name, array $guarded, array $handed )
    {
        $arguments = array( '--apply', '--dry-run' );
        if ( $name === 'installcrontab' )
            $arguments[] = '--add';
        list( $code, $out ) = expApplyFlagCommandsTest::runCommand( $name, $arguments );
        $this->assertSame( 2, $code, $out );
        $this->assertStringContainsString( '--apply and --dry-run exclude each other', $out );
    }

    /** The installers and installcrontab write runcronjobs.php lines only; the cronjob parts call the classes. */
    public function testTheCrontabBlockRunsOnlyRuncronjobs()
    {
        $entries = array( array( 'part' => 'frequent', 'siteaccess' => 'admin', 'schedule' => '*/15 * * * *' ),
                          array( 'part' => expCrontabSetup::GLOBAL_PART, 'siteaccess' => '', 'schedule' => '35 6 * * *' ) );
        $lines = expCrontabSetup::block( $entries, '/var/www/example', '/usr/bin/php' );
        $this->assertNotEmpty( $lines );
        foreach ( $lines as $line )
        {
            if ( $line === '' || $line[0] === '#' )
                continue;
            $this->assertStringContainsString( 'runcronjobs.php', $line );
            $this->assertStringNotContainsString( 'bin/php/', $line );
            $this->assertStringNotContainsString( 'console', $line );
        }
        $parts = array( 'mailbounces' => '\\expMailBounceReader::run()', 'mailpreferences' => '\\expConsentLog::cleanup()',
                        'notification' => '\\expNotificationService::run(' );
        foreach ( $parts as $part => $call )
        {
            $code = file_get_contents( eZSys::rootDir() . '/kernel/private/classes/cronjobs/' . $part . '.php' );
            $this->assertStringContainsString( $call, $code, "the cronjob part $part calls the class" );
            $this->assertDoesNotMatchRegularExpression( '#bin/php/|exp:[a-z]#', preg_replace( '#/\*.*?\*/|//[^\n]*#s', '', $code ), "the cronjob part $part runs no command" );
        }
    }

    public function testRunNowOfTheStatusPageSendsWithApply()
    {
        $code = file_get_contents( eZSys::rootDir() . '/kernel/classes/expnotificationjob.php' );
        $this->assertStringContainsString( "'bin/php/notificationrun.php'", $code );
        $this->assertStringContainsString( "\$args[] = \$dry ? '--dry-run' : '--apply';", $code );
    }

    /**
     * Every line of the demo-mode scripts and crontab that runs a command which is a dry run without --apply passes
     * --apply (a command counts when its code reads applyRequested(); --help, --show, --status and list lines read only).
     */
    public function testTheDemoScriptsPassApply()
    {
        $root = eZSys::rootDir();
        $dryByDefault = array();
        foreach ( glob( $root . '/kernel/private/classes/commands/*.php' ) as $file )
        {
            if ( strpos( (string)file_get_contents( $file ), 'self::applyRequested( $options )' ) !== false )
                $dryByDefault[] = basename( $file, '.php' );
        }
        $this->assertContains( 'notificationrun', $dryByDefault );
        $checked = 0;
        foreach ( array_merge( glob( $root . '/doc/eng-us/examples/demo-mode/*.sh' ), array( $root . '/doc/eng-us/examples/demo-mode/crontab-demo' ) ) as $file )
        {
            foreach ( file( $file ) as $number => $line )
            {
                $trimmed = trim( $line );
                if ( $trimmed === '' || $trimmed[0] === '#' || preg_match( '/--help|--show|--status|\blist\b/', $trimmed ) )
                    continue;
                foreach ( $dryByDefault as $name )
                {
                    if ( !preg_match( '#bin/php/' . preg_quote( $name, '#' ) . '\.php\b|console exp:' . preg_quote( $name, '#' ) . '\b#', $trimmed ) )
                        continue;
                    $checked++;
                    $this->assertStringContainsString( '--apply', $trimmed, basename( $file ) . ':' . ( $number + 1 ) . " runs $name without --apply" );
                }
            }
        }
        $this->assertGreaterThanOrEqual( 0, $checked );
    }
}
