<?php
/**
 * The collaboration tool: the sample data command (exp:collaboration:sample-data), the inbox and the fixed views.
 * Live style: the tests read the installation they run on; no test database. Where there is no installation
 * (CI) they are skipped.
 *
 *  CS-01 - --help names the options and exits 0
 *  CS-02 - --dry-run lists the plan, ends in PASS and changes nothing (row counts stay the same)
 *  CS-03 - --remove --dry-run changes nothing either
 *  CS-04 - Every marker the command writes starts with "Sample: " / "sample-collab-" / example.invalid (the contract --remove relies on)
 *  CS-05 - Opening an item that does not exist is "not available", not a fatal error (it was a 500)
 *  CS-06 - The state of an approval item: waiting, approved, denied; any other type is open or closed
 *  CS-07 - The group manager refuses an empty name and a missing group before it touches anything
 *  CS-08 - (changes the installation, so only with EXP_COLLAB_SAMPLE_MUTATE=1 on an installation without sample data)
 *          apply builds the set, a second apply creates nothing, --remove leaves nothing behind
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group collaboration
 */

class CollaborationSampleDataTest extends PHPUnit\Framework\TestCase
{
    private static $installation;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        chdir( self::$installation );
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
    }

    /** @return array( int code, string output ) */
    private function command( array $args )
    {
        $command = array_merge( array( PHP_BINARY, 'bin/php/collaborationsampledata.php' ), $args, array( '--allow-root-user', '--no-colors' ) );
        $descriptors = array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) );
        $process = proc_open( $command, $descriptors, $pipes, self::$installation );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        return array( proc_close( $process ), $out );
    }

    /** @return array the numbers a dry run must not change, and the numbers of sample things */
    private function counts()
    {
        $db = eZDB::instance();
        $one = function ( $sql ) use ( $db ) {
            $rows = $db->arrayQuery( $sql );
            return (int)$rows[0]['n'];
        };
        return array(
            'items' => $one( 'SELECT COUNT(*) AS n FROM ezcollab_item' ),
            'messages' => $one( 'SELECT COUNT(*) AS n FROM ezcollab_simple_message' ),
            'objects' => $one( 'SELECT COUNT(*) AS n FROM ezcontentobject' ),
            'workflows' => $one( 'SELECT COUNT(*) AS n FROM ezworkflow' ),
            'triggers' => $one( 'SELECT COUNT(*) AS n FROM eztrigger' ),
            'sections' => $one( 'SELECT COUNT(*) AS n FROM ezsection' ),
            'groups' => $one( 'SELECT COUNT(*) AS n FROM ezcollab_group' ),
            'sample_objects' => $one( "SELECT COUNT(*) AS n FROM ezcontentobject WHERE remote_id LIKE 'sample-collab-%'" ),
            'sample_section' => $one( "SELECT COUNT(*) AS n FROM ezsection WHERE identifier = 'sample_collaboration'" ),
            'sample_workflow' => $one( "SELECT COUNT(*) AS n FROM ezworkflow WHERE name = 'Approval (sample)'" ),
            'sample_groups' => $one( "SELECT COUNT(*) AS n FROM ezcollab_group WHERE title LIKE 'Sample: %'" ),
        );
    }

    /** CS-01 */
    public function testHelpNamesTheOptions()
    {
        list( $code, $out ) = $this->command( array( '--help' ) );
        $this->assertSame( 0, $code );
        $this->assertStringContainsString( '--remove', $out );
        $this->assertStringContainsString( '--dry-run', $out );
        $this->assertStringContainsString( 'example.invalid', $out );
    }

    /** CS-02 */
    public function testDryRunPlansAndChangesNothing()
    {
        $before = $this->counts();
        list( $code, $out ) = $this->command( array( '--dry-run' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'Dry run', $out );
        $this->assertStringContainsString( 'Editors (sample)', $out );
        $this->assertStringContainsString( 'Approval (sample)', $out );
        $this->assertStringContainsString( 'Sample: Spring campaign announcement', $out );
        $this->assertMatchesRegularExpression( '/PASS: dry run, \d+ step\(s\) pending/', $out );
        $this->assertSame( $before, $this->counts() );
    }

    /** CS-03 */
    public function testRemoveDryRunChangesNothing()
    {
        $before = $this->counts();
        list( $code, $out ) = $this->command( array( '--remove', '--dry-run' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'PASS: dry run', $out );
        $this->assertSame( $before, $this->counts() );
    }

    /** CS-04 */
    public function testMarkersAreAllDistinct()
    {
        $this->assertStringStartsWith( 'Sample: ', expCollaborationSampleData::SECTION_NAME );
        $this->assertStringStartsWith( 'Sample: ', expCollaborationSampleData::FOLDER_NAME );
        $this->assertSame( 'sample-collab-', expCollaborationSampleData::REMOTE_PREFIX );
        $this->assertSame( 'example.invalid', expCollaborationSampleData::MAIL_DOMAIN );
        $this->assertSame( 'Editors (sample)', expCollaborationSampleData::USER_GROUP_NAME );
        $this->assertSame( 'Approval (sample)', expCollaborationSampleData::WORKFLOW_NAME );
        foreach ( ( new expCollaborationSampleData() )->plan() as $row )
            $this->assertMatchesRegularExpression( '/^(Sample: |Editors \(sample\)|Approval \(sample\)|content \/ publish|[A-Z][a-z]+ Sample-Editor <[a-z]+\.sample@example\.invalid>)/', $row['name'] );
    }

    /**
     * CS-05 (without the runner's error handler: the error page code resets the handlers)
     */
    #[\PHPUnit\Framework\Attributes\WithoutErrorHandler]
    public function testMissingItemIsNotAvailableNotFatal()
    {
        $module = eZModule::exists( 'collaboration' );
        $this->assertInstanceOf( 'eZModule', $module );
        $Params = array( 'Module' => $module, 'ViewMode' => 'full', 'ItemID' => 2147483000, 'Offset' => 0 );
        $result = \Exponential\View\Kernel\Collaboration\Item::main( __FILE__, get_defined_vars() );
        $this->assertNotNull( $result );
        $this->assertSame( eZModule::STATUS_FAILED, $module->exitStatus() );
        // a text that is no number is the same
        $Params['ItemID'] = 'abc';
        $module2 = eZModule::exists( 'collaboration' );
        $Params['Module'] = $module2;
        \Exponential\View\Kernel\Collaboration\Item::main( __FILE__, get_defined_vars() );
        $this->assertSame( eZModule::STATUS_FAILED, $module2->exitStatus() );
    }

    /** CS-06 */
    public function testStateOfAnItem()
    {
        $item = eZCollaborationItem::create( 'ezapprove', 14 );
        $item->setAttribute( 'data_int3', 0 );
        $this->assertSame( 'waiting', expCollaborationInbox::stateOf( $item ) );
        $item->setAttribute( 'data_int3', eZApproveCollaborationHandler::STATUS_ACCEPTED );
        $this->assertSame( 'approved', expCollaborationInbox::stateOf( $item ) );
        $item->setAttribute( 'data_int3', eZApproveCollaborationHandler::STATUS_DENIED );
        $this->assertSame( 'denied', expCollaborationInbox::stateOf( $item ) );
        $item->setAttribute( 'data_int3', eZApproveCollaborationHandler::STATUS_DEFERRED );
        $this->assertSame( 'denied', expCollaborationInbox::stateOf( $item ) );
        $other = eZCollaborationItem::create( 'somethingelse', 14 );
        $this->assertSame( 'open', expCollaborationInbox::stateOf( $other ) );
        $other->setAttribute( 'status', eZCollaborationItem::STATUS_INACTIVE );
        $this->assertSame( 'closed', expCollaborationInbox::stateOf( $other ) );
    }

    /** CS-06b - An active handler of another type that has inboxState() says the state; an unknown answer keeps the status */
    public function testStateOfAnItemWhoseHandlerSaysItsState()
    {
        $type = 'csteststate';
        $ini = eZINI::instance( 'collaboration.ini' );
        $active = (array)$ini->variable( 'HandlerSettings', 'Active' );
        $cache =& $GLOBALS['eZCollaborationHandlerObjectCache'];
        if ( !is_array( $cache ) )
            $cache = array();
        $cache[$type] = new CollaborationInboxStateTestHandler( $type );
        $ini->setVariable( 'HandlerSettings', 'Active', array_merge( $active, array( $type ) ) );
        try
        {
            $item = eZCollaborationItem::create( $type, 14 );
            foreach ( array( 0 => 'waiting', 1 => 'approved', 2 => 'denied' ) as $value => $state )
            {
                $item->setAttribute( 'data_int3', $value );
                $this->assertSame( $state, expCollaborationInbox::stateOf( $item ) );
            }
            // an answer that is not a state: the status decides, as for any other type
            $item->setAttribute( 'data_int3', 9 );
            $this->assertSame( 'open', expCollaborationInbox::stateOf( $item ) );
            $item->setAttribute( 'status', eZCollaborationItem::STATUS_INACTIVE );
            $this->assertSame( 'closed', expCollaborationInbox::stateOf( $item ) );
            // a handler that is not active is never asked
            $ini->setVariable( 'HandlerSettings', 'Active', $active );
            $item->setAttribute( 'status', eZCollaborationItem::STATUS_ACTIVE );
            $item->setAttribute( 'data_int3', 1 );
            $this->assertSame( 'open', expCollaborationInbox::stateOf( $item ) );
        }
        finally
        {
            $ini->setVariable( 'HandlerSettings', 'Active', $active );
            unset( $cache[$type] );
        }
    }

    /** CS-07 */
    public function testGroupManagerRefusesBadInput()
    {
        $before = $this->counts();
        $r = expCollaborationGroupManager::create( 14, "  \t ", 0 );
        $this->assertFalse( $r['ok'] );
        $r = expCollaborationGroupManager::rename( 14, 2147483000, 'x' );
        $this->assertFalse( $r['ok'] );
        $r = expCollaborationGroupManager::delete( 14, 2147483000 );
        $this->assertFalse( $r['ok'] );
        $r = expCollaborationGroupManager::moveItem( 14, 2147483000, 2147483000 );
        $this->assertFalse( $r['ok'] );
        $this->assertSame( $before, $this->counts() );
    }

    /** CS-08 */
    public function testApplyIsIdempotentAndRemoveLeavesNothing()
    {
        if ( getenv( 'EXP_COLLAB_SAMPLE_MUTATE' ) !== '1' )
            $this->markTestSkipped( 'changes the installation: set EXP_COLLAB_SAMPLE_MUTATE=1 on an installation without sample data' );
        $start = $this->counts();
        if ( $start['sample_objects'] || $start['sample_section'] || $start['sample_workflow'] || $start['sample_groups'] )
            $this->markTestSkipped( 'the installation has sample data already: remove it first' );

        list( $code, $out ) = $this->command( array() );
        $this->assertSame( 0, $code, $out );
        $this->assertMatchesRegularExpression( '/PASS: \d+ created/', $out );
        $built = $this->counts();
        $this->assertSame( 1, $built['sample_section'] );
        $this->assertSame( 1, $built['sample_workflow'] );
        $this->assertGreaterThanOrEqual( 11, $built['sample_objects'] ); // folder, group, 3 editors, 7 articles
        $this->assertSame( $start['items'] + 7, $built['items'] );

        list( $code, $out ) = $this->command( array() );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'PASS: 0 created', $out );
        $this->assertSame( $built, $this->counts(), 'a second run changes nothing' );

        list( $code, $out ) = $this->command( array( '--remove' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertSame( $start, $this->counts(), '--remove leaves exactly what was there before' );

        list( $code, $out ) = $this->command( array( '--dry-run' ) );
        $this->assertStringContainsString( 'would create', $out );
    }
}

/** A collaboration handler that says the inbox state of its items from data_int3 (CS-06b). */
class CollaborationInboxStateTestHandler extends eZCollaborationItemHandler
{
    public function __construct( $type = 'csteststate' )
    {
        parent::__construct( $type, 'Inbox state test', array( 'use-messages' => false ) );
    }

    public function inboxState( $item )
    {
        $map = array( 0 => 'waiting', 1 => 'approved', 2 => 'denied' );
        $value = (int)$item->attribute( 'data_int3' );
        return isset( $map[$value] ) ? $map[$value] : 'something';
    }
}
