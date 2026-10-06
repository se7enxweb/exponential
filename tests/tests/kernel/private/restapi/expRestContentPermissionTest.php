<?php
/**
 * expRestContentPermission, the check of the current user's rights before a REST read or write
 * (doc/guides/api-keys.md, "Permissions of the REST interface"), without a database: nodes, objects, classes and
 * languages are stand-ins whose answers the tests set.
 *
 *  CP-01 create: parentNodeID and classIdentifier are required (400), an unknown parent is 404, an unknown class or
 *        language 400; content/create is asked of the parent object with the class, the parent's class and the language
 *  CP-02 read: the node's or the object's canRead(); missing ids 400, unknown ones 404
 *  CP-03 edit: canEdit() of the node, and in the language when one is given
 *  CP-04 remove: canRemove() of every location of the object and every node below them
 *  CP-05 an unknown action allows nothing; ids must be positive integers
 *  CP-06 forRequest() reads the route variables and the POST fields; the API key guard refuses only on 403
 *  CP-07 the edit check goes through the filter content/edit/access as eZContentObject::editAccess() does: a
 *        listener gets the kernel's answer, the object, no version and the language; only true allows
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group rest
 */

class expRestContentPermissionTestObject extends eZContentObject
{
    public $id = 0;
    public $classID = 1;
    public $read = true;
    public $create = array();
    public $editLanguages = array();
    public $nodes = array();
    public $calls = array();

    public function __construct( $id )
    {
        $this->id = $id;
    }

    public function attribute( $attr, $noFunction = false )
    {
        if ( $attr === 'contentclass_id' )
            return $this->classID;
        return $attr === 'id' ? $this->id : null;
    }

    public function canRead()
    {
        return $this->read;
    }

    public function checkAccess( $functionName, $originalClassID = false, $parentClassID = false, $returnAccessList = false, $language = false )
    {
        $this->calls[] = array( $functionName, $originalClassID, $parentClassID, $language );
        return in_array( $originalClassID . '/' . ( $language === false ? '*' : $language ), $this->create, true ) ? 1 : 0;
    }

    public function canEdit( $originalClassID = false, $parentClassID = false, $returnAccessList = false, $language = false )
    {
        return in_array( $language, $this->editLanguages, true );
    }

    public function assignedNodes( $asObject = true, $checkVisibility = false, $offset = false, $limit = false, $sortField = false, $sortOrder = 'asc' )
    {
        return $this->nodes;
    }
}

class expRestContentPermissionTestNode extends eZContentObjectTreeNode
{
    public $id;
    public $object;
    public $read = true;
    public $edit = true;
    public $remove = true;

    public function __construct( $id, $object = null )
    {
        $this->id = $id;
        $this->object = $object;
    }

    public function attribute( $attr, $noFunction = false )
    {
        switch ( $attr )
        {
            case 'node_id': return $this->id;
            case 'object': return $this->object;
        }
        return null;
    }

    public function canRead()
    {
        return $this->read;
    }

    public function canEdit()
    {
        return $this->edit;
    }

    public function canRemove()
    {
        return $this->remove;
    }
}

class expRestContentPermissionTestClass extends eZContentClass
{
    public $id;

    public function __construct( $id )
    {
        $this->id = $id;
    }

    public function attribute( $attr, $noFunction = false )
    {
        return $attr === 'id' ? $this->id : null;
    }
}

class expRestContentPermissionTestDouble extends expRestContentPermission
{
    public static $nodes = array();
    public static $objects = array();
    public static $classes = array();
    public static $languages = array( 'eng-US', 'ger-DE' );
    public static $subtreeRemovable = true;
    public static $subtreeAsked = array();

    protected static function fetchNode( $nodeID )
    {
        return isset( self::$nodes[$nodeID] ) ? self::$nodes[$nodeID] : null;
    }

    protected static function fetchObject( $objectID )
    {
        return isset( self::$objects[$objectID] ) ? self::$objects[$objectID] : null;
    }

