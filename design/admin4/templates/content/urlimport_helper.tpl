{* The helpers of an import of a URL map (content/urlimport/(source)/sitemap|404|suggest, since 6.0.15): a map made
   from the old sitemap, from this site's 404 log, or from a map whose rows lack targets; the targets suggested by
   how alike the old address is to this site's pages; downloaded as CSV to complete, or planned as an import to
   preview (urlimport_preview.tpl). Nothing is written to the site here.

   Variables (kernel/private/classes/views/content/urlimport.php): helper_source, helper_form, helper_max_rows,
   helper_max_size, helper_log_enabled, helper_siteaccesses, helper_default_siteaccess, helper_error,
   helper_error_text. The same file is in design/admin and design/admin4. Works without javascript. *}

<div class="context-block a4-page a4-url a4-content-urlmap">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Import a URL map'|i18n( 'design/admin/content/urlimport' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{include uri='design:content/urlmap_toolbar.tpl' current='import' all=true()}
{include uri='design:content/urlimport_sources.tpl' current=$helper_source}

{switch match=$helper_error}
{case match='error-no-sitemap'}<div class="a4-notice is-bad" role="alert"><p>{'Give the address of the sitemap or upload it.'|i18n( 'design/admin/content/urlimport' )}</p></div>{/case}
{case match='error-no-file'}<div class="a4-notice is-bad" role="alert"><p>{'Choose a file or paste the rows.'|i18n( 'design/admin/content/urlimport' )}</p></div>{/case}
{case match='error-too-large'}<div class="a4-notice is-bad" role="alert"><p>{'The file is larger than %size.'|i18n( 'design/admin/content/urlimport',, hash( '%size', $helper_max_size|si( byte ) ) )|wash}</p></div>{/case}
{case match='error-read'}<div class="a4-notice is-bad" role="alert"><p>{'The file cannot be read: %error'|i18n( 'design/admin/content/urlimport',, hash( '%error', $helper_error_text ) )|wash}</p></div>{/case}
{case match='error-empty'}<div class="a4-notice is-warn" role="alert"><p>{'No old address was found.'|i18n( 'design/admin/content/urlimport' )}</p></div>{/case}
{case match='error-store'}<div class="a4-notice is-bad" role="alert"><p>{'The import cannot be kept on the server: %error'|i18n( 'design/admin/content/urlimport',, hash( '%error', $helper_error_text ) )|wash}</p></div>{/case}
{case}{/case}
{/switch}

<form method="post" action={concat( 'content/urlimport/(source)/', $helper_source )|ezurl} enctype="multipart/form-data">
<div class="a4-grid is-wide a4-section">

<section class="a4-card" aria-labelledby="urlhelper-source-title">
<div class="a4-card-head">
    <h2 class="a4-h2" id="urlhelper-source-title">{switch match=$helper_source}{case match='sitemap'}{'From the old sitemap'|i18n( 'design/admin/content/urlimport' )}{/case}{case match='404'}{'From the 404 log'|i18n( 'design/admin/content/urlimport' )}{/case}{case}{'Suggest targets'|i18n( 'design/admin/content/urlimport' )}{/case}{/switch}</h2>
