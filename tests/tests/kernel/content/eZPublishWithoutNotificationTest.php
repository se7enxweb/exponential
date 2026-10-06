<?php
/**
 * Publishing without notification, without the database.
 *
 *  PN-01 - The button counts only when notification.ini [NotificationSettings] PublishWithoutNotification is enabled
 *  PN-02 - The publish operation takes notify (optional, true by default) and hands it to create-notification
 *  PN-03 - createNotificationEvent() gives content/notification/create false for notify=false, and makes no event
 *  PN-04 - content/edit and content/versionview map the new buttons to their Publish action
 *  PN-05 - The button counts only for a user with content/publish_without_notification (a limited policy too)
 *  PN-06 - A posted button without the setting or without the policy is the ordinary publish, not a refusal
 *  PN-07 - 0 and "0" mean no as false does; true, null and anything else mean yes
 *  PN-08 - With asynchronous publishing, a publication without notification is published at once, not queued
 *  PN-09 - notify=false kept in a stored memento reaches create-notification when the operation resumes
 *  PN-10 - The content module has the function publish_without_notification, without limitations
 *  PN-11 - Every template that shows the button asks for the setting and the policy; the conflict pages offer it again
 *  PN-12 - The views pass the choice on to the conflict page
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

/**
 * A user whose policies say $accessWord for every function, without the database.
 */
class eZPublishWithoutNotificationTestUser extends eZUser
{
    public $accessWord;
    public $asked = array();

    public function __construct( $accessWord )
    {
        $this->accessWord = $accessWord;
    }

    function hasAccessTo( $module, $function = false )
    {
        $this->asked[] = "$module/$function";
        return array( 'accessWord' => $this->accessWord );
    }
}

/**
 * Records what the operation hands to create-notification.
 */
class eZPublishWithoutNotificationTestRecorder
{
    public static $calls = array();

    public static function createNotificationEvent( $objectID, $versionNum, $notify = true )
    {
        self::$calls[] = array( $objectID, $versionNum, $notify );
        return array( 'status' => eZModuleOperationInfo::STATUS_CONTINUE );
    }
}

