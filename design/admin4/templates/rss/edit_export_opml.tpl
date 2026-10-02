{* The OPML half of the RSS export edit page.

   An OPML document is a list of feeds rather than a list of articles, so an
   export set to OPML has no content source and no class mapping. What it has
   instead is a head, a set of outlines, and a browser over the other exports on
   this installation to fill those outlines from.

   Every control here is a submit button rather than a link. They sit inside the
   edit form, and a link would leave the page taking everything typed into it
   along - each button writes the draft first and then acts. *}

<div class="block"><fieldset>
<legend>{'OPML head'|i18n( 'design/admin/rss/edit_export' )}</legend>
<div class="context-attributes">
    <p>{'These become the <head> of the document. The title, and the dates, are taken from the export itself. Anything left empty is left out rather than written empty.'|i18n( 'design/admin/rss/edit_export' )|wash}</p>
</div>

<div class="block">
<label class="inline" for="opmlOwnerName">{'Owner name'|i18n( 'design/admin/rss/edit_export' )}:</label>
<input class="halfbox" type="text" id="opmlOwnerName" name="OPMLHead_ownerName" value="{$opml_head.ownerName|wash}" title="{'Who put this list together. Left empty, the export\'s creator is used.'|i18n( 'design/admin/rss/edit_export' )|wash}" />
</div>

<div class="block">
<label class="inline" for="opmlOwnerEmail">{'Owner email'|i18n( 'design/admin/rss/edit_export' )}:</label>
<input class="halfbox" type="text" id="opmlOwnerEmail" name="OPMLHead_ownerEmail" value="{$opml_head.ownerEmail|wash}" title="{'Left empty, the administrator address from site.ini is used.'|i18n( 'design/admin/rss/edit_export' )}" />
</div>

<div class="block">
<label class="inline" for="opmlOwnerId">{'Owner id'|i18n( 'design/admin/rss/edit_export' )}:</label>
<input class="halfbox" type="text" id="opmlOwnerId" name="OPMLHead_ownerId" value="{$opml_head.ownerId|wash}" title="{'An address that identifies the owner, if you publish one.'|i18n( 'design/admin/rss/edit_export' )}" />
</div>

<div class="block">
<label class="inline" for="opmlDocs">{'Docs'|i18n( 'design/admin/rss/edit_export' )}:</label>
<input class="halfbox" type="text" id="opmlDocs" name="OPMLHead_docs" value="{$opml_head.docs|wash}" title="{'Where the format this document follows is written down.'|i18n( 'design/admin/rss/edit_export' )}" />
</div>

<div class="block opml-window">
<label class="inline">{'Outliner state'|i18n( 'design/admin/rss/edit_export' )}:</label>
<span class="opml-window-field"><label for="opmlExpansionState">{'expansionState'|i18n( 'design/admin/rss/edit_export' )}</label>
<input type="text" id="opmlExpansionState" name="OPMLHead_expansionState" value="{$opml_head.expansionState|wash}" size="12" /></span>
<span class="opml-window-field"><label for="opmlVertScrollState">{'vertScrollState'|i18n( 'design/admin/rss/edit_export' )}</label>
<input type="text" id="opmlVertScrollState" name="OPMLHead_vertScrollState" value="{$opml_head.vertScrollState|wash}" size="6" /></span>
<span class="opml-window-field"><label for="opmlWindowTop">{'top'|i18n( 'design/admin/rss/edit_export' )}</label>
<input type="text" id="opmlWindowTop" name="OPMLHead_windowTop" value="{$opml_head.windowTop|wash}" size="5" /></span>
<span class="opml-window-field"><label for="opmlWindowLeft">{'left'|i18n( 'design/admin/rss/edit_export' )}</label>
<input type="text" id="opmlWindowLeft" name="OPMLHead_windowLeft" value="{$opml_head.windowLeft|wash}" size="5" /></span>
<span class="opml-window-field"><label for="opmlWindowBottom">{'bottom'|i18n( 'design/admin/rss/edit_export' )}</label>
<input type="text" id="opmlWindowBottom" name="OPMLHead_windowBottom" value="{$opml_head.windowBottom|wash}" size="5" /></span>
<span class="opml-window-field"><label for="opmlWindowRight">{'right'|i18n( 'design/admin/rss/edit_export' )}</label>
<input type="text" id="opmlWindowRight" name="OPMLHead_windowRight" value="{$opml_head.windowRight|wash}" size="5" /></span>
<div class="context-attributes"><p>{'Where an outliner last had this list open on screen. Readers that do not keep window state ignore them.'|i18n( 'design/admin/rss/edit_export' )}</p></div>
</div>
</fieldset></div>


{* ------------------------------------------------ what is in the document *}