</div>
<div class="a4-form a4-card-body">
{switch match=$helper_source}
{case match='sitemap'}
    <p class="a4-help">{'The addresses of the old site from its sitemap (sitemap.xml, a sitemap index or .gz) or from a list with one address per line. Each becomes a row with an empty new address.'|i18n( 'design/admin/content/urlimport' )}</p>
    <div class="a4-field">
        <label for="urlhelper-address">{'Address of the sitemap'|i18n( 'design/admin/content/urlimport' )}</label>
        <input type="url" id="urlhelper-address" name="Address" value="{$helper_form.address|wash}" placeholder="https://www.old-site.com/sitemap.xml" spellcheck="false" />
    </div>
    <div class="a4-field">
        <label for="urlhelper-sitemap-file">{'Or upload the sitemap or the list'|i18n( 'design/admin/content/urlimport' )}</label>
        <input type="file" id="urlhelper-sitemap-file" name="SitemapFile" accept=".xml,.gz,.txt,.csv" />
    </div>
    <label class="a4-check"><input type="checkbox" name="AnyHost" value="1"{if $helper_form.any_host} checked="checked"{/if} /> <span>{'Also the addresses of other hosts'|i18n( 'design/admin/content/urlimport' )}</span></label>
{/case}
{case match='404'}
    <p class="a4-help">{'The addresses visitors asked for on this site and did not find, the most asked first. Images, scripts and bots are left out.'|i18n( 'design/admin/content/urlimport' )}</p>
    {if $helper_log_enabled|not}<div class="a4-notice is-warn" role="status"><p>{'The 404 log is switched off (site.ini [RequestLogSettings] NotFoundLog): it has no new addresses.'|i18n( 'design/admin/content/urlimport' )}</p></div>{/if}
    <div class="a4-field">
        <label for="urlhelper-min-hits">{'At least this many requests'|i18n( 'design/admin/content/urlimport' )}</label>
        <input type="number" id="urlhelper-min-hits" name="MinHits" value="{$helper_form.min_hits|wash}" min="1" />
    </div>
    <div class="a4-field">
        <label for="urlhelper-days">{'In the last days'|i18n( 'design/admin/content/urlimport' )}</label>
        <input type="number" id="urlhelper-days" name="Days" value="{$helper_form.days|wash}" min="1" max="3650" />
    </div>
{/case}
{case}
    <p class="a4-help">{'A map whose rows lack a new address: each gets the pages of this site whose address or name is most like the old one.'|i18n( 'design/admin/content/urlimport' )}</p>
    <div class="a4-field">
        <label for="urlhelper-map-file">{'File'|i18n( 'design/admin/content/urlimport' )}</label>
        <input type="file" id="urlhelper-map-file" name="UrlMapFile" accept=".csv,.tsv,.txt,.json,.xlsx,.ods,.htaccess,.apache,.apacheconf,.conf,.nginx" aria-describedby="urlhelper-map-file-help" />
        <span class="a4-help" id="urlhelper-map-file-help">{'CSV, JSON, Excel (.xlsx), OpenDocument (.ods), Apache or nginx rewrite rules, at most %size.'|i18n( 'design/admin/content/urlimport',, hash( '%size', $helper_max_size|si( byte ) ) )|wash}</span>
    </div>
{/case}
{/switch}
{if ne( $helper_source, 'suggest' )}
    <div class="a4-field">
        <label for="urlhelper-limit">{'At most this many rows'|i18n( 'design/admin/content/urlimport' )}</label>
        <input type="number" id="urlhelper-limit" name="Limit" value="{$helper_form.limit|wash}" min="1" max="{$helper_max_rows}" />
    </div>
{/if}
</div>
</section>

<section class="a4-card" aria-labelledby="urlhelper-targets-title">
<div class="a4-card-head">
    <h2 class="a4-h2" id="urlhelper-targets-title">{'New addresses'|i18n( 'design/admin/content/urlimport' )}</h2>
</div>
<div class="a4-form a4-card-body">
    {if ne( $helper_source, 'suggest' )}
    <label class="a4-check"><input type="checkbox" name="Suggest" value="1"{if $helper_form.suggest} checked="checked"{/if} /> <span>{'Suggest targets'|i18n( 'design/admin/content/urlimport' )}</span></label>
    {/if}
    <div class="a4-field">
        <label for="urlhelper-fill">{'Fill in a suggestion that scores at least'|i18n( 'design/admin/content/urlimport' )}</label>
        <input type="number" id="urlhelper-fill" name="FillAbove" value="{$helper_form.fill_above|wash}" min="0" max="100" aria-describedby="urlhelper-fill-help" />
        <span class="a4-help" id="urlhelper-fill-help">{'A score from 0 to 100 says how alike the old address is to a page of this site. The other suggestions are listed in the notes of the row.'|i18n( 'design/admin/content/urlimport' )}</span>
    </div>
    <div class="a4-field">
        <label for="urlhelper-siteaccess">{'Siteaccess of bare paths'|i18n( 'design/admin/content/urlimport' )}</label>
        <select id="urlhelper-siteaccess" name="SiteAccess">
            {foreach $helper_siteaccesses as $siteaccess}<option value="{$siteaccess|wash}"{if eq( $siteaccess, $helper_form.siteaccess )} selected="selected"{/if}>{$siteaccess|wash}{if eq( $siteaccess, $helper_default_siteaccess )} ({'default'|i18n( 'design/admin/content/urlimport' )}){/if}</option>{/foreach}
        </select>
    </div>
</div>
</section>

</div>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button type="submit" class="a4-btn a4-btn-primary" name="PreviewMapButton" value="1">{'Preview'|i18n( 'design/admin/content/urlimport' )}</button>
        <button type="submit" class="a4-btn" name="DownloadMapButton" value="1">{'Download as CSV'|i18n( 'design/admin/content/urlimport' )}</button>
    </div>
    <p class="a4-meta">{'Nothing is written yet. Download the map to complete it in a spreadsheet, or preview it: rows without a new address are shown as not importable.'|i18n( 'design/admin/content/urlimport' )}</p>
</div>
</form>

</div></div></div>
</div>
