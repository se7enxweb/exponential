<?php
/**
 * The location an object that was never published will be published under, as the access checks read it:
 * eZContentObject::mainNodeAssignmentOf(), draftMainNodeAssignment(), draftParentNodeIDArray() and
 * draftCreateAccess(). No database: the node assignments and the parent object are stand-ins.
 *
 *  DL-01 - The assignment marked main is chosen, wherever it is in the list
 *  DL-02 - Without one marked main, the first assignment is chosen
 *  DL-03 - An empty list, or one without objects, has no main assignment
 *  DL-04 - A draft (never published) object names the parent of its main assignment
 *  DL-05 - A published object names none, whatever its assignments say
 *  DL-06 - A draft without node assignment names none
 *  DL-07 - draftCreateAccess() does not apply (null) without a node assignment or for a published object
 *  DL-08 - draftCreateAccess() denies (0) when the parent object is missing
 *  DL-09 - draftCreateAccess() asks the parent for create access to the object's class, with the language
 *  DL-10 - draftCreateAccess() denies when the parent refuses create
 *  DL-11 - draftCreateAccess() asks the parent for the user it is given
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/** A node assignment: only its attributes */
class X1DraftLocationStandInAssignment
{
    private $attributes;

    public function __construct( $attributes )
    {
        $this->attributes = $attributes;
    }

    public function attribute( $name )
    {
        return isset( $this->attributes[$name] ) ? $this->attributes[$name] : null;
    }
}

/** A content object whose node assignments are given instead of fetched */
class X1DraftLocationStandInObject extends eZContentObject
{
    public $standInAssignments = array();

    public function currentVersionNodeAssignments()
    {
        return $this->standInAssignments;
    }
}

/** A parent object that records the create checks asked of it */
class X1DraftLocationStandInParent extends eZContentObject
{
    public $answer = 1;
    public $asked = array();

    public function checkAccess( $functionName, $originalClassID = false, $parentClassID = false, $returnAccessList = false, $language = false, $userID = false )
    {
        $this->asked[] = array( $functionName, $originalClassID, $parentClassID, $returnAccessList, $language, $userID );
        return $this->answer;
    }
}

class eZContentObjectDraftLocationTest extends PHPUnit\Framework\TestCase
{
    private function assignment( $parentNode, $isMain, $parentObject = null )
    {
        return new X1DraftLocationStandInAssignment( array( 'parent_node' => $parentNode, 'is_main' => $isMain,
                                                            'parent_contentobject' => $parentObject ) );
    }

    private function object( $status, array $assignments )
    {
        $object = new X1DraftLocationStandInObject( array( 'id' => 990001, 'status' => $status, 'current_version' => 2,
                                                           'contentclass_id' => 16 ) );
        $object->standInAssignments = $assignments;
        return $object;
    }

    /** DL-01 */
    public function testTheAssignmentMarkedMainIsChosen()
    {
        $first = $this->assignment( 58, 0 );
        $main = $this->assignment( 60, 1 );
        $last = $this->assignment( 61, 0 );
        $this->assertSame( $main, eZContentObject::mainNodeAssignmentOf( array( $first, $main, $last ) ) );
        $this->assertSame( $main, eZContentObject::mainNodeAssignmentOf( array( $main, $first ) ) );
    }

    /** DL-02 */
    public function testWithoutMainTheFirstAssignmentIsChosen()
    {
        $first = $this->assignment( 58, 0 );
        $this->assertSame( $first, eZContentObject::mainNodeAssignmentOf( array( $first, $this->assignment( 60, 0 ) ) ) );
    }

    /** DL-03 */
    public function testAnEmptyListHasNoMainAssignment()
    {
        $this->assertNull( eZContentObject::mainNodeAssignmentOf( array() ) );
        $this->assertNull( eZContentObject::mainNodeAssignmentOf( null ) );
        $this->assertNull( eZContentObject::mainNodeAssignmentOf( array( false, null ) ) );
    }

    /** DL-04 */
    public function testADraftNamesTheParentOfItsMainAssignment()
    {
        $object = $this->object( eZContentObject::STATUS_DRAFT, array( $this->assignment( 58, 0 ), $this->assignment( '60', 1 ) ) );
        $this->assertSame( array( 60 ), $object->draftParentNodeIDArray() );
        $this->assertSame( 60, (int)$object->draftMainNodeAssignment()->attribute( 'parent_node' ) );
    }

    /** DL-05 */
    public function testAPublishedObjectNamesNone()
    {
        $object = $this->object( eZContentObject::STATUS_PUBLISHED, array( $this->assignment( 58, 1 ) ) );
        $this->assertSame( array(), $object->draftParentNodeIDArray() );
        $this->assertNull( $object->draftMainNodeAssignment() );
    }

    /** DL-06 */
    public function testADraftWithoutAssignmentNamesNone()
    {
        $object = $this->object( eZContentObject::STATUS_DRAFT, array() );
        $this->assertSame( array(), $object->draftParentNodeIDArray() );
    }

    /** DL-07 */
    public function testCreateAccessDoesNotApplyWithoutAssignmentOrWhenPublished()
    {
        $this->assertNull( $this->object( eZContentObject::STATUS_DRAFT, array() )->draftCreateAccess() );
        $parent = new X1DraftLocationStandInParent( array( 'id' => 990002, 'contentclass_id' => 1 ) );
        $this->assertNull( $this->object( eZContentObject::STATUS_PUBLISHED, array( $this->assignment( 58, 1, $parent ) ) )->draftCreateAccess() );
        $this->assertSame( array(), $parent->asked );
    }

    /** DL-08 */
    public function testCreateAccessDeniesWithoutParentObject()
    {
        $object = $this->object( eZContentObject::STATUS_DRAFT, array( $this->assignment( 58, 1, null ) ) );
        $this->assertSame( 0, $object->draftCreateAccess() );
    }

    /** DL-09 */
    public function testCreateAccessAsksTheParent()
    {
        $parent = new X1DraftLocationStandInParent( array( 'id' => 990002, 'contentclass_id' => 1 ) );
        $object = $this->object( eZContentObject::STATUS_DRAFT, array( $this->assignment( 58, 1, $parent ) ) );
        $this->assertSame( 1, $object->draftCreateAccess( 'ger-DE' ) );
        $this->assertSame( array( array( 'create', 16, 1, false, 'ger-DE', false ) ), $parent->asked );
    }

    /** DL-10 */
    public function testCreateAccessDeniesWhenTheParentRefuses()
    {
        $parent = new X1DraftLocationStandInParent( array( 'id' => 990002, 'contentclass_id' => 1 ) );
        $parent->answer = 0;
        $object = $this->object( eZContentObject::STATUS_DRAFT, array( $this->assignment( 58, 1, $parent ) ) );
        $this->assertSame( 0, $object->draftCreateAccess() );
    }

    /** DL-11 */
    public function testCreateAccessAsksTheParentForTheGivenUser()
    {
        $parent = new X1DraftLocationStandInParent( array( 'id' => 990002, 'contentclass_id' => 1 ) );
        $object = $this->object( eZContentObject::STATUS_DRAFT, array( $this->assignment( 58, 1, $parent ) ) );
        $this->assertSame( 1, $object->draftCreateAccess( false, 990003 ) );
        $this->assertSame( array( array( 'create', 16, 1, false, false, 990003 ) ), $parent->asked );
    }
}
