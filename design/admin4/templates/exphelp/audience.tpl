{* exphelp/audience: the landing page of one audience (doc/features/6.0/help-system.md, "Landing pages"). The blocks
   and their order come from exphelp.ini [LandingSettings] Blocks[] or [Landing_<audience>] Blocks[]: hero (name,
   summary, a search box limited to the audience, buttons), start (the reading path), boxes (info boxes), categories
   (a search button per category), key (key pages), latest (recently updated), related (related audiences), commits
   (parts/landing_commits.tpl) and support (parts/support.tpl). Styles: stylesheets/modules/help.css
   (.a4-help-landing*); no inline CSS or script. The same file is in design/admin and design/admin4.

   Variables (kernel/private/classes/views/exphelp/audience.php): help_landing (id, name, icon, count, summary,
   blocks, start, reading, boxes, key, categories, latest, related, search_url), help_audiences, help_commits,
   help_commit_merges, help_commit_params, help_support, help_search_url, help_i18n_json. *}
{def $help_ctx = 'kernel/exphelp'
     $help_l = $help_landing
     $help_i18n = cond( is_set( $help_i18n_json ), $help_i18n_json, '' )
     $help_search_action = cond( is_set( $help_search_url ), $help_search_url, '/exphelp/search' )}
<div class="a4-page a4-help a4-help-landing is-{$help_l.id|wash}" data-exphelp{if $help_i18n|ne( '' )} data-exphelp-i18n="{$help_i18n|wash}"{/if}>
{foreach $help_l.blocks as $help_block}
{switch match=$help_block}

{case match='hero'}
    <section class="a4-help-landing-hero" aria-labelledby="help-landing-title">
        <span class="a4-help-tile is-{$help_l.id|wash}" aria-hidden="true"><span class="a4-icon a4-bi-{$help_l.icon|wash}"></span></span>
        <div class="a4-help-landing-intro">
            <p class="a4-help-hero-pre"><a href={'/exphelp/dashboard'|ezurl}>{'Help center'|i18n( $help_ctx )}</a></p>
            <h1 id="help-landing-title">{'Help for %audience'|i18n( $help_ctx,, hash( '%audience', $help_l.name ) )|wash}</h1>
            {if $help_l.summary|ne( '' )}<p class="a4-help-landing-summary">{$help_l.summary|wash}</p>{/if}
            <p class="a4-help-landing-count">{'%count pages'|i18n( $help_ctx,, hash( '%count', $help_l.count ) )|wash}</p>
        </div>
        <form class="a4-help-search a4-help-landing-search" action={$help_search_action|ezurl} method="get" role="search">
            <input type="hidden" name="audience" value="{$help_l.id|wash}" />
            <div class="a4-help-search-field">
                <span class="a4-icon a4-bi-search" aria-hidden="true"></span>
                <label class="a4-sr-only" for="help-landing-q">{'Search the help for %audience'|i18n( $help_ctx,, hash( '%audience', $help_l.name ) )|wash}</label>
                <input type="search" id="help-landing-q" name="q" value="" placeholder="{'Search the help for %audience'|i18n( $help_ctx,, hash( '%audience', $help_l.name ) )|wash}" autocomplete="off" spellcheck="false" />
                <button type="submit" class="a4-btn a4-btn-primary">{'Search'|i18n( $help_ctx )}</button>
            </div>
        </form>
        <div class="a4-help-landing-actions">
            {if $help_l.start}
            <a class="a4-btn a4-btn-primary" href={concat( '/exphelp/viewer/', $help_l.start.id )|ezurl}><span class="a4-icon a4-bi-signpost-split" aria-hidden="true"></span>{'Start here'|i18n( $help_ctx )}</a>
            {/if}
            <a class="a4-btn" href={$help_l.search_url|ezurl}><span class="a4-icon a4-bi-list-ul" aria-hidden="true"></span>{'All pages for %audience'|i18n( $help_ctx,, hash( '%audience', $help_l.name ) )|wash}</a>
            <a class="a4-btn" href={'/exphelp/dashboard'|ezurl}><span class="a4-icon a4-bi-arrow-left" aria-hidden="true"></span>{'All audiences'|i18n( $help_ctx )}</a>
        </div>
    </section>
{/case}

{case match='start'}
    {if $help_l.reading|count|gt( 0 )}
    <section class="a4-help-landing-block" aria-labelledby="help-landing-start">
        <div class="a4-help-section-head">
            <h2 id="help-landing-start"><span class="a4-icon a4-bi-signpost-split" aria-hidden="true"></span>{'Start here'|i18n( $help_ctx )}</h2>
            <p>{'Read these pages in this order.'|i18n( $help_ctx )}</p>
        </div>
        <ol class="a4-help-grow-steps a4-help-landing-path">
        {foreach $help_l.reading as $help_n => $doc}
            <li><a href={concat( '/exphelp/viewer/', $doc.id )|ezurl}><span class="a4-help-grow-num" aria-hidden="true">{$help_n|inc}</span><span class="a4-help-grow-step"><strong>{$doc.title|wash}</strong> {if $doc.summary|ne( '' )}{$doc.summary|shorten( 110 )|wash}{/if}</span></a></li>
        {/foreach}
        </ol>
    </section>
    {/if}
{/case}

