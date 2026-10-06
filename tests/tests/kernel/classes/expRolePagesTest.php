<?php
/**
 * What the role pages and the unactivated users page work out before they show anything. No database.
 *
 *  RP-01 - A policy in words: action, conditions, the sentence; every module; every function of a module
 *  RP-02 - Each kernel limitation has its own wording, singular and plural; others fall back to "label: values"
 *  RP-03 - A limitation without values, with a "denies" note; lists of values are joined and cut after six
 *  RP-04 - Full access and "manages roles" flags; grouping by module keeps order
 *  RP-05 - Every text the sentence builder produces is in sourceTexts() (for the translation file)
 *  RP-06 - The role list: search, the orders, the paging, the normalised address parts
 *  RP-07 - Comparing a draft with the saved role: signatures ignore value order but not policy order
 *  RP-08 - "Assign with limitation" takes subtree or section only
 *  RP-09 - Unactivated users: order, search, ids, age, LIKE pattern, activation address, new hash
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRolePagesTest extends PHPUnit\Framework\TestCase
{
    /** @var array every source text translated during a test */
    private $seen = array();

    private function sentence()
    {
        $seen = &$this->seen;
        return new expRolePolicySentence( function ( $source, $args ) use ( &$seen ) {
            $seen[$source] = true;
            return strtr( $source, $args );
        } );
    }

    private static function lim( $identifier, array $values, $label = null, $denies = false )
    {
        return array( 'identifier' => $identifier, 'label' => $label === null ? $identifier : $label, 'values' => $values, 'denies' => $denies );
    }

    public function testRP01PolicyInWords()
    {
        $s = $this->sentence();
        $d = $s->describe( array( 'module' => 'content', 'function' => 'read', 'limitations' => array(
            self::lim( 'Section', array( 'Standard' ) ), self::lim( 'Class', array( 'Article', 'Folder' ) ) ) ) );
        $this->assertSame( 'May read content, in section Standard, of the classes Article and Folder', $d['sentence'] );
        $this->assertSame( 'read content', $d['action'] );
        $this->assertCount( 2, $d['conditions'] );
        $this->assertFalse( $d['full_access'] );
        $this->assertFalse( $d['all_functions'] );

        $d = $s->describe( array( 'module' => '*', 'function' => '*', 'limitations' => array() ) );
        $this->assertSame( 'May do everything, in every module', $d['sentence'] );
        $this->assertTrue( $d['full_access'] );

        $d = $s->describe( array( 'module' => 'shop', 'function' => '*' ) );
        $this->assertSame( 'May do everything in the shop', $d['sentence'] );
        $this->assertTrue( $d['all_functions'] );

        $d = $s->describe( array( 'module' => 'frobnicate', 'function' => '*' ) );
        $this->assertSame( 'May use every function of the frobnicate module', $d['sentence'] );
        $d = $s->describe( array( 'module' => 'frobnicate', 'function' => 'twist' ) );
        $this->assertSame( 'May use the function twist of the frobnicate module', $d['sentence'] );
        $d = $s->describe( array( 'module' => 'user', 'function' => 'login', 'limitations' => array( self::lim( 'SiteAccess', array( 'site', 'bold' ) ) ) ) );
        $this->assertSame( 'May log in, on the siteaccesses site and bold', $d['sentence'] );
    }

    public function testRP02LimitationWordings()
    {
        $s = $this->sentence();
        $cases = array(
            array( 'Section', array( 'A' ), 'in section A' ), array( 'Section', array( 'A', 'B' ), 'in the sections A and B' ),
            array( 'Class', array( 'A' ), 'of class A' ), array( 'ParentClass', array( 'A', 'B' ), 'below objects of the classes A and B' ),
            array( 'ParentDepth', array( '2' ), 'at depth 2' ), array( 'Owner', array( 'Self' ), 'only content they own' ),
            array( 'ParentOwner', array( 'Self' ), 'only below content they own' ),
            array( 'Group', array( 'Self' ), 'only content owned by a member of their groups' ),
            array( 'ParentGroup', array( 'Self' ), 'only below content owned by a member of their groups' ),
            array( 'Node', array( 'Home' ), 'on the node Home' ), array( 'Node', array( 'A', 'B' ), 'on the nodes A and B' ),
            array( 'Subtree', array( 'Media' ), 'in the subtree Media' ), array( 'Subtree', array( 'A', 'B' ), 'in the subtrees A and B' ),
            array( 'SiteAccess', array( 'site' ), 'on the siteaccess site' ), array( 'Language', array( 'German' ), 'in the language German' ),
            array( 'Language', array( 'German', 'French' ), 'in the languages German and French' ),
            array( 'NewSection', array( 'A' ), 'into section A' ), array( 'NewSection', array( 'A', 'B' ), 'into the sections A and B' ),
            array( 'NewState', array( 'Locked' ), 'to the state Locked' ), array( 'NewState', array( 'A', 'B' ), 'to the states A and B' ),
            array( 'FunctionList', array( 'f' ), 'only the function f' ), array( 'FunctionList', array( 'f', 'g' ), 'only the functions f and g' ),
            array( 'Class', array( 'A', 'B', 'C' ), 'of the classes A, B and C' ), array( 'ParentClass', array( 'A' ), 'below objects of class A' ),
            array( 'ParentDepth', array( '1', '2' ), 'at the depths 1 and 2' ), array( 'SiteAccess', array( 'a', 'b' ), 'on the siteaccesses a and b' ),
        );
        foreach ( $cases as $case )
            $this->assertSame( $case[2], $s->condition( self::lim( $case[0], $case[1] ) ), $case[0] );
        $this->assertSame( 'in the state Draft (Lock)', $s->condition( self::lim( 'StateGroup_ez_lock', array( 'Draft' ), 'Lock' ) ) );
        $this->assertSame( 'in the states A and B (Lock)', $s->condition( self::lim( 'StateGroup_ez_lock', array( 'A', 'B' ), 'Lock' ) ) );
        $this->assertSame( 'Tag: News, Sport', $s->condition( self::lim( 'Tag', array( 'News, Sport' ) ) ) );
        $this->assertSame( 'Keyword: a and b', $s->condition( self::lim( 'Keywords', array( 'a', 'b' ), 'Keyword' ) ) );
        // a label that is empty falls back to the identifier
        $this->assertSame( 'Keywords: a', $s->condition( self::lim( 'Keywords', array( 'a' ), ' ' ) ) );
    }

    public function testRP03EmptyDeniesAndLongLists()
    {
        $s = $this->sentence();
        $this->assertSame( 'Subtree: none of the chosen values exists any more', $s->condition( self::lim( 'Subtree', array() ) ) );
        $this->assertSame( 'Subtree: none of the chosen values exists any more', $s->condition( self::lim( 'Subtree', array( '', ' ', array() ) ) ) );
        $d = $s->describe( array( 'module' => 'content', 'function' => 'read', 'limitations' => array( self::lim( 'Foo', array( 'x' ), null, true ) ) ) );
        $this->assertTrue( $d['denies'] );
        $this->assertSame( '', $s->joinList( array() ) );
        $this->assertSame( 'a', $s->joinList( array( 'a' ) ) );
        $this->assertSame( 'a, b, c, d, e, f and g', $s->joinList( array( 'a', 'b', 'c', 'd', 'e', 'f', 'g' ) ) );
        $this->assertSame( 'a, b, c, d, e, f and 3 more', $s->joinList( array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i' ) ) );
        // markup is kept as text: the templates escape it
        $this->assertSame( 'in section <b>x</b>', $s->condition( self::lim( 'Section', array( '<b>x</b>' ) ) ) );
    }

    public function testRP04FlagsAndGroups()
    {
        $this->assertTrue( expRolePolicySentence::managesRoles( 'role', '*' ) );
        $this->assertTrue( expRolePolicySentence::managesRoles( '*', '*' ) );
        $this->assertFalse( expRolePolicySentence::managesRoles( 'content', 'read' ) );
        $groups = expRolePolicySentence::groupByModule( array(
            10 => array( 'module' => 'content' ), 11 => array( 'module' => 'user' ), 12 => array( 'module' => 'content' ), 13 => 'nonsense' ) );
        $this->assertSame( array( 'content', 'user', '' ), array_map( 'strval', array_keys( $groups ) ) );
        $this->assertSame( array( 10, 12 ), array_keys( $groups['content'] ) );
    }

    public function testRP05SourceTextsAreComplete()
    {
        $s = $this->sentence();
        foreach ( array_keys( expRolePolicySentence::moduleActions() ) as $module )
            $s->describe( array( 'module' => $module, 'function' => '*' ) );
        foreach ( array_keys( expRolePolicySentence::functionActions() ) as $key )
        {
            list( $module, $function ) = explode( '/', $key );
            $s->describe( array( 'module' => $module, 'function' => $function, 'limitations' => array( self::lim( 'Section', array( 'a', 'b' ) ) ) ) );
        }
        $this->testRP02LimitationWordings();
        $this->testRP03EmptyDeniesAndLongLists();
        $s->describe( array( 'module' => '*', 'function' => '*' ) );
        $s->describe( array( 'module' => 'x', 'function' => '*' ) );
        $s->describe( array( 'module' => 'x', 'function' => 'y' ) );
        $known = expRolePolicySentence::sourceTexts();
        foreach ( array_keys( $this->seen ) as $source )
            $this->assertContains( $source, $known );
        $this->assertSame( count( $known ), count( array_unique( $known ) ) );
    }

    public function testRP06RoleListRows()
    {
        $rows = array(
            array( 'id' => 1, 'name' => 'Anonymous', 'policies' => 15, 'assigned' => 3 ),
            array( 'id' => 2, 'name' => 'administrator', 'policies' => 1, 'assigned' => 2 ),
            array( 'id' => 3, 'name' => 'Editor', 'policies' => 25, 'assigned' => 2 ),
            array( 'id' => 30, 'name' => 'Member', 'policies' => 11, 'assigned' => 3 ),
        );
        $this->assertSame( array( 2, 1, 3, 30 ), array_column( expRolePage::sortRows( $rows, 'name', 'asc' ), 'id' ) );
        $this->assertSame( array( 30, 3, 1, 2 ), array_column( expRolePage::sortRows( $rows, 'name', 'desc' ), 'id' ) );
        $this->assertSame( array( 30, 3, 2, 1 ), array_column( expRolePage::sortRows( $rows, 'id', 'desc' ), 'id' ) );
        $this->assertSame( array( 3, 1, 30, 2 ), array_column( expRolePage::sortRows( $rows, 'policies', 'desc' ), 'id' ) );
        // equal counts go by name A to Z in both directions
        $this->assertSame( array( 1, 30, 2, 3 ), array_column( expRolePage::sortRows( $rows, 'assigned', 'desc' ), 'id' ) );
        $this->assertSame( array( 2, 3, 1, 30 ), array_column( expRolePage::sortRows( $rows, 'assigned', 'asc' ), 'id' ) );
        $this->assertSame( array( 2, 1, 3, 30 ), array_column( expRolePage::sortRows( $rows, 'nonsense', 'x' ), 'id' ) );

        $this->assertSame( array( 2 ), array_column( expRolePage::filterRows( $rows, 'ADMIN' ), 'id' ) );
        $this->assertSame( array( 30 ), array_column( expRolePage::filterRows( $rows, '30' ), 'id' ) );
        $this->assertSame( array( 1, 2, 3, 30 ), array_column( expRolePage::filterRows( $rows, "  \n" ), 'id' ) );
        $this->assertSame( array(), expRolePage::filterRows( $rows, '%' ) );

        $page = expRolePage::pageRows( $rows, 2, 2 );
        $this->assertSame( array( 3, 30 ), array_column( $page['rows'], 'id' ) );
        $page = expRolePage::pageRows( $rows, 99, 3 );
        $this->assertSame( 3, $page['offset'] );
        $page = expRolePage::pageRows( array(), 50, 10 );
        $this->assertSame( 0, $page['offset'] );

        $this->assertSame( 'a b', expRolePage::normaliseSearch( " a \t\x01 b " ) );
        $this->assertSame( '', expRolePage::normaliseSearch( array( 'x' ) ) );
        $this->assertSame( 100, mb_strlen( expRolePage::normaliseSearch( str_repeat( 'ä', 300 ) ) ) );
        $this->assertSame( 'policies', expRolePage::normaliseSort( 'policies' ) );
        $this->assertSame( 'name', expRolePage::normaliseSort( 'name; DROP' ) );
        $this->assertSame( 'desc', expRolePage::normaliseDirection( 'DESC' ) );
        $this->assertSame( 'asc', expRolePage::normaliseDirection( null ) );
    }

    public function testRP07DraftSignatures()
    {
        $a = expRolePage::policySignature( array( 'module' => 'content', 'function' => 'read', 'limitations' => array( 'Section' => array( 2, 1 ), 'Class' => array( '3' ) ) ) );
        $b = expRolePage::policySignature( array( 'module' => 'content', 'function' => 'read', 'limitations' => array( 'Class' => array( 3 ), 'Section' => array( '1', '2' ) ) ) );
        $c = expRolePage::policySignature( array( 'module' => 'content', 'function' => 'read', 'limitations' => array( 'Section' => array( '1' ) ) ) );
        $this->assertSame( $a, $b );
        $this->assertNotSame( $a, $c );
        $this->assertFalse( expRolePage::signaturesDiffer( array( $a, $c ), array( $b, $c ) ) );
        $this->assertTrue( expRolePage::signaturesDiffer( array( $a, $c ), array( $c, $a ) ) );
        $this->assertTrue( expRolePage::signaturesDiffer( array( $a ), array( $a, $c ) ) );
    }

    public function testRP08AssignLimitType()
    {
        $this->assertSame( 'subtree', expRolePage::assignLimitType( 'subtree' ) );
        $this->assertSame( 'section', expRolePage::assignLimitType( 'section' ) );
        foreach ( array( 'Subtree', '//evil.example', '../x', '', null, array( 'subtree' ), 'section/1' ) as $bad )
            $this->assertFalse( expRolePage::assignLimitType( $bad ) );
    }

    public function testRP10PolicyOrderMoves()
    {
        $ids = array( 11, 12, 15, 20, 21 );
        $contents = array( 11 => 'a', 12 => 'b', 15 => 'c', 20 => 'd', 21 => 'e' );
        // a drag from position 1 to 4: three swaps down, the others close up
        $steps = expRolePage::moveSteps( $ids, 11, 4 );
        $this->assertSame( array( array( 11, 'down' ), array( 12, 'down' ), array( 15, 'down' ) ), $steps );
        $this->assertSame( array( 'b', 'c', 'd', 'a', 'e' ), array_values( expRolePage::applySteps( $contents, $steps ) ) );
        // from position 5 to 2
        $steps = expRolePage::moveSteps( $ids, 21, 2 );
        $this->assertSame( array( 'a', 'e', 'b', 'c', 'd' ), array_values( expRolePage::applySteps( $contents, $steps ) ) );
        // the ids stay where they are; only the contents move
        $this->assertSame( $ids, array_keys( expRolePage::applySteps( $contents, $steps ) ) );
        // past the ends is the end; the same place, a policy not in the list and an empty list do nothing
        $this->assertSame( array( 'b', 'c', 'd', 'e', 'a' ), array_values( expRolePage::applySteps( $contents, expRolePage::moveSteps( $ids, 11, 99 ) ) ) );
        $this->assertSame( array( 'c', 'a', 'b', 'd', 'e' ), array_values( expRolePage::applySteps( $contents, expRolePage::moveSteps( $ids, 15, -3 ) ) ) );
        $this->assertSame( array(), expRolePage::moveSteps( $ids, 15, 3 ) );
        $this->assertSame( array(), expRolePage::moveSteps( $ids, 99, 1 ) );
        $this->assertSame( array(), expRolePage::moveSteps( array(), 1, 1 ) );
        $this->assertSame( array( array( 12, 'up' ) ), expRolePage::moveSteps( array( '11', '12' ), '12', '1' ) );
        // every move of every policy to every place gives a permutation that puts it there and keeps the rest in order
        foreach ( $ids as $from => $id )
        {
            foreach ( range( 1, 5 ) as $to )
            {
                $after = array_values( expRolePage::applySteps( $contents, expRolePage::moveSteps( $ids, $id, $to ) ) );
                $this->assertSame( $contents[$id], $after[$to - 1] );
                $rest = array_values( array_diff( array_values( $contents ), array( $contents[$id] ) ) );
                $this->assertSame( $rest, array_values( array_diff( $after, array( $contents[$id] ) ) ) );
            }
        }
    }

    public function testRP11RemoveAllUnactivated()
    {
        $p = array( 10, 14 );
        $this->assertSame( 'remove', expUnactivatedUsers::removalDecision( array( 'exists' => true, 'is_enabled' => 0, 'has_key' => true ), 50, $p ) );
        $this->assertSame( 'protected', expUnactivatedUsers::removalDecision( array( 'exists' => true, 'is_enabled' => 0, 'has_key' => true ), 14, $p ) );
        $this->assertSame( 'protected', expUnactivatedUsers::removalDecision( array( 'exists' => true, 'is_enabled' => 0, 'has_key' => true ), '10', $p ) );
        $this->assertSame( 'activated', expUnactivatedUsers::removalDecision( array( 'exists' => true, 'is_enabled' => 1, 'has_key' => true ), 50, $p ) );
        $this->assertSame( 'activated', expUnactivatedUsers::removalDecision( array( 'exists' => true, 'is_enabled' => 0, 'has_key' => false ), 50, $p ) );
        $this->assertSame( 'activated', expUnactivatedUsers::removalDecision( array( 'exists' => true ), 50, $p ) );
        $this->assertSame( 'gone', expUnactivatedUsers::removalDecision( null, 50, $p ) );

        // 230 candidates in batches of 50: 5 batches; some change state between the list and their removal
        $users = array();
        foreach ( range( 101, 330 ) as $id )
            $users[$id] = array( 'exists' => true, 'is_enabled' => 0, 'has_key' => true );
        $users[120]['is_enabled'] = 1;          // activated meanwhile
        $users[121]['has_key'] = false;          // activated meanwhile
        $users[200] = null;                      // removed meanwhile
        $protected = array( 150, 10 );           // the current user, the anonymous user
        $removed = array(); $asked = array(); $batches = array();
        $result = expUnactivatedUsers::removeAllWith(
            function ( $after, $limit ) use ( &$users, &$asked ) {
                $asked[] = array( $after, $limit );
                $ids = array();
                foreach ( array_keys( $users ) as $id )
                    if ( $id > $after && count( $ids ) < $limit ) $ids[] = $id;
                return $ids;
            },
            function ( $id ) use ( &$users ) { return $users[$id]; },
            function ( $id ) use ( &$removed ) { $removed[] = $id; return $id !== 310; },
            $protected, 50,
            function ( $n ) use ( &$batches ) { $batches[] = $n; } );
        $this->assertSame( 225, $result['removed'] );
        $this->assertSame( array( 'activated' => 2, 'protected' => 1, 'gone' => 1, 'failed' => 1 ), $result['skipped'] );
        $this->assertSame( 5, $result['batches'] );
        $this->assertSame( array( 1, 2, 3, 4, 5 ), $batches );
        $this->assertSame( array( 0, 50 ), $asked[0] );
        $this->assertSame( array( 150, 50 ), $asked[1] );
        $this->assertNotContains( 150, $removed );
        $this->assertNotContains( 120, $removed );
        $this->assertNotContains( 200, $removed );
        $this->assertSame( count( array_unique( $removed ) ), count( $removed ) );

        // nothing to remove: no batch, nothing asked twice
        $calls = 0;
        $result = expUnactivatedUsers::removeAllWith( function () use ( &$calls ) { $calls++; return array(); },
                                                      function () { return null; }, function () { return true; }, array() );
        $this->assertSame( array( 'removed' => 0, 'skipped' => array(), 'batches' => 0 ), $result );
        $this->assertSame( 1, $calls );
        // exactly a full last batch: one more question, which is empty
        $calls = 0;
        $result = expUnactivatedUsers::removeAllWith( function ( $after ) use ( &$calls ) { $calls++; return $after < 2 ? array( 1, 2 ) : array(); },
                                                      function () { return array( 'exists' => true, 'is_enabled' => 0, 'has_key' => true ); },
                                                      function () { return true; }, array(), 2 );
        $this->assertSame( 2, $result['removed'] );
        $this->assertSame( 2, $calls );
    }

    public function testRP09UnactivatedUsers()
    {
        $this->assertSame( 'time', expUnactivatedUsers::normaliseSort( 'nonsense' ) );
        $this->assertSame( 'email', expUnactivatedUsers::normaliseSort( 'email' ) );
        $this->assertSame( 'name', expUnactivatedUsers::normaliseSort( 'name' ) );
        $this->assertSame( 'time', expUnactivatedUsers::normaliseSort( null ) );
        $this->assertSame( 'desc', expUnactivatedUsers::normaliseOrder( 'DeSc' ) );
        $this->assertSame( 'asc', expUnactivatedUsers::normaliseOrder( 'up' ) );
        $this->assertSame( 'a b c', expUnactivatedUsers::normaliseSearch( "a/(b)\x00 c" ) );
        $this->assertSame( '', expUnactivatedUsers::normaliseSearch( array() ) );
        $this->assertSame( array( 3, 12 ), expUnactivatedUsers::normaliseIDs( array( '3', 12, '3', '-1', '0', 'x', '1e3', array( 5 ), 4.5 ) ) );
        $this->assertSame( array( 7 ), expUnactivatedUsers::normaliseIDs( '7' ) );

        $now = 1700000000;
        $age = expUnactivatedUsers::age( $now - 3 * 86400 - 10, $now );
        $this->assertSame( 3, $age['days'] );
        $this->assertFalse( $age['is_old'] );
        $age = expUnactivatedUsers::age( $now - 30 * 86400, $now );
        $this->assertTrue( $age['is_old'] );
        $age = expUnactivatedUsers::age( $now - 5400, $now );
        $this->assertSame( 0, $age['days'] );
        $this->assertSame( 1, $age['hours'] );
        $this->assertTrue( expUnactivatedUsers::age( 0, $now )['unknown'] );
        $this->assertSame( 0, expUnactivatedUsers::age( $now + 100, $now )['hours'] );

        $this->assertSame( '%a!%b!_c!!d%', expUnactivatedUsers::likePattern( 'A%b_c!d' ) );
        $this->assertSame( 'https://example.org/user/activate/abc/42', expUnactivatedUsers::activationURL( 'https://example.org/', 'abc', '42x' ) );
        $this->assertSame( 'https://example.org/sub/user/activate/a%2Fb/0', expUnactivatedUsers::activationURL( 'https://example.org/sub', 'a/b', 'x' ) );
        $h1 = expUnactivatedUsers::newHash();
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{32}$/', $h1 );
        $this->assertNotSame( $h1, expUnactivatedUsers::newHash() );
    }
}
