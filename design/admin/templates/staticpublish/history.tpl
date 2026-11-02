{* The staticpublish/history view: the publishes and rollbacks, the tags and commits, and the rollback. Variables:
   target (expStaticAdmin::overview()), messages, runs (exp_static_run rows through expStaticAdmin::runView(), newest
   first, with version and commit_short), tags (name, number, commit, time,
   subject; newest first), commits (commit, time, author, subject), commit_url (the host's address of a commit, '' when
   unknown), chosen (the tag chosen in a refused rollback), running, view, github (expStaticAdmin::github() with the
   state of GitHub Pages). *}
<div class="a4-page a4-setup-publish" id="exp-staticpublish-history">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'History of %name'|i18n( 'design/admin/staticpublish',, hash( '%name', $target.name ) )|wash}</h1>
        <p class="a4-pagehead-sub">{'Every publish is a commit with a tag static/%id/<n>. Going back makes a new commit with the files of an earlier publish: the history stays, no tag moves and nothing is forced.'|i18n( 'design/admin/staticpublish',, hash( '%id', $target.id ) )|wash}</p>
    </div>
    <div class="a4-pagehead-actions"><a class="a4-btn" href={'exphelp/viewer/help/static-publishing/history-and-rollback.md'|ezurl}>{'Help'|i18n( 'design/admin/staticpublish' )}</a></div>
</div>

{include uri='design:staticpublish/parts/tabs.tpl' target=$target view=$view}

{foreach $messages as $m}<div class="a4-notice is-bad" role="alert"><p>{$m.text|wash}</p></div>{/foreach}
{if $running}<div class="a4-notice" role="status"><p><span class="a4-status is-running"><span class="a4-dot" aria-hidden="true"></span><a href={concat( 'staticpublish/log/', $running.id )|ezurl}>{'A job is going on, started by %user'|i18n( 'design/admin/staticpublish',, hash( '%user', $running.user ) )|wash}</a></span></p></div>{/if}
{include uri='design:staticpublish/parts/github.tpl' github=$github}

