<form method="post" action={'visual/toolbarlist'|ezurl}>

<div class="context-block a4-page a4-visual">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'Toolbar management'|i18n( 'design/admin/visual/toolbar' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="context-attributes">

<label>{'Siteaccess'|i18n( 'design/admin/visual/toolbar' )}:</label>
    {if $current_siteaccess}
{*        <p>{'Current siteaccess'|i18n( 'design/admin/visual/toolbar' )}: <strong>{$current_siteaccess}</strong></p> *}
    {/if}
{*        <label>{'Select siteaccess'|i18n( 'design/admin/visual/toolbar' )}:</label> *}

        <select class="a4-input is-auto" name="CurrentSiteAccess" aria-label="{'Select siteaccess'|i18n( 'design/admin/visual/toolbar' )|wash}">
            {section var=siteaccess loop=$siteaccess_list}
                {if eq( $current_siteaccess, $siteaccess )}
                    <option value="{$siteaccess}" selected="selected">{$siteaccess}</option>
                {else}
                <option value="{$siteaccess}">{$siteaccess}</option>
            {/if}
        {/section}
        </select>

</div>

<div class="a4-toolbar is-bottom">
<div class="a4-actions">
        <input class="a4-btn" type="submit" name="SelectCurrentSiteAccessButton" value="{'Set'|i18n( 'design/admin/visual/toolbar' )}" />
</div>
</div>

{* DESIGN: Content END *}</div></div></div>

</div>


<div class="context-block a4-page a4-visual">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h2 class="context-title">{'Available toolbars for the <%siteaccess> siteaccess'|i18n( 'design/admin/visual/toolbar',, hash( '%siteaccess', $current_siteaccess ) )|wash}</h2>



{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="a4-table-wrap">
<table class="a4-table">
{section var=toolbar loop=$toolbar_list}
<tr>
    <td>
    <a href={concat( 'visual/toolbar/', $current_siteaccess, '/', $toolbar )|ezurl}>{$toolbar}</a>
    </td>
</tr>
{/section}
</table>
</div>

{* DESIGN: Content END *}</div></div></div>

</div>

</form>
