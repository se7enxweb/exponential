<?php
/**
 * Tests of the evaluation of a user's access array, without the database: eZUser::hasAccessTo() (no access, full
 * access through "*", limited access with the policies of the module wide and the function's own entries merged)
 * and eZUser::hasAccessToView(), which evaluates the policy function expression of a view ("read", "edit or
 * create", "(read && edit) || create", a list meaning "and") and refuses expressions it cannot evaluate safely.
 *
 * The access array is given to the user object directly; the module is a fixture (fixtures/modules/k1perm).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZUserAccessEvaluationTest extends PHPUnit\Framework\TestCase
{
    const USER_ID = 999999801;

    private $limitationList;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->limitationList = $GLOBALS['ezpolicylimitation_list'] ?? null;
    }

    protected function tearDown(): void
    {
        if ( $this->limitationList === null )
            unset( $GLOBALS['ezpolicylimitation_list'] );
        else
            $GLOBALS['ezpolicylimitation_list'] = $this->limitationList;
    }

    private function user( array $accessArray )
    {
        $user = new eZUser( array( 'contentobject_id' => self::USER_ID, 'login' => 'k1perm', 'email' => 'k1perm@k1.example.invalid' ) );
        $user->AccessArray = $accessArray;
        return $user;
    }

    private function module()
    {
        $module = eZModule::findModule( 'k1perm', null, __DIR__ . '/fixtures/modules' );
        $this->assertInstanceOf( 'eZModule', $module );
        return $module;
    }

    // ------------------------------------------------------------ hasAccessTo

    public function testNoEntryMeansNoAccessAndSaysWhatIsMissing()
    {
        $result = $this->user( array( 'user' => array( 'login' => array( '*' => '*' ) ) ) )->hasAccessTo( 'content', 'read' );
        $this->assertSame( 'no', $result['accessWord'] );
        $this->assertSame( array( 'Module' => 'content', 'Function' => 'read', 'ClassID' => '', 'MainNodeID' => '' ),
                           $result['accessList']['FunctionRequired'] );
        $this->assertSame( array(), $result['accessList']['PolicyList'] );
    }

    public function testEmptyAccessArrayMeansNoAccess()
    {
        $this->assertSame( 'no', $this->user( array() )->hasAccessTo( 'content' )['accessWord'] );
    }

    public function testEverythingPolicyGivesAccessToEveryModule()
    {
        $user = $this->user( array( '*' => array( '*' => array( '*' => '*' ) ) ) );
        $this->assertSame( array( 'accessWord' => 'yes' ), $user->hasAccessTo( 'content', 'read' ) );
        $this->assertSame( array( 'accessWord' => 'yes' ), $user->hasAccessTo( 'setup' ) );
    }

    public function testModuleWidePolicyGivesAccessToEveryFunctionOfThatModuleOnly()
    {
        $user = $this->user( array( 'content' => array( '*' => array( '*' => '*' ) ) ) );
        $this->assertSame( 'yes', $user->hasAccessTo( 'content', 'remove' )['accessWord'] );
        $this->assertSame( 'yes', $user->hasAccessTo( 'content' )['accessWord'] );
        $this->assertSame( 'no', $user->hasAccessTo( 'user', 'login' )['accessWord'] );
    }

    public function testFunctionPolicyCountsOnlyWhenTheFunctionIsAsked()
    {
        $user = $this->user( array( 'content' => array( 'read' => array( '*' => '*' ) ) ) );
        $this->assertSame( 'yes', $user->hasAccessTo( 'content', 'read' )['accessWord'] );
        $this->assertSame( 'no', $user->hasAccessTo( 'content', 'edit' )['accessWord'] );
        $this->assertSame( 'no', $user->hasAccessTo( 'content' )['accessWord'] );
        $this->assertSame( 'no', $user->hasAccessTo( 'content', '*' )['accessWord'], 'the function * is not a wildcard for a request' );
    }

    public function testLimitedAccessReturnsThePoliciesOfEveryMatchingEntry()
    {
        $user = $this->user( array(
            '*' => array( '*' => array( 'p_1' => array( 'Section' => array( 1 ) ) ) ),
            'content' => array(
                '*' => array( 'p_2' => array( 'Class' => array( 2, 5 ) ) ),
                'read' => array( 'p_3' => array( 'Subtree' => array( '/1/2/' ) ), 'p_4' => array( 'Node' => array( 43 ) ) ),
                'edit' => array( 'p_5' => array( 'Owner' => array( 1 ) ) ),
            ),
        ) );
        $result = $user->hasAccessTo( 'content', 'read' );
        $this->assertSame( 'limited', $result['accessWord'] );
        $this->assertSame( array( 'p_1', 'p_2', 'p_3', 'p_4' ), array_keys( $result['policies'] ) );
        $this->assertSame( array( 'Subtree' => array( '/1/2/' ) ), $result['policies']['p_3'] );
        $this->assertArrayNotHasKey( 'p_5', $result['policies'] );
    }

    public function testAnUnlimitedPolicyBesideLimitedOnesWins()
    {
        $user = $this->user( array( 'content' => array(
            '*' => array( '*' => '*' ),
            'read' => array( '*' => '*', 'p_3' => array( 'Subtree' => array( '/1/2/' ) ) ),
        ) ) );
        // the two "*" entries merge into array( '*', '*' )
        $this->assertSame( array( 'accessWord' => 'yes' ), $user->hasAccessTo( 'content', 'read' ) );
    }

    public function testStarArrayFromSeveralRolesMeansFullAccess()
    {
        $user = $this->user( array( 'content' => array( 'read' => array( '*' => array( '*', '*' ) ) ) ) );
        $this->assertSame( 'yes', $user->hasAccessTo( 'content', 'read' )['accessWord'] );
    }

    // -------------------------------------------------------- hasAccessToView

    public static function viewProvider()
    {
        $read = array( 'read' => array( '*' => '*' ) );
        $edit = array( 'edit' => array( '*' => '*' ) );
        $create = array( 'create' => array( '*' => '*' ) );
        return array(
            'single function granted' => array( 'read', $read, true ),
            'single function missing' => array( 'read', $edit, false ),
            'list means and: one missing' => array( 'both', $read, false ),
            'list means and: both' => array( 'both', $read + $edit, true ),
            'or: first' => array( 'either', $edit, true ),
            'or: second' => array( 'either', $create, true ),
            'or: none' => array( 'either', $read, false ),
            'symbols: and part' => array( 'symbols', $read + $edit, true ),
            'symbols: only half the and part' => array( 'symbols', $read, false ),
            'symbols: or part' => array( 'symbols', $create, true ),
            'string expression' => array( 'string', $edit, true ),
            'string expression none' => array( 'string', $create, false ),
            'empty entry refuses' => array( 'emptyentry', $read, false ),
            'unknown function refuses' => array( 'unknown', $read, false ),
            'code in the expression refuses' => array( 'code', $read, false ),
            'function name is matched as a whole word' => array( 'prefix', $read, false ),
            'function name is matched as a whole word, granted' => array( 'prefix', array( 'readall' => array( '*' => '*' ) ), true ),
            'unknown view' => array( 'nosuchview', $read + $edit + $create, false ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('viewProvider')]
    public function testHasAccessToView( $view, $functions, $expected )
    {
        $params = array();
        $user = $this->user( array( 'k1perm' => $functions ) );
        $this->assertSame( $expected, $user->hasAccessToView( $this->module(), $view, $params ) );
    }

    public function testViewWithoutFunctionsNeedsAccessToTheModule()
    {
        $params = array();
        $this->assertFalse( $this->user( array( 'content' => array( '*' => array( '*' => '*' ) ) ) )->hasAccessToView( $this->module(), 'open', $params ) );
        $this->assertSame( 'k1perm', $params['accessList']['FunctionRequired']['Module'] );

        $params = array();
        $this->assertTrue( $this->user( array( 'k1perm' => array( '*' => array( '*' => '*' ) ) ) )->hasAccessToView( $this->module(), 'open', $params ) );
        $this->assertArrayNotHasKey( 'Limitation', $params );
    }

    public function testLimitedAccessToAViewHandsOnTheLimitations()
    {
        $limits = array( 'p_9' => array( 'Section' => array( 3 ) ) );
        $params = array();
        $user = $this->user( array( 'k1perm' => array( 'read' => $limits ) ) );
        $this->assertTrue( $user->hasAccessToView( $this->module(), 'read', $params ) );
        $this->assertSame( $limits, $params['Limitation'] );
        $this->assertSame( $limits, $GLOBALS['ezpolicylimitation_list'][self::USER_ID]['k1perm']['read'] );
    }

    public function testMissingFunctionInAViewSaysWhichOne()
    {
        $params = array();
        $this->assertFalse( $this->user( array( 'k1perm' => array( 'read' => array( '*' => '*' ) ) ) )->hasAccessToView( $this->module(), 'both', $params ) );
        $this->assertSame( 'edit', $params['accessList']['FunctionRequired']['Function'] );
    }

    public function testLimitedModuleAccessForAViewWithoutFunctions()
    {
        $limits = array( 'p_7' => array( 'Section' => array( 1 ) ) );
        $params = array();
        $this->assertTrue( $this->user( array( 'k1perm' => array( '*' => $limits ) ) )->hasAccessToView( $this->module(), 'open', $params ) );
        $this->assertSame( $limits, $params['Limitation'] );
        $this->assertSame( $limits, $GLOBALS['ezpolicylimitation_list'][self::USER_ID]['k1perm']['*'] );
    }
}
