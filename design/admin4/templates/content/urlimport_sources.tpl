{* The ways to start an import of a URL map (since 6.0.15): upload a file (content/urlimport), or make the map from
   the old sitemap, from this site's 404 log, or by suggesting the targets of a map
   (content/urlimport/(source)/sitemap|404|suggest), and the formats with an example of each (examples).

   Parameter: current (upload, sitemap, 404, suggest, examples). The same file is in design/admin and design/admin4. *}
{def $sources = array( hash( 'key', 'upload', 'url', 'content/urlimport', 'text', 'Upload a file'|i18n( 'design/admin/content/urlimport' ) ),
                       hash( 'key', 'sitemap', 'url', 'content/urlimport/(source)/sitemap', 'text', 'From the old sitemap'|i18n( 'design/admin/content/urlimport' ) ),
                       hash( 'key', '404', 'url', 'content/urlimport/(source)/404', 'text', 'From the 404 log'|i18n( 'design/admin/content/urlimport' ) ),
                       hash( 'key', 'suggest', 'url', 'content/urlimport/(source)/suggest', 'text', 'Suggest targets'|i18n( 'design/admin/content/urlimport' ) ),
                       hash( 'key', 'examples', 'url', 'content/urlimport/(source)/examples', 'text', 'Formats and examples'|i18n( 'design/admin/content/urlimport' ) ) )}
<nav class="a4-toolbar" aria-label="{'Ways to start'|i18n( 'design/admin/content/urlimport' )|wash}">
    <div class="a4-actions">
    {foreach $sources as $source}
        {if eq( $source.key, $current )}<a class="a4-btn a4-btn-primary" href={$source.url|ezurl} aria-current="page">{$source.text|wash}</a>{else}<a class="a4-btn" href={$source.url|ezurl}>{$source.text|wash}</a>{/if}
    {/foreach}
    </div>
</nav>
{undef $sources}
