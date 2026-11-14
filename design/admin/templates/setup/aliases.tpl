{* Setup > Image aliases (setup/aliases): the image aliases made on demand. Five parts, each its own address
   (/(tab)/<part>): the dashboard, bulk runs as jobs with their progress, test tools, the run history of
   imagealias.log and the settings of [AliasSettings], [AliasStaging] and [AliasSource_<environment>].

   The same file is in design/admin and design/admin4, so every administration design resolves it. It uses the shared
   admin components (components.css) and stylesheets/modules/setup.css (.a4-setup-alias-*), no inline CSS and no
   inline script: page/setup_aliases.js follows a job's progress and the history (texts in #ia-script's data-text-*).
   Everything works without the script: actions are forms (POST with the form token, setup/managealiases), the filter
   and lookup forms are answered with their /(name)/value address. Credentials are named by their secret, never shown. *}

<div class="context-block a4-page a4-setup-alias">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Image aliases'|i18n( 'design/admin/setup/aliases' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Image aliases are the scaled and cropped copies of an image that the templates show. Here you see how they are made, run bulk jobs over the site, test one image or a staging source, follow the run history and change the settings.'|i18n( 'design/admin/setup/aliases' )}</p>

{foreach $feedback as $ia_note}
<div class="a4-notice is-{$ia_note[0]|wash}" role="{if eq( $ia_note[0], 'bad' )}alert{else}status{/if}">
    <p><span class="a4-meta">[{currentdate()|l10n( shortdatetime )}]</span> {$ia_note[1]|wash}</p>
</div>
{/foreach}

<nav aria-label="{'Parts of the page'|i18n( 'design/admin/setup/aliases' )|wash}">
<ul class="a4-tabs a4-setup-alias-tabs">
{foreach $tabs as $ia_tab}
    <li>{if eq( $ia_tab, $tab )}<span class="current" aria-current="page">{else}<a href={concat( 'setup/aliases/(tab)/', $ia_tab )|ezurl}>{/if}{switch match=$ia_tab}{case match='dashboard'}{'Dashboard'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='bulk'}{'Bulk runs'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='tools'}{'Test tools'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='history'}{'Run history'|i18n( 'design/admin/setup/aliases' )}{/case}{case}{'Settings'|i18n( 'design/admin/setup/aliases' )}{/case}{/switch}{if eq( $ia_tab, $tab )}</span>{else}</a>{/if}</li>
{/foreach}
</ul>
</nav>

{if $can_manage|not}
<p class="a4-notice is-info">{'You can look at everything here. Changing settings, running jobs and the tools that make files need the policy setup/managealiases.'|i18n( 'design/admin/setup/aliases' )}</p>
{/if}

{* ---------------------------------------------------------------------------------------------- the dashboard *}
{if eq( $tab, 'dashboard' )}
{def $d = $dashboard}

{if $d.checks|count|gt( 0 )}
<section aria-labelledby="ia-checks-title">
<h2 class="a4-sr-only" id="ia-checks-title">{'What needs attention'|i18n( 'design/admin/setup/aliases' )}</h2>
{foreach $d.checks as $ia_check}
<div class="a4-notice is-{$ia_check.level|wash}">
    <p>{switch match=$ia_check.key}
    {case match='render-mode'}{'Render mode: templates make missing aliases while the page renders, as before. The on-demand addresses, placeholders and background jobs are not used.'|i18n( 'design/admin/setup/aliases' )}{/case}
    {case match='no-webp'}{'Format negotiation is on, but no converter here can write WebP.'|i18n( 'design/admin/setup/aliases' )}{/case}
    {case match='source-no-url'}{'This is a staging installation, but its source has no BaseURL: missing images cannot be fetched.'|i18n( 'design/admin/setup/aliases' )}{/case}
    {case match='source-paused'}{'The staging source is paused after failures in a row: missing images get placeholders until the pause ends or you reset it.'|i18n( 'design/admin/setup/aliases' )}{/case}
    {case match='source-failing'}{'The staging source failed recently. See its last error below.'|i18n( 'design/admin/setup/aliases' )}{/case}
    {case match='staging-no-source'}{'This installation is not production, but SyncFrom names no source: nothing is fetched.'|i18n( 'design/admin/setup/aliases' )}{/case}
    {case match='no-queue'}{'The job queue (expqueue) is not available: aliases that miss their time are made by the next request, pre-generation after publishing is skipped and bulk runs are limited to small scopes.'|i18n( 'design/admin/setup/aliases' )}{/case}
    {case match='dead-jobs'}{'Some jobs of image aliases failed for good. Setup > Job queue shows why.'|i18n( 'design/admin/setup/aliases' )}{/case}
    {case match='bulk-paused'}{'Bulk runs are paused.'|i18n( 'design/admin/setup/aliases' )}{/case}
    {case match='failures'}{'Aliases failed in the last 24 hours. The run history lists them.'|i18n( 'design/admin/setup/aliases' )}{/case}
    {case match='key-old'}{'The signing key is more than a year old. Rotating it keeps old addresses valid for the grace period.'|i18n( 'design/admin/setup/aliases' )}{/case}
    {case match='no-key'}{'There is no signing key yet: it is made with the first on-demand address.'|i18n( 'design/admin/setup/aliases' )}{/case}
    {case}{$ia_check.key|wash}{/case}
    {/switch}</p>
</div>
{/foreach}
</section>
{/if}

<section aria-labelledby="ia-counts-title">
<div class="a4-section-head"><h2 class="a4-h2" id="ia-counts-title">{'The last 24 hours'|i18n( 'design/admin/setup/aliases' )}</h2></div>
<ul class="a4-figures">
    <li class="a4-figure is-ok"><a href={'setup/aliases/(tab)/history/(event)/generate'|ezurl}><strong>{$d.counts.generated}</strong><span>{'Aliases generated'|i18n( 'design/admin/setup/aliases' )}</span></a></li>
    <li class="a4-figure{if $d.counts.placeholders|gt( 0 )} is-warn{/if}"><a href={'setup/aliases/(tab)/history/(event)/placeholder'|ezurl}><strong>{$d.counts.placeholders}</strong><span>{'Placeholders answered'|i18n( 'design/admin/setup/aliases' )}</span></a></li>
    <li class="a4-figure{if $d.counts.failures|gt( 0 )} is-bad{/if}"><a href={'setup/aliases/(tab)/history/(event)/fail'|ezurl}><strong>{$d.counts.failures}</strong><span>{'Failures'|i18n( 'design/admin/setup/aliases' )}</span></a></li>
    <li class="a4-figure"><a href={'setup/aliases/(tab)/history/(event)/stage'|ezurl}><strong>{$d.counts.staged}</strong><span>{'Files staged'|i18n( 'design/admin/setup/aliases' )}</span></a></li>
    <li class="a4-figure"><a href={'setup/aliases/(tab)/history/(event)/runs'|ezurl}><strong>{$d.counts.runs}</strong><span>{'Runs started'|i18n( 'design/admin/setup/aliases' )}</span></a></li>
    <li class="a4-figure{if $d.queue_total.waiting|gt( 0 )} is-info{/if}"><a href={'setup/aliases/(tab)/bulk'|ezurl}><strong>{$d.queue_total.waiting} / {$d.queue_total.running}</strong><span>{'Jobs waiting / running'|i18n( 'design/admin/setup/aliases' )}</span></a></li>
