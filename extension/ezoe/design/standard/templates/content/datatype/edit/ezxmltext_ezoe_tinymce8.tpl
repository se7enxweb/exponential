{* Prototype: eZ Online Editor on TinyMCE 8, included by ezxmltext_ezoe.tpl when
   ezoe.ini[EditorSettings]EditorEngine=tinymce8 or user preference ezoe_engine=tinymce8.
   Input and output stay XHTML as produced / parsed by eZOEXMLInput and eZOEInputParser. *}
{default attribute_base='ContentObjectAttribute'
         editorRow=10}

{def $layout_settings = $input_handler.editor_layout_settings}

{run-once}
{def $content_css_list_temp = ezini('StylesheetSettings', 'EditorCSSFileList', 'design.ini',,true())
     $content_css_list = array()
     $skin             = ezini('EditorSettings', 'Skin', 'ezoe.ini',,true() )
     $cur_locale       = fetch( 'content', 'locale' )
     $tiny_language    = $cur_locale.http_locale_code|explode('-')[0]|downcase
     $directionality   = 'ltr'
     $relation_groups  = hash()
     $relation_group_list = ezini( 'RelationGroupSettings', 'Groups', 'content.ini' )
     $image_sizes      = ezini( 'AliasSettings', 'AliasList', 'image.ini' )
     $view_modes       = ezini( 'embed', 'AvailableViewModes', 'content.ini' )|merge( ezini( 'embed-inline', 'AvailableViewModes', 'content.ini' ) )|unique
     $table_defaults   = hash()
     $link_classes     = hash()
     $link_class_names = hash()
     $search_classes   = array()
     $browse_roots     = array()
}
{* top level roots of the browse tabs like design:ezoe/box_browse.tpl: content, media and users root, and this object *}
{if fetch( 'user', 'has_access_to', hash( 'module', 'ezoe', 'function', 'browse' ) )}
    {foreach array( 'RootNode', 'MediaRootNode', 'UserRootNode' ) as $root_setting}
        {def $root_node = fetch( 'content', 'node', hash( 'node_id', ezini( 'NodeSettings', $root_setting, 'content.ini' ) ) )}
        {if $root_node}
            {set $browse_roots = $browse_roots|append( hash( 'node_id', $root_node.node_id, 'name', $root_node.name|shorten( 35 ) ) )}
        {/if}
        {undef $root_node}
    {/foreach}
    {def $this_object = fetch( 'content', 'object', hash( 'object_id', $attribute.contentobject_id ) )}
    {if and( $this_object, $this_object.published, $this_object.main_node_id )}
        {set $browse_roots = $browse_roots|append( hash( 'node_id', $this_object.main_node_id, 'name', concat( $this_object.name|shorten( 35 ), ' (', 'this'|i18n( 'design/standard/ezoe' ), ')' ) ) )}
    {/if}
    {undef $this_object}
{/if}
{* content classes for the class filter of the dialog search, like design:ezoe/box_search.tpl *}
{foreach fetch( 'class', 'list', hash( 'sort_by', array( 'name', true() ) ) ) as $search_class}
    {set $search_classes = $search_classes|append( hash( 'id', $search_class.id, 'name', $search_class.name ) )}
{/foreach}
{if ezini_hasvariable( 'link', 'ClassDescription', 'content.ini' )}
    {set $link_class_names = ezini( 'link', 'ClassDescription', 'content.ini', '', true() )}
{/if}
{foreach ezini( 'link', 'AvailableClasses', 'content.ini' ) as $link_class}
    {set $link_classes = $link_classes|merge( hash( $link_class, first_set( $link_class_names[$link_class], $link_class ) ) )}
{/foreach}
{* content.ini [table] Defaults like the TinyMCE 3 table dialog, rows/cols are chosen in the table grid *}
{if ezini_hasvariable( 'table', 'Defaults', 'content.ini' )}
    {foreach ezini( 'table', 'Defaults', 'content.ini' ) as $table_default_name => $table_default_value}
        {if array( 'width', 'border', 'class' )|contains( $table_default_name )}
            {set $table_defaults = $table_defaults|merge( hash( $table_default_name, $table_default_value ) )}
        {/if}
    {/foreach}
{/if}
{foreach $content_css_list_temp as $css}
    {set $content_css_list = $content_css_list|append( $css|explode( '<skin>' )|implode( $skin ) )}
{/foreach}
{foreach $relation_group_list as $group}
    {if ezini_hasvariable( 'RelationGroupSettings', concat( $group|upfirst, 'ClassList' ), 'content.ini' )}
        {set $relation_groups = $relation_groups|merge( hash( $group, ezini( 'RelationGroupSettings', concat( $group|upfirst, 'ClassList' ), 'content.ini' ) ) )}
    {/if}
{/foreach}
{if ezini_hasvariable( 'EditorSettings', 'Directionality', 'ezoe.ini',,true() )}
    {set $directionality = ezini('EditorSettings', 'Directionality', 'ezoe.ini',,true() )}
{/if}
{if $tiny_language|ne( 'de' )}{* only the German language pack is bundled in the prototype *}
    {set $tiny_language = ''}
{/if}

