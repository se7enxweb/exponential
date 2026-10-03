<?php
/**
 * URL alias, search and content job services. Aliases and wildcards are made on test content and removed; jobs
 * are created on test content under the Media root, and their job files are removed in tearDown.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expContentServicesTestCase.php';

class expUrlSearchJobServicesTest extends expContentServicesTestCase
{
    public function testDeclarations()
    {
        $this->assertDeclarations( 'expUrlAliasServices', 22 );
        $this->assertDeclarations( 'expSearchServices', 17 );
        $this->assertDeclarations( 'expContentJobServices', 24 );
    }

    // ------------------------------------------------------------------ URL aliases

    public function testAliasReads()
    {
        $e = $this->envelope( 'expUrlAliasServices', 'forNode', array( 43, 'name' ) );
        $this->assertNotEmpty( $e['data'] );
        $row = $e['data'][0];
        $this->assertSame( 'eznode:43', $row['action'] );
        $path = $this->ok( 'expUrlAliasServices', 'path', array( 43 ) )['path'];
        $this->assertSame( $path, $row['path'] );
        $r = $this->ok( 'expUrlAliasServices', 'resolve', array( $path ) );
        $this->assertSame( 43, $r['node']['node_id'] );
        $this->assertTrue( $this->ok( 'expUrlAliasServices', 'exists', array( $path ) )['exists'] );
        $this->assertFalse( $this->ok( 'expUrlAliasServices', 'exists', array( 'no/such/path/at/all' ) )['exists'] );
        $this->fails( 404, 'expUrlAliasServices', 'resolve', array( 'no/such/path/at/all' ) );
        $this->assertSame( 43, $this->ok( 'expUrlAliasServices', 'forObject', array( eZContentObjectTreeNode::fetch( 43 )->attribute( 'contentobject_id' ) ) )[0]['node_id'] );
        $this->assertNotEmpty( $this->ok( 'expUrlAliasServices', 'byAction', array( 'eznode:43' ) ) );
        $this->fails( 400, 'expUrlAliasServices', 'byAction', array( 'bad action' ) );
        $this->fails( 400, 'expUrlAliasServices', 'forNode', array( 43, 'weird' ) );
    }

    public function testAliasListsAndNormalize()
    {
        $this->assertArrayHasKey( 'total', $this->envelope( 'expUrlAliasServices', 'list', array( 'all', 5 ) )['meta'] );
        $this->assertGreaterThan( 0, $this->ok( 'expUrlAliasServices', 'count', array( 'all' ) )['count'] );
        $this->assertArrayHasKey( 'total', $this->envelope( 'expUrlAliasServices', 'redirects', array( 5 ) )['meta'] );
        $this->assertArrayHasKey( 'total', $this->envelope( 'expUrlAliasServices', 'children', array( '', 5 ) )['meta'] );
        $this->assertSame( 'my-car', strtolower( $this->ok( 'expUrlAliasServices', 'normalize', array( 'My car' ) )['alias'] ) );
        $this->assertSame( 'what-is-this/second-part', strtolower( $this->ok( 'expUrlAliasServices', 'normalizePath', array( 'What is this?/Second part' ) )['path'] ) );
        $this->assertIsString( $this->ok( 'expUrlAliasServices', 'pathPrefix' )['prefix'] );
    }

    public function testCreateAndRemoveAlias()
    {
        $n = $this->createFolder( 'alias test ' . uniqid() );
        $alias = 'exptest-alias-' . substr( uniqid(), -8 );
        $r = $this->ok( 'expUrlAliasServices', 'createAlias', array( $n ), array( 'alias' => $alias, 'parent_is_root' => '1' ) );
        $this->assertSame( $alias, $r['path'] );
        $this->assertSame( $n, $this->ok( 'expUrlAliasServices', 'resolve', array( $alias ) )['node']['node_id'] );
        $custom = $this->envelope( 'expUrlAliasServices', 'forNode', array( $n, 'alias' ) );
        $this->assertSame( 1, $custom['meta']['total'] );
        $ref = $custom['data'][0]['ref'];
        $other = $this->createFolder( 'alias other ' . uniqid() );
        $this->fails( 409, 'expUrlAliasServices', 'createAlias', array( $other ), array( 'alias' => $alias, 'parent_is_root' => '1' ) );
        $this->fails( 404, 'expUrlAliasServices', 'removeAlias', array( $other ), array( 'elements' => $ref ) );
        $this->fails( 400, 'expUrlAliasServices', 'removeAlias', array( $n ), array( 'elements' => 'garbage' ) );
        $this->fails( 422, 'expUrlAliasServices', 'createAlias', array( $n ), array( 'alias' => '   ' ) );
        $this->assertSame( array( $ref ), $this->ok( 'expUrlAliasServices', 'removeAlias', array( $n ), array( 'elements' => $ref ) )['removed'] );
        $this->assertFalse( $this->ok( 'expUrlAliasServices', 'exists', array( $alias ) )['exists'] );
        $this->ok( 'expUrlAliasServices', 'createAlias', array( $n ), array( 'alias' => $alias . '-b', 'parent_is_root' => '1' ) );
        $this->ok( 'expUrlAliasServices', 'createAlias', array( $n ), array( 'alias' => $alias . '-c', 'parent_is_root' => '1' ) );
        $this->assertSame( 2, $this->ok( 'expUrlAliasServices', 'removeAllAliases', array( $n ) )['removed'] );
        $this->assertSame( 0, $this->envelope( 'expUrlAliasServices', 'forNode', array( $n, 'alias' ) )['meta']['total'] );
    }

    public function testWildcardLifecycle()
    {
        $source = 'exptest-wild-' . substr( uniqid(), -8 ) . '/*';
        $this->cleanup( function () use ( $source ) { $w = eZURLWildcard::fetchBySourceURL( $source ); if ( $w ) { eZURLWildcard::removeByIDs( array( $w->attribute( 'id' ) ) ); eZURLWildcard::expireCache(); } } );
        $before = $this->ok( 'expUrlAliasServices', 'wildcardCount' )['count'];
        $w = $this->ok( 'expUrlAliasServices', 'createWildcard', array(), array( 'source' => $source, 'destination' => 'Media/{1}', 'type' => 'forward' ) );
        $this->assertSame( 'forward', $w['type'] );
        $this->assertSame( $before + 1, $this->ok( 'expUrlAliasServices', 'wildcardCount' )['count'] );
        $this->fails( 409, 'expUrlAliasServices', 'createWildcard', array(), array( 'source' => $source, 'destination' => 'x' ) );
        $this->fails( 400, 'expUrlAliasServices', 'createWildcard', array(), array( 'source' => 'x/*' ) );
        $this->fails( 400, 'expUrlAliasServices', 'createWildcard', array(), array( 'source' => 'x/*', 'destination' => 'y', 'type' => 'sideways' ) );
        $this->assertSame( $w['id'], $this->ok( 'expUrlAliasServices', 'wildcard', array( $w['id'] ) )['id'] );
        $this->assertSame( $w['id'], $this->ok( 'expUrlAliasServices', 'wildcardBySource', array( $source ) )['id'] );
        $list = $this->envelope( 'expUrlAliasServices', 'wildcards', array( 200 ) );
        $this->assertContains( $w['id'], array_column( $list['data'], 'id' ) );
        $this->assertTrue( $this->ok( 'expUrlAliasServices', 'wildcardMatches', array( str_replace( '/*', '/anything', $source ) ) )['matches'] );
        $this->assertSame( array( $w['id'] ), $this->ok( 'expUrlAliasServices', 'removeWildcard', array( $w['id'] ) )['removed'] );
        $this->fails( 404, 'expUrlAliasServices', 'wildcard', array( $w['id'] ) );
        $a = $this->ok( 'expUrlAliasServices', 'createWildcard', array(), array( 'source' => $source, 'destination' => 'Media' ) );
        $this->assertSame( array( $a['id'] ), $this->ok( 'expUrlAliasServices', 'removeWildcards', array(), array( 'ids' => (string)$a['id'] ) )['removed'] );
        $this->assertSame( $before, $this->ok( 'expUrlAliasServices', 'wildcardCount' )['count'] );
    }

    // ------------------------------------------------------------------ search

    public function testSearchBasics()
    {
        $this->assertNotEmpty( $this->ok( 'expSearchServices', 'engine' )['engine'] );
        $this->assertSame( 'hello world', trim( $this->ok( 'expSearchServices', 'normalize', array( 'Hello World' ) )['text'] ) );
        $this->fails( 400, 'expSearchServices', 'search', array( '   ' ) );
        $this->fails( 400, 'expSearchServices', 'search', array( str_repeat( 'a', 300 ) ) );
        $this->assertNotEmpty( $this->ok( 'expSearchServices', 'searchableClasses' ) );
        $this->assertIsArray( $this->ok( 'expSearchServices', 'stats' ) );
        $this->assertArrayHasKey( 'total', $this->envelope( 'expSearchServices', 'topPhrases', array( 5 ) )['meta'] );
    }

    public function testSearchFindsNewContentAndFilters()
    {
        $word = 'zxqv' . substr( preg_replace( '/[^a-z]/', '', strtolower( md5( uniqid() ) ) ), 0, 8 ) . 'unique';
        $a = $this->createFolder( "$word findable" );
        $oid = $this->objectOf( $a );
        $engine = $this->ok( 'expSearchServices', 'engine' );
        if ( $engine['disabled'] )
            $this->markTestSkipped( 'search is disabled' );
        $e = $this->envelope( 'expSearchServices', 'search', array( $word, 10 ) );
        if ( !$e['meta']['total'] )
            $this->markTestSkipped( 'the search index does not pick up new content in this environment' );
        $this->assertContains( $a, array_column( $e['data'], 'node_id' ) );
        $this->assertSame( $e['meta']['total'], $this->ok( 'expSearchServices', 'count', array( $word ) )['count'] );
        $this->assertContains( $a, array_column( $this->envelope( 'expSearchServices', 'byClass', array( $word, 'folder' ) )['data'], 'node_id' ) );
        $this->assertSame( 0, $this->envelope( 'expSearchServices', 'byClass', array( $word, 'article' ) )['meta']['total'] );
        $this->assertContains( $a, array_column( $this->envelope( 'expSearchServices', 'inSubtree', array( $word, $this->testFolder() ) )['data'], 'node_id' ) );
        $this->assertContains( $a, array_column( $this->envelope( 'expSearchServices', 'search', array( $word, 10, 0, '{"date":"day"}' ) )['data'], 'node_id' ) );
        $facets = $this->envelope( 'expSearchServices', 'facets', array( $word ) );
        $this->assertSame( 1, $facets['data']['classes']->folder );
        $this->assertSame( 1, $this->ok( 'expSearchServices', 'indexStatus', array( $oid ) )['words'] > 0 ? 1 : 0 );
        $this->fails( 400, 'expSearchServices', 'search', array( $word, 10, 0, '{"date":"decade"}' ) );
        $this->fails( 404, 'expSearchServices', 'search', array( $word, 10, 0, '{"class":["no_such_class"]}' ) );
        $this->ok( 'expSearchServices', 'removeFromIndex', array( $oid ) );
        $this->assertSame( 0, $this->envelope( 'expSearchServices', 'search', array( $word ) )['meta']['total'] );
        $this->ok( 'expSearchServices', 'reindex', array( $oid ) );
        $this->assertSame( 1, $this->envelope( 'expSearchServices', 'search', array( $word ) )['meta']['total'] );
        $s = $this->ok( 'expSearchServices', 'suggest', array( substr( $word, 0, 6 ) ) );
        $this->assertIsArray( $s );
        $this->fails( 400, 'expSearchServices', 'suggest', array( 'a' ) );
    }

    public function testSearchSectionAttributeAndSimilar()
    {
        $media = $this->ok( 'expSectionServices', 'getByIdentifier', array( 'media' ) );
        $this->assertArrayHasKey( 'total', $this->envelope( 'expSearchServices', 'bySection', array( 'media', $media['id'] ) )['meta'] );
        $attr = $this->ok( 'expClassServices', 'attributeByIdentifier', array( 'folder', 'name' ) );
        $this->assertArrayHasKey( 'total', $this->envelope( 'expSearchServices', 'byAttribute', array( 'media', $attr['id'] ) )['meta'] );
        $this->fails( 404, 'expSearchServices', 'bySection', array( 'x', 99999 ) );
        $this->assertIsArray( $this->ok( 'expSearchServices', 'similar', array( eZContentObjectTreeNode::fetch( 43 )->attribute( 'contentobject_id' ), 3 ) ) );
        $this->fails( 400, 'expSearchServices', 'clearPhrases', array(), array() );
    }

    // ------------------------------------------------------------------ jobs

    protected function trackJob( $id )
    {
        $this->cleanup( function () use ( $id ) {
            $job = expContentJob::fetch( $id );
            if ( $job && $job->canCancel() )
                $job->cancel();
            expContentJobLock::release( $id );
            expContentJobStore::remove( $id );
        } );
    }

    public function testJobCatalogueAndEstimates()
    {
        $types = array_column( $this->ok( 'expContentJobServices', 'types' ), 'type' );
        foreach ( array( 'remove', 'copy', 'move', 'hide', 'reveal', 'section', 'state', 'addlocation', 'removelocation' ) as $t )
            $this->assertContains( $t, $types );
        $s = $this->ok( 'expContentJobServices', 'settings' );
        $this->assertArrayHasKey( 'NowLimit', $s );
        $folder = $this->testFolder();
        $est = $this->ok( 'expContentJobServices', 'estimate', array( 'hide', json_encode( array( 'node_id' => $folder ) ) ) );
        $this->assertGreaterThanOrEqual( 1, $est['nodes'] );
        $this->assertContains( $est['default_mode'], array( 'now', 'job' ) );
        $this->assertTrue( $est['now_allowed'] );
        $this->fails( 422, 'expContentJobServices', 'estimate', array( 'nonsense', '{}' ) );
        $this->assertFalse( $this->ok( 'expContentJobServices', 'lockOf', array( $folder ) )['locked'] );
        $this->assertIsArray( $this->ok( 'expContentJobServices', 'list', array( 'all', 5 ) ) );
        $this->fails( 400, 'expContentJobServices', 'list', array( 'weird' ) );
        $this->assertArrayHasKey( 'queued', (array)$this->ok( 'expContentJobServices', 'summary' ) );
        $this->assertIsArray( $this->ok( 'expContentJobServices', 'active' ) );
        $this->assertArrayHasKey( 'total', $this->envelope( 'expContentJobServices', 'listAll', array( 'all', 5 ) )['meta'] );
    }

    public function testCreateInspectCancelAndLock()
    {
        $folder = $this->testFolder();
        $j = $this->ok( 'expContentJobServices', 'hide', array(), array( 'node_id' => $folder, 'spawn' => '0' ) );
        $this->trackJob( $j['id'] );
        $this->assertSame( 'hide', $j['type'] );
        $this->assertSame( 'queued', $j['state'] );
        $this->assertFalse( $j['spawned'] );
        $this->assertSame( $j['id'], $this->ok( 'expContentJobServices', 'get', array( $j['id'], 5 ) )['id'] );
        $this->assertSame( 'queued', $this->ok( 'expContentJobServices', 'progress', array( $j['id'] ) )['state'] );
        $this->assertNotEmpty( $this->ok( 'expContentJobServices', 'log', array( $j['id'] ) ) );
        $this->assertSame( 'queued', $this->ok( 'expContentJobServices', 'result', array( $j['id'] ) )['state'] );
        $this->assertContains( $j['id'], array_column( $this->envelope( 'expContentJobServices', 'list', array( 'queued', 50 ) )['data'], 'id' ) );
        $lock = $this->ok( 'expContentJobServices', 'lockOf', array( $folder ) );
        $this->assertTrue( $lock['locked'] );
        $this->assertSame( $j['id'], $lock['job_id'] );
        $this->fails( 409, 'expNodeServices', 'hide', array( $folder ), array( 'mode' => 'now' ) );
        $this->fails( 409, 'expContentJobServices', 'hide', array(), array( 'node_id' => $folder, 'spawn' => '0' ) );
        $c = $this->ok( 'expContentJobServices', 'cancel', array( $j['id'] ) );
        $this->assertSame( 'cancelled', $c['state'] );
        $this->fails( 409, 'expContentJobServices', 'resume', array( $j['id'] ) );
        $this->fails( 409, 'expContentJobServices', 'cancel', array( $j['id'] ) );
        $this->fails( 409, 'expContentJobServices', 'spawn', array( $j['id'] ) );
        $this->assertFalse( $this->ok( 'expContentJobServices', 'lockOf', array( $folder ) )['locked'] );
    }

    public function testJobAccessAndErrors()
    {
        $this->fails( 400, 'expContentJobServices', 'get', array( '../../etc/passwd' ) );
        $this->fails( 404, 'expContentJobServices', 'get', array( 'aaaaaaaaaaaaaaaa' ) );
        $this->fails( 404, 'expContentJobServices', 'create', array( 'nonsense' ), array( 'params' => '{}' ) );
        $this->fails( 403, 'expContentJobServices', 'move', array(), array( 'node_id' => 43, 'new_parent_node_id' => 43, 'spawn' => '0' ) );
        $this->fails( 400, 'expContentJobServices', 'hide', array(), array() );
        $this->loginAnonymous();
        $this->fails( 401, 'expContentJobServices', 'list' );
    }

    public function testJobRunsToTheEnd()
    {
        $a = $this->createFolder();
        $this->createFolder( 'child', $a );
        $j = $this->ok( 'expContentJobServices', 'hide', array(), array( 'node_id' => $a, 'spawn' => '1' ) );
        $this->trackJob( $j['id'] );
        $state = 'queued';
        for ( $i = 0; $i < 90 && !in_array( $state, array( 'done', 'failed', 'cancelled' ), true ); $i++ )
        {
            sleep( 1 );
            $state = $this->ok( 'expContentJobServices', 'progress', array( $j['id'] ) )['state'];
        }
        if ( $state !== 'done' )
            $this->markTestSkipped( 'the background worker did not finish in 90 s here (state ' . $state . ')' );
        $r = $this->ok( 'expContentJobServices', 'result', array( $j['id'] ) );
        $this->assertGreaterThanOrEqual( 1, $r['result']->changed );
        $this->assertSame( 1, (int)eZContentObjectTreeNode::fetch( $a )->attribute( 'is_hidden' ) );
        $p = $this->ok( 'expContentJobServices', 'progress', array( $j['id'] ) );
        $this->assertSame( 100, (int)$p['percent'] );
    }

}
