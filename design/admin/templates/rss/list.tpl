{* The list is fetched a page at a time; these are the controls that move
   through it. Everything else on this page is as it was. *}
{literal}
<style type="text/css">
.rss-pagination {
    display: flex; flex-wrap: wrap; align-items: center;
    justify-content: space-between; gap: .75rem; padding: .6rem 0;
}
.rss-pagination-count { color: #666; }
.rss-pagination-pages { margin: 0; }
.rss-pagination-pages p { margin: 0; }
.rss-pagination-pages a,
.rss-pagination-pages .current,
.rss-pagination-pages .disabled {
    display: inline-block; padding: .15rem .45rem; margin: 0 .1rem;
    border: 1px solid #d5d5da; border-radius: 4px; text-decoration: none;
}
.rss-pagination-pages .current { background: #4a4a52; border-color: #4a4a52; color: #fff; font-weight: 700; }
.rss-pagination-pages .disabled { color: #b0b0b6; border-color: #e6e6ea; }
.rss-pagination-of { margin: .35rem 0 0 0 !important; color: #666; font-size: .9em; }
/* Sortable headings. The whole cell reacts, but the link inside it is what
   actually carries the address. */
table.list th.sortable { cursor: pointer; white-space: nowrap; }
table.list th.sortable a.sort-link { display: block; text-decoration: none; color: inherit; }
table.list th.sortable:hover a.sort-link { text-decoration: underline; }
table.list th.sorted a.sort-link { font-weight: 700; }
.sort-arrow { display: inline-block; width: 1em; font-size: .8em; color: #6a6a72; }
table.list td.rss-id { text-align: right; color: #666; white-space: nowrap; }
table.list td.rss-uri code {
    font-family: Menlo, Consolas, monospace; font-size: .92em;
    background: #f4f4f5; border: 1px solid #e6e6ea; border-radius: 3px; padding: 0 .3rem;
}
</style>
{/literal}

{* Export window. *}
<form name="rssexportslist" method="post" action={'rss/list'|ezurl}>

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h2 class="context-title">{'RSS exports (%exports_count)'|i18n( 'design/admin/rss/list',, hash( '%exports_count', $rssexport_count ) )}</h2>

{* How many rows a page holds. The same control, and the same markup, that
   /section/list and the other admin lists use, so it is styled by the admin
   stylesheet rather than by anything of its own. Changing it starts both lists
   again from the top, and is remembered for the next visit. *}
<div class="context-toolbar">
<div class="button-left">
<p class="table-preferences">
{foreach $page_limit_links as $rss_limit}
    {if $rss_limit.current}<span class="current">{$rss_limit.limit}</span>
    {else}<a href={concat( '/rss/list', $rss_limit.suffix )|ezurl}>{$rss_limit.limit}</a>{/if}
{/foreach}
</p>
</div>
</div>



{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{section show=$rssexport_list}
<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} width="16" height="16" alt="{'Invert selection'|i18n( 'design/admin/rss/list' )}" onclick="ezjs_toggleCheckboxes( document.rssexportslist, 'DeleteIDArray[]' ); return false;" title="{'Invert selection.'|i18n( 'design/admin/rss/list' )}" /></th>
    {include uri='design:rss/sortheader.tpl' key='id'          label='ID'|i18n( 'design/admin/rss/list' )       sort=$rssexport_sort suffix=$rssexport_sort.suffix cell_class='tight'}
    {include uri='design:rss/sortheader.tpl' key='title'       label='Name'|i18n( 'design/admin/rss/list' )     sort=$rssexport_sort suffix=$rssexport_sort.suffix}
    {include uri='design:rss/sortheader.tpl' key='access_url'  label='URI'|i18n( 'design/admin/rss/list' )      sort=$rssexport_sort suffix=$rssexport_sort.suffix}
    {include uri='design:rss/sortheader.tpl' key='rss_version' label='Version'|i18n( 'design/admin/rss/list' )  sort=$rssexport_sort suffix=$rssexport_sort.suffix}
    {include uri='design:rss/sortheader.tpl' key='active'      label='Status'|i18n( 'design/admin/rss/list' )   sort=$rssexport_sort suffix=$rssexport_sort.suffix}
    {include uri='design:rss/sortheader.tpl' key='modifier_id' label='Modifier'|i18n( 'design/admin/rss/list' ) sort=$rssexport_sort suffix=$rssexport_sort.suffix}
    {include uri='design:rss/sortheader.tpl' key='modified'    label='Modified'|i18n( 'design/admin/rss/list' ) sort=$rssexport_sort suffix=$rssexport_sort.suffix}
    <th class="tight">&nbsp;</th>
</tr>
{section var=RSSExports loop=$rssexport_list sequence=array( bglight, bgdark )}
<tr class="{$RSSExports.sequence}">

    {* Remove. *}
    <td><input type="checkbox" name="DeleteIDArray[]" value="{$RSSExports.item.id}" title="{'Select RSS export for removal.'|i18n( 'design/admin/rss/list' )}" /></td>

    {* Feed id, as it appears in the edit address and in the database. *}
    <td class="rss-id">{$RSSExports.item.id}</td>

    {* Name. *}
    <td>{if $RSSExports.item.active|eq( 1 )} <a href={concat( 'rss/feed/', $RSSExports.item.access_url )|ezurl}>{$RSSExports.item.title|wash}</a>{else}{$RSSExports.item.title|wash}{/if}</td>

    {* The part of the address the feed answers on: /rss/feed/<this>. *}
    <td class="rss-uri">{if $RSSExports.item.access_url|ne('')}<code>{$RSSExports.item.access_url|wash}</code>{else}<span class="rss-pagination-count">{'not set'|i18n( 'design/admin/rss/list' )}</span>{/if}</td>

    {* Version. *}
    <td>{$RSSExports.item.rss_version|wash}</td>

    {* Status. *}
    <td>{if $RSSExports.item.active|eq( 1 )}{'Active'|i18n( 'design/admin/rss/list' )}{else}{'Inactive'|i18n( 'design/admin/rss/list' )}{/if}</td>

    {* Modifier. *}
    <td><a href={$RSSExports.item.modifier.contentobject.main_node.url_alias|ezurl}>{$RSSExports.item.modifier.contentobject.name|wash}</a></td>

    {* Modified. *}
    <td>{$RSSExports.item.modified|l10n( shortdatetime )}</td>

    {* Edit. *}
    <td><a href={concat( 'rss/edit_export/', $RSSExports.item.id )|ezurl}><img class="button" src={'edit.gif'|ezimage} width="16" height="16" alt="{'Edit'|i18n( 'design/admin/rss/list' )}" title="{'Edit the <%name> RSS export.'|i18n('design/admin/rss/list',, hash( '%name', $RSSExports.item.title) )|wash}" /></a></td>

</tr>
{/section}
</table>
{section-else}
<div class="block">
    <p>{'The RSS export list is empty.'|i18n( 'design/admin/rss/list' )}</p>
</div>
{/section}

{include uri='design:rss/pagination.tpl' pager=$rssexport_pager page_uri='/rss/list'}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    <input type="submit" name="RemoveExportButton" value="{'Remove selected'|i18n( 'design/admin/rss/list' )}" title="{'Remove selected RSS exports.'|i18n( 'design/admin/rss/list' ) }" {if $rssexport_list|not}class="button-disabled" disabled="disabled"{else}class="button"{/if}
/>
    <input class="button" type="submit" name="NewExportButton" value="{'New export'|i18n( 'design/admin/rss/list' )}" title="{'Create a new RSS export.'|i18n( 'design/admin/rss/list' )}" />
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

</form>



{* Import window. *}
<form name="rssimportslist" method="post" action={'rss/list'|ezurl}>

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h2 class="context-title">{'RSS imports (%imports_count)'|i18n( 'design/admin/rss/list',, hash( '%imports_count', $rssimport_count ) )}</h2>



{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{section show=$rssimport_list}
<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} width="16" height="16" alt="{'Invert selection'|i18n( 'design/admin/rss/list' )}" onclick="ezjs_toggleCheckboxes( document.rssimportslist, 'DeleteIDArrayImport[]' ); return false;" title="{'Invert selection.'|i18n( 'design/admin/rss/list' )}" /></th>
    {include uri='design:rss/sortheader.tpl' key='id'          label='ID'|i18n( 'design/admin/rss/list' )         sort=$rssimport_sort suffix=$rssimport_sort.suffix sort_name='importsort' dir_name='importdir' cell_class='tight'}
    {include uri='design:rss/sortheader.tpl' key='name'        label='Name'|i18n( 'design/admin/rss/list' )       sort=$rssimport_sort suffix=$rssimport_sort.suffix sort_name='importsort' dir_name='importdir'}
    {include uri='design:rss/sortheader.tpl' key='url'         label='Source URL'|i18n( 'design/admin/rss/list' ) sort=$rssimport_sort suffix=$rssimport_sort.suffix sort_name='importsort' dir_name='importdir'}
    {include uri='design:rss/sortheader.tpl' key='active'      label='Status'|i18n( 'design/admin/rss/list' )     sort=$rssimport_sort suffix=$rssimport_sort.suffix sort_name='importsort' dir_name='importdir'}
    {include uri='design:rss/sortheader.tpl' key='modifier_id' label='Modifier'|i18n( 'design/admin/rss/list' )   sort=$rssimport_sort suffix=$rssimport_sort.suffix sort_name='importsort' dir_name='importdir'}
    {include uri='design:rss/sortheader.tpl' key='modified'    label='Modified'|i18n( 'design/admin/rss/list' )   sort=$rssimport_sort suffix=$rssimport_sort.suffix sort_name='importsort' dir_name='importdir'}
    <th class="tight">&nbsp;</th>
</tr>
{section var=RSSImports loop=$rssimport_list sequence=array( bglight, bgdark )}
<tr class="{$RSSImports.sequence}">

    {* Remove. *}
    <td><input type="checkbox" name="DeleteIDArrayImport[]" value="{$RSSImports.item.id}" title="{'Select RSS import for removal.'|i18n( 'design/admin/rss/list' )}" /></td>

    {* Import id. *}
    <td class="rss-id">{$RSSImports.item.id}</td>

    {* Name. *}
    <td>{$RSSImports.item.name|wash}</td>

    {* Where it reads from. *}
    <td class="rss-uri">{if $RSSImports.item.url|ne('')}<code>{$RSSImports.item.url|wash}</code>{else}<span class="rss-pagination-count">{'not set'|i18n( 'design/admin/rss/list' )}</span>{/if}</td>

    {* Status. *}
    <td>{if $RSSImports.item.active|eq(1)}{'Active'|i18n( 'design/admin/rss/list' )}{else}{'Inactive'|i18n( 'design/admin/rss/list' )}{/if}</td>

    {* Modifier. *}
    <td><a href={$RSSImports.item.modifier.contentobject.main_node.url_alias|ezurl}>{$RSSImports.item.modifier.contentobject.name|wash}</a></td>

    {* Modified. *}
    <td>{$RSSImports.item.modified|l10n( shortdatetime )}</td>

    {* Edit. *}
    <td><a href={concat( 'rss/edit_import/', $RSSImports.item.id )|ezurl}><img class="button" src={'edit.gif'|ezimage} width="16" height="16" alt="{'Edit'|i18n( 'design/admin/rss/list' )}" title="{'Edit the <%name> RSS import.'|i18n('design/admin/rss/list',, hash( '%name', $RSSImports.item.name) )|wash }" /></a></td>

</tr>
{/section}
</table>
{section-else}
<div class="block">
    <p>{'The RSS import list is empty.'|i18n( 'design/admin/rss/list' )}</p>
</div>
{/section}

{include uri='design:rss/pagination.tpl' pager=$rssimport_pager page_uri='/rss/list'}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    <input {if $rssimport_list|count}class="button"{else}class="button-disabled" disabled="disabled"{/if} type="submit" name="RemoveImportButton" value="{'Remove selected'|i18n( 'design/admin/rss/list' )}" title="{'Remove selected RSS imports.'|i18n( 'design/admin/rss/list' ) }" />
    <input class="button" type="submit" name="NewImportButton" value="{'New import'|i18n( 'design/admin/rss/list' )}" title="{'Create a new RSS import.'|i18n( 'design/admin/rss/list' )}" />
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

</form>

{* The headings are links already; this only widens the click target to the
   whole cell and lets the keyboard reach them. Sorting itself is done by the
   database, because the list is shown a page at a time - reordering the rows
   on screen would sort twenty five of four thousand. *}
{literal}
<script type="text/javascript">
( function () {
    var headings = document.getElementsByTagName( 'th' ), i;

    for ( i = 0; i < headings.length; i++ )
    {
        if ( headings[i].className.indexOf( 'sortable' ) === -1 )
            continue;

        ( function ( heading ) {
            var links = heading.getElementsByTagName( 'a' );
            if ( !links.length )
                return;

            var href = links[0].href;

            heading.onclick = function ( event ) {
                // The link itself is left to the browser, so a middle click or
                // a modifier still opens a new tab the way it should.
                var target = ( event && event.target ) || window.event.srcElement;
                while ( target && target !== heading )
                {
                    if ( target.tagName && target.tagName.toLowerCase() === 'a' )
                        return true;
                    target = target.parentNode;
                }
                if ( event && ( event.metaKey || event.ctrlKey || event.shiftKey || event.button > 0 ) )
                    return true;

                window.location.href = href;
                return false;
            };

            heading.setAttribute( 'tabindex', '0' );
            heading.onkeydown = function ( event ) {
                var key = ( event || window.event ).keyCode;
                if ( key !== 13 && key !== 32 )
                    return true;
                window.location.href = href;
                return false;
            };
        } )( headings[i] );
    }
} )();
</script>
{/literal}
