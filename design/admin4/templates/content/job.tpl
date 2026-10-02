{* The progress page of a content job (content/job/<id>): a subtree remove or copy running in the background.
   The page polls content/job/<id>?json=1 every two seconds and updates the bar, the counts, the message and the
   log; when the job finishes it opens the result, when it fails or is cancelled it reloads to show the buttons. *}
{def $state_names = hash( 'queued',    'Waiting'|i18n( 'design/admin/content/job' ),
                          'running',   'Running'|i18n( 'design/admin/content/job' ),
                          'done',      'Done'|i18n( 'design/admin/content/job' ),
                          'failed',    'Failed'|i18n( 'design/admin/content/job' ),
                          'cancelled', 'Cancelled'|i18n( 'design/admin/content/job' ) )
     $finished = or( eq( $job.state, 'done' ), eq( $job.state, 'failed' ), eq( $job.state, 'cancelled' ) )}
<style type="text/css">
{literal}
#exp-contentjob .cj-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: .4em 1em; margin: .3em 0 .2em; }
#exp-contentjob .cj-state { display: inline-block; padding: .15em .6em; border-radius: var(--a4-radius-s, 9px); font-size: .85em; font-weight: bold; text-transform: uppercase; background: var(--a4-line, #e3e6eb); color: var(--a4-muted, #5d6573); }
#exp-contentjob .cj-state.running, #exp-contentjob .cj-state.queued { background: var(--a4-orange-soft, #fde7d9); color: var(--a4-orange-dark, #b84a0e); }
#exp-contentjob .cj-state.done { background: #e1f1e2; color: #2e7d32; }
#exp-contentjob .cj-state.failed { background: #fbe3e6; color: #b00020; }
#exp-contentjob .cj-counts { font-weight: bold; }
#exp-contentjob .cj-elapsed { color: var(--a4-muted, #5d6573); }
#exp-contentjob .cj-bar { height: 14px; background: var(--a4-line, #e3e6eb); border-radius: 7px; overflow: hidden; margin: .5em 0 .4em; max-width: 46em; }
#exp-contentjob .cj-bar span { display: block; height: 100%; width: 0; background: var(--a4-orange, #f26a21); transition: width .4s; }
#exp-contentjob .cj-bar.done span { background: #2e7d32; }
#exp-contentjob .cj-bar.failed span { background: #b00020; }
#exp-contentjob .cj-message { margin: .3em 0 .8em; overflow-wrap: anywhere; }
#exp-contentjob .cj-groups { display: grid; grid-template-columns: repeat(auto-fit, minmax(22em, 1fr)); gap: 0 1.5em; }
#exp-contentjob .cj-group h2 { font-size: 1.05em; margin: .6em 0 .2em; }
#exp-contentjob .cj-download { font-size: .7em; font-weight: normal; margin-left: .8em; }
#exp-contentjob table.cj-details { width: 100%; margin-bottom: 1em; }
#exp-contentjob table.cj-details th { text-align: left; padding-right: 1.5em; white-space: nowrap; font-weight: normal; color: var(--a4-muted, #5d6573); background: transparent; }
#exp-contentjob table.cj-details td { overflow-wrap: anywhere; }
#exp-contentjob .cj-log { max-height: 18em; overflow: auto; background: #161b25; color: #d7dbe2; padding: .8em 1em; border-radius: var(--a4-radius-s, 9px); font-size: .85em; white-space: pre-wrap; overflow-wrap: anywhere; margin: .3em 0 0; }
#exp-contentjob .cj-error pre { white-space: pre-wrap; overflow-wrap: anywhere; margin: .4em 0 0; }
#exp-contentjob [hidden] { display: none !important; }
{/literal}
</style>

<div class="context-block" id="exp-contentjob"
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
<div class="message-error cj-action-error"><h2>{$action_error|wash}</h2></div>
{/if}

<div class="block">
    <div class="cj-head">
        <span class="cj-state {$job.state|wash}">{$state_names[$job.state]|wash}</span>
        <span class="cj-counts">{'%done of %total nodes'|i18n( 'design/admin/content/job',, hash( '%done', $job.done, '%total', $job.total ) )}</span>
        <span class="cj-elapsed"></span>
    </div>
    <div class="cj-bar {$job.state|wash}" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{$job.percent}" aria-label="{'Progress'|i18n( 'design/admin/content/job' )|wash}"><span style="width: {$job.percent}%"></span></div>
    <p class="cj-message" aria-live="polite">{if and( $job.cancel_requested, $finished|not )}{'Stopping after the current batch'|i18n( 'design/admin/content/job' )}{else}{$job.message|wash}{/if}</p>
