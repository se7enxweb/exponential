{* The migration/run view: the form that runs the pending migrations in the background.
   Variables: report (expMigrationStatus::report()), pending (the pending and failed migrations, with kind), changed (the
   migrations whose file changed after they were applied), form (chosen: ids, flags: names, force, reason, long: the long-run fields), flags (the
   names of the run flags), long_run (the long-run fields), long_run_defaults ([RunSettings] of each), messages, problem, job (the run going on, or null), lock_text, login, can_run, can_rollback,
   view. *}
<div class="a4-page a4-setup-migration" id="exp-migration-run">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Run the migrations'|i18n( 'design/admin/migration' )}</h1>
        <p class="a4-pagehead-sub">{'The run goes on in the background as %user, and its page shows it as it goes. Before the first migration a caption of the installation is taken; when a migration fails the run stops and the caption is restored.'|i18n( 'design/admin/migration',, hash( '%user', $login ) )|wash}</p>
    </div>
    <div class="a4-pagehead-actions"><a class="a4-btn" href={'migration/dryrun'|ezurl}>{'Dry run first'|i18n( 'design/admin/migration' )}</a></div>
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}
{include uri='design:migration/parts/running.tpl' job=$job lock_text=$lock_text}

{foreach $messages as $m}
<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{elseif eq( $m.kind, 'warn' )}warn{else}bad{/if}" role="{if eq( $m.kind, 'bad' )}alert{else}status{/if}"><p>{$m.text|wash}</p></div>
{/foreach}
{if $problem}<div class="a4-notice is-bad" role="alert"><p>{'The status could not be read: %problem'|i18n( 'design/admin/migration',, hash( '%problem', $problem ) )|wash}</p></div>{/if}
{foreach $report.errors as $error}<div class="a4-notice is-bad" role="alert"><p>{$error|wash}</p></div>{/foreach}

{if or( $pending|count, $changed|count )}
<form method="post" action={'migration/run'|ezurl} class="a4-form">
<input type="hidden" name="MigrationChoice" value="1" />

{if $pending|count}
<section class="a4-card is-flush a4-section" aria-labelledby="migration-run-list">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-run-list">{'Pending migrations'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'They run in this order. Untick one to leave it, and the ones that require it, for a later run.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-run-list" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col"><span class="a4-sr-only">{'Run it'|i18n( 'design/admin/migration' )}</span></th>
            <th scope="col">{'Migration'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Source'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/migration' )}</th>
        </tr></thead>
        <tbody>
        {foreach $pending as $i => $m}
        <tr>
            <td><input type="checkbox" id="migration-only-{$i}" name="MigrationOnly[]" value="{$m.id|wash}"{if $form.chosen|contains( $m.id )} checked="checked"{/if} /></td>
            <td><label for="migration-only-{$i}"><code class="a4-code">{$m.id|wash}</code></label>{if $m.description}<br /><span class="a4-meta">{$m.description|wash}</span>{/if}
                {if eq( $m.maintenance, 'asked' )}<br /><span class="a4-warn-text">{'Switches maintenance mode on while it runs.'|i18n( 'design/admin/migration' )}</span>{/if}</td>
            <td>{$m.source|wash}</td>
            <td>{include uri='design:migration/parts/state.tpl' state=$m.state kind=$m.kind}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</section>
{/if}

