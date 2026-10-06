<?php
/**
 * File containing the StateViewDatabaseTest class.
 *
 * What the object state pages read from the installation: a group described with its states
 * in order, the objects in each and its roles; the locale guard; and that removing a group
 * takes its StateGroup_<identifier> limitation with it. Reads the system group ez_lock; the
 * one write is a test group without states (so no object is touched), removed in the test.
 * Skipped without a database or without ez_lock.
 *
 * Run through the queue: bash ai/bin/one/run_alpha_db_heavy_command_one_at_a_time.sh <label>
 *     php vendor/bin/phpunit --testsuite kernel-private --filter StateViewDatabaseTest
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\State\Groups;

class StateViewDatabaseTest extends ezpTestCase
{
    /**
     * The first database use of a run installs the kernel's exception handler; done here, before
     * any test, so no test is reported for leaving it behind.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        try
        {
            eZDB::instance();
            eZContentObjectStateGroup::fetchByIdentifier( 'ez_lock' );
            Groups::references();
        }
        catch ( \Throwable $e )
        {
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        try
        {
            $db = eZDB::instance();
            if ( !$db || !$db->isConnected() )
                $this->markTestSkipped( 'No database' );
        }
        catch ( \Throwable $e )
        {
            $this->markTestSkipped( 'No database: ' . $e->getMessage() );
        }
    }

    private function lockGroup()
    {
        $group = eZContentObjectStateGroup::fetchByIdentifier( 'ez_lock' );
        if ( !$group )
            $this->markTestSkipped( 'No ez_lock group in this installation' );
        return $group;
    }

    public function testDescribeTheLockGroup()
    {
        $info = Groups::describeGroup( $this->lockGroup(), Groups::references() );
        $this->assertSame( 'ez_lock', $info['identifier'] );
        $this->assertTrue( $info['internal'] );
        $this->assertSame( '', $info['limitation'] );
        $this->assertNotEmpty( $info['states'] );
        $this->assertSame( $info['states'][0], $info['default_state'] );
        $this->assertTrue( $info['states'][0]['is_default'] );
        $sum = 0;
        foreach ( $info['states'] as $i => $state )
        {
            $this->assertSame( $i + 1, $state['position'] );
            $this->assertSame( $i === 0, $state['is_default'] );
            $this->assertIsInt( $state['object_count'] );
            $sum += $state['object_count'];
            if ( $i > 0 )
                $this->assertGreaterThanOrEqual( $info['states'][$i - 1]['priority'], $state['priority'] );
        }
        $this->assertSame( $sum, $info['objects'] );
        $this->assertIsString( $info['default_locale'] );
        $this->assertIsArray( $info['locales'] );
        // ez_lock is never a limitation, so no role can name it as one
        $this->assertSame( array(), $info['roles'] );
    }

    public function testReferencesAreKeyedByGroupIdentifier()
    {
        $refs = Groups::references();
        $this->assertIsArray( $refs );
        foreach ( $refs as $identifier => $roles )
        {
            $this->assertIsString( $identifier );
            foreach ( $roles as $role )
                $this->assertGreaterThan( 0, $role['role_id'] );
        }
    }

    public function testKnownLocale()
    {
        $first = eZContentLanguage::fetchList();
        $first = reset( $first );
        if ( $first )
            $this->assertSame( $first->attribute( 'locale' ), Groups::knownLocale( $first->attribute( 'locale' ) ) );
        $this->assertSame( '', Groups::knownLocale( 'xxx-YY' ) );
        $this->assertSame( '', Groups::knownLocale( '../etc' ) );
        $this->assertSame( '', Groups::knownLocale( '' ) );
        $this->assertSame( '', Groups::knownLocale( null ) );
    }

    public function testRemovingAGroupRemovesItsLimitation()
    {
        $identifier = 'exptestviews' . substr( uniqid(), -7 );
        $group = new eZContentObjectStateGroup( array( 'identifier' => $identifier ) );
        $language = eZContentLanguage::topPriorityLanguage();
        if ( !$language )
            $this->markTestSkipped( 'No language' );
        $group->setAttribute( 'default_language_id', $language->attribute( 'id' ) );
        $translation = $group->translationByLocale( $language->attribute( 'locale' ) );
        $translation->setAttribute( 'name', 'Exp test views' );
        $translation->setAttribute( 'description', '' );
        $messages = array();
        $this->assertTrue( $group->isValid( $messages ), implode( '; ', $messages ) );
        $group->store();
        $id = $group->attribute( 'id' );
        try
        {
            $info = Groups::describeGroup( eZContentObjectStateGroup::fetchById( $id ), array() );
            $this->assertFalse( $info['internal'] );
            $this->assertSame( 'StateGroup_' . $identifier, $info['limitation'] );
            $this->assertSame( array(), $info['states'] );
            $this->assertFalse( $info['default_state'] );
            $this->assertSame( 0, $info['objects'] );
        }
        finally
        {
            $before = time();
            eZContentObjectStateGroup::removeByID( $id );
        }
        $this->assertFalse( eZContentObjectStateGroup::fetchById( $id ) );
        $this->assertGreaterThanOrEqual( $before, eZExpiryHandler::instance()->timestamp( 'state-limitations' ) );
        $this->assertArrayNotHasKey( 'StateGroup_' . $identifier, eZContentObjectStateGroup::limitations() );
    }
}
