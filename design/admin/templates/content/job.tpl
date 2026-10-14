{* The progress page of a content job (content/job/<id>): a subtree remove or copy running in the background.
   The page polls content/job/<id>?json=1 every two seconds and updates the bar, the counts, the message and the
   log; when the job finishes it opens the result, when it fails or is cancelled it reloads to show the buttons. *}
{def $state_names = hash( 'queued',    'Waiting'|i18n( 'design/admin/content/job' ),
                          'running',   'Running'|i18n( 'design/admin/content/job' ),
                          'done',      'Done'|i18n( 'design/admin/content/job' ),
                          'failed',    'Failed'|i18n( 'design/admin/content/job' ),
                          'cancelled', 'Cancelled'|i18n( 'design/admin/content/job' ) )
     $finished = or( eq( $job.state, 'done' ), eq( $job.state, 'failed' ), eq( $job.state, 'cancelled' ) )}
<div class="context-block a4-page a4-content-job" id="exp-contentjob"
     data-status-url={concat( 'content/job/', $job.id, '?json=1' )|ezurl}
     data-state="{$job.state|wash}"{if $job.can_resume} data-can-resume="1"{/if}
     data-started="{$job.started|wash}"
     data-text-counts="{'%done of %total nodes'|i18n( 'design/admin/content/job' )|wash}"
     data-text-elapsed="{'%seconds s'|i18n( 'design/admin/content/job' )|wash}"
     data-text-opening="{'Opening the result in %seconds s'|i18n( 'design/admin/content/job' )|wash}"
     data-text-stopping="{'Stopping after the current batch'|i18n( 'design/admin/content/job' )|wash}"
     data-text-waiting="{'Waiting for the status...'|i18n( 'design/admin/content/job' )|wash}"
     data-states="{concat( $state_names.queued, '|', $state_names.running, '|', $state_names.done, '|', $state_names.failed, '|', $state_names.cancelled )|wash}">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{$job.title|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $action_error}
<div class="message-error a4-content-job-action-error"><h2>{$action_error|wash}</h2></div>
{/if}

<div class="block">
    <div class="a4-content-job-head">
        <span class="a4-badge a4-content-job-state {$job.state|wash}">{$state_names[$job.state]|wash}</span>
        <span class="a4-content-job-counts">{'%done of %total nodes'|i18n( 'design/admin/content/job',, hash( '%done', $job.done, '%total', $job.total ) )}</span>
        <span class="a4-content-job-elapsed"></span>
    </div>
    <div class="a4-meter a4-content-job-bar {$job.state|wash}" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{$job.percent}" aria-label="{'Progress'|i18n( 'design/admin/content/job' )|wash}"><span class="a4-pct-{$job.percent|round}"></span></div>
    <p class="a4-content-job-message" aria-live="polite">{if and( $job.cancel_requested, $finished|not )}{'Stopping after the current batch'|i18n( 'design/admin/content/job' )}{else}{$job.message|wash}{/if}</p>
</div>

<div class="message-feedback a4-content-job-done"{if ne( $job.state, 'done' )} hidden{/if}>
    <h2>{$job.done_text|wash}</h2>
    <p><a class="a4-content-job-open" href={$job.redirect_url|ezurl}>{$job.open_text|wash}</a> <span class="a4-content-job-auto"></span></p>
</div>

{if $job.warnings|count}
<div class="message-warning a4-content-job-warnings">
    <h2>{'Warnings'|i18n( 'design/admin/content/job' )}</h2>
    <ul>{foreach $job.warnings as $warning}<li>{$warning|wash}</li>{/foreach}</ul>
</div>
{/if}

{if eq( $job.state, 'failed' )}
<div class="message-error a4-content-job-error">
    <h2>{'The job stopped with an error.'|i18n( 'design/admin/content/job' )}</h2>
    <pre>{$job.error|wash}</pre>
    {if $job.error_node_id}<p><a href={concat( 'content/view/full/', $job.error_node_id )|ezurl}>{'Open the node that failed'|i18n( 'design/admin/content/job' )}</a></p>{/if}
    <p>{'Everything up to the last finished batch is kept. Resume continues from there; nothing is done twice.'|i18n( 'design/admin/content/job' )}</p>
</div>
{/if}

{if eq( $job.state, 'cancelled' )}
<div class="message-warning">
    {if eq( $job.type, 'copy' )}
        <h2>{'The copy was cancelled.'|i18n( 'design/admin/content/job' )}</h2>
        {if $job.partial_node_id}
        <p>{'The part copied so far is kept under %target.'|i18n( 'design/admin/content/job',, hash( '%target', $job.target_name ) )|wash}
           <a href={concat( 'content/view/full/', $job.partial_node_id )|ezurl}>{'Open the partial copy'|i18n( 'design/admin/content/job' )}</a></p>
        <p>{'Remove the partial copy starts a job that deletes it. To copy the subtree, start the copy again.'|i18n( 'design/admin/content/job' )}</p>
        {else}
        <p>{if gt( $job.done, 0 )}{'The partial copy is no longer there (it has been removed).'|i18n( 'design/admin/content/job' )}{else}{'Nothing had been copied yet.'|i18n( 'design/admin/content/job' )}{/if}</p>
        {/if}
    {else}
        <h2>{'The removal was cancelled.'|i18n( 'design/admin/content/job' )}</h2>
        <p>{'What was not removed yet is still in place. Remove it again to remove the rest.'|i18n( 'design/admin/content/job' )}</p>
    {/if}
</div>
{/if}

