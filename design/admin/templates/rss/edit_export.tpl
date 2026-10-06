{* Edit an RSS export (rss/edit_export/<id>).

   One form in the look of the RSS list: the feed (name, description, format, address, active), what it lists (the
   number of items, main locations only, and one card per source with its location, class and which attributes
   become the title, description, category and enclosure), the Apple Podcasts fields when the format is Apple
   Podcasts, and the OPML half (design:rss/edit_export_opml.tpl) when it is OPML. Each field says what it is used
   for; validation messages are listed at the top.

   Every field and button name is the one the view has always read (title, Description, url, RSSImageID, RSSVersion,
   NumberOfObjects, active, MainNodeOnly, Access_URL, RSSExport_ID, Item_Count, Item_ID_<n>, Item_Subnodes_<n>,
   Item_Class_<n>, Item_Class_Attribute_*_<n>, Podcast_*, StoreButton, RemoveButton, AddSourceButton,
   BrowseImageButton, RemoveImageButton, SourceBrowse_<n>, RemoveSource_<n>, Update_Item_Class), and every template
   variable is still set. Cancel (RemoveButton) discards the draft and goes back to the RSS list. This is an edit
   view, which admin4 draws without its main card; .exp-standalone gives the page its own. The same file is in
   design/admin and design/admin4. Guide: doc/guides/rss-feeds.md *}
{include uri='design:rss/exp_style.tpl'}

{def $feed_url = first_set( $rss_feed_url, '' )
     $source_count = $rss_export.item_list|count}

<form action={"rss/edit_export"|ezurl} method="post" name="RSSExport" class="exp-lists exp-rss exp-standalone" aria-labelledby="rss-export-edit-title">

<div class="context-block">
<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title" id="rss-export-edit-title">{'Edit <%rss_export_name> [RSS Export]'|i18n( 'design/admin/rss/edit_export',, hash( '%rss_export_name', $rss_export.title ) )|wash}</h1>
<span class="exp-meta">{'ID %id'|i18n( 'design/admin/rss/edit_export',, hash( '%id', $rss_export.id ) )}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'An export is a feed at its own address. It lists the newest objects below its sources; feed readers and other sites subscribe to it. Nothing changes for them until you press OK.'|i18n( 'design/admin/rss/edit_export' )}</p>

{if not( $valid )}
<div class="exp-feedback is-bad" role="alert" id="rss-export-errors" tabindex="-1">
    <h2 class="exp-h2">{'Invalid input'|i18n( 'design/admin/rss/edit_export' )}</h2>
    <p>{'The export was not saved. Correct the following and press OK again:'|i18n( 'design/admin/rss/edit_export' )}</p>
    <ul>
    {foreach $validation_errors as $validation_error}
        <li>{$validation_error|wash}</li>
    {/foreach}
    </ul>
</div>
{/if}

<input type="hidden" name="RSSExport_ID" value="{$rss_export.id}" />
<input type="hidden" name="Item_Count" value="{$source_count}" />
<input type="hidden" name="RSSImageID" value="{$rss_export.image_id}" />