class eZPublishWithoutNotificationTest extends PHPUnit\Framework\TestCase
{
    private $post;
    private $listeners = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->post = $_POST;
        eZPublishWithoutNotificationTestRecorder::$calls = array();
    }

    protected function tearDown(): void
    {
        $_POST = $this->post;
        foreach ( $this->listeners as $id )
        {
            ezpEvent::getInstance()->detach( 'content/notification/create', $id );
        }
        ezpINIHelper::restoreINISettings();
    }

    private function listen( array &$asked )
    {
        $this->listeners[] = ezpEvent::getInstance()->attach( 'content/notification/create', function ( $create, $objectID, $version ) use ( &$asked )
        {
            $asked[] = array( $create, $objectID, $version );
            return false; // never reach the database
        } );
    }

    /** PN-01 */
    public function testTheButtonCountsOnlyWhenEnabled()
    {
        $editor = new eZPublishWithoutNotificationTestUser( 'yes' );
        $_POST['PublishNotNotifyButton'] = 'x';
        $this->assertSame( 'disabled', eZINI::instance( 'notification.ini' )->variable( 'NotificationSettings', 'PublishWithoutNotification' ), 'the shipped default' );
        $this->assertFalse( eZContentOperationCollection::publishWithoutNotificationEnabled() );
        $this->assertFalse( eZContentOperationCollection::publishWithoutNotification( 'PublishNotNotifyButton', $editor ) );
        $this->assertFalse( eZContentOperationCollection::canPublishWithoutNotification( $editor ) );
        $this->assertSame( array(), $editor->asked, 'with the setting disabled the policies are not even asked' );

        ezpINIHelper::setINISetting( 'notification.ini', 'NotificationSettings', 'PublishWithoutNotification', 'enabled' );
        $this->assertTrue( eZContentOperationCollection::publishWithoutNotificationEnabled() );
        $this->assertTrue( eZContentOperationCollection::publishWithoutNotification( 'PublishNotNotifyButton', $editor ) );
        $this->assertFalse( eZContentOperationCollection::publishWithoutNotification( 'PreviewPublishNotNotifyButton', $editor ), 'the other button was not pressed' );
        unset( $_POST['PublishNotNotifyButton'] );
        $this->assertFalse( eZContentOperationCollection::publishWithoutNotification( 'PublishNotNotifyButton', $editor ) );

        ezpINIHelper::setINISetting( 'notification.ini', 'NotificationSettings', 'PublishWithoutNotification', 'true' );
        $this->assertFalse( eZContentOperationCollection::publishWithoutNotificationEnabled(), 'only "enabled" enables it' );
    }

    private function operations()
    {
        $OperationList = array();
        include 'kernel/content/operation_definition.php';
        return $OperationList;
    }

    private function parameter( array $parameters, $name )
    {
        foreach ( $parameters as $parameter )
        {
            if ( $parameter['name'] === $name )
                return $parameter;
        }
        return null;
    }

    private function step( $name )
    {
        foreach ( $this->operations()['publish']['body'] as $body )
        {
            if ( isset( $body['name'] ) && $body['name'] === $name )
                return $body;
        }
        return null;
    }

    /** PN-02 */
    public function testThePublishOperationTakesNotify()
    {
        $publish = $this->operations()['publish'];
        $notify = $this->parameter( $publish['parameters'], 'notify' );
        $this->assertSame( array( 'name' => 'notify', 'type' => 'boolean', 'required' => false, 'default' => true ), $notify );
        $this->assertSame( array( 'object_id', 'version' ), $publish['keys'], 'notify is no key: a held back publication is found by object and version' );
        $info = new eZModuleOperationInfo( 'content', false );
        $plain = $info->makeOperationKeyArray( $publish, array( 'object_id' => 1, 'version' => 2 ) );
        $this->assertSame( array( 'object_id' => 1, 'version' => 2 ), $plain, 'a caller without notify: the key of before' );
        $this->assertSame( $plain, $info->makeOperationKeyArray( $publish, array( 'object_id' => 1, 'version' => 2, 'notify' => false ) ) );

        $step = $this->step( 'create-notification' );
        $this->assertNotNull( $step );
        $this->assertSame( array( 'object_id', 'version', 'notify' ), array_column( $step['parameters'], 'name' ) );
        $this->assertTrue( $this->parameter( $step['parameters'], 'notify' )['default'] );

        $queue = $this->step( 'send-to-publishing-queue' );
        $this->assertSame( array( 'object_id', 'version', 'notify' ), array_column( $queue['parameters'], 'name' ) );
    }

    /** PN-03 */
    public function testNotifyFalseReachesTheFilterAndMakesNoEvent()
    {
        $asked = array();
        $this->listeners[] = ezpEvent::getInstance()->attach( 'content/notification/create', function ( $create, $objectID, $version ) use ( &$asked )
        {
            $asked[] = array( $create, $objectID, $version );
            return $create;
        } );
        // false never reaches the database: no event is made
        $this->assertNull( eZContentOperationCollection::createNotificationEvent( '990301', '2', false ) );
        $this->assertSame( array( array( false, 990301, 2 ) ), $asked );
    }

    /** PN-04 */
    public function testTheViewsMapTheButtonsToPublish()
    {
        // Read from the source: loading the content module asks the database for the state groups
        $source = file_get_contents( 'kernel/content/module.php' );
        $this->assertStringContainsString( "'PublishNotNotifyButton' => 'Publish'", $source );
        $this->assertStringContainsString( "'PreviewPublishNotNotifyButton' => 'Publish'", $source );
    }

    /** PN-05 */
    public function testTheButtonCountsOnlyWithThePolicy()
    {
        ezpINIHelper::setINISetting( 'notification.ini', 'NotificationSettings', 'PublishWithoutNotification', 'enabled' );
        $_POST['PublishNotNotifyButton'] = 'x';

        $none = new eZPublishWithoutNotificationTestUser( 'no' );
        $this->assertFalse( eZContentOperationCollection::canPublishWithoutNotification( $none ) );
        $this->assertFalse( eZContentOperationCollection::publishWithoutNotification( 'PublishNotNotifyButton', $none ) );
        $this->assertContains( 'content/publish_without_notification', $none->asked );

        // fetch( 'user', 'has_access_to' ) shows the button for a limited policy (content/* with a limitation), so
        // the button counts there as well: what is shown is what is done
        $limited = new eZPublishWithoutNotificationTestUser( 'limited' );
        $this->assertTrue( eZContentOperationCollection::publishWithoutNotification( 'PublishNotNotifyButton', $limited ) );

        $all = new eZPublishWithoutNotificationTestUser( 'yes' );
        $this->assertTrue( eZContentOperationCollection::canPublishWithoutNotification( $all ) );
    }

    /** PN-06 */
    public function testATamperedButtonIsTheOrdinaryPublish()
    {
        $_POST['PublishNotNotifyButton'] = 'x';
        $_POST['PreviewPublishNotNotifyButton'] = 'x';
        $editor = new eZPublishWithoutNotificationTestUser( 'yes' );
        $none = new eZPublishWithoutNotificationTestUser( 'no' );

        // setting disabled: notify stays true, whoever posts it
        $this->assertTrue( !eZContentOperationCollection::publishWithoutNotification( 'PublishNotNotifyButton', $editor ) );
        $this->assertTrue( !eZContentOperationCollection::publishWithoutNotification( 'PreviewPublishNotNotifyButton', $editor ) );

        // setting enabled, no policy: notify stays true
        ezpINIHelper::setINISetting( 'notification.ini', 'NotificationSettings', 'PublishWithoutNotification', 'enabled' );
        $this->assertTrue( !eZContentOperationCollection::publishWithoutNotification( 'PublishNotNotifyButton', $none ) );
        $this->assertTrue( !eZContentOperationCollection::publishWithoutNotification( 'PreviewPublishNotNotifyButton', $none ) );
    }

    /** PN-07 */
    public function testTheSpellingsOfNo()
    {
        $asked = array();
        $this->listen( $asked );
        foreach ( array( false, 0, '0' ) as $no )
        {
            eZContentOperationCollection::createNotificationEvent( 1, 1, $no );
        }
        foreach ( array( true, 1, '1', null, 'yes' ) as $yes )
        {
            eZContentOperationCollection::createNotificationEvent( 1, 1, $yes );
        }
        eZContentOperationCollection::createNotificationEvent( 1, 1 );
        $this->assertSame( array( false, false, false, true, true, true, true, true, true ), array_column( $asked, 0 ) );
    }

    /** PN-08 */
    public function testAsynchronousPublishingLeavesAPublicationWithoutNotificationAlone()
    {
        ezpINIHelper::setINISetting( 'content.ini', 'PublishingSettings', 'AsynchronousPublishing', 'enabled' );
        foreach ( array( false, 0, '0' ) as $no )
        {
            $behaviour = new ezpContentPublishingBehaviour();
            $behaviour->isTemporary = true;
            $behaviour->disableAsynchronousPublishing = false;
            ezpContentPublishingBehaviour::setBehaviour( $behaviour );
            // a queued publication would ask the queue table; this one is published at once
            $this->assertSame( array( 'status' => eZModuleOperationInfo::STATUS_CONTINUE ),
                               eZContentOperationCollection::sendToPublishingQueue( 990302, 1, $no ) );
        }
    }

    /** PN-09 */
    public function testNotifyFromAStoredMementoReachesCreateNotification()
    {
        // What storeBodyMemento() stores when a workflow holds the publication back, and what the workflow cronjob
        // hands to the operation again: the memento is serialized into the database and read back
        $memento = eZOperationMemento::create( array( 'object_id' => 990303, 'version' => 2 ),
                                               array( 'name' => 'pre_publish',
                                                      'parameters' => array( 'object_id' => 990303, 'version' => 2, 'notify' => false ),
                                                      'module_name' => 'content', 'operation_name' => 'publish' ) );
        $stored = new eZOperationMemento( array( 'memento_data' => $memento->attribute( 'memento_data' ) ) );
        $parameters = $stored->data()['parameters'];
        $this->assertFalse( $parameters['notify'] );

        $info = new eZModuleOperationInfo( 'content', false );
        $step = $this->step( 'create-notification' );
        $info->executeClassMethod( __FILE__, 'eZPublishWithoutNotificationTestRecorder', 'createNotificationEvent', $step['parameters'], $parameters );
        $info->executeClassMethod( __FILE__, 'eZPublishWithoutNotificationTestRecorder', 'createNotificationEvent', $step['parameters'], array( 'object_id' => 990303, 'version' => 3 ) );
        $this->assertSame( array( array( 990303, 2, false ), array( 990303, 3, true ) ), eZPublishWithoutNotificationTestRecorder::$calls,
                           'false is passed on, not replaced by the default; a missing notify is the default true' );
    }

    /** PN-10 */
    public function testTheContentModuleHasTheFunction()
    {
        $source = file_get_contents( 'kernel/content/module.php' );
        $this->assertStringContainsString( "\$FunctionList['publish_without_notification'] = array();", $source );
    }

    /** PN-11 */
    public function testTheTemplatesAskForTheSettingAndThePolicy()
    {
        $condition = "{if and( ezini( 'NotificationSettings', 'PublishWithoutNotification', 'notification.ini' )|eq( 'enabled' ), fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'publish_without_notification' ) ) )}";
        $buttons = array(
            'design/admin/templates/content/edit.tpl' => array( 'PublishNotNotifyButton', 2 ),
            'design/admin3/templates/content/edit.tpl' => array( 'PublishNotNotifyButton', 2 ),
            'design/admin4/templates/content/edit.tpl' => array( 'PublishNotNotifyButton', 2 ),
            'design/standard/templates/content/edit.tpl' => array( 'PublishNotNotifyButton', 1 ),
            'design/admin/templates/content/view/versionview.tpl' => array( 'PreviewPublishNotNotifyButton', 1 ),
            'design/admin3/templates/content/view/versionview.tpl' => array( 'PreviewPublishNotNotifyButton', 1 ),
            'design/admin4/templates/content/view/versionview.tpl' => array( 'PreviewPublishNotNotifyButton', 1 ),
            'design/standard/templates/content/view/versionview.tpl' => array( 'PreviewPublishNotNotifyButton', 1 ),
            'design/standard/templates/content/view/versionviewframe.tpl' => array( 'PreviewPublishNotNotifyButton', 1 ),
        );
        foreach ( $buttons as $file => list( $name, $count ) )
        {
            $source = file_get_contents( $file );
            $this->assertSame( $count, substr_count( $source, $condition ), "$file asks for the setting and the policy" );
            $this->assertSame( $count, substr_count( $source, 'name="' . $name . '"' ), "$file has the button" );
        }
        foreach ( array( 'admin', 'admin4', 'standard' ) as $design )
        {
            $source = file_get_contents( "design/$design/templates/content/edit_conflict.tpl" );
            $this->assertStringContainsString( '{if is_set( $publish_without_notification )}{if $publish_without_notification}', $source );
            $this->assertSame( 1, substr_count( $source, 'name="PublishNotNotifyButton"' ), "the conflict page of $design offers it again" );
        }
    }

    /** PN-12 */
    public function testTheViewsPassTheChoiceToTheConflictPage()
    {
        $this->assertStringContainsString( "\$tpl->setVariable( 'publish_without_notification', eZContentOperationCollection::publishWithoutNotification( 'PublishNotNotifyButton' ) );",
                                           file_get_contents( 'kernel/private/classes/views/content/edit.php' ) );
        $this->assertStringContainsString( "\$tpl->setVariable( 'publish_without_notification', eZContentOperationCollection::publishWithoutNotification( 'PreviewPublishNotNotifyButton' ) );",
                                           file_get_contents( 'kernel/content/versionviewframe.php' ) );
    }
}
