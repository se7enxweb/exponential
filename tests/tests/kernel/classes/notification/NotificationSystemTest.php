<?php
/**
 * The notification system end to end: subscribe, publish, run the filter, read the mail. Live style: the tests
 * run on the installation they find, with test users and content named NOTTEST that are removed again; where
 * there is no installation (CI) they are skipped.
 *
 * NO MAIL LEAVES THE SERVER. Every test runs with the mail transport forced to "file" in the process's own ini
 * (never written to a settings file), the mail goes to var/tmp/notification-mail/<run>/, and the test addresses
 * are on nottest.invalid. setUpBeforeClass() refuses to run when the transport is not the file transport.
 *
 * A test that publishes content first checks that no one outside the test subscribes to the ancestors of the
 * container, and runs only its own events through the filter (process( $ids )), so the installation's own
 * pending events and digests are not touched.
 *
 *  NT-01  The mail transport is the file transport and writes where the test says
 *  NT-02  Subscribe and unsubscribe through the handler: duplicates, unreadable nodes, someone else's rule, an empty list
 *  NT-03  Publish under a subscribed subtree: the subscriber gets one mail, the event and the collection are gone
 *  NT-04  A user without read access and a user without a subscription get nothing
 *  NT-05  A hidden node is skipped
 *  NT-06  Digest: nothing now, the item waits with a send date in the digest window, a time event after it sends one mail
 *  NT-07  Digest windows: daily, weekly, monthly send dates are in the right place; month day 31 is clamped
 *  NT-08  The digest settings are validated; a user with no settings row can store them
 *  NT-09  The collaboration notification: a rule, an event, a mail to the participant
 *  NT-10  Cleanup: handled events nothing waits for, old events by age, dry run changes nothing
 *  NT-11  A user without the notification policies has no access to the notification module
 *  NT-12  The service: status, problems, plan (rolled back, nothing sent), the lock, the run record
 *  NT-13  The subscription list: paging, name and class filter, a node that is gone
 *  NT-14  An event of content that is gone, or of an unknown type, does not stop the run
 *  NT-15  The commands: --help, status, run --dry-run, run, events, subscriptions
 *  NT-16  Every string of the notification templates has a German text; no template area is left empty
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group notification
 */

class NotificationSystemTest extends PHPUnit\Framework\TestCase
{
    const MAIL_DOMAIN = 'nottest.invalid';

    private static $installation;
    private static $mailDir;
    private static $created = array(); // content object ids made by the test
    private static $roleIDs = array();
    private static $folder;
    private static $group;
    private static $users = array();    // key => eZContentObject
    private static $skipPublish = '';
    private static $adminUser;
    private static $startedAt = 0;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        chdir( self::$installation );
        ezpLiveInstallation::requireOrSkip();

