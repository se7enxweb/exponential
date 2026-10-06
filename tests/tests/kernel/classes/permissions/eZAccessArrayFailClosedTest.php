<?php
/**
 * The access array fails closed: a read of roles, policies, limitations or values that fails (the database answers
 * with an error) denies what it could not read, never grants it. Without a database: a stub answers each query with
 * rows or with an error.
 *
 *  FC-01 - The roles of a user cannot be read: no roles, an empty access array, the failure is counted
 *  FC-02 - The role ids of a user cannot be read (eZRole::fetchIDListByUser()): none, counted
 *  FC-03 - The policies of a role cannot be read: the role has none
 *  FC-04 - The limitations of a policy cannot be read: the policy is disabled and gives no part, not '*'
 *  FC-05 - The values of a limitation cannot be read: the policy gives no part, not one with fewer values
 *  FC-06 - The whole build, with the rows loaded ahead and role by role: a policy whose limitations cannot be read is
 *          left out, an unlimited policy that was read stays, and the build counts the failures once
 *  FC-07 - With every read answered, nothing is counted and the policy is built as before
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

require_once __DIR__ . '/fixtures/ezcontentpermissionsqltestdb.php';

class eZAccessArrayFailClosedTest extends PHPUnit\Framework\TestCase
{
    /** @var eZDBInterface|null */
    private $previousDB;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->previousDB = $GLOBALS['eZDBGlobalInstance'] ?? null;
    }

    protected function tearDown(): void
    {
        eZDB::setInstance( $this->previousDB );
        ezpINIHelper::restoreINISettings();
    }

    /**
     * A database whose answer to each query is decided by $answer( $sql ): rows, or false for an error.
     */
    private function database( callable $answer )
    {
        $db = new class extends eZContentPermissionSQLTestDB {
            public $answer;
            public $asked = array();
            function arrayQuery( $sql, $params = array(), $server = false )
            {
                $this->asked[] = $sql;
                return ( $this->answer )( $sql );
            }
        };
        $db->answer = $answer;
        eZDB::setInstance( $db );
        return $db;
    }

    private static function policy( $id, $function = 'read' )
    {
        return new eZPolicy( array( 'id' => $id, 'role_id' => 77, 'module_name' => 'content', 'function_name' => $function, 'original_id' => 0 ) );
    }

    /** FC-01 */
    public function testRolesThatCannotBeReadGiveNothing()
    {
        $this->database( function ( $sql ) { return false; } );
        $before = eZRole::$accessReadFailures;
        $this->assertSame( array(), eZRole::fetchByUser( array( 10, 11 ) ) );
        $this->assertSame( $before + 1, eZRole::$accessReadFailures );
        $this->assertSame( array(), eZRole::accessArrayByUserID( array( 10, 11 ) ) );
        $this->assertSame( $before + 2, eZRole::$accessReadFailures );
    }

    /** FC-02 */
    public function testRoleIdsThatCannotBeReadAreNone()
    {
        $this->database( function ( $sql ) { return false; } );
        $before = eZRole::$accessReadFailures;
        $this->assertSame( array(), eZRole::fetchIDListByUser( array( 10 ) ) );
        $this->assertSame( $before + 1, eZRole::$accessReadFailures );
    }

    /** FC-03 */
    public function testPoliciesThatCannotBeReadAreNone()
    {
        $this->database( function ( $sql ) { return false; } );
        $before = eZRole::$accessReadFailures;
        $role = new eZRole( array( 'id' => 77, 'name' => 'r', 'version' => 0 ) );
        $this->assertSame( array(), $role->policyList() );
        $this->assertSame( array(), $role->accessArray() );
        $this->assertSame( $before + 1, eZRole::$accessReadFailures );
    }

    /** FC-04 */
    public function testLimitationsThatCannotBeReadDenyThePolicy()
    {
        $this->database( function ( $sql ) { return false; } );
        $before = eZRole::$accessReadFailures;
        $policy = self::policy( 901 );
        $this->assertSame( array(), $policy->accessArray() );
        $this->assertTrue( $policy->Disabled );
        $this->assertSame( $before + 1, eZRole::$accessReadFailures );

        // Assigned for a subtree: still nothing, not the assignment's limitation alone
        $assigned = self::policy( 902 );
        $assigned->setAttribute( 'limit_identifier', 'User_Subtree' );
        $assigned->setAttribute( 'limit_value', '/1/2/' );
        $this->assertSame( array(), $assigned->accessArray() );
    }

    /** FC-05 */
    public function testValuesThatCannotBeReadDenyThePolicy()
    {
        $this->database( function ( $sql ) {
            if ( strpos( $sql, 'ezpolicy_limitation_value' ) !== false )
                return false;
            if ( strpos( $sql, 'ezpolicy_limitation' ) !== false )
                return array( array( 'id' => 9011, 'policy_id' => 903, 'identifier' => 'Class' ),
                              array( 'id' => 9012, 'policy_id' => 903, 'identifier' => 'Section' ) );
            return array();
        } );
        $before = eZRole::$accessReadFailures;
        $this->assertSame( array(), self::policy( 903 )->accessArray() );
        $this->assertGreaterThan( $before, eZRole::$accessReadFailures );
    }

    /** FC-06 */
    public function testTheBuildLeavesOutWhatItCouldNotRead()
    {
        foreach ( array( 'enabled', 'disabled' ) as $prefetch )
        {
            ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'AccessArrayPrefetch', $prefetch );
            $db = $this->database( function ( $sql ) {
                if ( strpos( $sql, 'ezuser_role' ) !== false )
                    return array( array( 'id' => 77, 'name' => 'r', 'version' => 0, 'limit_identifier' => '', 'limit_value' => '', 'user_role_id' => 5 ) );
                if ( strpos( $sql, 'ezpolicy_limitation' ) !== false )
                    return false;
                if ( strpos( $sql, 'ezpolicy' ) !== false )
                    return array( array( 'id' => 904, 'role_id' => 77, 'module_name' => 'content', 'function_name' => 'read', 'original_id' => 0 ),
                                  array( 'id' => 905, 'role_id' => 77, 'module_name' => 'user', 'function_name' => 'login', 'original_id' => 0 ) );
                return array();
            } );
            $before = eZRole::$accessReadFailures;
            $array = eZRole::accessArrayByUserID( array( 10 ) );
            $this->assertArrayNotHasKey( 'content', $array, $prefetch );
            $this->assertArrayNotHasKey( 'user', $array, "$prefetch: a policy whose limitations were not read is not '*'" );
            $this->assertSame( $before + 2, eZRole::$accessReadFailures, $prefetch );
            $this->assertNull( eZRole::$prefetchedPolicyRows );
            $this->assertNull( eZPolicy::$prefetchedLimitationRows );
        }
    }

    /** FC-07 */
    public function testWithEveryReadAnsweredNothingIsCounted()
    {
        $this->database( function ( $sql ) {
            if ( strpos( $sql, 'ezpolicy_limitation_value' ) !== false )
                return array( array( 'id' => 1, 'limitation_id' => 9061, 'value' => '2' ) );
            if ( strpos( $sql, 'ezpolicy_limitation' ) !== false && strpos( $sql, "'906'" ) !== false )
                return array( array( 'id' => 9061, 'policy_id' => 906, 'identifier' => 'Class' ) );
            return array();
        } );
        $before = eZRole::$accessReadFailures;
        $this->assertSame( array( 'content' => array( 'read' => array( 'p_906' => array( 'Class' => array( '2' ) ) ) ) ),
                           self::policy( 906 )->accessArray() );
        $this->assertSame( array( 'content' => array( 'read' => array( '*' => '*' ) ) ), self::policy( 907 )->accessArray(),
                           'a policy without limitations stays unlimited when the read answered' );
        $this->assertSame( $before, eZRole::$accessReadFailures );
    }
}
