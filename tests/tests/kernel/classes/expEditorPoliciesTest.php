<?php
/**
 * The everyday policies of the Editor role (expEditorPolicies): what the setup step and the upgrade script
 * bin/php/addeditorpolicies.php add, and the over-broad grants the script reports.
 *
 * No database: the role's policies are plain arrays as policiesOf() makes them.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expEditorPoliciesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expEditorPoliciesTest extends PHPUnit\Framework\TestCase
{
    private static function policies( array $names )
    {
        $list = array();
        foreach ( $names as $name )
        {
            list( $module, $function ) = explode( '/', $name );
            $list[] = array( 'module' => $module, 'function' => $function );
        }
        return $list;
    }

    private static function granted( array $plan )
    {
        $names = array();
        foreach ( $plan as $entry )
            if ( $entry['action'] === 'grant' )
                $names[] = $entry['module'] . '/' . $entry['function'];
        return $names;
    }

    /** The Editor role of the site packages, with the Member role of the Editors group alongside. */
    private static function packageEditor()
    {
        return self::policies( array( 'user/login', 'ezoe/*', 'content/create', 'websitetoolbar/use', 'content/edit',
                                      'content/read', 'notification/use', 'uiswitch/language', 'uiswitch/design',
                                      'ezjscore/call', 'content/manage_locations', 'ezodf/*', 'content/diff',
                                      'content/versionread', 'content/versionremove', 'content/remove',
                                      'content/translate', 'rss/feed', 'content/bookmark', 'content/pendinglist',
                                      'content/dashboard', 'content/view_embed', 'oauth/authorize', 'exphelp/read' ) );
    }

    private static function packageMember()
    {
        return self::policies( array( 'content/create', 'content/edit', 'user/selfedit', 'notification/use', 'user/password',
                                      'ezjscore/call', 'content/read', 'tags/view', 'info-collection/read' ) );
    }

    public function testPackageEditorGetsHideTrashReverseRelationsAndMultiUpload()
    {
        $plan = expEditorPolicies::plan( self::packageEditor(), array( 'content', 'user', 'ezmultiupload' ), array(), array(), self::packageMember() );
        $this->assertSame( array( 'content/hide', 'content/restore', 'content/reverserelatedlist', 'ezmultiupload/*' ), self::granted( $plan ) );
        $this->assertCount( count( expEditorPolicies::definitions() ), $plan );
    }

    /** The image editor (expimageeditor/edit) where its module exists; never where it does not. */
    public function testImageEditorWhereTheModuleExists()
    {
        $plan = expEditorPolicies::plan( self::packageEditor(), array( 'expimageeditor' ), array(), array(), self::packageMember() );
        $this->assertContains( 'expimageeditor/edit', self::granted( $plan ) );
        $plan = expEditorPolicies::plan( self::packageEditor(), array(), array(), array(), self::packageMember() );
        $this->assertNotContains( 'expimageeditor/edit', self::granted( $plan ) );
        $plan = expEditorPolicies::plan( array_merge( self::packageEditor(), self::policies( array( 'expimageeditor/*' ) ) ), array( 'expimageeditor' ),
                                         array(), array(), self::packageMember() );
        $this->assertNotContains( 'expimageeditor/edit', self::granted( $plan ) );
    }

    /** Without the Member role alongside (plain_site), the own account policies are added to the Editor role too. */
    public function testOwnAccountPoliciesWhenNoOtherRoleHasThem()
    {
        $plan = expEditorPolicies::plan( self::packageEditor(), array(), array(), array(), array() );
        $this->assertSame( array( 'content/hide', 'content/restore', 'content/reverserelatedlist', 'user/password', 'user/selfedit' ),
                           self::granted( $plan ) );
    }

    /** The base data's content/* covers every content function; ezmultiupload only where the module exists. */
    public function testBaseDataContentStarAndMissingModule()
    {
        $base = self::policies( array( 'content/*', 'ezoe/*', 'user/login', 'uiswitch/language', 'uiswitch/design', 'oauth/authorize', 'exphelp/read' ) );
        $this->assertSame( array( 'user/password', 'user/selfedit', 'notification/use' ),
                           self::granted( expEditorPolicies::plan( $base, array( 'content', 'user' ) ) ) );
        $this->assertSame( array( 'ezmultiupload/*', 'user/password', 'user/selfedit', 'notification/use' ),
                           self::granted( expEditorPolicies::plan( $base, array( 'ezmultiupload' ) ) ) );
    }

    /** A policy in any form counts; a second run adds nothing. */
    public function testExistingPoliciesAreLeftAlone()
    {
        $this->assertTrue( expEditorPolicies::covers( self::policies( array( 'content/*' ) ), 'content', 'hide' ) );
        $this->assertTrue( expEditorPolicies::covers( self::policies( array( '*/*' ) ), 'ezmultiupload', '*' ) );
        $this->assertFalse( expEditorPolicies::covers( self::policies( array( 'content/edit' ) ), 'content', 'hide' ) );
        $first = expEditorPolicies::plan( self::packageEditor(), array( 'ezmultiupload' ), array(), array(), self::packageMember() );
        $after = array_merge( self::packageEditor(), self::policies( self::granted( $first ) ) );
        $this->assertSame( array(), self::granted( expEditorPolicies::plan( $after, array( 'ezmultiupload' ), array(), array(), self::packageMember() ) ) );
    }

    public function testOnlyAndSkip()
    {
        $plan = expEditorPolicies::plan( self::packageEditor(), array( 'ezmultiupload' ), array( 'content/restore' ), array(), self::packageMember() );
        $this->assertSame( array( 'content/restore' ), self::granted( $plan ) );
        $plan = expEditorPolicies::plan( self::packageEditor(), array( 'ezmultiupload' ), array(), array( 'ezmultiupload/*', 'content/hide' ), self::packageMember() );
        $this->assertSame( array( 'content/restore', 'content/reverserelatedlist' ), self::granted( $plan ) );
    }

    /** content/* holds the installation-wide content functions; the package role holds none of them. */
    public function testOverGrants()
    {
        $base = expEditorPolicies::overGrants( self::policies( array( 'content/*', 'ezoe/*', 'user/login' ) ) );
        $names = array_map( function ( $o ) { return $o['module'] . '/' . $o['function'] . ' by ' . $o['by']; }, $base );
        $this->assertSame( array( 'content/cleantrash by content/*', 'content/translations by content/*',
                                  'content/urltranslator by content/*', 'content/jobs by content/*' ), $names );
        $this->assertSame( array(), expEditorPolicies::overGrants( self::packageEditor() ) );
        $admin = expEditorPolicies::overGrants( self::policies( array( '*/*' ) ) );
        $this->assertCount( count( expEditorPolicies::overGrantDefinitions() ), $admin );
        $setup = expEditorPolicies::overGrants( self::policies( array( 'setup/cache', 'user/*' ) ) );
        $this->assertSame( array( 'setup/* by setup/cache', 'user/register by user/*' ),
                           array_map( function ( $o ) { return $o['module'] . '/' . $o['function'] . ' by ' . $o['by']; }, $setup ) );
    }

    /** plain_site keeps the base data's content/*: it becomes the single functions the site packages give editors. */
    public function testPlainSiteContentWildcardIsNarrowed()
    {
        $base = self::policies( array( 'content/*', 'ezoe/*', 'user/login' ) );
        $plan = expEditorPolicies::narrowPlan( $base );
        $this->assertSame( 'narrow', $plan['action'] );
        $this->assertSame( 1, $plan['remove'] );
        $this->assertSame( expEditorPolicies::narrowedContentFunctions(), $plan['add'] );
        $this->assertStringContainsString( 'content/cleantrash', $plan['why'] );

        // what the role holds afterwards: no over-grant left, nothing of definitions() missing in content
        $after = self::policies( array( 'ezoe/*', 'user/login' ) );
        foreach ( $plan['add'] as $function )
            $after[] = array( 'module' => 'content', 'function' => $function );
        $this->assertSame( array(), expEditorPolicies::overGrants( $after ) );
        $granted = self::granted( expEditorPolicies::plan( $after, array( 'content', 'user', 'notification' ) ) );
        $this->assertSame( array( 'user/password', 'user/selfedit', 'notification/use' ), $granted );
    }

    /** Every content function the site packages give editors is in the narrowed list, and no over-grant is. */
    public function testNarrowedListMatchesThePackagesAndLeavesOutOverGrants()
    {
        $list = expEditorPolicies::narrowedContentFunctions();
        foreach ( self::packageEditor() as $policy )
            if ( $policy['module'] === 'content' )
                $this->assertContains( $policy['function'], $list );
        foreach ( expEditorPolicies::definitions() as $definition )
            if ( $definition['module'] === 'content' )
                $this->assertContains( $definition['function'], $list );
        foreach ( expEditorPolicies::overGrantDefinitions() as $over )
            if ( $over['module'] === 'content' )
                $this->assertNotContains( $over['function'], $list );
        $this->assertSame( array_values( array_unique( $list ) ), $list );
    }

    /** The site packages' Editor role has no content/*: nothing changes there. */
    public function testPackageEditorIsNotNarrowed()
    {
        $plan = expEditorPolicies::narrowPlan( self::packageEditor() );
        $this->assertSame( 'keep', $plan['action'] );
        $this->assertSame( array(), $plan['add'] );
    }

    /** A limited content/* is the site's choice; single functions the role holds already are not added again. */
    public function testLimitedWildcardIsKeptAndHeldFunctionsAreNotAddedTwice()
    {
        $limited = array( array( 'module' => 'content', 'function' => '*', 'limited' => true ) );
        $plan = expEditorPolicies::narrowPlan( $limited );
        $this->assertSame( 'keep', $plan['action'] );
        $this->assertStringContainsString( 'limitations', $plan['why'] );

        $policies = self::policies( array( 'content/*', 'content/read', 'content/hide' ) );
        $plan = expEditorPolicies::narrowPlan( $policies, array( 'content/restore' ) );
        $this->assertSame( 'narrow', $plan['action'] );
        $this->assertNotContains( 'read', $plan['add'] );
        $this->assertNotContains( 'hide', $plan['add'] );
        $this->assertNotContains( 'restore', $plan['add'] );
        $this->assertContains( 'edit', $plan['add'] );
    }

    public function testDefinitionsAreUniqueAndExplained()
    {
        $seen = array();
        foreach ( expEditorPolicies::definitions() as $definition )
        {
            $name = $definition['module'] . '/' . $definition['function'];
            $this->assertArrayNotHasKey( $name, $seen );
            $seen[$name] = true;
            $this->assertNotSame( '', $definition['reason'] );
            foreach ( expEditorPolicies::overGrantDefinitions() as $over )
                $this->assertNotSame( $over['module'] . '/' . $over['function'], $name, 'a policy is both needed and an over-grant' );
        }
    }

    /**
     * bin/php/addeditorpolicies.php is a dry run by default: no flag and --dry-run (kept as a synonym) do not write,
     * --apply writes, both together are refused.
     */
    public function testCommandWritesOnlyWithApply()
    {
        $this->assertFalse( \Exponential\Runnable\Command::applyRequested( array( 'apply' => false, 'dry-run' => false ) ), 'default: dry run' );
        $this->assertFalse( \Exponential\Runnable\Command::applyRequested( array( 'apply' => null, 'dry-run' => null ) ), 'options not given at all' );
        $this->assertFalse( \Exponential\Runnable\Command::applyRequested( array() ) );
        $this->assertFalse( \Exponential\Runnable\Command::applyRequested( array( 'apply' => false, 'dry-run' => true ) ), '--dry-run: dry run' );
        $this->assertTrue( \Exponential\Runnable\Command::applyRequested( array( 'apply' => true, 'dry-run' => false ) ), '--apply writes' );
        $this->assertNull( \Exponential\Runnable\Command::applyRequested( array( 'apply' => true, 'dry-run' => true ) ), 'both: usage error' );

        self::assertWritesOnlyOutsideADryRun( $this, 'kernel/private/classes/commands/addeditorpolicies.php',
                                              array( '\\expEditorPolicies::apply(', '\\expEditorPolicies::narrow(' ) );
    }

    public function testCommandRefusesApplyTogetherWithDryRun()
    {
        list( $code, $out ) = self::runCommand( 'addeditorpolicies', array( '--apply', '--dry-run' ) );
        $this->assertSame( 2, $code, $out );
        $this->assertStringContainsString( '--apply and --dry-run exclude each other', $out );
    }

    /**
     * The command's options and that every write call (each of $writes) sits in an "if ( !$dryRun )" branch, with
     * $dryRun the opposite of applyRequested(): without --apply nothing reaches the database.
     */
    public static function assertWritesOnlyOutsideADryRun( PHPUnit\Framework\TestCase $test, $file, array $writes )
    {
        $code = file_get_contents( $file );
        $test->assertStringContainsString( '"[apply][dry-run]', $code, 'the command takes --apply and --dry-run' );
        $test->assertStringContainsString( '$apply = self::applyRequested( $options );', $code );
        $test->assertStringContainsString( '$script->shutdown( 2 );', $code, 'both flags: exit 2' );
        $test->assertStringContainsString( '$dryRun = !$apply;', $code );
        $lines = explode( "\n", $code );
        $found = 0;
        foreach ( $lines as $number => $line )
        {
            foreach ( $writes as $write )
            {
                if ( strpos( $line, $write ) === false )
                    continue;
                $found++;
                $before = implode( "\n", array_slice( $lines, max( 0, $number - 3 ), min( 3, $number ) ) );
                $test->assertStringContainsString( 'if ( !$dryRun )', $before, "$file:" . ( $number + 1 ) . " writes outside a dry-run check" );
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