<div class="block"><fieldset>
<legend>{'Feeds in this document'|i18n( 'design/admin/rss/edit_export' )} ({$opml_items|count})</legend>

{if $opml_items|count|gt( 0 )}
<table class="list opml-outlines" cellspacing="0">
<tr>
    <th class="tight">&nbsp;</th>
    <th class="tight">{'Order'|i18n( 'design/admin/rss/edit_export' )}</th>
    <th class="tight">{'Type'|i18n( 'design/admin/rss/edit_export' )}</th>
    <th>{'Points at'|i18n( 'design/admin/rss/edit_export' )}</th>
    <th>{'Text'|i18n( 'design/admin/rss/edit_export' )}</th>
    <th>{'Category'|i18n( 'design/admin/rss/edit_export' )}</th>
    <th class="tight">{'Inside'|i18n( 'design/admin/rss/edit_export' )}</th>
</tr>
{foreach $opml_items as $opml_item sequence array( bglight, bgdark ) as $opml_seq}
<tr class="{$opml_seq}">
    <td><input type="checkbox" name="OPMLItemRemove[]" value="{$opml_item.id}" title="{'Select this outline for removal.'|i18n( 'design/admin/rss/edit_export' )}" /><input type="hidden" name="OPMLItem_ID[]" value="{$opml_item.id}" /></td>
    <td class="tight"><input type="text" name="OPMLItem_Priority[{$opml_item.id}]" value="{$opml_item.priority}" size="3" title="{'Lower numbers come first in the document.'|i18n( 'design/admin/rss/edit_export' )}" /></td>
    <td class="tight">
        <select name="OPMLItem_Type[{$opml_item.id}]">
        {foreach $opml_outline_types as $opml_type_key => $opml_type_label}
            <option value="{$opml_type_key}"{if eq( $opml_item.outline_type, $opml_type_key )} selected="selected"{/if}>{$opml_type_label|i18n( 'design/admin/rss/edit_export' )}</option>
        {/foreach}
        </select>
    </td>
    <td>
    {if $opml_item.target}
        <a href={concat( 'rss/edit_export/', $opml_item.target.id )|ezurl}>{$opml_item.target.title|wash}</a>
        <span class="opml-meta">{$opml_item.target.rss_version|wash} &middot; <code>{$opml_item.target.access_url|wash}</code></span>
    {elseif $opml_item.target_gone}
        <span class="opml-gone">{'The feed this pointed at has been deleted.'|i18n( 'design/admin/rss/edit_export' )}</span>
    {elseif $opml_item.source_node}
        <span class="opml-meta">{$opml_item.source_path|wash}</span>
        <button type="submit" class="button" name="ClearOPMLNode" value="{$opml_item.id}">{'Clear'|i18n( 'design/admin/rss/edit_export' )}</button>
    {else}
        <button type="submit" class="button" name="BrowseOPMLNode" value="{$opml_item.id}" title="{'Point this outline at a content node instead of a feed.'|i18n( 'design/admin/rss/edit_export' )}">{'Browse content'|i18n( 'design/admin/rss/edit_export' )}</button>
    {/if}
    </td>
    <td><input type="text" name="OPMLItem_Text[{$opml_item.id}]" value="{$opml_item.outline_text|wash}" size="24" title="{'What a reader shows on the line. Left empty, the feed\'s own name is used.'|i18n( 'design/admin/rss/edit_export' )|wash}" /></td>
    <td><input type="text" name="OPMLItem_Category[{$opml_item.id}]" value="{$opml_item.category|wash}" size="14" /></td>
    <td class="tight">
        <select name="OPMLItem_Parent[{$opml_item.id}]">
            <option value="0">{'Top level'|i18n( 'design/admin/rss/edit_export' )}</option>
        {foreach $opml_groups as $opml_group}
            {if ne( $opml_group.id, $opml_item.id )}
            <option value="{$opml_group.id}"{if eq( $opml_item.parent_id, $opml_group.id )} selected="selected"{/if}>{$opml_group.label|wash}</option>
            {/if}
        {/foreach}
        </select>
    </td>