    protected static function fetchClass( $identifier )
    {
        return isset( self::$classes[$identifier] ) ? self::$classes[$identifier] : null;
    }

    protected static function languageExists( $locale )
    {
        return in_array( $locale, self::$languages, true );
    }

    protected static function subtreeRemovable( array $nodeIDs )
    {
        self::$subtreeAsked[] = $nodeIDs;
        return self::$subtreeRemovable;
    }
}

class expRestContentPermissionTest extends PHPUnit\Framework\TestCase
{
    /** @var expRestContentPermissionTestObject */
    private $folder;
    /** @var expRestContentPermissionTestNode */
    private $parent;

    protected function setUp(): void
    {
        $this->folder = new expRestContentPermissionTestObject( 100 );
        $this->parent = new expRestContentPermissionTestNode( 60, $this->folder );
        $this->folder->classID = 1;
        $this->folder->nodes = array( $this->parent );
        expRestContentPermissionTestDouble::$nodes = array( 60 => $this->parent );
        expRestContentPermissionTestDouble::$objects = array( 100 => $this->folder );
        expRestContentPermissionTestDouble::$classes = array( 'article' => new expRestContentPermissionTestClass( 16 ),
                                                              'folder' => new expRestContentPermissionTestClass( 1 ) );
        expRestContentPermissionTestDouble::$subtreeRemovable = true;
        expRestContentPermissionTestDouble::$subtreeAsked = array();
    }

    private static function check( $action, array $params )
    {
        return expRestContentPermissionTestDouble::check( $action, $params );
    }

    private static function outcome( $refusal )
    {
        return $refusal === null ? 'allowed' : $refusal['status'] . ' ' . $refusal['reason'];
    }

    /** CP-01 */
    public function testCreate()
    {
        $this->folder->create = array( '16/eng-US' );
        $this->assertSame( 'allowed', self::outcome( self::check( 'create', array( 'parentNodeID' => '60', 'classIdentifier' => 'article', 'languageLocale' => 'eng-US' ) ) ) );
        $this->assertSame( array( 'create', 16, 1, 'eng-US' ), $this->folder->calls[0], 'class, parent class and language, as content/action asks' );
        $refusal = self::check( 'create', array( 'parentNodeID' => 60, 'classIdentifier' => 'article', 'languageLocale' => 'ger-DE' ) );
        $this->assertSame( '403 access_denied', self::outcome( $refusal ), 'another language' );
        $this->assertStringContainsString( 'content/create', $refusal['message'] );
        $this->assertSame( '403 access_denied', self::outcome( self::check( 'create', array( 'parentNodeID' => 60, 'classIdentifier' => 'folder', 'languageLocale' => 'eng-US' ) ) ), 'another class' );
        $this->assertSame( '403 access_denied', self::outcome( self::check( 'create', array( 'parentNodeID' => 60, 'classIdentifier' => 'article' ) ) ), 'any language is not eng-US' );
        $this->assertSame( '400 invalid_request', self::outcome( self::check( 'create', array( 'classIdentifier' => 'article' ) ) ) );
        $this->assertSame( '400 invalid_request', self::outcome( self::check( 'create', array( 'parentNodeID' => 60 ) ) ) );
        $this->assertSame( '404 not_found', self::outcome( self::check( 'create', array( 'parentNodeID' => 61, 'classIdentifier' => 'article' ) ) ) );
        $this->assertSame( '400 invalid_request', self::outcome( self::check( 'create', array( 'parentNodeID' => 60, 'classIdentifier' => 'nosuch' ) ) ) );
        $this->assertSame( '400 invalid_request', self::outcome( self::check( 'create', array( 'parentNodeID' => 60, 'classIdentifier' => 'article', 'languageLocale' => 'xxx-XX' ) ) ) );
        $orphan = new expRestContentPermissionTestNode( 62, null );
        expRestContentPermissionTestDouble::$nodes[62] = $orphan;
        $this->assertSame( '403 access_denied', self::outcome( self::check( 'create', array( 'parentNodeID' => 62, 'classIdentifier' => 'article' ) ) ), 'a parent without object' );
    }

