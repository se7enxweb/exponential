<?php
/**
 * The content checks against a real installation (doc/features/6.0/content-checks.md): which objects are
 * unreferenced, the trash action, the broken relations, and what a user with less access sees.
 *
 *  CS-01  unreferenced: an object no other object refers to is listed; one referred to from a published version
 *         is not; one referred to only from a draft is not; one referred to only from an object in the trash, only
 *         from an old version, or only by itself is listed; an object with sub-items only with Containers
 *  CS-02  the class filter, the count and the paging agree with the list
 *  CS-03  a reader of one subtree sees its objects only and may move none of them; the start node must be readable
 *  CS-04  the trash plan: allowed, referenced, has sub-items (also only hidden ones), not found; the move needs the confirmation token of
 *         exactly that plan and goes through the kernel's removal (the object is in the trash, restorable); a second
 *         post of the same confirmation does nothing
 *  CS-05  broken relations: targets deleted (a link in rich text to a purged object), in the trash (a relation, a
 *         node embed), hidden, never published; a target that is fine is not reported; the kinds, the reference
 *         types and the excluded subtrees filter; the edit link only for a user who may edit the source
 *  CS-06  the scan pages by object id and ends after the last source
 *  CS-07  what a user may not read is only counted: no name of an unreadable object below the node, of an
 *         unreadable source or of a hidden or trashed target
 *  CS-08  who may open which view: the module functions, ANDed for the trash view
 *
 * It WRITES to the database (content, users, roles), so it runs ONLY in a throwaway installation named by
 * EXP_CONTENTCHECK_SANDBOX (the current directory), with an SQLite database inside it or a throwaway database
 * named by EXP_CONTENTCHECK_SANDBOX_DATABASE as well, on the siteaccess EXP_CONTENTCHECK_SANDBOX_SITEACCESS
 * (default admin), and is skipped everywhere else, before the kernel is started:
 *     EXP_CONTENTCHECK_SANDBOX=$PWD EXP_CONTENTCHECK_SANDBOX_DATABASE=<db> \
 *         php vendor/bin/phpunit tests/tests/kernel/contentcheck/expContentCheckSandboxTest.php
 * Each run makes its own content below a new folder; nothing that exists is changed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expContentCheckSandboxTest extends PHPUnit\Framework\TestCase
{
    /** @var string|null why the tests cannot run here */
    protected static $skip = null;

    /** @var array the objects of this run, by short name: eZContentObject */
    protected static $o = array();

    /** @var array the users of this run: login => array( id, password ) */
    protected static $users = array();

    /** @var string */
    protected static $run = '';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( self::$o || self::$skip !== null )
            return;
        $sandbox = (string)getenv( 'EXP_CONTENTCHECK_SANDBOX' );
        if ( $sandbox === '' || realpath( $sandbox ) !== getcwd() )
        {
            self::$skip = 'writes to the database: only in a throwaway installation named by EXP_CONTENTCHECK_SANDBOX';
            return;
        }
        try
        {
            if ( !eZScript::instance()->isInitialized() )
            {
                $siteAccess = (string)getenv( 'EXP_CONTENTCHECK_SANDBOX_SITEACCESS' );
                $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
                $script->startup();
                $script->setUseSiteAccess( $siteAccess !== '' ? $siteAccess : 'admin' );
                $script->initialize();
                eZExecution::setCleanExit();
            }
            $why = self::refusal();
            if ( $why !== '' )
            {
                self::$skip = $why;
                return;
            }
            self::seed();
        }
        catch ( Throwable $e )
        {
            self::$skip = 'kernel not available: ' . $e->getMessage();
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        if ( self::$skip !== null )
            $this->markTestSkipped( self::$skip );
        expContentCheck::setTestSecret( 'cc-test-secret' );
        self::signIn( 'admin' );
    }

    protected function tearDown(): void
    {
        expContentCheck::setTestSecret( null );
        if ( self::$skip === null )
            self::signIn( 'admin' );
        parent::tearDown();
    }

    /** '' when the database is a throwaway one, else why not */
    protected static function refusal()
    {
        $db = eZDB::instance();
        if ( !$db || !$db->isConnected() )
            return 'no database connection';
        if ( !expContentCheck::isSupportedDatabase() )
            return 'the checks do not run on ' . $db->databaseName();
        $ini = eZINI::instance();
        if ( $db->databaseName() === 'sqlite' )
        {
            $file = realpath( eZSQLite3DB::filePath( $ini->variable( 'DatabaseSettings', 'Database' ) ) );
            return $file !== false && strpos( $file, getcwd() . '/' ) === 0 ? '' : 'the database file is not inside the throwaway installation';
        }
        $named = (string)getenv( 'EXP_CONTENTCHECK_SANDBOX_DATABASE' );
        $configured = array( (string)$ini->variable( 'DatabaseSettings', 'Database' ), (string)$ini->variable( 'DatabaseSettings', 'User' ) );
        return $named !== '' && in_array( $named, $configured, true ) ? '' : 'EXP_CONTENTCHECK_SANDBOX_DATABASE does not name the database as a throwaway one';
    }

    protected static function signIn( $login )
    {
        $user = eZUser::fetchByName( $login );
        if ( !$user instanceof eZUser )
            throw new RuntimeException( "no user $login" );
        $user->loginCurrent();
        eZContentObject::clearCache();
        if ( method_exists( 'eZUser', 'resetAccessUsers' ) )
            eZUser::resetAccessUsers();
        if ( class_exists( 'ezpContentLimitation' ) && method_exists( 'ezpContentLimitation', 'resetCache' ) )
            ezpContentLimitation::resetCache();
        unset( $GLOBALS['ezpolicylimitation_list'] );
        return $user;
    }

    /** the first attribute identifier of a datatype in a class */
    protected static function attribute( $classIdentifier, $datatype )
    {
        $class = eZContentClass::fetchByIdentifier( $classIdentifier );
        foreach ( $class ? $class->fetchAttributes() : array() as $attribute )
        {
            if ( $attribute->attribute( 'data_type_string' ) === $datatype )
                return $attribute;
        }
        return null;
    }

    protected static function make( $key, $parentNodeID, $class = null )
    {
        $class = $class ?: ( eZContentClass::fetchByIdentifier( 'article' ) ? 'article' : 'folder' );
        $name = self::attribute( $class, 'ezstring' );
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => $parentNodeID, 'class_identifier' => $class,
                                                                     'remote_id' => 'cc-' . self::$run . '-' . $key,
                                                                     'attributes' => $name ? array( $name->attribute( 'identifier' ) => 'CC ' . $key ) : array() ) );
        if ( !$object instanceof eZContentObject )
            throw new RuntimeException( "could not create $key" );
        eZContentObject::clearCache();
        return self::$o[$key] = eZContentObject::fetch( $object->attribute( 'id' ) );
    }

    protected static function node( $key )
    {
        return (int)self::$o[$key]->attribute( 'main_node_id' );
    }

    protected static function id( $key )
    {
        return (int)self::$o[$key]->attribute( 'id' );
    }

    protected static function relate( $from, $to, $version = false, $type = eZContentObject::RELATION_COMMON )
    {
        $object = self::$o[$from];
        $object->addContentObjectRelation( self::id( $to ), $version === false ? (int)$object->attribute( 'current_version' ) : $version, 0, $type );
    }

    protected static function user( $login, array $policies )
    {
        // in a group of its own below the users' top node, so that no role of the default group reaches it
        $userPlacement = (int)eZINI::instance()->variable( 'UserSettings', 'DefaultUserPlacement' );
        $usersRoot = (int)eZContentObjectTreeNode::fetch( $userPlacement )->attribute( 'parent_node_id' );
        $groupClass = eZContentClass::fetchByIdentifier( 'user_group' ) ? 'user_group' : 'folder';
        $group = self::make( 'group-' . $login, $usersRoot, $groupClass );
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => (int)$group->attribute( 'main_node_id' ), 'class_identifier' => 'user',
                                                                     'attributes' => array( 'first_name' => 'CC', 'last_name' => $login ) ) );
        $userID = (int)$object->attribute( 'id' );
        $user = eZUser::create( $userID );
        $password = 'Cc-' . bin2hex( random_bytes( 8 ) ) . '!';
        $user->setInformation( $userID, $login, $login . '@example.invalid', $password, $password );
        $user->store();
        $role = eZRole::create( $login . ' role' );
        $role->store();
        foreach ( $policies as $p )
            $role->appendPolicy( $p[0], $p[1], isset( $p[2] ) ? $p[2] : array() );
        $role->assignToUser( $userID );
        eZUser::purgeUserCacheByUserId( $userID );
        eZRole::expireCache();
        self::$users[$login] = array( 'id' => $userID );
    }

    /**
     *   CC root (folder)
     *     unref          nothing refers to it
     *     target         referred to by source (published relation)
     *     source         refers to target
     *     drafttarget    referred to only from a draft of draftsource
     *     draftsource
     *     trashtarget    referred to only from trashsource, which is in the trash
     *     trashsource    (trashed)
     *     oldtarget      referred to only from version 1 of oldsource (now archived)
     *     oldsource
     *     self           refers to itself
     *     box (folder)   has a sub-item
     *       inbox        nothing refers to it
     *     hidbox (folder) has a hidden sub-item only
     *       hidchild
     *   CC broken (folder)
     *     page           refers to: gone (purged, link in its rich text), binned (relation, trashed), hidden (relation,
     *                    hidden), nodebinned (embed of a node in the trash), fine (relation, visible), draft (never
     *                    published, relation)
     *     gone, binned, hidden, nodebinned, fine
     */
    protected static function seed()
    {
        self::$run = bin2hex( random_bytes( 4 ) );
        self::signIn( 'admin' );
        $root = (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' );
        self::make( 'root', $root, 'folder' );
        $r = self::node( 'root' );
        foreach ( array( 'unref', 'target', 'source', 'drafttarget', 'draftsource', 'trashtarget', 'trashsource', 'oldtarget', 'oldsource', 'self' ) as $key )
            self::make( $key, $r );
        self::make( 'box', $r, 'folder' );
        self::make( 'inbox', self::node( 'box' ) );
        self::make( 'boxfolder', self::node( 'box' ), 'folder' );
        self::make( 'hidbox', $r, 'folder' );
        self::make( 'hidchild', self::node( 'hidbox' ) );
        eZContentObjectTreeNode::hideSubTree( eZContentObjectTreeNode::fetch( self::node( 'hidchild' ) ) );

        self::relate( 'source', 'target' );
        $draft = self::$o['draftsource']->createNewVersion();
        self::$o['draftsource']->addContentObjectRelation( self::id( 'drafttarget' ), (int)$draft->attribute( 'version' ) );
        self::relate( 'trashsource', 'trashtarget' );
        eZContentObjectTreeNode::removeSubtrees( array( self::node( 'trashsource' ) ), true );
        self::relate( 'oldsource', 'oldtarget' );
        eZContentFunctions::updateAndPublishObject( self::$o['oldsource'], array( 'attributes' => array() ) );
        eZContentObject::clearCache();
        self::$o['oldsource'] = eZContentObject::fetch( self::id( 'oldsource' ) );
        self::$o['oldsource']->removeContentObjectRelation( self::id( 'oldtarget' ), (int)self::$o['oldsource']->attribute( 'current_version' ) );
        self::relate( 'self', 'self' );

        self::make( 'broken', $root, 'folder' );
        $b = self::node( 'broken' );
        foreach ( array( 'page', 'gone', 'binned', 'hidden', 'nodebinned', 'fine' ) as $key )
            self::make( $key, $b );
        self::make( 'brokenfolder', $b, 'folder' );
        self::make( 'closed', $root, 'folder' );
        self::make( 'closedhidden', self::node( 'closed' ) );
        self::relate( 'page', 'closedhidden' );
        self::relate( 'brokenfolder', 'binned' );
        eZContentObjectTreeNode::hideSubTree( eZContentObjectTreeNode::fetch( self::node( 'closedhidden' ) ) );
        foreach ( array( 'binned', 'hidden', 'fine' ) as $key )
            self::relate( 'page', $key );
        $class = eZContentClass::fetchByIdentifier( eZContentClass::fetchByIdentifier( 'article' ) ? 'article' : 'folder' );
        $draftObject = $class->instantiate( false, 0, false, false, eZContentObjectVersion::STATUS_DRAFT );
        self::$o['draft'] = $draftObject;
        self::relate( 'page', 'draft' );
        $nodebinnedNode = self::node( 'nodebinned' );
        $xml = self::attribute( $class->attribute( 'identifier' ), 'ezxmltext' );
        if ( $xml )
        {
            $text = '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/"><paragraph>'
                  . '<link object_id="' . self::id( 'gone' ) . '">gone</link> <embed view="embed" size="medium" node_id="' . $nodebinnedNode . '"/>'
                  . ' <link object_id="' . self::id( 'fine' ) . '">fine</link></paragraph></section>';
            eZDB::instance()->query( "UPDATE ezcontentobject_attribute SET data_text = '" . eZDB::instance()->escapeString( $text ) . "'" .
                                     ' WHERE contentobject_id = ' . self::id( 'page' ) . ' AND version = ' . (int)self::$o['page']->attribute( 'current_version' ) .
                                     ' AND contentclassattribute_id = ' . (int)$xml->attribute( 'id' ) );
        }
        eZContentObjectTreeNode::removeSubtrees( array( self::node( 'binned' ), $nodebinnedNode ), true );
        eZContentObjectTreeNode::hideSubTree( eZContentObjectTreeNode::fetch( self::node( 'hidden' ) ) );
        self::$o['gone']->purge();
        eZContentObject::clearCache();

        self::user( 'cc_reader_' . self::$run, array( array( 'user', 'login' ), array( 'contentcheck', 'unreferenced' ),
                                                      array( 'contentcheck', 'trash' ), array( 'contentcheck', 'brokenrelations' ),
                                                      array( 'content', 'read', array( 'Subtree' => array( eZContentObjectTreeNode::fetch( self::node( 'box' ) )->attribute( 'path_string' ) ) ) ) ) );
        $article = eZContentClass::fetchByIdentifier( 'article' ) ? (int)eZContentClass::fetchByIdentifier( 'article' )->attribute( 'id' ) : 0;
        $pathOf = function ( $key ) { return eZContentObjectTreeNode::fetch( self::node( $key ) )->attribute( 'path_string' ); };
        self::user( 'cc_classreader_' . self::$run, array( array( 'user', 'login' ), array( 'contentcheck', 'unreferenced' ),
                                                           array( 'content', 'read', array( 'Node' => array( self::node( 'box' ) ) ) ),
                                                           array( 'content', 'read', array( 'Subtree' => array( $pathOf( 'box' ) ), 'Class' => array( $article ) ) ) ) );
        self::user( 'cc_brokenreader_' . self::$run, array( array( 'user', 'login' ), array( 'contentcheck', 'brokenrelations' ),
                                                            array( 'content', 'read', array( 'Node' => array( self::node( 'broken' ) ) ) ),
                                                            array( 'content', 'read', array( 'Subtree' => array( $pathOf( 'broken' ) ), 'Class' => array( $article ) ) ) ) );
        self::user( 'cc_noaccess_' . self::$run, array( array( 'user', 'login' ), array( 'content', 'read' ) ) );
        self::signIn( 'admin' );
    }

    protected function unreferenced( array $input )
    {
        eZContentObject::clearCache();
        $r = expContentCheckUnreferenced::fetch( expContentCheckUnreferenced::parameters( $input + array( 'Limit' => 100 ), 2 ) );
        $ids = array();
        foreach ( $r['rows'] as $row )
            $ids[] = $row['object_id'];
        return array( $r, $ids );
    }

    /** CS-01 */
    public function testWhatIsUnreferenced()
    {
        list( $r, $ids ) = $this->unreferenced( array( 'NodeID' => self::node( 'root' ) ) );
        $this->assertSame( '', $r['error'] );
        foreach ( array( 'unref', 'source', 'draftsource', 'trashtarget', 'oldtarget', 'oldsource', 'self', 'inbox' ) as $key )
            $this->assertContains( self::id( $key ), $ids, $key );
        foreach ( array( 'target' => 'a published relation', 'drafttarget' => 'a relation of a current draft',
                         'box' => 'it has a sub-item', 'trashsource' => 'it is in the trash', 'root' => 'the start node itself' ) as $key => $why )
            $this->assertNotContains( self::id( $key ), $ids, "$key: $why" );
        list( , $withBoxes ) = $this->unreferenced( array( 'NodeID' => self::node( 'root' ), 'Containers' => '1' ) );
        $this->assertContains( self::id( 'box' ), $withBoxes );
    }

    /** CS-02 */
    public function testFilterCountAndPaging()
    {
        $folder = (int)eZContentClass::fetchByIdentifier( 'folder' )->attribute( 'id' );
        list( $r, $ids ) = $this->unreferenced( array( 'NodeID' => self::node( 'root' ), 'ClassID' => array( $folder ), 'Containers' => '1' ) );
        $this->assertSame( array( self::id( 'box' ), self::id( 'boxfolder' ), self::id( 'hidbox' ) ), $ids );
        $this->assertSame( 3, $r['count'] );
        $this->assertSame( 0, $r['hidden'], 'an unlimited reader sees everything' );

        list( $all, $allIDs ) = $this->unreferenced( array( 'NodeID' => self::node( 'root' ), 'Sort' => 'id', 'Order' => 'asc' ) );
        $this->assertSame( count( $allIDs ), $all['count'] );
        $sorted = $allIDs;
        sort( $sorted );
        $this->assertSame( $sorted, $allIDs );
        $page = expContentCheckUnreferenced::fetch( expContentCheckUnreferenced::parameters( array( 'NodeID' => self::node( 'root' ), 'Sort' => 'id', 'Order' => 'asc', 'Offset' => 2, 'Limit' => 3 ), 2 ) );
        $this->assertSame( array_slice( $allIDs, 2, 3 ), array_column( $page['rows'], 'object_id' ) );
        $this->assertSame( $all['count'], $page['count'] );
        foreach ( $all['rows'] as $row )
            $this->assertTrue( $row['can_trash'], $row['name'] );
        $this->assertSame( 0, $all['hidden'] );
    }

    /** CS-03 */
    public function testAReaderOfOneSubtree()
    {
        self::signIn( 'cc_reader_' . self::$run );
        $this->assertTrue( expContentCheck::hasPolicy( 'contentcheck', 'unreferenced' ) );
        $this->assertSame( 'not_found', expContentCheckUnreferenced::fetch( expContentCheckUnreferenced::parameters( array( 'NodeID' => self::node( 'root' ) ), 2 ) )['error'] );
        list( $r, $ids ) = $this->unreferenced( array( 'NodeID' => self::node( 'box' ) ) );
        $this->assertSame( array( self::id( 'boxfolder' ), self::id( 'inbox' ) ), $ids );
        $this->assertFalse( $r['rows'][1]['can_trash'], 'no content/remove' );
        $plan = expContentCheckTrash::plan( array( self::id( 'inbox' ), self::id( 'unref' ) ) );
        $this->assertSame( array(), $plan['allowed'] );
        $this->assertSame( array( 'not_removable', 'not_found' ), array_column( $plan['items'], 'reason' ) );
        $this->assertSame( '', $plan['items'][1]['name'], 'nothing is told about an object the user may not read' );
        $broken = expContentCheckBrokenRelations::scan( expContentCheckBrokenRelations::parameters( array( 'NodeID' => self::node( 'broken' ) ) ) );
        $this->assertSame( 'not_found', $broken['error'] );
    }

    /** CS-07 */
    public function testWhatAReaderMayNotReadIsOnlyCounted()
    {
        self::signIn( 'cc_classreader_' . self::$run );
        list( $r, $ids ) = $this->unreferenced( array( 'NodeID' => self::node( 'box' ), 'Containers' => '1' ) );
        $this->assertSame( array( self::id( 'inbox' ) ), $ids, 'the folder below box is not readable for this user' );
        $this->assertSame( 1, $r['hidden'] );
        $this->assertStringNotContainsString( 'CC boxfolder', serialize( $r['rows'] ) );

        self::signIn( 'cc_brokenreader_' . self::$run );
        eZContentObject::clearCache();
        $scan = expContentCheckBrokenRelations::scan( expContentCheckBrokenRelations::parameters( array( 'NodeID' => self::node( 'broken' ), 'Limit' => 100 ) ) );
        $this->assertSame( '', $scan['error'] );
        $sources = array_unique( array_column( $scan['findings'], 'source_id' ) );
        $this->assertSame( array( self::id( 'page' ) ), array_values( $sources ), 'the folder source is not readable: not checked' );
        $this->assertSame( 1, $scan['hidden'], 'brokenfolder is counted, not shown' );
        $closed = null;
        foreach ( $scan['findings'] as $finding )
        {
            if ( $finding['target_id'] === self::id( 'closedhidden' ) )
                $closed = $finding;
        }
        $this->assertNotNull( $closed );
        $this->assertSame( 'hidden', $closed['kind'] );
        $this->assertSame( '', $closed['target_name'], 'a hidden target outside the reader\'s access: id and state only' );
        $this->assertStringNotContainsString( 'CC closedhidden', serialize( $scan ) );
        $this->assertStringNotContainsString( 'CC brokenfolder', serialize( $scan['findings'] ) );
    }

    /** CS-08 */
    public function testWhoMayOpenTheViews()
    {
        $module = eZModule::exists( 'contentcheck' );
        $this->assertInstanceOf( 'eZModule', $module );
        $params = array();
        $cases = array( 'cc_noaccess_' => array( 'unreferenced' => false, 'brokenrelations' => false, 'trash' => false ),
                        'cc_classreader_' => array( 'unreferenced' => true, 'brokenrelations' => false, 'trash' => false ),
                        'cc_brokenreader_' => array( 'unreferenced' => false, 'brokenrelations' => true, 'trash' => false ),
                        'cc_reader_' => array( 'unreferenced' => true, 'brokenrelations' => true, 'trash' => true ) );
        foreach ( $cases as $login => $views )
        {
            $user = self::signIn( $login . self::$run );
            foreach ( $views as $view => $allowed )
            {
                $access = $user->hasAccessToView( $module, $view, $params );
                $this->assertSame( $allowed, (bool)$access, "$login $view" );
            }
        }
    }

    /** CS-04 */
    public function testMovingToTheTrash()
    {
        $missing = 2147483000;
        $plan = expContentCheckTrash::plan( array( self::id( 'oldtarget' ), self::id( 'target' ), self::id( 'box' ), $missing, self::id( 'hidbox' ) ) );
        $this->assertSame( array( '', 'referenced', 'has_children', 'not_found', 'has_children' ), array_column( $plan['items'], 'reason' ),
                           'a hidden sub-item counts as well' );
        $this->assertSame( array( self::id( 'oldtarget' ) ), $plan['allowed'] );

        $wrong = expContentCheckTrash::execute( array( self::id( 'oldtarget' ) ), expContentCheck::confirmToken( 'trash|1,2' ) );
        $this->assertFalse( $wrong['done'] );
        $this->assertSame( 'changed', $wrong['reason'] );
        $this->assertSame( eZContentObject::STATUS_PUBLISHED, (int)eZContentObject::fetch( self::id( 'oldtarget' ), false )['status'] );

        $done = expContentCheckTrash::execute( array( self::id( 'oldtarget' ), self::id( 'target' ) ), expContentCheck::confirmToken( $plan['fingerprint'] ) );
        $this->assertTrue( $done['done'] );
        $this->assertSame( array( self::id( 'oldtarget' ) ), $done['moved'] );
        eZContentObject::clearCache();
        $this->assertSame( eZContentObject::STATUS_ARCHIVED, (int)eZContentObject::fetch( self::id( 'oldtarget' ) )->attribute( 'status' ) );
        $this->assertSame( eZContentObject::STATUS_PUBLISHED, (int)eZContentObject::fetch( self::id( 'target' ) )->attribute( 'status' ), 'referenced: left alone' );
        $this->assertSame( 1, (int)eZContentObjectTrashNode::trashListCount( array( 'ContentObjectIDList' => array( self::id( 'oldtarget' ) ), 'Limitation' => array() ) ) );

        $again = expContentCheckTrash::execute( array( self::id( 'oldtarget' ) ), expContentCheck::confirmToken( $plan['fingerprint'] ) );
        $this->assertFalse( $again['done'], 'a second post of the same confirmation does nothing' );
    }

    protected function findings( array $input )
    {
        eZContentObject::clearCache();
        $r = expContentCheckBrokenRelations::scan( expContentCheckBrokenRelations::parameters( $input + array( 'NodeID' => self::node( 'broken' ), 'Limit' => 100 ) ) );
        $found = array();
        foreach ( $r['findings'] as $f )
        {
            $this->assertContains( $f['source_id'], array( self::id( 'page' ), self::id( 'brokenfolder' ) ) );
            if ( $f['source_id'] === self::id( 'page' ) )
                $found[$f['target_type'] . ':' . $f['target_id']] = $f['kind'];
        }
        ksort( $found );
        return array( $r, $found );
    }

    /** CS-05 */
    public function testBrokenRelations()
    {
        $hasXml = (bool)self::attribute( eZContentClass::fetchByIdentifier( 'article' ) ? 'article' : 'folder', 'ezxmltext' );
        list( $r, $found ) = $this->findings( array() );
        $expected = array( 'object:' . self::id( 'binned' ) => 'trash', 'object:' . self::id( 'hidden' ) => 'hidden',
                           'object:' . self::id( 'closedhidden' ) => 'hidden',
                           'object:' . self::id( 'draft' ) => 'unpublished' );
        if ( $hasXml )
        {
            $expected['object:' . self::id( 'gone' )] = 'deleted';
            $expected['node:' . self::node( 'nodebinned' )] = 'trash';
        }
        ksort( $expected );
        $this->assertSame( $expected, $found );
        $this->assertTrue( $r['done'] );
        foreach ( $r['findings'] as $f )
        {
            $this->assertTrue( $f['can_edit'] );
            if ( in_array( $f['kind'], array( 'deleted', 'trash', 'hidden' ), true ) )
                $this->assertSame( '', $f['target_name'], 'a deleted, trashed or hidden target: id and state only' );
        }

        list( , $onlyDeleted ) = $this->findings( array( 'Kind' => array( 'deleted' ) ) );
        $this->assertSame( $hasXml ? array( 'object:' . self::id( 'gone' ) => 'deleted' ) : array(), $onlyDeleted );
        list( , $onlyObject ) = $this->findings( array( 'Relation' => array( 'object' ) ) );
        $this->assertArrayNotHasKey( 'object:' . self::id( 'gone' ), $onlyObject );
        $this->assertArrayHasKey( 'object:' . self::id( 'binned' ), $onlyObject );
        list( $excluded, $none ) = $this->findings( array( 'Exclude' => (string)self::node( 'broken' ) ) );
        $this->assertSame( array(), $none );
        $this->assertSame( 0, $excluded['checked'] );
    }

    /** CS-06 */
    public function testScanPaging()
    {
        $first = expContentCheckBrokenRelations::scan( expContentCheckBrokenRelations::parameters( array( 'NodeID' => self::node( 'broken' ), 'Limit' => 1 ) ) );
        $this->assertGreaterThanOrEqual( 1, count( $first['findings'] ), 'a page ends after a whole source' );
        $this->assertSame( self::id( 'page' ), $first['last'] );
        $next = expContentCheckBrokenRelations::scan( expContentCheckBrokenRelations::parameters( array( 'NodeID' => self::node( 'broken' ), 'Limit' => 1,
                                                                                                          'After' => $first['last'] ) ) );
        $this->assertSame( array( self::id( 'brokenfolder' ) ), array_values( array_unique( array_column( $next['findings'], 'source_id' ) ) ) );
        $last = expContentCheckBrokenRelations::scan( expContentCheckBrokenRelations::parameters( array( 'NodeID' => self::node( 'broken' ), 'Limit' => 1,
                                                                                                          'After' => $next['last'] ) ) );
        $this->assertSame( array(), $last['findings'] );
        $this->assertTrue( $last['done'] );
    }
}
