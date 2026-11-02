{* The migration/deploy view: the form that deploys the migrations to remotes, and the history of the deploys.
   Variables: remotes (expMigrationRemoteAdmin::overview()), groups (group => remote ids), history
   (expMigrationRemoteDeploy::history(): deploy_id, mode, state, in_sync, started_at, finished_at, user, remotes (id =>
   state), stages, next_stage), form (remotes, groups, mode, staged, only: what was entered), messages, job (the job
   going on, or null), login, can_deploy (migration/deploy), can_edit (migration/remotes), can_run, can_rollback, view. *}
<div class="a4-page a4-setup-migration" id="exp-migration-deploy">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Deploys'|i18n( 'design/admin/migration' )}</h1>
        <p class="a4-pagehead-sub">{'A deploy sends this installation\'s migrations to the remotes chosen and runs them there as %user, all at once and in sync: every remote is checked first, and when one fails, every remote that applied is taken back. It goes on in the background, and its page shows every remote as it goes.'|i18n( 'design/admin/migration',, hash( '%user', $login ) )|wash}</p>
    </div>
    <div class="a4-pagehead-actions"><a class="a4-btn" href={'exphelp/viewer/help/migrations/remotes-and-deploys.md'|ezurl}>{'Help'|i18n( 'design/admin/migration' )}</a></div>
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}

{if $job}
<div class="a4-notice" role="status">
    <p class="a4-notice-title"><span class="a4-status is-running"><span class="a4-dot" aria-hidden="true"></span>{'A run is going on'|i18n( 'design/admin/migration' )}</span></p>
    <p>{'Started by %user at %time.'|i18n( 'design/admin/migration',, hash( '%user', $job.user, '%time', $job.started|l10n( 'shortdatetime' ) ) )|wash}
       <a href={cond( eq( $job.kind, 'deploy' ), concat( 'migration/deployment/', $job.target ), concat( 'migration/log/', $job.id ) )|ezurl}>{'Follow it'|i18n( 'design/admin/migration' )}</a></p>
</div>
{/if}

{foreach $messages as $m}<div class="a4-notice is-bad" role="alert"><p>{$m.text|wash}</p></div>{/foreach}

{if and( $can_deploy, $remotes|count )}
<form method="post" action={'migration/deploy'|ezurl} class="a4-form">
<section class="a4-card is-flush a4-section" aria-labelledby="migration-deploy-remotes">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-deploy-remotes">{'1. The remotes'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Tick remotes, a group, or both.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-deploy-remotes" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col"><span class="a4-sr-only">{'Deploy to it'|i18n( 'design/admin/migration' )}</span></th>
            <th scope="col">{'Remote'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Environment'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Address'|i18n( 'design/admin/migration' )}</th>
        </tr></thead>
        <tbody>
        {foreach $remotes as $i => $r}
        <tr>
            <td><input type="checkbox" id="deploy-remote-{$i}" name="DeployRemote[]" value="{$r.id|wash}"{if $form.remotes|contains( $r.id )} checked="checked"{/if}{if $r.problems|count} disabled="disabled"{/if} /></td>
            <td><label for="deploy-remote-{$i}"><code class="a4-code">{$r.id|wash}</code></label>
                {if $r.problems|count}<br /><span class="a4-badge is-bad">{'settings to fix'|i18n( 'design/admin/migration' )}</span>{/if}
                {if $r.can_run|not}<br /><span class="a4-meta">{'files only (FTP)'|i18n( 'design/admin/migration' )}</span>{/if}</td>
            <td>{$r.environment|wash}</td>
            <td><code class="a4-code">{$r.address|wash}</code></td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {if $groups|count}
    <div class="a4-card-body">
        <fieldset class="a4-field">
            <legend>{'Groups'|i18n( 'design/admin/migration' )}</legend>
            <ul class="a4-radios">
            {foreach $groups as $group => $ids}
                <li><label class="a4-check"><input type="checkbox" name="DeployGroup[]" value="{$group|wash}"{if $form.groups|contains( $group )} checked="checked"{/if} /> <span><strong>{$group|wash}</strong> <span class="a4-help">{$ids|implode( ', ' )|wash}</span></span></label></li>
            {/foreach}
            </ul>
        </fieldset>
    </div>
    {/if}
</section>

