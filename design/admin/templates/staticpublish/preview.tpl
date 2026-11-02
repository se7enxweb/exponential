{* The staticpublish/preview view: the build not yet published, before Publish. Variables: target
   (expStaticAdmin::overview()), messages, enabled, has_build, published (a publish exists), changes (files added,
   changed or removed), diff (expStaticAdmin::diffDetails()), limits (expStaticAdmin::limits()), build_job, report (the
   last build's report, or null), running (a job going on, or null), preview_url, github_site, github_file, view,
   github (expStaticAdmin::github() with the state of GitHub Pages). *}
<div class="a4-page a4-setup-publish" id="exp-staticpublish-preview">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Preview and publish %name'|i18n( 'design/admin/staticpublish',, hash( '%name', $target.name ) )|wash}</h1>
        <p class="a4-pagehead-sub">{'Check what the build changes, look at it, then publish it or throw it away.'|i18n( 'design/admin/staticpublish' )}</p>
    </div>
    <div class="a4-pagehead-actions"><a class="a4-btn" href={'exphelp/viewer/help/static-publishing/build-preview-and-publish.md'|ezurl}>{'Help'|i18n( 'design/admin/staticpublish' )}</a></div>
</div>

{include uri='design:staticpublish/parts/tabs.tpl' target=$target view=$view}

{foreach $messages as $m}<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{else}bad{/if}" role="{if eq( $m.kind, 'ok' )}status{else}alert{/if}"><p>{$m.text|wash}</p></div>{/foreach}
{if $running}<div class="a4-notice" role="status"><p><span class="a4-status is-running"><span class="a4-dot" aria-hidden="true"></span><a href={concat( 'staticpublish/log/', $running.id )|ezurl}>{'A job is going on, started by %user'|i18n( 'design/admin/staticpublish',, hash( '%user', $running.user ) )|wash}</a></span></p></div>{/if}
{include uri='design:staticpublish/parts/github.tpl' github=$github}

{if $has_build|not}
<div class="a4-empty a4-section">
    <p>{'There is no build to check yet.'|i18n( 'design/admin/staticpublish' )}</p>
    {if $target.permissions.build}<p><a class="a4-btn a4-btn-primary" href={concat( 'staticpublish/build/', $target.id )|ezurl}>{'Build'|i18n( 'design/admin/staticpublish' )}</a></p>{/if}
</div>
{else}

<section class="a4-card a4-section" aria-labelledby="sp-changes">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-changes">{'What changes'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{if $published}{'Compared with the last publish.'|i18n( 'design/admin/staticpublish' )}{else}{'Nothing was published yet: every file is new.'|i18n( 'design/admin/staticpublish' )}{/if}</p></div>
    <div class="a4-card-body a4-stack">
        <dl class="a4-facts">
            <div><dt>{'Added'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$diff.counts.added|wash}</dd></div>
            <div><dt>{'Changed'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$diff.counts.changed|wash}</dd></div>
            <div><dt>{'Removed'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$diff.counts.removed|wash}</dd></div>
            <div><dt>{'Unchanged'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$diff.counts.unchanged|wash}</dd></div>
            <div><dt>{'Size of the build'|i18n( 'design/admin/staticpublish' )}</dt><dd>{$diff.bytes|si( 'byte' )}</dd></div>
        </dl>
        {if $changes|eq( 0 )}<p class="a4-ok-text">{'The build is the same as the last publish: there is nothing to publish.'|i18n( 'design/admin/staticpublish' )}</p>{/if}
        {foreach array( hash( 'list', 'added', 'title', 'Added'|i18n( 'design/admin/staticpublish' ) ),
                        hash( 'list', 'changed', 'title', 'Changed'|i18n( 'design/admin/staticpublish' ) ),
                        hash( 'list', 'removed', 'title', 'Removed'|i18n( 'design/admin/staticpublish' ) ) ) as $l}
        {if $diff[$l.list]|count}
        <details class="a4-fold"{if and( ne( $l.list, 'added' ), $diff[$l.list]|count|lt( 50 ) )} open="open"{/if}>
            <summary><h3 class="a4-h3">{$l.title|wash}</h3><span class="a4-muted">{$diff.counts[$l.list]|wash}</span></summary>
            <div class="a4-fold-body">
            <div class="a4-table-wrap" role="region" aria-label="{$l.title|wash}" tabindex="0">
            <table class="a4-table">
                <thead><tr>
                    <th scope="col">{'File'|i18n( 'design/admin/staticpublish' )}</th>
                    <th scope="col">{'Kind'|i18n( 'design/admin/staticpublish' )}</th>
                    <th scope="col" class="a4-num">{'Size'|i18n( 'design/admin/staticpublish' )}</th>
                    {if eq( $l.list, 'changed' )}<th scope="col" class="a4-num">{'Before'|i18n( 'design/admin/staticpublish' )}</th>{/if}
                </tr></thead>
                <tbody>
                {foreach $diff[$l.list] as $row}
                <tr>
                    <td><code class="a4-code">{$row.path|wash}</code></td>
                    <td>{switch match=$row.kind}{case match='page'}{'page'|i18n( 'design/admin/staticpublish' )}{/case}{case match='storage'}{'image'|i18n( 'design/admin/staticpublish' )}{/case}{case match='extra'}{'extra'|i18n( 'design/admin/staticpublish' )}{/case}{case}{'file'|i18n( 'design/admin/staticpublish' )}{/case}{/switch}</td>
                    <td class="a4-num">{if eq( $l.list, 'removed' )}{$row.old_size|si( 'byte' )}{else}{$row.size|si( 'byte' )}{/if}</td>
                    {if eq( $l.list, 'changed' )}<td class="a4-num">{$row.old_size|si( 'byte' )}</td>{/if}
                </tr>
                {/foreach}
                </tbody>
            </table>
            </div>
            {if $diff.more[$l.list]}<p class="a4-meta">{'%count more are not listed.'|i18n( 'design/admin/staticpublish',, hash( '%count', $diff.more[$l.list] ) )|wash}</p>{/if}
            </div>
        </details>
        {/if}
        {/foreach}
    </div>
</section>

<section class="a4-card a4-section{if eq( $limits.state, 'bad' )} is-bad{elseif eq( $limits.state, 'warn' )} is-warn{/if}" aria-labelledby="sp-limits">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-limits">{'Size and limits'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'GitHub Pages allows a site of %site and a file of %file.'|i18n( 'design/admin/staticpublish',, hash( '%site', $github_site|si( 'byte' ), '%file', $github_file|si( 'byte' ) ) )|wash}</p></div>
    <div class="a4-card-body a4-stack">
        <p>{'%bytes of %limit (%percent %)'|i18n( 'design/admin/staticpublish',, hash( '%bytes', $limits.bytes|si( 'byte' ), '%limit', $limits.site_limit|si( 'byte' ), '%percent', $limits.percent ) )|wash}</p>
        <div class="a4-meter is-{if eq( $limits.state, 'bad' )}bad{elseif eq( $limits.state, 'warn' )}warn{else}ok{/if}" aria-hidden="true"><span class="a4-pct-{$limits.meter}"></span></div>
        {if $limits.largest.path}<p class="a4-meta">{'The largest file: %path, %size.'|i18n( 'design/admin/staticpublish',, hash( '%path', $limits.largest.path, '%size', $limits.largest.size|si( 'byte' ) ) )|wash}</p>{/if}
        {foreach $limits.too_large as $f}<p class="a4-bad-text">{'%path is %size, more than the host allows a file.'|i18n( 'design/admin/staticpublish',, hash( '%path', $f.path, '%size', $f.size|si( 'byte' ) ) )|wash}</p>{/foreach}
    </div>
</section>

{if $report}
<section class="a4-card a4-section{if $report.errors|count} is-bad{/if}" aria-labelledby="sp-build-report">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-build-report">{'Report of the build'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta"><a href={concat( 'staticpublish/log/', $build_job.id )|ezurl}>{'Build %id by %user'|i18n( 'design/admin/staticpublish',, hash( '%id', $build_job.id, '%user', $build_job.user ) )|wash}</a></p></div>
    <div class="a4-card-body a4-stack">
        {foreach $report.errors as $error}<div class="a4-notice is-bad" role="alert"><p>{$error|wash}</p></div>{/foreach}
        {foreach $report.warnings as $warning}<div class="a4-notice is-warn" role="status"><p>{$warning|wash}</p></div>{/foreach}
        {include uri='design:staticpublish/parts/report.tpl' report=$report}
    </div>
</section>
{/if}

<section class="a4-card a4-section" aria-labelledby="sp-look">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-look">{'Look at the build'|i18n( 'design/admin/staticpublish' )}</h2></div>
    <div class="a4-card-body a4-stack">
        <p>{'Opens the build as the host will serve it, from the target\'s folder. The link works for you for an hour.'|i18n( 'design/admin/staticpublish' )}</p>
        <p><a class="a4-btn" href={$preview_url|ezurl} target="_blank" rel="noopener">{'Open the preview'|i18n( 'design/admin/staticpublish' )}</a></p>
        <p class="a4-help">{'On the server: ./console exp:static:serve %id serves it the same way.'|i18n( 'design/admin/staticpublish',, hash( '%id', $target.id ) )|wash}</p>
    </div>
</section>

<div class="a4-grid is-wide a4-section">
{if $target.permissions.publish}
<form method="post" action={concat( 'staticpublish/preview/', $target.id )|ezurl} class="a4-card">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title">{'Publish'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'Commits the build, tags it and pushes it to %remote. A push is never forced; you can go back to any publish later.'|i18n( 'design/admin/staticpublish',, hash( '%remote', cond( $target.remote, $target.remote, 'the folder only'|i18n( 'design/admin/staticpublish' ) ) ) )|wash}</p></div>
    <div class="a4-card-body a4-stack">
        <div class="a4-field">
            <label class="a4-label" for="sp-message">{'Message'|i18n( 'design/admin/staticpublish' )}</label>
            <input class="a4-input" id="sp-message" name="PublishMessage" maxlength="500" />
            <span class="a4-help">{'Optional. Empty: what was published.'|i18n( 'design/admin/staticpublish' )}</span>
        </div>
        <label class="a4-check"><input type="checkbox" name="ConfirmPublish" value="1" /> <span>{'I have checked the changes'|i18n( 'design/admin/staticpublish' )}</span></label>
    </div>
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn a4-btn-primary" name="PublishButton" value="1"{if or( $running, $changes|eq( 0 ), $enabled|not )} disabled="disabled"{/if}>{'Publish'|i18n( 'design/admin/staticpublish' )}</button>
    </div>
</form>
{/if}
{if and( $target.permissions.build, $changes|gt( 0 ) )}
<form method="post" action={concat( 'staticpublish/preview/', $target.id )|ezurl} class="a4-card">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title">{'Discard'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'Throws this build away: the folder holds the last publish again. The live site does not change.'|i18n( 'design/admin/staticpublish' )}</p></div>
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn a4-btn-outline-danger" name="DiscardButton" value="1"{if $running} disabled="disabled"{/if}>{'Discard the build'|i18n( 'design/admin/staticpublish' )}</button>
    </div>
</form>
{/if}
</div>

{/if}

</div>
