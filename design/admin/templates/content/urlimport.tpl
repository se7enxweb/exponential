{* Import a URL map, the first step (content/urlimport): the file (uploaded or pasted), its format, the siteaccess and
   language of bare paths, what happens to an old address that has an entry already, and the old hosts. Preview keeps
   the file on the server and opens content/urlimport/(plan)/<token> (urlimport_preview.tpl); nothing is written yet.

   Variables (kernel/private/classes/views/content/urlimport.php): import_form, import_formats, import_siteaccesses,
   import_default_siteaccess, import_languages, import_max_size, import_error, import_error_text, import_tables.
   The same file is in design/admin and design/admin4. Works without javascript. *}

<div class="context-block a4-page a4-url a4-content-urlmap">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Import a URL map'|i18n( 'design/admin/content/urlimport' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Bring the addresses of an old site over in one go: a file lists each old address and the page of this site it belongs to now. Exact addresses become redirects or URL aliases, addresses with * become wildcards. You see what each row would do before anything is written, and every import can be undone as a whole.'|i18n( 'design/admin/content/urlimport' )}</p>

{include uri='design:content/urlmap_toolbar.tpl' current='import' all=true()}

{if $import_tables|not}
<div class="a4-notice is-warn" role="alert"><p>{'The tables of URL map imports do not exist yet. Run the upgrade step 6.0.15-url-map (./console exp:upgrade --apply) before importing.'|i18n( 'design/admin/content/urlimport' )}</p></div>
{/if}

{switch match=$import_error}
{case match='error-expired'}<div class="a4-notice is-warn" role="alert"><p>{'This import is no longer available: it expired after a day or belongs to another user. Upload the file again.'|i18n( 'design/admin/content/urlimport' )}</p></div>{/case}
{case match='error-no-file'}<div class="a4-notice is-bad" role="alert"><p>{'Choose a file or paste the rows.'|i18n( 'design/admin/content/urlimport' )}</p></div>{/case}
{case match='error-too-large'}<div class="a4-notice is-bad" role="alert"><p>{'The file is larger than %size.'|i18n( 'design/admin/content/urlimport',, hash( '%size', $import_max_size|si( byte ) ) )|wash}</p></div>{/case}
{case match='error-format'}<div class="a4-notice is-bad" role="alert"><p>{'The file cannot be read: %error'|i18n( 'design/admin/content/urlimport',, hash( '%error', $import_error_text ) )|wash}</p></div>{/case}
{case match='error-siteaccess'}<div class="a4-notice is-bad" role="alert"><p>{'Choose a siteaccess of this installation.'|i18n( 'design/admin/content/urlimport' )}</p></div>{/case}
{case match='error-store'}<div class="a4-notice is-bad" role="alert"><p>{'The import cannot be kept on the server: %error'|i18n( 'design/admin/content/urlimport',, hash( '%error', $import_error_text ) )|wash}</p></div>{/case}
{case}{/case}
{/switch}

<form method="post" action={'content/urlimport'|ezurl} enctype="multipart/form-data">
<div class="a4-grid is-wide a4-section">

<section class="a4-card" aria-labelledby="urlimport-file-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="urlimport-file-title">{'The file'|i18n( 'design/admin/content/urlimport' )}</h2>
</div>
<div class="a4-form">
    <div class="a4-field">
        <label for="urlimport-file">{'File'|i18n( 'design/admin/content/urlimport' )}</label>
        <input type="file" id="urlimport-file" name="UrlMapFile" accept=".csv,.tsv,.txt,.json,.xlsx,.ods,.htaccess,.apache,.apacheconf,.conf,.nginx" aria-describedby="urlimport-file-help" />
        <span class="a4-help" id="urlimport-file-help">{'CSV, JSON, Excel (.xlsx), OpenDocument (.ods), Apache or nginx rewrite rules, at most %size.'|i18n( 'design/admin/content/urlimport',, hash( '%size', $import_max_size|si( byte ) ) )|wash}</span>
    </div>
    <div class="a4-field">
        <label for="urlimport-text">{'Or paste the rows'|i18n( 'design/admin/content/urlimport' )}</label>
        <textarea id="urlimport-text" name="UrlMapText" rows="6" spellcheck="false" aria-describedby="urlimport-text-help">{$import_form.text|wash}</textarea>
        <span class="a4-help" id="urlimport-text-help">{'One row per line, for example /old-page,/new-page. A first line may name the columns: old_url, new_url, kind, siteaccess, language, note, status.'|i18n( 'design/admin/content/urlimport' )}</span>
    </div>
    <div class="a4-field">
        <label for="urlimport-format">{'Format'|i18n( 'design/admin/content/urlimport' )}</label>
        <select id="urlimport-format" name="Format" aria-describedby="urlimport-format-help">
            <option value="">{'Find out from the file'|i18n( 'design/admin/content/urlimport' )}</option>
            {foreach $import_formats as $format}<option value="{$format.name|wash}"{if eq( $format.name, $import_form.format )} selected="selected"{/if}>{$format.label|wash}</option>{/foreach}
        </select>
        <span class="a4-help" id="urlimport-format-help">{'Only needed when neither the name nor the content of the file says.'|i18n( 'design/admin/content/urlimport' )}</span>
    </div>
