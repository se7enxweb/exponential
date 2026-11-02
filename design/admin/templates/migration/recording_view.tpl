{* migration/recording/<id>: one recording of changes. Variables: recording (expMigrationRecording::summary() with
   user_name: events, steps (type/mode => count), with_omitted, step_types), events (recorded_at, audit_name, step,
   subject, omitted, notes; the first 1000), more_events, plan (the steps a migration of it would have: step, subject,
   audit, omitted), missing_step_types, may_stop, messages, can_run, can_rollback, view. Generate (GenerateButton,
   MigrationName) writes the migration of it when the installation has its step types. *}
<div class="a4-page a4-setup-migration" id="exp-migration-recording-view">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Recording %id'|i18n( 'design/admin/migration',, hash( '%id', $recording.recording_id ) )|wash}</h1>
        <p class="a4-pagehead-sub">{if eq( $recording.kind, 'site' )}{'Site-wide'|i18n( 'design/admin/migration' )}{else}{'Changes of %name'|i18n( 'design/admin/migration',, hash( '%name', $recording.user_name ) )|wash}{/if}
            · {$recording.started_at|l10n( 'shortdatetime' )}{if $recording.stopped_at} – {$recording.stopped_at|l10n( 'shortdatetime' )}{/if}
            {if $recording.label}· {$recording.label|wash}{/if}</p>
    </div>
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}

{foreach $messages as $message}
<div class="a4-notice is-{$message.kind|wash}" role="{if eq( $message.kind, 'bad' )}alert{else}status{/if}"><p>{$message.text|wash}</p></div>
{/foreach}
{if $recording.message}<div class="a4-notice is-warn" role="status"><p>{$recording.message|wash}</p></div>{/if}

<div class="a4-section a4-actions">
    {if and( eq( $recording.state, 'recording' ), $may_stop )}
    <form method="post" action={concat( 'migration/recording/', $recording.recording_id )|ezurl}>
        <input type="hidden" name="StopRecordingID" value="{$recording.recording_id|wash}" />
        <button type="submit" class="a4-btn a4-btn-primary" name="StopButton" value="1">{'Stop recording'|i18n( 'design/admin/migration' )}</button>
    </form>
    {/if}
    <a class="a4-btn" href={'migration/recording'|ezurl}>{'All recordings'|i18n( 'design/admin/migration' )}</a>
</div>

<section class="a4-card a4-section" aria-labelledby="migration-recording-plan">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-recording-plan">{'The migration it would make'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'The changes folded into one step per thing changed: what a migration generated from this recording would do.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body a4-stack">
        {if $missing_step_types|count}
        <div class="a4-notice is-info" role="status"><p>{'A migration can be generated from it once this installation has the YAML step types %types.'|i18n( 'design/admin/migration',, hash( '%types', $missing_step_types|implode( ', ' ) ) )|wash}</p>
            <p><code class="a4-code">./console exp:migration:generate --from-recording={$recording.recording_id|wash}</code></p></div>
        {/if}
        {if $plan|count}
        <ol class="a4-setup-recording-steps">
        {foreach $plan as $p}
            <li><code class="a4-code">{$p.step|wash}</code> {$p.subject|wash}
                {if $p.omitted|count}<br /><span class="a4-meta">{'Left out on purpose: %list'|i18n( 'design/admin/migration',, hash( '%list', $p.omitted|implode( ', ' ) ) )|wash}</span>{/if}</li>
        {/foreach}
        </ol>
        {else}
        <p class="a4-empty">{'Nothing was recorded.'|i18n( 'design/admin/migration' )}</p>
        {/if}
    </div>
    {if and( $plan|count, eq( $missing_step_types|count, 0 ) )}
    <form method="post" action={concat( 'migration/recording/', $recording.recording_id )|ezurl}>
    <div class="a4-card-body">
        <div class="a4-field a4-field-wide">
            <label class="a4-label" for="migration-recording-generate-name">{'Name of the migration (optional)'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="migration-recording-generate-name" name="MigrationName" maxlength="100" value="{$recording.label|wash}" />
        </div>
    </div>
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn a4-btn-primary" name="GenerateButton" value="1">{'Generate migration'|i18n( 'design/admin/migration' )}</button>
    </div>
    </form>
    {/if}
</section>

<section class="a4-card is-flush a4-section" aria-labelledby="migration-recording-events">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-recording-events">{'What it captured'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'%count change(s), in the order they were made.'|i18n( 'design/admin/migration',, hash( '%count', $recording.events ) )|wash}</p></div>
    {if $events|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-recording-events" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Time'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Change'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Step'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'About'|i18n( 'design/admin/migration' )}</th>
        </tr></thead>
        <tbody>
        {foreach $events as $e}
        <tr>
            <td class="a4-nowrap">{$e.recorded_at|l10n( 'shorttime' )}</td>
            <td><code class="a4-code">{$e.audit_name|wash}</code></td>
            <td><code class="a4-code">{$e.step|wash}</code></td>
            <td>{$e.subject|wash}
                {if $e.omitted|count}<br /><span class="a4-meta">{'Left out on purpose: %list'|i18n( 'design/admin/migration',, hash( '%list', $e.omitted|implode( ', ' ) ) )|wash}</span>{/if}
                {foreach $e.notes as $note}<br /><span class="a4-meta">{$note|wash}</span>{/foreach}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {if $more_events}<div class="a4-card-body"><p class="a4-meta">{'%count more: ./console exp:migration:record:show lists every one.'|i18n( 'design/admin/migration',, hash( '%count', $more_events ) )|wash}</p></div>{/if}
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'Nothing was recorded.'|i18n( 'design/admin/migration' )}</p></div>
    {/if}
</section>

<section class="a4-card a4-section is-attention" aria-labelledby="migration-recording-discard">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-recording-discard">{'Discard'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Removes the recording and what it captured. What was changed stays changed.'|i18n( 'design/admin/migration' )}</p></div>
    <form method="post" action={concat( 'migration/recording/', $recording.recording_id )|ezurl}>
    <div class="a4-card-foot a4-actions">
        <input type="hidden" name="DiscardRecordingID" value="{$recording.recording_id|wash}" />
        <label class="a4-check"><input type="checkbox" name="ConfirmDiscard" value="1" /> <span>{'Yes, discard this recording'|i18n( 'design/admin/migration' )}</span></label>
        <button type="submit" class="a4-btn a4-btn-danger" name="DiscardButton" value="1">{'Discard'|i18n( 'design/admin/migration' )}</button>
    </div>
    </form>
</section>

</div>
