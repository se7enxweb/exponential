{* exphelp/dashboard: the help start page (doc/en/us/features/6.0/help-system.md). From top to bottom: the search hero
   (instant search through exphelp.js, a plain GET form to exphelp/search without it), the audiences (fourteen, each with its pages; the project-manager and sales-marketing cards link their start page), featured
   pages next to the recently changed pages, the sections as quick links, how to report an error, the license, the
   Support Exponential block (parts/support.tpl) and the keyboard shortcuts. Each audience card's title opens the
   audience's landing page (exphelp/audience/<id>) while [LandingSettings] Enabled=enabled, else its search. Styles: stylesheets/modules/help.css (.a4-help-*); no inline CSS or script.

   Variables (kernel/private/classes/views/exphelp/dashboard.php): help_audiences (id => id, name, count), help_audience_pages (id => up to 4 documents),
   help_categories (id => id, name, icon, count), help_featured (documents: id, path, title, summary, category,
   audience[]), help_recent (id, title, time, subject), help_trackers (key => key, label, description, url,
   new_issue_url), help_search_url, help_report_url; optional help_i18n_json (client strings) and help_stats
   (pages, categories), help_support (the Support Exponential block or false). Every part is left out when its list is
   empty. *}
{def $help_ctx = 'kernel/exphelp'
     $help_audience_ids = array( 'editor', 'admin', 'developer', 'user', 'project-manager', 'sales-marketing', 'partner', 'advocate', 'designer', 'hosting', 'app-creator', 'website-creator', 'supporter', 'sponsor', 'trainer', 'press', 'businesses', 'government', 'customer' )
     $help_audience_meta = hash(
        'editor',    hash( 'icon', 'pencil-square', 'name', 'Editors'|i18n( 'kernel/exphelp' ),        'text', 'Create, edit, translate and publish content: media, layouts, search and the editor.'|i18n( 'kernel/exphelp' ) ),
        'admin',     hash( 'icon', 'gear',          'name', 'Administrators'|i18n( 'kernel/exphelp' ), 'text', 'Run the installation: sites, users and roles, caches, logs, security and the system\'s health.'|i18n( 'kernel/exphelp' ) ),
        'developer', hash( 'icon', 'code-square',   'name', 'Developers'|i18n( 'kernel/exphelp' ),     'text', 'Build on it: extensions, templates, settings, the APIs, the command line and upgrades.'|i18n( 'kernel/exphelp' ) ),
        'user',      hash( 'icon', 'person',        'name', 'End users'|i18n( 'kernel/exphelp' ),      'text', 'Use the site: your account, signing in, two-factor authentication and finding things.'|i18n( 'kernel/exphelp' ) ),
        'project-manager', hash( 'icon', 'diagram-3', 'name', 'Project managers'|i18n( 'kernel/exphelp' ), 'text', 'Plan and lead projects: phases and roles, hosting choices, scoping, checklists, quality gates, releases and risks.'|i18n( 'kernel/exphelp' ),
                           'start', 'help/leadership/project-managers-start-here.md' ),
        'sales-marketing', hash( 'icon', 'graph-up-arrow', 'name', 'Sales & marketing'|i18n( 'kernel/exphelp' ), 'text', 'Present and promote Exponential: positioning, feature highlights, demos, objections, licensing, brand and community.'|i18n( 'kernel/exphelp' ),
                           'start', 'help/business/sales-and-marketing-start-here.md' ),
        'partner', hash( 'icon', 'building', 'name', 'Partners'|i18n( 'kernel/exphelp' ), 'text', 'Build, host and sell with Exponential: the partner program, partner classes, benefits, requirements and how to apply.'|i18n( 'kernel/exphelp' ) ),
        'advocate', hash( 'icon', 'people', 'name', 'Advocates'|i18n( 'kernel/exphelp' ), 'text', 'Spread the word: talking points, events, community channels, demos and helping new users find their way.'|i18n( 'kernel/exphelp' ) ),
        'designer', hash( 'icon', 'palette', 'name', 'Designers'|i18n( 'kernel/exphelp' ), 'text', 'Design themes and templates, build extensions, then publish, sell and maintain them through their life cycle.'|i18n( 'kernel/exphelp' ) ),
        'hosting', hash( 'icon', 'database', 'name', 'Hosting companies'|i18n( 'kernel/exphelp' ), 'text', 'Host Exponential sites: server requirements, PHP and databases, Velocity, caching, security, backups and operations.'|i18n( 'kernel/exphelp' ) ),
        'app-creator', hash( 'icon', 'phone', 'name', 'Application creators'|i18n( 'kernel/exphelp' ), 'text', 'Build applications on Exponential: content models, modules and views, the APIs, extensions and testing.'|i18n( 'kernel/exphelp' ) ),
        'website-creator', hash( 'icon', 'globe', 'name', 'Website creators'|i18n( 'kernel/exphelp' ), 'text', 'Build websites with Exponential: plan the site, content classes, designs, layouts, the launch and its care.'|i18n( 'kernel/exphelp' ) ),
        'supporter', hash( 'icon', 'star', 'name', 'Supporters'|i18n( 'kernel/exphelp' ), 'text', 'Help the project grow: contribute code, documentation, translations and testing, and share what you build.'|i18n( 'kernel/exphelp' ) ),
        'sponsor', hash( 'icon', 'piggy-bank', 'name', 'Sponsors'|i18n( 'kernel/exphelp' ), 'text', 'Fund the work on Exponential: why sponsorship matters, what it pays for and how to sponsor 7x and the Foundation.'|i18n( 'kernel/exphelp' ) ),
        'customer', hash( 'icon', 'person-check', 'name', 'Customers'|i18n( 'kernel/exphelp' ), 'text', 'Run the site you have on Exponential: your team and daily work, commerce, upkeep with your provider, growth and getting value.'|i18n( 'kernel/exphelp' ) ),
        'government', hash( 'icon', 'bank', 'name', 'Governments'|i18n( 'kernel/exphelp' ), 'text', 'Exponential in the public sector: accessibility, privacy and security, open source and procurement, hosting and long-term care.'|i18n( 'kernel/exphelp' ) ),
        'businesses', hash( 'icon', 'briefcase', 'name', 'Businesses'|i18n( 'kernel/exphelp' ), 'text', 'Run your organization\'s website on Exponential: what it offers a business, costs and support, hosting, security and choosing partners.'|i18n( 'kernel/exphelp' ) ),
        'press', hash( 'icon', 'megaphone', 'name', 'Press, media and influencers'|i18n( 'kernel/exphelp' ), 'text', 'Write and talk about Exponential: the facts, the history, the people, quotes, images and who to contact.'|i18n( 'kernel/exphelp' ) ),
        'trainer', hash( 'icon', 'easel', 'name', 'Trainers'|i18n( 'kernel/exphelp' ), 'text', 'Teach Exponential: courses for editors, administrators, developers and designers, workshops, exercises and assessments.'|i18n( 'kernel/exphelp' ) ) )
     $help_search_action = cond( is_set( $help_search_url ), $help_search_url, '/exphelp/search' )
     $help_report = cond( is_set( $help_report_url ), $help_report_url, '/exphelp/report' )
     $help_i18n = cond( is_set( $help_i18n_json ), $help_i18n_json, '' )
     $help_audience = false()
     $help_pages = array()
     $help_category = false()
     $help_icon = ''
     $help_landing_on = ezini( 'LandingSettings', 'Enabled', 'exphelp.ini' )|eq( 'enabled' )}
