{if and( is_set( $warning_messages), $warning_messages|count|ge(1) )}
<div class="message-warning">
    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Problems detected during autoload generation:'|i18n( 'design/admin/setup/extensions' )}</h2>
    <ul>
    {foreach $warning_messages as $warning}
        <li><p>{$warning|break()}</p></li>
    {/foreach}
    </ul>
</div>
{/if}

<form name="extensionform" method="post" action={'/setup/extensions'|ezurl}>

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header">

<h1 class="context-title">{'Available extensions (%extension_count)'|i18n( 'design/admin/setup/extensions',, hash( '%extension_count', $available_extension_array|count ) )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div>

{* DESIGN: Content START *}<div class="box-content">

{section show=$available_extension_array}
<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} width="16" height="16" alt="{'Invert selection.'|i18n( 'design/admin/setup/extensions' )}" title="{'Toggle all.'|i18n( 'design/admin/content/translations' )}" onclick="ezjs_toggleCheckboxes( document.extensionform, 'ActiveExtensionList[]' ); return false;"/></th>
    <th><a href={concat( '/setup/extensions?SortBy=name&amp;SortOrder=', cond( and( $sort_by|eq('name'), $sort_order|eq('asc') ), 'desc', 'asc' ) )|ezurl} class="sortable-header">{'Name'|i18n( 'design/admin/setup/extensions' )}</a></th>
    <th>{'Extension'|i18n( 'design/admin/setup/extensions' )}</th>
    <th>{'License'|i18n( 'design/admin/setup/extensions' )}</th>
    <th><a href={concat( '/setup/extensions?SortBy=version&amp;SortOrder=', cond( and( $sort_by|eq('version'), $sort_order|eq('asc') ), 'desc', 'asc' ) )|ezurl} class="sortable-header">{'Version'|i18n( 'design/admin/setup/extensions' )}</a></th>
    <th><a href={concat( '/setup/extensions?SortBy=mtime&amp;SortOrder=', cond( and( $sort_by|eq('mtime'), $sort_order|eq('asc') ), 'desc', 'asc' ) )|ezurl} class="sortable-header">{'Modified'|i18n( 'design/admin/setup/extensions' )}</a></th>
    <th>{'Info'|i18n( 'design/admin/setup/extensions' )}</th>
</tr>
{section var=Extensions loop=$available_extension_array sequence=array( bglight, bgdark )}
{def $ext = $extension_info[$Extensions.item]}
<tr class="{$Extensions.sequence}">
    {* Status. *}
    <td><input type="checkbox" name="ActiveExtensionList[]" value="{$Extensions.item}" {if $selected_extension_array|contains($Extensions.item)}checked="checked"{/if} title="{'Activate or deactivate extension. Use the "Update" button to apply the changes.'|i18n( 'design/admin/setup/extensions' )|wash}" /></td>
    {* Name (folder). *}
    <td><a href="#" class="extension-name-link" data-name="{$Extensions.item|wash}">{$Extensions.item|wash}</a></td>
    {* Full extension name. *}
    <td>{if $ext.name}{$ext.name|wash}{else}—{/if}</td>
    {* License. *}
    <td>{if $ext.license}{$ext.license|wash}{else}—{/if}</td>
    {* Version. *}
    <td>{if $ext.version}{$ext.version|wash}{else}—{/if}</td>
    {* Modified. *}
    <td>{if $ext.mtime_formatted}{$ext.mtime_formatted|wash}{else}—{/if}</td>
    {* Info popin trigger. *}
    <td><a href="#" class="extension-info-link" data-name="{$Extensions.item|wash}">{'Details'|i18n( 'design/admin/setup/extensions' )}</a></td>