<section class="a4-card is-flush a4-section" aria-labelledby="sp-runs">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-runs">{'Publishes'|i18n( 'design/admin/staticpublish' )}</h2></div>
    {if $runs|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="sp-runs" tabindex="0">
    <table class="a4-table a4-setup-publish-runs">
        <thead><tr>
            <th scope="col">{'When'|i18n( 'design/admin/staticpublish' )}</th>
            <th scope="col">{'What'|i18n( 'design/admin/staticpublish' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/staticpublish' )}</th>
            <th scope="col" class="a4-num">{'Version'|i18n( 'design/admin/staticpublish' )}</th>
            <th scope="col">{'Commit'|i18n( 'design/admin/staticpublish' )}</th>
            <th scope="col">{'Push'|i18n( 'design/admin/staticpublish' )}</th>
            <th scope="col">{'By'|i18n( 'design/admin/staticpublish' )}</th>
            <th scope="col" class="a4-num">{'Changes'|i18n( 'design/admin/staticpublish' )}</th>
        </tr></thead>
        <tbody>
        {foreach $runs as $run}
        <tr>
            <td data-label="{'When'|i18n( 'design/admin/staticpublish' )|wash}"><a href={concat( 'staticpublish/log/', $run.run_id )|ezurl}>{$run.started_at|l10n( 'shortdatetime' )}</a></td>
            <td data-label="{'What'|i18n( 'design/admin/staticpublish' )|wash}">{if eq( $run.kind, 'rollback' )}{'rollback'|i18n( 'design/admin/staticpublish' )}{else}{'publish'|i18n( 'design/admin/staticpublish' )}{/if}</td>
            <td data-label="{'State'|i18n( 'design/admin/staticpublish' )|wash}">{include uri='design:staticpublish/parts/run_state.tpl' run=$run state_only=true()}</td>
            <td class="a4-num" data-label="{'Version'|i18n( 'design/admin/staticpublish' )|wash}">{if $run.version}<span title="{$run.tag_name|wash}">{$run.version|wash}</span>{else}&mdash;{/if}</td>
            <td data-label="{'Commit'|i18n( 'design/admin/staticpublish' )|wash}">{if $run.commit_short}{if $commit_url}<a href="{concat( $commit_url, $run.commit_id )|wash}" rel="noopener noreferrer"><code class="a4-code">{$run.commit_short|wash}</code></a>{else}<code class="a4-code" title="{$run.commit_id|wash}">{$run.commit_short|wash}</code>{/if}{else}&mdash;{/if}</td>
            <td data-label="{'Push'|i18n( 'design/admin/staticpublish' )|wash}">{include uri='design:staticpublish/parts/push_state.tpl' run=$run dash=true()}</td>
            <td data-label="{'By'|i18n( 'design/admin/staticpublish' )|wash}">{$run.run_by|wash}</td>
            <td class="a4-num" data-label="{'Changes'|i18n( 'design/admin/staticpublish' )|wash}">{'+%added ~%changed -%removed'|i18n( 'design/admin/staticpublish',, hash( '%added', $run.added, '%changed', $run.changed, '%removed', $run.removed ) )|wash}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-muted">{'Nothing was published yet.'|i18n( 'design/admin/staticpublish' )}</p></div>
    {/if}
</section>

{if and( $target.permissions.publish, $tags|count|gt( 1 ) )}
<form method="post" action={concat( 'staticpublish/history/', $target.id )|ezurl} class="a4-card a4-section is-attention" aria-labelledby="sp-rollback">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-rollback">{'Go back to an earlier publish'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'For experts. The public site changes as soon as the push is through.'|i18n( 'design/admin/staticpublish' )}</p></div>
    <div class="a4-card-body a4-stack">
        <div class="a4-field">
            <label class="a4-label" for="sp-rollback-tag">{'Publish to go back to'|i18n( 'design/admin/staticpublish' )}</label>
            <select id="sp-rollback-tag" name="RollbackTag">
            {foreach $tags as $i => $tag}{if $i|gt( 0 )}
                <option value="{$tag.name|wash}"{if eq( $tag.name, $chosen )} selected="selected"{/if}>{$tag.name|wash} &mdash; {$tag.time|l10n( 'shortdatetime' )}</option>
            {/if}{/foreach}
            </select>
        </div>
        <label class="a4-check"><input type="checkbox" name="RollbackExpert" value="1" /> <span><strong>{'I know what this changes'|i18n( 'design/admin/staticpublish' )}</strong><span class="a4-help">{'The site goes back to the files of that publish; a build not published yet must be published or thrown away first.'|i18n( 'design/admin/staticpublish' )}</span></span></label>
        <div class="a4-field">
            <label class="a4-label" for="sp-rollback-typed">{'Type the name of the tag again'|i18n( 'design/admin/staticpublish' )}</label>
            <input class="a4-input" id="sp-rollback-typed" name="RollbackTyped" autocomplete="off" spellcheck="false" />
        </div>
    </div>
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn a4-btn-danger" name="RollbackButton" value="1"{if $running} disabled="disabled"{/if}>{'Roll back'|i18n( 'design/admin/staticpublish' )}</button>
    </div>
</form>
{/if}

<div class="a4-grid is-wide a4-section">
<section class="a4-card is-flush" aria-labelledby="sp-tags">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-tags">{'Tags'|i18n( 'design/admin/staticpublish' )}</h2></div>
    {if $tags|count}
    <ol class="a4-timeline a4-card-body">
    {foreach $tags as $tag}
        <li class="is-ok"><time class="a4-timeline-time" datetime="{$tag.time|datetime( 'custom', '%Y-%m-%dT%H:%i:%s' )}">{$tag.time|l10n( 'shortdatetime' )}</time>
            <div class="a4-timeline-body"><code class="a4-code">{$tag.name|wash}</code>
                <span class="a4-timeline-meta">{if $commit_url}<a href="{concat( $commit_url, $tag.commit )|wash}" rel="noopener noreferrer">{$tag.commit|shorten( 10, '' )|wash}</a>{else}{$tag.commit|shorten( 10, '' )|wash}{/if} &middot; {$tag.subject|wash}</span></div></li>
    {/foreach}
    </ol>
    {else}
    <div class="a4-card-body"><p class="a4-muted">{'No tag yet.'|i18n( 'design/admin/staticpublish' )}</p></div>
    {/if}
</section>
<section class="a4-card is-flush" aria-labelledby="sp-commits">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-commits">{'Commits'|i18n( 'design/admin/staticpublish' )}</h2></div>
    {if $commits|count}
    <ol class="a4-timeline a4-card-body">
    {foreach $commits as $c}
        <li class="is-info"><time class="a4-timeline-time" datetime="{$c.time|datetime( 'custom', '%Y-%m-%dT%H:%i:%s' )}">{$c.time|l10n( 'shortdatetime' )}</time>
            <div class="a4-timeline-body">{$c.subject|wash}
                <span class="a4-timeline-meta">{if $commit_url}<a href="{concat( $commit_url, $c.commit )|wash}" rel="noopener noreferrer">{$c.commit|shorten( 10, '' )|wash}</a>{else}{$c.commit|shorten( 10, '' )|wash}{/if} &middot; {$c.author|wash}</span></div></li>
    {/foreach}
    </ol>
    {else}
    <div class="a4-card-body"><p class="a4-muted">{'No commit yet.'|i18n( 'design/admin/staticpublish' )}</p></div>
    {/if}
</section>
</div>

</div>
