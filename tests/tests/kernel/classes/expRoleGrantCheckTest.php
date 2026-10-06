<?php
/**
 * Whether a role editor may grant a policy: only what their own access array covers (expRoleGrantCheck). No database.
 *
 *  RG-01 - Every function of every module may grant anything; an empty access array grants nothing
 *  RG-02 - Module and function wildcards: every function of a module, of every module, one function; a grant over
 *          every function needs the same
 *  RG-03 - Limitations the editor has must be met as narrowly; ones the grant adds only narrow it
 *  RG-04 - Subtree containment: inside, the same, beside, above; User_Subtree; nodes inside a subtree
 *  RG-05 - Section, Class, Owner, Node, Language, SiteAccess, StateGroup_*: values among the editor's
 *  RG-06 - User_Section (a role assigned for a section) needs the grant's sections among it
 *  RG-07 - Two policies together cover a grant split by the values of one limitation
 *  RG-08 - Assignments narrow the grants: section, subtree, both ways; nothing for no limitation
 *  RG-09 - Normalising values; uncovered() keeps the keys of the refused grants
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRoleGrantCheckTest extends PHPUnit\Framework\TestCase
{
    private static function check( array $access )
    {
        $paths = array( 60 => '/1/2/55/60/', 70 => '/1/2/70/', 99 => null );
        return new expRoleGrantCheck( $access, function ( $id ) use ( $paths ) { return isset( $paths[$id] ) ? $paths[$id] : null; } );
    }

    public function testRG01Extremes()
    {
        $all = self::check( array( '*' => array( '*' => array( '*' => '*' ) ) ) );
        $this->assertTrue( $all->isUnlimited() );
        $this->assertTrue( $all->covers( '*', '*' ) );
        $this->assertTrue( $all->covers( 'role', '*' ) );
        $none = self::check( array() );
        $this->assertFalse( $none->isUnlimited() );
        $this->assertFalse( $none->covers( 'content', 'read' ) );
        $this->assertFalse( $none->covers( 'content', 'read', array( 'Section' => array( 1 ) ) ) );
        // every module but limited is not unlimited
        $limitedAll = self::check( array( '*' => array( '*' => array( 'p_1' => array( 'Section' => array( '1' ) ) ) ) ) );
        $this->assertFalse( $limitedAll->isUnlimited() );
        $this->assertTrue( $limitedAll->covers( 'content', 'read', array( 'Section' => array( 1 ) ) ) );
        $this->assertFalse( $limitedAll->covers( 'content', 'read' ) );
    }

    public function testRG02Wildcards()
    {
        $c = self::check( array( 'content' => array( '*' => array( '*' => '*' ) ), 'user' => array( 'login' => array( '*' => '*' ) ) ) );
        $this->assertTrue( $c->covers( 'content', 'read' ) );
        $this->assertTrue( $c->covers( 'content', '*' ) );
        $this->assertTrue( $c->covers( 'content', 'remove', array( 'Class' => array( 2 ) ) ) );
        $this->assertTrue( $c->covers( 'user', 'login' ) );
        $this->assertFalse( $c->covers( 'user', '*' ) );
        $this->assertFalse( $c->covers( 'user', 'password' ) );
        $this->assertFalse( $c->covers( '*', '*' ) );
        $this->assertFalse( $c->covers( 'role', '*' ) );
        $this->assertFalse( $c->covers( 'setup', 'administrate' ) );
    }

    public function testRG03LimitationsMustBeMet()
    {
        $c = self::check( array( 'content' => array( 'read' => array( 'p_5' => array( 'Section' => array( '1', '3' ), 'Class' => array( '2' ) ) ) ) ) );
        $this->assertTrue( $c->covers( 'content', 'read', array( 'Section' => array( 1 ), 'Class' => array( 2 ) ) ) );
        $this->assertTrue( $c->covers( 'content', 'read', array( 'Section' => array( 1, 3 ), 'Class' => array( 2 ), 'Language' => array( 'eng-GB' ) ) ) );
        $this->assertFalse( $c->covers( 'content', 'read', array( 'Section' => array( 1 ) ) ), 'the class limitation is missing' );
        $this->assertFalse( $c->covers( 'content', 'read', array( 'Section' => array( 1, 2 ), 'Class' => array( 2 ) ) ), 'section 2 is not held' );
        $this->assertFalse( $c->covers( 'content', 'read' ), 'no limitation at all' );
        $this->assertFalse( $c->covers( 'content', '*', array( 'Section' => array( 1 ), 'Class' => array( 2 ) ) ), 'every function' );
        $this->assertFalse( $c->covers( 'content', 'edit', array( 'Section' => array( 1 ), 'Class' => array( 2 ) ) ), 'another function' );
    }

    public function testRG04Subtrees()
    {
        $c = self::check( array( 'content' => array( 'edit' => array( 'p_7' => array( 'Subtree' => array( '/1/2/55/' ) ) ),
                                                     'read' => array( 'p_8_3' => array( 'User_Subtree' => array( '/1/2/55/' ) ) ) ) ) );
        $this->assertTrue( $c->covers( 'content', 'edit', array( 'Subtree' => array( '/1/2/55/' ) ) ), 'the same subtree' );
        $this->assertTrue( $c->covers( 'content', 'edit', array( 'Subtree' => array( '/1/2/55/60/', '/1/2/55/61/' ) ) ), 'inside' );
        $this->assertTrue( $c->covers( 'content', 'edit', array( 'Subtree' => array( '1/2/55/60' ) ) ), 'without the slashes' );
        $this->assertFalse( $c->covers( 'content', 'edit', array( 'Subtree' => array( '/1/2/' ) ) ), 'above' );
        $this->assertFalse( $c->covers( 'content', 'edit', array( 'Subtree' => array( '/1/2/5/' ) ) ), 'a sibling with the same prefix digits' );
        $this->assertFalse( $c->covers( 'content', 'edit', array( 'Subtree' => array( '/1/2/55/60/', '/1/2/70/' ) ) ), 'one beside' );
        $this->assertFalse( $c->covers( 'content', 'edit' ) );
        $this->assertTrue( $c->covers( 'content', 'edit', array( 'Node' => array( 60 ) ) ), 'a node inside the subtree' );
        $this->assertFalse( $c->covers( 'content', 'edit', array( 'Node' => array( 70 ) ) ), 'a node outside' );
        $this->assertFalse( $c->covers( 'content', 'edit', array( 'Node' => array( 99 ) ) ), 'a node that does not exist' );
        $this->assertTrue( $c->covers( 'content', 'edit', array( 'Subtree' => array( '/1/2/70/' ), 'Node' => array( 60 ) ) ), 'the nodes are inside' );
        // a role assigned for a subtree
        $this->assertTrue( $c->covers( 'content', 'read', array( 'Subtree' => array( '/1/2/55/60/' ) ) ) );
        $this->assertFalse( $c->covers( 'content', 'read', array( 'Section' => array( 1 ) ) ) );
        $this->assertTrue( expRoleGrantCheck::pathsInside( array(), array( '/1/' ) ) );
    }

    public function testRG05ValueLimitations()
    {
        $c = self::check( array( 'content' => array(
            'read'   => array( 'p_1' => array( 'Owner' => array( '1' ) ), 'p_2' => array( 'Node' => array( '60', '61' ) ) ),
            'create' => array( 'p_3' => array( 'Class' => array( '2', '16' ), 'ParentClass' => array( '1' ), 'Language' => array( 'eng-GB' ) ) ),
            'edit'   => array( 'p_4' => array( 'StateGroup_ez_lock' => array( '1' ) ) ) ),
            'user' => array( 'login' => array( 'p_5' => array( 'SiteAccess' => array( '1766001124' ) ) ) ) ) );
        $this->assertTrue( $c->covers( 'content', 'read', array( 'Owner' => array( 1 ) ) ) );
        $this->assertFalse( $c->covers( 'content', 'read', array( 'Owner' => array( 1, 2 ) ) ), 'owner or session is broader' );
        $this->assertTrue( $c->covers( 'content', 'read', array( 'Node' => array( 61 ) ) ) );
        $this->assertFalse( $c->covers( 'content', 'read', array( 'Node' => array( 62 ) ) ) );
        $this->assertTrue( $c->covers( 'content', 'create', array( 'Class' => array( 16 ), 'ParentClass' => array( 1 ), 'Language' => array( 'eng-GB' ) ) ) );
        $this->assertFalse( $c->covers( 'content', 'create', array( 'Class' => array( 16 ), 'ParentClass' => array( 1 ), 'Language' => array( 'ger-DE' ) ) ) );
        $this->assertFalse( $c->covers( 'content', 'create', array( 'Class' => array( 16 ), 'Language' => array( 'eng-GB' ) ) ) );
        $this->assertTrue( $c->covers( 'content', 'edit', array( 'StateGroup_ez_lock' => array( 1 ) ) ) );
        $this->assertFalse( $c->covers( 'content', 'edit', array( 'StateGroup_ez_lock' => array( 2 ) ) ) );
        $this->assertTrue( $c->covers( 'user', 'login', array( 'SiteAccess' => array( '1766001124' ) ) ) );
        $this->assertFalse( $c->covers( 'user', 'login' ) );
    }

    public function testRG06UserSection()
    {
        $c = self::check( array( 'content' => array( 'read' => array( 'p_9_4' => array( 'User_Section' => array( '3' ) ) ) ) ) );
        $this->assertTrue( $c->covers( 'content', 'read', array( 'Section' => array( 3 ) ) ) );
        $this->assertFalse( $c->covers( 'content', 'read', array( 'Section' => array( 3, 1 ) ) ) );
        $this->assertFalse( $c->covers( 'content', 'read' ) );
    }

    public function testRG07SplitAcrossPolicies()
    {
        $c = self::check( array( 'content' => array( 'read' => array( 'p_1' => array( 'Section' => array( '1' ) ), 'p_2' => array( 'Section' => array( '3' ) ),
                                                                      'p_3' => array( 'Section' => array( '5' ), 'Class' => array( '2' ) ) ) ) ) );
        $this->assertTrue( $c->covers( 'content', 'read', array( 'Section' => array( 1, 3 ) ) ), 'each section by its own policy' );
        $this->assertFalse( $c->covers( 'content', 'read', array( 'Section' => array( 1, 5 ) ) ), 'section 5 needs a class' );
        $this->assertTrue( $c->covers( 'content', 'read', array( 'Section' => array( 1, 5 ), 'Class' => array( 2 ) ) ) );
        $this->assertFalse( $c->covers( 'content', 'read', array( 'Section' => array( 1, 3, 4 ) ) ) );
    }

    public function testRG08AssignmentNarrows()
    {
        $grants = array( 10 => array( 'module' => 'content', 'function' => 'read', 'limitations' => array() ),
                         11 => array( 'module' => 'content', 'function' => 'read', 'limitations' => array( 'Section' => array( 1, 2 ) ) ),
                         12 => array( 'module' => 'content', 'function' => 'edit', 'limitations' => array( 'Subtree' => array( '/1/2/55/60/', '/1/2/70/' ) ) ) );
        $s = expRoleGrantCheck::narrowByAssignment( $grants, 'section', '1' );
        $this->assertSame( array( '1' ), $s[10]['limitations']['Section'] );
        $this->assertSame( array( '1' ), $s[11]['limitations']['Section'] );
        $t = expRoleGrantCheck::narrowByAssignment( $grants, 'subtree', '/1/2/55/' );
        $this->assertSame( array( '/1/2/55/' ), $t[10]['limitations']['Subtree'] );
        $this->assertSame( array( '/1/2/55/60/' ), $t[12]['limitations']['Subtree'] );
        $u = expRoleGrantCheck::narrowByAssignment( $grants, 'Subtree', '/1/2/55/60/61/' );
        $this->assertSame( array( '/1/2/55/60/61/' ), $u[12]['limitations']['Subtree'] );
        $this->assertSame( $grants, expRoleGrantCheck::narrowByAssignment( $grants, '', '' ) );

        // an editor of subtree 55 may assign an unlimited content/read role only for that subtree
        $c = self::check( array( 'content' => array( 'read' => array( 'p_1' => array( 'Subtree' => array( '/1/2/55/' ) ) ) ) ) );
        $read = array( $grants[10] );
        $this->assertCount( 1, $c->uncovered( $read ) );
        $this->assertCount( 0, $c->uncovered( expRoleGrantCheck::narrowByAssignment( $read, 'subtree', '/1/2/55/60/' ) ) );
        $this->assertCount( 1, $c->uncovered( expRoleGrantCheck::narrowByAssignment( $read, 'subtree', '/1/2/' ) ) );
        // a section outside an own section: the narrowed grant has no section at all and grants nothing; covered
        $d = self::check( array( 'content' => array( 'read' => array( 'p_1' => array( 'Section' => array( '1' ) ) ) ) ) );
        $narrowed = expRoleGrantCheck::narrowByAssignment( array( $grants[11] ), 'section', '7' );
        $this->assertSame( array(), $narrowed[0]['limitations']['Section'] );
        $this->assertCount( 0, $d->uncovered( $narrowed ) );
    }

    public function testRG09NormaliseAndKeys()
    {
        $this->assertSame( array( 'Section' => array( '1', '2' ), 'Owner' => array( '1' ) ),
                           expRoleGrantCheck::normalise( array( 'Section' => array( 1, '2', 1, array( 9 ) ), 'Owner' => 1 ) ) );
        $c = self::check( array( 'content' => array( 'read' => array( '*' => '*' ) ) ) );
        $refused = $c->uncovered( array( 7 => array( 'module' => 'content', 'function' => 'read' ),
                                         8 => array( 'module' => 'content', 'function' => 'edit' ),
                                         9 => array( 'module' => '*', 'function' => '*', 'limitations' => array() ) ) );
        $this->assertSame( array( 8, 9 ), array_keys( $refused ) );
        $this->assertTrue( expRoleGrantCheck::subset( array(), array() ) );
        $this->assertFalse( expRoleGrantCheck::subset( array( '1' ), array() ) );
    }

    public function testRG10ActivationPlan()
    {
        $this->assertSame( array( 'way' => 'resume', 'mail' => true ), expUserActivation::plan( true, false, 'enabled', 'disabled' ) );
        $this->assertSame( array( 'way' => 'steps', 'mail' => true ), expUserActivation::plan( false, true, 'enabled', 'enabled' ) );
        $this->assertSame( array( 'way' => 'steps', 'mail' => false ), expUserActivation::plan( false, true, 'enabled', 'disabled' ) );
        $this->assertSame( array( 'way' => 'resume', 'mail' => false ), expUserActivation::plan( true, true, 'enabled', 'disabled' ) );
        $this->assertSame( array( 'way' => 'resume', 'mail' => false ), expUserActivation::plan( true, false, 'disabled', 'enabled' ) );
        $this->assertTrue( expUserActivation::isRegisterMemento( array( 'module_name' => 'user', 'operation_name' => 'register' ) ) );
        $this->assertFalse( expUserActivation::isRegisterMemento( array( 'module_name' => 'user', 'operation_name' => 'activation' ) ) );
        $this->assertFalse( expUserActivation::isRegisterMemento( null ) );
    }
}
