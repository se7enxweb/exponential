{* The staticpublish/log view: one job (build, publish or rollback started here) or run (exp_static_run). Variables:
   run_id, run (the row, or null), target_id, target_name, view, and those of parts/job.tpl. *}
<div class="a4-page a4-setup-publish" id="exp-staticpublish-log">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'%target: job %id'|i18n( 'design/admin/staticpublish',, hash( '%target', $target_name, '%id', $run_id ) )|wash}</h1>
        <p class="a4-pagehead-sub">{'What the job did and its record. A job started here goes on in the background when this page is closed.'|i18n( 'design/admin/staticpublish' )}</p>
    </div>
    <div class="a4-pagehead-actions">
        <a class="a4-btn" href={concat( 'staticpublish/history/', $target_id )|ezurl}>{'History'|i18n( 'design/admin/staticpublish' )}</a>
        <a class="a4-btn" href={concat( 'staticpublish/preview/', $target_id )|ezurl}>{'Preview'|i18n( 'design/admin/staticpublish' )}</a>
    </div>
</div>

{if $job}
{include uri='design:staticpublish/parts/job.tpl' job=$job job_running=$job_running progress=$progress meter=$meter progress_offset=$progress_offset lines=$lines report=$report job_errors=$job_errors}
{/if}

{if $run}
<section class="a4-card a4-section" aria-labelledby="sp-record">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-record">{'Record'|i18n( 'design/admin/staticpublish' )}</h2></div>
    <div class="a4-card-body">
        <dl class="a4-facts is-rows">
            <dt>{'State'|i18n( 'design/admin/staticpublish' )}</dt><dd>{include uri='design:staticpublish/parts/run_state.tpl' run=$run}</dd>
            <dt>{'By'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$run.run_by|wash}</dd>
            <dt>{'Started'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$run.started_at|l10n( 'shortdatetime' )}</dd>
            <dt>{'Ended'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if $run.finished_at}{$run.finished_at|l10n( 'shortdatetime' )}{else}-{/if}</dd>
            <dt>{'Pages'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$run.pages|wash}</dd>
            <dt>{'Files'|i18n( 'design/admin/staticpublish' )}</dt><dd>{'%added added, %changed changed, %removed removed'|i18n( 'design/admin/staticpublish',, hash( '%added', $run.added, '%changed', $run.changed, '%removed', $run.removed ) )|wash}</dd>
            <dt>{'Size'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$run.bytes|si( 'byte' )}</dd>
            <dt>{'Commit'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if $run.commit_id}<code class="a4-code">{$run.commit_id|wash}</code>{else}-{/if}</dd>
            <dt>{'Tag'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if $run.tag_name}<code class="a4-code">{$run.tag_name|wash}</code>{else}-{/if}</dd>
            {if $run.message}<dt>{'Message'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$run.message|wash}</dd>{/if}
        </dl>
    </div>
</section>
{/if}

</div>
{if $job_running}{ezscript( array( 'page/staticpublish_job.js' ) )}{/if}
