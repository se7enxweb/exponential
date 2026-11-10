{* The formats of a URL map and an example file of each (content/urlimport/(source)/examples, since 6.0.15): what the
   format is read from, what the example shows and how many rows the import makes of it, the file itself to look at
   and copy, Download (content/urlimport/(example)/<id>) and, for a text format, Open in the import
   (content/urlimport/(use)/<id>: the first page with the file in its text field). Below: the columns and the kinds.
   The files are the ones of the documentation (expUrlMapExamples).

   Variables (kernel/private/classes/views/content/urlimport.php): examples, example_columns, import_max_size.
   The same file is in design/admin and design/admin4; Copy (data-a4-copy, admin4.js) only in admin4. *}

<div class="context-block a4-page a4-url a4-content-urlmap">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Import a URL map'|i18n( 'design/admin/content/urlimport' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'A URL map lists each old address and the page of this site it belongs to now. These are the formats the import reads, each with an example to copy: change the addresses to your own, then upload the file or paste it. Every example imports into the demo site as it is.'|i18n( 'design/admin/content/urlimport' )}</p>

{include uri='design:content/urlmap_toolbar.tpl' current='examples' all=true()}
{include uri='design:content/urlimport_sources.tpl' current='examples'}

<div class="a4-section">
{foreach $examples as $example}
<section class="a4-card" id="example-{$example.id|wash}" aria-labelledby="example-{$example.id|wash}-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="example-{$example.id|wash}-title">{$example.title|i18n( 'design/admin/content/urlimport' )|wash}</h2>
    <span class="a4-badge is-info">{$example.format_label|wash}</span>
</div>
<div>
    <p>{$example.description|i18n( 'design/admin/content/urlimport' )|wash}</p>
    <p class="a4-meta">{'File %file, %size · read from %extensions · %rows rows in the import'|i18n( 'design/admin/content/urlimport',, hash( '%file', $example.file, '%size', $example.size|si( byte ), '%extensions', concat( '.', $example.extensions|implode( ', .' ) ), '%rows', $example.rows ) )|wash}</p>
    {if $example.text}<pre class="a4-pre" tabindex="0" aria-label="{$example.file|wash}"><code>{$example.text|wash}</code></pre>{/if}
    <div class="a4-actions a4-section">
        <a class="a4-btn" href={concat( 'content/urlimport/(example)/', $example.id )|ezurl} download="{$example.file|wash}"><span class="a4-icon a4-bi-download" aria-hidden="true"></span> {'Download'|i18n( 'design/admin/content/urlimport' )}</a>
        {if $example.is_text}
        <a class="a4-btn a4-btn-primary" href={concat( 'content/urlimport/(use)/', $example.id )|ezurl}>{'Open in the import'|i18n( 'design/admin/content/urlimport' )}</a>
        {/if}
    </div>
</div>
</section>
{/foreach}
</div>

<div class="a4-grid is-wide a4-section">

<section class="a4-card" aria-labelledby="example-columns-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="example-columns-title">{'The columns'|i18n( 'design/admin/content/urlimport' )}</h2>
</div>
<div>
    <p>{'The first line of a CSV file or a spreadsheet names the columns, in any order; JSON uses the same names as keys. Only old_url and new_url are needed. Without a first line of names, the columns are taken in this order.'|i18n( 'design/admin/content/urlimport' )}</p>
    <table class="a4-table a4-content-urlmap-table is-names">
    <thead><tr><th scope="col">{'Column'|i18n( 'design/admin/content/urlimport' )}</th><th scope="col">{'What it holds'|i18n( 'design/admin/content/urlimport' )}</th></tr></thead>
    <tbody>
    <tr><td><code>old_url</code></td><td>{'The old address: a path (/about-us.html), a path with a query (/index.php?id=12) or a full address of the old host. A * matches any part.'|i18n( 'design/admin/content/urlimport' )}</td></tr>
    <tr><td><code>new_url</code></td><td>{'The page of this site: its path (/recipes), its node ID (node:163, which follows the page when it moves) or a full address elsewhere. %first, %second take what the * matched.'|i18n( 'design/admin/content/urlimport',, hash( '%first', '&#123;1&#125;', '%second', '&#123;2&#125;' ) )}</td></tr>
    <tr><td><code>kind</code></td><td>{'What the row becomes, see the kinds. Empty: a permanent redirect, a redirecting wildcard when old_url has a *.'|i18n( 'design/admin/content/urlimport' )}</td></tr>
    <tr><td><code>siteaccess</code></td><td>{'The siteaccess of a bare path; empty: the one chosen on the import page.'|i18n( 'design/admin/content/urlimport' )}</td></tr>
    <tr><td><code>language</code></td><td>{'The language of the URL alias, for example eng-GB; empty: the language of the siteaccess.'|i18n( 'design/admin/content/urlimport' )}</td></tr>
    <tr><td><code>note</code></td><td>{'Your own note; it is kept with the import.'|i18n( 'design/admin/content/urlimport' )}</td></tr>
    <tr><td><code>status</code></td><td>{'The redirect status: 301 or 308 (permanent), 302, 303 or 307 (temporary); empty: the kind decides.'|i18n( 'design/admin/content/urlimport' )}</td></tr>
    </tbody>
    </table>
</div>
</section>

<section class="a4-card" aria-labelledby="example-kinds-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="example-kinds-title">{'The kinds'|i18n( 'design/admin/content/urlimport' )}</h2>
</div>
<div>
    <table class="a4-table a4-content-urlmap-table is-names">
    <thead><tr><th scope="col">{'Kind'|i18n( 'design/admin/content/urlimport' )}</th><th scope="col">{'What visitors get'|i18n( 'design/admin/content/urlimport' )}</th></tr></thead>
    <tbody>
    <tr><td><code>redirect</code></td><td>{'A permanent redirect; a global URL alias that redirects.'|i18n( 'design/admin/content/urlimport' )}</td></tr>
    <tr><td><code>redirect-temporary</code></td><td>{'A temporary redirect: search engines keep the old address.'|i18n( 'design/admin/content/urlimport' )}</td></tr>
    <tr><td><code>alias</code></td><td>{'The new page at the old address, without a redirect; a global URL alias.'|i18n( 'design/admin/content/urlimport' )}</td></tr>
    <tr><td><code>wildcard-forward</code></td><td>{'A permanent redirect for every matching address; a URL wildcard.'|i18n( 'design/admin/content/urlimport' )}</td></tr>
    <tr><td><code>wildcard-forward-temporary</code></td><td>{'A temporary redirect for every matching address.'|i18n( 'design/admin/content/urlimport' )}</td></tr>
    <tr><td><code>wildcard-direct</code></td><td>{'The new page at every matching address, without a redirect.'|i18n( 'design/admin/content/urlimport' )}</td></tr>
    </tbody>
    </table>
    <p class="a4-meta">{'Files up to %size. Every detail of the formats is in the help center: Migrating URLs, and the URL map examples.'|i18n( 'design/admin/content/urlimport',, hash( '%size', $import_max_size|si( byte ) ) )|wash}</p>
</div>
</section>

</div>

</div></div></div>
</div>
