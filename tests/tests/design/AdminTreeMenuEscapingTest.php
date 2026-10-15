<?php
/**
 * Names, request values and other data reach the admin pages as text, never as markup or script: the content tree
 * menu keeps its node data out of inline handlers, the popup menus put names into their header and items as text,
 * the relations list and the sub-items table insert class and section names as text, request values in the
 * templates are escaped for where they stand, and hiding a node takes a POST.
 *
 * These are source-text checks: they hold the constructs that make the escaping work, in every copy of a file.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminTreeMenuEscapingTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminTreeMenuEscapingTest extends PHPUnit\Framework\TestCase
{
    private static function file( $path )
    {
        $file = dirname( __DIR__, 3 ) . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    /** The existing copies of a design file */
    private static function copies( $path, array $designs = array( 'admin', 'admin4' ) )
    {
        $files = array();
        foreach ( $designs as $design )
        {
            if ( is_file( dirname( __DIR__, 3 ) . "/design/$design/$path" ) )
                $files["design/$design/$path"] = self::file( "design/$design/$path" );
        }
        self::assertNotEmpty( $files, "No copy of $path" );
        return $files;
    }

    public function testTheTreeMenuButtonCarriesOnlyTheNodeID()
    {
        foreach ( self::copies( 'javascript/contentstructuremenu_dynamic.js' ) as $path => $js )
        {
            // no name, class or language list is written into an inline handler
            $this->assertStringNotContainsString( "onclick=\"ezpopmenu_showTopLevel", $js, $path );
            $this->assertDoesNotMatchRegularExpression( '/String\(\s*item\.name\s*\)\.replace/', $js, $path );
            $this->assertDoesNotMatchRegularExpression( "/onclick=[^\\n]*item\\.name/", $js, $path );
            $this->assertStringContainsString( '" data-node-id="', $js, $path );
            $this->assertStringContainsString( "closest( 'a.nodeicon[data-node-id]' )", $js, $path );
            $this->assertStringContainsString( 'this.nodes[nodeID] = {', $js, $path );
            // the link of a known node never navigates, even without the popup menu script
            $this->assertMatchesRegularExpression( '/if \( !node \)\s*\{\s*return;\s*\}\s*e\.preventDefault\(\);/', $js, $path );
            // the name arrives HTML-encoded and is decoded once, then handled as text
            $this->assertStringContainsString( 'name = _text( item.name )', $js, $path );
            $this->assertStringContainsString( 'span.textContent = name;', $js, $path );
            $this->assertStringNotContainsString( 'span.innerHTML', $js, $path );
            // values in the generated markup's attributes go through _attr
            $this->assertStringContainsString( "_attr( itemClass.name )", $js, $path );
            $this->assertStringContainsString( "_attr( icon )", $js, $path );
        }
    }

    /** wash( 'javascript' ) is safe inside a script element: no < or > (no </script>), no raw line break */
    public function testWashJavascriptEscapesWhatEndsAScriptOrAString()
    {
        $operator = self::file( 'lib/eztemplate/classes/eztemplatestringoperator.php' );
        if ( !class_exists( 'eZTemplateStringOperator' ) )
            require_once dirname( __DIR__, 3 ) . '/lib/eztemplate/classes/eztemplatestringoperator.php';
        $this->assertSame( 'a\\\\b\\042\\047\\x3C/script\\x3E\\n\\r\\u2028\\u2029',
                           eZTemplateStringOperator::javascriptString( "a\\b\"'</script>\n\r\u{2028}\u{2029}" ) );
        // & as well: in an HTML attribute a character reference (&#39;) would be decoded to a quote first
        $this->assertSame( '\\x26#39;', eZTemplateStringOperator::javascriptString( '&#39;' ) );
        // the interpreted and the compiled path both use it
        $this->assertStringContainsString( '$operatorValue = self::javascriptString( $operatorValue );', $operator );
        $this->assertStringContainsString( "eZTemplateStringOperator::javascriptString( %1% )", $operator );
    }

    /** Outside a script element (an onclick, an href="javascript:") a JS-escaped value is HTML-escaped as well */
    public function testJavascriptWashInAnAttributeIsHtmlWashedToo()
    {
        $root = dirname( __DIR__, 3 );
        $found = array();
        foreach ( array( 'admin', 'admin3', 'admin4', 'standard' ) as $design )
        {
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( "$root/design/$design", FilesystemIterator::SKIP_DOTS ) );
            foreach ( $files as $file )
            {
                if ( substr( $file->getFilename(), -4 ) !== '.tpl' )
                    continue;
                $tpl = (string)file_get_contents( $file->getPathname() );
                $outside = preg_replace( '#<script\b[^>]*>.*?</script>#is', '', $tpl );
                if ( preg_match_all( '/\|wash\(\s*["\']?javascript["\']?\s*\)(?!\s*\|wash\b)/', $outside, $m ) )
                    $found[] = substr( $file->getPathname(), strlen( $root ) + 1 ) . ' (' . count( $m[0] ) . ')';
            }
        }
        $this->assertSame( array(), $found, 'wash( javascript ) without |wash outside a script element' );
    }

    public function testThePopupMenuHeaderAndItemsAreText()
    {
        $copies = self::copies( 'javascript/popupmenu/ezpopupmenu.js', array( 'admin', 'admin4', 'standard' ) );
        $this->assertCount( 3, $copies );
        foreach ( $copies as $path => $js )
        {
            $this->assertStringContainsString( 'header.textContent = String( menuHeader );', $js, $path );
            $this->assertStringNotContainsString( 'header.innerHTML', $js, $path );
            $this->assertStringNotContainsString( 'hrefElement.innerHTML = content', $js, $path );
            // values into text nodes and attributes of the parsed item markup; JS-escaped in on* attributes
            $this->assertStringContainsString( "node.nodeType === 3", $js, $path );
            $this->assertStringContainsString( "/^on/i.test( name )", $js, $path );
            $this->assertStringContainsString( "JSON.stringify( value ).slice( 1, -1 ).replace( /'/g", $js, $path );
        }
    }

    public function testRelationsAndSubItemsInsertNamesAsText()
    {
        foreach ( self::copies( 'javascript/ezajaxrelations_jquery.js' ) as $path => $js )
        {
            foreach ( array( 'className', 'sectionName', 'publishedTxt' ) as $value )
            {
                $this->assertStringContainsString( ".text( $value )", $js, $path );
                $this->assertStringNotContainsString( ".html( $value )", $js, $path );
            }
            $this->assertStringContainsString( ".text( data.content.SearchString )", $js, $path );
            $this->assertStringNotContainsString( "+ data.content.SearchString +", $js, $path );
        }
        foreach ( self::copies( 'javascript/ezajaxsubitems_expdatatable.js' ) as $path => $js )
        {
            $this->assertStringContainsString( "'aria-label': name", $js, $path );
            $this->assertStringNotContainsString( "'aria-label': row.name", $js, $path );
            $this->assertStringContainsString( '}, name, row.node_id, createhereMenu);', $js, $path );
            $this->assertStringContainsString( "'::' + encodeURIComponent(confObj.nameFilter) + '?ContentType=json'", $js, $path );
        }
    }

    public function testRequestValuesAreEscapedWhereTheyStand()
    {
        foreach ( self::copies( 'templates/children_detailed.tpl' ) as $path => $tpl )
        {
            // in a script element: wash( 'javascript' ) alone, which escapes < and > too (see below); no HTML entities,
            // so a filter with & or < reaches the server as typed
            $this->assertStringContainsString( "nameFilter: '{\$view_parameters.namefilter|wash(javascript)}',", $tpl, $path );
            $this->assertStringContainsString( '"{$group.group_name|wash(javascript)}"', $tpl, $path );
        }
        $washed = array(
            'templates/visual/templatelist.tpl' => 'value="{$filterString|wash}"',
            'templates/shop/currencylist.tpl' => 'value="{$view_parameters.offset|int}"',
            'templates/shop/productsoverview.tpl' => 'value="{$view_parameters.offset|int}"',
            'templates/error/kernel/1.tpl' => 'value="{$redirect_uri|wash}"',
            'templates/content/view/versionview.tpl' => 'value="{$redirect_uri|wash}"',
            'templates/content/upload.tpl' => '{$content_object.name|wash}</option>',
            'templates/url/edit.tpl' => 'value="{$url.url|wash}"',
        );
        foreach ( $washed as $file => $needle )
        {
            foreach ( self::copies( $file, array( 'admin', 'admin3', 'admin4', 'standard' ) ) as $path => $tpl )
            {
                if ( $path === 'design/standard/templates/url/edit.tpl' || $path === 'design/standard/templates/visual/templatelist.tpl' )
                    continue;   // no such field in the standard design
                if ( $path === 'design/standard/templates/content/view/versionview.tpl' )
                    continue;
                $this->assertStringContainsString( $needle, $tpl, $path );
            }
        }
    }

    /** A name in a JS string inside an onclick attribute: JS-escaped, then HTML-escaped for the attribute */
    public function testNamesInInlineMenuHandlersAreEscapedTwice()
    {
        $openers = array(
            'templates/node/view/thumbnail.tpl' => "'{\$:node.object.name|shorten(18)|wash(javascript)|wash}'",
            'templates/node/view/line.tpl' => "'{\$:node.object.name|shorten(18)|wash(javascript)|wash}'",
            'templates/contentstructuremenu/show_content_structure.tpl' => "'{\$:parentNode.object.name|shorten(18)|wash(javascript)|wash}'",
            'templates/content/view/versionview.tpl' => "'{\$object.content_class.name|wash(javascript)|wash}'",
        );
        foreach ( $openers as $file => $needle )
        {
            foreach ( self::copies( $file, array( 'admin', 'admin3', 'admin4' ) ) as $path => $tpl )
            {
                $this->assertStringContainsString( $needle, $tpl, $path );
                $this->assertDoesNotMatchRegularExpression( "/onclick=\"ezpopmenu_showTopLevel[^\\n]*\\|wash\\((?:\\s*)javascript(?:\\s*)\\)'/", $tpl,
                                                            "$path: a value only JS-escaped in an attribute is decoded by the browser first" );
            }
        }
        foreach ( array( 'design/admin4/templates/node/view/a4_full_body.tpl', 'design/admin/templates/node/view/full.tpl' ) as $path )
        {
            $tpl = self::file( $path );
            $this->assertStringContainsString( "'{\$node.class_name|wash(javascript)|wash}'", $tpl, $path );
            $this->assertStringContainsString( "'%languages%', {\$js_class_languages|wash} )", $tpl, $path );
        }
        $this->assertStringContainsString( "'{\$node.name|shorten(18)|wash(javascript)|wash}'", self::file( 'design/standard/templates/content/bookmark_tree.tpl' ) );
    }

    public function testThePreviewLinksOnlyToWebAndMailAddresses()
    {
        foreach ( self::copies( 'override/templates/admin_preview/link.tpl' ) as $path => $tpl )
        {
            $this->assertStringContainsString( "begins_with( 'https://' )", $tpl, $path );
            $this->assertStringContainsString( "begins_with( 'mailto:' )", $tpl, $path );
            $this->assertStringContainsString( "\$link_scheme|contains( ':' )|not", $tpl, $path );
            $this->assertStringContainsString( 'href="{$link_url|wash}"', $tpl, $path );
            $this->assertStringNotContainsString( 'href="{$node.data_map.location.content}"', $tpl, $path );
            $this->assertStringContainsString( '{$node.data_map.location.data_text|wash}', $tpl, $path );
        }
    }

    public function testHidingANodeTakesAPost()
    {
        $view = self::file( 'kernel/private/classes/views/content/hide.php' );
        $check = strpos( $view, "serverVariable( 'REQUEST_METHOD', true ) !== 'POST'" );
        $this->assertNotFalse( $check, 'content/hide acts on a GET' );
        $this->assertLessThan( strpos( $view, "operationIsAvailable( 'content_hide' )" ), $check, 'The POST check comes before the change' );
        $this->assertStringContainsString( "design:content/job_confirm.tpl", substr( $view, $check ) );
        // the confirmation is not stored as the page to return to, or OK would lead back to it
        $this->assertMatchesRegularExpression( "/\\\$ViewList\\['hide'\\] = array\\([^;]*'ui_context' => 'administration'/s", self::file( 'kernel/content/module.php' ) );
        foreach ( self::copies( 'templates/popupmenu/popup_sub_advance_menu.tpl' ) as $path => $tpl )
        {
            $this->assertStringNotContainsString( "'url': {\"/content/hide/", $tpl, $path );
            $this->assertStringContainsString( "ezpopmenu_submitForm( 'menu-form-hide' )", $tpl, $path );
            $this->assertMatchesRegularExpression( '#<form id="menu-form-hide" method="post" action=\{"/content/hide/%nodeID%"\|ezurl\}>#', $tpl, $path );
        }
        foreach ( self::copies( 'javascript/popupmenu/ezpopupmenu.js' ) as $path => $js )
            $this->assertStringContainsString( "formElement.setAttribute( 'action', _fillString( action, encoded, false ) );", $js, $path );
        // no template of a kernel design links to content/hide: Hide and Reveal on the Locations tab post a form
        $root = dirname( __DIR__, 3 );
        foreach ( array( 'admin', 'admin2', 'admin3', 'admin4', 'admin4l', 'standard' ) as $design )
        {
            if ( !is_dir( "$root/design/$design/templates" ) )
                continue;
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( "$root/design/$design/templates", FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( substr( $file->getFilename(), -4 ) !== '.tpl' )
                    continue;
                $this->assertDoesNotMatchRegularExpression( "#href=\\{[^}]*'/?content/hide/#", (string)file_get_contents( $file->getPathname() ),
                                                            substr( $file->getPathname(), strlen( $root ) + 1 ) . ' links to content/hide' );
            }
        }
        foreach ( self::copies( 'templates/locations.tpl' ) as $path => $tpl )
        {
            $this->assertSame( 3, substr_count( $tpl, 'form="locations-hide-form" formaction={concat( \'/content/hide/\', $assignment_node.node_id )|ezurl}' ), $path );
            $this->assertStringContainsString( '<form id="locations-hide-form" method="post" action={\'content/hide\'|ezurl}></form>', $tpl, $path );
        }
    }

    /** The tree icon's title is escaped once: by the script, for the attribute; the template only escapes it for the script */
    public function testTheTreeIconTitleIsEscapedOnce()
    {
        foreach ( self::copies( 'templates/contentstructuremenu/content_structure_menu_dynamic.tpl' ) as $path => $tpl )
            $this->assertStringContainsString( "\"expand\":\"{'Click on the icon to display a context-sensitive menu.'|i18n('design/admin/contentstructuremenu')|wash(javascript)}\"", $tpl, $path );
        foreach ( self::copies( 'javascript/contentstructuremenu_dynamic.js' ) as $path => $js )
            $this->assertStringContainsString( "_attr( i18n.expand )", $js, $path );
    }

    /** Text values that the templates printed raw */
    public function testTextValuesAreWashed()
    {
        $washed = array(
            'templates/translations.tpl' => '{$Translations.item.name|wash}</a>',
            'templates/relations.tpl' => '( {$attr.name|wash} )',
            'templates/workflow/edit.tpl' => 'value="{$Events.item.description|wash}"',
            'templates/workflow/view.tpl' => '<td>{$Events.item.description|wash}</td>',
            'templates/package/install_error.tpl' => '{$error.description|wash}</h3>',
            'templates/package/uninstall_error.tpl' => '{$error.description|wash}</h3>',
            'templates/shop/editvatrule.tpl' => '<h2>{$error_header|wash}</h2>',
            'templates/shop/editcurrency.tpl' => "value=\"{\$currency_data['symbol']|wash}\"",
            'templates/setup/moduleextension.tpl' => 'value="{$wizard_settings.db_sample|wash}"',
            'templates/parts/ini_menu.tpl' => '{set $item_name = $name_list[$link_key]|d18n($i18n_section)|wash}',
            'templates/infocollection_validation.tpl' => "item.description|explode( '&lt;' )|implode( '<' )|explode( '&gt;' )|implode( '>' )|wash}",
            'templates/user/register.tpl' => "item.description|explode( '&lt;' )|implode( '<' )|explode( '&gt;' )|implode( '>' )|wash}",
        );
        foreach ( $washed as $file => $needle )
            foreach ( self::copies( $file ) as $path => $tpl )
                $this->assertStringContainsString( $needle, $tpl, $path );
    }
}