</div>
</section>

<section class="a4-card" aria-labelledby="urlimport-defaults-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="urlimport-defaults-title">{'Defaults'|i18n( 'design/admin/content/urlimport' )}</h2>
</div>
<div class="a4-form">
    <div class="a4-field">
        <label for="urlimport-siteaccess">{'Siteaccess of bare paths'|i18n( 'design/admin/content/urlimport' )}</label>
        <select id="urlimport-siteaccess" name="SiteAccess" aria-describedby="urlimport-siteaccess-help">
            {foreach $import_siteaccesses as $siteaccess}<option value="{$siteaccess|wash}"{if eq( $siteaccess, $import_form.siteaccess )} selected="selected"{/if}>{$siteaccess|wash}{if eq( $siteaccess, $import_default_siteaccess )} ({'default'|i18n( 'design/admin/content/urlimport' )}){/if}</option>{/foreach}
        </select>
        <span class="a4-help" id="urlimport-siteaccess-help">{'Rows with a full address take the siteaccess of their host instead.'|i18n( 'design/admin/content/urlimport' )}</span>
    </div>
    <div class="a4-field">
        <label for="urlimport-language">{'Language'|i18n( 'design/admin/content/urlimport' )}</label>
        <select id="urlimport-language" name="Language">
            <option value="">{'The language of the siteaccess'|i18n( 'design/admin/content/urlimport' )}</option>
            {foreach $import_languages as $language}<option value="{$language.locale|wash}"{if eq( $language.locale, $import_form.language )} selected="selected"{/if}>{$language.name|wash} ({$language.locale|wash})</option>{/foreach}
        </select>
    </div>
    <fieldset class="a4-field">
        <legend>{'When an old address has an entry already'|i18n( 'design/admin/content/urlimport' )}</legend>
        <label class="a4-check"><input type="radio" name="OnConflict" value="skip"{if ne( $import_form.on_conflict, 'overwrite' )} checked="checked"{/if} /> <span>{'Keep the entry (skip the row)'|i18n( 'design/admin/content/urlimport' )}</span></label>
        <label class="a4-check"><input type="radio" name="OnConflict" value="overwrite"{if eq( $import_form.on_conflict, 'overwrite' )} checked="checked"{/if} /> <span>{'Replace the entry'|i18n( 'design/admin/content/urlimport' )}</span></label>
    </fieldset>
    <div class="a4-field">
        <label for="urlimport-hosts">{'Old hosts'|i18n( 'design/admin/content/urlimport' )}</label>
        <textarea id="urlimport-hosts" name="Hosts" rows="3" spellcheck="false" aria-describedby="urlimport-hosts-help">{$import_form.hosts|wash}</textarea>
        <span class="a4-help" id="urlimport-hosts-help">{'Full addresses of a host this installation does not know are not imported. Name the siteaccess each old host stands for, one per line: www.old-site.com = site'|i18n( 'design/admin/content/urlimport' )}</span>
    </div>
</div>
</section>

</div>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button type="submit" class="a4-btn a4-btn-primary" name="PreviewButton" value="1">{'Preview'|i18n( 'design/admin/content/urlimport' )}</button>
    </div>
    <p class="a4-meta">{'Nothing is written yet: the next page shows what each row would do.'|i18n( 'design/admin/content/urlimport' )}</p>
</div>
</form>

</div></div></div>
</div>
