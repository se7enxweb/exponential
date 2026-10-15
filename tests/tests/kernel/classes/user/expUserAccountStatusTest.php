<?php
/**
 * The account status the administration marks on user objects (expUserAccountStatus), with the database replaced
 * by a loader that counts its queries.
 *
 *  UAS-01 - A page of objects costs at most two queries (ezuser_setting, then ezuservisit for the enabled users
 *           among them), never one per row
 *  UAS-02 - Disabled before locked; locked once the failed logins exceed the limit (eZUser::isLocked()); no lock
 *           without a limit, and then no ezuservisit query at all
 *  UAS-03 - Objects that are no user accounts are left out; ids are taken once, as integers
 *  UAS-04 - addToList() adds user_status to the user rows of an encoded list, only for viewers with role/read,
 *           and without any query for the others
 *  UAS-05 - forObjectID() (fetch( 'user', 'account_status' )): the status, or false
 *  UAS-06 - The callers read the whole page at once: ezjscnode::subtree, expsubitems::rows and the tree menu call
 *           the bundle outside their loops; changing the enabled flag renews the tree's stamp
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expUserAccountStatusTest extends PHPUnit\Framework\TestCase
{
    /** @var array[] table => list of the id lists it was asked for */
    private $queries = array();

    /** user_id => is_enabled */
    private $settings = array( 10 => 1, 11 => 0, 12 => 1, 13 => 1, 14 => 0 );

    /** user_id => failed_login_attempts */
    private $visits = array( 10 => 0, 11 => 9, 12 => 6, 13 => 5 );

    protected function tearDown(): void
    {
        expUserAccountStatus::setTestDoubles();
    }

    private function doubles( $canView = true, $max = 5 )
    {
        $this->queries = array();
        $test = $this;
        expUserAccountStatus::setTestDoubles(
            function () use ( $canView ) { return $canView; },
            function ( $table, array $ids ) use ( $test ) { return $test->load( $table, $ids ); },
            $max );
    }

    /** The stand-in for the database: the rows of $table for $ids, and the query noted */
    public function load( $table, array $ids )
    {
        $this->queries[$table][] = $ids;
        $rows = array();
        foreach ( $ids as $id )
        {
            if ( $table === 'ezuser_setting' && isset( $this->settings[$id] ) )
                $rows[] = array( 'user_id' => (string)$id, 'is_enabled' => (string)$this->settings[$id], 'max_login' => '0' );
            if ( $table === 'ezuservisit' && isset( $this->visits[$id] ) )
                $rows[] = array( 'user_id' => (string)$id, 'failed_login_attempts' => (string)$this->visits[$id] );
        }
        return $rows;
    }

    private function queryCount()
    {
        $n = 0;
        foreach ( $this->queries as $list )
            $n += count( $list );
        return $n;
    }

    /** UAS-01 */
    public function testAPageCostsAtMostTwoQueries()
    {
        $this->doubles();
        $ids = range( 1, 200 );
        $status = expUserAccountStatus::forObjectIDs( $ids );
        $this->assertSame( 2, $this->queryCount(), 'one query per table, whatever the number of rows' );
        $this->assertCount( 1, $this->queries['ezuser_setting'] );
        $this->assertSame( $ids, $this->queries['ezuser_setting'][0] );
        // only the enabled users are asked for their failed logins
        $this->assertSame( array( 10, 12, 13 ), $this->queries['ezuservisit'][0] );
        $this->assertCount( 5, $status );
    }

    /** UAS-02 */
    public function testDisabledBeforeLockedAndLockedAboveTheLimit()
    {
        $this->doubles( true, 5 );
        $status = expUserAccountStatus::forObjectIDs( array( 10, 11, 12, 13, 14 ) );
        $this->assertSame( array( 10 => 'enabled', 11 => 'disabled', 12 => 'locked', 13 => 'enabled', 14 => 'disabled' ), $status,
            '11 has 9 failed logins but is disabled; 13 has exactly the limit and may still log in' );

        $this->doubles( true, 0 );
        $status = expUserAccountStatus::forObjectIDs( array( 10, 11, 12 ) );
        $this->assertSame( array( 10 => 'enabled', 11 => 'disabled', 12 => 'enabled' ), $status );
        $this->assertArrayNotHasKey( 'ezuservisit', $this->queries, 'no limit, no lock: the visits are not read' );
    }

    /** UAS-03 */
    public function testObjectsThatAreNoUsersAreLeftOut()
    {
        $this->doubles();
        $this->assertSame( array(), expUserAccountStatus::forObjectIDs( array() ) );
        $this->assertSame( 0, $this->queryCount(), 'nothing to read, no query' );

        $status = expUserAccountStatus::forObjectIDs( array( '10', 10, 99, 0, -3, 'x' ) );
        $this->assertSame( array( 10 => 'enabled' ), $status );
        $this->assertSame( array( 10, 99 ), $this->queries['ezuser_setting'][0], 'each id once, as an integer; none below 1' );
    }

    /** UAS-04 */
    public function testAddToListMarksTheUserRowsForThoseWhoMaySee()
    {
        $list = array( array( 'contentobject_id' => 11, 'name' => 'Disabled user' ),
                       array( 'contentobject_id' => 50, 'name' => 'Folder' ),
                       array( 'contentobject_id' => 12, 'name' => 'Locked user' ),
                       array( 'contentobject_id' => 10, 'name' => 'User' ),
                       'not a row' );
        $this->doubles( true );
        $marked = expUserAccountStatus::addToList( $list );
        $this->assertSame( 2, $this->queryCount() );
        $this->assertSame( 'disabled', $marked[0]['user_status'] );
        $this->assertArrayNotHasKey( 'user_status', $marked[1] );
        $this->assertSame( 'locked', $marked[2]['user_status'] );
        $this->assertSame( 'enabled', $marked[3]['user_status'] );
        $this->assertSame( 'not a row', $marked[4] );

        $this->doubles( false );
        $this->assertSame( $list, expUserAccountStatus::addToList( $list ), 'without role/read the list stays as it is' );
        $this->assertSame( 0, $this->queryCount(), 'and nothing is read' );
    }

    /** UAS-05 */
    public function testForObjectIDAnswersTheTemplateFetch()
    {
        $this->doubles( true );
        $this->assertSame( 'disabled', expUserAccountStatus::forObjectID( 11 ) );
        $this->assertSame( 'locked', expUserAccountStatus::forObjectID( 12 ) );
        $this->assertFalse( expUserAccountStatus::forObjectID( 50 ) );
        $this->doubles( false );
        $this->assertFalse( expUserAccountStatus::forObjectID( 11 ) );
        $this->assertSame( 0, $this->queryCount() );

        include dirname( __DIR__, 5 ) . '/kernel/user/function_definition.php';
        $this->assertSame( 'fetchAccountStatus', $FunctionList['account_status']['call_method']['method'] );
        $this->assertTrue( method_exists( 'eZUserFunctionCollection', 'fetchAccountStatus' ) );
    }

    /** UAS-06 */
    public function testTheCallersReadThePageAtOnce()
    {
        $root = dirname( __DIR__, 5 );
        $subtree = file_get_contents( "$root/extension/ezjscore/classes/ezjscserverfunctionsnode.php" );
        $this->assertStringContainsString( '$list = expUserAccountStatus::addToList( $list );', $subtree );
        $rows = file_get_contents( "$root/kernel/classes/subitems/expsubitemsserverfunctions.php" );
        $this->assertStringContainsString( '$list = expUserAccountStatus::addToList( $list );', $rows );

        // the tree menu: one call before the loop over the children, none inside it
        $tree = file_get_contents( "$root/kernel/private/classes/views/content/treemenu.php" );
        $loop = strpos( $tree, 'foreach ( $children as $child )' . "\n" . '            {' );
        $this->assertNotFalse( $loop );
        $this->assertSame( 1, substr_count( $tree, '\\expUserAccountStatus::forObjectIDs(' ) );
        $this->assertLessThan( $loop, strpos( $tree, '\\expUserAccountStatus::forObjectIDs(' ) );
        $this->assertStringContainsString( "\$childResponse['user_status'] = \\expUserAccountStatus::DISABLED;", $tree );

        $ajax = file_get_contents( "$root/extension/ezjscore/classes/ezjscajaxcontent.php" );
        $this->assertStringContainsString( "\$ret['is_hidden']            = (bool) \$node->attribute( 'is_hidden' );", $ajax );
        $this->assertStringContainsString( "\$ret['is_invisible']         = (bool) \$node->attribute( 'is_invisible' );", $ajax );

        $operations = file_get_contents( "$root/kernel/user/ezuseroperationcollection.php" );
        $this->assertSame( 2, substr_count( $operations, 'expUserAccountStatus::touchTree( $userID );' ),
            'enabling and disabling (setSettings, activation) renew the tree' );
    }
}
