<?php
/**
 * The grant of exphelp/read (bin/php/addexphelpreadpolicy.php, expHelpReadPolicyGrant) and the clean install data:
 * the Editor role reads the help on a new installation, and the script gives it to the roles named (the Editor role
 * when none is), once. No database: the roles are plain arrays as roleData() makes them, the SQL files are read.
 *
 *  HG-01  without --role only the Editor role is granted; Administrator (* / *) and the others are left alone
 *  HG-02  --role by ID or name; a role with exphelp/read or exphelp/* is skipped
 *  HG-03  a second run grants nothing
 *  HG-04  apply() appends exphelp/read without limitation, and only for a grant
 *  HG-05  both cleandata.sql files give the Editor role (3) exphelp/read with a policy ID no other policy uses
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpReadPolicyGrantTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpReadPolicyGrantTest extends PHPUnit\Framework\TestCase
{
    private static function role( $id, $name, array $policies )
    {
        $list = array();
        foreach ( $policies as $policy )
        {
            list( $module, $function ) = explode( '/', $policy );
            $list[] = array( 'module' => $module, 'function' => $function );
        }
        return array( 'id' => $id, 'name' => $name, 'policies' => $list );
    }

    private static function roles()
    {
        return array(
            self::role( 1, 'Anonymous', array( 'content/read', 'user/login' ) ),
            self::role( 2, 'Administrator', array( '*/*' ) ),
            self::role( 3, 'Editor', array( 'content/*', 'user/login' ) ),
            self::role( 4, 'Member', array( 'user/login', 'content/read' ) ),
            self::role( 5, 'Help readers', array( 'exphelp/read' ) ),
            self::role( 6, 'Help all', array( 'exphelp/*' ) ),
        );
    }

    private static function byName( array $plan )
    {
        $result = array();
        foreach ( $plan as $entry )
            $result[$entry['name']] = $entry;
        return $result;
    }

    public function testOnlyTheEditorRoleByDefault()
    {
        $plan = self::byName( expHelpReadPolicyGrant::plan( self::roles() ) );
        $this->assertCount( 6, $plan );
        $this->assertSame( 'grant', $plan['Editor']['action'] );
        foreach ( array( 'Anonymous', 'Administrator', 'Member', 'Help readers', 'Help all' ) as $name )
        {
            $this->assertSame( 'skip', $plan[$name]['action'], $name );
            $this->assertSame( 'not listed', $plan[$name]['reason'], $name );
        }
    }

    public function testListedRolesByIdOrName()
    {
        $plan = self::byName( expHelpReadPolicyGrant::plan( self::roles(), array( '4', 'Administrator', 'Help readers', '6' ) ) );
        $this->assertSame( 'grant', $plan['Member']['action'] );
        $this->assertSame( 'not listed', $plan['Editor']['reason'] );
        $this->assertSame( 'already has exphelp/read', $plan['Administrator']['reason'] );
        $this->assertSame( 'already has exphelp/read', $plan['Help readers']['reason'] );
        $this->assertSame( 'already has exphelp/read', $plan['Help all']['reason'] );
        $this->assertSame( 'not listed', $plan['Anonymous']['reason'] );
    }

    public function testASecondRunGrantsNothing()
    {
        $roles = self::roles();
        $only = array( 'Editor', 'Member' );
        foreach ( expHelpReadPolicyGrant::plan( $roles, $only ) as $i => $entry )
        {
            if ( $entry['action'] === 'grant' )
                $roles[$i]['policies'][] = array( 'module' => 'exphelp', 'function' => 'read' );
        }
        foreach ( expHelpReadPolicyGrant::plan( $roles, $only ) as $entry )
            $this->assertSame( 'skip', $entry['action'], $entry['name'] );
    }

    public function testApplyOnlyGrants()
    {
        $this->assertFalse( expHelpReadPolicyGrant::apply( null, array( 'action' => 'skip' ) ) );
        $role = new class {
            public $calls = array();
            public function appendPolicy( $module, $function, $limitations = array() ) { $this->calls[] = func_get_args(); return 'policy'; }
        };
        $this->assertSame( 'policy', expHelpReadPolicyGrant::apply( $role, array( 'action' => 'grant' ) ) );
        $this->assertSame( array( array( 'exphelp', 'read', array() ) ), $role->calls );
    }

    public function testCleanDataGivesTheEditorRoleThePolicy()
    {
        $root = dirname( __DIR__, 5 );
        foreach ( array( 'kernel/sql/common/cleandata.sql', 'kernel/sql/sqlite/cleandata.sql' ) as $file )
        {
            $sql = file_get_contents( "$root/$file" );
            $this->assertIsString( $sql, $file );
            preg_match_all( "/INSERT INTO ezpolicy \(\s*function_name,\s*id,\s*module_name,\s*original_id,\s*role_id\s*\) VALUES \(\s*'([^']*)',\s*(\d+),\s*'([^']*)',\s*\d+,\s*(\d+)\s*\);/", $sql, $m, PREG_SET_ORDER );
            $this->assertNotEmpty( $m, $file );
            $ids = array();
            $found = 0;
            foreach ( $m as $row )
            {
                $ids[] = (int)$row[2];
                if ( $row[3] === 'exphelp' )
                {
                    $this->assertSame( array( 'read', '3' ), array( $row[1], $row[4] ), "$file: exphelp policy only as read for the Editor role" );
                    $found++;
                }
            }
            $this->assertSame( 1, $found, "$file: one exphelp/read policy" );
            $this->assertSame( count( $ids ), count( array_unique( $ids ) ), "$file: policy IDs are unique" );
        }
    }
}
