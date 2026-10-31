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

    public function testEditorGetsTheFourOnceEvenWithTheToolbar()
    {
        $roles = array( self::role( 3, 'Editor', array( 'websitetoolbar/use', 'content/edit' ) ) );
        $this->assertSame( array( 'Editor:expwt/use', 'Editor:expwt/edit', 'Editor:expwt/create', 'Editor:expwt/structure' ),
                           self::grants( expWebsiteSidebarMigration::policyPlan( $roles ) ) );
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
        $roles = array( self::role( 3, 'Editor', array( 'expwt/use', 'expwt/edit', 'expwt/create', 'expwt/structure', 'websitetoolbar/use' ) ),
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
        $plan = expWebsiteSidebarMigration::policyPlan( array( self::roleWithToolbars( 3, 'Editor', array( $class, array( 'Class' => array( '16', '2' ) ) ) ) ) );
        $this->assertSame( array( 'Editor:expwt/use+Class', 'Editor:expwt/edit+Class', 'Editor:expwt/create+Class', 'Editor:expwt/structure+Class' ),
                           self::grants( $plan ) );
        $this->assertSame( '2 websitetoolbar/use policies', $plan[0]['why'] );
    }

    public function testDifferentClassListsAreJoinedIntoOneGrant()
    {
        $plan = expWebsiteSidebarMigration::policyPlan( array( self::roleWithToolbars( 3, 'Editor',
            array( array( 'Class' => array( '1', '16', '2' ) ), array( 'Class' => array( '43', '2', '44' ) ) ) ) ) );
        $this->assertCount( 4, $plan );
        $this->assertSame( array( 'Class' => array( '1', '2', '16', '43', '44' ) ), $plan[0]['limitations'] );
        $this->assertSame( 'expwt/use to Editor (3) limited to Class 1, 2, 16, 43, 44 (from 2 websitetoolbar/use policies)',
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
        $role = self::roleWithToolbars( 3, 'Editor', $sets );
        foreach ( expWebsiteSidebarMigration::policyPlan( array( $role ) ) as $grant )
            $role['policies'][] = array( 'module' => 'expwt', 'function' => $grant['function'], 'limitations' => $grant['limitations'] );
        $this->assertSame( array(), expWebsiteSidebarMigration::policyPlan( array( $role ) ), 'the joined grant covers both' );
        // an unlimited grant the role holds covers any limited one
        $held = self::roleWithToolbars( 3, 'Editor', $sets, array( 'expwt/use', 'expwt/edit', 'expwt/create', 'expwt/structure' ) );
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
}
