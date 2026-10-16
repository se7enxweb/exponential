{* The options step of the content object export wizard (package/create, creator ezcontentobject): what comes with
   the chosen content (class definitions, templates of which siteaccesses), which versions and languages, and how
   node assignments and related objects are kept. The defaults suit most packages. The POST names are those of
   before: IncludeClasses, IncludeTemplates, SiteAccesses[], VersionExport, Languages[], NodeAssignment,
   RelatedObjects, read by eZContentObjectPackageCreator; siteaccesses and languages are checkboxes now, which post
   the same lists the multiple selects posted. The same file is in design/admin and design/admin4.
   Guide: doc/features/6.0/package-wizard.md *}
{def $k_nodes = 'Selected nodes'|i18n( 'kernel/package' )}
<div id="package" class="create">
<div id="sid-{$current_step.id|wash}" class="pc-{$creator.id|wash}">
<form method="post" action={'package/create'|ezurl} class="a4-page a4-standalone a4-package-wizard">
<div class="context-block">
{include uri="design:package/create/wizard_head.tpl"}
<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri="design:package/create/wizard_progress.tpl"
         intro='Specify export properties. The default settings will most likely be suitable for your needs.'|i18n( 'design/admin/package' )
         field_ids=hash( $k_nodes, 'pkg-languages' )}

<div class="a4-package-options">
<section class="a4-card" aria-labelledby="pkg-misc">
    <div class="a4-card-head"><h3 class="a4-h3" id="pkg-misc">{'What comes with the content'|i18n( 'design/admin/package' )}</h3></div>
    <div class="a4-stack">
        <label class="a4-check"><input type="checkbox" name="IncludeClasses" value="1"{if $options.include_classes} checked="checked"{/if} /><span>{'Include class definitions.'|i18n( 'design/admin/package' )}<span class="a4-help">{'Needed when the site the package goes to does not have these classes yet.'|i18n( 'design/admin/package' )}</span></span></label>
        <label class="a4-check"><input type="checkbox" name="IncludeTemplates" value="1"{if $options.include_templates} checked="checked"{/if} /><span>{'Include templates related to exported objects.'|i18n( 'design/admin/package' )}</span></label>
    </div>
    <fieldset class="a4-field a4-package-choices" id="pkg-siteaccesses">
        <legend>{'Select templates from the following siteaccesses'|i18n( 'design/admin/package' )}</legend>
        <ul class="a4-chips">
        {foreach $available_site_accesses as $site_access}
            <li><label class="a4-chip"><input type="checkbox" name="SiteAccesses[]" value="{$site_access|wash}"{if $options.site_access_array|contains( $site_access )} checked="checked"{/if} /><span>{$site_access|wash}</span></label></li>
        {/foreach}
        </ul>
    </fieldset>
</section>

<section class="a4-card" aria-labelledby="pkg-versions-title">
    <div class="a4-card-head"><h3 class="a4-h3" id="pkg-versions-title">{'Versions and languages'|i18n( 'design/admin/package' )}</h3></div>
    <fieldset class="a4-field">
        <legend>{'Versions'|i18n( 'design/admin/package' )}</legend>
        <ul class="a4-radios">
            <li><label><input type="radio" name="VersionExport" value="current"{if $options.versions|eq( 'current' )} checked="checked"{/if} /><span>{'Published version'|i18n( 'design/admin/package' )}</span></label></li>
            <li><label><input type="radio" name="VersionExport" value="all"{if $options.versions|eq( 'all' )} checked="checked"{/if} /><span>{'All versions'|i18n( 'design/admin/package' )}</span></label></li>
        </ul>
    </fieldset>
    <fieldset class="a4-field a4-package-choices" id="pkg-languages" tabindex="-1">
        <legend>{'Select languages to export'|i18n( 'design/admin/package' )}</legend>
        <ul class="a4-chips">
        {foreach $available_languages as $language}
            <li><label class="a4-chip"><input type="checkbox" name="Languages[]" value="{$language.locale|wash}"{if $options.language_array|contains( $language.locale )} checked="checked"{/if} /><span>{$language.name|wash}</span></label></li>
        {/foreach}
        </ul>
    </fieldset>
</section>

<section class="a4-card" aria-labelledby="pkg-placement-title">
    <div class="a4-card-head"><h3 class="a4-h3" id="pkg-placement-title">{'Placement'|i18n( 'design/admin/package' )}</h3></div>
    <fieldset class="a4-field">
        <legend>{'Node assignments'|i18n( 'design/admin/package' )}</legend>
        <ul class="a4-radios">
            <li><label><input type="radio" name="NodeAssignment" value="selected"{if $options.node_assignment|eq( 'selected' )} checked="checked"{/if} /><span>{'Keep all in selected nodes'|i18n( 'design/admin/package' )}</span></label></li>
            <li><label><input type="radio" name="NodeAssignment" value="main"{if $options.node_assignment|eq( 'main' )} checked="checked"{/if} /><span>{'Main only'|i18n( 'design/admin/package' )}</span></label></li>
        </ul>
    </fieldset>
    <fieldset class="a4-field">
        <legend>{'Related objects'|i18n( 'design/admin/package' )}</legend>
        <ul class="a4-radios">
            <li><label><input type="radio" name="RelatedObjects" value="selected"{if $options.related_objects|eq( 'selected' )} checked="checked"{/if} /><span>{'Keep all in selected nodes'|i18n( 'design/admin/package' )}</span></label></li>
            <li><label><input type="radio" name="RelatedObjects" value="none"{if $options.related_objects|eq( 'none' )} checked="checked"{/if} /><span>{'None'|i18n( 'design/admin/package' )}</span></label></li>
        </ul>
    </fieldset>
</section>
</div>

{include uri="design:package/create/wizard_actions.tpl"}
</div></div></div>
</div>
</form>
</div>
</div>
{undef $k_nodes}