</tr>
<tr class="{$Extensions.sequence} extension-card-row" id="extension-card-{$Extensions.item|wash}" style="display:none;">
    <td colspan="7" class="extension-card-cell">
        <div class="extension-card">
            <h3>{$ext.name|wash} <span class="extension-folder">({$Extensions.item|wash})</span></h3>
            {if $ext.meta.description}<p>{$ext.meta.description|wash}</p>{/if}
            <dl>
                {if $ext.version}<dt>{'Version'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$ext.version|wash}</dd>{/if}
                {if $ext.mtime_formatted}<dt>{'Modified'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$ext.mtime_formatted|wash}</dd>{/if}
                {if $ext.meta.copyright}<dt>{'Copyright'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$ext.meta.copyright|wash}</dd>{/if}
                {if $ext.meta.author}<dt>{'Author'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$ext.meta.author|wash}</dd>{/if}
                {if $ext.meta.license}<dt>{'License'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$ext.meta.license|wash}</dd>{/if}
                {if $ext.meta.info_url}<dt>{'Info URL'|i18n( 'design/admin/setup/extensions' )}</dt><dd><a href="{$ext.meta.info_url|wash}" target="_blank">{$ext.meta.info_url|wash}</a></dd>{/if}
            </dl>
            <div class="extension-downloads">
                <strong>{'Download'|i18n( 'design/admin/setup/extensions' )}</strong>
                <a href={concat( '/setup/extensions/', $Extensions.item, '/tar.gz' )|ezurl}>tar.gz</a>
                <a href={concat( '/setup/extensions/', $Extensions.item, '/zip' )|ezurl}>.zip</a>
                <a href={concat( '/setup/extensions/', $Extensions.item, '/tar.bz2' )|ezurl}>tar.bz2</a>
                <a href={concat( '/setup/extensions/', $Extensions.item, '/ezpkg' )|ezurl}>.ezpkg</a>
            </div>
            <a href="#" class="extension-card-close">{'Close'|i18n( 'design/admin/setup/extensions' )}</a>
        </div>
    </td>
</tr>
{/section}
</table>
{section-else}
<div class="block">
    <p>{'There are no available extensions.'|i18n( 'design/admin/setup/extensions' )}</p>
</div>
{/section}

{* DESIGN: Content END *}</div>

<div class='block'>
<div class="controlbar">
{* DESIGN: Control bar START *}
<div class="block">
{if $available_extension_array}
    <input class="button" type="submit" name="ActivateExtensionsButton" value="{'Update'|i18n( 'design/admin/setup/extensions' )}" title="{'Click this button to store changes if you have modified the status of the checkboxes above.'|i18n( 'design/admin/setup/extensions' )}" />
{else}
    <input class="button-disabled" type="submit" name="ActivateExtensionsButton" value="{'Update'|i18n( 'design/admin/setup/extensions' )}" disabled="disabled" />
{/if}
    <input class="button" type="submit" name="GenerateAutoloadArraysButton" value="{'Regenerate autoload arrays for extensions'|i18n( 'design/admin/setup/extensions' )}" title="{'Click this button to regenerate the autoload arrays used by the system for extensions.'|i18n( 'design/admin/setup/extensions' )}" />
</div>
{* DESIGN: Control bar END *}
</div>
</div>

</div>

</form>

{* Highlight "Update" button on changes *}
{literal}
<script type="text/javascript">
$(document).ready(function() {
    var initialExtensionSettings = {};
    var extensionChecks = jQuery('[name=extensionform] :checkbox');

    function styleUpdateButton() {
        var b = jQuery('[name=ActivateExtensionsButton]:first');
        jQuery(extensionChecks).each( function(){
            if (initialExtensionSettings[this.value] !== this.checked) {
                b.removeClass('button').addClass('defaultbutton');
                return false;
            } else {
                b.removeClass('defaultbutton').addClass('button');
            }
        });
    }

    jQuery(extensionChecks).each( function(){
        initialExtensionSettings[this.value] = this.checked;
    }).change(function(){styleUpdateButton();});

    // Extension info card popin
    function toggleExtensionCard( name ) {
        var row = jQuery( '#extension-card-' + name );
        var wasVisible = row.is(':visible');
        jQuery( '.extension-card-row' ).hide();
        if ( !wasVisible ) {
            row.show();
        }
    }

    jQuery( '.extension-name-link, .extension-info-link' ).on( 'click', function( e ) {
        e.preventDefault();
        toggleExtensionCard( jQuery(this).data('name') );
    });

    jQuery( '.extension-card-close' ).on( 'click', function( e ) {
        e.preventDefault();
        jQuery(this).closest( '.extension-card-row' ).hide();
    });
});
</script>
{/literal}
