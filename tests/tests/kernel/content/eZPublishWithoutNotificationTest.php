<?php
/**
 * Publishing without notification, without the database.
 *
 *  PN-01 - The button counts only when notification.ini [NotificationSettings] PublishWithoutNotification is enabled
 *  PN-02 - The publish operation takes notify (optional, true by default) and hands it to create-notification
 *  PN-03 - createNotificationEvent() gives content/notification/create false for notify=false, and makes no event
 *  PN-04 - content/edit and content/versionview map the new buttons to their Publish action
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZPublishWithoutNotificationTest extends PHPUnit\Framework\TestCase
{
    private $post;
    private $listeners = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->post = $_POST;
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

    /** PN-01 */
    public function testTheButtonCountsOnlyWhenEnabled()
    {
        $_POST['PublishNotNotifyButton'] = 'x';
        $this->assertSame( 'disabled', eZINI::instance( 'notification.ini' )->variable( 'NotificationSettings', 'PublishWithoutNotification' ), 'the shipped default' );
        $this->assertFalse( eZContentOperationCollection::publishWithoutNotification( 'PublishNotNotifyButton' ) );

        ezpINIHelper::setINISetting( 'notification.ini', 'NotificationSettings', 'PublishWithoutNotification', 'enabled' );
        $this->assertTrue( eZContentOperationCollection::publishWithoutNotification( 'PublishNotNotifyButton' ) );
        $this->assertFalse( eZContentOperationCollection::publishWithoutNotification( 'PreviewPublishNotNotifyButton' ), 'the other button was not pressed' );
        unset( $_POST['PublishNotNotifyButton'] );
        $this->assertFalse( eZContentOperationCollection::publishWithoutNotification( 'PublishNotNotifyButton' ) );
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

    /** PN-02 */
    public function testThePublishOperationTakesNotify()
    {
        $publish = $this->operations()['publish'];
        $notify = $this->parameter( $publish['parameters'], 'notify' );
        $this->assertSame( array( 'name' => 'notify', 'type' => 'boolean', 'required' => false, 'default' => true ), $notify );

        $step = null;
        foreach ( $publish['body'] as $body )
        {
            if ( isset( $body['name'] ) && $body['name'] === 'create-notification' )
                $step = $body;
        }
        $this->assertNotNull( $step );
        $this->assertSame( array( 'object_id', 'version', 'notify' ), array_column( $step['parameters'], 'name' ) );
        $this->assertTrue( $this->parameter( $step['parameters'], 'notify' )['default'] );
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
}
