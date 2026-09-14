{* An OPML export lists feeds rather than articles, so the page shows it a
   different set of controls. Which half is on screen follows the format
   drop-down below, both here and on the server. *}
{literal}
<style type="text/css">
.opml-meta { color: #666; font-size: .92em; }
.opml-gone { color: #a33; }
.opml-outlines td, .opml-browser td { vertical-align: middle; padding-top: .4rem; padding-bottom: .4rem; }
.opml-outlines input[type=text] { font: inherit; padding: .2rem .3rem; }
.opml-outlines code, .opml-browser code {
    font-family: Menlo, Consolas, monospace; font-size: .92em;
    background: #f4f4f5; border: 1px solid #e6e6ea; border-radius: 3px; padding: 0 .3rem;
}
.opml-more summary { cursor: pointer; color: #555; padding: .2rem 0; }
.opml-more-grid { display: flex; flex-wrap: wrap; gap: .9rem 1.4rem; padding: .6rem 0 .2rem 0; }
.opml-more-grid > span { display: flex; flex-direction: column; gap: .2rem; }
.opml-more-grid label { font-size: .85em; color: #666; }
.opml-more-flags { flex-direction: row !important; align-items: center; gap: 1rem !important; }
.opml-more-flags label { font-size: .95em; color: inherit; }
.opml-actions { display: flex; flex-wrap: wrap; gap: .6rem; padding-top: .9rem; }
.opml-browser-controls { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; padding-bottom: .9rem; }
.opml-browser-pages { display: flex; flex-wrap: wrap; align-items: center; gap: .3rem; padding-top: .9rem; }
.opml-browser-pages .current {
    display: inline-block; padding: .2rem .5rem; border: 1px solid #4a4a52;
    border-radius: 4px; background: #4a4a52; color: #fff; font-weight: 700;
}
.opml-already td { opacity: .65; }
.opml-tick { color: #2a7; font-weight: 700; }
.opml-window { display: flex; flex-wrap: wrap; align-items: flex-end; gap: .6rem 1rem; }
.opml-window-field { display: flex; flex-direction: column; gap: .2rem; }
.opml-window-field label { font-size: .85em; color: #666; }
table.list th.sortable { white-space: nowrap; }
table.list th.sortable button.sort-button {
    background: none; border: 0; padding: 0; margin: 0; font: inherit;
    color: inherit; cursor: pointer;
}
table.list th.sortable:hover button.sort-button { text-decoration: underline; }
table.list th.sorted button.sort-button { font-weight: 700; }
.sort-arrow { display: inline-block; width: 1em; font-size: .8em; color: #6a6a72; }
</style>
{/literal}

<form action={"rss/edit_export"|ezurl} method="post" name="RSSExport">

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header">

<h1 class="context-title">{'Edit <%rss_export_name> [RSS Export]'|i18n( 'design/admin/rss/edit_export',, hash( '%rss_export_name', $rss_export.title ) )|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div>

{* DESIGN: Content START *}<div class="box-content">

<div class="context-attributes">

    {if not( $valid )}
        <div class="error">
            <h2>{'Invalid input'|i18n( 'design/admin/rss/edit_export' )}</h2>
            <ul>
{section var=Errors loop=$validation_errors}
             <li>{$Errors.item}</li>
{/section}
{*                <li>{'If RSS Export is Active then a valid Access URL is required.'|i18n( 'design/admin/rss/edit_export' )}</li>*}
            </ul>
        </div>
    {/if}
    
    <div class="block"><fieldset>
    <legend>{'Field data'|i18n( 'design/admin/rss/edit_export' )}</legend>
    <div class="block">
    <label for="exportName">{'Name'|i18n( 'design/admin/rss/edit_export' )}:</label>
    <input class="halfbox" id="exportName" type="text" name="title" value="{$rss_export.title|wash}" title="{'Name of the RSS export. This name is used in the Administration Interface only, to distinguish the different exports from each other.'|i18n('design/admin/rss/edit_export')}" />
    </div>

    <div class="block">
    <label for="rssExportDescription">{'Description'|i18n( 'design/admin/rss/edit_export' )}:</label>
    <textarea class="halfbox" id="rssExportDescription" name="Description" rows="3" title="{'Use the description field to write a text explaining what users can expect from the RSS export.'|i18n('design/admin/rss/edit_export')}">{$rss_export.description|wash}</textarea>
    </div>

    <div class="block">
    <label for="rssExportSiteUrl">{'Site URL'|i18n( 'design/admin/rss/edit_export' )}:</label>
    <input class="halfbox" id="rssExportSiteUrl" type="text" name="url" value="{$rss_export.url|wash}"/>
    <div class="context-attributes">
        <p>{'Use this field to enter the base URL of your site. It is used to produce the URLs in the export, composed by the Site URL (e.g. "http://www.example.com/index.php") and the path to the object (e.g. "/articles/my_article"). The Site URL depends on your web server and Exponential configuration.'|i18n( 'design/admin/rss/edit_export')}</p>
        <p>{'Leave this field empty if you want system automaticaly detect the URL of your site from the URL you access feed with'|i18n( 'design/admin/rss/edit_export')}</p>
    </div>
    </div>

    <input type="hidden" name="RSSImageID" value="{$rss_export.image_id}" />

    <div class="block">
    <label class="inline" for="rssExportImage">{'Image'|i18n( 'design/admin/rss/edit_export' )}:</label>
    <input type="text" id="rssExportImage" readonly="readonly" size="45" value="{$rss_export.image_path|wash}" />
    <input class="button" type="submit" name="BrowseImageButton" value="{'Browse'|i18n( 'design/admin/rss/edit_export' )}" title="{'Click this button to select an image for the RSS export. Note that images only work with RSS version 2.0'|i18n('design/admin/rss/edit_export')}" />
    </div>
    {if ne( $rss_export.image_id, 0 )}
      <div class="block">
        <input class="button" type="submit" name="RemoveImageButton" value="{'Remove image'|i18n( 'design/admin/rss/edit_export' )}" title="{'Click to remove image from RSS export.'|i18n('design/admin/rss/edit_export')}" />
      </div>
    {/if}

    <div class="block">
    <label class="inline" for="rssExportVersion">{'Feed format'|i18n( 'design/admin/rss/edit_export' )}:</label>
    {* The value behind each option is the stored one - 1.0, 2.0, ATOM, OPML -
       and only the wording in front of it says which format that is. *}
    <select id="rssExportVersion" name="RSSVersion" title="{'Which format this export is written in. RSS and Atom are feeds of articles; OPML is a list of other feeds. Only RSS 2.0 carries the image selected above.'|i18n('design/admin/rss/edit_export')}">
    {foreach $rss_version_options as $rss_version_option}
    <option
    {if eq( $rss_export.rss_version, '' )}
      {if eq( $rss_version_option.value, $rss_version_default )}
        selected="selected"
      {/if}
    {else}
      {if eq( $rss_version_option.value, $rss_export.rss_version )}
        selected="selected"
      {/if}
    {/if}
      value="{$rss_version_option.value|wash}">{$rss_version_option.label|wash}</option>
    {/foreach}
    </select>
    </div>

    <div class="block" id="rssExportLimitBlock"{if $rss_is_opml} style="display:none;"{/if}>
    <label class="inline" for="rssExportLimit">{'Number of objects'|i18n( 'design/admin/rss/edit_export' )}:</label>
    <select id="rssExportLimit" name="NumberOfObjects" title="{'Use this drop-down to select the maximum number of objects included in the RSS feed.'|i18n('design/admin/rss/edit_export')}">
    {foreach $number_of_objects_array as $number_of_objects_item}
    <option
    {if eq( $rss_export.number_of_objects, 0 )}
      {if eq( $number_of_objects_item, $number_of_objects_default )}
        selected="selected"
      {/if}
    {else}
      {if eq( $number_of_objects_item, $rss_export.number_of_objects )}
        selected="selected"
      {/if}
    {/if}
      value="{$number_of_objects_item}">{$number_of_objects_item|wash}
    </option>
    {/foreach}
    </select>
    </div>

    <div class="block">
    <label class="inline" for="rssExporActive">{'Active'|i18n( 'design/admin/rss/edit_export' )}:</label>
    <input id="rssExporActive" type="checkbox" name="active" {if $rss_export.active|eq( 1 )}checked="checked"{/if} title="{'Use this checkbox to control if the RSS export is active or not. An inactive export will not be automatically updated.'|i18n('design/admin/rss/edit_export')}"/>
    </div>

    <div class="block">
    <label class="inline" for="rssExporMainNodeOnly">{'Main node only'|i18n( 'design/admin/rss/edit_export' )}:</label>
    <input type="checkbox" id="rssExporMainNodeOnly" name="MainNodeOnly" {if $rss_export.main_node_only|eq( 1 )}checked="checked"{/if} title="{'Check if you want to only feed the object from the main node.'|i18n('design/admin/rss/edit_export')}"/>
    </div>

    <div class="block">
    <label class="inline" for="rssExporAccessURL">{'Access URL'|i18n( 'design/admin/rss/edit_export' )}:</label>
    rss/feed/<input class="halfbox" id="rssExporAccessURL" type="text" name="Access_URL" value="{$rss_export.access_url|wash}" title="{'Use this field to set the URL where the RSS export should be available. Note that "rss/feed/" will be appended to the real URL. '|i18n('design/admin/rss/edit_export')|wash}" />
    </div>

    <input type="hidden" name="RSSExport_ID" value={$rss_export.id} />
    <input type="hidden" name="Item_Count" value={count($rss_export.item_list)} />
    </fieldset></div>   
    
    
<div id="rssExportSources"{if $rss_is_opml} style="display:none;"{/if}>
    {section name=Source loop=$rss_export.item_list}
    <div class="block"><fieldset>
       <legend>{'Source'|i18n( 'design/admin/rss/edit_export' )} {sum($Source:index, 1)}</legend>

       <input type="hidden" name="Item_ID_{$Source:index}" value="{$Source:item.id}" />
       <input type="hidden" name="Ignore_Values_On_Browse_{$Source:index}" id="Ignore_Values_On_Browse_{$Source:index}" value="{$Source:item.title|eq('')}" />

       <div class="block">
       <label class="inline" for="rssExporSource_{$Source:index}">{'Source path'|i18n( 'design/admin/rss/edit_export' )}:</label>
       <input type="text" readonly="readonly" size="45" id="rssExporSource_{$Source:index}" value="{$Source:item.source_path|wash}" />
       <input class="button" type="submit" name="{concat( 'SourceBrowse_', $Source:index )}" value="{'Browse'|i18n( 'design/admin/rss/edit_export' )}" title="{'Click this button to select the source node for the RSS export source. Objects of the type selected in the drop-down below published as sub items of the selected node will be included in the RSS export.'|i18n('design/admin/rss/edit_export')}" />
       </div>
       
        <div class="block">
        <label class="inline" for="rssExporSubNodes_{$Source:index}">{'Subnodes'|i18n( 'design/admin/rss/edit_export' )}:</label>
        <input type="checkbox" id="rssExporSubNodes_{$Source:index}" name="Item_Subnodes_{$Source:index}" {if $Source:item.subnodes|wash|eq( 1 )}checked="checked"{/if} title="{'Activate this checkbox if objects from the subnodes of the source should also be fed.'|i18n('design/admin/rss/edit_export')}" onchange="document.getElementById('Ignore_Values_On_Browse_{$Source:index}').value=0;" />
        </div>
    
       <div class="block">
       <label class="inline" for="rssExporClass_{$Source:index}">{'Class'|i18n( 'design/admin/rss/edit_export' )}:</label>
       <select id="rssExporClass_{$Source:index}" name="Item_Class_{$Source:index}" title="{'Use this drop-down to select the type of object that triggers the export. Click the "Set" button to load the correct attribute types for the remaining fields.'|i18n('design/admin/rss/edit_export')|wash}" onchange="document.getElementById('Ignore_Values_On_Browse_{$Source:index}').value=0;">
       {section name=ContentClass loop=$rss_class_array }
       <option
       {section name=Class show=eq( $:item.id, $Source:item.class_id )}
         selected="selected"
       {/section} value="{$:item.id}">{$:item.name|wash}</option>
       {/section}
       </select>
       <input class="button" type="submit" name="Update_Item_Class" value="{'Set'|i18n( 'design/admin/rss/edit_export' )}" title="{'Click this button to load the correct values into the drop-down fields below. Use the drop-down menu on the left to select the class.'|i18n('design/admin/rss/edit_export')}" />
       </div>

       {section name=Attribute show=count( $rss_export.item_list[$Source:index] )|gt( 0 )}

         <div class="block">
         <label class="inline" for="rssExporClass_{$Source:index}_title">{'Title'|i18n( 'design/admin/rss/edit_export' )}:</label>
         <select id="rssExporClass_{$Source:index}_title" name="Item_Class_Attribute_Title_{$Source:index}" title="{'Use this drop-down to select the attribute that should be exported as the title of the RSS export entry.'|i18n('design/admin/rss/edit_export')}" onchange="document.getElementById('Ignore_Values_On_Browse_{$Source:index}').value=0;">
         {section name=ClassAttribute loop=$rss_export.item_list[$Source:index].class_attributes}
         <option value="{$:item.identifier}"
             {section name=ShowSelected show=eq( $Source:item.title, $:item.identifier )}
                 selected="selected"
             {/section}>{$:item.name|wash}</option>
         {/section}
         </select>
       </div>

       <div class="block">
         <label class="inline" for="rssExporClass_{$Source:index}_desc">{'Description'|i18n( 'design/admin/rss/edit_export' )} ({'optional'|i18n( 'design/admin/rss/edit_export' )}):</label>
         <select id="rssExporClass_{$Source:index}_desc" name="Item_Class_Attribute_Description_{$Source:index}" title="{'Use this drop-down to select the attribute that should be exported as the description of the RSS export entry.'|i18n('design/admin/rss/edit_export')}" onchange="document.getElementById('Ignore_Values_On_Browse_{$Source:index}').value=0;">
         <option value="">[{'Skip'|i18n('design/admin/rss/edit_export')}]</option>
         {section name=ClassAttribute loop=$rss_export.item_list[$Source:index].class_attributes}
         <option value="{$:item.identifier|wash}"
             {section name=ShowSelected show=eq( $Source:item.description, $:item.identifier )}
                 selected="selected"
             {/section}>{$:item.name|wash}</option>
         {/section}
         </select>
       </div>

       <div class="block">
         <label class="inline" for="rssExporClass_{$Source:index}_category">{'Category'|i18n( 'design/admin/rss/edit_export' )} ({'optional'|i18n( 'design/admin/rss/edit_export' )}):</label>
         <select id="rssExporClass_{$Source:index}_category" name="Item_Class_Attribute_Category_{$Source:index}" title="{'Use this drop-down to select the attribute that should be exported as the category of the RSS export entry.'|i18n('design/admin/rss/edit_export')}" onchange="document.getElementById('Ignore_Values_On_Browse_{$Source:index}').value=0;">
         <option value="">[{'Skip'|i18n('design/admin/rss/edit_export')}]</option>
         {section name=ClassAttribute loop=$rss_export.item_list[$Source:index].class_attributes}
         <option value="{$:item.identifier|wash}"
             {section name=ShowSelected show=eq( $Source:item.category, $:item.identifier )}
                 selected="selected"
             {/section}>{$:item.name|wash}</option>
         {/section}
         </select>
       </div>

       <div class="block">
         <label class="inline" for="rssExporClass_{$Source:index}_enclosure">{'Enclosure (media)'|i18n( 'design/admin/rss/edit_export' )} ({'optional'|i18n( 'design/admin/rss/edit_export' )}):</label>
         <select id="rssExporClass_{$Source:index}_enclosure" name="Item_Class_Attribute_Enclosure_{$Source:index}" title="{'Use this drop-down to select the attribute that should be exported as the enclosure of the RSS export entry, enclosures are direct link to a media file, so use a media/image/file datatype .'|i18n('design/admin/rss/edit_export')}" onchange="document.getElementById('Ignore_Values_On_Browse_{$Source:index}').value=0;">
         <option value="">[{'Skip'|i18n('design/admin/rss/edit_export')}]</option>
         {foreach $rss_export.item_list[$Source:index].class_attributes as $class_attribute}
         <option value="{$class_attribute.identifier|wash}"
             {if eq( $Source:item.enclosure, $class_attribute.identifier )}
                 selected="selected"
             {/if}>{$class_attribute.name|wash}</option>
         {/foreach}
         </select>
       </div>

       {/section}

       <input class="button" type="submit" name="{concat( 'RemoveSource_', $Source:index )}" value="{'Remove this source'|i18n( 'design/admin/rss/edit_export' )}" title="{'Click to remove this source from the RSS export.'|i18n('design/admin/rss/edit_export')}" />
    </fieldset></div>
    {/section}
</div>

{* The OPML half. Shown when the format is OPML, and only then. *}
<div id="rssExportOPML"{if $rss_is_opml|not} style="display:none;"{/if}>
{include uri='design:rss/edit_export_opml.tpl'
         opml_head=$opml_head
         opml_items=$opml_items
         opml_groups=$opml_groups
         opml_outline_types=$opml_outline_types
         opml_browser_list=$opml_browser_list
         opml_browser_pager=$opml_browser_pager
         opml_browser_search=$opml_browser_search
         opml_browser_limits=$opml_browser_limits}
</div>

</div>

{* DESIGN: Content END *}</div>

    <div class="controlbar">
{* DESIGN: Control bar START *}
    <div class="block">
        <input class="defaultbutton" type="submit" name="StoreButton" value="{'OK'|i18n( 'design/admin/rss/edit_export' )}" title="{'Apply the changes and return to the RSS overview.'|i18n('design/admin/rss/edit_export')}" />
        <input class="button" type="submit" name="RemoveButton" value="{'Cancel'|i18n( 'design/admin/rss/edit_export' )}" title="{'Cancel the changes and return to the RSS overview.'|i18n('design/admin/rss/edit_export')}" />
        <span id="rssExportAddSource"{if $rss_is_opml} style="display:none;"{/if}><input class="button" type="submit" name="AddSourceButton" value="{'Add source'|i18n( 'design/admin/rss/edit_export' )}" title="{'Click to add a new source to the RSS export.'|i18n('design/admin/rss/edit_export')}" /></span>        
    </div>
{* DESIGN: Control bar END *}
    </div>

</div>
</form>

{literal}
<script type="text/javascript">
jQuery(function( $ )//called on document.ready
{
    document.getElementById('exportName').select();
    document.getElementById('exportName').focus();
});

( function () {
    var version = document.getElementById( 'rssExportVersion' );
    if ( !version ) return;

    // Which half of the page applies follows the format. The server decides
    // the same thing after every submit; this only saves waiting for one.
    function show()
    {
        var opml    = version.value === 'OPML',
            sources = document.getElementById( 'rssExportSources' ),
            panel   = document.getElementById( 'rssExportOPML' ),
            add     = document.getElementById( 'rssExportAddSource' ),
            limit   = document.getElementById( 'rssExportLimitBlock' );

        if ( sources ) sources.style.display = opml ? 'none' : '';
        if ( panel )   panel.style.display   = opml ? '' : 'none';
        if ( add )     add.style.display     = opml ? 'none' : '';
        if ( limit )   limit.style.display   = opml ? 'none' : '';
    }

    version.onchange = show;
    show();

    // Enter in the browser's search box should search, not save and leave: the
    // first submit button in the form is OK, and that is what Enter would hit.
    var search = document.getElementById( 'feedBrowserSearch' ),
        apply  = document.getElementById( 'feedBrowserApply' );
    if ( search && apply )
    {
        search.onkeydown = function ( event ) {
            var key = ( event || window.event ).keyCode;
            if ( key !== 13 ) return true;
            apply.click();
            return false;
        };
    }
} )();
</script>
{/literal}
