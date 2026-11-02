{* A job of the administration (build, publish or rollback): its progress, followed live while it runs
   (page/staticpublish_job.js over staticpublish/stream), the lines it wrote, and its report once it has ended.
   Variables: job (expStaticJob::get()), job_running, progress (expStaticProgress::fold()), meter, progress_offset, lines,
   report (the command's exp-static/1 report, or null), job_errors. *}
<section class="a4-card a4-section" aria-labelledby="sp-job">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-job">{switch match=$job.kind}{case match='publish'}{'Publish'|i18n( 'design/admin/staticpublish' )}{/case}{case match='rollback'}{'Rollback'|i18n( 'design/admin/staticpublish' )}{/case}{case}{'Build'|i18n( 'design/admin/staticpublish' )}{/case}{/switch}
        <code class="a4-code">{$job.id|wash}</code></h2>
        <p class="a4-meta">{'Started by %user at %time.'|i18n( 'design/admin/staticpublish',, hash( '%user', $job.user, '%time', $job.started|l10n( 'shortdatetime' ) ) )|wash}</p></div>
    <div class="a4-card-body a4-stack"
         {if $job_running}data-staticpublish-stream="{concat( 'staticpublish/stream/', $job.id, '/(offset)/', $progress_offset )|ezurl( 'no' )}"
         data-text-pages="{'%pages pages written'|i18n( 'design/admin/staticpublish' )|wash}"
         data-text-done="{'The job has ended. Loading its report.'|i18n( 'design/admin/staticpublish' )|wash}"
         data-text-lost="{'The progress could not be followed. Reload the page to see how the job is doing.'|i18n( 'design/admin/staticpublish' )|wash}"{/if}>
        <p>{if $job_running}<span class="a4-status is-running"><span class="a4-dot" aria-hidden="true"></span>{'running'|i18n( 'design/admin/staticpublish' )}</span>
           {elseif $report|not}<span class="a4-badge is-warn">{'ended without a report'|i18n( 'design/admin/staticpublish' )}</span>
           {elseif eq( $job.exit, 0 )}<span class="a4-badge is-ok">{'passed'|i18n( 'design/admin/staticpublish' )}</span>
           {elseif eq( $job.exit, 2 )}<span class="a4-badge is-bad">{'refused'|i18n( 'design/admin/staticpublish' )}</span>
           {else}<span class="a4-badge is-bad">{'failed'|i18n( 'design/admin/staticpublish' )}</span>{/if}
           <span class="a4-meta" id="sp-job-phase" aria-live="polite">{if $progress.pages}{'%pages pages written'|i18n( 'design/admin/staticpublish',, hash( '%pages', $progress.pages ) )|wash}{/if}{if and( $job_running, $progress.phase )} &middot; {$progress.phase|wash}{/if}</span></p>
        {if $job_running}
        <div class="a4-meter is-ok a4-setup-progress" aria-hidden="true"><span id="sp-job-bar" class="a4-pct-{$meter}"></span></div>
        <p class="a4-meta">{'This page follows the job as it goes; it goes on in the background when the page is closed.'|i18n( 'design/admin/staticpublish' )} <a href={concat( 'staticpublish/log/', $job.id )|ezurl}>{'Reload'|i18n( 'design/admin/staticpublish' )}</a></p>
        {/if}
        <pre class="a4-pre is-tall" id="sp-job-lines" tabindex="0" aria-label="{'Progress'|i18n( 'design/admin/staticpublish' )|wash}">{$lines|implode( "\n" )|wash}</pre>
    </div>
</section>

{if $report}
<section class="a4-card a4-section{if $report.errors|count} is-bad{/if}" aria-labelledby="sp-report">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-report">{'Report'|i18n( 'design/admin/staticpublish' )}</h2></div>
    <div class="a4-card-body a4-stack">
        {foreach $report.errors as $error}<div class="a4-notice is-bad" role="alert"><p>{$error|wash}</p></div>{/foreach}
        {foreach $report.warnings as $warning}<div class="a4-notice is-warn" role="status"><p>{$warning|wash}</p></div>{/foreach}
        {include uri='design:staticpublish/parts/report.tpl' report=$report}
    </div>
</section>
{/if}

{if $job_errors}
<section class="a4-card a4-section" aria-labelledby="sp-errors">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-errors">{'What the job printed as errors'|i18n( 'design/admin/staticpublish' )}</h2></div>
    <div class="a4-card-body"><pre class="a4-pre">{$job_errors|wash}</pre></div>
</section>
{/if}