{if $changed|count}
<section class="a4-card a4-section is-attention" aria-labelledby="migration-run-changed">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-run-changed">{'Changed after they were applied'|i18n( 'design/admin/migration' )}</h2></div>
    <div class="a4-card-body a4-stack">
        <p>{'These files are not the ones that were applied. A run refuses them unless the change is accepted with a reason; they are not run again, the new checksum is recorded and the reason kept in the audit log.'|i18n( 'design/admin/migration' )}</p>
        <ul class="a4-plain">{foreach $changed as $m}<li><code class="a4-code">{$m.id|wash}</code> <span class="a4-meta">{$m.file|wash}</span></li>{/foreach}</ul>
        <label class="a4-check"><input type="checkbox" name="ForceChanged" value="1"{if $form.force} checked="checked"{/if} /> <span>{'Accept the changes'|i18n( 'design/admin/migration' )}</span></label>
        <div class="a4-field a4-field-wide">
            <label class="a4-label" for="migration-force-reason">{'Why'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="migration-force-reason" name="ForceReason" maxlength="500" value="{$form.reason|wash}" />
        </div>
    </div>
</section>
{/if}

<section class="a4-card a4-section" aria-labelledby="migration-run-long">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-run-long">{'A long run in parts'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Empty fields keep the settings of migration.ini [RunSettings].'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body a4-grid">
        {foreach $long_run as $name}
        <div class="a4-field">
            <label class="a4-label" for="migration-run-{$name|wash}">{switch match=$name}
            {case match='max-migrations'}{'At most this many migrations'|i18n( 'design/admin/migration' )}{/case}
            {case match='max-time'}{'No further migration after (seconds)'|i18n( 'design/admin/migration' )}{/case}
            {case match='throttle'}{'Pause between migrations (seconds)'|i18n( 'design/admin/migration' )}{/case}
            {case match='nice'}{'Lower priority (1 to 19)'|i18n( 'design/admin/migration' )}{/case}
            {case}{$name|wash}{/case}
            {/switch}</label>
            <input class="a4-input" id="migration-run-{$name|wash}" name="LongRun[{$name|wash}]" type="number" inputmode="{if eq( $name, 'throttle' )}decimal{else}numeric{/if}"
                   min="{if eq( $name, 'throttle' )}0{else}1{/if}"{if eq( $name, 'throttle' )} max="60" step="0.1"{elseif eq( $name, 'nice' )} max="19" step="1"{else} step="1"{/if}
                   value="{$form.long[$name]|wash}" placeholder="{$long_run_defaults[$name]|wash}" />
        </div>
        {/foreach}
    </div>
</section>

<section class="a4-card a4-section" aria-labelledby="migration-run-options">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-run-options">{'Before and after the run'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'The defaults are the safe ones. Each box leaves a step out.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body a4-stack">
        {foreach $flags as $flag}
        <label class="a4-check"><input type="checkbox" name="RunFlag[{$flag|wash}]" value="1"{if $form.flags|contains( $flag )} checked="checked"{/if} />
            <span>{switch match=$flag}
            {case match='no-caption'}<strong>{'No caption'|i18n( 'design/admin/migration' )}</strong><span class="a4-help">{'No caption before and after the run: a failure cannot be restored automatically.'|i18n( 'design/admin/migration' )}</span>{/case}
            {case match='no-restore'}<strong>{'No restore'|i18n( 'design/admin/migration' )}</strong><span class="a4-help">{'After a failure, leave the installation as the failure left it.'|i18n( 'design/admin/migration' )}</span>{/case}
            {case match='include-uncommitted'}<strong>{'Keep changes that are not committed'|i18n( 'design/admin/migration' )}</strong><span class="a4-help">{'Run although settings, extensions or designs have changes that are not committed; they go into the caption.'|i18n( 'design/admin/migration' )}</span>{/case}
            {case match='no-commit'}<strong>{'No commit'|i18n( 'design/admin/migration' )}</strong><span class="a4-help">{'Do not commit the status file and the run log (no tag and no push either).'|i18n( 'design/admin/migration' )}</span>{/case}
            {case match='no-push'}<strong>{'No push'|i18n( 'design/admin/migration' )}</strong><span class="a4-help">{'Commit and tag, but do not push.'|i18n( 'design/admin/migration' )}</span>{/case}
            {case match='no-tag'}<strong>{'No tag'|i18n( 'design/admin/migration' )}</strong><span class="a4-help">{'Commit and push, but do not tag the run.'|i18n( 'design/admin/migration' )}</span>{/case}
            {case}{$flag|wash}{/case}
            {/switch}</span></label>
        {/foreach}
    </div>
    <div class="a4-card-foot a4-actions">
        <label class="a4-check"><input type="checkbox" name="ConfirmRun" value="1" /> <span>{'Yes, run these migrations now'|i18n( 'design/admin/migration' )}</span></label>
        <button type="submit" class="a4-btn a4-btn-primary" name="RunButton" value="1"{if $job} disabled="disabled"{/if}>{'Run'|i18n( 'design/admin/migration' )}</button>
    </div>
</section>
</form>
{else}
<div class="a4-empty a4-section"><p>{'No migration is pending.'|i18n( 'design/admin/migration' )}</p><p><a href={'migration/status'|ezurl}>{'Back to the status'|i18n( 'design/admin/migration' )}</a></p></div>
{/if}

</div>
