{* The staticpublish/dashboard view: Setup > Static publishing. Variables: enabled (staticcache.ini [StaticPublishSettings]
   Enabled), targets (expStaticAdmin::overview() of each target the user may read: with version, the n of the last tag,
   commit and commit_short, the tag's commit, commit_url, and deploy, the state of the last run; the state of the Pages
   build is on the history, which asks GitHub, not here), can_setup (staticpublish/setup on
   every target: new targets and the switch), messages, problem, site_limit. *}
<div class="a4-page a4-setup-publish" id="exp-staticpublish-dashboard">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Static publishing'|i18n( 'design/admin/staticpublish' )}</h1>
        <p class="a4-pagehead-sub">{'Publish a static copy of a site to GitHub Pages or any git host: build it, check the changes, publish, and go back to an earlier publish when you need to.'|i18n( 'design/admin/staticpublish' )}</p>
    </div>
    <div class="a4-pagehead-actions">
        <a class="a4-btn" href={'exphelp/viewer/help/static-publishing/README.md'|ezurl}>{'Help'|i18n( 'design/admin/staticpublish' )}</a>
        {if $can_setup}<a class="a4-btn a4-btn-primary" href={'staticpublish/target/new'|ezurl}>{'New target'|i18n( 'design/admin/staticpublish' )}</a>{/if}
    </div>
</div>

{foreach $messages as $m}
<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{else}bad{/if}" role="{if eq( $m.kind, 'ok' )}status{else}alert{/if}"><p>{$m.text|wash}</p></div>
{/foreach}
{if $problem}<div class="a4-notice is-bad" role="alert"><p>{'The targets could not be read: %problem'|i18n( 'design/admin/staticpublish',, hash( '%problem', $problem ) )|wash}</p></div>{/if}

{if $enabled|not}
<div class="a4-notice is-warn a4-section" role="status">
    <p class="a4-notice-title">{'Static publishing is switched off'|i18n( 'design/admin/staticpublish' )}</p>
    <p>{'Targets can be made and changed, but nothing is built or published until it is switched on.'|i18n( 'design/admin/staticpublish' )}</p>
    {if $can_setup}
    <form method="post" action={'staticpublish/dashboard'|ezurl} class="a4-actions">
        <input type="hidden" name="SwitchTo" value="on" />
        <button type="submit" class="a4-btn a4-btn-primary" name="SwitchButton" value="1">{'Switch on'|i18n( 'design/admin/staticpublish' )}</button>
    </form>
    {/if}
</div>
{/if}

{if $targets|count|eq( 0 )}
<div class="a4-empty a4-section">
    <p><strong>{'No target yet.'|i18n( 'design/admin/staticpublish' )}</strong> {'A target is one site published to one place, for example your public site on GitHub Pages.'|i18n( 'design/admin/staticpublish' )}</p>
    <p>{if $can_setup}<a class="a4-btn a4-btn-primary" href={'staticpublish/target/new'|ezurl}>{'Make a target'|i18n( 'design/admin/staticpublish' )}</a>{/if}
       <a class="a4-btn" href={'exphelp/viewer/guides/host-static-sites-on-github-pages.md'|ezurl}>{'Read the tutorial'|i18n( 'design/admin/staticpublish' )}</a></p>
