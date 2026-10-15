<?php
/**
 * The check of reverse relations before a removal or a hide (Exponential\Service\ReverseRelationCheck,
 * doc/features/6.0/reverse-relation-check.md).
 *
 *  RC-01 - the modes: defaults (remove warn, hide disabled), known values, unknown values, block for a hide is warn
 *  RC-02 - block refuses while references exist or the check failed; disabled and warn never refuse
 *  RC-03 - a hide asks first only with warn, findings and no confirmation
 *  RC-04 - what the template gets: nothing for disabled or without findings, blocked and refused flags
 *  RC-05 - the references, on SQLite in memory: only from the published current version of an object that stays,
 *          only to objects with every location inside the subtrees, no relation_type 0, every kind, in order
 *  RC-06 - each reference belongs to the first selected subtree that holds its target
 *  RC-07 - at most LIST_LIMIT rows are read, the total is counted in full
 *  RC-08 - a path that is not digits and slashes is never put into the query
 *  RC-09 - a failing query is reported as failed, not as "no references"
 *  RC-10 - the kinds of a relation_type bit field
 *  RC-11 - content/removeobject refuses with block before every way it removes (job, confirmation, the direct
 *          removal of locations), and hands the check to its template
 *  RC-12 - content/hide and "Hide selected" of content/action ask first with warn, only when hiding
 *  RC-13 - content.ini ships the settings with their defaults
 *  RC-14 - objects and locations the user may not read are counted, never named; a block holds all the same
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\Service\ReverseRelationCheck;

class expReverseRelationCheckTest extends PHPUnit\Framework\TestCase
{
    /** @var eZDBInterface|null */
    private $savedDB;

    /** @var array the content.ini values the test changes, to put back */
    private $savedIni = array();

    protected function setUp(): void
    {
        $this->savedDB = eZDB::hasInstance() ? eZDB::instance() : null;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'content.ini' );
        foreach ( $this->savedIni as $key => $value )
        {
            list( $section, $name ) = explode( '/', $key );
            if ( $value === null )
            {
                if ( $ini->hasVariable( $section, $name ) )
                    $ini->removeSetting( $section, $name );
            }
            else
                $ini->setVariable( $section, $name, $value[0] );
        }
        $this->savedIni = array();
        if ( $this->savedDB )
            eZDB::setInstance( $this->savedDB );
    }

    private function setting( $section, $value )
    {
        $ini = eZINI::instance( 'content.ini' );
        $key = "$section/ReverseRelationCheck";
        if ( !array_key_exists( $key, $this->savedIni ) )
            $this->savedIni[$key] = $ini->hasVariable( $section, 'ReverseRelationCheck' ) ? array( $ini->variable( $section, 'ReverseRelationCheck' ) ) : null;
        if ( $value === null )
        {
            if ( $ini->hasVariable( $section, 'ReverseRelationCheck' ) )
                $ini->removeSetting( $section, 'ReverseRelationCheck' );
        }
        else
            $ini->setVariable( $section, 'ReverseRelationCheck', $value );
    }

    private static function check( $total, $failed = false, $locations = 0 )
    {
        return array( 'items' => array(), 'total' => $total, 'listed' => $total, 'unreadable' => 0, 'location_count' => $locations, 'failed' => $failed );
    }

    /** RC-01 */
    public function testModes()
    {
        $this->setting( 'RemoveSettings', null );
        $this->setting( 'HideSettings', null );
        $this->assertSame( 'warn', ReverseRelationCheck::removeMode(), 'without the setting, removal only adds the list' );
        $this->assertSame( 'disabled', ReverseRelationCheck::hideMode(), 'without the setting, a hide has no new page' );
        foreach ( array( 'disabled', 'warn', 'block', ' Block ' ) as $value )
        {
            $this->setting( 'RemoveSettings', $value );
            $this->assertSame( strtolower( trim( $value ) ), ReverseRelationCheck::removeMode() );
        }
        $this->setting( 'RemoveSettings', 'refuse' );
        $this->assertSame( 'warn', ReverseRelationCheck::removeMode(), 'an unknown value is the default' );
        $this->setting( 'HideSettings', 'warn' );
        $this->assertSame( 'warn', ReverseRelationCheck::hideMode() );
        $this->setting( 'HideSettings', 'block' );
        $this->assertSame( 'warn', ReverseRelationCheck::hideMode(), 'a hide is never refused' );
        $this->setting( 'HideSettings', 'nonsense' );
        $this->assertSame( 'disabled', ReverseRelationCheck::hideMode() );
    }

    /** RC-02 */
    public function testOnlyBlockRefusesARemoval()
    {
        $this->assertTrue( ReverseRelationCheck::refusesRemoval( 'block', self::check( 3 ) ) );
        $this->assertTrue( ReverseRelationCheck::refusesRemoval( 'block', self::check( 0, true ) ), 'what could not be checked is refused' );
        $this->assertFalse( ReverseRelationCheck::refusesRemoval( 'block', self::check( 0 ) ) );
        $this->assertFalse( ReverseRelationCheck::refusesRemoval( 'block', null ) );
        $this->assertFalse( ReverseRelationCheck::refusesRemoval( 'warn', self::check( 3 ) ) );
        $this->assertFalse( ReverseRelationCheck::refusesRemoval( 'disabled', self::check( 3 ) ) );
        $this->assertFalse( ReverseRelationCheck::refusesRemoval( 'block', self::check( 0, false, 2 ) ), 'further locations do not block a removal' );
    }

    /** RC-03 */
    public function testAHideAsksFirstOnlyWithWarnFindingsAndNoConfirmation()
    {
        $this->assertTrue( ReverseRelationCheck::hideNeedsConfirmation( 'warn', self::check( 1 ), false ) );
        $this->assertTrue( ReverseRelationCheck::hideNeedsConfirmation( 'warn', self::check( 0, false, 1 ), false ), 'further locations' );
        $this->assertTrue( ReverseRelationCheck::hideNeedsConfirmation( 'warn', self::check( 0, true ), false ), 'a failed check' );
        $this->assertFalse( ReverseRelationCheck::hideNeedsConfirmation( 'warn', self::check( 1 ), true ), 'confirmed' );
        $this->assertFalse( ReverseRelationCheck::hideNeedsConfirmation( 'warn', self::check( 0 ), false ), 'nothing found' );
        $this->assertFalse( ReverseRelationCheck::hideNeedsConfirmation( 'disabled', self::check( 1 ), false ) );
        $this->assertFalse( ReverseRelationCheck::hideNeedsConfirmation( 'warn', null, false ) );
    }

    /** RC-04 */
    public function testTemplateData()
    {
        $this->assertNull( ReverseRelationCheck::templateData( 'disabled', self::check( 2 ) ) );
        $this->assertNull( ReverseRelationCheck::templateData( 'warn', self::check( 0 ) ) );
        $this->assertNull( ReverseRelationCheck::templateData( 'block', null ) );
        $warn = ReverseRelationCheck::templateData( 'warn', self::check( 2 ) );
        $this->assertSame( array( 'warn', false, false, true, false ), array( $warn['mode'], $warn['blocked'], $warn['refused'], $warn['has_references'], $warn['more'] ) );
        // no count reaches the page
        $this->assertSame( array( 'items', 'has_references', 'more', 'failed', 'mode', 'blocked', 'refused' ), array_keys( $warn ) );
        $raw = self::check( 150 );
        $raw['listed'] = 90;
        $raw['unreadable'] = 37;
        $raw['items'] = array( array( 'node_id' => 10, 'name' => 'Folder', 'relations' => array(), 'unreadable' => 37, 'locations' => array(), 'unreadable_locations' => 23 ) );
        $data = ReverseRelationCheck::templateData( 'warn', $raw );
        $this->assertSame( array( true, true, true ), array( $data['more'], $data['items'][0]['unreadable'], $data['items'][0]['unreadable_locations'] ) );
        foreach ( array( '37', '23', '150', '90' ) as $count )
            $this->assertStringNotContainsString( $count, json_encode( $data ) );
        $block = ReverseRelationCheck::templateData( 'block', self::check( 2 ), true );
        $this->assertSame( array( 'block', true, true ), array( $block['mode'], $block['blocked'], $block['refused'] ) );
    }

    /**
     * A SQLite database in memory with the three tables the check reads, made the current database:
     *
     *   /1/2/10/   100 Folder          (selected)
     *     11       101 Image           only here
     *     12       102 Shared          also at /1/2/20/30/: stays
     *     13       103 Inner page      only here, points at 101: goes as well, not counted
     *   /1/2/20/   200 Other folder
     *     21       201 Page A          v2 published: embeds 101; v1 pointed at 100 (an old version, not counted)
     *     22       202 Page B          a draft (status 0) pointing at 100: not counted
     *     23       203 Page C          relation field 77 to 100, link and embed to 100, common to 102 (stays),
     *                                  relation_type 0 to 101 (taken back): two counted
     *     30       102 (second location)
     *   204 Page D, published without a location: points at 101
     *   /1/2/40/   400 Second          Page A points at it
     */
    private function sqlite()
    {
        if ( !extension_loaded( 'sqlite3' ) )
            $this->markTestSkipped( 'SQLite3 extension is not loaded' );
        $db = new eZSQLite3DB( array(
            'server' => '', 'port' => '', 'user' => '', 'password' => '', 'database' => ':memory:',
            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => '', 'slave_user' => '', 'slave_password' => '',
            'slave_database' => '', 'socket' => false, 'charset' => 'utf-8', 'is_internal_charset' => true,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => 'disabled' ) );
        // as in kernel/sql/sqlite/schema.sql
        $db->query( "CREATE TABLE ezcontentobject ( contentclass_id integer NOT NULL DEFAULT '0', current_version integer DEFAULT NULL,
                     id integer NOT NULL PRIMARY KEY AUTOINCREMENT, name varchar(255) DEFAULT NULL, status integer DEFAULT '0' )" );
        $db->query( "CREATE TABLE ezcontentobject_tree ( contentobject_id integer DEFAULT NULL, main_node_id integer DEFAULT NULL,
                     node_id integer NOT NULL PRIMARY KEY AUTOINCREMENT, parent_node_id integer NOT NULL DEFAULT '0',
                     path_string varchar(255) NOT NULL DEFAULT '' )" );
        $db->query( "CREATE TABLE ezcontentobject_link ( contentclassattribute_id integer NOT NULL DEFAULT '0',
                     from_contentobject_id integer NOT NULL DEFAULT '0', from_contentobject_version integer NOT NULL DEFAULT '0',
                     id integer NOT NULL PRIMARY KEY AUTOINCREMENT, relation_type integer NOT NULL DEFAULT '1',
                     to_contentobject_id integer NOT NULL DEFAULT '0' )" );
        $objects = array( 1 => array( 'Root', 1, 1 ), 100 => array( 'Folder', 1, 1 ), 101 => array( 'Image', 1, 1 ),
                          102 => array( 'Shared', 1, 1 ), 103 => array( 'Inner page', 1, 1 ), 200 => array( 'Other folder', 1, 1 ),
                          201 => array( 'Page A', 2, 1 ), 202 => array( 'Page B', 1, 0 ), 203 => array( 'Page C', 1, 1 ),
                          204 => array( 'Page D', 1, 1 ), 400 => array( 'Second', 1, 1 ) );
        foreach ( $objects as $id => list( $name, $version, $status ) )
            $db->query( "INSERT INTO ezcontentobject ( id, contentclass_id, current_version, name, status ) VALUES ( $id, 7, $version, '$name', $status )" );
        $nodes = array( 2 => array( 1, '/1/2/', 2 ), 10 => array( 100, '/1/2/10/', 10 ), 11 => array( 101, '/1/2/10/11/', 11 ),
                        12 => array( 102, '/1/2/10/12/', 12 ), 13 => array( 103, '/1/2/10/13/', 13 ), 20 => array( 200, '/1/2/20/', 20 ),
                        21 => array( 201, '/1/2/20/21/', 21 ), 22 => array( 202, '/1/2/20/22/', 22 ), 23 => array( 203, '/1/2/20/23/', 23 ),
                        30 => array( 102, '/1/2/20/30/', 12 ), 40 => array( 400, '/1/2/40/', 40 ) );
        foreach ( $nodes as $nodeID => list( $objectID, $path, $main ) )
            $db->query( "INSERT INTO ezcontentobject_tree ( node_id, contentobject_id, path_string, main_node_id ) VALUES ( $nodeID, $objectID, '$path', $main )" );
        $links = array( array( 103, 1, 101, 2, 0 ), array( 201, 2, 101, 2, 0 ), array( 201, 1, 100, 1, 0 ), array( 202, 1, 100, 1, 0 ),
                        array( 203, 1, 100, 8, 77 ), array( 203, 1, 100, 6, 0 ), array( 203, 1, 102, 1, 0 ), array( 203, 1, 101, 0, 0 ),
                        array( 204, 1, 101, 1, 0 ), array( 201, 2, 400, 1, 0 ) );
        foreach ( $links as list( $from, $version, $to, $type, $attribute ) )
            $db->query( "INSERT INTO ezcontentobject_link ( from_contentobject_id, from_contentobject_version, to_contentobject_id, relation_type, contentclassattribute_id )
                         VALUES ( $from, $version, $to, $type, $attribute )" );
        eZDB::setInstance( $db );
        return $db;
    }

    private static function pairs( array $rows )
    {
        $pairs = array();
        foreach ( $rows as $row )
            $pairs[] = $row['from_id'] . '>' . $row['to_id'] . ':' . $row['relation_type'] . '/' . $row['attribute_id'];
        return $pairs;
    }

    /** RC-05 */
    public function testTheReferencesThatCount()
    {
        $this->sqlite();
        $found = ReverseRelationCheck::references( array( array( 'path_string' => '/1/2/10/' ) ) );
        $this->assertFalse( $found['failed'] );
        $this->assertSame( 4, $found['total'] );
        $this->assertSame( array( '203>100:6/0', '203>100:8/77', '201>101:2/0', '204>101:1/0' ), self::pairs( $found['by_root'][0] ) );
        $rows = $found['by_root'][0];
        $this->assertSame( array( 'Page C', 23, 'Folder' ), array( $rows[0]['from_name'], $rows[0]['from_node_id'], $rows[0]['to_name'] ) );
        $this->assertSame( 0, $rows[3]['from_node_id'], 'a referring object without a location has no node to link to' );

        // a subtree whose objects all stay elsewhere, or have no references: nothing
        $this->assertSame( 0, ReverseRelationCheck::references( array( array( 'path_string' => '/1/2/10/12/' ) ) )['total'] );
        $this->assertSame( 0, ReverseRelationCheck::references( array( array( 'path_string' => '/1/2/20/22/' ) ) )['total'] );
        // both locations of the shared object selected: it goes, and Page C's common relation counts
        $both = ReverseRelationCheck::references( array( array( 'path_string' => '/1/2/10/12/' ), array( 'path_string' => '/1/2/20/30/' ) ) );
        $this->assertSame( array( '203>102:1/0' ), self::pairs( $both['by_root'][0] ) );
    }

    /** RC-06 */
    public function testEachReferenceBelongsToTheFirstSelectedSubtreeHoldingItsTarget()
    {
        $this->sqlite();
        $found = ReverseRelationCheck::references( array( array( 'path_string' => '/1/2/40/' ), array( 'path_string' => '/1/2/10/' ),
                                                          array( 'path_string' => '/1/2/10/11/' ) ) );
        $this->assertSame( 5, $found['total'] );
        $this->assertSame( array( '201>400:1/0' ), self::pairs( $found['by_root'][0] ) );
        $this->assertSame( array( '203>100:6/0', '203>100:8/77', '201>101:2/0', '204>101:1/0' ), self::pairs( $found['by_root'][1] ) );
        $this->assertArrayNotHasKey( 2, $found['by_root'], 'the image is in the folder selected before it' );
    }

    /** RC-07 */
    public function testAtMostTheLimitIsRead()
    {
        $db = $this->sqlite();
        for ( $i = 1; $i <= ReverseRelationCheck::LIST_LIMIT + 5; $i++ )
        {
            $id = 1000 + $i;
            $db->query( "INSERT INTO ezcontentobject ( id, contentclass_id, current_version, name, status ) VALUES ( $id, 7, 1, 'Ref $i', 1 )" );
            $db->query( "INSERT INTO ezcontentobject_link ( from_contentobject_id, from_contentobject_version, to_contentobject_id, relation_type ) VALUES ( $id, 1, 400, 2 )" );
        }
        $found = ReverseRelationCheck::references( array( array( 'path_string' => '/1/2/40/' ) ) );
        $this->assertSame( ReverseRelationCheck::LIST_LIMIT + 6, $found['total'] );
        $this->assertCount( ReverseRelationCheck::LIST_LIMIT, $found['by_root'][0] );
    }

    /** RC-08 */
    public function testOnlyPathsOfDigitsAndSlashesAreQueried()
    {
        $this->sqlite();
        foreach ( array( "/1/2/10/' OR '1'='1", '/1/2/10', '', '/1/2/x/', '%' ) as $path )
            $this->assertSame( array( 'by_root' => array(), 'total' => 0, 'failed' => false ),
                               ReverseRelationCheck::references( array( array( 'path_string' => $path ) ) ), var_export( $path, true ) );
    }

    /** RC-09 */
    public function testAFailingQueryIsReportedAsFailed()
    {
        $db = $this->sqlite();
        $db->query( 'DROP TABLE ezcontentobject_link' );
        $found = ReverseRelationCheck::references( array( array( 'path_string' => '/1/2/10/' ) ) );
        $this->assertTrue( $found['failed'] );
        $this->assertTrue( ReverseRelationCheck::refusesRemoval( 'block', $found ) );
    }

    /** RC-10 */
    public function testKinds()
    {
        $this->assertSame( array( 'common' ), ReverseRelationCheck::kinds( 1 ) );
        $this->assertSame( array( 'embed', 'link' ), ReverseRelationCheck::kinds( 6 ) );
        $this->assertSame( array( 'attribute' ), ReverseRelationCheck::kinds( 8 ) );
        $this->assertSame( array( 'common', 'embed', 'link', 'attribute' ), ReverseRelationCheck::kinds( 15 ) );
    }

    private static function code( $path )
    {
        return (string)file_get_contents( dirname( __DIR__, 4 ) . '/' . $path );
    }

    /** RC-11 */
    public function testTheRemovalViewRefusesWithBlockBeforeEveryWayItRemoves()
    {
        $code = self::code( 'kernel/private/classes/views/content/removeobject.php' );
        $blocks = strpos( $code, '$relationBlocks = \Exponential\Service\ReverseRelationCheck::refusesRemoval( $relationMode, $relationCheck );' );
        $this->assertNotFalse( $blocks );
        foreach ( array( "self::contentJob( 'startJob'", "'delete',", '\eZContentOperationCollection::deleteObject(',
                         "'removelocation',", '\eZContentOperationCollection::removeNodes(' ) as $needle )
        {
            $at = strpos( $code, $needle );
            $this->assertNotFalse( $at, $needle );
            $this->assertGreaterThan( $blocks, $at, $needle );
        }
        $this->assertStringContainsString( 'if ( !$relationBlocks and $runAsJob and ( $http->hasPostVariable( "ConfirmButton" ) or $hideRemoveConfirm ) )', $code );
        $this->assertStringContainsString( "if ( !\$relationBlocks and ( \$http->hasPostVariable( \"ConfirmButton\" ) or\n             \$hideRemoveConfirm ) )", $code );
        $this->assertStringContainsString( 'if ( $totalChildCount == 0 && !$runAsJob && !$relationBlocks && !$relationListed )', $code,
                                           'with a list, the page is shown instead of removing every location of an object at once' );
        // the confirmation is computed from the posted session selection, never from the form
        $this->assertStringContainsString( "ReverseRelationCheck::forNodes( \$jobParams['node_ids'] )", $code );
        $this->assertStringContainsString( "\$tpl->setVariable( 'reverse_relation_check' , \\Exponential\\Service\\ReverseRelationCheck::templateData( \$relationMode, \$relationCheck, \$relationRefused ) );", $code );
        $this->assertSame( 1, substr_count( $code, "'delete'," ), 'one delete operation' );
    }

    /** RC-12 */
    public function testHidingAsksFirstWithWarn()
    {
        $hide = self::code( 'kernel/private/classes/views/content/hide.php' );
        $check = strpos( $hide, 'ReverseRelationCheck::hideMode() === \Exponential\Service\ReverseRelationCheck::WARN' );
        $this->assertNotFalse( $check );
        $this->assertStringContainsString( "if ( !\$curNode->attribute( 'is_hidden' )\n             && \\Exponential\\Service\\ReverseRelationCheck::hideMode()", $hide, 'a reveal is not checked' );
        $this->assertLessThan( strpos( $hide, 'Job::interstitial(' ), $check, 'before a content job could start' );
        $this->assertLessThan( strpos( $hide, '\eZContentOperationCollection::changeHideStatus(' ), $check );
        $this->assertStringContainsString( '$hidden[\Exponential\Service\ReverseRelationCheck::CONFIRMED_POST] = \'1\';', $hide );

        $action = self::code( 'kernel/private/classes/views/content/action.php' );
        $branch = strpos( $action, "else if ( \$http->hasPostVariable( 'HideButton' ) || \$http->hasPostVariable( 'UnhideButton' ) )" );
        $this->assertNotFalse( $branch );
        $asked = strpos( $action, '$confirmation = self::hideConfirmation( $nodeIDArray', $branch );
        $this->assertNotFalse( $asked );
        $this->assertLessThan( strpos( $action, "'hide',", $branch ), $asked );
        $this->assertStringContainsString( "&& \$hideSelected\n                     && \\Exponential\\Service\\ReverseRelationCheck::hideMode() === \\Exponential\\Service\\ReverseRelationCheck::WARN\n                     && !\$http->hasPostVariable( \\Exponential\\Service\\ReverseRelationCheck::CONFIRMED_POST ) )", $action );
        $this->assertStringContainsString( "'SelectedIDArray[]' => array_values( array_map( 'intval', \$nodeIDArray ) )", $action );
    }

    /** RC-13 */
    public function testContentIniShipsTheDefaults()
    {
        $ini = self::code( 'settings/content.ini' );
        $this->assertMatchesRegularExpression( '/^\[RemoveSettings\]\n(?:(?!\[).*\n)*?ReverseRelationCheck=warn\n/m', $ini );
        $this->assertMatchesRegularExpression( '/^\[HideSettings\]\n(?:(?!\[).*\n)*?ReverseRelationCheck=disabled\n/m', $ini );
    }

    /** RC-14 */
    public function testWhatTheUserMayNotReadIsCountedNeverNamed()
    {
        $roots = array( array( 'node_id' => 10, 'object_id' => 100, 'path_string' => '/1/2/10/', 'name' => 'Folder' ),
                        array( 'node_id' => 40, 'object_id' => 400, 'path_string' => '/1/2/40/', 'name' => 'Second' ) );
        $row = function ( $from, $name, $to, $toName )
        {
            // class and field 0: nothing to look up for them
            return array( 'from_id' => $from, 'from_name' => $name, 'from_class_id' => 0, 'from_node_id' => $from + 1000,
                          'to_id' => $to, 'to_name' => $toName, 'relation_type' => 2, 'attribute_id' => 0 );
        };
        $found = array( 'by_root' => array( 0 => array( $row( 201, 'Page A', 101, 'Secret image' ), $row( 666, 'Secret page', 100, 'Folder' ) ),
                                            1 => array( $row( 667, 'Other secret', 400, 'Second' ) ) ),
                        'total' => 5, 'failed' => false );
        $locations = function ( $nodeID, $objectID )
        {
            return array( 'listed' => array(), 'unreadable' => $nodeID === 40 ? 2 : 0 );
        };

        // the user may read only Page A and the folder
        $check = ReverseRelationCheck::summarise( $roots, $found, function ( $id ) { return in_array( $id, array( 201, 100 ), true ); }, $locations );
        $this->assertSame( array( 5, 1, 2, 2 ), array( $check['total'], $check['listed'], $check['unreadable'], $check['location_count'] ) );
        $this->assertSame( array( 1, 1 ), array( $check['items'][0]['unreadable'], $check['items'][1]['unreadable'] ) );
        $this->assertSame( array( 'Page A', '' ), array( $check['items'][0]['relations'][0]['from_name'], $check['items'][0]['relations'][0]['to_name'] ),
                           'a target the user may not read is not named' );
        $this->assertSame( array(), $check['items'][1]['relations'] );
        $this->assertSame( 2, $check['items'][1]['unreadable_locations'] );
        $serialised = serialize( $check );
        foreach ( array( 'Secret page', 'Other secret', 'Secret image', '666', '667' ) as $secret )
            $this->assertStringNotContainsString( $secret, $serialised );

        // nothing readable at all: no name, and a block holds because the total counts every reference
        $none = ReverseRelationCheck::summarise( $roots, $found, function ( $id ) { return false; } );
        $this->assertSame( array( 0, 3 ), array( $none['listed'], $none['unreadable'] ) );
        $this->assertStringNotContainsString( 'Secret', serialize( $none ) );
        $this->assertStringNotContainsString( 'Page A', serialize( $none ) );
        $this->assertTrue( ReverseRelationCheck::refusesRemoval( 'block', $none ) );
        $data = ReverseRelationCheck::templateData( 'block', $none );
        $this->assertTrue( $data['blocked'] );
    }
}