{def $cache_query = concat( '?v=', $input_handler.tinymce8_cache_key )
     $plugin_urls = hash()}
{foreach $input_handler.engine.plugins as $plugin_name => $plugin_path}
    {set $plugin_urls = $plugin_urls|merge( hash( $plugin_name, $plugin_path|ezdesign( 'no' ) ) )}
{/foreach}
<script src="{'javascript/tinymce8/tinymce.min.js'|ezdesign( 'no' )}{$cache_query}" charset="utf-8"></script>
<script src="{'javascript/tinymce8_ez/ezoe_dialog.js'|ezdesign( 'no' )}{$cache_query}" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="{'javascript/tinymce8_ez/ezoe_dialog.css'|ezdesign( 'no' )}{$cache_query}" />
{if $skin|eq( 'o2k7' )}
<link rel="stylesheet" type="text/css" href="{'javascript/tinymce8_ez/skins/o2k7/skin.css'|ezdesign( 'no' )}{$cache_query}" />
{/if}
<script type="text/javascript">
var eZOe8GlobalSettings = {ldelim}
    license_key: 'gpl',
    // TinyMCE loads plugins, skins and language packs itself, the suffix makes browsers reload changed files
    cache_suffix: '{$cache_query}',
    {if $tiny_language}
    language: '{$tiny_language}',
    language_url: {concat( 'javascript/tinymce8/langs/', $tiny_language, '.js' )|ezdesign},
    {/if}
    directionality: '{$directionality}',
    external_plugins: {json_encode( $plugin_urls )},
    // no advlist (split list buttons) and no pagebreak (ezoe pagebreak is a custom tag, not an html comment)
    plugins: 'lists autolink link anchor table charmap fullscreen code help',
    menubar: false,
    promotion: false,
    branding: false,
    statusbar: true,
    elementpath: true,
    resize: true,
    min_height: 300,
    content_css: {json_encode( ezcssfiles( $content_css_list, 3, true() ) )},
    content_style: '.ezoeItemNonEditable {ldelim} outline: 1px dashed #8aa4c4; background: #f3f7fb; cursor: default; {rdelim} div.ezoeItemNonEditable {ldelim} margin: .5em 0; padding: .25em; {rdelim} td, th {ldelim} min-width: 3em; padding: 2px 4px; {rdelim}',
    block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre',
    // Same element list as the TinyMCE 3 editor, it reflects what the ezxml parser accepts
    valid_elements: "-strong/b[class|customattributes],-em/i[class|customattributes],span[id|type|class|title|customattributes|align|style|view|inline|alt],sub[class|type|customattributes|align],sup[class|type|customattributes|align],u[class|type|customattributes|align],pre[class|title|customattributes],ol[class|customattributes],ul[class|customattributes],li[class|customattributes],a[href|name|target|view|title|class|id|customattributes],p[class|customattributes|align|style],img[id|type|class|title|customattributes|align|style|view|inline|alt|src|width|height],table[class|border|width|id|title|customattributes|ezborder|bordercolor|align|style],tr[class|customattributes],th[class|width|rowspan|colspan|customattributes|align|style],td[class|width|rowspan|colspan|customattributes|align|style],div[id|type|class|title|customattributes|align|style|view|inline|alt],h1[class|customattributes|align|style],h2[class|customattributes|align|style],h3[class|customattributes|align|style],h4[class|customattributes|align|style],h5[class|customattributes|align|style],h6[class|customattributes|align|style],br",
    // ezxml has no address/blockquote, keep underline as <u> like the old editor
    formats: {ldelim} underline: {ldelim} inline: 'u', exact: true {rdelim} {rdelim},
    entity_encoding: 'named',
    entities: '160,nbsp',
    convert_urls: false,
    inline_styles: false,
    object_resizing: false,
    table_use_colgroups: false,
    table_default_styles: {ldelim}{rdelim},
    table_default_attributes: {json_encode( $table_defaults )},
    table_resize_bars: false,
    visual_table_class: 'mceItemTable',
    noneditable_class: 'ezoeItemNonEditable',
    browser_spellcheck: true,
    contextmenu: 'ezlink ezembed ezcustomtag ezliteral eztable ezgeneral',
    // toolbar above a table with the ez dialogs, like the table buttons of the TinyMCE 3 editor
    table_toolbar: 'eztable eztablecell eztablerow | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | tabledelete',
    ez_skin_class: 'ezoe-skin-{$skin|wash}',
    ez_disable_editor_text: {json_encode( 'Disable editor'|i18n('design/standard/content/datatype') )},
    ez_xml_tag_alias: {$input_handler.json_xml_tag_alias},
    ez_path_open_dialog: {cond( ezini( 'EditorSettings', 'TagPathOpenDialog', 'ezoe.ini',,true() )|eq( 'enabled' ), 'true', 'false' )},
    ez_literal: {json_encode( $input_handler.literal_definition )},
    ez_table_definitions: {json_encode( $input_handler.table_definitions )},
    ez_table_dialog: {json_encode( cond( ezini( 'Engine_tinymce8', 'TableDialog', 'ezoe.ini',,true() )|eq( 'modern' ), 'modern', 'classic' ) )},
    ez_table_classic_texts: {ldelim}
        properties: {json_encode( 'Properties'|i18n('design/standard/ezoe') )},
        size: {json_encode( 'Size'|i18n('design/standard/ezoe') )},
        size_title: {json_encode( 'Click to select table size'|i18n('design/standard/ezoe') )},
        columns: {json_encode( 'Columns'|i18n('design/standard/ezoe') )},
        rows: {json_encode( 'Rows'|i18n('design/standard/ezoe') )},
        width: {json_encode( 'Width'|i18n('design/standard/ezoe') )},
        border: {json_encode( 'Border'|i18n('design/standard/ezoe') )},
        'class': {json_encode( 'Class'|i18n('design/standard/ezoe') )},
        width_title: {json_encode( 'To set the width of the tag, either as percentage by appending % or as pixel size by just using a number.'|i18n('design/standard/ezoe') )},
        class_title: {json_encode( 'Class are often used to give different design or appearance, either by using a different template, style or both.'|i18n('design/standard/ezoe') )},
        ok: {json_encode( 'OK'|i18n('design/standard/ezoe') )},
        cancel: {json_encode( 'Cancel'|i18n('design/standard/ezoe') )}
    {rdelim},
    ez_general_definitions: {json_encode( $input_handler.general_definitions )},
    ez_custom_tags: {json_encode( $input_handler.custom_tag_definitions )},
    ez_link_classes: {json_encode( $link_classes )},
    ez_link_view_modes: {json_encode( ezini( 'link', 'AvailableViewModes', 'content.ini' ) )},
    ez_custom_attribute_style_map: {json_encode( ezini( 'EditorSettings', 'CustomAttributeStyleMap', 'ezoe.ini',,true() ) )},
    ez_settings: {ldelim}
        root_url: {'/'|ezroot},
        root_node: {ezini( 'NodeSettings', 'RootNode', 'content.ini' )|int},
        contentobject_id: {$attribute.contentobject_id},
        contentobject_version: {$attribute.version},
        embed_definitions: {json_encode( $input_handler.embed_definitions )},
        content_edit_url: {'/content/edit'|ezurl},
        browse_image_alias: {json_encode( ezini( 'EditorSettings', 'BrowseImageAlias', 'ezoe.ini',,true() ) )},
        search_classes: {json_encode( $search_classes )},
        browse_roots: {json_encode( $browse_roots )},
        upload_file_extensions: {json_encode( $input_handler.engine.config.upload_extensions )},
        upload_from_url: {json_encode( $input_handler.engine.config.upload_from_url )},
        extension_url: {'/ezoe/'|ezurl},
        ezjscore_url: {'/ezjscore/'|ezurl},
        form_token: "@$ezxFormToken@",
        default_size: {json_encode( ezini( 'ImageSettings', 'DefaultEmbedAlias', 'content.ini' ) )},
        image_sizes: {json_encode( $image_sizes )},
        view_modes: {json_encode( $view_modes )},
        relation_groups: {json_encode( $relation_groups )},
        relation_default_group: {json_encode( ezini( 'RelationGroupSettings', 'DefaultGroup', 'content.ini' ) )},
        compatibility_mode: {cond( ezini('EditorSettings', 'CompatibilityMode', 'ezoe.ini',,true())|eq('enabled'), 'true', 'false' )},
        attachment_icon: {'tango/mail-attachment32.png'|ezimage}
    {rdelim}
{rdelim};

