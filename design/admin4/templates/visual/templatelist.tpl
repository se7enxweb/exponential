{* Design > Templates: every template of the siteaccess and the most common ones, each linked to its overrides
   (visual/templateview). The same file is in design/admin and design/admin4; the styling is the shared admin
   components (components.css). *}
<div class="context-block a4-page a4-visual">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Template list'|i18n( 'design/admin/visual/templatelist' )}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<form class="a4-toolbar" action={"/visual/templatelist"|ezurl} method="post">
    <div class="a4-actions">
        <input class="a4-input is-auto" type="text" size="30" name="filterString" id="Filter" aria-label="{'Filter'|i18n( 'design/standard/layout' )|wash}" value="{$filterString|wash}" />
        <input class="a4-btn" name="FilterButton" type="submit" value="{'Filter'|i18n( 'design/standard/layout' )|wash}" />
    </div>
</form>

<div class="a4-table-wrap">
<table class="a4-table">
<thead>
<tr>
    <th scope="col">{'Template'|i18n( 'design/admin/visual/templatelist' )}</th>
    <th scope="col">{'Design resource'|i18n( 'design/admin/visual/templatelist' )}</th>
</tr>
</thead>
<tbody>
{section var=Templates max=20 offset=$view_parameters.offset loop=$template_array}
<tr>
    <td><a href={concat( '/visual/templateview', $Templates.item.template )|ezurl} title="{'Manage overrides for template.'|i18n( 'design/admin/visual/templatelist' )|wash}">{$Templates.item.template|wash}</a></td>
    <td><code>{$Templates.item.base_dir|wash}</code></td>
</tr>
{/section}
</tbody>
</table>
</div>

<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/visual/templatelist'
         page_uri_suffix=$filter_suffix
         item_count=$template_count
         view_parameters=$view_parameters
         item_limit=20}
</div>

<h2>{'Most common templates'|i18n( 'design/admin/visual/templatelist' )}</h2>

<div class="a4-table-wrap">
<table class="a4-table">
<thead>
<tr>
    <th scope="col">{'Template'|i18n( 'design/admin/visual/templatelist' )}</th>
    <th scope="col">{'Design resource'|i18n( 'design/admin/visual/templatelist' )}</th>
</tr>
</thead>
<tbody>
{section var=Templates loop=$most_used_template_array}
<tr>
    <td><a href={concat( '/visual/templateview', $Templates.item.template )|ezurl} title="{'Manage overrides for template.'|i18n( 'design/admin/visual/templatelist' )|wash}">{$Templates.item.template|wash}</a></td>
    <td><code>{$Templates.item.base_dir|wash}</code></td>
</tr>
{/section}
</tbody>
</table>
</div>

</div></div></div>

</div>
