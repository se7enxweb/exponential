<?php
/**
 * The filters through which an extension takes part in access decisions and view cache keys, without the database:
 *
 *  PH-01 - content/view/cachekeys gets named keys and the context; without listener the cache file name is the same
 *          as from the positional keys before
 *  PH-02 - A listener of content/view/cachekeys can leave keys out or add some; one that returns no array changes
 *          nothing
 *  PH-03 - content/download/access gets the kernel's answer with object, attribute and version; only true allows,
 *          and a refusal keeps the kernel's error
 *  PH-04 - content/edit/access gets canEdit() with object, version, user and language; only true allows
 *  PH-05 - collaboration/item/access gets whether the user takes part, with item and user; only true opens the item
 *  PH-06 - content/notification/create gets true with object and version; false leaves the event out
 *
 * Objects, nodes, collaboration items and the user are stand-ins whose answers are given.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

/** A user whose roles and role limitations are given */
class X1PermissionHooksUser extends eZUser
{
    public function roleIDList()
    {
        return array( 1, 2 );
    }

    public function limitValueList()
    {
        return array();
    }
}

/** A location the user may read */
class X1PermissionHooksNode
{
    public function attribute( $name )
    {
        return $name === 'is_invisible' ? 0 : null;
    }

    public function canRead()
    {
        return true;
    }
}

/** An object whose locations are given instead of fetched */
class X1PermissionHooksObject extends eZContentObject
{
    public $standInNodes = array();

    public function assignedNodes( $asObject = true, $checkVisibility = false, $offset = false, $limit = false, $sortField = false, $sortOrder = 'asc' )
    {
        return $this->standInNodes;
    }
}

/** A collaboration item whose participants are given */
class X1PermissionHooksCollaborationItem extends eZCollaborationItem
{
    public $participant = false;

    public function userIsParticipant( eZUser $user )
    {
        return $this->participant;
    }
}

