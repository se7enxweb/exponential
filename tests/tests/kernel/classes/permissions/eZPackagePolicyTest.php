<?php
/**
 * Tests of the package policy checks, without the database: eZPackage::canUsePolicyFunction() and the per package
 * checks (read, export, import, install) with the policy's Type limitation, and fetchMaintainerRoleIDList(), the
 * maintainer roles a user may choose under package/create, limited by Role and by Type.
 *
 * The current user is a stand-in (the anonymous user id) whose access array a test sets; it is put back in
 * tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class eZPackagePolicyTest extends PHPUnit\Framework\TestCase
{
    private $hadUser;
    private $user;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->hadUser = array_key_exists( 'eZUserGlobalInstance_', $GLOBALS );
        $this->user = $this->hadUser ? $GLOBALS['eZUserGlobalInstance_'] : null;
    }

    protected function tearDown(): void
    {
        if ( $this->hadUser )
            $GLOBALS['eZUserGlobalInstance_'] = $this->user;
        else
            unset( $GLOBALS['eZUserGlobalInstance_'] );
    }

    private function access( array $packageFunctions )
    {
        $user = new eZUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'k1anonymous', 'email' => 'k1@k1.example.invalid' ) );
        $user->AccessArray = $packageFunctions ? array( 'package' => $packageFunctions ) : array();
        $GLOBALS['eZUserGlobalInstance_'] = $user;
        $this->assertSame( $user, eZUser::currentUser() );
    }

    private function package( $type )
    {
        return new eZPackage( array( 'name' => 'k1_policy', 'type' => $type ), 'var/tmp' );
    }

    public function testNoPolicyNoAccess()
    {
        $this->access( array() );
        $this->assertFalse( eZPackage::canUsePolicyFunction( 'read' ) );
        $this->assertFalse( $this->package( 'contentclass' )->canRead() );
        $this->assertFalse( $this->package( 'contentclass' )->attribute( 'can_install' ) );
    }

    public function testFullAccess()
    {
        $this->access( array( '*' => array( '*' => '*' ) ) );
        $this->assertTrue( eZPackage::canUsePolicyFunction( 'install' ) );
        $package = $this->package( 'contentclass' );
        $this->assertTrue( $package->canRead() );
        $this->assertTrue( $package->canExport() );
        $this->assertTrue( $package->canImport() );
        $this->assertTrue( $package->canInstall() );
    }

    public function testTypeLimitation()
    {
        $this->access( array( 'install' => array( 'p_1' => array( 'Type' => array( 'contentclass' ) ),
                                                  'p_2' => array( 'Type' => array( 'site' ) ) ),
                              'read' => array( 'p_3' => array( 'Role' => array( 'lead' ) ) ) ) );
        $this->assertTrue( eZPackage::canUsePolicyFunction( 'install' ), 'limited access is access to the function' );
        $this->assertTrue( $this->package( 'contentclass' )->canInstall() );
        $this->assertTrue( $this->package( 'site' )->canInstall() );
        $this->assertFalse( $this->package( 'design' )->canInstall() );
        $this->assertTrue( $this->package( 'design' )->canRead(), 'a policy without a Type limitation allows every type' );
        $this->assertFalse( $this->package( 'design' )->canExport() );
    }

    public function testPolicyResultsAreRememberedPerPackage()
    {
        $this->access( array( '*' => array( '*' => '*' ) ) );
        $package = $this->package( 'design' );
        $this->assertTrue( $package->canInstall() );
        $this->access( array() );
        $this->assertTrue( $package->canInstall() );
        $this->assertFalse( $this->package( 'design' )->canInstall() );
    }

    public function testAllMaintainerRolesWithoutChecking()
    {
        $this->assertSame( array( 'lead', 'developer', 'designer', 'contributor', 'tester' ), eZPackage::fetchMaintainerRoleIDList() );
        $this->assertSame( 'Lead', eZPackage::maintainerRoleName( 'lead' ) );
        $this->assertFalse( eZPackage::maintainerRoleName( 'k1nosuchrole' ) );
        $this->assertSame( array( 'lead', 'developer', 'designer', 'contributor', 'tester' ), array_column( eZPackage::maintainerRoleListForRoles(), 'id' ) );
    }

    public function testMaintainerRolesWithFullAccess()
    {
        $this->access( array( 'create' => array( '*' => '*' ) ) );
        $this->assertCount( 5, eZPackage::fetchMaintainerRoleIDList( false, true ) );
    }

    public function testMaintainerRolesLimitedByRole()
    {
        $this->access( array( 'create' => array( 'p_1' => array( 'Role' => array( 'lead', 'tester' ) ) ) ) );
        $this->assertSame( array( 'lead', 'tester' ), array_values( eZPackage::fetchMaintainerRoleIDList( false, true ) ) );
        $this->assertSame( array( 'lead', 'tester' ), array_column( eZPackage::fetchMaintainerRoleList( false, true ), 'id' ) );
    }

    public function testMaintainerRolesLimitedByRoleAndType()
    {
        $this->access( array( 'create' => array( 'p_1' => array( 'Role' => array( 'lead' ), 'Type' => array( 'contentclass' ) ),
                                                 'p_2' => array( 'Role' => array( 'designer' ), 'Type' => array( 'design' ) ) ) ) );
        $this->assertSame( array( 'lead' ), array_values( eZPackage::fetchMaintainerRoleIDList( 'contentclass', true ) ) );
        $this->assertSame( array( 'designer' ), array_values( eZPackage::fetchMaintainerRoleIDList( 'design', true ) ) );
        $this->assertSame( array( 'lead', 'designer' ), array_values( eZPackage::fetchMaintainerRoleIDList( false, true ) ), 'no package type yet: every policy counts' );
        $this->assertSame( array(), eZPackage::fetchMaintainerRoleIDList( 'site', true ) );
    }

    public function testMaintainerRolesLimitedByTypeOnly()
    {
        $this->access( array( 'create' => array( 'p_1' => array( 'Type' => array( 'contentclass' ) ) ) ) );
        $this->assertCount( 5, eZPackage::fetchMaintainerRoleIDList( 'contentclass', true ), 'no Role limitation: every role' );
        $this->assertSame( array(), eZPackage::fetchMaintainerRoleIDList( 'design', true ) );
    }

    public function testMaintainerStepIsSkippedWhenTheUserIsAlreadyAMaintainer()
    {
        // The stand-in is the anonymous user (an anonymous current user needs no database); its content object
        // comes from the in-memory object cache, as an object without id that answers with its own name
        $objectID = eZUser::anonymousId();
        $user = new eZUser( array( 'contentobject_id' => $objectID, 'login' => 'k1anonymous', 'email' => 'k1@k1.example.invalid' ) );
        $user->AccessArray = array( 'package' => array( 'create' => array( '*' => '*' ) ) );
        $GLOBALS['eZUserGlobalInstance_'] = $user;
        $hadCached = isset( $GLOBALS['eZContentObjectContentObjectCache'][$objectID] );
        $cached = $hadCached ? $GLOBALS['eZContentObjectContentObjectCache'][$objectID] : null;
        $GLOBALS['eZContentObjectContentObjectCache'][$objectID] = new eZContentObject( array( 'id' => null, 'name' => 'Ada Example' ) );
        try
        {
            $creator = new eZPackageCreationHandler( 'k1', 'K1', array() );
            $data = array();
            $package = new eZPackage( array( 'name' => 'k1_maintained' ), 'var/tmp' );
            $this->assertTrue( $creator->checkPackageMaintainer( $package, $data ), 'not a maintainer yet' );
            $package->appendMaintainer( 'Ada Example', 'ada@k1.example.invalid', 'lead' );
            $this->assertFalse( $creator->checkPackageMaintainer( $package, $data ), 'already a maintainer: the step is left out' );
            $this->assertTrue( $creator->checkPackageMaintainer( false, $data ), 'a package not created yet' );
        }
        finally
        {
            if ( $hadCached )
                $GLOBALS['eZContentObjectContentObjectCache'][$objectID] = $cached;
            else
                unset( $GLOBALS['eZContentObjectContentObjectCache'][$objectID] );
        }
    }
}
