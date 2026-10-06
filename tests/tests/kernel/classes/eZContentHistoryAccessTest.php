<?php
/**
 * Who may open the versions of an object (content/history), without the database:
 * \Exponential\View\Kernel\Content\History::canOpen().
 *
 *  HA-01 - Who may read the object opens its history, as before
 *  HA-02 - Who may edit it opens it too: the owner's draft is not readable for the other editors
 *  HA-03 - A further editor an extension lets in (filter content/edit/access) opens it
 *  HA-04 - Who may neither read nor edit stays out, also when a listener refuses edit
 *
 * The object is a stand-in whose permissions are given; the current user is a stand-in anonymous user.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class eZContentHistoryAccessTest extends PHPUnit\Framework\TestCase
{
    private $hadUser;
    private $user;
    private $listeners = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->hadUser = array_key_exists( 'eZUserGlobalInstance_', $GLOBALS );
        $this->user = $this->hadUser ? $GLOBALS['eZUserGlobalInstance_'] : null;
        $GLOBALS['eZUserGlobalInstance_'] = new eZUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'x1anonymous' ) );
    }

    protected function tearDown(): void
    {
        foreach ( $this->listeners as $id )
        {
            ezpEvent::getInstance()->detach( 'content/edit/access', $id );
        }
        if ( $this->hadUser )
            $GLOBALS['eZUserGlobalInstance_'] = $this->user;
        else
            unset( $GLOBALS['eZUserGlobalInstance_'] );
    }

    private function object( $canRead, $canEdit )
    {
        $object = new eZContentObject( array( 'id' => 990401, 'status' => eZContentObject::STATUS_DRAFT ) );
        $permissions = array( 'can_read' => $canRead, 'can_edit' => $canEdit );
        $object->setPermissions( $permissions );
        return $object;
    }

    private function canOpen( $object, $canEdit = null )
    {
        return \Exponential\View\Kernel\Content\History::canOpen( $object, $canEdit );
    }

    /** HA-01 */
    public function testWhoMayReadOpensIt()
    {
        $this->assertTrue( $this->canOpen( $this->object( 1, 0 ) ) );
    }

    /** HA-02 */
    public function testWhoMayEditOpensIt()
    {
        $object = $this->object( 0, 1 );
        $this->assertTrue( $this->canOpen( $object ) );
        $this->assertTrue( $this->canOpen( $object, true ), 'the answer the view has already' );
    }

    /** HA-03 */
    public function testAFurtherEditorAnExtensionLetsInOpensIt()
    {
        $object = $this->object( 0, 0 );
        $this->assertFalse( $this->canOpen( $object ) );
        $this->listeners[] = ezpEvent::getInstance()->attach( 'content/edit/access', function ( $allowed, $subject ) use ( $object )
        {
            return $allowed || $subject === $object;
        } );
        $this->assertTrue( $this->canOpen( $object ) );
    }

    /** HA-04 */
    public function testWhoMayNeitherReadNorEditStaysOut()
    {
        $this->assertFalse( $this->canOpen( $this->object( 0, 0 ) ) );
        $this->assertFalse( $this->canOpen( $this->object( 0, 1 ), false ) );
        $object = $this->object( 0, 1 );
        $this->listeners[] = ezpEvent::getInstance()->attach( 'content/edit/access', function ( $allowed ) { return false; } );
        $this->assertFalse( $this->canOpen( $object ) );
    }
}