<div class="a4-content-job-groups">
{foreach $job.details as $group}
<section class="a4-content-job-group" data-group="{$group.id|wash}">
<h2>{$group.title|wash}</h2>
<table class="list a4-content-job-details" cellspacing="0">
{foreach $group.rows as $row}
<tr data-key="{$row.key|wash}"><th scope="row">{$row.label|wash}</th>
    <td{if eq( $row.key, 'batch' )} class="a4-content-job-batch"{/if}>{if $row.url}<a href={$row.url|ezurl}>{$row.text|wash}</a>{elseif $row.mono}<code>{$row.text|wash}</code>{else}{$row.text|wash}{/if}</td></tr>
{/foreach}
</table>
</section>
{/foreach}
</div>
<p class="a4-content-job-jobid">{'Job'|i18n( 'design/admin/content/job' )} <code>{$job.id|wash}</code></p>
{* the module name in a variable: a server process started before the audit classes existed (Velocity) then gets no audit link instead of an error *}
{def $cj_audit_module = 'audit'}{if fetch( $cj_audit_module, 'can_read', hash( 'channel', 'content' ) )}<p class="a4-content-job-audit"><a href={concat( 'audit/console/(job)/', $job.id )|ezurl}>{'Audit trail of this job'|i18n( 'design/admin/content/job' )}</a></p>{/if}{undef $cj_audit_module}

<h2>{'Log'|i18n( 'design/admin/content/job' )} <a class="a4-content-job-download" href={concat( 'content/job/', $job.id, '?log=1' )|ezurl}>{'Download the whole log'|i18n( 'design/admin/content/job' )}</a></h2>
<pre class="a4-pre a4-content-job-log" aria-live="off">{$job.log|wash}</pre>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<form method="post" action={concat( 'content/job/', $job.id )|ezurl}>
<div class="block">
    {if $job.can_cancel}
        <input class="button" type="submit" name="CancelJobButton" value="{'Cancel'|i18n( 'design/admin/content/job' )}" title="{'Stop after the current batch. What is done so far is kept.'|i18n( 'design/admin/content/job' )|wash}" />
    {/if}
    {if $job.can_resume}
        <input class="defaultbutton" type="submit" name="ResumeJobButton" value="{'Resume'|i18n( 'design/admin/content/job' )}" title="{'Continue from the last finished batch.'|i18n( 'design/admin/content/job' )|wash}" />
    {/if}
    {if $job.can_remove_partial}
        <input class="button" type="submit" name="RemovePartialCopyButton" value="{'Remove the partial copy'|i18n( 'design/admin/content/job' )}" title="{'Start a job that removes what the cancelled copy created.'|i18n( 'design/admin/content/job' )|wash}" />
    {/if}
    <a class="button" href={'content/jobs'|ezurl}>{'All jobs'|i18n( 'design/admin/content/job' )}</a>
</div>
</form>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

{if or( $finished|not, $job.just_finished )}
<script type="text/javascript">
{literal}
(function () {
  var box = document.getElementById('exp-contentjob'); if (!box || !window.fetch) return;
  var url = box.getAttribute('data-status-url'), first = box.getAttribute('data-state');
  var names = box.getAttribute('data-states').split('|'), keys = ['queued', 'running', 'done', 'failed', 'cancelled'];
  var t = function (k, map) { var s = box.getAttribute('data-text-' + k); for (var m in map) s = s.split(m).join(map[m]); return s; };
  var q = function (sel) { return box.querySelector(sel); };
  function show(s) {
    var badge = q('.a4-content-job-state'); badge.className = 'a4-badge a4-content-job-state ' + s.state; badge.textContent = names[keys.indexOf(s.state)] || s.state;
    q('.a4-content-job-counts').textContent = t('counts', {'%done': s.done, '%total': s.total});
    var bar = q('.a4-content-job-bar'); bar.className = 'a4-meter a4-content-job-bar ' + s.state; bar.setAttribute('aria-valuenow', s.percent); q('.a4-content-job-bar span').className = 'a4-pct-' + Math.max(0, Math.min(100, Math.round(s.percent)));
    q('.a4-content-job-message').textContent = s.cancel_requested && s.state === 'running' ? t('stopping', {}) : (s.message || '');
    var cb = q('.a4-content-job-batch'); if (cb) cb.textContent = s.batch;
    if (s.started) q('.a4-content-job-elapsed').textContent = t('elapsed', {'%seconds': Math.max(0, (s.finished || s.now) - s.started)});
    var log = q('.a4-content-job-log'), atEnd = log.scrollTop + log.clientHeight >= log.scrollHeight - 4;
    log.textContent = s.log || ''; if (atEnd) log.scrollTop = log.scrollHeight;
  }
  function poll() {
    fetch(url, {cache: 'no-store', credentials: 'same-origin'}).then(function (r) { return r.json(); }).then(function (s) {
      if (!s.ok) { q('.a4-content-job-message').textContent = s.error || t('waiting', {}); setTimeout(poll, 4000); return; }
      show(s);
      if (s.can_resume && !box.hasAttribute('data-can-resume')) { location.reload(); return; }
      if (s.state === 'done') {
        var done = q('.a4-content-job-done'), a = q('.a4-content-job-open'), n = 3; done.hidden = false;
        if (s.redirect_url) a.href = s.redirect_url;
        (function tick() { q('.a4-content-job-auto').textContent = t('opening', {'%seconds': n}); if (n-- <= 0) location.href = a.href; else setTimeout(tick, 1000); })();
        return;
      }
      if (s.state !== first && s.state !== 'running' && s.state !== 'queued') { location.reload(); return; }
      setTimeout(poll, 2000);
    }).catch(function () { q('.a4-content-job-message').textContent = t('waiting', {}); setTimeout(poll, 4000); });
  }
  poll();
})();
{/literal}
</script>
{/if}
