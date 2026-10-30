<?php
/**
 * The repair of accounts without an ezuser_setting row (expUserSettingRepair, update/common/scripts/6.0/
 * createmissingusersettings.php). No database: plan() decides on plain values; missing() and apply() were proven on
 * an installation (see the 6.0.15 changelog).
 *
 *  US-01 - A published account gets an enabled row, one waiting for its activation (account key) a disabled one
 *  US-02 - With $enable false every row is disabled; an unpublished account (draft held for approval, orphan ezuser
 *          row) is always left alone; ids are ordered, a broken id is dropped
 *  US-03 - The upgrade script: --dry-run changes nothing, rows that exist are never changed (apply() checks again),
 *          the user cache is purged for each row
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUserSettingRepairTest extends PHPUnit\Framework\TestCase
{
    /** US-01 */
    public function testEnabledUnlessWaitingForActivation()
    {
        $plan = expUserSettingRepair::plan( array(
            array( 'id' => 120, 'published' => true, 'has_key' => false ),
            array( 'id' => 121, 'published' => true, 'has_key' => true ),
        ) );
        $this->assertSame( array( 120 => 1, 121 => 0 ), $plan );
    }

    /** US-02 */
    public function testDisabledOptionAndUnpublishedAccounts()
    {
        $accounts = array(
            array( 'id' => 130, 'published' => true, 'has_key' => false ),
            array( 'id' => 125, 'published' => false, 'has_key' => false ),
            array( 'id' => 0, 'published' => true, 'has_key' => false ),
        );
        $this->assertSame( array( 125 => null, 130 => 0 ), expUserSettingRepair::plan( $accounts, false ) );
        $this->assertSame( array( 125 => null, 130 => 1 ), expUserSettingRepair::plan( $accounts ) );
        $this->assertSame( array(), expUserSettingRepair::plan( array() ) );
    }

    /** US-03 */
    public function testScriptAndApplyAreSafeToRunAgain()
    {
        $root = dirname( __DIR__, 5 );
        $script = (string)file_get_contents( $root . '/update/common/scripts/6.0/createmissingusersettings.php' );
        $this->assertStringContainsString( "'[dry-run][disabled]'", $script );
        $this->assertMatchesRegularExpression( "/if \\( \\\$options\\['dry-run'\\] \\)\\s*\\{[^}]*shutdown\\( 0 \\)/s", $script,
            '--dry-run stops before apply()' );
        $this->assertLessThan( strpos( $script, 'expUserSettingRepair::apply(' ), strpos( $script, "\$options['dry-run']" ) );

        $class = (string)file_get_contents( $root . '/kernel/classes/user/expusersettingrepair.php' );
        $this->assertMatchesRegularExpression( '/eZUserSetting::fetch\( \$id \) instanceof eZUserSetting\s*\)\s*continue;/', $class,
            'a row that exists is never changed' );
        $this->assertStringContainsString( 'eZUser::purgeUserCacheByUserId( (int)$id );', $class );
    }
}