</ul>
{if $d.counts.complete|not}<p class="a4-meta">{'Counted from the last %lines lines of %file, which reach back to %time.'|i18n( 'design/admin/setup/aliases',, hash( '%lines', $d.counts.lines, '%file', 'imagealias.log', '%time', $d.counts.oldest|l10n( shortdatetime ) ) )}</p>{/if}
</section>

<div class="a4-grid is-wide">

<section class="a4-card" aria-labelledby="ia-mode-title">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-mode-title">{'Mode and delivery'|i18n( 'design/admin/setup/aliases' )}</h2></div>
    <div class="a4-card-body">
    <dl class="a4-facts is-rows">
        <div><dt>{'Mode'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{if eq( $d.mode, 'render' )}<span class="a4-badge is-info">{'render'|i18n( 'design/admin/setup/aliases' )}</span>{else}<span class="a4-badge is-ok">{'on demand'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</dd></div>
        <div><dt>{'Address form'|i18n( 'design/admin/setup/aliases' )}</dt><dd><code>{$d.url_form|wash}</code></dd></div>
        <div><dt>{'Delivery'|i18n( 'design/admin/setup/aliases' )}</dt><dd><code>{$d.delivery|wash}</code></dd></div>
        <div><dt>{'Longest wait of a request'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{'%seconds s'|i18n( 'design/admin/setup/aliases',, hash( '%seconds', $d.max_wait ) )}</dd></div>
        <div><dt>{'Conversions at once'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{'%busy of %max busy now'|i18n( 'design/admin/setup/aliases',, hash( '%busy', $d.busy, '%max', $d.max_parallel ) )}</dd></div>
        <div><dt>{'Format negotiation'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{if $d.negotiate}{'on'|i18n( 'design/admin/setup/aliases' )}{else}{'off'|i18n( 'design/admin/setup/aliases' )}{/if}</dd></div>
        <div><dt>{'Placeholder and swap script'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{if $d.placeholder}{'on'|i18n( 'design/admin/setup/aliases' )}{else}{'off'|i18n( 'design/admin/setup/aliases' )}{/if} / {if $d.swap_script}{'on'|i18n( 'design/admin/setup/aliases' )}{else}{'off'|i18n( 'design/admin/setup/aliases' )}{/if}</dd></div>
        <div><dt>{'Pre-generation after publishing'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{if $d.pre_generate}{'on'|i18n( 'design/admin/setup/aliases' )}{if $d.pre_generate_aliases|count} <code>{$d.pre_generate_aliases|implode( ', ' )|wash}</code>{/if}{if $d.pre_generate_formats|not} <span class="a4-meta">{'Formats'|i18n( 'design/admin/setup/aliases' )}: {'off'|i18n( 'design/admin/setup/aliases' )}</span>{/if}{else}{'off'|i18n( 'design/admin/setup/aliases' )}{/if}</dd></div>
        <div><dt>{'Aliases of the site'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{$d.aliases}</dd></div>
    </dl>
    </div>
</section>

<section class="a4-card" aria-labelledby="ia-conv-title">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-conv-title">{'Converters of the format variants'|i18n( 'design/admin/setup/aliases' )}</h2></div>
    <div class="a4-card-body">
    <div class="a4-table-wrap" role="region" aria-labelledby="ia-conv-title" tabindex="0">
    <table class="a4-table is-compact">
    <thead><tr><th scope="col">{'Converter'|i18n( 'design/admin/setup/aliases' )}</th><th scope="col">{'Tried'|i18n( 'design/admin/setup/aliases' )}</th><th scope="col">WebP</th><th scope="col">AVIF</th></tr></thead>
    <tbody>
    {foreach $d.converter_rows as $ia_conv}
    <tr>
        <th scope="row"><code>{$ia_conv.converter|wash}</code>{if $ia_conv.preferred} <span class="a4-badge is-accent">{'preferred'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</th>
        <td class="a4-num">{$ia_conv.rank}</td>
        <td>{if $ia_conv.formats|contains( 'webp' )}<span class="a4-badge is-ok">{'yes'|i18n( 'design/admin/setup/aliases' )}</span>{else}<span class="a4-muted">{'no'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</td>
        <td>{if $ia_conv.formats|contains( 'avif' )}<span class="a4-badge is-ok">{'yes'|i18n( 'design/admin/setup/aliases' )}</span>{else}<span class="a4-muted">{'no'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</td>
    </tr>
    {/foreach}
    </tbody>
    </table>
    </div>
    <p class="a4-meta">{'The aliases themselves are made by the converters of [ImageConverterSettings], as before; these write the WebP and AVIF variants.'|i18n( 'design/admin/setup/aliases' )}</p>
    </div>
</section>

<section class="a4-card{if eq( $d.source_state, 'paused' )} is-bad{elseif eq( $d.source_state, 'warn' )} is-warn{/if}" aria-labelledby="ia-staging-title">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-staging-title">{'Environment and source'|i18n( 'design/admin/setup/aliases' )}</h2></div>
    <div class="a4-card-body">
    <dl class="a4-facts is-rows">
        <div><dt>{'This installation'|i18n( 'design/admin/setup/aliases' )}</dt><dd><code>{$d.staging.environment|wash}</code>{if eq( $d.staging.environment, 'pro' )} <span class="a4-meta">{'(production: never fetches)'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</dd></div>
        <div><dt>{'Fetches from'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{if $d.staging.sync_from|ne( '' )}<code>{$d.staging.sync_from|wash}</code>{else}<span class="a4-muted">{'nothing'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</dd></div>
        {if $d.source}
        <div><dt>{'Source address'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{if $d.source.base_url|ne( '' )}<code class="a4-setup-alias-path">{$d.source.base_url|wash}</code>{else}<span class="a4-bad-text">{'not set'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</dd></div>
        <div><dt>{'Credential'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{if $d.source.credential|ne( '' )}{'secret %name'|i18n( 'design/admin/setup/aliases',, hash( '%name', $d.source.credential ) )|wash}{else}{'none'|i18n( 'design/admin/setup/aliases' )}{/if}</dd></div>
        <div><dt>{'State'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{switch match=$d.source_state}{case match='ok'}<span class="a4-badge is-ok">{'working'|i18n( 'design/admin/setup/aliases' )}</span>{/case}{case match='paused'}<span class="a4-badge is-bad">{'paused until %time'|i18n( 'design/admin/setup/aliases',, hash( '%time', $d.source.paused_until|l10n( shortdatetime ) ) )}</span>{/case}{case match='warn'}<span class="a4-badge is-warn">{'failing'|i18n( 'design/admin/setup/aliases' )}</span>{/case}{case}<span class="a4-muted">&mdash;</span>{/case}{/switch}</dd></div>
        <div><dt>{'Failures in a row'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{$d.source.failures|wash} <span class="a4-meta">({'pause after %count'|i18n( 'design/admin/setup/aliases',, hash( '%count', $d.staging.failures_before_pause ) )})</span></dd></div>
        {if $d.source.last_error}<div><dt>{'Last error'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{$d.source.last_error|wash}{if and( is_set( $d.source.last_error_time ), $d.source.last_error_time|gt( 0 ) )} <span class="a4-meta">{$d.source.last_error_time|l10n( shortdatetime )}</span>{/if}</dd></div>{/if}
        {if $d.source.last_success|gt( 0 )}<div><dt>{'Last success'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{$d.source.last_success|l10n( shortdatetime )}</dd></div>{/if}
        {/if}
    </dl>
    {if and( $can_manage, $d.source, or( $d.source.paused, $d.source.failures|gt( 0 ) ) )}
    <form method="post" action={'setup/aliases'|ezurl}>
        <button type="submit" class="a4-btn" name="ResetSourceButton" value="1">{'Reset the failures and ask the source again'|i18n( 'design/admin/setup/aliases' )}</button>
    </form>
    {/if}
    </div>
</section>

<section class="a4-card" aria-labelledby="ia-queue-title">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-queue-title">{'Job queue'|i18n( 'design/admin/setup/aliases' )}</h2></div>
    <div class="a4-card-body">
    {if $d.queue.available}
    <div class="a4-table-wrap" role="region" aria-labelledby="ia-queue-title" tabindex="0">
    <table class="a4-table is-compact">
    <thead><tr><th scope="col">{'Job type'|i18n( 'design/admin/setup/aliases' )}</th><th scope="col">{'Waiting'|i18n( 'design/admin/setup/aliases' )}</th><th scope="col">{'Running'|i18n( 'design/admin/setup/aliases' )}</th><th scope="col">{'Failed'|i18n( 'design/admin/setup/aliases' )}</th></tr></thead>
    <tbody>
    {foreach $d.queue.types as $ia_type => $ia_row}
    <tr><th scope="row"><code>{$ia_type|wash}</code>{if $ia_row.registered|not} <span class="a4-badge is-warn">{'not registered'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</th><td class="a4-num">{$ia_row.waiting}</td><td class="a4-num">{$ia_row.running}</td><td class="a4-num">{$ia_row.dead}</td></tr>
    {/foreach}
    </tbody>
    </table>
    </div>
    {else}
    <p class="a4-empty">{'The job queue is not available.'|i18n( 'design/admin/setup/aliases' )}</p>
    {/if}
    <p>{if $d.bulk_pause}<span class="a4-badge is-warn">{'Bulk runs paused'|i18n( 'design/admin/setup/aliases' )}</span> <span class="a4-meta">{'since %time by %user'|i18n( 'design/admin/setup/aliases',, hash( '%time', $d.bulk_pause.since|l10n( shortdatetime ), '%user', $d.bulk_pause.by ) )|wash}</span>{else}<span class="a4-badge is-ok">{'Bulk runs may run'|i18n( 'design/admin/setup/aliases' )}</span>{/if}
       <a href={'setup/aliases/(tab)/bulk'|ezurl}>{'Bulk runs'|i18n( 'design/admin/setup/aliases' )}</a></p>
    </div>
</section>

<section class="a4-card" aria-labelledby="ia-key-title">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-key-title">{'Signing key'|i18n( 'design/admin/setup/aliases' )}</h2></div>
    <div class="a4-card-body">
    <dl class="a4-facts is-rows">
        <div><dt>{'Key'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{if $d.key.exists}{'in the secret store'|i18n( 'design/admin/setup/aliases' )}{else}<span class="a4-muted">{'none yet'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</dd></div>
        {if $d.key_age_days|is_null|not}<div><dt>{'Age'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{'%days days (since %time)'|i18n( 'design/admin/setup/aliases',, hash( '%days', $d.key_age_days, '%time', $d.key.modified|l10n( shortdatetime ) ) )}</dd></div>{/if}
        <div><dt>{'Previous key'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{if $d.previous_key_valid}{'valid until %time'|i18n( 'design/admin/setup/aliases',, hash( '%time', $d.key.previous_until|l10n( shortdatetime ) ) )}{else}<span class="a4-muted">{'none'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</dd></div>
    </dl>
    {if and( $can_manage, $d.key.exists )}
    <form method="post" action={'setup/aliases'|ezurl}>
        <details class="a4-confirm">
            <summary class="a4-btn">{'Rotate the key'|i18n( 'design/admin/setup/aliases' )}</summary>
            <p>{'Addresses signed with the current key keep working for the grace period (SigningKeyGrace), then they answer placeholders until the pages are made again.'|i18n( 'design/admin/setup/aliases' )}</p>
            <button type="submit" class="a4-btn a4-btn-danger" name="RotateKeyButton" value="1">{'Rotate now'|i18n( 'design/admin/setup/aliases' )}</button>
        </details>
    </form>
    {/if}
    </div>
</section>

</div>

<details class="a4-fold" id="ia-how">
    <summary>
        <h2 class="a4-h2">{'How it works'|i18n( 'design/admin/setup/aliases' )}</h2>
        <span class="a4-meta">{'On demand, render mode, staging and the log'|i18n( 'design/admin/setup/aliases' )}</span>
    </summary>
    <div class="a4-fold-body a4-stack">
        <ul class="a4-plain">
            <li>{'On demand (the default): a page links an alias that is not there yet to a signed address. The first request makes it within the longest wait, else it answers a neutral placeholder and a job finishes the work; the swap script replaces the placeholder when the image is there.'|i18n( 'design/admin/setup/aliases' )}</li>
            <li>{'Render mode: the template makes the alias while the page renders, as before. Switch between the two at any time; nothing needs to be migrated.'|i18n( 'design/admin/setup/aliases' )}</li>
            <li>{'A staging installation fetches the originals and aliases it is missing from its source; a failing source is paused after failures in a row and the administrators get one e-mail an hour at most.'|i18n( 'design/admin/setup/aliases' )}</li>
            <li>{'Bulk runs and pre-generation after publishing are jobs of the job queue, at a lower priority than visitors. Removing aliases also clears the response cache of Velocity, so a removed file is not served from it.'|i18n( 'design/admin/setup/aliases' )}</li>
            <li>{'Every alias made, placeholder answered, file staged and run is a line of %file. The command line has the same tools: %command.'|i18n( 'design/admin/setup/aliases',, hash( '%file', $log_path, '%command', './console exp:image:alias' ) )|wash}</li>
        </ul>
    </div>
</details>
{undef $d}
{/if}

{* ---------------------------------------------------------------------------------------------- bulk runs *}
{if eq( $tab, 'bulk' )}

{if $job}
<section class="a4-card" id="ia-job" aria-labelledby="ia-job-title" data-feed={concat( 'setup/aliasesfeed/(job)/', $job.id )|ezurl} data-final="{if $job.final}1{else}0{/if}">
    <div class="a4-card-head">
        <h2 class="a4-h2" id="ia-job-title">{'Job %job: %action'|i18n( 'design/admin/setup/aliases',, hash( '%job', $job.id, '%action', $job.action ) )|wash}</h2>
        <span class="a4-badge is-info" id="ia-job-state">{$job.state|wash}</span>
    </div>
    <div class="a4-card-body">
    <div class="a4-meter is-ok a4-setup-progress" aria-hidden="true"><span id="ia-job-bar" class="a4-pct-{$job.percent}"></span></div>
    <p aria-live="polite"><span id="ia-job-count">{'%done of %total images'|i18n( 'design/admin/setup/aliases',, hash( '%done', $job.done, '%total', $job.total ) )}</span> <span class="a4-meta" id="ia-job-message">{$job.message|wash}</span></p>
    <dl class="a4-facts">
        <div><dt>{'Run'|i18n( 'design/admin/setup/aliases' )}</dt><dd><a href={concat( 'setup/aliases/(tab)/history/(run)/', $job.run|view_parameter )|ezurl}><code>{$job.run|wash}</code></a></dd></div>
        <div><dt>{'Scope'|i18n( 'design/admin/setup/aliases' )}</dt><dd><code>{$job.scope|wash}</code></dd></div>
        <div id="ia-job-counts-row"{if $job.counts|count|eq( 0 )} hidden{/if}><dt>{'Result'|i18n( 'design/admin/setup/aliases' )}</dt><dd id="ia-job-counts">{foreach $job.counts as $ia_name => $ia_count}{if $ia_count|gt( 0 )}{$ia_name|wash}={$ia_count} {/if}{/foreach}</dd></div>
        <div id="ia-job-error-row"{if $job.error|eq( '' )} hidden{/if}><dt>{'Last error'|i18n( 'design/admin/setup/aliases' )}</dt><dd id="ia-job-error">{$job.error|wash}</dd></div>
    </dl>
    {if and( $can_manage, $job.can_cancel )}
    <form method="post" action={'setup/aliases'|ezurl} id="ia-job-cancel">
        <button type="submit" class="a4-btn a4-btn-outline-danger" name="CancelJobButton" value="{$job.id}">{'Cancel this job'|i18n( 'design/admin/setup/aliases' )}</button>
    </form>
    {/if}
    </div>
</section>
{elseif $job_id|gt( 0 )}
<p class="a4-empty">{'Job %job is not a job of image aliases, or it was removed from the queue.'|i18n( 'design/admin/setup/aliases',, hash( '%job', $job_id ) )}</p>
{/if}

{if and( $run_result, $run_result.summary )}
<section class="a4-card is-ok" aria-labelledby="ia-sync-title">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-sync-title">{'Result of run %run'|i18n( 'design/admin/setup/aliases',, hash( '%run', $run_result.run ) )|wash}</h2></div>
    <div class="a4-card-body">
    <dl class="a4-facts">
        {foreach $run_result.summary as $ia_name => $ia_value}{if is_array( $ia_value )|not}<div><dt>{$ia_name|wash}</dt><dd>{$ia_value|wash}</dd></div>{/if}{/foreach}
        {if is_set( $run_result.summary.counts )}{foreach $run_result.summary.counts as $ia_name => $ia_count}<div><dt>{$ia_name|wash}</dt><dd>{$ia_count|wash}</dd></div>{/foreach}{/if}
    </dl>
    </div>
</section>
{/if}

<section class="a4-card{if $bulk_pause} is-warn{/if}" aria-labelledby="ia-pause-title">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-pause-title">{if $bulk_pause}{'Bulk runs are paused'|i18n( 'design/admin/setup/aliases' )}{else}{'Bulk runs may run'|i18n( 'design/admin/setup/aliases' )}{/if}</h2></div>
    <div class="a4-card-body">
    {if $bulk_pause}
    <p>{'Paused since %time by %user.'|i18n( 'design/admin/setup/aliases',, hash( '%time', $bulk_pause.since|l10n( shortdatetime ), '%user', $bulk_pause.by ) )|wash}{if $bulk_pause.reason|ne( '' )} <span class="a4-meta">{$bulk_pause.reason|wash}</span>{/if}</p>
    {else}
    <p class="a4-meta">{'A pause stops every bulk run after its current image, on every server; queued runs wait. Visitors\' aliases are still made.'|i18n( 'design/admin/setup/aliases' )}</p>
    {/if}
    {if $can_manage}
    <form method="post" action={'setup/aliases'|ezurl} class="a4-row">
        {if $bulk_pause}
        <button type="submit" class="a4-btn a4-btn-primary" name="ResumeBulkButton" value="1">{'Resume bulk runs'|i18n( 'design/admin/setup/aliases' )}</button>
        {else}
        <label class="a4-sr-only" for="ia-pause-reason">{'Reason (optional)'|i18n( 'design/admin/setup/aliases' )}</label>
        <input type="text" class="a4-input a4-setup-alias-reason" id="ia-pause-reason" name="PauseReason" maxlength="200" placeholder="{'Reason (optional)'|i18n( 'design/admin/setup/aliases' )|wash}" />
        <button type="submit" class="a4-btn" name="PauseBulkButton" value="1">{'Pause bulk runs'|i18n( 'design/admin/setup/aliases' )}</button>
        {/if}
    </form>
    {/if}
    </div>
</section>

{if $can_manage}
<section class="a4-card" aria-labelledby="ia-run-title">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-run-title">{'Start a run'|i18n( 'design/admin/setup/aliases' )}</h2>
        <p>{if $queue.available}{'Runs are jobs of the job queue: you can leave the page and come back.'|i18n( 'design/admin/setup/aliases' )}{else}{'Without the job queue a run is done in this request, for at most %count images.'|i18n( 'design/admin/setup/aliases',, hash( '%count', $sync_limit ) )}{/if}</p></div>
    <div class="a4-card-body">
    <form method="post" action={'setup/aliases'|ezurl} class="a4-form is-wide">
    <fieldset class="a4-setup-alias-fieldset">
        <legend class="a4-label">{'Action'|i18n( 'design/admin/setup/aliases' )}</legend>
        <div class="a4-radios">
        <label class="a4-check"><input type="radio" name="Run[action]" value="generate"{if eq( $run_input.action, 'generate' )} checked="checked"{/if} /> <span>{'Generate: make the aliases that are missing'|i18n( 'design/admin/setup/aliases' )}</span></label>
        <label class="a4-check"><input type="radio" name="Run[action]" value="regenerate"{if eq( $run_input.action, 'regenerate' )} checked="checked"{/if} /> <span>{'Regenerate: remove and make them again'|i18n( 'design/admin/setup/aliases' )}</span></label>
        <label class="a4-check"><input type="radio" name="Run[action]" value="purge"{if eq( $run_input.action, 'purge' )} checked="checked"{/if} /> <span>{'Purge: remove them (made again on demand)'|i18n( 'design/admin/setup/aliases' )}</span></label>
        <label class="a4-check"><input type="radio" name="Run[action]" value="resync"{if eq( $run_input.action, 'resync' )} checked="checked"{/if}{if $staging_active|not} disabled="disabled"{/if} /> <span>{'Resync: fetch the files from the staging source'|i18n( 'design/admin/setup/aliases' )}{if $staging_active|not} <span class="a4-meta">{'(only on a staging installation)'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</span></label>
        </div>
        {if is_set( $run_errors.action )}<span class="a4-field-error">{$run_errors.action|wash}</span>{/if}
    </fieldset>
    <fieldset class="a4-setup-alias-fieldset">
        <legend class="a4-label">{'Scope'|i18n( 'design/admin/setup/aliases' )}</legend>
        <div class="a4-radios">
        <label class="a4-check"><input type="radio" name="Run[target]" value="all"{if eq( $run_input.target, 'all' )} checked="checked"{/if} /> <span>{'The whole site'|i18n( 'design/admin/setup/aliases' )}</span></label>
        <label class="a4-check"><input type="radio" name="Run[target]" value="node"{if eq( $run_input.target, 'node' )} checked="checked"{/if} /> <span>{'One node'|i18n( 'design/admin/setup/aliases' )}</span></label>
        <label class="a4-check"><input type="radio" name="Run[target]" value="subtree"{if eq( $run_input.target, 'subtree' )} checked="checked"{/if} /> <span>{'A node and its subtree'|i18n( 'design/admin/setup/aliases' )}</span></label>
        </div>
        <div class="a4-filterbar">
            <div class="a4-field"><label for="ia-run-node">{'Node id'|i18n( 'design/admin/setup/aliases' )}</label>
                <input type="text" inputmode="numeric" class="a4-input" id="ia-run-node" name="Run[node]" value="{$run_input.node|wash}" maxlength="10" />
                {if is_set( $run_errors.node )}<span class="a4-field-error">{$run_errors.node|wash}</span>{/if}</div>
            <div class="a4-field"><label for="ia-run-class">{'Classes'|i18n( 'design/admin/setup/aliases' )}</label>
                <input type="text" class="a4-input" id="ia-run-class" name="Run[class]" value="{$run_input.class|wash}" placeholder="article, image" />
                {if is_set( $run_errors.class )}<span class="a4-field-error">{$run_errors.class|wash}</span>{/if}</div>
            <div class="a4-field"><label for="ia-run-attribute">{'Attributes'|i18n( 'design/admin/setup/aliases' )}</label>
                <input type="text" class="a4-input" id="ia-run-attribute" name="Run[attribute]" value="{$run_input.attribute|wash}" placeholder="image" />
                {if is_set( $run_errors.attribute )}<span class="a4-field-error">{$run_errors.attribute|wash}</span>{/if}</div>
            <div class="a4-field"><label for="ia-run-since">{'Changed since'|i18n( 'design/admin/setup/aliases' )}</label>
                <input type="text" class="a4-input" id="ia-run-since" name="Run[since]" value="{$run_input.since|wash}" placeholder="2 days" aria-describedby="ia-run-since-help" />
                <span class="a4-help" id="ia-run-since-help">{'A date (2026-11-01) or a time ago (12 hours, 2 days).'|i18n( 'design/admin/setup/aliases' )}</span>
                {if is_set( $run_errors.since )}<span class="a4-field-error">{$run_errors.since|wash}</span>{/if}</div>
            <div class="a4-field"><label for="ia-run-limit">{'At most images'|i18n( 'design/admin/setup/aliases' )}</label>
                <input type="text" inputmode="numeric" class="a4-input" id="ia-run-limit" name="Run[limit]" value="{$run_input.limit|wash}" maxlength="7" />
                {if is_set( $run_errors.limit )}<span class="a4-field-error">{$run_errors.limit|wash}</span>{/if}</div>
        </div>
    </fieldset>
    <fieldset class="a4-setup-alias-fieldset">
        <legend class="a4-label">{'Aliases (none ticked: all of them)'|i18n( 'design/admin/setup/aliases' )}</legend>
        <div class="a4-chips">
        {foreach $alias_plan as $ia_sa => $ia_names}{foreach $ia_names as $ia_alias}
            <label class="a4-chip"><input type="checkbox" name="Run[alias][]" value="{$ia_alias|wash}"{if $run_input.alias|contains( $ia_alias )} checked="checked"{/if} /> {$ia_alias|wash}</label>
        {/foreach}{/foreach}
        {if $staging_active}<label class="a4-chip"><input type="checkbox" name="Run[alias][]" value="original"{if $run_input.alias|contains( 'original' )} checked="checked"{/if} /> original <span class="a4-meta">({'resync'|i18n( 'design/admin/setup/aliases' )})</span></label>{/if}
        </div>
        {if is_set( $run_errors.alias )}<span class="a4-field-error">{$run_errors.alias|wash}</span>{/if}
    </fieldset>
    <div class="a4-radios">
        <label class="a4-check"><input type="checkbox" name="Run[dry_run]" value="1"{if $run_input.dry_run} checked="checked"{/if} /> <span>{'Dry run: only count (a resync checks the source and stores nothing)'|i18n( 'design/admin/setup/aliases' )}</span></label>
        <label class="a4-check"><input type="checkbox" name="Run[overwrite]" value="1"{if $run_input.overwrite} checked="checked"{/if} /> <span>{'Overwrite files that differ (resync)'|i18n( 'design/admin/setup/aliases' )}</span></label>
    </div>
    <div class="a4-actions">
        <button type="submit" class="a4-btn a4-btn-primary" name="StartRunButton" value="1">{'Start the run'|i18n( 'design/admin/setup/aliases' )}</button>
    </div>
    </form>
    </div>
</section>
{/if}

<section aria-labelledby="ia-jobs-title">
<div class="a4-section-head"><h2 class="a4-h2" id="ia-jobs-title">{'Latest runs'|i18n( 'design/admin/setup/aliases' )}</h2></div>
{if $jobs|count|gt( 0 )}
<div class="a4-table-wrap" role="region" aria-labelledby="ia-jobs-title" tabindex="0">
<table class="a4-table">
<thead><tr>
    <th scope="col">{'Job'|i18n( 'design/admin/setup/aliases' )}</th>
    <th scope="col">{'Action'|i18n( 'design/admin/setup/aliases' )}</th>
    <th scope="col">{'Scope'|i18n( 'design/admin/setup/aliases' )}</th>
    <th scope="col">{'State'|i18n( 'design/admin/setup/aliases' )}</th>
    <th scope="col">{'Progress'|i18n( 'design/admin/setup/aliases' )}</th>
    <th scope="col">{'Queued'|i18n( 'design/admin/setup/aliases' )}</th>
    <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/setup/aliases' )}</span></th>
</tr></thead>
<tbody>
{foreach $jobs as $ia_job}
<tr>
    <td><a href={concat( 'setup/aliases/(tab)/bulk/(job)/', $ia_job.id )|ezurl}>{$ia_job.id}</a></td>
    <td>{$ia_job.action|wash}</td>
    <td><code class="a4-setup-alias-path">{$ia_job.scope|wash}</code></td>
    <td><span class="a4-badge{switch match=$ia_job.state}{case match='done'} is-ok{/case}{case match='dead'} is-bad{/case}{case match='running'} is-info{/case}{case match='cancelled'}{/case}{case} is-warn{/case}{/switch}">{$ia_job.state|wash}</span></td>
    <td class="a4-num">{if $ia_job.total|gt( 0 )}{$ia_job.done} / {$ia_job.total}{else}&mdash;{/if}</td>
    <td class="a4-nowrap">{$ia_job.created|l10n( shortdatetime )}</td>
    <td>{if and( $can_manage, $ia_job.can_cancel )}<form method="post" action={'setup/aliases'|ezurl}><button type="submit" class="a4-btn a4-btn-small" name="CancelJobButton" value="{$ia_job.id}">{'Cancel'|i18n( 'design/admin/setup/aliases' )}</button></form>{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{else}
<p class="a4-empty">{'No bulk run or resync yet.'|i18n( 'design/admin/setup/aliases' )}</p>
{/if}
</section>
{/if}

{* ---------------------------------------------------------------------------------------------- test tools *}
{if eq( $tab, 'tools' )}

<section class="a4-card" aria-labelledby="ia-test-title">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-test-title">{'One image'|i18n( 'design/admin/setup/aliases' )}</h2>
        <p>{'The addresses a template gets now, the sizes and types computed for every alias, and whether each file is there.'|i18n( 'design/admin/setup/aliases' )}</p></div>
    <div class="a4-card-body">
    <form method="post" action={'setup/aliases'|ezurl} class="a4-searchrow" id="ia-test-form" data-base={'setup/aliases/(tab)/tools'|ezurl}>
        <label class="a4-sr-only" for="ia-test-kind">{'Look up by'|i18n( 'design/admin/setup/aliases' )}</label>
        <select class="a4-input is-auto" id="ia-test-kind" name="TestImageKind">
            <option value="node"{if $test_object|eq( 0 )} selected="selected"{/if}>{'Node id'|i18n( 'design/admin/setup/aliases' )}</option>
            <option value="object"{if $test_object|gt( 0 )} selected="selected"{/if}>{'Object id'|i18n( 'design/admin/setup/aliases' )}</option>
        </select>
        <label class="a4-sr-only" for="ia-test-id">{'Id'|i18n( 'design/admin/setup/aliases' )}</label>
        <input type="text" inputmode="numeric" class="a4-input" id="ia-test-id" name="TestImageID" maxlength="10" value="{if $test_node|gt( 0 )}{$test_node}{elseif $test_object|gt( 0 )}{$test_object}{/if}" />
        <button type="submit" class="a4-btn a4-btn-primary" name="TestImageButton" value="1">{'Show'|i18n( 'design/admin/setup/aliases' )}</button>
    </form>

    {if $test_image}
    {if $test_image.ok|not}
        <p class="a4-empty">{switch match=$test_image.error}{case match='no-access'}{'You may not read this object.'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='no-image'}{'This object has no image.'|i18n( 'design/admin/setup/aliases' )}{/case}{case}{'No object with this id.'|i18n( 'design/admin/setup/aliases' )}{/case}{/switch}</p>
    {else}
        <p><strong>{$test_image.object.name|wash}</strong> <span class="a4-meta">{'object %object, node %node, version %version'|i18n( 'design/admin/setup/aliases',, hash( '%object', $test_image.object.id, '%node', $test_image.object.node, '%version', $test_image.object.version ) )}</span></p>
        {foreach $test_image.images as $ia_image}
        <div class="a4-section-head"><h3 class="a4-h3">{$ia_image.name|wash} <code>{$ia_image.identifier|wash}</code> <span class="a4-lang">{$ia_image.language|wash}</span></h3></div>
        {if $ia_image.original}
        <p class="a4-meta">{'Original: %width x %height, %type'|i18n( 'design/admin/setup/aliases',, hash( '%width', $ia_image.original.width, '%height', $ia_image.original.height, '%type', $ia_image.original.mime ) )|wash} {if $ia_image.original.exists}<span class="a4-badge is-ok">{'file there'|i18n( 'design/admin/setup/aliases' )}</span>{else}<span class="a4-badge is-bad">{'file missing'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</p>
        <div class="a4-table-wrap" role="region" aria-label="{'Aliases of %name'|i18n( 'design/admin/setup/aliases',, hash( '%name', $ia_image.identifier ) )|wash}" tabindex="0">
        <table class="a4-table is-compact">
        <thead><tr>
            <th scope="col">{'Alias'|i18n( 'design/admin/setup/aliases' )}</th>
            <th scope="col">{'Size'|i18n( 'design/admin/setup/aliases' )}</th>
            <th scope="col">{'Type'|i18n( 'design/admin/setup/aliases' )}</th>
            <th scope="col">{'File'|i18n( 'design/admin/setup/aliases' )}</th>
            <th scope="col">{'Formats'|i18n( 'design/admin/setup/aliases' )}</th>
            <th scope="col">{'Address in a template'|i18n( 'design/admin/setup/aliases' )}</th>
            <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/setup/aliases' )}</span></th>
        </tr></thead>
        <tbody>
        {foreach $ia_image.aliases as $ia_alias}
        <tr>
            <th scope="row"><code>{$ia_alias.alias|wash}</code>{if $ia_alias.siteaccess|ne( '' )} <span class="a4-meta">{$ia_alias.siteaccess|wash}</span>{/if}</th>
            <td class="a4-nowrap">{if $ia_alias.width|gt( 0 )}{$ia_alias.width} x {$ia_alias.height}{else}&mdash;{/if}</td>
            <td>{$ia_alias.mime|wash}</td>
            <td>{if $ia_alias.exists}<span class="a4-badge is-ok">{'there'|i18n( 'design/admin/setup/aliases' )}</span>{else}<span class="a4-badge is-warn">{'on demand'|i18n( 'design/admin/setup/aliases' )}</span>{/if}</td>
            <td>{foreach $ia_alias.formats as $ia_format => $ia_state}<span class="a4-tag">{$ia_format|wash}: {switch match=$ia_state}{case match='present'}{'there'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='missing'}{'missing'|i18n( 'design/admin/setup/aliases' )}{/case}{case}{'not for this type'|i18n( 'design/admin/setup/aliases' )}{/case}{/switch}</span> {/foreach}</td>
            <td><code class="a4-setup-alias-path">{$ia_alias.url|wash}</code></td>
            <td>{if $can_manage}<form method="post" action={'setup/aliases'|ezurl}>
                <input type="hidden" name="TestNodeID" value="{$test_node}" /><input type="hidden" name="TestObjectID" value="{$test_object}" />
                <button type="submit" class="a4-btn a4-btn-small" name="MakeAliasButton" value="{concat( $ia_image.attribute, '|', $ia_image.version, '|', $ia_image.language, '|', $ia_alias.alias, '|', $ia_alias.siteaccess )|wash}">{if $ia_alias.exists}{'Check'|i18n( 'design/admin/setup/aliases' )}{else}{'Make'|i18n( 'design/admin/setup/aliases' )}{/if}</button>
            </form>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
        </table>
        </div>
        {else}
        <p class="a4-empty">{'No image in this field.'|i18n( 'design/admin/setup/aliases' )}</p>
        {/if}
        {/foreach}
    {/if}
    {/if}
    {if $made}
    <dl class="a4-facts">
        <div><dt>{'Alias'|i18n( 'design/admin/setup/aliases' )}</dt><dd><code>{$made.alias|wash}</code></dd></div>
        <div><dt>{'Result'|i18n( 'design/admin/setup/aliases' )}</dt><dd><span class="a4-badge {if $made.ok}is-ok{else}is-bad{/if}">{$made.result|wash}</span></dd></div>
        <div><dt>{'Time'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{'%ms ms'|i18n( 'design/admin/setup/aliases',, hash( '%ms', $made.ms ) )}</dd></div>
        {if $made.variants|count|gt( 0 )}<div><dt>{'Formats'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{foreach $made.variants as $ia_format => $ia_state}<span class="a4-tag">{$ia_format|wash}: {$ia_state|wash}</span> {/foreach}</dd></div>{/if}
    </dl>
    {/if}
    </div>
</section>

<section class="a4-card" aria-labelledby="ia-source-title">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-source-title">{'Connection to a source'|i18n( 'design/admin/setup/aliases' )}</h2>
        <p>{'A HEAD request to a staging source, with its credential when one is named. The credential is sent, never shown.'|i18n( 'design/admin/setup/aliases' )}</p></div>
    <div class="a4-card-body">
    {if $sources|count|gt( 0 )}
    {if $can_manage}
    <form method="post" action={'setup/aliases'|ezurl} class="a4-filterbar">
        <div class="a4-field"><label for="ia-source-env">{'Source'|i18n( 'design/admin/setup/aliases' )}</label>
            <select class="a4-input" id="ia-source-env" name="SourceEnvironment">
            {foreach $sources as $ia_source}<option value="{$ia_source.environment|wash}"{if and( $source_test, eq( $source_test.environment, $ia_source.environment ) )} selected="selected"{/if}>{$ia_source.environment|wash}: {$ia_source.base_url|wash}{if $ia_source.credential} ({'with credential'|i18n( 'design/admin/setup/aliases' )}){/if}</option>{/foreach}
            </select></div>
        <div class="a4-field a4-field-wide"><label for="ia-source-path">{'Storage path (optional)'|i18n( 'design/admin/setup/aliases' )}</label>
            <input type="text" class="a4-input" id="ia-source-path" name="SourcePath" maxlength="500" placeholder="var/site/storage/images/..." /></div>
        <div class="a4-field"><button type="submit" class="a4-btn a4-btn-primary" name="TestSourceButton" value="1">{'Test the connection'|i18n( 'design/admin/setup/aliases' )}</button></div>
    </form>
    {/if}
    {else}
    <p class="a4-empty">{'No source has a BaseURL. Sources are set in the settings ([AliasSource_<environment>]).'|i18n( 'design/admin/setup/aliases' )|wash}</p>
    {/if}
    {if $source_test}
    <dl class="a4-facts">
        <div><dt>{'Result'|i18n( 'design/admin/setup/aliases' )}</dt><dd><span class="a4-badge {if $source_test.ok}is-ok{else}is-bad{/if}">{switch match=$source_test.result}{case match='ok'}{'reachable'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='unauthorized'}{'refused: check the credential'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='missing'}{'not found at the source'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='down'}{'no answer'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='timeout'}{'timed out'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='redirect'}{'redirected (not followed)'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='no-url'}{'no BaseURL'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='bad-path'}{'not a storage path'|i18n( 'design/admin/setup/aliases' )}{/case}{case}{$source_test.result|wash}{/case}{/switch}</span></dd></div>
        {if $source_test.url|ne( '' )}<div><dt>{'Address'|i18n( 'design/admin/setup/aliases' )}</dt><dd><code class="a4-setup-alias-path">{$source_test.url|wash}</code></dd></div>{/if}
        <div><dt>{'Status'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{if $source_test.status|gt( 0 )}{$source_test.status}{else}&mdash;{/if}{if $source_test.type|ne( '' )} <span class="a4-meta">{$source_test.type|wash}</span>{/if}{if $source_test.length|gt( 0 )} <span class="a4-meta">{$source_test.length|si( byte )}</span>{/if}</dd></div>
        <div><dt>{'Time'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{'%ms ms'|i18n( 'design/admin/setup/aliases',, hash( '%ms', $source_test.ms ) )}</dd></div>
        <div><dt>{'Credential'|i18n( 'design/admin/setup/aliases' )}</dt><dd>{switch match=$source_test.credential}{case match='sent'}{'sent'|i18n( 'design/admin/setup/aliases' )}{/case}{case match='missing'}<span class="a4-bad-text">{'the secret is missing'|i18n( 'design/admin/setup/aliases' )}</span>{/case}{case match='unusable'}<span class="a4-bad-text">{'the secret is not user:password or Bearer <token>'|i18n( 'design/admin/setup/aliases' )|wash}</span>{/case}{case}{'none'|i18n( 'design/admin/setup/aliases' )}{/case}{/switch}</dd></div>
    </dl>
    {/if}
    </div>
</section>
{/if}

{* ---------------------------------------------------------------------------------------------- run history *}
{if eq( $tab, 'history' )}
<section aria-labelledby="ia-history-title">
<div class="a4-section-head"><h2 class="a4-h2" id="ia-history-title">{'Run history'|i18n( 'design/admin/setup/aliases' )}</h2>
    <p class="a4-meta">{'The newest lines of %file, newest first.'|i18n( 'design/admin/setup/aliases',, hash( '%file', $log_path ) )|wash} <a href={'setup/logs'|ezurl}>{'Setup > Log files'|i18n( 'design/admin/setup/aliases' )|wash}</a></p></div>
<form method="post" action={'setup/aliases'|ezurl} class="a4-filterbar" id="ia-history-form" data-base={'setup/aliases/(tab)/history'|ezurl}>
    <div class="a4-field"><label for="ia-history-event">{'Event'|i18n( 'design/admin/setup/aliases' )}</label>
        <select class="a4-input" id="ia-history-event" name="HistoryEvent" data-name="event">
            <option value="">{'All events'|i18n( 'design/admin/setup/aliases' )}</option>
            {foreach $events as $ia_event}<option value="{$ia_event|wash}"{if eq( $filter.event, $ia_event )} selected="selected"{/if}>{if eq( $ia_event, 'runs' )}{'Runs (start and end)'|i18n( 'design/admin/setup/aliases' )}{else}{$ia_event|wash}{/if}</option>{/foreach}
        </select></div>
    <div class="a4-field"><label for="ia-history-run">{'Run'|i18n( 'design/admin/setup/aliases' )}</label>
        <input type="text" class="a4-input" id="ia-history-run" name="HistoryRun" data-name="run" value="{$filter.run|wash}" maxlength="64" /></div>
    <div class="a4-field"><label for="ia-history-alias">{'Alias'|i18n( 'design/admin/setup/aliases' )}</label>
        <input type="text" class="a4-input" id="ia-history-alias" name="HistoryAlias" data-name="alias" value="{$filter.alias|wash}" maxlength="100" /></div>
    <div class="a4-field"><label for="ia-history-text">{'Path contains'|i18n( 'design/admin/setup/aliases' )}</label>
        <input type="text" class="a4-input" id="ia-history-text" name="HistoryText" data-name="text" value="{$filter.text|wash}" maxlength="200" /></div>
    <div class="a4-field"><label for="ia-history-lines">{'Lines'|i18n( 'design/admin/setup/aliases' )}</label>
        <select class="a4-input" id="ia-history-lines" name="HistoryLines" data-name="lines">{foreach array( 50, 100, 200, 500 ) as $ia_lines}<option value="{$ia_lines}"{if eq( $filter.lines, $ia_lines )} selected="selected"{/if}>{$ia_lines}</option>{/foreach}</select></div>
    <div class="a4-field"><button type="submit" class="a4-btn a4-btn-primary" name="HistoryFilterButton" value="1">{'Filter'|i18n( 'design/admin/setup/aliases' )}</button></div>
</form>
<p class="a4-row a4-setup-js-only" hidden><label class="a4-check" for="ia-follow"><input type="checkbox" id="ia-follow" checked="checked" /> <span>{'Follow: show new lines as they are written'|i18n( 'design/admin/setup/aliases' )}</span></label> <span class="a4-meta" id="ia-follow-state" aria-live="polite"></span></p>
<div class="a4-table-wrap" role="region" aria-labelledby="ia-history-title" tabindex="0">
<table class="a4-table is-compact" id="ia-log" data-feed={concat( 'setup/aliasesfeed', $feed_suffix )|ezurl}>
<thead><tr>
    <th scope="col">{'Time'|i18n( 'design/admin/setup/aliases' )}</th>
    <th scope="col">{'Event'|i18n( 'design/admin/setup/aliases' )}</th>
    <th scope="col">{'Result'|i18n( 'design/admin/setup/aliases' )}</th>
    <th scope="col">{'Alias'|i18n( 'design/admin/setup/aliases' )}</th>
    <th scope="col">ms</th>
    <th scope="col">{'Run'|i18n( 'design/admin/setup/aliases' )}</th>
    <th scope="col">{'Path and details'|i18n( 'design/admin/setup/aliases' )}</th>
</tr></thead>
<tbody id="ia-log-rows">
{foreach $entries as $ia_entry}
<tr>
    <td class="a4-nowrap">{$ia_entry.time|l10n( shortdatetime )}</td>
    <td><span class="a4-badge{switch match=$ia_entry.event}{case match='fail'} is-bad{/case}{case match='placeholder'} is-warn{/case}{case match='generate'} is-ok{/case}{case match='stage'} is-info{/case}{case}{/case}{/switch}">{$ia_entry.event|wash}</span></td>
    <td>{$ia_entry.result|wash}</td>
    <td><code>{$ia_entry.alias|wash}</code></td>
    <td class="a4-num">{$ia_entry.ms|wash}</td>
    <td>{if $ia_entry.run|ne( '' )}<a href={concat( 'setup/aliases/(tab)/history/(run)/', $ia_entry.run|view_parameter )|ezurl}><code>{$ia_entry.run|wash}</code></a>{/if}</td>
    <td><code class="a4-setup-alias-path">{$ia_entry.path|wash}</code>{if $ia_entry.extra|ne( '' )}<br /><span class="a4-meta a4-setup-alias-path">{$ia_entry.extra|wash}</span>{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
<p class="a4-empty" id="ia-log-empty"{if $entries|count|gt( 0 )} hidden{/if}>{'No line of the log matches.'|i18n( 'design/admin/setup/aliases' )}</p>
</section>
{/if}

{* ---------------------------------------------------------------------------------------------- settings *}
{if eq( $tab, 'settings' )}
{if $settings_result}
<section class="a4-card{if $settings_result.preview} is-info{else} is-ok{/if}" aria-labelledby="ia-diff-title">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-diff-title">{if $settings_result.preview}{'What saving would change'|i18n( 'design/admin/setup/aliases' )}{else}{'What was changed'|i18n( 'design/admin/setup/aliases' )}{/if}</h2>
        <p><code>{$settings_result.path|wash}</code></p></div>
    <div class="a4-card-body"><pre class="a4-pre">{$settings_result.diff|wash}</pre></div>
</section>
{/if}

<form method="post" action={'setup/aliases'|ezurl} id="ia-settings-form">
<p class="a4-meta">{'Saved into settings/override/image.ini.append.php, so a value here wins over every siteaccess. The values shown are those in force for this siteaccess. Saving asks you to confirm who you are first.'|i18n( 'design/admin/setup/aliases' )}</p>
<datalist id="ia-secret-names">{foreach $secret_names as $ia_secret}<option value="{$ia_secret.name|wash}"></option>{/foreach}</datalist>

{foreach array( 'AliasSettings', 'AliasStaging' ) as $ia_block}
<section class="a4-card" aria-labelledby="ia-block-{$ia_block}">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-block-{$ia_block}">[{$ia_block}]</h2>
        <p>{if eq( $ia_block, 'AliasSettings' )}{'How aliases are made and delivered.'|i18n( 'design/admin/setup/aliases' )}{else}{'Fetching missing images from another installation (alpha, dev, beta, stage, pro, post, arc). Production (pro) never fetches.'|i18n( 'design/admin/setup/aliases' )}{/if}</p></div>
    <div class="a4-card-body">
    <div class="a4-table-wrap" role="region" aria-labelledby="ia-block-{$ia_block}" tabindex="0">
    <table class="a4-table is-compact a4-setup-alias-settings">
    <thead><tr><th scope="col">{'Setting'|i18n( 'design/admin/setup/aliases' )}</th><th scope="col">{'Value'|i18n( 'design/admin/setup/aliases' )}</th><th scope="col">{'In settings/override'|i18n( 'design/admin/setup/aliases' )}</th></tr></thead>
    <tbody>
    {foreach $settings[$ia_block].variables as $ia_row}
    {include uri='design:setup/aliases_setting_row.tpl' row=$ia_row can_manage=$can_manage}
    {/foreach}
    </tbody>
    </table>
    </div>
    </div>
</section>
{/foreach}

<section class="a4-card" aria-labelledby="ia-block-sources">
    <div class="a4-card-head"><h2 class="a4-h2" id="ia-block-sources">[AliasSource_&lt;environment&gt;]</h2>
        <p>{'The address of each source and the name of the secret that holds its credential (user:password or Bearer <token>). The value of a secret is set in Setup > Secrets and never shown here.'|i18n( 'design/admin/setup/aliases' )|wash}</p></div>
    <div class="a4-card-body">
    <div class="a4-table-wrap" role="region" aria-labelledby="ia-block-sources" tabindex="0">
    <table class="a4-table is-compact a4-setup-alias-settings">
    <thead><tr><th scope="col">{'Setting'|i18n( 'design/admin/setup/aliases' )}</th><th scope="col">{'Value'|i18n( 'design/admin/setup/aliases' )}</th><th scope="col">{'In settings/override'|i18n( 'design/admin/setup/aliases' )}</th></tr></thead>
    <tbody>
    {foreach $environments as $ia_env}
    {foreach $settings[concat( 'AliasSource_', $ia_env )].variables as $ia_row}
    {include uri='design:setup/aliases_setting_row.tpl' row=$ia_row can_manage=$can_manage}
    {/foreach}
    {/foreach}
    </tbody>
    </table>
    </div>
    </div>
</section>

{if $can_manage}
<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button type="submit" class="a4-btn" name="PreviewSettingsButton" value="1">{'Preview the changes'|i18n( 'design/admin/setup/aliases' )}</button>
        <button type="submit" class="a4-btn a4-btn-primary" name="SaveSettingsButton" value="1">{'Save'|i18n( 'design/admin/setup/aliases' )}</button>
    </div>
</div>
{/if}
</form>
{/if}

</div></div></div>
</div>
<div id="ia-script" hidden data-text-images="{'%done of %total images'|i18n( 'design/admin/setup/aliases' )|wash}" data-text-following="{'Following: updated %time'|i18n( 'design/admin/setup/aliases' )|wash}" data-text-paused="{'Paused'|i18n( 'design/admin/setup/aliases' )|wash}" data-text-failed="{'The feed did not answer; trying again.'|i18n( 'design/admin/setup/aliases' )|wash}" data-text-ended="{'The job has ended.'|i18n( 'design/admin/setup/aliases' )|wash}"></div>
{ezscript( array( 'page/setup_aliases.js' ) )}
