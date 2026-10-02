<?php
/**
 * The subitems server functions without a database: what rows computes, the columns response,
 * the per-user preference and the default cell rendering of the base class.
 *
 *  SF-01 — only the requested, available, non-built-in columns are computed, once per row
 *  SF-02 — unknown, refused and built-in keys are ignored; an empty request computes nothing
 *  SF-03 — every item gets a columns object { key: { v, h } }, {} when nothing was asked
 *  SF-04 — a column that throws gives { v: null, h: "" }, the others go on
 *  SF-05 — the columns response: only available columns, defaults/presets/preference filtered
 *  SF-06 — preference merge: unknown keys dropped, preset ids, names, page size, per part
 *  SF-07 — preference decode of broken values, forPart fallback, the 4000 byte limit
 *  SF-08 — base class html()/text(): escaping, link, bool, list, date, html
 *  SF-09 — the server functions are registered in ezjscore.ini
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group subitems
 */

require_once __DIR__ . '/fixtures/expsubitemstesthelpers.php';

class expSubitemsServerFunctionsTest extends ezpTestCase
{
    private $granted = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        expSubitemsTestFixtures::warmUp();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->granted = array( 'content/read' => true );
        $granted = &$this->granted;
        expSubitemsColumnRegistry::setAccessChecker( function ( $module, $function ) use ( &$granted ) {
            return !empty( $granted["$module/$function"] );
        } );
        expSubitemsTestCountingColumn::$calls = array();
        expSubitemsTestFixtures::$handlerCalls = 0;
    }

    public function tearDown(): void
    {
        expSubitemsColumnRegistry::setAccessChecker( null );
        expSubitemsServerFunctions::$registry = null;
        parent::tearDown();
    }

    public function testRowsComputeOnlyRequestedColumns()
    {
        $r = expSubitemsTestFixtures::registry();
        $parent = expSubitemsTestFixtures::parent();
        $nodes = expSubitemsTestFixtures::children( 3 );

        $columns = $r->resolveColumns( 'classcol,name,secret,nosuch,handlercol', $parent );
        $this->assertSame( array( 'classcol', 'handlercol' ), array_keys( $columns ) );

        $values = expSubitemsServerFunctions::columnValues( $nodes, $columns );
        $this->assertCount( 3, $values );
        $this->assertSame( array( 'classcol' => 3 ), expSubitemsTestCountingColumn::$calls, 'secret never computed, classcol once per row' );
        $this->assertSame( 3, expSubitemsTestFixtures::$handlerCalls );

        $first = $values[0];
        $this->assertInstanceOf( 'stdClass', $first );
        $this->assertSame( array( 'classcol', 'handlercol' ), array_keys( get_object_vars( $first ) ) );
        $this->assertSame( array( 'v' => 'classcol:101', 'h' => 'classcol:101' ), $first->classcol );
        $this->assertSame( '<b>Handler column</b> #101', $first->handlercol['v'] );
        $this->assertSame( '&lt;b&gt;Handler column&lt;/b&gt; #101', $first->handlercol['h'], 'Type=text is escaped' );
        $this->assertFalse( property_exists( $first, 'name' ), 'built-ins are rendered by the browser' );
    }

    public function testNothingRequested()
    {
        $r = expSubitemsTestFixtures::registry();
        $values = expSubitemsServerFunctions::columnValues( expSubitemsTestFixtures::children( 2 ), $r->resolveColumns( '', expSubitemsTestFixtures::parent() ) );
        $this->assertSame( '{}', json_encode( $values[0] ), 'an empty object, not []' );
        $this->assertSame( array(), expSubitemsTestCountingColumn::$calls );
    }

    public function testRefusedKeyBecomesAvailableWithPolicy()
    {
        $r = expSubitemsTestFixtures::registry();
        $parent = expSubitemsTestFixtures::parent();
        $this->assertSame( array(), array_keys( $r->resolveColumns( 'secret', $parent ) ) );
        $this->granted['user/selfedit'] = true;
        $this->assertSame( array( 'secret' ), array_keys( $r->resolveColumns( 'secret', $parent ) ) );
    }

    public function testThrowingColumn()
    {
        $columns = array( 'bad' => new expSubitemsTestThrowingColumn( 'bad', array() ),
                          'good' => new expSubitemsTestCountingColumn( 'good', array() ) );
        $values = expSubitemsServerFunctions::columnValues( expSubitemsTestFixtures::children( 1 ), $columns );
        $this->assertSame( array( 'v' => null, 'h' => '' ), $values[0]->bad );
        $this->assertSame( 'good:101', $values[0]->good['v'] );
    }

    public function testColumnsResponse()
    {
        $r = expSubitemsTestFixtures::registry();
        $parent = expSubitemsTestFixtures::parent( 70, 'gallery' );
        $pref = expSubitemsPreference::blank();
        $pref['presets'] = array( 'mine' => array( 'name' => 'Mine', 'columns' => array( 'secret', 'nodeid' ) ) );

        $res = expSubitemsServerFunctions::columnsResponse( $parent, $r, $pref );
        $keys = array_map( function ( $c ) { return $c['key']; }, $res['columns'] );
        $this->assertContains( 'handlercol', $keys );
        $this->assertContains( 'nodeid', $keys );
        $this->assertNotContains( 'secret', $keys );
        $this->assertNotContains( 'broken', $keys );

        $this->assertSame( array( 'name', 'handlercol' ), $res['defaults'] );
        $this->assertSame( array( 'seo', 'tech', 'mine' ), array_map( function ( $p ) { return $p['id']; }, $res['presets'] ) );
        $this->assertSame( array( 'name', 'handlercol' ), $res['presets'][0]['columns'], 'secret filtered out of the INI preset' );
        $this->assertSame( 'ini', $res['presets'][0]['source'] );
        $this->assertSame( array( 'nodeid' ), $res['presets'][2]['columns'] );
        $this->assertSame( 'user', $res['presets'][2]['source'] );
        $this->assertSame( array( 'visible' => array( 'name', 'handlercol' ), 'preset' => null, 'page_size' => null, 'saved' => false ), $res['preference'] );
        $this->assertSame( array( 10, 20 ), $res['page_sizes'] );
        $this->assertTrue( $res['csv_export'] );
        $this->assertNotFalse( json_encode( $res ) );
    }

    public function testPreferenceMerge()
    {
        $r = expSubitemsTestFixtures::registry();
        $pref = expSubitemsPreference::merge( expSubitemsPreference::blank(), array(
            'visible' => array( 'name', 'nosuch', 'handlercol', 'name', 'attr:article/title', 'attr:article/nosuch', 42 ),
            'preset' => 'seo',
            'page_size' => '25',
            'presets' => array(
                'mine' => array( 'name' => '<b>My</b> columns', 'columns' => array( 'nodeid', 'nosuch' ) ),
                'seo' => array( 'name' => 'Takes an INI id', 'columns' => array( 'name' ) ),
                'Bad Id' => array( 'name' => 'x', 'columns' => array() ),
                'noname' => array( 'columns' => array( 'name' ) ),
            ),
        ), $r, 'ezcontentnavigationpart' );

        $this->assertSame( array( 'visible' => array( 'name', 'handlercol', 'attr:article/title' ), 'preset' => 'seo' ),
                           $pref['parts']['ezcontentnavigationpart'] );
        $this->assertSame( 25, $pref['page_size'] );
        $this->assertSame( array( 'mine', 'noname' ), array_keys( $pref['presets'] ) );
        $this->assertSame( 'My columns', $pref['presets']['mine']['name'] );
        $this->assertSame( array( 'nodeid' ), $pref['presets']['mine']['columns'] );
        $this->assertSame( 'noname', $pref['presets']['noname']['name'] );

        // another part keeps its own; presets kept when not sent; an unknown preset is cleared
        $pref = expSubitemsPreference::merge( $pref, array( 'visible' => array( 'thumbnail' ), 'preset' => 'gone' ), $r, 'ezmedianavigationpart' );
        $this->assertSame( array( 'name', 'handlercol', 'attr:article/title' ), $pref['parts']['ezcontentnavigationpart']['visible'] );
        $this->assertSame( array( 'visible' => array( 'thumbnail' ), 'preset' => null ), $pref['parts']['ezmedianavigationpart'] );
        $this->assertSame( array( 'mine', 'noname' ), array_keys( $pref['presets'] ) );

        // a user preset can be chosen
        $pref = expSubitemsPreference::merge( $pref, array( 'preset' => 'mine' ), $r, 'ezmedianavigationpart' );
        $this->assertSame( 'mine', $pref['parts']['ezmedianavigationpart']['preset'] );
        $this->assertSame( array( 'thumbnail' ), $pref['parts']['ezmedianavigationpart']['visible'] );

        foreach ( array( array( 'page_size' => 0 ), array( 'page_size' => 501 ), array( 'page_size' => 'x' ),
                         array( 'visible' => 'name' ), array( 'presets' => 'x' ) ) as $bad )
        {
            try
            {
                expSubitemsPreference::merge( $pref, $bad, $r, '*' );
                $this->fail( 'refused: ' . json_encode( $bad ) );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertNotSame( '', $e->getMessage() );
            }
        }
        $this->expectException( InvalidArgumentException::class );
        expSubitemsPreference::merge( $pref, array(), $r, 'bad part!' );
    }

    public function testPreferenceLimits()
    {
        $r = expSubitemsTestFixtures::registry();
        $presets = array();
        for ( $i = 0; $i < 30; $i++ )
            $presets["p$i"] = array( 'name' => str_repeat( 'n', 100 ), 'columns' => expSubitemsBuiltinColumn::keys() );
        $pref = expSubitemsPreference::merge( expSubitemsPreference::blank(), array( 'presets' => $presets ), $r, '*' );
        $this->assertCount( expSubitemsPreference::MAX_PRESETS, $pref['presets'] );
        $this->assertSame( expSubitemsPreference::MAX_PRESET_NAME, mb_strlen( $pref['presets']['p0']['name'] ) );
        $this->assertGreaterThan( expSubitemsPreference::MAX_BYTES, strlen( expSubitemsPreference::encode( $pref ) ) );
        try
        {
            expSubitemsPreference::store( $pref );
            $this->fail( 'too large' );
        }
        catch ( InvalidArgumentException $e )
        {
            $this->assertStringContainsString( 'too large', $e->getMessage() );
        }
    }

    public function testPreferenceDecodeAndForPart()
    {
        $this->assertSame( expSubitemsPreference::blank(), expSubitemsPreference::decode( false ) );
        $this->assertSame( expSubitemsPreference::blank(), expSubitemsPreference::decode( '{broken' ) );
        $this->assertSame( expSubitemsPreference::blank(), expSubitemsPreference::decode( '"string"' ) );

        $pref = expSubitemsPreference::decode( expSubitemsPreference::encode( array(
            'parts' => array( '*' => array( 'visible' => array( 'name' ), 'preset' => null ),
                              'ezmedianavigationpart' => array( 'visible' => array( 'thumbnail', 'name' ), 'preset' => 'seo' ) ),
            'page_size' => 50, 'presets' => array() ) ) );
        $this->assertSame( array( 'visible' => array( 'thumbnail', 'name' ), 'preset' => 'seo', 'page_size' => 50, 'saved' => true ),
                           expSubitemsPreference::forPart( $pref, 'ezmedianavigationpart', array( 'x' ) ) );
        $this->assertSame( array( 'visible' => array( 'name' ), 'preset' => null, 'page_size' => 50, 'saved' => true ),
                           expSubitemsPreference::forPart( $pref, 'ezcontentnavigationpart', array( 'x' ) ), '* is the fallback' );
        $blank = expSubitemsPreference::blank();
        $this->assertSame( array( 'visible' => array( 'x' ), 'preset' => null, 'page_size' => null, 'saved' => false ),
                           expSubitemsPreference::forPart( $blank, 'ezcontentnavigationpart', array( 'x' ) ) );
        $this->assertSame( '{"v":1,"parts":{},"page_size":null,"presets":{}}', expSubitemsPreference::encode( $blank ) );
        $this->assertSame( '*', expSubitemsPreference::navigationPart( null ) );
        $this->assertSame( '*', expSubitemsPreference::navigationPart( expSubitemsTestFixtures::parent() ), 'no object, no section' );
    }

    public function testBaseHtmlAndText()
    {
        $node = expSubitemsTestFixtures::parent();
        $text = new expSubitemsTestCountingColumn( 'x', array( 'Type' => 'text' ) );
        $this->assertSame( '&lt;script&gt;&quot;', $text->html( $node, '<script>"' ) );
        $this->assertSame( '', $text->html( $node, null ) );
        $this->assertSame( 'a, &lt;b&gt;', $text->html( $node, array( 'a', '<b>' ) ) );
        $this->assertSame( 'a b', $text->text( $node, "a\n  b" ) );
        $this->assertSame( 'a, b', $text->text( $node, array( 'a', 'b' ) ) );

        $link = new expSubitemsTestCountingColumn( 'x', array( 'Type' => 'link' ) );
        $this->assertSame( '<a href="/a?b=1&amp;c">/a?b=1&amp;c</a>', $link->html( $node, '/a?b=1&c' ) );
        $this->assertSame( 'javascript:alert(1)', $link->html( $node, 'javascript:alert(1)' ), 'no anchor for other schemes' );

        $bool = new expSubitemsTestCountingColumn( 'x', array( 'Type' => 'bool' ) );
        $this->assertSame( 'Yes', $bool->html( $node, true ) );
        $this->assertSame( 'No', $bool->html( $node, false ) );
        $this->assertSame( '1', $bool->text( $node, true ) );

        $date = new expSubitemsTestCountingColumn( 'x', array( 'Type' => 'date' ) );
        $this->assertSame( date( 'Y-m-d', 86400 * 365 ), $date->text( $node, 86400 * 365 ) );
        $this->assertNotSame( '', $date->html( $node, 86400 * 365 ) );

        $html = new expSubitemsTestCountingColumn( 'x', array( 'Type' => 'html' ) );
        $this->assertSame( '<em>x</em>', $html->html( $node, '<em>x</em>' ) );
        $this->assertSame( 'x & y', $html->text( $node, '<em>x</em> &amp; y' ) );

        $code = new expSubitemsTestCountingColumn( 'x', array( 'Type' => 'code' ) );
        $this->assertSame( '<code>a&lt;b</code>', $code->html( $node, 'a<b' ) );
    }

    public function testCallableAndTemplateValidation()
    {
        $this->assertNotNull( expSubitemsCallableColumn::callableFromSetting( 'expSubitemsTestFixtures::handlerValue' ) );
        $this->assertNull( expSubitemsCallableColumn::callableFromSetting( 'system' ) );
        $this->assertNull( expSubitemsCallableColumn::callableFromSetting( 'expSubitemsTestFixtures::nosuch' ) );
        $this->assertNull( expSubitemsCallableColumn::callableFromSetting( 'a::b; rm' ) );
        $this->assertTrue( expSubitemsTemplateColumn::isValidTemplate( 'design:subitems/columns/x.tpl' ) );
        $this->assertFalse( expSubitemsTemplateColumn::isValidTemplate( 'file:/etc/passwd' ) );
        $this->assertFalse( expSubitemsTemplateColumn::isValidTemplate( 'design:../x.tpl' ) );
        $this->assertFalse( expSubitemsTemplateColumn::isValidTemplate( 'design:x.php' ) );
    }

    public function testRegisteredInEzjscoreIni()
    {
        $ini = eZINI::fetchFromFile( 'extension/ezjscore/settings/ezjscore.ini' );
        $this->assertTrue( $ini->hasGroup( 'ezjscServer_expsubitems' ) );
        $this->assertSame( 'expSubitemsServerFunctions', $ini->variable( 'ezjscServer_expsubitems', 'Class' ) );
        foreach ( array( 'columns', 'rows', 'savepreference' ) as $function )
            $this->assertTrue( is_callable( array( 'expSubitemsServerFunctions', $function ) ), $function );
        $this->assertSame( -1, expSubitemsServerFunctions::getCacheTime( 'rows' ) );
    }
}
