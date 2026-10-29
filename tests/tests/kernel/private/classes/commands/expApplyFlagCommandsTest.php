<?php
/**
 * The commands that change an installation only show what they would change unless --apply is given
 * (Exponential\Runnable\Command::applyRequested()). No database: the code of each command is read, and the bin/php
 * script is run with both flags, which ends it with exit code 2 before any database access.
 *
 *  AF-01 - Each command takes --apply and --dry-run, reads them with applyRequested() and exits 2 for both
 *  AF-02 - Every write call of a command sits behind the dry-run check: without --apply nothing is written
 *  AF-03 - The dry run ends with "PASS: ... (dry run, nothing changed; --apply writes)"
 *  AF-04 - bin/php/<command>.php --apply --dry-run exits 2 with the usage error
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expApplyFlagCommandsTest extends PHPUnit\Framework\TestCase
{
    /**
     * The commands and their write calls: each must sit behind "if ( !$dryRun )" (within the three lines before
     * it), or after an "if ( $dryRun )" block of the same method that returns.
     */
    public static function commands()
    {
        return array(
            'addanonymouspublicpolicies' => array( 'addanonymouspublicpolicies', array( '\\expAnonymousPublicPolicies::apply(' ) ),
            'addexphelpreadpolicy' => array( 'addexphelpreadpolicy', array( '\\expHelpReadPolicyGrant::apply(' ) ),
            'addoauthauthorizepolicy' => array( 'addoauthauthorizepolicy', array( '\\expOAuthAuthorizePolicyGrant::apply(' ) ),
            'adduserapproval' => array( 'adduserapproval', array( '\\expUserApproval::apply(', '\\expUserApprovalSection::apply(' ) ),
            'cleanuppolicies' => array( 'cleanuppolicies', array( '->removeThis()' ) ),
            'adddefaultstates' => array( 'adddefaultstates', array( 'self::apply( $db' ) ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'commands' )]
    public function testTheCommandReadsTheApplyFlag( $name, array $writes )
    {
        $code = self::code( $name );
        $this->assertMatchesRegularExpression( '/"\[apply\]\[dry-run\]/', $code, "$name takes --apply and --dry-run" );
        $this->assertStringContainsString( '$apply = self::applyRequested( $options );', $code );
        $this->assertStringContainsString( '$script->shutdown( 2 );', $code, 'both flags: exit 2' );
        $this->assertStringContainsString( '$dryRun = !$apply;', $code );
        $this->assertStringContainsString( '(dry run, nothing changed; --apply writes)', $code );
        $this->assertStringNotContainsString( "\$dryRun = (bool)\$options['dry-run'];", $code, 'the old default wrote' );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'commands' )]
    public function testEveryWriteSitsBehindTheDryRunCheck( $name, array $writes )
    {
        self::assertWritesOnlyWithApply( $this, 'kernel/private/classes/commands/' . $name . '.php', $writes );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'commands' )]
    public function testBothFlagsAreAUsageError( $name, array $writes )
    {
        list( $code, $out ) = self::runCommand( $name, array( '--apply', '--dry-run' ) );
        $this->assertSame( 2, $code, $out );
        $this->assertStringContainsString( '--apply and --dry-run exclude each other', $out );
    }

    /**
     * The usage error comes before the database: adddefaultstates checks the flags between options() and the
     * script's initialize().
     */
    public function testAdddefaultstatesChecksTheFlagsBeforeInitialize()
    {
        $code = self::code( 'adddefaultstates' );
        $check = strpos( $code, '$apply = self::applyRequested( $options );' );
        $initialize = strpos( $code, '$script->initialize();' );
        $this->assertNotFalse( $check );
        $this->assertNotFalse( $initialize );
        $this->assertLessThan( $initialize, $check );
        // the dry run reports per state group, read only
        $this->assertStringContainsString( 'public static function plan( $db )', $code );
        $plan = substr( $code, strpos( $code, 'public static function plan( $db )' ), strpos( $code, 'public static function apply(' ) - strpos( $code, 'public static function plan( $db )' ) );
        $this->assertStringNotContainsString( 'INSERT', $plan );
        $this->assertStringNotContainsString( '->insert(', $plan );
    }

    /** cleanuppolicies collects the policies first and pages by ID, so a removal never shifts the pages it reads. */
    public function testCleanuppoliciesReadsBeforeItRemoves()
    {
        $code = self::code( 'cleanuppolicies' );
        $this->assertStringContainsString( "array( 'id' => 'asc' )", $code );
        $orphans = substr( $code, strpos( $code, 'public static function orphanPolicies(' ) );
        $this->assertStringNotContainsString( 'removeThis', $orphans );
        $this->assertStringNotContainsString( '$GLOBALS', $code );
    }

    public static function code( $name )
    {
        return file_get_contents( eZSys::rootDir() . '/kernel/private/classes/commands/' . $name . '.php' );
    }

    /**
     * Every line of $file that holds one of $writes is preceded within three lines by "if ( !$dryRun )", or the
     * method it is in has an "if ( $dryRun )" block before it that returns.
     */
    public static function assertWritesOnlyWithApply( PHPUnit\Framework\TestCase $test, $file, array $writes )
    {
        $lines = explode( "\n", file_get_contents( eZSys::rootDir() . '/' . $file ) );
        $found = 0;
        foreach ( $lines as $number => $line )
        {
            foreach ( $writes as $write )
            {
                if ( strpos( $line, $write ) === false )
                    continue;
                $found++;
                $before = implode( "\n", array_slice( $lines, max( 0, $number - 3 ), min( 3, $number ) ) );
                if ( strpos( $before, 'if ( !$dryRun )' ) !== false )
                    continue;
                $start = $number;
                while ( $start > 0 && strpos( $lines[$start], 'function ' ) === false )
                    $start--;
                $guarded = false;
                for ( $i = $start; $i < $number; $i++ )
                {
                    if ( trim( $lines[$i] ) !== 'if ( $dryRun )' )
                        continue;
                    $block = implode( "\n", array_slice( $lines, $i, 8 ) );
                    if ( strpos( $block, 'return;' ) !== false )
                        $guarded = true;
                }
                $test->assertTrue( $guarded, "$file:" . ( $number + 1 ) . " writes outside a dry-run check" );
            }
        }
        $test->assertGreaterThan( 0, $found, "no write call found in $file" );
    }

    /** Runs bin/php/<name>.php in a child process; array( exit code, output ). */
    public static function runCommand( $name, array $arguments )
    {
        $line = array_merge( array( PHP_BINARY, 'bin/php/' . $name . '.php', '--allow-root-user' ), $arguments );
        $process = proc_open( $line, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, eZSys::rootDir(),
                              array( 'PATH' => getenv( 'PATH' ) ?: '/usr/bin:/bin' ) );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        return array( proc_close( $process ), $out );
    }
}
