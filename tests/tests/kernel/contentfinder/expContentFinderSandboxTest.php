<?php
/**
 * The content finder against a real installation (doc/features/6.0/content-finder.md): lookups, the rights per
 * result with users of different roles, the actions and their refusals, and the audit records.
 *
 *  CS-01  every kind of id, the remote id and the URL alias find the object; the result shows its versions,
 *         locations and metadata
 *  CS-02  an image's file path (exact, as a full URL, and below a prefix) finds the attribute, the object and the
 *         node; so do the attribute id and a binary file's storage path
 *  CS-03  a node in the trash is found by its node id, with the trash entry, its original path and who trashed it
 *  CS-04  rights: a reader of one subtree finds what is in it and nothing outside; a user without content/versionread
 *         sees the published version only; a version id of a version they may not read is not found
 *  CS-05  "not found" and "no access" give the same page, byte for byte
 *  CS-06  wildcards and quotes in a text are literal: no LIKE wildcard matches, no SQL error, nothing written
 *  CS-07  the action view refuses another method than POST, a missing or wrong token, a user without
 *         contentfinder/delete, an unknown action; a permanent action needs the explicit confirmation
 *  CS-08  plans: refused without the content policies; allowed with them, with the nodes and children counted
 *  CS-09  move to trash, delete permanently, purge, remove a version: done through the kernel's operations, refused
 *         when the confirmation token does not fit the plan; each recorded as content.finder.action with the kernel's
 *         own events as its children
 *  CS-10  each search is recorded as content.finder.search (user, type, value, results)
 *  CS-11  the finder answers only where the settings let it (SiteAccessList[], Enabled)
 *  CS-12  remote ids and file paths match in their case, exactly and as a prefix, on every engine
 *  CS-13  the last part of a URL alias (or a shorter tail of it) finds the node, with the full path; an old address
 *         is listed as history when nothing current matches; rights per result
 *  CS-14  a part of a URL alias (at least MinimumPartLength characters, words in order, any separator, any case)
 *         finds the node; wildcards and quotes are text; the result cap; PartialAliasSearch=disabled; rights
 *
 * It runs on SQLite and, in a throwaway database named by EXP_CONTENTFINDER_SANDBOX_DATABASE as well, on MySQL or
 * MariaDB, PostgreSQL, Oracle and MongoDB (doc/features/6.0/content-finder.md, "Databases").
 *
 * It WRITES to the database (content, users, roles; fixtures/expcontentfindertestfixtures.php), so it runs ONLY
 * in a throwaway copy of an installation with an SQLite database inside it, named by EXP_CONTENTFINDER_SANDBOX,
 * on its admin siteaccess, and is skipped everywhere else, before the kernel is started:
 *     cd /path/to/copy && EXP_CONTENTFINDER_SANDBOX=$PWD php vendor/bin/phpunit tests/tests/kernel/contentfinder/expContentFinderSandboxTest.php
 * The objects the actions remove are made by the test itself. Audit records go to var/tmp/audit-tests/.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/classes/audit/fixtures/expaudittestfixtures.php';
require_once __DIR__ . '/fixtures/expcontentfindertestfixtures.php';

use Exponential\View\Kernel\Contentfinder\Action;
use Exponential\View\Kernel\Contentfinder\Search;

class expContentFinderSandboxTest extends PHPUnit\Framework\TestCase
{
    /** @var string|null why the tests cannot run here */
    protected static $skip = null;

    /** @var array the seed file */
    protected static $seed = array();

    /** @var string|null */
    protected $auditDir = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( self::$seed || self::$skip !== null )
            return;
        // decided before the kernel is started: without the variable nothing connects to any database
        $sandbox = (string)getenv( 'EXP_CONTENTFINDER_SANDBOX' );
        if ( $sandbox === '' || realpath( $sandbox ) !== getcwd() )
        {
            self::$skip = 'writes to the database: only in a throwaway copy of an installation named by EXP_CONTENTFINDER_SANDBOX';
            return;
        }
        try
        {
            if ( !eZScript::instance()->isInitialized() )
            {
                $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
                $script->startup();
                $script->setUseSiteAccess( 'admin' );
                $script->initialize();
                eZExecution::setCleanExit();
            }
            $why = expContentFinderTestFixtures::refusal();
            if ( $why !== '' )
            {
                self::$skip = $why;
                return;
            }
            self::$seed = expContentFinderTestFixtures::seed( getcwd() . '/var/tmp/cf-files' );
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
        expContentFinderAction::setTestSecret( 'cs-test-secret' );
        $this->signIn( 'admin' );
    }

    protected function tearDown(): void
    {
        expContentFinderAction::setTestSecret( null );
        if ( $this->auditDir !== null )
        {
            expAuditHook::reset();
            expAuditTestFixtures::tearDown();
            $this->auditDir = null;
        }
        ezpINIHelper::restoreINISettings();
        if ( self::$skip === null )
            $this->signIn( 'admin' );
        parent::tearDown();
    }

    /** Signs in as a user; the permission caches of the objects are dropped */
    protected function signIn( $login )
    {
        $user = eZUser::fetchByName( $login );
        $this->assertInstanceOf( 'eZUser', $user, $login );
        $user->loginCurrent();
        eZContentObject::clearCache();
        if ( method_exists( 'eZUser', 'resetAccessUsers' ) )
            eZUser::resetAccessUsers();
        if ( class_exists( 'ezpContentLimitation' ) && method_exists( 'ezpContentLimitation', 'resetCache' ) )
            ezpContentLimitation::resetCache();
        return $user;
    }

    protected function search( $text, $type = 'auto' )
    {
        eZContentObject::clearCache();
        $finder = new expContentFinder();
        return $finder->search( $text, $type );
    }

    /** @return int[] the object ids of a search */
    protected function ids( $text, $type = 'auto' )
    {
        return array_column( $this->search( $text, $type )['results'], 'object_id' );
    }

    protected function resultFor( $text, $objectID, $type = 'auto' )
    {
        foreach ( $this->search( $text, $type )['results'] as $result )
            if ( $result['object_id'] === (int)$objectID )
                return $result;
        $this->fail( "object $objectID not found by $text" );
    }

    /** An article made for one test, under CF Open, by cf_editor (the owner of all test content) */
    protected function article( $name )
    {
        $this->signIn( 'cf_editor' );
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => self::$seed['nodes']['open'], 'class_identifier' => $this->articleClass(),
                                                                     'remote_id' => 'cf-test-' . bin2hex( random_bytes( 6 ) ),
                                                                     'attributes' => $this->nameAttributes( $name ) ) );
        $this->assertInstanceOf( 'eZContentObject', $object );
        eZContentObject::clearCache();
        return eZContentObject::fetch( $object->attribute( 'id' ) );
    }

    protected function articleClass()
    {
        return eZContentClass::fetchByIdentifier( 'article' ) ? 'article' : 'folder';
    }

    /** The attributes that give an object of articleClass() its name */
    protected function nameAttributes( $name )
    {
        foreach ( eZContentClass::fetchByIdentifier( $this->articleClass() )->fetchAttributes() as $attribute )
            if ( $attribute->attribute( 'data_type_string' ) === 'ezstring' )
                return array( $attribute->attribute( 'identifier' ) => $name );
        return array();
    }

    /** The audit to a throwaway directory, with the real actor (no fixed context) */
    protected function audit()
    {
        $this->auditDir = expAuditTestFixtures::setUp( $this->name() );
        expAuditConfig::setOverride( array( 'logDir' => $this->auditDir . 'log', 'keyDir' => $this->auditDir . 'keys', 'handlers' => false ) );
        expAuditTaxonomy::reset();
        expAudit::resetAll();
        return $this->auditDir;
    }

    protected function auditRecords( $channel )
    {
        expAudit::flush();
        return expAuditTestFixtures::events( $this->auditDir, $channel );
    }

    // ------------------------------------------------------------------ CS-01 .. CS-03

    /** CS-01 */
    public function testEveryKindOfIDFindsTheObject()
    {
        $id = self::$seed['objects']['article'];
        $node = self::$seed['nodes']['article'];
        $draft = self::$seed['article_versions'][3]['id'];
        foreach ( array( array( (string)$id, 'object' ), array( (string)$node, 'node' ), array( (string)$draft, 'version' ),
                         array( 'cf-article', 'remote' ), array( self::$seed['urlalias']['article'], 'urlalias' ),
                         array( 'node:' . $node, 'auto' ), array( 'https://www.example.invalid/' . self::$seed['urlalias']['article'], 'auto' ),
                         array( 'https://www.example.invalid/admin/content/view/full/' . $node, 'auto' ) ) as $case )
            $this->assertContains( $id, $this->ids( $case[0], $case[1] ), implode( ' as ', $case ) );
        // the whole alias resolves as an alias (eZURLAliasML::fetchByPath()), not only by its last part
        $result = $this->resultFor( self::$seed['urlalias']['article'], $id, 'urlalias' );
        $this->assertSame( 'urlalias', $result['matches'][0]['via'] );
        $this->assertSame( $node, (int)eZURLAliasML::fetchNodeIDByPath( self::$seed['urlalias']['article'] ) );

        $result = $this->resultFor( (string)$id, $id, 'object' );
        $this->assertSame( 'CF Article', $result['name'] );
        $this->assertSame( 'published', $result['status'] );
        $this->assertSame( array( 3, 2, 1 ), array_column( $result['versions'], 'version' ) );
        $this->assertSame( array( 'draft', 'published', 'archived' ), array_column( $result['versions'], 'status_name' ) );
        $this->assertCount( 1, $result['locations'] );
        $this->assertSame( $node, $result['locations'][0]['node_id'] );
        $this->assertTrue( $result['locations'][0]['is_main'] );
        $this->assertSame( 'cf-article', $result['remote_id'] );
        $this->assertNotEmpty( $result['languages'] );
        $this->assertFalse( $result['in_trash'] );
        $this->assertTrue( $result['actions']['trash'] );
        $this->assertTrue( $result['actions']['delete'] );
        $this->assertTrue( $result['actions']['remove_version'] );
    }

    /** CS-02 */
    public function testAnImagePathFindsAttributeObjectAndNode()
    {
        if ( empty( self::$seed['files']['image'] ) )
            $this->markTestSkipped( 'no image class' );
        $image = self::$seed['files']['image'][0];
        $objectID = self::$seed['objects']['image'];
        foreach ( array( $image['path'], '/' . $image['path'], 'https://www.example.invalid/' . $image['path'] ) as $text )
        {
            $result = $this->resultFor( $text, $objectID );
            $this->assertSame( 'file', $result['matches'][0]['via'], $text );
            $row = $result['matches'][0]['rows'][0];
            $this->assertSame( $image['attribute_id'], $row['attribute_id'] );
            $this->assertSame( 'ezimage', $row['datatype'] );
            $this->assertSame( 'image', $row['identifier'] );
            $this->assertContains( $image['path'], $row['files'] );
            $this->assertNotEmpty( $result['locations'] );
        }
        // the directory of the image, as a prefix; the attribute id
        $this->assertContains( $objectID, $this->ids( dirname( $image['path'] ) . '/' ) );
        $this->assertContains( $objectID, $this->ids( (string)$image['attribute_id'], 'attribute' ) );
        // one character less is no exact match
        $this->assertSame( array(), $this->ids( substr( $image['path'], 0, -1 ), 'file' ) );

        if ( !empty( self::$seed['files']['binary'] ) )
        {
            $binary = self::$seed['files']['binary'][0];
            $result = $this->resultFor( $binary['path'], self::$seed['objects']['file'] );
            $this->assertSame( 'ezbinaryfile', $result['matches'][0]['rows'][0]['datatype'] );
            $this->assertContains( self::$seed['objects']['file'], $this->ids( dirname( $binary['path'] ) . '/*' ) );
        }
    }

    /** CS-12 */
    public function testRemoteIDsAndPathsMatchInTheirCaseOnEveryEngine()
    {
        // MySQL compares case-insensitively and SQLite's LIKE does too; PostgreSQL, Oracle and MongoDB do not
        $article = self::$seed['objects']['article'];
        $this->assertContains( $article, $this->ids( 'cf-article', 'remote' ) );
        $this->assertSame( array(), $this->ids( 'CF-ARTICLE', 'remote' ) );
        if ( !empty( self::$seed['files']['image'] ) )
        {
            $path = self::$seed['files']['image'][0]['path'];
            $upper = 'var/' . strtoupper( substr( $path, 4 ) );
            $this->assertSame( array(), $this->ids( $upper, 'file' ) );
            $this->assertSame( array(), $this->ids( dirname( $upper ) . '/', 'file' ) );
            $this->assertContains( self::$seed['objects']['image'], $this->ids( dirname( $path ) . '/', 'file' ) );
        }
        if ( !empty( self::$seed['files']['binary'] ) )
        {
            $path = self::$seed['files']['binary'][0]['path'];
            $name = basename( $path );
            // the stored name is hexadecimal: its letters are what changes case
            $this->assertNotSame( strtoupper( $name ), $name );
            $this->assertSame( array(), $this->ids( dirname( $path ) . '/' . strtoupper( $name ), 'file' ) );
            if ( strtoupper( substr( $name, 0, 6 ) ) !== substr( $name, 0, 6 ) )
                $this->assertSame( array(), $this->ids( dirname( $path ) . '/' . strtoupper( substr( $name, 0, 6 ) ) . '*', 'file' ) );
            $this->assertContains( self::$seed['objects']['file'], $this->ids( dirname( $path ) . '/' . substr( $name, 0, 6 ) . '*', 'file' ) );
        }
    }

    /** CS-13 */
    public function testTheLastPartOfAURLAlias()
    {
        $article = self::$seed['objects']['article'];
        $alias = trim( self::$seed['urlalias']['article'], '/' );
        $elements = explode( '/', $alias );
        $this->assertGreaterThan( 1, count( $elements ), $alias );
        $last = end( $elements );
        foreach ( array( $last, strtoupper( $last ), implode( '/', array_slice( $elements, -2 ) ) ) as $text )
        {
            $result = $this->resultFor( $text, $article, 'urlalias' );
            $this->assertSame( 'urlalias_tail', $result['matches'][0]['via'], $text );
            $this->assertSame( $alias, $result['matches'][0]['value'], $text );
            $this->assertSame( self::$seed['nodes']['article'], $result['matches'][0]['node_id'] );
            $this->assertFalse( $result['matches'][0]['history'] );
        }
        // a tail that is not above it is no match of the last part; a part of an element is one only for the part
        // search (CS-14)
        $this->assertSame( array(), $this->ids( 'no-such-folder/' . $last, 'urlalias' ) );
        $result = $this->resultFor( substr( $last, 0, -1 ), $article, 'urlalias' );
        $this->assertSame( 'urlalias_part', $result['matches'][0]['via'] );

        // a renamed object: its old address is history, listed and marked when nothing current has that name
        $object = $this->article( 'CF CS-13 old name ' . bin2hex( random_bytes( 3 ) ) );
        $node = eZContentObjectTreeNode::fetch( (int)$object->attribute( 'main_node_id' ) );
        $oldElements = explode( '/', trim( (string)$node->urlAlias(), '/' ) );
        $oldLast = end( $oldElements );
        $this->signIn( 'cf_editor' );
        eZContentFunctions::updateAndPublishObject( $object, array( 'attributes' => $this->nameAttributes( 'CF CS-13 new name ' . bin2hex( random_bytes( 3 ) ) ) ) );
        $this->signIn( 'admin' );
        $result = $this->resultFor( $oldLast, $object->attribute( 'id' ), 'urlalias' );
        $this->assertSame( 'urlalias_tail', $result['matches'][0]['via'] );
        $this->assertTrue( $result['matches'][0]['history'] );
        $this->assertStringEndsWith( '/' . $oldLast, $result['matches'][0]['value'] );
        // rights per result: a reader outside the subtree does not get it
        $this->signIn( 'cf_reader' );
        $this->assertSame( array(), $this->ids( basename( (string)eZContentObjectTreeNode::fetch( self::$seed['nodes']['secret'] )->urlAlias() ), 'urlalias' ) );
    }

    /** CS-14 */
    public function testAPartOfAURLAlias()
    {
        $article = self::$seed['objects']['article'];
        $alias = trim( self::$seed['urlalias']['article'], '/' );
        // the seeded alias ends with cf-article: its part, in other case, with a space or an underscore for the dash
        foreach ( array( 'f-articl', 'F-ARTICL', 'f-artic', 'cf artic', 'cf_artic' ) as $text )
        {
            $result = $this->resultFor( $text, $article, 'urlalias' );
            $this->assertSame( 'urlalias_part', $result['matches'][0]['via'], $text );
            $this->assertSame( $alias, $result['matches'][0]['value'], $text );
            $this->assertSame( self::$seed['nodes']['article'], $result['matches'][0]['node_id'] );
            $this->assertFalse( $result['matches'][0]['history'] );
        }
        // the words in their order, and only with a separator between them
        $this->assertNotContains( $article, $this->ids( 'artic cf', 'urlalias' ) );
        $this->assertNotContains( $article, $this->ids( 'cfartic', 'urlalias' ) );

        // shorter than MinimumPartLength: nothing, with the hint
        $found = $this->search( 'cf', 'urlalias' );
        $this->assertSame( array(), $found['results'] );
        $this->assertSame( 'alias_part_too_short', $found['notice'] );
        $this->assertSame( '', $this->search( 'f-articl', 'urlalias' )['notice'] );

        // wildcards and quotes are text: no LIKE wildcard matches, no SQL error; a backslash is refused
        foreach ( array( '%%%', '___', 'c%e', 'cf_%', "art'icl", "x' OR '1'='1" ) as $text )
        {
            $found = $this->search( $text, 'urlalias' );
            $this->assertSame( '', $found['input']['error'], $text );
            $this->assertSame( array(), $found['results'], $text );
        }
        $this->assertSame( 'invalid', $this->search( 'art\\icl', 'urlalias' )['input']['error'] );

        // the cap: at most ResultLimit objects, and the page says there were more
        ezpINIHelper::setINISetting( 'contentfinder.ini', 'ContentFinderSettings', 'MinimumPartLength', 2 );
        eZContentObject::clearCache();
        $finder = new expContentFinder( 2 );
        $found = $finder->search( 'me', 'urlalias' );
        $this->assertCount( 2, $found['results'] );
        $this->assertTrue( $found['truncated'] );

        // switched off: no part search at all
        ezpINIHelper::setINISetting( 'contentfinder.ini', 'ContentFinderSettings', 'PartialAliasSearch', 'disabled' );
        $this->assertSame( array(), $this->ids( 'f-articl', 'urlalias' ) );
        ezpINIHelper::restoreINISettings();

        // rights per result: a reader outside the subtree does not get the secret one
        $this->signIn( 'cf_reader' );
        $this->assertSame( array(), $this->ids( 'f-secre', 'urlalias' ) );
        $this->assertContains( $article, $this->ids( 'f-articl', 'urlalias' ) );
    }

    /** CS-03 */
    public function testANodeInTheTrash()
    {
        $object = $this->article( 'CF CS-03 trashed' );
        $nodeID = (int)$object->attribute( 'main_node_id' );
        $this->signIn( 'admin' );
        eZContentObjectTreeNode::removeSubtrees( array( $nodeID ), true );
        $result = $this->resultFor( (string)$nodeID, $object->attribute( 'id' ), 'node' );
        $this->assertSame( 'trash_node', $result['matches'][0]['via'] );
        $this->assertTrue( $result['in_trash'] );
        $this->assertSame( 'archived', $result['status'] );
        $this->assertSame( $nodeID, $result['trash'][0]['node_id'] );
        $this->assertSame( self::$seed['nodes']['open'], $result['trash'][0]['parent_node_id'] );
        $this->assertGreaterThan( 0, $result['trash'][0]['trashed'] );
        if ( eZContentObjectTrashNode::hasTrashedByColumns() )
            $this->assertSame( (int)eZUser::fetchByName( 'admin' )->attribute( 'contentobject_id' ), $result['trash'][0]['trashed_by_id'] );
        $this->assertSame( array(), $result['locations'] );
        $this->assertTrue( $result['actions']['purge'] );
        $this->assertTrue( $result['actions']['restore'] );
        $this->assertFalse( $result['actions']['trash'] );
    }

    // ------------------------------------------------------------------ CS-04, CS-05: rights

    /** CS-04 */
    public function testRightsPerResult()
    {
        $article = self::$seed['objects']['article'];
        $secret = self::$seed['objects']['secret'];
        $draftID = self::$seed['article_versions'][3]['id'];

        $this->signIn( 'cf_reader' );
        $this->assertSame( array( $article ), $this->ids( (string)$article, 'object' ) );
        $this->assertSame( array(), $this->ids( (string)$secret, 'object' ), 'outside the subtree' );
        $this->assertSame( array(), $this->ids( (string)self::$seed['nodes']['secret'], 'node' ) );
        $this->assertSame( array(), $this->ids( 'cf-secret', 'remote' ) );
        $result = $this->resultFor( (string)$article, $article, 'object' );
        $this->assertSame( array( 3, 2, 1 ), array_column( $result['versions'], 'version' ), 'versionread in the subtree' );
        $this->assertFalse( $result['actions']['trash'], 'no contentfinder/delete' );
        $this->assertFalse( $result['actions']['remove_version'] );
        $this->assertSame( array(), $result['trash'], 'no content/restore' );

        $this->signIn( 'cf_noversions' );
        $result = $this->resultFor( (string)$article, $article, 'object' );
        $this->assertSame( array( 2 ), array_column( $result['versions'], 'version' ), 'the published version only' );
        $this->assertSame( array(), $this->ids( (string)$draftID, 'version' ), 'a draft it may not read' );
        $this->assertContains( $secret, $this->ids( (string)$secret, 'object' ), 'reads everywhere' );

        $this->signIn( 'cf_nofinder' );
        $this->assertFalse( expContentFinder::hasPolicy( 'contentfinder', 'search' ) );
        $user = eZUser::currentUser();
        $access = $user->hasAccessTo( 'contentfinder', 'search' );
        $this->assertSame( 'no', $access['accessWord'] );

        $this->signIn( 'cf_searcher' );
        $this->assertTrue( expContentFinder::hasPolicy( 'contentfinder', 'search' ) );
        $this->assertFalse( expContentFinder::hasPolicy( 'contentfinder', 'delete' ) );
    }

    /** CS-05 */
    public function testNotFoundAndNoAccessLookTheSame()
    {
        $this->signIn( 'cf_reader' );
        $denied = $this->search( (string)self::$seed['objects']['secret'], 'object' );
        $missing = $this->search( '2147480000', 'object' );
        $this->assertSame( 0, $denied['count'] );
        $this->assertSame( 1, $denied['hidden'], 'found, not shown' );
        $this->assertSame( 0, $missing['hidden'] );
        $render = function ( array $found ) {
            $found['input']['text'] = 'X';
            $tpl = eZTemplate::factory();
            $tpl->setVariable( 'search_text', 'X' );
            $tpl->setVariable( 'search_type', 'object' );
            $tpl->setVariable( 'types', expContentFinderInput::types() );
            $tpl->setVariable( 'found', $found );
            $tpl->setVariable( 'feedback', null );
            $tpl->setVariable( 'form_token', 'T' );
            $tpl->setVariable( 'token_field', expContentFinderAction::TOKEN_FIELD );
            $tpl->setVariable( 'can_act', false );
            return $tpl->fetch( 'design:contentfinder/search.tpl' );
        };
        $a = $render( $denied );
        $b = $render( $missing );
        $this->assertStringContainsString( 'cf-none', $a );
        $this->assertSame( $b, $a );
        // the plan of an action gives the same answer
        $this->signIn( 'cf_remover' );
        eZContentObject::clearCache();
        $this->assertSame( 'not_removable', expContentFinderAction::plan( 'trash', self::$seed['objects']['secret'] )['reason'] );
        $this->signIn( 'cf_reader' );
        $denied = expContentFinderAction::plan( 'trash', self::$seed['objects']['secret'] );
        $missing = expContentFinderAction::plan( 'trash', 2147480000 );
        $this->assertSame( 'not_found', $denied['reason'] );
        $this->assertSame( 'not_found', $missing['reason'] );
        $this->assertSame( '', $denied['name'] );
    }

    // ------------------------------------------------------------------ CS-06: injection

    /** CS-06 */
    public function testWildcardsAndQuotesAreLiteral()
    {
        $db = eZDB::instance();
        $before = $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject' );
        if ( !empty( self::$seed['files']['image'] ) )
        {
            $path = self::$seed['files']['image'][0]['path'];
            // cf-finder in the path: with _ unescaped, cf_finder would match it
            $this->assertStringContainsString( 'cf-finder', $path );
            $this->assertSame( array(), $this->ids( str_replace( 'cf-finder', 'cf_finder', dirname( $path ) ) . '/' ) );
            $this->assertSame( array(), $this->ids( str_replace( 'cf-finder', 'cf%finder', dirname( $path ) ) . '/' ) );
        }
        foreach ( array( "' OR '1'='1", "x'); DROP TABLE ezcontentobject; --", '%', '_', "cf-article' --", '\\' ) as $text )
        {
            foreach ( array( 'remote', 'urlalias', 'auto' ) as $type )
            {
                $found = $this->search( $text, $type );
                $this->assertSame( 0, $found['count'], "$text as $type" );
                $this->assertSame( 0, $found['hidden'], "$text as $type" );
            }
        }
        $this->assertSame( array(), $this->ids( "var/site/storage/images/a'b.jpg" ) );
        $this->assertSame( array(), $this->ids( "var/site/storage/images/a'b/*" ) );
        $this->assertSame( $before, $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezcontentobject' ) );
    }

    // ------------------------------------------------------------------ CS-07, CS-08: refusals and plans

    /** CS-07 */
    public function testTheActionViewRefuses()
    {
        $this->signIn( 'cf_remover' );
        $token = array( expContentFinderAction::TOKEN_FIELD => expContentFinderAction::formToken() );
        $id = (string)self::$seed['objects']['article'];
        $this->assertSame( 'method', Action::decide( 'GET', $token + array( 'Action' => 'trash', 'ObjectID' => $id ) )['reason'] );
        $this->assertSame( 'method', Action::decide( 'HEAD', array() )['reason'] );
        $this->assertSame( 'token', Action::decide( 'POST', array( 'Action' => 'trash', 'ObjectID' => $id ) )['reason'] );
        $this->assertSame( 'token', Action::decide( 'POST', array( expContentFinderAction::TOKEN_FIELD => str_repeat( 'a', 64 ), 'Action' => 'trash', 'ObjectID' => $id ) )['reason'] );
        $this->assertSame( 'token', Action::decide( 'POST', array( expContentFinderAction::TOKEN_FIELD => array( 'x' ), 'Action' => 'trash', 'ObjectID' => $id ) )['reason'] );
        $this->assertSame( 'unknown_action', Action::decide( 'POST', $token + array( 'Action' => 'drop', 'ObjectID' => $id ) )['reason'] );
        $this->assertSame( 'not_found', Action::decide( 'POST', $token + array( 'Action' => 'trash', 'ObjectID' => '1 OR 1=1' ) )['reason'] );
        $this->assertSame( array( 'confirm', '' ), array_values( array_intersect_key( Action::decide( 'POST', $token + array( 'Action' => 'trash', 'ObjectID' => $id ) ), array( 'step' => 1, 'reason' => 1 ) ) ) );
        $this->assertSame( 'execute', Action::decide( 'POST', $token + array( 'Action' => 'trash', 'ObjectID' => $id, 'ConfirmButton' => 'x' ) )['step'] );
        $permanent = Action::decide( 'POST', $token + array( 'Action' => 'delete', 'ObjectID' => $id, 'ConfirmButton' => 'x' ) );
        $this->assertSame( array( 'confirm', 'confirm_permanent' ), array( $permanent['step'], $permanent['reason'] ) );
        $this->assertSame( 'execute', Action::decide( 'POST', $token + array( 'Action' => 'delete', 'ObjectID' => $id, 'ConfirmButton' => 'x', 'ConfirmPermanent' => '1' ) )['step'] );
        $this->assertSame( 'cancel', Action::decide( 'POST', $token + array( 'Action' => 'delete', 'ObjectID' => $id, 'CancelButton' => 'x' ) )['step'] );

        // another user's token is not this user's
        $this->signIn( 'cf_searcher' );
        $this->assertSame( 'token', Action::decide( 'POST', $token + array( 'Action' => 'trash', 'ObjectID' => $id ) )['reason'] );
        $own = array( expContentFinderAction::TOKEN_FIELD => expContentFinderAction::formToken() );
        $this->assertSame( 'no_policy', Action::decide( 'POST', $own + array( 'Action' => 'trash', 'ObjectID' => $id ) )['reason'] );
        // and the module itself asks for both policies
        $module = eZModule::exists( 'contentfinder' );
        $this->assertInstanceOf( 'eZModule', $module );
        $views = $module->attribute( 'views' );
        $this->assertSame( array( 'search', 'delete' ), $views['action']['functions'] );
        $this->assertSame( array( 'search' ), $views['search']['functions'] );
        $params = array();
        $this->assertFalse( eZUser::currentUser()->hasAccessToView( $module, 'action', $params ) );
        $this->assertTrue( eZUser::currentUser()->hasAccessToView( $module, 'search', $params ) );
        $this->signIn( 'cf_nofinder' );
        $this->assertFalse( eZUser::currentUser()->hasAccessToView( $module, 'search', $params ) );
    }

    /** CS-08 */
    public function testPlans()
    {
        $article = $this->article( 'CF CS-08 plan' );
        $id = (int)$article->attribute( 'id' );
        $this->signIn( 'cf_searcher' );
        $this->assertSame( 'no_policy', expContentFinderAction::plan( 'trash', $id )['reason'] );
        $this->signIn( 'cf_reader' );
        $this->assertSame( 'no_policy', expContentFinderAction::plan( 'delete', $id )['reason'] );
        $this->signIn( 'cf_remover' );
        $plan = expContentFinderAction::plan( 'trash', $id );
        $this->assertTrue( $plan['allowed'], $plan['reason'] );
        $this->assertSame( 1, $plan['locations'] );
        $this->assertSame( 0, $plan['children'] );
        $this->assertSame( (int)$article->attribute( 'main_node_id' ), $plan['nodes'][0]['node_id'] );
        $this->assertTrue( $plan['move_to_trash'] );
        $this->assertFalse( $plan['permanent'] );
        $this->assertTrue( expContentFinderAction::plan( 'delete', $id )['permanent'] );
        $this->assertSame( 'not_in_trash', expContentFinderAction::plan( 'purge', $id )['reason'] );
        $this->assertSame( 'not_found', expContentFinderAction::plan( 'removeversion', $id, self::$seed['article_versions'][1]['id'] )['reason'], 'a version of another object' );
        $this->assertSame( 'version_not_removable', expContentFinderAction::plan( 'removeversion', $id, (int)$article->currentVersion()->attribute( 'id' ) )['reason'], 'the published version' );
        // the folder above CF Open is outside the remover's content/remove
        $folder = expContentFinderAction::plan( 'trash', self::$seed['objects']['finder'] );
        $this->assertFalse( $folder['allowed'] );
        $this->assertContains( $folder['reason'], array( 'not_removable', 'too_many' ) );
    }

    // ------------------------------------------------------------------ CS-09, CS-10: actions and audit

    /** CS-09 */
    public function testActions()
    {
        $dir = $this->audit();
        $trash = $this->article( 'CF CS-09 trash' );
        $delete = $this->article( 'CF CS-09 delete' );
        $purge = $this->article( 'CF CS-09 purge' );
        $this->signIn( 'cf_remover' );

        // a confirmation of another plan (another object) does not fit
        $planTrash = expContentFinderAction::plan( 'trash', $trash->attribute( 'id' ) );
        $planDelete = expContentFinderAction::plan( 'delete', $delete->attribute( 'id' ) );
        $wrong = expContentFinderAction::execute( 'trash', $trash->attribute( 'id' ), 0, expContentFinderAction::confirmToken( $planDelete ) );
        $this->assertSame( array( false, 'changed' ), array( $wrong['done'], $wrong['reason'] ) );
        $this->assertSame( eZContentObject::STATUS_PUBLISHED, (int)eZContentObject::fetch( $trash->attribute( 'id' ) )->attribute( 'status' ) );

        $done = expContentFinderAction::execute( 'trash', $trash->attribute( 'id' ), 0, expContentFinderAction::confirmToken( $planTrash ) );
        $this->assertTrue( $done['done'], $done['reason'] );
        eZContentObject::clearCache();
        $this->assertSame( eZContentObject::STATUS_ARCHIVED, (int)eZContentObject::fetch( $trash->attribute( 'id' ) )->attribute( 'status' ) );
        // the same confirmation again: the plan changed (the object is in the trash now)
        $again = expContentFinderAction::execute( 'trash', $trash->attribute( 'id' ), 0, expContentFinderAction::confirmToken( $planTrash ) );
        $this->assertFalse( $again['done'] );

        $done = expContentFinderAction::execute( 'delete', $delete->attribute( 'id' ), 0, expContentFinderAction::confirmToken( $planDelete ) );
        $this->assertTrue( $done['done'], $done['reason'] );
        eZContentObject::clearCache();
        $this->assertNull( eZContentObject::fetch( $delete->attribute( 'id' ) ) );
        $this->assertSame( array(), eZContentObjectTrashNode::trashList( array( 'ContentObjectIDList' => array( (int)$delete->attribute( 'id' ) ), 'AttributeFilter' => false, 'Limitation' => array() ) ) ?: array() );

        $this->signIn( 'admin' );
        eZContentObjectTreeNode::removeSubtrees( array( (int)$purge->attribute( 'main_node_id' ) ), true );
        $this->signIn( 'cf_remover' );
        $planPurge = expContentFinderAction::plan( 'purge', $purge->attribute( 'id' ) );
        $this->assertTrue( $planPurge['allowed'], $planPurge['reason'] );
        $done = expContentFinderAction::execute( 'purge', $purge->attribute( 'id' ), 0, expContentFinderAction::confirmToken( $planPurge ) );
        $this->assertTrue( $done['done'], $done['reason'] );
        eZContentObject::clearCache();
        $this->assertNull( eZContentObject::fetch( $purge->attribute( 'id' ) ) );

        // remove a version: the archived first version of an object with two
        $versioned = $this->article( 'CF CS-09 versions' );
        $this->assertTrue( (bool)eZContentFunctions::updateAndPublishObject( $versioned, array( 'attributes' => $this->nameAttributes( 'CF CS-09 versions, 2' ) ) ) );
        eZContentObject::clearCache();
        $first = eZContentObjectVersion::fetchVersion( 1, $versioned->attribute( 'id' ) );
        $this->signIn( 'cf_remover' );
        $planVersion = expContentFinderAction::plan( 'removeversion', $versioned->attribute( 'id' ), $first->attribute( 'id' ) );
        $this->assertTrue( $planVersion['allowed'], $planVersion['reason'] );
        $this->assertSame( 1, $planVersion['version']['version'] );
        $done = expContentFinderAction::execute( 'removeversion', $versioned->attribute( 'id' ), $first->attribute( 'id' ), expContentFinderAction::confirmToken( $planVersion ) );
        $this->assertTrue( $done['done'], $done['reason'] );
        $this->assertNull( eZContentObjectVersion::fetch( $first->attribute( 'id' ) ) );
        $this->assertNotNull( eZContentObject::fetch( $versioned->attribute( 'id' ) ) );

        // the audit: one content.finder.action per action, done, by this user, the kernel's removal events below it
        $records = $this->auditRecords( 'content' );
        $actions = array_values( array_filter( $records, function ( $r ) { return $r['name'] === 'content.finder.action'; } ) );
        $byAction = array();
        foreach ( $actions as $r )
            $byAction[$r['after']['action']][] = $r;
        foreach ( array( 'trash', 'delete', 'purge', 'removeversion' ) as $a )
        {
            $this->assertArrayHasKey( $a, $byAction, "content.finder.action $a" );
            $last = end( $byAction[$a] );
            $this->assertSame( 'success', $last['result'] ?? 'success', $a );
            $this->assertSame( (int)eZUser::fetchByName( 'cf_remover' )->attribute( 'contentobject_id' ), (int)$last['actor']['user_id'], $a );
        }
        $refused = array_values( array_filter( $actions, function ( $r ) { return ( $r['result'] ?? '' ) === 'refused'; } ) );
        $this->assertEmpty( $refused, 'execute() itself records only what it did; the view records refusals' );
        $trashParent = $byAction['trash'][0]['id'];
        $children = array_filter( $records, function ( $r ) use ( $trashParent ) { return ( $r['parent'] ?? null ) === $trashParent; } );
        $this->assertNotEmpty( $children, 'the kernel removal event is a child of the finder action' );
        $this->assertSame( 'content.node.remove.trash', reset( $children )['name'] );
        $this->assertSame( 'node', $byAction['trash'][0]['target']['type'] );
        $this->assertSame( (string)$planTrash['nodes'][0]['node_id'], (string)$byAction['trash'][0]['target']['id'] );
        $this->assertSame( 'version', $byAction['removeversion'][0]['target']['type'] );

        // a refusal, as the view records it
        expContentFinderAction::auditRefusal( $wrong['plan'], 'changed' );
        $records = $this->auditRecords( 'content' );
        $last = end( $records );
        $this->assertSame( 'content.finder.action', $last['name'] );
        $this->assertSame( 'refused', $last['result'] );
        $this->assertSame( 'changed', $last['reason'] );
    }

    /** CS-10 */
    public function testSearchesAreAudited()
    {
        $this->audit();
        $this->signIn( 'cf_reader' );
        Search::audit( $this->search( (string)self::$seed['objects']['article'], 'object' ) );
        Search::audit( $this->search( (string)self::$seed['objects']['secret'], 'auto' ) );
        Search::audit( $this->search( "a\0b" ) );
        $records = array_values( array_filter( $this->auditRecords( 'content' ), function ( $r ) { return $r['name'] === 'content.finder.search'; } ) );
        $this->assertCount( 3, $records );
        $reader = (int)eZUser::fetchByName( 'cf_reader' )->attribute( 'contentobject_id' );
        $this->assertSame( $reader, (int)$records[0]['actor']['user_id'] );
        $this->assertSame( array( 'object', array( 'object' ), (string)self::$seed['objects']['article'], 1, 0 ),
                           array( $records[0]['after']['type'], $records[0]['after']['as'], $records[0]['after']['value'], $records[0]['after']['results'], $records[0]['after']['hidden'] ) );
        $this->assertSame( array( 'auto', array( 'object', 'node', 'attribute', 'version' ), 0, true ),
                           array( $records[1]['after']['type'], $records[1]['after']['as'], $records[1]['after']['results'], $records[1]['after']['hidden'] >= 1 ) );
        $this->assertSame( array( 'refused', 'invalid', '' ), array( $records[2]['result'], $records[2]['reason'], $records[2]['after']['value'] ) );
        $this->assertSame( 'read', $records[0]['verb'] );
    }

    /** CS-11 */
    public function testWhereTheFinderAnswers()
    {
        $this->assertTrue( expContentFinder::isAvailableHere(), 'the admin siteaccess has an administration design' );
        ezpINIHelper::setINISetting( 'contentfinder.ini', 'ContentFinderSettings', 'SiteAccessList', array( 'site' ) );
        $this->assertFalse( expContentFinder::isAvailableHere(), 'not in SiteAccessList[]' );
        ezpINIHelper::setINISetting( 'contentfinder.ini', 'ContentFinderSettings', 'SiteAccessList', array( 'admin' ) );
        $this->assertTrue( expContentFinder::isAvailableHere() );
        ezpINIHelper::setINISetting( 'contentfinder.ini', 'ContentFinderSettings', 'Enabled', 'false' );
        $this->assertFalse( expContentFinder::isAvailableHere(), 'Enabled=false' );
        ezpINIHelper::restoreINISettings();
        $design = eZINI::instance()->variable( 'DesignSettings', 'SiteDesign' );
        $additional = eZINI::instance()->variable( 'DesignSettings', 'AdditionalSiteDesignList' );
        ezpINIHelper::setINISetting( 'site.ini', 'DesignSettings', 'SiteDesign', 'media' );
        ezpINIHelper::setINISetting( 'site.ini', 'DesignSettings', 'AdditionalSiteDesignList', array( 'standard' ) );
        $this->assertFalse( expContentFinder::isAvailableHere(), 'a public design' );
        ezpINIHelper::restoreINISettings();
        $this->assertSame( $design, eZINI::instance()->variable( 'DesignSettings', 'SiteDesign' ) );
        $this->assertSame( $additional, eZINI::instance()->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) );
    }
}
