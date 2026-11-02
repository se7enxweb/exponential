{* The migration/recording view: start and stop the recording of the signed-in user's own changes, open and close the
   site-wide window, and every recording. Variables: ready (the tables exist), problem, messages (kind, text), enabled
   ([RecordingSettings] Recording), mine (the user's running recording or null), site (the site-wide one or null),
   recordings (every recording, newest first: recording_id, kind, label, state, user_id, user_name, started_at,
   stopped_at, event_count, message), can_run, can_rollback, view. *}
<div class="a4-page a4-setup-migration" id="exp-migration-recording">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Recording changes'|i18n( 'design/admin/migration' )}</h1>
        <p class="a4-pagehead-sub">{'While a recording runs, what you change in the administration or the website sidebar is kept as the steps of a migration: content, classes, roles and policies, sections, users and groups, URL aliases, settings. Stop it when you are done, look at what it captured, and generate a migration from it.'|i18n( 'design/admin/migration' )}</p>
    </div>
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}

{foreach $messages as $message}
<div class="a4-notice is-{$message.kind|wash}" role="{if eq( $message.kind, 'bad' )}alert{else}status{/if}"><p>{$message.text|wash}</p></div>
{/foreach}
{if $problem}<div class="a4-notice is-bad" role="alert"><p>{'The recordings could not be read: %problem'|i18n( 'design/admin/migration',, hash( '%problem', $problem ) )|wash}</p></div>{/if}
{if $ready|not}<div class="a4-notice is-warn" role="status"><p>{'The recording tables do not exist yet: run the upgrade (exp:upgrade --apply) first.'|i18n( 'design/admin/migration' )}</p></div>{/if}
{if $enabled|not}<div class="a4-notice is-warn" role="status"><p>{'Recording is switched off in migration.ini ([RecordingSettings] Recording=disabled).'|i18n( 'design/admin/migration' )}</p></div>{/if}

