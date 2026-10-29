<?php
/**
 * The installers only install with --apply: exp:install (bin/php/install.php) and exp:kickstarter run
 * (bin/php/kickstarter.php, expKickstarter). Without --apply a run is the dry run that shows the plan and checks the
 * database and the remote packages. No database and no installation: the option rules, the plan and the code are
 * checked, and the bin/php scripts are run only with options that end them with exit code 2.
 *
 *  AI-01 - The run options: --apply with --dry-run and --force without --apply are refused, the dry run is the default
 *  AI-02 - The plan names the database, says plainly that DatabaseAction=remove drops it, and shows no password
 *  AI-03 - expKickstarter checks the options before initialize() and confirms the CreateSites step only with --apply
 *  AI-04 - exp:install checks the options before it writes kickstart.ini and hands --apply on only when given
 *  AI-05 - bin/php/install.php and bin/php/kickstarter.php run exit 2 for --apply --dry-run and for --force alone
 *  AI-06 - The demo installers in doc/eng-us/examples/demo-mode pass --apply to every installation they run
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expApplyFlagCommandsTest.php';

class expApplyFlagInstallerCommandsTest extends PHPUnit\Framework\TestCase
{
    public function testTheRunOptionsMakeTheDryRunTheDefault()
    {
        $this->assertNull( expKickstarter::applyProblem( array() ) );
        $this->assertNull( expKickstarter::applyProblem( array( 'dry-run' => true ) ) );
        $this->assertNull( expKickstarter::applyProblem( array( 'apply' => true ) ) );
        $this->assertNull( expKickstarter::applyProblem( array( 'apply' => true, 'force' => true ) ), '--apply --force: as scripts add --apply' );
        $this->assertNull( expKickstarter::applyProblem( array( 'list-steps' => true, 'force' => true ) ), 'listing writes nothing' );
        $this->assertStringContainsString( 'exclude each other', (string)expKickstarter::applyProblem( array( 'apply' => true, 'dry-run' => true ) ) );
        $this->assertStringContainsString( '--force no longer installs on its own', (string)expKickstarter::applyProblem( array( 'force' => true ) ) );

        $options = expKickstarter::withApplyDefault( expKickstarter::parseRunOptions( array( '--allow-root-user' ) ) );
        $this->assertTrue( (bool)$options['dry-run'], 'no flag: the dry run' );
        $this->assertFalse( $options['apply'] );
        $options = expKickstarter::withApplyDefault( expKickstarter::parseRunOptions( array( '--apply' ) ) );
        $this->assertTrue( $options['apply'] );
        $this->assertFalse( (bool)$options['dry-run'], '--apply: the installation' );
        $options = expKickstarter::withApplyDefault( expKickstarter::parseRunOptions( array( '--list-steps' ) ) );
        $this->assertFalse( (bool)$options['dry-run'], '--list-steps only lists' );
    }

    public function testThePlanSaysWhatWouldBeDroppedAndShowsNoPassword()
    {
        $ini = new expApplyFlagInstallerFakeIni( array(
            'database_choice' => array( 'Type' => 'mysqli' ),
            'database_init' => array( 'Server' => 'db.local', 'Port' => '3306', 'Database' => 'site', 'User' => 'exp', 'Password' => 'Secret-db-1234' ),
            'site_types' => array( 'Site_package' => 'sevenx_multisite' ),
            'language_options' => array( 'Primary' => 'eng-US', 'Languages' => array( 'ger-DE' ) ),
            'site_access' => array( 'Access' => 'url' ),
            'site_details' => array( 'Database' => 'site', 'DatabaseAction' => 'remove', 'Access' => 'site', 'AdminAccess' => 'admin', 'EditorAccess' => 'editor' ),
            'site_admin' => array( 'Email' => 'admin@example.com', 'Password' => 'Secret-admin-1234' ),
        ) );
        $text = implode( "\n", expKickstarter::planLines( $ini ) );
        $this->assertStringContainsString( 'mysqli site at db.local:3306, user exp', $text );
        $this->assertStringContainsString( 'would DROP every table of site', $text );
        $this->assertStringContainsString( 'sevenx_multisite, eng-US + ger-DE', $text );
        $this->assertStringContainsString( 'site, admin, editor', $text );
        $this->assertStringContainsString( 'settings/override/site.ini.append.php', $text );
        $this->assertStringNotContainsString( 'Secret-', $text, 'no password in the plan' );

        $ini = new expApplyFlagInstallerFakeIni( array( 'site_details' => array( 'Database' => 'site', 'DatabaseAction' => 'skip' ) ) );
        $text = implode( "\n", expKickstarter::planLines( $ini ) );
        $this->assertStringNotContainsString( 'DROP', $text );
        $this->assertStringContainsString( 'without emptying it first', $text );
    }

    public function testTheKickstarterChecksBeforeInitializeAndInstallsOnlyWithApply()
    {
        $code = file_get_contents( eZSys::rootDir() . '/kernel/classes/expkickstarter.php' );
        $check = strpos( $code, '$problem = self::applyProblem( $this->options );' );
        $initialize = strpos( $code, '$this->script->initialize();' );
        $this->assertNotFalse( $check );
        $this->assertLessThan( $initialize, $check, 'the usage error comes before any database access' );
        $this->assertStringContainsString( '$this->script->shutdown( 2 );', $code );
        $this->assertStringContainsString( "\$this->force = !empty( \$this->options['apply'] );", $code );
        $this->assertStringNotContainsString( "\$this->force = !empty( \$this->options['force'] );", $code, '--force alone does not install' );
        // the CreateSites step, the one that empties the database and installs, needs that confirmation
        $this->assertMatchesRegularExpression( '/\$stopIndex >= \$createSitesIndex && !\$this->force \)/', $code );
        $this->assertStringContainsString( '(dry run, nothing changed; --apply writes)', $code );
        $this->assertStringContainsString( "'[apply][start-step:]", $code );
    }

    public function testExpInstallChecksBeforeItWritesAndHandsApplyOn()
    {
        $script = file_get_contents( eZSys::rootDir() . '/bin/php/install.php' );
        $check = strpos( $script, '$apply = \\Exponential\\Runnable\\Command::applyRequested( $flags );' );
        $this->assertNotFalse( $check );
        $this->assertLessThan( strpos( $script, 'file_put_contents( $kickstart' ), $check );
        $this->assertLessThan( strpos( $script, '@rename( $kickstart' ), $check );
        $this->assertStringContainsString( "\$flags['dry-run'] = !\$apply;", $script );
        $this->assertStringContainsString( 'exit( 2 );', $script );
        $class = file_get_contents( eZSys::rootDir() . '/kernel/private/classes/commands/install.php' );
        $this->assertStringContainsString( "\$forward = \$flags['dry-run'] ? array( '--dry-run' ) : array( '--apply' );", $class );
        $this->assertStringNotContainsString( "array( '--force' )", $class );
    }

    public static function usageErrors()
    {
        return array(
            'install, both' => array( array( 'bin/php/install.php', '--apply', '--dry-run' ), 'exclude each other' ),
            'install, --force alone' => array( array( 'bin/php/install.php', '--force' ), '--force no longer installs on its own' ),
            'kickstarter run, both' => array( array( 'bin/php/kickstarter.php', 'run', '--apply', '--dry-run' ), 'exclude each other' ),
            'kickstarter run, --force alone' => array( array( 'bin/php/kickstarter.php', 'run', '--force' ), '--force no longer installs on its own' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'usageErrors' )]
    public function testUsageErrorsExitTwo( array $arguments, $message )
    {
        $line = array_merge( array( PHP_BINARY ), $arguments, array( '--allow-root-user' ) );
        // EXP_KICKSTART_LOG=0: no log rotation in var/log for a run that ends at once
        $process = proc_open( $line, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, eZSys::rootDir(),
                              array( 'PATH' => getenv( 'PATH' ) ?: '/usr/bin:/bin', 'EXP_KICKSTART_LOG' => '0' ) );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $this->assertSame( 2, proc_close( $process ), $out );
        $this->assertStringContainsString( $message, $out );
    }

    public function testTheDemoInstallersPassApply()
    {
        $found = 0;
        foreach ( glob( eZSys::rootDir() . '/doc/eng-us/examples/demo-mode/*.sh' ) as $file )
        {
            foreach ( file( $file ) as $number => $line )
            {
                // the lines that run PHP with an installer (not comments, messages or the fail texts)
                if ( !preg_match( '#^\s*("\$PHP"|\$PHP|php[0-9.]*)\s+(bin/php/install\.php|bin/php/console exp:install|bin/php/kickstarter\.php run|bin/php/console exp:kickstarter run)#', $line ) )
                    continue;
                $found++;
                $this->assertStringContainsString( '--apply', $line, basename( $file ) . ':' . ( $number + 1 ) );
            }
        }
        $this->assertGreaterThanOrEqual( 2, $found, 'the two demo installers run exp:install' );
    }
}

/** kickstart.ini as planLines() reads it: hasVariable() and variable(). */
class expApplyFlagInstallerFakeIni
{
    private $groups;

    public function __construct( array $groups )
    {
        $this->groups = $groups;
    }

    public function hasVariable( $group, $name )
    {
        return isset( $this->groups[$group][$name] );
    }

    public function variable( $group, $name )
    {
        return $this->groups[$group][$name];
    }
}
