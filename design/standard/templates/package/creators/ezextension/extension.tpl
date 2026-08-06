<div id="package" class="create">
<div id="sid-{$current_step.id|wash}" class="pc-{$creator.id|wash}">

<form enctype="multipart/form-data" method="post" action={'package/create'|ezurl}>

{include uri="design:package/create/error.tpl"}

{include uri="design:package/header.tpl"}

<p>{'Please select the extensions to be exported.'|i18n('design/standard/package')}</p>

<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} width="16" height="16" alt="{'Invert selection.'|i18n( 'design/standard/package' )}" title="{'Toggle all.'|i18n( 'design/standard/package' )}" onclick="ezjs_toggleCheckboxes( document.forms[0], 'PackageExtensionNames[]' ); return false;"/></th>
    <th>{'Name'|i18n( 'design/standard/package' )}</th>
    <th>{'Version'|i18n( 'design/standard/package' )}</th>
    <th>{'Modified'|i18n( 'design/standard/package' )}</th>
    <th>{'Info'|i18n( 'design/standard/package' )}</th>
</tr>
{foreach $extension_list as $extension}
{def $ext = $extension_info[$extension]}
<tr>
    <td><input name="PackageExtensionNames[]" type="checkbox" value="{$extension|wash}" /></td>
    <td><a href="#" class="extension-name-link" data-name="{$extension|wash}">{$extension|wash}</a></td>
    <td>{if $ext.version}{$ext.version|wash}{else}—{/if}</td>
    <td>{if $ext.mtime_formatted}{$ext.mtime_formatted|wash}{else}—{/if}</td>
    <td><a href="#" class="extension-info-link" data-name="{$extension|wash}">{'Details'|i18n( 'design/standard/package' )}</a></td>
</tr>
<tr class="extension-card-row" id="extension-card-{$extension|wash}" style="display:none;">
    <td colspan="5" class="extension-card-cell">
        <div class="extension-card">
            <h3>{$ext.name|wash}</h3>
            {if $ext.meta.description}<p>{$ext.meta.description|wash}</p>{/if}
            <dl>
                {if $ext.version}<dt>{'Version'|i18n( 'design/standard/package' )}</dt><dd>{$ext.version|wash}</dd>{/if}
                {if $ext.mtime_formatted}<dt>{'Modified'|i18n( 'design/standard/package' )}</dt><dd>{$ext.mtime_formatted|wash}</dd>{/if}
                {if $ext.meta.author}<dt>{'Author'|i18n( 'design/standard/package' )}</dt><dd>{$ext.meta.author|wash}</dd>{/if}
                {if $ext.meta.license}<dt>{'License'|i18n( 'design/standard/package' )}</dt><dd>{$ext.meta.license|wash}</dd>{/if}
                {if $ext.meta.info_url}<dt>{'Info URL'|i18n( 'design/standard/package' )}</dt><dd><a href="{$ext.meta.info_url|wash}" target="_blank">{$ext.meta.info_url|wash}</a></dd>{/if}
            </dl>
            <a href="#" class="extension-card-close">{'Close'|i18n( 'design/standard/package' )}</a>
        </div>
    </td>
</tr>
{/foreach}
</table>

{include uri="design:package/navigator.tpl"}

</form>

{literal}
<script type="text/javascript">
$(document).ready(function() {
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

</div>
</div>

