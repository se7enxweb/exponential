<?php
/**
 * Publishing without notification on the installation the tests run on. Live style: no test database. The tests
 * publish folders of their own below node 2 and remove them, the notification events and the operation mementos they
 * made, afterwards; where there is no installation (CI) they are skipped.
 *
 *  PL-01 - The publish operation with notify=false publishes the version and makes no notification event
 *  PL-02 - Without notify, the next version of the same object makes its event as before
 *  PL-03 - A publication held back at pre_publish (as an approval workflow holds it) and resumed from its memento the
 *          way the workflow cronjob resumes it keeps notify=false: published, no event
 *  PL-04 - The policy function: the administrator has content/publish_without_notification, the anonymous user not
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/**
 * The content operations with the trigger pre_publish holding the publication back, as a workflow that waits (an
 * approval) makes eZTrigger::runTrigger() answer: the body memento is stored and the operation halts. No workflow
 * and no trigger row are needed, so nothing else published meanwhile on the installation is held back.
 */
class eZPublishWithoutNotificationHoldBack extends eZModuleOperationInfo
{
    public $held = null;

    function executeTrigger( &$bodyReturnValue, $body,
                             $operationParameterDefinitions, $operationParameters,
                             &$bodyCallCount, $currentLoopData,
                             $triggerRestored, $operationName, &$operationKeys )
    {
        if ( $body['name'] === 'pre_publish' && $this->held === null )
        {
            $this->held = $this->storeBodyMemento( $body['name'], $body['keys'],
                                                   $operationKeys, $operationParameterDefinitions, $operationParameters,
                                                   $bodyCallCount, $currentLoopData, $operationName );
            $bodyReturnValue['result'] = array( 'content' => 'Deffered to cron' );
            return eZModuleOperationInfo::STATUS_HALTED;
        }
        return parent::executeTrigger( $bodyReturnValue, $body, $operationParameterDefinitions, $operationParameters,
                                       $bodyCallCount, $currentLoopData, $triggerRestored, $operationName, $operationKeys );
    }
}

class eZPublishWithoutNotificationLiveTest extends PHPUnit\Framework\TestCase
{
    const ADMIN_ID = 14;