{* ---- The feed ---- *}
<section class="exp-panel" aria-labelledby="rss-export-feed-title">
<div class="exp-section-head"><h2 class="exp-h2" id="rss-export-feed-title">{'The feed'|i18n( 'design/admin/rss/edit_export' )}</h2></div>
<div class="exp-form-fields exp-form-wide">

    <div class="exp-field">
        <label for="exportName">{'Name'|i18n( 'design/admin/rss/edit_export' )}</label>
        <input id="exportName" type="text" name="title" value="{$rss_export.title|wash}" maxlength="255" aria-describedby="exportName-help" />
        <span class="exp-help" id="exportName-help">{'Name of the RSS export. This name is used in the Administration Interface only, to distinguish the different exports from each other.'|i18n( 'design/admin/rss/edit_export' )} {'Feeds also carry it as their title.'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>

    <div class="exp-field">
        <label for="rssExportDescription">{'Description'|i18n( 'design/admin/rss/edit_export' )}</label>
        <textarea id="rssExportDescription" name="Description" rows="3" aria-describedby="rssExportDescription-help">{$rss_export.description|wash}</textarea>
        <span class="exp-help" id="rssExportDescription-help">{'Use the description field to write a text explaining what users can expect from the RSS export.'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>

    <div class="exp-field">
        <label for="rssExportVersion">{'Feed format'|i18n( 'design/admin/rss/edit_export' )}</label>
        {* The value behind each option is the stored one - 1.0, 2.0, ATOM, OPML - and only the wording says which format that is. *}
        <select id="rssExportVersion" name="RSSVersion" aria-describedby="rssExportVersion-help">
        {foreach $rss_version_options as $rss_version_option}
            <option value="{$rss_version_option.value|wash}"{if eq( $rss_export.rss_version, '' )}{if eq( $rss_version_option.value, $rss_version_default )} selected="selected"{/if}{elseif eq( $rss_version_option.value, $rss_export.rss_version )} selected="selected"{/if}>{$rss_version_option.label|wash}</option>
        {/foreach}
        </select>
        <span class="exp-help" id="rssExportVersion-help">{'Which format this export is written in. RSS and Atom are feeds of articles; OPML is a list of other feeds. Only RSS 2.0 carries the image selected above.'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>

    <div class="exp-field">
        <label for="rssExporAccessURL">{'Access URL'|i18n( 'design/admin/rss/edit_export' )}</label>
        <div class="exp-inline"><code class="exp-prefix">rss/feed/</code><input id="rssExporAccessURL" type="text" name="Access_URL" value="{$rss_export.access_url|wash}" spellcheck="false" autocomplete="off" aria-describedby="rssExporAccessURL-help" /></div>
        <span class="exp-help" id="rssExporAccessURL-help">{'The address readers subscribe to is the site address followed by rss/feed/ and this. An active export needs one; changing it leaves the readers of the old address with an error.'|i18n( 'design/admin/rss/edit_export' )}</span>
        {if $feed_url|ne( '' )}<span class="exp-url"><code>{$feed_url|wash}</code>{if $rss_export.active|eq( 1 )} <a href="{$feed_url|wash}" target="_blank" rel="noopener">{'Open feed'|i18n( 'design/admin/rss/edit_export' )}</a>{/if}</span>{/if}
    </div>

    <div class="exp-field">
        <label class="exp-check"><input id="rssExporActive" type="checkbox" name="active"{if $rss_export.active|eq( 1 )} checked="checked"{/if} aria-describedby="rssExporActive-help" /> {'Active'|i18n( 'design/admin/rss/edit_export' )}</label>
        <span class="exp-help" id="rssExporActive-help">{'Only an active export answers at its address. Clear this to stop a feed for a while without losing its settings.'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>

    <div class="exp-field">
        <label for="rssExportSiteUrl">{'Site URL'|i18n( 'design/admin/rss/edit_export' )}</label>
        <input id="rssExportSiteUrl" type="text" name="url" value="{$rss_export.url|wash}" spellcheck="false" aria-describedby="rssExportSiteUrl-help" />
        <span class="exp-help" id="rssExportSiteUrl-help">{'Use this field to enter the base URL of your site. It is used to produce the URLs in the export, composed by the Site URL (e.g. "http://www.example.com/index.php") and the path to the object (e.g. "/articles/my_article"). The Site URL depends on your web server and Exponential configuration.'|i18n( 'design/admin/rss/edit_export' )} {'Leave this field empty if you want system automaticaly detect the URL of your site from the URL you access feed with'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>

    <div class="exp-field">
        <label for="rssExportImage">{'Image'|i18n( 'design/admin/rss/edit_export' )}</label>
        <div class="exp-inline">
            <input type="text" id="rssExportImage" readonly="readonly" value="{$rss_export.image_path|wash}" placeholder="{'No image'|i18n( 'design/admin/rss/edit_export' )}" aria-describedby="rssExportImage-help" />
            <button class="exp-btn" type="submit" name="BrowseImageButton" value="1">{'Browse'|i18n( 'design/admin/rss/edit_export' )}</button>
            {if ne( $rss_export.image_id, 0 )}<button class="exp-btn exp-btn-outline-danger" type="submit" name="RemoveImageButton" value="1">{'Remove image'|i18n( 'design/admin/rss/edit_export' )}</button>{/if}
        </div>
        <span class="exp-help" id="rssExportImage-help">{'Click this button to select an image for the RSS export. Note that images only work with RSS version 2.0'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>

</div>
</section>

{* ---- What it lists: the sources. Hidden for OPML, which lists other feeds instead. ---- *}
<section class="exp-panel" id="rssExportSources"{if $rss_is_opml} style="display:none;"{/if} aria-labelledby="rss-export-sources-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="rss-export-sources-title">{'What the feed lists'|i18n( 'design/admin/rss/edit_export' )}</h2>
    <p>{'Each source is a location: the feed lists the newest objects of its class published below it. Browse picks the location; Set loads the attributes of the chosen class for the fields below it.'|i18n( 'design/admin/rss/edit_export' )}</p>
</div>

<div class="exp-form-fields exp-form-wide" id="rssExportLimitBlock"{if $rss_is_opml} style="display:none;"{/if}>
    <div class="exp-field">
        <label for="rssExportLimit">{'Number of objects'|i18n( 'design/admin/rss/edit_export' )}</label>
        <select id="rssExportLimit" name="NumberOfObjects" aria-describedby="rssExportLimit-help">
        {foreach $number_of_objects_array as $number_of_objects_item}
            <option value="{$number_of_objects_item}"{if eq( $rss_export.number_of_objects, 0 )}{if eq( $number_of_objects_item, $number_of_objects_default )} selected="selected"{/if}{elseif eq( $number_of_objects_item, $rss_export.number_of_objects )} selected="selected"{/if}>{$number_of_objects_item|wash}</option>
        {/foreach}
        </select>
        <span class="exp-help" id="rssExportLimit-help">{'Use this drop-down to select the maximum number of objects included in the RSS feed.'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>
    <div class="exp-field">
        <label class="exp-check"><input type="checkbox" id="rssExporMainNodeOnly" name="MainNodeOnly"{if $rss_export.main_node_only|eq( 1 )} checked="checked"{/if} aria-describedby="rssExporMainNodeOnly-help" /> {'Main node only'|i18n( 'design/admin/rss/edit_export' )}</label>
        <span class="exp-help" id="rssExporMainNodeOnly-help">{'An object with several locations below the sources is listed once, by its main location.'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>
</div>

{if $source_count|eq( 0 )}
<p class="exp-empty" style="margin-top: 14px;">{'No source yet: the feed has no items. Press Add source, then Browse to pick the location whose content it lists.'|i18n( 'design/admin/rss/edit_export' )}</p>
{/if}

<ul class="exp-secs" style="margin-top: 14px;">
{section name=Source loop=$rss_export.item_list}
<li class="exp-sec">
    <input type="hidden" name="Item_ID_{$Source:index}" value="{$Source:item.id}" />
    <input type="hidden" name="Ignore_Values_On_Browse_{$Source:index}" id="Ignore_Values_On_Browse_{$Source:index}" value="{$Source:item.title|eq('')}" />
    <div class="exp-sec-head">
        <div class="exp-sec-title"><h3>{'Source'|i18n( 'design/admin/rss/edit_export' )} {sum( $Source:index, 1 )}</h3></div>
        <div class="exp-actions">
            <button class="exp-btn exp-btn-small exp-btn-outline-danger" type="submit" name="{concat( 'RemoveSource_', $Source:index )}" value="1" title="{'Click to remove this source from the RSS export.'|i18n( 'design/admin/rss/edit_export' )}">{'Remove this source'|i18n( 'design/admin/rss/edit_export' )}</button>
        </div>
    </div>
    <div class="exp-form-fields exp-form-wide" style="margin-top: 10px;">
        <div class="exp-field">
            <label for="rssExporSource_{$Source:index}">{'Source path'|i18n( 'design/admin/rss/edit_export' )}</label>
            <div class="exp-inline">
                <input type="text" readonly="readonly" id="rssExporSource_{$Source:index}" value="{$Source:item.source_path|wash}" placeholder="{'No location chosen'|i18n( 'design/admin/rss/edit_export' )}" />
                <button class="exp-btn" type="submit" name="{concat( 'SourceBrowse_', $Source:index )}" value="1">{'Browse'|i18n( 'design/admin/rss/edit_export' )}</button>
            </div>
            <span class="exp-help">{'Click this button to select the source node for the RSS export source. Objects of the type selected in the drop-down below published as sub items of the selected node will be included in the RSS export.'|i18n( 'design/admin/rss/edit_export' )}</span>
        </div>
        <div class="exp-field">
            <label class="exp-check"><input type="checkbox" id="rssExporSubNodes_{$Source:index}" name="Item_Subnodes_{$Source:index}"{if $Source:item.subnodes|eq( 1 )} checked="checked"{/if} onchange="document.getElementById('Ignore_Values_On_Browse_{$Source:index}').value=0;" /> {'Subnodes'|i18n( 'design/admin/rss/edit_export' )}</label>
            <span class="exp-help">{'Activate this checkbox if objects from the subnodes of the source should also be fed.'|i18n( 'design/admin/rss/edit_export' )}</span>
        </div>
        <div class="exp-field">
            <label for="rssExporClass_{$Source:index}">{'Class'|i18n( 'design/admin/rss/edit_export' )}</label>
            <div class="exp-inline">
                <select id="rssExporClass_{$Source:index}" name="Item_Class_{$Source:index}" onchange="document.getElementById('Ignore_Values_On_Browse_{$Source:index}').value=0;">
                {section name=ContentClass loop=$rss_class_array}
                    <option value="{$:item.id}"{if eq( $:item.id, $Source:item.class_id )} selected="selected"{/if}>{$:item.name|wash}</option>
                {/section}
                </select>
                <button class="exp-btn" type="submit" name="Update_Item_Class" value="1">{'Set'|i18n( 'design/admin/rss/edit_export' )}</button>
            </div>
            <span class="exp-help">{'Click this button to load the correct values into the drop-down fields below. Use the drop-down menu on the left to select the class.'|i18n( 'design/admin/rss/edit_export' )}</span>
        </div>
    </div>

    {if count( $rss_export.item_list[$Source:index] )|gt( 0 )}
    <div class="exp-mapping">
        <div class="exp-field">
            <label for="rssExporClass_{$Source:index}_title">{'Title'|i18n( 'design/admin/rss/edit_export' )}</label>
            <select id="rssExporClass_{$Source:index}_title" name="Item_Class_Attribute_Title_{$Source:index}" onchange="document.getElementById('Ignore_Values_On_Browse_{$Source:index}').value=0;">
            {section name=ClassAttribute loop=$rss_export.item_list[$Source:index].class_attributes}
                <option value="{$:item.identifier|wash}"{if eq( $Source:item.title, $:item.identifier )} selected="selected"{/if}>{$:item.name|wash}</option>
            {/section}
            </select>
            <span class="exp-help">{'Use this drop-down to select the attribute that should be exported as the title of the RSS export entry.'|i18n( 'design/admin/rss/edit_export' )}</span>
        </div>
        <div class="exp-field">
            <label for="rssExporClass_{$Source:index}_desc">{'Description'|i18n( 'design/admin/rss/edit_export' )} ({'optional'|i18n( 'design/admin/rss/edit_export' )})</label>
            <select id="rssExporClass_{$Source:index}_desc" name="Item_Class_Attribute_Description_{$Source:index}" onchange="document.getElementById('Ignore_Values_On_Browse_{$Source:index}').value=0;">
                <option value="">[{'Skip'|i18n( 'design/admin/rss/edit_export' )}]</option>
            {section name=ClassAttribute loop=$rss_export.item_list[$Source:index].class_attributes}
                <option value="{$:item.identifier|wash}"{if eq( $Source:item.description, $:item.identifier )} selected="selected"{/if}>{$:item.name|wash}</option>
            {/section}
            </select>
            <span class="exp-help">{'Use this drop-down to select the attribute that should be exported as the description of the RSS export entry.'|i18n( 'design/admin/rss/edit_export' )}</span>
        </div>
        <div class="exp-field">
            <label for="rssExporClass_{$Source:index}_category">{'Category'|i18n( 'design/admin/rss/edit_export' )} ({'optional'|i18n( 'design/admin/rss/edit_export' )})</label>
            <select id="rssExporClass_{$Source:index}_category" name="Item_Class_Attribute_Category_{$Source:index}" onchange="document.getElementById('Ignore_Values_On_Browse_{$Source:index}').value=0;">
                <option value="">[{'Skip'|i18n( 'design/admin/rss/edit_export' )}]</option>
            {section name=ClassAttribute loop=$rss_export.item_list[$Source:index].class_attributes}
                <option value="{$:item.identifier|wash}"{if eq( $Source:item.category, $:item.identifier )} selected="selected"{/if}>{$:item.name|wash}</option>
            {/section}
            </select>
            <span class="exp-help">{'Use this drop-down to select the attribute that should be exported as the category of the RSS export entry.'|i18n( 'design/admin/rss/edit_export' )}</span>
        </div>
        <div class="exp-field">
            <label for="rssExporClass_{$Source:index}_enclosure">{'Enclosure (media)'|i18n( 'design/admin/rss/edit_export' )} ({'optional'|i18n( 'design/admin/rss/edit_export' )})</label>
            <select id="rssExporClass_{$Source:index}_enclosure" name="Item_Class_Attribute_Enclosure_{$Source:index}" onchange="document.getElementById('Ignore_Values_On_Browse_{$Source:index}').value=0;">
                <option value="">[{'Skip'|i18n( 'design/admin/rss/edit_export' )}]</option>
            {foreach $rss_export.item_list[$Source:index].class_attributes as $class_attribute}
                <option value="{$class_attribute.identifier|wash}"{if eq( $Source:item.enclosure, $class_attribute.identifier )} selected="selected"{/if}>{$class_attribute.name|wash}</option>
            {/foreach}
            </select>
            <span class="exp-help">{'A direct link to a media file of the item: choose a media, image or file attribute. Podcasts need it.'|i18n( 'design/admin/rss/edit_export' )}</span>
        </div>
    </div>
    {/if}
</li>
{/section}
</ul>

<div class="exp-actions" id="rssExportAddSource" style="margin-top: 12px;{if $rss_is_opml} display:none;{/if}">
    <button class="exp-btn" type="submit" name="AddSourceButton" value="1" title="{'Click to add a new source to the RSS export.'|i18n( 'design/admin/rss/edit_export' )}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 2h2v5h5v2H9v5H7V9H2V7h5z"/></svg>{'Add source'|i18n( 'design/admin/rss/edit_export' )}</button>
</div>
</section>

{* ---- The Apple Podcasts half. Shown when the format is Apple Podcasts, and only then. ---- *}
<section class="exp-panel" id="rssExportPodcast"{if $rss_is_podcast|not} style="display:none;"{/if} aria-labelledby="rss-export-podcast-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="rss-export-podcast-title">{'Apple Podcasts'|i18n( 'design/admin/rss/edit_export' )}</h2>
    <p>{'Apple reads these from the itunes namespace. A feed missing one it requires is rejected outright, so the required ones are marked. Everything else is optional and left out of the document when empty.'|i18n( 'design/admin/rss/edit_export' )}</p>
</div>
<div class="exp-form-fields exp-form-wide">
    <div class="exp-field">
        <label for="podcastAuthor">{'Author'|i18n( 'design/admin/rss/edit_export' )}</label>
        <input type="text" id="podcastAuthor" name="Podcast_author" value="{$podcast_head.author|wash}" />
        <span class="exp-help">{'The name shown as the show\'s author.'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>
    <div class="exp-field">
        <label for="podcastOwnerName">{'Owner name'|i18n( 'design/admin/rss/edit_export' )} <em>({'required'|i18n( 'design/admin/rss/edit_export' )})</em></label>
        <input type="text" id="podcastOwnerName" name="Podcast_ownerName" value="{$podcast_head.ownerName|wash}" />
    </div>
    <div class="exp-field">
        <label for="podcastOwnerEmail">{'Owner email'|i18n( 'design/admin/rss/edit_export' )} <em>({'required'|i18n( 'design/admin/rss/edit_export' )})</em></label>
        <input type="text" id="podcastOwnerEmail" name="Podcast_ownerEmail" value="{$podcast_head.ownerEmail|wash}" />
        <span class="exp-help">{'Apple writes to this address to confirm the show is yours. It is not published in the directory.'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>
    <div class="exp-field">
        <label for="podcastImageUrl">{'Artwork address'|i18n( 'design/admin/rss/edit_export' )} <em>({'required'|i18n( 'design/admin/rss/edit_export' )})</em></label>
        <input type="text" id="podcastImageUrl" name="Podcast_imageUrl" value="{$podcast_head.imageUrl|wash}" />
        <span class="exp-help">{'A square jpeg or png between 1400 and 3000 pixels, reachable without a login. Apple fetches it; a link it cannot follow is the commonest reason a feed is rejected.'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>
    <div class="exp-field">
        <label for="podcastCategory">{'Category'|i18n( 'design/admin/rss/edit_export' )} <em>({'required'|i18n( 'design/admin/rss/edit_export' )})</em></label>
        <select id="podcastCategory" name="Podcast_category">
            <option value="">{'Choose one'|i18n( 'design/admin/rss/edit_export' )}</option>
        {foreach $podcast_categories as $podcast_category}
            <option value="{$podcast_category.name|wash}"{if eq( $podcast_head.category, $podcast_category.name )} selected="selected"{/if}>{$podcast_category.name|wash}</option>
        {/foreach}
        </select>
    </div>
    <div class="exp-field">
        <label for="podcastSubcategory">{'Subcategory'|i18n( 'design/admin/rss/edit_export' )}</label>
        <select id="podcastSubcategory" name="Podcast_subcategory">
            <option value="">{'None'|i18n( 'design/admin/rss/edit_export' )}</option>
        {foreach $podcast_categories as $podcast_category}
            {foreach $podcast_category.subcategories as $podcast_subcategory}
            <option value="{$podcast_subcategory|wash}" data-parent="{$podcast_category.name|wash}"{if eq( $podcast_head.subcategory, $podcast_subcategory )} selected="selected"{/if}>{$podcast_subcategory|wash}</option>
            {/foreach}
        {/foreach}
        </select>
    </div>
    <div class="exp-field">
        <label for="podcastType">{'Show type'|i18n( 'design/admin/rss/edit_export' )}</label>
        <select id="podcastType" name="Podcast_type">
            <option value="episodic"{if eq( $podcast_head.type, 'episodic' )} selected="selected"{/if}>{'Episodic - newest first'|i18n( 'design/admin/rss/edit_export' )}</option>
            <option value="serial"{if eq( $podcast_head.type, 'serial' )} selected="selected"{/if}>{'Serial - oldest first'|i18n( 'design/admin/rss/edit_export' )}</option>
        </select>
    </div>
    <div class="exp-field">
        <label for="podcastLanguage">{'Language'|i18n( 'design/admin/rss/edit_export' )}</label>
        <input type="text" id="podcastLanguage" name="Podcast_language" value="{$podcast_head.language|wash}" />
        <span class="exp-help">{'A two letter code such as en, or a regional one such as en-us. Left empty the siteaccess language is used.'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>
    <div class="exp-field">
        <label for="podcastSubtitle">{'Subtitle'|i18n( 'design/admin/rss/edit_export' )}</label>
        <input type="text" id="podcastSubtitle" name="Podcast_subtitle" value="{$podcast_head.subtitle|wash}" />
    </div>
    <div class="exp-field">
        <label for="podcastSummary">{'Summary'|i18n( 'design/admin/rss/edit_export' )}</label>
        <textarea id="podcastSummary" name="Podcast_summary" rows="3">{$podcast_head.summary|wash}</textarea>
    </div>
    <div class="exp-field">
        <label for="podcastCopyright">{'Copyright'|i18n( 'design/admin/rss/edit_export' )}</label>
        <input type="text" id="podcastCopyright" name="Podcast_copyright" value="{$podcast_head.copyright|wash}" />
    </div>
    <div class="exp-field">
        <label class="exp-check"><input type="checkbox" id="podcastExplicit" name="Podcast_explicit" value="1"{if eq( $podcast_head.explicit, 'true' )} checked="checked"{/if} /> {'Contains explicit content'|i18n( 'design/admin/rss/edit_export' )}</label>
        <label class="exp-check"><input type="checkbox" id="podcastComplete" name="Podcast_complete" value="1"{if eq( $podcast_head.complete, 'true' )} checked="checked"{/if} /> {'The show is finished - no further episodes'|i18n( 'design/admin/rss/edit_export' )}</label>
        <label class="exp-check"><input type="checkbox" id="podcastBlock" name="Podcast_block" value="1"{if eq( $podcast_head.block, 'true' )} checked="checked"{/if} /> {'Keep the show out of the Apple Podcasts directory'|i18n( 'design/admin/rss/edit_export' )}</label>
    </div>
    <div class="exp-field">
        <label for="podcastNewFeedUrl">{'Moved to'|i18n( 'design/admin/rss/edit_export' )}</label>
        <input type="text" id="podcastNewFeedUrl" name="Podcast_newFeedUrl" value="{$podcast_head.newFeedUrl|wash}" />
        <span class="exp-help">{'The feed\'s new address, if it has moved. Apple follows it and updates every subscriber.'|i18n( 'design/admin/rss/edit_export' )}</span>
    </div>
</div>
<p class="exp-help" style="margin-top: 12px;">{'Each episode needs a source below whose enclosure is mapped to a media or file attribute. An episode with no enclosure is left out of the feed, because Apple rejects a feed containing one.'|i18n( 'design/admin/rss/edit_export' )}</p>
</section>

{* ---- The OPML half. Shown when the format is OPML, and only then. ---- *}
<div id="rssExportOPML" class="exp-opml"{if $rss_is_opml|not} style="display:none;"{/if}>
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

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="StoreButton" value="1" title="{'Apply the changes and return to the RSS overview.'|i18n( 'design/admin/rss/edit_export' )}">{'OK'|i18n( 'design/admin/rss/edit_export' )}</button>
        <button class="exp-btn" type="submit" name="RemoveButton" value="1" title="{'Cancel the changes and return to the RSS overview.'|i18n( 'design/admin/rss/edit_export' )}">{'Cancel'|i18n( 'design/admin/rss/edit_export' )}</button>
    </div>
    <p class="exp-meta">{'OK saves the export and goes back to the RSS list. Cancel throws away every change made since the export was opened, including added or removed sources, and goes back to the list.'|i18n( 'design/admin/rss/edit_export' )}</p>
</div>

</div></div></div>
</div>
</form>
{undef $feed_url $source_count}

<script type="text/javascript">
var expRssExportFocus = {if not( $valid )}'rss-export-errors'{else}'exportName'{/if};
{literal}
( function () {
    var focus = document.getElementById( expRssExportFocus );
    if ( focus ) { focus.focus(); if ( focus.select ) focus.select(); }

    var version = document.getElementById( 'rssExportVersion' );
    if ( !version ) return;

    // Which half of the page applies follows the format. The server decides the same thing after every submit;
    // this only saves waiting for one.
    function show()
    {
        var opml    = version.value === 'OPML',
            podcast = version.value === 'ITUNES',
            sources = document.getElementById( 'rssExportSources' ),
            panel   = document.getElementById( 'rssExportOPML' ),
            pod     = document.getElementById( 'rssExportPodcast' ),
            add     = document.getElementById( 'rssExportAddSource' ),
            limit   = document.getElementById( 'rssExportLimitBlock' );

        // A podcast keeps its sources: the episodes come from content. Only OPML replaces them with other feeds.
        if ( sources ) sources.style.display = opml ? 'none' : '';
        if ( panel )   panel.style.display   = opml ? '' : 'none';
        if ( pod )     pod.style.display     = podcast ? '' : 'none';
        if ( add )     add.style.display     = opml ? 'none' : '';
        if ( limit )   limit.style.display   = opml ? 'none' : '';

        subcategories();
    }

    // Only the subcategories of the chosen category are worth offering; Apple rejects a pairing that is not its own.
    function subcategories()
    {
        var category = document.getElementById( 'podcastCategory' ),
            sub      = document.getElementById( 'podcastSubcategory' );
        if ( !category || !sub ) return;
        var chosen = category.value, cleared = false;
        for ( var i = 0; i < sub.options.length; i++ )
        {
            var option = sub.options[i], parent = option.getAttribute( 'data-parent' );
            if ( !parent ) continue;
            var applies = parent === chosen;
            option.hidden = !applies;
            option.disabled = !applies;
            if ( !applies && option.selected ) { option.selected = false; cleared = true; }
        }
        if ( cleared ) sub.selectedIndex = 0;
    }

    version.onchange = show;
    var podcastCategory = document.getElementById( 'podcastCategory' );
    if ( podcastCategory ) podcastCategory.onchange = subcategories;
    show();

    // Enter in the feed browser's search box searches instead of pressing the first button of the form.
    var search = document.getElementById( 'feedBrowserSearch' ),
        apply  = document.getElementById( 'feedBrowserApply' );
    if ( search && apply )
    {
        search.onkeydown = function ( event ) {
            if ( ( event || window.event ).keyCode !== 13 ) return true;
            apply.click();
            return false;
        };
    }
} )();
{/literal}
</script>