class expPermissionHooksTest extends PHPUnit\Framework\TestCase
{
    private $listeners = array();
    private $globals = array();
    private $asked = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        foreach ( array( 'eZCurrentAccess', 'eZUserGlobalInstance_' ) as $name )
        {
            $this->globals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        }
        $GLOBALS['eZCurrentAccess'] = array( 'name' => 'x1site', 'type' => eZSiteAccess::TYPE_DEFAULT );
        $GLOBALS['eZUserGlobalInstance_'] = $this->user();
    }

    protected function tearDown(): void
    {
        foreach ( $this->listeners as $listener )
        {
            ezpEvent::getInstance()->detach( $listener[0], $listener[1] );
        }
        foreach ( $this->globals as $name => $value )
        {
            if ( $value === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $value[0];
        }
    }

    /** Attaches a listener of $event that records its arguments and answers with $answer( $value ) */
    private function listen( $event, $answer )
    {
        $asked = &$this->asked;
        $id = ezpEvent::getInstance()->attach( $event, function () use ( $event, $answer, &$asked )
        {
            $arguments = func_get_args();
            $asked[$event][] = $arguments;
            return $answer( $arguments[0] );
        } );
        $this->listeners[] = array( $event, $id );
    }

    private function user()
    {
        return new X1PermissionHooksUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'x1anonymous',
                                                 'email' => 'x1anonymous@x1.example.invalid' ) );
    }

    private function cacheFile( $tweak = 'ignore_discountlist;ignore_userpreferences' )
    {
        $file = eZNodeviewfunctions::generateViewCacheFile( $this->user(), 2, 0, false, 'eng-US', 'full', false, false, $tweak );
        return $file['cache_file'];
    }

    /** PH-01 */
    public function testCacheKeysAreNamedAndTheFileNameIsUnchanged()
    {
        $before = '2-' . md5( implode( '-', array( 2, 'full', 'eng-US', 0, false, '1.2', '', eZSys::indexFile() ) ) ) . '.cache';
        $this->assertSame( $before, $this->cacheFile() );

        $this->listen( 'content/view/cachekeys', function ( $keys ) { return $keys; } );
        $this->assertSame( $before, $this->cacheFile() );
        list( $keys, $context ) = $this->asked['content/view/cachekeys'][0];
        $this->assertSame( array( 'node_id', 'viewmode', 'language', 'offset', 'layout', 'userroles', 'userlimitedlist', 'access_path' ),
                           array_keys( $keys ) );
        $this->assertSame( '1.2', $keys['userroles'] );
        $this->assertSame( array( 'user', 'node_id', 'view_mode', 'language', 'offset', 'layout', 'view_parameters', 'view_cache_tweak' ),
                           array_keys( $context ) );
        $this->assertSame( 2, $context['node_id'] );
        $this->assertSame( 'full', $context['view_mode'] );
        $this->assertInstanceOf( 'eZUser', $context['user'] );
    }

    /** PH-02 */
    public function testCacheKeysCanBeChanged()
    {
        $this->listen( 'content/view/cachekeys', function ( $keys )
        {
            unset( $keys['userroles'], $keys['userlimitedlist'] );
            $keys['x1_matrix'] = 'm7';
            return $keys;
        } );
        $this->assertSame( '2-' . md5( implode( '-', array( 2, 'full', 'eng-US', 0, false, eZSys::indexFile(), 'm7' ) ) ) . '.cache',
                           $this->cacheFile() );

        $this->tearDown();
        $this->setUp();
        $this->listen( 'content/view/cachekeys', function ( $keys ) { return null; } );
        $this->assertSame( '2-' . md5( implode( '-', array( 2, 'full', 'eng-US', 0, false, '1.2', '', eZSys::indexFile() ) ) ) . '.cache',
                           $this->cacheFile() );
    }

    private function downloadObject( $canRead )
    {
        $object = new X1PermissionHooksObject( array( 'id' => 990201, 'status' => eZContentObject::STATUS_PUBLISHED,
                                                      'current_version' => 3 ) );
        $permissions = array( 'can_read' => $canRead );
        $object->setPermissions( $permissions );
        $object->standInNodes = array( new X1PermissionHooksNode() );
        return $object;
    }

    /** PH-03 */
    public function testDownloadAccessGoesThroughTheFilter()
    {
        $attribute = new eZContentObjectAttribute( array( 'id' => 990202, 'contentobject_id' => 990201, 'version' => 3 ) );
        $download = '\Exponential\View\Kernel\Content\Download';

        // The kernel allows and denies on its own
        $this->assertNull( $download::access( $this->downloadObject( 1 ), $attribute, 3, 3 ) );
        $this->assertSame( eZError::KERNEL_ACCESS_DENIED, $download::access( $this->downloadObject( 0 ), $attribute, 3, 3 ) );

        // A listener lets someone in, with the object, the attribute and the version
        $this->listen( 'content/download/access', function ( $allowed ) { return true; } );
        $denied = $this->downloadObject( 0 );
        $this->assertNull( $download::access( $denied, $attribute, '3', 3 ) );
        $this->assertSame( array( false, $denied, $attribute, 3 ), $this->asked['content/download/access'][0] );

        // A listener keeps someone out; only true allows
        $this->tearDown();
        $this->setUp();
        $this->listen( 'content/download/access', function ( $allowed ) { return 1; } );
        $this->assertSame( eZError::KERNEL_ACCESS_DENIED, $download::access( $this->downloadObject( 1 ), $attribute, 3, 3 ) );
    }

    /** PH-04 */
    public function testEditAccessGoesThroughTheFilter()
    {
        $object = new eZContentObject( array( 'id' => 990203, 'status' => eZContentObject::STATUS_DRAFT ) );
        $permissions = array( 'can_edit' => 0 );
        $object->setPermissions( $permissions );
        $version = new eZContentObjectVersion( array( 'id' => 990204, 'contentobject_id' => 990203, 'version' => 2 ) );

        $this->assertFalse( $object->editAccess( $version ) );

        $this->listen( 'content/edit/access', function ( $allowed ) { return true; } );
        $this->assertTrue( $object->editAccess( $version ) );
        $this->assertTrue( $object->editAccess( 'no version' ) );
        $this->assertSame( array( false, $object, $version, (int)eZUser::anonymousId(), false ), $this->asked['content/edit/access'][0] );
        $this->assertNull( $this->asked['content/edit/access'][1][2] );

        $this->tearDown();
        $this->setUp();
        $permissions = array( 'can_edit' => 1 );
        $object->setPermissions( $permissions );
        $this->assertTrue( $object->editAccess() );
        $this->listen( 'content/edit/access', function ( $allowed ) { return 'yes'; } );
        $this->assertFalse( $object->editAccess() );
    }

    /** PH-05 */
    public function testCollaborationItemAccessGoesThroughTheFilter()
    {
        $user = $this->user();
        $item = '\Exponential\View\Kernel\Collaboration\Item';
        $collabItem = new X1PermissionHooksCollaborationItem( array( 'id' => 990205 ) );

        $isParticipant = null;
        $this->assertFalse( $item::access( $collabItem, $user, $isParticipant ) );
        $this->assertFalse( $isParticipant );
        $collabItem->participant = true;
        $this->assertTrue( $item::access( $collabItem, $user, $isParticipant ) );
        $this->assertTrue( $isParticipant );

        // A supervisor who does not take part
        $collabItem->participant = false;
        $this->listen( 'collaboration/item/access', function ( $allowed ) { return true; } );
        $this->assertTrue( $item::access( $collabItem, $user, $isParticipant ) );
        $this->assertFalse( $isParticipant );
        $this->assertSame( array( false, $collabItem, $user ), $this->asked['collaboration/item/access'][0] );
    }

    /** PH-06 */
    public function testNotificationEventCanBeLeftOut()
    {
        $this->listen( 'content/notification/create', function ( $create ) { return false; } );
        // false never reaches the database: no event is created
        $this->assertNull( eZContentOperationCollection::createNotificationEvent( '990206', '4' ) );
        $this->assertSame( array( array( true, 990206, 4 ) ), $this->asked['content/notification/create'] );
    }
}
