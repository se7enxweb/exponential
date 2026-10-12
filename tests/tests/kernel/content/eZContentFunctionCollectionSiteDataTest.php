<?php
/**
 * Site data read from templates and listed in the admin (fetch( 'content', 'site_data', ... ), setup/sitedata).
 *
 *  SD-01 - With TemplateReadable[] empty (the default) no name is readable: the fetch gives false
 *  SD-02 - A listed name that is stored gives its eZSiteData row (.value), the same as eZSiteData::fetchByName()
 *  SD-03 - Only whole names: listing a prefix does not open the longer name; a listed name not stored gives false;
 *          empty, blank and non-scalar names give false
 *  SD-04 - The template fetch (eZFunctionHandler, as fetch() calls it) obeys the same list
 *  SD-05 - The function is declared read only, with the one required parameter "name"
 *  SD-06 - fetchList()/fetchListCount(): a whole name, * as a wildcard, ordered by name, paged
 *  SD-07 - The admin view: setup/sitedata needs setup/system_info, is in the Setup menu, the filter keeps only
 *          characters a name has; the same template is in design/admin and design/admin4
 *
 * Read only: the live installation's ezsite_data is read (its ezpublish-version row), never written. The
 * settings are changed in memory for the test and put back.
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits a679a13176 and 94b7b6c239), Copyright (C)
 * Mugo Web, GPL v2.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/content/eZContentFunctionCollectionSiteDataTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZContentFunctionCollectionSiteDataTest extends PHPUnit\Framework\TestCase
{
    const VERSION_ROW = 'ezpublish-version';

    /** @var array|null the TemplateReadable[] in force before the test, null when it was not set */
    private $saved = null;

    /** @var bool */
    private $hadSetting = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        ezpLiveInstallation::requireOrSkip();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $ini = eZINI::instance();
        $this->hadSetting = $ini->hasVariable( 'SiteDataSettings', 'TemplateReadable' );
        $this->saved = $this->hadSetting ? $ini->variable( 'SiteDataSettings', 'TemplateReadable' ) : null;
        if ( !eZSiteData::fetchByName( self::VERSION_ROW ) )
            $this->markTestSkipped( 'needs the ' . self::VERSION_ROW . ' row of an installed site' );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        if ( $this->hadSetting )
            $ini->setVariable( 'SiteDataSettings', 'TemplateReadable', $this->saved );
        else
            $ini->removeSetting( 'SiteDataSettings', 'TemplateReadable' );
        parent::tearDown();
    }

    private function readable( array $names )
    {
        eZINI::instance()->setVariable( 'SiteDataSettings', 'TemplateReadable', $names );
    }

    private function fetch( $name )
    {
        $r = eZContentFunctionCollection::fetchSiteData( $name );
        $this->assertArrayHasKey( 'result', $r );
        return $r['result'];
    }

    /** SD-01 */
    public function testNothingReadableByDefault()
    {
        $shipped = eZINI::fetchFromFile( 'settings/site.ini' );
        $this->assertTrue( $shipped->hasGroup( 'SiteDataSettings' ), 'site.ini ships the block' );
        $this->assertSame( array(), array_values( array_filter( (array)$shipped->variable( 'SiteDataSettings', 'TemplateReadable' ) ) ),
                           'TemplateReadable[] is empty in the shipped settings' );
        $this->readable( array() );
        $this->assertSame( array(), eZSiteData::templateReadableNames() );
        $this->assertFalse( eZSiteData::isTemplateReadable( self::VERSION_ROW ) );
        $this->assertFalse( $this->fetch( self::VERSION_ROW ) );
    }

    /** SD-02 */
    public function testListedNameIsReadable()
    {
        $this->readable( array( 'some-other-name', self::VERSION_ROW ) );
        $row = $this->fetch( self::VERSION_ROW );
        $this->assertInstanceOf( 'eZSiteData', $row );
        $this->assertSame( self::VERSION_ROW, $row->attribute( 'name' ) );
        $this->assertSame( eZSiteData::fetchByName( self::VERSION_ROW )->attribute( 'value' ), $row->attribute( 'value' ) );
        $this->assertSame( $row->attribute( 'value' ), $this->fetch( '  ' . self::VERSION_ROW . ' ' )->attribute( 'value' ), 'trimmed' );
    }

    /** SD-03 */
    public function testWholeNamesOnly()
    {
        $this->readable( array( 'ezpublish', 'ezpublish-*', 'exp-test-site-data-not-stored-' . getmypid() ) );
        $this->assertFalse( $this->fetch( self::VERSION_ROW ), 'a prefix or a pattern opens nothing' );
        $this->assertFalse( $this->fetch( 'exp-test-site-data-not-stored-' . getmypid() ), 'listed but not stored' );
        foreach ( array( '', '   ', null, false, array( self::VERSION_ROW ) ) as $bad )
            $this->assertFalse( $this->fetch( $bad ), var_export( $bad, true ) );
        $this->readable( array( '', '  ', self::VERSION_ROW, self::VERSION_ROW ) );
        $this->assertSame( array( self::VERSION_ROW ), eZSiteData::templateReadableNames(), 'blank entries dropped, duplicates once' );
    }

    /** SD-04 */
    public function testTemplateFetch()
    {
        $this->readable( array() );
        $this->assertFalse( eZFunctionHandler::execute( 'content', 'site_data', array( 'name' => self::VERSION_ROW ) ) );
        $this->readable( array( self::VERSION_ROW ) );
        $row = eZFunctionHandler::execute( 'content', 'site_data', array( 'name' => self::VERSION_ROW ) );
        $this->assertInstanceOf( 'eZSiteData', $row );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'name', self::VERSION_ROW );
        $this->assertSame( $row->attribute( 'value' ), trim( $tpl->fetch( 'file:' . __DIR__ . '/fixtures/site_data_fetch.tpl' ) ) );
        $this->readable( array() );
        $this->assertSame( 'false', trim( $tpl->fetch( 'file:' . __DIR__ . '/fixtures/site_data_fetch.tpl' ) ) );
    }

    /** SD-05 */
    public function testFunctionDefinition()
    {
        $FunctionList = array();
        include dirname( __DIR__, 4 ) . '/kernel/content/function_definition.php';
        $this->assertArrayHasKey( 'site_data', $FunctionList );
        $def = $FunctionList['site_data'];
        $this->assertSame( array( 'read' ), $def['operation_types'] );
        $this->assertSame( array( 'class' => 'eZContentFunctionCollection', 'method' => 'fetchSiteData' ), $def['call_method'] );
        $this->assertCount( 1, $def['parameters'] );
        $this->assertSame( 'name', $def['parameters'][0]['name'] );
        $this->assertTrue( $def['parameters'][0]['required'] );
    }

    /** SD-06 */
    public function testList()
    {
        $all = eZSiteData::fetchList( '', 0, 1000 );
        $this->assertSame( count( $all ), eZSiteData::fetchListCount( '' ) );
        $names = array_map( function ( $r ) { return $r->attribute( 'name' ); }, $all );
        $sorted = $names;
        sort( $sorted, SORT_STRING );
        $this->assertSame( $sorted, $names, 'ordered by name' );
        $this->assertContains( self::VERSION_ROW, $names );

        $one = eZSiteData::fetchList( self::VERSION_ROW );
        $this->assertCount( 1, $one, 'a whole name' );
        $this->assertSame( 1, eZSiteData::fetchListCount( self::VERSION_ROW ) );
        $this->assertSame( 0, eZSiteData::fetchListCount( 'ezpublish' ), 'no wildcard without *' );
        $like = array_map( function ( $r ) { return $r->attribute( 'name' ); }, eZSiteData::fetchList( 'ezpublish-*' ) );
        $this->assertContains( self::VERSION_ROW, $like );
        foreach ( $like as $n )
            $this->assertStringStartsWith( 'ezpublish-', $n );
        $this->assertCount( 1, eZSiteData::fetchList( '', 0, 1 ), 'limit' );
        if ( count( $all ) > 1 )
            $this->assertSame( $names[1], eZSiteData::fetchList( '', 1, 1 )[0]->attribute( 'name' ), 'offset' );
    }

    /** SD-07 */
    public function testAdminView()
    {
        $root = dirname( __DIR__, 4 ) . '/';
        $ViewList = array();
        $FunctionList = array();
        include $root . 'kernel/setup/module.php';
        $this->assertArrayHasKey( 'sitedata', $ViewList );
        $this->assertSame( array( 'system_info' ), $ViewList['sitedata']['functions'] );
        $this->assertFileExists( $root . 'kernel/setup/sitedata.php' );
        $this->assertTrue( class_exists( 'Exponential\\View\\Kernel\\Setup\\Sitedata' ) );

        // the shipped file as written (the INI cache of a running site may predate it)
        $menu = file_get_contents( $root . 'settings/menu.ini' );
        $setup = substr( $menu, strpos( $menu, '[Leftmenu_setup]' ) );
        $setup = substr( $setup, 0, (int)strpos( $setup, "\n[", 1 ) ?: strlen( $setup ) );
        $this->assertStringContainsString( "\nLinks[sitedata]=setup/sitedata\n", $setup );
        $this->assertStringContainsString( "\nPolicyList_sitedata[]=setup/system_info\n", $setup );
        foreach ( array( 'admin', 'admin4' ) as $design )
            $this->assertStringContainsString( "'sitedata'", file_get_contents( $root . "design/$design/templates/parts/setup/menu.tpl" ), $design );
        $this->assertFileEquals( $root . 'design/admin/templates/setup/sitedata.tpl', $root . 'design/admin4/templates/setup/sitedata.tpl' );

        $clean = array( 'Exponential\\View\\Kernel\\Setup\\Sitedata', 'cleanFilter' );
        $this->assertSame( 'ezpublish-*', call_user_func( $clean, ' ezpublish-* ' ) );
        $this->assertSame( 'ab', call_user_func( $clean, "a'<b>" ) );
        $this->assertSame( 'ab', call_user_func( $clean, 'a/b' ), 'no slash: it would end the view parameter' );
        $this->assertSame( 100, strlen( call_user_func( $clean, str_repeat( 'x', 300 ) ) ) );
    }
}
