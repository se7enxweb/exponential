<?php
/**
 * Who sees help maintained as content, decided from the repository's cached rows without loading the objects
 * (expHelpContentAccess, expHelpContentProvider::newestTopics()), and the repository's cache file. No database.
 *
 *  HCA-01  an unlimited access word allows every published object, 'no' none
 *  HCA-02  Section, Class, State and StateGroup_ limitations, as checkAccess() compares them; all of a policy must
 *          match, one policy is enough
 *  HCA-03  Subtree matches a part of any location's path (checkAccess() uses strstr), User_Subtree likewise
 *  HCA-04  Node and Subtree in one policy: either is enough, as in checkAccess()
 *  HCA-05  Owner (and ParentOwner), Language and ParentDepth
 *  HCA-06  what only the object can tell gives null: Group, an extension's limitation, the anonymous "own session"
 *          owner, an object that is not published, the user's own object, the member management guard
 *  HCA-07  decide(): Released and readable is read, else editable is preview, else none; null passes through
 *  HCA-08  newestTopics(): the newest visible topics (the same time in tree order), the count, previews, without
 *          loading an object when the rows decide
 *  HCA-09  the cache file: the index is read without the texts, the texts with it, a foreign or damaged file is none
 *  HCA-10  searchWordSets(): each word of a field and its stem
 *  HCA-11  the background build: the command line PHP, one build at a time (the marker), the siteaccesses that used
 *          the cache
 *  HCA-12  exp:velocity deploy fills the help cache after the cache clears and before the warm-up, SKIP without help
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/help/expHelpContentAccessTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpContentAccessTestRepository extends expHelpContentRepository
{
    public $rows = array();
    public $data = array();
    public $objectCalls = 0;

    public function index()
    {
        return array( 'topics' => $this->rows, 'figures' => array(), 'maps' => array(), 'glossary' => array() );
    }

    public function load()
    {
        return $this->index() + array( 'objects' => array() );
    }

    public function accessData( $objectId )
    {
        return isset( $this->data[$objectId] ) ? $this->data[$objectId] : null;
    }

    public function object( $objectId )
    {
        $this->objectCalls++;
        return null;
    }
}

class expHelpContentAccessTestProvider extends expHelpContentProvider
{
    public $checker;

    protected function accessChecker()
    {
        return $this->checker;
    }
}

class expHelpContentAccessTest extends PHPUnit\Framework\TestCase
{
    const USER = 500;

    private static function data( array $override = array() )
    {
        return $override + array( 'id' => 1000, 'class_id' => 70, 'section_id' => 6, 'owner_id' => 14, 'status' => 1, 'main_node_id' => 300,
                                  'state_ids' => array( 1, 82 ), 'locations' => array( array( 'path' => '/1/43/46073/200/300/', 'depth' => 5 ) ) );
    }

    private static function limited( array $policies )
    {
        return array( 'accessWord' => 'limited', 'policies' => $policies );
    }

    private static function access( $read, $edit = null, $anonymous = false, $guarded = null )
    {
        return new expHelpContentAccess( array( 'read' => $read, 'edit' => $edit !== null ? $edit : array( 'accessWord' => 'no' ) ), self::USER,
            $anonymous,
            function ( $nodeId ) { return $nodeId === 999 ? 300 : (int)$nodeId; },
            function ( array $locales ) { return in_array( 'eng-GB', $locales, true ) ? 4 : 0; },
            $guarded );
    }

    /** HCA-01 */
    public function testAccessWords()
    {
        $this->assertSame( 1, self::access( array( 'accessWord' => 'yes' ) )->check( 'read', self::data() ) );
        $this->assertSame( 0, self::access( array( 'accessWord' => 'no' ) )->check( 'read', self::data() ) );
        $this->assertSame( 0, self::access( self::limited( array() ) )->check( 'read', self::data() ) );
    }

    /** HCA-02 */
    public function testSectionClassState()
    {
        $access = self::access( self::limited( array( 'p1' => array( 'Section' => array( '6', '7' ) ) ) ) );
        $this->assertSame( 1, $access->check( 'read', self::data() ) );
        $this->assertSame( 0, $access->check( 'read', self::data( array( 'section_id' => 8 ) ) ) );
        $access = self::access( self::limited( array( 'p1' => array( 'Section' => array( '6' ), 'Class' => array( '71' ) ),
                                                      'p2' => array( 'StateGroup_help_status' => array( '82' ) ) ) ) );
        $this->assertSame( 1, $access->check( 'read', self::data() ), 'the second policy' );
        $this->assertSame( 0, $access->check( 'read', self::data( array( 'state_ids' => array( 1, 80 ) ) ) ) );
        $this->assertSame( 1, $access->check( 'read', self::data( array( 'class_id' => 71, 'state_ids' => array( 80 ) ) ) ), 'the first policy' );
        $access = self::access( self::limited( array( 'p1' => array( 'State' => array( '1' ), 'User_Section' => array( '6' ) ) ) ) );
        $this->assertSame( 1, $access->check( 'read', self::data() ) );
        $this->assertSame( 0, $access->check( 'read', self::data( array( 'state_ids' => array( 2 ) ) ) ) );
        $this->assertSame( 1, self::access( self::limited( array( 'p' => array( 'ParentClass' => array( 70 ) ) ) ) )->check( 'read', self::data() ),
            'checkAccess() compares ParentClass with the object\'s own class' );
    }

    /** HCA-03 */
    public function testSubtree()
    {
        $access = self::access( self::limited( array( 'p' => array( 'Subtree' => array( '/1/43/46073/' ) ) ) ) );
        $this->assertSame( 1, $access->check( 'read', self::data() ) );
        $this->assertSame( 0, $access->check( 'read', self::data( array( 'locations' => array( array( 'path' => '/1/2/9/', 'depth' => 3 ) ) ) ) ) );
        $this->assertSame( 1, $access->check( 'read', self::data( array( 'locations' => array( array( 'path' => '/1/2/9/', 'depth' => 3 ),
            array( 'path' => '/1/43/46073/5/', 'depth' => 4 ) ) ) ) ), 'any location' );
        $this->assertSame( 1, self::access( self::limited( array( 'p' => array( 'Subtree' => array( '/200/' ) ) ) ) )->check( 'read', self::data() ),
            'a part of the path, as strstr()' );
        $access = self::access( self::limited( array( 'p' => array( 'User_Subtree' => array( '/46073/' ) ) ) ) );
        $this->assertSame( 1, $access->check( 'read', self::data() ) );
        $this->assertSame( 0, $access->check( 'read', self::data( array( 'locations' => array( array( 'path' => '/1/2/', 'depth' => 1 ) ) ) ) ) );
    }

    /** HCA-04 */
    public function testNodeAndSubtree()
    {
        $access = self::access( self::limited( array( 'p' => array( 'Node' => array( 999 ), 'Subtree' => array( '/1/2/' ) ) ) ) );
        $this->assertSame( 1, $access->check( 'read', self::data() ), 'the node (999 has the main node 300)' );
        $this->assertSame( 1, $access->check( 'read', self::data( array( 'main_node_id' => 5, 'locations' => array( array( 'path' => '/1/2/5/', 'depth' => 2 ) ) ) ) ),
            'the subtree' );
        $this->assertSame( 0, $access->check( 'read', self::data( array( 'main_node_id' => 5 ) ) ), 'neither' );
        $access = self::access( self::limited( array( 'p' => array( 'Subtree' => array( '/1/2/' ), 'Node' => array( 999 ) ) ) ) );
        $this->assertSame( 1, $access->check( 'read', self::data() ), 'the other order' );
        $this->assertSame( 0, $access->check( 'read', self::data( array( 'main_node_id' => 5 ) ) ) );
    }

    /** HCA-05 */
    public function testOwnerLanguageDepth()
    {
        $access = self::access( self::limited( array( 'p' => array( 'Owner' => array( '1' ), 'Class' => array( '70' ) ) ) ) );
        $this->assertSame( 0, $access->check( 'read', self::data() ) );
        $this->assertSame( 1, $access->check( 'read', self::data( array( 'owner_id' => self::USER ) ) ) );
        $this->assertSame( 1, self::access( self::limited( array( 'p' => array( 'ParentOwner' => array( '1' ) ) ) ) )
            ->check( 'read', self::data( array( 'owner_id' => self::USER ) ) ) );
        $this->assertSame( 1, self::access( self::limited( array( 'p' => array( 'Language' => array( 'eng-GB' ) ) ) ) )->check( 'read', self::data() ) );
        $this->assertSame( 0, self::access( self::limited( array( 'p' => array( 'Language' => array( 'nor-NO' ) ) ) ) )->check( 'read', self::data() ) );
        $this->assertSame( 1, self::access( self::limited( array( 'p' => array( 'ParentDepth' => array( '5' ) ) ) ) )->check( 'read', self::data() ) );
        $this->assertSame( 0, self::access( self::limited( array( 'p' => array( 'ParentDepth' => array( '3' ) ) ) ) )->check( 'read', self::data() ) );
    }

    /** HCA-06 */
    public function testUndecided()
    {
        $this->assertNull( self::access( self::limited( array( 'p' => array( 'Group' => array( '1' ) ) ) ) )->check( 'read', self::data() ) );
        $this->assertNull( self::access( self::limited( array( 'p' => array( 'SomeExtension' => array( '1' ) ) ) ) )->check( 'read', self::data() ) );
        $this->assertNull( self::access( self::limited( array( 'p' => array( 'Owner' => array( '2' ) ) ) ), null, true )->check( 'read', self::data() ) );
        $this->assertSame( 0, self::access( self::limited( array( 'p' => array( 'Owner' => array( '2' ) ) ) ) )->check( 'read', self::data() ),
            'a user who is not anonymous: the owner as usual' );
        $this->assertNull( self::access( array( 'accessWord' => 'yes' ) )->check( 'read', self::data( array( 'status' => 0 ) ) ), 'a draft' );
        $this->assertNull( self::access( array( 'accessWord' => 'yes' ) )->check( 'read', self::data( array( 'id' => self::USER ) ) ), 'the user' );
        $guard = function ( $function, $classId ) { return $classId === 70; };
        $this->assertNull( self::access( self::limited( array( 'p' => array( 'Section' => array( '6' ) ) ) ), null, false, $guard )->check( 'read', self::data() ) );
        $this->assertSame( 1, self::access( array( 'accessWord' => 'yes' ), null, false, $guard )->check( 'read', self::data() ), 'yes passes the guard' );
        $this->assertNull( self::access( self::limited( array( 'p' => array( 'Section' => array( '6' ) ) ) ) )
            ->check( 'read', self::data( array( 'locations' => array() ) ) ), 'no location' );
    }

    /** HCA-07 */
    public function testDecide()
    {
        $editor = self::access( self::limited( array( 'p' => array( 'Section' => array( '1' ) ) ) ), array( 'accessWord' => 'yes' ) );
        $this->assertSame( 'preview', $editor->decide( self::data(), true ), 'not readable, editable' );
        $reader = self::access( array( 'accessWord' => 'yes' ) );
        $this->assertSame( 'read', $reader->decide( self::data(), true ) );
        $this->assertSame( 'none', $reader->decide( self::data(), false ), 'a draft topic for a reader' );
        $this->assertSame( 'preview', self::access( array( 'accessWord' => 'yes' ), array( 'accessWord' => 'yes' ) )->decide( self::data(), false ) );
        $this->assertNull( self::access( self::limited( array( 'p' => array( 'Group' => array( '1' ) ) ) ) )->decide( self::data(), true ) );
        $this->assertSame( 'read', self::access( array( 'accessWord' => 'yes' ), self::limited( array( 'p' => array( 'Group' => array( '1' ) ) ) ) )
            ->decide( self::data(), true ), 'edit is not asked when read is enough' );
    }

    /** HCA-08 */
    public function testNewestTopics()
    {
        $repository = new expHelpContentAccessTestRepository();
        $rows = array( array( 'a', 100, 6, 'released' ), array( 'b', 300, 6, 'released' ), array( 'c', 300, 6, 'released' ),
                       array( 'd', 400, 1, 'released' ), array( '', 900, 6, 'released' ), array( 'e', 200, 6, 'draft' ) );
        foreach ( $rows as $i => $row )
        {
            $id = 1000 + $i;
            $repository->rows[] = array( 'object_id' => $id, 'help_id' => $row[0], 'modified' => $row[1], 'state' => $row[3], 'title' => strtoupper( $row[0] ) );
            $repository->data[$id] = self::data( array( 'id' => $id, 'section_id' => $row[2], 'state_ids' => array( $row[3] === 'draft' ? 80 : 82 ) ) );
        }
        $provider = new expHelpContentAccessTestProvider( $repository );
        // reads section 6, edits drafts (state 80): a preview
        $provider->checker = self::access( self::limited( array( 'p' => array( 'Section' => array( '6' ) ) ) ),
                                           self::limited( array( 'p' => array( 'StateGroup_help_status' => array( '80' ) ) ) ) );
        $newest = $provider->newestTopics( 3 );
        $this->assertSame( 4, $newest['count'], 'a, b, c and the preview e; d is in another section, the empty help_id is not listed' );
        $this->assertSame( array( 'b', 'c', 'e' ), array_column( $newest['topics'], 'help_id' ), 'newest first, the same time in tree order' );
        $this->assertSame( array( false, false, true ), array_column( $newest['topics'], 'preview' ) );
        $this->assertSame( 0, $repository->objectCalls, 'no object was loaded' );
    }

    /** HCA-09 */
    public function testCacheFile()
    {
        $dir = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-exphelp-access-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        mkdir( $dir, 0777, true );
        $file = $dir . '/repository-x.cache';
        try
        {
            $index = array( 'format' => expHelpContentRepository::CACHE_FORMAT, 'stamp' => 's1', 'topics' => array( array( 'object_id' => 1 ) ),
                            'figures' => array(), 'maps' => array(), 'glossary' => array(), 'access' => array( 1 => self::data() ), 'signatures' => array() );
            $serialized = serialize( $index );
            file_put_contents( $file, expHelpContentRepository::CACHE_FORMAT . "\n" . strlen( $serialized ) . "\n" . $serialized
                . serialize( array( 'stamp' => 's1', 'fields' => array( 1 => array( 'body' => 'Text' ) ) ) ) );
            $read = expHelpContentRepository::readCacheFile( $file, false );
            $this->assertSame( 's1', $read['stamp'] );
            $this->assertArrayNotHasKey( 'fields', $read, 'the index alone' );
            $this->assertSame( array( 1 => array( 'body' => 'Text' ) ), expHelpContentRepository::readCacheFile( $file, true )['fields'] );
            // fields of another stamp (a file written in parts): none
            file_put_contents( $file, expHelpContentRepository::CACHE_FORMAT . "\n" . strlen( $serialized ) . "\n" . $serialized
                . serialize( array( 'stamp' => 's0', 'fields' => array() ) ) );
            $this->assertNotNull( expHelpContentRepository::readCacheFile( $file, false ) );
            $this->assertNull( expHelpContentRepository::readCacheFile( $file, true ) );
            file_put_contents( $file, "another-format\n10\n0123456789" );
            $this->assertNull( expHelpContentRepository::readCacheFile( $file, false ) );
            file_put_contents( $file, expHelpContentRepository::CACHE_FORMAT . "\n50\nnot serialized" );
            $this->assertNull( expHelpContentRepository::readCacheFile( $file, false ) );
            // an object in the file is never made, and a file with one is none
            $evil = serialize( array( 'stamp' => 's', 'topics' => new ArrayObject(), 'figures' => array(), 'maps' => array(), 'glossary' => array(),
                                      'access' => array(), 'signatures' => array() ) );
            file_put_contents( $file, expHelpContentRepository::CACHE_FORMAT . "\n" . strlen( $evil ) . "\n" . $evil );
            $this->assertNull( expHelpContentRepository::readCacheFile( $file, false ) );
            $this->assertNull( expHelpContentRepository::readCacheFile( $dir . '/missing.cache', false ) );
        }
        finally
        {
            foreach ( (array)glob( $dir . '/*' ) as $f )
                unlink( $f );
            rmdir( $dir );
        }
    }

    /** HCA-11 */
    public function testBackgroundBuild()
    {
        $php = expHelpContentRepository::commandLinePhp();
        $this->assertNotSame( '', $php, 'a command line PHP is found' );
        $this->assertTrue( is_executable( $php ) );
        $this->assertStringNotContainsString( 'fpm', basename( $php ) );

        $dir = dirname( __DIR__, 5 ) . '/var/tmp/phpunit-exphelp-build-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        mkdir( $dir, 0777, true );
        try
        {
            // a build that runs (a fresh marker): no second one
            touch( $dir . '/repository-x.cache.building' );
            $this->assertTrue( expHelpContentRepository::startBackgroundBuild( $dir . '/repository-x.cache' ) );
            $this->assertSame( array( 'repository-x.cache.building' ), array_values( array_diff( scandir( $dir ), array( '.', '..' ) ) ) );
            expHelpContentRepository::finishBackgroundBuild( $dir . '/repository-x.cache' );
            $this->assertFileDoesNotExist( $dir . '/repository-x.cache.building' );

            // the siteaccesses that used a cache: only valid names
            file_put_contents( $dir . '/siteaccesses.json', json_encode( array( 'repository-a.cache' => 'admin', 'repository-b.cache' => 'bad name;',
                                                                                'repository-c.cache' => array( 'x' ), 'repository-d.cache' => 'eng_site' ) ) );
            $this->assertSame( array( 'repository-a.cache' => 'admin', 'repository-d.cache' => 'eng_site' ),
                               expHelpContentRepository::recordedSiteAccesses( $dir ) );
            unlink( $dir . '/siteaccesses.json' );
            $this->assertSame( array(), expHelpContentRepository::recordedSiteAccesses( $dir ) );
        }
        finally
        {
            foreach ( (array)glob( $dir . '/*' ) as $f )
                unlink( $f );
            rmdir( $dir );
        }
    }

    /** HCA-12 */
    public function testDeployFillsTheHelpCache()
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/classes/expvelocitydeploy.php' );
        $help = strpos( $source, '$this->helpCacheSteps()' );
        $this->assertNotFalse( $help, 'the deploy has the help cache steps' );
        $this->assertGreaterThan( strpos( $source, "'velocity-cache'" ), $help, 'after the caches are cleared' );
        $this->assertLessThan( strpos( $source, '$steps[] = $this->warmUpStep();' ), $help, 'before the warm-up requests' );
        $this->assertStringContainsString( "'bin/php/exphelpcontent.php', 'cache', '-s'", $source );
        $this->assertStringContainsString( "strpos( \$tail, 'SKIP:' ) === 0", $source, 'help content not installed is a SKIP' );
    }

    /** HCA-10 */
    public function testSearchWordSets()
    {
        $sets = expHelpContentProvider::searchWordSets( array( 'title' => 'Clearing caches', 'help_id' => 'admin.cache-clear', 'shortdesc' => '',
                                                               'fields' => array( 'body' => 'Use **ezcache.php** to clear [the caches](help:x).' ) ) );
        $this->assertArrayHasKey( 'caches', $sets['title'] );
        $this->assertArrayHasKey( 'cache', $sets['title'], 'the stem' );
        $this->assertArrayHasKey( 'cache', $sets['help_id'] );
        $this->assertSame( array(), $sets['shortdesc'] );
        $this->assertArrayHasKey( 'ezcache', $sets['text'] );
        $this->assertArrayNotHasKey( 'help', $sets['text'], 'link targets are not text' );
    }
}