    /** CP-02 */
    public function testRead()
    {
        $this->assertSame( 'allowed', self::outcome( self::check( 'read', array( 'nodeId' => 60 ) ) ) );
        $this->parent->read = false;
        $this->assertSame( '403 access_denied', self::outcome( self::check( 'read', array( 'nodeId' => 60 ) ) ) );
        $this->assertSame( 'allowed', self::outcome( self::check( 'read', array( 'objectId' => 100 ) ) ) );
        $this->folder->read = false;
        $this->assertSame( '403 access_denied', self::outcome( self::check( 'read', array( 'objectId' => 100 ) ) ) );
        $this->assertSame( '404 not_found', self::outcome( self::check( 'read', array( 'nodeId' => 99 ) ) ) );
        $this->assertSame( '404 not_found', self::outcome( self::check( 'read', array( 'objectId' => 99 ) ) ) );
        $this->assertSame( '400 invalid_request', self::outcome( self::check( 'read', array() ) ) );
    }

    /** CP-03 */
    public function testEdit()
    {
        $this->assertSame( 'allowed', self::outcome( self::check( 'edit', array( 'nodeId' => 60 ) ) ) );
        $this->folder->editLanguages = array( 'eng-US' );
        $this->assertSame( 'allowed', self::outcome( self::check( 'edit', array( 'nodeId' => 60, 'languageLocale' => 'eng-US' ) ) ) );
        $this->assertSame( '403 access_denied', self::outcome( self::check( 'edit', array( 'nodeId' => 60, 'languageLocale' => 'ger-DE' ) ) ) );
        $this->parent->edit = false;
        $this->assertSame( '403 access_denied', self::outcome( self::check( 'edit', array( 'nodeId' => 60 ) ) ) );
        $this->assertSame( '404 not_found', self::outcome( self::check( 'edit', array( 'nodeId' => 61 ) ) ) );
        $this->assertSame( '400 invalid_request', self::outcome( self::check( 'edit', array( 'objectId' => 100 ) ) ), 'edit needs a node' );
    }

    /** CP-04 */
    public function testRemoveNeedsEveryLocationAndTheSubtree()
    {
        $second = new expRestContentPermissionTestNode( 70, $this->folder );
        $this->folder->nodes = array( $this->parent, $second );
        $this->assertSame( 'allowed', self::outcome( self::check( 'remove', array( 'nodeId' => 60 ) ) ) );
        $this->assertSame( array( array( 60, 70 ) ), expRestContentPermissionTestDouble::$subtreeAsked, 'every location of the object' );
        $second->remove = false;
        $this->assertSame( '403 access_denied', self::outcome( self::check( 'remove', array( 'nodeId' => 60 ) ) ), 'another location may not be removed' );
        $second->remove = true;
        expRestContentPermissionTestDouble::$subtreeRemovable = false;
        $refusal = self::check( 'remove', array( 'nodeId' => 60 ) );
        $this->assertSame( '403 access_denied', self::outcome( $refusal ), 'something below may not be removed' );
        $this->assertStringContainsString( 'content/remove', $refusal['message'] );
        $this->assertSame( '404 not_found', self::outcome( self::check( 'remove', array( 'nodeId' => 61 ) ) ) );
    }

    /** CP-05 */
    public function testUnknownActionAndBadIds()
    {
        $this->assertSame( '403 access_denied', self::outcome( self::check( 'publish', array( 'nodeId' => 60 ) ) ) );
        $this->assertSame( '403 access_denied', self::outcome( self::check( '', array() ) ) );
        foreach ( array( '0', '-60', '60abc', '6.0', array( 60 ), ' ', '1e3' ) as $bad )
            $this->assertSame( '400 invalid_request', self::outcome( self::check( 'read', array( 'nodeId' => $bad ) ) ), var_export( $bad, true ) );
        $this->assertSame( 'allowed', self::outcome( self::check( 'read', array( 'nodeId' => ' 60 ' ) ) ) );
    }

