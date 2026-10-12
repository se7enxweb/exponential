<?php
/**
 * expPolicyOmitList: [RoleSettings] PolicyOmitList[] without PolicyOmitListExclude[].
 *
 * Reads the shipped settings/site.ini alone (eZINI::fetchFromFile(): no siteaccess, extension or
 * override files) and changes values in memory only. Cases: the shipped default has no exclusions
 * and keeps user/register omitted (upgrades unchanged); the new-installation value takes
 * user/register out and nothing else; module and module/view entries; empty entries ignored.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expPolicyOmitListTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 98854ab935), Copyright (C) Mugo Web, GPL v2
 */

require_once dirname( __DIR__, 4 ) . '/kernel/classes/exppolicyomitlist.php';

class expPolicyOmitListTest extends PHPUnit\Framework\TestCase
{
    /** @var eZINI the shipped settings/site.ini */
    protected $ini;

    /** @var string */
    protected $cwd;

    protected function setUp(): void
    {
        // eZINI reads paths relative to the installation
        $this->cwd = getcwd();
        chdir( dirname( __DIR__, 4 ) );
        $this->ini = eZINI::fetchFromFile( 'settings/site.ini' );
        $this->ini->setReadOnlySettingsCheck( false );
    }

    protected function tearDown(): void
    {
        chdir( $this->cwd );
    }

    public function testShippedDefaultExcludesNothingAndKeepsRegisterOpen()
    {
        $this->assertSame( array(), expPolicyOmitList::excluded( $this->ini ) );
        $shipped = array_values( (array)$this->ini->variable( 'RoleSettings', 'PolicyOmitList' ) );
        $this->assertSame( $shipped, expPolicyOmitList::entries( $this->ini ) );
        $this->assertContains( 'user/register', expPolicyOmitList::entries( $this->ini ) );
        $this->assertTrue( expPolicyOmitList::omits( 'user', 'register', $this->ini ) );
    }

    public function testNewInstallationValueClosesRegisterOnly()
    {
        $shipped = array_values( (array)$this->ini->variable( 'RoleSettings', 'PolicyOmitList' ) );
        $this->ini->setVariable( 'RoleSettings', 'PolicyOmitListExclude', array( 'user/register' ) );

        $entries = expPolicyOmitList::entries( $this->ini );
        $this->assertNotContains( 'user/register', $entries );
        $this->assertFalse( expPolicyOmitList::omits( 'user', 'register', $this->ini ) );
        // everything else stays, in its order: login, logout, success, activate, forgotpassword ...
        $this->assertSame( array_values( array_diff( $shipped, array( 'user/register' ) ) ), $entries );
        $this->assertTrue( expPolicyOmitList::omits( 'user', 'login', $this->ini ) );
        $this->assertTrue( expPolicyOmitList::omits( 'user', 'success', $this->ini ) );
    }

    public function testModuleEntriesAndEmptyValues()
    {
        $this->ini->setVariable( 'RoleSettings', 'PolicyOmitList', array( 'layout', 'user/login', 'user/register' ) );
        $this->ini->setVariable( 'RoleSettings', 'PolicyOmitListExclude', array( '', ' ', 'layout' ) );

        $this->assertSame( array( 'layout' ), expPolicyOmitList::excluded( $this->ini ) );
        $this->assertSame( array( 'user/login', 'user/register' ), expPolicyOmitList::entries( $this->ini ) );
        $this->assertFalse( expPolicyOmitList::omits( 'layout', 'set', $this->ini ) );
        $this->assertTrue( expPolicyOmitList::omits( 'user', 'register', $this->ini ) );
        // a module/view entry never opens the whole module
        $this->assertFalse( expPolicyOmitList::omits( 'user', '', $this->ini ) );
    }

    public function testMissingSettingsGiveAnEmptyList()
    {
        $ini = eZINI::fetchFromFile( 'settings/site.ini' );
        $ini->setReadOnlySettingsCheck( false );
        $ini->removeGroup( 'RoleSettings' );
        $this->assertSame( array(), expPolicyOmitList::entries( $ini ) );
        $this->assertFalse( expPolicyOmitList::omits( 'user', 'register', $ini ) );
    }
}
