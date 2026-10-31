<?php
/**
 * From ezwt to expwt (expWebsiteSidebarMigration, the upgrade step 6.0.15-expwt-enable and exp:wt:migrate-from-ezwt):
 * the policies, the settings and the activation, planned on plain arrays and text.
 *
 * No database, no files.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expWebsiteSidebarMigrationTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expWebsiteSidebarMigrationTest extends PHPUnit\Framework\TestCase
{
    private static function role( $id, $name, array $policies )
    {
        $list = array();
        foreach ( $policies as $key => $limitations )
        {
            $name2 = is_int( $key ) ? $limitations : $key;
            list( $module, $function ) = explode( '/', $name2 );
            $list[] = array( 'module' => $module, 'function' => $function, 'limitations' => is_int( $key ) ? array() : $limitations );
        }
        return array( 'id' => $id, 'name' => $name, 'policies' => $list );
    }

    private static function grants( array $plan )
    {
        $out = array();
        foreach ( $plan as $g )
            $out[] = $g['name'] . ':expwt/' . $g['function'] . ( $g['limitations'] ? '+' . implode( ',', array_keys( $g['limitations'] ) ) : '' );
        return $out;
    }

    public function testToolbarPolicyBecomesTheFourAreasWithItsLimitations()
    {
        $roles = array( self::role( 7, 'Partner', array( 'websitetoolbar/use' => array( 'Class' => array( '16', '2' ) ), 'content/read' ) ) );
        $plan = expWebsiteSidebarMigration::policyPlan( $roles );
        $this->assertSame( array( 'Partner:expwt/use+Class', 'Partner:expwt/edit+Class', 'Partner:expwt/create+Class', 'Partner:expwt/structure+Class' ),
                           self::grants( $plan ) );
        $this->assertSame( array( 'Class' => array( '16', '2' ) ), $plan[0]['limitations'] );
        $this->assertSame( 'websitetoolbar/use', $plan[0]['why'] );
    }

    public function testEditorGetsExpwtWildcardWithoutLimitationsInPlaceOfNarrowerGrants()
    {
        // F22: expwt/* without limitations, also where the toolbar was limited
        $roles = array( self::role( 3, 'Editor', array( 'websitetoolbar/use' => array( 'Class' => array( '16' ) ), 'content/edit' ) ) );
        $this->assertSame( array( 'Editor:expwt/*' ), self::grants( expWebsiteSidebarMigration::policyPlan( $roles ) ) );
        // the limited grants of an earlier migration are replaced (taken away by their ids), once
        $earlier = self::role( 3, 'Editor', array( 'expwt/use' => array( 'Class' => array( '16' ) ), 'expwt/edit', 'content/edit' ) );
        $earlier['policies'][0]['id'] = 41;
        $earlier['policies'][1]['id'] = 42;
        $plan = expWebsiteSidebarMigration::policyPlan( array( $earlier ) );
        $this->assertSame( array( 'Editor:expwt/*', 'Editor:expwt/use+Class', 'Editor:expwt/edit' ), self::grants( $plan ) );
        $this->assertSame( array( 41, 42 ), array_column( array_slice( $plan, 1 ), 'remove' ) );
        $this->assertArrayNotHasKey( 'remove', $plan[0] );
        // idempotent: expwt/* without limitations (or everything) plans nothing
        $this->assertSame( array(), expWebsiteSidebarMigration::policyPlan( array( self::role( 3, 'Editor', array( 'expwt/*', 'content/edit' ) ) ) ) );
        $this->assertSame( array(), expWebsiteSidebarMigration::policyPlan( array( self::role( 3, 'Editor', array( '*/*' ) ) ) ) );
        // expwt/* with limitations is not enough
        $this->assertSame( array( 'Editor:expwt/*' ), self::grants( expWebsiteSidebarMigration::policyPlan(
            array( self::role( 3, 'Editor', array( 'expwt/*' => array( 'Section' => array( '1' ) ) ) ) ) ) ) );
    }

    public function testTheAnonymousRoleNeverGetsAnything()
    {
        $this->assertSame( array(), expWebsiteSidebarMigration::policyPlan( array( self::role( 1, 'Anonymous', array( 'websitetoolbar/use', 'content/read' ) ) ) ) );
        $assigned = self::role( 9, 'Visitors', array( 'websitetoolbar/use' ) );
        $assigned['anonymous'] = true;
        $this->assertSame( array(), expWebsiteSidebarMigration::policyPlan( array( $assigned ) ), 'a role of the anonymous user, whatever its name' );
    }

    public function testTheSidebarIsSwitchedOnWhereEzwtWasActive()
    {
        $site = array( '/i/settings/override/site.ini.append.php' => "[ExtensionSettings]\nActiveExtensions[]=ezwt\n",
                       '/i/settings/siteaccess/smarty/site.ini.append.php' => "[ExtensionSettings]\nActiveAccessExtensions[]=ezwt\n",
                       '/i/settings/siteaccess/admin/site.ini.append.php' => "[SiteSettings]\nSiteName=x\n" );
        $plan = expWebsiteSidebarMigration::enablePlan( $site, array( '/i/settings/siteaccess/smarty/expwt.ini.append.php' => "<?php /*\n[ExpwtSettings]\nEnabled=disabled\n*/ ?>\n" ) );
        $this->assertSame( array( '/i/settings/override/expwt.ini.append.php' ), array_keys( $plan ), 'F18; a file that sets Enabled keeps it' );
        $this->assertStringContainsString( "[ExpwtSettings]\n# switched on where ezwt was active", $plan['/i/settings/override/expwt.ini.append.php'] );
        $this->assertStringContainsString( "Enabled=enabled", $plan['/i/settings/override/expwt.ini.append.php'] );
        $this->assertSame( array(), expWebsiteSidebarMigration::enablePlan( $site, $plan + array( '/i/settings/siteaccess/smarty/expwt.ini.append.php' => "Enabled=disabled\n" ) ), 'runs again: nothing' );
        $merged = expWebsiteSidebarMigration::withEnabled( "<?php /* #?ini charset=\"utf-8\"?\n[CreateSettings]\nHiddenClassList[]=x\n*/ ?>\n" );
        $this->assertStringContainsString( "HiddenClassList[]=x", $merged );
        $this->assertStringContainsString( "[ExpwtSettings]\n# switched on", $merged );
    }

    public function testAdministratorGetsExpwtWildcardUnlessItHasEverything()
    {
        $this->assertSame( array( 'Administrator:expwt/*' ),
                           self::grants( expWebsiteSidebarMigration::policyPlan( array( self::role( 2, 'Administrator', array( 'content/*' ) ) ) ) ) );
        $this->assertSame( array(), expWebsiteSidebarMigration::policyPlan( array( self::role( 2, 'Administrator', array( '*/*' ) ) ) ) );
        $this->assertSame( array(), expWebsiteSidebarMigration::policyPlan( array( self::role( 2, 'Administrator', array( 'expwt/*' ) ) ) ) );
    }

    public function testRunsAgainWithoutGrantingTwice()
    {
        $roles = array( self::role( 3, 'Editor', array( 'expwt/*', 'websitetoolbar/use' ) ),
                        self::role( 7, 'Partner', array( 'websitetoolbar/use' => array( 'Class' => array( '2', '16' ) ),
                                                         'expwt/use' => array( 'Class' => array( '16', '2' ) ),
                                                         'expwt/edit' => array( 'Class' => array( '2', '16' ) ),
                                                         'expwt/create' => array( 'Class' => array( '2', '16' ) ),
                                                         'expwt/structure' => array( 'Class' => array( '2', '16' ) ) ) ) );
        $this->assertSame( array(), expWebsiteSidebarMigration::policyPlan( $roles ), 'the same limitations in another order count' );
    }

    /** Two policies under one key cannot be written with role(), so they are listed here */
    private static function roleWithToolbars( $id, $name, array $limitationSets, array $more = array() )
    {
        $role = self::role( $id, $name, $more );
        foreach ( $limitationSets as $limitations )
            $role['policies'][] = array( 'module' => 'websitetoolbar', 'function' => 'use', 'limitations' => $limitations );
        return $role;
    }

    public function testTwoIdenticalToolbarPoliciesGiveOneGrantPerFunction()
    {
        $class = array( 'Class' => array( '2', '16' ) );
        $plan = expWebsiteSidebarMigration::policyPlan( array( self::roleWithToolbars( 3, 'Webmaster', array( $class, array( 'Class' => array( '16', '2' ) ) ) ) ) );
        $this->assertSame( array( 'Webmaster:expwt/use+Class', 'Webmaster:expwt/edit+Class', 'Webmaster:expwt/create+Class', 'Webmaster:expwt/structure+Class' ),
                           self::grants( $plan ) );
        $this->assertSame( '2 websitetoolbar/use policies', $plan[0]['why'] );
    }

    public function testDifferentClassListsAreJoinedIntoOneGrant()
    {
        $plan = expWebsiteSidebarMigration::policyPlan( array( self::roleWithToolbars( 3, 'Webmaster',
            array( array( 'Class' => array( '1', '16', '2' ) ), array( 'Class' => array( '43', '2', '44' ) ) ) ) ) );
        $this->assertCount( 4, $plan );
        $this->assertSame( array( 'Class' => array( '1', '2', '16', '43', '44' ) ), $plan[0]['limitations'] );
        $this->assertSame( 'expwt/use to Webmaster (3) limited to Class 1, 2, 16, 43, 44 (from 2 websitetoolbar/use policies)',
                           expWebsiteSidebarMigration::grantText( $plan[0] ) );
    }

    public function testLimitationSetsThatCannotBeJoinedStayApart()
    {
        // Class 2 in Section 1, or Class 16 in Section 3: joining both would also grant Class 2 in Section 3
        $a = array( 'Class' => array( '2' ), 'Section' => array( '1' ) );
        $b = array( 'Class' => array( '16' ), 'Section' => array( '3' ) );
        $plan = expWebsiteSidebarMigration::policyPlan( array( self::roleWithToolbars( 7, 'Partner', array( $a, $b ) ) ) );
        $this->assertSame( array( $a, $b ), array_slice( array_column( $plan, 'limitations' ), 0, 2 ) );
        $this->assertCount( 8, $plan );
        // the same Section: one grant; one without limitations: only that one; a narrower set: dropped
        $this->assertSame( array( array( 'Class' => array( '2', '16' ), 'Section' => array( '1' ) ) ),
                           expWebsiteSidebarMigration::mergeLimitations( array( $a, array( 'Class' => array( '16' ), 'Section' => array( '1' ) ) ) ) );
        $this->assertSame( array( array() ), expWebsiteSidebarMigration::mergeLimitations( array( $a, array(), $b ) ) );
        $this->assertSame( array( array( 'Class' => array( '2', '16' ) ) ),
                           expWebsiteSidebarMigration::mergeLimitations( array( array( 'Class' => array( '2', '16' ) ), $a ) ) );
        // Node and Subtree match when either does, so such sets are not joined
        $ns1 = array( 'Node' => array( '2' ), 'Subtree' => array( '/1/2/' ) );
        $ns2 = array( 'Node' => array( '5' ), 'Subtree' => array( '/1/2/' ) );
        $this->assertSame( array( $ns1, $ns2 ), expWebsiteSidebarMigration::mergeLimitations( array( $ns1, $ns2 ) ) );
        $this->assertFalse( expWebsiteSidebarMigration::subsumes( array( 'Node' => array( '2' ) ), $ns1 ) );
    }

    public function testRunAfterAJoinedGrantPlansNothing()
    {
        $sets = array( array( 'Class' => array( '1', '16', '2' ) ), array( 'Class' => array( '43', '2', '44' ) ) );
        $role = self::roleWithToolbars( 3, 'Webmaster', $sets );
        foreach ( expWebsiteSidebarMigration::policyPlan( array( $role ) ) as $grant )
            $role['policies'][] = array( 'module' => 'expwt', 'function' => $grant['function'], 'limitations' => $grant['limitations'] );
        $this->assertSame( array(), expWebsiteSidebarMigration::policyPlan( array( $role ) ), 'the joined grant covers both' );
        // an unlimited grant the role holds covers any limited one
        $held = self::roleWithToolbars( 3, 'Webmaster', $sets, array( 'expwt/use', 'expwt/edit', 'expwt/create', 'expwt/structure' ) );
        $this->assertSame( array(), expWebsiteSidebarMigration::policyPlan( array( $held ) ) );
    }

    public function testOtherRolesAreLeftAlone()
    {
        $this->assertSame( array(), expWebsiteSidebarMigration::policyPlan( array( self::role( 1, 'Anonymous', array( 'content/read', 'user/login' ) ) ) ) );
    }

    public function testHiddenClassesAreCopiedBesideTheToolbarSettings()
    {
        $files = array( 'settings/override/websitetoolbar.ini.append.php' =>
                        "<?php /*\n[WebsiteToolbarSettings]\nHiddenContentClasses[]\nHiddenContentClasses[]=banner\nHiddenContentClasses[]=common_ini_settings\n"
                        . "ODFDisplayClasses[]=article\n[CustomTemplateSettings]\nCustomTemplateList[]=object_states\n*/ ?>" );
        $plan = expWebsiteSidebarMigration::settingsPlan( $files );
        $this->assertSame( array( 'settings/override/expwt.ini.append.php' ), array_keys( $plan['writes'] ) );
        $written = $plan['writes']['settings/override/expwt.ini.append.php'];
        $this->assertStringContainsString( "[CreateSettings]\nHiddenClassList[]=banner\nHiddenClassList[]=common_ini_settings\n", $written );
        $this->assertStringStartsWith( '<?php /*', $written );
        $this->assertCount( 2, $plan['notes'], 'the eZODF buttons and the custom templates are reported' );
        $this->assertStringContainsString( 'object_states', $plan['notes'][1] );

        $again = expWebsiteSidebarMigration::settingsPlan( $files, $plan['writes'] );
        $this->assertSame( array(), $again['writes'], 'a second run writes nothing' );
    }

    public function testHiddenClassesJoinAnExpwtFileThereIs()
    {
        $existing = "<?php /* #?ini charset=\"utf-8\"?\n\n[ExpwtSettings]\nEnabled=enabled\n\n*/ ?>\n";
        $text = expWebsiteSidebarMigration::withHiddenClasses( $existing, array( 'banner' ) );
        $this->assertStringContainsString( "Enabled=enabled", $text );
        $this->assertStringContainsString( "[CreateSettings]\nHiddenClassList[]=banner\n", $text );
        $this->assertStringEndsWith( "*/ ?>\n", $text );
        $withGroup = "<?php /*\n[CreateSettings]\nHiddenClassList[]=folder\n*/ ?>\n";
        $this->assertSame( "<?php /*\n[CreateSettings]\nHiddenClassList[]=banner\nHiddenClassList[]=folder\n*/ ?>\n",
                           expWebsiteSidebarMigration::withHiddenClasses( $withGroup, array( 'banner' ) ) );
    }

    public function testActivationPutsExpwtInEzwtsPlace()
    {
        $text = "[ExtensionSettings]\nActiveExtensions[]\nActiveExtensions[]=ezjscore\nActiveExtensions[]=ezwt\nActiveExtensions[]=ezoe\n";
        $this->assertSame( "[ExtensionSettings]\nActiveExtensions[]\nActiveExtensions[]=ezjscore\nActiveExtensions[]=expwt\nActiveExtensions[]=ezoe\n",
                           expWebsiteSidebarMigration::activationPlan( $text ) );
        $both = "ActiveAccessExtensions[]=expwt\nActiveAccessExtensions[]=ezwt\n";
        $this->assertSame( "ActiveAccessExtensions[]=expwt\n", expWebsiteSidebarMigration::activationPlan( $both ), 'no second expwt' );
        $this->assertNull( expWebsiteSidebarMigration::activationPlan( "ActiveExtensions[]=ezwtx\n#ActiveExtensions[]=ezwt\n" ) );
        $this->assertNull( expWebsiteSidebarMigration::activationPlan( "ActiveExtensions[]=expwt\n" ) );
    }

    public function testWithExpwtActiveEzwtIsOnlyRemoved()
    {
        $after = "[ExtensionSettings]\nActiveExtensions[]=ezjscore\nActiveExtensions[]=ezwt\n# the sidebar\nActiveExtensions[]=expwt\nActiveExtensions[]=ezoe\n";
        $change = expWebsiteSidebarMigration::activationChange( $after );
        $this->assertSame( 'remove', $change['action'] );
        $this->assertSame( "[ExtensionSettings]\nActiveExtensions[]=ezjscore\n# the sidebar\nActiveExtensions[]=expwt\nActiveExtensions[]=ezoe\n", $change['text'] );
        $this->assertNull( expWebsiteSidebarMigration::activationChange( $change['text'] ), 'a second run changes nothing' );

        $before = "ActiveExtensions[]=expwt\r\nActiveExtensions[]=ezwt\r\nActiveExtensions[]=ezoe";
        $change = expWebsiteSidebarMigration::activationChange( $before );
        $this->assertSame( 'remove', $change['action'] );
        $this->assertSame( "ActiveExtensions[]=expwt\r\nActiveExtensions[]=ezoe", $change['text'] );
        $this->assertSame( 1, substr_count( $change['text'], 'expwt' ) );

        $twice = "ActiveExtensions[]=ezwt\nActiveExtensions[]=ezoe\nActiveExtensions[]=ezwt\n";
        $change = expWebsiteSidebarMigration::activationChange( $twice );
        $this->assertSame( 'replace', $change['action'] );
        $this->assertSame( "ActiveExtensions[]=expwt\nActiveExtensions[]=ezoe\n", $change['text'], 'one expwt for two ezwt' );
    }

    /**
     * F21: the schedule entries of earlier expwt versions are read into steps; what cannot be read is removed, not
     * carried out.
     */
    public function testSchedulePlanReadsTheEntriesOfEarlierExpwtVersions()
    {
        $plan = expWebsiteSidebarMigration::schedulePlan( array(
            'expwt_unpublish_88' => '{"type":"unpublish","node":88,"user":14,"at":3000}',
            'expwt_publish_59_4' => array( 'type' => 'publish', 'object' => 59, 'version' => 4, 'user' => 14, 'at' => 2000, 'until' => 5000 ),
            'expwt_publish_60_2' => 'not json',
            'expwt_publish_61_1' => '{"type":"unpublish","node":9,"user":14,"at":1}',
            'expwt_publish_62_1' => '{"type":"publish","object":62,"version":1,"user":0,"at":1}',
        ) );
        $by = array();
        foreach ( $plan as $step )
            $by[$step['name']] = $step;
        $this->assertSame( array( 'expwt_publish_59_4', 'expwt_publish_60_2', 'expwt_publish_61_1', 'expwt_publish_62_1', 'expwt_unpublish_88' ),
                           array_keys( $by ), 'by name' );
        $p = $by['expwt_publish_59_4'];
        $this->assertSame( array( 'publish', 59, 4, 14, 2000, 5000, 'move' ),
                           array( $p['kind'], $p['object'], $p['version'], $p['user'], $p['at'], $p['until'], $p['state'] ) );
        $t = $by['expwt_unpublish_88'];
        $this->assertSame( array( 'takedown', 88, 3000, null, 'move' ), array( $t['kind'], $t['node'], $t['at'], $t['until'], $t['state'] ) );
        $this->assertSame( 'drop', $by['expwt_publish_60_2']['state'], 'not JSON' );
        $this->assertSame( 'drop', $by['expwt_publish_61_1']['state'], 'a take-down under the name of a publish' );
        $this->assertSame( 'drop', $by['expwt_publish_62_1']['state'], 'no user' );
    }

    public function testScheduleDecideMovesWhatTheKernelCanCarry()
    {
        $publish = array( 'identifier' => 'publish_date', 'datatype' => 'ezdatetime', 'precision' => 'datetime' );
        $hide = array( 'identifier' => 'hide_date', 'datatype' => 'ezdatetime', 'precision' => 'datetime' );
        $step = array( 'name' => 'expwt_publish_59_4', 'kind' => 'publish', 'object' => 59, 'version' => 4, 'node' => null,
                       'user' => 14, 'at' => 2000, 'until' => 5000, 'state' => 'move', 'why' => '' );
        $all = array( 'exists' => true, 'draft' => true, 'user' => true, 'publish' => $publish, 'takeDown' => $hide, 'now' => 1000 );
        $this->assertSame( 'move', expWebsiteSidebarMigration::scheduleDecide( $step, $all )['state'] );
        $this->assertSame( 'drop', expWebsiteSidebarMigration::scheduleDecide( $step, array( 'exists' => false ) + $all )['state'], 'gone' );
        $this->assertSame( 'drop', expWebsiteSidebarMigration::scheduleDecide( $step, array( 'draft' => false ) + $all )['state'], 'published meanwhile' );
        $this->assertSame( 'blocked', expWebsiteSidebarMigration::scheduleDecide( $step, array( 'user' => false ) + $all )['state'], 'no user' );
        $this->assertSame( 'blocked', expWebsiteSidebarMigration::scheduleDecide( $step, array( 'publish' => null ) + $all )['state'], 'no publish date field' );
        $this->assertSame( 'blocked', expWebsiteSidebarMigration::scheduleDecide( $step, array( 'takeDown' => null ) + $all )['state'], 'no hide date field' );
        $due = array( 'until' => null ) + $step;
        $this->assertSame( 'move', expWebsiteSidebarMigration::scheduleDecide( $due, array( 'publish' => null, 'now' => 3000 ) + $all )['state'],
                           'due already: published now, no date field needed' );
        $down = array( 'name' => 'expwt_unpublish_88', 'kind' => 'takedown', 'node' => 88, 'at' => 3000, 'until' => null ) + $step;
        $this->assertSame( 'move', expWebsiteSidebarMigration::scheduleDecide( $down, array( 'draft' => false, 'publish' => null ) + $all )['state'] );
        $this->assertSame( 'blocked', expWebsiteSidebarMigration::scheduleDecide( $down, array( 'takeDown' => null ) + $all )['state'] );
        $line = expWebsiteSidebarMigration::scheduleText( expWebsiteSidebarMigration::scheduleDecide( $step, array( 'publish' => null ) + $all ), false );
        $this->assertStringContainsString( 'stays until it can', $line );
        $this->assertStringContainsString( 'wait-until-date', $line );
    }

    /**
     * The backup of a settings file (which can hold credentials) keeps the file's mode instead of becoming readable by
     * everybody, as a plain copy() made it.
     */
    public function testBackupKeepsTheModeOfTheSettingsFile()
    {
        $dir = 'var/tmp/expwt-migration-backup-test-' . getmypid();
        @mkdir( $dir, eZDir::dirMode( 0700 ), true );
        $path = $dir . '/site.ini.append.php';
        file_put_contents( $path, "<?php /*\n[ExtensionSettings]\nActiveExtensions[]=ezwt\n*/ ?>\n" );
        chmod( $path, eZFile::fileMode( 0640 ) );
        $method = new ReflectionMethod( 'expWebsiteSidebarMigration', 'backup' );
        $method->setAccessible( true );
        $method->invoke( null, $path );
        clearstatcache();
        $this->assertFileExists( $path . '.before-expwt' );
        $this->assertSame( fileperms( $path ) & 0777, fileperms( $path . '.before-expwt' ) & 0777, 'the backup has the mode of the file' );
        $this->assertSame( 0, fileperms( $path . '.before-expwt' ) & 0007, 'the backup is not readable by others' );
        unlink( $path . '.before-expwt' );
        unlink( $path );
        rmdir( $dir );
    }
}
