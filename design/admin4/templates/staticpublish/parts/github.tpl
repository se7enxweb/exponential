{* A target on GitHub: the repository, GitHub Pages and the state of the last Pages build, for the history and the
   preview. Variables: github (expStaticAdmin::github() with its status), target_id. Shows nothing for a target that is
   not a GitHub Pages one on github.com. *}
{if $github.github}
<section class="a4-card a4-section" aria-labelledby="sp-github-state">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-github-state">{'On GitHub'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta"><a href="https://github.com/{$github.owner|wash}/{$github.repo|wash}">github.com/{$github.owner|wash}/{$github.repo|wash}</a> &middot; <a href="{$github.pages_url|wash}">{$github.pages_url|wash}</a></p></div>
    <div class="a4-card-body a4-stack">
    {if and( $github.usable, $github.status )}
        {def $s=$github.status}
        <dl class="a4-facts is-rows">
            <dt>{'Repository'|i18n( 'design/admin/staticpublish' )}</dt>
            <dd>{if $s.repo.exists}{if $s.repo.private}{'private'|i18n( 'design/admin/staticpublish' )}{else}{'public'|i18n( 'design/admin/staticpublish' )}{/if}{else}<span class="a4-warn-text">{'not found'|i18n( 'design/admin/staticpublish' )}</span>{/if}</dd>
            <dt>{'GitHub Pages'|i18n( 'design/admin/staticpublish' )}</dt>
            <dd>{if $s.pages.enabled}{if eq( $s.pages.build_type, 'workflow' )}{'from GitHub Actions'|i18n( 'design/admin/staticpublish' )}{else}{'from the branch %branch'|i18n( 'design/admin/staticpublish',, hash( '%branch', concat( $s.pages.branch, cond( $s.pages.folder, concat( ' /', $s.pages.folder ), '' ) ) ) )|wash}{/if}{if $s.pages.cname} &middot; {$s.pages.cname|wash}{if $s.pages.https_enforced} &middot; HTTPS{/if}{/if}{else}<span class="a4-warn-text">{'off'|i18n( 'design/admin/staticpublish' )}</span>{/if}</dd>
            {if $s.build}
            <dt>{'Last Pages build'|i18n( 'design/admin/staticpublish' )}</dt>
            <dd><span class="a4-status is-{switch match=$s.build.status}{case match='built'}ok{/case}{case match='errored'}bad{/case}{case}warn{/case}{/switch}"><span class="a4-dot" aria-hidden="true"></span>{switch match=$s.build.status}{case match='built'}{'built'|i18n( 'design/admin/staticpublish' )}{/case}{case match='building'}{'building'|i18n( 'design/admin/staticpublish' )}{/case}{case match='queued'}{'queued'|i18n( 'design/admin/staticpublish' )}{/case}{case match='errored'}{'failed'|i18n( 'design/admin/staticpublish' )}{/case}{case match='none'}{'none yet'|i18n( 'design/admin/staticpublish' )}{/case}{case}{'unknown'|i18n( 'design/admin/staticpublish' )}{/case}{/switch}</span>
                {if $s.build.url} <a href="{$s.build.url|wash}">{'Details on GitHub'|i18n( 'design/admin/staticpublish' )}</a>{/if}
                {if $s.build.error} <span class="a4-warn-text">{$s.build.error|wash}</span>{/if}</dd>
            {/if}
        </dl>
        {if $s.warnings|count}<ul class="a4-plain">{foreach $s.warnings as $warning}<li class="a4-warn-text">{$warning|wash}</li>{/foreach}</ul>{/if}
        {if $s.errors|count}<ul class="a4-plain">{foreach $s.errors as $error}<li class="a4-warn-text">{$error|wash}</li>{/foreach}</ul>{/if}
        {undef $s}
    {else}
        <p class="a4-help">{'The state of GitHub Pages is shown with a token or GitHub App as the credential.'|i18n( 'design/admin/staticpublish' )}{if $github.problem} ({$github.problem|wash}){/if}</p>
    {/if}
    </div>
</section>
{/if}
