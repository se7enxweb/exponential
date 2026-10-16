{* The extension step of the extension export wizard (package/create, creator ezextension): the extensions the package
   carries, with their version, last change and details. Exporting is switched off by default (package.ini
   [ExtensionExportSettings]): an extension that may not be exported cannot be chosen, says why, and the step says how
   to switch exporting on. The POST name is that of before: PackageExtensionNames[], read by
   eZExtensionPackageCreator, which refuses a forbidden extension even when it is posted. The same file is in
   design/admin and design/admin4. Guide: doc/features/6.0/package-wizard.md *}
{def $k_list = 'Extension list'|i18n( 'kernel/package' )
     $chosen = first_set( $persistent_data.extensionlist, array() )
     $choosable = 0
     $errs = hash()}
{foreach $error_list as $e}{set $errs = $errs|merge( hash( $e.field, $e.description ) )}{/foreach}
{foreach $extension_list as $extension}{if first_set( $export_refusals[$extension], "" )|not}{set $choosable = $choosable|inc}{/if}{/foreach}
<div id="package" class="create">
<div id="sid-{$current_step.id|wash}" class="pc-{$creator.id|wash}">
<form enctype="multipart/form-data" method="post" action={'package/create'|ezurl} class="a4-page a4-standalone a4-package-wizard">
<div class="context-block">
{include uri="design:package/create/wizard_head.tpl"}
<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri="design:package/create/wizard_progress.tpl"
         intro='Please select the extensions to be exported.'|i18n( 'design/standard/package' )
         field_ids=hash( $k_list, 'a4-package-extensions' )}

{* Exporting extensions is switched off by default: how to switch it on *}
{if and( is_set( $export_notice ), $export_notice )}
<div class="a4-notice is-warn" role="status" id="a4-package-export-notice"><p><strong>{$export_notice|wash}</strong></p></div>
{/if}

<section class="a4-card is-flush a4-package-pick" id="a4-package-extensions" tabindex="-1" aria-labelledby="a4-package-extensions-title" aria-describedby="{if is_set( $errs[$k_list] )}a4-package-extensions-error {/if}a4-package-extensions-count">
    <div class="a4-card-head">
        <h3 class="a4-h3" id="a4-package-extensions-title">{'Extensions'|i18n( 'design/admin/package' )}</h3>
        <p class="a4-package-pick-count" id="a4-package-extensions-count" aria-live="polite" data-template="{'%count of %total selected'|i18n( 'design/admin/package' )|wash}">{'%count of %total selected'|i18n( 'design/admin/package',, hash( '%count', $chosen|count, '%total', $choosable ) )|wash}</p>
    </div>
    {if is_set( $errs[$k_list] )}<p class="a4-field-error a4-package-pick-error" id="a4-package-extensions-error">{$errs[$k_list]|wash}</p>{/if}
    {if $choosable|eq( 0 )}<details class="a4-fold a4-package-ext-all"><summary>{'Show the %count extensions of this site'|i18n( 'design/admin/package',, hash( '%count', $extension_list|count ) )|wash}</summary>{/if}
    <div class="a4-filterbar a4-package-pick-filter" hidden>
        <div class="a4-field">
            <label for="a4-package-extensions-filter">{'Filter by name'|i18n( 'design/admin/package' )}</label>
            <input type="search" id="a4-package-extensions-filter" autocomplete="off" spellcheck="false" />
        </div>
    </div>
    <div class="a4-table-wrap">
    <table class="a4-table a4-package-pick-table">
        <thead>
        <tr>
            <th scope="col" class="a4-package-pick-check"><span class="a4-sr-only">{'Include'|i18n( 'design/admin/package' )}</span></th>
            <th scope="col">{'Name'|i18n( 'design/standard/package' )}</th>
            <th scope="col">{'Version'|i18n( 'design/standard/package' )}</th>
            <th scope="col" class="a4-package-pick-groups">{'Modified'|i18n( 'design/standard/package' )}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $extension_list as $extension}
        {def $ext = $extension_info[$extension]
             $refusal = ''}
        {if and( is_set( $export_refusals ), is_set( $export_refusals[$extension] ) )}{set $refusal = $export_refusals[$extension]}{/if}
        <tr data-filter="{$extension|downcase|wash}"{if $refusal} class="is-disabled"{/if}>
            <td class="a4-package-pick-check"><input name="PackageExtensionNames[]" type="checkbox" id="a4-package-ext-{$extension|wash}" value="{$extension|wash}"{if $refusal} disabled="disabled"{elseif $chosen|contains( $extension )} checked="checked"{/if}{if $refusal} aria-describedby="{if $refusal|eq( first_set( $export_notice, "" ) )}a4-package-export-notice{else}a4-package-ext-{$extension|wash}-refusal{/if}"{/if} /></td>
            <td>
                <label class="a4-inline-label" for="a4-package-ext-{$extension|wash}"><code>{$extension|wash}</code></label>
                {if and( $refusal, $refusal|ne( first_set( $export_notice, "" ) ) )}<span class="a4-meta a4-package-refusal" id="a4-package-ext-{$extension|wash}-refusal">{$refusal|wash}</span>{/if}
                {if or( $ext.meta.description, $ext.meta.author, $ext.meta.license, $ext.meta.info_url )}
                <details class="a4-package-ext-details">
                    <summary>{'Details'|i18n( 'design/standard/package' )}<span class="a4-sr-only"> {$extension|wash}</span></summary>
                    {if $ext.meta.description}<p>{$ext.meta.description|wash}</p>{/if}
                    <dl class="a4-facts is-rows">
                        {if $ext.meta.author}<dt>{'Author'|i18n( 'design/standard/package' )}</dt><dd>{$ext.meta.author|wash}</dd>{/if}
                        {if $ext.meta.license}<dt>{'License'|i18n( 'design/standard/package' )}</dt><dd>{$ext.meta.license|wash}</dd>{/if}
                        {if $ext.meta.info_url}<dt>{'Info URL'|i18n( 'design/standard/package' )}</dt><dd><a href="{$ext.meta.info_url|wash}" target="_blank" rel="noopener noreferrer">{$ext.meta.info_url|wash}</a></dd>{/if}
                    </dl>
                </details>
                {/if}
            </td>
            <td>{if $ext.version}{$ext.version|wash}{else}&ndash;{/if}</td>
            <td class="a4-package-pick-groups">{if $ext.mtime_formatted}{$ext.mtime_formatted|wash}{else}&ndash;{/if}</td>
        </tr>
        {undef $ext $refusal}
        {/foreach}
        </tbody>
    </table>
    </div>
    <p class="a4-empty a4-package-pick-none" hidden>{'No extension matches the filter.'|i18n( 'design/admin/package' )}</p>
    {if $choosable|eq( 0 )}</details>{/if}
</section>

{include uri="design:package/create/wizard_actions.tpl"}
</div></div></div>
</div>
</form>
</div>
</div>
{include uri="design:package/create/wizard_pick_script.tpl"}
{undef $k_list $chosen $choosable $errs}
