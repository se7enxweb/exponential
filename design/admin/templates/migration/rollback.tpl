{* The migration/rollback view: take back a run (its caption), one migration (its down()) or, for experts, restore an
   earlier caption. Variables: target, kind (run, migration, caption, '' without a target), run, migration, caption (the
   target's details), runs (runs with a caption taken before them), reversible (applied migrations with a down()),
   captions (expUpgradeCaption::all()), parts, default_parts, messages, problem, job, can_run, can_rollback, view. *}
<div class="a4-page a4-setup-migration" id="exp-migration-rollback">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{if $kind}{'Take back %target'|i18n( 'design/admin/migration',, hash( '%target', $target ) )|wash}{else}{'Rollback'|i18n( 'design/admin/migration' )}{/if}</h1>
        <p class="a4-pagehead-sub">{'A run is taken back to the caption taken before it, one migration with its down(). Restoring an earlier caption is for experts. Each runs in the background as you, after a caption of the state now.'|i18n( 'design/admin/migration' )}</p>
    </div>
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}
{include uri='design:migration/parts/running.tpl' job=$job lock_text=''}

{foreach $messages as $m}
<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{elseif eq( $m.kind, 'warn' )}warn{else}bad{/if}" role="{if eq( $m.kind, 'bad' )}alert{else}status{/if}"><p>{$m.text|wash}</p></div>
{/foreach}
{if $problem}<div class="a4-notice is-bad" role="alert"><p>{'What can be taken back could not be read: %problem'|i18n( 'design/admin/migration',, hash( '%problem', $problem ) )|wash}</p></div>{/if}