{if and( $ready, $enabled )}
<div class="a4-setup-recording-grid a4-section">
<section class="a4-card" aria-labelledby="migration-recording-mine">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-recording-mine">{'My changes'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Records only what you change, wherever you change it, until you stop it.'|i18n( 'design/admin/migration' )}</p></div>
    <form method="post" action={'migration/recording'|ezurl}>
    <div class="a4-card-body a4-stack">
        {if $mine}
        <p><span class="a4-status is-running"><span class="a4-dot" aria-hidden="true"></span>{'Recording'|i18n( 'design/admin/migration' )}</span>
           {'since %time, %count change(s) so far.'|i18n( 'design/admin/migration',, hash( '%time', $mine.started_at|l10n( 'shortdatetime' ), '%count', $mine.event_count ) )|wash}</p>
        {if $mine.label}<p class="a4-meta">{$mine.label|wash}</p>{/if}
        <input type="hidden" name="StopRecordingID" value="{$mine.recording_id|wash}" />
        {else}
        <div class="a4-field a4-field-wide">
            <label class="a4-label" for="migration-recording-label">{'Label (optional)'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="migration-recording-label" name="RecordingLabel" maxlength="255" value="" />
        </div>
        {/if}
    </div>
    <div class="a4-card-foot a4-actions">
        {if $mine}
        <a class="a4-btn" href={concat( 'migration/recording/', $mine.recording_id )|ezurl}>{'Show what it captured'|i18n( 'design/admin/migration' )}</a>
        <button type="submit" class="a4-btn a4-btn-primary" name="StopButton" value="1">{'Stop recording'|i18n( 'design/admin/migration' )}</button>
        {else}
        <button type="submit" class="a4-btn a4-btn-primary" name="StartButton" value="1">{'Start recording my changes'|i18n( 'design/admin/migration' )}</button>
        {/if}
    </div>
    </form>
</section>

<section class="a4-card" aria-labelledby="migration-recording-site">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-recording-site">{'Site-wide window'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Records every change of every user and of the console until it is closed. Opening it asks you to verify it is you.'|i18n( 'design/admin/migration' )}</p></div>
    <form method="post" action={'migration/recording'|ezurl}>
    <div class="a4-card-body a4-stack">
        {if $site}
        <p><span class="a4-status is-running"><span class="a4-dot" aria-hidden="true"></span>{'Open'|i18n( 'design/admin/migration' )}</span>
           {'since %time, %count change(s) so far.'|i18n( 'design/admin/migration',, hash( '%time', $site.started_at|l10n( 'shortdatetime' ), '%count', $site.event_count ) )|wash}</p>
        {if $site.stopped_at}<p>{'It closes by itself at %time.'|i18n( 'design/admin/migration',, hash( '%time', $site.stopped_at|l10n( 'shortdatetime' ) ) )|wash}</p>{/if}
        {if $site.label}<p class="a4-meta">{$site.label|wash}</p>{/if}
        <input type="hidden" name="StopRecordingID" value="{$site.recording_id|wash}" />
        {else}
        <div class="a4-field a4-field-wide">
            <label class="a4-label" for="migration-recording-site-label">{'Label (optional)'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="migration-recording-site-label" name="RecordingLabel" maxlength="255" value="" />
        </div>
        <div class="a4-field">
            <label class="a4-label" for="migration-recording-site-minutes">{'Close by itself after (minutes, optional)'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="migration-recording-site-minutes" name="RecordingMinutes" type="number" min="1" max="44640" step="1" inputmode="numeric" value="" />
        </div>
        {/if}
    </div>
    <div class="a4-card-foot a4-actions">
        {if $site}
        <a class="a4-btn" href={concat( 'migration/recording/', $site.recording_id )|ezurl}>{'Show what it captured'|i18n( 'design/admin/migration' )}</a>
        <button type="submit" class="a4-btn a4-btn-primary" name="StopButton" value="1">{'Close the window'|i18n( 'design/admin/migration' )}</button>
        {else}
        <button type="submit" class="a4-btn" name="StartSiteButton" value="1">{'Open the site-wide window'|i18n( 'design/admin/migration' )}</button>
        {/if}
    </div>
    </form>
</section>
</div>
{/if}

<section class="a4-card is-flush a4-section" aria-labelledby="migration-recording-list">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-recording-list">{'Recordings'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'The account of a user (login, e-mail, password) and the fields of user accounts are never recorded.'|i18n( 'design/admin/migration' )}</p></div>
    {if $recordings|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-recording-list" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Recording'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Kind'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Started'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Changes'|i18n( 'design/admin/migration' )}</th>
        </tr></thead>
        <tbody>
        {foreach $recordings as $r}
        <tr>
            <td><a href={concat( 'migration/recording/', $r.recording_id )|ezurl}><code class="a4-code">{$r.recording_id|wash}</code></a>
                {if $r.label}<br /><span class="a4-meta">{$r.label|wash}</span>{/if}</td>
            <td>{if eq( $r.kind, 'site' )}{'Site-wide'|i18n( 'design/admin/migration' )}{else}{'Changes of %name'|i18n( 'design/admin/migration',, hash( '%name', $r.user_name ) )|wash}{/if}</td>
            <td>{if eq( $r.state, 'recording' )}<span class="a4-status is-running"><span class="a4-dot" aria-hidden="true"></span>{'Recording'|i18n( 'design/admin/migration' )}</span>{else}<span class="a4-badge is-info">{'Stopped'|i18n( 'design/admin/migration' )}</span>{/if}</td>
            <td>{$r.started_at|l10n( 'shortdatetime' )}</td>
            <td>{$r.event_count|wash}{if $r.message}<br /><span class="a4-meta">{$r.message|wash}</span>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No recording yet.'|i18n( 'design/admin/migration' )}</p></div>
    {/if}
</section>

</div>