</div>

<div class="message-feedback cj-done"{if ne( $job.state, 'done' )} hidden{/if}>
    <h2>{$job.done_text|wash}</h2>
    <p><a class="cj-open" href={$job.redirect_url|ezurl}>{$job.open_text|wash}</a> <span class="cj-auto"></span></p>
</div>

{if $job.warnings|count}
<div class="message-warning cj-warnings">
    <h2>{'Warnings'|i18n( 'design/admin/content/job' )}</h2>
    <ul>{foreach $job.warnings as $warning}<li>{$warning|wash}</li>{/foreach}</ul>
</div>
{/if}

{if eq( $job.state, 'failed' )}
<div class="message-error cj-error">
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

<div class="cj-groups">
{foreach $job.details as $group}
<section class="cj-group" data-group="{$group.id|wash}">
<h2>{$group.title|wash}</h2>
<table class="list cj-details" cellspacing="0">
{foreach $group.rows as $row}
<tr data-key="{$row.key|wash}"><th scope="row">{$row.label|wash}</th>
    <td{if eq( $row.key, 'batch' )} class="cj-batch"{/if}>{if $row.url}<a href={$row.url|ezurl}>{$row.text|wash}</a>{elseif $row.mono}<code>{$row.text|wash}</code>{else}{$row.text|wash}{/if}</td></tr>
{/foreach}
</table>
</section>
{/foreach}
</div>
<p class="cj-jobid">{'Job'|i18n( 'design/admin/content/job' )} <code>{$job.id|wash}</code></p>
{* the module name in a variable: a server process started before the audit classes existed (Velocity) then gets no audit link instead of an error *}
{def $cj_audit_module = 'audit'}{if fetch( $cj_audit_module, 'can_read', hash( 'channel', 'content' ) )}<p class="cj-audit"><a href={concat( 'audit/console/(job)/', $job.id )|ezurl}>{'Audit trail of this job'|i18n( 'design/admin/content/job' )}</a></p>{/if}{undef $cj_audit_module}

<h2>{'Log'|i18n( 'design/admin/content/job' )} <a class="cj-download" href={concat( 'content/job/', $job.id, '?log=1' )|ezurl}>{'Download the whole log'|i18n( 'design/admin/content/job' )}</a></h2>
<pre class="cj-log" aria-live="off">{$job.log|wash}</pre>

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
    var badge = q('.cj-state'); badge.className = 'cj-state ' + s.state; badge.textContent = names[keys.indexOf(s.state)] || s.state;
    q('.cj-counts').textContent = t('counts', {'%done': s.done, '%total': s.total});
    var bar = q('.cj-bar'); bar.className = 'cj-bar ' + s.state; bar.setAttribute('aria-valuenow', s.percent); q('.cj-bar span').style.width = s.percent + '%';
    q('.cj-message').textContent = s.cancel_requested && s.state === 'running' ? t('stopping', {}) : (s.message || '');
    var cb = q('.cj-batch'); if (cb) cb.textContent = s.batch;
    if (s.started) q('.cj-elapsed').textContent = t('elapsed', {'%seconds': Math.max(0, (s.finished || s.now) - s.started)});
    var log = q('.cj-log'), atEnd = log.scrollTop + log.clientHeight >= log.scrollHeight - 4;
    log.textContent = s.log || ''; if (atEnd) log.scrollTop = log.scrollHeight;
  }
  function poll() {
    fetch(url, {cache: 'no-store', credentials: 'same-origin'}).then(function (r) { return r.json(); }).then(function (s) {
      if (!s.ok) { q('.cj-message').textContent = s.error || t('waiting', {}); setTimeout(poll, 4000); return; }
      show(s);
      if (s.can_resume && !box.hasAttribute('data-can-resume')) { location.reload(); return; }
      if (s.state === 'done') {
        var done = q('.cj-done'), a = q('.cj-open'), n = 3; done.hidden = false;
        if (s.redirect_url) a.href = s.redirect_url;
        (function tick() { q('.cj-auto').textContent = t('opening', {'%seconds': n}); if (n-- <= 0) location.href = a.href; else setTimeout(tick, 1000); })();
        return;
      }
      if (s.state !== first && s.state !== 'running' && s.state !== 'queued') { location.reload(); return; }
      setTimeout(poll, 2000);
    }).catch(function () { q('.cj-message').textContent = t('waiting', {}); setTimeout(poll, 4000); });
  }
  poll();
})();
{/literal}
</script>
{/if}