{literal}
// Maps the button names of ezoe.ini [EditorLayout] Buttons[] (TinyMCE 3 ez theme) to TinyMCE 8 toolbar items.
// Buttons without a counterpart in the prototype are dropped.
{/literal}
var eZOe8ButtonMap = {json_encode( $input_handler.engine.toolbar_map|merge( hash( '|', '|' ) ) )};
{literal}
function eZOe8Toolbar( buttons )
{
    var items = [];
    buttons.forEach( function( b ) {
        var item = eZOe8ButtonMap[ b ];
        // skip unknown buttons, duplicates (image/object both map to ezembed) and double separators
        if ( !item || ( item !== '|' && items.indexOf( item ) !== -1 ) || ( item === '|' && ( !items.length || items[ items.length - 1 ] === '|' ) ) )
            return;
        items.push( item );
    });
    if ( items[ items.length - 1 ] === '|' )
        items.pop();
    return items.join( ' ' ) + ' | code';
}

function eZOe8Init( id, attributeId, buttons, pathLocation, rows )
{
    var settings = Object.assign( {}, eZOe8GlobalSettings, {
        selector: '#' + id,
        toolbar: eZOe8Toolbar( buttons ),
        toolbar_mode: 'wrap',
        statusbar: pathLocation !== 'none',
        height: Math.max( 300, rows * 24 ),
        setup: function( editor ) {
            editor.options.register( 'ez_skin_class', { processor: 'string', default: '' } );
            editor.options.register( 'ez_disable_editor_text', { processor: 'string', default: 'Disable editor' } );

            // skin class for skins/<skin>/skin.css on the editor container
            editor.on( 'PostRender', function() {
                if ( editor.options.get( 'ez_skin_class' ) )
                {
                    editor.getContainer().classList.add( editor.options.get( 'ez_skin_class' ) );
                    // dialogs and menus are rendered outside the editor container
                    document.body.classList.add( editor.options.get( 'ez_skin_class' ) );
                }
            });

            // toolbar counterpart of the "Disable editor" form button (ezoe.ini [EditorLayout] button "disable")
            editor.ui.registry.addButton( 'ezdisable', {
                icon: 'close',
                tooltip: editor.options.get( 'ez_disable_editor_text' ),
                onAction: function() {
                    var button = document.querySelector( 'input[name="CustomActionButton[' + attributeId + '_disable_editor]"]' );
                    if ( button )
                    {
                        editor.save();
                        button.click();
                    }
                },
                onSetup: function( api ) {
                    api.setEnabled( !!document.querySelector( 'input[name="CustomActionButton[' + attributeId + '_disable_editor]"]' ) );
                }
            });
        }
    });
    tinymce.init( settings );
}
{/literal}
</script>
{/run-once}

<div class="oe-window">
    <textarea class="box" id="{$attribute_base}_data_text_{$attribute.id}" name="{$attribute_base}_data_text_{$attribute.id}" cols="88" rows="{$editorRow}">{$input_handler.input_xml}</textarea>
</div>

<div class="block">
    {if $input_handler.can_disable}
        <input class="button{if $layout_settings['buttons']|contains('disable')} hide{/if}" type="submit" name="CustomActionButton[{$attribute.id}_disable_editor]" value="{'Disable editor'|i18n('design/standard/content/datatype')}" />
    {/if}
    {include uri='design:content/datatype/edit/ezxmltext_ezoe_engine_switch.tpl' attribute=$attribute input_handler=$input_handler}
    <script type="text/javascript">
    eZOe8Init( '{$attribute_base}_data_text_{$attribute.id}', {$attribute.id}, {json_encode( $layout_settings['buttons'] )}, '{$layout_settings['path_location']}', {$editorRow} );
    </script>
</div>
{/default}
