<?php
/**
 * Publishing without notification on the installation the tests run on. Live style: no test database. The test
 * publishes a folder of its own below node 2 and removes it, and the notification events it made, afterwards; where
 * there is no installation (CI) it is skipped.
 *
 *  PL-01 - The publish operation with notify=false publishes the version and makes no notification event
 *  PL-02 - Without notify, the next version of the same object makes its event as before
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

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

    private function publish( $version, array $extra = array() )
    {
        $result = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $this->objectID, 'version' => $version ) + $extra );
        if ( isset( $result['status'] ) && $result['status'] != eZModuleOperationInfo::STATUS_CONTINUE )
        {
            $this->markTestSkipped( 'a workflow holds the publication back on this installation' );
        }
        eZContentObject::clearCache( array( $this->objectID ) );
        return eZContentObject::fetch( $this->objectID );
    }

    /** PL-01, PL-02 */
    public function testPublishWithoutNotificationMakesNoEvent()
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
        $dataMap['name']->fromString( 'X1 publish without notification' );
        $dataMap['name']->store();

        $published = $this->publish( 1, array( 'notify' => false ) );
        $this->assertSame( eZContentObject::STATUS_PUBLISHED, (int)$published->attribute( 'status' ) );
        $this->assertSame( 1, (int)$published->attribute( 'current_version' ) );
        $this->assertSame( 0, $this->events(), 'no notification event for the version published without notification' );

        $newVersion = $published->createNewVersion();
        $republished = $this->publish( (int)$newVersion->attribute( 'version' ) );
        $this->assertSame( (int)$newVersion->attribute( 'version' ), (int)$republished->attribute( 'current_version' ) );
        $this->assertSame( 1, $this->events(), 'the ordinary publish makes its event' );
    }
}
