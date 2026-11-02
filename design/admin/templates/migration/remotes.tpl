{* The migration/remotes view: the remotes this installation deploys its migrations to, and Test connection.
   Variables: remotes (expMigrationRemoteAdmin::overview(): id, environment, transport, address, groups, credential
   (kind, name, env), can_run, problems), groups (group => remote ids), settings (expMigrationRemote::settings()), test
   (expMigrationRemoteAdmin::testConnection() after Test connection, else null), tested (its remote id), messages,
   can_edit (migration/remotes), can_deploy, can_run, can_rollback, view. *}
<div class="a4-page a4-setup-migration" id="exp-migration-remotes">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Remotes'|i18n( 'design/admin/migration' )}</h1>
        <p class="a4-pagehead-sub">{'The other installations this one deploys its migrations to: a deploy sends the migrations to the remotes chosen and runs them there, all at once and in sync. A remote never holds a secret: its credential names one in Setup > Secrets.'|i18n( 'design/admin/migration' )}</p>
    </div>
    <div class="a4-pagehead-actions">
        {if $can_edit}<a class="a4-btn a4-btn-primary" href={'migration/remote/new'|ezurl}>{'New remote'|i18n( 'design/admin/migration' )}</a>{/if}
        <a class="a4-btn" href={'exphelp/viewer/help/migrations/remotes-and-deploys.md'|ezurl}>{'Help'|i18n( 'design/admin/migration' )}</a>
    </div>
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}

{foreach $messages as $m}<div class="a4-notice is-bad" role="alert"><p>{$m.text|wash}</p></div>{/foreach}

{if $test}
<section class="a4-card a4-section{if $test.ok|not} is-bad{/if}" aria-labelledby="migration-remote-test" id="migration-remote-test-result">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-remote-test">{'Test connection of %id'|i18n( 'design/admin/migration',, hash( '%id', $tested ) )|wash}</h2>
        <p class="a4-meta">{'%ms ms'|i18n( 'design/admin/migration',, hash( '%ms', $test.duration_ms ) )|wash}</p></div>
    <div class="a4-card-body a4-stack">
        {include uri='design:migration/parts/remote_test.tpl' test=$test}
    </div>
</section>
{/if}

{if $remotes|count}
<section class="a4-card is-flush a4-section" aria-labelledby="migration-remotes-list">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-remotes-list">{'The remotes'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Up to %parallel remotes are worked on at the same time; one phase may take %timeout seconds on each.'|i18n( 'design/admin/migration',, hash( '%parallel', $settings.parallel, '%timeout', $settings.timeout ) )|wash}</p></div>
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-remotes-list" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Remote'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Environment'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Address'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Credential'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Groups'|i18n( 'design/admin/migration' )}</th>
            {if $can_edit}<th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/migration' )}</span></th>{/if}
        </tr></thead>
        <tbody>
        {foreach $remotes as $r}
        <tr>
            <td><code class="a4-code">{$r.id|wash}</code>
                {if $r.problems|count}<br /><span class="a4-badge is-bad">{'settings to fix'|i18n( 'design/admin/migration' )}</span>
                    <ul class="a4-plain a4-meta">{foreach $r.problems as $problem}<li>{$problem|wash}</li>{/foreach}</ul>{/if}
                {if $r.can_run|not}<br /><span class="a4-meta">{'files only (FTP)'|i18n( 'design/admin/migration' )}</span>{/if}</td>
            <td>{$r.environment|wash}</td>
            <td><code class="a4-code">{$r.address|wash}</code></td>
            <td>{switch match=$r.credential.kind}
                {case match='sshkey'}{'SSH key'|i18n( 'design/admin/migration' )}{/case}
                {case match='password'}{'Password'|i18n( 'design/admin/migration' )}{/case}
                {case}{'none (the host\'s SSH agent and keys)'|i18n( 'design/admin/migration' )}{/case}
                {/switch}
                {if $r.credential.name}<br /><code class="a4-code">{$r.credential.name|wash}</code>{elseif $r.credential.env}<br /><code class="a4-code">env:{$r.credential.env|wash}</code>{/if}</td>
            <td>{if $r.groups|count}{foreach $r.groups as $g}<span class="a4-tag">{$g|wash}</span> {/foreach}{else}<span class="a4-muted">-</span>{/if}</td>
            {if $can_edit}
            <td class="a4-actions">
                <form method="post" action={'migration/remotes'|ezurl}>
                    <input type="hidden" name="TestRemoteID" value="{$r.id|wash}" />
                    <button type="submit" class="a4-btn" name="TestButton" value="1">{'Test connection'|i18n( 'design/admin/migration' )}</button>
                </form>
                <a class="a4-btn a4-btn-ghost" href={concat( 'migration/remote/', $r.id )|ezurl}>{'Edit'|i18n( 'design/admin/migration' )}</a>
            </td>
            {/if}
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</section>

{if $groups|count}
<section class="a4-card a4-section" aria-labelledby="migration-remotes-groups">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-remotes-groups">{'Groups'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'A deploy to a group goes to every remote of it.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body">
        <dl class="a4-facts is-rows">
        {foreach $groups as $group => $ids}<dt><span class="a4-tag">{$group|wash}</span></dt><dd>{$ids|implode( ', ' )|wash}</dd>{/foreach}
        </dl>
    </div>
</section>
{/if}
{if $can_deploy}<p class="a4-section"><a class="a4-btn" href={'migration/deploy'|ezurl}>{'Deploy to remotes'|i18n( 'design/admin/migration' )}</a></p>{/if}
{else}
<div class="a4-empty a4-section">
    <p>{'No remote is set up yet.'|i18n( 'design/admin/migration' )}</p>
    {if $can_edit}<p><a class="a4-btn a4-btn-primary" href={'migration/remote/new'|ezurl}>{'New remote'|i18n( 'design/admin/migration' )}</a></p>{/if}
</div>
{/if}

</div>