</tr>
<tr class="{$opml_seq}">
    <td>&nbsp;</td>
    <td colspan="6" class="opml-more">
    <details>
        <summary>{'Everything else OPML lets this line carry'|i18n( 'design/admin/rss/edit_export' )}</summary>
        <div class="opml-more-grid">
            <span><label for="opmlTitle{$opml_item.id}">{'title'|i18n( 'design/admin/rss/edit_export' )}</label>
            <input type="text" id="opmlTitle{$opml_item.id}" name="OPMLItem_Title[{$opml_item.id}]" value="{$opml_item.title|wash}" size="24" /></span>

            <span><label for="opmlDesc{$opml_item.id}">{'description'|i18n( 'design/admin/rss/edit_export' )}</label>
            <input type="text" id="opmlDesc{$opml_item.id}" name="OPMLItem_Description[{$opml_item.id}]" value="{$opml_item.description|wash}" size="30" /></span>

            <span><label for="opmlLang{$opml_item.id}">{'language'|i18n( 'design/admin/rss/edit_export' )}</label>
            <input type="text" id="opmlLang{$opml_item.id}" name="OPMLItem_Language[{$opml_item.id}]" value="{$opml_item.language|wash}" size="8" /></span>

            <span><label for="opmlXml{$opml_item.id}">{'xmlUrl'|i18n( 'design/admin/rss/edit_export' )}</label>
            <input type="text" id="opmlXml{$opml_item.id}" name="OPMLItem_XmlUrl[{$opml_item.id}]" value="{$opml_item.xml_url|wash}" size="34" title="{'Left empty, the address of the feed this points at is worked out when the document is written.'|i18n( 'design/admin/rss/edit_export' )}" /></span>

            <span><label for="opmlHtml{$opml_item.id}">{'htmlUrl'|i18n( 'design/admin/rss/edit_export' )}</label>
            <input type="text" id="opmlHtml{$opml_item.id}" name="OPMLItem_HtmlUrl[{$opml_item.id}]" value="{$opml_item.html_url|wash}" size="34" /></span>

            <span><label for="opmlUrl{$opml_item.id}">{'url'|i18n( 'design/admin/rss/edit_export' )}</label>
            <input type="text" id="opmlUrl{$opml_item.id}" name="OPMLItem_Url[{$opml_item.id}]" value="{$opml_item.url|wash}" size="34" title="{'Used by the link and include types.'|i18n( 'design/admin/rss/edit_export' )}" /></span>

            <span class="opml-more-flags">
            <label><input type="checkbox" name="OPMLItem_IsComment[{$opml_item.id}]" {if $opml_item.is_comment|eq( 1 )}checked="checked"{/if} /> {'isComment'|i18n( 'design/admin/rss/edit_export' )}</label>
            <label><input type="checkbox" name="OPMLItem_IsBreakpoint[{$opml_item.id}]" {if $opml_item.is_breakpoint|eq( 1 )}checked="checked"{/if} /> {'isBreakpoint'|i18n( 'design/admin/rss/edit_export' )}</label>
            <label><input type="checkbox" name="OPMLItem_Subnodes[{$opml_item.id}]" {if $opml_item.subnodes|eq( 1 )}checked="checked"{/if} /> {'include subnodes'|i18n( 'design/admin/rss/edit_export' )}</label>
            </span>
        </div>
    </details>
    </td>
</tr>
{/foreach}
</table>
{else}
<div class="block"><p>{'Nothing is listed yet. Find feeds below and add them.'|i18n( 'design/admin/rss/edit_export' )}</p></div>
{/if}

<div class="block opml-actions">
    <input class="button" type="submit" name="RemoveOPMLItemsButton" value="{'Remove selected'|i18n( 'design/admin/rss/edit_export' )}" title="{'Take the ticked outlines out of the document.'|i18n( 'design/admin/rss/edit_export' )}" />
    <input class="button" type="submit" name="AddOPMLGroupButton" value="{'Add group'|i18n( 'design/admin/rss/edit_export' )}" title="{'Add a folder that other outlines can sit inside. OPML nests outlines, and readers show that nesting as groups.'|i18n( 'design/admin/rss/edit_export' )}" />
</div>
</fieldset></div>


{* --------------------------------------------------------- the browser ---*}

<div class="block"><fieldset>
<legend>{'Find feeds to add'|i18n( 'design/admin/rss/edit_export' )}</legend>

<input type="hidden" name="FeedBrowserOffset" value="{$opml_browser_pager.offset}" />
<input type="hidden" name="FeedBrowserSort" value="{$opml_browser_pager.sort.field|wash}" />
<input type="hidden" name="FeedBrowserDir" value="{$opml_browser_pager.sort.direction|wash}" />

<div class="block opml-browser-controls">
    <label for="feedBrowserSearch">{'Search'|i18n( 'design/admin/rss/edit_export' )}:</label>
    <input type="text" id="feedBrowserSearch" name="FeedBrowserSearch" value="{$opml_browser_search|wash}" size="24" title="{'Looks in the name, the address and the description.'|i18n( 'design/admin/rss/edit_export' )}" />
    <label for="feedBrowserLimit">{'Per page'|i18n( 'design/admin/rss/edit_export' )}:</label>
    <select id="feedBrowserLimit" name="FeedBrowserLimit">
    {foreach $opml_browser_limits as $opml_limit}
        <option value="{$opml_limit}"{if eq( $opml_browser_pager.limit, $opml_limit )} selected="selected"{/if}>{$opml_limit}</option>
    {/foreach}
    </select>
    <button type="submit" class="button" name="FeedBrowserApply" value="1" id="feedBrowserApply">{'Apply'|i18n( 'design/admin/rss/edit_export' )}</button>
    <span class="opml-meta">{'Showing %from to %to of %count'|i18n( 'design/admin/rss/edit_export',, hash( '%from', $opml_browser_pager.from, '%to', $opml_browser_pager.to, '%count', $opml_browser_pager.count ) )}</span>
