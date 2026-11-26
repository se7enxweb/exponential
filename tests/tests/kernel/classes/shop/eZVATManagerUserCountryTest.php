<?php
/**
 * eZVATManager::setUserCountry() without the database: the anonymous user (every guest at checkout) is refused and
 * its content object is never read or stored, so one guest's checkout country never becomes the country of every
 * visitor; a registered user's country attribute is still set and stored.
 *
 * No database: the users, the content object and its attribute are mocks; shop.ini is set for the test and put back.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group shop
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
#[\PHPUnit\Framework\Attributes\Group('shop')]
class eZVATManagerUserCountryTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->setting( 'VATSettings', 'UserCountryAttribute', 'country' );
        $this->setting( 'VATSettings', 'RequireUserCountry', 'true' );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'shop.ini' );
        foreach ( array_reverse( $this->saved ) as $entry )
        {
            if ( $entry[2] === null )
                $ini->removeSetting( $entry[0], $entry[1] );
            else
                $ini->setVariable( $entry[0], $entry[1], $entry[2][0] );
        }
        $this->saved = array();
    }

    private function setting( $group, $name, $value )
    {
        $ini = eZINI::instance( 'shop.ini' );
        $this->saved[] = array( $group, $name, $ini->hasVariable( $group, $name ) ? array( $ini->variable( $group, $name ) ) : null );
        $ini->setVariable( $group, $name, $value );
    }

    /**
     * A user whose attribute() must never be called: setUserCountry() has to stop before it reads the content object.
     */
    private function untouchedUser( $contentObjectID )
    {
        $user = $this->getMockBuilder( 'eZUser' )
            ->setConstructorArgs( array( array( 'contentobject_id' => $contentObjectID ) ) )
            ->onlyMethods( array( 'attribute' ) )
            ->getMock();
        $user->expects( $this->never() )->method( 'attribute' );
        return $user;
    }

    public function testAnonymousUserIsRefused()
    {
        $this->assertFalse( eZVATManager::setUserCountry( $this->untouchedUser( eZUser::anonymousId() ), 'DE' ) );
    }

    public function testUserWithoutContentObjectIsRefused()
    {
        $this->assertFalse( eZVATManager::setUserCountry( $this->untouchedUser( -1 ), 'DE' ) );
    }

    public function testRegisteredUserCountryIsStored()
    {
        $attribute = $this->createMock( 'eZContentObjectAttribute' );
        $attribute->method( 'content' )->willReturn( array( 'value' => '' ) );
        $attribute->expects( $this->once() )->method( 'setContent' )->with( array( 'value' => 'DE' ) );
        $attribute->expects( $this->once() )->method( 'store' );

        $object = $this->createMock( 'eZContentObject' );
        $object->expects( $this->atLeastOnce() )->method( 'attribute' )->willReturnMap( array( array( 'data_map', array( 'country' => $attribute ) ) ) );

        $user = $this->getMockBuilder( 'eZUser' )
            ->setConstructorArgs( array( array( 'contentobject_id' => eZUser::anonymousId() + 1000 ) ) )
            ->onlyMethods( array( 'attribute' ) )
            ->getMock();
        $user->expects( $this->atLeastOnce() )->method( 'attribute' )->willReturnMap( array(
            array( 'contentobject', $object ),
            array( 'login', 'buyer' ),
        ) );

        $this->assertTrue( eZVATManager::setUserCountry( $user, 'DE' ) );
    }
}
