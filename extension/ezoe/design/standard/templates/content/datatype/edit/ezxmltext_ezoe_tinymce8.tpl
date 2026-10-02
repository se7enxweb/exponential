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
}
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

<script src={'javascript/tinymce8/tinymce.min.js'|ezdesign} charset="utf-8"></script>
<script type="text/javascript">
var eZOe8GlobalSettings = {ldelim}
    license_key: 'gpl',
    {if $tiny_language}
    language: '{$tiny_language}',
    language_url: {concat( 'javascript/tinymce8/langs/', $tiny_language, '.js' )|ezdesign},
    {/if}
    directionality: '{$directionality}',
    external_plugins: {ldelim}
        ezembed: {'javascript/tinymce8_ez/plugins/ezembed/plugin.js'|ezdesign}
    {rdelim},
    plugins: 'lists advlist autolink link anchor table charmap pagebreak fullscreen code help',
    menubar: false,
    promotion: false,
    branding: false,
    statusbar: true,
    elementpath: true,
    resize: true,
    min_height: 300,
    content_css: {json_encode( ezcssfiles( $content_css_list, 3, true() ) )},
    content_style: '.ezoeItemNonEditable {ldelim} outline: 1px dashed #8aa4c4; background: #f3f7fb; cursor: default; {rdelim} div.ezoeItemNonEditable {ldelim} margin: .5em 0; padding: .25em; {rdelim}',
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
    table_default_attributes: {ldelim} border: '0' {rdelim},
    noneditable_class: 'ezoeItemNonEditable',
    browser_spellcheck: true,
    contextmenu: 'link ezembed table',
    ez_settings: {ldelim}
        root_url: {'/'|ezroot},
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
// Buttons without a counterpart in the prototype (custom, literal, ...) are dropped.
var eZOe8ButtonMap = {
    formatselect: 'blocks', bold: 'bold', italic: 'italic', underline: 'underline',
    sub: 'subscript', sup: 'superscript',
    justifyleft: 'alignleft', justifycenter: 'aligncenter', justifyright: 'alignright', justifyfull: 'alignjustify',
    bullist: 'bullist', numlist: 'numlist', outdent: 'outdent', indent: 'indent',
    undo: 'undo', redo: 'redo',
    link: 'link', unlink: 'unlink', anchor: 'anchor',
    image: 'ezembed', object: 'ezembed', file: 'ezembed',
    charmap: 'charmap', pagebreak: 'pagebreak',
    table: 'table',
    fullscreen: 'fullscreen', help: 'help',
    '|': '|'
};

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

function eZOe8Init( id, buttons, pathLocation, rows )
{
    var settings = Object.assign( {}, eZOe8GlobalSettings, {
        selector: '#' + id,
        toolbar: eZOe8Toolbar( buttons ),
        toolbar_mode: 'wrap',
        statusbar: pathLocation !== 'none',
        height: Math.max( 300, rows * 24 )
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
    <script type="text/javascript">
    eZOe8Init( '{$attribute_base}_data_text_{$attribute.id}', {json_encode( $layout_settings['buttons'] )}, '{$layout_settings['path_location']}', {$editorRow} );
    </script>
</div>
{/default}