    /** CP-06 */
    public function testForRequestAndTheApiKeyGuard()
    {
        $this->folder->create = array( '16/eng-US' );
        $request = new ezpRestRequest();
        $request->post = array( 'parentNodeID' => '60', 'classIdentifier' => 'article', 'languageLocale' => 'eng-US', 'title' => 'x' );
        $this->assertNull( expRestContentPermissionTestDouble::forRequest( 'create', $request ) );
        $request->post['languageLocale'] = 'ger-DE';
        $this->assertSame( '403 access_denied', self::outcome( expRestContentPermissionTestDouble::forRequest( 'create', $request ) ) );
        $request = new ezpRestRequest();
        $request->variables = array( 'nodeId' => '60' );
        $this->assertNull( expRestContentPermissionTestDouble::forRequest( 'remove', $request ) );
        $this->parent->remove = false;
        $this->assertSame( '403 access_denied', self::outcome( expRestContentPermissionTestDouble::forRequest( 'remove', $request ) ) );

        // the key guard: unknown guard names refuse; an empty request is left to the controller (400 there)
        $this->assertFalse( expApiKeyRest::guardAllows( 'publish', new ezpRestRequest() ) );
        $this->assertTrue( expApiKeyRest::guardAllows( 'create', new ezpRestRequest() ) );
    }

    /** CP-07 */
    public function testEditGoesThroughTheEditAccessFilter()
    {
        $asked = array();
        $answer = null;
        // The filter gets the ID of the current user: an anonymous stand-in, so no session or database is asked
        $hadUser = array_key_exists( 'eZUserGlobalInstance_', $GLOBALS );
        $previousUser = $hadUser ? $GLOBALS['eZUserGlobalInstance_'] : null;
        $GLOBALS['eZUserGlobalInstance_'] = new eZUser( array( 'contentobject_id' => eZUser::anonymousId(), 'login' => 'cp07', 'email' => 'cp07@example.invalid' ) );
        $id = ezpEvent::getInstance()->attach( 'content/edit/access', function ( $allowed, $object, $version, $userID, $language ) use ( &$asked, &$answer )
        {
            $asked[] = array( $allowed, $object, $version, $language );
            return $answer === null ? $allowed : $answer;
        } );
        try
        {
            // A listener that hands the answer back changes nothing, and gets the object and the language
            $this->folder->editLanguages = array( 'eng-US' );
            $this->assertSame( 'allowed', self::outcome( self::check( 'edit', array( 'nodeId' => 60, 'languageLocale' => 'eng-US' ) ) ) );
            $this->assertSame( array( true, $this->folder, null, 'eng-US' ), $asked[0] );
            $this->assertSame( '403 access_denied', self::outcome( self::check( 'edit', array( 'nodeId' => 60, 'languageLocale' => 'ger-DE' ) ) ) );
            $this->assertFalse( $asked[1][0], 'the kernel refused the other language' );

            // A listener keeps someone out where the kernel allows
            $answer = false;
            $this->assertSame( '403 access_denied', self::outcome( self::check( 'edit', array( 'nodeId' => 60 ) ) ) );
            // ... and only true allows: a truthy answer that is not true refuses
            $answer = 1;
            $this->assertSame( '403 access_denied', self::outcome( self::check( 'edit', array( 'nodeId' => 60 ) ) ) );

            // A listener lets someone in where the kernel refuses
            $answer = true;
            $this->parent->edit = false;
            $this->assertSame( 'allowed', self::outcome( self::check( 'edit', array( 'nodeId' => 60 ) ) ) );
            $this->assertFalse( $asked[count( $asked ) - 1][0], 'the listener saw the kernel refuse' );
        }
        finally
        {
            ezpEvent::getInstance()->detach( 'content/edit/access', $id );
            if ( $hadUser )
                $GLOBALS['eZUserGlobalInstance_'] = $previousUser;
            else
                unset( $GLOBALS['eZUserGlobalInstance_'] );
        }
    }
}