<section class="a4-card a4-section" aria-labelledby="migration-deploy-how">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-deploy-how">{'2. How'|i18n( 'design/admin/migration' )}</h2></div>
    <div class="a4-card-body a4-stack">
        <fieldset class="a4-field">
            <legend>{'Deploy'|i18n( 'design/admin/migration' )}</legend>
            <ul class="a4-radios">
                <li><label><input type="radio" name="DeployMode" value="apply"{if eq( $form.mode, 'apply' )} checked="checked"{/if} /> <span><strong>{'Send and run'|i18n( 'design/admin/migration' )}</strong> <span class="a4-help">{'The migrations are sent, verified and run on every remote with one shared run id.'|i18n( 'design/admin/migration' )}</span></span></label></li>
                <li><label><input type="radio" name="DeployMode" value="transfer-only"{if eq( $form.mode, 'transfer-only' )} checked="checked"{/if} /> <span><strong>{'Transfer only'|i18n( 'design/admin/migration' )}</strong> <span class="a4-help">{'The files are sent and verified, nothing is run (the only way for an FTP remote).'|i18n( 'design/admin/migration' )}</span></span></label></li>
            </ul>
        </fieldset>
        <label class="a4-check"><input type="checkbox" name="DeployStaged" value="1"{if $form.staged} checked="checked"{/if} />
            <span><strong>{'Staged'|i18n( 'design/admin/migration' )}</strong><span class="a4-help">{'Stage by stage in the order of the environments (dev, stage, beta, production ...): the first stage is deployed, then a gate waits until someone approves the next one. All or nothing within each stage.'|i18n( 'design/admin/migration' )}</span></span></label>
        <div class="a4-field a4-field-wide">
            <label class="a4-label" for="deploy-only">{'Only these migrations'|i18n( 'design/admin/migration' )}</label>
            <input class="a4-input" id="deploy-only" name="DeployOnly" value="{$form.only|wash}" maxlength="2000" autocomplete="off" spellcheck="false" />
            <span class="a4-help">{'Optional: migration ids, separated by commas. Empty: every migration of the site and its extensions.'|i18n( 'design/admin/migration' )}</span>
        </div>
    </div>
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn" name="PreflightButton" value="1"{if $job} disabled="disabled"{/if}>{'Preflight only'|i18n( 'design/admin/migration' )}</button>
        <label class="a4-check"><input type="checkbox" name="ConfirmDeploy" value="1" /> <span>{'Yes, deploy now'|i18n( 'design/admin/migration' )}</span></label>
        <button type="submit" class="a4-btn a4-btn-primary" name="DeployButton" value="1"{if $job} disabled="disabled"{/if}>{'Deploy'|i18n( 'design/admin/migration' )}</button>
    </div>
</section>
</form>
{elseif $remotes|count|not}
<div class="a4-empty a4-section">
    <p>{'No remote is set up yet.'|i18n( 'design/admin/migration' )}</p>
    {if $can_edit}<p><a class="a4-btn a4-btn-primary" href={'migration/remote/new'|ezurl}>{'New remote'|i18n( 'design/admin/migration' )}</a></p>{/if}
</div>
{/if}

<section class="a4-card is-flush a4-section" aria-labelledby="migration-deploy-history">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-deploy-history">{'History'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'The deploys that sent or ran something, the newest first (a preflight keeps no report).'|i18n( 'design/admin/migration' )}</p></div>
    {if $history|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-deploy-history" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Deploy'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Remotes'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'By'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Started'|i18n( 'design/admin/migration' )}</th>
        </tr></thead>
        <tbody>
        {foreach $history as $h}
        <tr>
            <td><a href={concat( 'migration/deployment/', $h.deploy_id )|ezurl}><code class="a4-code">{$h.deploy_id|wash}</code></a>
                {if eq( $h.mode, 'transfer-only' )}<br /><span class="a4-meta">{'transfer only'|i18n( 'design/admin/migration' )}</span>{/if}
                {if $h.stages|gt( 0 )}<br /><span class="a4-meta">{'staged, %stages stages'|i18n( 'design/admin/migration',, hash( '%stages', $h.stages ) )|wash}</span>{/if}</td>
            <td>{include uri='design:migration/parts/deploy_state.tpl' state=$h.state kind=$h.kind}
                {if $h.out_of_sync}<br /><span class="a4-badge is-bad">{'out of sync'|i18n( 'design/admin/migration' )}</span>{/if}</td>
            <td>{foreach $h.remotes as $rid => $rstate}<span class="a4-tag">{$rid|wash}: {include uri='design:migration/parts/deploy_state_text.tpl' state=$rstate}</span> {/foreach}</td>
            <td>{if $h.user}{$h.user|wash}{else}<span class="a4-muted">-</span>{/if}</td>
            <td>{if $h.started_ts}{$h.started_ts|l10n( 'shortdatetime' )}{else}-{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-muted">{'No deploy yet.'|i18n( 'design/admin/migration' )}</p></div>
    {/if}
</section>

</div>
