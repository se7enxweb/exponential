{* Export the URL map (content/urlexport): the format, which entries (the global URL aliases of the URL translator,
   optionally the custom aliases of content, the wildcards, the redirects) and the siteaccess and language of the
   redirects and aliases. Export answers the posted form with the file; nothing is read from the query string.

   Variables (kernel/private/classes/views/content/urlexport.php): export_form, export_formats, export_siteaccesses,
   export_languages (locale => name), export_error. The same file is in design/admin and design/admin4. *}

<div class="context-block a4-page a4-url a4-content-urlmap">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Export the URL map'|i18n( 'design/admin/content/urlimport' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Download the global URL aliases, the wildcards and the redirects of this site as one file: to keep, to edit in a spreadsheet and import again, or to give to a web server as rewrite rules.'|i18n( 'design/admin/content/urlimport' )}</p>

{include uri='design:content/urlmap_toolbar.tpl' current='export' all=true()}

{switch match=$export_error}
{case match='error-no-kind'}<div class="a4-notice is-bad" role="alert"><p>{'Choose at least one kind of entry.'|i18n( 'design/admin/content/urlimport' )}</p></div>{/case}
{case match='error-write'}<div class="a4-notice is-bad" role="alert"><p>{'The file could not be written.'|i18n( 'design/admin/content/urlimport' )}</p></div>{/case}
{case}{/case}
{/switch}

<form method="post" action={'content/urlexport'|ezurl}>
<section class="a4-card a4-section" aria-labelledby="urlexport-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="urlexport-title">{'What to export'|i18n( 'design/admin/content/urlimport' )}</h2>
</div>
<div class="a4-form">
    <div class="a4-field">
        <label for="urlexport-format">{'Format'|i18n( 'design/admin/content/urlimport' )}</label>
        <select id="urlexport-format" name="Format">
            {foreach $export_formats as $format}<option value="{$format.name|wash}"{if eq( $format.name, $export_form.format )} selected="selected"{/if}>{$format.label|wash}</option>{/foreach}
        </select>
    </div>
    <fieldset class="a4-field">
        <legend>{'Entries'|i18n( 'design/admin/content/urlimport' )}</legend>
        <label class="a4-check"><input type="checkbox" name="Kinds[]" value="alias"{if $export_form.kinds|contains( 'alias' )} checked="checked"{/if} /> <span>{'Global URL aliases (the URL translator)'|i18n( 'design/admin/content/urlimport' )}</span></label>
        <label class="a4-check"><input type="checkbox" name="IncludeNodeAliases" value="1"{if $export_form.include_node_aliases} checked="checked"{/if} /> <span>{'Also the custom URL aliases of content'|i18n( 'design/admin/content/urlimport' )}<span class="a4-help">{'Written with the node as target, for example node:42.'|i18n( 'design/admin/content/urlimport' )}</span></span></label>
        <label class="a4-check"><input type="checkbox" name="Kinds[]" value="wildcard"{if $export_form.kinds|contains( 'wildcard' )} checked="checked"{/if} /> <span>{'Wildcards'|i18n( 'design/admin/content/urlimport' )}</span></label>
        <label class="a4-check"><input type="checkbox" name="Kinds[]" value="redirect"{if $export_form.kinds|contains( 'redirect' )} checked="checked"{/if} /> <span>{'Redirects'|i18n( 'design/admin/content/urlimport' )}</span></label>
    </fieldset>
    <div class="a4-field">
        <label for="urlexport-siteaccess">{'Siteaccess'|i18n( 'design/admin/content/urlimport' )}</label>
        <select id="urlexport-siteaccess" name="SiteAccess" aria-describedby="urlexport-siteaccess-help">
            <option value="">{'Every siteaccess'|i18n( 'design/admin/content/urlimport' )}</option>
            {foreach $export_siteaccesses as $siteaccess}<option value="{$siteaccess|wash}"{if eq( $siteaccess, $export_form.siteaccess )} selected="selected"{/if}>{$siteaccess|wash}</option>{/foreach}
        </select>
        <span class="a4-help" id="urlexport-siteaccess-help">{'Only redirects belong to a siteaccess; URL aliases and wildcards are exported for every siteaccess.'|i18n( 'design/admin/content/urlimport' )}</span>
    </div>
    <div class="a4-field">
        <label for="urlexport-language">{'Language'|i18n( 'design/admin/content/urlimport' )}</label>
        <select id="urlexport-language" name="Language">
            <option value="">{'Every language'|i18n( 'design/admin/content/urlimport' )}</option>
            {foreach $export_languages as $locale => $name}<option value="{$locale|wash}"{if eq( $locale, $export_form.language )} selected="selected"{/if}>{$name|wash} ({$locale|wash})</option>{/foreach}
        </select>
    </div>
    <div class="a4-actions">
        <button type="submit" class="a4-btn a4-btn-primary" name="ExportButton" value="1">{'Export'|i18n( 'design/admin/content/urlimport' )}</button>
    </div>
</div>
</section>
</form>

</div></div></div>
</div>
