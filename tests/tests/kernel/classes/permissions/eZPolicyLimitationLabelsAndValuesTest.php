<?php
/**
 * How the role screens name and store limitations, including those an extension adds through the filter
 * module/functionlist, without the database:
 *
 *  PL-01 - A limitation is found under its key or by its name
 *  PL-02 - The label of a limitation is its 'label' when it has one, otherwise its name
 *  PL-03 - The offered values come from 'values' or from the 'class' and 'function' that list them
 *  PL-04 - Posted values that were not offered are not stored; "Any", duplicates, no list, Node and Subtree
 *  PL-05 - A stored limitation shows its label, and its values even when its module no longer defines it
 *  PL-06 - A content limitation that no handler evaluates is marked as denying; kernel ones and other modules not
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

/** Lists values the way the content module's limitation classes do */
class X1PolicyLimitationLister
{
    public function __construct( $row )
    {
    }

    public function regions( $prefix )
    {
        return array( array( 'id' => 1, 'name' => "$prefix North" ), array( 'id' => '2', 'name' => "$prefix South" ) );
    }
}

/** A stored limitation whose policy and values are given instead of fetched */
class X1PolicyLimitationStandIn extends eZPolicyLimitation
{
    public $standInPolicy;
    public $standInValues = array();

    function policy()
    {
        return $this->standInPolicy;
    }

    function allValues()
    {
        return $this->standInValues;
    }
}