{if $kind}
<form method="post" action={concat( 'migration/rollback/', $target|rawurlencode )|ezurl} class="a4-form">
<input type="hidden" name="RollbackTarget" value="{$target|wash}" />
<section class="a4-card a4-section is-attention" aria-labelledby="migration-rollback-target">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-rollback-target">{switch match=$kind}{case match='run'}{'A run'|i18n( 'design/admin/migration' )}{/case}{case match='migration'}{'One migration'|i18n( 'design/admin/migration' )}{/case}{case}{'An earlier caption'|i18n( 'design/admin/migration' )}{/case}{/switch}</h2></div>
    <div class="a4-card-body a4-stack">
        {if eq( $kind, 'run' )}
        <dl class="a4-facts is-rows">
            <dt>{'Run'|i18n( 'design/admin/migration' )}</dt><dd><a href={concat( 'migration/log/', $run.run_id )|ezurl}><code class="a4-code">{$run.run_id|wash}</code></a></dd>
            <dt>{'State'|i18n( 'design/admin/migration' )}</dt><dd>{include uri='design:migration/parts/state.tpl' state=$run.state kind=$run.kind}</dd>
            <dt>{'Started'|i18n( 'design/admin/migration' )}</dt><dd>{if $run.started_ts}{$run.started_ts|l10n( 'shortdatetime' )}{else}-{/if}</dd>
            <dt>{'Caption before'|i18n( 'design/admin/migration' )}</dt><dd>{if $run.caption_pre}<code class="a4-code">{$run.caption_pre|wash}</code>{else}<span class="a4-bad-text">{'none'|i18n( 'design/admin/migration' )}</span>{/if}</dd>
        </dl>
        {elseif eq( $kind, 'migration' )}
        <dl class="a4-facts is-rows">
            <dt>{'Migration'|i18n( 'design/admin/migration' )}</dt><dd><code class="a4-code">{$migration.id|wash}</code> <span class="a4-meta">{$migration.source|wash}</span></dd>
            {if $migration.description}<dt>{'Description'|i18n( 'design/admin/migration' )}</dt><dd>{$migration.description|wash}</dd>{/if}
            <dt>{'State'|i18n( 'design/admin/migration' )}</dt><dd>{$migration.state|wash}</dd>
            <dt>{'down()'|i18n( 'design/admin/migration' )}</dt><dd>{if $migration.reversible}{'yes'|i18n( 'design/admin/migration' )}{else}<span class="a4-bad-text">{'no'|i18n( 'design/admin/migration' )}</span>{/if}</dd>
        </dl>
        {else}
        <dl class="a4-facts is-rows">
            <dt>{'Caption'|i18n( 'design/admin/migration' )}</dt><dd><code class="a4-code">{$caption.id|wash}</code></dd>
            {if $caption.label}<dt>{'Label'|i18n( 'design/admin/migration' )}</dt><dd>{$caption.label|wash}</dd>{/if}
            {if $caption.created}<dt>{'Taken'|i18n( 'design/admin/migration' )}</dt><dd>{$caption.created|l10n( 'shortdatetime' )}</dd>{/if}
            {if $caption.kind}<dt>{'Kind'|i18n( 'design/admin/migration' )}</dt><dd>{$caption.kind|wash}</dd>{/if}
        </dl>
        {/if}

        {if ne( $kind, 'migration' )}
        <fieldset class="a4-field">
            <legend class="a4-label">{'Parts to restore'|i18n( 'design/admin/migration' )}</legend>
            {foreach $parts as $part}
            <label class="a4-check"><input type="checkbox" name="RestorePart[]" value="{$part|wash}"{if $default_parts|contains( $part )} checked="checked"{/if} />
                <span>{switch match=$part}{case match='database'}{'The database'|i18n( 'design/admin/migration' )}{/case}{case match='storage'}{'The storage files (var/)'|i18n( 'design/admin/migration' )}{/case}{case}{'The settings (settings/override)'|i18n( 'design/admin/migration' )}{/case}{/switch}</span></label>
            {/foreach}
        </fieldset>
        <div class="a4-notice is-warn" role="note"><p>{'Restoring the database takes it back to the time of the caption, sessions included: you may be asked to sign in again afterwards. Everything changed since then is lost; a caption of the state now is taken first.'|i18n( 'design/admin/migration' )}</p></div>
        {/if}

        {if eq( $kind, 'caption' )}
        <label class="a4-check"><input type="checkbox" name="Expert" value="1" /> <span>{'I know what this restores'|i18n( 'design/admin/migration' )}</span></label>
        <div class="a4-field">
            <label class="a4-label" for="migration-confirm-caption">{'Type the id of the caption again'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="migration-confirm-caption" name="ConfirmCaption" autocomplete="off" value="" />
        </div>
        {else}
        <label class="a4-check"><input type="checkbox" name="ConfirmRollback" value="1" /> <span>{'Yes, take it back'|i18n( 'design/admin/migration' )}</span></label>
        {/if}
    </div>
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn a4-btn-danger" name="RollbackButton" value="1"{if $job} disabled="disabled"{/if}>{'Roll back'|i18n( 'design/admin/migration' )}</button>
        <a class="a4-btn" href={'migration/rollback'|ezurl}>{'Cancel'|i18n( 'design/admin/migration' )}</a>
    </div>
</section>
</form>
{else}

<section class="a4-card is-flush a4-section" aria-labelledby="migration-rollback-runs">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-rollback-runs">{'Runs'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'The runs with a caption taken before them.'|i18n( 'design/admin/migration' )}</p></div>
    {if $runs|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-rollback-runs" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Run'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Started'|i18n( 'design/admin/migration' )}</th>
            <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/migration' )}</span></th>
        </tr></thead>
        <tbody>
        {foreach $runs as $run}
        <tr>
            <td><code class="a4-code">{$run.run_id|wash}</code></td>
            <td>{include uri='design:migration/parts/state.tpl' state=$run.state kind=$run.kind}</td>
            <td>{if $run.started_ts}{$run.started_ts|l10n( 'shortdatetime' )}{else}-{/if}</td>
            <td><a class="a4-btn a4-btn-small" href={concat( 'migration/rollback/', $run.run_id )|ezurl}>{'Choose'|i18n( 'design/admin/migration' )}<span class="a4-sr-only"> {$run.run_id|wash}</span></a></td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No run with a caption yet.'|i18n( 'design/admin/migration' )}</p></div>
    {/if}
</section>

<section class="a4-card is-flush a4-section" aria-labelledby="migration-rollback-migrations">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-rollback-migrations">{'Migrations'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'The applied migrations that can take themselves back (they have a down()).'|i18n( 'design/admin/migration' )}</p></div>
    {if $reversible|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-rollback-migrations" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Migration'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Source'|i18n( 'design/admin/migration' )}</th>
            <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/migration' )}</span></th>
        </tr></thead>
        <tbody>
        {foreach $reversible as $m}
        <tr>
            <td><code class="a4-code">{$m.id|wash}</code>{if $m.description}<br /><span class="a4-meta">{$m.description|wash}</span>{/if}</td>
            <td>{$m.source|wash}</td>
            <td><a class="a4-btn a4-btn-small" href={concat( 'migration/rollback/', $m.id|rawurlencode )|ezurl}>{'Choose'|i18n( 'design/admin/migration' )}<span class="a4-sr-only"> {$m.id|wash}</span></a></td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No applied migration has a down().'|i18n( 'design/admin/migration' )}</p></div>
    {/if}
</section>

<details class="a4-fold a4-section">
    <summary>{'Earlier captions (experts)'|i18n( 'design/admin/migration' )}</summary>
    <div class="a4-fold-body">
    {if $captions|count}
    <div class="a4-table-wrap" role="region" aria-label="{'Earlier captions (experts)'|i18n( 'design/admin/migration' )|wash}" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Caption'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Label'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Taken'|i18n( 'design/admin/migration' )}</th>
            <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/migration' )}</span></th>
        </tr></thead>
        <tbody>
        {foreach $captions as $c}
        <tr>
            <td><code class="a4-code">{$c.id|wash}</code></td>
            <td>{$c.label|wash}</td>
            <td>{if $c.created}{$c.created|l10n( 'shortdatetime' )}{else}-{/if}</td>
            <td><a class="a4-btn a4-btn-small" href={concat( 'migration/rollback/', $c.id|rawurlencode )|ezurl}>{'Choose'|i18n( 'design/admin/migration' )}<span class="a4-sr-only"> {$c.id|wash}</span></a></td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <p class="a4-empty">{'No caption yet.'|i18n( 'design/admin/migration' )}</p>
    {/if}
    </div>
</details>
{/if}

</div>
