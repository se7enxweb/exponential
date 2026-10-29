<?php
/**
 * The maintenance and content commands change an installation only with --apply
 * (Exponential\Runnable\Command::applyRequested()): audit, explogs, contentcheck, exphelpcontent (their changing
 * actions), exploremipsumfill, collaborationsampledata, cleanuprss, clusterpurge, ezsubtreecopy, ezsubtreeremove,
 * trashpurge and cleanupversions. No database: the code of each command is read, and the bin/php script is run with
 * both flags, which ends it with exit code 2.
 *
 *  AM-01 - Each command takes --apply and --dry-run, reads them with applyRequested() and exits 2 for both
 *  AM-02 - Every write call sits behind the dry-run check (an "if ( !$dryRun )" just before it, or an earlier
 *          "if ( $dryRun )" / "if ( $this->dryRun() )" block of the same method that returns or skips)
 *  AM-03 - The dry run ends with "(dry run, nothing changed; --apply writes)"
 *  AM-04 - bin/php/<command>.php --apply --dry-run exits 2 with the usage error
 *  AM-05 - The commands that hand the dry run to a class hand it the flag, and the class checks it before writing
 *  AM-06 - The callers that rely on writing pass --apply (kickstarter: build-manuals --queue)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expApplyFlagCommandsTest.php';

class expApplyFlagMaintenanceCommandsTest extends PHPUnit\Framework\TestCase
{
    /**
     * The commands and their write calls in the command file (an empty list: the class checks the flag, AM-05).
     */
    public static function commands()
    {
        return array(
            'audit' => array( 'audit', array( '\\expAudit::checkpoint()', '$this->archiver()->restore(', '$indexer->rebuild(', '$keys->rotatePseudonym()',
                                              '$r = $keys->rotate();', '\\expAuditSinkRegistry::deliverSpools(', '$r = $m->run(' ) ),
            'explogs' => array( 'explogs', array( '$manager->apply( $plan )' ) ),
            'contentcheck' => array( 'contentcheck', array( '\\expContentCheckQuarantine::execute(', '\\expContentCheckFileFix::execute(' ) ),
            'exphelpcontent' => array( 'exphelpcontent', array( '\\expHelpContentProvider::clearCache()' ) ),
            'exploremipsumfill' => array( 'exploremipsumfill', array( '$object->createNewVersionIn(' ) ),
            'collaborationsampledata' => array( 'collaborationsampledata', array( '$data->remove()', '$data->apply()' ) ),
            'cleanuprss' => array( 'cleanuprss', array() ),
            'clusterpurge' => array( 'clusterpurge', array() ),
            'ezsubtreecopy' => array( 'ezsubtreecopy', array( '$copyResult = copyPublishContentObject(' ) ),
            'ezsubtreeremove' => array( 'ezsubtreeremove', array( '\\eZContentObjectTreeNode::removeSubtrees(' ) ),
            'trashpurge' => array( 'trashpurge', array( '\\Exponential\\Service\\Trash::purge(' ) ),
            'cleanupversions' => array( 'cleanupversions', array( '$version->removeThis()' ) ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'commands' )]
    public function testTheCommandReadsTheApplyFlag( $name, array $writes )
    {
        $code = expApplyFlagCommandsTest::code( $name );
        $this->assertMatchesRegularExpression( '/\[apply\]\[dry-run\]/', $code, "$name takes --apply and --dry-run" );
        $this->assertMatchesRegularExpression( '/\$apply = self::applyRequested\( \$(this->)?options \);|\$apply = self::applyRequested\( \$scriptOptions \);/', $code );
        $this->assertMatchesRegularExpression( '/shutdown\( 2 \);/', $code, 'both flags: exit 2' );
        $this->assertStringContainsString( '$dryRun = !$apply;', $code );
        $this->assertStringContainsString( '(dry run, nothing changed; --apply writes)', $code );
        $this->assertStringContainsString( '--apply', substr( $code, 0, 2000 ), 'the header says --apply' );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'commands' )]
    public function testEveryWriteSitsBehindTheDryRunCheck( $name, array $writes )
    {
        if ( !$writes )
        {
            $this->assertTrue( true, 'the class checks the flag (AM-05)' );
            return;
        }
        self::assertWritesOnlyWithApply( $this, 'kernel/private/classes/commands/' . $name . '.php', $writes );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'commands' )]
    public function testBothFlagsAreAUsageError( $name, array $writes )
    {
        list( $code, $out ) = expApplyFlagCommandsTest::runCommand( $name, array( '--apply', '--dry-run' ) );
        $this->assertSame( 2, $code, $out );
        $this->assertStringContainsString( '--apply and --dry-run exclude each other', $out );
    }

    /** AM-05 */
    public function testTheClassesGetTheFlag()
    {
        $root = eZSys::rootDir() . '/';
        $rss = expApplyFlagCommandsTest::code( 'cleanuprss' );
        $this->assertStringContainsString( "array( 'dry-run' => \$dryRun,", $rss );
        $class = file_get_contents( $root . 'kernel/classes/expcleanuprss.php' );
        $this->assertMatchesRegularExpression( '/if \( \$this->dryRun \)\s+return;\s+(\/\/[^\n]*\n\s*)*eZContentObjectTreeNode::removeSubtrees/', $class );

        $purge = expApplyFlagCommandsTest::code( 'clusterpurge' );
        $this->assertStringContainsString( '$purgeHandler->optDryRun = $dryRun;', $purge );
        $this->assertLessThan( strpos( $purge, '$script->initialize();' ), strpos( $purge, '$apply = self::applyRequested( $options );' ),
                               'the flags are checked before the database' );
        $handler = file_get_contents( $root . 'kernel/private/classes/ezscriptclusterpurge.php' );
        $this->assertMatchesRegularExpression( '/if \( \$this->optDryRun == false \)\s*\{\s*self::monitor\( "purge" \);\s*\$fh = eZClusterFileHandler::instance\( \$file \);\s*\$fh->purge\(/', $handler );

        $check = expApplyFlagCommandsTest::code( 'contentcheck' );
        $this->assertStringContainsString( "\$options['dry-run'] = \$dryRun;", $check, 'fix() and storage() read the option' );

        $help = expApplyFlagCommandsTest::code( 'exphelpcontent' );
        $this->assertStringContainsString( "\$common = array( 'dry_run' => \$dryRun,", $help );
        $this->assertStringContainsString( 'queueManuals( $options, $user, $json, $dryRun )', $help );
        foreach ( array( 'install', 'tables', 'classes', 'tree', 'roles', 'seed-glossary', 'seed-topics', 'build-manuals', 'ebooks', 'uninstall' ) as $action )
            $this->assertMatchesRegularExpression( "/const WRITING_ACTIONS = array\([^)]*'$action'/", $help, "$action writes only with --apply" );

        $audit = expApplyFlagCommandsTest::code( 'audit' );
        $this->assertStringContainsString( "return !\$this->apply;", $audit, 'dryRun() is the opposite of --apply' );
        foreach ( array( '->rotate( $this->dryRun()', "'dryRun' => \$this->dryRun()", '->purge( $c, $this->dryRun() )' ) as $passed )
            $this->assertStringContainsString( $passed, $audit );
    }

    /** AM-06 */
    public function testTheCallersPassApply()
    {
        $kickstarter = file_get_contents( eZSys::rootDir() . '/kernel/classes/expkickstarter.php' );
        $this->assertStringContainsString( "'build-manuals', '--queue', '--apply', '-s', 'admin'", $kickstarter );
        $this->assertStringNotContainsString( 'build-manuals -s admin --queue)', $kickstarter );
    }

    /**
     * Every line of $file that holds one of $writes is preceded within three lines by "if ( !$dryRun )", or the
     * method it is in has an "if ( $dryRun )" or "if ( $this->dryRun() )" block before it that returns or continues.
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
                if ( strpos( $before, 'if ( !$dryRun )' ) !== false || strpos( $before, '!$dryRun &&' ) !== false )
                    continue;
                $start = $number;
                while ( $start > 0 && !preg_match( '/^\s*(public |protected |private )?(static )?function /', $lines[$start] ) )
                    $start--;
                $guarded = false;
                for ( $i = $start; $i < $number; $i++ )
                {
                    if ( !in_array( trim( $lines[$i] ), array( 'if ( $dryRun )', 'if ( $this->dryRun() )' ), true ) )
                        continue;
                    $block = implode( "\n", array_slice( $lines, $i, 12 ) );
                    if ( preg_match( '/\b(return|continue)\b/', $block ) )
                        $guarded = true;
                }
                $test->assertTrue( $guarded, "$file:" . ( $number + 1 ) . " writes outside a dry-run check" );
            }
        }
        $test->assertGreaterThan( 0, $found, "no write call found in $file" );
    }
}