</div>
{else}
<div class="a4-grid is-wide a4-section">
{foreach $targets as $t}
<section class="a4-card{if $t.errors|count} is-attention{/if}" aria-labelledby="sp-target-{$t.id|wash}">
    <div class="a4-card-head">
        <h2 class="a4-h3 a4-card-title" id="sp-target-{$t.id|wash}">{$t.name|wash}</h2>
        <p class="a4-meta"><code class="a4-code">{$t.id|wash}</code> &middot; {if eq( $t.kind, 'github-pages' )}{'GitHub Pages'|i18n( 'design/admin/staticpublish' )}{else}{'git host'|i18n( 'design/admin/staticpublish' )}{/if}</p>
    </div>
    <div class="a4-card-body a4-stack">
        {if $t.job}<p><span class="a4-status is-running"><span class="a4-dot" aria-hidden="true"></span><a href={concat( 'staticpublish/log/', $t.job.id )|ezurl}>{'A job is going on, started by %user'|i18n( 'design/admin/staticpublish',, hash( '%user', $t.job.user ) )|wash}</a></span></p>{/if}
        {foreach $t.errors as $error}<p class="a4-bad-text">{$error|wash}</p>{/foreach}
        <dl class="a4-facts">
            <div><dt>{'Siteaccesses'|i18n( 'design/admin/staticpublish' )}</dt><dd>{include uri='design:staticpublish/parts/siteaccesses.tpl' members=$t.siteaccesses}</dd></div>
            <div><dt>{'Version'|i18n( 'design/admin/staticpublish' )}</dt>
                <dd>{if $t.version}<strong>{$t.version|wash}</strong> <code class="a4-code">{$t.last_tag|wash}</code>{else}<span class="a4-muted">{'none yet'|i18n( 'design/admin/staticpublish' )}</span>{/if}</dd></div>
            <div><dt>{'Commit'|i18n( 'design/admin/staticpublish' )}</dt>
                <dd>{if $t.commit_short}{if $t.commit_url}<a href="{concat( $t.commit_url, $t.commit )|wash}" rel="noopener noreferrer"><code class="a4-code">{$t.commit_short|wash}</code></a>{else}<code class="a4-code" title="{$t.commit|wash}">{$t.commit_short|wash}</code>{/if}{else}-{/if}</dd></div>
            <div><dt>{'Last publish'|i18n( 'design/admin/staticpublish' )}</dt>
                <dd>{include uri='design:staticpublish/parts/deploy_state.tpl' deploy=$t.deploy}{if $t.deploy.run_id} <a href={concat( 'staticpublish/log/', $t.deploy.run_id )|ezurl}>{$t.deploy.time|l10n( 'shortdatetime' )}</a>{/if}</dd></div>
            <div><dt>{'Stale pages'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if $t.stale}<span class="a4-badge is-warn">{$t.stale|wash}</span>{else}0{/if}</dd></div>
            <div><dt>{'Build to check'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if $t.pending_build}<a href={concat( 'staticpublish/preview/', $t.id )|ezurl}>{'yes, not published yet'|i18n( 'design/admin/staticpublish' )}</a>{else}{'no'|i18n( 'design/admin/staticpublish' )}{/if}</dd></div>
            <div><dt>{'Size'|i18n( 'design/admin/staticpublish' )}</dt><dd>{if $t.bytes}{$t.bytes|si( 'byte' )} <span class="a4-meta">{'%percent % of the limit'|i18n( 'design/admin/staticpublish',, hash( '%percent', $t.limit_percent ) )|wash}</span>{else}-{/if}</dd></div>
        </dl>
        {if $t.public_url}<p class="a4-meta">{'Public address'|i18n( 'design/admin/staticpublish' )}: <a href="{$t.public_url|wash}" rel="noopener noreferrer">{$t.public_url|wash}</a></p>{/if}
    </div>
    <div class="a4-card-foot a4-actions">
        {if $t.permissions.build}<a class="a4-btn{if $t.pending_build|not} a4-btn-primary{/if}" href={concat( 'staticpublish/build/', $t.id )|ezurl}>{'Build'|i18n( 'design/admin/staticpublish' )}</a>{/if}
        <a class="a4-btn{if $t.pending_build} a4-btn-primary{/if}" href={concat( 'staticpublish/preview/', $t.id )|ezurl}>{'Preview'|i18n( 'design/admin/staticpublish' )}</a>
        <a class="a4-btn" href={concat( 'staticpublish/history/', $t.id )|ezurl}>{'History'|i18n( 'design/admin/staticpublish' )}</a>
        {if $t.permissions.setup}<a class="a4-btn a4-btn-ghost" href={concat( 'staticpublish/target/', $t.id )|ezurl}>{'Settings'|i18n( 'design/admin/staticpublish' )}</a>{/if}
    </div>
</section>
{/foreach}
</div>
{/if}

{if and( $enabled, $can_setup )}
<form method="post" action={'staticpublish/dashboard'|ezurl} class="a4-actions a4-section">
    <input type="hidden" name="SwitchTo" value="off" />
    <span class="a4-meta">{'Static publishing is switched on.'|i18n( 'design/admin/staticpublish' )}</span>
    <button type="submit" class="a4-btn a4-btn-ghost" name="SwitchButton" value="1">{'Switch off'|i18n( 'design/admin/staticpublish' )}</button>
</form>
{/if}

</div>
