<?php
/**
 * The administration (admin4, admin4l) runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md): script-src 'self' runs no inline <script>, no on...= attribute and
 * no javascript: URL; style-src 'self' applies no style="" attribute. Checked on the files alone; no database.
 *
 * - CSP-01: the templates admin4 and admin4l render - their own and those they take from admin3, admin and standard
 *   (the first design of the chain that has a path wins), in templates/ and override/templates/ - carry no inline
 *   script, no event handler attribute, no javascript: URL, no style="" attribute and no <style> element. Template comments {* ... *}
 *   are not read; a <script> with a src and a <template> are fine. EXCEPTIONS are permanent and say why; ALLOWLIST
 *   holds what is still to be moved and only shrinks: a listed file with fewer findings than listed fails until its
 *   entry is lowered or removed.
 * - CSP-02: the scripts of admin4 (design/admin4/javascript) and the standard scripts it loads build no inline code:
 *   no document.write, eval(), new Function or string timer, and no markup with an on...= or style="" attribute.
 * - CSP-03: the head script (color mode, preferences) is a file loaded before the stylesheets, and the small actions
 *   (a4_actions.js) are loaded on every page.
 * - CSP-04 to CSP-06 run the scripts in node against a small stand-in for the DOM (js/mini_dom.js): the data-a4-*
 *   actions (js/csp_actions.js), a popup menu declared in data-menu-* attributes and the ways extensions still add to
 *   menus (js/csp_popupmenu.js), and the content tree drawn from its data attributes with its open/close buttons
 *   (js/csp_tree.js).
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminContentSecurityPolicyTest extends PHPUnit\Framework\TestCase
{
    /** The design chains of the administration designs, as uiswitch.ini [UISwitch] DesignChain[] and standard */
    const CHAINS = array(
        'admin4' => array( 'admin4', 'admin3', 'admin', 'standard' ),
        'admin4l' => array( 'admin4l', 'admin4', 'admin3', 'admin', 'standard' ),
    );

    /** Templates that are no administration page, path prefix => why */
    const EXCEPTIONS = array(
        'design/standard/templates/mail/' => 'e-mail HTML: mail programs need inline styles and run no script',
        'design/standard/templates/mailpreferences/mail/' => 'e-mail HTML: mail programs need inline styles and run no script',
        'design/standard/templates/setup_pagelayout.tpl' => 'the setup wizard, which runs before an administration exists, under its own pagelayout',
        'design/standard/templates/setup/init/' => 'the setup wizard, which runs before an administration exists, under its own pagelayout',
        'design/standard/templates/setup/page_copyright.tpl' => 'the setup wizard, which runs before an administration exists, under its own pagelayout',
        'design/standard/templates/shop/orderreceipt_document.tpl' => 'a receipt as a file to keep: a whole HTML document that carries its own styles',
        'design/standard/templates/page_head_displaystyles.tpl' => 'the head of the standard pagelayout (the sites); admin4 and admin4l have their own',
        'design/standard/templates/contentstructuremenu/content_structure_menu.tpl' => 'the static tree of the standard design; admin4 and admin4l draw the dynamic tree (content_structure_menu_dynamic.tpl)',
    );

    /**
     * Templates still carrying inline code, path => array( kind => count ) for script, handler, jsurl, style,
     * styleblock. This
     * list only shrinks; it is empty when the work is done.
     */
    const ALLOWLIST = array(
        // content output: the width of a rich text table, the alignment of its cells and the border of an image come
        // from the content; under an enforced policy the browser drops these declarations
        'design/standard/templates/content/datatype/view/ezxmltags/table.tpl' => array( 'style' => 1 ),
        'design/standard/templates/content/datatype/view/ezxmltags/td.tpl' => array( 'style' => 1 ),
        'design/standard/templates/content/datatype/view/ezimage.tpl' => array( 'style' => 1 ),
        // the receipt's styles, shared with the receipt as a file to keep (orderreceipt_document.tpl)
        'design/standard/templates/shop/orderreceipt_style.tpl' => array( 'styleblock' => 1 ),
    );

    /** The standard scripts admin4 loads (design.ini [JavaScriptSettings] BackendJavaScriptList[] and the trees) */
    const STANDARD_SCRIPTS = array(
        'design/standard/javascript/tools/ezjsselection.js',
        'design/standard/javascript/lib/ezjslibimagepreloader.js',
        'design/standard/javascript/contentstructuremenu/contentstructuremenu.js',
    );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /** The templates a design chain renders: relative path => file of the first design that has it */
    private static function effective( array $chain )
    {
        $root = self::root();
        $files = array();
        foreach ( $chain as $design )
        {
            self::assertDirectoryExists( "$root/design/$design/templates", "design/$design is missing" );
            foreach ( array( 'templates', 'override/templates' ) as $sub )
            {
                $dir = "$root/design/$design/$sub";
                if ( !is_dir( $dir ) )
                    continue;
                $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
                foreach ( $it as $file )
                {
                    if ( substr( $file->getFilename(), -4 ) !== '.tpl' )
                        continue;
                    $relative = $sub . substr( $file->getPathname(), strlen( $dir ) );
                    if ( !isset( $files[$relative] ) )
                        $files[$relative] = substr( $file->getPathname(), strlen( $root ) + 1 );
                }
            }
        }
        return $files;
    }

    /**
     * The inline code of a template, kind => count: inline <script> elements (no src; JSON data blocks and templates
     * are not code), event handler attributes, javascript: URLs, style="" attributes, <style> elements
     */
    public static function findings( $template )
    {
        $code = (string)preg_replace( '/\{\*.*?\*\}/s', '', $template );
        $found = array( 'script' => 0, 'handler' => 0, 'jsurl' => 0, 'style' => 0, 'styleblock' => 0 );
        if ( preg_match_all( '/<script\b([^>]*)>/i', $code, $scripts ) )
        {
            foreach ( $scripts[1] as $attributes )
            {
                if ( !preg_match( '/\ssrc\s*=/i', $attributes ) && !preg_match( '/\stype\s*=\s*["\']?(application\/(ld\+)?json|text\/template)/i', $attributes ) )
                    $found['script']++;
            }
        }
        // every handler attribute of every tag
        if ( preg_match_all( '/<[a-z][^<>]*>/is', $code, $tags ) )
        {
            $found['handler'] = 0;
            foreach ( $tags[0] as $tag )
                $found['handler'] += preg_match_all( '/\son[a-z]+\s*=\s*["\'{]/i', $tag );
        }
        $found['jsurl'] = preg_match_all( '/\b(?:href|action|src|formaction)\s*=\s*["\']?\s*javascript:/i', $code );
        $found['style'] = preg_match_all( '/<[a-z][^<>]*?\sstyle\s*=/is', $code );
        $found['styleblock'] = preg_match_all( '/<style\b/i', $code );
        return array_filter( $found );
    }

    private static function excepted( $path )
    {
        foreach ( array_keys( self::EXCEPTIONS ) as $prefix )
        {
            if ( strpos( $path, $prefix ) === 0 )
                return true;
        }
        return false;
    }

    /** CSP-01 */
    public function testTheAdministrationTemplatesCarryNoInlineCode()
    {
        $paths = array();
        foreach ( self::CHAINS as $chain )
        {
            foreach ( self::effective( $chain ) as $path )
                $paths[$path] = true;
        }
        $this->assertGreaterThan( 400, count( $paths ), 'the template chains were not read' );

        $unexpected = array();
        $fewer = array();
        foreach ( array_keys( $paths ) as $path )
        {
            if ( self::excepted( $path ) )
                continue;
            $found = self::findings( (string)file_get_contents( self::root() . '/' . $path ) );
            $allowed = isset( self::ALLOWLIST[$path] ) ? self::ALLOWLIST[$path] : array();
            foreach ( array( 'script', 'handler', 'jsurl', 'style', 'styleblock' ) as $kind )
            {
                $now = isset( $found[$kind] ) ? $found[$kind] : 0;
                $listed = isset( $allowed[$kind] ) ? $allowed[$kind] : 0;
                if ( $now > $listed )
                    $unexpected[] = "$path: $now $kind (allowed: $listed)";
                else if ( $now < $listed )
                    $fewer[] = "$path: $now $kind, listed with $listed - lower or remove its ALLOWLIST entry";
            }
        }
        foreach ( array_keys( self::ALLOWLIST ) as $path )
        {
            if ( !isset( $paths[$path] ) )
                $fewer[] = "$path is listed but not rendered by admin4 or admin4l any more";
        }
        $this->assertSame( array(), $unexpected,
            'Inline code in an administration template: move scripts into a file (ezscript), handlers to a listener '
            . 'or the data-a4-* actions of a4_actions.js, styles to a class (doc/features/6.0/content-security-policy.md)' );
        $this->assertSame( array(), $fewer );
    }

    /** CSP-01: what counts as inline code */
    public function testTheFindingsAreCounted()
    {
        $this->assertSame( array(), self::findings( '<script src="/a.js"></script><script type="application/json">{}</script>'
                                                    . '<a href="/x" data-onclick="y" class="onclick">on="x"</a>{* <script>x</script> onclick="y" *}' ) );
        $this->assertSame( array( 'script' => 2, 'handler' => 3, 'jsurl' => 1, 'style' => 1 ),
                           self::findings( "<script>var a;</script><script type=\"text/javascript\">b()</script>"
                                           . "<a href=\"javascript:void(0)\" onclick=\"x()\" onmouseover='y()'>a</a>"
                                           . "<div style=\"color: red\" onclick={concat( 'a' )}></div>" ) );
        $this->assertSame( array( 'styleblock' => 1 ), self::findings( '<style>a { color: red; }</style>' ) );
    }

    /** The scripts of a directory, comments removed */
    private static function scripts( $dir )
    {
        $files = array();
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . "/$dir", FilesystemIterator::SKIP_DOTS ) );
        foreach ( $it as $file )
        {
            if ( substr( $file->getFilename(), -3 ) === '.js' )
                $files[] = substr( $file->getPathname(), strlen( self::root() ) + 1 );
        }
        sort( $files );
        return $files;
    }

    /** A script without its comments (block comments and line comments that start a line or follow code) */
    private static function code( $path )
    {
        $js = (string)file_get_contents( self::root() . '/' . $path );
        $js = (string)preg_replace( '#/\*.*?\*/#s', '', $js );
        return (string)preg_replace( '#(^|[\s;{}(),])//[^\n]*#', '$1', $js );
    }

    /** CSP-02 */
    public function testTheAdministrationScriptsBuildNoInlineCode()
    {
        $files = array_merge( self::scripts( 'design/admin4/javascript' ), self::STANDARD_SCRIPTS );
        $this->assertGreaterThan( 10, count( $files ) );
        $patterns = array(
            'document.write' => '/document\.write(ln)?\s*\(/',
            'eval()' => '/(^|[^\w.])eval\s*\(/',
            'new Function' => '/\bnew\s+Function\s*\(/',
            'a string timer' => '/\bset(Timeout|Interval)\s*\(\s*["\']/',
            'an event handler attribute in markup' => '/\son[a-z]+\s*=\s*\\\\?["\']/',
            'a style attribute in markup' => '/\sstyle\s*=\s*\\\\?["\']/',
            'a javascript: URL' => '/["\']\s*javascript:/i',
        );
        $found = array();
        foreach ( $files as $path )
        {
            $this->assertFileExists( self::root() . '/' . $path );
            $js = self::code( $path );
            foreach ( $patterns as $name => $pattern )
            {
                if ( preg_match( $pattern, $js, $match ) )
                    $found[] = "$path: $name (" . trim( $match[0] ) . ')';
            }
        }
        $this->assertSame( array(), $found );
    }

    /** CSP-03 */
    public function testTheHeadScriptAndTheActionsAreFiles()
    {
        $style = (string)file_get_contents( self::root() . '/design/admin4/templates/page_head_style.tpl' );
        $load = strpos( $style, "{ezscript( array( 'a4_head.js' ) )}" );
        $this->assertNotFalse( $load, 'page_head_style.tpl loads a4_head.js' );
        $this->assertLessThan( strpos( $style, 'ezcss_load' ), $load, 'the color mode is set before the stylesheets load' );
        $head = (string)file_get_contents( self::root() . '/design/admin4/javascript/a4_head.js' );
        $this->assertStringContainsString( 'window.a4Pref = {', $head );
        $this->assertStringContainsString( "root.setAttribute('data-a4-theme', dark ? 'dark' : 'light')", $head );

        $script = (string)file_get_contents( self::root() . '/design/admin4/templates/page_head_script.tpl' );
        $this->assertStringContainsString( "{ezscript_load( array( 'a4_actions.js' ) )}", $script );
        foreach ( array( 'pagelayout.tpl', 'loginpagelayout.tpl' ) as $layout )
        {
            $tpl = (string)file_get_contents( self::root() . "/design/admin4/templates/$layout" );
            $this->assertStringContainsString( "{include uri='design:page_head_style.tpl'}", $tpl, $layout );
            $this->assertStringContainsString( "{include uri='design:page_head_script.tpl'}", $tpl, $layout );
        }
    }
    /** The JSON lines a harness of js/ printed, by case name */
    private function harness( $script, $file )
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . "/js/$script" ) . ' '
                                   . escapeshellarg( self::root() . "/$file" ) . ' 2>&1' );
        $cases = array();
        foreach ( array_filter( explode( "\n", $out ) ) as $line )
        {
            $row = json_decode( $line, true );
            $this->assertIsArray( $row, "the harness ran: $out" );
            $cases[$row['name']] = $row;
        }
        return $cases;
    }

    /** CSP-04 */
    public function testTheActionsWork()
    {
        $c = $this->harness( 'csp_actions.js', 'design/admin4/javascript/a4_actions.js' );
        $this->assertCount( 11, $c );
        $this->assertSame( array( 'Remove?', 'Remove?' ), $c['confirm link']['asked'] );
        $this->assertTrue( $c['confirm link']['cancelledPrevented'] );
        $this->assertFalse( $c['confirm link']['confirmedPrevented'] );
        $this->assertSame( 0, $c['confirm button']['cancelled'], 'a cancelled confirmation does not send the form' );
        $this->assertSame( 1, $c['confirm button']['confirmed'] );
        $this->assertTrue( $c['confirm form']['cancelledPrevented'] );
        $this->assertFalse( $c['confirm form']['confirmedPrevented'] );
        $this->assertSame( '0', $c['discard']['value'], 'the confirmed discard skips the page that asks again' );
        $this->assertTrue( $c['discard']['prevented'] );
        // inverted: only the enabled boxes of that name, in the form of the link or the one it names
        $this->assertSame( array( true, true, false, false, true ), array( $c['toggle']['prevented'], $c['toggle']['c1'], $c['toggle']['c2'], $c['toggle']['c3'], $c['toggle']['c4'] ) );
        $this->assertSame( array( true, true ), $c['check all']['on'] );
        $this->assertSame( array( false, false ), $c['check all']['off'] );
        $this->assertTrue( $c['dismiss']['parent'] );
        $this->assertTrue( $c['dismiss']['named'] );
        $this->assertSame( array( 'f' ), $c['change']['submitted'] );
        $this->assertSame( '/setup/info', $c['change']['location'] );
        $this->assertSame( '0', $c['change']['reset'] );
        $this->assertSame( 1, $c['print']['prints'] );
        // the button's own click goes on (its name is sent), the buttons are disabled after it
        $this->assertFalse( $c['disable on submit']['prevented'] );
        $this->assertSame( 1, $c['disable on submit']['sent'] );
        $this->assertFalse( $c['disable on submit']['before'] );
        $this->assertSame( array( true, true ), $c['disable on submit']['after'] );
        $this->assertSame( 'b', $c['focus']['active'] );
    }

    /** CSP-05 */
    public function testADeclaredPopupMenuWorks()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $c = $this->harness( 'csp_popupmenu.js', "design/$design/javascript/popupmenu/ezpopupmenu.js" );
            $this->assertCount( 10, $c, $design );
            // an extension's script adds to a declared menu: the menu is created from its attributes
            $this->assertSame( 0, $c['extension entry']['depth'] );
            $this->assertSame( 'M-head', $c['extension entry']['header'] );
            $open = $c['open'];
            $this->assertTrue( $open['prevented'] && $open['shown'] );
            $this->assertSame( 'Tom & Jerry <i>', $open['header'], 'the title is text' );
            $this->assertSame( '/content/edit/7', $open['edit'] );
            $this->assertSame( '/extension/5', $open['ext'] );
            $this->assertSame( array( 'menu-item-disabled', '#', 'true' ), $open['off'], 'disabled by data-menu-disable' );
            $this->assertSame( array( 'menu-item-disabled', 'true' ), $open['create'], 'disabled by data-menu-disable-item' );
            $this->assertSame( 'menu', $open['role'] );
            $this->assertTrue( $c['submenu']['shown'] );
            $this->assertSame( 'true', $c['submenu']['opener'] );
            // the entries of a looping variable: values as text and attribute values, from the <template>
            $this->assertSame( '<a href="/content/edit/7/f/eng-GB" role="menuitem" tabindex="-1">English &lt;b&gt;"UK"</a>', $c['submenu']['items'] );
            $this->assertFalse( $c['pointing at another item']['submenuShown'] );
            $this->assertTrue( $c['pointing at another item']['menuShown'] );
            $this->assertFalse( $c['disabled item']['submenuShown'], 'a disabled item opens no submenu' );
            $this->assertTrue( $c['legacy item']['submenuShown'], 'an item with its own onmouseover is left to it' );
            $this->assertTrue( $c['form']['prevented'] );
            $this->assertSame( array( array( 'id' => 'move-form', 'action' => '/content/action/5', 'values' => array( 'ContentNodeID=5' ) ) ), $c['form']['sent'] );
            $this->assertSame( '/content/action/%nodeID%', $c['form']['actionAfter'], 'the form is put back for the next item' );
            $this->assertSame( '%nodeID%', $c['form']['valueAfter'] );
            // the class id of an entry reaches the form, also with a quote in it (JSON-escaped)
            $this->assertSame( '["classID","12\\"x"]', $c['create here']['values'] );
            $this->assertSame( array( 'ClassID=12"x', 'NodeID=9' ), $c['create here']['sent'][0]['values'] );
            $this->assertSame( '/visual/templatecreate/(nodeID)/9', $c['redirect']['location'] );
            $this->assertTrue( $c['redirect']['hidden'] );
        }
    }

    /** CSP-05: the declared menus live in menuArray, which an example must not tell anyone to assign anew */
    public function testTheMenuScriptDoesNotSuggestAssigningMenuArray()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $js = (string)file_get_contents( self::root() . "/design/$design/javascript/popupmenu/ezpopupmenu.js" );
            $this->assertDoesNotMatchRegularExpression( '/^\s*(var\s+)?menuArray\s*=\s*(new Array\(\)|\{\})\s*;/m', $js, $design );
            $this->assertStringContainsString( 'never assign it anew', $js, $design );
            $this->assertStringContainsString( 'window.menuArray = _declaringMenus(', $js, $design );
        }
    }

    /** CSP-06 */
    public function testTheTreeIsDrawnFromItsData()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $c = $this->harness( 'csp_tree.js', "design/$design/javascript/contentstructuremenu_dynamic.js" );
            $this->assertCount( 5, $c, $design );
            $this->assertTrue( $c['drawn']['treeMenu'], 'window.treeMenu is set, as by the inline script before' );
            $this->assertSame( 'tree', $c['drawn']['role'] );
            $this->assertSame( 'Content tree', $c['drawn']['label'] );
            $this->assertTrue( $c['drawn']['root'] );
            $this->assertFalse( $c['drawn']['handlers'] );
            $this->assertSame( array( '/content/treemenu/2/100/1/abc' ), $c['drawn']['requests'] );
            $this->assertSame( array( '5', '7' ), array( $c['sub items']['load'], $c['sub items']['modified'] ) );
            $this->assertFalse( $c['sub items']['handlers'], 'the open/close buttons carry no handler attribute' );
            $this->assertFalse( $c['sub items']['hiddenNodeShown'], 'hideNodes (strings in the data) still hides node 43' );
            $this->assertTrue( $c['click']['prevented'] );
            $this->assertSame( array( '/content/treemenu/5/7/1/abc' ), $c['click']['requests'] );
            $this->assertSame( 'openclose-close', $c['loaded']['state'] );
            $this->assertSame( 'true', $c['loaded']['expanded'] );
            $this->assertSame( array( '/content/treemenu/8/3/1/abc' ), $c['loaded']['reopened'], 'a node remembered as open opens again' );
            $this->assertTrue( $c['space closes']['prevented'] );
            $this->assertSame( 'openclose-open', $c['space closes']['state'] );
            $this->assertSame( 'hidden', $c['space closes']['container'] );
        }
    }
}
