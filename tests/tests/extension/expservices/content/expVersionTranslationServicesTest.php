<?php
/**
 * Version and translation services on test content under the Media root.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expContentServicesTestCase.php';

class expVersionTranslationServicesTest extends expContentServicesTestCase
{
    protected function mediaObject()
    {
        return (int)eZContentObjectTreeNode::fetch( 43 )->attribute( 'contentobject_id' );
    }

    public function testDeclarations()
    {
        $this->assertDeclarations( 'expVersionServices', 22 );
        $this->assertDeclarations( 'expTranslationServices', 17 );
    }

    public function testVersionReads()
    {
        $id = $this->mediaObject();
        $e = $this->envelope( 'expVersionServices', 'list', array( $id, 5 ) );
        $this->assertNotEmpty( $e['data'] );
        $this->assertGreaterThanOrEqual( 1, $e['meta']['total'] );
        $cur = $this->ok( 'expVersionServices', 'current', array( $id ) );
        $this->assertSame( 'published', $cur['status_name'] );
        $this->assertSame( $cur['version'], $this->ok( 'expVersionServices', 'published', array( $id ) )['version'] );
        $this->assertSame( $cur['version'], $this->ok( 'expVersionServices', 'get', array( $id, $cur['version'] ) )['version'] );
        $this->assertSame( 'published', $this->ok( 'expVersionServices', 'status', array( $id, $cur['version'] ) )['status_name'] );
        $this->assertGreaterThanOrEqual( 1, $this->ok( 'expVersionServices', 'count', array( $id ) )['count'] );
        $this->assertArrayHasKey( 'name', $this->ok( 'expVersionServices', 'creator', array( $id, $cur['version'] ) ) );
        $this->assertArrayHasKey( 'name', $this->ok( 'expVersionServices', 'dataMap', array( $id, $cur['version'] ) ) );
        $this->assertNotEmpty( $this->ok( 'expVersionServices', 'translations', array( $id, $cur['version'] ) ) );
        $this->assertIsArray( $this->ok( 'expVersionServices', 'nodeAssignments', array( $id, $cur['version'] ) ) );
        $this->assertStringContainsString( 'content/versionview/' . $id, $this->ok( 'expVersionServices', 'viewUrl', array( $id, $cur['version'] ) )['url'] );
        $this->fails( 404, 'expVersionServices', 'get', array( $id, 9999 ) );
        $this->fails( 400, 'expVersionServices', 'list', array( $id, 5, 0, 'bogus' ) );
    }

    public function testDraftListsAndPending()
    {
        $this->assertIsArray( $this->ok( 'expVersionServices', 'drafts', array( $this->mediaObject() ) ) );
        $this->assertArrayHasKey( 'total', $this->envelope( 'expVersionServices', 'myDrafts' )['meta'] );
        $this->assertArrayHasKey( 'total', $this->envelope( 'expVersionServices', 'allDrafts' )['meta'] );
        $this->assertArrayHasKey( 'total', $this->envelope( 'expVersionServices', 'pending' )['meta'] );
    }

    public function testDraftCreateCompareHasConflictsPublishAndDiscard()
    {
        $n = $this->createFolder( 'versions ' . uniqid() );
        $id = $this->objectOf( $n );
        $d = $this->ok( 'expVersionServices', 'createDraft', array( $id ) );
        $this->assertSame( 'draft', $d['status_name'] );
        $this->assertContains( $d['version'], array_column( $this->ok( 'expVersionServices', 'drafts', array( $id ) ), 'version' ) );
        $this->ok( 'expAttributeServices', 'setDraft', array( $id, $d['version'], 'name' ), array( 'value' => 'Second version' ) );
        $cmp = $this->envelope( 'expVersionServices', 'compare', array( $id, 1, $d['version'] ) );
        $this->assertTrue( $cmp['data']['name']['changed'] );
        $this->assertSame( 'Second version', $cmp['data']['name']['b'] );
        $this->assertGreaterThanOrEqual( 1, $cmp['meta']['changed'] );
        $this->assertIsBool( $this->ok( 'expVersionServices', 'hasConflicts', array( $id, $d['version'] ) )['conflicts'] );
        $pub = $this->ok( 'expVersionServices', 'publish', array( $id, $d['version'] ) );
        $this->assertSame( 'Second version', $pub['name'] );
        $this->assertSame( $d['version'], $pub['current_version'] );
        $this->assertSame( 'archived', $this->ok( 'expVersionServices', 'status', array( $id, 1 ) )['status_name'] );
        $d2 = $this->ok( 'expVersionServices', 'createDraft', array( $id ) );
        $this->assertSame( $d2['version'], $this->ok( 'expVersionServices', 'discard', array( $id, $d2['version'] ) )['discarded'] );
        eZContentObject::clearCache();
        $this->fails( 404, 'expVersionServices', 'get', array( $id, $d2['version'] ) );
        $this->fails( 409, 'expVersionServices', 'discard', array( $id, $d['version'] ) );
    }

    public function testRevertRemoveAndRemoveArchived()
    {
        $n = $this->createFolder( 'revert ' . uniqid() );
        $id = $this->objectOf( $n );
        for ( $i = 2; $i <= 3; $i++ )
            $this->ok( 'expAttributeServices', 'set', array( $id, 'name' ), array( 'value' => "Name $i" ) );
        $current = $this->ok( 'expVersionServices', 'current', array( $id ) )['version'];
        $this->assertGreaterThanOrEqual( 3, $current );
        $this->fails( 409, 'expVersionServices', 'remove', array( $id, $current ) );
        $rev = $this->ok( 'expVersionServices', 'revert', array( $id, 1 ) );
        $this->assertSame( 'draft', $rev['status_name'] );
        $pub = $this->ok( 'expVersionServices', 'publish', array( $id, $rev['version'] ) );
        $this->assertNotSame( 'Name 3', $pub['name'] );
        $this->assertSame( 'archived', $this->ok( 'expVersionServices', 'status', array( $id, 2 ) )['status_name'] );
        $this->assertSame( 2, $this->ok( 'expVersionServices', 'remove', array( $id, 2 ) )['removed'] );
        $r = $this->ok( 'expVersionServices', 'removeArchived', array( $id, 0 ) );
        $this->assertNotEmpty( $r['removed'] );
    }

    public function testCleanupDraftsNeedsOnlyHours()
    {
        $r = $this->ok( 'expVersionServices', 'cleanupDrafts', array( 24 * 365 * 20 ) );
        $this->assertSame( 0, $r['cleaned'] );
    }

    public function testSiteLanguages()
    {
        $langs = $this->ok( 'expTranslationServices', 'languages' );
        $this->assertContains( 'eng-US', array_column( $langs, 'locale' ) );
        $this->assertSame( 'eng-US', $this->ok( 'expTranslationServices', 'language', array( 'eng-US' ) )['locale'] );
        $this->fails( 404, 'expTranslationServices', 'language', array( 'xxx-XX' ) );
        $this->assertNotEmpty( $this->ok( 'expTranslationServices', 'prioritized' ) );
        $this->assertArrayHasKey( 'locale', $this->ok( 'expTranslationServices', 'topPriority' ) );
        $stats = $this->ok( 'expTranslationServices', 'stats' );
        $this->assertArrayHasKey( 'objects', $stats[0] );
        $e = $this->envelope( 'expTranslationServices', 'knownLocales', array( 'ger', 5 ) );
        $this->assertContains( 'ger-DE', array_column( $e['data'], 'locale' ) );
    }

    public function testObjectTranslationReads()
    {
        $id = $this->mediaObject();
        $t = $this->ok( 'expTranslationServices', 'ofObject', array( $id ) );
        $this->assertNotEmpty( $t );
        $this->assertTrue( in_array( true, array_column( $t, 'is_initial' ), true ) );
        $this->assertIsArray( $this->ok( 'expTranslationServices', 'missing', array( $id ) ) );
        $this->assertNotEmpty( $this->ok( 'expTranslationServices', 'content', array( $id, $t[0]['language'] ) ) );
        $this->fails( 404, 'expTranslationServices', 'content', array( $id, 'jpn-JP' ) );
        $this->assertNotEmpty( (array)$this->ok( 'expTranslationServices', 'names', array( $id ) ) );
        $this->assertNotEmpty( (array)$this->ok( 'expTranslationServices', 'nodeNames', array( 43 ) ) );
        $this->assertNotEmpty( (array)$this->ok( 'expTranslationServices', 'classNames', array( 'folder' ) ) );
        $this->assertTrue( $this->ok( 'expTranslationServices', 'canTranslate', array( $id ) )['can_translate'] );
        $st = $this->envelope( 'expTranslationServices', 'status', array( 43 ) );
        $this->assertArrayHasKey( 'eng-US', $st['data'] );
    }

    public function testTranslateCopyAndRemoveLanguage()
    {
        $n = $this->createFolder( 'translate ' . uniqid() );
        $id = $this->objectOf( $n );
        $o = $this->ok( 'expTranslationServices', 'translate', array( $id, 'ger-DE' ), array( 'attributes' => '{"name":"Deutscher Titel"}' ) );
        $this->assertContains( 'ger-DE', $o['languages'] );
        $this->assertSame( 'Deutscher Titel', $this->ok( 'expAttributeServices', 'value', array( $id, 'name', 'ger-DE' ) ) );
        $this->assertContains( 'ger-DE', array_column( $this->ok( 'expTranslationServices', 'ofObject', array( $id ) ), 'language' ) );
        $this->assertNotContains( 'ger-DE', array_column( $this->ok( 'expTranslationServices', 'missing', array( $id ) ), 'locale' ) );
        $this->fails( 409, 'expTranslationServices', 'remove', array( $id, 'eng-US' ) );
        $removed = $this->ok( 'expTranslationServices', 'remove', array( $id, 'ger-DE' ) );
        $this->assertNotContains( 'ger-DE', $removed['languages'] );
        $this->fails( 404, 'expTranslationServices', 'remove', array( $id, 'ger-DE' ) );
        $c = $this->ok( 'expTranslationServices', 'copyLanguage', array( $id, 'eng-US', 'ger-DE' ) );
        $this->assertContains( 'ger-DE', $c['languages'] );
        $this->fails( 422, 'expTranslationServices', 'copyLanguage', array( $id, 'eng-US', 'eng-US' ) );
        $this->fails( 400, 'expTranslationServices', 'translate', array( $id, 'ger-DE' ), array() );
    }

    public function testAddAndRemoveSiteLanguage()
    {
        $locale = 'jpn-JP';
        if ( eZContentLanguage::fetchByLocale( $locale ) )
            $this->markTestSkipped( "$locale is a site language here" );
        $this->cleanup( function () use ( $locale ) { $l = eZContentLanguage::fetchByLocale( $locale ); if ( $l ) { $l->removeThis(); } eZContentLanguage::expireCache(); } );
        $this->fails( 400, 'expTranslationServices', 'addLanguage', array( 'japanese' ) );
        $l = $this->ok( 'expTranslationServices', 'addLanguage', array( $locale ) );
        $this->assertSame( $locale, $l['locale'] );
        $this->fails( 409, 'expTranslationServices', 'addLanguage', array( $locale ) );
        $this->assertSame( $locale, $this->ok( 'expTranslationServices', 'removeLanguage', array( $locale ) )['removed'] );
        $this->fails( 404, 'expTranslationServices', 'removeLanguage', array( $locale ) );
    }
}