class eZPolicyLimitationLabelsAndValuesTest extends PHPUnit\Framework\TestCase
{
    private $pathList;
    private $hadPathList;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->hadPathList = array_key_exists( 'eZModuleGlobalPathList', $GLOBALS );
        $this->pathList = $this->hadPathList ? $GLOBALS['eZModuleGlobalPathList'] : null;
        ezpContentLimitation::resetCache();
    }

    protected function tearDown(): void
    {
        if ( $this->hadPathList )
            $GLOBALS['eZModuleGlobalPathList'] = $this->pathList;
        else
            unset( $GLOBALS['eZModuleGlobalPathList'] );
        ezpINIHelper::restoreINISettings();
    }

    private function functions()
    {
        return array( 'read' => array( 'Region' => array( 'name' => 'Region', 'label' => 'Sales region',
                                                          'values' => array( array( 'Name' => 'North', 'value' => '1' ),
                                                                             array( 'Name' => 'South', 'value' => '2' ) ) ),
                                       'k' => array( 'name' => 'Keyed', 'values' => array() ),
                                       'Dynamic' => array( 'name' => 'Dynamic', 'values' => array(), 'class' => 'X1PolicyLimitationLister',
                                                           'function' => 'regions', 'parameter' => array( 'x1' ) ),
                                       'Node' => array( 'name' => 'Node', 'values' => array() ) ) );
    }

    /** PL-01 */
    public function testADefinitionIsFoundByKeyOrName()
    {
        $this->assertSame( 'Region', eZPolicyLimitation::findDefinition( $this->functions(), 'read', 'Region' )['name'] );
        $this->assertSame( 'Keyed', eZPolicyLimitation::findDefinition( $this->functions(), 'read', 'Keyed' )['name'] );
        $this->assertNull( eZPolicyLimitation::findDefinition( $this->functions(), 'read', 'Gone' ) );
        $this->assertNull( eZPolicyLimitation::findDefinition( $this->functions(), 'edit', 'Region' ) );
        $this->assertNull( eZPolicyLimitation::findDefinition( false, 'read', 'Region' ) );
    }

    /** PL-02 */
    public function testTheLabelOfADefinition()
    {
        $functions = $this->functions();
        $this->assertSame( 'Sales region', eZPolicyLimitation::definitionLabel( $functions['read']['Region'] ) );
        $this->assertSame( 'Keyed', eZPolicyLimitation::definitionLabel( $functions['read']['k'] ) );
        $this->assertSame( 'Keyed', eZPolicyLimitation::definitionLabel( array( 'name' => 'Keyed', 'label' => '  ' ) ) );
        $this->assertSame( 'Keyed', eZPolicyLimitation::definitionLabel( array( 'name' => 'Keyed', 'label' => array( 'x' ) ) ) );
    }

    /** PL-03 */
    public function testOfferedValues()
    {
        $functions = $this->functions();
        $this->assertSame( array( '1', '2' ), eZPolicyLimitation::offeredValues( $functions['read']['Region'] ) );
        $this->assertSame( array( '1', '2' ), eZPolicyLimitation::offeredValues( $functions['read']['Dynamic'] ) );
        $this->assertNull( eZPolicyLimitation::offeredValues( $functions['read']['Node'] ) );
        $this->assertNull( eZPolicyLimitation::offeredValues( array( 'name' => 'X', 'class' => 'X1NoSuchListerClass', 'function' => 'all' ) ) );
    }

    /** PL-04 */
    public function testOnlyOfferedValuesAreStored()
    {
        $functions = $this->functions();
        $this->assertSame( array( '1' ), eZPolicyLimitation::validValues( $functions['read']['Region'], array( '1', '3', '1) OR (1=1', '1' ) ) );
        $this->assertSame( array( 2 ), eZPolicyLimitation::validValues( $functions['read']['Dynamic'], array( 2, 9 ) ) );
        $this->assertSame( array( '-1' ), eZPolicyLimitation::validValues( $functions['read']['Region'], array( '-1' ) ) );
        $this->assertSame( array(), eZPolicyLimitation::validValues( $functions['read']['Region'], array( '7' ) ) );
        // a single value instead of a list (Region=1 instead of Region[]=1) made in_array() end the request
        $this->assertSame( array(), eZPolicyLimitation::validValues( $functions['read']['Region'], '1' ) );
        $this->assertSame( array( '1' ), eZPolicyLimitation::validValues( $functions['read']['Region'], array( '1', array( '2' ) ) ) );
        // Node and Subtree are picked in the content browser, not from a list
        $this->assertSame( array( '43', '44' ), eZPolicyLimitation::validValues( $functions['read']['Node'], array( '43', '44', '43' ) ) );
    }

    private function stored( $module, $function, $identifier, array $values )
    {
        $limitation = new X1PolicyLimitationStandIn( array( 'id' => 990501, 'policy_id' => 990502, 'identifier' => $identifier ) );
        $limitation->standInPolicy = new eZPolicy( array( 'id' => 990502, 'role_id' => 1, 'module_name' => $module, 'function_name' => $function ) );
        $limitation->standInValues = $values;
        return $limitation;
    }

    /** PL-05 */
    public function testAStoredLimitationShowsItsLabelAndValues()
    {
        $GLOBALS['eZModuleGlobalPathList'] = array( __DIR__ . '/fixtures/modules' );
        $event = ezpEvent::getInstance();
        $id = $event->attach( 'module/functionlist', function ( $functionList, $moduleName )
        {
            if ( $moduleName === 'k1perm' )
                $functionList['read']['Region'] = array( 'name' => 'Region', 'label' => 'Sales region',
                                                         'values' => array( array( 'Name' => 'North', 'value' => '1' ),
                                                                            array( 'Name' => 'South', 'value' => '2' ) ) );
            return $functionList;
        } );
        try
        {
            $region = $this->stored( 'k1perm', 'read', 'Region', array( '2' ) );
            $this->assertSame( 'Sales region', $region->attribute( 'label' ) );
            $this->assertSame( array( array( 'Name' => 'South', 'value' => '2' ) ), $region->attribute( 'values_as_array_with_names' ) );
        }
        finally
        {
            $event->detach( 'module/functionlist', $id );
        }
        // the extension is gone: the identifier and the stored values are shown, without a warning
        $gone = $this->stored( 'k1perm', 'read', 'Region', array( '2', '5' ) );
        $this->assertSame( 'Region', $gone->attribute( 'label' ) );
        $this->assertSame( array( array( 'Name' => '2', 'value' => '2' ), array( 'Name' => '5', 'value' => '5' ) ),
                           $gone->attribute( 'values_as_array_with_names' ) );
    }

    /** PL-06 */
    public function testAContentLimitationWithoutAHandlerIsMarked()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'RoleSettings', 'LimitationHandlers', array( 'Handled' => 'X1PermissionSQLLimitationHandlerForLabels' ) );
        $this->assertTrue( $this->stored( 'content', 'read', 'Region', array( '1' ) )->attribute( 'denies_without_handler' ) );
        $this->assertTrue( $this->stored( '*', '*', 'Region', array( '1' ) )->attribute( 'denies_without_handler' ) );
        $this->assertFalse( $this->stored( 'content', 'read', 'Section', array( '1' ) )->attribute( 'denies_without_handler' ) );
        $this->assertFalse( $this->stored( 'content', 'read', 'StateGroup_ez_lock', array( '1' ) )->attribute( 'denies_without_handler' ) );
        $this->assertFalse( $this->stored( 'content', 'read', 'Handled', array( '1' ) )->attribute( 'denies_without_handler' ) );
        $this->assertFalse( $this->stored( 'user', 'login', 'SiteAccess', array( '1' ) )->attribute( 'denies_without_handler' ) );
    }
}

class X1PermissionSQLLimitationHandlerForLabels implements ezpContentLimitationHandler
{
    public function checkAccess( $limitation, array $values, $functionName, $subject, $userID )
    {
        return true;
    }

    public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
    {
        return false;
    }
}
