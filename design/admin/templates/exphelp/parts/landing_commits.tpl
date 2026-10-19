{* exphelp/parts/landing_commits: the latest commits of the Exponential repository on a landing page (block commits;
   expHelpCommitFeed). The server reads the project's Atom feed with a timeout and caches it; the browser loads
   nothing from outside. Every text is escaped; links go to the commits on GitHub (built from the hash). The pages
   use the shared pager (design:navigator/google.tpl) with the view parameter (offset); (merges)/1 shows merge
   commits too. Styles: stylesheets/modules/help.css (.a4-help-commits*); no inline CSS or script. The same file is in
   design/admin and design/admin4.

   Variables: commits (commits, paging, total, fetched, stale, error, history), merges (bool), commit_params (view
   parameters of the pager), audience (the landing page's audience id). *}
{def $help_cctx = 'kernel/exphelp'
     $help_cpage = concat( '/exphelp/audience/', $audience )}
<section class="a4-card a4-help-landing-block a4-help-commits" aria-labelledby="help-landing-commits" id="commits">
    <div class="a4-help-section-head">
        <h2 id="help-landing-commits"><span class="a4-icon a4-bi-git" aria-hidden="true"></span>{'Latest commits to Exponential'|i18n( $help_cctx )}</h2>
        <a class="a4-help-section-more" href="{$commits.history|wash}" rel="noopener noreferrer" target="_blank"><span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span> {'Full history on GitHub'|i18n( $help_cctx )}<span class="a4-sr-only"> {'(opens in a new window)'|i18n( $help_cctx )}</span></a>
    </div>
    <p class="a4-help-commits-intro">{'What changed in the source code most recently: the main branch of se7enxweb/exponential, read from its public feed by this server.'|i18n( $help_cctx )}</p>

    {if $commits.stale}
    <p class="a4-notice is-warn a4-help-commits-note" role="status">
        {if $commits.fetched|gt( 0 )}
        {'GitHub could not be reached just now. These are the commits read on %date.'|i18n( $help_cctx,, hash( '%date', $commits.fetched|l10n( 'shortdatetime' ) ) )|wash}
        {else}
        {'The commits could not be read from GitHub. Open the history on GitHub instead.'|i18n( $help_cctx )}
        {/if}
    </p>
    {/if}

    <p class="a4-help-commits-filter">
        {if $merges}
        <a href={concat( $help_cpage, '#commits' )|ezurl}>{'Hide merge commits'|i18n( $help_cctx )}</a>
        {else}
        <a href={concat( $help_cpage, '/(merges)/1#commits' )|ezurl}>{'Show merge commits too'|i18n( $help_cctx )}</a>
        {/if}
    </p>

    {if $commits.commits|count|gt( 0 )}
    <ol class="a4-help-commits-list">
    {foreach $commits.commits as $commit}
        <li>
            <span class="a4-icon a4-bi-git" aria-hidden="true"></span>
            <div class="a4-help-commits-main">
                <a class="a4-help-commits-title" href="{$commit.url|wash}" rel="noopener noreferrer" target="_blank">{$commit.title|wash}<span class="a4-sr-only"> {'(opens in a new window)'|i18n( $help_cctx )}</span></a>
                <span class="a4-help-commits-meta">{$commit.author|wash}{if $commit.time|gt( 0 )} &middot; <time datetime="{$commit.time|datetime( 'custom', '%Y-%m-%dT%H:%i:%s' )}">{$commit.time|l10n( 'shortdatetime' )}</time>{/if}</span>
                {if and( $commit.message|ne( '' ), $commit.message|ne( $commit.title ) )}
                <details class="a4-help-commits-details">
                    <summary>{'Message'|i18n( $help_cctx )}</summary>
                    <pre class="a4-help-commits-message">{$commit.message|wash}</pre>
                </details>
                {/if}
            </div>
            <code class="a4-code a4-help-commits-hash">{$commit.short|wash}</code>
        </li>
    {/foreach}
    </ol>
    <div class="a4-listfoot a4-help-commits-foot">
        <p class="a4-meta">{'Showing %from–%to of %total commits'|i18n( $help_cctx,, hash( '%from', $commits.paging.from, '%to', $commits.paging.to, '%total', $commits.paging.total ) )|wash}</p>
        {if $commits.paging.page_count|gt( 1 )}
        <div class="a4-pager">
        {include name=navigator
                 uri='design:navigator/google.tpl'
                 page_uri=$help_cpage
                 item_count=$commits.paging.total
                 view_parameters=$commit_params
                 item_limit=$commits.paging.limit}
        </div>
        {/if}
    </div>
    {elseif $commits.stale|not}
    <p class="a4-empty">{'No commits to show yet.'|i18n( $help_cctx )}</p>
    {/if}
</section>
{undef $help_cctx $help_cpage}
