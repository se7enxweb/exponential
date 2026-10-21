{* exphelp/dashboard: the help start page (doc/eng-us/features/6.0/help-system.md). From top to bottom: the search hero
   (instant search through exphelp.js, a plain GET form to exphelp/search without it), the audiences (forty-one in five groups, the first open and the others folded, each with its pages; the project-manager and sales-marketing cards link their start page), featured
   pages next to the recently changed pages, the sections as quick links, how to report an error, the license, the
   Support Exponential block (parts/support.tpl) and the keyboard shortcuts. Each audience card's title opens the
   audience's landing page (exphelp/audience/<id>) while [LandingSettings] Enabled=enabled, else its search. Styles: stylesheets/modules/help.css (.a4-help-*); no inline CSS or script.

   Variables (kernel/private/classes/views/exphelp/dashboard.php): help_audiences (id => id, name, count), help_audience_pages (id => up to 4 documents),
   help_categories (id => id, name, icon, count), help_featured (documents: id, path, title, summary, category,
   audience[]), help_recent (id, title, time, subject), help_trackers (key => key, label, description, url,
   new_issue_url), help_search_url, help_report_url; optional help_i18n_json (client strings), help_reading (the reader's language and its switch, parts/reading_language.tpl) and help_stats
   (pages, categories), help_support (the Support Exponential block or false). Every part is left out when its list is
   empty. *}
{def $help_ctx = 'kernel/exphelp'
     $help_audience_ids = array( 'editor', 'admin', 'developer', 'user', 'project-manager', 'sales-marketing', 'partner', 'advocate', 'designer', 'hosting', 'app-creator', 'website-creator', 'supporter', 'sponsor', 'trainer', 'press', 'businesses', 'government', 'customer', 'community', 'ecosystem', 'php-developer', 'theme-designer', 'reseller', 'bundler', 'ai-agents', 'software-engineer', 'adopter', 'mobile-app-developer', 'web-framework-developer', 'cms-developer', 'dxp-developer', 'ecommerce', 'integrator', 'dba', 'podcaster', 'non-profit', 'local-business', 'individual', 'non-technical', 'student' )
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
        'trainer', hash( 'icon', 'easel', 'name', 'Trainers'|i18n( 'kernel/exphelp' ), 'text', 'Teach Exponential: courses for editors, administrators, developers and designers, workshops, exercises and assessments.'|i18n( 'kernel/exphelp' )  ),
        'community', hash( 'icon', 'chat-left-dots', 'name', 'Community'|i18n( 'kernel/exphelp' ), 'text', 'Use, promote and build with Exponential worldwide: taking part, the channels, events, the code of conduct and contributing.'|i18n( 'kernel/exphelp' ) ),
        'ecosystem', hash( 'icon', 'globe2', 'name', 'Ecosystem'|i18n( 'kernel/exphelp' ), 'text', 'The wider Exponential world in the PHP stack: its products, extensions, distributions, partners, users and creators.'|i18n( 'kernel/exphelp' ) ),
        'php-developer', hash( 'icon', 'terminal', 'name', 'PHP Developers'|i18n( 'kernel/exphelp' ), 'text', 'Work on Exponential in PHP: the kernel and its classes, autoloads, the command line, Composer, tests and coding standards.'|i18n( 'kernel/exphelp' ) ),
        'theme-designer', hash( 'icon', 'brush', 'name', 'Theme designers'|i18n( 'kernel/exphelp' ), 'text', 'Design themes for Exponential: designs and overrides, templates, stylesheets and assets, packaging a theme and keeping it current.'|i18n( 'kernel/exphelp' ) ),
        'reseller', hash( 'icon', 'tag', 'name', 'Resellers'|i18n( 'kernel/exphelp' ), 'text', 'Resell Exponential and the services around it: hosting, support and implementation packages, pricing, licensing and partners.'|i18n( 'kernel/exphelp' ) ),
        'bundler', hash( 'icon', 'box-seam', 'name', 'Bundlers'|i18n( 'kernel/exphelp' ), 'text', 'Bundle Exponential: distributions, site packages, extensions and installers, with their versions, licenses and updates.'|i18n( 'kernel/exphelp' ) ),
        'ai-agents', hash( 'icon', 'cpu', 'name', 'AI, agents and agentic'|i18n( 'kernel/exphelp' ), 'text', 'Work with AI and agents in Exponential: the writing assistant expai, agent access through the APIs, agentic workflows and safety.'|i18n( 'kernel/exphelp' ) ),
        'software-engineer', hash( 'icon', 'tools', 'name', 'Software Engineers'|i18n( 'kernel/exphelp' ), 'text', 'Engineer software on Exponential: architecture, the request cycle, caching, testing, quality gates and maintainability.'|i18n( 'kernel/exphelp' ) ),
        'adopter', hash( 'icon', 'check2-square', 'name', 'Adopters'|i18n( 'kernel/exphelp' ), 'text', 'Adopt Exponential: evaluate it, plan the move, migrate content, train your team and run it with confidence.'|i18n( 'kernel/exphelp' ) ),
        'mobile-app-developer', hash( 'icon', 'phone-flip', 'name', 'Mobile App Developers'|i18n( 'kernel/exphelp' ), 'text', 'Build mobile apps on Exponential content: the REST APIs, authentication, content as JSON, images and caching.'|i18n( 'kernel/exphelp' ) ),
        'web-framework-developer', hash( 'icon', 'diagram-2', 'name', 'Web Framework Developers'|i18n( 'kernel/exphelp' ), 'text', 'Combine Exponential with web frameworks: Composer, the APIs, embedding content and running them side by side.'|i18n( 'kernel/exphelp' ) ),
        'cms-developer', hash( 'icon', 'file-earmark-code', 'name', 'CMS Developers'|i18n( 'kernel/exphelp' ), 'text', 'Develop on a content management system: content classes, templates, workflows, permissions, multisite and languages.'|i18n( 'kernel/exphelp' ) ),
        'dxp-developer', hash( 'icon', 'display', 'name', 'DXP Developers'|i18n( 'kernel/exphelp' ), 'text', 'Develop for Exponential Platform DXP: the Symfony distribution, its admin UI, REST and GraphQL, and how it differs from Exponential 6.'|i18n( 'kernel/exphelp' ) ),
        'ecommerce', hash( 'icon', 'tags', 'name', 'E-commerce'|i18n( 'kernel/exphelp' ), 'text', 'Sell online with Exponential: the shop, products and prices, VAT, orders, payments and running the store day to day.'|i18n( 'kernel/exphelp' ) ),
        'integrator', hash( 'icon', 'plug', 'name', 'Integrators'|i18n( 'kernel/exphelp' ), 'text', 'Connect Exponential to other systems: the REST APIs, RSS feeds, imports and exports, and search.'|i18n( 'kernel/exphelp' ) ),
        'dba', hash( 'icon', 'table', 'name', 'Database Administrators'|i18n( 'kernel/exphelp' ), 'text', 'Run the database behind Exponential: the supported databases, schema and upgrades, backups and restores, and performance.'|i18n( 'kernel/exphelp' ) ),
        'podcaster', hash( 'icon', 'collection-play', 'name', 'Podcasters, radio and streaming'|i18n( 'kernel/exphelp' ), 'text', 'Publish podcasts, radio and streams with Exponential: audio and video, feeds, embeds, show notes, scheduling and rights.'|i18n( 'kernel/exphelp' ) ),
        'non-profit', hash( 'icon', 'people', 'name', 'Non-profits'|i18n( 'kernel/exphelp' ), 'text', 'Run the website of a non-profit on Exponential: members and volunteers, fundraising pages, accessibility, transparency and small budgets.'|i18n( 'kernel/exphelp' ) ),
        'local-business', hash( 'icon', 'geo-alt', 'name', 'Local businesses'|i18n( 'kernel/exphelp' ), 'text', 'Put a local business online with Exponential: opening hours, location and contact pages, local search, a simple shop and a small budget.'|i18n( 'kernel/exphelp' ) ),
        'individual', hash( 'icon', 'person-circle', 'name', 'Individuals'|i18n( 'kernel/exphelp' ), 'text', 'Build a site of your own with Exponential: a personal site, portfolio, blog, family or club site, step by step.'|i18n( 'kernel/exphelp' ) ),
        'non-technical', hash( 'icon', 'chat-square-text', 'name', 'Non-technical people'|i18n( 'kernel/exphelp' ), 'text', 'Exponential in plain language: no command line, choosing hosting, the words you will meet and simple how-tos.'|i18n( 'kernel/exphelp' ) ),
        'student', hash( 'icon', 'book', 'name', 'Students and learners'|i18n( 'kernel/exphelp' ), 'text', 'Learn Exponential: learning paths, your first site, study projects, certifications and the Student partner level.'|i18n( 'kernel/exphelp' ) ) )
     $help_audience_groups = array(
        hash( 'id', 'use', 'open', true(), 'name', 'Use and run Exponential'|i18n( 'kernel/exphelp' ), 'text', 'Editors, administrators, developers and users, and everyone with a site of their own.'|i18n( 'kernel/exphelp' ),
              'ids', array( 'editor', 'admin', 'developer', 'user', 'customer', 'adopter', 'non-technical', 'individual' ) ),
        hash( 'id', 'organizations', 'open', false(), 'name', 'Organizations'|i18n( 'kernel/exphelp' ), 'text', 'Businesses, public bodies and non-profits, and the people who lead and sell their projects.'|i18n( 'kernel/exphelp' ),
              'ids', array( 'businesses', 'local-business', 'ecommerce', 'non-profit', 'government', 'project-manager', 'sales-marketing' ) ),
        hash( 'id', 'build', 'open', false(), 'name', 'Build with Exponential'|i18n( 'kernel/exphelp' ), 'text', 'Designers, developers and engineers who build sites, themes, applications and integrations.'|i18n( 'kernel/exphelp' ),
              'ids', array( 'designer', 'theme-designer', 'website-creator', 'app-creator', 'php-developer', 'software-engineer', 'cms-developer', 'dxp-developer', 'web-framework-developer', 'mobile-app-developer', 'ai-agents', 'integrator', 'dba' ) ),
        hash( 'id', 'partners', 'open', false(), 'name', 'Host, sell and partner'|i18n( 'kernel/exphelp' ), 'text', 'Partners, hosting companies, resellers and bundlers who bring Exponential to their customers.'|i18n( 'kernel/exphelp' ),
              'ids', array( 'partner', 'hosting', 'reseller', 'bundler' ) ),
        hash( 'id', 'community', 'open', false(), 'name', 'Community and learning'|i18n( 'kernel/exphelp' ), 'text', 'The community and the ecosystem, supporters and sponsors, the media, trainers and students.'|i18n( 'kernel/exphelp' ),
              'ids', array( 'community', 'ecosystem', 'advocate', 'supporter', 'sponsor', 'press', 'podcaster', 'trainer', 'student' ) ) )
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

        <form class="a4-help-search" action={$help_search_action|ezurl} method="post" role="search" data-exphelp-search data-exphelp-endpoint={$help_search_action|ezurl}>
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
                {* the audiences of the open group(s) as pills, every other audience behind "More audiences" *}
                {foreach $help_audience_groups as $help_group}{if $help_group.open}{foreach $help_group.ids as $aid}
                <label class="a4-help-filter" data-exphelp-facet="audience:{$aid}"><input type="radio" name="audience" value="{$aid}" data-exphelp-filter /><span><span class="a4-icon a4-bi-{$help_audience_meta[$aid].icon}" aria-hidden="true"></span>{$help_audience_meta[$aid].name|wash} <span data-exphelp-count></span></span></label>
                {/foreach}{/if}{/foreach}
                <details class="a4-help-filters-more">
                    <summary><span class="a4-icon a4-bi-chevron-down" aria-hidden="true"></span>{'More audiences'|i18n( $help_ctx )}</summary>
                    <div class="a4-help-filters-more-list">
                    {foreach $help_audience_groups as $help_group}{if $help_group.open|not}{foreach $help_group.ids as $aid}
                    <label class="a4-help-filter" data-exphelp-facet="audience:{$aid}"><input type="radio" name="audience" value="{$aid}" data-exphelp-filter /><span><span class="a4-icon a4-bi-{$help_audience_meta[$aid].icon}" aria-hidden="true"></span>{$help_audience_meta[$aid].name|wash} <span data-exphelp-count></span></span></label>
                    {/foreach}{/if}{/foreach}
                    </div>
                </details>
            </fieldset>
        </form>

        {* the reader's language and the switch to the help's other languages (parts/reading_language.tpl) *}
        {if and( is_set( $help_reading ), $help_reading )}{include uri='design:exphelp/parts/reading_language.tpl' reading=$help_reading prefix='help-dash-reading'}{/if}

        {if and( is_set( $help_stats ), is_set( $help_stats.pages ) )}
        <ul class="a4-help-stats">
            <li><strong>{$help_stats.pages|wash}</strong> {'pages'|i18n( $help_ctx )}</li>
            {if is_set( $help_stats.categories )}<li><strong>{$help_stats.categories|wash}</strong> {'sections'|i18n( $help_ctx )}</li>{/if}
            <li><strong>{$help_audience_ids|count}</strong> {'audiences'|i18n( $help_ctx )}</li>
        </ul>
        {/if}

        {* quick access to the master help index (exphelp/contents), the same button as the viewer sidebar *}
        <p class="a4-help-hero-actions"><a class="a4-btn a4-btn-small" href={'exphelp/contents'|ezurl}><span class="a4-icon a4-bi-list-ul" aria-hidden="true"></span> {'Browse all help'|i18n( 'kernel/exphelp' )}</a></p>
    </section>

    {* ---- Who makes Exponential (parts/dashboard_about.tpl) ---- *}
    {include uri='design:exphelp/parts/dashboard_about.tpl' landing_on=$help_landing_on}

    {* ---- Audiences ---- *}
    <section aria-labelledby="help-dash-audiences">
        <div class="a4-help-section-head">
            <h2 id="help-dash-audiences"><span class="a4-icon a4-bi-people" aria-hidden="true"></span>{'Start where you are'|i18n( $help_ctx )}</h2>
            <p>{'Pick your role to see the pages written for it.'|i18n( $help_ctx )}</p>
        </div>
        {* the audiences in groups (forty-one cards): the first group open, the others folded, each with its count *}
        <div class="a4-help-audience-groups">
        {foreach $help_audience_groups as $help_group}
        <details class="a4-help-audience-group" id="help-dash-group-{$help_group.id|wash}"{if $help_group.open} open{/if}>
            <summary class="a4-help-audience-group-head">
                <span class="a4-icon a4-bi-chevron-right a4-help-audience-group-chevron" aria-hidden="true"></span>
                <span class="a4-help-audience-group-title">{$help_group.name|wash}</span>
                <span class="a4-help-audience-group-count">{'%count audiences'|i18n( $help_ctx,, hash( '%count', $help_group.ids|count ) )|wash}</span>
                <span class="a4-help-audience-group-text">{$help_group.text|wash}</span>
            </summary>
        <ul class="a4-help-audiences">
        {foreach $help_group.ids as $aid}
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
        </details>
        {/foreach}
        </div>
    </section>

    {* ---- Help maintained as content: topics overdue for review, for who can act on them ---- *}
    {if and( is_set( $help_review ), $help_review )}
    {include uri='design:exphelp/parts/dashboard_review.tpl' review=$help_review}
    {/if}

    {* ---- Help maintained as content: manuals and topics (doc/eng-us/features/6.0/help-content.md) ---- *}
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
