<?php
require_once __DIR__ . '/../media/expMediaTestCase.php';

/** explayout: layouts, zones, blocks, rules and the resolver; drafts are made and discarded, rules toggled and put back. */
class expLayoutServicesTest extends expMediaTestCase
{
    protected function aLayout( $shared = false )
    {
        foreach ( $this->ok( 'expLayoutServices', 'layouts', array( 100, 0 ) )['data'] as $l )
            if ( $l['shared'] === $shared )
                return $l;
        $this->markTestSkipped( 'no such layout' );
    }

    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expLayoutServices', 27 );
    }

    public function testAvailableCountsLayouts()
    {
        $r = $this->ok( 'expLayoutServices', 'available' )['data'];
        $this->assertTrue( $r['available'] );
        $this->assertGreaterThan( 0, $r['layouts'] );
    }

    public function testLayoutsArePublishedAndPaged()
    {
        $r = $this->ok( 'expLayoutServices', 'layouts', array( 5, 0 ) );
        $this->assertPaged( $r );
        foreach ( $r['data'] as $l )
            $this->assertSame( 'published', $l['status_name'] );
    }

    public function testSharedAndDrafts()
    {
        foreach ( $this->ok( 'expLayoutServices', 'shared' )['data'] as $l )
            $this->assertTrue( $l['shared'] );
        $this->assertIsArray( $this->ok( 'expLayoutServices', 'drafts' )['data'] );
    }

    public function testViewHasZones()
    {
        $l = $this->aLayout();
        $v = $this->ok( 'expLayoutServices', 'view', array( $l['id'] ) )['data'];
        $this->assertSame( $l['identifier'], $v['identifier'] );
        $this->assertNotEmpty( $v['zones'] );
    }

    public function testMissingLayoutIs404()
    {
        $this->fails( 404, 'expLayoutServices', 'view', array( 99999999 ) );
    }

    public function testByIdentifier()
    {
        $l = $this->aLayout();
        $this->assertSame( $l['id'], $this->ok( 'expLayoutServices', 'byidentifier', array( $l['identifier'] ) )['data']['id'] );
        $this->fails( 404, 'expLayoutServices', 'byidentifier', array( 'no-such-layout' ) );
    }

    public function testTypesAndBlockTypes()
    {
        $types = $this->ok( 'expLayoutServices', 'types' )['data'];
        $this->assertContains( '1_column', array_column( $types, 'identifier' ) );
        $blocks = $this->ok( 'expLayoutServices', 'blocktypes' )['data'];
        $this->assertContains( 'text', array_column( $blocks, 'identifier' ) );
        foreach ( $this->ok( 'expLayoutServices', 'blocktypes', array( 'containers' ) )['data'] as $b )
            $this->assertSame( 'containers', $b['category'] );
    }

    public function testZonesAndOneZone()
    {
        $l = $this->aLayout();
        $zones = $this->ok( 'expLayoutServices', 'zones', array( $l['id'] ) )['data'];
        $this->assertNotEmpty( $zones );
        $z = $this->ok( 'expLayoutServices', 'zone', array( $l['id'], $zones[0]['identifier'] ) )['data'];
        $this->assertSame( $zones[0]['id'], $z['id'] );
        $this->fails( 404, 'expLayoutServices', 'zone', array( $l['id'], 'no_zone' ) );
    }

    public function testBlocksOfAZoneAndOneBlock()
    {
        foreach ( $this->ok( 'expLayoutServices', 'layouts', array( 100, 0 ) )['data'] as $l )
            foreach ( $this->ok( 'expLayoutServices', 'zones', array( $l['id'] ) )['data'] as $z )
            {
                $blocks = $this->ok( 'expLayoutServices', 'blocks', array( $l['id'], $z['identifier'] ) );
                if ( !$blocks['data'] )
                    continue;
                $b = $this->ok( 'expLayoutServices', 'block', array( $blocks['data'][0]['id'] ) )['data'];
                $this->assertSame( $blocks['data'][0]['id'], $b['id'] );
                $this->assertIsObject( $this->ok( 'expLayoutServices', 'blockparameters', array( $b['id'] ) )['data'] );
                $this->assertIsArray( $this->ok( 'expLayoutServices', 'blockchildren', array( $b['id'] ) )['data'] );
                return;
            }
        $this->markTestSkipped( 'no layout has blocks' );
    }

    public function testLinkedZoneAnswersTheSharedBlocks()
    {
        $links = $this->ok( 'expLayoutServices', 'linkedzones' )['data'];
        if ( !$links )
            $this->markTestSkipped( 'no linked zones' );
        $z = $links[0];
        $layout = $this->ok( 'expLayoutServices', 'view', array( $z['layout_id'] ) )['data'];
        $r = $this->ok( 'expLayoutServices', 'blocks', array( $z['layout_id'], $z['zone'] ) );
        $this->assertTrue( $r['meta']['linked'] );
        $this->assertSame( $z['linked_layout_id'], $r['meta']['source_layout_id'] );
    }

    public function testMissingBlockIs404()
    {
        $this->fails( 404, 'expLayoutServices', 'block', array( 99999999 ) );
    }

    public function testRulesAreOrderedAndHaveTargets()
    {
        $r = $this->ok( 'expLayoutServices', 'rules', array( 50, 0 ) );
        $this->assertPaged( $r );
        $this->assertNotEmpty( $r['data'] );
        $this->assertArrayHasKey( 'targets', $r['data'][0] );
        $one = $this->ok( 'expLayoutServices', 'rule', array( $r['data'][0]['id'] ) )['data'];
        $this->assertSame( $r['data'][0]['layout_id'], $one['layout_id'] );
        $this->fails( 404, 'expLayoutServices', 'rule', array( 99999999 ) );
    }

    public function testRulesForALayout()
    {
        $rules = $this->ok( 'expLayoutServices', 'rules', array( 50, 0 ) )['data'];
        $layoutId = $rules[0]['layout_id'];
        foreach ( $this->ok( 'expLayoutServices', 'rulesfor', array( $layoutId ) )['data'] as $rule )
            $this->assertSame( $layoutId, $rule['layout_id'] );
    }

    public function testResolveANode()
    {
        $r = $this->ok( 'expLayoutServices', 'resolve', array( 2 ) )['data'];
        $this->assertSame( 2, $r['node'] );
        $this->assertArrayHasKey( 'layout', $r );
        $this->assertNotNull( $r['layout'] );
    }

    public function testResolveAPathDoesNotTouchTheResolverCache()
    {
        $dir = eZINI::instance( 'site.ini' )->variable( 'FileSettings', 'VarDir' ) . '/cache/explayouts/resolver';
        $before = glob( $dir . '/*.php' ) ?: array();
        $this->ok( 'expLayoutServices', 'resolvepath', array( 'a/path/that/matches/nothing-' . uniqid() ) );
        $this->assertCount( count( $before ), glob( $dir . '/*.php' ) ?: array() );
    }

    public function testResolvePathRefusesControlCharacters()
    {
        $this->fails( 400, 'expLayoutServices', 'resolvepath', array( "a\x01b" ) );
    }

    public function testStats()
    {
        $s = $this->ok( 'expLayoutServices', 'stats' )['data'];
        $this->assertGreaterThan( 0, $s['layouts'] );
        $this->assertGreaterThan( 0, $s['blocks'] );
        $this->assertNotEmpty( $s['blocks_per_definition'] );
    }

    public function testWritesNeedPost()
    {
        $l = $this->aLayout();
        $this->fails( 403, 'expLayoutServices', 'createdraft', array( $l['id'] ) );
        $this->fails( 403, 'expLayoutServices', 'publish', array( $l['id'] ) );
        $this->fails( 403, 'expLayoutServices', 'clearcache' );
    }

    public function testPublishWithoutDraftIsConflict()
    {
        $l = $this->aLayout();
        if ( expLayoutsLayout::fetchByIdentifier( $l['identifier'], 1 ) )
            $this->markTestSkipped( 'the layout has a draft of somebody' );
        $this->fails( 409, 'expLayoutServices', 'publish', array( $l['id'] ), array( 'confirm' => '1' ) );
        $this->fails( 409, 'expLayoutServices', 'discard', array( $l['id'] ), array( 'confirm' => '1' ) );
    }

    public function testCreateDraftAndDiscardItAgain()
    {
        $l = $this->aLayout();
        if ( expLayoutsLayout::fetchByIdentifier( $l['identifier'], 1 ) )
            $this->markTestSkipped( 'the layout has a draft of somebody' );
        $identifier = $l['identifier'];
        $this->cleanups[] = function () use ( $identifier ) {
            $d = expLayoutsLayout::fetchByIdentifier( $identifier, 1 );
            if ( $d )
                ( new expLayoutsCoreLayoutService() )->discard( (int)$d->attribute( 'id' ) );
        };
        $draft = $this->ok( 'expLayoutServices', 'createdraft', array( $l['id'] ), array( 'confirm' => '1' ) )['data'];
        $this->assertSame( 1, $draft['status'] );
        $this->assertSame( $identifier, $draft['identifier'] );
        $this->assertContains( $draft['id'], array_column( $this->ok( 'expLayoutServices', 'drafts' )['data'], 'id' ) );
        $zones = $this->ok( 'expLayoutServices', 'zones', array( $draft['id'] ) )['data'];
        $this->assertCount( count( $this->ok( 'expLayoutServices', 'zones', array( $l['id'] ) )['data'] ), $zones );
        $this->ok( 'expLayoutServices', 'discard', array( $l['id'] ), array( 'confirm' => '1' ) );
        $this->assertFalse( (bool)expLayoutsLayout::fetchByIdentifier( $identifier, 1 ) );
    }

    public function testRuleToggleIsPutBack()
    {
        $rules = $this->ok( 'expLayoutServices', 'rules', array( 50, 0 ) )['data'];
        $rule = $rules[0];
        $id = $rule['id'];
        $was = $rule['enabled'];
        $this->cleanups[] = function () use ( $id, $was ) {
            $r = expLayoutsRule::fetch( $id );
            $r->setAttribute( 'enabled', $was ? 1 : 0 );
            $r->store();
            expLayoutsResolver::clearCache();
        };
        $off = $this->ok( 'expLayoutServices', 'disablerule', array( $id ), array( 'confirm' => '1' ) )['data'];
        $this->assertFalse( $off['enabled'] );
        $on = $this->ok( 'expLayoutServices', 'enablerule', array( $id ), array( 'confirm' => '1' ) )['data'];
        $this->assertTrue( $on['enabled'] );
    }
}
