{* Setup > Site data (setup/sitedata): the rows of ezsite_data, read only, with a filter and paging.

   Variables: rows (name, value, template_readable), count, limit, filter, template_readable (the names of
   site.ini [SiteDataSettings] TemplateReadable[]), view_parameters, page_uri.

   The same file is in design/admin and design/admin4. Guide: doc/guides/system-information.md ("Site data")
   Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 94b7b6c239), Copyright (C) Mugo Web, GPL v2. *}
<div class="context-block exp-sitedata">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Site data (%count)'|i18n( 'design/admin/setup/sitedata',, hash( '%count', $count ) )}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p>{'Site data are name and value pairs the installation and its extensions keep in the database, such as the version of the installed database schema. This list is read only.'|i18n( 'design/admin/setup/sitedata' )}</p>
<p>{"Templates can read a row with fetch( 'content', 'site_data', hash( 'name', ... ) ) only when its name is listed in site.ini [SiteDataSettings] TemplateReadable[]."|i18n( 'design/admin/setup/sitedata' )}&nbsp;
{if $template_readable|count|gt( 0 )}{'Listed now: %names.'|i18n( 'design/admin/setup/sitedata',, hash( '%names', $template_readable|implode( ', ' )|wash ) )}{else}{'No name is listed now.'|i18n( 'design/admin/setup/sitedata' )}{/if}</p>

<form method="get" action={'/setup/sitedata'|ezurl} class="exp-sitedata-filter">
<div class="block">
    <label for="exp-sitedata-filter">{'Name'|i18n( 'design/admin/setup/sitedata' )}</label>
    <input type="text" id="exp-sitedata-filter" name="Filter" value="{$filter|wash}" size="30" maxlength="100" />
    <input class="button" type="submit" value="{'Filter'|i18n( 'design/admin/setup/sitedata' )}" />
    {if $filter}<a href={'/setup/sitedata'|ezurl}>{'Show all'|i18n( 'design/admin/setup/sitedata' )}</a>{/if}
    <div class="exp-sitedata-hint">{'A whole name, or * for any characters (ezpublish-*).'|i18n( 'design/admin/setup/sitedata' )}</div>
</div>
</form>

{if $rows|count|gt( 0 )}
<table class="list exp-sitedata-list" cellspacing="0" summary="{'Site data'|i18n( 'design/admin/setup/sitedata' )}">
<tr>
    <th scope="col">{'Name'|i18n( 'design/admin/setup/sitedata' )}</th>
    <th scope="col">{'Value'|i18n( 'design/admin/setup/sitedata' )}</th>
    <th scope="col">{'Readable in templates'|i18n( 'design/admin/setup/sitedata' )}</th>
</tr>
{foreach $rows as $row sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td><code>{$row.name|wash}</code></td>
    <td class="exp-sitedata-value">{$row.value|wash}</td>
    <td>{if $row.template_readable}{'Yes'|i18n( 'design/admin/setup/sitedata' )}{else}{'No'|i18n( 'design/admin/setup/sitedata' )}{/if}</td>
</tr>
{/foreach}
</table>
{else}
<p class="exp-sitedata-empty">{if $filter}{'No site data matches the filter.'|i18n( 'design/admin/setup/sitedata' )}{else}{'There is no site data.'|i18n( 'design/admin/setup/sitedata' )}{/if}</p>
{/if}

<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=$page_uri
         item_count=$count
         view_parameters=$view_parameters
         item_limit=$limit}
</div>

</div></div></div>

</div>
<style>
.exp-sitedata-value {ldelim} word-break: break-word; {rdelim}
.exp-sitedata-hint {ldelim} font-size: 0.85em; opacity: 0.8; margin-top: 0.25em; {rdelim}
</style>
