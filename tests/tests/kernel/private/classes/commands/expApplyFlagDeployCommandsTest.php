<?php
/**
 * The server commands only change something with --apply: preload, and the commands of exp:velocity, exp:webserver
 * and exp:frankenphp that write (config set|unset, cache clear, layout migrate, site|conf|mod enable|disable, ssl renew,
 * watchdog install|remove, install, webserver configure). Process control (start, stop, restart, graceful, kill,
 * status, watchdog run) and exp:velocity deploy run as before, and so do ezpgenerateautoloads and exp:cache, which act
 * at once like build tools (owner decision 2026-10-29). No database and no server is touched: the code is read, and
 * the bin/php scripts are run with both flags, which ends them with exit code 2 before anything is read.
 *
 *  AD-01 - Each converted command reads --apply and --dry-run with applyRequested() and exits 2 for both
 *  AD-02 - Every write call sits behind the dry-run check
 *  AD-03 - ezpgenerateautoloads and exp:cache keep writing without --apply
 *  AD-04 - The callers pass --apply: maintenance mode, the command line Setup > Preload shows; exp:velocity deploy
 *          keeps running the autoloads without it
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expApplyFlagCommandsTest.php';

class expApplyFlagDeployCommandsTest extends PHPUnit\Framework\TestCase
{
    /** bin/php scripts and the arguments that reach a command that writes, run with both flags */
    public static function bothFlags()
    {
        return array(
            'preload' => array( 'preload', array() ),
            'velocity cache clear' => array( 'velocity', array( 'cache', 'clear' ) ),
            'velocity config set' => array( 'velocity', array( 'config', 'set', 'ServerSettings', 'Port', '9999' ) ),
            'webserver configure' => array( 'webserver', array( 'configure' ) ),
            'frankenphp install' => array( 'frankenphp', array( 'install' ) ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'bothFlags' )]
    public function testBothFlagsAreAUsageError( $name, array $arguments )
    {
        list( $code, $out ) = expApplyFlagCommandsTest::runCommand( $name, array_merge( $arguments, array( '--apply', '--dry-run' ) ) );
        $this->assertSame( 2, $code, $out );
        $this->assertStringContainsString( '--apply and --dry-run exclude each other', $out );
    }

    /** The commands and their write calls: within the fourteen lines before each, a condition on $dryRun */
    public static function writes()
    {
        return array(
            'preload' => array( 'preload', array( '\\expPreloadJob::execute(' ) ),
            'velocity' => array( 'velocity', array( '$config->set(', '$config->unsetVariable(', '$velocity->migrateLayout()',
                                                    '->toggle(', '$watchdog->$action()', '$velocity->install(',
                                                    '$velocity->clearCache()' ) ),
            'webserver' => array( 'webserver', array( 'webserverConfigure( $options, $dryRun )' ) ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'writes' )]
    public function testEveryWriteSitsBehindTheDryRunCheck( $name, array $writes )
    {
        $code = expApplyFlagCommandsTest::code( $name );
        $this->assertStringContainsString( '[apply]', $code, "$name takes --apply" );
        $this->assertStringContainsString( 'self::applyRequested(', $code );
        $this->assertStringContainsString( '--apply and --dry-run exclude each other', $code );
        $this->assertStringContainsString( '(dry run, nothing changed; --apply writes)', $code );
        $lines = explode( "\n", $code );
        $found = 0;
        foreach ( $lines as $number => $line )
        {
            foreach ( $writes as $write )
            {
                if ( strpos( $line, $write ) === false )
                    continue;
                $found++;
                if ( $write === 'webserverConfigure( $options, $dryRun )' )
                    continue;
                $before = implode( "\n", array_slice( $lines, max( 0, $number - 14 ), min( 14, $number ) ) );
                $this->assertMatchesRegularExpression( '/if \( !?\$dryRun\b/', $before, "$name.php:" . ( $number + 1 ) . " writes outside a dry-run check" );
            }
        }
        $this->assertGreaterThanOrEqual( count( $writes ), $found, "a write call of $name was not found" );
        if ( $name === 'webserver' )
            $this->assertStringContainsString( "\$dryRun = \$verb === 'configure' ? !\$apply : !empty( \$options['dry-run'] );", $code );
    }

    /** exp:velocity accepts --dry-run with the commands that write and deploy, refuses it with process control */
    public function testVelocityKeepsProcessControlAsItWas()
    {
        $code = expApplyFlagCommandsTest::code( 'velocity' );
        $this->assertStringContainsString( "\$writers = array( 'config', 'cache', 'layout', 'site', 'conf', 'mod', 'ssl', 'watchdog', 'install', 'deploy' );", $code );
        foreach ( array( 'start', 'stop', 'restart', 'graceful', 'kill', 'status' ) as $verb )
            $this->assertStringNotContainsString( "'$verb'", substr( $code, strpos( $code, '$writers = array(' ), 120 ) );
        // ssl renew runs only with --apply (ssl show reads); ext and ctl pass on to the engine's own tool as before
        $this->assertStringContainsString( "if ( !\$dryRun || \$action === 'show' )\n                    \$script->shutdown( \$velocity->ctl( \$args ) );", $code );
        // deploy keeps its own --dry-run and runs without a flag
        $this->assertStringContainsString( "'dry-run'      => !empty( \$options['dry-run'] ),", $code );
    }

    /** ezpgenerateautoloads and exp:cache act at once, as before: no --apply, --dry-run is what keeps them from writing */
    public function testAutoloadsAndCacheKeepWritingWithoutApply()
    {
        $autoloads = expApplyFlagCommandsTest::code( 'ezpgenerateautoloads' );
        $this->assertStringNotContainsString( 'applyRequested', $autoloads );
        $this->assertStringContainsString( '$autoloadOptions->writeFiles = !$dryrunOption->value;', $autoloads );
        $cache = expApplyFlagCommandsTest::code( 'cache' );
        $this->assertStringNotContainsString( 'applyRequested', $cache );
        $this->assertStringContainsString( "\$dryRun = (bool)\$options['dry-run'];", $cache );
    }

    /** Every caller that runs one of the converted commands passes --apply; the autoload and cache callers do not */
    public function testTheCallersPassApply()
    {
        $root = eZSys::rootDir();
        $deploy = file_get_contents( $root . '/kernel/classes/expvelocitydeploy.php' );
        $this->assertStringContainsString( "array( 'bin/php/ezpgenerateautoloads.php', '-e', '-q' )", $deploy );
        $this->assertStringContainsString( "array( 'bin/php/ezpgenerateautoloads.php', '-k', '-q', '--exclude='", $deploy );
        // the Velocity cache step clears in-process ($velocity->clearCache()); the line it shows names --apply
        $this->assertStringContainsString( "'command' => 'exp:velocity cache clear --apply'", $deploy );

        $this->assertStringContainsString( "exp:velocity cache clear --apply --allow-root-user", file_get_contents( $root . '/kernel/classes/expmaintenance.php' ) );
        $this->assertStringContainsString( "'php bin/php/preload.php', '--apply'", file_get_contents( $root . '/kernel/classes/exppreloadjob.php' ) );

        // kernel and lib code that runs preload or a writing Velocity command
        foreach ( array( 'kernel', 'lib' ) as $dir )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/' . $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( substr( $file->getFilename(), -4 ) !== '.php' || strpos( $file->getPathname(), '/commands/' ) !== false )
                    continue;
                foreach ( explode( "\n", file_get_contents( $file->getPathname() ) ) as $number => $line )
                {
                    if ( preg_match( "/'bin\\/php\\/preload\\.php'|exp:velocity cache clear --allow-root-user/", $line ) )
                        $this->assertStringContainsString( '--apply', $line, $file->getPathname() . ':' . ( $number + 1 ) );
                }
            }
        }
    }
}