        self::$startedAt = time();
        self::$mailDir = 'var/tmp/notification-mail/nottest-' . getmypid() . '-' . time();
        self::forceFileTransport();
        self::$adminUser = eZUser::fetchByName( 'admin' );
        if ( !self::$adminUser )
            self::markTestSkipped( 'needs the admin user' );
        eZUser::setCurrentlyLoggedInUser( self::$adminUser, self::$adminUser->attribute( 'contentobject_id' ) );
        self::removeFixtures();   // what an earlier, interrupted run left
        self::buildFixtures();
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$installation === null )
            return;
        chdir( self::$installation );
        self::forceFileTransport();
        self::removeFixtures();
        // the runs the test made are not the installation's history: taken out of the record again
        $record = expNotificationService::directory() . '/runs.jsonl';
        if ( is_file( $record ) )
        {
            $keep = array();
            foreach ( file( $record, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line )
            {
                $run = json_decode( $line, true );
                if ( !is_array( $run ) || $run['time'] < self::$startedAt )
                    $keep[] = $line;
            }
            file_put_contents( $record, $keep ? implode( "\n", $keep ) . "\n" : '' );
        }
        // the mail of the test is the test's own: removed again
        if ( self::$mailDir !== null && is_dir( self::$mailDir ) )
        {
            foreach ( glob( self::$mailDir . '/*' ) ?: array() as $file )
                @unlink( $file );
            @rmdir( self::$mailDir );
        }
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        self::forceFileTransport();
        eZUser::setCurrentlyLoggedInUser( self::$adminUser, self::$adminUser->attribute( 'contentobject_id' ) );
    }

    protected function tearDown(): void
    {
        eZMailNotificationTransport::observe( null );
    }

    // ------------------------------------------------------------------ the guard

    private static function forceFileTransport()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'MailSettings', 'Transport', 'file' );
        $ini->setVariable( 'MailSettings', 'FileTransportDirectory', self::$mailDir );
        if ( trim( $ini->variable( 'MailSettings', 'Transport' ) ) !== 'file' )
            throw new RuntimeException( 'The mail transport is not the file transport: the test refuses to run.' );
    }

    private function mailFiles()
    {
        $files = glob( self::$mailDir . '/*.mail' ) ?: array();
        sort( $files );
        return $files;
    }

    /** @return string[] the file names of the mail that went to $address */
    private function mailsTo( $address )
    {
        $out = array();
        foreach ( $this->mailFiles() as $file )
            if ( strpos( (string)file_get_contents( $file ), $address ) !== false )
                $out[] = $file;
        return $out;
    }

    private function address( $key )
    {
        return 'nottest-' . $key . '@' . self::MAIL_DOMAIN;
    }

    // ------------------------------------------------------------------ fixtures

    private static function create( $class, $parentNodeID, $remote, $attributes, $section = 0 )
    {
        $object = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => $parentNodeID, 'class_identifier' => $class,
            'creator_id' => self::$adminUser->attribute( 'contentobject_id' ), 'section_id' => $section,
            'remote_id' => 'nottest-' . $remote, 'attributes' => $attributes ) );
        if ( !$object )
            throw new RuntimeException( "NOTTEST: $class $remote could not be created" );
        self::$created[] = (int)$object->attribute( 'id' );
        return $object;
    }

    private static function makeUser( $key, $group )
    {
        $login = 'nottest-' . $key;
        $password = bin2hex( random_bytes( 16 ) );
        $type = eZUser::hashType();
        $hash = eZUser::createHash( $login, $password, eZUser::site(), $type );
        $account = $login . '|' . $login . '@' . self::MAIL_DOMAIN . '|' . $hash . '|' . eZUser::passwordHashTypeName( $type ) . '|1';
        return self::create( 'user', (int)$group->attribute( 'main_node_id' ), 'user-' . $key,
                             array( 'first_name' => 'NOTTEST', 'last_name' => ucfirst( $key ), 'user_account' => $account ) );
    }

    private static function makeRole( $name, array $policies, $groupObjectID )
    {
        $role = eZRole::create( $name );
        $role->store();
        foreach ( $policies as $p )
            $role->appendPolicy( $p[0], $p[1] );
        $role->store();
        $role->assignToUser( $groupObjectID );
        self::$roleIDs[] = (int)$role->attribute( 'id' );
        eZUser::cleanupCache();
        return $role;
    }

    private static function buildFixtures()
    {
        self::$folder = self::create( 'folder', 2, 'folder', array( 'name' => 'NOTTEST folder' ) );
        self::$group = self::create( 'user_group', 5, 'group-subscribers', array( 'name' => 'NOTTEST subscribers' ) );
        $none = self::create( 'user_group', 5, 'group-none', array( 'name' => 'NOTTEST nothing' ) );
        $noread = self::create( 'user_group', 5, 'group-noread', array( 'name' => 'NOTTEST no read' ) );
        self::makeRole( 'NOTTEST subscribers', array( array( 'content', 'read' ), array( 'notification', 'use' ) ), (int)self::$group->attribute( 'id' ) );
        // notification but no content/read: a subscription that gives no mail
        self::makeRole( 'NOTTEST no read', array( array( 'notification', 'use' ) ), (int)$noread->attribute( 'id' ) );
        foreach ( array( 'a', 'b', 'c' ) as $key )
            self::$users[$key] = self::makeUser( $key, self::$group );
        self::$users['noread'] = self::makeUser( 'noread', $noread );
        self::$users['nopolicy'] = self::makeUser( 'nopolicy', $none );
        eZUser::cleanupCache();

        // no one outside the test may subscribe to the container or what is above it: its events could mail them
        $node = self::$folder->attribute( 'main_node' );
        $ids = array_filter( explode( '/', trim( $node->attribute( 'path_string' ), '/' ) ), 'strlen' );
        $db = eZDB::instance();
        $own = array();
        foreach ( self::$users as $u )
            $own[] = (int)$u->attribute( 'id' );
        $foreign = $db->arrayQuery( 'SELECT COUNT(*) AS n FROM ezsubtree_notification_rule WHERE node_id IN (' . implode( ',', array_map( 'intval', $ids ) ) .
                                    ') AND user_id NOT IN (' . implode( ',', $own ) . ')' );
        if ( (int)$foreign[0]['n'] > 0 )
            self::$skipPublish = 'someone outside the test subscribes to the content root or the container: publishing here would mail them';
    }

    private static function removeFixtures()
    {
        $db = eZDB::instance();
        // earlier runs: everything named nottest-
        $rows = $db->arrayQuery( "SELECT id FROM ezcontentobject WHERE remote_id LIKE 'nottest-%' AND remote_id NOT LIKE 'nottest-ui-%'" );
        $ids = array();
        foreach ( $rows as $r )
            $ids[(int)$r['id']] = (int)$r['id'];
        foreach ( self::$created as $id )
            $ids[$id] = $id;
        if ( $ids )
        {
            $list = implode( ',', $ids );
            $db->query( "DELETE FROM ezsubtree_notification_rule WHERE user_id IN ( $list )" );
            $db->query( "DELETE FROM ezcollab_notification_rule WHERE user_id IN ( $list )" );
            $db->query( "DELETE FROM ezgeneral_digest_user_settings WHERE user_id IN ( $list )" );
            $items = $db->arrayQuery( "SELECT eznotificationcollection_item.id FROM eznotificationcollection_item WHERE address LIKE '%@" . self::MAIL_DOMAIN . "'" );
            foreach ( $items as $i )
                $db->query( 'DELETE FROM eznotificationcollection_item WHERE id=' . (int)$i['id'] );
            // the events of the content: the version is the event's content
            foreach ( $ids as $id )
                $db->query( "DELETE FROM eznotificationevent WHERE event_type_string='ezpublish' AND data_int1=" . (int)$id );
            // the time events the test made carry a time far from now (see eventAt())
            $db->query( "DELETE FROM eznotificationevent WHERE event_type_string='ezcurrenttime' AND data_text1='nottest'" );
            eZNotificationCollection::removeEmpty();
            foreach ( $ids as $id )
            {
                $object = eZContentObject::fetch( $id );
                if ( $object )
                    $object->purge();
            }
        }
        foreach ( $db->arrayQuery( "SELECT id FROM ezrole WHERE name LIKE 'NOTTEST%'" ) as $r )
        {
            $role = eZRole::fetch( (int)$r['id'] );
            if ( $role )
                $role->removeThis();
        }
        self::$created = array();
        self::$roleIDs = array();
        self::$users = array();
        self::$folder = null;
        self::$group = null;
        eZUser::cleanupCache();
    }

    private function skipUnlessPublishing()
    {
        if ( self::$skipPublish !== '' )
            $this->markTestSkipped( self::$skipPublish );
    }

    private function articleUnderFolder( $title, $hidden = false )
    {
        $object = self::create( 'article', (int)self::$folder->attribute( 'main_node_id' ), 'article-' . md5( $title . microtime() ),
                                array( 'title' => $title, 'short_title' => $title,
                                       'intro' => '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph>NOTTEST</paragraph></section>' ) );
        if ( $hidden )
        {
            eZContentObjectTreeNode::hideSubTree( $object->attribute( 'main_node' ) );
        }
        return $object;
    }

    /** @return int[] ids of the ezpublish events of an object */
    private function eventsOf( $object )
    {
        $rows = eZDB::instance()->arrayQuery( "SELECT id FROM eznotificationevent WHERE event_type_string='ezpublish' AND data_int1=" . (int)$object->attribute( 'id' ) . ' ORDER BY id' );
        return array_map( function ( $r ) { return (int)$r['id']; }, $rows );
    }

    /** A time event (the kernel's type) at $time, marked as the test's own so a failed run can find it again. */
    private function eventAt( $time )
    {
        $event = eZNotificationEvent::create( 'ezcurrenttime', array( 'time' => $time ) );
        $event->setAttribute( 'data_text1', 'nottest' );
        $event->store();
        return (int)$event->attribute( 'id' );
    }

    private function rows( $table, $where = '1=1' )
    {
        $rows = eZDB::instance()->arrayQuery( "SELECT COUNT(*) AS n FROM $table WHERE $where" );
        return (int)$rows[0]['n'];
    }

    private function subscribe( $key, $node, $digest = 0 )
    {
        $rule = eZSubtreeNotificationRule::create( (int)$node->attribute( 'node_id' ), (int)self::$users[$key]->attribute( 'id' ), $digest );
        $rule->store();
        return $rule;
    }

    /** Takes what the earlier steps left of the test's own: no leftover mail, rules or digest items. */
    private function clean()
    {
        foreach ( $this->mailFiles() as $file )
            @unlink( $file );
        $db = eZDB::instance();
        $own = array();
        foreach ( self::$users as $u )
            $own[] = (int)$u->attribute( 'id' );
        $db->query( 'DELETE FROM ezsubtree_notification_rule WHERE user_id IN (' . implode( ',', $own ) . ')' );
        $db->query( 'DELETE FROM ezgeneral_digest_user_settings WHERE user_id IN (' . implode( ',', $own ) . ')' );
        $db->query( 'DELETE FROM ezcollab_notification_rule WHERE user_id IN (' . implode( ',', $own ) . ')' );
        $db->query( "DELETE FROM eznotificationcollection_item WHERE address LIKE '%@" . self::MAIL_DOMAIN . "'" );
        $db->query( "DELETE FROM eznotificationevent WHERE event_type_string='ezcurrenttime' AND data_text1='nottest'" );
        eZNotificationCollection::removeEmpty();
    }

    /** Digest items of someone else make the digest tests unsafe: a time event in the future would send them. */
    private function skipIfForeignItems()
    {
        if ( $this->rows( 'eznotificationcollection_item', "address NOT LIKE '%@" . self::MAIL_DOMAIN . "'" ) > 0 )
            $this->markTestSkipped( 'the installation has digest messages of its own; a time event in the future would send them' );
    }

    // ------------------------------------------------------------------ tests

    /** NT-01 */
    public function testMailGoesToFilesOnly()
    {
        $this->assertSame( 'file', trim( eZINI::instance()->variable( 'MailSettings', 'Transport' ) ) );
        $before = count( $this->mailFiles() );
        $transport = eZNotificationTransport::instance( 'ezmail' );
        $this->assertInstanceOf( 'eZMailNotificationTransport', $transport );
        $transport->send( array( $this->address( 'a' ) ), 'NOTTEST transport', 'body' );
        $files = $this->mailFiles();
        $this->assertCount( $before + 1, $files );
        $this->assertStringContainsString( self::$mailDir, $files[count( $files ) - 1] );
        $this->assertStringContainsString( 'NOTTEST transport', (string)file_get_contents( $files[count( $files ) - 1] ) );
        // an invalid address is dropped, not a fatal error
        $this->assertFalse( $transport->send( array( 'not an address' ), 'NOTTEST invalid', 'body' ) );
        // a single address as a string works (it used to be walked by foreach)
        $this->assertTrue( (bool)$transport->send( $this->address( 'b' ), 'NOTTEST string', 'body' ) );
        $this->clean();
    }

    /** NT-02 */
    public function testSubscribeAndUnsubscribeThroughTheHandler()
    {
        $this->clean();
        $a = self::$users['a'];
        $folderNodeID = (int)self::$folder->attribute( 'main_node_id' );
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( $a->attribute( 'id' ) ), $a->attribute( 'id' ) );
        $handler = new eZSubTreeHandler();
        $http = eZHTTPTool::instance();
        $keys = array( 'BrowseActionName', 'SelectedNodeIDArray', 'RemoveRule_ezsubtree', 'SelectedRuleIDArray_ezsubtree' );
        $set = function ( array $values ) use ( $http, $keys ) {
            foreach ( $keys as $k )
                unset( $_POST[$k] );
            foreach ( $values as $k => $v )
                $http->setPostVariable( $k, $v );
        };
        $uid = (int)$a->attribute( 'id' );

        // remove with nothing subscribed: an undefined variable used to be passed to in_array()
        $set( array( 'RemoveRule_ezsubtree' => 'x', 'SelectedRuleIDArray_ezsubtree' => array( 1 ) ) );
        $handler->fetchHttpInput( $http, null );
        $this->assertSame( 0, eZSubtreeNotificationRule::fetchListCount( $uid ) );

        // add: once, a duplicate and a node that does not exist are ignored
        $set( array( 'BrowseActionName' => 'AddSubtreeSubscribingNode', 'SelectedNodeIDArray' => array( $folderNodeID, $folderNodeID, 99999999 ) ) );
        $handler->fetchHttpInput( $http, null );
        $this->assertSame( 1, eZSubtreeNotificationRule::fetchListCount( $uid ) );

        // someone else's rule is not removed through my form
        $other = $this->subscribe( 'b', self::$folder->attribute( 'main_node' ) );
        $mine = eZSubtreeNotificationRule::fetchList( $uid, true );
        $set( array( 'RemoveRule_ezsubtree' => 'x', 'SelectedRuleIDArray_ezsubtree' => array( (int)$other->attribute( 'id' ), (int)$mine[0]->attribute( 'id' ) ) ) );
        $handler->fetchHttpInput( $http, null );
        $this->assertSame( 0, eZSubtreeNotificationRule::fetchListCount( $uid ) );
        $this->assertSame( 1, eZSubtreeNotificationRule::fetchListCount( (int)self::$users['b']->attribute( 'id' ) ) );
        $this->clean();
    }

    /** NT-03 */
    public function testPublishUnderASubscribedSubtreeSendsOneMail()
    {
        $this->skipUnlessPublishing();
        $this->clean();
        $this->subscribe( 'a', self::$folder->attribute( 'main_node' ) );
        $object = $this->articleUnderFolder( 'NOTTEST published article' );
        $events = $this->eventsOf( $object );
        $this->assertNotEmpty( $events, 'publishing makes a notification event' );

        $seen = array();
        eZMailNotificationTransport::observe( function ( $addresses, $subject ) use ( &$seen ) { $seen[] = array( $addresses, $subject ); }, false );
        $result = eZNotificationEventFilter::process( $events );
        $this->assertSame( count( $events ), $result['events'] );
        $this->assertSame( 0, $result['failed'] );
        $this->assertCount( 1, $seen );
        $this->assertSame( array( $this->address( 'a' ) ), $seen[0][0] );
        $this->assertStringContainsString( 'NOTTEST published article', $seen[0][1] . implode( '', array_map( 'file_get_contents', $this->mailFiles() ) ) );
        $this->assertCount( 1, $this->mailsTo( $this->address( 'a' ) ) );
        $this->assertCount( 0, $this->mailsTo( $this->address( 'b' ) ) );
        // nothing is left: the event and the collection are gone
        $this->assertSame( 0, $this->rows( 'eznotificationevent', 'id IN (' . implode( ',', $events ) . ')' ) );
        $this->assertSame( 0, $this->rows( 'eznotificationcollection_item', "address LIKE '%@" . self::MAIL_DOMAIN . "'" ) );
        // a second pass finds nothing to do for those events
        $again = eZNotificationEventFilter::process( $events );
        $this->assertSame( 0, $again['events'] );
        $this->clean();
    }

    /** NT-04 */
    public function testNoReadAccessAndNoSubscriptionGetNothing()
    {
        $this->skipUnlessPublishing();
        $this->clean();
        $this->subscribe( 'noread', self::$folder->attribute( 'main_node' ) );
        $this->subscribe( 'c', self::$folder->attribute( 'main_node' ) );
        $object = $this->articleUnderFolder( 'NOTTEST for readers' );
        eZNotificationEventFilter::process( $this->eventsOf( $object ) );
        $this->assertCount( 0, $this->mailsTo( $this->address( 'noread' ) ), 'no content/read policy: no mail' );
        $this->assertCount( 0, $this->mailsTo( $this->address( 'nopolicy' ) ) );
        $this->assertCount( 1, $this->mailsTo( $this->address( 'c' ) ) );
        $this->clean();
    }

    /** NT-05 */
    public function testHiddenNodeIsSkipped()
    {
        $this->skipUnlessPublishing();
        $this->clean();
        $this->subscribe( 'a', self::$folder->attribute( 'main_node' ) );
        $object = $this->articleUnderFolder( 'NOTTEST hidden article', true );
        $node = eZContentObjectTreeNode::fetch( (int)$object->attribute( 'main_node_id' ) );
        $this->assertTrue( (int)$node->attribute( 'is_invisible' ) + (int)$node->attribute( 'is_hidden' ) > 0 );
        $this->clean();
        eZNotificationEventFilter::process( $this->eventsOf( $object ) );
        $this->assertCount( 0, $this->mailsTo( $this->address( 'a' ) ) );
    }

    /** NT-06 */
    public function testDigestWaitsAndIsSentByTheTimeEvent()
    {
        $this->skipUnlessPublishing();
        $this->skipIfForeignItems();
        $this->clean();
        $uid = (int)self::$users['a']->attribute( 'id' );
        $settings = eZGeneralDigestUserSettings::create( $uid, 1, eZGeneralDigestUserSettings::TYPE_DAILY, '', '7:00' );
        $settings->store();
        $this->subscribe( 'a', self::$folder->attribute( 'main_node' ), 0 );
        $object = $this->articleUnderFolder( 'NOTTEST digest article' );
        eZNotificationEventFilter::process( $this->eventsOf( $object ) );

        $this->assertCount( 0, $this->mailsTo( $this->address( 'a' ) ), 'a digest user gets nothing at once' );
        $rows = eZDB::instance()->arrayQuery( "SELECT send_date FROM eznotificationcollection_item WHERE address='" . $this->address( 'a' ) . "'" );
        $this->assertCount( 1, $rows );
        $due = (int)$rows[0]['send_date'];
        $this->assertGreaterThan( time(), $due );
        $this->assertLessThanOrEqual( time() + 86400 + 60, $due );
        $this->assertSame( '07:00', date( 'H:i', $due ) );
        // the event stays, handled, until the digest is sent
        $this->assertSame( 1, $this->rows( 'eznotificationevent', 'status = ' . eZNotificationEvent::STATUS_HANDLED . ' AND id IN (' . implode( ',', $this->eventsOf( $object ) ) . ')' ) );

        // a time event before the date sends nothing
        eZNotificationEventFilter::process( array( $this->eventAt( $due - 600 ) ) );
        $this->assertCount( 0, $this->mailsTo( $this->address( 'a' ) ) );
        $this->assertSame( 1, $this->rows( 'eznotificationcollection_item', "address='" . $this->address( 'a' ) . "'" ) );

        // one after it sends one digest mail and the item is gone
        $result = eZNotificationEventFilter::process( array( $this->eventAt( $due + 60 ) ) );
        $this->assertSame( 0, $result['failed'] );
        $this->assertCount( 1, $this->mailsTo( $this->address( 'a' ) ) );
        $this->assertStringContainsString( 'NOTTEST digest article', file_get_contents( $this->mailsTo( $this->address( 'a' ) )[0] ) );
        $this->assertSame( 0, $this->rows( 'eznotificationcollection_item', "address='" . $this->address( 'a' ) . "'" ) );
        // and the handled event nothing waits for is removed by the cleanup that ends a run
        $this->assertSame( 0, $this->rows( 'eznotificationevent', 'status = ' . eZNotificationEvent::STATUS_HANDLED . ' AND id IN (' . implode( ',', $this->eventsOf( $object ) ?: array( 0 ) ) . ')' ) );
        $this->clean();
    }

    /** NT-07 */
    public function testDigestWindows()
    {
        $item = new eZNotificationCollectionItem( array( 'collection_id' => 0, 'event_id' => 0, 'address' => 'x', 'send_date' => 0 ) );
        $now = time();
        // daily: the next 9:00, within a day
        $date = eZNotificationSchedule::setDateForItem( $item, array( 'frequency' => 'day', 'hour' => 9 ) );
        $this->assertGreaterThan( $now, $date );
        $this->assertLessThanOrEqual( $now + 86400, $date );
        $this->assertSame( '09', date( 'H', $date ) );
        // weekly: the next given weekday at 9, within a week
        foreach ( array( 0, 3, 6 ) as $wday )
        {
            $date = eZNotificationSchedule::setDateForItem( $item, array( 'frequency' => 'week', 'day' => $wday, 'hour' => 9 ) );
            $this->assertGreaterThan( $now, $date );
            $this->assertLessThanOrEqual( $now + 7 * 86400, $date );
            $this->assertSame( $wday, (int)date( 'w', $date ) );
            $this->assertSame( '09', date( 'H', $date ) );
        }
        // monthly: day 31 is clamped to the last day of a short month, and the date is in the future
        $date = eZNotificationSchedule::setDateForItem( $item, array( 'frequency' => 'month', 'day' => 31, 'hour' => 9 ) );
        $this->assertGreaterThan( $now, $date );
        $this->assertLessThanOrEqual( $now + 32 * 86400, $date );
        $date = eZNotificationSchedule::setDateForItem( $item, array( 'frequency' => 'month', 'day' => (int)date( 'j' ), 'hour' => (int)date( 'G' ) + 1 ) );
        $this->assertGreaterThanOrEqual( $now, $date );
        // the items are found by their window (it used to be a condition that overwrote itself), and a handler that is
        // not available is not an undefined index
        $this->assertSame( array(), eZNotificationCollectionItem::fetchByDate( 1 ), 'no item is due at time 1' );
        $this->assertSame( array(), eZGeneralDigestHandler::fetchHandlersForUser( $now, 'nobody@' . self::MAIL_DOMAIN ) );
    }

    /** NT-08 */
    public function testDigestSettingsAreValidated()
    {
        $this->clean();
        $user = eZUser::fetch( (int)self::$users['b']->attribute( 'id' ) );
        eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
        $uid = (int)$user->attribute( 'contentobject_id' );
        $this->assertNull( eZGeneralDigestUserSettings::fetchByUserId( $uid ), 'a user who never opened the page has no row' );
        $handler = new eZGeneralDigestHandler();
        $http = eZHTTPTool::instance();
        foreach ( array( 'ReceiveDigest_ezgeneraldigest', 'DigestType_ezgeneraldigest', 'Time_ezgeneraldigest', 'Weekday_ezgeneraldigest', 'Monthday_ezgeneraldigest' ) as $k )
            unset( $_POST[$k] );
        $http->setPostVariable( 'ReceiveDigest_ezgeneraldigest', '1' );
        $http->setPostVariable( 'DigestType_ezgeneraldigest', '2' );
        $http->setPostVariable( 'Monthday_ezgeneraldigest', '99' );
        $http->setPostVariable( 'Time_ezgeneraldigest', '25:61' );
        $handler->storeSettings( $http, null );
        $s = eZGeneralDigestUserSettings::fetchByUserId( $uid );
        $this->assertNotNull( $s );
        $this->assertSame( 1, (int)$s->attribute( 'receive_digest' ) );
        $this->assertSame( 2, (int)$s->attribute( 'digest_type' ) );
        $this->assertSame( '31', $s->attribute( 'day' ), 'the month day is clamped to 1..31' );
        $this->assertSame( '0:00', $s->attribute( 'time' ), 'a time that is not one of the offered hours is not stored' );

        $http->setPostVariable( 'DigestType_ezgeneraldigest', '7' );
        $http->setPostVariable( 'Time_ezgeneraldigest', '14:00' );
        $handler->storeSettings( $http, null );
        $s = eZGeneralDigestUserSettings::fetchByUserId( $uid );
        $this->assertSame( eZGeneralDigestUserSettings::TYPE_DAILY, (int)$s->attribute( 'digest_type' ) );
        $this->assertSame( '14:00', $s->attribute( 'time' ) );

        $http->setPostVariable( 'DigestType_ezgeneraldigest', '1' );
        $http->setPostVariable( 'Weekday_ezgeneraldigest', 'Blursday' );
        $handler->storeSettings( $http, null );
        $s = eZGeneralDigestUserSettings::fetchByUserId( $uid );
        $this->assertContains( $s->attribute( 'day' ), $handler->attribute( 'all_week_days' ) );

        // unchecked: off
        unset( $_POST['ReceiveDigest_ezgeneraldigest'] );
        $handler->storeSettings( $http, null );
        $this->assertSame( 0, (int)eZGeneralDigestUserSettings::fetchByUserId( $uid )->attribute( 'receive_digest' ) );
        $this->clean();
    }

    /** NT-09 */
    public function testCollaborationNotification()
    {
        $this->clean();
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT i.id, i.data_text1 FROM ezcollab_item i, ezcollab_item_participant_link p WHERE i.type_identifier='ezapprove' AND p.collaboration_id=i.id AND p.participant_type=" . eZCollaborationItemParticipantLink::TYPE_USER . " ORDER BY i.id DESC", array( 'limit' => 1 ) );
        if ( !$rows )
            $this->markTestSkipped( 'needs an approval item (exp:collaboration:sample-data makes some)' );
        $itemID = (int)$rows[0]['id'];
        $participants = $db->arrayQuery( 'SELECT p.participant_id, u.email FROM ezcollab_item_participant_link p, ezuser u WHERE u.contentobject_id = p.participant_id AND p.collaboration_id=' . $itemID . ' AND p.participant_type=' . eZCollaborationItemParticipantLink::TYPE_USER );
        // only a participant whose address is not deliverable (the sample editors, example.invalid): never a real address
        $target = false;
        foreach ( $participants as $p )
            if ( preg_match( '/@(example|nottest)\.invalid$/', $p['email'] ) )
                $target = $p;
        if ( !$target )
            $this->markTestSkipped( 'the approval items have no participant on an .invalid address; the test will not use a real one' );

        $item = eZCollaborationItem::fetch( $itemID );
        $rule = eZCollaborationNotificationRule::create( 'ezapprove', (int)$target['participant_id'] );
        $rule->store();
        $event = eZNotificationEvent::create( 'ezcollaboration', array( 'collaboration_id' => $itemID, 'collaboration_identifier' => 'ezapprove' ) );
        $event->store();
        $eventID = (int)$event->attribute( 'id' );
        $seen = array();
        eZMailNotificationTransport::observe( function ( $addresses ) use ( &$seen ) { foreach ( $addresses as $a ) $seen[] = $a; }, false );
        try
        {
            $result = eZNotificationEventFilter::process( array( $eventID ) );
        }
        finally
        {
            eZPersistentObject::removeObject( eZCollaborationNotificationRule::definition(), array( 'id' => $rule->attribute( 'id' ) ) );
            $db->query( 'DELETE FROM eznotificationevent WHERE id=' . $eventID );
        }
        $this->assertSame( 0, $result['failed'] );
        $this->assertSame( array( $target['email'] ), $seen );
        $this->assertCount( 1, $this->mailsTo( $target['email'] ) );
        // the rules of the settings page are listed by user id (they were looked up by e-mail address)
        $handler = new eZCollaborationNotificationHandler();
        $this->assertSame( array(), $handler->rules( eZUser::fetch( (int)$target['participant_id'] ) ) );
        $this->clean();
    }

    /** NT-10 */
    public function testCleanup()
    {
        $this->clean();
        $db = eZDB::instance();
        $oldEvent = $this->eventAt( time() - 40 * 86400 );
        $newEvent = $this->eventAt( time() - 3600 );
        $handled = eZNotificationEvent::create( 'ezcurrenttime', array( 'time' => time() ) );
        $handled->setAttribute( 'data_text1', 'nottest' );
        $handled->setAttribute( 'status', eZNotificationEvent::STATUS_HANDLED );
        $handled->store();
        $handledID = (int)$handled->attribute( 'id' );

        // dry runs change nothing
        $before = $this->rows( 'eznotificationevent' );
        $dry = expNotificationService::cleanup( 30 * 86400, null, false, true );
        $this->assertGreaterThanOrEqual( 1, $dry['handled'] );
        $this->assertGreaterThanOrEqual( 1, $dry['removed'] );
        $this->assertSame( $before, $this->rows( 'eznotificationevent' ) );

        // the events of the test: older than 30 days goes, the handled orphan goes, the recent one stays
        $r = eZNotificationEvent::removeOlderThan( time() - 30 * 86400, eZNotificationEvent::STATUS_CREATED, false, false );
        $this->assertContains( $oldEvent, $r['ids'] );
        $this->assertNotContains( $newEvent, $r['ids'] );
        $this->assertSame( 0, $this->rows( 'eznotificationevent', 'id=' . $oldEvent ) );
        $this->assertSame( 1, $this->rows( 'eznotificationevent', 'id=' . $newEvent ) );
        $this->assertGreaterThanOrEqual( 1, eZNotificationEvent::cleanupHandled() );
        $this->assertSame( 0, $this->rows( 'eznotificationevent', 'id=' . $handledID ), 'a handled event nothing waits for is removed' );
        // a handled event something waits for stays
        $kept = eZNotificationEvent::create( 'ezcurrenttime', array( 'time' => time() ) );
        $kept->setAttribute( 'data_text1', 'nottest' );
        $kept->setAttribute( 'status', eZNotificationEvent::STATUS_HANDLED );
        $kept->store();
        $item = eZNotificationCollectionItem::create( 0, (int)$kept->attribute( 'id' ), $this->address( 'a' ), time() + 3600 );
        $item->store();
        eZNotificationEvent::cleanupHandled();
        $this->assertSame( 1, $this->rows( 'eznotificationevent', 'id=' . (int)$kept->attribute( 'id' ) ) );
        $this->clean();
        $db->query( 'DELETE FROM eznotificationevent WHERE id=' . (int)$kept->attribute( 'id' ) );

        $this->assertSame( 86400 * 30, expNotificationService::parseAge( '30d' ) );
        $this->assertSame( 3600 * 12, expNotificationService::parseAge( '12h' ) );
        $this->assertSame( 7 * 86400, expNotificationService::parseAge( '1w' ) );
        $this->assertSame( 5 * 86400, expNotificationService::parseAge( '5' ) );
        $this->assertFalse( expNotificationService::parseAge( 'soon' ) );
    }

    /** NT-11 */
    public function testUserWithoutNotificationPoliciesHasNoAccess()
    {
        $none = eZUser::fetch( (int)self::$users['nopolicy']->attribute( 'id' ) );
        $with = eZUser::fetch( (int)self::$users['a']->attribute( 'id' ) );
        $noread = eZUser::fetch( (int)self::$users['noread']->attribute( 'id' ) );
        $this->assertSame( 'no', $none->hasAccessTo( 'notification', 'use' )['accessWord'] );
        $this->assertSame( 'yes', $with->hasAccessTo( 'notification', 'use' )['accessWord'] );
        $this->assertSame( 'no', $with->hasAccessTo( 'notification', 'administrate' )['accessWord'], 'the status page is for administrators' );
        $this->assertSame( 'yes', self::$adminUser->hasAccessTo( 'notification', 'administrate' )['accessWord'] );
        $this->assertSame( 'yes', $noread->hasAccessTo( 'notification', 'use' )['accessWord'] );
        $this->assertSame( 'no', $noread->hasAccessTo( 'content', 'read' )['accessWord'] );
    }

    /** NT-12 */
    public function testServiceStatusPlanAndLock()
    {
        $this->skipUnlessPublishing();
        $this->clean();
        $status = expNotificationService::status();
        foreach ( array( 'pending_total', 'handled_kept', 'collections', 'items_total', 'subscriptions', 'runs', 'problems', 'transport', 'sender', 'handlers' ) as $key )
            $this->assertArrayHasKey( $key, $status );
        $this->assertSame( 'file', $status['transport'] );
        $this->assertContains( 'file', array_map( function ( $p ) { return $p[1] === 'notification_transport_file' ? 'file' : ''; }, $status['problems'] ) );
        foreach ( $status['problems'] as $p )
            $this->assertNotSame( '', expNotificationService::problemText( $p ) );

        // a plan: the same pass, rolled back, the mail only reported
        $this->subscribe( 'a', self::$folder->attribute( 'main_node' ) );
        $object = $this->articleUnderFolder( 'NOTTEST planned article' );
        $events = $this->eventsOf( $object );
        $snapshot = array( $this->rows( 'eznotificationevent' ), $this->rows( 'eznotificationcollection' ), $this->rows( 'eznotificationcollection_item' ) );
        $files = count( $this->mailFiles() );
        $plan = expNotificationService::plan( array( 'time_event' => false ) );
        $this->assertSame( 'ok', $plan['result'], $plan['error'] );
        $this->assertNotEmpty( $plan['mails'], 'the plan lists the mail' );
        $found = false;
        foreach ( $plan['mails'] as $m )
            if ( in_array( $this->address( 'a' ), $m['raw'], true ) )
                $found = true;
        $this->assertTrue( $found );
        $this->assertSame( $snapshot, array( $this->rows( 'eznotificationevent' ), $this->rows( 'eznotificationcollection' ), $this->rows( 'eznotificationcollection_item' ) ), 'nothing was marked, removed or made' );
        $this->assertSame( $files, count( $this->mailFiles() ), 'no mail was written' );
        $this->assertSame( count( $events ), $this->rows( 'eznotificationevent', 'status = 0 AND id IN (' . implode( ',', $events ) . ')' ) );

        // the lock: a run while another holds it is refused
        $dir = expNotificationService::directory();
        eZDir::mkdir( $dir, false, true );
        $handle = fopen( $dir . '/run.lock', 'c+' );
        $this->assertTrue( flock( $handle, LOCK_EX | LOCK_NB ) );
        ftruncate( $handle, 0 );
        fwrite( $handle, json_encode( array( 'pid' => 4242, 'since' => time() ) ) );
        $busy = expNotificationService::run( array( 'source' => 'test', 'time_event' => false ) );
        $this->assertSame( 'busy', $busy['result'] );
        $running = expNotificationService::runningNow();
        $this->assertIsArray( $running );
        $this->assertSame( 4242, (int)$running['pid'] );
        flock( $handle, LOCK_UN );
        fclose( $handle );
        $this->assertFalse( expNotificationService::runningNow() );

        // a run of only these events: handled, mailed to the file, recorded
        $runs = count( expNotificationService::lastRuns( 500 ) );
        $run = expNotificationService::run( array( 'source' => 'test', 'events' => $events ) );
        $this->assertSame( 'ok', $run['result'], $run['error'] );
        $this->assertSame( count( $events ), $run['events'] );
        $this->assertSame( 1, $run['mails'] );
        $this->assertSame( 1, $run['recipients'] );
        $this->assertCount( 1, $this->mailsTo( $this->address( 'a' ) ) );
        $this->assertSame( $run['time'], expNotificationService::lastRuns( 1 )[0]['time'] );
        $this->assertGreaterThan( $runs - 1, count( expNotificationService::lastRuns( 500 ) ) );
        $this->clean();
    }

    /** NT-13 */
    public function testSubscriptionList()
    {
        $this->skipUnlessPublishing();
        $this->clean();
        $uid = (int)self::$users['a']->attribute( 'id' );
        $nodeIDs = array();
        $articles = array();
        foreach ( array( 'Alpha', 'Beta', 'Gamma' ) as $name )
        {
            $article = $this->articleUnderFolder( 'NOTTEST list ' . $name );
            $articles[] = $article;
            $this->subscribe( 'a', $article->attribute( 'main_node' ) );
        }
        $this->subscribe( 'a', self::$folder->attribute( 'main_node' ) );
        $all = expNotificationService::subscriptions( $uid, array(), 0, 25 );
        $this->assertSame( 4, $all['total'] );
        $this->assertCount( 4, $all['rows'] );
        $page = expNotificationService::subscriptions( $uid, array(), 3, 2 );
        $this->assertSame( 4, $page['total'] );
        $this->assertCount( 1, $page['rows'] );
        $filtered = expNotificationService::subscriptions( $uid, array( 'q' => 'list beta' ), 0, 25 );
        $this->assertSame( 1, $filtered['total'] );
        $this->assertSame( 'NOTTEST list Beta', $filtered['rows'][0]['name'] );
        $this->assertContains( 'NOTTEST folder', $filtered['rows'][0]['path'] );
        $this->assertSame( 'article', $filtered['rows'][0]['class_identifier'] );
        $this->assertNotFalse( $filtered['rows'][0]['last_change'] );
        $this->assertSame( 1, expNotificationService::subscriptions( $uid, array( 'class' => 'folder' ), 0, 25 )['total'] );
        // a wildcard in the filter is a character, not a pattern
        $this->assertSame( 0, expNotificationService::subscriptions( $uid, array( 'q' => '%' ), 0, 25 )['total'] );
        $this->assertArrayHasKey( 'article', expNotificationService::subscribedClasses( $uid ) );

        // a node that is gone: the subscription is listed as missing
        $rule = eZSubtreeNotificationRule::create( 99999999, $uid );
        $rule->store();
        $missing = expNotificationService::subscriptions( $uid, array( 'missing' => true ), 0, 25 );
        $this->assertSame( 1, $missing['total'] );
        $this->assertTrue( $missing['rows'][0]['missing'] );
        $this->assertSame( array(), $missing['rows'][0]['path'] );
        $this->clean();
    }

    /** NT-14 */
    public function testEventOfGoneContentOrUnknownTypeDoesNotStopTheRun()
    {
        $this->clean();
        $db = eZDB::instance();
        $event = eZNotificationEvent::create( 'ezpublish', array( 'object' => 999999, 'version' => 1 ) );
        $event->store();
        $unknown = new eZNotificationEvent( array( 'id' => null, 'event_type_string' => 'nottesttype', 'data_int1' => 1, 'data_int2' => 1, 'data_int3' => 0, 'data_int4' => 0,
                                                   'data_text1' => 'nottest', 'data_text2' => '', 'data_text3' => '', 'data_text4' => '' ) );
        $unknown->store();
        $ids = array( (int)$event->attribute( 'id' ), (int)$unknown->attribute( 'id' ) );
        $result = eZNotificationEventFilter::process( $ids );
        $this->assertSame( 2, $result['events'] );
        $this->assertSame( 0, $this->rows( 'eznotificationevent', 'id IN (' . implode( ',', $ids ) . ')' ), 'events nothing waits for are removed' );
        $this->clean();
    }

    /** NT-16 */
    public function testEveryTemplateStringHasAGermanText()
    {
        $catalogue = file_get_contents( self::$installation . '/share/translations/ger-DE/translation.ts' );
        $missing = array();
        $files = array_merge( glob( self::$installation . '/design/admin4/templates/notification/*.tpl' ) ?: array(),
                              glob( self::$installation . '/design/admin4/templates/notification/*/*/*/*.tpl' ) ?: array(),
                              glob( self::$installation . '/design/admin4/templates/notification/parts/*.tpl' ) ?: array() );
        $this->assertNotEmpty( $files );
        foreach ( $files as $file )
        {
            $code = file_get_contents( $file );
            $this->assertNotSame( '', trim( $code ), $file . ' is empty' );
            preg_match_all( "/'((?:[^'\\\\]|\\\\.)*)'\\s*\\|\\s*i18n\\(\\s*'(design\\/admin\\/notification[^']*)'/", $code, $m, PREG_SET_ORDER );
            foreach ( $m as $hit )
            {
                $source = htmlspecialchars( str_replace( "\\'", "'", $hit[1] ), ENT_QUOTES | ENT_XML1 );
                if ( strpos( $catalogue, '<source>' . $source . '</source>' ) === false )
                    $missing[] = $hit[1];
            }
        }
        $this->assertSame( array(), $missing, 'strings without a German text' );
    }

    // ------------------------------------------------------------------ commands

    /** @return array( int code, string output ) */
    private function command( $script, array $args )
    {
        $command = array_merge( array( PHP_BINARY, 'bin/php/' . $script . '.php' ), $args, array( '--allow-root-user', '--no-colors' ) );
        $descriptors = array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) );
        $process = proc_open( $command, $descriptors, $pipes, self::$installation );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        return array( proc_close( $process ), $out );
    }

    /** NT-15 */
    public function testHelpOfEveryCommand()
    {
        foreach ( array( 'notificationstatus' => '--json', 'notificationrun' => '--mail-file-dir', 'notificationevents' => '--older-than',
                         'notificationsubscriptions' => '--missing' ) as $script => $option )
        {
            list( $code, $out ) = $this->command( $script, array( '--help' ) );
            $this->assertSame( 0, $code, $script );
            $this->assertStringContainsString( $option, $out, $script );
        }
    }

    public function testStatusCommand()
    {
        list( $code, $out ) = $this->command( 'notificationstatus', array() );
        $this->assertContains( $code, array( 0, 1 ) );
        $this->assertStringContainsString( 'Events', $out );
        $this->assertStringContainsString( 'Problems', $out );
        $this->assertMatchesRegularExpression( '/(PASS|FAIL)/', $out );
        list( $code, $out ) = $this->command( 'notificationstatus', array( '--json' ) );
        $data = json_decode( substr( $out, (int)strpos( $out, '{' ), strrpos( $out, '}' ) - (int)strpos( $out, '{' ) + 1 ), true );
        $this->assertIsArray( $data, $out );
        $this->assertArrayHasKey( 'pending_total', $data );
        $this->assertStringNotContainsString( '@', json_encode( $data['runs'] ), 'no address in the runs' );
    }

    public function testDryRunCommandChangesNothing()
    {
        $this->skipUnlessPublishing();
        $this->clean();
        $this->subscribe( 'a', self::$folder->attribute( 'main_node' ) );
        $object = $this->articleUnderFolder( 'NOTTEST dry run article' );
        $events = $this->eventsOf( $object );
        $counts = array( $this->rows( 'eznotificationevent' ), $this->rows( 'eznotificationcollection' ), $this->rows( 'eznotificationcollection_item' ) );
        list( $code, $out ) = $this->command( 'notificationrun', array( '--dry-run', '--no-time-event', '--mail-file-dir=' . self::$mailDir ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'Dry run', $out );
        $this->assertStringContainsString( 'would send', $out );
        $this->assertStringContainsString( 'n***@' . self::MAIL_DOMAIN, $out, 'the address is masked' );
        $this->assertStringNotContainsString( $this->address( 'a' ), $out );
        $this->assertStringContainsString( 'PASS', $out );
        $this->assertSame( $counts, array( $this->rows( 'eznotificationevent' ), $this->rows( 'eznotificationcollection' ), $this->rows( 'eznotificationcollection_item' ) ) );
        $this->assertCount( 0, $this->mailFiles(), 'a dry run writes no mail' );
        list( , $withAddresses ) = $this->command( 'notificationrun', array( '--dry-run', '--no-time-event', '--addresses', '--mail-file-dir=' . self::$mailDir ) );
        $this->assertStringContainsString( $this->address( 'a' ), $withAddresses );
        $this->assertSame( count( $events ), $this->rows( 'eznotificationevent', 'status = 0 AND id IN (' . implode( ',', $events ) . ')' ) );
        $this->clean();
    }

    public function testRunCommandRecordsTheRunAndKeepsMailLocal()
    {
        $this->skipUnlessPublishing();
        $this->skipIfForeignItems();
        $this->clean();
        $this->subscribe( 'c', self::$folder->attribute( 'main_node' ) );
        $object = $this->articleUnderFolder( 'NOTTEST run article' );
        $runs = count( expNotificationService::lastRuns( 500 ) );
        list( $code, $out ) = $this->command( 'notificationrun', array( '--event=' . implode( ',', $this->eventsOf( $object ) ), '--mail-file-dir=' . self::$mailDir ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'PASS', $out );
        $this->assertCount( 1, $this->mailsTo( $this->address( 'c' ) ), $out );
        $last = expNotificationService::lastRuns( 1 );
        $this->assertSame( 'console', $last[0]['source'] );
        $this->assertGreaterThanOrEqual( 1, $last[0]['mails'] );
        $this->assertGreaterThan( $runs - 1, count( expNotificationService::lastRuns( 500 ) ) );
        $this->clean();
    }

    public function testEventsAndSubscriptionsCommands()
    {
        $this->clean();
        $this->subscribe( 'a', self::$folder->attribute( 'main_node' ) );
        $rule = eZSubtreeNotificationRule::create( 99999998, (int)self::$users['a']->attribute( 'id' ) );
        $rule->store();
        list( $code, $out ) = $this->command( 'notificationsubscriptions', array( 'list', '--user=nottest-a' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'NOTTEST folder', $out );
        $this->assertStringContainsString( '(content is gone)', $out );
        $this->assertStringNotContainsString( '@' . self::MAIL_DOMAIN, $out, 'no address unless asked for' );
        list( , $out ) = $this->command( 'notificationsubscriptions', array( 'list', '--user=nottest-a', '--addresses' ) );
        $this->assertStringContainsString( $this->address( 'a' ), $out );
        list( $code, $out ) = $this->command( 'notificationsubscriptions', array( 'remove-missing', '--user=nottest-a', '--dry-run' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'would be removed', $out );
        $this->assertSame( 1, expNotificationService::subscriptions( (int)self::$users['a']->attribute( 'id' ), array( 'missing' => true ) )['total'] );
        list( $code, $out ) = $this->command( 'notificationsubscriptions', array( 'remove-missing', '--user=nottest-a' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertSame( 0, expNotificationService::subscriptions( (int)self::$users['a']->attribute( 'id' ), array( 'missing' => true ) )['total'] );
        list( $code ) = $this->command( 'notificationsubscriptions', array( 'list', '--user=nottest-nobody-here' ) );
        $this->assertSame( 1, $code );
        $this->clean();

        list( $code, $out ) = $this->command( 'notificationevents', array( 'list', '--limit=3' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'PASS', $out );
        list( $code, $out ) = $this->command( 'notificationevents', array( 'cleanup', '--older-than=soon' ) );
        $this->assertSame( 2, $code );
        list( $code, $out ) = $this->command( 'notificationevents', array( 'cleanup', '--older-than=3650d', '--dry-run' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'Would remove', $out );
        list( $code ) = $this->command( 'notificationevents', array( 'frobnicate' ) );
        $this->assertSame( 2, $code );
    }
}