{case match='boxes'}
    {if $help_l.boxes|count|gt( 0 )}
    <section class="a4-help-landing-block" aria-labelledby="help-landing-boxes">
        <div class="a4-help-section-head">
            <h2 id="help-landing-boxes"><span class="a4-icon a4-bi-stars" aria-hidden="true"></span>{'Good to know'|i18n( $help_ctx )}</h2>
        </div>
        <ul class="a4-help-landing-boxes">
        {foreach $help_l.boxes as $doc}
            <li class="a4-card a4-help-landing-box">
                <h3 class="a4-card-title"><a href={concat( '/exphelp/viewer/', $doc.id )|ezurl}>{$doc.title|wash}</a></h3>
                {if $doc.summary|ne( '' )}<p>{$doc.summary|shorten( 220 )|wash}</p>{/if}
            </li>
        {/foreach}
        </ul>
    </section>
    {/if}
{/case}

{case match='categories'}
    {if $help_l.categories|count|gt( 0 )}
    <section class="a4-help-landing-block" aria-labelledby="help-landing-categories">
        <div class="a4-help-section-head">
            <h2 id="help-landing-categories"><span class="a4-icon a4-bi-search" aria-hidden="true"></span>{'Search by topic'|i18n( $help_ctx )}</h2>
            <p>{'Each button lists the pages of one topic for %audience.'|i18n( $help_ctx,, hash( '%audience', $help_l.name ) )|wash}</p>
        </div>
        <ul class="a4-help-links">
        {foreach $help_l.categories as $help_category}
            <li><a href={$help_category.url|ezurl}><span class="a4-icon a4-bi-{cond( $help_category.icon|ne( '' ), $help_category.icon, 'journal-text' )|wash}" aria-hidden="true"></span>{$help_category.name|wash}<span class="a4-help-links-count">{$help_category.count|wash}</span></a></li>
        {/foreach}
        </ul>
    </section>
    {/if}
{/case}

{case match='key'}
    {if $help_l.key|count|gt( 0 )}
    <section class="a4-help-landing-block" aria-labelledby="help-landing-key">
        <div class="a4-help-section-head">
            <h2 id="help-landing-key"><span class="a4-icon a4-bi-bookmark" aria-hidden="true"></span>{'Key pages'|i18n( $help_ctx )}</h2>
            <a class="a4-help-section-more" href={$help_l.search_url|ezurl}><span class="a4-icon a4-bi-list-ul" aria-hidden="true"></span> {'All pages for %audience'|i18n( $help_ctx,, hash( '%audience', $help_l.name ) )|wash}</a>
        </div>
        <ul class="a4-help-landing-list">
        {foreach $help_l.key as $doc}
            <li><a href={concat( '/exphelp/viewer/', $doc.id )|ezurl}><span class="a4-icon a4-bi-chevron-right is-small" aria-hidden="true"></span><span class="a4-help-landing-list-text"><strong>{$doc.title|wash}</strong>{if $doc.summary|ne( '' )} <span>{$doc.summary|shorten( 140 )|wash}</span>{/if}</span></a></li>
        {/foreach}
        </ul>
    </section>
    {/if}
{/case}

{case match='latest'}
    {if $help_l.latest|count|gt( 0 )}
    <section class="a4-card a4-help-landing-block" aria-labelledby="help-landing-latest">
        <div class="a4-help-section-head">
            <h2 id="help-landing-latest"><span class="a4-icon a4-bi-clock-history" aria-hidden="true"></span>{'Recently updated'|i18n( $help_ctx )}</h2>
        </div>
        <ul class="a4-help-recent">
        {foreach $help_l.latest as $change}
            <li>
                <span class="a4-icon a4-bi-file-earmark" aria-hidden="true"></span>
                <span class="a4-help-recent-main">
                    <a href={concat( '/exphelp/viewer/', $change.id )|ezurl}>{$change.title|wash}</a>
                    {if and( is_set( $change.subject ), $change.subject|ne( '' ) )}<span class="a4-help-recent-path">{$change.subject|shorten( 90 )|wash}</span>{/if}
                </span>
                {if and( is_set( $change.time ), $change.time|gt( 0 ) )}<time datetime="{$change.time|datetime( 'custom', '%Y-%m-%dT%H:%i:%s' )}">{$change.time|l10n( 'shortdate' )}</time>{/if}
            </li>
        {/foreach}
        </ul>
    </section>
    {/if}
{/case}

{case match='related'}
    {if $help_l.related|count|gt( 0 )}
    <section class="a4-help-landing-block" aria-labelledby="help-landing-related">
        <div class="a4-help-section-head">
            <h2 id="help-landing-related"><span class="a4-icon a4-bi-people" aria-hidden="true"></span>{'Related audiences'|i18n( $help_ctx )}</h2>
        </div>
        <ul class="a4-help-links">
        {foreach $help_l.related as $help_rel}
            <li><a href={$help_rel.url|ezurl}><span class="a4-icon a4-bi-{$help_rel.icon|wash}" aria-hidden="true"></span>{$help_rel.name|wash}<span class="a4-help-links-count">{$help_rel.count|wash}</span></a></li>
        {/foreach}
        </ul>
    </section>
    {/if}
{/case}

{case match='commits'}
    {if $help_commits}
    {include uri='design:exphelp/parts/landing_commits.tpl' commits=$help_commits merges=$help_commit_merges commit_params=$help_commit_params audience=$help_l.id}
    {/if}
{/case}

{case match='support'}
    {if $help_support}
    {include uri='design:exphelp/parts/support.tpl' support=$help_support}
    {/if}
{/case}

{case}{/case}
{/switch}
{/foreach}

    {include uri='design:exphelp/parts/license_dashboard.tpl'}
</div>
{ezscript_require( 'exphelp.js' )}
{undef}
