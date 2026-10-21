<?php
/**
 * The administration (admin4, admin4l) runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md): script-src 'self' runs no inline <script>, no on...= attribute and
 * no javascript: URL; style-src 'self' applies no style="" attribute. Checked on the files alone; no database.
 *
 * - CSP-01: the templates admin4 and admin4l render carry no inline script, no event handler attribute, no
 *   javascript: URL, no style="" attribute and no <style> element. The chain is the one the kernel builds
 *   (eZTemplateDesignResource::findDesignBase()): the designs of uiswitch.ini [UISwitch] DesignChain[] and standard,
 *   and for each design first the design directories of the extensions (design.ini [ExtensionSettings]
 *   DesignExtensions[] of the active extensions), then design/<design>; the first base that has a path wins, in
 *   templates/ and override/templates/. Active are the extensions of site.ini [ExtensionSettings] ActiveExtensions[]
 *   with settings/override, and for every siteaccess whose SiteDesign is admin4 or admin4l also its
 *   ActiveAccessExtensions[]; a checkout without settings/override (as distributed) counts every extension it
 *   carries. Only the extensions this repository ships (SHIPPED_EXTENSIONS) are checked; installed extensions of
 *   their own repositories only with CSP_ALL_EXTENSIONS=1 (an audit of an installation). Template comments
 *   {* ... *} are not read; a <script> with a src, a JSON data block and a <template>
 *   are fine. EXCEPTIONS are permanent and say why; ALLOWLIST (core templates) and EXTENSION_ALLOWLIST (per
 *   extension, with the reason) hold what is still to be moved and only shrink: a listed file with fewer findings
 *   than listed, or no longer rendered, fails until its entry is lowered or removed.
 * - CSP-01: the chain itself - admin2 is part of it, an extension's design directory comes before the core directory
 *   of the same design, and no two active extensions carry the same template of one design (their order among each
 *   other is the kernel's loading order, which this test does not rebuild).
 * - CSP-02: the scripts of admin4 (design/admin4/javascript) and the standard scripts it loads build no inline code:
 *   no document.write, eval(), new Function or string timer, and no markup with an on...= or style="" attribute.
 * - CSP-03: the head script (color mode, preferences) is a file loaded before the stylesheets, and the small actions
 *   (a4_actions.js) are loaded on every page.
 * - CSP-04 to CSP-06 run the scripts in node against a small stand-in for the DOM (js/mini_dom.js): the data-a4-*
 *   actions (js/csp_actions.js), a popup menu declared in data-menu-* attributes and the ways extensions still add to
 *   menus, which link and redirect only to addresses of the site (js/csp_popupmenu.js), and the content tree drawn from its data attributes with its open/close buttons
 *   (js/csp_tree.js).
 * - CSP-09: every JSON data block (<script type="application/json">) of a rendered template prints one variable
 *   through JSON_ESCAPE, and that chain, rendered by eZTemplate, leaves no < or > of a value in the block.
 * - CSP-07: the TinyMCE 8 editor (ezoe) starts from ezoe8_start.js: the edit template writes its settings as JSON
 *   data blocks and each textarea's toolbar in data-ezoe8-* attributes; run in node (js/csp_tinymce8.js).
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminNodeBinary.php';

class AdminContentSecurityPolicyTest extends PHPUnit\Framework\TestCase
{
    /**
     * The administration designs and their fallback designs as uiswitch.ini [UISwitch] DesignChain[] ships them, for
     * a tree without that setting; the test reads DesignChain[] where it is set
     */
    const DESIGNS = array(
        'admin4' => 'admin3;admin2;admin',
        'admin4l' => 'admin4;admin3;admin2;admin',
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

    /** The extensions this repository ships (share/filelist.md5): checked always; an allowlisted one missing fails under CI=1 */
    const SHIPPED_EXTENSIONS = array( 'expservices', 'ezformtoken', 'ezjscore', 'ezoe' );

    /**
     * Templates of extensions still carrying inline code: extension => array( 'why' => what is left and why, 'files'
     * => array( path below the extension directory => array( kind => count ) ) ). Like ALLOWLIST this only shrinks;
     * an extension that is not in the checkout is not checked.
     */
    const EXTENSION_ALLOWLIST = array(
        'ezoe' => array(
            'why' => 'the TinyMCE 3 engine (ezoe.ini EditorEngine=tinymce3) and its dialogs (ezoe/*.tpl under '
                . 'ezoe/popup_pagelayout.tpl): tiny_mce.js and tiny_mce_popup.js build their toolbars, menus and dialogs with '
                . 'javascript: links and onclick attributes themselves, so moving these templates into files would not '
                . 'let TinyMCE 3 run under the policy; the TinyMCE 8 engine starts from ezoe8_start.js',
            'files' => array(
                'design/standard/templates/content/datatype/edit/ezxmltext_ezoe.tpl' => array( 'script' => 2 ),
                'design/standard/templates/ezoe/box_bookmarks.tpl' => array( 'script' => 1, 'jsurl' => 1, 'style' => 4 ),
                'design/standard/templates/ezoe/box_browse.tpl' => array( 'script' => 1, 'jsurl' => 3, 'style' => 6 ),
                'design/standard/templates/ezoe/box_search.tpl' => array( 'handler' => 2, 'jsurl' => 1, 'style' => 4 ),
                'design/standard/templates/ezoe/customattributes.tpl' => array( 'style' => 1 ),
                'design/standard/templates/ezoe/customattributes/color.tpl' => array( 'handler' => 3, 'jsurl' => 1, 'style' => 1 ),
                'design/standard/templates/ezoe/customattributes/cssborder.tpl' => array( 'script' => 1, 'handler' => 3, 'jsurl' => 1, 'style' => 1 ),
                'design/standard/templates/ezoe/customattributes/csssize.tpl' => array( 'script' => 1 ),
                'design/standard/templates/ezoe/customattributes/csssize4.tpl' => array( 'script' => 1 ),
                'design/standard/templates/ezoe/customattributes/htmlsize.tpl' => array( 'script' => 1 ),
                'design/standard/templates/ezoe/customattributes/link.tpl' => array( 'script' => 1, 'jsurl' => 3 ),
                'design/standard/templates/ezoe/help.tpl' => array( 'script' => 1, 'jsurl' => 3, 'style' => 2 ),
                'design/standard/templates/ezoe/merge_cells.tpl' => array( 'script' => 1, 'handler' => 2, 'style' => 3 ),
                'design/standard/templates/ezoe/popup_pagelayout.tpl' => array( 'styleblock' => 1 ),
                'design/standard/templates/ezoe/tag_anchor.tpl' => array( 'script' => 1, 'jsurl' => 2, 'style' => 1 ),
                'design/standard/templates/ezoe/tag_custom.tpl' => array( 'script' => 1, 'jsurl' => 2, 'style' => 2 ),
                'design/standard/templates/ezoe/tag_embed_files.tpl' => array( 'script' => 1, 'jsurl' => 3, 'style' => 3 ),
                'design/standard/templates/ezoe/tag_embed_images.tpl' => array( 'script' => 1, 'jsurl' => 3, 'style' => 3 ),
                'design/standard/templates/ezoe/tag_embed_objects.tpl' => array( 'script' => 1, 'jsurl' => 3, 'style' => 3 ),
                'design/standard/templates/ezoe/tag_general.tpl' => array( 'script' => 1, 'jsurl' => 2, 'style' => 1 ),
                'design/standard/templates/ezoe/tag_header.tpl' => array( 'script' => 1, 'jsurl' => 2, 'style' => 1 ),
                'design/standard/templates/ezoe/tag_link.tpl' => array( 'script' => 1, 'jsurl' => 2, 'style' => 2 ),
                'design/standard/templates/ezoe/tag_table.tpl' => array( 'script' => 1, 'jsurl' => 2, 'style' => 2 ),
                'design/standard/templates/ezoe/tag_table_cell.tpl' => array( 'script' => 1, 'jsurl' => 2, 'style' => 1 ),
                'design/standard/templates/ezoe/upload/common_attributes.tpl' => array( 'script' => 1, 'style' => 2 ),
                'design/standard/templates/ezoe/upload_files.tpl' => array( 'script' => 1, 'handler' => 1, 'jsurl' => 5, 'style' => 3 ),
                'design/standard/templates/ezoe/upload_images.tpl' => array( 'script' => 1, 'handler' => 1, 'jsurl' => 5, 'style' => 3 ),
                'design/standard/templates/ezoe/upload_objects.tpl' => array( 'script' => 1, 'handler' => 1, 'jsurl' => 5, 'style' => 3 ),
            ),
        ),
    );

    /**
     * How a template prints a JSON data block: one variable (a set-block) with < and > written as \u003c and \u003e.
     * json_encode leaves "<!--" and "<script" as they are, and "<!--<script>" in a value makes the browser read past
     * the block's </script>, so the scripts after it never run.
     */
    const JSON_ESCAPE = "|explode( '<' )|implode( '\\u003c' )|explode( '>' )|implode( '\\u003e' )";

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

    /**
     * The values of an array setting in INI files read in this order, as eZINI merges them: "Key[]" empties the list,
     * "Key[]=value" adds to it. Files that do not exist are skipped.
     */
    private static function iniList( array $files, $section, $key )
    {
        $values = array();
        foreach ( $files as $file )
        {
            if ( !is_file( $file ) )
                continue;
            $current = '';
            foreach ( preg_split( '/\R/', (string)file_get_contents( $file ) ) as $line )
            {
                $line = trim( $line );
                if ( preg_match( '/^\[([^\]]+)\]$/', $line, $m ) )
                    $current = trim( $m[1] );
                else if ( $current === $section && preg_match( '/^' . preg_quote( $key, '/' ) . '\[\]\s*(=(.*))?$/', $line, $m ) )
                {
                    if ( isset( $m[1] ) && $m[1] !== '' )
                        $values[] = trim( $m[2] );
                    else
                        $values = array();
                }
            }
        }
        return $values;
    }

    /** A single setting in INI files read in this order, the last file that sets it wins; null when none does */
    private static function iniValue( array $files, $section, $key )
    {
        $value = null;
        foreach ( $files as $file )
        {
            if ( !is_file( $file ) )
                continue;
            $current = '';
            foreach ( preg_split( '/\R/', (string)file_get_contents( $file ) ) as $line )
            {
                $line = trim( $line );
                if ( preg_match( '/^\[([^\]]+)\]$/', $line, $m ) )
                    $current = trim( $m[1] );
                else if ( $current === $section && preg_match( '/^' . preg_quote( $key, '/' ) . '=(.*)$/', $line, $m ) )
                    $value = trim( $m[1] );
            }
        }
        return $value;
    }

    /** The INI files of one name in a settings directory: name, name.append, name.append.php */
    private static function iniFiles( $dir, $name )
    {
        return array( "$dir/$name", "$dir/$name.append", "$dir/$name.append.php" );
    }

    /**
     * The designs of a chain a user chooses (uiswitch.ini): the design, its DesignChain[] entry (DESIGNS without that
     * setting), standard
     */
    public static function chain( $design )
    {
        $root = self::root();
        $chain = self::DESIGNS[$design];
        foreach ( array_merge( self::iniFiles( "$root/settings", 'uiswitch.ini' ), self::iniFiles( "$root/settings/override", 'uiswitch.ini' ) ) as $file )
        {
            if ( is_file( $file ) && preg_match( '/^DesignChain\[' . preg_quote( $design, '/' ) . '\]=(.*)$/m', (string)file_get_contents( $file ), $m ) )
                $chain = trim( $m[1] );
        }
        return array_values( array_unique( array_merge( array( $design ), array_filter( array_map( 'trim', explode( ';', $chain ) ) ), array( 'standard' ) ) ) );
    }

    /** True when the checkout has an extension of that name */
    private static function hasExtension( $name )
    {
        return $name !== '' && is_dir( self::root() . "/extension/$name" );
    }

    /**
     * True when the templates of an extension are checked: the extensions this repository ships (SHIPPED_EXTENSIONS,
     * the ones share/filelist.md5 lists). Extensions installed beside them come from repositories of their own and
     * are checked there, so their state cannot fail this repository's tests. CSP_ALL_EXTENSIONS=1 checks every
     * extension of the checkout, to audit an installation:
     *   CSP_ALL_EXTENSIONS=1 php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php
     */
    public static function checkedExtension( $name )
    {
        if ( !self::hasExtension( $name ) )
            return false;
        return getenv( 'CSP_ALL_EXTENSIONS' ) ? true : in_array( $name, self::SHIPPED_EXTENSIONS, true );
    }

    /**
     * What an administration renders with, label => array( active extensions in activation order, chain of designs ):
     * for every siteaccess whose SiteDesign is one of DESIGNS its own chain (SiteDesign, AdditionalSiteDesignList[],
     * StandardDesign) with the global extensions and its ActiveAccessExtensions[], and with the same extensions the
     * chain of every design of DESIGNS a user may switch to; without such a siteaccess (as distributed) the chains of
     * DESIGNS with the global extensions
     */
    public static function variants()
    {
        $root = self::root();
        $siteFiles = array_merge( self::iniFiles( "$root/settings", 'site.ini' ), self::iniFiles( "$root/settings/override", 'site.ini' ) );
        $global = self::iniList( $siteFiles, 'ExtensionSettings', 'ActiveExtensions' );
        if ( !is_dir( "$root/settings/override" ) )
        {
            // as distributed: the setup wizard activates the extensions the checkout carries
            foreach ( glob( "$root/extension/*", GLOB_ONLYDIR ) as $dir )
                $global[] = basename( $dir );
        }
        // The extensions this repository ships are always checked, also when settings/override exists without
        // listing them (other tests of a full run create that directory, and the chain then lost ezoe)
        $global = array_merge( $global, self::SHIPPED_EXTENSIONS );
        $global = array_values( array_unique( array_filter( $global, array( __CLASS__, 'checkedExtension' ) ) ) );

        // the settings directories of every siteaccess: settings/siteaccess/<name> and those of the active extensions
        $siteAccessDirs = array();
        foreach ( array_merge( array( "$root/settings" ), array_map( function ( $e ) use ( $root ) { return "$root/extension/$e/settings"; }, $global ) ) as $settings )
        {
            foreach ( glob( "$settings/siteaccess/*", GLOB_ONLYDIR ) ?: array() as $dir )
                $siteAccessDirs[basename( $dir )][] = $dir;
        }
        ksort( $siteAccessDirs );
        $variants = array();
        foreach ( $siteAccessDirs as $siteAccess => $dirs )
        {
            $files = array( "$root/settings/site.ini" );
            foreach ( $dirs as $dir )
                $files = array_merge( $files, self::iniFiles( $dir, 'site.ini' ) );
            $files = array_merge( $files, self::iniFiles( "$root/settings/override", 'site.ini' ) );
            $siteDesign = self::iniValue( $files, 'DesignSettings', 'SiteDesign' );
            if ( !isset( self::DESIGNS[$siteDesign] ) )
                continue;
            $access = self::iniList( $files, 'ExtensionSettings', 'ActiveAccessExtensions' );
            $active = array_values( array_unique( array_merge( $global, array_filter( $access, array( __CLASS__, 'checkedExtension' ) ) ) ) );
            $chain = array_merge( array( $siteDesign ), self::iniList( $files, 'DesignSettings', 'AdditionalSiteDesignList' ),
                                  array( (string)self::iniValue( $files, 'DesignSettings', 'StandardDesign' ) ) );
            $variants[$siteAccess] = array( $active, array_values( array_unique( array_filter( $chain ) ) ) );
            foreach ( array_keys( self::DESIGNS ) as $design )
                $variants["$siteAccess, $design chosen"] = array( $active, self::chain( $design ) );
        }
        if ( !$variants )
        {
            foreach ( array_keys( self::DESIGNS ) as $design )
                $variants[$design] = array( $global, self::chain( $design ) );
        }
        return $variants;
    }

    /**
     * The design extensions of a set of active extensions, as design.ini [ExtensionSettings] DesignExtensions[]
     * resolves (eZTemplateDesignResource::designExtensions()): the extension that is activated first comes first
     */
    public static function designExtensions( array $active )
    {
        $root = self::root();
        // eZINI reads the settings of the last activated extension first, then the earlier ones, then settings/override;
        // designExtensions() turns the merged list around
        $files = self::iniFiles( "$root/settings", 'design.ini' );
        foreach ( array_reverse( $active ) as $extension )
            $files = array_merge( $files, self::iniFiles( "$root/extension/$extension/settings", 'design.ini' ) );
        $files = array_merge( $files, self::iniFiles( "$root/settings/override", 'design.ini' ) );
        $names = array_reverse( self::iniList( $files, 'ExtensionSettings', 'DesignExtensions' ) );
        return array_values( array_unique( array_filter( $names, array( __CLASS__, 'checkedExtension' ) ) ) );
    }

    /** The design bases of a chain, as eZTemplateDesignResource::findDesignBase(): per design the extensions, then the core */
    public static function designBases( array $chain, array $designExtensions )
    {
        $root = self::root();
        $bases = array();
        foreach ( $chain as $design )
        {
            foreach ( $designExtensions as $extension )
            {
                if ( is_dir( "$root/extension/$extension/design/$design" ) )
                    $bases[] = "extension/$extension/design/$design";
            }
            if ( is_dir( "$root/design/$design" ) )
                $bases[] = "design/$design";
        }
        return $bases;
    }

    /** The templates of a design base, relative path (templates/... or override/templates/...) => file */
    private static function baseTemplates( $base )
    {
        $root = self::root();
        $files = array();
        foreach ( array( 'templates', 'override/templates' ) as $sub )
        {
            $dir = "$root/$base/$sub";
            if ( !is_dir( $dir ) )
                continue;
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( substr( $file->getFilename(), -4 ) === '.tpl' )
                    $files[$sub . substr( $file->getPathname(), strlen( $dir ) )] = substr( $file->getPathname(), strlen( $root ) + 1 );
            }
        }
        ksort( $files );
        return $files;
    }

    /** The templates a list of design bases renders: relative path => file of the first base that has it */
    private static function effective( array $bases )
    {
        $files = array();
        foreach ( $bases as $base )
        {
            foreach ( self::baseTemplates( $base ) as $relative => $file )
            {
                if ( !isset( $files[$relative] ) )
                    $files[$relative] = $file;
            }
        }
        return $files;
    }

    /** Every template the administration designs render with every extension set: file => true */
    private static function renderedTemplates()
    {
        $paths = array();
        foreach ( self::variants() as $variant )
        {
            foreach ( self::effective( self::designBases( $variant[1], self::designExtensions( $variant[0] ) ) ) as $path )
                $paths[$path] = true;
        }
        return $paths;
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
        $paths = self::renderedTemplates();
        $this->assertGreaterThan( 400, count( $paths ), 'the template chains were not read' );
        $allowlist = self::ALLOWLIST;
        $missing = array();
        foreach ( self::EXTENSION_ALLOWLIST as $extension => $entry )
        {
            $this->assertNotEmpty( $entry['why'], $extension );
            $this->assertNotEmpty( $entry['files'], "$extension has nothing left: remove its EXTENSION_ALLOWLIST entry" );
            if ( !self::hasExtension( $extension ) )
            {
                // not in this checkout: not checked, but a shipped one must be there in CI
                if ( getenv( 'CI' ) && in_array( $extension, self::SHIPPED_EXTENSIONS, true ) )
                    $missing[] = $extension;
                continue;
            }
            foreach ( $entry['files'] as $file => $kinds )
                $allowlist["extension/$extension/$file"] = $kinds;
        }

        $this->assertSame( array(), $missing, 'extensions of the distribution listed in EXTENSION_ALLOWLIST are missing from the checkout' );

        $unexpected = array();
        $fewer = array();
        foreach ( array_keys( $paths ) as $path )
        {
            if ( self::excepted( $path ) )
                continue;
            $found = self::findings( (string)file_get_contents( self::root() . '/' . $path ) );
            $allowed = isset( $allowlist[$path] ) ? $allowlist[$path] : array();
            foreach ( array( 'script', 'handler', 'jsurl', 'style', 'styleblock' ) as $kind )
            {
                $now = isset( $found[$kind] ) ? $found[$kind] : 0;
                $listed = isset( $allowed[$kind] ) ? $allowed[$kind] : 0;
                if ( $now > $listed )
                    $unexpected[] = "$path: $now $kind (allowed: $listed)";
                else if ( $now < $listed )
                    $fewer[] = "$path: $now $kind, listed with $listed - lower or remove its (EXTENSION_)ALLOWLIST entry";
            }
        }
        foreach ( array_keys( $allowlist ) as $path )
        {
            if ( !isset( $paths[$path] ) )
                $fewer[] = "$path is listed but not rendered by admin4 or admin4l any more";
        }
        $this->assertSame( array(), $unexpected,
            'Inline code in an administration template: move scripts into a file (ezscript), handlers to a listener '
            . 'or the data-a4-* actions of a4_actions.js, styles to a class (doc/eng-us/features/6.0/content-security-policy.md)' );
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

    /** CSP-01: the chain the templates are read from */
    public function testTheChainIsTheKernels()
    {
        $variants = self::variants();
        $this->assertNotEmpty( $variants );
        foreach ( $variants as $label => $variant )
        {
            list( $active, $chain ) = $variant;
            $this->assertArrayHasKey( $chain[0], self::DESIGNS, $label );
            $this->assertContains( 'admin2', $chain, "$label falls back to admin2" );
            $bases = self::designBases( $chain, self::designExtensions( $active ) );
            // the bundled online editor is part of the chain, before the core directory of its design
            if ( in_array( 'ezoe', $active, true ) )
            {
                $this->assertContains( 'extension/ezoe/design/standard', $bases, $label );
                $this->assertLessThan( array_search( 'design/standard', $bases, true ), array_search( 'extension/ezoe/design/standard', $bases, true ), $label );
            }
            // two extensions with the same template in one design: which one renders depends on the loading order
            $owners = array();
            foreach ( $bases as $base )
            {
                if ( strpos( $base, 'extension/' ) !== 0 )
                    continue;
                $design = substr( $base, strrpos( $base, '/' ) + 1 );
                foreach ( array_keys( self::baseTemplates( $base ) ) as $relative )
                    $owners["$design:$relative"][] = $base;
            }
            $this->assertSame( array(), array_filter( $owners, function ( $o ) { return count( $o ) > 1; } ), "$label: the same template in two extensions" );
        }
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
        // skipped without node on a developer machine, a failure where CI is set
        $node = AdminNodeBinary::require();
        $out =(string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . "/js/$script" ) . ' '
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
        // an address that is not of this site is not followed: javascript:, data:, another host, protocol-relative
        $this->assertSame( array( 'javascript' => null, 'data' => null, 'other host' => null, 'protocol-relative' => null,
                                  'absolute site' => 'https://admin.example/setup/info' ), $c['navigate refused']['seen'] );
        $this->assertSame( '0', $c['change']['reset'] );
        // data-a4-print and data-a4-disable-on-submit are gone: a click on them is an ordinary click
        $this->assertSame( array( 'prints' => 0, 'prevented' => false, 'timers' => 0, 'sent' => 1 ),
                           array_diff_key( $c['removed'], array( 'name' => 1 ) ) );
        $this->assertSame( 'b', $c['focus']['active'] );
    }

    /** CSP-05 */
    public function testADeclaredPopupMenuWorks()
    {
        foreach ( array( 'admin4', 'admin' ) as $design )
        {
            $c = $this->harness( 'csp_popupmenu.js', "design/$design/javascript/popupmenu/ezpopupmenu.js" );
            $this->assertCount( 11, $c, $design );
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
            // only addresses of this site: javascript:, //other.host, https://other.host and data: go nowhere, the
            // value is URL-encoded, and a javascript: item address is not linked; each refusal is in the console
            $unsafe = $c['unsafe redirect'];
            $this->assertSame( array( 'r-js' => null, 'r-proto' => null, 'r-data' => null, 'r-abs' => null,
                                      'r-own' => 'https://admin.example/search/a%20b%2Fc%3Fd' ), $unsafe['went'], $design );
            $this->assertNull( $unsafe['ran'] );
            $this->assertSame( '#', $unsafe['linkHref'] );
            $this->assertSame( '/content/edit/4', $unsafe['editHref'], 'a path of the site is linked' );
            $this->assertSame( 5, $unsafe['refused'] );
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

    /**
     * A JSON data block of a template with every template expression standing for one JSON value ({ldelim} and
     * {rdelim} are the braces, {if}/{/if} keep their content): the decoded block, null when it is not JSON
     */
    public static function jsonBlock( $template, $openTag )
    {
        $code = (string)preg_replace( '/\{\*.*?\*\}/s', '', $template );
        $start = strpos( $code, $openTag );
        if ( $start === false )
            return null;
        $start += strlen( $openTag );
        $json = substr( $code, $start, strpos( $code, '</script>', $start ) - $start );
        // printed from a set-block variable through the escape chain: the JSON is the set-block's content
        if ( preg_match( '/^\{\$(\w+)\|/', $json, $m ) && preg_match( '/\{set-block variable=\$' . $m[1] . '\}(.*?)\{\/set-block\}/s', $code, $block ) )
            $json = $block[1];
        $json = (string)preg_replace( '#\{/?(if|else|elseif)\b[^{}]*\}#', '', $json );
        $json = (string)preg_replace( '/\{(?!ldelim\}|rdelim\})[^{}]*\}/', '0', $json );
        $decoded = json_decode( str_replace( array( '{ldelim}', '{rdelim}' ), array( '{', '}' ), $json ), true );
        return is_array( $decoded ) ? $decoded : null;
    }

    /** CSP-07 */
    public function testTheTinyMCE8EditorStartsFromAFile()
    {
        $tpl = (string)file_get_contents( self::root() . '/extension/ezoe/design/standard/templates/content/datatype/edit/ezxmltext_ezoe_tinymce8.tpl' );
        $this->assertSame( array(), self::findings( $tpl ) );
        $this->assertStringContainsString( '<script type="application/json" id="ezoe8-settings">', $tpl );
        $this->assertStringContainsString( '<script type="application/json" id="ezoe8-button-map">', $tpl );
        $this->assertStringContainsString( "<script src=\"{'javascript/tinymce8_ez/ezoe8_start.js'|ezdesign( 'no' )}\" charset=\"utf-8\" defer></script>", $tpl );
        foreach ( array( 'data-ezoe8-attribute="{$attribute.id}"', 'data-ezoe8-buttons="{json_encode( $layout_settings[\'buttons\'] )|wash}"',
                         'data-ezoe8-path-location="{$layout_settings[\'path_location\']|wash}"', 'data-ezoe8-rows="{$editorRow}"' ) as $attribute )
            $this->assertStringContainsString( $attribute, $tpl );
        // JSON, not script: quoted keys, values from the installation through json_encode
        $this->assertStringContainsString( '"license_key": "gpl",', $tpl );
        $this->assertStringContainsString( "\"root_url\": {json_encode( '/'|ezroot( 'no' ) )},", $tpl );
        $this->assertDoesNotMatchRegularExpression( '/^\s*[a-z_]+\s*:/m', $tpl, 'a key without quotes' );
        $settings = self::jsonBlock( $tpl, '<script type="application/json" id="ezoe8-settings">' );
        $this->assertIsArray( $settings, 'the settings block is JSON once the template values are in' );
        $this->assertSame( 'gpl', $settings['license_key'] );
        $this->assertArrayHasKey( 'form_token', $settings['ez_settings'] );
        $this->assertStringContainsString( '{set-block variable=$ezoe8_button_map_json}{json_encode( $input_handler.engine.toolbar_map|merge( hash( \'|\', \'|\' ) ) )}{/set-block}', $tpl );

        $c = $this->harness( 'csp_tinymce8.js', 'extension/ezoe/design/standard/javascript/tinymce8_ez/ezoe8_start.js' );
        $this->assertCount( 5, $c );
        $this->assertSame( array( 0, 1 ), array( $c['no tinymce']['inits'], $c['no tinymce']['errors'] ), 'without TinyMCE the console says why' );
        $start = $c['start'];
        $this->assertSame( 2, $start['inits'], 'one editor per marked textarea' );
        $this->assertSame( 2, $start['again'], 'a second start starts no editor twice' );
        $this->assertSame( array( '#ContentObjectAttribute_data_text_5', '#ContentObjectAttribute_data_text_6' ), $start['selectors'] );
        $this->assertSame( 'gpl', $start['license'] );
        $this->assertSame( '/', $start['root'] );
        $this->assertSame( 'bold | ezembed | italic | ezdisable | code', $start['toolbar'], 'the button map from the data block' );
        $this->assertSame( array( true, false ), $start['statusbar'], 'path location none: no status bar' );
        $this->assertSame( 480, $start['height'] );
        $this->assertSame( 64, $start['offset'] );
        $this->assertSame( array( 'ez_skin_class', 'ez_disable_editor_text' ), $start['registered'] );
        $this->assertSame( 'function', $start['disable'] );
        $this->assertSame( '1', $start['started'] );
        $this->assertSame( array( 'function', 'function', 'function', 'function' ), $start['globals'] );
        $this->assertSame( 0, $start['errors'] );
        $this->assertSame( array( 'rtl', 'ar' ), array( $c['rtl']['directionality'], $c['rtl']['lang'] ) );
        $this->assertSame( array( 0, 1 ), array( $c['deferred']['before'], $c['deferred']['after'] ) );
        $this->assertSame( array( 0, 1 ), array( $c['broken']['inits'], $c['broken']['errors'] ), 'broken data starts nothing and says so' );
    }

    /** CSP-09 */
    public function testTheJSONDataBlocksCarryNoAngleBrackets()
    {
        $blocks = 0;
        $wrong = array();
        foreach ( array_keys( self::renderedTemplates() ) as $path )
        {
            $code = (string)preg_replace( '/\{\*.*?\*\}/s', '', (string)file_get_contents( self::root() . '/' . $path ) );
            if ( !preg_match_all( '#<script\b[^>]*\stype="application/json"[^>]*>(.*?)</script>#s', $code, $m ) )
                continue;
            foreach ( $m[1] as $content )
            {
                $blocks++;
                if ( !preg_match( '/^\{\$\w+' . preg_quote( self::JSON_ESCAPE, '/' ) . '\}$/', $content ) )
                    $wrong[] = "$path: " . substr( $content, 0, 80 );
            }
        }
        $this->assertGreaterThan( 0, $blocks, 'the JSON blocks were found' );
        $this->assertSame( array(), $wrong, 'a JSON block prints {$variable' . self::JSON_ESCAPE . '} (a set-block)' );

        // the chain itself, interpreted and compiled
        $dir = self::root() . '/var/tmp/csp_json_' . uniqid( '', true );
        mkdir( $dir, 0700, true );
        $file = "$dir/block.tpl";
        file_put_contents( $file, '{set-block variable=$j}{json_encode( $v )}{/set-block}{$j' . self::JSON_ESCAPE . '}' );
        $value = array( 'a' => '<!--<script>x</script>-->', 'b' => "</SCRIPT ><b>\u{00e4}" );
        $saved = array_key_exists( 'eZTemplateCompilerSettings', $GLOBALS ) ? $GLOBALS['eZTemplateCompilerSettings'] : null;
        try
        {
            foreach ( array( false, true ) as $compiled )
            {
                $GLOBALS['eZTemplateCompilerSettings']['compile'] = $compiled;
                $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = $dir;
                eZTemplate::resetInstance();
                $tpl = new eZTemplate();
                $tpl->autoload();
                $tpl->registerAutoloadOperators( array( 'script' => 'extension/ezjscore/autoloads/ezjscencodingtemplatefunctions.php',
                                                        'class' => 'ezjscEncodingTemplateFunctions',
                                                        'operator_names' => array( 'json_encode' ) ) );
                $tpl->setVariable( 'v', $value );
                $out = trim( (string)$tpl->fetch( $file ) );
                $label = $compiled ? 'compiled' : 'interpreted';
                $this->assertStringNotContainsString( '<', $out, $label );
                $this->assertStringNotContainsString( '>', $out, $label );
                $this->assertSame( $value, json_decode( $out, true ), "$label: the values come back" );
            }
        }
        finally
        {
            if ( $saved === null )
                unset( $GLOBALS['eZTemplateCompilerSettings'] );
            else
                $GLOBALS['eZTemplateCompilerSettings'] = $saved;
            eZTemplate::resetInstance();
            foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ) as $f )
                $f->isDir() ? rmdir( $f->getPathname() ) : unlink( $f->getPathname() );
            rmdir( $dir );
        }
    }
}
