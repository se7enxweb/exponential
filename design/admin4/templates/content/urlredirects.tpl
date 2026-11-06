{* The redirects checked on the full request (content/urlredirects[/(offset)/<n>]): the rows of exp_url_redirect that
   URL map imports made, read only apart from removing one (its Remove button) or the ticked ones.

   Variables (kernel/private/classes/views/content/urlredirects.php): redirects, redirect_count, redirect_limit,
   redirect_removed (-1: nothing removed), redirect_tables, view_parameters. The same file is in design/admin and
   design/admin4. *}

<div class="context-block a4-page a4-url a4-content-urlmap">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Redirects (%count)'|i18n( 'design/admin/content/urlimport',, hash( '%count', $redirect_count ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'These redirects come from URL map imports: old addresses with a query (/index.php?id=12), temporary redirects and redirects to other sites. They are checked on the full address, before the URL aliases and wildcards. A permanent redirect to a page of this site is a global URL alias instead and is listed there.'|i18n( 'design/admin/content/urlimport' )}</p>

{include uri='design:content/urlmap_toolbar.tpl' current='redirects' all=true()}

{if $redirect_removed|ge( 0 )}<div class="a4-notice is-ok" role="status"><p>{'%count redirects removed.'|i18n( 'design/admin/content/urlimport',, hash( '%count', $redirect_removed ) )|wash}</p></div>{/if}
{if $redirect_tables|not}
<div class="a4-notice is-warn" role="alert"><p>{'The tables of URL map imports do not exist yet. Run the upgrade step 6.0.15-url-map (./console exp:upgrade --apply) before importing.'|i18n( 'design/admin/content/urlimport' )}</p></div>
{/if}

{if $redirects|count|eq( 0 )}
<p class="a4-empty">{'There are no redirects. They are made by importing a URL map.'|i18n( 'design/admin/content/urlimport' )}</p>
{else}
<form method="post" action={concat( 'content/urlredirects', cond( $view_parameters.offset|gt( 0 ), concat( '/(offset)/', $view_parameters.offset ), '' ) )|ezurl}>
<div class="a4-table-wrap">
<table class="a4-table a4-content-urlmap-table">
<caption class="a4-sr-only">{'Redirects checked on the full address'|i18n( 'design/admin/content/urlimport' )}</caption>
<thead><tr>
    <th scope="col"><span class="a4-sr-only">{'Select'|i18n( 'design/admin/content/urlimport' )}</span></th>
    <th scope="col">{'Old address'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Leads to'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col" class="a4-num">{'Status'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Siteaccess'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Language'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'From import'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col"><span class="a4-sr-only">{'Remove'|i18n( 'design/admin/content/urlimport' )}</span></th>
</tr></thead>
<tbody>
{foreach $redirects as $redirect}
<tr>
    <td><input type="checkbox" name="RedirectIDList[]" value="{$redirect.id}" aria-label="{'Select %address'|i18n( 'design/admin/content/urlimport',, hash( '%address', $redirect.source ) )|wash}" /></td>
    <td class="a4-content-urlmap-addr"><code>{$redirect.source|wash}</code></td>
    <td class="a4-content-urlmap-addr"><code>{$redirect.target|wash}</code></td>
    <td class="a4-num">{$redirect.status}</td>
    <td>{if $redirect.siteaccess|ne( '' )}{$redirect.siteaccess|wash}{else}{'Every siteaccess'|i18n( 'design/admin/content/urlimport' )}{/if}</td>
    <td>{$redirect.language|wash}</td>
    <td>{if $redirect.batch_id|gt( 0 )}<a href={concat( 'content/urlimporthistory/', $redirect.batch_id )|ezurl}>{'Import %id'|i18n( 'design/admin/content/urlimport',, hash( '%id', $redirect.batch_id ) )|wash}</a>{/if}</td>
    <td><button type="submit" class="a4-btn a4-btn-small a4-btn-outline-danger" name="RemoveRedirectButton" value="{$redirect.id}" aria-label="{'Remove %address'|i18n( 'design/admin/content/urlimport',, hash( '%address', $redirect.source ) )|wash}" data-a4-confirm="{'Remove this redirect?'|i18n( 'design/admin/content/urlimport' )|wash}">{'Remove'|i18n( 'design/admin/content/urlimport' )}</button></td>
</tr>
{/foreach}
</tbody>
</table>
</div>

<div class="a4-listfoot">
    <div class="a4-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri='content/urlredirects'
             item_count=$redirect_count
             view_parameters=$view_parameters
             item_limit=$redirect_limit}
    </div>
</div>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button type="submit" class="a4-btn a4-btn-outline-danger" name="RemoveSelectedRedirectsButton" value="1" data-a4-confirm="{'Remove the selected redirects?'|i18n( 'design/admin/content/urlimport' )|wash}">{'Remove selected'|i18n( 'design/admin/content/urlimport' )}</button>
    </div>
    <p class="a4-meta">{'A removed redirect stops working at once.'|i18n( 'design/admin/content/urlimport' )}</p>
</div>
</form>
{/if}

</div></div></div>
</div>
