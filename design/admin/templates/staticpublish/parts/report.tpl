{* The facts of an exp-static/1 report: a build (pages, files, links, size, diff, refused pages, files missing, the link
   check) or a publish or rollback (commit, tag, push, counts). Variables: report. *}
{if is_set( $report.push_state )}
<dl class="a4-facts is-rows">
    <dt>{'Commit'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if $report.commit_id}<code class="a4-code">{$report.commit_id|shorten( 12, '' )|wash}</code>{else}<span class="a4-muted">{'none: nothing changed'|i18n( 'design/admin/staticpublish' )}</span>{/if}</dd>
    <dt>{'Tag'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if $report.tag_name}<code class="a4-code">{$report.tag_name|wash}</code>{else}-{/if}</dd>
    <dt>{'Push'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$report.push_state|wash}</dd>
    <dt>{'Files'|i18n( 'design/admin/staticpublish' )}</dt><dd>{'%added added, %changed changed, %removed removed'|i18n( 'design/admin/staticpublish',, hash( '%added', $report.counts.added, '%changed', $report.counts.changed, '%removed', $report.counts.removed ) )|wash}</dd>
    {if $report.counts.bytes}<dt>{'Size'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$report.counts.bytes|si( 'byte' )}</dd>{/if}
    {if and( is_set( $report.from_tag ), $report.from_tag )}<dt>{'Files of'|i18n( 'design/admin/staticpublish' )}</dt><dd><code class="a4-code">{$report.from_tag|wash}</code></dd>{/if}
</dl>
{if $report.messages|count}<ul class="a4-plain">{foreach $report.messages as $message}<li class="a4-meta">{$message|wash}</li>{/foreach}</ul>{/if}
{elseif is_set( $report.counts.pages_refused )}
<dl class="a4-facts">
    <div><dt>{'Pages'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$report.counts.pages|wash}</dd></div>
    <div><dt>{'Files'|i18n( 'design/admin/staticpublish' )}</dt><dd>{sum( $report.counts.files, $report.counts.storage, $report.counts.extras )|wash}</dd></div>
    <div><dt>{'Size'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$report.counts.bytes|si( 'byte' )}</dd></div>
    <div><dt>{'Links rewritten'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$report.counts.links_rewritten|wash}</dd></div>
    <div><dt>{'Refused'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if $report.counts.pages_refused}<span class="a4-badge is-bad">{$report.counts.pages_refused|wash}</span>{else}0{/if}</dd></div>
    <div><dt>{'Files missing'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if $report.counts.files_failed}<span class="a4-badge is-warn">{$report.counts.files_failed|wash}</span>{else}0{/if}</dd></div>
    {if $report.link_check}<div><dt>{'Broken links'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if $report.link_check.broken_count}<span class="a4-badge is-bad">{$report.link_check.broken_count|wash}</span>{else}0{/if}</dd></div>{/if}
    <div><dt>{'Time'|i18n( 'design/admin/staticpublish' )}</dt><dd>{'%seconds s'|i18n( 'design/admin/staticpublish',, hash( '%seconds', div( $report.duration_ms, 1000 )|round ) )|wash}</dd></div>
</dl>
{if $report.diff}<p>{'Compared with the folder before: %added added, %changed changed, %removed removed, %unchanged unchanged.'|i18n( 'design/admin/staticpublish',, hash( '%added', $report.diff.added, '%changed', $report.diff.changed, '%removed', $report.diff.removed, '%unchanged', $report.diff.unchanged ) )|wash}</p>{/if}
{if $report.refused|count}
<h3 class="a4-h3">{'Pages refused'|i18n( 'design/admin/staticpublish' )}</h3>
<p class="a4-help">{'A page that still carries debug output, a form token or a session id after stripping is never published.'|i18n( 'design/admin/staticpublish' )}</p>
<ul class="a4-plain">{foreach $report.refused as $r}<li><code class="a4-code">{$r.path|wash}</code> <span class="a4-meta">{$r.reasons|implode( ', ' )|wash}</span></li>{/foreach}</ul>
{/if}
{if $report.files_failed|count}
<h3 class="a4-h3">{'Files that could not be collected'|i18n( 'design/admin/staticpublish' )}</h3>
<ul class="a4-plain">{foreach $report.files_failed as $f max 20}<li><code class="a4-code">{$f.url|wash}</code> <span class="a4-meta">{$f.error|wash}</span></li>{/foreach}</ul>
{/if}
{if and( is_set( $report.degraded ), $report.degraded|count )}
<h3 class="a4-h3">{'What does not work statically'|i18n( 'design/admin/staticpublish' )}</h3>
<p class="a4-help">{'These parts of the pages need a server; each got a fallback. Check them before you publish.'|i18n( 'design/admin/staticpublish' )}</p>
<ul class="a4-plain">{foreach $report.degraded as $feature => $d}<li><strong>{switch match=$feature}
    {case match='search'}{'Search'|i18n( 'design/admin/staticpublish' )}{/case}
    {case match='collect'}{'Forms that collect information'|i18n( 'design/admin/staticpublish' )}{/case}
    {case match='comments'}{'Comments'|i18n( 'design/admin/staticpublish' )}{/case}
    {case match='shop'}{'Basket'|i18n( 'design/admin/staticpublish' )}{/case}
    {case match='login'}{'Login'|i18n( 'design/admin/staticpublish' )}{/case}
    {case match='ajax'}{'Calls to the server'|i18n( 'design/admin/staticpublish' )}{/case}
    {case}{'Other forms'|i18n( 'design/admin/staticpublish' )}{/case}
    {/switch}</strong> <span class="a4-meta">{'%count on %pages page(s):'|i18n( 'design/admin/staticpublish',, hash( '%count', $d.count, '%pages', $d.pages ) )|wash}
    {foreach $d.handled as $how => $n}{switch match=$how}
    {case match='search-page'}{'the search page of the copy'|i18n( 'design/admin/staticpublish' )}{/case}
    {case match='endpoint'}{'the form endpoint of the live site'|i18n( 'design/admin/staticpublish' )}{/case}
    {case match='fallback'}{'the fallback text'|i18n( 'design/admin/staticpublish' )}{/case}
    {case match='removed'}{'left out'|i18n( 'design/admin/staticpublish' )}{/case}
    {case}{'the live site'|i18n( 'design/admin/staticpublish' )}{/case}
    {/switch} ({$n|wash}){delimiter}, {/delimiter}{/foreach}</span>
    <br /><span class="a4-meta">{foreach $d.examples as $page max 5}<code class="a4-code">{$page|wash}</code>{delimiter} {/delimiter}{/foreach}</span></li>{/foreach}</ul>
{/if}
{if and( is_set( $report.search ), $report.search )}<p>{'Search index: %pages page(s) in %languages.'|i18n( 'design/admin/staticpublish',, hash( '%pages', $report.search.pages, '%languages', $report.search.languages|implode( ', ' ) ) )|wash}</p>{/if}
{if and( is_set( $report.counts.redirects ), $report.counts.redirects )}<p>{'Redirect pages: %count.'|i18n( 'design/admin/staticpublish',, hash( '%count', $report.counts.redirects ) )|wash}</p>{/if}
{if and( $report.link_check, $report.link_check.broken|count )}
<h3 class="a4-h3">{'Broken links in the build'|i18n( 'design/admin/staticpublish' )}</h3>
<ul class="a4-plain">{foreach $report.link_check.broken as $b max 20}<li><code class="a4-code">{$b.file|wash}</code> &rarr; <code class="a4-code">{$b.link|wash}</code></li>{/foreach}</ul>
{/if}
{/if}