</div>

{if $opml_browser_list|count|gt( 0 )}
<table class="list opml-browser" cellspacing="0">
<tr>
    <th class="tight">&nbsp;</th>
    {include uri='design:rss/sortbutton.tpl' key='id'          label='ID'|i18n( 'design/admin/rss/edit_export' )       sort=$opml_browser_pager.sort}
    {include uri='design:rss/sortbutton.tpl' key='title'       label='Name'|i18n( 'design/admin/rss/edit_export' )     sort=$opml_browser_pager.sort}
    {include uri='design:rss/sortbutton.tpl' key='access_url'  label='URI'|i18n( 'design/admin/rss/edit_export' )      sort=$opml_browser_pager.sort}
    {include uri='design:rss/sortbutton.tpl' key='rss_version' label='Version'|i18n( 'design/admin/rss/edit_export' )  sort=$opml_browser_pager.sort}
    {include uri='design:rss/sortbutton.tpl' key='active'      label='Status'|i18n( 'design/admin/rss/edit_export' )   sort=$opml_browser_pager.sort}
    {include uri='design:rss/sortbutton.tpl' key='modified'    label='Modified'|i18n( 'design/admin/rss/edit_export' ) sort=$opml_browser_pager.sort}
</tr>
{foreach $opml_browser_list as $opml_candidate sequence array( bglight, bgdark ) as $opml_candidate_seq}
<tr class="{$opml_candidate_seq}{if $opml_candidate.selected} opml-already{/if}">
    <td>{if $opml_candidate.selected}<span class="opml-tick" title="{'Already in this document.'|i18n( 'design/admin/rss/edit_export' )}">&#10003;</span>{else}<input type="checkbox" name="FeedBrowserSelect[]" value="{$opml_candidate.id}" />{/if}</td>
    <td class="tight opml-meta">{$opml_candidate.id}</td>
    <td>{$opml_candidate.title|wash}</td>
    <td><code>{$opml_candidate.access_url|wash}</code></td>
    <td>{$opml_candidate.rss_version|wash}</td>
    <td>{if $opml_candidate.active|eq( 1 )}{'Active'|i18n( 'design/admin/rss/edit_export' )}{else}{'Inactive'|i18n( 'design/admin/rss/edit_export' )}{/if}</td>
    <td class="opml-meta">{$opml_candidate.modified|l10n( shortdatetime )}</td>
</tr>
{/foreach}
</table>

{if $opml_browser_pager.needed}
<div class="block opml-browser-pages">
    {if $opml_browser_pager.has_previous}
    <button type="submit" class="button" name="FeedBrowserSetOffset" value="{$opml_browser_pager.first}">&laquo;</button>
    <button type="submit" class="button" name="FeedBrowserSetOffset" value="{$opml_browser_pager.previous}">&lsaquo;</button>
    {/if}
    {foreach $opml_browser_pager.pages as $opml_page}
        {if $opml_page.current}<span class="current">{$opml_page.number}</span>
        {else}<button type="submit" class="button" name="FeedBrowserSetOffset" value="{$opml_page.offset}">{$opml_page.number}</button>{/if}
    {/foreach}
    {if $opml_browser_pager.has_next}
    <button type="submit" class="button" name="FeedBrowserSetOffset" value="{$opml_browser_pager.next}">&rsaquo;</button>
    <button type="submit" class="button" name="FeedBrowserSetOffset" value="{$opml_browser_pager.last}">&raquo;</button>
    {/if}
    <span class="opml-meta">{'Page %page of %pages'|i18n( 'design/admin/rss/edit_export',, hash( '%page', $opml_browser_pager.page, '%pages', $opml_browser_pager.page_count ) )}</span>
</div>
{/if}

<div class="block opml-actions">
    <input class="defaultbutton" type="submit" name="AddFeedsButton" value="{'Add selected feeds'|i18n( 'design/admin/rss/edit_export' )}" title="{'Add the ticked feeds to this document.'|i18n( 'design/admin/rss/edit_export' )}" />
</div>
{else}
<div class="block"><p>{'No feed matches that.'|i18n( 'design/admin/rss/edit_export' )}</p></div>
{/if}
</fieldset></div>
