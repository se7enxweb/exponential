<form action={concat( 'visual/templateedit/', $template )|ezurl} method="post">

{if $original_template}
<input type="hidden" name="RedirectToURI" value="{concat( '/visual/templateview', $original_template )|wash}" />
{else}
<input type="hidden" name="RedirectToURI" value="/visual/templatelist" />
{/if}

<div class="context-block a4-page a4-visual">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Edit <%template_name> [Template]'|i18n( 'design/admin/visual/templateedit',, hash( '%template_name', $template ) )|wash}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="a4-notice is-warn" role="status">
    <p><strong>{'The template cannot be edited.'|i18n( 'design/admin/visual/templateedit' )}</strong> <span>[{currentdate()|l10n( shortdatetime )}]</span></p>
{if $template_exists}
    {if $is_readable}
    <p>{'The web server does not have write access to the requested template.'|i18n( 'design/admin/visual/templateedit' )}</p>
    {else}
    <p>{'The web server does not have read access to the requested template.'|i18n( 'design/admin/visual/templateedit' )}</p>
    {/if}
{else}
    <p>{'The requested template does not exist or is not being used as an override.'|i18n( 'design/admin/visual/templateedit' )}</p>
{/if}
</div>

<div class="a4-table-wrap">
<table class="a4-table">
<tbody>
<tr>
    <th scope="row">{'Requested template'|i18n( 'design/admin/visual/templateedit' )}</th>
    <td><code>{$template|wash}</code></td>
</tr>
<tr>
    <th scope="row">{'Siteaccess'|i18n( 'design/admin/visual/templateedit' )}</th>
    <td>{$site_access|wash}</td>
</tr>
{if $original_template}
<tr>
    <th scope="row">{'Override template'|i18n( 'design/admin/visual/templateedit' )}</th>
    <td><a href={concat( 'visual/templateview', $original_template )|ezurl}>{$original_template|wash}</a></td>
</tr>
{/if}
</tbody>
</table>
</div>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <input class="a4-btn" {if or( not( $template_exists ), not( $is_readable ) )}disabled="disabled" {/if}type="submit" name="OpenReadOnly" value="{'Open as read only'|i18n( 'design/admin/visual/templateedit' )|wash}" />
        <input class="a4-btn" type="submit" name="Cancel" value="{'Cancel'|i18n( 'design/admin/visual/templateedit' )|wash}" />
    </div>
</div>

</div></div></div>

</div>

</form>