    private static $installation;
    private $previousUser;
    private $objectID = 0;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 4 );
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        $this->previousUser = eZUser::currentUser();
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( self::ADMIN_ID ), self::ADMIN_ID, eZUser::NO_SESSION_REGENERATE );
    }

    protected function tearDown(): void
    {
        if ( $this->objectID )
        {
            $db = eZDB::instance();
            $db->query( "DELETE FROM eznotificationevent WHERE event_type_string = 'ezpublish' AND data_int1 = " . (int)$this->objectID );
            foreach ( array( 1, 2 ) as $version )
            {
                $mainKey = eZOperationMemento::createKey( array( 'object_id' => $this->objectID, 'version' => $version ) );
                eZPersistentObject::removeObject( eZOperationMemento::definition(), array( 'main_key' => $mainKey ) );
            }
            $object = eZContentObject::fetch( $this->objectID );
            if ( $object instanceof eZContentObject )
            {
                $object->purge();
            }
            eZContentObject::clearCache();
        }
        if ( $this->previousUser instanceof eZUser )
        {
            eZUser::setCurrentlyLoggedInUser( $this->previousUser, $this->previousUser->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        }
    }

    private function events()
    {
        $rows = eZDB::instance()->arrayQuery( "SELECT COUNT(*) AS n FROM eznotificationevent WHERE event_type_string = 'ezpublish' AND data_int1 = " . (int)$this->objectID );
        return (int)$rows[0]['n'];
    }

    private function newFolder( $name )
    {
        $class = eZContentClass::fetchByIdentifier( 'folder' );
        if ( !$class instanceof eZContentClass )
        {
            $this->markTestSkipped( 'needs the folder class' );
        }
        $object = $class->instantiate( self::ADMIN_ID );
        $this->objectID = (int)$object->attribute( 'id' );
        $object->createNodeAssignment( 2, true );
        $dataMap = $object->dataMap();
        $dataMap['name']->fromString( $name );
        $dataMap['name']->store();
        return $object;
    }

    private function fresh()
    {
        eZContentObject::clearCache( array( $this->objectID ) );
        return eZContentObject::fetch( $this->objectID );
    }

    private function publish( $version, array $extra = array() )
    {
        $result = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $this->objectID, 'version' => $version ) + $extra );
        if ( isset( $result['status'] ) && $result['status'] != eZModuleOperationInfo::STATUS_CONTINUE )
        {
            $this->markTestSkipped( 'a workflow holds the publication back on this installation' );
        }
        return $this->fresh();
    }

    /** PL-01, PL-02 */
    public function testPublishWithoutNotificationMakesNoEvent()
    {
        $this->newFolder( 'X1 publish without notification' );

        $published = $this->publish( 1, array( 'notify' => false ) );
        $this->assertSame( eZContentObject::STATUS_PUBLISHED, (int)$published->attribute( 'status' ) );
        $this->assertSame( 1, (int)$published->attribute( 'current_version' ) );
        $this->assertSame( 0, $this->events(), 'no notification event for the version published without notification' );

        $newVersion = $published->createNewVersion();
        $republished = $this->publish( (int)$newVersion->attribute( 'version' ) );
        $this->assertSame( (int)$newVersion->attribute( 'version' ), (int)$republished->attribute( 'current_version' ) );
        $this->assertSame( 1, $this->events(), 'the ordinary publish makes its event' );
    }

    /** PL-03 */
    public function testAPublicationResumedFromItsMementoKeepsNotifyFalse()
    {
        $this->newFolder( 'X1 publish without notification, held back' );

        // The editor presses "Publish without notification"; pre_publish holds the publication back
        $info = new eZPublishWithoutNotificationHoldBack( 'content' );
        $info->loadDefinition();
        $result = $info->execute( 'publish', array( 'object_id' => $this->objectID, 'version' => 1, 'notify' => false ) );
        $this->assertSame( eZModuleOperationInfo::STATUS_HALTED, $result['status'] );
        $this->assertInstanceOf( eZOperationMemento::class, $info->held );
        $this->assertNotSame( eZContentObject::STATUS_PUBLISHED, (int)$this->fresh()->attribute( 'status' ), 'held back, not published yet' );
        $this->assertSame( 0, $this->events() );

        // Later the workflow cronjob resumes it, as kernel/private/classes/cronjobs/workflow.php does
        $bodyMemento = eZOperationMemento::fetchChild( $info->held->attribute( 'memento_key' ) );
        $this->assertInstanceOf( eZOperationMemento::class, $bodyMemento, 'the body memento was stored' );
        $mainMemento = $bodyMemento->attribute( 'main_memento' );
        $this->assertInstanceOf( eZOperationMemento::class, $mainMemento, 'the main memento was stored' );
        $mementoData = $bodyMemento->data();
        $this->assertFalse( $mementoData['parameters']['notify'], 'the memento keeps notify' );
        $mementoData['main_memento'] = $mainMemento;
        $mementoData['skip_trigger'] = true;
        $mementoData['memento_key'] = $info->held->attribute( 'memento_key' );
        $bodyMemento->remove();
        $resumed = eZOperationHandler::execute( $mementoData['module_name'], $mementoData['operation_name'], $mementoData['parameters'], $mementoData );
        if ( isset( $resumed['status'] ) && $resumed['status'] != eZModuleOperationInfo::STATUS_CONTINUE )
        {
            $this->markTestSkipped( 'a workflow after publishing holds the publication back on this installation' );
        }

        $published = $this->fresh();
        $this->assertSame( eZContentObject::STATUS_PUBLISHED, (int)$published->attribute( 'status' ) );
        $this->assertSame( 1, (int)$published->attribute( 'current_version' ) );
        $this->assertSame( 0, $this->events(), 'approved later, still without notification' );
    }

    /** PL-04 */
    public function testThePolicyFunction()
    {
        $admin = eZUser::fetch( self::ADMIN_ID );
        $access = $admin->hasAccessTo( 'content', 'publish_without_notification' );
        $this->assertSame( 'yes', $access['accessWord'], 'content/* of the administrator includes it' );

        $anonymous = eZUser::fetch( eZUser::anonymousId() );
        if ( $anonymous instanceof eZUser )
        {
            $access = $anonymous->hasAccessTo( 'content', 'publish_without_notification' );
            $this->assertSame( 'no', $access['accessWord'] );
            ezpINIHelper::setINISetting( 'notification.ini', 'NotificationSettings', 'PublishWithoutNotification', 'enabled' );
            try
            {
                $this->assertTrue( eZContentOperationCollection::canPublishWithoutNotification( $admin ) );
                $this->assertFalse( eZContentOperationCollection::canPublishWithoutNotification( $anonymous ) );
            }
            finally
            {
                ezpINIHelper::restoreINISettings();
            }
        }
    }
}
