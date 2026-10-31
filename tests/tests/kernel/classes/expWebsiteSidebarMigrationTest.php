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
}
