<?php
/**
 * checkAccess( 'edit' ) with the access list asked for, as eZContentObject::accessList( 'edit' ) does when a
 * module builds its access denied result (content/edit for a visitor without edit access). The result must be
 * the access list, which the access denied page reads as an array, not the integer 0 that gave
 * "Trying to access array offset on int" in the error view. No database: the user and the object are stand-ins.
 *
 *  EAL-01 - Denied edit with the access list asked for returns the list
 *  EAL-02 - Denied edit without the access list stays the integer 0
 *  EAL-03 - Edit allowed by the create rule for a draft stays 1, with or without the list
 *  EAL-04 - accessList( 'edit' ) returns the list
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** A user for whom every content function answers 'no' */
class X1EditAccessListStandInUser extends eZUser
{
    public function isAnonymous()
    {
        return true;
    }

    public function hasAccessTo( $module, $function = false )
    {
        return array(
            'accessWord' => 'no',
            'accessList' => array(
                'FunctionRequired' => array( 'Module' => $module, 'Function' => $function, 'ClassID' => '', 'MainNodeID' => '' ),
                'PolicyList' => array(),
            ),
        );
    }
}

/** An object whose create rule for drafts is given instead of checked */
class X1EditAccessListStandInObject extends eZContentObject
{
    public $draftCreateAnswer = false;

    public function draftCreateAccess( $language = false, $userID = false )
    {
        return $this->draftCreateAnswer;
    }
}

class eZContentObjectEditAccessListTest extends PHPUnit\Framework\TestCase
{
    private $previousInstance;
    private $hadInstance;

    protected function setUp(): void
    {
        $this->hadInstance = array_key_exists( 'eZUserGlobalInstance_', $GLOBALS );
        $this->previousInstance = $this->hadInstance ? $GLOBALS['eZUserGlobalInstance_'] : null;
        $GLOBALS['eZUserGlobalInstance_'] = new X1EditAccessListStandInUser( array( 'contentobject_id' => 987654 ) );
    }

    protected function tearDown(): void
    {
        if ( $this->hadInstance )
        {
            $GLOBALS['eZUserGlobalInstance_'] = $this->previousInstance;
        }
        else
        {
            unset( $GLOBALS['eZUserGlobalInstance_'] );
        }
    }

    private function object()
    {
        return new X1EditAccessListStandInObject( array( 'id' => 123456, 'contentclass_id' => 1, 'owner_id' => 14, 'section_id' => 1, 'status' => 1 ) );
    }

    /** EAL-01 */
    public function testDeniedEditReturnsTheAccessListWhenAskedFor()
    {
        $list = $this->object()->checkAccess( 'edit', false, false, true );
        $this->assertIsArray( $list );
        $this->assertSame( 'content', $list['FunctionRequired']['Module'] );
        $this->assertSame( 'edit', $list['FunctionRequired']['Function'] );
    }

    /** EAL-02 */
    public function testDeniedEditWithoutTheAccessListIsZero()
    {
        $this->assertSame( 0, $this->object()->checkAccess( 'edit' ) );
    }

    /** EAL-03 */
    public function testEditAllowedByTheDraftCreateRuleStaysOne()
    {
        $object = $this->object();
        $object->draftCreateAnswer = true;
        $this->assertSame( 1, $object->checkAccess( 'edit' ) );
        $this->assertSame( 1, $object->checkAccess( 'edit', false, false, true ) );
    }

    /** EAL-04 */
    public function testAccessListForEditIsAnArray()
    {
        $list = $this->object()->accessList( 'edit' );
        $this->assertIsArray( $list );
        $this->assertArrayHasKey( 'FunctionRequired', $list );
    }
}
