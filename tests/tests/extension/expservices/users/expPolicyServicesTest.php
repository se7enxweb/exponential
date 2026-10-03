<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expUsersTestCase.php';

class expPolicyServicesTest extends expUsersTestCase
{
    public function testModulesAndFunctions()
    {
        $this->assertContains( 'content', $this->okCall( 'expPolicyServices', 'modules' ) );
        $this->assertContains( 'read', $this->okCall( 'expPolicyServices', 'functions', array( 'content' ) ) );
        $this->assertContains( 'Class', $this->okCall( 'expPolicyServices', 'availableLimitations', array( 'content', 'read' ) ) );
        $this->assertError( $this->call( 'expPolicyServices', 'functions', array( 'nomodule' ) ), 404 );
        $this->assertError( $this->call( 'expPolicyServices', 'availableLimitations', array( 'content', 'nofunction' ) ), 404 );
    }

    public function testLimitationValuesArePaged()
    {
        $r = $this->call( 'expPolicyServices', 'limitationValues', array( 'content', 'read', 'Section', '2' ) );
        $this->assertPaged( $r );
        $this->assertLessThanOrEqual( 2, count( $r['data'] ) );
        $this->assertError( $this->call( 'expPolicyServices', 'limitationValues', array( 'content', 'read', 'Bogus' ) ), 404 );
    }

    public function testAddWithLimitationsAndSummary()
    {
        $role = $this->newRole();
        $p = $this->okWrite( 'expPolicyServices', 'add', array( 'role' => $role, 'module' => 'content', 'function' => 'read', 'limitations' => json_encode( array( 'Section' => array( '1', '3' ) ) ) ) );
        $this->assertSame( array( '1', '3' ), $p['limitations']->Section );
        $this->assertStringContainsString( 'content/read where Section in (1, 3)', $this->okCall( 'expPolicyServices', 'summary', array( (string)$p['id'] ) )['text'] );
        $this->assertSame( $p['id'], $this->okCall( 'expPolicyServices', 'fetch', array( (string)$p['id'] ) )['id'] );
        $this->assertCount( 1, (array)$this->okCall( 'expPolicyServices', 'limitations', array( (string)$p['id'] ) ) );
        $this->assertCount( 1, $this->call( 'expPolicyServices', 'listOfRole', array( (string)$role ) )['data'] );
    }

    public function testAddValidatesTheModule()
    {
        $role = $this->newRole();
        $this->assertError( $this->write( 'expPolicyServices', 'add', array( 'role' => $role, 'module' => 'nomodule', 'function' => 'x' ) ), 404 );
        $this->assertError( $this->write( 'expPolicyServices', 'add', array( 'role' => $role, 'module' => 'content', 'function' => 'nofunction' ) ), 422 );
        $this->assertError( $this->write( 'expPolicyServices', 'add', array( 'role' => $role, 'module' => 'content', 'function' => 'read', 'limitations' => '{"Bogus":["1"]}' ) ), 422 );
        $this->assertError( $this->write( 'expPolicyServices', 'add', array( 'role' => $role, 'module' => 'content', 'function' => '*', 'limitations' => '{"Class":["1"]}' ) ), 422 );
        $this->assertError( $this->write( 'expPolicyServices', 'add', array( 'role' => $role, 'module' => 'content', 'function' => 'read', 'limitations' => '{bad' ) ), 400 );
    }

    public function testSetAddAndRemoveLimitation()
    {
        $role = $this->newRole( array( array( 'content', 'read', array( 'Section' => array( '1' ) ) ) ) );
        $id = $this->okCall( 'expRoleServices', 'view', array( (string)$role ) )['policies'][0]['id'];
        $p = $this->okWrite( 'expPolicyServices', 'setLimitations', array( 'id' => $id, 'limitations' => json_encode( array( 'Section' => array( '2' ) ) ) ) );
        $this->assertSame( array( '2' ), $p['limitations']->Section );
        $p = $this->okWrite( 'expPolicyServices', 'addLimitation', array( 'id' => $id, 'identifier' => 'Class', 'values' => '1,2' ) );
        $this->assertSame( array( '1', '2' ), $p['limitations']->Class );
        $this->assertError( $this->write( 'expPolicyServices', 'addLimitation', array( 'id' => $id, 'identifier' => 'Class', 'values' => '3' ) ), 409 );
        $p = $this->okWrite( 'expPolicyServices', 'removeLimitation', array( 'id' => $id, 'identifier' => 'Class' ) );
        $this->assertArrayNotHasKey( 'Class', (array)$p['limitations'] );
        $this->assertError( $this->write( 'expPolicyServices', 'removeLimitation', array( 'id' => $id, 'identifier' => 'Class' ) ), 404 );
    }

    public function testCopyAndRemove()
    {
        $a = $this->newRole( array( array( 'content', 'read' ) ) );
        $b = $this->newRole();
        $id = $this->okCall( 'expRoleServices', 'view', array( (string)$a ) )['policies'][0]['id'];
        $copy = $this->okWrite( 'expPolicyServices', 'copy', array( 'id' => $id, 'role' => $b ) );
        $this->assertSame( $b, $copy['role_id'] );
        $this->assertTrue( $this->okWrite( 'expPolicyServices', 'remove', array( 'id' => $copy['id'] ) )['removed'] );
        $this->assertError( $this->call( 'expPolicyServices', 'fetch', array( (string)$copy['id'] ) ), 404 );
    }

    public function testOfModuleAndFindByLimitation()
    {
        $role = $this->newRole( array( array( 'content', 'read', array( 'Section' => array( '9999' ) ) ) ) );
        $this->assertPaged( $this->call( 'expPolicyServices', 'ofModule', array( 'content', '5' ) ) );
        $r = $this->call( 'expPolicyServices', 'findByLimitation', array( 'Section', '9999' ) );
        $this->assertPaged( $r );
        $this->assertContains( $role, array_column( $r['data'], 'role_id' ) );
    }

    public function testDraftPoliciesAreNotAddedDirectly()
    {
        $role = $this->newRole();
        $draft = $this->okWrite( 'expRoleServices', 'draftCreate', array( 'id' => $role ) );
        $this->assertError( $this->write( 'expPolicyServices', 'add', array( 'role' => $draft['id'], 'module' => 'content', 'function' => 'read' ) ), 409 );
        $this->okWrite( 'expRoleServices', 'draftDiscard', array( 'id' => $role ) );
    }
}