<div class="a4-page a4-help-frame" data-exphelp{if $help_i18n|ne( '' )} data-exphelp-i18n="{$help_i18n|wash}"{/if}>

    {* ---- Search hero ---- *}
    <section class="a4-help-hero" aria-labelledby="help-dash-title">
        {include uri='design:exphelp/parts/dashboard_brand.tpl'}
        <p class="a4-help-hero-pre"><span class="a4-icon a4-bi-journal-text" aria-hidden="true"></span>{'Help center'|i18n( $help_ctx )}</p>
        <h1 id="help-dash-title">{'How can we help?'|i18n( $help_ctx )}</h1>
        <p class="a4-help-hero-sub">{'Guides and reference for everyone who works with this installation, written from the documentation of its own version.'|i18n( $help_ctx )}</p>

        <form class="a4-help-search" action={$help_search_action|ezurl} method="get" role="search" data-exphelp-search data-exphelp-endpoint={$help_search_action|ezurl}>
            <div class="a4-help-search-box">
                <div class="a4-help-search-field">
                    <span class="a4-icon a4-bi-search" aria-hidden="true"></span>
                    <label class="a4-sr-only" for="help-dash-q">{'Search the help'|i18n( $help_ctx )}</label>
                    <input type="search" id="help-dash-q" name="q" value="" placeholder="{'Search guides, settings, commands…'|i18n( $help_ctx )|wash}" autocomplete="off" spellcheck="false" aria-controls="help-dash-results" aria-describedby="help-dash-keys" data-exphelp-search-input />
                    <kbd class="a4-kbd" aria-hidden="true">/</kbd>
                    <button type="submit" class="a4-btn a4-btn-primary">{'Search'|i18n( $help_ctx )}</button>
                </div>
                <div id="help-dash-results" data-exphelp-results aria-live="polite" hidden></div>
            </div>
            <fieldset class="a4-help-filters">
                <legend>{'For'|i18n( $help_ctx )}</legend>
                <label class="a4-help-filter"><input type="radio" name="audience" value="" checked data-exphelp-filter /><span>{'Everyone'|i18n( $help_ctx )}</span></label>
                {foreach $help_audience_ids as $aid}
                <label class="a4-help-filter" data-exphelp-facet="audience:{$aid}"><input type="radio" name="audience" value="{$aid}" data-exphelp-filter /><span><span class="a4-icon a4-bi-{$help_audience_meta[$aid].icon}" aria-hidden="true"></span>{$help_audience_meta[$aid].name|wash} <span data-exphelp-count></span></span></label>
                {/foreach}
            </fieldset>
        </form>

        {if and( is_set( $help_stats ), is_set( $help_stats.pages ) )}
        <ul class="a4-help-stats">
            <li><strong>{$help_stats.pages|wash}</strong> {'pages'|i18n( $help_ctx )}</li>
            {if is_set( $help_stats.categories )}<li><strong>{$help_stats.categories|wash}</strong> {'sections'|i18n( $help_ctx )}</li>{/if}
            <li><strong>{$help_audience_ids|count}</strong> {'audiences'|i18n( $help_ctx )}</li>
        </ul>
        {/if}
    </section>

    {* ---- Who makes Exponential (parts/dashboard_about.tpl) ---- *}
    {include uri='design:exphelp/parts/dashboard_about.tpl'}

    {* ---- Audiences ---- *}
    <section aria-labelledby="help-dash-audiences">
        <div class="a4-help-section-head">
            <h2 id="help-dash-audiences"><span class="a4-icon a4-bi-people" aria-hidden="true"></span>{'Start where you are'|i18n( $help_ctx )}</h2>
            <p>{'Pick your role to see the pages written for it.'|i18n( $help_ctx )}</p>
        </div>
        <ul class="a4-help-audiences">
        {foreach $help_audience_ids as $aid}
            {set $help_audience = cond( and( is_set( $help_audiences ), is_set( $help_audiences[$aid] ) ), $help_audiences[$aid], false() )}
            <li class="a4-help-audience is-{$aid}">
                <div class="a4-help-audience-head">
                    <span class="a4-help-tile is-{$aid}" aria-hidden="true"><span class="a4-icon a4-bi-{$help_audience_meta[$aid].icon}"></span></span>
                    {if and( $help_audience, is_set( $help_audience.count ) )}<span class="a4-help-audience-count">{'%count pages'|i18n( $help_ctx,, hash( '%count', $help_audience.count ) )|wash}</span>{/if}
                </div>
                <h3><a href={cond( $help_landing_on, concat( '/exphelp/audience/', $aid ), concat( '/exphelp/search/(audience)/', $aid ) )|ezurl}>{cond( and( $help_audience, is_set( $help_audience.name ) ), $help_audience.name, $help_audience_meta[$aid].name )|wash}</a></h3>
                <p>{$help_audience_meta[$aid].text|wash}</p>
                {if is_set( $help_audience_meta[$aid].start )}
                <a class="a4-help-audience-start" href={concat( '/exphelp/viewer/', $help_audience_meta[$aid].start )|ezurl}><span class="a4-icon a4-bi-signpost-split" aria-hidden="true"></span>{'Start here'|i18n( $help_ctx )}<span class="a4-sr-only">: {$help_audience_meta[$aid].name|wash}</span></a>
                {/if}
                {set $help_pages = cond( and( is_set( $help_audience_pages ), is_set( $help_audience_pages[$aid] ) ), $help_audience_pages[$aid], array() )}
                {if $help_pages|count|gt( 0 )}
                <ul aria-label="{'Popular pages for %audience'|i18n( $help_ctx,, hash( '%audience', $help_audience_meta[$aid].name ) )|wash}">
                    {foreach $help_pages as $doc max 4}
                    <li><a href={concat( '/exphelp/viewer/', $doc.id )|ezurl}><span class="a4-icon a4-bi-chevron-right is-small" aria-hidden="true"></span>{$doc.title|wash}</a></li>
                    {/foreach}
                </ul>
                {/if}
            </li>
        {/foreach}
        </ul>
    </section>

    {* ---- Help maintained as content: manuals and topics (doc/en/us/features/6.0/help-content.md) ---- *}
    {if and( is_set( $help_content ), $help_content, or( $help_content.manuals|count|gt( 0 ), $help_content.topics|count|gt( 0 ) ) )}
    {include uri='design:exphelp/parts/dashboard_content.tpl' content=$help_content}
    {/if}

    {* ---- Featured and recently changed pages ---- *}
    {if or( and( is_set( $help_featured ), $help_featured|count|gt( 0 ) ), and( is_set( $help_recent ), $help_recent|count|gt( 0 ) ) )}
    <div class="a4-help-columns">
        {if and( is_set( $help_featured ), $help_featured|count|gt( 0 ) )}
        <section aria-labelledby="help-dash-featured">
            <div class="a4-help-section-head">
                <h2 id="help-dash-featured"><span class="a4-icon a4-bi-stars" aria-hidden="true"></span>{'Featured guides'|i18n( $help_ctx )}</h2>
            </div>
            <ul class="a4-help-featured">
            {foreach $help_featured as $doc}
                {set $help_icon = 'journal-text'}
                {if and( is_set( $doc.category ), is_set( $help_categories ), is_set( $help_categories[$doc.category] ), is_set( $help_categories[$doc.category].icon ), $help_categories[$doc.category].icon|ne( '' ) )}{set $help_icon = $help_categories[$doc.category].icon}{/if}
                <li class="a4-help-feature">
                    <a href={concat( '/exphelp/viewer/', $doc.id )|ezurl}>
                        <span class="a4-help-tile is-small" aria-hidden="true"><span class="a4-icon a4-bi-{$help_icon|wash}"></span></span>
                        <span class="a4-help-feature-text">
                            <span class="a4-help-feature-title">{$doc.title|wash}</span>
                            {if and( is_set( $doc.summary ), $doc.summary|ne( '' ) )}<span class="a4-help-feature-summary">{$doc.summary|shorten( 140 )|wash}</span>{/if}
                            {if and( is_set( $doc.audience ), $doc.audience|count|gt( 0 ) )}
                            <span class="a4-help-feature-meta">
                                {foreach $doc.audience as $aid}{if is_set( $help_audience_meta[$aid] )}<span class="a4-tag a4-help-pill is-{$aid}">{$help_audience_meta[$aid].name|wash}</span>{/if}{/foreach}
                            </span>
                            {/if}
                        </span>
                    </a>
                </li>
            {/foreach}
            </ul>
        </section>
        {/if}

        {if and( is_set( $help_recent ), $help_recent|count|gt( 0 ) )}
        <section class="a4-card" aria-labelledby="help-dash-recent">
            <div class="a4-help-section-head">
                <h2 id="help-dash-recent"><span class="a4-icon a4-bi-clock-history" aria-hidden="true"></span>{'Recently updated'|i18n( $help_ctx )}</h2>
            </div>
            <ul class="a4-help-recent">
            {foreach $help_recent as $change}
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
    </div>
    {/if}

    {* ---- Sections as quick links ---- *}
    {if and( is_set( $help_categories ), $help_categories|count|gt( 0 ) )}
    <section aria-labelledby="help-dash-sections">
        <div class="a4-help-section-head">
            <h2 id="help-dash-sections"><span class="a4-icon a4-bi-signpost-split" aria-hidden="true"></span>{'Browse by topic'|i18n( $help_ctx )}</h2>
            <a class="a4-help-section-more" href={'exphelp/contents'|ezurl}><span class="a4-icon a4-bi-list-ul" aria-hidden="true"></span> {'Browse all help'|i18n( 'kernel/exphelp' )}</a>
        </div>
        <ul class="a4-help-links">
        {foreach $help_categories as $help_category}
            <li><a href={concat( '/exphelp/search/(category)/', $help_category.id|urlencode )|ezurl}><span class="a4-icon a4-bi-{cond( and( is_set( $help_category.icon ), $help_category.icon|ne( '' ) ), $help_category.icon, 'journal-text' )|wash}" aria-hidden="true"></span>{$help_category.name|wash}{if is_set( $help_category.count )}<span class="a4-help-links-count">{$help_category.count|wash}</span>{/if}</a></li>
        {/foreach}
        </ul>
    </section>
    {/if}

    {* ---- Grow from user to developer (parts/dashboard_grow.tpl) ---- *}
    {include uri='design:exphelp/parts/dashboard_grow.tpl'}

    {* ---- Report an error ---- *}
    <section class="a4-help-report" aria-labelledby="help-dash-report">
        <span class="a4-help-tile" aria-hidden="true"><span class="a4-icon a4-bi-exclamation-diamond"></span></span>
        <div>
            <h2 id="help-dash-report">{'Found an error?'|i18n( $help_ctx )}</h2>
            <p>{'Tell us on GitHub: the guide shows which tracker fits, what to include and the system details to copy, without any secrets.'|i18n( $help_ctx )}</p>
            <a class="a4-help-report-security" href={concat( $help_report, '#security' )|ezurl}><span class="a4-icon a4-bi-shield-lock" aria-hidden="true"></span>{'A security issue? Report it privately, not in a public issue.'|i18n( $help_ctx )}</a>
        </div>
        <div class="a4-help-report-actions">
            <a class="a4-btn a4-btn-primary" href={$help_report|ezurl}><span class="a4-icon a4-bi-list-task" aria-hidden="true"></span>{'How to report an error'|i18n( $help_ctx )}</a>
            {if is_set( $help_trackers )}
            {foreach $help_trackers as $tracker}
                {if and( is_set( $tracker.new_issue_url ), $tracker.new_issue_url|begins_with( 'https://' ) )}
                <a class="a4-btn" href="{$tracker.new_issue_url|wash}" rel="noopener noreferrer" target="_blank"{if and( is_set( $tracker.description ), $tracker.description|ne( '' ) )} title="{$tracker.description|wash}"{/if}><span class="a4-icon a4-bi-git" aria-hidden="true"></span>{$tracker.label|wash}<span class="a4-sr-only"> {'(new issue on GitHub, opens in a new window)'|i18n( $help_ctx )}</span></a>
                {/if}
            {/foreach}
            {/if}
        </div>
    </section>

    {* ---- Free documentation: the license and what readers may do with it ---- *}
    {include uri='design:exphelp/parts/license_dashboard.tpl'}

    {* ---- Support Exponential: sponsor 7x and the Exponential Foundation (parts/support.tpl, [SupportSettings]) ---- *}
    {if and( is_set( $help_support ), $help_support )}
    {include uri='design:exphelp/parts/support.tpl' support=$help_support}
    {/if}

    {* ---- Keyboard ---- *}
    <ul class="a4-help-keys" id="help-dash-keys" aria-label="{'Keyboard shortcuts'|i18n( $help_ctx )|wash}">
        <li><kbd class="a4-kbd">/</kbd> {'search'|i18n( $help_ctx )}</li>
        <li><kbd class="a4-kbd">↑</kbd><kbd class="a4-kbd">↓</kbd> {'move through the results'|i18n( $help_ctx )}</li>
        <li><kbd class="a4-kbd">Enter</kbd> {'open'|i18n( $help_ctx )}</li>
        <li><kbd class="a4-kbd">Esc</kbd> {'clear'|i18n( $help_ctx )}</li>
    </ul>
</div>
{ezscript_require( 'exphelp.js' )}
{undef}
