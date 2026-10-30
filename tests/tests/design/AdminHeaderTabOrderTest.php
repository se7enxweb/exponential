<?php
/**
 * The Tab order of the header is the same in admin4 and admin4l: logo, menu button, menu, search, site link,
 * light/dark switch, user menu, sidebar controls. admin4 draws it from page_topmenu.tpl; admin4l draws the header zone
 * and the topmenu zone of its admin layout around the one menu panel, so the page draws the logo and the menu button
 * (the blocks admin_logo and admin_menu_button) before the panel and the rest of the header zone after it.
 *
 * Checked on the files alone: the focusable parts are found by their markup in the templates, in the order the page
 * draws them. The stock layouts are read from the prepared seed of the explayouts extension when it is installed.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminHeaderTabOrderTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminHeaderTabOrderTest extends PHPUnit\Framework\TestCase
{
    /** The focusable parts of the header, by the markup that starts them */
    const PARTS = array(
        'logo'     => 'class="brand"',
        'menu-btn' => 'class="navbar-icon"',
        'menu'     => 'id="a4-navbar-menu"',
        'search'   => 'id="header-search"',
        'site'     => 'class="site-preview',
        'theme'    => 'id="a4-theme-toggle"',
        'user'     => 'id="a4-user-toggle"',
        'controls' => 'class="sidebar-controls"',
    );

    const ORDER = array( 'logo', 'menu-btn', 'menu', 'search', 'site', 'theme', 'user', 'controls' );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        $file = self::root() . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    /** A template with the includes of the header parts and of admin4l's blocks that carry focusable controls written out in place */
    private static function expand( $tpl )
    {
        return preg_replace_callback( "/\\{include uri='design:((?:parts\\/a4_header_(?:site|user)|explayouts\\/block\\/admin_\\w+)\\.tpl)'[^}]*\\}/",
            function ( $m )
            {
                $design = strpos( $m[1], 'explayouts/' ) === 0 ? 'admin4l' : 'admin4';
                return self::expand( self::read( "design/$design/templates/" . $m[1] ) );
            }, $tpl );
    }

    /** The parts a text contains, in the order they stand in it */
    private static function partsOf( $text )
    {
        $at = array();
        foreach ( self::PARTS as $name => $needle )
        {
            $pos = strpos( $text, $needle );
            if ( $pos !== false )
                $at[$name] = $pos;
        }
        asort( $at );
        return array_keys( $at );
    }

    /** The parts a block template of admin4l draws, in order */
    private static function partsOfBlock( $identifier )
    {
        return self::partsOf( self::expand( self::read( "design/admin4l/templates/explayouts/block/$identifier.tpl" ) ) );
    }

    /** What the page draws for a zone: the blocks of $zone (definition identifiers in zone order) a pass takes */
    private static function pass( array $blocks, $only, $skip )
    {
        $drawn = array();
        foreach ( $blocks as $identifier )
        {
            if ( ( $only === null || in_array( $identifier, $only, true ) ) && ( $skip === null || !in_array( $identifier, $skip, true ) ) )
                $drawn = array_merge( $drawn, $identifier === 'admin_tab_menu' ? array() : self::partsOfBlock( $identifier ) );
        }
        return $drawn;
    }

    /** the blocks named in "only=array( ... )" or "skip=array( ... )" of the header zone's include in the page layout */
    private static function passes()
    {
        $page = self::read( 'design/admin4l/templates/pagelayout.tpl' );
        $found = array();
        preg_match_all( "/set-block variable=\\\$(\\w+)\\}\\{include uri='design:explayouts\\/admin_zone\\.tpl'[^}]*zone_identifier='header'( (?:only|skip)=array\\( ([^)]*) \\))?/", $page, $m, PREG_SET_ORDER );
        foreach ( $m as $row )
        {
            $list = $row[3] === '' || !isset( $row[3] ) ? null : array_map( function ( $v ) { return trim( $v, " '" ); }, explode( ',', $row[3] ) );
            $found[$row[1]] = array( 'only' => strpos( $row[2], 'only' ) !== false ? $list : null,
                                     'skip' => strpos( $row[2], 'skip' ) !== false ? $list : null );
        }
        return $found;
    }

    /** the places of the header start, the menu panel and the rest of the header in the page layout's navbar */
    private static function navbarOrder()
    {
        $page = self::read( 'design/admin4l/templates/pagelayout.tpl' );
        $navbar = substr( $page, strpos( $page, '<div id="navbar" class="navbar-main">' ) );
        $navbar = substr( $navbar, 0, strpos( $navbar, '{else}' ) );
        $at = array();
        foreach ( array( 'header_start' => '{$header_start}', 'menu' => '<div id="a4-navbar-menu"', 'header_html' => '{$header_html}' ) as $k => $needle )
        {
            $pos = strpos( $navbar, $needle );
            $at[$k] = $pos === false ? PHP_INT_MAX : $pos;
        }
        asort( $at );
        return array_keys( $at );
    }

    /** The parts in the order admin4l draws them for a header zone with these blocks (the tab menu is in the topmenu zone) */
    private static function admin4lOrder( array $header )
    {
        $passes = self::passes();
        $parts = array();
        foreach ( self::navbarOrder() as $place )
        {
            if ( $place === 'menu' )
            {
                $parts[] = 'menu';
                continue;
            }
            $var = $place;
            if ( !isset( $passes[$var] ) )
                continue;
            $parts = array_merge( $parts, self::pass( $header, $passes[$var]['only'], $passes[$var]['skip'] ) );
        }
        return $parts;
    }

    /** The header zone of the stock layouts: from the seed when the explayouts extension is installed, else as shipped */
    private static function stockHeaders()
    {
        $file = self::root() . '/extension/explayouts/share/fragments/admin_layouts_db_data.dba';
        if ( !is_file( $file ) )
            return array( 'admin_3col' => array( 'admin_logo', 'admin_menu_button', 'admin_search', 'admin_theme_switch', 'admin_sidebar_controls' ) );
        $data = array();
        include $file;
        $zones = array();
        foreach ( $data['explayouts_zone']['rows'] as $row )
        {
            $z = array_combine( $data['explayouts_zone']['fields'], $row );
            $zones[$z['id']] = array( $z['layout_id'], $z['identifier'] );
        }
        $layouts = array();
        foreach ( $data['explayouts_layout']['rows'] as $row )
        {
            $l = array_combine( $data['explayouts_layout']['fields'], $row );
            $layouts[$l['id']] = $l['identifier'];
        }
        $headers = array();
        foreach ( $data['explayouts_block']['rows'] as $row )
        {
            $b = array_combine( $data['explayouts_block']['fields'], $row );
            if ( $zones[$b['zone_id']][1] === 'header' )
                $headers[$layouts[$b['layout_id']]][(int)$b['position']] = $b['definition_identifier'];
        }
        foreach ( $headers as $k => $blocks )
        {
            ksort( $blocks );
            $headers[$k] = array_values( $blocks );
        }
        return $headers;
    }

    public function testAdmin4DrawsTheOrderThisTestHolds()
    {
        $tpl = self::expand( self::read( 'design/admin4/templates/page_topmenu.tpl' ) );
        $this->assertSame( self::ORDER, self::partsOf( $tpl ) );
    }

    public function testThePageDrawsTheLogoAndTheMenuButtonBeforeTheMenuAndTheRestAfterIt()
    {
        $this->assertSame( array( 'header_start', 'menu', 'header_html' ), self::navbarOrder() );
        $passes = self::passes();
        $this->assertSame( array( 'header_start', 'header_html' ), array_keys( $passes ) );
        $this->assertSame( array( 'admin_logo', 'admin_menu_button' ), $passes['header_start']['only'] );
        $this->assertSame( array( 'admin_logo', 'admin_menu_button' ), $passes['header_html']['skip'] );
    }

    public function testTheStockLayoutsHaveAdmin4sTabOrder()
    {
        $headers = self::stockHeaders();
        $this->assertNotEmpty( $headers );
        foreach ( $headers as $layout => $blocks )
        {
            $this->assertSame( self::ORDER, self::admin4lOrder( $blocks ), "layout $layout" );
            $this->assertNotContains( 'admin_sidebar_toggles', $blocks, "layout $layout: the split blocks, not the old one" );
        }
    }

    /** A layout stored before the split has the old block: it stays drawn, once, after the tools (the old order) */
    public function testTheOldCombinedBlockIsStillDrawnOnce()
    {
        $order = self::admin4lOrder( array( 'admin_logo', 'admin_search', 'admin_theme_switch', 'admin_sidebar_toggles' ) );
        $this->assertSame( array( 'logo', 'menu', 'search', 'site', 'theme', 'user', 'menu-btn', 'controls' ), $order );
        $this->assertSame( self::partsOfBlock( 'admin_menu_button' ), array( 'menu-btn' ) );
        $this->assertSame( self::partsOfBlock( 'admin_sidebar_controls' ), array( 'controls' ) );
        $this->assertSame( array( 'menu-btn', 'controls' ), self::partsOfBlock( 'admin_sidebar_toggles' ), 'the old block draws both' );
    }

    public function testTheCombinedBlockIsMadeOfTheTwoNewOnes()
    {
        $old = self::read( 'design/admin4l/templates/explayouts/block/admin_sidebar_toggles.tpl' );
        foreach ( array( 'admin_menu_button', 'admin_sidebar_controls' ) as $block )
            $this->assertStringContainsString( "{include uri='design:explayouts/block/$block.tpl'}", $old );
    }
}
