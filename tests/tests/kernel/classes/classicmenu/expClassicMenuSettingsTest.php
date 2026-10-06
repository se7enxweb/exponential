<?php
/**
 * The classic menu settings page (visual/menuconfig) without a database: which siteaccesses read menu.ini
 * [SelectedMenu] (expClassicMenuSiteAccessInspector) and how the settings are read and written
 * (expClassicMenuSettings). Writes go to a fixture installation in var/tmp/classicmenu-tests/, never to settings/.
 *
 *  CM-01 - The design list is SiteDesign, the additional designs and the standard design, each once
 *  CM-02 - Any admin* design makes an administration siteaccess
 *  CM-03 - Commented-out menus do not count; fixed design: includes are found, paths with .. are not
 *  CM-04 - A page layout is classic, mixed, layouts, other, admin or unknown, by what it reads
 *  CM-05 - Siteaccesses are ordered with those that read the settings first
 *  CM-06 - Menu choices come from AvailableMenuArray, once each, with what they put at the top and on the left
 *  CM-07 - Only a listed menu type is saved; a posted value is never written as it is
 *  CM-08 - A siteaccess name is letters, digits, _ and -; the directory must be a known one inside settings/siteaccess
 *  CM-09 - The source of a value is named by the kind of place; a global override is reported
 *  CM-10 - The PHP wrapper check catches a missing start, a missing end and a comment end in between
 *  CM-11 - Saving changes only the three settings, keeps the rest of the file and its wrapper, and backs it up
 *  CM-12 - Saving the same again writes nothing; a new file is created wrapped
 *  CM-13 - Saving refuses an unknown siteaccess, a traversal and a siteaccess without a directory
 *  CM-14 - The example lines on the page are exactly what saving that arrangement writes
 *  CM-15 - What each arrangement lists follows the menu templates (parent, classes, depth)
 *  CM-16 - A design draws the menus, only has menu templates, or neither
 *  CM-17 - The template examples exist, and the guide holds each of them word for word
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expClassicMenuSettingsTest extends PHPUnit\Framework\TestCase
{
    /** @var string fixture installation root, with a trailing slash */
    protected $root;

    protected function setUp(): void
    {
        parent::setUp();
        $base = expIniEditor::realRoot() . 'var/tmp/classicmenu-tests';
        $this->root = $base . '/' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '/';
        mkdir( $this->root . 'settings/siteaccess/demo', 0700, true );
        mkdir( $this->root . 'settings/siteaccess/fresh', 0700, true );
        mkdir( $this->root . 'settings/siteaccess/other', 0700, true );
        mkdir( $this->root . 'var/backup/ini', 0700, true );
        expIniEditor::setRoot( $this->root, array(), array( 'demo', 'fresh', 'other' ) );
    }

    protected function tearDown(): void
    {
        expIniEditor::setRoot( null );
        $this->removeTree( rtrim( $this->root, '/' ) );
        parent::tearDown();
    }

    private function removeTree( $dir )
    {
        if ( strpos( $dir, '/var/tmp/classicmenu-tests/' ) === false || !is_dir( $dir ) )
            return;
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
                                                 RecursiveIteratorIterator::CHILD_FIRST ) as $item )
        {
            if ( $item->isDir() && !$item->isLink() )
                rmdir( $item->getPathname() );
            else
                unlink( $item->getPathname() );
        }
        rmdir( $dir );
    }

    private function menuGroups()
    {
        return array(
            'TopOnly' => array( 'TitleText' => 'Only top menu', 'MenuThumbnail' => 'menu/top_only.jpg', 'TopMenu' => 'flat_top', 'LeftMenu' => '' ),
            'LeftOnly' => array( 'TitleText' => 'Left menu', 'MenuThumbnail' => 'menu/left_only.jpg', 'TopMenu' => '', 'LeftMenu' => 'flat_left' ),
            'DoubleTop' => array( 'TitleText' => 'Double top menu', 'TopMenu' => 'double_top', 'LeftMenu' => '' ),
            'LeftTop' => array( 'TitleText' => 'Left and top', 'TopMenu' => 'flat_top', 'LeftMenu' => 'sub_left' ),
            'Fancy' => array( 'TopMenu' => 'mega', 'LeftMenu' => '' ),
        );
    }

    private function choices()
    {
        return expClassicMenuSettings::choices( array( 'TopOnly', 'LeftOnly', 'DoubleTop', 'LeftTop', 'Fancy' ), $this->menuGroups() );
    }

    /** CM-01 */
    public function testDesignListOrderAndDuplicates()
    {
        $this->assertSame( array( 'media', 'ezwebin', 'standard', 'base' ),
                           expClassicMenuSiteAccessInspector::designList( 'media', array( 'ezwebin', 'standard', 'base', 'media' ), 'standard' ) );
        $this->assertSame( array( 'standard' ), expClassicMenuSiteAccessInspector::designList( '', array(), 'standard' ) );
    }

    /** CM-02 */
    public function testAdminDesigns()
    {
        $this->assertTrue( expClassicMenuSiteAccessInspector::isAdminDesign( array( 'admin' ) ) );
        $this->assertTrue( expClassicMenuSiteAccessInspector::isAdminDesign( array( 'editor', 'admin4l', 'admin4' ) ) );
        $this->assertTrue( expClassicMenuSiteAccessInspector::isAdminDesign( array( 'adminui' ) ) );
        $this->assertFalse( expClassicMenuSiteAccessInspector::isAdminDesign( array( 'media', 'ezwebin', 'standard' ) ) );
        $this->assertFalse( expClassicMenuSiteAccessInspector::isAdminDesign( array( 'myadmin' ) ) );
    }

    /** CM-03 */
    public function testCommentsAndIncludes()
    {
        $source = "{* {menu name=TopMenu} *}\n{include uri='design:page_topmenu.tpl'}\n{include uri=\"design:parts/x.tpl\" a=1}\n"
                . "{include uri='design:../secret.tpl'}\n{include uri=concat('design:menu/', \$x, '.tpl')}\n{include uri='design:page_topmenu.tpl'}";
        $stripped = expClassicMenuSiteAccessInspector::stripComments( $source );
        $this->assertStringNotContainsString( 'menu name', $stripped );
        $this->assertSame( array( 'page_topmenu.tpl', 'parts/x.tpl' ), expClassicMenuSiteAccessInspector::includedTemplates( $stripped ) );
    }

    /** CM-04 */
    public function testClassification()
    {
        $web = array( 'mysite', 'ezwebin', 'standard', 'base' );
        $c = expClassicMenuSiteAccessInspector::classify( $web, 'pl.tpl', array( 'pl.tpl' => '<div>{menu name=TopMenu}</div>' ) );
        $this->assertSame( 'classic', $c['status'] );
        $this->assertTrue( $c['relevant'] );
        $this->assertSame( array( 'pl.tpl' ), $c['found_in'] );

        $c = expClassicMenuSiteAccessInspector::classify( $web, 'pl.tpl', array( 'pl.tpl' => '{include uri=\'design:page_topmenu.tpl\'}',
                                                                                  'top.tpl' => "{include uri=concat('design:menu/', \$pagedata.top_menu, '.tpl')}" ) );
        $this->assertSame( 'classic', $c['status'], 'found in an included template' );
        $this->assertSame( array( 'top.tpl' ), $c['found_in'] );

        $c = expClassicMenuSiteAccessInspector::classify( $web, 'pl.tpl', array( 'pl.tpl' => "{*{if \$pagedata.top_menu}{/if}*}<body></body>" ) );
        $this->assertSame( 'other', $c['status'], 'a commented-out menu is not read' );
        $this->assertFalse( $c['relevant'] );

        $layouts = "{set \$el_layout = fetch('explayouts','resolve_layout',hash())}{include uri='design:explayouts/layout.tpl'}";
        $c = expClassicMenuSiteAccessInspector::classify( array( 'media' ), 'pl.tpl', array( 'pl.tpl' => $layouts ) );
        $this->assertSame( 'layouts', $c['status'] );
        $this->assertFalse( $c['relevant'] );

        $c = expClassicMenuSiteAccessInspector::classify( array( 'media' ), 'pl.tpl', array( 'pl.tpl' => $layouts . '{menu name=LeftMenu}' ) );
        $this->assertSame( 'mixed', $c['status'] );
        $this->assertTrue( $c['relevant'] );

        $c = expClassicMenuSiteAccessInspector::classify( array( 'admin4l', 'admin4', 'admin' ), 'pl.tpl', array( 'pl.tpl' => '{menu name=TopMenu}' ) );
        $this->assertSame( 'admin', $c['status'], 'an administration design is admin whatever its page layout holds' );
        $this->assertFalse( $c['relevant'] );

        $c = expClassicMenuSiteAccessInspector::classify( array( 'nothing' ), false, array() );
        $this->assertSame( 'unknown', $c['status'] );
        $this->assertSame( '', $c['pagelayout'] );
    }

    /** CM-05 */
    public function testOrder()
    {
        $sorted = expClassicMenuSiteAccessInspector::sortResults( array(
            'admin' => array( 'status' => 'admin' ), 'site' => array( 'status' => 'layouts' ), 'old' => array( 'status' => 'classic' ),
            'portal' => array( 'status' => 'other' ), 'old2' => array( 'status' => 'classic' ), 'x' => array( 'status' => 'mixed' ) ) );
        $this->assertSame( array( 'old', 'old2', 'x', 'portal', 'site', 'admin' ), array_map( 'strval', array_keys( $sorted ) ) );
    }

    /** CM-06 */
    public function testChoices()
    {
        $choices = expClassicMenuSettings::choices( array( 'TopOnly', 'Missing', 'TopOnly', 'LeftTop', 'DoubleTop', '', 'Fancy' ), $this->menuGroups() );
        $this->assertSame( array( 'TopOnly', 'LeftTop', 'DoubleTop', 'Fancy' ), array_column( $choices, 'type' ) );
        $this->assertSame( 'Only top menu', $choices[0]['title'] );
        $this->assertSame( array( 'flat_top', 'none' ), array( $choices[0]['top_kind'], $choices[0]['left_kind'] ) );
        $this->assertSame( array( true, false, true ), array( $choices[1]['has_top'], $choices[1]['has_second_row'], $choices[1]['has_left'] ) );
        $this->assertSame( 'sub_left', $choices[1]['left_kind'] );
        $this->assertTrue( $choices[2]['has_second_row'] );
        $this->assertSame( 'Fancy', $choices[3]['title'], 'without a TitleText the type is the title' );
        $this->assertSame( 'custom', $choices[3]['top_kind'] );
        $this->assertSame( 'none', expClassicMenuSettings::templateKind( '  ' ) );
    }

    /** CM-07 */
    public function testSelection()
    {
        $choices = $this->choices();
        $this->assertSame( array( 'CurrentMenu' => 'LeftTop', 'TopMenu' => 'flat_top', 'LeftMenu' => 'sub_left' ),
                           expClassicMenuSettings::selection( 'LeftTop', $choices ) );
        $this->assertFalse( expClassicMenuSettings::selection( 'Bogus', $choices ) );
        $this->assertFalse( expClassicMenuSettings::selection( "TopOnly\nTopMenu=evil", $choices ) );
        $this->assertFalse( expClassicMenuSettings::selection( array( 'TopOnly' ), $choices ) );
        $this->assertFalse( expClassicMenuSettings::selection( '', $choices ) );
        $this->assertSame( array( '[SelectedMenu]', 'CurrentMenu=LeftOnly', 'TopMenu=', 'LeftMenu=flat_left' ),
                           expClassicMenuSettings::plannedLines( expClassicMenuSettings::selection( 'LeftOnly', $choices ) ) );
    }

    /** CM-08 */
    public function testSiteAccessNamesAndDirectories()
    {
        foreach ( array( 'site', 'bold_ger', 'admin-2' ) as $ok )
            $this->assertTrue( expClassicMenuSettings::isSiteAccessName( $ok ), $ok );
        foreach ( array( '', '..', '../site', 'a/b', "site\0", 'site ', array( 'site' ), null, str_repeat( 'a', 65 ) ) as $bad )
            $this->assertFalse( expClassicMenuSettings::isSiteAccessName( $bad ) );

        $known = array( 'demo', 'other', 'missing', 'link' );
        $this->assertSame( 'settings/siteaccess/demo', expClassicMenuSettings::siteAccessDirectory( 'demo', $known, $this->root ) );
        $this->assertFalse( expClassicMenuSettings::siteAccessDirectory( 'fresh', $known, $this->root ), 'not offered' );
        $this->assertFalse( expClassicMenuSettings::siteAccessDirectory( 'missing', $known, $this->root ), 'no directory' );
        $this->assertFalse( expClassicMenuSettings::siteAccessDirectory( '../siteaccess/demo', array( '../siteaccess/demo' ), $this->root ) );
        mkdir( $this->root . 'outside', 0700 );
        symlink( $this->root . 'outside', $this->root . 'settings/siteaccess/link' );
        $this->assertFalse( expClassicMenuSettings::siteAccessDirectory( 'link', $known, $this->root ), 'a link that leaves settings/siteaccess' );
    }

    /** CM-09 */
    public function testPlacements()
    {
        $root = '/srv/site';
        $this->assertSame( array( 'file' => 'settings/menu.ini', 'kind' => 'default' ), expClassicMenuSettings::describePlacement( '/srv/site/settings/menu.ini', $root ) );
        $this->assertSame( 'siteaccess', expClassicMenuSettings::describePlacement( 'settings/siteaccess/demo/menu.ini.append.php', $root )['kind'] );
        $this->assertSame( 'extension', expClassicMenuSettings::describePlacement( 'extension/ezwebin/settings/menu.ini.append.php', $root )['kind'] );
        $this->assertSame( 'extension_siteaccess', expClassicMenuSettings::describePlacement( 'extension/x/settings/siteaccess/demo/menu.ini.append.php', $root )['kind'] );
        $this->assertSame( 'override', expClassicMenuSettings::describePlacement( array( 'settings/menu.ini', 'settings/override/menu.ini.append.php' ), $root )['kind'] );
        $this->assertSame( 'unknown', expClassicMenuSettings::describePlacement( '', $root )['kind'] );

        $current = expClassicMenuSettings::describeCurrent(
            array( 'CurrentMenu' => 'LeftTop', 'TopMenu' => 'flat_top', 'LeftMenu' => 'sub_left' ),
            array( 'CurrentMenu' => 'settings/override/menu.ini.append.php', 'TopMenu' => 'settings/menu.ini', 'LeftMenu' => 'settings/menu.ini' ),
            array( 'TopIdentifierList' => array( 'folder', '', 'gallery' ), 'LeftIdentifierList' => 'nonsense' ), $root );
        $this->assertSame( 'LeftTop', $current['current_menu'] );
        $this->assertTrue( $current['overridden'] );
        $this->assertSame( array( 'folder', 'gallery' ), $current['top_classes'] );
        $this->assertSame( array(), $current['left_classes'] );
        $current = expClassicMenuSettings::describeCurrent( array( 'CurrentMenu' => 'TopOnly' ), array( 'CurrentMenu' => 'settings/siteaccess/demo/menu.ini.append.php' ), array(), $root );
        $this->assertFalse( $current['overridden'] );
        $this->assertSame( '', $current['settings']['LeftMenu']['value'] );
    }

    /** CM-10 */
    public function testWrapperCheck()
    {
        $good = "<?php /* #?ini charset=\"utf-8\"?\n\n[SelectedMenu]\nCurrentMenu=TopOnly\n*/ ?>\n";
        $this->assertTrue( expClassicMenuSettings::wrapperIntact( $good, $good ) );
        $this->assertTrue( expClassicMenuSettings::wrapperIntact( $good, false ), 'a new file must be wrapped' );
        $this->assertFalse( expClassicMenuSettings::wrapperIntact( "[SelectedMenu]\nCurrentMenu=TopOnly\n*/ ?>", $good ) );
        $this->assertFalse( expClassicMenuSettings::wrapperIntact( "<?php /*\n[SelectedMenu]\nCurrentMenu=TopOnly\n", $good ) );
        $this->assertFalse( expClassicMenuSettings::wrapperIntact( "<?php /*\n[A]\nB=*/ phpinfo(); /*\n*/ ?>", $good ) );
        $this->assertTrue( expClassicMenuSettings::wrapperIntact( "[A]\nB=c\n", "[A]\nB=d\n" ), 'a file without the wrapper keeps none' );
    }

    /** CM-11 */
    public function testSavingChangesOnlyTheThreeSettings()
    {
        $file = $this->root . 'settings/siteaccess/demo/menu.ini.append.php';
        $before = "<?php /* #?ini charset=\"utf-8\"?\n\n# the admin tabs of this siteaccess\n[TopAdminMenu]\nTabs[]\nTabs[]=content\n\n"
                . "[SelectedMenu]\n# chosen in 2014\nCurrentMenu=TopOnly\nTopMenu=flat_top\nLeftMenu=\n\n[MenuContentSettings]\nTopIdentifierList[]\nTopIdentifierList[]=folder\n*/ ?>\n";
        file_put_contents( $file, $before );

        $selection = expClassicMenuSettings::selection( 'LeftTop', $this->choices() );
        $result = expClassicMenuSettings::write( 'demo', $selection, array( 'demo', 'other' ) );
        $this->assertTrue( $result['ok'], 'error: ' . $result['error'] );
        $this->assertTrue( $result['changed'] );
        $this->assertFalse( $result['created'] );
        $this->assertSame( 'settings/siteaccess/demo/menu.ini.append.php', $result['file'] );

        $after = file_get_contents( $file );
        $expected = str_replace( "CurrentMenu=TopOnly\nTopMenu=flat_top\nLeftMenu=\n", "CurrentMenu=LeftTop\nTopMenu=flat_top\nLeftMenu=sub_left\n", $before );
        $this->assertSame( $expected, $after, 'only the changed lines differ; comments, other sections and the wrapper stay' );
        $this->assertNotSame( '', $result['backup'] );
        $this->assertFileExists( $this->root . $result['backup'] );
        $this->assertSame( $before, file_get_contents( $this->root . $result['backup'] ) );
    }

    /** CM-12 */
    public function testUnchangedAndNewFile()
    {
        $selection = expClassicMenuSettings::selection( 'DoubleTop', $this->choices() );
        $result = expClassicMenuSettings::write( 'other', $selection, array( 'other' ) );
        $this->assertTrue( $result['ok'], 'error: ' . $result['error'] );
        $this->assertTrue( $result['created'] );
        $file = $this->root . 'settings/siteaccess/other/menu.ini.append.php';
        $text = file_get_contents( $file );
        $this->assertTrue( expClassicMenuSettings::wrapperIntact( $text, false ) );
        $this->assertStringContainsString( "[SelectedMenu]\nCurrentMenu=DoubleTop\nTopMenu=double_top\nLeftMenu=\n", $text );
        $check = new expIniEditor( expIniEditor::scope( 'siteaccess:other' ), 'menu' );
        $this->assertSame( 'double_top', $check->get( 'SelectedMenu', 'TopMenu' ) );

        $mtime = filemtime( $file );
        $again = expClassicMenuSettings::write( 'other', $selection, array( 'other' ) );
        $this->assertTrue( $again['ok'] );
        $this->assertFalse( $again['changed'], 'nothing to write' );
        $this->assertSame( $text, file_get_contents( $file ) );
        clearstatcache();
        $this->assertSame( $mtime, filemtime( $file ) );
    }

    /** CM-13 */
    public function testRefusals()
    {
        $selection = expClassicMenuSettings::selection( 'TopOnly', $this->choices() );
        foreach ( array( array( 'fresh', array( 'demo' ) ), array( '../demo', array( '../demo' ) ), array( 'nodir', array( 'nodir' ) ) ) as $case )
        {
            $result = expClassicMenuSettings::write( $case[0], $selection, $case[1] );
            $this->assertFalse( $result['ok'], $case[0] );
            $this->assertSame( 'no_directory', $result['error'], $case[0] );
        }
        $this->assertFileDoesNotExist( $this->root . 'settings/siteaccess/fresh/menu.ini.append.php' );
        $this->assertFileDoesNotExist( $this->root . 'settings/menu.ini.append.php' );
    }

    /** CM-14 */
    public function testExampleLinesAreWhatSavingWrites()
    {
        $choices = $this->choices();
        $recipes = expClassicMenuSettings::recipes( $choices, 'demo', 'demo', 'other' );
        $this->assertSame( array( 'top_only', 'top_and_left', 'limit_classes', 'hide_node', 'per_siteaccess' ), array_keys( $recipes ) );
        $this->assertSame( array( 'settings/siteaccess/demo/menu.ini.append.php', 'settings/siteaccess/other/menu.ini.append.php' ),
                           array_keys( $recipes['per_siteaccess']['files'] ) );
        $this->assertArrayNotHasKey( 'per_siteaccess', expClassicMenuSettings::recipes( $choices, 'demo', 'demo', 'demo' ) );

        $file = $this->root . 'settings/siteaccess/demo/menu.ini.append.php';
        foreach ( array( 'top_only' => 'TopOnly', 'top_and_left' => 'LeftTop' ) as $id => $type )
        {
            $shown = $recipes[$id]['files']['settings/siteaccess/demo/menu.ini.append.php'];
            $result = expClassicMenuSettings::write( 'demo', expClassicMenuSettings::selection( $type, $choices ), array( 'demo' ) );
            $this->assertTrue( $result['ok'], $type );
            $text = file_get_contents( $file );
            $this->assertStringContainsString( implode( "\n", $shown ) . "\n", $text, "the page shows for $type exactly what saving writes" );
        }
        foreach ( $choices as $choice )
        {
            $this->assertSame( expClassicMenuSettings::plannedLines( expClassicMenuSettings::selection( $choice['type'], $choices ) ),
                               array( '[SelectedMenu]', 'CurrentMenu=' . $choice['type'], 'TopMenu=' . $choice['top'], 'LeftMenu=' . $choice['left'] ) );
        }
    }

    /** CM-15 */
    public function testWhatEachArrangementLists()
    {
        $by = array();
        foreach ( $this->choices() as $choice )
            $by[$choice['type']] = expClassicMenuSettings::menuSources( $choice );
        $this->assertSame( array( array( 'position' => 'top', 'template' => 'flat_top', 'parent' => 'root', 'classes' => 'TopIdentifierList', 'deeper' => false ) ), $by['TopOnly'] );
        $this->assertSame( 'root', $by['LeftOnly'][0]['parent'], 'LeftOnly lists the pages below the start page' );
        $this->assertTrue( $by['LeftOnly'][0]['deeper'] );
        $this->assertSame( array( 'top', 'second' ), array_column( $by['DoubleTop'], 'position' ) );
        $this->assertSame( array( 'root', 'section' ), array_column( $by['DoubleTop'], 'parent' ) );
        $this->assertSame( array( 'TopIdentifierList', 'TopIdentifierList' ), array_column( $by['DoubleTop'], 'classes' ), 'both rows use the top list' );
        $this->assertSame( array( 'flat_top', 'sub_left' ), array_column( $by['LeftTop'], 'template' ) );
        $this->assertSame( 'section', $by['LeftTop'][1]['parent'] );
        $this->assertSame( 'LeftIdentifierList', $by['LeftTop'][1]['classes'] );
        $this->assertSame( array(), $by['Fancy'], 'nothing is claimed for a design\'s own template' );
    }

    /** CM-16 */
    public function testDesignClassification()
    {
        $menus = array( 'flat_top' => "{fetch( 'content', 'list', hash( 'class_filter_array', ezini( 'MenuContentSettings', 'TopIdentifierList', 'menu.ini' ) ) )}",
                        'dropdown' => "{ezini( 'MenuContentSettings', 'TopIdentifierList', 'menu.ini' )}", 'other' => '<ul></ul>' );
        $d = expClassicMenuSiteAccessInspector::classifyDesign( '{menu name=TopMenu}', array(), $menus );
        $this->assertSame( 'draws', $d['status'] );
        $this->assertSame( array( 'dropdown', 'flat_top' ), $d['menu_templates'] );
        $d = expClassicMenuSiteAccessInspector::classifyDesign( "{*{if \$pagedata.top_menu}{include uri='design:page_topmenu.tpl'}{/if}*}", array(), $menus );
        $this->assertSame( 'templates_only', $d['status'], 'menus commented out of the page layout' );
        $d = expClassicMenuSiteAccessInspector::classifyDesign( false, array(), $menus );
        $this->assertSame( 'templates_only', $d['status'], 'no page layout of its own' );
        $d = expClassicMenuSiteAccessInspector::classifyDesign( "{include uri='design:page_topmenu.tpl'}",
                                                               array( 'page_topmenu.tpl' => "{include uri=concat('design:menu/', \$pagedata.top_menu, '.tpl')}" ), array() );
        $this->assertSame( 'draws', $d['status'], 'read in a template the page layout includes' );
        $d = expClassicMenuSiteAccessInspector::classifyDesign( "{fetch('explayouts','resolve_layout',hash())}", array(), array() );
        $this->assertSame( array( 'none', true ), array( $d['status'], $d['layouts'] ) );
    }

    /** CM-17 */
    public function testTemplateExamplesAndGuide()
    {
        $examples = expClassicMenuSettings::templateExamples();
        $this->assertSame( array( 'pagelayout_menus', 'menu_my_top', 'menu_my_section_left', 'design_extension' ), array_keys( $examples ) );
        $this->assertStringContainsString( '{menu name=TopMenu}', $examples['pagelayout_menus']['text'] );
        $this->assertStringContainsString( '{cache-block keys=', $examples['pagelayout_menus']['text'] );
        foreach ( array( 'menu_my_top', 'menu_my_section_left' ) as $id )
        {
            $text = $examples[$id]['text'];
            $this->assertStringContainsString( "'class_filter_array', ezini( 'MenuContentSettings'", $text, $id );
            $this->assertStringContainsString( '|ezurl}', $text, $id );
            $this->assertStringContainsString( '.name|wash}', $text, $id );
            $this->assertStringNotContainsString( 'ignore_visibility', $text, "$id leaves hidden pages out" );
            $this->assertSame( substr_count( $text, '{def ' ), substr_count( $text, '{undef ' ), "$id undefines what it defines" );
        }
        $guide = file_get_contents( expIniEditor::realRoot() . 'doc/guides/classic-menu-settings.md' );
        foreach ( $examples as $id => $example )
            $this->assertStringContainsString( $example['text'], $guide, "the guide shows $id word for word" );
    }
}
